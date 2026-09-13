<?php
/**
 * Article System reader bootstrap and table of contents.
 *
 * Every published WordPress post uses the same reader shell. The reader header
 * is rendered directly by template-parts/blog-header.php; this file owns
 * request detection, TOC assets and the visual single-post contract.
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
 * Enqueue one shared TOC implementation for every single post.
 *
 * The JavaScript enhances the server-rendered #toc-list and quietly does
 * nothing when a post has no useful heading structure.
 *
 * @return void
 */
function hu_enqueue_article_reader_toc_assets() : void {
	if ( ! hu_is_article_reader_toc_request() ) {
		return;
	}

	$style_path       = get_stylesheet_directory() . '/assets/css/article-reader-toc.css';
	$script_path      = get_stylesheet_directory() . '/assets/js/article-reader-toc.js';
	$style_url        = get_stylesheet_directory_uri() . '/assets/css/article-reader-toc.css';
	$script_url       = get_stylesheet_directory_uri() . '/assets/js/article-reader-toc.js';
	$fallback_version = wp_get_theme()->get( 'Version' );
	$style_version    = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $style_path ) : $fallback_version;
	$script_version   = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $script_path ) : $fallback_version;

	wp_enqueue_style(
		'nexus-article-reader-toc-css',
		$style_url,
		[ 'nexus-single-editorial-css', 'nexus-system-css' ],
		$style_version
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
 * The single template owns presentation. Article-specific modules may keep
 * their content and routing logic, but must not repaint the entire page.
 *
 * @return void
 */
function hu_enforce_shared_article_reader_assets() : void {
	if ( ! hu_is_article_reader_request() ) {
		return;
	}

	// /wordpress-projekte-auslagern/ previously replaced the complete reader
	// with a separate dark flagship layout. Keep its content/CTA logic but drop
	// that route-specific visual and behavioural shell.
	wp_dequeue_style( 'hu-agency-outsourcing-article' );
	wp_dequeue_script( 'hu-agency-outsourcing-article-ui' );

	$style_path       = get_stylesheet_directory() . '/assets/css/single-reader-unified.css';
	$style_url        = get_stylesheet_directory_uri() . '/assets/css/single-reader-unified.css';
	$fallback_version = wp_get_theme()->get( 'Version' );
	$style_version    = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $style_path ) : $fallback_version;

	wp_enqueue_style(
		'nexus-single-reader-unified-css',
		$style_url,
		[ 'nexus-article-reader-toc-css', 'nexus-single-editorial-css', 'nexus-system-css' ],
		$style_version
	);
}
add_action( 'wp_enqueue_scripts', 'hu_enforce_shared_article_reader_assets', 100 );
