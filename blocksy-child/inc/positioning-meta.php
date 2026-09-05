<?php
/**
 * Positioning-specific SEO copy and blog taxonomy normalization.
 *
 * `seo-meta.php` remains the canonical title/description engine. Its public
 * filters are used here so the repositioning can be reviewed independently of
 * the large route-level SEO registry and without changing existing query
 * ownership for specialist money pages.
 *
 * The blog taxonomy migration belongs to the same repositioning release: the
 * public Werkstatt exposes four dossiers, so WordPress should not keep a second
 * competing category model in the editor. Existing posts stay editor-owned;
 * only their category relations are consolidated.
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
 * The hero eyebrow shortens the visible label to "SEO" so the fachliche Klammer
 * stays on one line. The full term therefore carries its search weight here and
 * in the description below; no money page owns "technisches seo" in
 * docs/seo/query-ownership.csv, so this does not cannibalize a specialist route.
 *
 * @return string
 */
function hu_positioned_homepage_seo_title() : string {
	return 'WordPress, technisches SEO & Tracking | Haşim Üner';
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

/**
 * Canonical category model behind the four public Werkstatt dossiers.
 *
 * Stable, already-used slugs are preferred so the migration changes as few
 * public URLs as possible. Names and descriptions are the editorial contract.
 *
 * @return array<string, array{name: string, description: string}>
 */
function hu_get_positioned_blog_dossier_taxonomy() : array {
	return [
		'leadgenerierung' => [
			'name'        => 'Eigene Anfragen & Leadökonomie',
			'description' => 'Portale, Leadkosten, CPO, Vorqualifizierung und eigene Nachfrage-Infrastruktur.',
		],
		'wordpress-performance' => [
			'name'        => 'WordPress & Performance',
			'description' => 'WordPress-Architektur, technisches SEO, Performance, Core Web Vitals und Relaunch.',
		],
		'tracking' => [
			'name'        => 'Tracking & Messbarkeit',
			'description' => 'Server-Side Tracking, Attribution, Consent, Analytics und belastbare Messketten.',
		],
		'cro' => [
			'name'        => 'Conversion & Anfragearchitektur',
			'description' => 'Conversion-Optimierung, Landingpages, Formulare, CRM-Übergaben und Anfragearchitektur.',
		],
	];
}

/**
 * Map retired blog categories onto the four canonical dossiers.
 *
 * `sichtbarkeit-daten-conversion` was itself an umbrella and therefore maps to
 * the three technical dossiers instead of forcing every affected post into one
 * arbitrary bucket.
 *
 * @return array<string, array<int, string>>
 */
function hu_get_positioned_blog_legacy_taxonomy_map() : array {
	return [
		'markteinordnung'                    => [ 'leadgenerierung' ],
		'solar-waermepumpen-anfrage-systeme' => [ 'leadgenerierung' ],
		'owned-leads'                        => [ 'leadgenerierung' ],
		'wordpress-growth-agentur'           => [ 'wordpress-performance' ],
		'sichtbarkeit-daten-conversion'      => [ 'wordpress-performance', 'tracking', 'cro' ],
		'seo'                                => [ 'wordpress-performance' ],
		'seo-sichtbarkeit'                   => [ 'wordpress-performance' ],
		'wordpress'                          => [ 'wordpress-performance' ],
		'analytics'                          => [ 'tracking' ],
		'tracking-analytics'                 => [ 'tracking' ],
		'strategie'                          => [ 'cro' ],
		'performance-marketing'              => [ 'cro' ],
		'conversion'                         => [ 'cro' ],
		'paid-media'                         => [ 'cro' ],
	];
}

/**
 * Return a signature that reruns the consolidation when one of the repo-owned
 * blog seeders changes its version later.
 *
 * This keeps future seed releases from silently recreating retired categories.
 *
 * @return string
 */
function hu_get_positioned_blog_taxonomy_signature() : string {
	$provider_version = function_exists( 'hu_get_lead_provider_posts_seed_version' )
		? (string) hu_get_lead_provider_posts_seed_version()
		: 'provider-none';
	$pillar_version   = function_exists( 'hu_get_blog_pillar_posts_seed_version' )
		? (string) hu_get_blog_pillar_posts_seed_version()
		: 'pillar-none';

	return implode( '|', [ '2026-09-05-1', $provider_version, $pillar_version ] );
}

/**
 * Ensure one canonical dossier term exists and carries its canonical name.
 *
 * Descriptions are refreshed only for a new taxonomy schema release, not every
 * time a future post seeder changes. That keeps later editor-owned description
 * changes intact.
 *
 * @param string               $slug         Category slug.
 * @param array<string,string> $data         Canonical term data.
 * @param bool                 $refresh_copy Refresh description this release.
 * @return int Term ID or 0 on failure.
 */
function hu_ensure_positioned_blog_dossier_term( $slug, $data, $refresh_copy = false ) : int {
	$slug = sanitize_title( (string) $slug );
	$name = sanitize_text_field( (string) ( $data['name'] ?? '' ) );
	$desc = sanitize_text_field( (string) ( $data['description'] ?? '' ) );

	if ( '' === $slug || '' === $name ) {
		return 0;
	}

	$term = get_term_by( 'slug', $slug, 'category' );

	if ( $term instanceof WP_Term ) {
		$updates = [];

		if ( $name !== (string) $term->name ) {
			$updates['name'] = $name;
		}

		if ( $refresh_copy || '' === trim( (string) $term->description ) ) {
			$updates['description'] = $desc;
		}

		if ( ! empty( $updates ) ) {
			$updated = wp_update_term( (int) $term->term_id, 'category', $updates );
			if ( is_wp_error( $updated ) ) {
				return 0;
			}
		}

		return (int) $term->term_id;
	}

	$created = wp_insert_term(
		$name,
		'category',
		[
			'slug'        => $slug,
			'description' => $desc,
		]
	);

	if ( is_wp_error( $created ) || empty( $created['term_id'] ) ) {
		return 0;
	}

	return (int) $created['term_id'];
}

/**
 * Consolidate legacy WordPress categories into the four Werkstatt dossiers.
 *
 * Relations are copied before an old term is deleted. Unknown categories are
 * deliberately left alone: this migration only owns slugs that are documented
 * in the repo, never arbitrary editor taxonomy.
 *
 * @return void
 */
function hu_maybe_migrate_positioned_blog_taxonomy() : void {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$signature  = hu_get_positioned_blog_taxonomy_signature();
	$option_key = 'hu_positioned_blog_taxonomy_signature';

	if ( (string) get_option( $option_key, '' ) === $signature ) {
		return;
	}

	$schema_version = '2026-09-05-1';
	$refresh_copy   = (string) get_option( 'hu_positioned_blog_taxonomy_schema', '' ) !== $schema_version;
	$canonical      = hu_get_positioned_blog_dossier_taxonomy();
	$canonical_ids  = [];
	$all_done       = true;

	foreach ( $canonical as $slug => $data ) {
		$term_id = hu_ensure_positioned_blog_dossier_term( $slug, $data, $refresh_copy );
		if ( $term_id <= 0 ) {
			$all_done = false;
			continue;
		}
		$canonical_ids[ $slug ] = $term_id;
	}

	if ( count( $canonical_ids ) !== count( $canonical ) ) {
		return;
	}

	$default_category = (int) get_option( 'default_category' );

	foreach ( hu_get_positioned_blog_legacy_taxonomy_map() as $legacy_slug => $target_slugs ) {
		$legacy = get_term_by( 'slug', $legacy_slug, 'category' );
		if ( ! ( $legacy instanceof WP_Term ) ) {
			continue;
		}

		$target_ids = [];
		foreach ( $target_slugs as $target_slug ) {
			if ( ! empty( $canonical_ids[ $target_slug ] ) ) {
				$target_ids[] = (int) $canonical_ids[ $target_slug ];
			}
		}
		$target_ids = array_values( array_unique( array_filter( $target_ids ) ) );

		if ( empty( $target_ids ) ) {
			$all_done = false;
			continue;
		}

		$object_ids = get_objects_in_term( (int) $legacy->term_id, 'category' );
		if ( is_wp_error( $object_ids ) ) {
			$all_done = false;
			continue;
		}

		$relation_failed = false;
		foreach ( array_map( 'absint', (array) $object_ids ) as $object_id ) {
			if ( $object_id <= 0 ) {
				continue;
			}

			$set = wp_set_post_terms( $object_id, $target_ids, 'category', true );
			if ( is_wp_error( $set ) ) {
				$relation_failed = true;
				$all_done        = false;
			}
		}

		if ( $relation_failed ) {
			continue;
		}

		if ( $default_category === (int) $legacy->term_id ) {
			update_option( 'default_category', (int) $target_ids[0] );
			$default_category = (int) $target_ids[0];
		}

		$deleted = wp_delete_term( (int) $legacy->term_id, 'category' );
		if ( is_wp_error( $deleted ) || false === $deleted ) {
			$all_done = false;
		}
	}

	if ( $all_done ) {
		update_option( 'hu_positioned_blog_taxonomy_schema', $schema_version, false );
		update_option( $option_key, $signature, false );
	}
}
add_action( 'init', 'hu_maybe_migrate_positioned_blog_taxonomy', 40 );

/**
 * Preserve public equity from retired category archive URLs.
 *
 * `owned-leads` is intentionally not listed here. The evidence-based redirect
 * in helpers.php keeps sending that historical archive to the portal-vs-own
 * system comparison page instead of a generic category hub.
 *
 * @return array<string,string>
 */
function hu_get_positioned_blog_category_redirect_map() : array {
	$blog_url       = function_exists( 'nexus_get_blog_posts_url' ) ? nexus_get_blog_posts_url() : home_url( '/blog/' );
	$lead_url       = function_exists( 'nexus_get_category_url' ) ? nexus_get_category_url( 'leadgenerierung', home_url( '/category/leadgenerierung/' ) ) : home_url( '/category/leadgenerierung/' );
	$wordpress_url  = function_exists( 'nexus_get_category_url' ) ? nexus_get_category_url( 'wordpress-performance', home_url( '/category/wordpress-performance/' ) ) : home_url( '/category/wordpress-performance/' );
	$tracking_url   = function_exists( 'nexus_get_category_url' ) ? nexus_get_category_url( 'tracking', home_url( '/category/tracking/' ) ) : home_url( '/category/tracking/' );
	$conversion_url = function_exists( 'nexus_get_category_url' ) ? nexus_get_category_url( 'cro', home_url( '/category/cro/' ) ) : home_url( '/category/cro/' );

	return [
		'/category/markteinordnung/'                    => $lead_url,
		'/category/solar-waermepumpen-anfrage-systeme/' => $lead_url,
		'/category/wordpress-growth-agentur/'           => $wordpress_url,
		'/category/seo/'                                => $wordpress_url,
		'/category/seo-sichtbarkeit/'                   => $wordpress_url,
		'/category/wordpress/'                          => $wordpress_url,
		'/category/analytics/'                          => $tracking_url,
		'/category/tracking-analytics/'                 => $tracking_url,
		'/category/sichtbarkeit-daten-conversion/'      => $blog_url,
		'/category/strategie/'                          => $conversion_url,
		'/category/performance-marketing/'              => $conversion_url,
		'/category/conversion/'                         => $conversion_url,
		'/category/paid-media/'                         => $conversion_url,
	];
}

/**
 * Redirect deleted legacy category archives after the taxonomy consolidation.
 *
 * @return void
 */
function hu_redirect_positioned_blog_category_archives() : void {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$current_path = function_exists( 'nexus_get_current_request_path' )
		? nexus_get_current_request_path()
		: trailingslashit( '/' . ltrim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ), PHP_URL_PATH ), '/' ) );
	$redirect_map = hu_get_positioned_blog_category_redirect_map();

	if ( empty( $redirect_map[ $current_path ] ) ) {
		return;
	}

	$target_url = (string) $redirect_map[ $current_path ];
	if ( function_exists( 'nexus_append_current_query_to_redirect_url' ) ) {
		$target_url = nexus_append_current_query_to_redirect_url( $target_url );
	}

	wp_safe_redirect( $target_url, 301 );
	exit;
}
add_action( 'template_redirect', 'hu_redirect_positioned_blog_category_archives', 1 );

/**
 * Give the four surviving category hubs copy that matches the Werkstatt model.
 *
 * @param array<string,array<string,string>> $map Existing category SEO map.
 * @return array<string,array<string,string>>
 */
function hu_positioned_blog_category_seo_map( $map ) : array {
	$map = is_array( $map ) ? $map : [];

	$map['leadgenerierung'] = [
		'title'       => 'Eigene Anfragen & Leadökonomie | Haşim Üner',
		'description' => 'Analysen zu Leadkosten, CPO, Portalen, Vorqualifizierung und eigener Nachfrage-Infrastruktur — mit Fokus auf belastbare Wirtschaftlichkeit statt Lead-Menge.',
	];
	$map['wordpress-performance'] = [
		'title'       => 'WordPress & Performance | Haşim Üner',
		'description' => 'WordPress-Architektur, technisches SEO, Core Web Vitals, Hosting und Relaunch: technische Entscheidungen mit Wirkung auf Sichtbarkeit und Conversion.',
	];
	$map['tracking'] = [
		'title'       => 'Tracking & Messbarkeit | Haşim Üner',
		'description' => 'Server-Side Tracking, Attribution, Consent und Analytics: Messketten, die Entscheidungen statt nur Dashboard-Zahlen liefern.',
	];
	$map['cro'] = [
		'title'       => 'Conversion & Anfragearchitektur | Haşim Üner',
		'description' => 'Conversion-Optimierung, Landingpages, Formulare und CRM-Übergaben: wie aus Besuchern nachvollziehbar qualifizierte Anfragen werden.',
	];

	return $map;
}
add_filter( 'hu_category_archive_seo_map', 'hu_positioned_blog_category_seo_map', 20 );
