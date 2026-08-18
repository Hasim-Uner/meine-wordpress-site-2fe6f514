<?php
/**
 * Positioning-specific SEO copy overrides.
 *
 * `seo-meta.php` remains the canonical title/description engine. Its public
 * filters are used here so the repositioning can be reviewed independently of
 * the large route-level SEO registry and without changing existing query
 * ownership for specialist money pages.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global homepage title: fachliche Klammer, not a duplicate local Freelancer
 * or Solar money-page query.
 *
 * @return string
 */
function hu_positioned_homepage_seo_title() : string {
	return 'WordPress, Tracking & Conversion | Haşim Üner';
}
add_filter( 'hu_homepage_seo_title', 'hu_positioned_homepage_seo_title', 20 );

/**
 * Global homepage description covering the three commercial entry paths.
 *
 * @return string
 */
function hu_positioned_homepage_seo_description() : string {
	return 'WordPress-Entwicklung, technisches SEO, Tracking und Conversion aus Pattensen bei Hannover. Für direkte Projekte, Agenturen und Solar-/Wärmepumpen-Anbieter.';
}
add_filter( 'hu_homepage_seo_description', 'hu_positioned_homepage_seo_description', 20 );

/**
 * Blog index title: broaden the knowledge hub beyond the Energy vertical.
 *
 * @return string
 */
function hu_positioned_blog_seo_title() : string {
	return 'Blog: WordPress, Tracking, SEO & Conversion | Haşim Üner';
}
add_filter( 'hu_blog_archive_seo_title', 'hu_positioned_blog_seo_title', 20 );

/**
 * Blog index description: broad technical-marketing knowledge hub with the
 * Energy cluster retained as one specialization.
 *
 * @return string
 */
function hu_positioned_blog_seo_description() : string {
	return 'Analysen zu WordPress, technischem SEO, Tracking, Conversion und Performance. Dazu Praxiswissen zu Anfragesystemen für Solar und Wärmepumpe.';
}
add_filter( 'hu_blog_archive_seo_description', 'hu_positioned_blog_seo_description', 20 );
