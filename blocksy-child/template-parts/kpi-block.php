<?php
/**
 * Template Part: KPI Block
 *
 * Isolierter KPI-Anzeige-Block für Metriken als visuelle Anker.
 * Kann per get_template_part() eingefügt werden.
 *
 * ACF-Felder (optional, falls registriert):
 *   - kpi_value   (Text: z.B. "-85,3%", "0.8s", "98")
 *   - kpi_label   (Text: z.B. "CPL-Reduktion")
 *   - kpi_context (Text: z.B. "via Server-Side Tracking")
 *
 * Alternativ: Variablen über set_query_var() übergeben:
 *   set_query_var( 'kpi_value', '-85,3%' );
 *   set_query_var( 'kpi_label', 'CPL-Reduktion' );
 *   set_query_var( 'kpi_context', 'via Server-Side Tracking' );
 *   get_template_part( 'template-parts/kpi-block' );
 *
 * [CRO] template-parts/kpi-block: Metriken als visuelle Anker
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Priority: passed variables → ACF fields → defaults
$kpi_value   = get_query_var( 'kpi_value', '' );
$kpi_label   = get_query_var( 'kpi_label', '' );
$kpi_context = get_query_var( 'kpi_context', '' );

if ( empty( $kpi_value ) && function_exists( 'get_field' ) ) {
	$kpi_value   = get_field( 'kpi_value' ) ?: '';
	$kpi_label   = get_field( 'kpi_label' ) ?: '';
	$kpi_context = get_field( 'kpi_context' ) ?: '';
}

if ( empty( $kpi_value ) ) {
	return;
}
?>

<div class="kpi-block highlight-metric" data-track-section="kpi_block">
	<span class="kpi-block__value"><?php echo esc_html( $kpi_value ); ?></span>
	<?php if ( $kpi_label ) : ?>
		<span class="kpi-block__label"><?php echo esc_html( $kpi_label ); ?></span>
	<?php endif; ?>
	<?php if ( $kpi_context ) : ?>
		<span class="kpi-block__context"><?php echo esc_html( $kpi_context ); ?></span>
	<?php endif; ?>
</div>
