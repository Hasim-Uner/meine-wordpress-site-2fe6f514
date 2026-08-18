<?php
/**
 * Template Part: Footer CTA
 *
 * Wiederverwendbarer Bottom-CTA-Block für Service- und Blog-Seiten.
 * Tracking-ready mit data-track-* Attributen.
 *
 * Default = generische Projektanfrage. Spezialisierte Funnels müssen ihren
 * Zielpfad und ihre Copy explizit übergeben. So kann der Solar-Marktcheck nicht
 * versehentlich auf fachfremden WordPress-/Tracking-/CRO-Seiten erscheinen.
 *
 * Energy-Beispiel:
 *   set_query_var( 'cta_heading', 'Passt Ihr Betrieb für ein eigenes Anfragesystem?' );
 *   set_query_var( 'cta_text', 'Der Marktcheck prüft Fit, Marktbild und nächsten Schritt.' );
 *   set_query_var( 'cta_url', '/solar-waermepumpen-leadgenerierung/#marktcheck' );
 *   set_query_var( 'cta_button_text', 'Marktcheck mit Fit-Entscheid starten' );
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

$project_url = function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: add_query_arg(
		[
			'type'  => 'project',
			'focus' => 'followup_scope',
		],
		home_url( '/kontakt/' )
	);

$heading     = get_query_var( 'cta_heading', __( 'Sie haben ein konkretes Projekt?', 'blocksy-child' ) );
$text        = get_query_var( 'cta_text', __( 'Schicken Sie mir kurz Ziel, Ausgangslage und Scope. Ich sage Ihnen, ob und wie ich sinnvoll unterstützen kann.', 'blocksy-child' ) );
$url         = get_query_var( 'cta_url', $project_url );
$button_text = get_query_var( 'cta_button_text', __( 'Projekt anfragen', 'blocksy-child' ) );
$action      = get_query_var( 'cta_action', 'cta_footer_project' );
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
			<a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>
			<span aria-hidden="true">·</span>
			<a href="<?php echo esc_url( $imprint_url ); ?>">Impressum</a>
		</p>

		<?php get_template_part( 'template-parts/trust-section' ); ?>
	</div>
</section>
