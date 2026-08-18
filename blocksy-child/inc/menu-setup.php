<?php
/**
 * NEXUS MENU SETUP
 *
 * Persistiert dieselbe strategische Navigation, die der öffentliche Header
 * direkt rendert. Das gespeicherte WordPress-Menü ist damit Fallback/Backend-
 * Zustand und keine zweite Quelle der Informationsarchitektur mehr.
 *
 * Einmal-Setup: Wird beim Theme-Switch oder manuell via
 * ?nexus_rebuild_menu=1 ausgelöst.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detect a legacy conversion CTA item.
 *
 * Der historische Funktionsname bleibt für interne Verbraucher stabil. Neue
 * globale CTAs werden als Projektanfrage normalisiert; Marktcheck bleibt nur
 * in expliziten Energy-Kontexten.
 *
 * @param WP_Post $item Menu item object.
 * @return bool
 */
function nexus_is_audit_cta_menu_item( $item ) {
	$classes = isset( $item->classes ) && is_array( $item->classes ) ? $item->classes : [];
	$title   = isset( $item->title ) ? wp_strip_all_tags( (string) $item->title ) : '';
	$url     = isset( $item->url ) ? (string) $item->url : '';
	$path    = $url ? wp_parse_url( $url, PHP_URL_PATH ) : '';

	if ( in_array( 'nav-cta-button', $classes, true ) || in_array( 'nav-project-link', $classes, true ) ) {
		return true;
	}

	$legacy_paths = [
		'/audit/',
		'/customer-journey-audit/',
		'/growth-audit/',
		'/360-audit/',
		'/system-diagnose/',
	];

	if ( $path && in_array( trailingslashit( $path ), $legacy_paths, true ) ) {
		return true;
	}

	$title = strtolower( $title );

	return false !== strpos( $title, 'journey audit' )
		|| false !== strpos( $title, 'growth audit' )
		|| false !== strpos( $title, 'free journey audit' )
		|| false !== strpos( $title, 'system-diagnose' )
		|| false !== strpos( $title, 'marktcheck' )
		|| false !== strpos( $title, 'projekt anfragen' );
}

/**
 * Detect results/proof menu items even if the stored WordPress label is outdated.
 *
 * @param WP_Post $item Menu item object.
 * @return bool
 */
function nexus_is_results_menu_item( $item ) {
	$classes = isset( $item->classes ) && is_array( $item->classes ) ? $item->classes : [];
	$title   = isset( $item->title ) ? wp_strip_all_tags( (string) $item->title ) : '';
	$url     = isset( $item->url ) ? (string) $item->url : '';
	$path    = $url ? wp_parse_url( $url, PHP_URL_PATH ) : '';

	if ( in_array( 'nav-results-link', $classes, true ) ) {
		return true;
	}

	$results_paths = [
		'/case-studies/',
		'/case-studies-e-commerce/',
		'/ergebnisse/',
		'/case-study-solar-leadgenerierung/',
		'/e3-new-energy/',
	];

	if ( $path && in_array( trailingslashit( $path ), $results_paths, true ) ) {
		return true;
	}

	$title = strtolower( $title );

	return false !== strpos( $title, 'case stud' )
		|| false !== strpos( $title, 'ergebnisse' )
		|| false !== strpos( $title, 'proof' );
}

/**
 * Return a safe navigation fallback if the canonical routing module is absent.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_get_menu_setup_fallback_contract() {
	$project_url = function_exists( 'hu_get_navigation_project_request_url' )
		? hu_get_navigation_project_request_url()
		: add_query_arg( [ 'type' => 'project', 'focus' => 'implementation_scope' ], home_url( '/kontakt/' ) );

	return [
		[
			'label' => 'Solar & Wärmepumpen',
			'url'   => home_url( '/solar-waermepumpen-leadgenerierung/' ),
			'class' => 'nav-solar-link',
		],
		[
			'label' => 'WordPress Freelancer',
			'url'   => home_url( '/wordpress-freelancer-hannover/' ),
			'class' => 'nav-freelancer-link',
		],
		[
			'label' => 'Für Agenturen',
			'url'   => home_url( '/whitelabel-retainer/' ),
			'class' => 'nav-agency-link',
		],
		[
			'label' => 'Ergebnisse',
			'url'   => home_url( '/ergebnisse/' ),
			'class' => 'nav-results-link',
		],
		[
			'label' => 'Über Haşim',
			'url'   => home_url( '/hasim-uener/' ),
			'class' => 'nav-about-link',
		],
		[
			'label' => 'Projekt anfragen',
			'url'   => $project_url,
			'class' => 'nav-cta-button nav-project-link',
		],
	];
}

/**
 * Create the stored WordPress menu from the canonical navigation contract.
 *
 * @return void
 */
function nexus_setup_main_menu() {
	$menu_name = 'Nexus Hauptmenü';
	$contract  = function_exists( 'hu_get_primary_navigation_contract' )
		? hu_get_primary_navigation_contract()
		: nexus_get_menu_setup_fallback_contract();

	$existing = wp_get_nav_menu_object( $menu_name );
	if ( $existing ) {
		wp_delete_nav_menu( $existing->term_id );
	}

	$menu_id = wp_create_nav_menu( $menu_name );
	if ( is_wp_error( $menu_id ) ) {
		return;
	}

	foreach ( $contract as $item ) {
		$title   = trim( (string) ( $item['label'] ?? '' ) );
		$url     = (string) ( $item['url'] ?? '' );
		$classes = trim( (string) ( $item['class'] ?? '' ) );

		if ( '' === $title || '' === $url ) {
			continue;
		}

		wp_update_nav_menu_item(
			$menu_id,
			0,
			[
				'menu-item-title'   => $title,
				'menu-item-object'  => 'custom',
				'menu-item-type'    => 'custom',
				'menu-item-url'     => $url,
				'menu-item-status'  => 'publish',
				'menu-item-classes' => $classes,
			]
		);
	}

	$locations = get_theme_mod( 'nav_menu_locations', [] );
	$locations['primary']      = $menu_id;
	$locations['primary-slim'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

add_action( 'after_switch_theme', 'nexus_setup_main_menu' );

/**
 * Create proof/agency hub pages that are routed via page-slug templates.
 *
 * Triggered manually for admins so production content does not change
 * unexpectedly.
 *
 * @return void
 */
function nexus_seed_results_pages() {
	$pages = [
		[
			'slug'   => 'ergebnisse',
			'title'  => 'Ergebnisse',
			'update' => [ 'Case Studies', 'Case Studies E-Commerce', 'Results', 'Ergebnisse' ],
		],
		[
			'slug'   => 'whitelabel-retainer',
			'title'  => 'Whitelabel & Weiterentwicklung',
			'update' => [],
		],
	];

	foreach ( $pages as $page ) {
		$existing = get_page_by_path( $page['slug'] );

		if ( $existing instanceof WP_Post ) {
			if ( in_array( $existing->post_title, $page['update'], true ) ) {
				wp_update_post(
					[
						'ID'         => $existing->ID,
						'post_title' => $page['title'],
					]
				);
			}

			continue;
		}

		wp_insert_post(
			[
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['title'],
				'post_name'    => $page['slug'],
				'post_content' => '',
			]
		);
	}
}

// Manual admin maintenance routes.
add_action( 'admin_init', function () {
	if (
		isset( $_GET['nexus_rebuild_menu'] ) &&
		'1' === $_GET['nexus_rebuild_menu'] &&
		current_user_can( 'manage_options' )
	) {
		nexus_setup_main_menu();
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-success is-dismissible"><p>Nexus Hauptmenü wurde aus dem zentralen Routing-Contract erstellt.</p></div>';
		} );
	}

	if (
		isset( $_GET['nexus_seed_results_pages'] ) &&
		'1' === $_GET['nexus_seed_results_pages'] &&
		current_user_can( 'manage_options' )
	) {
		nexus_seed_results_pages();
		nexus_setup_main_menu();
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-success is-dismissible"><p>Ergebnisse- und Whitelabel-Seiten wurden angelegt bzw. aktualisiert.</p></div>';
		} );
	}
} );
