<?php
/**
 * Template Name: Whitelabel & Weiterentwicklung
 * Description: White-Label-Partner-Funnel für Agenturen. Zehn Sektionen:
 *              Lieferfelder und Marge vor dem Ablauf, Kontrakt und prüfbare
 *              Belege vor der Einordnung, Abschluss über drei gleichrangige
 *              Wege statt über ein vorgeschaltetes Quiz.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$whitelabel_fit_url = function_exists( 'nexus_get_whitelabel_calendar_url' )
	? nexus_get_whitelabel_calendar_url()
	: 'https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true';
$contact_email       = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';

// ── Der zweite Ausgang ist jetzt ein Formular, kein mailto ──────
// Der Sekundaer-CTA war ein `mailto:` mit vorformuliertem Body. Am
// Firmenrechner oeffnet das ein Mailprogramm, das viele nicht nutzen, oder gar
// nichts — und ein mailto-Klick ist kein Abschluss, sondern ein Klick. Die
// Conversion dieser Route war damit nicht messbar.
//
// Das Formular liegt in Sektion 10 und wird ueber `?case=` vorbelegt, damit die
// drei Wege im Posteingang unterscheidbar bleiben. Der Endpunkt gehoert der
// Route allein (inc/whitelabel-request.php) und feuert ein eigenes Event.
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

// Das Antwortversprechen steht an mehreren Stellen der Seite (Kontrakt-Karte 02
// samt Bullet, Founder-Chip, FAQ "kapazitaet", Eckdaten). Die Zahl kommt aus
// canon/messaging-canon.php: dieselbe Zusage endet auf /kontakt/.
$task_brief_response = function_exists( 'hu_response_promise' )
	? hu_response_promise()
	: 'Antwort innerhalb von 24 Stunden werktags';

$imprint_url  = home_url( '/impressum/' );
$privacy_url  = home_url( '/datenschutz/' );
$current_year = wp_date( 'Y' );

/*
 * Rückwege in die Site.
 *
 * Die Route hatte eine geschlossene Seitennavigation und dazu eine Wortmarke
 * als <span>: kein Weg zurück zur Startseite, und die eine Stelle, an der jeder
 * Besucher ihn zuerst sucht, war tot.
 *
 * Der Fuss trug zusaetzlich "WordPress Freelancer" und "Solar & Wärmepumpen".
 * Fuer einen Agenturbesucher sind das die zwei irrelevantesten Ziele der
 * Website — und sie erzeugen genau den Wettbewerbsgedanken, den der Margenblock
 * in Sektion 03 gerade aufloest. Es bleiben Startseite, Impressum, Datenschutz.
 */
$wl_routes     = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$wl_home_url   = $wl_routes['home'] ?? home_url( '/' );
$wl_brand_text = function_exists( 'hu_get_site_wordmark_text' ) ? hu_get_site_wordmark_text() : 'HAŞIM ÜNER';
$wl_home_label = sprintf(
	/* translators: %s: site or brand name. */
	__( 'Startseite - %s', 'blocksy-child' ),
	$wl_brand_text
);

// Diese Route hat eine eigene, geschlossene Seitennavigation. Der globale
// Header bleibt für alle anderen Requests unverändert registriert.
remove_action( 'wp_body_open', 'nexus_render_site_header', 20 );
add_action(
	'wp_body_open',
	static function () use ( $wl_home_url, $wl_brand_text, $wl_home_label, $wl_form_task_url ) {
		?>
		<a class="wl-skip-link" href="#main">Direkt zum Inhalt</a>
		<header class="wl-site-header" role="banner" data-track-section="whitelabel_header">
			<div class="nx-container">
				<div class="wl-site-header__shell">
					<a
						class="site-logo wl-site-header__brand"
						href="<?php echo esc_url( $wl_home_url ); ?>"
						rel="home"
						aria-label="<?php echo esc_attr( $wl_home_label ); ?>"
						data-track-action="nav_whitelabel_home"
						data-track-category="navigation"
						data-track-section="whitelabel_header"
					>
						<?php echo esc_html( $wl_brand_text ); ?>
					</a>

					<nav class="wl-site-header__nav" aria-label="Navigation auf dieser Seite">
						<ul role="list">
							<li><a href="#lieferfelder" data-track-action="nav_whitelabel_services" data-track-category="navigation" data-track-section="whitelabel_header">Lieferfelder</a></li>
							<li><a href="#einstieg" data-track-action="nav_whitelabel_pricing" data-track-category="navigation" data-track-section="whitelabel_header">Preise</a></li>
							<li><a href="#proof" data-track-action="nav_whitelabel_proof" data-track-category="navigation" data-track-section="whitelabel_header">Belege</a></li>
							<li><a href="#faq" data-track-action="nav_whitelabel_faq" data-track-category="navigation" data-track-section="whitelabel_header">FAQ</a></li>
						</ul>
					</nav>

					<a class="wl-site-header__cta" href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_header_task_brief" data-track-category="lead_gen" data-track-section="whitelabel_header">
						<span class="wl-site-header__cta-full">Aufgabe beschreiben</span>
						<span class="wl-site-header__cta-short" aria-hidden="true">Aufgabe</span>
					</a>
				</div>
			</div>
		</header>
		<?php
	},
	20
);

get_header();

$portrait_url = function_exists( 'hu_get_portrait_image_url' )
	? hu_get_portrait_image_url()
	: home_url( '/wp-content/uploads/2026/01/Hasim-Uener-Prtraeit_Startseite.webp' );

$about_url = function_exists( 'nexus_get_page_url' )
	? nexus_get_page_url( [ 'hasim-uener', 'uber-mich' ], home_url( '/hasim-uener/' ) )
	: home_url( '/hasim-uener/' );

$stack_agentur_url = home_url( '/stack-agentur/' );
$outsourcing_url   = home_url( '/wordpress-projekte-auslagern/' );
$tracking_b2b_url  = home_url( '/server-side-tracking-b2b/' );

$test_sprint_price      = hu_whitelabel_price( 'test_sprint' );
$test_sprint_price_card = hu_whitelabel_price( 'test_sprint', 'display_fixed', $test_sprint_price );
$tracking_audit_price   = hu_whitelabel_price( 'tracking_audit', 'display', 'Festpreis nach Umfangsklärung' );
$server_side_price      = hu_whitelabel_price( 'server_side', 'display', 'Festpreis nach Umfangsklärung' );
$landingpage_price      = hu_whitelabel_price( 'landingpage', 'display', 'Festpreis nach Umfangsklärung' );
$retainer_price_hours   = hu_whitelabel_price( 'retainer', 'display_hours', hu_whitelabel_price( 'retainer' ) );

// ── Prüfbare Arbeiten statt anonymisierter Zahlen ───────────────
// Der Kennzahlen-Kasten (150 € → 22 €, 1.750+, Abschlussquote) ist von dieser
// Route entfernt. Grund: e3-newenergy.de steht zwei Absaetze weiter unten als
// klickbare Live-Referenz. Anonymisierter Case plus namentliche Referenz heben
// die Anonymisierung gegenseitig auf. Aus demselben Grund fuehrt diese Seite
// keinen Link auf die Case Study — sonst entstuende dieselbe Verbindung einen
// Klick weiter. Die Kennzahlen auf den Solar-Routen bleiben unveraendert.
//
// Beschrieben wird jeweils die Aufgabe, nicht der Kunde und nicht der Inhalt.
$proof_references = [
	[
		'label' => 'civaka-azad.org',
		'url'   => 'https://civaka-azad.org/',
		'note'  => 'Informationsarchitektur für einen großen, über Jahre gewachsenen redaktionellen Bestand: Navigation, Archive und interne Verweise so strukturiert, dass ältere Beiträge auffindbar bleiben.',
	],
	[
		'label' => 'e3-newenergy.de',
		'url'   => 'https://e3-newenergy.de/',
		// Bewusst ohne Kennzahlen: die Seite steht hier als Referenz, nicht als
		// Beleg. Auch bewusst ohne Aussage zur Code-Ebene — die Seite läuft heute
		// auf einem Page-Builder, und der Block daneben wirbt mit individuellen
		// Templates.
		'note'  => 'Solar und Wärmepumpe mit erklärungsbedürftigem Angebot: eine Strecke, die Beratung, Angebotsanfrage und Kontakt zusammenführt, statt sie über die Seite zu verteilen.',
	],
	[
		'label' => 'hasimuener.org',
		'url'   => 'https://hasimuener.org/',
		'note'  => 'Editorial Design: Typografie, Raster und Lesefluss als eigentliche Aufgabe — Layout, das ohne Bildmaterial trägt.',
	],
];

// ── Ablauf-Kette: vier Stationen eines Mandats ──────────────────
// Stand bis hierher im Hero und nahm dort den Platz ein, den die Aussage
// braucht. Sie gehoert zum Ablauf, also in die Sektion, die den Ablauf erklaert.
$flow_steps = [
	[ 'num' => '01', 'title' => 'Ihr Kunde', 'desc' => 'Anfrage an eure Agentur' ],
	[ 'num' => '02', 'title' => 'Eure Agentur', 'desc' => 'Kundenkontakt bleibt bei euch' ],
	[ 'num' => '03', 'title' => 'Umsetzung im Hintergrund', 'desc' => 'Tracking · Landingpage · CRO' ],
	[ 'num' => '04', 'title' => 'Ergebnis unter eurem Namen', 'desc' => 'Euer Branding, eure Marge' ],
];

// ── Erstprojekte: fixer Scope, Festpreis vorab (Einstieg vor Retainer) ──
// Die Test-Sprint-Detailsektion ist entfallen: ihr Inhalt stand dreimal auf der
// Seite (eigene Sektion, Angebotskarte, FAQ 2). Er steht jetzt in der Karte.
$entry_projects = [
	[
		'key'       => 'testsprint',
		'tag'       => 'WordPress',
		'title'     => 'WordPress-Test-Sprint',
		'price'     => $test_sprint_price_card,
		'copy'      => 'Eine vorab schriftlich abgegrenzte technische Aufgabe als kleinster Einstieg in die erste Zusammenarbeit.',
		'card_line' => 'Eine abgegrenzte Aufgabe zum Festpreis — inklusive Umsetzung, Funktionstest, technischer Dokumentation und einer Korrekturrunde.',
		'note'      => 'Max. ein Arbeitstag. Der Aufgabenumfang wird vor Beginn schriftlich festgelegt; Erweiterungen sind ein eigenes Angebot.',
	],
	[
		'key'     => 'tracking',
		'tag'     => 'Tracking',
		'title'   => 'Tracking-Audit',
		'price'   => $tracking_audit_price,
		'deliver' => 'Schriftlicher Befund + priorisierte Fixliste',
		'copy'    => 'GA4, GTM und Consent-Bestand eures Kunden geprüft: Was misst, was fehlt, was verfälscht. Danach wisst ihr, worauf jede weitere Maßnahme aufsetzt.',
	],
	[
		'key'     => 'tracking',
		'tag'     => 'Server',
		'title'   => 'Server-Side-Setup',
		'price'   => $server_side_price,
		'deliver' => 'Produktives Setup + Doku + Übergabe',
		'copy'    => 'Eigener Server-Side-Container, Enhanced Conversions, Meta CAPI, Consent Mode V2 — produktiv geschaltet und dokumentiert, nicht nur konfiguriert.',
	],
	[
		'key'     => 'landingpage',
		'tag'     => 'Landingpage',
		'title'   => 'Landingpage',
		'price'   => $landingpage_price,
		'deliver' => 'Live-Page + Doku, bereit für Traffic',
		'copy'    => 'Individuelles Template mit klarer Funnel-Logik, sauberen Core Web Vitals und belastbarer Messbarkeit ab dem ersten Klick.',
	],
];

// ── Pre-Sales-Scoping: greift vor dem Erstprojekt, noch in der Angebotsphase ──
$presales_scoping = [
	'question' => 'Ihr seid noch in der Angebotsphase?',
	'copy'     => 'Ich schätze technische Anforderungen, Aufwand und Machbarkeit vorab mit euch ein, bevor ihr dem Kunden etwas zusagt.',
];

$entry_bullets = [ 'NDA', 'Fixer Scope', 'Festpreis vorab', 'Keine Verlängerungsfalle' ];

$solution_modes = [
	'hintergrund' => [
		'label' => 'Im Hintergrund',
		'copy'  => 'Komplett unsichtbar. Code, Tracking, Doku laufen über euch — euer Ton, euer Branding. Euer Kunde sieht nur das Ergebnis.',
	],
	'kundencall'  => [
		'label' => 'Mit im Kunden-Call',
		'copy'  => 'Als euer Technik-Lead beantworte ich technische Fragen direkt im Call. Rolle, Kommunikation und Freigaben klären wir vorab.',
	],
];

// ── Lieferfelder: drei Bereiche statt Leistungskatalog ──────────
// Landingpages sind bewusst kein vierter Bereich, sondern der Querschnitt
// aus allen dreien (siehe $stack_crosscut).
$stack_fields = [
	[
		'tag'   => 'WordPress',
		'title' => 'WordPress & Technical Delivery',
		'copy'  => 'Individuelle Templates für vorhandene Layouts, Core Web Vitals und technisches SEO — umgesetzt, geprüft und dokumentiert im bestehenden WordPress-Setup.',
		'chips' => [ 'Templates', 'CWV', 'Technical SEO', 'Hardening' ],
		'entry' => 'Typisches Erstprojekt: eine abgegrenzte WordPress-Aufgabe oder ein technischer SEO-Fix.',
	],
	[
		'tag'   => 'Tracking',
		'title' => 'Tracking & Attribution',
		'copy'  => 'GA4 und GTM, eigener Server-Side-Container (Stape/GCP), Enhanced Conversions, Meta CAPI, Consent Mode V2 — die Messkette vom Klick bis zum Abschluss, dokumentiert und nachvollziehbar.',
		'chips' => [ 'GA4', 'GTM-SS', 'CAPI', 'Consent V2' ],
		'entry' => 'Typisches Erstprojekt: Tracking-Audit oder Server-Side-Setup.',
	],
	[
		'tag'   => 'Ops',
		'title' => 'CRM & Automation',
		'copy'  => 'n8n, Make und Zapier, CRM-Integrationen in Bitrix24, HubSpot und Pipedrive, Leadflows und API-Anbindungen — mit Fehlerbehandlung, Doku und Übergabe statt Workflow-Screenshot.',
		'chips' => [ 'n8n', 'CRM', 'Leadflow', 'APIs' ],
		'entry' => 'Typisches Erstprojekt: Lead-Routing bis ins CRM.',
	],
];

$stack_crosscut = 'Landingpages sind kein eigener Bereich. Sie sind der Querschnitt: WordPress-Umsetzung, Messbarkeit und Lead-Routing aus allen drei Feldern in einer Seite.';

$contract_cards = [
	[
		'eyebrow' => '01',
		'title'   => 'Diskret',
		'copy'    => 'NDA standardmäßig, kein eigenes Branding, keine Akquise in eurem Kundenstamm. Die Sichtbarkeit legt ihr fest — vom unsichtbaren Backoffice bis zum Technik-Lead in eurem Call.',
		'bullets' => [ 'NDA inkludiert', 'Kein eigenes Branding', 'Sichtbarkeit: ihr entscheidet' ],
	],
	[
		'eyebrow' => '02',
		'title'   => 'Verbindlich',
		'copy'    => $task_brief_response . '. Verfügbarkeit, Starttermin und Delivery-Fenster werden vor Projektbeginn verbindlich vereinbart. Dringende Aufgaben werden vorab separat priorisiert und bestätigt.',
		'bullets' => [
			function_exists( 'hu_response_promise' ) ? hu_response_promise( 'compact' ) : 'Antwort in 24 Stunden werktags',
			'Starttermin vorab bestätigt',
			'Dringendes separat priorisiert',
		],
	],
	[
		'eyebrow' => '03',
		'title'   => 'Planbar',
		'copy'    => 'Monats-Retainer mit vereinbartem Leistungsrahmen oder feste Projektpreise für abgegrenzte Aufgaben. Saubere Dokumentation schafft klare Übergaben.',
		'bullets' => [ 'Projektpreis vorab', 'Klare Doku', 'Nachvollziehbare Abrechnung' ],
	],
];

$comparison_columns = [ 'Senior einstellen', 'Projektweise extern', 'White-Label-Partner' ];

// Vier Zeilen statt sechs. "Qualität" und "Diskretion" sind gestrichen: beide
// werden im Kontrakt (Sektion 05) und in der FAQ praeziser beantwortet als eine
// Tabellenzelle es kann.
//
// Die mittlere Spalte war so vorsichtig formuliert, dass sie nichts aussagte
// ("wird pro Auftrag vereinbart" in jeder Zeile). Sie benennt jetzt den
// tatsaechlichen Unterschied — Verhandlung pro Auftrag statt einmal geregelt —
// ohne Zahlen zu erfinden und ohne andere Anbieter schlechtzumachen.
$comparison_rows = [
	[
		'label' => 'Verfügbar ab',
		'cells' => [
			'Monate: Suche, Kündigungsfrist, Einarbeitung',
			'Pro Auftrag neu: Ausschreibung, Angebote vergleichen, Briefing aufsetzen',
			'Nach einem Fit-Gespräch und NDA',
		],
	],
	[
		'label' => 'Fixkostenrisiko',
		'cells' => [
			'Volles Gehalt, auch in schwachen Monaten',
			'Keines — dafür beginnt die Auswahl bei jedem Projekt von vorn',
			'Retainer oder Projektpreis — passend zum vereinbarten Bedarf',
		],
	],
	[
		'label' => 'Skill-Breite',
		'cells' => [
			'Eine Person, ein Schwerpunkt',
			'So breit wie das jeweils beauftragte Profil — Schnittstellen koordiniert ihr',
			'SEO, WordPress, Tracking, CRO, Automation aus einer Hand',
		],
	],
	[
		'label' => 'Verantwortung',
		'cells' => [
			'Führung und QA liegen bei euch',
			'Koordination und Abnahme bleiben bei euch, in jedem Auftrag erneut',
			'Lieferung inklusive Doku und Abnahme',
		],
	],
];

$founder_credentials = '8+ Jahre WordPress-Entwicklung · B.A. Medienwissenschaften · Performance-Marketing und Leadgenerierung für D2C und B2B · Hannover';

$founder_chips = [ 'NDA standardmäßig', $task_brief_response ];

$faq_items = nexus_get_whitelabel_faq_items();

$tech_bullets = [
	'Individuelle WordPress-Templates für vorhandene Layouts',
	'Performance-Optimierung: Core Web Vitals, kritischer Renderpfad',
	'Barrierefreiheit: semantisches HTML, Fokusreihenfolge, Kontraste nach WCAG 2.2 AA und vollständige Tastaturbedienung — die technischen Voraussetzungen, an denen Standard-Themes regelmäßig scheitern.',
	'Versionierter Code, dokumentierte Übergabe',
];

$hero_chips = [ 'GA4', 'GTM', 'Server-Side', 'Consent V2', 'WordPress', 'n8n' ];

// ── Eckdaten: maschinenlesbare Zusammenfassung am Ende der FAQ ──
// Zweck ist generative Suche: ohne diesen Block muessen Systeme die Antwort aus
// zehn Sektionen zusammensuchen. Bewusst eine schlichte Definitionsliste und
// kein Kartendesign — der Block ist Zusammenfassung, nicht Angebot.
$wl_facts = [
	[
		'term' => 'Leistungen',
		'desc' => 'WordPress-Entwicklung, technisches SEO, Core Web Vitals, GA4 und GTM, Server-Side-Tracking, Consent Mode V2, Meta CAPI, CRO, Landingpages, CRM-Anbindung, Automation',
	],
	[
		'term' => 'Einstieg',
		'desc' => sprintf( 'WordPress-Test-Sprint, %s, Festpreis, max. ein Arbeitstag', $test_sprint_price ),
	],
	[
		'term' => 'Erstprojekte',
		'desc' => sprintf(
			'Tracking-Audit %1$s, Server-Side-Setup %2$s, Landingpage %3$s',
			str_replace( ' netto', '', $tracking_audit_price ),
			str_replace( ' netto', '', $server_side_price ),
			$landingpage_price
		),
	],
	[
		'term' => 'Laufend',
		'desc' => sprintf(
			'%s, monatlich kündbar',
			hu_whitelabel_price( 'retainer', 'display_hours_plain', 'nach Vereinbarung' )
		),
	],
	[
		'term' => 'Vertragsform',
		'desc' => 'Subunternehmer, Vertrag mit der Agentur, NDA standardmäßig, Auftragsverarbeitung nach DSGVO',
	],
	[
		'term' => 'Antwortzeit',
		'desc' => function_exists( 'hu_response_promise' ) ? hu_response_promise( 'value' ) : '24 Stunden werktags',
	],
	[
		'term' => 'Sichtbarkeit',
		'desc' => 'im Hintergrund oder als Technik-Lead im Kundengespräch, Entscheidung liegt bei der Agentur',
	],
	[
		'term' => 'Sitz',
		'desc' => 'Pattensen bei Hannover, Zusammenarbeit remote im gesamten deutschsprachigen Raum',
	],
];

// ── Drei Wege im Abschluss ──────────────────────────────────────
// Das vorgeschaltete Quiz ist ersatzlos entfallen. Es qualifizierte den
// Anbieter, nicht den Kaeufer, und stand zwischen dem Interessenten und dem,
// was der eigentlich wollte: "3 Fragen, 60 Sekunden" versprach Geschwindigkeit
// und endete in einem 30-Minuten-Termin. Dieselbe Mechanik ist auf der
// Startseite bereits entfernt.
//
// Weg 2 ist der Neuzugang: er unterstuetzt die Agentur in ihrem eigenen
// Verkaufsprozess, statt ihr etwas zu verkaufen, und ist damit
// niedrigschwelliger als jedes Angebot. Der Satz stand bisher als grauer
// Nachsatz auf der Seite.
$wl_ways = [
	[
		'title'  => 'Ihr habt eine konkrete Aufgabe.',
		'copy'   => 'Beschreibt sie in vier Zeilen, ich antworte innerhalb von 24 Stunden werktags mit Einschätzung, Aufwand und Preis.',
		'label'  => 'Aufgabe beschreiben',
		'url'    => $wl_form_task_url,
		'action' => 'cta_whitelabel_way_task',
		'form'   => true,
	],
	[
		'title'  => 'Ihr steckt in der Angebotsphase.',
		'copy'   => 'Schickt mir, was euer Kunde will. Ich sage euch, ob es machbar ist und was es kostet, bevor ihr etwas zusagt. Kostet nichts und ist keine Beauftragung.',
		'label'  => 'Vorhaben schildern',
		'url'    => $wl_form_offer_url,
		'action' => 'cta_whitelabel_way_offer',
		'form'   => true,
	],
	[
		'title'  => 'Ihr wollt erst reden.',
		'copy'   => '30 Minuten, direkt mit mir, kein Deck.',
		'label'  => 'Termin wählen',
		'url'    => $whitelabel_fit_url,
		'action' => 'cta_whitelabel_way_call',
		'form'   => false,
	],
];

$wl_access_options = function_exists( 'hu_whitelabel_request_access_options' )
	? hu_whitelabel_request_access_options()
	: [
		'ja'        => 'Ja',
		'teilweise' => 'Teilweise',
		'nein'      => 'Nein',
	];
?>

<div class="wl-page" data-track-section="whitelabel_page">

	<noscript>
		<style>
			.wl-page .nx-reveal,
			.wl-page .reveal-stagger > * { opacity: 1; transform: none; }
		</style>
	</noscript>

	<!-- ═══════════════════════════════════════════════
	     SECTION 01 — HERO
	     "Ihr verkauft es, ich liefere es" ist die Definition von White Label:
	     jeder Wettbewerber sagt das. Die eine Aussage, die im gesichteten
	     DACH-Feld niemand kopieren kann, ist die Kombination aus Umsetzung und
	     Messkette in einer Person. Sie stand bisher in Sektion 7.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-hero" data-nx-theme="dark" id="hero">
		<div class="wl-hero__bg" aria-hidden="true">
			<div class="wl-hero__bg-warmth"></div>
			<div class="wl-hero__bg-vignette"></div>
			<div class="wl-hero__bg-grid"></div>
		</div>

		<div class="nx-container">
			<header class="wl-hero__top">
				<span class="wl-hero__status">
					<span class="wl-status-dot" aria-hidden="true"></span>
					NDA standardmäßig
				</span>
			</header>

			<div class="wl-hero__grid wl-hero__grid--solo">
				<div class="wl-hero__copy">
					<?php /* Der Kicker trägt „White-Label“ und „Agenturen“ für Suche und
					        Entity-Erkennung, die H1 trägt die Aussage. */ ?>
					<span class="wl-eyebrow wl-hero__kicker">White-Label für Agenturen</span>
					<h1 class="wl-hero__title">
						Gebaut und gemessen von derselben Person.
					</h1>
					<p class="wl-hero__lede">
						WordPress-Umsetzung, technisches SEO und die vollständige Messkette — GA4, Server-Side, Consent Mode V2, CRM-Anbindung. Keine Übergabe zwischen Entwicklung und Tracking, weil es keine zweite Partei gibt.
					</p>

					<div class="wl-hero__actions">
						<a href="<?php echo esc_url( $wl_form_task_url ); ?>" class="nx-btn nx-btn--primary" data-wl-form-link data-track-action="cta_whitelabel_hero_task_brief" data-track-category="lead_gen" data-track-section="hero">
							<span>Aufgabe beschreiben</span>
							<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
								<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
						<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn wl-btn--task" data-track-action="cta_whitelabel_hero_call" data-track-category="lead_gen" data-track-section="hero">
							<span>30 Minuten buchen</span>
						</a>
					</div>

					<p class="wl-hero__fineprint">
						NDA ab dem ersten Gespräch · Zugänge bleiben in euren Accounts · Vertrag mit eurer Agentur, nicht mit eurem Kunden
					</p>

					<ul class="wl-hero__chips" role="list" aria-label="Stack-Komponenten">
						<?php foreach ( $hero_chips as $chip ) : ?>
							<li><span class="wl-chip"><?php echo esc_html( $chip ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 02 — WAS IHR ZUKAUFT (Lieferfelder)
	     Stand als Sektion 7 unter fünf Abschnitten, die jeder Wettbewerber
	     wörtlich genauso trifft. Der Lückensatz ist das Argument, nicht die
	     Aufzählung darunter.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-stack" id="lieferfelder">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Was ihr zukauft</span>
				<h2 class="nx-headline-section">Drei Felder, die zusammengehören</h2>
				<p class="wl-section-lede">Die meisten Agenturen kaufen die Umsetzung bei einem Anbieter und das Tracking bei einem anderen — oder das Tracking gar nicht. Die Lücke dazwischen fällt erst auf, wenn der Kunde fragt, warum im GA4 andere Zahlen stehen als im Ads-Konto. Ich decke beide Seiten ab. Deshalb gibt es diese Lücke nicht.</p>
			</div>

			<div class="wl-stack__grid reveal-stagger">
				<?php foreach ( $stack_fields as $card ) : ?>
					<article class="wl-stack-card">
						<header class="wl-stack-card__head">
							<span class="wl-stack-card__tag"><?php echo esc_html( $card['tag'] ); ?></span>
						</header>
						<h3 class="wl-stack-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="wl-stack-card__copy"><?php echo esc_html( $card['copy'] ); ?></p>
						<ul class="wl-stack-card__chips" aria-label="Komponenten">
							<?php foreach ( $card['chips'] as $chip ) : ?>
								<li><span class="wl-chip wl-chip--mono"><?php echo esc_html( $chip ); ?></span></li>
							<?php endforeach; ?>
						</ul>
						<p class="wl-stack-card__entry"><?php echo esc_html( $card['entry'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>

			<p class="wl-stack__crosscut nx-reveal"><?php echo esc_html( $stack_crosscut ); ?></p>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 03 — EINSTIEG, PREISE, EURE MARGE
	     Die Problem-Sektion mit drei Kacheln (Kapazität, Tiefe, Marge) ist
	     ersatzlos entfallen: sie beschrieb der Agentur ihren eigenen Alltag.
	     Dieselben drei Gründe stehen jetzt als drei Sätze über dem Angebot.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-entry" id="einstieg">
		<div class="nx-container">
			<p class="wl-entry__intro nx-reveal">
				Drei Gründe, warum Agenturen hier landen: Der Kalender ist voll. Ein technischer Spezialfall blockiert die Abnahme. Oder eine Festanstellung lohnt für den tatsächlichen Bedarf nicht. Alle drei lassen sich mit einem abgegrenzten Erstprojekt prüfen, bevor irgendjemand über eine längere Zusammenarbeit spricht.
			</p>

			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Einstieg</span>
				<h2 class="nx-headline-section">Kein Blind-Retainer. Erst ein Erstprojekt mit fixem Scope.</h2>
				<p class="wl-section-lede">Der WordPress-Test-Sprint ist der kleinste Einstieg zum festen Preis. Bei den größeren Erstprojekten nennt die Karte die Untergrenze; der verbindliche Festpreis steht nach der Umfangsklärung schriftlich fest, bevor die Arbeit beginnt. Ein Retainer entsteht erst nach einem erfolgreichen Erstprojekt.</p>
			</div>

			<div class="wl-entry__presales nx-reveal">
				<p><strong><?php echo esc_html( $presales_scoping['question'] ); ?></strong> <?php echo esc_html( $presales_scoping['copy'] ); ?></p>
			</div>

			<div class="wl-entry__grid reveal-stagger">
				<?php foreach ( $entry_projects as $project ) : ?>
					<article class="wl-entry-card" data-entry-key="<?php echo esc_attr( $project['key'] ); ?>">
						<header class="wl-entry-card__head">
							<span class="wl-entry-card__tag"><?php echo esc_html( $project['tag'] ); ?></span>
							<span class="wl-entry-card__price"><?php echo esc_html( $project['price'] ); ?></span>
						</header>
						<h3 class="wl-entry-card__title"><?php echo esc_html( $project['title'] ); ?></h3>
						<p class="wl-entry-card__copy"><?php echo esc_html( $project['copy'] ); ?></p>
						<?php if ( ! empty( $project['card_line'] ) ) : ?>
							<p class="wl-entry-card__summary"><?php echo esc_html( $project['card_line'] ); ?></p>
						<?php else : ?>
							<dl class="wl-entry-card__meta">
								<div>
									<dt>Lieferung</dt>
									<dd><?php echo esc_html( $project['deliver'] ); ?></dd>
								</div>
							</dl>
						<?php endif; ?>
						<?php if ( ! empty( $project['note'] ) ) : ?>
							<p class="wl-entry-card__note"><?php echo esc_html( $project['note'] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>

			<?php /* Der Margenblock steht direkt unter den Preiskarten: er beantwortet
			        die Frage, die die Karten aufwerfen. Er setzt voraus, dass der
			        White-Label-Preis für Server-Side unter dem öffentlichen
			        Endkundenpreis auf /server-side-tracking-b2b/ liegt — beide Werte
			        kommen aus canon/pricing-canon.php. */ ?>
			<div class="wl-margin nx-reveal">
				<h3 class="wl-margin__title">Was ihr daran verdient</h3>
				<p class="wl-margin__copy">
					Diese Preise liegen rund 30 % unter dem, was ich Endkunden für dieselbe Leistung berechne. Diese Endkundenpreise stehen öffentlich auf <a href="<?php echo esc_url( $tracking_b2b_url ); ?>" data-track-action="link_whitelabel_margin_pricing" data-track-category="internal_link" data-track-section="einstieg">derselben Website</a> — ihr könnt nachrechnen, statt es mir zu glauben. Die Differenz ist eure Marge. Was ihr eurem Kunden dafür berechnet, geht mich nichts an, und ich erfahre es auch nicht.
				</p>
			</div>

			<?php /* Der Retainer stand bisher nur als grauer Nachsatz unter den CTAs und
			        war damit eine Absichtserklärung. Als eigene Karte auf vierter Ebene
			        ist er ein Angebot mit Zahl und Grenze. Ein Stundensatz steht
			        nirgends auf dieser Seite — das Kontingent ist die Einheit. */ ?>
			<article class="wl-entry-card wl-entry-card--retainer nx-reveal">
				<header class="wl-entry-card__head">
					<span class="wl-entry-card__tag">Retainer</span>
					<span class="wl-entry-card__price"><?php echo esc_html( $retainer_price_hours ); ?></span>
				</header>
				<h3 class="wl-entry-card__title">Laufende Kapazität</h3>
				<p class="wl-entry-card__copy">Ein festes Kontingent pro Monat, monatlich kündbar. Entsteht nach einem erfolgreichen Erstprojekt, nicht davor. Ausdrücklich ohne Rufbereitschaft und ohne Reaktionszeit-Zusage über die 24 Stunden werktags hinaus.</p>
			</article>

			<ul class="wl-entry__bullets nx-reveal" aria-label="Rahmen Erstprojekt">
				<?php foreach ( $entry_bullets as $bullet ) : ?>
					<li><span class="wl-entry__bullet-dot" aria-hidden="true"></span><?php echo esc_html( $bullet ); ?></li>
				<?php endforeach; ?>
			</ul>

			<div class="wl-entry__actions nx-reveal">
				<a href="<?php echo esc_url( $wl_form_task_url ); ?>" class="nx-btn nx-btn--primary" data-wl-form-link data-track-action="cta_whitelabel_entry_task_brief" data-track-category="lead_gen" data-track-section="entry">
					Aufgabe beschreiben
				</a>
				<a href="<?php echo esc_url( $whitelabel_fit_url ); ?>" class="nx-btn wl-btn--task" data-track-action="cta_whitelabel_entry_call" data-track-category="lead_gen" data-track-section="entry">
					30 Minuten buchen
				</a>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 04 — WIE ES LÄUFT
	     Ablauf-Kette (vorher im Hero), Sichtbarkeitsmodi (vorher eigene
	     Sektion) und das Briefing-Muster in einer Sektion. Die Modi standen
	     zusätzlich im Ablauf-Diagramm und in Kontrakt-Regel 01.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-solution" data-nx-theme="dark" id="ablauf">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Ablauf</span>
				<h2 class="nx-headline-section">Wie es läuft</h2>
			</div>

			<div class="wl-flow nx-reveal" aria-label="Ablauf eines White-Label-Mandats">
				<header class="wl-flow__head">
					<span class="wl-flow__kicker">Der Ablauf</span>
					<span class="wl-flow__sub">Ein Mandat, vier Stationen</span>
				</header>
				<ol class="wl-flow__list">
					<?php foreach ( $flow_steps as $flow_i => $step ) : ?>
						<li class="wl-flow__step<?php echo 3 === $flow_i ? ' wl-flow__step--final' : ''; ?>">
							<span class="wl-flow__num"><?php echo esc_html( $step['num'] ); ?></span>
							<div class="wl-flow__body">
								<span class="wl-flow__title"><?php echo esc_html( $step['title'] ); ?></span>
								<span class="wl-flow__desc"><?php echo esc_html( $step['desc'] ); ?></span>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>

			<div class="wl-mode" id="wl-mode" data-wl-mode="hintergrund">
				<h3 class="wl-mode__title">Zwei Modi. Volle Kontrolle.</h3>
				<div class="wl-mode__control nx-reveal">
					<fieldset class="wl-mode__switch">
						<legend class="wl-visually-hidden">Arbeitsmodus wählen</legend>
						<?php foreach ( $solution_modes as $mode_key => $mode ) : ?>
							<input
								type="radio"
								name="wl-mode"
								id="wl-mode-<?php echo esc_attr( $mode_key ); ?>"
								class="wl-mode__radio"
								value="<?php echo esc_attr( $mode_key ); ?>"
								<?php checked( 'hintergrund' === $mode_key ); ?>
							/>
							<label
								for="wl-mode-<?php echo esc_attr( $mode_key ); ?>"
								class="wl-mode__label"
								data-track-action="toggle_whitelabel_mode_<?php echo esc_attr( $mode_key ); ?>"
								data-track-category="engagement"
								data-track-section="ablauf"
							><?php echo esc_html( $mode['label'] ); ?></label>
						<?php endforeach; ?>
					</fieldset>
					<p class="wl-mode__hint">Pro Projekt vereinbart und bei Bedarf gemeinsam angepasst.</p>
				</div>

				<div class="wl-mode__panels">
					<?php foreach ( $solution_modes as $mode_key => $mode ) : ?>
						<div class="wl-mode__panel wl-mode__statement" data-mode-panel="<?php echo esc_attr( $mode_key ); ?>" aria-label="<?php echo esc_attr( 'Modus: ' . $mode['label'] ); ?>">
							<p><?php echo esc_html( $mode['copy'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>

				<p class="wl-mode__footer">Egal wie: euer Branding, eure Marge, eure Kundenbeziehung bleiben bei euch.</p>
			</div>

			<div class="wl-briefing nx-reveal">
				<h3 class="wl-briefing__title">Womit ich sofort anfangen kann</h3>
				<p class="wl-briefing__copy">Ein Briefing, mit dem es losgeht, braucht vier Angaben: was am Ende funktionieren soll, einen Zugang zu Staging oder Live, das Design oder die Vorlage, falls es eine gibt, und den Termin, an dem euer Kunde das Ergebnis sieht. Was ich nicht brauche, ist ein Lastenheft. Was fehlt, frage ich per Mail nach — nicht in einem Workshop.</p>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 05 — DER KONTRAKT
	     Inhaltlich unverändert. „Diskret. Verbindlich. Planbar." war eine
	     Adjektivreihe, die jeder Anbieter über sich schreibt; die neue
	     Überschrift benennt, wo die drei Regeln stehen.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-contract" id="kontrakt">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">White-Label-Kontrakt</span>
				<h2 class="nx-headline-section">Drei Regeln, die im Vertrag stehen</h2>
				<p class="wl-section-lede">Drei Regeln, schriftlich im Kontrakt — prüfbar ab der ersten Minute, ohne Sternchen.</p>
			</div>

			<div class="wl-contract__grid reveal-stagger">
				<?php foreach ( $contract_cards as $card ) : ?>
					<article class="wl-contract-card">
						<span class="wl-contract-card__num"><?php echo esc_html( $card['eyebrow'] ); ?></span>
						<h3 class="wl-contract-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="wl-contract-card__copy"><?php echo esc_html( $card['copy'] ); ?></p>
						<ul class="wl-contract-card__bullets">
							<?php foreach ( $card['bullets'] as $bullet ) : ?>
								<li><?php echo esc_html( $bullet ); ?></li>
							<?php endforeach; ?>
						</ul>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 06 — WAS ICH ZEIGEN KANN
	     Erst die drei klickbaren Referenzen, dann der Code, dann die Herkunft
	     der Messtechnik. Wer klicken kann, prüft — statt zu glauben.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-proof" data-nx-theme="dark" id="proof">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Was ich zeigen kann</span>
				<h2 class="nx-headline-section">Keine geliehenen Logos. Drei Umsetzungen, die ihr selbst prüfen könnt.</h2>
				<p class="wl-section-lede">Was ich unter eurem Namen liefere, bleibt unter eurem Namen — NDA. Deshalb steht hier, was offen einsehbar ist.</p>
			</div>

			<div class="wl-proof__refs nx-reveal">
				<h3 class="wl-proof__refs-title">Direkt prüfbar: drei Live-Referenzen</h3>
				<p class="wl-proof__refs-lede">Drei Seiten, die offen einsehbar sind — anschauen und selbst beurteilen, ob das Niveau passt.</p>
				<ul class="wl-proof__refs-list" role="list">
					<?php foreach ( $proof_references as $reference ) : ?>
						<li class="wl-proof__ref">
							<a
								class="wl-proof__ref-link"
								href="<?php echo esc_url( $reference['url'] ); ?>"
								target="_blank"
								rel="noopener noreferrer"
								data-track-action="ref_whitelabel_proof_site"
								data-track-label="<?php echo esc_attr( $reference['label'] ); ?>"
								data-track-category="engagement"
								data-track-section="proof"
							>
								<?php echo esc_html( $reference['label'] ); ?>
								<span class="wl-visually-hidden"> (öffnet in neuem Tab)</span>
							</a>
							<span class="wl-proof__ref-note"><?php echo esc_html( $reference['note'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="wl-tech nx-reveal">
				<h3 class="wl-tech__title">Technischer Beleg</h3>
				<p class="wl-tech__lede">Individuelle Templates, bedarfsgesteuertes Asset-Loading und dokumentierte Übergabe — passend zum vorhandenen WordPress-Setup.</p>

				<div class="wl-tech__split">
					<ul class="wl-tech__bullets">
						<?php foreach ( $tech_bullets as $bullet ) : ?>
							<li><?php echo esc_html( $bullet ); ?></li>
						<?php endforeach; ?>
					</ul>

					<figure class="wl-tech__code" aria-hidden="true">
						<figcaption class="wl-tech__code-head">
							<span class="wl-tech__code-dot wl-tech__code-dot--r"></span>
							<span class="wl-tech__code-dot wl-tech__code-dot--y"></span>
							<span class="wl-tech__code-dot wl-tech__code-dot--g"></span>
							<span class="wl-tech__code-file">inc/enqueue.php</span>
						</figcaption>
<pre class="wl-tech__code-body" tabindex="-1"><span class="wl-tech__c">// Bedarfsgesteuertes Asset-Loading pro Template</span>
<span class="wl-tech__k">if</span> ( is_page_template( <span class="wl-tech__s">'page-whitelabel.php'</span> ) ) {
    hu_enqueue_css( <span class="wl-tech__s">'whitelabel'</span>, <span class="wl-tech__s">'whitelabel.css'</span>, [ <span class="wl-tech__s">'design-system'</span> ] );
    hu_enqueue_js(  <span class="wl-tech__s">'whitelabel'</span>,  <span class="wl-tech__s">'whitelabel.js'</span> );
}

<span class="wl-tech__c">// Server-Side Event auf Lead-Submit</span>
window.dataLayer.push({
    <span class="wl-tech__p">event</span>: <span class="wl-tech__s">'lead_qualified'</span>,
    <span class="wl-tech__p">source</span>: <span class="wl-tech__s">'whitelabel_partner'</span>,
    <span class="wl-tech__p">value</span>: leadValue
});</pre>
					</figure>
				</div>
			</div>

			<?php /* Ersetzt den anonymisierten Case samt Kennzahlen-Kasten. Ohne Zahlen
			        gibt es auch nichts zu disclaimern — die Fußnote ist mitentfallen. */ ?>
			<div class="wl-proof__origin nx-reveal">
				<h3 class="wl-proof__origin-title">Woher die Messtechnik kommt</h3>
				<p class="wl-proof__origin-copy">Für einen mittelständischen PV-Installationsbetrieb habe ich Landingpage, Kampagnensteuerung in Google und Meta Ads und die gesamte Messkette verantwortet: Server-Side-Tracking, Consent Mode V2, CRM-Attribution vom Klick bis zum Abschluss. Kein White-Label-Mandat, sondern ein Projekt, an dem sich zeigen lässt, wie die Teile zusammenspielen.</p>
				<p class="wl-proof__origin-copy">Für euch ist daran nicht das Ergebnis interessant, sondern was es voraussetzt: eine Messkette, die durchhält, auch nach dem dritten Plugin-Update. Genau das baue ich unter eurem Namen.</p>
			</div>

			<p class="wl-proof__docs nx-reveal">
				Stack und Übergabe könnt ihr vorab prüfen: der <a href="<?php echo esc_url( $stack_agentur_url ); ?>" data-track-action="link_whitelabel_stack_agentur" data-track-category="internal_link" data-track-section="proof">Agentur-Stack</a> zeigt Infrastruktur und Deployment, der <a href="<?php echo esc_url( $outsourcing_url ); ?>" data-track-action="link_whitelabel_outsourcing_guide" data-track-category="internal_link" data-track-section="proof">Auslagerungs-Leitfaden</a> Rollen, Übergaben und Kontrollpunkte.
			</p>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 07 — WER LIEFERT
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-founder" id="person">
		<div class="nx-container">
			<div class="wl-founder__grid nx-reveal">
				<figure class="wl-founder__media">
					<img
						class="wl-founder__photo"
						src="<?php echo esc_url( $portrait_url ); ?>"
						alt="Haşim Üner, White-Label-Partner für Agenturen"
						width="640"
						height="800"
						loading="lazy"
						decoding="async"
					/>
				</figure>
				<div class="wl-founder__body">
					<span class="wl-eyebrow">Wer liefert</span>
					<h2 class="nx-headline-section">Eine Person. Kein Ticketsystem.</h2>
					<p class="wl-founder__copy">
						Kein Delivery-Team, keine Vermittlungsplattform. Haşim Üner — Technical SEO, Server-Side-Tracking, WordPress &amp; Core Web Vitals, Landingpages, Automation. Die Arbeitsprobe oben: von der Tracking-Architektur bis zur letzten Zeile Code aus einer Hand. Im Fit-Gespräch sitzt ihr mit genau der Person, die nachher euren Code schreibt.
					</p>

					<?php /* Das Ein-Personen-Modell hat ein Ausfallrisiko. Es wegzuargumentieren
					        wäre unglaubwürdig; geregelt wird der Umgang damit. Ersetzt den
					        Verfügbarkeits-Satz, der das Thema nur streifte. */ ?>
					<h3 class="wl-founder__risk-title">Was passiert, wenn ich ausfalle</h3>
					<p class="wl-founder__copy">Eine Person hat ein Ausfallrisiko, und das lässt sich nicht wegargumentieren. Regeln lässt sich der Umgang damit. Wenn ich ausfalle, erfahrt ihr es am selben Tag — nicht, wenn die Deadline verstrichen ist. Und weil der Code versioniert in eurem Repository liegt, die Zugänge in euren Accounts sind und jede Übergabe dokumentiert wird, kann jeder andere Entwickler dort weitermachen, wo ich aufgehört habe.</p>
					<p class="wl-founder__copy">Das ist kein Ersatz für ein Team. Es ist der Grund, warum ein Ausfall bei mir Zeit kostet und nichts sonst.</p>
					<p class="wl-founder__availability">Kritische Deadlines werden nur zugesagt, wenn die Umsetzung im vereinbarten Zeitraum abgesichert ist.</p>

					<p class="wl-founder__credentials"><?php echo esc_html( $founder_credentials ); ?></p>
					<?php /* An genau dieser Stelle will der Leser die Person prüfen. Bis hierher
					        führte kein Weg dorthin — ein verschenktes E-E-A-T-Signal. */ ?>
					<p class="wl-founder__about">
						<a href="<?php echo esc_url( $about_url ); ?>" data-track-action="link_whitelabel_about_person" data-track-category="internal_link" data-track-section="person">Werdegang, Arbeitsweise und Kontakt auf der Über-Seite</a>
					</p>
					<ul class="wl-founder__chips" aria-label="Rahmendaten">
						<?php foreach ( $founder_chips as $chip ) : ?>
							<li><span class="wl-chip"><?php echo esc_html( $chip ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 08 — EINORDNUNG (Vergleich)
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-compare-section" id="vergleich">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">Einordnung</span>
				<h2 class="nx-headline-section">Senior einstellen, projektweise extern vergeben — oder andocken?</h2>
				<p class="wl-section-lede">Drei Wege zu derselben Kapazität. Ohne Fantasiezahlen — die Unterschiede liegen in Anlaufzeit, Risiko und Tiefe.</p>
			</div>

			<div class="wl-compare-wrap nx-reveal">
				<table class="wl-compare">
					<caption class="wl-visually-hidden">Vergleich der drei Wege: Senior einstellen, projektweise extern vergeben, White-Label-Partner</caption>
					<thead>
						<tr>
							<th scope="col" class="wl-compare__crit"><span class="wl-visually-hidden">Kriterium</span></th>
							<?php foreach ( $comparison_columns as $i => $column ) : ?>
								<th scope="col"<?php echo 2 === $i ? ' class="wl-compare__col--hl"' : ''; ?>>
									<?php echo esc_html( $column ); ?>
									<?php if ( 2 === $i ) : ?>
										<span class="wl-compare__chip">Dieses Modell</span>
									<?php endif; ?>
								</th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $comparison_rows as $row ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( $row['label'] ); ?></th>
								<?php foreach ( $row['cells'] as $i => $cell ) : ?>
									<td data-label="<?php echo esc_attr( $comparison_columns[ $i ] ); ?>"<?php echo 2 === $i ? ' class="wl-compare__cell--hl"' : ''; ?>><?php echo esc_html( $cell ); ?></td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 09 — FAQ (Einwände vor dem Gespräch)
	     nexus-core initFaqAccordion() wirkt dokument-weit auf <details>:
	     diese FAQ muss die einzige <details>-Gruppe der Seite bleiben.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-faq-section" id="faq">
		<div class="nx-container">
			<div class="wl-section-header nx-reveal">
				<span class="wl-eyebrow">FAQ · Zusammenarbeit</span>
				<h2 class="nx-headline-section">Was Agenturen vor dem ersten Projekt wissen wollen.</h2>
			</div>

			<div class="wl-faq reveal-stagger">
				<?php foreach ( $faq_items as $item ) : ?>
					<details class="wl-faq__item" name="hu-faq-whitelabel">
						<summary
							id="wl-faq-summary-<?php echo esc_attr( $item['key'] ); ?>"
							class="wl-faq__summary"
							aria-expanded="false"
							aria-controls="wl-faq-answer-<?php echo esc_attr( $item['key'] ); ?>"
							data-track-action="faq_whitelabel_open"
							data-track-label="<?php echo esc_attr( $item['key'] ); ?>"
							data-track-category="engagement"
							data-track-section="faq"
						>
							<span class="wl-faq__q"><?php echo esc_html( $item['question'] ); ?></span>
							<span class="wl-faq__icon" aria-hidden="true"></span>
						</summary>
						<div
							id="wl-faq-answer-<?php echo esc_attr( $item['key'] ); ?>"
							class="wl-faq__answer"
							aria-labelledby="wl-faq-summary-<?php echo esc_attr( $item['key'] ); ?>"
						>
							<p><?php echo esc_html( $item['answer'] ); ?></p>
						</div>
					</details>
				<?php endforeach; ?>
			</div>

			<p class="wl-faq__more">
				Eure Frage fehlt?
				<a href="<?php echo esc_url( $wl_form_task_url ); ?>" data-wl-form-link data-track-action="cta_whitelabel_faq_task_brief" data-track-category="lead_gen" data-track-section="faq">Stellt sie im Formular</a> — Antwort <?php echo esc_html( function_exists( 'hu_response_promise' ) ? hu_response_promise( 'window' ) : 'innerhalb von 24 Stunden werktags' ); ?>.
			</p>

			<div class="wl-facts nx-reveal">
				<h3 class="wl-facts__title">Eckdaten</h3>
				<dl class="wl-facts__list">
					<?php foreach ( $wl_facts as $fact ) : ?>
						<div class="wl-facts__row">
							<dt><?php echo esc_html( $fact['term'] ); ?></dt>
							<dd><?php echo esc_html( $fact['desc'] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>
		</div>
	</section>

	<!-- ═══════════════════════════════════════════════
	     SECTION 10 — NÄCHSTER SCHRITT
	     Drei gleichrangige Wege statt eines Quiz. Das Formular darunter ist
	     das Ziel von Weg 1 und Weg 2; `?case=` unterscheidet beide im
	     Posteingang, ohne dass es zwei Formulare braucht.
	     ═══════════════════════════════════════════════ -->
	<section class="nx-section wl-cta" data-nx-theme="dark" id="naechster-schritt">
		<div class="wl-cta__bg" aria-hidden="true">
			<div class="wl-hero__bg-warmth"></div>
			<div class="wl-hero__bg-vignette"></div>
		</div>
		<div class="nx-container">
			<div class="wl-section-header wl-section-header--center nx-reveal">
				<span class="wl-eyebrow">Nächster Schritt</span>
				<h2 class="nx-headline-section">Drei Wege, je nachdem wo ihr steht</h2>
			</div>

			<div class="wl-ways reveal-stagger">
				<?php foreach ( $wl_ways as $way ) : ?>
					<article class="wl-way">
						<h3 class="wl-way__title"><?php echo esc_html( $way['title'] ); ?></h3>
						<p class="wl-way__copy"><?php echo esc_html( $way['copy'] ); ?></p>
						<a
							class="wl-way__link"
							href="<?php echo esc_url( $way['url'] ); ?>"
							<?php if ( $way['form'] ) : ?>data-wl-form-link<?php endif; ?>
							data-track-action="<?php echo esc_attr( $way['action'] ); ?>"
							data-track-category="lead_gen"
							data-track-section="naechster_schritt"
						><?php echo esc_html( $way['label'] ); ?></a>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="wl-request" id="aufgabe">
				<div class="wl-request__head">
					<h3 class="wl-request__title">Aufgabe beschreiben</h3>
					<p class="wl-request__lede">Vier Angaben genügen. Ich antworte <?php echo esc_html( function_exists( 'hu_response_promise' ) ? hu_response_promise( 'window' ) : 'innerhalb von 24 Stunden werktags' ); ?> persönlich.</p>
				</div>

				<div class="wl-request__error-summary is-hidden" role="alert" aria-live="assertive" data-wl-error-summary>
					<p class="wl-request__error-title">Bitte prüft folgende Felder:</p>
					<ul class="wl-request__error-list" data-wl-error-list></ul>
				</div>

				<form
					class="wl-request__form"
					data-wl-request-form
					action="<?php echo esc_url( $wl_form_endpoint ); ?>"
					method="post"
					novalidate
				>
					<div class="wl-request__honeypot" aria-hidden="true">
						<label for="wl-company-website">Website</label>
						<input id="wl-company-website" type="text" name="company_website" tabindex="-1" autocomplete="off">
					</div>

					<?php /* Serverseitig immer der Standardwert: die Route wird gecacht, und
					        ein aus der URL gerenderter Wert landete sonst im Cache des
					        nächsten Besuchers. Das Skript setzt ihn clientseitig aus
					        ?case= nach. */ ?>
					<input type="hidden" name="case" value="aufgabe" data-wl-case>

					<div class="wl-request__field">
						<label class="wl-request__label" for="wl-task">Aufgabe <span class="wl-request__req" aria-hidden="true">*</span></label>
						<textarea
							class="wl-request__input wl-request__input--area"
							id="wl-task"
							name="task"
							rows="5"
							required
							aria-required="true"
							placeholder="Was soll am Ende funktionieren?"
						></textarea>
					</div>

					<div class="wl-request__row">
						<div class="wl-request__field">
							<label class="wl-request__label" for="wl-timeframe">Gewünschter Zeitraum</label>
							<input
								class="wl-request__input"
								id="wl-timeframe"
								name="timeframe"
								type="text"
								autocomplete="off"
								placeholder="z. B. KW 42 oder Ende November"
							>
						</div>

						<div class="wl-request__field">
							<label class="wl-request__label" for="wl-email">E-Mail <span class="wl-request__req" aria-hidden="true">*</span></label>
							<input
								class="wl-request__input"
								id="wl-email"
								name="email"
								type="email"
								required
								aria-required="true"
								autocomplete="email"
								placeholder="name@agentur.de"
							>
						</div>
					</div>

					<fieldset class="wl-request__field wl-request__fieldset">
						<legend class="wl-request__label">Zugänge vorhanden — WordPress, GA4, GTM</legend>
						<div class="wl-request__choices">
							<?php foreach ( $wl_access_options as $access_value => $access_label ) : ?>
								<span class="wl-request__choice">
									<input
										type="radio"
										id="wl-access-<?php echo esc_attr( $access_value ); ?>"
										name="access"
										value="<?php echo esc_attr( $access_value ); ?>"
									>
									<label for="wl-access-<?php echo esc_attr( $access_value ); ?>"><?php echo esc_html( $access_label ); ?></label>
								</span>
							<?php endforeach; ?>
						</div>
					</fieldset>

					<div class="wl-request__actions">
						<button class="nx-btn nx-btn--primary" type="submit" data-wl-submit>Aufgabe senden</button>
						<a class="wl-request__aux" href="<?php echo esc_url( $whitelabel_fit_url ); ?>" data-track-action="cta_whitelabel_form_call" data-track-category="lead_gen" data-track-section="naechster_schritt">Lieber 30 Minuten sprechen</a>
					</div>

					<p class="wl-request__legal">
						Die Angaben werden für die Beantwortung eurer Anfrage verarbeitet. Details in der <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.
					</p>

					<div class="wl-request__feedback" data-wl-feedback aria-live="polite" role="status"></div>
				</form>
			</div>
		</div>
	</section>

	<!-- Sticky Mobile CTA -->
	<div class="wl-sticky-cta" id="wl-sticky-cta" aria-hidden="true">
		<div class="wl-sticky-cta__inner">
			<div class="wl-sticky-cta__label">
				<strong>White-Label-Partner</strong>
				<span>Vier Zeilen · Antwort in 24 Stunden werktags</span>
			</div>
			<a href="<?php echo esc_url( $wl_form_task_url ); ?>" class="nx-btn nx-btn--primary" data-wl-form-link data-track-action="cta_sticky_whitelabel_task_brief" data-track-category="lead_gen" data-track-section="sticky_mobile">
				Aufgabe beschreiben
			</a>
		</div>
	</div>

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
		<h2 id="wl-page-footer-heading" class="wl-visually-hidden">Seitenabschluss</h2>
		<div class="nx-container wl-page-footer__inner">
			<?php /* Der Rückweg ins übrige Angebot: die Route endet nicht mehr im Nichts.
			        Freelancer- und Solar-Link sind entfallen — für einen Agenturbesucher
			        die zwei irrelevantesten Ziele der Website. */ ?>
			<nav class="wl-page-footer__site" aria-label="Weitere Seiten">
				<a href="<?php echo esc_url( $wl_home_url ); ?>" rel="home" data-track-action="nav_whitelabel_footer_home" data-track-category="navigation" data-track-section="whitelabel_footer">Startseite</a>
			</nav>
			<p>&copy; <time datetime="<?php echo esc_attr( $current_year ); ?>"><?php echo esc_html( $current_year ); ?></time> Haşim Üner · White-Label-Partner für Agenturen</p>
			<nav class="wl-page-footer__legal" aria-label="Rechtliches">
				<a href="<?php echo esc_url( $imprint_url ); ?>" data-track-action="nav_whitelabel_footer_imprint" data-track-category="navigation" data-track-section="whitelabel_footer">Impressum</a>
				<span aria-hidden="true">·</span>
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
