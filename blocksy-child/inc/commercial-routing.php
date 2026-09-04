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
 * Return the repo-owned full-screen header contract.
 *
 * The rendered sheet, the WordPress-menu compatibility layer and the SEO
 * Cockpit all derive from this structure. Tracking action names deliberately
 * reuse the historical header values; only the menu toggle adds a new action.
 *
 * @return array<string, mixed>
 */
function hu_get_site_header_navigation_contract() {
	$routes      = hu_get_commercial_route_map();
	$public_urls = function_exists( 'nexus_get_primary_public_url_map' )
		? nexus_get_primary_public_url_map()
		: [];

	return [
		'toggle' => [
			'track'    => 'nav_menu_toggle',
			'category' => 'navigation',
			'section'  => 'header',
		],
		'routes' => [
			[
				'kind'     => 'route',
				'kicker'   => __( 'Direkte Projekte', 'blocksy-child' ),
				'label'    => __( 'WordPress-Umsetzung', 'blocksy-child' ),
				'desc'     => __( 'Neubau, Relaunch und Weiterentwicklung — mit Messung, die von Anfang an mitgebaut wird.', 'blocksy-child' ),
				'url'      => $routes['freelancer'],
				'current'  => is_page( 'wordpress-freelancer-hannover' ) || is_page_template( 'page-wordpress-freelancer-hannover.php' ),
				'class'    => 'nav-freelancer-link',
				'track'    => 'nav_header_freelancer',
				'category' => 'navigation',
				'section'  => 'header',
			],
			[
				'kind'     => 'route',
				'kicker'   => __( 'Für Agenturen', 'blocksy-child' ),
				'label'    => __( 'White-Label-Retainer', 'blocksy-child' ),
				'desc'     => __( 'Technik im Hintergrund, unter Ihrem Namen. Feste Kapazität statt Projektpoker.', 'blocksy-child' ),
				'url'      => $routes['whitelabel'],
				'current'  => function_exists( 'nexus_is_agency_nav_context' ) && nexus_is_agency_nav_context(),
				'class'    => 'nav-agency-link',
				'track'    => 'nav_header_whitelabel',
				'category' => 'navigation',
				'section'  => 'header',
			],
			[
				'kind'     => 'route',
				'kicker'   => __( 'Vertikale', 'blocksy-child' ),
				'label'    => __( 'Solar & Wärmepumpe', 'blocksy-child' ),
				'desc'     => __( 'Anfragesysteme für Betriebe, die keine gekauften Portalleads mehr wollen.', 'blocksy-child' ),
				'url'      => $routes['energy'],
				'current'  => function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context(),
				'class'    => 'nav-solar-link',
				'track'    => 'nav_header_solar',
				'category' => 'navigation',
				'section'  => 'header',
			],
		],
		'groups' => [
			[
				'title' => __( 'Leistungen', 'blocksy-child' ),
				'items' => [
					[
						'kind'     => 'group',
						'label'    => __( 'Server-Side-Tracking B2B', 'blocksy-child' ),
						'url'      => $routes['tracking_b2b'],
						'current'  => is_page( 'server-side-tracking-b2b' ),
						'class'    => 'nav-tracking-link',
						'track'    => 'nav_header_freelancer',
						'category' => 'navigation',
						'section'  => 'header',
					],
					[
						'kind'     => 'group',
						'label'    => __( 'WordPress-Agentur Hannover', 'blocksy-child' ),
						'url'      => $routes['agentur_local'],
						'current'  => is_page( 'wordpress-agentur-hannover' ) || is_page( 'wordpress-agentur' ) || is_page_template( 'page-wordpress-agentur.php' ),
						'class'    => 'nav-agentur-link',
						'track'    => 'nav_header_freelancer',
						'category' => 'navigation',
						'section'  => 'header',
					],
				],
			],
			[
				'title' => __( 'Belege', 'blocksy-child' ),
				'items' => [
					[
						'kind'     => 'group',
						'label'    => __( 'Ergebnisse', 'blocksy-child' ),
						'url'      => $routes['results'],
						'current'  => function_exists( 'nexus_is_results_context' ) && nexus_is_results_context(),
						'class'    => 'nav-results-link',
						'track'    => 'nav_header_results',
						'category' => 'navigation',
						'section'  => 'header',
					],
					[
						'kind'     => 'group',
						'label'    => __( 'Case Study Solar', 'blocksy-child' ),
						'url'      => $public_urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' ),
						'current'  => is_page( 'case-study-solar-leadgenerierung' ) || is_page_template( 'page-case-e3.php' ),
						'class'    => 'nav-case-link',
						'track'    => 'nav_header_results',
						'category' => 'navigation',
						'section'  => 'header',
					],
					[
						'kind'     => 'group',
						'label'    => __( 'Blog', 'blocksy-child' ),
						'url'      => $public_urls['blog'] ?? home_url( '/blog/' ),
						'current'  => is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_author(),
						'class'    => 'nav-blog-link',
						'track'    => 'nav_header_results',
						'category' => 'navigation',
						'section'  => 'header',
					],
					[
						'kind'     => 'group',
						'label'    => __( 'Glossar', 'blocksy-child' ),
						'url'      => $public_urls['glossary'] ?? home_url( '/glossar/' ),
						'current'  => ( function_exists( 'nexus_is_glossary_hub_page' ) && nexus_is_glossary_hub_page() ) || is_singular( 'glossary_term' ),
						'class'    => 'nav-glossary-link',
						'track'    => 'nav_header_results',
						'category' => 'navigation',
						'section'  => 'header',
					],
				],
			],
			[
				'title' => __( 'Person & Kontakt', 'blocksy-child' ),
				'items' => [
					[
						'kind'     => 'group',
						'label'    => __( 'Über Haşim', 'blocksy-child' ),
						'url'      => $routes['about'],
						'current'  => is_page( 'hasim-uener' ) || is_page( 'uber-mich' ) || is_page_template( 'page-hasim-uener.php' ),
						'class'    => 'nav-about-link',
						'track'    => 'nav_header_about',
						'category' => 'navigation',
						'section'  => 'header',
					],
					[
						'kind'     => 'group',
						'label'    => __( 'Kontakt', 'blocksy-child' ),
						'url'      => $routes['contact'],
						'current'  => is_page( 'kontakt' ),
						'class'    => 'nav-contact-link',
						'track'    => 'nav_header_project',
						'category' => 'lead_gen',
						'section'  => 'header',
					],
					[
						'kind'     => 'group',
						'label'    => __( 'Impressum', 'blocksy-child' ),
						'url'      => $public_urls['impressum'] ?? home_url( '/impressum/' ),
						'current'  => is_page( 'impressum' ),
						'class'    => 'nav-imprint-link',
						'track'    => 'nav_header_about',
						'category' => 'navigation',
						'section'  => 'header',
					],
					[
						'kind'     => 'group',
						'label'    => __( 'Datenschutz', 'blocksy-child' ),
						'url'      => $public_urls['datenschutz'] ?? home_url( '/datenschutz/' ),
						'current'  => is_page( 'datenschutz' ),
						'class'    => 'nav-privacy-link',
						'track'    => 'nav_header_about',
						'category' => 'navigation',
						'section'  => 'header',
					],
				],
			],
		],
		'meta' => [
			'location' => __( 'Pattensen bei Hannover · DACH-weit', 'blocksy-child' ),
			'links'    => [
				[
					'label'    => 'kontakt@hasimuener.de',
					'url'      => 'mailto:kontakt@hasimuener.de',
					'track'    => 'nav_header_project',
					'category' => 'lead_gen',
					'section'  => 'header',
				],
				[
					'label'    => '+49 176 76596580',
					'url'      => 'tel:+4917676596580',
					'track'    => 'nav_header_project',
					'category' => 'lead_gen',
					'section'  => 'header',
				],
				[
					'label'    => 'hasimuener.org',
					'url'      => 'https://hasimuener.org/',
					'track'    => 'nav_header_about',
					'category' => 'navigation',
					'section'  => 'header',
				],
			],
		],
		'cta' => [
			'kind'        => 'cta',
			'label'       => __( 'Projekt anfragen', 'blocksy-child' ),
			'short_label' => __( 'Anfragen', 'blocksy-child' ),
			'url'         => $routes['project_request'],
			'current'     => false,
			'class'       => 'nav-cta-button nav-project-link',
			'track'       => 'nav_header_project',
			'category'    => 'lead_gen',
			'section'     => 'header',
		],
	];
}

/**
 * Return a flat compatibility view of the global navigation contract.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_get_primary_navigation_contract() {
	$contract = hu_get_site_header_navigation_contract();
	$items    = (array) ( $contract['routes'] ?? [] );

	foreach ( (array) ( $contract['groups'] ?? [] ) as $group ) {
		$items = array_merge( $items, (array) ( $group['items'] ?? [] ) );
	}

	if ( ! empty( $contract['cta'] ) && is_array( $contract['cta'] ) ) {
		$items[] = $contract['cta'];
	}

	return $items;
}
