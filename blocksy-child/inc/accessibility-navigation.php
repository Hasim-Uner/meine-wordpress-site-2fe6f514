<?php
/**
 * Global keyboard-navigation contract.
 *
 * Keeps the public skip link aligned with the actual main landmark without
 * renaming legacy template IDs. The historic `#primary` IDs remain untouched
 * because route CSS/JS may still depend on them.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
 * Styling intentionally matches the existing link; only target selection and
 * the explicit data hook change. The JS enhancement moves keyboard focus to
 * the landmark while native fragment navigation remains the fallback.
 *
 * @return void
 */
function hu_render_accessible_skip_link() {
	$target_id = hu_get_skip_link_target_id();

	printf(
		'<a href="#%1$s" class="skip-to-content" data-skip-link style="position:absolute;top:-100px;left:16px;background:#b46a3c;color:#fff8f3;padding:10px 16px;border:1px solid rgba(255,248,243,0.18);border-radius:999px;font-weight:800;font-size:13px;letter-spacing:0.01em;box-shadow:0 16px 34px rgba(180,106,60,0.28);z-index:99999;text-decoration:none;transition:top 0.2s ease;" onfocus="this.style.top=\'16px\'" onblur="this.style.top=\'-100px\'">%2$s</a>',
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
 * Add the keyboard-focus enhancement globally. It is deliberately tiny and
 * depends on nexus-core only for predictable asset ordering; it does not
 * depend on JavaScript for the underlying fragment fallback.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() ) {
		return;
	}

	if ( function_exists( 'hu_enqueue_js' ) ) {
		hu_enqueue_js( 'hu-skip-link-focus-js', 'skip-link-focus.js', [ 'nexus-core-js' ] );
		return;
	}

	$path = get_stylesheet_directory() . '/assets/js/skip-link-focus.js';
	$url  = get_stylesheet_directory_uri() . '/assets/js/skip-link-focus.js';
	$ver  = is_file( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_script( 'hu-skip-link-focus-js', $url, [ 'nexus-core-js' ], $ver, true );
}, 40 );
