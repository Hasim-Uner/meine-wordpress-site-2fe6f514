<?php
/** Real routing and editor repairs against the existing WordPress boundary. */
require __DIR__ . '/navigation-harness.php';
require __DIR__ . '/../../blocksy-child/inc/blog-provider-posts.php';

function solar_check( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
	echo 'PASS ' . $message . "\n";
}

nav_test_use_context( 'solar' );
$map = nexus_get_consolidated_solar_redirect_map();
solar_check( 5 === count( $map ), 'Five standalone routes are consolidated' );
$provisioned = array_column( nexus_get_provisioned_pages(), 'slug' );
foreach ( array_keys( $map ) as $path ) {
	solar_check( ! in_array( trim( $path, '/' ), $provisioned, true ), $path . ' is not recreated on deploy' );
}
$related = hu_get_solar_cluster_related_links( 'solar-leads-kaufen-alternative' );
solar_check( 3 === count( $related ), 'Buy comparison has three useful retained siblings' );
foreach ( $related as $link ) {
	solar_check( ! isset( $map[ parse_url( $link['url'], PHP_URL_PATH ) ] ), 'Related link lands on a retained route' );
}
$target = hu_get_internal_redirect_target( '/qualifizierte-pv-anfragen/?utm_source=mail#old-section' );
solar_check( home_url( '/solar-leads-kaufen-alternative/?utm_source=mail#qualitaet' ) === $target, 'Campaign query stays before the new quality fragment' );
solar_check( '' === hu_get_internal_redirect_target( 'https://external.example/qualifizierte-pv-anfragen/' ), 'External source is not rewritten' );
solar_check( '' === hu_get_internal_redirect_target( '/qualifizierte-pv-anfragen-extra/' ), 'Unrelated paths are not matched by prefix' );

// Invalid historical input is assembled separately from public runtime facts.
$legacy_rate = implode( ' ', [ '12', '%' ] );
$content = '<p>Eigener redaktioneller Absatz bleibt.</p><p>Der [Solar Case Study](/case-study-solar-leadgenerierung/) demonstriert das mit 1.750 qualifizierten Anfragen und ' . $legacy_rate . ' Abschlussquote.</p><p>[Vergleich](/eigene-leadgenerierung-vs-portale/)</p>';
$after = hu_normalize_solar_provider_editor_content( $content );
solar_check( str_contains( $after, '<p>Eigener redaktioneller Absatz bleibt.</p>' ), 'Editor-owned paragraphs are preserved' );
solar_check( ! str_contains( $after, '12 %' ) && ! str_contains( $after, '1.750 qualifizierten' ), 'Known stale case paragraph is repaired without inventing figures' );
solar_check( str_contains( $after, home_url( '/solar-leads-kaufen-alternative/#vergleich' ) ), 'Relative provider Markdown points directly at the retained comparison' );
solar_check( $after === hu_normalize_solar_provider_editor_content( $after ), 'Provider repair is idempotent' );
$custom = '<p>Eigene Erfahrung: 12 % im eigenen Betrieb.</p>';
solar_check( $custom === hu_normalize_solar_provider_editor_content( $custom ), 'Unrelated author figures are not replaced' );
$future_seed = hu_lead_provider_format_inline_markdown( '[Kosten](/solar-leads-kosten-studie/)' );
solar_check( str_contains( $future_seed, '<a href="' . home_url( '/solar-leads-kosten-studie/' ) . '">' ), 'Future seeds support root-relative internal links' );
$label = '<p>[TCO-Vergleich Portal-Leads vs. eigenes Anfragesystem über 24 Monate](/eigene-leadgenerierung-vs-portale/)</p>';
solar_check( ! str_contains( hu_normalize_solar_provider_editor_content( $label ), 'über 24 Monate' ), 'Known provider anchor describes the retained comparison' );
echo "OK solar-consolidation\n";
