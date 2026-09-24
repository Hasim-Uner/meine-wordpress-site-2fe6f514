<?php
/**
 * Fall-Baustein: das Anfragesystem des PV-Installationsbetriebs.
 *
 * Ueber [hu_fall id="pv-anfragesystem"] eingebunden. Der Text steht hier,
 * Bezeichnung und jede Zahl kommen aus inc/canon/e3-proof-canon.php. Bewusst
 * nicht verwendet: `lead_conversion` — sie ist keine Abschlussquote und
 * gehoert nicht in diese Zahlenreihe.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args   = wp_parse_args( $args ?? [], [ 'dom_id' => 'fall-pv-anfragesystem' ] );
$dom_id = sanitize_html_class( (string) $args['dom_id'] );
$canon  = hu_e3_canon();

$case_label = (string) $canon['case_label_accusative'];
$case_url   = (string) $canon['url'];
$cpl_before = hu_e3_metric( 'cpl_before' );
$cpl_after  = hu_e3_metric( 'cpl_after' );
$lead_count = hu_e3_metric( 'lead_count' );
$timeframe  = hu_e3_metric( 'timeframe', 'display_dative' );
$abschluss  = hu_e3_metric( 'sales_conversion' );
?>
<aside class="hu-fall" aria-labelledby="<?php echo esc_attr( $dom_id ); ?>-titel">
	<p class="hu-fall__kicker">Aus der Praxis</p>
	<p class="hu-fall__titel" id="<?php echo esc_attr( $dom_id ); ?>-titel">Die Kette, Station für Station gebaut</p>
	<p><?php echo esc_html( sprintf( 'Für einen %1$s habe ich diese Kette aufgebaut. Zugekaufte Anfragen waren dort ein wesentlicher Teil der Leadversorgung, zu %2$s pro Anfrage. Neu entstanden Landingpages für konkrete Bedürfnisse, ein mehrstufiges Formular, das Bedarf und Objekt vor dem ersten Gespräch erfasst, eine strukturierte Übergabe ins CRM und ein Rückkanal, über den die Conversion-Signale zurück in Messung und Kampagnensteuerung flossen.', $case_label, $cpl_before ) ); ?></p>
	<ul class="hu-fall__zahlen">
		<li><b><?php echo esc_html( $cpl_before . ' → ' . $cpl_after ); ?></b><span>Kosten pro Anfrage</span></li>
		<li><b><?php echo esc_html( $lead_count ); ?></b><span><?php echo esc_html( 'qualifizierte Anfragen in ' . $timeframe ); ?></span></li>
		<li><b><?php echo esc_html( $abschluss ); ?></b><span>Abschlussquote auf Auftrag</span></li>
	</ul>
	<p class="hu-fall__nachsatz">Das war kein Relaunch, sondern ein Neuaufbau. Er zeigt trotzdem, worum es hier geht: Das Ergebnis kam nicht aus einem einzelnen Hebel, sondern aus Übergängen, die jeweils eine Aufgabe und einen Messpunkt hatten. An der Abschlussquote hatte der Vertrieb des Betriebs einen wesentlichen Anteil. <span class="hu-fall__weiter"><span aria-hidden="true">→</span> <a class="hu-fall__link" href="<?php echo esc_url( $case_url ); ?>">Zur Fallstudie</a></span></p>
</aside>
