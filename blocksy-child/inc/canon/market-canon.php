<?php
/**
 * Canonical third-party market figures.
 *
 * Abgrenzung zu canon/e3-proof-canon.php: dort stehen gemessene Werte aus dem
 * eigenen dokumentierten Fall, hier ausschliesslich fremde Marktzahlen aus
 * oeffentlich zugaenglichen Quellen. Die Trennung ist der Zweck dieser
 * Datei — eine Marktzahl darf auf keiner Seite so aussehen wie ein eigenes
 * Ergebnis, und ein eigenes Ergebnis nicht wie ein Marktdurchschnitt.
 *
 * Zwei Bestaende, getrennt nach Einsatzort:
 *
 * - hu_market_figures(): Solar/SHK-Cluster. Die Energie-Money-Page gibt die
 *   Liste vollstaendig aus, jede Ausgabe zeigt den Hinweissatz aus
 *   hu_market_figures_disclaimer() in Sichtweite.
 * - hu_b2b_market_figures(): allgemeine B2B-Marktzahlen fuer Fachartikel.
 *   Sie werden einzeln per [hu_markt key="…"] im Fliesstext ausgegeben; die
 *   Quelle steht sichtbar direkt daneben, im Artikel als [hu_notiz]. Diese
 *   Liste darf nie in die Solar-Schleife geraten, sonst stuende eine
 *   B2B-Vertriebszahl zwischen Leadpreisen.
 *
 * Jeder Eintrag traegt seine Quelle mit.
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
 * Herkunft. Die Herkunft steht ausserdem gesammelt im Hinweissatz aus
 * hu_market_figures_disclaimer() und wird dort sichtbar genannt.
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
 * Return general B2B market figures for editorial articles.
 *
 * Gleiche Felder wie hu_market_figures(). `value` traegt ein geschuetztes
 * Leerzeichen vor dem Prozentzeichen, damit es nicht allein umbricht.
 *
 * @return array<int, array<string, string>>
 */
function hu_b2b_market_figures() {
	return [
		[
			'key'    => 'gartner_b2b_rep_share',
			'value'  => "17\u{00A0}%",
			'body'   => 'des gesamten Kaufprozesses im B2B entfallen auf Gespräche mit Vertriebsleuten.',
			'source' => 'Gartner, 5 Ways the Shift in B2B Buying Will Reconfigure B2B Selling, 2020, aktualisiert 2022; zitiert in der Gartner-Mitteilung vom 27.03.2023',
		],
	];
}

/**
 * Return one field of a third-party market figure by key.
 *
 * Sucht in beiden Bestaenden. Ein unbekannter Schluessel liefert den
 * Fallback, nie eine Zahl aus einem anderen Eintrag.
 *
 * @param string $key      Figure key.
 * @param string $field    Field name: value, body or source.
 * @param string $fallback Returned for an unknown key or field.
 * @return string
 */
function hu_market_figure( $key, $field = 'value', $fallback = '' ) {
	$key = (string) $key;

	foreach ( array_merge( hu_market_figures(), hu_b2b_market_figures() ) as $figure ) {
		if ( isset( $figure['key'] ) && $key === $figure['key'] ) {
			return isset( $figure[ $field ] ) ? (string) $figure[ $field ] : $fallback;
		}
	}

	return $fallback;
}

/**
 * Mandatory qualifier shown with every rendering of the market figures.
 *
 * Ohne diesen Satz stehen fremde Zahlen im selben Layout wie die eigenen und
 * werden als eigene gelesen. Er ist deshalb kein Kleingedrucktes, sondern
 * Bestandteil der Zahlen.
 *
 * Die Quellen stehen namentlich im Satz. Eine Zahl, deren Herkunft erst auf
 * Nachfrage genannt wird, ist auf einer Money Page kein Beleg.
 *
 * @return string
 */
function hu_market_figures_disclaimer() {
	return sprintf(
		'Quellen: Anfragenfluss, CPL-Übersicht Handwerk %1$d · pv magazine, Leitfaden PV-Leadkauf · A&M Beratung, Leadportale im Handwerk. Fremde Zahlen, nicht aus meinen Projekten.',
		HU_MARKET_FIGURES_YEAR
	);
}
