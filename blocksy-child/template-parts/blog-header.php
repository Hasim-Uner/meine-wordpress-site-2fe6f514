<?php
/**
 * Blog header compatibility router.
 *
 * The former full duplicate blog navigation has been retired. Single posts
 * keep the focused Article System reader header; generic archives fall back to
 * the canonical shared site header. Category archives render that shared
 * header directly in category.php.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_singular( 'post' ) ) {
	$slug = (string) get_post_field( 'post_name', get_queried_object_id() );

	// Non-pilot posts receive the reader header from the compatibility action in
	// inc/article-reader-toc.php at this exact template slot. The six historical
	// pilot posts predate that action and still need the explicit render here.
	$legacy_pilots = function_exists( 'hu_get_article_reader_legacy_pilot_slugs' )
		? hu_get_article_reader_legacy_pilot_slugs()
		: [
			'aroundhome-solar-einordnung',
			'checkfox-solar-waermepumpe-einordnung',
			'wattfox-solar-leads-einordnung',
			'wordpress-ttfb-google-ads-ladezeit',
			'server-side-tracking-gtm',
			'b2b-landingpage-optimieren',
		];

	if ( in_array( $slug, $legacy_pilots, true ) ) {
		get_template_part(
			'template-parts/article-reader-header',
			null,
			[
				'slug' => $slug,
			]
		);
	}

	return;
}

// Generic tag/date archives still enter through this compatibility template
// because inc/header.php intentionally excludes archive requests from its
// automatic renderer. They now receive the same header as the main website.
get_template_part( 'template-parts/site-header' );