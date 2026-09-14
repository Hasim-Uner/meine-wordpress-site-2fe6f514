<?php
/** Results hub: segmented close. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<section id="weiter" aria-labelledby="weiter-h" data-track-section="weiter">
	<div class="blatt reihe">
		<div class="spalte-links"><div class="kapitel"><span class="nr">04</span><span class="titel">Nächster Schritt</span><span class="strich" aria-hidden="true"></span></div></div>
		<div class="voll">
			<h2 class="kopf" id="weiter-h">Was möchten Sie umsetzen?</h2>
			<p class="vorspann">Nach den Belegen folgt nur noch die passende Route für Ihr Vorhaben.</p>
			<div class="erg-next">
				<?php foreach ( $next_steps as $step ) { require __DIR__ . '/next-row.php'; } ?>
			</div>
			<p class="erg-note erg-after">Leistungen und Preisrahmen finden Sie auf der <a href="<?php echo esc_url( $freelancer_url ); ?>" data-track-action="results_next_to_freelancer" data-track-category="navigation" data-track-section="weiter">Startseite</a>. Für Energieunternehmen erklärt die <a href="<?php echo esc_url( $energy_url ); ?>" data-track-action="results_next_to_energy" data-track-category="navigation" data-track-section="weiter">Branchenseite</a> den Anfrageweg genauer.</p>
		</div>
	</div>
</section>
