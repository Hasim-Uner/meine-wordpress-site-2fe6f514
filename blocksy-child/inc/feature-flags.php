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
defined( 'HU_FEATURE_READINESS_SUBMIT' ) || define( 'HU_FEATURE_READINESS_SUBMIT', true );
defined( 'HU_FEATURE_ENERGY_DEMO_ROUTE' ) || define( 'HU_FEATURE_ENERGY_DEMO_ROUTE', true );

// Keep the one-time editor-content migrations isolated in small modules. The
// loaders are intentionally tiny; migrations themselves run later on init.
$hu_article_content_hygiene_paths = [
	__DIR__ . '/article-content-hygiene.php',
	__DIR__ . '/article-content-hygiene-ttfb.php',
	__DIR__ . '/article-content-hygiene-landingpage.php',
];

foreach ( $hu_article_content_hygiene_paths as $hu_article_content_hygiene_path ) {
	if ( file_exists( $hu_article_content_hygiene_path ) ) {
		require_once $hu_article_content_hygiene_path;
	}
}
