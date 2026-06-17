<?php
/**
 * Noindex homepage test route for the visual "wow" variant.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical request path for the homepage test route.
 *
 * @return string
 */
function hu_get_homepage_wow_request_path() {
	return trailingslashit( '/' . ltrim( (string) wp_parse_url( home_url( '/startseite-wow/' ), PHP_URL_PATH ), '/' ) );
}

/**
 * Check whether the current request targets the homepage test route.
 *
 * @return bool
 */
function hu_is_homepage_wow_request() {
	return function_exists( 'nexus_get_current_request_path' )
		&& nexus_get_current_request_path() === hu_get_homepage_wow_request_path();
}

/**
 * Prevent canonical redirects from fighting the virtual test route.
 *
 * @param string|false $redirect_url Redirect target.
 * @return string|false
 */
function hu_disable_canonical_redirect_for_homepage_wow( $redirect_url ) {
	if ( hu_is_homepage_wow_request() ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'hu_disable_canonical_redirect_for_homepage_wow' );

/**
 * Turn /startseite-wow/ into a virtual page without requiring an editor page.
 *
 * @param bool     $preempt Existing preempt flag.
 * @param WP_Query $wp_query Current query.
 * @return bool
 */
function hu_preempt_homepage_wow_404( $preempt, $wp_query ) {
	if ( is_admin() || wp_doing_ajax() || ! ( $wp_query instanceof WP_Query ) || ! hu_is_homepage_wow_request() ) {
		return $preempt;
	}

	$wp_query->is_404             = false;
	$wp_query->is_page            = true;
	$wp_query->is_singular        = true;
	$wp_query->is_home            = false;
	$wp_query->is_archive         = false;
	$wp_query->is_posts_page      = false;
	$wp_query->queried_object     = null;
	$wp_query->queried_object_id  = 0;
	$wp_query->query_vars['pagename'] = 'startseite-wow';
	unset( $wp_query->query['error'], $wp_query->query_vars['error'] );

	status_header( 200 );

	return true;
}
add_filter( 'pre_handle_404', 'hu_preempt_homepage_wow_404', 10, 2 );

/**
 * Use the visual homepage test template for the virtual route.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function hu_use_homepage_wow_template( $template ) {
	if ( ! hu_is_homepage_wow_request() ) {
		return $template;
	}

	$virtual_template = get_stylesheet_directory() . '/page-startseite-wow.php';

	if ( file_exists( $virtual_template ) ) {
		return $virtual_template;
	}

	return $template;
}
add_filter( 'template_include', 'hu_use_homepage_wow_template', 99 );

/**
 * Enqueue the scoped assets for the visual homepage test route.
 *
 * @return void
 */
function hu_enqueue_homepage_wow_assets() {
	if ( ! hu_is_homepage_wow_request() ) {
		return;
	}

	hu_enqueue_css( 'nexus-home-redesign-css', 'homepage-redesign.css', [ 'nexus-design-system' ] );
	hu_enqueue_css( 'nexus-homepage-wow-css', 'homepage-wow.css', [ 'nexus-home-redesign-css' ] );
	hu_enqueue_js( 'nexus-homepage-wow-js', 'homepage-wow.js', [ 'nexus-core-js' ] );
}
add_action( 'wp_enqueue_scripts', 'hu_enqueue_homepage_wow_assets', 30 );

/**
 * Remove block-editor CSS on the fully versioned test route.
 *
 * @return void
 */
function hu_dequeue_block_styles_on_homepage_wow() {
	if ( ! hu_is_homepage_wow_request() ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
}
add_action( 'wp_enqueue_scripts', 'hu_dequeue_block_styles_on_homepage_wow', 101 );

/**
 * Add body classes for route-level styling and debugging.
 *
 * @param array<int, string> $classes Existing body classes.
 * @return array<int, string>
 */
function hu_add_homepage_wow_body_class( $classes ) {
	if ( ! hu_is_homepage_wow_request() ) {
		return $classes;
	}

	$classes   = array_diff( $classes, [ 'error404' ] );
	$classes[] = 'page';
	$classes[] = 'page-startseite-wow';
	$classes[] = 'homepage-wow-test';

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'hu_add_homepage_wow_body_class', 20 );

/**
 * Output route-specific noindex metadata and avoid duplicate default meta.
 *
 * @return void
 */
function hu_setup_homepage_wow_meta() {
	if ( ! hu_is_homepage_wow_request() ) {
		return;
	}

	remove_action( 'wp_head', 'hu_seo_meta_tags', 1 );
	add_action( 'wp_head', 'hu_output_homepage_wow_meta', 1 );
}
add_action( 'wp', 'hu_setup_homepage_wow_meta', 1 );

/**
 * Print minimal route-specific meta tags for the noindex test page.
 *
 * @return void
 */
function hu_output_homepage_wow_meta() {
	$title       = 'Startseiten-Variante | Haşim Üner';
	$description = 'Noindex-Testseite fuer eine visuell staerkere Homepage-Variante des Anfrage-Systems fuer Solar- und Waermepumpen-Anbieter.';
	$canonical   = home_url( '/startseite-wow/' );

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
	echo '<meta name="robots" content="noindex,nofollow">' . "\n";
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $canonical ) );
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:locale" content="de_DE">' . "\n";
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	echo '<meta name="twitter:card" content="summary">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
}

/**
 * Send an HTTP noindex header for crawlers that respect X-Robots-Tag.
 *
 * @return void
 */
function hu_send_homepage_wow_robots_header() {
	if ( ! is_admin() && ! wp_doing_ajax() && hu_is_homepage_wow_request() ) {
		header( 'X-Robots-Tag: noindex, nofollow', true );
	}
}
add_action( 'send_headers', 'hu_send_homepage_wow_robots_header' );

/**
 * Override the document title for the test route.
 *
 * @param string $title Current document title.
 * @return string
 */
function hu_homepage_wow_document_title( $title ) {
	if ( hu_is_homepage_wow_request() ) {
		return 'Startseiten-Variante | Haşim Üner';
	}

	return $title;
}
add_filter( 'pre_get_document_title', 'hu_homepage_wow_document_title', 99 );
