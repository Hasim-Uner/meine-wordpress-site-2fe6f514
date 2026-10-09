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
 * Gutachten-System aus system.css; die Produktübersicht und nativen Details
 * haben ein Delta in assets/css/landingpage-offer.css. Fakten kommen aus dem Kanon: Preis aus
 * hu_landingpage_price(), Zusätze aus der Tracking-Leiter und der
 * Freelancer-Preisliste, Antwortzeit aus hu_response_promise(), Arbeitsbeleg aus
 * hu_e3_canon(). Fragen und FAQPage-Schema lesen nexus_get_landingpage_faq_items(),
 * das Service-Schema steht in inc/org-schema.php, Title und Description in
 * inc/seo-meta.php.
 *
 * Primärer CTA: Projektanfrage mit Thema „Landingpage“
 * (/kontakt/?type=project&focus=landingpage), damit die Anfrage ohne
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
$contact_url     = add_query_arg( [ 'type' => 'project', 'focus' => 'landingpage' ], home_url( '/kontakt/' ) );
$tracking_url    = $routes['tracking_setup'];
$whitelabel_url  = $routes['whitelabel'];
$website_url     = $routes['website'];
$page_url        = get_permalink() ? get_permalink() : home_url( '/landingpage-erstellen-lassen/' );
$psi_url         = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( $page_url );

$price           = $fest( hu_landingpage_price() );
$response        = hu_response_promise( 'compact' );
$response_window = hu_response_promise( 'window' );
$tracking_offer  = hu_tracking_product_ladder()['measurement'];
$tracking_price  = $fest( $tracking_offer['price'] );
$website_price   = $fest( hu_freelancer_website_price() );
$retainer        = $fest( hu_freelancer_retainer_display() );

$e3_canon   = hu_e3_canon();
$case_url   = $e3_canon['url'];
$case_label = $e3_canon['case_label_accusative'] ?? HU_E3_CASE_LABEL_ACCUSATIVE;

$references = hu_public_reference_projects();
$faq_items  = nexus_get_landingpage_faq_items();

// Fünf Liefergruppen: Überblick sichtbar, technische Einzelheiten auf Wunsch.
$scope = [
	[
		'title' => 'Text für Ihr Angebot',
		'summary' => 'Konzept und Text für eine klare Entscheidung: Angebot, Nutzen, Belege und der nächste Schritt.',
		'label' => 'Wie der Text entsteht',
		'detail' => 'Im Auftaktgespräch und kurzen Fragebogen klären wir Zielgruppe, Angebot, Preise und Einwände. Sie liefern die Fakten; ich entwickle Aufbau und Text. Sie geben den Text frei, bevor gebaut wird.',
	],
	[
		'title' => 'Eine Seite in WordPress',
		'summary' => 'Die Angebotsseite wird für Handy und Desktop umgesetzt, mit technischer SEO-Basis und auf einer Testumgebung.',
		'label' => 'Technik und Suchindex',
		'detail' => 'Titel, Beschreibung und strukturierte Daten gehören dazu. Ob die Seite in den Suchindex soll oder ausschließlich für Kampagnen genutzt wird, entscheiden wir vor dem Start. Die Einbindung in Ihre Website klären wir im Auftakt.',
	],
	[
		'title' => 'Formular & Bestätigung',
		'summary' => 'Ein Formular mit den nötigen Angaben, eine Bestätigung für den Interessenten und die Anfrage in Ihrem Postfach.',
		'label' => 'So läuft die Anfrage',
		'detail' => 'Die Pflichtangaben richten sich nach der ersten Einordnung, nicht nach einem vollständigen Verkaufsgespräch. Vor dem Livegang verfolgen wir eine Testanfrage bis in Ihr Postfach. Eine CRM-, Shop- oder Checkout-Anbindung ist separat.',
	],
	[
		'title' => 'Herkunft & Conversion',
		'summary' => 'Einstiegsseite und verfügbare Kampagnenparameter kommen mit der Anfrage an. Die vereinbarte Conversion dieser Seite wird in Ihren vorhandenen GA4- oder Google-Ads-Konten eingerichtet.',
		'label' => 'Umfang und Messgrenzen',
		'detail' => 'Welche Herkunft erfassbar ist und wie eine Conversion zählt, klären wir anhand von Linkparametern, Einwilligungszuständen und dem bestehenden Setup. Ein neues vollständiges GA4-/GTM-/Consent-Setup gehört zum separaten Tracking-Produkt.',
	],
	[
		'title' => 'Prüfung & Übergabe',
		'summary' => 'Testanfrage, Ladezeitprüfung und Übergabeprotokoll. Zwei Korrekturschleifen sind enthalten; live geht die Seite nach Ihrer Freigabe.',
		'label' => 'Was Sie danach behalten',
		'detail' => 'Domain, Hosting, Werbekonten und Zugänge bleiben bei Ihnen. Sie erhalten das Übergabeprotokoll und können die Seite danach selbst pflegen. Der Termin steht nach dem Auftakt schriftlich fest; Material und Freigaben beeinflussen ihn.',
	],
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
	'Konzept und Text inklusive',
	'Formular und seitenbezogene Messung',
	'Zwei Korrekturschleifen',
	'Live erst nach Ihrer Abnahme',
];

// Der Ablauf ergänzt die Lieferung, statt sie als zweiten Leistungsblock zu wiederholen.
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
			<h1>Landingpage erstellen lassen: eine Seite für Ihr Angebot.</h1>
			<p class="aufriss">
				<span class="erst">Konzept, Text und WordPress zum Festpreis.</span>
				Ich baue die Seite für Ihr einzelnes Angebot: mit einem klaren Weg zum Formular, Bestätigung und seitenbezogener Messung. Für Unternehmen, die eine Kampagne starten oder ein konkretes Angebot präsentieren möchten.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_lp_offer_hero_project" data-track-category="lead_gen" data-track-section="lp_hero">Landingpage anfragen <span class="pf" aria-hidden="true">→</span></a>
				<a class="tun still" href="#umfang" data-track-action="lp_offer_hero_to_scope" data-track-category="navigation" data-track-section="lp_hero">Was im Preis steckt</a>
			</div>
			<p class="mono"><?php echo esc_html( $price ); ?> netto Festpreis · Text inklusive · <?php echo esc_html( $response ); ?></p>

			<div class="lp-produktvergleich" aria-label="Website und Landingpage einordnen">
				<p><b>Website ab <?php echo esc_html( $website_price ); ?> netto</b><br>Grundsystem und erste Hauptseite; Texterstellung optional.</p>
				<p><b>Landingpage für <?php echo esc_html( $price ); ?> netto</b><br>Eine Angebotsseite; Konzept und Text sind enthalten.</p>
			</div>
			<p class="lp-kurzinfo">Sie liefern Fakten, Bilder und Freigaben. Anzeigenbetreuung, CRM-Anbindung und ein neues vollständiges Tracking-Setup sind separat.</p>
			<p class="lp-nebenweg">Für Agenturen: <a class="satzlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="lp_offer_hero_whitelabel" data-track-category="navigation" data-track-section="lp_hero">Umsetzung unter Ihrem Namen</a></p>
		</div>
	</header>

	<section id="beleg" data-track-section="lp_offer_proof">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Arbeitsbeleg</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Landingpages im realen Anfrageweg</p>
				<h2 class="kopf">Die Seite ist ein Teil der Strecke. Der Fall zeigt die ganze.</h2>
				<p class="vorspann">Für einen <?php echo esc_html( $case_label ); ?> habe ich Landingpages, Vorqualifizierung, Messung und die Übergabe an den Vertrieb verbunden. Die Fallstudie beschreibt meinen Beitrag und die Grenzen der Ergebnisse.</p>
				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $case_url ); ?>" data-track-action="lp_offer_proof_case" data-track-category="proof" data-track-section="lp_offer_proof">Arbeitsbeleg und Methodik ansehen</a>
					<a class="textlink" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="lp_offer_proof_pagespeed" data-track-category="proof" data-track-section="lp_offer_proof">Ladezeit dieser Seite messen<span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
				</div>
				<?php if ( ! empty( $references ) ) : ?>
					<details class="lp-details lp-weitere-belege"><summary>Weitere öffentliche Arbeiten</summary>
						<div class="protokoll posten" aria-label="Öffentliche Arbeiten">
							<?php foreach ( $references as $reference ) : ?>
								<div class="z"><span><?php echo esc_html( $reference['tag'] ); ?></span><b><a class="satzlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="lp_offer_reference_open" data-track-category="proof" data-track-section="lp_offer_proof"><?php echo esc_html( $reference['name'] ); ?><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>: <?php echo esc_html( $reference['text'] ); ?></b></div>
							<?php endforeach; ?>
						</div>
					</details>
				<?php endif; ?>
			</div>
			<aside class="marg"><p class="note"><span class="label">Einordnung</span>Die Ergebnisse stammen aus einem Gesamtprojekt mit Kampagnen, Tracking und CRM. Sie sind keine Prognose für eine einzelne Landingpage.</p></aside>
		</div>
	</section>

	<section id="umfang" data-track-section="lp_offer_scope">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Umfang</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel angebot">
					<div class="angebot-kopf">
						<div class="angebot-titel">
							<p class="mono stempelfarbe">Ein Produkt · fünf Liefergruppen</p>
							<h2 class="kopf">Vom Angebot zur fertigen Landingpage.</h2>
							<p class="vorspann">Konzept, Text, Umsetzung und Anfrageweg gehören zusammen. Der Festpreis gilt für eine Seite zu einem Angebot. Den vereinbarten Umfang bestätigen wir schriftlich vor dem Start.</p>
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
								<h3><?php echo esc_html( $item['title'] ); ?></h3>
								<p><?php echo esc_html( $item['summary'] ); ?></p>
								<details class="lp-details lp-lieferdetail"><summary><?php echo esc_html( $item['label'] ); ?></summary><p><?php echo esc_html( $item['detail'] ); ?></p></details>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<section id="zusaetze" data-track-section="lp_offer_addons">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Einordnung</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Vor dem Start klären</p>
				<h2 class="kopf">Die Seite ergänzt Ihre Website. Den Rest buchen Sie bei Bedarf.</h2>
				<p class="vorspann">Die seitenbezogene Conversion in vorhandenen Konten ist enthalten. Müssen GA4, Tag Manager und Consent erst als vollständiges System eingerichtet werden, ist das ein eigenes Tracking-Produkt für <?php echo esc_html( $tracking_price ); ?> netto.</p>
				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="lp_offer_to_tracking_setup" data-track-category="navigation" data-track-section="lp_offer_addons">Vollständiges Tracking-Setup ansehen</a>
					<a class="textlink" href="<?php echo esc_url( $website_url ); ?>" data-track-action="lp_offer_to_website_offer" data-track-category="navigation" data-track-section="lp_offer_addons">Website-Angebot ansehen</a>
				</div>
				<div class="lp-vertiefungen">
					<details id="anlass" class="lp-details" data-track-section="lp_offer_occasion">
						<summary>Wann ist eine Landingpage der passende Einstieg?</summary>
						<p>Für ein konkretes Angebot, eine geplante Kampagne oder einen gezielten Link, der bisher auf Ihre allgemeine Startseite führt. Die Seite hält das Versprechen dieses Einstiegs und erklärt den nächsten Schritt.</p>
						<p>Hat Ihre bestehende Website bereits Besucher, aber zu wenig passende Anfragen? Die <a class="satzlink" href="<?php echo esc_url( $routes['conversion'] ); ?>" data-track-action="lp_offer_occasion_to_conversion" data-track-category="navigation" data-track-section="lp_offer_occasion">Conversion-Analyse</a> klärt zuerst, wo die Strecke hakt.</p>
					</details>
					<details id="ablauf" class="lp-details" data-track-section="lp_offer_process">
						<summary>Ablauf, Termin und Ihr Beitrag</summary>
						<p>Sie liefern die Fakten zum Angebot, passende Bilder und die Freigaben. Nach dem Auftakt vereinbaren wir den Termin schriftlich. Wie schnell Material und Entscheidungen kommen, beeinflusst den Livegang.</p>
						<div class="protokoll posten" aria-label="Ablauf">
							<?php foreach ( $process as $step ) : ?>
								<div class="z"><span><?php echo esc_html( $step[0] ); ?></span><b><?php echo esc_html( $step[1] ); ?></b></div>
							<?php endforeach; ?>
						</div>
					</details>
					<details class="lp-details">
						<summary>Was separat vereinbart wird</summary>
						<ul class="lp-grenzen" role="list">
							<?php foreach ( $not_included as $excluded ) : ?>
								<li><?php echo esc_html( $excluded ); ?></li>
							<?php endforeach; ?>
						</ul>
						<p>Domain, Hosting und externe Dienste sind eigene Kosten. Zusätzlicher Umfang wird vor der Beauftragung schriftlich vereinbart.</p>
					</details>
					<details class="lp-details">
						<summary>Nach dem Livegang weiterentwickeln</summary>
						<p><?php echo esc_html( sprintf( 'Optionales Monatskontingent: %s netto, monatlich kündbar.', $retainer ) ); ?> Eine Voraussetzung für die Landingpage ist es nicht.</p>
					</details>
				</div>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $faq_items ) ) : ?>
		<section id="fragen" data-track-section="lp_offer_faq">
			<div class="blatt reihe">
				<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Fragen</span><span class="strich"></span></div></div>
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
				<p class="aufriss">Nennen Sie Angebot, geplanten Besucherweg und gewünschten Termin. Das Thema Landingpage ist im Formular bereits gewählt. Ich prüfe Ihre Angaben und antworte mit Rückfragen oder einem Terminvorschlag.</p>
				<p class="lp-kurzinfo">Umfang, Festpreis und Termin stehen vor dem Auftrag schriftlich fest. Ihre Anfrage ist noch keine Beauftragung.</p>
				<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_lp_offer_close_project" data-track-category="lead_gen" data-track-section="lp_offer_close">Landingpage anfragen <span class="pf" aria-hidden="true">→</span></a></div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Antwort</span>Persönlich <?php echo esc_html( $response_window ); ?>, mit Rückfragen oder einem Terminvorschlag.</p></aside>
		</div></div></div></div>
	</section>
</div>

<?php
get_footer();
