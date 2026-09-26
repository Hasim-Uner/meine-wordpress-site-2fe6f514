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
check( HU_LANDINGPAGE_PRICE < HU_FREELANCER_WEBSITE_MIN, 'a landing page costs less than Website Kompakt' );
check( HU_FREELANCER_WEBSITE_EXTRA_PAGE < HU_LANDINGPAGE_PRICE, 'an extra website page costs less than a landing page' );
check( HU_WHITELABEL_LANDINGPAGE_MIN <= (int) round( HU_LANDINGPAGE_PRICE * 0.75 ), 'agencies pay at least 25 % less than end customers for a landing page' );
check( str_contains( hu_freelancer_website_scope_display(), hu_freelancer_website_extra_page_price( true ) ), 'website scope phrase names the extra page price' );

echo "OK tracking-ladder\n";
