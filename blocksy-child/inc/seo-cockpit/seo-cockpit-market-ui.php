<?php
/**
 * Market Intelligence admin UI and controls.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @return string */
function nexus_market_intelligence_admin_slug() {
	return 'nexus-seo-cockpit-market';
}

/** @return string */
function nexus_market_intelligence_admin_url( $args = [] ) {
	$url = admin_url( 'admin.php?page=' . nexus_market_intelligence_admin_slug() );

	return ! empty( $args ) ? add_query_arg( $args, $url ) : $url;
}

/** @return void */
function nexus_register_market_intelligence_admin_page() {
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Market Intelligence',
		'Markt & Wettbewerb',
		nexus_get_seo_cockpit_view_cap(),
		nexus_market_intelligence_admin_slug(),
		'nexus_render_market_intelligence_admin_page'
	);
}
add_action( 'admin_menu', 'nexus_register_market_intelligence_admin_page', 44 );

/**
 * Load the small additive stylesheet only on the market page.
 *
 * @param string $hook Current admin hook.
 * @return void
 */
function nexus_enqueue_market_intelligence_assets( $hook ) {
	$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : '';
	if ( ! in_array( $page, [ nexus_market_intelligence_admin_slug(), nexus_get_seo_cockpit_menu_slug() ], true ) ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/seo-cockpit-market.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'nexus-seo-cockpit-market',
		get_stylesheet_directory_uri() . '/assets/css/seo-cockpit-market.css',
		[ 'nexus-seo-cockpit-admin' ],
		filemtime( $path )
	);
}
add_action( 'admin_enqueue_scripts', 'nexus_enqueue_market_intelligence_assets', 25 );

/**
 * Sanitize provider settings while preserving an existing password when the
 * password field is left empty.
 *
 * @param array<string, mixed> $raw Raw POST payload.
 * @return array<string, string>
 */
function nexus_sanitize_dataforseo_settings( $raw ) {
	$raw      = is_array( $raw ) ? $raw : [];
	$previous = nexus_get_dataforseo_settings();
	$password = trim( (string) ( $raw['api_password'] ?? '' ) );

	if ( '' === $password ) {
		$password = (string) ( $previous['api_password'] ?? '' );
	} else {
		$password = preg_replace( '/[\x00-\x1F\x7F]/u', '', $password );
		$password = is_string( $password ) ? trim( $password ) : '';
	}

	$watch_keywords         = sanitize_textarea_field( (string) ( $raw['watch_keywords'] ?? '' ) );
	$strategic_competitors = sanitize_textarea_field( (string) ( $raw['strategic_competitors'] ?? '' ) );

	return [
		'api_login'               => sanitize_text_field( (string) ( $raw['api_login'] ?? $previous['api_login'] ?? '' ) ),
		'api_password'            => $password,
		'location_name'           => sanitize_text_field( (string) ( $raw['location_name'] ?? 'Germany' ) ),
		'language_code'           => strtolower( sanitize_key( (string) ( $raw['language_code'] ?? 'de' ) ) ),
		'local_location_name'        => sanitize_text_field( (string) ( $raw['local_location_name'] ?? '' ) ),
		'organic_live_location_name' => sanitize_text_field( (string) ( $raw['organic_live_location_name'] ?? $raw['local_location_name'] ?? '' ) ),
		'organic_live_depth'         => (string) max( 10, min( 200, absint( $raw['organic_live_depth'] ?? 50 ) ) ),
		'local_business_name'        => sanitize_text_field( (string) ( $raw['local_business_name'] ?? '' ) ),
		'watch_keywords'          => $watch_keywords,
		'strategic_competitors'  => $strategic_competitors,
		'auto_refresh'            => ! empty( $raw['auto_refresh'] ) ? '1' : '0',
		'ranked_limit'            => (string) max( 20, min( 250, absint( $raw['ranked_limit'] ?? 100 ) ) ),
		'competitor_limit'        => (string) max( 5, min( 50, absint( $raw['competitor_limit'] ?? 15 ) ) ),
		'keyword_overview_limit'  => (string) max( 10, min( 100, absint( $raw['keyword_overview_limit'] ?? 40 ) ) ),
		'monthly_auto_budget_usd' => number_format( max( 0.25, min( 100.0, (float) ( $raw['monthly_auto_budget_usd'] ?? 2.0 ) ) ), 2, '.', '' ),
	];
}

/** @return void */
function nexus_handle_dataforseo_settings_save() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_dataforseo_settings_save' );

	$raw = isset( $_POST['dataforseo'] ) && is_array( $_POST['dataforseo'] )
		? wp_unslash( $_POST['dataforseo'] )
		: [];

	$settings = nexus_sanitize_dataforseo_settings( $raw );

	if ( ! empty( $_POST['clear_credentials'] ) && ! nexus_dataforseo_uses_constant_credentials() ) {
		$settings['api_login']    = '';
		$settings['api_password'] = '';
	}

	nexus_update_dataforseo_settings( $settings );
	nexus_maybe_schedule_market_intelligence_refresh();

	wp_safe_redirect( nexus_market_intelligence_admin_url( [ 'market_notice' => 'settings_saved' ] ) );
	exit;
}
add_action( 'admin_post_nexus_dataforseo_settings_save', 'nexus_handle_dataforseo_settings_save' );

/** @return void */
function nexus_handle_market_intelligence_refresh() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_market_intelligence_refresh' );
	$result = nexus_refresh_market_intelligence( false );
	$notice = is_wp_error( $result ) ? 'refresh_error' : 'refresh_done';

	wp_safe_redirect(
		nexus_market_intelligence_admin_url(
			[
				'market_notice' => $notice,
				'market_error'  => is_wp_error( $result ) ? rawurlencode( $result->get_error_message() ) : '',
			]
		)
	);
	exit;
}
add_action( 'admin_post_nexus_market_intelligence_refresh', 'nexus_handle_market_intelligence_refresh' );

/** @return void */
function nexus_handle_market_intelligence_live_refresh() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_market_intelligence_live_refresh' );
	$mode   = isset( $_POST['mode'] ) ? sanitize_key( (string) wp_unslash( $_POST['mode'] ) ) : 'organic';
	$mode   = 'maps' === $mode ? 'maps' : 'organic';
	$result = nexus_queue_market_intelligence_live_refresh( $mode );
	$notice = is_wp_error( $result ) ? 'live_error' : ( 'maps' === $mode ? 'maps_queued' : 'live_queued' );

	wp_safe_redirect(
		nexus_market_intelligence_admin_url(
			[
				'market_notice' => $notice,
				'market_error'  => is_wp_error( $result ) ? rawurlencode( $result->get_error_message() ) : '',
			]
		)
	);
	exit;
}
add_action( 'admin_post_nexus_market_intelligence_live_refresh', 'nexus_handle_market_intelligence_live_refresh' );

/** @return void */
function nexus_handle_market_intelligence_strategic_refresh() {
	if ( ! nexus_current_user_can_manage_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	check_admin_referer( 'nexus_market_intelligence_strategic_refresh' );
	$result = nexus_refresh_market_intelligence_strategic_overview();

	if ( is_wp_error( $result ) ) {
		$notice = 'strategic_error';
		$error  = $result->get_error_message();
	} else {
		$errors = is_array( $result['errors'] ?? null ) ? $result['errors'] : [];
		$notice = empty( $errors ) ? 'strategic_done' : 'strategic_partial';
		$error  = empty( $errors ) ? '' : implode( ' | ', array_values( $errors ) );
	}

	wp_safe_redirect(
		nexus_market_intelligence_admin_url(
			[
				'market_notice' => $notice,
				'market_error'  => '' !== $error ? rawurlencode( $error ) : '',
			]
		)
	);
	exit;
}
add_action( 'admin_post_nexus_market_intelligence_strategic_refresh', 'nexus_handle_market_intelligence_strategic_refresh' );

/**
 * Render one admin notice.
 *
 * @return void
 */
function nexus_render_market_intelligence_notice() {
	$notice = isset( $_GET['market_notice'] ) ? sanitize_key( (string) wp_unslash( $_GET['market_notice'] ) ) : '';
	if ( '' === $notice ) {
		return;
	}

	$messages = [
		'settings_saved' => [ 'success', 'DataForSEO-Einstellungen gespeichert.' ],
		'refresh_done'   => [ 'success', 'Market Intelligence wurde aktualisiert.' ],
		'live_done'      => [ 'success', 'Live-SERP-Watchlist wurde aktualisiert.' ],
		'maps_done'      => [ 'success', 'Google-Maps-Watchlist wurde aktualisiert.' ],
		'live_queued'    => [ 'info', 'Organic Live läuft im Hintergrund. Die Watchlist wird Keyword für Keyword geprüft.' ],
		'maps_queued'    => [ 'info', 'Maps Live läuft im Hintergrund. Die Watchlist wird Keyword für Keyword geprüft.' ],
		'refresh_error'  => [ 'error', 'Market-Refresh fehlgeschlagen.' ],
		'live_error'     => [ 'error', 'Live-Check fehlgeschlagen.' ],
		'export_empty'      => [ 'warning', 'Für den Market-Export liegt noch kein verwertbarer Snapshot vor.' ],
		'strategic_done'    => [ 'success', 'Strategische Wettbewerber wurden mit DataForSEO geprüft.' ],
		'strategic_partial' => [ 'warning', 'Strategische Wettbewerber wurden teilweise aktualisiert.' ],
		'strategic_error'   => [ 'error', 'Strategischer Wettbewerber-Check fehlgeschlagen.' ],
	];

	if ( ! isset( $messages[ $notice ] ) ) {
		return;
	}

	$type    = (string) $messages[ $notice ][0];
	$message = (string) $messages[ $notice ][1];
	$error   = isset( $_GET['market_error'] ) ? sanitize_text_field( rawurldecode( (string) wp_unslash( $_GET['market_error'] ) ) ) : '';

	if ( '' !== $error ) {
		$message .= ' ' . $error;
	}

	printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>', esc_attr( $type ), esc_html( $message ) );
}

/**
 * Count top-10 ranked rows.
 *
 * @param array<int, array<string, mixed>> $rows Ranked rows.
 * @return int
 */
function nexus_market_intelligence_top10_count( $rows ) {
	$count = 0;
	foreach ( $rows as $row ) {
		$rank = is_array( $row ) ? absint( $row['rank_group'] ?? 0 ) : 0;
		if ( $rank > 0 && $rank <= 10 ) {
			$count++;
		}
	}
	return $count;
}

/**
 * Render market opportunities shared by the dedicated page and dashboard.
 *
 * @param array<int, array<string, mixed>> $rows Opportunity rows.
 * @param int                              $limit Max rows.
 * @return void
 */
function nexus_render_market_intelligence_opportunities( $rows, $limit = 10 ) {
	$rows = array_slice( $rows, 0, max( 1, $limit ) );

	if ( empty( $rows ) ) {
		echo '<p class="nsc-market-empty">Noch keine kombinierbaren Markt-/GSC-Signale. Nach dem ersten DataForSEO-Refresh wird dieser Bereich gefüllt.</p>';
		return;
	}

	$segment_counts = [ 'business' => 0, 'content' => 0, 'brand' => 0, 'other' => 0 ];
	foreach ( $rows as $row ) {
		$key = sanitize_key( (string) ( $row['segment'] ?? 'other' ) );
		if ( isset( $segment_counts[ $key ] ) ) {
			$segment_counts[ $key ]++;
		}
	}
	?>
	<div class="nsc-market-segment-summary">
		<span><strong><?php echo esc_html( number_format_i18n( $segment_counts['business'] ) ); ?></strong> Geschäft</span>
		<span><strong><?php echo esc_html( number_format_i18n( $segment_counts['content'] ) ); ?></strong> Content</span>
		<span><strong><?php echo esc_html( number_format_i18n( $segment_counts['brand'] ) ); ?></strong> Marke/Proof</span>
	</div>
	<div class="nsc-market-opportunity-list">
		<?php foreach ( $rows as $row ) : ?>
			<article class="nsc-market-opportunity">
				<div class="nsc-market-opportunity__score"><?php echo esc_html( (string) absint( $row['score'] ?? 0 ) ); ?></div>
				<div class="nsc-market-opportunity__body">
					<div class="nsc-market-opportunity__head">
						<strong><?php echo esc_html( (string) ( $row['keyword'] ?? '' ) ); ?></strong>
						<div class="nsc-market-opportunity__badges">
							<span class="is-segment-<?php echo esc_attr( sanitize_key( (string) ( $row['segment'] ?? 'other' ) ) ); ?>"><?php echo esc_html( (string) ( $row['segment_label'] ?? 'Beobachten' ) ); ?></span>
							<span><?php echo esc_html( (string) ( $row['action'] ?? 'Beobachten' ) ); ?></span>
						</div>
					</div>
					<div class="nsc-market-opportunity__meta">
						<span>DataForSEO Pos. <?php echo esc_html( number_format_i18n( (float) ( $row['rank'] ?? 0 ), 0 ) ); ?></span>
						<span>Vol. <?php echo esc_html( number_format_i18n( (float) ( $row['search_volume'] ?? 0 ), 0 ) ); ?></span>
						<?php if ( ! empty( $row['page_role_label'] ) ) : ?><span><?php echo esc_html( (string) $row['page_role_label'] ); ?></span><?php endif; ?>
						<?php if ( (float) ( $row['gsc_impressions'] ?? 0 ) > 0 ) : ?><span>GSC <?php echo esc_html( number_format_i18n( (float) $row['gsc_impressions'], 0 ) ); ?> Impr.</span><?php endif; ?>
						<?php if ( absint( $row['leads_current'] ?? 0 ) > 0 ) : ?><span><?php echo esc_html( number_format_i18n( absint( $row['leads_current'] ) ) ); ?> Audit-Leads</span><?php endif; ?>
						<?php if ( absint( $row['crm_contacts_current'] ?? 0 ) > 0 ) : ?><span><?php echo esc_html( number_format_i18n( absint( $row['crm_contacts_current'] ) ) ); ?> CRM-Kontakte</span><?php endif; ?>
						<?php if ( isset( $row['difficulty'] ) && is_numeric( $row['difficulty'] ) ) : ?><span>KD <?php echo esc_html( number_format_i18n( (float) $row['difficulty'], 0 ) ); ?></span><?php endif; ?>
					</div>
					<?php if ( ! empty( $row['url'] ) ) : ?><a href="<?php echo esc_url( (string) $row['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( function_exists( 'nexus_get_seo_cockpit_short_url' ) ? nexus_get_seo_cockpit_short_url( (string) $row['url'] ) : (string) $row['url'] ); ?></a><?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render the manually curated strategic comparison group.
 *
 * Automatic DataForSEO competitors remain untouched; this layer answers the
 * different question of who should be watched for the intended WordPress/B2B
 * market. Metrics appear when a strategic domain is also present in the
 * current organic-overlap snapshot.
 *
 * @param array<int, array<string, mixed>> $rows Strategic competitors.
 * @return void
 */
function nexus_render_market_intelligence_strategic_competitors( $rows ) {
	if ( empty( $rows ) ) {
		echo '<p class="nsc-market-empty">Noch keine strategischen Wettbewerber gepflegt.</p>';
		return;
	}
	?>
	<div class="nsc-market-competitor-grid is-strategic">
		<?php foreach ( $rows as $row ) : ?>
			<?php
			$is_checked = ! empty( $row['is_checked'] );
			$is_overlap = ! empty( $row['is_overlap'] );
			$status     = $is_checked ? 'DataForSEO geprüft' : ( $is_overlap ? 'organische Überschneidung' : 'strategische Watchlist' );
			$status_cls = $is_checked ? 'is-checked' : ( $is_overlap ? 'is-overlap' : 'is-watch' );
			?>
			<article class="nsc-market-competitor is-strategic">
				<div class="nsc-market-competitor__head">
					<strong><?php echo esc_html( (string) ( $row['domain'] ?? '' ) ); ?></strong>
					<span class="nsc-market-strategic-status <?php echo esc_attr( $status_cls ); ?>"><?php echo esc_html( $status ); ?></span>
				</div>

				<?php if ( $is_checked || (float) ( $row['organic_etv'] ?? 0 ) > 0 || absint( $row['organic_keywords'] ?? 0 ) > 0 ) : ?>
					<div><span>Rankende Keywords</span><b><?php echo esc_html( number_format_i18n( absint( $row['organic_keywords'] ?? 0 ) ) ); ?></b></div>
					<div><span>ETV</span><b><?php echo esc_html( number_format_i18n( (float) ( $row['organic_etv'] ?? 0 ), 1 ) ); ?></b></div>
					<div><span>Top 10</span><b><?php echo esc_html( number_format_i18n( absint( $row['domain_top10'] ?? 0 ) ) ); ?></b></div>
					<?php if ( $is_overlap ) : ?><small><?php echo esc_html( number_format_i18n( absint( $row['intersections'] ?? 0 ) ) ); ?> gemeinsame SERP-Keywords im aktuellen Overlap-Snapshot.</small><?php endif; ?>
				<?php else : ?>
					<p>Noch keine Domain-Metriken geladen. Der manuelle Check liest Ranking-Verteilung und Traffic-Schätzung direkt aus DataForSEO.</p>
				<?php endif; ?>

				<?php if ( ! empty( $row['checked_at'] ) ) : ?><small>Geprüft <?php echo esc_html( wp_date( 'd.m.Y H:i', absint( $row['checked_at'] ) ) ); ?></small><?php endif; ?>
				<a href="<?php echo esc_url( 'https://' . (string) ( $row['domain'] ?? '' ) . '/' ); ?>" target="_blank" rel="noopener noreferrer">Website öffnen</a>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}
/**
 * Render competitor cards.
 *
 * @param array<int, array<string, mixed>> $rows Competitors.
 * @param int                              $limit Max rows.
 * @return void
 */
function nexus_render_market_intelligence_competitors( $rows, $limit = 8 ) {
	$rows = array_slice( $rows, 0, max( 1, $limit ) );
	if ( empty( $rows ) ) {
		echo '<p class="nsc-market-empty">Noch keine Wettbewerberdaten.</p>';
		return;
	}
	?>
	<div class="nsc-market-competitor-grid">
		<?php foreach ( $rows as $row ) : ?>
			<article class="nsc-market-competitor">
				<strong><?php echo esc_html( (string) ( $row['domain'] ?? '' ) ); ?></strong>
				<div><span>Überschneidungen</span><b><?php echo esc_html( number_format_i18n( absint( $row['intersections'] ?? 0 ) ) ); ?></b></div>
				<div><span>Shared ETV</span><b><?php echo esc_html( number_format_i18n( (float) ( $row['shared_etv'] ?? 0 ), 1 ) ); ?></b></div>
				<div><span>Top-10 shared</span><b><?php echo esc_html( number_format_i18n( absint( $row['top10_shared'] ?? 0 ) ) ); ?></b></div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render live organic/maps watch results.
 *
 * @param array<int, array<string, mixed>> $rows Rows.
 * @param string                           $mode organic|maps.
 * @return void
 */
function nexus_render_market_intelligence_live_rows( $rows, $mode ) {
	if ( empty( $rows ) ) {
		echo '<p class="nsc-market-empty">Noch kein manueller Live-Check für diese Watchlist.</p>';
		return;
	}
	?>
	<div class="nsc-market-live-list">
		<?php foreach ( $rows as $row ) : ?>
			<?php
			$result   = is_array( $row['result'] ?? null ) ? $row['result'] : [];
			$own      = is_array( $result['own'] ?? null ) ? $result['own'] : [];
			$depth    = absint( $row['depth'] ?? ( 'maps' === $mode ? 100 : 10 ) );
			$location = trim( (string) ( $row['location_name'] ?? '' ) );
			$context  = 'maps' === $mode ? 'Google Maps' : 'Google Organic';
			if ( '' !== $location ) {
				$context .= ' · ' . $location;
			}
			if ( 'organic' === $mode ) {
				$context .= ' · Top ' . max( 10, $depth );
			}
			$status = ! empty( $own )
				? '#' . absint( $own['rank'] ?? 0 )
				: ( 'organic' === $mode ? 'nicht in Top ' . max( 10, $depth ) : 'kein eigener Maps-Treffer' );
			?>
			<article>
				<div><strong><?php echo esc_html( (string) ( $row['keyword'] ?? '' ) ); ?></strong><span><?php echo esc_html( $context ); ?></span></div>
				<b><?php echo esc_html( $status ); ?></b>
				<?php if ( 'maps' === $mode && ! empty( $own['title'] ) ) : ?><small><?php echo esc_html( (string) $own['title'] ); ?></small><?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Render the dedicated Market Intelligence workspace.
 *
 * @return void
 */
function nexus_render_market_intelligence_admin_page() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	$can_manage = nexus_current_user_can_manage_seo_cockpit();
	$config     = nexus_get_dataforseo_config();
	$settings   = nexus_get_dataforseo_settings();
	$runtime    = nexus_get_dataforseo_runtime();
	$snapshot   = nexus_get_market_intelligence_snapshot();
	$ranked     = is_array( $snapshot['ranked'] ?? null ) ? $snapshot['ranked'] : [];
	$ranked_rows= is_array( $ranked['rows'] ?? null ) ? $ranked['rows'] : [];
	$metrics    = is_array( $ranked['metrics'] ?? null ) ? $ranked['metrics'] : [];
	$competitors= is_array( $snapshot['competitors'] ?? null ) ? $snapshot['competitors'] : [];
	$strategic_competitors = function_exists( 'nexus_market_intelligence_strategic_competitors' ) ? nexus_market_intelligence_strategic_competitors( $competitors ) : [];
	$seo        = function_exists( 'nexus_get_seo_cockpit_snapshot' ) ? nexus_get_seo_cockpit_snapshot( false, 28 ) : [];
	$opportunities = is_wp_error( $seo ) || ! is_array( $seo ) ? [] : nexus_get_market_intelligence_opportunities( $seo, 12 );
	$has_credentials = nexus_dataforseo_has_credentials();
	$next_sync  = wp_next_scheduled( nexus_market_intelligence_cron_hook() );
	$organic_job = function_exists( 'nexus_get_market_intelligence_live_job' ) ? nexus_get_market_intelligence_live_job( 'organic' ) : [];
	$maps_job    = function_exists( 'nexus_get_market_intelligence_live_job' ) ? nexus_get_market_intelligence_live_job( 'maps' ) : [];

	$organic_job_active = in_array( (string) ( $organic_job['status'] ?? '' ), [ 'queued', 'running' ], true );
	$maps_job_active    = in_array( (string) ( $maps_job['status'] ?? '' ), [ 'queued', 'running' ], true );

	$organic_live_rows = $organic_job_active
		? array_values( (array) ( $organic_job['rows'] ?? [] ) )
		: array_values( (array) ( $snapshot['live_serp'] ?? [] ) );
	$maps_live_rows = $maps_job_active
		? array_values( (array) ( $maps_job['rows'] ?? [] ) )
		: array_values( (array) ( $snapshot['local_maps'] ?? [] ) );

	$organic_job_context = is_array( $organic_job['context'] ?? null ) ? $organic_job['context'] : [];
	$maps_job_context    = is_array( $maps_job['context'] ?? null ) ? $maps_job['context'] : [];

	$organic_live_location = $organic_job_active
		? (string) ( $organic_job_context['location_name'] ?? $config['organic_live_location_name'] )
		: (string) $config['organic_live_location_name'];
	$organic_live_depth = $organic_job_active
		? absint( $organic_job_context['depth'] ?? $config['organic_live_depth'] )
		: absint( $config['organic_live_depth'] );
	$maps_live_location = $maps_job_active
		? (string) ( $maps_job_context['location_name'] ?? $config['local_location_name'] )
		: (string) $config['local_location_name'];
	?>
	<div class="wrap nexus-seo-cockpit nsc-market">
		<?php nexus_render_market_intelligence_notice(); ?>

		<header class="nsc-market-hero">
			<div>
				<p class="nexus-seo-cockpit__eyebrow">Market Intelligence · DataForSEO</p>
				<h1>Markt & Wettbewerb</h1>
				<p>Externe Suchmarktdaten werden mit Search Console und CRM verbunden. DataForSEO bleibt Datenquelle; das Cockpit entscheidet anhand eigener Regeln, was relevant ist.</p>
			</div>
			<div class="nsc-market-hero__actions">
				<span class="nexus-seo-cockpit__status-dot <?php echo $has_credentials ? 'is-connected' : 'is-warning'; ?>"><?php echo esc_html( $has_credentials ? 'DataForSEO konfiguriert' : 'Credentials fehlen' ); ?></span>
				<?php if ( $can_manage && $has_credentials ) : ?>
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_market_intelligence_refresh' ) ); ?>">
						<?php wp_nonce_field( 'nexus_market_intelligence_refresh' ); ?>
						<button class="button button-primary" type="submit">Marktdaten aktualisieren</button>
					</form>
				<?php endif; ?>
				<?php if ( $can_manage && ! empty( $snapshot ) ) : ?>
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_market_intelligence_export' ) ); ?>">
						<?php wp_nonce_field( 'nexus_market_intelligence_export' ); ?>
						<button class="button" type="submit"><span class="dashicons dashicons-download" aria-hidden="true"></span> Market CSV</button>
					</form>
				<?php endif; ?>
			</div>
		</header>

		<section class="nsc-market-kpis">
			<article><span>Rankende Keywords</span><strong><?php echo esc_html( number_format_i18n( absint( $ranked['total_count'] ?? 0 ) ) ); ?></strong><small>DataForSEO Labs · <?php echo esc_html( (string) $config['location_name'] ); ?></small></article>
			<article><span>Top 10</span><strong><?php echo esc_html( number_format_i18n( nexus_market_intelligence_top10_count( $ranked_rows ) ) ); ?></strong><small>aus dem geladenen Keyword-Set</small></article>
			<article><span>Geschätzter Traffic</span><strong><?php echo esc_html( number_format_i18n( (float) ( $metrics['etv'] ?? 0 ), 1 ) ); ?></strong><small>ETV · externe Schätzung</small></article>
			<article><span>Wettbewerber</span><strong><?php echo esc_html( number_format_i18n( count( $competitors ) ) ); ?></strong><small>organische Überschneidung</small></article>
			<article><span>Strategische Gruppe</span><strong><?php echo esc_html( number_format_i18n( count( $strategic_competitors ) ) ); ?></strong><small>WordPress/B2B-Vergleich</small></article>
			<article><span>API-Kosten Monat</span><strong>$<?php echo esc_html( number_format_i18n( (float) ( $runtime['cost_month_usd'] ?? 0 ), 4 ) ); ?></strong><small>Auto-Budget $<?php echo esc_html( number_format_i18n( (float) $config['monthly_auto_budget_usd'], 2 ) ); ?></small></article>
		</section>

		<section class="nsc-market-section">
			<div class="nsc-market-section__head"><div><p class="nexus-seo-cockpit__eyebrow">Opportunity Engine</p><h2>Wo Markt, GSC und Leads zusammenfallen</h2><p>Score aus Suchvolumen, Ranking-Lücke, eigener GSC-Nachfrage, Intent und vorhandenen Lead-Signalen. Kein externer Tool-Score wird blind übernommen.</p></div></div>
			<?php nexus_render_market_intelligence_opportunities( $opportunities, 12 ); ?>
		</section>

		<section class="nsc-market-section">
			<div class="nsc-market-section__head">
				<div>
					<p class="nexus-seo-cockpit__eyebrow">Strategische Vergleichsgruppe</p>
					<h2>Mit wem du dich im WordPress-/B2B-Markt vergleichen willst</h2>
					<p>Diese Liste ist bewusst kuratiert und bleibt getrennt von der automatischen SERP-Überschneidung. So zeigt das Cockpit gleichzeitig deine aktuelle Google-Nachbarschaft und deine eigentliche Zielkonkurrenz.</p>
				</div>
				<?php if ( $can_manage && $has_credentials && ! empty( $strategic_competitors ) ) : ?>
					<div class="nsc-market-section__actions">
						<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_market_intelligence_strategic_refresh' ) ); ?>">
							<?php wp_nonce_field( 'nexus_market_intelligence_strategic_refresh' ); ?>
							<button type="submit" class="button">Strategische Domains prüfen</button>
						</form>
						<small>manuell · max. 8 DataForSEO-Calls</small>
					</div>
				<?php endif; ?>
			</div>
			<?php nexus_render_market_intelligence_strategic_competitors( $strategic_competitors ); ?>
		</section>

		<section class="nsc-market-section">
			<div class="nsc-market-section__head"><div><p class="nexus-seo-cockpit__eyebrow">Competitive Landscape</p><h2>Wer dir organisch wirklich begegnet</h2><p>Diese Liste bleibt vollständig datengetrieben. Erneuerbare-Energien-Domains sind hier ein Signal dafür, welche Themen Google aktuell mit deiner Domain verbindet – nicht automatisch deine strategische Konkurrenz.</p></div></div>
			<?php nexus_render_market_intelligence_competitors( $competitors, 10 ); ?>
		</section>

		<section class="nsc-market-section">
			<div class="nsc-market-section__head"><div><p class="nexus-seo-cockpit__eyebrow">Live Watch</p><h2>Strategische Keywords live prüfen</h2><p>Live-SERPs und Google Maps werden nur auf expliziten Klick abgefragt. Jeder Lauf wird im Hintergrund Keyword für Keyword verarbeitet, damit Varnish und PHP nicht auf mehrere externe Requests warten müssen.</p></div>
				<?php if ( $can_manage && $has_credentials ) : ?><div class="nsc-market-section__actions">
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_market_intelligence_live_refresh' ) ); ?>"><?php wp_nonce_field( 'nexus_market_intelligence_live_refresh' ); ?><input type="hidden" name="mode" value="organic"><button type="submit" class="button" <?php disabled( in_array( (string) ( $organic_job['status'] ?? '' ), [ 'queued', 'running' ], true ) ); ?>><?php echo esc_html( in_array( (string) ( $organic_job['status'] ?? '' ), [ 'queued', 'running' ], true ) ? 'Organic läuft …' : 'Organic live' ); ?></button></form>
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_market_intelligence_live_refresh' ) ); ?>"><?php wp_nonce_field( 'nexus_market_intelligence_live_refresh' ); ?><input type="hidden" name="mode" value="maps"><button type="submit" class="button" <?php disabled( in_array( (string) ( $maps_job['status'] ?? '' ), [ 'queued', 'running' ], true ) ); ?>><?php echo esc_html( in_array( (string) ( $maps_job['status'] ?? '' ), [ 'queued', 'running' ], true ) ? 'Maps läuft …' : 'Maps live' ); ?></button></form>
				</div><?php endif; ?>
			</div>
			<div class="nsc-market-live-progress">
				<?php foreach ( [ 'organic' => $organic_job, 'maps' => $maps_job ] as $job_mode => $job ) : ?>
					<?php
					$job_status = (string) ( $job['status'] ?? 'idle' );
					$total      = count( (array) ( $job['keywords'] ?? [] ) );
					$done       = min( $total, absint( $job['index'] ?? 0 ) );
					$is_active  = in_array( $job_status, [ 'queued', 'running' ], true );
					?>
					<?php if ( $is_active ) : ?>
						<span class="nsc-market-live-progress__item is-running"><strong><?php echo esc_html( 'organic' === $job_mode ? 'Organic Live' : 'Maps Live' ); ?></strong> läuft im Hintergrund · <?php echo esc_html( $done . '/' . $total ); ?></span>
					<?php elseif ( in_array( $job_status, [ 'partial', 'error' ], true ) ) : ?>
						<span class="nsc-market-live-progress__item is-warning"><strong><?php echo esc_html( 'organic' === $job_mode ? 'Organic Live' : 'Maps Live' ); ?></strong> <?php echo esc_html( 'partial' === $job_status ? 'teilweise abgeschlossen' : 'mit Fehler beendet' ); ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="nsc-market-live-grid">
				<div>
					<h3>Google Organic · <?php echo esc_html( $organic_live_location ); ?> · Top <?php echo esc_html( (string) max( 10, $organic_live_depth ) ); ?></h3>
					<?php if ( $organic_job_active && empty( $organic_live_rows ) ) : ?>
						<p class="nsc-market-empty">Neue Top-<?php echo esc_html( (string) max( 10, $organic_live_depth ) ); ?>-Prüfung läuft. Die ersten Ergebnisse erscheinen hier, sobald das erste Keyword abgeschlossen ist.</p>
					<?php else : ?>
						<?php nexus_render_market_intelligence_live_rows( $organic_live_rows, 'organic' ); ?>
					<?php endif; ?>
				</div>
				<div>
					<h3>Google Maps · <?php echo esc_html( $maps_live_location ); ?></h3>
					<?php if ( $maps_job_active && empty( $maps_live_rows ) ) : ?>
						<p class="nsc-market-empty">Neue Maps-Prüfung läuft. Die ersten Ergebnisse erscheinen hier, sobald das erste Keyword abgeschlossen ist.</p>
					<?php else : ?>
						<?php nexus_render_market_intelligence_live_rows( $maps_live_rows, 'maps' ); ?>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="nsc-market-section">
			<div class="nsc-market-section__head"><div><p class="nexus-seo-cockpit__eyebrow">Keyword Radar</p><h2>Stärkste geladene Rankings</h2><p>DataForSEO liefert Suchvolumen, Intent, Keyword Difficulty und die aktuelle Datenbankposition. GSC bleibt die Quelle für deine realen Impressionen und Klicks.</p></div></div>
			<div class="nexus-seo-cockpit__table-wrap">
				<table class="widefat striped nexus-seo-cockpit__table">
					<thead><tr><th>Keyword</th><th>Pos.</th><th>Volumen</th><th>Intent</th><th>KD</th><th>URL</th></tr></thead>
					<tbody>
						<?php foreach ( array_slice( $ranked_rows, 0, 25 ) as $row ) : ?>
							<tr>
								<td><strong><?php echo esc_html( (string) ( $row['keyword'] ?? '' ) ); ?></strong></td>
								<td><?php echo esc_html( number_format_i18n( (float) ( $row['rank_group'] ?? 0 ), 0 ) ); ?></td>
								<td><?php echo esc_html( number_format_i18n( (float) ( $row['search_volume'] ?? 0 ), 0 ) ); ?></td>
								<td><?php echo esc_html( '' !== (string) ( $row['intent'] ?? '' ) ? (string) $row['intent'] : '—' ); ?></td>
								<td><?php echo esc_html( isset( $row['difficulty'] ) && is_numeric( $row['difficulty'] ) ? number_format_i18n( (float) $row['difficulty'], 0 ) : '—' ); ?></td>
								<td class="nexus-seo-cockpit__cell--url"><?php if ( ! empty( $row['url'] ) ) : ?><a href="<?php echo esc_url( (string) $row['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( nexus_get_seo_cockpit_short_url( (string) $row['url'] ) ); ?></a><?php else : ?>—<?php endif; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>

		<section class="nsc-market-section nsc-market-settings">
			<div class="nsc-market-section__head"><div><p class="nexus-seo-cockpit__eyebrow">Provider Control</p><h2>DataForSEO konfigurieren</h2><p>Produktion bevorzugt Runtime-Konstanten. Die UI ist ein Fallback und zeigt gespeicherte Passwörter niemals wieder an.</p></div></div>
			<?php if ( ! $can_manage ) : ?><p>Nur Nutzer mit Cockpit-Managementrechten können diese Einstellungen ändern.</p><?php else : ?>
				<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_dataforseo_settings_save' ) ); ?>">
					<?php wp_nonce_field( 'nexus_dataforseo_settings_save' ); ?>
					<div class="nsc-market-form-grid">
						<label><span>API Login</span><input type="text" name="dataforseo[api_login]" value="<?php echo esc_attr( nexus_dataforseo_uses_constant_credentials() ? '' : (string) ( $settings['api_login'] ?? '' ) ); ?>" <?php disabled( nexus_dataforseo_uses_constant_credentials() ); ?> autocomplete="off"><small><?php echo nexus_dataforseo_uses_constant_credentials() ? 'Kommt aus NEXUS_DATAFORSEO_LOGIN.' : 'DataForSEO API Access Login.'; ?></small></label>
						<label><span>API Password</span><input type="password" name="dataforseo[api_password]" value="" <?php disabled( nexus_dataforseo_uses_constant_credentials() ); ?> autocomplete="new-password" placeholder="<?php echo esc_attr( nexus_dataforseo_has_credentials() ? 'Gespeichert – leer lassen zum Beibehalten' : 'API Password' ); ?>"><small><?php echo nexus_dataforseo_uses_constant_credentials() ? 'Kommt aus NEXUS_DATAFORSEO_PASSWORD.' : 'Nie im Repo speichern.'; ?></small></label>
						<label><span>Labs Location</span><input type="text" name="dataforseo[location_name]" value="<?php echo esc_attr( (string) ( $settings['location_name'] ?? 'Germany' ) ); ?>"><small>z. B. Germany.</small></label>
						<label><span>Sprache</span><input type="text" name="dataforseo[language_code]" value="<?php echo esc_attr( (string) ( $settings['language_code'] ?? 'de' ) ); ?>"><small>ISO-Code, z. B. de.</small></label>
						<label><span>Maps Location</span><input type="text" name="dataforseo[local_location_name]" value="<?php echo esc_attr( (string) ( $settings['local_location_name'] ?? '' ) ); ?>"><small>Für lokale Maps-Watchlist.</small></label>
						<label><span>Organic Live Standort</span><input type="text" name="dataforseo[organic_live_location_name]" value="<?php echo esc_attr( (string) ( $settings['organic_live_location_name'] ?? $settings['local_location_name'] ?? 'Hanover,Lower Saxony,Germany' ) ); ?>"><small>Standort für die manuelle Organic-Live-Watchlist, z. B. Hanover,Lower Saxony,Germany.</small></label>
						<label><span>Organic Live Tiefe</span><input type="number" min="10" max="200" step="10" name="dataforseo[organic_live_depth]" value="<?php echo esc_attr( (string) ( $settings['organic_live_depth'] ?? '50' ) ); ?>"><small>Standard 50. DataForSEO berechnet Organic Live in 10er-Blöcken; größere Tiefe kann den manuellen Check verteuern.</small></label>
						<label><span>Business Name</span><input type="text" name="dataforseo[local_business_name]" value="<?php echo esc_attr( (string) ( $settings['local_business_name'] ?? 'Haşim Üner' ) ); ?>"><small>Nur für die Zuordnung im Maps-Ergebnis.</small></label>
						<label><span>Ranked Keywords Limit</span><input type="number" min="20" max="250" name="dataforseo[ranked_limit]" value="<?php echo esc_attr( (string) ( $settings['ranked_limit'] ?? '100' ) ); ?>"></label>
						<label><span>Competitor Limit</span><input type="number" min="5" max="50" name="dataforseo[competitor_limit]" value="<?php echo esc_attr( (string) ( $settings['competitor_limit'] ?? '15' ) ); ?>"></label>
						<label><span>Keyword Overview Limit</span><input type="number" min="10" max="100" name="dataforseo[keyword_overview_limit]" value="<?php echo esc_attr( (string) ( $settings['keyword_overview_limit'] ?? '40' ) ); ?>"></label>
						<label><span>Auto-Budget / Monat (USD)</span><input type="number" step="0.25" min="0.25" max="100" name="dataforseo[monthly_auto_budget_usd]" value="<?php echo esc_attr( (string) ( $settings['monthly_auto_budget_usd'] ?? '2.00' ) ); ?>"><small>Stoppt weitere automatische Calls, sobald die gemeldeten Monatskosten das Limit erreichen.</small></label>
						<label class="nsc-market-form-grid__wide"><span>Watchlist-Keywords</span><textarea rows="7" name="dataforseo[watch_keywords]" placeholder="wordpress freelancer hannover&#10;wordpress entwickler hannover"><?php echo esc_textarea( (string) ( $settings['watch_keywords'] ?? '' ) ); ?></textarea><small>Ein Keyword pro Zeile. Live-Organic nutzt maximal 8, Maps maximal 5 pro manueller Prüfung.</small></label>
						<label class="nsc-market-form-grid__wide"><span>Strategische Wettbewerber</span><textarea rows="7" name="dataforseo[strategic_competitors]" placeholder="oliverfleck.de&#10;onma.de&#10;goldenberg-agentur.de"><?php echo esc_textarea( (string) ( $settings['strategic_competitors'] ?? '' ) ); ?></textarea><small>Eine Domain pro Zeile. Die Liste selbst erzeugt keine Calls. Der Button „Strategische Domains prüfen“ startet bewusst einen kostenpflichtigen Domain-Rank-Overview-Call je Domain (maximal 8).</small></label>
						<label class="nsc-market-checkbox"><input type="checkbox" name="dataforseo[auto_refresh]" value="1" <?php checked( '1', (string) ( $settings['auto_refresh'] ?? '1' ) ); ?>><span>Wöchentliche Labs-Aktualisierung aktivieren</span></label>
					</div>
					<div class="nsc-market-form-actions"><button type="submit" class="button button-primary">Einstellungen speichern</button><?php if ( nexus_dataforseo_has_credentials() && ! nexus_dataforseo_uses_constant_credentials() ) : ?><button type="submit" class="button" name="clear_credentials" value="1">Credentials entfernen</button><?php endif; ?></div>
				</form>
			<?php endif; ?>
		</section>

		<footer class="nsc-market-runtime">
			<span>Letzter Market-Snapshot: <strong><?php echo esc_html( ! empty( $snapshot['generated_at'] ) ? wp_date( 'd.m.Y H:i', (int) $snapshot['generated_at'] ) : 'noch keiner' ); ?></strong></span>
			<span>Nächster Auto-Sync: <strong><?php echo esc_html( $next_sync ? wp_date( 'd.m.Y H:i', (int) $next_sync ) : 'nicht geplant' ); ?></strong></span>
			<span>Letzter API-Call: <strong><?php echo esc_html( ! empty( $runtime['last_call_at'] ) ? wp_date( 'd.m.Y H:i', (int) $runtime['last_call_at'] ) : '—' ); ?></strong></span>
		</footer>
	</div>
	<?php
}

/**
 * Render a compact market pulse inside Dashboard V3.
 *
 * @param array<string, mixed> $seo_snapshot Current SEO snapshot.
 * @return void
 */
function nexus_render_market_intelligence_dashboard_panel( $seo_snapshot ) {
	$market = nexus_get_market_intelligence_snapshot();
	if ( empty( $market ) ) {
		return;
	}

	$opportunities = nexus_get_market_intelligence_opportunities( $seo_snapshot, 4 );
	$competitors   = is_array( $market['competitors'] ?? null ) ? $market['competitors'] : [];
	$ranked        = is_array( $market['ranked'] ?? null ) ? $market['ranked'] : [];
	?>
	<section class="nsc-v3-section nsc-v3-market-pulse" aria-labelledby="nsc-v3-market-title">
		<div class="nsc-v3-section__head">
			<div><p class="nsc-v3-eyebrow">Market Intelligence</p><h2 id="nsc-v3-market-title">Was außerhalb deiner Website passiert</h2><p>DataForSEO ergänzt GSC um Suchvolumen, Wettbewerber und externe Ranking-Sicht. Priorisiert wird erst nach dem Join mit deinen eigenen Daten.</p></div>
			<a class="nsc-v3-button" href="<?php echo esc_url( nexus_market_intelligence_admin_url() ); ?>">Markt öffnen</a>
		</div>
		<div class="nsc-v3-split nsc-v3-split--wide-left">
			<article class="nsc-v3-panel"><div class="nsc-v3-panel__head"><div><span class="nsc-v3-panel__icon"><span class="dashicons dashicons-chart-line"></span></span><div><strong>Marktchancen</strong><p>Externe Nachfrage + eigene Signale.</p></div></div></div><?php nexus_render_market_intelligence_opportunities( $opportunities, 4 ); ?></article>
			<article class="nsc-v3-panel"><div class="nsc-v3-panel__head"><div><span class="nsc-v3-panel__icon"><span class="dashicons dashicons-networking"></span></span><div><strong>Marktdruck</strong><p><?php echo esc_html( number_format_i18n( absint( $ranked['total_count'] ?? 0 ) ) ); ?> rankende Keywords · <?php echo esc_html( number_format_i18n( count( $competitors ) ) ); ?> Wettbewerber im Snapshot.</p></div></div></div><?php nexus_render_market_intelligence_competitors( $competitors, 4 ); ?></article>
		</div>
	</section>
	<?php
}
