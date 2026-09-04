<?php
/**
 * SEO Cockpit Research Intelligence V2 renderer.
 *
 * Keeps the CrUX implementation intact and layers Energy-Charts into the same
 * Research workspace without widening the original provider module.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace the V1 Research submenu callback with the multi-provider renderer.
 *
 * @return void
 */
function nexus_register_seo_cockpit_research_page_v2() {
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Research Intelligence',
		'Research',
		nexus_get_seo_cockpit_view_cap(),
		nexus_get_seo_cockpit_research_slug(),
		'nexus_render_seo_cockpit_research_page_v2'
	);
}
remove_action( 'admin_menu', 'nexus_register_seo_cockpit_research_page', 40 );
add_action( 'admin_menu', 'nexus_register_seo_cockpit_research_page_v2', 40 );

/**
 * Format a Research Intelligence numeric value.
 *
 * @param float|null $value Numeric value.
 * @param string     $unit Unit label.
 * @param int        $decimals Decimal places.
 * @return string
 */
function nexus_format_seo_cockpit_research_value( $value, $unit = '', $decimals = 1 ) {
	if ( null === $value ) {
		return '—';
	}

	$formatted = number_format_i18n( (float) $value, max( 0, absint( $decimals ) ) );

	return '' !== $unit ? $formatted . ' ' . $unit : $formatted;
}

/**
 * Render the Energy-Charts provider panel.
 *
 * @param array<string, mixed> $summary Energy-Charts summary.
 * @param bool                 $can_manage Whether current user may refresh data.
 * @return void
 */
function nexus_render_seo_cockpit_energy_charts_panel( $summary, $can_manage ) {
	$available   = ! empty( $summary['is_available'] );
	$installed   = is_array( $summary['solar_installed'] ?? null ) ? $summary['solar_installed'] : [];
	$target      = is_array( $summary['solar_target'] ?? null ) ? $summary['solar_target'] : [];
	$share       = is_array( $summary['solar_share_30d'] ?? null ) ? $summary['solar_share_30d'] : [];
	$price       = is_array( $summary['price_current'] ?? null ) ? $summary['price_current'] : [];
	$errors      = is_array( $summary['errors'] ?? null ) ? $summary['errors'] : [];
	$period      = trim( (string) ( $installed['period'] ?? '' ) );
	$period      = '' !== $period ? substr( $period, 0, 4 ) : '—';
	$target_period = trim( (string) ( $target['period'] ?? '' ) );
	$target_period = '' !== $target_period ? substr( $target_period, 0, 4 ) : '';
	$growth      = isset( $installed['growth_pct'] ) && is_numeric( $installed['growth_pct'] ) ? (float) $installed['growth_pct'] : null;
	$delta       = isset( $installed['delta'] ) && is_numeric( $installed['delta'] ) ? (float) $installed['delta'] : null;
	$target_now  = isset( $target['value'] ) && is_numeric( $target['value'] ) ? (float) $target['value'] : null;
	$share_now   = isset( $share['value'] ) && is_numeric( $share['value'] ) ? (float) $share['value'] : null;
	$share_prev  = isset( $share['previous'] ) && is_numeric( $share['previous'] ) ? (float) $share['previous'] : null;
	$share_delta = null !== $share_now && null !== $share_prev ? $share_now - $share_prev : null;
	$price_now   = isset( $price['value'] ) && is_numeric( $price['value'] ) ? (float) $price['value'] : null;
	$series_label = trim( (string) ( $installed['series_label'] ?? '' ) );
	?>
	<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--primary nexus-seo-cockpit__research-energy">
		<div class="nexus-seo-cockpit__panel-head">
			<div>
				<p class="nexus-seo-cockpit__eyebrow">Datenlayer 02 · Deutschland</p>
				<h2>Fraunhofer Energy-Charts</h2>
			</div>
			<div class="nexus-seo-cockpit__research-provider-actions">
				<span class="nexus-seo-cockpit__status-dot <?php echo $available ? 'is-connected' : 'is-warning'; ?>"><?php echo esc_html( $available ? 'API erreichbar' : 'Daten teilweise nicht verfügbar' ); ?></span>
				<?php if ( $can_manage ) : ?>
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_energy_charts_refresh' ) ); ?>">
						<?php wp_nonce_field( 'nexus_seo_cockpit_energy_charts_refresh' ); ?>
						<button type="submit" class="button">Energy-Charts neu laden</button>
					</form>
				<?php endif; ?>
			</div>
		</div>

		<p class="nexus-seo-cockpit__hint">Öffentliche Primärdaten für Solar-Dossiers und Marktanalysen. Installierter PV-Bestand und künftige Ausbauziele werden getrennt behandelt; zusätzlich zeigt der Layer den Solaranteil der letzten 30 Tage und den aktuellen Day-Ahead-Preis für DE-LU.</p>

		<div class="nexus-seo-cockpit__metrics nexus-seo-cockpit__research-energy-metrics">
			<article class="nexus-seo-cockpit__metric-card">
				<span class="nexus-seo-cockpit__metric-label">Installierte PV-Leistung · Stand <?php echo esc_html( $period ); ?></span>
				<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( nexus_format_seo_cockpit_research_value( $installed['value'] ?? null, (string) ( $installed['unit'] ?? '' ), 1 ) ); ?></strong>
				<span class="nexus-seo-cockpit__research-trend"><?php echo esc_html( '' !== $series_label ? $series_label . ' · Ist-Wert' : 'Ist-Wert ohne Ausbauplanung' ); ?></span>
				<?php if ( null !== $target_now ) : ?>
					<span class="nexus-seo-cockpit__research-trend is-neutral">EEG-Ausbauziel<?php echo '' !== $target_period ? ' ' . esc_html( $target_period ) : ''; ?>: <?php echo esc_html( nexus_format_seo_cockpit_research_value( $target_now, (string) ( $target['unit'] ?? '' ), 1 ) ); ?></span>
				<?php endif; ?>
			</article>

			<article class="nexus-seo-cockpit__metric-card">
				<span class="nexus-seo-cockpit__metric-label">PV-Veränderung · vorheriger Ist-Jahreswert</span>
				<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( null !== $growth ? ( ( $growth > 0 ? '+' : '' ) . number_format_i18n( $growth, 1 ) . ' %' ) : '—' ); ?></strong>
				<?php if ( null !== $delta ) : ?>
					<span class="nexus-seo-cockpit__research-trend <?php echo esc_attr( $delta >= 0 ? 'is-positive' : 'is-negative' ); ?>"><?php echo esc_html( ( $delta > 0 ? '+' : '' ) . nexus_format_seo_cockpit_research_value( $delta, (string) ( $installed['unit'] ?? '' ), 1 ) ); ?></span>
				<?php endif; ?>
			</article>

			<article class="nexus-seo-cockpit__metric-card">
				<span class="nexus-seo-cockpit__metric-label">Solaranteil · letzte 30 Tage</span>
				<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( nexus_format_seo_cockpit_research_value( $share_now, '%', 1 ) ); ?></strong>
				<?php if ( null !== $share_delta ) : ?>
					<span class="nexus-seo-cockpit__research-trend <?php echo esc_attr( $share_delta >= 0 ? 'is-positive' : 'is-negative' ); ?>"><?php echo esc_html( ( $share_delta > 0 ? '+' : '' ) . number_format_i18n( $share_delta, 1 ) . ' %-Punkte vs. vorige 30 Tage' ); ?></span>
				<?php elseif ( null !== $share_now ) : ?>
					<span class="nexus-seo-cockpit__research-trend">noch kein vollständiges Vergleichsfenster</span>
				<?php endif; ?>
			</article>

			<article class="nexus-seo-cockpit__metric-card">
				<span class="nexus-seo-cockpit__metric-label">Day-Ahead-Preis · DE-LU</span>
				<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( nexus_format_seo_cockpit_research_value( $price_now, (string) ( $price['unit'] ?? '' ), 2 ) ); ?></strong>
				<span class="nexus-seo-cockpit__research-trend">aktuelles Marktintervall · keine Bewertung als gut/schlecht</span>
			</article>
		</div>

		<div class="nexus-seo-cockpit__research-source-note">
			<strong>Quelle:</strong> Fraunhofer ISE · Energy-Charts.info
			<?php if ( '' !== (string) ( $summary['license'] ?? '' ) ) : ?>
				<span>· <?php echo esc_html( (string) $summary['license'] ); ?></span>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $errors ) ) : ?>
			<details class="nexus-seo-cockpit__research-errors">
				<summary>Teilweise fehlende Provider-Daten</summary>
				<ul>
					<?php foreach ( $errors as $key => $message ) : ?>
						<li><strong><?php echo esc_html( (string) $key ); ?>:</strong> <?php echo esc_html( (string) $message ); ?></li>
					<?php endforeach; ?>
				</ul>
			</details>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Render the multi-provider Research Intelligence page.
 *
 * @return void
 */
function nexus_render_seo_cockpit_research_page_v2() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	$api_key      = nexus_get_seo_cockpit_crux_api_key();
	$origin       = nexus_get_seo_cockpit_crux_origin();
	$can_manage   = nexus_current_user_can_manage_seo_cockpit();
	$uses_const   = nexus_seo_cockpit_crux_uses_constant();
	$notice       = isset( $_GET['research_notice'] ) ? sanitize_key( (string) wp_unslash( $_GET['research_notice'] ) ) : '';
	$phone        = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'PHONE', false ) : new WP_Error( 'nexus_crux_missing_key', 'CrUX ist noch nicht konfiguriert.' );
	$phone_hist   = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'PHONE', true ) : $phone;
	$desktop      = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'DESKTOP', false ) : $phone;
	$desktop_hist = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'DESKTOP', true ) : $phone;
	$energy       = function_exists( 'nexus_get_seo_cockpit_energy_charts_summary' ) ? nexus_get_seo_cockpit_energy_charts_summary() : [ 'is_available' => false ];
	?>
	<div class="wrap nexus-seo-cockpit nexus-seo-cockpit__research">
		<p class="nexus-seo-cockpit__eyebrow">Research Intelligence</p>
		<h1>Primärdaten statt Bauchgefühl</h1>

		<?php if ( 'saved' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p>Research-Einstellungen gespeichert.</p></div>
		<?php elseif ( 'refresh' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p>CrUX-Cache geleert. Die Ansicht lädt frische Felddaten.</p></div>
		<?php elseif ( 'energy_refresh' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p>Energy-Charts-Cache geleert. Die Ansicht lädt frische Marktdaten.</p></div>
		<?php elseif ( 'constant' === $notice ) : ?>
			<div class="notice notice-info is-dismissible"><p>Der CrUX-Key kommt aus <code>NEXUS_CRUX_API_KEY</code> und wird deshalb hier nicht überschrieben.</p></div>
		<?php endif; ?>

		<div class="nexus-seo-cockpit__toolbar">
			<div class="nexus-seo-cockpit__toolbar-meta">
				<span class="nexus-seo-cockpit__status-dot <?php echo '' !== $api_key ? 'is-connected' : 'is-warning'; ?>"><?php echo esc_html( '' !== $api_key ? 'CrUX konfiguriert' : 'CrUX-Key fehlt' ); ?></span>
				<span class="nexus-seo-cockpit__status-dot <?php echo ! empty( $energy['is_available'] ) ? 'is-connected' : 'is-warning'; ?>"><?php echo esc_html( ! empty( $energy['is_available'] ) ? 'Energy-Charts aktiv' : 'Energy-Charts prüfen' ); ?></span>
				<span><strong>Origin:</strong> <code><?php echo esc_html( $origin ?: 'nicht ermittelbar' ); ?></code></span>
			</div>
			<?php if ( $can_manage && '' !== $api_key ) : ?>
				<div class="nexus-seo-cockpit__toolbar-actions">
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_research_refresh' ) ); ?>">
						<?php wp_nonce_field( 'nexus_seo_cockpit_research_refresh' ); ?>
						<button type="submit" class="button">CrUX neu laden</button>
					</form>
				</div>
			<?php endif; ?>
		</div>

		<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--primary nexus-seo-cockpit__research-intro">
			<div class="nexus-seo-cockpit__panel-head">
				<div>
					<p class="nexus-seo-cockpit__eyebrow">Datenlayer 01</p>
					<h2>Chrome UX Report</h2>
				</div>
				<span class="nexus-seo-cockpit__chip">echte Chrome-Felddaten</span>
			</div>
			<p class="nexus-seo-cockpit__hint">Origin-Level-Daten für Mobil und Desktop. Angezeigt werden LCP, INP, CLS und TTFB am 75. Perzentil sowie die Richtung des verfügbaren CrUX-Verlaufs.</p>

			<?php if ( $can_manage ) : ?>
				<form class="nexus-seo-cockpit__research-key-form" method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_research_save' ) ); ?>">
					<?php wp_nonce_field( 'nexus_seo_cockpit_research_save' ); ?>
					<?php if ( $uses_const ) : ?>
						<p class="nexus-seo-cockpit__status is-positive">API-Key kommt aus <code>NEXUS_CRUX_API_KEY</code> in der Runtime-Konfiguration.</p>
					<?php else : ?>
						<label for="nexus-crux-api-key"><strong>CrUX API-Key</strong></label>
						<div class="nexus-seo-cockpit__research-key-row">
							<input id="nexus-crux-api-key" type="password" name="crux_api_key" value="" autocomplete="new-password" placeholder="<?php echo '' !== $api_key ? esc_attr( 'Gespeichert – leer lassen zum Beibehalten' ) : esc_attr( 'Google Cloud API-Key' ); ?>">
							<button type="submit" class="button button-primary">Speichern</button>
							<?php if ( '' !== $api_key ) : ?><button type="submit" class="button" name="clear_crux_api_key" value="1">Key entfernen</button><?php endif; ?>
						</div>
						<p class="description">Der Key wird nicht im Repo gespeichert. Alternativ <code>define( 'NEXUS_CRUX_API_KEY', '…' );</code> in der Runtime-Konfiguration setzen.</p>
					<?php endif; ?>
				</form>
			<?php endif; ?>
		</section>

		<?php if ( '' !== $api_key ) : ?>
			<div class="nexus-seo-cockpit__research-stack">
				<?php nexus_render_seo_cockpit_crux_metric_group( 'Mobil', $phone, $phone_hist ); ?>
				<?php nexus_render_seo_cockpit_crux_metric_group( 'Desktop', $desktop, $desktop_hist ); ?>
			</div>
		<?php else : ?>
			<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--setup">
				<p class="nexus-seo-cockpit__eyebrow">Einmalige Einrichtung</p>
				<h2>CrUX API-Key hinterlegen</h2>
				<ol class="nexus-seo-cockpit__steps">
					<li>Im bestehenden Google-Cloud-Projekt die <strong>Chrome UX Report API</strong> aktivieren.</li>
					<li>Einen API-Key erstellen beziehungsweise einen vorhandenen Key dafür freigeben.</li>
					<li>Den Key oben speichern oder als <code>NEXUS_CRUX_API_KEY</code> in der Runtime-Konfiguration setzen.</li>
				</ol>
			</section>
		<?php endif; ?>

		<?php nexus_render_seo_cockpit_energy_charts_panel( $energy, $can_manage ); ?>

		<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__research-providers">
			<div class="nexus-seo-cockpit__panel-head">
				<div><p class="nexus-seo-cockpit__eyebrow">Provider-Roadmap</p><h2>Der Research-Layer bleibt modular</h2></div>
			</div>
			<div class="nexus-seo-cockpit__research-provider-grid">
				<article><strong>CrUX</strong><span class="nexus-seo-cockpit__status is-positive">angebunden</span><p>Core Web Vitals und TTFB aus realen Chrome-Nutzungsdaten.</p></article>
				<article><strong>Energy-Charts</strong><span class="nexus-seo-cockpit__status is-positive">angebunden</span><p>PV-Leistung, Solaranteil und DE-LU-Day-Ahead-Preis aus der Fraunhofer-ISE-API.</p></article>
				<article><strong>Destatis GENESIS</strong><span class="nexus-seo-cockpit__status is-neutral">noch nicht angebunden</span><p>Gebäude-, Unternehmens- und Regionaldaten für eigene Marktanalysen.</p></article>
				<article><strong>Eurostat</strong><span class="nexus-seo-cockpit__status is-neutral">noch nicht angebunden</span><p>EU-Vergleiche zu Energie, Gebäuden und wirtschaftlichen Indikatoren.</p></article>
			</div>
		</section>
	</div>
	<?php
}
