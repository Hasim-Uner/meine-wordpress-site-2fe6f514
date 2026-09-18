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

// Interner Intake-Vertrag: vier Fit-Signale plus Kontaktdaten ergeben fünf
// Datengruppen. Die UI fasst sie bewusst in zwei sichtbare Schritte zusammen.
// HU_MARKETCHECK_STEPS bleibt als Compatibility-Name fuer die Datengruppen
// bestehen; oeffentliche Copy darf dafuer nur HU_MARKETCHECK_VISIBLE_STEPS
// verwenden. Der Lead-Path-Smoke sichert beide Ebenen getrennt ab.
define( 'HU_MARKETCHECK_STEPS', 5 );
define( 'HU_MARKETCHECK_VISIBLE_STEPS', 2 );
define( 'HU_MARKETCHECK_FIT_QUESTIONS', 4 );
define( 'HU_MARKETCHECK_MINUTES', 2 );

// Antwortzeit auf den Marktcheck. Seit 2026-09-18 keine eigene Zahl mehr:
// der Befund faellt unter dieselbe Zusage wie jede andere Anfrage und liest
// sie ueber hu_marketcheck_reply_label() aus canon/messaging-canon.php.
// Eine zweite Frist neben der Antwortzeit war zuletzt die einzige Quelle
// abweichender Werte auf den Energy-Routen.

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
		'marketcheck_data_groups'    => HU_MARKETCHECK_STEPS,
		'marketcheck_visible_steps'  => HU_MARKETCHECK_VISIBLE_STEPS,
		'marketcheck_fit_questions'  => HU_MARKETCHECK_FIT_QUESTIONS,
		'marketcheck_minutes'        => HU_MARKETCHECK_MINUTES,
		'marketcheck_reply_promise'  => hu_marketcheck_reply_label(),
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
 * Display value for the visible length of the marketcheck intake.
 *
 * Entry points on other routes make this promise before the visitor ever
 * sees the form. Public copy therefore follows the two rendered screens,
 * not the five internal data groups used by qualification and CRM storage.
 *
 * @return string
 */
function hu_marketcheck_length_label() {
	return sprintf( '%d sichtbare Schritte · %s', HU_MARKETCHECK_VISIBLE_STEPS, hu_marketcheck_duration_label() );
}

/**
 * Display value for the marketcheck reply promise.
 *
 * Gibt die kanonische Antwortzeit aus canon/messaging-canon.php zurueck. Die
 * Funktion bleibt als eigener Name bestehen, weil der Marktcheck ein eigener
 * Vorgang ist: ein haendisch geschriebener Befund, keine Antwort auf eine
 * E-Mail. Die Aufrufer rahmen ihn deshalb mit dem Wort "Befund". Der Wert
 * dahinter ist seit 2026-09-18 derselbe — eine zweite Frist neben der
 * Antwortzeit liest sich nicht als Praezision, sondern als Vorbehalt.
 *
 * Soll der Befund spaeter wieder eine eigene Frist bekommen, bekommt er hier
 * eine eigene Konstante. Ein Literal in einem Template ist nie die Antwort.
 *
 * @param bool $short Beibehalten fuer Aufrufer, die eine knappe Fassung wollen;
 *                    beide Varianten sind seit der Vereinheitlichung gleich.
 * @return string
 */
function hu_marketcheck_reply_label( $short = false ) {
	unset( $short );

	return hu_response_promise_short();
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
