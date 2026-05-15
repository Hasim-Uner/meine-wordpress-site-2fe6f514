<?php
/**
 * WGOS asset content model.
 *
 * Hub = WGOS page. Spokes = hierarchical WGOS assets powered by a dedicated CPT.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize labels and slugs into one stable lookup key.
 *
 * @param string $value Raw lookup value.
 * @return string
 */
function nexus_get_wgos_asset_lookup_key( $value ) {
	return sanitize_title( wp_strip_all_tags( (string) $value ) );
}

/**
 * Build the explorer slug for a WGOS asset label or post.
 *
 * @param string|WP_Post $value Asset label, slug or post object.
 * @return string
 */
function nexus_get_wgos_asset_anchor_slug( $value ) {
	if ( $value instanceof WP_Post ) {
		$definition = function_exists( 'nexus_get_wgos_asset_definition' ) ? nexus_get_wgos_asset_definition( $value ) : null;

		if ( is_array( $definition ) && ! empty( $definition['slug'] ) ) {
			return sanitize_title( wp_strip_all_tags( (string) $definition['slug'] ) );
		}

		$raw_value = $value->post_title ? $value->post_title : $value->post_name;

		return sanitize_title( wp_strip_all_tags( (string) $raw_value ) );
	}

	$raw_value = (string) $value;
	$definition = function_exists( 'nexus_get_wgos_asset_definition' ) ? nexus_get_wgos_asset_definition( $raw_value ) : null;

	if ( is_array( $definition ) && ! empty( $definition['slug'] ) ) {
		return sanitize_title( wp_strip_all_tags( (string) $definition['slug'] ) );
	}

	$asset     = nexus_get_wgos_asset( $raw_value );

	if ( $asset instanceof WP_Post ) {
		return nexus_get_wgos_asset_anchor_slug( $asset );
	}

	return sanitize_title( wp_strip_all_tags( $raw_value ) );
}

/**
 * Build a per-request lookup table for published WGOS assets.
 *
 * Supports both explicit slugs and labels from the WGOS hub tables.
 *
 * @return array<string, WP_Post>
 */
function nexus_get_wgos_asset_lookup_map() {
	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map   = [];
	$posts = get_posts(
		[
			'post_type'              => 'wgos_asset',
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
		$title_key = nexus_get_wgos_asset_lookup_key( $post->post_title );
		$slug_key  = nexus_get_wgos_asset_lookup_key( $post->post_name );

		if ( $title_key ) {
			$map[ $title_key ] = $post;
		}

		if ( $slug_key ) {
			$map[ $slug_key ] = $post;
		}
	}

	return $map;
}

/**
 * Resolve a published WGOS asset by its hub label or slug.
 *
 * @param string $value Asset label or slug.
 * @return WP_Post|null
 */
function nexus_get_wgos_asset( $value ) {
	$key = nexus_get_wgos_asset_lookup_key( $value );

	if ( '' === $key ) {
		return null;
	}

	$map = nexus_get_wgos_asset_lookup_map();

	return $map[ $key ] ?? null;
}

/**
 * Return short hover copy for a linked WGOS asset.
 *
 * @param WP_Post $asset Asset post object.
 * @return string
 */
function nexus_get_wgos_asset_hover_text( $asset ) {
	$excerpt = trim( (string) $asset->post_excerpt );

	if ( '' !== $excerpt ) {
		return $excerpt;
	}

	return '';
}

/**
 * Render a hub table label that links to the WGOS asset when available.
 *
 * Falls back to plain text until a matching published asset exists.
 *
 * @param string $label Hub-visible asset label.
 * @return string
 */
function nexus_render_wgos_asset_label( $label ) {
	$label = (string) $label;
	$asset = nexus_get_wgos_asset( $label );
	$hint  = $asset instanceof WP_Post ? nexus_get_wgos_asset_hover_text( $asset ) : '';
	$url   = nexus_get_wgos_asset_anchor_url( $asset instanceof WP_Post ? $asset : $label );
	$cta   = $asset instanceof WP_Post ? __( 'Asset ansehen', 'blocksy-child' ) : __( 'Zur Asset-Landkarte', 'blocksy-child' );

	if ( '' === $hint ) {
		$hint = $asset instanceof WP_Post
			? __( 'Öffnet die passende WGOS-Asset-Seite mit Nutzen, Kontext und nächstem sinnvollen Schritt.', 'blocksy-child' )
			: __( 'Dieses Asset wird im WGOS-Kontext priorisiert und in der Asset-Landkarte sauber eingeordnet.', 'blocksy-child' );
	}

	return sprintf(
		'<span class="wgos-asset-link-wrap"><span class="wgos-asset-link">%1$s</span><span class="wgos-asset-link__panel"><span class="wgos-asset-link__text">%2$s</span><a class="wgos-asset-link__cta" href="%3$s" data-track-action="cta_wgos_asset_table" data-track-category="navigation">%4$s</a></span></span>',
		esc_html( $label ),
		esc_html( $hint ),
		esc_url( $url ),
		esc_html( $cta )
	);
}

/**
 * Resolve the WGOS hub page ID.
 *
 * @return int
 */
function nexus_get_wgos_page_id() {
	$template_page_id = nexus_get_page_id_by_template( 'page-wgos.php' );

	if ( $template_page_id ) {
		return $template_page_id;
	}

	return nexus_get_page_id( [ 'wgos', 'wordpress-growth-operating-system' ] );
}

/**
 * Resolve the WGOS hub page URL.
 *
 * @return string
 */
function nexus_get_wgos_url() {
	$agentur_url = function_exists( 'nexus_get_primary_public_url' )
		? nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) )
		: home_url( '/wordpress-agentur-hannover/' );

	return trailingslashit( $agentur_url ) . '#wgos';
}

/**
 * Resolve the dedicated WGOS asset hub page ID.
 *
 * @return int
 */
function nexus_get_wgos_asset_hub_page_id() {
	$slug_page_id = nexus_get_page_id( [ 'wgos-systemlandkarte', 'wgos-asset-hub', 'systemlandkarte' ] );

	if ( $slug_page_id ) {
		return $slug_page_id;
	}

	$template_page_id = nexus_get_page_id_by_template( 'page-wgos-assets.php' );

	if ( $template_page_id ) {
		return $template_page_id;
	}

	return 0;
}

/**
 * Resolve the dedicated WGOS asset hub page URL.
 *
 * Falls back to the core WGOS hub until the dedicated page exists.
 *
 * @return string
 */
function nexus_get_wgos_asset_hub_url() {
	$agentur_url = function_exists( 'nexus_get_primary_public_url' )
		? nexus_get_primary_public_url( 'agentur', home_url( '/wordpress-agentur-hannover/' ) )
		: home_url( '/wordpress-agentur-hannover/' );

	return trailingslashit( $agentur_url ) . '#asset-uebersicht';
}

/**
 * Ensure the dedicated WGOS asset hub page exists and uses the correct template.
 *
 * @return void
 */
function nexus_maybe_ensure_wgos_asset_hub_page() {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$page_id = nexus_get_wgos_asset_hub_page_id();

	if ( ! $page_id ) {
		return;
	}

	$current_template = (string) get_post_meta( $page_id, '_wp_page_template', true );

	if ( 'page-wgos-assets.php' === $current_template ) {
		return;
	}

	update_post_meta( (int) $page_id, '_wp_page_template', 'page-wgos-assets.php' );
}
add_action( 'init', 'nexus_maybe_ensure_wgos_asset_hub_page', 25 );

/**
 * Build an ordered list payload for the server-rendered WGOS asset hub overview.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_get_wgos_asset_hub_sections() {
	$phase_registry  = nexus_get_wgos_asset_phase_catalog();
	$module_registry = nexus_get_wgos_asset_module_catalog();
	$registry        = nexus_get_wgos_asset_registry();
	$sections        = [];

	foreach ( $module_registry as $module_key => $module ) {
		$phase_key = (string) $module['phase_key'];

		if ( ! isset( $phase_registry[ $phase_key ] ) ) {
			continue;
		}

		$items = [];

		foreach ( $registry as $asset ) {
			if ( (string) $asset['module_key'] !== $module_key ) {
				continue;
			}

			$detail_url = nexus_get_wgos_asset_detail_url( $asset );
			$items[]    = [
				'id'           => nexus_get_wgos_asset_anchor_id( (string) $asset['slug'] ),
				'title'        => (string) $asset['title'],
				'url'          => $detail_url ? $detail_url : nexus_get_wgos_asset_anchor_url( (string) $asset['slug'] ),
				'credits'      => (string) $asset['credits'],
				'goal'         => (string) $asset['goal'],
				'result'       => (string) $asset['result'],
				'status'       => (string) $asset['status'],
				'core_area'    => (string) $asset['core_area'],
				'keyword'      => (string) $asset['keyword'],
				'prerequisite' => (string) $asset['prerequisite'],
			];
		}

		if ( empty( $items ) ) {
			continue;
		}

		$sections[] = [
			'phase_label' => (string) $phase_registry[ $phase_key ]['label'],
			'phase_step'  => (string) $phase_registry[ $phase_key ]['eyebrow'],
			'module_id'   => (string) $module['id'],
			'module_no'   => (string) $module['number'],
			'module'      => (string) $module['label'],
			'summary'     => (string) $module['summary'],
			'accent'      => (string) $module['accent'],
			'items'       => $items,
		];
	}

	return $sections;
}

/**
 * Resolve the explorer anchor ID for a WGOS asset.
 *
 * @param string|WP_Post $value Asset label, slug or post object.
 * @return string
 */
function nexus_get_wgos_asset_anchor_id( $value ) {
	$slug = nexus_get_wgos_asset_anchor_slug( $value );

	if ( '' === $slug ) {
		return '';
	}

	return 'asset-' . $slug;
}

/**
 * Resolve the preferred WGOS destination for an asset.
 *
 * Asset detail pages take precedence. If no published asset page exists yet,
 * fall back to the dedicated asset hub.
 *
 * @param string|WP_Post $value Asset label, slug or post object.
 * @return string
 */
function nexus_get_wgos_asset_anchor_url( $value ) {
	$asset = $value instanceof WP_Post ? $value : nexus_get_wgos_asset( $value );

	if ( $asset instanceof WP_Post ) {
		$asset_url = get_permalink( $asset );

		if ( $asset_url ) {
			return $asset_url;
		}
	}

	$anchor_id = nexus_get_wgos_asset_anchor_id( $value );

	if ( '' !== $anchor_id ) {
		$hub_url = nexus_get_wgos_asset_hub_url();

		if ( false !== strpos( $hub_url, '#' ) ) {
			return $hub_url;
		}

		return trailingslashit( $hub_url ) . '#' . $anchor_id;
	}

	return nexus_get_wgos_asset_hub_url();
}

/**
 * Return the ordered WGOS asset phase catalog.
 *
 * @return array<string, array<string, string|array<int, string>>>
 */
function nexus_get_wgos_asset_phase_catalog() {
	return [
		'fundament' => [
			'id'          => 'fundament',
			'label'       => 'Fundament',
			'eyebrow'     => 'Phase 1',
			'description' => 'Strategie, technische Basis und Messbarkeit zuerst sauber aufstellen.',
			'aliases'     => [ 'fundament', 'phase-1', 'phase1', 'foundation', 'basis' ],
		],
		'aufbau' => [
			'id'          => 'aufbau',
			'label'       => 'Aufbau',
			'eyebrow'     => 'Phase 2',
			'description' => 'Sichtbarkeit und Conversion systematisch auf belastbarem Fundament ausbauen.',
			'aliases'     => [ 'aufbau', 'phase-2', 'phase2', 'build', 'growth' ],
		],
		'weiterentwicklung' => [
			'id'          => 'weiterentwicklung',
			'label'       => 'Weiterentwicklung',
			'eyebrow'     => 'Phase 3',
			'description' => 'Optimierung, Automatisierung und Betrieb erst dann erweitern, wenn das System trägt.',
			'aliases'     => [ 'weiterentwicklung', 'skalierung', 'phase-3', 'phase3', 'optimierung', 'betrieb' ],
		],
		'weitere' => [
			'id'          => 'weitere',
			'label'       => 'Weitere',
			'eyebrow'     => 'Ergänzend',
			'description' => 'Nicht sauber zuordenbare Assets werden hier gesammelt, bis die Struktur geschärft ist.',
			'aliases'     => [ 'weitere', 'sonstige', 'uncategorized', 'andere' ],
		],
	];
}

/**
 * Return the ordered WGOS asset module catalog.
 *
 * @return array<string, array<string, string|array<int, string>>>
 */
function nexus_get_wgos_asset_module_catalog() {
	return [
		'strategy' => [
			'id'        => 'module-strategy',
			'number'    => '01',
			'label'     => 'Strategie',
			'phase_key' => 'fundament',
			'accent'    => '#d4af37',
			'summary'   => 'Positionierung, Angebotslogik und Prioritäten als Richtungsgeber für das gesamte System.',
			'aliases'   => [ 'strategie', 'positionierung', 'angebot', 'angebotslogik', 'roadmap' ],
		],
		'foundation' => [
			'id'        => 'module-foundation',
			'number'    => '02',
			'label'     => 'Technisches Fundament',
			'phase_key' => 'fundament',
			'accent'    => '#6ea8ff',
			'summary'   => 'Performance, Stabilität und technische Tragfähigkeit als Basis für Sichtbarkeit und Nachfrage.',
			'aliases'   => [ 'technisches-fundament', 'performance', 'security', 'sicherheit', 'stabilitaet', 'core-web-vitals', 'module-01', 'module-02' ],
		],
		'measurement' => [
			'id'        => 'module-measurement',
			'number'    => '03',
			'label'     => 'Messbarkeit',
			'phase_key' => 'fundament',
			'accent'    => '#b084ff',
			'summary'   => 'Tracking, Datenqualität und Entscheidungsgrundlagen für ein steuerbares Nachfrage-System.',
			'aliases'   => [ 'messbarkeit', 'measurement', 'tracking', 'analytics', 'datenschutz', 'ga4', 'sgtm', 'module-03' ],
		],
		'visibility' => [
			'id'        => 'module-visibility',
			'number'    => '04',
			'label'     => 'Sichtbarkeit',
			'phase_key' => 'aufbau',
			'accent'    => '#52d39a',
			'summary'   => 'SEO, Struktur und Inhalte, die echte Nachfrage auf die richtigen Seiten führen.',
			'aliases'   => [ 'sichtbarkeit', 'seo', 'content', 'inhalte', 'technical-seo', 'pillar-page', 'module-04' ],
		],
		'conversion' => [
			'id'        => 'module-conversion',
			'number'    => '05',
			'label'     => 'Conversion',
			'phase_key' => 'aufbau',
			'accent'    => '#f2c15f',
			'summary'   => 'Nutzerführung, Proof und Angebotslogik für sauberere Anfragepfade.',
			'aliases'   => [ 'conversion', 'cro', 'landing-page', 'landing-pages', 'formular', 'formulare', 'module-05' ],
		],
		'iteration' => [
			'id'        => 'module-iteration',
			'number'    => '06',
			'label'     => 'Weiterentwicklung',
			'phase_key' => 'weiterentwicklung',
			'accent'    => '#ff9f67',
			'summary'   => 'Optimierung, Reporting, Automatisierung und Ausbau erst auf stabiler Systembasis.',
			'aliases'   => [ 'weiterentwicklung', 'skalierung', 'automation', 'automatisierung', 'betrieb', 'paid', 'reporting', 'module-06', 'module-07' ],
		],
	];
}

/**
 * Check whether a raw meta value actually contains meaningful content.
 *
 * @param mixed $value Raw meta or ACF value.
 * @return bool
 */
function nexus_wgos_asset_value_exists( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $item ) {
			if ( nexus_wgos_asset_value_exists( $item ) ) {
				return true;
			}
		}

		return false;
	}

	if ( is_object( $value ) ) {
		return nexus_wgos_asset_value_exists( (array) $value );
	}

	return ! in_array( $value, [ '', null, false ], true );
}

/**
 * Return the first non-empty asset value across multiple field candidates.
 *
 * Supports ACF fields and plain post meta.
 *
 * @param int   $post_id Post ID.
 * @param array $keys    Candidate field/meta keys.
 * @return mixed
 */
function nexus_get_wgos_asset_value( $post_id, $keys ) {
	$keys = (array) $keys;

	foreach ( $keys as $key ) {
		if ( ! $key ) {
			continue;
		}

		if ( function_exists( 'get_field' ) ) {
			$acf_value = get_field( $key, $post_id );

			if ( nexus_wgos_asset_value_exists( $acf_value ) ) {
				return $acf_value;
			}
		}

		$meta_value = get_post_meta( $post_id, $key, true );

		if ( nexus_wgos_asset_value_exists( $meta_value ) ) {
			return $meta_value;
		}
	}

	return '';
}

/**
 * Normalize a choice-like field value into a single string.
 *
 * @param mixed $value Raw ACF/meta value.
 * @return string
 */
function nexus_get_wgos_asset_choice_string( $value ) {
	if ( is_array( $value ) ) {
		foreach ( [ 'value', 'label', 'title', 'name', 'text' ] as $candidate ) {
			if ( isset( $value[ $candidate ] ) && nexus_wgos_asset_value_exists( $value[ $candidate ] ) ) {
				return trim( (string) $value[ $candidate ] );
			}
		}

		if ( 1 === count( $value ) ) {
			return nexus_get_wgos_asset_choice_string( reset( $value ) );
		}

		return '';
	}

	if ( is_object( $value ) ) {
		return nexus_get_wgos_asset_choice_string( (array) $value );
	}

	if ( is_scalar( $value ) ) {
		return trim( (string) $value );
	}

	return '';
}

/**
 * Return the first string-like value for an asset from candidate fields.
 *
 * @param int          $post_id  Post ID.
 * @param array        $keys     Candidate field/meta keys.
 * @param string|false $fallback Fallback string.
 * @return string
 */
function nexus_get_wgos_asset_text_value( $post_id, $keys, $fallback = '' ) {
	$value = nexus_get_wgos_asset_value( $post_id, $keys );
	$text  = nexus_get_wgos_asset_choice_string( $value );

	if ( '' !== $text ) {
		return $text;
	}

	return false === $fallback ? '' : (string) $fallback;
}

/**
 * Normalize asset bullets from textareas, repeaters or arrays.
 *
 * @param mixed $value Raw ACF/meta value.
 * @return array<int, string>
 */
function nexus_get_wgos_asset_bullets_from_value( $value ) {
	$bullets = [];

	if ( is_string( $value ) ) {
		$lines = preg_split( '/\r\n|\r|\n/', $value );

		foreach ( (array) $lines as $line ) {
			$line = trim( wp_strip_all_tags( (string) $line ) );

			if ( '' !== $line ) {
				$bullets[] = $line;
			}
		}
	}

	if ( is_array( $value ) ) {
		foreach ( $value as $item ) {
			if ( is_string( $item ) ) {
				$item = trim( wp_strip_all_tags( $item ) );

				if ( '' !== $item ) {
					$bullets[] = $item;
				}

				continue;
			}

			if ( is_array( $item ) || is_object( $item ) ) {
				$item = (array) $item;

				foreach ( [ 'bullet', 'text', 'item', 'label', 'value', 'description' ] as $candidate ) {
					if ( ! empty( $item[ $candidate ] ) ) {
						$bullet = trim( wp_strip_all_tags( (string) $item[ $candidate ] ) );

						if ( '' !== $bullet ) {
							$bullets[] = $bullet;
							break;
						}
					}
				}
			}
		}
	}

	$bullets = array_values( array_unique( array_filter( $bullets ) ) );

	return $bullets;
}

/**
 * Match a raw phase label to the internal phase catalog key.
 *
 * @param string $raw_value Raw phase value.
 * @return string
 */
function nexus_match_wgos_asset_phase_key( $raw_value ) {
	$catalog = nexus_get_wgos_asset_phase_catalog();
	$needle  = nexus_get_wgos_asset_lookup_key( $raw_value );

	if ( '' === $needle ) {
		return '';
	}

	foreach ( $catalog as $phase_key => $phase ) {
		$aliases = $phase['aliases'] ?? [];
		$haystack = array_merge(
			[ $phase_key, $phase['id'], $phase['label'] ],
			(array) $aliases
		);

		foreach ( $haystack as $alias ) {
			if ( $needle === nexus_get_wgos_asset_lookup_key( $alias ) ) {
				return $phase_key;
			}
		}
	}

	return '';
}

/**
 * Match a raw module label to the internal module catalog key.
 *
 * @param string $raw_value Raw module value.
 * @return string
 */
function nexus_match_wgos_asset_module_key( $raw_value ) {
	$catalog = nexus_get_wgos_asset_module_catalog();
	$needle  = nexus_get_wgos_asset_lookup_key( $raw_value );

	if ( '' === $needle ) {
		return '';
	}

	foreach ( $catalog as $module_key => $module ) {
		$aliases = $module['aliases'] ?? [];
		$haystack = array_merge(
			[ $module_key, $module['id'], $module['label'] ],
			(array) $aliases
		);

		foreach ( $haystack as $alias ) {
			if ( $needle === nexus_get_wgos_asset_lookup_key( $alias ) ) {
				return $module_key;
			}
		}
	}

	return '';
}

/**
 * Build the shared link map for the asset explorer CTA layer.
 *
 * @return array<string, string>
 */
function nexus_get_wgos_asset_explorer_links() {
	$calendar_url = function_exists( 'nexus_get_audit_calendar_url' ) ? nexus_get_audit_calendar_url() : home_url( '/kontakt/' );
	$cases_url    = nexus_get_results_url();

	return [
		'audit'    => nexus_get_audit_url(),
		'calendar' => $calendar_url,
		'hub'      => nexus_get_wgos_asset_hub_url(),
		'wgos'     => nexus_get_wgos_url(),
		'pakete'   => nexus_get_wgos_url(),
		'cases'    => $cases_url,
	];
}

/**
 * Build the dynamic explorer payload from the versioned asset registry.
 *
 * @return array<string, mixed>
 */
function nexus_get_wgos_asset_explorer_payload() {
	static $payload = null;

	if ( null !== $payload ) {
		return $payload;
	}

	$phase_registry   = nexus_get_wgos_asset_phase_catalog();
	$module_registry  = nexus_get_wgos_asset_module_catalog();
	$assets           = [];
	$used_modules     = [];
	$used_phases      = [];
	$published_count  = 0;
	$draft_count      = 0;

	foreach ( nexus_get_wgos_asset_registry() as $asset ) {
		$module_key = (string) $asset['module_key'];
		$phase_key  = (string) $asset['phase_key'];

		if ( ! isset( $module_registry[ $module_key ], $phase_registry[ $phase_key ] ) ) {
			continue;
		}

		$detail_url  = nexus_get_wgos_asset_detail_url( $asset );
		$is_publish  = '' !== $detail_url && 'publish' === $asset['status'];
		$phase_label = (string) $phase_registry[ $phase_key ]['label'];
		$module      = $module_registry[ $module_key ];

		$used_modules[ $module_key ] = true;
		$used_phases[ $phase_key ]   = true;

		if ( $is_publish ) {
			$published_count++;
		} else {
			$draft_count++;
		}

		$assets[] = [
			'id'          => nexus_get_wgos_asset_anchor_slug( (string) $asset['slug'] ),
			'aliases'     => array_values(
				array_filter(
					array_unique(
						array_map(
							'nexus_get_wgos_asset_lookup_key',
							array_merge(
								[ (string) $asset['slug'] ],
								(array) ( $asset['legacy_slugs'] ?? [] )
							)
						)
					)
				)
			),
			'label'       => (string) $asset['title'],
			'moduleId'    => (string) $module['id'],
			'group'       => (string) $asset['core_area'],
			'category'    => $phase_label,
			'short'       => (string) $asset['excerpt'],
			'long'        => [
				'intro'   => isset( $asset['problem'][0] ) ? (string) $asset['problem'][0] : (string) $asset['excerpt'],
				'bullets' => nexus_get_wgos_asset_explorer_bullets( $asset ),
			],
			'deliverable' => (string) $asset['result'],
			'credits'     => (string) $asset['credits'],
			'status'      => $asset['status'],
			'cta'         => $is_publish
				? [
					'label' => __( 'Asset im Detail ansehen', 'blocksy-child' ),
					'href'  => $detail_url,
				]
				: [
					'label'   => nexus_get_audit_cta_label(),
					'hrefKey' => 'audit',
				],
		];
	}

	$phases  = [];
	$modules = [];

	foreach ( $phase_registry as $phase_key => $phase ) {
		if ( empty( $used_phases[ $phase_key ] ) ) {
			continue;
		}

		$phases[] = [
			'id'          => (string) $phase['id'],
			'label'       => (string) $phase['label'],
			'eyebrow'     => (string) $phase['eyebrow'],
			'description' => (string) $phase['description'],
		];
	}

	foreach ( $module_registry as $module_key => $module ) {
		if ( empty( $used_modules[ $module_key ] ) ) {
			continue;
		}

		$modules[] = [
			'id'       => (string) $module['id'],
			'number'   => (string) $module['number'],
			'label'    => (string) $module['label'],
			'category' => (string) $phase_registry[ (string) $module['phase_key'] ]['label'],
			'accent'   => (string) $module['accent'],
			'summary'  => (string) $module['summary'],
		];
	}

	$payload = [
		'wgosAssetPhases'  => $phases,
		'wgosAssetModules' => $modules,
		'wgosAssets'       => $assets,
		'summary'          => [
			'totalAssets'     => count( $assets ),
			'publishedAssets' => $published_count,
			'draftAssets'     => $draft_count,
		],
	];

	return $payload;
}

/**
 * Register the hierarchical WGOS asset post type.
 *
 * The post type is public, searchable and block-editor ready so new asset
 * spokes can be added without creating more static templates.
 *
 * @return void
 */
function nexus_register_wgos_asset_post_type() {
	register_post_type(
		'wgos_asset',
		[
			'labels' => [
				'name'                  => __( 'WGOS Assets', 'blocksy-child' ),
				'singular_name'         => __( 'WGOS Asset', 'blocksy-child' ),
				'menu_name'             => __( 'WGOS Assets', 'blocksy-child' ),
				'name_admin_bar'        => __( 'WGOS Asset', 'blocksy-child' ),
				'add_new'               => __( 'Neu', 'blocksy-child' ),
				'add_new_item'          => __( 'Neues WGOS Asset', 'blocksy-child' ),
				'edit_item'             => __( 'WGOS Asset bearbeiten', 'blocksy-child' ),
				'new_item'              => __( 'Neues WGOS Asset', 'blocksy-child' ),
				'view_item'             => __( 'WGOS Asset ansehen', 'blocksy-child' ),
				'view_items'            => __( 'WGOS Assets ansehen', 'blocksy-child' ),
				'search_items'          => __( 'WGOS Assets suchen', 'blocksy-child' ),
				'not_found'             => __( 'Keine WGOS Assets gefunden.', 'blocksy-child' ),
				'not_found_in_trash'    => __( 'Keine WGOS Assets im Papierkorb gefunden.', 'blocksy-child' ),
				'all_items'             => __( 'Alle WGOS Assets', 'blocksy-child' ),
				'archives'              => __( 'WGOS Asset Archiv', 'blocksy-child' ),
				'attributes'            => __( 'WGOS Asset Attribute', 'blocksy-child' ),
				'insert_into_item'      => __( 'In WGOS Asset einfügen', 'blocksy-child' ),
				'uploaded_to_this_item' => __( 'Zu diesem WGOS Asset hochgeladen', 'blocksy-child' ),
				'parent_item_colon'     => __( 'Übergeordnetes WGOS Asset:', 'blocksy-child' ),
			],
			'description'         => __( 'Hierarchische WGOS Asset-Detailseiten als skalierbare Spokes unter dem WGOS-Hub.', 'blocksy-child' ),
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
				'slug'       => 'wgos-assets',
				'with_front' => false,
				'feeds'      => false,
				'pages'      => true,
			],
			'hierarchical'        => true,
			'menu_icon'           => 'dashicons-networking',
			'menu_position'       => 24,
			'supports'            => [
				'title',
				'editor',
				'excerpt',
				'thumbnail',
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
add_action( 'init', 'nexus_register_wgos_asset_post_type' );

// Das Template rendert den Hero selbst; der Blocksy-Standardtitel waere doppelt.
add_filter( 'blocksy:post_types:wgos_asset:has_page_title', '__return_false' );
