<?php
/**
 * Article System reader bootstrap and table of contents.
 *
 * Every published WordPress post uses the same reader shell. This file owns
 * request detection and the deterministic single-post stylesheet stack.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All published single posts use the shared Article System reader.
 *
 * @return bool
 */
function hu_is_article_reader_request() : bool {
	return is_singular( 'post' ) && get_queried_object_id() > 0;
}

/**
 * Backward-compatible TOC request helper.
 *
 * @return bool
 */
function hu_is_article_reader_toc_request() : bool {
	return hu_is_article_reader_request();
}

/**
 * Enqueue the common reader body and TOC for every single post.
 *
 * @return void
 */
function hu_enqueue_article_reader_toc_assets() : void {
	if ( ! hu_is_article_reader_toc_request() ) {
		return;
	}

	$fallback_version = wp_get_theme()->get( 'Version' );
	$body_path        = get_stylesheet_directory() . '/assets/css/article-reader-body.css';
	$body_url         = get_stylesheet_directory_uri() . '/assets/css/article-reader-body.css';
	$toc_path         = get_stylesheet_directory() . '/assets/css/article-reader-toc.css';
	$toc_url          = get_stylesheet_directory_uri() . '/assets/css/article-reader-toc.css';
	$script_path      = get_stylesheet_directory() . '/assets/js/article-reader-toc.js';
	$script_url       = get_stylesheet_directory_uri() . '/assets/js/article-reader-toc.js';
	$body_version     = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $body_path ) : $fallback_version;
	$toc_version      = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $toc_path ) : $fallback_version;
	$script_version   = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $script_path ) : $fallback_version;

	wp_enqueue_style(
		'nexus-article-reader-body-css',
		$body_url,
		[ 'nexus-single-editorial-css', 'nexus-system-css' ],
		$body_version
	);

	wp_enqueue_style(
		'nexus-article-reader-toc-css',
		$toc_url,
		[ 'nexus-article-reader-body-css' ],
		$toc_version
	);

	wp_enqueue_script(
		'nexus-article-reader-toc-js',
		$script_url,
		[ 'nexus-core-js', 'nexus-single-editorial-js' ],
		$script_version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hu_enqueue_article_reader_toc_assets', 30 );

/**
 * Article-specific modules may keep content and behaviour, but no longer own
 * the outer page shell. Load the final reader contract after route assets.
 *
 * @return void
 */
function hu_enforce_shared_article_reader_assets() : void {
	if ( ! hu_is_article_reader_request() ) {
		return;
	}

	// This route formerly replaced the complete page with a separate dark
	// flagship layout. Its content remains, its page-level skin does not.
	wp_dequeue_style( 'hu-agency-outsourcing-article' );
	wp_dequeue_script( 'hu-agency-outsourcing-article-ui' );

	$style_path       = get_stylesheet_directory() . '/assets/css/single-reader-unified.css';
	$style_url        = get_stylesheet_directory_uri() . '/assets/css/single-reader-unified.css';
	$fallback_version = wp_get_theme()->get( 'Version' );
	$style_version    = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $style_path ) : $fallback_version;

	wp_enqueue_style(
		'nexus-single-reader-unified-css',
		$style_url,
		[ 'nexus-article-reader-toc-css' ],
		$style_version
	);
}
add_action( 'wp_enqueue_scripts', 'hu_enforce_shared_article_reader_assets', 100 );

/**
 * Provider decision modules predate the shared single reader and still contain
 * their own historical hero H1. Their hero is hidden when embedded, but a
 * hidden H1 still creates two document H1 elements. Keep the first reader H1
 * and demote only later duplicates server-side until the provider partials are
 * fully componentised.
 *
 * @param string $html Complete front-end response.
 * @return string
 */
function hu_normalize_embedded_provider_headings( string $html ) : string {
	$h1_index = 0;

	$normalized = preg_replace_callback(
		'/<h1\b([^>]*)>(.*?)<\/h1>/is',
		static function ( array $match ) use ( &$h1_index ) : string {
			$h1_index++;

			if ( 1 === $h1_index ) {
				return $match[0];
			}

			$attributes = preg_replace( '/\s+id=("|\')nexus-article-title\1/i', '', $match[1] );
			$attributes = is_string( $attributes ) ? $attributes : '';

			return '<h2' . $attributes . '>' . $match[2] . '</h2>';
		},
		$html
	);

	return is_string( $normalized ) ? $normalized : $html;
}

/**
 * Start response normalization only on the two embedded provider decision
 * routes. Regular posts never pay for output buffering.
 *
 * @return void
 */
function hu_start_embedded_provider_heading_normalizer() : void {
	if ( ! is_single( [ 'checkfox-solar-waermepumpe-einordnung', 'aroundhome-solar-einordnung' ] ) ) {
		return;
	}

	ob_start( 'hu_normalize_embedded_provider_headings' );
}
add_action( 'template_redirect', 'hu_start_embedded_provider_heading_normalizer', 0 );
