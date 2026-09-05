<?php
/**
 * Article System reader bootstrap and table of contents.
 *
 * The TTFB reader is now the default presentation for every WordPress post.
 * Existing pilot posts still enter through template-parts/blog-header.php;
 * all other posts receive the same reader shell at that exact template slot.
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
 * Render the reader header immediately before the legacy blog-header template
 * on posts that were not part of the original six-post pilot.
 *
 * WordPress fires this dynamic action exactly where single.php calls
 * get_template_part( 'template-parts/blog-header' ). This gives every migrated
 * post the same server-rendered sibling structure as the TTFB pilot, so the
 * existing Article System CSS works without JavaScript relocation.
 *
 * The legacy blog header still loads immediately afterwards for compatibility,
 * but is hidden on these migrated posts to avoid a duplicate navigation layer.
 *
 * @return void
 */
function hu_render_default_article_reader_at_blog_header_slot() : void {
	static $rendered = false;

	if ( $rendered || ! hu_is_article_reader_request() ) {
		return;
	}

	$post_id = get_queried_object_id();
	$slug    = (string) get_post_field( 'post_name', $post_id );

	if ( in_array( $slug, hu_get_article_reader_legacy_pilot_slugs(), true ) ) {
		return;
	}

	$rendered = true;

	get_template_part(
		'template-parts/article-reader-header',
		null,
		[
			'slug' => $slug,
		]
	);

	// The original blog header is included next by WordPress. Keep it out of the
	// visual and accessibility tree while the old compatibility branch exists.
	echo '<style id="nexus-default-reader-header-compat">body.single-post .nexus-blog-header{display:none!important}</style>';
}
add_action( 'get_template_part_template-parts/blog-header', 'hu_render_default_article_reader_at_blog_header_slot', 5, 3 );

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
