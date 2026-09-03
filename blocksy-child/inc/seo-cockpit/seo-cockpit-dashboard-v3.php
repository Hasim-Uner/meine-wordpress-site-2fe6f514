<?php
/**
 * SEO Cockpit Dashboard V3.
 *
 * Visual command-center layer for the existing SEO Cockpit data model.
 * OAuth, snapshots, cron, Search Console writes and URL drilldowns stay in
 * their existing modules; this file only changes top-level rendering.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace only the top-level cockpit callback with the V3 renderer.
 *
 * Search Console registers its own submenu separately at priority 30.
 *
 * @return void
 */
function nexus_register_seo_cockpit_menu_v3() {
	add_menu_page(
		'SEO Cockpit',
		'SEO Cockpit',
		nexus_get_seo_cockpit_view_cap(),
		nexus_get_seo_cockpit_menu_slug(),
		'nexus_render_seo_cockpit_dashboard_v3',
		'dashicons-chart-area',
		59
	);

	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'SEO Cockpit',
		'Übersicht',
		nexus_get_seo_cockpit_view_cap(),
		nexus_get_seo_cockpit_menu_slug(),
		'nexus_render_seo_cockpit_dashboard_v3'
	);

	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'SEO Cockpit Einstellungen',
		'Einstellungen',
		nexus_get_seo_cockpit_manage_cap(),
		'nexus-seo-cockpit-settings',
		'nexus_render_seo_cockpit_settings_page'
	);
}
remove_action( 'admin_menu', 'nexus_register_seo_cockpit_menu' );
add_action( 'admin_menu', 'nexus_register_seo_cockpit_menu_v3' );

/**
 * Layer V3 styles over the existing cockpit stylesheet.
 *
 * @param string $hook Current admin hook.
 * @return void
 */
function nexus_enqueue_seo_cockpit_dashboard_v3_assets( $hook ) {
	if ( false === strpos( (string) $hook, nexus_get_seo_cockpit_menu_slug() ) ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/seo-cockpit-dashboard-v3.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'nexus-seo-cockpit-dashboard-v3',
		get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-dashboard-v3.css',
		[ 'nexus-seo-cockpit-admin' ],
		filemtime( $path )
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_enqueue_seo_cockpit_dashboard_v3_assets', 20 );

/**
 * Return a dashicon class for one cockpit action type.
 *
 * @param array<string, mixed> $row Action row.
 * @return string
 */
function nexus_seo_cockpit_v3_action_icon( $row ) {
	$type    = strtolower( (string) ( $row['type'] ?? '' ) );
	$problem = strtolower( (string) ( $row['problem'] ?? '' ) );

	if ( false !== strpos( $problem, 'index' ) || false !== strpos( $problem, 'sitemap' ) ) {
		return 'dashicons-search';
	}

	if ( false !== strpos( $problem, 'link' ) ) {
		return 'dashicons-admin-links';
	}

	if ( false !== strpos( $problem, 'ctr' ) || false !== strpos( $problem, 'snippet' ) ) {
		return 'dashicons-chart-line';
	}

	if ( false !== strpos( $problem, 'kannibal' ) ) {
		return 'dashicons-warning';
	}

	$map = [
		'cta'      => 'dashicons-megaphone',
		'tracking' => 'dashicons-chart-area',
		'lead'     => 'dashicons-groups',
		'page'     => 'dashicons-admin-page',
		'manual'   => 'dashicons-visibility',
	];

	return $map[ $type ] ?? 'dashicons-lightbulb';
}

/**
 * Convert the existing revenue queue into three visual action lanes.
 *
 * @param array<string, mixed> $command Revenue command payload.
 * @return array<string, array<int, array<string, mixed>>>
 */
function nexus_seo_cockpit_v3_action_lanes( $command ) {
	$rows  = is_array( $command['rows'] ?? null ) ? $command['rows'] : [];
	$lanes = [
		'now'   => [],
		'week'  => [],
		'watch' => [],
	];

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$status = sanitize_key( (string) ( $row['status'] ?? 'new' ) );
		if ( in_array( $status, [ 'done', 'ignored' ], true ) ) {
			continue;
		}

		$score  = absint( $row['priority_score'] ?? 0 );
		$bucket = sanitize_key( (string) ( $row['priority_bucket'] ?? 'low' ) );

		if ( in_array( $status, [ 'today', 'in_progress' ], true ) || 'critical' === $bucket || $score >= 72 ) {
			$lanes['now'][] = $row;
		} elseif ( in_array( $bucket, [ 'high', 'medium' ], true ) || $score >= 48 ) {
			$lanes['week'][] = $row;
		} else {
			$lanes['watch'][] = $row;
		}
	}

	if ( empty( $lanes['now'] ) ) {
		$promoted     = array_slice( $lanes['week'], 0, 3 );
		$promoted_ids = array_map(
			static function ( $row ) {
				return (string) ( $row['id'] ?? '' );
			},
			$promoted
		);
		$lanes['now'] = $promoted;
		$lanes['week'] = array_values(
			array_filter(
				$lanes['week'],
				static function ( $row ) use ( $promoted_ids ) {
					return ! in_array( (string) ( $row['id'] ?? '' ), $promoted_ids, true );
				}
			)
		);
	}

	return $lanes;
}

/**
 * Render the status control inside one action card.
 *
 * @param array<string, mixed> $row Action row.
 * @return void
 */
function nexus_seo_cockpit_v3_render_status_control( $row ) {
	$labels = function_exists( 'nexus_get_revenue_command_center_status_labels' )
		? nexus_get_revenue_command_center_status_labels()
		: [
			'new'         => 'Neu',
			'today'       => 'Heute',
			'in_progress' => 'In Arbeit',
			'done'        => 'Erledigt',
			'ignored'     => 'Ignorieren',
		];

	$status = sanitize_key( (string) ( $row['status'] ?? 'new' ) );
	if ( ! isset( $labels[ $status ] ) ) {
		$status = 'new';
	}

	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		echo '<span class="nsc-v3-status-pill">' . esc_html( $labels[ $status ] ) . '</span>';
		return;
	}
	?>
	<form class="nsc-v3-status-control" method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_revenue_command_center_status' ) ); ?>">
		<?php wp_nonce_field( 'nexus_revenue_command_center_status' ); ?>
		<input type="hidden" name="item_id" value="<?php echo esc_attr( (string) ( $row['id'] ?? '' ) ); ?>">
		<input type="hidden" name="range" value="<?php echo esc_attr( (string) nexus_get_seo_cockpit_requested_range_days() ); ?>">
		<input type="hidden" name="detail_url" value="">
		<label class="screen-reader-text" for="nsc-v3-status-<?php echo esc_attr( (string) ( $row['id'] ?? '' ) ); ?>">Status</label>
		<select id="nsc-v3-status-<?php echo esc_attr( (string) ( $row['id'] ?? '' ) ); ?>" name="item_status">
			<?php foreach ( $labels as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<button type="submit" class="nsc-v3-icon-button" aria-label="Status speichern"><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span></button>
	</form>
	<?php
}

/**
 * Render one visual action card.
 *
 * @param array<string, mixed> $row Action row.
 * @return void
 */
function nexus_seo_cockpit_v3_render_action_card( $row ) {
	$bucket      = sanitize_key( (string) ( $row['priority_bucket'] ?? 'low' ) );
	$score       = absint( $row['priority_score'] ?? 0 );
	$problem     = trim( (string) ( $row['problem'] ?? 'SEO-Signal prüfen' ) );
	$target      = trim( (string) ( $row['target_label'] ?? '' ) );
	$target_url  = trim( (string) ( $row['target_url'] ?? '' ) );
	$admin_url   = trim( (string) ( $row['admin_url'] ?? '' ) );
	$funnel_role = trim( (string) ( $row['funnel_role'] ?? '' ) );
	$next_action = trim( (string) ( $row['next_action'] ?? '' ) );
	$why_now     = trim( (string) ( $row['why_now'] ?? '' ) );
	$effort      = trim( (string) ( $row['effort'] ?? 'M' ) );
	$confidence  = trim( (string) ( $row['confidence'] ?? 'mittel' ) );
	$icon        = nexus_seo_cockpit_v3_action_icon( $row );

	if ( '' === $target && '' !== $target_url && function_exists( 'nexus_get_seo_cockpit_short_url' ) ) {
		$target = nexus_get_seo_cockpit_short_url( $target_url );
	}

	$link = '' !== $admin_url ? $admin_url : $target_url;
	?>
	<article class="nsc-v3-action-card is-<?php echo esc_attr( $bucket ); ?>">
		<div class="nsc-v3-action-card__top">
			<span class="nsc-v3-action-card__icon" aria-hidden="true"><span class="dashicons <?php echo esc_attr( $icon ); ?>"></span></span>
			<div class="nsc-v3-action-card__priority"><span class="nsc-v3-priority-dot"></span><?php echo esc_html( strtoupper( $bucket ) ); ?> · <?php echo esc_html( (string) $score ); ?></div>
		</div>

		<div class="nsc-v3-action-card__body">
			<h4><?php echo esc_html( $problem ); ?></h4>
			<?php if ( '' !== $target ) : ?>
				<p class="nsc-v3-action-card__target">
					<?php if ( '' !== $link ) : ?>
						<a href="<?php echo esc_url( $link ); ?>"<?php echo '' === $admin_url ? ' target="_blank" rel="noreferrer noopener"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $target ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $target ); ?>
					<?php endif; ?>
					<?php if ( '' !== $funnel_role ) : ?><span><?php echo esc_html( $funnel_role ); ?></span><?php endif; ?>
				</p>
			<?php endif; ?>

			<?php if ( '' !== $why_now ) : ?><p class="nsc-v3-action-card__reason"><?php echo esc_html( $why_now ); ?></p><?php endif; ?>
			<?php if ( '' !== $next_action ) : ?>
				<div class="nsc-v3-action-card__next"><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span><span><?php echo esc_html( $next_action ); ?></span></div>
			<?php endif; ?>
		</div>

		<div class="nsc-v3-action-card__footer">
			<div class="nsc-v3-action-card__meta"><span>Aufwand <?php echo esc_html( $effort ); ?></span><span>Confidence <?php echo esc_html( $confidence ); ?></span></div>
			<?php nexus_seo_cockpit_v3_render_status_control( $row ); ?>
		</div>
	</article>
	<?php
}

/**
 * Render the Action Hub lanes.
 *
 * @param array<string, mixed> $command Revenue command payload.
 * @return void
 */
function nexus_seo_cockpit_v3_render_action_hub( $command ) {
	$lanes = nexus_seo_cockpit_v3_action_lanes( $command );
	$definitions = [
		'now' => [ 'label' => 'Sofort', 'note' => 'Die stärksten Hebel zuerst.', 'icon' => 'dashicons-warning' ],
		'week' => [ 'label' => 'Diese Woche', 'note' => 'Relevant, aber nicht akut.', 'icon' => 'dashicons-calendar-alt' ],
		'watch' => [ 'label' => 'Beobachten', 'note' => 'Signale sammeln, nicht überreagieren.', 'icon' => 'dashicons-visibility' ],
	];
	?>
	<section class="nsc-v3-section nsc-v3-action-hub" aria-labelledby="nsc-v3-action-hub-title">
		<div class="nsc-v3-section__head">
			<div><p class="nsc-v3-eyebrow">Action Hub</p><h2 id="nsc-v3-action-hub-title">Die richtigen Maßnahmen. Zum richtigen Zeitpunkt.</h2><p>Keine To-do-Wand mehr: priorisiert nach Revenue-Signal, Funnel-Nähe, SEO-Potenzial und Datensicherheit.</p></div>
			<?php if ( function_exists( 'nexus_get_seo_cockpit_search_console_control_url' ) ) : ?>
				<a class="nsc-v3-text-link" href="<?php echo esc_url( nexus_get_seo_cockpit_search_console_control_url() ); ?>">Search Console <span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></a>
			<?php endif; ?>
		</div>

		<div class="nsc-v3-lanes">
			<?php foreach ( $definitions as $lane_key => $definition ) : ?>
				<?php $lane_rows = (array) ( $lanes[ $lane_key ] ?? [] ); ?>
				<div class="nsc-v3-lane is-<?php echo esc_attr( $lane_key ); ?>">
					<div class="nsc-v3-lane__head">
						<div><span class="dashicons <?php echo esc_attr( $definition['icon'] ); ?>" aria-hidden="true"></span><strong><?php echo esc_html( $definition['label'] ); ?></strong><span class="nsc-v3-lane__count"><?php echo esc_html( (string) count( $lane_rows ) ); ?></span></div>
						<p><?php echo esc_html( $definition['note'] ); ?></p>
					</div>
					<div class="nsc-v3-lane__cards">
						<?php if ( empty( $lane_rows ) ) : ?>
							<div class="nsc-v3-empty-card"><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><p>Aktuell kein belastbarer Eintrag in dieser Spur.</p></div>
						<?php else : ?>
							<?php foreach ( array_slice( $lane_rows, 0, 4 ) as $row ) : ?><?php nexus_seo_cockpit_v3_render_action_card( $row ); ?><?php endforeach; ?>
							<?php if ( count( $lane_rows ) > 4 ) : ?><p class="nsc-v3-lane__more">+ <?php echo esc_html( (string) ( count( $lane_rows ) - 4 ) ); ?> weitere Signale</p><?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}

/**
 * Build SVG points for a compact sparkline.
 *
 * @param array<int, array<string, mixed>> $trend Trend rows.
 * @param string                           $metric Metric key.
 * @param int                              $width Width.
 * @param int                              $height Height.
 * @return string
 */
function nexus_seo_cockpit_v3_sparkline_points( $trend, $metric, $width = 180, $height = 42 ) {
	$values = [];
	foreach ( (array) $trend as $point ) {
		if ( is_array( $point ) ) {
			$values[] = (float) ( $point[ $metric ] ?? 0 );
		}
	}
	if ( count( $values ) < 2 ) {
		return '';
	}

	$min    = min( $values );
	$max    = max( $values );
	$span   = max( 0.0001, $max - $min );
	$last   = max( 1, count( $values ) - 1 );
	$points = [];
	foreach ( $values as $index => $value ) {
		$x = ( $index / $last ) * $width;
		$y = $height - ( ( ( $value - $min ) / $span ) * ( $height - 6 ) ) - 3;
		$points[] = number_format( $x, 1, '.', '' ) . ',' . number_format( $y, 1, '.', '' );
	}
	return implode( ' ', $points );
}

/**
 * Render one KPI card.
 *
 * @param string                           $key Metric key.
 * @param string                           $label Label.
 * @param array<string, mixed>             $current Current overview.
 * @param array<string, mixed>             $previous Previous overview.
 * @param array<int, array<string, mixed>> $trend Trend rows.
 * @return void
 */
function nexus_seo_cockpit_v3_render_metric_card( $key, $label, $current, $previous, $trend ) {
	$current_value  = (float) ( $current[ $key ] ?? 0 );
	$previous_value = (float) ( $previous[ $key ] ?? 0 );
	$delta          = nexus_get_seo_cockpit_metric_delta( $key, $current_value, $previous_value );
	$points         = nexus_seo_cockpit_v3_sparkline_points( $trend, $key );
	?>
	<article class="nsc-v3-kpi">
		<div class="nsc-v3-kpi__head"><span><?php echo esc_html( $label ); ?></span><span class="nsc-v3-kpi__delta is-<?php echo esc_attr( $delta['class'] ); ?>"><?php echo esc_html( $delta['label'] ); ?></span></div>
		<strong class="nsc-v3-kpi__value"><?php echo esc_html( nexus_format_seo_cockpit_metric( $key, $current_value ) ); ?></strong>
		<?php if ( '' !== $points ) : ?><svg class="nsc-v3-kpi__sparkline" viewBox="0 0 180 42" role="img" aria-label="<?php echo esc_attr( $label . ' Verlauf' ); ?>"><polyline points="<?php echo esc_attr( $points ); ?>"></polyline></svg><?php endif; ?>
	</article>
	<?php
}

/**
 * Render a summary KPI for active actions.
 *
 * @param array<string, mixed> $command Revenue command payload.
 * @return void
 */
function nexus_seo_cockpit_v3_render_action_metric( $command ) {
	$summary = is_array( $command['summary'] ?? null ) ? $command['summary'] : [];
	$active  = absint( $summary['active'] ?? 0 );
	$leaks   = absint( $summary['conversion_leaks'] ?? 0 );
	?>
	<article class="nsc-v3-kpi nsc-v3-kpi--action">
		<div class="nsc-v3-kpi__head"><span>Aktive Hebel</span><span class="nsc-v3-kpi__delta is-neutral"><?php echo esc_html( (string) $leaks ); ?> Conversion Leaks</span></div>
		<strong class="nsc-v3-kpi__value"><?php echo esc_html( number_format_i18n( $active ) ); ?></strong>
		<div class="nsc-v3-kpi__action-line"><span></span><span></span><span></span><span></span><span></span></div>
	</article>
	<?php
}

/**
 * Render quick-win opportunities as visual rows.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @return void
 */
function nexus_seo_cockpit_v3_render_opportunities( $snapshot ) {
	$rows = function_exists( 'nexus_get_seo_cockpit_quick_wins' ) ? nexus_get_seo_cockpit_quick_wins( $snapshot, 6 ) : [];
	if ( empty( $rows ) ) {
		echo '<div class="nsc-v3-empty-state"><span class="dashicons dashicons-lightbulb"></span><p>Noch keine belastbaren Striking-Distance-Chancen in diesem Zeitraum.</p></div>';
		return;
	}

	$max_score = max( 1.0, max( array_map( static function ( $row ) { return (float) ( $row['score'] ?? 0 ); }, $rows ) ) );
	?>
	<div class="nsc-v3-opportunities">
		<?php foreach ( $rows as $row ) : ?>
			<?php
			$score  = (float) ( $row['score'] ?? 0 );
			$width  = max( 8, min( 100, ( $score / $max_score ) * 100 ) );
			$query  = (string) ( $row['query'] ?? '' );
			$page   = (string) ( $row['page'] ?? '' );
			$detail = (string) ( $row['detail_url'] ?? '' );
			?>
			<article class="nsc-v3-opportunity">
				<div class="nsc-v3-opportunity__main"><span class="nsc-v3-opportunity__icon"><span class="dashicons dashicons-chart-line" aria-hidden="true"></span></span><div><strong><?php echo esc_html( $query ); ?></strong><a href="<?php echo esc_url( $detail ); ?>"><?php echo esc_html( function_exists( 'nexus_get_seo_cockpit_short_url' ) ? nexus_get_seo_cockpit_short_url( $page ) : $page ); ?></a></div></div>
				<div class="nsc-v3-opportunity__bar" aria-hidden="true"><span style="width:<?php echo esc_attr( number_format( $width, 1, '.', '' ) ); ?>%"></span></div>
				<div class="nsc-v3-opportunity__metrics"><span><?php echo esc_html( number_format_i18n( (float) ( $row['impressions'] ?? 0 ) ) ); ?> Impr.</span><span>Pos. <?php echo esc_html( number_format_i18n( (float) ( $row['position'] ?? 0 ), 1 ) ); ?></span><span><?php echo esc_html( number_format_i18n( (float) ( ( $row['ctr'] ?? 0 ) * 100 ), 1 ) ); ?>% CTR</span></div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render compact query movers.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @return void
 */
function nexus_seo_cockpit_v3_render_movers( $snapshot ) {
	$movers = function_exists( 'nexus_get_seo_cockpit_query_movers' ) ? nexus_get_seo_cockpit_query_movers( $snapshot, 4 ) : [ 'gainers' => [], 'losers' => [] ];
	?>
	<div class="nsc-v3-mover-groups">
		<?php foreach ( [ 'gainers' => 'Gewinner', 'losers' => 'Verlierer' ] as $key => $label ) : ?>
			<?php $rows = (array) ( $movers[ $key ] ?? [] ); ?>
			<div class="nsc-v3-mover-group is-<?php echo esc_attr( $key ); ?>">
				<div class="nsc-v3-mover-group__head"><strong><?php echo esc_html( $label ); ?></strong><span><?php echo esc_html( 'gainers' === $key ? 'Klicks gewonnen' : 'Klicks verloren' ); ?></span></div>
				<?php if ( empty( $rows ) ) : ?><p class="nsc-v3-muted">Keine signifikante Bewegung.</p><?php else : ?>
					<?php foreach ( $rows as $row ) : ?><div class="nsc-v3-mover-row"><div><strong><?php echo esc_html( (string) ( $row['query'] ?? '' ) ); ?></strong><span>Pos. <?php echo esc_html( number_format_i18n( (float) ( $row['position'] ?? 0 ), 1 ) ); ?></span></div><b><?php echo esc_html( ( (float) ( $row['delta'] ?? 0 ) > 0 ? '+' : '' ) . number_format_i18n( (float) ( $row['delta'] ?? 0 ), 0 ) ); ?></b></div><?php endforeach; ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render the strongest problem URLs as cards rather than another table.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @return void
 */
function nexus_seo_cockpit_v3_render_problem_cards( $snapshot ) {
	$pages = array_slice( (array) ( $snapshot['problem_pages'] ?? [] ), 0, 6 );
	if ( empty( $pages ) ) {
		echo '<div class="nsc-v3-empty-state"><span class="dashicons dashicons-yes-alt"></span><p>Keine priorisierten Problemseiten für diesen Zeitraum.</p></div>';
		return;
	}
	?>
	<div class="nsc-v3-url-cards">
		<?php foreach ( $pages as $page ) : ?>
			<?php
			if ( ! is_array( $page ) ) { continue; }
			$primary = is_array( $page['primary'] ?? null ) ? $page['primary'] : [];
			$row     = is_array( $page['row'] ?? null ) ? $page['row'] : [];
			$context = is_array( $page['context'] ?? null ) ? $page['context'] : [];
			$bucket  = sanitize_key( (string) ( $primary['priority_bucket'] ?? 'low' ) );
			$url     = (string) ( $page['url'] ?? '' );
			$title   = (string) ( $context['post_title'] ?? '' );
			$title   = '' !== $title ? $title : ( function_exists( 'nexus_get_seo_cockpit_short_url' ) ? nexus_get_seo_cockpit_short_url( $url ) : $url );
			?>
			<article class="nsc-v3-url-card is-<?php echo esc_attr( $bucket ); ?>">
				<div class="nsc-v3-url-card__head"><span class="nsc-v3-badge is-<?php echo esc_attr( $bucket ); ?>"><?php echo esc_html( (string) ( $primary['priority_label'] ?? strtoupper( $bucket ) ) ); ?> · <?php echo esc_html( (string) absint( $primary['priority_score'] ?? 0 ) ); ?></span><span><?php echo esc_html( (string) ( $primary['page_role_label'] ?? '' ) ); ?></span></div>
				<h4><a href="<?php echo esc_url( (string) ( $page['detail_url'] ?? '' ) ); ?>"><?php echo esc_html( $title ); ?></a></h4>
				<p><?php echo esc_html( (string) ( $primary['label'] ?? 'SEO-Signal prüfen' ) ); ?></p>
				<div class="nsc-v3-url-card__metrics"><span><strong><?php echo esc_html( number_format_i18n( (float) ( $row['impressions'] ?? 0 ) ) ); ?></strong> Impr.</span><span><strong><?php echo esc_html( number_format_i18n( (float) ( $row['clicks'] ?? 0 ) ) ); ?></strong> Klicks</span><span><strong><?php echo esc_html( number_format_i18n( (float) ( $row['position'] ?? 0 ), 1 ) ); ?></strong> Pos.</span></div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render the Search Console / onsite system-status panel.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @param array<string, mixed> $setup Setup state.
 * @param array<string, mixed> $runtime Runtime summary.
 * @param array<string, mixed> $koko Koko status.
 * @return void
 */
function nexus_seo_cockpit_v3_render_system_status( $snapshot, $setup, $runtime, $koko ) {
	$sitemaps     = (array) ( $snapshot['sitemaps'] ?? [] );
	$sitemap      = ! empty( $sitemaps ) && is_array( $sitemaps[0] ) ? $sitemaps[0] : [];
	$lead         = is_array( $snapshot['leads'] ?? null ) ? $snapshot['leads'] : [];
	$lead_current = is_array( $lead['overview']['current'] ?? null ) ? $lead['overview']['current'] : [];
	$write_scope  = function_exists( 'nexus_seo_cockpit_has_write_scope' ) ? nexus_seo_cockpit_has_write_scope() : false;
	?>
	<div class="nsc-v3-system-grid">
		<article class="nsc-v3-system-card"><div class="nsc-v3-system-card__icon is-success"><span class="dashicons dashicons-google" aria-hidden="true"></span></div><div><span>Search Console</span><strong><?php echo esc_html( ! empty( $setup['is_connected'] ) ? 'Verbunden' : 'Nicht verbunden' ); ?></strong><p><?php echo esc_html( $write_scope ? 'Lesen + Sitemap schreiben' : 'Lesend / Scope prüfen' ); ?></p></div><span class="dashicons <?php echo ! empty( $setup['is_connected'] ) ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>" aria-hidden="true"></span></article>
		<article class="nsc-v3-system-card"><div class="nsc-v3-system-card__icon"><span class="dashicons dashicons-media-code" aria-hidden="true"></span></div><div><span>Sitemap</span><strong><?php echo esc_html( ! empty( $sitemap ) ? ( ! empty( $sitemap['isPending'] ) ? 'Pending' : 'Aktiv' ) : 'Keine Daten' ); ?></strong><p><?php echo esc_html( ! empty( $sitemap['path'] ) ? (string) $sitemap['path'] : 'wp-sitemap.xml' ); ?></p></div><span class="dashicons <?php echo ! empty( $sitemap ) ? 'dashicons-yes-alt' : 'dashicons-minus'; ?>" aria-hidden="true"></span></article>
		<article class="nsc-v3-system-card"><div class="nsc-v3-system-card__icon"><span class="dashicons dashicons-chart-pie" aria-hidden="true"></span></div><div><span>Koko Analytics</span><strong><?php echo esc_html( ! empty( $koko['active'] ) ? 'Aktiv' : 'Nicht aktiv' ); ?></strong><p>Onsite-Kontext zur GSC-Nachfrage</p></div><span class="dashicons <?php echo ! empty( $koko['active'] ) ? 'dashicons-yes-alt' : 'dashicons-minus'; ?>" aria-hidden="true"></span></article>
		<article class="nsc-v3-system-card"><div class="nsc-v3-system-card__icon"><span class="dashicons dashicons-groups" aria-hidden="true"></span></div><div><span>Audit-CRM</span><strong><?php echo esc_html( number_format_i18n( (int) ( $lead_current['requests'] ?? 0 ) ) ); ?> Leads</strong><p><?php echo esc_html( number_format_i18n( (int) ( $lead_current['mapped_requests'] ?? 0 ) ) ); ?> intern zugeordnet</p></div><span class="dashicons dashicons-chart-area" aria-hidden="true"></span></article>
	</div>
	<p class="nsc-v3-system-note">Letzter Sync: <strong><?php echo esc_html( ! empty( $runtime['last_sync_at'] ) ? wp_date( 'd.m.Y H:i', (int) $runtime['last_sync_at'] ) : 'n/a' ); ?></strong><?php if ( ! empty( $runtime['next_sync_at'] ) ) : ?> · Nächster Sync: <strong><?php echo esc_html( wp_date( 'd.m.Y H:i', (int) $runtime['next_sync_at'] ) ); ?></strong><?php endif; ?></p>
	<?php
}

/**
 * Render compact raw tables inside a secondary disclosure.
 *
 * @param array<string, mixed> $snapshot Snapshot payload.
 * @return void
 */
function nexus_seo_cockpit_v3_render_raw_tables( $snapshot ) {
	$pages   = array_slice( array_values( (array) ( $snapshot['top_pages'] ?? [] ) ), 0, 8 );
	$queries = array_slice( array_values( (array) ( $snapshot['top_queries'] ?? [] ) ), 0, 8 );
	?>
	<details class="nsc-v3-raw">
		<summary><span><strong>Rohdaten & Tabellen</strong><small>Top Pages und Top Queries kompakt aufklappen</small></span><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></summary>
		<div class="nsc-v3-raw__body"><div class="nsc-v3-raw__grid">
			<section><h3>Top Pages</h3><div class="nexus-seo-cockpit__table-wrap"><table class="widefat striped nexus-seo-cockpit__table nexus-seo-cockpit__table--urls"><thead><tr><th>URL</th><th>Klicks</th><th>Impr.</th><th>CTR</th><th>Pos.</th></tr></thead><tbody>
			<?php foreach ( $pages as $row ) : ?><?php $url = function_exists( 'nexus_get_seo_cockpit_row_key' ) ? nexus_get_seo_cockpit_row_key( $row, 0 ) : (string) ( $row['keys'][0] ?? '' ); ?><tr><td class="nexus-seo-cockpit__cell--url"><a href="<?php echo esc_url( nexus_get_seo_cockpit_detail_url( $url ) ); ?>"><?php echo esc_html( function_exists( 'nexus_get_seo_cockpit_short_url' ) ? nexus_get_seo_cockpit_short_url( $url ) : $url ); ?></a></td><td><?php echo esc_html( number_format_i18n( (float) ( $row['clicks'] ?? 0 ) ) ); ?></td><td><?php echo esc_html( number_format_i18n( (float) ( $row['impressions'] ?? 0 ) ) ); ?></td><td><?php echo esc_html( number_format_i18n( (float) ( ( $row['ctr'] ?? 0 ) * 100 ), 1 ) . '%' ); ?></td><td><?php echo esc_html( number_format_i18n( (float) ( $row['position'] ?? 0 ), 1 ) ); ?></td></tr><?php endforeach; ?>
			</tbody></table></div></section>
			<section><h3>Top Queries</h3><div class="nexus-seo-cockpit__table-wrap"><table class="widefat striped nexus-seo-cockpit__table"><thead><tr><th>Query</th><th>Klicks</th><th>Impr.</th><th>CTR</th><th>Pos.</th></tr></thead><tbody>
			<?php foreach ( $queries as $row ) : ?><tr><td><strong><?php echo esc_html( function_exists( 'nexus_get_seo_cockpit_row_label' ) ? nexus_get_seo_cockpit_row_label( $row ) : (string) ( $row['keys'][0] ?? '' ) ); ?></strong></td><td><?php echo esc_html( number_format_i18n( (float) ( $row['clicks'] ?? 0 ) ) ); ?></td><td><?php echo esc_html( number_format_i18n( (float) ( $row['impressions'] ?? 0 ) ) ); ?></td><td><?php echo esc_html( number_format_i18n( (float) ( ( $row['ctr'] ?? 0 ) * 100 ), 1 ) . '%' ); ?></td><td><?php echo esc_html( number_format_i18n( (float) ( $row['position'] ?? 0 ), 1 ) ); ?></td></tr><?php endforeach; ?>
			</tbody></table></div></section>
		</div></div>
	</details>
	<?php
}

/**
 * Render the V3 top-level SEO Cockpit.
 *
 * URL drilldown requests deliberately delegate to the existing renderer so
 * the established inspection/detail contract remains unchanged.
 *
 * @return void
 */
function nexus_render_seo_cockpit_dashboard_v3() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		return;
	}

	$detail_url = nexus_get_seo_cockpit_selected_detail_url();
	if ( '' !== $detail_url ) {
		nexus_render_seo_cockpit_dashboard();
		return;
	}

	$setup          = nexus_get_seo_cockpit_setup_state();
	$config         = is_array( $setup['config'] ?? null ) ? $setup['config'] : [];
	$runtime        = nexus_get_seo_cockpit_runtime_summary();
	$koko           = nexus_get_koko_analytics_status();
	$range_days     = nexus_get_seo_cockpit_requested_range_days();
	$is_connected   = ! empty( $setup['is_connected'] );
	$can_manage     = nexus_current_user_can_manage_seo_cockpit();
	$snapshot       = nexus_get_seo_cockpit_snapshot( false, $range_days );
	$snapshot_error = is_wp_error( $snapshot );
	$command        = function_exists( 'nexus_get_revenue_command_center_data' ) ? nexus_get_revenue_command_center_data( $snapshot_error ? [] : (array) $snapshot, $range_days, $snapshot_error ) : [];
	?>
	<div class="wrap nexus-seo-cockpit nsc-v3">
		<?php nexus_render_seo_cockpit_notice(); ?>

		<header class="nsc-v3-hero">
			<div class="nsc-v3-hero__brand"><span class="nsc-v3-hero__mark" aria-hidden="true"><span class="dashicons dashicons-chart-area"></span></span><div><p class="nsc-v3-eyebrow">Organic Growth Command Center</p><h1>SEO Cockpit</h1><p>Performance, Chancen und technische Prioritäten auf einen Blick.</p></div></div>
			<div class="nsc-v3-hero__right">
				<div class="nsc-v3-hero__status"><span class="nsc-v3-live-dot <?php echo $is_connected ? 'is-live' : 'is-offline'; ?>"></span><div><strong><?php echo esc_html( $is_connected ? 'Search Console live' : 'Search Console offline' ); ?></strong><span><?php echo esc_html( (string) ( $config['property'] ?? 'Property nicht gesetzt' ) ); ?></span></div></div>
				<div class="nsc-v3-hero__actions">
					<?php nexus_render_seo_cockpit_range_switcher( $range_days, '' ); ?>
					<?php if ( $can_manage && $is_connected ) : ?>
						<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_refresh' ) ); ?>"><?php wp_nonce_field( 'nexus_seo_cockpit_refresh' ); ?><input type="hidden" name="range" value="<?php echo esc_attr( (string) $range_days ); ?>"><input type="hidden" name="detail_url" value=""><button type="submit" class="nsc-v3-button nsc-v3-button--primary"><span class="dashicons dashicons-update" aria-hidden="true"></span>Synchronisieren</button></form>
						<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_export' ) ); ?>"><?php wp_nonce_field( 'nexus_seo_cockpit_export' ); ?><input type="hidden" name="range" value="<?php echo esc_attr( (string) $range_days ); ?>"><button type="submit" class="nsc-v3-button"><span class="dashicons dashicons-download" aria-hidden="true"></span>CSV</button></form>
					<?php elseif ( $can_manage ) : ?>
						<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_connect' ) ); ?>"><?php wp_nonce_field( 'nexus_seo_cockpit_connect' ); ?><button type="submit" class="nsc-v3-button nsc-v3-button--primary" <?php disabled( ! nexus_has_seo_cockpit_search_console_credentials() ); ?>>Google verbinden</button></form>
					<?php endif; ?>
				</div>
			</div>
		</header>

		<?php if ( empty( $setup['is_ready'] ) ) : ?>
			<section class="nsc-v3-setup"><span class="dashicons dashicons-admin-tools" aria-hidden="true"></span><div><strong>Cockpit noch nicht komplett eingerichtet</strong><p><?php echo esc_html( ! empty( $setup['missing'] ) ? 'Fehlt: ' . implode( ', ', (array) $setup['missing'] ) : 'Google-Verbindung noch nicht vollständig.' ); ?></p></div><?php if ( $can_manage ) : ?><a class="nsc-v3-button" href="<?php echo esc_url( nexus_get_seo_cockpit_settings_url() ); ?>">Einstellungen</a><?php endif; ?></section>
		<?php endif; ?>

		<?php if ( $snapshot_error ) : ?>
			<?php nexus_seo_cockpit_v3_render_action_hub( $command ); ?>
			<section class="nsc-v3-section"><div class="nsc-v3-empty-state nsc-v3-empty-state--large"><span class="dashicons dashicons-chart-area" aria-hidden="true"></span><h2>Noch kein SEO-Snapshot</h2><p><?php echo esc_html( $snapshot->get_error_message() ); ?></p></div></section>
			<?php return; ?>
		<?php endif; ?>

		<?php
		$current  = is_array( $snapshot['overview']['current'] ?? null ) ? $snapshot['overview']['current'] : [];
		$previous = is_array( $snapshot['overview']['previous'] ?? null ) ? $snapshot['overview']['previous'] : [];
		$trend    = is_array( $snapshot['trend'] ?? null ) ? $snapshot['trend'] : [];
		?>

		<section class="nsc-v3-kpi-grid" aria-label="SEO Leistungskennzahlen">
			<?php nexus_seo_cockpit_v3_render_metric_card( 'clicks', 'Klicks', $current, $previous, $trend ); ?>
			<?php nexus_seo_cockpit_v3_render_metric_card( 'impressions', 'Impressionen', $current, $previous, $trend ); ?>
			<?php nexus_seo_cockpit_v3_render_metric_card( 'ctr', 'CTR', $current, $previous, $trend ); ?>
			<?php nexus_seo_cockpit_v3_render_metric_card( 'position', 'Ø Position', $current, $previous, $trend ); ?>
			<?php nexus_seo_cockpit_v3_render_action_metric( $command ); ?>
		</section>

		<?php nexus_seo_cockpit_v3_render_action_hub( $command ); ?>

		<section class="nsc-v3-section" aria-labelledby="nsc-v3-momentum-title"><div class="nsc-v3-section__head"><div><p class="nsc-v3-eyebrow">Momentum</p><h2 id="nsc-v3-momentum-title">Wo sich gerade etwas bewegt</h2><p>Striking Distance und Query-Bewegung statt statischer Aufgabenlisten.</p></div><span class="nsc-v3-period">Letzte <?php echo esc_html( (string) $range_days ); ?> Tage</span></div><div class="nsc-v3-split nsc-v3-split--wide-left">
			<article class="nsc-v3-panel"><div class="nsc-v3-panel__head"><div><span class="nsc-v3-panel__icon"><span class="dashicons dashicons-lightbulb" aria-hidden="true"></span></span><div><strong>Top Chancen</strong><p>Queries, die mit überschaubarem Aufwand näher an die Spitze können.</p></div></div></div><?php nexus_seo_cockpit_v3_render_opportunities( (array) $snapshot ); ?></article>
			<article class="nsc-v3-panel"><div class="nsc-v3-panel__head"><div><span class="nsc-v3-panel__icon"><span class="dashicons dashicons-chart-line" aria-hidden="true"></span></span><div><strong>Query-Mover</strong><p>Gewinner und Verlierer gegenüber der Vorperiode.</p></div></div></div><?php nexus_seo_cockpit_v3_render_movers( (array) $snapshot ); ?></article>
		</div></section>

		<section class="nsc-v3-section" aria-labelledby="nsc-v3-trend-title"><div class="nsc-v3-section__head"><div><p class="nsc-v3-eyebrow">Performance Pulse</p><h2 id="nsc-v3-trend-title">Verlauf statt Momentaufnahme</h2><p>Vier Signale in Tagesauflösung. Die Details bleiben bewusst sekundär zur Action Queue.</p></div></div><div class="nsc-v3-panel nsc-v3-panel--trend"><div class="nexus-seo-cockpit__trend-grid"><?php nexus_render_seo_cockpit_trend_card( $trend, 'clicks', 'Klicks' ); nexus_render_seo_cockpit_trend_card( $trend, 'impressions', 'Impressionen' ); nexus_render_seo_cockpit_trend_card( $trend, 'ctr', 'CTR' ); nexus_render_seo_cockpit_trend_card( $trend, 'position', 'Position' ); ?></div></div></section>

		<section class="nsc-v3-section" aria-labelledby="nsc-v3-url-title"><div class="nsc-v3-section__head"><div><p class="nsc-v3-eyebrow">URL Radar</p><h2 id="nsc-v3-url-title">Seiten mit echtem Handlungsbedarf</h2><p>Die wichtigsten URLs als fokussierte Karten – nicht als zehnspaltige Problem-Tabelle.</p></div></div><?php nexus_seo_cockpit_v3_render_problem_cards( (array) $snapshot ); ?></section>

		<section class="nsc-v3-section" aria-labelledby="nsc-v3-system-title"><div class="nsc-v3-section__head"><div><p class="nsc-v3-eyebrow">System Health</p><h2 id="nsc-v3-system-title">Datenquellen & Index-Signale</h2><p>Search Console, Sitemap, Koko und CRM als kompakter Gesundheitscheck.</p></div></div><?php nexus_seo_cockpit_v3_render_system_status( (array) $snapshot, $setup, $runtime, $koko ); ?></section>

		<?php nexus_seo_cockpit_v3_render_raw_tables( (array) $snapshot ); ?>
	</div>
	<?php
}
