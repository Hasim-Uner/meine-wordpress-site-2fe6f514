<?php
/**
 * Messleiter: die vier Stufen des Tracking-Produkts an einer Stelle.
 *
 * Seit 2026-10-30. Vorher nannte /ga4-tracking-setup/ im FAQ feste Preise für
 * Server-Side, Meta und CRM, /server-side-tracking-b2b/ dieselben Leistungen
 * "separat kalkuliert" und /performance-marketing/ "vorab angeboten". Der
 * Kanon kannte die Leiter, keine Seite zeigte sie. Diese Komponente liest
 * hu_tracking_product_ladder() und ist die einzige sichtbare Fassung.
 *
 * Die Leiter ist Auskunft, keine Paketwahl: Es gibt keine Schaltfläche je
 * Stufe. Welche Stufe ein Setup braucht, klärt die Bestandsaufnahme; die
 * Seite behält ihren einen Abschluss. Die Stufe der aufrufenden Seite ist
 * markiert (Kante in --stempel, Etikett "Diese Seite").
 *
 * Argumente:
 * - current         Stufenschlüssel der Seite (measurement, standard, …) oder ''.
 * - from            Erste gezeigte Stufe (1–4), Standard 1.
 * - heading         Überschrift (h3).
 * - track_category  data-track-category der Verweise.
 *
 * Tracking: `ladder_to_<schlüssel>` auf den Verweisen zu anderen Seiten.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hu_tracking_product_ladder' ) ) {
	return;
}

$args = wp_parse_args(
	$args ?? [],
	[
		'current'        => '',
		'from'           => 1,
		'heading'        => 'Vier Stufen. Jede enthält die vorige.',
		'track_category' => 'navigation',
	]
);

$ladder  = hu_tracking_product_ladder();
$current = (string) $args['current'];

// Wo jede Stufe ausführlich steht. Server-Side und Server-Side mit Meta
// teilen sich die Fachseite; die CRM-Stufe hat keine eigene Seite.
$stage_urls = [
	'measurement' => function_exists( 'hu_get_commercial_route' ) ? hu_get_commercial_route( 'tracking_setup' ) : home_url( '/ga4-tracking-setup/' ),
	'standard'    => function_exists( 'hu_get_commercial_route' ) ? hu_get_commercial_route( 'tracking_b2b' ) : home_url( '/server-side-tracking-b2b/' ),
	'pro'         => function_exists( 'hu_get_commercial_route' ) ? hu_get_commercial_route( 'tracking_b2b' ) : home_url( '/server-side-tracking-b2b/' ),
	'individual'  => '',
];

$current_url = '' !== $current && isset( $stage_urls[ $current ] ) ? $stage_urls[ $current ] : '';
$care_price  = function_exists( 'hu_tracking_price' ) ? hu_tracking_price( 'standard', 'care' ) : '';
?>
<div class="messleiter" id="messleiter" data-track-section="tracking_ladder">
	<div class="messleiter-kopf">
		<p class="mono stempelfarbe">Tracking in vier Stufen · Preise netto, einmalig</p>
		<h3><?php echo esc_html( (string) $args['heading'] ); ?></h3>
		<p>Welche Stufe Ihr Setup braucht, steht nach der Bestandsaufnahme fest, schriftlich und vor dem Start. Bis dahin ist nichts gebucht.</p>
	</div>

	<ol class="messleiter-stufen">
		<?php foreach ( $ladder as $key => $product ) : ?>
			<?php
			if ( (int) $product['stage'] < (int) $args['from'] ) {
				continue;
			}

			$is_current = $key === $current;
			$url        = (string) ( $stage_urls[ $key ] ?? '' );
			$links_away = ! $is_current && '' !== $url && $url !== $current_url;
			?>
			<li class="stufe<?php echo $is_current ? ' ist-hier' : ''; // raw-ok -- static class. ?>"<?php echo $is_current ? ' aria-current="true"' : ''; // raw-ok -- static attribute. ?>>
				<span class="nr" aria-hidden="true"><?php echo esc_html( (string) $product['stage'] ); ?></span>
				<div class="was">
					<p class="name">
						<span class="nur-vorlesen"><?php echo esc_html( sprintf( 'Stufe %d: ', (int) $product['stage'] ) ); ?></span><?php
						if ( $links_away ) :
							?><a href="<?php echo esc_url( $url ); ?>" data-track-action="<?php echo esc_attr( 'ladder_to_' . $key ); ?>" data-track-category="<?php echo esc_attr( (string) $args['track_category'] ); ?>" data-track-section="tracking_ladder"><?php echo esc_html( (string) $product['name'] ); ?></a><?php
						else :
							echo esc_html( (string) $product['name'] );
						endif;
						if ( $is_current ) :
							?><span class="hier">Diese Seite</span><?php
						endif;
					?></p>
					<p class="umfang"><?php echo esc_html( (string) $product['scope'] ); ?></p>
					<p class="wann"><?php echo esc_html( (string) $product['lead'] ); ?></p>
				</div>
				<p class="preis">
					<b><?php echo esc_html( (string) $product['price'] ); ?></b>
					<?php if ( '' !== (string) $product['weeks'] ) : ?>
						<span><?php echo esc_html( (string) $product['weeks'] ); ?></span>
					<?php else : ?>
						<span>nach Aufnahme</span>
					<?php endif; ?>
				</p>
			</li>
		<?php endforeach; ?>
	</ol>

	<p class="messleiter-fuss">
		Ab Stufe 2 läuft ein eigener Tracking-Server; das Hosting zahlen Sie direkt über Ihr Konto.<?php
		if ( '' !== $care_price ) :
			echo esc_html( sprintf( ' Laufende Betreuung ist optional, ab %s netto, monatlich kündbar.', $care_price ) );
		endif;
		?>
	</p>
</div>
