<?php
/**
 * Template Name: Ergebnisse Hub
 * Description: Dokumentierter Projektfall, WordPress-Arbeiten und technische Belege.
 *
 * Proof order: measured outcome -> public work -> technical verification -> close.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$results_parts = get_stylesheet_directory() . '/template-parts/results/';
require $results_parts . 'data.php';
require $results_parts . 'wayfinding.php';

// Cache-sichere Kontextpriorisierung am Abschluss: liest nur URL/Referrer im
// Browser, speichert nichts und sendet keine Analysedaten.
if ( function_exists( 'hu_enqueue_js' ) ) {
	hu_enqueue_js( 'nexus-results-routing-js', 'results-routing.js', [ 'nexus-core-js' ] );
}

get_header();
?>
<div id="results-content" class="doku hu-erg" data-track-page="results_hub">
	<?php
	require $results_parts . 'hero.php';
	require $results_parts . 'case.php';
	require $results_parts . 'works.php';
	require $results_parts . 'technical.php';
	require $results_parts . 'next.php';
	?>
</div>
<?php get_footer(); ?>
