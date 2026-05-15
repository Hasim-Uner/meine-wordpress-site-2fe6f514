<?php
/**
 * WGOS asset registry, content sync and structured rendering helpers.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the current WGOS asset sync version.
 *
 * @return string
 */
function nexus_get_wgos_asset_registry_version() {
	return '2026-05-15-wgos-assets-v6-cta-strategiegespraech';
}

/**
 * Map WGOS core areas to phase and explorer module keys.
 *
 * @return array<string, array<string, string>>
 */
function nexus_get_wgos_asset_area_map() {
	return [
		'Strategie' => [
			'phase_key'  => 'fundament',
			'module_key' => 'strategy',
		],
		'Technisches Fundament' => [
			'phase_key'  => 'fundament',
			'module_key' => 'foundation',
		],
		'Messbarkeit' => [
			'phase_key'  => 'fundament',
			'module_key' => 'measurement',
		],
		'Sichtbarkeit' => [
			'phase_key'  => 'aufbau',
			'module_key' => 'visibility',
		],
		'Conversion' => [
			'phase_key'  => 'aufbau',
			'module_key' => 'conversion',
		],
		'Weiterentwicklung' => [
			'phase_key'  => 'weiterentwicklung',
			'module_key' => 'iteration',
		],
	];
}

/**
 * Load the WGOS asset registry from the versioned data file.
 *
 * @return array<string, array<string, mixed>>
 */
function nexus_get_wgos_asset_registry() {
	static $registry = null;

	if ( null !== $registry ) {
		return $registry;
	}

	$definitions = require __DIR__ . '/wgos-asset-registry-data.php';
	$area_map    = nexus_get_wgos_asset_area_map();
	$registry    = [];

	foreach ( (array) $definitions as $slug => $definition ) {
		if ( ! is_array( $definition ) ) {
			continue;
		}

		$asset_slug = sanitize_title( $definition['slug'] ?? $slug );
		$core_area  = isset( $definition['core_area'] ) ? (string) $definition['core_area'] : '';
		$area_data  = $area_map[ $core_area ] ?? [
			'phase_key'  => 'weitere',
			'module_key' => 'custom',
		];

		$definition['slug']            = $asset_slug;
		$definition['title']           = isset( $definition['title'] ) ? (string) $definition['title'] : ucfirst( str_replace( '-', ' ', $asset_slug ) );
		$definition['core_area']       = $core_area;
		$definition['phase_key']       = isset( $definition['phase_key'] ) ? (string) $definition['phase_key'] : (string) $area_data['phase_key'];
		$definition['module_key']      = isset( $definition['module_key'] ) ? (string) $definition['module_key'] : (string) $area_data['module_key'];
		$definition['status']          = 'publish' === ( $definition['status'] ?? '' ) ? 'publish' : 'draft';
		$definition['credits']         = isset( $definition['credits'] ) ? (string) $definition['credits'] : '';
		$definition['goal']            = isset( $definition['goal'] ) ? (string) $definition['goal'] : '';
		$definition['result']          = isset( $definition['result'] ) ? (string) $definition['result'] : '';
		$definition['prerequisite']    = isset( $definition['prerequisite'] ) ? (string) $definition['prerequisite'] : '';
		$definition['keyword']         = isset( $definition['keyword'] ) ? (string) $definition['keyword'] : $definition['title'];
		$definition['excerpt']         = isset( $definition['excerpt'] ) ? (string) $definition['excerpt'] : '';
		$definition['seo_title']       = hu_normalize_brand_text( isset( $definition['seo_title'] ) ? (string) $definition['seo_title'] : $definition['title'] . ' - WGOS Asset | Haşim Üner' );
		$definition['seo_description'] = isset( $definition['seo_description'] ) ? (string) $definition['seo_description'] : $definition['excerpt'];
		$definition['schema_type']     = isset( $definition['schema_type'] ) ? (string) $definition['schema_type'] : 'Service';
		$definition['legacy_slugs']    = array_values(
			array_filter(
				array_map(
					'sanitize_title',
					(array) ( $definition['legacy_slugs'] ?? [] )
				)
			)
		);
		$definition['problem']         = array_values(
			array_filter(
				array_map(
					'strval',
					(array) ( $definition['problem'] ?? [] )
				)
			)
		);
		$definition['deliverables']    = array_values(
			array_filter(
				(array) ( $definition['deliverables'] ?? [] ),
				static function ( $deliverable ) {
					return is_array( $deliverable ) && ! empty( $deliverable['title'] ) && ! empty( $deliverable['description'] );
				}
			)
		);
		$definition['system_context']  = array_values(
			array_filter(
				array_map(
					'strval',
					(array) ( $definition['system_context'] ?? [] )
				)
			)
		);
		$definition['priority']        = array_values(
			array_filter(
				array_map(
					'strval',
					(array) ( $definition['priority'] ?? [] )
				)
			)
		);
		$definition['related_assets']  = array_filter(
			(array) ( $definition['related_assets'] ?? [] ),
			static function ( $context ) {
				return is_string( $context ) && '' !== trim( $context );
			}
		);

		$registry[ $asset_slug ] = $definition;
	}

	return $registry;
}

/**
 * Normalize registry core-area labels to the public six-step keys.
 *
 * @param string $core_area Registry core area label.
 * @return string
 */
function hue_get_wgos_kernbereich_key( $core_area ) {
	$lookup = [
		'strategie'               => 'strategie',
		'technisches-fundament'   => 'fundament',
		'fundament'               => 'fundament',
		'messbarkeit'             => 'messbarkeit',
		'sichtbarkeit'            => 'sichtbarkeit',
		'conversion'              => 'conversion',
		'weiterentwicklung'       => 'weiterentwicklung',
	];

	$key = sanitize_title( (string) $core_area );

	return $lookup[ $key ] ?? $key;
}

/**
 * Return the public label for one WGOS core-area key.
 *
 * @param string $area Core-area key.
 * @return string
 */
function hue_kernbereich_label( $area ) {
	$labels = [
		'strategie'         => 'Strategie',
		'fundament'         => 'Fundament',
		'messbarkeit'       => 'Messbarkeit',
		'sichtbarkeit'      => 'Sichtbarkeit',
		'conversion'        => 'Conversion',
		'weiterentwicklung' => 'Weiterentwicklung',
	];

	$key = sanitize_title( (string) $area );

	return $labels[ $key ] ?? (string) $area;
}

/**
 * Check whether a registry asset is public and has a public detail route.
 *
 * @param string $slug Registry asset slug.
 * @return bool
 */
function hue_wgos_asset_exists_and_published( $slug ) {
	$definition = nexus_get_wgos_asset_definition( $slug );

	if ( empty( $definition['slug'] ) || 'publish' !== (string) ( $definition['status'] ?? '' ) ) {
		return false;
	}

	$post = nexus_get_wgos_asset_registry_post( $definition );

	return $post instanceof WP_Post && 'publish' === $post->post_status;
}

/**
 * Return the preferred public URL for a WGOS asset or a fallback target.
 *
 * @param string $slug     Registry asset slug.
 * @param string $fallback Fallback URL.
 * @return string
 */
function hue_get_wgos_asset_redirect_url( $slug, $fallback = '' ) {
	$slug = sanitize_title( (string) $slug );

	if ( '' !== $slug && hue_wgos_asset_exists_and_published( $slug ) ) {
		return trailingslashit( home_url( '/wgos-assets/' . $slug ) );
	}

	return $fallback ? $fallback : home_url( '/wordpress-agentur-hannover/#wgos' );
}

/**
 * Compatibility wrapper for page templates that need a compact public registry.
 *
 * @return array<string, array<string, mixed>>
 */
function hue_get_wgos_asset_registry() {
	$public_registry = [];

	foreach ( nexus_get_wgos_asset_registry() as $slug => $asset ) {
		$asset_slug  = sanitize_title( (string) ( $asset['slug'] ?? $slug ) );
		$status      = 'publish' === (string) ( $asset['status'] ?? '' ) ? 'published' : 'draft';
		$area_key    = hue_get_wgos_kernbereich_key( (string) ( $asset['core_area'] ?? '' ) );
		$detail_url  = 'published' === $status
			? trailingslashit( home_url( '/wgos-assets/' . $asset_slug ) )
			: nexus_get_wgos_asset_anchor_url( $asset_slug );

		$public_registry[ $asset_slug ] = array_merge(
			$asset,
			[
				'slug'        => $asset_slug,
				'status'      => $status,
				'status_raw'  => (string) ( $asset['status'] ?? '' ),
				'kernbereich' => $area_key,
				'short'       => (string) ( $asset['excerpt'] ?? '' ),
				'url'         => $detail_url,
			]
		);
	}

	return $public_registry;
}

/**
 * Build a lookup map for registry slugs, legacy slugs and titles.
 *
 * @return array<string, array<string, mixed>>
 */
function nexus_get_wgos_asset_registry_lookup() {
	static $lookup = null;

	if ( null !== $lookup ) {
		return $lookup;
	}

	$lookup = [];

	foreach ( nexus_get_wgos_asset_registry() as $asset ) {
		$candidates = array_merge(
			[
				$asset['slug'],
				$asset['title'],
			],
			(array) $asset['legacy_slugs']
		);

		foreach ( $candidates as $candidate ) {
			$key = nexus_get_wgos_asset_lookup_key( $candidate );

			if ( '' !== $key ) {
				$lookup[ $key ] = $asset;
			}
		}
	}

	return $lookup;
}

/**
 * Resolve a WGOS asset definition from slug, title or post object.
 *
 * @param string|WP_Post $value Asset identifier.
 * @return array<string, mixed>|null
 */
function nexus_get_wgos_asset_definition( $value ) {
	if ( $value instanceof WP_Post ) {
		$value = $value->post_name ? $value->post_name : $value->post_title;
	}

	$key = nexus_get_wgos_asset_lookup_key( $value );

	if ( '' === $key ) {
		return null;
	}

	$lookup = nexus_get_wgos_asset_registry_lookup();

	return $lookup[ $key ] ?? null;
}

/**
 * Build a lookup map for WGOS asset posts across public and draft states.
 *
 * @return array<string, WP_Post>
 */
function nexus_get_wgos_asset_post_lookup() {
	if ( isset( $GLOBALS['nexus_wgos_asset_post_lookup'] ) && is_array( $GLOBALS['nexus_wgos_asset_post_lookup'] ) ) {
		return $GLOBALS['nexus_wgos_asset_post_lookup'];
	}

	$lookup = [];
	$posts  = get_posts(
		[
			'post_type'              => 'wgos_asset',
			'post_status'            => [ 'publish', 'draft', 'pending', 'future', 'private' ],
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]
	);

	foreach ( $posts as $post ) {
		$slug_key = nexus_get_wgos_asset_lookup_key( $post->post_name );

		if ( '' !== $slug_key ) {
			$lookup[ $slug_key ] = $post;
		}
	}

	$GLOBALS['nexus_wgos_asset_post_lookup'] = $lookup;

	return $lookup;
}

/**
 * Resolve the stored post that belongs to a registry asset.
 *
 * @param array<string, mixed>|string|WP_Post $asset Asset definition or identifier.
 * @return WP_Post|null
 */
function nexus_get_wgos_asset_registry_post( $asset ) {
	$definition = is_array( $asset ) ? $asset : nexus_get_wgos_asset_definition( $asset );

	if ( empty( $definition['slug'] ) ) {
		return null;
	}

	$lookup     = nexus_get_wgos_asset_post_lookup();
	$candidates = array_merge(
		[ $definition['slug'] ],
		(array) ( $definition['legacy_slugs'] ?? [] )
	);

	foreach ( $candidates as $candidate ) {
		$key = nexus_get_wgos_asset_lookup_key( $candidate );

		if ( isset( $lookup[ $key ] ) ) {
			return $lookup[ $key ];
		}
	}

	return null;
}

/**
 * Resolve the public detail URL for a registry asset when available.
 *
 * @param array<string, mixed>|string|WP_Post $asset Asset definition or identifier.
 * @return string
 */
function nexus_get_wgos_asset_detail_url( $asset ) {
	$post = nexus_get_wgos_asset_registry_post( $asset );

	if ( $post instanceof WP_Post && 'publish' === $post->post_status ) {
		return (string) get_permalink( $post );
	}

	return '';
}

/**
 * Build explorer bullets from the deliverable list.
 *
 * @param array<string, mixed> $asset Asset definition.
 * @return array<int, string>
 */
function nexus_get_wgos_asset_explorer_bullets( $asset ) {
	$bullets = [];

	foreach ( (array) $asset['deliverables'] as $deliverable ) {
		$bullets[] = sprintf(
			'%1$s - %2$s',
			(string) $deliverable['title'],
			(string) $deliverable['description']
		);
	}

	return array_slice( $bullets, 0, 4 );
}

/**
 * Build the related asset card payload for an asset page.
 *
 * @param array<string, mixed> $asset Asset definition.
 * @return array<int, array<string, string>>
 */
function nexus_get_wgos_asset_related_items( $asset ) {
	$items = [];

	foreach ( (array) $asset['related_assets'] as $slug => $context ) {
		$definition = nexus_get_wgos_asset_definition( $slug );

		if ( empty( $definition['title'] ) ) {
			continue;
		}

		$items[] = [
			'title'   => (string) $definition['title'],
			'url'     => nexus_get_wgos_asset_anchor_url( (string) $definition['slug'] ),
			'context' => (string) $context,
		];
	}

	return array_slice( $items, 0, 4 );
}

/**
 * Build the HTML content body for a WGOS asset post.
 *
 * @param array<string, mixed> $asset Asset definition.
 * @return string
 */
function nexus_get_wgos_asset_content_html( $asset ) {
	$wgos_url     = nexus_get_wgos_url();
	$hub_url      = nexus_get_wgos_asset_hub_url();
	$audit_url    = nexus_get_audit_url();
	$calendar_url = function_exists( 'nexus_get_audit_calendar_url' ) ? nexus_get_audit_calendar_url() : home_url( '/kontakt/' );
	$related      = nexus_get_wgos_asset_related_items( $asset );

	ob_start();
	?>
	<section class="wgos-section wgos-section--white">
		<div class="wgos-container">
			<div class="wgos-section-head">
				<span class="wgos-principle-kicker">Asset auf einen Blick</span>
				<h2 class="wgos-h2">Kurzprofil</h2>
				<p class="wgos-section-intro">Sie sehen sofort, wo dieses Asset im System liegt, welchen Umfang es hat und was danach konkret vorliegt.</p>
			</div>
			<div class="wgos-asset-profile-shell">
				<dl class="wgos-asset-profile-grid">
					<div class="wgos-asset-profile-row">
						<dt>Kernbereich</dt>
						<dd><?php echo esc_html( (string) $asset['core_area'] ); ?></dd>
					</div>
					<div class="wgos-asset-profile-row">
						<dt>Credits</dt>
						<dd><?php echo esc_html( (string) $asset['credits'] ); ?></dd>
					</div>
					<div class="wgos-asset-profile-row">
						<dt>Ziel</dt>
						<dd><?php echo esc_html( (string) $asset['goal'] ); ?></dd>
					</div>
					<div class="wgos-asset-profile-row">
						<dt>Typisches Ergebnis</dt>
						<dd><?php echo esc_html( (string) $asset['result'] ); ?></dd>
					</div>
					<div class="wgos-asset-profile-row">
						<dt>Voraussetzung</dt>
						<dd><?php echo esc_html( (string) $asset['prerequisite'] ); ?></dd>
					</div>
				</dl>

				<aside class="wgos-note-card">
					<span class="wgos-principle-kicker">Trust-Signal</span>
					<h3>Dieses Asset ist Teil eines dokumentierten Systems.</h3>
					<p>Wir priorisieren nicht isoliert, sondern gegen Strategie, Fundament, Messbarkeit und Conversion. Die Systemlogik ist im WGOS beschrieben und im Theme versioniert.</p>
					<p class="wgos-inline-cta">
						<a href="<?php echo esc_url( $wgos_url ); ?>">WGOS Hub ansehen</a>
						<span aria-hidden="true"> / </span>
						<a href="<?php echo esc_url( $hub_url ); ?>">Zur Asset-Landkarte</a>
					</p>
				</aside>
			</div>
		</div>
	</section>

	<section class="wgos-section wgos-section--gray">
		<div class="wgos-container">
			<div class="wgos-section-head">
				<span class="wgos-principle-kicker">Problemkontext</span>
				<h2 class="wgos-h2">Warum dieses Asset existiert</h2>
			</div>
			<div class="wgos-prose">
				<?php foreach ( (array) $asset['problem'] as $paragraph ) : ?>
					<p><?php echo esc_html( $paragraph ); ?></p>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="wgos-section wgos-section--white">
		<div class="wgos-container">
			<div class="wgos-section-head">
				<span class="wgos-principle-kicker">Arbeitsumfang</span>
				<h2 class="wgos-h2">Was in diesem Asset passiert</h2>
			</div>
			<ol class="wgos-asset-step-list">
				<?php foreach ( (array) $asset['deliverables'] as $index => $deliverable ) : ?>
					<li class="wgos-asset-step">
						<span class="wgos-asset-step__index"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="wgos-asset-step__body">
							<strong><?php echo esc_html( (string) $deliverable['title'] ); ?></strong>
							<p><?php echo esc_html( (string) $deliverable['description'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="wgos-section wgos-section--gray">
		<div class="wgos-container">
			<div class="wgos-section-head">
				<span class="wgos-principle-kicker">Systemlogik</span>
				<h2 class="wgos-h2">Dieses Asset im WGOS-System</h2>
			</div>
			<div class="wgos-principle-shell">
				<div class="wgos-prose">
					<?php foreach ( (array) $asset['system_context'] as $paragraph ) : ?>
						<p><?php echo esc_html( $paragraph ); ?></p>
					<?php endforeach; ?>
				</div>
				<p class="wgos-inline-cta wgos-inline-cta--principle">
					<a href="<?php echo esc_url( $hub_url ); ?>">Dieses Asset in der Landkarte einordnen</a>
				</p>
			</div>
		</div>
	</section>

	<section class="wgos-section wgos-section--white">
		<div class="wgos-container">
			<div class="wgos-section-head">
				<span class="wgos-principle-kicker">Priorisierung</span>
				<h2 class="wgos-h2">Wann dieses Asset priorisiert wird</h2>
			</div>
			<ul class="wgos-checklist wgos-checklist--compact">
				<?php foreach ( (array) $asset['priority'] as $item ) : ?>
					<li><?php echo esc_html( $item ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="wgos-section wgos-section--gray">
		<div class="wgos-container">
			<div class="wgos-asset-cta-card">
				<span class="wgos-principle-kicker">Nächster Schritt</span>
				<h2 class="wgos-h2">Prüfen, ob dieses Asset jetzt Priorität hat.</h2>
				<p class="wgos-section-intro">Im Strategiegespräch klären wir, ob dieses Asset jetzt Priorität hat – oder ob ein anderer Baustein zuerst dran ist.</p>
				<div class="wgos-hero__actions">
					<a href="<?php echo esc_url( $calendar_url ); ?>" class="wgos-btn wgos-btn--primary" data-track-action="cta_wgos_asset_content_calendar" data-track-category="lead_gen">Strategiegespräch vereinbaren</a>
				</div>
			</div>
		</div>
	</section>

	<section class="wgos-section wgos-section--white">
		<div class="wgos-container">
			<div class="wgos-section-head">
				<span class="wgos-principle-kicker">Interne Verlinkung</span>
				<h2 class="wgos-h2">Verwandte WGOS-Bausteine</h2>
			</div>
			<div class="wgos-asset-related-grid">
				<?php foreach ( $related as $item ) : ?>
					<article class="wgos-asset-related-card">
						<h3><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a></h3>
						<p><?php echo esc_html( $item['context'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php

	return trim( (string) ob_get_clean() );
}

/**
 * Create or update the managed WGOS asset posts from the registry.
 *
 * @return array{created: array<int, string>, updated: array<int, string>, errors: array<int, string>}
 */
function nexus_sync_wgos_asset_posts() {
	$results = [
		'created' => [],
		'updated' => [],
		'errors'  => [],
	];
	$order   = 0;

	foreach ( nexus_get_wgos_asset_registry() as $asset ) {
		$order += 10;

		$existing  = nexus_get_wgos_asset_registry_post( $asset );
		$post_data = [
			'post_type'    => 'wgos_asset',
			'post_title'   => $asset['title'],
			'post_name'    => $asset['slug'],
			'post_status'  => $asset['status'],
			'post_excerpt' => $asset['excerpt'],
			'post_content' => nexus_get_wgos_asset_content_html( $asset ),
			'menu_order'   => $order,
			'post_parent'  => 0,
		];

		if ( $existing instanceof WP_Post ) {
			$post_data['ID'] = $existing->ID;
		}

		$post_id = wp_insert_post( wp_slash( $post_data ), true );

		if ( is_wp_error( $post_id ) ) {
			$results['errors'][] = sprintf( '%1$s: %2$s', $asset['title'], $post_id->get_error_message() );
			continue;
		}

		if ( $existing instanceof WP_Post ) {
			$results['updated'][] = $asset['title'];
		} else {
			$results['created'][] = $asset['title'];
		}

		update_post_meta( $post_id, 'asset_phase', $asset['phase_key'] );
		update_post_meta( $post_id, 'asset_module', $asset['core_area'] );
		update_post_meta( $post_id, 'asset_group', $asset['core_area'] );
		update_post_meta( $post_id, 'asset_credits', $asset['credits'] );
		update_post_meta( $post_id, 'asset_goal', $asset['goal'] );
		update_post_meta( $post_id, 'asset_result', $asset['result'] );
		update_post_meta( $post_id, 'asset_prerequisite', $asset['prerequisite'] );
		update_post_meta( $post_id, 'asset_keyword', $asset['keyword'] );
		update_post_meta( $post_id, 'asset_short', $asset['excerpt'] );
		update_post_meta( $post_id, 'asset_intro', $asset['problem'][0] ?? $asset['excerpt'] );
		update_post_meta( $post_id, 'asset_deliverable', $asset['result'] );
		update_post_meta( $post_id, 'asset_bullets', nexus_get_wgos_asset_explorer_bullets( $asset ) );
		update_post_meta( $post_id, 'asset_schema_type', $asset['schema_type'] );
		update_post_meta( $post_id, 'asset_registry_status', $asset['status'] );
		update_post_meta( $post_id, 'asset_related_slugs', array_keys( (array) $asset['related_assets'] ) );
		update_post_meta( $post_id, 'asset_cta_label', 'publish' === $asset['status'] ? 'Asset im Detail ansehen' : nexus_get_audit_cta_label() );
		update_post_meta( $post_id, 'asset_cta_target', 'publish' === $asset['status'] ? $asset['slug'] : 'audit' );
		update_post_meta( $post_id, 'seo_title', $asset['seo_title'] );
		update_post_meta( $post_id, 'seo_description', $asset['seo_description'] );
		update_post_meta( $post_id, '_nexus_wgos_asset_managed', '1' );
		update_post_meta( $post_id, '_nexus_wgos_asset_registry_version', nexus_get_wgos_asset_registry_version() );
	}

	unset( $GLOBALS['nexus_wgos_asset_post_lookup'] );

	return $results;
}

/**
 * Sync WGOS assets once per registry version with a transient lock.
 *
 * @return void
 */
function nexus_maybe_sync_wgos_asset_posts() {
	if ( ! apply_filters( 'nexus_wgos_asset_sync_enabled', true ) ) {
		return;
	}

	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	$version = nexus_get_wgos_asset_registry_version();

	if ( $version === get_option( 'nexus_wgos_asset_sync_version', '' ) ) {
		return;
	}

	if ( get_transient( 'nexus_wgos_asset_sync_lock' ) ) {
		return;
	}

	set_transient( 'nexus_wgos_asset_sync_lock', '1', 5 * MINUTE_IN_SECONDS );

	$results = nexus_sync_wgos_asset_posts();

	delete_transient( 'nexus_wgos_asset_sync_lock' );

	if ( empty( $results['errors'] ) ) {
		update_option( 'nexus_wgos_asset_sync_version', $version, false );
		delete_option( 'nexus_wgos_asset_sync_errors' );
		return;
	}

	update_option( 'nexus_wgos_asset_sync_errors', wp_json_encode( $results['errors'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), false );
}
add_action( 'init', 'nexus_maybe_sync_wgos_asset_posts', 30 );

/**
 * Redirect legacy WGOS asset slugs to the registry slug.
 *
 * @return void
 */
function nexus_maybe_redirect_legacy_wgos_asset_slug() {
	if ( is_admin() || ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	$request_path = trim( (string) wp_parse_url( home_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ), '/' );

	if ( 0 !== strpos( $request_path, 'wgos-assets/' ) ) {
		return;
	}

	$current_slug = sanitize_title( basename( $request_path ) );
	$definition   = nexus_get_wgos_asset_definition( $current_slug );

	if ( empty( $definition['slug'] ) || $definition['slug'] === $current_slug ) {
		return;
	}

	wp_safe_redirect( trailingslashit( home_url( '/wgos-assets/' . $definition['slug'] ) ), 301 );
	exit;
}
add_action( 'template_redirect', 'nexus_maybe_redirect_legacy_wgos_asset_slug', 1 );
