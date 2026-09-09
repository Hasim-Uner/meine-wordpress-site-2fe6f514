<?php
/**
 * SEO Cockpit Content Intelligence.
 *
 * Connects cached primary-source research data with cached Search Console
 * query/page data. V1 is deterministic, admin/background-only and never
 * generates or publishes content automatically.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a safe float without forcing PHPStan into literal-number shapes.
 *
 * @param mixed $value Raw value.
 * @return float
 */
function nexus_ci_number( $value ) {
	return is_numeric( $value ) ? (float) $value : 0.0;
}

/**
 * Persistent bounded history option.
 *
 * @return string
 */
function nexus_ci_history_option_name() {
	return 'nexus_content_intelligence_history_v1';
}

/**
 * Read the bounded observation history.
 *
 * @return array<string, array<int, array<string, mixed>>>
 */
function nexus_ci_get_history() {
	$history = get_option( nexus_ci_history_option_name(), [] );
	return is_array( $history ) ? $history : [];
}

/**
 * Build one normalized numeric observation.
 *
 * @param string              $provider Provider key.
 * @param string              $metric Metric key.
 * @param string              $label Human label.
 * @param mixed               $value Numeric value.
 * @param string              $unit Unit.
 * @param string              $period Source period.
 * @param array<string,mixed> $meta Comparison metadata.
 * @return array<string,mixed>|null
 */
function nexus_ci_observation( $provider, $metric, $label, $value, $unit = '', $period = '', $meta = [] ) {
	if ( ! is_numeric( $value ) ) {
		return null;
	}

	return [
		'provider'     => sanitize_key( $provider ),
		'metric'       => sanitize_key( $metric ),
		'label'        => sanitize_text_field( $label ),
		'value'        => (float) $value,
		'unit'         => sanitize_text_field( $unit ),
		'period'       => sanitize_text_field( $period ),
		'meta'         => is_array( $meta ) ? $meta : [],
		'captured_at'  => time(),
	];
}

/**
 * Return normalized observations from already-existing provider summaries.
 *
 * @return array<int, array<string,mixed>>
 */
function nexus_ci_current_observations() {
	$items = [];

	if ( function_exists( 'nexus_get_seo_cockpit_energy_charts_summary' ) ) {
		$energy = nexus_get_seo_cockpit_energy_charts_summary();
		if ( is_array( $energy ) && ! empty( $energy['is_available'] ) ) {
			$installed = (array) ( $energy['solar_installed'] ?? [] );
			$item = nexus_ci_observation(
				'energy_charts',
				'solar_installed',
				'Installierte PV-Leistung Deutschland',
				$installed['value'] ?? null,
				(string) ( $installed['unit'] ?? '' ),
				(string) ( $installed['period'] ?? '' ),
				[
					'growth_pct' => isset( $installed['growth_pct'] ) && is_numeric( $installed['growth_pct'] ) ? (float) $installed['growth_pct'] : null,
					'delta'      => isset( $installed['delta'] ) && is_numeric( $installed['delta'] ) ? (float) $installed['delta'] : null,
				]
			);
			if ( $item ) {
				$items[] = $item;
			}

			$share     = (array) ( $energy['solar_share_30d'] ?? [] );
			$share_now = isset( $share['value'] ) && is_numeric( $share['value'] ) ? (float) $share['value'] : null;
			$share_old = isset( $share['previous'] ) && is_numeric( $share['previous'] ) ? (float) $share['previous'] : null;
			$item = nexus_ci_observation(
				'energy_charts',
				'solar_share_30d',
				'Solaranteil letzte 30 Tage',
				$share_now,
				'%',
				wp_date( 'Y-m-d' ),
				[
					'previous' => $share_old,
					'delta_pp' => null !== $share_now && null !== $share_old ? $share_now - $share_old : null,
				]
			);
			if ( $item ) {
				$items[] = $item;
			}

			$price = (array) ( $energy['price_current'] ?? [] );
			$item  = nexus_ci_observation(
				'energy_charts',
				'day_ahead_price',
				'Day-Ahead-Preis DE-LU',
				$price['value'] ?? null,
				(string) ( $price['unit'] ?? '' ),
				wp_date( 'Y-m-d' )
			);
			if ( $item ) {
				$items[] = $item;
			}
		}
	}

	$destatis_token = function_exists( 'nexus_get_seo_cockpit_destatis_api_token' ) ? nexus_get_seo_cockpit_destatis_api_token() : '';
	if ( '' !== $destatis_token && function_exists( 'nexus_get_seo_cockpit_destatis_summary' ) ) {
		$destatis = nexus_get_seo_cockpit_destatis_summary();
		if ( is_array( $destatis ) && ! empty( $destatis['is_available'] ) ) {
			foreach ( [ 'de' => 'Deutschland', 'ni' => 'Niedersachsen' ] as $scope => $label ) {
				$data = (array) ( $destatis[ $scope ] ?? [] );
				$item = nexus_ci_observation(
					'destatis',
					$scope . '_residential_buildings',
					'Wohngebäude ' . $label,
					$data['total'] ?? null,
					'Gebäude',
					isset( $data['year'] ) && is_numeric( $data['year'] ) ? (string) absint( $data['year'] ) : '',
					[
						'one_two'       => isset( $data['one_two'] ) && is_numeric( $data['one_two'] ) ? (float) $data['one_two'] : null,
						'one_two_share' => isset( $data['one_two_share'] ) && is_numeric( $data['one_two_share'] ) ? (float) $data['one_two_share'] : null,
					]
				);
				if ( $item ) {
					$items[] = $item;
				}
			}
		}
	}

	if ( function_exists( 'nexus_get_seo_cockpit_eurostat_summary' ) ) {
		$eurostat = nexus_get_seo_cockpit_eurostat_summary();
		if ( is_array( $eurostat ) && ! empty( $eurostat['is_available'] ) ) {
			$labels = [
				'de_total'       => 'Deutschland · Erneuerbare gesamt',
				'eu_total'       => 'EU27 · Erneuerbare gesamt',
				'de_electricity' => 'Deutschland · Erneuerbarer Strom',
				'eu_electricity' => 'EU27 · Erneuerbarer Strom',
			];
			foreach ( $labels as $key => $label ) {
				$data = (array) ( $eurostat[ $key ] ?? [] );
				$item = nexus_ci_observation(
					'eurostat',
					$key,
					$label,
					$data['value'] ?? null,
					'%',
					(string) ( $data['period'] ?? '' ),
					[
						'previous' => isset( $data['previous'] ) && is_numeric( $data['previous'] ) ? (float) $data['previous'] : null,
						'delta_pp' => isset( $data['delta_pp'] ) && is_numeric( $data['delta_pp'] ) ? (float) $data['delta_pp'] : null,
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

/**
 * Persist distinct observations after the existing Research background run.
 * Keeps at most 36 distinct states per metric; this is small enough for a
 * non-autoloaded WordPress option and avoids a separate schema for V1.
 *
 * @return void
 */
function nexus_ci_capture_history() {
	$history = nexus_ci_get_history();

	foreach ( nexus_ci_current_observations() as $observation ) {
		$key = (string) $observation['provider'] . '.' . (string) $observation['metric'];
		$rows = isset( $history[ $key ] ) && is_array( $history[ $key ] ) ? $history[ $key ] : [];
		$fingerprint = hash(
			'sha256',
			wp_json_encode(
				[
					$observation['value'],
					$observation['period'],
					$observation['meta'],
				]
			)
		);

		$latest_fingerprint = ! empty( $rows ) ? (string) ( $rows[0]['fingerprint'] ?? '' ) : '';
		if ( $fingerprint === $latest_fingerprint ) {
			continue;
		}

		$observation['fingerprint'] = $fingerprint;
		array_unshift( $rows, $observation );
		$history[ $key ] = array_slice( $rows, 0, 36 );
	}

	if ( false === get_option( nexus_ci_history_option_name(), false ) ) {
		add_option( nexus_ci_history_option_name(), $history, '', false );
	} else {
		update_option( nexus_ci_history_option_name(), $history, false );
	}
	update_option( 'nexus_content_intelligence_last_capture', time(), false );
}
add_action( 'nexus_seo_cockpit_research_background_refresh', 'nexus_ci_capture_history', 20 );

/**
 * Signal definitions. Scores intentionally stay deterministic in V1.
 *
 * @return array<string,array<string,mixed>>
 */
function nexus_ci_signal_definitions() {
	return [
		'energy_charts.solar_installed' => [
			'title'      => 'PV-Ausbau Deutschland verändert sich deutlich',
			'comparison' => 'growth_pct',
			'threshold'  => 3.0,
			'business'   => 25,
			'keywords'   => [ 'solar', 'photovoltaik', 'pv', 'solaranlage', 'solarleads', 'speicher' ],
			'context'    => 'Installierte PV-Leistung und Ausbaugeschwindigkeit sind ein belastbares Signal für Markt- und Vertriebsinhalte.',
		],
		'energy_charts.solar_share_30d' => [
			'title'      => 'Solaranteil bewegt sich deutlich',
			'comparison' => 'delta_pp',
			'threshold'  => 3.0,
			'business'   => 18,
			'keywords'   => [ 'solar', 'photovoltaik', 'pv', 'speicher', 'eigenverbrauch', 'strom' ],
			'context'    => 'Ein deutlicher 30-Tage-Shift kann für Speicher-, Eigenverbrauchs- und Marktargumente relevant sein.',
		],
		'eurostat.de_total' => [
			'title'      => 'Deutschlands Erneuerbaren-Anteil verändert sich',
			'comparison' => 'delta_pp',
			'threshold'  => 0.5,
			'business'   => 14,
			'keywords'   => [ 'erneuerbare', 'solar', 'photovoltaik', 'pv', 'energiewende', 'wärmepump' ],
			'context'    => 'Eurostat liefert den belastbaren Deutschland-/EU-Kontext für datenbasierte Marktanalysen.',
		],
		'eurostat.de_electricity' => [
			'title'      => 'Erneuerbarer Strom in Deutschland verändert sich',
			'comparison' => 'delta_pp',
			'threshold'  => 0.5,
			'business'   => 16,
			'keywords'   => [ 'erneuerbare', 'solar', 'photovoltaik', 'pv', 'strom', 'speicher', 'energiewende' ],
			'context'    => 'Der Strommix ist Kontext für Solar-, Speicher- und Elektrifizierungsinhalte.',
		],
	];
}

/**
 * Freshness points from the source period.
 *
 * @param string $period Source period.
 * @return int
 */
function nexus_ci_freshness_score( $period ) {
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

/**
 * Format one relative change.
 *
 * @param float  $change Change.
 * @param string $kind growth_pct or delta_pp.
 * @return string
 */
function nexus_ci_change_label( $change, $kind ) {
	return ( $change > 0 ? '+' : '' ) . number_format_i18n( $change, 1 ) . ( 'growth_pct' === $kind ? ' %' : ' %-Punkte' );
}

/**
 * Build current material signals from the latest persisted observations.
 *
 * @return array<int,array<string,mixed>>
 */
function nexus_ci_signals() {
	$history     = nexus_ci_get_history();
	$definitions = nexus_ci_signal_definitions();
	$signals     = [];

	foreach ( $definitions as $key => $definition ) {
		$rows = isset( $history[ $key ] ) && is_array( $history[ $key ] ) ? $history[ $key ] : [];
		if ( empty( $rows ) ) {
			continue;
		}
		$latest = $rows[0];
		$meta   = is_array( $latest['meta'] ?? null ) ? $latest['meta'] : [];
		$kind   = (string) $definition['comparison'];
		$change = isset( $meta[ $kind ] ) && is_numeric( $meta[ $kind ] ) ? (float) $meta[ $kind ] : null;
		$limit  = (float) $definition['threshold'];
		if ( null === $change || abs( $change ) < $limit ) {
			continue;
		}

		$signals[] = [
			'provider'      => (string) ( $latest['provider'] ?? '' ),
			'label'         => (string) ( $latest['label'] ?? '' ),
			'value'         => nexus_ci_number( $latest['value'] ?? null ),
			'unit'          => (string) ( $latest['unit'] ?? '' ),
			'period'        => (string) ( $latest['period'] ?? '' ),
			'title'         => (string) $definition['title'],
			'context'       => (string) $definition['context'],
			'change_label'  => nexus_ci_change_label( $change, $kind ),
			'keywords'      => (array) $definition['keywords'],
			'market_score'  => (int) min( 35, round( 15 * ( abs( $change ) / max( 0.01, $limit ) ) ) ),
			'fresh_score'   => nexus_ci_freshness_score( (string) ( $latest['period'] ?? '' ) ),
			'business_score'=> (int) $definition['business'],
		];
	}

	$destatis_rows = isset( $history['destatis.de_residential_buildings'] ) && is_array( $history['destatis.de_residential_buildings'] )
		? $history['destatis.de_residential_buildings']
		: [];
	if ( count( $destatis_rows ) >= 2 ) {
		$latest   = $destatis_rows[0];
		$previous = null;
		foreach ( array_slice( $destatis_rows, 1 ) as $candidate ) {
			if ( (string) ( $candidate['period'] ?? '' ) !== (string) ( $latest['period'] ?? '' ) ) {
				$previous = $candidate;
				break;
			}
		}
		if ( is_array( $previous ) ) {
			$old = nexus_ci_number( $previous['value'] ?? null );
			$new = nexus_ci_number( $latest['value'] ?? null );
			$pct = $old > 0 ? ( ( $new - $old ) / $old ) * 100 : 0.0;
			$signals[] = [
				'provider'       => 'destatis',
				'label'          => 'Wohngebäudebestand Deutschland',
				'value'          => $new,
				'unit'           => 'Gebäude',
				'period'         => (string) ( $latest['period'] ?? '' ),
				'title'          => 'Neue Destatis-Gebäudestrukturdaten verfügbar',
				'context'        => 'Ein neues Berichtsjahr ist ein Freshness-Signal für Marktpotenzial-, PV- und Wärmepumpen-Inhalte.',
				'change_label'   => nexus_ci_change_label( $pct, 'growth_pct' ),
				'keywords'       => [ 'wärmepump', 'heizung', 'shk', 'gebäude', 'solar', 'photovoltaik', 'pv' ],
				'market_score'   => 22,
				'fresh_score'    => 15,
				'business_score' => 24,
			];
		}
	}

	return $signals;
}

/**
 * Conservative substring matcher for Search Console queries.
 *
 * @param string            $query Query.
 * @param array<int,string> $keywords Keywords.
 * @return bool
 */
function nexus_ci_query_matches( $query, $keywords ) {
	$query = function_exists( 'nexus_normalize_seo_cockpit_query' ) ? nexus_normalize_seo_cockpit_query( $query ) : strtolower( trim( $query ) );
	foreach ( $keywords as $keyword ) {
		$needle = function_exists( 'nexus_normalize_seo_cockpit_query' ) ? nexus_normalize_seo_cockpit_query( $keyword ) : strtolower( trim( $keyword ) );
		if ( '' === $needle ) {
			continue;
		}
		$matched = strlen( $needle ) <= 3
			? false !== strpos( ' ' . $query . ' ', ' ' . $needle . ' ' )
			: false !== strpos( $query, $needle );
		if ( $matched ) {
			return true;
		}
	}
	return false;
}

/**
 * Match one signal to the cached 28-day query/page dataset.
 *
 * @param array<string,mixed> $signal Signal.
 * @param array<string,mixed> $snapshot Cached GSC snapshot.
 * @return array<string,mixed>
 */
function nexus_ci_match_gsc( $signal, $snapshot ) {
	$rows       = (array) ( $snapshot['query_page_rows'] ?? [] );
	$keywords   = array_values( array_filter( array_map( 'strval', (array) ( $signal['keywords'] ?? [] ) ) ) );
	$page_impr  = [];
	$page_click = [];
	$page_pos_w = [];
	$page_pos_n = [];
	$queries    = [];

	foreach ( $rows as $row ) {
		$page  = function_exists( 'nexus_get_seo_cockpit_row_key' ) ? nexus_get_seo_cockpit_row_key( $row, 0 ) : (string) ( $row['keys'][0] ?? '' );
		$query = function_exists( 'nexus_get_seo_cockpit_row_key' ) ? nexus_get_seo_cockpit_row_key( $row, 1 ) : (string) ( $row['keys'][1] ?? '' );
		if ( '' === $page || '' === $query || ! nexus_ci_query_matches( $query, $keywords ) ) {
			continue;
		}
		if ( function_exists( 'nexus_is_seo_cockpit_non_target_query' ) && nexus_is_seo_cockpit_non_target_query( $query ) ) {
			continue;
		}

		$page = function_exists( 'nexus_get_seo_cockpit_effective_insight_url' ) ? nexus_get_seo_cockpit_effective_insight_url( $page ) : $page;
		$impressions = max( 0.0, nexus_ci_number( $row['impressions'] ?? null ) );
		$clicks      = max( 0.0, nexus_ci_number( $row['clicks'] ?? null ) );
		$position    = max( 0.0, nexus_ci_number( $row['position'] ?? null ) );
		$weight      = $impressions > 0 ? $impressions : 1.0;

		$page_impr[ $page ]  = nexus_ci_number( $page_impr[ $page ] ?? null ) + $impressions;
		$page_click[ $page ] = nexus_ci_number( $page_click[ $page ] ?? null ) + $clicks;
		$page_pos_w[ $page ] = nexus_ci_number( $page_pos_w[ $page ] ?? null ) + ( $position * $weight );
		$page_pos_n[ $page ] = nexus_ci_number( $page_pos_n[ $page ] ?? null ) + $weight;
		$queries[] = [
			'query'       => $query,
			'impressions' => $impressions,
		];
	}

	$pages = [];
	$total = 0.0;
	foreach ( $page_impr as $page => $impressions ) {
		$samples = nexus_ci_number( $page_pos_n[ $page ] ?? null );
		$average = $samples > 0 ? nexus_ci_number( $page_pos_w[ $page ] ?? null ) / $samples : 0.0;
		$total  += nexus_ci_number( $impressions );
		$pages[] = [
			'url'         => (string) $page,
			'impressions' => nexus_ci_number( $impressions ),
			'clicks'      => nexus_ci_number( $page_click[ $page ] ?? null ),
			'position'    => $average,
		];
	}

	usort( $pages, static function ( $a, $b ) {
		return nexus_ci_number( $b['impressions'] ?? null ) <=> nexus_ci_number( $a['impressions'] ?? null );
	} );
	usort( $queries, static function ( $a, $b ) {
		return nexus_ci_number( $b['impressions'] ?? null ) <=> nexus_ci_number( $a['impressions'] ?? null );
	} );

	$best          = ! empty( $pages ) ? $pages[0] : null;
	$best_impr     = is_array( $best ) ? nexus_ci_number( $best['impressions'] ?? null ) : 0.0;
	$best_position = is_array( $best ) ? nexus_ci_number( $best['position'] ?? null ) : 0.0;
	$share         = $total > 0 ? $best_impr / $total : 0.0;

	$search_score = $total >= 500 ? 12 : ( $total >= 100 ? 10 : ( $total >= 25 ? 7 : ( $total >= 10 ? 4 : 0 ) ) );
	if ( $best_position >= 6 && $best_position <= 20 ) {
		$search_score += 8;
	} elseif ( $best_position > 20 && $best_position <= 40 ) {
		$search_score += 5;
	} elseif ( $best_position > 0 && $best_position < 6 ) {
		$search_score += 4;
	} elseif ( $best_position > 40 ) {
		$search_score += 2;
	}
	$search_score = min( 20, $search_score );

	$action = 'watch';
	$label  = 'Beobachten';
	$reason = 'Das Marktsignal ist real, aber die Search Console zeigt noch keine belastbare Nachfrage für eine Content-Maßnahme.';
	if ( $total >= 20 && is_array( $best ) ) {
		if ( $share >= 0.55 ) {
			if ( $best_position >= 6 && $best_position <= 25 ) {
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
			$reason = 'Die Nachfrage verteilt sich auf mehrere URLs. Vor einer neuen URL muss Kannibalisierung ausgeschlossen werden.';
		} else {
			$action = 'expand';
			$label  = 'Beste Seite erweitern';
			$reason = 'Es gibt passende Suchnachfrage, aber noch kein dominantes Ziel. Die stärkste bestehende URL ist der konservative Startpunkt.';
		}
	}

	$context = is_array( $best ) && function_exists( 'nexus_get_seo_cockpit_wp_context_for_url' )
		? nexus_get_seo_cockpit_wp_context_for_url( (string) ( $best['url'] ?? '' ) )
		: [];

	return [
		'impressions'   => $total,
		'page_count'    => count( $pages ),
		'best_page'     => $best,
		'top_queries'   => array_slice( $queries, 0, 5 ),
		'search_score'  => $search_score,
		'action'        => $action,
		'action_label'  => $label,
		'action_reason' => $reason,
		'target_context'=> is_array( $context ) ? $context : [],
	];
}

/**
 * Return the existing 28-day GSC snapshot only; never make a live GSC request.
 *
 * @return array<string,mixed>
 */
function nexus_ci_cached_gsc_snapshot() {
	if ( ! function_exists( 'nexus_get_seo_cockpit_snapshot_cache_key' ) ) {
		return [];
	}
	$snapshot = get_transient( nexus_get_seo_cockpit_snapshot_cache_key( 28 ) );
	return is_array( $snapshot ) ? $snapshot : [];
}

/**
 * Build scored opportunities.
 *
 * @return array<int,array<string,mixed>>
 */
function nexus_ci_opportunities() {
	$snapshot = nexus_ci_cached_gsc_snapshot();
	$items    = [];

	foreach ( nexus_ci_signals() as $signal ) {
		$gsc = ! empty( $snapshot ) ? nexus_ci_match_gsc( $signal, $snapshot ) : [
			'impressions'   => 0.0,
			'page_count'    => 0,
			'best_page'     => null,
			'top_queries'   => [],
			'search_score'  => 0,
			'action'        => 'watch',
			'action_label'  => 'Search Console fehlt',
			'action_reason' => 'Ohne Search-Console-Kontext erzeugt Content Intelligence bewusst keine Content-Empfehlung.',
			'target_context'=> [],
		];
		$signal['gsc'] = $gsc;
		$signal['priority_score'] = min(
			100,
			(int) $signal['market_score'] +
			(int) $signal['fresh_score'] +
			(int) $signal['business_score'] +
			5 +
			(int) ( $gsc['search_score'] ?? 0 )
		);
		$items[] = $signal;
	}

	usort( $items, static function ( $a, $b ) {
		return (int) ( $b['priority_score'] ?? 0 ) <=> (int) ( $a['priority_score'] ?? 0 );
	} );
	return $items;
}

function nexus_ci_admin_slug() {
	return 'nexus-seo-cockpit-opportunities';
}

function nexus_ci_register_admin_page() {
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Content Intelligence',
		'Opportunities',
		nexus_get_seo_cockpit_view_cap(),
		nexus_ci_admin_slug(),
		'nexus_ci_render_admin_page'
	);
}
add_action( 'admin_menu', 'nexus_ci_register_admin_page', 41 );

function nexus_ci_enqueue_admin_assets() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( nexus_ci_admin_slug() !== $page ) {
		return;
	}
	$path = get_stylesheet_directory() . '/assets/css/seo-cockpit-research.css';
	if ( file_exists( $path ) ) {
		wp_enqueue_style( 'nexus-seo-cockpit-research', get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-research.css', [], (string) filemtime( $path ) );
	}
	wp_add_inline_style(
		'nexus-seo-cockpit-research',
		'.nexus-ci-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:18px 0}.nexus-ci-summary article,.nexus-ci-card{background:#fff;border:1px solid #dcdcde;border-radius:14px;padding:18px}.nexus-ci-summary strong{display:block;font-size:26px;margin-top:6px}.nexus-ci-list{display:grid;gap:16px;margin-top:18px}.nexus-ci-head{display:flex;gap:18px;justify-content:space-between}.nexus-ci-score{display:inline-flex;align-items:center;justify-content:center;min-width:64px;height:64px;border-radius:50%;font-size:20px;font-weight:800;background:#f0f6fc;border:1px solid #c5d9ed}.nexus-ci-meta{color:#646970;font-size:13px}.nexus-ci-action{margin-top:16px;padding:14px;border-radius:10px;background:#f6f7f7}.nexus-ci-gsc{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:14px}.nexus-ci-gsc div{padding:12px;border:1px solid #e2e4e7;border-radius:9px}.nexus-ci-query{display:inline-block;background:#f0f0f1;border-radius:999px;padding:5px 9px;margin:8px 5px 0 0;font-size:12px}@media(max-width:900px){.nexus-ci-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.nexus-ci-gsc{grid-template-columns:1fr}}'
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_ci_enqueue_admin_assets' );

function nexus_ci_handle_refresh() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}
	check_admin_referer( 'nexus_ci_refresh' );
	if ( function_exists( 'nexus_schedule_seo_cockpit_research_background_refresh' ) ) {
		nexus_schedule_seo_cockpit_research_background_refresh();
	}
	wp_safe_redirect( admin_url( 'admin.php?page=' . nexus_ci_admin_slug() . '&ci_notice=queued' ) );
	exit;
}
add_action( 'admin_post_nexus_ci_refresh', 'nexus_ci_handle_refresh' );

function nexus_ci_render_admin_page() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	$history = nexus_ci_get_history();
	$items   = nexus_ci_opportunities();
	$snapshots = 0;
	foreach ( $history as $rows ) {
		$snapshots += is_array( $rows ) ? count( $rows ) : 0;
	}
	$high = count( array_filter( $items, static function ( $item ) {
		return (int) ( $item['priority_score'] ?? 0 ) >= 70;
	} ) );
	$with_gsc = count( array_filter( $items, static function ( $item ) {
		return nexus_ci_number( $item['gsc']['impressions'] ?? null ) >= 20;
	} ) );
	$last = absint( get_option( 'nexus_content_intelligence_last_capture', 0 ) );
	?>
	<div class="wrap nexus-seo-cockpit nexus-seo-cockpit__research">
		<p class="nexus-seo-cockpit__eyebrow">Content Intelligence</p>
		<div class="nexus-seo-cockpit__panel-head">
			<div>
				<h1>Marktsignale mit Search Console verbinden</h1>
				<p class="nexus-seo-cockpit__hint">Primärdaten → Veränderung → GSC-Nachfrage → Content-Maßnahme. V1 ist regelbasiert und veröffentlicht nichts automatisch.</p>
			</div>
			<?php if ( nexus_current_user_can_manage_seo_cockpit() ) : ?>
				<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_ci_refresh' ) ); ?>">
					<?php wp_nonce_field( 'nexus_ci_refresh' ); ?>
					<button type="submit" class="button button-primary">Research aktualisieren</button>
				</form>
			<?php endif; ?>
		</div>

		<div class="nexus-ci-summary">
			<article><span class="nexus-ci-meta">Snapshots</span><strong><?php echo esc_html( number_format_i18n( $snapshots ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Marktsignale</span><strong><?php echo esc_html( number_format_i18n( count( $items ) ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Priorität ≥ 70</span><strong><?php echo esc_html( number_format_i18n( $high ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Mit GSC-Nachfrage</span><strong><?php echo esc_html( number_format_i18n( $with_gsc ) ); ?></strong></article>
		</div>
		<p class="nexus-ci-meta">Letzte Research-Aufnahme: <?php echo esc_html( $last ? wp_date( 'd.m.Y H:i', $last ) : 'noch keine' ); ?></p>

		<?php if ( empty( $items ) ) : ?>
			<div class="nexus-ci-card"><h2>Noch kein materielles Marktsignal</h2><p>Nach dem ersten Background-Refresh werden die Primärdaten historisiert. Ohne überschrittene Schwelle erzeugt das System bewusst keine Aufgabe.</p></div>
		<?php else : ?>
			<div class="nexus-ci-list">
			<?php foreach ( $items as $item ) :
				$gsc     = is_array( $item['gsc'] ?? null ) ? $item['gsc'] : [];
				$best    = is_array( $gsc['best_page'] ?? null ) ? $gsc['best_page'] : null;
				$context = is_array( $gsc['target_context'] ?? null ) ? $gsc['target_context'] : [];
			?>
				<article class="nexus-ci-card">
					<div class="nexus-ci-head">
						<div>
							<p class="nexus-seo-cockpit__eyebrow"><?php echo esc_html( strtoupper( str_replace( '_', ' ', (string) $item['provider'] ) ) . ' · ' . (string) $item['period'] ); ?></p>
							<h2><?php echo esc_html( (string) $item['title'] ); ?></h2>
							<p><?php echo esc_html( (string) $item['context'] ); ?></p>
							<p class="nexus-ci-meta"><?php echo esc_html( (string) $item['label'] . ': ' . number_format_i18n( nexus_ci_number( $item['value'] ?? null ), 1 ) . ' ' . (string) $item['unit'] . ' · Veränderung ' . (string) $item['change_label'] ); ?></p>
						</div>
						<span class="nexus-ci-score"><?php echo esc_html( (string) $item['priority_score'] ); ?></span>
					</div>
					<div class="nexus-ci-action">
						<strong><?php echo esc_html( (string) ( $gsc['action_label'] ?? 'Beobachten' ) ); ?></strong>
						<p><?php echo esc_html( (string) ( $gsc['action_reason'] ?? '' ) ); ?></p>
						<?php if ( is_array( $best ) ) :
							$url = (string) ( $best['url'] ?? '' );
						?>
							<p><strong>Ziel:</strong> <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( '' !== (string) ( $context['post_title'] ?? '' ) ? (string) $context['post_title'] : $url ); ?></a></p>
						<?php endif; ?>
					</div>
					<div class="nexus-ci-gsc">
						<div><span class="nexus-ci-meta">Impressionen · 28 Tage</span><strong><?php echo esc_html( number_format_i18n( nexus_ci_number( $gsc['impressions'] ?? null ), 0 ) ); ?></strong></div>
						<div><span class="nexus-ci-meta">Passende URLs</span><strong><?php echo esc_html( number_format_i18n( absint( $gsc['page_count'] ?? 0 ) ) ); ?></strong></div>
						<div><span class="nexus-ci-meta">Beste URL · Ø Position</span><strong><?php echo esc_html( is_array( $best ) ? number_format_i18n( nexus_ci_number( $best['position'] ?? null ), 1 ) : '—' ); ?></strong></div>
					</div>
					<?php foreach ( (array) ( $gsc['top_queries'] ?? [] ) as $query ) : ?>
						<span class="nexus-ci-query"><?php echo esc_html( (string) ( $query['query'] ?? '' ) . ' · ' . number_format_i18n( nexus_ci_number( $query['impressions'] ?? null ), 0 ) . ' Impr.' ); ?></span>
					<?php endforeach; ?>
				</article>
			<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
