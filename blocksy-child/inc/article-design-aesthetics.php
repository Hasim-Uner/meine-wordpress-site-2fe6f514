<?php
/**
 * Guarded one-time refresh for the design / aesthetics flagship article.
 *
 * The WordPress editor remains the long-term content owner. The repo source is
 * applied once only while the live post still matches the known legacy copy.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether the current request is the design essay.
 *
 * @return bool
 */
function hu_is_design_aesthetics_article() : bool {
	if ( ! is_singular( 'post' ) ) {
		return false;
	}

	$post_id = get_queried_object_id();

	return $post_id > 0 && 'design-ist-mehr-als-aesthetik' === (string) get_post_field( 'post_name', $post_id );
}

/**
 * Load the article-specific editorial layer.
 *
 * @return void
 */
function hu_enqueue_design_aesthetics_article_assets() : void {
	if ( ! hu_is_design_aesthetics_article() ) {
		return;
	}

	$path    = get_stylesheet_directory() . '/assets/css/article-design-aesthetics.css';
	$url     = get_stylesheet_directory_uri() . '/assets/css/article-design-aesthetics.css';
	$version = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'hu-article-design-aesthetics',
		$url,
		[ 'nexus-single-editorial-css' ],
		$version
	);
}
add_action( 'wp_enqueue_scripts', 'hu_enqueue_design_aesthetics_article_assets', 35 );

/**
 * Apply the reviewed v2 body once while the legacy fingerprint is intact.
 *
 * @return void
 */
function hu_maybe_refresh_design_aesthetics_article() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = '2026-09-28-design-aesthetics-v2';
	$option_key = 'hu_article_design_aesthetics_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	if ( ! function_exists( 'hu_article_content_hygiene_find_post_id' ) ) {
		return;
	}

	$post_id = hu_article_content_hygiene_find_post_id( 'design-ist-mehr-als-aesthetik' );
	if ( $post_id <= 0 ) {
		return;
	}

	$current_title   = (string) get_post_field( 'post_title', $post_id );
	$current_content = (string) get_post_field( 'post_content', $post_id );
	$new_marker      = 'data-design-essay="v2"';
	$expected_title  = 'Design ist kein Geschmack. Es ist Architektur.';
	$new_excerpt     = 'Wie sich Ästhetik und Funktionalität im Design verbinden: von Designgeschichte und Semiotik bis UX, Conversion, Core Web Vitals und Dark Patterns.';

	if ( false !== strpos( $current_content, $new_marker ) ) {
		update_post_meta( $post_id, '_hu_article_design_aesthetics_version', $version );
		update_option( $option_key, $version, false );
		return;
	}

	$legacy_markers = [
		'Einleitung: Die unsichtbare Steuerung',
		'Design als Conversion-System – operativ und messbar',
		'Unser kostenloser Journey Audit analysiert',
		'Typische Verbesserung durch systematische Design-Optimierung: 20–50 %',
	];

	$remaining_markers = 0;
	foreach ( $legacy_markers as $marker ) {
		if ( false !== strpos( $current_content, $marker ) ) {
			$remaining_markers++;
		}
	}

	if ( $expected_title !== $current_title || $remaining_markers < 2 ) {
		return;
	}

	$source_path = get_stylesheet_directory() . '/assets/content/blog/design-ist-mehr-als-aesthetik-v2.html';
	if ( ! is_readable( $source_path ) ) {
		return;
	}

	$new_content = file_get_contents( $source_path );
	if ( false === $new_content || '' === trim( $new_content ) || false === strpos( $new_content, $new_marker ) ) {
		return;
	}

	$result = wp_update_post(
		wp_slash(
			[
				'ID'           => $post_id,
				'post_excerpt' => $new_excerpt,
				'post_content' => trim( $new_content ),
			]
		),
		true
	);

	if ( is_wp_error( $result ) || ! $result ) {
		return;
	}

	// Keep the established SEO title. Only sharpen the description around the
	// query Google is already testing for this URL.
	update_post_meta(
		$post_id,
		'seo_description',
		'Wie sich Ästhetik und Funktionalität im Design verbinden: Prinzipien aus Designgeschichte, UX, Semiotik, Conversion und Ethik – konkret für Websites.'
	);
	update_post_meta( $post_id, '_hu_article_design_aesthetics_version', $version );
	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_refresh_design_aesthetics_article', 44 );
