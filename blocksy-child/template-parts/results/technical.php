<?php
/** Results hub: technical verification. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<section id="technik" aria-labelledby="technik-h" data-track-section="technik">
	<div class="blatt reihe">
		<div class="spalte-links"><div class="kapitel"><span class="nr">03</span><span class="titel">Technische Belege</span><span class="strich" aria-hidden="true"></span></div></div>
		<div class="voll">
			<p class="mono erg-kicker">Code · Prüfungen · Performance</p>
			<h2 class="kopf" id="technik-h">Nicht nur ansehen. Prüfen.</h2>
			<p class="vorspann">Technische Qualität sollte nicht von einer Behauptung abhängen. Diese Nachweise können Sie selbst öffnen.</p>
			<div class="erg-evidence-list">
				<?php foreach ( $technical_proofs as $proof ) { require __DIR__ . '/proof-row.php'; } ?>
			</div>
			<p class="erg-note erg-after">Diese Belege zeigen Arbeitsweise und technische Qualität. Sie ersetzen keine projektspezifischen Geschäftsergebnisse; PageSpeed liefert Labormesswerte und keine vollständige Barrierefreiheits- oder Umsatzbewertung.</p>
		</div>
	</div>
</section>
