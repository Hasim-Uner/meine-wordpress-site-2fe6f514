<?php
/**
 * Template Name: Server-Side Tracking für B2B-Leadgenerierung
 * Description: Money-Page für Server-Side Tracking (Server-GTM, GA4, Google Ads,
 *              Meta CAPI, Consent-Anbindung) mit Leistungsumfang, Paketen,
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
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$privacy_url     = function_exists( 'nexus_get_page_url' )
	? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) )
	: home_url( '/datenschutz/' );
$form_anchor     = '#anfrage';
$rest_endpoint   = rest_url( 'nexus/v1/contact-request' );
$setup_cta_label = 'Setup-Empfehlung anfordern';

// ── Route-spezifischer Preis- und Lieferkanon ─────────────────
$standard_setup_price = function_exists( 'hu_tracking_price' )
	? hu_tracking_price( 'standard', 'setup', 'display', '1.290 €' )
	: '1.290 €';
$standard_care_price = function_exists( 'hu_tracking_price' )
	? hu_tracking_price( 'standard', 'care', 'display', '99 € / Monat' )
	: '99 € / Monat';
$pro_setup_price = function_exists( 'hu_tracking_price' )
	? hu_tracking_price( 'pro', 'setup', 'display', '1.900 €' )
	: '1.900 €';
$pro_care_price = function_exists( 'hu_tracking_price' )
	? hu_tracking_price( 'pro', 'care', 'display', '149 € / Monat' )
	: '149 € / Monat';
$individual_setup_price = function_exists( 'hu_tracking_price' )
	? hu_tracking_price( 'individual', 'setup', 'display', 'ab 3.500 €' )
	: 'ab 3.500 €';
$individual_care_price = function_exists( 'hu_tracking_price' )
	? hu_tracking_price( 'individual', 'care', 'display', 'ab 199 € / Monat' )
	: 'ab 199 € / Monat';
$standard_terms = function_exists( 'hu_tracking_package_detail' )
	? hu_tracking_package_detail( 'standard', 'terms', 'Nettopreise, monatlich kündbar, Hosting separat' )
	: 'Nettopreise, monatlich kündbar, Hosting separat';
$pro_terms = function_exists( 'hu_tracking_package_detail' )
	? hu_tracking_package_detail( 'pro', 'terms', 'Nettopreise, monatlich kündbar, Hosting separat' )
	: 'Nettopreise, monatlich kündbar, Hosting separat';
$individual_terms = function_exists( 'hu_tracking_package_detail' )
	? hu_tracking_package_detail( 'individual', 'terms', 'Nettopreise, Umfang nach Aufnahme, Hosting separat' )
	: 'Nettopreise, Umfang nach Aufnahme, Hosting separat';
$standard_minutes = function_exists( 'hu_tracking_package_detail' )
	? hu_tracking_package_detail( 'standard', 'included_minutes', '30' )
	: '30';
$pro_minutes = function_exists( 'hu_tracking_package_detail' )
	? hu_tracking_package_detail( 'pro', 'included_minutes', '60' )
	: '60';
$response_days = function_exists( 'hu_tracking_package_detail' )
	? hu_tracking_package_detail( 'standard', 'response_business_days', '2' )
	: '2';
$delivery_window = function_exists( 'hu_tracking_delivery_weeks_display' )
	? hu_tracking_delivery_weeks_display()
	: '2 bis 3 Wochen';

// ── Formular-Registries (bestehender Kontakt-Intake) ──────────
$ad_platform_options = function_exists( 'nexus_get_contact_ad_platform_options' )
	? nexus_get_contact_ad_platform_options()
	: [];
$ad_budget_options   = function_exists( 'nexus_get_contact_ad_budget_options' )
	? nexus_get_contact_ad_budget_options()
	: [];
$focus_options       = function_exists( 'nexus_get_contact_focus_options' )
	? nexus_get_contact_focus_options()
	: [];
$tracking_focus_types = isset( $focus_options['tracking']['types'] )
	? implode( ',', array_map( 'sanitize_key', (array) $focus_options['tracking']['types'] ) )
	: 'project';

/**
 * Render a 24x24 stroke icon for this template.
 *
 * @param string $paths Raw SVG path markup.
 * @return string
 */
function hu_sst_icon_svg( $paths ) {
	return '<svg class="hu-sst__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths . '</svg>';
}

// ── 2) Symptome ───────────────────────────────────────────────
$symptoms = [
	[
		't' => 'CRM und Werbekonten melden unterschiedliche Zahlen',
		's' => 'Ohne gemeinsame Event-Definition und dokumentierten Datenfluss bleibt offen, welche Zahl für Budgetentscheidungen belastbar genug ist.',
	],
	[
		't' => 'Conversions fehlen oder werden doppelt gezählt',
		's' => 'Dieselbe Anfrage taucht als Formular-Absenden, Danke-Seiten-Aufruf und Klick-Conversion auf. Oder sie taucht gar nicht auf, weil der Browser den Request abgebrochen hat.',
	],
	[
		't' => 'Kampagnen optimieren auf unvollständige Signale',
		's' => 'Smart Bidding lernt aus dem, was ankommt. Fehlen Signale systematisch bei bestimmten Browsern oder Endgeräten, verschiebt sich die Aussteuerung dorthin, wo gemessen wird — nicht dorthin, wo verkauft wird.',
	],
];

// ── 4) Fit / Non-Fit ──────────────────────────────────────────
$fit_yes = [
	'Sie schalten laufend Google Ads oder Meta Ads mit relevantem Budget.',
	'Ihre Website läuft auf WordPress oder einem vergleichbaren Lead-Funnel.',
	'Es gibt mehrere Formulare oder mehrere Conversion-Strecken.',
	'Zwischen CRM und Werbeplattformen bestehen erklärungsbedürftige Abweichungen.',
	'Sie brauchen Meta CAPI oder Enhanced Conversions und haben es bisher nicht sauber umgesetzt.',
	'Im Haus gibt es niemanden, der die Tracking-Verantwortung dauerhaft trägt.',
];

$fit_no = [
	'Reine Informationsseiten ohne laufende Kampagnen.',
	'Sehr wenig Traffic — dann fehlt die Datenmenge, an der sich eine Verbesserung überhaupt zeigen könnte.',
	'Keine klar definierten Conversion-Ziele. Was nicht definiert ist, lässt sich auch serverseitig nicht messen.',
	'Keine laufende Kampagnensteuerung. Ohne jemanden, der auf die Daten reagiert, ist die Messung Selbstzweck.',
];

// ── 5) Architektur / Datenfluss ───────────────────────────────
$flow_chain = [
	[
		'label' => 'Website',
		'note'  => 'WordPress, Formulare, Funnel-Schritte',
	],
	[
		'label' => 'Web-GTM',
		'note'  => 'Container im Browser, Consent-Status',
	],
	[
		'label' => 'Eigene Tracking-Subdomain',
		'note'  => 'z. B. sgtm.ihre-domain.de',
	],
	[
		'label' => 'Server-GTM auf Stape EU',
		'note'  => 'Verarbeitung, Anreicherung, Weitergabe',
	],
];

$flow_outputs = [
	[
		'label'    => 'GA4',
		'note'     => 'Property des Kunden',
		'optional' => false,
	],
	[
		'label'    => 'Google Ads',
		'note'     => 'Conversions, Enhanced Conversions',
		'optional' => false,
	],
	[
		'label'    => 'Meta CAPI',
		'note'     => 'mit Deduplizierung gegen den Pixel',
		'optional' => true,
	],
	[
		'label'    => 'CRM',
		'note'     => 'Lead-Status, Offline-Conversions',
		'optional' => true,
	],
];

// ── 6) Leistungsumfang ────────────────────────────────────────
$setup_items = [
	[
		't' => 'Bestandsaufnahme und Messplan',
		's' => 'Container, Tags, Pixel, Formulare und Conversion-Ziele werden geprüft. Danach steht schriftlich fest, welches Event wann feuert und was es transportiert.',
	],
	[
		't' => 'Server-GTM und eigene Subdomain',
		's' => 'Server-Container, DNS-Anbindung und Stape-EU-Hosting werden in Ihren Konten eingerichtet — nicht in einer fremden Sammelinfrastruktur.',
	],
	[
		't' => 'GA4, Google Ads und optional Meta CAPI',
		's' => 'Die vereinbarten Plattformen werden angebunden; Enhanced Conversions und Deduplizierung nur dort, wo Formular, Consent und Datenlage es tragen.',
	],
	[
		't' => 'Consent-Signale im Datenfluss',
		's' => 'Das vorhandene Consent-System wird mit Web- und Server-Container verbunden. Server-Side Tracking ersetzt weder Einwilligung noch rechtliche Prüfung.',
	],
	[
		't' => 'Paralleltest vor der Umschaltung',
		's' => 'Alte und neue Messung laufen zunächst nebeneinander. Fehlende, doppelte oder falsch zugeordnete Events werden in Ihren eigenen Konten sichtbar.',
	],
	[
		't' => 'Versionierte Übergabe',
		's' => 'Sie erhalten benannte GTM-Versionen, Event-Definitionen, Datenfluss-Dokumentation und alle Zugänge. Konten und Hosting bleiben bei Ihnen.',
	],
];

// ── 7) Pakete ─────────────────────────────────────────────────
$packages = [
	[
		'key'      => 'standard',
		'name'     => 'Standard · GA4 + Google Ads',
		'setup'    => $standard_setup_price,
		'monthly'  => $standard_care_price,
		'terms'    => $standard_terms,
		'lead'     => 'Für eine Website mit klaren Haupt-Conversions und einem überschaubaren Google-Setup.',
		'featured' => false,
		'items'    => [
			'Eine Website oder Domain',
			'Server-GTM, eigene Subdomain und Consent-Anbindung',
			'GA4, Google Ads und bis zu drei Haupt-Conversions',
			'Paralleltest, Dokumentation und Übergabe',
			sprintf( 'Monatlicher Funktionstest plus %s Minuten kleinere Korrekturen', $standard_minutes ),
			sprintf( 'Supportantwort innerhalb von %s Werktagen', $response_days ),
		],
		'cta'      => $setup_cta_label,
		'action'   => 'cta_package_standard',
	],
	[
		'key'      => 'pro',
		'name'     => 'Pro · Google + Meta CAPI',
		'setup'    => $pro_setup_price,
		'monthly'  => $pro_care_price,
		'terms'    => $pro_terms,
		'lead'     => 'Für Google- und Meta-Kampagnen, mehrere Formulare oder mehrere Conversion-Strecken.',
		'featured' => true,
		'items'    => [
			'Alles aus Standard',
			'Meta Pixel und Meta Conversion API',
			'Deduplizierung zwischen Browser und Server',
			'Bis zu acht definierte Events',
			'Mehrere Formulare oder Conversion-Strecken',
			sprintf( 'Monatliche Kontrolle plus %s Minuten kleinere Anpassungen', $pro_minutes ),
		],
		'cta'      => $setup_cta_label,
		'action'   => 'cta_package_pro',
	],
	[
		'key'      => 'individual',
		'name'     => 'Individuell · CRM + Offline-Conversions',
		'setup'    => $individual_setup_price,
		'monthly'  => $individual_care_price,
		'terms'    => $individual_terms,
		'lead'     => 'Wenn CRM, Shop, mehrere Märkte oder individuelle Datenpipelines Teil der Messstrecke sind.',
		'featured' => false,
		'items'    => [
			'CRM-Anbindung und Offline-Conversions',
			'Leadstatus oder Lead-Scoring',
			'Shop-Systeme mit Kauf- und Checkout-Events',
			'Mehrere Domains oder Märkte',
			'Weitere Werbeplattformen nach Aufnahme',
			'Eigene Infrastruktur oder individuelle Datenpipelines',
		],
		'cta'      => $setup_cta_label,
		'action'   => 'cta_package_individual',
	],
];

// ── 8) Tracking Care ──────────────────────────────────────────
$care_included = [
	'Regelmäßige Prüfung der Haupt-Conversions',
	'Kontrolle von GA4 und Werbeplattformen',
	'Kontrolle der Tracking-Subdomain',
	'Prüfung der Consent-Signale',
	'Erkennung fehlender oder doppelter Events',
	'GTM-Versionierung',
	'Kleinere Fehlerkorrekturen im vereinbarten Zeitrahmen',
	'Kurze Statusmeldung bei Auffälligkeiten',
];

$care_excluded = [
	'Neue Plattformen',
	'Neue Websites oder Funnels',
	'Umfangreiche Website-Umbauten',
	'Wechsel des Consent-Systems',
	'Neue CRM-Integrationen',
	'Datenschutzberatung',
	'Arbeiten außerhalb des vereinbarten Zeitbudgets',
];

// ── 9) Ablauf ─────────────────────────────────────────────────
$process_steps = [
	[
		't' => 'Vorprüfung und Fit-Entscheid',
		's' => 'Website, Kampagnen, Konten und bekannte Abweichungen einordnen. Wenn Server-Side Tracking nicht die richtige erste Baustelle ist, erfahren Sie es hier.',
	],
	[
		't' => 'Scope und Messplan',
		's' => 'Plattformen, Events, Consent-Grenzen, Eigentum und Paket schriftlich festlegen — bevor Zugänge ausgetauscht oder Container gebaut werden.',
	],
	[
		't' => 'Einrichtung und Paralleltest',
		's' => 'Server-GTM, Subdomain und vereinbarte Plattformen einrichten. Neue und bisherige Messung nebeneinander prüfen und Abweichungen korrigieren.',
	],
	[
		't' => 'Übergabe und Tracking Care',
		's' => 'Dokumentation, GTM-Versionen und Zugänge übergeben. Danach kontrolliert Tracking Care die Haupt-Conversions und meldet Auffälligkeiten.',
	],
];

// ── 10) Sicherheit und Eigentum ───────────────────────────────
$security_items = [
	'Konten und Container gehören dem Kunden — angelegt in seinen Konten, nicht in unseren.',
	'Getrennte Systeme pro Kunde. Keine geteilten Container über mehrere Betriebe hinweg.',
	'Zwei-Faktor-Authentifizierung für alle beteiligten Konten empfohlen.',
	'Zugriffe werden auf das für die Arbeit nötige Minimum begrenzt.',
	'Keine Zugangsdaten über öffentliche Formulare — auch nicht über das Formular auf dieser Seite.',
	'Keine Secrets im Repository.',
	'Keine unnötigen personenbezogenen Daten an Analyseplattformen.',
	'Ausgehende Endpunkte und Templates werden kontrolliert und dokumentiert.',
	'GTM-Versionen werden dokumentiert, damit Änderungen nachvollziehbar bleiben.',
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
		'answer'   => 'WordPress ist der häufigste Ausgangspunkt. Standardmäßig werden GA4 und Google Ads angebunden, im Pro-Setup zusätzlich Meta Pixel und Meta Conversion API mit Deduplizierung. Weitere Plattformen, Shops oder CRM-Systeme werden nach technischer Aufnahme individuell bewertet.',
	],
	[
		'question' => 'Wie viele Conversions kommen zusätzlich an?',
		'answer'   => 'Eine seriöse Prozentzahl lässt sich vor dem Paralleltest nicht nennen. Das Ergebnis hängt vom bestehenden Setup, Browsermix, Consent-Verhalten und der bisherigen Event-Qualität ab. Deshalb läuft die neue Messung zunächst neben der alten. Entscheidend ist die nachvollziehbare Differenz in Ihren eigenen Konten — nicht eine pauschale Erfolgszahl.',
	],
	[
		'question' => 'Was kostet Server-Side Tracking?',
		'answer'   => sprintf( 'Standard kostet %1$s einmalig plus %2$s für Tracking Care. Pro mit Meta CAPI kostet %3$s einmalig plus %4$s. Individuelle Setups: %5$s einmalig; Betreuung: %6$s. Das Stape-Hosting ist nicht enthalten und läuft direkt über Ihr eigenes Konto. Alle genannten Preise sind Nettopreise für Geschäftskunden.', $standard_setup_price, $standard_care_price, $pro_setup_price, $pro_care_price, $individual_setup_price, $individual_care_price ),
	],
	[
		'question' => 'Wie lange dauert die Einrichtung?',
		'answer'   => sprintf( 'Ein Standard-Setup ist in der Regel innerhalb von %s produktiv, gerechnet ab Bereitstellung der Zugänge. Die größte Variable ist meist die Abstimmung der Conversion-Ziele sowie die Freigabe von DNS und Konten. Nach der Übergabe kontrolliert Tracking Care die Haupt-Conversions und meldet Auffälligkeiten nach Website- oder Plattformänderungen.', $delivery_window ),
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

$service_offers = [];
foreach ( $packages as $package ) {
	$service_offers[] = [
		'@type'                 => 'Offer',
		'name'                  => $package['name'],
		'description'           => $package['lead'],
		'priceCurrency'         => 'EUR',
		'valueAddedTaxIncluded' => false,
		'url'                   => trailingslashit( $page_url ) . '#pakete',
	];
}

$service_schema = [
	'@context'    => 'https://schema.org',
	'@type'       => 'Service',
	'@id'         => trailingslashit( $page_url ) . '#service',
	'name'        => 'Server-Side Tracking einrichten und betreuen',
	'serviceType' => 'Server-Side Tagging: Server-GTM, GA4, Google Ads und Meta Conversion API über eine eigene Tracking-Subdomain',
	'url'         => $page_url,
	'description' => 'Einrichtung, Testbetrieb und laufende Kontrolle von Server-Side Tracking für Unternehmen mit laufenden Google-Ads- oder Meta-Ads-Kampagnen: Server-GTM über Stape EU, eigene Tracking-Subdomain, GA4, Google Ads, optional Meta CAPI und CRM-Anbindung. Konten und Container bleiben beim Kunden.',
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

<main id="primary" class="hu-intercept hu-sst" role="main" data-track-page="server-side-tracking-b2b">

	<?php // ── 01 Hero ── dunkel ─────────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--warm hu-sst__hero" id="hero" data-nx-theme="dark" aria-labelledby="hu-sst-hero-title">
		<div class="hu-sst__container">
			<div class="hu-sst__hero-grid">
				<div class="hu-sst__hero-copy">
					<p class="hu-sst__eyebrow">Server-Side Tracking für aktive Kampagnen</p>
					<h1 class="hu-sst__h1" id="hu-sst-hero-title">
						Server-Side Tracking, wenn CRM und Werbekonten widersprechen
					</h1>
					<p class="hu-sst__lead">
						Ich prüfe Ihre Messstrecke und richte GA4, Google Ads und optional Meta CAPI über eine eigene Tracking-Subdomain ein — mit Paralleltest, Dokumentation und laufender Kontrolle.
					</p>
					<p class="hu-sst__lead-sub">
						Für Unternehmen mit laufenden Kampagnen, klaren Conversion-Zielen und einer Person, die auf Basis dieser Daten Budget steuert.
					</p>
					<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
					<div class="hu-sst__cta">
						<a class="hu-sst__btn hu-sst__btn--primary"
						   href="<?php echo esc_url( $form_anchor ); ?>"
						   data-track-action="cta_form_tracking_check"
						   data-track-category="server_side_tracking_b2b"
						   data-track-section="hero">
							<?php echo esc_html( $setup_cta_label ); ?>
						</a>
						<a class="hu-sst__btn hu-sst__btn--ghost"
						   href="#pakete"
						   data-track-action="cta_scope"
						   data-track-category="server_side_tracking_b2b"
						   data-track-section="hero">
							Pakete &amp; Preise ansehen
						</a>
					</div>
					<p class="hu-sst__cta-note">
						Solar-, Wärmepumpen- oder SHK-Betrieb und nicht nur ein Messproblem?
						<a href="<?php echo esc_url( $marktcheck_url ); ?>"
						   data-track-action="cta_marktcheck_branch"
						   data-track-category="server_side_tracking_b2b"
						   data-track-section="hero">Gesamten Anfrageweg im Marktcheck einordnen</a>
					</p>
				</div>

				<dl class="hu-sst__proof-strip" aria-label="Rahmen des Angebots">
					<div>
						<dt>Preis vorab</dt>
						<dd>Setup ab <?php echo esc_html( $standard_setup_price ); ?> netto</dd>
					</div>
					<div>
						<dt>Nachweis</dt>
						<dd>Paralleltest in Ihren Konten</dd>
					</div>
					<div>
						<dt>Eigentum</dt>
						<dd>Konten und Hosting bleiben bei Ihnen</dd>
					</div>
				</dl>

				<aside class="hu-sst__decision" aria-label="Ablauf der Vorprüfung">
					<p class="hu-sst__decision-kicker">Vor dem Angebot</p>
					<h2 class="hu-sst__decision-title">Was Sie zuerst erhalten</h2>
					<ol class="hu-sst__decision-list" role="list">
						<li>
							<span class="hu-sst__decision-num">01</span>
							<span><strong>Fit-Einschätzung</strong> — ist Server-Side Tracking jetzt die richtige Baustelle?</span>
						</li>
						<li>
							<span class="hu-sst__decision-num">02</span>
							<span><strong>Scope-Empfehlung</strong> — Standard, Pro oder individuelle Aufnahme.</span>
						</li>
						<li>
							<span class="hu-sst__decision-num">03</span>
							<span><strong>Offene Voraussetzungen</strong> — Konten, Consent, Events und technische Grenzen.</span>
						</li>
					</ol>
					<p class="hu-sst__decision-note">Für die erste Einordnung reichen Name, geschäftliche E-Mail und Ihr konkretes Messproblem. Website und technische Details helfen, sind aber optional. Noch keine Zugangsdaten.</p>
				</aside>
			</div>
		</div>
	</section>

	<?php // ── 02 Symptome ── hell ───────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--cream" id="symptome" data-nx-theme="light">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Ausgangslage</p>
				<h2 class="hu-sst__h2" id="hu-sst-symptome-title">Wenn Messsignale fehlen, wird Werbebudget nach einem verzerrten Bild verteilt</h2>
				<p class="hu-sst__section-lead">
					Diese drei Muster sind keine reine Reporting-Frage. Sie verändern, welche Kampagnen Budget bekommen und welche Signale die Plattformen zum Lernen erhalten.
				</p>
			</div>
			<div class="hu-sst__grid hu-sst__grid--3">
				<?php foreach ( $symptoms as $item ) : ?>
					<article class="hu-sst__card">
						<h3 class="hu-sst__card-title"><?php echo esc_html( $item['t'] ); ?></h3>
						<p class="hu-sst__card-text"><?php echo esc_html( $item['s'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php // ── 03 Unterschied ── dunkel ─────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--deep" id="unterschied" data-nx-theme="dark">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Der technische Unterschied</p>
				<h2 class="hu-sst__h2" id="hu-sst-unterschied-title">Wer die Daten sendet, entscheidet, wie viel davon ankommt</h2>
			</div>

			<div class="hu-sst__compare">
				<article class="hu-sst__compare-col">
					<h3 class="hu-sst__compare-title">Klassisches Tracking</h3>
					<ol class="hu-sst__chain" role="list">
						<li class="hu-sst__chain-node">Browser</li>
						<li class="hu-sst__chain-node">GA4, Google Ads, Meta</li>
					</ol>
					<p class="hu-sst__compare-text">
						Der Browser sendet direkt an die Plattformen. Ob ein Signal ankommt, hängt an Browsereinstellungen, Erweiterungen, Skript-Laufzeiten und daran, ob die Seite lange genug offen bleibt.
					</p>
				</article>

				<article class="hu-sst__compare-col hu-sst__compare-col--accent">
					<h3 class="hu-sst__compare-title">Server-Side Tracking</h3>
					<ol class="hu-sst__chain" role="list">
						<li class="hu-sst__chain-node">Browser</li>
						<li class="hu-sst__chain-node">Kontrollierter Server-Endpunkt</li>
						<li class="hu-sst__chain-node">GA4, Google Ads, Meta</li>
					</ol>
					<p class="hu-sst__compare-text">
						Relevante Signale gehen zunächst an einen kontrollierten Endpunkt, werden dort verarbeitet und gezielt weitergegeben. Sie legen fest, welche Felder welche Plattform erreichen — und können es dokumentieren.
					</p>
				</article>
			</div>

			<p class="hu-sst__note">
				In der Praxis ist meist ein <strong>hybrides Setup</strong> sinnvoll: browserseitige und serverseitige Messung laufen parallel und werden gegeneinander dedupliziert. Ein reiner Serverbetrieb ist selten die beste Lösung, weil einzelne Plattformfunktionen weiterhin browserseitige Signale erwarten.
			</p>
		</div>
	</section>

	<?php // ── 04 Fit / Non-Fit ── hell ─────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--white" id="fit" data-nx-theme="light">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Einordnung</p>
				<h2 class="hu-sst__h2" id="hu-sst-fit-title">Für wen sich das lohnt — und für wen nicht</h2>
				<p class="hu-sst__section-lead">
					Server-Side Tracking ist eine technische Leistung mit klaren Voraussetzungen. Fehlen sie, ist das Ergebnis Aufwand ohne Wirkung.
				</p>
			</div>

			<div class="hu-sst__split">
				<article class="hu-sst__split-col hu-sst__split-col--yes">
					<h3 class="hu-sst__split-title">
						<span class="hu-sst__split-badge" aria-hidden="true">✓</span>
						Geeignet, wenn
					</h3>
					<ul class="hu-sst__list" role="list">
						<?php foreach ( $fit_yes as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</article>

				<article class="hu-sst__split-col hu-sst__split-col--no">
					<h3 class="hu-sst__split-title">
						<span class="hu-sst__split-badge" aria-hidden="true">×</span>
						Nicht sinnvoll bei
					</h3>
					<ul class="hu-sst__list" role="list">
						<?php foreach ( $fit_no as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
					<p class="hu-sst__split-note">
						Wenn einer dieser Punkte auf Sie zutrifft, sagen wir das im Erstgespräch — und nicht nach der Rechnung.
					</p>
				</article>
			</div>
		</div>
	</section>

	<?php // ── 05 Architektur ── dunkel ─────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--warm" id="architektur" data-nx-theme="dark" aria-labelledby="hu-sst-architektur-title">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Architektur</p>
				<h2 class="hu-sst__h2" id="hu-sst-architektur-title">Der Datenfluss, den Sie am Ende besitzen</h2>
			</div>

			<figure class="hu-sst__flow">
				<ol class="hu-sst__flow-chain" role="list">
					<?php foreach ( $flow_chain as $index => $node ) : ?>
						<li class="hu-sst__flow-node">
							<span class="hu-sst__flow-step"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<span class="hu-sst__flow-label"><?php echo esc_html( $node['label'] ); ?></span>
							<span class="hu-sst__flow-note"><?php echo esc_html( $node['note'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ol>

				<p class="hu-sst__flow-divider"><span>weitergegeben an</span></p>

				<ul class="hu-sst__flow-outputs" role="list">
					<?php foreach ( $flow_outputs as $node ) : ?>
						<li class="hu-sst__flow-node hu-sst__flow-node--output<?php echo esc_attr( $node['optional'] ? ' is-optional' : '' ); ?>">
							<span class="hu-sst__flow-label"><?php echo esc_html( $node['label'] ); ?></span>
							<span class="hu-sst__flow-note"><?php echo esc_html( $node['note'] ); ?></span>
							<?php if ( $node['optional'] ) : ?>
								<span class="hu-sst__flow-tag">optional</span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<figcaption class="hu-sst__flow-caption">
					Datenfluss im Setup: Von der Website gehen Signale an den Web-GTM-Container, von dort über eine eigene Tracking-Subdomain an den Server-GTM-Container auf Stape EU. Der Server-Container gibt die Daten an GA4 und Google Ads weiter, optional zusätzlich an die Meta Conversion API und an ein CRM.
				</figcaption>
			</figure>
		</div>
	</section>

	<?php // ── 06 Leistungsumfang ── hell ───────────────────── ?>
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--cream" id="umfang" data-nx-theme="light">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Leistungsumfang</p>
				<h2 class="hu-sst__h2" id="hu-sst-umfang-title">Was in der Einrichtung tatsächlich entsteht</h2>
				<p class="hu-sst__section-lead">Sechs Ergebnisse statt einer langen Tool-Liste: von der Bestandsaufnahme bis zu einem Datenfluss, den Ihr Team nachvollziehen und weiterbetreiben kann.</p>
			</div>

			<div class="hu-sst__grid hu-sst__grid--2">
				<?php foreach ( $setup_items as $item ) : ?>
					<article class="hu-sst__item">
						<span class="hu-sst__item-mark" aria-hidden="true"><?php echo hu_sst_icon_svg( '<path d="M4 12.5l5 5L20 6.5"/>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup ?></span>
						<div class="hu-sst__item-body">
							<h3 class="hu-sst__item-title"><?php echo esc_html( $item['t'] ); ?></h3>
							<p class="hu-sst__item-text"><?php echo esc_html( $item['s'] ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<aside class="hu-sst__callout">
				<h3 class="hu-sst__callout-title">Der Paralleltest entscheidet — nicht eine pauschale Prozentzahl</h3>
				<p>
					Die bisherige und die neue Messung laufen zunächst nebeneinander. So werden fehlende, doppelte oder falsch zugeordnete Events in <strong>Ihren eigenen Konten</strong> sichtbar. Konten, Container und Hosting bleiben bei Ihnen; jede Änderung liegt als benannte GTM-Version vor.
				</p>
				<p class="hu-sst__callout-links">
					Noch in der Messkonzept-Phase? <a href="<?php echo esc_url( $ga4_setup_url ); ?>" data-track-action="internal_ga4_setup" data-track-category="internal_link" data-track-section="umfang">GA4 Tracking Setup als Grundlage</a>. Technische Vertiefung: <a href="<?php echo esc_url( $gtm_guide_url ); ?>" data-track-action="internal_sst_gtm" data-track-category="internal_link" data-track-section="umfang">Server-Side Tracking mit GTM</a>.
				</p>
			</aside>
		</div>
	</section>

	<?php // ── 07 Pakete ── dunkel ──────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--deep" id="pakete" data-nx-theme="dark" aria-labelledby="hu-sst-pakete-title">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Pakete</p>
				<h2 class="hu-sst__h2" id="hu-sst-pakete-title">Einrichtung und laufende Kontrolle — klar getrennt</h2>
				<p class="hu-sst__section-lead">
					Die einmalige Einrichtung baut die Messstrecke. Tracking Care kontrolliert sie danach regelmäßig. Alle Preise sind netto für Geschäftskunden; das Server-Hosting läuft separat über Ihr eigenes Konto.
				</p>
			</div>

			<div class="hu-sst__pricing">
				<?php foreach ( $packages as $package ) : ?>
					<article class="hu-sst__price-card<?php echo esc_attr( $package['featured'] ? ' hu-sst__price-card--featured' : '' ); ?>">
						<?php if ( $package['featured'] ) : ?>
							<p class="hu-sst__price-flag">Für Google Ads und Meta CAPI</p>
						<?php endif; ?>
						<h3 class="hu-sst__price-name"><?php echo esc_html( $package['name'] ); ?></h3>
						<p class="hu-sst__price-lead"><?php echo esc_html( $package['lead'] ); ?></p>
						<dl class="hu-sst__price-figures">
							<div>
								<dt>Einrichtung</dt>
								<dd><?php echo esc_html( $package['setup'] ); ?></dd>
							</div>
							<div>
								<dt>Betreuung</dt>
								<dd><?php echo esc_html( $package['monthly'] ); ?></dd>
							</div>
						</dl>
						<p class="hu-sst__price-terms"><?php echo esc_html( $package['terms'] ); ?></p>
						<ul class="hu-sst__price-list" role="list">
							<?php foreach ( $package['items'] as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
						<a class="hu-sst__btn hu-sst__btn--<?php echo esc_attr( $package['featured'] ? 'primary' : 'ghost' ); ?> hu-sst__btn--block"
						   href="<?php echo esc_url( $form_anchor ); ?>"
						   data-track-action="<?php echo esc_attr( $package['action'] ); ?>"
						   data-track-category="server_side_tracking_b2b"
						   data-track-section="pakete">
							<?php echo esc_html( $package['cta'] ); ?>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php // ── 08 Tracking Care ── hell ─────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--white" id="tracking-care" data-nx-theme="light">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Tracking Care</p>
				<h2 class="hu-sst__h2" id="hu-sst-care-title">Der Monatsbeitrag bezahlt nicht Hosting, sondern Verantwortung</h2>
				<p class="hu-sst__section-lead">
					Ein Tracking-Setup ist kein Möbelstück, das einmal aufgebaut wird und dann steht. Website-Updates, Plugin-Wechsel und Plattformänderungen greifen laufend hinein. Tracking Care ist die regelmäßige Kontrolle, die den Unterschied zwischen „läuft" und „lief mal" bemerkt.
				</p>
			</div>

			<div class="hu-sst__split">
				<article class="hu-sst__split-col hu-sst__split-col--yes">
					<h3 class="hu-sst__split-title">
						<span class="hu-sst__split-badge" aria-hidden="true">✓</span>
						Enthalten
					</h3>
					<ul class="hu-sst__list" role="list">
						<?php foreach ( $care_included as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</article>

				<article class="hu-sst__split-col hu-sst__split-col--no">
					<h3 class="hu-sst__split-title">
						<span class="hu-sst__split-badge" aria-hidden="true">×</span>
						Nicht enthalten
					</h3>
					<ul class="hu-sst__list" role="list">
						<?php foreach ( $care_excluded as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
					<p class="hu-sst__split-note">
						Diese Punkte sind nicht ausgeschlossen, sondern separat kalkuliert. Sie werden als eigener Auftrag angeboten, damit der Monatsbeitrag planbar bleibt.
					</p>
				</article>
			</div>
		</div>
	</section>

	<?php // ── 09 Ablauf ── dunkel ──────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--warm" id="ablauf" data-nx-theme="dark">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Ablauf</p>
				<h2 class="hu-sst__h2" id="hu-sst-ablauf-title">Vom unklaren Zahlenbild zum kontrollierten Datenfluss</h2>
				<p class="hu-sst__section-lead">Vier Entscheidungspunkte, damit Technik erst gebaut wird, wenn Ziel, Scope und Zuständigkeiten geklärt sind.</p>
			</div>

			<ol class="hu-sst__steps" role="list">
				<?php foreach ( $process_steps as $index => $step ) : ?>
					<li class="hu-sst__step">
						<span class="hu-sst__step-num"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="hu-sst__step-body">
							<h3 class="hu-sst__step-title"><?php echo esc_html( $step['t'] ); ?></h3>
							<p class="hu-sst__step-text"><?php echo esc_html( $step['s'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<?php // ── 10 Sicherheit ── hell ────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--cream" id="sicherheit" data-nx-theme="light">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Sicherheit und Eigentum</p>
				<h2 class="hu-sst__h2" id="hu-sst-sicherheit-title">Ihre Datenebene bleibt Ihre Datenebene</h2>
			</div>

			<ul class="hu-sst__checklist" role="list">
				<?php foreach ( $security_items as $item ) : ?>
					<li>
						<span class="hu-sst__item-mark" aria-hidden="true"><?php echo hu_sst_icon_svg( '<path d="M12 3l7 3v5.5c0 4.2-2.9 7.6-7 8.5-4.1-.9-7-4.3-7-8.5V6l7-3z"/>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup ?></span>
						<span><?php echo esc_html( $item ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

			<p class="hu-sst__note hu-sst__note--light">
				Diese Seite trifft keine Aussage zur rechtlichen Bewertung Ihres Setups und ersetzt keine Rechtsberatung. Was hier beschrieben wird, sind technische Maßnahmen und Zuständigkeiten.
			</p>
		</div>
	</section>

	<?php // ── 11 FAQ ── hell ───────────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--white" id="faq" data-nx-theme="light" aria-labelledby="hu-sst-faq-title">
		<div class="hu-sst__container hu-sst__container--narrow">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Häufige Fragen</p>
				<h2 class="hu-sst__h2" id="hu-sst-faq-title">Technik, Consent, Kosten und Eigentum</h2>
			</div>

			<div class="hu-sst__faq-list">
				<?php foreach ( $faq as $item ) : ?>
					<details class="hu-sst__faq-item" name="hu-faq-server-side">
						<summary class="hu-sst__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<div class="hu-sst__faq-a">
							<p><?php echo esc_html( $item['answer'] ); ?></p>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php // ── 12 Formular ── dunkel ────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--warm hu-sst__final" id="anfrage" data-nx-theme="dark" aria-labelledby="hu-sst-form-title">
		<div class="hu-sst__container hu-sst__container--narrow">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Anfrage</p>
				<h2 class="hu-sst__h2" id="hu-sst-form-title">Tracking-Setup einordnen lassen</h2>
				<p class="hu-sst__section-lead">
					Beschreiben Sie kurz, welche Zahlen nicht zusammenpassen oder was künftig sauber gemessen werden soll. Sie erhalten eine Fit-Einschätzung, den passenden Scope und die offenen Voraussetzungen — vor einem Angebot.
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
</main>

<?php
get_template_part(
	'template-parts/seo-subpage-sticky-cta',
	null,
	[
		'cta_url'           => $form_anchor,
		'track_category'    => 'server_side_tracking_b2b',
		'region_label'      => 'Schnellzugang zur Anfrage',
		'lead'              => 'Setup einordnen lassen',
		'sub'               => 'Fit und Scope vor Angebot',
		'label'             => $setup_cta_label,
		'track_action'      => 'cta_sticky_form_tracking',
		'hide_when_visible' => '#anfrage',
	]
);

get_footer();
