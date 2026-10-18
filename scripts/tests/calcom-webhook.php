<?php
/**
 * Cal.com webhook: signature, CRM write, timeline, internal mail, idempotency
 * and failure modes. Real handler, isolated WP boundaries (no DB, mail, network).
 */
require __DIR__ . '/intake-wordpress-doubles.php';
if ( ! function_exists( 'wp_date' ) ) { function wp_date( $format, $timestamp = null ) { return gmdate( $format, $timestamp ?? time() ); } }
if ( ! function_exists( 'wp_salt' ) ) { function wp_salt( $scheme = 'auth' ) { return 'fixture-salt'; } }
foreach ( [ 'canon/messaging-canon', 'canon/pricing-canon', 'canon/e3-proof-canon', 'crm', 'contact-page', 'calcom-webhook' ] as $module ) {
	require __DIR__ . '/../../blocksy-child/inc/' . $module . '.php';
}
function check( $condition, $message ) { if ( ! $condition ) throw new RuntimeException( 'FAIL: ' . $message ); }
function run( $name, $callback ) { intake_reset(); $callback(); echo "PASS $name\n"; }
class Signed_Request extends WP_REST_Request {
	public function __construct( private string $body, private string $signature ) { parent::__construct( [] ); }
	public function get_body() { return $this->body; }
	public function get_header( $name ) { return in_array( $name, [ 'x_cal_signature_256', 'x-cal-signature-256' ], true ) ? $this->signature : ''; }
}
function booking( $trigger = 'BOOKING_CREATED', $extra = [] ) {
	return [ 'triggerEvent' => $trigger, 'createdAt' => '2026-10-18T10:00:00Z', 'payload' => array_replace_recursive( [
		'uid' => 'abc123', 'title' => '30 Min Gespräch', 'startTime' => '2026-10-21T08:30:00Z', 'endTime' => '2026-10-21T09:00:00Z',
		'attendees' => [ [ 'name' => 'Fixture Person', 'email' => 'fixture@example.test', 'timeZone' => 'Europe/Berlin' ] ],
		'responses' => [ 'notes' => [ 'value' => 'Relaunch geplant.' ] ],
		'metadata' => [ 'quelle' => 'chat', 'seite' => '/whitelabel-retainer/' ],
	], $extra ) ];
}
$secret = 'fixture-secret';
$sign   = static fn( $body ) => hash_hmac( 'sha256', $body, $secret );

run( 'signature required and checked on the raw body', static function () use ( $secret, $sign ) {
	update_option( HU_CALCOM_SECRET_OPTION, $secret );
	$body = json_encode( booking() );
	check( 401 === hu_calcom_handle_webhook( new Signed_Request( $body, '' ) )->status, 'missing signature rejected' );
	check( 401 === hu_calcom_handle_webhook( new Signed_Request( $body, hash_hmac( 'sha256', $body, 'other' ) ) )->status, 'foreign secret rejected' );
	check( 401 === hu_calcom_handle_webhook( new Signed_Request( $body . ' ', $sign( $body ) ) )->status, 'altered body rejected' );
	check( [] === $GLOBALS['intake_test']['posts'] && [] === $GLOBALS['intake_test']['mails'], 'nothing written on bad signature' );
	check( 201 === hu_calcom_handle_webhook( new Signed_Request( $body, strtoupper( $sign( $body ) ) ) )->status, 'valid signature accepted' );
} );

run( 'no secret: route stays unregistered and every call fails', static function () use ( $sign ) {
	$body = json_encode( booking() );
	check( 401 === hu_calcom_handle_webhook( new Signed_Request( $body, $sign( $body ) ) )->status, 'empty secret never validates' );
} );

run( 'booking creates contact, timeline, mail and event', static function () {
	$r = hu_calcom_process_event( booking() );
	check( 201 === $r->status && true === $r->data['ok'], 'accepted' );
	check( 1 === count( $GLOBALS['intake_test']['posts'] ), 'one contact' );
	check( 'calendar_booking' === get_post_meta( 1, '_nexus_contact_latest_source' ), 'source calendar_booking' );
	check( 'new' === get_post_meta( 1, '_nexus_contact_status' ), 'new contact status' );
	check( 'Fixture Person' === get_post_meta( 1, '_nexus_contact_name' ), 'name stored' );
	$activity = end( $GLOBALS['intake_test']['activities'] );
	check( str_starts_with( $activity['subject'], 'Termin gebucht · 21.10.' ), 'timeline subject' );
	check( str_contains( $activity['body'], 'Quelle: KI-Assistent' ) && str_contains( $activity['body'], 'Seite: /whitelabel-retainer/' ) && str_contains( $activity['body'], 'Relaunch geplant.' ), 'timeline body with source, page and note' );
	$mail = $GLOBALS['intake_test']['mails'][0];
	check( str_contains( $mail['subject'], '[Termin] Termin gebucht — Fixture Person' ), 'internal subject' );
	check( str_contains( $mail['body'], 'KI-Assistent' ) && str_contains( $mail['body'], 'Neuer Kontakt.' ), 'mail names source and history' );
	check( in_array( 'Reply-To: fixture@example.test', $mail['headers'], true ), 'reply goes to the guest' );
} );

run( 'retry of the same event is idempotent', static function () {
	hu_calcom_process_event( booking() );
	$r = hu_calcom_process_event( booking() );
	check( 200 === $r->status && ! empty( $r->data['duplicate'] ), 'duplicate acknowledged' );
	check( 1 === count( $GLOBALS['intake_test']['mails'] ) && 1 === count( $GLOBALS['intake_test']['activities'] ), 'no second mail or activity' );
} );

run( 'existing contact keeps status and gets history', static function () {
	hu_calcom_process_event( booking() );
	update_post_meta( 1, '_nexus_contact_status', 'active' );
	update_post_meta( 1, '_nexus_contact_sources', [ 'chat_handover', 'calendar_booking' ] );
	update_post_meta( 1, '_nexus_contact_request_type_label', 'Chat-Anfrage' );
	$r = hu_calcom_process_event( booking( 'BOOKING_RESCHEDULED', [ 'startTime' => '2026-10-22T08:30:00Z' ] ) );
	check( 201 === $r->status && 1 === count( $GLOBALS['intake_test']['posts'] ), 'same contact' );
	check( 'active' === get_post_meta( 1, '_nexus_contact_status' ), 'status untouched' );
	check( str_starts_with( end( $GLOBALS['intake_test']['activities'] )['subject'], 'Termin verschoben' ), 'reschedule on timeline' );
	check( str_contains( end( $GLOBALS['intake_test']['mails'] )['body'], 'Bekannt aus: Chat-Anfrage. Letzte Anfrage: Chat-Anfrage.' ), 'history in mail' );
} );

run( 'cancellation carries the reason', static function () {
	hu_calcom_process_event( booking( 'BOOKING_CANCELLED', [ 'cancellationReason' => 'Krank.' ] ) );
	check( str_contains( end( $GLOBALS['intake_test']['activities'] )['body'], 'Absagegrund: Krank.' ), 'reason on timeline' );
} );

run( 'ping, unknown events and broken bookings are acknowledged without writes', static function () {
	check( 200 === hu_calcom_process_event( [ 'triggerEvent' => 'PING' ] )->status, 'ping' );
	check( 200 === hu_calcom_process_event( booking( 'MEETING_ENDED' ) )->status, 'unknown event' );
	check( 200 === hu_calcom_process_event( booking( 'BOOKING_CREATED', [ 'attendees' => [ [ 'email' => 'kaputt' ] ] ] ) )->status, 'invalid email' );
	check( [] === $GLOBALS['intake_test']['posts'] && [] === $GLOBALS['intake_test']['mails'], 'no writes' );
} );

run( 'metadata is reduced to a token and a path', static function () {
	hu_calcom_process_event( booking( 'BOOKING_CREATED', [ 'metadata' => [ 'quelle' => '<script>X', 'seite' => 'https://evil.test/' ] ] ) );
	$body = end( $GLOBALS['intake_test']['activities'] )['body'];
	check( str_contains( $body, 'Quelle: Website · scriptx' ) && ! str_contains( $body, 'evil.test' ), 'sanitized source, foreign page dropped' );
} );

run( 'no metadata means a direct Cal.com booking', static function () {
	hu_calcom_process_event( booking( 'BOOKING_CREATED', [ 'metadata' => [ 'quelle' => '', 'seite' => '' ] ] ) );
	check( str_contains( end( $GLOBALS['intake_test']['activities'] )['body'], 'Direkt in Cal.com' ), 'direct label' );
} );

run( 'CRM failure still mails; both failing asks Cal.com to retry', static function () {
	intake_reset( [ 'storage_fail' => true ] );
	check( 201 === hu_calcom_process_event( booking() )->status && 1 === count( $GLOBALS['intake_test']['mails'] ), 'mail without CRM' );
	intake_reset( [ 'storage_fail' => true, 'internal_mail' => false ] );
	check( 500 === hu_calcom_process_event( booking() )->status, 'retry requested' );
	check( 0 === (int) get_transient( 'hu_cal_seen_' . md5( 'abc123|BOOKING_CREATED|' . strtotime( '2026-10-21T08:30:00Z' ) ) ), 'failed event not marked as seen' );
} );

echo "Cal.com webhook passed.\n";
