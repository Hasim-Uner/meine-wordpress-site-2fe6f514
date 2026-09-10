<?php
/** Nexus CRM sales operations bootstrap. @package Blocksy_Child */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$nexus_crm_sales_modules = [
	'core.php',
	'sync.php',
	'activity.php',
	'actions.php',
	'contact-admin.php',
	'ui.php',
];

foreach ( $nexus_crm_sales_modules as $nexus_crm_sales_module ) {
	$nexus_crm_sales_path = __DIR__ . '/crm-sales/' . $nexus_crm_sales_module;
	if ( file_exists( $nexus_crm_sales_path ) ) {
		require_once $nexus_crm_sales_path;
	}
}

unset( $nexus_crm_sales_modules, $nexus_crm_sales_module, $nexus_crm_sales_path );
