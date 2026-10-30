<?php
/**
 * Template Name: WordPress Agentur Hannover
 * Description: Lokale Seite für den Agentur-Intent: Belege und Leistungen zuerst, danach die Entscheidungshilfe größere Agentur oder direkte Umsetzung.
 *
 * Die Startseite besitzt den direkten WordPress-/Freelancer-Kaufintent.
 * Diese URL beantwortet bewusst die lokale Agentur-Suche: erst Belege und
 * Leistungen, dann ein einziger Vergleich, wann eine größere Agentur besser
 * passt. Keine zweite allgemeine Money Page.
 *
 * Copy-Regel der Route: kurze Sätze, Punkt statt Gedankenstrich-Kette.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'hu_forced_singular_seo_map', function ( $map ) {
	if ( ! is_array( $map ) ) {
		$map = [];
	}

	$map['wordpress-agentur-hannover'] = [
		'title'       => 'WordPress Agentur Hannover: Website, SEO & Tracking | Haşim Üner',
		'description' => 'WordPress Agentur Hannover: Websites, Relaunches und Landingpages mit technischem SEO und Tracking. Geplant und gebaut von einem Ansprechpartner.',
	];

	return $map;
}, 90 );

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'hu-wayfinding-active';
	$classes[] = 'hu-wayfinding-results';
	$classes[] = 'hu-agentur-decision-page';
	return array_values( array_unique( $classes ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$navigation_css = '/assets/css/navigation-ecosystem.css';
	$navigation_js  = '/assets/js/navigation-ecosystem.js';
	$route_css      = '/assets/css/agentur-decision.css';

	if ( is_file( $dir . $navigation_css ) ) {
		wp_enqueue_style( 'hu-navigation-ecosystem', $uri . $navigation_css, [], (string) filemtime( $dir . $navigation_css ) );
	}
	if ( is_file( $dir . $route_css ) ) {
		wp_enqueue_style( 'hu-agentur-decision', $uri . $route_css, [ 'nexus-system-css', 'hu-navigation-ecosystem' ], (string) filemtime( $dir . $route_css ) );
	}
	if ( is_file( $dir . $navigation_js ) ) {
		wp_enqueue_script( 'hu-navigation-ecosystem', $uri . $navigation_js, [], (string) filemtime( $dir . $navigation_js ), true );
	}
}, 90 );

add_action( 'wp_body_open', function () {
	?>
	<nav class="hu-wayfinding-breadcrumb" aria-label="Breadcrumb" data-track-section="breadcrumb">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track-action="breadcrumb_home" data-track-category="navigation">Startseite</a></li>
			<li><span aria-current="page">WordPress Agentur Hannover</span></li>
		</ol>
	</nav>
	<?php
}, 30 );

add_action( 'wp_body_open', function () {
	$items = [
		[ 'id' => 'belege', 'label' => 'Beleg' ],
		[ 'id' => 'zusammenarbeit', 'label' => 'Leistung' ],
		[ 'id' => 'technik', 'label' => 'Vergleich' ],
		[ 'id' => 'hannover', 'label' => 'Hannover' ],
		[ 'id' => 'faq', 'label' => 'FAQ' ],
	];
	?>
	<nav class="hu-page-toc hu-results-register" aria-label="Abschnitte dieses Dokuments" data-hu-rail="true" data-hu-results-register-ready="true" data-track-section="page_toc">
		<span class="hu-results-register__marke" aria-hidden="true"><span class="hu-results-register__marke-short">Reg.</span><span class="hu-results-register__marke-full">Register</span></span>
		<div class="hu-results-register__entries">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php $number = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ); ?>
				<a href="#<?php echo esc_attr( $item['id'] ); ?>" data-track-action="toc_agentur_<?php echo esc_attr( sanitize_key( $item['id'] ) ); ?>" data-track-category="navigation"><span class="hu-results-register__nr"><?php echo esc_html( $number ); ?></span><span class="hu-results-register__txt"><?php echo esc_html( $item['label'] ); ?></span></a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
}, 31 );

// Ohne Vorauswahl: Wer „WordPress Agentur“ sucht, will meist eine neue
// Website oder einen Relaunch. Die Kontaktseite fragt das Vorhaben selbst ab,
// statt jede Anfrage als „Bestehende Website weiterentwickeln“ abzulegen.
$contact_url    = hu_get_navigation_project_request_url();
$website_url    = hu_get_commercial_route( 'website', home_url( '/wordpress-website-erstellen-lassen/' ) );
$references_url = hu_get_commercial_route( 'references', home_url( '/referenzen/' ) );
$case_url       = nexus_get_results_url();
$response       = hu_response_promise( 'compact' );
$faqs           = function_exists( 'nexus_get_agentur_faq_items' ) ? nexus_get_agentur_faq_items() : [];

// Öffentlich prüfbare Projekte. Beschreibungen folgen /referenzen/ – dort
// steht der eigene Beitrag je Projekt. Keine Zahlen ohne Beleg.
$projects = [
	[
		'url'    => 'https://civaka-azad.org/',
		'name'   => 'civaka-azad.org',
		'kind'   => 'Redaktionelle Website',
		'detail' => 'Informationsarchitektur, Navigation und Archive für einen großen Inhaltsbestand.',
		'track'  => 'agentur_proof_project_civaka_azad',
	],
	[
		'url'    => 'https://kurdischer-rat.org/',
		'name'   => 'kurdischer-rat.org',
		'kind'   => 'Organisationswebsite',
		'detail' => 'Klare Hierarchie. Veröffentlichung über einen versionierten, kontrollierten Ablauf.',
		'track'  => 'agentur_proof_project_kurdischer_rat',
	],
];

// Leistungen = Angebotsseiten. Eine Karte pro Ausgangslage; Umfang und Preis
// stehen auf der Zielseite, nicht hier.
$services = [
	[
		'key'    => 'website',
		'label'  => 'Neu',
		'title'  => 'WordPress-Website erstellen lassen',
		'detail' => 'Neubau oder Relaunch. Der Preis steht vor dem Start fest.',
		'track'  => 'agentur_to_website_offer',
	],
	[
		'key'    => 'takeover',
		'label'  => 'Bestand',
		'title'  => 'Bestehende Website übernehmen',
		'detail' => 'Erst geprüft, dann weiterentwickelt. Auch fremde Installationen.',
		'track'  => 'agentur_to_takeover_offer',
	],
	[
		'key'    => 'technical_seo',
		'label'  => 'Sichtbarkeit',
		'title'  => 'Technisches SEO für WordPress',
		'detail' => 'Seitenrollen, Canonicals, Schema, Weiterleitungen.',
		'track'  => 'agentur_to_technical_seo_offer',
	],
	[
		'key'    => 'tracking_setup',
		'label'  => 'Messung',
		'title'  => 'Conversion-Tracking einrichten lassen',
		'detail' => 'Woher jede Anfrage kommt. GA4 und Tag Manager.',
		'track'  => 'agentur_to_tracking_offer',
	],
	[
		'key'    => 'conversion',
		'label'  => 'Wirkung',
		'title'  => 'Conversion-Optimierung für B2B',
		'detail' => 'Besucher sind da. Passende Anfragen fehlen.',
		'track'  => 'agentur_to_conversion_offer',
	],
	[
		'key'    => 'maintenance',
		'label'  => 'Betrieb',
		'title'  => 'WordPress-Wartung mit Anfrage-Test',
		'detail' => 'Jeden Monat geprüft: Kommen Anfragen an?',
		'track'  => 'agentur_to_maintenance_offer',
	],
];

get_header();
?>

<div class="site-main doku agentur-decision" data-track-page="wordpress_agentur_hannover_decision">
	<header class="kopfteil" data-track-section="agentur_hero">
		<div class="blatt">
			<p class="gegenstand">WordPress Agentur Hannover · ein Ansprechpartner</p>
			<h1>WordPress Agentur Hannover. Ein Ansprechpartner, der selbst baut.</h1>
			<p class="aufriss">
				<span class="erst">Websites, Relaunches und Landingpages mit WordPress.</span>
				Technisches SEO und Messung sind von Anfang an drin. Sie sprechen immer mit der Person, die baut.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_hero_project" data-track-category="lead_gen">
					Projekt einordnen <span class="pf" aria-hidden="true">→</span>
				</a>
				<a class="tun still" href="<?php echo esc_url( $website_url ); ?>" data-track-action="cta_agentur_hero_website" data-track-category="navigation">Website-Angebot ansehen</a>
			</div>

			<a class="agentur-proofline" href="<?php echo esc_url( $case_url ); ?>" data-track-action="agentur_hero_case" data-track-category="proof">
				<strong>−<?php echo esc_html( (string) HU_E3_CPL_REDUCTION_PERCENT ); ?> %</strong>
				<span>Kosten pro qualifizierter Anfrage · dokumentierter B2B-Fall</span>
			</a>

			<p class="mono"><?php echo esc_html( $response ); ?> · Pattensen bei Hannover · DACH-weit</p>
		</div>
	</header>

	<section id="belege" data-track-section="agentur_proof">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Beleg</span><span class="strich"></span></div>
			</div>

			<div class="voll">
				<div class="proof-messung">
					<div class="proof-messung__zahl" aria-label="<?php echo esc_attr( HU_E3_CPL_REDUCTION_PERCENT . ' Prozent weniger Kosten pro qualifizierter Anfrage' ); ?>">
						<span>−<?php echo esc_html( (string) HU_E3_CPL_REDUCTION_PERCENT ); ?></span><small>%</small>
					</div>
					<div class="proof-messung__text">
						<p class="mono">Kosten pro qualifizierter Anfrage</p>
						<h2>Beleg vor Behauptung.</h2>
						<p>Ein B2B-Fall, die ganze Strecke: Zielseiten, Vorqualifizierung, Tracking, Übergabe an den Vertrieb. Verglichen werden zwei Anfragequellen im selben Betrieb. Keine Erfolgszusage für Ihr Projekt.</p>
					</div>
				</div>

				<div class="beleg-links">
					<?php foreach ( $projects as $project ) : ?>
						<a class="beleg-link" href="<?php echo esc_url( $project['url'] ); ?>" data-track-action="<?php echo esc_attr( $project['track'] ); ?>" data-track-category="proof">
							<span><?php echo esc_html( $project['kind'] ); ?></span>
							<strong><?php echo esc_html( $project['name'] ); ?></strong>
							<em><?php echo esc_html( $project['detail'] ); ?></em>
						</a>
					<?php endforeach; ?>
					<a class="beleg-link" href="<?php echo esc_url( $references_url ); ?>" data-track-action="agentur_proof_references" data-track-category="proof">
						<span>Öffentlich prüfbar</span>
						<strong>Alle Projekte</strong>
						<em>Mit meinem Beitrag je Projekt. Reale URLs statt Logo-Wand.</em>
					</a>
					<a class="beleg-link" href="<?php echo esc_url( $case_url ); ?>" data-track-action="agentur_proof_case" data-track-category="proof">
						<span>Dokumentierter Fall</span>
						<strong>Methodik und Zahlen</strong>
						<em>Die −<?php echo esc_html( (string) HU_E3_CPL_REDUCTION_PERCENT ); ?> % im Zusammenhang.</em>
					</a>
				</div>
			</div>
		</div>
	</section>

	<section id="zusammenarbeit" data-track-section="agentur_collaboration">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Leistung</span><span class="strich"></span></div>
			</div>

			<div class="voll">
				<p class="mono stempelfarbe">Leistungen</p>
				<h2 class="kopf">Was ich für Unternehmen in Hannover baue.</h2>
				<p class="vorspann">Sechs Ausgangslagen, eine Seite je Angebot. Umfang und Preis stehen dort.</p>

				<nav class="route-grid" aria-label="Leistungen">
					<?php foreach ( $services as $service ) : ?>
						<a href="<?php echo esc_url( hu_get_commercial_route( $service['key'] ) ); ?>" data-track-action="<?php echo esc_attr( $service['track'] ); ?>" data-track-category="internal_link">
							<span><?php echo esc_html( $service['label'] ); ?></span>
							<strong><?php echo esc_html( $service['title'] ); ?></strong>
							<small><?php echo esc_html( $service['detail'] ); ?></small>
						</a>
					<?php endforeach; ?>
				</nav>

				<div class="ausgang">
					<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_collaboration_project" data-track-category="lead_gen">Projekt einordnen <span class="pf" aria-hidden="true">→</span></a>
				</div>
			</div>
		</div>
	</section>

	<section id="technik" data-track-section="agentur_comparison">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Vergleich</span><span class="strich"></span></div>
			</div>

			<div class="voll">
				<div class="tafel">
					<p class="mono stempelfarbe">Größere Agentur oder direkt</p>
					<h2 class="kopf">Nicht das Etikett entscheidet. Ihr Projekt entscheidet.</h2>
					<p class="vorspann">Jede Übergabe kostet Kontext. Manche Projekte brauchen trotzdem ein großes Team. So sehen Sie, welches Modell Ihr Risiko senkt.</p>

					<div class="vergleich" role="table" aria-label="Vergleich größere Agentur und direkte Umsetzung">
						<div class="vergleich-zeile vergleich-kopf" role="row">
							<div role="columnheader">Anforderung</div>
							<strong role="columnheader">Größere Agentur</strong>
							<strong role="columnheader">Direkt mit Haşim Üner</strong>
						</div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Viele Rollen gleichzeitig</div><div role="cell">stark mit parallelen Teams</div><div role="cell">bewusst begrenzter Umfang</div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Vertretung, SLA, 24/7</div><div role="cell">feste Vertretung möglich</div><div role="cell">keine 24/7-Bereitschaft, keine Vertretung</div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Entscheidungsweg</div><div role="cell">Projektleitung und Fachteam getrennt</div><div role="cell"><strong>Entscheidung und Umsetzung in einem Gespräch</strong></div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">WordPress, SEO, Tracking, CRO</div><div role="cell">hängt an Übergaben</div><div role="cell"><strong>ein gemeinsamer technischer Kontext</strong></div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Typischer Fit</div><div role="cell">großer Rollout, Ausschreibung, mehrere Gewerke</div><div role="cell"><strong>B2B-Website, Relaunch, Landingpage, Weiterentwicklung</strong></div></div>
					</div>

					<p class="vergleich-grenze"><span class="mono stempelfarbe">Nicht passend</span> Große Shops, reine Design-Relaunches, 24/7-Bereitschaft, mehrere volle Teams parallel. Dafür ist eine größere Agentur die bessere Wahl.</p>
				</div>
			</div>
		</div>
	</section>

	<section id="hannover" data-track-section="agentur_local">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Hannover</span><span class="strich"></span></div>
			</div>

			<div class="haupt">
				<p class="mono stempelfarbe">Lokal erreichbar</p>
				<h2 class="kopf">Vor Ort, wenn es hilft. Remote, wenn es reicht.</h2>
				<p class="vorspann">Sitz in Pattensen bei Hannover. Workshops und Reviews vor Ort nach Vereinbarung, auch in Hildesheim, Braunschweig, Wolfsburg und Celle. Entwicklung, Tracking und Abstimmung laufen remote, im ganzen DACH-Raum.</p>

				<dl class="lokal-zeile">
					<div><dt>Sitz</dt><dd>Pattensen bei Hannover</dd></div>
					<div><dt>Vor Ort</dt><dd>Region Hannover und Niedersachsen</dd></div>
					<div><dt>Remote</dt><dd>Deutschland, Österreich, Schweiz</dd></div>
				</dl>
			</div>
		</div>
	</section>

	<section id="faq" data-track-section="agentur_faq">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">FAQ</span><span class="strich"></span></div>
			</div>

			<div class="haupt">
				<p class="mono stempelfarbe">Vor der Entscheidung</p>
				<h2 class="kopf leise">Fragen zur WordPress Agentur in Hannover.</h2>

				<?php if ( ! empty( $faqs ) ) : ?>
					<?php // name= öffnet immer nur eine Antwort; data-faq-group ist der JS-Rückfall in nexus-core.js. ?>
					<div class="fragen" data-faq-group>
						<?php foreach ( $faqs as $index => $item ) : ?>
							<?php
							$question = isset( $item['question'] ) ? (string) $item['question'] : '';
							$answer   = isset( $item['answer'] ) ? (string) $item['answer'] : '';
							if ( '' === $question || '' === $answer ) {
								continue;
							}
							?>
							<details name="agentur-faq">
								<summary data-track-action="agentur_faq_open" data-track-label="<?php echo esc_attr( (string) ( $index + 1 ) ); ?>" data-track-category="engagement" data-track-section="agentur_faq"><?php echo esc_html( $question ); ?></summary>
								<div class="huelle"><div><p class="antwort"><?php echo esc_html( $answer ); ?></p></div></div>
							</details>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="abschluss" id="anfrage" data-track-section="agentur_final_cta">
		<div class="blatt reihe">
			<div class="ganz">
				<div class="tafel">
					<div class="reihe">
						<div class="haupt">
							<p class="mono stempelfarbe">Nächster Schritt</p>
							<h2>Nicht die größte Struktur gewinnt. Die passende.</h2>
							<p class="aufriss">Drei Angaben reichen: Website, Ziel, Engpass. Sie bekommen eine klare Einordnung. Auch ein Nein, wenn ein größeres Team besser passt.</p>
							<div class="ausgang">
								<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_final_project" data-track-category="lead_gen">Projekt einordnen <span class="pf" aria-hidden="true">→</span></a>
							</div>
						</div>
						<aside class="marg">
							<p class="note"><span class="label">Ausgang</span>Umfang, passende Angebotsseite oder ein klares Nein. Kein Pflicht-Relaunch.</p>
						</aside>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
