<?php
/**
 * Template Name: Solar & Wärmepumpen Leadgenerierung (SOLARA)
 * Description: v2 · Ink/Creme-Wechsel im hasimuener.de-System (.hu-hp-Kit),
 *              Kupfer als einziger Akzent. Primärer Lead-Pfad: B2B-Marktcheck
 *              im Hero (REST → CRM), CPL-Telemetrie als Proof daneben.
 *              Zielgruppe: Solar-/Wärmepumpen-Betriebe mit hohen Projektwerten,
 *              klarem Zielgebiet und eigener Vertriebsverantwortung.
 *
 * Währungs-Doktrin: ausnahmslos EUR (€). Niemals $ oder generische Symbole.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url     = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_url       = home_url( '/case-study-solar-leadgenerierung/' );
$privacy_url  = home_url( '/datenschutz/' );
$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$tracking_url = $primary_urls['tracking'] ?? home_url( '/ga4-tracking-setup/' );
$cwv_url      = $primary_urls['cwv'] ?? home_url( '/wgos-assets/cwv-optimierung/' );
$cro_url      = $primary_urls['cro'] ?? home_url( '/wordpress-agentur-hannover/#methode' );
$about_url    = $primary_urls['about'] ?? home_url( '/uber-mich/' );
$seo_url      = $primary_urls['seo'] ?? home_url( '/wordpress-agentur-hannover/#technisches-seo' );
$paid_url     = $primary_urls['performance_marketing'] ?? home_url( '/performance-marketing/' );
$cal_url      = function_exists( 'hu_get_analysis_calcom_base_url' )
	? hu_get_analysis_calcom_base_url()
	: 'https://cal.com/hasim-uener/30min?overlayCalendar=true';

// ── E3-Proof-Metriken (Canon) ──────────────────────────────────
$e3_canon            = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics          = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label       = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';
$e3_lead_count       = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conversion = $e3_metrics['sales_conversion']['display'] ?? '12 %';
$e3_conv_before      = $e3_metrics['sales_conversion_before']['display'] ?? '1 – 5 %';
$e3_conv_uplift      = $e3_metrics['sales_conversion_uplift']['display'] ?? '1 – 5 % → 12 %';
$e3_cpl_reduction    = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_cpl_before       = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after        = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_cpl_before_val   = (int) ( $e3_metrics['cpl_before']['value'] ?? 150 );
$e3_cpl_after_val    = (int) ( $e3_metrics['cpl_after']['value'] ?? 22 );
$e3_timeframe        = $e3_metrics['timeframe']['display'] ?? '6 Monate';
$e3_timeframe_dative = $e3_metrics['timeframe']['display_dative'] ?? '6 Monaten';

// Sofortkontakt-Setup: Preis aus dem pricing-canon, damit Nav-Label,
// Rechner-CTA und Angebots-Panel nicht auseinanderlaufen.
$entry_price     = function_exists( 'hu_entry_setup_price' ) ? hu_entry_setup_price() : '790 €';
$entry_price_net = function_exists( 'hu_entry_setup_price' ) ? hu_entry_setup_price( true ) : '790 € netto';

// ── Inhaltsmodelle ─────────────────────────────────────────────
$trust_items = [
	'Solar · Wärmepumpe · Speicher · DACH',
	'Server in Frankfurt · Daten in der EU',
	'Tracking, das Ad-Blocker übersteht',
	'Eigener Code statt Baukasten',
	'Immer direkt mit mir',
	'Marktcheck · Fit-Befund in 48 h',
];

// 01 / Status quo — drei Kostenkarten (Creme)
$cost_cards = [
	[
		'big'  => '80–150 €',
		'sub'  => 'pro Portal-Lead — geteilt',
		'body' => 'Aroundhome, DAA, Wattfox: Mieter ohne Dach, Preisvergleicher ohne Budget — Anfragen, die parallel bei drei Wettbewerbern landen. Die Hälfte geht nicht ans Telefon.',
	],
	[
		'big'  => 'Blindflug',
		'sub'  => 'Sie wissen nicht, welcher Euro verkauft',
		'body' => 'Google Ads, Portale und Empfehlungen laufen parallel. Kommt ein Auftrag über 40.000 € herein, kann niemand sagen, welcher Kanal ihn gebracht hat. Also wird nach Bauchgefühl verteilt — und der schwächste Kanal bekommt oft am meisten.',
	],
	[
		'big'  => 'Seit 2024',
		'sub'  => 'kommen Anfragen nicht mehr von allein',
		'body' => 'Der PV-Boom ist vorbei, der Markt normalisiert sich. Ohne eigene Nachfrage-Infrastruktur bleiben Sie abhängig von Portalen — und deren Preisen.',
	],
];

// 02 / Markt vs. eigener Weg (Compare)
$compare_bad = [
	[ 't' => 'Portal-Leads',      's' => 'Drei Wettbewerber bekommen dieselbe Anfrage. Sie bieten gegen den Preis.' ],
	[ 't' => 'Klickberichte',     's' => 'Reportings über Impressionen — nicht über Projektwert.' ],
	[ 't' => '5-Felder-Formular', 's' => 'Verliert Interessenten, bevor sie qualifiziert sind.' ],
	[ 't' => 'Black-Box-Agentur', 's' => 'Niemand weiß, woher die nächste Anfrage kommt — oder warum sie ausbleibt.' ],
];
$compare_good = [
	[ 't' => 'Eigene Anfragestrecke',    's' => 'Anfragen, die Ihrem Betrieb gehören — nicht dem Portal.' ],
	[ 't' => 'Anfragequalität messbar',  's' => 'Region, Heizart, Dach, Projektwert — vor dem Anruf.' ],
	[ 't' => 'Vorqualifizierung zuerst', 's' => 'Geschäftliche Daten erst nach problembasierter Vorqualifizierung. Kein 5-Felder-Hürdenlauf.' ],
	[ 't' => 'Dokumentiertes System',    's' => 'Sie verstehen, warum es funktioniert. Code, Tracking, Daten bleiben bei Ihnen.' ],
];

// 03 / Das System — Asset-Panel (links) + drei Schritte (rechts)
$process_rows = [
	[ 'e' => 'Fundament',          't' => 'Eigener Code statt Baukasten · Server in Frankfurt', 'url' => $cwv_url ],
	[ 'e' => 'Daten & Tracking',   't' => 'Jede Anfrage bis zur Anzeige zurückverfolgbar',      'url' => $tracking_url ],
	[ 'e' => 'Weg zur Anfrage',    't' => 'Vorqualifizierung · Fit bekannt vor dem Anruf',      'url' => $cro_url ],
];
$method_cards = [
	[
		'n'   => '01',
		't'   => 'Diagnose & Fundament',
		's'   => 'Anfrage-Quellen, Tracking, Funnel, Vertriebsanschluss — vier Bausteine, ein schriftlicher Befund, drei priorisierte Hebel. Auf Umsetzung 1:1 verrechenbar.',
		'out' => 'Output / Befund · keine Folien · verrechenbar',
	],
	[
		'n'   => '02',
		't'   => 'Eigenes Anfrage-System',
		's'   => 'Die Seite, auf der Ihre Anfragen entstehen, plus Beleg- und Angebotsseiten. <a href="' . esc_url( $tracking_url ) . '">Server-Side-Tracking</a> auf eigenem Server — belastbare Zahlen trotz Ad-Blockern. <a href="' . esc_url( $cro_url ) . '">Vorqualifizierung</a> vor dem Formular statt 5-Felder-Hürdenlauf.',
		'out' => 'Output / System · Frankfurt · CAPI · Lead-Scoring',
	],
	[
		'n'   => '03',
		't'   => 'Skalieren & Übergeben',
		's'   => 'Auf sauberem <a href="' . esc_url( $cwv_url ) . '">technischen Fundament</a> rechnen sich <a href="' . esc_url( $paid_url ) . '">Google Ads und Meta Ads</a> endlich — plus <a href="' . esc_url( $seo_url ) . '">SEO-Anteil</a>. Wöchentliches Reporting. Bei Vertragsende: dokumentierte Übergabe.',
		'out' => 'Output / Skalierung · monatlich kündbar · 100 % Übergabe',
	],
];

// 04 / CAPEX vs OPEX · Zahlen aus dem messaging-canon ──────────
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
$capex_now     = $capex_timeframes[ $capex_default ];

// 04b / Portal-Kosten-Rechner ─────────────────────────────────
// Eingabewerte des Nutzers × Canon-Zahlen. Bewusst KEINE Prognose:
// die Ausgabe ist eine Gegenüberstellung, kein Ersparnis-Versprechen.
// Setup-Spanne und Hosting stammen aus derselben Quelle wie die
// Modellkarten oben ($capex_timeframes) — Zahlen bleiben konsistent.
$calc_setup_low  = 12000;
$calc_setup_high = 18000;
$calc_hosting    = 50;
$calc_leads      = 14;
$calc_price      = 80;

$calc_portal_total = $calc_leads * $calc_price * $capex_default;
$calc_own_low      = $calc_setup_low + $calc_hosting * $capex_default;
$calc_own_high     = $calc_setup_high + $calc_hosting * $capex_default;
$calc_diff_low     = $calc_portal_total - $calc_own_high;
$calc_diff_high    = $calc_portal_total - $calc_own_low;

// Deutsche Tausenderpunkte, Währungs-Doktrin EUR (siehe Dateikopf).
$calc_eur = static function ( $value ) {
	return number_format( (float) $value, 0, ',', '.' ) . ' €';
};

// 07 / Fit-Check (passt / passt nicht) ─────────────────────────
$fit_yes = [
	[ 't' => 'Solar, Wärmepumpe oder Speicher',     's' => 'Projektwert, Marge und Vertriebsfähigkeit stimmen.' ],
	[ 't' => 'Eigener Vertrieb',                    's' => 'Ihr Team verkauft selbst — oder die Geschäftsführung übernimmt den Abschluss.' ],
	[ 't' => 'Klares Zielgebiet',                   's' => 'Region oder Bundesland definiert — kein „bundesweit, alles".' ],
	[ 't' => 'Hohe Projektwerte',                   's' => 'B2C ab ca. 15 k €, B2B ab ca. 50 k € pro Projekt.' ],
	[ 't' => '12–24-Monate-Horizont',               's' => 'Bereit, ein Anfrage-Asset aufzubauen statt nur Anfragen einzukaufen.' ],
	[ 't' => 'Marke und Sichtbarkeit sind gewollt', 's' => 'Der eigene Anfrageweg lebt davon, dass Ihr Betrieb sichtbar und unterscheidbar wird.' ],
];
$fit_no = [
	[ 't' => 'Reines Vermittlungsgeschäft',                 's' => 'Wer Leads weiterverkauft, braucht kein eigenes System.' ],
	[ 't' => '„Nächste Woche brauchen wir Leads."',         's' => 'Tragfähige Pipelines wachsen über Monate, nicht Tage.' ],
	[ 't' => 'Kein funktionierender Vertriebsprozess',      's' => 'Anfragen sterben, wenn niemand konsequent qualifiziert, zurückruft und nachfasst.' ],
	[ 't' => 'Keine Marke gewollt',                         's' => 'Eigener Anfrageweg lebt davon, dass Sie sichtbar werden.' ],
	[ 't' => 'Kein sauberer Daten- und Anfrageweg gewollt', 's' => 'Ohne Tracking, Vorqualifizierung und klare Zuständigkeit bleibt auch ein eigenes System blind.' ],
];

// 08 / Risiko-Umkehr ───────────────────────────────────────────
$guarantee_points = [
	[
		'e' => 'Befund',
		't' => 'Marktcheck schafft Entscheidungsgrundlage',
		's' => 'Händisch geprüfter Befund Ihrer Domain und Region innerhalb von 48 Stunden per E-Mail — mit Fit-Einschätzung und klarer Empfehlung für oder gegen den nächsten Schritt.',
	],
	[
		'e' => 'Abrat inklusive',
		't' => 'Drei priorisierte Hebel — auch bei Nein',
		's' => 'Kommt die Diagnose zum Ergebnis, dass Sie das volle System nicht brauchen, bekommen Sie trotzdem drei Hebel mit konkretem nächstem Schritt.',
	],
	[
		'e' => 'Verrechnung',
		't' => 'Diagnose wird 1:1 angerechnet',
		's' => 'Bei Umsetzung zahlen Sie die Diagnose nicht doppelt. Die Größenordnung kennen Sie vorher: 12.000–18.000 € einmalig für den Aufbau, danach rund 50 € Hosting im Monat. Keine Mindestlaufzeit, volle Asset-Übergabe.',
	],
];

// ── Sticky In-Page Section Nav (CRO: Orientierung + Jump) ─────
$section_nav = [
	[ 'h' => '#problem',    'l' => 'Status quo' ],
	[ 'h' => '#vergleich',  'l' => 'Vergleich' ],
	[ 'h' => '#system',     'l' => 'System' ],
	[ 'h' => '#capex',      'l' => 'Rechnung' ],
	[ 'h' => '#einstieg',   'l' => 'Ab ' . $entry_price ],
	[ 'h' => '#ergebnisse', 'l' => 'Referenz' ],
	[ 'h' => '#fit',        'l' => 'Passt es?' ],
	[ 'h' => '#deeper',     'l' => 'Vertiefung' ],
	[ 'h' => '#faq',        'l' => 'FAQ' ],
];

// ── Vertiefung: SEO-Sub-Pages-Cluster (Topical Authority) ──────
$deeper_clusters = [
	[
		'group' => 'Strategie & Vergleich',
		'items' => [
			[ 't' => 'Solar Leads kaufen? CPL-Rechnung pro Anfrage',  's' => 'Markteinordnung der Lead-Anbieter und Kosten pro Anfrage.',     'url' => home_url( '/solar-leads-kaufen-alternative/' ) ],
			[ 't' => 'TCO 24/36 Monate: Portal vs. eigenes System',   's' => 'Strategischer 8-Kriterien-Vergleich mit Asset-Eigentum-Logik.', 'url' => home_url( '/eigene-leadgenerierung-vs-portale/' ) ],
		],
	],
	[
		'group' => 'Lead-Qualität & CPL',
		'items' => [
			[ 't' => 'Was kosten Solar-Leads? (Marktstudie)', 's' => 'DACH-Preise je Modell und warum Cost-per-Order statt Cost-per-Lead zählt.', 'url' => home_url( '/solar-leads-kosten-studie/' ) ],
			[ 't' => 'Cost per Lead Photovoltaik',           's' => 'Drei Szenarien im CPL-Vergleich und versteckte Kostentreiber.',     'url' => home_url( '/cost-per-lead-photovoltaik/' ) ],
			[ 't' => 'Qualifizierte PV-Anfragen',            's' => 'Vier Merkmale für hochwertige Solar-Anfragen plus Warnsignale.',   'url' => home_url( '/qualifizierte-pv-anfragen/' ) ],
		],
	],
	[
		'group' => 'Funnel & Tracking',
		'items' => [
			[ 't' => 'Lead-Funnel Solar',                    's' => 'Fünf Stufen einer belastbaren Solar-Funnel-Architektur.',          'url' => home_url( '/lead-funnel-solar/' ) ],
			[ 't' => 'Server-Side Tracking für B2B',         's' => 'GA4, Meta CAPI und Consent Mode v2 — DSGVO-konform auf eigenem Server.', 'url' => home_url( '/server-side-tracking-b2b/' ) ],
		],
	],
	[
		'group' => 'Zielgruppen & Marktbild',
		'items' => [
			[ 't' => 'B2B Solar Leads (Gewerbe)',            's' => 'Buying-Center-Funnel für gewerbliche Photovoltaik-Projekte.',       'url' => home_url( '/b2b-solar-leads/' ) ],
			[ 't' => 'Kunden gewinnen für Solarteure',       's' => 'Mythen-Aufklärung und fünf systematische Hebel im DACH-Mittelstand.', 'url' => home_url( '/kunden-gewinnen-solarteure/' ) ],
		],
	],
];

$faq_items = [
	[
		'question' => 'Wie läuft der Marktcheck konkret ab und wie lange dauert er?',
		'answer'   => 'Drei strukturierte Schritte: Vertriebsteam-Größe, Portal-Margenverlust, geschäftliche Eckdaten. Den händisch geprüften Infrastruktur-Befund Ihrer Domain und Region erhalten Sie in der Regel innerhalb von 48 Stunden, spätestens 2 Werktage, per E-Mail. Keine automatisierten Standard-PDFs, sondern eine strategische Einordnung für Geschäftsführung und Vertriebsleitung.',
	],
	[
		'question' => 'Was passiert nach dem Marktcheck?',
		'answer'   => 'Ich lese Ihre Antworten persönlich und melde mich in der Regel innerhalb von 48 Stunden, spätestens 2 Werktage, per E-Mail. Wenn der Fit passt, schlage ich ein 30-minütiges Erstgespräch vor oder lade Sie in die verrechenbare Tiefendiagnose ein. Wenn der Fit nicht passt, sage ich das ehrlich und nenne Ihnen die realistischere Alternative.',
	],
	[
		'question' => 'Was kostet das im Vergleich zur Performance-Agentur?',
		'answer'   => 'Initiales Setup: 12.000–18.000 € einmalig. Laufend ca. 50 €/Monat für Hochleistungs-Hosting. TCO über 24 Monate: 13.200–19.200 € — und Sie besitzen Code, Tracking und Daten. Ein vergleichbares Agentur-Mietmodell kostet im gleichen Zeitraum rund 26.000 € — und Sie besitzen am Ende nichts. Bilanziell: CAPEX statt OPEX. Wenn das zu groß gedacht ist: Der Einstieg über das Sofortkontakt-Setup liegt bei ' . $entry_price_net . ' und ist in fünf Werktagen eingerichtet.',
	],
	[
		'question' => 'Gibt es einen kleineren Einstieg als das volle System?',
		'answer'   => 'Ja. Das Sofortkontakt-Setup für ' . $entry_price_net . ' richtet Sofort-Alarm, automatische Eingangsbestätigung und eine Anfragen-Übersicht nach Quelle ein — auch für Leads, die Sie bereits bei Portalen kaufen. In fünf Werktagen fertig, ohne zusätzliches Werbebudget. Danach haben Sie eigene Zahlen zur Hand und können in Ruhe entscheiden, ob ein eigenes System sich rechnet.',
	],
	[
		'question' => 'Welche Daten brauchen Sie für die Diagnose?',
		'answer'   => 'Für den Marktcheck reichen zwei Klick-Antworten plus geschäftliche Eckdaten (Firma, Ansprechpartner, Position, geschäftliche E-Mail, Firmen-PLZ). Für die Tiefendiagnose: Lesezugriff auf Google Analytics, Google Ads und Meta Ads Manager, Einblick in den CRM-Datenbestand der letzten 90 Tage und eine 15-Minuten-Bestandsaufnahme zu Vertriebsprozess und Lead-Quellen. Wenn Tracking-Daten fehlen, ist das oft schon das erste Diagnose-Ergebnis.',
	],
	[
		'question' => 'Warum nicht einfach mehr Google Ads schalten?',
		'answer'   => 'Mehr Budget auf schlechte Seiten heißt mehr Geld für dieselben unqualifizierten Anfragen. Erst wenn Seite, Formular und serverseitiges Tracking sauber arbeiten, lohnt sich mehr Reichweite. Ohne eigene Datenebene bleiben Sie zudem in der Logik der Plattform.',
	],
	[
		'question' => 'Brauchen wir eine neue Website?',
		'answer'   => 'Meistens nicht im Komplettumfang. Was Sie brauchen: eigenen Code statt Baukasten, serverseitiges Tracking auf eigenem Server und einen Weg vom Klick bis zur Anfrage, der Ihnen gehört statt gemietet zu sein. Ob das ein Teil-Umbau oder ein sauberer Erstaufbau wird, zeigt sich im ersten Schritt.',
	],
	[
		'question' => 'Wir nutzen schon eine Performance-Agentur — warum sollten wir wechseln?',
		'answer'   => 'Müssen Sie nicht. Drei Prüffragen: 1) Wem gehört der Code Ihrer Landingpage? 2) Wem gehört das CRM, in dem Ihre Leads liegen? 3) Wem gehört der Tracking-Account? Wenn die Antwort dreimal „uns" ist, brauchen Sie mich nicht. Wenn die Antwort dreimal „der Agentur" ist, mieten Sie ein System.',
	],
	[
		'question' => 'Wie schnell sieht man Ergebnisse?',
		'answer'   => 'Erste Verbesserungen entstehen oft nach den ersten Optimierungen an Tracking, Formularen und dem Weg zur Anfrage. Belastbare Skalierung braucht in der Regel mehrere Wochen bis Monate — weil Daten, Tests und Kanäle zusammenspielen müssen.',
	],
	[
		'question' => 'Was unterscheidet Sie von Lead-Portalen?',
		'answer'   => 'Portale vermieten Nachfrage. Sie zahlen für jeden Kontakt, den auch drei Mitbewerber erhalten. Das System hier baut eigene Nachfrage-Infrastruktur auf, die Ihrem Betrieb gehört und langfristig für exklusive Anfragen sorgt.',
	],
	[
		'question' => 'Was ist B2B Solar Leadgenerierung — und wie funktioniert sie ohne Portale wie Aroundhome, DAA oder Wattfox?',
		'answer'   => 'B2B Solar Leadgenerierung ist der Aufbau einer eigenen Nachfrage-Infrastruktur für Solar-, Wärmepumpen- und Speicheranbieter. Anders als Portale (Aroundhome, DAA, Wattfox), die identische Anfragen parallel an drei Wettbewerber verkaufen, gehören die Anfragen hier exklusiv Ihrem Betrieb. Die Infrastruktur besteht aus drei Schichten: einer hardcoded WordPress-Money-Page, serverseitigem Tracking (GA4 + Meta CAPI auf eigenem Server) und einem mehrstufigen Lead-Scoring vor dem Erstkontakt.',
	],
	[
		'question' => 'Wie unterscheidet sich „eigene Solar Leads gewinnen" von Photovoltaik-Leadkauf?',
		'answer'   => 'Beim Leadkauf zahlen Sie 80–150 € pro Kontakt — geteilt mit drei Wettbewerbern, oft ohne Telefonnummer, häufig ohne Budget. Eigene Solar Leads werden über Ihre eigene Domain generiert, über Ihre Vorqualifizierung gefiltert und landen exklusiv in Ihrem CRM. Über 24 Monate liegen die Gesamtkosten dabei rund 50 % unter dem reinen Portal-Modell — und Sie besitzen am Ende ein aktivierbares Asset.',
	],
];

// ── Schema.org ─────────────────────────────────────────────────
$organization_id = trailingslashit( home_url( '/' ) ) . '#organization';
$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'B2B Solar Leadgenerierung — Aufbau eigener Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter',
	'alternateName' => [ 'Photovoltaik Leadgenerierung', 'Eigene Solar Leads gewinnen', 'B2B Solar Leads' ],
	'serviceType' => 'Eigene Anfrage-Infrastruktur für Solar- und Wärmepumpen-Anbieter: WordPress hardcoded, Server-Side-Tracking, Lead-Scoring und CRM-Übergabe',
	'category'    => 'B2B Lead Generation Infrastructure',
	'url'         => $page_url,
	'mainEntityOfPage' => $page_url,
	'description' => sprintf( 'B2B-Leadgenerierung für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Raum. Souveräne Nachfrage-Infrastruktur statt geteilter Portal-Leads. Referenz %1$s: %2$s weniger Kosten pro Anfrage in %3$s (CPL von %4$s auf %5$s).', $e3_case_label, $e3_cpl_reduction, $e3_timeframe_dative, $e3_cpl_before, $e3_cpl_after ),
	'provider'    => [
		'@type'       => 'Organization',
		'@id'         => $organization_id,
		'name'        => 'Haşim Üner — Anfrage-Systeme für Solar & Wärmepumpe',
		'url'         => home_url( '/' ),
		'founder'     => [
			'@id' => function_exists( 'hu_person_schema_id' ) ? hu_person_schema_id() : home_url( '/uber-mich/#person' ),
		],
	],
	'audience'    => [
		'@type'        => 'BusinessAudience',
		'audienceType' => 'Solar-, Wärmepumpen-, Speicher- und Energie-Anbieter im DACH-Mittelstand mit eigenem Vertrieb',
	],
	'areaServed'  => [
		[ '@type' => 'Country', 'name' => 'Deutschland' ],
		[ '@type' => 'Country', 'name' => 'Österreich' ],
		[ '@type' => 'Country', 'name' => 'Schweiz' ],
	],
	'serviceOutput' => [
		'@type' => 'Thing',
		'name'  => 'Eigene Anfrage-Infrastruktur',
		'description' => 'Eigene, exklusive Nachfrage-Infrastruktur — WordPress hardcoded, Server-Side-Tracking, Lead-Scoring. Code, Tracking und Daten verbleiben beim Auftraggeber.',
	],
	'additionalProperty' => [
		[
			'@type' => 'PropertyValue',
			'name'  => 'serviceOutput',
			'value' => 'Eigene Anfrage-Infrastruktur',
		],
		[
			'@type' => 'PropertyValue',
			'name'  => 'cplReduction',
			'value' => $e3_cpl_reduction,
		],
		[
			'@type' => 'PropertyValue',
			'name'  => 'assetOwnership',
			'value' => '100 % — Code, Tracking, Daten beim Auftraggeber',
		],
	],
	'offers'      => [
		'@type'         => 'Offer',
		'price'         => '0',
		'priceCurrency' => 'EUR',
		'description'   => 'Marktcheck mit händisch geprüftem Fit-Befund Ihrer Region innerhalb von 48 Stunden per E-Mail.',
		'availability'  => 'https://schema.org/InStock',
	],
	'isRelatedTo' => [
		[ '@type' => 'WebPage', 'url' => home_url( '/cost-per-lead-photovoltaik/' ),  'name' => 'Cost per Lead Photovoltaik' ],
		[ '@type' => 'WebPage', 'url' => home_url( '/qualifizierte-pv-anfragen/' ),   'name' => 'Qualifizierte PV-Anfragen' ],
		[ '@type' => 'WebPage', 'url' => home_url( '/b2b-solar-leads/' ),             'name' => 'B2B Solar Leads' ],
		[ '@type' => 'WebPage', 'url' => home_url( '/lead-funnel-solar/' ),           'name' => 'Lead-Funnel Solar' ],
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
	<div class="solara-landing hu-hp" data-track-section="energy_service_landing">

		<!-- ════════════════════════════════════════════════════════════
		     HERO — Ink, Kupfer-Sonne, CPL-Telemetrie + Marktcheck-Card
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-hero sol-hero" id="hero" data-track-section="hero">
			<div class="hu-hero__grid-bg" aria-hidden="true"></div>

			<?php
			// Kupfer-Sonne: 32 Strahlen, alternierende Länge/Stärke,
			// langsam rotierend (CSS), rein dekorativ.
			?>
			<svg class="sol-sun" viewBox="0 0 600 600" aria-hidden="true" focusable="false">
				<defs>
					<radialGradient id="sol-sun-core" cx="50%" cy="50%" r="50%">
						<stop offset="0%" stop-color="#E08A3C" stop-opacity="0.22" />
						<stop offset="60%" stop-color="#E08A3C" stop-opacity="0.07" />
						<stop offset="100%" stop-color="#E08A3C" stop-opacity="0" />
					</radialGradient>
				</defs>
				<circle cx="300" cy="300" r="150" fill="url(#sol-sun-core)" />
				<g class="sol-sun-rays">
					<?php
					for ( $ray = 0; $ray < 32; $ray++ ) :
						$angle = $ray / 32 * 2 * M_PI;
						$r1    = 150;
						$r2    = 0 === $ray % 2 ? 225 : 188;
						?>
						<line
							x1="<?php echo esc_attr( (string) round( 300 + cos( $angle ) * $r1, 1 ) ); ?>"
							y1="<?php echo esc_attr( (string) round( 300 + sin( $angle ) * $r1, 1 ) ); ?>"
							x2="<?php echo esc_attr( (string) round( 300 + cos( $angle ) * $r2, 1 ) ); ?>"
							y2="<?php echo esc_attr( (string) round( 300 + sin( $angle ) * $r2, 1 ) ); ?>"
							stroke="#E08A3C" stroke-width="<?php echo esc_attr( 0 === $ray % 2 ? '1.5' : '0.9' ); ?>"
							stroke-linecap="round" opacity="0.4" />
					<?php endfor; ?>
				</g>
				<circle cx="300" cy="300" r="150" fill="none" stroke="#E08A3C" stroke-width="1" opacity="0.3" />
				<circle cx="300" cy="300" r="122" fill="none" stroke="#E08A3C" stroke-width="0.7" opacity="0.18" stroke-dasharray="1 6" />
			</svg>

			<div class="hu-container hu-hero__container sol-hero-inner">
				<div class="sol-hero-left">
					<span class="hu-tag">
						<span class="hu-dot hu-dot--live" aria-hidden="true"></span>
						<span class="hu-mono">Für Solar- &amp; Wärmepumpen-Betriebe · Eigener Vertrieb · DACH</span>
					</span>
					<h1 class="hu-display hu-hero__title sol-hero-h1">
						<?php
					// Kein hartes <br />: .hu-display setzt `text-wrap: balance`,
					// das bricht ueber alle Breiten sauberer als ein fixer Umbruch
					// mitten im Kompositum. &nbsp; haelt nur „zu mieten." zusammen.
					?>
					Hören Sie auf, Photovoltaik-Anfragen zu&nbsp;<span class="hu-hero__title-2">mieten.</span>
					</h1>
					<p class="hu-hero__sub">
						Aroundhome, DAA und Wattfox verkaufen dieselbe Anfrage an drei Betriebe — Sie bieten gegen den Preis.
						Ein eigenes Anfrage-System für Photovoltaik, Wärmepumpe und Speicher zeigt
						<strong>Region, Dach und Projektwert</strong> vor dem ersten Anruf.
						Der Kanal gehört Ihnen, nicht dem Portal.
					</p>

					<?php
					// CPL-Telemetrie: 6 Kupfer-Balken, Endpunkte aus dem E3-Canon
					// (150 € → 22 €), Zwischenwerte kosmetische Kaskade.
					$cpl_bars  = [ $e3_cpl_before_val, 96, 61, 42, 30, $e3_cpl_after_val ];
					$cpl_w     = 520;
					$cpl_h     = 210;
					$cpl_pad_l = 16;
					$cpl_pad_t = 22;
					$cpl_pad_b = 30;
					$cpl_max   = 160;
					$cpl_inner = $cpl_h - $cpl_pad_t - $cpl_pad_b;
					$cpl_bw    = ( $cpl_w - $cpl_pad_l - 10 ) / count( $cpl_bars );
					?>
					<figure class="hu-diagram sol-cpl" aria-labelledby="sol-cpl-title" data-track-section="hero_dashboard">
						<div class="hu-lead-sketch__head">
							<span class="hu-eyebrow">CPL-Verlauf · <?php echo esc_html( $e3_case_label ); ?></span>
							<span class="hu-lead-sketch__status" id="sol-cpl-title">Kosten pro qualifizierter Anfrage · <?php echo esc_html( $e3_timeframe ); ?></span>
						</div>
						<svg viewBox="0 0 <?php echo (int) $cpl_w; ?> <?php echo (int) $cpl_h; ?>" role="img"
							aria-label="Kosten pro Anfrage fallen von <?php echo esc_attr( (string) $e3_cpl_before_val ); ?> Euro auf <?php echo esc_attr( (string) $e3_cpl_after_val ); ?> Euro in sechs Monaten">
							<?php
							foreach ( [ 0, 40, 80, 120, 160 ] as $grid_v ) :
								$grid_y = $cpl_pad_t + ( 1 - $grid_v / $cpl_max ) * $cpl_inner;
								?>
								<line class="sol-cpl-grid" x1="<?php echo (int) ( $cpl_pad_l + 16 ); ?>" x2="<?php echo (int) $cpl_w; ?>"
									y1="<?php echo esc_attr( (string) round( $grid_y, 1 ) ); ?>" y2="<?php echo esc_attr( (string) round( $grid_y, 1 ) ); ?>" />
								<text class="sol-cpl-tick" x="<?php echo (int) ( $cpl_pad_l + 10 ); ?>" y="<?php echo esc_attr( (string) round( $grid_y + 3, 1 ) ); ?>"
									text-anchor="end"><?php echo (int) $grid_v; ?></text>
							<?php endforeach; ?>
							<?php
							foreach ( $cpl_bars as $bar_i => $bar_v ) :
								$bar_x    = $cpl_pad_l + 24 + $bar_i * $cpl_bw;
								$bar_h    = $bar_v / $cpl_max * $cpl_inner;
								$bar_y    = $cpl_h - $cpl_pad_b - $bar_h;
								$bar_last = count( $cpl_bars ) - 1 === $bar_i;
								?>
								<rect class="sol-bar<?php echo esc_attr( $bar_last ? ' is-final' : '' ); ?>" style="animation-delay:<?php echo (int) ( 250 + $bar_i * 110 ); ?>ms"
									x="<?php echo esc_attr( (string) round( $bar_x, 1 ) ); ?>" y="<?php echo esc_attr( (string) round( $bar_y, 1 ) ); ?>"
									width="<?php echo esc_attr( (string) round( $cpl_bw * 0.5, 1 ) ); ?>" height="<?php echo esc_attr( (string) round( $bar_h, 1 ) ); ?>"
									rx="2" />
								<text class="sol-bar-label<?php echo esc_attr( $bar_last ? ' is-final' : '' ); ?>" style="animation-delay:<?php echo (int) ( 520 + $bar_i * 110 ); ?>ms"
									x="<?php echo esc_attr( (string) round( $bar_x + $cpl_bw * 0.25, 1 ) ); ?>" y="<?php echo esc_attr( (string) round( $bar_y - 7, 1 ) ); ?>"
									text-anchor="middle"><?php echo (int) $bar_v; ?> €</text>
								<text class="sol-cpl-tick" x="<?php echo esc_attr( (string) round( $bar_x + $cpl_bw * 0.25, 1 ) ); ?>" y="<?php echo (int) ( $cpl_h - $cpl_pad_b + 17 ); ?>"
									text-anchor="middle">M<?php echo (int) ( $bar_i + 1 ); ?></text>
							<?php endforeach; ?>
						</svg>
						<figcaption class="hu-lead-sketch__footer">
							<span>Reduktion <?php echo esc_html( $e3_cpl_reduction ); ?></span>
							<span>DSGVO · Server in Frankfurt</span>
						</figcaption>
					</figure>

					<div class="hu-hero__stats">
						<div>
							<?php // --accent-ink statt --accent: im hellen Hero muss auch die grosse Zahl AA halten. ?>
							<div class="hu-stat-num" style="color:var(--accent-ink);" data-sol-countup><?php echo esc_html( $e3_cpl_after ); ?></div>
							<div class="hu-stat-label">CPL nach <?php echo esc_html( $e3_timeframe_dative ); ?> · <?php echo esc_html( $e3_case_label ); ?></div>
						</div>
						<div class="hu-stat-divider"></div>
						<div>
							<div class="hu-stat-num" data-sol-countup><?php echo esc_html( $e3_lead_count ); ?></div>
							<div class="hu-stat-label">Qualifizierte Anfragen</div>
						</div>
						<div class="hu-stat-divider"></div>
						<div>
							<div class="hu-stat-num" data-sol-countup><?php echo esc_html( $e3_sales_conversion ); ?></div>
							<div class="hu-stat-label">Abschlussquote · Anfrage → Vertrag</div>
						</div>
					</div>

					<ul class="hu-hero__bullets">
						<li><span class="hu-bullet-dot" aria-hidden="true"></span>Befund in 48 h · kein Pflicht-Call</li>
						<li><span class="hu-bullet-dot" aria-hidden="true"></span>Keine Zahlungsdaten, kein Abo</li>
						<li><span class="hu-bullet-dot" aria-hidden="true"></span>Für Solar, Wärmepumpe und Speicher</li>
					</ul>

					<p class="sol-hero-callink hu-mono">
						Bereits qualifizierter Fall?
						<a
							href="<?php echo esc_url( $cal_url ); ?>"
							data-track-action="cta_solar_to_calcom"
							data-track-category="lead_gen"
							data-track-section="hero_secondary"
						>Direkt 30-Min-Gespräch buchen →</a>
					</p>
				</div>

				<aside class="sol-hero-right" aria-labelledby="sol-quiz-title">
					<div class="sol-cta-card" id="marktcheck">
						<!--
						  Marktcheck Mount-Point. JS rendert hier die
						  3-stufige Progressive-Disclosure-Sequenz.
						  Wenn JS fehlt, bleibt der SSR-Fallback aktiv.
						-->
						<div data-sol-quiz id="sol-quiz-mount">
							<noscript>
								<style>.solara-landing .sol-cta-fineprint{display:none!important;}</style>
								<p class="sol-cta-hint">
									Aktivieren Sie JavaScript für den Marktcheck oder schreiben Sie direkt an
									<a href="mailto:hasim@hasimuener.de" style="color:var(--accent);">hasim@hasimuener.de</a>.
								</p>
								<a
									class="sol-cta-submit"
									href="mailto:hasim@hasimuener.de"
									data-track-action="cta_solar_noscript_mail"
									data-track-category="lead_gen"
									data-track-section="hero_noscript"
								>
									<span>Marktcheck per E-Mail beantragen</span>
									<span class="sol-cta-submit-arrow" aria-hidden="true"><?php echo $arrow_svg; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</a>
							</noscript>

							<!-- SSR-Fallback (sichtbar bis JS gemounted hat) -->
							<div class="sol-cta-head">
								<span class="sol-cta-tag sol-mono">
									<span class="sol-cta-tag-dot" aria-hidden="true"></span>
									Marktcheck · Fit geprüft · 48-h-Befund
								</span>
								<span class="sol-cta-head-right sol-mono">Fit-Check</span>
							</div>
							<h2 id="sol-quiz-title" class="sol-cta-title">
								Marktcheck für Ihren Vertrieb starten.
							</h2>
							<p class="sol-cta-hint">
								Drei Schritte, vier Angaben. Sie erhalten einen händisch geprüften Fit-Befund
								für Ihre Region — mit drei priorisierten Hebeln und klarem nächsten Schritt.
								Kein Newsletter, kein Pitch-Deck.
							</p>
							<ul class="sol-cta-bullets sol-mono" aria-label="Was Sie nach dem Marktcheck erhalten">
								<li><span class="sol-cta-bullets-tick" aria-hidden="true">✓</span>Keine Zahlungsdaten, kein Abo, keine Verpflichtung</li>
								<li><span class="sol-cta-bullets-tick" aria-hidden="true">✓</span>Einordnung Ihres Marktumfelds anhand der Firmen-PLZ</li>
								<li><span class="sol-cta-bullets-tick" aria-hidden="true">✓</span>Manuelle Erst-Analyse statt automatisierter Tool-Bericht</li>
								<li><span class="sol-cta-bullets-tick" aria-hidden="true">✓</span>Befund in 48 h per E-Mail · kein Pflicht-Call</li>
							</ul>
							<p class="sol-cta-fineprint">Wird geladen …</p>
						</div>

						<?php
						// Person-Signal: bleibt bewusst AUSSERHALB des Quiz-Mounts,
						// damit es beim JS-Mount nicht mit dem SSR-Fallback wegfällt.
						?>
						<p class="sol-cta-person">
							<span class="sol-cta-person-dot" aria-hidden="true"></span>
							Ihre Antworten liest und beantwortet
							<a href="<?php echo esc_url( $about_url ); ?>"
								data-track-action="cta_solar_intake_to_about"
								data-track-category="trust"
								data-track-section="hero_intake">Haşim Üner</a>
							persönlich — kein Tool, kein automatisches PDF.
						</p>
					</div>
				</aside>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     ANFRAGE-SYSTEM-KETTE — Anzeige → Landingpage → Qualifizierung → CRM
		     Inline-SVG in zwei Layout-Varianten (CSS-Breakpoint 820px),
		     baut sich beim Scroll-Eintritt stufenweise auf.
		     SSR zeigt den Endzustand — ohne JS/bei Reduced Motion statisch.
		     ════════════════════════════════════════════════════════════ -->
		<?php
		$chain_stages = [
			[ 'k' => '01', 't' => 'Anzeige',        's' => 'Klick mit Kaufabsicht' ],
			[ 'k' => '02', 't' => 'Landingpage',    's' => 'Eigener Kanal, eigene Daten' ],
			[ 'k' => '03', 't' => 'Qualifizierung', 's' => 'Region · Dach · Projektwert' ],
			[ 'k' => '04', 't' => 'CRM',            's' => 'Exklusiv für Ihren Vertrieb' ],
		];
		$chain_cx = [ 95, 285, 475, 665 ];
		$chain_my = [ 24, 166, 308, 450 ];
		?>
		<section
			class="sol-chain"
			id="anfrage-system"
			aria-label="Das Anfrage-System im Überblick"
			data-track-section="request_chain"
		>
			<div class="hu-container">
				<span class="hu-eyebrow">Das Anfrage-System · von der Anzeige bis ins CRM</span>
				<figure class="sol-chain-fig">
					<svg class="sol-chain-svg sol-chain-svg--d" viewBox="0 0 760 150" aria-hidden="true" focusable="false">
						<?php foreach ( $chain_stages as $ci => $stage ) : $cx = $chain_cx[ $ci ]; ?>
							<g class="sol-chain-node<?php echo 3 === $ci ? ' sol-chain-node--final' : ''; ?>" style="--sol-d:<?php echo esc_attr( (string) ( $ci * 0.35 ) ); ?>s">
								<rect class="sol-chain-rect" x="<?php echo (int) ( $cx - 75 ); ?>" y="30" width="150" height="78" rx="13" />
								<text class="sol-chain-k" x="<?php echo (int) $cx; ?>" y="57" text-anchor="middle"><?php echo esc_html( $stage['k'] ); ?></text>
								<text class="sol-chain-t" x="<?php echo (int) $cx; ?>" y="80" text-anchor="middle"><?php echo esc_html( $stage['t'] ); ?></text>
								<text class="sol-chain-s" x="<?php echo (int) $cx; ?>" y="130" text-anchor="middle"><?php echo esc_html( $stage['s'] ); ?></text>
							</g>
							<?php if ( $ci < 3 ) : ?>
								<line class="sol-chain-line" pathLength="1" style="--sol-d:<?php echo esc_attr( (string) ( 0.18 + $ci * 0.35 ) ); ?>s"
									x1="<?php echo (int) ( $cx + 81 ); ?>" y1="69" x2="<?php echo (int) ( $chain_cx[ $ci + 1 ] - 81 ); ?>" y2="69" />
							<?php endif; ?>
						<?php endforeach; ?>
					</svg>
					<svg class="sol-chain-svg sol-chain-svg--m" viewBox="0 0 340 550" aria-hidden="true" focusable="false">
						<?php foreach ( $chain_stages as $ci => $stage ) : $my = $chain_my[ $ci ]; ?>
							<g class="sol-chain-node<?php echo 3 === $ci ? ' sol-chain-node--final' : ''; ?>" style="--sol-d:<?php echo esc_attr( (string) ( $ci * 0.35 ) ); ?>s">
								<rect class="sol-chain-rect" x="45" y="<?php echo (int) $my; ?>" width="250" height="84" rx="13" />
								<text class="sol-chain-k" x="170" y="<?php echo (int) ( $my + 26 ); ?>" text-anchor="middle"><?php echo esc_html( $stage['k'] ); ?></text>
								<text class="sol-chain-t" x="170" y="<?php echo (int) ( $my + 50 ); ?>" text-anchor="middle"><?php echo esc_html( $stage['t'] ); ?></text>
								<text class="sol-chain-s" x="170" y="<?php echo (int) ( $my + 70 ); ?>" text-anchor="middle"><?php echo esc_html( $stage['s'] ); ?></text>
							</g>
							<?php if ( $ci < 3 ) : ?>
								<line class="sol-chain-line" pathLength="1" style="--sol-d:<?php echo esc_attr( (string) ( 0.18 + $ci * 0.35 ) ); ?>s"
									x1="170" y1="<?php echo (int) ( $my + 92 ); ?>" x2="170" y2="<?php echo (int) ( $chain_my[ $ci + 1 ] - 8 ); ?>" />
							<?php endif; ?>
						<?php endforeach; ?>
					</svg>
					<figcaption class="sol-sr">
						Das Anfrage-System in vier Stufen: Anzeige, Landingpage, Qualifizierung, CRM.
					</figcaption>
				</figure>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     TRUST STRIP
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-trust" aria-label="Vertrauenssignale" data-track-section="trust_strip">
			<div class="hu-container">
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
		     PROOF-BAR — Case-Study-Kennzahlen früh sichtbar (Teaser → #ergebnisse)
		     ════════════════════════════════════════════════════════════ -->
		<section class="sol-proofbar" aria-label="Belegte Ergebnisse aus dem <?php echo esc_attr( $e3_case_label ); ?>-Case" data-track-section="proof_bar">
			<div class="hu-container" data-sol-reveal>
				<div class="sol-proofbar-inner">
					<p class="sol-proofbar-summary sol-mono">
						<strong>Belegt · <?php echo esc_html( $e3_case_label ); ?></strong>
						<span>Dokumentierter Vorher-/Nachher-Vergleich über <?php echo esc_html( $e3_timeframe ); ?></span>
					</p>
					<a class="sol-proofbar-link sol-mono" href="#ergebnisse"
						data-track-action="cta_solar_proofbar_to_results"
						data-track-category="proof"
						data-track-section="proof_bar"
					>Wie die Zahlen zustande kommen →</a>
				</div>
				<p class="sol-proofbar-context sol-mono">Ein realer Fall über <?php echo esc_html( $e3_timeframe ); ?>, eigener Anfrageweg statt Portal-Leads — dokumentiert, keine pauschale Übertragbarkeitsgarantie.</p>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     STICKY IN-PAGE SECTION NAV
		     Wird nach dem Hero sticky · CRO: schnelle Orientierung,
		     ohne den globalen Header zu ersetzen (SEO/Brand bleibt).
		     ════════════════════════════════════════════════════════════ -->
		<nav class="sol-section-nav" aria-label="Sektionen dieser Seite" data-track-section="section_nav">
			<div class="hu-container">
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
						data-track-action="cta_solar_section_nav_to_intake"
						data-track-category="lead_gen"
						data-track-section="section_nav"
					>Marktcheck →</a>
				</div>
			</div>
		</nav>

		<!-- ════════════════════════════════════════════════════════════
		     01 / STATUS QUO (Creme)
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section hu-section--cream" id="problem" data-track-section="problem">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-section-head">
					<span class="hu-eyebrow">01 / Status quo</span>
					<div>
						<h2>Sie zahlen für Anfragen, die nicht Ihnen gehören.</h2>
						<p class="hu-lead">Und für Vertriebsstunden, die niemand kauft. Drei Gründe, warum der Lead-Einkauf Sie teurer kommt, als er aussieht.</p>
					</div>
				</div>
				<div class="hu-cost-grid">
					<?php foreach ( $cost_cards as $c ) : ?>
						<article class="hu-cost-card">
							<div class="hu-cost-card__big"><?php echo esc_html( $c['big'] ); ?></div>
							<div class="hu-cost-card__sub"><?php echo esc_html( $c['sub'] ); ?></div>
							<p class="hu-cost-card__body"><?php echo esc_html( $c['body'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     02 / MARKT VS. EIGENER WEG (Ink)
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section" id="vergleich" data-track-section="compare">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-section-head">
					<span class="hu-eyebrow">02 / Markt vs. eigener Weg</span>
					<div>
						<h2>Mieten oder besitzen.</h2>
						<p class="hu-lead">Der Ausweg ist nicht ein besseres Portal. Es ist ein Anfrageweg, der Ihnen gehört — und sich messen lässt.</p>
					</div>
				</div>
				<div class="hu-compare">
					<div class="hu-compare__col hu-compare__col--bad">
						<div class="hu-compare__head">
							<span class="hu-compare__icon" aria-hidden="true">✕</span>
							<span>Portal-Miete</span>
						</div>
						<?php foreach ( $compare_bad as $row ) : ?>
							<div class="hu-compare__row">
								<div class="hu-compare__row-t"><?php echo esc_html( $row['t'] ); ?></div>
								<div class="hu-compare__row-d"><?php echo esc_html( $row['s'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="hu-compare__divider" aria-hidden="true">
						<span class="hu-compare__divider-icon">→</span>
					</div>
					<div class="hu-compare__col hu-compare__col--good">
						<div class="hu-compare__head">
							<span class="hu-compare__icon hu-compare__icon--good" aria-hidden="true">✓</span>
							<span>Eigener Anfrageweg</span>
						</div>
						<?php foreach ( $compare_good as $row ) : ?>
							<div class="hu-compare__row">
								<div class="hu-compare__row-t"><?php echo esc_html( $row['t'] ); ?></div>
								<div class="hu-compare__row-d"><?php echo esc_html( $row['s'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     03 / DAS SYSTEM — Asset-Panel + drei Schritte (Creme)
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section hu-section--cream" id="system" data-track-section="method">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-section-head">
					<span class="hu-eyebrow">03 / Das System</span>
					<div>
						<h2>Diagnose. Aufbau. Eigentum.</h2>
						<p class="hu-lead">Drei Schritte, ein Ergebnis: exklusive Anfragen. Jede Komponente ist dokumentiert, gehört Ihnen und wird 1:1 übergeben — inklusive Zugang zu allen Accounts.</p>
					</div>
				</div>
				<div class="hu-process-grid">
					<div class="hu-process-asset">
						<?php foreach ( $process_rows as $row ) : ?>
							<div class="hu-process-asset__row">
								<span class="hu-eyebrow"><?php echo esc_html( $row['e'] ); ?></span>
								<div class="hu-process-asset__row-title">
									<?php if ( ! empty( $row['url'] ) ) : ?>
										<a href="<?php echo esc_url( $row['url'] ); ?>"
											data-track-action="cluster_link_<?php echo esc_attr( sanitize_title( $row['e'] ) ); ?>"
											data-track-category="internal_link"
											data-track-section="method"><?php echo esc_html( $row['t'] ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $row['t'] ); ?>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
						<div class="hu-process-asset__metrics">
							<div class="hu-process-asset__metric">
								<div class="hu-process-asset__metric-num">100 %</div>
								<div class="hu-process-asset__metric-lbl">gehört Ihrem Betrieb</div>
							</div>
							<div class="hu-process-asset__metric">
								<div class="hu-process-asset__metric-num">1:1</div>
								<div class="hu-process-asset__metric-lbl">Übergabe · Accounts</div>
							</div>
						</div>
					</div>
					<div class="hu-steps">
						<?php foreach ( $method_cards as $card ) : ?>
							<div class="hu-step">
								<div class="hu-step__num"><?php echo esc_html( $card['n'] ); ?></div>
								<div class="hu-step__title"><?php echo esc_html( $card['t'] ); ?></div>
								<p class="hu-step__body"><?php echo wp_kses_post( $card['s'] ); ?></p>
								<div class="hu-step__out"><?php echo esc_html( $card['out'] ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     04 / DIE RECHNUNG — CAPEX vs OPEX (Ink)
		     Interaktiver Zeitraum-Picker (12 · 24 · 36 Monate).
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section" id="capex" data-track-section="capex_opex">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-section-head">
					<span class="hu-eyebrow">04 / Die Rechnung</span>
					<div>
						<h2>Eine Bilanz-Entscheidung.</h2>
						<p class="hu-lead">Beide Wege kosten Geld. Nur einer hinterlässt ein Asset, das Ihnen gehört — CAPEX statt OPEX.</p>
					</div>
				</div>

				<div class="sol-capex-picker" role="tablist" aria-label="Zeitraum auswählen">
					<span class="sol-capex-picker-label sol-mono">Zeitraum</span>
					<div class="sol-capex-picker-buttons" data-sol-capex-buttons>
						<?php foreach ( [ 12, 24, 36 ] as $tf ) : ?>
							<button type="button"
								class="sol-capex-picker-btn<?php echo esc_attr( $tf === $capex_default ? ' is-active' : '' ); ?>"
								data-sol-capex-tf="<?php echo esc_attr( (string) $tf ); ?>"
								role="tab"
								aria-selected="<?php echo esc_attr( $tf === $capex_default ? 'true' : 'false' ); ?>"
								data-track-action="capex_timeframe"
								data-track-category="engagement"
								data-track-section="capex_opex"
							><?php echo esc_html( (string) $tf ); ?> Monate</button>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="hu-models" data-sol-capex>
					<article class="hu-model hu-model--a">
						<div class="hu-model__label">Modell A · OPEX</div>
						<div class="hu-model__title">Portal-Leads mieten</div>
						<ul class="hu-model__list">
							<li><span class="hu-model__bullet" aria-hidden="true">✕</span><span>Lead-Kosten Ø 80 €/Stück — <span data-sol-capex-out="portal_monthly"><?php echo esc_html( $capex_now['portal_monthly'] ); ?> / Monat</span></span></li>
							<li><span class="hu-model__bullet" aria-hidden="true">✕</span><span><span data-sol-capex-out="portal_leads"><?php echo esc_html( $capex_now['portal_leads'] ); ?> Stück</span> erwartet — rund 50 % gehen nicht ans Telefon</span></li>
							<li><span class="hu-model__bullet" aria-hidden="true">✕</span><span>Drei Wettbewerber parallel auf jeder Anfrage</span></li>
							<li><span class="hu-model__bullet" aria-hidden="true">✕</span><span>Portal behält die Daten · Preiserhöhung jederzeit</span></li>
						</ul>
						<div class="hu-model__foot">Gesamt über <span data-sol-capex-out="tf"><?php echo esc_html( (string) $capex_default ); ?></span> Monate: <span data-sol-capex-out="portal_total"><?php echo esc_html( $capex_now['portal_total'] ); ?></span></div>
						<span class="hu-model__pill">0 € Asset am Ende — Budget aus, Anfragen aus</span>
					</article>
					<article class="hu-model hu-model--b">
						<div class="hu-model__label">Modell B · CAPEX</div>
						<div class="hu-model__title">Eigenes System besitzen</div>
						<ul class="hu-model__list">
							<li><span class="hu-model__bullet" aria-hidden="true">✓</span><span>Setup einmalig <span data-sol-capex-out="own_setup"><?php echo esc_html( $capex_now['own_setup'] ); ?></span> · Hosting <span data-sol-capex-out="own_monthly"><?php echo esc_html( $capex_now['own_monthly'] ); ?> / Monat</span></span></li>
							<li><span class="hu-model__bullet" aria-hidden="true">✓</span><span>Exklusive Anfragen — nur für Ihren Vertrieb</span></li>
							<li><span class="hu-model__bullet" aria-hidden="true">✓</span><span>Vorqualifizierung: Region · Projektwert · Fit-Score</span></li>
							<li><span class="hu-model__bullet" aria-hidden="true">✓</span><span>Datenhoheit 100 % bei Ihnen · skaliert ohne Kostenexplosion</span></li>
						</ul>
						<div class="hu-model__foot">Gesamt über <span data-sol-capex-out="tf2"><?php echo esc_html( (string) $capex_default ); ?></span> Monate: <span data-sol-capex-out="own_total"><?php echo esc_html( $capex_now['own_total'] ); ?></span></div>
						<span class="hu-model__pill">Aktiviertes Asset — bleibt, auch ohne laufende Kosten</span>
					</article>
				</div>

				<?php
				// ── Portal-Kosten-Rechner ──────────────────────────────
				// Zwei Slider, Ausgabe an den bestehenden Zeitraum-Picker
				// gekoppelt (kein zweiter Zeitraum-Zustand). SSR rendert
				// die Startwerte fertig aus — ohne JS steht hier eine
				// korrekte, statische Beispielrechnung.
				?>
				<div class="sol-calc" data-sol-calc
					data-setup-low="<?php echo (int) $calc_setup_low; ?>"
					data-setup-high="<?php echo (int) $calc_setup_high; ?>"
					data-hosting="<?php echo (int) $calc_hosting; ?>">
					<div class="sol-calc-head">
						<span class="hu-eyebrow">Mit Ihren Zahlen</span>
						<h3 class="sol-calc-h">Was kostet Sie der Portal-Weg?</h3>
						<p class="sol-calc-sub">
							Die Modelle oben rechnen mit Marktdurchschnitt. Tragen Sie Ihre eigenen
							Werte ein — die Gegenüberstellung rechnet sofort mit dem Zeitraum, den
							Sie oben gewählt haben.
						</p>
					</div>

					<div class="sol-calc-controls">
						<div class="sol-calc-field">
							<div class="sol-calc-lbl sol-mono">
								<label for="sol-calc-leads">Gekaufte Anfragen pro Monat</label>
								<output class="sol-calc-val" for="sol-calc-leads" data-sol-calc-out="leads"><?php echo (int) $calc_leads; ?></output>
							</div>
							<input class="sol-calc-range" type="range" id="sol-calc-leads"
								name="sol-calc-leads" min="2" max="60" step="1"
								value="<?php echo (int) $calc_leads; ?>"
								data-sol-calc-input="leads"
								aria-describedby="sol-calc-result"
								data-track-action="capex_calc_leads"
								data-track-category="engagement"
								data-track-section="capex_opex" />
						</div>
						<div class="sol-calc-field">
							<div class="sol-calc-lbl sol-mono">
								<label for="sol-calc-price">Preis pro Portal-Anfrage</label>
								<output class="sol-calc-val" for="sol-calc-price" data-sol-calc-out="price"><?php echo (int) $calc_price; ?> €</output>
							</div>
							<input class="sol-calc-range" type="range" id="sol-calc-price"
								name="sol-calc-price" min="40" max="200" step="5"
								value="<?php echo (int) $calc_price; ?>"
								data-sol-calc-input="price"
								aria-describedby="sol-calc-result"
								data-track-action="capex_calc_price"
								data-track-category="engagement"
								data-track-section="capex_opex" />
						</div>
					</div>

					<div class="sol-calc-result" id="sol-calc-result" aria-live="polite">
						<div class="sol-calc-row">
							<span class="sol-calc-row-lbl">Portal-Anfragen über <span data-sol-capex-out="tf5"><?php echo (int) $capex_default; ?></span> Monate</span>
							<span class="sol-calc-row-num" data-sol-calc-out="portal"><?php echo esc_html( $calc_eur( $calc_portal_total ) ); ?></span>
						</div>
						<div class="sol-calc-row">
							<span class="sol-calc-row-lbl">Eigenes System im selben Zeitraum</span>
							<span class="sol-calc-row-num" data-sol-calc-out="own"><?php echo esc_html( $calc_eur( $calc_own_low ) . ' – ' . $calc_eur( $calc_own_high ) ); ?></span>
						</div>
						<p class="sol-calc-verdict" data-sol-calc-out="verdict">
							<?php
							echo esc_html(
								sprintf(
									'Differenz: %s bis %s — und am Ende gehört Ihnen das System.',
									$calc_eur( $calc_diff_low ),
									$calc_eur( $calc_diff_high )
								)
							);
							?>
						</p>
					</div>

					<p class="sol-calc-fineprint sol-mono">
						Rechenbeispiel mit Ihren Eingaben — keine Prognose und keine zugesagte
						Ersparnis. Eigenes System: <?php echo esc_html( $calc_eur( $calc_setup_low ) . ' – ' . $calc_eur( $calc_setup_high ) ); ?>
						einmalig plus <?php echo esc_html( $calc_eur( $calc_hosting ) ); ?> Hosting im Monat.
					</p>
				</div>

				<aside class="sol-capex-summary">
					<h3 class="sol-capex-summary-h">Bilanziell: CAPEX statt OPEX</h3>
					<p>
						Ein eigenes Anfrage-System ist eine <strong>aktivierbare Investition</strong> (CAPEX), kein wiederkehrender Kostenfaktor (OPEX). Portal-Leads sind Betriebsausgaben ohne Restwert.
						Nach <span data-sol-capex-out="tf3"><?php echo esc_html( (string) $capex_default ); ?></span> Monaten haben Sie entweder
						<strong><span data-sol-capex-out="portal_total2"><?php echo esc_html( $capex_now['portal_total'] ); ?></span> ausgegeben und besitzen nichts</strong> —
						oder Sie haben <strong><span data-sol-capex-out="own_total2"><?php echo esc_html( $capex_now['own_total'] ); ?></span> investiert und ein skalierbares Asset</strong>
						(Setup einmalig + <span data-sol-capex-out="tf4"><?php echo esc_html( (string) $capex_default ); ?></span> × Hosting/Wartung).
					</p>
				</aside>

				<div class="sol-section-cta">
					<div class="sol-section-cta-actions">
						<a class="hu-btn hu-btn-primary" href="#marktcheck"
							data-track-action="cta_solar_capex_to_intake"
							data-track-category="lead_gen"
							data-track-section="capex_opex"
							data-track-funnel-stage="intake_open"
						>
							<span>Diese Rechnung für meinen Betrieb prüfen</span>
							<?php echo $arrow_svg; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<a class="hu-btn hu-btn-ghost" href="#einstieg"
							data-track-action="cta_solar_capex_to_entry"
							data-track-category="lead_gen"
							data-track-section="capex_opex"
						>
							<span>Noch zu groß? Einstieg ab <?php echo esc_html( $entry_price ); ?></span>
						</a>
					</div>
					<div class="sol-section-cta-micro sol-mono">Manuelle Erst-Analyse · Fit-Entscheid mit drei Hebeln · Befund in 48 h</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     05 / KLEINER EINSTIEG — Sofortkontakt-Setup (Creme)
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section hu-section--cream sol-entry" id="einstieg" aria-labelledby="sol-entry-title" data-track-section="entry_offer">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-section-head sol-entry-head">
					<span class="hu-eyebrow">05 / Kleiner Einstieg</span>
					<div>
						<h2 id="sol-entry-title">Wer zuerst zurückruft, bekommt den Termin.</h2>
						<p class="hu-lead">Nicht jeder Betrieb braucht sofort ein ganzes System. Der schnellste Hebel liegt meistens vor der Anfrage-Menge: bei der Zeit zwischen Anfrage und erstem Kontakt. Eine Anfrage, die vier Stunden liegen bleibt, ist praktisch verloren — bei geteilten Portal-Leads sowieso, weil drei Betriebe dieselbe Anfrage haben.</p>
					</div>
				</div>

				<div class="sol-entry-panel">
					<p class="sol-entry-price">
						<strong>Sofortkontakt-Setup — <?php echo esc_html( $entry_price_net ); ?></strong>
						<span>Fertig eingerichtet in fünf Werktagen. Kein Werbebudget nötig, keine Mindestlaufzeit, kein CRM-Wechsel.</span>
					</p>

					<h3>Was eingerichtet wird</h3>
					<ul class="sol-entry-list">
						<li><strong>Sofort-Alarm bei jeder neuen Anfrage.</strong> In unter 60 Sekunden per SMS oder WhatsApp an den zuständigen Vertriebler — mit Ort, Projektart und Uhrzeit direkt in der Nachricht. Nicht als Mail, die im Postfach untergeht.</li>
						<li><strong>Automatische Eingangsbestätigung</strong> an den Interessenten, mit direktem Terminbuchungslink. Ihr Betrieb ist der Erste, der zurückmeldet — auch abends, auch am Wochenende.</li>
						<li><strong>Alle Anfragen an einem Ort, nach Quelle getrennt.</strong> Portal, Website, Google, Empfehlung — in einer Übersicht, die Sie morgens aufmachen und sofort verstehen.</li>
					</ul>

					<p><strong>Auch Ihre gekauften Leads laufen mit durch.</strong> Aroundhome, DAA und Wattfox liefern per E-Mail — die werden mit ausgelesen und genauso schnell weitergereicht. Sie holen ab dem ersten Tag mehr aus dem Budget, das Sie ohnehin schon ausgeben.</p>
					<p>Nach rund 60 Tagen wissen Sie mit Ihren eigenen Zahlen, was ein gekaufter Lead Sie pro gewonnenem Auftrag wirklich kostet. Ob danach ein eigenes System sinnvoll ist, entscheiden Sie auf dieser Grundlage — nicht auf meiner Behauptung.</p>
					<p class="sol-entry-qualifier"><em>Nicht sinnvoll, wenn bei Ihnen aktuell kaum Anfragen eingehen. Dann gibt es nichts zu beschleunigen, und wir reden über die Quelle statt über die Reaktion.</em></p>

					<div class="sol-section-cta">
						<a class="hu-btn hu-btn-primary" href="#marktcheck"
							data-track-action="cta_solar_entry_to_intake"
							data-track-category="lead_gen"
							data-track-section="entry_offer"
							data-track-funnel-stage="intake_open"
						>
							<span>Sofortkontakt-Setup anfragen</span>
							<?php echo $arrow_svg; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     06 / SOLAR CASE STUDY — Vorher/Nachher + Stats (Ink)
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section" id="ergebnisse" data-track-section="results">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-proof-headline">
					<span class="hu-eyebrow">06 / <?php echo esc_html( $e3_case_label ); ?></span>
					<h2>Vorher. Nachher. Beziffert.</h2>
					<p><?php echo esc_html( $e3_timeframe ); ?> eigener Anfrageweg — Methodik-Snapshot statt Referenz-Logo-Wand.</p>
				</div>
				<div class="hu-proof-cards">
					<div class="hu-proof-card">
						<div class="hu-proof-card__lbl">Vorher · Portal-Miete</div>
						<div class="hu-proof-card__title">Geteilte Leads, steigende Preise</div>
						<ul class="hu-proof-list">
							<li><span class="x" aria-hidden="true">✕</span><?php echo esc_html( $e3_cpl_before ); ?> Kosten pro Anfrage zu Beginn</li>
							<li><span class="x" aria-hidden="true">✕</span>Abschlussquote <?php echo esc_html( $e3_conv_before ); ?> auf Portal-Leads</li>
							<li><span class="x" aria-hidden="true">✕</span>Keine Sicht auf Region, Dach, Projektwert</li>
						</ul>
					</div>
					<div class="hu-proof-arrow" aria-hidden="true">→</div>
					<div class="hu-proof-card hu-proof-card--after">
						<div class="hu-proof-card__lbl">Nachher · Eigener Anfrageweg</div>
						<div class="hu-proof-card__title">Exklusive, vorqualifizierte Anfragen</div>
						<ul class="hu-proof-list">
							<li><span class="v" aria-hidden="true">✓</span><?php echo esc_html( $e3_cpl_after ); ?> Kosten pro Anfrage nach <?php echo esc_html( $e3_timeframe_dative ); ?></li>
							<li><span class="v" aria-hidden="true">✓</span><?php echo esc_html( $e3_sales_conversion ); ?> Abschlussquote — Anfrage zu Vertrag</li>
							<li><span class="v" aria-hidden="true">✓</span>Fit-Signal vor dem ersten Anruf</li>
						</ul>
					</div>
				</div>
				<div class="hu-proof-stats">
					<div class="hu-proof-stat">
						<div class="hu-proof-stat__num" data-sol-countup><?php echo esc_html( $e3_lead_count ); ?></div>
						<div class="hu-proof-stat__lbl">Qualifizierte Anfragen</div>
					</div>
					<div class="hu-proof-stat">
						<div class="hu-proof-stat__num"><?php echo esc_html( $e3_conv_uplift ); ?></div>
						<div class="hu-proof-stat__lbl">Abschlussquote · vorher → nachher</div>
					</div>
					<div class="hu-proof-stat">
						<div class="hu-proof-stat__num" style="color:var(--accent);" data-sol-countup><?php echo esc_html( $e3_cpl_reduction ); ?></div>
						<div class="hu-proof-stat__lbl">Weniger Kosten pro Anfrage</div>
					</div>
					<div class="hu-proof-stat">
						<div class="hu-proof-stat__num"><?php echo esc_html( $e3_timeframe ); ?></div>
						<div class="hu-proof-stat__lbl">Zeitraum · dokumentiert</div>
					</div>
				</div>
				<div class="sol-proof-foot">
					<p>
						Schritt 2 nach dem Marktcheck: die verrechenbare <strong>Tiefendiagnose</strong> —
						vier Bausteine, schriftlicher Befund in 7 Werktagen, drei priorisierte Hebel,
						eine Wirtschaftlichkeits-Einordnung. Klarheit, keine Folien.
					</p>
					<a
						class="hu-btn hu-btn-ghost"
						href="<?php echo esc_url( $e3_url ); ?>"
						data-track-action="cta_solar_to_e3_case"
						data-track-category="proof"
						data-track-section="results"
					>
						Vollständige Methodik in der Fallstudie
						<?php echo $arrow_svg; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     07 / WANN ES PASST — ehrliche Vorauswahl (Creme)
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section hu-section--cream" id="fit" data-track-section="fit_check">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-section-head">
					<span class="hu-eyebrow">07 / Wann es passt</span>
					<div>
						<h2>Lieber jetzt klären, ob es passt.</h2>
						<p class="hu-lead">Als später ein Setup zu bauen, das ins Leere läuft. Ehrliche Vorauswahl, bevor wir reden.</p>
					</div>
				</div>
				<div class="hu-fit-grid">
					<article class="hu-fit-col hu-fit-col--yes">
						<div class="hu-fit-col__head">
							<span class="hu-fit-col__badge hu-fit-col__badge--yes" aria-hidden="true">✓</span>
							<span>Passt, wenn …</span>
						</div>
						<ul class="hu-fit-list">
							<?php foreach ( $fit_yes as $row ) : ?>
								<li>
									<div class="hu-fit-list__t"><?php echo esc_html( $row['t'] ); ?></div>
									<div class="hu-fit-list__d"><?php echo esc_html( $row['s'] ); ?></div>
								</li>
							<?php endforeach; ?>
						</ul>
					</article>
					<article class="hu-fit-col hu-fit-col--no">
						<div class="hu-fit-col__head">
							<span class="hu-fit-col__badge hu-fit-col__badge--no" aria-hidden="true">✕</span>
							<span>Passt nicht, wenn …</span>
						</div>
						<ul class="hu-fit-list">
							<?php foreach ( $fit_no as $row ) : ?>
								<li>
									<div class="hu-fit-list__t"><?php echo esc_html( $row['t'] ); ?></div>
									<div class="hu-fit-list__d"><?php echo esc_html( $row['s'] ); ?></div>
								</li>
							<?php endforeach; ?>
						</ul>
					</article>
				</div>
				<div class="sol-section-cta">
					<div class="sol-section-cta-actions">
						<a class="hu-btn hu-btn-primary" href="#marktcheck"
							data-track-action="cta_solar_fit_to_intake"
							data-track-category="lead_gen"
							data-track-section="fit_check"
							data-track-funnel-stage="intake_open"
						>
							<span>Marktcheck beantragen</span>
							<?php echo $arrow_svg; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<a class="hu-btn hu-btn-ghost" href="#ergebnisse"
							data-track-action="cta_solar_fit_to_results"
							data-track-category="proof"
							data-track-section="fit_check"
						>
							Erst die Fallstudie ansehen
							<?php echo $arrow_svg; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</div>
					<div class="sol-section-cta-micro sol-mono">Manuelle Erst-Analyse · Fit-Entscheid mit drei Hebeln · Befund in 48 h</div>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     08 / RISIKO-UMKEHR (Ink)
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section" id="garantie" data-track-section="guarantee">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-section-head">
					<span class="hu-eyebrow">08 / Risiko-Umkehr</span>
					<div>
						<h2>Diagnose vor Pitch.</h2>
						<p class="hu-lead">Der Marktcheck ist kein Verkaufsritual — er ist eine ehrliche Ersteinschätzung. Auch dann, wenn sie heißt: nicht mit mir.</p>
					</div>
				</div>
				<div class="sol-risk-grid">
					<?php foreach ( $guarantee_points as $p ) : ?>
						<div class="sol-risk-card">
							<span class="hu-eyebrow"><?php echo esc_html( $p['e'] ); ?></span>
							<div class="sol-risk-card-t"><?php echo esc_html( $p['t'] ); ?></div>
							<p class="sol-risk-card-d"><?php echo esc_html( $p['s'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     09 / VERTIEFUNG — Themen-Hub für SEO-Sub-Pages (Ink)
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section" id="deeper" data-track-section="deeper">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-section-head">
					<span class="hu-eyebrow">09 / Vertiefung</span>
					<div>
						<h2>Wenn Sie tiefer einsteigen wollen.</h2>
						<p class="hu-lead">Acht Vertiefungs-Seiten zu Strategie, Lead-Qualität, Funnel-Architektur und Markteinordnung — jede kann unabhängig gelesen werden, alle führen zurück zum Marktcheck.</p>
					</div>
				</div>
				<div class="hu-deeper-clusters">
					<?php foreach ( $deeper_clusters as $cluster ) : ?>
						<div class="hu-deeper-cluster">
							<h3 class="hu-deeper-cluster__h"><?php echo esc_html( $cluster['group'] ); ?></h3>
							<ul class="hu-deeper-list">
								<?php foreach ( $cluster['items'] as $item ) : ?>
									<li class="hu-deeper-item">
										<a class="hu-deeper-link"
										   href="<?php echo esc_url( $item['url'] ); ?>"
										   data-track-action="deeper_cluster_link"
										   data-track-category="solar_money_page"
										   data-track-section="deeper">
											<span class="hu-deeper-link__t"><?php echo esc_html( $item['t'] ); ?></span>
											<span class="hu-deeper-link__s"><?php echo esc_html( $item['s'] ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ════════════════════════════════════════════════════════════
		     10 / FAQ (Ink)
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section" id="faq" data-track-section="faq">
			<div class="hu-container sol-faq-inner" data-sol-reveal>
				<div class="sol-faq-left">
					<span class="hu-eyebrow">10 / FAQ</span>
					<h2 class="sol-faq-h">Bevor Sie fragen.</h2>
					<p class="sol-faq-sub">
						Was hier nicht beantwortet wird, klären wir im Marktcheck — strukturiert, schriftlich, ohne Verkaufsgespräch.
					</p>
				</div>
				<ul class="sol-faq-list">
					<?php foreach ( $faq_items as $i => $item ) : ?>
						<li class="sol-faq-item<?php echo 0 === $i ? ' is-open' : ''; ?>">
							<button type="button" class="sol-faq-q" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>"
								data-track-action="faq_toggle"
								data-track-category="engagement"
								data-track-section="faq"
								data-faq-index="<?php echo esc_attr( (string) ( $i + 1 ) ); ?>"
							>
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
		     FINAL CTA — zurück zum Marktcheck
		     ════════════════════════════════════════════════════════════ -->
		<section class="hu-section" id="final-cta" data-track-section="final_cta">
			<div class="hu-container" data-sol-reveal>
				<div class="hu-final-cta">
					<span class="hu-tag">
						<span class="hu-dot hu-dot--live" aria-hidden="true"></span>
						<span class="hu-mono">Marktcheck · Fit-Befund in 48 h</span>
					</span>
					<h2>Anfragen besitzen,<br />nicht mieten.</h2>
					<p>
						Ich arbeite 1:1 mit Solar- und SHK-Betrieben, damit jede Region in Diagnose, Daten-Pipeline und
						Vertriebsanschluss persönlich abgebildet werden kann. Der Marktcheck entscheidet, ob die Architektur
						zu Ihrem Betrieb passt — und sagt auch ab, wenn sie es nicht tut.
					</p>
					<ul class="sol-cta-facts">
						<li>Einstieg: Marktcheck · Fit-Befund statt Verkaufsgespräch</li>
						<li>Entscheidung: nach Marktcheck · händisch · innerhalb von 48 Stunden</li>
						<li>Bedingung: eigener Vertrieb · klares Zielgebiet · 12–24-Monate-Horizont</li>
					</ul>
					<a
						class="hu-btn hu-btn-primary"
						href="#marktcheck"
						data-track-action="cta_solar_final_to_intake"
						data-track-category="lead_gen"
						data-track-section="final_cta"
						data-track-funnel-stage="intake_open"
					>
						<span>Marktcheck starten</span>
						<?php echo $arrow_svg; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<div class="hu-final-cta__signature">
						Befund in 48 h · <strong>kein Pflicht-Call</strong> · keine Zahlungsdaten · Einordnung des Marktumfelds anhand der Firmen-PLZ inklusive
					</div>
				</div>
			</div>
		</section>

		<!-- Sticky Mobile-CTA · Erscheint ab 20 % Scrolltiefe -->
		<a
			class="sol-sticky-cta"
			href="#marktcheck"
			data-track-action="cta_solar_sticky_to_intake"
			data-track-category="lead_gen"
			data-track-section="sticky_mobile"
			data-track-funnel-stage="intake_open"
		>
			<span>Marktcheck starten</span>
			<span class="sol-sticky-cta-arrow" aria-hidden="true"><?php echo $arrow_svg; // raw-ok phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</a>

	</div><!-- /.solara-landing -->
</main>

<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>

<?php get_footer(); ?>
