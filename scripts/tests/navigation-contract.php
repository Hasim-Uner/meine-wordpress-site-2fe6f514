<?php
/**
 * Navigation contract: header, footer and 404 rendered from the real theme.
 *
 * Guards the decisions of the navigation rebuild (2026-09-25) and the bug it
 * fixed: the header item "Tracking" had switched twice between the Tracking
 * offer and the Server-Side specialist page, and the door matrix of the
 * header (2026-10-01): one door per context, amount only from the canon.
 * Exit 0 = all invariants hold.
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
			'door'    => (string) $a->getAttribute( 'data-door' ),
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
$known_anchors = [ '/#angebote', '/#arbeiten', '/whitelabel-retainer/#aufgabe', '/solar-waermepumpen-leadgenerierung/#einstieg' ];
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

nav_check( [ 'Projekte', 'Tracking', 'White-Label', 'Solar & Wärmepumpe', 'Ergebnisse', 'Über Haşim', 'Projekt anfragen' ] === $labels, 'header order: projects, audience paths, proof, default door' );
nav_check( [ true, false ] === array_column( $header['groups'][0]['items'], 'row' ), 'header row carries Ergebnisse; Über Haşim stays in sheet and footer' );
nav_check( '/ga4-tracking-setup/' === nav_path( $routes['tracking_setup'] ), 'route tracking_setup is the Tracking offer' );
nav_check( '/server-side-tracking-b2b/' === nav_path( $routes['tracking_b2b'] ), 'route tracking_b2b stays the Server-Side specialist page' );
nav_check( 'Tracking' === $tracking['label'] && $routes['tracking_setup'] === $tracking['url'], 'header "Tracking" links the Tracking offer' );
nav_check( 'https://hasimuener.de/kontakt/?type=project' === $header['cta']['url'], 'header CTA is the open project request' );
nav_check( [ 'freelancer', 'tracking', 'whitelabel', 'energy' ] === array_column( $footer['routes'], 'route' ), 'footer ways follow the header order' );
nav_check( [ [ 'projekt' ], [ 'tracking' ], [ 'aufgabe' ], [ 'marktcheck', 'analyse', 'sofort' ] ] === array_column( $footer['routes'], 'doors' ), 'footer ways carry their door keys; the Energy way ends in the paid ladder' );
nav_check( ! array_key_exists( 'picks', $footer ), 'footer contract no longer carries landing-page picks' );
nav_check( 'Ich bin Solar- oder Wärmepumpenbetrieb und kaufe heute Portal-Anfragen.' === implode( '', array_map( static function ( $part ) use ( $footer ) { return $footer['routes'][3][ $part ]; }, [ 'pre', 'strong', 'post' ] ) ), 'footer Energy sentence names today\'s portal buyers' );
nav_check( count( hu_get_site_footer_door_urls() ) === count( array_unique( hu_get_site_footer_door_urls() ) ) && 0 < count( hu_get_site_footer_door_urls() ), 'footer door URLs are listed once for the SEO cockpit' );
nav_check( [ 'Leistungen', 'Belege & Person', 'Wissen', 'Rechtliches' ] === array_column( $footer['directory'], 'title' ), 'footer directory has four groups' );

$directory_items = array_merge( ...array_column( $footer['directory'], 'items' ) );
$by_label        = array_column( $directory_items, 'url', 'label' );
nav_check( ( $by_label['Server-Side Tracking'] ?? '' ) === $routes['tracking_b2b'], 'specialist page keeps a sitewide link with its exact name' );
nav_check( '/performance-marketing/' === nav_path( $by_label['Performance Marketing'] ?? '' ), 'Performance Marketing query owner is linked sitewide' );

// --- 1b. Doors: one decision for header and footer ------------------------------

$doors     = hu_funnel_doors();
// Die Betraege stehen hier nicht als Zahl: auch Tests tragen keine Preisliterale (scripts/canon-guard.sh).
$door_spec = [
	'projekt'    => [ 'Projekt anfragen', 'Projekt', '', '/kontakt/?type=project', 'nav_header_project' ],
	'ersteinschaetzung' => [ hu_first_assessment_text( 'cta' ), hu_first_assessment_text( 'label' ), '', '/kontakt/?focus=ersteinschaetzung', 'nav_header_ersteinschaetzung' ],
	'tracking'   => [ 'Tracking anfragen', 'Tracking', 'ab ' . hu_tracking_price( 'measurement', 'setup' ), '/kontakt/?type=project&focus=tracking', 'nav_header_door_tracking' ],
	'aufgabe'    => [ 'Test-Sprint anfragen', 'Test-Sprint', hu_format_eur( HU_WHITELABEL_TEST_SPRINT_PRICE ), '/whitelabel-retainer/#aufgabe', 'nav_header_door_whitelabel' ],
	'marktcheck' => [ 'Marktcheck', 'Marktcheck', hu_format_eur( 0 ), '/solar-waermepumpen-leadgenerierung/#marktcheck', 'nav_header_door_marktcheck' ],
	'analyse'    => [ 'Analyse anfragen', 'Analyse', hu_analysis_price(), '/solar-waermepumpen-leadgenerierung/#einstieg', 'nav_header_door_analyse' ],
	'sofort'     => [ 'Sofortkontakt', 'Sofortkontakt', hu_entry_setup_price(), '/solar-waermepumpen-leadgenerierung/#einstieg', 'nav_header_door_sofortkontakt' ],
];

nav_check( array_keys( $door_spec ) === array_keys( $doors ), 'doors include the homepage assessment and the six footer doors' );

foreach ( $door_spec as $key => $spec ) {
	$door = $doors[ $key ] ?? [];
	nav_check(
		[ $spec[0], $spec[1], $spec[2], $spec[3], $spec[4] ] === [
			$door['label'] ?? '',
			$door['short'] ?? '',
			$door['amount'] ?? '',
			str_replace( 'https://hasimuener.de', '', (string) ( $door['url'] ?? '' ) ),
			$door['track'] ?? '',
		],
		"door {$key}: label, amount, target and tracking action match the matrix"
	);
}

// Betrag nur aus dem Kanon. Ein "ab"-Betrag ist die echte Untergrenze dessen, was hinter der Tuer liegt.
nav_check( $doors['tracking']['amount'] === 'ab ' . hu_tracking_price( 'measurement', 'setup' ), 'tracking door amount is the canon measurement setup' );
nav_check( min( HU_TRACKING_STANDARD_SETUP, HU_TRACKING_PRO_SETUP, HU_TRACKING_CUSTOM_SETUP_MIN ) >= HU_TRACKING_MEASUREMENT_SETUP, 'tracking "ab" amount is not above any price of the tracking ladder' );
nav_check( $doors['aufgabe']['amount'] === hu_format_eur( HU_WHITELABEL_TEST_SPRINT_PRICE ), 'White-Label door amount is the canon test sprint price' );
nav_check( $doors['analyse']['amount'] === hu_analysis_price() && $doors['sofort']['amount'] === hu_entry_setup_price(), 'analysis and Sofortkontakt amounts come from the canon' );
nav_check( '' === $doors['projekt']['amount'], 'project door bundles several products and carries no amount' );
nav_check( [ 'tracking' ] === array_keys( array_filter( array_column( $doors, 'amount', 'key' ), static function ( $amount ) { return 0 === strpos( (string) $amount, 'ab ' ); } ) ), 'only the tracking door carries an "ab" amount' );

foreach ( [ 'inc/funnel-doors.php', 'template-parts/site-header.php', 'template-parts/site-footer.php' ] as $source ) {
	nav_check( ! preg_match( '/\d[\d.]*\s*(?:€|EUR|&euro;)/u', (string) file_get_contents( get_stylesheet_directory() . '/' . $source ) ), "{$source}: no price literal, amounts come from the canon" );
}

// Der Schalter HU_FEATURE_SOLAR_DOORS: aus -> #einstieg, an -> eigene Anker. Konstanten lassen
// sich nicht umdefinieren, deshalb ein zweiter Prozess.
$flag_run = shell_exec( escapeshellarg( PHP_BINARY ) . ' -r ' . escapeshellarg( 'define("HU_FEATURE_SOLAR_DOORS", true); require ' . var_export( __DIR__ . '/navigation-harness.php', true ) . '; nav_test_use_context("imprint"); echo json_encode(array_column(hu_funnel_doors(), "url", "key"));' ) );
$flag_on  = json_decode( (string) $flag_run, true );
nav_check( 'https://hasimuener.de/solar-waermepumpen-leadgenerierung/#einstieg' === $doors['analyse']['url'] && $doors['analyse']['url'] === $doors['sofort']['url'], 'flag off: analysis and Sofortkontakt doors point at #einstieg' );
nav_check( is_array( $flag_on ) && str_ends_with( (string) $flag_on['analyse'], '/#analyse' ) && str_ends_with( (string) $flag_on['sofort'], '/#sofortkontakt' ) && str_ends_with( (string) $flag_on['marktcheck'], '/#marktcheck' ), 'flag on: analysis and Sofortkontakt doors point at their own anchors' );

// --- 1c. Decision per context ---------------------------------------------------

$expect_funnel = [
	'home'          => [ 'voll', 'ersteinschaetzung', 'freelancer' ],
	'tracking'      => [ 'voll', 'tracking', 'tracking' ],
	'server_side'   => [ 'voll', 'tracking', 'tracking' ],
	'case_study'    => [ 'voll', 'marktcheck', 'energy' ],
	'about'         => [ 'voll', 'projekt', '' ],
	'contact'       => [ 'voll', null, '' ],
	'agentur_local' => [ 'voll', 'projekt', '' ],
	'imprint'       => [ 'voll', 'projekt', '' ],
	'conversion'    => [ 'voll', 'projekt', '' ],
	'not_found'     => [ 'voll', 'projekt', '' ],
	'whitelabel'    => [ 'voll', 'aufgabe', 'whitelabel' ],
	'solar'         => [ 'fokus', null, 'energy' ],
	'portal'        => [ 'leser', 'sofort', 'energy' ],
	'article_lead'  => [ 'leser', 'marktcheck', 'energy' ],
	'article_track' => [ 'leser', 'tracking', 'tracking' ],
	'article_cro'   => [ 'leser', 'projekt', '' ],
	'article_plain' => [ 'leser', 'projekt', '' ],
];

foreach ( array_keys( nav_test_contexts() ) as $context ) {
	nav_test_use_context( $context );
	$decision = hu_funnel_context();
	nav_check( isset( $expect_funnel[ $context ] ) && $expect_funnel[ $context ] === [ $decision['mode'], $decision['door'], $decision['route'] ], "{$context}: funnel decision " . json_encode( $decision ) );
}

nav_test_use_context( 'imprint' );

$tracks = array_merge(
	array_column( hu_get_primary_navigation_contract(), 'track' ),
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
	'conversion'    => [],
	'not_found'     => [],
	// nexus_is_results_context() schliesst die Agenturseite ein; das gilt unveraendert.
	'whitelabel'    => [ 'White-Label' => 'page', 'Ergebnisse' => 'true' ],
];

// Mark per context which way the footer register flags as "Ihr Weg" and where its doors point.
$footer_door_order = [ 'projekt', 'tracking', 'aufgabe', 'marktcheck', 'analyse', 'sofort' ];

foreach ( nav_test_contexts() as $context => $definition ) {
	nav_test_use_context( $context );
	$mode        = $expect_funnel[ $context ][0];
	$door_key    = $expect_funnel[ $context ][1];
	$footer_html = nav_test_render( 'template-parts/site-footer.php' );
	$footer_x    = nav_dom( $footer_html );
	$register    = nav_links( $footer_html, '//nav[@class="register"]//a' );
	$route_now   = $expect_funnel[ $context ][2];

	// Door register: on every page except /kontakt/ and the Money Pages in hu_footer_register_suppressed_templates(),
	// six doors in one click, own way marked, not hidden.
	if ( in_array( $context, [ 'home', 'contact', 'conversion' ], true ) ) {
		nav_check( 0 === $footer_x->query( '//nav[@class="register"]' )->length, "{$context}: footer skips the door register" );
		nav_check( 1 === $footer_x->query( '//footer[@id="footer"]' )->length && 1 === $footer_x->query( '//nav[@aria-label="Weitere Seiten"]' )->length, "{$context}: footer keeps direct line, directory and sender" );
	} else {
		nav_check( 1 === $footer_x->query( '//nav[@class="register"]' )->length, "{$context}: footer shows the door register" );
		nav_check( $footer_door_order === array_column( $register, 'door' ), "{$context}: every door is one click from the footer, in header order" );
		nav_check( array_map( static function ( $key ) { return 'cta_footer_door_' . $key; }, $footer_door_order ) === array_column( $register, 'track' ), "{$context}: footer doors carry cta_footer_door_<key>" );

		$marked = $footer_x->query( '//div[contains(concat(" ", @class, " "), " ist-hier ")]' );
		nav_check( '' === $route_now ? 0 === $marked->length : ( 1 === $marked->length && 'fuss-weg-' . $route_now . '' === ( $marked->item( 0 )->getAttribute( 'aria-labelledby' ) ) ), "{$context}: own way " . ( '' === $route_now ? 'none marked' : "{$route_now} marked, not hidden" ) );
		nav_check( 4 === $footer_x->query( '//nav[@class="register"]//div[@class="tueren" or contains(@class,"weg")]/p[@class="satz"]' )->length, "{$context}: all four ways stay in the register" );

		foreach ( $register as $index => $door_link ) {
			$spec_row = $door_spec[ $door_link['door'] ];
			$target   = str_replace( 'https://hasimuener.de', '', html_entity_decode( $door_link['href'] ) );
			$on_page  = 0 === strpos( $target, '#' ) && str_ends_with( $spec_row[3], $target ) && 0 === strpos( $spec_row[3], nav_path( $definition['path'] ) );
			nav_check( $spec_row[3] === $target || $on_page, "{$context}: footer door {$door_link['door']} targets " . ( $on_page ? "the anchor {$target} of its own page" : $spec_row[3] ) );
			$amount_node = $footer_x->query( '(//nav[@class="register"]//a)[' . ( $index + 1 ) . ']/span[@class="betrag"]' )->item( 0 );
			$expected_amount = 'tracking' === $door_link['door'] ? 'ab ' . hu_tracking_price( 'standard', 'setup' ) : $spec_row[2];
			nav_check( $amount_node && ( '' === $expected_amount ? 'nach Umfang' : $expected_amount ) === trim( $amount_node->textContent ), "{$context}: footer door {$door_link['door']} amount " . ( '' === $expected_amount ? 'nach Umfang' : 'from the canon' ) );
		}
	}

	// On the page that owns the anchors, the doors point at the page itself.
	if ( 'solar' === $context ) {
		nav_check( [ '#marktcheck', '#einstieg', '#einstieg' ] === array_slice( array_column( $register, 'href' ), 3 ), 'solar: Energy doors point at anchors of the page' );
	}

	$header_html = nav_test_render( 'template-parts/site-header.php' );
	$row         = nav_links( $header_html, '//nav[@aria-label="Hauptnavigation"]//a' );
	$sheet       = nav_links( $header_html, '//div[@data-leiste-blatt]//nav//a' );
	$trail       = nav_links( $header_html, '//nav[@class="pfad"]//a' );
	$footer_nav  = nav_links( $footer_html, '//nav[@class="verzeichnis"]//a' );
	$header_x    = nav_dom( $header_html );
	$doors_html  = nav_links( $header_html, '//a[contains(concat(" ", @class, " "), " tuer ")]' );
	$header_el   = $header_x->query( '//header' )->item( 0 );

	$expected_header_class = "leiste leiste--{$mode}" . ( 'home' === $context ? ' st-messkopf tafel' : '' );
	nav_check( $header_el && $expected_header_class === preg_replace( '/ nexus-article-reader-header$/', '', $header_el->getAttribute( 'class' ) ), "{$context}: header renders mode {$mode} with dark measurement tokens only on home" );

	if ( 'voll' === $mode ) {
		$sheet_without_about = array_values( array_filter( $sheet, static function ( $link ) { return 'Über Haşim' !== $link['text']; } ) );
		nav_check( array_column( $row, 'href' ) === array_column( $sheet_without_about, 'href' ), "{$context}: row lists the sheet targets in the same order, without Über Haşim" );
		nav_check( [ 'Projekte', 'Tracking', 'White-Label', 'Solar & Wärmepumpe', 'Ergebnisse', 'Über Haşim' ] === array_column( $sheet, 'text' ), "{$context}: sheet lists all six points" );
		nav_check( 1 === $header_x->query( '//nav[@aria-label="Hauptnavigation"]//a[@class="beleg"]' )->length && 'Ergebnisse' === $row[4]['text'], "{$context}: hairline sits before Ergebnisse" );

		$current = [];
		foreach ( $sheet as $link ) {
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
	} elseif ( 'fokus' === $mode ) {
		$ladder = nav_links( $header_html, '//nav[@class="leiter"]//a' );
		nav_check( [] === $row && [] === $sheet && 0 === $header_x->query( '//button[@data-leiste-klappe]' )->length, "{$context}: focus mode has no main menu, sheet or menu button" );
		nav_check( [ 'marktcheck', 'analyse', 'sofort' ] === array_column( $ladder, 'door' ), "{$context}: focus ladder is Marktcheck, Analyse, Sofortkontakt" );
		nav_check( [ '#marktcheck', '#einstieg', '#einstieg' ] === array_column( $ladder, 'href' ), "{$context}: focus ladder points at anchors of the page" );
		nav_check( [ 'nav_header_door_marktcheck', 'nav_header_door_analyse', 'nav_header_door_sofortkontakt' ] === array_column( $ladder, 'track' ), "{$context}: focus ladder keeps the nav_header_door_* actions" );
		nav_check(
			[ 'Marktcheck ' . $door_spec['marktcheck'][2], 'Analyse ' . $door_spec['analyse'][2], 'Sofortkontakt ' . $door_spec['sofort'][2] ] === array_column( $ladder, 'text' ),
			"{$context}: focus ladder shows each step with its canon amount"
		);
	} else {
		nav_check( [] === $row && [] === $sheet && 0 === $header_x->query( '//button[@data-leiste-klappe]' )->length, "{$context}: reader mode has no main menu, sheet or menu button" );
		nav_check( [ 'Wissen' ] === [ $trail[0]['text'] ?? '' ] && [ 'article_reader_back_blog', 'article_reader_open_dossier' ] === array_column( $trail, 'track' ), "{$context}: article path Wissen / dossier keeps its tracking" );
		nav_check( $header_el && false !== strpos( $header_el->getAttribute( 'class' ), 'nexus-article-reader-header' ), "{$context}: reader hook class stays for the article stylesheets" );
		// Das Dossier-Etikett steht als CSS-String im Beitrag: Umlaute roh, kein JSON-Escape (\u00f6 ist in CSS kein Escape).
		nav_check( 1 === preg_match( '/content: "([^"]*)";/', $header_html, $label_match ) && false === strpos( $label_match[1], '\\u' ) && html_entity_decode( strip_tags( $trail[1]['text'] ?? '' ) ) === $label_match[1], "{$context}: dossier label is a plain CSS string equal to the path label" );
	}

	// One door per context, the same one in the row and in the sheet.
	if ( null === $door_key ) {
		nav_check( [] === $doors_html, "{$context}: no header door" );
	} else {
		$spec = $door_spec[ $door_key ];
		nav_check( count( $doors_html ) === ( 'voll' === $mode ? 2 : 1 ), "{$context}: door " . ( 'voll' === $mode ? 'in row and sheet' : 'in the row' ) );

		foreach ( $doors_html as $door_link ) {
			nav_check(
				$door_key === $door_link['door'] && $spec[4] === $door_link['track'] && $spec[3] === str_replace( 'https://hasimuener.de', '', html_entity_decode( $door_link['href'] ) ),
				"{$context}: door {$door_key} carries data-door, tracking action and target"
			);
		}

		$door_xpath = nav_dom( $header_html );
		$label      = trim( (string) $door_xpath->query( '(//a[contains(@class,"tuer")])[1]/span[@class="lang"]' )->item( 0 )->textContent );
		$short      = trim( (string) $door_xpath->query( '(//a[contains(@class,"tuer")])[1]/span[@class="kurz"]' )->item( 0 )->textContent );
		$amount     = $door_xpath->query( '(//a[contains(@class,"tuer")])[1]/span[@class="preis"]' )->item( 0 );
		nav_check(
			trim( $spec[0] . ' →' ) === $label || $spec[0] === trim( str_replace( '→', '', $label ) ),
			"{$context}: door label {$spec[0]}"
		);
		nav_check( $spec[1] === $short, "{$context}: door short label {$spec[1]}" );
		nav_check( '' === $spec[2] ? null === $amount : ( $amount && $spec[2] === trim( $amount->textContent ) ), "{$context}: door amount " . ( '' === $spec[2] ? 'none' : $spec[2] ) );
	}

	if ( in_array( $context, [ 'tracking', 'server_side' ], true ) ) {
		nav_check( 'fuss-weg-tracking' === $footer_x->query( '//div[contains(@class,"ist-hier")]' )->item( 0 )->getAttribute( 'aria-labelledby' ), "{$context}: both tracking pages mark the tracking way" );
	}

	if ( 'home' === $context ) {
		$sig = $header_x->query( '//a[contains(@class,"sig")]' )->item( 0 );
		nav_check( $sig && 'page' === $sig->getAttribute( 'aria-current' ), 'home: wordmark marks the current page' );
	}

	foreach ( array_merge( $row, $sheet, $trail, $footer_nav ) as $link ) {
		$href = $link['href'];
		if ( 0 !== strpos( $href, 'https://hasimuener.de' ) ) {
			nav_check( 1 === preg_match( '#^(mailto:|tel:|https://www\.linkedin\.com/)#', $href ), "{$context}: external link is intended: {$href}" );
			continue;
		}
		$path     = nav_path( $href );
		$fragment = (string) wp_parse_url( $href, PHP_URL_FRAGMENT );
		nav_check( ! in_array( $path, $retired, true ) && 'marktcheck' !== $fragment, "{$context}: no retired or Marktcheck target outside the door: {$href}" );
		nav_check(
			'' === $fragment ? in_array( $path, $known_paths, true ) : in_array( $path . '#' . $fragment, $known_anchors, true ),
			"{$context}: known public route: {$href}"
		);
		nav_check( '' !== $link['track'], "{$context}: tracked: {$link['text']}" );
	}

	// Marktcheck is a door of the Energy context only (AGENTS.md): header doors on other routes never point at it.
	foreach ( $doors_html as $door_link ) {
		$path     = nav_path( $door_link['href'] );
		$fragment = (string) wp_parse_url( html_entity_decode( $door_link['href'] ), PHP_URL_FRAGMENT );
		nav_check( ! in_array( $path, $retired, true ), "{$context}: door target is not retired: {$door_link['href']}" );
		nav_check( 'marktcheck' !== $fragment || 'energy' === $expect_funnel[ $context ][2], "{$context}: a Marktcheck door appears only in the Energy context" );
		nav_check( '' !== $door_link['track'] && '' !== $door_link['door'], "{$context}: door is tracked and keyed" );
	}

	$generic_to_specialist = array_filter( array_merge( $row, $footer_nav ), static function ( $link ) {
		return 'Tracking' === $link['text'] && '/server-side-tracking-b2b/' === nav_path( $link['href'] );
	} );
	nav_check( [] === $generic_to_specialist, "{$context}: plain \"Tracking\" never points at the specialist page" );
}

// --- 2b. White-Label landing page keeps its own header and its tracking, carries the same door ----

nav_test_use_context( 'whitelabel' );
ob_start();
get_template_part( 'template-parts/whitelabel-header', null, [
	'home_url'   => home_url( '/' ),
	'brand'      => 'HAŞIM ÜNER',
	'home_label' => 'Startseite',
	'form_url'   => home_url( '/whitelabel-retainer/?case=aufgabe#aufgabe' ),
	'nav'        => [ [ '#lieferfelder', 'Leistungen', 'nav_whitelabel_services' ], [ '#faq', 'Fragen', 'nav_whitelabel_faq' ] ],
] );
$wl_header = (string) ob_get_clean();
$wl_x      = nav_dom( $wl_header );
$wl_door   = $wl_x->query( '//a[contains(@class,"tuer")]' )->item( 0 );
nav_check( null !== $wl_door && 'aufgabe' === $wl_door->getAttribute( 'data-door' ), 'white-label header carries the Test-Sprint door with data-door' );
nav_check( null !== $wl_door && 'cta_whitelabel_header_task_brief' === $wl_door->getAttribute( 'data-track-action' ) && 'whitelabel_header' === $wl_door->getAttribute( 'data-track-section' ), 'white-label door keeps its existing tracking action' );
nav_check( null !== $wl_door && $wl_door->hasAttribute( 'data-wl-form-link' ), 'white-label door keeps data-wl-form-link for whitelabel.js' );
nav_check( null !== $wl_door && str_ends_with( $wl_door->getAttribute( 'href' ), '#aufgabe' ), 'white-label door targets the form anchor' );
nav_check( null !== $wl_door && $door_spec['aufgabe'][2] === trim( (string) $wl_x->query( './/span[@class="preis"]', $wl_door )->item( 0 )->textContent ), 'white-label door amount comes from the canon' );
nav_check( [ 'nav_whitelabel_services', 'nav_whitelabel_faq' ] === array_column( nav_links( $wl_header, '//nav[@data-wl-anker]//a' ), 'track' ), 'white-label page anchors keep their nav_whitelabel_* actions' );
nav_check( 'nav_whitelabel_home' === ( nav_links( $wl_header, '//a[contains(@class,"sig")]' )[0]['track'] ?? '' ), 'white-label wordmark keeps nav_whitelabel_home' );
nav_check( false !== strpos( (string) file_get_contents( get_stylesheet_directory() . '/page-whitelabel-retainer.php' ), "'template-parts/whitelabel-header'" ), 'white-label template renders its header through the partial' );

// --- 3. 404 recovery links ----------------------------------------------------

nav_test_use_context( 'not_found' );
$not_found = nav_links( nav_test_render( '404.php' ), '//div[contains(@class,"nexus-404__links")]//a' );
nav_check( [ 'Startseite', 'Projekte', 'Tracking', 'White-Label', 'Solar & Wärmepumpe', 'Blog' ] === array_column( $not_found, 'text' ), '404: recovery links mirror the header routes' );
nav_check( ! preg_grep( '/marktcheck/i', array_column( $not_found, 'href' ) ), '404: no sitewide Marktcheck link' );
nav_check( count( $not_found ) === count( array_unique( array_column( $not_found, 'track' ) ) ), '404: tracking actions are unique' );

// Die Solar-Seite hat genau einen Kopf: die Leiste im Modus fokus. Die frueher im Template
// stehende Kopfzeile (.strecke-kopfleiste) wuerde sie verdoppeln.
$solar_template = (string) file_get_contents( get_stylesheet_directory() . '/page-solar-waermepumpen-leadgenerierung.php' );
nav_check( false === strpos( $solar_template, '<header' ) && false === strpos( $solar_template, 'strecke-kopfleiste' ), 'solar: the page template carries no header of its own' );
nav_check( false === strpos( (string) file_get_contents( get_stylesheet_directory() . '/assets/css/anfragestrecke.css' ), 'strecke-kopfleiste' ), 'solar: no leftover Kopfleiste styles' );
// Am Render-Ergebnis: Kopf der Harness (Leiste, Modus fokus) plus das echte Seitentemplate.
$solar_page = (string) shell_exec( 'php ' . escapeshellarg( __DIR__ . '/render-page.php' ) . ' solar page-solar-waermepumpen-leadgenerierung.php assets/css/system.css 2>/dev/null' );
nav_check( '' !== $solar_page && 1 === preg_match_all( '/<header\b[^>]*\bclass="leiste\b/s', $solar_page ) && false === strpos( $solar_page, 'strecke-kopfleiste' ), 'solar: the rendered page has exactly one header, the bar in mode fokus' );

echo $failures ? "\n{$failures} navigation invariant(s) failed.\n" : "\nNavigation contract ok.\n";
exit( $failures ? 1 : 0 );
