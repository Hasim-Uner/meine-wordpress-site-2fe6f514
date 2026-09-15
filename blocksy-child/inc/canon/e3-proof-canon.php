<?php
/**
 * Canonical anonymized case-study proof metrics.
 *
 * Nur im dokumentierten Fall gemessene bzw. direkt dokumentierte Werte duerfen
 * hier als numerische Proof-Metrik leben. Marktannahmen und Vergleichswerte
 * gehoeren nicht in diesen Canon.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HU_E3_CASE_LABEL', 'mittelständischer PV-Installationsbetrieb' );
define( 'HU_E3_CPL_BEFORE', 150 );
define( 'HU_E3_CPL_AFTER', 22 );
define( 'HU_E3_CPL_REDUCTION_PERCENT', 85 );
define( 'HU_E3_LEAD_COUNT', 1750 );
define( 'HU_E3_LEAD_CONVERSION_PERCENT', 12 );
define( 'HU_E3_SALES_CONVERSION_PERCENT', 15 );
define( 'HU_E3_TIMEFRAME_MONTHS', 6 );

// Zwischenwerte der Strecke. Standen bis 2026-08 als Literale in
// page-e3-new-energy.php und waren damit weder pruefbar noch mitpflegbar.
define( 'HU_E3_CPL_RAMP_LOW', 70 );
define( 'HU_E3_CPL_RAMP_HIGH', 100 );
define( 'HU_E3_BUILD_MONTHS', 3 );
define( 'HU_E3_TUNING_MONTHS', 3 );

/**
 * Return the canonical E3 proof data.
 *
 * @return array<string, mixed>
 */
function hu_e3_canon() {
	return [
		'case_label' => HU_E3_CASE_LABEL,
		'url'        => home_url( '/case-study-solar-leadgenerierung/' ),
		'metrics'    => [
			'cpl_before'       => [
				'value'   => HU_E3_CPL_BEFORE,
				'display' => '150 €',
				'label'   => 'Kosten pro gekaufter Anfrage vorher',
			],
			'cpl_after'        => [
				'value'   => HU_E3_CPL_AFTER,
				'display' => '22 €',
				'label'   => 'Kosten pro eigener Anfrage nachher',
			],
			// Labtest-Werte dieser Website. Sie leben hier, damit jede sichtbare
			// Kennzahl denselben Canon-Zugriff nutzt: Startseite und
			// Ergebnisse-Hub zeigen sonst zwei Staende derselben Messung.
			// Lighthouse ist ein Labormesswert und ersetzt keine Felddaten —
			// sichtbare Copy muss das mitfuehren.
			'site_lighthouse_performance' => [
				'value'   => 99,
				'display' => '99',
				'max'     => '/100',
				'label'   => 'PageSpeed mobil',
			],
			'site_lighthouse_accessibility' => [
				'value'   => 100,
				'display' => '100',
				'max'     => '/100',
				'label'   => 'Barrierefreiheit',
			],
			'site_lighthouse_seo' => [
				'value'   => 100,
				'display' => '100',
				'max'     => '/100',
				'label'   => 'SEO',
			],
			'site_lighthouse_best_practices' => [
				'value'   => 100,
				'display' => '100',
				'max'     => '/100',
				'label'   => 'Best Practices',
			],
			'cpl_reduction'    => [
				'value'                => HU_E3_CPL_REDUCTION_PERCENT,
				'display'              => 'über 85 %',
				'conservative_display' => 'über 85 %',
				'counter_target'       => '85',
				'label'                => 'Kosten pro Anfrage',
			],
			'lead_count'       => [
				'value'          => HU_E3_LEAD_COUNT,
				'display'        => '1.750+',
				'counter_target' => '1750',
				'label'          => 'qualifizierte Anfragen',
			],
			'lead_conversion'  => [
				'value'          => HU_E3_LEAD_CONVERSION_PERCENT,
				'display'        => '12 %',
				'counter_target' => '12',
				'label'          => 'Lead-Conversion-Rate',
			],
			'sales_conversion' => [
				'value'          => HU_E3_SALES_CONVERSION_PERCENT,
				'display'        => '15 %',
				'counter_target' => '15',
				'label'          => 'Abschlussquote',
			],
			// Kompatibilitaets-Key fuer bestehende Templates. Die frueher genannte
			// Vorherquote war keine Kohortenmessung dieses Falls und ist deshalb als
			// numerische Proof-Metrik entfernt. Neue Copy darf daraus keinen
			// Vorher/Nachher-Uplift ableiten.
			'sales_conversion_before' => [
				'value'          => null,
				'display'        => 'nicht als Kohorte gemessen',
				'display_hedged' => 'nicht als Kohorte gemessen',
				'label'          => 'Abschlussquote vorher (keine belastbare Kohortenmessung)',
				'status'         => 'not_measured',
			],
			'sales_conversion_after' => [
				'value'          => HU_E3_SALES_CONVERSION_PERCENT,
				'display'        => '15 %',
				'counter_target' => '15',
				'label'          => 'Abschlussquote nachher (eigenes Anfragesystem)',
			],
			// Kompatibilitaets-Key: kein numerischer Uplift mehr, weil fuer die
			// Vorher-Seite keine belastbare Kohortenmessung dokumentiert ist.
			'sales_conversion_uplift' => [
				'display' => '15 % nach Aufbau',
				'short'   => '15 % Abschlussquote nach Aufbau',
				'label'   => 'Abschlussquote nachher; Vorherwert nicht als Kohortenmessung dokumentiert',
				'status'  => 'not_comparable',
			],
			'timeframe'        => [
				'value'          => HU_E3_TIMEFRAME_MONTHS,
				'display'        => '6 Monate',
				'display_dative' => '6 Monaten',
				'counter_target' => '6',
				'label'          => 'Zeitraum',
			],
			'cpl_ramp'         => [
				'value'      => HU_E3_CPL_RAMP_LOW,
				'value_high' => HU_E3_CPL_RAMP_HIGH,
				'display'    => '70 – 100 €',
				'label'      => 'Kosten pro Anfrage in der Aufbauphase',
			],
			'build_months'     => [
				'value'   => HU_E3_BUILD_MONTHS,
				'display' => '3 Monate',
				'label'   => 'Implementierung',
			],
			// Anlaufzeit bis zur ersten qualifizierten Anfrage im dokumentierten
			// Fall. Erfahrungswert aus diesem einen Projekt, kein Marktversprechen
			// — die Copy muss den Fallbezug immer mitfuehren.
			'first_requests_weeks' => [
				'display' => '4–6 Wochen',
				'label'   => 'bis zu den ersten qualifizierten Anfragen (dokumentierter Fall)',
			],
			'tuning_months'    => [
				'value'   => HU_E3_TUNING_MONTHS,
				'display' => '3 Monate',
				'label'   => 'Optimierung',
			],
		],
		'summary'    => [
			'compact'    => '150 € auf 22 € Kosten pro Anfrage, 1.750+ qualifizierte Anfragen, 12 % Lead-Conversion-Rate und 15 % Abschlussquote, 6 Monate.',
			'proof'      => 'Referenz mittelständischer PV-Installationsbetrieb: 1.750+ qualifizierte Anfragen, 12 % Lead-Conversion-Rate, 15 % Abschlussquote und über 85 % weniger Kosten pro Anfrage.',
			'conversion' => 'Im selben Projekt lag die Lead-Conversion-Rate bei 12 % und die Abschlussquote bei 15 %; an der Abschlussquote hatte der Vertrieb einen wesentlichen Anteil.',
		],
	];
}

/**
 * Return one display field from the E3 canon.
 *
 * @param string $metric Metric key.
 * @param string $field  Field key.
 * @param string $fallback Fallback value.
 * @return string
 */
function hu_e3_metric( $metric, $field = 'display', $fallback = '' ) {
	$canon   = hu_e3_canon();
	$metrics = isset( $canon['metrics'] ) && is_array( $canon['metrics'] ) ? $canon['metrics'] : [];

	if ( ! isset( $metrics[ $metric ] ) || ! is_array( $metrics[ $metric ] ) ) {
		return $fallback;
	}

	if ( ! array_key_exists( $field, $metrics[ $metric ] ) ) {
		return $fallback;
	}

	return (string) $metrics[ $metric ][ $field ];
}

/**
 * Return a canonical E3 summary sentence.
 *
 * @param string $variant Summary variant.
 * @return string
 */
function hu_e3_summary( $variant = 'proof' ) {
	$canon     = hu_e3_canon();
	$summaries = isset( $canon['summary'] ) && is_array( $canon['summary'] ) ? $canon['summary'] : [];

	if ( isset( $summaries[ $variant ] ) ) {
		return (string) $summaries[ $variant ];
	}

	return isset( $summaries['proof'] ) ? (string) $summaries['proof'] : '';
}
