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

/**
 * Return the canonical messaging model.
 *
 * @return array<string, mixed>
 */
function hu_messaging_canon() {
	return [
		'value_anchor_architecture' => HU_MESSAGE_VALUE_ANCHOR_ARCHITECTURE,
		'value_anchor_price'        => HU_MESSAGE_VALUE_ANCHOR_PRICE,
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
