<?php
/**
 * Template Name: Whitelabel & Weiterentwicklung
 * Description: White-Label WordPress und Tracking für Agenturen.
 *
 * Acht Stationen auf der bestehenden Strecke. Dunkel sind nur die beiden
 * Messinstrumente: der sich selbst prüfende Hero und die Margen-Tafel.
 * Fakten kommen aus inc/canon/. Der bestehende Formularvertrag bleibt.
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
$wl_form_later_url = $wl_form_url( 'vormerken' );
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
 * Akquise-Mails. Gebaut aus der .leiste des Systems, ohne Klappblatt, in
 * template-parts/whitelabel-header.php; schmal bleiben Wortmarke und Tuer. Die
 * Anker markiert whitelabel.js, solange ihr Abschnitt im Blick ist
 * (aria-current="location").
 */
$wl_nav = [
	[ '#lieferfelder', 'Leistungen', 'nav_whitelabel_services' ],
	[ '#proof', 'Belege', 'nav_whitelabel_proof' ],
	[ '#zusammenarbeit', 'Ablauf', 'nav_whitelabel_process' ],
	[ '#einstieg', 'Preise', 'nav_whitelabel_pricing' ],
	[ '#faq', 'Fragen', 'nav_whitelabel_faq' ],
];

remove_action( 'wp_body_open', 'nexus_render_site_header', 20 );
add_action(
	'wp_body_open',
	static function () use ( $wl_home_url, $wl_brand_text, $wl_home_label, $wl_form_task_url, $wl_nav ) {
		get_template_part(
			'template-parts/whitelabel-header',
			null,
			[
				'home_url'   => $wl_home_url,
				'brand'      => $wl_brand_text,
				'home_label' => $wl_home_label,
				'form_url'   => $wl_form_task_url,
				'nav'        => $wl_nav,
			]
		);
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

$retainer_price    = $fest( hu_whitelabel_price( 'retainer', 'display_hours_plain', hu_whitelabel_price( 'retainer' ) ) );

/*
 * Texte des Formulars je Weg. Der Server rendert immer `aufgabe`, weil die
 * Route gecacht wird; whitelabel.js setzt den Weg aus ?case= und tauscht
 * diese Texte. Die Wege selbst stehen in hu_whitelabel_request_cases().
 */
$task_texts    = [
	'label'       => 'Konkrete Aufgabe',
	'field'       => 'Was soll umgesetzt oder geklärt werden?',
	'placeholder' => 'Zum Beispiel: Unser Kunde braucht ein Anfrageformular in WordPress. Die Anfragen sollen ins vorhandene CRM gelangen …',
	'hint'        => 'Aufgabe, vorhandenes Setup und gewünschtes Ergebnis. Bitte keine Passwörter oder Kundendaten senden.',
	'error'       => 'Bitte die Aufgabe kurz beschreiben. Vier Zeilen genügen.',
	'submit'      => 'Aufgabe senden',
];
$wl_case_texts = [
	'aufgabe'       => $task_texts,
	'angebotsphase' => array_merge( $task_texts, [ 'label' => 'Technische Einschätzung zur Angebotsphase' ] ),
	'vormerken'     => [
		'label'       => 'Für das nächste Projekt vormerken',
		'field'       => 'Womit arbeitet ihr, und was steht als Nächstes an?',
		'placeholder' => 'Zum Beispiel: WordPress mit Elementor, meist Landingpages für B2B-Kunden. Im Frühjahr stehen zwei Relaunches an …',
		'hint'        => 'Stack, typische Aufgaben und ungefähr, wann ihr Unterstützung braucht. Bitte keine Passwörter oder Kundendaten senden.',
		'error'       => 'Bitte kurz schreiben, womit ihr arbeitet. Zwei Sätze genügen.',
		'submit'      => 'Vormerken lassen',
	],
];

$faq_items           = nexus_get_whitelabel_faq_items();
$wl_access_options   = hu_whitelabel_request_access_options();
$wl_referral_options = function_exists( 'nexus_get_inquiry_referral_options' ) ? nexus_get_inquiry_referral_options() : [];

$references     = hu_whitelabel_reference_projects();
$reference_n    = count( $references );

// Sieben lokale Prüfungen, ohne eingetragene Ergebnisse.
$protocol = [
	[ 'ueberschriften', 'Überschriften', 'Genau eine H1, jede H2 mit Sprunganker.' ],
	[ 'bilder', 'Bilder', 'Jedes Bild hat ein Alt-Attribut, eine Breite und eine Höhe.' ],
	[ 'formular', 'Pflichtfelder', 'Jedes Pflichtfeld hat eine Beschriftung.' ],
	[ 'neuer-tab', 'Neuer Tab', 'Links mit neuem Tab haben einen Texthinweis und sind mit noopener gekennzeichnet.' ],
	[ 'lcp', 'Ladezeit (LCP)', 'Bis das größte Element steht. Der Browser misst diesen Besuch.' ],
	[ 'cookies', 'Cookies', 'Der Browser zählt die für diese Seite lesbaren Cookies.' ],
	[ 'schema', 'Strukturierte Daten', 'Die JSON-LD-Blöcke lassen sich lesen und enthalten typisierte Einträge.' ],
];

/*
 * Vier Arten von Aufgaben. Jede beginnt mit der Lage, in der Agenturen einen
 * Freelancer suchen (Ausschreibungen im Outreach-Tracker: Auftragsspitzen,
 * Tracking, CRM-Anbindung, Barrierefreiheit), und endet mit dem, was die
 * Agentur abnimmt.
 */
$services = [
	[
		'anlass' => 'Der Entwurf ist freigegeben, euer Team ist ausgebucht.',
		'tags'   => 'WordPress · Templates · Landingpages · Ladezeit · technisches SEO',
		'text'   => 'Ich baue Landingpages und Templates in eurem bestehenden WordPress. Auch ein angefangenes Projekt kann ich übernehmen. Bei langsamen Seiten oder technischen SEO-Fehlern arbeite ich direkt im Theme und in den betroffenen Komponenten.',
		'abnahme' => 'Auf Staging vergleicht ihr die fertige Seite mit eurem Entwurf und könnt sie am Handy bedienen.',
	],
	[
		'anlass' => 'Beim Kunden passen die Zahlen nicht zu den Anfragen.',
		'tags'   => 'Tracking · GA4 · Tag Manager · Consent Mode',
		'text'   => 'Ich prüfe, welche Aktionen GA4 über den Google Tag Manager erfasst und wie die Tags auf den Consent Mode reagieren. Braucht das Projekt Server-Side Tracking oder Meta CAPI, vereinbaren wir die Events vorab.',
		'abnahme' => 'Im Messplan steht, was zählen soll. Das Testprotokoll zeigt für jedes vereinbarte GA4-Event, was beim Auslösen angekommen ist.',
	],
	[
		'anlass' => 'Die Anfragen kommen an, aber nicht im CRM.',
		'tags'   => 'Anfragestrecken · Formular · Mailversand · CRM',
		'text'   => 'Ich verbinde das WordPress-Formular mit eurem Mailversand oder dem CRM des Kunden und ordne die Felder zu, die sein Vertrieb braucht.',
		'abnahme' => 'Ihr sendet das Formular ab und seht im vereinbarten Postfach oder CRM nach, ob die Testanfrage mit den richtigen Angaben ankommt.',
	],
	[
		'anlass' => 'Euer Kunde fragt nach Barrierefreiheit.',
		'tags'   => 'Barrierefreiheit · WCAG 2.1 AA · BFSG',
		'text'   => 'Ich prüfe die vereinbarten Templates anhand der relevanten WCAG-Kriterien. Dazu gehören Tastaturbedienung, Kontraste und verständliche Formularmeldungen. Technische Barrieren behebe ich in Theme und Templates.',
		'abnahme' => 'Im Protokoll je Template seht ihr den Befund, die Korrektur und das Ergebnis der Nachprüfung.',
	],
];

$service_names = [ 'WordPress', 'Tracking', 'CRM-Strecke', 'Barrierefreiheit' ];
$service_evidence = [
	[ 'So arbeitet ihr zuerst an einer echten Aufgabe mit mir.', '#test-sprint', 'whitelabel_field_sprint', 'Zum Test-Sprint' ],
	[ 'Der Umfang bestimmt den Festpreis, bevor ich anfange.', '#einstieg', 'whitelabel_field_prices', 'Zu den Agenturpreisen' ],
	[ 'Die Testanfrage gehört zu jeder Abnahme mit Formular.', '#zusammenarbeit', 'whitelabel_field_process', 'Zum Ablauf' ],
	[ 'Das Protokoll oben prüft diese Seite gerade im Browser.', '#protokoll', 'whitelabel_field_protocol', 'Zum Protokoll' ],
];
$entry_projects = [
	[ 'id' => 'test-sprint', 'key' => 'test_sprint', 'name' => 'Test-Sprint', 'lage' => 'Lernt meine Arbeit an einem Ticket aus eurem Projekt kennen.', 'text' => 'Eine abgegrenzte WordPress-Aufgabe, zum Beispiel einen Formularfehler beheben oder eine vorhandene Komponente anpassen. Höchstens ein Arbeitstag, mit Funktionstest, Dokumentation und einer Korrekturrunde.', 'more' => 'Relaunch, ganze Landingpage und vollständiges Tracking-Setup gehören nicht dazu. Lizenzen rechne ich separat ab.', 'track' => 'cta_whitelabel_entry_task_brief' ],
	[ 'id' => 'angebot-tracking-audit', 'key' => 'tracking_audit', 'name' => 'Tracking-Audit', 'lage' => 'Das Reporting zeigt andere Zahlen als der Vertrieb.', 'text' => 'Ich prüfe GA4, Tag Manager und Consent. Ihr bekommt die Befunde und eine nach Dringlichkeit sortierte Liste der Korrekturen.', 'track' => 'cta_whitelabel_offer_audit' ],
	[ 'id' => 'angebot-server-side', 'key' => 'server_side', 'name' => 'Server-Side-Setup', 'lage' => 'Euer Kunde braucht serverseitiges Tracking für seine Kampagnen.', 'text' => 'Server-Container, Events und Consent eingerichtet, dokumentiert und in euren Accounts übergeben.', 'track' => 'cta_whitelabel_offer_server_side' ],
	[ 'id' => 'angebot-landingpage', 'key' => 'landingpage', 'name' => 'Landingpage', 'lage' => 'Entwurf und Text stehen, die Seite muss gebaut und gemessen werden.', 'text' => 'Seite, Formular und die vereinbarte Conversion-Messung, auf Staging gebaut und nach eurer Abnahme live.', 'track' => 'cta_whitelabel_offer_landingpage' ],
];
$margin_rows = hu_whitelabel_margin_rows();
$hero_margin = $margin_rows ? reset( $margin_rows ) : null;

/*
 * Margen-Rechner: Die Agentur rechnet mit ihrem eigenen Kundenpreis. Alle
 * Betraege kommen aus dem Preis-Kanon. Einen Startwert ueber dem Agenturpreis
 * gibt es nur dort, wo ich fuer denselben Umfang einen oeffentlichen
 * Endkundenpreis nenne (Server-Side-Setup); sonst beginnt der Regler bei
 * meinem Preis, also bei null Marge.
 */
$calc_rows = [];
foreach ( [ 'tracking_audit' => 'Tracking-Audit', 'server_side' => 'Server-Side-Setup', 'landingpage' => 'Landingpage' ] as $calc_key => $calc_name ) {
	$calc_agency = (int) hu_whitelabel_price( $calc_key, 'value' );
	if ( $calc_agency <= 0 ) {
		continue;
	}
	$calc_start = $calc_agency;
	if ( $hero_margin && $calc_key === $hero_margin['key'] ) {
		$calc_start = (int) $hero_margin['retail_value'];
	}
	$calc_rows[] = [
		'key'    => $calc_key,
		'name'   => $calc_name,
		'agency' => $calc_agency,
		'start'  => $calc_start,
		'public' => $calc_start > $calc_agency,
	];
}
$calc_first = null;
foreach ( $calc_rows as $calc_row ) {
	if ( $calc_row['public'] ) {
		$calc_first = $calc_row;
		break;
	}
}
if ( ! $calc_first && $calc_rows ) {
	$calc_first = $calc_rows[0];
}
$process = [
	[ 'Aufgabe', 'Schickt mir Ziel, Termin und den aktuellen Stand. Ich kläre mit euch, was für ein Angebot noch fehlt.' ],
	[ 'Umfang', 'Ihr bekommt Umfang, Festpreis, Termin und Abnahmekriterien schriftlich, bevor ihr den Auftrag erteilt.' ],
	[ 'Umsetzung', 'Auf Staging könnt ihr den aktuellen Stand ansehen. Zeichnet sich eine Verzögerung ab, melde ich mich bei euch.' ],
	[ 'Abnahme', 'Ihr prüft die vereinbarten Kriterien und gebt den Livegang frei. Fehler in meiner Lieferung behebe ich auch nach der Abnahme ohne Berechnung. Für zusätzliche Wünsche bekommt ihr ein eigenes Angebot.' ],
	[ 'Übergabe', 'Dokumentation, Code und Zugänge liegen danach bei euch. Euer Team kann ohne mich weiterarbeiten.' ],
];
$safeguards = [
	[ 'Zugänge', 'Ihr vergebt einen eigenen Zugang für mich und entzieht ihn, wann ihr wollt.' ],
	[ 'Vertraulichkeit', 'NDA, bevor ich Kundendaten sehe. Wo personenbezogene Daten im Spiel sind, zusätzlich ein Vertrag zur Auftragsverarbeitung.' ],
	[ 'Dokumentation', 'Aufbau, Änderungen und offene Punkte, geschrieben für euer Team.' ],
	[ 'Ausstieg', 'Ein Projekt endet mit der Abnahme. Für das Monatskontingent legen wir die Kündigung vorher schriftlich fest, ohne Verlängerungsfalle.' ],
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

<div class="doku st wl-page" id="top" data-track-section="whitelabel_page" data-st data-st-final>

	<section class="st-abschnitt st-hero st-messflaeche wl-hero tafel" id="hero" aria-labelledby="wl-title" data-st-abschnitt="01" data-track-section="hero" data-wl-pruefstand>
		<?php echo $marke( '01', 'Auftrag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="st-inhalt" data-wl-messfeld>
			<div class="wl-hero__kopf"><p class="st-klein-label">White-Label für Agenturen</p></div>
			<div class="wl-hero__raster">
				<div class="wl-hero__links">
					<h1 class="wl-hero__h1" id="wl-title" data-wl-mess="h1">WordPress-Entwicklung und Tracking für eure Kunden.</h1>
					<p class="st-hero__satz" data-wl-mess="satz">Ich baue unter eurem Namen WordPress-Seiten, vom Entwurf bis zur Anfrage im CRM. Festpreis und Liefertermin kennt ihr, bevor ihr eurem Kunden zusagt.</p>
					<div class="st-hero__ctas">
						<a class="tun" href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-wl-mess="cta" data-track-action="cta_whitelabel_hero_task_brief" data-track-category="lead_gen" data-track-section="hero">Projekt einschätzen lassen <span class="pf" aria-hidden="true">→</span></a>
						<a class="st-link" href="#einstieg" data-track-action="cta_whitelabel_hero_prices" data-track-category="navigation" data-track-section="hero">Agenturpreise ansehen <span aria-hidden="true">↓</span></a>
					</div>
					<p class="st-hero__notiz">Kostenlose Ersteinschätzung. <?php echo esc_html( $response_sentence ); ?></p>
					<div class="st-hero__meta">
						<img class="st-hero__portrait" src="<?php echo esc_url( $img_uri . 'hasim-freelancer-portrait-112.webp' ); ?>" width="48" height="48" alt="" decoding="async" fetchpriority="low" data-wl-mess="bild">
						<p class="st-hero__person"><strong>Haşim Üner</strong> · Region Hannover, remote in DACH <br><a class="st-link" href="<?php echo esc_url( $about_url ); ?>" data-track-action="whitelabel_about" data-track-category="trust" data-track-section="hero">Wer für euch arbeitet</a></p>
					</div>
				</div>
				<aside class="wl-protokoll" id="protokoll" aria-labelledby="wl-protokoll-titel" data-wl-protokoll>
					<div class="wl-protokoll__kopf">
						<p class="st-klein-label" id="wl-protokoll-titel">Arbeitsprobe · Live-Messung</p>
						<div class="wl-stempel" data-wl-stempel aria-hidden="true"><span class="wl-stempel__kopf">Geprüft</span><span class="wl-stempel__wert" data-wl-stempel-wert></span></div>
					</div>
					<p class="wl-protokoll__messwert"><span data-wl-lcp-kopie>–</span></p>
					<p class="wl-protokoll__kennzahl">Ladezeit bei eurem Besuch · <span data-wl-lcp-proof-label>Browsermessung</span></p>
					<div class="wl-protokoll__balken" aria-hidden="true"><i data-wl-fortschritt></i></div>
					<p class="wl-protokoll__status" data-wl-status aria-live="polite" aria-atomic="true">Prüflauf braucht JavaScript. Es läuft keine Prüfung.</p>
					<p class="wl-protokoll__erklaerung">Diese Werte misst euer Browser jetzt. Bei eurem Projekt zeigt das Abnahmeprotokoll zum Beispiel, ob eine Testanfrage im CRM ankommt.</p>
					<details class="wl-protokoll__details" data-wl-details>
						<summary>Prüfpunkte ansehen</summary>
						<ol class="wl-protokoll__liste">
							<?php foreach ( $protocol as $point ) : ?>
								<li data-wl-pruefung="<?php echo esc_attr( $point[0] ); ?>"><span class="wl-pl-name"<?php echo 'lcp' === $point[0] ? ' data-wl-lcp-label' : ''; ?>><?php echo esc_html( $point[1] ); ?></span><span class="wl-pl-wert">–</span><span class="wl-pl-satz"><?php echo esc_html( $point[2] ); ?></span></li>
							<?php endforeach; ?>
						</ol>
						<div class="wl-protokoll__controls"><button type="button" class="wl-protokoll__nochmal" data-wl-nochmal hidden data-track-action="whitelabel_protocol_rerun" data-track-category="engagement" data-track-section="hero">Erneut prüfen <span aria-hidden="true">↻</span></button><button type="button" class="wl-protokoll__masse" data-wl-mass-toggle hidden aria-pressed="false">Designmaße anzeigen</button></div>
					</details>
				</aside>
			</div>
			<ul class="st-belege" aria-label="Belege auf dieser Seite">
				<li><a href="#test-sprint" data-track-action="whitelabel_proof_strip_sprint" data-track-category="proof" data-track-section="hero"><span class="st-belege__zahl"><?php echo esc_html( $fest( hu_format_eur( (int) hu_whitelabel_price( 'test_sprint', 'value' ) ) ) ); ?></span><span class="st-belege__text">netto, Festpreis für den Test-Sprint: eine Aufgabe, höchstens ein Arbeitstag</span></a></li>
				<?php if ( $hero_margin ) : ?><li><a href="#marge" data-track-action="whitelabel_proof_strip_margin" data-track-category="proof" data-track-section="hero"><span class="st-belege__zahl st-belege__zahl--messwert"><?php echo esc_html( $fest( $hero_margin['discount_display'] ) ); ?></span><span class="st-belege__text">beim Server-Side-Setup gegenüber meinem Endkundenpreis für denselben Umfang</span></a></li><?php endif; ?>
				<li><a href="#proof" data-track-action="whitelabel_proof_strip_references" data-track-category="proof" data-track-section="hero"><span class="st-belege__zahl"><?php echo esc_html( (string) $reference_n ); ?></span><span class="st-belege__text">öffentliche Websites und der Code dieser Seite, offen zum Nachprüfen</span></a></li>
			</ul>
		</div>
	</section>

	<section class="st-abschnitt st-leistungen wl-leistungen" id="lieferfelder" aria-labelledby="lieferfelder-h" data-st-abschnitt="02" data-track-section="services">
		<?php echo $marke( '02', 'Felder' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="lieferfelder-h">Vom WordPress-Entwurf <span class="st-leiser">bis zur Anfrage im CRM.</span></h2>
			<p class="st-vorspann">Euer Kunde gibt das Design frei. Zum Start will er wissen: Kommt eine Anfrage beim Vertrieb an? Und sieht das Marketing sie in GA4? Ich baue die Seite und prüfe mit euch den Weg der ersten Testanfrage.</p>
			<div class="wl-schichten">
				<div class="wl-stack" aria-hidden="true" data-wl-stack>
					<div class="wl-stack__szene" data-wl-szene>
						<?php foreach ( $service_names as $i => $service_name ) : ?>
							<div class="wl-platte wl-platte--<?php echo esc_attr( (string) ( $i + 1 ) ); ?><?php echo 0 === $i ? ' is-aktiv' : ''; ?>" style="--i:<?php echo esc_attr( (string) $i ); ?>" data-wl-platte="<?php echo esc_attr( (string) $i ); ?>" data-track-action="whitelabel_stack_layer" data-track-label="<?php echo esc_attr( $service_name ); ?>" data-track-category="engagement" data-track-section="services"><span class="wl-platte__nr"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><span class="wl-platte__name"><?php echo esc_html( $service_name ); ?></span><span class="wl-platte__bild"></span><span class="wl-platte__puls"></span></div>
						<?php endforeach; ?>
					</div>
					<p class="wl-stack__legende"><span class="wl-stack__punkt"></span>eine Anfrage, von der Seite bis ins CRM</p>
				</div>
				<div class="wl-schichten__felder">
					<p class="wl-schichten__satz">Ihr könnt die ganze Strecke oder eine einzelne Aufgabe an mich geben.</p>
					<div class="st-stationen">
						<?php foreach ( $services as $i => $service ) : ?>
							<details class="st-station wl-feld" name="feld" data-wl-feld="<?php echo esc_attr( (string) $i ); ?>"<?php echo 0 === $i ? ' open' : ''; ?>>
								<summary><span class="st-station__nr"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><span class="st-station__name" id="leistung-<?php echo esc_attr( (string) ( $i + 1 ) ); ?>-h"><?php echo esc_html( $service_names[ $i ] ); ?></span><span class="st-station__teaser"><?php echo esc_html( $service['anlass'] ); ?></span></summary>
								<div class="st-station__detail">
									<p class="st-klein-label wl-feld__tags"><?php echo esc_html( $liste( $service['tags'] ) ); ?></p>
									<div><p><?php echo nexus_glossary_explain_text( $service['text'], 'consent-mode' ); ?></p></div>
									<div><p class="st-klein-label">So prüft ihr das Ergebnis</p><p><?php echo esc_html( $service['abnahme'] ); ?></p></div>
									<p class="st-station__beleg"><?php echo esc_html( $service_evidence[ $i ][0] ); ?> <a class="st-link" href="<?php echo esc_attr( $service_evidence[ $i ][1] ); ?>" data-track-action="<?php echo esc_attr( $service_evidence[ $i ][2] ); ?>" data-track-category="navigation" data-track-section="services"><?php echo esc_html( $service_evidence[ $i ][3] ); ?> <span aria-hidden="true">→</span></a></p>
								</div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="st-abschnitt st-pruefstand st-wash wl-belege" id="proof" aria-labelledby="pruefstand-h" data-st-abschnitt="03" data-track-section="pruefstand">
		<?php echo $marke( '03', 'Belege' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<div class="wl-referenzen" id="referenzen" data-track-section="proof">
				<h2 class="st-h2" id="pruefstand-h"><span id="proof-h">Projekte und mein Beitrag.</span></h2>
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
				<p class="wl-referenzen__satz">White-Label-Kunden nenne ich nur mit Freigabe der Agentur. Für eine erste gemeinsame Aufgabe gibt es den <a class="st-link" href="#test-sprint" data-track-action="cta_whitelabel_proof_test_sprint" data-track-category="lead_gen" data-track-section="proof">Test-Sprint</a>.</p>
				<p class="wl-referenzen__satz"><a class="st-link" href="<?php echo esc_url( hu_get_commercial_route( 'references' ) ); ?>" data-track-action="whitelabel_references_overview" data-track-category="proof" data-track-section="proof">Projektaufgaben und meinen Beitrag ansehen <span aria-hidden="true">→</span></a></p>
			</div>

			<div id="pruefstand">
				<div class="st-tafel__kopf">
					<h3 class="st-h3">Gebaut. Gemessen. Abgenommen.</h3>
					<p class="st-vorspann">Für euer Entwicklungsteam: der Code dieser Website, die Prüfungen vor dem Livegang und die Ladezeit zum Nachmessen.</p>
				</div>
				<ol class="st-pruefungen">
					<li>
						<p class="st-pruefungen__label">Quellcode</p>
						<p class="st-pruefungen__text">Im öffentlichen Repository seht ihr, wie ich das Theme entwickle und welche Änderungen in jede Version eingeflossen sind.</p>
						<a class="st-link" href="<?php echo esc_url( $github_url . '/commits/main/' ); ?>" target="_blank" rel="noopener" data-track-action="whitelabel_proof_repo" data-track-category="proof" data-track-section="pruefstand">Code und Änderungen auf GitHub&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
					</li>
					<li>
						<p class="st-pruefungen__label">Prüfungen</p>
						<p class="st-pruefungen__text">Vor jedem Livegang prüft die CI unter anderem PHP-Syntax, statische Analyse, strukturierte Daten und eine Sperrliste veralteter Preise und Zusagen. Schlägt eine Prüfung fehl, geht nichts live.</p>
						<a class="st-link" href="<?php echo esc_url( $github_url . '/actions' ); ?>" target="_blank" rel="noopener" data-track-action="whitelabel_proof_ci" data-track-category="proof" data-track-section="pruefstand">Prüfläufe auf GitHub&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
					</li>
					<li>
						<p class="st-pruefungen__label">Ladezeit</p>
						<p class="st-pruefungen__text">Öffnet den Bericht in PageSpeed Insights für eine unabhängige Messung dieser Seite.</p>
						<p class="wl-lcp" data-wl-lcp-proof hidden>Dieser Besuch: <span data-wl-lcp-proof-label>LCP</span> <span data-wl-lcp-kopie></span>.</p>
						<a class="st-link" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="whitelabel_proof_pagespeed" data-track-category="proof" data-track-section="pruefstand">PageSpeed jetzt messen&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
					</li>
				</ol>
			</div>
		</div>
	</section>

	<section class="st-abschnitt wl-ablauf-abschnitt" id="zusammenarbeit" aria-labelledby="zusammenarbeit-h" data-st-abschnitt="04" data-track-section="process">
		<?php echo $marke( '04', 'Ablauf' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="st-inhalt st-zweispaltig">
			<div><h2 class="st-h2" id="zusammenarbeit-h">So arbeiten wir <span class="st-leiser">unter eurem Namen.</span></h2>
				<p class="st-vorspann">Ihr führt das Kundenprojekt und rechnet mit eurem Kunden ab. Ich arbeite in euren Accounts und komme auf euren Wunsch zu technischen Gesprächen dazu.</p>
				<div class="st-einwand"><p><strong>Kundenschutz.</strong> Direkte Anfragen eures Kunden verweise ich an euch, während der Zusammenarbeit und <?php echo esc_html( hu_whitelabel_delivery_promise( 'customer_protection' ) ); ?> danach. In eurem Kundenstamm mache ich keine Akquise.</p></div>
			</div>
			<ol class="wl-ablauf">
				<?php foreach ( $process as $i => $step ) : ?>
					<li class="wl-ablauf__station"><span class="wl-ablauf__nr"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><p class="wl-ablauf__name"><?php echo esc_html( $step[0] ); ?></p><p class="wl-ablauf__text"><?php echo esc_html( $step[1] ); ?></p></li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="st-abschnitt st-leistungen st-wash wl-preise" id="einstieg" aria-labelledby="einstieg-h" data-st-abschnitt="05" data-track-section="entry">
		<?php echo $marke( '05', 'Preise' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="einstieg-h">Agenturpreise <span class="st-leiser">für euer Kundenangebot.</span></h2>
			<p class="st-vorspann">Eine abgegrenzte WordPress-Aufgabe könnt ihr im Test-Sprint vergeben. Für größere Vorhaben dienen die Ab-Preise zur ersten Kalkulation. Den Festpreis für euren Umfang bekommt ihr vor der Beauftragung. Alle Beträge sind netto.</p>
			<div class="st-angebote">
				<?php foreach ( $entry_projects as $i => $project ) : ?>
					<article class="st-angebot" id="<?php echo esc_attr( $project['id'] ); ?>" aria-labelledby="<?php echo esc_attr( $project['id'] . '-h' ); ?>">
						<span class="st-angebot__nr" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
						<div><h3 class="st-angebot__titel" id="<?php echo esc_attr( $project['id'] . '-h' ); ?>"><?php echo esc_html( $project['name'] ); ?></h3><p class="st-angebot__lage"><?php echo esc_html( $project['lage'] ); ?></p></div>
						<div class="st-angebot__text"><p><?php echo esc_html( $project['text'] ); ?></p><?php if ( ! empty( $project['more'] ) ) : ?><p class="st-angebot__mehr"><?php echo esc_html( $project['more'] ); ?></p><?php endif; ?></div>
						<div class="st-angebot__preise"><p class="st-angebot__preis"><?php echo esc_html( $fest( hu_whitelabel_price( $project['key'], 'test_sprint' === $project['key'] ? 'display_fixed' : 'display' ) ) ); ?></p><p class="st-angebot__wege"><a class="st-link st-link--stark" href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-track-action="<?php echo esc_attr( $project['track'] ); ?>" data-track-category="lead_gen" data-track-section="entry"><?php echo esc_html( $project['name'] ); ?> anfragen <span aria-hidden="true">→</span></a></p></div>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="st-weiter" id="kontingent"><span class="st-klein-label">05</span><p><strong>Monatskontingent nach einem erfolgreichen Erstprojekt.</strong> <?php echo esc_html( $retainer_price ); ?> für vereinbarte Aufgaben. Prioritäten und Kündigungsfrist klären wir vor dem Start.</p><a class="st-link" href="#absicherung" data-track-action="whitelabel_offer_retainer_more" data-track-category="navigation" data-track-section="entry">Übergabe und Kündigung <span aria-hidden="true">↓</span></a></div>
			<?php foreach ( $margin_rows as $row ) : ?>
				<figure class="st-messtafel messtafel tafel wl-marge" id="marge" aria-labelledby="marge-h" data-wl-marge>
					<div class="st-messtafel__kopf"><h3 class="st-klein-label" id="marge-h">Messwerte · Endkundenpreis gegen Agenturpreis</h3><span class="st-klein-label">maßstäblich</span></div>
					<p class="wl-marge__name"><?php echo esc_html( $row['name'] ); ?> · gleicher Umfang</p>
					<div class="st-vergleich"><div class="st-vergleich__kopf"><span>Endkunden, öffentlich</span><strong><?php echo esc_html( $fest( $row['retail_display'] ) ); ?></strong></div><div class="st-balken" aria-hidden="true"><i style="--st-anteil:<?php echo esc_attr( $row['retail_ratio'] ); ?>"></i></div></div>
					<div class="st-vergleich st-vergleich--nach"><div class="st-vergleich__kopf"><span>Agenturen, ab</span><strong><?php echo esc_html( $fest( $row['agency_display'] ) ); ?></strong></div><div class="st-balken" aria-hidden="true"><i style="--st-anteil:<?php echo esc_attr( $row['agency_ratio'] ); ?>"></i></div></div>
					<div class="st-skala" aria-hidden="true"><?php foreach ( $row['scale_ticks'] as $tick ) : ?><span style="--st-position:<?php echo esc_attr( $tick['position'] ); ?>"><?php echo esc_html( $fest( $tick['display'] ) ); ?></span><?php endforeach; ?></div>
					<dl class="st-kennzahlen"><div><dt>unter dem Endkundenpreis</dt><dd class="st-kennzahl"><?php echo esc_html( $fest( $row['discount_display'] ) ); ?></dd></div><div><dt>Abstand zum Endkundenpreis</dt><dd class="st-kennzahl"><?php echo esc_html( $fest( $row['difference_display'] ) ); ?></dd></div></dl>
					<figcaption>Den Endkundenpreis nenne ich öffentlich auf meiner Seite zum <a href="<?php echo esc_url( $tracking_b2b_url ); ?>" data-track-action="whitelabel_margin_reference" data-track-category="proof" data-track-section="entry">Server-Side Tracking</a>. Verglichen wird nur, was denselben Umfang hat. Was ihr eurem Kunden berechnet, legt ihr fest.</figcaption>
				</figure>
			<?php endforeach; ?>
			<?php if ( $calc_first ) : ?>
				<div class="wl-rechner" id="rechner" role="group" aria-labelledby="rechner-h" data-wl-rechner hidden>
					<div class="wl-rechner__kopf">
						<h3 class="st-h3" id="rechner-h">Wie viel bleibt für eure eigene Arbeit?</h3>
						<p>Wählt die Leistung und zieht den Regler auf den Betrag, den ihr eurem Kunden berechnet.</p>
					</div>
					<fieldset class="wl-rechner__wahl">
						<legend class="st-klein-label">Leistung</legend>
						<?php foreach ( $calc_rows as $calc_row ) : ?>
							<label for="wl-rechner-<?php echo esc_attr( $calc_row['key'] ); ?>"><input type="radio" id="wl-rechner-<?php echo esc_attr( $calc_row['key'] ); ?>" name="wl-rechner-leistung" value="<?php echo esc_attr( $calc_row['key'] ); ?>" data-agentur="<?php echo esc_attr( (string) $calc_row['agency'] ); ?>" data-start="<?php echo esc_attr( (string) $calc_row['start'] ); ?>"<?php echo $calc_row['public'] ? ' data-oeffentlich' : ''; ?><?php checked( $calc_row['key'], $calc_first['key'] ); ?> data-track-action="whitelabel_calc_product" data-track-label="<?php echo esc_attr( $calc_row['key'] ); ?>" data-track-category="engagement" data-track-section="entry"><span><?php echo esc_html( $calc_row['name'] ); ?></span></label>
						<?php endforeach; ?>
					</fieldset>
					<div class="wl-rechner__regler">
						<label for="wl-rechner-preis">Euer Preis an den Kunden, netto</label>
						<output for="wl-rechner-preis" data-wl-rechner-preis><?php echo esc_html( $fest( hu_format_eur( $calc_first['start'] ) ) ); ?></output>
						<input type="range" id="wl-rechner-preis" min="<?php echo esc_attr( (string) $calc_first['agency'] ); ?>" max="<?php echo esc_attr( (string) ( (int) ceil( $calc_first['agency'] * 3 / 100 ) * 100 ) ); ?>" step="10" value="<?php echo esc_attr( (string) $calc_first['start'] ); ?>" aria-describedby="wl-rechner-hinweis">
						<p class="wl-rechner__hinweis" id="wl-rechner-hinweis" data-wl-rechner-hinweis>Startwert: mein öffentlicher Endkundenpreis für denselben Umfang.</p>
					</div>
					<div class="wl-rechner__balken" aria-hidden="true"><i data-wl-rechner-balken></i></div>
					<dl class="wl-rechner__ergebnis" aria-live="polite">
						<div><dt>Mein Agenturpreis, ab</dt><dd data-wl-rechner-agentur><?php echo esc_html( $fest( hu_format_eur( $calc_first['agency'] ) ) ); ?></dd></div>
						<div class="wl-rechner__marge"><dt>Differenz vor eigenen Kosten</dt><dd data-wl-rechner-marge><?php echo esc_html( $fest( hu_format_eur( $calc_first['start'] - $calc_first['agency'] ) ) ); ?></dd></div>
						<div><dt>Anteil an eurem Preis</dt><dd data-wl-rechner-anteil><?php echo esc_html( $fest( (string) (int) round( ( $calc_first['start'] - $calc_first['agency'] ) / $calc_first['start'] * 100 ) . ' %' ) ); ?></dd></div>
					</dl>
					<p class="wl-rechner__notiz">Die Rechnung nutzt den jeweiligen Ab-Preis. Aus der Differenz deckt ihr unter anderem eure Projektleitung und weiteren eigenen Aufwand. Euer Verkaufspreis bleibt in eurem Browser.</p>
				</div>
			<?php endif; ?>
			<div class="st-folge"><p>Noch in der Angebotsphase? Ich sage euch, ob es machbar ist und was es kostet, bevor ihr zusagt. Kostenlos.</p><a class="tun" href="<?php echo esc_url( $wl_form_offer_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_offers_assessment" data-track-category="lead_gen" data-track-section="entry">Vorhaben einschätzen lassen <span class="pf" aria-hidden="true">→</span></a></div>
		</div>
	</section>

	<section class="st-abschnitt wl-absicherung" id="absicherung" aria-labelledby="absicherung-h" data-st-abschnitt="06" data-track-section="absicherung">
		<?php echo $marke( '06', 'Absicherung' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="st-inhalt">
			<div class="st-zweispaltig">
				<div><h2 class="st-h2" id="absicherung-h">Euer Team kann <span class="st-leiser">selbst weiterarbeiten.</span></h2><p class="st-vorspann">Auch wenn ein anderer Entwickler übernimmt, findet er den aktuellen Stand, die Zugänge und die offenen Aufgaben bei euch.</p></div>
				<ol class="wl-ablauf"><?php foreach ( $safeguards as $i => $step ) : ?><li class="wl-ablauf__station"><span class="wl-ablauf__nr"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><p class="wl-ablauf__name"><?php echo esc_html( $step[0] ); ?></p><p class="wl-ablauf__text"><?php echo esc_html( $step[1] ); ?></p></li><?php endforeach; ?></ol>
			</div>
			<p><a class="st-link" href="<?php echo esc_url( home_url( '/wordpress-projekte-auslagern/' ) ); ?>" data-track-action="whitelabel_abnahme_leitfaden" data-track-category="navigation" data-track-section="absicherung">So prüft ihr auf Staging, ob ein WordPress-Projekt bereit für die Abnahme ist.</a></p>
			<p id="eignung">Ihr sucht Unterstützung für eure eigene Unternehmenswebsite? <a class="st-link" href="<?php echo esc_url( $wl_home_url ); ?>" data-track-action="nav_whitelabel_door_home" data-track-category="navigation" data-track-section="absicherung">Zum Angebot für Unternehmen <span aria-hidden="true">→</span></a></p>
		</div>
	</section>

	<section class="st-abschnitt st-fragen st-wash" id="faq" aria-labelledby="faq-h" data-st-abschnitt="07" data-track-section="faq">
		<?php echo $marke( '07', 'Fragen' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt st-zweispaltig">
			<h2 class="st-h2" id="faq-h">Fragen zur White-Label-Zusammenarbeit.</h2>
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
		<div class="st-inhalt"><p class="st-ende"><span>Ende der Strecke</span><span class="st-ende__zeit" data-st-ende-zeit hidden></span></p></div>
		<div class="st-inhalt wl-anfrage__raster">
			<div class="wl-anfrage__text">
				<h2 class="st-h2 st-anfrage__h2" id="anfrage-h">Was steht bei euch an?</h2>
				<p class="st-vorspann">Nennt mir euer Ziel, den aktuellen Stand und euren Wunschtermin. Ich antworte persönlich <?php echo esc_html( $response_short ); ?> mit einer Einschätzung oder konkreten Rückfragen.</p>
				<ul class="wl-wege">
					<li>
						<p>Ihr steckt noch in der Angebotsphase? Dann sage ich euch, ob es machbar ist und was es kostet, bevor ihr eurem Kunden zusagt.</p>
						<a class="st-link st-link--stark" href="<?php echo esc_url( $wl_form_offer_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_way_offer" data-track-category="lead_gen" data-track-section="naechster_schritt">Vorhaben zur Einschätzung beschreiben&nbsp;<span aria-hidden="true">→</span></a>
					</li>
					<li>
						<p>Gerade kein Projekt, aber bald wieder eins? Dann merke ich euch vor.</p>
						<a class="st-link st-link--stark" href="<?php echo esc_url( $wl_form_later_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_way_later" data-track-category="lead_gen" data-track-section="naechster_schritt">Für das nächste Projekt vormerken&nbsp;<span aria-hidden="true">→</span></a>
					</li>
					<li>
						<p>Lieber zuerst sprechen?</p>
						<a class="st-link st-link--stark" href="<?php echo esc_url( $whitelabel_fit_url ); ?>" data-track-action="cta_whitelabel_form_call" data-track-category="lead_gen" data-track-section="naechster_schritt"><?php echo esc_html( hu_whitelabel_delivery_promise( 'call' ) ); ?> buchen&nbsp;<span aria-hidden="true">↗</span></a>
					</li>
				</ul>

			</div>

			<div class="wl-request" id="aufgabe">
				<div class="wl-request__head">
					<p class="st-klein-label" data-wl-case-label aria-live="polite"><?php echo esc_html( $wl_case_texts['aufgabe']['label'] ); ?></p>
					<h3 class="st-h3">Beschreibt euer Vorhaben.</h3>
					<p class="wl-request__pflicht">Pflicht sind nur das Textfeld und eure E-Mail.</p>
				</div>
				<div id="wl-form-errors" class="wl-request__error-summary is-hidden" role="alert" tabindex="-1" data-wl-error-summary><strong>Bitte prüft eure Angaben.</strong><ul data-wl-error-list></ul></div>
				<form class="wl-request__form" data-wl-request-form data-wl-case-texts="<?php echo esc_attr( (string) wp_json_encode( $wl_case_texts ) ); ?>" action="<?php echo esc_url( $wl_form_endpoint ); ?>" method="post" novalidate>
					<div class="wl-request__honeypot" aria-hidden="true"><label for="wl-company-website">Website</label><input id="wl-company-website" name="company_website" type="text" tabindex="-1" autocomplete="off"></div>
					<input type="hidden" name="case" value="aufgabe" data-wl-case>
					<input type="hidden" name="ads_source" id="wl-ads-source" value="">
					<input type="hidden" name="utm_campaign" id="wl-utm-campaign" value="">
					<div class="wl-request__field"><label for="wl-task"><span data-wl-task-label><?php echo esc_html( $wl_case_texts['aufgabe']['field'] ); ?></span> <span>(Pflichtfeld)</span></label><textarea id="wl-task" name="task" rows="5" required minlength="12" maxlength="4000" aria-describedby="wl-task-hint" placeholder="<?php echo esc_attr( $wl_case_texts['aufgabe']['placeholder'] ); ?>"></textarea><p id="wl-task-hint" class="wl-request__hint"><?php echo esc_html( $wl_case_texts['aufgabe']['hint'] ); ?></p></div>
					<div class="wl-request__field"><label for="wl-email">Eure geschäftliche E-Mail <span>(Pflichtfeld)</span></label><input id="wl-email" name="email" type="email" required autocomplete="email" inputmode="email" placeholder="name@agentur.de"></div>
					<div class="wl-request__field"><label for="wl-timeframe">Gewünschter Zeitraum <span>(optional)</span></label><input id="wl-timeframe" name="timeframe" type="text" maxlength="160" placeholder="Zum Beispiel: ab nächstem Monat" autocomplete="off"></div>
					<div class="wl-request__field"><label for="wl-referral">Wie seid ihr auf mich aufmerksam geworden? <span>(optional)</span></label><select id="wl-referral" name="referral_source"><option value="">Bitte wählen</option><?php foreach ( $wl_referral_options as $value => $label ) : ?><option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></div>
					<details class="wl-request__optional"><summary>Schon Informationen zu den Zugängen? <span>(optional)</span></summary><fieldset class="wl-request__access"><legend>Sind die benötigten Zugänge verfügbar?</legend><div class="wl-request__choices"><?php foreach ( $wl_access_options as $value => $label ) : ?><label for="wl-access-<?php echo esc_attr( $value ); ?>"><input id="wl-access-<?php echo esc_attr( $value ); ?>" name="access" type="radio" value="<?php echo esc_attr( $value ); ?>"><span><?php echo esc_html( $label ); ?></span></label><?php endforeach; ?></div></fieldset></details>
					<button class="tun" type="submit" data-wl-submit disabled><?php echo esc_html( $wl_case_texts['aufgabe']['submit'] ); ?></button>
					<p class="wl-request__legal">Mit dem Absenden fragt ihr unverbindlich an. Eure Angaben nutze ich zur Beantwortung. Details in der <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.</p>
					<div class="wl-request__feedback" data-wl-feedback aria-live="polite" role="status" tabindex="-1"></div>
				</form>
				<noscript><p class="wl-request__ohne-js">Ohne JavaScript sendet das Formular nicht. Schickt eure Aufgabe dann an <a href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>.</p></noscript>
				<p class="wl-request__fallback">Ihr habt meine E-Mail schon? Antwortet mir direkt darauf oder schreibt an <a href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a></p>
			</div>
		</div>
	</section>

	<div class="wl-sticky-cta" id="wl-sticky-cta" aria-hidden="true"><a href="<?php echo esc_url( $wl_form_task_url ); ?>" class="tun" data-wl-form-link data-track-action="cta_sticky_whitelabel_task_brief" data-track-category="lead_gen" data-track-section="sticky_mobile" tabindex="-1">Projekt einschätzen lassen <span class="pf" aria-hidden="true">→</span></a></div>
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
