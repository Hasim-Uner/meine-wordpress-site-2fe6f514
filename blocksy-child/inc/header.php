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
 * @return array<int, array<string, mixed>>
 */
function nexus_get_site_header_fallback_items() {
	$solar_page_id = nexus_get_page_id( [ 'solar-waermepumpen-leadgenerierung' ] );
	$about_page_id = nexus_get_page_id( [ 'uber-mich' ] );
	$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
	$request_url  = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#energie-anfrage' );
	$request_cta  = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Anfrage stellen';

	return [
		[
			'label'  => __( 'Solar & Wärmepumpen', 'blocksy-child' ),
			'url'    => $solar_page_id ? get_permalink( $solar_page_id ) : home_url( '/solar-waermepumpen-leadgenerierung/' ),
			'active' => $solar_page_id ? is_page( $solar_page_id ) : false,
			'class'  => '',
			'track'  => 'solar',
		],
		[
			'label'  => __( 'Ergebnisse', 'blocksy-child' ),
			'url'    => nexus_get_results_url(),
			'active' => nexus_is_results_context(),
			'class'  => '',
			'track'  => 'results',
		],
		[
			'label'  => __( 'Über mich', 'blocksy-child' ),
			'url'    => $primary_urls['about'] ?? home_url( '/uber-mich/' ),
			'active' => $about_page_id ? is_page( $about_page_id ) : false,
			'class'  => '',
			'track'  => 'about',
		],
		[
			'label'  => $request_cta,
			'url'    => $request_url,
			'active' => false,
			'class'  => 'nav-cta-button',
			'track'  => 'request',
		],
	];
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
 * Render the menu markup for desktop or mobile header contexts.
 *
 * @param string $context Menu context, e.g. desktop or mobile.
 * @return void
 */
function nexus_render_site_header_menu( $context = 'desktop' ) {
	$context    = sanitize_key( $context );
	$location   = nexus_get_site_header_menu_location();
	$menu_class = 'nx-site-header__menu nx-site-header__menu--' . $context;

	if ( $location ) {
		wp_nav_menu(
			[
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $menu_class,
				'fallback_cb'    => false,
				'depth'          => 2,
			]
		);

		return;
	}

	$fallback_items = nexus_get_site_header_fallback_items();

	if ( empty( $fallback_items ) ) {
		return;
	}

	echo '<ul class="' . esc_attr( $menu_class ) . '">';

	foreach ( $fallback_items as $item ) {
		$item_class = ! empty( $item['class'] ) ? ' ' . sanitize_html_class( $item['class'] ) : '';
		$link_class = ! empty( $item['active'] ) ? ' aria-current="page"' : '';
		$track_attr = ! empty( $item['track'] ) ? sprintf( ' data-track-action="nav_header_%s" data-track-category="navigation"', esc_attr( $item['track'] ) ) : '';

		printf(
			'<li class="menu-item%1$s"><a href="%2$s"%3$s%4$s>%5$s</a></li>',
			esc_attr( $item_class ),
			esc_url( $item['url'] ),
			$link_class,
			$track_attr,
			esc_html( $item['label'] )
		);
	}

	echo '</ul>';
}

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

	$blocked_paths = [
		'/whitelabel-retainer/',
		'/wordpress-agentur-hannover/',
	];

	$filtered_items = [];

	foreach ( $items as $item ) {
		$item_url  = isset( $item->url ) ? (string) $item->url : '';
		$item_path = (string) wp_parse_url( $item_url, PHP_URL_PATH );

		if ( '' !== $item_path ) {
			$item_path = trailingslashit( untrailingslashit( $item_path ) );
		}

		if ( in_array( $item_path, $blocked_paths, true ) ) {
			continue;
		}

		$filtered_items[] = $item;
	}

	return $filtered_items;
}
add_filter( 'wp_nav_menu_objects', 'nexus_strip_side_funnel_nav_items', 10, 2 );

/**
 * Swap the nav CTA label on the energy systems landing page when a WordPress
 * menu is assigned (wp_nav_menu path).
 *
 * @param array           $items Sorted menu item objects.
 * @param stdClass|string $args  Menu arguments.
 * @return array
 */
function nexus_energy_nav_cta_label( $items, $args ) {
	if ( ! nexus_is_primary_header_menu_args( $args ) ) {
		return $items;
	}

	$request_url = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#energie-anfrage' );
	$request_cta = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Anfrage stellen';

	foreach ( $items as $item ) {
		if ( in_array( $item->title, ['Audit starten', 'System-Diagnose', 'System-Diagnose starten', 'Audit', 'AI-Audit'], true ) ) {
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
	if ( function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context() ) {
		return '';
	}

	$description = trim( (string) get_bloginfo( 'description' ) );

	if ( '' !== $description ) {
		return $description;
	}

	return __( 'Anfrage-Systeme für B2B. Spezialisiert auf Solar & Wärmepumpen.', 'blocksy-child' );
}
