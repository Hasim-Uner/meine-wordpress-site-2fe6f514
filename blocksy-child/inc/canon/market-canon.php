<?php
/**
 * Canonical third-party market figures for the Solar/SHK cluster.
 *
 * Abgrenzung zu canon/e3-proof-canon.php: dort stehen gemessene Werte aus dem
 * eigenen dokumentierten Fall, hier ausschliesslich fremde Marktzahlen aus
 * oeffentlich zugaenglichen Branchenquellen. Die Trennung ist der Zweck dieser
 * Datei — eine Marktzahl darf auf keiner Seite so aussehen wie ein eigenes
 * Ergebnis, und ein eigenes Ergebnis nicht wie ein Marktdurchschnitt.
 *
 * Deshalb traegt jeder Eintrag seine Quelle mit und jede Ausgabe muss den
 * Hinweissatz aus hu_market_figures_disclaimer() in Sichtweite zeigen.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Erhebungsstand der Zahlen. Aendert sich der Stand, aendern sich auch die
// Werte — das Jahr steht deshalb nicht als Literal in der Copy.
define( 'HU_MARKET_FIGURES_YEAR', 2026 );

/**
 * Return the canonical third-party market figures.
 *
 * `value` ist die sichtbare Zahl, `body` der Satz dahinter, `source` die
 * Herkunft. Die Quelle wird derzeit nur auf Nachfrage genannt, steht aber im
 * Code, damit sie nicht verloren geht.
 *
 * @return array<int, array<string, string>>
 */
function hu_market_figures() {
	return [
		[
			'key'    => 'cpl_range',
			'value'  => '25–200 €',
			'body'   => sprintf(
				'kostet eine Photovoltaik-Anfrage %d je nach Anbieter und Exklusivität. Nicht exklusiv liegt bei rund 30 €, exklusiv und vorqualifiziert bei rund 150 €.',
				HU_MARKET_FIGURES_YEAR
			),
			'source' => 'Anfragenfluss (CPL-Übersicht); Spanne exklusiv/nicht exklusiv: pv magazine',
		],
		[
			'key'    => 'lead_sharing',
			'value'  => '3 bis 5',
			'body'   => 'Betriebe bekommen denselben Kontakt. Wer als Vierter anruft, verkauft über den Preis oder gar nicht.',
			'source' => 'Anfragenfluss; A&M Beratung',
		],
		[
			'key'    => 'cost_per_deal',
			'value'  => '2.000 €',
			'body'   => 'pro gewonnenem Auftrag in einem dokumentierten Fall: 84.000 € Lead-Budget, 950 Anfragen, 42 Aufträge. Abschlussquote 4,4 %.',
			'source' => 'A&M Beratung',
		],
	];
}

/**
 * Mandatory qualifier shown with every rendering of the market figures.
 *
 * Ohne diesen Satz stehen fremde Zahlen im selben Layout wie die eigenen und
 * werden als eigene gelesen. Er ist deshalb kein Kleingedrucktes, sondern
 * Bestandteil der Zahlen.
 *
 * @return string
 */
function hu_market_figures_disclaimer() {
	return sprintf(
		'Marktzahlen aus öffentlich zugänglichen Branchenquellen, Stand %d — nicht aus meinen Projekten. Quellen nenne ich auf Nachfrage.',
		HU_MARKET_FIGURES_YEAR
	);
}
