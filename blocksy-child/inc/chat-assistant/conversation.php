<?php
/**
 * Conversation route: POST /nexus/v1/chat/messages.
 *
 * Stateless. The browser keeps the history and sends it with every message;
 * the server stores no conversation text, neither on success nor on error.
 * Only plain user/assistant text turns are accepted; tool calls are never
 * replayed from the client.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HU_CHAT_MAX_TOKENS          = 1024;
const HU_CHAT_MAX_USER_MESSAGES   = 20;
const HU_CHAT_MAX_USER_CHARS      = 2000;
const HU_CHAT_MAX_ASSISTANT_CHARS = 6000;
const HU_CHAT_HOURLY_LIMIT        = 30;
const HU_CHAT_TOOL_NAME           = 'anfrage_uebergeben';
const HU_CHAT_ROUTES              = [ 'projekt', 'whitelabel', 'energie', 'sonstiges' ];

/** Visitor-facing failure text; names only the canonical contact address. */
function hu_chat_unavailable_text(): string {
	return 'Der Assistent ist gerade nicht erreichbar. Schreiben Sie bitte an ' . hu_get_contact_email() . '.';
}

/** @return array<string, mixed> */
function hu_chat_tool_definition(): array {
	return [
		'name'         => HU_CHAT_TOOL_NAME,
		'description'  => 'Bereitet eine Anfrage an Haşim Üner vor. Nur aufrufen, wenn der Besucher ausdrücklich Kontakt, ein Angebot oder einen Termin möchte und Name sowie E-Mail genannt hat. Der Besucher prüft und sendet danach selbst.',
		'input_schema' => [
			'type'       => 'object',
			'properties' => [
				'name'              => [ 'type' => 'string' ],
				'email'             => [ 'type' => 'string' ],
				'firma'             => [ 'type' => 'string' ],
				'weg'               => [ 'type' => 'string', 'enum' => HU_CHAT_ROUTES ],
				'zusammenfassung'   => [ 'type' => 'string', 'description' => 'Lage, Ziel, Engpass, Budget- und Zeitrahmen in drei bis sechs Sätzen, nur was der Besucher gesagt hat.' ],
				'naechster_schritt' => [ 'type' => 'string' ],
			],
			'required'   => [ 'name', 'email', 'weg', 'zusammenfassung' ],
		],
	];
}

/** Bound a free-text field to plain text of a maximum length. */
function hu_chat_limit_text( $value, int $max, bool $multiline = false ): string {
	if ( ! is_string( $value ) ) {
		return '';
	}
	$value = $multiline ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	return mb_substr( trim( $value ), 0, $max, 'UTF-8' );
}

/**
 * Validate the browser history and turn it into Anthropic messages.
 * Leading assistant turns (the fixed greeting) are dropped, consecutive turns
 * of one role are merged, and the last turn must come from the visitor.
 *
 * @return array<int, array{role: string, content: string}>|null
 */
function hu_chat_normalize_messages( $raw ): ?array {
	if ( ! is_array( $raw ) || ! array_is_list( $raw ) || count( $raw ) > 2 * HU_CHAT_MAX_USER_MESSAGES + 2 ) {
		return null;
	}
	$messages = [];
	$users    = 0;
	foreach ( $raw as $item ) {
		if ( ! is_array( $item ) || ! is_string( $item['role'] ?? null ) || ! is_string( $item['text'] ?? null ) ) {
			return null;
		}
		$role = $item['role'];
		if ( ! in_array( $role, [ 'user', 'assistant' ], true ) ) {
			return null;
		}
		$limit = 'user' === $role ? HU_CHAT_MAX_USER_CHARS : HU_CHAT_MAX_ASSISTANT_CHARS;
		if ( mb_strlen( $item['text'], 'UTF-8' ) > $limit ) {
			return null;
		}
		// Control characters other than line breaks and tabs never reach the model.
		$text = trim( (string) preg_replace( '/[^\P{C}\n\t]/u', '', $item['text'] ) );
		if ( '' === $text ) {
			continue;
		}
		if ( 'user' === $role ) {
			$users++;
		}
		if ( ! $messages && 'assistant' === $role ) {
			continue;
		}
		$last = count( $messages ) - 1;
		if ( $last >= 0 && $messages[ $last ]['role'] === $role ) {
			$messages[ $last ]['content'] .= "\n\n" . $text;
			continue;
		}
		$messages[] = [ 'role' => $role, 'content' => $text ];
	}
	if ( ! $messages || $users > HU_CHAT_MAX_USER_MESSAGES || 'user' !== $messages[ count( $messages ) - 1 ]['role'] ) {
		return null;
	}
	return $messages;
}

/** Current page path as a hint for the model; anything odd becomes '/'. */
function hu_chat_normalize_page( $raw ): string {
	if ( ! is_string( $raw ) || strlen( $raw ) > 200 || ! preg_match( '#\A/[A-Za-z0-9/_\-.~%]*\z#', $raw ) ) {
		return '/';
	}
	return $raw;
}

/** Anthropic Messages body with the cached system prompt and the tool. */
function hu_chat_conversation_body( array $messages, string $page, string $system ): string {
	return wp_json_encode(
		[
			'anthropic_version' => 'bedrock-2023-05-31',
			'max_tokens'        => HU_CHAT_MAX_TOKENS,
			'system'            => [
				[ 'type' => 'text', 'text' => $system, 'cache_control' => [ 'type' => 'ephemeral' ] ],
				[ 'type' => 'text', 'text' => 'Aktuelle Seite des Besuchers: ' . $page ],
			],
			'tools'             => [ hu_chat_tool_definition() ],
			'messages'          => $messages,
		],
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
}

/**
 * Sanitize a tool call before it reaches the browser. Only the handover tool
 * exists; anything else is dropped.
 *
 * @return array<string, mixed>|null
 */
function hu_chat_sanitize_tool_event( array $data ): ?array {
	if ( HU_CHAT_TOOL_NAME !== ( $data['name'] ?? '' ) || ! is_array( $data['input'] ?? null ) ) {
		return null;
	}
	$input = $data['input'];
	$email = sanitize_email( is_string( $input['email'] ?? null ) ? $input['email'] : '' );
	$route = is_string( $input['weg'] ?? null ) && in_array( $input['weg'], HU_CHAT_ROUTES, true ) ? $input['weg'] : 'sonstiges';
	return [
		'name'  => HU_CHAT_TOOL_NAME,
		'input' => [
			'name'              => hu_chat_limit_text( $input['name'] ?? '', 120 ),
			'email'             => is_email( $email ) ? $email : '',
			'firma'             => hu_chat_limit_text( $input['firma'] ?? '', 160 ),
			'weg'               => $route,
			'zusammenfassung'   => hu_chat_limit_text( $input['zusammenfassung'] ?? '', 1500, true ),
			'naechster_schritt' => hu_chat_limit_text( $input['naechster_schritt'] ?? '', 300 ),
		],
	];
}

/** Browser requests must come from this site (Origin, else Referer). */
function hu_chat_same_origin( WP_REST_Request $request ): bool {
	$home = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );
	foreach ( [ 'origin', 'referer' ] as $header ) {
		$value = (string) $request->get_header( $header );
		if ( '' !== $value ) {
			return strtolower( (string) wp_parse_url( $value, PHP_URL_HOST ) ) === $home;
		}
	}
	return false;
}

/** Salted hourly IP counter; returns false once the limit is reached. */
function hu_chat_rate_allow( string $scope, int $limit ): bool {
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '';
	$key   = 'hu_chat_' . $scope . '_rl_' . substr( hash_hmac( 'sha256', $ip . gmdate( 'YmdH' ), wp_salt( 'nonce' ) ), 0, 32 );
	$count = (int) get_transient( $key );
	if ( $count >= $limit ) {
		return false;
	}
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );
	return true;
}

/** @return true|WP_Error */
function hu_chat_conversation_permission( WP_REST_Request $request ) {
	if ( ! hu_chat_visitor_allowed() ) {
		return new WP_Error( 'chat_unavailable', 'Der Assistent ist nicht verfügbar.', [ 'status' => 403 ] );
	}
	if ( ! hu_chat_same_origin( $request ) ) {
		return new WP_Error( 'chat_origin', 'Der Assistent ist nur auf dieser Website verfügbar.', [ 'status' => 403 ] );
	}
	return true;
}

/**
 * Checks in contract order: mode/config, budget, rate limit, payload. The
 * stream itself is written by hu_chat_conversation_serve().
 *
 * @return WP_REST_Response|WP_Error
 */
function hu_chat_conversation_prepare( WP_REST_Request $request ) {
	$permission = hu_chat_conversation_permission( $request );
	if ( is_wp_error( $permission ) ) {
		return $permission;
	}
	$data     = json_decode( $request->get_body(), true, 8 );
	$messages = is_array( $data ) ? hu_chat_normalize_messages( $data['messages'] ?? null ) : null;
	if ( null === $messages ) {
		return new WP_Error( 'chat_payload', 'Die Nachricht ist zu lang oder ungültig. Bitte kürzer formulieren oder ein neues Gespräch beginnen.', [ 'status' => 400 ] );
	}
	try {
		$system = hu_chat_system_prompt();
	} catch ( Throwable $error ) {
		error_log( '[hu-chat] prompt_unavailable' );
		return new WP_Error( 'chat_unavailable', hu_chat_unavailable_text(), [ 'status' => 503 ] );
	}
	$body        = hu_chat_conversation_body( $messages, hu_chat_normalize_page( $data['page'] ?? '/' ), $system );
	$config      = hu_chat_config();
	$reservation = hu_chat_budget_reserve( $config, strlen( $body ), HU_CHAT_MAX_TOKENS );
	if ( false === $reservation ) {
		return new WP_Error( 'chat_budget', 'Der Assistent macht für diesen Monat Pause. Schreiben Sie bitte direkt an ' . hu_get_contact_email() . '.', [ 'status' => 429 ] );
	}
	if ( ! hu_chat_rate_allow( 'msg', HU_CHAT_HOURLY_LIMIT ) ) {
		hu_chat_spike_settle( $reservation, [], $config['prices'] );
		return new WP_Error( 'chat_rate_limited', 'Das waren viele Nachrichten in kurzer Zeit. Bitte in einer Stunde weiterschreiben oder direkt an ' . hu_get_contact_email() . '.', [ 'status' => 429 ] );
	}
	// Body and reservation stay server-side; the REST object carries a marker only.
	$GLOBALS['hu_chat_conversation'] = [ 'body' => $body, 'reservation' => $reservation ];
	return new WP_REST_Response( [ 'hu_chat_conversation' => true ], 200 );
}

/** Constant error category only; never the exception text or any message. */
function hu_chat_conversation_log_failure(): void {
	error_log( '[hu-chat] conversation_transport_failed' );
	if ( function_exists( 'nexus_api_telemetry_append_log' ) ) {
		nexus_api_telemetry_append_log( [
			'timestamp'    => current_time( 'timestamp' ),
			'route'        => '/nexus/v1/chat/messages',
			'error_code'   => 'bedrock_transport_failed',
			'http_status'  => 502,
			'payload_keys' => [],
		] );
	}
}

/**
 * Event filter between model and browser: probes stay internal, tool calls
 * are sanitized, text passes through.
 */
function hu_chat_conversation_emitter( HU_Chat_SSE_Writer $writer ): callable {
	return static function ( string $event, array $data ) use ( $writer ): void {
		if ( 'probe' === $event ) {
			return;
		}
		if ( 'tool' === $event ) {
			$data = hu_chat_sanitize_tool_event( $data );
			if ( null === $data ) {
				return;
			}
		}
		$writer->emit( $event, $data );
	};
}

/** @return bool */
function hu_chat_conversation_serve( $served, $response, $request, $server ) {
	if ( '/nexus/v1/chat/messages' !== rtrim( $request->get_route(), '/' ) || ! isset( $GLOBALS['hu_chat_conversation'] ) || 200 !== $response->get_status() ) {
		return $served;
	}
	$job = $GLOBALS['hu_chat_conversation'];
	unset( $GLOBALS['hu_chat_conversation'] );
	$config = hu_chat_config();
	hu_chat_stream_begin();
	ignore_user_abort( true ); // Always settle the reservation after disconnect.
	$writer = new HU_Chat_SSE_Writer( hu_chat_flush_pad() );
	$emit   = hu_chat_conversation_emitter( $writer );
	$client = new HU_Chat_Bedrock_Client( $config );
	try {
		$client->invoke( $job['body'], $emit, [ $writer, 'tick' ] );
		$writer->emit( 'done', [] );
	} catch ( Throwable $error ) {
		hu_chat_conversation_log_failure();
		$writer->emit( 'error', [ 'text' => hu_chat_unavailable_text() ] );
	} finally {
		$writer->finish();
		hu_chat_spike_settle( $job['reservation'], $client->attempts, $config['prices'] );
	}
	return true;
}
add_filter( 'rest_pre_serve_request', 'hu_chat_conversation_serve', 10, 4 );

function hu_chat_register_conversation_route(): void {
	if ( ! hu_chat_assistant_enabled() ) {
		return;
	}
	register_rest_route( 'nexus/v1', '/chat/messages', [
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'hu_chat_conversation_prepare',
		'permission_callback' => 'hu_chat_conversation_permission',
	] );
}
add_action( 'rest_api_init', 'hu_chat_register_conversation_route' );
