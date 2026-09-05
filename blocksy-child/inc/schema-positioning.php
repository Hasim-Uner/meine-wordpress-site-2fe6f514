<?php
/**
 * Canonical positioning layer for the site-wide schema graph.
 *
 * `org-schema.php` continues to own the mature route-specific schema registry
 * (FAQ, Article, Breadcrumb, specialist Service nodes). This module owns only
 * the commercial identity contract: Person, Organization, WebSite and the
 * direct Freelancer service. It normalizes those nodes at output time so the
 * large route registry can evolve independently without reintroducing the
 * retired Solar-only identity.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The commercial router is the single source of truth for Direct, White-Label
// and Energy destinations. Load it here as a compatibility bootstrap while the
// legacy module loader in functions.php is still being simplified.
$hu_commercial_routing_file = __DIR__ . '/commercial-routing.php';
if ( is_file( $hu_commercial_routing_file ) ) {
	require_once $hu_commercial_routing_file;
}

/**
 * Return the global offer catalog that reflects the current commercial routes.
 *
 * SEO query ownership stays on the destination pages; this catalog describes
 * what the business offers and does not redirect or replace those pages.
 *
 * This is the ONE source for the Organization catalog. hu_output_schema() in
 * org-schema.php builds the node and this function fills its hasOfferCatalog,
 * so builder and normalizer cannot state two different catalogs — they did,
 * and the divergence was invisible because hu_normalize_positioned_schema_node()
 * overwrote the builder at render time. scripts/lint-entity-crawler-signals.php
 * asserts the no-op.
 *
 * Shape: OfferCatalog › Offer › itemOffered › Service. An Offer is the
 * commercial wrapper, the Service is the thing being offered — a bare Offer
 * carrying name and description states a price without ever naming what is for
 * sale. The URL sits on the Service because it identifies the service, not the
 * offer, and `provider` keeps each Service self-describing when a consumer
 * lifts it out of the surrounding Organization node.
 *
 * @return array<string, mixed>
 */
function hu_get_positioned_schema_offer_catalog() : array {
	$routes = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
	$marketcheck_url = $routes['marketcheck'] ?? (
		function_exists( 'hu_get_request_analysis_url' )
			? hu_get_request_analysis_url()
			: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' )
	);
	$whitelabel_url = $routes['whitelabel'] ?? (
		function_exists( 'nexus_get_whitelabel_page_url' )
			? nexus_get_whitelabel_page_url()
			: home_url( '/whitelabel-retainer/' )
	);
	$freelancer_url = $routes['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );
	$tracking_url   = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
	$energy_url     = $routes['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );

	$offer = static function ( string $name, string $description, string $url, string $service_type ) : array {
		return [
			'@type'       => 'Offer',
			'itemOffered' => [
				'@type'       => 'Service',
				'name'        => $name,
				'description' => $description,
				'url'         => $url,
				'serviceType' => $service_type,
				'provider'    => [ '@id' => home_url( '/#organization' ) ],
			],
		];
	};

	return [
		'@type'           => 'OfferCatalog',
		'name'            => 'WordPress, Tracking, Conversion und spezialisierte Anfragesysteme',
		'itemListElement' => [
			$offer(
				'WordPress-Entwicklung',
				'WordPress-Websites und Landingpages mit technischer SEO, Performance und sauberer Weiterentwicklung.',
				$freelancer_url,
				'WordPress-Entwicklung'
			),
			$offer(
				'Server-Side Tracking & Attribution',
				'Server-GTM, GA4, Google Ads, Meta CAPI und nachvollziehbare Messkonzepte für belastbare Conversion-Signale.',
				$tracking_url,
				'Tracking & Attribution'
			),
			$offer(
				'Conversion-Optimierung',
				'Optimierung von Landingpages, Funnels, Proof und Anfragewegen mit Fokus auf messbare Conversion.',
				$freelancer_url,
				'Conversion Rate Optimization'
			),
			$offer(
				'White-Label für Agenturen',
				'WordPress-, Tracking-, CRO- und technische SEO-Umsetzung im Hintergrund für Agenturprojekte.',
				$whitelabel_url,
				'White-Label-Umsetzung'
			),
			$offer(
				'Anfragesysteme für Solar & Wärmepumpe',
				'Spezialisierte Nachfrage- und Anfragewege für Solar-, Wärmepumpen- und Speicher-Anbieter.',
				$energy_url,
				'Anfragesystem'
			),
			$offer(
				'Marktcheck für Solar & Wärmepumpe',
				'Diagnostischer Einstieg für den Energie-Cluster: Region, Anfragequalität, Datenlage und nächster sinnvoller Schritt.',
				$marketcheck_url,
				'Marktcheck'
			),
		],
	];
}

/**
 * Normalize one JSON-LD node emitted by the existing schema generator.
 *
 * @param array<string, mixed> $schema Schema node.
 * @return array<string, mixed>
 */
function hu_normalize_positioned_schema_node( array $schema ) : array {
	$id = isset( $schema['@id'] ) ? (string) $schema['@id'] : '';

	if ( home_url( '/#organization' ) === $id ) {
		$schema['name']        = 'Haşim Üner';
		$schema['description'] = 'WordPress-Entwicklung, technisches SEO, Tracking und Conversion für Unternehmen und Agenturen. Solar- und Wärmepumpen-Anfragesysteme bleiben eine spezialisierte Vertikale mit eigenem Marktcheck.';
		// Eine Quelle fuer beide Knoten; siehe hu_get_identity_knows_about().
		$schema['knowsAbout']  = hu_get_identity_knows_about();
		$schema['hasOfferCatalog'] = hu_get_positioned_schema_offer_catalog();

		// hu_brand_map_url() liefert diese CID-URL inzwischen selbst. Der Block
		// bleibt als Regressionsschutz, falls wieder eine Maps-Place-URL mit dem
		// zurueckgezogenen Rollen-Claim im Pfad in den Graph geraet.
		$stable_map_url = function_exists( 'hu_brand_map_url' )
			? hu_brand_map_url()
			: 'https://www.google.com/maps?cid=7273014379384770345';
		$schema['hasMap'] = $stable_map_url;
		if ( isset( $schema['sameAs'] ) && is_array( $schema['sameAs'] ) ) {
			$schema['sameAs'] = array_values(
				array_unique(
					array_merge(
						array_filter(
							$schema['sameAs'],
							static function ( $url ) : bool {
								return ! is_string( $url ) || false === strpos( $url, 'google.' ) || false === strpos( $url, '/maps/' );
							}
						),
						[ $stable_map_url ]
					)
				)
			);
		}
	}

	if ( home_url( '/#website' ) === $id ) {
		$schema['name']        = 'Haşim Üner';
		$schema['description'] = 'WordPress, technisches SEO, Tracking und Conversion als zusammenhängendes System. Direkte Projekte, White-Label für Agenturen und eine spezialisierte Solar-/Wärmepumpen-Vertikale.';
	}

	if ( function_exists( 'hu_person_schema_id' ) && hu_person_schema_id() === $id ) {
		$schema['jobTitle']    = 'WordPress Freelancer und Tracking-Spezialist';
		$schema['description'] = 'Haşim Üner verbindet WordPress-Entwicklung, technisches SEO, Tracking und Conversion für direkte Projekte und White-Label-Agenturarbeit. Anfragesysteme für Solar- und Wärmepumpen-Anbieter sind eine spezialisierte Vertikale.';
		// Eine Quelle fuer beide Knoten; siehe hu_get_identity_knows_about().
		$schema['knowsAbout']  = hu_get_identity_knows_about();
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
function hu_get_wordpress_freelancer_service_schema() : array {
	$routes = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
	$freelancer_url = $routes['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );

	return [
		'@context'      => 'https://schema.org',
		'@type'         => 'Service',
		'@id'           => trailingslashit( $freelancer_url ) . '#service',
		'url'           => $freelancer_url,
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
 * Render the existing route-specific graph and normalize the canonical
 * commercial identity nodes without changing Article/FAQ/Breadcrumb logic.
 *
 * @return void
 */
function hu_output_positioned_schema() : void {
	if ( ! function_exists( 'hu_output_schema' ) ) {
		return;
	}

	ob_start();
	hu_output_schema();
	$markup = (string) ob_get_clean();

	$markup = preg_replace_callback(
		'#<script type="application/ld\+json">(.*?)</script>#s',
		static function ( array $match ) : string {
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
