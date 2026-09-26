<?php
/**
 * Contract test for page provisioning in blocksy-child/inc/helpers.php:
 * once per deployed revision, legacy slugs renamed before inserting,
 * templates written only when they differ. Loads the real functions.
 */

class WP_Post { public function __construct( array $data ) { foreach ( $data as $key => $value ) { $this->$key = $value; } } }
class WP_Error {}

function reset_site( array $pages = [], array $options = [], string $sha = 'abc1234' ) {
	$GLOBALS['site'] = [ 'pages' => $pages, 'meta' => [], 'options' => $options, 'sha' => $sha, 'cron' => false, 'log' => [] ];
}
function log_call( $call ) { $GLOBALS['site']['log'][] = $call; }
function wp_installing() { return false; }
function wp_doing_ajax() { return false; }
function wp_doing_cron() { return $GLOBALS['site']['cron']; }
function get_option( $name ) { return $GLOBALS['site']['options'][ $name ] ?? false; }
function update_option( $name, $value ) { $GLOBALS['site']['options'][ $name ] = $value; return true; }
function trailingslashit( $value ) { return rtrim( $value, '/\\' ) . '/'; }
function get_stylesheet_directory() {
	$dir = sys_get_temp_dir() . '/provisioning-test';
	is_dir( $dir ) || mkdir( $dir );
	file_put_contents( $dir . '/.nexus-deploy-sha', $GLOBALS['site']['sha'] );
	return $dir;
}
function wp_get_theme() { return new class { public function get( $key ) { return '1.0'; } }; }
function wp_slash( $value ) { return $value; }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function get_page_by_path( $path ) {
	log_call( "lookup:$path" );
	$id = array_search( $path, $GLOBALS['site']['pages'], true );
	return false === $id ? null : new WP_Post( [ 'ID' => $id ] );
}
function wp_update_post( $post ) { log_call( "rename:{$post['post_name']}" ); $GLOBALS['site']['pages'][ $post['ID'] ] = $post['post_name']; return $post['ID']; }
function wp_insert_post( $post ) { log_call( "insert:{$post['post_name']}" ); $id = count( $GLOBALS['site']['pages'] ) + 100; $GLOBALS['site']['pages'][ $id ] = $post['post_name']; return $id; }
function get_post_meta( $id, $key ) { return $GLOBALS['site']['meta'][ $id ][ $key ] ?? ''; }
function update_post_meta( $id, $key, $value ) { log_call( "template:$value" ); $GLOBALS['site']['meta'][ $id ][ $key ] = $value; }

$source = file_get_contents( __DIR__ . '/../../blocksy-child/inc/helpers.php' );
foreach ( [ 'nexus_get_page_id', 'nexus_get_deploy_marker_sha', 'nexus_get_route_pages_stamp', 'nexus_route_pages_ensure_due',
	'nexus_mark_route_pages_ensured', 'nexus_get_provisioned_pages', 'nexus_ensure_provisioned_page', 'nexus_maybe_ensure_provisioned_pages' ] as $name ) {
	if ( ! preg_match( '/^function ' . $name . '\(.*?^}$/ms', $source, $match ) ) {
		throw new RuntimeException( "missing $name() in helpers.php" );
	}
	// The due flag is cached per request; strip the static so each case is a fresh request.
	eval( str_replace( "static \$due = null;\n\n\tif ( null === \$due ) {", "\$due = null;\n\n\tif ( null === \$due ) {", $match[0] ) );
}

function request() {
	nexus_maybe_ensure_provisioned_pages();
	nexus_mark_route_pages_ensured();
	return $GLOBALS['site']['log'];
}
function check( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } echo "PASS $message\n"; }
function count_prefix( array $log, $prefix ) { return count( array_filter( $log, fn( $c ) => str_starts_with( $c, $prefix ) ) ); }

$pages = count( nexus_get_provisioned_pages() );

reset_site();
$log = request();
check( count_prefix( $log, 'insert:' ) === $pages && count_prefix( $log, 'template:' ) === $pages, "fresh site: all $pages pages inserted with template" );
check( 'abc1234' === get_option( 'nexus_route_pages_stamp' ), 'stamp stored after provisioning' );

$GLOBALS['site']['log'] = [];
check( [] === request(), 'second request with same deploy: no lookups, no writes' );

reset_site( [ 7 => 'uber-mich' ] );
$log = request();
check( in_array( 'rename:hasim-uener', $log, true ) && ! in_array( 'insert:hasim-uener', $log, true ), 'legacy slug renamed instead of inserted' );

reset_site( [ 9 => 'waermepumpen-leads' ] );
$GLOBALS['site']['meta'][9]['_wp_page_template'] = 'page-waermepumpen-leads.php';
$log = request();
check( ! in_array( 'insert:waermepumpen-leads', $log, true ) && count_prefix( $log, 'template:' ) === $pages - 1, 'existing page with correct template left untouched' );

reset_site();
$GLOBALS['site']['cron'] = true;
check( [] === request() && false === get_option( 'nexus_route_pages_stamp' ), 'cron request neither provisions nor consumes the stamp' );

reset_site( [], [ 'nexus_route_pages_stamp' => 'abc1234' ], 'def5678' );
check( count_prefix( request(), 'lookup:' ) > 0 && 'def5678' === get_option( 'nexus_route_pages_stamp' ), 'new deploy provisions again' );

echo "OK page-provisioning\n";
