<?php
/**
 * Positioning normalization for the site-wide schema graph.
 *
 * `org-schema.php` still owns route-specific schema generation. This module
 * changes only the canonical Person, Organization and WebSite identity nodes
 * plus the direct Freelancer service node. Keeping the normalization separate
 * makes the repositioning reviewable without rewriting the large schema
 * registry in one risky change.
 *
 * Once the migration is proven in production, these canonical values can be
 * folded back into org-schema.php in a dedicated refactor.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the global offer catalog that reflects the current commercial routes.
 *
 * SEO query ownership stays on the destination pages; this catalog describes
 * what the business offers and does not redirect or replace those pages.
 *
 * @return array<string, mixed>
 */
function hu_get_positioned_schema_offer_catalog() {
	$project_url = function_exists( 'hu_get_navigation_project_request_url' )
		? hu_get_navigation_project_request_url()
		: home_url( '/kontakt/' );
	$marketcheck_url = function_exists( 'hu_get_request_analysis_url' )
		? hu_get_request_analysis_url()
		: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
	$whitelabel_url = function_exists( 'nexus_get_whitelabel_page_url' )
		? nexus_get_whitelabel_page_url()
		: home_url( '/whitelabel-retainer/' );

	return [
		'@type'           => 'OfferCatalog',
		'name'            => 'WordPress, Tracking, Conversion und spezialisierte Anfragesysteme',
		'itemListElement' => [
			[
				'@type'       => 'Offer',
				'name'        => 'WordPress-Entwicklung',
				'description' => 'WordPress-Websites und Landingpages mit technischer SEO, Performance und sauberer Weiterentwicklung.',
				'url'         => home_url( '/wordpress-freelancer-hannover/' ),
			],
			[
				'@type'       => 'Offer',
				'name'        => 'Server-Side Tracking & Attribution',
				'description' => 'Server-GTM, GA4, Google Ads, Meta CAPI und nachvollziehbare Messkonzepte für belastbare Conversion-Signale.',
				'url'         => home_url( '/server-side-tracking-b2b/' ),
			],
			[
				'@type'       => 'Offer',
				'name'        => 'Conversion-Optimierung',
				'description' => 'Optimierung von Landingpages, Funnels, Proof und Anfragewegen mit Fokus auf messbare Conversion.',
				'url'         => $project_url,
			],
			[
				'@type'       => 'Offer',
				'name'        => 'White-Label für Agenturen',
				'description' => 'WordPress-, Tracking-, CRO- und technische SEO-Umsetzung im Hintergrund für Agenturprojekte.',
				'url'         => $whitelabel_url,
			],
			[
				'@type'       => 'Offer',
				'name'        => 'Anfragesysteme für Solar & Wärmepumpe',
				'description' => 'Spezialisierte Nachfrage- und Anfragewege für Solar-, Wärmepumpen- und Speicher-Anbieter.',
				'url'         => home_url( '/solar-waermepumpen-leadgenerierung/' ),
			],
			[
				'@type'       => 'Offer',
				'name'        => 'Marktcheck für Solar & Wärmepumpe',
				'description' => 'Diagnostischer Einstieg für den Energie-Cluster: Region, Anfragequalität, Datenlage und nächster sinnvoller Schritt.',
				'url'         => $marketcheck_url,
			],
		],
	];
}

/**
 * Normalize one JSON-LD node emitted by the existing schema generator.
 *
 * @param array<string, mixed> $schema Schema node.
 * @return array<string, mixed>
 */
function hu_normalize_positioned_schema_node( $schema ) {
	$id = isset( $schema['@id'] ) ? (string) $schema['@id'] : '';

	if ( home_url( '/#organization' ) === $id ) {
		$schema['name']        = 'Haşim Üner';
		$schema['description'] = 'WordPress-Entwicklung, technisches SEO, Tracking und Conversion für Unternehmen und Agenturen. Solar- und Wärmepumpen-Anfragesysteme bleiben eine spezialisierte Vertikale mit eigenem Marktcheck.';
		$schema['knowsAbout']  = [
			'WordPress-Entwicklung',
			'Technisches SEO',
			'Tracking & Attribution',
			'Server-Side Tracking',
			'Conversion Rate Optimization',
			'Landingpages & Funnel',
			'Core Web Vitals',
			'Performance Marketing',
			'Solar- und Wärmepumpen-Leadgenerierung',
		];
		$schema['hasOfferCatalog'] = hu_get_positioned_schema_offer_catalog();
	}

	if ( home_url( '/#website' ) === $id ) {
		$schema['name']        = 'Haşim Üner';
		$schema['description'] = 'WordPress, technisches SEO, Tracking und Conversion als zusammenhängendes System. Direkte Projekte, White-Label für Agenturen und eine spezialisierte Solar-/Wärmepumpen-Vertikale.';
	}

	if ( function_exists( 'hu_person_schema_id' ) && hu_person_schema_id() === $id ) {
		$schema['jobTitle']    = 'WordPress Freelancer und Tracking-Spezialist';
		$schema['description'] = 'Haşim Üner verbindet WordPress-Entwicklung, technisches SEO, Tracking und Conversion für direkte Projekte und White-Label-Agenturarbeit. Anfragesysteme für Solar- und Wärmepumpen-Anbieter sind eine spezialisierte Vertikale.';
		$schema['knowsAbout']  = [
			'WordPress-Entwicklung',
			'Technisches SEO',
			'Tracking & Attribution',
			'Server-Side Tracking',
			'Conversion Rate Optimization',
			'Landingpages & Funnel',
			'Core Web Vitals',
			'Performance Marketing',
			'Solar- und Wärmepumpen-Leadgenerierung',
		];
	}

	$freelancer_webpage_id = home_url( '/wordpress-freelancer-hannover/#webpage' );
	if ( $freelancer_webpage_id === $id ) {
		$schema['mainEntity'] = [ '@id' => home_url( '/wordpress-freelancer-hannover/#service' ) ];
		$schema['about']      = [ '@id' => home_url( '/#organization' ) ];
	}

	return $schema;
}

/**
 * Build the Service node for the direct Freelancer money page.
 *
 * @return array<string, mixed>
 */
function hu_get_wordpress_freelancer_service_schema() {
	return [
		'@context'      => 'https://schema.org',
		'@type'         => 'Service',
		'@id'           => home_url( '/wordpress-freelancer-hannover/#service' ),
		'url'           => home_url( '/wordpress-freelancer-hannover/' ),
		'name'          => 'WordPress Freelancer Hannover',
		'description'   => 'Direkte WordPress-Zusammenarbeit mit Entwicklung, technischer SEO, Tracking und Conversion-Optimierung aus Pattensen bei Hannover.',
		'provider'      => [ '@id' => home_url( '/#organization' ) ],
		'serviceType'   => 'WordPress-Entwicklung',
		'serviceOutput' => 'Technisch saubere WordPress-Websites und Landingpages mit messbaren Conversion-Pfaden, Tracking und kontrollierter Weiterentwicklung.',
		'areaServed'    => [
			[
				'@type' => 'AdministrativeArea',
				'name'  => 'Region Hannover',
			],
			[
				'@type' => 'AdministrativeArea',
				'name'  => 'DACH',
			],
		],
		'hasOfferCatalog' => [
			'@type'           => 'OfferCatalog',
			'name'            => 'Leistungsfelder der direkten WordPress-Zusammenarbeit',
			'itemListElement' => [
				[
					'@type' => 'Offer',
					'name'  => 'WordPress-Entwicklung',
				],
				[
					'@type' => 'Offer',
					'name'  => 'Tracking & Attribution',
				],
				[
					'@type' => 'Offer',
					'name'  => 'Conversion-Optimierung',
				],
				[
					'@type' => 'Offer',
					'name'  => 'Technisches SEO',
				],
			],
		],
	];
}

/**
 * Render the existing route-specific graph after normalizing only the canonical
 * identity nodes. This deliberately preserves all page-specific FAQ, Article,
 * Breadcrumb and Service logic already owned by org-schema.php.
 *
 * @return void
 */
function hu_output_positioned_schema() {
	if ( ! function_exists( 'hu_output_schema' ) ) {
		return;
	}

	ob_start();
	hu_output_schema();
	$markup = (string) ob_get_clean();

	$markup = preg_replace_callback(
		'#<script type="application/ld\+json">(.*?)</script>#s',
		static function ( $match ) {
			$decoded = json_decode( (string) $match[1], true );

			if ( ! is_array( $decoded ) ) {
				return (string) $match[0];
			}

			$decoded = hu_normalize_positioned_schema_node( $decoded );
			$json    = wp_json_encode( $decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

			if ( ! is_string( $json ) || '' === $json ) {
				return (string) $match[0];
			}

			return '<script type="application/ld+json">' . $json . '</script>';
		},
		$markup
	);

	if ( is_string( $markup ) ) {
		echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD generated from wp_json_encode.
	}

	if ( is_page( 'wordpress-freelancer-hannover' ) || is_page_template( 'page-wordpress-freelancer-hannover.php' ) ) {
		$freelancer_schema = hu_get_wordpress_freelancer_service_schema();
		$json              = wp_json_encode( $freelancer_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

		if ( is_string( $json ) && '' !== $json ) {
			echo '<script type="application/ld+json">' . $json . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD generated from wp_json_encode.
		}
	}
}

remove_action( 'wp_head', 'hu_output_schema', 10 );
add_action( 'wp_head', 'hu_output_positioned_schema', 10 );
