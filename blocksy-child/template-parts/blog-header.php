<?php
/**
 * Blog header compatibility router.
 *
 * Single posts, tag/date archives and category archives enter through this
 * template because inc/header.php intentionally excludes the blog area from
 * its automatic renderer. Alle erhalten dieselbe Leiste wie die ganze Website;
 * hu_funnel_context() (inc/funnel-doors.php) entscheidet den Modus: Einzel-
 * beitraege im Lesemodus (Artikelpfad plus Tuer), Archive im Modus voll.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/site-header' );
