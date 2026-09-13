<?php
/**
 * Blog header compatibility router.
 *
 * Single posts always render the same focused Article System reader header.
 * Archives that still enter through this compatibility template receive the
 * canonical shared site header.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_singular( 'post' ) ) {
	$slug = (string) get_post_field( 'post_name', get_queried_object_id() );

	get_template_part(
		'template-parts/article-reader-header',
		null,
		[
			'slug' => $slug,
		]
	);

	return;
}

// Tag/date archives still enter through this compatibility template because
// inc/header.php intentionally excludes archive requests from its automatic
// renderer. They receive the same header as the main website.
get_template_part( 'template-parts/site-header' );
