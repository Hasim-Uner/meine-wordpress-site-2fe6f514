<?php
/**
 * Rechner: Was kostet ein Formular, das still ausfällt?
 *
 * `[hu_wartung_rechner]` für den Fachartikel zu den Kosten der WordPress-Wartung.
 * Rechnet den verlorenen Deckungsbeitrag eines unbemerkten Formularausfalls
 * gegen den Jahrespreis der Wartung. Der Wartungspreis kommt aus dem Kanon
 * (HU_MAINTENANCE_PRICE); alle anderen Werte sind Voreinstellungen eines
 * Beispiels, keine Messung.
 *
 * Das Ergebnis wird serverseitig mit den Voreinstellungen gerendert, damit die
 * Komponente ohne JavaScript dieselben Zahlen zeigt, die im Artikel stehen.
 * Kein Tracking, keine Cookies, nichts wird gesendet.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default inputs of the calculator.
 *
 * Der Artikel rechnet im Fließtext mit genau diesen Werten (15 Anfragen, 20 %,
 * 1.500 €, 21 Tage). Wer sie ändert, ändert den Beispielabsatz mit.
 *
 * @return array<string, array<string, int|string>>
 */
function hu_wartung_rechner_fields() : array {
	return [
		'anfragen' => [ 'label' => 'Anfragen über die Website pro Monat', 'min' => 1, 'max' => 200, 'step' => 1, 'value' => 15 ],
		'quote'    => [ 'label' => 'Davon werden zu Aufträgen', 'min' => 1, 'max' => 60, 'step' => 1, 'value' => 20 ],
		'db'       => [ 'label' => 'Deckungsbeitrag je Auftrag', 'min' => 100, 'max' => 20000, 'step' => 100, 'value' => 1500 ],
		'tage'     => [ 'label' => 'Tage, bis es jemand merkt', 'min' => 1, 'max' => 90, 'step' => 1, 'value' => 21 ],
		'wartung'  => [
			'label' => 'Wartung pro Monat, netto',
			'min'   => 0,
			'max'   => 500,
			'step'  => 1,
			'value' => defined( 'HU_MAINTENANCE_PRICE' ) ? (int) HU_MAINTENANCE_PRICE : 149,
		],
	];
}

/**
 * Compute the result for a set of inputs. Mirrors assets/js/wartung-rechner.js.
 *
 * @param array<string, float|int> $in Inputs keyed like hu_wartung_rechner_fields().
 * @return array<string, string>
 */
function hu_wartung_rechner_compute( array $in ) : array {
	$a     = (float) $in['anfragen'];
	$q     = (float) $in['quote'] / 100;
	$db    = (float) $in['db'];
	$t     = (float) $in['tage'];
	$w     = (float) $in['wartung'];
	$tag   = $a / 30 * $q * $db;
	$req   = $a / 30 * $t;
	$jobs  = $req * $q;
	$loss  = $tag * $t;
	$jahr  = $w * 12;
	$eur   = static function ( $v ) {
		return number_format( (float) round( $v ), 0, ',', '.' ) . ' €';
	};
	$one   = static function ( $v ) {
		$s = number_format( (float) $v, 1, ',', '.' );
		return ',0' === substr( $s, -2 ) ? substr( $s, 0, -2 ) : $s;
	};

	if ( $w <= 0 ) {
		$satz = 'Ohne Wartungskosten gibt es nichts gegenzurechnen.';
	} else {
		$be = (int) ceil( $jahr / $tag );

		if ( $be > 365 ) {
			$satz = 'Mit diesen Zahlen trägt sich die Wartung über stille Ausfälle nicht. Dann zählen eher Sicherheit und Ihre eingesparte Zeit.';
		} else {
			$satz = sprintf( 'Die Wartung hat sich bezahlt gemacht, wenn sie einmal im Jahr %d %s stillen Ausfall verhindert.', $be, 1 === $be ? 'Tag' : 'Tage' );

			if ( $be <= $t ) {
				$satz .= ' Dieser eine Ausfall kostet mehr als ein Jahr Wartung.';
			}
		}
	}

	return [
		'verlust'   => $eur( $loss ),
		'anfragen'  => $one( $req ),
		'auftraege' => $one( $jobs ),
		'jahr'      => $eur( $jahr ),
		'satz'      => $satz,
	];
}

/**
 * Display value of one input next to its label.
 *
 * @param string $key   Field key.
 * @param int    $value Value.
 * @return string
 */
function hu_wartung_rechner_display( string $key, int $value ) : string {
	if ( 'quote' === $key ) {
		return $value . "\u{00A0}%";
	}

	if ( 'db' === $key || 'wartung' === $key ) {
		return number_format( $value, 0, ',', '.' ) . "\u{00A0}€";
	}

	if ( 'tage' === $key ) {
		return $value . ( 1 === $value ? ' Tag' : ' Tage' );
	}

	return (string) $value;
}

/**
 * Render the calculator.
 *
 * @return string
 */
function hu_wartung_rechner_shortcode() : string {
	$fields = hu_wartung_rechner_fields();
	$in     = [];

	foreach ( $fields as $key => $field ) {
		$in[ $key ] = (int) $field['value'];
	}

	$out = hu_wartung_rechner_compute( $in );

	ob_start();
	?>
	<section class="hu-wartung-rechner" data-hu-wartung-rechner data-track-section="wartung_rechner" aria-labelledby="hu-wartung-rechner-titel">
		<div class="hu-wartung-rechner__eingabe">
			<p class="hu-wartung-rechner__kicker">Rechner · Ihre Zahlen</p>
			<p class="hu-wartung-rechner__titel" id="hu-wartung-rechner-titel">Was kostet ein Formular, das still ausfällt?</p>
			<?php foreach ( $fields as $key => $field ) : ?>
				<div class="hu-wartung-rechner__feld">
					<label for="hu-wr-<?php echo esc_attr( $key ); ?>">
						<?php echo esc_html( (string) $field['label'] ); ?>
						<output for="hu-wr-<?php echo esc_attr( $key ); ?>" data-wr-anzeige="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( hu_wartung_rechner_display( $key, (int) $field['value'] ) ); ?></output>
					</label>
					<input type="range" id="hu-wr-<?php echo esc_attr( $key ); ?>" data-wr-feld="<?php echo esc_attr( $key ); ?>" min="<?php echo esc_attr( (string) $field['min'] ); ?>" max="<?php echo esc_attr( (string) $field['max'] ); ?>" step="<?php echo esc_attr( (string) $field['step'] ); ?>" value="<?php echo esc_attr( (string) $field['value'] ); ?>">
					<?php if ( 'db' === $key ) : ?>
						<p class="hu-wartung-rechner__hinweis">Umsatz minus Material und Fremdleistung, nicht der Umsatz.</p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="hu-wartung-rechner__ergebnis" aria-live="polite">
			<div>
				<p class="hu-wartung-rechner__kicker">Verlorener Deckungsbeitrag</p>
				<p class="hu-wartung-rechner__zahl" data-wr-out="verlust"><?php echo esc_html( $out['verlust'] ); ?></p>
			</div>
			<dl class="hu-wartung-rechner__zeilen">
				<div><dt>Anfragen, die nie ankommen</dt><dd data-wr-out="anfragen"><?php echo esc_html( $out['anfragen'] ); ?></dd></div>
				<div><dt>Aufträge, die daraus geworden wären</dt><dd data-wr-out="auftraege"><?php echo esc_html( $out['auftraege'] ); ?></dd></div>
				<div><dt>Wartung im Jahr</dt><dd data-wr-out="jahr"><?php echo esc_html( $out['jahr'] ); ?></dd></div>
			</dl>
			<p class="hu-wartung-rechner__satz" data-wr-out="satz"><?php echo esc_html( $out['satz'] ); ?></p>
			<p class="hu-wartung-rechner__notiz">Erwartungswert, gerechnet mit 30 Tagen pro Monat. Nichts wird gespeichert oder gesendet.</p>
		</div>
	</section>
	<?php
	$html = (string) ob_get_clean();

	return function_exists( 'hu_editorial_bausteine_skip_faq' ) ? hu_editorial_bausteine_skip_faq( $html ) : $html;
}
add_shortcode( 'hu_wartung_rechner', 'hu_wartung_rechner_shortcode' );
