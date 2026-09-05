<?php
/**
 * B2B inquiry-system flagship article.
 *
 * Keeps the existing indexed URL and refreshes the legacy editor content only
 * while it still matches the known pre-repositioning article. The WordPress
 * editor remains the long-term content owner after this one-time migration.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether the current request is the B2B inquiry-system article.
 *
 * @return bool
 */
function hu_is_b2b_inquiry_system_article() : bool {
	if ( ! is_singular( 'post' ) ) {
		return false;
	}

	$post_id = get_queried_object_id();

	return $post_id > 0 && 'wordpress-seo-keine-anfragen' === (string) get_post_field( 'post_name', $post_id );
}

/**
 * Load the article-specific editorial diagrams only on the flagship article.
 *
 * @return void
 */
function hu_enqueue_b2b_inquiry_system_article_assets() : void {
	if ( ! hu_is_b2b_inquiry_system_article() ) {
		return;
	}

	$path    = get_stylesheet_directory() . '/assets/css/article-b2b-inquiry-system.css';
	$url     = get_stylesheet_directory_uri() . '/assets/css/article-b2b-inquiry-system.css';
	$version = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'nexus-b2b-inquiry-system-css',
		$url,
		[ 'nexus-single-editorial-css' ],
		$version
	);

	// The reader canvas is dark. Keep the diagram stylesheet portable, then
	// bind its semantic colors to the active reader tokens at render time.
	wp_add_inline_style(
		'nexus-b2b-inquiry-system-css',
		'.single-post .b2b-inquiry-article{--b2b-ink:var(--nx-text);--b2b-muted:var(--nx-text-muted);--b2b-rule:rgba(255,255,255,.15);--b2b-soft:rgba(255,255,255,.045);--b2b-accent:var(--accent-hover,#d58a5d)}'
	);
}
add_action( 'wp_enqueue_scripts', 'hu_enqueue_b2b_inquiry_system_article_assets', 35 );

/**
 * Replace the legacy article body with the reviewed flagship source once.
 *
 * @return void
 */
function hu_maybe_refresh_b2b_inquiry_system_article() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = '2026-09-05-b2b-inquiry-v1';
	$option_key = 'hu_article_b2b_inquiry_system_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	if ( ! function_exists( 'hu_article_content_hygiene_find_post_id' ) ) {
		return;
	}

	$post_id = hu_article_content_hygiene_find_post_id( 'wordpress-seo-keine-anfragen' );
	if ( $post_id <= 0 ) {
		return;
	}

	$legacy_title    = 'Warum WordPress-Websites trotz SEO keine Anfragen liefern';
	$new_title       = 'Website bringt keine Anfragen? So entsteht ein messbares B2B-Anfragesystem';
	$current_title   = (string) get_post_field( 'post_title', $post_id );
	$current_content = (string) get_post_field( 'post_content', $post_id );
	$new_marker      = 'data-b2b-inquiry-system="v1"';

	// An editor may already have applied the reviewed version manually.
	if ( $new_title === $current_title && false !== strpos( $current_content, $new_marker ) ) {
		update_option( $option_key, $version, false );
		return;
	}

	$legacy_markers = [
		'Sichtbarkeit ist nicht der Maßstab. Nachfrage ist der Maßstab.',
		'Sie haben ein <strong>Übersetzungsproblem</strong>.',
		'SEO ohne Conversion-Logik produziert Leerlauf',
		'Ohne Tracking optimierst du an Symptomen',
		'Growth Audit',
	];

	$remaining_markers = 0;
	foreach ( $legacy_markers as $marker ) {
		if ( false !== strpos( $current_content, $marker ) ) {
			$remaining_markers++;
		}
	}

	// Never overwrite a materially edited article just because the slug matches.
	if ( $legacy_title !== $current_title || $remaining_markers < 2 ) {
		return;
	}

	$source_path = get_stylesheet_directory() . '/assets/content/blog/wordpress-seo-keine-anfragen.html';
	if ( ! is_readable( $source_path ) ) {
		return;
	}

	$new_content = file_get_contents( $source_path );
	if ( false === $new_content || '' === trim( $new_content ) || false === strpos( $new_content, $new_marker ) ) {
		return;
	}

	$new_excerpt = 'Traffic allein verkauft nichts. Dieser Leitfaden zeigt, an welchen Übergaben B2B-Websites Anfragen verlieren – und wie SEO, WordPress, Proof, Tracking und Vertrieb als messbarer Anfragepfad zusammenarbeiten.';

	$result = wp_update_post(
		wp_slash(
			[
				'ID'           => $post_id,
				'post_title'   => $new_title,
				'post_excerpt' => $new_excerpt,
				'post_content' => trim( $new_content ),
			]
		),
		true
	);

	if ( is_wp_error( $result ) || ! $result ) {
		return;
	}

	update_post_meta( $post_id, 'seo_title', 'Website bringt keine Anfragen? B2B-System prüfen' );
	update_post_meta( $post_id, 'seo_description', 'Website hat Besucher, aber zu wenig Anfragen? Prüfen Sie Suchintention, Proof, Formular, Tracking und Vertriebsübergabe als ein System.' );
	update_post_meta( $post_id, '_hu_article_b2b_inquiry_system_version', $version );
	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_refresh_b2b_inquiry_system_article', 45 );
