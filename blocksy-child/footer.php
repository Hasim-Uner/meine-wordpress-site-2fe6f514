<?php
/**
 * Footer template override.
 *
 * Replaces the Blocksy footer-builder output with the custom CRO footer
 * so the WordPress footer widget area can stay empty.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'blocksy_after_current_template' ) ) {
	blocksy_after_current_template();
}

do_action( 'blocksy:content:bottom' );
?>
	</main>
<?php
do_action( 'blocksy:content:after' );
do_action( 'blocksy:footer:before' );

get_template_part( 'template-parts/site-footer' );
?>
<style id="nexus-footer-cta-contrast">
	/*
	 * Defensive cascade layer for the light footer CTA. Some global/theme link
	 * rules can otherwise wash out the anchor text and hand the arrow to the
	 * platform emoji font. Keep this small override next to the rendered footer
	 * so the call-to-action stays legible independent of page-specific CSS.
	 */
	.ft--modern .ft-modern__cta-button,
	.ft--modern .ft-modern__cta-button:link,
	.ft--modern .ft-modern__cta-button:visited {
		background: #fffaf4 !important;
		color: #11100f !important;
		-webkit-text-fill-color: #11100f;
		border-color: rgba(215, 154, 115, 0.78) !important;
		opacity: 1 !important;
		box-shadow: 0 12px 30px rgba(0, 0, 0, 0.20);
	}

	.ft--modern .ft-modern__cta-button > span {
		display: inline-grid;
		place-items: center;
		width: 24px;
		height: 24px;
		flex: 0 0 24px;
		border-radius: 999px;
		background: #b46a3c;
		color: #fffaf4 !important;
		-webkit-text-fill-color: #fffaf4;
		font-size: 0;
		line-height: 1;
	}

	.ft--modern .ft-modern__cta-button > span::before {
		content: '↗';
		font-family: Arial, sans-serif;
		font-size: 13px;
		font-weight: 700;
		line-height: 1;
	}

	.ft--modern .ft-modern__cta-button:hover,
	.ft--modern .ft-modern__cta-button:focus-visible {
		background: #ffffff !important;
		color: #11100f !important;
		-webkit-text-fill-color: #11100f;
		border-color: #b46a3c !important;
	}

	.ft--modern .ft-modern__cta-button:hover > span,
	.ft--modern .ft-modern__cta-button:focus-visible > span {
		background: #7f4023;
	}
</style>
<?php

do_action( 'blocksy:footer:after' );
?>
</div>

<?php wp_footer(); ?>
</body>
</html>
