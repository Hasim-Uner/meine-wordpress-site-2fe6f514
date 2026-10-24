<?php
/**
 * Anfrage-Monitor: Marker im Kontakt-Handler, Ergebnis-Annahme und Status.
 * Laeuft die echten Handler mit isolierten WP-Grenzen (keine Datenbank, keine
 * Mail, kein Netz).
 */
require __DIR__ . '/intake-wordpress-doubles.php';
define( 'ARRAY_A', 'ARRAY_A' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HU_MONITOR_TOKEN', 'fixture-monitor-token-0123456789abcdef' );
if ( ! function_exists( 'wp_salt' ) ) { function wp_salt( $scheme = 'auth' ) { return 'fixture-salt'; } }
if ( ! function_exists( 'wp_date' ) ) { function wp_date( $format, $timestamp = null ) { return gmdate( $format, $timestamp ?? time() ); } }
function get_site_transient( $key ) { return $GLOBALS['intake_test']['site_transients'][ $key ] ?? false; }
function delete_option( $key ) { unset( $GLOBALS['intake_test']['options'][ $key ] ); }
function delete_transient( $key ) { unset( $GLOBALS['intake_test']['transients'][ $key ] ); }
foreach ( [ 'canon/messaging-canon', 'canon/pricing-canon', 'canon/e3-proof-canon', 'crm', 'crm-sales/sync', 'contact-page', 'review-crm', 'inquiry-events', 'anfrage-monitor' ] as $module ) {
	require __DIR__ . '/../../blocksy-child/inc/' . $module . '.php';
}

class MonitorDatabase {
	public $prefix = 'wp_';
	public $rows = [];
	public $col = [];
	public function insert( $table, $data, $format ) { $this->rows[] = $data; return 1; }
	public function prepare( $sql, ...$values ) { return [ $sql, $values ]; }
	public function get_results( $query, $format ) { return []; }
	public function get_col( $query ) { return $this->col; }
}
class MonitorRequest extends WP_REST_Request {
	private $auth;
	public function __construct( $payload, $auth = '' ) { parent::__construct( $payload ); $this->auth = $auth; }
	public function get_header( $name ) { return 'authorization' === $name ? $this->auth : ''; }
}
function check( $condition, $message ) { if ( ! $condition ) throw new RuntimeException( $message ); }
function run_case( $name, $callback ) {
	intake_reset( [ 'site_transients' => [] ] );
	update_option( 'hu_inquiry_events_schema', '1' );
	$GLOBALS['wpdb'] = new MonitorDatabase();
	hu_monitor_current_run( '' );
	$callback();
	echo "PASS $name\n";
}
function contact( $extra = [] ) {
	$payload = [ 'name' => 'Monitor', 'email' => 'monitor@example.test', 'request_type' => 'project', 'focus' => 'tracking', 'message' => 'Automatische Testanfrage des Anfrage-Monitors.', 'consent' => '1' ] + $extra;
	return nexus_handle_contact_request_submission( new WP_REST_Request( $payload ) );
}
function marker( $run = 'run-20261025' ) { return [ 'monitor_token' => HU_MONITOR_TOKEN, 'monitor_run_id' => $run ]; }

run_case( 'valid marker runs the real path and is marked everywhere', static function () {
	$r = contact( marker() );
	check( 201 === $r->status && true === $r->data['ok'], 'Accepted' );
	check( 'run-20261025' === $r->data['monitor']['runId'], 'Monitor block in response' );
	check( $r->data['monitor']['contactStored'] && $r->data['monitor']['notified'] && $r->data['monitor']['eventRecorded'], 'Real path reported' );
	$internal = array_values( array_filter( $GLOBALS['intake_test']['mails'], static fn( $m ) => 'internal@example.test' === $m['to'] ) );
	check( 0 === strpos( $internal[0]['subject'], '[MONITOR run-20261025] ' ), 'Internal mail subject carries the run' );
	$confirmation = array_values( array_filter( $GLOBALS['intake_test']['mails'], static fn( $m ) => 'monitor@example.test' === $m['to'] ) );
	check( 1 === count( $confirmation ), 'Confirmation mail went to the sender' );
	check( 0 === strpos( $confirmation[0]['subject'], '[MONITOR run-20261025 · Bestätigung] ' ), 'Confirmation mail subject carries the run' );
	check( 'monitor_test' === $GLOBALS['wpdb']->rows[0]['event'], 'Event is monitor_test, not anfrage_gesendet' );
	$contact_id = $r->data['contactId'];
	check( 'archived' === get_post_meta( $contact_id, '_nexus_contact_status' ), 'CRM contact archived' );
	check( 'run-20261025' === get_post_meta( $contact_id, '_nexus_contact_monitor_run' ), 'CRM contact carries the run' );
	check( false === nexus_crm_contact_is_sales_relevant( $contact_id ), 'Never a sales opportunity' );
	check( '' === hu_monitor_current_run(), 'Run marker cleared after the request' );
	check( is_array( hu_monitor_get_intake( 'run-20261025' ) ), 'Intake facts stored for the result endpoint' );
} );

run_case( 'wrong token behaves exactly like a normal request', static function () {
	$r = contact( [ 'monitor_token' => 'wrong-token-0123456789abcdef0123456789', 'monitor_run_id' => 'run-20261025' ] );
	check( 201 === $r->status && ! isset( $r->data['monitor'] ), 'No monitor block' );
	check( 'anfrage_gesendet' === $GLOBALS['wpdb']->rows[0]['event'], 'Counted as a real inquiry' );
	$internal = array_values( array_filter( $GLOBALS['intake_test']['mails'], static fn( $m ) => 'internal@example.test' === $m['to'] ) );
	check( false === strpos( $internal[0]['subject'], 'MONITOR' ), 'No monitor subject' );
	foreach ( $GLOBALS['intake_test']['mails'] as $mail ) {
		check( false === strpos( $mail['subject'], 'MONITOR' ), 'No mail subject contains MONITOR' );
	}
	check( 'new' === get_post_meta( $r->data['contactId'], '_nexus_contact_status' ), 'Normal CRM status' );
} );

run_case( 'marker skips the rate limit, honeypot still drops', static function () {
	for ( $i = 0; $i < 10; $i++ ) {
		$r = contact( marker( 'run-rate-' . $i . 'xx' ) );
		check( 201 === $r->status, "Monitor request $i not rate limited" );
	}
	for ( $i = 0; $i < 9; $i++ ) {
		$last = contact( [ 'email' => "person$i@example.test" ] );
	}
	check( 429 === $last->status, 'Normal requests are still rate limited' );
	$GLOBALS['intake_test']['mails'] = [];
	$r = contact( marker() + [ 'company_website' => 'https://spam.example' ] );
	check( 200 === $r->status && [] === $GLOBALS['intake_test']['mails'], 'Honeypot still drops silently' );
} );

run_case( 'result endpoint: auth, normalisation, server facts win', static function () {
	check( is_wp_error( hu_monitor_authorize( new MonitorRequest( [] ) ) ), 'No header: 401' );
	check( is_wp_error( hu_monitor_authorize( new MonitorRequest( [], 'Bearer nope' ) ) ), 'Wrong bearer: 401' );
	check( true === hu_monitor_authorize( new MonitorRequest( [], 'Bearer ' . HU_MONITOR_TOKEN ) ), 'Valid bearer' );

	contact( marker() );
	$all = array_fill_keys( HU_MONITOR_CHECKS, true );
	$r   = hu_monitor_handle_result( new MonitorRequest( [ 'runId' => 'run-20261025', 'startedAt' => gmdate( 'c' ), 'checks' => $all ] ) );
	check( 201 === $r->status && true === $r->data['run']['ok'], 'Complete run is ok' );

	$r = hu_monitor_handle_result( new MonitorRequest( [ 'runId' => 'run-unknown1', 'startedAt' => gmdate( 'c' ), 'checks' => $all ] ) );
	check( false === $r->data['run']['ok'] && 'crmStored' === $r->data['run']['failedAt'], 'Unknown run: server did not see the request' );

	$r = hu_monitor_handle_result( new MonitorRequest( [ 'runId' => 'run-20261025', 'startedAt' => gmdate( 'c' ), 'checks' => [ 'mailAdmin' => false ] + $all ] ) );
	check( 'mailAdmin' === $r->data['run']['failedAt'], 'First failing check named' );

	check( 400 === hu_monitor_handle_result( new MonitorRequest( [ 'runId' => 'BAD ID', 'startedAt' => gmdate( 'c' ) ] ) )->status, 'Invalid run id rejected' );
} );

run_case( 'status: inquiries light', static function () {
	$now = time();
	check( 'none' === hu_monitor_status( $now )['anfragen']['state'], 'No run yet' );
	hu_monitor_store_run( [ 'runId' => 'run-a1b2c3d4', 'startedAt' => gmdate( 'c', $now - 3600 ), 'ok' => true, 'failedAt' => null, 'checks' => [], 'durationsMs' => [] ] );
	check( 'ok' === hu_monitor_status( $now )['anfragen']['state'], 'Fresh ok run' );
	check( 'warn' === hu_monitor_status( $now + 30 * 3600 )['anfragen']['state'], 'Stale run warns' );
	hu_monitor_store_run( [ 'runId' => 'run-e5f6g7h8', 'startedAt' => gmdate( 'c', $now - 60 ), 'ok' => false, 'failedAt' => 'mailAdmin', 'checks' => [], 'durationsMs' => [] ] );
	$s = hu_monitor_status( $now );
	check( 'fail' === $s['anfragen']['state'] && 'mailAdmin' === $s['anfragen']['failedAt'], 'Latest failure wins' );
	check( 2 === count( $s['verlauf'] ) && false === $s['verlauf'][0]['ok'], 'History newest first' );
	check( false === strpos( wp_json_encode( $s ), '@' ), 'No e-mail address in the status' );
} );

run_case( 'status: updates light', static function () {
	$now = time();
	check( 'ok' === hu_monitor_status_updates( $now )['state'], 'Nothing pending' );
	$GLOBALS['intake_test']['site_transients']['update_plugins'] = (object) [ 'response' => [ 'a/a.php' => (object) [], 'b/b.php' => (object) [] ] ];
	$u = hu_monitor_status_updates( $now );
	check( 'warn' === $u['state'] && 2 === $u['pending'], 'Pending updates warn' );
	check( 'fail' === hu_monitor_status_updates( $now + 8 * DAY_IN_SECONDS )['state'], 'Open longer than 7 days fails' );
} );

run_case( 'status: silence light', static function () {
	$now = strtotime( '2026-10-25 08:00:00 UTC' );
	$GLOBALS['wpdb']->col = [ '2026-10-01 09:00:00', '2026-10-03 09:00:00' ];
	check( 'none' === hu_monitor_status_silence( $now )['state'], 'Too few inquiries: no statement' );
	$GLOBALS['wpdb']->col = [ '2026-09-20 09:00:00', '2026-09-24 09:00:00', '2026-09-27 09:00:00', '2026-10-01 09:00:00', '2026-10-05 09:00:00', '2026-10-08 09:00:00', '2026-10-20 09:00:00' ];
	$s = hu_monitor_status_silence( $now );
	check( 'ok' === $s['state'] && 4 === $s['days'] && 18 === $s['threshold'], 'Within the usual gaps (threshold 1.5 x p90 = 18)' );
	check( 'fail' === hu_monitor_status_silence( $now + 20 * DAY_IN_SECONDS )['state'], 'Unusual silence fails' );
} );

echo "OK anfrage-monitor\n";
