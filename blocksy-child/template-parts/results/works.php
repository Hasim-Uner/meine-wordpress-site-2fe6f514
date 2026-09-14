<?php
/** Results hub: public WordPress work. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array<int, array{role:string,name:string,discipline:string,url:string,text:string}> $references */
?>
<section id="arbeiten" aria-labelledby="arbeiten-h" data-track-section="arbeiten">
	<div class="blatt reihe">
		<div class="spalte-links"><div class="kapitel"><span class="nr">02</span><span class="titel">WordPress-Arbeiten</span><span class="strich" aria-hidden="true"></span></div></div>
		<div class="voll">
			<h2 class="kopf" id="arbeiten-h">Unterschiedliche Aufgaben. Konkrete Umsetzung.</h2>
			<p class="vorspann">Drei öffentlich erreichbare Arbeiten zeigen Struktur, Gestaltung und Entwicklung. Bei jeder ist mein Beitrag klar benannt.</p>
			<div class="erg-projects">
				<?php foreach ( $references as $reference ) { require __DIR__ . '/work-card.php'; } ?>
			</div>
			<p class="erg-note erg-after">Diese Live-Arbeiten belegen meinen Umsetzungsumfang. Für sie veröffentliche ich hier keine nicht belegten Vorher-Nachher-Zahlen zu Traffic oder Anfragen.</p>
		</div>
	</div>
</section>
