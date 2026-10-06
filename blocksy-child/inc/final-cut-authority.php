<?php
/**
 * Final-Cut authority graph for the primary commercial SEO architecture.
 *
 * Solar/Waermepumpe keeps its own mature cluster graph in
 * seo-subpage-cluster-links.php. This module gives the new commercial core the
 * same discipline without turning every page into a link directory:
 * route-specific pages expose only two or three contextually adjacent owners.
 *
 * Query ownership stays authoritative in docs/seo/query-ownership.csv.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical nodes of the Final-Cut commercial authority graph.
 *
 * @return array<string, array{label:string,description:string,url:string,role:string}>
 */
function hu_get_final_cut_authority_nodes() : array {
	$routes = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
	$urls   = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];

	return [
		'website' => [
			'label'       => 'WordPress-Website erstellen lassen',
			'description' => 'Neubau oder Relaunch mit technischem SEO, Anfrageweg, Konfigurator und dokumentierter Übergabe.',
			'url'         => $routes['website'] ?? home_url( '/wordpress-website-erstellen-lassen/' ),
			'role'        => 'Website',
		],
		'landingpage' => [
			'label'       => 'Landingpage erstellen lassen',
			'description' => 'Eine Seite für ein Angebot: Konzept, Text, Formular, Herkunftsmessung und Abnahme zum Festpreis.',
			'url'         => $routes['landingpage'] ?? home_url( '/landingpage-erstellen-lassen/' ),
			'role'        => 'Landingpage',
		],
		'tracking' => [
			'label'       => 'Conversion Tracking einrichten lassen',
			'description' => 'GA4, Tag Manager, Consent Mode und Google Ads mit Messplan und Abnahmeprotokoll.',
			'url'         => $routes['tracking_setup'] ?? home_url( '/ga4-tracking-setup/' ),
			'role'        => 'Tracking',
		],
		'server_side' => [
			'label'       => 'Server-Side Tracking einrichten lassen',
			'description' => 'Server-GTM, eigene Tracking-Subdomain, Enhanced Conversions und Paralleltest nach technischem Bedarf.',
			'url'         => $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' ),
			'role'        => 'Tracking · Vertiefung',
		],
		'conversion' => [
			'label'       => 'Conversion-Optimierung für B2B',
			'description' => 'Die Strecke vom Besuch bis zur Rückmeldung prüfen und Engpässe priorisiert statt isoliert bearbeiten.',
			'url'         => $routes['conversion'] ?? home_url( '/conversion-optimierung/' ),
			'role'        => 'Conversion',
		],
		'whitelabel' => [
			'label'       => 'White-Label für Agenturen',
			'description' => 'WordPress-, Tracking- und technische SEO-Umsetzung im Hintergrund mit klarer Abnahme und Übergabe.',
			'url'         => $routes['whitelabel'] ?? home_url( '/whitelabel-retainer/' ),
			'role'        => 'Agenturen',
		],
		'agence_local' => [
			'label'       => 'WordPress Agentur Hannover',
			'description' => 'Lokale Entscheidungsseite: klassische Agentur oder direkter technischer Umsetzungspartner.',
			'url'         => $routes['agentur_local'] ?? home_url( '/wordpress-agentur-hannover/' ),
			'role'        => 'Lokal',
		],
		'relaunch' => [
			'label'       => 'Website-Relaunch richtig planen',
			'description' => 'Informationsarchitektur, Weiterleitungen, Messung und Abnahme vor dem sichtbaren Neubau entscheiden.',
			'url'         => $urls['website_relaunch'] ?? home_url( '/website-relaunch/' ),
			'role'        => 'Leitfaden',
		],
		'outsourcing' => [
			'label'       => 'WordPress-Projekte sauber auslagern',
			'description' => 'Scope, Handoff, Staging, QA, Deployment und Übergabe für die Zusammenarbeit mit externen Entwicklern.',
			'url'         => home_url( '/wordpress-projekte-auslagern/' ),
			'role'        => 'Agentur-Leitfaden',
		],
	];
}

/**
 * Route-specific edges. Order is intentional: nearest next decision first.
 *
 * @return array<string, array<int,string>>
 */
function hu_get_final_cut_authority_edges() : array {
	return [
		'website'     => [ 'landingpage', 'tracking', 'conversion' ],
		'landingpage' => [ 'website', 'tracking', 'conversion' ],
		'tracking'    => [ 'server_side', 'conversion', 'website' ],
		'server_side' => [ 'tracking', 'conversion', 'whitelabel' ],
		'conversion'  => [ 'landingpage', 'tracking', 'website' ],
		'whitelabel'  => [ 'outsourcing', 'server_side', 'website' ],
		'agence_local'=> [ 'website', 'tracking', 'whitelabel' ],
		'relaunch'    => [ 'website', 'conversion', 'tracking' ],
		'outsourcing' => [ 'whitelabel', 'website', 'server_side' ],
	];
}

/**
 * Map public paths to authority-graph node keys.
 *
 * @return array<string,string>
 */
function hu_get_final_cut_authority_path_map() : array {
	$nodes = hu_get_final_cut_authority_nodes();
	$map   = [];

	foreach ( $nodes as $key => $node ) {
		$path = (string) wp_parse_url( (string) $node['url'], PHP_URL_PATH );
		if ( '' === $path ) {
			continue;
		}

		$path = '/' === $path ? '/' : trailingslashit( '/' . ltrim( $path, '/' ) );
		$map[ $path ] = $key;
	}

	return $map;
}

/**
 * Resolve the graph node of the current request.
 *
 * @return string
 */
function hu_get_current_final_cut_authority_node() : string {
	if ( is_admin() || wp_doing_ajax() || is_front_page() ) {
		return '';
	}

	$path = function_exists( 'nexus_get_current_request_path' )
		? nexus_get_current_request_path()
		: (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ), PHP_URL_PATH );

	$path = '/' === $path ? '/' : trailingslashit( '/' . ltrim( (string) $path, '/' ) );
	$map  = hu_get_final_cut_authority_path_map();

	return isset( $map[ $path ] ) ? (string) $map[ $path ] : '';
}

/**
 * Resolve contextually adjacent authority nodes for one owner.
 *
 * @param string $key Current node key.
 * @return array<int, array{label:string,description:string,url:string,role:string}>
 */
function hu_get_final_cut_authority_links( string $key ) : array {
	$nodes = hu_get_final_cut_authority_nodes();
	$edges = hu_get_final_cut_authority_edges();
	$out   = [];

	foreach ( $edges[ $key ] ?? [] as $target ) {
		if ( isset( $nodes[ $target ] ) ) {
			$out[] = $nodes[ $target ];
		}
	}

	return $out;
}

/**
 * Load the tiny presentation layer only on routes that render the graph.
 *
 * @return void
 */
function hu_enqueue_final_cut_authority_assets() : void {
	if ( '' === hu_get_current_final_cut_authority_node() ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/final-cut-authority.css';
	$url  = get_stylesheet_directory_uri() . '/assets/css/final-cut-authority.css';

	if ( ! is_file( $path ) ) {
		return;
	}

	$version = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $path ) : (string) filemtime( $path );
	wp_enqueue_style( 'hu-final-cut-authority', $url, [ 'nexus-system-css' ], $version );
}
add_action( 'wp_enqueue_scripts', 'hu_enqueue_final_cut_authority_assets', 35 );

/**
 * Render contextual links immediately before the global footer.
 *
 * This is intentionally not site-wide navigation. The footer already owns that
 * job. The block exists only on graph nodes and carries adjacent query owners,
 * so anchor context remains specific and the Solar cluster stays separate.
 *
 * @return void
 */
function hu_render_final_cut_authority_links() : void {
	$current = hu_get_current_final_cut_authority_node();
	if ( '' === $current ) {
		return;
	}

	$links = hu_get_final_cut_authority_links( $current );
	if ( empty( $links ) ) {
		return;
	}
	?>
	<section class="doku hu-authority-links" aria-label="Passende fachliche Vertiefungen" data-track-section="final_cut_authority_links">
		<div class="blatt">
			<div class="hu-authority-links__head">
				<p class="mono stempelfarbe">Passende Vertiefungen</p>
				<h2 class="kopf leise">Das Thema im System weiterführen.</h2>
			</div>
			<nav class="hu-authority-links__grid" aria-label="Verwandte Fachseiten">
				<?php foreach ( $links as $index => $link ) : ?>
					<a
						class="hu-authority-links__item"
						href="<?php echo esc_url( $link['url'] ); ?>"
						data-track-action="<?php echo esc_attr( 'authority_link_' . ( $index + 1 ) ); ?>"
						data-track-category="internal_link"
					>
						<span class="hu-authority-links__role"><?php echo esc_html( $link['role'] ); ?></span>
						<strong><?php echo esc_html( $link['label'] ); ?></strong>
						<span><?php echo esc_html( $link['description'] ); ?></span>
						<b aria-hidden="true">→</b>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>
	</section>
	<?php
}
add_action( 'get_footer', 'hu_render_final_cut_authority_links', 8 );
