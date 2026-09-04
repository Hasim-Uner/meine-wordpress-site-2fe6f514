<?php
/**
 * NEXUS Custom Header
 *
 * Rendert einen projekt-eigenen Header im Child-Theme und blendet den
 * Blocksy Header visuell aus, damit Navigation, Sticky-Verhalten und
 * Mobile-Menü zentral gesteuert werden.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Der projekt-eigene Header wird weiter unten über `wp_body_open` gerendert.
// Den Blocksy-Parent-Header serverseitig abschalten, damit dessen per CSS
// versteckte Navigation und Offcanvas-Panels nicht zusätzlich im HTML landen.
add_filter( 'blocksy:builder:header:enabled', '__return_false' );

/**
 * Detect the blog area that uses the dedicated blog header template.
 *
 * @return bool
 */
function nexus_is_blog_header_context() {
	return ( is_archive() || is_singular( 'post' ) ) && ! is_home();
}

add_filter( 'body_class', 'nexus_add_custom_header_body_class' );
/**
 * Mark the frontend so the custom header CSS can disable the theme header.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function nexus_add_custom_header_body_class( $classes ) {
	if ( is_admin() ) {
		return $classes;
	}

	if ( nexus_is_blog_header_context() ) {
		$classes[] = 'nx-blog-header-active';
		return $classes;
	}

	$classes[] = 'nx-custom-header-active';

	return $classes;
}

add_action( 'wp_body_open', 'nexus_render_site_header', 20 );
/**
 * Render the custom global site header once per request.
 *
 * @return void
 */
function nexus_render_site_header() {
	static $rendered = false;

	if ( $rendered || is_admin() || is_feed() ) {
		return;
	}

	if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
		return;
	}

	if ( nexus_is_blog_header_context() ) {
		return;
	}

	// Die Solar-Money-Page nutzt ihre eigene sticky Sprungnavigation.
	if ( function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context() ) {
		return;
	}

	if ( function_exists( 'hu_is_energy_demo_request_path' ) && hu_is_energy_demo_request_path() ) {
		return;
	}

	$rendered = true;

	get_template_part( 'template-parts/site-header' );
}

/**
 * Resolve the preferred nav location for the custom header.
 *
 * @return string
 */
function nexus_get_site_header_menu_location() {
	$locations = [ 'primary-slim', 'primary' ];

	foreach ( $locations as $location ) {
		if ( has_nav_menu( $location ) ) {
			return $location;
		}
	}

	return '';
}

/**
 * Check whether wp_nav_menu args target the primary header navigation.
 *
 * @param stdClass|array|string $args wp_nav_menu arguments.
 * @return bool
 */
function nexus_is_primary_header_menu_args( $args ) {
	if ( ! is_object( $args ) || empty( $args->theme_location ) ) {
		return false;
	}

	return in_array( (string) $args->theme_location, [ 'primary-slim', 'primary' ], true );
}

/**
 * Provide a sane navigation fallback if no WordPress menu is assigned.
 *
 * Reads the one canonical navigation contract instead of keeping a second
 * list. The hand-maintained copy here still carried the pre-repositioning
 * navigation — "WordPress Agentur" as a main entry and "Marktcheck · 48 h"
 * as the CTA. The header stopped calling it, but
 * inc/seo-cockpit/seo-cockpit-links.php still reads it and turns it into
 * internal link suggestions, so the retired navigation kept propagating from
 * there.
 *
 * The shape (label/url/active/class/track) is kept for existing callers.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_get_site_header_fallback_items() {
	if ( ! function_exists( 'hu_get_primary_navigation_contract' ) ) {
		return [];
	}

	$items = [];

	foreach ( hu_get_primary_navigation_contract() as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$item['label']    = (string) ( $item['label'] ?? '' );
		$item['url']      = (string) ( $item['url'] ?? '' );
		$item['active']   = ! empty( $item['current'] );
		$item['class']    = (string) ( $item['class'] ?? '' );
		$item['track']    = (string) ( $item['track'] ?? '' );
		$item['category'] = (string) ( $item['category'] ?? 'navigation' );
		$item['section']  = (string) ( $item['section'] ?? 'header' );
		$item['kind']     = (string) ( $item['kind'] ?? 'group' );
		$item['kicker']   = (string) ( $item['kicker'] ?? '' );
		$item['desc']     = (string) ( $item['desc'] ?? '' );

		$items[] = $item;
	}

	return $items;
}

/**
 * Check whether the current page is the energy systems landing page.
 *
 * @return bool
 */
function nexus_is_energy_systems_context() {
	return is_page( 'solar-waermepumpen-leadgenerierung' )
		|| is_page( 'website-fuer-solar-und-waermepumpen-anbieter' )
		|| is_page_template( 'page-solar-waermepumpen-leadgenerierung.php' );
}

/**
 * Check whether the current request is the agency (white-label) route.
 *
 * @return bool
 */
function nexus_is_agency_nav_context() {
	return is_page( 'whitelabel-retainer' )
		|| is_page( 'whitelabel-retainer-proof' )
		|| is_page( 'whitelabel' )
		|| is_page_template( 'page-whitelabel-retainer.php' );
}

/*
 * nexus_render_site_header_menu() stand hier bis zur Repositionierung und ist
 * entfallen. template-parts/site-header.php rendert die Navigation seit dem
 * Vollflächen-Sheet selbst aus hu_get_primary_navigation_contract(), und die
 * Funktion hatte danach keinen Aufrufer mehr. Als zweiter Renderer mit eigener
 * Markup-Variante war sie genau der Weg, auf dem die alte Navigation
 * zurueckkommen konnte.
 */

/**
 * Remove side-funnel destinations from the primary header navigation.
 *
 * @param array           $items Sorted menu item objects.
 * @param stdClass|string $args  Menu arguments.
 * @return array
 */
function nexus_strip_side_funnel_nav_items( $items, $args ) {
	if ( is_admin() || ! nexus_is_primary_header_menu_args( $args ) ) {
		return $items;
	}

	// '/whitelabel-retainer/' steht bewusst nicht mehr hier: die Agenturseite
	// ist jetzt ein regulärer Navigationspunkt, kein Footer-only-Nebenpfad.
	$blocked_paths = [
		'/core-web-vitals/',
		'/conversion-rate-optimization/',
		'/wordpress-seo-hannover/',
		'/wordpress-growth-operating-system/',
		'/wgos-systemlandkarte/',
		'/kostenlose-tools/',
		'/tools/',
		'/website-performance-analyse/',
	];

	$filtered_items = [];

	foreach ( $items as $item ) {
		$item_url  = isset( $item->url ) ? (string) $item->url : '';
		$item_path = (string) wp_parse_url( $item_url, PHP_URL_PATH );
		$item_classes = isset( $item->classes ) && is_array( $item->classes ) ? $item->classes : [];

		if ( '' !== $item_path ) {
			$item_path = trailingslashit( untrailingslashit( $item_path ) );
		}

		if ( in_array( $item_path, $blocked_paths, true ) && ! in_array( 'nav-cta-button', $item_classes, true ) ) {
			continue;
		}

		$filtered_items[] = $item;
	}

	return $filtered_items;
}
add_filter( 'wp_nav_menu_objects', 'nexus_strip_side_funnel_nav_items', 10, 2 );

/**
 * Keep the agency entry point in the primary header navigation.
 *
 * Das gespeicherte WordPress-Menü wird nur beim Theme-Wechsel oder über
 * `?nexus_rebuild_menu=1` neu aufgebaut. Damit "Für Agenturen" nicht von einem
 * Admin-Schritt abhängt, wird der Punkt hier ergänzt, falls kein Menüeintrag
 * bereits auf die Route zeigt. Er steht vor dem CTA-Button, damit der Marktcheck
 * der letzte Punkt der Leiste bleibt.
 *
 * @param array           $items Sorted menu item objects.
 * @param stdClass|string $args  Menu arguments.
 * @return array
 */
function nexus_ensure_agency_nav_item( $items, $args ) {
	if ( is_admin() || ! nexus_is_primary_header_menu_args( $args ) || ! is_array( $items ) ) {
		return $items;
	}

	$agency_url  = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );
	$agency_path = (string) wp_parse_url( $agency_url, PHP_URL_PATH );
	$agency_path = '' !== $agency_path ? trailingslashit( untrailingslashit( $agency_path ) ) : '/whitelabel-retainer/';
	$cta_index   = null;

	// WordPress schlüsselt die Menüpunkte nach `menu_order`, nicht nach
	// Position. Erst neu indizieren, damit der gefundene Index auch der
	// Offset für array_splice() ist.
	$items = array_values( $items );

	foreach ( $items as $index => $item ) {
		$item_url  = isset( $item->url ) ? (string) $item->url : '';
		$item_path = (string) wp_parse_url( $item_url, PHP_URL_PATH );

		if ( '' !== $item_path && trailingslashit( untrailingslashit( $item_path ) ) === $agency_path ) {
			return $items;
		}

		$item_classes = isset( $item->classes ) && is_array( $item->classes ) ? $item->classes : [];
		if ( null === $cta_index && in_array( 'nav-cta-button', $item_classes, true ) ) {
			$cta_index = (int) $index;
		}
	}

	$is_current  = nexus_is_agency_nav_context();
	$agency_item = (object) [
		'ID'                    => 0,
		'db_id'                 => 0,
		'menu_item_parent'      => 0,
		'object_id'             => 0,
		'object'                => 'custom',
		'type'                  => 'custom',
		'type_label'            => 'Custom Link',
		'title'                 => __( 'Für Agenturen', 'blocksy-child' ),
		'url'                   => $agency_url,
		'target'                => '',
		'attr_title'            => '',
		'description'           => '',
		'xfn'                   => '',
		'classes'               => $is_current
			? [ 'menu-item', 'nav-agency-link', 'current-menu-item', 'current_page_item' ]
			: [ 'menu-item', 'nav-agency-link' ],
		'current'               => $is_current,
		'current_item_ancestor' => false,
		'current_item_parent'   => false,
		'menu_order'            => 0,
		'post_type'             => 'nav_menu_item',
		'post_status'           => 'publish',
	];

	if ( null === $cta_index ) {
		$items[] = $agency_item;

		return $items;
	}

	array_splice( $items, $cta_index, 0, [ $agency_item ] );

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'nexus_ensure_agency_nav_item', 25, 2 );

/**
 * Repair legacy primary-menu placeholders and dirty editor URLs at render time.
 *
 * Group headings remain visible as non-interactive labels. Ambiguous leaf items
 * without a reliable destination are removed, while known legacy page IDs are
 * normalized to the canonical public route map.
 *
 * @param array           $items Sorted menu item objects.
 * @param stdClass|string $args  Menu arguments.
 * @return array
 */
function nexus_repair_primary_header_menu_items( $items, $args ) {
	if ( is_admin() || ! nexus_is_primary_header_menu_args( $args ) ) {
		return $items;
	}

	$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
	$group_labels = [ 'Leistungen', 'Build & Care', 'Sichtbarkeit (SEO)', 'Ressourcen' ];
	$remove_items = [ 'Performance', 'Tracking & CRO' ];
	$clean_urls   = [
		'13035' => $primary_urls['seo'] ?? home_url( '/wordpress-agentur-hannover/#technisches-seo' ),
		'14283' => $primary_urls['results'] ?? home_url( '/ergebnisse/' ),
	];
	$filtered_items = [];

	foreach ( $items as $item ) {
		$title      = isset( $item->title ) ? trim( wp_strip_all_tags( (string) $item->title ) ) : '';
		$item_url   = isset( $item->url ) ? trim( (string) $item->url ) : '';
		$item_query = (string) wp_parse_url( $item_url, PHP_URL_QUERY );
		$query_args = [];
		wp_parse_str( $item_query, $query_args );
		$legacy_id = isset( $query_args['page_id'] ) ? (string) absint( $query_args['page_id'] ) : '';

		if ( in_array( $title, $remove_items, true ) ) {
			continue;
		}

		if ( 'WordPress SEO' === $title || '13035' === $legacy_id ) {
			$item->url = $clean_urls['13035'];
		}

		if ( 'E-Commerce Growth' === $title || '14283' === $legacy_id ) {
			$item->url   = $clean_urls['14283'];
			$item->title = __( 'Ergebnisse', 'blocksy-child' );
		}

		if ( in_array( $title, $group_labels, true ) ) {
			$item->url = '';
			$item->classes = isset( $item->classes ) && is_array( $item->classes ) ? $item->classes : [];
			$item->classes[] = 'nx-menu-label-item';
		}

		$filtered_items[] = $item;
	}

	return $filtered_items;
}
add_filter( 'wp_nav_menu_objects', 'nexus_repair_primary_header_menu_items', 15, 2 );

/**
 * Render primary-menu group headings without a fake link destination.
 *
 * @param string   $item_output Existing walker output.
 * @param WP_Post  $menu_item   Menu item object.
 * @param int      $depth       Menu depth.
 * @param stdClass $args        Menu arguments.
 * @return string
 */
function nexus_render_primary_header_menu_label( $item_output, $menu_item, $depth, $args ) {
	if ( ! nexus_is_primary_header_menu_args( $args ) ) {
		return $item_output;
	}

	$item_classes = isset( $menu_item->classes ) && is_array( $menu_item->classes ) ? $menu_item->classes : [];
	if ( ! in_array( 'nx-menu-label-item', $item_classes, true ) ) {
		return $item_output;
	}

	$before = isset( $args->before ) ? (string) $args->before : '';
	$after  = isset( $args->after ) ? (string) $args->after : '';
	$label  = isset( $menu_item->title ) ? wp_strip_all_tags( (string) $menu_item->title ) : '';

	return $before . '<span class="nx-site-header__menu-label">' . esc_html( $label ) . '</span>' . $after;
}
add_filter( 'walker_nav_menu_start_el', 'nexus_render_primary_header_menu_label', 10, 4 );

/**
 * Redirect the two legacy menu page-ID URLs to their canonical public routes.
 *
 * @return void
 */
function nexus_redirect_legacy_menu_page_ids() {
	if ( is_admin() || ! isset( $_GET['page_id'] ) ) {
		return;
	}
	if ( ! is_scalar( $_GET['page_id'] ) ) {
		return;
	}

	$page_id = absint( wp_unslash( $_GET['page_id'] ) );
	if ( ! in_array( $page_id, [ 13035, 14283 ], true ) ) {
		return;
	}

	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$request_path = (string) wp_parse_url( $request_uri, PHP_URL_PATH );
	$home_path    = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( untrailingslashit( $request_path ) !== untrailingslashit( $home_path ) ) {
		return;
	}

	$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
	$redirects    = [
		13035 => $primary_urls['seo'] ?? home_url( '/wordpress-agentur-hannover/#technisches-seo' ),
		14283 => $primary_urls['results'] ?? home_url( '/ergebnisse/' ),
	];

	wp_safe_redirect( $redirects[ $page_id ], 301, 'Nexus Legacy Menu Permalink' );
	exit;
}
add_action( 'template_redirect', 'nexus_redirect_legacy_menu_page_ids', 1 );

/**
 * Swap legacy nav CTAs to the current analysis entry when a WordPress menu is assigned.
 *
 * Nur im Energy-Kontext. Der Filter schreibt jeden Menü-CTA, dessen Titel auf
 * der Liste unten steht, auf den Marktcheck um — darunter allgemeine Titel wie
 * "Direkt anfragen", "Anfrage stellen" und "Audit starten". Seit der
 * Repositionierung heisst die globale CTA "Projekt anfragen" und steht nicht
 * auf der Liste; der Filter griff deshalb faktisch nicht mehr. Bei der
 * naechsten Menuepflege haette schon ein "Anfrage stellen" gereicht, um die
 * sitewide CTA wieder in den Solar-Trichter zu haengen.
 *
 * @param array           $items Sorted menu item objects.
 * @param stdClass|string $args  Menu arguments.
 * @return array
 */
function nexus_energy_nav_cta_label( $items, $args ) {
	if ( ! nexus_is_primary_header_menu_args( $args ) ) {
		return $items;
	}

	if ( ! function_exists( 'nexus_is_energy_systems_context' ) || ! nexus_is_energy_systems_context() ) {
		return $items;
	}

	$request_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
	$request_cta = 'Marktcheck · 48 h';

	foreach ( $items as $item ) {
		$legacy_analysis_label = 'Analyse ' . 'starten';
		$legacy_diagnose_label = 'System-Diagnose ' . 'starten';
		$legacy_diagnose_request_label = 'System-Diagnose ' . 'anfragen';
		$legacy_fast_marketcheck_label = implode( ' ', [ 'Marktcheck ·', '48', 'h' ] );
		$legacy_business_days_marketcheck_label = implode( ' ', [ 'Marktcheck ·', '2', 'Werktage' ] );
		if ( in_array( $item->title, [ $legacy_analysis_label, $legacy_diagnose_label, 'System-Diagnose', 'Marktcheck', 'Marktcheck · 60 Sek.', $legacy_fast_marketcheck_label, $legacy_business_days_marketcheck_label, 'Marktcheck · 48 h', 'Audit starten', $legacy_diagnose_request_label, 'Audit', 'AI-Audit', 'Anfrage stellen', 'Direkt anfragen' ], true ) ) {
			$item->title = $request_cta;
			$item->url   = $request_url;
			break;
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'nexus_energy_nav_cta_label', 20, 2 );

/**
 * Resolve the compact header eyebrow text.
 *
 * @return string
 */
function nexus_get_site_header_eyebrow() {
	return '';
}
