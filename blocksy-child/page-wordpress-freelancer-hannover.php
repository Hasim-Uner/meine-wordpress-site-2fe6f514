<?php
/**
 * Template Name: WordPress Freelancer (auf Startseite zusammengeführt)
 * Template Post Type: page
 *
 * Legacy assignments are redirected before output by inc/helpers.php.
 * If assigned to the static front page, render its canonical template instead.
 *
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require get_stylesheet_directory() . '/front-page.php';
