<?php
/**
 * SEO Cockpit Content Intelligence V1.1.
 *
 * Tightens Search Console matching around the actual market/data intent of a
 * signal and separates market relevance, SEO opportunity and page/content fit.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize one phrase for deterministic matching.
 *
 * @param string $value Raw text.
 * @return string
 */
function nexus_ci_v11_normalize( $value ) {
	$value = function_exists( 'nexus_normalize_seo_cockpit_query' )
		? nexus_normalize_seo_cockpit_query( $value )
		: strtolower( trim( (string) $value ) );

	$replace = [
		'ä' => 'ae',
		'ö' => 'oe',
		'ü' => 'ue',
		'ß' => 'ss',
	];

	return strtr( (string) $value, $replace );
}

/**
 * Test whether normalized haystack contains at least one term.
 *
 * @param string            $haystack Normalized text.
 * @param array<int,string> $terms Terms.
 * @return bool
 */
function nexus_ci_v11_contains_any( $haystack, $terms ) {
	foreach ( $terms as $term ) {
		$needle = nexus_ci_v11_normalize( $term );
		if ( '' !== $needle && false !== strpos( $haystack, $needle ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Count distinct matching terms.
 *
 * @param string            $haystack Normalized text.
 * @param array<int,string> $terms Terms.
 * @return int
 */
function nexus_ci_v11_count_hits( $haystack, $terms ) {
	$count = 0;
	foreach ( array_unique( $terms ) as $term ) {
		$needle = nexus_ci_v11_normalize( $term );
		if ( '' !== $needle && false !== strpos( $haystack, $needle ) ) {
			$count++;
		}
	}
	return $count;
}

/**
 * Return a strict topic corridor for one market signal.
 *
 * @param array<string,mixed> $signal Signal.
 * @return array<string,mixed>
 */
function nexus_ci_v11_profile_for_signal( $signal ) {
	$title = nexus_ci_v11_normalize( (string) ( $signal['title'] ?? '' ) );

	$commercial_excludes = [
		'lead', 'leads', 'leadgenerierung', 'termin', 'termine', 'anfrage', 'anfragen',
		'kaufen', 'anbieter', 'agentur', 'dienstleister', 'serioes', 'checkfox', 'kosten',
		'preis', 'preise', 'b2b', 'loesung', 'vertriebspartner', 'vermittlung',
	];

	if ( false !== strpos( $title, 'pv-ausbau' ) ) {
		return [
			'label'         => 'PV-Ausbau / Markt',
			'subject_terms' => [ 'pv', 'photovoltaik', 'solar' ],
			'context_terms' => [ 'ausbau', 'zubau', 'leistung', 'installiert', 'installierte', 'markt', 'deutschland', 'gw', 'gigawatt', 'bestand', 'entwicklung', 'kapazitaet', 'kapazitaeten', '2025', '2026' ],
			'exclude_terms' => $commercial_excludes,
		];
	}

	if ( false !== strpos( $title, 'solaranteil' ) ) {
		return [
			'label'         => 'Solarstrom / Anteil',
			'subject_terms' => [ 'solar', 'photovoltaik', 'pv', 'solarstrom', 'strom' ],
			'context_terms' => [ 'anteil', 'erzeugung', 'strommix', 'eigenverbrauch', 'speicher', 'solarstrom', 'erneuerbar', 'prozent', 'produktion', 'einspeisung', '2025', '2026' ],
			'exclude_terms' => $commercial_excludes,
		];
	}

	if ( false !== strpos( $title, 'erneuerbarer strom' ) ) {
		return [
			'label'         => 'Erneuerbarer Strom / Strommix',
			'subject_terms' => [ 'erneuerbar', 'strom', 'strommix', 'solar', 'photovoltaik', 'pv' ],
			'context_terms' => [ 'anteil', 'deutschland', 'eu', 'eurostat', 'erzeugung', 'energie', 'strommix', 'vergleich', 'statistik', '2025', '2026' ],
			'exclude_terms' => $commercial_excludes,
		];
	}

	if ( false !== strpos( $title, 'erneuerbaren-anteil' ) ) {
		return [
			'label'         => 'Erneuerbare Energien / Markt',
			'subject_terms' => [ 'erneuerbar', 'energiewende', 'solar', 'photovoltaik', 'pv', 'waermepump' ],
			'context_terms' => [ 'anteil', 'deutschland', 'eu', 'eurostat', 'energie', 'energiemix', 'strommix', 'vergleich', 'statistik', 'markt', '2025', '2026' ],
			'exclude_terms' => $commercial_excludes,
		];
	}

	if ( false !== strpos( $title, 'destatis' ) || false !== strpos( $title, 'gebaeudestruktur' ) ) {
		return [
			'label'         => 'Gebäudebestand / Marktpotenzial',
			'subject_terms' => [ 'waermepump', 'heizung', 'gebaeude', 'wohngebaeude', 'solar', 'photovoltaik', 'pv' ],
			'context_terms' => [ 'bestand', 'gebaeude', 'wohngebaeude', 'einfamilienhaus', 'zweifamilienhaus', 'deutschland', 'niedersachsen', 'statistik', 'marktpotenzial', 'struktur', '2025', '2026' ],
			'exclude_terms' => $commercial_excludes,
		];
	}

	return [
		'label'         => 'Markt-/Datenanalyse',
		'subject_terms' => array_values( array_filter( array_map( 'strval', (array) ( $signal['keywords'] ?? [] ) ) ) ),
		'context_terms' => [ 'markt', 'entwicklung', 'anteil', 'statistik', 'deutschland', '2025', '2026' ],
		'exclude_terms' => $commercial_excludes,
	];
}

/**
 * Score query fit to a signal from 0 to 100.
 * Requires both topic and market/data context. Commercial lead intent is
 * explicitly excluded from market-signal matching.
 *
 * @param string              $query Query.
 * @param array<string,mixed> $profile Signal profile.
 * @return int
 */
function nexus_ci_v11_query_fit_score( $query, $profile ) {
	$normalized = nexus_ci_v11_normalize( $query );
	if ( '' === $normalized ) {
		return 0;
	}

	$excluded = nexus_ci_v11_contains_any( $normalized, (array) ( $profile['exclude_terms'] ?? [] ) );
	if ( $excluded ) {
		return 0;
	}

	$subject_hits = nexus_ci_v11_count_hits( $normalized, (array) ( $profile['subject_terms'] ?? [] ) );
	$context_hits = nexus_ci_v11_count_hits( $normalized, (array) ( $profile['context_terms'] ?? [] ) );
	if ( $subject_hits < 1 || $context_hits < 1 ) {
		return 0;
	}

	$score = 45 + min( 25, $subject_hits * 10 ) + min( 30, $context_hits * 10 );
	return min( 100, $score );
}

/**
 * Score fit of an already-ranking page to the signal corridor.
 *
 * @param array<string,mixed>|null $page Best GSC page.
 * @param array<string,mixed>      $profile Signal profile.
 * @return array<string,mixed>
 */
function nexus_ci_v11_page_fit( $page, $profile ) {
	if ( ! is_array( $page ) || empty( $page['url'] ) ) {
		return [ 'score' => 0, 'context' => [], 'has_commercial_conflict' => false ];
	}

	$url     = (string) $page['url'];
	$context = function_exists( 'nexus_get_seo_cockpit_wp_context_for_url' ) ? nexus_get_seo_cockpit_wp_context_for_url( $url ) : [];
	$haystack = nexus_ci_v11_normalize(
		$url . ' ' .
		(string) ( $context['post_title'] ?? '' ) . ' ' .
		(string) ( $context['seo_title'] ?? '' ) . ' ' .
		(string) ( $context['seo_description'] ?? '' )
	);

	$subject_hits = nexus_ci_v11_count_hits( $haystack, (array) ( $profile['subject_terms'] ?? [] ) );
	$context_hits = nexus_ci_v11_count_hits( $haystack, (array) ( $profile['context_terms'] ?? [] ) );
	$commercial   = nexus_ci_v11_contains_any( $haystack, (array) ( $profile['exclude_terms'] ?? [] ) );

	$score = min( 40, $subject_hits * 15 ) + min( 60, $context_hits * 20 );
	if ( $commercial ) {
		$score -= 35;
	}
	$score = max( 0, min( 100, $score ) );

	return [
		'score'                   => $score,
		'context'                 => is_array( $context ) ? $context : [],
		'has_commercial_conflict' => $commercial,
	];
}

/**
 * Turn direct matching demand into a 0-100 SEO-opportunity score.
 *
 * @param float $impressions Matching impressions.
 * @param float $position Best page position.
 * @param int   $query_count Number of matching queries.
 * @return int
 */
function nexus_ci_v11_seo_score( $impressions, $position, $query_count ) {
	$score = 0;
	if ( $impressions >= 500 ) {
		$score += 45;
	} elseif ( $impressions >= 200 ) {
		$score += 40;
	} elseif ( $impressions >= 100 ) {
		$score += 35;
	} elseif ( $impressions >= 50 ) {
		$score += 30;
	} elseif ( $impressions >= 20 ) {
		$score += 22;
	} elseif ( $impressions >= 10 ) {
		$score += 15;
	} elseif ( $impressions > 0 ) {
		$score += 8;
	}

	if ( $position > 0 && $position < 4 ) {
		$score += 25;
	} elseif ( $position <= 10 && $position > 0 ) {
		$score += 35;
	} elseif ( $position <= 20 && $position > 0 ) {
		$score += 30;
	} elseif ( $position <= 40 && $position > 0 ) {
		$score += 20;
	} elseif ( $position <= 70 && $position > 0 ) {
		$score += 10;
	} elseif ( $position > 70 ) {
		$score += 5;
	}

	$score += min( 20, max( 0, $query_count ) * 4 );
	return min( 100, $score );
}

/**
 * Strict V1.1 GSC matcher.
 *
 * @param array<string,mixed> $signal Signal.
 * @param array<string,mixed> $snapshot GSC snapshot.
 * @return array<string,mixed>
 */
function nexus_ci_v11_match_gsc( $signal, $snapshot ) {
	$rows      = (array) ( $snapshot['query_page_rows'] ?? [] );
	$profile   = nexus_ci_v11_profile_for_signal( $signal );
	$page_impr = [];
	$page_click = [];
	$page_pos_w = [];
	$page_pos_n = [];
	$queries   = [];

	foreach ( $rows as $row ) {
		$page  = function_exists( 'nexus_get_seo_cockpit_row_key' ) ? nexus_get_seo_cockpit_row_key( $row, 0 ) : (string) ( $row['keys'][0] ?? '' );
		$query = function_exists( 'nexus_get_seo_cockpit_row_key' ) ? nexus_get_seo_cockpit_row_key( $row, 1 ) : (string) ( $row['keys'][1] ?? '' );
		if ( '' === $page || '' === $query ) {
			continue;
		}
		if ( function_exists( 'nexus_is_seo_cockpit_non_target_query' ) && nexus_is_seo_cockpit_non_target_query( $query ) ) {
			continue;
		}

		$query_fit = nexus_ci_v11_query_fit_score( $query, $profile );
		if ( $query_fit < 60 ) {
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
			'position'    => $position,
			'fit'         => $query_fit,
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
	$best_position = is_array( $best ) ? nexus_ci_number( $best['position'] ) : 0.0;
	$page_fit      = nexus_ci_v11_page_fit( $best, $profile );
	$content_fit   = (int) ( $page_fit['score'] ?? 0 );
	$seo_score     = nexus_ci_v11_seo_score( $total, $best_position, count( $queries ) );

	$action = 'watch';
	$label  = 'Marktbeobachtung';
	$reason = 'Das Signal ist fachlich relevant, aber es gibt aktuell keine ausreichend direkte Search-Console-Nachfrage für eine Content-Maßnahme.';

	if ( $total >= 20 && $seo_score >= 45 ) {
		if ( $content_fit >= 60 && is_array( $best ) ) {
			if ( $best_position >= 6 && $best_position <= 25 ) {
				$action = 'expand';
				$label  = 'Passende Seite erweitern';
				$reason = 'Direkte Suchnachfrage ist vorhanden und eine bestehende Seite passt thematisch. Neue Primärdaten sollten dort ergänzt werden.';
			} else {
				$action = 'update';
				$label  = 'Passende Seite aktualisieren';
				$reason = 'Direkte Suchnachfrage und ein guter Seiten-Fit sind vorhanden. Die bestehende Seite ist das sinnvollste Ziel.';
			}
		} elseif ( $content_fit < 60 ) {
			$action = 'create_review';
			$label  = 'Neue Analyse prüfen';
			$reason = 'Es gibt direkte Suchnachfrage zum Marktsignal, aber keine ausreichend passende bestehende Seite. Eine neue Analyse ist plausibel; Kannibalisierung muss vor Erstellung geprüft werden.';
		}
	}

	return [
		'profile'          => $profile,
		'impressions'      => $total,
		'page_count'       => count( $pages ),
		'best_page'        => $best,
		'top_queries'      => array_slice( $queries, 0, 5 ),
		'seo_score'        => $seo_score,
		'content_fit'      => $content_fit,
		'action'           => $action,
		'action_label'     => $label,
		'action_reason'    => $reason,
		'target_context'   => (array) ( $page_fit['context'] ?? [] ),
		'commercial_conflict' => ! empty( $page_fit['has_commercial_conflict'] ),
	];
}

/**
 * Convert the signal-only components into a 0-100 market relevance score.
 *
 * @param array<string,mixed> $signal Signal.
 * @return int
 */
function nexus_ci_v11_market_score( $signal ) {
	$raw = (int) ( $signal['market_score'] ?? 0 ) + (int) ( $signal['fresh_score'] ?? 0 ) + (int) ( $signal['business_score'] ?? 0 ) + 5;
	return min( 100, (int) round( ( $raw / 80 ) * 100 ) );
}

/** @return array<int,array<string,mixed>> */
function nexus_ci_v11_opportunities() {
	$snapshot = function_exists( 'nexus_ci_cached_gsc_snapshot' ) ? nexus_ci_cached_gsc_snapshot() : [];
	$items    = [];

	foreach ( nexus_ci_signals() as $signal ) {
		$match = ! empty( $snapshot ) ? nexus_ci_v11_match_gsc( $signal, $snapshot ) : [
			'profile' => nexus_ci_v11_profile_for_signal( $signal ),
			'impressions' => 0.0,
			'page_count' => 0,
			'best_page' => null,
			'top_queries' => [],
			'seo_score' => 0,
			'content_fit' => 0,
			'action' => 'watch',
			'action_label' => 'Search Console fehlt',
			'action_reason' => 'Ohne Search-Console-Kontext erzeugt Content Intelligence bewusst keine Content-Empfehlung.',
			'target_context' => [],
			'commercial_conflict' => false,
		];

		$signal['v11']          = $match;
		$signal['market_score'] = nexus_ci_v11_market_score( $signal );
		$items[]                = $signal;
	}

	usort( $items, static function ( $a, $b ) {
		$left  = max( (int) $a['market_score'], (int) ( $a['v11']['seo_score'] ?? 0 ) );
		$right = max( (int) $b['market_score'], (int) ( $b['v11']['seo_score'] ?? 0 ) );
		return $right <=> $left;
	} );

	return $items;
}

/** Replace the V1 renderer with the stricter V1.1 renderer. */
function nexus_ci_v11_register_admin_page() {
	remove_submenu_page( nexus_get_seo_cockpit_menu_slug(), nexus_ci_admin_slug() );
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Content Intelligence',
		'Opportunities',
		nexus_get_seo_cockpit_view_cap(),
		nexus_ci_admin_slug(),
		'nexus_ci_v11_render_admin_page'
	);
}
add_action( 'admin_menu', 'nexus_ci_v11_register_admin_page', 42 );

/** Add V1.1 display styles on top of the existing Content Intelligence CSS. */
function nexus_ci_v11_enqueue_styles() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( nexus_ci_admin_slug() !== $page ) {
		return;
	}
	wp_add_inline_style(
		'nexus-seo-cockpit-research',
		'.nexus-ci-v11-scores{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:14px 0}.nexus-ci-v11-score{padding:12px;border:1px solid #e2e4e7;border-radius:10px;background:#fff}.nexus-ci-v11-score strong{display:block;font-size:24px;margin-top:4px}.nexus-ci-v11-fit-low{color:#b32d2e}.nexus-ci-v11-fit-mid{color:#996800}.nexus-ci-v11-fit-high{color:#008a20}.nexus-ci-v11-note{margin-top:10px;padding:10px 12px;border-left:3px solid #72aee6;background:#f6f7f7}.nexus-ci-v11-profile{display:inline-block;margin-top:6px;padding:4px 8px;border-radius:999px;background:#f0f0f1;font-size:12px}.nexus-ci-v11-noquery{color:#646970;font-style:italic;margin-top:10px}@media(max-width:900px){.nexus-ci-v11-scores{grid-template-columns:1fr}}'
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_ci_v11_enqueue_styles', 20 );

/** Return a score CSS class. */
function nexus_ci_v11_score_class( $score ) {
	if ( $score >= 70 ) {
		return 'nexus-ci-v11-fit-high';
	}
	if ( $score >= 40 ) {
		return 'nexus-ci-v11-fit-mid';
	}
	return 'nexus-ci-v11-fit-low';
}

function nexus_ci_v11_render_admin_page() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	$history   = nexus_ci_get_history();
	$items     = nexus_ci_v11_opportunities();
	$snapshots = 0;
	foreach ( $history as $rows ) {
		$snapshots += is_array( $rows ) ? count( $rows ) : 0;
	}

	$seo_opportunities = count( array_filter( $items, static function ( $item ) {
		return (int) ( $item['v11']['seo_score'] ?? 0 ) >= 60;
	} ) );
	$good_fit = count( array_filter( $items, static function ( $item ) {
		return (int) ( $item['v11']['content_fit'] ?? 0 ) >= 60;
	} ) );
	$actions = count( array_filter( $items, static function ( $item ) {
		return 'watch' !== (string) ( $item['v11']['action'] ?? 'watch' );
	} ) );
	$last = absint( get_option( 'nexus_content_intelligence_last_capture', 0 ) );
	?>
	<div class="wrap nexus-seo-cockpit nexus-seo-cockpit__research">
		<p class="nexus-seo-cockpit__eyebrow">Content Intelligence · V1.1</p>
		<div class="nexus-seo-cockpit__panel-head">
			<div>
				<h1>Marktsignale mit echter Suchintention verbinden</h1>
				<p class="nexus-seo-cockpit__hint">Primärdaten → Marktsignal → direkte GSC-Nachfrage → Seiten-Fit → Content-Maßnahme. Kommerzielle Lead-Queries werden nicht mehr automatisch als Marktinteresse gewertet.</p>
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
			<article><span class="nexus-ci-meta">SEO-Chancen ≥ 60</span><strong><?php echo esc_html( number_format_i18n( $seo_opportunities ) ); ?></strong></article>
			<article><span class="nexus-ci-meta">Konkrete Maßnahmen</span><strong><?php echo esc_html( number_format_i18n( $actions ) ); ?></strong></article>
		</div>
		<p class="nexus-ci-meta">Guter bestehender Seiten-Fit ≥ 60: <?php echo esc_html( number_format_i18n( $good_fit ) ); ?> · Letzte Research-Aufnahme: <?php echo esc_html( $last ? wp_date( 'd.m.Y H:i', $last ) : 'noch keine' ); ?></p>

		<?php if ( empty( $items ) ) : ?>
		<div class="nexus-ci-card"><h2>Noch kein materielles Marktsignal</h2><p>Ohne überschrittene fachliche Schwelle erzeugt das System bewusst keine Aufgabe.</p></div>
		<?php else : ?>
		<div class="nexus-ci-list">
		<?php foreach ( $items as $item ) :
			$match   = is_array( $item['v11'] ?? null ) ? $item['v11'] : [];
			$profile = is_array( $match['profile'] ?? null ) ? $match['profile'] : [];
			$best    = is_array( $match['best_page'] ?? null ) ? $match['best_page'] : null;
			$context = is_array( $match['target_context'] ?? null ) ? $match['target_context'] : [];
			$market_score = (int) ( $item['market_score'] ?? 0 );
			$seo_score    = (int) ( $match['seo_score'] ?? 0 );
			$content_fit  = (int) ( $match['content_fit'] ?? 0 );
		?>
		<article class="nexus-ci-card">
			<div class="nexus-ci-head">
				<div>
					<p class="nexus-seo-cockpit__eyebrow"><?php echo esc_html( strtoupper( str_replace( '_', ' ', (string) $item['provider'] ) ) . ' · ' . (string) $item['period'] ); ?></p>
					<h2><?php echo esc_html( (string) $item['title'] ); ?></h2>
					<p><?php echo esc_html( (string) $item['context'] ); ?></p>
					<p class="nexus-ci-meta"><?php echo esc_html( (string) $item['label'] . ': ' . number_format_i18n( nexus_ci_number( $item['value'] ?? null ), 1 ) . ' ' . (string) $item['unit'] . ' · Veränderung ' . (string) $item['change_label'] ); ?></p>
					<span class="nexus-ci-v11-profile">Intent: <?php echo esc_html( (string) ( $profile['label'] ?? 'Markt-/Datenanalyse' ) ); ?></span>
				</div>
			</div>

			<div class="nexus-ci-v11-scores">
				<div class="nexus-ci-v11-score"><span class="nexus-ci-meta">Marktsignal</span><strong class="<?php echo esc_attr( nexus_ci_v11_score_class( $market_score ) ); ?>"><?php echo esc_html( (string) $market_score ); ?>/100</strong></div>
				<div class="nexus-ci-v11-score"><span class="nexus-ci-meta">SEO-Chance</span><strong class="<?php echo esc_attr( nexus_ci_v11_score_class( $seo_score ) ); ?>"><?php echo esc_html( (string) $seo_score ); ?>/100</strong></div>
				<div class="nexus-ci-v11-score"><span class="nexus-ci-meta">Bestehender Content-Fit</span><strong class="<?php echo esc_attr( nexus_ci_v11_score_class( $content_fit ) ); ?>"><?php echo esc_html( (string) $content_fit ); ?>/100</strong></div>
			</div>

			<div class="nexus-ci-action">
				<strong><?php echo esc_html( (string) ( $match['action_label'] ?? 'Marktbeobachtung' ) ); ?></strong>
				<p><?php echo esc_html( (string) ( $match['action_reason'] ?? '' ) ); ?></p>
				<?php if ( is_array( $best ) ) :
					$url = (string) ( $best['url'] ?? '' );
					$title = '' !== (string) ( $context['post_title'] ?? '' ) ? (string) $context['post_title'] : $url;
				?>
				<p><strong><?php echo esc_html( $content_fit >= 60 ? 'Passende bestehende Seite:' : 'Aktuell rankende URL, noch nicht als Ziel bestätigt:' ); ?></strong> <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $title ); ?></a></p>
				<?php endif; ?>
			</div>

			<div class="nexus-ci-gsc">
				<div><span class="nexus-ci-meta">Direkt passende Impressionen · 28 Tage</span><strong><?php echo esc_html( number_format_i18n( nexus_ci_number( $match['impressions'] ?? null ), 0 ) ); ?></strong></div>
				<div><span class="nexus-ci-meta">Passende URLs</span><strong><?php echo esc_html( number_format_i18n( absint( $match['page_count'] ?? 0 ) ) ); ?></strong></div>
				<div><span class="nexus-ci-meta">Beste URL · Ø Position</span><strong><?php echo esc_html( is_array( $best ) ? number_format_i18n( nexus_ci_number( $best['position'] ?? null ), 1 ) : '—' ); ?></strong></div>
			</div>

			<?php if ( ! empty( $match['commercial_conflict'] ) ) : ?>
			<p class="nexus-ci-v11-note">Die aktuell rankende Seite ist überwiegend kommerziell ausgerichtet. Sie wird deshalb nicht automatisch als Ziel für einen Markt-/Datenartikel empfohlen.</p>
			<?php endif; ?>

			<?php if ( empty( $match['top_queries'] ) ) : ?>
			<p class="nexus-ci-v11-noquery">Keine direkt passenden Markt-/Daten-Queries im aktuellen 28-Tage-GSC-Snapshot.</p>
			<?php else : ?>
			<?php foreach ( (array) $match['top_queries'] as $query ) : ?>
			<span class="nexus-ci-query"><?php echo esc_html( (string) ( $query['query'] ?? '' ) . ' · ' . number_format_i18n( nexus_ci_number( $query['impressions'] ?? null ), 0 ) . ' Impr.' ); ?></span>
			<?php endforeach; ?>
			<?php endif; ?>
		</article>
		<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php
}
