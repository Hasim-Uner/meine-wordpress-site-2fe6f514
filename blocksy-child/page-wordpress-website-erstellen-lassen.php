<?php
/**
 * Template Name: WordPress-Website erstellen lassen
 * Description: Festpreis-Angebot Website Kompakt für Direktkunden, mit Relaunch-Block.
 *
 * Query-Owner für „wordpress website erstellen lassen“ (docs/seo/query-ownership.csv).
 * Der Relaunch ist ein Block dieser Seite, nicht ihr Fokus; „website relaunch“
 * gehört dem Beitrag /website-relaunch/. Herleitung des Preises:
 * docs/decisions/preise-website-landingpage.md.
 *
 * Layout wie /landingpage-erstellen-lassen/ (Gutachten-System aus system.css).
 * Das Angebotsblatt im Abschnitt „Umfang“ (Preiskarte, Leistungsraster) nutzt
 * das Delta assets/css/landingpage-offer.css mit; die Seite trägt dafür die
 * Klasse lp-offer-page, es gibt keine eigene CSS-Datei. Fakten kommen aus dem
 * Kanon: Preis, Seitenzahl und Zusatzseite aus den Freelancer-Getter, Zusätze
 * aus Tracking-Leiter, hu_landingpage_price() und der Freelancer-Preisliste,
 * Antwortzeit aus hu_response_promise(), Fallzahlen aus hu_e3_metric().
 * Fragen und FAQPage-Schema lesen nexus_get_website_faq_items(), das
 * Service-Schema steht in inc/org-schema.php, Title und Description in
 * inc/seo-meta.php.
 *
 * Primärer CTA: Projektanfrage mit Thema „Relaunch oder neue Website“
 * (/kontakt/?type=project&focus=relaunch), derselbe Fokus wie das Website-
 * Angebot der Startseite. Kein Marktcheck, keine Ersteinschätzung.
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
		$classes[] = 'hu-website-offer-page';
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
				<li><span aria-current="page">WordPress-Website erstellen lassen</span></li>
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
$contact_url    = hu_get_contact_intake_url( 'project', 'relaunch' );
$tracking_url   = $routes['tracking_setup'];
$landing_url    = $routes['landingpage'];
$whitelabel_url = $routes['whitelabel'];
$article_url    = home_url( '/website-relaunch/' );
$page_url       = get_permalink() ? get_permalink() : home_url( '/wordpress-website-erstellen-lassen/' );
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( $page_url );

$price           = $fest( hu_freelancer_website_price() );
$price_net       = $fest( hu_freelancer_website_price( true ) );
$pages_word      = hu_pricing_count_word( HU_FREELANCER_WEBSITE_PAGES );
$scope_display   = $fest( hu_freelancer_website_scope_display() );
$extra_page      = $fest( hu_freelancer_website_extra_page_price( true ) );
$response        = hu_response_promise( 'compact' );
$response_window = hu_response_promise( 'window' );
$tracking_offer  = hu_tracking_product_ladder()['measurement'];
$tracking_price  = $fest( $tracking_offer['price'] );
$landing_price   = $fest( hu_landingpage_price() );
$takeover_price  = $fest( hu_freelancer_takeover_check_price() );
$retainer        = $fest( hu_freelancer_retainer_display() );

$e3_canon   = hu_e3_canon();
$case_url   = $e3_canon['url'];
$case_label = $e3_canon['case_label_accusative'] ?? HU_E3_CASE_LABEL_ACCUSATIVE;
$cpl_before = hu_e3_metric( 'cpl_before' );
$cpl_after  = hu_e3_metric( 'cpl_after' );
$timeframe  = hu_e3_metric( 'timeframe', 'display_dative' );

$references = hu_public_reference_projects();
$faq_items  = nexus_get_website_faq_items();

// Was im Festpreis steckt. Reihenfolge = Reihenfolge der Arbeit.
$scope = [
	[ 'Struktur und Feinschliff', 'Ich lege fest, wie die Seiten aufgebaut sind: Angebot, Preis und Beleg in dieser Reihenfolge. Die Texte liefern Sie, Struktur und Feinschliff sind enthalten.' ],
	[ 'Umsetzung in WordPress', 'Seiten, die auf dem Handy schnell stehen, gebaut auf einer Testumgebung. Die Inhalte pflegt Ihr Team danach selbst im Editor.' ],
	[ 'Kontaktformular', 'So wenige Pflichtfelder wie möglich, Fehlermeldungen direkt am Feld und ein Versand, der über SPF und DKIM abgesichert ist. Die Danke-Seite sagt, was als Nächstes passiert.' ],
	[ 'Title, Canonical, Schema', 'Saubere Titel und Canonicals und strukturierte Daten, damit jede Seite für ihre eigene Suchanfrage steht.' ],
	[ 'Abnahme', 'Sie sehen die Website auf der Testumgebung, bevor sie jemand anderes sieht. Live geht sie erst nach Ihrer Abnahme.' ],
	[ 'Übergabe', 'Testumgebung, Code und Repository, Zugänge und Dokumentation liegen nach dem Projekt bei Ihnen.' ],
	[ 'Weitere Seiten', sprintf( 'Jede Seite über die %1$s hinaus kostet %2$s. Umfang und Endpreis stehen vor dem Start schriftlich fest.', $pages_word, $extra_page ) ],
];

// Was bewusst nicht dazugehört. Nur belegte Grenzen: Texte liefert der Kunde,
// Shop, Schnittstellen und große Relaunches werden separat kalkuliert.
$not_included = [
	'Texte (die liefern Sie)',
	'Shop',
	'Schnittstellen',
	'Relaunches mit vielen Seiten (separat kalkuliert)',
];

// Die Preiskarte setzt Betrag und Währung getrennt, damit das Zeichen leiser steht.
$price_parts  = preg_split( '/[\s\x{00A0}]+/u', $price, 2 );
$price_amount = $price_parts[0];
$price_unit   = $price_parts[1] ?? '';

// Kurzfassung in der Preiskarte; die Einzelheiten stehen im Raster darunter.
$price_facts = [
	sprintf( 'Bis zu %s Seiten mit Kontaktformular', $pages_word ),
	'Struktur und Feinschliff inklusive',
	'Testumgebung, live nach Ihrer Abnahme',
	sprintf( 'Jede weitere Seite %s', $extra_page ),
];

// Wann sich eine neue Website lohnt.
$occasions = [
	'Sie haben noch keine Website und brauchen einen Auftritt, der Anfragen annimmt.',
	'Die Seite braucht auf dem Handy mehrere Sekunden, und das Angebot steht erst nach einer langen Einleitung.',
	'Das Formular sendet, aber die Mail kommt nicht an.',
	'Ein Relaunch steht an, und Sie wollen nicht, dass alte Links und Suchergebnisse ins Leere laufen.',
];

// Anlass 04 führt zum Relaunch-Block auf dieser Seite.
$occasion_links = [
	3 => '#relaunch',
];

// Ablauf in vier Stationen; Umfang, Endpreis und Zeitrahmen stehen vor dem Start fest.
$process = [
	[ '01 · Anfrage', 'Sie beschreiben das Vorhaben. Umfang und Endpreis stehen danach schriftlich fest, den Zeitrahmen nenne ich mit dem Angebot.' ],
	[ '02 · Texte und Freigaben', 'Sie liefern die Texte. Im Angebot steht, wann ich Texte, Bilder und Freigaben von Ihnen brauche.' ],
	[ '03 · Umsetzung', 'Ich baue auf einer Testumgebung. Sie sehen die Website, bevor sie jemand anderes sieht.' ],
	[ '04 · Abnahme', 'Live geht die Website nach Ihrer Abnahme. Danach liegen Testumgebung, Code, Zugänge und Dokumentation bei Ihnen.' ],
];

// Relaunch-Block: was bei einer bestehenden Website zusätzlich geschieht.
$relaunch = [
	[ 'Weiterleitungsplan', 'Jede alte URL bekommt eine Weiterleitung, damit bestehende Links und Suchergebnisse weiter ankommen.' ],
	[ 'Testumgebung', 'Die neue Website entsteht auf einer Testumgebung und geht erst nach Ihrer Abnahme live.' ],
	[ 'Title, Canonical, Schema', 'Eine Seite pro Suchanfrage, saubere Titel und Canonicals, strukturierte Daten. Zwei Unterseiten, die um dieselbe Suchanfrage konkurrieren, bringen keine nach vorn.' ],
	[ 'Umfang', sprintf( 'Der Festpreis gilt für bis zu %s Seiten. Relaunches mit vielen Seiten kalkuliere ich separat.', $pages_word ) ],
];

get_header();
?>

<div id="website-content" class="doku lp-offer-page website-offer-page" data-track-page="website_offer">
	<header class="kopfteil" data-track-section="website_offer_hero">
		<div class="blatt">
			<p class="gegenstand">Website · WordPress · Festpreis</p>
			<h1><?php echo esc_html( sprintf( 'WordPress-Website erstellen lassen: bis zu %s Seiten zum Festpreis.', $pages_word ) ); ?></h1>
			<p class="aufriss">
				<span class="erst">Neubau oder Relaunch, mit Kontaktformular.</span>
				Ich baue die Website auf einer Testumgebung, und sie geht erst nach Ihrer Abnahme live. Beim Relaunch bekommt jede alte URL eine Weiterleitung.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_website_offer_hero_project" data-track-category="lead_gen" data-track-section="website_offer_hero">Website-Projekt anfragen <span class="pf" aria-hidden="true">→</span></a>
				<a class="tun still" href="#umfang" data-track-action="website_offer_hero_to_scope" data-track-category="navigation" data-track-section="website_offer_hero">Was im Preis steckt</a>
			</div>
			<p class="mono"><?php echo esc_html( 'Ab ' . $price_net ); ?> · <?php echo esc_html( $response ); ?></p>

			<div class="meta" aria-label="Angebot im Überblick">
				<dl>
					<div><dt>Preis</dt><dd><?php echo esc_html( 'Ab ' . $price_net ); ?>, Umfang und Endpreis stehen vor dem Start schriftlich fest</dd></div>
					<div><dt>Umfang</dt><dd><?php echo esc_html( $scope_display ); ?></dd></div>
					<div><dt>Abnahme</dt><dd>Live erst nach Ihrer Abnahme auf der Testumgebung</dd></div>
					<div><dt>Für Agenturen</dt><dd><a class="satzlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="website_offer_hero_whitelabel" data-track-category="navigation" data-track-section="website_offer_hero">Umsetzung unter Ihrem Namen</a></dd></div>
				</dl>
			</div>
		</div>
	</header>

	<section id="anlass" data-track-section="website_offer_occasion">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Anlass</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Wann sich eine neue Website lohnt</p>
				<h2 class="kopf">Eine Website muss gefunden werden, schnell stehen und Anfragen weitergeben.</h2>
				<p class="vorspann">Wo eines davon bricht, liegt der Anlass für einen Neubau oder einen Relaunch.</p>
				<div class="protokoll posten" aria-label="Typische Anlässe">
					<?php foreach ( $occasions as $i => $occasion ) : ?>
						<div class="z"><span><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><b>
							<?php if ( isset( $occasion_links[ $i ] ) ) : ?>
								<a class="satzlink" href="<?php echo esc_url( $occasion_links[ $i ] ); ?>" data-track-action="website_offer_occasion_to_relaunch" data-track-category="navigation" data-track-section="website_offer_occasion"><?php echo esc_html( $occasion ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $occasion ); ?>
							<?php endif; ?>
						</b></div>
					<?php endforeach; ?>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Neubau oder Relaunch</span><b>Beides ist möglich.</b> Bei einem Relaunch kommt der Weiterleitungsplan für jede alte URL dazu.</p></aside>
		</div>
	</section>

	<section id="umfang" data-track-section="website_offer_scope">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Umfang</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel angebot">
					<div class="angebot-kopf">
						<div class="angebot-titel">
							<p class="mono stempelfarbe">Angebot im Detail</p>
							<h2 class="kopf">Das steckt im Festpreis.</h2>
							<p class="vorspann">Der Umfang steht, bevor ich anfange. Er gilt für eine kleine Website mit Kontaktformular. Was darüber hinausgeht, wird einzeln berechnet.</p>
						</div>
						<div class="angebot-preis" role="group" aria-label="Festpreis">
							<p class="mono">Festpreis ab</p>
							<p class="betrag"><span class="summe"><?php echo esc_html( $price_amount ); ?></span><?php if ( '' !== $price_unit ) : ?><span class="einheit"><?php echo esc_html( $price_unit ); ?></span><?php endif; ?></p>
							<p class="netto">netto, Umfang und Endpreis stehen vor dem Start schriftlich fest</p>
							<ul class="haken" role="list">
								<?php foreach ( $price_facts as $fact ) : ?>
									<li><?php echo esc_html( $fact ); ?></li>
								<?php endforeach; ?>
							</ul>
							<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_website_offer_scope_project" data-track-category="lead_gen" data-track-section="website_offer_scope">Website-Projekt anfragen <span class="pf" aria-hidden="true">→</span></a>
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

	<section id="ablauf" data-track-section="website_offer_process">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Ablauf</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Vier Schritte</p>
				<h2 class="kopf">Live geht die Website erst nach Ihrer Abnahme.</h2>
				<p class="vorspann">Bis dahin arbeite ich auf einer Testumgebung. Den Zeitrahmen nenne ich mit dem Angebot; er hängt vor allem daran, wie schnell Texte, Bilder und Freigaben kommen.</p>
				<div class="protokoll posten" aria-label="Ablauf">
					<?php foreach ( $process as $step ) : ?>
						<div class="z"><span><?php echo esc_html( $step[0] ); ?></span><b><?php echo esc_html( $step[1] ); ?></b></div>
					<?php endforeach; ?>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Ihre Konten</span>Domain, Hosting, Konten und Repository laufen auf Ihren Namen. Auf Ihrer Seite braucht es eine Person, die entscheidet und freigibt.</p></aside>
		</div>
	</section>

	<section id="relaunch" data-track-section="website_offer_relaunch">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Relaunch</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Wenn schon eine Website besteht</p>
				<h2 class="kopf">Beim Relaunch bekommt jede alte URL eine Weiterleitung.</h2>
				<p class="vorspann">Nach einem Relaunch ohne Weiterleitungen laufen alte Links und Suchergebnisse ins Leere. Deshalb gehört der Weiterleitungsplan zum Relaunch, nicht zu den Extras.</p>
				<div class="protokoll posten" aria-label="Relaunch im Überblick">
					<?php foreach ( $relaunch as $item ) : ?>
						<div class="z"><span><?php echo esc_html( $item[0] ); ?></span><b><?php echo esc_html( $item[1] ); ?></b></div>
					<?php endforeach; ?>
				</div>
				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $article_url ); ?>" data-track-action="website_offer_relaunch_to_article" data-track-category="navigation" data-track-section="website_offer_relaunch">Beitrag zum Website-Relaunch</a>
				</div>
			</div>
		</div>
	</section>

	<section id="zusaetze" data-track-section="website_offer_addons">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Zusätze</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Wenn mehr gebraucht wird</p>
				<h2 class="kopf">Was darüber hinausgeht, hat einen eigenen Preis.</h2>
				<p class="vorspann">So bleibt die Website ein Festpreis, und Sie buchen nur, was Ihr Vorhaben tatsächlich braucht.</p>
				<div class="protokoll posten" aria-label="Buchbare Zusätze">
					<div class="z"><span><?php echo esc_html( $tracking_offer['name'] ); ?></span><b><?php echo esc_html( sprintf( 'Festpreis %s netto: Messplan, GA4, Tag Manager, Consent Mode und Google Ads, mit Abnahmeprotokoll.', $tracking_price ) ); ?></b></div>
					<div class="z"><span>Landingpage</span><b><?php echo esc_html( sprintf( 'Festpreis %s netto: eine Seite für ein Angebot, mit Text, Anfrageformular und Herkunft jeder Anfrage.', $landing_price ) ); ?></b></div>
					<div class="z"><span>Übernahme-Check</span><b><?php echo esc_html( sprintf( '%s netto: schriftlicher Befund zu Theme, Plugins, Updates, Backups, Zugängen und Ladezeit, mit Festpreis für den nächsten Schritt. Bei Beauftragung wird der Check verrechnet.', $takeover_price ) ); ?></b></div>
					<div class="z"><span>Weiterentwicklung</span><b><?php echo esc_html( sprintf( 'Monatskontingent: %s, monatlich kündbar.', $retainer ) ); ?></b></div>
				</div>
				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="website_offer_to_tracking_setup" data-track-category="navigation" data-track-section="website_offer_addons">Tracking-Setup im Detail</a>
					<a class="textlink" href="<?php echo esc_url( $landing_url ); ?>" data-track-action="website_offer_to_landingpage" data-track-category="navigation" data-track-section="website_offer_addons">Landingpage zum Festpreis</a>
				</div>
			</div>
		</div>
	</section>

	<section id="beleg" data-track-section="website_offer_proof">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">06</span><span class="titel">Beleg</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Dokumentierter Fall</p>
				<h2 class="kopf">Eigene Anfragen statt gekaufter: ein Fall mit offener Herleitung.</h2>
				<p class="vorspann">
					<?php
					echo esc_html(
						sprintf(
							'Für einen %1$s sanken die Kosten pro qualifizierter Anfrage in %2$s von %3$s auf %4$s. Gebaut habe ich die ganze Strecke: Website und Landingpages, Vorqualifizierung im Formular, Server-Side Tracking und die Übergabe jeder Anfrage an den Vertrieb.',
							$case_label,
							$timeframe,
							$cpl_before,
							$cpl_after
						)
					);
					?>
				</p>
				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $case_url ); ?>" data-track-action="website_offer_proof_case" data-track-category="proof" data-track-section="website_offer_proof">Fallstudie und Herleitung ansehen</a>
					<a class="textlink" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="website_offer_proof_pagespeed" data-track-category="proof" data-track-section="website_offer_proof">Ladezeit dieser Seite messen<span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
				</div>
				<?php if ( ! empty( $references ) ) : ?>
					<div class="protokoll posten" aria-label="Öffentliche Arbeiten">
						<?php foreach ( $references as $reference ) : ?>
							<div class="z"><span><?php echo esc_html( $reference['tag'] ); ?></span><b><a class="satzlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="website_offer_reference_open" data-track-category="proof" data-track-section="website_offer_proof"><?php echo esc_html( $reference['name'] ); ?><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>: <?php echo esc_html( $reference['text'] ); ?></b></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<aside class="marg"><p class="note"><span class="label">Übertragbar?</span>Das hängt an Markt, Angebot und Wettbewerb. Die Fallstudie legt die Herleitung offen, statt die Zahl allein zu zeigen.</p></aside>
		</div>
	</section>

	<?php if ( ! empty( $faq_items ) ) : ?>
		<section id="fragen" data-track-section="website_offer_faq">
			<div class="blatt reihe">
				<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">07</span><span class="titel">Fragen</span><span class="strich"></span></div></div>
				<div class="haupt">
					<p class="mono stempelfarbe">Vor der Beauftragung</p>
					<h2 class="kopf leise">Häufige Fragen zur Website.</h2>
					<div class="fragen">
						<?php // Dieselbe Quelle wie das FAQPage-Schema in inc/org-schema.php. ?>
						<?php foreach ( $faq_items as $item ) : ?>
							<details name="website-offer-faq"><summary data-track-action="faq_website_offer_open" data-track-label="<?php echo esc_attr( $item['key'] ); ?>" data-track-category="engagement" data-track-section="website_offer_faq"><?php echo esc_html( $item['question'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $item['answer'] ); ?></p></div></div></details>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="abschluss" id="anfrage" data-track-section="website_offer_close">
		<div class="blatt reihe"><div class="ganz"><div class="tafel"><div class="reihe">
			<div class="haupt">
				<p class="mono stempelfarbe">Nächster Schritt</p>
				<h2>Neubau oder Relaunch: was soll die Website können?</h2>
				<p class="aufriss">Schreiben Sie kurz, ob es ein Neubau oder ein Relaunch ist, welche Seiten Sie brauchen und bis wann die Website stehen soll. Sie bekommen Rückfragen oder ein Angebot.</p>
				<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_website_offer_close_project" data-track-category="lead_gen" data-track-section="website_offer_close">Website-Projekt anfragen <span class="pf" aria-hidden="true">→</span></a></div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Antwort</span>Persönlich <?php echo esc_html( $response_window ); ?>, mit Rückfragen oder einem Angebot.</p></aside>
		</div></div></div></div>
	</section>
</div>

<?php
get_footer();
