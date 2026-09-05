<?php
/**
 * Article System reader bootstrap and table of contents.
 *
 * The TTFB reader is now the default presentation for every WordPress post.
 * Existing pilot posts still enter through template-parts/blog-header.php;
 * all other posts receive the same reader shell from wp_body_open().
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Historical pilot posts that already render article-reader-header.php from
 * template-parts/blog-header.php. Keep this temporary compatibility list until
 * that older gate is removed from the header template itself.
 *
 * @return array<int,string>
 */
function hu_get_article_reader_legacy_pilot_slugs() : array {
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
 * Render the reader header for posts that are not part of the old six-post
 * pilot. The legacy blog header is still called later by single.php, so hide
 * that duplicate surface immediately. Once the document is available, move
 * the reader next to the single-post main element so the exact same sibling
 * selectors used by the original TTFB pilot continue to apply.
 *
 * @return void
 */
function hu_render_default_article_reader_header() : void {
	if ( ! hu_is_article_reader_request() ) {
		return;
	}

	$post_id = get_queried_object_id();
	$slug    = (string) get_post_field( 'post_name', $post_id );

	if ( in_array( $slug, hu_get_article_reader_legacy_pilot_slugs(), true ) ) {
		return;
	}

	get_template_part(
		'template-parts/article-reader-header',
		null,
		[
			'slug' => $slug,
		]
	);

	// The old blog header may live inside a theme wrapper, so scope by body
	// rather than by sibling relationship to prevent a duplicate header flash.
	echo '<style id="nexus-default-reader-header-compat">body.single-post .nexus-blog-header{display:none!important}</style>';

	// article-reader-body.css intentionally scopes many rules with
	// ".nexus-article-reader-header ~ .nexus-single-container". Recreate that
	// proven pilot DOM relationship for migrated posts after parsing completes.
	echo '<script id="nexus-default-reader-dom-align">document.addEventListener("DOMContentLoaded",function(){var r=document.querySelector(".nexus-article-reader-header"),m=document.querySelector(".nexus-single-container");if(r&&m&&m.parentNode&&r.parentNode!==m.parentNode){m.parentNode.insertBefore(r,m);}else if(r&&m&&m.parentNode&&r.nextElementSibling!==m){m.parentNode.insertBefore(r,m);}var h=document.querySelector(".nexus-blog-header");if(h){h.remove();}});</script>';
}
add_action( 'wp_body_open', 'hu_render_default_article_reader_header', 25 );

/**
 * Enqueue the reader TOC for every single post. The JavaScript itself guards
 * on [data-article-system] and quietly does nothing when a post has no useful
 * heading structure.
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
