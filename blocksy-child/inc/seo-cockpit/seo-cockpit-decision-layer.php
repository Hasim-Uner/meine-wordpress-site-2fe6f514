<?php
/**
 * SEO Cockpit Decision Layer.
 *
 * Converts market, Search Console, Research, WordPress and CRM signals into a
 * small action queue. This module does not call external providers directly;
 * it consumes existing SEO Cockpit, Market Intelligence and Research snapshots.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @return string */
function nexus_decision_layer_admin_slug() {
	return function_exists( 'nexus_ci_admin_slug' ) ? nexus_ci_admin_slug() : 'nexus-seo-cockpit-opportunities';
}

/** @return string */
function nexus_decision_layer_admin_url() {
	return admin_url( 'admin.php?page=' . nexus_decision_layer_admin_slug() );
}

/**
 * Normalize one score to 0-100.
 *
 * @param mixed $score Raw score.
 * @return int
 */
function nexus_decision_layer_score( $score ) {
	return max( 0, min( 100, absint( $score ) ) );
}

/**
 * Return a compact priority label.
 *
 * @param int $score Decision score.
 * @return string
 */
function nexus_decision_layer_priority_label( $score ) {
	$score = nexus_decision_layer_score( $score );

	if ( $score >= 75 ) {
		return 'P1';
	}
	if ( $score >= 60 ) {
		return 'P2';
	}
	if ( $score >= 45 ) {
		return 'P3';
	}
	return 'P4';
}

/**
 * Build one deterministic next step for a Market Intelligence opportunity.
 *
 * @param array<string, mixed> $row Market opportunity.
 * @return string
 */
function nexus_decision_layer_market_next_step( $row ) {
	$action  = (string) ( $row['action'] ?? 'Beobachten' );
	$segment = sanitize_key( (string) ( $row['segment'] ?? 'other' ) );

	if ( 'Top-10-Push' === $action ) {
		return 'Zielseite gegen die aktuelle SERP prüfen, fehlende Themen und interne Links priorisieren und anschließend Snippet sowie Hauptabschnitte gezielt überarbeiten.';
	}

	if ( 'Seite ausbauen' === $action ) {
		return 'Suchintention und SERP-Abdeckung prüfen. Danach die bestehende Zielseite um die wichtigsten fehlenden Inhalte und interne Verlinkung erweitern.';
	}

	if ( 'Position verteidigen' === $action ) {
		return 'Aktualität, Belege und interne Verlinkung sichern. Nur ändern, wenn die Live-SERP oder GSC einen konkreten Verlust zeigt.';
	}

	if ( 'brand' === $segment ) {
		return 'Als Marken-/Proof-Sichtbarkeit beobachten. Nur investieren, wenn daraus nachweisbar qualifizierter Traffic, Leads oder strategische Autorität entsteht.';
	}

	return 'Signal beobachten und erst handeln, wenn Ranking, Nachfrage oder First-Party-Signale eine klare Maßnahme rechtfertigen.';
}

/**
 * Convert one market opportunity to the common decision format.
 *
 * @param array<string, mixed> $row Market opportunity.
 * @return array<string, mixed>
 */
function nexus_decision_layer_from_market( $row ) {
	$score       = nexus_decision_layer_score( $row['score'] ?? 0 );
	$segment     = sanitize_key( (string) ( $row['segment'] ?? 'other' ) );
	$action      = (string) ( $row['action'] ?? 'Beobachten' );
	$is_action   = 'brand' !== $segment && 'Beobachten' !== $action && $score >= 45;
	$evidence    = [];

	if ( absint( $row['rank'] ?? 0 ) > 0 ) {
		$evidence[] = 'DataForSEO Pos. ' . absint( $row['rank'] );
	}
	if ( (float) ( $row['search_volume'] ?? 0 ) > 0 ) {
		$evidence[] = 'Volumen ' . number_format_i18n( (float) $row['search_volume'], 0 );
	}
	if ( (float) ( $row['gsc_impressions'] ?? 0 ) > 0 ) {
		$evidence[] = 'GSC ' . number_format_i18n( (float) $row['gsc_impressions'], 0 ) . ' Impr.';
	}
	if ( absint( $row['crm_contacts_current'] ?? 0 ) > 0 ) {
		$evidence[] = number_format_i18n( absint( $row['crm_contacts_current'] ) ) . ' CRM-Kontakte';
	}
	if ( absint( $row['leads_current'] ?? 0 ) > 0 ) {
		$evidence[] = number_format_i18n( absint( $row['leads_current'] ) ) . ' Audit-Leads';
	}
	if ( ! empty( $row['page_role_label'] ) ) {
		$evidence[] = (string) $row['page_role_label'];
	}

	$why_parts = [];
	if ( ! empty( $row['segment_label'] ) ) {
		$why_parts[] = (string) $row['segment_label'];
	}
	if ( ! empty( $row['intent'] ) ) {
		$why_parts[] = 'Intent: ' . (string) $row['intent'];
	}
	if ( (float) ( $row['gsc_impressions'] ?? 0 ) > 0 ) {
		$why_parts[] = 'eigene GSC-Nachfrage vorhanden';
	}
	if ( absint( $row['crm_contacts_current'] ?? 0 ) > 0 || absint( $row['leads_current'] ?? 0 ) > 0 ) {
		$why_parts[] = 'First-Party-Lead-Signal vorhanden';
	}

	return [
		'id'             => 'market:' . md5( (string) ( $row['keyword'] ?? '' ) . '|' . (string) ( $row['url'] ?? '' ) ),
		'source'         => 'market',
		'source_label'   => 'Markt & Wettbewerb',
		'title'          => (string) ( $row['keyword'] ?? 'Keyword-Chance' ),
		'action_label'   => $action,
		'why'            => ! empty( $why_parts ) ? implode( ' · ', $why_parts ) : 'Externe Markt- und First-Party-Signale wurden zusammengeführt.',
		'next_step'      => nexus_decision_layer_market_next_step( $row ),
		'score'          => $score,
		'priority'       => nexus_decision_layer_priority_label( $score ),
		'is_actionable'  => $is_action,
		'segment'        => $segment,
		'segment_label'  => (string) ( $row['segment_label'] ?? 'Beobachten' ),
		'target_url'     => esc_url_raw( (string) ( $row['url'] ?? '' ) ),
		'evidence'       => $evidence,
		'sources'        => [ 'DataForSEO', 'GSC', 'WordPress', 'CRM' ],
	];
}

/**
 * Convert one Research/Content Intelligence opportunity to the common format.
 *
 * @param array<string, mixed> $item Content Intelligence signal.
 * @return array<string, mixed>
 */
function nexus_decision_layer_from_research( $item ) {
	$match         = is_array( $item['v11'] ?? null ) ? $item['v11'] : [];
	$market_score  = nexus_decision_layer_score( $item['market_score'] ?? 0 );
	$seo_score     = nexus_decision_layer_score( $match['seo_score'] ?? 0 );
	$content_fit   = nexus_decision_layer_score( $match['content_fit'] ?? 0 );
	$action        = sanitize_key( (string) ( $match['action'] ?? 'watch' ) );
	$is_actionable = 'watch' !== $action;
	$score         = (int) round( ( $market_score * 0.35 ) + ( $seo_score * 0.5 ) + ( $is_actionable ? 15 : 0 ) );
	$score         = nexus_decision_layer_score( $score );
	$best          = is_array( $match['best_page'] ?? null ) ? $match['best_page'] : [];
	$evidence      = [];

	if ( isset( $item['value'] ) && is_numeric( $item['value'] ) ) {
		$evidence[] = trim( number_format_i18n( (float) $item['value'], 1 ) . ' ' . (string) ( $item['unit'] ?? '' ) );
	}
	if ( ! empty( $item['change_label'] ) ) {
		$evidence[] = 'Veränderung ' . (string) $item['change_label'];
	}
	if ( (float) ( $match['impressions'] ?? 0 ) > 0 ) {
		$evidence[] = 'GSC ' . number_format_i18n( (float) $match['impressions'], 0 ) . ' Impr.';
	}
	if ( ! empty( $best ) && (float) ( $best['position'] ?? 0 ) > 0 ) {
		$evidence[] = 'Ø Pos. ' . number_format_i18n( (float) $best['position'], 1 );
	}
	$evidence[] = 'Content-Fit ' . $content_fit . '/100';

	$next_steps = [
		'expand'        => 'Bestehende Zielseite öffnen, neue Primärdaten an der fachlich passenden Stelle ergänzen und danach Title, interne Links und Aktualität prüfen.',
		'update'        => 'Bestehende Zielseite mit den neuen Primärdaten aktualisieren. Keine neue URL erzeugen, solange die vorhandene Seite die Suchintention sauber abdeckt.',
		'create_review' => 'Vor einer neuen URL zuerst SERP, Kannibalisierung und interne Themenabdeckung prüfen. Nur bei klarer Lücke eine neue Analyse anlegen.',
		'watch'         => 'Nur beobachten. Erst bei ausreichender direkter Suchnachfrage oder einer klaren bestehenden Zielseite in die Arbeitsqueue nehmen.',
	];

	return [
		'id'             => 'research:' . md5( (string) ( $item['provider'] ?? '' ) . '|' . (string) ( $item['title'] ?? '' ) . '|' . (string) ( $item['period'] ?? '' ) ),
		'source'         => 'research',
		'source_label'   => 'Research',
		'title'          => (string) ( $item['title'] ?? 'Marktsignal' ),
		'action_label'   => (string) ( $match['action_label'] ?? 'Marktbeobachtung' ),
		'why'            => (string) ( $match['action_reason'] ?? 'Primärdaten und Search Console wurden zusammengeführt.' ),
		'next_step'      => (string) ( $next_steps[ $action ] ?? $next_steps['watch'] ),
		'score'          => $score,
		'priority'       => nexus_decision_layer_priority_label( $score ),
		'is_actionable'  => $is_actionable,
		'segment'        => 'content',
		'segment_label'  => 'Content & Beleg',
		'target_url'     => esc_url_raw( (string) ( $best['url'] ?? '' ) ),
		'evidence'       => array_values( array_filter( $evidence ) ),
		'sources'        => [ 'Research', 'GSC', 'WordPress' ],
		'provider'       => sanitize_key( (string) ( $item['provider'] ?? '' ) ),
	];
}

/**
 * Build the common decision queue from already available snapshots.
 *
 * @param array<string, mixed> $seo_snapshot SEO snapshot.
 * @return array<string, mixed>
 */
function nexus_get_seo_cockpit_decision_layer( $seo_snapshot ) {
	$items = [];

	$market_rows = is_array( $seo_snapshot['market']['opportunities'] ?? null )
		? $seo_snapshot['market']['opportunities']
		: [];

	if ( empty( $market_rows ) && function_exists( 'nexus_get_market_intelligence_opportunities' ) ) {
		$market_rows = nexus_get_market_intelligence_opportunities( $seo_snapshot, 20 );
	}

	foreach ( (array) $market_rows as $row ) {
		if ( is_array( $row ) ) {
			$items[] = nexus_decision_layer_from_market( $row );
		}
	}

	if ( function_exists( 'nexus_ci_v11_opportunities' ) ) {
		foreach ( (array) nexus_ci_v11_opportunities() as $item ) {
			if ( is_array( $item ) ) {
				$items[] = nexus_decision_layer_from_research( $item );
			}
		}
	}

	usort(
		$items,
		static function ( $left, $right ) {
			$action_diff = (int) ! empty( $right['is_actionable'] ) <=> (int) ! empty( $left['is_actionable'] );
			if ( 0 !== $action_diff ) {
				return $action_diff;
			}

			$score_diff = absint( $right['score'] ?? 0 ) <=> absint( $left['score'] ?? 0 );
			if ( 0 !== $score_diff ) {
				return $score_diff;
			}

			return strcmp( (string) ( $left['title'] ?? '' ), (string) ( $right['title'] ?? '' ) );
		}
	);

	$actionable = [];
	$watch      = [];

	foreach ( $items as $item ) {
		if ( ! empty( $item['is_actionable'] ) ) {
			$actionable[] = $item;
		} else {
			$watch[] = $item;
		}
	}

	return [
		'generated_at' => time(),
		'actionable'   => array_slice( $actionable, 0, 12 ),
		'watch'        => array_slice( $watch, 0, 12 ),
		'all'          => $items,
		'counts'       => [
			'actionable' => count( $actionable ),
			'watch'      => count( $watch ),
			'total'      => count( $items ),
		],
	];
}

/**
 * Render evidence chips.
 *
 * @param array<string, mixed> $item Decision item.
 * @return void
 */
function nexus_render_decision_layer_evidence( $item ) {
	$evidence = array_values( array_filter( array_map( 'strval', (array) ( $item['evidence'] ?? [] ) ) ) );
	$sources  = array_values( array_filter( array_map( 'strval', (array) ( $item['sources'] ?? [] ) ) ) );
	?>
	<div class="nsc-decision__evidence">
		<?php foreach ( $evidence as $value ) : ?><span><?php echo esc_html( $value ); ?></span><?php endforeach; ?>
		<?php foreach ( $sources as $source ) : ?><span class="is-source"><?php echo esc_html( $source ); ?></span><?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render one decision card.
 *
 * @param array<string, mixed> $item Decision item.
 * @param bool                 $compact Compact dashboard mode.
 * @return void
 */
function nexus_render_decision_layer_item( $item, $compact = false ) {
	$target_url = (string) ( $item['target_url'] ?? '' );
	?>
	<article class="nsc-decision<?php echo $compact ? ' is-compact' : ''; ?>">
		<div class="nsc-decision__score">
			<strong><?php echo esc_html( (string) nexus_decision_layer_score( $item['score'] ?? 0 ) ); ?></strong>
			<span><?php echo esc_html( (string) ( $item['priority'] ?? 'P4' ) ); ?></span>
		</div>
		<div class="nsc-decision__body">
			<div class="nsc-decision__head">
				<div>
					<p class="nsc-decision__source"><?php echo esc_html( (string) ( $item['source_label'] ?? '' ) ); ?> · <?php echo esc_html( (string) ( $item['segment_label'] ?? '' ) ); ?></p>
					<h3><?php echo esc_html( (string) ( $item['title'] ?? '' ) ); ?></h3>
				</div>
				<span class="nsc-decision__action"><?php echo esc_html( (string) ( $item['action_label'] ?? 'Beobachten' ) ); ?></span>
			</div>

			<?php if ( ! $compact ) : ?>
				<div class="nsc-decision__reason">
					<strong>Warum jetzt?</strong>
					<p><?php echo esc_html( (string) ( $item['why'] ?? '' ) ); ?></p>
				</div>
				<div class="nsc-decision__next">
					<strong>Nächster Schritt</strong>
					<p><?php echo esc_html( (string) ( $item['next_step'] ?? '' ) ); ?></p>
				</div>
				<?php nexus_render_decision_layer_evidence( $item ); ?>
			<?php else : ?>
				<p class="nsc-decision__compact-next"><?php echo esc_html( (string) ( $item['next_step'] ?? '' ) ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $target_url ) : ?>
				<a class="nsc-decision__target" href="<?php echo esc_url( $target_url ); ?>" target="_blank" rel="noopener noreferrer">Zielseite öffnen</a>
			<?php endif; ?>
		</div>
	</article>
	<?php
}

/**
 * Register the simplified Opportunities workspace.
 *
 * @return void
 */
function nexus_register_decision_layer_admin_page() {
	$slug = nexus_decision_layer_admin_slug();
	$hook = function_exists( 'get_plugin_page_hookname' )
		? get_plugin_page_hookname( $slug, nexus_get_seo_cockpit_menu_slug() )
		: '';

	if ( is_string( $hook ) && '' !== $hook ) {
		remove_action( $hook, 'nexus_ci_render_admin_page' );
		remove_action( $hook, 'nexus_ci_v11_render_admin_page' );
	}

	remove_submenu_page( nexus_get_seo_cockpit_menu_slug(), $slug );

	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Content-Chancen',
		'Content-Chancen',
		nexus_get_seo_cockpit_view_cap(),
		$slug,
		'nexus_render_decision_layer_admin_page'
	);
}
add_action( 'admin_menu', 'nexus_register_decision_layer_admin_page', 120 );

/**
 * Load Decision Layer styles only where required.
 *
 * @return void
 */
function nexus_enqueue_decision_layer_assets() {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( ! in_array( $page, [ nexus_decision_layer_admin_slug(), nexus_get_seo_cockpit_menu_slug() ], true ) ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/seo-cockpit-decision.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'nexus-seo-cockpit-decision',
		get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-decision.css',
		[],
		(string) filemtime( $path )
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_enqueue_decision_layer_assets', 35 );

/**
 * Render the simplified Content-Chancen workspace.
 *
 * @return void
 */
function nexus_render_decision_layer_admin_page() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	$snapshot = function_exists( 'nexus_get_seo_cockpit_snapshot' )
		? nexus_get_seo_cockpit_snapshot( false, 28 )
		: [];

	$error = is_wp_error( $snapshot ) ? $snapshot : null;
	$data  = ! $error && is_array( $snapshot )
		? nexus_get_seo_cockpit_decision_layer( $snapshot )
		: [ 'actionable' => [], 'watch' => [], 'counts' => [ 'actionable' => 0, 'watch' => 0, 'total' => 0 ] ];

	$counts = is_array( $data['counts'] ?? null ) ? $data['counts'] : [];
	?>
	<div class="wrap nexus-seo-cockpit nsc-decision-page">
		<header class="nsc-decision-hero">
			<div>
				<p class="nexus-seo-cockpit__eyebrow">Decision Layer</p>
				<h1>Was du als Nächstes tun solltest</h1>
				<p>Markt, Search Console, Research, WordPress und CRM werden zu einer kleinen Arbeitsqueue verdichtet. Die Rohdaten bleiben in den jeweiligen Datenbereichen; hier zählt die Konsequenz.</p>
			</div>
			<div class="nsc-decision-hero__links">
				<a class="button" href="<?php echo esc_url( function_exists( 'nexus_market_intelligence_admin_url' ) ? nexus_market_intelligence_admin_url() : admin_url() ); ?>">Markt &amp; Wettbewerb</a>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . nexus_get_seo_cockpit_research_slug() ) ); ?>">Datenquellen</a>
			</div>
		</header>

		<?php if ( $error ) : ?>
			<div class="notice notice-warning inline"><p><?php echo esc_html( $error->get_error_message() ); ?></p></div>
		<?php endif; ?>

		<section class="nsc-decision-summary">
			<article><span>Jetzt bearbeiten</span><strong><?php echo esc_html( number_format_i18n( absint( $counts['actionable'] ?? 0 ) ) ); ?></strong><small>klare nächste Maßnahme</small></article>
			<article><span>Beobachten</span><strong><?php echo esc_html( number_format_i18n( absint( $counts['watch'] ?? 0 ) ) ); ?></strong><small>noch kein Arbeitsauftrag</small></article>
			<article><span>Datenlogik</span><strong>5</strong><small>Markt · GSC · Research · WP · CRM</small></article>
		</section>

		<section class="nsc-decision-section">
			<div class="nsc-decision-section__head">
				<div><p class="nexus-seo-cockpit__eyebrow">Arbeitsqueue</p><h2>Jetzt bearbeiten</h2><p>Nur Signale mit einer konkreten Handlung. Der Score ist Priorisierung, keine Erfolgsprognose.</p></div>
			</div>
			<div class="nsc-decision-list">
				<?php if ( empty( $data['actionable'] ) ) : ?>
					<p class="nsc-decision-empty">Aktuell gibt es keine ausreichend belastbare Content-/SEO-Maßnahme. Das ist ein gültiger Zustand.</p>
				<?php else : ?>
					<?php foreach ( (array) $data['actionable'] as $item ) : nexus_render_decision_layer_item( (array) $item ); endforeach; ?>
				<?php endif; ?>
			</div>
		</section>

		<section class="nsc-decision-section is-secondary">
			<details>
				<summary>Beobachten &amp; Belege <span><?php echo esc_html( number_format_i18n( absint( $counts['watch'] ?? 0 ) ) ); ?></span></summary>
				<p class="nsc-decision-section__intro">Diese Signale bleiben sichtbar, blockieren aber nicht deine tägliche Arbeitsqueue.</p>
				<div class="nsc-decision-list">
					<?php if ( empty( $data['watch'] ) ) : ?>
						<p class="nsc-decision-empty">Keine zusätzlichen Beobachtungssignale.</p>
					<?php else : ?>
						<?php foreach ( (array) $data['watch'] as $item ) : nexus_render_decision_layer_item( (array) $item ); endforeach; ?>
					<?php endif; ?>
				</div>
			</details>
		</section>
	</div>
	<?php
}

/**
 * Render the top Decision Layer actions inside Dashboard V3.
 *
 * @param array<string, mixed> $snapshot Current SEO snapshot.
 * @return void
 */
function nexus_render_decision_layer_dashboard_panel( $snapshot ) {
	if ( empty( $snapshot ) || ! is_array( $snapshot ) ) {
		return;
	}

	$data  = nexus_get_seo_cockpit_decision_layer( $snapshot );
	$items = array_slice( (array) ( $data['actionable'] ?? [] ), 0, 3 );

	if ( empty( $items ) ) {
		return;
	}
	?>
	<section class="nsc-v3-section nsc-decision-dashboard" aria-labelledby="nsc-decision-dashboard-title">
		<div class="nsc-v3-section__head">
			<div>
				<p class="nsc-v3-eyebrow">Decision Layer</p>
				<h2 id="nsc-decision-dashboard-title">Nächste Entscheidungen</h2>
				<p>Die drei stärksten handlungsfähigen Signale aus Markt, Search Console, Research, WordPress und CRM.</p>
			</div>
			<a class="nsc-v3-button" href="<?php echo esc_url( nexus_decision_layer_admin_url() ); ?>">Alle Content-Chancen</a>
		</div>
		<div class="nsc-decision-dashboard__list">
			<?php foreach ( $items as $item ) : nexus_render_decision_layer_item( (array) $item, true ); endforeach; ?>
		</div>
	</section>
	<?php
}
