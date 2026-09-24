<?php
/**
 * Abbildung: vorher und nachher, wie viele an jedem Uebergang weiterkommen.
 *
 * Schematisch, keine Messwerte. Ueber [hu_abb id="relaunch-besser"]
 * eingebunden. Die Geometrie ist eine Rechnung: vier Uebergaenge je mal 1,2,
 * am Ende 1,2^4 ≈ 2,07. Werte und Rechnung stehen in
 * docs/briefings/blog-website-relaunch/abb2.py und gehoeren bewusst in keinen
 * Kanon. Sichtbar sind nur „+ 20 %“ und „rund 2 ×“.
 *
 * Farben und Schriften kommen ueber Klassen aus system.css
 * (editorial-bausteine.css), im SVG steht kein Farbwert. Unter 640 px
 * Containerbreite scrollt die Grafik in ihrer eigenen Flaeche.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args   = wp_parse_args(
	$args ?? [],
	[
		'dom_id' => 'abb-relaunch-besser',
		'nr'     => '2',
	]
);
$dom_id = sanitize_html_class( (string) $args['dom_id'] );
$nr     = (string) $args['nr'];

// x-Positionen der acht Stationen im viewBox 900 × 330 (abb2.py, x1 = 710).
$stationen = [
	[ '46.0', 'SUCHE' ],
	[ '140.9', 'SEITE' ],
	[ '235.7', 'BELEG' ],
	[ '330.6', 'BUTTON' ],
	[ '425.4', 'FORMULAR' ],
	[ '520.3', 'POSTFACH' ],
	[ '615.1', 'RÜCKRUF' ],
	[ '710.0', 'AUFTRAG' ],
];

// Mitten der vier verbesserten Uebergaenge 02 → 03 bis 05 → 06.
$marken = [ '188.3', '283.1', '378.0', '472.9' ];

$band_nachher = '46.0,58.0 140.9,77.0 235.7,84.6 330.6,95.5 425.4,111.6 520.3,123.2 615.1,124.7 710.0,138.9 710.0,167.1 615.1,181.3 520.3,182.8 425.4,194.4 330.6,210.5 235.7,221.4 140.9,229.0 46.0,248.0';
$band_vorher  = '46.0,58.0 140.9,77.0 235.7,96.0 330.6,113.1 425.4,129.1 520.3,138.6 615.1,139.4 710.0,146.2 710.0,159.8 615.1,166.6 520.3,167.4 425.4,176.9 330.6,192.9 235.7,210.0 140.9,229.0 46.0,248.0';
?>
<figure class="hu-abb hu-abb--besser" aria-labelledby="<?php echo esc_attr( $dom_id ); ?>-titel">
	<figcaption class="hu-abb__kopf">
		<span class="hu-abb__titelzeile">
			<span class="hu-abb__nr"><?php echo esc_html( 'Abb. ' . $nr ); ?></span>
			<span class="hu-abb__titel" id="<?php echo esc_attr( $dom_id ); ?>-titel">Vorher und nachher: wie viele an jedem Übergang weiterkommen</span>
		</span>
		<span class="hu-besser-legende" aria-hidden="true">
			<span><i class="is-vorher"></i>vorher</span>
			<span><i class="is-nachher"></i>nachher</span>
		</span>
	</figcaption>
	<div class="hu-besser__flaeche" tabindex="0" role="group" aria-labelledby="<?php echo esc_attr( $dom_id ); ?>-titel">
		<svg class="hu-besser" viewBox="0 0 900 330" role="img" aria-labelledby="<?php echo esc_attr( $dom_id ); ?>-t <?php echo esc_attr( $dom_id ); ?>-d" xmlns="http://www.w3.org/2000/svg">
			<title id="<?php echo esc_attr( $dom_id ); ?>-t">Vorher und nachher: wie viele Besucher an jedem Übergang weiterkommen</title>
			<desc id="<?php echo esc_attr( $dom_id ); ?>-d">Schematische Darstellung ohne Messwerte. Zwei Bänder von der Suche bis zum Auftrag. An vier Übergängen, von der Seite bis zum Postfach, kommt nachher jeweils ein Fünftel mehr weiter. Am Ende der Kette sind es dadurch rund doppelt so viele Aufträge.</desc>
			<?php foreach ( $stationen as $station ) : ?>
				<line class="hu-besser__raster" x1="<?php echo esc_attr( $station[0] ); ?>" y1="50" x2="<?php echo esc_attr( $station[0] ); ?>" y2="256"/>
			<?php endforeach; ?>
			<polygon class="hu-besser__band hu-besser__band--nachher" points="<?php echo esc_attr( $band_nachher ); ?>"/>
			<polygon class="hu-besser__band hu-besser__band--vorher" points="<?php echo esc_attr( $band_vorher ); ?>"/>
			<?php foreach ( $marken as $marke ) : ?>
				<text class="hu-besser__plus" x="<?php echo esc_attr( $marke ); ?>" y="40" text-anchor="middle">+ 20 %</text>
			<?php endforeach; ?>
			<line class="hu-besser__ende" x1="720.0" y1="138.9" x2="720.0" y2="167.1"/>
			<text class="hu-besser__ende-label" x="730.0" y="128.9">NACHHER</text>
			<text class="hu-besser__faktor" x="730.0" y="158.0">rund 2 ×</text>
			<text class="hu-besser__ende-fuss" x="730.0" y="189.1">SO VIELE WIE VORHER</text>
			<?php foreach ( $stationen as $index => $station ) : ?>
				<text class="hu-besser__nr" x="<?php echo esc_attr( $station[0] ); ?>" y="278" text-anchor="middle"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></text>
				<text class="hu-besser__station" x="<?php echo esc_attr( $station[0] ); ?>" y="294" text-anchor="middle"><?php echo esc_html( $station[1] ); ?></text>
			<?php endforeach; ?>
		</svg>
	</div>
	<p class="hu-abb__fuss">Schematisch, keine Messwerte. An vier Übergängen kommt jeweils ein Fünftel mehr weiter. Keiner der Schritte ist groß, am Ende der Kette sind es trotzdem rund doppelt so viele.</p>
</figure>
