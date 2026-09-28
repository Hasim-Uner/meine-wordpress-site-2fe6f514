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

		if ( null === $own && '' !== $domain && $target === $domain ) {
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
		$domain_hit= '' !== $domain && $target === $domain;

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
 * Run a small manual live SERP or Maps watchlist.
 *
 * @param string $mode organic|maps.
 * @return array<string, mixed>|WP_Error
 */
function nexus_refresh_market_intelligence_live( $mode = 'organic' ) {
	if ( ! nexus_dataforseo_has_credentials() ) {
		return new WP_Error( 'nexus_market_credentials', 'DataForSEO ist noch nicht konfiguriert.' );
	}

	$mode     = 'maps' === sanitize_key( $mode ) ? 'maps' : 'organic';
	$config   = nexus_get_dataforseo_config();
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

	$rows   = [];
	$errors = [];

	foreach ( $keywords as $keyword ) {
		$task = [
			'keyword'       => $keyword,
			'language_code' => (string) $config['language_code'],
			'location_name' => 'maps' === $mode && '' !== (string) $config['local_location_name']
				? (string) $config['local_location_name']
				: (string) $config['location_name'],
			'device'        => 'desktop',
			'tag'           => 'nexus_market_' . $mode,
		];

		$path     = 'maps' === $mode ? 'v3/serp/google/maps/live/advanced' : 'v3/serp/google/organic/live/advanced';
		$response = nexus_dataforseo_request( $path, $task, false );
		$result   = nexus_dataforseo_first_result( $response );

		if ( is_wp_error( $result ) ) {
			$errors[ $keyword ] = $result->get_error_message();
			continue;
		}

		$rows[] = [
			'keyword' => $keyword,
			'result'  => 'maps' === $mode
				? nexus_market_intelligence_parse_maps_result( $result )
				: nexus_market_intelligence_parse_live_serp_result( $result ),
		];
	}

	$key                  = 'maps' === $mode ? 'local_maps' : 'live_serp';
	$snapshot[ $key ]     = $rows;
	$snapshot['last_live_at'] = time();
	$snapshot['live_errors']   = $errors;
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
 * Join external market data with first-party GSC and CRM signals.
 *
 * @param array<string, mixed> $seo_snapshot Current SEO snapshot.
 * @param int                  $limit Max rows.
 * @return array<int, array<string, mixed>>
 */
function nexus_get_market_intelligence_opportunities( $seo_snapshot, $limit = 12 ) {
	$market = nexus_get_market_intelligence_snapshot();
	$ranked = is_array( $market['ranked']['rows'] ?? null ) ? $market['ranked']['rows'] : [];
	$gsc    = nexus_market_intelligence_gsc_query_map( $seo_snapshot );
	$leads  = is_array( $seo_snapshot['leads']['page_map'] ?? null ) ? $seo_snapshot['leads']['page_map'] : [];
	$out    = [];

	foreach ( $ranked as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$keyword  = (string) ( $row['keyword'] ?? '' );
		$rank     = absint( $row['rank_group'] ?? 0 );
		$volume   = max( 0.0, (float) ( $row['search_volume'] ?? 0.0 ) );
		$difficulty = isset( $row['difficulty'] ) && is_numeric( $row['difficulty'] ) ? (float) $row['difficulty'] : null;
		$key      = function_exists( 'nexus_normalize_seo_cockpit_query' ) ? nexus_normalize_seo_cockpit_query( $keyword ) : mb_strtolower( $keyword );
		$gsc_row  = isset( $gsc[ $key ] ) ? $gsc[ $key ] : [];
		$url      = (string) ( $row['url'] ?? '' );
		$lead_url = function_exists( 'nexus_get_seo_cockpit_internal_attribution_url' ) ? nexus_get_seo_cockpit_internal_attribution_url( $url ) : $url;
		$lead_row = '' !== $lead_url && is_array( $leads[ $lead_url ] ?? null ) ? $leads[ $lead_url ] : [];
		$current_leads = is_array( $lead_row['current'] ?? null ) ? $lead_row['current'] : [];
		$lifetime_leads= is_array( $lead_row['lifetime'] ?? null ) ? $lead_row['lifetime'] : [];
		$lead_count    = absint( $current_leads['requests'] ?? 0 );
		$won_count     = absint( $lifetime_leads['won'] ?? 0 );

		$volume_score = min( 25.0, log10( $volume + 1.0 ) * 8.0 );
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
		}

		$gsc_score = min( 20.0, log10( max( 0.0, (float) ( $gsc_row['impressions'] ?? 0.0 ) ) + 1.0 ) * 7.0 );
		$intent     = sanitize_key( (string) ( $row['intent'] ?? '' ) );
		$intent_score = in_array( $intent, [ 'commercial', 'transactional' ], true ) ? 10.0 : ( 'informational' === $intent ? 4.0 : 2.0 );
		$lead_score = min( 15.0, ( $lead_count * 4.0 ) + ( $won_count * 6.0 ) );
		$penalty    = null !== $difficulty ? min( 10.0, max( 0.0, ( $difficulty - 50.0 ) / 5.0 ) ) : 0.0;
		$score      = max( 0, min( 100, (int) round( $volume_score + $rank_score + $gsc_score + $intent_score + $lead_score - $penalty ) ) );

		$action = 'Beobachten';
		if ( $rank >= 4 && $rank <= 20 ) {
			$action = 'Top-10-Push';
		} elseif ( $rank >= 21 && $rank <= 50 && $volume >= 20 ) {
			$action = 'Seite ausbauen';
		} elseif ( $rank > 0 && $rank <= 3 && $volume >= 50 ) {
			$action = 'Position verteidigen';
		}

		$out[] = [
			'keyword'         => $keyword,
			'score'           => $score,
			'action'          => $action,
			'rank'            => $rank,
			'search_volume'   => $volume,
			'difficulty'      => $difficulty,
			'intent'          => $intent,
			'url'             => $url,
			'gsc_clicks'      => (float) ( $gsc_row['clicks'] ?? 0.0 ),
			'gsc_impressions' => (float) ( $gsc_row['impressions'] ?? 0.0 ),
			'gsc_position'    => (float) ( $gsc_row['position'] ?? 0.0 ),
			'leads_current'   => $lead_count,
			'won_lifetime'    => $won_count,
		];
	}

	usort(
		$out,
		static function ( $left, $right ) {
			$score_diff = absint( $right['score'] ?? 0 ) <=> absint( $left['score'] ?? 0 );
			if ( 0 !== $score_diff ) {
				return $score_diff;
			}
			return (float) ( $right['search_volume'] ?? 0.0 ) <=> (float) ( $left['search_volume'] ?? 0.0 );
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
