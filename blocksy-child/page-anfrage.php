<?php
/**
 * Template Name: Anfrage Legacy Redirect
 * Description: Redirects the retired /anfrage/ intake to the Anfrage-System-Analyse.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$target_url = function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/anfrage-system-analyse/' );

nocache_headers();
wp_safe_redirect( $target_url, 301 );
exit;
