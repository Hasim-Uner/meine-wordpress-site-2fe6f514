<?php
/**
 * Sticky-CTA-Bar fuer SEO-Sub-Pages (mobil-only, barrierefrei).
 *
 * Args (passed via get_template_part(..., $args)):
 * - marktcheck_url:  Ziel-URL fuer den primaeren CTA
 * - track_category:  Tracking-Kategorie (z. B. "intercept_solar_leads")
 *
 * Barrierefreiheit:
 * - role="region" + aria-label fuer Screenreader-Orientierung
 * - Schließen-Button mit aria-label und Tastatur-Erreichbarkeit
 * - JS respektiert prefers-reduced-motion und localStorage-Dismiss
 * - kein Fokus-Trap, Tab-Reihenfolge bleibt natuerlich
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hu_sticky_args      = isset( $args ) && is_array( $args ) ? $args : [];
$hu_sticky_target    = isset( $hu_sticky_args['marktcheck_url'] ) ? (string) $hu_sticky_args['marktcheck_url'] : '';
$hu_sticky_category  = isset( $hu_sticky_args['track_category'] ) ? (string) $hu_sticky_args['track_category'] : 'seo_subpage_sticky';

if ( '' === $hu_sticky_target ) {
	$hu_sticky_target = function_exists( 'hu_get_request_analysis_url' )
		? hu_get_request_analysis_url()
		: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
}
?>

<aside class="hu-sticky-cta"
       id="hu-sticky-cta"
       role="region"
       aria-label="Marktcheck-Schnellzugang"
       hidden>
	<div class="hu-sticky-cta__inner">
		<p class="hu-sticky-cta__text">
			<span class="hu-sticky-cta__lead">Manueller Marktcheck</span>
			<span class="hu-sticky-cta__sub">Händische Analyse · Befund innerhalb von 48 Stunden</span>
		</p>
		<a class="hu-sticky-cta__primary"
		   href="<?php echo esc_url( $hu_sticky_target ); ?>"
		   data-track-action="cta_sticky_marktcheck"
		   data-track-category="<?php echo esc_attr( $hu_sticky_category ); ?>"
		   data-track-section="sticky_bar">
			Eigene Region jetzt prüfen
			<span class="hu-sticky-cta__primary-icon" aria-hidden="true">→</span>
		</a>
		<button class="hu-sticky-cta__close"
		        type="button"
		        aria-label="Schnellzugang ausblenden"
		        data-track-action="sticky_cta_dismiss"
		        data-track-category="<?php echo esc_attr( $hu_sticky_category ); ?>"
		        data-track-section="sticky_bar">
			<span aria-hidden="true">×</span>
		</button>
	</div>
</aside>
