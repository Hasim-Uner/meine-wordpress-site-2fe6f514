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
