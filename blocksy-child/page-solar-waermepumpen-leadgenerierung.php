<?php
/**
 * Template Name: Solar & Wärmepumpen Leadgenerierung (SOLARA)
 * Description: Premium · cinematic · minimalistisch. Hybrid-Theme (warm-cream + Copper).
 *              Primärer Lead-Pfad: 60-Sek-Marktcheck im Hero (REST → CRM).
 *              Alte System-Diagnose-Links führen auf diesen Marktcheck.
 *              Zielgruppe: Solar-/Wärmepumpen-Anbieter im DACH-Mittelstand (10–25 MA).
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url     = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_url       = home_url( '/e3-new-energy/' );
$privacy_url  = home_url( '/datenschutz/' );
$cal_url      = function_exists( 'hu_get_analysis_calcom_base_url' )
	? hu_get_analysis_calcom_base_url()
	: 'https://cal.com/hasim-uener/30min?overlayCalendar=true';

// ── E3-Proof-Metriken (Canon) ──────────────────────────────────
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'E3 New Energy';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? '> 85 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '9 Monate';
$e3_timeframe_dative = $e3_metrics['timeframe']['display_dative'] ?? '9 Monaten';

// ── Inhaltsmodelle ─────────────────────────────────────────────
$hero_metrics = [
	[ 'n' => $e3_cpl_after, 'l' => 'CPL nach 9 Monaten · ' . $e3_case_label ],
	[ 'n' => '< 24 h',      'l' => 'Persönliche Antwort · keine Pitch-Mail' ],
	[ 'n' => '100 %',       'l' => 'Asset-Eigentum · Code · Tracking · Daten' ],
];

$trust_items = [
	'B2B · DACH · Mittelstand',
	'Server in Frankfurt · DSGVO',
	'Server-Side-Tracking · CAPI',
	'Hardcoded WordPress · kein Page-Builder',
	'1:1 Senior · keine Junior-Kette',
	'Marktcheck kostenfrei',
];

$problem_cards = [
	[
		'n' => '01',
		'l' => 'Lead-Einkauf',
		't' => 'Portal-Leads kosten 80–150 €. Die Hälfte geht nicht ans Telefon.',
		's' => 'Aroundhome, Check24, DAA: Mieter ohne Dach, Preisvergleicher ohne Budget, Anfragen, die bei drei Wettbewerbern parallel landen. Ihr Vertrieb verbrennt Stunden mit Leuten, die nie kaufen werden.',
	],
	[
		'n' => '02',
		'l' => 'Blindflug',
		't' => 'Kein Überblick, welcher Kanal sich wirklich lohnt.',
		's' => 'Google Ads, Portal-Leads, Empfehlungen — niemand kann sauber sagen, woher die guten Abschlüsse kommen. Ohne diese Klarheit investieren Sie blind. Und skalieren falsch.',
	],
	[
		'n' => '03',
		'l' => 'Marktwende',
		't' => 'Seit 2024 kommen Anfragen nicht mehr von allein.',
		's' => 'Der PV-Boom ist vorbei. Der Markt normalisiert sich. Wer jetzt keine eigene Anfrage-Infrastruktur hat, ist abhängig von Portalen — und deren Preisen.',
	],
];

$method_cards = [
	[
		'n'  => 'I',
		'p'  => 'Phase 01',
		't'  => 'Diagnose & Fundament',
		's'  => 'Anfrage-Quellen, Tracking, Funnel, Vertriebsanschluss — vier Module, schriftlicher Befund, drei priorisierte Hebel. Verrechenbar auf Umsetzung.',
		'b'  => [ 'Module: Quellen · Daten · Funnel · Sales', 'Schriftlicher Befund · keine Folien', 'Auf Umsetzung 1:1 verrechenbar' ],
	],
	[
		'n'  => 'II',
		'p'  => 'Phase 02',
		't'  => 'Eigenes Anfrage-System',
		's'  => 'WordPress hardcoded — kein Page-Builder, kein Plugin-Stack. Server-Side-Tracking auf eigenem Server. Smarte Vorqualifizierung. Conversion-Pfad ohne Mietsysteme.',
		'b'  => [ 'Money-Page · Proof- & Angebotsseiten', 'Frankfurt-Server · CAPI · DSGVO', '60-Sek-Funnel mit Lead-Scoring' ],
	],
	[
		'n'  => 'III',
		'p'  => 'Phase 03',
		't'  => 'Skalieren & Übergeben',
		's'  => 'Mit sauberem Fundament rechnen sich Google Ads, Meta Ads und SEO endlich. Wöchentliches Reporting. Bei Vertragsende: dokumentierte Übergabe — Code, Tracking, Daten bleiben bei Ihnen.',
		'b'  => [ 'Google Ads · Meta Ads · SEO-Anteil', 'Wöchentliches Reporting', 'Monatlich kündbar · 100 % Asset-Übergabe' ],
	],
];

$results_qualifiers = [
	[ 'k' => 'Anfrage-Quellen',    'v' => 'Beziffert' ],
	[ 'k' => 'Tracking & CAPI',    'v' => 'Auditiert' ],
	[ 'k' => 'Funnel-Hebel',       'v' => 'Drei priorisiert' ],
	[ 'k' => 'Wirtschaftlichkeit', 'v' => 'Einordnung' ],
	[ 'k' => 'Nächster Schritt',   'v' => 'Konkret' ],
];

$guarantee_points = [
	[
		't' => 'Marktcheck ist kostenfrei',
		's' => 'Fünf Fragen, 60 Sekunden. Sie bekommen eine persönliche Ersteinschätzung — ohne Newsletter, ohne Pitch-Deck, ohne Folgekosten.',
	],
	[
		't' => 'Drei Hebel — auch bei Abrat',
		's' => 'Wenn die anschließende Diagnose zum Ergebnis kommt, dass Sie das volle System nicht brauchen, bekommen Sie trotzdem drei priorisierte Hebel mit konkretem nächstem Schritt.',
	],
	[
		't' => 'Diagnose wird verrechnet',
		's' => 'Bei Umsetzung wird die Diagnose 1:1 angerechnet. Sie zahlen sie nur dann, wenn Sie sich gegen die Umsetzung entscheiden. Keine Mindestlaufzeit, volle Asset-Übergabe.',
	],
];

// ── Sticky In-Page Section Nav (CRO: orientation + jump to relevant block) ─────
$section_nav = [
	[ 'h' => '#problem',     'l' => 'Status quo' ],
	[ 'h' => '#vergleich',   'l' => 'Vergleich' ],
	[ 'h' => '#system-bild', 'l' => 'System' ],
	[ 'h' => '#capex',       'l' => 'CAPEX vs OPEX' ],
	[ 'h' => '#ergebnisse',  'l' => 'Referenz' ],
	[ 'h' => '#fit',         'l' => 'Passt es?' ],
	[ 'h' => '#faq',         'l' => 'FAQ' ],
];

// ── Markt-Standard vs Eigener Weg (Compare-Section) ───────────────────────────
$compare_bad = [
	[ 't' => 'Portal-Leads',      's' => 'Drei Wettbewerber bekommen dieselbe Anfrage. Sie bieten gegen den Preis.' ],
	[ 't' => 'Klickberichte',     's' => 'Reportings über Impressionen — nicht über Projektwert.' ],
	[ 't' => '5-Felder-Formular', 's' => 'Verliert Interessenten, bevor sie qualifiziert sind.' ],
	[ 't' => 'Black-Box-Agentur', 's' => 'Niemand weiß, woher die nächste Anfrage kommt — oder warum sie ausbleibt.' ],
];
$compare_good = [
	[ 't' => 'Eigene Anfragestrecke',    's' => 'Anfragen, die Ihrem Betrieb gehören — nicht dem Portal.' ],
	[ 't' => 'Anfragequalität messbar',  's' => 'Region, Heizart, Dach, Projektwert — vor dem Anruf.' ],
	[ 't' => '60-Sek-Vorqualifizierung', 's' => 'Daten erst, wenn der Fit klar ist. Kein 5-Felder-Hürdenlauf.' ],
	[ 't' => 'Dokumentiertes System',    's' => 'Sie verstehen, warum es funktioniert. Code, Tracking, Daten bleiben bei Ihnen.' ],
];

// ── System-Diagramm: 4 Layer · was Kunden konkret bekommen ─────────────────────
$system_layers = [
	[
		'n'      => '01',
		'name'   => 'Fundament',
		'status' => 'DSGVO · Frankfurt',
		'cols'   => 2,
		'items'  => [
			[ 'i' => 'M3 12l9-9 9 9M5 10v10h14V10', 't' => 'WordPress hardcoded',  's' => 'Kein Page-Builder, kein Plugin-Stack.' ],
			[ 'i' => 'M3 5h18v12H3zM3 21h18',       't' => 'Frankfurt-Server',     's' => 'DSGVO-konform · eigenes Hosting.' ],
		],
	],
	[
		'n'      => '02',
		'name'   => 'Daten & Tracking',
		'status' => 'Server-Side · CAPI',
		'cols'   => 2,
		'items'  => [
			[ 'i' => 'M4 19V5m0 14h16m-12-7v7m4-12v12m4-9v9', 't' => 'Server-Side Tracking', 's' => 'GA4 · CAPI · eigener Container.' ],
			[ 'i' => 'M10 14l4-4m-7-3a5 5 0 017-7l3 3m1 11a5 5 0 01-7 7l-3-3', 't' => 'Saubere Attribution', 's' => 'Welcher Kanal bringt zahlende Aufträge?' ],
		],
	],
	[
		'n'      => '03',
		'name'   => 'Conversion-Pfad',
		'status' => 'Vorqualifizierung · Score',
		'cols'   => 3,
		'items'  => [
			[ 'i' => 'M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z', 't' => 'Money Page',           's' => 'Solar/WP-spezifisch · Proof.' ],
			[ 'i' => 'M13 2L4 14h7v8l9-12h-7z',                              't' => '60-Sek-Qualifizierung', 's' => 'Region · Heizart · Projektwert.' ],
			[ 'i' => 'M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01z', 't' => 'Lead-Scoring', 's' => 'Grün, gelb, rot — vor dem Anruf.' ],
		],
	],
	[
		'n'      => '04',
		'name'   => 'Skalierung',
		'status' => 'Multi-Channel · messbar',
		'cols'   => 3,
		'items'  => [
			[ 'i' => 'M11 4a7 7 0 100 14 7 7 0 000-14zM21 21l-5.2-5.2', 't' => 'Google Ads', 's' => 'Lokal · hoher Intent.' ],
			[ 'i' => 'M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm5 17v.01', 't' => 'Meta Ads', 's' => 'Facebook · Instagram · Retargeting.' ],
			[ 'i' => 'M12 2a10 10 0 100 20 10 10 0 000-20zM2 12h20M12 2a15.3 15.3 0 010 20m0-20a15.3 15.3 0 000 20', 't' => 'SEO-Anteil', 's' => 'Basis-Optimierung · Indexierung.' ],
		],
	],
];

// ── CAPEX vs OPEX · Zahlen aus dem messaging-canon ─────────────────────────────
$capex_timeframes = [
	12 => [
		'portal_monthly' => '~ 1.080 €',
		'portal_leads'   => '~ 160',
		'portal_total'   => '13.000 €',
		'own_setup'      => '12.000 – 18.000 €',
		'own_monthly'    => '~ 50 €',
		'own_total'      => '12.600 – 18.600 €',
	],
	24 => [
		'portal_monthly' => '~ 1.080 €',
		'portal_leads'   => '~ 320',
		'portal_total'   => '26.000 €',
		'own_setup'      => '12.000 – 18.000 €',
		'own_monthly'    => '~ 50 €',
		'own_total'      => '13.200 – 19.200 €',
	],
	36 => [
		'portal_monthly' => '~ 1.080 €',
		'portal_leads'   => '~ 480',
		'portal_total'   => '39.000 €',
		'own_setup'      => '12.000 – 18.000 €',
		'own_monthly'    => '~ 50 €',
		'own_total'      => '14.160 – 20.160 €',
	],
];
$capex_default = 24;

// ── Fit-Check (passt / passt nicht) ────────────────────────────────────────────
$fit_yes = [
	[ 't' => 'Solar oder Wärmepumpe im DACH-Mittelstand', 's' => 'Mit eigenem Vertrieb, nicht reine Vermittlung.' ],
	[ 't' => 'Klares Zielgebiet',                          's' => 'Region oder Bundesland definiert — kein „bundesweit, alles".' ],
	[ 't' => 'Hohe Projektwerte',                          's' => 'B2C ab ~ 15 k €, B2B ab ~ 50 k € pro Projekt.' ],
	[ 't' => 'Geschäftsführung entscheidet',               's' => 'Über Aufbau, Marke und Positionierung — kurze Wege.' ],
	[ 't' => '24-Monate-Horizont',                         's' => 'Bereit, ein Asset aufzubauen statt nur Anfragen einzukaufen.' ],
];
$fit_no = [
	[ 't' => 'Reines Vermittlungsgeschäft',                's' => 'Wer Leads weiterverkauft, braucht kein eigenes System.' ],
	[ 't' => '„Nächste Woche brauchen wir Leads."',        's' => 'Tragfähige Pipelines wachsen über Monate, nicht Tage.' ],
	[ 't' => 'Kein Vertrieb am Telefon',                   's' => 'Anfragen sterben, wenn niemand zurückruft.' ],
	[ 't' => 'Keine Marke gewollt',                        's' => 'Eigener Anfrageweg lebt davon, dass Sie sichtbar werden.' ],
];

$faq_items = [
	[
		'question' => 'Was passiert nach dem Marktcheck?',
		'answer'   => 'Ich lese Ihre Antworten persönlich und melde mich innerhalb von 24 Stunden per E-Mail. Wenn der Fit passt, schlage ich ein 30-minütiges Erstgespräch vor oder lade Sie in die kostenpflichtige Tiefendiagnose ein. Wenn der Fit nicht passt, sage ich das ehrlich und nenne Ihnen die realistischere Alternative.',
	],
	[
		'question' => 'Was kostet das im Vergleich zur Performance-Agentur?',
		'answer'   => 'Initiales Setup: 12.000–18.000 € einmalig. Laufend ca. 50 €/Monat für Hochleistungs-Hosting. TCO über 24 Monate: 13.200–19.200 € — und Sie besitzen Code, Tracking und Daten. Eine Performance-Agentur mit Paket „Regio+" kostet im gleichen Zeitraum rund 26.000 € und Sie besitzen am Ende nichts. Bilanziell: CAPEX statt OPEX.',
	],
	[
		'question' => 'Welche Daten brauchen Sie für die Diagnose?',
		'answer'   => 'Für den Marktcheck reichen 5 Antworten. Für die Tiefendiagnose: Lesezugriff auf Google Analytics, Google Ads und Meta Ads Manager, Einblick in den CRM-Datenbestand der letzten 90 Tage und eine 15-Minuten-Bestandsaufnahme zu Vertriebsprozess und Lead-Quellen. Wenn Tracking-Daten fehlen, ist das oft schon das erste Diagnose-Ergebnis.',
	],
	[
		'question' => 'Warum nicht einfach mehr Google Ads schalten?',
		'answer'   => 'Mehr Budget auf schlechte Seiten heißt mehr Geld für dieselben unqualifizierten Anfragen. Erst wenn Seite, Formular und serverseitiges Tracking sauber arbeiten, lohnt sich mehr Reichweite. Ohne eigene Datenebene bleiben Sie zudem in der Logik der Plattform.',
	],
	[
		'question' => 'Brauchen wir eine neue Website?',
		'answer'   => 'Meistens nicht im Komplettumfang. Was Sie brauchen: hardcoded WordPress (kein Page-Builder, kein Plugin-Stack), serverseitiges Tracking auf eigenem Server, Conversion-Pfad ohne Mietsysteme. Ob das ein Teil-Umbau oder ein sauberer Erstaufbau wird, zeigt sich im ersten Schritt.',
	],
	[
		'question' => 'Wir nutzen schon eine Performance-Agentur — warum sollten wir wechseln?',
		'answer'   => 'Müssen Sie nicht. Drei Prüffragen: 1) Wem gehört der Code Ihrer Landingpage? 2) Wem gehört das CRM, in dem Ihre Leads liegen? 3) Wem gehört der Tracking-Account? Wenn die Antwort dreimal „uns" ist, brauchen Sie mich nicht. Wenn die Antwort dreimal „der Agentur" ist, mieten Sie ein System.',
	],
	[
		'question' => 'Wie schnell sieht man Ergebnisse?',
		'answer'   => 'Erste Verbesserungen entstehen oft nach den ersten Optimierungen an Tracking, Formularen und Conversion-Pfaden. Belastbare Skalierung braucht in der Regel mehrere Wochen bis Monate — weil Daten, Tests und Kanäle zusammenspielen müssen.',
	],
	[
		'question' => 'Was unterscheidet Sie von Lead-Portalen?',
		'answer'   => 'Portale vermieten Nachfrage. Sie zahlen für jeden Kontakt, den auch 3–4 Mitbewerber erhalten. Das System hier baut eigene Nachfrage-Infrastruktur auf, die Ihrem Betrieb gehört und langfristig für exklusive Anfragen sorgt.',
	],
];

// ── Schema.org ─────────────────────────────────────────────────
$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Aufbau eigener Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter',
	'serviceType' => 'System-Architektur für eigene Lead-Infrastruktur: WordPress, Tracking, Qualifizierung, CRM-Übergabe',
	'url'         => $page_url,
	'description' => sprintf( 'Eigenes Anfrage-System für Solar- und Wärmepumpen-Betriebe. Referenz %1$s: %2$s weniger Kosten pro Anfrage in %3$s.', $e3_case_label, $e3_cpl_reduction, $e3_timeframe_dative ),
	'provider'    => [
		'@type' => 'Person',
		'name'  => 'Haşim Üner',
		'url'   => home_url( '/' ),
	],
	'audience'    => [
		'@type'        => 'Audience',
		'audienceType' => 'Solar-, Wärmepumpen-, Speicher- und Energie-Anbieter im DACH-Raum',
	],
	'areaServed'  => [
		[ '@type' => 'Country', 'name' => 'Deutschland' ],
	],
	'offers'      => [
		'@type'         => 'Offer',
		'price'         => '0',
		'priceCurrency' => 'EUR',
		'description'   => 'Marktcheck in 60 Sekunden, persönliche Ersteinschätzung innerhalb von 24 Stunden.',
	],
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => trailingslashit( $page_url ) . '#faq',
	'mainEntity' => [],
];

foreach ( $faq_items as $faq_item ) {
	$faq_schema['mainEntity'][] = [
		'@type'          => 'Question',
		'name'           => $faq_item['question'],
		'acceptedAnswer' => [
			'@type' => 'Answer',
			'text'  => $faq_item['answer'],
		],
	];
}

// ── inline SVG-Pfeil ───────────────────────────────────────────
$arrow_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" focusable="false" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';

get_header();
?>

<main id="main" class="site-main">
	<div class="solara-landing" data-track-section="energy_service_landing">

		<!-- ════════════════════════════════════════════════════════════
		     HERO mit Marktcheck-Quiz
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-hero" id="hero" data-track-section="hero">
			<div class="sol-hero-sky" aria-hidden="true"></div>
			<div class="sol-hero-sun" aria-hidden="true">
				<div class="sol-hero-sun-disc"></div>
				<div class="sol-hero-sun-halo"></div>
				<div class="sol-hero-sun-rays" data-sol-rays></div>
			</div>
			<div class="sol-hero-horizon" aria-hidden="true"></div>

			<div class="sol-wrap sol-hero-inner">
				<div class="sol-hero-left">
					<div class="sol-eyebrow">Für Solar- &amp; Wärmepumpen-Anbieter · 10–25 MA · DACH</div>
					<h1 class="sol-display sol-hero-h1">
						Hören Sie auf,<br /><em>Anfragen zu mieten.</em><br />Bauen Sie eine,<br />die <em>Ihnen gehört.</em>
					</h1>
					<p class="sol-hero-sub">
						Portal-Leads kosten 80–150 €. Die Hälfte geht nicht ans Telefon. Wir bauen Ihrem Betrieb ein eigenes Anfrage-System — WordPress hardcoded, Tracking auf eigenem Server, Vorqualifizierung mit Lead-Score. Das System gehört Ihnen. Die Anfragen sind exklusiv.
					</p>

					<div class="sol-hero-metrics">
						<?php foreach ( $hero_metrics as $m ) : ?>
							<div class="sol-metric">
								<div class="sol-metric-n sol-display"><?php echo esc_html( $m['n'] ); ?></div>
								<div class="sol-metric-l sol-mono"><?php echo esc_html( $m['l'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>

					<p class="sol-mono" style="margin-top:14px;color:var(--sol-fg-dim);font-size:11px;letter-spacing:.06em;">
						Bereits qualifizierter Fall?
						<a
							href="<?php echo esc_url( $cal_url ); ?>"
							style="color:var(--sol-accent);text-decoration:underline;text-underline-offset:3px;margin-left:6px;"
							data-track-action="cta_solar_to_calcom"
							data-track-category="lead_funnel"
							data-track-section="hero_secondary"
						>Direkt 30-Min-Gespräch buchen →</a>
					</p>
				</div>

				<aside class="sol-hero-right" aria-labelledby="sol-quiz-title">
					<div class="sol-cta-card" id="marktcheck">
						<div class="sol-cta-particles" aria-hidden="true">
							<span class="sol-cta-particle"></span>
							<span class="sol-cta-particle"></span>
							<span class="sol-cta-particle"></span>
						</div>
						<!--
						  Quiz mount point. JS rendert hier das 5-Step-Quiz.
						  Wenn JS fehlt, bleibt der SSR-Fallback unten als
						  funktionierende Alternative.
						-->
						<div data-sol-quiz id="sol-quiz-mount">
							<noscript>
								<div class="sol-cta-head">
									<span class="sol-cta-tag sol-mono">
										<span class="sol-cta-tag-dot" aria-hidden="true"></span>
										Marktcheck · 60 Sek
									</span>
									<span class="sol-cta-head-right sol-mono">Kostenfrei</span>
								</div>
								<h2 id="sol-quiz-title" class="sol-cta-title">
									Wo verlieren Sie heute Anfragen — und wie viel kostet Sie das?
								</h2>
								<p class="sol-cta-hint">
									Aktivieren Sie JavaScript für den 60-Sek-Marktcheck oder schreiben Sie direkt an
									<a href="mailto:hasim@hasimuener.de" style="color:var(--sol-accent);">hasim@hasimuener.de</a>.
								</p>
								<a
									class="sol-cta-submit"
									href="mailto:hasim@hasimuener.de"
									data-track-action="cta_solar_noscript_mail"
									data-track-category="lead_funnel"
									data-track-section="hero_noscript"
								>
									<span>Per E-Mail anfragen</span>
									<span class="sol-cta-submit-arrow" aria-hidden="true"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</a>
							</noscript>

							<!-- SSR-Fallback (sichtbar bis JS gemounted hat) -->
							<div class="sol-cta-head">
								<span class="sol-cta-tag sol-mono">
									<span class="sol-cta-tag-dot" aria-hidden="true"></span>
									Marktcheck · 60 Sek · 5 Fragen
								</span>
								<span class="sol-cta-head-right sol-mono">Kostenfrei</span>
							</div>
							<h2 id="sol-quiz-title" class="sol-cta-title">
								Wo verlieren Sie heute Anfragen — und wie viel kostet Sie das?
							</h2>
							<p class="sol-cta-hint">
								Fünf Fragen, 60 Sekunden. Persönliche Ersteinschätzung innerhalb von 24 h — keine Newsletter, keine Pitch-Mail.
							</p>
							<p class="sol-cta-fineprint" style="text-align:left;margin:0 0 14px;">Wird geladen …</p>
						</div>
					</div>
				</aside>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     TRUST STRIP
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-trust" aria-label="Vertrauenssignale" data-track-section="trust_strip">
			<div class="sol-wrap">
				<div class="sol-trust-row">
					<?php foreach ( $trust_items as $t ) : ?>
						<span class="sol-trust-item sol-mono">
							<span class="sol-trust-dot" aria-hidden="true"></span>
							<?php echo esc_html( $t ); ?>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     STICKY IN-PAGE SECTION NAV
		     Wird nach dem Hero sticky · CRO: schnelle Orientierung,
		     ohne den globalen Header zu ersetzen (SEO/Brand bleibt).
		     ════════════════════════════════════════════════════════════ -->
		<nav class="sol-section-nav" aria-label="Sektionen dieser Seite" data-track-section="section_nav">
			<div class="sol-wrap">
				<div class="sol-section-nav-inner">
					<span class="sol-section-nav-label sol-mono" aria-hidden="true">Auf dieser Seite</span>
					<ul class="sol-section-nav-list">
						<?php foreach ( $section_nav as $n ) : ?>
							<li><a href="<?php echo esc_attr( $n['h'] ); ?>" class="sol-mono"
								data-track-action="section_nav_jump"
								data-track-category="navigation"
								data-track-section="section_nav"
							><?php echo esc_html( $n['l'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
					<a class="sol-section-nav-cta sol-mono" href="#marktcheck"
						data-track-action="cta_solar_section_nav_to_marktcheck"
						data-track-category="lead_funnel"
						data-track-section="section_nav"
					>Marktcheck →</a>
				</div>
			</div>
		</nav>

		<!-- ════════════════════════════════════════════════════════════
		     PROBLEM
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section" id="problem" data-track-section="problem">
			<div class="sol-wrap">
				<div class="sol-eyebrow">Der Status Quo</div>
				<h2 class="sol-display sol-problem-h">
					Sie zahlen für <em>Anfragen, die nicht Ihnen gehören</em> — und Vertriebsstunden, die niemand kauft.
				</h2>
				<div class="sol-problem-grid">
					<?php foreach ( $problem_cards as $c ) : ?>
						<article class="sol-problem-card">
							<div class="sol-problem-card-n"><?php echo esc_html( $c['n'] ); ?> · <?php echo esc_html( $c['l'] ); ?></div>
							<div class="sol-problem-card-t"><?php echo esc_html( $c['t'] ); ?></div>
							<p class="sol-problem-card-s"><?php echo esc_html( $c['s'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     COMPARE — Markt-Standard vs Eigener Weg
		     Konturiert die strategische Alternative zum Status quo.
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-compare-section" id="vergleich" data-track-section="compare">
			<div class="sol-wrap">
				<div class="sol-eyebrow">Markt vs Eigener Weg</div>
				<h2 class="sol-display sol-compare-h">
					Sie kaufen keine Leads mehr. Sie bauen <em>den Weg</em>, auf dem sie entstehen.
				</h2>
				<p class="sol-compare-sub">
					Der Ausweg ist nicht ein besseres Portal. Es ist ein Anfrageweg, der Ihnen gehört — und sich messen lässt.
				</p>

				<div class="sol-compare">
					<div class="sol-compare-col sol-compare-col--bad">
						<div class="sol-compare-tag sol-mono" aria-hidden="true">Aktuell · kostet</div>
						<div class="sol-compare-head">
							<span class="sol-compare-icon sol-compare-icon--bad" aria-hidden="true">✕</span>
							<span>Markt-Standard</span>
						</div>
						<?php foreach ( $compare_bad as $row ) : ?>
							<div class="sol-compare-row">
								<div class="sol-compare-row-t"><?php echo esc_html( $row['t'] ); ?></div>
								<div class="sol-compare-row-d"><?php echo esc_html( $row['s'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="sol-compare-divider" aria-hidden="true">
						<span class="sol-compare-divider-icon"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</div>

					<div class="sol-compare-col sol-compare-col--good">
						<div class="sol-compare-tag sol-mono sol-compare-tag--good" aria-hidden="true">Empfohlen · bleibt</div>
						<div class="sol-compare-head">
							<span class="sol-compare-icon sol-compare-icon--good" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg>
							</span>
							<span>Eigener Weg</span>
						</div>
						<?php foreach ( $compare_good as $row ) : ?>
							<div class="sol-compare-row">
								<div class="sol-compare-row-t"><?php echo esc_html( $row['t'] ); ?></div>
								<div class="sol-compare-row-d"><?php echo esc_html( $row['s'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     SYSTEM-DIAGRAMM — 4 Layer · was Kunden konkret bekommen
		     "Ihr eigenes System — was Sie genau bekommen"
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-section--tinted sol-system-section" id="system-bild" data-track-section="system_diagram">
			<div class="sol-wrap">
				<div class="sol-system-head">
					<span class="sol-system-badge sol-mono">
						<span class="sol-system-badge-pulse" aria-hidden="true"></span>
						Ihr eigenes System
					</span>
					<h2 class="sol-display sol-system-h">
						Was Sie <em>genau</em> bekommen.
					</h2>
					<p class="sol-system-sub">
						Kein Blackbox-Setup. Jede Komponente ist dokumentiert, gehört Ihnen und wird 1:1 übergeben — inklusive Zugang zu allen Accounts.
					</p>
				</div>

				<div class="sol-system-canvas">
					<?php foreach ( $system_layers as $layer_index => $layer ) : ?>
						<article class="sol-system-layer<?php echo 3 === $layer['cols'] ? ' is-three' : ''; ?>" style="--sol-delay:<?php echo (float) ( $layer_index * 0.12 ); ?>s;">
							<header class="sol-system-layer-head">
								<div class="sol-system-layer-n sol-display"><?php echo esc_html( $layer['n'] ); ?></div>
								<div class="sol-system-layer-name"><?php echo esc_html( $layer['name'] ); ?></div>
								<div class="sol-system-layer-status sol-mono"><?php echo esc_html( $layer['status'] ); ?></div>
							</header>
							<div class="sol-system-grid">
								<?php foreach ( $layer['items'] as $item ) : ?>
									<div class="sol-system-comp">
										<span class="sol-system-comp-icon" aria-hidden="true">
											<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="<?php echo esc_attr( $item['i'] ); ?>"/></svg>
										</span>
										<div class="sol-system-comp-body">
											<div class="sol-system-comp-t"><?php echo esc_html( $item['t'] ); ?></div>
											<div class="sol-system-comp-s"><?php echo esc_html( $item['s'] ); ?></div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
							<?php if ( $layer_index < count( $system_layers ) - 1 ) : ?>
								<div class="sol-system-layer-connector" aria-hidden="true"></div>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="sol-system-ownership" role="note">
					<div class="sol-system-ownership-mark" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
					</div>
					<div class="sol-system-ownership-body">
						<div class="sol-system-ownership-t">100 % Asset-Eigentum</div>
						<div class="sol-system-ownership-s">Code, Tracking-Accounts, Server-Zugang, Datenhoheit — alles dokumentiert übergeben. Das System bleibt bei Ihnen, auch wenn die Zusammenarbeit endet.</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     METHOD / SYSTEM
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-method" id="system" data-track-section="method">
			<div class="sol-wrap">
				<div class="sol-method-head">
					<div class="sol-eyebrow">Das System</div>
					<h2 class="sol-display sol-method-h">
						Diagnose. Aufbau. Eigentum.<br />Drei Phasen — <em>ein Ergebnis</em>: exklusive Anfragen.
					</h2>
				</div>
				<div class="sol-method-grid">
					<?php foreach ( $method_cards as $card ) : ?>
						<article class="sol-method-card">
							<div class="sol-method-card-top">
								<div class="sol-method-card-n sol-display"><?php echo esc_html( $card['n'] ); ?></div>
								<div class="sol-method-card-pill"><?php echo esc_html( $card['p'] ); ?></div>
							</div>
							<h3 class="sol-method-card-t"><?php echo esc_html( $card['t'] ); ?></h3>
							<p class="sol-method-card-s"><?php echo esc_html( $card['s'] ); ?></p>
							<ul class="sol-method-card-list">
								<?php foreach ( $card['b'] as $b ) : ?>
									<li><span class="sol-method-tick">+</span><?php echo esc_html( $b ); ?></li>
								<?php endforeach; ?>
							</ul>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     CAPEX vs OPEX — Bilanzielle Entscheidung
		     Interaktiver Zeitraum-Picker (12 · 24 · 36 Monate).
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-capex-section" id="capex" data-track-section="capex_opex">
			<div class="sol-wrap">
				<div class="sol-capex-head">
					<div class="sol-eyebrow">CAPEX statt OPEX</div>
					<h2 class="sol-display sol-capex-h">
						Mieten oder besitzen. Eine <em>Bilanz-Entscheidung</em>.
					</h2>
					<p class="sol-capex-sub">
						Beide Wege kosten Geld. Nur einer hinterlässt am Ende ein Asset, das Ihnen gehört.
					</p>
				</div>

				<div class="sol-capex-picker" role="tablist" aria-label="Zeitraum auswählen">
					<span class="sol-capex-picker-label sol-mono">Zeitraum</span>
					<div class="sol-capex-picker-buttons" data-sol-capex-buttons>
						<?php foreach ( [ 12, 24, 36 ] as $tf ) : ?>
							<button type="button"
								class="sol-capex-picker-btn<?php echo $tf === $capex_default ? ' is-active' : ''; ?>"
								data-sol-capex-tf="<?php echo esc_attr( (string) $tf ); ?>"
								role="tab"
								aria-selected="<?php echo $tf === $capex_default ? 'true' : 'false'; ?>"
								data-track-action="capex_timeframe"
								data-track-category="engagement"
								data-track-section="capex_opex"
							><?php echo esc_html( (string) $tf ); ?> Monate</button>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="sol-capex-compare" data-sol-capex>
					<!-- OPEX · Portal-Leads -->
					<article class="sol-capex-col sol-capex-col--opex">
						<header class="sol-capex-col-head">
							<span class="sol-capex-col-badge sol-capex-col-badge--opex sol-mono">OPEX</span>
							<h3 class="sol-capex-col-title">Portal-Leads mieten</h3>
							<p class="sol-capex-col-sub">Monatlich zahlen, nichts besitzen.</p>
						</header>
						<div class="sol-capex-breakdown">
							<div class="sol-capex-row">
								<span class="sol-capex-row-l">Lead-Kosten (Ø 80 €/Stk)</span>
								<span class="sol-capex-row-v" data-sol-capex-out="portal_monthly"><?php echo esc_html( $capex_timeframes[ $capex_default ]['portal_monthly'] ); ?> / Monat</span>
							</div>
							<div class="sol-capex-row">
								<span class="sol-capex-row-l">Erwartete Leads</span>
								<span class="sol-capex-row-v" data-sol-capex-out="portal_leads"><?php echo esc_html( $capex_timeframes[ $capex_default ]['portal_leads'] ); ?> Stück</span>
							</div>
							<div class="sol-capex-row sol-capex-row--issue">
								<span class="sol-capex-row-l">Qualität</span>
								<span class="sol-capex-row-v">~ 50 % gehen nicht ans Telefon</span>
							</div>
							<div class="sol-capex-row sol-capex-row--issue">
								<span class="sol-capex-row-l">Exklusivität</span>
								<span class="sol-capex-row-v">3–4 Wettbewerber parallel</span>
							</div>
						</div>
						<div class="sol-capex-total">
							<div class="sol-capex-total-l sol-mono">Gesamt (<span data-sol-capex-out="tf"><?php echo esc_html( (string) $capex_default ); ?></span> Monate)</div>
							<div class="sol-capex-total-v sol-capex-total-v--opex sol-display" data-sol-capex-out="portal_total"><?php echo esc_html( $capex_timeframes[ $capex_default ]['portal_total'] ); ?></div>
						</div>
						<div class="sol-capex-result">
							<div class="sol-capex-result-icon sol-capex-result-icon--bad" aria-hidden="true">✕</div>
							<div class="sol-capex-result-body">
								<strong>0 € Asset am Ende</strong>
								<span>Budget aus = Anfragen aus.</span>
							</div>
						</div>
						<ul class="sol-capex-list">
							<li><span class="sol-capex-list-x" aria-hidden="true">✕</span>Portal behält die Daten</li>
							<li><span class="sol-capex-list-x" aria-hidden="true">✕</span>Keine Kontrolle über Qualität</li>
							<li><span class="sol-capex-list-x" aria-hidden="true">✕</span>Preiserhöhungen jederzeit möglich</li>
							<li><span class="sol-capex-list-x" aria-hidden="true">✕</span>Vertrieb verbrennt Zeit mit Nicht-Käufern</li>
						</ul>
					</article>

					<div class="sol-capex-divider" aria-hidden="true">
						<span class="sol-capex-divider-line"></span>
						<span class="sol-capex-divider-tag sol-mono">VS</span>
						<span class="sol-capex-divider-line"></span>
					</div>

					<!-- CAPEX · Eigenes System -->
					<article class="sol-capex-col sol-capex-col--capex">
						<header class="sol-capex-col-head">
							<span class="sol-capex-col-badge sol-capex-col-badge--capex sol-mono">CAPEX</span>
							<h3 class="sol-capex-col-title">Eigenes System besitzen</h3>
							<p class="sol-capex-col-sub">Einmalig investieren, langfristig skalieren.</p>
						</header>
						<div class="sol-capex-breakdown">
							<div class="sol-capex-row">
								<span class="sol-capex-row-l">Setup (einmalig)</span>
								<span class="sol-capex-row-v" data-sol-capex-out="own_setup"><?php echo esc_html( $capex_timeframes[ $capex_default ]['own_setup'] ); ?></span>
							</div>
							<div class="sol-capex-row">
								<span class="sol-capex-row-l">Hosting + Wartung</span>
								<span class="sol-capex-row-v" data-sol-capex-out="own_monthly"><?php echo esc_html( $capex_timeframes[ $capex_default ]['own_monthly'] ); ?> / Monat</span>
							</div>
							<div class="sol-capex-row sol-capex-row--benefit">
								<span class="sol-capex-row-l">Datenhoheit</span>
								<span class="sol-capex-row-v">100 % bei Ihnen</span>
							</div>
							<div class="sol-capex-row sol-capex-row--benefit">
								<span class="sol-capex-row-l">Vorqualifizierung</span>
								<span class="sol-capex-row-v">Region · Projektwert · Fit-Score</span>
							</div>
						</div>
						<div class="sol-capex-total">
							<div class="sol-capex-total-l sol-mono">Gesamt (<span data-sol-capex-out="tf2"><?php echo esc_html( (string) $capex_default ); ?></span> Monate)</div>
							<div class="sol-capex-total-v sol-capex-total-v--capex sol-display" data-sol-capex-out="own_total"><?php echo esc_html( $capex_timeframes[ $capex_default ]['own_total'] ); ?></div>
						</div>
						<div class="sol-capex-result sol-capex-result--good">
							<div class="sol-capex-result-icon sol-capex-result-icon--good" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="M5 12l5 5L20 7"/></svg>
							</div>
							<div class="sol-capex-result-body">
								<strong>Aktiviertes Asset</strong>
								<span>Code, Tracking, Daten bleiben bei Ihnen.</span>
							</div>
						</div>
						<ul class="sol-capex-list">
							<li><span class="sol-capex-list-v" aria-hidden="true">✓</span>Exklusive Anfragen — nur für Sie</li>
							<li><span class="sol-capex-list-v" aria-hidden="true">✓</span>Volle Transparenz über Herkunft</li>
							<li><span class="sol-capex-list-v" aria-hidden="true">✓</span>Skaliert ohne Kostenexplosion</li>
							<li><span class="sol-capex-list-v" aria-hidden="true">✓</span>System bleibt — auch ohne laufende Kosten</li>
						</ul>
					</article>
				</div>

				<aside class="sol-capex-summary">
					<h3 class="sol-capex-summary-h">Bilanziell: CAPEX statt OPEX</h3>
					<p>
						Ein eigenes Anfrage-System ist eine <strong>aktivierbare Investition</strong> (CAPEX), kein wiederkehrender Kostenfaktor (OPEX). Portal-Leads sind Betriebsausgaben ohne Restwert.
						Nach <span data-sol-capex-out="tf3"><?php echo esc_html( (string) $capex_default ); ?></span> Monaten haben Sie entweder
						<strong><span data-sol-capex-out="portal_total2"><?php echo esc_html( $capex_timeframes[ $capex_default ]['portal_total'] ); ?></span> ausgegeben und besitzen nichts</strong> —
						oder Sie haben <strong><span data-sol-capex-out="own_total2"><?php echo esc_html( $capex_timeframes[ $capex_default ]['own_total'] ); ?></span> investiert und ein skalierbares Asset</strong>
						(Setup einmalig + <span data-sol-capex-out="tf4"><?php echo esc_html( (string) $capex_default ); ?></span> × Hosting/Wartung).
					</p>
				</aside>

				<div class="sol-capex-cta">
					<a class="sol-btn sol-btn-primary" href="#marktcheck"
						data-track-action="cta_solar_capex_to_marktcheck"
						data-track-category="lead_funnel"
						data-track-section="capex_opex"
						data-track-funnel-stage="quiz_open"
					>
						<span>Marktcheck starten · 60 Sek</span>
						<span class="sol-btn-arrow"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
					<div class="sol-capex-cta-micro sol-mono">TCO-Rechnung · 1:1 verrechenbar bei Umsetzung</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     RESULTS — E3-Proof + Methodik-Snapshot
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section" id="ergebnisse" data-track-section="results">
			<div class="sol-wrap sol-results-inner">
				<div class="sol-results-text">
					<div class="sol-eyebrow">Was die Diagnose liefert</div>
					<h2 class="sol-display sol-results-h">
						Klarheit, keine <em>Folien</em>.
					</h2>
					<p class="sol-results-sub">
						Vier Module · schriftlicher Befund nach 7 Werktagen · drei priorisierte Hebel · eine Wirtschaftlichkeits-Einordnung — als belastbare Entscheidungsgrundlage, nicht als Pitch-Deck.
					</p>
					<ul class="sol-results-list">
						<?php foreach ( $results_qualifiers as $row ) : ?>
							<li>
								<span><?php echo esc_html( $row['k'] ); ?></span>
								<span class="sol-mono sol-results-list-v"><?php echo esc_html( $row['v'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>

					<a
						class="sol-btn sol-btn-ghost"
						href="<?php echo esc_url( $e3_url ); ?>"
						data-track-action="cta_solar_to_e3_case"
						data-track-category="proof"
						data-track-section="results"
						style="margin-top:32px;"
					>
						Vollständige Methodik im <?php echo esc_html( $e3_case_label ); ?>-Case
						<span class="sol-btn-arrow"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
				</div>

				<div class="sol-results-mock" aria-label="<?php echo esc_attr( $e3_case_label ); ?> — Methodik-Snapshot">
					<div class="sol-results-mock-head">
						<div class="sol-results-mock-dots" aria-hidden="true">
							<span></span><span></span><span></span>
						</div>
						<div class="sol-mono sol-results-mock-title"><?php echo esc_html( $e3_case_label ); ?> · Methodik-Snapshot</div>
						<div class="sol-mono sol-results-mock-live">
							<span class="sol-live-dot" aria-hidden="true"></span>
							<?php echo esc_html( $e3_timeframe ); ?>
						</div>
					</div>
					<div class="sol-results-mock-body">
						<div class="sol-results-mock-row">
							<div class="sol-results-mock-stat">
								<div class="sol-mono">Anfragen</div>
								<div class="sol-display sol-results-mock-big"><?php echo esc_html( $e3_lead_count ); ?></div>
								<div class="sol-results-mock-delta">qualifiziert · in <?php echo esc_html( $e3_timeframe_dative ); ?></div>
							</div>
							<div class="sol-results-mock-stat">
								<div class="sol-mono">Abschlussquote</div>
								<div class="sol-display sol-results-mock-big"><?php echo esc_html( $e3_sales_conversion ); ?></div>
								<div class="sol-results-mock-delta">Anfrage → Vertrag</div>
							</div>
							<div class="sol-results-mock-stat">
								<div class="sol-mono">CPL</div>
								<div class="sol-display sol-results-mock-big"><?php echo esc_html( $e3_cpl_after ); ?></div>
								<div class="sol-results-mock-delta">von <?php echo esc_html( $e3_cpl_before ); ?> · <?php echo esc_html( $e3_cpl_reduction ); ?></div>
							</div>
						</div>
						<div class="sol-results-mock-chart" aria-hidden="true">
							<svg viewBox="0 0 400 120" preserveAspectRatio="none" width="100%" height="120">
								<defs>
									<linearGradient id="sol-rg" x1="0" x2="0" y1="0" y2="1">
										<stop offset="0%" stop-color="currentColor" stop-opacity=".25" />
										<stop offset="100%" stop-color="currentColor" stop-opacity="0" />
									</linearGradient>
								</defs>
								<g style="color: var(--sol-accent);">
									<path d="M0 96 L40 90 L80 80 L120 72 L160 66 L200 56 L240 46 L280 38 L320 30 L360 22 L400 16 L400 120 L0 120 Z" fill="url(#sol-rg)" />
									<path d="M0 96 L40 90 L80 80 L120 72 L160 66 L200 56 L240 46 L280 38 L320 30 L360 22 L400 16" fill="none" stroke="currentColor" stroke-width="1.5" />
									<circle cx="40"  cy="90" r="3" fill="currentColor" />
									<circle cx="120" cy="72" r="3" fill="currentColor" />
									<circle cx="200" cy="56" r="3" fill="currentColor" />
									<circle cx="280" cy="38" r="3" fill="currentColor" />
									<circle cx="360" cy="22" r="3" fill="currentColor" />
								</g>
							</svg>
							<div class="sol-results-mock-axis sol-mono">
								<span>Mon 1</span><span>Mon 3</span><span>Mon 5</span><span>Mon 7</span><span>Mon 9</span>
							</div>
						</div>
						<div class="sol-results-mock-list">
							<div class="sol-results-mock-item">
								<span class="sol-results-mock-tag is-new">Neu</span>
								<span>Anfrage-Quellen quantifiziert</span>
								<span class="sol-results-mock-prod sol-mono">Modul 01</span>
								<span class="sol-results-mock-time">7 d</span>
							</div>
							<div class="sol-results-mock-item">
								<span class="sol-results-mock-tag is-call">Befund</span>
								<span>Drei priorisierte Hebel</span>
								<span class="sol-results-mock-prod sol-mono">Output</span>
								<span class="sol-results-mock-time">7 d</span>
							</div>
							<div class="sol-results-mock-item">
								<span class="sol-results-mock-tag is-booked">Übergabe</span>
								<span>30-Min-Call · optional</span>
								<span class="sol-results-mock-prod sol-mono">Wahlfrei</span>
								<span class="sol-results-mock-time">+ 1 Wo</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     FIT — passt / passt nicht (ehrliche Vorauswahl)
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-section--tinted sol-fit-section" id="fit" data-track-section="fit_check">
			<div class="sol-wrap">
				<div class="sol-fit-head">
					<div class="sol-eyebrow">Wann es sich lohnt</div>
					<h2 class="sol-display sol-fit-h">
						Ehrliche Vorauswahl, <em>bevor wir reden</em>.
					</h2>
					<p class="sol-fit-sub">
						Lieber jetzt klären, ob es passt — als später ein Setup zu bauen, das ins Leere läuft.
					</p>
				</div>

				<div class="sol-fit-grid">
					<article class="sol-fit-col sol-fit-col--yes">
						<header class="sol-fit-col-head">
							<span class="sol-fit-col-badge sol-fit-col-badge--yes" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="M5 12l5 5L20 7"/></svg>
							</span>
							<span>Passt, wenn …</span>
						</header>
						<ul class="sol-fit-list">
							<?php foreach ( $fit_yes as $row ) : ?>
								<li>
									<div class="sol-fit-list-t"><?php echo esc_html( $row['t'] ); ?></div>
									<div class="sol-fit-list-d"><?php echo esc_html( $row['s'] ); ?></div>
								</li>
							<?php endforeach; ?>
						</ul>
					</article>
					<article class="sol-fit-col sol-fit-col--no">
						<header class="sol-fit-col-head">
							<span class="sol-fit-col-badge sol-fit-col-badge--no" aria-hidden="true">✕</span>
							<span>Passt nicht, wenn …</span>
						</header>
						<ul class="sol-fit-list">
							<?php foreach ( $fit_no as $row ) : ?>
								<li>
									<div class="sol-fit-list-t"><?php echo esc_html( $row['t'] ); ?></div>
									<div class="sol-fit-list-d"><?php echo esc_html( $row['s'] ); ?></div>
								</li>
							<?php endforeach; ?>
						</ul>
					</article>
				</div>

				<div class="sol-fit-cta">
					<a class="sol-btn sol-btn-primary" href="#marktcheck"
						data-track-action="cta_solar_fit_to_marktcheck"
						data-track-category="lead_funnel"
						data-track-section="fit_check"
						data-track-funnel-stage="quiz_open"
					>
						<span>Prüfen, ob es passt</span>
						<span class="sol-btn-arrow"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
					<div class="sol-fit-cta-micro sol-mono">Erst Marktcheck · dann Entscheidung · keine Verkaufspräsentation</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     GUARANTEE
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-guarantee" id="garantie" data-track-section="guarantee">
			<div class="sol-wrap sol-guarantee-inner">
				<div class="sol-guarantee-glow" aria-hidden="true"></div>
				<div class="sol-eyebrow" style="justify-content:center;">Risiko-Umkehr</div>
				<h2 class="sol-display sol-guarantee-h">
					Drei Hebel — auch wenn wir <em>nicht zusammenarbeiten</em>.
				</h2>
				<p class="sol-guarantee-sub">
					Der Marktcheck ist kein Verkaufsritual. Er ist eine ehrliche Ersteinschätzung. Wenn sich daraus keine Zusammenarbeit ergibt, bekommen Sie trotzdem drei priorisierte Hebel mit konkretem nächstem Schritt — auch dann, wenn das heißt, dass Sie nicht mit mir weitermachen.
				</p>
				<div class="sol-guarantee-points">
					<?php foreach ( $guarantee_points as $p ) : ?>
						<div class="sol-guarantee-point">
							<div class="sol-guarantee-point-mark" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
									<path d="M5 12l5 5L20 7" />
								</svg>
							</div>
							<div>
								<div class="sol-guarantee-point-t"><?php echo esc_html( $p['t'] ); ?></div>
								<div class="sol-guarantee-point-s"><?php echo esc_html( $p['s'] ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     FAQ
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-faq" id="faq" data-track-section="faq">
			<div class="sol-wrap sol-faq-inner">
				<div class="sol-faq-left">
					<div class="sol-eyebrow">Häufige Fragen</div>
					<h2 class="sol-display sol-faq-h">
						Bevor Sie <em>fragen</em>.
					</h2>
					<p class="sol-faq-sub">
						Was hier nicht beantwortet wird, klären wir im Marktcheck — kurz, schriftlich, ohne Verkaufsgespräch.
					</p>
				</div>
				<ul class="sol-faq-list">
					<?php foreach ( $faq_items as $i => $item ) : ?>
						<li class="sol-faq-item<?php echo 0 === $i ? ' is-open' : ''; ?>">
							<button type="button" class="sol-faq-q" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>">
								<span class="sol-faq-q-n"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<span class="sol-faq-q-t"><?php echo esc_html( $item['question'] ); ?></span>
								<span class="sol-faq-q-mark" aria-hidden="true">
									<span></span><span></span>
								</span>
							</button>
							<div class="sol-faq-a-wrap">
								<div class="sol-faq-a"><?php echo esc_html( $item['answer'] ); ?></div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     FINAL CTA — zurück zum Marktcheck im Hero
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-section sol-final" data-track-section="final_cta">
			<div class="sol-wrap">
				<div class="sol-final-inner">
					<div class="sol-final-sun" aria-hidden="true">
						<div class="sol-final-sun-disc"></div>
					</div>
					<div class="sol-eyebrow" style="justify-content:center;">Letzter Schritt</div>
					<h2 class="sol-display sol-final-h">
						Anfragen <em>besitzen</em>,<br />nicht mieten.
					</h2>
					<p class="sol-final-sub">
						5 Fragen · 60 Sekunden · persönliche Antwort in 24 h.
					</p>
					<a
						class="sol-btn sol-btn-primary sol-final-btn"
						href="#marktcheck"
						data-track-action="cta_solar_final_to_marktcheck"
						data-track-category="lead_funnel"
						data-track-section="final_cta"
						data-track-funnel-stage="quiz_open"
					>
						<span>Marktcheck starten</span>
						<span class="sol-btn-arrow"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
					<div class="sol-final-micro">Kein Pitch · kein Folien-Deck · konkreter nächster Schritt</div>
				</div>
			</div>
		</section>

		<!-- Sticky Mobile-CTA -->
		<a
			class="sol-sticky-cta"
			href="#marktcheck"
			data-track-action="cta_solar_sticky_to_marktcheck"
			data-track-category="lead_funnel"
			data-track-section="sticky_mobile"
			data-track-funnel-stage="quiz_open"
		>
			<span>Marktcheck starten · 60 Sek</span>
			<span class="sol-sticky-cta-arrow" aria-hidden="true"><?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</a>

	</div><!-- /.solara-landing -->
</main>

<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

<?php get_footer(); ?>
