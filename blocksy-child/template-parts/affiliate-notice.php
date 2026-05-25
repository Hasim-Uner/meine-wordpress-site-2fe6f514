<?php
/**
 * Template Part: Affiliate Disclosure Notice
 *
 * Werbekennzeichnung für Partnerlinks. Wird immer VOR dem ersten Affiliate-Link
 * einer Sektion gerendert.
 *
 * Usage:
 *   set_query_var( 'affiliate_notice_provider_label', 'HostPress' );
 *   set_query_var( 'affiliate_notice_context', 'stack' ); // 'stack' | 'inline'
 *   get_template_part( 'template-parts/affiliate-notice' );
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$provider_label = get_query_var( 'affiliate_notice_provider_label', 'HostPress' );
$context        = get_query_var( 'affiliate_notice_context', 'stack' );

$context_text = ( 'inline' === $context )
	? sprintf(
		/* translators: %s: provider name */
		__( 'Werbung · Partnerlink. Die Empfehlung für %s basiert auf eigenem Einsatz im Anfrage-System-Setup. Bei einem Abschluss über diesen Link entsteht eine Vergütung — ohne Mehrkosten.', 'blocksy-child' ),
		$provider_label
	)
	: sprintf(
		/* translators: %s: provider name */
		__( 'Werbung · Partnerlink. %s ist eine eigene Stack-Empfehlung. Wenn du über den Link abschließt, entsteht eine Vergütung — der Preis für dich bleibt identisch.', 'blocksy-child' ),
		$provider_label
	);

$class = ( 'inline' === $context ) ? 'hu-affiliate-notice hu-affiliate-notice--inline' : 'hu-affiliate-notice hu-affiliate-notice--stack';
?>
<aside class="<?php echo esc_attr( $class ); ?>" role="note" aria-label="<?php esc_attr_e( 'Werbekennzeichnung', 'blocksy-child' ); ?>">
	<span class="hu-affiliate-notice__label"><?php esc_html_e( 'Werbung', 'blocksy-child' ); ?></span>
	<p class="hu-affiliate-notice__text"><?php echo esc_html( $context_text ); ?></p>
</aside>
