<?php
/**
 * Standalone glossary rendering contract smoke test (no WordPress/database).
 * Run: php scripts/smoke-glossary.php [--render-dir=/absolute/preview/path]
 * The small WordPress doubles below only resolve posts, URLs and escaping.
 * They do not claim to validate database sync, cache purges or production SEO.
 */

define( 'ABSPATH', __DIR__ . '/' );
error_reporting( E_ALL );
set_error_handler( static function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

class WP_Post {
	public $ID;
	public $post_name;
	public $post_title;
	public $post_type = 'glossary_term';
	public $post_status = 'publish';
}
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function remove_accents( $value ) { return strtr( $value, [ 'ä' => 'a', 'ö' => 'o', 'ü' => 'u', 'ß' => 'ss' ] ); }
function sanitize_title( $value ) { return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( remove_accents( $value ) ) ), '-' ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) ); }
function wp_strip_all_tags( $value ) { return strip_tags( $value ); }
function hu_normalize_brand_text( $value ) { return $value; }
function __( $value, $domain = '' ) { return $value; }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return esc_html( $value ); }
function esc_url( $value ) { return esc_attr( $value ); }
function nexus_get_page_id( $slugs ) { return 0; }
function nexus_get_page_id_by_template( $template ) { return 0; }
function get_posts( $args ) { return array_values( $GLOBALS['glossary_test_posts'] ); }
function get_permalink( $post ) { return home_url( '/glossar/' . $post->post_name . '/' ); }
function get_post( $post = null ) { return $post ?? $GLOBALS['glossary_test_post']; }
function get_post_meta( ...$args ) { return ''; }
function get_option( $key, $default = '' ) { return $default; }
function hu_get_commercial_route( $key, $fallback = '' ) { return home_url( '/kontakt/?type=project' ); }
function nexus_get_primary_public_url( $key, $fallback = '' ) {
	$routes = [ 'home' => '/', 'cro' => '/#angebot-funnel', 'tracking' => '/ga4-tracking-setup/', 'cwv' => '/wgos-assets/cwv-optimierung/', 'seo' => '/wordpress-agentur-hannover/#zusammenarbeit', 'wgos' => '/wordpress-agentur-hannover/#zusammenarbeit', 'agentur' => '/wordpress-agentur-hannover/', 'energy' => '/solar-waermepumpen-leadgenerierung/', 'solar_leads_alternative' => '/solar-leads-kaufen-alternative/' ];
	return isset( $routes[ $key ] ) ? home_url( $routes[ $key ] ) : $fallback;
}
function get_header() { echo '<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Glossar · Layoutprüfung</title><link rel="stylesheet" href="preview.css"></head><body><main id="main">'; }
function get_footer() { echo '</main><script src="glossary.js"></script></body></html>'; }
function have_posts() { return ! $GLOBALS['glossary_test_loop_done']; }
function the_post() { $GLOBALS['glossary_test_loop_done'] = true; }
function the_title() { echo esc_html( $GLOBALS['glossary_test_post']->post_title ); }
function get_the_excerpt() { return ''; }
function the_content() { echo '<p>Editor content fallback</p>'; }

require __DIR__ . '/../blocksy-child/inc/glossary/glossary.php';
require __DIR__ . '/../blocksy-child/inc/glossary/glossary-registry.php';

function glossary_check( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}
$registry = nexus_get_glossary_registry();
$GLOBALS['glossary_test_posts'] = [];
foreach ( $registry as $term ) {
	if ( ! nexus_glossary_term_requires_post( $term ) ) { continue; }
	$post = new WP_Post();
	$post->ID = count( $GLOBALS['glossary_test_posts'] ) + 1;
	$post->post_name = $term['slug'];
	$post->post_title = $term['title'];
	$GLOBALS['glossary_test_posts'][ $term['slug'] ] = $post;
}
$items = nexus_get_glossary_directory_items();
$visible_terms = array_filter( $registry, static function ( $term ) {
	return 'publish' === $term['status'] && $term['show_in_hub'];
} );
glossary_check( count( $items ) === count( $visible_terms ), 'Every visible published term needs a directory entry.' );
glossary_check( $registry['cta-hierarchie']['index_policy'] === 'noindex', 'Keep existing noindex policy.' );
glossary_check( $registry['wordpress-agentur-hannover']['index_policy'] === 'alias', 'Keep local query owner.' );
glossary_check( ! in_array( 'wordpress-agentur-hannover', array_column( $items, 'slug' ), true ), 'Local service is not a dictionary term.' );
$previous = '';
foreach ( $items as $item ) {
	glossary_check( strnatcasecmp( $previous, $item['title'] ) <= 0, 'Directory must be alphabetical.' );
	$previous = $item['title'];
	glossary_check( $item['url'] !== nexus_get_glossary_hub_url(), 'A term must not link back to the directory.' );
}
$definition_counts = array_fill_keys( array_keys( nexus_get_glossary_area_catalog() ), 0 );
foreach ( $registry as $term ) {
	glossary_check( ! preg_match( '/Primary URL|Head Term|Alias-Eintrag|Sub-Term/', $term['short_definition'] . $term['excerpt'] ), 'Public definition leaks SEO notes.' );
	if ( ! nexus_glossary_term_requires_post( $term ) ) { continue; }
	$definition_counts[ $term['core_area'] ]++;
	glossary_check( $term['show_in_hub'], 'Every definition must be discoverable in the directory.' );
	glossary_check( count( $term['mistakes'] ) >= 1, 'Every definition needs a practical mistake to avoid.' );
	glossary_check( count( nexus_get_glossary_related_primary_items( $term ) ) >= 1, 'Every definition needs a useful implementation link.' );
	foreach ( $term['related_terms'] as $related_slug ) {
		glossary_check( isset( $registry[ $related_slug ] ) && 'publish' === $registry[ $related_slug ]['status'], 'Related terms must exist and be published: ' . $related_slug );
		glossary_check( $related_slug !== $term['slug'], 'A related term must not link to itself.' );
	}
	$html = nexus_get_glossary_term_content_html( $term );
	glossary_check( ! preg_match( '/Primary URL|Index-Policy|Head Term|Marktcheck|wgos-/', $html ), 'Rendered content contains obsolete internals or funnel.' );
	glossary_check( strpos( $html, 'Ein Beispiel' ) !== false, 'Every detail needs a concrete example.' );
	glossary_check( strpos( $html, 'glossary-definition' ) !== false, 'Stored content must retain its definition.' );
}
foreach ( $definition_counts as $area => $count ) {
	glossary_check( $count >= 12, 'At least twelve real definitions are required in ' . $area . '; aliases do not count.' );
}
$match = $registry['message-match'];
$before = nexus_get_glossary_term_content_html( $match, false );
glossary_check( strpos( $before, '/glossar/lead-qualifizierung/' ) !== false, 'Resolve existing related details.' );
// Simulate a related term that becomes unavailable after content was saved.
$GLOBALS['glossary_test_posts']['lead-qualifizierung']->post_status = 'draft';
unset( $GLOBALS['nexus_glossary_post_lookup'] );
$after = nexus_get_glossary_term_content_html( $match, false );
glossary_check( strpos( $after, '/glossar/lead-qualifizierung/' ) === false, 'Do not retain stale related links.' );
glossary_check( ! in_array( 'lead-qualifizierung', array_column( nexus_get_glossary_directory_items(), 'slug' ), true ), 'Do not list unavailable detail pages.' );
glossary_check( strpos( $after, 'href="https://example.test/glossar/"' ) === false, 'Do not offer a hub fallback as a related definition.' );
$GLOBALS['glossary_test_posts']['lead-qualifizierung']->post_status = 'publish';
unset( $GLOBALS['nexus_glossary_post_lookup'] );
$escaped = $registry['utm-parameter'];
$escaped['example']['text'] = '<script>alert(1)</script>';
glossary_check( strpos( nexus_get_glossary_term_content_html( $escaped ), '<script>' ) === false, 'Example text must be escaped.' );
glossary_check( strpos( nexus_get_glossary_term_content_html( $registry['canonical-url'] ), '&lt;link rel=' ) !== false, 'Show HTML examples as code, not markup.' );

$render_dir = '';
foreach ( $argv as $arg ) { if ( strpos( $arg, '--render-dir=' ) === 0 ) { $render_dir = substr( $arg, 13 ); } }
if ( $render_dir ) {
	if ( ! is_dir( $render_dir ) ) { mkdir( $render_dir, 0777, true ); }
	ob_start(); require __DIR__ . '/../blocksy-child/page-glossar.php';
	file_put_contents( $render_dir . '/index.html', ob_get_clean() );
	foreach ( $GLOBALS['glossary_test_posts'] as $post ) {
		$GLOBALS['glossary_test_post'] = $post;
		$GLOBALS['glossary_test_loop_done'] = false;
		ob_start(); require __DIR__ . '/../blocksy-child/single-glossary_term.php';
		file_put_contents( $render_dir . '/' . $post->post_name . '.html', ob_get_clean() );
	}
}
echo 'PASS: ' . array_sum( $definition_counts ) . " definitions, at least twelve per area; directory, policies, examples, escaping and current related links.\n";
