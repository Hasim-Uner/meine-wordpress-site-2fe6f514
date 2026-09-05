<?php
/**
 * Agency outsourcing flagship article.
 *
 * Seeds one reviewed article into WordPress and leaves it editor-owned after
 * the seed. Also loads the article-specific editorial diagrams on its route.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return whether the current request is the agency outsourcing article.
 *
 * @return bool
 */
function hu_is_agency_outsourcing_article() : bool {
	if ( ! is_singular( 'post' ) ) {
		return false;
	}

	$post_id = get_queried_object_id();

	return $post_id > 0 && 'wordpress-projekte-auslagern' === (string) get_post_field( 'post_name', $post_id );
}

/**
 * Register White-Label as a glossary-style alias to the existing money page.
 *
 * This keeps the commercial query owner on /whitelabel-retainer/ while blog
 * readers still receive the same hover/focus explanation as registry terms.
 *
 * @param array<string, array<string, string>> $aliases Existing aliases.
 * @return array<string, array<string, string>>
 */
function hu_agency_outsourcing_glossary_aliases( $aliases ) : array {
	$aliases = is_array( $aliases ) ? $aliases : [];
	$url     = function_exists( 'nexus_get_whitelabel_page_url' )
		? nexus_get_whitelabel_page_url()
		: home_url( '/whitelabel-retainer/' );

	$aliases['White-Label'] = [
		'url'        => $url,
		'title'      => 'Glossar: White-Label',
		'tooltip'    => 'White-Label bedeutet, dass die technische Umsetzung im Hintergrund erfolgt, während Kundenbeziehung, Marke und Kommunikation bei der Agentur bleiben.',
		'linked_key' => 'glossary_alias:white-label',
	];

	return $aliases;
}
add_filter( 'nexus_contextual_glossary_aliases', 'hu_agency_outsourcing_glossary_aliases' );

/**
 * Load article-specific editorial diagrams.
 *
 * @return void
 */
function hu_enqueue_agency_outsourcing_article_assets() : void {
	if ( ! hu_is_agency_outsourcing_article() ) {
		return;
	}

	$path    = get_stylesheet_directory() . '/assets/css/article-agency-outsourcing.css';
	$url     = get_stylesheet_directory_uri() . '/assets/css/article-agency-outsourcing.css';
	$version = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'hu-agency-outsourcing-article',
		$url,
		[ 'nexus-single-editorial-css' ],
		$version
	);
}
add_action( 'wp_enqueue_scripts', 'hu_enqueue_agency_outsourcing_article_assets', 35 );

/**
 * Find an existing article by slug without assuming the pillar seeder exists.
 *
 * @return int
 */
function hu_find_agency_outsourcing_article_id() : int {
	if ( function_exists( 'hu_blog_pillar_find_post_id_by_slug' ) ) {
		return (int) hu_blog_pillar_find_post_id_by_slug( 'wordpress-projekte-auslagern' );
	}

	$posts = get_posts(
		[
			'name'                   => 'wordpress-projekte-auslagern',
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
 * Seed the reviewed agency article once.
 *
 * Existing editor-owned content at the same slug is never overwritten.
 *
 * @return void
 */
function hu_maybe_seed_agency_outsourcing_article() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = '2026-09-06-agency-outsourcing-v1';
	$option_key = 'hu_agency_outsourcing_article_version';

	if ( (string) get_option( $option_key, '' ) === $version ) {
		return;
	}

	$existing_id = hu_find_agency_outsourcing_article_id();

	if ( $existing_id > 0 ) {
		$is_managed = '1' === (string) get_post_meta( $existing_id, '_hu_agency_outsourcing_seeded', true );

		if ( ! $is_managed ) {
			update_option( $option_key, $version, false );
			return;
		}

		if ( $version === (string) get_post_meta( $existing_id, '_hu_agency_outsourcing_seed_version', true ) ) {
			update_option( $option_key, $version, false );
			return;
		}
	}

	$source_path = get_stylesheet_directory() . '/assets/content/blog/wordpress-projekte-auslagern.html';
	if ( ! is_readable( $source_path ) ) {
		return;
	}

	$content = file_get_contents( $source_path );
	if ( false === $content || '' === trim( $content ) || false === strpos( $content, 'data-agency-outsourcing="v1"' ) ) {
		return;
	}

	$category_id = function_exists( 'hu_blog_pillar_ensure_term_id' )
		? hu_blog_pillar_ensure_term_id( 'WordPress & Performance', 'wordpress-performance', 'category' )
		: 0;

	if ( $category_id <= 0 ) {
		return;
	}

	$author_id = function_exists( 'hu_blog_pillar_seed_author_id' ) ? hu_blog_pillar_seed_author_id() : 1;
	$post_data = [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'post_title'     => 'WordPress-Projekte auslagern: So arbeiten Agenturen mit Freelancern ohne Kontrollverlust',
		'post_name'      => 'wordpress-projekte-auslagern',
		'post_content'   => trim( $content ),
		'post_excerpt'   => 'Externe WordPress-Kapazität entlastet nur, wenn Übergaben funktionieren. Ein Leitfaden für Agenturen zu Scope, Figma-Handoff, Staging, QA, Tracking, Deployment und sauberer Rückgabe.',
		'post_author'    => $author_id,
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
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
			'WordPress Projekte auslagern',
			'WordPress für Agenturen',
			'White-Label',
			'WordPress Freelancer',
			'Staging',
			'QA',
			'Deployment',
		],
		'post_tag',
		false
	);

	update_post_meta( $post_id, 'seo_title', 'WordPress-Projekte auslagern: Leitfaden für Agenturen' );
	update_post_meta( $post_id, 'seo_description', 'WordPress-Projekte extern umsetzen lassen, ohne Kontrolle zu verlieren: Briefing, Scope, Staging, QA, Tracking, Deployment und Handover für Agenturen.' );
	update_post_meta( $post_id, '_hu_agency_outsourcing_seeded', '1' );
	update_post_meta( $post_id, '_hu_agency_outsourcing_seed_version', $version );
	update_option( $option_key, $version, false );
}
add_action( 'init', 'hu_maybe_seed_agency_outsourcing_article', 33 );
