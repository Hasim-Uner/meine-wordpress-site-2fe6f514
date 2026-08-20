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
<style id="nexus-footer-contrast-layer">
	/*
	 * Defensive cascade layer for the modern footer. A few global/theme anchor
	 * rules are intentionally stronger than the component stylesheet and can
	 * otherwise wash out labels or hand arrow glyphs to the platform emoji font.
	 * Keep the final contrast treatment next to the rendered footer so the three
	 * route cards and the primary CTA remain deterministic on every page.
	 */

	/* Premium dark route cards on the warm footer surface. */
	.ft--modern .ft-modern__routes {
		gap: 18px;
	}

	.ft--modern .ft-modern__route,
	.ft--modern .ft-modern__route:link,
	.ft--modern .ft-modern__route:visited {
		position: relative;
		overflow: hidden;
		isolation: isolate;
		min-height: 172px;
		padding: 27px;
		column-gap: 18px;
		row-gap: 8px;
		border: 1px solid rgba(255, 250, 244, 0.09) !important;
		background:
			linear-gradient(145deg, rgba(255, 255, 255, 0.035), rgba(255, 255, 255, 0) 42%),
			#171411 !important;
		color: #fffaf4 !important;
		-webkit-text-fill-color: #fffaf4;
		opacity: 1 !important;
		box-shadow:
			0 18px 44px rgba(23, 20, 17, 0.18),
			inset 0 1px 0 rgba(255, 255, 255, 0.035);
		text-decoration: none !important;
	}

	.ft--modern .ft-modern__route::before {
		content: '';
		position: absolute;
		z-index: -1;
		inset: 0 auto 0 0;
		width: 3px;
		background: linear-gradient(180deg, transparent 8%, #b46a3c 50%, transparent 92%);
		opacity: 0.7;
	}

	.ft--modern .ft-modern__route::after {
		content: '';
		position: absolute;
		z-index: -1;
		top: -88px;
		right: -70px;
		width: 220px;
		height: 220px;
		border-radius: 50%;
		background: radial-gradient(circle, rgba(180, 106, 60, 0.16), transparent 68%);
		pointer-events: none;
	}

	.ft--modern .ft-modern__route:hover,
	.ft--modern .ft-modern__route:focus-visible {
		transform: translateY(-5px);
		border-color: rgba(215, 154, 115, 0.46) !important;
		background:
			linear-gradient(145deg, rgba(255, 255, 255, 0.055), rgba(255, 255, 255, 0) 44%),
			#1d1916 !important;
		color: #fffaf4 !important;
		-webkit-text-fill-color: #fffaf4;
		box-shadow:
			0 26px 58px rgba(23, 20, 17, 0.25),
			0 0 0 1px rgba(180, 106, 60, 0.10) inset,
			inset 0 1px 0 rgba(255, 255, 255, 0.05);
		text-decoration: none !important;
	}

	.ft--modern .ft-modern__route:focus-visible {
		outline: 3px solid rgba(180, 106, 60, 0.28);
		outline-offset: 4px;
	}

	.ft--modern .ft-modern__route-icon {
		width: 48px;
		height: 48px;
		border-color: rgba(215, 154, 115, 0.30) !important;
		background: rgba(180, 106, 60, 0.10) !important;
		color: #dfa27b !important;
		-webkit-text-fill-color: #dfa27b;
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
	}

	.ft--modern .ft-modern__route-icon svg {
		width: 22px;
		height: 22px;
		stroke: currentColor !important;
	}

	.ft--modern .ft-modern__route-label {
		font-size: 1.14rem;
		font-weight: 790;
		line-height: 1.15;
		letter-spacing: -0.015em;
		color: #fffaf4 !important;
		-webkit-text-fill-color: #fffaf4;
		text-shadow: none;
	}

	.ft--modern .ft-modern__route-copy {
		max-width: 31ch;
		font-size: 0.9rem;
		line-height: 1.55;
		font-weight: 500;
		color: #cfc6be !important;
		-webkit-text-fill-color: #cfc6be;
		opacity: 1 !important;
	}

	.ft--modern .ft-modern__route-arrow {
		display: inline-grid;
		place-items: center;
		width: 40px;
		height: 40px;
		border: 1px solid rgba(215, 154, 115, 0.34) !important;
		border-radius: 999px;
		background: rgba(180, 106, 60, 0.12) !important;
		color: #e2a47c !important;
		line-height: 1;
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.035);
	}

	/* Der Pfeil ist Inline-SVG (hu_arrow_up_right_svg). Die frueheren
	   font-size: 0 plus ::before-Glyphe in Arial waren der Versuch, die
	   Emoji-Praesentation von U+2197 zu umgehen; das SVG braucht sie nicht und
	   font-size: 0 wuerde nur noch alles ausser dem SVG verschlucken. */
	.ft--modern .ft-modern__route-arrow > svg {
		width: 17px;
		height: 17px;
	}

	.ft--modern .ft-modern__route:hover .ft-modern__route-arrow,
	.ft--modern .ft-modern__route:focus-visible .ft-modern__route-arrow {
		transform: translate(3px, -3px);
		border-color: rgba(226, 164, 124, 0.62) !important;
		background: rgba(180, 106, 60, 0.22) !important;
	}

	.ft--modern .ft-modern__route:hover .ft-modern__route-icon,
	.ft--modern .ft-modern__route:focus-visible .ft-modern__route-icon {
		border-color: rgba(226, 164, 124, 0.52) !important;
		background: rgba(180, 106, 60, 0.16) !important;
	}

	/* Primary CTA contrast. */
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
		line-height: 1;
	}

	.ft--modern .ft-modern__cta-button > span > svg {
		width: 13px;
		height: 13px;
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

	@media (max-width: 700px) {
		.ft--modern .ft-modern__route,
		.ft--modern .ft-modern__route:link,
		.ft--modern .ft-modern__route:visited {
			min-height: 138px;
			padding: 22px;
			column-gap: 15px;
		}

		.ft--modern .ft-modern__route-copy {
			max-width: none;
			font-size: 0.86rem;
		}
	}

	@media (prefers-reduced-motion: reduce) {
		.ft--modern .ft-modern__route:hover,
		.ft--modern .ft-modern__route:focus-visible,
		.ft--modern .ft-modern__route:hover .ft-modern__route-arrow,
		.ft--modern .ft-modern__route:focus-visible .ft-modern__route-arrow {
			transform: none;
		}
	}
</style>
<?php

do_action( 'blocksy:footer:after' );
?>
</div>

<?php wp_footer(); ?>
</body>
</html>
