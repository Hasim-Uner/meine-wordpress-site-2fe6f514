<?php
/**
 * Guarded one-time accuracy refresh for the WordPress TTFB flagship article.
 *
 * The live WordPress editor remains the content owner. A full body refresh is
 * allowed only while the post still matches the known legacy title and at
 * least two legacy accuracy markers. If an editor has materially changed the
 * article, this migration leaves it alone.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Refresh the legacy TTFB article from the reviewed repo source.
 *
 * @return void
 */
function hu_maybe_refresh_ttfb_article_content() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = '2026-09-05-1';
	$option_key = 'hu_article_content_hygiene_ttfb_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	if ( ! function_exists( 'hu_article_content_hygiene_find_post_id' ) ) {
		return;
	}

	$post_id = hu_article_content_hygiene_find_post_id( 'wordpress-ttfb-google-ads-ladezeit' );
	if ( $post_id <= 0 ) {
		return;
	}

	$legacy_title = 'WordPress TTFB unter 200 ms: Wie Server-Antwortzeit den Google-Ads-Qualitätsfaktor entscheidet';
	$new_title    = 'WordPress TTFB optimieren: Was Server-Antwortzeit für Google Ads wirklich bedeutet';
	$current_title   = (string) get_post_field( 'post_title', $post_id );
	$current_content = (string) get_post_field( 'post_content', $post_id );

	$legacy_markers = [
		'Google misst TTFB als Teil der Core Web Vitals.',
		'TTFB über 600 Millisekunden',
		'20 bis 40 Prozent niedrigere Cost per Lead',
		'hostpress.de/wordpress-hosting',
		'Das ist DSGVO-konform und gleichzeitig schneller als jeder US-Cloud-Setup.',
	];

	$remaining_markers = 0;
	foreach ( $legacy_markers as $marker ) {
		if ( false !== strpos( $current_content, $marker ) ) {
			$remaining_markers++;
		}
	}

	// An editor may already have completed the refresh manually.
	if ( $new_title === $current_title && 0 === $remaining_markers ) {
		update_option( $option_key, $version, false );
		return;
	}

	// Full-body refresh is intentionally conservative: exact old title plus a
	// strong legacy fingerprint. Any materially edited article is left alone.
	if ( $legacy_title !== $current_title || $remaining_markers < 2 ) {
		return;
	}

	if ( ! function_exists( 'hu_blog_pillar_extract_article_markdown' ) || ! function_exists( 'hu_blog_pillar_markdown_to_html' ) ) {
		return;
	}

	$source_path = get_stylesheet_directory() . '/assets/content/blog/wordpress-ttfb-google-ads-ladezeit.md';
	if ( ! is_readable( $source_path ) ) {
		return;
	}

	$markdown = file_get_contents( $source_path );
	if ( false === $markdown || '' === trim( $markdown ) ) {
		return;
	}

	$article_markdown = hu_blog_pillar_extract_article_markdown( $markdown );
	$new_content      = hu_blog_pillar_markdown_to_html( $article_markdown );
	if ( '' === trim( $new_content ) ) {
		return;
	}

	$new_excerpt = 'Eine langsame Server-Antwort verlängert alle nachfolgenden Ladephasen. Aber TTFB wirkt nicht über eine einfache Formel auf Qualitätsfaktor oder CPC. Was Google tatsächlich sagt — und welche WordPress-Hebel messbar helfen.';

	$result = wp_update_post(
		wp_slash(
			[
				'ID'           => $post_id,
				'post_title'   => $new_title,
				'post_excerpt' => $new_excerpt,
				'post_content' => $new_content,
			]
		),
		true
	);

	if ( is_wp_error( $result ) || ! $result ) {
		return;
	}

	update_post_meta( $post_id, 'seo_title', 'WordPress TTFB optimieren: Google Ads richtig einordnen' );
	update_post_meta( $post_id, 'seo_description', 'TTFB ist kein Core Web Vital und der Qualitätsfaktor kein Auktionssignal. So messen und optimieren Sie die WordPress-Server-Antwortzeit sauber.' );
	update_post_meta( $post_id, '_hu_article_content_hygiene_ttfb_version', $version );
	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_refresh_ttfb_article_content', 43 );
