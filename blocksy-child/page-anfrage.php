<?php
/**
 * /anfrage/
 *
 * Retired intake route. The only public Anfrage entry is
 * /system-diagnose/.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$target_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/system-diagnose/' );

nocache_headers();
wp_safe_redirect( $target_url, 301 );
exit;
