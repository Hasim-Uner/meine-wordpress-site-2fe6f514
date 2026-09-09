<?php
/**
 * SEO Cockpit Content Intelligence.
 *
 * Persists selected Research Intelligence metrics, turns material market
 * changes into deterministic signals and matches those signals against the
 * cached Search Console query/page dataset. No LLM and no auto-publishing.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nexus_get_content_intelligence_schema_version() {
	return '1.0.0';
}

function nexus_get_content_intelligence_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'nexus_research_snapshots';
}

/** Install or upgrade the compact research snapshot table. */
function nexus_maybe_install_content_intelligence_schema() {
	$version = nexus_get_content_intelligence_schema_version();
	if ( $version === (string) get_option( 'nexus_content_intelligence_schema_version', '' ) ) {
		return;
	}

	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table   = nexus_get_content_intelligence_table_name();
	$charset = $wpdb->get_charset_collate();
	$sql     = "CREATE TABLE {$table} (
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
	) {$charset};";

	dbDelta( $sql );
	update_option( 'nexus_content_intelligence_schema_version', $version, false );
}
add_action( 'admin_init', 'nexus_maybe_install_content_intelligence_schema', 5 );
add_action( 'wp_loaded', 'nexus_maybe_install_content_intelligence_schema', 5 );

/** Normalize one observation before persistence. */
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

/** Build normalized observations from cached Research provider data. */
function nexus_get_content_intelligence_current_observations() {
	$observations = [];
	$today        = wp_date( 'Y-m-d' );

	if ( function_exists( 'nexus_get_seo_cockpit_energy_charts_summary' ) ) {
		$energy = nexus_get_seo_cockpit_energy_charts_summary();
		if ( is_array( $energy ) && ! empty( $energy['is_available'] ) ) {
			$installed = (array) ( $energy['solar_installed'] ?? [] );
			$item = nexus_content_intelligence_observation(
				'energy_charts',
				'solar_installed',
				'Installierte PV-Leistung Deutschland',
				$installed['value'] ?? null,
				(string) ( $installed['unit'] ?? '' ),
				(string) ( $installed['period'] ?? '' ),
				(string) ( $energy['generated_at'] ?? '' ),
				[
					'delta'      => isset( $installed['delta'] ) && is_numeric( $installed['delta'] ) ? (float) $installed['delta'] : null,
					'growth_pct' => isset( $installed['growth_pct'] ) && is_numeric( $installed['growth_pct'] ) ? (float) $installed['growth_pct'] : null,
					'series_id'  => sanitize_key( (string) ( $installed['series_id'] ?? '' ) ),
				]
			);
			if ( $item ) {
				$observations[] = $item;
			}

			$share      = (array) ( $energy['solar_share_30d'] ?? [] );
			$share_prev = isset( $share['previous'] ) && is_numeric( $share['previous'] ) ? (float) $share['previous'] : null;
			$share_now  = isset( $share['value'] ) && is_numeric( $share['value'] ) ? (float) $share['value'] : null;
			$item = nexus_content_intelligence_observation(
				'energy_charts',
				'solar_share_30d',
				'Solaranteil letzte 30 Tage',
				$share_now,
				'%',
				$today,
				(string) ( $energy['generated_at'] ?? '' ),
				[
					'previous'  => $share_prev,
					'delta_pp'  => null !== $share_now && null !== $share_prev ? $share_now - $share_prev : null,
					'series_id' => sanitize_key( (string) ( $share['series_id'] ?? '' ) ),
				]
			);
			if ( $item ) {
				$observations[] = $item;
			}

			$price = (array) ( $energy['price_current'] ?? [] );
			$item  = nexus_content_intelligence_observation(
				'energy_charts',
				'day_ahead_price',
				'Day-Ahead-Preis DE-LU',
				$price['value'] ?? null,
				(string) ( $price['unit'] ?? '' ),
				$today,
				(string) ( $energy['generated_at'] ?? '' ),
				[ 'series_id' => sanitize_key( (string) ( $price['id'] ?? '' ) ) ]
			);
			if ( $item ) {
				$observations[] = $item;
			}
		}
	}

	$destatis_token = function_exists( 'nexus_get_seo_cockpit_destatis_api_token' ) ? nexus_get_seo_cockpit_destatis_api_token() : '';
	if ( '' !== $destatis_token && function_exists( 'nexus_get_seo_cockpit_destatis_summary' ) ) {
		$destatis = nexus_get_seo_cockpit_destatis_summary();
		if ( is_array( $destatis ) && ! empty( $destatis['is_available'] ) ) {
			foreach ( [ 'de' => 'Deutschland', 'ni' => 'Niedersachsen' ] as $scope => $scope_label ) {
				$metrics = (array) ( $destatis[ $scope ] ?? [] );
				$period  = isset( $metrics['year'] ) && is_numeric( $metrics['year'] ) ? (string) absint( $metrics['year'] ) : '';
				$item = nexus_content_intelligence_observation(
					'destatis',
					$scope . '_residential_buildings',
					'Wohngebäude ' . $scope_label,
					$metrics['total'] ?? null,
					'Gebäude',
					$period,
					'',
					[
						'one_two'       => isset( $metrics['one_two'] ) && is_numeric( $metrics['one_two'] ) ? (float) $metrics['one_two'] : null,
						'one_two_share' => isset( $metrics['one_two_share'] ) && is_numeric( $metrics['one_two_share'] ) ? (float) $metrics['one_two_share'] : null,
					]
				);
				if ( $item ) {
					$observations[] = $item;
				}
			}
		}
	}

	if ( function_exists( 'nexus_get_seo_cockpit_eurostat_summary' ) ) {
		$eurostat = nexus_get_seo_cockpit_eurostat_summary();
		if ( is_array( $eurostat ) && ! empty( $eurostat['is_available'] ) ) {
			$definitions = [
				'de_total'       => 'Deutschland · Erneuerbare gesamt',
				'eu_total'       => 'EU27 · Erneuerbare gesamt',
				'de_electricity' => 'Deutschland · Erneuerbarer Strom',
				'eu_electricity' => 'EU27 · Erneuerbarer Strom',
			];
			foreach ( $definitions as $key => $label ) {
				$series = (array) ( $eurostat[ $key ] ?? [] );
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
						'dataset'  => sanitize_key( (string) ( $eurostat['dataset'] ?? 'nrg_ind_ren' ) ),
					]
				);
				if ( $item ) {
					$observations[] = $item;
				}
			}
		}
	}

	return $observations;
}

/** Persist one distinct provider observation. */
function nexus_store_content_intelligence_observation( $observation ) {
	if ( ! is_array( $observation ) || ! isset( $observation['value'] ) || ! is_numeric( $observation['value'] ) ) {
		return false;
	}

	global $wpdb;
	$table = nexus_get_content_intelligence_table_name();
	$meta  = is_array( $observation['meta'] ?? null ) ? $observation['meta'] : [];
	$parts = [
		(string) ( $observation['provider'] ?? '' ),
		(string) ( $observation['metric_key'] ?? '' ),
		sprintf( '%.8F', (float) $observation['value'] ),
		(string) ( $observation['source_period'] ?? '' ),
		wp_json_encode( $meta ),
	];
	$fingerprint = hash( 'sha256', implode( '|', $parts ) );

	$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE fingerprint = %s LIMIT 1", $fingerprint ) );
	if ( $existing ) {
		return false;
	}

	$inserted = $wpdb->insert(
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

	return false !== $inserted;
}

/** Capture provider values after the existing Research background refresh. */
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

function nexus_get_content_intelligence_observation_history( $limit = 300 ) {
	nexus_maybe_install_content_intelligence_schema();
	global $wpdb;
	$table = nexus_get_content_intelligence_table_name();
	$limit = max( 20, min( 1000, absint( $limit ) ) );
	$rows  = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY captured_at DESC, id DESC LIMIT {$limit}", ARRAY_A );

	foreach ( (array) $rows as &$row ) {
		$row['value'] = isset( $row['value'] ) && is_numeric( $row['value'] ) ? (float) $row['value'] : null;
		$meta         = json_decode( (string) ( $row['meta_json'] ?? '' ), true );
		$row['meta']  = is_array( $meta ) ? $meta : [];
	}
	unset( $row );
	return is_array( $rows ) ? $rows : [];
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
	$period = (string) ( $latest['source_period'] ?? '' );
	$seen   = false;
	foreach ( (array) $rows as $row ) {
		if ( ! $seen ) {
			if ( (int) ( $row['id'] ?? 0 ) === (int) ( $latest['id'] ?? 0 ) ) {
				$seen = true;
			}
			continue;
		}
		if ( $period !== (string) ( $row['source_period'] ?? '' ) ) {
			return $row;
		}
	}
	return null;
}

/** Deterministic V1 market-change rules. */
function nexus_get_content_intelligence_signal_definitions() {
	return [
		'energy_charts.solar_installed' => [
			'title'              => 'PV-Ausbau Deutschland verändert sich deutlich',
			'comparison'         => 'growth_pct',
			'threshold'          => 3.0,
			'business_relevance' => 25,
			'keywords'           => [ 'solar', 'photovoltaik', 'pv', 'solaranlage', 'solarleads', 'speicher' ],
			'context'            => 'Installierte PV-Leistung und Ausbaugeschwindigkeit sind ein belastbares Marktsignal für Solar-Nachfrage und Vertriebsargumentation.',
		],
		'energy_charts.solar_share_30d' => [
			'title'              => 'Solaranteil bewegt sich deutlich',
			'comparison'         => 'delta_pp',
			'threshold'          => 3.0,
			'business_relevance' => 18,
			'keywords'           => [ 'solar', 'photovoltaik', 'pv', 'speicher', 'eigenverbrauch', 'strom' ],
			'context'            => 'Ein deutlicher 30-Tage-Shift kann für Speicher-, Eigenverbrauchs- und Marktargumente relevant sein.',
		],
		'eurostat.de_total' => [
			'title'              => 'Deutschlands Erneuerbaren-Anteil verändert sich',
			'comparison'         => 'delta_pp',
			'threshold'          => 0.5,
			'business_relevance' => 14,
			'keywords'           => [ 'erneuerbare', 'solar', 'photovoltaik', 'pv', 'energiewende', 'wärmepump' ],
			'context'            => 'Eurostat liefert den belastbaren Deutschland-/EU-Kontext für datenbasierte Marktanalysen.',
		],
		'eurostat.de_electricity' => [
			'title'              => 'Erneuerbarer Strom in Deutschland verändert sich',
			'comparison'         => 'delta_pp',
			'threshold'          => 0.5,
			'business_relevance' => 16,
			'keywords'           => [ 'erneuerbare', 'solar', 'photovoltaik', 'pv', 'strom', 'speicher', 'energiewende' ],
			'context'            => 'Der Strommix ist Kontext für Solar-, Speicher- und Elektrifizierungsinhalte.',
		],
	];
}

function nexus_get_content_intelligence_freshness_score( $row ) {
	$period = (string) ( $row['source_period'] ?? '' );
	$year   = 0;
	if ( preg_match( '/(20\d{2})/', $period, $matches ) ) {
		$year = (int) $matches[1];
	}
	$current = (int) wp_date( 'Y' );
	if ( $year >= $current ) {
		return 15;
	}
	if ( $year === $current - 1 ) {
		return 12;
	}
	if ( $year > 0 ) {
		return 8;
	}
	return 10;
}

function nexus_format_content_intelligence_change( $value, $kind ) {
	$prefix = $value > 0 ? '+' : '';
	$suffix = 'growth_pct' === $kind ? ' %' : ' %-Punkte';
	return $prefix . number_format_i18n( $value, 1 ) . $suffix;
}

/** Build current material research signals from stored observations. */
function nexus_get_content_intelligence_signals() {
	$history     = nexus_get_content_intelligence_observation_history();
	$groups      = nexus_group_content_intelligence_history( $history );
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

		$strength = (int) min( 35, round( 15 * ( abs( $change ) / max( 0.01, $threshold ) ) ) );
		$signals[] = [
			'id'                 => hash( 'sha256', $group_key . '|' . (string) ( $latest['source_period'] ?? '' ) . '|' . sprintf( '%.4F', $change ) ),
			'provider'           => (string) ( $latest['provider'] ?? '' ),
			'metric_key'         => (string) ( $latest['metric_key'] ?? '' ),
			'metric_label'       => (string) ( $latest['metric_label'] ?? '' ),
			'title'              => (string) $definition['title'],
			'context'            => (string) $definition['context'],
			'value'              => (float) $latest['value'],
			'unit'               => (string) ( $latest['unit'] ?? '' ),
			'change'             => $change,
			'change_kind'        => $kind,
			'change_label'       => nexus_format_content_intelligence_change( $change, $kind ),
			'direction'          => $change > 0 ? 'up' : 'down',
			'source_period'      => (string) ( $latest['source_period'] ?? '' ),
			'source_updated'     => (string) ( $latest['source_updated'] ?? '' ),
			'captured_at'        => (string) ( $latest['captured_at'] ?? '' ),
			'keywords'           => (array) $definition['keywords'],
			'market_score'       => $strength,
			'freshness_score'    => nexus_get_content_intelligence_freshness_score( $latest ),
			'business_score'     => (int) $definition['business_relevance'],
			'data_quality_score' => 5,
		];
	}

	$destatis_rows = (array) ( $groups['destatis.de_residential_buildings'] ?? [] );
	if ( ! empty( $destatis_rows ) ) {
		$latest   = $destatis_rows[0];
		$previous = nexus_get_content_intelligence_previous_period_row( $destatis_rows, $latest );
		if ( is_array( $previous ) && is_numeric( $latest['value'] ?? null ) && is_numeric( $previous['value'] ?? null ) ) {
			$delta = (float) $latest['value'] - (float) $previous['value'];
			$pct   = 0.0 !== (float) $previous['value'] ? ( $delta / (float) $previous['value'] ) * 100 : 0.0;
			$meta  = is_array( $latest['meta'] ?? null ) ? $latest['meta'] : [];
			$signals[] = [
				'id'                 => hash( 'sha256', 'destatis.release|' . (string) $latest['source_period'] ),
				'provider'           => 'destatis',
				'metric_key'         => 'de_residential_buildings',
				'metric_label'       => 'Wohngebäudebestand Deutschland',
				'title'              => 'Neue Destatis-Gebäudestrukturdaten verfügbar',
				'context'            => 'Ein neues Berichtsjahr ist ein Freshness-Signal für Marktpotenzial-, PV- und Wärmepumpen-Inhalte.',
				'value'              => (float) $latest['value'],
				'unit'               => 'Gebäude',
				'change'             => $pct,
				'change_kind'        => 'growth_pct',
				'change_label'       => nexus_format_content_intelligence_change( $pct, 'growth_pct' ),
				'direction'          => $delta >= 0 ? 'up' : 'down',
				'source_period'      => (string) ( $latest['source_period'] ?? '' ),
				'source_updated'     => '',
				'captured_at'        => (string) ( $latest['captured_at'] ?? '' ),
				'keywords'           => [ 'wärmepump', 'heizung', 'shk', 'gebäude', 'solar', 'photovoltaik', 'pv' ],
				'market_score'       => 22,
				'freshness_score'    => 15,
				'business_score'     => 24,
				'data_quality_score' => 5,
				'one_two_share'      => isset( $meta['one_two_share'] ) && is_numeric( $meta['one_two_share'] ) ? (float) $meta['one_two_share'] : null,
			];
		}
	}

	return $signals;
}

function nexus_content_intelligence_query_matches( $query, $keywords ) {
	$query  = function_exists( 'nexus_normalize_seo_cockpit_query' ) ? nexus_normalize_seo_cockpit_query( $query ) : strtolower( trim( (string) $query ) );
	$padded = ' ' . $query . ' ';
	foreach ( (array) $keywords as $keyword ) {
		$keyword = function_exists( 'nexus_normalize_seo_cockpit_query' ) ? nexus_normalize_seo_cockpit_query( $keyword ) : strtolower( trim( (string) $keyword ) );
		if ( '' === $keyword ) {
			continue;
		}
		if ( strlen( $keyword ) <= 3 ) {
			if ( false !== strpos( $padded, ' ' . $keyword . ' ' ) ) {
				return true;
			}
		} elseif ( false !== strpos( $query, $keyword ) ) {
			return true;
		}
	}
	return false;
}

/** Match one market signal against cached Search Console page/query rows. */
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
				'url'              => $page,
				'impressions'      => 0.0,
				'clicks'           => 0.0,
				'position_weight'  => 0.0,
				'position_samples' => 0.0,
			];
		}
		$pages[ $page ]['impressions'] += $impressions;
		$pages[ $page ]['clicks']      += $clicks;
		$weight = $impressions > 0 ? $impressions : 1.0;
		$pages[ $page ]['position_weight']  += $position * $weight;
		$pages[ $page ]['position_samples'] += $weight;

		$queries[] = [
			'query'       => $query,
			'page'        => $page,
			'impressions' => $impressions,
			'clicks'      => $clicks,
			'position'    => $position,
		];
	}

	$total_impressions = 0.0;
	foreach ( $pages as &$page_data ) {
		$page_data['position'] = $page_data['position_samples'] > 0 ? $page_data['position_weight'] / $page_data['position_samples'] : 0.0;
		$total_impressions += $page_data['impressions'];
	}
	unset( $page_data );

	usort( $pages, static function ( $a, $b ) { return (float) $b['impressions'] <=> (float) $a['impressions']; } );
	usort( $queries, static function ( $a, $b ) { return (float) $b['impressions'] <=> (float) $a['impressions']; } );

	$best       = ! empty( $pages ) ? $pages[0] : null;
	$best_share = is_array( $best ) && $total_impressions > 0 ? (float) $best['impressions'] / $total_impressions : 0.0;
	$score      = 0;
	if ( $total_impressions >= 500 ) {
		$score += 12;
	} elseif ( $total_impressions >= 100 ) {
		$score += 10;
	} elseif ( $total_impressions >= 25 ) {
		$score += 7;
	} elseif ( $total_impressions >= 10 ) {
		$score += 4;
	}
	if ( is_array( $best ) ) {
		$position = (float) $best['position'];
		if ( $position >= 6 && $position <= 20 ) {
			$score += 8;
		} elseif ( $position > 20 && $position <= 40 ) {
			$score += 5;
		} elseif ( $position > 0 && $position < 6 ) {
			$score += 4;
		} elseif ( $position > 40 ) {
			$score += 2;
		}
	}
	$score = min( 20, $score );

	$action       = 'watch';
	$action_label = 'Beobachten';
	$reason       = 'Das Marktsignal ist real, aber die Search Console zeigt noch keine belastbare Nachfrage für eine Content-Maßnahme.';
	if ( $total_impressions >= 20 && is_array( $best ) ) {
		if ( $best_share >= 0.55 ) {
			if ( (float) $best['position'] >= 6 && (float) $best['position'] <= 25 ) {
				$action       = 'expand';
				$action_label = 'Seite erweitern';
				$reason       = 'Eine bestehende URL bündelt die Nachfrage, hat aber noch Ranking-Potenzial. Neue Primärdaten sollten dort ergänzt werden.';
			} else {
				$action       = 'update';
				$action_label = 'Seite aktualisieren';
				$reason       = 'Eine bestehende URL ist bereits das klare Ziel für die relevanten Suchanfragen. Kein neues URL-Silo nötig.';
			}
		} elseif ( $total_impressions >= 100 && count( $pages ) >= 2 ) {
			$action       = 'create_review';
			$action_label = 'Eigene Seite prüfen';
			$reason       = 'Die Nachfrage verteilt sich auf mehrere URLs. Vor einem neuen Artikel muss geprüft werden, ob ein eigenes Suchthema fehlt oder nur Kannibalisierung vorliegt.';
		} else {
			$action       = 'expand';
			$action_label = 'Beste Seite erweitern';
			$reason       = 'Es gibt passende Suchnachfrage, aber noch kein dominantes Ziel. Die stärkste bestehende URL ist der konservative Startpunkt.';
		}
	}

	$context = [];
	if ( is_array( $best ) && function_exists( 'nexus_get_seo_cockpit_wp_context_for_url' ) ) {
		$context = nexus_get_seo_cockpit_wp_context_for_url( (string) $best['url'] );
	}

	return [
		'has_data'        => ! empty( $rows ),
		'impressions'     => $total_impressions,
		'page_count'      => count( $pages ),
		'best_page'       => $best,
		'best_page_share' => $best_share,
		'top_queries'     => array_slice( $queries, 0, 5 ),
		'search_score'    => $score,
		'action'          => $action,
		'action_label'    => $action_label,
		'action_reason'   => $reason,
		'target_context'  => $context,
	];
}

/** Return only the normal cached 28-day GSC snapshot; never trigger live API IO. */
function nexus_get_content_intelligence_cached_gsc_snapshot() {
	if ( ! function_exists( 'nexus_get_seo_cockpit_snapshot_cache_key' ) ) {
		return [];
	}
	$cached = get_transient( nexus_get_seo_cockpit_snapshot_cache_key( 28 ) );
	return is_array( $cached ) ? $cached : [];
}

function nexus_get_content_intelligence_opportunities() {
	$signals  = nexus_get_content_intelligence_signals();
	$snapshot = nexus_get_content_intelligence_cached_gsc_snapshot();
	$gsc_ok   = ! empty( $snapshot );
	$items    = [];

	foreach ( $signals as $signal ) {
		$match = $gsc_ok ? nexus_match_content_intelligence_signal_to_gsc( $signal, $snapshot ) : [
			'has_data'       => false,
			'impressions'    => 0,
			'page_count'     => 0,
			'best_page'      => null,
			'top_queries'    => [],
			'search_score'   => 0,
			'action'         => 'watch',
			'action_label'   => 'Search Console fehlt',
			'action_reason'  => 'Ohne Search-Console-Kontext erzeugt Content Intelligence bewusst keine Content-Empfehlung.',
			'target_context' => [],
		];
		$score = min(
			100,
			(int) $signal['market_score'] +
			(int) $signal['freshness_score'] +
			(int) $signal['business_score'] +
			(int) $signal['data_quality_score'] +
			(int) ( $match['search_score'] ?? 0 )
		);
		$signal['gsc']            = $match;
		$signal['priority_score'] = $score;
		$items[]                  = $signal;
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
		wp_enqueue_style(
			'nexus-seo-cockpit-research',
			get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-research.css',
			[],
			(string) filemtime( $path )
		);
	}

	wp_add_inline_style(
		'nexus-seo-cockpit-research',
		'.nexus-ci-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:18px 0}.nexus-ci-summary article,.nexus-ci-card{background:#fff;border:1px solid #dcdcde;border-radius:14px;padding:18px}.nexus-ci-summary strong{display:block;font-size:26px;line-height:1.1;margin-top:6px}.nexus-ci-list{display:grid;gap:16px;margin-top:18px}.nexus-ci-card__head{display:flex;gap:18px;justify-content:space-between;align-items:flex-start}.nexus-ci-score{display:inline-flex;align-items:center;justify-content:center;min-width:64px;height:64px;border-radius:50%;font-size:20px;font-weight:800;background:#f0f6fc;border:1px solid #c5d9ed}.nexus-ci-card h2{margin:4px 0 8px}.nexus-ci-meta{color:#646970;font-size:13px}.nexus-ci-action{margin-top:16px;padding:14px;border-radius:10px;background:#f6f7f7}.nexus-ci-gsc{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:14px}.nexus-ci-gsc div{padding:12px;border:1px solid #e2e4e7;border-radius:9px}.nexus-ci-queries{display:flex;flex-wrap:wrap;gap:7px;margin-top:12px}.nexus-ci-query{background:#f0f0f1;border-radius:999px;padding:5px 9px;font-size:12px}.nexus-ci-target{word-break:break-word}.nexus-ci-empty{background:#fff;border:1px solid #dcdcde;border-radius:14px;padding:24px;margin-top:18px}@media(max-width:1000px){.nexus-ci-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.nexus-ci-gsc{grid-template-columns:1fr}}@media(max-width:620px){.nexus-ci-summary{grid-template-columns:1fr}.nexus-ci-card__head{display:block}.nexus-ci-score{margin-top:12px}}'
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

	$can_manage     = nexus_current_user_can_manage_seo_cockpit();
	$opportunities  = nexus_get_content_intelligence_opportunities();
	$snapshot_count = nexus_get_content_intelligence_snapshot_count();
	$high           = count( array_filter( $opportunities, static function ( $item ) { return (int) ( $item['priority_score'] ?? 0 ) >= 70; } ) );
	$gsc_backed     = count( array_filter( $opportunities, static function ( $item ) { return (float) ( $item['gsc']['impressions'] ?? 0 ) >= 20; } ) );
	$last_capture   = absint( get_option( 'nexus_content_intelligence_last_capture', 0 ) );
	$notice         = isset( $_GET['ci_notice'] ) ? sanitize_key( (string) wp_unslash( $_GET['ci_notice'] ) ) : '';
	?>
	<div class="wrap nexus-seo-cockpit nexus-seo-cockpit__research">
		<p class="nexus-seo-cockpit__eyebrow">Content Intelligence</p>
		<div class="nexus-seo-cockpit__panel-head">
			<div>
				<h1>Marktsignale mit Search Console verbinden</h1>
				<p class="nexus-seo-cockpit__hint">Primärdaten → materielle Veränderung → GSC-Nachfrage → konkrete Content-Maßnahme. V1 arbeitet regelbasiert, erzeugt keine KI-Texte und veröffentlicht nichts automatisch.</p>
			</div>
			<?php if ( $can_manage ) : ?>
				<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_content_intelligence_refresh' ) ); ?>">
					<?php wp_nonce_field( 'nexus_content_intelligence_refresh' ); ?>
					<button type="submit" class="button button-primary">Research aktualisieren</button>
				</form>
			<?php endif; ?>
		</div>

		<?php if ( 'refresh_queued' === $notice ) : ?>
			<div class="notice notice-info inline"><p>Research-Refresh wurde im Hintergrund eingeplant. Neue Providerwerte werden danach automatisch als Snapshot gespeichert.</p></div>
		<?php endif; ?>

		<div class="nexus-ci-summary">
			<article><span class="nexus-ci-meta">Snapshots gespeichert</span><strong><?php echo esc_html( number_format_i18n( $snapshot_count ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Aktive Marktsignale</span><strong><?php echo esc_html( number_format_i18n( count( $opportunities ) ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Priorität ≥ 70</span><strong><?php echo esc_html( number_format_i18n( $high ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Mit GSC-Nachfrage</span><strong><?php echo esc_html( number_format_i18n( $gsc_backed ) ); ?></strong></article>
		</div>

		<p class="nexus-ci-meta">Letzte Research-Aufnahme: <?php echo esc_html( $last_capture ? wp_date( 'd.m.Y H:i', $last_capture ) : 'noch keine' ); ?></p>

		<?php if ( empty( $opportunities ) ) : ?>
			<div class="nexus-ci-empty">
				<h2>Noch kein materielles Marktsignal</h2>
				<p>Das ist kein Fehler. Content Intelligence erzeugt bewusst keine Aufgabe, solange die definierten Schwellenwerte nicht überschritten werden. Nach dem ersten Background-Refresh werden die Primärdaten historisiert.</p>
			</div>
		<?php else : ?>
			<div class="nexus-ci-list">
				<?php foreach ( $opportunities as $item ) : ?>
					<?php
					$gsc        = (array) ( $item['gsc'] ?? [] );
					$best       = is_array( $gsc['best_page'] ?? null ) ? $gsc['best_page'] : null;
					$context    = is_array( $gsc['target_context'] ?? null ) ? $gsc['target_context'] : [];
					$target_url = is_array( $best ) ? (string) ( $best['url'] ?? '' ) : '';
					?>
					<article class="nexus-ci-card">
						<div class="nexus-ci-card__head">
							<div>
								<p class="nexus-seo-cockpit__eyebrow"><?php echo esc_html( strtoupper( str_replace( '_', ' ', (string) $item['provider'] ) ) . ' · ' . (string) ( $item['source_period'] ?? '' ) ); ?></p>
								<h2><?php echo esc_html( (string) $item['title'] ); ?></h2>
								<p><?php echo esc_html( (string) $item['context'] ); ?></p>
								<p class="nexus-ci-meta"><?php echo esc_html( (string) $item['metric_label'] ); ?>: <strong><?php echo esc_html( number_format_i18n( (float) $item['value'], 1 ) . ' ' . (string) $item['unit'] ); ?></strong> · Veränderung <strong><?php echo esc_html( (string) $item['change_label'] ); ?></strong></p>
							</div>
							<span class="nexus-ci-score" title="Priorität 0–100"><?php echo esc_html( (string) $item['priority_score'] ); ?></span>
						</div>

						<div class="nexus-ci-action">
							<strong><?php echo esc_html( (string) ( $gsc['action_label'] ?? 'Beobachten' ) ); ?></strong>
							<p><?php echo esc_html( (string) ( $gsc['action_reason'] ?? '' ) ); ?></p>
							<?php if ( '' !== $target_url ) : ?>
								<p class="nexus-ci-target"><strong>Ziel:</strong> <a href="<?php echo esc_url( $target_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( '' !== (string) ( $context['post_title'] ?? '' ) ? (string) $context['post_title'] : $target_url ); ?></a></p>
							<?php endif; ?>
						</div>

						<div class="nexus-ci-gsc">
							<div><span class="nexus-ci-meta">Relevante Impressionen · 28 Tage</span><strong><?php echo esc_html( number_format_i18n( (float) ( $gsc['impressions'] ?? 0 ), 0 ) ); ?></strong></div>
							<div><span class="nexus-ci-meta">Passende URLs</span><strong><?php echo esc_html( number_format_i18n( absint( $gsc['page_count'] ?? 0 ) ) ); ?></strong></div>
							<div><span class="nexus-ci-meta">Beste URL · Ø Position</span><strong><?php echo esc_html( is_array( $best ) ? number_format_i18n( (float) ( $best['position'] ?? 0 ), 1 ) : '—' ); ?></strong></div>
						</div>

						<?php if ( ! empty( $gsc['top_queries'] ) ) : ?>
							<div class="nexus-ci-queries">
								<?php foreach ( (array) $gsc['top_queries'] as $query ) : ?>
									<span class="nexus-ci-query"><?php echo esc_html( (string) ( $query['query'] ?? '' ) . ' · ' . number_format_i18n( (float) ( $query['impressions'] ?? 0 ), 0 ) . ' Impr.' ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
