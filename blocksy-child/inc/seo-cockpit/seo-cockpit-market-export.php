<?php
/**
 * Market Intelligence CSV export.
 *
 * Streams the already persisted DataForSEO market snapshot plus locally joined
 * first-party opportunity signals. The export is cache/read-only and never
 * triggers a DataForSEO or Research provider request.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stable column contract for the Market Intelligence export.
 *
 * @return array<int, string>
 */
function nexus_get_market_intelligence_export_columns() {
	return [
		'row_type',
		'source',
		'snapshot_at',
		'target',
		'location_name',
		'language_code',
		'keyword',
		'domain',
		'url',
		'title',
		'rank',
		'previous_rank',
		'search_volume',
		'cpc',
		'difficulty',
		'intent',
		'competition_level',
		'serp_type',
		'etv',
		'avg_referring_domains',
		'intersections',
		'shared_etv',
		'shared_count',
		'organic_etv',
		'organic_keywords',
		'domain_top10',
		'estimated_paid_traffic_cost',
		'strategic_checked_at',
		'top10_shared',
		'strategic',
		'strategic_overlap',
		'segment',
		'segment_label',
		'score',
		'action',
		'gsc_clicks',
		'gsc_impressions',
		'gsc_position',
		'leads_current',
		'crm_contacts_current',
		'won_lifetime',
		'is_own',
		'cid',
		'note',
	];
}

/**
 * German Excel-compatible decimal without thousands separator.
 *
 * @param mixed $value Numeric value.
 * @param int   $decimals Decimal places.
 * @return string
 */
function nexus_market_intelligence_export_decimal( $value, $decimals = 2 ) {
	if ( null === $value || '' === $value || ! is_numeric( $value ) ) {
		return '';
	}

	return number_format( (float) $value, max( 0, absint( $decimals ) ), ',', '' );
}

/**
 * Return one blank/base row for the current snapshot.
 *
 * @param array<string, mixed> $snapshot Market snapshot.
 * @param string               $row_type Row type.
 * @param string               $source Source identifier.
 * @return array<string, mixed>
 */
function nexus_market_intelligence_export_base_row( $snapshot, $row_type, $source ) {
	$config = is_array( $snapshot['config'] ?? null ) ? $snapshot['config'] : [];

	return array_merge(
		array_fill_keys( nexus_get_market_intelligence_export_columns(), '' ),
		[
			'row_type'      => sanitize_key( $row_type ),
			'source'        => sanitize_key( $source ),
			'snapshot_at'   => ! empty( $snapshot['generated_at'] ) ? wp_date( 'c', absint( $snapshot['generated_at'] ) ) : '',
			'target'        => (string) ( $snapshot['target'] ?? nexus_dataforseo_target_domain() ),
			'location_name' => (string) ( $config['location_name'] ?? '' ),
			'language_code' => (string) ( $config['language_code'] ?? '' ),
		]
	);
}

/**
 * Return the existing 28-day SEO snapshot only if already cached.
 *
 * The market export must not turn into a Search Console fetch path.
 *
 * @return array<string, mixed>
 */
function nexus_market_intelligence_export_cached_seo_snapshot() {
	if ( function_exists( 'nexus_ci_cached_gsc_snapshot' ) ) {
		$snapshot = nexus_ci_cached_gsc_snapshot();
		if ( is_array( $snapshot ) ) {
			return $snapshot;
		}
	}

	if ( function_exists( 'nexus_get_seo_cockpit_snapshot_cache_key' ) ) {
		$snapshot = get_transient( nexus_get_seo_cockpit_snapshot_cache_key( 28 ) );
		if ( is_array( $snapshot ) ) {
			return $snapshot;
		}
	}

	return [];
}

/**
 * Add local lead/CRM context to the cached SEO snapshot.
 *
 * @param array<string, mixed> $seo Cached SEO snapshot.
 * @return array<string, mixed>
 */
function nexus_market_intelligence_export_local_context( $seo ) {
	$seo    = is_array( $seo ) ? $seo : [];
	$ranges = function_exists( 'nexus_get_seo_cockpit_date_ranges' )
		? nexus_get_seo_cockpit_date_ranges( 28 )
		: [];

	if ( empty( $seo['leads'] ) && ! empty( $ranges ) && function_exists( 'nexus_get_seo_cockpit_lead_snapshot_data' ) ) {
		$seo['leads'] = nexus_get_seo_cockpit_lead_snapshot_data( $ranges );
	}

	if ( empty( $seo['acquisition'] ) && ! empty( $ranges ) && function_exists( 'nexus_get_seo_cockpit_crm_acquisition_snapshot_data' ) ) {
		$seo['acquisition'] = nexus_get_seo_cockpit_crm_acquisition_snapshot_data( $ranges );
	}

	return $seo;
}

/**
 * Build normalized Market Intelligence export rows.
 *
 * @param array<string, mixed> $snapshot Current market snapshot.
 * @return array<int, array<string, mixed>>
 */
function nexus_build_market_intelligence_export_rows( $snapshot ) {
	if ( empty( $snapshot ) || ! is_array( $snapshot ) ) {
		return [];
	}

	$rows        = [];
	$ranked      = is_array( $snapshot['ranked']['rows'] ?? null ) ? $snapshot['ranked']['rows'] : [];
	$competitors = is_array( $snapshot['competitors'] ?? null ) ? $snapshot['competitors'] : [];
	$overview    = is_array( $snapshot['keyword_overview'] ?? null ) ? $snapshot['keyword_overview'] : [];
	$strategic   = function_exists( 'nexus_market_intelligence_strategic_competitors' )
		? nexus_market_intelligence_strategic_competitors( $competitors )
		: [];
	$strategic_domains = [];

	foreach ( $strategic as $row ) {
		if ( is_array( $row ) && ! empty( $row['domain'] ) ) {
			$strategic_domains[ (string) $row['domain'] ] = true;
		}
	}

	foreach ( $ranked as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$export = nexus_market_intelligence_export_base_row( $snapshot, 'ranked_keyword', 'dataforseo_labs' );
		$export['keyword']               = (string) ( $row['keyword'] ?? '' );
		$export['url']                   = (string) ( $row['url'] ?? '' );
		$export['rank']                  = absint( $row['rank_group'] ?? 0 );
		$export['previous_rank']         = isset( $row['previous_rank'] ) && is_numeric( $row['previous_rank'] ) ? absint( $row['previous_rank'] ) : '';
		$export['search_volume']         = nexus_market_intelligence_export_decimal( $row['search_volume'] ?? 0, 0 );
		$export['cpc']                   = nexus_market_intelligence_export_decimal( $row['cpc'] ?? 0, 4 );
		$export['difficulty']            = nexus_market_intelligence_export_decimal( $row['difficulty'] ?? null, 2 );
		$export['intent']                = (string) ( $row['intent'] ?? '' );
		$export['competition_level']     = (string) ( $row['competition_level'] ?? '' );
		$export['serp_type']             = (string) ( $row['serp_type'] ?? '' );
		$export['etv']                   = nexus_market_intelligence_export_decimal( $row['etv'] ?? 0, 4 );
		$export['avg_referring_domains'] = nexus_market_intelligence_export_decimal( $row['avg_referring_domains'] ?? null, 2 );
		$rows[] = $export;
	}

	foreach ( $overview as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$export = nexus_market_intelligence_export_base_row( $snapshot, 'keyword_overview', 'dataforseo_labs' );
		$export['keyword']               = (string) ( $row['keyword'] ?? '' );
		$export['search_volume']         = nexus_market_intelligence_export_decimal( $row['search_volume'] ?? 0, 0 );
		$export['cpc']                   = nexus_market_intelligence_export_decimal( $row['cpc'] ?? 0, 4 );
		$export['difficulty']            = nexus_market_intelligence_export_decimal( $row['difficulty'] ?? null, 2 );
		$export['intent']                = (string) ( $row['intent'] ?? '' );
		$export['competition_level']     = (string) ( $row['competition_level'] ?? '' );
		$export['avg_referring_domains'] = nexus_market_intelligence_export_decimal( $row['avg_referring_domains'] ?? null, 2 );
		$export['note']                  = ! empty( $row['serp_features'] ) ? implode( ',', array_map( 'strval', (array) $row['serp_features'] ) ) : '';
		$rows[] = $export;
	}

	foreach ( $competitors as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$domain = (string) ( $row['domain'] ?? '' );
		$export = nexus_market_intelligence_export_base_row( $snapshot, 'competitor_auto', 'dataforseo_labs' );
		$export['domain']           = $domain;
		$export['rank']             = nexus_market_intelligence_export_decimal( $row['avg_position'] ?? null, 2 );
		$export['intersections']     = absint( $row['intersections'] ?? 0 );
		$export['shared_etv']        = nexus_market_intelligence_export_decimal( $row['shared_etv'] ?? 0, 4 );
		$export['shared_count']      = absint( $row['shared_count'] ?? 0 );
		$export['organic_etv']       = nexus_market_intelligence_export_decimal( $row['organic_etv'] ?? 0, 4 );
		$export['organic_keywords']  = absint( $row['organic_keywords'] ?? 0 );
		$export['top10_shared']      = absint( $row['top10_shared'] ?? 0 );
		$export['strategic']         = isset( $strategic_domains[ $domain ] ) ? 1 : 0;
		$export['strategic_overlap'] = isset( $strategic_domains[ $domain ] ) ? 1 : 0;
		$rows[] = $export;
	}

	foreach ( $strategic as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$export = nexus_market_intelligence_export_base_row( $snapshot, 'competitor_strategic', 'repo_strategy' );
		$export['domain']           = (string) ( $row['domain'] ?? '' );
		$export['rank']             = nexus_market_intelligence_export_decimal( $row['avg_position'] ?? null, 2 );
		$export['intersections']     = absint( $row['intersections'] ?? 0 );
		$export['shared_etv']        = nexus_market_intelligence_export_decimal( $row['shared_etv'] ?? 0, 4 );
		$export['shared_count']      = absint( $row['shared_count'] ?? 0 );
		$export['organic_etv']                  = nexus_market_intelligence_export_decimal( $row['organic_etv'] ?? 0, 4 );
		$export['organic_keywords']             = absint( $row['organic_keywords'] ?? 0 );
		$export['domain_top10']                 = absint( $row['domain_top10'] ?? 0 );
		$export['estimated_paid_traffic_cost']  = nexus_market_intelligence_export_decimal( $row['estimated_paid_traffic_cost'] ?? 0, 4 );
		$export['strategic_checked_at']          = ! empty( $row['checked_at'] ) ? wp_date( 'c', absint( $row['checked_at'] ) ) : '';
		$export['top10_shared']                 = absint( $row['top10_shared'] ?? 0 );
		$export['strategic']                    = 1;
		$export['strategic_overlap'] = ! empty( $row['is_overlap'] ) ? 1 : 0;
		$export['note']              = ! empty( $row['is_overlap'] )
			? 'Strategische Vergleichsgruppe; auch im organischen Competitor-Snapshot gefunden.'
			: 'Strategische Vergleichsgruppe; aktuell keine Überschneidung im automatischen Competitor-Snapshot.';
		$rows[] = $export;
	}

	$seo = nexus_market_intelligence_export_local_context( nexus_market_intelligence_export_cached_seo_snapshot() );
	if ( function_exists( 'nexus_get_market_intelligence_opportunities' ) ) {
		foreach ( nexus_get_market_intelligence_opportunities( $seo, 100 ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$export = nexus_market_intelligence_export_base_row( $snapshot, 'opportunity', 'cockpit_join' );
			$export['keyword']              = (string) ( $row['keyword'] ?? '' );
			$export['url']                  = (string) ( $row['url'] ?? '' );
			$export['rank']                 = absint( $row['rank'] ?? 0 );
			$export['search_volume']        = nexus_market_intelligence_export_decimal( $row['search_volume'] ?? 0, 0 );
			$export['difficulty']           = nexus_market_intelligence_export_decimal( $row['difficulty'] ?? null, 2 );
			$export['intent']               = (string) ( $row['intent'] ?? '' );
			$export['segment']              = (string) ( $row['segment'] ?? '' );
			$export['segment_label']        = (string) ( $row['segment_label'] ?? '' );
			$export['score']                = absint( $row['score'] ?? 0 );
			$export['action']               = (string) ( $row['action'] ?? '' );
			$export['gsc_clicks']           = nexus_market_intelligence_export_decimal( $row['gsc_clicks'] ?? 0, 0 );
			$export['gsc_impressions']      = nexus_market_intelligence_export_decimal( $row['gsc_impressions'] ?? 0, 0 );
			$export['gsc_position']         = nexus_market_intelligence_export_decimal( $row['gsc_position'] ?? 0, 2 );
			$export['leads_current']        = absint( $row['leads_current'] ?? 0 );
			$export['crm_contacts_current'] = absint( $row['crm_contacts_current'] ?? 0 );
			$export['won_lifetime']         = absint( $row['won_lifetime'] ?? 0 );
			$rows[] = $export;
		}
	}

	$target = nexus_dataforseo_target_domain();
	foreach ( [ 'live_serp' => 'live_organic', 'local_maps' => 'live_maps' ] as $snapshot_key => $row_type ) {
		foreach ( (array) ( $snapshot[ $snapshot_key ] ?? [] ) as $watch_row ) {
			if ( ! is_array( $watch_row ) ) {
				continue;
			}

			$keyword = (string) ( $watch_row['keyword'] ?? '' );
			$result  = is_array( $watch_row['result'] ?? null ) ? $watch_row['result'] : [];
			$top     = is_array( $result['top'] ?? null ) ? $result['top'] : [];
			$own     = is_array( $result['own'] ?? null ) ? $result['own'] : [];
			$own_seen = false;

			foreach ( $top as $top_row ) {
				if ( ! is_array( $top_row ) ) {
					continue;
				}

				$domain = strtolower( preg_replace( '/^www\./i', '', (string) ( $top_row['domain'] ?? '' ) ) );
				$is_own = '' !== $domain && $target === $domain;
				$own_seen = $own_seen || $is_own;
				$export = nexus_market_intelligence_export_base_row( $snapshot, $row_type, 'dataforseo_live' );
				$export['keyword'] = $keyword;
				$export['domain']  = $domain;
				$export['url']     = (string) ( $top_row['url'] ?? '' );
				$export['title']   = (string) ( $top_row['title'] ?? '' );
				$export['rank']    = absint( $top_row['rank'] ?? 0 );
				$export['is_own']  = $is_own ? 1 : 0;
				$export['cid']     = (string) ( $top_row['cid'] ?? '' );
				$rows[] = $export;
			}

			if ( ! empty( $own ) && ! $own_seen ) {
				$export = nexus_market_intelligence_export_base_row( $snapshot, $row_type . '_own', 'dataforseo_live' );
				$export['keyword'] = $keyword;
				$export['domain']  = (string) ( $own['domain'] ?? $target );
				$export['url']     = (string) ( $own['url'] ?? '' );
				$export['title']   = (string) ( $own['title'] ?? '' );
				$export['rank']    = absint( $own['rank'] ?? 0 );
				$export['is_own']  = 1;
				$export['cid']     = (string) ( $own['cid'] ?? '' );
				$rows[] = $export;
			}
		}
	}

	return $rows;
}

/**
 * Stream the current Market Intelligence snapshot as Excel-compatible CSV.
 *
 * @return void
 */
function nexus_handle_market_intelligence_export() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_market_intelligence_export' );

	$snapshot = nexus_get_market_intelligence_snapshot();
	$rows     = nexus_build_market_intelligence_export_rows( $snapshot );

	if ( empty( $snapshot ) || empty( $rows ) ) {
		wp_safe_redirect( nexus_market_intelligence_admin_url( [ 'market_notice' => 'export_empty' ] ) );
		exit;
	}

	$stamp    = ! empty( $snapshot['generated_at'] ) ? wp_date( 'Y-m-d', absint( $snapshot['generated_at'] ) ) : wp_date( 'Y-m-d' );
	$filename = 'seo-market-intelligence-' . sanitize_file_name( $stamp ) . '.csv';

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

	// UTF-8 BOM + semicolon: directly usable in German Excel.
	echo "\xEF\xBB\xBF";

	$columns = nexus_get_market_intelligence_export_columns();
	$output  = fopen( 'php://output', 'w' );

	if ( false === $output ) {
		wp_die( 'Export konnte nicht geöffnet werden.' );
	}

	fputcsv( $output, $columns, ';', '"', '' );

	foreach ( $rows as $row ) {
		$line = [];
		foreach ( $columns as $column ) {
			$line[] = $row[ $column ] ?? '';
		}
		fputcsv( $output, $line, ';', '"', '' );
	}

	fclose( $output );
	exit;
}
add_action( 'admin_post_nexus_market_intelligence_export', 'nexus_handle_market_intelligence_export' );
