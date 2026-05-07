<?php
/**
 * System-Diagnose shortcode for the instant analysis flow.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the instant System-Diagnose app.
 *
 * @return string
 */
function cja_audit_shortcode() {
	$css_path            = get_stylesheet_directory() . '/assets/css/cja-audit.css';
	$js_path             = get_stylesheet_directory() . '/assets/js/cja-audit.js';
	$request_url         = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/anfrage-system-analyse/' );
	$legacy_webhook_url  = (string) apply_filters( 'cja_audit_webhook_url', 'https://n8n.hasimuener.de/webhook/cja-analyze' );
	$default_start_url   = (string) preg_replace( '#/webhook/[^/?]+$#', '/webhook/audit', $legacy_webhook_url );
	$default_status_url  = (string) preg_replace( '#/webhook/[^/?]+$#', '/webhook/audit-status', $legacy_webhook_url );
	$webhook_start_url   = (string) apply_filters( 'cja_audit_webhook_start_url', $default_start_url );
	$webhook_status_url  = (string) apply_filters( 'cja_audit_webhook_status_url', $default_status_url );
	$css_version         = file_exists( $css_path ) ? (string) filemtime( $css_path ) : '1.0.0';
	$js_version          = file_exists( $js_path ) ? (string) filemtime( $js_path ) : '1.0.0';

	if ( $default_start_url === $legacy_webhook_url ) {
		$webhook_start_url = (string) apply_filters( 'cja_audit_webhook_start_url', 'https://n8n.hasimuener.de/webhook/audit' );
	}

	if ( $default_status_url === $legacy_webhook_url ) {
		$webhook_status_url = (string) apply_filters( 'cja_audit_webhook_status_url', 'https://n8n.hasimuener.de/webhook/audit-status' );
	}

	wp_enqueue_style(
		'cja-audit',
		get_stylesheet_directory_uri() . '/assets/css/cja-audit.css',
		[ 'nexus-design-system' ],
		$css_version
	);

	wp_enqueue_script(
		'cja-audit',
		get_stylesheet_directory_uri() . '/assets/js/cja-audit.js',
		[],
		$js_version,
		true
	);

	wp_script_add_data( 'cja-audit', 'defer', true );

	wp_localize_script(
		'cja-audit',
		'cjaConfig',
		[
			'webhookStartUrl' => esc_url_raw( $webhook_start_url ),
			'webhookStatusUrl' => esc_url_raw( $webhook_status_url ),
			'legacyWebhookUrl' => esc_url_raw( $legacy_webhook_url ),
			'webhookUrl' => esc_url_raw( $legacy_webhook_url ),
			'pollInterval' => 4500,
			'pollTimeout' => 180000,
		]
	);

	ob_start();
	?>
	<div class="cja-wrapper" id="cja-app" data-track-section="growth_audit_instant" data-track-funnel-stage="audit_input">
		<span id="form" class="cja-anchor" aria-hidden="true"></span>

		<div class="cja-phase cja-hero" id="cja-input">
			<span class="cja-overline">60-Sekunden-Diagnose für Solar- und Wärmepumpen-Anbieter</span>
			<h1 class="cja-headline">In 60 Sekunden sehen Sie, wo Ihre Website Anfragen verliert.</h1>
			<p class="cja-subtitle">Die Diagnose zeigt direkt, wo Performance, Messbarkeit, Vertrauen, Anfragepfad und Vorqualifizierung qualifizierte Anfragen bremsen — und welche Hebel zuerst Wirkung versprechen.</p>

			<div class="cja-input-group">
				<label class="screen-reader-text" for="cja-url-input">Website-URL</label>
				<input type="text" id="cja-url-input" placeholder="ihre-domain.de" autocomplete="off" inputmode="url" autocapitalize="off" spellcheck="false" aria-describedby="cja-error">
				<button id="cja-submit" type="button" data-track-action="cja_start_analysis" data-track-category="lead_gen" data-track-section="growth_audit_input" data-track-funnel-stage="audit_input">Diagnose starten</button>
			</div>
			<p class="cja-input-help">Starten Sie mit Ihrer Startseite oder der wichtigsten Angebotsseite. Sie sehen direkt die größten Reibungen und priorisierten Hebel.</p>

			<div class="cja-trust-line" aria-label="Diagnose-Vertrauen">
				<span>Keine E-Mail nötig</span>
				<span>Fokus: Solar, Wärmepumpe, Speicher</span>
				<span>Priorisierte Hebel statt Tool-Score</span>
			</div>

			<div class="cja-preview" aria-label="Ergebnisvorschau">
				<div class="cja-preview-head">
					<p class="cja-preview-kicker">Sie sehen direkt</p>
					<h2 class="cja-preview-title">Kein generischer Score, sondern ein klarer Diagnose-Einstieg.</h2>
				</div>

				<div class="cja-preview-grid">
					<article class="cja-preview-item">
						<span class="cja-preview-index">01</span>
						<h3>Wo Anfragen verloren gehen</h3>
						<p>Versprechen, Proof, CTA und Reibung im Anfragepfad Ihrer Website.</p>
					</article>
					<article class="cja-preview-item">
						<span class="cja-preview-index">02</span>
						<h3>Welche Signale fehlen</h3>
						<p>Messbarkeit, SEO, Angebotsklarheit und Performance dort, wo Nachfrage messbar werden sollte.</p>
					</article>
					<article class="cja-preview-item">
						<span class="cja-preview-index">03</span>
						<h3>Was zuerst Sinn ergibt</h3>
						<p>Priorisierte Hebel statt Maßnahmenliste ohne Reihenfolge oder Kontext.</p>
					</article>
				</div>
			</div>

			<div class="cja-error" id="cja-error" aria-live="polite" hidden></div>
		</div>

		<div class="cja-phase cja-loading" id="cja-loading" hidden>
			<div class="cja-spinner" aria-hidden="true"></div>
			<div class="cja-loading-step" id="cja-loading-step" aria-live="polite">Website wird geladen...</div>
			<div class="cja-loading-url" id="cja-loading-url"></div>
			<div class="cja-progress-track" aria-hidden="true">
				<div class="cja-progress-bar" id="cja-progress-bar"></div>
			</div>
		</div>

		<div class="cja-phase cja-results" id="cja-results" hidden>
			<div class="cja-score-header" id="cja-score-header"></div>
			<div class="cja-modules" id="cja-modules"></div>
			<div class="cja-revenue-shell" id="cja-revenue"></div>

			<div class="cja-cta-section cja-reveal">
				<p class="cja-cta-kicker">Nächster Schritt</p>
				<h2>Wenn das nach Ihrer Situation klingt, folgt jetzt die persönliche Einordnung.</h2>
				<p>Kurz beschreiben, wo Ihr Anfrageprozess heute steht — Sie erhalten eine persönliche Einschätzung per E-Mail, ohne Pitch.</p>
				<div class="cja-cta-actions">
					<a href="<?php echo esc_url( $request_url ); ?>" class="cja-cta-button" data-track-action="cja_results_request" data-track-category="lead_gen" data-track-section="growth_audit_results" data-track-funnel-stage="audit_results">Einordnung anfordern</a>
				</div>
				<div class="cja-cta-meta">2 Minuten · persönliche Rückmeldung · kein Pflicht-Call</div>
			</div>

			<div class="cja-footer-line">System-Diagnose für Solar- und Wärmepumpen-Anbieter.</div>
		</div>
	</div>
	<?php

	return (string) ob_get_clean();
}

add_shortcode( 'cja_audit', 'cja_audit_shortcode' );
