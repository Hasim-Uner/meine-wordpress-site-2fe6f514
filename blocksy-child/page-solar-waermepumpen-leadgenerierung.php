<?php
/**
 * Template Name: Solar & Wärmepumpen Leadgenerierung (Anfragestrecke)
 * Description: Gutachten-Standard. Weisser Grund, Serif im Fliesstext,
 *              Randspalte mit laufender Nummer, dunkle Tafeln fuer
 *              Beweis und Handlung. Der Marktcheck ist das Gate und steht
 *              bewusst nach der Argumentation, nicht davor.
 *
 *              Aufbau: Dokumentkopf mit Definition und Messschrieb ·
 *              01 Strecke · 02 Ihr Anteil · 03 Rechnung · 04 Fall ·
 *              05 Einstieg · 06 Passung · 07 Marktcheck · 08 Fragen ·
 *              09 Verweise.
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

// Voreinstellungen des Vergleichsrechners. Bewusst vorsichtiger als der Fall
// und deshalb im Canon als eigene Kennzahl gefuehrt: als nacktes Literal im
// Template war die angesetzte Quote von der gemessenen Abschlussquote des
// Falls nicht zu unterscheiden.
$calc_cpl_display   = hu_e3_metric( 'calc_cpl_conservative' );
$calc_cpl_input     = hu_e3_metric( 'calc_cpl_conservative', 'input' );
$calc_quote_display = hu_e3_metric( 'calc_sales_conversion_conservative' );
$calc_quote_input   = hu_e3_metric( 'calc_sales_conversion_conservative', 'input' );

// Die Vorher-Quote ist eine Marktannahme, keine Messung dieses Betriebs.
// Deshalb die vorsichtige Fassung, sobald sie in einer Tabelle neben
// gemessenen Werten steht.
$e3_conv_before = $e3_metrics['sales_conversion_before']['display_hedged'] ?? 'einstellig';

// Zahlenwerte fuer den Messschrieb.
$e3_cpl_before_val = (int) ( $e3_metrics['cpl_before']['value'] ?? 150 );
$e3_cpl_after_val  = (int) ( $e3_metrics['cpl_after']['value'] ?? 22 );

// ── Preise (Pricing-Canon) ─────────────────────────────────────
// Erst die Rohwerte, dann die Anzeigeform daraus. Ein Fallback-Literal
// wie '14.900 €' waere eine zweite Preisquelle im Template — genau das,
// was scripts/lint-canon-drift.sh verhindert.
$pricing_canon = function_exists( 'hu_pricing_canon' ) ? hu_pricing_canon() : [];

// Rohwerte gehen zusaetzlich als data-Attribute ins Markup, damit das
// JavaScript den Aufbaupreis nicht ein zweites Mal kennen muss.
$calc_build   = (int) ( $pricing_canon['foundation_price_standard'] ?? 14900 );
$calc_hosting = (int) ( $pricing_canon['foundation_hosting_monthly'] ?? 50 );
$calc_months  = 24;

$format_eur = static function ( $value ) {
	return function_exists( 'hu_format_eur' )
		? hu_format_eur( $value )
		: number_format( (float) $value, 0, ',', '.' ) . ' €';
};

$foundation_price = $format_eur( $calc_build );
$hosting_price    = $format_eur( $calc_hosting );
$setup_price      = $format_eur( (int) ( $pricing_canon['entry_setup_price'] ?? 790 ) );
$analysis_price   = $format_eur( (int) ( $pricing_canon['analysis_price'] ?? 690 ) );
$entry_price      = $analysis_price;

// ── Marktcheck (Diagnose-Canon) ────────────────────────────────
$diagnose_canon    = function_exists( 'hu_diagnose_canon' ) ? hu_diagnose_canon() : [];
$analysis_days     = (int) ( $diagnose_canon['primary_days'] ?? 7 );
$marketcheck_reply = hu_marketcheck_reply_label();
$marketcheck_visible_steps = (int) ( $diagnose_canon['marketcheck_visible_steps'] ?? 2 );
$marketcheck_fit_q        = (int) ( $diagnose_canon['marketcheck_fit_questions'] ?? 4 );
$marketcheck_mins         = (int) ( $diagnose_canon['marketcheck_minutes'] ?? 2 );

// ── Kontakt (Messaging-Canon) ──────────────────────────────────
$contact_email = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
// Ausweichweg fuer den Marktcheck-Mount ohne JavaScript: ein echtes
// Formular auf /kontakt/, kein mailto. Siehe Abschnitt 07.
$contact_url = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );

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
	[ 'nr' => '01', 'id' => 'strecke',    'titel' => 'Die Strecke',  'kurz' => 'Strecke' ],
	[ 'nr' => '02', 'id' => 'anteil',     'titel' => 'Ihr Anteil',   'kurz' => 'Ihr Anteil' ],
	[ 'nr' => '03', 'id' => 'rechnung',   'titel' => 'Die Rechnung', 'kurz' => 'Rechnung' ],
	[ 'nr' => '04', 'id' => 'ergebnisse', 'titel' => 'Der Fall',     'kurz' => 'Fall' ],
	[ 'nr' => '05', 'id' => 'einstieg',   'titel' => 'Ihr Einstieg', 'kurz' => 'Einstieg' ],
	[ 'nr' => '06', 'id' => 'passung',    'titel' => 'Passung',      'kurz' => 'Passung' ],
	[ 'nr' => '07', 'id' => 'marktcheck', 'titel' => 'Marktcheck',   'kurz' => 'Marktcheck' ],
	[ 'nr' => '08', 'id' => 'fragen',     'titel' => 'Fragen',       'kurz' => 'Fragen' ],
	[ 'nr' => '09', 'id' => 'verweise',   'titel' => 'Verweise',     'kurz' => 'Verweise' ],
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

// ── 01 Die Strecke: fuenf Stationen ────────────────────────────
// Je Station: was gebaut wird, was davon auf dem Schreibtisch ankommt,
// und welchen Nutzen das fuer den Vertrieb hat.
$stations = [
	[
		'id' => 'station-sichtbarkeit',
		'titel' => 'Landingpages und Kampagnen',
		'bau' => 'Ich entwickle Seiten für Ihr Angebot und Zielgebiet, richte Kampagnen aus und teste Anzeigenvarianten. Technisches SEO ergänzt die bezahlte Reichweite.',
		'sicht' => 'Interessenten lernen Ihren Betrieb kennen und fragen direkt bei Ihnen an.',
	],
	[
		'id' => 'station-vorqualifizierung',
		'titel' => 'Formulare mit Vorqualifizierung',
		'bau' => 'Ich stimme die Fragen auf Ihre Projekte ab: Produktinteresse, Standort, Objekt und Zeithorizont. Die Angaben werden strukturiert übergeben.',
		'sicht' => 'Ihr Vertrieb kann Anfragen einordnen und das erste Gespräch vorbereiten.',
	],
	[
		'id' => 'station-messung',
		'titel' => 'Tracking und Quellenzuordnung',
		'bau' => 'Ich verbinde Browser-, Plattform- und CRM-Signale über serverseitiges Tracking. Einwilligungen und verfügbare Identifikatoren begrenzen die Zuordnung.',
		'sicht' => 'Sie erkennen, welche Quellen Anfragen und nachvollziehbare Vertriebsresultate liefern.',
	],
	[
		'id' => 'station-vertrieb',
		'titel' => 'CRM und Rückmeldung',
		'bau' => 'Ich richte die CRM-Übergabe nach Produktinteresse und Herkunft ein, dazu Benachrichtigung und Eingangsbestätigung. Zuständigkeiten stimmen wir mit Ihrem Vertrieb ab.',
		'sicht' => 'Anfragen landen beim zuständigen Ansprechpartner und lassen sich nachverfolgen.',
	],
	[
		'id' => 'station-eigentum',
		'titel' => 'Dokumentation und Übergabe',
		'bau' => 'Ich arbeite auf Ihren Konten und dokumentiere Code, Tracking und Anbindungen. Sie erhalten eine Übersicht der Zugänge und Komponenten.',
		'sicht' => 'Ihr Betrieb behält die Kontrolle und kann die Betreuung später übergeben.',
	],
];

// ── 02 Ihr Anteil ──────────────────────────────────────────────
// Steht bewusst vor dem Preis. Wer diese drei Punkte nicht liefern
// kann, soll vor der Rechnung aussteigen, nicht nach dem Angebot.
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

// ── 04 Der Fall: Projektmonate inklusive Vorbereitung ──────────
$phases = [
	[ 'label' => $e3_timeline['preparation_label'], 'text' => $e3_timeline['preparation'] ],
	[ 'label' => $e3_timeline['campaign_label'], 'text' => $e3_timeline['campaign'] ],
	[ 'label' => $e3_timeline['optimization_label'], 'text' => $e3_timeline['optimization'] . ' ' . $e3_timeline['followup'] ],
];


// ── 05 Die Leiter ──────────────────────────────────────────────
// Vier Stufen, jede einzeln buchbar. Der Preis steht an der Stufe,
// nicht in einer Preisliste am Seitenende.
$ladder = [
	[
		'id'    => 'stufe-marktcheck',
		'titel' => 'Marktcheck',
		'takt'  => sprintf( '%d Minuten · Befund %s', $marketcheck_mins, $marketcheck_reply ),
		'text'  => sprintf( '%d Fit-Fragen plus Kontaktdaten in %d Schritten. Danach lese ich Ihre Antworten selbst und schreibe zurück, ob ein eigener Anfrageweg bei Ihnen wirtschaftlich trägt. Auch wenn die Antwort nein ist — dann mit drei Hebeln, die ohne mich funktionieren.', $marketcheck_fit_q, $marketcheck_visible_steps ),
		'preis' => '0 €',
		'note'  => 'kostenlos',
	],
	[
		'id'    => 'stufe-analyse',
		'titel' => 'Anfragesystem-Analyse',
		'takt'  => sprintf( '%d Werktage · wird angerechnet', $analysis_days ),
		'text'  => 'Anfragequellen, Tracking, Funnel und Vertriebsanschluss als schriftlicher Befund mit drei priorisierten Hebeln und einer Wirtschaftlichkeits-Einordnung. Bei Umsetzung zahlen Sie sie nicht doppelt.',
		'preis' => $analysis_price,
		'note'  => 'netto · anrechenbar',
	],
	[
		'id'    => 'stufe-sofortkontakt',
		'titel' => 'Sofortkontakt-Setup',
		'takt'  => sprintf( '%d Werktage · keine Mindestlaufzeit · auch einzeln buchbar', (int) ( $pricing_canon['entry_setup_business_days'] ?? 5 ) ),
		'text'  => 'Wirkt auf die Anfragen, die Sie heute schon haben — auch auf gekaufte Leads von Aroundhome, DAA oder Wattfox. Alarm unter 60 Sekunden, automatische Eingangsbestätigung mit Terminlink, alle Quellen in einer Übersicht. Sobald ausreichend Abschlüsse erfasst sind, lassen sich die Anfragequellen wirtschaftlich vergleichen.',
		'preis' => $setup_price,
		'note'  => 'netto · einmalig',
	],
	[
		'id'    => 'stufe-aufbau',
		'titel' => 'Aufbau der Anfragestrecke',
		'takt'  => 'nach Befund · Übergabe dokumentiert',
		'text'  => 'Die fünf Stationen aus Abschnitt 01, gebaut auf Ihren Zugängen. Danach optional laufende Weiterentwicklung mit wöchentlichem Reporting — monatlich kündbar, keine Rufbereitschaft.',
		'preis' => $foundation_price,
		'note'  => sprintf( 'netto + ~%s/Mon. Hosting', $hosting_price ),
	],
];

$exits = [
	[
		'titel' => 'Nach dem Marktcheck.',
		'text'  => 'Wenn ich absage, kostet Sie das nichts und Sie behalten die drei Hebel. Eine Absage begründe ich anhand Ihrer Ausgangslage.',
	],
	[
		'titel' => 'Nach der Analyse.',
		'text'  => 'Der Befund gehört Ihnen, auch wenn Sie nicht weiterarbeiten. Er ist so geschrieben, dass ein anderer Dienstleister damit arbeiten kann.',
	],
	[
		'titel' => 'Während des Aufbaus.',
		'text'  => 'Jede fertige Komponente läuft auf Ihren Konten und ist dokumentiert. Ein Abbruch hinterlässt kein totes System, sondern die Teile, die bis dahin stehen — bedienbar ohne mich.',
	],
	[
		'titel' => 'Danach.',
		'text'  => 'Die Weiterentwicklung ist monatlich kündbar. Was ich nicht zusage: Rufbereitschaft, eine Reaktionszeit im Störfall oder ein garantiertes Ergebnis. Dafür wäre eine gesonderte Betreuung nötig.',
	],
];

// ── 06 Passung ─────────────────────────────────────────────────
$fit_yes = [
	[ 't' => 'Projektwerte ab ca. 15.000 € privat, 50.000 € gewerblich', 's' => 'Richtwerte für die Einordnung; entscheidend sind Marge, Kapazität und Zielgebiet.' ],
	[ 't' => 'Eigener Vertrieb, der abschließt', 's' => 'Ihr Team oder die Geschäftsführung — jemand, der zurückruft und nachfasst.' ],
	[ 't' => 'Definiertes Zielgebiet', 's' => 'Region oder Bundesland. Nicht „bundesweit, alles“.' ],
	[ 't' => 'Horizont 12 bis 24 Monate', 's' => 'Bereit, Anfragegewinnung über mehrere Monate aufzubauen und zu verbessern.' ],
];
$fit_no = [
	[ 't' => '„Nächste Woche brauchen wir Leads.“', 's' => 'Ein Neuaufbau ist keine Sofortversorgung mit Kontakten.' ],
	[ 't' => 'Reines Vermittlungsgeschäft', 's' => 'Dieses Angebot ist auf ausführende Installationsbetriebe ausgerichtet.' ],
	[ 't' => 'Kein Vertriebsprozess', 's' => 'Anfragen sterben, wenn niemand konsequent qualifiziert und nachfasst.' ],
	[ 't' => 'Sichtbarkeit nicht gewollt', 's' => 'Der eigene Anfrageweg lebt davon, dass Ihr Betrieb unterscheidbar wird.' ],
];

// ── 07 Marktcheck: was im Befund steht ─────────────────────────
$report_items = [
	'Erste Einordnung Ihres Betriebs und Zielgebiets',
	'Einschätzung anhand Ihrer Projektgröße und Vertriebsstruktur',
	'Drei priorisierte Ansatzpunkte — auch wenn keine Zusammenarbeit passt',
	'Eine klare Empfehlung: jetzt, später oder gar nicht',
];

$receipt_rows = [
	[ 'k' => 'Form',          'v' => 'Befund per E-Mail' ],
	[ 'k' => 'Frist',         'v' => $marketcheck_reply ],
	// "Geprüft von" statt "Geprüft" brach im schmalen Panel auf zwei Zeilen
	// um, ohne etwas hinzuzufuegen: der Name daneben sagt das "von" schon.
	[ 'k' => 'Geprüft',       'v' => 'Haşim Üner, persönlich' ],
	[ 'k' => 'Auch bei Nein', 'v' => 'drei priorisierte Hebel' ],
	[ 'k' => 'Kosten',        'v' => 'keine' ],
];

// ── 08 Fragen ──────────────────────────────────────────────────
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
		'lead' => sprintf( 'Der Aufbau einer eigenen Anfragestrecke liegt bei %s netto einmalig plus rund %s Hosting im Monat; das Werbebudget kommt separat hinzu und bleibt auf dem Konto des Betriebs.', $foundation_price, $hosting_price ),
		'rest' => sprintf( 'Zwei kleinere Stufen davor: die Anfragesystem-Analyse für %s netto, die bei Umsetzung angerechnet wird, und das Sofortkontakt-Setup für %s netto, das auf bereits vorhandene Anfragen wirkt.', $analysis_price, $entry_price ),
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
		'rest' => 'Die Analyse klärt, welche Bausteine fehlen oder überarbeitet werden müssen. Ein vollständiger Neuaufbau ist keine automatische Voraussetzung.',
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

// ── 09 Verweise ────────────────────────────────────────────────
// Nach der Frage sortiert, die dahintersteckt — nicht nach Kategorie.
// Wer sucht, sucht eine Antwort, keine Rubrik.
$references = [
	[ 'f' => 'Was kosten Solar-Leads am Markt?',              'z' => 'Marktstudie DACH, Preise je Modell',        'url' => home_url( '/solar-leads-kosten-studie/' ) ],
	[ 'f' => 'Soll ich PV-Leads überhaupt kaufen?',           'z' => 'Einordnung der Anbieter und die Alternative', 'url' => home_url( '/solar-leads-kaufen-alternative/' ) ],
	[ 'f' => 'Was ist ein realistischer CPL in Photovoltaik?', 'z' => 'Drei Szenarien, versteckte Kostentreiber',   'url' => home_url( '/cost-per-lead-photovoltaik/' ) ],
	[ 'f' => 'Woran erkenne ich eine qualifizierte PV-Anfrage?', 'z' => 'Vier Merkmale plus Warnsignale',          'url' => home_url( '/qualifizierte-pv-anfragen/' ) ],
	[ 'f' => 'Portal oder eigenes System über 24 Monate?',    'z' => 'Vergleich über acht Kriterien',              'url' => home_url( '/eigene-leadgenerierung-vs-portale/' ) ],
	[ 'f' => 'Wie ist ein Solar-Funnel aufgebaut?',           'z' => 'Fünf Stufen einer belastbaren Architektur',  'url' => home_url( '/lead-funnel-solar/' ) ],
	[ 'f' => 'Wie funktioniert Server-Side-Tracking im B2B?', 'z' => 'GA4, Meta CAPI, Consent Mode v2',            'url' => $tracking_url ],
	[ 'f' => 'Was gilt bei Wärmepumpen-Leads anders?',        'z' => 'Marktmodelle und CPL im Heizungstausch',     'url' => home_url( '/waermepumpen-leads/' ) ],
	[ 'f' => 'Wie läuft gewerbliche PV-Leadgenerierung?',     'z' => 'Buying-Center-Funnel im B2B',                'url' => home_url( '/b2b-solar-leads/' ) ],
	[ 'f' => 'Wie gewinnen Solarteure systematisch Kunden?',  'z' => 'Fünf Hebel im DACH-Mittelstand',             'url' => home_url( '/kunden-gewinnen-solarteure/' ) ],
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
			'name'          => 'Marktcheck',
			'price'         => '0',
			'priceCurrency' => 'EUR',
			'description'   => sprintf( '%d Fit-Fragen plus Kontaktdaten in %d Schritten, etwa %d Minuten. Danach ein händisch geprüfter schriftlicher Befund zu Betrieb und Region per E-Mail — %s.', $marketcheck_fit_q, $marketcheck_visible_steps, $marketcheck_mins, $marketcheck_reply ),
			'availability'  => 'https://schema.org/InStock',
		],
		[
			'@type'         => 'Offer',
			'name'          => 'Anfragesystem-Analyse',
			'price'         => (string) ( $pricing_canon['analysis_price'] ?? 690 ),
			'priceCurrency' => 'EUR',
			'description'   => sprintf( 'Schriftlicher Befund zu Anfragequellen, Tracking, Funnel und Vertriebsanschluss in %d Werktagen, mit drei priorisierten Hebeln. Wird bei Umsetzung auf den Aufbau angerechnet. Netto.', $analysis_days ),
			'availability'  => 'https://schema.org/InStock',
		],
		[
			'@type'         => 'Offer',
			'name'          => 'Sofortkontakt-Setup',
			'price'         => (string) ( $pricing_canon['entry_setup_price'] ?? 790 ),
			'priceCurrency' => 'EUR',
			'description'   => 'Alarm unter 60 Sekunden, automatische Eingangsbestätigung mit Terminlink und eine Übersicht aller Anfragen nach Quelle. Wirkt auch auf gekaufte Portal-Leads. Netto, einmalig.',
			'availability'  => 'https://schema.org/InStock',
		],
		[
			'@type'         => 'Offer',
			'name'          => 'Aufbau der Anfragestrecke',
			'price'         => (string) $calc_build,
			'priceCurrency' => 'EUR',
			'description'   => sprintf( 'Aufbau der fünf Stationen auf den Zugängen des Betriebs. Netto, einmalig, zuzüglich rund %s Hosting im Monat.', $hosting_price ),
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

// 2 · HowTo — die fuenf Stationen als Ablauf
$schema_blocks[] = [
	'@context'    => 'https://schema.org',
	'@type'       => 'HowTo',
	'@id'         => $page_url . '#howto',
	'name'        => 'Eigene Photovoltaik-Anfragen aufbauen statt Leads kaufen',
	'description' => 'Die fünf Stationen, die eine Anfrage im eigenen Anfragesystem durchläuft — von der Sichtbarkeit auf der eigenen Domain bis zur dokumentierten Übergabe.',
	'totalTime'   => 'P10W',
	'estimatedCost' => [
		'@type'    => 'MonetaryAmount',
		'currency' => 'EUR',
		'value'    => (string) $calc_build,
	],
	'step'        => array_values(
		array_map(
			static function ( $index, $station ) use ( $page_url ) {
				return [
					'@type' => 'HowToStep',
					'position' => $index + 1,
					'name'  => $station['titel'],
					'text'  => $station['bau'],
					'url'   => $page_url . '#' . $station['id'],
				];
			},
			array_keys( $stations ),
			$stations
		)
	),
];

// 3 · DefinedTerm — der Begriff, den diese Seite besetzt
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
	// .solara-landing bleibt als Wurzel stehen: solar-leadgenerierung-solara.js
	// haengt seinen Marktcheck-Mount und den Ankerhandler daran. Alle uebrigen
	// Setups dieser Datei greifen ins Leere und tun still nichts.
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
					<p class="gegenstand">Gegenstand · Anfragegewinnung für Photovoltaik, Wärmepumpe und Speicher</p>

					<h1>Eigene Anfragen für <em>Solar und Wärmepumpe.</em></h1>

					<p class="aufriss">
						<span class="erst">Für Solar- und SHK-Betriebe mit eigenem Vertrieb.</span>
						Ich entwickle Landingpages, optimiere Kampagnen und verbinde Formulare,
						Tracking und CRM. So kommen Anfragen mit Produktinteresse und Herkunft
						bei Ihrem Vertrieb an. Code, Konten und Daten bleiben bei Ihnen.
					</p>

					<div class="ausgang">
						<a class="tun" href="#marktcheck"
							data-track-action="cta_strecke_kopf_to_marktcheck"
							data-track-category="lead_gen"
							data-track-section="dokumentkopf"
						>Kostenlosen Marktcheck starten <span class="pf" aria-hidden="true">→</span></a>
						<a class="tun still" href="#strecke"
							data-track-action="cta_strecke_kopf_to_stationen"
							data-track-category="navigation"
							data-track-section="dokumentkopf"
						>Was ich für Sie umsetze</a>
					</div>

					<div class="meta">
						<dl>
							<div>
								<dt>Für wen</dt>
								<dd>Solar- und SHK-Betriebe mit eigenem Vertrieb</dd>
							</div>
							<div>
								<dt>Einstieg</dt>
								<dd>Marktcheck, kostenlos · Befund <?php echo esc_html( $marketcheck_reply ); ?></dd>
							</div>
							<div>
								<dt>Aufbau</dt>
								<dd><span class="zahl"><?php echo esc_html( $foundation_price ); ?></span> netto · kleiner Einstieg ab <span class="zahl"><?php echo esc_html( $entry_price ); ?></span></dd>
							</div>
							<div>
								<dt>Bearbeitet von</dt>
								<dd>Haşim Üner · Strategie und Umsetzung persönlich</dd>
							</div>
						</dl>
					</div>

				</div>
			</div>

			<!-- Messschrieb: der eigene, gemessene Fall -->
			<div class="reihe schrieb">
				<div class="haupt breit tafel">
					<div class="kopfzeile">
						<h2 id="messschrieb">Kosten pro qualifizierter Anfrage</h2>
						<span class="mono">Dokumentierter Fall · PV-Mittelstand · <?php echo esc_html( $e3_timeframe ); ?></span>
					</div>

					<?php
					// Zwei Vergleichswerte, keine erfundenen monatlichen Zwischenpunkte.
					$e3_after_y = 114 - ( 96 * $e3_cpl_after_val / max( 1, $e3_cpl_before_val ) );
					?>
					<svg viewBox="0 -22 620 192" role="img" aria-label="<?php echo esc_attr( $e3_timeline['comparison'] ); ?>">
						<line x1="46" y1="114" x2="612" y2="114" stroke="var(--strich)" stroke-width="1" />
						<g stroke="var(--stempel)" stroke-width="24">
							<line x1="140" y1="114" x2="140" y2="18" />
							<line x1="470" y1="114" x2="470" y2="<?php echo esc_attr( (string) $e3_after_y ); ?>" />
						</g>
						<g class="marke" fill="var(--tinte)" font-family="IBM Plex Mono, monospace" font-size="11" font-weight="500" text-anchor="middle">
							<text x="140" y="12"><?php echo esc_html( $e3_cpl_before ); ?></text>
							<text x="470" y="<?php echo esc_attr( (string) ( $e3_after_y - 11 ) ); ?>"><?php echo esc_html( $e3_cpl_after ); ?></text>
						</g>
						<g class="zeit" fill="var(--matt)" font-family="IBM Plex Mono, monospace" font-size="8.5" text-anchor="middle">
							<text x="140" y="150">PORTAL · VORHER</text>
							<text x="470" y="150">EIGENE · ERREICHT</text>
						</g>
					</svg>
					<p><?php echo esc_html( $e3_timeline['comparison'] ); ?></p>

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
					<h2 class="kopf" id="strecke-titel">Was ich für Ihre Anfragegewinnung umsetze.</h2>
					<p class="vorspann">Von der ersten Anzeige bis zur CRM-Übergabe: Ich übernehme die technische Umsetzung und die Optimierung. Ihr Vertrieb übernimmt Beratung, Angebot und Abschluss.</p>

					<div class="strecke">
						<?php foreach ( $stations as $station_index => $station ) : ?>
							<article class="station" id="<?php echo esc_attr( $station['id'] ); ?>">
								<span class="i" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $station_index + 1 ) ); ?></span>
								<div>
									<h3 id="<?php echo esc_attr( $station['id'] . '-titel' ); ?>"><?php echo esc_html( $station['titel'] ); ?></h3>
									<p class="bau"><?php echo esc_html( $station['bau'] ); ?></p>
								</div>
								<div class="sicht">
									<span class="l">Für Ihren Vertrieb</span>
									<p><?php echo esc_html( $station['sicht'] ); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 02 Ihr Anteil ════════ -->
		<section id="anteil">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['anteil'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="anteil-titel">Drei Dinge müssen bei Ihnen passieren.</h2>
					<p class="vorspann">Damit die Umsetzung vorankommt, brauche ich Zugänge, einen festen Ansprechpartner und einen Vertrieb, der Anfragen bearbeitet.</p>

					<div class="bedingungen">
						<?php foreach ( $conditions as $condition_index => $condition ) : ?>
							<div>
								<span class="i" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $condition_index + 1 ) ); ?></span>
								<h3 id="<?php echo esc_attr( $condition['id'] ); ?>"><?php echo esc_html( $condition['titel'] ); ?></h3>
								<p><?php echo esc_html( $condition['text'] ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 03 Die Rechnung ════════ -->
		<section id="rechnung">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['rechnung'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="rechnung-titel">Nicht pro Anfrage rechnen. Pro Auftrag.</h2>
					<p class="vorspann">Vergleichen Sie beide Wege mit Ihren eigenen Annahmen. Entscheidend sind Anfragekosten, Abschlussquote und laufender Aufwand. Die vorbelegten Werte sind Rechenbeispiele, keine Prognose für Ihren Betrieb.</p>

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
									<input id="strecke-a1" data-feld="a1" type="number" inputmode="numeric" min="0" max="500" step="1" value="25">
									<span class="einheit">Stk</span>
								</span>
							</div>
							<div class="eingabe">
								<label for="strecke-a2">Preis pro Anfrage</label>
								<span class="feld">
									<input id="strecke-a2" data-feld="a2" type="number" inputmode="numeric" min="0" max="1000" step="1" value="80">
									<span class="einheit">€</span>
								</span>
							</div>
							<div class="eingabe">
								<label for="strecke-a3">Abschlussquote auf diese Anfragen</label>
								<span class="feld">
									<input id="strecke-a3" data-feld="a3" type="number" inputmode="decimal" min="0" max="100" step="0.5" value="4">
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
						</div>

						<div class="weg b">
							<span class="mono">Weg B · Eigene Strecke</span>

							<div class="eingabe">
								<label for="strecke-b1">Werbebudget pro Monat</label>
								<span class="feld">
									<input id="strecke-b1" data-feld="b1" type="number" inputmode="numeric" min="0" max="50000" step="100" value="2000">
									<span class="einheit">€</span>
								</span>
							</div>
							<div class="eingabe">
								<label for="strecke-b2">Kosten pro Anfrage, die Sie ansetzen</label>
								<span class="feld">
									<input id="strecke-b2" data-feld="b2" type="number" inputmode="numeric" min="1" max="1000" step="1" value="<?php echo esc_attr( $calc_cpl_input ); ?>">
									<span class="einheit">€</span>
								</span>
							</div>
							<div class="eingabe">
								<label for="strecke-b3">Abschlussquote auf vorqualifizierte Anfragen</label>
								<span class="feld">
									<input id="strecke-b3" data-feld="b3" type="number" inputmode="decimal" min="0" max="100" step="0.5" value="<?php echo esc_attr( $calc_quote_input ); ?>">
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
					</div>

					<p class="fuss">
						<b>Enthalten:</b> In Weg B sind <?php echo esc_html( $foundation_price ); ?> Aufbau auf
						<?php echo esc_html( (string) $calc_months ); ?> Monate verteilt, rund
						<?php echo esc_html( $hosting_price ); ?> Hosting monatlich und Ihr Werbebudget.
						Vertriebszeit und laufende Betreuung sind nicht eingerechnet.
						Die Voreinstellungen von <?php echo esc_html( $calc_cpl_display ); ?> pro Anfrage und
						<?php echo esc_html( $calc_quote_display ); ?> Abschlussquote sind Rechenannahmen.
						Kosten und Quote müssen sich auf dieselbe Leadmenge beziehen.
					</p>
				</div>
			</div>
		</section>

		<!-- ════════ 04 Der Fall ════════ -->
		<section id="ergebnisse">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['ergebnisse'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="fall"><?php echo esc_html( $e3_timeframe ); ?>, ein Betrieb, drei Phasen.</h2>
					<p class="vorspann">
						Ein <?php echo esc_html( $e3_case_label ); ?> in DACH.
						Die Projektmonate zählen ab Beginn der Vorbereitung, nicht ab Kampagnenstart.
						Die Entwicklung beruht auf dem Zusammenspiel der Maßnahmen; der isolierte Beitrag des Trackings ist nicht gemessen.
					</p>

					<div class="phasen">
						<?php foreach ( $phases as $phase ) : ?>
							<div>
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

		<!-- ════════ 05 Die Leiter ════════ -->
		<section id="einstieg">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['einstieg'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="leiter">Welcher Einstieg zu Ihrer Ausgangslage passt.</h2>
					<p class="vorspann">Der kostenlose Marktcheck klärt den Bedarf. Danach entscheiden Sie, ob eine vertiefte Analyse, ein einzelnes Setup oder der Aufbau sinnvoll ist. Es gibt keine Pflicht, alle Stufen zu buchen.</p>

					<div class="leiter">
						<?php foreach ( $ladder as $rung_index => $rung ) : ?>
							<article class="stufe">
								<span class="i" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $rung_index + 1 ) ); ?></span>
								<div>
									<h3 id="<?php echo esc_attr( $rung['id'] ); ?>"><?php echo esc_html( $rung['titel'] ); ?></h3>
									<span class="takt"><?php echo esc_html( $rung['takt'] ); ?></span>
								</div>
								<p><?php echo esc_html( $rung['text'] ); ?></p>
								<div class="preis">
									<span class="p zahl"><?php echo esc_html( $rung['preis'] ); ?></span>
									<span class="n"><?php echo esc_html( $rung['note'] ); ?></span>
								</div>
							</article>
						<?php endforeach; ?>
					</div>

					<div class="ausstieg">
						<span class="mono">Und wenn es nicht funktioniert?</span>
						<div class="ag">
							<?php foreach ( $exits as $exit ) : ?>
								<p>
									<b><?php echo esc_html( $exit['titel'] ); ?></b>
									<?php echo esc_html( $exit['text'] ); ?>
								</p>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 06 Passung ════════ -->
		<section id="passung">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['passung'] ); ?>
				<div class="voll">
					<h2 class="kopf leise" id="passung-titel">Lieber jetzt klären, ob es passt.</h2>
					<p class="vorspann">Das Angebot richtet sich an Installationsbetriebe, die eigene Anfragen gewinnen und selbst bearbeiten. Unsicher bei einem Punkt? Beschreiben Sie Ihre Ausgangslage im Marktcheck.</p>

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

		<!-- ════════ 07 Marktcheck ════════ -->
		<section id="marktcheck">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['marktcheck'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="marktcheck-titel">So geht es nach Ihrer Anfrage weiter.</h2>
					<p class="vorspann">Beantworten Sie <?php echo esc_html( (string) $marketcheck_fit_q ); ?> kurze Fragen und hinterlassen Sie Ihre Kontaktdaten. Ich prüfe Ihre Angaben persönlich und sende den Befund <?php echo esc_html( $marketcheck_reply ); ?> per E-Mail. Sie entscheiden danach, ob wir weiterarbeiten. Kein Pflichtgespräch, keine Buchung durch das Absenden.</p>

					<div class="gate">
						<div>
							<span class="mono">Was im Befund steht</span>
							<ol>
								<?php foreach ( $report_items as $report_item ) : ?>
									<li><?php echo esc_html( $report_item ); ?></li>
								<?php endforeach; ?>
							</ol>
							<p class="hinweis">
								Gefragt wird nach Leistungsfokus, Projekt-Fit, Vertriebsverantwortung,
								Umsetzungshorizont und geschäftlichen Eckdaten.
								<?php echo esc_html( sprintf( '%d Fit-Fragen · %d sichtbare Schritte · etwa %d Minuten.', $marketcheck_fit_q, $marketcheck_visible_steps, $marketcheck_mins ) ); ?>
							</p>
						</div>

						<div class="schein tafel">
							<?php
							// Der Beleg steht ausserhalb des Mounts. Was Sie bekommen,
							// aendert sich nicht dadurch, dass das Formular laedt — und
							// solar-leadgenerierung-solara.js raeumt beim Mounten alles
							// weg, was im Mount steht.
							?>
							<span class="mono">Was Sie bekommen</span>
							<?php foreach ( $receipt_rows as $receipt_row ) : ?>
								<div class="z">
									<span><?php echo esc_html( $receipt_row['k'] ); ?></span>
									<b><?php echo esc_html( $receipt_row['v'] ); ?></b>
								</div>
							<?php endforeach; ?>

							<?php
							// Marktcheck-Mount. Das Skript ersetzt den Inhalt dieses
							// Knotens durch die mehrstufige Sequenz. Was hier steht,
							// ist der Zustand ohne JavaScript und muss fuer sich allein
							// einen gangbaren Kontaktweg anbieten.
							//
							// Kein mailto: der Ausweichweg fuehrt auf das
							// serverseitig gerenderte Formular auf /kontakt/. Die
							// Adresse bleibt als Text lesbar, aber nicht als Ziel
							// eines CTA, der "Marktcheck starten" verspricht.
							?>
							<div data-sol-quiz id="sol-quiz-mount">
								<h3 id="sol-quiz-title" class="nur-vorlesen">Marktcheck für Ihren Vertrieb starten</h3>
								<p class="klein">
									Der Marktcheck läuft über ein mehrstufiges Formular und braucht JavaScript.
								</p>
								<a class="tun" href="<?php echo esc_url( $contact_url ); ?>"
									data-track-action="cta_strecke_marktcheck_fallback_kontakt"
									data-track-category="lead_gen"
									data-track-section="marktcheck"
									data-track-funnel-stage="intake_open"
								>Über das Kontaktformular anfragen <span class="pf" aria-hidden="true">→</span></a>
								<p class="klein">
									Keine Zahlungsdaten · kein Pflicht-Call<br>
									<?php echo esc_html( $contact_email ); ?>
								</p>
							</div>

							<?php
							// Person-Signal bleibt ausserhalb des Mounts, damit es beim
							// Mounten nicht mit dem Ausgangszustand weggeraeumt wird.
							?>
							<p class="person">
								Ihre Angaben liest
								<a class="textlink" href="<?php echo esc_url( $about_url ); ?>"
									data-track-action="cta_strecke_marktcheck_to_about"
									data-track-category="trust"
									data-track-section="marktcheck"
								>Haşim Üner</a>
								persönlich — kein automatischer Standardbericht.
							</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 08 Fragen ════════ -->
		<section id="fragen">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['fragen'] ); ?>
				<div class="haupt">
					<h2 class="kopf leise" id="fragen-titel">Bevor Sie fragen.</h2>
					<p class="vorspann">
						Was hier nicht beantwortet wird, klären wir im Marktcheck — schriftlich, ohne
						Verkaufsgespräch.
					</p>

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
						<a class="satzlink" href="#rechnung">Abschnitt 03</a>.
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 09 Verweise ════════ -->
		<section id="verweise">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['verweise'] ); ?>
				<div class="voll">
					<h2 class="kopf leise" id="verweise-titel">Einzelne Fragen vertiefen.</h2>
					<p class="vorspann">
						<?php echo esc_html( (string) count( $references ) ); ?> Seiten zu Strategie,
						Lead-Qualität, Funnel-Architektur und Markteinordnung — nach der Frage sortiert,
						die dahintersteckt. Jede ist unabhängig lesbar.
					</p>

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
						<h2 id="abschluss">Passt ein eigener Anfrageweg zu Ihrem Betrieb?</h2>
						<p class="aufriss">Starten Sie mit dem kostenlosen Marktcheck. Sie erhalten meine schriftliche Einschätzung und einen konkreten nächsten Schritt.</p>
						<div class="ausgang">
							<a class="tun" href="#marktcheck"
								data-track-action="cta_strecke_abschluss_to_marktcheck"
								data-track-category="lead_gen"
								data-track-section="abschluss"
							>Kostenlosen Marktcheck starten <span class="pf" aria-hidden="true">→</span></a>
							<a class="tun still" href="#einstieg"
								data-track-action="cta_strecke_abschluss_to_leiter"
								data-track-category="offer"
								data-track-section="abschluss"
							>Einstieg ab <?php echo esc_html( $entry_price ); ?></a>
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
