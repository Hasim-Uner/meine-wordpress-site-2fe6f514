<?php
/**
 * Fachartikel: Was WordPress-Wartung kostet und was es kostet, wenn sie fehlt.
 *
 * Seeds the reviewed article once into WordPress and leaves it editor-owned
 * after the seed. Other posts are never touched. Muster: inc/article-website-relaunch.php.
 * Der Artikel ist Zubringer für /wordpress-wartung/ und trägt den Rechner
 * [hu_wartung_rechner] aus inc/wartung-rechner.php.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stable slug of the article.
 *
 * @return string
 */
function hu_wartung_kosten_article_slug() : string {
	return 'wordpress-wartung-kosten';
}

/**
 * Find the article by slug across public and editor statuses.
 *
 * @return int
 */
function hu_find_wartung_kosten_article_id() : int {
	if ( function_exists( 'hu_blog_pillar_find_post_id_by_slug' ) ) {
		return (int) hu_blog_pillar_find_post_id_by_slug( hu_wartung_kosten_article_slug() );
	}

	$posts = get_posts(
		[
			'name'                   => hu_wartung_kosten_article_slug(),
			'post_type'              => 'post',
			'post_status'            => [ 'publish', 'draft', 'pending', 'future', 'private' ],
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]
	);

	return ! empty( $posts ) ? (int) $posts[0] : 0;
}

/**
 * Resolve the "WordPress & technisches SEO" dossier term.
 *
 * @return int Term ID or 0.
 */
function hu_wartung_kosten_category_id() : int {
	if ( function_exists( 'hu_get_positioned_blog_dossier_taxonomy' ) && function_exists( 'hu_ensure_positioned_blog_dossier_term' ) ) {
		$canonical = hu_get_positioned_blog_dossier_taxonomy();

		if ( ! empty( $canonical['wordpress-performance'] ) ) {
			return hu_ensure_positioned_blog_dossier_term( 'wordpress-performance', $canonical['wordpress-performance'], false );
		}
	}

	$term = get_term_by( 'slug', 'wordpress-performance', 'category' );

	return $term instanceof WP_Term ? (int) $term->term_id : 0;
}

/**
 * Read the reviewed article body from the theme.
 *
 * @return string Empty string when the source is missing or incomplete.
 */
function hu_wartung_kosten_article_source() : string {
	$source_path = get_stylesheet_directory() . '/assets/content/blog/wordpress-wartung-kosten.html';

	if ( ! is_readable( $source_path ) ) {
		return '';
	}

	$content = file_get_contents( $source_path );

	if ( false === $content ) {
		return '';
	}

	$content = trim( (string) preg_replace( '/^\s*<!--.*?-->\s*/s', '', $content ) );

	if ( '' === $content || false === strpos( $content, '[hu_wartung_rechner]' ) || false === strpos( $content, '[hu_abschluss' ) || false === strpos( $content, '[hu_quellen]' ) ) {
		return '';
	}

	return $content;
}

/**
 * Seed the reviewed article once per managed content version.
 *
 * Existing editor-owned content at the same slug is never overwritten.
 *
 * @return void
 */
function hu_maybe_seed_wartung_kosten_article() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = '2026-10-26-wartung-kosten-v1';
	$option_key = 'hu_wartung_kosten_article_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	$existing_id = hu_find_wartung_kosten_article_id();

	if ( $existing_id > 0 ) {
		$is_managed = '1' === (string) get_post_meta( $existing_id, '_hu_wartung_kosten_seeded', true );

		if ( ! $is_managed || $version === (string) get_post_meta( $existing_id, '_hu_wartung_kosten_seed_version', true ) ) {
			update_option( $option_key, $version, false );
			return;
		}
	}

	$content     = hu_wartung_kosten_article_source();
	$category_id = hu_wartung_kosten_category_id();

	if ( '' === $content || $category_id <= 0 ) {
		return;
	}

	$title = 'WordPress-Wartung: Was sie kostet und was es kostet, wenn sie fehlt';

	$post_data = [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'post_title'     => $title,
		'post_name'      => hu_wartung_kosten_article_slug(),
		'post_content'   => $content,
		'post_excerpt'   => 'Einstiegspakete für WordPress-Wartung sind günstig. Teuer wird, was sie nicht prüfen: ein Kontaktformular, das „Danke“ zeigt und nichts zustellt. Dieser Artikel zeigt, was den Preis bestimmt, was in einen Wartungsvertrag gehört und was ein stiller Ausfall Ihr Unternehmen kostet, mit Rechner für Ihre Zahlen.',
		'post_author'    => function_exists( 'hu_blog_pillar_seed_author_id' ) ? hu_blog_pillar_seed_author_id() : 1,
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
		// Vor save_post gesetzt, damit hu_update_post_faq_schema_cache() die
		// Frage-Ueberschriften schon beim ersten Speichern auswertet.
		'meta_input'     => [
			'enable_faq_schema'  => '1',
			'_enable_faq_schema' => 'field_enable_faq_schema',
			'seo_title'          => 'WordPress-Wartung: Kosten 2026 und was ohne sie kostet',
			'_seo_title'         => 'field_seo_title',
			'seo_description'    => 'Was WordPress-Wartung 2026 kostet, was im Preis stecken muss und was ein unbemerkter Formularausfall kostet. Mit Rechner für Ihre Zahlen.',
			'_seo_description'   => 'field_seo_description',
		],
	];

	if ( $existing_id > 0 ) {
		$post_data['ID'] = $existing_id;
		$result          = wp_update_post( wp_slash( $post_data ), true );
	} else {
		$result = wp_insert_post( wp_slash( $post_data ), true );
	}

	if ( is_wp_error( $result ) || ! $result ) {
		return;
	}

	$post_id = (int) $result;

	wp_set_post_terms( $post_id, [ $category_id ], 'category', false );
	wp_set_post_terms(
		$post_id,
		[
			'WordPress-Wartung',
			'Wartungsvertrag',
			'Kontaktformular',
			'Conversion-Tracking',
		],
		'post_tag',
		false
	);

	$image_id = function_exists( 'hu_blog_pillar_ensure_featured_image' )
		? (int) hu_blog_pillar_ensure_featured_image(
			$post_id,
			'assets/img/blog/wordpress-wartung-kosten-hero.png',
			$title,
			'Titelbild: ein Monatstest mit vier grünen Prüfpunkten für Updates, Backup und Erreichbarkeit, darunter zwei rote: Die Anfrage kommt nicht im Postfach an und wird nicht gezählt.'
		)
		: 0;

	if ( $image_id > 0 ) {
		update_post_meta( $post_id, 'og_image', $image_id );
		update_post_meta( $post_id, '_og_image', 'field_og_image' );
	}

	if ( function_exists( 'hu_update_post_faq_schema_cache' ) ) {
		$post = get_post( $post_id );

		if ( $post instanceof WP_Post ) {
			hu_update_post_faq_schema_cache( $post_id, $post );
		}
	}

	update_post_meta( $post_id, '_hu_wartung_kosten_seeded', '1' );
	update_post_meta( $post_id, '_hu_wartung_kosten_seed_version', $version );
	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_seed_wartung_kosten_article', 33 );
