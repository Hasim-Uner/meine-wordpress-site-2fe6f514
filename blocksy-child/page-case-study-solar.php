<?php
/**
 * Template Name: Solar Case Study Methodik-Case
 * Description: Methodik-Case fuer /case-study-solar-leadgenerierung/.
 *
 * Konzept seit 2026-10-23: "Die Seite laeuft die Strecke". Links steht ein
 * Messgeraet (sticky), rechts das Protokoll in Kapiteln. Jedes Kapitel traegt
 * in data-* den Zustand des Geraets (Wert, Phase, Stationen, Rueckkopplung);
 * case-study-motion.js setzt ihn beim Scrollen. Ohne JS zeigt das Geraet den
 * Endzustand, jedes Kapitel nennt seine Zahl ohnehin im Text.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$diagnostic_url = function_exists( 'hu_get_request_analysis_url' )
	? hu_get_request_analysis_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );

$tracking_attrs = 'data-track-section="case_solar_methodology" data-track-funnel-stage="proof"';

$e3 = static function ( $metric, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $metric, $field, $fallback ) : $fallback;
};

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_label = isset( $e3_canon['case_label'] ) ? (string) $e3_canon['case_label'] : 'mittelständischer PV-Installationsbetrieb';

$e3_cpl_before   = $e3( 'cpl_before', 'display', '150 €' );
$e3_cpl_after    = $e3( 'cpl_after', 'display', '22 €' );
$e3_cpl_ramp     = $e3( 'cpl_ramp', 'display', '120 → 70–80 €' );
$e3_cpl_tested   = $e3( 'cpl_tested', 'display', '70–80 €' );
$e3_lead_count   = $e3( 'lead_count', 'display', '1.750' );
$e3_conv_after   = $e3( 'sales_conversion_after', 'display', '15 %' );
$e3_timeframe_dt = $e3( 'timeframe', 'display_dative', '6 Monaten' );
$e3_timeline     = $e3_canon['timeline'];
$e3_cpl_late     = $e3( 'cpl_range_late', 'display', '15–30 €' );
$e3_sst_effect   = $e3( 'sst_effect', 'display', 'über 40 %' );
$e3_crm_share    = $e3( 'crm_capture_share', 'display', '95 %' );
$e3_reached      = isset( $e3_canon['cpl_reached_label'] ) ? (string) $e3_canon['cpl_reached_label'] : 'ab Monat 3';

// Zahlen fuer das Messgeraet (Skala 0 bis Portalpreis).
$meter_max    = (int) HU_E3_CPL_BEFORE;
$meter_after  = (int) HU_E3_CPL_AFTER;
$meter_start  = (int) HU_E3_CPL_RAMP_HIGH;
$meter_tested = (int) $e3( 'cpl_tested', 'value_high', '80' );
$tested_range = trim( str_replace( '€', '', $e3_cpl_tested ) );

// Kurze Phasentexte fuer das Geraet (mobil steht es als schmale Leiste).
$phase_campaign = sprintf( 'Monat 2. Kampagnenstart bei rund %d €, nach zwei bis drei Wochen Testen %s.', $meter_start, $e3_cpl_tested );
$phase_end      = sprintf( '%s rund %s, einzelne Monate zwischen %s.', ucfirst( $e3_reached ), $e3_cpl_after, str_replace( '–', ' und ', $e3_cpl_late ) );

// Kompetenzbeleg fuer alle Wege: Hauptaktion ist die Projektanfrage mit Fokus
// Anfragestrecke, weil der Fall genau dieses Produkt zeigt. Der Marktcheck
// bleibt ein leiser Weg fuer Solar-/Waermepumpenbetriebe.
$project_url = hu_get_contact_intake_url( 'project', 'anfragestrecke' );
$offer_price = function_exists( 'hu_anfragestrecke_price' ) ? hu_anfragestrecke_price() : '';
$offer_crm   = function_exists( 'hu_anfragestrecke_price' ) ? hu_anfragestrecke_price( true ) : '';
$reply_label = defined( 'HU_RESPONSE_PROMISE_SENTENCE' ) ? HU_RESPONSE_PROMISE_SENTENCE : 'Antwort innerhalb von 24 Stunden werktags.';
$situations  = function_exists( 'hu_contact_situation_options' ) ? hu_contact_situation_options() : [];

$role_own = [
	'Audit der Betriebsdaten',
	'Konzept der Strecke',
	'Landingpages und Formulare',
	'Kampagnen bei Google und Meta',
	'Tracking, auch serverseitig',
	'Anbindung an Bitrix24',
	'Technisches SEO',
];
$role_business = [
	'Rückruf und Beratung',
	'Angebot und Abschluss',
	'Freigaben durch die Geschäftsführung',
];

$comparison_rows = [
	[ 'Menge', 'hängt am Kontingent des Portals', 'über eigenes Budget steuerbar' ],
	[ 'Exklusivität', 'geht an mehrere Betriebe', 'wird nicht weiterverkauft; vergleichen kann der Interessent trotzdem' ],
	[ 'Angaben vorab', 'Name und Telefonnummer', 'Produktinteresse und Herkunft im CRM' ],
	[ 'Lernen', 'keine Rückmeldung', 'Abschlüsse gehen zurück an die Werbekonten' ],
];

$offer_rows = [
	[
		'title'  => 'Anfragestrecke',
		'price'  => $offer_price,
		'note'   => 'netto · Festpreis',
		'items'  => [ 'Landingpage', 'mehrstufiges Formular', 'Conversion-Tracking', 'Benachrichtigung ans Team', 'Bestätigung mit Terminlink' ],
		'plus'   => [],
		'action' => 'case_offer_anfragestrecke',
	],
	[
		'title'  => 'Anfragestrecke mit CRM',
		'price'  => $offer_crm,
		'note'   => 'netto · wie in diesem Fall',
		'items'  => [ 'alles aus der Anfragestrecke' ],
		'plus'   => [ 'CRM-Anbindung mit Herkunft', 'serverseitige Messung', 'Rückmeldung an Google Ads' ],
		'action' => 'case_offer_anfragestrecke_crm',
	],
];

$deeper_links = [
	[ 'title' => 'Server-Side-Tracking B2B', 'url' => home_url( '/server-side-tracking-b2b/' ) ],
	[ 'title' => 'Anfrage- und Auftragskosten berechnen', 'url' => home_url( '/solar-leads-kosten-studie/#cpo' ) ],
	[ 'title' => 'Der eigene Anfrageweg im Betrieb', 'url' => home_url( '/solar-waermepumpen-leadgenerierung/#strecke' ) ],
	[ 'title' => 'Portal-Leads vs. eigenes System', 'url' => home_url( '/solar-leads-kaufen-alternative/#vergleich' ) ],
];

$faq_items = function_exists( 'nexus_get_e3_case_faq_items' ) ? nexus_get_e3_case_faq_items() : [];

$case_css_path = get_stylesheet_directory() . '/assets/css/e3-case-v2.css';
wp_enqueue_style(
	'hu-e3-case-v2',
	get_stylesheet_directory_uri() . '/assets/css/e3-case-v2.css',
	[ 'nexus-system-css' ],
	file_exists( $case_css_path ) ? (string) filemtime( $case_css_path ) : wp_get_theme()->get( 'Version' )
);

$track = static function ( $action, $category = 'lead_gen', $section = 'case_solar_methodology' ) {
	return sprintf(
		'data-track-action="%s" data-track-category="%s" data-track-section="%s"',
		esc_attr( $action ),
		esc_attr( $category ),
		esc_attr( $section )
	);
};

/*
 * Zustand des Messgeraets je Kapitel. wert: Anzeige; zahl: Zahl fuer Skala und
 * Zaehler, wenn wert eine Spanne ist; von: Startwert der Zaehlung.
 */
$chapter = static function ( array $state ) {
	$out = 'data-kapitel';
	foreach ( $state as $key => $value ) {
		$out .= sprintf( ' data-%s="%s"', esc_attr( $key ), esc_attr( (string) $value ) );
	}
	return $out;
};

get_header();
?>

<div class="site-main">
	<article class="e3-case-v2 fs" <?php echo $tracking_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="fs-lauf">
			<aside class="fs-geraet" id="fs-geraet" aria-label="Messgerät: Werbekosten pro Anfrage im Verlauf des Falls" data-ziel="1" data-schleife="1" data-ende="0">
				<p class="fs-label">Messprotokoll · Werbekosten pro Anfrage</p>
				<div class="fs-anzeige">
					<p class="fs-wert"><span class="fs-wert__zahl" data-geraet-zahl><?php echo esc_html( (string) $meter_after ); ?></span><span class="fs-wert__einheit" data-geraet-einheit>€</span></p>
					<p class="fs-phase" data-geraet-phase><?php echo esc_html( $phase_end ); ?></p>
					<span class="fs-blitz" data-geraet-blitz>Server-Side live · <?php echo esc_html( $e3_sst_effect ); ?> günstiger</span>
				</div>
				<div class="fs-skala" aria-hidden="true">
					<div class="fs-skala__spur"><div class="fs-skala__fuell" data-geraet-fuell style="--anteil:<?php echo esc_attr( (string) round( $meter_after / $meter_max, 4 ) ); ?>"></div></div>
					<div class="fs-skala__zahlen"><span>0 €</span><span>50 €</span><span>100 €</span><span><?php echo esc_html( $e3_cpl_before ); ?></span></div>
				</div>
				<div class="fs-schema"><?php get_template_part( 'template-parts/case-study-strecke' ); ?></div>
				<div class="fs-monate" aria-hidden="true"><span data-m="0">Vorher</span><span data-m="1">Monat 1</span><span data-m="2">Monat 2</span><span class="is-an" data-m="3"><?php echo esc_html( $e3_reached ); ?></span></div>
				<a class="fs-geraet__cta" href="#angebot" <?php echo $track( 'case_meter_to_offer', 'navigation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>Diese Strecke für Ihr Angebot <small>ab <?php echo esc_html( $offer_price ); ?> →</small></a>
			</aside>

			<div class="fs-protokoll">
				<section class="fs-kapitel fs-kapitel--start" id="fall" aria-labelledby="fs-titel" <?php echo $chapter( [ 'wert' => $meter_max, 'phase' => 'Portal-Einkauf, vorher. Dieselbe Anfrage geht an mehrere Betriebe.', 'monat' => '0' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<p class="fs-label">Fallstudie · <?php echo esc_html( $e3_case_label ); ?> · DACH</p>
					<h1 id="fs-titel">Von gekauften Leads zum eigenen Anfrage&shy;system.</h1>
					<p class="fs-lead">Der Betrieb kaufte seine Anfragen bei Portalen, zu <?php echo esc_html( $e3_cpl_before ); ?> pro Stück. Ich habe ihm eine eigene Strecke gebaut: von der Anzeige über Landingpage und Formular bis ins CRM und von dort als Signal zurück in die Werbekonten. Am Ende kostete eine Anfrage <?php echo esc_html( $e3_cpl_after ); ?>.</p>
					<div class="fs-fakten">
						<div><b><?php echo esc_html( $e3_cpl_after ); ?></b><span>Werbekosten pro Anfrage <?php echo esc_html( $e3_reached ); ?></span></div>
						<div><b><?php echo esc_html( $e3_lead_count ); ?></b><span>Anfragen in <?php echo esc_html( $e3_timeframe_dt ); ?></span></div>
						<div><b><?php echo esc_html( $e3_conv_after ); ?></b><span>Abschlussquote der vorqualifizierten CRM-Anfragen</span></div>
					</div>
					<div class="fs-aktionen">
						<a class="fs-knopf" href="#schritt1" <?php echo $track( 'case_hero_to_step1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>Projekt anfragen <span aria-hidden="true">→</span></a>
						<span class="fs-hinweis fs-label"><i aria-hidden="true"></i>Die Strecke ablaufen</span>
					</div>
				</section>

				<section class="fs-kapitel" id="ausgangslage" aria-labelledby="fs-ausgang" <?php echo $chapter( [ 'wert' => $meter_max, 'phase' => 'Portal-Einkauf, vorher. Keine Rückmeldung, welche Anfrage zum Auftrag wurde.', 'monat' => '0' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<p class="fs-marke">Ausgangslage</p>
					<h2 id="fs-ausgang">Die Anfragen kamen. Gesteuert hat sie jemand anderes.</h2>
					<p class="fs-satz">Den ersten Hinweis gab der Vertrieb: Anfragen aus einem Hersteller-Partnerprogramm wurden <strong>deutlich häufiger zu Aufträgen</strong> als die gekauften. Gleiches Team, gleiche Produkte. Der Unterschied lag in der Anfrage selbst.</p>
					<div class="fs-tabelle">
						<table>
							<thead><tr><td></td><th scope="col">Portal-Einkauf</th><th scope="col">Eigene Strecke</th></tr></thead>
							<tbody>
								<?php foreach ( $comparison_rows as $row ) : ?>
									<tr><th scope="row"><?php echo esc_html( $row[0] ); ?></th><td class="fs-alt"><?php echo esc_html( $row[1] ); ?></td><td><?php echo esc_html( $row[2] ); ?></td></tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</section>

				<section class="fs-kapitel" id="audit" aria-labelledby="fs-audit" <?php echo $chapter( [ 'wert' => '–', 'phase' => 'Monat 1. Noch keine Kampagnen: erst die Daten des Betriebs.', 'monat' => '1' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<p class="fs-marke">Monat 1 · Audit</p>
					<h2 id="fs-audit">Erst verstehen, was der Betrieb weiß. Dann bauen.</h2>
					<p class="fs-satz"><?php echo esc_html( $e3_timeline['preparation'] ); ?></p>
					<div class="fs-rolle">
						<div>
							<p class="fs-label">Mein Teil</p>
							<ul><?php foreach ( $role_own as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul>
						</div>
						<div>
							<p class="fs-label">Teil des Betriebs</p>
							<ul><?php foreach ( $role_business as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul>
						</div>
					</div>
				</section>

				<section class="fs-kapitel" id="system" aria-labelledby="fs-nachfrage" <?php echo $chapter( [ 'wert' => $tested_range, 'zahl' => $meter_tested, 'von' => $meter_start, 'stationen' => '1,2', 'phase' => $phase_campaign, 'monat' => '2' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<p class="fs-marke">Stationen 01–02 · Nachfrage und Einstieg</p>
					<h2 id="fs-nachfrage">Eigene Kanäle statt fremdem Kontingent.</h2>
					<p class="fs-satz">Kampagnen bei Meta und Google, Landingpages je Produkt: <strong>Photovoltaik, Wärmepumpe, Kombination</strong>. Dazu technisches SEO für kaufnahe Suchanfragen. Die Menge hing ab jetzt am eigenen Budget, nicht am Portal.</p>
					<p class="fs-beleg"><b><?php echo esc_html( $e3_lead_count ); ?></b><span>Anfragen in sechs Monaten, vor allem über Meta, organische Suche und Google Ads.</span></p>
				</section>

				<section class="fs-kapitel" id="uebergabe" aria-labelledby="fs-uebergabe" <?php echo $chapter( [ 'wert' => $tested_range, 'zahl' => $meter_tested, 'stationen' => '1,2,3,4', 'phase' => 'Stationen 03–04. Jede Anfrage kommt mit Produkt und Herkunft im CRM an.', 'monat' => '2' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<p class="fs-marke">Stationen 03–04 · Anfrage und Übergabe</p>
					<h2 id="fs-uebergabe">Der Vertrieb weiß vor dem Anruf, worum es geht.</h2>
					<p class="fs-satz">Das Formular fragt in drei Schritten ab, was für das erste Gespräch zählt. Probieren Sie es aus: Daneben sehen Sie, was dabei im CRM ankommt.</p>
					<div class="fs-versuch">
						<div class="fs-form">
							<p class="fs-label">Schema · Schritt 1 von 3</p>
							<p class="fs-form__frage">Wofür interessieren Sie sich?</p>
							<div class="fs-form__optionen" role="group" aria-label="Produktinteresse">
								<button type="button" class="fs-opt" data-produkt="Photovoltaik" aria-pressed="true">Photovoltaik</button>
								<button type="button" class="fs-opt" data-produkt="Wärmepumpe" aria-pressed="false">Wärmepumpe</button>
								<button type="button" class="fs-opt" data-produkt="Kombination" aria-pressed="false">Beides</button>
							</div>
							<p class="fs-label">Schritt 2 · Objekt und Zeitrahmen</p>
							<p class="fs-label">Schritt 3 · Kontakt</p>
						</div>
						<dl class="fs-crm" aria-live="polite" aria-label="CRM-Eintrag, schematisch">
							<div><dt>Produktinteresse</dt><dd data-crm-produkt>Photovoltaik</dd></div>
							<div><dt>Herkunft</dt><dd>Meta · Kampagne Region</dd></div>
							<div><dt>Einstiegsseite</dt><dd data-crm-seite>Landingpage Photovoltaik</dd></div>
							<div><dt>Status</dt><dd>qualifiziert → Termin</dd></div>
							<div><dt>Rückmeldung</dt><dd class="fs-signal">Signal an Werbekonto</dd></div>
						</dl>
					</div>
					<p class="fs-beleg"><b><?php echo esc_html( $e3_crm_share ); ?></b><span>der Anfragen kamen mit Produktinteresse und Herkunft im CRM an. Originaldaten des Betriebs werden nicht veröffentlicht.</span></p>
				</section>

				<section class="fs-kapitel" id="rueckmeldung" aria-labelledby="fs-rueckmeldung" <?php echo $chapter( [ 'wert' => $meter_after, 'stationen' => '1,2,3,4,5', 'schleife' => '1', 'blitz' => '1', 'ziel' => '1', 'phase' => sprintf( '%s. Die Rückmeldung aus dem CRM steuert die Kampagnen: rund %s.', ucfirst( $e3_reached ), $e3_cpl_after ), 'monat' => '3' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<p class="fs-marke">Station 06 · Rückmeldung</p>
					<h2 id="fs-rueckmeldung">Die Werbekonten lernen, welche Anfrage ein Auftrag wurde.</h2>
					<p class="fs-satz">Tracking im Browser und serverseitig. Qualifizierte Anfragen und Abschlüsse gehen aus Bitrix24 <strong>als Signal zurück an die Werbekonten</strong>. Ab da optimieren die Kampagnen auf Aufträge, nicht auf Formulareingänge.</p>
					<p class="fs-beleg"><b><?php echo esc_html( $e3_sst_effect ); ?></b><span>niedrigere Kosten pro Anfrage, drei bis vier Tage nach dem Livegang des Server-Side-Trackings, bei durchgehend laufenden Kampagnen. Mit der CRM-Rückmeldung ging es weiter bis rund <?php echo esc_html( $e3_cpl_after ); ?>.</span></p>
				</section>

				<section class="fs-kapitel" id="ergebnisse" aria-labelledby="fs-ergebnis" <?php echo $chapter( [ 'wert' => $meter_after, 'stationen' => '1,2,3,4,5', 'schleife' => '1', 'ziel' => '1', 'ende' => '1', 'phase' => $phase_end, 'monat' => '3' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<p class="fs-marke">Ergebnis · sechs Monate</p>
					<h2 id="fs-ergebnis">Die Technik stand schnell. Die Zeit ging in Prüfen und Abstimmen.</h2>
					<ol class="fs-phasen">
						<li><span class="fs-label">Monat 1</span><b>–</b><p>Audit, Landingpages, Formulare. Noch keine Kampagnen.</p></li>
						<li><span class="fs-label">Monat 2</span><b><?php echo esc_html( $e3_cpl_ramp ); ?></b><p>Kampagnenstart, nach zwei bis drei Wochen Testen <?php echo esc_html( $e3_cpl_tested ); ?>.</p></li>
						<li><span class="fs-label"><?php echo esc_html( $e3_reached ); ?></span><b><?php echo esc_html( $e3_cpl_after ); ?></b><p>Einzelne Monate zwischen <?php echo esc_html( str_replace( '–', ' und ', $e3_cpl_late ) ); ?>.</p></li>
					</ol>
					<div class="fs-grenzen">
						<div>
							<p class="fs-label">Worauf sich die Zahlen beziehen</p>
							<ul>
								<li>Reine Werbekosten pro Anfrage, ohne Aufbau und Betreuung. <?php echo esc_html( $e3_cpl_before ); ?> ist der Einkaufspreis einer Portal-Anfrage.</li>
								<li><?php echo esc_html( $e3_conv_after ); ?>: Abschlussquote der vorqualifizierten Anfragen im CRM.</li>
							</ul>
						</div>
						<div>
							<p class="fs-label">Was sie nicht sagen</p>
							<ul>
								<li>Die <?php echo esc_html( $e3_sst_effect ); ?> sind ein Vorher-nachher-Vergleich. Gleichzeitig mit und ohne Server-Side gemessen wurde nicht.</li>
								<li><?php echo esc_html( $e3_cpl_after ); ?> sind ein erreichter Wert, kein Durchschnitt und kein Tarif.</li>
								<li>Ohne Vertrieb, der zügig zurückruft, wird aus keiner Anfrage ein Auftrag.</li>
							</ul>
						</div>
					</div>
				</section>
			</div>
		</div>

		<div class="fs-nach">
			<section class="fs-angebot" id="angebot" aria-labelledby="fs-angebot-titel">
				<div class="fs-stapel">
					<p class="fs-marke">Diese Strecke für Ihr Angebot</p>
					<h2 id="fs-angebot-titel">Die Branche war Solar. Die Strecke passt auf jeden B2B-Betrieb mit Vertrieb.</h2>
					<p class="fs-satz">Ihre bestehende Website bleibt. Gebaut wird auf einer Testumgebung, live geht es erst nach Ihrer Abnahme.</p>
					<div class="fs-zusage">
						<p>Festpreis, Umfang steht vor dem Start schriftlich fest.</p>
						<p>Konten, Tag Manager und Tracking-Server laufen auf Ihren Namen.</p>
						<p>Nach der Übergabe können Sie mich ersetzen. Das ist Absicht.</p>
					</div>
				</div>
				<div class="fs-preise">
					<?php foreach ( $offer_rows as $row ) : ?>
						<div class="fs-preis">
							<h3><?php echo esc_html( $row['title'] ); ?></h3>
							<p class="fs-preis__betrag"><?php echo esc_html( $row['price'] ); ?><small><?php echo esc_html( $row['note'] ); ?></small></p>
							<ul>
								<?php foreach ( $row['items'] as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?>
								<?php foreach ( $row['plus'] as $item ) : ?><li class="is-plus"><?php echo esc_html( $item ); ?></li><?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="fs-schritt1" id="schritt1" aria-labelledby="fs-schritt1-titel">
				<div class="fs-stapel">
					<p class="fs-marke">Schritt 1 Ihrer Strecke</p>
					<h2 id="fs-schritt1-titel">Was soll die Strecke bei Ihnen lösen?</h2>
					<p class="fs-satz">Dieselbe Frage stellt das Formular aus dem Fall. Ihre Antwort geht mit ins Kontaktformular, Sie müssen sie dort nicht noch einmal schreiben.</p>
				</div>
				<div>
					<div class="fs-wahl" role="group" aria-label="Ausgangslage">
						<?php $letter_index = 0; ?>
						<?php foreach ( $situations as $key => $label ) : ?>
							<?php $letter = chr( 65 + $letter_index++ ); ?>
							<a href="<?php echo esc_url( add_query_arg( 'ausgangslage', $key, $project_url ) ); ?>" data-wahl <?php echo $track( 'case_step1_' . str_replace( '-', '_', $key ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><span aria-hidden="true"><?php echo esc_html( $letter ); ?></span><?php echo esc_html( $label ); ?><i aria-hidden="true">→</i></a>
						<?php endforeach; ?>
					</div>
					<p class="fs-klein"><?php echo esc_html( $reply_label ); ?> Oder ohne Vorauswahl: <a class="fs-link" href="<?php echo esc_url( $project_url ); ?>" <?php echo $track( 'cta_case_study_to_project' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>Projekt anfragen</a></p>
					<p class="fs-klein"><a class="fs-link fs-link--leise" href="<?php echo esc_url( $diagnostic_url ); ?>" <?php echo $track( 'cta_case_study_to_diagnostic_request' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>Solar- oder Wärmepumpenbetrieb? Zum kostenlosen Marktcheck</a></p>
				</div>
			</section>

			<section class="fs-unten" id="fragen" aria-labelledby="fs-fragen">
				<div class="fs-stapel">
					<p class="fs-marke">Fragen zum Fall</p>
					<h2 class="fs-h2-leise" id="fs-fragen">Was häufig nachgefragt wird.</h2>
					<ul class="fs-verweise">
						<?php foreach ( $deeper_links as $link ) : ?>
							<li><a href="<?php echo esc_url( $link['url'] ); ?>" <?php echo $track( 'cta_case_study_deeper_link', 'navigation', 'vertiefung' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $link['title'] ); ?> <span aria-hidden="true">→</span></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php if ( ! empty( $faq_items ) ) : ?>
					<div class="fs-faq">
						<?php foreach ( $faq_items as $faq_item ) : ?>
							<details name="fs-faq">
								<summary><?php echo esc_html( $faq_item['question'] ); ?></summary>
								<p><?php echo esc_html( $faq_item['answer'] ); ?></p>
							</details>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</section>
		</div>
	</article>
</div>

<?php
get_template_part(
	'template-parts/seo-subpage-sticky-cta',
	null,
	[
		'cta_url'        => $project_url,
		'track_category' => 'case_solar_methodology',
		'lead'           => 'Diese Strecke für Ihr Angebot',
		'sub'            => $reply_label,
		'label'          => 'Projekt anfragen',
		'track_action'   => 'cta_sticky_case_study_project',
	]
);

get_footer();
