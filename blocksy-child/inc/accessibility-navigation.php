<?php
/**
 * Global keyboard-navigation contract.
 *
 * Keeps skip links, focus treatment and the public header aligned with the
 * actual main landmark without renaming legacy template IDs.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preload the exact homepage font faces that Lighthouse identified as the
 * remaining desktop CLS source.
 *
 * The global preload contract already covers Satoshi and Figtree 600. The hero
 * also paints body copy in Figtree 400 and compact labels/buttons in IBM Plex
 * Mono 500/600. Without an early hint those three faces arrived after first
 * paint and reflowed .blatt.kopfteil by roughly 0.22 CLS on desktop.
 *
 * Scope this strictly to the front page so secondary routes do not pay for
 * fonts they may never render above the fold.
 *
 * @return void
 */
function hu_preload_homepage_stability_fonts() {
	if ( ! is_front_page() ) {
		return;
	}

	$font_dir = get_stylesheet_directory() . '/fonts/';
	$font_uri = get_stylesheet_directory_uri() . '/fonts/';
	$fonts    = [
		'figtree-400.woff2',
		'IBMPlexMono-500-latin.woff2',
		'IBMPlexMono-600-latin.woff2',
	];

	foreach ( $fonts as $font ) {
		if ( ! is_file( $font_dir . $font ) ) {
			continue;
		}

		printf(
			'<link rel="preload" href="%1$s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( $font_uri . $font )
		);
	}
}
add_action( 'wp_head', 'hu_preload_homepage_stability_fonts', 2 );

/**
 * Resolve the real main-landmark ID for the current template.
 *
 * Most routes use `#main`. A small family of legacy intercept templates owns
 * its main landmark as `#primary`; changing those IDs would create avoidable
 * selector and fragment regressions, so the skip link adapts instead.
 *
 * @return string
 */
function hu_get_skip_link_target_id() {
	$primary_templates = [
		'page-lead-funnel-solar.php',
		'page-qualifizierte-pv-anfragen.php',
		'page-cost-per-lead-photovoltaik.php',
		'page-kunden-gewinnen-solarteure.php',
		'page-waermepumpen-leads.php',
		'page-solar-leads-kosten-studie.php',
		'page-b2b-solar-leads.php',
		'page-eigene-leadgenerierung-vs-portale.php',
		'page-solar-leads-kaufen-alternative.php',
		'page-server-side-tracking-b2b.php',
	];

	return is_page_template( $primary_templates ) ? 'primary' : 'main';
}

/**
 * Render the canonical global skip link.
 *
 * Presentation lives in accessibility-navigation.css. Without CSS or JS this
 * remains a normal native fragment link, so bypass navigation never depends on
 * enhancement code.
 *
 * @return void
 */
function hu_render_accessible_skip_link() {
	$target_id = hu_get_skip_link_target_id();

	printf(
		'<a href="#%1$s" class="skip-to-content" data-skip-link>%2$s</a>',
		esc_attr( $target_id ),
		esc_html__( 'Zum Hauptinhalt springen', 'blocksy-child' )
	);
}

/**
 * Replace the legacy hard-coded `#main` skip link after theme functions have
 * finished registering their hooks but before `wp_body_open` is rendered.
 *
 * @return void
 */
function hu_register_accessible_skip_link() {
	remove_action( 'wp_body_open', 'hasim_skip_to_content' );
	add_action( 'wp_body_open', 'hu_render_accessible_skip_link', 0 );
}
add_action( 'wp', 'hu_register_accessible_skip_link', 1 );

/**
 * Load the small global accessibility layer after the shared system styles.
 * It owns focus visibility and practical pointer targets for the repo-owned
 * header, while the JS keeps keyboard focus aligned with skip-link navigation.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() ) {
		return;
	}

	$css_path = get_stylesheet_directory() . '/assets/css/accessibility-navigation.css';
	$css_url  = get_stylesheet_directory_uri() . '/assets/css/accessibility-navigation.css';
	$css_ver  = is_file( $css_path ) ? (string) filemtime( $css_path ) : wp_get_theme()->get( 'Version' );

	if ( function_exists( 'hu_enqueue_css' ) ) {
		hu_enqueue_css( 'hu-accessibility-navigation-css', 'accessibility-navigation.css', [ 'nexus-system-css' ] );
	} elseif ( is_file( $css_path ) ) {
		wp_enqueue_style( 'hu-accessibility-navigation-css', $css_url, [], $css_ver );
	}

	if ( function_exists( 'hu_enqueue_js' ) ) {
		hu_enqueue_js( 'hu-skip-link-focus-js', 'skip-link-focus.js', [ 'nexus-core-js' ] );
		return;
	}

	$js_path = get_stylesheet_directory() . '/assets/js/skip-link-focus.js';
	$js_url  = get_stylesheet_directory_uri() . '/assets/js/skip-link-focus.js';
	$js_ver  = is_file( $js_path ) ? (string) filemtime( $js_path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_script( 'hu-skip-link-focus-js', $js_url, [ 'nexus-core-js' ], $js_ver, true );
}, 40 );
