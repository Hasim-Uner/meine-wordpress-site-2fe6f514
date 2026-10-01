<?php
/** Verify the anonymous counter boundary without network, mail or a database. */
require __DIR__ . '/intake-wordpress-doubles.php';
define( 'MINUTE_IN_SECONDS', 60 );
define( 'ARRAY_A', 'ARRAY_A' );
require __DIR__ . '/../../blocksy-child/inc/solar-events.php';
require __DIR__ . '/../../blocksy-child/inc/api-telemetry.php';

function register_rest_route( $namespace, $route, $args ) { $GLOBALS['solar_routes'] = $args; }
function current_user_can( $cap ) { return ! empty( $GLOBALS['solar_admin'] ); }
class SolarRequest extends WP_REST_Request {
	public $origin = 'https://example.test';
	public $site = 'same-origin';
	public $route = '/nexus/v1/solar-events';
	public function get_header( $name ) { return 'origin' === $name ? $this->origin : $this->site; }
	public function get_body() { return json_encode( $this->get_json_params() ); }
	public function get_route() { return $this->route; }
}
class SolarDatabase {
	public $prefix = 'wp_';
	public $writes = [];
	public $fail = false;
	public function prepare( $sql, ...$values ) { return [ $sql, $values ]; }
	public function query( $query ) {
		$this->writes[] = $query;
		return $this->fail ? false : 1;
	}
	public function get_results( $query, $format ) { return []; }
}
function solar_check( $condition, $name ) { if ( ! $condition ) throw new RuntimeException( $name ); }
function solar_reset() {
	intake_reset();
	update_option( 'nexus_solar_events_schema', '1' );
	$GLOBALS['wpdb'] = new SolarDatabase();
}
$valid = [ 'event' => 'form_opened', 'door' => 'marktcheck', 'page' => '/solar-waermepumpen-leadgenerierung/', 'day' => gmdate( 'Y-m-d' ) ];
solar_reset();
nexus_register_solar_events();
$GLOBALS['solar_admin'] = false;
solar_check( ! ( $GLOBALS['solar_routes'][1]['permission_callback'] )(), 'Anonymous aggregate read denied' );
$GLOBALS['solar_admin'] = true;
solar_check( ( $GLOBALS['solar_routes'][1]['permission_callback'] )(), 'Administrator aggregate read allowed' );
echo "PASS protected aggregate read\n";

foreach ( [ 'marktcheck', 'analyse', 'sofortkontakt' ] as $door ) {
	solar_reset();
	$payload = array_merge( $valid, [ 'door' => $door ] );
	for ( $i = 0; $i < 2; $i++ ) {
		$r = nexus_record_solar_event( new SolarRequest( $payload ) );
		solar_check( 202 === $r->status, 'Valid door accepted' );
	}
	$writes = $GLOBALS['wpdb']->writes;
	solar_check( 2 === count( $writes ) && $writes[0] === $writes[1], 'Same daily bucket incremented twice' );
	solar_check( false !== strpos( $writes[0][0], 'ON DUPLICATE KEY UPDATE total' ), 'Atomic increment' );
	solar_check( [ $payload['day'], $payload['event'], $door, $payload['page'] ] === $writes[0][1], 'Only four anonymous dimensions persisted' );
	echo "PASS daily bucket $door\n";
}
$rejected = [
	[ 'event' => 'arbitrary' ], [ 'event' => '<script>' ], [ 'door' => 'unknown' ], [ 'door' => '' ],
	[ 'page' => '/solar-waermepumpen-leadgenerierung/?email=private@example.test' ],
	[ 'day' => '2020-01-01' ], [ 'day' => gmdate( 'Y-m-d', time() + DAY_IN_SECONDS ) ],
	[ 'email' => 'private@example.test' ], [ 'user_id' => '123' ], [ 'event' => [] ],
	[ 'event' => 'form_step_two', 'door' => 'analyse' ],
];
foreach ( $rejected as $changes ) {
	solar_reset();
	$r = nexus_record_solar_event( new SolarRequest( array_merge( $valid, $changes ) ) );
	solar_check( 400 === $r->status && ! $GLOBALS['wpdb']->writes, 'Malformed, identifying or stale payload rejected before storage' );
}
solar_reset();
$r = nexus_record_solar_event( new SolarRequest( array_merge( $valid, [ 'event' => 'link_strecke_vertiefung', 'door' => '' ] ) ) );
solar_check( 202 === $r->status, 'General content click may have no door' );
echo "PASS bounded vocabulary, day and exact private-free payload\n";

foreach ( [ 'origin', 'site' ] as $property ) {
	solar_reset();
	$request = new SolarRequest( $valid );
	$request->$property = 'origin' === $property ? 'https://foreign.example' : 'cross-site';
	$r = nexus_record_solar_event( $request );
	solar_check( 403 === $r->status && ! $GLOBALS['wpdb']->writes, 'Cross-site request blocked' );
}
solar_reset();
$r = nexus_record_solar_event( new SolarRequest( array_merge( $valid, [ 'event' => str_repeat( 'a', 600 ) ] ) ) );
solar_check( 413 === $r->status && ! $GLOBALS['wpdb']->writes, 'Oversized request blocked' );
set_transient( 'nexus_solar_events_rate_' . gmdate( 'YmdHi' ), 1000, 60 );
$r = nexus_record_solar_event( new SolarRequest( $valid ) );
solar_check( 429 === $r->status && ! $GLOBALS['wpdb']->writes, 'Aggregate rate limit blocks before storage' );
solar_reset();
$GLOBALS['wpdb']->fail = true;
$r = nexus_record_solar_event( new SolarRequest( $valid ) );
solar_check( 503 === $r->status, 'Database failure is not acknowledged as success' );
echo "PASS origin, size, rate limit and database failure\n";

foreach ( [ '/nexus/v1/solar-events', '/nexus/v1/solar-events/' ] as $route ) {
	solar_reset();
	$request = new SolarRequest( array_merge( $valid, [ 'email' => 'private@example.test' ] ) );
	$request->route = $route;
	$r = new WP_REST_Response( [ 'ok' => false ], 400 );
	solar_check( $r === nexus_api_telemetry_capture( $r, null, $request ), 'Response preserved' );
	solar_check( ! $GLOBALS['intake_test']['transients'], 'No error telemetry, IP hash or trace stored for counters' );
}
echo "PASS counter errors bypass identifying telemetry\n";
