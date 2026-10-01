<?php
/**
 * Template Name: Conversion-Optimierung
 * Description: Money Page für B2B-Websites mit wenig Traffic – schriftlicher Befund der Anfragestrecke, danach Umsetzung zu festen Preisen.
 *
 * Query-Owner für „conversion optimierung b2b“, „conversion optimierung“,
 * „conversion rate optimierung“ und „wordpress conversion optimieren“
 * (docs/seo/query-ownership.csv). Positionierung: keine A/B-Tests bei ein paar
 * Dutzend Anfragen im Monat, sondern ein Befund der Strecke vom Besuch bis zur
 * Rückmeldung im Vertrieb.
 *
 * Layout wie /landingpage-erstellen-lassen/ (Gutachten-System aus system.css),
 * keine eigene Stylesheet-Datei. Die Wurzelklasse heißt bewusst nicht
 * `cro-page`: assets/css/cro.css (Scope .cro-page) gehört zur stillgelegten
 * Route /conversion-rate-optimization/ und bleibt unberührt.
 *
 * Fakten kommen aus dem Kanon, hier steht kein Preis und keine Antwortzeit als
 * Text: Analyse aus hu_analysis_price(), Dauer aus hu_diagnose_canon(),
 * Landingpage, Tracking, Website und Weiterentwicklung aus inc/canon/pricing-canon.php,
 * Antwortzeit aus hu_response_promise(), Fall aus hu_e3_canon()/hu_e3_metric().
 * Fragen und FAQPage-Schema lesen nexus_get_conversion_faq_items(), das
 * Service-Schema steht in inc/org-schema.php, Title und Description in
 * inc/seo-meta.php.
 *
 * Primärer CTA: Website-Analyse mit Fokus „Landingpage oder Anfrageweg“
 * (/kontakt/?type=analysis&focus=conversion). Kein Marktcheck-CTA: der
 * Marktcheck gehört zum Energie-Cluster.
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

$routes         = hu_get_commercial_route_map();
$contact_url    = hu_get_contact_intake_url( 'analysis', 'conversion' );
$tracking_url   = $routes['tracking_setup'];
$landing_url    = $routes['landingpage'];
$whitelabel_url = $routes['whitelabel'];
$website_url    = home_url( '/#angebot-website' );
$page_url       = get_permalink() ? get_permalink() : home_url( '/conversion-optimierung/' );
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( $page_url );

$analysis_price  = $fest( hu_analysis_price() );
$analysis_days   = (int) hu_diagnose_canon()['primary_days'];
$response_window = hu_response_promise( 'window' );
$landing_price   = $fest( hu_landingpage_price() );
$tracking_offer  = hu_tracking_product_ladder()['measurement'];
$tracking_price  = $fest( $tracking_offer['price'] );
$website_price   = $fest( hu_freelancer_website_price() );
$retainer        = $fest( hu_freelancer_retainer_display() );

$e3_canon   = hu_e3_canon();
$case_url   = $e3_canon['url'];
$case_label = $e3_canon['case_label_accusative'] ?? HU_E3_CASE_LABEL_ACCUSATIVE;
$cpl_before = hu_e3_metric( 'cpl_before' );
$cpl_after  = hu_e3_metric( 'cpl_after' );
$timeframe  = hu_e3_metric( 'timeframe', 'display_dative' );

$references = hu_public_reference_projects();
$faq_items  = nexus_get_conversion_faq_items();

// 01 · Anlass.
$occasions = [
	'Die Seite hat Besucher, aber im Monat kommen nur eine Handvoll Anfragen.',
	'Es kommen Anfragen, aber viele passen nicht zu Ihrem Angebot oder Budget.',
	'Sie wissen nicht, welche Seite oder Kampagne die guten Anfragen bringt.',
	'Anfragen liegen im Postfach, bis jemand Zeit hat, und der Interessent hat inzwischen woanders angefragt.',
];

// 02 · Methode.
$method = [
	[ 'Prüfen statt raten', 'Ich gehe die Strecke selbst durch, auf dem Handy und am Rechner, mit Ihren echten Formularen und Ihren echten Anfragen der letzten Monate.' ],
	[ 'Messen, was fehlt', 'Wo keine Daten sind, richte ich die Messung zuerst ein. Ohne Herkunft jeder Anfrage ist jede Änderung ein Gefühl.' ],
	[ 'Reihenfolge nach Aufwand', 'Die drei Hebel im Befund sind danach sortiert, was am wenigsten kostet und am meisten Anfragen rettet.' ],
	[ 'Danach vergleichen', 'Nach der Umsetzung vergleichen wir Anfragen und ihre Qualität mit dem Stand vorher. Nicht per Test, sondern vorher und nachher, sauber dokumentiert.' ],
];

// 03 · Strecke: Station, was ich prüfe, typischer Befund.
$stations = [
	[ 'Einstieg', 'Passt die Seite zu dem, was die Anzeige, der Suchbegriff oder der Link verspricht?', 'Besucher landen auf der Startseite statt beim Angebot.' ],
	[ 'Angebot', 'Ist in wenigen Sekunden klar, für wen das ist, was es kostet und was als Nächstes passiert?', 'Preisrahmen und nächster Schritt fehlen oder stehen ganz unten.' ],
	[ 'Formular', 'Wie viele Felder, welche Pflichtfragen, wie verhält es sich auf dem Handy?', 'Pflichtfelder fragen Dinge ab, die erst im Gespräch wichtig sind.' ],
	[ 'Messung', 'Kommt jede Anfrage mit Seite, Quelle und Kampagne an?', 'Anfragen werden gezählt, aber niemand weiß, woher die guten kommen.' ],
	[ 'Vorqualifizierung', 'Werden Anfragen nach Ihren Regeln eingeordnet, bevor jemand sie liest?', 'Jede Anfrage wird gleich behandelt, die wertvollen warten mit.' ],
	[ 'Rückmeldung', 'Wie schnell und wie verbindlich meldet sich jemand? Landet die Anfrage im CRM?', 'Die Bestätigung sagt nicht, was als Nächstes passiert.' ],
];

// 04 · Vorqualifizierung: drei Beispielanfragen, statisch und ohne Skript.
// Ergebnis: [ Fettdruck, Rest ].
$examples = [
	[
		'„SHK-Betrieb, 32 Mitarbeiter, neue Website mit Tracking, HubSpot vorhanden, Budget rund 15.000 €“',
		'Branche, Systeme, Budget genannt',
		'4 von 4 erfüllt',
		[ 'Priorität A', 'Aufgabe im CRM, Rückruf am selben Tag' ],
	],
	[
		'„Wir informieren uns grundsätzlich über eine neue Website, Budget offen“',
		'Bedarf ja, Budget und Zeitrahmen fehlen',
		'2 von 4',
		[ 'Mensch prüft', 'keine automatische Priorität' ],
	],
	[
		'„Wir bieten Ihnen eine Reseller-Partnerschaft an“',
		'kein Projektbedarf',
		'1 von 4',
		[ 'Nicht qualifiziert', 'dokumentiert, keine Vertriebsaufgabe' ],
	],
];

// 05 · Analyse.
$analysis = [
	[ 'Auftakt', 'Ein Gespräch von einer Stunde: Angebot, Zielgruppe, woher die Besucher kommen, was eine gute Anfrage für Sie ist.' ],
	[ 'Zugänge', 'Lesender Zugang zu Website, Analytics und, wenn vorhanden, CRM. Sie behalten alle Zugänge.' ],
	[ 'Prüfung', 'Alle sechs Stationen, mit Ihren Anfragen der letzten Monate als Grundlage.' ],
	[ 'Befund', 'Schriftlich: wo Anfragen verloren gehen, drei priorisierte Hebel mit Aufwand, und was ich ausdrücklich nicht empfehle.' ],
	[ 'Besprechung', 'Wir gehen den Befund gemeinsam durch. Danach entscheiden Sie, ob, was und mit wem Sie umsetzen.' ],
	[ 'Nicht dazu', 'Umsetzung, Texte für neue Seiten, Anzeigenbetreuung, A/B-Test-Software.' ],
];

get_header();
?>

<div id="conversion-optimierung-content" class="doku cro-offer-page" data-track-page="cro_offer">
	<header class="kopfteil" data-track-section="cro_offer_hero">
		<div class="blatt">
			<p class="gegenstand">Conversion-Optimierung · B2B · WordPress</p>
			<h1>Conversion-Optimierung für B2B: mehr brauchbare Anfragen aus dem Traffic, den Sie schon haben.</h1>
			<p class="aufriss">
				<span class="erst">Erst der Befund, dann die Umsetzung.</span>
				Ich prüfe die ganze Strecke, die ein Interessent auf Ihrer Website geht, bis seine Anfrage bei jemandem im Vertrieb ankommt. Sie bekommen schriftlich, wo Anfragen verloren gehen und in welcher Reihenfolge sich das lohnt.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cro_offer_cta_hero_analysis" data-track-category="lead_gen" data-track-section="cro_offer_hero">Analyse anfragen <span class="pf" aria-hidden="true">→</span></a>
				<a class="tun still" href="#strecke" data-track-action="cro_offer_hero_to_strecke" data-track-category="navigation" data-track-section="cro_offer_hero">Wo Anfragen verloren gehen</a>
			</div>
			<p class="mono">Analyse <?php echo esc_html( $analysis_price ); ?> netto · <?php echo esc_html( (string) $analysis_days ); ?> Werktage · wird bei Umsetzung angerechnet</p>

			<div class="meta" aria-label="Angebot im Überblick">
				<dl>
					<div><dt>Einstieg</dt><dd>Anfragesystem-Analyse, <?php echo esc_html( $analysis_price ); ?> netto</dd></div>
					<div><dt>Für</dt><dd>B2B-Websites mit Besuchern, aber zu wenig passenden Anfragen</dd></div>
					<div><dt>Ergebnis</dt><dd>Schriftlicher Befund mit drei priorisierten Hebeln</dd></div>
					<div><dt>Für Agenturen</dt><dd><a class="satzlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="cro_offer_hero_whitelabel" data-track-category="navigation" data-track-section="cro_offer_hero">Umsetzung unter Ihrem Namen</a></dd></div>
				</dl>
			</div>
		</div>
	</header>

	<section id="anlass" data-track-section="cro_offer_occasion">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Anlass</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Wann sich das lohnt</p>
				<h2 class="kopf">Besucher kommen. Anfragen kaum. Oder die falschen.</h2>
				<p class="vorspann">Mehr Traffic löst das selten. Wenn eine Seite schon heute Besucher hat, aber wenig daraus wird, liegt der Verlust meist an einer Stelle, die man benennen kann: im Angebot, im Formular, in der Messung oder danach, wenn niemand zurückruft.</p>
				<div class="protokoll posten" aria-label="Typische Anlässe">
					<?php foreach ( $occasions as $i => $occasion ) : ?>
						<div class="z"><span><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><b><?php echo esc_html( $occasion ); ?></b></div>
					<?php endforeach; ?>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Vorher klären</span><b>Nicht jede Seite hat ein Conversion-Problem.</b> Manchmal ist das Angebot zu teuer oder die Zielgruppe falsch. Das steht dann auch so im Befund.</p></aside>
		</div>
	</section>

	<section id="methode" data-track-section="cro_offer_method">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Methode</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Warum ohne A/B-Test</p>
				<h2 class="kopf">Bei wenig Traffic beweist ein Test nichts. Ein Befund schon.</h2>
				<p class="vorspann">Große Shops testen zwei Varianten gegeneinander und lassen die Zahlen entscheiden. Das funktioniert, wenn täglich Hunderte kaufen. Bei ein paar Dutzend Anfragen im Monat zeigt ein Test über Wochen vor allem Zufall. Für B2B-Websites in dieser Größe arbeite ich deshalb anders.</p>
				<div class="protokoll posten" aria-label="Vorgehen">
					<?php foreach ( $method as $step ) : ?>
						<div class="z"><span><?php echo esc_html( $step[0] ); ?></span><b><?php echo esc_html( $step[1] ); ?></b></div>
					<?php endforeach; ?>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Ab wann Tests</span>Hat Ihre Seite genug Anfragen für belastbare Tests, sage ich das im Befund und empfehle, wer das besser kann.</p></aside>
		</div>
	</section>

	<section id="strecke" data-track-section="cro_offer_path">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Strecke</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Sechs Stellen, an denen Anfragen verloren gehen</p>
				<h2 class="kopf">Die Analyse prüft jede Station, nicht nur die Seite.</h2>
				<p class="vorspann">Die meisten Optimierungen enden am Formular. Verloren geht aber auch viel danach: in einer Bestätigung, die nichts sagt, oder in einem Postfach, das zwei Tage niemand öffnet.</p>
			</div>
			<aside class="marg"><p class="note"><span class="label">Ihr Vertrieb</span>Station 6 liegt bei Ihnen. Ich baue die Übergabe, aber zurückrufen müssen Sie.</p></aside>
		</div>
		<div class="blatt reihe">
			<div class="voll">
				<div class="tabelle" role="region" aria-label="Die sechs Stationen der Anfragestrecke" tabindex="0">
					<table>
						<caption class="nur-vorlesen">Die sechs Stationen der Anfragestrecke: was ich prüfe und der typische Befund</caption>
						<thead>
							<tr><th scope="col">Station</th><th scope="col">Was ich prüfe</th><th scope="col">Typischer Befund</th></tr>
						</thead>
						<tbody>
							<?php foreach ( $stations as $station ) : ?>
								<tr><th scope="row"><?php echo esc_html( $station[0] ); ?></th><td><?php echo esc_html( $station[1] ); ?></td><td><?php echo esc_html( $station[2] ); ?></td></tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>

	<section id="vorqualifizierung" data-track-section="cro_offer_prequal">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Vorqualifizierung</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel">
					<p class="mono stempelfarbe">Station 5 im Detail</p>
					<h2 class="kopf">Eine Anfrage, die sich selbst einordnet, bevor jemand sie liest.</h2>
					<p class="vorspann">Freitext aus dem Formular wird in feste Felder übersetzt: Branche, Bedarf, vorhandene Systeme, Budget, Zeitrahmen. Danach entscheiden Ihre Regeln, nicht ein Sprachmodell, was passiert. Ist die Einordnung unsicher, geht die Anfrage an einen Menschen.</p>
					<div class="tabelle" role="region" aria-label="Drei Beispielanfragen und ihre Einordnung" tabindex="0">
						<table>
							<caption class="nur-vorlesen">Drei Beispielanfragen: was erkannt wird, wie viele Regeln erfüllt sind und was daraus folgt</caption>
							<thead>
								<tr><th scope="col">Anfrage</th><th scope="col">Erkannt</th><th scope="col">Regeln</th><th scope="col">Ergebnis</th></tr>
							</thead>
							<tbody>
								<?php foreach ( $examples as $example ) : ?>
									<tr>
										<th scope="row"><?php echo esc_html( $example[0] ); ?></th>
										<td><?php echo esc_html( $example[1] ); ?></td>
										<td><?php echo esc_html( $example[2] ); ?></td>
										<td><strong><?php echo esc_html( $example[3][0] ); ?></strong>: <?php echo esc_html( $example[3][1] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<p class="mono">Beispielanfragen zur Erklärung, keine Kundendaten.</p>
					<p class="vorspann">Die Vorqualifizierung ist ein Baustein, kein eigenes Produkt. Sie lohnt sich erst, wenn regelmäßig Anfragen kommen, die unterschiedlich viel wert sind. Laufzeit und Modell-Anbieter können in Deutschland oder der EU liegen.</p>
				</div>
			</div>
		</div>
	</section>

	<section id="analyse" data-track-section="cro_offer_analysis">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Analyse</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel">
					<p class="mono stempelfarbe">Anfragesystem-Analyse · <?php echo esc_html( $analysis_price ); ?> netto</p>
					<h2 class="kopf">Das steckt in der Analyse.</h2>
					<p class="vorspann">Ein fester Preis, <?php echo esc_html( (string) $analysis_days ); ?> Werktage, ein schriftliches Ergebnis. Beauftragen Sie danach die Umsetzung, wird der Betrag angerechnet.</p>
					<div class="protokoll posten" aria-label="Umfang der Analyse">
						<?php foreach ( $analysis as $item ) : ?>
							<div class="z"><span><?php echo esc_html( $item[0] ); ?></span><b><?php echo esc_html( $item[1] ); ?></b></div>
						<?php endforeach; ?>
					</div>
					<div class="ausgang">
						<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cro_offer_cta_analysis_scope" data-track-category="lead_gen" data-track-section="cro_offer_analysis">Analyse anfragen <span class="pf" aria-hidden="true">→</span></a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="umsetzung" data-track-section="cro_offer_implementation">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">06</span><span class="titel">Umsetzung</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Nach dem Befund</p>
				<h2 class="kopf">Umgesetzt wird nur, was im Befund steht, zu festen Preisen.</h2>
				<p class="vorspann">Die meisten Hebel sind Bausteine, die es als Festpreis schon gibt. Was nicht in diese Liste passt, bekommt nach der Analyse ein eigenes Angebot.</p>
				<div class="protokoll posten" aria-label="Bausteine der Umsetzung">
					<div class="z"><span>Landingpage</span><b><?php echo esc_html( sprintf( '%s netto: eine Seite zu einem Angebot mit Text, Formular und Herkunft jeder Anfrage.', $landing_price ) ); ?> <a class="satzlink" href="<?php echo esc_url( $landing_url ); ?>" data-track-action="cro_offer_to_landingpage" data-track-category="navigation" data-track-section="cro_offer_implementation">Zur Landingpage</a></b></div>
					<div class="z"><span>Messung</span><b><?php echo esc_html( sprintf( '%s netto: Herkunft jeder Anfrage in GA4 und Google Ads.', $tracking_price ) ); ?> <a class="satzlink" href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="cro_offer_to_tracking" data-track-category="navigation" data-track-section="cro_offer_implementation">Zum Tracking-Setup</a></b></div>
					<div class="z"><span>Vorqualifizierung und CRM</span><b>Nach Umfang, Festpreis nach der Analyse.</b></div>
					<div class="z"><span>Neue Website</span><b><?php echo esc_html( sprintf( 'Ab %s netto, wenn der Befund zeigt, dass Stückwerk nicht reicht.', $website_price ) ); ?> <a class="satzlink" href="<?php echo esc_url( $website_url ); ?>" data-track-action="cro_offer_to_website" data-track-category="navigation" data-track-section="cro_offer_implementation">Zum Website-Angebot</a></b></div>
					<div class="z"><span>Weiterentwicklung</span><b><?php echo esc_html( sprintf( 'Monatskontingent %s, monatlich kündbar.', $retainer ) ); ?></b></div>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Selbst umsetzen</span>Der Befund ist so geschrieben, dass Ihr Team oder Ihre Agentur damit arbeiten kann. Sie sind nicht an mich gebunden.</p></aside>
		</div>
	</section>

	<section id="beleg" data-track-section="cro_offer_proof">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">07</span><span class="titel">Beleg</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Dokumentierter Fall</p>
				<h2 class="kopf">Weniger Kosten pro Anfrage, weil die Strecke stimmt: ein Fall mit offener Herleitung.</h2>
				<p class="vorspann">
					<?php
					echo esc_html(
						sprintf(
							'Für einen %1$s sanken die Kosten pro Anfrage in %2$s von %3$s auf %4$s. Aufgebaut wurde dafür eine eigene Anfragestrecke mit Vorqualifizierung und Messung, statt weiter Anfragen bei Portalen zu kaufen.',
							$case_label,
							$timeframe,
							$cpl_before,
							$cpl_after
						)
					);
					?>
				</p>
				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $case_url ); ?>" data-track-action="cro_offer_proof_case" data-track-category="proof" data-track-section="cro_offer_proof">Fallstudie und Herleitung ansehen</a>
					<a class="textlink" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="cro_offer_proof_pagespeed" data-track-category="proof" data-track-section="cro_offer_proof">Ladezeit dieser Seite messen<span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
				</div>
				<?php if ( ! empty( $references ) ) : ?>
					<div class="protokoll posten" aria-label="Öffentliche Arbeiten">
						<?php foreach ( $references as $reference ) : ?>
							<div class="z"><span><?php echo esc_html( $reference['tag'] ); ?></span><b><a class="satzlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="cro_offer_reference_open" data-track-category="proof" data-track-section="cro_offer_proof"><?php echo esc_html( $reference['name'] ); ?><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>: <?php echo esc_html( $reference['text'] ); ?></b></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<aside class="marg"><p class="note"><span class="label">Übertragbar?</span>Das hängt an Markt, Angebot und Wettbewerb. Die Fallstudie legt die Herleitung offen, statt die Zahl allein zu zeigen.</p></aside>
		</div>
	</section>

	<?php if ( ! empty( $faq_items ) ) : ?>
		<section id="fragen" data-track-section="cro_offer_faq">
			<div class="blatt reihe">
				<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">08</span><span class="titel">Fragen</span><span class="strich"></span></div></div>
				<div class="haupt">
					<p class="mono stempelfarbe">Vor der Beauftragung</p>
					<h2 class="kopf leise">Häufige Fragen zur Conversion-Optimierung.</h2>
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

	<section class="abschluss" id="anfrage" data-track-section="cro_offer_close">
		<div class="blatt reihe"><div class="ganz"><div class="tafel"><div class="reihe">
			<div class="haupt">
				<p class="mono stempelfarbe">Nächster Schritt</p>
				<h2>Wie viele Anfragen kommen heute, und wie viele davon passen?</h2>
				<p class="aufriss">Schreiben Sie kurz, was Sie anbieten, wie viele Anfragen im Monat ungefähr eingehen und woher die Besucher kommen. Sie bekommen Rückfragen oder einen Termin für das Auftaktgespräch.</p>
				<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cro_offer_cta_close_analysis" data-track-category="lead_gen" data-track-section="cro_offer_close">Analyse anfragen <span class="pf" aria-hidden="true">→</span></a></div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Antwort</span>Persönlich <?php echo esc_html( $response_window ); ?>, mit Rückfragen oder einem Terminvorschlag.</p></aside>
		</div></div></div></div>
	</section>
</div>

<?php
get_footer();
