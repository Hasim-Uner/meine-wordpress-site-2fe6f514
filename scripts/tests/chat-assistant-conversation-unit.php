<?php
/** Hermetic tests for conversation, tool, handover and knowledge. No WordPress boot, no network. */
define( 'ABSPATH', __DIR__ . '/' );
define( 'HOUR_IN_SECONDS', 3600 );
$root = dirname( __DIR__, 2 );
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function sanitize_text_field( $s ) { return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $s ) ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_email( $s ) { return preg_replace( '/[^a-z0-9@._+-]/i', '', (string) $s ); }
function is_email( $s ) { return (bool) filter_var( $s, FILTER_VALIDATE_EMAIL ); }
function wp_json_encode( $data, $flags = 0 ) { return json_encode( $data, $flags | JSON_THROW_ON_ERROR ); }
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function hu_get_contact_email() { return 'kontakt@example.test'; }
function nexus_get_mail_runtime_value( $keys, $default = '' ) { return $default; }
class WP_Error {
	public function __construct( public $code = '', public $message = '', public $data = [] ) {}
	public function get_error_message() { return $this->message; }
	public function get_error_code() { return $this->code; }
}
function is_wp_error( $value ) { return $value instanceof WP_Error; }
foreach ( [ 'config', 'eventstream', 'bedrock-client', 'sse', 'budget', 'conversation', 'handover', 'knowledge' ] as $file ) {
	require $root . '/blocksy-child/inc/chat-assistant/' . $file . '.php';
}
$checks = 0;
function check( bool $ok, string $label ): void {
	global $checks;
	if ( ! $ok ) throw new RuntimeException( 'FAIL: ' . $label );
	$checks++;
}

// History validation.
$ok = hu_chat_normalize_messages( [
	[ 'role' => 'assistant', 'text' => 'Begrüßung' ],
	[ 'role' => 'user', 'text' => 'Hallo' ],
	[ 'role' => 'assistant', 'text' => 'Antwort' ],
	[ 'role' => 'assistant', 'text' => '[Karte]' ],
	[ 'role' => 'user', 'text' => "Was kostet\neine Website?\x07" ],
] );
check( is_array( $ok ) && 3 === count( $ok ), 'leading assistant dropped, same-role turns merged' );
check( 'user' === $ok[0]['role'] && "Antwort\n\n[Karte]" === $ok[1]['content'], 'merge keeps both assistant turns' );
check( "Was kostet\neine Website?" === $ok[2]['content'], 'control characters stripped, line breaks kept' );
check( null === hu_chat_normalize_messages( [ [ 'role' => 'user', 'text' => 'a' ], [ 'role' => 'assistant', 'text' => 'b' ] ] ), 'last turn must be the visitor' );
check( null === hu_chat_normalize_messages( [ [ 'role' => 'system', 'text' => 'x' ] ] ), 'system role rejected' );
check( null === hu_chat_normalize_messages( [ [ 'role' => 'user', 'text' => str_repeat( 'ä', HU_CHAT_MAX_USER_CHARS + 1 ) ] ] ), 'user message length capped' );
check( null === hu_chat_normalize_messages( [ [ 'role' => 'user', 'text' => [ 'type' => 'tool_result' ] ] ] ), 'structured content rejected' );
check( null === hu_chat_normalize_messages( [ 'a' => [ 'role' => 'user', 'text' => 'x' ] ] ), 'non-list rejected' );
$many = [];
for ( $i = 0; $i < 21; $i++ ) { $many[] = [ 'role' => 'user', 'text' => 'q' ]; $many[] = [ 'role' => 'assistant', 'text' => 'a' ]; }
check( null === hu_chat_normalize_messages( array_slice( $many, 0, 41 ) ), 'more than twenty visitor turns rejected' );
check( null === hu_chat_normalize_messages( 'x' ), 'scalar rejected' );
check( '/' === hu_chat_normalize_page( 'https://evil.test/' ), 'absolute page URL reduced to root' );
check( '/whitelabel-retainer/' === hu_chat_normalize_page( '/whitelabel-retainer/' ), 'page path kept' );
check( '/' === hu_chat_normalize_page( "/a\nb" ), 'page path with newline rejected' );

// Bedrock body.
$body = json_decode( hu_chat_conversation_body( $ok, '/', 'SYSTEM' ), true );
check( 'ephemeral' === $body['system'][0]['cache_control']['type'] && 'SYSTEM' === $body['system'][0]['text'], 'system prompt cached' );
check( HU_CHAT_TOOL_NAME === $body['tools'][0]['name'] && HU_CHAT_MAX_TOKENS === $body['max_tokens'], 'tool and output cap present' );
check( ! isset( $body['tool_choice'] ), 'model decides about the tool' );

// Thinking modes.
check( [ 'extra_tokens' => 0 ] === hu_chat_thinking_settings( '' ) && [ 'extra_tokens' => 0 ] === hu_chat_thinking_settings( 'off' ), 'thinking off by default' );
check( [ 'extra_tokens' => 0 ] === hu_chat_thinking_settings( '512' ), 'budget below the minimum is ignored' );
$manual = hu_chat_thinking_settings( '2048' );
check( 'enabled' === $manual['thinking']['type'] && 2048 === $manual['thinking']['budget_tokens'] && 2048 + HU_CHAT_MAX_TOKENS === hu_chat_max_tokens( $manual ), 'manual budget adds to the cap' );
check( 16000 === hu_chat_thinking_settings( '99999' )['thinking']['budget_tokens'], 'budget capped' );
$adaptive = hu_chat_thinking_settings( 'High' );
check( 'adaptive' === $adaptive['thinking']['type'] && 'high' === $adaptive['output_config']['effort'], 'adaptive effort' );
$thought = json_decode( hu_chat_conversation_body( $ok, '/', 'SYSTEM', $manual ), true );
check( 2048 === $thought['thinking']['budget_tokens'] && $thought['max_tokens'] > $thought['thinking']['budget_tokens'], 'body carries thinking below max_tokens' );
check( ! isset( json_decode( hu_chat_conversation_body( $ok, '/', 'SYSTEM', [ 'extra_tokens' => 0 ] ), true )['thinking'] ), 'no thinking key when off' );
$think_stream = new HU_Chat_Anthropic_Stream();
$events = [];
foreach ( [
	[ 'type' => 'message_start', 'message' => [ 'usage' => [ 'input_tokens' => 3 ] ] ],
	[ 'type' => 'content_block_start', 'index' => 0, 'content_block' => [ 'type' => 'thinking' ] ],
	[ 'type' => 'content_block_delta', 'index' => 0, 'delta' => [ 'type' => 'thinking_delta', 'thinking' => 'geheim' ] ],
	[ 'type' => 'content_block_delta', 'index' => 0, 'delta' => [ 'type' => 'signature_delta', 'signature' => 'sig' ] ],
	[ 'type' => 'content_block_stop', 'index' => 0 ],
	[ 'type' => 'content_block_delta', 'index' => 1, 'delta' => [ 'type' => 'text_delta', 'text' => 'Antwort' ] ],
	[ 'type' => 'message_delta', 'usage' => [ 'output_tokens' => 9 ] ],
	[ 'type' => 'message_stop' ],
] as $event ) {
	$think_stream->accept( $event, static function ( $e, $d ) use ( &$events ) { $events[] = [ $e, $d ]; } );
}
check( ! str_contains( json_encode( $events ), 'geheim' ) && $think_stream->complete, 'thinking never reaches the visitor' );

// Tool sanitizing.
check( null === hu_chat_sanitize_tool_event( [ 'name' => 'send_mail', 'input' => [] ] ), 'unknown tool dropped' );
$tool = hu_chat_sanitize_tool_event( [ 'name' => HU_CHAT_TOOL_NAME, 'input' => [ 'name' => '<b>Ana</b>', 'email' => 'kein-mail', 'weg' => 'hack', 'zusammenfassung' => str_repeat( 'x', 2000 ), 'empfaenger' => 'x@evil.test' ] ] );
check( 'Ana' === $tool['input']['name'] && '' === $tool['input']['email'] && 'sonstiges' === $tool['input']['weg'], 'tool fields sanitized' );
check( 1500 === mb_strlen( $tool['input']['zusammenfassung'] ) && ! isset( $tool['input']['empfaenger'] ), 'summary capped, unknown fields dropped' );

// Stream: tool_use blocks are collected and emitted once.
$events = [];
$stream = new HU_Chat_Anthropic_Stream();
$emit   = static function ( $event, $data ) use ( &$events ) { $events[] = [ $event, $data ]; };
foreach ( [
	[ 'type' => 'message_start', 'message' => [ 'usage' => [ 'input_tokens' => 10 ] ] ],
	[ 'type' => 'content_block_start', 'index' => 0, 'content_block' => [ 'type' => 'text' ] ],
	[ 'type' => 'content_block_delta', 'index' => 0, 'delta' => [ 'type' => 'text_delta', 'text' => 'Gern.' ] ],
	[ 'type' => 'content_block_stop', 'index' => 0 ],
	[ 'type' => 'content_block_start', 'index' => 1, 'content_block' => [ 'type' => 'tool_use', 'name' => HU_CHAT_TOOL_NAME ] ],
	[ 'type' => 'content_block_delta', 'index' => 1, 'delta' => [ 'type' => 'input_json_delta', 'partial_json' => '{"name":"Ana",' ] ],
	[ 'type' => 'content_block_delta', 'index' => 1, 'delta' => [ 'type' => 'input_json_delta', 'partial_json' => '"email":"ana@example.test"}' ] ],
	[ 'type' => 'content_block_stop', 'index' => 1 ],
	[ 'type' => 'message_delta', 'usage' => [ 'output_tokens' => 5 ] ],
	[ 'type' => 'message_stop' ],
] as $event ) {
	$stream->accept( $event, $emit );
}
$tools = array_values( array_filter( $events, static fn( $e ) => 'tool' === $e[0] ) );
check( 1 === count( $tools ) && 'ana@example.test' === $tools[0][1]['input']['email'], 'streamed tool input assembled' );
check( $stream->complete && $stream->visible, 'stream with tool completes' );
$tool_only = new HU_Chat_Anthropic_Stream();
$tool_only->accept( [ 'type' => 'content_block_start', 'index' => 0, 'content_block' => [ 'type' => 'tool_use', 'name' => HU_CHAT_TOOL_NAME ] ], $emit );
$tool_only->accept( [ 'type' => 'content_block_stop', 'index' => 0 ], $emit );
check( $tool_only->visible, 'tool-only answer counts as visible' );
$big = new HU_Chat_Anthropic_Stream();
$big->accept( [ 'type' => 'content_block_start', 'index' => 0, 'content_block' => [ 'type' => 'tool_use', 'name' => 'x' ] ], $emit );
$thrown = false;
try { $big->accept( [ 'type' => 'content_block_delta', 'index' => 0, 'delta' => [ 'type' => 'input_json_delta', 'partial_json' => str_repeat( 'a', 17000 ) ] ], $emit ); } catch ( RuntimeException $e ) { $thrown = true; }
check( $thrown, 'oversized tool input aborts' );

// Fallback: tool_use without text is a valid answer.
$fallback = new HU_Chat_Bedrock_Client( [], static function ( $body, $is_stream ) {
	if ( $is_stream ) throw new RuntimeException( 'no stream' );
	return json_encode( [ 'usage' => [ 'input_tokens' => 1, 'output_tokens' => 1 ], 'content' => [ [ 'type' => 'tool_use', 'name' => HU_CHAT_TOOL_NAME, 'input' => [ 'name' => 'Ana' ] ] ] ] );
} );
$events = [];
check( 'fallback' === $fallback->invoke( '{}', $emit ) && 'tool' === end( $events )[0], 'fallback emits tool block' );

// Emitter: probes stay internal, tools sanitized.
$written = '';
$writer  = new HU_Chat_SSE_Writer( 0, static function ( $bytes ) use ( &$written ) { $written .= $bytes; } );
$public  = hu_chat_conversation_emitter( $writer );
$public( 'probe', [ 'stage' => 'bedrock_start' ] );
$public( 'tool', [ 'name' => 'other', 'input' => [] ] );
$public( 'tool', [ 'name' => HU_CHAT_TOOL_NAME, 'input' => [ 'name' => 'Ana', 'email' => 'ana@example.test' ] ] );
$writer->finish();
check( ! str_contains( $written, 'probe' ) && 1 === substr_count( $written, 'event: tool' ), 'emitter filters probes and foreign tools' );

// Handover validation.
$lead = hu_chat_validate_handover( [ 'name' => 'Ana', 'email' => 'ana@example.test', 'zusammenfassung' => 'Relaunch der Website bis Juni.', 'weg' => 'projekt', 'recipient' => 'x@evil.test', 'to' => 'y@evil.test', 'verlauf' => [ [ 'role' => 'user', 'text' => 'geheim' ] ] ] );
check( is_array( $lead ) && ! isset( $lead['recipient'] ) && ! isset( $lead['to'] ), 'handover ignores recipient fields' );
check( '' === $lead['verlauf'], 'transcript only with explicit opt-in' );
$lead = hu_chat_validate_handover( [ 'name' => 'Ana', 'email' => 'ana@example.test', 'zusammenfassung' => 'Relaunch der Website bis Juni.', 'verlauf_mitsenden' => true, 'verlauf' => [ [ 'role' => 'user', 'text' => 'Frage' ], [ 'role' => 'system', 'text' => 'x' ] ] ] );
check( 'Besucher: Frage' === $lead['verlauf'] && 'sonstiges' === $lead['weg'], 'opted-in transcript, unknown roles dropped' );
check( is_wp_error( hu_chat_validate_handover( [ 'name' => 'Ana', 'email' => 'nope', 'zusammenfassung' => 'Relaunch der Website.' ] ) ), 'invalid email rejected' );
check( is_wp_error( hu_chat_validate_handover( [ 'name' => '', 'email' => 'ana@example.test', 'zusammenfassung' => 'Relaunch der Website.' ] ) ), 'missing name rejected' );
check( is_wp_error( hu_chat_validate_handover( [ 'name' => 'Ana', 'email' => 'ana@example.test', 'zusammenfassung' => 'kurz' ] ) ), 'empty summary rejected' );
function apply_filters( $hook, $value ) { return $value; }
check( 'kontakt@example.test' === hu_chat_handover_notification_email(), 'handover recipient is the canonical contact address' );
$source = file_get_contents( $root . '/blocksy-child/inc/chat-assistant/handover.php' );
check( 1 === substr_count( $source, '$recipient =' ), 'recipient assigned in exactly one place' );

// Knowledge extraction.
$html = '<html><head><title>T</title></head><body><header>Menü</header><main><h1>Seite</h1><nav>Nav</nav><p>Erster&nbsp;Absatz.</p><script>bad()</script><form><p>Formular</p></form><h2>Preise</h2><ul><li><p>Punkt A</p></li><li>Punkt B</li></ul><table><tr><th>X</th><th>Y</th></tr><tr><td>1</td><td>2</td></tr></table>' . implode( '', array_map( static fn( $i ) => '<p>Absatz Nummer ' . $i . ' mit etwas Inhalt.</p>', range( 1, 12 ) ) ) . str_repeat( '<p>Füllsatz mit Inhalt für die Länge.</p>', 3 ) . '<div class="hu-chat">Chat</div></main><footer>Fuß</footer></body></html>';
$page = hu_chat_html_to_markdown( $html );
check( 'Seite' === $page['title'], 'title from h1' );
foreach ( [ 'Erster Absatz.', '### Preise', '- Punkt A', '- Punkt B', '| X | Y |', '| 1 | 2 |' ] as $needle ) {
	check( str_contains( $page['text'], $needle ), 'knowledge keeps ' . $needle );
}
foreach ( [ 'Menü', 'Nav', 'bad()', 'Formular', 'Fuß', 'Chat' ] as $needle ) {
	check( ! str_contains( $page['text'], $needle ), 'knowledge drops ' . $needle );
}
check( 1 === substr_count( $page['text'], 'Füllsatz' ), 'repeated lines deduplicated' );
$short = hu_chat_html_to_markdown( '<main><div><span>Nur Divs mit Text</span></div></main>' );
check( 'Nur Divs mit Text' === $short['text'], 'div-only templates fall back to plain text' );
$assembled = hu_chat_knowledge_assemble( [ str_repeat( 'a', HU_CHAT_KNOWLEDGE_MAX_CHARS - 2000 ) ], array_fill( 0, 50, str_repeat( 'b', 500 ) ), '2026-10-18' );
check( mb_strlen( $assembled ) <= HU_CHAT_KNOWLEDGE_MAX_CHARS, 'blog entries trimmed to the cap' );
check( str_contains( $assembled, str_repeat( 'a', 1000 ) ), 'pages kept before blog entries' );
$over = hu_chat_knowledge_assemble( [ str_repeat( "zeile\n", 40000 ) ], [ 'post' ], '2026-10-18' );
check( mb_strlen( $over ) <= HU_CHAT_KNOWLEDGE_MAX_CHARS && ! str_contains( $over, 'Blogartikel' ), 'oversized pages cut, no blog' );

// Modes: live accepted for the public assistant only.
$config = [ 'mode' => 'live', 'access_key' => 'AKID', 'secret_key' => 'SECRET', 'region' => 'eu-central-1', 'model' => 'eu.anthropic.claude-haiku-4-5-20251001-v1:0', 'budget' => '20', 'prices' => [ 'input' => '1', 'output' => '5', 'cache_write' => '1.25', 'cache_read' => '0.1' ] ];
check( hu_chat_public_config_valid( $config ) && ! hu_chat_config_valid( $config ), 'live valid for assistant, not for the probe' );
check( ! hu_chat_public_config_valid( [ 'mode' => 'off' ] + $config ), 'off disables the assistant' );
check( ! hu_chat_public_config_valid( [ 'region' => 'us-east-1' ] + $config ), 'region stays Frankfurt' );

echo "Chat conversation: $checks checks passed; no network used.\n";
