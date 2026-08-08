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
$solar_money_url = function_exists( 'nexus_get_energy_systems_url' )
	? nexus_get_energy_systems_url()
	: home_url( '/solar-waermepumpen-leadgenerierung/' );
$marktcheck_url  = trailingslashit( $solar_money_url ) . '#marktcheck';
$privacy_url     = function_exists( 'nexus_get_page_url' )
	? nexus_get_page_url( [ 'datenschutz' ], home_url( '/datenschutz/' ) )
	: home_url( '/datenschutz/' );
$form_anchor     = '#anfrage';
$rest_endpoint   = rest_url( 'nexus/v1/contact-request' );

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
		's' => 'Im CRM stehen 40 Anfragen, Google Ads meldet 61 Conversions, Meta noch einmal 25. Welche Zahl in die Budgetentscheidung geht, ist reine Auslegungssache.',
	],
	[
		't' => 'Conversions fehlen oder werden doppelt gezählt',
		's' => 'Dieselbe Anfrage taucht als Formular-Absenden, Danke-Seiten-Aufruf und Klick-Conversion auf. Oder sie taucht gar nicht auf, weil der Browser den Request abgebrochen hat.',
	],
	[
		't' => 'Kampagnen optimieren auf unvollständige Signale',
		's' => 'Smart Bidding lernt aus dem, was ankommt. Fehlen Signale systematisch bei bestimmten Browsern oder Endgeräten, verschiebt sich die Aussteuerung dorthin, wo gemessen wird — nicht dorthin, wo verkauft wird.',
	],
	[
		't' => 'Consent-, Formular- oder Website-Änderungen beschädigen das Tracking',
		's' => 'Ein Plugin-Update, ein neues Formular-Layout, eine geänderte Consent-Kategorie — und ein Event feuert nicht mehr. Auffallen tut es meist erst Wochen später im Monatsreport.',
	],
	[
		't' => 'Unklar, welche Daten an welche Plattform gehen',
		's' => 'Über die Jahre sind Tags, Pixel und Plugins dazugekommen. Wer heute belegen soll, welches System welche Felder erhält, findet keine Dokumentation, sondern einen Container mit 60 gewachsenen Tags.',
	],
	[
		't' => 'GA4, Google Ads und Meta widersprechen sich',
		's' => 'Drei Systeme, drei Attributionsmodelle, drei Zeitfenster. Ohne saubere Event-Definition und Deduplizierung ist der Vergleich zwischen ihnen wertlos.',
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
		't' => 'Analyse des bestehenden Setups',
		's' => 'Welche Container, Tags, Pixel und Plugins laufen heute, welche Events feuern doppelt, welche gar nicht.',
	],
	[
		't' => 'Server-GTM-Container',
		's' => 'Eigener serverseitiger Container, angelegt im Google-Konto des Kunden.',
	],
	[
		't' => 'Stape-EU-Hosting im Kundenkonto',
		's' => 'Der Hosting-Vertrag läuft auf den Kunden. Die Kosten dafür laufen separat und direkt über dieses Konto.',
	],
	[
		't' => 'Eigene Tracking-Subdomain',
		's' => 'Einrichtung und DNS-Anbindung einer Subdomain der Kundendomain als Messendpunkt.',
	],
	[
		't' => 'GA4-Anbindung',
		's' => 'Serverseitige Anbindung der bestehenden GA4-Property mit abgestimmter Event-Struktur.',
	],
	[
		't' => 'Google-Ads-Conversion-Tracking',
		's' => 'Conversion-Aktionen sauber definiert, zugeordnet und gegen doppelte Zählung geprüft.',
	],
	[
		't' => 'Enhanced Conversions, soweit technisch möglich',
		's' => 'Abhängig von Formularaufbau, verfügbaren Feldern und Consent-Situation. Wo es nicht sinnvoll umsetzbar ist, sagen wir das vorher.',
	],
	[
		't' => 'Consent-Tool-Anbindung',
		's' => 'Verbindung des vorhandenen Consent-Systems mit Web- und Server-Container, damit Consent-Signale im Datenfluss ankommen.',
	],
	[
		't' => 'Definierte Events und Conversions',
		's' => 'Schriftlich festgelegt, welches Event was bedeutet, wann es feuert und welche Felder es transportiert.',
	],
	[
		't' => 'Prüfung auf fehlende oder doppelte Events',
		's' => 'Abgleich zwischen Website, Containern, GA4 und Werbekonten vor dem Livegang.',
	],
	[
		't' => 'Testbetrieb',
		's' => 'Paralleler Betrieb und Kontrolle, bevor die alte Messung abgelöst wird.',
	],
	[
		't' => 'GTM-Versionierung',
		's' => 'Jede Änderung als benannte Container-Version, damit nachvollziehbar bleibt, wann was geändert wurde.',
	],
	[
		't' => 'Technische Dokumentation',
		's' => 'Datenfluss, Event-Definitionen, Endpunkte und offene Punkte in einem Dokument, das auch ein Nachfolger lesen kann.',
	],
	[
		't' => 'Übergabe aller Zugänge',
		's' => 'Konten, Container und Hosting bleiben beim Kunden. Die Zugänge werden vollständig übergeben.',
	],
];

// ── 7) Pakete ─────────────────────────────────────────────────
$packages = [
	[
		'key'      => 'standard',
		'name'     => 'Tracking Care Standard',
		'setup'    => '890 €',
		'monthly'  => '99 € / Monat',
		'terms'    => 'Nettopreise, monatlich kündbar, Hosting separat',
		'lead'     => 'Für einen sauberen Erstaufbau auf einer Website mit klaren Haupt-Conversions.',
		'featured' => false,
		'items'    => [
			'Eine Website oder Domain',
			'WordPress oder vergleichbarer Lead-Funnel',
			'Server-GTM über Stape EU',
			'Eigene Tracking-Subdomain',
			'GA4 und Google Ads angebunden',
			'Bis zu drei Haupt-Conversions',
			'Consent-Anbindung',
			'Enhanced Conversions, soweit möglich',
			'Qualitätssicherung vor Livegang',
			'Dokumentation und Übergabe',
			'Monatlicher Funktionstest',
			'Kontrolle der wichtigsten Conversion-Signale',
			'Prüfung auf doppelte oder fehlende Events',
			'Kurze Statusmeldung',
			'Bis zu 30 Minuten kleinere Korrekturen pro Monat',
			'Supportantwort innerhalb von zwei Werktagen',
		],
		'cta'      => 'Standard-Setup prüfen lassen',
		'action'   => 'cta_package_standard',
	],
	[
		'key'      => 'pro',
		'name'     => 'Tracking Care Pro',
		'setup'    => '1.290 €',
		'monthly'  => '149 € / Monat',
		'terms'    => 'Nettopreise, monatlich kündbar, Hosting separat',
		'lead'     => 'Für Setups mit Meta-Ads, mehreren Formularen und mehr als einer Conversion-Strecke.',
		'featured' => true,
		'items'    => [
			'Alles aus Tracking Care Standard',
			'Meta Pixel und Meta Conversion API',
			'Event-Deduplizierung zwischen Pixel und CAPI',
			'Bis zu acht definierte Events',
			'Mehrere Formulare oder Conversion-Strecken',
			'Ausführlichere Datenkontrolle',
			'Bis zu 60 Minuten kleinere Anpassungen pro Monat',
			'Priorisierte Bearbeitung',
		],
		'cta'      => 'Pro-Setup anfragen',
		'action'   => 'cta_package_pro',
	],
	[
		'key'      => 'individuell',
		'name'     => 'Individuelles Tracking-Setup',
		'setup'    => 'ab 2.500 €',
		'monthly'  => 'ab 199 € / Monat',
		'terms'    => 'Nettopreise, Umfang nach Aufnahme, Hosting separat',
		'lead'     => 'Wenn Systeme, Märkte oder Datenpipelines dazukommen, die über ein Standard-Setup hinausgehen.',
		'featured' => false,
		'items'    => [
			'CRM-Anbindung',
			'Offline-Conversions',
			'Leadstatus und Lead-Scoring',
			'WooCommerce oder Shopify',
			'Mehrere Domains oder Märkte',
			'Microsoft Ads, LinkedIn Ads, TikTok',
			'Eigene Infrastruktur auf Hetzner oder Google Cloud',
			'Individuelle Datenpipelines',
		],
		'cta'      => 'Individuelles Setup besprechen',
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
		't' => 'Bestehendes Tracking prüfen',
		's' => 'Aufnahme des Ist-Zustands: Container, Tags, Events, Consent-Lösung, bekannte Abweichungen.',
	],
	[
		't' => 'Plattformen und Conversion-Ziele definieren',
		's' => 'Welche Systeme angebunden werden und was künftig als Conversion gilt — schriftlich, bevor gebaut wird.',
	],
	[
		't' => 'Server-GTM, Hosting und Subdomain einrichten',
		's' => 'Container anlegen, Stape-EU-Hosting im Kundenkonto aufsetzen, Subdomain verbinden.',
	],
	[
		't' => 'Tags und Consent-Signale verbinden',
		's' => 'Web-Container, Server-Container und Consent-Tool so verbinden, dass der Consent-Status im Datenfluss ankommt.',
	],
	[
		't' => 'Events testen und parallel prüfen',
		's' => 'Testbetrieb neben der bisherigen Messung, Abgleich der Zahlen, Korrektur der Abweichungen.',
	],
	[
		't' => 'Dokumentation und Übergabe',
		's' => 'Datenfluss und Event-Definitionen dokumentiert, Zugänge übergeben.',
	],
	[
		't' => 'Laufende Qualitätskontrolle',
		's' => 'Ab hier greift Tracking Care: regelmäßige Kontrolle statt Zufallsfund im Quartalsreport.',
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

// ── 11) Erfahrungswert ────────────────────────────────────────
// ACHTUNG, kein belegter Kanon: Diese Spanne ist die Eigenaussage des
// Betreibers aus bisherigen Umstellungen, nicht aus einer Belegdatei und
// nicht aus inc/canon/. Sie gilt nur auf dieser Seite und darf nicht auf
// andere Seiten uebernommen oder als Referenzwert zitiert werden. Bewusst
// "gemessene Conversions", nicht "Conversion Rate": serverseitiges Tracking
// erhoeht die Erfassung, nicht die Kaufbereitschaft.
$measured_uplift = '20 bis 40 Prozent';

// ── 12) FAQ ───────────────────────────────────────────────────
$faq = [
	[
		'question' => 'Was ist Server-Side Tracking?',
		'answer'   => 'Beim Server-Side Tracking laufen Messsignale nicht mehr direkt vom Browser an die Werbe- und Analyseplattformen, sondern zunächst an einen kontrollierten Server-Endpunkt. Dort werden sie verarbeitet und gezielt weitergegeben. Der Vorteil liegt in der Kontrolle: Sie legen fest, welche Daten den Endpunkt in welche Richtung verlassen, und können das dokumentieren.',
	],
	[
		'question' => 'Was ist der Unterschied zwischen Client-Side und Server-Side Tracking?',
		'answer'   => 'Client-Side bedeutet: der Browser des Besuchers sendet die Daten selbst an GA4, Google Ads oder Meta. Das ist einfach einzurichten, hängt aber von Browsereinstellungen, Erweiterungen und Skript-Laufzeiten ab. Server-Side bedeutet: der Browser sendet an einen eigenen Endpunkt, dieser verteilt weiter. In der Praxis ist meist ein hybrides Setup sinnvoll — beide Wege parallel, mit Deduplizierung, damit nichts doppelt gezählt wird.',
	],
	[
		'question' => 'Brauche ich weiterhin ein Consent-Banner?',
		'answer'   => 'Server-Side Tracking ersetzt kein Consent-Management. Die Einwilligungspflicht richtet sich nach dem, was tatsächlich passiert, nicht danach, wo der Code läuft. Das Setup wird so gebaut, dass Consent-Signale aus Ihrem bestehenden Consent-Tool im Datenfluss ankommen und dort berücksichtigt werden. Die rechtliche Bewertung Ihres konkreten Setups gehört zu Ihrer Rechtsberatung, nicht in dieses Angebot.',
	],
	[
		'question' => 'Funktioniert das mit WordPress?',
		'answer'   => 'Ja. WordPress ist der Regelfall in diesen Projekten. Entscheidend ist weniger das CMS als der Aufbau der Formulare und Conversion-Strecken: welche Events auslösbar sind, welche Felder zur Verfügung stehen und wie das Consent-Tool eingebunden ist. Das wird in der Analyse vorab geprüft.',
	],
	[
		'question' => 'Welche Plattformen können angebunden werden?',
		'answer'   => 'Standardmäßig GA4 und Google Ads, im Pro-Setup zusätzlich Meta Pixel und Meta Conversion API mit Deduplizierung. Darüber hinaus sind Microsoft Ads, LinkedIn Ads, TikTok sowie CRM-Systeme mit REST-API oder Webhook möglich — das läuft über das individuelle Setup.',
	],
	[
		'question' => 'Wie viele Conversions kommen zusätzlich an?',
		'answer'   => sprintf( 'In den Setups, die ich bisher umgestellt habe, kamen anschließend rund %s mehr Conversions in den Werbekonten an als vorher. Das ist ein Erfahrungswert, keine Zusage: Wie viel es im Einzelfall wird, hängt vom Anteil an Safari- und iOS-Traffic ab, vom Anteil der Werbeblocker, von der Consent-Rate und davon, wie sauber vorher gemessen wurde. Wichtig zur Einordnung: Es kaufen dadurch nicht mehr Menschen — es werden mehr der ohnehin stattfindenden Abschlüsse erfasst und richtig zugeordnet. Im Testbetrieb läuft die neue Messung neben der alten, damit Sie die Differenz an Ihren eigenen Zahlen sehen.', $measured_uplift ),
	],
	[
		'question' => 'Was kostet Server-Side Tracking?',
		'answer'   => 'Das Standard-Setup liegt bei 890 € netto einmalig plus 99 € netto pro Monat für die laufende Betreuung. Das Pro-Setup mit Meta CAPI und Event-Deduplizierung liegt bei 1.290 € netto einmalig plus 149 € netto pro Monat. Individuelle Setups beginnen bei 2.500 € netto einmalig, die Betreuung bei 199 € netto pro Monat. Alle Preise sind Nettopreise für Geschäftskunden.',
	],
	[
		'question' => 'Was kostet das Hosting?',
		'answer'   => 'Das Hosting des Server-Containers läuft über Stape und wird direkt über Ihr eigenes Konto abgerechnet — es ist in den genannten Preisen nicht enthalten. Der Grund dafür ist Eigentum: läuft das Hosting über unser Konto, hängt Ihre Datenebene an unserem Vertrag. Die aktuellen Tarife entnehmen Sie Stape direkt.',
	],
	[
		'question' => 'Wie lange dauert die Einrichtung?',
		'answer'   => 'Ein Standard-Setup ist in der Regel innerhalb von zwei bis drei Wochen produktiv, gerechnet ab Bereitstellung der Zugänge. Der größte Zeitfaktor ist selten die Technik, sondern die Abstimmung darüber, was künftig als Conversion gilt, und die Wartezeit auf DNS- und Kontofreigaben.',
	],
	[
		'question' => 'Was passiert nach Website- oder Plugin-Updates?',
		'answer'   => 'Updates können Formulare, Selektoren oder Consent-Kategorien verändern und damit Events beschädigen. Genau dafür gibt es die monatliche Kontrolle: der Funktionstest prüft die Haupt-Conversions, und Auffälligkeiten werden gemeldet, statt bis zum nächsten Report zu warten. Kleinere Korrekturen sind im vereinbarten Zeitrahmen enthalten.',
	],
	[
		'question' => 'Gehören mir Container, Konten und Zugänge?',
		'answer'   => 'Ja. Google-Konten, GTM-Container, GA4-Property, Werbekonten und das Stape-Hosting laufen auf Ihren Namen und in Ihren Konten. Am Ende der Einrichtung werden alle Zugänge übergeben. Bei einer Trennung bleibt die Datenebene bei Ihnen.',
	],
	[
		'question' => 'Ist Server-Side Tracking vollständig ausfallsicher?',
		'answer'   => 'Nein, und niemand sollte das behaupten. Ein Server-Endpunkt kann ausfallen, ein Plattform-API kann sich ändern, ein Website-Update kann ein Event brechen, und Consent-Ablehnung bleibt Consent-Ablehnung. Server-Side Tracking reduziert typische Datenverluste und macht den Datenfluss kontrollierbarer — es ersetzt keine laufende Kontrolle, sondern setzt sie voraus.',
	],
	[
		'question' => 'Wann lohnt es sich nicht?',
		'answer'   => 'Bei sehr wenig Traffic, ohne laufende Kampagnen oder ohne klar definierte Conversion-Ziele. Dann steht dem Aufwand kein Nutzen gegenüber, weil die Datenmenge zu klein ist, um Entscheidungen zu stützen. Auch wenn niemand im Betrieb auf die Zahlen reagiert, ist eine bessere Messung die falsche erste Baustelle.',
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
			<p class="hu-sst__eyebrow">Server-Side Tracking</p>
			<h1 class="hu-sst__h1" id="hu-sst-hero-title">
				Server-Side Tracking für belastbare Conversion-Daten
			</h1>
			<p class="hu-sst__lead">
				GA4, Google Ads und Meta CAPI über eine eigene Tracking-Domain – professionell eingerichtet, getestet und laufend kontrolliert.
			</p>
			<p class="hu-sst__lead-sub">
				Für Unternehmen mit aktiven Kampagnen, WordPress-Websites, Lead-Funnels und messbaren Conversion-Zielen.
			</p>
			<?php get_template_part( 'template-parts/seo-subpage-byline', null, [ 'template_path' => __FILE__ ] ); ?>
			<div class="hu-sst__cta">
				<a class="hu-sst__btn hu-sst__btn--primary"
				   href="<?php echo esc_url( $form_anchor ); ?>"
				   data-track-action="cta_form_tracking_check"
				   data-track-category="server_side_tracking_b2b"
				   data-track-section="hero">
					Tracking-Setup prüfen lassen
				</a>
				<a class="hu-sst__btn hu-sst__btn--ghost"
				   href="#umfang"
				   data-track-action="cta_scope"
				   data-track-category="server_side_tracking_b2b"
				   data-track-section="hero">
					Leistungsumfang ansehen
				</a>
			</div>
			<p class="hu-sst__cta-note">
				Solar-, Wärmepumpen- oder SHK-Betrieb?
				<a href="<?php echo esc_url( $marktcheck_url ); ?>"
				   data-track-action="cta_marktcheck_branch"
				   data-track-category="server_side_tracking_b2b"
				   data-track-section="hero">Zum Marktcheck mit Fit-Entscheid</a>
			</p>
		</div>
	</section>

	<?php // ── 02 Symptome ── hell ───────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--cream" id="symptome" data-nx-theme="light" aria-labelledby="hu-sst-symptome-title">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Ausgangslage</p>
				<h2 class="hu-sst__h2" id="hu-sst-symptome-title">Die Zahlen widersprechen sich — und trotzdem wird auf sie hin entschieden</h2>
				<p class="hu-sst__section-lead">
					Diese sechs Muster kommen in Messgesprächen fast immer vor. Wer drei davon kennt, hat kein Reporting-Problem, sondern ein Datenerfassungsproblem.
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
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--deep" id="unterschied" data-nx-theme="dark" aria-labelledby="hu-sst-unterschied-title">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Der technische Unterschied</p>
				<h2 class="hu-sst__h2" id="hu-sst-unterschied-title">Wer die Daten sendet, entscheidet, wie viel davon ankommt</h2>
			</div>

			<div class="hu-sst__compare">
				<article class="hu-sst__compare-col">
					<h3 class="hu-sst__compare-title">Klassisches Tracking</h3>
					<ol class="hu-sst__chain">
						<li class="hu-sst__chain-node">Browser</li>
						<li class="hu-sst__chain-node">GA4, Google Ads, Meta</li>
					</ol>
					<p class="hu-sst__compare-text">
						Der Browser sendet direkt an die Plattformen. Ob ein Signal ankommt, hängt an Browsereinstellungen, Erweiterungen, Skript-Laufzeiten und daran, ob die Seite lange genug offen bleibt.
					</p>
				</article>

				<article class="hu-sst__compare-col hu-sst__compare-col--accent">
					<h3 class="hu-sst__compare-title">Server-Side Tracking</h3>
					<ol class="hu-sst__chain">
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
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--white" id="fit" data-nx-theme="light" aria-labelledby="hu-sst-fit-title">
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
					<ul class="hu-sst__list">
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
					<ul class="hu-sst__list">
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
				<ol class="hu-sst__flow-chain">
					<?php foreach ( $flow_chain as $index => $node ) : ?>
						<li class="hu-sst__flow-node">
							<span class="hu-sst__flow-step"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<span class="hu-sst__flow-label"><?php echo esc_html( $node['label'] ); ?></span>
							<span class="hu-sst__flow-note"><?php echo esc_html( $node['note'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ol>

				<p class="hu-sst__flow-divider"><span>weitergegeben an</span></p>

				<ul class="hu-sst__flow-outputs">
					<?php foreach ( $flow_outputs as $node ) : ?>
						<li class="hu-sst__flow-node hu-sst__flow-node--output<?php echo $node['optional'] ? ' is-optional' : ''; ?>">
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
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--cream" id="umfang" data-nx-theme="light" aria-labelledby="hu-sst-umfang-title">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Leistungsumfang</p>
				<h2 class="hu-sst__h2" id="hu-sst-umfang-title">Was bei der Einrichtung tatsächlich passiert</h2>
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
				<h3 class="hu-sst__callout-title">Eigentum und Kosten</h3>
				<p>
					<strong>Konten und Zugänge gehören dem Kunden.</strong> Google-Konten, GTM-Container, GA4-Property, Werbekonten und das Stape-Hosting laufen auf Ihren Namen. <strong>Die Hostingkosten laufen separat über Ihr eigenes Kundenkonto</strong> und sind in den Paketpreisen nicht enthalten.
				</p>
			</aside>
		</div>
	</section>

	<?php // ── 07 Pakete ── dunkel ──────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--deep" id="pakete" data-nx-theme="dark" aria-labelledby="hu-sst-pakete-title">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Pakete</p>
				<h2 class="hu-sst__h2" id="hu-sst-pakete-title">Einrichtung und Betreuung, mit Preis vorab</h2>
				<p class="hu-sst__section-lead">
					Alle Preise sind Nettopreise für Geschäftskunden. Das Hosting des Server-Containers läuft separat über Ihr eigenes Konto.
				</p>
			</div>

			<div class="hu-sst__pricing">
				<?php foreach ( $packages as $package ) : ?>
					<article class="hu-sst__price-card<?php echo $package['featured'] ? ' hu-sst__price-card--featured' : ''; ?>">
						<?php if ( $package['featured'] ) : ?>
							<p class="hu-sst__price-flag">Der häufigste Zuschnitt</p>
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
						<ul class="hu-sst__price-list">
							<?php foreach ( $package['items'] as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
						<a class="hu-sst__btn hu-sst__btn--<?php echo $package['featured'] ? 'primary' : 'ghost'; ?> hu-sst__btn--block"
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
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--white" id="tracking-care" data-nx-theme="light" aria-labelledby="hu-sst-care-title">
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
					<ul class="hu-sst__list">
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
					<ul class="hu-sst__list">
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
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--warm" id="ablauf" data-nx-theme="dark" aria-labelledby="hu-sst-ablauf-title">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Ablauf</p>
				<h2 class="hu-sst__h2" id="hu-sst-ablauf-title">Sieben Schritte von der Aufnahme bis zur laufenden Kontrolle</h2>
			</div>

			<ol class="hu-sst__steps">
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
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--cream" id="sicherheit" data-nx-theme="light" aria-labelledby="hu-sst-sicherheit-title">
		<div class="hu-sst__container">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Sicherheit und Eigentum</p>
				<h2 class="hu-sst__h2" id="hu-sst-sicherheit-title">Ihre Datenebene bleibt Ihre Datenebene</h2>
			</div>

			<ul class="hu-sst__checklist">
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

	<?php // ── 11 Erfahrungswerte ── dunkel ─────────────────── ?>
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--deep" id="nachweis" data-nx-theme="dark" aria-labelledby="hu-sst-nachweis-title">
		<div class="hu-sst__container hu-sst__container--narrow">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Einordnung</p>
				<h2 class="hu-sst__h2" id="hu-sst-nachweis-title">Erfahrungswerte statt Logos</h2>
			</div>

			<div class="hu-sst__prose">
				<p>
					Ein großer Teil meiner Tracking-Arbeit läuft unter fremdem Namen, als technische Ebene hinter Agenturprojekten. Deshalb stehen hier keine Logos.
				</p>
				<p>
					Aus den Setups, die ich bisher umgestellt habe: In den Werbekonten kommen anschließend rund <strong><?php echo esc_html( $measured_uplift ); ?> mehr Conversions</strong> an als vorher, und die Zuordnung zu Kampagne und Anzeigengruppe wird stabiler.
				</p>
				<p>
					Das ist meine Erfahrung, keine Zusage. Wie viel es bei Ihnen wird, hängt an Ihrem Traffic: Anteil Safari und iOS, Werbeblocker, Consent-Rate und Zustand des bisherigen Setups. War vorher sauber gemessen, bleibt weniger übrig. War es kaputt, mehr.
				</p>
				<p>
					Nachprüfbar ist es trotzdem. Im Testbetrieb läuft die neue Messung neben der alten — die Differenz sehen Sie in Ihren eigenen Konten, nicht in meiner Präsentation. Container, Hosting und Zugänge laufen auf Ihren Namen, jede Änderung liegt als benannte GTM-Version vor.
				</p>
			</div>
		</div>
	</section>

	<?php // ── 12 FAQ ── hell ───────────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--light hu-sst__band--white" id="faq" data-nx-theme="light" aria-labelledby="hu-sst-faq-title">
		<div class="hu-sst__container hu-sst__container--narrow">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Häufige Fragen</p>
				<h2 class="hu-sst__h2" id="hu-sst-faq-title">Technik, Consent, Kosten und Eigentum</h2>
			</div>

			<div class="hu-sst__faq-list">
				<?php foreach ( $faq as $item ) : ?>
					<details class="hu-sst__faq-item">
						<summary class="hu-sst__faq-q"><?php echo esc_html( $item['question'] ); ?></summary>
						<div class="hu-sst__faq-a">
							<p><?php echo esc_html( $item['answer'] ); ?></p>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php // ── 13 Formular ── dunkel ────────────────────────── ?>
	<section class="hu-sst__band hu-sst__band--dark hu-sst__band--warm hu-sst__final" id="anfrage" data-nx-theme="dark" aria-labelledby="hu-sst-form-title">
		<div class="hu-sst__container hu-sst__container--narrow">
			<div class="hu-sst__section-head">
				<p class="hu-sst__eyebrow">Anfrage</p>
				<h2 class="hu-sst__h2" id="hu-sst-form-title">Tracking-Setup prüfen lassen</h2>
				<p class="hu-sst__section-lead">
					Senden Sie mir Ihre Website und eine kurze Beschreibung Ihres aktuellen Setups. Sie erhalten eine ehrliche Einschätzung, ob Server-Side Tracking in Ihrem Fall sinnvoll ist und welches Paket technisch passt.
				</p>
			</div>

			<div class="contact-error-summary is-hidden" role="alert" aria-live="assertive" data-contact-error-summary>
				<p class="contact-error-summary__title">Bitte prüfen Sie folgende Felder:</p>
				<ul class="contact-error-summary__list" data-contact-error-list></ul>
			</div>

			<form
				class="contact-form hu-sst__form"
				data-contact-form
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
				<div class="hu-sst__form-fixed">
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

					<div class="contact-field">
						<label for="contact-company">Unternehmen <span class="hu-sst__optional">optional</span></label>
						<input id="contact-company" name="company" type="text" autocomplete="organization" maxlength="120">
					</div>
				</div>

				<div class="contact-form__row">
					<div class="contact-field" data-contact-field="email">
						<label for="contact-email">Geschäftliche E-Mail</label>
						<input id="contact-email" name="email" type="email" autocomplete="email" required aria-describedby="contact-email-error">
						<p class="contact-field__error is-hidden" id="contact-email-error" aria-live="polite"></p>
					</div>

					<div class="contact-field">
						<label for="contact-website">Website <span class="hu-sst__optional">optional</span></label>
						<input id="contact-website" name="website_url" type="url" autocomplete="url" inputmode="url" placeholder="https://example.de">
					</div>
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

				<div class="contact-field" data-contact-field="message">
					<label for="contact-message">Konkretes Problem oder Ziel</label>
					<p id="contact-message-help" class="contact-field__help">Woran Sie merken, dass die Zahlen nicht stimmen — oder was Sie künftig sauber messen wollen.</p>
					<textarea
						id="contact-message"
						name="message"
						rows="5"
						required
						minlength="24"
						aria-describedby="contact-message-help contact-message-error"
						data-contact-message
					></textarea>
					<p class="contact-field__error is-hidden" id="contact-message-error" aria-live="polite"></p>
				</div>

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
					<button class="contact-submit" type="submit" data-contact-submit data-track-action="contact_submit" data-track-category="server_side_tracking_b2b" data-track-section="sst_form">
						Tracking-Setup prüfen lassen
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
		'cta_url'        => $form_anchor,
		'track_category' => 'server_side_tracking_b2b',
		'region_label'   => 'Schnellzugang zur Anfrage',
		'lead'           => 'Tracking-Setup prüfen',
		'sub'            => 'Einschätzung vor Angebot',
		'label'          => 'Setup schildern',
		'track_action'   => 'cta_sticky_form_tracking',
	]
);

get_footer();
