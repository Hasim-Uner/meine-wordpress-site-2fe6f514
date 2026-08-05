<?php
/**
 * Slug-specific decision cockpit for the editor-owned Checkfox post.
 *
 * The page is deliberately short: hero, in-page navigation and five sections.
 * Everything the visitor needs to decide sits above the sources block, so the
 * stored editor article is no longer rendered as a trailing long read.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$analysis_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/' );
$tco_url      = home_url( '/eigene-leadgenerierung-vs-portale/' );
$solar_url    = home_url( '/solar-leads-kaufen-alternative/' );
$heatpump_url = home_url( '/waermepumpen-leads/' );

// Avatare sind in WordPress deaktiviert, die Autorenbox darunter zeigt nur die
// Initialen. Das Portrait hier ist damit das einzige echte Foto der Seite.
$portrait_url = function_exists( 'nexus_asset_url' )
	? nexus_asset_url( 'img/hasim-portrait.png' )
	: get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';

$contract_items = [
	'exklusivitaet'    => 'Exklusivität schriftlich geregelt',
	'empfaenger'       => 'Anzahl paralleler Empfänger bekannt',
	'mindestabnahme'   => 'Mindestabnahme geklärt',
	'reklamation'      => 'Reklamationsgründe und Frist dokumentiert',
	'uebergabezeit'    => 'Übergabezeit vereinbart',
	'vertriebsgebiet'  => 'Vertriebsgebiet sauber begrenzt',
	'qualitaetsdaten'  => 'Zugriff auf Abschluss- und Qualitätsdaten',
	'kuendigungsfrist' => 'Kündigungsfrist bekannt',
];

$toc_items = [
	'portal-kostenrechner' => 'Kostenrechner',
	'vertragscheck'        => 'Vertragscheck',
	'wann-sinnvoll'        => 'Wann sinnvoll?',
	'quellen'              => 'Quellen',
];
?>

<article class="hu-checkfox" id="article-content" data-checkfox-cockpit data-track-section="checkfox_decision_page">
	<header class="hu-checkfox__hero" data-track-section="checkfox_hero" aria-labelledby="nexus-article-title">
		<div class="hu-checkfox__hero-copy">
			<p class="hu-checkfox__eyebrow">Markteinordnung für Solar- &amp; SHK-Betriebe</p>
			<h1 id="nexus-article-title">Checkfox seriös? Erst Vertrag prüfen, dann Kosten pro Auftrag rechnen.</h1>
			<p class="hu-checkfox__hero-lead">Seriosität lässt sich über Anbieterangaben, Datenschutz und Vertragsregeln prüfen. Ob sich der Kanal für Ihren Betrieb lohnt, zeigen erst Abschlussquote, Vertriebszeit und Marge.</p>
			<div class="hu-checkfox__hero-actions">
				<a class="hu-checkfox__button hu-checkfox__button--primary" href="#portal-kostenrechner" data-track-action="jump_checkfox_calculator" data-track-category="engagement" data-track-section="checkfox_hero">Kosten pro Auftrag berechnen</a>
				<a class="hu-checkfox__button hu-checkfox__button--secondary" href="#vertragscheck" data-track-action="jump_checkfox_contract" data-track-category="engagement" data-track-section="checkfox_hero">8 Vertragspunkte prüfen</a>
			</div>
			<p class="hu-checkfox__hero-note">Unabhängige Einordnung · keine Rechtsberatung · Stand August 2026</p>
		</div>

		<figure class="hu-checkfox__decision-visual" aria-labelledby="hu-checkfox-visual-caption">
			<div class="hu-checkfox__decision-side">
				<span class="hu-checkfox__decision-number">01</span>
				<strong>Seriosität prüfen</strong>
				<small>Vertrag · Datenschutz · Regeln</small>
			</div>
			<div class="hu-checkfox__decision-divider" aria-hidden="true"><span></span></div>
			<div class="hu-checkfox__decision-side">
				<span class="hu-checkfox__decision-number">02</span>
				<strong>Wirtschaft&shy;lichkeit berechnen</strong>
				<small>Abschlussquote · Zeit · Marge</small>
			</div>
			<figcaption id="hu-checkfox-visual-caption">Zwei getrennte Prüfungen, eine Kanalentscheidung.</figcaption>
		</figure>
	</header>

	<nav class="hu-checkfox__toc" aria-label="Auf dieser Seite" data-checkfox-toc>
		<ul>
			<?php foreach ( $toc_items as $toc_target => $toc_label ) : ?>
				<li><a href="#<?php echo esc_attr( $toc_target ); ?>"><?php echo esc_html( $toc_label ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<section class="hu-checkfox__section" id="portal-kostenrechner" tabindex="-1" data-track-section="checkfox_calculator">
		<h2>Kosten pro Auftrag berechnen</h2>
		<p class="hu-checkfox__section-lead">Der Stückpreis einer Anfrage sagt für sich genommen wenig. Entscheidend ist, was ein gewonnener Auftrag kostet: Anfragepreis geteilt durch Ihre Abschlussquote, plus die Vertriebszeit bis zum Abschluss.</p>
		<?php if ( function_exists( 'hu_render_portal_cpo_calculator' ) ) : ?>
			<?php echo hu_render_portal_cpo_calculator(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in renderer. ?>
		<?php endif; ?>
	</section>

	<section class="hu-checkfox__section" data-track-section="checkfox_contract">
		<h2>Vertragscheck vor der Unterschrift</h2>
		<p class="hu-checkfox__section-lead">Seriosität zeigt sich weniger am Auftritt eines Portals als daran, ob diese acht Punkte vor der Unterschrift schriftlich beantwortet sind. Bleibt einer offen, verschiebt sich das Risiko auf Ihren Betrieb. Der Check bewertet weder Checkfox noch einen konkreten Vertrag, sondern nur, wie belastbar Ihr Informationsstand ist. Es wird nichts gespeichert und nichts versendet.</p>

		<details class="hu-checkfox__contract" id="vertragscheck" data-checkfox-contract>
			<summary>8 Vertragspunkte prüfen</summary>
			<div class="hu-checkfox__contract-body">
				<fieldset>
					<legend>Was ist für Ihren Betrieb schriftlich geklärt?</legend>
					<ul class="hu-checkfox__contract-list">
						<?php foreach ( $contract_items as $item_key => $item_label ) : ?>
							<?php $control_id = 'checkfox-contract-' . $item_key; ?>
							<li>
								<input
									type="checkbox"
									id="<?php echo esc_attr( $control_id ); ?>"
									data-contract-check
									data-track-action="interact_checkfox_contract"
									data-track-category="engagement"
									data-track-section="checkfox_contract"
								>
								<label for="<?php echo esc_attr( $control_id ); ?>"><?php echo esc_html( $item_label ); ?></label>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>

				<p class="hu-checkfox__contract-result" data-track-action="complete_checkfox_contract" data-track-category="engagement" data-track-section="checkfox_contract">
					<strong data-contract-result aria-live="polite" aria-atomic="true">0 von 8 Punkten geklärt</strong>
					<span data-contract-detail>Solange Grundlagen offen sind, rechnet der Kostenrechner mit Annahmen statt mit Ihren Werten.</span>
				</p>
			</div>
		</details>
	</section>

	<section class="hu-checkfox__section" id="wann-sinnvoll" tabindex="-1" data-track-section="checkfox_process">
		<h2>Wann welcher Weg sinnvoll ist</h2>
		<p class="hu-checkfox__section-lead">Beide Wege schließen sich nicht aus. Sie unterscheiden sich darin, wem Reichweite, Daten und Kundenbeziehung gehören — und wie schnell sie wirken. Ein Portal liefert sofort Volumen aus fremder Reichweite, der eigene Weg baut über Monate eine Strecke auf, die dem Betrieb gehört.</p>
		<div class="hu-checkfox__compare">
			<div class="hu-checkfox__compare-lane">
				<h3>Portal-Modell</h3>
				<p class="hu-checkfox__compare-path">Endkunde → Portal → Verteilung laut Vertrag → Betrieb</p>
				<p class="hu-checkfox__compare-fit">Passt, wenn:</p>
				<ul>
					<li>Eine kurzfristige Auslastungslücke soll überbrückt werden.</li>
					<li>Der Vertrieb kann neue Kontakte binnen Minuten bearbeiten.</li>
					<li>Eine neue Region wird geprüft, bevor eigene Nachfrage steht.</li>
					<li>Kosten pro Auftrag und Kanalabhängigkeit werden getrennt gemessen.</li>
				</ul>
			</div>
			<div class="hu-checkfox__compare-lane">
				<h3>Eigener Anfrageweg</h3>
				<p class="hu-checkfox__compare-path">Endkunde → eigene Seite → Vorqualifizierung → CRM</p>
				<p class="hu-checkfox__compare-fit">Passt, wenn:</p>
				<ul>
					<li>Projektwert und Marge tragen einen systematischen Aufbau.</li>
					<li>Vorqualifizierung soll vor dem ersten Gespräch stattfinden.</li>
					<li>Messdaten und Markenbeziehung sollen beim Betrieb bleiben.</li>
					<li>Die Portalabhängigkeit soll über Monate sinken.</li>
				</ul>
			</div>
		</div>
	</section>

	<section class="hu-checkfox__section hu-checkfox__method" id="quellen" tabindex="-1" data-track-section="checkfox_methodology">
		<h2>Quellen, Methodik und Grenzen</h2>
		<p class="hu-checkfox__section-lead">Diese Seite trennt öffentlich prüfbare Seriositätsmerkmale von der internen Wirtschaftlichkeitsrechnung eines Betriebs. Sie ist kein Erfahrungsbericht, keine Rechtsberatung und keine Aussage über konkrete Checkfox-Verträge, Preise, Exklusivität oder Leadqualität.</p>
		<ul class="hu-checkfox__sources">
			<li><strong>Seriositätsprüfung:</strong> Anbieterangaben, Impressum, Vertragswerk, Datenschutzhinweise und Reklamationsregeln des jeweiligen Anbieters. Öffentliche Bewertungen liefern zusätzliche Perspektiven, ersetzen aber die Prüfung Ihres konkreten Vertrags nicht.</li>
			<li><strong>Rechenlogik:</strong> Die voreingestellten Werte im Rechner sind Beispiele, keine Checkfox-Konditionen. Anfragepreis, Abschlussquote und Vertriebszeit stammen aus Ihrem Betrieb — erst damit wird die Rechnung belastbar.</li>
			<li><strong>Grenzen:</strong> Diese Seite nennt keine Preise, Exklusivitätsregeln oder Qualitätsquoten eines Anbieters. Solche Angaben sind vertragsabhängig und ohne Einsicht in Ihren Vertrag nicht belegbar. Marktmechaniken ändern sich; maßgeblich ist immer Ihr aktueller Vertragsstand.</li>
			<li><strong>Vertiefung:</strong> <a href="<?php echo esc_url( $tco_url ); ?>" data-track-action="checkfox_deeper_tco" data-track-category="internal_link" data-track-section="checkfox_methodology">TCO-Vergleich Portal vs. eigenes System</a>, <a href="<?php echo esc_url( $solar_url ); ?>" data-track-action="checkfox_deeper_solar" data-track-category="internal_link" data-track-section="checkfox_methodology">Photovoltaik-Anfragen</a>, <a href="<?php echo esc_url( $heatpump_url ); ?>" data-track-action="checkfox_deeper_heatpump" data-track-category="internal_link" data-track-section="checkfox_methodology">Wärmepumpen-Anfragen</a>.</li>
		</ul>
	</section>

	<section class="hu-checkfox__final" id="finaler-marktcheck" data-track-section="checkfox_final">
		<div class="hu-checkfox__final-copy">
			<h2>Mit Ihren Zahlen in den Marktcheck.</h2>
			<p>Wenn Anfragepreis, Abschlussquote und Vertriebsaufwand stehen, ordnen wir den Kanal mit Region, Projektwert und Portalabhängigkeit ein.</p>
			<a class="hu-checkfox__button hu-checkfox__button--primary" href="<?php echo esc_url( $analysis_url ); ?>" data-track-action="cta_checkfox_final_marktcheck" data-track-category="lead_gen" data-track-section="checkfox_final">Marktcheck mit meinen Zahlen starten</a>
			<p class="hu-checkfox__byline">
				<img
					src="<?php echo esc_url( $portrait_url ); ?>"
					alt="<?php echo esc_attr( get_the_author() ); ?>"
					width="1254"
					height="1254"
					loading="lazy"
					decoding="async"
				>
				<span>Einordnung von <?php echo esc_html( get_the_author() ); ?> · zuletzt aktualisiert am <?php echo esc_html( get_the_modified_date( 'd. F Y' ) ); ?></span>
			</p>
		</div>
	</section>
</article>
