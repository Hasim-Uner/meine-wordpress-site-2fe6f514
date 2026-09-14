<?php
/**
 * Canonical customer-facing messaging anchors and wording guardrails.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define(
	'HU_MESSAGE_VALUE_ANCHOR_ARCHITECTURE',
	'WordPress, Tracking und Conversion gehören zusammen. Ich entwickle die technische Basis und messe, was daraus entsteht.'
);

define(
	'HU_MESSAGE_VALUE_ANCHOR_PRICE',
	'Scope und Preis stehen vor dem Start fest. Kein Paketpreis ohne geklärten Umfang.'
);

// ── Oeffentliche Kontaktadresse ───────────────────────────────────
// Die White-Label-Seite nannte hallo@, die Solar-Seite hasim@, das
// Organization-Schema info@ — drei Adressen fuer denselben Zweck, zwei davon
// auf indexierten Seiten. Sichtbare Kontaktwege lesen ab hier.
//
// Primaer ist kontakt@. hallo@ bleibt als Alias bestehen und nimmt Post an,
// wird aber nirgends mehr ausgegeben: zwei aktive Adressen sitewide sind eine
// Frage zu viel fuer den Empfaenger. Die Kopfnavigation nannte kontakt@ schon,
// waehrend der Canon noch hallo@ fuehrte — hier laeuft beides wieder zusammen.
//
// Ausgenommen: die im Impressum und in der Datenschutzerklaerung benannte
// Adresse. Das ist ein rechtlich benannter Kontaktweg, keine Marketing-Copy,
// und wird nicht nebenbei mitgezogen.
//
// Der tatsaechliche Absender der Transaktionsmails kommt aus der
// Laufzeitkonfiguration (NEXUS_BREVO_FROM_EMAIL et al. in inc/mail.php), nicht
// von hier. Wo Copy den Absender benennt, muss beides zusammenpassen.
define( 'HU_CONTACT_EMAIL', 'kontakt@hasimuener.de' );

/**
 * Public contact address for visible contact paths.
 *
 * @return string
 */
function hu_get_contact_email() {
	return HU_CONTACT_EMAIL;
}

// Telefonnummer als zweiter direkter Weg. Impressum und Datenschutz tragen die
// Nummer bis heute je einmal hart im Template; der Fuss holt sie hier, damit
// eine dritte Kopie gar nicht erst entsteht.
define( 'HU_CONTACT_PHONE', '+4917676596580' );
define( 'HU_CONTACT_PHONE_DISPLAY', '0176 76596580' );

/**
 * Public phone number for visible contact paths.
 *
 * @param string $variant One of: link, display.
 * @return string
 */
function hu_get_contact_phone( $variant = 'display' ) {
	if ( 'link' === $variant ) {
		return 'tel:' . HU_CONTACT_PHONE;
	}

	return HU_CONTACT_PHONE_DISPLAY;
}

// ── Antwortzeit auf eine Anfrage ──────────────────────────────────
// Startseite und White-Label-Seite versprachen "4 Stunden werktags", die
// Kontaktseite als gemeinsames Ziel beider CTAs dagegen "in der Regel 48
// Stunden, spaetestens 2 Werktage". Damit stand die schwaechste Fassung genau
// dort, wo abgeschickt wird: das Versprechen brach im Formular. Seither liest
// jede sichtbare Stelle aus dieser Datei.
//
// Seit 2026-09-12 ist die Zusage in Werktagen formuliert, nicht in Stunden.
// Die Stundenangabe war eine zweite Zeitrechnung neben dem Marktcheck-Label
// ("spaetestens 2 Werktage") und nebenbei die haertere Zusage fuer den
// Absender: wer freitags abends schreibt, bekam ein 24-Stunden-Versprechen,
// das erst montags einloesbar war. Ein Mass fuer beide Vorgaenge.
//
// HU_MARKETCHECK_REPLY_HOURS in canon/diagnose-canon.php bleibt davon
// unberuehrt: das ist die interne Bearbeitungszeit des Marktchecks bis zum
// haendischen Befund, keine Antwortzeit auf eine Anfrage. Sie steuert seit
// 2026-09-11 keine sichtbare Copy mehr.
define( 'HU_RESPONSE_BUSINESS_DAYS', 2 );

/**
 * Display value for the canonical response promise.
 *
 * Fuenf Fassungen derselben Zusage, damit kein Aufrufer sie selbst
 * zusammensetzt:
 *
 * - `value`    der nackte Wert fuer Label-Spalten ("spätestens 2 Werktage").
 *              Der Fuss setzt ihn in ein <b> und braucht ihn deshalb ohne
 *              Rahmensatz.
 * - `window`   die Praepositionalfassung fuer den Fliesstext ("Sie erhalten
 *              … eine Rueckmeldung"). Dativ, deshalb "Werktagen".
 * - `compact`  eine Zeile fuer Metadaten und Microcopy.
 * - `sentence` ein abgeschlossener Satz.
 * - `phrase`   derselbe Satz ohne Punkt, fuer Aufrufer, die selbst
 *              interpunktieren.
 *
 * @param string $variant One of: phrase, sentence, compact, window, value.
 * @return string
 */
function hu_response_promise( $variant = 'phrase' ) {
	$value  = sprintf( 'spätestens %d Werktage', HU_RESPONSE_BUSINESS_DAYS );
	$window = sprintf( 'spätestens in %d Werktagen', HU_RESPONSE_BUSINESS_DAYS );

	if ( 'value' === $variant ) {
		return $value;
	}

	if ( 'window' === $variant ) {
		return $window;
	}

	if ( 'compact' === $variant ) {
		return sprintf( 'Antwort %s', $value );
	}

	if ( 'sentence' === $variant ) {
		return sprintf( 'Antwort %s.', $window );
	}

	return sprintf( 'Antwort %s', $window );
}

/**
 * Return the canonical messaging model.
 *
 * @return array<string, mixed>
 */
function hu_messaging_canon() {
	return [
		'value_anchor_architecture' => HU_MESSAGE_VALUE_ANCHOR_ARCHITECTURE,
		'value_anchor_price'        => HU_MESSAGE_VALUE_ANCHOR_PRICE,
		'contact_email'             => HU_CONTACT_EMAIL,
		'response_business_days'    => HU_RESPONSE_BUSINESS_DAYS,
		'response_promise'          => hu_response_promise( 'sentence' ),
		'what_we_dont_sell'         => [
			'Keine reine Design-Retusche ohne technischen oder messbaren Zweck.',
			'Keine Reporting-Fassade ohne belastbare Datengrundlage.',
			'Keine Anfrage- oder Umsatzgarantie ohne belastbare Grundlage.',
			'Keine Kundendaten-Blackbox, bei der Ownership unklar bleibt.',
			'Kein Full-Service-Versprechen für Leistungen, die nicht zum vereinbarten Scope gehören.',
		],
		'forbidden_terms'           => [
			'Pilotprojekt',
			'Pilot',
			'Beta',
			'Test',
			'eigentlich kostet das viel mehr',
			'ich bin neu',
			'starte gerade',
			'Berufsanfänger',
			'Modul',
		],
		'preferred_terms'           => [
			'Projekt anfragen',
			'direkte Zusammenarbeit',
			'White-Label',
			'Umsetzungspartner',
			'Baustein',
		],
		'term_definitions'          => [
			'Umsetzungspartner' => 'Betrieb, für den im Solar-/Wärmepumpen-Funnel nach dem Marktcheck ein eigenes Anfragesystem umgesetzt wird; kein Mitgründer, kein Anteilseigner und keine gesellschaftsrechtliche Partnerschaft.',
		],
	];
}

/**
 * Canonical FAQ for the broad Conversion Tracking Setup product.
 *
 * The visible FAQ and the FAQPage JSON-LD both read this function so the
 * commercial scope cannot drift between copy and structured data.
 *
 * @return array<int, array{question:string,answer:string}>
 */
function hu_tracking_setup_faq_items() {
	$setup_price = function_exists( 'hu_tracking_price' )
		? hu_tracking_price( 'standard', 'setup', 'display', '1.290 €' )
		: '1.290 €';
	$delivery_window = function_exists( 'hu_tracking_delivery_weeks_display' )
		? hu_tracking_delivery_weeks_display()
		: '2 bis 3 Wochen';

	return [
		[
			'question' => 'Was kostet ein Conversion Tracking Setup?',
			'answer'   => sprintf( 'Der klar abgegrenzte Basisscope startet bei %1$s netto und wird in der Regel in %2$s umgesetzt. Vor dem Start steht schriftlich fest, welche Systeme, Formulare und Conversions enthalten sind.', $setup_price, $delivery_window ),
		],
		[
			'question' => 'Was ist im Basisscope ab 1.290 € enthalten?',
			'answer'   => 'Bestandsaufnahme und Messplan, GTM- und GA4-Struktur beziehungsweise Bereinigung, CMP- und Consent-Mode-Anbindung im vereinbarten Setup, Google Ads sowie bis zu drei Haupt-Conversions. Dazu kommen Abnahmetests, Dokumentation und Übergabe.',
		],
		[
			'question' => 'Ist Server-Side Tracking im Einstiegspreis enthalten?',
			'answer'   => 'Nein, nicht automatisch. Server-GTM, eigene Tracking-Subdomain, Meta CAPI und Browser-Server-Deduplizierung sind eine eigene Erweiterungsentscheidung. Wenn ein sauberes clientseitiges Setup das Problem löst, wird keine zusätzliche Server-Infrastruktur verkauft.',
		],
		[
			'question' => 'Wann ist Server-Side Tracking sinnvoll?',
			'answer'   => 'Wenn Serversignale, Meta CAPI, Deduplizierung oder eine kontrolliertere technische Messstrecke für das konkrete Setup gebraucht werden. Server-Side Tracking ersetzt weder Einwilligung noch rechtliche Prüfung und verspricht keine 100-Prozent-Attribution.',
		],
		[
			'question' => 'Kann das Tracking bis ins CRM und zu Offline Conversions erweitert werden?',
			'answer'   => 'Ja. CRM-Status, eindeutige Lead-Zuordnung, Offline Conversions und qualifizierte Rücksignale können als eigener Revenue-Scope umgesetzt werden. Diese Ebene wird separat aufgenommen und kalkuliert.',
		],
		[
			'question' => 'Wie wird geprüft, ob das Tracking wirklich funktioniert?',
			'answer'   => 'Die Abnahme arbeitet mit definierten Testfällen: erfolgreicher und fehlgeschlagener Formularversand, Consent-Zustände, Conversion-Aktionen und – falls beauftragt – Deduplizierung sowie CRM-Übergaben. Übergeben werden Messplan, QA-Protokoll, GTM-Versionen und bekannte Grenzen.',
		],
		[
			'question' => 'Brauche ich dafür einen Website-Relaunch?',
			'answer'   => 'Nein. Wenn die Website funktioniert und nur die Messung unsauber ist, kann das Conversion Tracking als eigenständiges Projekt eingerichtet oder bereinigt werden.',
		],
	];
}

/**
 * Add explicit product-family routes without changing the SEO owner of the
 * dedicated Server-Side Tracking service. Header navigation can use the broad
 * setup route while schema and specialist links keep pointing at SST.
 *
 * @param array<string,string> $routes Commercial route map.
 * @return array<string,string>
 */
function hu_position_tracking_product_route( $routes ) {
	if ( ! is_array( $routes ) ) {
		return $routes;
	}

	$routes['tracking_setup']    = home_url( '/ga4-tracking-setup/' );
	$routes['tracking_advanced'] = home_url( '/server-side-tracking-b2b/' );

	return $routes;
}
add_filter( 'hu_commercial_route_map', 'hu_position_tracking_product_route', 20 );
