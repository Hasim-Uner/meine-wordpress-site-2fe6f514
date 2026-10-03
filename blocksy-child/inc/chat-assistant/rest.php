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

/** No user-supplied messages, keys or recipients enter the phase-one transport. */
function hu_chat_spike_payload_valid( string $body ): bool {
	return '{}' === trim( $body );
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
	if ( ! hu_chat_spike_payload_valid( $request->get_body() ) ) {
		hu_chat_spike_settle( $reservation, [], $config['prices'] );
		return new WP_Error( 'chat_spike_payload', 'Der Streaming-Test akzeptiert nur den festen Testaufruf.', [ 'status' => 400 ] );
	}
	// Do not expose config or a reservation in the REST response object.
	$GLOBALS['hu_chat_spike_reservation'] = $reservation;
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
	unset( $GLOBALS['hu_chat_spike_reservation'] );
	$config = hu_chat_config();
	while ( ob_get_level() > 0 ) {
		if ( ! @ob_end_clean() ) {
			break;
		}
	}
	@ini_set( 'zlib.output_compression', '0' );
	ignore_user_abort( true ); // Always settle the reservation after disconnect.
	header( 'Content-Type: text/event-stream; charset=UTF-8' );
	header( 'Cache-Control: no-store, no-transform' );
	header( 'X-Accel-Buffering: no' );
	$started = microtime( true );
	$emit = static function ( string $event, array $data ) use ( $started ): void {
		$data['elapsed_ms'] = (int) round( ( microtime( true ) - $started ) * 1000 );
		echo 'event: ' . $event . "\n" . 'data: ' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE ) . "\n\n";
		flush();
	};
	$client = new HU_Chat_Bedrock_Client( $config );
	try {
		$emit( 'probe', [ 'stage' => 'ready' ] );
		$transport = $client->invoke( wp_json_encode( [
			'anthropic_version' => 'bedrock-2023-05-31',
			'max_tokens' => 64,
			'messages' => [ [ 'role' => 'user', 'content' => 'Antworte ausschließlich mit dem Wort Hallo.' ] ],
		] ), $emit );
		$emit( 'done', [ 'transport' => $transport ] );
	} catch ( Throwable $error ) {
		hu_chat_spike_log_failure();
		$emit( 'error', [ 'text' => 'Der Chat ist gerade nicht erreichbar. Schreiben Sie bitte an ' . hu_get_contact_email() . '.' ] );
	} finally {
		hu_chat_spike_settle( $reservation, $client->attempts, $config['prices'] );
	}
	return true;
}
add_filter( 'rest_pre_serve_request', 'hu_chat_spike_serve', 10, 4 );
