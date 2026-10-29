<?php
/** Regression checks for duplicate article routing and template-owned SEO data. */
require __DIR__ . '/navigation-harness.php';
require_once get_stylesheet_directory() . '/inc/seo-meta.php';

function seo_article_check( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	echo 'PASS ' . $message . "\n";
}

nav_test_use_context( 'home' );
$owner = home_url( '/wordpress-wartung-kosten/' );
$map   = nexus_get_legacy_offer_redirect_map();
seo_article_check( $owner === $map['/wordpress-wartung-kosten-2/'], 'Duplicate article redirects directly to the retained owner' );
seo_article_check( ! isset( $map['/wordpress-wartung-kosten/'] ), 'Retained article never redirects back to its duplicate' );
seo_article_check( $owner . '?utm_source=mail' === hu_get_internal_redirect_target( '/wordpress-wartung-kosten-2/?utm_source=mail' ), 'Duplicate consolidation retains campaign attribution' );
seo_article_check( in_array( 'wordpress-wartung-kosten-2', nexus_get_sitemap_excluded_slugs(), true ), 'Duplicate is excluded from the native sitemap even before editor cleanup' );
seo_article_check( ! in_array( 'wordpress-wartung-kosten', nexus_get_sitemap_excluded_slugs(), true ), 'Retained article stays eligible for the sitemap' );

seo_article_check( hu_get_commercial_route( 'technical_seo' ) === nexus_get_primary_public_url( 'seo' ), 'Generic SEO links agree with the technical SEO offer' );
seo_article_check( home_url( '/technisches-seo-wordpress/#pruefumfang' ) === nexus_get_primary_public_url( 'cwv' ), 'CWV links reach the performance assessment scope' );
seo_article_check( home_url( '/wordpress-agentur-hannover/' ) === nexus_get_primary_public_url( 'agentur' ), 'Local agency route retains its own destination' );

// The fixture intentionally supplies an unrelated stored title (Einordnung).
// Verify the actual rendered page, rather than only comparing PHP literals.
$process = proc_open(
	[ 'php', __DIR__ . '/render-article.php', 'technisches-seo-performance-fundament' ],
	[ 0 => [ 'pipe', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
	$pipes
);
if ( ! is_resource( $process ) ) {
	throw new RuntimeException( 'Could not render the SEO article fixture' );
}
fclose( $pipes[0] );
$html  = stream_get_contents( $pipes[1] );
$error = stream_get_contents( $pipes[2] );
fclose( $pipes[1] );
fclose( $pipes[2] );
seo_article_check( 0 === proc_close( $process ), 'Real cornerstone template renders: ' . $error );

$dom = new DOMDocument();
libxml_use_internal_errors( true );
$dom->loadHTML( $html );
libxml_clear_errors();
$xpath    = new DOMXPath( $dom );
$articles = [];
foreach ( $xpath->query( '//script[@type="application/ld+json"]' ) as $script ) {
	$node = json_decode( $script->textContent, true, 512, JSON_THROW_ON_ERROR );
	if ( 'Article' === ( $node['@type'] ?? '' ) ) {
		$articles[] = $node;
	}
}
$h1 = $xpath->query( '//h1' );
seo_article_check( 1 === $h1->length && 1 === count( $articles ), 'Cornerstone has one H1 and one Article node' );
seo_article_check( trim( $h1->item( 0 )->textContent ) === $articles[0]['headline'], 'Visible headline and Article headline agree despite stale stored title' );
seo_article_check( hu_get_seo_cornerstone_identity()['description'] === $articles[0]['description'], 'Article description uses the current central metadata' );
$case = $xpath->query( '//*[@id="praxisfall"]' )->item( 0 );
seo_article_check( null !== $case && str_contains( $case->textContent, hu_e3_metric( 'cpl_before' ) ) && str_contains( $case->textContent, hu_e3_metric( 'cpl_after' ) ), 'Case heading reads both costs from the proof canon' );
seo_article_check( str_contains( $html, 'kein Durchschnitt über den gesamten Projektzeitraum' ), 'Case keeps the achieved value separate from a period average' );
echo "OK seo-article-consistency\n";
