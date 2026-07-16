<?php
/**
 * Canonical anonymized case-study proof metrics.
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
define( 'HU_E3_SALES_CONVERSION_PERCENT', 12 );
define( 'HU_E3_SALES_CONVERSION_BEFORE_LOW', 1 );
define( 'HU_E3_SALES_CONVERSION_BEFORE_HIGH', 2 );
define( 'HU_E3_TIMEFRAME_MONTHS', 6 );

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
			'sales_conversion' => [
				'value'          => HU_E3_SALES_CONVERSION_PERCENT,
				'display'        => '12 %',
				'counter_target' => '12',
				'label'          => 'Abschlussquote',
			],
			'sales_conversion_before' => [
				'value'           => HU_E3_SALES_CONVERSION_BEFORE_LOW,
				'value_high'      => HU_E3_SALES_CONVERSION_BEFORE_HIGH,
				'display'         => '1 – 5 %',
				'label'           => 'Abschlussquote vorher (gekaufte Portal-Leads)',
			],
			'sales_conversion_after' => [
				'value'          => HU_E3_SALES_CONVERSION_PERCENT,
				'display'        => '12 %',
				'counter_target' => '12',
				'label'          => 'Abschlussquote nachher (eigenes Anfrage-System)',
			],
			'sales_conversion_uplift' => [
				'display' => '1 – 5 % → 12 %',
				'short'   => '6× bis 12× höhere Abschlussquote',
				'label'   => 'Anstieg der Abschlussquote durch eigenes System',
			],
			'timeframe'        => [
				'value'          => HU_E3_TIMEFRAME_MONTHS,
				'display'        => '6 Monate',
				'display_dative' => '6 Monaten',
				'counter_target' => '6',
				'label'          => 'Zeitraum',
			],
		],
		'summary'    => [
			'compact'    => '150 € auf 22 € Kosten pro Anfrage, 1.750+ qualifizierte Anfragen, Abschlussquote von 1 – 5 % auf 12 %, 6 Monate.',
			'proof'      => 'Referenz mittelständischer PV-Installationsbetrieb: 1.750+ qualifizierte Anfragen, Abschlussquote von 1 – 5 % auf 12 % und über 85 % weniger Kosten pro Anfrage.',
			'conversion' => 'Die Abschlussquote stieg im selben Zeitraum von 1 – 5 % (gekaufte Portal-Leads) auf 12 % (eigenes Anfrage-System).',
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
