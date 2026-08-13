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
define( 'HU_REQUEST_ANALYSIS_LABEL', 'Marktcheck' );
define( 'HU_REQUEST_ANALYSIS_ROUTE', '/system-diagnose/' );
define( 'HU_REQUEST_ANALYSIS_DAYS', 7 );
define( 'HU_REQUEST_ANALYSIS_OUTPUT_LABEL', 'schriftlicher Befund zu Anfrage-Quellen, Tracking, Funnel und Vertriebsanschluss' );
define( 'HU_REQUEST_ANALYSIS_PRICE_LABEL', 'nur für passende Betriebe nach Potenzialcheck' );

// Länge des Marktcheck-Intakes im Hero der Solar-Money-Page. Muss zu
// QUIZ_STEPS in assets/js/solar-leadgenerierung-solara.js passen; der
// Lead-Path-Smoke bricht ab, sobald Formular und Canon auseinanderlaufen.
define( 'HU_MARKETCHECK_STEPS', 5 );
define( 'HU_MARKETCHECK_FIT_QUESTIONS', 4 );
define( 'HU_MARKETCHECK_MINUTES', 2 );

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
		'primary_route'              => '/solar-waermepumpen-leadgenerierung/#marktcheck',
		'legacy_primary_route'       => HU_REQUEST_ANALYSIS_ROUTE,
		'primary_days'               => HU_REQUEST_ANALYSIS_DAYS,
		'primary_output_label'       => HU_REQUEST_ANALYSIS_OUTPUT_LABEL,
		'primary_price_label'        => HU_REQUEST_ANALYSIS_PRICE_LABEL,
		'marketcheck_steps'          => HU_MARKETCHECK_STEPS,
		'marketcheck_fit_questions'  => HU_MARKETCHECK_FIT_QUESTIONS,
		'marketcheck_minutes'        => HU_MARKETCHECK_MINUTES,
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
		'credit_policy'              => 'Anrechenbar auf die Umsetzung, wenn aus der Diagnose ein passender Umsetzungsfall wird.',
	];
}

/**
 * Display value for how long the marketcheck intake takes.
 *
 * @return string
 */
function hu_marketcheck_duration_label() {
	return sprintf( 'etwa %d Minuten', HU_MARKETCHECK_MINUTES );
}

/**
 * Display value for the length of the marketcheck intake.
 *
 * Entry points on other routes make this promise before the visitor ever
 * sees the form, so both halves come from here. They used to be literal
 * copy, and the intake grew to five steps while three routes still
 * advertised a three-step, sixty-second marketcheck.
 *
 * @return string
 */
function hu_marketcheck_length_label() {
	return sprintf( '%d Schritte · %s', HU_MARKETCHECK_STEPS, hu_marketcheck_duration_label() );
}

/**
 * Return the current public marketcheck URL.
 *
 * @return string
 */
function hu_get_request_marketcheck_url() {
	$energy_url = function_exists( 'nexus_get_energy_systems_url' ) ? nexus_get_energy_systems_url() : home_url( '/solar-waermepumpen-leadgenerierung/' );
	$url_parts  = explode( '#', (string) $energy_url, 2 );

	return trailingslashit( $url_parts[0] ) . '#marktcheck';
}

/**
 * Return the current public analysis URL.
 *
 * Kept as compatibility alias because older templates and editor-owned CTAs
 * still call the System-Diagnose helper.
 *
 * @return string
 */
function hu_get_request_analysis_url() {
	return hu_get_request_marketcheck_url();
}
