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

// Keep the one-time editor-content migration isolated in its own module. This
// early loader is intentionally tiny; the migration itself runs later on init.
$hu_article_content_hygiene_path = __DIR__ . '/article-content-hygiene.php';
if ( file_exists( $hu_article_content_hygiene_path ) ) {
	require_once $hu_article_content_hygiene_path;
}
