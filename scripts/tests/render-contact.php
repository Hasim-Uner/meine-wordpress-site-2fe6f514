<?php
/** Render the actual contact template for controller tests without booting WP. */
require __DIR__ . '/intake-wordpress-doubles.php';
require __DIR__ . '/../../blocksy-child/inc/canon/messaging-canon.php';
require __DIR__ . '/../../blocksy-child/inc/canon/pricing-canon.php';
require __DIR__ . '/../../blocksy-child/inc/contact-page.php';
require __DIR__ . '/../../blocksy-child/inc/crm.php';
function get_header() {}
function get_footer() {}
function rest_url( $path ) { return home_url( '/wp-json/' . $path ); }
function selected( $left, $right ) { if ( (string) $left === (string) $right ) echo 'selected'; }
$_GET = ( $argv[1] ?? '' ) === 'assessment' ? [ 'focus' => 'ersteinschaetzung' ] : [ 'type' => 'project', 'focus' => 'tracking' ];
if ( ( $argv[1] ?? '' ) === 'project' ) unset( $_GET['focus'] );
if ( ( $argv[1] ?? '' ) === 'website' ) {
    parse_str( $argv[2] ?? 'type=project&focus=website&seiten=5&art=relaunch&tracking=1', $_GET );
}
require __DIR__ . '/../../blocksy-child/page-kontakt.php';
