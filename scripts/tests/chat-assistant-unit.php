<?php
/** Hermetic transport-spike tests. No WordPress boot and no network. */
define( 'ABSPATH', __DIR__ . '/' );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'WP_ENVIRONMENT_TYPE', 'production' );
$root = dirname( __DIR__, 2 );
$GLOBALS['chat_test'] = [ 'admin' => true, 'nonces' => true, 'options' => [], 'transients' => [], 'routes' => [], 'menus' => [] ];
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function current_user_can( $cap ) { return 'manage_options' === $cap && $GLOBALS['chat_test']['admin']; }
function wp_verify_nonce( $nonce, $action ) { return 'wp_rest' === $action && $GLOBALS['chat_test']['nonces'] && 'valid' === $nonce; }
function register_rest_route( $namespace, $route, $args ) { $GLOBALS['chat_test']['routes'][ $namespace . $route ] = $args; }
function add_management_page( ...$args ) { $GLOBALS['chat_test']['menus'][] = $args; }
function wp_salt( $scheme ) { return 'fixture-salt'; }
function get_transient( $key ) { return $GLOBALS['chat_test']['transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl ) { $GLOBALS['chat_test']['transients'][ $key ] = $value; }
function add_option( $key, $value, $deprecated = '', $autoload = false ) { $GLOBALS['chat_test']['options'][ $key ] ??= $value; }
function wp_cache_delete( ...$args ) {}
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function wp_json_encode( $data, $flags = 0 ) { return json_encode( $data, $flags | JSON_THROW_ON_ERROR ); }
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function rest_url( $path ) { return 'https://example.test/wp-json/' . $path; }
function wp_create_nonce( $action ) { return 'fixture-nonce'; }
function wp_upload_dir() { return [ 'basedir' => $GLOBALS['chat_test']['uploads'], 'error' => false ]; }
class WP_Error {
	public function __construct( public $code, public $message, public $data ) {}
}
class WP_REST_Response {
	public function __construct( public $data, public $status ) {}
}
class WP_REST_Server {
	public const CREATABLE = 'POST';
}
class WP_REST_Request {
	public function __construct( private string $body = '{}', private string $nonce = 'valid' ) {}
	public function get_header( $key ) { return $this->nonce; }
	public function get_body() { return $this->body; }
	public function get_route() { return '/nexus/v1/chat'; }
}
class Chat_Test_DB {
	public string $options = 'wp_options';
	public bool $fail = false;
	public array $args = [];
	public function prepare( $sql, ...$args ) { $this->args = $args; return $sql; }
	public function query( $sql ) {
		if ( $this->fail ) return false;
		[ $amount, $key ] = $this->args;
		$value = (int) ( $GLOBALS['chat_test']['options'][ $key ] ?? 0 );
		if ( isset( $this->args[2] ) && $value > $this->args[2] ) return 0;
		$GLOBALS['chat_test']['options'][ $key ] = str_contains( $sql, 'GREATEST' ) ? max( 0, $value - $amount ) : $value + $amount;
		return 1;
	}
}
$wpdb = new Chat_Test_DB();
foreach ( [ 'canon/pricing-canon', 'canon/messaging-canon', 'mail' ] as $file ) require $root . '/blocksy-child/inc/' . $file . '.php';
foreach ( [ 'config', 'sigv4', 'eventstream', 'bedrock-client', 'sse', 'budget', 'prompt', 'rest', 'spike' ] as $file ) require $root . '/blocksy-child/inc/chat-assistant/' . $file . '.php';
require $root . '/blocksy-child/inc/api-telemetry.php';
$checks = 0;
function check_chat( bool $ok, string $label ): void {
	global $checks;
	if ( ! $ok ) throw new RuntimeException( 'FAIL: ' . $label );
	$checks++;
}
function rejects_chat( callable $call, string $label ): void {
	$thrown = false;
	try { $call(); } catch ( Throwable $error ) { $thrown = true; }
	check_chat( $thrown, $label );
}

// Official AWS botocore aws4_testsuite/get-vanilla (develop, blobs pinned in docs).
$headers = HU_Chat_SigV4::sign( 'GET', 'https://example.amazonaws.com/', '', [], 'AKIDEXAMPLE', 'wJalrXUtnFEMI/K7MDENG+bPxRfiCYEXAMPLEKEY', 'us-east-1', 'service', '20150830T123600Z' );
check_chat( $headers['authorization'] === 'AWS4-HMAC-SHA256 Credential=AKIDEXAMPLE/20150830/us-east-1/service/aws4_request, SignedHeaders=host;x-amz-date, Signature=5fa00fa31553b73ebf1942676e86291e8372ff2a2260956d9b8aae1d763fbf31', 'official AWS SigV4 vector' );
$sign = static fn( $url ) => HU_Chat_SigV4::sign( 'POST', $url, '{}', [], 'AKIDEXAMPLE', 'EXAMPLEKEY', 'eu-central-1', 'bedrock', '20150830T123600Z' );
check_chat( $sign( 'https://example.amazonaws.com/?a0=x&a=z&a=b' ) === $sign( 'https://example.amazonaws.com/?a=b&a=z&a0=x' ), 'query keys and duplicate values sorted' );
check_chat( $sign( 'https://example.amazonaws.com/model/test%3A0/invoke' ) !== $sign( 'https://example.amazonaws.com/model/test:0/invoke' ), 'encoded model URI signs differently' );

$fixture = json_decode( file_get_contents( __DIR__ . '/fixtures/chat-eventstream.json' ), true, 32, JSON_THROW_ON_ERROR );
$binary = implode( '', array_map( static fn( $frame ) => base64_decode( $frame, true ), $fixture['frames_base64'] ) );
foreach ( [ 1, 2, 7, 11, 12, 13, 65, strlen( $binary ) ] as $size ) {
	$decoder = new HU_Chat_Eventstream(); $frames = [];
	foreach ( str_split( $binary, $size ) as $chunk ) $frames = array_merge( $frames, $decoder->push( $chunk ) );
	$decoder->finish();
	check_chat( count( $frames ) === 4, 'all frame boundaries, chunk size ' . $size );
}
for ( $cut = 1; $cut < strlen( base64_decode( $fixture['frames_base64'][0] ) ); $cut++ ) {
	$decoder = new HU_Chat_Eventstream();
	$frames = $decoder->push( substr( $binary, 0, $cut ) );
	$frames = array_merge( $frames, $decoder->push( substr( $binary, $cut ) ) );
	check_chat( count( $frames ) === 4, 'two-chunk split ' . $cut );
}
$corrupt = $binary; $corrupt[0] = chr( ord( $corrupt[0] ) ^ 1 );
rejects_chat( static fn() => ( new HU_Chat_Eventstream() )->push( $corrupt ), 'prelude CRC corruption' );
$corrupt = $binary; $corrupt[110] = chr( ord( $corrupt[110] ) ^ 1 );
rejects_chat( static fn() => ( new HU_Chat_Eventstream() )->push( $corrupt ), 'message CRC corruption' );
$decoder = new HU_Chat_Eventstream(); $decoder->push( substr( $binary, 0, -1 ) );
rejects_chat( static fn() => $decoder->finish(), 'truncated last frame' );

$emit_events = [];
$emit = static function ( $event, $data ) use ( &$emit_events ) { $emit_events[] = [ $event, $data ]; };
$client = new HU_Chat_Bedrock_Client( [], static function ( $body, $stream, $consume ) use ( $binary ) { $consume( $binary ); return ''; } );
check_chat( 'stream' === $client->invoke( '{}', $emit ), 'stream result' );
check_chat( $client->attempts[0]['input_tokens'] === 17 && $client->attempts[0]['output_tokens'] === 3, 'start/delta usage merged' );
check_chat( count( array_filter( $emit_events, static fn( $event ) => 'text' === $event[0] ) ) === 1, 'text delta forwarded exactly once' );
$calls = 0;
$client = new HU_Chat_Bedrock_Client( [], static function ( $body, $stream, $consume ) use ( &$calls ) {
	$calls++;
	if ( $stream ) throw new RuntimeException( 'fixture transport failure' );
	return '{"content":[{"type":"text","text":"Hallo"}],"usage":{"input_tokens":17,"output_tokens":3}}';
} );
check_chat( 'fallback' === $client->invoke( '{}', $emit ) && 2 === $calls && null === $client->attempts[0], 'single fallback and uncertain stream billed' );
$calls = 0;
$partial = base64_decode( $fixture['frames_base64'][0] ) . base64_decode( $fixture['frames_base64'][1] );
$client = new HU_Chat_Bedrock_Client( [], static function ( $body, $stream, $consume ) use ( &$calls, $partial ) { $calls++; $consume( $partial ); throw new RuntimeException( 'fixture partial failure' ); } );
rejects_chat( static fn() => $client->invoke( '{}', $emit ), 'partial response fails without duplicate text' );
check_chat( 1 === $calls && null === $client->attempts[0], 'partial output never retried, billing conservative' );

$config = [ 'mode' => 'preview', 'region' => 'eu-central-1', 'model' => 'eu.anthropic.claude-haiku-4-5-20251001-v1:0', 'access_key' => 'EXAMPLE', 'secret_key' => 'EXAMPLE', 'budget' => '20', 'prices' => [ 'input' => '1', 'output' => '5', 'cache_write' => '2', 'cache_read' => '0.1' ] ];
check_chat( hu_chat_config_valid( $config ), 'explicit EU Haiku config' );
foreach ( [ [ 'mode', 'off' ], [ 'mode', 'live' ], [ 'region', 'us-east-1' ], [ 'model', 'global.anthropic.claude' ], [ 'model', '' ], [ 'secret_key', '' ], [ 'budget', 'NaN' ] ] as [ $key, $value ] ) {
	$invalid = $config; $invalid[ $key ] = $value;
	check_chat( ! hu_chat_config_valid( $invalid ), 'reject config ' . $key . ' ' . $value );
}
$invalid = $config; $invalid['prices']['output'] = '';
check_chat( ! hu_chat_config_valid( $invalid ), 'prices required, never guessed' );
check_chat( '' === hu_chat_config()['model'] && 'off' === hu_chat_config()['mode'], 'no model literal default' );
check_chat( hu_chat_usage_cost( [ 'input_tokens' => 10, 'output_tokens' => 4, 'cache_creation_input_tokens' => 3, 'cache_read_input_tokens' => 2 ], $config['prices'] ) === 37, 'all four usage prices and upward rounding' );
$reservation = hu_chat_spike_reserve( $config );
check_chat( is_array( $reservation ), 'budget reserved before calls' );
check_chat( $reservation['one'] === (int) ceil( 1024 * 5 + HU_CHAT_SPIKE_MAX_TOKENS * 5 ), 'reservation covers the probe output cap' );
hu_chat_spike_settle( $reservation, [ [ 'input_tokens' => 10, 'output_tokens' => 4 ] ], $config['prices'] );
check_chat( $GLOBALS['chat_test']['options'][ $reservation['key'] ] === 30, 'verified usage refunds unused reservation' );
$reservation = hu_chat_spike_reserve( $config );
hu_chat_spike_settle( $reservation, [ null ], $config['prices'] );
check_chat( $GLOBALS['chat_test']['options'][ $reservation['key'] ] === 30 + $reservation['one'], 'unknown attempt retains worst-case cost' );
$low = $config; $low['budget'] = '0.001';
check_chat( false === hu_chat_spike_reserve( $low ), 'hard cap, insufficient room for fallback' );
$GLOBALS['chat_test']['options'][ 'hu_chat_cost_' . gmdate( 'Ym' ) ] = 20000000;
check_chat( false === hu_chat_spike_reserve( $config ), 'exhausted budget rejects invocation' );
$wpdb->fail = true;
check_chat( false === hu_chat_spike_reserve( $config ), 'database outage fails closed' );
$wpdb->fail = false;

// Exercise registration against mutable runtime configuration, without AWS calls.
$runtime = [ 'HU_CHAT_MODE' => 'preview', 'HU_BEDROCK_REGION' => $config['region'], 'HU_BEDROCK_MODEL_ID' => $config['model'], 'HU_BEDROCK_ACCESS_KEY_ID' => $config['access_key'], 'HU_BEDROCK_SECRET_ACCESS_KEY' => $config['secret_key'], 'HU_CHAT_MONTHLY_BUDGET_USD' => $config['budget'] ];
foreach ( $config['prices'] as $type => $price ) $runtime[ 'HU_CHAT_PRICE_' . strtoupper( $type ) ] = $price;
foreach ( $runtime as $key => $value ) putenv( $key . '=' . $value );
// Region and monthly budget keep their existing defaults; the rest is mandatory.
$mandatory = array_diff( array_keys( $runtime ), [ 'HU_BEDROCK_REGION', 'HU_CHAT_MONTHLY_BUDGET_USD' ] );
$disabled = array_merge( [ [ 'HU_CHAT_MODE', 'off' ], [ 'HU_CHAT_MODE', 'live' ], [ 'HU_BEDROCK_REGION', 'us-east-1' ], [ 'HU_BEDROCK_MODEL_ID', 'global.anthropic.claude' ], [ 'HU_CHAT_MONTHLY_BUDGET_USD', 'NaN' ], [ 'HU_CHAT_PRICE_INPUT', 'NaN' ] ], array_map( static fn( $key ) => [ $key, '' ], $mandatory ) );
foreach ( $disabled as [ $key, $value ] ) {
	putenv( $key . '=' . $value );
	$GLOBALS['chat_test']['routes'] = []; $GLOBALS['chat_test']['menus'] = [];
	hu_chat_register_spike_route(); hu_chat_spike_admin_menu();
	check_chat( [] === $GLOBALS['chat_test']['routes'], 'disabled configuration registers no route: ' . $key . ' ' . $value );
	check_chat( [] === $GLOBALS['chat_test']['menus'], 'disabled configuration adds no menu: ' . $key . ' ' . $value );
	ob_start(); hu_chat_spike_admin_page(); $page = ob_get_clean();
	check_chat( '' === $page, 'disabled configuration renders no admin markup or script: ' . $key . ' ' . $value );
	check_chat( hu_chat_spike_permission( new WP_REST_Request() )->data['status'] === 503, 'disabled configuration rejects direct handler: ' . $key . ' ' . $value );
	putenv( $key . '=' . $runtime[ $key ] );
}
hu_chat_register_spike_route(); hu_chat_spike_admin_menu();
$route = $GLOBALS['chat_test']['routes']['nexus/v1/chat'];
check_chat( 'POST' === $route['methods'] && 'hu_chat_spike_permission' === $route['permission_callback'], 'production preview registers only privileged POST route' );
check_chat( 1 === count( $GLOBALS['chat_test']['menus'] ) && 'manage_options' === $GLOBALS['chat_test']['menus'][0][2], 'production preview registers admin menu' );
ob_start(); hu_chat_spike_admin_page(); $page = ob_get_clean();
check_chat( str_contains( $page, 'id="hu-chat-padding" checked' ) && str_contains( $page, '{"padding":true}' ) && str_contains( $page, '4096-Byte-Blöcke' ), 'admin page offers padded (default) and plain sends' );
check_chat( str_contains( $page, 'content-encoding|content-length|transfer-encoding|server|via|vary|x-cache' ) && str_contains( $page, "name.startsWith('x-')" ), 'protocol records transport headers and all X-* headers' );
check_chat( str_contains( $page, 'nonce|token|auth|key|secret|session|cookie' ), 'protocol excludes credential-like headers' );
check_chat( ! str_contains( $page, 'fixture-salt' ) && ! str_contains( $page, 'EXAMPLE' ), 'admin page carries no secret' );
$GLOBALS['chat_test']['options'] = [];
check_chat( true === hu_chat_spike_permission( new WP_REST_Request() ), 'production preview admin and nonce accepted' );
$GLOBALS['chat_test']['admin'] = false;
check_chat( hu_chat_spike_permission( new WP_REST_Request() )->data['status'] === 403, 'anonymous access blocked' );
$GLOBALS['chat_test']['menus'] = []; hu_chat_spike_admin_menu();
check_chat( [] === $GLOBALS['chat_test']['menus'], 'non-admin gets no menu in preview' );
ob_start(); hu_chat_spike_admin_page(); $page = ob_get_clean();
check_chat( '' === $page, 'non-admin gets no direct page markup or script' );
$GLOBALS['chat_test']['admin'] = true;
check_chat( hu_chat_spike_permission( new WP_REST_Request( '{}', 'bad' ) )->data['status'] === 403, 'invalid nonce blocked' );
$GLOBALS['chat_test']['nonces'] = false;
check_chat( hu_chat_spike_permission( new WP_REST_Request() )->data['status'] === 403, 'expired nonce blocked on production' );
$GLOBALS['chat_test']['nonces'] = true;
foreach ( [ '[]', '', '{"messages":[]}', '{"recipient":"attacker@example.test"}', '{"email":"attacker@example.test"}' ] as $body ) check_chat( ! hu_chat_spike_payload_valid( $body ), 'no visitor fields or recipients: ' . $body );
check_chat( hu_chat_spike_payload_valid( ' {} ' ), 'only fixed probe accepted' );
check_chat( 'plain' === hu_chat_spike_variant( '{}' ) && 'plain' === hu_chat_spike_variant( '{"padding":false}' ), 'plain variant' );
check_chat( 'padded' === hu_chat_spike_variant( '{"padding":true}' ), 'padded variant' );
foreach ( [ '{"padding":"yes"}', '{"padding":1}', '{"padding":true,"messages":[]}', '{"padding":{"x":true}}', '[true]', 'true', '{"Padding":true}' ] as $body ) check_chat( null === hu_chat_spike_variant( $body ), 'variant switch accepts nothing else: ' . $body );
$probe = json_decode( hu_chat_spike_probe_body(), true, 8, JSON_THROW_ON_ERROR );
check_chat( HU_CHAT_SPIKE_MAX_TOKENS === 200 && 200 === $probe['max_tokens'], 'probe output capped at 200 tokens' );
check_chat( [ [ 'role' => 'user', 'content' => 'Zähle langsam von 1 bis 30, jede Zahl in eine eigene Zeile.' ] ] === $probe['messages'] && ! isset( $probe['system'] ), 'fixed counting prompt, no system prompt' );
$stream_headers = hu_chat_stream_headers();
foreach ( [ 'Content-Type: text/event-stream; charset=UTF-8', 'Cache-Control: no-store, no-transform', 'Content-Encoding: identity', 'X-Accel-Buffering: no' ] as $line ) check_chat( in_array( $line, $stream_headers, true ), 'stream header ' . $line );
check_chat( hu_chat_spike_prepare( new WP_REST_Request( '{"recipient":"attacker@example.test"}' ) )->data['status'] === 400, 'REST rejects arbitrary fields' );

// Size-buffering proxy: collect text, send at most every 120 ms, pad every send to whole blocks.
check_chat( 4096 === hu_chat_flush_pad(), 'flush pad defaults to 4096' );
foreach ( [ '0' => 0, '8192' => 8192, '999999' => 65536, '-1' => 4096, 'abc' => 4096, '4096.5' => 4096 ] as $value => $expected ) {
	putenv( 'HU_CHAT_FLUSH_PAD=' . $value );
	check_chat( $expected === hu_chat_flush_pad(), 'HU_CHAT_FLUSH_PAD ' . $value );
}
putenv( 'HU_CHAT_FLUSH_PAD' );
foreach ( [ [ 0, 4096, '' ], [ 4096, 4096, '' ], [ 4095, 4096, 'extra-block' ], [ 4094, 4096, 'extra-block' ], [ 4093, 4096, 3 ], [ 100, 4096, 3996 ], [ 5000, 4096, 3192 ], [ 100, 0, '' ] ] as [ $length, $block, $expected ] ) {
	$comment = HU_Chat_SSE_Writer::padding( $length, $block );
	if ( 'extra-block' === $expected ) $expected = 4096 - $length % 4096 + 4096;
	check_chat( ( '' === $expected ? '' === $comment : strlen( $comment ) === $expected && ':' === $comment[0] && "\n\n" === substr( $comment, -2 ) && '' === trim( substr( $comment, 1 ) ) ), 'padding comment for ' . $length . '/' . $block );
	if ( $block > 0 && '' !== $comment ) check_chat( 0 === ( $length + strlen( $comment ) ) % $block, 'padded send is whole blocks ' . $length );
}
$now = 0.0; $sent = [];
$writer = new HU_Chat_SSE_Writer( 4096, static function ( $bytes ) use ( &$sent ) { $sent[] = $bytes; }, static function () use ( &$now ) { return $now; } );
$writer->emit( 'probe', [ 'stage' => 'ready' ] );
check_chat( 1 === count( $sent ) && 4096 === strlen( $sent[0] ) && str_starts_with( $sent[0], "event: probe\n" ), 'ready leaves at once as one padded block' );
$now = 0.050; $writer->emit( 'text', [ 'text' => '1' ] );
check_chat( 1 === count( $sent ), 'text within 120 ms of the last send waits' );
$now = 0.130; $writer->emit( 'text', [ 'text' => "\n2" ] );
check_chat( 2 === count( $sent ) && str_contains( $sent[1], '"text":"1\n2"' ), 'collected text leaves with the first delta after 120 ms' );
foreach ( [ [ 0.170, "\n3" ], [ 0.210, "\n4" ], [ 0.249, "\n5" ] ] as [ $at, $delta ] ) { $now = $at; $writer->emit( 'text', [ 'text' => $delta ] ); }
check_chat( 2 === count( $sent ), 'deltas inside 120 ms are collected' );
$now = 0.251; $writer->tick();
check_chat( 3 === count( $sent ) && str_contains( $sent[2], '"text":"\n3\n4\n5"' ) && 1 === substr_count( $sent[2], 'event: text' ), 'progress tick sends the collected deltas as one event after 120 ms' );
check_chat( [] === array_filter( $sent, static fn( $bytes ) => 0 !== strlen( $bytes ) % 4096 ), 'every send is whole 4096-byte blocks' );
$now = 0.260; $writer->emit( 'text', [ 'text' => "\n6" ] );
$now = 0.270; $writer->emit( 'done', [ 'transport' => 'stream' ] );
check_chat( 4 === count( $sent ) && strpos( $sent[3], '"\n6"' ) < strpos( $sent[3], 'event: done' ), 'done first sends pending text, in order, in the same block' );
$writer->finish();
check_chat( 4 === count( $sent ), 'finish without pending text sends nothing' );
$sent = []; $now = 0.0;
$writer = new HU_Chat_SSE_Writer( 0, static function ( $bytes ) use ( &$sent ) { $sent[] = $bytes; }, static function () use ( &$now ) { return $now; } );
$writer->emit( 'text', [ 'text' => 'a' ] ); $now = 0.01; $writer->emit( 'text', [ 'text' => 'b' ] ); $writer->finish();
check_chat( 2 === count( $sent ) && ! str_contains( implode( '', $sent ), "\n:" ) && ':' !== $sent[0][0], 'pad 0 sends no comment but still batches' );
$ticks = 0;
$client = new HU_Chat_Bedrock_Client( [], static function ( $body, $stream, $consume, $tick ) use ( $binary, &$ticks ) { $tick(); $ticks++; $consume( $binary ); return ''; } );
check_chat( 'stream' === $client->invoke( '{}', static function () {}, static function () {} ) && 1 === $ticks, 'Bedrock client hands the progress tick to the transport' );

for ( $i = 1; $i < 30; $i++ ) check_chat( hu_chat_spike_prepare( new WP_REST_Request() ) instanceof WP_REST_Response, 'rate-limit permits request ' . $i );
check_chat( hu_chat_spike_prepare( new WP_REST_Request() )->data['status'] === 429, 'rate-limit stops next request' );
$failure = new WP_Error( 'chat_spike_payload', 'Fixed error', [ 'status' => 400 ] );
check_chat( $failure === nexus_api_telemetry_capture( $failure, null, new WP_REST_Request( '{"secret conversation in a key":"secret"}' ) ), 'generic telemetry never inspects a chat payload' );

$directory = sys_get_temp_dir() . '/hu-chat-unit-' . bin2hex( random_bytes( 6 ) );
mkdir( $directory . '/hu-chat', 0700, true );
$GLOBALS['chat_test']['uploads'] = $directory;
try {
	file_put_contents( $directory . '/hu-chat/knowledge.md', 'FIXTURE ONE {{KONTAKT_EMAIL}}' );
	$prompt = hu_chat_system_prompt();
	check_chat( str_contains( $prompt, 'Kontakt: ' . hu_get_contact_email() ) && str_contains( $prompt, hu_response_promise( 'value' ) ), 'fresh messaging canon' );
	check_chat( str_contains( $prompt, hu_freelancer_website_price( true ) ) && str_contains( $prompt, hu_landingpage_price( true ) ), 'website and landingpage prices from canon' );
	check_chat( str_contains( $prompt, hu_tracking_price( 'measurement', 'setup' ) ) && str_contains( $prompt, hu_whitelabel_price( 'retainer' ) ), 'tracking and White-Label canon' );
	check_chat( str_contains( $prompt, 'FIXTURE ONE {{KONTAKT_EMAIL}}' ), 'no recursive interpolation of website text' );
	file_put_contents( $directory . '/hu-chat/knowledge.md', 'FIXTURE TWO' );
	check_chat( str_contains( hu_chat_system_prompt(), 'FIXTURE TWO' ), 'knowledge read again each invocation' );
	unlink( $directory . '/hu-chat/knowledge.md' );
	rejects_chat( static fn() => hu_chat_system_prompt(), 'missing knowledge fails closed' );
} finally {
	if ( is_file( $directory . '/hu-chat/knowledge.md' ) ) unlink( $directory . '/hu-chat/knowledge.md' );
	rmdir( $directory . '/hu-chat' ); rmdir( $directory );
}
$template = file_get_contents( $root . '/blocksy-child/inc/chat-assistant/systemprompt.md' );
check_chat( ! str_contains( $template, '<!--' ), 'HTML comment removed' );
check_chat( ! preg_match( '/(?<![\pL\pN_])\d+(?:[.,]\d+)?(?![\pL\pN_])/u', $template ), 'no numeric literal in system prompt' );
check_chat( ! preg_match( '/[\w.%+-]+@[\w.-]+\.[a-z]{2,}/i', $template ), 'no literal email in system prompt' );
foreach ( file( $root . '/scripts/canon-forbidden-values.txt', FILE_IGNORE_NEW_LINES ) as $rule ) {
	$columns = explode( "\t", $rule );
	if ( 'rule' !== $columns[0] ) continue;
	$matched = preg_match( '~' . str_replace( '~', '\\~', $columns[2] ) . '~m', $template );
	check_chat( false !== $matched && 0 === $matched, 'system prompt excludes canon-forbidden rule ' . $columns[1] );
}
echo "Chat spike: $checks checks passed; no network used.\n";
