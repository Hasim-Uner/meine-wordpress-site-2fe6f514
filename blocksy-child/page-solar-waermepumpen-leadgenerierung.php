<?php
/**
 * Template Name: Solar & Wärmepumpen Leadgenerierung (Anfragestrecke)
 * Description: Gutachten-Standard. Weisser Grund, Serif im Fliesstext,
 *              Randspalte mit laufender Nummer, vier dunkle Tafeln fuer
 *              Beweis und Handlung. Der Marktcheck ist das Gate und steht
 *              bewusst nach der Argumentation, nicht davor.
 *
 *              Aufbau: Dokumentkopf mit Definition und Messschrieb ·
 *              01 Strecke · 02 Ihr Anteil · 03 Rechnung · 04 Fall ·
 *              05 Leiter · 06 Passung · 07 Marktcheck · 08 Fragen ·
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

$e3_lead_count    = $e3_metrics['lead_count']['display'] ?? '1.750+';
$e3_sales_conv    = $e3_metrics['sales_conversion']['display'] ?? '15 %';
$e3_lead_conv     = $e3_metrics['lead_conversion']['display'] ?? '12 %';
$e3_cpl_reduction = $e3_metrics['cpl_reduction']['display'] ?? 'über 85 %';
$e3_cpl_before    = $e3_metrics['cpl_before']['display'] ?? '150 €';
$e3_cpl_after     = $e3_metrics['cpl_after']['display'] ?? '22 €';
$e3_timeframe     = $e3_metrics['timeframe']['display'] ?? '6 Monate';
$e3_build_months  = $e3_metrics['build_months']['display'] ?? '3 Monate';

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
$entry_price      = $format_eur( (int) ( $pricing_canon['entry_setup_price'] ?? 790 ) );
$analysis_price   = $format_eur( (int) ( $pricing_canon['analysis_price'] ?? 690 ) );

// ── Marktcheck (Diagnose-Canon) ────────────────────────────────
$diagnose_canon    = function_exists( 'hu_diagnose_canon' ) ? hu_diagnose_canon() : [];
$analysis_days     = (int) ( $diagnose_canon['primary_days'] ?? 7 );
$marketcheck_reply = function_exists( 'hu_marketcheck_reply_label' ) ? hu_marketcheck_reply_label() : 'spätestens 2 Werktage';
$marketcheck_steps = (int) ( $diagnose_canon['marketcheck_steps'] ?? 5 );
$marketcheck_fit_q = (int) ( $diagnose_canon['marketcheck_fit_questions'] ?? 4 );
$marketcheck_mins  = (int) ( $diagnose_canon['marketcheck_minutes'] ?? 2 );

// ── Kontakt (Messaging-Canon) ──────────────────────────────────
$contact_email = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';

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
	[ 'nr' => '05', 'id' => 'einstieg',   'titel' => 'Die Leiter',   'kurz' => 'Leiter' ],
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
// und was passiert, wenn die Station fehlt. Die dritte Spalte ist der
// eigentliche Verkaufstext — sie beziffert den Verlust.
$stations = [
	[
		'id'    => 'station-sichtbarkeit',
		'titel' => 'Sichtbarkeit auf Ihrer Domain',
		'bau'   => 'Anzeigen in Google und Meta sowie organische Suche führen auf eine Seite, die Ihnen gehört — nicht auf ein Portal-Formular, das Ihren Namen erst nach dem Kauf nennt.',
		'sicht' => 'Der Interessent kennt Ihren Betriebsnamen, bevor er das Formular abschickt.',
		'fehlt' => 'Die Aufmerksamkeit gehört der Marke des Portals. Ein PV-Kontakt geht dort üblicherweise an drei bis fünf Betriebe — der Interessent hat also mehrere Angebote, bevor der erste Rückruf kommt.',
	],
	[
		'id'    => 'station-vorqualifizierung',
		'titel' => 'Vorqualifizierung statt Fünf-Felder-Formular',
		'bau'   => 'Das Formular fragt in Schritten, was über die Passung entscheidet: Objektart und Dachsituation, Postleitzahl, Projektgröße, Zeithorizont, Eigentum. Wer nicht passt, bricht ab — und das ist der Zweck.',
		'sicht' => 'Sie sehen Region, Objekt und Projektwert, bevor Sie zum Telefon greifen. Kein Erstgespräch zur Feststellung, dass es eine Mietwohnung ist.',
		'fehlt' => 'Die Qualifizierung passiert am Telefon statt im Formular. Bei 25 Anfragen im Monat und je einer Viertelstunde sind das über sechs Stunden Vertriebszeit, bevor überhaupt ein Angebot geschrieben ist.',
	],
	[
		'id'    => 'station-messung',
		'titel' => 'Messung auf eigenem Server',
		'bau'   => 'Serverseitiges Tracking auf einer Subdomain Ihrer Website, Server in Frankfurt. Jede Anfrage bleibt bis zur auslösenden Anzeige zurückverfolgbar — auch bei Ad-Blockern und abgelehnten Cookies. Die Messdaten laufen über Ihren Container, Ihre Konten.',
		'sicht' => 'Sie können sagen, welche Anzeige den 40.000-€-Auftrag gebracht hat. Und welche 900 € verbrannt hat, ohne eine einzige Anfrage.',
		'fehlt' => 'Optimiert wird auf Klickberichte. Das Budget wandert dahin, wo geklickt wird — nicht dahin, wo unterschrieben wird. Der Unterschied fällt erst auf, wenn man beides nebeneinanderlegen kann.',
	],
	[
		'id'    => 'station-vertrieb',
		'titel' => 'Anschluss an Ihren Vertrieb',
		'bau'   => 'Alarm per SMS oder WhatsApp an den zuständigen Vertriebler in unter 60 Sekunden, mit Ort, Projektart und Uhrzeit in der Nachricht. Automatische Eingangsbestätigung mit Terminbuchungslink. Alle Anfragen nach Quelle getrennt in einer Übersicht — auch die, die Sie weiter bei Portalen kaufen.',
		'sicht' => 'Ihr Betrieb ist der Erste, der zurückmeldet. Auch um 20:30 Uhr, auch am Samstag.',
		'fehlt' => 'Die Anfrage liegt bis Montag im Sammelpostfach. Bei einem Kontakt, den vier andere auch haben, entscheidet der erste Rückruf — nicht das beste Angebot. Das ist der Auftrag, den man verliert, ohne je davon zu erfahren.',
	],
	[
		'id'    => 'station-eigentum',
		'titel' => 'Eigentum, schriftlich',
		'bau'   => 'Code, Werbekonten, Tracking-Container, CRM und Domain laufen auf Ihren Zugängen. Jede Komponente ist dokumentiert. Bei Vertragsende gibt es eine Übergabe mit Übersicht, keine Abhängigkeitserklärung.',
		'sicht' => 'Wenn die Zusammenarbeit endet, läuft die Strecke weiter. Ein anderer Dienstleister kann sie übernehmen, ohne neu zu bauen.',
		'fehlt' => 'Man mietet ein System und merkt es erst beim Wechsel. Drei Prüffragen für das, was Sie heute haben: Wem gehört der Code Ihrer Anfrageseite? Wem das CRM? Wem der Tracking-Account?',
	],
];

// ── 02 Ihr Anteil ──────────────────────────────────────────────
// Steht bewusst vor dem Preis. Wer diese drei Punkte nicht liefern
// kann, soll vor der Rechnung aussteigen, nicht nach dem Angebot.
$conditions = [
	[
		'id'    => 'anteil-zugaenge',
		'titel' => 'Zugänge',
		'text'  => 'Domain, Hosting, Werbekonten und CRM auf Ihren Namen. Wo noch nichts existiert, wird es auf Ihren Namen angelegt. Rund zwei Stunden Ihrer Zeit in der ersten Woche.',
	],
	[
		'id'    => 'anteil-entscheider',
		'titel' => 'Eine Person, die entscheidet',
		'text'  => 'Geschäftsführung oder Vertriebsleitung mit Entscheidungsbefugnis, erreichbar für etwa eine Stunde alle zwei Wochen. Kein Gremium, keine Abstimmungsrunde.',
	],
	[
		'id'    => 'anteil-rueckruf',
		'titel' => 'Rückruf-Disziplin',
		'text'  => 'Der Alarm hilft nur, wenn jemand reagiert. Wer vorqualifizierte Anfragen zwei Tage liegen lässt, braucht kein besseres System, sondern eine andere Absprache im Vertrieb.',
	],
];

// ── 04 Der Fall: drei Phasen ───────────────────────────────────
$phases = [
	[
		'label' => sprintf( 'Monat 1–%d · Aufbau', 3 ),
		'text'  => sprintf(
			'Anfragestrecke, Vorqualifizierung und serverseitiges Tracking entstehen. Anzeigen laufen, aber die Kosten pro Anfrage bleiben zunächst im dreistelligen Bereich — es gibt noch keine Datengrundlage, auf die sich optimieren ließe. <b>In dieser Phase sieht ein Betrieb wenig für sein Geld.</b> Wer das nicht aushält, sollte nicht anfangen. Vorbereitung im dokumentierten Fall: %s.',
			esc_html( $e3_build_months )
		),
	],
	[
		'label' => 'Ab Monat 3 · Wirkung',
		'text'  => sprintf(
			'Mit den ersten belastbaren Daten wird messbar, welche Anzeige zu welcher Anfrage und welche Anfrage zu welchem Vertrag führt. Ab hier stabilisieren sich die Kosten pro qualifizierter Anfrage bei %s — von %s zu Beginn.',
			esc_html( $e3_cpl_after ),
			esc_html( $e3_cpl_before )
		),
	],
	[
		'label' => 'Monat 6 · Stand',
		// Die Lead-Conversion-Rate steht ohne Nenner da, weil der Canon
		// keinen nennt. "12 % der Besucher" waere eine erfundene Bezugsgroesse.
		'text'  => sprintf(
			'%s qualifizierte Anfragen im Zeitraum, Lead-Conversion-Rate %s, Abschlussquote von der Anfrage zum Vertrag %s. Der Betrieb hat die Konten, den Code und die Daten.',
			esc_html( $e3_lead_count ),
			esc_html( $e3_lead_conv ),
			esc_html( $e3_sales_conv )
		),
	],
];

$case_rows = [
	[ 'k' => 'Kosten pro qualifizierter Anfrage', 'vor' => $e3_cpl_before, 'nach' => $e3_cpl_after ],
	[ 'k' => 'Abschlussquote Anfrage → Vertrag',  'vor' => $e3_conv_before, 'nach' => $e3_sales_conv ],
	[ 'k' => 'Qualifizierte Anfragen im Zeitraum', 'vor' => '—', 'nach' => $e3_lead_count ],
	[ 'k' => 'Sicht auf Region, Dach, Projektwert', 'vor' => 'keine', 'nach' => 'vor dem Anruf' ],
	[ 'k' => 'Exklusivität der Kontakte', 'vor' => 'je Tarif', 'nach' => 'immer' ],
];

// ── 05 Die Leiter ──────────────────────────────────────────────
// Vier Stufen, jede einzeln buchbar. Der Preis steht an der Stufe,
// nicht in einer Preisliste am Seitenende.
$ladder = [
	[
		'id'    => 'stufe-marktcheck',
		'titel' => 'Marktcheck',
		'takt'  => sprintf( '%d Minuten · Befund %s', $marketcheck_mins, $marketcheck_reply ),
		'text'  => sprintf( '%d Fragen, dann lese ich Ihre Antworten selbst und schreibe zurück, ob ein eigener Anfrageweg bei Ihnen wirtschaftlich trägt. Auch wenn die Antwort nein ist — dann mit drei Hebeln, die ohne mich funktionieren.', $marketcheck_steps ),
		'preis' => '0 €',
		'note'  => 'kostenlos',
	],
	[
		'id'    => 'stufe-sofortkontakt',
		'titel' => 'Sofortkontakt-Setup',
		'takt'  => '5 Werktage · keine Mindestlaufzeit',
		'text'  => 'Wirkt auf die Anfragen, die Sie heute schon haben — auch auf gekaufte Leads von Aroundhome, DAA oder Wattfox. Alarm unter 60 Sekunden, automatische Eingangsbestätigung mit Terminlink, alle Quellen in einer Übersicht. Nach rund 60 Tagen wissen Sie mit eigenen Zahlen, was ein gekaufter Lead pro gewonnenem Auftrag wirklich kostet.',
		'preis' => $entry_price,
		'note'  => 'netto · einmalig',
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
		'text'  => 'Wenn ich absage, kostet Sie das nichts und Sie behalten die drei Hebel. Ich sage häufiger ab als zu — meistens, weil der Projektwert die Rechnung nicht trägt oder weil im Vertrieb niemand zurückruft.',
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
		'text'  => 'Die Weiterentwicklung ist monatlich kündbar. Was ich nicht zusage: Rufbereitschaft, eine Reaktionszeit im Störfall oder ein garantiertes Ergebnis. Wer das braucht, braucht eine Agentur mit Team.',
	],
];

// ── 06 Passung ─────────────────────────────────────────────────
$fit_yes = [
	[ 't' => 'Projektwerte ab ca. 15.000 € privat, 50.000 € gewerblich', 's' => 'Darunter trägt die Marge den Aufbau nicht.' ],
	[ 't' => 'Eigener Vertrieb, der abschließt', 's' => 'Ihr Team oder die Geschäftsführung — jemand, der zurückruft und nachfasst.' ],
	[ 't' => 'Definiertes Zielgebiet', 's' => 'Region oder Bundesland. Nicht „bundesweit, alles“.' ],
	[ 't' => 'Horizont 12 bis 24 Monate', 's' => 'Bereit, ein Asset aufzubauen statt Anfragen zu mieten.' ],
];
$fit_no = [
	[ 't' => '„Nächste Woche brauchen wir Leads.“', 's' => 'Dann kaufen Sie welche. Das ist keine Kritik, sondern der richtige Weg für diesen Zeitrahmen.' ],
	[ 't' => 'Reines Vermittlungsgeschäft', 's' => 'Wer Leads weiterverkauft, braucht kein eigenes System.' ],
	[ 't' => 'Kein Vertriebsprozess', 's' => 'Anfragen sterben, wenn niemand konsequent qualifiziert und nachfasst.' ],
	[ 't' => 'Sichtbarkeit nicht gewollt', 's' => 'Der eigene Anfrageweg lebt davon, dass Ihr Betrieb unterscheidbar wird.' ],
];

// ── 07 Marktcheck: was im Befund steht ─────────────────────────
$report_items = [
	'Einordnung Ihres Marktumfelds anhand der Firmen-Postleitzahl — wie viele Betriebe dort um dieselben Anfragen konkurrieren',
	'Ob der Projektwert die Rechnung trägt, mit Ihren Zahlen durchgerechnet',
	'Drei priorisierte Hebel mit konkretem nächstem Schritt — auch wenn ich absage',
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
		'lead' => 'Ein eigenes Anfragesystem ist eine Anfragestrecke auf der Domain des Installationsbetriebs: Anzeigen und organische Suche führen auf eine eigene Seite, ein mehrstufiges Formular qualifiziert vor dem Erstkontakt vor, serverseitiges Tracking macht jede Anfrage bis zur auslösenden Anzeige zurückverfolgbar, und ein Alarm erreicht den Vertrieb in unter 60 Sekunden.',
		'rest' => 'Code, Werbekonten, Tracking-Container und Daten liegen beim Betrieb. Der Unterschied zum Lead-Einkauf liegt nicht im Preis pro Anfrage, sondern in Exklusivität, Vorqualifizierung und Eigentum.',
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
		'rest' => 'Rechenbeispiel: 2.000 € Monatsbudget ergeben bei 80 € pro Kontakt 25 Anfragen; bei 4 % Abschlussquote ist das ein Auftrag, also 2.000 € pro Auftrag. Eine eigene Strecke mit 45 € pro Anfrage und 12 % Abschlussquote kommt bei gleichem Budget auf einen deutlich niedrigeren Wert — nicht wegen des niedrigeren Anfragepreises, sondern wegen der Vorqualifizierung.',
	],
	[
		'id'   => 'faq-tracking',
		'q'    => 'Funktioniert serverseitiges Tracking ohne Cookie-Banner?',
		'lead' => 'Serverseitiges Tracking verlagert die Messung vom Browser auf einen eigenen Server und macht Anfragen auch bei Ad-Blockern und abgelehnten Cookies zurückverfolgbar — es ersetzt aber keine erforderliche Einwilligung.',
		'rest' => 'Welche Messung einwilligungspflichtig ist, hängt von den eingesetzten Diensten ab; das ist eine rechtliche Bewertung und keine technische.',
	],
	[
		'id'   => 'faq-agentur',
		'q'    => 'Wie unterscheidet sich das von einer Performance-Agentur?',
		'lead' => 'Der Unterschied ist Eigentum: Bei vielen Agenturmodellen liegen Landingpage-Code, Werbekonto und Tracking-Container beim Dienstleister, hier liegen sie beim Betrieb.',
		'rest' => 'Drei Prüffragen: Wem gehört der Code Ihrer Anfrageseite? Wem gehört das CRM? Wem gehört der Tracking-Account? Wenn die Antwort dreimal „uns“ ist, brauchen Sie keinen Wechsel.',
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
// "Befund", nicht "Antwort": der Fuss der Domain verspricht eine Antwort auf
// eine gewoehnliche Anfrage innerhalb von 24 Stunden werktags. Stuende hier
// "Antwort · spätestens 2 Werktage", laesen sich beide Zeilen auf derselben
// Seite als Widerspruch. Es sind zwei Zusagen, und diese hier gilt dem
// haendisch geschriebenen Marktcheck-Befund.
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
			'description'   => sprintf( '%d Fragen in etwa %d Minuten, danach ein händisch geprüfter schriftlicher Befund zu Betrieb und Region per E-Mail — %s.', $marketcheck_steps, $marketcheck_mins, $marketcheck_reply ),
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
			'name'          => 'Anfragesystem-Analyse',
			'price'         => (string) ( $pricing_canon['analysis_price'] ?? 690 ),
			'priceCurrency' => 'EUR',
			'description'   => sprintf( 'Schriftlicher Befund zu Anfragequellen, Tracking, Funnel und Vertriebsanschluss in %d Werktagen, mit drei priorisierten Hebeln. Wird bei Umsetzung auf den Aufbau angerechnet. Netto.', $analysis_days ),
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

<main id="main" class="site-main">
	<?php
	// .solara-landing bleibt als Wurzel stehen: solar-leadgenerierung-solara.js
	// haengt seinen Marktcheck-Mount und den Ankerhandler daran. Alle uebrigen
	// Setups dieser Datei greifen ins Leere und tun still nichts.
	?>
	<div class="solara-landing strecke-doc" data-track-section="anfragestrecke">

		<!-- ════════ Kapitelleiste ════════ -->
		<div class="leiste">
			<div class="blatt in">
				<span class="stand">Anfragestrecke · Photovoltaik, Wärmepumpe, Speicher</span>
				<nav aria-label="Abschnitte dieses Dokuments" data-strecke-leiste>
					<?php foreach ( array_slice( $chapters, 0, 5 ) as $chapter ) : ?>
						<a href="#<?php echo esc_attr( $chapter['id'] ); ?>"
							data-track-action="chapter_jump"
							data-track-category="navigation"
							data-track-section="chapter_bar"
						><?php echo esc_html( $chapter['nr'] . ' ' . $chapter['kurz'] ); ?></a>
					<?php endforeach; ?>
					<a href="#marktcheck"
						data-track-action="cta_strecke_bar_to_marktcheck"
						data-track-category="lead_gen"
						data-track-section="chapter_bar"
					>Marktcheck</a>
				</nav>
			</div>
		</div>

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

					<h1>Anfragen, die auf <em>Ihrer</em> Domain entstehen.</h1>

					<p class="aufriss">
						<span class="erst">Ein eigenes Anfragesystem ist eine Anfragestrecke auf Ihrer Domain.</span>
						Anzeige oder Suche führt auf Ihre Seite. Ein Formular qualifiziert vor, bevor jemand
						Ihren Vertrieb erreicht. Serverseitiges Tracking macht jede Anfrage bis zur auslösenden
						Anzeige zurückverfolgbar. Ein Alarm erreicht Ihren Vertrieb in unter 60 Sekunden.
						Code, Konten und Daten gehören Ihrem Betrieb.
					</p>

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
								<dd>Haşim Üner, Pattensen · 1:1, kein Team</dd>
							</div>
						</dl>
					</div>

					<div class="ausgang">
						<a class="tun" href="#strecke"
							data-track-action="cta_strecke_kopf_to_stationen"
							data-track-category="navigation"
							data-track-section="dokumentkopf"
						>Die fünf Stationen <span class="pf" aria-hidden="true">→</span></a>
						<a class="tun still" href="#einstieg"
							data-track-action="cta_strecke_kopf_to_leiter"
							data-track-category="offer"
							data-track-section="dokumentkopf"
						>Einstieg ab <?php echo esc_html( $entry_price ); ?></a>
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
					// Die viewBox beginnt bei -22, nicht bei 0: die 150-€-Marke sitzt
					// ueber ihrem Punkt bei y=18, und auf dem Telefon wird sie auf
					// 30 Einheiten hochskaliert. Ohne diesen Kopfraum schneidet der
					// obere Rand des SVG sie ab.
					?>
					<svg viewBox="0 -22 620 172" role="img"
						aria-label="Die Kosten pro qualifizierter Anfrage fallen von <?php echo esc_attr( (string) $e3_cpl_before_val ); ?> Euro im ersten Monat auf <?php echo esc_attr( (string) $e3_cpl_after_val ); ?> Euro im sechsten Monat.">
						<g stroke="var(--haar)" stroke-width="1">
							<line x1="46" y1="18" x2="612" y2="18" />
							<line x1="46" y1="50" x2="612" y2="50" />
							<line x1="46" y1="82" x2="612" y2="82" />
						</g>
						<line x1="46" y1="114" x2="612" y2="114" stroke="var(--strich)" stroke-width="1" />
						<?php
						// font-size steht als Attribut UND als CSS-Klasse: unter 700 px
						// skaliert das Stylesheet die Beschriftung hoch, weil das SVG
						// sonst auf ~290 px Breite gerechnet wird und 9 Einheiten dort
						// gut vier Pixel ergeben.
						?>
						<g class="achse" fill="var(--matt)" font-family="IBM Plex Mono, monospace" font-size="9">
							<text x="0" y="21">150 €</text>
							<text x="0" y="53">100 €</text>
							<text x="8" y="85">50 €</text>
							<text x="17" y="117">0 €</text>
						</g>
						<polyline class="kurve" points="58,18 168,45 278,66 388,93 498,107 600,111" fill="none"
							stroke="var(--stempel)" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
						<g fill="var(--stempel)" class="punkte">
							<circle cx="58" cy="18" r="3.5" />
							<circle cx="600" cy="111" r="4.5" />
						</g>
						<g class="marke" fill="var(--tinte)" font-family="IBM Plex Mono, monospace" font-size="11" font-weight="500">
							<text x="69" y="15"><?php echo esc_html( $e3_cpl_before ); ?></text>
							<text x="548" y="105"><?php echo esc_html( $e3_cpl_after ); ?></text>
						</g>
						<?php
						// Die letzte Zeitmarke haengt am rechten Achsenende statt an
						// einer festen x-Position: hochskaliert lief sie sonst aus
						// dem Bild.
						?>
						<g class="zeit" fill="var(--matt)" font-family="IBM Plex Mono, monospace" font-size="8.5">
							<text x="52" y="132">MONAT 1</text>
							<text x="300" y="132" text-anchor="middle">MONAT 3</text>
							<text x="612" y="132" text-anchor="end">MONAT 6</text>
						</g>
					</svg>

					<table class="werte">
						<caption class="nur-vorlesen">Kennzahlen des dokumentierten Falls</caption>
						<tbody>
							<tr>
								<th scope="row">Kosten pro qualifizierter Anfrage, Monat 6</th>
								<td class="gross"><?php echo esc_html( $e3_cpl_after ); ?></td>
							</tr>
							<tr>
								<th scope="row">Ausgangswert über Portale, Monat 1</th>
								<td><?php echo esc_html( $e3_cpl_before ); ?></td>
							</tr>
							<tr>
								<th scope="row">Qualifizierte Anfragen im Zeitraum</th>
								<td><?php echo esc_html( $e3_lead_count ); ?></td>
							</tr>
							<tr>
								<th scope="row">Abschlussquote Anfrage → Vertrag</th>
								<td class="gross"><?php echo esc_html( $e3_sales_conv ); ?></td>
							</tr>
							<tr class="quelle">
								<th scope="row" colspan="2">
									<?php echo esc_html( ucfirst( $e3_case_label ) ); ?>, DACH, anonymisiert.
									<?php echo esc_html( $e3_build_months ); ?> Vorbereitung, ab Monat drei stabil.
									Reduktion <?php echo esc_html( $e3_cpl_reduction ); ?>.
									Dokumentierte Werte eines einzelnen Betriebs, keine Prognose für Ihren.
								</th>
							</tr>
						</tbody>
					</table>
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

			<!-- Hauptgrafik: zwei Wege, ein Interessent -->
			<div class="reihe bild">
				<div class="ganz tafel">
					<figure>
						<div class="bildkopf">
							<div>
								<figcaption>Derselbe Interessent, zwei Wege — und wo unterwegs etwas verloren geht.</figcaption>
								<span class="mono" style="display:block;margin-top:.7rem">Beide Wege mit 2.000 € Werbe- bzw. Einkaufsbudget im Monat</span>
							</div>
							<div class="quotient">
								<span class="q zahl">5×</span>
								<span class="qt">so viele Aufträge<br>bei diesen Annahmen</span>
							</div>
						</div>

						<div class="buehne">
							<svg viewBox="0 26 1180 450" role="img"
								aria-label="Vergleich zweier Wege bei gleichem Monatsbudget von 2.000 Euro. Beim Einkauf über Portale entstehen 25 Anfragen, die an drei bis fünf Betriebe verteilt werden, am Telefon qualifiziert werden müssen und oft zu spät zurückgerufen werden; am Ende steht ein Auftrag im Monat zu 2.000 Euro. Über die eigene Strecke entstehen 44 Anfragen, die exklusiv sind, sich im Formular selbst vorqualifizieren und binnen 60 Sekunden einen Alarm auslösen; am Ende stehen 5,3 Aufträge im Monat zu 501 Euro.">
								<g stroke="#332e28" stroke-width="1" stroke-dasharray="2 5">
									<line x1="330" y1="72" x2="330" y2="440" />
									<line x1="545" y1="72" x2="545" y2="440" />
									<line x1="755" y1="72" x2="755" y2="440" />
									<line x1="940" y1="72" x2="940" y2="440" />
								</g>
								<g fill="#948c81" font-family="IBM Plex Mono, monospace" font-size="11.5" letter-spacing="1.6">
									<text x="95" y="60">ANFRAGE ENTSTEHT</text>
									<text x="330" y="60">VERTEILUNG</text>
									<text x="545" y="60">QUALIFIZIERUNG</text>
									<text x="755" y="60">RÜCKRUF</text>
									<text x="940" y="60">AUFTRAG</text>
								</g>

								<path d="M95,125.0 C212,125.0 212,125.0 330,125.0 L330,175.0 C212,175.0 212,175.0 95,175.0 Z" fill="#565049" stroke="#8c8279" stroke-width="1" />
								<path d="M330,125.0 C438,125.0 438,135.0 545,135.0 L545,165.0 C438,165.0 438,175.0 330,175.0 Z" fill="#565049" stroke="#8c8279" stroke-width="1" />
								<path d="M545,135.0 C650,135.0 650,141.5 755,141.5 L755,158.5 C650,158.5 650,165.0 545,165.0 Z" fill="#565049" stroke="#8c8279" stroke-width="1" />
								<path d="M755,141.5 C848,141.5 848,146.0 940,146.0 L940,154.0 C848,154.0 848,158.5 755,158.5 Z" fill="#565049" stroke="#8c8279" stroke-width="1" />

								<path d="M95,324.0 C212,324.0 212,324.0 330,324.0 L330,412.0 C212,412.0 212,412.0 95,412.0 Z" fill="#8a4a1e" stroke="#ef8b4d" stroke-width="1.2" />
								<path d="M330,324.0 C438,324.0 438,345.0 545,345.0 L545,391.0 C438,391.0 438,412.0 330,412.0 Z" fill="#8a4a1e" stroke="#ef8b4d" stroke-width="1.2" />
								<path d="M545,345.0 C650,345.0 650,347.5 755,347.5 L755,388.5 C650,388.5 650,391.0 545,391.0 Z" fill="#8a4a1e" stroke="#ef8b4d" stroke-width="1.2" />
								<path d="M755,347.5 C848,347.5 848,350.0 940,350.0 L940,386.0 C848,386.0 848,388.5 755,388.5 Z" fill="#8a4a1e" stroke="#ef8b4d" stroke-width="1.2" />

								<g fill="#f4f1ec" font-family="IBM Plex Mono, monospace" font-size="13" font-weight="500">
									<text x="95" y="115">25 gekaufte Anfragen</text>
									<text x="95" y="313">44 eigene Anfragen</text>
								</g>

								<g stroke="#7d746a" stroke-width="1.2" fill="none" marker-end="url(#strecke-pfeil-grau)">
									<path d="M437,124 L437,96" />
									<path d="M650,131 L650,103" />
									<path d="M848,137 L848,109" />
								</g>
								<g fill="#a9a199" font-family="IBM Plex Mono, monospace" font-size="11.5">
									<text x="437" y="88" text-anchor="middle">an 3–5 Betriebe</text>
									<text x="650" y="95" text-anchor="middle">15 Min Telefonzeit je Kontakt</text>
									<text x="848" y="101" text-anchor="middle">zu spät angerufen</text>
								</g>

								<g stroke="#ef8b4d" stroke-width="1.2" fill="none" marker-end="url(#strecke-pfeil-orange)">
									<path d="M437,412 L437,436" />
									<path d="M650,394 L650,436" />
									<path d="M848,389 L848,436" />
								</g>
								<g fill="#ef8b4d" font-family="IBM Plex Mono, monospace" font-size="11.5">
									<text x="437" y="459" text-anchor="middle">bleibt exklusiv</text>
									<text x="650" y="459" text-anchor="middle">Abbruch im Formular</text>
									<text x="848" y="459" text-anchor="middle">Alarm unter 60 Sek.</text>
								</g>

								<g font-family="IBM Plex Mono, monospace">
									<text x="962" y="152" fill="#f4f1ec" font-size="42" font-weight="500" letter-spacing="-1.5">1,0</text>
									<text x="962" y="174" fill="#948c81" font-size="11.5">Auftrag / Monat</text>
									<text x="962" y="192" fill="#948c81" font-size="11.5">2.000 € je Auftrag</text>
									<text x="962" y="370" fill="#ef8b4d" font-size="42" font-weight="500" letter-spacing="-1.5">5,3</text>
									<text x="962" y="392" fill="#948c81" font-size="11.5">Aufträge / Monat</text>
									<text x="962" y="410" fill="#948c81" font-size="11.5">501 € je Auftrag</text>
								</g>

								<defs>
									<marker id="strecke-pfeil-grau" viewBox="0 0 8 8" refX="4" refY="4" markerWidth="5" markerHeight="5" orient="auto">
										<polygon points="0,0 8,4 0,8" fill="#7d746a" />
									</marker>
									<marker id="strecke-pfeil-orange" viewBox="0 0 8 8" refX="4" refY="4" markerWidth="5" markerHeight="5" orient="auto">
										<polygon points="0,0 8,4 0,8" fill="#ef8b4d" />
									</marker>
								</defs>
							</svg>
							<div class="blende" aria-hidden="true"></div>
						</div>

						<p class="wischhinweis">Seitwärts wischen für den vollen Verlauf →</p>

						<p class="bildfuss">
							Beide Wege verlieren unterwegs. Der Unterschied liegt darin, <b>wo</b>:
							Beim Einkauf bricht die Menge nach dem Kontakt weg — also nachdem der Kontakt
							bezahlt und Vertriebszeit hineingeflossen ist. Auf der eigenen Strecke bricht sie
							im Formular weg, also vor dem ersten Anruf und ohne Kosten. Bandbreiten der
							Anfragen maßstäblich, Verlauf danach schematisch; die Endwerte sind mit den
							Vorgabewerten aus <a class="satzlink" href="#rechnung">Abschnitt 03</a> gerechnet
							und keine Zusage.
						</p>
					</figure>
				</div>
			</div>
		</div>

		<!-- ════════ 01 Die Strecke ════════ -->
		<section id="strecke">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['strecke'] ); ?>
				<div class="voll">
					<h2 class="kopf" id="strecke-titel">Was eine Anfrage bei Ihnen durchläuft.</h2>
					<p class="vorspann">
						Fünf Stationen. An jeder entscheidet sich, ob aus einem Klick ein Termin wird —
						und an jeder geht heute etwas verloren, das nicht auf der Rechnung steht:
						Vertriebszeit und Aufträge, die jemand anders zuerst angerufen hat. Links steht,
						was gebaut wird. Rechts, was davon auf Ihrem Schreibtisch ankommt.
					</p>

					<div class="strecke">
						<?php foreach ( $stations as $station_index => $station ) : ?>
							<article class="station" id="<?php echo esc_attr( $station['id'] ); ?>">
								<span class="i" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $station_index + 1 ) ); ?></span>
								<div>
									<h3 id="<?php echo esc_attr( $station['id'] . '-titel' ); ?>"><?php echo esc_html( $station['titel'] ); ?></h3>
									<p class="bau"><?php echo esc_html( $station['bau'] ); ?></p>
								</div>
								<div class="sicht">
									<span class="l">Auf Ihrem Schreibtisch</span>
									<p><?php echo esc_html( $station['sicht'] ); ?></p>
								</div>
								<p class="fehlt">
									<b>Fehlt diese Station</b>
									<?php echo esc_html( $station['fehlt'] ); ?>
								</p>
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
					<p class="vorspann">
						Ein Anfragesystem ist kein Zukauf, den man aufstellt und laufen lässt.
						Diese drei Punkte entscheiden über das Ergebnis stärker als jede technische
						Entscheidung — deshalb stehen sie <em>vor</em> dem Preis.
					</p>

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
					<p class="vorspann">
						Ein Lead für 80 € ist billig, bis man weiß, wie viele davon zum Auftrag führen.
						Hier steht deshalb die einzige Zahl, die im Betrieb zählt: was Sie ein gewonnener
						Auftrag im jeweiligen Weg kostet. Alle Werte sind Ihre Annahmen — die Seite
						behauptet keine.
					</p>
					<p class="vorspann klein">
						Beide Spalten sind mit <b>2.000 € im Monat</b> vorbelegt, damit der Vergleich am
						selben Budget hängt — dieselbe Rechnung wie in der Grafik oben. In dem
						dokumentierten Marktfall aus der Zeile „Zum Vergleich“ lag der Wert für den
						Einkaufsweg ebenfalls bei rund 2.000 € pro Auftrag. Die Voreinstellung ist also
						nicht schwarzgemalt.
					</p>

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
									<input id="strecke-b2" data-feld="b2" type="number" inputmode="numeric" min="1" max="1000" step="1" value="45">
									<span class="einheit">€</span>
								</span>
							</div>
							<div class="eingabe">
								<label for="strecke-b3">Abschlussquote auf vorqualifizierte Anfragen</label>
								<span class="feld">
									<input id="strecke-b3" data-feld="b3" type="number" inputmode="decimal" min="0" max="100" step="0.5" value="12">
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
						<b>Enthalten</b> — in Weg B der Aufbau mit <?php echo esc_html( $foundation_price ); ?> auf
						<?php echo esc_html( (string) $calc_months ); ?> Monate verteilt, rund
						<?php echo esc_html( $hosting_price ); ?> Hosting im Monat und Ihr Werbebudget.
						<b>Nicht enthalten</b> — die Vertriebszeit. Sie fällt in beiden Wegen an, in Weg A
						aber pro unqualifizierter Anfrage: Wer 25 Kontakte im Monat je eine Viertelstunde
						durchtelefoniert, um den einen zu finden, der unterschreibt, verbringt damit gut
						sechs Stunden am Telefon. Voreingestellt sind bewusst vorsichtige Werte:
						45 € statt der <?php echo esc_html( $e3_cpl_after ); ?> aus dem dokumentierten Fall,
						12 % statt <?php echo esc_html( $e3_sales_conv ); ?>.
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
						Ein <?php echo esc_html( $e3_case_label ); ?> in DACH. Anonymisiert, weil ein Verfahren
						läuft — die Belege zeige ich im Gespräch. Der Verlauf ist wichtiger als die Endzahl,
						weil er zeigt, wann nichts passiert.
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

					<div class="fall tafel">
						<table>
							<caption class="nur-vorlesen">Vorher-Nachher-Vergleich des dokumentierten Falls</caption>
							<thead>
								<tr>
									<th scope="col">Kennzahl</th>
									<th scope="col">Vorher · Portal-Einkauf</th>
									<th scope="col">Nachher · eigene Strecke</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $case_rows as $row ) : ?>
									<tr>
										<th scope="row"><?php echo esc_html( $row['k'] ); ?></th>
										<td class="vor"><?php echo esc_html( $row['vor'] ); ?></td>
										<td class="nach"><?php echo esc_html( $row['nach'] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3">
										<?php echo esc_html( $e3_build_months ); ?> Vorbereitung, ab Monat drei stabil bei
										<?php echo esc_html( $e3_cpl_after ); ?>. Reduktion <?php echo esc_html( $e3_cpl_reduction ); ?>.
										Die Vorher-Abschlussquote ist eine Marktannahme, keine gemessene Zahl dieses Betriebs.
									</td>
								</tr>
							</tfoot>
						</table>
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
					<h2 class="kopf" id="leiter">Vier Stufen. Sie müssen nicht oben anfangen.</h2>
					<p class="vorspann">
						Jede Stufe steht für sich und lässt sich einzeln buchen. Wer unten einsteigt, hat
						nach zwei Monaten eigene Zahlen und entscheidet auf dieser Grundlage über die nächste.
					</p>

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
					<p class="vorspann">
						Ehrliche Vorauswahl, bevor wir reden. Wenn mehr als ein Punkt in der rechten Spalte
						auf Sie zutrifft, sparen Sie sich den Marktcheck.
					</p>

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
					<h2 class="kopf" id="marktcheck-titel">Marktcheck vor Angebot.</h2>
					<p class="vorspann">
						Kein Verkaufsgespräch, kein Pflicht-Call. <?php echo esc_html( (string) $marketcheck_steps ); ?>
						Fragen, die ich selbst lese, und ein schriftlicher Befund zu Betrieb und Region.
						Wenn es nicht passt, sage ich das — mit drei Hebeln, die Sie ohne mich umsetzen können.
					</p>
					<p class="vorspann klein">
						Andere Anbieter in diesem Markt führen an dieser Stelle ins Gespräch. Ich schicke ein
						Dokument. Der Unterschied ist kein Stil, sondern eine Arbeitsentscheidung: Was ich
						schriftlich behaupte, muss ich belegen können, und Sie können es in Ruhe lesen,
						weiterreichen und ablehnen, ohne jemanden am Telefon abwimmeln zu müssen.
					</p>

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
								<?php echo esc_html( (string) $marketcheck_steps ); ?> Schritte, etwa
								<?php echo esc_html( (string) $marketcheck_mins ); ?> Minuten.
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
							?>
							<div data-sol-quiz id="sol-quiz-mount">
								<h3 id="sol-quiz-title" class="nur-vorlesen">Marktcheck für Ihren Vertrieb starten</h3>
								<a class="tun" href="mailto:<?php echo esc_attr( $contact_email ); ?>?subject=<?php echo rawurlencode( 'Marktcheck' ); ?>"
									data-track-action="cta_strecke_marktcheck_mail"
									data-track-category="lead_gen"
									data-track-section="marktcheck"
									data-track-funnel-stage="intake_open"
								>Marktcheck starten <span class="pf" aria-hidden="true">→</span></a>
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
						kostet. Die Kennzahl ist die einzige, die beide Wege vergleichbar macht — deshalb
						steht sie in <a class="satzlink" href="#rechnung">Abschnitt 03</a> und nicht in einer
                        Fußnote.
					</div>
				</div>
			</div>
		</section>

		<!-- ════════ 09 Verweise ════════ -->
		<section id="verweise">
			<div class="blatt reihe">
				<?php $render_chapter( $chapter_by_id['verweise'] ); ?>
				<div class="voll">
					<h2 class="kopf leise" id="verweise-titel">Die Seiten, auf denen die Zahlen herkommen.</h2>
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
						<h2 id="abschluss">Anfragen besitzen, nicht mieten.</h2>
						<p class="aufriss">
							Ich arbeite 1:1 mit Solar- und SHK-Betrieben. Jede Region wird in Befund,
							Datenkette und Vertriebsanschluss einzeln abgebildet — deshalb entscheidet der
							Marktcheck über die Zusammenarbeit, nicht ein Vertriebsgespräch.
						</p>
						<div class="ausgang">
							<a class="tun" href="#marktcheck"
								data-track-action="cta_strecke_abschluss_to_marktcheck"
								data-track-category="lead_gen"
								data-track-section="abschluss"
							>Marktcheck starten <span class="pf" aria-hidden="true">→</span></a>
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
</main>

<?php foreach ( $schema_blocks as $schema_block ) : ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $schema_block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<?php endforeach; ?>

<?php get_footer(); ?>
