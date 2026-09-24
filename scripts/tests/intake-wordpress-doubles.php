<?php
/** Isolated WP boundary doubles: no WP bootstrap, database, network or real mail. */
define( 'ABSPATH', __DIR__ . '/' );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'DAY_IN_SECONDS', 86400 );
error_reporting( E_ALL );
set_error_handler( static function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

class WP_Error {
	private $code;
	private $message;
	private $data;
	public function __construct( $code, $message, $data = null ) { $this->code = $code; $this->message = $message; $this->data = $data; }
	public function get_error_code() { return $this->code; }
	public function get_error_message() { return $this->message; }
	public function get_error_data() { return $this->data; }
}
class WP_REST_Request {
	private $payload;
	public function __construct( $payload ) { $this->payload = $payload; }
	public function get_json_params() { return $this->payload; }
	public function get_body_params() { return $this->payload; }
	public function get_header( $name ) { return ''; }
}
class WP_REST_Response {
	public $data;
	public $status;
	public function __construct( $data, $status ) { $this->data = $data; $this->status = $status; }
	public function header( $name, $value ) {}
}
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function wp_parse_args( $args, $defaults ) { return array_merge( $defaults, $args ); }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function sanitize_textarea_field( $value ) { return trim( strip_tags( $value ) ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) ); }
function sanitize_email( $value ) { return trim( $value ); }
function is_email( $value ) { return (bool) filter_var( $value, FILTER_VALIDATE_EMAIL ); }
function esc_url_raw( $value ) { return trim( $value ); }
function wp_http_validate_url( $value ) { return (bool) filter_var( $value, FILTER_VALIDATE_URL ); }
function wp_parse_url( $value, $component = -1 ) { return parse_url( $value, $component ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return esc_html( $value ); }
function esc_url( $value ) { return esc_html( $value ); }
function wp_kses( $value, $allowed ) { return $value; }
function wp_unslash( $value ) { return $value; }
function wp_json_encode( $value ) { return json_encode( $value ); }
function wp_specialchars_decode( $value, $flags ) { return htmlspecialchars_decode( $value, $flags ); }
function wp_trim_words( $value, $count, $suffix = '...' ) { return implode( ' ', array_slice( explode( ' ', $value ), 0, $count ) ); }
function get_bloginfo( $key ) { return 'Fixture site'; }
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function admin_url( $path ) { return home_url( '/wp-admin/' . $path ); }
function trailingslashit( $value ) { return rtrim( $value, '/' ) . '/'; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }
function absint( $value ) { return abs( (int) $value ); }
function current_time( $type, $gmt = false ) { return time(); }
function wp_generate_password( ...$args ) { return 'fixture-token-' . count( $GLOBALS['intake_test']['posts'] ); }
function wp_verify_nonce( $nonce, $action ) { return 'fixture-nonce' === $nonce; }
function number_format_i18n( $value, $decimals = 0 ) { return number_format( $value, $decimals, ',', '.' ); }
function apply_filters( $hook, $value ) {
	if ( in_array( $hook, [ 'nexus_contact_notification_email', 'hu_whitelabel_request_notification_email', 'nexus_audit_notification_email' ], true ) ) {
		return $GLOBALS['intake_test']['recipient'];
	}
	return $value;
}
function get_option( $key, $default = '' ) { return $GLOBALS['intake_test']['options'][ $key ] ?? ( 'admin_email' === $key ? 'internal@example.test' : $default ); }
function update_option( $key, $value, $autoload = null ) { $GLOBALS['intake_test']['options'][ $key ] = $value; }
function get_transient( $key ) { return $GLOBALS['intake_test']['transients'][ $key ] ?? 0; }
function set_transient( $key, $value, $ttl ) { $GLOBALS['intake_test']['transients'][ $key ] = $value; }
function get_post_meta( $id, $key, $single = true ) { return $GLOBALS['intake_test']['meta'][ $id ][ $key ] ?? ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['intake_test']['meta'][ $id ][ $key ] = $value; }
function delete_post_meta( $id, $key ) { unset( $GLOBALS['intake_test']['meta'][ $id ][ $key ] ); }
function wp_insert_post( $post, $error = false ) {
	$GLOBALS['intake_test']['events'][] = 'insert:' . $post['post_type'];
	if ( $GLOBALS['intake_test']['storage_fail'] ) return new WP_Error( 'db_error', 'Fixture storage failure' );
	$id = count( $GLOBALS['intake_test']['posts'] ) + 1;
	$GLOBALS['intake_test']['posts'][ $id ] = $post;
	return $id;
}
function wp_update_post( $post ) { return $post['ID']; }
function get_posts( $args ) {
	$ids = [];
	foreach ( $GLOBALS['intake_test']['posts'] as $id => $post ) {
		if ( $post['post_type'] !== $args['post_type'] ) continue;
		foreach ( $args['meta_query'] ?? [] as $query ) {
			if ( get_post_meta( $id, $query['key'] ) !== $query['value'] ) continue 2;
		}
		$ids[] = $id;
	}
	return $ids;
}
function nexus_record_crm_activity( $args ) { $GLOBALS['intake_test']['activities'][] = $args; return count( $GLOBALS['intake_test']['activities'] ); }
function wp_mail( $to, $subject, $body, $headers = [] ) {
	// Never call PHP mail(), SMTP or provider transport. Fail closed on non-fixture recipients.
	if ( ! preg_match( '/@example\.test$/', $to ) ) throw new RuntimeException( 'Non-fixture mail recipient' );
	$internal = 'internal@example.test' === $to;
	$GLOBALS['intake_test']['events'][] = $internal ? 'mail:internal' : 'mail:confirmation';
	$GLOBALS['intake_test']['mails'][] = compact( 'to', 'subject', 'body', 'headers' );
	return $GLOBALS['intake_test'][ $internal ? 'internal_mail' : 'confirmation_mail' ];
}
function intake_reset( $overrides = [] ) {
	$GLOBALS['intake_test'] = array_merge( [
		'posts' => [], 'meta' => [], 'options' => [], 'transients' => [], 'activities' => [], 'mails' => [], 'events' => [],
		'storage_fail' => false, 'internal_mail' => true, 'confirmation_mail' => true, 'recipient' => 'internal@example.test',
	], $overrides );
	$_SERVER['REMOTE_ADDR'] = '192.0.2.1';
}
