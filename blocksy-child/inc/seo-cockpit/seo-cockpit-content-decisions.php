<?php
/**
 * SEO Cockpit Content Decision Layer.
 *
 * Converts the existing V1.1 Content Intelligence output into an operational
 * decision surface. No provider is queried from this file; Research, GSC,
 * DataForSEO and CRM data are consumed from existing cached/local snapshots.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @return string */
function nexus_ci_decision_stylesheet_handle() {
	return 'nexus-seo-cockpit-content-decisions';
}

/**
 * Normalize a URL for cross-layer matching.
 *
 * @param string $url URL.
 * @return string
 */
function nexus_ci_decision_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}

	if ( function_exists( 'nexus_get_seo_cockpit_internal_attribution_url' ) ) {
		$internal = nexus_get_seo_cockpit_internal_attribution_url( $url );
		if ( '' !== $internal ) {
			return $internal;
		}
	}

	return function_exists( 'nexus_normalize_seo_cockpit_url' )
		? nexus_normalize_seo_cockpit_url( $url )
		: $url;
}

/**
 * Convert ranked URL-count rows into a normalized lookup map.
 *
 * @param array<int, array<string, mixed>> $rows Ranked count rows.
 * @return array<string, int>
 */
function nexus_ci_decision_count_map( $rows ) {
	$map = [];

	foreach ( (array) $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$url = nexus_ci_decision_url( (string) ( $row['key'] ?? '' ) );
		if ( '' === $url ) {
			continue;
		}

		$map[ $url ] = absint( $row['count'] ?? 0 );
	}

	return $map;
}

/**
 * Return all local/cached support layers needed for content decisions.
 *
 * No Search Console or DataForSEO network request is made here.
 *
 * @return array<string, mixed>
 */
function nexus_ci_decision_context() {
	static $context = null;

	if ( is_array( $context ) ) {
		return $context;
	}

	$ranges = function_exists( 'nexus_get_seo_cockpit_date_ranges' )
		? nexus_get_seo_cockpit_date_ranges( 28 )
		: [];

	$leads = ! empty( $ranges ) && function_exists( 'nexus_get_seo_cockpit_lead_snapshot_data' )
		? nexus_get_seo_cockpit_lead_snapshot_data( $ranges )
		: [];

	$crm = ! empty( $ranges ) && function_exists( 'nexus_get_seo_cockpit_crm_acquisition_snapshot_data' )
		? nexus_get_seo_cockpit_crm_acquisition_snapshot_data( $ranges )
		: [];

	$market = function_exists( 'nexus_get_market_intelligence_snapshot' )
		? nexus_get_market_intelligence_snapshot()
		: [];

	$crm_current_map  = is_array( $crm['entry_map']['current'] ?? null ) ? $crm['entry_map']['current'] : [];
	$crm_lifetime_map = is_array( $crm['entry_map']['lifetime'] ?? null ) ? $crm['entry_map']['lifetime'] : [];

	if ( empty( $crm_current_map ) ) {
		$crm_current_map = nexus_ci_decision_count_map( (array) ( $crm['entry_rows']['current'] ?? [] ) );
	}
	if ( empty( $crm_lifetime_map ) ) {
		$crm_lifetime_map = nexus_ci_decision_count_map( (array) ( $crm['entry_rows']['lifetime'] ?? [] ) );
	}

	$context = [
		'leads'                => is_array( $leads ) ? $leads : [],
		'crm'                  => is_array( $crm ) ? $crm : [],
		'crm_current_entries'  => $crm_current_map,
		'crm_lifetime_entries' => $crm_lifetime_map,
		'market'               => is_array( $market ) ? $market : [],
	];

	return $context;
}

/**
 * Aggregate the cached GSC query×page rows into query totals suitable for the
 * existing Market Intelligence opportunity engine.
 *
 * This function never calls Search Console. It only reads the snapshot already
 * maintained for Content Intelligence.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_ci_decision_cached_gsc_query_totals() {
	$snapshot = function_exists( 'nexus_ci_cached_gsc_snapshot' ) ? nexus_ci_cached_gsc_snapshot() : [];
	$rows     = is_array( $snapshot['query_page_rows'] ?? null ) ? $snapshot['query_page_rows'] : [];
	$totals   = [];

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$query = function_exists( 'nexus_get_seo_cockpit_row_key' )
			? nexus_get_seo_cockpit_row_key( $row, 1 )
			: (string) ( $row['keys'][1] ?? '' );

		$query = trim( (string) $query );
		if ( '' === $query ) {
			continue;
		}

		$key = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $query )
			: mb_strtolower( $query );

		if ( '' === $key ) {
			continue;
		}

		$impressions = max( 0.0, (float) ( $row['impressions'] ?? 0.0 ) );
		$clicks      = max( 0.0, (float) ( $row['clicks'] ?? 0.0 ) );
		$position    = max( 0.0, (float) ( $row['position'] ?? 0.0 ) );
		$weight      = $impressions > 0 ? $impressions : 1.0;

		if ( ! isset( $totals[ $key ] ) ) {
			$totals[ $key ] = [
				'query'       => $query,
				'impressions' => 0.0,
				'clicks'      => 0.0,
				'position_w'  => 0.0,
				'position_n'  => 0.0,
			];
		}

		$totals[ $key ]['impressions'] += $impressions;
		$totals[ $key ]['clicks']      += $clicks;
		$totals[ $key ]['position_w']  += $position * $weight;
		$totals[ $key ]['position_n']  += $weight;
	}

	$out = [];

	foreach ( $totals as $total ) {
		$impressions = (float) $total['impressions'];
		$clicks      = (float) $total['clicks'];
		$position_n  = (float) $total['position_n'];

		$out[] = [
			'keys'        => [ (string) $total['query'] ],
			'clicks'      => $clicks,
			'impressions' => $impressions,
			'ctr'         => $impressions > 0 ? $clicks / $impressions : 0.0,
			'position'    => $position_n > 0 ? (float) $total['position_w'] / $position_n : 0.0,
		];
	}

	usort(
		$out,
		static function ( $left, $right ) {
			return (float) $right['impressions'] <=> (float) $left['impressions'];
		}
	);

	return array_slice( $out, 0, 250 );
}

/**
 * Return a deterministic next step for a direct Market Intelligence decision.
 *
 * @param string $action Market action.
 * @param string $segment Market segment.
 * @return string
 */
function nexus_ci_decision_market_next_step( $action, $segment ) {
	$action  = trim( (string) $action );
	$segment = sanitize_key( (string) $segment );

	if ( 'Top-10-Push' === $action ) {
		return 'Aktuelle SERP und Zielseite gegeneinander prüfen. Danach nur die belegten Content-, Snippet- und internen Link-Lücken schließen.';
	}

	if ( 'Seite ausbauen' === $action ) {
		return 'Suchintention und konkurrierende Ergebnisse prüfen. Anschließend die bestehende Zielseite gezielt um fehlende kaufnahe Inhalte und interne Links erweitern.';
	}

	if ( 'Position verteidigen' === $action ) {
		return 'Aktualität, Belege und interne Verlinkung sichern. Nur eingreifen, wenn GSC oder die Live-SERP einen konkreten Verlust zeigt.';
	}

	if ( 'brand' === $segment ) {
		return 'Als Marken-/Proof-Sichtbarkeit beobachten. Erst priorisieren, wenn daraus qualifizierter Traffic, Leads oder strategische Autorität nachweisbar werden.';
	}

	return 'Signal beobachten und erst bei einer klaren Ranking-, Nachfrage- oder Lead-Veränderung in die Arbeitsqueue ziehen.';
}

/**
 * Convert direct Market Intelligence opportunities into the same operational
 * decision shape as Research/Content Intelligence.
 *
 * @param array<string, mixed> $context Local/cached support layers.
 * @return array<int, array<string, mixed>>
 */
function nexus_ci_market_decision_items( $context ) {
	if ( ! function_exists( 'nexus_get_market_intelligence_opportunities' ) ) {
		return [];
	}

	$top_queries = nexus_ci_decision_cached_gsc_query_totals();
	$seo_context = [
		'top_queries'  => $top_queries,
		'leads'        => is_array( $context['leads'] ?? null ) ? $context['leads'] : [],
		'acquisition'  => is_array( $context['crm'] ?? null ) ? $context['crm'] : [],
		'page_contexts'=> [],
	];

	$rows  = nexus_get_market_intelligence_opportunities( $seo_context, 20 );
	$items = [];

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$score         = max( 0, min( 100, absint( $row['score'] ?? 0 ) ) );
		$segment       = sanitize_key( (string) ( $row['segment'] ?? 'other' ) );
		$segment_label = (string) ( $row['segment_label'] ?? 'Beobachten' );
		$action        = (string) ( $row['action'] ?? 'Beobachten' );
		$target_url    = nexus_ci_decision_url( (string) ( $row['url'] ?? '' ) );
		$target_ctx    = '' !== $target_url && function_exists( 'nexus_get_seo_cockpit_wp_context_for_url' )
			? nexus_get_seo_cockpit_wp_context_for_url( $target_url )
			: [];
		$target_label  = trim( (string) ( $target_ctx['post_title'] ?? '' ) );

		if ( '' === $target_label ) {
			$target_label = $target_url;
		}

		if ( 'business' === $segment && 'Beobachten' !== $action && $score >= 55 ) {
			$lane       = 'now';
			$lane_label = 'Jetzt tun';
		} elseif ( 'brand' !== $segment && 'Beobachten' !== $action ) {
			$lane       = 'plan';
			$lane_label = 'Prüfen & planen';
		} else {
			$lane       = 'observe';
			$lane_label = 'Beobachten';
		}

		$why = [ $segment_label ];

		if ( ! empty( $row['intent'] ) ) {
			$why[] = 'Intent ' . (string) $row['intent'];
		}
		if ( (float) ( $row['gsc_impressions'] ?? 0 ) > 0 ) {
			$why[] = 'eigene GSC-Nachfrage';
		}
		if ( absint( $row['crm_contacts_current'] ?? 0 ) > 0 || absint( $row['leads_current'] ?? 0 ) > 0 ) {
			$why[] = 'First-Party-Lead-Signal';
		}

		$sources = [ 'DataForSEO', 'GSC' ];
		if ( '' !== $target_url ) {
			$sources[] = 'WordPress';
		}
		if ( absint( $row['crm_contacts_current'] ?? 0 ) > 0 || absint( $row['leads_current'] ?? 0 ) > 0 ) {
			$sources[] = 'CRM';
		}

		$decision = [
			'score'          => $score,
			'lane'           => $lane,
			'lane_label'     => $lane_label,
			'action'         => sanitize_key( $action ),
			'action_label'   => $action,
			'why'            => implode( ' · ', array_filter( $why ) ),
			'next_step'      => nexus_ci_decision_market_next_step( $action, $segment ),
			'target_url'     => $target_url,
			'target_label'   => $target_label,
			'page_role'      => sanitize_key( (string) ( $row['page_role'] ?? 'unknown' ) ),
			'page_role_label'=> (string) ( $row['page_role_label'] ?? 'Sonstiges' ),
			'impressions'    => (float) ( $row['gsc_impressions'] ?? 0.0 ),
			'position'       => (float) ( $row['gsc_position'] ?? 0.0 ),
			'market_score'   => $score,
			'seo_score'      => 0,
			'content_fit'    => 0,
			'audit_current'  => absint( $row['leads_current'] ?? 0 ),
			'audit_won'      => absint( $row['won_lifetime'] ?? 0 ),
			'crm_current'    => absint( $row['crm_contacts_current'] ?? 0 ),
			'crm_lifetime'   => 0,
			'market_support' => [
				'available' => true,
				'best'      => [
					'keyword'       => (string) ( $row['keyword'] ?? '' ),
					'search_volume' => (float) ( $row['search_volume'] ?? 0.0 ),
					'rank'          => absint( $row['rank'] ?? 0 ),
					'intent'        => sanitize_key( (string) ( $row['intent'] ?? '' ) ),
				],
			],
			'direct_market'  => [
				'keyword'        => (string) ( $row['keyword'] ?? '' ),
				'search_volume'  => (float) ( $row['search_volume'] ?? 0.0 ),
				'rank'           => absint( $row['rank'] ?? 0 ),
				'difficulty'     => isset( $row['difficulty'] ) && is_numeric( $row['difficulty'] ) ? (float) $row['difficulty'] : null,
				'intent'         => sanitize_key( (string) ( $row['intent'] ?? '' ) ),
				'segment'        => $segment,
				'segment_label'  => $segment_label,
			],
			'sources'        => array_values( array_unique( $sources ) ),
		];

		$items[] = [
			'source_type'  => 'market',
			'provider'     => 'dataforseo',
			'period'       => 'aktuell',
			'title'        => (string) ( $row['keyword'] ?? 'Keyword-Chance' ),
			'context'      => 'Direktes Suchmarkt-Signal aus DataForSEO, verbunden mit First-Party-Daten.',
			'label'        => 'Suchvolumen',
			'value'        => (float) ( $row['search_volume'] ?? 0.0 ),
			'unit'         => 'Suchen/Monat',
			'change_label' => '—',
			'v11'          => [
				'profile'     => [ 'label' => $segment_label ],
				'top_queries' => (float) ( $row['gsc_impressions'] ?? 0 ) > 0
					? [ [ 'query' => (string) ( $row['keyword'] ?? '' ), 'impressions' => (float) $row['gsc_impressions'] ] ]
					: [],
			],
			'decision'     => $decision,
		];
	}

	return $items;
}

/**
 * Return DataForSEO support for a target URL / directly matching query.
 *
 * @param string               $target_url Target URL.
 * @param array<int, mixed>    $top_queries Matching GSC queries.
 * @param array<string, mixed> $market Market Intelligence snapshot.
 * @return array<string, mixed>
 */
function nexus_ci_decision_market_support( $target_url, $top_queries, $market ) {
	$rows       = is_array( $market['ranked']['rows'] ?? null ) ? $market['ranked']['rows'] : [];
	$target_url = nexus_ci_decision_url( $target_url );
	$query_keys = [];

	foreach ( (array) $top_queries as $query ) {
		if ( ! is_array( $query ) ) {
			continue;
		}
		$value = trim( (string) ( $query['query'] ?? '' ) );
		if ( '' === $value ) {
			continue;
		}
		$key = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $value )
			: mb_strtolower( $value );
		if ( '' !== $key ) {
			$query_keys[ $key ] = true;
		}
	}

	$matches = [];

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$row_url = nexus_ci_decision_url( (string) ( $row['url'] ?? '' ) );
		$keyword = trim( (string) ( $row['keyword'] ?? '' ) );
		$key     = function_exists( 'nexus_normalize_seo_cockpit_query' )
			? nexus_normalize_seo_cockpit_query( $keyword )
			: mb_strtolower( $keyword );

		$url_match   = '' !== $target_url && '' !== $row_url && $target_url === $row_url;
		$query_match = '' !== $key && isset( $query_keys[ $key ] );

		if ( ! $url_match && ! $query_match ) {
			continue;
		}

		$matches[] = [
			'keyword'       => $keyword,
			'search_volume' => is_numeric( $row['search_volume'] ?? null ) ? (float) $row['search_volume'] : 0.0,
			'rank'          => absint( $row['rank_group'] ?? 0 ),
			'intent'        => sanitize_key( (string) ( $row['intent'] ?? '' ) ),
		];
	}

	usort(
		$matches,
		static function ( $left, $right ) {
			return (float) $right['search_volume'] <=> (float) $left['search_volume'];
		}
	);

	return [
		'available'     => ! empty( $matches ),
		'matched_count' => count( $matches ),
		'best'          => ! empty( $matches ) ? $matches[0] : [],
	];
}

/**
 * Resolve a deterministic next action.
 *
 * @param string $action Existing V1.1 action.
 * @return string
 */
function nexus_ci_decision_next_step( $action ) {
	$action = sanitize_key( (string) $action );

	$steps = [
		'expand' => 'Bestehende Seite um den neuen Primärdaten-Abschnitt erweitern, passende Queries sauber einbauen und interne Links zur Seite prüfen.',
		'update' => 'Primärdaten, Datumsstand und Kernaussage der bestehenden Seite aktualisieren; anschließend Snippet und Einleitung gegen die passenden Queries prüfen.',
		'create_review' => 'SERP und Kannibalisierung gegen bestehende Seiten prüfen. Nur wenn kein passendes Ziel existiert, ein Briefing für eine neue Analyse anlegen.',
		'watch' => 'Keine Content-Änderung auslösen. Beim nächsten Daten-Refresh erneut prüfen und erst bei ausreichender Suchnachfrage eskalieren.',
	];

	return $steps[ $action ] ?? $steps['watch'];
}

/**
 * Build one decision payload from a V1.1 opportunity.
 *
 * @param array<string, mixed> $item Existing Content Intelligence item.
 * @param array<string, mixed> $context Local/cached support layers.
 * @return array<string, mixed>
 */
function nexus_ci_build_content_decision( $item, $context ) {
	$match        = is_array( $item['v11'] ?? null ) ? $item['v11'] : [];
	$best         = is_array( $match['best_page'] ?? null ) ? $match['best_page'] : [];
	$target_ctx   = is_array( $match['target_context'] ?? null ) ? $match['target_context'] : [];
	$target_url   = nexus_ci_decision_url( (string) ( $best['url'] ?? '' ) );
	$action       = sanitize_key( (string) ( $match['action'] ?? 'watch' ) );
	$market_score = max( 0, min( 100, absint( $item['market_score'] ?? 0 ) ) );
	$seo_score    = max( 0, min( 100, absint( $match['seo_score'] ?? 0 ) ) );
	$content_fit  = max( 0, min( 100, absint( $match['content_fit'] ?? 0 ) ) );
	$impressions  = max( 0.0, (float) ( $match['impressions'] ?? 0.0 ) );
	$position     = is_numeric( $best['position'] ?? null ) ? (float) $best['position'] : 0.0;

	$page_role = '' !== $target_url && function_exists( 'nexus_get_seo_cockpit_page_role' )
		? nexus_get_seo_cockpit_page_role( $target_ctx, $target_url )
		: 'unknown';

	$role_scores = function_exists( 'nexus_get_seo_cockpit_page_role_scores' )
		? nexus_get_seo_cockpit_page_role_scores( $page_role )
		: [ 'business' => 5, 'funnel' => 3 ];

	$lead_page = '' !== $target_url && is_array( $context['leads']['page_map'][ $target_url ] ?? null )
		? $context['leads']['page_map'][ $target_url ]
		: [];

	$audit_current = absint( $lead_page['current']['requests'] ?? 0 );
	$audit_won     = absint( $lead_page['lifetime']['won'] ?? 0 );
	$crm_current   = '' !== $target_url ? absint( $context['crm_current_entries'][ $target_url ] ?? 0 ) : 0;
	$crm_lifetime  = '' !== $target_url ? absint( $context['crm_lifetime_entries'][ $target_url ] ?? 0 ) : 0;

	$market_support = nexus_ci_decision_market_support(
		$target_url,
		(array) ( $match['top_queries'] ?? [] ),
		is_array( $context['market'] ?? null ) ? $context['market'] : []
	);
	$market_best = is_array( $market_support['best'] ?? null ) ? $market_support['best'] : [];
	$dfs_volume  = is_numeric( $market_best['search_volume'] ?? null ) ? (float) $market_best['search_volume'] : 0.0;

	$action_base = [
		'expand'        => 26.0,
		'update'        => 23.0,
		'create_review' => 20.0,
		'watch'         => 0.0,
	];
	$base = (float) ( $action_base[ $action ] ?? 0.0 );

	$score  = $base;
	$score += $seo_score * 0.22;
	$score += $market_score * 0.12;
	$score += min( 20.0, max( 0.0, (float) ( $role_scores['business'] ?? 5 ) ) );
	$score += min( 12.0, ( $audit_current * 2.5 ) + ( $crm_current * 3.0 ) + ( $audit_won * 5.0 ) );
	$score += min( 8.0, log10( $dfs_volume + 1.0 ) * 3.0 );

	if ( in_array( $action, [ 'expand', 'update' ], true ) ) {
		$score += $content_fit * 0.08;
	} elseif ( 'create_review' === $action ) {
		$score += ( 100 - $content_fit ) * 0.04;
	}

	$score = max( 0, min( 100, (int) round( $score ) ) );

	if ( 'watch' !== $action && $score >= 55 ) {
		$lane       = 'now';
		$lane_label = 'Jetzt tun';
	} elseif ( 'watch' !== $action ) {
		$lane       = 'plan';
		$lane_label = 'Prüfen & planen';
	} else {
		$lane       = 'observe';
		$lane_label = 'Beobachten';
	}

	$sources = [ 'Research', 'GSC' ];
	if ( '' !== $target_url ) {
		$sources[] = 'WordPress';
	}
	if ( ! empty( $market_support['available'] ) ) {
		$sources[] = 'DataForSEO';
	}
	if ( $audit_current > 0 || $crm_current > 0 || $crm_lifetime > 0 ) {
		$sources[] = 'CRM';
	}

	$target_label = trim( (string) ( $target_ctx['post_title'] ?? '' ) );
	if ( '' === $target_label ) {
		$target_label = $target_url;
	}

	return [
		'score'          => $score,
		'lane'           => $lane,
		'lane_label'     => $lane_label,
		'action'         => $action,
		'action_label'   => (string) ( $match['action_label'] ?? 'Marktbeobachtung' ),
		'why'            => (string) ( $match['action_reason'] ?? '' ),
		'next_step'      => nexus_ci_decision_next_step( $action ),
		'target_url'     => $target_url,
		'target_label'   => $target_label,
		'page_role'      => $page_role,
		'page_role_label'=> function_exists( 'nexus_get_seo_cockpit_page_role_label' ) ? nexus_get_seo_cockpit_page_role_label( $page_role ) : $page_role,
		'impressions'    => $impressions,
		'position'       => $position,
		'market_score'   => $market_score,
		'seo_score'      => $seo_score,
		'content_fit'    => $content_fit,
		'audit_current'  => $audit_current,
		'audit_won'      => $audit_won,
		'crm_current'    => $crm_current,
		'crm_lifetime'   => $crm_lifetime,
		'market_support' => $market_support,
		'sources'        => array_values( array_unique( $sources ) ),
	];
}

/**
 * Enrich and sort V1.1 opportunities into operational lanes.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_ci_content_decisions() {
	$items   = function_exists( 'nexus_ci_v11_opportunities' ) ? nexus_ci_v11_opportunities() : [];
	$context = nexus_ci_decision_context();

	foreach ( $items as $index => $item ) {
		$items[ $index ]['source_type'] = 'research';
		$items[ $index ]['decision']    = nexus_ci_build_content_decision( $item, $context );
	}

	foreach ( nexus_ci_market_decision_items( $context ) as $market_item ) {
		$items[] = $market_item;
	}

	$lane_weight = [ 'now' => 3, 'plan' => 2, 'observe' => 1 ];

	usort(
		$items,
		static function ( $left, $right ) use ( $lane_weight ) {
			$left_decision  = is_array( $left['decision'] ) ? $left['decision'] : [];
			$right_decision = is_array( $right['decision'] ) ? $right['decision'] : [];

			$left_lane  = sanitize_key( (string) ( $left_decision['lane'] ?? 'observe' ) );
			$right_lane = sanitize_key( (string) ( $right_decision['lane'] ?? 'observe' ) );
			$lane_diff  = (int) ( $lane_weight[ $right_lane ] ?? 0 ) <=> (int) ( $lane_weight[ $left_lane ] ?? 0 );

			if ( 0 !== $lane_diff ) {
				return $lane_diff;
			}

			return absint( $right_decision['score'] ?? 0 ) <=> absint( $left_decision['score'] ?? 0 );
		}
	);

	return $items;
}

/**
 * Group decisions by lane.
 *
 * @param array<int, array<string, mixed>> $items Decision items.
 * @return array<string, array<int, array<string, mixed>>>
 */
function nexus_ci_content_decision_lanes( $items ) {
	$lanes = [ 'now' => [], 'plan' => [], 'observe' => [] ];

	foreach ( $items as $item ) {
		$decision = is_array( $item['decision'] ?? null ) ? $item['decision'] : [];
		$lane     = sanitize_key( (string) ( $decision['lane'] ?? 'observe' ) );
		if ( ! isset( $lanes[ $lane ] ) ) {
			$lane = 'observe';
		}
		$lanes[ $lane ][] = $item;
	}

	return $lanes;
}

/**
 * Register the simplified decision surface under the existing stable slug.
 *
 * @return void
 */
function nexus_ci_register_content_decision_page() {
	remove_submenu_page( nexus_get_seo_cockpit_menu_slug(), nexus_ci_admin_slug() );

	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Content-Chancen',
		'Content-Chancen',
		nexus_get_seo_cockpit_view_cap(),
		nexus_ci_admin_slug(),
		'nexus_ci_render_content_decision_page'
	);
}
add_action( 'admin_menu', 'nexus_ci_register_content_decision_page', 43 );

/**
 * Detach legacy renderers sharing the same submenu hook.
 *
 * @return void
 */
function nexus_ci_detach_previous_content_renderers() {
	if ( ! function_exists( 'get_plugin_page_hookname' ) ) {
		return;
	}

	$hook = get_plugin_page_hookname( nexus_ci_admin_slug(), nexus_get_seo_cockpit_menu_slug() );
	if ( ! is_string( $hook ) || '' === $hook ) {
		return;
	}

	remove_action( $hook, 'nexus_ci_render_admin_page' );
	remove_action( $hook, 'nexus_ci_v11_render_admin_page' );
}
add_action( 'admin_menu', 'nexus_ci_detach_previous_content_renderers', 100 );

/**
 * Load the decision-layer stylesheet.
 *
 * @return void
 */
function nexus_ci_enqueue_content_decision_assets() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( ! in_array( $page, [ nexus_ci_admin_slug(), nexus_get_seo_cockpit_menu_slug() ], true ) ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/seo-cockpit-content-decisions.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		nexus_ci_decision_stylesheet_handle(),
		get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-content-decisions.css',
		[ 'nexus-seo-cockpit-research' ],
		(string) filemtime( $path )
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_ci_enqueue_content_decision_assets', 40 );

/**
 * Render compact evidence badges.
 *
 * @param array<string, mixed> $decision Decision.
 * @return void
 */
function nexus_ci_render_content_decision_evidence( $decision ) {
	$market_support = is_array( $decision['market_support'] ?? null ) ? $decision['market_support'] : [];
	$market_best    = is_array( $market_support['best'] ?? null ) ? $market_support['best'] : [];
	$direct_market  = is_array( $decision['direct_market'] ?? null ) ? $decision['direct_market'] : [];
	?>
	<div class="nsc-decision-evidence">
		<?php if ( ! empty( $direct_market ) && absint( $direct_market['rank'] ?? 0 ) > 0 ) : ?><span>DataForSEO Pos. <?php echo esc_html( number_format_i18n( absint( $direct_market['rank'] ), 0 ) ); ?></span><?php endif; ?>
		<?php if ( (float) ( $decision['impressions'] ?? 0 ) > 0 ) : ?><span>GSC <?php echo esc_html( number_format_i18n( (float) $decision['impressions'], 0 ) ); ?> Impr.</span><?php endif; ?>
		<?php if ( (float) ( $decision['position'] ?? 0 ) > 0 ) : ?><span>GSC Ø Pos. <?php echo esc_html( number_format_i18n( (float) $decision['position'], 1 ) ); ?></span><?php endif; ?>
		<?php if ( ! empty( $decision['page_role_label'] ) && 'Sonstiges' !== (string) $decision['page_role_label'] ) : ?><span><?php echo esc_html( (string) $decision['page_role_label'] ); ?></span><?php endif; ?>
		<?php if ( ! empty( $market_support['available'] ) ) : ?><span>DataForSEO Vol. <?php echo esc_html( number_format_i18n( (float) ( $market_best['search_volume'] ?? 0 ), 0 ) ); ?></span><?php endif; ?>
		<?php if ( absint( $decision['crm_current'] ?? 0 ) > 0 ) : ?><span><?php echo esc_html( number_format_i18n( absint( $decision['crm_current'] ) ) ); ?> CRM-Kontakte</span><?php endif; ?>
		<?php if ( absint( $decision['audit_current'] ?? 0 ) > 0 ) : ?><span><?php echo esc_html( number_format_i18n( absint( $decision['audit_current'] ) ) ); ?> Audit-Leads</span><?php endif; ?>
	</div>
	<?php
}

/**
 * Render one decision card.
 *
 * @param array<string, mixed> $item Decision item.
 * @return void
 */
function nexus_ci_render_content_decision_card( $item ) {
	$decision = is_array( $item['decision'] ?? null ) ? $item['decision'] : [];
	$match    = is_array( $item['v11'] ?? null ) ? $item['v11'] : [];
	$profile  = is_array( $match['profile'] ?? null ) ? $match['profile'] : [];
	$sources  = array_values( array_filter( array_map( 'strval', (array) ( $decision['sources'] ?? [] ) ) ) );
	$lane     = sanitize_key( (string) ( $decision['lane'] ?? 'observe' ) );
	?>
	<article class="nsc-decision-card is-<?php echo esc_attr( $lane ); ?>">
		<div class="nsc-decision-score" aria-label="Entscheidungsscore <?php echo esc_attr( (string) absint( $decision['score'] ?? 0 ) ); ?>">
			<strong><?php echo esc_html( (string) absint( $decision['score'] ?? 0 ) ); ?></strong>
			<span><?php echo esc_html( (string) ( $decision['lane_label'] ?? 'Beobachten' ) ); ?></span>
		</div>

		<div class="nsc-decision-card__body">
			<div class="nsc-decision-card__topline">
				<span><?php echo esc_html( strtoupper( str_replace( '_', ' ', (string) ( $item['provider'] ?? 'Research' ) ) ) ); ?></span>
				<?php if ( ! empty( $item['period'] ) ) : ?><span><?php echo esc_html( (string) $item['period'] ); ?></span><?php endif; ?>
				<?php if ( ! empty( $profile['label'] ) ) : ?><span><?php echo esc_html( (string) $profile['label'] ); ?></span><?php endif; ?>
			</div>

			<h3><?php echo esc_html( (string) ( $decision['action_label'] ?? 'Marktbeobachtung' ) ); ?></h3>
			<p class="nsc-decision-signal"><?php echo esc_html( (string) ( $item['title'] ?? '' ) ); ?></p>

			<?php nexus_ci_render_content_decision_evidence( $decision ); ?>

			<div class="nsc-decision-logic">
				<div>
					<span>Warum jetzt</span>
					<p><?php echo esc_html( (string) ( $decision['why'] ?? '' ) ); ?></p>
				</div>
				<div class="is-next">
					<span>Nächster Schritt</span>
					<p><?php echo esc_html( (string) ( $decision['next_step'] ?? '' ) ); ?></p>
				</div>
			</div>

			<div class="nsc-decision-target">
				<?php if ( ! empty( $decision['target_url'] ) ) : ?>
					<span>Zielseite</span>
					<a href="<?php echo esc_url( (string) $decision['target_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( (string) ( $decision['target_label'] ?: $decision['target_url'] ) ); ?></a>
				<?php else : ?>
					<span>Noch keine bestätigte Zielseite</span>
				<?php endif; ?>
			</div>

			<details class="nsc-decision-details">
				<summary>Datenbasis anzeigen</summary>
				<?php if ( 'market' === (string) ( $item['source_type'] ?? '' ) ) : ?>
					<?php $direct_market = is_array( $decision['direct_market'] ?? null ) ? $decision['direct_market'] : []; ?>
					<div class="nsc-decision-detail-grid">
						<div><span>Opportunity</span><strong><?php echo esc_html( (string) absint( $decision['score'] ?? 0 ) ); ?>/100</strong></div>
						<div><span>DataForSEO Pos.</span><strong><?php echo esc_html( absint( $direct_market['rank'] ?? 0 ) > 0 ? number_format_i18n( absint( $direct_market['rank'] ), 0 ) : '—' ); ?></strong></div>
						<div><span>Suchvolumen</span><strong><?php echo esc_html( number_format_i18n( (float) ( $direct_market['search_volume'] ?? 0 ), 0 ) ); ?></strong></div>
						<div><span>Intent</span><strong><?php echo esc_html( '' !== (string) ( $direct_market['intent'] ?? '' ) ? (string) $direct_market['intent'] : '—' ); ?></strong></div>
						<div><span>Segment</span><strong><?php echo esc_html( (string) ( $direct_market['segment_label'] ?? 'Beobachten' ) ); ?></strong></div>
						<div><span>Quellen</span><strong><?php echo esc_html( ! empty( $sources ) ? implode( ' · ', $sources ) : 'DataForSEO' ); ?></strong></div>
					</div>
					<p class="nsc-decision-source-value">Direktes Suchmarktsignal. Es wird nur aus vorhandenen DataForSEO-, GSC-, WordPress- und CRM-Snapshots berechnet.</p>
				<?php else : ?>
					<div class="nsc-decision-detail-grid">
						<div><span>Marktsignal</span><strong><?php echo esc_html( (string) absint( $decision['market_score'] ?? 0 ) ); ?>/100</strong></div>
						<div><span>SEO-Chance</span><strong><?php echo esc_html( (string) absint( $decision['seo_score'] ?? 0 ) ); ?>/100</strong></div>
						<div><span>Content-Fit</span><strong><?php echo esc_html( (string) absint( $decision['content_fit'] ?? 0 ) ); ?>/100</strong></div>
						<div><span>Quellen</span><strong><?php echo esc_html( ! empty( $sources ) ? implode( ' · ', $sources ) : 'Research' ); ?></strong></div>
					</div>

					<p class="nsc-decision-source-value">
						<?php echo esc_html( (string) ( $item['label'] ?? '' ) . ': ' . number_format_i18n( nexus_ci_number( $item['value'] ?? null ), 1 ) . ' ' . (string) ( $item['unit'] ?? '' ) . ' · Veränderung ' . (string) ( $item['change_label'] ?? '—' ) ); ?>
					</p>

					<?php foreach ( (array) ( $match['top_queries'] ?? [] ) as $query ) : ?>
						<span class="nexus-ci-query"><?php echo esc_html( (string) ( $query['query'] ?? '' ) . ' · ' . number_format_i18n( nexus_ci_number( $query['impressions'] ?? null ), 0 ) . ' Impr.' ); ?></span>
					<?php endforeach; ?>

					<?php if ( ! empty( $match['commercial_conflict'] ) ) : ?>
						<p class="nexus-ci-v11-note">Die aktuell rankende Seite ist kommerziell ausgerichtet und wird deshalb nicht automatisch als Ziel für einen Markt-/Datenartikel bestätigt.</p>
					<?php endif; ?>
				<?php endif; ?>
			</details>
		</div>
	</article>
	<?php
}

/**
 * Render one operational lane.
 *
 * @param string                           $key Lane key.
 * @param string                           $title Lane title.
 * @param string                           $description Lane description.
 * @param array<int, array<string, mixed>> $items Lane items.
 * @return void
 */
function nexus_ci_render_content_decision_lane( $key, $title, $description, $items ) {
	?>
	<section class="nsc-decision-lane is-<?php echo esc_attr( sanitize_key( $key ) ); ?>">
		<div class="nsc-decision-lane__head">
			<div>
				<p class="nexus-seo-cockpit__eyebrow"><?php echo esc_html( $title ); ?></p>
				<h2><?php echo esc_html( $description ); ?></h2>
			</div>
			<strong><?php echo esc_html( number_format_i18n( count( $items ) ) ); ?></strong>
		</div>

		<?php if ( empty( $items ) ) : ?>
			<p class="nsc-decision-empty">Aktuell keine Einträge in dieser Stufe.</p>
		<?php else : ?>
			<div class="nsc-decision-list">
				<?php foreach ( $items as $item ) : ?>
					<?php nexus_ci_render_content_decision_card( $item ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Render the Content Decision Layer.
 *
 * @return void
 */
function nexus_ci_render_content_decision_page() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	$items = nexus_ci_content_decisions();
	$lanes = nexus_ci_content_decision_lanes( $items );
	$last  = absint( get_option( 'nexus_content_intelligence_last_capture', 0 ) );
	?>
	<div class="wrap nexus-seo-cockpit nsc-decision">
		<p class="nexus-seo-cockpit__eyebrow">Decision Layer · Content</p>

		<header class="nsc-decision-hero">
			<div>
				<h1>Content-Chancen</h1>
				<p>Hier steht nicht mehr die Datenquelle im Mittelpunkt, sondern die Entscheidung: <strong>Was lohnt sich jetzt, warum und was ist der nächste Schritt?</strong></p>
			</div>
			<div class="nsc-decision-hero__actions">
				<?php if ( nexus_current_user_can_manage_seo_cockpit() ) : ?>
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_ci_refresh' ) ); ?>">
						<?php wp_nonce_field( 'nexus_ci_refresh' ); ?>
						<button type="submit" class="button button-primary">Daten aktualisieren</button>
					</form>
				<?php endif; ?>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . nexus_get_seo_cockpit_research_slug() ) ); ?>">Datenbasis öffnen</a>
			</div>
		</header>

		<div class="nsc-decision-summary">
			<article class="is-now"><span>Jetzt tun</span><strong><?php echo esc_html( number_format_i18n( count( $lanes['now'] ) ) ); ?></strong><small>belastbare Maßnahme</small></article>
			<article class="is-plan"><span>Prüfen & planen</span><strong><?php echo esc_html( number_format_i18n( count( $lanes['plan'] ) ) ); ?></strong><small>noch eine Entscheidung nötig</small></article>
			<article class="is-observe"><span>Beobachten</span><strong><?php echo esc_html( number_format_i18n( count( $lanes['observe'] ) ) ); ?></strong><small>noch kein Eingriff</small></article>
			<article><span>Datenstand</span><strong><?php echo esc_html( $last ? wp_date( 'd.m.', $last ) : '—' ); ?></strong><small><?php echo esc_html( $last ? wp_date( 'H:i', $last ) : 'noch keine Aufnahme' ); ?></small></article>
		</div>

		<?php if ( empty( $items ) ) : ?>
			<section class="nsc-decision-lane">
				<p class="nsc-decision-empty">Noch kein materielles Marktsignal. Das System erzeugt bewusst keine Aufgabe, solange Primärdaten und Suchnachfrage die Schwelle nicht tragen.</p>
			</section>
		<?php else : ?>
			<?php nexus_ci_render_content_decision_lane( 'now', '01 · Jetzt tun', 'Diese Maßnahmen haben genug Signal für den nächsten Arbeitsschritt.', $lanes['now'] ); ?>
			<?php nexus_ci_render_content_decision_lane( 'plan', '02 · Prüfen & planen', 'Plausibel, aber vor der Änderung ist noch ein fachlicher oder struktureller Check nötig.', $lanes['plan'] ); ?>
			<?php nexus_ci_render_content_decision_lane( 'observe', '03 · Beobachten', 'Relevant, aber aktuell kein Grund, Content nur wegen eines Signals zu verändern.', $lanes['observe'] ); ?>
		<?php endif; ?>
	</div>
	<?php
}
