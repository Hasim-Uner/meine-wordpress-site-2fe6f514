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
	'Andere Agenturen verkaufen Optik. Wir bauen die Architektur, die Anfragen produziert. Das Design ist dabei.'
);

define(
	'HU_MESSAGE_VALUE_ANCHOR_PRICE',
	'Sie würden bei einer WordPress-Agentur 15.000 € für Design ausgeben. Hier bekommen Sie für 9.900 € ein funktionierendes Anfrage-System. Das Design ist dabei.'
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
			'Keine reine Design-Retusche ohne Anfrage-System.',
			'Keine Reporting-Fassade ohne belastbare Datengrundlage.',
			'Keine Anfrage-Volumengarantie ohne passendes Werbebudget und schriftliche Grundlage.',
			'Keine Kundendaten-Blackbox, bei der Ownership unklar bleibt.',
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
			'Founding Cohort 2026',
			'Founding-Partner',
			'Founding-Konditionen',
			'Baustein',
		],
		'term_definitions'          => [
			'Founding-Partner' => 'Früher Umsetzungspartner der Founding Cohort 2026; kein Mitgründer, kein Anteilseigner und keine gesellschaftsrechtliche Partnerschaft.',
		],
	];
}
