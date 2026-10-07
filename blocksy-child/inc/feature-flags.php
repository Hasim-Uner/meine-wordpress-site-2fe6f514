<?php
/**
 * Runtime feature flags for staged funnel rollout.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

defined( 'HU_FEATURE_READINESS_DIAGNOSIS_ROUTE' ) || define( 'HU_FEATURE_READINESS_DIAGNOSIS_ROUTE', true );
// /wp-json/nexus/v1/analysis-submit ist seit 2026-09-22 standardmäßig aus:
// Die Analyse ist durch den Marktcheck ersetzt, kein Formular sendet mehr
// dorthin, und der offene Endpoint verschickte Bestätigungsmails an jede
// eingetragene Adresse. Wieder einschalten nur per wp-config.php.
defined( 'HU_FEATURE_READINESS_SUBMIT' ) || define( 'HU_FEATURE_READINESS_SUBMIT', false );
defined( 'HU_FEATURE_ENERGY_DEMO_ROUTE' ) || define( 'HU_FEATURE_ENERGY_DEMO_ROUTE', true );
// Legacy-Schalter fuer den Analyse-Anker der Solar-Seite. Analyse ist seit
// Smartflow kein sichtbares Energy-Produkt mehr; Sofortkontakt fuehrt immer
// auf den gescopten Kontakt-Intake. Umschalten nur per wp-config.php, falls die
// Anker live sind:
//
//   define( 'HU_FEATURE_SOLAR_DOORS', true );
defined( 'HU_FEATURE_SOLAR_DOORS' ) || define( 'HU_FEATURE_SOLAR_DOORS', false );
