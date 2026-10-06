<?php
/**
 * Contract test for the tracking product ladder in
 * blocksy-child/inc/canon/pricing-canon.php.
 *
 * Bis 2026-09-26 stand derselbe Betrag auf zwei Seiten fuer zwei verschiedene
 * Produkte (docs/decisions/tracking-preisleiter.md). Dieser Test haelt die
 * Regeln fest, die das verhindern: vier Stufen in fester Reihenfolge, jede
 * teurer als die vorige, der Server-Side-Preis ist Stufe 2, und Agenturen
 * zahlen fuer dasselbe Setup weniger als Endkunden. Laedt den echten Kanon.
 */

define( 'ABSPATH', sys_get_temp_dir() . '/' );
require __DIR__ . '/../../blocksy-child/inc/canon/pricing-canon.php';

function check( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } echo "PASS $message\n"; }

$ladder = hu_tracking_product_ladder();

check( [ 'measurement', 'standard', 'pro', 'individual' ] === array_keys( $ladder ), 'ladder has four stages under the canon package keys' );
check( [ 1, 2, 3, 4 ] === array_column( $ladder, 'stage' ), 'stages are numbered 1 to 4 in order' );

foreach ( $ladder as $key => $product ) {
	check( '' !== $product['name'] && '' !== $product['scope'] && '' !== $product['lead'] && '' !== $product['terms'], "stage $key has name, scope, lead and terms" );
	check( count( $product['items'] ) >= 3, "stage $key lists its scope items" );
	check( $product['price'] === hu_tracking_price( $key, 'setup' ), "stage $key shows the canon setup price" );
}

check( 4 === count( array_unique( array_column( $ladder, 'name' ) ) ), 'every stage has its own name' );

$values = array_map( static fn( $key ) => (int) hu_tracking_price( $key, 'setup', 'value' ), array_keys( $ladder ) );
for ( $i = 1; $i < count( $values ); $i++ ) {
	check( $values[ $i ] > $values[ $i - 1 ], sprintf( 'stage %d costs more than stage %d', $i + 1, $i ) );
}

check( HU_TRACKING_STANDARD_SETUP === (int) hu_tracking_price( 'standard', 'setup', 'value' ), 'the Server-Side price is stage 2' );
check( HU_WHITELABEL_SERVER_SIDE_MIN < HU_TRACKING_STANDARD_SETUP, 'agencies pay less than end customers for the Server-Side setup' );
check( HU_TRACKING_MEASUREMENT_WEEKS_MAX <= HU_TRACKING_DURATION_WEEKS_MIN, 'stage 1 is delivered no later than the Server-Side stages start' );

check( str_starts_with( $ladder['standard']['items'][0], 'Alles aus ' . $ladder['measurement']['name'] ), 'stage 2 includes stage 1 by name' );
check( str_starts_with( $ladder['pro']['items'][0], 'Alles aus ' . $ladder['standard']['name'] ), 'stage 3 includes stage 2 by name' );

check( str_ends_with( hu_tracking_ladder_display(), ' netto' ) && 4 === substr_count( hu_tracking_ladder_display(), ' €' ), 'ladder phrase names all four prices once' );
check( 3 === substr_count( hu_tracking_ladder_display( 2 ), ' €' ), 'ladder phrase from stage 2 leaves stage 1 out' );

// Website Kompakt und Landingpage (docs/decisions/preise-website-landingpage.md):
// eine Seite kostet weniger als das kleinste Website-Paket, eine Zusatzseite
// weniger als eine Landingpage, und Agenturen zahlen fuer die Landingpage
// rund 30 % weniger als Endkunden.
check( HU_FREELANCER_WEBSITE_MIN === 1900 && HU_FREELANCER_WEBSITE_PAGES === 1, 'approved request website includes its first page' );
check( HU_LANDINGPAGE_PRICE === 1990, 'landing page with copy remains unchanged' );
check( HU_FREELANCER_WEBSITE_EXTRA_PAGE < HU_LANDINGPAGE_PRICE, 'an extra website page costs less than a landing page' );
check( HU_WHITELABEL_LANDINGPAGE_MIN <= (int) round( HU_LANDINGPAGE_PRICE * 0.75 ), 'agencies pay at least 25 % less than end customers for a landing page' );
check( str_contains( hu_freelancer_website_scope_display(), hu_freelancer_website_extra_page_price( true ) ), 'website scope phrase names the extra page price' );

$ready = hu_website_quote( 1 );
check( 2 === $ready['days'] && 0 === $ready['preparation_days'], 'prepared scope includes implementation and QA in two days' );
check( 2 === hu_website_quote( 5 )['days'] && 3 === hu_website_quote( 6 )['days'] && 3 === hu_website_quote( 10 )['days'], 'implementation tiers change at the sixth page' );
check( 3 === hu_website_quote( 1, 'neubau', true )['days'], 'tracking adds one project day' );
check( 5 === hu_website_quote( 1, 'neubau', true, [ 'design' => 'neu', 'design_layouts' => 1 ] )['days'], 'one custom layout and tracking add their distinct phases' );
check( 4 === hu_website_quote( 3, 'neubau', false, [ 'texte' => 1 ] )['days'], 'three pages with included new texts have four production days' );
check( 2 === hu_website_quote( 1, 'neubau', false, [ 'design' => 'vorhanden' ] )['days'] && hu_website_quote( 1, 'neubau', false, [ 'design' => 'vorhanden' ] )['price_review'], 'supplied designs avoid a new design phase but require preflight' );
$shared = hu_website_quote( 4, 'neubau', false, [ 'design' => 'neu', 'design_layouts' => 2 ] );
check( 4040 === $shared['price'] && 940 === $shared['design_price'] && 5 === $shared['days'], 'four pages using two custom layouts pay once per layout' );
$rounded = hu_website_quote( 2, 'neubau', false, [ 'texte' => 1, 'design' => 'neu', 'design_layouts' => 2 ] );
check( 1.5 === $rounded['components']['texts'] && 2.5 === $rounded['components']['design'] && 6 === $rounded['days'], 'half-day phases are summed before rounding, not rounded independently' );
$all = hu_website_quote( 3, 'neubau', true, [ 'crm' => 1, 'design' => 'neu', 'design_layouts' => 3 ] );
check( 5770 === $all['price'] && 9 === $all['days'] && ! $all['duration_open'], 'all standardized extras form a complete price and duration' );
foreach ( range( 1, HU_WEBSITE_CALCULATOR_MAX ) as $pages ) {
	$plain = hu_website_quote( $pages );
	$tracked = hu_website_quote( $pages, 'neubau', true );
	check( 1 === $tracked['days'] - $plain['days'], "tracking setup is project-based at $pages pages" );
	$crm = hu_website_quote( $pages, 'neubau', false, [ 'crm' => 1 ] );
	check( 990 === $crm['price'] - $plain['price'] && 3 === $crm['days'] - $plain['days'] && ! $crm['duration_open'] && $crm['price_review'], "CRM has one fixed contribution and requires preflight at $pages pages" );
	$dashboard = hu_website_quote( $pages, 'neubau', false, [ 'dashboard' => 1 ] );
	check( $dashboard['duration_open'] && $dashboard['price_review'] && $plain['price'] === $dashboard['price'] && $plain['days'] === $dashboard['days'], "dashboard remains an explicitly unpriced extension at $pages pages" );
}

echo "OK tracking-ladder\n";
