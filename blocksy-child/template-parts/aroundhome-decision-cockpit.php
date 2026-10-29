<?php
/**
 * Aroundhome: Kosten für Handwerker — Entscheidungsseite.
 *
 * Aufbau nach Bauplan Oktober 2026, bewusst schlicht: Live-Rechnung mit
 * Beispielwerten zuerst, dann vier Vertragsfragen, dann FAQ, Quellen und
 * ein Abschluss. Die H1 und die Zwei-Satz-Antwort trägt der Redaktionskopf
 * (single-reader.php). Sofortkontakt-Brücke oben und Türen-Register im Fuß
 * bleiben im Leserlayout und werden hier nicht dupliziert.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$primary_urls       = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$analysis_url       = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/' );
$tco_url            = $primary_urls['solar_leads_tco'] ?? home_url( '/solar-leads-kaufen-alternative/#vergleich' );
$case_url           = $primary_urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$aroundhome_partner = 'https://www.aroundhome.de/partner-werden/';
$aroundhome_terms   = 'https://www.aroundhome.de/legal/';
$aroundhome_faqs    = function_exists( 'nexus_get_aroundhome_faq_items' ) ? nexus_get_aroundhome_faq_items() : [];
$case_cpl_before    = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' ) : '150 €';
$case_cpl_after     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '22 €' ) : '22 €';
$source_date        = '29. Oktober 2026';

$contract_items = [
	[
		'key'   => 'preis',
		'title' => 'Was kostet eine Anfrage — und wofür genau?',
		'text'  => 'Preis pro Produkt, und welches Ereignis die Gebühr auslöst.',
	],
	[
		'key'   => 'verteilung',
		'title' => 'Wie viele Betriebe bekommen dieselbe Anfrage?',
		'text'  => 'Aroundhome nennt bis zu drei. Was steht in Ihrem Vertrag?',
	],
	[
		'key'   => 'region',
		'title' => 'Welche Region und welche Projekte?',
		'text'  => 'Radius, Gewerk, Projektgröße, Ausschlüsse.',
	],
	[
		'key'   => 'reklamation',
		'title' => 'Was passiert mit unbrauchbaren Anfragen?',
		'text'  => 'Gründe, Frist und Gutschrift — schriftlich.',
	],
];
?>

<article
	class="hu-aroundhome hu-aroundhome--simple"
	id="article-content"
	data-provider-decision
	data-aroundhome-decision
	data-track-section="aroundhome_decision_page"
>
	<section class="hu-aroundhome__band hu-aroundhome__lead" id="kostenrechner" tabindex="-1" data-track-section="aroundhome_cpo">
		<div class="hu-aroundhome__inner">
			<h2 class="hu-aroundhome__lead-title">Aus 60 € pro Anfrage werden 850 € pro Auftrag.</h2>
			<p class="hu-aroundhome__lead-text">Ein Rechenbeispiel: Nicht jede Anfrage wird ein Auftrag, und jede kostet Vertriebszeit. Ersetzen Sie die Beispielwerte durch Ihre eigenen.</p>

			<?php if ( function_exists( 'hu_render_portal_cpo_calculator' ) ) : ?>
				<?php
				echo hu_render_portal_cpo_calculator( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in renderer.
					[
						'provider_label'   => 'Aroundhome',
						'tracking_prefix'  => 'aroundhome',
						'tracking_section' => 'aroundhome_cpo',
						'defaults'         => [
							'cpl'           => 60,
							'leads'         => 20,
							'close_rate'    => 10,
							'sales_minutes' => 30,
							'hourly_rate'   => 50,
						],
						'require_complete' => false,
						'heading'          => 'Was kostet Sie ein gewonnener Auftrag?',
						'description'      => 'Anfragekosten plus Vertriebszeit, geteilt durch die Aufträge, die wirklich zustande kommen.',
						'example_text'     => 'Beispielwerte, keine Aroundhome-Preise. Ihre Eingaben bleiben im Browser.',
						'cta_label'        => 'Mit meinen Zahlen einordnen lassen',
						'cta_note'         => 'Marktcheck: Wir prüfen Region, Projektwert und Vertriebskapazität und geben eine Grün-, Gelb- oder Rot-Einordnung.',
					]
				);
				?>
			<?php endif; ?>
			<noscript>
				<p class="hu-aroundhome__noscript">Ohne JavaScript bleibt das Beispiel stehen: 20 Anfragen × 60 € = 1.200 €, plus 10 Stunden Vertrieb à 50 € = 500 €. Bei 2 Aufträgen sind das 850 € pro Auftrag.</p>
			</noscript>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--paper" id="vertragscheck" tabindex="-1" data-track-section="aroundhome_contract">
		<div class="hu-aroundhome__inner">
			<div class="hu-aroundhome__section-head">
				<h2>Vier Fragen vor dem Vertrag</h2>
				<p>Haken Sie ab, was Ihnen schriftlich vorliegt. Es wird nichts gespeichert.</p>
			</div>

			<div class="hu-aroundhome__ledger" data-aroundhome-contract>
				<div class="hu-aroundhome__ledger-head">
					<strong data-contract-result aria-live="polite" aria-atomic="true">0 von <?php echo esc_html( (string) count( $contract_items ) ); ?> schriftlich geklärt</strong>
				</div>
				<fieldset>
					<legend class="screen-reader-text">Welche Punkte liegen Ihnen schriftlich vor?</legend>
					<ul class="hu-aroundhome__contract-list">
						<?php foreach ( $contract_items as $contract_item ) : ?>
							<?php $control_id = 'aroundhome-contract-' . $contract_item['key']; ?>
							<li>
								<input id="<?php echo esc_attr( $control_id ); ?>" type="checkbox" data-contract-check>
								<label for="<?php echo esc_attr( $control_id ); ?>">
									<span aria-hidden="true"></span>
									<strong><?php echo esc_html( $contract_item['title'] ); ?></strong>
									<small><?php echo esc_html( $contract_item['text'] ); ?></small>
								</label>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
				<p class="hu-aroundhome__ledger-status" data-contract-detail role="status" aria-live="polite" aria-atomic="true" data-track-action="complete_aroundhome_contract" data-track-category="engagement" data-track-section="aroundhome_contract">Offene Punkte machen jede Rechnung zur Schätzung.</p>
			</div>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--paper" id="faq" tabindex="-1" data-track-section="aroundhome_faq">
		<div class="hu-aroundhome__inner">
			<div class="hu-aroundhome__section-head">
				<h2>Häufige Fragen</h2>
			</div>

			<div class="hu-aroundhome__faq-list">
				<?php foreach ( $aroundhome_faqs as $faq_item ) : ?>
					<details id="faq-<?php echo esc_attr( $faq_item['key'] ); ?>" data-track-action="toggle_aroundhome_faq" data-track-category="engagement" data-track-section="aroundhome_faq">
						<summary><strong><?php echo esc_html( $faq_item['question'] ); ?></strong></summary>
						<div class="hu-aroundhome__faq-answer"><p><?php echo esc_html( $faq_item['answer'] ); ?></p></div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="hu-aroundhome__inner hu-aroundhome__method-wrap">
			<div class="hu-aroundhome__method" data-track-section="aroundhome_methodology">
				<h2>Quellen und Stand</h2>
				<ul>
					<li>Abrechnungsmodell, Verteilung und Laufzeit: <a href="<?php echo esc_url( $aroundhome_partner ); ?>" rel="noopener noreferrer" target="_blank" data-track-action="source_aroundhome_partner_method" data-track-category="external_link" data-track-section="aroundhome_methodology">Aroundhome-Partnerseite</a>, abgerufen am <?php echo esc_html( $source_date ); ?>.</li>
					<li>Vertragsmechanik: <a href="<?php echo esc_url( $aroundhome_terms ); ?>" rel="noopener noreferrer" target="_blank" data-track-action="source_aroundhome_terms" data-track-category="external_link" data-track-section="aroundhome_methodology">AGB für Partnerunternehmen</a>. Ihr konkreter Vertrag kann abweichen.</li>
					<li>Der Rechner nutzt nur Ihre Eingaben bzw. markierte Beispielwerte. Keine Rechtsberatung.</li>
				</ul>
			</div>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--ink hu-aroundhome__final" data-track-section="aroundhome_final">
		<div class="hu-aroundhome__inner hu-aroundhome__final-grid">
			<div>
				<h2>Weniger zahlen pro Auftrag?</h2>
				<p>Ein eigener Anfrageweg senkte in einem PV-Betrieb die Kosten pro Anfrage von <?php echo esc_html( $case_cpl_before ); ?> auf <?php echo esc_html( $case_cpl_after ); ?>. <a href="<?php echo esc_url( $case_url ); ?>" data-track-action="aroundhome_open_case" data-track-category="proof" data-track-section="aroundhome_final">Fall ansehen</a> · <a href="<?php echo esc_url( $tco_url ); ?>" data-track-action="aroundhome_deeper_tco" data-track-category="internal_link" data-track-section="aroundhome_final">Portal vs. eigener Weg</a></p>
			</div>
			<div>
				<a class="hu-aroundhome__button hu-aroundhome__button--primary" href="<?php echo esc_url( $analysis_url ); ?>" data-track-action="cta_aroundhome_final_marktcheck" data-track-category="lead_gen" data-track-section="aroundhome_final">Marktcheck starten</a>
				<p class="hu-aroundhome__byline">Anbieterunabhängige Einordnung von <?php echo esc_html( get_the_author() ); ?>, der selbst eigene Anfragesysteme anbietet · Stand <?php echo esc_html( $source_date ); ?></p>
			</div>
		</div>
	</section>
</article>
