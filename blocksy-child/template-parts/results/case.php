<?php
/** Results hub: documented outcome proof. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<section id="grossprojekt" aria-labelledby="grossprojekt-h" data-track-section="grossprojekt">
	<div class="blatt reihe">
		<div class="spalte-links"><div class="kapitel"><span class="nr">01</span><span class="titel">Dokumentierter Fall</span><span class="strich" aria-hidden="true"></span></div></div>
		<div class="voll">
			<p class="mono erg-kicker"><?php echo esc_html( $e3_case_label ); ?> · anonymisiert</p>
			<h2 class="kopf" id="grossprojekt-h">Von der Website bis zur Übergabe an den Vertrieb.</h2>
			<p class="vorspann">Website, Landingpages, Vorqualifizierung, Tracking und CRM-Übergabe wurden mit Kampagnen und laufender Optimierung verbunden. Betrachtungszeitraum: <?php echo esc_html( $metric( 'timeframe' ) ); ?>.</p>
			<div class="tafel erg-solar">
				<p class="mono">Kosten pro Anfrage im dokumentierten Fall</p>
				<dl class="erg-cpl"><div><dt>Gekaufte Anfrage · vorher</dt><dd><?php echo esc_html( $metric( 'cpl_before' ) ); ?></dd></div><div><dt>Eigene Anfrage · nachher</dt><dd><?php echo esc_html( $metric( 'cpl_after' ) ); ?></dd></div></dl>
				<dl class="erg-solar-context"><div><dt>Qualifizierte Anfragen</dt><dd><?php echo esc_html( $metric( 'lead_count' ) ); ?></dd></div><div><dt>Abschlussquote</dt><dd><?php echo esc_html( $metric( 'sales_conversion' ) ); ?></dd></div><div><dt>Zeitraum</dt><dd><?php echo esc_html( $metric( 'timeframe' ) ); ?></dd></div></dl>
				<p class="erg-note">Die CPL-Werte vergleichen Anfrage-Einkauf mit eigener Gewinnung. Sie sind kein vollständiger Kosten-pro-Auftrag-Vergleich.</p>
			</div>
			<div class="erg-case-context"><div><h3>Was sich verändert hat</h3><p>Anfragen liefen durch Landingpage, qualifizierende Fragen und CRM-Übergabe statt als isolierte Formulareingänge.</p></div><div><h3>Wie das einzuordnen ist</h3><p>Die Werte stammen aus einem einzelnen PV-Projekt. Kampagnen, Angebot und Vertrieb wirkten gemeinsam; sie sind keine Prognose für andere Betriebe.</p></div></div>
			<a class="textlink erg-after" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="cta_results_case_study" data-track-category="trust" data-track-section="grossprojekt">Fallstudie und Methodik lesen <span aria-hidden="true">→</span></a>
		</div>
	</div>
</section>
