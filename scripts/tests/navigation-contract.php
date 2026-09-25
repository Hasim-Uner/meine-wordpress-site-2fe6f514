<?php
/**
 * Navigation contract: header, footer and 404 rendered from the real theme.
 *
 * Guards the decisions of the navigation rebuild (2026-09-25) and the bug it
 * fixed: the header item "Tracking" had switched twice between the Tracking
 * offer and the Server-Side specialist page. Exit 0 = all invariants hold.
 *
 * Usage: php scripts/tests/navigation-contract.php
 */

require __DIR__ . '/navigation-harness.php';

$failures = 0;

function nav_check( $condition, $message ) {
	global $failures;
	echo ( $condition ? 'PASS ' : 'FAIL ' ) . $message . "\n";
	if ( ! $condition ) {
		$failures++;
	}
}

function nav_path( $url ) {
	$path = (string) wp_parse_url( (string) $url, PHP_URL_PATH );
	return '' === $path ? '/' : trailingslashit( $path );
}

/**
 * Parse rendered markup into link records.
 *
 * @param string $html  Rendered template part.
 * @param string $xpath Query selecting the anchors.
 * @return array<int, array<string, string>>
 */
function nav_links( $html, $xpath ) {
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="utf-8"?><body>' . $html . '</body>' );
	libxml_clear_errors();

	$links = [];
	foreach ( ( new DOMXPath( $dom ) )->query( $xpath ) as $a ) {
		$links[] = [
			'href'    => (string) $a->getAttribute( 'href' ),
			'text'    => trim( preg_replace( '/\s+/u', ' ', (string) $a->textContent ) ),
			'current' => (string) $a->getAttribute( 'aria-current' ),
			'track'   => (string) $a->getAttribute( 'data-track-action' ),
		];
	}
	return $links;
}

function nav_dom( $html ) {
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="utf-8"?><body>' . $html . '</body>' );
	libxml_clear_errors();
	return new DOMXPath( $dom );
}

// Every internal target must be a known public route: the llms.txt index plus
// the legal pages and the contact intake. Anything else is a 404 risk.
preg_match_all( '/\]\((\/[^)\s]*)\)/', (string) file_get_contents( __DIR__ . '/../../llms.txt' ), $llms );
$known_paths = array_unique( array_merge(
	array_map( 'nav_path', $llms[1] ),
	[ '/', '/kontakt/', '/impressum/', '/datenschutz/', '/blog/', '/glossar/', '/hasim-uener/' ]
) );
$known_anchors = [ '/#angebote', '/#arbeiten' ];
$retired       = array_merge(
	function_exists( 'nexus_get_retired_gone_paths' ) ? nexus_get_retired_gone_paths() : [],
	[ '/ergebnisse/', '/audit/', '/growth-audit/', '/customer-journey-audit/', '/360-audit/', '/system-diagnose/', '/wordpress-freelancer-hannover/', '/case-studies/' ]
);

// --- 1. Contract --------------------------------------------------------------

nav_test_use_context( 'imprint' );
$routes   = hu_get_commercial_route_map();
$header   = hu_get_site_header_navigation_contract();
$footer   = hu_get_site_footer_navigation_contract();
$labels   = array_map( static function ( $item ) { return $item['label']; }, hu_get_primary_navigation_contract() );
$tracking = $header['routes'][1];

nav_check( [ 'Leistungen', 'Tracking', 'White-Label', 'Solar & Wärmepumpe', 'Ergebnisse', 'Über Haşim', 'Projekt anfragen' ] === $labels, 'header order: services, audience paths, proof, CTA' );
nav_check( '/ga4-tracking-setup/' === nav_path( $routes['tracking_setup'] ), 'route tracking_setup is the Tracking offer' );
nav_check( '/server-side-tracking-b2b/' === nav_path( $routes['tracking_b2b'] ), 'route tracking_b2b stays the Server-Side specialist page' );
nav_check( 'Tracking' === $tracking['label'] && $routes['tracking_setup'] === $tracking['url'], 'header "Tracking" links the Tracking offer' );
nav_check( 'https://hasimuener.de/kontakt/?type=project' === $header['cta']['url'], 'header CTA is the open project request' );
nav_check( [ 'freelancer', 'tracking', 'whitelabel', 'energy' ] === array_column( $footer['picks'], 'route' ), 'footer ways follow the header order' );
nav_check( $tracking['url'] === $footer['picks'][1]['url'], 'footer tracking way and header "Tracking" share one target' );
nav_check( [ 'Leistungen', 'Belege & Person', 'Wissen', 'Rechtliches' ] === array_column( $footer['directory'], 'title' ), 'footer directory has four groups' );

$directory_items = array_merge( ...array_column( $footer['directory'], 'items' ) );
$by_label        = array_column( $directory_items, 'url', 'label' );
nav_check( ( $by_label['Server-Side Tracking'] ?? '' ) === $routes['tracking_b2b'], 'specialist page keeps a sitewide link with its exact name' );
nav_check( '/performance-marketing/' === nav_path( $by_label['Performance Marketing'] ?? '' ), 'Performance Marketing query owner is linked sitewide' );

$tracks = array_merge(
	array_column( hu_get_primary_navigation_contract(), 'track' ),
	array_column( $footer['picks'], 'track' ),
	array_column( $directory_items, 'track' )
);
nav_check( ! in_array( '', $tracks, true ), 'every header and footer item carries a tracking action' );
nav_check( count( array_column( $directory_items, 'track' ) ) === count( array_unique( array_column( $directory_items, 'track' ) ) ), 'footer directory tracking actions are unique' );

// --- 2. Rendered surfaces per context ------------------------------------------

$expect_current = [
	'home'          => [],
	'tracking'      => [ 'Tracking' => 'page' ],
	'server_side'   => [ 'Tracking' => 'true' ],
	'case_study'    => [ 'Ergebnisse' => 'true' ],
	'about'         => [ 'Über Haşim' => 'page' ],
	'contact'       => [],
	'agentur_local' => [],
	'imprint'       => [],
	'not_found'     => [],
];

foreach ( array_keys( nav_test_contexts() ) as $context ) {
	nav_test_use_context( $context );
	$header_html = nav_test_render( 'template-parts/site-header.php' );
	$footer_html = nav_test_render( 'template-parts/site-footer.php' );
	$row         = nav_links( $header_html, '//nav[@aria-label="Hauptnavigation"]//a' );
	$sheet       = nav_links( $header_html, '//div[@data-leiste-blatt]//nav//a' );
	$footer_nav  = nav_links( $footer_html, '//nav//a' );
	$header_x    = nav_dom( $header_html );

	nav_check( array_column( $row, 'href' ) === array_column( $sheet, 'href' ), "{$context}: row and sheet list the same targets in the same order" );

	$current = [];
	foreach ( $row as $link ) {
		if ( '' !== $link['current'] ) {
			$current[ $link['text'] ] = $link['current'];
		}
	}
	nav_check( $expect_current[ $context ] === $current, "{$context}: aria-current " . json_encode( $current, JSON_UNESCAPED_UNICODE ) );

	$toggle = $header_x->query( '//button[@data-leiste-klappe]' )->item( 0 );
	nav_check(
		$toggle && 'false' === $toggle->getAttribute( 'aria-expanded' )
			&& 1 === $header_x->query( '//*[@id="' . $toggle->getAttribute( 'aria-controls' ) . '"]' )->length,
		"{$context}: menu button controls an existing sheet"
	);

	$ctas = nav_links( $header_html, '//a[contains(concat(" ", @class, " "), " tun ")]' );
	if ( 'contact' === $context ) {
		nav_check( [] === $ctas, 'contact: no header CTA pointing at the page itself' );
		nav_check( 0 === nav_dom( $footer_html )->query( '//nav[contains(@class,"wahl")]' )->length, 'contact: footer skips the way choice' );
	} else {
		nav_check( 2 === count( $ctas ) && 'nav_header_project' === $ctas[0]['track'], "{$context}: row and sheet carry the project CTA" );
	}

	if ( in_array( $context, [ 'tracking', 'server_side' ], true ) ) {
		nav_check( ! in_array( 'cta_footer_pick_tracking', array_column( $footer_nav, 'track' ), true ), "{$context}: footer does not offer the tracking way again" );
	}

	if ( 'home' === $context ) {
		$sig = $header_x->query( '//a[contains(@class,"sig")]' )->item( 0 );
		nav_check( $sig && 'page' === $sig->getAttribute( 'aria-current' ), 'home: wordmark marks the current page' );
	}

	foreach ( array_merge( $row, $sheet, $ctas, $footer_nav ) as $link ) {
		$href = $link['href'];
		if ( 0 !== strpos( $href, 'https://hasimuener.de' ) ) {
			nav_check( 1 === preg_match( '#^(mailto:|tel:|https://www\.linkedin\.com/)#', $href ), "{$context}: external link is intended: {$href}" );
			continue;
		}
		$path     = nav_path( $href );
		$fragment = (string) wp_parse_url( $href, PHP_URL_FRAGMENT );
		nav_check( ! in_array( $path, $retired, true ) && 'marktcheck' !== $fragment, "{$context}: no retired or Marktcheck target: {$href}" );
		nav_check(
			'' === $fragment ? in_array( $path, $known_paths, true ) : in_array( $path . '#' . $fragment, $known_anchors, true ),
			"{$context}: known public route: {$href}"
		);
		nav_check( '' !== $link['track'], "{$context}: tracked: {$link['text']}" );
	}

	$generic_to_specialist = array_filter( array_merge( $row, $footer_nav ), static function ( $link ) {
		return 'Tracking' === $link['text'] && '/server-side-tracking-b2b/' === nav_path( $link['href'] );
	} );
	nav_check( [] === $generic_to_specialist, "{$context}: plain \"Tracking\" never points at the specialist page" );
}

// --- 3. 404 recovery links ----------------------------------------------------

nav_test_use_context( 'not_found' );
$not_found = nav_links( nav_test_render( '404.php' ), '//div[contains(@class,"nexus-404__links")]//a' );
nav_check( [ 'Startseite', 'Leistungen', 'Tracking', 'White-Label', 'Solar & Wärmepumpe', 'Blog' ] === array_column( $not_found, 'text' ), '404: recovery links mirror the header routes' );
nav_check( ! preg_grep( '/marktcheck/i', array_column( $not_found, 'href' ) ), '404: no sitewide Marktcheck link' );
nav_check( count( $not_found ) === count( array_unique( array_column( $not_found, 'track' ) ) ), '404: tracking actions are unique' );

echo $failures ? "\n{$failures} navigation invariant(s) failed.\n" : "\nNavigation contract ok.\n";
exit( $failures ? 1 : 0 );
