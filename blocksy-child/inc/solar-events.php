<?php
/** Anonymous daily counters for Solar Smartflow decisions. No visitor identifiers. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @return string[] Accepted event names; arbitrary labels never reach storage. */
function nexus_solar_event_names() {
	return [
		'form_opened', 'form_step_two', 'form_submitted', 'form_validation_error',
		'chapter_jump', 'link_strecke_vertiefung',
		'nav_header_door_marktcheck', 'nav_header_door_system', 'nav_header_door_analyse', 'nav_header_door_sofortkontakt',
		// Die frühere Kopfzeile der Seite; gecachtes HTML sendet den Namen bis zum Cache-Purge noch.
		'cta_strecke_header_to_marktcheck',
		'cta_strecke_kopf_to_marktcheck',
		'cta_strecke_kopf_to_sofortkontakt', 'cta_strecke_kopf_to_system', 'cta_strecke_fall_to_case',
		'cta_strecke_leiter_to_marktcheck', 'cta_strecke_leiter_to_analyse',
		'cta_strecke_leiter_to_sofortkontakt', 'cta_strecke_sofortkontakt_submit',
		'cta_strecke_analyse_submit', 'cta_strecke_marktcheck_fallback_kontakt',
		'cta_strecke_marktcheck_to_about', 'cta_strecke_marktcheck_to_contact',
		'cta_strecke_config_to_contact', 'cta_strecke_sofort_to_contact',
		'cta_strecke_abschluss_to_marktcheck', 'cta_strecke_abschluss_to_system',
		'cta_strecke_abschluss_to_sofortkontakt',
	];
}

/** @return string Canonical path, without query string or fragment. */
function nexus_solar_event_page() {
	return (string) wp_parse_url( home_url( '/solar-waermepumpen-leadgenerierung/' ), PHP_URL_PATH );
}

/** @return bool Deploy-safe lazy schema creation; no theme reactivation needed. */
function nexus_solar_events_install() {
	global $wpdb;
	if ( '1' === get_option( 'nexus_solar_events_schema' ) ) {
		return true;
	}
	$table = $wpdb->prefix . 'nexus_solar_events';
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( "CREATE TABLE {$table} (
		day date NOT NULL,
		event varchar(64) NOT NULL,
		door varchar(16) NOT NULL,
		page varchar(96) NOT NULL,
		total bigint unsigned NOT NULL DEFAULT 0,
		PRIMARY KEY  (day,event,door,page)
	) " . $wpdb->get_charset_collate() . ';' );
	if ( $table !== $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) ) {
		return false;
	}
	update_option( 'nexus_solar_events_schema', '1', false );
	return true;
}

/** @return void */
function nexus_register_solar_events() {
	register_rest_route( 'nexus/v1', '/solar-events', [
		[
			'methods' => 'POST',
			'callback' => 'nexus_record_solar_event',
			'permission_callback' => '__return_true',
		],
		[
			'methods' => 'GET',
			'callback' => 'nexus_read_solar_events',
			'permission_callback' => static function () { return current_user_can( 'manage_options' ); },
		],
	] );
}
add_action( 'rest_api_init', 'nexus_register_solar_events' );

/** @return WP_REST_Response */
function nexus_solar_event_response( $status ) {
	$response = new WP_REST_Response( [ 'ok' => $status < 400 ], $status );
	$response->header( 'Cache-Control', 'no-store' );
	return $response;
}

/**
 * Stateless public write: exact keys, bounded vocabulary and day, same origin.
 * Global transient rate limit has no IP, hash, user agent or visitor key.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function nexus_record_solar_event( WP_REST_Request $request ) {
	global $wpdb;
	$home = wp_parse_url( home_url( '/' ) );
	$origin = $home['scheme'] . '://' . $home['host'] . ( isset( $home['port'] ) ? ':' . $home['port'] : '' );
	if ( $request->get_header( 'origin' ) !== $origin || 'cross-site' === $request->get_header( 'sec-fetch-site' ) ) {
		return nexus_solar_event_response( 403 );
	}
	if ( strlen( $request->get_body() ) > 512 ) {
		return nexus_solar_event_response( 413 );
	}
	$payload = $request->get_json_params();
	$keys = [ 'event', 'door', 'page', 'day' ];
	if ( ! is_array( $payload ) || count( $payload ) !== 4 || array_diff( array_keys( $payload ), $keys ) ) {
		return nexus_solar_event_response( 400 );
	}
	foreach ( $keys as $key ) {
		if ( ! isset( $payload[ $key ] ) || ! is_string( $payload[ $key ] ) ) {
			return nexus_solar_event_response( 400 );
		}
	}
	if ( ! in_array( $payload['event'], nexus_solar_event_names(), true )
		|| ! in_array( $payload['door'], [ '', 'marktcheck', 'system', 'analyse', 'sofortkontakt' ], true )
		|| $payload['page'] !== nexus_solar_event_page()
		|| ! in_array( $payload['day'], [ gmdate( 'Y-m-d' ), gmdate( 'Y-m-d', time() - DAY_IN_SECONDS ) ], true )
		|| ( 0 === strpos( $payload['event'], 'form_' ) && '' === $payload['door'] )
		|| ( 'form_step_two' === $payload['event'] && 'marktcheck' !== $payload['door'] ) ) {
		return nexus_solar_event_response( 400 );
	}
	$limit_key = 'nexus_solar_events_rate_' . gmdate( 'YmdHi' );
	$hits = (int) get_transient( $limit_key );
	if ( $hits >= 1000 ) {
		return nexus_solar_event_response( 429 );
	}
	set_transient( $limit_key, $hits + 1, MINUTE_IN_SECONDS );
	if ( ! nexus_solar_events_install() ) {
		return nexus_solar_event_response( 503 );
	}
	$table = $wpdb->prefix . 'nexus_solar_events';
	// One atomic upsert: concurrent clicks cannot overwrite another increment.
	$written = $wpdb->query( $wpdb->prepare(
		"INSERT INTO {$table} (day,event,door,page,total) VALUES (%s,%s,%s,%s,1)
		 ON DUPLICATE KEY UPDATE total = LEAST(total + 1, 1000000)",
		$payload['day'], $payload['event'], $payload['door'], $payload['page']
	) );
	return nexus_solar_event_response( false === $written ? 503 : 202 );
}

/** @return WP_REST_Response Administrator-only aggregate read for the last 31 days. */
function nexus_read_solar_events() {
	global $wpdb;
	if ( ! nexus_solar_events_install() ) {
		return nexus_solar_event_response( 503 );
	}
	$table = $wpdb->prefix . 'nexus_solar_events';
	$rows = $wpdb->get_results( $wpdb->prepare(
		"SELECT day,event,door,page,total FROM {$table} WHERE day >= %s ORDER BY day DESC,event,door",
		gmdate( 'Y-m-d', time() - 30 * DAY_IN_SECONDS )
	), 'ARRAY_A' );
	$response = new WP_REST_Response( [ 'days' => $rows ], 200 );
	$response->header( 'Cache-Control', 'no-store' );
	return $response;
}
