<?php
/**
 * Template Name: Server-Side Tracking für B2B-Leadgenerierung
 * Description: Money-Page für Server-Side Tracking (Server-GTM, GA4, Google Ads,
 *              Consent-Anbindung) mit Leistungsumfang, Festpreisrahmen,
 *              Ablauf und eigenem Anfrageformular.
 *              Primärer CTA: Formular auf der Seite (#anfrage), das über
 *              nexus/v1/contact-request mit type=project und focus=tracking
 *              in den bestehenden Kontakt-Intake schreibt.
 *              Begründung: Die Seite erhält laut GSC-Export ausschließlich
 *              generische Tracking-Queries ohne Solar-Bezug. Der Marktcheck
 *              bleibt als Textlink für Solar-/SHK-Betriebe erhalten.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── URLs ───────────────────────────────────────────────────────
$page_url        = home_url( '/server-side-tracking-b2b/' );
$ga4_setup_url   = home_url( '/ga4-tracking-setup/' );
$gtm_guide_url   = home_url( '/server-side-tracking-gtm/' );
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$e3_case_url     = function_exists( 'hu_e3_canon' )
	? (string) ( hu_e3_canon()['url'] ?? home_url( '/case-study-solar-leadgenerierung/' ) )
	: home_url( '/case-study-solar-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$privacy_url     = function_exists( 'nexus_get_page_url' )
	? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) )
	: home_url( '/datenschutz/' );
$form_anchor     = '#anfrage';
$rest_endpoint   = rest_url( 'nexus/v1/contact-request' );
$setup_cta_label = 'Tracking anfragen';

// ── Preis- und Lieferkanon ────────────────────────────────────
// Eine fachliche Ausprägung des Tracking-Produkts. Keine zweite Paketwahl.
$ladder               = hu_tracking_product_ladder();
$server_product       = $ladder['standard'];
$standard_setup_price = $server_product['price'];
$standard_care_price  = hu_tracking_price( 'standard', 'care' );
$standard_terms       = hu_tracking_package_detail( 'standard', 'terms' );
$standard_minutes     = hu_tracking_package_detail( 'standard', 'included_minutes' );
$delivery_window      = $server_product['weeks'];

// ── Formular-Registries (bestehender Kontakt-Intake) ──────────
$ad_platform_options = function_exists( 'nexus_get_contact_ad_platform_options' )
	? nexus_get_contact_ad_platform_options()
	: [];
$ad_budget_options   = function_exists( 'nexus_get_contact_ad_budget_options' )
	? nexus_get_contact_ad_budget_options()
	: [];
$referral_options    = function_exists( 'nexus_get_inquiry_referral_options' )
	? nexus_get_inquiry_referral_options()
	: [];
$focus_options       = function_exists( 'nexus_get_contact_focus_options' )
	? nexus_get_contact_focus_options()
	: [];
$tracking_focus_types = isset( $focus_options['tracking']['types'] )
	? implode( ',', array_map( 'sanitize_key', (array) $focus_options['tracking']['types'] ) )
	: 'project';

// ── Lieferumfang des serverseitigen Scopes ────────────────────
$setup_items = [
	[ 't' => 'Messplan, GA4, GTM und Google Ads', 's' => 'Die browserseitige Messbasis ist enthalten. Haupt-Conversions, Parameter und das Verhalten je Consent-Zustand werden schriftlich festgelegt.' ],
	[ 't' => 'Eigener Server-Endpunkt', 's' => 'Server-GTM auf Ihrer Tracking-Subdomain und in Ihren Konten. Hosting wird separat abgerechnet und bleibt in Ihrer Verfügung.' ],
	[ 't' => 'Enhanced Conversions nach Datenlage', 's' => 'Die Umsetzung hängt davon ab, was Formular und Consent tragen. Meta CAPI und CRM-Rücksignale werden bei Bedarf separat angeboten.' ],
	[ 't' => 'Paralleltest vor der Umschaltung', 's' => 'Neue und bisherige Messung werden auf fehlende und doppelte Events geprüft. Unterschiede und bekannte Grenzen stehen im Protokoll.' ],
	[ 't' => 'Versionen und Übergabe', 's' => 'Sie erhalten GTM-Versionen, Messplan, Testprotokoll und die Dokumentation des Datenflusses. Zugänge und Konten bleiben bei Ihnen.' ],
];

// ── 11) FAQ ───────────────────────────────────────────────────
$faq = [
	[
		'question' => 'Welches Problem löst Server-Side Tracking — und welches nicht?',
		'answer'   => 'Server-Side Tracking macht den Datenfluss kontrollierbarer: Messsignale laufen zunächst an einen eigenen Endpunkt und werden von dort gezielt an GA4, Google Ads oder Meta weitergegeben. Das kann fehlende oder doppelte Events reduzieren und die Zuordnung stabilisieren. Es erzeugt aber keine zusätzliche Nachfrage und ersetzt weder ein gutes Angebot noch funktionierende Kampagnen.',
	],
	[
		'question' => 'Was ist der Unterschied zwischen Client-Side und Server-Side Tracking?',
		'answer'   => 'Client-Side bedeutet: der Browser des Besuchers sendet die Daten selbst an GA4, Google Ads oder Meta. Das ist einfach einzurichten, hängt aber von Browsereinstellungen, Erweiterungen und Skript-Laufzeiten ab. Server-Side bedeutet: der Browser sendet an einen eigenen Endpunkt, dieser verteilt weiter. In der Praxis ist meist ein hybrides Setup sinnvoll — beide Wege parallel, mit Deduplizierung, damit nichts doppelt gezählt wird.',
	],
	[
		'question' => 'Ist Server-Side Tracking automatisch DSGVO-konform?',
		'answer'   => 'Nein. Der Serverweg ändert nicht automatisch die rechtliche Grundlage einer Verarbeitung. Server-Side Tracking ersetzt weder Consent-Management noch Rechtsberatung. Das technische Setup berücksichtigt die Signale Ihres vorhandenen Consent-Tools; die rechtliche Bewertung des konkreten Datenflusses bleibt eine separate Aufgabe.',
	],
	[
		'question' => 'Funktioniert das mit WordPress und meinen Werbeplattformen?',
		'answer'   => 'WordPress ist ein häufiger Ausgangspunkt. Der serverseitige Grundumfang umfasst die Messbasis mit GA4 und Google Ads, Server-GTM auf eigener Subdomain, Enhanced Conversions nach Datenlage sowie den Paralleltest. Meta CAPI, zusätzliche Events, Shops und CRM-Rücksignale werden nach technischer Aufnahme separat vereinbart.',
	],
	[
		'question' => 'Wie viele Conversions kommen zusätzlich an?',
		'answer'   => 'Eine seriöse Prozentzahl lässt sich vor dem Paralleltest nicht nennen. Das Ergebnis hängt vom bestehenden Setup, Browsermix, Consent-Verhalten und der bisherigen Event-Qualität ab. Deshalb läuft die neue Messung zunächst neben der alten. Entscheidend ist die nachvollziehbare Differenz in Ihren eigenen Konten — nicht eine pauschale Erfolgszahl.',
	],
	[
		'question' => 'Brauche ich eine Server-Side-Tracking-Agentur oder einen spezialisierten Freelancer?',
		'answer'   => 'Für die Qualität des Setups ist weniger die Unternehmensform entscheidend als die Person, die Messkonzept, Server-GTM, Consent, Deduplizierung und Tests tatsächlich verantwortet. Als spezialisierter Freelancer plane und implementiere ich die Messstrecke selbst — ohne Übergabe zwischen Vertrieb, Projektmanagement und Technik. Für größere Setups mit mehreren Märkten, Shops oder komplexen Datenpipelines wird der Umfang vorab klar abgegrenzt.',
	],
	[
		'question' => 'Was kostet Server-Side Tracking?',
		'answer'   => sprintf( 'Der beschriebene serverseitige Grundumfang kostet %1$s netto einmalig, inklusive browserseitiger Messbasis. Server-Hosting kommt separat hinzu. Meta CAPI, CRM-Rücksignale, weitere Systeme und größere Event-Umfänge werden vor Beauftragung zusätzlich kalkuliert. Laufende Betreuung ist optional: für den Grundumfang %2$s netto, %3$s. Reicht ein Setup im Browser, beginnt Conversion-Tracking bei %4$s netto.', $standard_setup_price, $standard_care_price, $standard_terms, $ladder['measurement']['price'] ),
	],
	[
		'question' => 'Wie lange dauert die Einrichtung?',
		'answer'   => sprintf( 'Server-Side Tracking ist in der Regel innerhalb von %s produktiv, gerechnet ab Bereitstellung der Zugänge. Die größte Variable ist meist die Abstimmung der Conversion-Ziele sowie die Freigabe von DNS und Konten. Nach der Übergabe kann laufende Kontrolle separat vereinbart werden.', $delivery_window ),
	],
	[
		'question' => 'Wann lohnt es sich nicht?',
		'answer'   => 'Bei sehr wenig Traffic, ohne laufende Kampagnen, ohne klar definierte Conversion-Ziele oder wenn niemand auf Basis der Daten Budget steuert. Dann ist eine bessere Messung nicht die erste Baustelle. Konten, Container und Hosting würden zwar bei Ihnen bleiben, aber der technische Aufwand hätte noch keinen belastbaren Entscheidungsnutzen.',
	],
];

// ── Schema.org: BreadcrumbList + Service + FAQPage ────────────
$author_person = function_exists( 'hu_get_canonical_author_person' )
	? hu_get_canonical_author_person()
	: [
		'@type' => 'Person',
		'name'  => 'Haşim Üner',
		'url'   => home_url( '/' ),
	];

// Bewusst kein hu_get_solar_subpage_breadcrumb_schema(): die Seite besitzt
// laut docs/seo/query-ownership.csv nicht ortsgebundene, branchenoffene
// Tracking-Queries und haengt inhaltlich nicht unter der Solar-Money-Page.
$breadcrumb_schema = [
	'@context'        => 'https://schema.org',
	'@type'           => 'BreadcrumbList',
	'@id'             => trailingslashit( $page_url ) . '#breadcrumb',
	'itemListElement' => [
		[
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => 'Startseite',
			'item'     => home_url( '/' ),
		],
		[
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => 'Server-Side Tracking',
			'item'     => $page_url,
		],
	],
];

$service_offers = [
	[
		'@type'                 => 'Offer',
		'name'                  => 'Conversion-Tracking mit Server-Side-Messstrecke',
		'description'           => 'Der beschriebene serverseitige Grundumfang inklusive browserseitiger Messbasis. Hosting und bedarfsabhängige Erweiterungen separat.',
		'price'                 => hu_tracking_price( 'standard', 'setup', 'value' ),
		'priceCurrency'         => 'EUR',
		'valueAddedTaxIncluded' => false,
		'url'                   => trailingslashit( $page_url ) . '#pakete',
	],
];

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Conversion-Tracking mit Server-Side-Messstrecke',
	'serviceType' => 'Server-Side Tagging: Server-GTM, GA4 und Google Ads über eine eigene Tracking-Subdomain',
	'url'         => $page_url,
	'description' => 'Einrichtung und Paralleltest von Server-Side Tracking: Server-GTM auf eigener Tracking-Subdomain, GA4, Google Ads und dokumentierte Übergabe. Meta CAPI, CRM-Rücksignale und laufende Kontrolle werden separat vereinbart. Hosting und Konten bleiben beim Kunden.',
	'provider'    => [ '@id' => home_url( '/#organization' ) ],
	'author'      => $author_person,
	'areaServed'  => [
		[
			'@type' => 'Country',
			'name'  => 'Deutschland',
		],
		[
			'@type' => 'Country',
			'name'  => 'Österreich',
		],
		[
			'@type' => 'Country',
			'name'  => 'Schweiz',
		],
	],
	'offers'      => $service_offers,
];

$faq_schema = [
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => trailingslashit( $page_url ) . '#faq',
	'url'        => $page_url,
	'mainEntity' => [],
];

foreach ( $faq as $faq_item ) {
	$faq_schema['mainEntity'][] = [
		'@type'          => 'Question',
		'name'           => $faq_item['question'],
		'acceptedAnswer' => [
			'@type' => 'Answer',
			'text'  => $faq_item['answer'],
		],
	];
}

get_header();
?>

<div id="primary" class="hu-sst doku sst-product-page" data-track-page="server-side-tracking-b2b">
	<header class="kopfteil" id="hero" data-track-section="hero">
		<div class="blatt sst-product-hero">
			<div>
				<p class="gegenstand">Das Tracking-Produkt · serverseitiger Scope</p>
				<h1 id="hu-sst-hero-title">Server-Side Tracking einrichten lassen. Mit Paralleltest in Ihren Konten.</h1>
				<p class="aufriss">Ich richte eine eigene Server-Messstrecke für GA4 und Google Ads ein und prüfe sie gegen die bisherige Messung. Für Unternehmen mit laufenden Kampagnen und klaren Conversion-Zielen.</p>
				<div class="ausgang">
					<a class="tun" href="<?php echo esc_url( $form_anchor ); ?>" data-track-action="cta_form_tracking_check" data-track-category="server_side_tracking_b2b" data-track-section="hero"><?php echo esc_html( $setup_cta_label ); ?> <span aria-hidden="true">→</span></a>
					<a class="textlink" href="#umfang" data-track-action="cta_scope" data-track-category="server_side_tracking_b2b" data-track-section="hero">Den Lieferumfang ansehen</a>
				</div>
				<p class="mono"><?php echo esc_html( hu_response_promise( 'compact' ) ); ?> · Anfrage ist noch kein Auftrag</p>
			</div>
			<aside class="sst-product-brief" aria-label="Preis und Umfang des serverseitigen Tracking-Setups">
				<p class="mono stempelfarbe">Inklusive browserseitiger Messbasis</p>
				<p class="sst-product-price"><?php echo esc_html( $standard_setup_price ); ?><small>netto · einmalig für den beschriebenen Scope</small></p>
				<dl><div><dt>Umsetzung</dt><dd><?php echo esc_html( $delivery_window ); ?></dd></div><div><dt>Abnahme</dt><dd>Paralleltest und Protokoll</dd></div><div><dt>Eigentum</dt><dd>Konten und Container bei Ihnen</dd></div></dl>
				<p>Server-Hosting zusätzlich. Meta CAPI, CRM und laufende Betreuung werden separat vereinbart.</p>
			</aside>
		</div>
	</header>

	<nav class="blatt sst-product-nav" aria-label="Abschnitte dieser Seite" data-track-section="toc">
		<a href="#umfang" data-track-action="toc_scope" data-track-category="server_side_tracking_b2b">Lieferumfang</a><a href="#pruefung" data-track-action="toc_acceptance" data-track-category="server_side_tracking_b2b">Paralleltest</a><a href="#pakete" data-track-action="toc_packages" data-track-category="server_side_tracking_b2b">Preis &amp; Rahmen</a><a href="#faq" data-track-action="toc_faq" data-track-category="server_side_tracking_b2b">Fragen</a>
	</nav>

	<section id="umfang" data-track-section="umfang">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Lieferumfang</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf">Eine kontrollierbare Server-Messstrecke. Fünf Lieferbausteine.</h2>
				<ol class="sst-product-features" role="list">
					<?php foreach ( $setup_items as $index => $item ) : ?>
						<li><span class="sst-product-features__nr" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span><div><h3><?php echo esc_html( $item['t'] ); ?></h3><p><?php echo esc_html( $item['s'] ); ?></p></div></li>
					<?php endforeach; ?>
				</ol>
			</div>
			<aside class="marg"><p class="note"><span class="label">Zuerst den Bedarf klären</span><a class="satzlink" href="<?php echo esc_url( $ga4_setup_url . '#stufe-1' ); ?>" data-track-action="cta_package_to_measurement" data-track-category="server_side_tracking_b2b">Das Tracking-Grundprodukt misst im Browser.</a> Ein Server wird ergänzt, wenn er für Ihre Messstrecke einen konkreten Zweck erfüllt.</p></aside>
		</div>
	</section>

	<section id="pruefung" data-track-section="acceptance">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Paralleltest</span><span class="strich"></span></div></div>
			<div class="haupt">
				<p class="mono stempelfarbe">Prüfbarer Lieferbeleg</p>
				<h2 class="kopf">Dasselbe Ereignis. Zwei Messwege. Ein Protokoll.</h2>
				<p class="vorspann">Vor der Umschaltung vergleiche ich den bestehenden Browserweg und den neuen Serverweg in Ihren Konten. Die Prüfung hält fest, welche Signale ankommen, fehlen oder doppelt gezählt werden.</p>
				<div class="sst-product-protocol"><p class="mono">Beispielstruktur · kein Kundenprotokoll</p><div class="protokoll"><div class="z"><span>Ereignis</span><b>Auslöser · Parameter · Consent-Zustand</b></div><div class="z"><span>Vergleich</span><b>Browser-Signal · Server-Signal · Abweichung</b></div><div class="z"><span>Übergabe</span><b>Messplan · Prüfergebnis · bekannte Grenzen</b></div></div></div>
				<p class="sst-product-evidence"><a class="satzlink" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="internal_sst_solar_case" data-track-category="internal_link" data-track-section="acceptance">Arbeitsbeleg: Tracking in einer vollständigen B2B-Anfragestrecke →</a><span>Die Fallzahlen stammen aus dem Zusammenspiel von Kampagnen, Landingpages, Tracking und Vertrieb.</span></p>
			</div>
			<aside class="marg"><p class="note"><span class="label">Bekannte Grenzen</span>Server-Side ersetzt keine Einwilligung und liefert keine vollständige Attribution. Unterschiede in Zählweise und Zeitfenstern werden erklärt.</p></aside>
		</div>
	</section>

	<section id="pakete" data-track-section="pakete">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Preis &amp; Rahmen</span><span class="strich"></span></div></div>
			<div class="voll"><div class="tafel sst-product-contract">
				<div><p class="mono stempelfarbe">Ein Festpreis für den beschriebenen Scope</p><h2>Server-Messstrecke für <?php echo esc_html( $standard_setup_price ); ?> netto.</h2><p class="aufriss">Der Gesamtpreis enthält die browserseitige Messbasis und die fünf serverseitigen Lieferbausteine. Die vereinbarten Systeme, Formulare, Events und Consent-Grenzen stehen vor Projektstart schriftlich fest.</p><div class="ausgang"><a class="tun" href="<?php echo esc_url( $form_anchor ); ?>" data-track-action="cta_package_standard" data-track-category="server_side_tracking_b2b" data-track-section="pakete"><?php echo esc_html( $setup_cta_label ); ?> <span aria-hidden="true">→</span></a></div></div>
				<div class="sst-product-contract__costs"><h3>Zusätzliche Kosten vorab klären.</h3><p>Server-Hosting wird direkt über Ihr Konto abgerechnet. Meta CAPI, CRM-Rücksignale, zusätzliche Plattformen und größere Event-Umfänge werden nach Bestandsaufnahme separat kalkuliert.</p><p>Laufende Betreuung ist optional: <?php echo esc_html( $standard_care_price ); ?> netto für den Grundumfang, <?php echo esc_html( $standard_terms ); ?>. Größere Setups erhalten einen eigenen Prüfumfang und Preis.</p><p>Reicht Messung im Browser, kostet das <a class="satzlink" href="<?php echo esc_url( $ga4_setup_url ); ?>" data-track-action="internal_ga4_setup" data-track-category="internal_link">Tracking-Grundprodukt <?php echo esc_html( $ladder['measurement']['price'] ); ?> netto</a>.</p></div>
			</div></div>
		</div>
	</section>

	<section id="vertiefung" data-track-section="technical_details">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Vertiefung</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf leise">Die Details hinter dem Setup.</h2>
				<div class="fragen sst-product-details">
					<details id="symptome"><summary>Wann ein Server die passende nächste Prüfung ist</summary><div class="huelle"><div><p>Fehlende oder doppelte Conversions und unklare Übergaben werden zuerst auf Ihre Ursache geprüft. Browser-Abweichungen allein beweisen keinen Serverbedarf. Entscheidend ist, ob eine serverseitige Datenstrecke für Ihre Kampagnen und Entscheidungen einen prüfbaren Nutzen bietet.</p><p>Es braucht klare Conversion-Ziele, die vereinbarten Konten- und DNS-Zugänge und eine Person, die die Messung fachlich verantwortet.</p></div></div></details>
					<details id="unterschied"><summary>Wie Client-Side und Server-Side zusammenspielen</summary><div class="huelle"><div><p>Browserseitig gehen Signale direkt an Analyse- und Werbeplattformen. Serverseitig laufen sie zunächst an Ihren eigenen Endpunkt und werden von dort nach definierten Regeln weitergegeben. Die Wege werden gemeinsam geprüft.</p><p>Das vorhandene Consent-System steuert die vereinbarten Signale. Server-Side Tracking ersetzt die rechtliche Bewertung des Datenflusses nicht.</p><p><a class="satzlink" href="<?php echo esc_url( $gtm_guide_url ); ?>" data-track-action="internal_sst_gtm" data-track-category="internal_link">Technische Vertiefung: Server-Side Tracking mit GTM →</a></p></div></div></details>
					<details id="ablauf"><summary>Von der Bestandsaufnahme zur Abnahme</summary><div class="huelle"><div><ol><li>Bestehende Messung, Systeme und Abweichungen aufnehmen.</li><li>Ziele, Umfang, Preis und Verantwortlichkeiten schriftlich klären.</li><li>Server-GTM und Subdomain in Ihren Konten einrichten.</li><li>Neue und bisherige Messung parallel prüfen.</li><li>Versionen, Protokoll und Dokumentation übergeben.</li></ol><p>Geplant sind <?php echo esc_html( $delivery_window ); ?> ab Bereitstellung der vereinbarten Zugänge. DNS-Freigaben und Abstimmung der Ziele beeinflussen den Start.</p></div></div></details>
					<details id="care"><summary>Was die optionale laufende Betreuung umfasst</summary><div class="huelle"><div><p>Für den serverseitigen Grundumfang: monatlicher Funktionstest der Haupt-Conversions und <?php echo esc_html( $standard_minutes ); ?> Minuten kleinere Korrekturen. Geprüft werden GA4, Werbeplattformen, Subdomain und Consent-Signale; Auffälligkeiten werden dokumentiert.</p><p>Neue Plattformen, CRM-Integrationen, umfangreiche Website-Umbauten und ein Wechsel des Consent-Tools sind zusätzliche Projekte. Umfang und Preis werden vorher vereinbart.</p></div></div></details>
					<details id="sicherheit"><summary>Konten, Zugänge und technische Datenschutzgrenzen</summary><div class="huelle"><div><p>Ihre Konten und Container bleiben getrennt und in Ihrer Verfügung. Zugriff wird auf die Arbeit begrenzt; GTM-Versionen und Datenflüsse werden dokumentiert. Zugangsdaten gehören nicht ins öffentliche Formular.</p><p>Das Setup verarbeitet nur die vereinbarten Daten. Ein Server garantiert keine rechtliche Konformität; die Bewertung Ihres konkreten Datenflusses bleibt separat.</p><p>Sie betreiben Solar oder SHK und brauchen eine gesamte Anfragestrecke? <a class="satzlink" href="<?php echo esc_url( $marktcheck_url ); ?>" data-track-action="cta_marktcheck_branch" data-track-category="server_side_tracking_b2b">Zum branchenspezifischen Marktcheck →</a></p></div></div></details>
				</div>
			</div>
		</div>
	</section>

	<section id="faq" data-track-section="faq">
		<div class="blatt reihe"><div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Fragen</span><span class="strich"></span></div></div><div class="haupt"><h2 class="kopf leise">Server-Side Tracking: Technik, Kosten und Grenzen.</h2><div class="fragen">
			<?php foreach ( $faq as $item ) : ?>
				<details><summary><?php echo esc_html( $item['question'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $item['answer'] ); ?></p></div></div></details>
			<?php endforeach; ?>
		</div></div></div>
	</section>

	<?php // ── 12 Formular ── dunkel ────────────────────────── ?>
	<section class="sst-product-form" id="anfrage" aria-labelledby="hu-sst-form-title">
		<div class="blatt sst-product-form__inner">
			<div class="sst-product-form__head">
				<p class="mono stempelfarbe">Nächster Schritt</p>
				<h2 class="kopf" id="hu-sst-form-title">Welche Messstrecke soll bei Ihnen funktionieren?</h2>
				<p class="vorspann">
					Beschreiben Sie kurz, welche Zahlen nicht zusammenpassen oder was künftig sauber gemessen werden soll. Sie erhalten eine Einordnung, den passenden Umfang und die offenen Voraussetzungen. Die Anfrage ist unverbindlich.
				</p>
			</div>

			<div class="contact-error-summary is-hidden" role="alert" aria-live="assertive" data-contact-error-summary>
				<p class="contact-error-summary__title">Bitte prüfen Sie folgende Felder:</p>
				<ul class="contact-error-summary__list" data-contact-error-list></ul>
			</div>

			<form
				class="contact-form hu-sst__form"
				data-contact-form
				data-contact-dom-error-order
				action="<?php echo esc_url( $rest_endpoint ); ?>"
				method="post"
				novalidate
			>
				<div class="contact-form__honeypot" aria-hidden="true">
					<label for="contact-company-website">Website</label>
					<input id="contact-company-website" type="text" name="company_website" tabindex="-1" autocomplete="off">
				</div>
				<input type="hidden" name="ads_source" id="ads_source" value="">
				<input type="hidden" name="ads_keyword" id="ads_keyword" value="">
				<input type="hidden" name="utm_medium" id="utm_medium" value="">
				<input type="hidden" name="utm_campaign" id="utm_campaign" value="">
				<input type="hidden" name="gclid" id="gclid" value="">
				<input type="hidden" name="matchtype" id="matchtype" value="">

				<?php
				// Anfragetyp und Thema sind auf dieser Seite fest vorbelegt. Das
				// gemeinsame Formular-JS prueft ein gesetztes Radio und eine
				// Focus-Option, deren data-types den Anfragetyp enthaelt.
				?>
				<div class="hu-sst__form-fixed" hidden>
					<input type="hidden" name="form_origin" value="sst">
					<input
						id="contact-type-project"
						type="radio"
						name="request_type"
						value="project"
						checked
						required
						data-contact-type-input
					>
					<label for="contact-type-project">Projektprüfung</label>
					<label for="contact-focus">Thema</label>
					<select id="contact-focus" name="focus" required data-contact-focus-select aria-describedby="contact-focus-error">
						<option value="tracking" data-types="<?php echo esc_attr( $tracking_focus_types ); ?>" selected>Tracking &amp; Analytics</option>
					</select>
					<p class="contact-field__error is-hidden" id="contact-focus-error" aria-live="polite"></p>
				</div>

				<div class="contact-form__row">
					<div class="contact-field" data-contact-field="name">
						<label for="contact-name">Name</label>
						<input id="contact-name" name="name" type="text" autocomplete="name" required aria-describedby="contact-name-error">
						<p class="contact-field__error is-hidden" id="contact-name-error" aria-live="polite"></p>
					</div>

					<div class="contact-field" data-contact-field="email">
						<label for="contact-email">Geschäftliche E-Mail</label>
						<input id="contact-email" name="email" type="email" autocomplete="email" required aria-describedby="contact-email-error">
						<p class="contact-field__error is-hidden" id="contact-email-error" aria-live="polite"></p>
					</div>
				</div>

				<div class="contact-field">
					<label for="contact-website">Website <span class="hu-sst__optional">optional</span></label>
					<p id="contact-website-help" class="contact-field__help">Hilft bei der ersten technischen Einordnung; Zugänge sind dafür nicht nötig.</p>
					<input id="contact-website" name="website_url" type="url" autocomplete="url" inputmode="url" placeholder="https://example.de" aria-describedby="contact-website-help">
				</div>

				<div class="contact-field" data-contact-field="message">
					<label for="contact-message">Konkretes Problem oder Ziel</label>
					<p id="contact-message-help" class="contact-field__help">Welche Zahlen widersprechen sich — oder was soll künftig verlässlich als Conversion ankommen?</p>
					<textarea
						id="contact-message"
						name="message"
						rows="5"
						required
						minlength="24"
						aria-describedby="contact-message-help contact-message-error"
						data-contact-message
						data-contact-message-placeholder="z. B. GA4 meldet weniger Anfragen als Google Ads; Meta zählt doppelt."
					></textarea>
					<p class="contact-field__error is-hidden" id="contact-message-error" aria-live="polite"></p>
				</div>

				<?php if ( ! empty( $referral_options ) ) : ?>
					<div class="contact-field">
						<label for="contact-referral">Wie sind Sie auf mich aufmerksam geworden? <span class="hu-sst__optional">optional</span></label>
						<select id="contact-referral" name="referral_source">
							<option value="" selected>Bitte auswählen</option>
							<?php foreach ( $referral_options as $referral_key => $referral_label ) : ?>
								<option value="<?php echo esc_attr( $referral_key ); ?>"><?php echo esc_html( $referral_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>

				<details class="hu-sst__form-details">
					<summary>Technische Angaben <span>optional</span></summary>
					<div class="hu-sst__form-details-body">
						<div class="contact-field">
							<label for="contact-company">Unternehmen <span class="hu-sst__optional">optional</span></label>
							<input id="contact-company" name="company" type="text" autocomplete="organization" maxlength="120">
						</div>

						<fieldset class="hu-sst__fieldset">
							<legend>Verwendete Werbeplattformen <span class="hu-sst__optional">optional</span></legend>
							<div class="hu-sst__checks">
								<?php foreach ( $ad_platform_options as $platform_key => $platform_label ) : ?>
									<label class="hu-sst__check" for="<?php echo esc_attr( 'contact-ad-platform-' . $platform_key ); ?>">
										<input
											id="<?php echo esc_attr( 'contact-ad-platform-' . $platform_key ); ?>"
											type="checkbox"
											name="<?php echo esc_attr( 'ad_platform_' . $platform_key ); ?>"
											value="1"
										>
										<span><?php echo esc_html( $platform_label ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</fieldset>

						<div class="contact-field" data-contact-field="ad_budget">
							<label for="contact-ad_budget">Ungefähres monatliches Werbebudget <span class="hu-sst__optional">optional</span></label>
							<select id="contact-ad_budget" name="ad_budget" aria-describedby="contact-ad_budget-error">
								<option value="" selected>Bitte auswählen</option>
								<?php foreach ( $ad_budget_options as $budget_key => $budget_label ) : ?>
									<option value="<?php echo esc_attr( $budget_key ); ?>"><?php echo esc_html( $budget_label ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="contact-field__error is-hidden" id="contact-ad_budget-error" aria-live="polite"></p>
						</div>

						<div class="contact-field">
							<label for="contact-tracking-setup">Aktuelles Tracking-Setup <span class="hu-sst__optional">optional</span></label>
							<p id="contact-tracking-setup-help" class="contact-field__help">Was heute läuft: GA4, Google Ads, Meta Pixel, Plugins, bereits vorhandener Server-Container.</p>
							<textarea id="contact-tracking-setup" name="tracking_setup" rows="3" maxlength="2000" aria-describedby="contact-tracking-setup-help"></textarea>
						</div>

						<div class="contact-field">
							<label for="contact-consent-tool">Verwendetes Consent-Tool <span class="hu-sst__optional">optional</span></label>
							<input id="contact-consent-tool" name="consent_tool" type="text" maxlength="120" placeholder="z. B. Cookiebot, Usercentrics, Complianz">
						</div>
					</div>
				</details>

				<label class="contact-consent" data-contact-field="consent">
					<input type="checkbox" name="consent" value="1" required aria-describedby="contact-consent-error">
					<span>
						Ich stimme zu, dass meine Angaben zur Bearbeitung meiner Anfrage verarbeitet werden.
						Mehr dazu in der <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutzerklärung</a>.
					</span>
					<p class="contact-field__error is-hidden" id="contact-consent-error" aria-live="polite"></p>
				</label>

				<p class="hu-sst__form-hint">
					Bitte keine Passwörter, API-Keys oder Zugangsdaten in dieses Formular eintragen. Zugänge werden erst nach Beauftragung und auf sicherem Weg ausgetauscht.
				</p>

				<div class="contact-form__actions">
					<button class="contact-submit" type="submit" data-contact-submit data-contact-submit-label="<?php echo esc_attr( $setup_cta_label ); ?>" data-track-action="contact_submit" data-track-category="server_side_tracking_b2b" data-track-section="sst_form">
						<?php echo esc_html( $setup_cta_label ); ?>
					</button>
				</div>

				<div class="contact-form__feedback" data-contact-feedback aria-live="polite" role="status"></div>
			</form>
		</div>
	</section>

	<script type="application/ld+json"><?php echo wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
</div>

<?php
get_template_part(
	'template-parts/seo-subpage-sticky-cta',
	null,
	[
		'cta_url'           => $form_anchor,
		'track_category'    => 'server_side_tracking_b2b',
		'region_label'      => 'Schnellzugang zur Anfrage',
		'lead'              => 'Tracking anfragen',
		'sub'               => 'Fit und Scope vor Angebot',
		'label'             => $setup_cta_label,
		'track_action'      => 'cta_sticky_form_tracking',
		'hide_when_visible' => '#anfrage',
	]
);

get_footer();
