<?php
/** Phase-one REST contract: authenticated preview probe with a fixed payload. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @return true|WP_Error */
function hu_chat_spike_permission( WP_REST_Request $request ) {
	if ( ! hu_chat_spike_enabled() ) {
		return new WP_Error( 'chat_unavailable', 'Der Streaming-Test ist nicht eingerichtet.', [ 'status' => 503 ] );
	}
	if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
		return new WP_Error( 'chat_forbidden', 'Der Streaming-Test ist nur für Administratoren verfügbar.', [ 'status' => 403 ] );
	}
	return true;
}

/**
 * The only accepted variant switch: `{}` or `{"padding":true|false}`. `padded` pads
 * every send to HU_CHAT_FLUSH_PAD blocks, `plain` sends without padding for comparison.
 * No user-supplied messages, keys or recipients enter the phase-one transport.
 *
 * @return 'plain'|'padded'|null
 */
function hu_chat_spike_variant( string $body ): ?string {
	if ( '{}' === trim( $body ) ) {
		return 'plain';
	}
	$data = json_decode( $body, true, 2 );
	if ( ! is_array( $data ) || [ 'padding' ] !== array_keys( $data ) || ! is_bool( $data['padding'] ) ) {
		return null;
	}
	return $data['padding'] ? 'padded' : 'plain';
}

function hu_chat_spike_payload_valid( string $body ): bool {
	return null !== hu_chat_spike_variant( $body );
}

function hu_chat_spike_probe_body(): string {
	return wp_json_encode( [
		'anthropic_version' => 'bedrock-2023-05-31',
		'max_tokens' => HU_CHAT_SPIKE_MAX_TOKENS,
		'messages' => [ [ 'role' => 'user', 'content' => HU_CHAT_SPIKE_PROMPT ] ],
	], JSON_UNESCAPED_UNICODE );
}

/** @return WP_REST_Response|WP_Error */
function hu_chat_spike_prepare( WP_REST_Request $request ) {
	$permission = hu_chat_spike_permission( $request );
	if ( is_wp_error( $permission ) ) {
		return $permission;
	}
	$config      = hu_chat_config();
	$reservation = hu_chat_spike_reserve( $config );
	if ( false === $reservation ) {
		return new WP_Error( 'chat_budget', 'Das Chat-Budget ist ausgeschöpft. Kontakt: ' . hu_get_contact_email(), [ 'status' => 429 ] );
	}
	// Same hourly, salted IP boundary as public intake; spike is admin-only.
	$ip  = $_SERVER['REMOTE_ADDR'] ?? '';
	$key = 'hu_chat_spike_rl_' . hash_hmac( 'sha256', $ip . gmdate( 'YmdH' ), wp_salt( 'nonce' ) );
	$count = (int) get_transient( $key );
	if ( $count >= 30 ) {
		hu_chat_spike_settle( $reservation, [], $config['prices'] );
		return new WP_Error( 'chat_rate_limited', 'Bitte später erneut testen.', [ 'status' => 429 ] );
	}
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );
	$variant = hu_chat_spike_variant( $request->get_body() );
	if ( null === $variant ) {
		hu_chat_spike_settle( $reservation, [], $config['prices'] );
		return new WP_Error( 'chat_spike_payload', 'Der Streaming-Test akzeptiert nur den festen Testaufruf.', [ 'status' => 400 ] );
	}
	// Do not expose config or a reservation in the REST response object.
	$GLOBALS['hu_chat_spike_reservation'] = $reservation;
	$GLOBALS['hu_chat_spike_variant']     = $variant;
	return new WP_REST_Response( [ 'hu_chat_spike' => true ], 200 );
}

function hu_chat_register_spike_route(): void {
	if ( ! hu_chat_spike_enabled() ) {
		return;
	}
	register_rest_route( 'nexus/v1', '/chat', [
		'methods' => WP_REST_Server::CREATABLE,
		'callback' => 'hu_chat_spike_prepare',
		'permission_callback' => 'hu_chat_spike_permission',
	] );
}
add_action( 'rest_api_init', 'hu_chat_register_spike_route' );

/** Only constant error categories enter logs/telemetry, never exception text. */
function hu_chat_spike_log_failure(): void {
	error_log( '[hu-chat] bedrock_transport_failed' );
	if ( function_exists( 'nexus_api_telemetry_append_log' ) ) {
		nexus_api_telemetry_append_log( [
			'timestamp' => current_time( 'timestamp' ),
			'route' => '/nexus/v1/chat',
			'error_code' => 'bedrock_transport_failed',
			'http_status' => 502,
			'payload_keys' => [],
		] );
	}
}

/** @return bool */
function hu_chat_spike_serve( $served, $response, $request, $server ) {
	if ( '/nexus/v1/chat' !== rtrim( $request->get_route(), '/' ) || ! isset( $GLOBALS['hu_chat_spike_reservation'] ) || 200 !== $response->get_status() ) {
		return $served;
	}
	$reservation = $GLOBALS['hu_chat_spike_reservation'];
	$variant     = $GLOBALS['hu_chat_spike_variant'] ?? 'plain';
	unset( $GLOBALS['hu_chat_spike_reservation'], $GLOBALS['hu_chat_spike_variant'] );
	$config = hu_chat_config();
	hu_chat_stream_begin();
	ignore_user_abort( true ); // Always settle the reservation after disconnect.
	$pad    = 'padded' === $variant ? hu_chat_flush_pad() : 0;
	$writer = new HU_Chat_SSE_Writer( $pad );
	$emit   = [ $writer, 'emit' ];
	$client = new HU_Chat_Bedrock_Client( $config );
	try {
		$emit( 'probe', [ 'stage' => 'ready', 'variant' => $variant, 'flush_pad' => $pad, 'flush_interval_ms' => HU_CHAT_FLUSH_INTERVAL_MS ] );
		$transport = $client->invoke( hu_chat_spike_probe_body(), $emit, [ $writer, 'tick' ] );
		$emit( 'done', [ 'transport' => $transport ] );
	} catch ( Throwable $error ) {
		hu_chat_spike_log_failure();
		$emit( 'error', [ 'text' => 'Der Chat ist gerade nicht erreichbar. Schreiben Sie bitte an ' . hu_get_contact_email() . '.' ] );
	} finally {
		$writer->finish();
		hu_chat_spike_settle( $reservation, $client->attempts, $config['prices'] );
	}
	return true;
}
add_filter( 'rest_pre_serve_request', 'hu_chat_spike_serve', 10, 4 );
