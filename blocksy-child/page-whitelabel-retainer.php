<?php
/**
 * Template Name: Whitelabel & Weiterentwicklung
 * Description: White-Label-Partner-Funnel für Agenturen — Diagnose-first:
 *              Engpass → Erstprojekt (fixer Scope + Preis) → Fit-Check vor Call.
 *              Hero rechts zeigt die Ablauf-Kette (wird im Folge-PR animiert).
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$whitelabel_fit_url = function_exists( 'nexus_get_whitelabel_calendar_url' )
	? nexus_get_whitelabel_calendar_url()
	: 'https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true';
$mailto_url = 'mailto:hallo@hasimuener.de';

$e3_case_url  = function_exists( 'hu_e3_canon' ) ? (string) ( hu_e3_canon()['url'] ?? home_url( '/case-study-solar-leadgenerierung/' ) ) : home_url( '/case-study-solar-leadgenerierung/' );
$portrait_url = function_exists( 'hu_get_portrait_image_url' )
	? hu_get_portrait_image_url()
	: home_url( '/wp-content/uploads/2026/01/Hasim-Uener-Prtraeit_Startseite.webp' );

$cpl_before = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before', 'display', '150 €' ) : '150 €';
$cpl_after  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after', 'display', '22 €' ) : '22 €';
$timeframe  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display_dative', '6 Monaten' ) : '6 Monaten';

$problem_cards = [
	[
		'eyebrow' => 'Kapazität',
		'title'   => 'Deadline steht. Team ist voll.',
		'copy'    => 'Akquise, Strategie, Kundentermine — euer Kalender ist dicht. Die technische Umsetzung rutscht in den Abend, und die Deadline rückt trotzdem näher.',
	],
	[
		'eyebrow' => 'Tiefe',
		'title'   => 'Technische Spezialfälle blockieren die Delivery.',
		'copy'    => 'Server-Side, Consent Mode V2 oder Core Web Vitals brauchen Spezialwissen und klare Verantwortung. Fehlt intern die Kapazität, geraten Qualität, Dokumentation und Abnahme unter Zeitdruck.',
	],
	[
		'eyebrow' => 'Marge',
		'title'   => 'Zusätzliche Senior-Kapazität muss planbar bleiben.',
		'copy'    => 'Eine Festanstellung bringt Such- und Auslastungsrisiko mit. Projektweise Unterstützung braucht klare Briefings, einheitliche Qualitätsstandards und eine eindeutig geregelte Verantwortung.',
	],
];

// ── Hero-Ablauf-Kette: vier Stationen eines Mandats ─────────────
// Statisches HTML; wird im Folge-PR zur animierten Inline-SVG.
$flow_steps = [
	[ 'num' => '01', 'title' => 'Ihr Kunde', 'desc' => 'Anfrage an eure Agentur' ],
	[ 'num' => '02', 'title' => 'Eure Agentur', 'desc' => 'Kundenkontakt bleibt bei euch' ],
	[ 'num' => '03', 'title' => 'Umsetzung im Hintergrund', 'desc' => 'Tracking · Landingpage · CRO' ],
	[ 'num' => '04', 'title' => 'Ergebnis unter eurem Namen', 'desc' => 'Euer Branding, eure Marge' ],
];

// ── Erstprojekte: fixer Scope, Preis vorab (Einstieg vor Retainer) ──
$entry_projects = [
	[
		'tag'             => 'WordPress',
		'title'           => 'WordPress-Test-Sprint',
		'price'           => '590 € netto',
		'duration'        => 'max. ein Arbeitstag',
		'deliver'         => 'Umsetzung, Funktionstest, kurze technische Dokumentation und eine Korrekturrunde',
		'copy'            => 'Eine vorab schriftlich abgegrenzte technische Aufgabe als kleinster Einstieg in die erste Zusammenarbeit.',
		'suitable'        => [
			'Template- oder Komponentenanpassung',
			'kleiner PHP-/JS-/CSS-Fix',
			'Performanceproblem',
			'technischer SEO-Fix',
			'fehlerhafte WordPress-Funktion',
			'Umsetzung eines vorhandenen Layouts',
		],
		'excluded'        => [
			'Relaunch',
			'vollständige Landingpage',
			'vollständiges Tracking-Setup',
			'unbegrenzte Korrekturen',
			'Lizenz- oder Drittanbieterkosten',
		],
		'scope_note'      => 'Der Aufgabenumfang wird vor Beginn schriftlich festgelegt. Erweiterungen sind ein eigenes Angebot.',
		'intro_price_note' => 'Einmaliger Einstiegspreis für die erste Zusammenarbeit.',
	],
	[
		'tag'      => 'Tracking',
		'title'    => 'Tracking-Audit',
		'price'    => 'ab 1.900 € netto',
		'duration' => '1 Woche',
		'deliver'  => 'Schriftlicher Befund + priorisierte Fixliste',
		'copy'     => 'GA4, GTM und Consent-Bestand eures Kunden geprüft: Was misst, was fehlt, was verfälscht. Danach wisst ihr, worauf jede weitere Maßnahme aufsetzt.',
	],
	[
		'tag'      => 'Server-Side',
		'title'    => 'Server-Side-Setup',
		'price'    => 'ab 2.900 € netto',
		'duration' => '1–2 Wochen',
		'deliver'  => 'Produktives Setup + Doku + Übergabe',
		'copy'     => 'Eigener Server-Side-Container, Enhanced Conversions, Meta CAPI, Consent Mode V2 — produktiv geschaltet und dokumentiert, nicht nur konfiguriert.',
	],
	[
		'tag'      => 'Landingpage',
		'title'    => 'Landingpage',
		'price'    => 'ab 3.900 € netto',
		'duration' => '2 Wochen',
		'deliver'  => 'Live-Page + Doku, bereit für Traffic',
		'copy'     => 'Individuelles Template mit klarer Funnel-Logik, sauberen Core Web Vitals und belastbarer Messbarkeit ab dem ersten Klick.',
	],
];

$entry_bullets = [ 'NDA', 'Fixer Scope', 'Preis vorab fix', 'Keine Verlängerungsfalle' ];

$solution_modes = [
	'hintergrund' => [
		'label' => 'Im Hintergrund',
		'copy'  => 'Komplett unsichtbar. Code, Tracking, Doku laufen über euch — euer Ton, euer Branding. Euer Kunde sieht nur das Ergebnis.',
	],
	'kundencall'  => [
		'label' => 'Mit im Kunden-Call',
		'copy'  => 'Als euer Senior-Tech-Lead direkt im Call. Technikfragen sofort beantwortet, ohne Übersetzungsschleife — der Kunde hört Kompetenz, ihr erntet sie.',
	],
];

// ── Lieferfelder: zwei Gruppen statt Leistungskatalog ───────────
$stack_groups = [
	[
		'title' => 'Anfrage-System-Handwerk',
		'cards' => [
			[
				'tag'   => 'Tracking',
				'title' => 'Tracking-Fundament',
				'copy'  => 'GA4 + GTM, eigener Server-Side-Container (Stape/GCP), Enhanced Conversions, Meta CAPI, Consent Mode V2 — eine Datenstrecke vom Klick bis zum Abschluss.',
				'chips' => [ 'GA4', 'GTM-SS', 'CAPI', 'Consent V2' ],
				'entry' => 'Typisches Erstprojekt: Tracking-Audit oder Server-Side-Setup.',
			],
			[
				'tag'   => 'CRO',
				'title' => 'Landingpages & CRO',
				'copy'  => 'Individuelle Templates mit klarer Funnel-Logik, belastbaren A/B-Hypothesen und schneller Ladezeit.',
				'chips' => [ 'LP', 'A/B', 'Funnel', 'Speed' ],
				'entry' => 'Typisches Erstprojekt: eine Landingpage, bereit für Traffic.',
			],
			[
				'tag'   => 'Ops',
				'title' => 'Automation & CRM',
				'copy'  => 'n8n, Make, Zapier; CRM-Attribution in Bitrix24, HubSpot, Pipedrive. Die Lead-Strecke endet im Abschluss, nicht im Postfach.',
				'chips' => [ 'n8n', 'Make', 'CRM', 'Attribution' ],
				'entry' => 'Typisches Erstprojekt: Lead-Routing bis ins CRM.',
			],
		],
	],
	[
		'title' => 'Technisches Fundament',
		'cards' => [
			[
				'tag'   => 'SEO',
				'title' => 'Technical SEO',
				'copy'  => 'Audits, Indexierung, Logfile-Analysen, Schema, hreflang — Architektur, die trägt, statt Checklisten-Kosmetik.',
				'chips' => [ 'Audit', 'Schema', 'hreflang', 'Logfiles' ],
				'entry' => 'Typisch im Retainer: fortlaufende technische Betreuung.',
			],
			[
				'tag'   => 'CWV',
				'title' => 'Performance & Core Web Vitals',
				'copy'  => 'Hardening, bedarfsgesteuertes Asset-Loading und ein optimierter kritischer Renderpfad — WordPress-spezifisch umgesetzt und dokumentiert.',
				'chips' => [ 'CWV', 'Caching', 'Hardening', 'Templates' ],
				'entry' => 'Typisch im Retainer: CWV-Budget halten, Release für Release.',
			],
		],
	],
];

$contract_cards = [
	[
		'eyebrow' => '01',
		'title'   => 'Diskret',
		'copy'    => 'NDA standardmäßig, kein eigenes Branding, keine Akquise in eurem Kundenstamm. Die Sichtbarkeit legt ihr fest — vom unsichtbaren Backoffice bis zum Technik-Lead in eurem Call.',
		'bullets' => [ 'NDA inkludiert', 'Kein eigenes Branding', 'Sichtbarkeit: ihr entscheidet' ],
	],
	[
		'eyebrow' => '02',
		'title'   => 'Verbindlich',
		'copy'    => 'Rückmeldung in der Regel innerhalb eines Werktags. Verfügbarkeit, Starttermin und Delivery-Fenster werden vor Projektbeginn verbindlich vereinbart. Dringende Aufgaben werden vorab separat priorisiert und bestätigt.',
		'bullets' => [ 'Starttermin vereinbart', 'Delivery-Fenster verbindlich', 'Dringendes separat bestätigt' ],
	],
	[
		'eyebrow' => '03',
		'title'   => 'Planbar',
		'copy'    => 'Monats-Retainer mit vereinbartem Leistungsrahmen oder feste Projektpreise für abgegrenzte Aufgaben. Saubere Dokumentation schafft klare Übergaben.',
		'bullets' => [ 'Projektpreis vorab', 'Klare Doku', 'Nachvollziehbare Abrechnung' ],
	],
];

$comparison_columns = [ 'Senior einstellen', 'Projektweise extern', 'White-Label-Partner' ];

$comparison_rows = [
	[
		'label' => 'Verfügbar ab',
		'cells' => [
			'Monate: Suche, Kündigungsfrist, Einarbeitung',
			'Abhängig von der vereinbarten externen Kapazität',
			'Nach einem Fit-Gespräch und NDA',
		],
	],
	[
		'label' => 'Fixkostenrisiko',
		'cells' => [
			'Volles Gehalt, auch in schwachen Monaten',
			'Projektbezogene Kosten ohne laufende Personalbindung',
			'Retainer oder Projektpreis — passend zum vereinbarten Bedarf',
		],
	],
	[
		'label' => 'Skill-Breite',
		'cells' => [
			'Eine Person, ein Schwerpunkt',
			'Je nach beauftragtem Profil und Projektscope',
			'SEO, WordPress, Tracking, CRO, Automation aus einer Hand',
		],
	],
	[
		'label' => 'Qualität',
		'cells' => [
			'Zeigt sich nach der Probezeit',
			'Standards und Dokumentation werden pro Auftrag vereinbart',
			'Ein Standard — prüfbar am Erstprojekt',
		],
	],
	[
		'label' => 'Diskretion',
		'cells' => [
			'Intern — Know-how geht mit der Person',
			'NDA und Sichtbarkeit werden pro Auftrag geregelt',
			'NDA standardmäßig, Sichtbarkeit wählbar',
		],
	],
	[
		'label' => 'Verantwortung',
		'cells' => [
			'Führung und QA liegen bei euch',
			'Koordination und Abnahme werden pro Auftrag festgelegt',
			'Lieferung inklusive Doku und Abnahme',
		],
	],
];

$founder_chips = [ 'Hannover', 'NDA standardmäßig', 'Rückmeldung in der Regel innerhalb eines Werktags' ];

$faq_items = [
	[
		'key' => 'abrechnung',
		'q'   => 'Wie rechnest du ab — Retainer oder Projekt?',
		'a'   => 'Der WordPress-Test-Sprint ist mit 590 € netto der kleinste Einstieg. Größere Erstprojekte beginnen mit dem Tracking-Audit ab 1.900 € netto; Server-Side-Setup und Landingpage starten bei den auf dieser Seite genannten Preisen. Ein Retainer entsteht erst nach einem erfolgreichen Erstprojekt und erhält einen vorab vereinbarten Leistungsrahmen.',
	],
	[
		'key' => 'sichtbarkeit',
		'q'   => 'Was passiert, wenn unser Kunde fragt, wer die Technik macht?',
		'a'   => 'Das legt ihr vorab fest. Standard: Backoffice — ihr antwortet als Team. Auf Wunsch stellt ihr euren Technik-Lead vor, und die Fragen werden direkt im Call beantwortet, unter eurem Branding. Was nie passiert: Akquise in eurem Kundenstamm. Das steht im Kontrakt.',
	],
	[
		'key' => 'pitch',
		'q'   => 'Gibt es Support schon im Pitch — bevor der Kunde unterschrieben hat?',
		'a'   => 'Ja. Scope, Aufwandsschätzung, technische Machbarkeit — ihr bekommt belastbare Zahlen für euer Angebot, bevor ihr etwas verkauft, das nachher niemand bauen kann. Den Rahmen dafür klären wir im Fit-Gespräch.',
	],
	[
		'key' => 'zugaenge',
		'q'   => 'Wie laufen Zugänge zu GA4, GTM, Server & Co.?',
		'a'   => 'Über eure Accounts — nie andersherum. Ihr vergebt den Zugang (eigene E-Mail, 2FA) und entzieht ihn, wann ihr wollt. Properties, Container und Server-Setups gehören euch oder eurem Kunden. Gebaut wird darin, nicht daneben.',
	],
	[
		'key' => 'kapazitaet',
		'q'   => 'Wie schnell reagierst du — und was ist mit Kapazität?',
		'a'   => 'Rückmeldung in der Regel innerhalb eines Werktags. Verfügbarkeit, Starttermin und Delivery-Fenster werden vor Projektbeginn verbindlich vereinbart. Dringende Aufgaben werden vorab separat priorisiert und bestätigt.',
	],
	[
		'key' => 'ownership',
		'q'   => 'Wem gehören Code, Setups und Daten?',
		'a'   => 'Euch beziehungsweise eurem Kunden. Versionierter Code, dokumentierte Übergabe, Zugänge in euren Accounts. Es gibt keine Black Box und keinen Lock-in — das ist Absicht.',
	],
	[
		'key' => 'exit',
		'q'   => 'Wie kommen wir wieder raus?',
		'a'   => 'Projekte enden mit der Abnahme, Retainer ohne Verlängerungsfalle. Durch Doku und Ownership in euren Accounts könnt ihr jederzeit intern übernehmen oder wechseln. Partner bleiben, weil die Lieferung stimmt — nicht, weil der Ausstieg wehtut.',
	],
	[
		'key' => 'start',
		'q'   => 'Wie schnell können wir starten?',
		'a'   => 'Verfügbarkeit, Starttermin und Delivery-Fenster werden vor Projektbeginn verbindlich vereinbart. Nach dem Fit-Gespräch folgen NDA, Zugänge und ein Erstprojekt mit fixem Scope. Erst nach dessen erfolgreichem Abschluss entscheidet ihr über ein weiteres Projekt oder einen Retainer.',
	],
	[
		'key' => 'recht',
		'q'   => 'Arbeitest du als Subunternehmer — und wie sauber ist das rechtlich?',
		'a'   => 'Ja, klassisches Subunternehmer-Verhältnis: Vertrag mit eurer Agentur, nicht mit eurem Kunden. NDA gehört zum Standard, Auftragsverarbeitung nach DSGVO, wo personenbezogene Daten im Spiel sind. Rechnung an euch — eure Marge bleibt eure Sache.',
	],
];

$tech_bullets = [
	'Individuelle WordPress-Templates für vorhandene Layouts',
	'Performance-Optimierung: Core Web Vitals, kritischer Renderpfad',
	'Versionierter Code, dokumentierte Übergabe',
];

$hero_chips = [ 'GA4', 'GTM', 'Server-Side', 'Consent V2', 'WordPress', 'n8n' ];

// ── Fit-Check: 3 Klickfragen vor der Terminbuchung ──────────────
// Rein clientseitig (kein REST, keine Speicherung) — Antworten sind
// Enum-Werte und fließen nur in data-track-Events + mailto-Vorlage.
// Frage 2 spiegelt bewusst die drei Problem-Karten.
$fitcheck_steps = [
	[
		'key'      => 'agenturtyp',
		'question' => 'Was für eine Agentur seid ihr?',
		'options'  => [
			'performance' => 'Performance / Paid',
			'webdesign'   => 'Webdesign / WordPress',
			'fullservice' => 'Full-Service / Brand',
			'solo'        => 'Solo mit Freelancer-Netzwerk',
		],
	],
	[
		'key'      => 'engpass',
		'question' => 'Wo drückt es gerade am meisten?',
		'options'  => [
			'kapazitaet' => 'Kapazität — Kalender dicht, Deadline steht',
			'tiefe'      => 'Tiefe — Tracking/SEO/CWV über unserem Level',
			'marge'      => 'Marge — ein Senior-Hire rechnet sich nicht',
		],
	],
	[
		'key'      => 'anlass',
		'question' => 'Was ist der Anlass?',
		'options'  => [
			'projekt'     => 'Ein konkretes Kundenprojekt steht an',
			'vorsorglich' => 'Vorsorglich Kapazität sichern',
		],
	],
];
?>

<main id="main" class="site-main wl-page" data-track-section="whitelabel_proof">

	<noscript>
		<style>
			.wl-page .nx-reveal,
			.wl-page .reveal-stagger > * { opacity: 1; transform: none; }
		</style>
	</noscript>

	<!-- ═══════════════════════════════════════════════
	     SECTION 01 — HERO (dark, Ablauf-Kette rechts)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-hero" data-nx-theme="dark" id="hero">
		<div class="wl-hero__bg" aria-hidden="true">
			<div class="wl-hero__bg-warmth"></div>
			<div class="wl-hero__bg-vignette"></div>
			<div class="wl-hero__bg-grid"></div>
		</div>

		<div class="nx-container">
			<header class="wl-hero__top">
				<span class="wl-hero__mark">
					<span class="wl-hero__mark-rule" aria-hidden="true"></span>
					White-Label-Partner für Agenturen
				</span>
				<span class="wl-hero__status">
					<span class="wl-status-dot" aria-hidden="true"></span>
					Aktive White-Label-Mandate
				</span>
			</header>

			<div class="wl-hero__grid">
				<div class="wl-hero__copy">
					<h1 class="wl-hero__title">
						<span class="wl-hero__title-line">Ihr verkauft es.</span>
						<span class="wl-hero__title-line">Ich liefere es.</span>
						<span class="wl-hero__title-line wl-hero__title-line--em">Euer Name steht drauf.</span>
					</h1>
					<p class="wl-hero__lede">
						Individuelle WordPress-Umsetzung, Performance, technisches SEO und Tracking — diskret unter eurem Namen. Unsichtbar im Hintergrund oder als euer Technik-Lead im Call: ihr entscheidet pro Projekt.
					</p>

					<div class="wl-hero__actions">
						<a href="#fit-check" class="nx-btn nx-btn--primary" data-track-action="cta_whitelabel_hero_to_fitcheck" data-track-category="navigation" data-track-section="hero">
							<span>Fit-Check starten — 3 Fragen, 60 Sekunden</span>
							<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
								<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
						<a href="#proof" class="nx-btn nx-btn--ghost" data-track-action="cta_whitelabel_hero_proof" data-track-category="navigation" data-track-section="hero">
							Arbeitsprobe ansehen
						</a>
					</div>

					<p class="wl-hero__fineprint">
						30&nbsp;Min direkt mit mir · NDA möglich · keine Verkaufsshow.
					</p>
				</div>

				<aside class="wl-flow" aria-label="Ablauf eines White-Label-Mandats">
					<header class="wl-flow__head">
						<span class="wl-flow__kicker">Der Ablauf</span>
						<span class="wl-flow__sub">Ein Mandat, vier Stationen</span>
					</header>
					<ol class="wl-flow__list">
						<?php foreach ( $flow_steps as $flow_i => $step ) : ?>
							<li class="wl-flow__step<?php echo 3 === $flow_i ? ' wl-flow__step--final' : ''; ?>">
								<span class="wl-flow__num"><?php echo esc_html( $step['num'] ); ?></span>
								<div class="wl-flow__body">
									<span class="wl-flow__title"><?php echo esc_html( $step['title'] ); ?></span>
									<span class="wl-flow__desc"><?php echo esc_html( $step['desc'] ); ?></span>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
					<div class="wl-flow__chips" role="list" aria-label="Stack-Komponenten">
						<?php foreach ( $hero_chips as $chip ) : ?>
							<span class="wl-chip" role="listitem"><?php echo esc_html( $chip ); ?></span>
						<?php endforeach; ?>
					</div>
				</aside>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 02 — PROBLEM
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-problem" id="problem">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Problem</span>
				<h2 class="nx-headline-section">Verkaufen könnt ihr. Liefern ist der Engpass.</h2>
				<p class="wl-section-lede">Drei Engpässe, die jede Performance- und Webdesign-Agentur kennt. Alle drei lösbar — ohne eine einzige Neueinstellung.</p>
			</div>

			<div class="wl-problem__grid reveal-stagger">
				<?php foreach ( $problem_cards as $card ) : ?>
					<article class="wl-problem-card">
						<span class="wl-eyebrow"><?php echo esc_html( $card['eyebrow'] ); ?></span>
						<h3 class="wl-problem-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="wl-problem-card__copy"><?php echo esc_html( $card['copy'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 03 — EINSTIEG (Erstprojekte mit Preis)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-entry" id="einstieg">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Einstieg</span>
				<h2 class="nx-headline-section">Kein Blind-Retainer. Erst ein Erstprojekt mit fixem Scope.</h2>
				<p class="wl-section-lede">Der WordPress-Test-Sprint ist der kleinste Einstieg. Größere Erstprojekte folgen mit fester Lieferung und Preis vorab. Ein Retainer entsteht erst nach einem erfolgreichen Erstprojekt.</p>
			</div>

			<div class="wl-entry__grid reveal-stagger">
				<?php foreach ( $entry_projects as $project ) : ?>
					<article class="wl-entry-card">
						<header class="wl-entry-card__head">
							<span class="wl-entry-card__tag"><?php echo esc_html( $project['tag'] ); ?></span>
							<span class="wl-entry-card__price"><?php echo esc_html( $project['price'] ); ?></span>
						</header>
						<h3 class="wl-entry-card__title"><?php echo esc_html( $project['title'] ); ?></h3>
						<p class="wl-entry-card__copy"><?php echo esc_html( $project['copy'] ); ?></p>
						<?php if ( ! empty( $project['suitable'] ) ) : ?>
							<div class="wl-entry-card__scope">
								<h4>Geeignet für</h4>
								<ul>
									<?php foreach ( $project['suitable'] as $item ) : ?>
										<li><?php echo esc_html( $item ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $project['excluded'] ) ) : ?>
							<div class="wl-entry-card__scope wl-entry-card__scope--excluded">
								<h4>Nicht enthalten</h4>
								<ul>
									<?php foreach ( $project['excluded'] as $item ) : ?>
										<li><?php echo esc_html( $item ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<dl class="wl-entry-card__meta">
							<div>
								<dt>Laufzeit</dt>
								<dd><?php echo esc_html( $project['duration'] ); ?></dd>
							</div>
							<div>
								<dt>Lieferung</dt>
								<dd><?php echo esc_html( $project['deliver'] ); ?></dd>
							</div>
						</dl>
						<?php if ( ! empty( $project['scope_note'] ) ) : ?>
							<p class="wl-entry-card__note"><?php echo esc_html( $project['scope_note'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $project['intro_price_note'] ) ) : ?>
							<p class="wl-entry-card__note wl-entry-card__note--price"><?php echo esc_html( $project['intro_price_note'] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>

			<ul class="wl-entry__bullets nx-reveal" aria-label="Rahmen Erstprojekt">
				<?php foreach ( $entry_bullets as $bullet ) : ?>
					<li><span class="wl-entry__bullet-dot" aria-hidden="true"></span><?php echo esc_html( $bullet ); ?></li>
				<?php endforeach; ?>
			</ul>

			<div class="wl-entry__actions nx-reveal">
				<a href="#fit-check" class="nx-btn nx-btn--primary" data-track-action="cta_whitelabel_entry_to_fitcheck" data-track-category="navigation" data-track-section="entry">
					Fit-Check starten — 3 Fragen, 60 Sekunden
				</a>
				<p class="wl-entry__retainer-note">Wenn das Erstprojekt erfolgreich abgeschlossen ist, kann daraus ein Monats-Retainer mit vorab vereinbartem Leistungsrahmen entstehen.</p>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 04 — LÖSUNG (Arbeitsmodus-Toggle)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-solution" data-nx-theme="dark" id="loesung">
		<div class="nx-container">
			<div class="wl-section-header wl-section-header--center nx-reveal">
				<span class="wl-eyebrow">Arbeitsmodus</span>
				<h2 class="nx-headline-section">Zwei Modi. Volle Kontrolle.</h2>
				<p class="wl-section-lede">Sichtbarkeit ist eine Einstellung, keine Grenze — ihr entscheidet pro Projekt.</p>
			</div>

			<div class="wl-mode" id="wl-mode" data-wl-mode="hintergrund">
				<div class="wl-mode__control nx-reveal">
					<fieldset class="wl-mode__switch">
						<legend class="wl-visually-hidden">Arbeitsmodus wählen</legend>
						<?php foreach ( $solution_modes as $mode_key => $mode ) : ?>
							<input
								type="radio"
								name="wl-mode"
								id="wl-mode-<?php echo esc_attr( $mode_key ); ?>"
								class="wl-mode__radio"
								value="<?php echo esc_attr( $mode_key ); ?>"
								<?php checked( 'hintergrund' === $mode_key ); ?>
							/>
							<label
								for="wl-mode-<?php echo esc_attr( $mode_key ); ?>"
								class="wl-mode__label"
								data-track-action="toggle_whitelabel_mode_<?php echo esc_attr( $mode_key ); ?>"
								data-track-category="engagement"
								data-track-section="loesung"
							><?php echo esc_html( $mode['label'] ); ?></label>
						<?php endforeach; ?>
					</fieldset>
					<p class="wl-mode__hint">Pro Projekt wählbar, jederzeit umstellbar.</p>
				</div>

				<div class="wl-mode__panels">
					<?php foreach ( $solution_modes as $mode_key => $mode ) : ?>
						<div class="wl-mode__panel wl-mode__statement" data-mode-panel="<?php echo esc_attr( $mode_key ); ?>" aria-label="<?php echo esc_attr( 'Modus: ' . $mode['label'] ); ?>">
							<p><?php echo esc_html( $mode['copy'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>

				<p class="wl-mode__footer">Egal wie: euer Branding, eure Marge, eure Kundenbeziehung bleiben bei euch.</p>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 05 — LIEFERFELDER (zwei Gruppen)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-stack" id="lieferfelder">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Lieferfelder</span>
				<h2 class="nx-headline-section">Ein System, keine Einzelteile.</h2>
				<p class="wl-section-lede">Alles zahlt auf denselben Zweck ein: Kundenprojekte, die messbar Anfragen bringen. Einzeln als Erstprojekt, kombiniert im Retainer — immer unter eurem Namen.</p>
			</div>

			<?php foreach ( $stack_groups as $group ) : ?>
				<div class="wl-stack__group">
					<h3 class="wl-stack__group-title nx-reveal"><?php echo esc_html( $group['title'] ); ?></h3>
					<div class="wl-stack__grid reveal-stagger">
						<?php foreach ( $group['cards'] as $card ) : ?>
							<article class="wl-stack-card">
								<header class="wl-stack-card__head">
									<span class="wl-stack-card__tag"><?php echo esc_html( $card['tag'] ); ?></span>
								</header>
								<h4 class="wl-stack-card__title"><?php echo esc_html( $card['title'] ); ?></h4>
								<p class="wl-stack-card__copy"><?php echo esc_html( $card['copy'] ); ?></p>
								<ul class="wl-stack-card__chips" aria-label="Komponenten">
									<?php foreach ( $card['chips'] as $chip ) : ?>
										<li><span class="wl-chip wl-chip--mono"><?php echo esc_html( $chip ); ?></span></li>
									<?php endforeach; ?>
								</ul>
								<p class="wl-stack-card__entry"><?php echo esc_html( $card['entry'] ); ?></p>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 06 — KONTRAKT / SICHERHEIT
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-contract" id="kontrakt">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">White-Label-Kontrakt</span>
				<h2 class="nx-headline-section">Diskret. Verbindlich. Planbar.</h2>
				<p class="wl-section-lede">Drei Regeln, schriftlich im Kontrakt — prüfbar ab der ersten Minute, ohne Sternchen.</p>
			</div>

			<div class="wl-contract__grid reveal-stagger">
				<?php foreach ( $contract_cards as $card ) : ?>
					<article class="wl-contract-card">
						<span class="wl-contract-card__num"><?php echo esc_html( $card['eyebrow'] ); ?></span>
						<h3 class="wl-contract-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="wl-contract-card__copy"><?php echo esc_html( $card['copy'] ); ?></p>
						<ul class="wl-contract-card__bullets">
							<?php foreach ( $card['bullets'] as $bullet ) : ?>
								<li><?php echo esc_html( $bullet ); ?></li>
							<?php endforeach; ?>
						</ul>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 07 — VERGLEICH (Einordnung)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-compare-section" id="vergleich">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Einordnung</span>
				<h2 class="nx-headline-section">Senior einstellen, projektweise extern vergeben — oder andocken?</h2>
				<p class="wl-section-lede">Drei Wege zu Senior-Kapazität. Ohne Fantasiezahlen — die Unterschiede liegen in Anlaufzeit, Risiko und Tiefe.</p>
			</div>

			<div class="wl-compare-wrap nx-reveal">
				<table class="wl-compare">
					<caption class="wl-visually-hidden">Vergleich der drei Wege zu Senior-Kapazität: Senior einstellen, projektweise extern vergeben, White-Label-Partner</caption>
					<thead>
						<tr>
							<th scope="col" class="wl-compare__crit"><span class="wl-visually-hidden">Kriterium</span></th>
							<?php foreach ( $comparison_columns as $i => $column ) : ?>
								<th scope="col"<?php echo 2 === $i ? ' class="wl-compare__col--hl"' : ''; ?>>
									<?php echo esc_html( $column ); ?>
									<?php if ( 2 === $i ) : ?>
										<span class="wl-compare__chip">Dieses Modell</span>
									<?php endif; ?>
								</th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $comparison_rows as $row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $row['label'] ); ?></th>
								<?php foreach ( $row['cells'] as $i => $cell ) : ?>
									<td data-label="<?php echo esc_attr( $comparison_columns[ $i ] ); ?>"<?php echo 2 === $i ? ' class="wl-compare__cell--hl"' : ''; ?>><?php echo esc_html( $cell ); ?></td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 08 — ARBEITSPROBE (offengelegter Case)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-proof" data-nx-theme="dark" id="proof">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Arbeitsprobe · offengelegt</span>
				<h2 class="nx-headline-section">Keine Referenz-Logos. Eine offengelegte Arbeitsprobe.</h2>
				<p class="wl-section-lede">Was ich unter eurem Namen liefere, bleibt unter eurem Namen — NDA. Deshalb zeige ich ein eigenes, offengelegtes Projekt bis in die Zahlen: ein mittelständischer PV-Installationsbetrieb — Server-Side-Tracking, Consent Mode V2, CRM-Attribution, Landingpage.</p>
			</div>

			<div class="wl-proof__grid nx-reveal" role="list" aria-label="Kennzahl der Arbeitsprobe">
				<div class="wl-proof__item" role="listitem">
					<div class="wl-proof__value"><?php echo esc_html( $cpl_before ); ?>&nbsp;→&nbsp;<?php echo esc_html( $cpl_after ); ?></div>
					<div class="wl-proof__label">Kosten pro qualifizierter Anfrage · in <?php echo esc_html( $timeframe ); ?></div>
				</div>
			</div>

			<p class="wl-proof__case-link nx-reveal">
				<a href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="link_whitelabel_case_e3" data-track-category="navigation" data-track-section="proof">
					Case im Detail ansehen
					<svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
						<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</a>
			</p>

			<p class="wl-proof__disclaimer">Stand 2024–2025 · kein White-Label-Mandat, sondern eigenes Projekt · keine pauschale Übertragbarkeitsgarantie.</p>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 09 — WER LIEFERT (Founder-Strip)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-founder" id="person">
		<div class="nx-container">
			<div class="wl-founder__grid nx-reveal">
				<figure class="wl-founder__media">
					<img
						class="wl-founder__photo"
						src="<?php echo esc_url( $portrait_url ); ?>"
						alt="Haşim Üner, White-Label-Partner für Agenturen"
						width="640"
						height="800"
						loading="lazy"
						decoding="async"
					/>
				</figure>
				<div class="wl-founder__body">
					<span class="wl-eyebrow">Wer liefert</span>
					<h2 class="nx-headline-section">Eine Person. Kein Ticketsystem.</h2>
					<p class="wl-founder__copy">
						Kein Delivery-Team, keine Vermittlungsplattform. Haşim Üner — Technical SEO, Server-Side-Tracking, WordPress &amp; Core Web Vitals, Landingpages, Automation. Die Arbeitsprobe oben: von der Tracking-Architektur bis zur letzten Zeile Code aus einer Hand. Im Fit-Gespräch sitzt ihr mit genau der Person, die nachher euren Code schreibt.
					</p>
					<ul class="wl-founder__chips" aria-label="Rahmendaten">
						<?php foreach ( $founder_chips as $chip ) : ?>
							<li><span class="wl-chip"><?php echo esc_html( $chip ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 10 — TECHNISCHER BELEG
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-tech" id="technik">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Technischer Beleg</span>
				<h2 class="nx-headline-section">Nachvollziehbarer Code, gezielte Abhängigkeiten.</h2>
				<p class="wl-section-lede">Individuelle Templates, bedarfsgesteuertes Asset-Loading und dokumentierte Übergabe — passend zum vorhandenen WordPress-Setup.</p>
			</div>

			<div class="wl-tech__split nx-reveal">
				<ul class="wl-tech__bullets">
					<?php foreach ( $tech_bullets as $bullet ) : ?>
						<li><?php echo esc_html( $bullet ); ?></li>
					<?php endforeach; ?>
				</ul>

				<figure class="wl-tech__code" aria-hidden="true">
					<figcaption class="wl-tech__code-head">
						<span class="wl-tech__code-dot wl-tech__code-dot--r"></span>
						<span class="wl-tech__code-dot wl-tech__code-dot--y"></span>
						<span class="wl-tech__code-dot wl-tech__code-dot--g"></span>
						<span class="wl-tech__code-file">inc/enqueue.php</span>
					</figcaption>
<pre class="wl-tech__code-body"><span class="wl-tech__c">// Bedarfsgesteuertes Asset-Loading pro Template</span>
<span class="wl-tech__k">if</span> ( is_page_template( <span class="wl-tech__s">'page-whitelabel.php'</span> ) ) {
    hu_enqueue_css( <span class="wl-tech__s">'whitelabel'</span>, <span class="wl-tech__s">'whitelabel.css'</span>, [ <span class="wl-tech__s">'design-system'</span> ] );
    hu_enqueue_js(  <span class="wl-tech__s">'whitelabel'</span>,  <span class="wl-tech__s">'whitelabel.js'</span> );
}

<span class="wl-tech__c">// Server-Side Event auf Lead-Submit</span>
window.dataLayer.push({
    <span class="wl-tech__p">event</span>: <span class="wl-tech__s">'lead_qualified'</span>,
    <span class="wl-tech__p">source</span>: <span class="wl-tech__s">'whitelabel_partner'</span>,
    <span class="wl-tech__p">value</span>: leadValue
});</pre>
				</figure>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 11 — FAQ (Einwände vor dem Gespräch)
	     nexus-core initFaqAccordion() wirkt dokument-weit auf <details>:
	     diese FAQ muss die einzige <details>-Gruppe der Seite bleiben.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-faq-section" id="faq">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">FAQ · Zusammenarbeit</span>
				<h2 class="nx-headline-section">Was Agenturen vor dem ersten Projekt wissen wollen.</h2>
			</div>

			<div class="wl-faq reveal-stagger">
				<?php foreach ( $faq_items as $item ) : ?>
					<details class="wl-faq__item">
						<summary class="wl-faq__summary" data-track-action="faq_whitelabel_open" data-track-label="<?php echo esc_attr( $item['key'] ); ?>" data-track-category="engagement" data-track-section="faq">
							<span class="wl-faq__q"><?php echo esc_html( $item['q'] ); ?></span>
							<span class="wl-faq__icon" aria-hidden="true"></span>
						</summary>
						<div class="wl-faq__answer">
							<p><?php echo esc_html( $item['a'] ); ?></p>
						</div>
					</details>
				<?php endforeach; ?>
			</div>

			<p class="wl-faq__more">
				Eure Frage fehlt? Direkter Draht:
				<a href="<?php echo esc_url( $mailto_url ); ?>" data-track-action="mail_whitelabel_faq" data-track-category="contact" data-track-section="faq">hallo@hasimuener.de</a>
			</p>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 12 — FIT-CHECK (3 Fragen → Termin)
	     Rein clientseitig: Quiz ist SSR-hidden, die Ergebnis-CTAs sind
	     SSR-sichtbar — ohne JS bleiben Buchung + Mail direkt nutzbar.
	     Keine <details> (FAQ-Accordion-Kollision), keine Speicherung.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-cta wl-fitcheck" data-nx-theme="dark" id="fit-check">
		<div class="wl-cta__bg" aria-hidden="true">
			<div class="wl-hero__bg-warmth"></div>
			<div class="wl-hero__bg-vignette"></div>
		</div>
		<div class="nx-container">
			<div class="wl-cta__shell nx-reveal">
				<div class="wl-cta__copy">
					<span class="wl-eyebrow">Nächster Schritt</span>
					<h2 class="wl-cta__title">Passt das zu eurem Setup?</h2>
					<p class="wl-cta__lede">Kurz prüfen, dann reden: 30&nbsp;Min, kein Pitch-Deck, keine Verkaufsshow. Danach wisst ihr, ob es passt — und wenn nicht, wisst ihr das auch.</p>
				</div>

				<div class="wl-fitcheck__box" data-fitcheck>
					<div class="wl-fitcheck__quiz" data-fitcheck-quiz hidden>
						<?php foreach ( $fitcheck_steps as $step_i => $step ) : ?>
							<div class="wl-fitcheck__step" data-fitcheck-step="<?php echo (int) ( $step_i + 1 ); ?>"<?php echo 0 !== $step_i ? ' hidden' : ''; ?>>
								<span class="wl-fitcheck__kicker">Schritt <?php echo (int) ( $step_i + 1 ); ?> von <?php echo count( $fitcheck_steps ); ?> · bleibt im Browser</span>
								<h3 class="wl-fitcheck__q"><?php echo esc_html( $step['question'] ); ?></h3>
								<div class="wl-fitcheck__opts">
									<?php foreach ( $step['options'] as $opt_value => $opt_label ) : ?>
										<button
											type="button"
											class="wl-fitcheck__opt"
											data-fitcheck-key="<?php echo esc_attr( $step['key'] ); ?>"
											data-fitcheck-value="<?php echo esc_attr( $opt_value ); ?>"
											data-fitcheck-label="<?php echo esc_attr( $opt_label ); ?>"
											data-track-action="fitcheck_<?php echo esc_attr( $step['key'] ); ?>_<?php echo esc_attr( $opt_value ); ?>"
											data-track-category="lead_gen"
											data-track-section="fitcheck_step_<?php echo (int) ( $step_i + 1 ); ?>"
										><?php echo esc_html( $opt_label ); ?></button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="wl-fitcheck__result" data-fitcheck-result tabindex="-1">
						<p class="wl-fitcheck__result-note" data-fitcheck-note hidden>Passt auf den ersten Blick. Nächster Schritt: 30 Minuten, direkt mit mir.</p>
						<div class="wl-cta__actions">
							<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn nx-btn--primary" data-fitcheck-book data-track-action="cta_whitelabel_fitcheck_book" data-track-category="lead_gen" data-track-section="fitcheck_result">
								Termin wählen (30 Min, direkt mit mir)
							</a>
							<a href="<?php echo esc_url( $mailto_url ); ?>" class="nx-btn nx-btn--ghost" data-fitcheck-mail data-track-action="mail_whitelabel_fitcheck" data-track-category="contact" data-track-section="fitcheck_result">
								Lieber schriftlich: hallo@hasimuener.de
							</a>
						</div>
					</div>

					<p class="wl-fitcheck__skip">
						<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" data-fitcheck-skip hidden data-track-action="cta_whitelabel_fitcheck_skip" data-track-category="lead_gen" data-track-section="fitcheck">
							Direkt buchen — ohne Fit-Check
						</a>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Sticky Mobile CTA -->
	<div class="wl-sticky-cta" id="wl-sticky-cta" aria-hidden="true">
		<div class="wl-sticky-cta__inner">
			<div class="wl-sticky-cta__label">
				<strong>White-Label-Partner</strong>
				<span>3 Fragen · dann 30&nbsp;Min direkt mit mir</span>
			</div>
			<a href="#fit-check" class="nx-btn nx-btn--primary" data-track-action="cta_sticky_whitelabel_to_fitcheck" data-track-category="navigation" data-track-section="sticky_mobile">
				Fit-Check starten
			</a>
		</div>
	</div>

</main>

<?php get_footer(); ?>
