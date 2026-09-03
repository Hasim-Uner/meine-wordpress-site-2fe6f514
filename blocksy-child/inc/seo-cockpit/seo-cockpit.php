<?php
/**
 * SEO Cockpit bootstrap loader.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// SEO Cockpit ist ein Admin-/Background-System — Frontend-Requests bleiben leicht.
if ( ! is_admin() && ! wp_doing_ajax() && ! wp_doing_cron() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
	return;
}

$nexus_seo_cockpit_modules = [
	'seo-cockpit-core.php',
	'seo-cockpit-api.php',
	'seo-cockpit-http-compat.php',
	'seo-cockpit-search-console-control.php',
	'seo-cockpit-koko.php',
	'seo-cockpit-links.php',
	'seo-cockpit-leads.php',
	'seo-cockpit-sync.php',
	'seo-cockpit-insights.php',
	'seo-cockpit-diagnostics.php',
	'seo-cockpit-command-center.php',
	'seo-cockpit-export.php',
	'seo-cockpit-ui.php',
	'seo-cockpit-dashboard-v3.php',
];

foreach ( $nexus_seo_cockpit_modules as $nexus_seo_cockpit_module ) {
	$nexus_seo_cockpit_path = __DIR__ . '/' . $nexus_seo_cockpit_module;

	if ( file_exists( $nexus_seo_cockpit_path ) ) {
		require_once $nexus_seo_cockpit_path;
	}
}

unset( $nexus_seo_cockpit_modules, $nexus_seo_cockpit_module, $nexus_seo_cockpit_path );
