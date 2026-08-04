<?php
/**
 * Slug-specific decision cockpit for the editor-owned Checkfox post.
 *
 * The WordPress post remains the source of record. This partial supplies the
 * repository-owned presentation, interactions and conversion path only.
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
$reading_time = function_exists( 'nexus_get_reading_time' ) ? (int) nexus_get_reading_time() : 0;

$contract_items = [
	'exklusivitaet'     => 'Exklusivität schriftlich geregelt',
	'empfaenger'        => 'Anzahl paralleler Empfänger bekannt',
	'mindestabnahme'    => 'Mindestabnahme geklärt',
	'reklamation'       => 'Reklamationsgründe und Frist dokumentiert',
	'uebergabezeit'     => 'Übergabezeit vereinbart',
	'vertriebsgebiet'   => 'Vertriebsgebiet sauber begrenzt',
	'qualitaetsdaten'   => 'Zugriff auf Abschluss- und Qualitätsdaten',
	'kuendigungsfrist'  => 'Kündigungsfrist bekannt',
];

$raw_editorial_content = (string) get_the_content();
$editorial_links       = [
	'[Wärmepumpen-Leads kaufen – die Alternative](/waermepumpen-leads/)' => sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $heatpump_url ),
		esc_html( 'Wärmepumpen-Leads kaufen – die Alternative' )
	),
	'[Photovoltaik & Solar Leads kaufen – die Alternative](/solar-leads-kaufen-alternative/)' => sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $solar_url ),
		esc_html( 'Photovoltaik & Solar Leads kaufen – die Alternative' )
	),
	'[Solar Case Study](/case-study-solar-leadgenerierung/)' => sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( home_url( '/case-study-solar-leadgenerierung/' ) ),
		esc_html( 'Solar Case Study' )
	),
	'[TCO-Vergleich Portal-Leads vs. eigenes Anfrage-System](/eigene-leadgenerierung-vs-portale/)' => sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $tco_url ),
		esc_html( 'TCO-Vergleich Portal-Leads vs. eigenes Anfrage-System' )
	),
	'[Solar-Marktcheck starten](/solar-waermepumpen-leadgenerierung/#marktcheck)' => sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $analysis_url ),
		esc_html( 'Marktcheck starten' )
	),
];
$raw_editorial_content = str_replace( array_keys( $editorial_links ), array_values( $editorial_links ), $raw_editorial_content );
$editorial_content     = apply_filters( 'the_content', $raw_editorial_content );

$editorial_headings = [
	'Kernthese'                                           => 'Seriosität und Wirtschaftlichkeit im redaktionellen Kontext',
	'Wer sucht das?'                                      => 'Für wen diese Einordnung relevant ist',
	'Ist Checkfox seriös?'                                => 'Prüfkriterien für Seriosität',
	'Wie das kategorieübergreifende Portal-Modell funktioniert' => 'Funktionsweise eines breiten Vermittlungsportals',
	'Die Rechnung, die vor jedem Vertrag stehen sollte'   => 'Beispielrechnung und Kostentreiber',
	'Warnsignale, auf die Betriebe achten sollten'         => 'Informationslücken vor einem Vertrag',
	'Die strategische Alternative: Eigene Anfrage-Infrastruktur' => 'Eigene Anfrage-Infrastruktur',
	'Wann das breite Portal-Modell trotzdem Sinn ergibt'   => 'Wann Portal-Leads sinnvoll sein können',
	'Nächster Schritt: Solar-Marktcheck'                   => 'Einordnung vor dem Marktcheck',
];

foreach ( $editorial_headings as $old_heading => $new_heading ) {
	$editorial_content = str_replace(
		'<h2>' . $old_heading . '</h2>',
		'<h3>' . $new_heading . '</h3>',
		$editorial_content
	);
}

// Keep the editor content intact while making any later H2 additions subordinate
// to the repository-owned "Methodik" section.
$editorial_content = preg_replace( '/<h1(\s[^>]*)?>/i', '<h3$1>', (string) $editorial_content );
$editorial_content = preg_replace( '/<\/h1>/i', '</h3>', (string) $editorial_content );
$editorial_content = preg_replace( '/<h2(\s[^>]*)?>/i', '<h3$1>', (string) $editorial_content );
$editorial_content = preg_replace( '/<\/h2>/i', '</h3>', (string) $editorial_content );

// Provider-specific conclusions and market figures remain deliberately bounded.
$editorial_content = str_replace(
	'Checkfox tritt als etabliertes Vermittlungsunternehmen mit öffentlich einsehbarem Firmenprofil auf. Öffentliche Bewertungen zeichnen – wie bei praktisch allen großen Portalen – ein gemischtes Bild aus unterschiedlichen Perspektiven.',
	'Prüfen Sie Anbieterangaben anhand von Impressum, Vertragswerk, Datenschutz, Konditionen und Reklamationsregeln. Öffentliche Bewertungen können zusätzliche Perspektiven liefern, ersetzen aber nicht die Prüfung des konkreten Vertrags.',
	(string) $editorial_content
);
$editorial_content = str_replace(
	'Marktüblich sind im PV-/Wärmepumpen-Segment',
	'<strong>Beispielwerte – keine Checkfox-Konditionen:</strong> Marktüblich sind im PV-/Wärmepumpen-Segment',
	(string) $editorial_content
);
$editorial_content = str_replace(
	'Wirtschaftlich relevant ist nicht der Stückpreis, sondern der <strong>Preis pro Auftrag</strong>. Bei 80 € pro Anfrage',
	'Wirtschaftlich relevant ist nicht der Stückpreis, sondern der <strong>Preis pro Auftrag</strong>. <strong>Beispielwerte – keine Checkfox-Konditionen:</strong> Bei 80 € pro Anfrage',
	(string) $editorial_content
);
?>

<article class="hu-checkfox" id="article-content" data-checkfox-cockpit data-track-section="checkfox_decision_page">
	<header class="hu-checkfox__hero" data-track-section="checkfox_hero" aria-labelledby="nexus-article-title">
		<div class="hu-checkfox__hero-copy">
			<div class="hu-checkfox__meta">
				<span>Markteinordnung für Solar / SHK</span>
				<span><?php echo esc_html( get_the_date( 'd. F Y' ) ); ?></span>
				<?php if ( $reading_time > 0 ) : ?>
					<span><?php echo esc_html( sprintf( '%d Min. Lesezeit', $reading_time ) ); ?></span>
				<?php endif; ?>
			</div>
			<h1 id="nexus-article-title">Checkfox seriös? Entscheidend ist, ob sich der Leadkanal für Ihren Betrieb rechnet.</h1>
			<p class="hu-checkfox__hero-lead">Prüfen Sie Vertrag, Verteilungslogik und Kosten pro Auftrag getrennt. Ein seriöses Portal kann für einen konkreten Betrieb trotzdem unwirtschaftlich sein.</p>
			<div class="hu-checkfox__hero-actions">
				<a class="hu-checkfox__button hu-checkfox__button--primary" href="#portal-kostenrechner" data-track-action="jump_checkfox_calculator" data-track-category="engagement" data-track-section="checkfox_hero">Kosten pro Auftrag berechnen</a>
				<a class="hu-checkfox__button hu-checkfox__button--secondary" href="#vertragscheck" data-track-action="jump_checkfox_contract" data-track-category="engagement" data-track-section="checkfox_hero">Vertragscheck öffnen</a>
			</div>
			<p class="hu-checkfox__byline">Einordnung von <?php echo esc_html( get_the_author() ); ?> · keine Rechtsberatung und keine pauschale Anbieterbewertung</p>
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
				<strong>Wirtschaftlichkeit berechnen</strong>
				<small>Abschlussquote · Zeit · Kosten</small>
			</div>
			<figcaption id="hu-checkfox-visual-caption">Zwei getrennte Prüfungen führen zu einer belastbaren Kanalentscheidung.</figcaption>
		</figure>
	</header>

	<section class="hu-checkfox__section hu-checkfox__section--intro" id="kurzantwort" data-track-section="checkfox_intent">
		<p class="hu-checkfox__eyebrow">Kurzantwort</p>
		<h2>Seriosität und wirtschaftliche Eignung sind zwei verschiedene Fragen.</h2>
		<fieldset class="hu-checkfox__intent" data-checkfox-intent>
			<legend>Welche Frage möchten Sie zuerst klären?</legend>
			<div class="hu-checkfox__intent-options">
				<input type="radio" name="checkfox-intent" id="checkfox-intent-serious" value="seriositaet" checked data-track-action="select_checkfox_intent_seriositaet" data-track-category="engagement" data-track-section="checkfox_intent">
				<label for="checkfox-intent-serious">Ist Checkfox seriös?</label>
				<input type="radio" name="checkfox-intent" id="checkfox-intent-economics" value="wirtschaftlichkeit" data-track-action="select_checkfox_intent_wirtschaftlichkeit" data-track-category="engagement" data-track-section="checkfox_intent">
				<label for="checkfox-intent-economics">Lohnt sich Checkfox für meinen Betrieb?</label>
			</div>
			<div class="hu-checkfox__intent-answer" id="checkfox-intent-answer" aria-live="polite" aria-atomic="true">
				<p data-checkfox-intent-answer="seriositaet">Seriosität lässt sich anhand von Impressum, Vertragswerk, Datenschutz, Konditionen und Reklamationsregeln prüfen. Diese Seite gibt keine pauschale Anbieterbewertung ab.</p>
				<p data-checkfox-intent-answer="wirtschaftlichkeit" hidden>Für Ihren Betrieb zählen Exklusivität, Übergabezeit, Abschlussquote, Vertriebsaufwand und Kosten pro gewonnenem Auftrag. Genau diese Werte sollten vor einem Vertrag berechnet werden.</p>
			</div>
			<noscript><p class="hu-checkfox__noscript-answer"><strong>Wirtschaftlichkeit:</strong> Für Ihren Betrieb zählen Exklusivität, Übergabezeit, Abschlussquote, Vertriebsaufwand und Kosten pro gewonnenem Auftrag. Genau diese Werte sollten vor einem Vertrag berechnet werden.</p></noscript>
		</fieldset>
	</section>

	<section class="hu-checkfox__section" id="seriositaet-wirtschaftlichkeit" data-track-section="checkfox_decision_thesis">
		<p class="hu-checkfox__eyebrow">Zwei Prüfpfade</p>
		<h2>Seriosität versus Wirtschaftlichkeit</h2>
		<div class="hu-checkfox__decision-grid">
			<div>
				<h3>Seriosität ist dokumentierbar.</h3>
				<p>Impressum, Datenschutz, Vertrag, Konditionen, Kündigung und Reklamation lassen sich sachlich prüfen. Diese Einordnung ersetzt keine Prüfung Ihres konkreten Vertrags.</p>
			</div>
			<div>
				<h3>Wirtschaftlichkeit ist betriebsspezifisch.</h3>
				<p>Dieselbe Anfrage kann für zwei Betriebe unterschiedlich rentabel sein – abhängig von Reaktionszeit, Abschlussquote, Vertriebsaufwand, Projektwert und Marge.</p>
			</div>
		</div>
	</section>

	<section class="hu-checkfox__section" id="portal-kostenrechner" tabindex="-1" data-track-section="checkfox_calculator">
		<p class="hu-checkfox__eyebrow">Mit eigenen Zahlen prüfen</p>
		<h2>Kosten pro Auftrag berechnen</h2>
		<?php if ( function_exists( 'hu_render_portal_cpo_calculator' ) ) : ?>
			<?php echo hu_render_portal_cpo_calculator(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in renderer. ?>
		<?php endif; ?>
	</section>

	<section class="hu-checkfox__section" id="vertragscheck" tabindex="-1" data-track-section="checkfox_contract">
		<p class="hu-checkfox__eyebrow">Informationsstand prüfen</p>
		<h2>Vertragscheck</h2>
		<p class="hu-checkfox__section-lead">Bewerten Sie nur, was für Ihren Betrieb schriftlich geklärt ist. Der Check bewertet weder Checkfox noch einen Vertrag und speichert oder sendet keine Auswahl.</p>
		<div class="hu-checkfox__contract" data-checkfox-contract>
			<?php foreach ( $contract_items as $item_key => $item_label ) : ?>
				<fieldset class="hu-checkfox__contract-row" data-contract-row="<?php echo esc_attr( $item_key ); ?>">
					<legend><?php echo esc_html( $item_label ); ?></legend>
					<div class="hu-checkfox__contract-options">
						<?php foreach ( [ 'geklaert' => 'geklärt', 'ungeklaert' => 'ungeklärt', 'nicht-zutreffend' => 'trifft nicht zu' ] as $state_key => $state_label ) : ?>
							<?php $control_id = 'checkfox-contract-' . $item_key . '-' . $state_key; ?>
							<input
								type="radio"
								id="<?php echo esc_attr( $control_id ); ?>"
								name="checkfox-contract-<?php echo esc_attr( $item_key ); ?>"
								value="<?php echo esc_attr( $state_key ); ?>"
								<?php checked( 'ungeklaert', $state_key ); ?>
								data-contract-check
								data-track-action="interact_checkfox_contract"
								data-track-category="engagement"
								data-track-section="checkfox_contract"
							>
							<label for="<?php echo esc_attr( $control_id ); ?>"><?php echo esc_html( $state_label ); ?></label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endforeach; ?>
			<div class="hu-checkfox__contract-result" data-track-action="complete_checkfox_contract" data-track-category="engagement" data-track-section="checkfox_contract">
				<span>Ihr Informationsstand</span>
				<strong data-contract-result aria-live="polite" aria-atomic="true">Wirtschaftliche Entscheidung noch nicht belastbar</strong>
				<p data-contract-detail>Acht Punkte sind noch ungeklärt. Klären Sie zuerst die Grundlagen und rechnen Sie danach mit belastbaren Werten.</p>
			</div>
		</div>
	</section>

	<section class="hu-checkfox__section" id="portal-funktionsweise" data-track-section="checkfox_process">
		<p class="hu-checkfox__eyebrow">Prozessvergleich</p>
		<h2>Funktionsweise eines breiten Vermittlungsportals</h2>
		<figure class="hu-checkfox__process">
			<div class="hu-checkfox__process-lane hu-checkfox__process-lane--portal">
				<h3>Portal-Modell</h3>
				<ol>
					<li>Endkunde</li>
					<li>Vergleichsportal</li>
					<li>Verteilung laut Vertragsmodell</li>
					<li>Betrieb</li>
					<li>nachgelagerte Qualifizierung</li>
				</ol>
				<p><strong>Zu klären:</strong> Exklusivität, Empfängerzahl, Übergabezeit sowie Zugriff auf Abschluss- und Qualitätsdaten.</p>
			</div>
			<div class="hu-checkfox__process-lane hu-checkfox__process-lane--owned">
				<h3>Eigener Anfrageweg</h3>
				<ol>
					<li>Endkunde</li>
					<li>eigene Seite</li>
					<li>Vorqualifizierung</li>
					<li>CRM</li>
					<li>eigener Vertrieb</li>
				</ol>
				<p><strong>Gestaltbar:</strong> Zugriff, Datenfluss und Markenbeziehung liegen innerhalb der eigenen Strecke.</p>
			</div>
			<figcaption>Die Darstellung beschreibt zwei mögliche Prozesslogiken. Ob und an wie viele Empfänger eine Portal-Anfrage verteilt wird, ergibt sich ausschließlich aus dem jeweiligen Vertrag.</figcaption>
		</figure>
	</section>

	<div class="hu-checkfox__section hu-checkfox__fit-grid">
		<section id="portal-sinnvoll" data-track-section="checkfox_portal_fit">
			<p class="hu-checkfox__eyebrow">Ergänzungskanal</p>
			<h2>Wann Portal-Leads sinnvoll sein können</h2>
			<ul>
				<li>Eine kurzfristige Auslastungslücke soll überbrückt werden.</li>
				<li>Der Vertrieb kann neue Kontakte schnell und konsequent bearbeiten.</li>
				<li>Eine neue Region wird getestet, bevor eigene Nachfrage aufgebaut ist.</li>
				<li>Kosten pro Auftrag und Kanalabhängigkeit werden separat gemessen.</li>
			</ul>
		</section>
		<section id="eigener-anfrageweg" data-track-section="checkfox_owned_fit">
			<p class="hu-checkfox__eyebrow">Eigene Infrastruktur</p>
			<h2>Wann eine eigene Anfrage-Infrastruktur wirtschaftlicher wird</h2>
			<ul>
				<li>Genügend Projektwert und Marge tragen den systematischen Aufbau.</li>
				<li>Vorqualifizierung soll vor dem ersten Vertriebsgespräch stattfinden.</li>
				<li>Markenbeziehung, Messdaten und Optimierungswissen sollen beim Betrieb bleiben.</li>
				<li>Portalabhängigkeit soll über einen längeren Zeitraum sinken.</li>
			</ul>
		</section>
	</div>

	<section class="hu-checkfox__section hu-checkfox__method" id="methodik" data-track-section="checkfox_methodology">
		<p class="hu-checkfox__eyebrow">Transparenz</p>
		<h2>Methodik, Quellen und Grenzen der Einordnung</h2>
		<p class="hu-checkfox__section-lead">Die Seite trennt öffentlich prüfbare Seriositätsmerkmale von der internen Wirtschaftlichkeitsrechnung eines Betriebs. Sie ist kein Erfahrungsbericht, keine Rechtsberatung und keine Aussage über konkrete Checkfox-Verträge, Preise, Exklusivität oder Leadqualität.</p>
		<p class="hu-checkfox__example-note"><strong>Beispielwerte – keine Checkfox-Konditionen:</strong> Alle Rechenwerte und marktüblichen Szenarien im folgenden redaktionellen Inhalt sind Beispiele. Belastbar wird die Entscheidung erst mit Ihrem Vertrag und Ihren Vertriebsdaten.</p>
		<div class="hu-checkfox__editorial-content">
			<?php echo $editorial_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered WordPress editor content. ?>
		</div>
	</section>

	<section class="hu-checkfox__final" id="finaler-marktcheck" data-track-section="checkfox_final">
		<p class="hu-checkfox__eyebrow">Nächster Schritt</p>
		<h2>Mit Ihren Zahlen in den Marktcheck.</h2>
		<p>Wenn Preis pro Anfrage, Abschlussquote und Vertriebsaufwand eingetragen sind, lässt sich der Kanal im nächsten Schritt mit Region, Projektwert und Portalabhängigkeit einordnen.</p>
		<a class="hu-checkfox__button hu-checkfox__button--primary" href="<?php echo esc_url( $analysis_url ); ?>" data-track-action="cta_checkfox_final_marktcheck" data-track-category="lead_gen" data-track-section="checkfox_final">Marktcheck mit meinen Zahlen starten</a>
		<nav class="hu-checkfox__deeper" aria-label="Sekundäre Vertiefung">
			<a href="<?php echo esc_url( $tco_url ); ?>" data-track-action="checkfox_deeper_tco" data-track-category="internal_link" data-track-section="checkfox_final">TCO-Vergleich Portal vs. eigenes System</a>
			<a href="<?php echo esc_url( $solar_url ); ?>" data-track-action="checkfox_deeper_solar" data-track-category="internal_link" data-track-section="checkfox_final">Photovoltaik-Anfragen vertiefen</a>
			<a href="<?php echo esc_url( $heatpump_url ); ?>" data-track-action="checkfox_deeper_heatpump" data-track-category="internal_link" data-track-section="checkfox_final">Wärmepumpen-Anfragen vertiefen</a>
		</nav>
	</section>
</article>
