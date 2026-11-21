<?php
/**
 * SYNTHETISCHER Kanon nur fuer test_case_proof_check.py.
 * Enthaelt keine realen Werte; der echte Kanon liegt in blocksy-child/inc/canon/.
 * Struktur wie der echte Kanon: Schluessel => Block mit value/display/counter_target/label.
 */
return [
	'fake_close_rate' => [
		'value'          => 15,
		'display'        => '15 %',
		'counter_target' => '15',
		'label'          => 'vorqualifizierte CRM-Leads',
	],
	// Annahme, keine Messung: nur display_hedged markiert das (value_high allein nicht).
	'fake_close_rate_before' => [
		'value'          => 1,
		'value_high'     => 5,
		'display'        => '1 – 5 %',
		'display_hedged' => 'einstellig',
		'label'          => 'Abschlussquote vorher (gekaufte Portal-Leads)',
	],
	'fake_cpl_after'  => [
		'value'   => 22,
		'display' => '22 €',
		'label'   => 'Kosten pro eigener Anfrage',
	],
	'fake_cpl_before' => [
		'value'   => 150,
		'display' => '150 €',
		'label'   => 'Kosten pro gekaufter Portal-Anfrage',
	],
	'fake_lead_count' => [
		'value'          => 200,
		'display'        => '200',
		'counter_target' => '200',
		'label'          => 'Anfragen insgesamt',
	],
	// Display mit Dezimalkomma, ohne counter_target: Fallback auf erste Zahl in display.
	'fake_decimal_rate' => [
		'value'   => 15.4,
		'display' => '15,4 %',
		'label'   => 'vorqualifizierte CRM-Leads',
	],
	// Gemessene Spanne: value + value_high und Anzeige "a–b" (Gedankenstrich), KEINE Annahme.
	'fake_range_measured' => [
		'value'      => 70,
		'value_high' => 80,
		'display'    => '70–80 €',
		'label'      => 'Kosten pro Anfrage im Messzeitraum',
	],
	// Gemessene Spanne nur als Anzeige, ohne value-Schluessel, mit Bindestrich.
	'fake_range_display_only' => [
		'display' => '15-30 €',
		'label'   => 'Spanne der Monatswerte',
	],
	// Spanne nur ueber value + value_high; die Anzeige nennt keine Spanne.
	'fake_range_values_only' => [
		'value'      => 40,
		'value_high' => 50,
		'display'    => 'ca. 40 €',
		'label'      => 'Kosten pro Anfrage, Werte-Spanne',
	],
	// Kein lesbarer Zahlenwert: darf nie still durchgehen.
	'fake_no_number' => [
		'value'   => FAKE_CONST,
		'display' => FAKE_CONST . ' %',
		'label'   => 'vorqualifizierte CRM-Leads',
	],
];
// Schluessel nur als Getter-Argument, kein Block: nicht parsebar.
// fake_getter_only wird ueber fake_metric( 'fake_getter_only' ) gelesen.
function fake_getter_demo() {
	return fake_metric( 'fake_getter_only' );
}
