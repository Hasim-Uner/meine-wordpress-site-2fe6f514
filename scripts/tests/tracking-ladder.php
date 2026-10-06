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

// Anfrage-Website und Landingpage:
// Grundprodukt und Seitentypen bleiben unter einer eigenständigen Landingpage;
// Tracking/CRM sind projektweite Beiträge, nicht pro URL.
check( HU_FREELANCER_WEBSITE_MIN === 1900 && HU_FREELANCER_WEBSITE_PAGES === 1, 'approved request website includes its first main page' );
check( HU_WEBSITE_PAGE_UTILITY === 300 && HU_WEBSITE_PAGE_STANDARD === 400 && HU_WEBSITE_PAGE_SALES === 790, 'website page types have distinct approved prices' );
check( HU_WEBSITE_COPY_BASE === 290 && HU_WEBSITE_COPY_UTILITY === 50 && HU_WEBSITE_COPY_STANDARD === 150 && HU_WEBSITE_COPY_SALES === 290, 'copy is priced independently by page type' );
check( HU_LANDINGPAGE_PRICE === 1990, 'landing page with copy remains unchanged' );
check( HU_WEBSITE_PAGE_SALES < HU_LANDINGPAGE_PRICE, 'an additional sales page reusing the website system costs less than a standalone landing page' );
check( HU_WHITELABEL_LANDINGPAGE_MIN <= (int) round( HU_LANDINGPAGE_PRICE * 0.75 ), 'agencies pay at least 25 % less than end customers for a landing page' );
check( str_contains( hu_freelancer_website_scope_display(), hu_format_eur( HU_WEBSITE_PAGE_UTILITY ) ), 'website scope phrase names the starting additional-page price' );

$ready = hu_website_quote( 1 );
check( 1900 === $ready['price'] && 2 === $ready['days'] && 0 === $ready['preparation_days'], 'prepared base scope is 1900 euros and two production days' );

$mixed = hu_website_quote( 5, 'neubau', false, [
	'utility_pages' => 1,
	'standard_pages' => 1,
	'sales_pages' => 2,
] );
check( 4180 === $mixed['price'] && 5 === $mixed['days'] && [ 'utility' => 1, 'standard' => 1, 'sales' => 2 ] === $mixed['page_types'], 'mixed five-page scope prices and schedules each page type' );

$mixed_copy = hu_website_quote( 5, 'neubau', false, [
	'utility_pages' => 1,
	'standard_pages' => 1,
	'sales_pages' => 2,
	'texte' => 1,
] );
check( 1070 === $mixed_copy['text_price'] && 5250 === $mixed_copy['price'] && 1.5 === $mixed_copy['components']['texts'], 'copy price and time follow the selected page types' );

check( 3 === hu_website_quote( 1, 'neubau', true )['days'], 'tracking adds one project day' );
check( 5 === hu_website_quote( 1, 'neubau', true, [ 'design' => 'neu', 'design_layouts' => 1 ] )['days'], 'one custom layout and tracking add their distinct phases' );
check( 2 === hu_website_quote( 1, 'neubau', false, [ 'design' => 'vorhanden' ] )['days'] && hu_website_quote( 1, 'neubau', false, [ 'design' => 'vorhanden' ] )['price_review'], 'supplied designs avoid a new design phase but require preflight' );

$shared = hu_website_quote( 4, 'neubau', false, [ 'design' => 'neu', 'design_layouts' => 2 ] );
check( 4040 === $shared['price'] && 940 === $shared['design_price'] && 6 === $shared['days'], 'legacy four-page scope still reuses two custom layouts and remains price-compatible' );

$rounded = hu_website_quote( 2, 'neubau', false, [ 'texte' => 1, 'design' => 'neu', 'design_layouts' => 2 ] );
check( 0.75 === $rounded['components']['texts'] && 2.5 === $rounded['components']['design'] && 6 === $rounded['days'], 'partial-day phases are summed before rounding, not rounded independently' );

$all = hu_website_quote( 3, 'neubau', true, [ 'crm' => 1, 'design' => 'neu', 'design_layouts' => 3 ] );
check( 5770 === $all['price'] && 10 === $all['days'] && ! $all['duration_open'], 'all standardized extras form a complete legacy-compatible price and duration' );

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
