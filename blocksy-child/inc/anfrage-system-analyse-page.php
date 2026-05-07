<?php
/**
 * Anfrage-System-Analyse route and legacy redirect.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical request path for the Anfrage-System-Analyse.
 *
 * @return string
 */
function hu_get_request_analysis_request_path() {
	return trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/anfrage-system-analyse/' ), PHP_URL_PATH ), '/' ) );
}

/**
 * Return legacy paths that should redirect to the German analysis route.
 *
 * @return array<int, string>
 */
function hu_get_request_analysis_legacy_paths() {
	return [
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
 * Redirect legacy intake routes to the German analysis route.
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

	$target_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/anfrage-system-analyse/' );

	nocache_headers();
	wp_safe_redirect( $target_url, 301 );
	exit;
}
add_action( 'template_redirect', 'hu_redirect_legacy_request_analysis_paths', 5 );

/**
 * Prevent canonical redirects from fighting the virtual analysis route.
 *
 * @param string|false $redirect_url Redirect target.
 * @return string|false
 */
function hu_disable_canonical_redirect_for_request_analysis( $redirect_url ) {
	if ( hu_is_request_analysis_request_path() ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'hu_disable_canonical_redirect_for_request_analysis' );

/**
 * Turn /anfrage-system-analyse/ into a virtual page when no WP page owns it.
 *
 * @param bool     $preempt Existing preempt flag.
 * @param WP_Query $wp_query Current query.
 * @return bool
 */
function hu_preempt_request_analysis_404( $preempt, $wp_query ) {
	if ( is_admin() || wp_doing_ajax() || ! ( $wp_query instanceof WP_Query ) ) {
		return $preempt;
	}

	if ( ! hu_is_request_analysis_request_path() || ! HU_FEATURE_REQUEST_ANALYSIS_ROUTE ) {
		return $preempt;
	}

	$wp_query->is_404            = false;
	$wp_query->is_page           = true;
	$wp_query->is_singular       = true;
	$wp_query->is_home           = false;
	$wp_query->is_archive        = false;
	$wp_query->is_posts_page     = false;
	$wp_query->queried_object    = null;
	$wp_query->queried_object_id = 0;
	$wp_query->query_vars['pagename'] = 'anfrage-system-analyse';
	unset( $wp_query->query['error'], $wp_query->query_vars['error'] );

	status_header( 200 );

	return true;
}
add_filter( 'pre_handle_404', 'hu_preempt_request_analysis_404', 10, 2 );

/**
 * Use the staged React template for the analysis route.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function hu_use_virtual_request_analysis_template( $template ) {
	if ( ! hu_is_request_analysis_request_path() || ! HU_FEATURE_REQUEST_ANALYSIS_ROUTE ) {
		return $template;
	}

	$virtual_template = get_stylesheet_directory() . '/page-readiness-diagnose.php';

	if ( file_exists( $virtual_template ) ) {
		return $virtual_template;
	}

	return $template;
}
add_filter( 'template_include', 'hu_use_virtual_request_analysis_template', 99 );

/**
 * Remove 404 body classes for the virtual analysis route.
 *
 * @param array<int, string> $classes Existing body classes.
 * @return array<int, string>
 */
function hu_add_virtual_request_analysis_body_class( $classes ) {
	if ( ! hu_is_request_analysis_request_path() ) {
		return $classes;
	}

	$classes   = array_diff( $classes, [ 'error404' ] );
	$classes[] = 'page';
	$classes[] = 'page-anfrage-system-analyse';
	$classes[] = 'page-template-page-readiness-diagnose';

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'hu_add_virtual_request_analysis_body_class', 20 );
