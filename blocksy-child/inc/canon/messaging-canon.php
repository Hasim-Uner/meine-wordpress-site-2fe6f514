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
// Ausgenommen: die im Impressum und in der Datenschutzerklaerung benannte
// Adresse. Das ist ein rechtlich benannter Kontaktweg, keine Marketing-Copy,
// und wird nicht nebenbei mitgezogen.
//
// Der tatsaechliche Absender der Transaktionsmails kommt aus der
// Laufzeitkonfiguration (NEXUS_BREVO_FROM_EMAIL et al. in inc/mail.php), nicht
// von hier. Wo Copy den Absender benennt, muss beides zusammenpassen.
define( 'HU_CONTACT_EMAIL', 'hallo@hasimuener.de' );

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
// dort, wo abgeschickt wird: das Versprechen brach im Formular.
//
// Nicht zu verwechseln mit HU_MARKETCHECK_REPLY_HOURS in
// canon/diagnose-canon.php. Das ist die Bearbeitungszeit des Marktchecks bis
// zum haendischen Befund, keine Antwortzeit auf eine Anfrage. Beide Werte
// duerfen auseinanderlaufen, aber nur mit dieser Begruendung.
define( 'HU_RESPONSE_HOURS', 24 );

/**
 * Display value for the canonical response promise.
 *
 * @param string $variant One of: phrase, sentence, compact, window.
 * @return string
 */
function hu_response_promise( $variant = 'phrase' ) {
	$window = sprintf( 'innerhalb von %d Stunden werktags', HU_RESPONSE_HOURS );

	if ( 'window' === $variant ) {
		return $window;
	}

	if ( 'compact' === $variant ) {
		return sprintf( 'Antwort in %d Stunden werktags', HU_RESPONSE_HOURS );
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
		'response_hours'            => HU_RESPONSE_HOURS,
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
