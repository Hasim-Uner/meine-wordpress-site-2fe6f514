<?php
/**
 * SEO Cockpit Content Intelligence V1.
 *
 * Research data -> snapshots -> deterministic market signals -> cached GSC
 * matching -> explainable content opportunities. No LLM or auto-publishing.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nexus_get_content_intelligence_schema_version() {
	return '1.0.1';
}

function nexus_get_content_intelligence_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'nexus_research_snapshots';
}

function nexus_maybe_install_content_intelligence_schema() {
	$version = nexus_get_content_intelligence_schema_version();
	if ( $version === (string) get_option( 'nexus_content_intelligence_schema_version', '' ) ) {
		return;
	}

	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table   = nexus_get_content_intelligence_table_name();
	$charset = $wpdb->get_charset_collate();

	dbDelta(
		"CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			provider varchar(32) NOT NULL,
			metric_key varchar(96) NOT NULL,
			metric_label varchar(190) NOT NULL,
			value double NULL,
			unit varchar(32) NOT NULL DEFAULT '',
			source_period varchar(80) NOT NULL DEFAULT '',
			source_updated varchar(80) NOT NULL DEFAULT '',
			fingerprint char(64) NOT NULL,
			meta_json longtext NULL,
			captured_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY fingerprint (fingerprint),
			KEY provider_metric_time (provider, metric_key, captured_at),
			KEY captured_at (captured_at)
		) {$charset};"
	);

	update_option( 'nexus_content_intelligence_schema_version', $version, false );
}
add_action( 'admin_init', 'nexus_maybe_install_content_intelligence_schema', 5 );
add_action( 'wp_loaded', 'nexus_maybe_install_content_intelligence_schema', 5 );

function nexus_content_intelligence_observation( $provider, $metric, $label, $value, $unit = '', $period = '', $updated = '', $meta = [] ) {
	if ( ! is_numeric( $value ) ) {
		return null;
	}

	return [
		'provider'       => sanitize_key( (string) $provider ),
		'metric_key'     => sanitize_key( (string) $metric ),
		'metric_label'   => sanitize_text_field( (string) $label ),
		'value'          => (float) $value,
		'unit'           => sanitize_text_field( (string) $unit ),
		'source_period'  => sanitize_text_field( (string) $period ),
		'source_updated' => sanitize_text_field( (string) $updated ),
		'meta'           => is_array( $meta ) ? $meta : [],
	];
}

function nexus_get_content_intelligence_current_observations() {
	$items = [];
	$today = wp_date( 'Y-m-d' );

	if ( function_exists( 'nexus_get_seo_cockpit_energy_charts_summary' ) ) {
		$summary = nexus_get_seo_cockpit_energy_charts_summary();
		if ( is_array( $summary ) && ! empty( $summary['is_available'] ) ) {
			$installed = (array) ( $summary['solar_installed'] ?? [] );
			$item = nexus_content_intelligence_observation(
				'energy_charts',
				'solar_installed',
				'Installierte PV-Leistung Deutschland',
				$installed['value'] ?? null,
				(string) ( $installed['unit'] ?? '' ),
				(string) ( $installed['period'] ?? '' ),
				(string) ( $summary['generated_at'] ?? '' ),
				[
					'growth_pct' => isset( $installed['growth_pct'] ) && is_numeric( $installed['growth_pct'] ) ? (float) $installed['growth_pct'] : null,
					'delta'      => isset( $installed['delta'] ) && is_numeric( $installed['delta'] ) ? (float) $installed['delta'] : null,
				]
			);
			if ( $item ) {
				$items[] = $item;
			}

			$share          = (array) ( $summary['solar_share_30d'] ?? [] );
			$current_share  = isset( $share['value'] ) && is_numeric( $share['value'] ) ? (float) $share['value'] : null;
			$previous_share = isset( $share['previous'] ) && is_numeric( $share['previous'] ) ? (float) $share['previous'] : null;
			$item = nexus_content_intelligence_observation(
				'energy_charts',
				'solar_share_30d',
				'Solaranteil letzte 30 Tage',
				$current_share,
				'%',
				$today,
				(string) ( $summary['generated_at'] ?? '' ),
				[
					'previous' => $previous_share,
					'delta_pp' => null !== $current_share && null !== $previous_share ? $current_share - $previous_share : null,
				]
			);
			if ( $item ) {
				$items[] = $item;
			}

			$price = (array) ( $summary['price_current'] ?? [] );
			$item = nexus_content_intelligence_observation(
				'energy_charts',
				'day_ahead_price',
				'Day-Ahead-Preis DE-LU',
				$price['value'] ?? null,
				(string) ( $price['unit'] ?? '' ),
				$today,
				(string) ( $summary['generated_at'] ?? '' )
			);
			if ( $item ) {
				$items[] = $item;
			}
		}
	}

	$destatis_token = function_exists( 'nexus_get_seo_cockpit_destatis_api_token' ) ? nexus_get_seo_cockpit_destatis_api_token() : '';
	if ( '' !== $destatis_token && function_exists( 'nexus_get_seo_cockpit_destatis_summary' ) ) {
		$summary = nexus_get_seo_cockpit_destatis_summary();
		if ( is_array( $summary ) && ! empty( $summary['is_available'] ) ) {
			foreach ( [ 'de' => 'Deutschland', 'ni' => 'Niedersachsen' ] as $scope => $label ) {
				$metric = (array) ( $summary[ $scope ] ?? [] );
				$item = nexus_content_intelligence_observation(
					'destatis',
					$scope . '_residential_buildings',
					'Wohngebäude ' . $label,
					$metric['total'] ?? null,
					'Gebäude',
					isset( $metric['year'] ) && is_numeric( $metric['year'] ) ? (string) absint( $metric['year'] ) : '',
					'',
					[
						'one_two'       => isset( $metric['one_two'] ) && is_numeric( $metric['one_two'] ) ? (float) $metric['one_two'] : null,
						'one_two_share' => isset( $metric['one_two_share'] ) && is_numeric( $metric['one_two_share'] ) ? (float) $metric['one_two_share'] : null,
					]
				);
				if ( $item ) {
					$items[] = $item;
				}
			}
		}
	}

	if ( function_exists( 'nexus_get_seo_cockpit_eurostat_summary' ) ) {
		$summary = nexus_get_seo_cockpit_eurostat_summary();
		if ( is_array( $summary ) && ! empty( $summary['is_available'] ) ) {
			$labels = [
				'de_total'       => 'Deutschland · Erneuerbare gesamt',
				'eu_total'       => 'EU27 · Erneuerbare gesamt',
				'de_electricity' => 'Deutschland · Erneuerbarer Strom',
				'eu_electricity' => 'EU27 · Erneuerbarer Strom',
			];
			foreach ( $labels as $key => $label ) {
				$series = (array) ( $summary[ $key ] ?? [] );
				$item = nexus_content_intelligence_observation(
					'eurostat',
					$key,
					$label,
					$series['value'] ?? null,
					'%',
					(string) ( $series['period'] ?? '' ),
					(string) ( $series['updated'] ?? '' ),
					[
						'previous' => isset( $series['previous'] ) && is_numeric( $series['previous'] ) ? (float) $series['previous'] : null,
						'delta_pp' => isset( $series['delta_pp'] ) && is_numeric( $series['delta_pp'] ) ? (float) $series['delta_pp'] : null,
					]
				);
				if ( $item ) {
					$items[] = $item;
				}
			}
		}
	}

	return $items;
}

function nexus_store_content_intelligence_observation( $observation ) {
	if ( ! is_array( $observation ) || ! isset( $observation['value'] ) || ! is_numeric( $observation['value'] ) ) {
		return false;
	}

	global $wpdb;
	$table = nexus_get_content_intelligence_table_name();
	$meta  = is_array( $observation['meta'] ?? null ) ? $observation['meta'] : [];
	$fingerprint = hash(
		'sha256',
		implode(
			'|',
			[
				(string) ( $observation['provider'] ?? '' ),
				(string) ( $observation['metric_key'] ?? '' ),
				sprintf( '%.8F', (float) $observation['value'] ),
				(string) ( $observation['source_period'] ?? '' ),
				wp_json_encode( $meta ),
			]
		)
	);

	if ( $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE fingerprint = %s LIMIT 1", $fingerprint ) ) ) {
		return false;
	}

	return false !== $wpdb->insert(
		$table,
		[
			'provider'       => sanitize_key( (string) ( $observation['provider'] ?? '' ) ),
			'metric_key'     => sanitize_key( (string) ( $observation['metric_key'] ?? '' ) ),
			'metric_label'   => sanitize_text_field( (string) ( $observation['metric_label'] ?? '' ) ),
			'value'          => (float) $observation['value'],
			'unit'           => sanitize_text_field( (string) ( $observation['unit'] ?? '' ) ),
			'source_period'  => sanitize_text_field( (string) ( $observation['source_period'] ?? '' ) ),
			'source_updated' => sanitize_text_field( (string) ( $observation['source_updated'] ?? '' ) ),
			'fingerprint'    => $fingerprint,
			'meta_json'      => wp_json_encode( $meta ),
			'captured_at'    => current_time( 'mysql', true ),
		],
		[ '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s' ]
	);
}

function nexus_capture_content_intelligence_snapshot() {
	nexus_maybe_install_content_intelligence_schema();
	foreach ( nexus_get_content_intelligence_current_observations() as $observation ) {
		nexus_store_content_intelligence_observation( $observation );
	}

	global $wpdb;
	$table = nexus_get_content_intelligence_table_name();
	$wpdb->query( "DELETE FROM {$table} WHERE captured_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 730 DAY)" );
	update_option( 'nexus_content_intelligence_last_capture', time(), false );
}
add_action( 'nexus_seo_cockpit_research_background_refresh', 'nexus_capture_content_intelligence_snapshot', 20 );

function nexus_get_content_intelligence_history( $limit = 300 ) {
	nexus_maybe_install_content_intelligence_schema();
	global $wpdb;
	$table = nexus_get_content_intelligence_table_name();
	$limit = max( 20, min( 1000, absint( $limit ) ) );
	$rows  = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY captured_at DESC, id DESC LIMIT {$limit}", 'ARRAY_A' );
	$rows  = is_array( $rows ) ? $rows : [];

	foreach ( $rows as &$row ) {
		$row['value'] = isset( $row['value'] ) && is_numeric( $row['value'] ) ? (float) $row['value'] : null;
		$meta         = json_decode( (string) ( $row['meta_json'] ?? '' ), true );
		$row['meta']  = is_array( $meta ) ? $meta : [];
	}
	unset( $row );
	return $rows;
}

function nexus_group_content_intelligence_history( $history ) {
	$groups = [];
	foreach ( (array) $history as $row ) {
		$key = sanitize_key( (string) ( $row['provider'] ?? '' ) ) . '.' . sanitize_key( (string) ( $row['metric_key'] ?? '' ) );
		if ( '.' !== $key ) {
			$groups[ $key ][] = $row;
		}
	}
	return $groups;
}

function nexus_get_content_intelligence_previous_period_row( $rows, $latest ) {
	$latest_id = (int) ( $latest['id'] ?? 0 );
	$period    = (string) ( $latest['source_period'] ?? '' );
	$passed    = false;
	foreach ( (array) $rows as $row ) {
		if ( ! $passed ) {
			$passed = (int) ( $row['id'] ?? 0 ) === $latest_id;
			continue;
		}
		if ( $period !== (string) ( $row['source_period'] ?? '' ) ) {
			return $row;
		}
	}
	return null;
}

function nexus_get_content_intelligence_signal_definitions() {
	return [
		'energy_charts.solar_installed' => [
			'title' => 'PV-Ausbau Deutschland verändert sich deutlich',
			'comparison' => 'growth_pct',
			'threshold' => 3.0,
			'business' => 25,
			'keywords' => [ 'solar', 'photovoltaik', 'pv', 'solaranlage', 'solarleads', 'speicher' ],
			'context' => 'Installierte PV-Leistung und Ausbaugeschwindigkeit sind ein belastbares Marktsignal für Solar-Nachfrage und Vertriebsargumentation.',
		],
		'energy_charts.solar_share_30d' => [
			'title' => 'Solaranteil bewegt sich deutlich',
			'comparison' => 'delta_pp',
			'threshold' => 3.0,
			'business' => 18,
			'keywords' => [ 'solar', 'photovoltaik', 'pv', 'speicher', 'eigenverbrauch', 'strom' ],
			'context' => 'Ein deutlicher 30-Tage-Shift kann für Speicher-, Eigenverbrauchs- und Marktargumente relevant sein.',
		],
		'eurostat.de_total' => [
			'title' => 'Deutschlands Erneuerbaren-Anteil verändert sich',
			'comparison' => 'delta_pp',
			'threshold' => 0.5,
			'business' => 14,
			'keywords' => [ 'erneuerbare', 'solar', 'photovoltaik', 'pv', 'energiewende', 'wärmepump' ],
			'context' => 'Eurostat liefert den belastbaren Deutschland-/EU-Kontext für datenbasierte Marktanalysen.',
		],
		'eurostat.de_electricity' => [
			'title' => 'Erneuerbarer Strom in Deutschland verändert sich',
			'comparison' => 'delta_pp',
			'threshold' => 0.5,
			'business' => 16,
			'keywords' => [ 'erneuerbare', 'solar', 'photovoltaik', 'pv', 'strom', 'speicher', 'energiewende' ],
			'context' => 'Der Strommix ist Kontext für Solar-, Speicher- und Elektrifizierungsinhalte.',
		],
	];
}

function nexus_content_intelligence_freshness_score( $row ) {
	$period  = (string) ( $row['source_period'] ?? '' );
	$year    = preg_match( '/(20\d{2})/', $period, $match ) ? (int) $match[1] : 0;
	$current = (int) wp_date( 'Y' );
	if ( $year >= $current ) {
		return 15;
	}
	if ( $year === $current - 1 ) {
		return 12;
	}
	return $year > 0 ? 8 : 10;
}

function nexus_format_content_intelligence_change( $value, $kind ) {
	return ( $value > 0 ? '+' : '' ) . number_format_i18n( $value, 1 ) . ( 'growth_pct' === $kind ? ' %' : ' %-Punkte' );
}

function nexus_get_content_intelligence_signals() {
	$groups      = nexus_group_content_intelligence_history( nexus_get_content_intelligence_history() );
	$definitions = nexus_get_content_intelligence_signal_definitions();
	$signals     = [];

	foreach ( $definitions as $group_key => $definition ) {
		$rows = (array) ( $groups[ $group_key ] ?? [] );
		if ( empty( $rows ) ) {
			continue;
		}
		$latest    = $rows[0];
		$meta      = is_array( $latest['meta'] ?? null ) ? $latest['meta'] : [];
		$kind      = (string) $definition['comparison'];
		$change    = isset( $meta[ $kind ] ) && is_numeric( $meta[ $kind ] ) ? (float) $meta[ $kind ] : null;
		$threshold = (float) $definition['threshold'];
		if ( null === $change || abs( $change ) < $threshold ) {
			continue;
		}

		$signals[] = [
			'id' => hash( 'sha256', $group_key . '|' . (string) ( $latest['source_period'] ?? '' ) . '|' . sprintf( '%.4F', $change ) ),
			'provider' => (string) ( $latest['provider'] ?? '' ),
			'metric_label' => (string) ( $latest['metric_label'] ?? '' ),
			'title' => (string) $definition['title'],
			'context' => (string) $definition['context'],
			'value' => (float) ( $latest['value'] ?? 0 ),
			'unit' => (string) ( $latest['unit'] ?? '' ),
			'change_label' => nexus_format_content_intelligence_change( $change, $kind ),
			'source_period' => (string) ( $latest['source_period'] ?? '' ),
			'keywords' => (array) $definition['keywords'],
			'market_score' => (int) min( 35, round( 15 * ( abs( $change ) / max( 0.01, $threshold ) ) ) ),
			'freshness_score' => nexus_content_intelligence_freshness_score( $latest ),
			'business_score' => (int) $definition['business'],
			'data_quality_score' => 5,
		];
	}

	$destatis_rows = (array) ( $groups['destatis.de_residential_buildings'] ?? [] );
	if ( ! empty( $destatis_rows ) ) {
		$latest   = $destatis_rows[0];
		$previous = nexus_get_content_intelligence_previous_period_row( $destatis_rows, $latest );
		if ( is_array( $previous ) && is_numeric( $latest['value'] ?? null ) && is_numeric( $previous['value'] ?? null ) ) {
			$old = (float) $previous['value'];
			$new = (float) $latest['value'];
			$pct = 0.0 !== $old ? ( ( $new - $old ) / $old ) * 100 : 0.0;
			$signals[] = [
				'id' => hash( 'sha256', 'destatis.release|' . (string) ( $latest['source_period'] ?? '' ) ),
				'provider' => 'destatis',
				'metric_label' => 'Wohngebäudebestand Deutschland',
				'title' => 'Neue Destatis-Gebäudestrukturdaten verfügbar',
				'context' => 'Ein neues Berichtsjahr ist ein Freshness-Signal für Marktpotenzial-, PV- und Wärmepumpen-Inhalte.',
				'value' => $new,
				'unit' => 'Gebäude',
				'change_label' => nexus_format_content_intelligence_change( $pct, 'growth_pct' ),
				'source_period' => (string) ( $latest['source_period'] ?? '' ),
				'keywords' => [ 'wärmepump', 'heizung', 'shk', 'gebäude', 'solar', 'photovoltaik', 'pv' ],
				'market_score' => 22,
				'freshness_score' => 15,
				'business_score' => 24,
				'data_quality_score' => 5,
			];
		}
	}

	return $signals;
}

function nexus_content_intelligence_query_matches( $query, $keywords ) {
	$query = function_exists( 'nexus_normalize_seo_cockpit_query' ) ? nexus_normalize_seo_cockpit_query( $query ) : strtolower( trim( (string) $query ) );
	foreach ( (array) $keywords as $keyword ) {
		$keyword = function_exists( 'nexus_normalize_seo_cockpit_query' ) ? nexus_normalize_seo_cockpit_query( $keyword ) : strtolower( trim( (string) $keyword ) );
		if ( '' === $keyword ) {
			continue;
		}
		$matched = strlen( $keyword ) <= 3
			? false !== strpos( ' ' . $query . ' ', ' ' . $keyword . ' ' )
			: false !== strpos( $query, $keyword );
		if ( $matched ) {
			return true;
		}
	}
	return false;
}

function nexus_match_content_intelligence_signal_to_gsc( $signal, $snapshot ) {
	$rows     = (array) ( $snapshot['query_page_rows'] ?? [] );
	$keywords = (array) ( $signal['keywords'] ?? [] );
	$pages    = [];
	$queries  = [];

	foreach ( $rows as $row ) {
		$page  = function_exists( 'nexus_get_seo_cockpit_row_key' ) ? nexus_get_seo_cockpit_row_key( $row, 0 ) : (string) ( $row['keys'][0] ?? '' );
		$query = function_exists( 'nexus_get_seo_cockpit_row_key' ) ? nexus_get_seo_cockpit_row_key( $row, 1 ) : (string) ( $row['keys'][1] ?? '' );
		if ( '' === $page || '' === $query || ! nexus_content_intelligence_query_matches( $query, $keywords ) ) {
			continue;
		}
		if ( function_exists( 'nexus_is_seo_cockpit_non_target_query' ) && nexus_is_seo_cockpit_non_target_query( $query ) ) {
			continue;
		}

		$page = function_exists( 'nexus_get_seo_cockpit_effective_insight_url' ) ? nexus_get_seo_cockpit_effective_insight_url( $page ) : $page;
		$impressions = max( 0.0, (float) ( $row['impressions'] ?? 0 ) );
		$clicks      = max( 0.0, (float) ( $row['clicks'] ?? 0 ) );
		$position    = max( 0.0, (float) ( $row['position'] ?? 0 ) );

		if ( ! isset( $pages[ $page ] ) ) {
			$pages[ $page ] = [
				'url' => $page,
				'impressions' => 0.0,
				'clicks' => 0.0,
				'position' => 0.0,
				'position_weight' => 0.0,
				'position_samples' => 0.0,
			];
		}
		$weight = $impressions > 0 ? $impressions : 1.0;
		$pages[ $page ]['impressions'] += $impressions;
		$pages[ $page ]['clicks'] += $clicks;
		$pages[ $page ]['position_weight'] += $position * $weight;
		$pages[ $page ]['position_samples'] += $weight;
		$queries[] = [
			'query' => $query,
			'page' => $page,
			'impressions' => $impressions,
			'clicks' => $clicks,
			'position' => $position,
		];
	}

	$total = 0.0;
	foreach ( $pages as &$page_data ) {
		$page_data['position'] = $page_data['position_samples'] > 0 ? $page_data['position_weight'] / $page_data['position_samples'] : 0.0;
		$total += $page_data['impressions'];
	}
	unset( $page_data );
	usort( $pages, static function ( $a, $b ) { return (float) $b['impressions'] <=> (float) $a['impressions']; } );
	usort( $queries, static function ( $a, $b ) { return (float) $b['impressions'] <=> (float) $a['impressions']; } );

	$best         = ! empty( $pages ) ? $pages[0] : null;
	$share        = is_array( $best ) && $total > 0 ? (float) $best['impressions'] / $total : 0.0;
	$search_score = $total >= 500 ? 12 : ( $total >= 100 ? 10 : ( $total >= 25 ? 7 : ( $total >= 10 ? 4 : 0 ) ) );
	if ( is_array( $best ) ) {
		$pos = (float) $best['position'];
		$search_score += $pos >= 6 && $pos <= 20 ? 8 : ( $pos > 20 && $pos <= 40 ? 5 : ( $pos > 0 && $pos < 6 ? 4 : ( $pos > 40 ? 2 : 0 ) ) );
	}
	$search_score = min( 20, $search_score );

	$action = 'watch';
	$label  = 'Beobachten';
	$reason = 'Das Marktsignal ist real, aber die Search Console zeigt noch keine belastbare Nachfrage für eine Content-Maßnahme.';
	if ( $total >= 20 && is_array( $best ) ) {
		if ( $share >= 0.55 ) {
			$pos = (float) $best['position'];
			if ( $pos >= 6 && $pos <= 25 ) {
				$action = 'expand';
				$label  = 'Seite erweitern';
				$reason = 'Eine bestehende URL bündelt die Nachfrage, hat aber noch Ranking-Potenzial. Neue Primärdaten sollten dort ergänzt werden.';
			} else {
				$action = 'update';
				$label  = 'Seite aktualisieren';
				$reason = 'Eine bestehende URL ist bereits das klare Ziel für die relevanten Suchanfragen. Kein neues URL-Silo nötig.';
			}
		} elseif ( $total >= 100 && count( $pages ) >= 2 ) {
			$action = 'create_review';
			$label  = 'Eigene Seite prüfen';
			$reason = 'Die Nachfrage verteilt sich auf mehrere URLs. Vor einem neuen Artikel muss geprüft werden, ob ein eigenes Suchthema fehlt oder Kannibalisierung vorliegt.';
		} else {
			$action = 'expand';
			$label  = 'Beste Seite erweitern';
			$reason = 'Es gibt passende Suchnachfrage, aber noch kein dominantes Ziel. Die stärkste bestehende URL ist der konservative Startpunkt.';
		}
	}

	$context = is_array( $best ) && function_exists( 'nexus_get_seo_cockpit_wp_context_for_url' )
		? nexus_get_seo_cockpit_wp_context_for_url( (string) $best['url'] )
		: [];

	return [
		'impressions' => $total,
		'page_count' => count( $pages ),
		'best_page' => $best,
		'top_queries' => array_slice( $queries, 0, 5 ),
		'search_score' => $search_score,
		'action' => $action,
		'action_label' => $label,
		'action_reason' => $reason,
		'target_context' => $context,
	];
}

function nexus_get_content_intelligence_cached_gsc_snapshot() {
	if ( ! function_exists( 'nexus_get_seo_cockpit_snapshot_cache_key' ) ) {
		return [];
	}
	$cached = get_transient( nexus_get_seo_cockpit_snapshot_cache_key( 28 ) );
	return is_array( $cached ) ? $cached : [];
}

function nexus_get_content_intelligence_opportunities() {
	$snapshot = nexus_get_content_intelligence_cached_gsc_snapshot();
	$items    = [];
	foreach ( nexus_get_content_intelligence_signals() as $signal ) {
		$gsc = ! empty( $snapshot )
			? nexus_match_content_intelligence_signal_to_gsc( $signal, $snapshot )
			: [
				'impressions' => 0,
				'page_count' => 0,
				'best_page' => null,
				'top_queries' => [],
				'search_score' => 0,
				'action' => 'watch',
				'action_label' => 'Search Console fehlt',
				'action_reason' => 'Ohne Search-Console-Kontext erzeugt Content Intelligence bewusst keine Content-Empfehlung.',
				'target_context' => [],
			];
		$signal['gsc'] = $gsc;
		$signal['priority_score'] = min(
			100,
			(int) $signal['market_score'] +
			(int) $signal['freshness_score'] +
			(int) $signal['business_score'] +
			(int) $signal['data_quality_score'] +
			(int) $gsc['search_score']
		);
		$items[] = $signal;
	}
	usort( $items, static function ( $a, $b ) { return (int) $b['priority_score'] <=> (int) $a['priority_score']; } );
	return $items;
}

function nexus_get_content_intelligence_snapshot_count() {
	nexus_maybe_install_content_intelligence_schema();
	global $wpdb;
	$table = nexus_get_content_intelligence_table_name();
	return absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ) );
}

function nexus_get_content_intelligence_slug() {
	return 'nexus-seo-cockpit-opportunities';
}

function nexus_register_content_intelligence_page() {
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Content Intelligence',
		'Opportunities',
		nexus_get_seo_cockpit_view_cap(),
		nexus_get_content_intelligence_slug(),
		'nexus_render_content_intelligence_page'
	);
}
add_action( 'admin_menu', 'nexus_register_content_intelligence_page', 41 );

function nexus_enqueue_content_intelligence_assets() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( nexus_get_content_intelligence_slug() !== $page ) {
		return;
	}
	$path = get_stylesheet_directory() . '/assets/css/seo-cockpit-research.css';
	if ( file_exists( $path ) ) {
		wp_enqueue_style( 'nexus-seo-cockpit-research', get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-research.css', [], (string) filemtime( $path ) );
	}
	wp_add_inline_style(
		'nexus-seo-cockpit-research',
		'.nexus-ci-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:18px 0}.nexus-ci-summary article,.nexus-ci-card{background:#fff;border:1px solid #dcdcde;border-radius:14px;padding:18px}.nexus-ci-summary strong{display:block;font-size:26px;margin-top:6px}.nexus-ci-list{display:grid;gap:16px;margin-top:18px}.nexus-ci-head{display:flex;gap:18px;justify-content:space-between;align-items:flex-start}.nexus-ci-score{display:inline-flex;align-items:center;justify-content:center;min-width:64px;height:64px;border-radius:50%;font-size:20px;font-weight:800;background:#f0f6fc;border:1px solid #c5d9ed}.nexus-ci-meta{color:#646970;font-size:13px}.nexus-ci-action{margin-top:16px;padding:14px;border-radius:10px;background:#f6f7f7}.nexus-ci-gsc{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:14px}.nexus-ci-gsc div{padding:12px;border:1px solid #e2e4e7;border-radius:9px}.nexus-ci-query{display:inline-block;background:#f0f0f1;border-radius:999px;padding:5px 9px;margin:8px 5px 0 0;font-size:12px}@media(max-width:900px){.nexus-ci-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.nexus-ci-gsc{grid-template-columns:1fr}}'
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_enqueue_content_intelligence_assets' );

function nexus_handle_content_intelligence_refresh() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}
	check_admin_referer( 'nexus_content_intelligence_refresh' );
	if ( function_exists( 'nexus_schedule_seo_cockpit_research_background_refresh' ) ) {
		nexus_schedule_seo_cockpit_research_background_refresh();
	}
	wp_safe_redirect( admin_url( 'admin.php?page=' . nexus_get_content_intelligence_slug() . '&ci_notice=refresh_queued' ) );
	exit;
}
add_action( 'admin_post_nexus_content_intelligence_refresh', 'nexus_handle_content_intelligence_refresh' );

function nexus_render_content_intelligence_page() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}
	$items     = nexus_get_content_intelligence_opportunities();
	$count     = nexus_get_content_intelligence_snapshot_count();
	$high      = count( array_filter( $items, static function ( $item ) { return (int) ( $item['priority_score'] ?? 0 ) >= 70; } ) );
	$gsc_count = count( array_filter( $items, static function ( $item ) { return (float) ( $item['gsc']['impressions'] ?? 0 ) >= 20; } ) );
	$last      = absint( get_option( 'nexus_content_intelligence_last_capture', 0 ) );
	?>
	<div class="wrap nexus-seo-cockpit nexus-seo-cockpit__research">
		<p class="nexus-seo-cockpit__eyebrow">Content Intelligence</p>
		<div class="nexus-seo-cockpit__panel-head">
			<div><h1>Marktsignale mit Search Console verbinden</h1><p class="nexus-seo-cockpit__hint">Primärdaten → Veränderung → GSC-Nachfrage → Content-Maßnahme. V1 ist regelbasiert und veröffentlicht nichts automatisch.</p></div>
			<?php if ( nexus_current_user_can_manage_seo_cockpit() ) : ?>
			<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_content_intelligence_refresh' ) ); ?>"><?php wp_nonce_field( 'nexus_content_intelligence_refresh' ); ?><button type="submit" class="button button-primary">Research aktualisieren</button></form>
			<?php endif; ?>
		</div>
		<div class="nexus-ci-summary">
			<article><span class="nexus-ci-meta">Snapshots</span><strong><?php echo esc_html( number_format_i18n( $count ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Marktsignale</span><strong><?php echo esc_html( number_format_i18n( count( $items ) ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Priorität ≥ 70</span><strong><?php echo esc_html( number_format_i18n( $high ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Mit GSC-Nachfrage</span><strong><?php echo esc_html( number_format_i18n( $gsc_count ) ); ?></strong></article>
		</div>
		<p class="nexus-ci-meta">Letzte Research-Aufnahme: <?php echo esc_html( $last ? wp_date( 'd.m.Y H:i', $last ) : 'noch keine' ); ?></p>
		<?php if ( empty( $items ) ) : ?>
			<div class="nexus-ci-card"><h2>Noch kein materielles Marktsignal</h2><p>Nach dem ersten Background-Refresh werden die Primärdaten historisiert. Ohne überschrittene Schwelle erzeugt das System bewusst keine Aufgabe.</p></div>
		<?php else : ?>
		<div class="nexus-ci-list">
		<?php foreach ( $items as $item ) :
			$gsc     = (array) ( $item['gsc'] ?? [] );
			$best    = is_array( $gsc['best_page'] ?? null ) ? $gsc['best_page'] : null;
			$context = is_array( $gsc['target_context'] ?? null ) ? $gsc['target_context'] : [];
		?>
			<article class="nexus-ci-card">
				<div class="nexus-ci-head"><div><p class="nexus-seo-cockpit__eyebrow"><?php echo esc_html( strtoupper( str_replace( '_', ' ', (string) $item['provider'] ) ) . ' · ' . (string) $item['source_period'] ); ?></p><h2><?php echo esc_html( (string) $item['title'] ); ?></h2><p><?php echo esc_html( (string) $item['context'] ); ?></p><p class="nexus-ci-meta"><?php echo esc_html( (string) $item['metric_label'] . ': ' . number_format_i18n( (float) $item['value'], 1 ) . ' ' . (string) $item['unit'] . ' · Veränderung ' . (string) $item['change_label'] ); ?></p></div><span class="nexus-ci-score"><?php echo esc_html( (string) $item['priority_score'] ); ?></span></div>
				<div class="nexus-ci-action"><strong><?php echo esc_html( (string) ( $gsc['action_label'] ?? 'Beobachten' ) ); ?></strong><p><?php echo esc_html( (string) ( $gsc['action_reason'] ?? '' ) ); ?></p>
				<?php if ( is_array( $best ) ) : $url = (string) ( $best['url'] ?? '' ); ?><p><strong>Ziel:</strong> <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( '' !== (string) ( $context['post_title'] ?? '' ) ? (string) $context['post_title'] : $url ); ?></a></p><?php endif; ?></div>
				<div class="nexus-ci-gsc"><div><span class="nexus-ci-meta">Impressionen · 28 Tage</span><strong><?php echo esc_html( number_format_i18n( (float) ( $gsc['impressions'] ?? 0 ), 0 ) ); ?></strong></div><div><span class="nexus-ci-meta">Passende URLs</span><strong><?php echo esc_html( number_format_i18n( absint( $gsc['page_count'] ?? 0 ) ) ); ?></strong></div><div><span class="nexus-ci-meta">Beste URL · Ø Position</span><strong><?php echo esc_html( is_array( $best ) ? number_format_i18n( (float) ( $best['position'] ?? 0 ), 1 ) : '—' ); ?></strong></div></div>
				<?php foreach ( (array) ( $gsc['top_queries'] ?? [] ) as $query ) : ?><span class="nexus-ci-query"><?php echo esc_html( (string) ( $query['query'] ?? '' ) . ' · ' . number_format_i18n( (float) ( $query['impressions'] ?? 0 ), 0 ) . ' Impr.' ); ?></span><?php endforeach; ?>
			</article>
		<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php
}
