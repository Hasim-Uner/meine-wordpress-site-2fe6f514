<?php
/**
 * /anfrage/
 *
 * Retired intake route. The only public Solar Anfrage entry is the
 * marketcheck on /solar-waermepumpen-leadgenerierung/.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$target_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

nocache_headers();
wp_safe_redirect( $target_url, 301 );
exit;
