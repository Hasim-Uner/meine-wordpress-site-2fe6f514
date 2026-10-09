<?php
/**
 * Template Name: GA4 Tracking Landing
 * Description: Conversion Tracking Setup fuer B2B – GA4, GTM, Consent, Ads und optionale Server-/CRM-Erweiterung.
 *
 * Die Route bleibt /ga4-tracking-setup/. Diese Seite besitzt den breiten
 * Tracking-Kaufintent; /server-side-tracking-b2b/ bleibt die spezialisierte
 * Vertiefung fuer Server-GTM, CAPI und deduplizierte Serversignale.
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

	$map['ga4-tracking-setup'] = [
		'title'       => 'Conversion Tracking einrichten lassen | GA4, GTM & Consent',
		'description' => sprintf(
			'Conversion Tracking für B2B-Websites: GA4, GTM, Consent Mode und Google Ads zum Festpreis von %s netto. Server-Side, Meta CAPI und CRM bei Bedarf separat vereinbart.',
			hu_tracking_price( 'measurement', 'setup' )
		),
	];

	return $map;
}, 90 );

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'hu-wayfinding-active';
	$classes[] = 'hu-wayfinding-tracking-setup';
	$classes[] = 'hu-tracking-setup-page';
	return array_values( array_unique( $classes ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$navigation_css = '/assets/css/navigation-ecosystem.css';
	$navigation_js  = '/assets/js/navigation-ecosystem.js';
	$route_css      = '/assets/css/tracking-setup.css';
	$product_css    = '/assets/css/ga4.css';

	if ( is_file( $dir . $navigation_css ) ) {
		wp_enqueue_style( 'hu-navigation-ecosystem', $uri . $navigation_css, [], (string) filemtime( $dir . $navigation_css ) );
	}
	if ( is_file( $dir . $route_css ) ) {
		wp_enqueue_style( 'hu-tracking-setup', $uri . $route_css, [ 'hu-navigation-ecosystem' ], (string) filemtime( $dir . $route_css ) );
	}
	if ( is_file( $dir . $product_css ) ) {
		wp_enqueue_style( 'nexus-ga4-css', $uri . $product_css, [ 'hu-tracking-setup' ], (string) filemtime( $dir . $product_css ) );
	}
	if ( is_file( $dir . $navigation_js ) ) {
		wp_enqueue_script( 'hu-navigation-ecosystem', $uri . $navigation_js, [], (string) filemtime( $dir . $navigation_js ), true );
	}
}, 45 );

function hu_render_tracking_setup_breadcrumb() {
	?>
	<nav class="hu-wayfinding-breadcrumb" aria-label="Breadcrumb" data-track-section="breadcrumb">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track-action="breadcrumb_home" data-track-category="navigation">Startseite</a></li>
			<li><span aria-current="page">Conversion Tracking Setup</span></li>
		</ol>
	</nav>
	<?php
}
add_action( 'wp_body_open', 'hu_render_tracking_setup_breadcrumb', 30 );

function hu_render_tracking_setup_toc() {
	$items = [
		[ 'id' => 'umfang', 'label' => 'Lieferumfang' ],
		[ 'id' => 'pruefung', 'label' => 'Abnahme' ],
		[ 'id' => 'angebot', 'label' => 'Preis & Rahmen' ],
		[ 'id' => 'messkette', 'label' => 'Technik & Ablauf' ],
		[ 'id' => 'faq', 'label' => 'Fragen' ],
	];
	?>
	<nav class="hu-page-toc hu-tracking-register" aria-label="Abschnitte dieses Dokuments" data-hu-rail="true" data-track-section="page_toc">
		<span class="hu-tracking-register__marke" aria-hidden="true">Register</span>
		<div class="hu-tracking-register__eintraege">
			<?php foreach ( $items as $index => $item ) : ?>
				<a href="#<?php echo esc_attr( $item['id'] ); ?>" data-track-action="toc_<?php echo esc_attr( sanitize_key( $item['id'] ) ); ?>" data-track-category="navigation">
					<span class="hu-tracking-register__nr" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
					<span class="hu-tracking-register__txt"><?php echo esc_html( $item['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
}
add_action( 'wp_body_open', 'hu_render_tracking_setup_toc', 31 );

$contact_url = function_exists( 'hu_get_contact_intake_url' )
	? hu_get_contact_intake_url( 'project', 'tracking' )
	: add_query_arg(
		[
			'type'  => 'project',
			'focus' => 'tracking',
		],
		home_url( '/kontakt/' )
	);

$server_side_url = home_url( '/server-side-tracking-b2b/' );
$results_url     = function_exists( 'nexus_get_primary_public_url' )
	? nexus_get_primary_public_url( 'results', home_url( '/case-study-solar-leadgenerierung/' ) )
	: home_url( '/case-study-solar-leadgenerierung/' );
$response_label  = hu_response_promise( 'compact' );

// Der Kanon bleibt die Quelle für Umfang, Preis und Lieferzeit.
// Sichtbar ist ein Grundprodukt; zusätzliche Systeme werden erst nach
// Bestandsaufnahme vereinbart und sind keine Auswahl aus einer Paketleiter.
$ladder        = hu_tracking_product_ladder();
$entry         = $ladder['measurement'];
$feature_notes = [
	'Es steht fest, welche Handlung zählt und welches System das Signal bekommt.',
	'Bestehende Tags und Events werden eingerichtet oder bereinigt.',
	'Die Messung folgt den Signalen Ihres vorhandenen Consent-Tools.',
	'Ihre wichtigsten Anfragen werden als klar definierte Kampagnensignale eingerichtet.',
	'Sie erhalten die geprüften Zustände, GTM-Versionen und bekannten Grenzen.',
];
$faq_items = hu_tracking_setup_faq_items();

get_header();
?>

<div class="site-main doku ga4-page" data-track-page="conversion_tracking_setup">
	<header class="kopfteil" data-track-section="tracking_hero">
		<div class="blatt ga4-product-hero">
			<div>
				<p class="gegenstand">Conversion-Tracking · GA4 · Google Ads</p>
				<h1>Conversion Tracking einrichten lassen. Damit klar ist, welche Anfrage zählt.</h1>
				<p class="aufriss">Ich richte GA4, Google Tag Manager und Google Ads so ein, dass Ihre Haupt-Conversions definiert, an Ihr Consent-Tool angebunden und mit Testfällen geprüft sind. Für Ihre bestehende Website, mit Messplan und dokumentierter Übergabe.</p>
				<div class="ausgang">
					<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_tracking_project" data-track-category="lead_gen">Tracking anfragen <span class="pf" aria-hidden="true">→</span></a>
					<a class="textlink" href="#umfang" data-track-action="tracking_hero_scope" data-track-category="navigation">Die fünf Lieferbausteine</a>
				</div>
				<p class="mono"><?php echo esc_html( $response_label ); ?> · Anfrage ist noch kein Auftrag</p>
			</div>
			<aside class="ga4-product-brief" aria-label="Preis und Umfang des Tracking-Produkts">
				<p class="mono stempelfarbe">Ein Produkt · geprüfte Messbasis</p>
				<p class="ga4-product-brief__name"><?php echo esc_html( $entry['name'] ); ?></p>
				<p class="ga4-product-brief__price"><?php echo esc_html( $entry['price'] ); ?><small>netto · einmalig</small></p>
				<p>Eine Website mit klaren Haupt-Conversions. Messung im Browser, ohne eigenen Tracking-Server.</p>
				<dl>
					<div><dt>Umsetzung</dt><dd><?php echo esc_html( $entry['weeks'] ); ?></dd></div>
					<div><dt>Übergabe</dt><dd>Messplan, Tests und Dokumentation</dd></div>
				</dl>
				<p class="ga4-product-brief__terms">Server-Side, Meta und CRM sind separat vereinbarte Erweiterungen. Externe Dienste und Lizenzen werden vor dem Start ausgewiesen.</p>
			</aside>
		</div>
	</header>

	<section id="umfang" data-track-section="tracking_scope">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Lieferumfang</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Im Grundprodukt enthalten</p>
				<h2 class="kopf">Fünf Bausteine. Eine nachvollziehbare Messung.</h2>
				<ol class="ga4-product-features" role="list">
					<?php foreach ( $entry['items'] as $index => $item ) : ?>
						<li><span class="ga4-product-features__nr" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span><div><h3><?php echo esc_html( $item ); ?></h3><p><?php echo esc_html( $feature_notes[ $index ] ?? '' ); ?></p></div></li>
					<?php endforeach; ?>
				</ol>
			</div>
			<aside class="marg"><p class="note"><span class="label">Grenze des Produkts</span>Gemessen werden vereinbarte Handlungen. Ob ein Lead fachlich passt oder zum Auftrag wird, braucht ein zusätzliches Geschäftssignal aus Ihrem Vertrieb.</p></aside>
		</div>
	</section>

	<section id="pruefung" data-track-section="tracking_acceptance">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Abnahme</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Qualität am Testfall prüfen</p>
				<h2 class="kopf">Sie bekommen ein Protokoll, das sich nachprüfen lässt.</h2>
				<p class="vorspann">Für die vereinbarten Conversions werden Auslöser, Consent-Zustand, erwartetes Signal und Prüfergebnis festgehalten. Bekannte Grenzen stehen mit in der Übergabe.</p>
				<div class="hu-tracking-proof-ledger" aria-label="Beispielstruktur der technischen Abnahme">
					<p class="hu-tracking-proof-ledger__eyebrow">Beispielstruktur · kein Kundenprotokoll</p>
					<div class="protokoll">
						<div class="z"><span>Erfolgreiche Anfrage</span><b>Ein definierter Conversion-Vorgang</b></div>
						<div class="z"><span>Fehlgeschlagener Versand</span><b>Keine Erfolgs-Conversion</b></div>
						<div class="z"><span>Consent-Zustände</span><b>Der vereinbarte Datenfluss wird geprüft</b></div>
						<div class="z"><span>Übergabe</span><b>Messplan · QA-Protokoll · GTM-Versionen</b></div>
					</div>
				</div>
				<p class="ga4-product-evidence"><a class="satzlink" href="<?php echo esc_url( $results_url ); ?>" data-track-action="tracking_acceptance_case" data-track-category="navigation">Arbeitsbeleg: Tracking und CRM im dokumentierten B2B-Fall →</a><span>Das Ergebnis dieses Falls entstand aus mehreren Maßnahmen gemeinsam; es ist keine Zusage für ein Tracking-Setup.</span></p>
			</div>
			<aside class="marg"><p class="note"><span class="label">Konten und Eigentum</span>GTM, GA4 und Werbekonten bleiben bei Ihnen. Die Dokumentation erklärt das Setup auch dem nächsten Entwickler.</p></aside>
		</div>
	</section>

	<section id="angebot" data-track-section="tracking_offer">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Preis &amp; Rahmen</span><span class="strich"></span></div></div>
			<div class="voll">
				<div class="tafel ga4-product-contract" id="stufe-1">
					<div>
						<p class="mono stempelfarbe">Festpreis für den Grundumfang</p>
						<h2><?php echo esc_html( $entry['name'] ); ?> für <?php echo esc_html( $entry['price'] ); ?> netto.</h2>
						<p class="aufriss">Die fünf Lieferbausteine gelten für eine Website und die vereinbarten Haupt-Conversions. Systeme, Formulare und Zugänge grenzen wir vor dem Start schriftlich ab.</p>
						<p class="mono"><?php echo esc_html( $entry['weeks'] ); ?> · <?php echo esc_html( $entry['terms'] ); ?></p>
						<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_tracking_project" data-track-category="lead_gen">Tracking anfragen <span class="pf" aria-hidden="true">→</span></a></div>
					</div>
					<div class="ga4-product-contract__extension">
						<h3>Wenn Ihr Setup mehr braucht.</h3>
						<p>Ein Server-Endpunkt, Meta CAPI oder CRM-Status als Rücksignal werden nach der Bestandsaufnahme separat angeboten. Sie sind im Grundpreis nicht enthalten.</p>
						<p>Bei Server-Side kommen laufende Hostingkosten hinzu; bei externen Consent-Tools oder Diensten gegebenenfalls Lizenzen. Laufende Betreuung ist optional. Umfang, Gesamtpreis und laufende Kosten stehen vor einer Beauftragung fest.</p>
						<a class="satzlink" href="<?php echo esc_url( $server_side_url ); ?>" data-track-action="tracking_offer_server_side" data-track-category="navigation">Server-Side: Umfang und Kostenbedingungen →</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="messkette" data-track-section="tracking_measurement_chain">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Vertiefung</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf leise">Technik und Ablauf, wenn Sie genauer hinsehen möchten.</h2>
				<div class="fragen ga4-product-details">
					<details id="probleme" data-track-section="tracking_problems"><summary>Welche Abweichungen zuerst geprüft werden</summary><div class="huelle"><div>
						<p>Fehlende oder doppelte Formulare, unklare Consent-Signale und unterschiedlich gezählte Conversions werden zunächst anhand des bestehenden Setups untersucht. Unterschiede zwischen GA4 und Ads können auch aus Zählweise, <?php echo nexus_glossary_link( 'attribution', 'Attribution' ); ?> und Zeitfenstern entstehen.</p>
						<p>Wenn die Messung funktioniert und zu wenige passende Anfragen kommen, führt die nächste Prüfung zur <a class="satzlink" href="<?php echo esc_url( home_url( '/conversion-optimierung/' ) ); ?>" data-track-action="tracking_problems_to_conversion" data-track-category="navigation">Conversion-Optimierung Ihrer Website</a>.</p>
					</div></div></details>
					<details><summary>Wie Website, Consent und Werbeplattformen zusammenarbeiten</summary><div class="huelle"><div>
						<p>Die Website löst eine definierte Handlung aus. Google Tag Manager verarbeitet Event und Parameter. Ihr Consent-Tool steuert das vereinbarte Verhalten; GA4 und Google Ads erhalten die passenden Signale.</p>
						<p><?php echo nexus_glossary_link( 'consent-mode', 'Consent Mode' ); ?> holt selbst keine Einwilligung ein. Der technische Datenfluss und seine Grenzen werden dokumentiert. Server-Side und CRM werden nur geprüft und eingerichtet, wenn sie beauftragt sind.</p>
					</div></div></details>
					<details id="ablauf" data-track-section="tracking_process"><summary>So kommen Sie vom Messplan zur Übergabe</summary><div class="huelle"><div>
						<ol><li>Bestehende Tags, Ziele, Consent und Abweichungen aufnehmen.</li><li>Conversions, Systeme und Projektumfang schriftlich festlegen.</li><li>GTM, GA4 und Google Ads nach diesem Messplan einrichten.</li><li>Die vereinbarten Formular- und Consent-Zustände testen.</li><li>Konten, GTM-Versionen, Testprotokoll und offene Grenzen übergeben.</li></ol>
						<p>Für die Umsetzung brauche ich die vereinbarten Kontenzugänge und eine Person, die Conversion-Ziele und Freigaben klärt.</p>
					</div></div></details>
				</div>
			</div>
		</div>
	</section>

	<section id="faq" data-track-section="tracking_faq">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Fragen</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf leise">Was vor dem Auftrag noch wichtig ist.</h2>
				<?php if ( ! empty( $faq_items ) ) : ?>
					<div class="fragen">
						<?php foreach ( $faq_items as $item ) : ?>
							<?php
							$question = isset( $item['question'] ) ? (string) $item['question'] : '';
							$answer   = isset( $item['answer'] ) ? (string) $item['answer'] : '';
							if ( '' === $question || '' === $answer ) { continue; }
							?>
							<details><summary><?php echo esc_html( $question ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $answer ); ?></p></div></div></details>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="abschluss" id="anfrage" data-track-section="tracking_final_cta">
		<div class="blatt reihe"><div class="ganz"><div class="tafel"><div class="reihe">
			<div class="haupt">
				<p class="mono stempelfarbe">Nächster Schritt</p>
				<h2>Was soll bei Ihnen als Conversion ankommen?</h2>
				<p class="aufriss">Schicken Sie Ihre Website, das bestehende Setup und die Abweichung oder das Ziel. Ich kläre mit Ihnen Umfang, Voraussetzungen und Festpreis. Die Anfrage ist unverbindlich.</p>
				<div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_tracking_project" data-track-category="lead_gen">Tracking anfragen <span class="pf" aria-hidden="true">→</span></a></div>
				<p class="mono"><?php echo esc_html( $response_label ); ?></p>
			</div>
			<aside class="marg"><p class="note"><span class="label">Nach Ihrer Anfrage</span>Sie bekommen eine Einordnung und gegebenenfalls Rückfragen. Ein Projekt beginnt nach dem vereinbarten Angebot und Ihrer Zusage.</p></aside>
		</div></div></div></div>
	</section>
</div>

<?php get_footer(); ?>
