<?php
/**
 * Template Part: Footer CTA
 *
 * Wiederverwendbarer Bottom-CTA-Block für Service- und Blog-Seiten.
 * Tracking-ready mit data-track-* Attributen.
 *
 * Usage:
 *   set_query_var( 'cta_heading', 'Passt Ihr Betrieb für ein eigenes Anfrage-System?' );
 *   set_query_var( 'cta_text', 'Die Analyse prüft Fit, Marktbild und nächsten Schritt.' );
 *   set_query_var( 'cta_url', '/solar-waermepumpen-leadgenerierung/#marktcheck' );
 *   set_query_var( 'cta_button_text', 'Marktcheck starten' );
 *   set_query_var( 'cta_action', 'cta_footer_analysis' );
 *   get_template_part( 'template-parts/footer-cta' );
 *
 * [CRO] template-parts/footer-cta: Conversion-optimierter Bottom-CTA
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading     = get_query_var( 'cta_heading', __( 'Passt Ihr Betrieb für ein eigenes Anfrage-System?', 'blocksy-child' ) );
$text        = get_query_var( 'cta_text', __( 'Die Analyse prüft Fit, Marktbild und den nächsten sinnvollen Schritt.', 'blocksy-child' ) );
$url         = get_query_var( 'cta_url', function_exists( 'hu_get_request_analysis_url' ) ? hu_get_request_analysis_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ) );
$button_text = get_query_var( 'cta_button_text', __( 'Marktcheck starten', 'blocksy-child' ) );
$action      = get_query_var( 'cta_action', 'cta_footer_analysis' );
$imprint_url = nexus_get_page_url( [ 'impressum' ], home_url( '/impressum/' ) );
$privacy_url = nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) );

set_query_var( 'nexus_hide_footer_primary_cta', true );
?>

<section class="nexus-footer-cta" data-track-section="footer_cta">
	<div class="nexus-footer-cta__inner">
		<h3 class="nexus-footer-cta__heading"><?php echo esc_html( $heading ); ?></h3>
		<p class="nexus-footer-cta__text"><?php echo esc_html( $text ); ?></p>

		<a href="<?php echo esc_url( $url ); ?>"
		   class="btn btn-primary nexus-footer-cta__btn"
		   data-track-action="<?php echo esc_attr( $action ); ?>"
		   data-track-category="lead_gen">
			<?php echo esc_html( $button_text ); ?>
		</a>

		<p class="nexus-footer-cta__legal">
			Keine Cookies bei öffentlichen Seitenaufrufen.
			<a href="<?php echo esc_url( $privacy_url ); ?>" rel="nofollow">Datenschutz</a>
			<span aria-hidden="true">·</span>
			<a href="<?php echo esc_url( $imprint_url ); ?>" rel="nofollow">Impressum</a>
		</p>

		<?php get_template_part( 'template-parts/trust-section' ); ?>
	</div>
</section>
