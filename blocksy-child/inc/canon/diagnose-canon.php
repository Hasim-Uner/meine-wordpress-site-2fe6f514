<?php
/**
 * Canonical diagnosis entry points and scope boundaries.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HU_READINESS_DIAGNOSIS_PRICE', 750 );
define( 'HU_READINESS_DIAGNOSIS_DAYS', 14 );
define( 'HU_READINESS_DIAGNOSIS_OUTPUT_PAGES_MIN', 4 );
define( 'HU_READINESS_DIAGNOSIS_OUTPUT_PAGES_MAX', 6 );
define( 'HU_READINESS_DIAGNOSIS_FORM_MINUTES_MIN', 15 );
define( 'HU_READINESS_DIAGNOSIS_FORM_MINUTES_MAX', 20 );
define( 'HU_REQUEST_ANALYSIS_LABEL', 'System-Diagnose' );
define( 'HU_REQUEST_ANALYSIS_ROUTE', '/system-diagnose/' );
define( 'HU_REQUEST_ANALYSIS_DAYS', 7 );
define( 'HU_REQUEST_ANALYSIS_OUTPUT_LABEL', 'schriftlicher Befund zu Anfrage-Quellen, Tracking, Funnel und Vertriebsanschluss' );
define( 'HU_REQUEST_ANALYSIS_PRICE_LABEL', 'nur für passende Founding-Partner nach Potenzialcheck' );

define( 'HU_DEEP_DIAGNOSIS_PRICE', 1500 );
define( 'HU_DEEP_DIAGNOSIS_DAYS', 30 );
define( 'HU_DEEP_DIAGNOSIS_SCREENSHARE_MINUTES', 30 );

/**
 * Return the canonical diagnosis model.
 *
 * @return array<string, mixed>
 */
function hu_diagnose_canon() {
	return [
		'primary_label'              => HU_REQUEST_ANALYSIS_LABEL,
		'primary_route'              => HU_REQUEST_ANALYSIS_ROUTE,
		'primary_days'               => HU_REQUEST_ANALYSIS_DAYS,
		'primary_output_label'       => HU_REQUEST_ANALYSIS_OUTPUT_LABEL,
		'primary_price_label'        => HU_REQUEST_ANALYSIS_PRICE_LABEL,
		'primary_is_public_freebie'  => false,
		'legacy_readiness_route'     => '/readiness-diagnose/',
		'readiness_label'            => HU_REQUEST_ANALYSIS_LABEL,
		'readiness_price'            => HU_READINESS_DIAGNOSIS_PRICE,
		'readiness_days'             => HU_REQUEST_ANALYSIS_DAYS,
		'readiness_output_pages_min' => HU_READINESS_DIAGNOSIS_OUTPUT_PAGES_MIN,
		'readiness_output_pages_max' => HU_READINESS_DIAGNOSIS_OUTPUT_PAGES_MAX,
		'readiness_form_minutes_min' => HU_READINESS_DIAGNOSIS_FORM_MINUTES_MIN,
		'readiness_form_minutes_max' => HU_READINESS_DIAGNOSIS_FORM_MINUTES_MAX,
		'deep_label'                 => 'Tiefendiagnose',
		'deep_public_active'         => false,
		'deep_price'                 => HU_DEEP_DIAGNOSIS_PRICE,
		'deep_days'                  => HU_DEEP_DIAGNOSIS_DAYS,
		'deep_screenshare_minutes'   => HU_DEEP_DIAGNOSIS_SCREENSHARE_MINUTES,
		'access_policy'              => 'Kein Admin-Zugang in der Diagnose.',
		'credit_policy'              => 'Anrechenbar auf die Umsetzung, wenn aus der Diagnose ein passender Founding-Partner-Fall wird.',
	];
}

/**
 * Return the current public analysis URL.
 *
 * @return string
 */
function hu_get_request_analysis_url() {
	return home_url( HU_REQUEST_ANALYSIS_ROUTE );
}
