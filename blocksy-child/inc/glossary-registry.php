<?php
/**
 * Glossary registry, sync and rendering helpers.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the current glossary sync version.
 *
 * @return string
 */
function nexus_get_glossary_registry_version() {
	return '2026-03-14-glossary-v2';
}

/**
 * Return sync observability payload for one glossary post.
 *
 * Uses stored sync metadata (post meta / options), never the code constant.
 *
 * @param int|WP_Post|null $post Glossary post object or ID.
 * @return array<string, string>
 */
function nexus_get_glossary_sync_observability( $post = null ) {
	$post = get_post( $post );

	if ( ! ( $post instanceof WP_Post ) || 'glossary_term' !== $post->post_type ) {
		return [
			'registry_version'   => '',
			'post_synced_at_gmt' => '',
			'last_sync_run_gmt'  => '',
		];
	}

	$registry_version = trim( (string) get_post_meta( $post->ID, '_nexus_glossary_registry_version', true ) );
	$post_synced_at   = trim( (string) get_post_meta( $post->ID, '_nexus_glossary_synced_at_gmt', true ) );
	$last_sync_run    = trim( (string) get_option( 'nexus_glossary_sync_last_run_gmt', '' ) );

	if ( '' === $registry_version ) {
		$registry_version = trim( (string) get_option( 'nexus_glossary_sync_version', '' ) );
	}

	return [
		'registry_version'   => $registry_version,
		'post_synced_at_gmt' => $post_synced_at,
		'last_sync_run_gmt'  => $last_sync_run,
	];
}

/**
 * Return the last globally synced glossary registry version.
 *
 * @return string
 */
function nexus_get_glossary_last_synced_version() {
	return trim( (string) get_option( 'nexus_glossary_sync_version', '' ) );
}

/**
 * Return the last stored glossary routing assertion status.
 *
 * @return string
 */
function nexus_get_glossary_last_assert_status() {
	$status = trim( (string) get_option( 'nexus_glossary_last_assert_status', '' ) );

	return in_array( $status, [ 'pass', 'fail' ], true ) ? $status : '';
}

/**
 * Check whether the glossary registry changed since the last successful sync.
 *
 * @return bool
 */
function nexus_glossary_sync_required() {
	return nexus_get_glossary_registry_version() !== nexus_get_glossary_last_synced_version();
}

/**
 * Return a compact technical sync status snapshot for admin/debug views.
 *
 * @return array<string, string|bool>
 */
function nexus_get_glossary_sync_status_snapshot() {
	$last_synced_version = nexus_get_glossary_last_synced_version();

	return [
		'current_registry_version'     => nexus_get_glossary_registry_version(),
		'last_synced_registry_version' => $last_synced_version,
		'sync_ever_ran'                => '' !== $last_synced_version,
		'last_sync_time_gmt'           => trim( (string) get_option( 'nexus_glossary_sync_last_run_gmt', '' ) ),
		'last_assert_status'           => nexus_get_glossary_last_assert_status(),
		'last_assert_time_gmt'         => trim( (string) get_option( 'nexus_glossary_last_assert_time', '' ) ),
		'sync_required'                => nexus_glossary_sync_required(),
	];
}

/**
 * Format a stored glossary sync timestamp for compact debug output.
 *
 * @param string $timestamp_gmt Timestamp string in GMT.
 * @return string
 */
function nexus_format_glossary_sync_timestamp( $timestamp_gmt ) {
	$timestamp_gmt = trim( (string) $timestamp_gmt );

	if ( '' === $timestamp_gmt ) {
		return 'n/a';
	}

	$timestamp = strtotime( $timestamp_gmt );

	if ( false === $timestamp ) {
		return $timestamp_gmt;
	}

	return gmdate( 'Y-m-d H:i:s', $timestamp ) . ' UTC';
}

/**
 * Load the glossary registry from the versioned data file.
 *
 * @return array<string, array<string, mixed>>
 */
function nexus_get_glossary_registry() {
	static $registry = null;

	if ( null !== $registry ) {
		return $registry;
	}

	$definitions = require __DIR__ . '/glossary-registry-data.php';
	$areas       = nexus_get_glossary_area_catalog();
	$registry    = [];

	foreach ( (array) $definitions as $slug => $definition ) {
		if ( ! is_array( $definition ) ) {
			continue;
		}

		$term_slug    = sanitize_title( $definition['slug'] ?? $slug );
		$core_area    = isset( $definition['core_area'] ) ? (string) $definition['core_area'] : 'Strategie';
		$index_policy = isset( $definition['index_policy'] ) ? sanitize_key( (string) $definition['index_policy'] ) : 'index';

		if ( ! isset( $areas[ $core_area ] ) ) {
			$core_area = 'Strategie';
		}

		if ( ! in_array( $index_policy, [ 'index', 'noindex', 'alias' ], true ) ) {
			$index_policy = 'index';
		}

		$definition['slug']               = $term_slug;
		$definition['title']              = isset( $definition['title'] ) ? (string) $definition['title'] : ucfirst( str_replace( '-', ' ', $term_slug ) );
		$definition['status']             = 'publish' === ( $definition['status'] ?? '' ) ? 'publish' : 'draft';
		$definition['core_area']          = $core_area;
		$definition['index_policy']       = $index_policy;
		$definition['excerpt']            = isset( $definition['excerpt'] ) ? (string) $definition['excerpt'] : '';
		$definition['short_definition']   = isset( $definition['short_definition'] ) ? (string) $definition['short_definition'] : $definition['excerpt'];
		$definition['why_it_matters']     = isset( $definition['why_it_matters'] ) ? (string) $definition['why_it_matters'] : '';
		$definition['measurement']        = isset( $definition['measurement'] ) ? (string) $definition['measurement'] : '';
		$definition['primary_url_key']    = isset( $definition['primary_url_key'] ) ? sanitize_key( (string) $definition['primary_url_key'] ) : '';
		$definition['primary_url_label']  = isset( $definition['primary_url_label'] ) ? (string) $definition['primary_url_label'] : '';
		$definition['primary_url_reason'] = isset( $definition['primary_url_reason'] ) ? (string) $definition['primary_url_reason'] : '';
		$definition['seo_title']          = hu_normalize_brand_text( isset( $definition['seo_title'] ) ? (string) $definition['seo_title'] : $definition['title'] . ' | Haşim Üner' );
		$definition['seo_description']    = isset( $definition['seo_description'] ) ? (string) $definition['seo_description'] : $definition['excerpt'];
		$definition['legacy_slugs']       = array_values(
			array_filter(
				array_map(
					'sanitize_title',
					(array) ( $definition['legacy_slugs'] ?? [] )
				)
			)
		);
		$definition['mistakes']           = array_values(
			array_filter(
				array_map(
					'strval',
					(array) ( $definition['mistakes'] ?? [] )
				)
			)
		);
		$definition['wgos_context']       = array_values(
			array_filter(
				array_map(
					'strval',
					(array) ( $definition['wgos_context'] ?? [] )
				)
			)
		);
		$definition['related_terms']      = array_values(
			array_filter(
				array_map(
					'sanitize_title',
					(array) ( $definition['related_terms'] ?? [] )
				)
			)
		);
		$definition['benchmarks']         = array_values(
			array_filter(
				(array) ( $definition['benchmarks'] ?? [] ),
				static function ( $item ) {
					return is_array( $item ) && ! empty( $item['label'] ) && ! empty( $item['value'] );
				}
			)
		);

		$definition['related_primary_urls'] = array_values(
			array_filter(
				array_map(
					static function ( $item ) {
						if ( ! is_array( $item ) || empty( $item['key'] ) ) {
							return null;
						}

						return [
							'key'      => sanitize_key( (string) $item['key'] ),
							'label'    => isset( $item['label'] ) ? (string) $item['label'] : '',
							'reason'   => isset( $item['reason'] ) ? (string) $item['reason'] : '',
							'fallback' => isset( $item['fallback'] ) ? (string) $item['fallback'] : '',
						];
					},
					(array) ( $definition['related_primary_urls'] ?? [] )
				)
			)
		);

		$definition['related_assets'] = array_values(
			array_filter(
				array_map(
					static function ( $key, $reason ) {
						if ( ! is_string( $key ) || '' === trim( $key ) ) {
							return null;
						}

						return [
							'slug'   => sanitize_title( $key ),
							'reason' => is_string( $reason ) ? trim( $reason ) : '',
						];
					},
					array_keys( (array) ( $definition['related_assets'] ?? [] ) ),
					array_values( (array) ( $definition['related_assets'] ?? [] ) )
				)
			)
		);

		$registry[ $term_slug ] = $definition;
	}

	return $registry;
}

/**
 * Build a lookup table for glossary registry slugs, legacy slugs and titles.
 *
 * @return array<string, array<string, mixed>>
 */
function nexus_get_glossary_registry_lookup() {
	static $lookup = null;

	if ( null !== $lookup ) {
		return $lookup;
	}

	$lookup = [];

	foreach ( nexus_get_glossary_registry() as $term ) {
		$candidates = array_merge(
			[
				$term['slug'],
				$term['title'],
			],
			(array) $term['legacy_slugs']
		);

		foreach ( $candidates as $candidate ) {
			$key = nexus_get_glossary_lookup_key( $candidate );

			if ( '' !== $key ) {
				$lookup[ $key ] = $term;
			}
		}
	}

	return $lookup;
}

/**
 * Resolve a glossary term definition from slug, title or post object.
 *
 * @param string|WP_Post $value Term identifier.
 * @return array<string, mixed>|null
 */
function nexus_get_glossary_definition( $value ) {
	if ( $value instanceof WP_Post ) {
		$value = $value->post_name ? $value->post_name : $value->post_title;
	}

	$key = nexus_get_glossary_lookup_key( $value );

	if ( '' === $key ) {
		return null;
	}

	$lookup = nexus_get_glossary_registry_lookup();

	return $lookup[ $key ] ?? null;
}

/**
 * Build a lookup table for glossary posts across all non-trash states.
 *
 * @return array<string, WP_Post>
 */
function nexus_get_glossary_post_lookup() {
	if ( isset( $GLOBALS['nexus_glossary_post_lookup'] ) && is_array( $GLOBALS['nexus_glossary_post_lookup'] ) ) {
		return $GLOBALS['nexus_glossary_post_lookup'];
	}

	$lookup = [];
	$posts  = get_posts(
		[
			'post_type'              => 'glossary_term',
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
		$slug_key = nexus_get_glossary_lookup_key( $post->post_name );

		if ( '' !== $slug_key ) {
			$lookup[ $slug_key ] = $post;
		}
	}

	$GLOBALS['nexus_glossary_post_lookup'] = $lookup;

	return $lookup;
}

/**
 * Resolve the stored post that belongs to a registry term.
 *
 * @param array<string, mixed>|string|WP_Post $term Term definition or identifier.
 * @return WP_Post|null
 */
function nexus_get_glossary_registry_post( $term ) {
	$definition = is_array( $term ) ? $term : nexus_get_glossary_definition( $term );

	if ( empty( $definition['slug'] ) ) {
		return null;
	}

	$lookup     = nexus_get_glossary_post_lookup();
	$candidates = array_merge(
		[ $definition['slug'] ],
		(array) ( $definition['legacy_slugs'] ?? [] )
	);

	foreach ( $candidates as $candidate ) {
		$key = nexus_get_glossary_lookup_key( $candidate );

		if ( '' !== $key && isset( $lookup[ $key ] ) ) {
			return $lookup[ $key ];
		}
	}

	return null;
}

/**
 * Resolve the primary URL for one glossary term.
 *
 * @param array<string, mixed>|string|WP_Post $term Term definition or identifier.
 * @return string
 */
function nexus_get_glossary_primary_url( $term ) {
	$definition = is_array( $term ) ? $term : nexus_get_glossary_definition( $term );

	if ( ! is_array( $definition ) || empty( $definition['primary_url_key'] ) ) {
		return '';
	}

	return nexus_get_primary_public_url( (string) $definition['primary_url_key'] );
}

/**
 * Resolve the published detail URL for one glossary term.
 *
 * @param array<string, mixed>|string|WP_Post $term Term definition or identifier.
 * @return string
 */
function nexus_get_glossary_term_detail_url( $term ) {
	$definition = is_array( $term ) ? $term : nexus_get_glossary_definition( $term );

	if ( ! is_array( $definition ) ) {
		return '';
	}

	if ( 'alias' === (string) $definition['index_policy'] ) {
		return nexus_get_glossary_primary_url( $definition );
	}

	$post = nexus_get_glossary_registry_post( $definition );

	if ( $post instanceof WP_Post && 'publish' === $post->post_status ) {
		return get_permalink( $post );
	}

	return '';
}

/**
 * Resolve related primary URL cards for one glossary term.
 *
 * @param array<string, mixed> $term Term definition.
 * @return array<int, array<string, string>>
 */
function nexus_get_glossary_related_primary_items( $term ) {
	$items = [];

	foreach ( (array) $term['related_primary_urls'] as $item ) {
		$key = isset( $item['key'] ) ? sanitize_key( (string) $item['key'] ) : '';

		if ( '' === $key ) {
			continue;
		}

		$url = nexus_get_primary_public_url( $key, (string) ( $item['fallback'] ?? '' ) );

		if ( '' === $url ) {
			continue;
		}

		$items[] = [
			'label'  => '' !== (string) $item['label'] ? (string) $item['label'] : ucwords( str_replace( '_', ' ', $key ) ),
			'url'    => $url,
			'reason' => isset( $item['reason'] ) ? (string) $item['reason'] : '',
		];
	}

	return $items;
}

/**
 * Resolve related WGOS asset cards for one glossary term.
 *
 * @param array<string, mixed> $term Term definition.
 * @return array<int, array{slug: string, label: string, url: string, reason: string}>
 */
function nexus_get_glossary_related_asset_items( $term ) {
	$items = [];

	foreach ( (array) $term['related_assets'] as $asset ) {
		if ( ! is_array( $asset ) || empty( $asset['slug'] ) ) {
			continue;
		}

		$definition = function_exists( 'nexus_get_wgos_asset_definition' ) ? nexus_get_wgos_asset_definition( (string) $asset['slug'] ) : null;
		$url        = function_exists( 'nexus_get_wgos_asset_detail_url' ) ? nexus_get_wgos_asset_detail_url( (string) $asset['slug'] ) : '';

		if ( '' === $url && function_exists( 'nexus_get_wgos_asset_anchor_url' ) ) {
			$url = nexus_get_wgos_asset_anchor_url( (string) $asset['slug'] );
		}

		if ( '' === $url ) {
			continue;
		}

		$items[] = [
			'slug'   => (string) $asset['slug'],
			'label'  => is_array( $definition ) && ! empty( $definition['title'] ) ? (string) $definition['title'] : ucwords( str_replace( '-', ' ', (string) $asset['slug'] ) ),
			'url'    => $url,
			'reason' => isset( $asset['reason'] ) ? (string) $asset['reason'] : '',
		];
	}

	return $items;
}

/**
 * Resolve related glossary term cards for one glossary term.
 *
 * @param array<string, mixed> $term Term definition.
 * @return array<int, array<string, string>>
 */
function nexus_get_glossary_related_term_items( $term ) {
	$items = [];

	foreach ( (array) $term['related_terms'] as $slug ) {
		$definition = nexus_get_glossary_definition( $slug );

		if ( ! is_array( $definition ) || 'publish' !== (string) ( $definition['status'] ?? '' ) ) {
			continue;
		}

		$destination = nexus_get_glossary_term_destination( $definition );

		$items[] = [
			'label'  => (string) $definition['title'],
			'url'    => (string) $destination['url'],
			'reason' => (string) $definition['excerpt'],
		];
	}

	return $items;
}

/**
 * Build the rendered HTML for one glossary term.
 *
 * @param array<string, mixed> $term Term definition.
 * @return string
 */
function nexus_get_glossary_term_content_html( $term ) {
	$hub_url          = nexus_get_glossary_hub_url();
	$audit_url        = nexus_get_primary_public_url( 'audit', home_url( '/anfrage-system-analyse/' ) );
	$primary_url      = nexus_get_glossary_primary_url( $term );
	$primary_items    = nexus_get_glossary_related_primary_items( $term );
	$asset_items      = nexus_get_glossary_related_asset_items( $term );
	$related_terms    = nexus_get_glossary_related_term_items( $term );
	$area_catalog     = nexus_get_glossary_area_catalog();
	$policy_catalog   = nexus_get_glossary_policy_catalog();
	$core_area        = isset( $term['core_area'] ) ? (string) $term['core_area'] : 'Strategie';
	$policy           = isset( $term['index_policy'] ) ? (string) $term['index_policy'] : 'index';
	$area             = $area_catalog[ $core_area ] ?? reset( $area_catalog );
	$policy_data      = $policy_catalog[ $policy ] ?? $policy_catalog['index'];

	ob_start();
	?>
	<section class="wgos-section wgos-section--white glossary-detail-intro">
		<div class="wgos-container">
			<div class="glossary-detail-grid">
				<article class="glossary-card">
					<span class="glossary-card__kicker">Kurzdefinition</span>
					<p class="glossary-card__lead"><?php echo esc_html( (string) $term['short_definition'] ); ?></p>
					<?php if ( '' !== trim( (string) $term['why_it_matters'] ) ) : ?>
						<p><?php echo esc_html( (string) $term['why_it_matters'] ); ?></p>
					<?php endif; ?>
				</article>

				<aside class="glossary-card glossary-card--accent" style="--glossary-accent: <?php echo esc_attr( (string) $area['accent'] ); ?>;">
					<span class="glossary-card__kicker">Einordnung</span>
					<dl class="glossary-facts">
						<div>
							<dt>Kernbereich</dt>
							<dd><?php echo esc_html( $core_area ); ?></dd>
						</div>
						<div>
							<dt>Index-Policy</dt>
							<dd><?php echo esc_html( (string) $policy_data['label'] ); ?></dd>
						</div>
						<?php if ( '' !== $primary_url ) : ?>
							<div>
								<dt>Primary URL</dt>
								<dd><a href="<?php echo esc_url( $primary_url ); ?>"><?php echo esc_html( (string) $term['primary_url_label'] ); ?></a></dd>
							</div>
						<?php endif; ?>
					</dl>
					<?php if ( '' !== trim( (string) $term['primary_url_reason'] ) ) : ?>
						<p class="glossary-card__micro"><?php echo esc_html( (string) $term['primary_url_reason'] ); ?></p>
					<?php endif; ?>
				</aside>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $term['benchmarks'] ) || '' !== trim( (string) $term['measurement'] ) ) : ?>
		<section class="wgos-section wgos-section--gray">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Messung und Bewertung</span>
					<h2 class="wgos-h2">Woran Sie den Begriff praktisch erkennen.</h2>
					<?php if ( '' !== trim( (string) $term['measurement'] ) ) : ?>
						<p class="wgos-section-intro"><?php echo esc_html( (string) $term['measurement'] ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $term['benchmarks'] ) ) : ?>
					<div class="glossary-benchmark-grid">
						<?php foreach ( (array) $term['benchmarks'] as $benchmark ) : ?>
							<article class="glossary-benchmark-card">
								<span class="glossary-benchmark-card__label"><?php echo esc_html( (string) $benchmark['label'] ); ?></span>
								<strong><?php echo esc_html( (string) $benchmark['value'] ); ?></strong>
								<?php if ( ! empty( $benchmark['note'] ) ) : ?>
									<p><?php echo esc_html( (string) $benchmark['note'] ); ?></p>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! empty( $term['mistakes'] ) ) : ?>
		<section class="wgos-section wgos-section--white">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Typische Fehler</span>
					<h2 class="wgos-h2">Wo Begriffsverständnis und Umsetzung oft auseinanderlaufen.</h2>
				</div>
				<ul class="glossary-checklist">
					<?php foreach ( (array) $term['mistakes'] as $mistake ) : ?>
						<li><?php echo esc_html( (string) $mistake ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endif; ?>

	<section class="wgos-section wgos-section--gray">
		<div class="wgos-container">
			<div class="wgos-section-head">
				<span class="wgos-principle-kicker">Im WGOS-Kontext</span>
				<h2 class="wgos-h2">Der Begriff ist nur dann sinnvoll, wenn er auf die richtige Primary URL zurückführt.</h2>
			</div>

			<div class="glossary-context-grid">
				<div class="glossary-card">
					<?php foreach ( (array) $term['wgos_context'] as $paragraph ) : ?>
						<p><?php echo esc_html( (string) $paragraph ); ?></p>
					<?php endforeach; ?>
				</div>

				<div class="glossary-card">
					<span class="glossary-card__kicker">Nächste sinnvolle Seiten</span>
					<div class="glossary-link-stack">
						<?php foreach ( $primary_items as $item ) : ?>
							<a class="glossary-link-card" href="<?php echo esc_url( $item['url'] ); ?>">
								<strong><?php echo esc_html( $item['label'] ); ?></strong>
								<?php if ( '' !== trim( $item['reason'] ) ) : ?>
									<span><?php echo esc_html( $item['reason'] ); ?></span>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>

						<?php foreach ( $asset_items as $item ) : ?>
							<a class="glossary-link-card glossary-link-card--asset" href="<?php echo esc_url( $item['url'] ); ?>">
								<strong><?php echo esc_html( $item['label'] ); ?></strong>
								<?php if ( '' !== trim( $item['reason'] ) ) : ?>
									<span><?php echo esc_html( $item['reason'] ); ?></span>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $related_terms ) ) : ?>
		<section class="wgos-section wgos-section--white">
			<div class="wgos-container">
				<div class="wgos-section-head">
					<span class="wgos-principle-kicker">Verwandte Begriffe</span>
					<h2 class="wgos-h2">Weitere Einträge, die direkt anschließen.</h2>
				</div>
				<div class="glossary-related-grid">
					<?php foreach ( $related_terms as $item ) : ?>
						<a class="glossary-related-card" href="<?php echo esc_url( $item['url'] ); ?>">
							<strong><?php echo esc_html( $item['label'] ); ?></strong>
							<span><?php echo esc_html( $item['reason'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="wgos-section wgos-section--white wgos-final-cta">
		<div class="wgos-container">
			<div class="wgos-final-cta__inner">
				<span class="wgos-principle-kicker">Nächster Schritt</span>
				<h2 class="wgos-h2">Begriff verstanden. Jetzt die richtige Priorität für Ihre Website setzen.</h2>
				<p class="wgos-prose">Wenn klar ist, was der Begriff bedeutet, bleibt die eigentliche Frage offen: Ist das Thema für Ihre Website gerade wirklich der Engpass oder nur ein Symptom? Die System-Diagnose bringt die Reihenfolge zurück.</p>
				<div class="wgos-hero__actions">
					<a href="<?php echo esc_url( $hub_url ); ?>" class="wgos-btn wgos-btn--outline">Zurück zum Glossar</a>
					<a href="<?php echo esc_url( $audit_url ); ?>" class="wgos-btn wgos-btn--primary" data-track-action="cta_glossary_term_audit" data-track-category="lead_gen"><?php echo esc_html( nexus_get_audit_cta_label() ); ?></a>
				</div>
			</div>
		</div>
	</section>
	<?php

	return trim( (string) ob_get_clean() );
}

/**
 * Check whether a registry term should get its own synced post.
 *
 * @param array<string, mixed> $term Term definition.
 * @return bool
 */
function nexus_glossary_term_requires_post( $term ) {
	return 'alias' !== (string) ( $term['index_policy'] ?? 'index' );
}

/**
 * Sync glossary registry definitions into glossary_term posts.
 *
 * @return array<string, mixed>
 */
function nexus_sync_glossary_term_posts() {
	$results      = [
		'created' => 0,
		'updated' => 0,
		'skipped' => 0,
		'errors'  => [],
	];
	$menu_order   = 0;
	$active_slugs = [];
	$synced_at_gmt = gmdate( 'c' );

	foreach ( nexus_get_glossary_registry() as $term ) {
		if ( ! nexus_glossary_term_requires_post( $term ) ) {
			continue;
		}

		++$menu_order;
		$active_slugs[] = (string) $term['slug'];
		$existing       = nexus_get_glossary_registry_post( $term );
		$postarr        = [
			'post_type'    => 'glossary_term',
			'post_status'  => (string) $term['status'],
			'post_title'   => (string) $term['title'],
			'post_name'    => (string) $term['slug'],
			'post_excerpt' => (string) $term['excerpt'],
			'post_content' => nexus_get_glossary_term_content_html( $term ),
			'menu_order'   => $menu_order,
		];

		if ( $existing instanceof WP_Post ) {
			$postarr['ID'] = (int) $existing->ID;
			$post_id       = wp_update_post( wp_slash( $postarr ), true );
			$action        = 'updated';
		} else {
			$post_id = wp_insert_post( wp_slash( $postarr ), true );
			$action  = 'created';
		}

		if ( is_wp_error( $post_id ) ) {
			$results['errors'][] = $post_id->get_error_message();
			continue;
		}

		$post_id = (int) $post_id;

		update_post_meta( $post_id, 'seo_title', (string) $term['seo_title'] );
		update_post_meta( $post_id, 'seo_description', (string) $term['seo_description'] );
		update_post_meta( $post_id, '_nexus_glossary_managed', '1' );
		update_post_meta( $post_id, '_nexus_glossary_registry_version', nexus_get_glossary_registry_version() );
		update_post_meta( $post_id, '_nexus_glossary_synced_at_gmt', $synced_at_gmt );
		update_post_meta( $post_id, '_nexus_glossary_index_policy', (string) $term['index_policy'] );
		update_post_meta( $post_id, '_nexus_glossary_core_area', (string) $term['core_area'] );

		// Legacy: rank_math_robots meta is still read by seo-meta.php as noindex
		// fallback. Write it here so existing noindex logic keeps working.
		if ( 'noindex' === (string) $term['index_policy'] ) {
			update_post_meta( $post_id, 'rank_math_robots', [ 'noindex' ] );
		} else {
			delete_post_meta( $post_id, 'rank_math_robots' );
		}

		++$results[ $action ];
	}

	$managed_posts = get_posts(
		[
			'post_type'              => 'glossary_term',
			'post_status'            => [ 'publish', 'draft', 'pending', 'future', 'private' ],
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_key'               => '_nexus_glossary_managed',
			'meta_value'             => '1',
		]
	);

	foreach ( $managed_posts as $managed_post_id ) {
		$managed_post_id = (int) $managed_post_id;
		$managed_slug    = (string) get_post_field( 'post_name', $managed_post_id );

		if ( in_array( $managed_slug, $active_slugs, true ) ) {
			continue;
		}

		wp_update_post(
			[
				'ID'          => $managed_post_id,
				'post_status' => 'draft',
			]
		);
	}

	unset( $GLOBALS['nexus_glossary_post_lookup'], $GLOBALS['nexus_glossary_term_lookup_map'] );

	return $results;
}

/**
 * Persist the latest routing assertion result after a successful sync.
 *
 * @return array{status: string, time: string, report: array<string, mixed>}
 */
function nexus_run_glossary_routing_assertions_after_sync() {
	$report = nexus_get_glossary_routing_assertions_report();
	$status = ! empty( $report['pass'] ) ? 'pass' : 'fail';
	$time   = gmdate( 'c' );

	update_option( 'nexus_glossary_last_assert_status', $status, false );
	update_option( 'nexus_glossary_last_assert_time', $time, false );

	return [
		'status' => $status,
		'time'   => $time,
		'report' => $report,
	];
}

/**
 * Write a compact sync log entry for later debugging.
 *
 * @param string               $registry_version Active registry version.
 * @param array<string, mixed> $results         Sync result counters.
 * @param string               $assert_status   pass|fail
 * @return void
 */
function nexus_log_glossary_sync_event( $registry_version, $results, $assert_status ) {
	if ( ! function_exists( 'error_log' ) ) {
		return;
	}

	$payload = [
		'registry_version' => (string) $registry_version,
		'updated_posts'    => (int) ( $results['updated'] ?? 0 ),
		'created_posts'    => (int) ( $results['created'] ?? 0 ),
		'assertion_status' => (string) $assert_status,
	];

	error_log( '[Nexus Glossary Sync] ' . wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
}

/**
 * Sync glossary posts once per registry version.
 *
 * @return void
 */
function nexus_maybe_sync_glossary_term_posts() {
	if ( ! apply_filters( 'nexus_glossary_sync_enabled', true ) ) {
		return;
	}

	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version = nexus_get_glossary_registry_version();

	if ( $version === get_option( 'nexus_glossary_sync_version', '' ) ) {
		// Version already up-to-date. Backfill assertion data if it was never recorded
		// (e.g. after a registry version bump that pre-dated the observability layer).
		$assert_missing = '' === nexus_get_glossary_last_assert_status()
		               || '' === trim( (string) get_option( 'nexus_glossary_last_assert_time', '' ) );

		if ( $assert_missing && ! get_transient( 'nexus_glossary_sync_lock' ) ) {
			set_transient( 'nexus_glossary_sync_lock', '1', 5 * MINUTE_IN_SECONDS );
			nexus_run_glossary_routing_assertions_after_sync();
			delete_transient( 'nexus_glossary_sync_lock' );
		}

		return;
	}

	if ( get_transient( 'nexus_glossary_sync_lock' ) ) {
		return;
	}

	set_transient( 'nexus_glossary_sync_lock', '1', 5 * MINUTE_IN_SECONDS );

	$results = nexus_sync_glossary_term_posts();

	delete_transient( 'nexus_glossary_sync_lock' );

	if ( empty( $results['errors'] ) ) {
		update_option( 'nexus_glossary_sync_version', $version, false );
		update_option( 'nexus_glossary_sync_last_run_gmt', gmdate( 'c' ), false );
		delete_option( 'nexus_glossary_sync_errors' );
		$assertion = nexus_run_glossary_routing_assertions_after_sync();
		nexus_log_glossary_sync_event( $version, $results, (string) $assertion['status'] );
		return;
	}

	update_option( 'nexus_glossary_sync_errors', wp_json_encode( $results['errors'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), false );
}
add_action( 'init', 'nexus_maybe_sync_glossary_term_posts', 31 );

/**
 * Print sync observability meta tags on glossary detail pages.
 *
 * @return void
 */
function nexus_output_glossary_sync_observability_meta() {
	if ( ! is_singular( 'glossary_term' ) ) {
		return;
	}

	$payload = nexus_get_glossary_sync_observability( get_queried_object_id() );

	if ( '' !== $payload['registry_version'] ) {
		printf(
			'<meta name="nexus-glossary-registry-version" content="%s">' . "\n",
			esc_attr( $payload['registry_version'] )
		);
	}

	if ( '' !== $payload['post_synced_at_gmt'] ) {
		printf(
			'<meta name="nexus-glossary-post-synced-at" content="%s">' . "\n",
			esc_attr( $payload['post_synced_at_gmt'] )
		);
	}

	if ( '' !== $payload['last_sync_run_gmt'] ) {
		printf(
			'<meta name="nexus-glossary-sync-last-run" content="%s">' . "\n",
			esc_attr( $payload['last_sync_run_gmt'] )
		);
	}
}
add_action( 'wp_head', 'nexus_output_glossary_sync_observability_meta', 5 );

/**
 * Add sync observability response headers on glossary detail pages.
 *
 * @return void
 */
function nexus_send_glossary_sync_observability_headers() {
	if ( ! is_singular( 'glossary_term' ) || headers_sent() ) {
		return;
	}

	$payload = nexus_get_glossary_sync_observability( get_queried_object_id() );
	$clean   = static function ( $value ) {
		return str_replace( [ "\r", "\n" ], '', (string) $value );
	};

	if ( '' !== $payload['registry_version'] ) {
		header( 'X-Nexus-Glossary-Registry-Version: ' . $clean( $payload['registry_version'] ) );
	}

	if ( '' !== $payload['post_synced_at_gmt'] ) {
		header( 'X-Nexus-Glossary-Post-Synced-At: ' . $clean( $payload['post_synced_at_gmt'] ) );
	}

	if ( '' !== $payload['last_sync_run_gmt'] ) {
		header( 'X-Nexus-Glossary-Sync-Last-Run: ' . $clean( $payload['last_sync_run_gmt'] ) );
	}
}
add_action( 'send_headers', 'nexus_send_glossary_sync_observability_headers' );

/**
 * Run low-level assertions for glossary and WGOS destination rules.
 *
 * @return array<string, mixed>
 */
function nexus_get_glossary_routing_assertions_report() {
	$report    = [
		'generated_at_gmt' => gmdate( 'c' ),
		'total'            => 0,
		'failed'           => 0,
		'pass'             => true,
		'failures'         => [],
	];
	$wgos_hub_url = function_exists( 'nexus_get_wgos_url' ) ? trailingslashit( nexus_get_wgos_url() ) : trailingslashit( home_url( '/wordpress-growth-operating-system/' ) );
	$terms        = nexus_get_glossary_registry();

	$assert = static function ( $condition, $code, $message, $context = [] ) use ( &$report ) {
		$report['total'] += 1;

		if ( $condition ) {
			return;
		}

		$report['failed']   += 1;
		$report['pass']      = false;
		$report['failures'][] = [
			'code'    => $code,
			'message' => $message,
			'context' => $context,
		];
	};

	foreach ( $terms as $term ) {
		if ( 'publish' !== (string) ( $term['status'] ?? '' ) ) {
			continue;
		}

		$policy      = (string) ( $term['index_policy'] ?? 'index' );
		$destination = nexus_get_glossary_term_destination( $term );
		$detail_url  = nexus_get_glossary_term_detail_url( $term );
		$primary_url = nexus_get_glossary_primary_url( $term );
		$term_slug   = (string) ( $term['slug'] ?? '' );

		// A) index / noindex / alias destination behaviour.
		if ( 'alias' === $policy ) {
			$assert(
				'' !== $primary_url && $destination['url'] === $primary_url,
				'glossary_alias_destination',
				'Alias term must resolve to the primary URL.',
				[
					'term'        => $term_slug,
					'destination' => (string) $destination['url'],
					'primary_url' => $primary_url,
				]
			);
		} elseif ( 'noindex' === $policy ) {
			$expected_url = '' !== $detail_url ? $detail_url : nexus_get_glossary_hub_url();

			$assert(
				$destination['url'] === $expected_url,
				'glossary_noindex_destination',
				'Noindex term must resolve to detail URL or glossary fallback.',
				[
					'term'         => $term_slug,
					'destination'  => (string) $destination['url'],
					'expected_url' => $expected_url,
				]
			);
		} else {
			$assert(
				'' !== $detail_url,
				'glossary_index_has_detail',
				'Index term must resolve to a published glossary detail URL.',
				[
					'term'       => $term_slug,
					'detail_url' => $detail_url,
				]
			);
			$assert(
				$destination['url'] === $detail_url,
				'glossary_index_destination',
				'Index term destination must equal glossary detail URL.',
				[
					'term'        => $term_slug,
					'destination' => (string) $destination['url'],
					'detail_url'  => $detail_url,
				]
			);
		}

		// B + D) Related terms must follow central destination logic and avoid WGOS pillar drift.
		$related_items_by_label = [];

		foreach ( nexus_get_glossary_related_term_items( $term ) as $item ) {
			$related_items_by_label[ (string) $item['label'] ] = (string) $item['url'];
		}

		foreach ( (array) ( $term['related_terms'] ?? [] ) as $related_slug ) {
			$related_definition = nexus_get_glossary_definition( (string) $related_slug );

			if ( ! is_array( $related_definition ) || 'publish' !== (string) ( $related_definition['status'] ?? '' ) ) {
				continue;
			}

			$related_destination = nexus_get_glossary_term_destination( $related_definition );
			$related_label       = (string) $related_definition['title'];
			$related_url         = $related_items_by_label[ $related_label ] ?? '';

			$assert(
				$related_url === (string) $related_destination['url'],
				'related_terms_destination_logic',
				'Related term card must use central glossary destination logic.',
				[
					'term'         => $term_slug,
					'related_term' => (string) $related_definition['slug'],
					'card_url'     => $related_url,
					'expected_url' => (string) $related_destination['url'],
				]
			);

			$related_policy = (string) ( $related_definition['index_policy'] ?? 'index' );
			$related_detail = nexus_get_glossary_term_detail_url( $related_definition );

			if ( 'alias' !== $related_policy && '' !== $related_detail ) {
				$assert(
					0 !== strpos( trailingslashit( $related_url ), $wgos_hub_url ),
					'related_terms_no_wgos_pillar_drift',
					'Related glossary terms must not drift to WGOS pillar when a glossary detail exists.',
					[
						'term'         => $term_slug,
						'related_term' => (string) $related_definition['slug'],
						'card_url'     => $related_url,
						'wgos_hub_url' => $wgos_hub_url,
					]
				);
			}
		}

		// C) WGOS asset routing must resolve to detail page or clean anchor fallback.
		$related_asset_items = nexus_get_glossary_related_asset_items( $term );
		$asset_items_by_slug = [];

		foreach ( $related_asset_items as $item ) {
			if ( ! empty( $item['slug'] ) ) {
				$asset_items_by_slug[ (string) $item['slug'] ] = (string) $item['url'];
			}
		}

		foreach ( (array) ( $term['related_assets'] ?? [] ) as $asset ) {
			if ( ! is_array( $asset ) || empty( $asset['slug'] ) ) {
				continue;
			}

			$asset_slug   = (string) $asset['slug'];
			$detail_url   = function_exists( 'nexus_get_wgos_asset_detail_url' ) ? nexus_get_wgos_asset_detail_url( $asset_slug ) : '';
			$fallback_url = function_exists( 'nexus_get_wgos_asset_anchor_url' ) ? nexus_get_wgos_asset_anchor_url( $asset_slug ) : '';
			$expected_url = '' !== $detail_url ? $detail_url : $fallback_url;
			$actual_url   = $asset_items_by_slug[ $asset_slug ] ?? '';

			$assert(
				$actual_url === $expected_url,
				'wgos_asset_destination_fallback',
				'Related WGOS asset link must resolve to detail URL or anchor fallback.',
				[
					'term'         => $term_slug,
					'asset_slug'   => $asset_slug,
					'asset_url'    => $actual_url,
					'expected_url' => $expected_url,
				]
			);
		}
	}

	return $report;
}

/**
 * Provide routing assertions as JSON for authenticated QA checks.
 *
 * URL: /?nexus_glossary_routing_assert=1
 *
 * @return void
 */
function nexus_maybe_output_glossary_routing_assertions() {
	$is_assert_request = isset( $_GET['nexus_glossary_routing_assert'] ) && '1' === sanitize_text_field( wp_unslash( (string) $_GET['nexus_glossary_routing_assert'] ) );

	if ( is_admin() || ! $is_assert_request ) {
		return;
	}

	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		status_header( 403 );
		wp_die( esc_html__( 'Forbidden', 'blocksy-child' ) );
	}

	nocache_headers();
	wp_send_json( nexus_get_glossary_routing_assertions_report() );
}
add_action( 'template_redirect', 'nexus_maybe_output_glossary_routing_assertions', 0 );

/**
 * Render glossary sync safety notices in wp-admin.
 *
 * @return void
 */
function nexus_render_glossary_sync_admin_notices() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$snapshot = nexus_get_glossary_sync_status_snapshot();

	if ( ! empty( $snapshot['sync_required'] ) ) {
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			esc_html__( 'Glossary Sync erforderlich – Registry-Version wurde geändert, Inhalte wurden noch nicht neu generiert.', 'blocksy-child' )
		);
	}

	if ( 'fail' === $snapshot['last_assert_status'] ) {
		printf(
			'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
			esc_html__( 'Glossary Routing Assertion fehlgeschlagen – interne Linkstruktur prüfen.', 'blocksy-child' )
		);
	}
}
add_action( 'admin_notices', 'nexus_render_glossary_sync_admin_notices' );

/**
 * Register a compact glossary sync debug widget on the default dashboard.
 *
 * @return void
 */
function nexus_register_glossary_sync_dashboard_widget() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	wp_add_dashboard_widget(
		'nexus_glossary_sync_dashboard_widget',
		'Glossary Sync Status',
		'nexus_render_glossary_sync_dashboard_widget'
	);
}
add_action( 'wp_dashboard_setup', 'nexus_register_glossary_sync_dashboard_widget' );

/**
 * Render the glossary sync debug widget.
 *
 * @return void
 */
function nexus_render_glossary_sync_dashboard_widget() {
	$snapshot            = nexus_get_glossary_sync_status_snapshot();
	$assert_status_label = '' !== $snapshot['last_assert_status'] ? strtoupper( (string) $snapshot['last_assert_status'] ) : 'NOT RUN';
	?>
	<div class="nexus-glossary-sync-widget">
		<p>
			<strong>Status:</strong>
			<?php echo esc_html( ! empty( $snapshot['sync_required'] ) ? 'Sync erforderlich' : 'Synchron' ); ?>
		</p>
		<table class="widefat striped">
			<tbody>
				<tr>
					<td><strong>Aktuelle Registry-Version</strong></td>
					<td><code><?php echo esc_html( (string) $snapshot['current_registry_version'] ); ?></code></td>
				</tr>
				<tr>
					<td><strong>Letzte gesyncte Version</strong></td>
					<td><code><?php echo esc_html( '' !== (string) $snapshot['last_synced_registry_version'] ? (string) $snapshot['last_synced_registry_version'] : 'n/a' ); ?></code></td>
				</tr>
				<tr>
					<td><strong>Letzter Sync</strong></td>
					<td>
						<?php
						$sync_ts = (string) $snapshot['last_sync_time_gmt'];
						if ( '' !== $sync_ts ) {
							echo esc_html( nexus_format_glossary_sync_timestamp( $sync_ts ) );
						} elseif ( ! empty( $snapshot['sync_ever_ran'] ) ) {
							echo '<em>synchronisiert &ndash; Zeitpunkt nicht verf&uuml;gbar</em>';
						} else {
							echo '<em>nie synchronisiert</em>';
						}
						?>
					</td>
				</tr>
				<tr>
					<td><strong>Letzter Assertion Status</strong></td>
					<td><code><?php echo esc_html( $assert_status_label ); ?></code></td>
				</tr>
				<tr>
					<td><strong>Letzte Assertion</strong></td>
					<td><?php echo esc_html( nexus_format_glossary_sync_timestamp( (string) $snapshot['last_assert_time_gmt'] ) ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}
