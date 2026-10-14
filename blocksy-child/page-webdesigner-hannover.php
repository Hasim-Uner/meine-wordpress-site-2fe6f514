<?php
/**
 * Template Name: Webdesigner Hannover
 * Description: Lokaler Einstieg für Website-Projekte in Hannover und der Region.
 *
 * Query-Owner für „webdesigner hannover“ und „webdesign hannover“
 * (docs/seo/query-ownership.csv, Entscheidung 2026-10-14). Die Seite ist der
 * lokale Einstieg; Preisformel, Rechner und Produktdetails besitzt
 * /wordpress-website-erstellen-lassen/. Den Agentur-Intent besitzt weiter
 * /wordpress-agentur-hannover/, deshalb stehen dort keine Webdesign-Begriffe
 * (BRAND_AND_COPY.md, Route WordPress Agentur Hannover).
 *
 * Abgrenzung aus der Wettbewerbsanalyse vom 2026-10-14: echter Sitz in der
 * Region statt Stadtseite, Festpreis vor dem Gespräch, keine Monatsbindung,
 * alles auf den Namen des Kunden, geprüfter Anfrageweg.
 *
 * Gutachten-System aus system.css; Preiskarte, Liefergruppen und Details
 * nutzen das Delta aus landingpage-offer.css (Klasse lp-offer-page). Fakten
 * kommen aus dem Kanon: Preise aus pricing-canon.php, Antwortzeit aus
 * hu_response_promise(), Referenzen aus hu_public_reference_projects().
 * Fragen und FAQPage-Schema lesen nexus_get_webdesigner_hannover_faq_items(),
 * das Service-Schema steht in inc/org-schema.php, Title und Description in
 * inc/seo-meta.php.
 *
 * Primärer CTA: Projektanfrage mit Thema „website“
 * (/kontakt/?type=project&focus=website).
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
		$classes[] = 'hu-webdesigner-hannover-page';
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
				<li><span aria-current="page">Webdesigner Hannover</span></li>
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
$contact_url     = add_query_arg( [ 'type' => 'project', 'focus' => 'website' ], home_url( '/kontakt/' ) );
$website_url     = $routes['website'];
$landingpage_url = $routes['landingpage'];
$agentur_url     = $routes['agentur_local'];
$page_url        = get_permalink() ? get_permalink() : home_url( '/webdesigner-hannover/' );
$psi_url         = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( $page_url );

$price           = $fest( hu_freelancer_website_price() );
$page_price      = $fest( hu_format_eur( HU_WEBSITE_PAGE_UTILITY ) );
$response        = hu_response_promise( 'compact' );
$response_window = hu_response_promise( 'window' );

$references = hu_public_reference_projects();
$faq_items  = nexus_get_webdesigner_hannover_faq_items();

// Woran sich die Seite von den Treffern zu „Webdesigner Hannover“ unterscheidet.
$difference = [
	[ 'Vor Ort', 'Sitz in Pattensen in der Region Hannover. Erstgespräch und Abnahme gern bei Ihnen im Unternehmen.' ],
	[ 'Festpreis', sprintf( 'Den Preis rechnen Sie vor dem ersten Gespräch selbst aus: ab %s netto für Grundsystem und erste Hauptseite.', $price ) ],
	[ 'Ohne Bindung', 'Kein Pflicht-Wartungsvertrag und kein Umzug zu meinem Hoster. Domain, Hosting, Code und Zugänge laufen auf Ihren Namen.' ],
	[ 'Für Anfragen', 'Formular, Bestätigung und Danke-Seite gehören dazu. Vor dem Livegang verfolgen wir eine Testanfrage bis in Ihr Postfach.' ],
	[ 'Nachprüfbar', 'Ladezeit, Suchmaschinen-Grundlagen und Anfrageweg prüfen wir vor der Abnahme gemeinsam, nicht erst danach.' ],
];

// Liefergruppen der Anfrage-Website, lokal erzählt; Einzelpreise besitzt die Produktseite.
$scope = [
	[
		'title'   => 'Gestaltung für Ihr Unternehmen',
		'summary' => 'Responsive Layouts, Typografie und Farben passend zu Ihrem Auftritt, für Handy und Desktop.',
		'label'   => 'Basisdesign oder eigenes Screendesign',
		'detail'  => 'Im Grundpreis passe ich bewährte Layouts an Ihr Unternehmen an. Ein individuelles Screendesign ist ein eigener Baustein, den der Rechner ausweist.',
	],
	[
		'title'   => 'WordPress, das Sie selbst pflegen',
		'summary' => 'Eigener Code ohne Pagebuilder. Texte und Bilder ändern Sie im WordPress-Editor.',
		'label'   => 'Warum ohne Pagebuilder',
		'detail'  => 'Pagebuilder binden Inhalte an ein Plugin und laden viel Code, den die Seite nicht braucht. Ohne ihn bleibt die Website schneller und Sie bleiben unabhängig.',
	],
	[
		'title'   => 'Ein Anfrageweg, der getestet ist',
		'summary' => 'Kontaktformular, Bestätigungsmail und Danke-Seite. Jede Anfrage landet geprüft in Ihrem Postfach.',
		'label'   => 'Was vor dem Livegang geprüft wird',
		'detail'  => 'Wir senden eine Testanfrage und verfolgen sie bis in Ihr Postfach. Conversion-Tracking in GA4 oder Google Ads ist ein optionaler Zusatz.',
	],
	[
		'title'   => 'Gefunden werden',
		'summary' => 'Technisches SEO und On-Page: Seitentitel, Beschreibungen, strukturierte Daten und kurze Ladezeiten.',
		'label'   => 'Bei einem Relaunch',
		'detail'  => 'Ich prüfe vorher Bestand und Adressen und lege einen Weiterleitungsplan an, damit vorhandene Sichtbarkeit nicht verloren geht.',
	],
	[
		'title'   => 'Abnahme und Übergabe',
		'summary' => 'Impressum- und Datenschutzseite mit Ihren Rechtstexten, Grundlagen der Barrierefreiheit, Abnahme vor dem Livegang.',
		'label'   => 'Was Sie danach behalten',
		'detail'  => 'Die Website läuft auf Ihrem Hosting, Domain und Zugänge bleiben bei Ihnen, und Sie erhalten den Projektcode. Rechtstexte erstelle ich nicht; Sie liefern sie.',
	],
];

$price_parts  = preg_split( '/[\s\x{00A0}]+/u', $price, 2 );
$price_amount = $price_parts[0];
$price_unit   = $price_parts[1] ?? '';

$price_facts = [
	'Grundsystem und erste Hauptseite',
	sprintf( 'Weitere Seiten ab %s netto', $page_price ),
	'Kontaktformular und technisches SEO',
	'Alles auf Ihren Namen',
];

$process = [
	[ '01 · Erstgespräch', 'Bei Ihnen vor Ort in der Region Hannover oder per Video. Wir klären Ziel, Seiten und Material.' ],
	[ '02 · Festpreis', 'Umfang, Preis und Termin stehen vor dem Auftrag schriftlich fest.' ],
	[ '03 · Umsetzung', 'Ich baue auf einer Testumgebung. Sie sehen jede Seite, bevor sie öffentlich ist.' ],
	[ '04 · Abnahme', 'Testanfrage, Ladezeit, Korrekturen. Live geht die Website nach Ihrer Freigabe.' ],
];

// Orte für persönliche Termine; die Umsetzung läuft auch außerhalb remote.
$region = [ 'Hannover', 'Laatzen', 'Hemmingen', 'Pattensen', 'Springe', 'Ronnenberg', 'Gehrden', 'Langenhagen', 'Garbsen', 'Hildesheim' ];

get_header();
?>

<div id="webdesigner-hannover-content" class="doku lp-offer-page wd-hannover-page" data-track-page="webdesigner_hannover">
	<header class="kopfteil" data-track-section="wd_hannover_hero">
		<div class="blatt">
			<p class="gegenstand">Webdesign · Hannover · WordPress</p>
			<h1>Webdesigner für Hannover: Ihre Website zum Festpreis, gebaut für Anfragen.</h1>
			<p class="aufriss">
				<span class="erst">Eine Person vom ersten Gespräch bis zur Abnahme.</span>
				Ich gestalte und baue WordPress-Websites für Unternehmen in Hannover und der Region. Den Preis kennen Sie vor dem Gespräch, der Anfrageweg ist vor dem Livegang getestet, und Domain, Hosting und Code laufen auf Ihren Namen.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_wd_hannover_hero_project" data-track-category="lead_gen" data-track-section="wd_hannover_hero">Website-Projekt anfragen <span class="pf" aria-hidden="true">→</span></a>
				<a class="tun still" href="<?php echo esc_url( $website_url ); ?>" data-track-action="wd_hannover_hero_to_calculator" data-track-category="navigation" data-track-section="wd_hannover_hero">Festpreis ausrechnen</a>
			</div>
			<p class="mono">ab <?php echo esc_html( $price ); ?> netto Festpreis · Sitz in Pattensen bei Hannover · <?php echo esc_html( $response ); ?></p>
		</div>
	</header>

	<section id="unterschied" data-track-section="wd_hannover_difference">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Unterschied</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Bevor Sie vergleichen</p>
				<h2 class="kopf">Ein Webdesigner aus der Region, mit Preis vor dem Gespräch.</h2>
				<p class="vorspann">Viele Treffer zu „Webdesigner Hannover“ sind Stadtseiten von Büros aus anderen Städten, oft mit Preis erst nach dem Termin und monatlicher Pflichtgebühr. Bei mir sind diese Punkte vorher geklärt.</p>
				<div class="protokoll posten" aria-label="Was Sie erwarten können">
					<?php foreach ( $difference as $row ) : ?>
						<div class="z"><span><?php echo esc_html( $row[0] ); ?></span><b><?php echo esc_html( $row[1] ); ?></b></div>
					<?php endforeach; ?>
				</div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Einordnung</span>Ich bin keine Agentur mit Team, sondern setze selbst um. Wenn Sie zwischen beidem abwägen: <a class="satzlink" href="<?php echo esc_url( $agentur_url ); ?>" data-track-action="wd_hannover_to_agentur" data-track-category="navigation" data-track-section="wd_hannover_difference">Agentur oder direkte Umsetzung?</a></p></aside>
		</div>
	</section>

	<section id="umfang" data-track-section="wd_hannover_scope">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Umfang</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel angebot">
					<div class="angebot-kopf">
						<div class="angebot-titel">
							<p class="mono stempelfarbe">Die Anfrage-Website · fünf Liefergruppen</p>
							<h2 class="kopf">Was in Ihrer neuen Website steckt.</h2>
							<p class="vorspann">Gestaltung, Technik, Sichtbarkeit und Anfrageweg gehören zusammen. Wie viele Seiten Sie brauchen und ob ich die Texte schreibe, rechnen Sie im Rechner aus; der Festpreis steht vor dem Start schriftlich fest.</p>
						</div>
						<div class="angebot-preis" role="group" aria-label="Festpreis">
							<p class="mono">Festpreis ab</p>
							<p class="betrag"><span class="summe"><?php echo esc_html( $price_amount ); ?></span><?php if ( '' !== $price_unit ) : ?><span class="einheit"><?php echo esc_html( $price_unit ); ?></span><?php endif; ?></p>
							<p class="netto">netto, fest vereinbart vor dem Start</p>
							<ul class="haken" role="list">
								<?php foreach ( $price_facts as $fact ) : ?>
									<li><?php echo esc_html( $fact ); ?></li>
								<?php endforeach; ?>
							</ul>
							<a class="tun" href="<?php echo esc_url( $website_url ); ?>" data-track-action="wd_hannover_scope_to_calculator" data-track-category="navigation" data-track-section="wd_hannover_scope">Festpreis ausrechnen <span class="pf" aria-hidden="true">→</span></a>
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

	<section id="ablauf" data-track-section="wd_hannover_process">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Ablauf</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Vier Schritte</p>
				<h2 class="kopf">Vom Erstgespräch zur fertigen Website.</h2>
				<p class="vorspann">Sie liefern Inhalte, Bilder und Freigaben. Wie schnell sie da sind, bestimmt den Livegang mehr als alles andere; den Termin legen wir nach dem Erstgespräch schriftlich fest.</p>
				<div class="protokoll posten" aria-label="Ablauf">
					<?php foreach ( $process as $step ) : ?>
						<div class="z"><span><?php echo esc_html( $step[0] ); ?></span><b><?php echo esc_html( $step[1] ); ?></b></div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<section id="beleg" data-track-section="wd_hannover_proof">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Beleg</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Selbst nachprüfen</p>
				<h2 class="kopf">Prüfen Sie die Arbeit, nicht das Versprechen.</h2>
				<p class="vorspann">Messen Sie die Ladezeit dieser Seite mit dem Werkzeug von Google, und sehen Sie sich öffentliche Arbeiten an, die Sie unter ihrer Adresse aufrufen können.</p>
				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="wd_hannover_proof_pagespeed" data-track-category="proof" data-track-section="wd_hannover_proof">Ladezeit dieser Seite messen<span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
				</div>
				<?php if ( ! empty( $references ) ) : ?>
					<div class="protokoll posten" aria-label="Öffentliche Arbeiten">
						<?php foreach ( $references as $reference ) : ?>
							<div class="z"><span><?php echo esc_html( $reference['tag'] ); ?></span><b><a class="satzlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="wd_hannover_reference_open" data-track-category="proof" data-track-section="wd_hannover_proof"><?php echo esc_html( $reference['name'] ); ?><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>: <?php echo esc_html( $reference['text'] ); ?></b></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section id="region" data-track-section="wd_hannover_region">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Region</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Hannover und Umgebung</p>
				<h2 class="kopf">Persönlich in der Region, digital ohne Umweg.</h2>
				<p class="vorspann">Mein Sitz ist Pattensen, südlich von Hannover. Persönliche Termine biete ich in Hannover und der Region an, unter anderem in <?php echo esc_html( implode( ', ', array_slice( $region, 0, -1 ) ) . ' und ' . end( $region ) ); ?>. Zwischen den Terminen läuft die Arbeit per Video und E-Mail, damit Sie keine Wege haben. Projekte außerhalb der Region setze ich ebenso um, dann vollständig remote.</p>
				<div class="lp-vertiefungen">
					<details class="lp-details">
						<summary>Nur eine Seite für ein einzelnes Angebot?</summary>
						<p>Dann ist eine Landingpage der passende Einstieg: eine Seite mit Text, Formular und Messung zum Festpreis. <a class="satzlink" href="<?php echo esc_url( $landingpage_url ); ?>" data-track-action="wd_hannover_to_landingpage" data-track-category="navigation" data-track-section="wd_hannover_region">Landingpage erstellen lassen</a></p>
					</details>
				</div>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $faq_items ) ) : ?>
		<section id="fragen" data-track-section="wd_hannover_faq">
			<div class="blatt reihe">
				<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">06</span><span class="titel">Fragen</span><span class="strich"></span></div></div>
				<div class="haupt">
					<p class="mono stempelfarbe">Vor der Beauftragung</p>
					<h2 class="kopf leise">Häufige Fragen an einen Webdesigner in Hannover.</h2>
					<div class="fragen">
						<?php // Dieselbe Quelle wie das FAQPage-Schema in inc/org-schema.php. ?>
						<?php foreach ( $faq_items as $item ) : ?>
							<details name="wd-hannover-faq"><summary data-track-action="faq_wd_hannover_open" data-track-label="<?php echo esc_attr( $item['key'] ); ?>" data-track-category="engagement" data-track-section="wd_hannover_faq"><?php echo esc_html( $item['question'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $item['answer'] ); ?></p></div></div></details>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="abschluss" id="anfrage" data-track-section="wd_hannover_close">
		<div class="blatt reihe"><div class="ganz"><div class="tafel"><div class="reihe">
			<div class="haupt">
				<p class="mono stempelfarbe">Nächster Schritt</p>
				<h2>Was soll Ihre neue Website leisten?</h2>
				<p class="aufriss">Nennen Sie Ihr Unternehmen, ob es um einen Neubau oder Relaunch geht, und Ihren Wunschtermin. Das Thema Website ist im Formular bereits gewählt. Ich antworte mit Rückfragen oder einem Terminvorschlag, gern für ein Gespräch bei Ihnen vor Ort.</p>
				<p class="lp-kurzinfo">Umfang, Festpreis und Termin stehen vor dem Auftrag schriftlich fest. Ihre Anfrage ist noch keine Beauftragung.</p>
				<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_wd_hannover_close_project" data-track-category="lead_gen" data-track-section="wd_hannover_close">Website-Projekt anfragen <span class="pf" aria-hidden="true">→</span></a></div>
			</div>
			<aside class="marg"><p class="note"><span class="label">Antwort</span>Persönlich <?php echo esc_html( $response_window ); ?>, mit Rückfragen oder einem Terminvorschlag.</p></aside>
		</div></div></div></div>
	</section>
</div>

<?php
get_footer();
