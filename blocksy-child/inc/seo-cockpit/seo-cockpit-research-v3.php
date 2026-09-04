<?php
/**
 * SEO Cockpit Research Intelligence V3 renderer.
 *
 * Adds Destatis GENESIS and Eurostat data to the CrUX and Energy-Charts
 * research workspace while keeping provider logic in separate modules.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace the V2 Research submenu callback with the multi-provider renderer.
 *
 * @return void
 */
function nexus_register_seo_cockpit_research_page_v3() {
	add_submenu_page(
		nexus_get_seo_cockpit_menu_slug(),
		'Research Intelligence',
		'Research',
		nexus_get_seo_cockpit_view_cap(),
		nexus_get_seo_cockpit_research_slug(),
		'nexus_render_seo_cockpit_research_page_v3'
	);
}
remove_action( 'admin_menu', 'nexus_register_seo_cockpit_research_page_v2', 40 );
add_action( 'admin_menu', 'nexus_register_seo_cockpit_research_page_v3', 40 );

/**
 * Convert CrUX's expected 404/no-data response into an admin-facing state.
 *
 * @param array<string, mixed>|WP_Error $result CrUX result.
 * @return array<string, mixed>|WP_Error
 */
function nexus_normalize_seo_cockpit_crux_display_result( $result ) {
	if ( ! is_wp_error( $result ) ) {
		return $result;
	}

	$message = (string) $result->get_error_message();
	if ( 'nexus_crux_http' === $result->get_error_code() && false !== stripos( $message, 'HTTP 404' ) ) {
		return new WP_Error(
			'nexus_crux_no_data',
			'Für diese Origin liegen aktuell noch keine veröffentlichten CrUX-Felddaten vor. Das ist typischerweise der Fall, wenn die anonymisierte Chrome-Stichprobe noch nicht ausreicht.'
		);
	}

	return $result;
}

/**
 * Render one CrUX form factor with a neutral empty-sample state.
 *
 * @param string                        $label Display label.
 * @param array<string, mixed>|WP_Error $current Current CrUX response.
 * @param array<string, mixed>|WP_Error $history CrUX history response.
 * @return void
 */
function nexus_render_seo_cockpit_crux_metric_group_v3( $label, $current, $history ) {
	$current = nexus_normalize_seo_cockpit_crux_display_result( $current );
	$history = nexus_normalize_seo_cockpit_crux_display_result( $history );

	if ( is_wp_error( $current ) && 'nexus_crux_no_data' === $current->get_error_code() ) {
		?>
		<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__research-metrics">
			<div class="nexus-seo-cockpit__panel-head">
				<div>
					<p class="nexus-seo-cockpit__eyebrow">CrUX · <?php echo esc_html( $label ); ?></p>
					<h2>Noch keine ausreichende CrUX-Stichprobe</h2>
				</div>
				<span class="nexus-seo-cockpit__chip">Stand —</span>
			</div>
			<p class="notice notice-info inline"><?php echo esc_html( $current->get_error_message() ); ?></p>
		</section>
		<?php
		return;
	}

	nexus_render_seo_cockpit_crux_metric_group( $label, $current, $history );
}

/**
 * Render the Destatis GENESIS provider panel.
 *
 * @param array<string, mixed> $summary Destatis summary.
 * @param bool                 $can_manage Whether the current user may manage credentials.
 * @return void
 */
function nexus_render_seo_cockpit_destatis_panel( $summary, $can_manage ) {
	$token      = function_exists( 'nexus_get_seo_cockpit_destatis_api_token' ) ? nexus_get_seo_cockpit_destatis_api_token() : '';
	$uses_const = function_exists( 'nexus_seo_cockpit_destatis_uses_constant' ) && nexus_seo_cockpit_destatis_uses_constant();
	$available  = ! empty( $summary['is_available'] );
	$de         = is_array( $summary['de'] ?? null ) ? $summary['de'] : [];
	$ni         = is_array( $summary['ni'] ?? null ) ? $summary['ni'] : [];
	$errors     = is_array( $summary['errors'] ?? null ) ? $summary['errors'] : [];
	$tables     = is_array( $summary['tables'] ?? null ) ? $summary['tables'] : [];
	$year_de    = isset( $de['year'] ) && is_numeric( $de['year'] ) ? (int) $de['year'] : null;
	$year_ni    = isset( $ni['year'] ) && is_numeric( $ni['year'] ) ? (int) $ni['year'] : null;
	?>
	<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__panel--primary nexus-seo-cockpit__research-destatis">
		<div class="nexus-seo-cockpit__panel-head">
			<div>
				<p class="nexus-seo-cockpit__eyebrow">Datenlayer 03 · Gebäudebestand</p>
				<h2>Destatis GENESIS</h2>
			</div>
			<div class="nexus-seo-cockpit__research-provider-actions">
				<span class="nexus-seo-cockpit__status-dot <?php echo $available ? 'is-connected' : 'is-warning'; ?>">
					<?php echo esc_html( '' === $token ? 'API-Token fehlt' : ( $available ? 'GENESIS aktiv' : 'GENESIS prüfen' ) ); ?>
				</span>
				<?php if ( $can_manage && '' !== $token ) : ?>
					<form method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_destatis_refresh' ) ); ?>">
						<?php wp_nonce_field( 'nexus_seo_cockpit_destatis_refresh' ); ?>
						<button type="submit" class="button">Destatis neu laden</button>
					</form>
				<?php endif; ?>
			</div>
		</div>

		<p class="nexus-seo-cockpit__hint">Dieser Layer beschreibt die Struktur des Wohngebäudebestands. Er ist eine belastbare Basis für Marktanalysen, aber bewusst kein erfundener "Wärmepumpen-Score" und keine Aussage über Eigentümerstatus.</p>

		<?php if ( $can_manage ) : ?>
			<form class="nexus-seo-cockpit__research-key-form" method="post" action="<?php echo esc_url( nexus_get_seo_cockpit_admin_action_url( 'nexus_seo_cockpit_destatis_save' ) ); ?>">
				<?php wp_nonce_field( 'nexus_seo_cockpit_destatis_save' ); ?>
				<?php if ( $uses_const ) : ?>
					<p class="nexus-seo-cockpit__status is-positive">API-Token kommt aus <code>NEXUS_DESTATIS_API_TOKEN</code> in der Runtime-Konfiguration.</p>
				<?php else : ?>
					<label for="nexus-destatis-api-token"><strong>GENESIS API-Token</strong></label>
					<div class="nexus-seo-cockpit__research-key-row">
						<input id="nexus-destatis-api-token" type="password" name="destatis_api_token" value="" autocomplete="new-password" placeholder="<?php echo '' !== $token ? esc_attr( 'Gespeichert – leer lassen zum Beibehalten' ) : esc_attr( 'Kostenloser GENESIS API-Token' ); ?>">
						<button type="submit" class="button button-primary">Speichern</button>
						<?php if ( '' !== $token ) : ?><button type="submit" class="button" name="clear_destatis_api_token" value="1">Token entfernen</button><?php endif; ?>
					</div>
					<p class="description">Kostenloser GENESIS-Account erforderlich. Der Token wird nicht im Repo gespeichert. Alternativ <code>define( 'NEXUS_DESTATIS_API_TOKEN', '…' );</code> in der Runtime-Konfiguration setzen.</p>
				<?php endif; ?>
			</form>
		<?php endif; ?>

		<?php if ( '' !== $token && $available ) : ?>
			<div class="nexus-seo-cockpit__metrics nexus-seo-cockpit__research-energy-metrics">
				<article class="nexus-seo-cockpit__metric-card">
					<span class="nexus-seo-cockpit__metric-label">Wohngebäude Deutschland<?php echo null !== $year_de ? ' · ' . esc_html( (string) $year_de ) : ''; ?></span>
					<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( isset( $de['total'] ) && is_numeric( $de['total'] ) ? number_format_i18n( (float) $de['total'], 0 ) : '—' ); ?></strong>
					<span class="nexus-seo-cockpit__research-trend">Wohngebäude insgesamt</span>
				</article>

				<article class="nexus-seo-cockpit__metric-card">
					<span class="nexus-seo-cockpit__metric-label">Deutschland · 1–2 Wohnungen</span>
					<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( isset( $de['one_two_share'] ) && is_numeric( $de['one_two_share'] ) ? number_format_i18n( (float) $de['one_two_share'], 1 ) . ' %' : '—' ); ?></strong>
					<span class="nexus-seo-cockpit__research-trend"><?php echo esc_html( isset( $de['one_two'] ) && is_numeric( $de['one_two'] ) ? number_format_i18n( (float) $de['one_two'], 0 ) . ' Gebäude' : 'Anteil am Wohngebäudebestand' ); ?></span>
				</article>

				<article class="nexus-seo-cockpit__metric-card">
					<span class="nexus-seo-cockpit__metric-label">Wohngebäude Niedersachsen<?php echo null !== $year_ni ? ' · ' . esc_html( (string) $year_ni ) : ''; ?></span>
					<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( isset( $ni['total'] ) && is_numeric( $ni['total'] ) ? number_format_i18n( (float) $ni['total'], 0 ) : '—' ); ?></strong>
					<span class="nexus-seo-cockpit__research-trend">regionaler Strukturwert für deinen Kernmarkt</span>
				</article>

				<article class="nexus-seo-cockpit__metric-card">
					<span class="nexus-seo-cockpit__metric-label">Niedersachsen · 1–2 Wohnungen</span>
					<strong class="nexus-seo-cockpit__metric-value"><?php echo esc_html( isset( $ni['one_two_share'] ) && is_numeric( $ni['one_two_share'] ) ? number_format_i18n( (float) $ni['one_two_share'], 1 ) . ' %' : '—' ); ?></strong>
					<span class="nexus-seo-cockpit__research-trend"><?php echo esc_html( isset( $ni['one_two'] ) && is_numeric( $ni['one_two'] ) ? number_format_i18n( (float) $ni['one_two'], 0 ) . ' Gebäude' : 'Anteil am Wohngebäudebestand' ); ?></span>
				</article>
			</div>
		<?php elseif ( '' === $token ) : ?>
			<div class="nexus-seo-cockpit__research-setup-inline">
				<strong>Einmalige Einrichtung:</strong> kostenlosen GENESIS-Account anlegen, im API-Menü den Token kopieren und oben hinterlegen.
			</div>
		<?php endif; ?>

		<div class="nexus-seo-cockpit__research-source-note">
			<strong>Quelle:</strong> Statistisches Bundesamt (Destatis) · GENESIS-Online
			<?php if ( '' !== (string) ( $summary['copyright'] ?? '' ) ) : ?><span>· <?php echo esc_html( (string) $summary['copyright'] ); ?></span><?php endif; ?>
			<?php if ( ! empty( $tables ) ) : ?><span>· Tabellen <?php echo esc_html( implode( ', ', array_values( $tables ) ) ); ?></span><?php endif; ?>
		</div>

		<?php if ( ! empty( $errors ) ) : ?>
			<details class="nexus-seo-cockpit__research-errors">
				<summary>Teilweise fehlende Destatis-Daten</summary>
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
 * Render the Research Intelligence page.
 *
 * @return void
 */
function nexus_render_seo_cockpit_research_page_v3() {
	if ( ! nexus_current_user_can_view_seo_cockpit() ) {
		wp_die( 'Nicht erlaubt.' );
	}

	$api_key        = nexus_get_seo_cockpit_crux_api_key();
	$origin         = nexus_get_seo_cockpit_crux_origin();
	$can_manage     = nexus_current_user_can_manage_seo_cockpit();
	$uses_const     = nexus_seo_cockpit_crux_uses_constant();
	$notice         = isset( $_GET['research_notice'] ) ? sanitize_key( (string) wp_unslash( $_GET['research_notice'] ) ) : '';
	$phone          = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'PHONE', false ) : new WP_Error( 'nexus_crux_missing_key', 'CrUX ist noch nicht konfiguriert.' );
	$phone_hist     = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'PHONE', true ) : $phone;
	$desktop        = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'DESKTOP', false ) : $phone;
	$desktop_hist   = '' !== $api_key ? nexus_get_seo_cockpit_crux_record( 'DESKTOP', true ) : $phone;
	$energy         = function_exists( 'nexus_get_seo_cockpit_energy_charts_summary' ) ? nexus_get_seo_cockpit_energy_charts_summary() : [ 'is_available' => false ];
	$destatis_token = function_exists( 'nexus_get_seo_cockpit_destatis_api_token' ) ? nexus_get_seo_cockpit_destatis_api_token() : '';
	$destatis       = '' !== $destatis_token && function_exists( 'nexus_get_seo_cockpit_destatis_summary' ) ? nexus_get_seo_cockpit_destatis_summary() : [ 'is_available' => false, 'errors' => [] ];
	$eurostat       = function_exists( 'nexus_get_seo_cockpit_eurostat_summary' ) ? nexus_get_seo_cockpit_eurostat_summary() : [ 'is_available' => false, 'errors' => [] ];
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
		<?php elseif ( 'destatis_saved' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p>Destatis-Einstellungen gespeichert.</p></div>
		<?php elseif ( 'destatis_refresh' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p>Destatis-Cache geleert. Die Ansicht lädt frische Gebäudedaten.</p></div>
		<?php elseif ( 'eurostat_refresh' === $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p>Eurostat-Cache geleert. Die Ansicht lädt frische EU-Vergleichsdaten.</p></div>
		<?php elseif ( 'destatis_constant' === $notice ) : ?>
			<div class="notice notice-info is-dismissible"><p>Der Destatis-Token kommt aus <code>NEXUS_DESTATIS_API_TOKEN</code> und wird deshalb hier nicht überschrieben.</p></div>
		<?php elseif ( 'constant' === $notice ) : ?>
			<div class="notice notice-info is-dismissible"><p>Der CrUX-Key kommt aus <code>NEXUS_CRUX_API_KEY</code> und wird deshalb hier nicht überschrieben.</p></div>
		<?php endif; ?>

		<div class="nexus-seo-cockpit__toolbar">
			<div class="nexus-seo-cockpit__toolbar-meta">
				<span class="nexus-seo-cockpit__status-dot <?php echo '' !== $api_key ? 'is-connected' : 'is-warning'; ?>"><?php echo esc_html( '' !== $api_key ? 'CrUX konfiguriert' : 'CrUX-Key fehlt' ); ?></span>
				<span class="nexus-seo-cockpit__status-dot <?php echo ! empty( $energy['is_available'] ) ? 'is-connected' : 'is-warning'; ?>"><?php echo esc_html( ! empty( $energy['is_available'] ) ? 'Energy-Charts aktiv' : 'Energy-Charts prüfen' ); ?></span>
				<span class="nexus-seo-cockpit__status-dot <?php echo ! empty( $destatis['is_available'] ) ? 'is-connected' : 'is-warning'; ?>"><?php echo esc_html( '' === $destatis_token ? 'Destatis-Token fehlt' : ( ! empty( $destatis['is_available'] ) ? 'Destatis aktiv' : 'Destatis prüfen' ) ); ?></span>
				<span class="nexus-seo-cockpit__status-dot <?php echo ! empty( $eurostat['is_available'] ) ? 'is-connected' : 'is-warning'; ?>"><?php echo esc_html( ! empty( $eurostat['is_available'] ) ? 'Eurostat aktiv' : 'Eurostat prüfen' ); ?></span>
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
				<?php nexus_render_seo_cockpit_crux_metric_group_v3( 'Mobil', $phone, $phone_hist ); ?>
				<?php nexus_render_seo_cockpit_crux_metric_group_v3( 'Desktop', $desktop, $desktop_hist ); ?>
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
		<?php nexus_render_seo_cockpit_destatis_panel( $destatis, $can_manage ); ?>
		<?php if ( function_exists( 'nexus_render_seo_cockpit_eurostat_panel' ) ) : ?>
			<?php nexus_render_seo_cockpit_eurostat_panel( $eurostat, $can_manage ); ?>
		<?php endif; ?>

		<section class="nexus-seo-cockpit__panel nexus-seo-cockpit__research-providers">
			<div class="nexus-seo-cockpit__panel-head">
				<div><p class="nexus-seo-cockpit__eyebrow">Provider-Roadmap</p><h2>Der Research-Layer bleibt modular</h2></div>
			</div>
			<div class="nexus-seo-cockpit__research-provider-grid">
				<article><strong>CrUX</strong><span class="nexus-seo-cockpit__status is-positive">angebunden</span><p>Core Web Vitals und TTFB aus realen Chrome-Nutzungsdaten.</p></article>
				<article><strong>Energy-Charts</strong><span class="nexus-seo-cockpit__status is-positive">angebunden</span><p>PV-Leistung, Solaranteil und DE-LU-Day-Ahead-Preis aus der Fraunhofer-ISE-API.</p></article>
				<article><strong>Destatis GENESIS</strong><span class="nexus-seo-cockpit__status is-positive">angebunden</span><p>Wohngebäudebestand Deutschland und Niedersachsen, inklusive Struktur nach Zahl der Wohnungen.</p></article>
				<article><strong>Eurostat</strong><span class="nexus-seo-cockpit__status is-positive">angebunden</span><p>Deutschland-vs.-EU27-Vergleiche zu erneuerbarer Energie und erneuerbarem Strom.</p></article>
			</div>
		</section>
	</div>
	<?php
}
