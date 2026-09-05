<?php
/**
 * Article System reader table of contents.
 *
 * Keeps the existing NexusCore TOC generator as the content source and adds a
 * lightweight reader UI only on the current Article System pilot posts.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reader posts that use the shared Article System shell.
 *
 * Keep this list aligned with template-parts/blog-header.php. The asset itself
 * still guards on [data-article-system], so an accidental mismatch fails quiet.
 *
 * @return array<int,string>
 */
function hu_get_article_reader_toc_slugs() : array {
	return [
		'aroundhome-solar-einordnung',
		'checkfox-solar-waermepumpe-einordnung',
		'wattfox-solar-leads-einordnung',
		'wordpress-ttfb-google-ads-ladezeit',
		'server-side-tracking-gtm',
		'b2b-landingpage-optimieren',
	];
}

/**
 * Whether the current request can render the shared reader TOC.
 *
 * @return bool
 */
function hu_is_article_reader_toc_request() : bool {
	if ( ! is_singular( 'post' ) ) {
		return false;
	}

	$post_id = get_queried_object_id();
	if ( $post_id <= 0 ) {
		return false;
	}

	$slug = (string) get_post_field( 'post_name', $post_id );
	return in_array( $slug, hu_get_article_reader_toc_slugs(), true );
}

/**
 * Enqueue the reader TOC after the regular blog assets are registered.
 *
 * @return void
 */
function hu_enqueue_article_reader_toc_assets() : void {
	if ( ! hu_is_article_reader_toc_request() ) {
		return;
	}

	$style_path = get_stylesheet_directory() . '/assets/css/article-reader-toc.css';
	$script_path = get_stylesheet_directory() . '/assets/js/article-reader-toc.js';
	$style_url = get_stylesheet_directory_uri() . '/assets/css/article-reader-toc.css';
	$script_url = get_stylesheet_directory_uri() . '/assets/js/article-reader-toc.js';
	$fallback_version = wp_get_theme()->get( 'Version' );
	$style_version = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $style_path ) : $fallback_version;
	$script_version = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $script_path ) : $fallback_version;

	wp_enqueue_style(
		'nexus-article-reader-toc-css',
		$style_url,
		[ 'nexus-blog-header-css', 'nexus-single-css' ],
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
