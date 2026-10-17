<?php
/**
 * Abbildung: die Schleife eines Coding-Agenten und wo sie reisst.
 *
 * Sechs Schritte vom Auftrag bis zur Freigabe. Der Mensch steht am Anfang und
 * am Ende, dazwischen arbeitet der Agent; 04 → 03 ist die Schleife, die
 * laeuft, bis das Ziel erreicht ist. Ueber [hu_abb id="agent-schleife"]
 * eingebunden. Waagerecht, solange die Abbildung mindestens 760 px breit ist,
 * darunter senkrecht (Container Query in editorial-bausteine.css). Ohne JS
 * steht alles sofort fertig; editorial-bausteine.js zeichnet die Linie einmal,
 * wenn sie ins Bild kommt (dasselbe Verhalten wie die Relaunch-Kette).
 *
 * Keine Zahl, kein Farbwert: Farben kommen ueber .tafel aus system.css.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args   = wp_parse_args(
	$args ?? [],
	[
		'dom_id' => 'abb-agent-schleife',
		'nr'     => '1',
	]
);
$dom_id = sanitize_html_class( (string) $args['dom_id'] );
$nr     = (string) $args['nr'];

$schritte = [
	[
		'wer'    => 'Mensch',
		'name'   => 'Auftrag',
		'kriterium' => 'Ziel, Grenzen und Abnahme stehen fest',
		'fehler' => 'Abnahme fehlt im Auftrag',
	],
	[
		'wer'    => 'Agent',
		'name'   => 'Code lesen',
		'kriterium' => 'Dateien, Regeln, bestehende Tests',
	],
	[
		'wer'    => 'Agent',
		'name'   => 'Ändern',
		'kriterium' => 'Änderung an Dateien und Befehlen',
		'fehler' => 'Neue Abhängigkeit ohne Not',
	],
	[
		'wer'    => 'Agent',
		'name'   => 'Testen',
		'kriterium' => 'Rückmeldung aus der Umgebung',
		'fehler' => 'Prüfung bestätigt das Falsche',
	],
	[
		'wer'    => 'Agent',
		'name'   => 'Pull Request',
		'kriterium' => 'Änderung mit Begründung',
		'fehler' => 'Symptom statt Ursache',
	],
	[
		'wer'    => 'Mensch',
		'name'   => 'Freigabe',
		'kriterium' => 'Funktion, Einbindung, Wirkung',
		'tor'    => true,
	],
];
?>
<figure class="hu-abb hu-abb--schleife tafel" aria-labelledby="<?php echo esc_attr( $dom_id ); ?>-titel">
	<figcaption class="hu-abb__kopf">
		<span class="hu-abb__titelzeile">
			<span class="hu-abb__nr"><?php echo esc_html( 'Abb. ' . $nr ); ?></span>
			<span class="hu-abb__titel" id="<?php echo esc_attr( $dom_id ); ?>-titel">Die Schleife eines Coding-Agenten und wo sie reißt</span>
		</span>
		<span class="hu-schleife-legende" aria-hidden="true">
			<span><i class="is-mensch"></i>Mensch</span>
			<span><i></i>Agent</span>
			<span><i class="is-fehler"></i>typischer Fehler</span>
		</span>
	</figcaption>
	<div class="hu-schleife__runde" aria-hidden="true">
		<span class="hu-schleife__bogen"></span>
		<span class="hu-schleife__runde-label">Schleife 04 → 03, bis das Ziel erreicht ist</span>
	</div>
	<ol class="hu-schleife" data-hu-schleife>
		<?php foreach ( $schritte as $index => $schritt ) : ?>
			<?php
			$klassen = 'hu-schleife__schritt';
			if ( 'Mensch' === $schritt['wer'] ) {
				$klassen .= ' is-mensch';
			}
			if ( ! empty( $schritt['tor'] ) ) {
				$klassen .= ' is-tor';
			}
			?>
			<li class="<?php echo esc_attr( $klassen ); ?>">
				<span class="hu-schleife__punkt" aria-hidden="true"></span>
				<span class="hu-schleife__nr"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?> <span class="hu-schleife__wer"><?php echo esc_html( $schritt['wer'] ); ?></span></span>
				<span class="hu-schleife__name"><?php echo esc_html( $schritt['name'] ); ?></span>
				<span class="hu-schleife__kriterium"><em>Prüft</em><?php echo esc_html( $schritt['kriterium'] ); ?></span>
				<?php if ( ! empty( $schritt['fehler'] ) ) : ?>
					<span class="hu-schleife__fehler"><em>Typischer Fehler</em><?php echo esc_html( $schritt['fehler'] ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
	<p class="hu-schleife__abbruch">
		<span class="hu-schleife__abbruch-label">Abbruch</span>
		<span>Das Ziel ist erreicht, ein Schrittlimit greift, oder eine Entscheidung steht an, die dem Agenten nicht zusteht.</span>
	</p>
	<p class="hu-abb__fuss">Der Agent prüft jeden Schritt an der Umgebung. Ob das Ergebnis zum Auftrag passt, prüft am Ende ein Mensch. Wer Schritt 01 unscharf lässt, bekommt in Schritt 06 eine saubere Lösung für die falsche Frage.</p>
</figure>
