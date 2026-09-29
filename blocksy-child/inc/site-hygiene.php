<?php
/**
 * One-time production hygiene migrations for site settings and legacy menus.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Apply the 2026-09-30 site hygiene migration once.
 *
 * - Store the canonical WordPress timezone as Europe/Berlin.
 * - Remove legacy database menus that are no longer rendered.
 * - Keep "Nexus Hauptmenü": it remains the deliberate backend/fallback menu
 *   rebuilt from the canonical navigation contract.
 *
 * @return void
 */
function hu_run_site_hygiene_20260930() {
	$option = 'hu_site_hygiene_20260930_v1';

	if ( '1' === (string) get_option( $option, '' ) ) {
		return;
	}

	if ( 'Europe/Berlin' !== (string) get_option( 'timezone_string', '' ) ) {
		update_option( 'timezone_string', 'Europe/Berlin', true );
	}

	$legacy_names = [
		'Footer',
		'Footer_Unterseiten',
		'Main',
		'Main-2025',
	];

	foreach ( (array) wp_get_nav_menus() as $menu ) {
		if ( ! ( $menu instanceof WP_Term ) ) {
			continue;
		}

		$name      = trim( (string) $menu->name );
		$term_id   = (int) $menu->term_id;
		$is_legacy = in_array( $name, $legacy_names, true );

		// Production-only orphan menu reported by WordPress as "(unbenannt)".
		// Its term ID is stable on this site and it is not referenced by code.
		if ( 55 === $term_id ) {
			$is_legacy = true;
		}

		if ( $is_legacy && 'Nexus Hauptmenü' !== $name ) {
			wp_delete_nav_menu( $term_id );
		}
	}

	update_option( $option, '1', false );
}
add_action( 'init', 'hu_run_site_hygiene_20260930', 90 );
