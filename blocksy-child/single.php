<?php
/**
 * Single post entrypoint.
 *
 * Every WordPress post is rendered through one shared reader contract. Route-
 * specific articles may provide content modules, but no longer own the page
 * shell, hero, table of contents or reading layout.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/single-reader' );
