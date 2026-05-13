<?php
/**
 * Versioned tools hub shell.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$primary_urls  = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$audit_url     = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/system-diagnose/' );
$tools_cards   = function_exists( 'nexus_get_tools_hub_items' ) ? nexus_get_tools_hub_items() : [];
?>

<main class="tools-main tools-main--shell">
	<section class="tools-hero nx-reveal" aria-labelledby="tools-hero-title">
		<div class="tools-hero__copy">
			<span class="tools-kicker">Kostenlose Diagnose-Einstiege</span>
			<h1 id="tools-hero-title" class="tools-hero__title">Kostenlose Tools, wenn Klarheit wichtiger ist als noch ein Schnellrechner.</h1>
			<p class="tools-hero__subtitle">
				Diese Seite bündelt die sinnvollsten Einstiege für Performance, Systemverständnis und Nachfrage-Probleme:
				repo-versioniert, ohne tote Enden und mit klarer nächster Handlung.
			</p>

			<div class="tools-hero__actions">
				<a href="<?php echo esc_url( $audit_url ); ?>" class="tools-btn tools-btn--primary" data-track-action="cta_tools_hero_audit" data-track-category="lead_gen"><?php echo esc_html( nexus_get_audit_cta_label() ); ?></a>
			</div>

			<div class="tools-hero__meta" aria-label="Arbeitsprinzip">
				<span>diagnose statt raten</span>
				<span>versionierte Zielseiten</span>
				<span>kein Editor-Drift</span>
			</div>
		</div>

		<aside class="tools-hero__panel">
			<span class="tools-panel__eyebrow">Hinweis zum alten ROI-Rechner</span>
			<h2 class="tools-panel__title">Der ROI-Intent bleibt. Die isolierte Rechner-Logik nicht.</h2>
			<p class="tools-panel__copy">
				Wenn Sie hier wegen des alten ROI-Rechners gelandet sind: diese Einzeltool-Logik wird nicht mehr separat gepflegt.
				Wirtschaftlichkeit wird jetzt zusammen mit Nachfrage, Conversion und Tracking eingeordnet, damit keine falschen Prioritäten aus einem isolierten Zahlenwert entstehen.
			</p>
			<a href="<?php echo esc_url( $audit_url ); ?>" class="tools-text-link" data-track-action="cta_tools_legacy_roi" data-track-category="lead_gen">ROI-Frage im Audit einordnen</a>
		</aside>
	</section>

	<section class="tools-section" aria-labelledby="tools-grid-title">
		<div class="tools-section__head nx-reveal">
			<span class="tools-kicker">Kuratiert statt gesammelt</span>
			<h2 id="tools-grid-title" class="tools-section__title">Vier belastbare Einstiege statt einer unverbundenen Tool-Sammlung.</h2>
			<p class="tools-section__intro">
				Jeder Einstieg führt in einen klaren Kontext: Diagnose, Systemverständnis, Systemlandkarte oder Performance.
				Keine Sackgassen, keine toten Legacy-Tools und kein Editor-Markup als Source of Truth.
			</p>
		</div>

		<div class="tools-grid">
			<?php foreach ( $tools_cards as $index => $card ) : ?>
				<article class="tools-card nx-reveal" style="--tools-delay: <?php echo esc_attr( (string) $index ); ?>;" aria-labelledby="<?php echo esc_attr( 'tools-card-' . $index ); ?>">
					<div class="tools-card__top">
						<span class="tools-card__eyebrow"><?php echo esc_html( $card['eyebrow'] ); ?></span>
						<h3 id="<?php echo esc_attr( 'tools-card-' . $index ); ?>" class="tools-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="tools-card__desc"><?php echo esc_html( $card['description'] ); ?></p>
					</div>

					<dl class="tools-card__facts">
						<div class="tools-card__fact">
							<dt>Wofür</dt>
							<dd><?php echo esc_html( $card['use_case'] ); ?></dd>
						</div>
						<div class="tools-card__fact">
							<dt>Liefert</dt>
							<dd><?php echo esc_html( $card['outcome'] ); ?></dd>
						</div>
					</dl>

					<div class="tools-card__actions">
						<a href="<?php echo esc_url( $card['url'] ); ?>" class="tools-card__cta" data-track-action="<?php echo esc_attr( 'cta_tools_card_' . sanitize_title( (string) $card['title'] ) ); ?>" data-track-category="navigation"><?php echo esc_html( $card['cta_label'] ); ?></a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="tools-bridge nx-reveal" aria-labelledby="tools-bridge-title">
		<div class="tools-bridge__copy">
			<span class="tools-kicker">Wenn Sie nicht raten wollen</span>
			<h2 id="tools-bridge-title" class="tools-section__title">Nicht sicher, welcher Einstieg zuerst Sinn ergibt?</h2>
			<p class="tools-section__intro">
				Dann starten Sie nicht mit dem falschen Tool. Die System-Diagnose ordnet zuerst ein, ob das Problem eher in Botschaft,
				Proof, Performance, Tracking oder im nächsten Schritt liegt.
			</p>
		</div>

		<div class="tools-bridge__actions">
			<a href="<?php echo esc_url( $audit_url ); ?>" class="tools-btn tools-btn--primary" data-track-action="cta_tools_footer_audit" data-track-category="lead_gen">Zur System-Diagnose</a>
		</div>
	</section>
</main>
