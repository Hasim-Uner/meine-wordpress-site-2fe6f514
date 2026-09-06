<?php
/**
 * SEO Cockpit GENESIS compatibility and diagnostics.
 *
 * GENESIS-Online's current POST API expects public table requests to use a
 * supported area such as "all". The original provider used the legacy/invalid
 * value "free", which makes otherwise valid token-authenticated requests fail.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize GENESIS table requests to the current supported area value.
 *
 * @param array<string,mixed> $args HTTP request arguments.
 * @param string              $url  Request URL.
 * @return array<string,mixed>
 */
function nexus_fix_seo_cockpit_destatis_request_area( $args, $url ) {
	$host = strtolower( (string) wp_parse_url( (string) $url, PHP_URL_HOST ) );
	$path = (string) wp_parse_url( (string) $url, PHP_URL_PATH );

	if ( 'genesis.destatis.de' !== $host || false === strpos( $path, '/genesisWS/rest/2020/data/table' ) ) {
		return $args;
	}

	if ( isset( $args['body'] ) && is_array( $args['body'] ) ) {
		$area = strtolower( trim( (string) ( $args['body']['area'] ?? '' ) ) );
		if ( '' === $area || 'free' === $area ) {
			$args['body']['area'] = 'all';
		}
	}

	return $args;
}
add_filter( 'http_request_args', 'nexus_fix_seo_cockpit_destatis_request_area', 20, 2 );

/**
 * Return the GENESIS logincheck endpoint.
 *
 * @return string
 */
function nexus_get_seo_cockpit_destatis_logincheck_endpoint() {
	return 'https://genesis.destatis.de/genesisWS/rest/2020/helloworld/logincheck';
}

/**
 * Test the stored personal GENESIS token without exposing it in the UI.
 *
 * The official API accepts the personal token in the username request header
 * and an empty password header. GENESIS also uses this endpoint to clean up
 * requests that have been running for more than 15 minutes.
 *
 * @return array{ok:bool,message:string,checked_at:int}
 */
function nexus_test_seo_cockpit_destatis_connection() {
	$token = function_exists( 'nexus_get_seo_cockpit_destatis_api_token' )
		? nexus_get_seo_cockpit_destatis_api_token()
		: '';

	$result = [
		'ok'         => false,
		'message'    => 'Kein GENESIS API-Token hinterlegt.',
		'checked_at' => time(),
	];

	if ( '' === $token ) {
		return $result;
	}

	$response = wp_remote_post(
		nexus_get_seo_cockpit_destatis_logincheck_endpoint(),
		[
			'timeout' => 12,
			'headers' => [
				'Accept'       => 'application/json',
				'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
				'username'     => $token,
				'password'     => '',
			],
			'body' => [
				'language' => 'de',
			],
		]
	);

	if ( is_wp_error( $response ) ) {
		$result['message'] = 'GENESIS-Verbindung fehlgeschlagen: ' . $response->get_error_message();
		return $result;
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	$body   = json_decode( (string) wp_remote_retrieve_body( $response ), true );
	$body   = is_array( $body ) ? $body : [];

	$raw_status = $body['Status'] ?? $body['status'] ?? '';
	if ( is_array( $raw_status ) ) {
		$api_status = sanitize_text_field( (string) ( $raw_status['Content'] ?? $raw_status['content'] ?? '' ) );
	} else {
		$api_status = sanitize_text_field( (string) $raw_status );
	}

	if ( $status >= 200 && $status < 300 && false !== stripos( $api_status, 'erfolgreich' ) ) {
		$result['ok']      = true;
		$result['message'] = 'GENESIS API-Token wurde erfolgreich authentifiziert.';
		return $result;
	}

	$message = '' !== $api_status
		? $api_status
		: sanitize_text_field( (string) ( $body['Content'] ?? 'Unbekannte GENESIS-Antwort.' ) );
	$result['message'] = sprintf( 'GENESIS antwortet mit HTTP %1$d: %2$s', $status, $message ?: 'Authentifizierung nicht bestätigt.' );

	return $result;
}

/**
 * Update the connection diagnostic.
 *
 * This function is deliberately not registered as a second callback on the
 * background-refresh hook. The async Research runner invokes it from inside
 * its shared lock so logincheck and the table requests cannot overlap across
 * multiple cron workers.
 *
 * @return void
 */
function nexus_refresh_seo_cockpit_destatis_connection_diagnostic() {
	if ( ! function_exists( 'nexus_get_seo_cockpit_destatis_api_token' ) || '' === nexus_get_seo_cockpit_destatis_api_token() ) {
		delete_option( 'nexus_seo_cockpit_destatis_connection_status' );
		return;
	}

	update_option(
		'nexus_seo_cockpit_destatis_connection_status',
		nexus_test_seo_cockpit_destatis_connection(),
		false
	);
}

/**
 * Surface the last background authentication result on the Research page.
 *
 * @return void
 */
function nexus_render_seo_cockpit_destatis_connection_notice() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( ! function_exists( 'nexus_get_seo_cockpit_research_slug' ) || nexus_get_seo_cockpit_research_slug() !== $page ) {
		return;
	}

	if ( ! function_exists( 'nexus_get_seo_cockpit_destatis_api_token' ) || '' === nexus_get_seo_cockpit_destatis_api_token() ) {
		return;
	}

	$status = get_option( 'nexus_seo_cockpit_destatis_connection_status', [] );
	if ( ! is_array( $status ) || empty( $status['checked_at'] ) ) {
		return;
	}

	$class   = ! empty( $status['ok'] ) ? 'notice notice-success' : 'notice notice-warning';
	$message = sanitize_text_field( (string) ( $status['message'] ?? '' ) );
	if ( '' === $message ) {
		return;
	}

	printf(
		'<div class="%1$s"><p><strong>GENESIS:</strong> %2$s</p></div>',
		esc_attr( $class ),
		esc_html( $message )
	);
}
add_action( 'admin_notices', 'nexus_render_seo_cockpit_destatis_connection_notice' );
