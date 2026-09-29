<?php
/**
 * DataForSEO-backed Market Intelligence layer for SEO Cockpit.
 *
 * This module is deliberately separate from Research Intelligence. Research
 * holds primary macro data; Market Intelligence describes the external search
 * market around this site and joins it with first-party GSC/CRM signals.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @return string */
function nexus_market_intelligence_snapshot_option_name() {
	return 'nexus_market_intelligence_snapshot_v1';
}

/** @return string */
function nexus_market_intelligence_history_option_name() {
	return 'nexus_market_intelligence_history_v1';
}

/** @return string */
function nexus_market_intelligence_cron_hook() {
	return 'nexus_dataforseo_market_weekly_refresh';
}

/** @return string */
function nexus_market_intelligence_lock_key() {
	return 'nexus_dataforseo_market_refresh_lock';
}

/**
 * Return the persisted market snapshot.
 *
 * @return array<string, mixed>
 */
function nexus_get_market_intelligence_snapshot() {
	$snapshot = get_option( nexus_market_intelligence_snapshot_option_name(), [] );

	return is_array( $snapshot ) ? $snapshot : [];
}

/**
 * Persist the market snapshot.
 *
 * @param array<string, mixed> $snapshot Snapshot.
 * @return void
 */
function nexus_update_market_intelligence_snapshot( $snapshot ) {
	update_option( nexus_market_intelligence_snapshot_option_name(), $snapshot, false );

	if ( function_exists( 'nexus_bump_seo_cockpit_cache_version' ) ) {
		nexus_bump_seo_cockpit_cache_version();
	}
}

/**
 * Convert the configured keyword textarea into a stable watchlist.
 *
 * @return array<int, string>
 */
function nexus_market_intelligence_manual_keywords() {
	$config = nexus_get_dataforseo_config();
	$raw    = preg_split( '/[\r\n,;]+/u', (string) ( $config['watch_keywords'] ?? '' ) );
	$raw    = is_array( $raw ) ? $raw : [];
	$seen   = [];
	$rows   = [];

	foreach ( $raw as $keyword ) {
		$keyword = trim( wp_strip_all_tags( (string) $keyword ) );
		if ( '' === $keyword || mb_strlen( $keyword ) > 80 ) {
			continue;
		}

		$key = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $keyword )
			: mb_strtolower( $keyword );

		if ( '' === $key || isset( $seen[ $key ] ) ) {
			continue;
		}

		$seen[ $key ] = true;
		$rows[]       = $keyword;

		if ( count( $rows ) >= 50 ) {
			break;
		}
	}

	return $rows;
}

/**
 * Convert the configured strategic competitor textarea into normalized domains.
 *
 * This is intentionally separate from DataForSEO's organic-overlap competitors:
 * the automatic list describes who Google currently associates with the site;
 * this list describes who should be watched for the site's intended market.
 *
 * @return array<int, string>
 */
function nexus_market_intelligence_strategic_domains() {
	$config = nexus_get_dataforseo_config();
	$raw    = preg_split( '/[\r\n,;]+/u', (string) ( $config['strategic_competitors'] ?? '' ) );
	$raw    = is_array( $raw ) ? $raw : [];
	$target = nexus_dataforseo_target_domain();
	$seen   = [];
	$out    = [];

	foreach ( $raw as $value ) {
		$value = trim( wp_strip_all_tags( (string) $value ) );
		if ( '' === $value ) {
			continue;
		}

		if ( false === strpos( $value, '://' ) ) {
			$value = 'https://' . ltrim( $value, '/' );
		}

		$domain = strtolower( (string) wp_parse_url( $value, PHP_URL_HOST ) );
		$domain = preg_replace( '/^www\./i', '', $domain );
		$domain = is_string( $domain ) ? trim( $domain, ". \t\n\r\0\x0B" ) : '';

		if (
			'' === $domain
			|| $domain === $target
			|| ! preg_match( '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $domain )
			|| isset( $seen[ $domain ] )
		) {
			continue;
		}

		$seen[ $domain ] = true;
		$out[]           = $domain;

		if ( count( $out ) >= 20 ) {
			break;
		}
	}

	return $out;
}

/**
 * Join the strategic comparison group with the current automatic competitor
 * snapshot without triggering any provider request.
 *
 * @param array<int, array<string, mixed>>|null $competitors Automatic competitors.
 * @return array<int, array<string, mixed>>
 */
function nexus_market_intelligence_strategic_competitors( $competitors = null ) {
	$snapshot = nexus_get_market_intelligence_snapshot();

	if ( null === $competitors ) {
		$competitors = is_array( $snapshot['competitors'] ?? null ) ? $snapshot['competitors'] : [];
	}

	$overview_rows = is_array( $snapshot['strategic_overview'] ?? null ) ? $snapshot['strategic_overview'] : [];
	$index         = [];
	$overview      = [];

	foreach ( (array) $competitors as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$domain = strtolower( (string) preg_replace( '/^www\./i', '', trim( (string) ( $row['domain'] ?? '' ) ) ) );
		if ( '' !== $domain ) {
			$index[ $domain ] = $row;
		}
	}

	foreach ( $overview_rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$domain = strtolower( (string) preg_replace( '/^www\./i', '', trim( (string) ( $row['domain'] ?? '' ) ) ) );
		if ( '' !== $domain ) {
			$overview[ $domain ] = $row;
		}
	}

	$out = [];
	foreach ( nexus_market_intelligence_strategic_domains() as $domain ) {
		$match = isset( $index[ $domain ] ) && is_array( $index[ $domain ] ) ? $index[ $domain ] : [];
		$rank  = isset( $overview[ $domain ] ) && is_array( $overview[ $domain ] ) ? $overview[ $domain ] : [];

		$out[] = [
			'domain'                      => $domain,
			'is_overlap'                  => ! empty( $match ),
			'is_checked'                  => ! empty( $rank ),
			'checked_at'                  => absint( $rank['checked_at'] ?? 0 ),
			'avg_position'                => is_numeric( $match['avg_position'] ?? null ) ? (float) $match['avg_position'] : null,
			'intersections'               => absint( $match['intersections'] ?? 0 ),
			'shared_etv'                  => is_numeric( $match['shared_etv'] ?? null ) ? (float) $match['shared_etv'] : 0.0,
			'shared_count'                => absint( $match['shared_count'] ?? 0 ),
			'organic_etv'                 => is_numeric( $rank['organic_etv'] ?? null )
				? (float) $rank['organic_etv']
				: ( is_numeric( $match['organic_etv'] ?? null ) ? (float) $match['organic_etv'] : 0.0 ),
			'organic_keywords'            => ! empty( $rank )
				? absint( $rank['organic_keywords'] ?? 0 )
				: absint( $match['organic_keywords'] ?? 0 ),
			'domain_top10'                => absint( $rank['top10'] ?? 0 ),
			'estimated_paid_traffic_cost' => is_numeric( $rank['estimated_paid_traffic_cost'] ?? null ) ? (float) $rank['estimated_paid_traffic_cost'] : 0.0,
			'is_new'                      => absint( $rank['is_new'] ?? 0 ),
			'is_up'                       => absint( $rank['is_up'] ?? 0 ),
			'is_down'                     => absint( $rank['is_down'] ?? 0 ),
			'is_lost'                     => absint( $rank['is_lost'] ?? 0 ),
			'top10_shared'                => absint( $match['top10_shared'] ?? 0 ),
		];
	}

	return $out;
}

/**
 * Manually fetch domain-level ranking metrics for the strategic comparison
 * group. One DataForSEO Live task is required per domain, so this path is never
 * called by cron and is capped to eight domains per click.
 *
 * @return array<string, mixed>|WP_Error
 */
function nexus_refresh_market_intelligence_strategic_overview() {
	if ( ! nexus_dataforseo_has_credentials() ) {
		return new WP_Error( 'nexus_market_credentials', 'DataForSEO ist noch nicht konfiguriert.' );
	}

	$domains = array_slice( nexus_market_intelligence_strategic_domains(), 0, 8 );
	if ( empty( $domains ) ) {
		return new WP_Error( 'nexus_market_strategic_empty', 'Es sind keine strategischen Wettbewerber gepflegt.' );
	}

	$config   = nexus_get_dataforseo_config();
	$snapshot = nexus_get_market_intelligence_snapshot();
	$rows     = [];
	$errors   = [];

	foreach ( $domains as $domain ) {
		$task = [
			'target'          => $domain,
			'location_name'   => (string) $config['location_name'],
			'language_code'   => (string) $config['language_code'],
			'ignore_synonyms' => true,
			'limit'           => 1,
			'tag'             => 'nexus_market_strategic_overview',
		];

		$response = nexus_dataforseo_request( 'v3/dataforseo_labs/google/domain_rank_overview/live', $task, false );
		$result   = nexus_dataforseo_first_result( $response );

		if ( is_wp_error( $result ) ) {
			$errors[ $domain ] = $result->get_error_message();
			continue;
		}

		$items   = is_array( $result['items'] ?? null ) ? $result['items'] : [];
		$item    = ! empty( $items ) && is_array( $items[0] ) ? $items[0] : [];
		$metrics = is_array( $item['metrics'] ?? null ) ? $item['metrics'] : [];
		$organic = is_array( $metrics['organic'] ?? null ) ? $metrics['organic'] : [];

		$rows[] = [
			'domain'                      => $domain,
			'checked_at'                  => time(),
			'organic_keywords'            => absint( $organic['count'] ?? 0 ),
			'organic_etv'                 => is_numeric( $organic['etv'] ?? null ) ? (float) $organic['etv'] : 0.0,
			'estimated_paid_traffic_cost' => is_numeric( $organic['estimated_paid_traffic_cost'] ?? null ) ? (float) $organic['estimated_paid_traffic_cost'] : 0.0,
			'pos_1'                       => absint( $organic['pos_1'] ?? 0 ),
			'pos_2_3'                     => absint( $organic['pos_2_3'] ?? 0 ),
			'pos_4_10'                    => absint( $organic['pos_4_10'] ?? 0 ),
			'top10'                       => absint( $organic['pos_1'] ?? 0 ) + absint( $organic['pos_2_3'] ?? 0 ) + absint( $organic['pos_4_10'] ?? 0 ),
			'is_new'                      => absint( $organic['is_new'] ?? 0 ),
			'is_up'                       => absint( $organic['is_up'] ?? 0 ),
			'is_down'                     => absint( $organic['is_down'] ?? 0 ),
			'is_lost'                     => absint( $organic['is_lost'] ?? 0 ),
		];
	}

	if ( empty( $rows ) && ! empty( $errors ) ) {
		return new WP_Error( 'nexus_market_strategic_failed', implode( ' | ', array_values( $errors ) ) );
	}

	$existing = is_array( $snapshot['strategic_overview'] ?? null ) ? $snapshot['strategic_overview'] : [];
	$index    = [];

	foreach ( $existing as $row ) {
		if ( is_array( $row ) && ! empty( $row['domain'] ) ) {
			$index[ (string) $row['domain'] ] = $row;
		}
	}
	foreach ( $rows as $row ) {
		$index[ (string) $row['domain'] ] = $row;
	}

	$snapshot['strategic_overview']        = array_values( $index );
	$snapshot['strategic_overview_errors'] = $errors;
	$snapshot['strategic_overview_at']     = time();
	nexus_update_market_intelligence_snapshot( $snapshot );

	return [
		'rows'   => $rows,
		'errors' => $errors,
	];
}

/**
 * Return useful GSC queries without triggering a separate keyword universe.
 *
 * @param int $limit Max queries.
 * @return array<int, string>
 */
function nexus_market_intelligence_gsc_keywords( $limit = 30 ) {
	if ( ! function_exists( 'nexus_get_seo_cockpit_snapshot' ) ) {
		return [];
	}

	$snapshot = nexus_get_seo_cockpit_snapshot( false, 28 );
	if ( is_wp_error( $snapshot ) || ! is_array( $snapshot ) ) {
		return [];
	}

	$rows = (array) ( $snapshot['top_queries'] ?? [] );
	$out  = [];
	$seen = [];

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$query = function_exists( 'nexus_get_seo_cockpit_row_key' )
			? nexus_get_seo_cockpit_row_key( $row, 0 )
			: (string) ( $row['keys'][0] ?? '' );

		$query = trim( (string) $query );
		if ( '' === $query ) {
			continue;
		}

		if ( function_exists( 'nexus_is_seo_cockpit_non_target_query' ) && nexus_is_seo_cockpit_non_target_query( $query ) ) {
			continue;
		}

		$key = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $query )
			: mb_strtolower( $query );

		if ( '' === $key || isset( $seen[ $key ] ) ) {
			continue;
		}

		$seen[ $key ] = true;
		$out[]        = $query;

		if ( count( $out ) >= max( 1, $limit ) ) {
			break;
		}
	}

	return $out;
}

/**
 * Fetch and normalize ranked keywords for the site.
 *
 * @param bool $automatic Whether this request belongs to the weekly job.
 * @return array<string, mixed>|WP_Error
 */
function nexus_market_intelligence_fetch_ranked_keywords( $automatic = false ) {
	$config = nexus_get_dataforseo_config();
	$task   = [
		'target'             => (string) $config['target'],
		'location_name'      => (string) $config['location_name'],
		'language_code'      => (string) $config['language_code'],
		'item_types'         => [ 'organic', 'featured_snippet', 'local_pack' ],
		'ignore_synonyms'    => true,
		'load_rank_absolute' => true,
		'historical_serp_mode' => 'live',
		'order_by'           => [ 'keyword_data.keyword_info.search_volume,desc', 'ranked_serp_element.serp_item.rank_group,asc' ],
		'limit'              => (int) $config['ranked_limit'],
		'tag'                => 'nexus_market_ranked_keywords',
	];

	$response = nexus_dataforseo_request( 'v3/dataforseo_labs/google/ranked_keywords/live', $task, $automatic );
	$result   = nexus_dataforseo_first_result( $response );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	$items = is_array( $result['items'] ?? null ) ? $result['items'] : [];
	$rows  = [];

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$keyword_data = is_array( $item['keyword_data'] ?? null ) ? $item['keyword_data'] : [];
		$keyword_info = is_array( $keyword_data['keyword_info'] ?? null ) ? $keyword_data['keyword_info'] : [];
		$properties   = is_array( $keyword_data['keyword_properties'] ?? null ) ? $keyword_data['keyword_properties'] : [];
		$intent       = is_array( $keyword_data['search_intent_info'] ?? null ) ? $keyword_data['search_intent_info'] : [];
		$backlinks    = is_array( $keyword_data['avg_backlinks_info'] ?? null ) ? $keyword_data['avg_backlinks_info'] : [];
		$ranked       = is_array( $item['ranked_serp_element'] ?? null ) ? $item['ranked_serp_element'] : [];
		$serp         = is_array( $ranked['serp_item'] ?? null ) ? $ranked['serp_item'] : [];
		$changes      = is_array( $serp['rank_changes'] ?? null ) ? $serp['rank_changes'] : [];
		$keyword      = trim( (string) ( $keyword_data['keyword'] ?? '' ) );

		if ( '' === $keyword ) {
			continue;
		}

		$rows[] = [
			'keyword'              => $keyword,
			'search_volume'        => is_numeric( $keyword_info['search_volume'] ?? null ) ? (float) $keyword_info['search_volume'] : 0.0,
			'cpc'                  => is_numeric( $keyword_info['cpc'] ?? null ) ? (float) $keyword_info['cpc'] : 0.0,
			'competition_level'    => sanitize_key( (string) ( $keyword_info['competition_level'] ?? '' ) ),
			'difficulty'           => is_numeric( $properties['keyword_difficulty'] ?? null ) ? (float) $properties['keyword_difficulty'] : null,
			'intent'               => sanitize_key( (string) ( $intent['main_intent'] ?? '' ) ),
			'serp_type'            => sanitize_key( (string) ( $serp['type'] ?? '' ) ),
			'rank_group'           => absint( $serp['rank_group'] ?? 0 ),
			'rank_absolute'        => absint( $serp['rank_absolute'] ?? 0 ),
			'previous_rank'        => is_numeric( $changes['previous_rank_absolute'] ?? null ) ? absint( $changes['previous_rank_absolute'] ) : null,
			'is_new'               => ! empty( $changes['is_new'] ),
			'is_up'                => ! empty( $changes['is_up'] ),
			'is_down'              => ! empty( $changes['is_down'] ),
			'url'                  => esc_url_raw( (string) ( $serp['url'] ?? '' ) ),
			'etv'                  => is_numeric( $serp['etv'] ?? null ) ? (float) $serp['etv'] : 0.0,
			'avg_referring_domains'=> is_numeric( $backlinks['referring_domains'] ?? null ) ? (float) $backlinks['referring_domains'] : null,
		];
	}

	$metrics = is_array( $result['metrics'] ?? null ) ? $result['metrics'] : [];
	$organic = is_array( $metrics['organic'] ?? null ) ? $metrics['organic'] : [];

	return [
		'total_count' => absint( $result['total_count'] ?? count( $rows ) ),
		'metrics'     => [
			'count'                       => absint( $organic['count'] ?? 0 ),
			'etv'                         => is_numeric( $organic['etv'] ?? null ) ? (float) $organic['etv'] : 0.0,
			'estimated_paid_traffic_cost' => is_numeric( $organic['estimated_paid_traffic_cost'] ?? null ) ? (float) $organic['estimated_paid_traffic_cost'] : 0.0,
			'is_new'                      => absint( $organic['is_new'] ?? 0 ),
			'is_up'                       => absint( $organic['is_up'] ?? 0 ),
			'is_down'                     => absint( $organic['is_down'] ?? 0 ),
			'is_lost'                     => absint( $organic['is_lost'] ?? 0 ),
		],
		'rows' => $rows,
	];
}

/**
 * Fetch competitor domains.
 *
 * @param bool $automatic Whether this request belongs to the weekly job.
 * @return array<int, array<string, mixed>>|WP_Error
 */
function nexus_market_intelligence_fetch_competitors( $automatic = false ) {
	$config = nexus_get_dataforseo_config();
	$task   = [
		'target'              => (string) $config['target'],
		'location_name'       => (string) $config['location_name'],
		'language_code'       => (string) $config['language_code'],
		'item_types'          => [ 'organic' ],
		'ignore_synonyms'     => true,
		'exclude_top_domains' => true,
		'limit'               => (int) $config['competitor_limit'],
		'max_rank_group'      => 100,
		'tag'                 => 'nexus_market_competitors',
	];

	$response = nexus_dataforseo_request( 'v3/dataforseo_labs/google/competitors_domain/live', $task, $automatic );
	$items    = nexus_dataforseo_items( $response );
	if ( is_wp_error( $items ) ) {
		return $items;
	}

	$rows   = [];
	$target = strtolower( (string) $config['target'] );

	foreach ( $items as $item ) {
		$domain = strtolower( trim( (string) ( $item['domain'] ?? '' ) ) );
		if ( '' === $domain || $target === $domain ) {
			continue;
		}

		$full       = is_array( $item['full_domain_metrics'] ?? null ) ? $item['full_domain_metrics'] : [];
		$full_org   = is_array( $full['organic'] ?? null ) ? $full['organic'] : [];
		$shared     = is_array( $item['competitor_metrics'] ?? null ) ? $item['competitor_metrics'] : [];
		$shared_org = is_array( $shared['organic'] ?? null ) ? $shared['organic'] : [];

		$rows[] = [
			'domain'            => $domain,
			'avg_position'      => is_numeric( $item['avg_position'] ?? null ) ? (float) $item['avg_position'] : 0.0,
			'intersections'      => absint( $item['intersections'] ?? 0 ),
			'shared_etv'         => is_numeric( $shared_org['etv'] ?? null ) ? (float) $shared_org['etv'] : 0.0,
			'shared_count'       => absint( $shared_org['count'] ?? 0 ),
			'organic_etv'        => is_numeric( $full_org['etv'] ?? null ) ? (float) $full_org['etv'] : 0.0,
			'organic_keywords'   => absint( $full_org['count'] ?? 0 ),
			'top10_shared'       => absint( $shared_org['pos_1'] ?? 0 ) + absint( $shared_org['pos_2_3'] ?? 0 ) + absint( $shared_org['pos_4_10'] ?? 0 ),
		];
	}

	return $rows;
}

/**
 * Fetch keyword overview data in one batched request.
 *
 * @param array<int, string> $keywords Keywords.
 * @param bool               $automatic Whether this request belongs to the weekly job.
 * @return array<int, array<string, mixed>>|WP_Error
 */
function nexus_market_intelligence_fetch_keyword_overview( $keywords, $automatic = false ) {
	$config   = nexus_get_dataforseo_config();
	$keywords = array_values( array_slice( array_filter( array_map( 'strval', $keywords ) ), 0, (int) $config['keyword_overview_limit'] ) );

	if ( empty( $keywords ) ) {
		return [];
	}

	$task = [
		'keywords'           => $keywords,
		'location_name'      => (string) $config['location_name'],
		'language_code'      => (string) $config['language_code'],
		'include_serp_info'  => true,
		'include_clickstream_data' => false,
		'tag'                => 'nexus_market_keyword_overview',
	];

	$response = nexus_dataforseo_request( 'v3/dataforseo_labs/google/keyword_overview/live', $task, $automatic );
	$items    = nexus_dataforseo_items( $response );
	if ( is_wp_error( $items ) ) {
		return $items;
	}

	$rows = [];

	foreach ( $items as $item ) {
		$keyword = trim( (string) ( $item['keyword'] ?? '' ) );
		if ( '' === $keyword ) {
			continue;
		}

		$info       = is_array( $item['keyword_info'] ?? null ) ? $item['keyword_info'] : [];
		$props      = is_array( $item['keyword_properties'] ?? null ) ? $item['keyword_properties'] : [];
		$intent     = is_array( $item['search_intent_info'] ?? null ) ? $item['search_intent_info'] : [];
		$serp       = is_array( $item['serp_info'] ?? null ) ? $item['serp_info'] : [];
		$backlinks  = is_array( $item['avg_backlinks_info'] ?? null ) ? $item['avg_backlinks_info'] : [];
		$rows[] = [
			'keyword'              => $keyword,
			'search_volume'        => is_numeric( $info['search_volume'] ?? null ) ? (float) $info['search_volume'] : 0.0,
			'cpc'                  => is_numeric( $info['cpc'] ?? null ) ? (float) $info['cpc'] : 0.0,
			'competition_level'    => sanitize_key( (string) ( $info['competition_level'] ?? '' ) ),
			'difficulty'           => is_numeric( $props['keyword_difficulty'] ?? null ) ? (float) $props['keyword_difficulty'] : null,
			'intent'               => sanitize_key( (string) ( $intent['main_intent'] ?? '' ) ),
			'serp_features'        => array_values( array_map( 'sanitize_key', (array) ( $serp['serp_item_types'] ?? [] ) ) ),
			'avg_referring_domains'=> is_numeric( $backlinks['referring_domains'] ?? null ) ? (float) $backlinks['referring_domains'] : null,
		];
	}

	return $rows;
}

/**
 * Build a stable keyword set from manual watchlist, GSC and ranked keywords.
 *
 * @param array<int, array<string, mixed>> $ranked Ranked rows.
 * @return array<int, string>
 */
function nexus_market_intelligence_keyword_set( $ranked ) {
	$config = nexus_get_dataforseo_config();
	$pool   = array_merge( nexus_market_intelligence_manual_keywords(), nexus_market_intelligence_gsc_keywords( 30 ) );

	foreach ( $ranked as $row ) {
		if ( is_array( $row ) && ! empty( $row['keyword'] ) ) {
			$pool[] = (string) $row['keyword'];
		}
	}

	$seen = [];
	$out  = [];

	foreach ( $pool as $keyword ) {
		$keyword = trim( (string) $keyword );
		if ( '' === $keyword ) {
			continue;
		}

		$key = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $keyword )
			: mb_strtolower( $keyword );

		if ( '' === $key || isset( $seen[ $key ] ) ) {
			continue;
		}

		$seen[ $key ] = true;
		$out[]        = $keyword;

		if ( count( $out ) >= (int) $config['keyword_overview_limit'] ) {
			break;
		}
	}

	return $out;
}

/**
 * Persist a small rolling market history, not raw API payloads.
 *
 * @param array<string, mixed> $snapshot Current snapshot.
 * @return void
 */
function nexus_market_intelligence_capture_history( $snapshot ) {
	$history = get_option( nexus_market_intelligence_history_option_name(), [] );
	$history = is_array( $history ) ? $history : [];
	$ranked  = is_array( $snapshot['ranked'] ?? null ) ? $snapshot['ranked'] : [];
	$metrics = is_array( $ranked['metrics'] ?? null ) ? $ranked['metrics'] : [];
	$entry   = [
		'captured_at' => absint( $snapshot['generated_at'] ?? time() ),
		'ranked_count'=> absint( $ranked['total_count'] ?? 0 ),
		'etv'         => is_numeric( $metrics['etv'] ?? null ) ? (float) $metrics['etv'] : 0.0,
		'is_up'       => absint( $metrics['is_up'] ?? 0 ),
		'is_down'     => absint( $metrics['is_down'] ?? 0 ),
		'is_lost'     => absint( $metrics['is_lost'] ?? 0 ),
		'competitors' => count( (array) ( $snapshot['competitors'] ?? [] ) ),
	];

	array_unshift( $history, $entry );
	update_option( nexus_market_intelligence_history_option_name(), array_slice( $history, 0, 26 ), false );
}

/**
 * Refresh the cost-controlled Labs market snapshot.
 *
 * Live organic and Maps checks are intentionally separate because they are
 * keyword-level paid requests and should never fan out silently in cron.
 *
 * @param bool $automatic Whether this is the weekly background job.
 * @return array<string, mixed>|WP_Error
 */
function nexus_refresh_market_intelligence( $automatic = false ) {
	if ( ! nexus_dataforseo_has_credentials() ) {
		return new WP_Error( 'nexus_market_credentials', 'DataForSEO ist noch nicht konfiguriert.' );
	}

	$lock_key = nexus_market_intelligence_lock_key();
	if ( get_transient( $lock_key ) ) {
		return new WP_Error( 'nexus_market_locked', 'Ein Market-Intelligence-Refresh läuft bereits.' );
	}
	set_transient( $lock_key, '1', 5 * MINUTE_IN_SECONDS );

	$before  = nexus_get_dataforseo_runtime();
	$errors  = [];
	$current = nexus_get_market_intelligence_snapshot();

	try {
		$ranked = nexus_market_intelligence_fetch_ranked_keywords( $automatic );
		if ( is_wp_error( $ranked ) ) {
			$errors['ranked_keywords'] = $ranked->get_error_message();
			$ranked = is_array( $current['ranked'] ?? null ) ? $current['ranked'] : [ 'total_count' => 0, 'metrics' => [], 'rows' => [] ];
		}

		$competitors = nexus_market_intelligence_fetch_competitors( $automatic );
		if ( is_wp_error( $competitors ) ) {
			$errors['competitors'] = $competitors->get_error_message();
			$competitors = is_array( $current['competitors'] ?? null ) ? $current['competitors'] : [];
		}

		$ranked_rows = is_array( $ranked['rows'] ?? null ) ? $ranked['rows'] : [];
		$keywords    = nexus_market_intelligence_keyword_set( $ranked_rows );
		$overview    = nexus_market_intelligence_fetch_keyword_overview( $keywords, $automatic );
		if ( is_wp_error( $overview ) ) {
			$errors['keyword_overview'] = $overview->get_error_message();
			$overview = is_array( $current['keyword_overview'] ?? null ) ? $current['keyword_overview'] : [];
		}

		$after = nexus_get_dataforseo_runtime();
		$cost  = max( 0.0, (float) ( $after['cost_month_usd'] ?? 0.0 ) - (float) ( $before['cost_month_usd'] ?? 0.0 ) );

		$snapshot = [
			'generated_at'    => time(),
			'target'          => nexus_dataforseo_target_domain(),
			'config'          => [
				'location_name'       => (string) nexus_get_dataforseo_config()['location_name'],
				'language_code'       => (string) nexus_get_dataforseo_config()['language_code'],
				'local_location_name' => (string) nexus_get_dataforseo_config()['local_location_name'],
			],
			'ranked'          => $ranked,
			'competitors'     => $competitors,
			'keyword_overview'=> $overview,
			'watch_keywords'  => $keywords,
			'strategic_overview'        => is_array( $current['strategic_overview'] ?? null ) ? $current['strategic_overview'] : [],
			'strategic_overview_errors' => is_array( $current['strategic_overview_errors'] ?? null ) ? $current['strategic_overview_errors'] : [],
			'strategic_overview_at'     => absint( $current['strategic_overview_at'] ?? 0 ),
			'live_serp'       => is_array( $current['live_serp'] ?? null ) ? $current['live_serp'] : [],
			'local_maps'      => is_array( $current['local_maps'] ?? null ) ? $current['local_maps'] : [],
			'last_live_at'    => absint( $current['last_live_at'] ?? 0 ),
			'cost_usd'        => $cost,
			'errors'          => $errors,
		];

		nexus_update_market_intelligence_snapshot( $snapshot );
		nexus_market_intelligence_capture_history( $snapshot );

		$runtime = nexus_get_dataforseo_runtime();
		$runtime['last_market_refresh_at']     = time();
		$runtime['last_market_refresh_status'] = empty( $errors ) ? 'ok' : 'partial';
		$runtime['last_market_refresh_error']  = empty( $errors ) ? '' : implode( ' | ', array_values( $errors ) );
		nexus_update_dataforseo_runtime( $runtime );

		return $snapshot;
	} finally {
		delete_transient( $lock_key );
	}
}

/**
 * Check whether a SERP result domain belongs to the current site.
 *
 * @param string $domain Result domain.
 * @param string $target Site target domain.
 * @return bool
 */
function nexus_market_intelligence_domain_matches_target( $domain, $target ) {
	$domain = strtolower( trim( preg_replace( '/^www\./i', '', (string) $domain ) ) );
	$target = strtolower( trim( preg_replace( '/^www\./i', '', (string) $target ) ) );

	if ( '' === $domain || '' === $target ) {
		return false;
	}

	$suffix = '.' . $target;

	return $domain === $target || ( strlen( $domain ) > strlen( $suffix ) && substr( $domain, -strlen( $suffix ) ) === $suffix );
}

/**
 * Find the site's organic position in one live SERP result.
 *
 * @param array<string, mixed> $result First DataForSEO result.
 * @return array<string, mixed>
 */
function nexus_market_intelligence_parse_live_serp_result( $result ) {
	$target = nexus_dataforseo_target_domain();
	$items  = is_array( $result['items'] ?? null ) ? $result['items'] : [];
	$top    = [];
	$own    = null;

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$type   = sanitize_key( (string) ( $item['type'] ?? '' ) );
		$domain = strtolower( preg_replace( '/^www\./i', '', (string) ( $item['domain'] ?? $item['main_domain'] ?? '' ) ) );
		$url    = esc_url_raw( (string) ( $item['url'] ?? '' ) );

		if ( 'organic' === $type && '' !== $domain && count( $top ) < 5 ) {
			$top[] = [
				'domain' => $domain,
				'rank'   => absint( $item['rank_absolute'] ?? $item['rank_group'] ?? 0 ),
				'url'    => $url,
				'title'  => sanitize_text_field( (string) ( $item['title'] ?? '' ) ),
			];
		}

		if ( null === $own && nexus_market_intelligence_domain_matches_target( $domain, $target ) ) {
			$own = [
				'type'  => $type,
				'rank'  => absint( $item['rank_absolute'] ?? $item['rank_group'] ?? 0 ),
				'url'   => $url,
				'title' => sanitize_text_field( (string) ( $item['title'] ?? '' ) ),
			];
		}
	}

	return [
		'own'       => $own,
		'top'       => $top,
		'item_types'=> array_values( array_map( 'sanitize_key', (array) ( $result['item_types'] ?? [] ) ) ),
	];
}

/**
 * Match the configured local business in a Google Maps result.
 *
 * @param array<string, mixed> $result First DataForSEO result.
 * @return array<string, mixed>
 */
function nexus_market_intelligence_parse_maps_result( $result ) {
	$config   = nexus_get_dataforseo_config();
	$target   = nexus_dataforseo_target_domain();
	$business = trim( (string) ( $config['local_business_name'] ?? '' ) );
	$needle   = '' !== $business ? mb_strtolower( remove_accents( $business ) ) : '';
	$items    = is_array( $result['items'] ?? null ) ? $result['items'] : [];
	$top      = [];
	$own      = null;

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) || 'maps_search' !== sanitize_key( (string) ( $item['type'] ?? '' ) ) ) {
			continue;
		}

		$domain = strtolower( preg_replace( '/^www\./i', '', (string) ( $item['domain'] ?? '' ) ) );
		$title  = sanitize_text_field( (string) ( $item['title'] ?? '' ) );
		$rank   = absint( $item['rank_absolute'] ?? $item['rank_group'] ?? 0 );
		$row    = [
			'rank'   => $rank,
			'title'  => $title,
			'domain' => $domain,
			'cid'    => sanitize_text_field( (string) ( $item['cid'] ?? '' ) ),
		];

		if ( count( $top ) < 5 ) {
			$top[] = $row;
		}

		$title_key = mb_strtolower( remove_accents( $title ) );
		$name_hit  = '' !== $needle && false !== strpos( $title_key, $needle );
		$domain_hit= nexus_market_intelligence_domain_matches_target( $domain, $target );

		if ( null === $own && ( $name_hit || $domain_hit ) ) {
			$own = $row;
		}
	}

	return [
		'own' => $own,
		'top' => $top,
	];
}

/**
 * Return the persisted state of one manual Live Watch job.
 *
 * @param string $mode organic|maps.
 * @return array<string, mixed>
 */
function nexus_get_market_intelligence_live_job( $mode = 'organic' ) {
	$mode  = 'maps' === sanitize_key( $mode ) ? 'maps' : 'organic';
	$jobs  = get_option( 'nexus_market_intelligence_live_jobs_v1', [] );
	$jobs  = is_array( $jobs ) ? $jobs : [];
	$state = isset( $jobs[ $mode ] ) && is_array( $jobs[ $mode ] ) ? $jobs[ $mode ] : [];

	return wp_parse_args(
		$state,
		[
			'mode'       => $mode,
			'status'     => 'idle',
			'keywords'   => [],
			'index'      => 0,
			'rows'       => [],
			'errors'     => [],
			'started_at' => 0,
			'updated_at' => 0,
			'finished_at'=> 0,
		]
	);
}

/**
 * Persist one Live Watch job state.
 *
 * @param string               $mode  organic|maps.
 * @param array<string, mixed> $state Job state.
 * @return void
 */
function nexus_update_market_intelligence_live_job( $mode, $state ) {
	$mode = 'maps' === sanitize_key( $mode ) ? 'maps' : 'organic';
	$jobs = get_option( 'nexus_market_intelligence_live_jobs_v1', [] );
	$jobs = is_array( $jobs ) ? $jobs : [];

	$state['mode']       = $mode;
	$state['updated_at'] = time();
	$jobs[ $mode ]       = $state;

	update_option( 'nexus_market_intelligence_live_jobs_v1', $jobs, false );
}

/**
 * Resolve the fixed request context for one manual live job.
 *
 * @param string $mode organic|maps.
 * @return array{location_name:string,depth:int,path:string}
 */
function nexus_market_intelligence_live_request_context( $mode ) {
	$mode   = 'maps' === sanitize_key( $mode ) ? 'maps' : 'organic';
	$config = nexus_get_dataforseo_config();

	$location_name = 'maps' === $mode
		? (string) $config['local_location_name']
		: (string) $config['organic_live_location_name'];

	if ( '' === trim( $location_name ) ) {
		$location_name = 'maps' === $mode
			? (string) $config['location_name']
			: ( '' !== trim( (string) $config['local_location_name'] ) ? (string) $config['local_location_name'] : (string) $config['location_name'] );
	}

	return [
		'location_name' => $location_name,
		'depth'         => 'maps' === $mode ? 100 : max( 10, min( 200, absint( $config['organic_live_depth'] ?? 50 ) ) ),
		'path'          => 'maps' === $mode ? 'v3/serp/google/maps/live/advanced' : 'v3/serp/google/organic/live/advanced',
	];
}

/**
 * Run exactly one keyword request.
 *
 * Keeping the worker to one external call prevents the reverse proxy or PHP
 * request from waiting for the complete eight-keyword watchlist.
 *
 * @param string               $keyword Keyword.
 * @param string               $mode organic|maps.
 * @param array<string, mixed> $context Fixed job context.
 * @return array<string, mixed>|WP_Error
 */
function nexus_market_intelligence_run_live_keyword( $keyword, $mode, $context ) {
	$config = nexus_get_dataforseo_config();
	$task   = [
		'keyword'       => trim( (string) $keyword ),
		'language_code' => (string) $config['language_code'],
		'location_name' => (string) ( $context['location_name'] ?? $config['location_name'] ),
		'device'        => 'desktop',
		'tag'           => 'nexus_market_' . $mode,
	];

	if ( 'organic' === $mode ) {
		$task['depth'] = max( 10, min( 200, absint( $context['depth'] ?? 50 ) ) );
	}

	$response = nexus_dataforseo_request( (string) $context['path'], $task, false );
	$result   = nexus_dataforseo_first_result( $response );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return [
		'keyword'       => trim( (string) $keyword ),
		'location_name' => (string) $context['location_name'],
		'depth'         => absint( $context['depth'] ?? 0 ),
		'checked_at'    => time(),
		'result'        => 'maps' === $mode
			? nexus_market_intelligence_parse_maps_result( $result )
			: nexus_market_intelligence_parse_live_serp_result( $result ),
	];
}

/**
 * Dispatch the next worker as a non-blocking loopback request.
 *
 * A cron event is scheduled as fallback so the job still progresses when a
 * host blocks WordPress loopback requests.
 *
 * @param string $mode organic|maps.
 * @return void
 */
function nexus_dispatch_market_intelligence_live_worker( $mode ) {
	$mode  = 'maps' === sanitize_key( $mode ) ? 'maps' : 'organic';
	$token = (string) get_transient( 'nexus_market_live_token_' . $mode );

	if ( '' !== $token ) {
		wp_remote_post(
			admin_url( 'admin-post.php' ),
			[
				'timeout'   => 0.01,
				'blocking'  => false,
				'body'      => [
					'action' => 'nexus_market_intelligence_live_worker',
					'mode'   => $mode,
					'token'  => $token,
				],
			]
		);
	}

	$hook = 'nexus_market_intelligence_live_background_step';
	if ( false === wp_next_scheduled( $hook, [ $mode ] ) ) {
		wp_schedule_single_event( time() + MINUTE_IN_SECONDS, $hook, [ $mode ] );
	}
}

/**
 * Queue one manual Live Watch job and return immediately.
 *
 * @param string $mode organic|maps.
 * @return array<string, mixed>|WP_Error
 */
function nexus_queue_market_intelligence_live_refresh( $mode = 'organic' ) {
	if ( ! nexus_dataforseo_has_credentials() ) {
		return new WP_Error( 'nexus_market_credentials', 'DataForSEO ist noch nicht konfiguriert.' );
	}

	$mode     = 'maps' === sanitize_key( $mode ) ? 'maps' : 'organic';
	$current  = nexus_get_market_intelligence_live_job( $mode );
	$started  = absint( $current['started_at'] ?? 0 );
	$is_fresh = $started > 0 && ( time() - $started ) < ( 15 * MINUTE_IN_SECONDS );

	if ( $is_fresh && in_array( (string) ( $current['status'] ?? '' ), [ 'queued', 'running' ], true ) ) {
		return $current;
	}

	$snapshot = nexus_get_market_intelligence_snapshot();
	$keywords = nexus_market_intelligence_manual_keywords();

	if ( empty( $keywords ) ) {
		$keywords = array_slice( (array) ( $snapshot['watch_keywords'] ?? [] ), 0, 8 );
	}

	$limit    = 'maps' === $mode ? 5 : 8;
	$keywords = array_slice( array_values( array_filter( array_map( 'strval', $keywords ) ) ), 0, $limit );

	if ( empty( $keywords ) ) {
		return new WP_Error( 'nexus_market_watchlist', 'Für den Live-Check fehlen Watchlist-Keywords.' );
	}

	$context = nexus_market_intelligence_live_request_context( $mode );
	$state   = [
		'mode'          => $mode,
		'status'        => 'queued',
		'keywords'      => $keywords,
		'index'         => 0,
		'rows'          => [],
		'errors'        => [],
		'context'       => $context,
		'started_at'    => time(),
		'updated_at'    => time(),
		'finished_at'   => 0,
	];

	nexus_update_market_intelligence_live_job( $mode, $state );

	$token = wp_generate_password( 32, false, false );
	set_transient( 'nexus_market_live_token_' . $mode, $token, 20 * MINUTE_IN_SECONDS );
	nexus_dispatch_market_intelligence_live_worker( $mode );

	return $state;
}

/**
 * Process one queued Live Watch keyword.
 *
 * @param string $mode organic|maps.
 * @return void
 */
function nexus_run_market_intelligence_live_background_step( $mode = 'organic' ) {
	$mode     = 'maps' === sanitize_key( $mode ) ? 'maps' : 'organic';
	$lock_key = 'nexus_market_live_step_lock_' . $mode;

	if ( get_transient( $lock_key ) ) {
		return;
	}

	set_transient( $lock_key, '1', 90 );

	try {
		$state = nexus_get_market_intelligence_live_job( $mode );
		if ( ! in_array( (string) ( $state['status'] ?? '' ), [ 'queued', 'running' ], true ) ) {
			return;
		}

		$keywords = is_array( $state['keywords'] ?? null ) ? array_values( $state['keywords'] ) : [];
		$index    = absint( $state['index'] ?? 0 );
		$context  = is_array( $state['context'] ?? null ) ? $state['context'] : nexus_market_intelligence_live_request_context( $mode );

		if ( $index >= count( $keywords ) ) {
			$state['status'] = empty( $state['errors'] ) ? 'complete' : ( empty( $state['rows'] ) ? 'error' : 'partial' );
		} else {
			$state['status'] = 'running';
			nexus_update_market_intelligence_live_job( $mode, $state );

			$keyword = (string) $keywords[ $index ];
			$row     = nexus_market_intelligence_run_live_keyword( $keyword, $mode, $context );

			if ( is_wp_error( $row ) ) {
				$state['errors'][ $keyword ] = $row->get_error_message();
			} else {
				$state['rows'][] = $row;
			}

			$state['index'] = $index + 1;
			if ( $state['index'] >= count( $keywords ) ) {
				$state['status'] = empty( $state['errors'] ) ? 'complete' : ( empty( $state['rows'] ) ? 'error' : 'partial' );
			}
		}

		if ( in_array( (string) $state['status'], [ 'complete', 'partial', 'error' ], true ) ) {
			$snapshot                   = nexus_get_market_intelligence_snapshot();
			$key                        = 'maps' === $mode ? 'local_maps' : 'live_serp';
			$snapshot[ $key ]           = array_values( (array) ( $state['rows'] ?? [] ) );
			$snapshot['last_live_at']   = time();
			$snapshot['live_errors']    = is_array( $state['errors'] ?? null ) ? $state['errors'] : [];
			$state['finished_at']       = time();

			nexus_update_market_intelligence_snapshot( $snapshot );
			nexus_update_market_intelligence_live_job( $mode, $state );
			delete_transient( 'nexus_market_live_token_' . $mode );
			wp_clear_scheduled_hook( 'nexus_market_intelligence_live_background_step', [ $mode ] );
			return;
		}

		nexus_update_market_intelligence_live_job( $mode, $state );

		// Release before chaining the next loopback. Otherwise a very fast
		// self-request could see the current lock and defer progress to cron.
		delete_transient( $lock_key );
		nexus_dispatch_market_intelligence_live_worker( $mode );
	} finally {
		delete_transient( $lock_key );
	}
}
add_action( 'nexus_market_intelligence_live_background_step', 'nexus_run_market_intelligence_live_background_step' );

/**
 * Handle the private loopback worker.
 *
 * @return void
 */
function nexus_handle_market_intelligence_live_worker_request() {
	$mode     = isset( $_POST['mode'] ) ? sanitize_key( (string) wp_unslash( $_POST['mode'] ) ) : 'organic';
	$mode     = 'maps' === $mode ? 'maps' : 'organic';
	$provided = isset( $_POST['token'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['token'] ) ) : '';
	$expected = (string) get_transient( 'nexus_market_live_token_' . $mode );

	if ( '' === $provided || '' === $expected || ! hash_equals( $expected, $provided ) ) {
		status_header( 403 );
		exit;
	}

	ignore_user_abort( true );
	nexus_run_market_intelligence_live_background_step( $mode );
	status_header( 204 );
	exit;
}
add_action( 'admin_post_nexus_market_intelligence_live_worker', 'nexus_handle_market_intelligence_live_worker_request' );
add_action( 'admin_post_nopriv_nexus_market_intelligence_live_worker', 'nexus_handle_market_intelligence_live_worker_request' );

/**
 * Compatibility helper for callers that explicitly need a synchronous watch.
 *
 * Admin buttons do not use this path; they queue the chunked worker above.
 *
 * @param string $mode organic|maps.
 * @return array<string, mixed>|WP_Error
 */
function nexus_refresh_market_intelligence_live( $mode = 'organic' ) {
	if ( ! nexus_dataforseo_has_credentials() ) {
		return new WP_Error( 'nexus_market_credentials', 'DataForSEO ist noch nicht konfiguriert.' );
	}

	$mode     = 'maps' === sanitize_key( $mode ) ? 'maps' : 'organic';
	$snapshot = nexus_get_market_intelligence_snapshot();
	$keywords = nexus_market_intelligence_manual_keywords();

	if ( empty( $keywords ) ) {
		$keywords = array_slice( (array) ( $snapshot['watch_keywords'] ?? [] ), 0, 8 );
	}

	$limit    = 'maps' === $mode ? 5 : 8;
	$keywords = array_slice( array_values( array_filter( array_map( 'strval', $keywords ) ) ), 0, $limit );
	if ( empty( $keywords ) ) {
		return new WP_Error( 'nexus_market_watchlist', 'Für den Live-Check fehlen Watchlist-Keywords.' );
	}

	$context = nexus_market_intelligence_live_request_context( $mode );
	$rows    = [];
	$errors  = [];

	foreach ( $keywords as $keyword ) {
		$row = nexus_market_intelligence_run_live_keyword( $keyword, $mode, $context );
		if ( is_wp_error( $row ) ) {
			$errors[ $keyword ] = $row->get_error_message();
		} else {
			$rows[] = $row;
		}
	}

	$key                      = 'maps' === $mode ? 'local_maps' : 'live_serp';
	$snapshot[ $key ]         = $rows;
	$snapshot['last_live_at'] = time();
	$snapshot['live_errors']  = $errors;
	nexus_update_market_intelligence_snapshot( $snapshot );

	return [
		'mode'   => $mode,
		'rows'   => $rows,
		'errors' => $errors,
	];
}

/**
 * Build a GSC query map for cross-layer scoring.
 *
 * @param array<string, mixed> $seo_snapshot Current SEO snapshot.
 * @return array<string, array<string, float|string>>
 */
function nexus_market_intelligence_gsc_query_map( $seo_snapshot ) {
	$map = [];

	foreach ( (array) ( $seo_snapshot['top_queries'] ?? [] ) as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$query = function_exists( 'nexus_get_seo_cockpit_row_key' )
			? nexus_get_seo_cockpit_row_key( $row, 0 )
			: (string) ( $row['keys'][0] ?? '' );
		$key = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $query )
			: mb_strtolower( trim( $query ) );

		if ( '' === $key ) {
			continue;
		}

		$map[ $key ] = [
			'query'       => $query,
			'clicks'      => (float) ( $row['clicks'] ?? 0 ),
			'impressions' => (float) ( $row['impressions'] ?? 0 ),
			'ctr'         => (float) ( $row['ctr'] ?? 0 ),
			'position'    => (float) ( $row['position'] ?? 0 ),
		];
	}

	return $map;
}

/**
 * Map an exact GSC query to the strongest currently observed URL.
 *
 * Query-page rows are first-party evidence that a page is already associated
 * with the query even when the DataForSEO ranked-keyword snapshot does not
 * contain that keyword. The URL with the most impressions wins; ties prefer
 * the better average position.
 *
 * @param array<string,mixed> $seo_snapshot Current SEO snapshot.
 * @return array<string,array<string,mixed>>
 */
function nexus_market_intelligence_gsc_query_page_map( $seo_snapshot ) {
	$map = [];

	foreach ( (array) ( $seo_snapshot['query_page_rows'] ?? [] ) as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$url = function_exists( 'nexus_get_seo_cockpit_row_key' )
			? nexus_get_seo_cockpit_row_key( $row, 0 )
			: (string) ( $row['keys'][0] ?? '' );
		$query = function_exists( 'nexus_get_seo_cockpit_row_key' )
			? nexus_get_seo_cockpit_row_key( $row, 1 )
			: (string) ( $row['keys'][1] ?? '' );
		$key = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $query )
			: mb_strtolower( trim( $query ) );

		if ( '' === $key || '' === trim( $url ) ) {
			continue;
		}

		$impressions = max( 0.0, (float) ( $row['impressions'] ?? 0.0 ) );
		$position    = max( 0.0, (float) ( $row['position'] ?? 0.0 ) );
		$existing    = is_array( $map[ $key ] ?? null ) ? $map[ $key ] : [];
		$replace     = empty( $existing )
			|| $impressions > (float) ( $existing['impressions'] ?? 0.0 )
			|| (
				$impressions === (float) ( $existing['impressions'] ?? 0.0 )
				&& $position > 0
				&& ( 0.0 === (float) ( $existing['position'] ?? 0.0 ) || $position < (float) $existing['position'] )
			);

		if ( $replace ) {
			$map[ $key ] = [
				'url'         => esc_url_raw( $url ),
				'query'       => (string) $query,
				'impressions' => $impressions,
				'clicks'      => max( 0.0, (float) ( $row['clicks'] ?? 0.0 ) ),
				'position'    => $position,
			];
		}
	}

	return $map;
}

/**
 * Build the opportunity universe from ranked keywords and keyword overview.
 *
 * Ranked Keywords alone is a biased universe: it cannot surface strategically
 * relevant demand that is configured or observed but absent from the loaded
 * ranking snapshot. Keyword Overview fills that gap without claiming that
 * absence proves an absolute Google non-ranking.
 *
 * @param array<string,mixed> $market Current market snapshot.
 * @param array<string,mixed> $seo_snapshot Current SEO snapshot.
 * @return array<int,array<string,mixed>>
 */
function nexus_market_intelligence_opportunity_candidates( $market, $seo_snapshot ) {
	$ranked    = is_array( $market['ranked']['rows'] ?? null ) ? $market['ranked']['rows'] : [];
	$overview  = is_array( $market['keyword_overview'] ?? null ) ? $market['keyword_overview'] : [];
	$gsc_pages = nexus_market_intelligence_gsc_query_page_map( $seo_snapshot );
	$index     = [];

	foreach ( $overview as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$keyword = trim( (string) ( $row['keyword'] ?? '' ) );
		$key     = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $keyword )
			: mb_strtolower( $keyword );

		if ( '' === $key ) {
			continue;
		}

		$gsc_page = is_array( $gsc_pages[ $key ] ?? null ) ? $gsc_pages[ $key ] : [];
		$row['rank_group']     = 0;
		$row['url']            = (string) ( $gsc_page['url'] ?? '' );
		$row['market_source']  = 'keyword_overview';
		$row['is_ranking_gap'] = true;
		$index[ $key ]         = $row;
	}

	foreach ( $ranked as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$keyword = trim( (string) ( $row['keyword'] ?? '' ) );
		$key     = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $keyword )
			: mb_strtolower( $keyword );

		if ( '' === $key ) {
			continue;
		}

		$merged = array_merge( is_array( $index[ $key ] ?? null ) ? $index[ $key ] : [], $row );
		$rank   = absint( $merged['rank_group'] ?? 0 );

		if ( empty( $merged['url'] ) && isset( $gsc_pages[ $key ]['url'] ) ) {
			$merged['url'] = (string) $gsc_pages[ $key ]['url'];
		}

		$merged['market_source']  = 'ranked_keywords';
		$merged['is_ranking_gap'] = 0 === $rank;
		$index[ $key ]            = $merged;
	}

	return array_values( $index );
}

/**
 * Classify one market opportunity into a small decision-oriented segment.
 *
 * Business opportunities are commercial/transactional queries or rankings on
 * high-value pages. Informational rankings stay useful, but they are separated
 * from direct acquisition work. Navigational queries are treated as
 * brand/proof visibility so third-party names cannot dominate the money queue.
 *
 * @param string $intent    DataForSEO search intent.
 * @param string $page_role SEO Cockpit page role.
 * @return array{key:string,label:string}
 */
function nexus_get_market_intelligence_segment( $intent, $page_role ) {
	$intent    = sanitize_key( (string) $intent );
	$page_role = sanitize_key( (string) $page_role );

	if ( 'navigational' === $intent ) {
		return [ 'key' => 'brand', 'label' => 'Marke & Proof' ];
	}

	if (
		in_array( $intent, [ 'commercial', 'transactional' ], true )
		|| in_array( $page_role, [ 'audit', 'service', 'contact', 'home', 'seo_subpage' ], true )
	) {
		return [ 'key' => 'business', 'label' => 'Geschäftschance' ];
	}

	if ( in_array( $page_role, [ 'blog', 'hub', 'results' ], true ) || 'informational' === $intent ) {
		return [ 'key' => 'content', 'label' => 'Content & Nachfrage' ];
	}

	return [ 'key' => 'other', 'label' => 'Beobachten' ];
}

/**
 * Join external market data with first-party GSC and CRM signals.
 *
 * @param array<string, mixed> $seo_snapshot Current SEO snapshot.
 * @param int                  $limit Max rows.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_market_intelligence_opportunities( $seo_snapshot, $limit = 12 ) {
	$market      = nexus_get_market_intelligence_snapshot();
	$candidates  = nexus_market_intelligence_opportunity_candidates( $market, $seo_snapshot );
	$gsc         = nexus_market_intelligence_gsc_query_map( $seo_snapshot );
	$leads       = is_array( $seo_snapshot['leads']['page_map'] ?? null ) ? $seo_snapshot['leads']['page_map'] : [];
	$acquisition = is_array( $seo_snapshot['acquisition'] ?? null ) ? $seo_snapshot['acquisition'] : [];
	$crm_entries = [];

	foreach ( (array) ( $acquisition['entry_rows']['current'] ?? [] ) as $entry_row ) {
		if ( ! is_array( $entry_row ) ) {
			continue;
		}
		$entry_url = function_exists( 'nexus_get_seo_cockpit_internal_attribution_url' )
			? nexus_get_seo_cockpit_internal_attribution_url( (string) ( $entry_row['key'] ?? '' ) )
			: (string) ( $entry_row['key'] ?? '' );
		if ( '' !== $entry_url ) {
			$crm_entries[ $entry_url ] = absint( $entry_row['count'] ?? 0 );
		}
	}

	$out = [];

	foreach ( $candidates as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$keyword    = (string) ( $row['keyword'] ?? '' );
		$rank       = absint( $row['rank_group'] ?? 0 );
		$volume     = max( 0.0, (float) ( $row['search_volume'] ?? 0.0 ) );
		$difficulty = isset( $row['difficulty'] ) && is_numeric( $row['difficulty'] ) ? (float) $row['difficulty'] : null;
		$key        = function_exists( 'nexus_normalize_seo_cockpit_query' ) ? nexus_normalize_seo_cockpit_query( $keyword ) : mb_strtolower( $keyword );
		$gsc_row        = isset( $gsc[ $key ] ) ? $gsc[ $key ] : [];
		$is_ranking_gap = ! empty( $row['is_ranking_gap'] );
		$market_source  = sanitize_key( (string) ( $row['market_source'] ?? ( $is_ranking_gap ? 'keyword_overview' : 'ranked_keywords' ) ) );
		$url            = (string) ( $row['url'] ?? '' );
		$lead_url = function_exists( 'nexus_get_seo_cockpit_internal_attribution_url' ) ? nexus_get_seo_cockpit_internal_attribution_url( $url ) : $url;
		$lead_row = '' !== $lead_url && is_array( $leads[ $lead_url ] ?? null ) ? $leads[ $lead_url ] : [];
		$current_leads = is_array( $lead_row['current'] ?? null ) ? $lead_row['current'] : [];
		$lifetime_leads= is_array( $lead_row['lifetime'] ?? null ) ? $lead_row['lifetime'] : [];
		$lead_count    = absint( $current_leads['requests'] ?? 0 );
		$won_count     = absint( $lifetime_leads['won'] ?? 0 );
		$crm_contacts  = '' !== $lead_url ? absint( $crm_entries[ $lead_url ] ?? 0 ) : 0;

		$context    = isset( $seo_snapshot['page_contexts'][ $lead_url ] ) && is_array( $seo_snapshot['page_contexts'][ $lead_url ] )
			? $seo_snapshot['page_contexts'][ $lead_url ]
			: ( function_exists( 'nexus_get_seo_cockpit_wp_context_for_url' ) ? nexus_get_seo_cockpit_wp_context_for_url( $lead_url ) : [] );
		$page_role   = function_exists( 'nexus_get_seo_cockpit_page_role' ) ? nexus_get_seo_cockpit_page_role( $context, $lead_url ) : 'unknown';
		$role_scores = function_exists( 'nexus_get_seo_cockpit_page_role_scores' ) ? nexus_get_seo_cockpit_page_role_scores( $page_role ) : [ 'business' => 5, 'funnel' => 3 ];
		$intent      = sanitize_key( (string) ( $row['intent'] ?? '' ) );
		$segment     = nexus_get_market_intelligence_segment( $intent, $page_role );

		$volume_score = min( 20.0, log10( $volume + 1.0 ) * 7.0 );
		$rank_score   = 0.0;
		if ( $rank >= 4 && $rank <= 10 ) {
			$rank_score = 25.0;
		} elseif ( $rank >= 11 && $rank <= 20 ) {
			$rank_score = 23.0;
		} elseif ( $rank >= 21 && $rank <= 40 ) {
			$rank_score = 16.0;
		} elseif ( $rank > 40 && $rank <= 70 ) {
			$rank_score = 8.0;
		} elseif ( $rank > 0 && $rank <= 3 ) {
			$rank_score = 10.0;
		} elseif ( $is_ranking_gap ) {
			// Missing from the bounded ranked-keyword snapshot is a gap signal,
			// not proof of an absolute non-ranking. Keep this bonus conservative.
			$rank_score = ( $volume >= 20 || (float) ( $gsc_row['impressions'] ?? 0.0 ) >= 10 ) ? 8.0 : 3.0;
		}

		$gsc_score    = min( 15.0, log10( max( 0.0, (float) ( $gsc_row['impressions'] ?? 0.0 ) ) + 1.0 ) * 5.5 );
		$intent_score = in_array( $intent, [ 'commercial', 'transactional' ], true ) ? 14.0 : ( 'informational' === $intent ? 5.0 : 1.0 );
		$business_score = min( 20.0, max( 0.0, (float) ( $role_scores['business'] ?? 5 ) ) );
		$lead_score   = min( 18.0, ( $lead_count * 3.0 ) + ( $crm_contacts * 3.0 ) + ( $won_count * 6.0 ) );
		$difficulty_penalty = null !== $difficulty ? min( 10.0, max( 0.0, ( $difficulty - 50.0 ) / 5.0 ) ) : 0.0;
		$segment_penalty    = 'brand' === (string) $segment['key'] ? 12.0 : 0.0;
		$score = max(
			0,
			min(
				100,
				(int) round( $volume_score + $rank_score + $gsc_score + $intent_score + $business_score + $lead_score - $difficulty_penalty - $segment_penalty )
			)
		);

		$action = 'Beobachten';
		if ( $is_ranking_gap && in_array( $intent, [ 'commercial', 'transactional' ], true ) && $volume >= 20 ) {
			$action = 'Ranking & Owner prüfen';
		} elseif ( $is_ranking_gap && 'informational' === $intent && $volume >= 20 ) {
			$action = 'Content-Gap prüfen';
		} elseif ( $is_ranking_gap && ( $volume > 0 || (float) ( $gsc_row['impressions'] ?? 0.0 ) > 0 ) ) {
			$action = 'Gap beobachten';
		} elseif ( $rank >= 4 && $rank <= 20 ) {
			$action = 'Top-10-Push';
		} elseif ( $rank >= 21 && $rank <= 50 && $volume >= 20 ) {
			$action = 'Seite ausbauen';
		} elseif ( $rank > 0 && $rank <= 3 && $volume >= 50 ) {
			$action = 'Position verteidigen';
		}

		$out[] = [
			'keyword'         => $keyword,
			'score'           => $score,
			'market_source'   => $market_source,
			'is_ranking_gap'  => $is_ranking_gap,
			'action'          => $action,
			'rank'            => $rank,
			'search_volume'   => $volume,
			'difficulty'      => $difficulty,
			'intent'          => $intent,
			'segment'         => (string) $segment['key'],
			'segment_label'   => (string) $segment['label'],
			'page_role'       => $page_role,
			'page_role_label' => function_exists( 'nexus_get_seo_cockpit_page_role_label' ) ? nexus_get_seo_cockpit_page_role_label( $page_role ) : $page_role,
			'url'             => $url,
			'gsc_clicks'      => (float) ( $gsc_row['clicks'] ?? 0.0 ),
			'gsc_impressions' => (float) ( $gsc_row['impressions'] ?? 0.0 ),
			'gsc_position'    => (float) ( $gsc_row['position'] ?? 0.0 ),
			'leads_current'        => $lead_count,
			'crm_contacts_current' => $crm_contacts,
			'won_lifetime'         => $won_count,
		];
	}

	$segment_weight = [
		'business' => 4,
		'content'  => 3,
		'brand'    => 2,
		'other'    => 1,
	];

	usort(
		$out,
		static function ( $left, $right ) use ( $segment_weight ) {
			$left_segment  = (string) $left['segment'];
			$right_segment = (string) $right['segment'];
			$segment_diff  = (int) ( $segment_weight[ $right_segment ] ?? 0 ) <=> (int) ( $segment_weight[ $left_segment ] ?? 0 );
			if ( 0 !== $segment_diff ) {
				return $segment_diff;
			}

			$score_diff = absint( $right['score'] ) <=> absint( $left['score'] );
			if ( 0 !== $score_diff ) {
				return $score_diff;
			}

			return (float) $right['search_volume'] <=> (float) $left['search_volume'];
		}
	);

	return array_slice( $out, 0, max( 1, $limit ) );
}

/**
 * Schedule one conservative weekly Labs refresh.
 *
 * @return void
 */
function nexus_maybe_schedule_market_intelligence_refresh() {
	$config = nexus_get_dataforseo_config();
	$hook   = nexus_market_intelligence_cron_hook();
	$next   = wp_next_scheduled( $hook );

	if ( empty( $config['auto_refresh'] ) || ! nexus_dataforseo_has_credentials() ) {
		if ( $next ) {
			wp_clear_scheduled_hook( $hook );
		}
		return;
	}

	if ( ! $next ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'weekly', $hook );
	}
}
add_action( 'admin_init', 'nexus_maybe_schedule_market_intelligence_refresh' );
add_action( 'after_switch_theme', 'nexus_maybe_schedule_market_intelligence_refresh' );

/** @return void */
function nexus_run_market_intelligence_weekly_refresh() {
	$config = nexus_get_dataforseo_config();
	if ( empty( $config['auto_refresh'] ) || ! nexus_dataforseo_has_credentials() ) {
		return;
	}

	nexus_refresh_market_intelligence( true );
}
add_action( nexus_market_intelligence_cron_hook(), 'nexus_run_market_intelligence_weekly_refresh' );
