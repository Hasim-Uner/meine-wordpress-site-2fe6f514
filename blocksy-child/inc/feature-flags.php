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

// Keep the one-time editor-content migrations isolated in small modules. The
// loaders are intentionally tiny; migrations themselves run later on init.
$hu_article_content_hygiene_paths = [
	__DIR__ . '/article-content-hygiene.php',
	__DIR__ . '/article-content-hygiene-ttfb.php',
	__DIR__ . '/article-content-hygiene-landingpage.php',
	__DIR__ . '/article-b2b-inquiry-system.php',
	__DIR__ . '/article-agency-outsourcing.php',
	__DIR__ . '/article-agency-outsourcing-hero.php',
];

foreach ( $hu_article_content_hygiene_paths as $hu_article_content_hygiene_path ) {
	if ( file_exists( $hu_article_content_hygiene_path ) ) {
		require_once $hu_article_content_hygiene_path;
	}
}

// Reader UI is runtime behaviour rather than editor-content migration. Load
// the module early so it can register its conditional wp_enqueue_scripts hook.
$hu_article_reader_toc_path = __DIR__ . '/article-reader-toc.php';
if ( file_exists( $hu_article_reader_toc_path ) ) {
	require_once $hu_article_reader_toc_path;
}
