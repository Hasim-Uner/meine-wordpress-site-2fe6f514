<?php
/**
 * Template Name: Solar & Wärmepumpen Leadgenerierung (Anfragestrecke)
 * Description: Gutachten-Standard. Weisser Grund, Hausschrift im Fliesstext,
 *              Randspalte mit laufender Nummer, dunkle Tafeln fuer
 *              Beweis und Handlung. Der Marktcheck ist das Gate und steht
 *              bewusst nach der Argumentation, nicht davor.
 *
 *              Aufbau: Dokumentkopf mit Definition und Messschrieb ·
 *              01 Strecke · 02 Rechnung · 03 Fall · 04 Einstieg ·
 *              05 Was es braucht · 06 Marktcheck · 07 Fragen · 08 Verweise.
 *
 * Waehrungs-Doktrin: ausnahmslos EUR. Niemals $ oder generische Symbole.
 *
 * Zahlen-Doktrin: gemessene Werte des eigenen Falls kommen aus
 * canon/e3-proof-canon.php, Preise aus canon/pricing-canon.php, fremde
 * Marktzahlen aus canon/market-canon.php. Kein Literal im Template.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url     = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
$page_url     = trailingslashit( explode( '#', (string) $page_url )[0] );
$e3_url       = home_url( '/case-study-solar-leadgenerierung/' );
$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
$tracking_url = $primary_urls['tracking'] ?? home_url( '/ga4-tracking-setup/' );
$about_url    = $primary_urls['about'] ?? home_url( '/hasim-uener/' );

// ── Eigene, gemessene Werte (E3-Canon) ─────────────────────────
$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_metrics    = isset( $e3_canon['metrics'] ) && is_array( $e3_canon['metrics'] ) ? $e3_canon['metrics'] : [];
$e3_case_label = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';

$e3_lead_count    = $e3_metrics['lead_count']['display'] ?? '1.750';
$e3_sales_conv    = $e3_metrics['sales_conversion']['display'] ?? '15 %';
$e3_cpl_reduction = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_cpl_before    = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after     = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe     = $e3_metrics['timeframe']['display'] ?? '6 Monate';
$e3_timeline      = $e3_canon['timeline'];

// Voreinstellungen des Vergleichsrechners: Kosten pro Anfrage vorsichtiger als
// der Fall, Abschlussquote wie im Fall. Beides steht im Canon als eigene
// Kennzahl; als nacktes Literal im Template war die angesetzte Quote von der
// gemessenen Abschlussquote des Falls nicht zu unterscheiden.
$calc_cpl_display   = hu_e3_metric( 'calc_cpl_conservative' );
$calc_cpl_input     = hu_e3_metric( 'calc_cpl_conservative', 'input' );
$calc_quote_display = hu_e3_metric( 'calc_sales_conversion' );
$calc_quote_input   = hu_e3_metric( 'calc_sales_conversion', 'input' );
$calc_defaults = [
	'a1' => 25,
	'a2' => 80,
	'a3' => 4,
	'b1' => 2000,
	'b2' => (float) $calc_cpl_input,
	'b3' => (float) $calc_quote_input,
];

// Die Vorher-Quote ist eine Marktannahme, keine Messung dieses Betriebs.
// Deshalb die vorsichtige Fassung, sobald sie in einer Tabelle neben
// gemessenen Werten steht.
$e3_conv_before = $e3_metrics['sales_conversion_before']['display_hedged'] ?? 'einstellig';

// Zahlenwerte fuer den Messschrieb.
$e3_cpl_before_val = (int) ( $e3_metrics['cpl_before']['value'] ?? 150 );
$e3_cpl_after_val  = (int) ( $e3_metrics['cpl_after']['value'] ?? 22 );

// ── Preise (Pricing-Canon) ─────────────────────────────────────
// Erst die Rohwerte, dann die Anzeigeform daraus. Ein Fallback-Literal
// Ein ausgeschriebener Aufbaupreis waere eine zweite Preisquelle im Template —
// genau das verhindert der Preis-Kanon.
$pricing_canon = function_exists( 'hu_pricing_canon' ) ? hu_pricing_canon() : [];

// Rohwerte gehen zusaetzlich als data-Attribute ins Markup, damit das
// JavaScript den Aufbaupreis nicht ein zweites Mal kennen muss.
$calc_build   = (int) ( $pricing_canon['foundation_price_standard'] ?? 9999 );
$calc_extra_product = (int) ( $pricing_canon['foundation_extra_product_price'] ?? 1000 );
$marketcheck_price_value = (int) ( $pricing_canon['marketcheck_price'] ?? 99 );
$calc_hosting = (int) ( $pricing_canon['foundation_hosting_monthly'] ?? 50 );
$calc_months  = 24;
$module_a_orders = $calc_defaults['a1'] * $calc_defaults['a3'] / 100;
$module_b_leads  = $calc_defaults['b1'] / $calc_defaults['b2'];
$module_b_orders = $module_b_leads * $calc_defaults['b3'] / 100;
$module_a_cpo    = $calc_defaults['a1'] * $calc_defaults['a2'] / max( 0.0001, $module_a_orders );
$module_b_cpo    = $module_b_orders > 0 ? ( $calc_defaults['b1'] + $calc_build / $calc_months + $calc_hosting ) / $module_b_orders : 0;

$format_eur = static function ( $value ) {
	return function_exists( 'hu_format_eur' )
		? hu_format_eur( $value )
		: number_format( (float) $value, 0, ',', '.' ) . ' €';
};
$format_orders = static function ( float $orders ): string {
	$formatted = number_format( $orders, 1, ',', '.' );
	return '1,0' === $formatted ? 'ein Auftrag' : $formatted . ' Aufträge';
};
$format_numeric_orders = static function ( float $orders ): string {
	$formatted = number_format( $orders, 1, ',', '.' );
	return $formatted . ( '1,0' === $formatted ? ' Auftrag' : ' Aufträge' );
};

$foundation_price   = $format_eur( $calc_build );
$extra_product_price = $format_eur( $calc_extra_product );
$marketcheck_price   = $format_eur( $marketcheck_price_value );
$hosting_price       = $format_eur( $calc_hosting );
$setup_price         = $format_eur( (int) ( $pricing_canon['entry_setup_price'] ?? 790 ) );

// ── Marktcheck (Diagnose-Canon) ────────────────────────────────
$diagnose_canon    = function_exists( 'hu_diagnose_canon' ) ? hu_diagnose_canon() : [];
$marketcheck_reply = hu_marketcheck_reply_label();
$marketcheck_fit_q        = (int) ( $diagnose_canon['marketcheck_fit_questions'] ?? 4 );
$marketcheck_mins         = (int) ( $diagnose_canon['marketcheck_minutes'] ?? 2 );

// ── Kontakt (Messaging-Canon) ──────────────────────────────────
$contact_email = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$contact_url = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );
$marketcheck_contact_url = add_query_arg( [ 'type' => 'audit', 'focus' => 'audit_scope' ], $contact_url );
$system_contact_url      = add_query_arg( [ 'type' => 'project', 'focus' => 'energy_system', 'produkte' => 'photovoltaik' ], $contact_url );
$sofort_contact_url      = add_query_arg( [ 'type' => 'implementation', 'focus' => 'response_setup' ], $contact_url );

// ── Fremde Marktzahlen (Market-Canon) ──────────────────────────
$market_figures    = function_exists( 'hu_market_figures' ) ? hu_market_figures() : [];
$market_disclaimer = function_exists( 'hu_market_figures_disclaimer' ) ? hu_market_figures_disclaimer() : '';

// ── Kapitel ────────────────────────────────────────────────────
// Nummer, Titel und Anker an einer Stelle. Die Leiste oben, die
// Randspalten-Marken und die Sprungziele lesen alle hieraus.
//
// Drei Anker sind aelter als dieses Template und werden von anderen
// Seiten verlinkt: #marktcheck, #ergebnisse und #einstieg. Sie bleiben
// die Abschnitts-IDs. Die neuen Namen (fall, leiter) haengen zusaetzlich
// an der jeweiligen H2.
$chapters = [
	[ 'nr' => '01', 'id' => 'strecke',    'titel' => 'Der Mechanismus',      'kurz' => 'Mechanismus' ],
	[ 'nr' => '02', 'id' => 'rechnung',   'titel' => 'Wirtschaftlichkeit',   'kurz' => 'Rechnung' ],
	[ 'nr' => '03', 'id' => 'ergebnisse', 'titel' => 'Der Fall',             'kurz' => 'Fall' ],
	[ 'nr' => '04', 'id' => 'einstieg',   'titel' => 'Produkte & Preis',    'kurz' => 'Produkte' ],
	[ 'nr' => '05', 'id' => 'anteil',     'titel' => 'Passung',              'kurz' => 'Passung' ],
	[ 'nr' => '06', 'id' => 'marktcheck', 'titel' => 'Marktcheck',           'kurz' => 'Marktcheck' ],
	[ 'nr' => '07', 'id' => 'fragen',     'titel' => 'Entscheidungsfragen',  'kurz' => 'Fragen' ],
	[ 'nr' => '08', 'id' => 'verweise',   'titel' => 'Vertiefung',           'kurz' => 'Vertiefung' ],
];

$chapter_by_id = [];
foreach ( $chapters as $chapter ) {
	$chapter_by_id[ $chapter['id'] ] = $chapter;
}

/**
 * Render the chapter mark in the left margin column.
 *
 * @param array<string, string> $chapter Chapter record.
 * @return void
 */
$render_chapter = static function ( $chapter ) {
	?>
	<div class="spalte-links">
		<div class="kapitel">
			<span class="nr" aria-hidden="true"><?php echo esc_html( $chapter['nr'] ); ?></span>
			<span class="titel" aria-hidden="true"><?php echo esc_html( $chapter['titel'] ); ?></span>
			<span class="strich" aria-hidden="true"></span>
		</div>
	</div>
	<?php
};

// Die Zwischenstufen zeigen den Ort des Verlusts, nicht eine Prognose.
$module_counts = [
	'a' => [ (int) $calc_defaults['a1'], 12, 6, 3, (int) round( $module_a_orders ) ],
	'b' => [ (int) round( $module_b_leads ), 30, 22, 12, (int) round( $module_b_orders ) ],
];
$module_stations = [
	[ 'name' => 'Anfrage entsteht', 'built' => 'Landingpages und Kampagnen auf Ihrer Domain, ausgerichtet auf Ihr Zielgebiet.', 'a' => 'Derselbe Kontakt geht an 3 bis 5 Betriebe. Wer als Vierter anruft, verkauft über den Preis oder gar nicht.', 'b' => 'Die Anfrage kommt über Ihre Seite und gehört nur Ihnen. Kein Mitbewerber hat dieselbe Telefonnummer.' ],
	[ 'name' => 'Einordnung', 'built' => 'Formular mit Vorqualifizierung: Produkt, Objekt, Standort, Zeithorizont.', 'a' => 'Was der Kontakt eigentlich will, klärt Ihr Vertrieb am Telefon. Unpassende Anfragen kosten erst Geld, dann Zeit.', 'b' => 'Unpassende fallen im Formular heraus, bevor jemand anruft. Ihr Vertrieb sieht vor dem Gespräch, worum es geht.' ],
	[ 'name' => 'Erster Anruf', 'built' => 'CRM-Übergabe mit Alarm unter 60 Sekunden und automatischer Eingangsbestätigung mit Terminlink.', 'a' => 'Der Anruf kommt, wenn jemand Zeit hat. Bis dahin hat der Interessent schon mit zwei anderen gesprochen.', 'b' => 'Die zuständige Person bekommt die Anfrage sofort. Der Interessent bekommt in derselben Minute eine Bestätigung.' ],
	[ 'name' => 'Nachfassen', 'built' => 'Bearbeitungsstand im CRM und Rückmeldung an die Kampagnen über serverseitiges Tracking.', 'a' => 'Der Anbieter erfährt nie, welche Kontakte verkauft haben. Nächsten Monat kaufen Sie dieselbe Qualität.', 'b' => 'Abschlüsse fließen zurück. Budget wandert zu den Quellen, die Aufträge bringen, nicht nur Formulare.' ],
	[ 'name' => 'Auftrag', 'built' => 'Dokumentation und Übergabe. Code, Konten und Daten liegen bei Ihnen.', 'a' => '', 'b' => '' ],
];
$module_stations[4]['a'] = sprintf( 'Rund %s im Monat, %s je Auftrag. Nächsten Monat beginnt alles von vorn.', $format_orders( $module_a_orders ), $format_eur( $module_a_cpo ) );
$module_stations[4]['b'] = sprintf( 'Rund %s im Monat, %s je Auftrag. Das System bleibt und wird mit jedem Monat genauer.', $format_orders( $module_b_orders ), $format_eur( $module_b_cpo ) );

// ── 05 Was es braucht ──────────────────────────────────────────
$conditions = [
	[
		'id'    => 'anteil-zugaenge',
		'titel' => 'Zugänge',
		'text'  => 'Domain, Hosting, Werbekonten und CRM auf Ihren Namen. Wo noch nichts existiert, wird es auf Ihren Namen angelegt. Die benötigten Zugänge klären wir vor dem Start.',
	],
	[
		'id'    => 'anteil-entscheider',
		'titel' => 'Eine Person, die entscheidet',
		'text'  => 'Eine feste Person aus Geschäftsführung oder Vertriebsleitung gibt Inhalte frei und entscheidet über die nächsten Schritte.',
	],
	[
		'id'    => 'anteil-rueckruf',
		'titel' => 'Rückruf-Disziplin',
		'text'  => 'Ihr Vertrieb ruft zurück, fasst nach und pflegt den Bearbeitungsstand im CRM. Erst damit werden aus Formulareingängen nachvollziehbare Ergebnisse.',
	],
];

// ── 03 Der Fall: Projektmonate inklusive Vorbereitung ──────────
$phases = [
	[ 'id' => 'vorbereitung', 'label' => $e3_timeline['preparation_label'], 'text' => $e3_timeline['preparation'] ],
	[ 'id' => 'kampagne', 'label' => $e3_timeline['campaign_label'], 'text' => $e3_timeline['campaign'] ],
	[ 'id' => 'optimierung', 'label' => $e3_timeline['optimization_label'], 'text' => $e3_timeline['optimization'] . ' ' . $e3_timeline['followup'] ],
];


// ── 04 Smartflow: zwei Hauptprodukte ───────────────────────────
// Marktcheck und Aufbau sind die zwei Hauptentscheidungen dieser Seite.
// Die vertiefte Analyse bleibt als globales Produkt fuer andere Routen im
// Pricing-Canon bestehen, wird im Energy-Funnel aber nicht mehr angeboten.
$system_products = [
	[
		'key'   => 'photovoltaik',
		'label' => 'Photovoltaik',
		'text'  => 'Eigene Landing- und Anfragestrecke für PV-Interessenten.',
	],
	[
		'key'   => 'waermepumpe',
		'label' => 'Wärmepumpe',
		'text'  => 'Eigenständige Such-, Kampagnen- und Qualifizierungsstrecke für Heizungstausch.',
	],
	[
		'key'   => 'speicher',
		'label' => 'Speicher',
		'text'  => 'Als eigene Produktstrecke, wenn Speicher separat akquiriert und qualifiziert wird.',
	],
];

$system_included = [
	[ 'k' => 'Anfrageweg', 'v' => 'Landingpage, Formular und Vorqualifizierung' ],
	[ 'k' => 'Vertrieb', 'v' => 'CRM-Anbindung und definierte Übergabe' ],
	[ 'k' => 'Messung', 'v' => 'Server-Side-Tracking und Herkunft der Anfrage' ],
	[ 'k' => 'SEO', 'v' => 'Technische SEO-Basis der gebauten Strecke' ],
	[ 'k' => 'Kampagnen', 'v' => 'Mess- und Kampagnenarchitektur auf Ihren Konten' ],
	[ 'k' => 'Eigentum', 'v' => 'Code, Konten, Daten und Dokumentation bei Ihrem Betrieb' ],
];

// ── 05 Passung ─────────────────────────────────────────────────
$fit_yes = [
	[ 't' => 'Projektwerte ab ca. 15.000 € privat, 50.000 € gewerblich', 's' => 'Richtwerte für die Einordnung; entscheidend sind Marge, Kapazität und Zielgebiet.' ],
	[ 't' => 'Definiertes Zielgebiet', 's' => 'Region oder Bundesland. Nicht „bundesweit, alles“.' ],
	[ 't' => 'Horizont 12 bis 24 Monate', 's' => 'Bereit, Anfragegewinnung über mehrere Monate aufzubauen und zu verbessern.' ],
];
$fit_no = [
	[ 't' => '„Nächste Woche brauchen wir Leads.“', 's' => 'Ein Neuaufbau ist keine Sofortversorgung mit Kontakten.' ],
	[ 't' => 'Reines Vermittlungsgeschäft', 's' => 'Dieses Angebot ist auf ausführende Installationsbetriebe ausgerichtet.' ],
	[ 't' => 'Kein Vertriebsprozess', 's' => 'Anfragen sterben, wenn niemand konsequent qualifiziert und nachfasst.' ],
	[ 't' => 'Sichtbarkeit nicht gewollt', 's' => 'Der eigene Anfrageweg lebt davon, dass Ihr Betrieb unterscheidbar wird.' ],
];

// ── 06 Marktcheck: was im Befund steht ─────────────────────────
$report_items = [
	'Erste Einordnung Ihres Betriebs und Zielgebiets',
	'Einschätzung anhand Ihrer Projektgröße und Vertriebsstruktur',
	'Drei priorisierte Ansatzpunkte — auch wenn keine Zusammenarbeit passt',
	'Eine klare Empfehlung: jetzt, später oder gar nicht',
];

$receipt_rows = [
	[ 'k' => 'Preis',      'v' => $marketcheck_price . ' netto' ],
	[ 'k' => 'Form',       'v' => 'schriftliche Einordnung' ],
	[ 'k' => 'Frist',      'v' => $marketcheck_reply ],
	[ 'k' => 'Geprüft',    'v' => 'Haşim Üner, persönlich' ],
	[ 'k' => 'Anrechnung', 'v' => 'voll auf den Aufbau' ],
];

// ── 07 Fragen ──────────────────────────────────────────────────
// `lead` und `rest` werden fuer die Anzeige getrennt gesetzt (der Lead
// steht in Tinte, der Rest in Grau) und fuer das FAQPage-Schema wieder
// zusammengefuegt. Dadurch kann der Schema-Text nicht vom sichtbaren
// Text abweichen — eine Abweichung waere ein Rich-Result-Verstoss.
$faq_items = [
	[
		'id'   => 'faq-definition',
		'q'    => 'Was ist ein eigenes Anfragesystem für Photovoltaik und Wärmepumpe?',
		'lead' => 'Eine eigene Website gewinnt Anfragen aus Anzeigen und organischer Suche. Formulare erfassen die wichtigsten Projektangaben und übergeben sie an den Vertrieb.',
		'rest' => 'Code, Werbekonten, Tracking-Container und Daten liegen beim Betrieb. Die Kosten hängen von Angebot, Region, Wettbewerb und Optimierung ab.',
		'open' => true,
	],
	[
		'id'   => 'faq-kosten',
		'q'    => 'Was kostet es, eigene Photovoltaik-Anfragen zu generieren statt zu kaufen?',
		'lead' => sprintf( 'Das Anfragesystem startet bei %s netto für eine eigenständige Produktstrecke. Jede weitere Produktstrecke kostet %s netto zusätzlich; rund %s Hosting im Monat und Werbebudget kommen separat hinzu.', $foundation_price, $extra_product_price, $hosting_price ),
		'rest' => sprintf( 'Der Marktcheck kostet %s netto und wird bei einem anschließenden Aufbau vollständig angerechnet. Das Sofortkontakt-Setup für %s netto bleibt ein separater Spezialpfad für bereits vorhandene Anfragen.', $marketcheck_price, $setup_price ),
	],
	[
		'id'   => 'faq-cpo',
		'q'    => 'Warum ist Cost per Order aussagekräftiger als Cost per Lead?',
		'lead' => 'Cost per Order — die Kosten pro gewonnenem Auftrag — berücksichtigt die Abschlussquote und ist deshalb die belastbarere Kennzahl.',
		'rest' => 'Für einen sinnvollen Vergleich müssen Anfragekosten und Abschlussquote dieselbe Leadmenge und denselben Zeitraum betreffen. Der Rechner zeigt zusätzlich den anteiligen Aufbau und das Hosting. Vertriebszeit und Betreuung kommen hinzu.',
	],
	[
		'id'   => 'faq-tracking',
		'q'    => 'Funktioniert serverseitiges Tracking ohne Cookie-Banner?',
		'lead' => 'Serverseitiges Tracking ergänzt die Browsermessung um eine kontrollierte Server-Strecke und kann Signalverluste durch Browserbeschränkungen oder Blocker reduzieren. Es macht abgelehnte Einwilligungen nicht zu messbaren Nutzerprofilen und ersetzt keine erforderliche Einwilligung.',
		'rest' => 'Welche Messung einwilligungspflichtig ist, hängt von den eingesetzten Diensten ab; das ist eine rechtliche Bewertung und keine technische.',
	],
	[
		'id'   => 'faq-agentur',
		'q'    => 'Kann meine bestehende Website bleiben?',
		'lead' => 'Das wird vor dem Angebot geprüft. Bestehende Seiten, Formulare und Konten können Teil der Lösung bleiben, wenn sie technisch und inhaltlich geeignet sind.',
		'rest' => 'Der Marktcheck und die Projektprüfung klären, welche Bausteine bleiben können und was für eine belastbare Anfragegewinnung ergänzt werden muss. Ein vollständiger Neuaufbau ist keine automatische Voraussetzung.',
	],
];

/**
 * Join the two halves of an answer into the single string the schema needs.
 *
 * @param array<string, string> $item FAQ record.
 * @return string
 */
$faq_answer_text = static function ( $item ) {
	return trim( $item['lead'] . ' ' . $item['rest'] );
};

// ── 08 Verweise ────────────────────────────────────────────────
// Nach der Frage sortiert, die dahintersteckt — nicht nach Kategorie.
// Wer sucht, sucht eine Antwort, keine Rubrik.
$references = [
	[ 'f' => 'Was kosten Solar-Leads am Markt?',               'z' => 'Marktstudie DACH, Preise je Modell',          'url' => home_url( '/solar-leads-kosten-studie/' ) ],
	[ 'f' => 'Soll ich PV-Leads überhaupt kaufen?',            'z' => 'Portalmodell einordnen und Alternative prüfen', 'url' => home_url( '/solar-leads-kaufen-alternative/' ) ],
	[ 'f' => 'Was ist ein realistischer CPL in Photovoltaik?', 'z' => 'Drei Szenarien und Kostentreiber',            'url' => home_url( '/cost-per-lead-photovoltaik/' ) ],
	[ 'f' => 'Was gilt bei Wärmepumpen-Leads anders?',         'z' => 'Eigene Anfragequelle für SHK und Wärmepumpe', 'url' => home_url( '/waermepumpen-leads/' ) ],
	[ 'f' => 'Wie läuft gewerbliche PV-Leadgenerierung?',      'z' => 'Buying-Center und Vorqualifizierung im B2B',  'url' => home_url( '/b2b-solar-leads/' ) ],
	[ 'f' => 'Wie wird die Strecke belastbar gemessen?',       'z' => 'Tracking-Setup, Ads und Consent',             'url' => $tracking_url ],
];

// ── Abschluss: Protokollzeile ──────────────────────────────────
// "Befund", nicht "Antwort": es sind zwei Vorgaenge. Der Fuss der Domain
// verspricht eine Antwort auf eine gewoehnliche Anfrage, diese Zeile den
// haendisch geschriebenen Marktcheck-Befund. Seit der Vereinheitlichung am
// 2026-09-12 nennen beide dieselbe Frist — das Label unterscheidet sie, nicht
// die Zahl, und genau so ist es gemeint.
$protocol_rows = [
	[ 'k' => 'Befund',      'v' => $marketcheck_reply ],
	[ 'k' => 'Sitz',        'v' => 'Pattensen · Hannover' ],
	[ 'k' => 'Arbeitsweise', 'v' => 'remote in DACH, 1:1' ],
	[ 'k' => 'Messung',     'v' => 'serverseitig, Frankfurt' ],
	[ 'k' => 'Kontakt',     'v' => $contact_email ],
];

// ── Breadcrumb ─────────────────────────────────────────────────
// Sichtbar in der Dokumentsprache plus genau ein Schema-Block weiter
// unten. Die geteilte template-parts/breadcrumb.php bleibt hier aussen
// vor: sie brächte ein zweites BreadcrumbList-Markup mit.
$breadcrumb_trail = [
	[ 'name' => 'Start', 'url' => home_url( '/' ) ],
	[ 'name' => 'Anfragestrecke Photovoltaik & Wärmepumpe', 'url' => $page_url ],
];

// ══ Schema.org ═════════════════════════════════════════════════
$organization_id = trailingslashit( home_url( '/' ) ) . '#organization';
$person_id       = function_exists( 'hu_person_schema_id' ) ? hu_person_schema_id() : home_url( '/hasim-uener/#person' );

$schema_blocks = [];

// 1 · Service
$schema_blocks[] = [
	'@context'         => 'https://schema.org',
	'@type'            => 'Service',
	'@id'              => $page_url . '#service',
	'name'             => 'Aufbau eigener Anfragesysteme für Photovoltaik, Wärmepumpe und Speicher',
	'alternateName'    => [ 'Photovoltaik Leadgenerierung', 'Eigene Solar Leads gewinnen', 'B2B Solar Leads' ],
	'serviceType'      => 'Eigene Anfragestrecke auf der Domain des Betriebs: Vorqualifizierung, serverseitiges Tracking, Vertriebsanschluss und dokumentierte Übergabe',
	'category'         => 'B2B Lead Generation Infrastructure',
	'url'              => $page_url,
	'mainEntityOfPage' => $page_url,
	'description'      => sprintf(
		'Eigene Anfragestrecke für Solar-, Wärmepumpen- und Speicher-Betriebe im DACH-Raum statt gekaufter Portal-Leads. Im dokumentierten Fall (%1$s) fielen die Kosten pro qualifizierter Anfrage in %2$s von %3$s auf %4$s, eine Reduktion von %5$s.',
		$e3_case_label,
		$e3_timeframe,
		$e3_cpl_before,
		$e3_cpl_after,
		$e3_cpl_reduction
	),
	'provider'         => [
		'@type'   => 'Organization',
		'@id'     => $organization_id,
		'name'    => 'Haşim Üner — Anfragesysteme für Solar & Wärmepumpe',
		'url'     => home_url( '/' ),
		'founder' => [ '@id' => $person_id ],
	],
	'audience'         => [
		'@type'        => 'BusinessAudience',
		'audienceType' => 'Solar-, Wärmepumpen-, Speicher- und SHK-Betriebe im DACH-Mittelstand mit eigenem Vertrieb',
	],
	'areaServed'       => [
		[ '@type' => 'Country', 'name' => 'Deutschland' ],
		[ '@type' => 'Country', 'name' => 'Österreich' ],
		[ '@type' => 'Country', 'name' => 'Schweiz' ],
	],
	'serviceOutput'    => [
		'@type'       => 'Thing',
		'name'        => 'Eigene Anfragestrecke',
		'description' => 'Anfragestrecke auf der Domain des Betriebs. Code, Werbekonten, Tracking-Container und Daten verbleiben beim Auftraggeber.',
	],
	'offers'           => [
		[
			'@type'         => 'Offer',
			'name'          => 'Marktcheck für Solar- und SHK-Betriebe',
			'price'         => (string) $marketcheck_price_value,
			'priceCurrency' => 'EUR',
			'description'   => sprintf( 'Bezahlter diagnostischer Einstieg mit kurzer Ausgangslage und persönlicher schriftlicher Einordnung per E-Mail — %s. Der Betrag wird bei anschließendem Aufbau vollständig angerechnet.', $marketcheck_reply ),
			'availability'  => 'https://schema.org/InStock',
		],
		[
			'@type'         => 'Offer',
			'name'          => 'Eigenes Anfragesystem',
			'price'         => (string) $calc_build,
			'priceCurrency' => 'EUR',
			'description'   => sprintf( 'Eine eigenständige Produktstrecke inklusive Vorqualifizierung, CRM-Anbindung, serverseitiger Messung, technischer SEO-Basis und dokumentierter Übergabe. Jede weitere eigenständige Produktstrecke %s zusätzlich. Netto, zuzüglich rund %s Hosting im Monat.', $extra_product_price, $hosting_price ),
			'availability'  => 'https://schema.org/InStock',
		],
	],
	'isRelatedTo'      => array_map(
		static function ( $reference ) {
			return [
				'@type' => 'WebPage',
				'url'   => $reference['url'],
				'name'  => $reference['f'],
			];
		},
		array_slice( $references, 0, 5 )
	),
];

// 2 · DefinedTerm — der Begriff, den diese Seite besetzt
//
// Kein eigener WebPage- und kein eigener BreadcrumbList-Block: beide gibt
// inc/org-schema.php global aus, unter exakt denselben @ids
// (<permalink>#webpage und <permalink>#breadcrumb). Ein zweiter Knoten
// unter derselben @id ist kein zusaetzliches Signal, sondern ein
// widerspruechlicher Graph. Der sichtbare Breadcrumb oben im Dokument
// bleibt davon unberuehrt — er gehoert zur Seite, nicht zum Schema.
//
// Der Begriff haengt deshalb per subjectOf an der globalen WebPage, statt
// sie zu ersetzen.
$schema_blocks[] = [
	'@context'         => 'https://schema.org',
	'@type'            => 'DefinedTerm',
	'@id'              => $page_url . '#begriff-anfragesystem',
	'name'             => 'Eigenes Anfragesystem',
	'description'      => $faq_answer_text( $faq_items[0] ),
	'url'              => $page_url,
	'subjectOf'        => [ '@id' => $page_url . '#webpage' ],
	'inDefinedTermSet' => [
		'@type' => 'DefinedTermSet',
		'name'  => 'Anfragegewinnung für Photovoltaik, Wärmepumpe und Speicher',
		'url'   => home_url( '/glossar/' ),
	],
];

// 4 · DefinedTerm — die Kennzahl, auf der die Rechnung steht
$schema_blocks[] = [
	'@context'         => 'https://schema.org',
	'@type'            => 'DefinedTerm',
	'@id'              => $page_url . '#begriff-cost-per-order',
	'name'             => 'Cost per Order',
	'description'      => $faq_answer_text( $faq_items[2] ),
	'url'              => $page_url . '#rechnung',
	'subjectOf'        => [ '@id' => $page_url . '#webpage' ],
	'inDefinedTermSet' => [
		'@type' => 'DefinedTermSet',
		'name'  => 'Anfragegewinnung für Photovoltaik, Wärmepumpe und Speicher',
		'url'   => home_url( '/glossar/' ),
	],
];

// 5 · FAQPage — Text wortgleich zur sichtbaren Antwort
$schema_blocks[] = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => $page_url . '#faq',
	'url'        => $page_url . '#fragen',
	'mainEntity' => array_map(
		static function ( $item ) use ( $faq_answer_text ) {
			return [
				'@type'          => 'Question',
				'name'           => $item['q'],
				'acceptedAnswer' => [
					'@type' => 'Answer',
					'text'  => $faq_answer_text( $item ),
				],
			];
		},
		$faq_items
	),
];

get_header();
?>

<div class="site-main">
	<?php
	// .solara-landing bleibt als stabile Wurzel fuer bestehende Styles und
	// Tracking-Selektoren. Formulare leben bewusst nicht mehr auf dieser Seite.
	?>
	<div class="solara-landing strecke-doc" data-track-section="anfragestrecke">
		<?php
		// ════════ Kapitelregister ════════
		//
		// Auf breiten Schirmen eine schmale senkrechte Leiste am linken
		// Rand, die nur die Nummern zeigt und bei Hover oder Tastaturfokus
		// auf die volle Breite mit Titeln aufklappt. Darunter eine flache
		// waagerechte Leiste unter dem Header.
		//
		// Dieselbe Liste in beiden Fassungen, ein Markup: der Unterschied
		// liegt vollstaendig im Stylesheet. Ein zweites, per Media Query
		// ausgeblendetes Markup haette dieselben Anker doppelt im Dokument
		// und damit doppelt im Tastaturlauf.
		?>
		<nav class="register" aria-label="Abschnitte dieses Dokuments" data-strecke-leiste>
			<span class="marke" aria-hidden="true">
				<span class="marke-kuerzel">Reg.</span>
				<span class="marke-voll">Register</span>
			</span>
			<div class="eintraege">
				<?php foreach ( $chapters as $chapter ) : ?>
					<a href="#<?php echo esc_attr( $chapter['id'] ); ?>"
						data-track-action="chapter_jump"
						data-track-category="navigation"
						data-track-section="register"
					>
						<span class="nr"><?php echo esc_html( $chapter['nr'] ); ?></span>
						<span class="txt"><?php echo esc_html( $chapter['titel'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</nav>

		<!-- ════════ Dokumentkopf ════════ -->
		<div class="blatt kopfteil">

			<nav class="pfad" aria-label="Brotkrumennavigation">
				<ol>
					<?php
					$crumb_total = count( $breadcrumb_trail );
					foreach ( $breadcrumb_trail as $crumb_index => $crumb ) :
						$is_last = ( $crumb_index === $crumb_total - 1 );
						?>
						<?php if ( $is_last ) : ?>
							<li aria-current="page"><?php echo esc_html( $crumb['name'] ); ?></li>
						<?php else : ?>
							<li><a href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['name'] ); ?></a></li>
							<li class="tr" aria-hidden="true">/</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ol>
			</nav>

			<div class="reihe">
				<div class="haupt breit">
					<p class="gegenstand">Eigenes Anfragesystem · Photovoltaik · Wärmepumpe · Speicher</p>

					<h1>Anfragen, die nur bei Ihnen ankommen. <em>Auf einem System, das Ihnen gehört.</em></h1>

					<p class="aufriss">
						<span class="erst">Statt denselben Portal-Kontakt mit mehreren Betrieben zu teilen, bauen Sie auf Ihrer Domain eine eigene Anfragequelle.</span>
						Ich verbinde Landingpages, Vorqualifizierung, Tracking und CRM zu einer Strecke — von der Herkunft bis zum Abschluss.
						Code, Werbekonten und Daten bleiben bei Ihrem Betrieb.
					</p>

					<div class="ausgang">
						<a class="tun" href="#marktcheck"
							data-track-action="cta_strecke_kopf_to_marktcheck"
							data-track-category="lead_gen"
							data-track-section="dokumentkopf"
						>Marktcheck · <?php echo esc_html( $marketcheck_price ); ?> <span class="pf" aria-hidden="true">→</span></a>
						<a class="hero-nebenweg" href="#einstieg"
							data-track-action="cta_strecke_kopf_to_system"
							data-track-category="lead_gen"
							data-track-section="dokumentkopf"
						>Anfragesystem konfigurieren · ab <?php echo esc_html( $foundation_price ); ?> →</a>
					</div>
					<p class="cta-sicherheit"><?php echo esc_html( sprintf( '%s netto · kurzer Intake · persönliche Einordnung %s', $marketcheck_price, $marketcheck_reply ) ); ?></p>

					<div class="hero-beleg" aria-label="Kennzahlen des dokumentierten Referenzfalls">
						<div>
							<span class="hero-beleg-wert zahl"><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></span>
							<span class="hero-beleg-label">Kosten pro qualifizierter Anfrage</span>
						</div>
						<div>
							<span class="hero-beleg-wert zahl"><?php echo esc_html( $e3_lead_count ); ?></span>
							<span class="hero-beleg-label">Anfragen · Zeitraum: <?php echo esc_html( $e3_timeframe ); ?></span>
						</div>
						<div>
							<span class="hero-beleg-wert zahl"><?php echo esc_html( $e3_sales_conv ); ?></span>
							<span class="hero-beleg-label">Abschlussquote vorqualifizierter CRM-Leads</span>
						</div>
						<p>Dokumentierter Fall · <?php echo esc_html( ucfirst( $e3_case_label ) ); ?> in DACH. Keine Prognose für Ihren Betrieb.</p>
					</div>

					<div class="meta">
						<dl>
							<div>
								<dt>Aufbau</dt>
								<dd><span class="zahl"><?php echo esc_html( $foundation_price ); ?></span> netto · plus rund <span class="zahl"><?php echo esc_html( $hosting_price ); ?></span>/Mon. Hosting</dd>
							</div>
							<div>
								<dt>Eigentum</dt>
								<dd>Domain, Code, Werbekonten und Daten bleiben bei Ihrem Betrieb.</dd>
							</div>
							<div>
								<dt>Verantwortlich</dt>
								<dd class="hero-person">
									<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/hasim-freelancer-portrait-112.webp' ); ?>" alt="" width="44" height="44" decoding="async">
									<span>Haşim Üner · Strategie, Umsetzung und Befund persönlich</span>
								</dd>
							</div>
						</dl>
					</div>

				</div>
			</div>

			<!-- Messschrieb: der eigene, gemessene Fall -->
			<div class="reihe schrieb">
				<div class="haupt breit tafel">
					<div class="kopfzeile">
						<h2 id="messschrieb">Dokumentierter Fall: <?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></h2>
						<span class="mono">Kosten pro qualifizierter Anfrage · <?php echo esc_html( $e3_timeframe ); ?> · Einzelfall</span>
					</div>

					<figure class="bandtreppe" aria-labelledby="messschrieb treppe-hinweis">
						<svg viewBox="0 0 1000 230" preserveAspectRatio="none" aria-hidden="true">
							<line x1="16" y1="210" x2="970" y2="210" stroke="var(--strich)" vector-effect="non-scaling-stroke" />
							<?php
							// Gemeinsamer Maßstab: 150 € entsprechen 180 SVG-Einheiten.
							// Nur Bandbreiten aus der dokumentierten Fallbeschreibung.
							$bands = [
								[ 'phase' => 'portal', 'label' => 'Vorher', 'title' => 'Vor dem Projekt · Portale', 'value' => '150 €', 'low' => 150, 'high' => 150 ],
								[ 'phase' => 'vorbereitung', 'label' => '1', 'title' => 'Projektmonat 1 · Vorbereitung', 'value' => 'Keine eigenen Kampagnen', 'low' => null, 'high' => null ],
								[ 'phase' => 'kampagne', 'label' => '2', 'title' => 'Projektmonat 2 · Kampagnenstart', 'value' => '70–100 €', 'low' => 70, 'high' => 100 ],
								[ 'phase' => 'optimierung', 'label' => '3', 'title' => 'Projektmonat 3 · Optimierung', 'value' => '30–50 €, dann rund 22 €', 'low' => 30, 'high' => 50 ],
								[ 'phase' => 'optimierung', 'label' => '4–6', 'title' => 'Projektmonate 4–6', 'value' => 'Überwiegend 22–30 €', 'low' => 22, 'high' => 30 ],
							];
							foreach ( $bands as $band_index => $band ) :
								$band_x = 16 + $band_index * 196;
								?>
								<g data-treppenphase="<?php echo esc_attr( $band['phase'] ); ?>">
									<g class="treppe-form" style="--versatz: <?php echo esc_attr( (string) ( $band_index * 60 ) ); ?>ms">
										<line x1="<?php echo esc_attr( (string) $band_x ); ?>" y1="30" x2="<?php echo esc_attr( (string) ( $band_x + 170 ) ); ?>" y2="30" stroke="var(--haar)" vector-effect="non-scaling-stroke" />
										<?php if ( null !== $band['low'] ) : ?>
											<?php $band_y = 210 - $band['high'] * 180 / $e3_cpl_before_val; ?>
											<?php if ( $band['low'] === $band['high'] ) : ?>
												<line class="treppe-marke" x1="<?php echo esc_attr( (string) $band_x ); ?>" y1="<?php echo esc_attr( (string) $band_y ); ?>" x2="<?php echo esc_attr( (string) ( $band_x + 170 ) ); ?>" y2="<?php echo esc_attr( (string) $band_y ); ?>" vector-effect="non-scaling-stroke" />
											<?php else : ?>
												<rect class="treppe-band" x="<?php echo esc_attr( (string) $band_x ); ?>" y="<?php echo esc_attr( (string) $band_y ); ?>" width="170" height="<?php echo esc_attr( (string) ( ( $band['high'] - $band['low'] ) * 180 / $e3_cpl_before_val ) ); ?>" />
											<?php endif; ?>
											<?php if ( 3 === $band_index ) : ?>
												<line class="treppe-marke" x1="<?php echo esc_attr( (string) $band_x ); ?>" y1="183.6" x2="<?php echo esc_attr( (string) ( $band_x + 170 ) ); ?>" y2="183.6" vector-effect="non-scaling-stroke" />
											<?php endif; ?>
										<?php endif; ?>
									</g>
								</g>
							<?php endforeach; ?>
						</svg>
						<div class="treppe-monate mono" aria-hidden="true">
							<?php foreach ( $bands as $band ) : ?>
								<span><?php echo esc_html( $band['label'] ); ?></span>
							<?php endforeach; ?>
						</div>
						<dl class="treppe-daten">
							<?php foreach ( $bands as $band ) : ?>
								<div data-treppenphase="<?php echo esc_attr( $band['phase'] ); ?>">
									<dt><?php echo esc_html( $band['title'] ); ?></dt>
									<dd><?php echo esc_html( $band['value'] ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
						<figcaption id="treppe-hinweis">Bandbreiten laut Fallbeschreibung, keine Monatsmesskurve.</figcaption>
					</figure>

					<table class="werte">
						<caption class="nur-vorlesen">Kennzahlen des dokumentierten Falls</caption>
						<tbody>
							<tr>
								<th scope="row"><?php echo esc_html( $e3_timeline['result_label'] ); ?></th>
								<td class="gross"><?php echo esc_html( $e3_cpl_after ); ?></td>
							</tr>
							<tr>
								<th scope="row">Ausgangswert über Portale vor dem Projekt</th>
								<td><?php echo esc_html( $e3_cpl_before ); ?></td>
							</tr>
							<tr>
								<th scope="row">Anfragen insgesamt im Zeitraum</th>
								<td><?php echo esc_html( $e3_lead_count ); ?></td>
							</tr>
							<tr>
								<th scope="row">Abschlussquote vorqualifizierter CRM-Leads</th>
								<td class="gross"><?php echo esc_html( $e3_sales_conv ); ?></td>
							</tr>
							<tr class="quelle">
								<th scope="row" colspan="2">
									<?php echo esc_html( ucfirst( $e3_case_label ) ); ?> in DACH.
									<?php echo esc_html( $e3_timeline['compact'] ); ?>
										Reduktion <?php echo esc_html( $e3_cpl_reduction ); ?>.
									Dokumentierte Werte eines einzelnen Betriebs, keine Prognose für Ihren.
								</th>
							</tr>
						</tbody>
					</table>
					<p class="belegzeile"><?php echo esc_html( hu_e3_summary( 'definitions' ) ); ?></p>
				</div>
			</div>

			<!-- Marktzeile: fremde, ueberpruefbare Zahlen -->
			<?php if ( ! empty( $market_figures ) ) : ?>
				<div class="reihe markt">
					<div class="haupt breit">
						<span class="mono ueberschrift">Zum Vergleich · was der Markt aufruft</span>
						<div class="marktzeile">
							<?php foreach ( $market_figures as $figure ) : ?>
								<div>
									<span class="w zahl"><?php echo esc_html( $figure['value'] ); ?></span>
									<p><?php echo esc_html( $figure['body'] ); ?></p>
								</div>
							<?php endforeach; ?>
						</div>
						<p class="belegzeile"><?php echo esc_html( $market_disclaimer ); ?></p>
					</div>
				</div>
			<?php endif; ?>

		</div>

		<!-- ════════ 01 Die Strecke ════════ -->
		<section id="strecke">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['strecke'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="strecke-titel">Fünf Stationen entscheiden, ob aus Nachfrage ein Auftrag wird.</h2>
					<p class="vorspann">Der Hebel ist nicht nur mehr Traffic. Quelle, Vorqualifizierung, Reaktionszeit, Nachfassen und Abschlussdaten müssen als eine Strecke zusammenarbeiten. Das Modell darunter zeigt, wo sich Lead-Einkauf und eigene Anfragequelle strukturell unterscheiden.</p>

					<div class="streckenmodul tafel" id="modul" data-streckenmodul data-counts="<?php echo esc_attr( wp_json_encode( $module_counts ) ); ?>">
						<div class="modul-kopf"><h3>Wo der Unterschied entsteht.</h3><span class="mono">Modellrechnung · ein Punkt = eine Anfrage</span></div>
						<div class="modul-buehne" data-module-stage>
							<?php
							$module_layouts = [
								'wide' => [ 'w' => 1000, 'h' => 400, 'xs' => [ 100, 280, 460, 640, 820 ], 'a' => 110, 'b' => 296, 'r' => 4.6, 'spread' => 26, 'fall' => 40 ],
								'narrow' => [ 'w' => 520, 'h' => 470, 'xs' => [ 40, 135, 230, 325, 420 ], 'a' => 95, 'b' => 330, 'r' => 6.5, 'spread' => 30, 'fall' => 46 ],
							];
							?>
							<?php foreach ( $module_layouts as $layout_name => $layout ) : ?>
								<svg class="modul-svg modul-svg--<?php echo esc_attr( $layout_name ); ?>" viewBox="0 0 <?php echo esc_attr( (string) $layout['w'] ); ?> <?php echo esc_attr( (string) $layout['h'] ); ?>" role="img" aria-labelledby="modul-<?php echo esc_attr( $layout_name ); ?>-title modul-<?php echo esc_attr( $layout_name ); ?>-desc" data-layout="<?php echo esc_attr( $layout_name ); ?>">
									<title id="modul-<?php echo esc_attr( $layout_name ); ?>-title">Zwei Wege, ein Monatsbudget</title>
									<desc id="modul-<?php echo esc_attr( $layout_name ); ?>-desc">Weg A kauft <?php echo esc_html( (string) $module_counts['a'][0] ); ?> Anfragen. Daraus werden <?php echo esc_html( $format_numeric_orders( $module_a_orders ) ); ?> zu <?php echo esc_html( $format_eur( $module_a_cpo ) ); ?> je Auftrag. Weg B gewinnt rund <?php echo esc_html( (string) $module_counts['b'][0] ); ?> eigene Anfragen. Daraus werden <?php echo esc_html( $format_numeric_orders( $module_b_orders ) ); ?> zu <?php echo esc_html( $format_eur( $module_b_cpo ) ); ?> je Auftrag einschließlich anteiligem Aufbau und Hosting.</desc>
									<g class="modul-raster" aria-hidden="true">
										<?php foreach ( $layout['xs'] as $station_index => $x ) : ?>
											<line x1="<?php echo esc_attr( (string) $x ); ?>" x2="<?php echo esc_attr( (string) $x ); ?>" y1="<?php echo esc_attr( (string) ( $layout['a'] - 50 ) ); ?>" y2="<?php echo esc_attr( (string) ( $layout['b'] + 75 ) ); ?>" data-station="<?php echo esc_attr( (string) $station_index ); ?>" />
										<?php endforeach; ?>
										<line class="modul-linie modul-linie--a" x1="10" x2="<?php echo esc_attr( (string) ( $layout['w'] - 10 ) ); ?>" y1="<?php echo esc_attr( (string) $layout['a'] ); ?>" y2="<?php echo esc_attr( (string) $layout['a'] ); ?>" />
										<line class="modul-linie modul-linie--b" x1="10" x2="<?php echo esc_attr( (string) ( $layout['w'] - 10 ) ); ?>" y1="<?php echo esc_attr( (string) $layout['b'] ); ?>" y2="<?php echo esc_attr( (string) $layout['b'] ); ?>" />
									</g>
									<g class="modul-punkte" aria-hidden="true">
										<?php foreach ( [ 'a', 'b' ] as $way ) : ?>
											<?php for ( $dot = 0; $dot < $module_counts[ $way ][0]; $dot++ ) : ?>
												<?php
											$last = 4;
											for ( $station = 1; $station < 5; $station++ ) {
												if ( $dot >= $module_counts[ $way ][ $station ] ) {
													$last = $station - 1;
													break;
												}
											}
											$dot_y = $layout[ $way ] + ( ( $dot * 17 ) % ( $layout['spread'] * 2 ) ) - $layout['spread'] + ( $last < 4 ? $layout['fall'] : 0 );
											$dot_x = $layout['xs'][ $last ] + ( $dot % 7 - 3 ) * 3;
												?>
												<circle class="modul-punkt modul-punkt--<?php echo esc_attr( $way ); ?>" r="<?php echo esc_attr( (string) $layout['r'] ); ?>" cx="<?php echo esc_attr( (string) $dot_x ); ?>" cy="<?php echo esc_attr( (string) $dot_y ); ?>" data-way="<?php echo esc_attr( $way ); ?>" data-last="<?php echo esc_attr( (string) $last ); ?>" data-index="<?php echo esc_attr( (string) $dot ); ?>" />
											<?php endfor; ?>
										<?php endforeach; ?>
									</g>
								</svg>
							<?php endforeach; ?>
							<div class="modul-labels" aria-hidden="true">
								<?php foreach ( [ 'a' => sprintf( 'Weg A · %d Anfragen gekauft', $module_counts['a'][0] ), 'b' => sprintf( 'Weg B · %d eigene Anfragen', $module_counts['b'][0] ) ] as $way => $label ) : ?>
									<div class="modul-labels-row modul-labels-row--<?php echo esc_attr( $way ); ?>">
										<span class="modul-way mono"><?php echo esc_html( $label ); ?></span>
										<div class="modul-counts">
											<?php foreach ( $module_counts[ $way ] as $count ) : ?><span class="modul-count"><?php echo esc_html( (string) $count ); ?></span><?php endforeach; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="modul-ende">
							<div><span class="mono">Weg A · Anfragen einkaufen</span><b><?php echo esc_html( $format_eur( $module_a_cpo ) ); ?></b><small>je Auftrag · <?php echo esc_html( $format_numeric_orders( $module_a_orders ) ); ?> im Monat</small></div>
							<div class="modul-ende-b"><span class="mono">Weg B · Eigene Strecke</span><b><?php echo esc_html( $format_eur( $module_b_cpo ) ); ?></b><small>je Auftrag · <?php echo esc_html( $format_numeric_orders( $module_b_orders ) ); ?> · inkl. Aufbau/<?php echo esc_html( (string) $calc_months ); ?> Mon. und Hosting</small></div>
						</div>
						<div class="modul-tabs" role="tablist" aria-label="Stationen einer Anfrage">
							<?php foreach ( $module_stations as $station_index => $station ) : ?>
								<button type="button" role="tab" id="modul-tab-<?php echo esc_attr( (string) $station_index ); ?>" aria-label="<?php echo esc_attr( sprintf( '%02d · %s', $station_index + 1, $station['name'] ) ); ?>" aria-controls="modul-panel" aria-selected="<?php echo 0 === $station_index ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $station_index ? '0' : '-1'; ?>" data-name="<?php echo esc_attr( $station['name'] ); ?>" data-built="<?php echo esc_attr( $station['built'] ); ?>" data-a="<?php echo esc_attr( $station['a'] ); ?>" data-b="<?php echo esc_attr( $station['b'] ); ?>">
									<span class="mono"><?php echo esc_html( sprintf( '%02d', $station_index + 1 ) ); ?></span>
									<span class="modul-tab-name"><?php echo esc_html( $station['name'] ); ?></span>
								</button>
							<?php endforeach; ?>
						</div>
						<div class="modul-panel" id="modul-panel" role="tabpanel" aria-labelledby="modul-tab-0" aria-live="polite">
							<p class="modul-panel-name">01 · <?php echo esc_html( $module_stations[0]['name'] ); ?></p>
							<div><span class="mono">Was gebaut wird</span><p data-module-copy="built"><?php echo esc_html( $module_stations[0]['built'] ); ?></p></div>
							<div><span class="mono">Weg A · Einkauf</span><p data-module-copy="a"><?php echo esc_html( $module_stations[0]['a'] ); ?></p></div>
							<div><span class="mono">Weg B · Eigene Strecke</span><p data-module-copy="b"><?php echo esc_html( $module_stations[0]['b'] ); ?></p></div>
						</div>
						<div class="modul-leiste">
							<p>Anfang und Ende gerechnet wie im Rechner (Abschnitt 02). Die Zwischenstufen sind schematisch und zeigen, wo verloren wird, nicht wie viel.</p>
							<button class="modul-replay" type="button">Noch einmal abspielen</button>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 02 Die Rechnung ════════ -->
		<section id="rechnung">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['rechnung'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="rechnung-titel">Der CPL endet am Formular. Entscheidend ist der Auftrag.</h2>
					<p class="vorspann">Rechnen Sie beide Wege mit denselben Bezugsgrößen. Anfragekosten, Abschlussquote und laufender Aufwand müssen zusammenpassen. Die vorbelegten Werte sind ein Rechenbeispiel, keine Prognose.</p>

					<div class="rechenblatt"
						data-strecke-rechner
						data-aufbau="<?php echo esc_attr( (string) $calc_build ); ?>"
						data-monate="<?php echo esc_attr( (string) $calc_months ); ?>"
						data-hosting="<?php echo esc_attr( (string) $calc_hosting ); ?>">

						<div class="weg">
							<span class="mono">Weg A · Anfragen einkaufen</span>

							<div class="eingabe">
								<label for="strecke-a1">Gekaufte Anfragen pro Monat</label>
								<span class="feld">
									<input id="strecke-a1" data-feld="a1" type="number" inputmode="numeric" min="0" max="500" step="1" value="<?php echo esc_attr( (string) $calc_defaults['a1'] ); ?>">
									<span class="einheit">Stk</span>
								</span>
							</div>
							<div class="eingabe">
								<label for="strecke-a2">Preis pro Anfrage</label>
								<span class="feld">
									<input id="strecke-a2" data-feld="a2" type="number" inputmode="numeric" min="0" max="1000" step="1" value="<?php echo esc_attr( (string) $calc_defaults['a2'] ); ?>">
									<span class="einheit">€</span>
								</span>
							</div>
							<div class="eingabe">
								<label for="strecke-a3">Abschlussquote auf diese Anfragen</label>
								<span class="feld">
									<input id="strecke-a3" data-feld="a3" type="number" inputmode="decimal" min="0" max="100" step="0.5" value="<?php echo esc_attr( (string) $calc_defaults['a3'] ); ?>">
									<span class="einheit">%</span>
								</span>
							</div>

							<div class="summe" aria-live="polite">
								<div class="z"><span>Einkauf pro Monat</span><b data-ausgabe="oA1">–</b></div>
								<div class="z"><span>Aufträge pro Monat</span><b data-ausgabe="oA2">–</b></div>
								<div class="haupt">
									<span class="k">Kosten je gewonnener Auftrag</span>
									<span class="w" data-ausgabe="oA3">–</span>
								</div>
							</div>
							<p class="quote-hinweis">Die Quote ist niedrig, weil derselbe Kontakt an 3 bis 5 Betriebe geht. Exklusive Portal-Anfragen kosten rund 150 € statt 80 €.</p>
						</div>

						<div class="weg b">
							<span class="mono">Weg B · Eigene Strecke</span>

							<div class="eingabe">
								<label for="strecke-b1">Werbebudget pro Monat</label>
								<span class="feld">
									<input id="strecke-b1" data-feld="b1" type="number" inputmode="numeric" min="0" max="50000" step="100" value="<?php echo esc_attr( (string) $calc_defaults['b1'] ); ?>">
									<span class="einheit">€</span>
								</span>
							</div>
							<div class="eingabe">
								<label for="strecke-b2">Kosten pro Anfrage, die Sie ansetzen</label>
								<span class="feld">
									<input id="strecke-b2" data-feld="b2" type="number" inputmode="numeric" min="1" max="1000" step="1" value="<?php echo esc_attr( (string) $calc_defaults['b2'] ); ?>">
									<span class="einheit">€</span>
								</span>
							</div>
							<div class="eingabe">
								<label for="strecke-b3">Abschlussquote auf vorqualifizierte Anfragen</label>
								<span class="feld">
									<input id="strecke-b3" data-feld="b3" type="number" inputmode="decimal" min="0" max="100" step="0.5" value="<?php echo esc_attr( (string) $calc_defaults['b3'] ); ?>">
									<span class="einheit">%</span>
								</span>
							</div>

							<div class="summe" aria-live="polite">
								<div class="z"><span>Budget + Aufbau/<?php echo esc_html( (string) $calc_months ); ?> Mon. + Hosting</span><b data-ausgabe="oB1">–</b></div>
								<div class="z"><span>Aufträge pro Monat</span><b data-ausgabe="oB2">–</b></div>
								<div class="haupt">
									<span class="k">Kosten je gewonnener Auftrag</span>
									<span class="w" data-ausgabe="oB3">–</span>
								</div>
							</div>
						</div>
						<figure class="auftragsbalken" aria-label="Kosten je gewonnener Auftrag im gemeinsamen Maßstab">
							<figcaption class="mono">Kosten je gewonnener Auftrag</figcaption>
							<?php foreach ( [ 'A', 'B' ] as $bar_way ) : ?>
								<div class="balken-zeile" data-balken="<?php echo esc_attr( $bar_way ); ?>">
									<span class="mono">Weg <?php echo esc_html( $bar_way ); ?></span>
									<div class="balken-spur">
										<span class="balken-flaeche"></span>
										<span class="balken-wert"><b>–</b></span>
									</div>
								</div>
							<?php endforeach; ?>
						</figure>
					</div>

					<p class="fuss">
						<b>Enthalten:</b> In Weg B sind <?php echo esc_html( $foundation_price ); ?> Aufbau auf
						<?php echo esc_html( (string) $calc_months ); ?> Monate verteilt, rund
						<?php echo esc_html( $hosting_price ); ?> Hosting monatlich und Ihr Werbebudget.
						Vertriebszeit und laufende Betreuung sind nicht eingerechnet.
						Voreingestellt sind <?php echo esc_html( $calc_cpl_display ); ?> pro Anfrage und
						<?php echo esc_html( $calc_quote_display ); ?> Abschlussquote — der Wert aus dem dokumentierten Fall.
						Kosten und Quote müssen sich auf dieselbe Leadmenge beziehen.
					</p>
				</div>
			</div>
		</section>

		<!-- ════════ 03 Der Fall ════════ -->
		<section id="ergebnisse">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['ergebnisse'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="fall">Was sich im dokumentierten Fall tatsächlich verändert hat.</h2>
					<p class="vorspann">
						Zeitraum: <?php echo esc_html( $e3_timeframe ); ?>. Fall: <?php echo esc_html( ucfirst( $e3_case_label ) ); ?> in DACH. Erst Strategie und Landingpages, dann Kampagnen, anschließend Tracking und CRM-Rückführung.
						Die Entwicklung beruht auf dem Zusammenspiel der Maßnahmen; der isolierte Beitrag einzelner Bausteine ist nicht gemessen.
					</p>

					<div class="phasen">
						<?php foreach ( $phases as $phase ) : ?>
							<div tabindex="0" data-fallphase="<?php echo esc_attr( $phase['id'] ); ?>">
								<span class="ph"><?php echo esc_html( $phase['label'] ); ?></span>
								<?php // Enthaelt nur <b> aus dem Inhaltsmodell oben, alle Werte sind dort escaped. ?>
								<p><?php echo wp_kses( $phase['text'], [ 'b' => [] ] ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="ausgang">
						<a class="textlink" href="<?php echo esc_url( $e3_url ); ?>"
							data-track-action="cta_strecke_fall_to_case"
							data-track-category="proof"
							data-track-section="fall"
						>Vollständige Methodik in der Fallstudie →</a>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 04 Produkte & Konfigurator ════════ -->
		<section id="einstieg">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['einstieg'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="leiter">Zwei Produkte. Ein klarer Einstieg.</h2>
					<p class="vorspann">Für eine erste wirtschaftliche Einordnung gibt es den Marktcheck für <?php echo esc_html( $marketcheck_price ); ?> netto. Wenn der Aufbau bereits klar ist, konfigurieren Sie direkt das eigene Anfragesystem ab <?php echo esc_html( $foundation_price ); ?> netto. Keine Analyse-Stufe dazwischen.</p>

					<div class="smartflow-produkte">
						<article class="smartflow-marktcheck">
							<div class="smartflow-kopf">
								<span class="mono">01 · kleiner Funnel</span>
								<h3>Marktcheck</h3>
							</div>
							<p>Kurzer Intake zu Betrieb, Zielgebiet, Vertrieb und heutiger Anfragegewinnung. Ich prüfe die Ausgangslage persönlich und schicke eine schriftliche Einordnung mit dem sinnvollen nächsten Schritt.</p>
							<div class="smartflow-preis">
								<strong class="zahl"><?php echo esc_html( $marketcheck_price ); ?></strong>
								<span>netto · bei Aufbau vollständig anrechenbar</span>
							</div>
							<a class="tun" href="<?php echo esc_url( $marketcheck_contact_url ); ?>"
								data-track-action="cta_strecke_marktcheck_to_contact"
								data-track-category="lead_gen"
								data-track-section="einstieg"
							>Marktcheck anfragen <span class="pf" aria-hidden="true">→</span></a>
						</article>

						<article class="system-konfigurator tafel"
							data-system-configurator
							data-base-price="<?php echo esc_attr( (string) $calc_build ); ?>"
							data-extra-price="<?php echo esc_attr( (string) $calc_extra_product ); ?>"
						>
							<header class="system-konfigurator__kopf">
								<div>
									<span class="mono">02 · großer Funnel</span>
									<h3>Anfragesystem konfigurieren</h3>
								</div>
								<p>Eine eigenständige Produktstrecke ist enthalten. Jede weitere erweitert Landingpage, Qualifizierung und Kampagnenlogik.</p>
							</header>

							<fieldset class="produktwahl">
								<legend>Welche Produktstrecken sollen aufgebaut werden?</legend>
								<?php foreach ( $system_products as $product_index => $product ) : ?>
									<label class="produktwahl__option">
										<input type="checkbox"
											value="<?php echo esc_attr( $product['key'] ); ?>"
											data-system-product
											<?php echo 0 === $product_index ? 'checked' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static boolean attribute ?>
										>
										<span class="produktwahl__text">
											<strong><?php echo esc_html( $product['label'] ); ?></strong>
											<small><?php echo esc_html( $product['text'] ); ?></small>
										</span>
										<span class="produktwahl__preis"><?php echo 0 === $product_index ? 'inklusive' : '+' . esc_html( $extra_product_price ); ?></span>
									</label>
								<?php endforeach; ?>
							</fieldset>

							<div class="system-pfade" aria-hidden="true">
								<?php foreach ( $system_products as $product_index => $product ) : ?>
									<div class="system-pfad<?php echo 0 === $product_index ? ' ist-aktiv' : ''; ?>" data-system-path="<?php echo esc_attr( $product['key'] ); ?>">
										<span><?php echo esc_html( $product['label'] ); ?></span>
										<i></i><b>Landingpage</b><i></i><b>Qualifizierung</b><i></i><b>CRM + Messung</b>
									</div>
								<?php endforeach; ?>
							</div>

							<div class="system-konfigurator__summe">
								<div>
									<span class="mono">Ihre Konfiguration</span>
									<strong data-config-selection>Photovoltaik</strong>
								</div>
								<div class="system-konfigurator__preis">
									<span>einmalig ab</span>
									<strong class="zahl" data-config-price><?php echo esc_html( $foundation_price ); ?></strong>
									<small>netto · + rund <?php echo esc_html( $hosting_price ); ?>/Mon. Hosting</small>
								</div>
							</div>

							<div class="system-konfigurator__aktion">
								<p>Server-Side-Tracking ist im System enthalten — keine Zusatzoption. Werbebudget und laufende Kampagnenbetreuung sind separat.</p>
								<a class="tun" href="<?php echo esc_url( $system_contact_url ); ?>"
									data-system-configurator-cta
									data-base-href="<?php echo esc_url( add_query_arg( [ 'type' => 'project', 'focus' => 'energy_system' ], $contact_url ) ); ?>"
									data-track-action="cta_strecke_config_to_contact"
									data-track-category="lead_gen"
									data-track-section="einstieg"
								>Konfiguration anfragen <span class="pf" aria-hidden="true">→</span></a>
							</div>
						</article>
					</div>

					<div class="system-umfang" aria-label="Im Anfragesystem enthalten">
						<span class="mono">Immer enthalten</span>
						<div>
							<?php foreach ( $system_included as $included ) : ?>
								<p><b><?php echo esc_html( $included['k'] ); ?></b><span><?php echo esc_html( $included['v'] ); ?></span></p>
							<?php endforeach; ?>
						</div>
					</div>

					<span id="analyse" class="nur-vorlesen" aria-hidden="true"></span>
					<aside class="sofortkontakt-bridge" id="sofortkontakt">
						<div>
							<span class="mono">Spezialfall · vorhandene Leads</span>
							<h3>Sofortkontakt-Setup</h3>
							<p>Sie kaufen bereits Portal-Leads oder bekommen regelmäßig Anfragen? Dann ist nicht zwingend ein Neuaufbau der erste Hebel. Das Sofortkontakt-Setup verbindet Eingang, Alarm und Übergabe an den Vertrieb.</p>
						</div>
						<div class="sofortkontakt-bridge__aktion">
							<strong class="zahl"><?php echo esc_html( $setup_price ); ?></strong>
							<span>netto · separates Anfrageformular</span>
							<a class="textlink" href="<?php echo esc_url( $sofort_contact_url ); ?>"
								data-track-action="cta_strecke_sofort_to_contact"
								data-track-category="lead_gen"
								data-track-section="einstieg"
							>Sofortkontakt anfragen →</a>
						</div>
					</aside>
				</div>
			</div>
		</section>

		<!-- ════════ 05 Was es braucht ════════ -->
		<section id="anteil">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['anteil'] ); ?>
				<div class="voll">
					<span id="passung" aria-hidden="true"></span>
					<h2 class="kopf leise" id="anteil-titel">Passt ein eigener Anfrageweg zu Ihrem Betrieb?</h2>
					<p class="vorspann">Das System ist für ausführende Solar-, Wärmepumpen- und Speicherbetriebe mit eigenem Vertrieb gebaut. Drei Dinge müssen vorhanden sein: Zugriff auf die eigenen Konten, eine entscheidungsfähige Person und ein Vertrieb, der konsequent nachfasst.</p>
					<div class="bedingungen">
						<?php foreach ( $conditions as $condition_index => $condition ) : ?>
							<div>
								<span class="i" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $condition_index + 1 ) ); ?></span>
								<h3 id="<?php echo esc_attr( $condition['id'] ); ?>"><?php echo esc_html( $condition['titel'] ); ?></h3>
								<p><?php echo 'anteil-zugaenge' === $condition['id'] ? nexus_glossary_explain_text( $condition['text'], 'crm', 'CRM' ) : esc_html( $condition['text'] ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
					<h3 class="passung-untertitel" id="passung-titel">Lieber jetzt klären, ob es passt.</h3>
					<p class="vorspann">Unsicher bei einem Punkt? Beschreiben Sie Ihre Ausgangslage im Marktcheck.</p>

					<div class="passung">
						<div class="ja">
							<span class="mono">Passt</span>
							<ul>
								<?php foreach ( $fit_yes as $item ) : ?>
									<li>
										<b><?php echo esc_html( $item['t'] ); ?></b>
										<span><?php echo esc_html( $item['s'] ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<div>
							<span class="mono">Passt nicht</span>
							<ul>
								<?php foreach ( $fit_no as $item ) : ?>
									<li>
										<b><?php echo esc_html( $item['t'] ); ?></b>
										<span><?php echo esc_html( $item['s'] ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 06 Marktcheck ════════ -->
		<section id="marktcheck">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['marktcheck'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="marktcheck-titel">Marktcheck für <?php echo esc_html( $marketcheck_price ); ?>. Erst einordnen, dann investieren.</h2>
					<p class="vorspann">Der Marktcheck ist der kleine Einstieg vor einem größeren Aufbau: kurze Ausgangslage, persönliche Prüfung und eine schriftliche Empfehlung. Ihre Angaben erfassen Sie anschließend in einem separaten, kurzen Formular.</p>

					<div class="gate gate--handoff">
						<div>
							<span class="mono">Was geprüft wird</span>
							<ol>
								<?php foreach ( $report_items as $report_item ) : ?>
									<li><?php echo esc_html( $report_item ); ?></li>
								<?php endforeach; ?>
							</ol>
							<p class="hinweis"><?php echo esc_html( sprintf( '%d kurze Fit-Signale · etwa %d Minuten Intake · kein Pflicht-Call.', $marketcheck_fit_q, $marketcheck_mins ) ); ?></p>
						</div>

						<div class="schein tafel">
							<span class="mono">Marktcheck</span>
							<?php foreach ( $receipt_rows as $receipt_row ) : ?>
								<div class="z">
									<span><?php echo esc_html( $receipt_row['k'] ); ?></span>
									<b><?php echo esc_html( $receipt_row['v'] ); ?></b>
								</div>
							<?php endforeach; ?>
							<a class="tun" href="<?php echo esc_url( $marketcheck_contact_url ); ?>"
								data-track-action="cta_strecke_marktcheck_to_contact"
								data-track-category="lead_gen"
								data-track-section="marktcheck"
							>Marktcheck anfragen · <?php echo esc_html( $marketcheck_price ); ?> <span class="pf" aria-hidden="true">→</span></a>
							<p class="person">Ihre Angaben und die Einordnung bearbeitet <a class="textlink" href="<?php echo esc_url( $about_url ); ?>" data-track-action="cta_strecke_marktcheck_to_about" data-track-category="trust" data-track-section="marktcheck">Haşim Üner</a> persönlich.</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 07 Fragen ════════ -->
		<section id="fragen">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['fragen'] ); ?>
				<div class="haupt">
					<h2 class="kopf leise" id="fragen-titel">Was Sie vor einer Entscheidung wissen sollten.</h2>
					<p class="vorspann">Kosten, Risiko, Tracking, bestehende Website und Eigentum müssen vor einem fünfstelligen Aufbau geklärt sein. Die wichtigsten Antworten stehen hier; den Rest klären wir schriftlich im Marktcheck.</p>

					<div class="fragen">
						<?php foreach ( $faq_items as $faq_item ) : ?>
							<details<?php echo ! empty( $faq_item['open'] ) ? ' open' : ''; ?>>
								<summary id="<?php echo esc_attr( $faq_item['id'] ); ?>"><?php echo esc_html( $faq_item['q'] ); ?></summary>
								<div class="huelle">
									<div>
										<div class="antwort">
											<span class="erst"><?php echo esc_html( $faq_item['lead'] ); ?></span>
											<?php echo esc_html( $faq_item['rest'] ); ?>
										</div>
									</div>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="marg">
					<div class="note">
						<span class="label">Anmerkung</span>
						<b>Cost per Order</b> heißt: was ein gewonnener Auftrag kostet, nicht was ein Kontakt
						kostet. Beziehen Sie zusätzlich Marge und Vertriebsaufwand ein. Den Vergleich finden Sie in
						<a class="satzlink" href="#rechnung">Abschnitt 02</a>.
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 08 Verweise ════════ -->
		<section id="verweise">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['verweise'] ); ?>
				<div class="voll">
					<h2 class="kopf leise" id="verweise-titel">Nur die Vertiefungen, die eine Entscheidung verändern.</h2>
					<p class="vorspann"><?php echo esc_html( (string) count( $references ) ); ?> Fachseiten für Kosten, Lead-Kauf, Wärmepumpe, Gewerbe-PV und Messung. Kein vollständiges Inhaltsverzeichnis — nur die nächsten sinnvollen Wege.</p>

					<div class="verweise">
						<?php foreach ( $references as $reference_index => $reference ) : ?>
							<a href="<?php echo esc_url( $reference['url'] ); ?>"
								data-track-action="link_strecke_vertiefung"
								data-track-category="internal_link"
								data-track-section="verweise"
							>
								<span class="i" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $reference_index + 1 ) ); ?></span>
								<span class="f"><?php echo esc_html( $reference['f'] ); ?></span>
								<span class="z"><?php echo esc_html( $reference['z'] ); ?></span>
								<span class="p" aria-hidden="true">→</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ Abschluss ════════ -->
		<div class="abschluss">
			<div class="blatt">
				<div class="tafel reihe">
					<div class="haupt breit">
						<h2 id="abschluss">Klein prüfen oder direkt konfigurieren.</h2>
						<p class="aufriss">Für die erste Einordnung reicht der Marktcheck. Wenn der Aufbau bereits feststeht, konfigurieren Sie die benötigten Produktstrecken direkt — ohne Analyse-Stufe dazwischen.</p>
						<div class="ausgang">
							<a class="tun" href="<?php echo esc_url( $marketcheck_contact_url ); ?>"
								data-track-action="cta_strecke_abschluss_to_marktcheck"
								data-track-category="lead_gen"
								data-track-section="abschluss"
							>Marktcheck · <?php echo esc_html( $marketcheck_price ); ?> <span class="pf" aria-hidden="true">→</span></a>
							<a class="hero-nebenweg" href="#einstieg"
								data-track-action="cta_strecke_abschluss_to_system"
								data-track-category="lead_gen"
								data-track-section="abschluss"
							>Anfragesystem konfigurieren · ab <?php echo esc_html( $foundation_price ); ?> →</a>
						</div>
					</div>
					<div class="marg">
						<div class="protokoll">
							<?php foreach ( $protocol_rows as $protocol_row ) : ?>
								<div class="z">
									<span><?php echo esc_html( $protocol_row['k'] ); ?></span>
									<b><?php echo esc_html( $protocol_row['v'] ); ?></b>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<?php foreach ( $schema_blocks as $schema_block ) : ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $schema_block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<?php endforeach; ?>

<?php get_footer(); ?>
