<?php
/**
 * Slug-specific cost and contract decision page for the Aroundhome post.
 *
 * Public provider statements are separated from contract-specific terms and
 * the economics of the individual business. The stored editor article is not
 * rendered on this route; SEO ownership and post metadata remain unchanged.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$primary_urls        = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$analysis_url        = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/' );
$tco_url             = $primary_urls['solar_leads_tco'] ?? home_url( '/eigene-leadgenerierung-vs-portale/' );
$solar_url           = $primary_urls['solar_leads_alternative'] ?? home_url( '/solar-leads-kaufen-alternative/' );
$cpl_url             = $primary_urls['solar_cpl'] ?? home_url( '/cost-per-lead-photovoltaik/' );
$case_url            = $primary_urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$aroundhome_partner  = 'https://www.aroundhome.de/partner-werden/';
$aroundhome_terms    = 'https://www.aroundhome.de/legal/';
$aroundhome_faqs     = function_exists( 'nexus_get_aroundhome_faq_items' ) ? nexus_get_aroundhome_faq_items() : [];
$case_cpl_before     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' ) : '150 €';
$case_cpl_after      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '22 €' ) : '22 €';
$case_leads          = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count', 'display', '1.750+' ) : '1.750+';
$case_conversion     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'sales_conversion', 'display', '12 %' ) : '12 %';
$case_timeframe      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display', '6 Monate' ) : '6 Monate';

$contract_items = [
	[
		'key'   => 'abrechnung',
		'title' => 'Abrechenbares Ereignis',
		'text'  => 'Was löst die Gebühr aus — Übermittlung, Kontakt, Termin oder ein anderes Ereignis?',
	],
	[
		'key'   => 'gesamtkosten',
		'title' => 'Preis und Gesamtkosten',
		'text'  => 'Welche Preisbestandteile, Steuern, Zusatzkosten und Mengen gehören in die Monatsrechnung?',
	],
	[
		'key'   => 'verteilung',
		'title' => 'Verteilung und Exklusivität',
		'text'  => 'An wie viele Fachbetriebe kann dieselbe Anfrage gehen — und was bedeutet „exklusiv“ im Vertrag?',
	],
	[
		'key'   => 'kriterien',
		'title' => 'Region und Leadkriterien',
		'text'  => 'Welche Gewerke, Projekte, Radien, Budgets und Ausschlusskriterien gelten verbindlich?',
	],
	[
		'key'   => 'laufzeit',
		'title' => 'Menge, Laufzeit und Pause',
		'text'  => 'Welche Abnahmemenge, Kündigungsfrist, Änderungsregeln und Pausenbedingungen sind vereinbart?',
	],
	[
		'key'   => 'reklamation',
		'title' => 'Reklamation und Gutschrift',
		'text'  => 'Welche Gründe zählen, welche Frist läuft und wie wird eine Entscheidung dokumentiert?',
	],
	[
		'key'   => 'daten',
		'title' => 'Herkunft und Messdaten',
		'text'  => 'Welche Einwilligungs-, Quellen-, Zeit- und Statusdaten können CRM und Attribution übernehmen?',
	],
];

$path_items = [
	[
		'key'   => 'portal',
		'label' => 'Portal',
		'title' => 'Kurzfristig Nachfrage ergänzen',
		'text'  => 'Plausibel, wenn Vertrag und CPO tragfähig sind und der Vertrieb neue Kontakte konsequent bearbeiten kann.',
		'path'  => 'Endkunde → Portal → Betrieb',
	],
	[
		'key'   => 'hybrid',
		'label' => 'Hybrid',
		'title' => 'Versorgung sichern, Eigentum aufbauen',
		'text'  => 'Plausibel als Übergang, wenn Portal- und Eigenanfragen getrennt gemessen und nicht in einer Quote vermischt werden.',
		'path'  => 'Portal + eigene Strecke → CRM',
	],
	[
		'key'   => 'eigen',
		'label' => 'Eigener Weg',
		'title' => 'Nachfrage kontrollierbar entwickeln',
		'text'  => 'Plausibel, wenn Projektwert, Marge und Vertriebsanschluss den Aufbau tragen und Daten beim Betrieb bleiben sollen.',
		'path'  => 'Endkunde → eigene Seite → CRM',
	],
];
?>

<article
	class="hu-aroundhome"
	id="article-content"
	data-provider-decision
	data-aroundhome-decision
	data-track-section="aroundhome_decision_page"
>
	<header class="hu-aroundhome__band hu-aroundhome__band--ink hu-aroundhome__hero" data-provider-decision-hero data-track-section="aroundhome_hero" aria-labelledby="nexus-article-title">
		<div class="hu-aroundhome__inner hu-aroundhome__hero-grid">
			<div class="hu-aroundhome__hero-copy">
				<p class="hu-aroundhome__eyebrow">Kostenentscheid für Solar- &amp; SHK-Betriebe</p>
				<h1 id="nexus-article-title">Aroundhome-Kosten für Handwerker: Rechnen Sie den Auftrag, nicht den Lead.</h1>
				<p class="hu-aroundhome__hero-lead">Aroundhome nennt auf seiner Partnerseite keinen einheitlichen öffentlichen Leadpreis. Der produktabhängige Preis wird nach Anbieterangaben im digitalen Vertragsprozess sichtbar. Ob der Kanal für Ihren Betrieb trägt, zeigen erst Abschlussquote, Vertriebszeit und Deckungsbeitrag.</p>
				<div class="hu-aroundhome__hero-actions">
					<a class="hu-aroundhome__button hu-aroundhome__button--primary" href="#kostenrechner" data-track-action="jump_aroundhome_calculator" data-track-category="engagement" data-track-section="aroundhome_hero">CPO mit eigenen Zahlen prüfen</a>
					<a class="hu-aroundhome__button hu-aroundhome__button--secondary" href="#vertragscheck" data-track-action="jump_aroundhome_contract" data-track-category="engagement" data-track-section="aroundhome_hero">7 Vertragspunkte prüfen</a>
				</div>
				<p class="hu-aroundhome__hero-note">Anbieterunabhängige wirtschaftliche Einordnung aus Sicht eines Anfragesystem-Anbieters · keine Rechtsberatung · Quellenstand 12. August 2026</p>
			</div>

			<aside class="hu-aroundhome__decision-sheet" aria-label="Drei Prüffelder für den Kanalentscheid">
				<div class="hu-aroundhome__sheet-head">
					<span>Entscheidungsakte</span>
					<strong>AH / 03</strong>
				</div>
				<ol class="hu-aroundhome__sheet-list">
					<li>
						<span>01</span>
						<div><strong>Kosten pro Auftrag</strong><small>Leadpreis + Vertriebszeit ÷ Aufträge</small></div>
					</li>
					<li>
						<span>02</span>
						<div><strong>Vertragsklarheit</strong><small>Verteilung · Menge · Reklamation</small></div>
					</li>
					<li>
						<span>03</span>
						<div><strong>Vertriebsfähigkeit</strong><small>Reaktion · Dokumentation · Abschluss</small></div>
					</li>
				</ol>
				<p>Fehlt ein Feld, fehlt keine Portalbewertung — sondern eine Entscheidungsgrundlage.</p>
			</aside>
		</div>
	</header>

	<nav class="hu-aroundhome__jump" aria-label="Auf dieser Seite" data-track-section="aroundhome_navigation">
		<div class="hu-aroundhome__inner">
			<ul>
				<li><a href="#kurzantwort">Kurzantwort</a></li>
				<li><a href="#kostenrechner">Kostenrechner</a></li>
				<li><a href="#vertragscheck">Vertragscheck</a></li>
				<li><a href="#erfahrungen">Erfahrungen</a></li>
				<li><a href="#wege">Drei Wege</a></li>
				<li><a href="#faq">FAQ</a></li>
			</ul>
		</div>
	</nav>

	<section class="hu-aroundhome__band hu-aroundhome__band--paper" id="kurzantwort" tabindex="-1" data-track-section="aroundhome_short_answer">
		<div class="hu-aroundhome__inner">
			<div class="hu-aroundhome__section-head hu-aroundhome__section-head--split">
				<div>
					<p class="hu-aroundhome__eyebrow">Die kurze Antwort</p>
					<h2>Was Aroundhome kostet, entscheidet Ihr Angebot. Was es Sie kostet, entscheidet Ihr Vertrieb.</h2>
				</div>
				<p>Der Preis pro Anfrage ist nur die sichtbare Zeile. Für eine belastbare Entscheidung müssen drei Ebenen getrennt bleiben: öffentliche Anbieterangaben, Ihr konkreter Vertrag und Ihre betriebliche Abschlussökonomie.</p>
			</div>

			<div class="hu-aroundhome__answer-grid">
				<article class="hu-aroundhome__answer-card">
					<span class="hu-aroundhome__card-index">01 / Öffentlich</span>
					<h3>Das Modell</h3>
					<p>Nach eigener Partnerseite zahlen Fachbetriebe pro übermittelter Anfrage; der Preis hängt vom angebotenen Produkt ab.</p>
					<a href="<?php echo esc_url( $aroundhome_partner ); ?>" rel="noopener noreferrer" target="_blank" data-track-action="source_aroundhome_partner" data-track-category="external_link" data-track-section="aroundhome_short_answer">Anbieterangabe prüfen ↗</a>
				</article>
				<article class="hu-aroundhome__answer-card">
					<span class="hu-aroundhome__card-index">02 / Vertrag</span>
					<h3>Die Konditionen</h3>
					<p>Preis, Verteilung, Mengen, Laufzeit und Reklamation können vom konkreten Angebot abhängen. Nur die schriftliche Fassung ist entscheidungsfähig.</p>
					<a href="#vertragscheck" data-track-action="jump_aroundhome_contract_from_answer" data-track-category="engagement" data-track-section="aroundhome_short_answer">Prüfraster öffnen ↓</a>
				</article>
				<article class="hu-aroundhome__answer-card hu-aroundhome__answer-card--accent">
					<span class="hu-aroundhome__card-index">03 / Betrieb</span>
					<h3>Die Wirtschaftlichkeit</h3>
					<p>Ihre Abschlussquote und Vertriebszeit verwandeln den Leadpreis in Kosten pro gewonnenem Auftrag. Erst dieser Wert ist vergleichbar.</p>
					<a href="#kostenrechner" data-track-action="jump_aroundhome_calculator_from_answer" data-track-category="engagement" data-track-section="aroundhome_short_answer">Mit eigenen Zahlen rechnen ↓</a>
				</article>
			</div>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--ink" id="kostenrechner" tabindex="-1" data-track-section="aroundhome_cpo">
		<div class="hu-aroundhome__inner">
			<div class="hu-aroundhome__section-head hu-aroundhome__section-head--split">
				<div>
					<p class="hu-aroundhome__eyebrow">Ihre Zahlen, keine Benchmarks</p>
					<h2>Vom Anfragepreis zu den Anfrage- und Vertriebskosten pro Auftrag.</h2>
				</div>
				<p>Eine günstige Anfrage kann teuer werden, wenn viele Kontakte nötig sind oder viel Vertriebszeit gebunden wird. Der Rechner nimmt deshalb keine angeblichen Aroundhome- oder Branchenwerte vorweg.</p>
			</div>

			<div class="hu-aroundhome__equation" aria-label="Anfragegebühren plus interne Vertriebskosten, geteilt durch gewonnene Aufträge, ergeben die Kosten pro Auftrag">
				<span>Anfragegebühren</span><b>+</b><span>interne Vertriebskosten</span><b>÷</b><span>gewonnene Aufträge</span><b>=</b><strong>CPO</strong>
			</div>

			<?php if ( function_exists( 'hu_render_portal_cpo_calculator' ) ) : ?>
				<?php
				echo hu_render_portal_cpo_calculator( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in renderer.
					[
						'provider_label'   => 'Aroundhome',
						'tracking_prefix'  => 'aroundhome',
						'tracking_section' => 'aroundhome_cpo',
						'defaults'         => [
							'cpl'           => '',
							'leads'         => '',
							'close_rate'    => '',
							'sales_minutes' => '',
							'hourly_rate'   => '',
						],
						'require_complete' => true,
						'heading'          => 'Was kostet Ihr Portal-Kanal pro gewonnenem Auftrag?',
						'description'      => 'Übernehmen Sie Preis und Menge aus Ihrem aktuellen Angebot. Abschlussquote und Bearbeitungszeit sollten aus CRM-Daten stammen oder bewusst konservativ angesetzt werden.',
						'example_text'     => 'Keine Aroundhome-Konditionen und keine Branchenwerte voreingestellt. Ihre Eingaben bleiben im Browser und werden nicht gespeichert.',
						'cta_label'        => 'Wirtschaftlichkeit im Marktcheck einordnen',
						'cta_note'         => 'Im Marktcheck werden Region, Projektwert, Vertriebskapazität und Kanalabhängigkeit händisch eingeordnet.',
					]
				);
				?>
			<?php endif; ?>
			<noscript>
				<p class="hu-aroundhome__noscript">Der Rechner benötigt JavaScript und überträgt keine Werte. Sie können trotzdem mit der Formel oben rechnen und die Wirtschaftlichkeit anschließend im Marktcheck einordnen.</p>
			</noscript>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--paper" id="vertragscheck" tabindex="-1" data-track-section="aroundhome_contract">
		<div class="hu-aroundhome__inner">
			<div class="hu-aroundhome__section-head hu-aroundhome__section-head--split">
				<div>
					<p class="hu-aroundhome__eyebrow">Vor dem ersten abrechenbaren Lead</p>
					<h2>Sieben Punkte, die nicht im Vertriebsgespräch bleiben dürfen.</h2>
				</div>
				<p>Der Check bewertet nicht Aroundhome und nicht die juristische Wirksamkeit eines Vertrags. Er zeigt nur, ob Ihr Informationsstand für eine wirtschaftliche Entscheidung ausreicht. Es wird nichts gespeichert oder versendet.</p>
			</div>

			<div class="hu-aroundhome__ledger" data-aroundhome-contract>
				<div class="hu-aroundhome__ledger-head">
					<div>
						<span>Vertragsakte / Selbstcheck</span>
						<strong data-contract-result aria-live="polite" aria-atomic="true">Noch nicht ausgewertet · <?php echo esc_html( (string) count( $contract_items ) ); ?> Punkte</strong>
					</div>
					<small>lokal im Browser</small>
				</div>
				<fieldset>
					<legend>Welche Punkte liegen Ihrem Betrieb schriftlich vor?</legend>
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
				<p class="hu-aroundhome__ledger-status" data-contract-detail role="status" aria-live="polite" aria-atomic="true" data-track-action="complete_aroundhome_contract" data-track-category="engagement" data-track-section="aroundhome_contract">Solange Grundlagen offen sind, rechnet der Kostenvergleich mit Annahmen statt mit vertraglich geklärten Werten.</p>
			</div>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--ink" id="erfahrungen" tabindex="-1" data-track-section="aroundhome_experiences">
		<div class="hu-aroundhome__inner hu-aroundhome__experience-layout">
			<div class="hu-aroundhome__section-head">
				<p class="hu-aroundhome__eyebrow">Erfahrungen richtig einordnen</p>
				<h2>Ein Erfahrungsbericht liefert eine Frage. Noch keine Kalkulation.</h2>
				<p>„Aroundhome Erfahrungen“ ist eine sinnvolle Suche, solange der Kontext sichtbar bleibt. Ohne Gewerk, Region, Zeitraum, Vertragsmodell und Abschlussquote lässt sich eine fremde Erfahrung nicht auf Ihren Betrieb übertragen.</p>
			</div>
			<ol class="hu-aroundhome__evidence-list">
				<li>
					<span>01</span>
					<div><strong>Eigene CRM- und Vertragsdaten</strong><p>Gewonnene Aufträge, echte Bearbeitungszeit, Reklamationen und Kosten derselben Kohorte.</p></div>
				</li>
				<li>
					<span>02</span>
					<div><strong>Dokumentierter Vergleichsbetrieb</strong><p>Nur hilfreich, wenn Gewerk, Region, Zeitraum und Messmethode Ihrem Fall tatsächlich ähneln.</p></div>
				</li>
				<li>
					<span>03</span>
					<div><strong>Einzelne Online-Erfahrung</strong><p>Gut als Quelle für Prüffragen; zu schwach als Beleg für Preis, Qualität oder Ihren erwartbaren CPO.</p></div>
				</li>
			</ol>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--paper" id="wege" tabindex="-1" data-track-section="aroundhome_paths">
		<div class="hu-aroundhome__inner">
			<div class="hu-aroundhome__section-head hu-aroundhome__section-head--split">
				<div>
					<p class="hu-aroundhome__eyebrow">Kein künstliches Entweder-oder</p>
					<h2>Portal, Hybrid oder eigener Anfrageweg?</h2>
				</div>
				<p>Die Wege unterscheiden sich weniger in „gut“ oder „schlecht“ als in Geschwindigkeit, Kontrolle und Eigentum. Der passende Weg hängt davon ab, welches Problem Ihr Betrieb jetzt lösen muss.</p>
			</div>

			<div class="hu-aroundhome__path-grid">
				<?php foreach ( $path_items as $path_item ) : ?>
					<article class="hu-aroundhome__path-card hu-aroundhome__path-card--<?php echo esc_attr( $path_item['key'] ); ?>">
						<span class="hu-aroundhome__path-label"><?php echo esc_html( $path_item['label'] ); ?></span>
						<h3><?php echo esc_html( $path_item['title'] ); ?></h3>
						<p><?php echo esc_html( $path_item['text'] ); ?></p>
						<small><?php echo esc_html( $path_item['path'] ); ?></small>
					</article>
				<?php endforeach; ?>
			</div>

			<p class="hu-aroundhome__path-note">Entscheidend im Hybrid: Quellen im CRM getrennt halten. Sonst verschwinden Kostenunterschiede in einer gemischten Abschlussquote.</p>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--ink" data-track-section="aroundhome_proof">
		<div class="hu-aroundhome__inner hu-aroundhome__proof">
			<div class="hu-aroundhome__proof-copy">
				<p class="hu-aroundhome__eyebrow">Dokumentierter Mechanismus</p>
				<h2>Ein eigener Weg ist messbar — aber nicht automatisch übertragbar.</h2>
				<p>In einem anonymisierten mittelständischen PV-Installationsbetrieb wurde ein eigener Anfrageweg über <?php echo esc_html( $case_timeframe ); ?> aufgebaut und optimiert. Das ist kein Aroundhome-Vergleich, keine Aussage über Aroundhome-Konditionen und keine Ergebnisgarantie.</p>
				<a href="<?php echo esc_url( $case_url ); ?>" data-track-action="aroundhome_open_case" data-track-category="proof" data-track-section="aroundhome_proof">Methodik und Grenzen des Falls ansehen →</a>
			</div>
			<div class="hu-aroundhome__proof-metrics" aria-label="Kennzahlen des anonymisierten PV-Falls">
				<div><span>Kosten pro Anfrage</span><strong><?php echo esc_html( $case_cpl_before ); ?> → <?php echo esc_html( $case_cpl_after ); ?></strong></div>
				<div><span>Qualifizierte Anfragen</span><strong><?php echo esc_html( $case_leads ); ?></strong></div>
				<div><span>Abschlussquote danach</span><strong><?php echo esc_html( $case_conversion ); ?></strong></div>
				<div><span>Beobachteter Zeitraum</span><strong><?php echo esc_html( $case_timeframe ); ?></strong></div>
			</div>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--paper" id="faq" tabindex="-1" data-track-section="aroundhome_faq">
		<div class="hu-aroundhome__inner hu-aroundhome__faq-layout">
			<div class="hu-aroundhome__section-head">
				<p class="hu-aroundhome__eyebrow">Häufige Fragen</p>
				<h2>Kosten, Erfahrungen und Vertrag — ohne pauschale Anbieterwertung.</h2>
				<p>Die Antworten trennen öffentlich beschriebene Portalmechanik von individuellen Konditionen. Sichtbarer Inhalt und FAQ-Schema stammen aus derselben Quelle.</p>
			</div>

			<div class="hu-aroundhome__faq-list">
				<?php foreach ( $aroundhome_faqs as $faq_index => $faq_item ) : ?>
					<details id="faq-<?php echo esc_attr( $faq_item['key'] ); ?>" data-track-action="toggle_aroundhome_faq" data-track-category="engagement" data-track-section="aroundhome_faq">
						<summary><span><?php echo esc_html( str_pad( (string) ( $faq_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><strong><?php echo esc_html( $faq_item['question'] ); ?></strong></summary>
						<div class="hu-aroundhome__faq-answer"><p><?php echo esc_html( $faq_item['answer'] ); ?></p></div>
					</details>
				<?php endforeach; ?>
			</div>

			<div class="hu-aroundhome__method" data-track-section="aroundhome_methodology">
				<h2>Quellen, Stand und Grenze</h2>
				<ul>
					<li><strong>Anbietermodell:</strong> Öffentliche <a href="<?php echo esc_url( $aroundhome_partner ); ?>" rel="noopener noreferrer" target="_blank" data-track-action="source_aroundhome_partner_method" data-track-category="external_link" data-track-section="aroundhome_methodology">Aroundhome-Partnerseite</a>, abgerufen am 12. August 2026.</li>
					<li><strong>Vertragsmechanik:</strong> Öffentliche <a href="<?php echo esc_url( $aroundhome_terms ); ?>" rel="noopener noreferrer" target="_blank" data-track-action="source_aroundhome_terms" data-track-category="external_link" data-track-section="aroundhome_methodology">AGB für Partnerunternehmen</a>, abgerufen am 12. August 2026. Der konkrete Vertrag kann abweichen.</li>
					<li><strong>Rechenlogik:</strong> Die Seite nutzt ausschließlich Ihre Eingaben. Sie nennt keinen Aroundhome-Preis, keine Qualitätsquote und keine erwartbare Abschlussquote.</li>
					<li><strong>Vertiefung:</strong> <a href="<?php echo esc_url( $tco_url ); ?>" data-track-action="aroundhome_deeper_tco" data-track-category="internal_link" data-track-section="aroundhome_methodology">Portal vs. eigener Weg</a>, <a href="<?php echo esc_url( $solar_url ); ?>" data-track-action="aroundhome_deeper_buy" data-track-category="internal_link" data-track-section="aroundhome_methodology">Solar-Leads kaufen</a> und <a href="<?php echo esc_url( $cpl_url ); ?>" data-track-action="aroundhome_deeper_cpl" data-track-category="internal_link" data-track-section="aroundhome_methodology">CPL-Kostentreiber</a>.</li>
				</ul>
			</div>
		</div>
	</section>

	<section class="hu-aroundhome__band hu-aroundhome__band--ink hu-aroundhome__final" data-track-section="aroundhome_final">
		<div class="hu-aroundhome__inner hu-aroundhome__final-grid">
			<div>
				<p class="hu-aroundhome__eyebrow">Der nächste sinnvolle Schritt</p>
				<h2>Brauchen Sie mehr Leads — oder einen kontrollierbaren Anfrageweg?</h2>
			</div>
			<div>
				<p>Im Marktcheck prüfen wir zuerst, ob Nachfrage, Wirtschaftlichkeit und Vertriebskapazität zu einem eigenen Anfragesystem passen. Das Ergebnis ist eine händische Grün-, Gelb- oder Rot-Einordnung mit dem nächsten sinnvollen Schritt.</p>
				<a class="hu-aroundhome__button hu-aroundhome__button--primary" href="<?php echo esc_url( $analysis_url ); ?>" data-track-action="cta_aroundhome_final_marktcheck" data-track-category="lead_gen" data-track-section="aroundhome_final">Marktcheck mit Fit-Entscheid starten</a>
				<p class="hu-aroundhome__byline">Anbieterunabhängige Einordnung von <?php echo esc_html( get_the_author() ); ?> · Haşim Üner bietet als Alternative eigene Anfragesysteme an · Quellenstand 12. August 2026</p>
			</div>
		</div>
	</section>
</article>
