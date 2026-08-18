<?php
/**
 * Canonical commercial routing for public entry points and conversion paths.
 *
 * SEO query ownership and conversion routing are deliberately separate:
 * a page may keep its own indexable search intent while its CTA routes to the
 * commercially correct next step. This module is the source of truth for the
 * three active commercial paths: direct projects, agency White-Label and the
 * Solar/Wärmepumpe specialization.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a contact URL for a typed direct-project intake.
 *
 * The existing contact form keys stay stable because CRM and automation may
 * consume them. Positioning changes the public semantics, not the transport
 * contract.
 *
 * @param string $type  Existing contact request type.
 * @param string $focus Existing contact focus key.
 * @return string
 */
function hu_get_contact_intake_url( $type = 'project', $focus = 'implementation_scope' ) {
	$contact_url = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );

	return add_query_arg(
		[
			'type'  => sanitize_key( (string) $type ),
			'focus' => sanitize_key( (string) $focus ),
		],
		$contact_url
	);
}

/**
 * Return the canonical commercial route map.
 *
 * Important: `agentur_local` is an SEO/local acquisition entry, not a global
 * business pillar. `tracking_b2b` remains a dedicated query owner. The
 * `marketcheck` belongs only to the Energy specialization and explicit Energy
 * contexts.
 *
 * @return array<string, string>
 */
function hu_get_commercial_route_map() {
	static $routes = null;

	if ( is_array( $routes ) ) {
		return $routes;
	}

	$routes = [
		'home'            => home_url( '/' ),
		'freelancer'      => function_exists( 'nexus_get_page_url' )
			? nexus_get_page_url( [ 'wordpress-freelancer-hannover' ], home_url( '/wordpress-freelancer-hannover/' ) )
			: home_url( '/wordpress-freelancer-hannover/' ),
		'project_request' => hu_get_contact_intake_url( 'project', 'implementation_scope' ),
		'contact'         => function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' ),
		'whitelabel'      => function_exists( 'nexus_get_whitelabel_page_url' )
			? nexus_get_whitelabel_page_url()
			: home_url( '/whitelabel-retainer/' ),
		'energy'          => function_exists( 'nexus_get_energy_systems_url' )
			? nexus_get_energy_systems_url()
			: home_url( '/solar-waermepumpen-leadgenerierung/' ),
		'marketcheck'     => function_exists( 'hu_get_request_analysis_url' )
			? hu_get_request_analysis_url()
			: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ),
		'tracking_b2b'    => function_exists( 'nexus_get_page_url' )
			? nexus_get_page_url( [ 'server-side-tracking-b2b' ], home_url( '/server-side-tracking-b2b/' ) )
			: home_url( '/server-side-tracking-b2b/' ),
		'agentur_local'   => function_exists( 'nexus_get_page_url' )
			? nexus_get_page_url( [ 'wordpress-agentur-hannover', 'wordpress-agentur' ], home_url( '/wordpress-agentur-hannover/' ) )
			: home_url( '/wordpress-agentur-hannover/' ),
		'results'         => function_exists( 'nexus_get_results_url' )
			? nexus_get_results_url()
			: home_url( '/ergebnisse/' ),
		'about'           => function_exists( 'nexus_get_page_url' )
			? nexus_get_page_url( [ 'hasim-uener', 'uber-mich' ], home_url( '/hasim-uener/' ) )
			: home_url( '/hasim-uener/' ),
	];

	return (array) apply_filters( 'hu_commercial_route_map', $routes );
}

/**
 * Resolve one canonical commercial route.
 *
 * @param string $key      Route key.
 * @param string $fallback Optional fallback URL.
 * @return string
 */
function hu_get_commercial_route( $key, $fallback = '' ) {
	$routes = hu_get_commercial_route_map();
	$key    = sanitize_key( (string) $key );

	if ( isset( $routes[ $key ] ) && '' !== (string) $routes[ $key ] ) {
		return (string) $routes[ $key ];
	}

	return $fallback ? $fallback : home_url( '/' );
}

/**
 * Return the one canonical global navigation contract.
 *
 * Both the directly rendered header and the stored WordPress menu use this
 * sequence. This prevents theme switches or menu rebuilds from restoring the
 * retired Agentur/Marktcheck-first navigation.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_get_primary_navigation_contract() {
	$routes = hu_get_commercial_route_map();

	return [
		[
			'label'    => __( 'Solar & Wärmepumpen', 'blocksy-child' ),
			'url'      => $routes['energy'],
			'current'  => function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context(),
			'class'    => 'nav-solar-link',
			'track'    => 'nav_header_solar',
			'category' => 'navigation',
		],
		[
			'label'    => __( 'WordPress Freelancer', 'blocksy-child' ),
			'url'      => $routes['freelancer'],
			'current'  => is_page( 'wordpress-freelancer-hannover' ) || is_page_template( 'page-wordpress-freelancer-hannover.php' ),
			'class'    => 'nav-freelancer-link',
			'track'    => 'nav_header_freelancer',
			'category' => 'navigation',
		],
		[
			'label'    => __( 'Für Agenturen', 'blocksy-child' ),
			'url'      => $routes['whitelabel'],
			'current'  => function_exists( 'nexus_is_agency_nav_context' ) && nexus_is_agency_nav_context(),
			'class'    => 'nav-agency-link',
			'track'    => 'nav_header_whitelabel',
			'category' => 'navigation',
		],
		[
			'label'    => __( 'Ergebnisse', 'blocksy-child' ),
			'url'      => $routes['results'],
			'current'  => function_exists( 'nexus_is_results_context' ) && nexus_is_results_context(),
			'class'    => 'nav-results-link',
			'track'    => 'nav_header_results',
			'category' => 'navigation',
		],
		[
			'label'    => __( 'Über Haşim', 'blocksy-child' ),
			'url'      => $routes['about'],
			'current'  => is_page( 'hasim-uener' ) || is_page( 'uber-mich' ) || is_page_template( 'page-hasim-uener.php' ),
			'class'    => 'nav-about-link',
			'track'    => 'nav_header_about',
			'category' => 'navigation',
		],
		[
			'label'    => __( 'Projekt anfragen', 'blocksy-child' ),
			'url'      => $routes['project_request'],
			'current'  => false,
			'class'    => 'nav-cta-button nav-project-link',
			'track'    => 'nav_header_project',
			'category' => 'lead_gen',
		],
	];
}
