<?php
/**
 * Cornerstone article: Website-Relaunch.
 *
 * Seeds the reviewed article once into WordPress and leaves it editor-owned
 * after the seed. Other posts are never touched. The article is the first one
 * built from the editorial building blocks (inc/editorial-bausteine.php).
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
function hu_website_relaunch_article_slug() : string {
	return 'website-relaunch';
}

/**
 * Return whether the current request is the Website-Relaunch article.
 *
 * @return bool
 */
function hu_is_website_relaunch_article() : bool {
	if ( ! is_singular( 'post' ) ) {
		return false;
	}

	$post_id = get_queried_object_id();

	return $post_id > 0 && hu_website_relaunch_article_slug() === (string) get_post_field( 'post_name', $post_id );
}

/**
 * Find the article by slug across public and editor statuses.
 *
 * @return int
 */
function hu_find_website_relaunch_article_id() : int {
	if ( function_exists( 'hu_blog_pillar_find_post_id_by_slug' ) ) {
		return (int) hu_blog_pillar_find_post_id_by_slug( hu_website_relaunch_article_slug() );
	}

	$posts = get_posts(
		[
			'name'                   => hu_website_relaunch_article_slug(),
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
 * Resolve the canonical "Conversion & Anfragearchitektur" dossier term.
 *
 * @return int Term ID or 0.
 */
function hu_website_relaunch_category_id() : int {
	if ( function_exists( 'hu_get_positioned_blog_dossier_taxonomy' ) && function_exists( 'hu_ensure_positioned_blog_dossier_term' ) ) {
		$canonical = hu_get_positioned_blog_dossier_taxonomy();

		if ( ! empty( $canonical['cro'] ) ) {
			return hu_ensure_positioned_blog_dossier_term( 'cro', $canonical['cro'], false );
		}
	}

	$term = get_term_by( 'slug', 'cro', 'category' );

	return $term instanceof WP_Term ? (int) $term->term_id : 0;
}

/**
 * Read the reviewed article body from the theme.
 *
 * Der Kopfkommentar der Datei richtet sich an die Redaktion im Repo und
 * gehoert nicht in post_content.
 *
 * @return string Empty string when the source is missing or incomplete.
 */
function hu_website_relaunch_article_source() : string {
	$source_path = get_stylesheet_directory() . '/assets/content/blog/website-relaunch.html';

	if ( ! is_readable( $source_path ) ) {
		return '';
	}

	$content = file_get_contents( $source_path );

	if ( false === $content ) {
		return '';
	}

	$content = trim( (string) preg_replace( '/^\s*<!--.*?-->\s*/s', '', $content ) );

	if ( '' === $content || false === strpos( $content, '[hu_abschluss' ) || false === strpos( $content, '[hu_quellen]' ) ) {
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
function hu_maybe_seed_website_relaunch_article() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = '2026-09-24-website-relaunch-v2';
	$option_key = 'hu_website_relaunch_article_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	$existing_id = hu_find_website_relaunch_article_id();

	if ( $existing_id > 0 ) {
		$is_managed = '1' === (string) get_post_meta( $existing_id, '_hu_website_relaunch_seeded', true );

		if ( ! $is_managed || $version === (string) get_post_meta( $existing_id, '_hu_website_relaunch_seed_version', true ) ) {
			update_option( $option_key, $version, false );
			return;
		}
	}

	$content     = hu_website_relaunch_article_source();
	$category_id = hu_website_relaunch_category_id();

	if ( '' === $content || $category_id <= 0 ) {
		return;
	}

	$post_data = [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'post_title'     => 'Website-Relaunch: Wann die neue Website wirklich besser ist',
		'post_name'      => hu_website_relaunch_article_slug(),
		'post_content'   => $content,
		'post_excerpt'   => 'Eine neue Website ist besser, wenn an jeder Stelle mehr der richtigen Besucher einen Schritt weiterkommen und Sie das sehen können. Dass sie moderner aussieht, ist dafür ein Mittel, kein Beweis. Dieser Artikel zeigt, was eine B2B-Website eigentlich leistet, wie Sie „besser“ vor dem Relaunch messbar machen und was auf dem Weg dorthin nicht verloren gehen darf.',
		'post_author'    => function_exists( 'hu_blog_pillar_seed_author_id' ) ? hu_blog_pillar_seed_author_id() : 1,
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
		// Vor save_post gesetzt, damit hu_update_post_faq_schema_cache() die
		// Frage-Ueberschriften schon beim ersten Speichern auswertet.
		// Die Unterstrich-Schluessel sind die SCF-Feldreferenzen, damit die
		// Werte im Editor an ihren Feldern stehen.
		'meta_input'     => [
			'enable_faq_schema'  => '1',
			'_enable_faq_schema' => 'field_enable_faq_schema',
			'seo_title'          => 'Website-Relaunch: Wann die neue Website wirklich besser ist',
			'_seo_title'         => 'field_seo_title',
			'seo_description'    => 'Besser heißt nicht moderner. Wie Sie vor dem Relaunch festlegen, was an jeder Stelle besser werden soll, und was dabei nicht verloren gehen darf.',
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
			'Website-Relaunch',
			'WordPress Relaunch',
			'Relaunch Checkliste',
			'Conversion-Tracking',
			'Weiterleitungen',
			'Lastenheft',
		],
		'post_tag',
		false
	);

	$image_id = function_exists( 'hu_blog_pillar_ensure_featured_image' )
		? (int) hu_blog_pillar_ensure_featured_image(
			$post_id,
			'assets/img/blog/website-relaunch-hero-v2.png',
			'Website-Relaunch: Wann die neue Website wirklich besser ist',
			'Titelbild: Wann die neue Website wirklich besser ist. Darunter zwei Bänder von der Suche bis zum Auftrag: An vier Übergängen kommt nachher jeweils ein Fünftel mehr weiter, am Ende rund doppelt so viele.'
		)
		: 0;

	// Ohne eigenes og_image nimmt seo-meta.php fuer Beitraege die Groesse
	// "large" (1024 px). Das Titelbild ist fuer 2400 × 1260 gebaut und soll in
	// voller Groesse geteilt werden; das SCF-Feld "Open Graph Bild" liefert
	// die URL der Originaldatei.
	if ( $image_id > 0 ) {
		update_post_meta( $post_id, 'og_image', $image_id );
		update_post_meta( $post_id, '_og_image', 'field_og_image' );
	}

	// Der Cache entsteht bei save_post. Nach Schlagworten und Titelbild noch
	// einmal ausdruecklich, damit das FAQ-Schema nicht vom Hook-Timing abhaengt.
	if ( function_exists( 'hu_update_post_faq_schema_cache' ) ) {
		$post = get_post( $post_id );

		if ( $post instanceof WP_Post ) {
			hu_update_post_faq_schema_cache( $post_id, $post );
		}
	}

	update_post_meta( $post_id, '_hu_website_relaunch_seeded', '1' );
	update_post_meta( $post_id, '_hu_website_relaunch_seed_version', $version );
	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_seed_website_relaunch_article', 33 );
