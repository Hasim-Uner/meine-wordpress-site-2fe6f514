<?php
/**
 * Server-side `anfrage_gesendet` log: one row per accepted request, correct form,
 * no personal data, never a reason to fail a request. Runs the real handlers with
 * isolated WP boundaries (no database, mail or network).
 */
require __DIR__ . '/intake-wordpress-doubles.php';
define( 'ARRAY_A', 'ARRAY_A' );
foreach ( [ 'canon/messaging-canon', 'canon/pricing-canon', 'canon/e3-proof-canon', 'crm', 'contact-page', 'whitelabel-request', 'review-crm', 'inquiry-events' ] as $module ) {
	require __DIR__ . '/../../blocksy-child/inc/' . $module . '.php';
}

class InquiryDatabase {
	public $prefix = 'wp_';
	public $rows = [];
	public $fail = false;
	public $summary_rows = [];
	public function insert( $table, $data, $format ) {
		if ( $this->fail ) return false;
		$this->rows[] = [ 'table' => $table ] + $data;
		return 1;
	}
	public function prepare( $sql, ...$values ) { return [ $sql, $values ]; }
	public function get_results( $query, $format ) { return $this->summary_rows; }
}
function check( $condition, $message ) { if ( ! $condition ) throw new RuntimeException( $message ); }
function run_case( $name, $callback ) {
	intake_reset();
	update_option( 'hu_inquiry_events_schema', '1' );
	$GLOBALS['wpdb'] = new InquiryDatabase();
	unset( $_SERVER['HTTP_REFERER'] );
	$callback();
	echo "PASS $name\n";
}
function rows() { return $GLOBALS['wpdb']->rows; }
function submit( $kind, $payload ) {
	$callbacks = [ 'contact' => 'nexus_handle_contact_request_submission', 'whitelabel' => 'hu_handle_whitelabel_request_submission', 'audit' => 'nexus_handle_review_request_submission' ];
	return $callbacks[ $kind ]( new WP_REST_Request( $payload ) );
}

$contact     = [ 'name' => 'Fixture Person', 'email' => 'fixture@example.test', 'request_type' => 'project', 'focus' => 'tracking', 'message' => 'Bitte das bestehende Tracking prüfen.', 'consent' => '1' ];
$assessment  = [ 'email' => 'fixture@example.test', 'request_type' => 'ersteinschaetzung', 'focus' => 'ersteinschaetzung', 'website_url' => 'https://www.example.test/seite', 'message' => '', 'consent' => '1' ];
$whitelabel  = [ 'email' => 'fixture@example.test', 'task' => 'Bitte das bestehende Tracking prüfen.' ];
$marketcheck = [ 'intake_variant' => 'energy_systems', 'audit_type' => 'b2b_system_intake', 'solution_focus' => 'photovoltaik', 'business_fit' => 'founder_led_regional', 'sales_team_size' => 'one', 'project_timing' => 'sofort', 'name' => 'Fixture Person', 'company' => 'Fixture GmbH', 'position' => 'Inhaber', 'email' => 'fixture@example.test', 'postal_code' => '30159', 'consent_privacy' => 'accepted' ];
$order_base  = [ 'company' => 'TEST Fixture GmbH', 'email' => 'fixture@gmx.de', 'phone' => '0301234567', 'request_sources' => [ 'aroundhome', 'eigene_website' ], 'consent_privacy' => 'accepted', 'contract_version' => nexus_get_review_request_contract_version() ];
$sofortkontakt = $order_base + [ 'intake_variant' => 'sofortkontakt', 'audit_type' => 'sofortkontakt_setup', 'lead_volume' => '20_50', 'lead_destination' => 'crm', 'crm_name' => 'TEST CRM', 'callback_name' => 'TEST Fixture Person', 'callback_mobile' => '01761234567', 'desired_start' => 'diese_woche' ];
$analyse       = $order_base + [ 'intake_variant' => 'analyse', 'audit_type' => 'anfragesystem_analyse', 'page_url' => 'https://example.test/', 'analysis_question' => 'TEST Anfrageweg prüfen', 'crm_name' => 'TEST CRM' ];

$cases = [
	'kontakt'           => [ 'contact', $contact ],
	'ersteinschaetzung' => [ 'contact', $assessment ],
	'sst'               => [ 'contact', $contact + [ 'form_origin' => 'sst' ] ],
	'whitelabel'        => [ 'whitelabel', $whitelabel ],
	'marktcheck'        => [ 'audit', $marketcheck ],
	'analyse'           => [ 'audit', $analyse ],
	'sofortkontakt'     => [ 'audit', $sofortkontakt ],
];
$known = array_keys( hu_inquiry_event_forms() );
$tested = array_keys( $cases );
sort( $known );
sort( $tested );
check( $known === $tested, 'Every known form has a test case' );

foreach ( $cases as $form => $case ) {
	run_case( "one row for $form", static function () use ( $form, $case ) {
		$r = submit( $case[0], $case[1] );
		check( true === $r->data['ok'] && 201 === $r->status, 'Request accepted' );
		check( 1 === count( rows() ), 'Exactly one row per accepted request' );
		$row = rows()[0];
		check( $form === $row['form'], "Form is $form, got {$row['form']}" );
		check( 'anfrage_gesendet' === $row['event'] && 'wp_hu_inquiry_events' === $row['table'], 'Event and table' );
		check( 1 === preg_match( '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $row['created_at'] ), 'UTC timestamp' );
		check( [ 'table', 'event', 'form', 'seite', 'utm_source', 'utm_campaign', 'created_at' ] === array_keys( $row ), 'Only the agreed columns are written' );
	} );
}

run_case( 'source, campaign and page path, no query string', static function () use ( $contact ) {
	$_SERVER['HTTP_REFERER'] = 'https://example.test/kontakt/?focus=ersteinschaetzung&email=private@example.test';
	submit( 'contact', $contact + [ 'ads_source' => 'Google Ads', 'utm_campaign' => 'Sommer 2026 <b>', 'utm_medium' => 'cpc' ] );
	$row = rows()[0];
	check( '/kontakt/' === $row['seite'], 'Path only' );
	check( 'google-ads' === $row['utm_source'], 'ads_source (the contact form field for utm_source) is normalized' );
	check( 'sommer-2026-b' === $row['utm_campaign'], 'Campaign reduced to a safe token' );
	run_case( 'utm_source alias wins over ads_source', static function () use ( $contact ) {
		submit( 'contact', $contact + [ 'utm_source' => 'newsletter', 'ads_source' => 'other' ] );
		check( 'newsletter' === rows()[0]['utm_source'], 'utm_source alias' );
	} );
} );

run_case( 'foreign or missing referer falls back to landing page, else empty', static function () use ( $contact ) {
	$_SERVER['HTTP_REFERER'] = 'https://foreign.example/kontakt/';
	submit( 'contact', $contact + [ 'landing_page_url' => 'https://example.test/server-side-tracking-b2b/?x=1' ] );
	check( '/server-side-tracking-b2b/' === rows()[0]['seite'], 'Own landing page path used, foreign referer dropped' );
	$_SERVER['HTTP_REFERER'] = 'https://foreign.example/kontakt/';
	submit( 'contact', $contact );
	check( '' === rows()[1]['seite'], 'No same-site page, no path' );
} );

run_case( 'no personal data in any column', static function () use ( $contact ) {
	$_SERVER['HTTP_REFERER'] = 'https://example.test/kontakt/';
	submit( 'contact', $contact + [ 'ads_source' => 'google', 'utm_campaign' => 'brand' ] );
	$dump = json_encode( rows() );
	foreach ( [ 'fixture@example.test', 'Fixture Person', 'Tracking prüfen', '192.0.2.1' ] as $private ) {
		check( false === strpos( $dump, $private ), "Row contains no $private" );
	}
} );

run_case( 'ersteinschaetzung without name: domain label, no stored name, plain thanks', static function () use ( $assessment ) {
	$v = nexus_validate_contact_request_payload( $assessment );
	check( ! is_wp_error( $v ) && 'example.test' === $v['name'] && false === $v['name_provided'], 'Domain stands in for the name, www removed' );
	$r = submit( 'contact', $assessment );
	check( 201 === $r->status, 'Accepted without name' );
	check( '' === get_post_meta( 1, '_nexus_contact_name' ), 'No name is written when none was asked' );
	$confirmation = $GLOBALS['intake_test']['mails'][1]['body'];
	check( false !== strpos( $confirmation, 'Danke. Ich prüfe' ) && false === strpos( $confirmation, 'Danke, example.test' ), 'Confirmation does not greet a domain' );
	check( false !== strpos( $GLOBALS['intake_test']['mails'][0]['subject'], 'example.test' ), 'Internal subject names the domain' );
} );

run_case( 'ersteinschaetzung keeps a known contact name', static function () use ( $contact, $assessment ) {
	submit( 'contact', $contact );
	submit( 'contact', $assessment );
	check( 'Fixture Person' === get_post_meta( 1, '_nexus_contact_name' ), 'Earlier name survives a nameless request' );
} );

run_case( 'other request types still need a name; assessment still needs a URL', static function () use ( $contact, $assessment ) {
	$r = submit( 'contact', array_merge( $contact, [ 'name' => '' ] ) );
	check( 400 === $r->status && 'missing_name' === $r->data['error_code'], 'Name stays required for other types' );
	$r = submit( 'contact', array_merge( $assessment, [ 'website_url' => '' ] ) );
	check( 400 === $r->status && 'missing_website' === $r->data['error_code'], 'URL stays required for the assessment' );
	check( ! rows(), 'Rejected requests leave no row' );
} );

foreach ( $cases as $form => $case ) {
	run_case( "$form: honeypot, validation error and rate limit leave no row", static function () use ( $case ) {
		[ $kind, $payload ] = $case;
		$r = submit( $kind, $payload + [ 'company_website' => 'bot' ] );
		check( 200 === $r->status && ! rows(), 'Honeypot' );
		$r = submit( $kind, array_merge( $payload, [ 'email' => 'invalid' ] ) );
		check( 400 === $r->status && ! rows(), 'Validation error' );
		$prefix = 'audit' === $kind ? 'nexus_review_rl_' : 'nexus_contact_rl_';
		set_transient( $prefix . md5( '192.0.2.1' . gmdate( 'YmdH' ) ), 10, HOUR_IN_SECONDS );
		$r = submit( $kind, $payload );
		check( 429 === $r->status && ! rows(), 'Rate limit' );
	} );
}

run_case( 'both CRM and mail failed: error, no row', static function () use ( $contact ) {
	intake_reset( [ 'storage_fail' => true, 'internal_mail' => false ] );
	$r = submit( 'contact', $contact );
	check( 500 === $r->status && ! rows(), 'Failed request is not counted' );
} );

run_case( 'CRM failed but internal mail delivered: accepted and counted once', static function () use ( $contact, $whitelabel ) {
	intake_reset( [ 'storage_fail' => true ] );
	update_option( 'hu_inquiry_events_schema', '1' );
	check( 201 === submit( 'contact', $contact )->status && 1 === count( rows() ), 'Contact' );
	check( 201 === submit( 'whitelabel', $whitelabel )->status && 2 === count( rows() ), 'White-Label' );
} );

run_case( 'a failing log write never fails the request', static function () use ( $contact, $marketcheck ) {
	$GLOBALS['wpdb']->fail = true;
	$r = submit( 'contact', $contact );
	check( 201 === $r->status && true === $r->data['ok'] && 2 === count( $GLOBALS['intake_test']['mails'] ), 'Contact still accepted and confirmed' );
	$r = submit( 'audit', $marketcheck );
	check( 201 === $r->status && true === $r->data['ok'], 'Marktcheck still accepted' );
	unset( $GLOBALS['wpdb'] );
	check( 201 === submit( 'contact', $contact )->status, 'No database object at all is tolerated' );
} );

run_case( 'unknown form is never written', static function () {
	check( false === hu_record_inquiry_event( 'newsletter', [] ) && false === hu_record_inquiry_event( '', [] ) && ! rows(), 'Whitelist' );
} );

run_case( 'summary groups by form and source in display order', static function () {
	$GLOBALS['wpdb']->summary_rows = [
		[ 'form' => 'sst', 'utm_source' => '', 'total' => '1' ],
		[ 'form' => 'kontakt', 'utm_source' => 'google', 'total' => '2' ],
		[ 'form' => 'kontakt', 'utm_source' => '', 'total' => '5' ],
		[ 'form' => 'unbekannt', 'utm_source' => 'x', 'total' => '9' ],
	];
	$summary = hu_inquiry_events_summary();
	check( [ 'kontakt', 'sst' ] === array_keys( $summary ), 'Display order, unknown rows dropped' );
	check( 7 === $summary['kontakt']['total'] && [ '' => 5, 'google' => 2 ] === $summary['kontakt']['sources'], 'Totals and sources, largest first' );
} );

echo "Inquiry event log passed; the database write itself is not exercised.\n";
