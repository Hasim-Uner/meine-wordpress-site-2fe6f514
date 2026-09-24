<?php
/**
 * Abbildung: die Kette vom Suchbegriff bis zum Auftrag.
 *
 * Acht Stationen, fuenf Bruchstellen, Rueckkanal 05 → 01. Ueber [hu_abb]
 * eingebunden. Waagerecht, solange die Abbildung mindestens 820 px breit ist,
 * darunter senkrecht (Container Query in editorial-bausteine.css). Ohne JS
 * steht die Kette sofort fertig; editorial-bausteine.js zeichnet sie einmal,
 * wenn sie ins Bild kommt.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args   = wp_parse_args(
	$args ?? [],
	[
		'dom_id' => 'abb-relaunch-kette',
		'nr'     => '1',
	]
);
$dom_id = sanitize_html_class( (string) $args['dom_id'] );
$nr     = (string) $args['nr'];

$stationen = [
	[
		'name'  => 'Suchbegriff oder Anzeige',
		'mess'  => 'Impression, Klick',
		'bruch' => 'Neue URL ohne 301',
	],
	[
		'name' => "Einstiegs\u{00AD}seite",
		'mess' => 'Scrolltiefe, Interaktion',
	],
	[
		'name' => 'Beleg',
		'mess' => 'Klick auf Referenz',
	],
	[
		'name'  => 'Button',
		'mess'  => 'Klick-Ereignis',
		'bruch' => 'Ereignis hängt an alter Klasse',
	],
	[
		'name'  => 'Formular',
		'mess'  => 'Absende-Ereignis',
		'bruch' => 'Neues Plugin, neue Danke-Seite',
	],
	[
		'name'  => 'Postfach und CRM',
		'mess'  => 'Eingang',
		'bruch' => 'Mailversand ohne SMTP',
	],
	[
		'name' => 'Rückruf',
		'mess' => 'Zeit bis zur Antwort',
	],
	[
		'name' => 'Auftrag',
		'mess' => 'Abschluss im CRM',
	],
];
?>
<figure class="hu-abb hu-abb--kette tafel" aria-labelledby="<?php echo esc_attr( $dom_id ); ?>-titel">
	<figcaption class="hu-abb__kopf">
		<span class="hu-abb__titelzeile">
			<span class="hu-abb__nr"><?php echo esc_html( 'Abb. ' . $nr ); ?></span>
			<span class="hu-abb__titel" id="<?php echo esc_attr( $dom_id ); ?>-titel">Die Kette vom Suchbegriff bis zum Auftrag</span>
		</span>
		<span class="hu-kette-legende" aria-hidden="true">
			<span><i></i>Übergang</span>
			<span><i class="is-bruch"></i>typische Bruchstelle beim Relaunch</span>
		</span>
	</figcaption>
	<ol class="hu-kette" data-hu-kette>
		<?php foreach ( $stationen as $index => $station ) : ?>
			<li class="hu-kette__station<?php echo ! empty( $station['bruch'] ) ? ' is-bruch' : ''; ?>">
				<span class="hu-kette__punkt" aria-hidden="true"></span>
				<span class="hu-kette__nr"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
				<span class="hu-kette__name"><?php echo esc_html( $station['name'] ); ?></span>
				<span class="hu-kette__mess"><em>Messpunkt</em><?php echo esc_html( $station['mess'] ); ?></span>
				<?php if ( ! empty( $station['bruch'] ) ) : ?>
					<span class="hu-kette__riss"><em>Bruchstelle</em><?php echo esc_html( $station['bruch'] ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
	<div class="hu-kette__rueck">
		<span class="hu-kette__bogen" aria-hidden="true"></span>
		<p class="hu-kette__rueck-text">
			<span class="hu-kette__rueck-label">Rückkanal 05 → 01</span>
			<span>Das Absende-Ereignis geht als Conversion an Google Ads und steuert, wer als Nächstes kommt.</span>
			<span class="hu-kette__rueck-riss"><i aria-hidden="true"></i>Bruchstelle: Tag fehlt oder zählt doppelt</span>
		</p>
	</div>
	<p class="hu-abb__fuss">Fünf der markierten Stellen liegen hinter dem sichtbaren Design. Keine davon fällt auf, wenn man sich die neue Seite nur ansieht.</p>
</figure>
