<?php
/**
 * Glossary hub and post type helpers.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize glossary labels and slugs into one stable lookup key.
 *
 * @param string $value Raw lookup value.
 * @return string
 */
function nexus_get_glossary_lookup_key( $value ) {
	return sanitize_title( wp_strip_all_tags( (string) $value ) );
}

/**
 * Return the ordered glossary area catalog.
 *
 * @return array<string, array<string, string|array<int, string>>>
 */
function nexus_get_glossary_area_catalog() {
	return [
		'Strategie' => [
			'id'          => 'strategy',
			'label'       => 'Strategie',
			'accent'      => '#d4af37',
			'summary'     => 'Begriffe, die Angebot, Priorisierung und Differenzierung sauber einordnen.',
			'description' => 'Diese Begriffe schaffen Orientierung, bevor Teams in Seitenbau, Kampagnen oder Tools springen.',
			'aliases'     => [ 'strategie', 'positionierung', 'angebot', 'owned-leads' ],
		],
		'Technisches Fundament' => [
			'id'          => 'foundation',
			'label'       => 'Technisches Fundament',
			'accent'      => '#6ea8ff',
			'summary'     => 'Metriken und Technikbegriffe, die Ladezeit, Stabilität und Systemtragfähigkeit erklären.',
			'description' => 'Hier liegt die definitorische Tiefe für Performance-Themen, die Head Terms nicht dupliziert.',
			'aliases'     => [ 'technisches-fundament', 'performance', 'core-web-vitals', 'server' ],
		],
		'Messbarkeit' => [
			'id'          => 'measurement',
			'label'       => 'Messbarkeit',
			'accent'      => '#b084ff',
			'summary'     => 'Tracking-, Analytics- und Attributionsbegriffe als Brücke zum Setup.',
			'description' => 'Das Glossar erklärt die Begriffe, während die Setup-Seiten die operative Umsetzung übernehmen.',
			'aliases'     => [ 'messbarkeit', 'tracking', 'analytics', 'ga4', 'utm' ],
		],
		'Sichtbarkeit' => [
			'id'          => 'visibility',
			'label'       => 'Sichtbarkeit',
			'accent'      => '#52d39a',
			'summary'     => 'SEO- und IA-Begriffe, die technische Klarheit auf die Primary URL zurückführen.',
			'description' => 'Hier landen definitorische Sub-Terms, nicht die kommerziellen Head Terms selbst.',
			'aliases'     => [ 'sichtbarkeit', 'seo', 'indexierung', 'canonical', 'crawlability' ],
		],
		'Conversion' => [
			'id'          => 'conversion',
			'label'       => 'Conversion',
			'accent'      => '#f2c15f',
			'summary'     => 'Mikrokonzepte aus CRO und Angebotslogik, die kaufnahe Reibung sichtbarer machen.',
			'description' => 'Statt CRO als Head Term zu wiederholen, erklärt das Glossar die kleinen Hebel darunter.',
			'aliases'     => [ 'conversion', 'cro', 'message-match', 'cta', 'proof' ],
		],
	];
}

/**
 * Return the supported glossary index policies.
 *
 * @return array<string, array<string, string>>
 */
function nexus_get_glossary_policy_catalog() {
	return [
		'index' => [
			'label'       => 'Index',
			'description' => 'Eigenständige Glossar-Seite mit eigener Suchintention.',
			'cta_label'   => 'Begriff öffnen',
		],
		'noindex' => [
			'label'       => 'Noindex',
			'description' => 'Eigenständige Seite für Klarheit auf der Website, aber ohne Ranking-Ziel.',
			'cta_label'   => 'Begriff öffnen',
		],
		'alias' => [
			'label'       => 'Alias',
			'description' => 'Glossar-Eintrag verweist bewusst auf die Primary URL, um Kannibalisierung zu vermeiden.',
			'cta_label'   => 'Primary URL öffnen',
		],
	];
}

/**
 * Resolve the glossary hub page ID.
 *
 * @return int
 */
function nexus_get_glossary_hub_page_id() {
	$slug_page_id = nexus_get_page_id( [ 'glossar' ] );

	if ( $slug_page_id ) {
		return $slug_page_id;
	}

	return nexus_get_page_id_by_template( 'page-glossar.php' );
}

/**
 * Resolve the glossary hub page URL.
 *
 * @return string
 */
function nexus_get_glossary_hub_url() {
	$page_id = nexus_get_glossary_hub_page_id();

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	return home_url( '/glossar/' );
}

/**
 * Check whether the current request targets the dedicated glossary hub.
 *
 * @return bool
 */
function nexus_is_glossary_hub_page() {
	if ( ! is_page() ) {
		return false;
	}

	$page_id = get_queried_object_id();

	if ( ! $page_id ) {
		return false;
	}

	if ( 'page-glossar.php' === get_page_template_slug( $page_id ) ) {
		return true;
	}

	return 'glossar' === get_post_field( 'post_name', $page_id );
}

/**
 * Ensure the dedicated glossary hub page exists and uses the correct template.
 *
 * @return void
 */
function nexus_maybe_ensure_glossary_hub_page() {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$page_id = nexus_get_glossary_hub_page_id();

	if ( ! $page_id ) {
		$page_id = wp_insert_post(
			wp_slash(
				[
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => 'Glossar',
					'post_name'    => 'glossar',
					'post_content' => '',
					'post_excerpt' => 'Glossar für SEO, Tracking, Performance und Conversion mit klaren Links auf die richtigen Primary URLs.',
				]
			),
			true
		);

		if ( is_wp_error( $page_id ) ) {
			return;
		}
	}

	$page_id          = (int) $page_id;
	$current_template = (string) get_post_meta( $page_id, '_wp_page_template', true );

	if ( 'page-glossar.php' !== $current_template ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-glossar.php' );
	}

	$current_excerpt = (string) get_post_field( 'post_excerpt', $page_id );

	if ( '' === trim( $current_excerpt ) ) {
		wp_update_post(
			[
				'ID'           => $page_id,
				'post_excerpt' => 'Glossar für SEO, Tracking, Performance und Conversion mit klaren Links auf die richtigen Primary URLs.',
			]
		);
	}

	if ( '' === trim( (string) get_post_meta( $page_id, 'seo_title', true ) ) ) {
		update_post_meta( $page_id, 'seo_title', 'Glossar für SEO, Tracking und CRO | Haşim Üner' );
	}

	if ( '' === trim( (string) get_post_meta( $page_id, 'seo_description', true ) ) ) {
		update_post_meta( $page_id, 'seo_description', 'Glossar für SEO, Tracking, Performance und Conversion: definitorische Begriffe mit sauberer Brücke zu den passenden Primary URLs.' );
	}
}
add_action( 'init', 'nexus_maybe_ensure_glossary_hub_page', 26 );

/**
 * Return the current rewrite version for glossary routes.
 *
 * @return string
 */
function nexus_get_glossary_rewrite_version() {
	return '2026-03-14-glossary-routes-v2';
}

/**
 * Flush glossary rewrite rules once after route changes.
 *
 * The glossary CPT was introduced after the active theme was already live, so
 * relying on after_switch_theme alone is not enough for existing installations.
 *
 * @return void
 */
function nexus_maybe_flush_glossary_rewrite_rules() {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version = nexus_get_glossary_rewrite_version();

	if ( $version === get_option( 'nexus_glossary_rewrite_version', '' ) ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'nexus_glossary_rewrite_version', $version, false );
}
add_action( 'init', 'nexus_maybe_flush_glossary_rewrite_rules', 40 );

/**
 * Build a lookup table for published glossary posts.
 *
 * @return array<string, WP_Post>
 */
function nexus_get_glossary_term_lookup_map() {
	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map   = [];
	$posts = get_posts(
		[
			'post_type'              => 'glossary_term',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]
	);

	foreach ( $posts as $post ) {
		$title_key = nexus_get_glossary_lookup_key( $post->post_title );
		$slug_key  = nexus_get_glossary_lookup_key( $post->post_name );

		if ( '' !== $title_key ) {
			$map[ $title_key ] = $post;
		}

		if ( '' !== $slug_key ) {
			$map[ $slug_key ] = $post;
		}
	}

	return $map;
}

/**
 * Resolve a published glossary term post by slug or title.
 *
 * @param string $value Term title or slug.
 * @return WP_Post|null
 */
function nexus_get_glossary_term( $value ) {
	$key = nexus_get_glossary_lookup_key( $value );

	if ( '' === $key ) {
		return null;
	}

	$map = nexus_get_glossary_term_lookup_map();

	return $map[ $key ] ?? null;
}

/**
 * Return compact glossary hub counters.
 *
 * @return array<string, int>
 */
function nexus_get_glossary_hub_summary() {
	$registry    = function_exists( 'nexus_get_glossary_registry' ) ? nexus_get_glossary_registry() : [];
	$index_count = 0;
	$alias_count = 0;
	$detail_count = 0;

	foreach ( $registry as $term ) {
		if ( 'publish' !== (string) ( $term['status'] ?? '' ) ) {
			continue;
		}

		$policy = isset( $term['index_policy'] ) ? (string) $term['index_policy'] : 'index';

		if ( 'alias' === $policy ) {
			++$alias_count;
			continue;
		}

		++$detail_count;

		if ( 'index' === $policy ) {
			++$index_count;
		}
	}

	return [
		'totalTerms'   => count( $registry ),
		'detailTerms'  => $detail_count,
		'indexTerms'   => $index_count,
		'aliasTerms'   => $alias_count,
		'areas'        => count( nexus_get_glossary_area_catalog() ),
	];
}

/**
 * Resolve the preferred destination and CTA for one glossary term.
 *
 * @param array<string, mixed>|string|WP_Post $term Term definition or identifier.
 * @return array<string, mixed>
 */
function nexus_get_glossary_term_destination( $term ) {
	$definition = is_array( $term ) ? $term : ( function_exists( 'nexus_get_glossary_definition' ) ? nexus_get_glossary_definition( $term ) : null );

	if ( ! is_array( $definition ) ) {
		return [
			'url'          => nexus_get_glossary_hub_url(),
			'cta_label'    => __( 'Zum Glossar', 'blocksy-child' ),
			'policy_label' => '',
			'is_primary'   => false,
		];
	}

	$policy_catalog = nexus_get_glossary_policy_catalog();
	$policy         = isset( $definition['index_policy'] ) ? (string) $definition['index_policy'] : 'index';
	$policy_data    = $policy_catalog[ $policy ] ?? $policy_catalog['index'];
	$primary_url    = function_exists( 'nexus_get_glossary_primary_url' ) ? nexus_get_glossary_primary_url( $definition ) : '';
	$detail_url     = function_exists( 'nexus_get_glossary_term_detail_url' ) ? nexus_get_glossary_term_detail_url( $definition ) : '';
	$url            = $detail_url ? $detail_url : nexus_get_glossary_hub_url();
	$is_primary     = false;

	if ( 'alias' === $policy && '' !== $primary_url ) {
		$url        = $primary_url;
		$is_primary = true;
	}

	return [
		'url'          => $url,
		'cta_label'    => (string) $policy_data['cta_label'],
		'policy_label' => (string) $policy_data['label'],
		'is_primary'   => $is_primary,
	];
}

/**
 * Build the ordered glossary hub sections.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_get_glossary_hub_sections() {
	if ( ! function_exists( 'nexus_get_glossary_registry' ) ) {
		return [];
	}

	$area_catalog = nexus_get_glossary_area_catalog();
	$sections     = [];

	foreach ( $area_catalog as $area_label => $area ) {
		$items = [];

		foreach ( nexus_get_glossary_registry() as $term ) {
			if ( 'publish' !== (string) ( $term['status'] ?? '' ) ) {
				continue;
			}

			if ( (string) $term['core_area'] !== $area_label ) {
				continue;
			}

			$destination = nexus_get_glossary_term_destination( $term );
			$primary_label = '';

			if ( ! empty( $term['primary_url_label'] ) ) {
				$primary_label = (string) $term['primary_url_label'];
			} elseif ( ! empty( $term['primary_url_key'] ) ) {
				$primary_label = ucwords( str_replace( '_', ' ', (string) $term['primary_url_key'] ) );
			}

			$items[] = [
				'slug'           => (string) $term['slug'],
				'title'          => (string) $term['title'],
				'excerpt'        => (string) $term['excerpt'],
				'url'            => (string) $destination['url'],
				'cta_label'      => (string) $destination['cta_label'],
				'policy_label'   => (string) $destination['policy_label'],
				'is_primary'     => ! empty( $destination['is_primary'] ),
				'primary_label'  => $primary_label,
				'primary_reason' => (string) $term['primary_url_reason'],
			];
		}

		if ( [] === $items ) {
			continue;
		}

		$sections[] = [
			'id'          => (string) $area['id'],
			'label'       => (string) $area['label'],
			'accent'      => (string) $area['accent'],
			'summary'     => (string) $area['summary'],
			'description' => (string) $area['description'],
			'items'       => $items,
		];
	}

	return $sections;
}

/**
 * Register the glossary post type.
 *
 * @return void
 */
function nexus_register_glossary_term_post_type() {
	register_post_type(
		'glossary_term',
		[
			'labels' => [
				'name'                  => __( 'Glossar', 'blocksy-child' ),
				'singular_name'         => __( 'Glossar-Begriff', 'blocksy-child' ),
				'menu_name'             => __( 'Glossar', 'blocksy-child' ),
				'name_admin_bar'        => __( 'Glossar-Begriff', 'blocksy-child' ),
				'add_new'               => __( 'Neu', 'blocksy-child' ),
				'add_new_item'          => __( 'Neuen Glossar-Begriff', 'blocksy-child' ),
				'edit_item'             => __( 'Glossar-Begriff bearbeiten', 'blocksy-child' ),
				'new_item'              => __( 'Neuer Glossar-Begriff', 'blocksy-child' ),
				'view_item'             => __( 'Glossar-Begriff ansehen', 'blocksy-child' ),
				'view_items'            => __( 'Glossar-Begriffe ansehen', 'blocksy-child' ),
				'search_items'          => __( 'Glossar-Begriffe suchen', 'blocksy-child' ),
				'not_found'             => __( 'Keine Glossar-Begriffe gefunden.', 'blocksy-child' ),
				'not_found_in_trash'    => __( 'Keine Glossar-Begriffe im Papierkorb gefunden.', 'blocksy-child' ),
				'all_items'             => __( 'Alle Glossar-Begriffe', 'blocksy-child' ),
				'archives'              => __( 'Glossar-Archiv', 'blocksy-child' ),
				'attributes'            => __( 'Glossar-Attribute', 'blocksy-child' ),
				'insert_into_item'      => __( 'In Glossar-Begriff einfügen', 'blocksy-child' ),
				'uploaded_to_this_item' => __( 'Zu diesem Glossar-Begriff hochgeladen', 'blocksy-child' ),
			],
			'description'         => __( 'Glossar-Begriffe für SEO, Tracking, Performance und Conversion mit sauberem Bezug auf die Primary URLs.', 'blocksy-child' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'show_in_rest'        => true,
			'exclude_from_search' => false,
			'has_archive'         => false,
			'rewrite'             => [
				'slug'       => 'glossar',
				'with_front' => false,
				'feeds'      => false,
				'pages'      => true,
			],
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-book-alt',
			'menu_position'       => 25,
			'supports'            => [
				'title',
				'editor',
				'excerpt',
				'page-attributes',
				'revisions',
				'custom-fields',
			],
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'can_export'          => true,
			'delete_with_user'    => false,
		]
	);
}
add_action( 'init', 'nexus_register_glossary_term_post_type' );

add_filter( 'blocksy:post_types:glossary_term:has_page_title', '__return_false' );
