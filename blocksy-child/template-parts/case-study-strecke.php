<?php
/**
 * Anfragestrecke im Messgeraet der Fallstudie (senkrecht, mit Rueckkopplung).
 *
 * Reines Markup. Farben aus e3-case-v2.css (Geraet-Tokens aus system.css).
 * case-study-motion.js schaltet Stationen (.is-an), zeichnet die Schleife und
 * fuehrt den Signalpunkt. Ruhezustand ohne JS: alle Stationen an, Schleife
 * gezeichnet, Punkt an Station 01.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stations = [
	[ 1, '01 NACHFRAGE', 'Google · Meta · Suche', '' ],
	[ 2, '02 EINSTIEG', 'Landingpage je Produkt', '' ],
	[ 3, '03 FILTER', 'Formular mit Vorqualifizierung', '' ],
	[ 4, '04 ÜBERGABE', 'CRM · Bitrix24', '' ],
	[ 5, '05 VERTRIEB · BETRIEB', 'Rückruf und Angebot', ' is-betrieb' ],
];
?>
<svg class="fs-svg" viewBox="0 0 320 360" preserveAspectRatio="xMidYMin meet" aria-hidden="true" focusable="false">
	<?php foreach ( $stations as $i => $station ) : ?>
		<?php $y = 6 + $i * 74; ?>
		<g class="fs-st is-an<?php echo esc_attr( $station[3] ); ?>" data-st="<?php echo esc_attr( (string) $station[0] ); ?>">
			<rect x="10" y="<?php echo esc_attr( (string) $y ); ?>" width="236" height="50" rx="3"/>
			<text class="fs-st__nr" x="24" y="<?php echo esc_attr( (string) ( $y + 18 ) ); ?>"><?php echo esc_html( $station[1] ); ?></text>
			<text x="24" y="<?php echo esc_attr( (string) ( $y + 38 ) ); ?>"><?php echo esc_html( $station[2] ); ?></text>
		</g>
		<?php if ( $i < 4 ) : ?>
			<path class="fs-fl is-an" data-fl="<?php echo esc_attr( (string) $station[0] ); ?>" d="M128 <?php echo esc_attr( (string) ( $y + 50 ) ); ?>V<?php echo esc_attr( (string) ( $y + 74 ) ); ?>"/>
		<?php endif; ?>
	<?php endforeach; ?>
	<path class="fs-schleife" data-geraet-schleife d="M246 253H282Q298 253 298 237V47Q298 31 282 31H252"/>
	<text class="fs-schleife__txt" x="306" y="142" transform="rotate(90 306 142)">06 RÜCKMELDUNG ANS WERBEKONTO</text>
	<circle class="fs-punkt__hof" data-geraet-hof cx="246" cy="31" r="6"/>
	<circle class="fs-punkt" data-geraet-punkt cx="246" cy="31" r="6"/>
</svg>
