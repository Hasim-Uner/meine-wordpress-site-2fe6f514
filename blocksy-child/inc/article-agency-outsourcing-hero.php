<?php
/**
 * One-time featured-image binding for the agency outsourcing flagship article.
 *
 * The artwork already lives in the WordPress media library. This module only
 * binds that existing attachment to the article so the normal single template,
 * Open Graph layer and BlogPosting schema all share the same canonical image.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attach the uploaded bridge artwork to the agency article once.
 *
 * @return void
 */
function hu_maybe_attach_agency_outsourcing_hero() : void {
	if ( wp_installing() || wp_doing_ajax() ) {
		return;
	}

	$version    = '2026-09-06-agency-hero-v1';
	$option_key = 'hu_agency_outsourcing_hero_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	$post_id = function_exists( 'hu_find_agency_outsourcing_article_id' )
		? hu_find_agency_outsourcing_article_id()
		: 0;

	if ( $post_id <= 0 ) {
		return;
	}

	$image_url = 'https://hasimuener.de/wp-content/uploads/2026/09/wordpress-projekte-auslagern-agentur-freelancer.png';
	$image_id  = attachment_url_to_postid( $image_url );

	// attachment_url_to_postid() can miss migrated/CDN-normalized uploads. Fall
	// back to the canonical upload filename before giving up.
	if ( $image_id <= 0 ) {
		$filename = wp_basename( (string) wp_parse_url( $image_url, PHP_URL_PATH ) );
		$matches  = get_posts(
			[
				'post_type'              => 'attachment',
				'post_status'            => 'inherit',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => [
					[
						'key'     => '_wp_attached_file',
						'value'   => $filename,
						'compare' => 'LIKE',
					],
				],
			]
		);

		$image_id = ! empty( $matches ) ? (int) $matches[0] : 0;
	}

	if ( $image_id <= 0 ) {
		return;
	}

	set_post_thumbnail( $post_id, $image_id );

	$existing_alt = trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
	if ( '' === $existing_alt ) {
		update_post_meta(
			$image_id,
			'_wp_attachment_image_alt',
			'Brücke zwischen Agentur-Design und WordPress-Umsetzung mit Leistungsumfang, Staging, Qualitätssicherung, Tracking, Deployment und Übergabe.'
		);
	}

	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_attach_agency_outsourcing_hero', 34 );
