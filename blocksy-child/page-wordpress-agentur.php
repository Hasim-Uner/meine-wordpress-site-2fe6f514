<?php
/**
 * Template Name: WordPress Agentur Hannover
 * Description: Compatibility dispatcher for the legacy assigned page template.
 *
 * The WordPress page /wordpress-agentur-hannover/ still has this historical
 * template filename stored in wp-admin. WordPress therefore resolves this
 * custom template before page-wordpress-agentur-hannover.php. Keep the stored
 * assignment compatible, but route the Hannover slug to the new decision page.
 * Any other page that still uses this template continues to render the preserved
 * legacy implementation.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_page( 'wordpress-agentur-hannover' ) ) {
	require __DIR__ . '/page-wordpress-agentur-hannover.php';
	return;
}

require __DIR__ . '/page-wordpress-agentur-legacy.php';
