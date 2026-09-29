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
 * Identify the one historical unnamed menu seen on production.
 *
 * The legacy term ID alone is not sufficient because database IDs can differ
 * between environments. Require the exact known item-title fingerprint too.
 *
 * @param WP_Term $menu Navigation menu term.
 * @return bool
 */
function hu_is_legacy_unnamed_menu_20260930( $menu ) {
	if ( ! ( $menu instanceof WP_Term ) || 55 !== (int) $menu->term_id ) {
		return false;
	}

	$items = wp_get_nav_menu_items( (int) $menu->term_id );
	if ( ! is_array( $items ) ) {
		return false;
	}

	$titles = [];
	foreach ( $items as $item ) {
		if ( is_object( $item ) && isset( $item->title ) ) {
			$titles[] = trim( wp_strip_all_tags( (string) $item->title ) );
		}
	}

	sort( $titles, SORT_STRING );

	$expected = [
		'Blog',
		'SEO & Marketing',
		'Shopify',
		'WordPress',
		'Über mich',
	];
	sort( $expected, SORT_STRING );

	return $expected === $titles;
}

/**
 * Apply the 2026-09-30 site hygiene migration once.
 *
 * - Store the canonical WordPress timezone as Europe/Berlin.
 * - Remove legacy database menus that are no longer rendered.
 * - Keep "Nexus Hauptmenü": it remains the deliberate backend/fallback menu
 *   rebuilt from the canonical navigation contract.
 *
 * The completion flag is written only after every requested mutation is
 * verifiably complete, so a transient failure is retried on the next request.
 *
 * @return void
 */
function hu_run_site_hygiene_20260930() {
	$option = 'hu_site_hygiene_20260930_v1';

	if ( '1' === (string) get_option( $option, '' ) ) {
		return;
	}

	$complete = true;

	if ( 'Europe/Berlin' !== (string) get_option( 'timezone_string', '' ) ) {
		update_option( 'timezone_string', 'Europe/Berlin', true );

		if ( 'Europe/Berlin' !== (string) get_option( 'timezone_string', '' ) ) {
			$complete = false;
		}
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
		$is_legacy = in_array( $name, $legacy_names, true )
			|| hu_is_legacy_unnamed_menu_20260930( $menu );

		if ( ! $is_legacy || 'Nexus Hauptmenü' === $name ) {
			continue;
		}

		$deleted = wp_delete_nav_menu( $term_id );
		if ( false === $deleted || is_wp_error( $deleted ) ) {
			$complete = false;
		}
	}

	if ( $complete ) {
		update_option( $option, '1', false );
	}
}
add_action( 'init', 'hu_run_site_hygiene_20260930', 90 );
