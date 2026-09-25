<?php
/**
 * Template Name: Whitelabel & Weiterentwicklung
 * Description: White-Label WordPress und Messung für Agenturen. Klare
 *              Einstiegsaufgabe, nachvollziehbare Abnahme, Weiterentwicklung
 *              nach dem Erstprojekt.
 *
 * Die Seite steht auf dem System der Startseite (startseite-strecke.css/.js):
 * Messlinie in der Randspalte, Marken 01 bis 08, Prüfstand als einzige
 * dunkle Tafel, Haarlinien, dieselbe Typo-Skala. Die Linie läuft hier durch
 * den Ablauf eines Auftrags (Abschnitt 05: Aufgabe, Umfang, Umsetzung,
 * Abnahme, Übergabe) und endet am Formular. whitelabel.css ist nur das
 * Delta dazu.
 *
 * Im Hero steht ein Abnahmeprotokoll als Muster. Ohne JavaScript sind alle
 * Punkte abgehakt; mit JavaScript hakt whitelabel.js sie ab, sobald die
 * Leselinie die zugehörige Station in Abschnitt 05 erreicht.
 *
 * Fakten (Preise, Antwortzeit, Kontakt, Referenzen) kommen aus inc/canon/.
 * Formularfelder, case-Parameter, Anker #aufgabe, Cal.com-Link und alle
 * data-track-Werte der Vorgängerfassung bleiben erhalten.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$whitelabel_fit_url = function_exists( 'nexus_get_whitelabel_calendar_url' )
	? nexus_get_whitelabel_calendar_url()
	: 'https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true';

// Bestehenden Anfrageweg und die beiden Posteingangs-Kontexte erhalten.
$wl_page_url = get_permalink();
if ( ! $wl_page_url ) {
	$wl_page_url = function_exists( 'nexus_get_whitelabel_page_url' )
		? nexus_get_whitelabel_page_url()
		: home_url( '/whitelabel-retainer/' );
}

$wl_form_anchor = '#aufgabe';
$wl_form_url    = static function ( $case ) use ( $wl_page_url, $wl_form_anchor ) {
	return add_query_arg(
		[
			'type' => 'whitelabel',
			'case' => $case,
		],
		$wl_page_url
	) . $wl_form_anchor;
};
$wl_form_task_url  = $wl_form_url( 'aufgabe' );
$wl_form_offer_url = $wl_form_url( 'angebotsphase' );
$wl_form_endpoint  = rest_url( 'nexus/v1/whitelabel-request' );

$imprint_url  = home_url( '/impressum/' );
$privacy_url  = home_url( '/datenschutz/' );
$current_year = wp_date( 'Y' );

$wl_routes     = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$wl_home_url   = $wl_routes['home'] ?? home_url( '/' );
$wl_brand_text = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$wl_home_label = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$wl_brand_text
);

/*
 * Reduzierte Seitennavigation: Die Seite ist die Landeseite der
 * Akquise-Mails. Gebaut aus der .leiste des Systems, ohne Klappblatt;
 * schmal bleiben Wortmarke und Button. Die Anker markiert whitelabel.js,
 * solange ihr Abschnitt im Blick ist (aria-current="location").
 */
$wl_nav = [
	[ '#lieferfelder', 'Leistungen', 'nav_whitelabel_services' ],
	[ '#einstieg', 'Preise', 'nav_whitelabel_pricing' ],
	[ '#zusammenarbeit', 'Arbeitsweise', 'nav_whitelabel_process' ],
	[ '#proof', 'Referenzen', 'nav_whitelabel_proof' ],
	[ '#faq', 'Fragen', 'nav_whitelabel_faq' ],
];

remove_action( 'wp_body_open', 'nexus_render_site_header', 20 );
add_action(
	'wp_body_open',
	static function () use ( $wl_home_url, $wl_brand_text, $wl_home_label, $wl_form_task_url, $wl_nav ) {
		?>
		<header class="leiste wl-site-header" role="banner" data-track-section="whitelabel_header">
			<div class="blatt in">
				<a
					class="sig site-logo"
					href="<?php echo esc_url( $wl_home_url ); ?>"
					rel="home"
					aria-label="<?php echo esc_attr( $wl_home_label ); ?>"
					data-track-action="nav_whitelabel_home"
					data-track-category="navigation"
					data-track-section="whitelabel_header"
				><?php echo esc_html( $wl_brand_text ); ?><i aria-hidden="true">.</i></a>
				<div class="rechts">
					<nav aria-label="Navigation auf dieser Seite" data-wl-anker>
						<?php foreach ( $wl_nav as $wl_nav_item ) : ?>
							<a href="<?php echo esc_attr( $wl_nav_item[0] ); ?>" data-track-action="<?php echo esc_attr( $wl_nav_item[2] ); ?>" data-track-category="navigation" data-track-section="whitelabel_header"><?php echo esc_html( $wl_nav_item[1] ); ?></a>
						<?php endforeach; ?>
					</nav>
					<a class="tun" href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_header_task_brief" data-track-category="lead_gen" data-track-section="whitelabel_header">Aufgabe beschreiben</a>
				</div>
			</div>
		</header>
		<?php
	},
	20
);

get_header();

$fest = static function ( $value ) {
	// Betrag und Einheit bleiben in einer Zeile.
	return str_replace( [ ' €', ' %' ], [ "\u{00A0}€", "\u{00A0}%" ], (string) $value );
};
$liste = static function ( $value ) {
	// Der Trennpunkt hängt am vorigen Wort, damit keine Zeile mit „·“ beginnt.
	return str_replace( ' · ', "\u{00A0}· ", (string) $value );
};

$response_short    = hu_response_promise_short();
$response_sentence = hu_response_promise( 'sentence' );
$contact_email     = hu_get_contact_email();
$about_url         = $wl_routes['about'] ?? home_url( '/hasim-uener/' );
$tracking_b2b_url  = ( $wl_routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' ) ) . '#pakete';
$img_uri           = get_stylesheet_directory_uri() . '/assets/img/';
$github_url        = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$psi_url           = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( $wl_page_url );

$test_sprint_price = $fest( hu_whitelabel_price( 'test_sprint', 'display_fixed', hu_whitelabel_price( 'test_sprint' ) ) );
$retainer_price    = $fest( hu_whitelabel_price( 'retainer', 'display_hours_plain', hu_whitelabel_price( 'retainer' ) ) );

$faq_items           = nexus_get_whitelabel_faq_items();
$wl_access_options   = hu_whitelabel_request_access_options();
$wl_referral_options = function_exists( 'nexus_get_inquiry_referral_options' ) ? nexus_get_inquiry_referral_options() : [];

$references     = hu_public_reference_projects();
$reference_n    = count( $references );
$count_words    = [ 1 => 'diese eine Website', 2 => 'diese zwei Websites', 3 => 'diese drei Websites', 4 => 'diese vier Websites', 5 => 'diese fünf Websites' ];
$reference_word = $count_words[ $reference_n ] ?? sprintf( 'diese %d Websites', $reference_n );

/*
 * Abnahmeprotokoll im Hero. Jeder Punkt nennt die Station in Abschnitt 05,
 * bei der er sich abhakt (data-wl-punkt ↔ data-wl-haken).
 */
$protocol = [
	[ 'umfang', 'Aufgabe & Umfang', 'Mit Festpreis und Abnahmekriterien, vor dem Start.' ],
	[ 'staging', 'Umsetzung auf Staging', 'Euer Kunde sieht erst den abgenommenen Stand.' ],
	[ 'test', 'Funktionstest', 'Formular, Events und Conversions mit Testfällen geprüft.' ],
	[ 'doku', 'Dokumentation', 'Aufbau, Änderungen und offene Punkte für euer Team.' ],
	[ 'zugaenge', 'Zugänge in euren Accounts', 'Ihr vergebt sie und entzieht sie, wann ihr wollt.' ],
];

// Drei Arten von Aufgaben. Zu jeder steht, was die Agentur am Ende abnimmt.
$services = [
	[
		'tags'   => 'WordPress · Templates · Landingpages · Ladezeit',
		'title'  => 'Ich setze euren Entwurf in WordPress um.',
		'text'   => 'Neue Templates, Landingpages und Erweiterungen, auch in bestehenden Installationen und mit eurem Page Builder. Ladezeit und technisches SEO behebe ich dort, wo sie im System entstehen.',
		'abnahme' => 'Den Stand auf Staging, Seite für Seite gegen euren Entwurf und auf dem Handy geprüft.',
	],
	[
		'tags'   => 'Tracking · GA4 · Tag Manager · Consent Mode',
		'title'  => 'Ich richte die Messung ein und prüfe jedes Event.',
		'text'   => 'GA4, Google Tag Manager und Consent Mode, bei Bedarf Server-Side Tracking und Meta CAPI. Weicht eine Zahl ab, suche ich die Ursache und behebe sie.',
		'abnahme' => 'Einen Messplan und ein Protokoll, in dem jedes Event einmal ausgelöst und in GA4 geprüft ist.',
	],
	[
		'tags'   => 'Anfragestrecken · Formular · Mailversand · CRM',
		'title'  => 'Ich verbinde Formular, Messung und CRM.',
		'text'   => 'Formulare mit wenigen Pflichtfeldern, Mailversand über SPF und DKIM und die Übergabe jeder Anfrage an den vereinbarten Empfänger oder ins CRM.',
		'abnahme' => 'Eine Testanfrage, die ihr vom Absenden bis zum Eingang im CRM verfolgt.',
	],
];

$entry_projects = [
	[ 'Tracking-Audit', hu_whitelabel_price( 'tracking_audit' ), 'GA4, Tag Manager und Consent geprüft, mit schriftlichem Befund und einer Fixliste nach Priorität.' ],
	[ 'Server-Side-Setup', hu_whitelabel_price( 'server_side' ), 'Server-Container, Events und Consent eingerichtet, dokumentiert und in euren Accounts übergeben.' ],
	[ 'Landingpage', hu_whitelabel_price( 'landingpage' ), 'Seite, Formular und die vereinbarte Conversion-Messung, auf Staging gebaut und nach eurer Abnahme live.' ],
];

/*
 * Margenblock: nur Leistungen, für die der Kanon einen veröffentlichten
 * Endkundenpreis für dieselbe Leistung führt. Das ist heute allein das
 * Server-Side-Setup (Basis-Setup auf /server-side-tracking-b2b/).
 */
$margin_rows = [
	[ 'Server-Side-Setup', hu_tracking_price( 'standard', 'setup', 'display' ) . ' netto', hu_whitelabel_price( 'server_side' ) ],
];

/*
 * Der Ablauf eines Auftrags, als Stationen auf der Linie. data-wl-haken
 * nennt die Punkte des Abnahmeprotokolls, die sich hier abhaken.
 */
$process = [
	[ 'Aufgabe', 'Ihr schickt das Briefing. Ein NDA unterschreibe ich, bevor ich Kundendaten sehe. Danach bekommt ihr Rückfragen oder Aufwand und Preis.', '' ],
	[ 'Umfang', 'Umfang, Festpreis, Termin und Abnahmekriterien stehen schriftlich fest, bevor ich anfange. Vertrag und Rechnung laufen über eure Agentur.', 'umfang' ],
	[ 'Umsetzung', 'Ich baue auf Staging in euren Accounts, mit einem eigenen Zugang, den ihr jederzeit entziehen könnt. Ich arbeite im Hintergrund oder sitze als euer Technik-Lead im Kundentermin. Verzug melde ich, sobald er absehbar ist, nicht erst am Abgabetag.', 'staging' ],
	[ 'Abnahme', 'Ihr prüft gegen die vereinbarten Kriterien. Was nicht passt, korrigiere ich im vereinbarten Umfang, und live geht es erst nach eurer Freigabe.', 'test' ],
	[ 'Übergabe', 'Dokumentation, Code und Zugänge liegen danach bei euch. Euer Team kann ohne mich weiterarbeiten.', 'doku zugaenge' ],
];

// Mono-Marke eines Abschnitts auf der Linie, wie auf der Startseite.
$marke = static function ( $nr, $name ) {
	return sprintf(
		'<div class="st-rail" aria-hidden="true"><span class="st-rail__fuellung" data-st-fuellung></span><span class="st-rail__punkt"></span></div><p class="st-marke"><span class="st-marke__nr">%1$s</span> <span class="st-marke__name">%2$s</span></p>',
		esc_html( $nr ),
		esc_html( $name )
	);
};
?>

<div class="doku st wl-page" id="top" data-track-section="whitelabel_page" data-st>

	<section class="st-abschnitt st-hero wl-hero" id="hero" aria-labelledby="wl-title" data-st-abschnitt="01" data-track-section="hero">
		<?php echo $marke( '01', 'White-Label' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt st-hero__raster">
			<h1 class="st-hero__h1 wl-hero__h1" id="wl-title"><span class="st-hero__h1-satz">Ich baue die WordPress-Seite und die Messung dazu.</span> <span class="st-hero__h1-satz st-hero__h1-satz--leise">Ob euer Kunde mich sieht, entscheidet ihr.</span></h1>
			<div class="st-hero__text">
				<div class="st-hero__meta">
					<img class="st-hero__portrait" src="<?php echo esc_url( $img_uri . 'hasim-freelancer-portrait-112.webp' ); ?>" width="56" height="56" alt="" decoding="async" fetchpriority="low">
					<p class="st-hero__metazeile">White-Label für Agenturen <span aria-hidden="true">·</span> <a href="<?php echo esc_url( $about_url ); ?>" data-track-action="whitelabel_about" data-track-category="trust" data-track-section="hero">Haşim Üner</a> <span aria-hidden="true">·</span> Pattensen&nbsp;bei&nbsp;Hannover</p>
				</div>
				<p class="st-hero__satz">Meldet euer Kunde einen Messfehler, schiebt ihn kein Dienstleister dem anderen zu, weil Seite, Formular und Tracking von mir kommen.</p>
				<div class="st-hero__ctas">
					<a class="tun" href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_hero_task_brief" data-track-category="lead_gen" data-track-section="hero">Aufgabe beschreiben <span class="pf" aria-hidden="true">→</span></a>
					<a class="st-link wl-hero__termin" href="<?php echo esc_url( $whitelabel_fit_url ); ?>" data-track-action="cta_whitelabel_hero_call" data-track-category="lead_gen" data-track-section="hero">Oder erst 30&nbsp;Minuten sprechen&nbsp;<span aria-hidden="true">↗</span></a>
				</div>
				<p class="st-hero__notiz"><?php echo esc_html( $response_sentence ); ?></p>
			</div>

			<aside class="st-protokoll wl-protokoll" id="protokoll" aria-labelledby="wl-protokoll-titel" data-wl-protokoll>
				<p class="st-protokoll__kopf"><span id="wl-protokoll-titel">Abnahmeprotokoll</span><span class="wl-protokoll__muster">Muster</span></p>
				<ol class="wl-protokoll__punkte">
					<?php foreach ( $protocol as $i => $point ) : ?>
						<li class="wl-protokoll__punkt" data-wl-punkt="<?php echo esc_attr( $point[0] ); ?>">
							<span class="wl-protokoll__kasten" aria-hidden="true"></span>
							<p class="wl-protokoll__name"><span class="wl-protokoll__nr"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span> <?php echo esc_html( $point[1] ); ?></p>
							<p class="wl-protokoll__satz"><?php echo esc_html( $point[2] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
				<p class="st-protokoll__fuss">Muster ohne Kundendaten.</p>
			</aside>
		</div>
	</section>

	<section class="st-abschnitt st-pruefstand" id="pruefstand" aria-labelledby="pruefstand-h" data-st-abschnitt="02" data-track-section="pruefstand">
		<?php echo $marke( '02', 'Prüfstand' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<div class="tafel st-tafel">
				<div class="st-tafel__kopf">
					<h2 class="st-h2" id="pruefstand-h">Bevor ihr mir einen Kunden gebt, könnt ihr meinen Code lesen.</h2>
					<p class="st-vorspann">Schickt den Link eurem Entwicklungsteam. In euren Projekten arbeite ich in eurem Workflow, und wie sorgfältig ich baue, zeigt diese Website.</p>
				</div>
				<ol class="st-pruefungen">
					<li>
						<p class="st-pruefungen__label">Quellcode</p>
						<p class="st-pruefungen__text">Jede Datei dieser Website und jede Änderung, mit Datum und Begründung.</p>
						<a class="st-link" href="<?php echo esc_url( $github_url . '/commits/main/' ); ?>" target="_blank" rel="noopener" data-track-action="whitelabel_proof_repo" data-track-category="proof" data-track-section="pruefstand">Code und Änderungen auf GitHub&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
					</li>
					<li>
						<p class="st-pruefungen__label">Prüfungen</p>
						<p class="st-pruefungen__text">Vor jedem Livegang prüft die CI unter anderem PHP-Syntax, statische Analyse, strukturierte Daten und eine Sperrliste veralteter Preise und Zusagen. Schlägt eine Prüfung fehl, geht nichts live.</p>
						<a class="st-link" href="<?php echo esc_url( $github_url . '/actions' ); ?>" target="_blank" rel="noopener" data-track-action="whitelabel_proof_ci" data-track-category="proof" data-track-section="pruefstand">Prüfläufe auf GitHub&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
					</li>
					<li>
						<p class="st-pruefungen__label">Ladezeit</p>
						<p class="st-pruefungen__text">Eine Zahl von mir wäre nur eine Behauptung. PageSpeed Insights misst diese Seite jederzeit und ohne mich.</p>
						<a class="st-link" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="whitelabel_proof_pagespeed" data-track-category="proof" data-track-section="pruefstand">PageSpeed jetzt messen&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
					</li>
				</ol>
			</div>
		</div>
	</section>

	<section class="st-abschnitt st-leistungen wl-leistungen" id="lieferfelder" aria-labelledby="lieferfelder-h" data-st-abschnitt="03" data-track-section="services">
		<?php echo $marke( '03', 'Leistungen' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="lieferfelder-h">Zu jeder Aufgabe steht hier, was ihr am Ende abnehmt.</h2>
			<p class="st-vorspann">Oft hängen zwei davon zusammen, etwa eine Landingpage und die Messung ihrer Anfragen. Dann baue ich beides.</p>
			<div class="st-angebote">
				<?php foreach ( $services as $i => $service ) : ?>
					<article class="st-angebot" aria-labelledby="leistung-<?php echo esc_attr( (string) ( $i + 1 ) ); ?>-h">
						<div class="st-angebot__kopf">
							<p class="st-angebot__tags"><span class="st-angebot__nr"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span> <?php echo esc_html( $liste( $service['tags'] ) ); ?></p>
							<h3 class="st-angebot__titel" id="leistung-<?php echo esc_attr( (string) ( $i + 1 ) ); ?>-h"><?php echo esc_html( $service['title'] ); ?></h3>
						</div>
						<div class="st-angebot__text">
							<p><?php echo esc_html( $service['text'] ); ?></p>
							<div class="wl-abnahme">
								<p class="st-klein-label">Ihr nehmt ab</p>
								<p><?php echo esc_html( $service['abnahme'] ); ?></p>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="st-abschnitt st-leistungen wl-preise" id="einstieg" aria-labelledby="einstieg-h" data-st-abschnitt="04" data-track-section="entry">
		<?php echo $marke( '04', 'Preise' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="einstieg-h">Ihr fangt klein an, mit einer Aufgabe zum Festpreis.</h2>
			<p class="st-vorspann">Alle Preise netto. Umfang, Termin und Abnahmekriterien stehen vor dem Start schriftlich fest, und ihr kalkuliert mit Festpreisen.</p>

			<div class="st-angebote">
				<article class="st-angebot wl-sprint" id="test-sprint" aria-labelledby="test-sprint-h">
					<div class="st-angebot__kopf">
						<p class="st-angebot__tags"><span class="st-angebot__nr">01</span> Kleinster Einstieg</p>
						<h3 class="st-angebot__titel" id="test-sprint-h">Im Test-Sprint löse ich eine abgegrenzte WordPress-Aufgabe.</h3>
						<p class="st-angebot__preis"><?php echo esc_html( $test_sprint_price ); ?></p>
					</div>
					<div class="st-angebot__text">
						<p>Zum Beispiel einen Formularfehler beheben oder eine vorhandene Komponente anpassen. Höchstens ein Arbeitstag Aufwand, mit Funktionstest, Dokumentation und einer Korrekturrunde. So seht ihr an einer echten Aufgabe, wie ich arbeite.</p>
						<p class="st-angebot__mehr">Relaunch, ganze Landingpage und vollständiges Tracking-Setup gehören nicht dazu. Lizenzen rechne ich separat ab.</p>
						<p class="st-angebot__wege">
							<a class="tun" href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_entry_task_brief" data-track-category="lead_gen" data-track-section="entry">Aufgabe für den Test-Sprint beschreiben <span class="pf" aria-hidden="true">→</span></a>
						</p>
					</div>
				</article>

				<article class="st-angebot wl-erstprojekte" aria-labelledby="erstprojekte-h">
					<div class="st-angebot__kopf">
						<p class="st-angebot__tags"><span class="st-angebot__nr">02</span> Erstprojekte</p>
						<h3 class="st-angebot__titel" id="erstprojekte-h">Größere Vorhaben starten als Erstprojekt.</h3>
					</div>
					<div class="st-angebot__text">
						<dl class="wl-projekte">
							<?php foreach ( $entry_projects as $project ) : ?>
								<div>
									<dt><?php echo esc_html( $project[0] ); ?></dt>
									<dd class="wl-projekte__preis"><?php echo esc_html( $fest( $project[1] ) ); ?></dd>
									<dd class="wl-projekte__satz"><?php echo esc_html( $project[2] ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
						<p class="st-angebot__mehr">Den Festpreis nenne ich, sobald der Umfang geklärt ist.</p>
					</div>
				</article>

				<article class="st-angebot wl-kontingent" aria-labelledby="kontingent-h">
					<div class="st-angebot__kopf">
						<p class="st-angebot__tags"><span class="st-angebot__nr">03</span> Weiterentwicklung</p>
						<h3 class="st-angebot__titel" id="kontingent-h">Nach dem ersten Projekt könnt ihr ein Monatskontingent buchen.</h3>
						<p class="st-angebot__preis"><?php echo esc_html( $retainer_price ); ?></p>
					</div>
					<div class="st-angebot__text">
						<p>Nur nach einem erfolgreichen Erstprojekt. Prioritäten und Kündigung legen wir vorher schriftlich fest.</p>
					</div>
				</article>
			</div>

			<div class="wl-marge" aria-labelledby="marge-h">
				<h3 class="st-h3" id="marge-h">Für dieselbe Leistung zahlen Endkunden bei mir mehr als ihr.</h3>
				<?php foreach ( $margin_rows as $row ) : ?>
					<dl class="wl-marge__zeile">
						<dt><?php echo esc_html( $row[0] ); ?></dt>
						<dd><span class="wl-marge__wer">Endkunden</span> <span class="wl-marge__preis"><?php echo esc_html( $fest( $row[1] ) ); ?></span></dd>
						<dd><span class="wl-marge__wer">Agenturen</span> <span class="wl-marge__preis wl-marge__preis--agentur"><?php echo esc_html( $fest( $row[2] ) ); ?></span></dd>
					</dl>
				<?php endforeach; ?>
				<p class="wl-marge__text">Den Endkundenpreis nenne ich öffentlich auf meiner <a class="st-link" href="<?php echo esc_url( $tracking_b2b_url ); ?>" data-track-action="whitelabel_margin_reference" data-track-category="proof" data-track-section="entry">Seite zum Server-Side Tracking</a>. Für die anderen Einstiege gibt es keinen veröffentlichten Endkundenpreis. Was ihr eurem Kunden berechnet, legt ihr fest.</p>
			</div>
		</div>
	</section>

	<section class="st-abschnitt wl-ablauf-abschnitt" id="zusammenarbeit" aria-labelledby="zusammenarbeit-h" data-st-abschnitt="05" data-track-section="process">
		<?php echo $marke( '05', 'Ablauf' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="zusammenarbeit-h">Ich arbeite unter eurem Namen und in euren Accounts.</h2>
			<p class="st-vorspann">In eurem Kundenstamm mache ich keine Akquise. Meldet sich euer Kunde direkt bei mir, verweise ich ihn an euch, während der Zusammenarbeit und zwölf Monate danach.</p>
			<ol class="wl-ablauf">
				<?php foreach ( $process as $i => $step ) : ?>
					<li class="wl-ablauf__station"<?php echo '' !== $step[2] ? ' data-wl-haken="' . esc_attr( $step[2] ) . '"' : ''; ?>>
						<span class="st-station__punkt" aria-hidden="true" data-st-punkt></span>
						<span class="wl-ablauf__nr"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
						<p class="wl-ablauf__name"><?php echo esc_html( $step[0] ); ?></p>
						<p class="wl-ablauf__text"><?php echo esc_html( $step[1] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
			<div class="wl-ausfall">
				<h3 class="st-h3" id="ausfall-h">Wenn ich ausfalle, sage ich es euch am selben Tag.</h3>
				<p>Eure Lieferung hängt trotzdem nicht an mir. Code und Setups liegen in euren Accounts, jeder Schritt ist dokumentiert, und der Stand auf Staging ist nachvollziehbar. Ein anderer Entwickler kann dort weitermachen, wo ich aufgehört habe.</p>
			</div>
		</div>
	</section>

	<section class="st-abschnitt st-arbeiten" id="proof" aria-labelledby="proof-h" data-st-abschnitt="06" data-track-section="proof">
		<?php echo $marke( '06', 'Referenzen' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="proof-h"><?php echo esc_html( ucfirst( $reference_word ) ); ?> könnt ihr selbst öffnen.</h2>
			<p class="st-vorspann">White-Label-Arbeit erscheint unter dem Namen der Agentur, und ohne ihre Freigabe zeige ich sie nirgends. Prüfbar sind deshalb öffentliche Projekte, der Code dieser Website und eine echte Aufgabe im <a class="st-link" href="#einstieg" data-track-action="cta_whitelabel_proof_test_sprint" data-track-category="lead_gen" data-track-section="proof">Test-Sprint</a>.</p>
			<?php if ( $reference_n ) : ?>
				<ul class="st-referenzen__liste">
					<?php foreach ( $references as $reference ) : ?>
						<li>
							<p class="st-klein-label"><?php echo esc_html( $liste( $reference['tag'] ) ); ?></p>
							<p class="st-referenzen__name"><a class="st-link st-link--stark" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="whitelabel_reference_open" data-track-category="proof" data-track-section="proof"><?php echo esc_html( $reference['name'] ); ?>&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a></p>
							<p><?php echo esc_html( $reference['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>

	<section class="st-abschnitt st-fragen" id="faq" aria-labelledby="faq-h" data-st-abschnitt="07" data-track-section="faq">
		<?php echo $marke( '07', 'Fragen' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt st-zweispaltig">
			<h2 class="st-h2" id="faq-h">Hier steht, was Kapazität, Stack und Ausstieg betrifft.</h2>
			<div class="fragen">
				<?php // Dieselbe Quelle wie das FAQPage-Schema in inc/org-schema.php. ?>
				<?php foreach ( $faq_items as $item ) : ?>
					<details class="wl-faq__item" name="hu-faq-whitelabel"><summary class="wl-faq__summary" data-track-action="faq_whitelabel_open" data-track-label="<?php echo esc_attr( $item['key'] ); ?>" data-track-category="engagement" data-track-section="faq"><?php echo esc_html( $item['question'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $item['answer'] ); ?></p></div></div></details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="st-abschnitt st-anfrage wl-anfrage" id="naechster-schritt" aria-labelledby="anfrage-h" data-st-abschnitt="08" data-track-section="naechster_schritt">
		<?php echo $marke( '08', 'Anfrage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt wl-anfrage__raster">
			<div class="wl-anfrage__text">
				<h2 class="st-h2 st-anfrage__h2" id="anfrage-h">Ich lese eure Aufgabe selbst und antworte <?php echo esc_html( $response_short ); ?>.</h2>
				<p class="st-vorspann">Ein paar Sätze reichen: was entstehen soll, was schon da ist und bis wann ihr es braucht. Ihr bekommt Rückfragen oder eine Einschätzung mit Aufwand und Preis.</p>
				<ul class="wl-wege">
					<li>
						<p>Ihr steckt noch in der Angebotsphase? Dann sage ich euch, ob es machbar ist und was es kostet, bevor ihr eurem Kunden zusagt.</p>
						<a class="st-link st-link--stark" href="<?php echo esc_url( $wl_form_offer_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_way_offer" data-track-category="lead_gen" data-track-section="naechster_schritt">Vorhaben zur Einschätzung beschreiben&nbsp;<span aria-hidden="true">→</span></a>
					</li>
					<li>
						<p>Lieber zuerst sprechen?</p>
						<a class="st-link st-link--stark" href="<?php echo esc_url( $whitelabel_fit_url ); ?>" data-track-action="cta_whitelabel_form_call" data-track-category="lead_gen" data-track-section="naechster_schritt">30&nbsp;Minuten buchen&nbsp;<span aria-hidden="true">↗</span></a>
					</li>
				</ul>
			</div>

			<div class="wl-request" id="aufgabe">
				<div class="wl-request__head">
					<p class="st-klein-label" data-wl-case-label aria-live="polite">Konkrete Aufgabe</p>
					<h3 class="st-h3">Beschreibt euer Vorhaben.</h3>
					<p class="wl-request__pflicht">Nur Aufgabe und E-Mail sind Pflichtfelder.</p>
				</div>
				<div id="wl-form-errors" class="wl-request__error-summary is-hidden" role="alert" tabindex="-1" data-wl-error-summary><strong>Bitte prüft eure Angaben.</strong><ul data-wl-error-list></ul></div>
				<form class="wl-request__form" data-wl-request-form action="<?php echo esc_url( $wl_form_endpoint ); ?>" method="post" novalidate>
					<div class="wl-request__honeypot" aria-hidden="true"><label for="wl-company-website">Website</label><input id="wl-company-website" name="company_website" type="text" tabindex="-1" autocomplete="off"></div>
					<input type="hidden" name="case" value="aufgabe" data-wl-case>
					<div class="wl-request__field"><label for="wl-task">Was soll umgesetzt oder geklärt werden? <span>(Pflichtfeld)</span></label><textarea id="wl-task" name="task" rows="5" required minlength="12" maxlength="4000" aria-describedby="wl-task-hint" placeholder="Zum Beispiel: Unser Kunde braucht ein Anfrageformular in WordPress. Die Anfragen sollen ins vorhandene CRM gelangen …"></textarea><p id="wl-task-hint" class="wl-request__hint">Aufgabe, vorhandenes Setup und gewünschtes Ergebnis. Bitte keine Passwörter oder Kundendaten senden.</p></div>
					<div class="wl-request__field"><label for="wl-email">Eure geschäftliche E-Mail <span>(Pflichtfeld)</span></label><input id="wl-email" name="email" type="email" required autocomplete="email" inputmode="email" placeholder="name@agentur.de"></div>
					<div class="wl-request__field"><label for="wl-timeframe">Gewünschter Zeitraum <span>(optional)</span></label><input id="wl-timeframe" name="timeframe" type="text" maxlength="160" placeholder="Zum Beispiel: ab nächstem Monat" autocomplete="off"></div>
					<div class="wl-request__field"><label for="wl-referral">Wie seid ihr auf mich aufmerksam geworden? <span>(optional)</span></label><select id="wl-referral" name="referral_source"><option value="">Bitte wählen</option><?php foreach ( $wl_referral_options as $value => $label ) : ?><option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></div>
					<details class="wl-request__optional"><summary>Schon Informationen zu den Zugängen? <span>(optional)</span></summary><fieldset class="wl-request__access"><legend>Sind die benötigten Zugänge verfügbar?</legend><div class="wl-request__choices"><?php foreach ( $wl_access_options as $value => $label ) : ?><label for="wl-access-<?php echo esc_attr( $value ); ?>"><input id="wl-access-<?php echo esc_attr( $value ); ?>" name="access" type="radio" value="<?php echo esc_attr( $value ); ?>"><span><?php echo esc_html( $label ); ?></span></label><?php endforeach; ?></div></fieldset></details>
					<button class="tun" type="submit" data-wl-submit disabled>Aufgabe senden</button>
					<p class="wl-request__legal">Mit dem Absenden fragt ihr unverbindlich an. Eure Angaben nutze ich zur Beantwortung. Details in der <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.</p>
					<div class="wl-request__feedback" data-wl-feedback aria-live="polite" role="status" tabindex="-1"></div>
				</form>
				<noscript><p class="wl-request__ohne-js">Ohne JavaScript sendet das Formular nicht. Schickt eure Aufgabe dann an <a href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>.</p></noscript>
				<p class="wl-request__fallback">Auch per E-Mail: <a href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a></p>
			</div>
		</div>
	</section>

	<div class="wl-sticky-cta" id="wl-sticky-cta" aria-hidden="true"><a href="<?php echo esc_url( $wl_form_task_url ); ?>" class="tun" data-wl-form-link data-track-action="cta_sticky_whitelabel_task_brief" data-track-category="lead_gen" data-track-section="sticky_mobile" tabindex="-1">Aufgabe beschreiben <span class="pf" aria-hidden="true">→</span></a></div>
</div>

<?php
if ( function_exists( 'blocksy_after_current_template' ) ) {
	blocksy_after_current_template();
}

do_action( 'blocksy:content:bottom' );
?>
	</main>
<?php
do_action( 'blocksy:content:after' );
do_action( 'blocksy:footer:before' );
?>
	<footer id="footer" class="wl-page-footer" aria-labelledby="wl-page-footer-heading" role="contentinfo">
		<h2 id="wl-page-footer-heading" class="nur-vorlesen">Seitenabschluss</h2>
		<div class="blatt wl-page-footer__inner">
			<nav class="wl-page-footer__site" aria-label="Weitere Seiten">
				<a href="<?php echo esc_url( $wl_home_url ); ?>" rel="home" data-track-action="nav_whitelabel_footer_home" data-track-category="navigation" data-track-section="whitelabel_footer">Startseite</a>
			</nav>
			<p>&copy; <time datetime="<?php echo esc_attr( $current_year ); ?>"><?php echo esc_html( $current_year ); ?></time> Haşim Üner <span aria-hidden="true">·</span> White-Label für Agenturen</p>
			<nav class="wl-page-footer__legal" aria-label="Rechtliches">
				<a href="<?php echo esc_url( $imprint_url ); ?>" data-track-action="nav_whitelabel_footer_imprint" data-track-category="navigation" data-track-section="whitelabel_footer">Impressum</a>
				<a href="<?php echo esc_url( $privacy_url ); ?>" data-track-action="nav_whitelabel_footer_privacy" data-track-category="navigation" data-track-section="whitelabel_footer">Datenschutz</a>
			</nav>
		</div>
	</footer>
<?php
do_action( 'blocksy:footer:after' );
?>
</div>

<?php wp_footer(); ?>
</body>
</html>
