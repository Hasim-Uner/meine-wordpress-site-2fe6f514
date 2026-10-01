<?php
/**
 * Template Name: Landingpage erstellen lassen
 * Description: Festpreis-Angebot Landingpage für Direktkunden – eine Seite, ein Angebot, ein Ziel.
 *
 * Query-Owner für „landingpage erstellen lassen“ (docs/seo/query-ownership.csv).
 * Das Angebot ist als Produkt gebaut: ein Festpreis, ein fester Umfang, eine
 * klare Grenze dessen, was nicht dazugehört. Herleitung des Preises:
 * docs/decisions/preise-website-landingpage.md.
 *
 * Layout wie /performance-marketing/ (Gutachten-System aus system.css). Nur das
 * Angebotsblatt im Abschnitt „Umfang“ (Preiskarte, Leistungsraster) hat ein
 * Delta in assets/css/landingpage-offer.css. Fakten kommen aus dem Kanon: Preis aus
 * hu_landingpage_price(), Zusätze aus der Tracking-Leiter und der
 * Freelancer-Preisliste, Antwortzeit aus hu_response_promise(), Fallzahlen aus
 * hu_e3_metric(). Fragen und FAQPage-Schema lesen nexus_get_landingpage_faq_items(),
 * das Service-Schema steht in inc/org-schema.php, Title und Description in
 * inc/seo-meta.php.
 *
 * Primärer CTA: Projektanfrage mit Thema „Landingpage oder Anfrageweg“
 * (/kontakt/?type=project&focus=conversion), damit die Anfrage ohne
 * Themenschritt ankommt und im CRM zuordenbar bleibt.
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
		$classes[] = 'hu-landingpage-offer-page';
		return array_values( array_unique( $classes ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( function_exists( 'hu_enqueue_css' ) ) {
			hu_enqueue_css( 'hu-navigation-ecosystem', 'navigation-ecosystem.css', [ 'nexus-system-css' ] );
			hu_enqueue_css( 'hu-landingpage-offer', 'landingpage-offer.css', [ 'nexus-system-css' ] );
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
		<nav class="hu-wayfinding-breadcrumb" aria-label="Breadcrumb" data-track-section="breadcrumb">
			<ol>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track-action="breadcrumb_home" data-track-category="navigation">Startseite</a></li>
				<li><span aria-current="page">Landingpage erstellen lassen</span></li>
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

$routes          = hu_get_commercial_route_map();
$contact_url     = hu_get_contact_intake_url( 'project', 'conversion' );
$tracking_url    = $routes['tracking_setup'];
$whitelabel_url  = $routes['whitelabel'];
$website_url     = home_url( '/#angebot-website' );
$page_url        = get_permalink() ? get_permalink() : home_url( '/landingpage-erstellen-lassen/' );
$psi_url         = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( $page_url );

$price           = $fest( hu_landingpage_price() );
$response        = hu_response_promise( 'compact' );
$response_window = hu_response_promise( 'window' );
$tracking_offer  = hu_tracking_product_ladder()['measurement'];
$tracking_price  = $fest( $tracking_offer['price'] );
$website_price   = $fest( hu_freelancer_website_price() );
$website_scope   = $fest( hu_freelancer_website_scope_display() );
$retainer        = $fest( hu_freelancer_retainer_display() );

$e3_canon   = hu_e3_canon();
$case_url   = $e3_canon['url'];
$case_label = $e3_canon['case_label_accusative'] ?? HU_E3_CASE_LABEL_ACCUSATIVE;
$cpl_before = hu_e3_metric( 'cpl_before' );
$cpl_after  = hu_e3_metric( 'cpl_after' );
$timeframe  = hu_e3_metric( 'timeframe', 'display_dative' );

$references = hu_public_reference_projects();
$faq_items  = nexus_get_landingpage_faq_items();

// Was im Festpreis steckt. Reihenfolge = Reihenfolge der Arbeit.
$scope = [
	[ 'Auftakt', 'Ein Gespräch von einer Stunde und ein kurzer Fragebogen: Angebot, Zielgruppe, Preise, woher die Besucher kommen und welche Einwände Sie aus Gesprächen kennen.' ],
	[ 'Konzept und Text', 'Aufbau der Seite, Botschaft und Handlungsaufforderung. Den Text schreibe ich, Sie geben ihn frei, bevor gebaut wird.' ],
	[ 'Umsetzung', 'Die Seite in WordPress, schnell auf dem Handy, auf einer Testumgebung gebaut und erst nach Ihrer Abnahme live.' ],
	[ 'Anfrageformular', 'Wenige Pflichtfelder und die Fragen, die Sie für die Einordnung brauchen. Eine Bestätigung geht an den Absender, die Anfrage per E-Mail an Sie.' ],
	[ 'Herkunft jeder Anfrage', 'Das Formular übergibt die Seite und die Kampagnen-Parameter aus der Adresse, ohne Cookie. Nutzen Sie Google Ads oder GA4, trage ich die Anfrage dort als Conversion ein.' ],
	[ 'SEO-Grundlagen', 'Titel, Beschreibung und strukturierte Daten. Ob die Seite in den Suchindex soll oder nur Anzeigen-Besucher bekommt, entscheiden wir vorher.' ],
	[ 'Abnahme', 'Eine Testanfrage bis in Ihr Postfach, eine Ladezeitmessung und ein Übergabeprotokoll. Zwei Korrekturschleifen sind enthalten.' ],
];

// Was bewusst nicht dazugehört; daraus entsteht der Festpreis.
$not_included = [
	'Anzeigen schalten und betreuen',
	'Foto- und Videoproduktion',
	'Mehrere Varianten der Seite',
	'Anbindung an ein CRM, Shop oder Checkout',
	'Mehrsprachigkeit',
];

// Die Preiskarte setzt Betrag und Währung getrennt, damit das Zeichen leiser steht.
$price_parts  = preg_split( '/[\s\x{00A0}]+/u', $price, 2 );
$price_amount = $price_parts[0];
$price_unit   = $price_parts[1] ?? '';

// Kurzfassung in der Preiskarte; die Einzelheiten stehen im Raster darunter.
$price_facts = [
	'Text inklusive',
	'Formular mit Herkunft jeder Anfrage',
	'Zwei Korrekturschleifen',
	'Live erst nach Ihrer Abnahme',
];

// Wann sich eine eigene Seite lohnt.
$occasions = [
	'Eine Google-Ads-Kampagne soll starten, aber die Anzeige würde auf der Startseite landen.',
	'Ein neues Angebot hat noch keine eigene Seite, auf die Sie Interessenten schicken können.',
	'Die Startseite erklärt alles gleichzeitig, und Besucher finden den nächsten Schritt nicht.',
	'Anfragen kommen, aber Sie wissen nicht, über welche Anzeige oder Seite.',
];

// Anlass 04 gehört nicht mehr zur Landingpage, sondern zur bestehenden Website:
// dort führt der Satz zur Conversion-Optimierung.
$occasion_links = [
	3 => $routes['conversion'],
];

// Ablauf in vier Stationen; der Termin wird vor dem Start schriftlich festgelegt.
$process = [
	[ '01 · Auftakt', 'Gespräch und Fragebogen. Danach stehen Umfang und Termin schriftlich fest.' ],
	[ '02 · Text', 'Sie bekommen Aufbau und Text zur Freigabe, bevor die Seite gebaut wird.' ],
	[ '03 · Umsetzung', 'Ich baue auf einer Testumgebung. Sie sehen die Seite, bevor sie jemand anderes sieht.' ],
	[ '04 · Abnahme', 'Testanfrage, Ladezeit, Korrekturen. Live geht die Seite nach Ihrer Freigabe.' ],
];

get_header();
?>

<div id="landingpage-content" class="doku lp-offer-page" data-track-page="landingpage_offer">
	<header class="kopfteil" data-track-section="lp_hero">
		<div class="blatt">
			<p class="gegenstand">Landingpage · WordPress · Festpreis</p>
			<h1>Landingpage erstellen lassen: eine Seite, ein Angebot, eine Anfrage.</h1>
			<p class="aufriss">
				<span class="erst">Zum Festpreis, mit Text und Messung.</span>
				Ich schreibe und baue die Seite, auf die Sie Anzeigen oder Interessenten schicken. Sie führt zu genau einer Handlung, und jede Anfrage kommt mit der Seite und Kampagne an, über die sie kam.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_lp_offer_hero_project" data-track-category="lead_gen" data-track-section="lp_hero">Landingpage anfragen <span class="pf" aria-hidden="true">→</span></a>
				<a class="tun still" href="#umfang" data-track-action="lp_offer_hero_to_scope" data-track-category="navigation" data-track-section="lp_hero">Was im Preis steckt</a>
			</div>
			<p class="mono"><?php echo esc_html( $price ); ?> netto Festpreis · Text inklusive · <?php echo esc_html( $response ); ?></p>

			<div class="meta" aria-label="Angebot im Überblick">
				<dl>
					<div><dt>Preis</dt><dd><?php echo esc_html( $price ); ?> netto, fest vereinbart vor dem Start</dd></div>
					<div><dt>Umfang</dt><dd>Eine Seite mit Text, Formular und Herkunft jeder Anfrage</dd></div>
					<div><dt>Abnahme</dt><dd>Live erst nach Testanfrage und Ihrer Freigabe</dd></div>
					<div><dt>Für Agenturen</dt><dd><a class="satzlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="lp_offer_hero_whitelabel" data-track-category="navigation" data-track-section="lp_hero">Umsetzung unter Ihrem Namen</a></dd></div>
				</dl>
			</div>
		</div>
	</header>

	<section id="anlass" data-track-section="lp_offer_occasion">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Anlass</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Wann sich eine eigene Seite lohnt</p>
				<h2 class="kopf">Eine Anzeige verspricht etwas. Die Seite dahinter muss es einlösen.</h2>
				<p class="vorspann">Eine Startseite spricht alle Besucher gleichzeitig an. Wer auf eine Anzeige oder einen Link zu einem bestimmten Angebot klickt, sucht aber genau dieses Angebot und den nächsten Schritt dorthin.</p>
				<div class="protokoll posten" aria-label="Typische Anlässe">
					<?php foreach ( $occasions as $i => $occasion ) : ?>
						<div class="z"><span><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><b>
							<?php if ( isset( $occasion_links[ $i ] ) ) : ?>
								<a class="satzlink" href="<?php echo esc_url( $occasion_links[ $i ] ); ?>" data-track-action="lp_offer_occasion_to_conversion" data-track-category="navigation" data-track-section="lp_offer_occasion"><?php echo esc_html( $occasion ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $occasion ); ?>
							<?php endif; ?>
						</b></div>
					<?php endforeach; ?>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Eine Handlung</span><b>Eine Seite, ein Ziel.</b> Jede weitere Auswahl auf der Seite ist eine Stelle, an der ein Besucher abspringt.</p></aside>
		</div>
	</section>

	<section id="umfang" data-track-section="lp_offer_scope">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Umfang</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel angebot">
					<div class="angebot-kopf">
						<div class="angebot-titel">
							<p class="mono stempelfarbe">Angebot im Detail</p>
							<h2 class="kopf">Das steckt im Festpreis.</h2>
							<p class="vorspann">Der Preis steht, bevor ich anfange. Er gilt für eine Seite zu einem Angebot, mit allem, was sie braucht, um Anfragen anzunehmen und ihre Herkunft zu zeigen.</p>
						</div>
						<div class="angebot-preis" role="group" aria-label="Festpreis">
							<p class="mono">Festpreis</p>
							<p class="betrag"><span class="summe"><?php echo esc_html( $price_amount ); ?></span><?php if ( '' !== $price_unit ) : ?><span class="einheit"><?php echo esc_html( $price_unit ); ?></span><?php endif; ?></p>
							<p class="netto">netto, fest vereinbart vor dem Start</p>
							<ul class="haken" role="list">
								<?php foreach ( $price_facts as $fact ) : ?>
									<li><?php echo esc_html( $fact ); ?></li>
								<?php endforeach; ?>
							</ul>
							<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_lp_offer_scope_project" data-track-category="lead_gen" data-track-section="lp_offer_scope">Landingpage anfragen <span class="pf" aria-hidden="true">→</span></a>
							<p class="mono antwort"><?php echo esc_html( $response ); ?></p>
						</div>
					</div>
					<ul class="angebot-posten" role="list" aria-label="Leistungsumfang">
						<?php foreach ( $scope as $i => $item ) : ?>
							<li>
								<span class="nr" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<h3><?php echo esc_html( $item[0] ); ?></h3>
								<p><?php echo esc_html( $item[1] ); ?></p>
							</li>
						<?php endforeach; ?>
						<li class="grenze">
							<span class="nr" aria-hidden="true">×</span>
							<h3>Nicht dazu</h3>
							<ul role="list">
								<?php foreach ( $not_included as $excluded ) : ?>
									<li><?php echo esc_html( $excluded ); ?></li>
								<?php endforeach; ?>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<section id="ablauf" data-track-section="lp_offer_process">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Ablauf</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Vier Schritte</p>
				<h2 class="kopf">Sie geben zweimal frei: den Text und die fertige Seite.</h2>
				<p class="vorspann">Dazwischen arbeite ich auf einer Testumgebung. Den Termin lege ich nach dem Auftakt schriftlich fest; er hängt vor allem daran, wie schnell Fakten, Bilder und Freigaben kommen.</p>
				<div class="protokoll posten" aria-label="Ablauf">
					<?php foreach ( $process as $step ) : ?>
						<div class="z"><span><?php echo esc_html( $step[0] ); ?></span><b><?php echo esc_html( $step[1] ); ?></b></div>
					<?php endforeach; ?>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Ihre Konten</span>Domain, Hosting, Werbekonten und Zugänge bleiben bei Ihnen. Sie bekommen das Übergabeprotokoll und können die Seite danach selbst pflegen.</p></aside>
		</div>
	</section>

	<section id="zusaetze" data-track-section="lp_offer_addons">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Zusätze</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Wenn mehr gebraucht wird</p>
				<h2 class="kopf">Was darüber hinausgeht, hat einen eigenen Preis.</h2>
				<p class="vorspann">So bleibt die Landingpage ein fester Betrag, und Sie buchen nur, was Ihr Vorhaben tatsächlich braucht.</p>
				<div class="protokoll posten" aria-label="Buchbare Zusätze">
					<div class="z"><span><?php echo esc_html( $tracking_offer['name'] ); ?></span><b><?php echo esc_html( sprintf( 'Festpreis %s netto: Messplan, GA4, Tag Manager, Consent Mode und Google Ads für die ganze Website, mit Abnahmeprotokoll.', $tracking_price ) ); ?></b></div>
					<div class="z"><span>Website</span><b><?php echo esc_html( sprintf( 'Ab %1$s netto. %2$s.', $website_price, $website_scope ) ); ?></b></div>
					<div class="z"><span>Weiterentwicklung</span><b><?php echo esc_html( sprintf( 'Monatskontingent: %s, monatlich kündbar.', $retainer ) ); ?></b></div>
				</div>
				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="lp_offer_to_tracking_setup" data-track-category="navigation" data-track-section="lp_offer_addons">Tracking-Setup im Detail</a>
					<a class="textlink" href="<?php echo esc_url( $website_url ); ?>" data-track-action="lp_offer_to_website" data-track-category="navigation" data-track-section="lp_offer_addons">Website-Angebot ansehen</a>
				</div>
			</div>
		</div>
	</section>

	<section id="beleg" data-track-section="lp_offer_proof">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Beleg</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Dokumentierter Fall</p>
				<h2 class="kopf">Eigene Anfragen statt gekaufter: ein Fall mit offener Herleitung.</h2>
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
					<a class="textlink" href="<?php echo esc_url( $case_url ); ?>" data-track-action="lp_offer_proof_case" data-track-category="proof" data-track-section="lp_offer_proof">Fallstudie und Herleitung ansehen</a>
					<a class="textlink" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="lp_offer_proof_pagespeed" data-track-category="proof" data-track-section="lp_offer_proof">Ladezeit dieser Seite messen<span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
				</div>
				<?php if ( ! empty( $references ) ) : ?>
					<div class="protokoll posten" aria-label="Öffentliche Arbeiten">
						<?php foreach ( $references as $reference ) : ?>
							<div class="z"><span><?php echo esc_html( $reference['tag'] ); ?></span><b><a class="satzlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="lp_offer_reference_open" data-track-category="proof" data-track-section="lp_offer_proof"><?php echo esc_html( $reference['name'] ); ?><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>: <?php echo esc_html( $reference['text'] ); ?></b></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<aside class="marg"><p class="note"><span class="label">Übertragbar?</span>Das hängt an Markt, Angebot und Wettbewerb. Die Fallstudie legt die Herleitung offen, statt die Zahl allein zu zeigen.</p></aside>
		</div>
	</section>

	<?php if ( ! empty( $faq_items ) ) : ?>
		<section id="fragen" data-track-section="lp_offer_faq">
			<div class="blatt reihe">
				<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">06</span><span class="titel">Fragen</span><span class="strich"></span></div></div>
				<div class="haupt">
					<p class="mono stempelfarbe">Vor der Beauftragung</p>
					<h2 class="kopf leise">Häufige Fragen zur Landingpage.</h2>
					<div class="fragen">
						<?php // Dieselbe Quelle wie das FAQPage-Schema in inc/org-schema.php. ?>
						<?php foreach ( $faq_items as $item ) : ?>
							<details name="lp-offer-faq"><summary data-track-action="faq_lp_offer_open" data-track-label="<?php echo esc_attr( $item['key'] ); ?>" data-track-category="engagement" data-track-section="lp_offer_faq"><?php echo esc_html( $item['question'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $item['answer'] ); ?></p></div></div></details>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="abschluss" id="anfrage" data-track-section="lp_offer_close">
		<div class="blatt reihe"><div class="ganz"><div class="tafel"><div class="reihe">
			<div class="haupt">
				<p class="mono stempelfarbe">Nächster Schritt</p>
				<h2>Für welches Angebot brauchen Sie die Seite?</h2>
				<p class="aufriss">Schreiben Sie kurz, was Sie anbieten, woher die Besucher kommen sollen und bis wann die Seite stehen muss. Sie bekommen Rückfragen oder den Termin für das Auftaktgespräch.</p>
				<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_lp_offer_close_project" data-track-category="lead_gen" data-track-section="lp_offer_close">Landingpage anfragen <span class="pf" aria-hidden="true">→</span></a></div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Antwort</span>Persönlich <?php echo esc_html( $response_window ); ?>, mit Rückfragen oder einem Terminvorschlag.</p></aside>
		</div></div></div></div>
	</section>
</div>

<?php
get_footer();
