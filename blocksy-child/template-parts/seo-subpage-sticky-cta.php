<?php
/**
 * Sticky-CTA-Bar fuer SEO-Sub-Pages (mobil-only, barrierefrei).
 *
 * Args (passed via get_template_part(..., $args)):
 * - cta_url:         Ziel-URL fuer den primaeren CTA
 * - marktcheck_url:  Legacy-Alias fuer cta_url (greift, wenn cta_url fehlt)
 * - track_category:  Tracking-Kategorie (z. B. "intercept_solar_leads")
 *
 * Optionale Copy-Overrides. Ohne sie bleibt die Marktcheck-Variante aktiv,
 * die alle Solar-Sub-Pages nutzen. Seiten mit anderem Ziel-Funnel (z. B.
 * Projektpruefung statt Marktcheck) setzen sie, damit Label und Ziel
 * zusammenpassen:
 * - region_label, lead, sub, label, track_action
 * - hide_when_visible: CSS-Selektor eines Zielbereichs, in dem die Bar pausiert
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
$hu_sticky_target    = isset( $hu_sticky_args['cta_url'] ) ? (string) $hu_sticky_args['cta_url'] : '';
$hu_sticky_category  = isset( $hu_sticky_args['track_category'] ) ? (string) $hu_sticky_args['track_category'] : 'seo_subpage_sticky';
$hu_sticky_region    = isset( $hu_sticky_args['region_label'] ) ? (string) $hu_sticky_args['region_label'] : 'Marktcheck-Schnellzugang';
$hu_sticky_lead      = isset( $hu_sticky_args['lead'] ) ? (string) $hu_sticky_args['lead'] : 'Manueller Marktcheck';
$hu_sticky_sub       = isset( $hu_sticky_args['sub'] ) ? (string) $hu_sticky_args['sub'] : 'Fit-Befund statt Standardbericht';
$hu_sticky_label     = isset( $hu_sticky_args['label'] ) ? (string) $hu_sticky_args['label'] : 'Eigene Region jetzt prüfen';
$hu_sticky_action    = isset( $hu_sticky_args['track_action'] ) ? (string) $hu_sticky_args['track_action'] : 'cta_sticky_marktcheck';
$hu_sticky_hide_when = isset( $hu_sticky_args['hide_when_visible'] ) ? trim( (string) $hu_sticky_args['hide_when_visible'] ) : '';

if ( '' === $hu_sticky_target && isset( $hu_sticky_args['marktcheck_url'] ) ) {
	$hu_sticky_target = (string) $hu_sticky_args['marktcheck_url'];
}

if ( '' === $hu_sticky_target ) {
	$hu_sticky_target = function_exists( 'hu_get_request_analysis_url' )
		? hu_get_request_analysis_url()
		: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
}
?>

<aside
	class="hu-sticky-cta"
	id="hu-sticky-cta"
	role="region"
	aria-label="<?php echo esc_attr( $hu_sticky_region ); ?>"
	<?php if ( '' !== $hu_sticky_hide_when ) : ?>
		data-sticky-hide-when-visible="<?php echo esc_attr( $hu_sticky_hide_when ); ?>"
	<?php endif; ?>
	hidden
>
	<div class="hu-sticky-cta__inner">
		<p class="hu-sticky-cta__text">
			<span class="hu-sticky-cta__lead"><?php echo esc_html( $hu_sticky_lead ); ?></span>
			<span class="hu-sticky-cta__sub"><?php echo esc_html( $hu_sticky_sub ); ?></span>
		</p>
		<a class="hu-sticky-cta__primary"
		   href="<?php echo esc_url( $hu_sticky_target ); ?>"
		   data-track-action="<?php echo esc_attr( $hu_sticky_action ); ?>"
		   data-track-category="<?php echo esc_attr( $hu_sticky_category ); ?>"
		   data-track-section="sticky_bar">
			<?php echo esc_html( $hu_sticky_label ); ?>
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
