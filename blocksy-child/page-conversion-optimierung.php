<?php
/**
 * Template Name: Conversion-Optimierung
 * Description: Money Page für B2B-Websites mit Besuchern, aber zu wenig passenden Anfragen – Anfragesystem-Analyse als Einstieg, danach Umsetzung zu festen Preisen.
 *
 * Query-Owner für „conversion optimierung b2b“, „conversion optimierung“,
 * „conversion rate optimierung“ und „wordpress conversion optimieren“
 * (docs/seo/query-ownership.csv). Positionierung: keine A/B-Tests bei ein paar
 * Dutzend Anfragen im Monat, sondern ein Befund der Strecke vom Besuch bis zur
 * Rückmeldung im Vertrieb.
 *
 * Aufbau (Verdichtung 2026-10): Kopf, sechs Abschnitte, Abschluss. Die
 * Kernaussage „Drei von sechs Stationen liegen hinter dem Formular“ trägt
 * Abschnitt 2 als eigene Grafik (HTML/CSS, geordnete Liste); alles andere
 * ordnet sich ihr unter. Methode, Vorqualifizierungs-Beispiele und die
 * Referenzliste sind keine eigenen Abschnitte mehr: „Warum kein A/B-Test“ steht
 * als Marginalie in 3, die Einordnung als Station 05 in 2.
 *
 * Layout: Gutachten-System aus system.css. Das Delta für die Strecken-Grafik,
 * den Befund und die Preiszeilen steht in assets/css/conversion-optimierung.css.
 * Die Wurzelklasse heißt bewusst nicht `cro-page`: assets/css/cro.css (Scope
 * .cro-page) gehört zur stillgelegten Route /conversion-rate-optimization/ und
 * bleibt unberührt.
 *
 * Fakten kommen aus dem Kanon, hier steht kein Preis, keine Antwortzeit und
 * keine Kontaktangabe als Text: Analyse aus hu_analysis_price(), Dauer aus
 * hu_diagnose_canon(), Landingpage, Messung und Weiterentwicklung aus
 * inc/canon/pricing-canon.php, Antwortzeit aus hu_response_promise(), Kontakt
 * aus hu_get_contact_email()/hu_get_contact_phone(), Fall aus
 * hu_e3_canon()/hu_e3_metric(). Fragen und FAQPage-Schema lesen
 * nexus_get_conversion_faq_items(), das Service-Schema steht in
 * inc/org-schema.php, Title und Description in inc/seo-meta.php.
 *
 * Primärer CTA: Website-Analyse mit Fokus „Landingpage oder Anfrageweg“
 * (/kontakt/?type=analysis&focus=conversion). Kein Marktcheck-CTA: der
 * Marktcheck gehört zum Energie-Cluster.
 *
 * Wegweiser: Das Türregister „Welcher Weg passt?“ im Fuß entfällt auf dieser
 * Seite. Der Schalter ist die Template-Liste in
 * hu_footer_register_suppressed_templates() (inc/funnel-doors.php).
 *
 * Tracking: alle data-track-action- und data-track-section-Werte tragen den
 * Präfix `cro_offer_`.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'hu-wayfinding-active';
		$classes[] = 'hu-cro-offer-page';
		return array_values( array_unique( $classes ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( function_exists( 'hu_enqueue_css' ) ) {
			hu_enqueue_css( 'hu-navigation-ecosystem', 'navigation-ecosystem.css', [ 'nexus-system-css' ] );
			hu_enqueue_css( 'hu-conversion-optimierung', 'conversion-optimierung.css', [ 'nexus-system-css' ] );
		}

		if ( function_exists( 'hu_enqueue_js' ) ) {
			hu_enqueue_js( 'hu-navigation-ecosystem', 'navigation-ecosystem.js', [] );
		}
	},
	90
);

add_action(
	'wp_body_open',
	static function () {
		?>
		<nav class="hu-wayfinding-breadcrumb" aria-label="Breadcrumb" data-track-section="cro_offer_breadcrumb">
			<ol>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track-action="cro_offer_breadcrumb_home" data-track-category="navigation" data-track-section="cro_offer_breadcrumb">Startseite</a></li>
				<li><span aria-current="page">Conversion-Optimierung</span></li>
			</ol>
		</nav>
		<?php
	},
	30
);

// Betrag und Einheit bleiben in einer Zeile.
$fest = static function ( $value ) {
	return str_replace( [ ' €', ' %' ], [ "\u{00A0}€", "\u{00A0}%" ], (string) $value );
};

$contact_url  = hu_get_contact_intake_url( 'analysis', 'conversion' );
$routes       = hu_get_commercial_route_map();
$tracking_url = $routes['tracking_setup'];
$landing_url  = $routes['landingpage'];

$analysis_price  = $fest( hu_analysis_price() );
$analysis_days   = (int) hu_diagnose_canon()['primary_days'];
$response_window = hu_response_promise( 'window' );
$landing_price   = $fest( hu_landingpage_price() );
$tracking_price  = $fest( hu_tracking_product_ladder()['measurement']['price'] );

// „4 h 490 € · 8 h 950 €“: Stufen aus dem Kanon, kein Preisliteral im Template.
// Jede Stufe ist ein eigenes Element, damit sie schmal untereinander stehen
// kann, statt den Trenner ans Zeilenende zu schieben.
$retainer_tiers = array_map(
	static function ( $tier ) use ( $fest ) {
		return sprintf( '%d h %s', (int) $tier['hours'], $fest( hu_format_eur( $tier['price'] ) ) );
	},
	HU_FREELANCER_RETAINER_TIERS
);

$contact_email = hu_get_contact_email();
$contact_phone = hu_get_contact_phone( 'display' );

$e3_canon      = hu_e3_canon();
$case_url      = $e3_canon['url'];
$case_label    = $e3_canon['case_label'];
$cpl_before    = $fest( hu_e3_metric( 'cpl_before' ) );
$cpl_after     = $fest( hu_e3_metric( 'cpl_after' ) );
$lead_count    = hu_e3_metric( 'lead_count' );
$case_months   = hu_pricing_count_word( (int) hu_e3_metric( 'timeframe', 'value' ) );
$page_url      = get_permalink() ? get_permalink() : home_url( '/conversion-optimierung/' );
$psi_url       = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( $page_url );
$faq_items     = nexus_get_conversion_faq_items();

// 1 · Anlass: vier Symptome.
$occasions = [
	'Die Seite hat Besucher, aber im Monat kommt nur eine Handvoll Anfragen.',
	'Viele Anfragen passen nicht zu Ihrem Angebot oder Budget.',
	'Sie wissen nicht, welche Seite oder Kampagne die guten Anfragen bringt.',
	'Anfragen liegen im Postfach, bis jemand Zeit hat.',
];

// 2 · Strecke: sechs Stationen. Die ersten drei liegen vor dem Absenden, die
// letzten drei dahinter.
$stations = [
	[ 'Einstieg', 'Besucher landen auf der Startseite statt beim Angebot.' ],
	[ 'Angebot', 'Preisrahmen und nächster Schritt fehlen oder stehen ganz unten.' ],
	[ 'Formular', 'Pflichtfelder fragen, was erst im Gespräch wichtig ist.' ],
	[ 'Messung', 'Anfragen werden gezählt, aber keiner weiß, woher die guten kommen.' ],
	[ 'Einordnung', 'Jede Anfrage wird gleich behandelt. Die wertvollen warten mit.' ],
	[ 'Rückmeldung', 'Die Bestätigung sagt nicht, was als Nächstes passiert.' ],
];
$stations_before_submit = 3;

// 3 · Analyse: vier Schritte, Überschrift fett, Satz dahinter.
$analysis = [
	[ 'Auftakt.', 'Eine Stunde Gespräch: Angebot, Zielgruppe, woher die Besucher kommen, was für Sie eine gute Anfrage ist.' ],
	[ 'Zugänge.', 'Lesend auf Website, Analytics und, falls vorhanden, CRM. Sie behalten alle Zugänge.' ],
	[ 'Prüfung.', 'Alle sechs Stationen, auf Handy und Rechner, mit Ihren echten Anfragen der letzten Monate.' ],
	[ 'Befund und Besprechung.', 'Wo Anfragen verloren gehen, drei Hebel mit Aufwand und was ich ausdrücklich nicht empfehle. Danach entscheiden Sie, ob und mit wem Sie umsetzen.' ],
];

get_header();
?>

<div id="conversion-optimierung-content" class="doku cro-offer-page" data-track-page="cro_offer">
	<header class="kopfteil" data-track-section="cro_offer_hero">
		<div class="blatt">
			<p class="gegenstand">Conversion-Optimierung · B2B · WordPress</p>
			<div class="reihe">
				<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">§</span><span class="titel">Anfragen</span><span class="strich"></span></div></div>
				<div class="haupt">
					<p class="mono kicker">Für B2B-Websites mit Besuchern, aber zu wenig passenden Anfragen</p>
					<h1>Mehr passende Anfragen aus dem Traffic, den Sie schon haben.</h1>
					<p class="aufriss">Ich prüfe die ganze Strecke vom ersten Klick bis zum Rückruf. Sie bekommen schriftlich, wo Anfragen verloren gehen, und drei Hebel, sortiert nach Aufwand.</p>

					<div class="ausgang">
						<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cro_offer_cta_hero_analysis" data-track-category="lead_gen" data-track-section="cro_offer_hero">Analyse anfragen <span class="pf" aria-hidden="true">→</span></a>
						<a class="textlink" href="#strecke" data-track-action="cro_offer_hero_to_strecke" data-track-category="navigation" data-track-section="cro_offer_hero">Wo Anfragen verloren gehen</a>
					</div>
					<p class="cro-meta"><span><b><?php echo esc_html( $analysis_price ); ?></b> netto</span><span><b><?php echo esc_html( (string) $analysis_days ); ?></b> Werktage</span><span>bei Umsetzung angerechnet</span></p>
				</div>
			</div>
		</div>
	</header>

	<section id="anlass" aria-labelledby="h-anlass" data-track-section="cro_offer_occasion">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">1</span><span class="titel">Anlass</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="h-anlass">Besucher kommen. Anfragen kaum. Oder die falschen.</h2>
				<ul class="symptome">
					<?php foreach ( $occasions as $occasion ) : ?>
						<li><?php echo esc_html( $occasion ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<aside class="marg"><div class="note"><span class="label">Vorher klären</span>Nicht jede Seite hat ein Conversion-Problem. Ist das Angebot zu teuer oder die Zielgruppe falsch, steht genau das im Befund.</div></aside>
		</div>
	</section>

	<section id="strecke" aria-labelledby="h-strecke" data-track-section="cro_offer_path">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">2</span><span class="titel">Strecke</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="h-strecke">Drei von sechs Stationen liegen hinter Ihrem Formular.</h2>
				<p class="vorspann">Die meisten Optimierungen hören beim Formular auf. Verloren geht aber viel danach: bei Anfragen ohne Herkunft, in einem Postfach, das zwei Tage niemand öffnet, beim Rückruf am nächsten Morgen. Die Analyse prüft alle sechs Stationen.</p>
			</div>
		</div>

		<div class="blatt reihe">
			<figure class="voll strecke" aria-label="Die sechs Stationen der Anfragestrecke">
				<div class="strecke-kopf" aria-hidden="true"><span>Auf der Website</span><span>Nach dem Absenden</span></div>
				<ol class="stationen">
					<?php foreach ( $stations as $index => $station ) : ?>
						<?php if ( $stations_before_submit === $index ) : ?>
							<li class="formkante" aria-hidden="true"><span>Formular abgeschickt</span></li>
						<?php endif; ?>
						<li class="station<?php echo $index >= $stations_before_submit ? ' hinten' : ''; // raw-ok -- static class. ?>">
							<span class="n"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
							<h3><?php
								if ( 0 === $index ) :
									?><span class="nur-vorlesen">Auf der Website: </span><?php
								elseif ( $stations_before_submit === $index ) :
									?><span class="nur-vorlesen">Nach dem Absenden: </span><?php
								endif;
								echo esc_html( $station[0] );
							?></h3>
							<p><?php echo esc_html( $station[1] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
				<figcaption class="strecke-fuss">Typischer Befund je Station. Den Rückruf selbst übernimmt Ihr Vertrieb, die Übergabe baue ich.</figcaption>
			</figure>
		</div>

		<div class="blatt reihe">
			<div class="voll befund">
				<p class="zahl">7×</p>
				<div>
					<p>Wer innerhalb einer Stunde zurückruft, kommt fast siebenmal so oft ins Gespräch mit einem Entscheider wie jemand, der eine Stunde später anruft. Nach einem Tag ist der Abstand mehr als sechzigfach.</p>
					<p class="quelle">Harvard Business Review, „The Short Life of Online Sales Leads“, 2011. 1,25 Mio. Online-Anfragen bei 42 US-Unternehmen, davon 13 B2B.</p>
				</div>
			</div>
		</div>
	</section>

	<section id="analyse" aria-labelledby="h-analyse" data-track-section="cro_offer_analysis">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">3</span><span class="titel">Analyse</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="h-analyse">Die Anfragesystem-Analyse. Ein Festpreis, ein schriftlicher Befund.</h2>
				<ol class="ablauf">
					<?php foreach ( $analysis as $step ) : ?>
						<li><span><b><?php echo esc_html( $step[0] ); ?></b> <?php echo esc_html( $step[1] ); ?></span></li>
					<?php endforeach; ?>
				</ol>
				<p class="nicht">Nicht enthalten: Umsetzung, Texte für neue Seiten, Anzeigenbetreuung, Test-Software.</p>
				<div class="ausgang">
					<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cro_offer_cta_analysis_scope" data-track-category="lead_gen" data-track-section="cro_offer_analysis">Analyse anfragen <span class="pf" aria-hidden="true">→</span></a>
					<p class="cro-meta"><span><b><?php echo esc_html( $analysis_price ); ?></b> netto · <b><?php echo esc_html( (string) $analysis_days ); ?></b> Werktage</span></p>
				</div>
			</div>
			<aside class="marg"><div class="note"><span class="label">Warum kein <?php echo nexus_glossary_link( 'ab-test', 'A/B-Test' ); ?></span><p>Bei ein paar Dutzend Anfragen im Monat zeigt ein Test über Wochen vor allem Zufall. Ich vergleiche vorher und nachher, sauber dokumentiert.</p><p>Reicht Ihr Traffic für belastbare Tests, steht das im Befund.</p></div></aside>
		</div>
	</section>

	<section id="danach" aria-labelledby="h-danach" data-track-section="cro_offer_implementation">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">4</span><span class="titel">Danach</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="h-danach">Umgesetzt wird nur, was im Befund steht.</h2>
				<table class="preise">
					<caption class="nur-vorlesen">Umsetzung nach dem Befund: Leistung und Preis, netto</caption>
					<tbody>
						<tr><th scope="row"><a href="<?php echo esc_url( $landing_url ); ?>" data-track-action="cro_offer_to_landingpage" data-track-category="navigation" data-track-section="cro_offer_implementation">Landingpage</a> für ein Angebot, mit Text, Formular und verfügbarem Herkunftskontext</th><td><?php echo esc_html( $landing_price ); ?></td></tr>
						<tr><th scope="row"><a href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="cro_offer_to_tracking" data-track-category="navigation" data-track-section="cro_offer_implementation">Conversion-Tracking</a>: GA4, Tag Manager, Consent und Ads mit geprüftem Messumfang</th><td><?php echo esc_html( $tracking_price ); ?></td></tr>
						<tr><th scope="row">Einordnung und Übergabe ins CRM</th><td>Festpreis nach Befund</td></tr>
						<tr><th scope="row">Weiterentwicklung, monatlich kündbar</th><td><?php
							foreach ( $retainer_tiers as $tier_index => $tier_label ) :
								if ( 0 < $tier_index ) :
									?><span class="trenner"> · </span><?php
								endif;
								?><span class="tarif"><?php echo esc_html( $tier_label ); ?></span><?php
							endforeach;
						?></td></tr>
					</tbody>
				</table>
				<p>Alle Preise netto. Die <?php echo esc_html( $analysis_price ); ?> der Analyse werden angerechnet.</p>
			</div>
			<aside class="marg"><div class="note"><span class="label">Selbst umsetzen</span>Der Befund ist so geschrieben, dass Ihr Team oder Ihre Agentur damit arbeiten kann. Sie sind nicht an mich gebunden.</div></aside>
		</div>
	</section>

	<section id="beleg" aria-labelledby="h-beleg" data-track-section="cro_offer_proof">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">5</span><span class="titel">Beleg</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="h-beleg"><?php echo esc_html( sprintf( 'Kosten pro Anfrage, %s Monate später.', $case_months ) ); ?></h2>
				<p class="fall-zahl"><span class="von"><span class="nur-vorlesen">vorher </span><?php echo esc_html( $cpl_before ); ?></span><span class="bis"><span class="nur-vorlesen">nachher </span><?php echo esc_html( $cpl_after ); ?></span><span class="einh">pro qualifizierter Anfrage</span></p>
				<p><?php
					echo esc_html(
						sprintf(
							'Ein %1$s in DACH kaufte seine Anfragen bei Portalen. Er bekam stattdessen eine eigene Strecke mit Einordnung und Messung. In %2$s Monaten kamen über %3$s qualifizierte Anfragen.',
							$case_label,
							$case_months,
							$lead_count
						)
					);
				?></p>
				<p><a class="textlink" href="<?php echo esc_url( $case_url ); ?>" data-track-action="cro_offer_proof_case" data-track-category="proof" data-track-section="cro_offer_proof">Fallstudie mit Herleitung</a></p>
			</div>
			<aside class="marg"><div class="note"><span class="label">Selbst nachmessen</span>Die Technik dieser Seite können Sie prüfen, bevor Sie anfragen: <a class="satzlink" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="cro_offer_proof_pagespeed" data-track-category="proof" data-track-section="cro_offer_proof">Ladezeit messen<span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>.</div></aside>
		</div>
	</section>

	<?php if ( ! empty( $faq_items ) ) : ?>
		<section id="fragen" aria-labelledby="h-fragen" data-track-section="cro_offer_faq">
			<div class="blatt reihe">
				<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">6</span><span class="titel">Fragen</span><span class="strich"></span></div></div>
				<div class="haupt">
					<h2 class="kopf" id="h-fragen">Vor der Beauftragung.</h2>
					<div class="fragen">
						<?php // Dieselbe Quelle wie das FAQPage-Schema in inc/org-schema.php. ?>
						<?php foreach ( $faq_items as $item ) : ?>
							<details name="cro-offer-faq"><summary data-track-action="cro_offer_faq_open" data-track-label="<?php echo esc_attr( $item['key'] ); ?>" data-track-category="engagement" data-track-section="cro_offer_faq"><?php echo esc_html( $item['question'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $item['answer'] ); ?></p></div></div></details>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section id="anfrage" aria-labelledby="h-ende" data-track-section="cro_offer_close">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">→</span><span class="titel">Start</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="h-ende">Wie viele Anfragen kommen heute, und wie viele davon passen?</h2>
				<p class="aufriss">Schreiben Sie kurz, was Sie anbieten, wie viele Anfragen im Monat eingehen und woher die Besucher kommen. Sie bekommen <?php echo esc_html( $response_window ); ?> Rückfragen oder einen Terminvorschlag.</p>
				<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cro_offer_cta_close_analysis" data-track-category="lead_gen" data-track-section="cro_offer_close">Analyse anfragen <span class="pf" aria-hidden="true">→</span></a></div>
				<p class="direkt"><span>Lieber direkt: <a href="<?php echo esc_url( 'mailto:' . $contact_email, [ 'mailto' ] ); ?>" data-track-action="cro_offer_close_mail" data-track-category="lead_gen" data-track-section="cro_offer_close"><?php echo esc_html( $contact_email ); ?></a></span><a href="<?php echo esc_url( hu_get_contact_phone( 'link' ), [ 'tel' ] ); ?>" data-track-action="cro_offer_close_tel" data-track-category="lead_gen" data-track-section="cro_offer_close"><?php echo esc_html( $contact_phone ); ?></a></p>
			</div>
		</div>
	</section>
</div>

<?php
get_footer();
