<?php
/**
 * SEO Cockpit admin UI rendering.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register a compact WordPress dashboard widget for the SEO cockpit.
 *
 * @return void
 */
function nexus_register_seo_cockpit_dashboard_widget() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		return;
	}

	wp_add_dashboard_widget(
		'nexus_seo_cockpit_dashboard_widget',
		'SEO Cockpit Snapshot',
		'nexus_render_seo_cockpit_dashboard_widget'
	);
}
add_action( 'wp_dashboard_setup', 'nexus_register_seo_cockpit_dashboard_widget' );

/**
 * Render the compact SEO cockpit widget on the default dashboard.
 *
 * @return void
 */
function nexus_render_seo_cockpit_dashboard_widget() {
	$runtime      = nexus_get_seo_cockpit_runtime_summary();
	$config       = nexus_get_seo_cockpit_search_console_config();
	$koko         = nexus_get_koko_analytics_status();
	$tokens       = nexus_get_seo_cockpit_tokens();
	$is_connected = '' !== (string) ( $tokens['access_token'] ?? '' );
	$snapshot     = nexus_get_seo_cockpit_snapshot( false, 28 );
	$insights     = ! is_wp_error( $snapshot ) ? array_slice( (array) ( $snapshot['insights'] ?? [] ), 0, 3 ) : [];
	$koko_data    = ! is_wp_error( $snapshot ) && is_array( $snapshot['koko'] ?? null ) ? $snapshot['koko'] : [];
	$lead_data    = ! is_wp_error( $snapshot ) && is_array( $snapshot['leads'] ?? null ) ? $snapshot['leads'] : [];
	?>
	<div class="nexus-seo-widget">
		<p class="nexus-seo-widget__status">
			<strong>Search Console:</strong>
			<?php echo esc_html( $is_connected ? 'verbunden' : 'nicht verbunden' ); ?>
			<span class="nexus-seo-widget__divider">|</span>
			<strong>Koko:</strong>
			<?php echo esc_html( $koko['active'] ? 'aktiv' : 'noch nicht aktiv' ); ?>
		</p>

		<?php if ( ! empty( $runtime['last_sync_at'] ) ) : ?>
			<p class="nexus-seo-widget__hint">
				Letzte Synchronisierung: <?php echo esc_html( wp_date( 'd.m.Y H:i', (int) $runtime['last_sync_at'] ) ); ?>
			</p>
		<?php endif; ?>

		<?php if ( is_wp_error( $snapshot ) ) : ?>
			<p class="nexus-seo-widget__hint"><?php echo esc_html( $snapshot->get_error_message() ); ?></p>
			<p><a class="button button-secondary" href="<?php echo esc_url( '' === $config['property'] ? nexus_get_seo_cockpit_settings_url() : nexus_get_seo_cockpit_dashboard_url() ); ?>">Zum SEO Cockpit</a></p>
		<?php else : ?>
			<?php $current = $snapshot['overview']['current']; ?>
			<div class="nexus-seo-widget__metrics">
				<div class="nexus-seo-widget__metric">
					<span>Klicks</span>
					<strong><?php echo esc_html( nexus_format_seo_cockpit_metric( 'clicks', $current['clicks'] ) ); ?></strong>
				</div>
				<div class="nexus-seo-widget__metric">
					<span>Impr.</span>
					<strong><?php echo esc_html( nexus_format_seo_cockpit_metric( 'impressions', $current['impressions'] ) ); ?></strong>
				</div>
				<div class="nexus-seo-widget__metric">
					<span>CTR</span>
					<strong><?php echo esc_html( nexus_format_seo_cockpit_metric( 'ctr', $current['ctr'] ) ); ?></strong>
				</div>
				<div class="nexus-seo-widget__metric">
					<span>Pos.</span>
					<strong><?php echo esc_html( nexus_format_seo_cockpit_metric( 'position', $current['position'] ) ); ?></strong>
				</div>
			</div>

			<?php if ( ! empty( $koko_data['available'] ) ) : ?>
				<p class="nexus-seo-widget__hint">
					Koko: <?php echo esc_html( number_format_i18n( (float) ( $koko_data['overview']['current']['visitors'] ?? 0 ) ) ); ?> Besucher /
					<?php echo esc_html( number_format_i18n( (float) ( $koko_data['overview']['current']['pageviews'] ?? 0 ) ) ); ?> Pageviews
				</p>
			<?php elseif ( ! empty( $koko['active'] ) ) : ?>
				<p class="nexus-seo-widget__hint">Koko ist aktiv, liefert im Cockpit aktuell aber noch keinen auswertbaren Datensatz.</p>
			<?php endif; ?>

			<?php if ( ! empty( $lead_data['available'] ) ) : ?>
				<p class="nexus-seo-widget__hint">
					Leads: <?php echo esc_html( number_format_i18n( (int) ( $lead_data['overview']['current']['requests'] ?? 0 ) ) ); ?> gesamt /
					<?php echo esc_html( number_format_i18n( (int) ( $lead_data['overview']['current']['mapped_requests'] ?? 0 ) ) ); ?> intern zugeordnet
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $insights ) ) : ?>
				<ul class="nexus-seo-widget__insights">
					<?php foreach ( $insights as $insight ) : ?>
						<li><?php echo esc_html( (string) $insight['label'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<p><a class="button button-secondary" href="<?php echo esc_url( nexus_get_seo_cockpit_dashboard_url() ); ?>">Zum SEO Cockpit</a></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Format a metric value for output.
 *
 * @param string    $key   Metric key.
 * @param float|int $value Metric value.
 * @return string
 */
function nexus_format_seo_cockpit_metric( $key, $value ) {
	$value = (float) $value;

	if ( 'ctr' === $key ) {
		return number_format_i18n( $value * 100, 1 ) . '%';
	}

	if ( 'position' === $key ) {
		return number_format_i18n( $value, 1 );
	}

	return number_format_i18n( $value );
}

/**
 * Format one metric delta value.
 *
 * @param string    $key      Metric key.
 * @param float|int $current  Current value.
 * @param float|int $previous Previous value.
 * @return array<string, string>
 */
function nexus_get_seo_cockpit_metric_delta( $key, $current, $previous ) {
	$current  = (float) $current;
	$previous = (float) $previous;

	if ( 0.0 === $previous ) {
		return [
			'label' => 0.0 === $current ? '0%' : 'neu',
			'class' => 'neutral',
		];
	}

	if ( 'position' === $key ) {
		$delta = $previous - $current;
	} else {
		$delta = ( ( $current - $previous ) / $previous ) * 100;
	}

	$class = $delta > 0 ? 'positive' : ( $delta < 0 ? 'negative' : 'neutral' );

	return [
		'label' => ( $delta > 0 ? '+' : '' ) . number_format_i18n( $delta, 1 ) . ( 'position' === $key ? ' Punkte' : '%' ),
		'class' => $class,
	];
}

/**
 * Render a standard metric-card grid.
 *
 * @param array<string, mixed>      $current Current metric payload.
 * @param array<string, mixed>      $previous Previous metric payload.
 * @param array<string, string>|null $labels Optional metric labels.
 * @return void
 */
function nexus_render_seo_cockpit_metric_cards( $current, $previous, $labels = null ) {
	$labels = is_array( $labels ) ? $labels : [
		'clicks'      => 'Klicks',
		'impressions' => 'Impressionen',
		'ctr'         => 'CTR',
		'position'    => 'Ø Position',
	];
	?>
	<div class="nexus-seo-cockpit__metrics">
		<?php foreach ( $labels as $key => $label ) : ?>
			<?php
			$current_value  = (float) ( $current[ $key ] ?? 0 );
			$previous_value = (float) ( $previous[ $key ] ?? 0 );
			$delta          = nexus_get_seo_cockpit_metric_delta( $key, $current_value, $previous_value );
			?>
			<article class="nexus-seo-cockpit__metric-card">
				<span class="nexus-seo-cockpit__metric-label"><?php echo esc_html( $label ); ?></span>
				<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( nexus_format_seo_cockpit_metric( $key, $current_value ) ); ?></strong>
				<span class="nexus-seo-cockpit__metric-delta is-<?php echo esc_attr( $delta['class'] ); ?>"><?php echo esc_html( $delta['label'] ); ?></span>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render compact Koko metric cards.
 *
 * @param array<string, mixed> $current Current Koko metrics.
 * @param array<string, mixed> $previous Previous Koko metrics.
 * @return void
 */
function nexus_render_seo_cockpit_koko_metrics( $current, $previous ) {
	$labels = [
		'visitors'  => 'Besucher',
		'pageviews' => 'Pageviews',
	];
	?>
	<div class="nexus-seo-cockpit__koko-metrics">
		<?php foreach ( $labels as $key => $label ) : ?>
			<?php
			$current_value  = (float) ( $current[ $key ] ?? 0 );
			$previous_value = (float) ( $previous[ $key ] ?? 0 );
			$delta          = nexus_get_seo_cockpit_metric_delta( 'clicks', $current_value, $previous_value );
			?>
			<article class="nexus-seo-cockpit__koko-card">
				<span class="nexus-seo-cockpit__metric-label"><?php echo esc_html( $label ); ?></span>
				<strong class="nexus-seo-cockpit__koko-value"><?php echo esc_html( number_format_i18n( $current_value ) ); ?></strong>
				<span class="nexus-seo-cockpit__delta-inline is-<?php echo esc_attr( $delta['class'] ); ?>"><?php echo esc_html( $delta['label'] ); ?></span>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render compact lead metric cards.
 *
 * @param array<string, mixed> $current Current lead metrics.
 * @param array<string, mixed> $previous Previous lead metrics.
 * @return void
 */
function nexus_render_seo_cockpit_lead_metrics( $current, $previous ) {
	$labels = [
		'requests'        => 'Audit-Leads',
		'mapped_requests' => 'Zugeordnet',
		'progressed'      => 'In Arbeit+',
		'won'             => 'Gewonnen',
	];
	?>
	<div class="nexus-seo-cockpit__koko-metrics">
		<?php foreach ( $labels as $key => $label ) : ?>
			<?php
			$current_value  = (float) ( $current[ $key ] ?? 0 );
			$previous_value = (float) ( $previous[ $key ] ?? 0 );
			$delta          = nexus_get_seo_cockpit_metric_delta( 'clicks', $current_value, $previous_value );
			?>
			<article class="nexus-seo-cockpit__koko-card">
				<span class="nexus-seo-cockpit__metric-label"><?php echo esc_html( $label ); ?></span>
				<strong class="nexus-seo-cockpit__koko-value"><?php echo esc_html( number_format_i18n( $current_value ) ); ?></strong>
				<span class="nexus-seo-cockpit__delta-inline is-<?php echo esc_attr( $delta['class'] ); ?>"><?php echo esc_html( $delta['label'] ); ?></span>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Return one compact attribution-mode label string.
 *
 * @param array<string, int> $modes Attribution mode counts.
 * @return string
 */
function nexus_get_seo_cockpit_lead_modes_summary( $modes ) {
	$modes = array_filter(
		(array) $modes,
		static function ( $count ) {
			return absint( $count ) > 0;
		}
	);

	if ( empty( $modes ) ) {
		return '—';
	}

	$parts = [];

	foreach ( $modes as $mode => $count ) {
		$parts[] = sprintf(
			'%s %s',
			number_format_i18n( (int) $count ),
			function_exists( 'nexus_get_seo_cockpit_lead_mode_label' ) ? nexus_get_seo_cockpit_lead_mode_label( (string) $mode ) : (string) $mode
		);
	}

	return implode( ' / ', $parts );
}

/**
 * Render a compact table of attributed lead pages.
 *
 * @param array<int, array<string, mixed>> $pages  Lead page rows.
 * @param int                              $limit  Max rows.
 * @param string                           $window Window key.
 * @return void
 */
function nexus_render_seo_cockpit_lead_pages_table( $pages, $limit = 5, $window = 'current' ) {
	$pages = array_slice( (array) $pages, 0, $limit );

	if ( empty( $pages ) ) {
		echo '<p class="nexus-seo-cockpit__hint">Für dieses Zeitfenster liegen noch keine intern zugeordneten Audit-Leads vor.</p>';
		return;
	}
	?>
	<table class="widefat striped nexus-seo-cockpit__table">
		<thead>
			<tr>
				<th>Seite</th>
				<th>Segment</th>
				<th>Leads</th>
				<th>Won</th>
				<th>Attribution</th>
				<th>Letzter Lead</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $pages as $page ) : ?>
				<?php $window_metrics = is_array( $page[ $window ] ?? null ) ? $page[ $window ] : []; ?>
				<tr>
					<td><a href="<?php echo esc_url( (string) ( $page['detail_url'] ?? nexus_get_seo_cockpit_detail_url( (string) ( $page['url'] ?? '' ) ) ) ); ?>"><code><?php echo esc_html( (string) ( $page['label'] ?? $page['url'] ?? '—' ) ); ?></code></a></td>
					<td><?php echo esc_html( (string) ( $page['page_role_label'] ?? '—' ) ); ?></td>
					<td><?php echo esc_html( number_format_i18n( (int) ( $window_metrics['requests'] ?? 0 ) ) ); ?></td>
					<td><?php echo esc_html( number_format_i18n( (int) ( $page['lifetime']['won'] ?? 0 ) ) ); ?></td>
					<td><?php echo esc_html( nexus_get_seo_cockpit_lead_modes_summary( (array) ( $page['attribution_modes'] ?? [] ) ) ); ?></td>
					<td><?php echo esc_html( ! empty( $page['last_request_at'] ) ? wp_date( 'd.m.Y', (int) $page['last_request_at'] ) : '—' ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

/**
 * Render compact runtime diagnostics.
 *
 * @param array<string, mixed> $diagnostics Diagnostic payload.
 * @param int                  $limit       Max rows.
 * @return void
 */
function nexus_render_seo_cockpit_diagnostics_list( $diagnostics, $limit = 8 ) {
	$checks = array_slice( (array) ( $diagnostics['checks'] ?? [] ), 0, $limit );

	if ( empty( $checks ) ) {
		echo '<p class="nexus-seo-cockpit__hint">Noch keine Laufzeitdiagnostik verfügbar.</p>';
		return;
	}
	?>
	<div class="nexus-seo-cockpit__diagnostics">
		<?php foreach ( $checks as $check ) : ?>
			<article class="nexus-seo-cockpit__diagnostic-card">
				<div class="nexus-seo-cockpit__insight-head">
					<span class="nexus-seo-cockpit__badge is-<?php echo esc_attr( (string) $check['status'] ); ?>">
						<?php echo esc_html( strtoupper( (string) $check['status'] ) ); ?>
					</span>
					<strong><?php echo esc_html( (string) $check['label'] ); ?></strong>
				</div>
				<p class="nexus-seo-cockpit__hint"><strong><?php echo esc_html( ucfirst( (string) $check['area'] ) ); ?>:</strong> <?php echo esc_html( (string) $check['message'] ); ?></p>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render one status notice on cockpit pages.
 *
 * @return void
 */
function nexus_render_seo_cockpit_notice() {
	$notice = isset( $_GET['nexus_seo_notice'] ) ? sanitize_key( (string) wp_unslash( $_GET['nexus_seo_notice'] ) ) : '';

	if ( '' === $notice ) {
		return;
	}

	$messages = [
		'missing_credentials'   => [ 'error', 'Bitte zuerst Client-ID und Client-Secret für Search Console hinterlegen.' ],
		'oauth_connected'       => [ 'success', 'Die Search Console wurde erfolgreich verbunden.' ],
		'oauth_disconnected'    => [ 'success', 'Die Search-Console-Verbindung wurde entfernt.' ],
		'oauth_denied'          => [ 'error', 'Die Google-Freigabe wurde abgebrochen.' ],
		'oauth_state_invalid'   => [ 'error', 'Der OAuth-Status war ungültig oder abgelaufen. Bitte erneut verbinden.' ],
		'oauth_missing_code'    => [ 'error', 'Google hat keinen Authorization Code geliefert.' ],
		'oauth_exchange_failed' => [ 'error', 'Der Google-Code konnte nicht in ein Token getauscht werden.' ],
		'refresh_success'       => [ 'success', 'Das SEO-Cockpit wurde frisch synchronisiert.' ],
		'refresh_failed'        => [ 'error', 'Die Synchronisierung ist fehlgeschlagen. Bitte Verbindung und Property prüfen.' ],
		'refresh_locked'        => [ 'warning', 'Es läuft bereits eine Synchronisierung. Bitte gleich erneut versuchen.' ],
		'inspection_success'    => [ 'success', 'Die URL-Inspektion wurde aktualisiert.' ],
		'inspection_failed'     => [ 'error', 'Die URL-Inspektion konnte nicht geladen werden.' ],
	];

	if ( empty( $messages[ $notice ] ) ) {
		return;
	}

	$message = $messages[ $notice ];
	printf(
		'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
		esc_attr( $message[0] ),
		esc_html( $message[1] )
	);
}

/**
 * Return one severity badge label.
 *
 * @param string $severity Severity key.
 * @return string
 */
function nexus_get_seo_cockpit_severity_label( $severity ) {
	$labels = [
		'critical' => 'Kritisch',
		'high'     => 'Hoch',
		'medium'   => 'Mittel',
		'low'      => 'Niedrig',
	];

	return $labels[ sanitize_key( (string) $severity ) ] ?? 'Hinweis';
}

/**
 * Render one range switcher.
 *
 * @param int    $current_range Current range.
 * @param string $detail_url    Optional detail URL.
 * @return void
 */
function nexus_render_seo_cockpit_range_switcher( $current_range, $detail_url = '' ) {
	?>
	<div class="nexus-seo-cockpit__range-switcher" role="tablist" aria-label="Zeitfenster">
		<?php foreach ( nexus_get_seo_cockpit_allowed_ranges() as $range ) : ?>
			<?php
			$url = '' !== $detail_url
				? nexus_get_seo_cockpit_detail_url( $detail_url, [ 'range' => $range ] )
				: nexus_get_seo_cockpit_dashboard_url( [ 'range' => $range ] );
			?>
			<a class="nexus-seo-cockpit__range-pill <?php echo esc_attr( $current_range === $range ? 'is-active' : '' ); ?>" href="<?php echo esc_url( $url ); ?>">
				<?php echo esc_html( $range ); ?> Tage
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render a simple inline SVG trend chart.
 *
 * @param array<int, array<string, mixed>> $series Series data.
 * @param string                           $metric Metric key.
 * @param string                           $label  Card label.
 * @return void
 */
function nexus_render_seo_cockpit_trend_card( $series, $metric, $label ) {
	$values = array_map(
		static function ( $point ) use ( $metric ) {
			return (float) ( $point[ $metric ] ?? 0 );
		},
		(array) $series
	);

	$max = ! empty( $values ) ? max( $values ) : 0;
	$min = ! empty( $values ) ? min( $values ) : 0;
	$max = ( $max === $min ) ? $max + 1 : $max;
	$width = 280;
	$height = 88;
	$count = max( 1, count( $values ) - 1 );
	$points = [];

	foreach ( $values as $index => $value ) {
		$x = ( $width / $count ) * $index;
		$y = $height - ( ( $value - $min ) / ( $max - $min ) ) * $height;
		$points[] = round( $x, 2 ) . ',' . round( $y, 2 );
	}
	?>
	<article class="nexus-seo-cockpit__trend-card">
		<div class="nexus-seo-cockpit__trend-head">
			<span><?php echo esc_html( $label ); ?></span>
			<strong><?php echo esc_html( nexus_format_seo_cockpit_metric( $metric, end( $values ) ?: 0 ) ); ?></strong>
		</div>
		<svg viewBox="0 0 <?php echo esc_attr( $width ); ?> <?php echo esc_attr( $height ); ?>" role="img" aria-label="<?php echo esc_attr( $label ); ?>">
			<polyline points="<?php echo esc_attr( implode( ' ', $points ) ); ?>" />
		</svg>
	</article>
	<?php
}

/**
 * Render the competing URLs for one cannibalization insight.
 *
 * @param array<string, mixed> $insight Insight row.
 * @return void
 */
function nexus_render_seo_cockpit_competing_urls( $insight ) {
	$metrics  = isset( $insight['metrics'] ) && is_array( $insight['metrics'] ) ? $insight['metrics'] : [];
	$url_rows = isset( $metrics['urls'] ) && is_array( $metrics['urls'] ) ? $metrics['urls'] : [];

	if ( empty( $url_rows ) ) {
		return;
	}

	$url_count = max( count( $url_rows ), (int) ( $metrics['url_count'] ?? 0 ) );
	?>
	<div class="nexus-seo-cockpit__insight-sublist">
		<strong>Konkurrierende URLs</strong>
		<ul class="nexus-seo-cockpit__competing-urls">
			<?php foreach ( $url_rows as $row ) : ?>
				<?php
				$url         = (string) ( $row['url'] ?? '' );
				$impressions = (float) ( $row['impressions'] ?? 0 );
				$position    = (float) ( $row['position'] ?? 0 );
				?>
				<?php if ( '' === $url ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<li>
					<a href="<?php echo esc_url( nexus_get_seo_cockpit_detail_url( $url ) ); ?>"><code><?php echo esc_html( $url ); ?></code></a>
					<span>
						<?php
						echo esc_html(
							sprintf(
								'%s Impressionen · Pos. %s',
								number_format_i18n( $impressions, 0 ),
								number_format_i18n( $position, 1 )
							)
						);
						?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( $url_count > count( $url_rows ) ) : ?>
			<p class="nexus-seo-cockpit__hint">Im Snapshot sind insgesamt <?php echo esc_html( number_format_i18n( $url_count ) ); ?> konkurrierende URLs erkannt; hier werden die stärksten Ziele angezeigt.</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render the insight list.
 *
 * @param array<int, array<string, mixed>> $insights Insight rows.
 * @param int                              $limit    Max items.
 * @return void
 */
function nexus_render_seo_cockpit_insights_list( $insights, $limit = 8 ) {
	$insights = array_slice( (array) $insights, 0, $limit );

	if ( empty( $insights ) ) {
		echo '<p class="nexus-seo-cockpit__hint">Aktuell keine priorisierten Auffälligkeiten für dieses Zeitfenster.</p>';
		return;
	}
	?>
	<div class="nexus-seo-cockpit__insights">
		<?php foreach ( $insights as $insight ) : ?>
			<article class="nexus-seo-cockpit__insight-card">
				<div class="nexus-seo-cockpit__insight-head">
					<?php if ( ! empty( $insight['priority_bucket'] ) ) : ?>
						<span class="nexus-seo-cockpit__badge is-<?php echo esc_attr( (string) $insight['priority_bucket'] ); ?>">
							<?php
							echo esc_html(
								sprintf(
									'%s · %d',
									(string) ( $insight['priority_label'] ?? nexus_get_seo_cockpit_priority_label( (string) $insight['priority_bucket'] ) ),
									(int) ( $insight['priority_score'] ?? 0 )
								)
							);
							?>
						</span>
					<?php endif; ?>
					<span class="nexus-seo-cockpit__badge is-<?php echo esc_attr( (string) $insight['severity'] ); ?>"><?php echo esc_html( nexus_get_seo_cockpit_severity_label( (string) $insight['severity'] ) ); ?></span>
					<strong><?php echo esc_html( (string) $insight['label'] ); ?></strong>
				</div>
				<p><?php echo esc_html( (string) $insight['reason'] ); ?></p>
				<?php if ( ! empty( $insight['recommended_action'] ) ) : ?>
					<p class="nexus-seo-cockpit__hint"><strong>Nächster Schritt:</strong> <?php echo esc_html( (string) $insight['recommended_action'] ); ?></p>
				<?php endif; ?>
				<?php if ( 'POSSIBLE_CANNIBALIZATION' === (string) ( $insight['type'] ?? '' ) ) : ?>
					<?php nexus_render_seo_cockpit_competing_urls( $insight ); ?>
				<?php endif; ?>
				<div class="nexus-seo-cockpit__insight-meta">
					<?php if ( ! empty( $insight['page_role_label'] ) ) : ?>
						<span>Segment: <?php echo esc_html( (string) $insight['page_role_label'] ); ?></span>
					<?php endif; ?>
					<span>Typ: <?php echo esc_html( str_replace( '_', ' ', (string) ( $insight['type'] ?? '' ) ) ); ?></span>
					<?php if ( ! empty( $insight['lead_requests_current'] ) || ! empty( $insight['lead_requests_lifetime'] ) ) : ?>
						<span>
							Leads:
							<?php
							echo esc_html(
								sprintf(
									'%d aktuell / %d gesamt',
									(int) ( $insight['lead_requests_current'] ?? 0 ),
									(int) ( $insight['lead_requests_lifetime'] ?? 0 )
								)
							);
							?>
						</span>
					<?php endif; ?>
					<?php if ( ! empty( $insight['query'] ) ) : ?>
						<span>Query: <?php echo esc_html( (string) $insight['query'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $insight['url'] ) ) : ?>
						<a href="<?php echo esc_url( nexus_get_seo_cockpit_detail_url( (string) $insight['url'] ) ); ?>">URL-Drilldown</a>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render a compact priority queue table.
 *
 * @param array<int, array<string, mixed>> $insights Insight rows.
 * @param int                              $limit    Max items.
 * @return void
 */
function nexus_render_seo_cockpit_priority_queue( $insights, $limit = 10 ) {
	$insights = array_slice( (array) $insights, 0, $limit );

	if ( empty( $insights ) ) {
		echo '<p class="nexus-seo-cockpit__hint">Aktuell keine priorisierte Queue für dieses Zeitfenster.</p>';
		return;
	}
	?>
	<table class="widefat striped nexus-seo-cockpit__table">
		<thead>
			<tr>
				<th>Prio</th>
				<th>Segment</th>
				<th>Hinweis</th>
				<th>URL</th>
				<th>Nächster Schritt</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $insights as $insight ) : ?>
				<tr>
					<td>
						<span class="nexus-seo-cockpit__badge is-<?php echo esc_attr( (string) ( $insight['priority_bucket'] ?? 'low' ) ); ?>">
							<?php
							echo esc_html(
								sprintf(
									'%s · %d',
									(string) ( $insight['priority_label'] ?? 'P4' ),
									(int) ( $insight['priority_score'] ?? 0 )
								)
							);
							?>
						</span>
					</td>
					<td><?php echo esc_html( (string) ( $insight['page_role_label'] ?? '—' ) ); ?></td>
					<td>
						<strong><?php echo esc_html( (string) ( $insight['label'] ?? '' ) ); ?></strong>
						<p class="nexus-seo-cockpit__table-note">
							<?php
							$note = ! empty( $insight['query'] )
								? sprintf( '%s | Query: %s', (string) ( $insight['reason'] ?? '' ), (string) $insight['query'] )
								: (string) ( $insight['reason'] ?? '' );

							if ( ! empty( $insight['lead_requests_current'] ) || ! empty( $insight['lead_requests_lifetime'] ) ) {
								$note .= sprintf(
									' | Leads: %d aktuell / %d gesamt',
									(int) ( $insight['lead_requests_current'] ?? 0 ),
									(int) ( $insight['lead_requests_lifetime'] ?? 0 )
								);
							}

							echo esc_html( $note );
							?>
						</p>
						<?php if ( 'POSSIBLE_CANNIBALIZATION' === (string) ( $insight['type'] ?? '' ) ) : ?>
							<?php nexus_render_seo_cockpit_competing_urls( $insight ); ?>
						<?php endif; ?>
					</td>
					<td>
						<?php if ( ! empty( $insight['url'] ) ) : ?>
							<a href="<?php echo esc_url( nexus_get_seo_cockpit_detail_url( (string) $insight['url'] ) ); ?>"><code><?php echo esc_html( (string) $insight['url'] ); ?></code></a>
						<?php else : ?>
							—
						<?php endif; ?>
					</td>
					<td><?php echo esc_html( (string) ( $insight['recommended_action'] ?? '—' ) ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

/**
 * Render one detail drilldown view.
 *
 * @param array<string, mixed> $detail Detail payload.
 * @return void
 */
function nexus_render_seo_cockpit_detail_view( $detail ) {
	$context         = is_array( $detail['context'] ?? null ) ? $detail['context'] : [];
	$can_manage      = nexus_current_user_can_manage_seo_cockpit();
	$current         = $detail['overview']['current'];
	$previous        = $detail['overview']['previous'];
	$inspection      = is_array( $detail['inspection'] ?? null ) ? $detail['inspection'] : [];
	$koko_detail     = is_array( $detail['koko'] ?? null ) ? $detail['koko'] : [];
	$lead_detail     = is_array( $detail['leads'] ?? null ) ? $detail['leads'] : [];
	$diagnostics     = is_array( $detail['diagnostics'] ?? null ) ? $detail['diagnostics'] : [];
	$range_days      = (int) ( $detail['range_days'] ?? 28 );
	$previous_queries = [];

	foreach ( (array) ( $detail['previous_queries'] ?? [] ) as $row ) {
		$previous_queries[ nexus_get_seo_cockpit_row_key( $row, 0 ) ] = $row;
	}
	?>
	<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--detail">
		<div class="nexus-seo-cockpit__panel-head">
			<div>
				<p class="nexus-seo-cockpit__eyebrow"><a href="<?php echo esc_url( nexus_get_seo_cockpit_dashboard_url( [ 'range' => $range_days ] ) ); ?>">Zur Übersicht</a></p>
				<h2>URL-Drilldown</h2>
				<p class="nexus-seo-cockpit__hint"><code><?php echo esc_html( (string) $detail['url'] ); ?></code></p>
			</div>
			<div class="nexus-seo-cockpit__actions">
				<?php if ( ! empty( $context['frontend_link'] ) ) : ?>
					<a class="button button-secondary" href="<?php echo esc_url( (string) $context['frontend_link'] ); ?>" target="_blank" rel="noreferrer noopener">Frontend</a>
				<?php endif; ?>
				<?php if ( ! empty( $context['edit_link'] ) ) : ?>
					<a class="button button-secondary" href="<?php echo esc_url( (string) $context['edit_link'] ); ?>">Bearbeiten</a>
				<?php endif; ?>
			</div>
		</div>

		<?php nexus_render_seo_cockpit_metric_cards( (array) $current, (array) $previous ); ?>
	</section>

	<div class="nexus-seo-cockpit__grid nexus-seo-cockpit__grid--detail">
		<section class="nexus-seo-cockpit__panel">
			<div class="nexus-seo-cockpit__panel-head">
				<h2>Trend</h2>
				<?php nexus_render_seo_cockpit_range_switcher( $range_days, (string) $detail['url'] ); ?>
			</div>
			<div class="nexus-seo-cockpit__trend-grid">
				<?php
				nexus_render_seo_cockpit_trend_card( (array) $detail['trend'], 'clicks', 'Klicks' );
				nexus_render_seo_cockpit_trend_card( (array) $detail['trend'], 'impressions', 'Impressionen' );
				nexus_render_seo_cockpit_trend_card( (array) $detail['trend'], 'ctr', 'CTR' );
				nexus_render_seo_cockpit_trend_card( (array) $detail['trend'], 'position', 'Position' );
				?>
			</div>
		</section>

		<section class="nexus-seo-cockpit__panel">
			<h2>WordPress-Kontext</h2>
			<?php
			$link_context   = isset( $context['internal_links'] ) && is_array( $context['internal_links'] ) ? $context['internal_links'] : [];
			$context_links  = isset( $link_context['context'] ) && is_array( $link_context['context'] ) ? $link_context['context'] : [];
			$sitewide_links = isset( $link_context['sitewide'] ) && is_array( $link_context['sitewide'] ) ? $link_context['sitewide'] : [];
			$total_links    = isset( $link_context['totals'] ) && is_array( $link_context['totals'] ) ? $link_context['totals'] : [];
			$sitewide_labels = [];

			foreach ( (array) ( $sitewide_links['sources'] ?? [] ) as $source ) {
				if ( empty( $source['label'] ) ) {
					continue;
				}

				$sitewide_labels[] = (string) $source['label'];
			}
			?>
			<ul class="nexus-seo-cockpit__meta-list">
				<li><strong>Post ID:</strong> <?php echo esc_html( (string) ( $context['post_id'] ?? '—' ) ); ?></li>
				<li><strong>Typ:</strong> <?php echo esc_html( (string) ( $context['post_type'] ?? '—' ) ); ?></li>
				<li><strong>Status:</strong> <?php echo esc_html( (string) ( $context['post_status'] ?? '—' ) ); ?></li>
				<li><strong>Seitentyp:</strong> <?php echo esc_html( (string) ( $context['page_type'] ?? '—' ) ); ?></li>
				<li><strong>Template:</strong> <?php echo esc_html( (string) ( $context['template'] ?? '—' ) ); ?></li>
				<li><strong>Zuletzt geändert:</strong> <?php echo esc_html( ! empty( $context['modified_at'] ) ? wp_date( 'd.m.Y H:i', (int) $context['modified_at'] ) : '—' ); ?></li>
				<li><strong>SEO-Titel vorhanden:</strong> <?php echo esc_html( ! empty( $context['seo_title_present'] ) ? 'Ja' : 'Nein' ); ?></li>
				<li><strong>SEO-Description vorhanden:</strong> <?php echo esc_html( ! empty( $context['seo_description_present'] ) ? 'Ja' : 'Nein' ); ?></li>
				<li><strong>Canonical vorhanden:</strong> <?php echo esc_html( ! empty( $context['canonical_present'] ) ? 'Ja' : 'Nein' ); ?></li>
				<li><strong>noindex:</strong> <?php echo esc_html( ! empty( $context['noindex'] ) ? 'Ja' : 'Nein' ); ?></li>
				<li><strong>In Sitemap:</strong> <?php echo esc_html( ! empty( $context['in_sitemap'] ) ? 'Ja' : 'Nein' ); ?></li>
				<li><strong>Wortanzahl:</strong> <?php echo esc_html( (string) ( $context['word_count'] ?? 0 ) ); ?></li>
				<li><strong>Kontextlinks eingehend:</strong> <?php echo esc_html( number_format_i18n( (float) ( $context_links['incoming_links'] ?? 0 ) ) ); ?></li>
				<li><strong>Verlinkende Inhaltsdokumente:</strong> <?php echo esc_html( number_format_i18n( (float) ( $context_links['incoming_documents'] ?? 0 ) ) ); ?></li>
				<li><strong>Sitewide-Links eingehend:</strong> <?php echo esc_html( number_format_i18n( (float) ( $sitewide_links['incoming_links'] ?? 0 ) ) ); ?></li>
				<li><strong>Verlinkende Sitewide-Quellen:</strong> <?php echo esc_html( number_format_i18n( (float) ( $sitewide_links['incoming_sources'] ?? 0 ) ) ); ?></li>
				<li><strong>Gesamt eingehend:</strong> <?php echo esc_html( number_format_i18n( (float) ( $total_links['incoming_links'] ?? 0 ) ) ); ?></li>
				<li><strong>Gesamtquellen eingehend:</strong> <?php echo esc_html( number_format_i18n( (float) ( $total_links['incoming_sources'] ?? 0 ) ) ); ?></li>
				<li><strong>Kontextlinks ausgehend:</strong> <?php echo esc_html( number_format_i18n( (float) ( $context_links['outgoing_links'] ?? 0 ) ) ); ?></li>
				<li><strong>Kontextziele:</strong> <?php echo esc_html( number_format_i18n( (float) ( $context_links['outgoing_unique_urls'] ?? 0 ) ) ); ?></li>
				<li><strong>Globale Shell:</strong> <?php echo esc_html( (string) ( $sitewide_links['shell_label'] ?? '—' ) ); ?></li>
				<li><strong>Globale Linkziele:</strong> <?php echo esc_html( number_format_i18n( (float) ( $sitewide_links['outgoing_unique_urls'] ?? 0 ) ) ); ?></li>
				<li><strong>Globale Linkplatzierungen:</strong> <?php echo esc_html( number_format_i18n( (float) ( $sitewide_links['outgoing_links'] ?? 0 ) ) ); ?></li>
				<li><strong>Sitewide-Bereiche:</strong> <?php echo esc_html( ! empty( $sitewide_labels ) ? implode( ', ', $sitewide_labels ) : '—' ); ?></li>
				<li><strong>Linkgraph-Notiz:</strong> <?php echo esc_html( (string) ( $link_context['note'] ?? 'Noch nicht gemessen' ) ); ?></li>
			</ul>
		</section>
	</div>

	<div class="nexus-seo-cockpit__grid nexus-seo-cockpit__grid--detail">
		<section class="nexus-seo-cockpit__panel">
			<h2>Insights für diese URL</h2>
			<?php nexus_render_seo_cockpit_insights_list( (array) ( $detail['insights'] ?? [] ), 6 ); ?>
		</section>

		<section class="nexus-seo-cockpit__panel">
			<h2>Koko-Kontext</h2>
			<p class="nexus-seo-cockpit__status <?php echo esc_attr( ! empty( $koko_detail['available'] ) ? 'is-positive' : 'is-neutral' ); ?>">
				<?php echo esc_html( (string) ( $koko_detail['status']['label'] ?? 'Koko nicht verfügbar' ) ); ?>
			</p>
			<?php if ( ! empty( $koko_detail['matched'] ) ) : ?>
				<?php nexus_render_seo_cockpit_koko_metrics( (array) ( $koko_detail['current'] ?? [] ), (array) ( $koko_detail['previous'] ?? [] ) ); ?>
			<?php else : ?>
				<p class="nexus-seo-cockpit__hint"><?php echo esc_html( (string) ( $koko_detail['note'] ?? 'Für diese URL liegt kein eindeutiger Koko-Kontext vor.' ) ); ?></p>
			<?php endif; ?>
		</section>

		<section class="nexus-seo-cockpit__panel">
			<h2>Lead-Kontext</h2>
			<?php if ( ! empty( $lead_detail['available'] ) ) : ?>
				<?php nexus_render_seo_cockpit_lead_metrics( (array) ( $lead_detail['current'] ?? [] ), (array) ( $lead_detail['previous'] ?? [] ) ); ?>
				<p class="nexus-seo-cockpit__hint">
					<?php
					echo esc_html(
						sprintf(
							'Lifetime: %d Leads, %d gewonnen. Attribution: %s.',
							(int) ( $lead_detail['lifetime']['requests'] ?? 0 ),
							(int) ( $lead_detail['lifetime']['won'] ?? 0 ),
							nexus_get_seo_cockpit_lead_modes_summary( (array) ( $lead_detail['attribution_modes'] ?? [] ) )
						)
					);
					?>
				</p>
				<?php if ( ! empty( $lead_detail['sources'] ) ) : ?>
					<div class="nexus-seo-cockpit__chips">
						<?php foreach ( nexus_get_seo_cockpit_lead_ranked_counts( (array) $lead_detail['sources'], 'nexus_get_seo_cockpit_lead_source_label', 4 ) as $source_row ) : ?>
							<span class="nexus-seo-cockpit__chip"><?php echo esc_html( sprintf( '%s: %d', (string) $source_row['label'], (int) $source_row['count'] ) ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<p class="nexus-seo-cockpit__hint"><?php echo esc_html( (string) ( $lead_detail['note'] ?? '' ) ); ?></p>
			<?php else : ?>
				<p class="nexus-seo-cockpit__hint"><?php echo esc_html( (string) ( $lead_detail['note'] ?? 'Lead-Daten sind derzeit nicht verfügbar.' ) ); ?></p>
			<?php endif; ?>
		</section>

		<section class="nexus-seo-cockpit__panel">
			<div class="nexus-seo-cockpit__panel-head">
				<h2>Indexierungsstatus</h2>
				<?php if ( $can_manage ) : ?>
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_inspect' ) ); ?>">
						<?php wp_nonce_field( 'nexus_seo_cockpit_inspect' ); ?>
						<input type="hidden" name="inspection_url" value="<?php echo esc_attr( (string) $detail['url'] ); ?>">
						<input type="hidden" name="range" value="<?php echo esc_attr( (string) $range_days ); ?>">
						<button type="submit" class="button button-secondary">Jetzt prüfen</button>
					</form>
				<?php endif; ?>
			</div>

			<?php if ( empty( $inspection ) ) : ?>
				<p class="nexus-seo-cockpit__hint">Noch keine URL-Inspektion im Cache. Für Quota-Schonung wird sie nur manuell im Drilldown ausgeführt.</p>
			<?php else : ?>
				<ul class="nexus-seo-cockpit__meta-list">
					<li><strong>Letzte Prüfung:</strong> <?php echo esc_html( ! empty( $inspection['checked_at'] ) ? wp_date( 'd.m.Y H:i', (int) $inspection['checked_at'] ) : '—' ); ?></li>
					<li><strong>Verdict:</strong> <?php echo esc_html( (string) ( $inspection['verdict'] ?? '—' ) ); ?></li>
					<li><strong>Coverage:</strong> <?php echo esc_html( (string) ( $inspection['coverage_state'] ?? '—' ) ); ?></li>
					<li><strong>Indexing:</strong> <?php echo esc_html( (string) ( $inspection['indexing_state'] ?? '—' ) ); ?></li>
					<li><strong>Page Fetch:</strong> <?php echo esc_html( (string) ( $inspection['page_fetch_state'] ?? '—' ) ); ?></li>
					<li><strong>Robots:</strong> <?php echo esc_html( (string) ( $inspection['robots_txt_state'] ?? '—' ) ); ?></li>
					<li><strong>Letzter Crawl:</strong> <?php echo esc_html( (string) ( $inspection['last_crawl_time'] ?? '—' ) ); ?></li>
					<li><strong>User Canonical:</strong> <code><?php echo esc_html( (string) ( $inspection['user_canonical'] ?? '—' ) ); ?></code></li>
					<li><strong>Google Canonical:</strong> <code><?php echo esc_html( (string) ( $inspection['google_canonical'] ?? '—' ) ); ?></code></li>
				</ul>
			<?php endif; ?>
		</section>
	</div>

	<div class="nexus-seo-cockpit__grid nexus-seo-cockpit__grid--detail">
		<section class="nexus-seo-cockpit__panel">
			<h2>Drilldown-Diagnostik</h2>
			<?php nexus_render_seo_cockpit_diagnostics_list( $diagnostics, 5 ); ?>
		</section>

		<section class="nexus-seo-cockpit__panel">
			<h2>Top Queries dieser URL</h2>
			<table class="widefat striped nexus-seo-cockpit__table">
				<thead>
					<tr>
						<th>Query</th>
						<th>Klicks</th>
						<th>Impressionen</th>
						<th>CTR</th>
						<th>Position</th>
						<th>Delta Klicks</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( (array) ( $detail['top_queries'] ?? [] ) as $row ) : ?>
						<?php
						$query    = nexus_get_seo_cockpit_row_key( $row, 0 );
						$previous_row = $previous_queries[ $query ] ?? [];
						$delta    = nexus_get_seo_cockpit_metric_delta( 'clicks', (float) ( $row['clicks'] ?? 0 ), (float) ( $previous_row['clicks'] ?? 0 ) );
						?>
						<tr>
							<td><?php echo esc_html( $query ); ?></td>
							<td><?php echo esc_html( number_format_i18n( (float) ( $row['clicks'] ?? 0 ) ) ); ?></td>
							<td><?php echo esc_html( number_format_i18n( (float) ( $row['impressions'] ?? 0 ) ) ); ?></td>
							<td><?php echo esc_html( number_format_i18n( (float) ( ( $row['ctr'] ?? 0 ) * 100 ), 1 ) . '%' ); ?></td>
							<td><?php echo esc_html( number_format_i18n( (float) ( $row['position'] ?? 0 ), 1 ) ); ?></td>
							<td><span class="nexus-seo-cockpit__delta-inline is-<?php echo esc_attr( $delta['class'] ); ?>"><?php echo esc_html( $delta['label'] ); ?></span></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</section>

		<section class="nexus-seo-cockpit__panel">
			<h2>Geräte</h2>
			<div class="nexus-seo-cockpit__chips">
				<?php foreach ( (array) ( $detail['devices'] ?? [] ) as $row ) : ?>
					<span class="nexus-seo-cockpit__chip">
						<?php
						echo esc_html(
							sprintf(
								'%s: %s Klicks',
								strtoupper( nexus_get_seo_cockpit_row_key( $row, 0 ) ),
								number_format_i18n( (float) ( $row['clicks'] ?? 0 ) )
							)
						);
						?>
					</span>
				<?php endforeach; ?>
			</div>
		</section>
	</div>
	<?php
}

/**
 * Render the SEO cockpit dashboard page.
 *
 * @return void
 */
function nexus_render_seo_cockpit_dashboard() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		return;
	}

	$setup         = nexus_get_seo_cockpit_setup_state();
	$config        = $setup['config'];
	$tokens        = nexus_get_seo_cockpit_tokens();
	$runtime       = nexus_get_seo_cockpit_runtime_summary();
	$koko          = nexus_get_koko_analytics_status();
	$range_days    = nexus_get_seo_cockpit_requested_range_days();
	$detail_url    = nexus_get_seo_cockpit_selected_detail_url();
	$is_connected  = $setup['is_connected'];
	$can_manage    = nexus_current_user_can_manage_seo_cockpit();
	$snapshot      = nexus_get_seo_cockpit_snapshot( false, $range_days );
	$site_list     = $is_connected ? nexus_get_seo_cockpit_sites() : new WP_Error( 'nexus_seo_not_connected', 'Die Search Console ist noch nicht verbunden.' );
	$detail        = '' !== $detail_url ? nexus_get_seo_cockpit_url_detail( $detail_url, false, $range_days ) : null;
	$diagnostics   = function_exists( 'nexus_get_seo_cockpit_diagnostics' ) ? nexus_get_seo_cockpit_diagnostics( $detail_url ) : [];
	?>
	<div class="wrap nexus-seo-cockpit">
		<h1>SEO Cockpit</h1>
		<?php nexus_render_seo_cockpit_notice(); ?>

		<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__header">
			<div class="nexus-seo-cockpit__panel-head">
				<div>
					<h2>SEO Operating Layer</h2>
					<p class="nexus-seo-cockpit__hint">Property: <code><?php echo esc_html( $config['property'] ?: 'Noch nicht gesetzt' ); ?></code></p>
				</div>
				<div class="nexus-seo-cockpit__actions">
					<?php nexus_render_seo_cockpit_range_switcher( $range_days, $detail_url ); ?>
					<?php if ( $can_manage && $is_connected ) : ?>
						<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_refresh' ) ); ?>">
							<?php wp_nonce_field( 'nexus_seo_cockpit_refresh' ); ?>
							<input type="hidden" name="range" value="<?php echo esc_attr( (string) $range_days ); ?>">
							<input type="hidden" name="detail_url" value="<?php echo esc_attr( $detail_url ); ?>">
							<button type="submit" class="button button-primary">Jetzt synchronisieren</button>
						</form>
						<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_disconnect' ) ); ?>">
							<?php wp_nonce_field( 'nexus_seo_cockpit_disconnect' ); ?>
							<button type="submit" class="button button-secondary">Verbindung trennen</button>
						</form>
					<?php elseif ( $can_manage ) : ?>
						<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_connect' ) ); ?>">
							<?php wp_nonce_field( 'nexus_seo_cockpit_connect' ); ?>
							<button type="submit" class="button button-primary" <?php disabled( ! nexus_has_seo_cockpit_search_console_credentials() ); ?>>Mit Google verbinden</button>
						</form>
					<?php endif; ?>
				</div>
			</div>

			<div class="nexus-seo-cockpit__header-grid">
				<div><strong>Status</strong><span><?php echo esc_html( $is_connected ? 'Verbunden' : 'Noch nicht verbunden' ); ?></span></div>
				<div><strong>Letzter Sync</strong><span><?php echo esc_html( ! empty( $runtime['last_sync_at'] ) ? wp_date( 'd.m.Y H:i', (int) $runtime['last_sync_at'] ) : 'n/a' ); ?></span></div>
				<div><strong>Nächster Sync</strong><span><?php echo esc_html( ! empty( $runtime['next_sync_at'] ) ? wp_date( 'd.m.Y H:i', (int) $runtime['next_sync_at'] ) : 'n/a' ); ?></span></div>
				<div><strong>Cache bis</strong><span><?php echo esc_html( ! empty( $runtime['cache_expires_at'] ) ? wp_date( 'd.m.Y H:i', (int) $runtime['cache_expires_at'] ) : 'n/a' ); ?></span></div>
			</div>
		</section>

		<?php if ( ! $setup['is_ready'] ) : ?>
			<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--setup">
				<div class="nexus-seo-cockpit__panel-head">
					<h2>Noch nicht komplett eingerichtet</h2>
					<?php if ( $can_manage ) : ?>
						<a class="button button-primary" href="<?php echo esc_url( nexus_get_seo_cockpit_settings_url() ); ?>">Zu den Einstellungen</a>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $setup['missing'] ) ) : ?>
					<p class="nexus-seo-cockpit__hint">Es fehlen aktuell: <strong><?php echo esc_html( implode( ', ', $setup['missing'] ) ); ?></strong>.</p>
				<?php elseif ( ! $is_connected ) : ?>
					<p class="nexus-seo-cockpit__hint">Die Werte sind hinterlegt. Als nächsten Schritt musst du nur noch Google verbinden.</p>
				<?php endif; ?>
				<ol class="nexus-seo-cockpit__steps">
					<li>Property, Client ID und Client Secret sauber hinterlegen.</li>
					<li>Google verbinden und die Freigabe mit dem Search-Console-Konto bestätigen.</li>
					<li>Danach priorisierte Chancen, Problemseiten und Drilldowns im Cockpit nutzen.</li>
				</ol>
			</section>
		<?php endif; ?>

		<?php if ( $detail && ! is_wp_error( $detail ) ) : ?>
			<?php nexus_render_seo_cockpit_detail_view( $detail ); ?>
		<?php elseif ( is_wp_error( $detail ) ) : ?>
			<section class="nexus-seo-cockpit__panel">
				<h2>URL-Drilldown</h2>
				<p><?php echo esc_html( $detail->get_error_message() ); ?></p>
			</section>
		<?php endif; ?>

		<div class="nexus-seo-cockpit__grid nexus-seo-cockpit__grid--top">
			<section class="nexus-seo-cockpit__panel">
				<div class="nexus-seo-cockpit__panel-head">
					<h2>Verbindung & Technik</h2>
					<?php if ( $can_manage ) : ?>
						<a class="button button-secondary" href="<?php echo esc_url( nexus_get_seo_cockpit_settings_url() ); ?>">Einstellungen</a>
					<?php endif; ?>
				</div>
				<ul class="nexus-seo-cockpit__meta-list">
					<li><strong>Property:</strong> <?php echo esc_html( $config['property'] ?: 'Noch nicht gesetzt' ); ?></li>
					<li><strong>Redirect URI:</strong> <code><?php echo esc_html( $config['redirect_uri'] ); ?></code></li>
					<li><strong>Token aktualisiert:</strong> <?php echo esc_html( ! empty( $tokens['updated_at'] ) ? wp_date( 'd.m.Y H:i', (int) $tokens['updated_at'] ) : 'n/a' ); ?></li>
					<li><strong>Sync-Quelle:</strong> <?php echo esc_html( (string) ( $runtime['last_sync_source'] ?? 'n/a' ) ); ?></li>
					<li><strong>Letzter Status:</strong> <?php echo esc_html( (string) ( $runtime['last_sync_status'] ?? 'n/a' ) ); ?></li>
				</ul>
				<?php if ( is_wp_error( $site_list ) ) : ?>
					<p class="nexus-seo-cockpit__hint"><?php echo esc_html( $site_list->get_error_message() ); ?></p>
				<?php elseif ( ! empty( $site_list ) ) : ?>
					<div class="nexus-seo-cockpit__chips">
						<?php foreach ( array_slice( $site_list, 0, 8 ) as $site_entry ) : ?>
							<span class="nexus-seo-cockpit__chip"><?php echo esc_html( (string) ( $site_entry['siteUrl'] ?? '' ) ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</section>

			<section class="nexus-seo-cockpit__panel">
				<h2>Koko Analytics</h2>
				<p class="nexus-seo-cockpit__status <?php echo esc_attr( $koko['active'] ? 'is-positive' : 'is-neutral' ); ?>">
					<?php echo esc_html( $koko['label'] ); ?>
				</p>
				<?php if ( ! empty( $koko['note'] ) ) : ?>
					<p class="nexus-seo-cockpit__hint"><?php echo esc_html( (string) $koko['note'] ); ?></p>
				<?php endif; ?>
				<div class="nexus-seo-cockpit__chips">
					<span class="nexus-seo-cockpit__chip">Totals: <?php echo esc_html( ! empty( $koko['endpoints']['totals'] ) ? 'ja' : 'nein' ); ?></span>
					<span class="nexus-seo-cockpit__chip">Stats: <?php echo esc_html( ! empty( $koko['endpoints']['stats'] ) ? 'ja' : 'nein' ); ?></span>
					<span class="nexus-seo-cockpit__chip">Posts: <?php echo esc_html( ! empty( $koko['endpoints']['posts'] ) ? 'ja' : 'nein' ); ?></span>
				</div>
			</section>
		</div>

		<?php if ( is_wp_error( $snapshot ) ) : ?>
			<section class="nexus-seo-cockpit__panel">
				<h2>Noch kein SEO-Snapshot</h2>
				<p><?php echo esc_html( $snapshot->get_error_message() ); ?></p>
				<p>Lege zuerst die Search-Console-Property in den Einstellungen fest und verbinde danach Google.</p>
			</section>
		<?php else : ?>
			<?php
			$current       = $snapshot['overview']['current'];
			$previous      = $snapshot['overview']['previous'];
			$koko_snapshot = is_array( $snapshot['koko'] ?? null ) ? $snapshot['koko'] : [];
			$lead_snapshot = is_array( $snapshot['leads'] ?? null ) ? $snapshot['leads'] : [];
			?>
			<section class="nexus-seo-cockpit__panel">
				<div class="nexus-seo-cockpit__panel-head">
					<div>
						<h2>SEO-Lage</h2>
						<p class="nexus-seo-cockpit__hint">
							<?php
							echo esc_html(
								sprintf(
									'Vergleich %s bis %s gegen %s bis %s. Stand: %s',
									(string) $snapshot['ranges']['current_start'],
									(string) $snapshot['ranges']['current_end'],
									(string) $snapshot['ranges']['previous_start'],
									(string) $snapshot['ranges']['previous_end'],
									wp_date( 'd.m.Y H:i', (int) $snapshot['generated_at'] )
								)
							);
							?>
						</p>
					</div>
				</div>

				<?php nexus_render_seo_cockpit_metric_cards( (array) $current, (array) $previous ); ?>
			</section>

			<section class="nexus-seo-cockpit__panel">
				<div class="nexus-seo-cockpit__panel-head">
					<h2>Trend</h2>
					<p class="nexus-seo-cockpit__hint"><?php echo esc_html( $range_days ); ?> Tage mit Tagesauflösung</p>
				</div>
				<div class="nexus-seo-cockpit__trend-grid">
					<?php
					nexus_render_seo_cockpit_trend_card( (array) $snapshot['trend'], 'clicks', 'Klicks' );
					nexus_render_seo_cockpit_trend_card( (array) $snapshot['trend'], 'impressions', 'Impressionen' );
					nexus_render_seo_cockpit_trend_card( (array) $snapshot['trend'], 'ctr', 'CTR' );
					nexus_render_seo_cockpit_trend_card( (array) $snapshot['trend'], 'position', 'Position' );
					?>
				</div>
			</section>

			<section class="nexus-seo-cockpit__panel">
				<div class="nexus-seo-cockpit__panel-head">
					<h2>Lead-Kontext</h2>
					<p class="nexus-seo-cockpit__hint">Audit-Leads aus dem internen WordPress-CRM. Die interne Seitenzuordnung nutzt Einstieg, letzte interne Seite und Formular-Landing.</p>
				</div>
				<?php if ( ! empty( $lead_snapshot['available'] ) ) : ?>
					<?php nexus_render_seo_cockpit_lead_metrics( (array) ( $lead_snapshot['overview']['current'] ?? [] ), (array) ( $lead_snapshot['overview']['previous'] ?? [] ) ); ?>
					<p class="nexus-seo-cockpit__hint">
						<?php
						echo esc_html(
							sprintf(
								'Zugeordnete Seiten: %d aktuell / %d gesamt. Nicht zugeordnet: %d im aktuellen Zeitfenster.',
								(int) ( $lead_snapshot['overview']['current']['mapped_pages'] ?? 0 ),
								(int) ( $lead_snapshot['overview']['lifetime']['mapped_pages'] ?? 0 ),
								(int) ( $lead_snapshot['overview']['current']['unmapped_requests'] ?? 0 )
							)
						);
						?>
					</p>
					<?php if ( ! empty( $lead_snapshot['source_rows']['current'] ) ) : ?>
						<div class="nexus-seo-cockpit__chips">
							<?php foreach ( (array) $lead_snapshot['source_rows']['current'] as $row ) : ?>
								<span class="nexus-seo-cockpit__chip"><?php echo esc_html( sprintf( '%s: %d', (string) ( $row['label'] ?? '' ), (int) ( $row['count'] ?? 0 ) ) ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php nexus_render_seo_cockpit_lead_pages_table( (array) ( $lead_snapshot['top_pages'] ?? [] ), 6, 'current' ); ?>
				<?php else : ?>
					<p class="nexus-seo-cockpit__hint"><?php echo esc_html( (string) ( $lead_snapshot['note'] ?? 'Lead-Daten sind derzeit nicht verfügbar.' ) ); ?></p>
				<?php endif; ?>
			</section>

			<div class="nexus-seo-cockpit__grid nexus-seo-cockpit__grid--reports">
				<section class="nexus-seo-cockpit__panel">
					<h2>Prioritäts-Queue</h2>
					<p class="nexus-seo-cockpit__hint">Sortiert nach SEO-Chance, Business-Wert, Funnel-Nähe und Datensicherheit.</p>
					<?php nexus_render_seo_cockpit_priority_queue( (array) ( $snapshot['insights'] ?? [] ), 10 ); ?>
				</section>

				<section class="nexus-seo-cockpit__panel">
					<h2>Runtime-Diagnostik</h2>
					<?php nexus_render_seo_cockpit_diagnostics_list( $diagnostics, 6 ); ?>
				</section>
			</div>

			<section class="nexus-seo-cockpit__panel">
				<h2>Top Hinweise</h2>
				<?php nexus_render_seo_cockpit_insights_list( (array) ( $snapshot['insights'] ?? [] ), 6 ); ?>
			</section>

			<div class="nexus-seo-cockpit__grid nexus-seo-cockpit__grid--reports">
				<section class="nexus-seo-cockpit__panel">
					<h2>Koko-Kontext</h2>
					<p class="nexus-seo-cockpit__hint">Onsite-Nutzung für denselben Zeitraum. Das ist ein Kontextlayer für Nachfrage, nicht der Ersatz für Search Console.</p>
					<?php if ( ! empty( $koko_snapshot['available'] ) ) : ?>
						<?php nexus_render_seo_cockpit_koko_metrics( (array) ( $koko_snapshot['overview']['current'] ?? [] ), (array) ( $koko_snapshot['overview']['previous'] ?? [] ) ); ?>
						<?php if ( ! empty( $koko_snapshot['top_pages'] ) ) : ?>
							<table class="widefat striped nexus-seo-cockpit__table">
								<thead>
									<tr>
										<th>Seite</th>
										<th>Besucher</th>
										<th>Pageviews</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( (array) $koko_snapshot['top_pages'] as $row ) : ?>
										<tr>
											<td>
												<?php if ( ! empty( $row['url'] ) ) : ?>
													<a href="<?php echo esc_url( nexus_get_seo_cockpit_detail_url( (string) $row['url'] ) ); ?>"><code><?php echo esc_html( (string) ( $row['title'] ?: $row['url'] ) ); ?></code></a>
												<?php else : ?>
													<?php echo esc_html( (string) ( $row['title'] ?? '—' ) ); ?>
												<?php endif; ?>
											</td>
											<td><?php echo esc_html( number_format_i18n( (float) ( $row['visitors'] ?? 0 ) ) ); ?></td>
											<td><?php echo esc_html( number_format_i18n( (float) ( $row['pageviews'] ?? 0 ) ) ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					<?php else : ?>
						<p class="nexus-seo-cockpit__hint">
							<?php echo esc_html( ! empty( $koko_snapshot['status']['label'] ) ? (string) $koko_snapshot['status']['label'] : 'Koko liefert derzeit keinen auswertbaren Kontext.' ); ?>
						</p>
					<?php endif; ?>
				</section>

				<section class="nexus-seo-cockpit__panel">
					<h2>Sitemaps & Technik</h2>
					<?php if ( ! empty( $snapshot['sitemaps'] ) ) : ?>
						<table class="widefat striped nexus-seo-cockpit__table">
							<thead>
								<tr>
									<th>Sitemap</th>
									<th>Status</th>
									<th>Typ</th>
									<th>Zuletzt geladen</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( (array) $snapshot['sitemaps'] as $sitemap ) : ?>
									<tr>
										<td><code><?php echo esc_html( (string) ( $sitemap['path'] ?? $sitemap['contents'] ?? '' ) ); ?></code></td>
										<td><?php echo esc_html( (string) ( $sitemap['isPending'] ?? false ? 'Pending' : 'Aktiv' ) ); ?></td>
										<td><?php echo esc_html( (string) ( $sitemap['type'] ?? '—' ) ); ?></td>
										<td><?php echo esc_html( (string) ( $sitemap['lastDownloaded'] ?? '—' ) ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p class="nexus-seo-cockpit__hint">Noch keine Sitemap-Daten in Search Console verfügbar oder abrufbar.</p>
					<?php endif; ?>
					<p class="nexus-seo-cockpit__hint">URL-Inspection wird im Drilldown bewusst nur manuell ausgeführt, damit Quotas nicht verbrannt werden.</p>
				</section>
			</div>

			<section class="nexus-seo-cockpit__panel">
				<h2>Problemseiten</h2>
				<?php if ( empty( $snapshot['problem_pages'] ) ) : ?>
					<p class="nexus-seo-cockpit__hint">Für dieses Zeitfenster wurden keine priorisierten Problemseiten erkannt.</p>
				<?php else : ?>
					<table class="widefat striped nexus-seo-cockpit__table">
						<thead>
							<tr>
								<th>Prio</th>
								<th>Segment</th>
								<th>URL</th>
								<th>Typ</th>
								<th>Impressionen</th>
								<th>Position</th>
								<th>SEO</th>
								<th>Koko</th>
								<th>Leads</th>
								<th>Primärer Hinweis</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( (array) $snapshot['problem_pages'] as $page ) : ?>
								<?php
								$context = is_array( $page['context'] ?? null ) ? $page['context'] : [];
								$primary = is_array( $page['primary'] ?? null ) ? $page['primary'] : [];
								$lead_page = is_array( $lead_snapshot['page_map'][ (string) $page['url'] ] ?? null ) ? $lead_snapshot['page_map'][ (string) $page['url'] ] : [];
								?>
								<tr>
									<td>
										<span class="nexus-seo-cockpit__badge is-<?php echo esc_attr( (string) ( $primary['priority_bucket'] ?? 'low' ) ); ?>">
											<?php
											echo esc_html(
												sprintf(
													'%s · %d',
													(string) ( $primary['priority_label'] ?? 'P4' ),
													(int) ( $primary['priority_score'] ?? 0 )
												)
											);
											?>
										</span>
									</td>
									<td><?php echo esc_html( (string) ( $primary['page_role_label'] ?? '—' ) ); ?></td>
									<td><a href="<?php echo esc_url( (string) $page['detail_url'] ); ?>"><code><?php echo esc_html( (string) $page['url'] ); ?></code></a></td>
									<td><?php echo esc_html( (string) ( $context['post_type'] ?? '—' ) ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( $page['row']['impressions'] ?? 0 ) ) ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( $page['row']['position'] ?? 0 ), 1 ) ); ?></td>
									<td>
										<?php
										echo esc_html(
											sprintf(
												'Title: %s / Desc: %s / noindex: %s',
												! empty( $context['seo_title_present'] ) ? 'Ja' : 'Nein',
												! empty( $context['seo_description_present'] ) ? 'Ja' : 'Nein',
												! empty( $context['noindex'] ) ? 'Ja' : 'Nein'
											)
										);
										?>
									</td>
									<td>
										<?php
										$koko_page = is_array( $koko_snapshot['page_map'][ (string) $page['url'] ] ?? null ) ? $koko_snapshot['page_map'][ (string) $page['url'] ] : [];
										echo esc_html(
											! empty( $koko_page )
												? sprintf(
													'%s / %s',
													number_format_i18n( (float) ( $koko_page['visitors'] ?? 0 ) ),
													number_format_i18n( (float) ( $koko_page['pageviews'] ?? 0 ) )
												)
												: '—'
										);
										?>
									</td>
									<td>
										<?php
										echo esc_html(
											! empty( $lead_page )
												? sprintf(
													'%d / %d',
													(int) ( $lead_page['current']['requests'] ?? 0 ),
													(int) ( $lead_page['lifetime']['requests'] ?? 0 )
												)
												: '—'
										);
										?>
									</td>
									<td><?php echo esc_html( (string) ( $primary['label'] ?? '' ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</section>

			<div class="nexus-seo-cockpit__grid nexus-seo-cockpit__grid--reports">
				<section class="nexus-seo-cockpit__panel">
					<h2>Top Pages</h2>
					<table class="widefat striped nexus-seo-cockpit__table">
						<thead>
							<tr>
								<th>URL</th>
								<th>Klicks</th>
								<th>Impressionen</th>
								<th>CTR</th>
								<th>Position</th>
								<th>Koko</th>
								<th>Leads</th>
								<th>WP-Kontext</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( (array) $snapshot['top_pages'] as $row ) : ?>
								<?php
								$url     = nexus_normalize_seo_cockpit_url( nexus_get_seo_cockpit_row_label( $row ) );
								$context = $snapshot['page_contexts'][ $url ] ?? nexus_get_seo_cockpit_wp_context_for_url( $url );
								$lead_page = is_array( $lead_snapshot['page_map'][ $url ] ?? null ) ? $lead_snapshot['page_map'][ $url ] : [];
								?>
								<tr>
									<td><a href="<?php echo esc_url( nexus_get_seo_cockpit_detail_url( $url ) ); ?>"><code><?php echo esc_html( $url ); ?></code></a></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( $row['clicks'] ?? 0 ) ) ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( $row['impressions'] ?? 0 ) ) ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( ( $row['ctr'] ?? 0 ) * 100 ), 1 ) . '%' ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( $row['position'] ?? 0 ), 1 ) ); ?></td>
									<td>
										<?php
										$koko_page = is_array( $koko_snapshot['page_map'][ $url ] ?? null ) ? $koko_snapshot['page_map'][ $url ] : [];
										echo esc_html(
											! empty( $koko_page )
												? sprintf(
													'%s / %s',
													number_format_i18n( (float) ( $koko_page['visitors'] ?? 0 ) ),
													number_format_i18n( (float) ( $koko_page['pageviews'] ?? 0 ) )
												)
												: '—'
										);
										?>
									</td>
									<td>
										<?php
										echo esc_html(
											! empty( $lead_page )
												? sprintf(
													'%d / %d',
													(int) ( $lead_page['current']['requests'] ?? 0 ),
													(int) ( $lead_page['lifetime']['requests'] ?? 0 )
												)
												: '—'
										);
										?>
									</td>
									<td><?php echo esc_html( (string) ( $context['post_type'] ?? 'nicht zugeordnet' ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</section>

				<section class="nexus-seo-cockpit__panel">
					<h2>Top Queries</h2>
					<table class="widefat striped nexus-seo-cockpit__table">
						<thead>
							<tr>
								<th>Query</th>
								<th>Klicks</th>
								<th>Impressionen</th>
								<th>CTR</th>
								<th>Position</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( (array) $snapshot['top_queries'] as $row ) : ?>
								<tr>
									<td><?php echo esc_html( nexus_get_seo_cockpit_row_label( $row ) ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( $row['clicks'] ?? 0 ) ) ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( $row['impressions'] ?? 0 ) ) ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( ( $row['ctr'] ?? 0 ) * 100 ), 1 ) . '%' ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (float) ( $row['position'] ?? 0 ), 1 ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</section>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render the cockpit settings page.
 *
 * @return void
 */
function nexus_render_seo_cockpit_settings_page() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		return;
	}

	$settings = nexus_get_seo_cockpit_settings();
	$config   = nexus_get_seo_cockpit_search_console_config();
	$source   = [
		'client_id'     => defined( 'NEXUS_GSC_CLIENT_ID' ) && NEXUS_GSC_CLIENT_ID ? 'Konstante' : 'Option',
		'client_secret' => defined( 'NEXUS_GSC_CLIENT_SECRET' ) && NEXUS_GSC_CLIENT_SECRET ? 'Konstante' : 'Option',
		'property'      => defined( 'NEXUS_GSC_PROPERTY' ) && NEXUS_GSC_PROPERTY ? 'Konstante' : 'Option',
	];
	?>
	<div class="wrap nexus-seo-cockpit">
		<h1>SEO Cockpit Einstellungen</h1>
		<?php settings_errors( nexus_get_seo_cockpit_option_name() ); ?>

		<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--setup">
			<h2>So aktivierst du Search Console</h2>
			<ol class="nexus-seo-cockpit__steps">
				<li>In Google Cloud einen OAuth Client vom Typ <strong>Web application</strong> anlegen.</li>
				<li>Als autorisierte Redirect URI exakt diese URL hinterlegen: <code><?php echo esc_html( $config['redirect_uri'] ); ?></code></li>
				<li>Hier Property, Client ID und Client Secret speichern und danach zur Übersicht wechseln.</li>
			</ol>
			<p class="nexus-seo-cockpit__hint">Wichtig: Die Property muss exakt so geschrieben sein wie in Search Console, zum Beispiel <code>sc-domain:hasimuener.de</code>.</p>
		</section>

		<form method="post" action="options.php" class="nexus-seo-cockpit__settings-form">
			<?php settings_fields( 'nexus_seo_cockpit_settings' ); ?>

			<section class="nexus-seo-cockpit__panel">
				<h2>Google Search Console</h2>
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><label for="nexus-seo-property">Property</label></th>
							<td>
								<input id="nexus-seo-property" name="<?php echo esc_attr( nexus_get_seo_cockpit_option_name() ); ?>[property]" type="text" class="regular-text" value="<?php echo esc_attr( $settings['property'] ); ?>" placeholder="sc-domain:example.com oder https://example.com/" <?php disabled( 'Konstante' === $source['property'] ); ?>>
								<p class="description">Aktive Quelle: <?php echo esc_html( $source['property'] ); ?>.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="nexus-seo-client-id">Client ID</label></th>
							<td>
								<input id="nexus-seo-client-id" name="<?php echo esc_attr( nexus_get_seo_cockpit_option_name() ); ?>[client_id]" type="text" class="regular-text" value="<?php echo esc_attr( $settings['client_id'] ); ?>" <?php disabled( 'Konstante' === $source['client_id'] ); ?>>
								<p class="description">Aktive Quelle: <?php echo esc_html( $source['client_id'] ); ?>. Secrets und IDs können auch außerhalb des Repos als Konstanten gesetzt werden.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="nexus-seo-client-secret">Client Secret</label></th>
							<td>
								<input id="nexus-seo-client-secret" name="<?php echo esc_attr( nexus_get_seo_cockpit_option_name() ); ?>[client_secret]" type="password" class="regular-text" value="<?php echo esc_attr( $settings['client_secret'] ); ?>" <?php disabled( 'Konstante' === $source['client_secret'] ); ?>>
								<p class="description">Aktive Quelle: <?php echo esc_html( $source['client_secret'] ); ?>.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="nexus-seo-refresh-window">Cache in Stunden</label></th>
							<td>
								<input id="nexus-seo-refresh-window" name="<?php echo esc_attr( nexus_get_seo_cockpit_option_name() ); ?>[refresh_window]" type="number" min="1" max="24" class="small-text" value="<?php echo esc_attr( $settings['refresh_window'] ); ?>">
								<p class="description">Der Cache und das Cron-Intervall folgen diesem Wert jetzt konsistent.</p>
							</td>
						</tr>
						<tr>
							<th scope="row">Redirect URI</th>
							<td>
								<code><?php echo esc_html( $config['redirect_uri'] ); ?></code>
								<p class="description">Diese URI muss im Google OAuth Client als autorisierte Redirect URI eingetragen sein.</p>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button( 'Einstellungen speichern' ); ?>
			</section>
		</form>
	</div>
	<?php
}
