<?php
/** Standalone destructive-migration contract: scoped deletion, retries and routing. */
define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );
set_error_handler( static function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );
class WP_Post {
	public function __construct( public $ID, public $post_type, public $post_name, public $post_content = '', public $post_parent = 0, public $post_status = 'publish' ) {}
}
class RetirementUser {
	public $caps = [ 'read' => true, 'view_wgos_dashboard' => true ];
	public function __construct( public $roles ) {}
	public function remove_role( $role ) { $this->roles = array_values( array_diff( $this->roles, [ $role ] ) ); }
	public function add_role( $role ) { $this->roles[] = $role; }
	public function remove_cap( $cap ) { unset( $this->caps[ $cap ] ); }
}
function add_action( ...$args ) {}
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function wp_parse_url( $url, $component ) { return parse_url( $url, $component ); }
function trailingslashit( $path ) { return rtrim( $path, '/' ) . '/'; }
function esc_url( $url ) { return htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' ); }
function nexus_get_glossary_registry_version() { return 'new'; }
function nexus_get_glossary_last_assert_status() { return 'pass'; }
function get_option( $key, $default = false ) { return $GLOBALS['options'][ $key ] ?? $default; }
function update_option( $key, $value, $autoload = null ) { $GLOBALS['options'][ $key ] = $value; }
function delete_option( $key ) { unset( $GLOBALS['options'][ $key ] ); }
function get_transient( $key ) { return $GLOBALS['transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl ) { $GLOBALS['transients'][ $key ] = $value; }
function delete_transient( $key ) { unset( $GLOBALS['transients'][ $key ] ); }
function get_post_stati() { return [ 'publish', 'draft', 'private', 'trash' ]; }
function get_posts( $args ) {
	return array_values( array_filter( $GLOBALS['posts'], static function ( $post ) use ( $args ) {
		return in_array( $post->post_type, (array) $args['post_type'], true ) && in_array( $post->post_status, (array) $args['post_status'], true );
	} ) );
}
function get_post( $id ) { return $GLOBALS['posts'][ $id ] ?? null; }
function wp_delete_post( $id, $force = false ) {
	retirement_check( $force, 'Deletion must use the authorized permanent retirement path.' );
	if ( $id === ( $GLOBALS['fail_delete'] ?? 0 ) ) { return false; }
	$post = get_post( $id );
	unset( $GLOBALS['posts'][ $id ] );
	$GLOBALS['deleted'][] = $id;
	return $post;
}
function wp_update_post( $args, $error = false ) {
	$GLOBALS['posts'][ $args['ID'] ]->post_content = $args['post_content'];
	return $args['ID'];
}
function wp_slash( $args ) { return $args; }
function is_wp_error( $value ) { return false; }
function maybe_unserialize( $value ) { $result = @unserialize( $value ); return false === $result ? $value : $result; }
function maybe_serialize( $value ) { return serialize( $value ); }
function get_page_uri( $post ) { return $post->post_name; }
function get_page_template_slug( $id ) { return $GLOBALS['meta'][ $id ]['_wp_page_template'] ?? ''; }
function get_post_meta( $id, $key, $single ) { return $GLOBALS['meta'][ $id ][ $key ] ?? ''; }
function get_permalink( $id ) { $post = get_post( $id ); return $post ? home_url( '/' . $post->post_name . '/' ) : ''; }
function get_users( $args = [] ) {
	return array_filter( $GLOBALS['users'], static fn ( $user ) => ! isset( $args['role'] ) || in_array( $args['role'], $user->roles, true ) );
}
function get_role( $name ) { return new class { public function remove_cap( $cap ) {} }; }
function wp_roles() { return (object) [ 'roles' => [ 'administrator' => [], 'wgos_client' => [] ] ]; }
function remove_role( $name ) { $GLOBALS['role_removed'] = $name; }
function post_type_exists( $name ) { return true; }
function unregister_post_type( $name ) { $GLOBALS['type_removed'] = $name; }
function flush_rewrite_rules( $hard ) { retirement_check( false === $hard, 'No rewrite-file writes.' ); $GLOBALS['flushed'] = true; }
function do_action( ...$args ) {}
function retirement_check( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }

require __DIR__ . '/../../blocksy-child/inc/legacy-system-retirement.php';
retirement_check( '/glossar/positionierung/' === hu_get_retired_system_target( '/wgos-assets/positionierungs-check/?x=1' ), 'Positionierungs-Check becomes a real definition.' );
retirement_check( '/glossar/utm-parameter/' === hu_get_retired_system_target( home_url( '/wgos-assets/utm-framework/' ) ), 'Reuse existing UTM definition.' );
retirement_check( '' === hu_get_retired_system_target( '/wgos-assets/monthly-review/' ), 'Do not turn reviews into definitions.' );
retirement_check( '' === hu_get_retired_system_target( '/wgos-assets/llm-workflow-automatisierung/' ), 'Retired workflow stays gone.' );
retirement_check( null === hu_get_retired_system_target( 'https://other.test/wgos-assets/positionierungs-check/' ), 'Do not rewrite external links.' );
retirement_check( null === hu_get_retired_system_target( '/ga4-tracking-setup/' ), 'Tracking route is preserved.' );
$legacy = [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'wgos_asset' ] ];
$normal = [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ] ];
retirement_check( [ $normal ] === hu_remove_retired_field_locations( [ $legacy, $normal ] ), 'Keep shared ACF location branches.' );
retirement_check( [] === hu_remove_retired_field_locations( [ $legacy ] ), 'Identify exclusive ACF groups.' );
retirement_check( [] === hu_remove_retired_field_locations( [ [ [ 'param' => 'page', 'operator' => '==', 'value' => '3' ] ] ], [ 3 ] ), 'Remove ACF groups bound directly to a retired page ID.' );
$negative = [ [ 'param' => 'post_type', 'operator' => '!=', 'value' => 'wgos_asset' ] ];
retirement_check( [ [ [ 'param' => 'post_type', 'operator' => '!=', 'value' => 'revision' ] ] ] === hu_remove_retired_field_locations( [ $negative ] ), 'Keep formerly global ACF groups.' );
$html = '<p>[hu_wgos_block]<a href="/wgos-assets/utm-framework/">UTM</a> <a href="https://other.test/wgos-assets/a/">External</a></p>';
$clean = hu_clean_retired_system_content( $html );
retirement_check( ! str_contains( $clean, '[hu_wgos' ) && str_contains( $clean, '/glossar/utm-parameter/' ) && str_contains( $clean, 'https://other.test/wgos-assets/a/' ), 'Clean only legacy placements and local links.' );
retirement_check( $clean === hu_clean_retired_system_content( $clean ), 'Content cleanup is idempotent.' );

$GLOBALS['options'] = [];
$GLOBALS['transients'] = [];
$GLOBALS['posts'] = [
	1 => new WP_Post( 1, 'wgos_asset', 'positionierungs-check' ),
	2 => new WP_Post( 2, 'wgos_asset', 'old-workflow', '', 0, 'trash' ),
	3 => new WP_Post( 3, 'page', 'wgos' ),
	4 => new WP_Post( 4, 'page', 'ga4-tracking-setup' ),
	5 => new WP_Post( 5, 'page', 'performance-marketing' ),
	6 => new WP_Post( 6, 'glossary_term', 'positionierung' ),
	7 => new WP_Post( 7, 'post', 'existing-article', $html ),
	10 => new WP_Post( 10, 'acf-field-group', 'custom-import', serialize( [ 'location' => [ $legacy ] ] ) ),
	11 => new WP_Post( 11, 'acf-field-group', 'shared-seo', serialize( [ 'location' => [ $legacy, $normal ] ] ) ),
	12 => new WP_Post( 12, 'acf-field', 'old-field', '', 10 ),
	13 => new WP_Post( 13, 'acf-field', 'nested-field', '', 12 ),
	14 => new WP_Post( 14, 'acf-field', 'shared-seo-field', '', 11 ),
	15 => new WP_Post( 15, 'acf-field', 'unrelated-orphan', '', 999 ),
	16 => new WP_Post( 16, 'acf-post-type', 'imported-cpt', serialize( [ 'post_type' => 'wgos_asset' ] ) ),
	20 => new WP_Post( 20, 'nav_menu_item', 'legacy-menu' ),
	21 => new WP_Post( 21, 'nav_menu_item', 'existing-menu' ),
];
$GLOBALS['meta'] = [ 20 => [ '_menu_item_type' => 'custom', '_menu_item_url' => '/wgos-assets/' ], 21 => [ '_menu_item_type' => 'post_type', '_menu_item_object' => 'page', '_menu_item_object_id' => 999 ] ];
$GLOBALS['users'] = [ new RetirementUser( [ 'wgos_client' ] ), new RetirementUser( [ 'administrator', 'wgos_client' ] ) ];
hu_maybe_retire_legacy_system();
retirement_check( count( $GLOBALS['posts'] ) === 16, 'Never retire before successful glossary sync.' );
$GLOBALS['options']['nexus_glossary_sync_version'] = 'new';
$GLOBALS['fail_delete'] = 13;
hu_maybe_retire_legacy_system();
retirement_check( ! get_option( 'hu_legacy_system_retirement_version' ), 'A failed deletion must not mark retirement complete.' );
retirement_check( isset( $GLOBALS['posts'][13] ), 'Failed child deletion is available for retry.' );
$GLOBALS['fail_delete'] = 0;
hu_maybe_retire_legacy_system();
foreach ( [ 1, 2, 3, 10, 12, 13, 16, 20 ] as $id ) { retirement_check( ! isset( $GLOBALS['posts'][ $id ] ), 'Retired object remains: ' . $id ); }
foreach ( [ 4, 5, 6, 7, 11, 14, 15, 21 ] as $id ) { retirement_check( isset( $GLOBALS['posts'][ $id ] ), 'Unrelated object was removed: ' . $id ); }
retirement_check( [ $normal ] === unserialize( $GLOBALS['posts'][11]->post_content )['location'], 'Shared imported group must keep its page rules.' );
retirement_check( $GLOBALS['posts'][7]->post_content === $clean, 'Stored article links must migrate.' );
retirement_check( [ 'subscriber' ] === $GLOBALS['users'][0]->roles && [ 'administrator' ] === $GLOBALS['users'][1]->roles, 'Keep accounts and their non-retired access.' );
retirement_check( ! isset( $GLOBALS['users'][1]->caps['view_wgos_dashboard'] ), 'Remove direct dashboard capabilities.' );
retirement_check( get_option( 'hu_legacy_system_retirement_version' ) === HU_LEGACY_SYSTEM_RETIREMENT_VERSION, 'Record success only at completion.' );
$deleted = $GLOBALS['deleted'];
hu_maybe_retire_legacy_system();
retirement_check( $deleted === $GLOBALS['deleted'], 'A completed retirement must not run twice.' );
echo "PASS: retirement scope, glossary prerequisite, stored links, ACF/SCF branches, nested-field retries, roles and idempotency.\n";
