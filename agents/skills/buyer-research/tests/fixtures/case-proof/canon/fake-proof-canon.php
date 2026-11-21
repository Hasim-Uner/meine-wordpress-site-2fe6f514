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
	// Annahme, keine Messung: value_high und display_hedged markieren das.
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
