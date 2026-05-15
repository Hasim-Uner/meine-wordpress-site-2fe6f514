<?php
/**
 * System-Diagnose route and legacy redirect.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical request path for the System-Diagnose.
 *
 * @return string
 */
function hu_get_request_analysis_request_path() {
	return trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/system-diagnose/' ), PHP_URL_PATH ), '/' ) );
}

/**
 * Return legacy paths that should redirect to the current marketcheck entry.
 *
 * @return array<int, string>
 */
function hu_get_request_analysis_legacy_paths() {
	$previous_analysis_path = '/anfrage-' . 'system-' . 'analyse/';

	return [
		trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( $previous_analysis_path ), PHP_URL_PATH ), '/' ) ),
		trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/readiness-diagnose/' ), PHP_URL_PATH ), '/' ) ),
		trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/anfrage/' ), PHP_URL_PATH ), '/' ) ),
	];
}

/**
 * Check whether the current request targets the canonical analysis path.
 *
 * @return bool
 */
function hu_is_request_analysis_request_path() {
	return function_exists( 'nexus_get_current_request_path' ) && nexus_get_current_request_path() === hu_get_request_analysis_request_path();
}

/**
 * Redirect the retired System-Diagnose route to the marketcheck on the
 * Solar-/Wärmepumpen landing page.
 *
 * @return void
 */
function hu_redirect_request_analysis_route_to_marketcheck() {
	if ( is_admin() || wp_doing_ajax() || ! hu_is_request_analysis_request_path() ) {
		return;
	}

	$target_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

	nocache_headers();
	wp_safe_redirect( $target_url, 301 );
	exit;
}
add_action( 'template_redirect', 'hu_redirect_request_analysis_route_to_marketcheck', 4 );

/**
 * Redirect legacy intake routes to the current marketcheck entry.
 *
 * @return void
 */
function hu_redirect_legacy_request_analysis_paths() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	if ( ! in_array( nexus_get_current_request_path(), hu_get_request_analysis_legacy_paths(), true ) ) {
		return;
	}

	$target_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

	nocache_headers();
	wp_safe_redirect( $target_url, 301 );
	exit;
}
add_action( 'template_redirect', 'hu_redirect_legacy_request_analysis_paths', 5 );

