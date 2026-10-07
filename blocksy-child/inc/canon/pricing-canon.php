<?php
/**
 * Canonical pricing for energy enquiry systems, websites and tracking.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HU_FOUNDATION_PRICE_STANDARD', 9999 );
define( 'HU_FOUNDATION_EXTRA_PRODUCT_PRICE', 1000 );
define( 'HU_MARKETCHECK_PRICE', 99 );
define( 'HU_FOUNDATION_HOSTING_MONTHLY', 50 );
define( 'HU_FOUNDATION_DURATION_WEEKS_MIN', 8 );
define( 'HU_FOUNDATION_DURATION_WEEKS_MAX', 10 );

/**
 * Return the canonical pricing model.
 *
 * @return array<string, int|string>
 */
function hu_pricing_canon() {
	return [
		'foundation_price_standard'       => HU_FOUNDATION_PRICE_STANDARD,
		'foundation_extra_product_price'  => HU_FOUNDATION_EXTRA_PRODUCT_PRICE,
		'marketcheck_price'               => HU_MARKETCHECK_PRICE,
		'foundation_hosting_monthly'      => HU_FOUNDATION_HOSTING_MONTHLY,
		'foundation_duration_weeks_min'   => HU_FOUNDATION_DURATION_WEEKS_MIN,
		'foundation_duration_weeks_max'   => HU_FOUNDATION_DURATION_WEEKS_MAX,
		'entry_setup_price'               => HU_ENTRY_SETUP_PRICE,
		'entry_setup_business_days'       => HU_ENTRY_SETUP_BUSINESS_DAYS,
		'analysis_price'                  => HU_ANALYSIS_PRICE,
		'freelancer_takeover_check_price' => HU_FREELANCER_TAKEOVER_CHECK_PRICE,
		// Schlüssel enthält "price", damit [hu_price] ihn als Betrag formatiert.
		'freelancer_website_price'        => HU_FREELANCER_WEBSITE_MIN,
		'freelancer_extra_page_price'     => HU_FREELANCER_WEBSITE_EXTRA_PAGE,
		'freelancer_website_pages'        => HU_FREELANCER_WEBSITE_PAGES,
		'website_design_first_price'     => HU_WEBSITE_DESIGN_FIRST,
		'website_design_extra_price'     => HU_WEBSITE_DESIGN_EXTRA,
		'website_crm_standard_price'     => HU_WEBSITE_CRM_STANDARD,
		'landingpage_price'               => HU_LANDINGPAGE_PRICE,
		// Als Satzbaustein, nicht als Stufen-Array: [hu_price] gibt nur Skalare aus.
		'freelancer_retainer_display'     => hu_freelancer_retainer_display(),
		'guarantee_scope'                 => 'Funktionsfähiges Anfragesystem, kein Anfrage-Volumen.',
	];
}

/**
 * Format a EUR amount with German thousand separators.
 *
 * @param int|float $value Amount in EUR.
 * @return string
 */
function hu_format_eur( $value ) {
	return number_format( (float) $value, 0, ',', '.' ) . ' €';
}

/**
 * Display value of the Foundation build price.
 *
 * @return string
 */
function hu_foundation_price_display() {
	return hu_format_eur( HU_FOUNDATION_PRICE_STANDARD );
}

/**
 * Display the surcharge for each additional independent product route.
 *
 * @return string
 */
function hu_foundation_extra_product_price_display() {
	return hu_format_eur( HU_FOUNDATION_EXTRA_PRODUCT_PRICE );
}

/**
 * Display the paid Solar/SHK marketcheck price.
 *
 * @param bool $with_net Append the net qualifier.
 * @return string
 */
function hu_marketcheck_price( $with_net = false ) {
	$price = hu_format_eur( HU_MARKETCHECK_PRICE );
	return $with_net ? $price . ' netto' : $price;
}

/**
 * Display value of the hosting cost that runs after the build.
 *
 * @return string
 */
function hu_foundation_hosting_display() {
	return hu_format_eur( HU_FOUNDATION_HOSTING_MONTHLY );
}

/**
 * Display value of build plus hosting over a number of months.
 *
 * Every page that holds the own system against bought leads quotes this
 * total. It is derived here because the price used to live as literal copy:
 * the money page moved to the canon while three other routes kept quoting
 * the retired range.
 *
 * @param int $months Months of hosting contained in the total.
 * @return string
 */
function hu_foundation_total_display( $months ) {
	$months = max( 0, (int) $months );

	return hu_format_eur( HU_FOUNDATION_PRICE_STANDARD + $months * HU_FOUNDATION_HOSTING_MONTHLY );
}

// ── Portal-Einkauf als Referenz-Rechenbeispiel ──────────────────
// Ausdruecklich kein Marktmittelwert: rund 13,5 gekaufte Anfragen im Monat zu
// rund 80 EUR. Drei Seiten zeigen dieselbe Gegenueberstellung — Money Page,
// TCO-Vergleich und die Preis-FAQ der Startseite. Vorher stand sie als
// Literal in allen dreien.
define( 'HU_PORTAL_REFERENCE_MONTHLY', 1080 );
define( 'HU_PORTAL_REFERENCE_LEAD_PRICE', 80 );

/**
 * Display value of the monthly portal spend in the reference example.
 *
 * @return string
 */
function hu_portal_reference_monthly_display() {
	return '~ ' . hu_format_eur( HU_PORTAL_REFERENCE_MONTHLY );
}

/**
 * Display value of the portal spend over a number of months.
 *
 * Auf volle Tausend gerundet. Die Zahl ist ein Rechenbeispiel, keine Rechnung;
 * eine exakte Summe taeuschte eine Genauigkeit vor, die das Modell nicht hat.
 *
 * @param int $months Months of portal purchasing.
 * @return string
 */
function hu_portal_reference_total_display( $months ) {
	$months = max( 0, (int) $months );
	$total  = HU_PORTAL_REFERENCE_MONTHLY * $months;

	return hu_format_eur( (int) round( $total / 1000 ) * 1000 );
}

/**
 * Approximate number of bought requests over a number of months.
 *
 * Auf volle Zehner abgerundet, aus derselben Monatsausgabe abgeleitet wie die
 * Summe — sonst behaupten Stueckzahl und Betrag zwei verschiedene Modelle.
 * Abgerundet, nicht kaufmaennisch: die Stueckzahl soll die Gegenueberstellung
 * nicht besser aussehen lassen, als das Modell hergibt.
 *
 * @param int $months Months of portal purchasing.
 * @return string
 */
function hu_portal_reference_leads_display( $months ) {
	$months = max( 0, (int) $months );
	$leads  = ( HU_PORTAL_REFERENCE_MONTHLY / HU_PORTAL_REFERENCE_LEAD_PRICE ) * $months;

	return '~ ' . number_format( (int) floor( $leads / 10 ) * 10, 0, ',', '.' );
}

// ── Sofortkontakt-Setup: Einstiegsangebot der Solar-Money-Page ───
// Eigene Ebene unterhalb des Foundation-Modells: beschleunigt die Reaktion
// auf vorhandene Anfragen, auch auf gekaufte Portal-Leads.
define( 'HU_ENTRY_SETUP_PRICE', 790 );
define( 'HU_ENTRY_SETUP_BUSINESS_DAYS', 5 );

/**
 * Display value of the Sofortkontakt-Setup price.
 *
 * Single source for the price, so nav label, section CTA and the offer
 * panel cannot drift apart when the price changes.
 *
 * @param bool $with_net Append the "netto" qualifier.
 * @return string
 */
function hu_entry_setup_price( $with_net = false ) {
	$price = sprintf( '%d €', HU_ENTRY_SETUP_PRICE );

	return $with_net ? $price . ' netto' : $price;
}

// ── Anfragesystem-Analyse: zweite Stufe der Angebotsleiter ───────
// Schriftlicher Befund zu Anfragequellen, Tracking, Funnel und
// Vertriebsanschluss. Steht vor dem Sofortkontakt-Setup (790 EUR) und
// Foundation-Aufbau und wird bei Umsetzung auf den Aufbau angerechnet.
//
// Die Anrechenbarkeit gehoert zum Preis und wird deshalb hier mitgefuehrt:
// ohne sie liest sich der Betrag als zusaetzliche Huerde vor dem Aufbau,
// mit ihr als vorgezogener Teil davon.
define( 'HU_ANALYSIS_PRICE', 690 );

/**
 * Display value of the Anfragesystem-Analyse price.
 *
 * @param bool $with_net Append the "netto" qualifier.
 * @return string
 */
function hu_analysis_price( $with_net = false ) {
	$price = hu_format_eur( HU_ANALYSIS_PRICE );

	return $with_net ? $price . ' netto' : $price;
}

// ── Tracking-Leiter: Einrichtung + laufende Kontrolle ─────────
// Vier Stufen, eine Definition (hu_tracking_product_ladder() weiter unten).
// Die Werte standen zuvor als Literale in Template, FAQ und Meta-Description
// und konnten dort driften.
//
// Stufe 1 (measurement) ist clientseitig: GA4, Tag Manager, Consent Mode und
// Google Ads im Browser, ohne eigenen Server. Bis 2026-09-26 hatte sie keinen
// eigenen Preis; Startseite und /ga4-tracking-setup/ liehen sich den
// Server-Side-Betrag und verkauften damit ein anderes Produkt als
// /server-side-tracking-b2b/ zum selben Preis. Herleitung der 890 € aus
// veroeffentlichten Festpreisen im Markt:
// docs/decisions/tracking-preisleiter.md.
define( 'HU_TRACKING_MEASUREMENT_SETUP', 890 );
define( 'HU_TRACKING_MEASUREMENT_WEEKS_MIN', 1 );
define( 'HU_TRACKING_MEASUREMENT_WEEKS_MAX', 2 );

// Stufen 2 bis 4 sind die Server-Side-Leiter. Die Setup-Betraege sind die
// Endkunden-Obergrenze fuer dieselbe Leistung. Der White-Label-Pfad weiter
// unten liegt bewusst darunter; werden diese Werte gesenkt, muss der
// White-Label-Block mitgeprueft werden, sonst zahlen Agenturen mehr als
// Endkunden — auf zwei indexierten Seiten nachlesbar.
// Die Monatsbeitraege der Tracking Care bleiben davon unberuehrt.
define( 'HU_TRACKING_STANDARD_SETUP', 1290 );
define( 'HU_TRACKING_STANDARD_CARE_MONTHLY', 99 );
define( 'HU_TRACKING_STANDARD_INCLUDED_MINUTES', 30 );
define( 'HU_TRACKING_PRO_SETUP', 1900 );
define( 'HU_TRACKING_PRO_CARE_MONTHLY', 149 );
define( 'HU_TRACKING_PRO_INCLUDED_MINUTES', 60 );
define( 'HU_TRACKING_CUSTOM_SETUP_MIN', 3500 );
define( 'HU_TRACKING_CUSTOM_CARE_MONTHLY_MIN', 199 );
define( 'HU_TRACKING_DURATION_WEEKS_MIN', 2 );
define( 'HU_TRACKING_DURATION_WEEKS_MAX', 3 );

/**
 * Return the canonical tracking prices and delivery model.
 *
 * Paketschluessel: measurement (Stufe 1, clientseitig), standard, pro und
 * individual (Stufen 2 bis 4, Server-Side). Stufe 1 hat keine Care-Stufe:
 * ohne eigenen Server gibt es keine Infrastruktur, die laufend zu pruefen
 * waere.
 *
 * @return array<string, mixed>
 */
function hu_tracking_pricing_canon() {
	$monthly_terms = 'Nettopreise, monatlich kündbar, Hosting separat';

	return [
		'measurement' => [
			'setup' => [
				'value'   => HU_TRACKING_MEASUREMENT_SETUP,
				'display' => hu_format_eur( HU_TRACKING_MEASUREMENT_SETUP ),
			],
			'terms' => 'Einmaliger Nettopreis, kein Server und kein Hosting nötig',
		],
		'standard' => [
			'setup'                  => [
				'value'   => HU_TRACKING_STANDARD_SETUP,
				'display' => hu_format_eur( HU_TRACKING_STANDARD_SETUP ),
			],
			'care'                   => [
				'value'   => HU_TRACKING_STANDARD_CARE_MONTHLY,
				'display' => hu_format_eur( HU_TRACKING_STANDARD_CARE_MONTHLY ) . ' / Monat',
			],
			'included_minutes'       => HU_TRACKING_STANDARD_INCLUDED_MINUTES,
			'terms'                  => $monthly_terms,
		],
		'pro'      => [
			'setup'                  => [
				'value'   => HU_TRACKING_PRO_SETUP,
				'display' => hu_format_eur( HU_TRACKING_PRO_SETUP ),
			],
			'care'                   => [
				'value'   => HU_TRACKING_PRO_CARE_MONTHLY,
				'display' => hu_format_eur( HU_TRACKING_PRO_CARE_MONTHLY ) . ' / Monat',
			],
			'included_minutes'       => HU_TRACKING_PRO_INCLUDED_MINUTES,
			'terms'                  => $monthly_terms,
		],
		'individual' => [
			'setup' => [
				'value'   => HU_TRACKING_CUSTOM_SETUP_MIN,
				'display' => 'ab ' . hu_format_eur( HU_TRACKING_CUSTOM_SETUP_MIN ),
			],
			'care'  => [
				'value'   => HU_TRACKING_CUSTOM_CARE_MONTHLY_MIN,
				'display' => 'ab ' . hu_format_eur( HU_TRACKING_CUSTOM_CARE_MONTHLY_MIN ) . ' / Monat',
			],
			'terms' => 'Nettopreise, Umfang nach Aufnahme, Hosting separat',
		],
		'delivery' => [
			'weeks_min' => HU_TRACKING_DURATION_WEEKS_MIN,
			'weeks_max' => HU_TRACKING_DURATION_WEEKS_MAX,
		],
	];
}

/**
 * Return one display field from a Server-Side-Tracking price.
 *
 * @param string $package   Package key.
 * @param string $component Price component: setup or care.
 * @param string $field     Field key.
 * @param string $fallback  Fallback value.
 * @return string
 */
function hu_tracking_price( $package, $component, $field = 'display', $fallback = '' ) {
	$prices = hu_tracking_pricing_canon();

	if ( ! isset( $prices[ $package ][ $component ] ) || ! is_array( $prices[ $package ][ $component ] ) ) {
		return $fallback;
	}

	if ( ! array_key_exists( $field, $prices[ $package ][ $component ] ) ) {
		return $fallback;
	}

	return (string) $prices[ $package ][ $component ][ $field ];
}

/**
 * Return one operational package detail from the tracking canon.
 *
 * @param string $package  Package key.
 * @param string $field    Field key.
 * @param string $fallback Fallback value.
 * @return string
 */
function hu_tracking_package_detail( $package, $field, $fallback = '' ) {
	$prices = hu_tracking_pricing_canon();

	if ( ! isset( $prices[ $package ] ) || ! is_array( $prices[ $package ] ) || ! array_key_exists( $field, $prices[ $package ] ) ) {
		return $fallback;
	}

	return (string) $prices[ $package ][ $field ];
}

/**
 * Display the canonical delivery window of a tracking setup.
 *
 * Ohne Argument gilt das Fenster der Server-Side-Stufen (Paralleltest
 * eingeschlossen); 'measurement' liefert das kuerzere Fenster von Stufe 1.
 *
 * @param string $package Package key.
 * @return string
 */
function hu_tracking_delivery_weeks_display( $package = 'standard' ) {
	if ( 'measurement' === $package ) {
		return sprintf( '%d bis %d Wochen', HU_TRACKING_MEASUREMENT_WEEKS_MIN, HU_TRACKING_MEASUREMENT_WEEKS_MAX );
	}

	return sprintf(
		'%d bis %d Wochen',
		HU_TRACKING_DURATION_WEEKS_MIN,
		HU_TRACKING_DURATION_WEEKS_MAX
	);
}

/**
 * Return the tracking product ladder: four stages, one definition.
 *
 * Name, Umfang, Preis und Lieferzeit jeder Stufe stehen nur hier. Startseite,
 * /ga4-tracking-setup/, /server-side-tracking-b2b/ und /performance-marketing/
 * zeigen Ausschnitte daraus, keine eigenen Fassungen: Genau diese eigenen
 * Fassungen hatten denselben Preis mit zwei verschiedenen Produkten belegt.
 *
 * Jede Server-Side-Stufe enthaelt die vorige. Stufe 4 baut auf Stufe 2 oder 3
 * auf; welche, entscheidet die technische Aufnahme.
 *
 * Felder: stage (1–4), name, scope (Stichworte), lead (fuer wen), items
 * (Umfang), price (Anzeige, bei Stufe 4 mit "ab"), weeks (Lieferzeit oder
 * leer), terms (Preisbedingung).
 *
 * @return array<string, array<string, mixed>>
 */
function hu_tracking_product_ladder() {
	$server_weeks = hu_tracking_delivery_weeks_display();

	return [
		'measurement' => [
			'stage' => 1,
			'name'  => 'Conversion-Tracking',
			'scope' => 'GA4 · Tag Manager · Consent Mode · Google Ads',
			'lead'  => 'Für eine Website mit klaren Haupt-Conversions. Gemessen wird im Browser, ohne eigenen Server.',
			'items' => [
				'Bestandsaufnahme und schriftlicher Messplan',
				'Google Tag Manager und GA4 eingerichtet oder bereinigt',
				'Consent Mode an Ihr Consent-Tool angebunden',
				'Google Ads und bis zu drei Haupt-Conversions',
				'Abnahmeprotokoll mit Testfällen, Dokumentation und Übergabe',
			],
			'price' => hu_tracking_price( 'measurement', 'setup' ),
			'weeks' => hu_tracking_delivery_weeks_display( 'measurement' ),
			'terms' => hu_tracking_package_detail( 'measurement', 'terms' ),
		],
		'standard'    => [
			'stage' => 2,
			'name'  => 'Server-Side Tracking',
			'scope' => 'Server-GTM · eigene Subdomain · Enhanced Conversions',
			'lead'  => 'Wenn Google Ads das Budget trägt und Signale im Browser verloren gehen: Die Messung läuft über Ihren eigenen Server-Endpunkt.',
			'items' => [
				'Alles aus Conversion-Tracking',
				'Server-GTM auf eigener Tracking-Subdomain, in Ihren Konten',
				'Enhanced Conversions, soweit Formular und Consent es tragen',
				'Paralleltest mit Prüfung auf fehlende und doppelte Events',
				'GTM-Versionen und Datenfluss dokumentiert',
			],
			'price' => hu_tracking_price( 'standard', 'setup' ),
			'weeks' => $server_weeks,
			'terms' => 'Einmaliger Nettopreis, Server-Hosting separat',
		],
		'pro'         => [
			'stage' => 3,
			'name'  => 'Server-Side mit Meta',
			'scope' => 'Meta Conversion API · Deduplizierung · bis zu acht Events',
			'lead'  => 'Für Unternehmen, die Google und Meta parallel für Anfragen einsetzen oder mehrere Conversion-Strecken messen.',
			'items' => [
				'Alles aus Server-Side Tracking',
				'Meta Pixel und Conversion API mit event_id-Deduplizierung',
				'Bis zu acht definierte Events',
				'Mehrere Formulare oder Conversion-Strecken',
				'Abnahme über GA4, Google Ads und Meta hinweg',
			],
			'price' => hu_tracking_price( 'pro', 'setup' ),
			'weeks' => $server_weeks,
			'terms' => 'Einmaliger Nettopreis, Server-Hosting separat',
		],
		'individual'  => [
			'stage' => 4,
			'name'  => 'Tracking bis ins CRM',
			'scope' => 'CRM-Status · Offline-Conversions · Rücksignal',
			'lead'  => 'Wenn nicht das Formular, sondern Lead-Qualität, Angebot oder Auftrag das Signal für die Kampagnen sein soll.',
			'items' => [
				'CRM-Anbindung mit eindeutiger Lead-Zuordnung',
				'Lead-Status oder Auftrag als Offline-Conversion zurück an die Werbeplattformen',
				'Mehrere Domains, Märkte oder Funnel',
				'Weitere Werbeplattformen nach technischer Aufnahme',
				'Individueller Messplan, Datenstrecke und Abnahme',
			],
			'price' => hu_tracking_price( 'individual', 'setup' ),
			'weeks' => '',
			'terms' => 'Nettopreis nach technischer Aufnahme, Server-Hosting separat',
		],
	];
}

/**
 * Display the tracking ladder, or part of it, as one phrase.
 *
 * Ein Satzbaustein wie hu_freelancer_retainer_display(): keine Oberflaeche
 * schreibt eine Stufe einzeln ab. "netto" steht einmal am Ende.
 *
 * @param int $from_stage First stage to include (1–4).
 * @return string
 */
function hu_tracking_ladder_display( $from_stage = 1 ) {
	$parts = [];

	foreach ( hu_tracking_product_ladder() as $product ) {
		if ( (int) $product['stage'] < (int) $from_stage ) {
			continue;
		}

		$parts[] = sprintf( '%s %s', $product['name'], $product['price'] );
	}

	return implode( ', ', $parts ) . ' netto';
}

// ── WordPress-Freelancer-Nebenpfad ───────────────────────────────
// Die Anfrage-Website wird nicht mehr als "Seitenzahl × Einheitsbetrag"
// kalkuliert. Das Grundprodukt trägt Systemkosten + erste Hauptseite.
// Zusatzseiten, Texterstellung und Projektmodule sind getrennte Einheiten.
// Öffentliche Festpreise bleiben gerundet; die internen Produktionsstunden
// dokumentieren den Aufwand hinter den Einheiten. Direkte offene Stundenarbeit
// wird mit 95 EUR/h kalkuliert, Agentur-/White-Label-Arbeit mit 70 EUR/h.
// Diese Sätze sind keine öffentlichen Preisanker des Produktkonfigurators.
define( 'HU_FREELANCER_WEBSITE_MIN', 1900 );
define( 'HU_FREELANCER_WEBSITE_PAGES', 1 );
define( 'HU_FREELANCER_WEBSITE_EXTRA_PAGE', 400 ); // Kompatibilität: Standard-Unterseite.
define( 'HU_WEBSITE_CALCULATOR_MAX', 10 );
define( 'HU_WEBSITE_PAGE_UTILITY', 300 );
define( 'HU_WEBSITE_PAGE_STANDARD', 400 );
define( 'HU_WEBSITE_PAGE_SALES', 790 );
define( 'HU_WEBSITE_COPY_BASE', 290 );
define( 'HU_WEBSITE_COPY_UTILITY', 50 );
define( 'HU_WEBSITE_COPY_STANDARD', 150 );
define( 'HU_WEBSITE_COPY_SALES', 290 );
define( 'HU_WEBSITE_DESIGN_FIRST', 690 );
define( 'HU_WEBSITE_DESIGN_EXTRA', 250 );
define( 'HU_WEBSITE_CRM_STANDARD', 990 );

/**
 * Canonical production units behind the public website configurator.
 *
 * Hours are productive-effort estimates, not public billing items. Public
 * prices are intentionally rounded product prices. Working-day factors model
 * planning capacity and may differ from hours / 8 because reviews and QA are
 * scheduled as explicit production phases.
 *
 * @return array<string, mixed>
 */
function hu_website_scope_units() {
	return [
		'base' => [ 'price' => HU_FREELANCER_WEBSITE_MIN, 'hours' => 20, 'days' => 2 ],
		'pages' => [
			'utility'  => [ 'price' => HU_WEBSITE_PAGE_UTILITY, 'hours' => 3, 'days' => 0.5 ],
			'standard' => [ 'price' => HU_WEBSITE_PAGE_STANDARD, 'hours' => 4, 'days' => 0.5 ],
			'sales'    => [ 'price' => HU_WEBSITE_PAGE_SALES, 'hours' => 8, 'days' => 1 ],
		],
		'copy' => [
			'base'     => [ 'price' => HU_WEBSITE_COPY_BASE, 'hours' => 3, 'days' => 0.5 ],
			'utility'  => [ 'price' => HU_WEBSITE_COPY_UTILITY, 'hours' => 0.5, 'days' => 0.25 ],
			'standard' => [ 'price' => HU_WEBSITE_COPY_STANDARD, 'hours' => 1.5, 'days' => 0.25 ],
			'sales'    => [ 'price' => HU_WEBSITE_COPY_SALES, 'hours' => 3, 'days' => 0.5 ],
		],
		'design' => [ 'first_hours' => 7, 'extra_hours' => 2.5 ],
		'tracking' => [ 'hours' => 9 ],
		'crm' => [ 'hours' => 10 ],
	];
}

/** Planning and pricing factors shared with the browser. */
function hu_website_calculator_rules() {
	$units = hu_website_scope_units();

	return [
		'version' => '2026-10-06.scope.v4',
		'max_pages' => HU_WEBSITE_CALCULATOR_MAX,
		'prices' => [
			'base' => HU_FREELANCER_WEBSITE_MIN,
			'page' => HU_FREELANCER_WEBSITE_EXTRA_PAGE,
			'page_utility' => HU_WEBSITE_PAGE_UTILITY,
			'page_standard' => HU_WEBSITE_PAGE_STANDARD,
			'page_sales' => HU_WEBSITE_PAGE_SALES,
			'copy_base' => HU_WEBSITE_COPY_BASE,
			'copy_utility' => HU_WEBSITE_COPY_UTILITY,
			'copy_standard' => HU_WEBSITE_COPY_STANDARD,
			'copy_sales' => HU_WEBSITE_COPY_SALES,
			'tracking' => (int) hu_tracking_price( 'measurement', 'setup', 'value' ),
			'design_first' => HU_WEBSITE_DESIGN_FIRST,
			'design_extra' => HU_WEBSITE_DESIGN_EXTRA,
			'crm' => HU_WEBSITE_CRM_STANDARD,
		],
		'days' => [
			'qa' => 1,
			'base_implementation' => $units['base']['days'],
			'page_utility' => $units['pages']['utility']['days'],
			'page_standard' => $units['pages']['standard']['days'],
			'page_sales' => $units['pages']['sales']['days'],
			'copy_base' => $units['copy']['base']['days'],
			'copy_utility' => $units['copy']['utility']['days'],
			'copy_standard' => $units['copy']['standard']['days'],
			'copy_sales' => $units['copy']['sales']['days'],
			'tracking' => 1,
			'relaunch' => 2,
			'crm' => 3,
			'design_first' => 2,
			'design_extra_layout' => 0.5,
		],
	];
}

/**
 * Normalize page-type counts.
 *
 * New requests send the three explicit counts. Legacy requests that only send
 * "seiten" remain valid and are interpreted as standard content pages.
 *
 * @param int   $pages   Total pages from legacy callers.
 * @param array $options Quote options.
 * @return array<string, int>
 */
function hu_website_page_type_counts( $pages, $options = [] ) {
	$has_explicit = array_key_exists( 'utility_pages', $options ) || array_key_exists( 'standard_pages', $options ) || array_key_exists( 'sales_pages', $options );
	if ( ! $has_explicit ) {
		return [ 'utility' => 0, 'standard' => max( 0, (int) $pages - 1 ), 'sales' => 0 ];
	}

	$counts = [
		'utility' => max( 0, (int) ( $options['utility_pages'] ?? 0 ) ),
		'standard' => max( 0, (int) ( $options['standard_pages'] ?? 0 ) ),
		'sales' => max( 0, (int) ( $options['sales_pages'] ?? 0 ) ),
	];
	$remaining = HU_WEBSITE_CALCULATOR_MAX - 1;
	foreach ( [ 'utility', 'standard', 'sales' ] as $key ) {
		$counts[ $key ] = min( $counts[ $key ], $remaining );
		$remaining -= $counts[ $key ];
	}

	return $counts;
}

/** Standard production plan; only a bespoke dashboard remains unpriced. */
function hu_website_quote( $pages, $kind = 'neubau', $tracking = false, $options = [] ) {
	$pages = max( 1, min( HU_WEBSITE_CALCULATOR_MAX, (int) $pages ) );
	$rules = hu_website_calculator_rules();
	$factors = $rules['days'];
	$counts = hu_website_page_type_counts( $pages, $options );
	$pages = 1 + array_sum( $counts );
	$design = $options['design'] ?? ( ! empty( $options['screendesign'] ) ? 'neu' : 'basis' );
	$design = in_array( $design, [ 'basis', 'vorhanden', 'neu' ], true ) ? $design : 'basis';
	$layouts = 'neu' === $design ? max( 1, min( $pages, (int) ( $options['design_layouts'] ?? 1 ) ) ) : 0;

	$page_price =
		$counts['utility'] * $rules['prices']['page_utility']
		+ $counts['standard'] * $rules['prices']['page_standard']
		+ $counts['sales'] * $rules['prices']['page_sales'];
	$text_price = ! empty( $options['texte'] )
		? $rules['prices']['copy_base']
			+ $counts['utility'] * $rules['prices']['copy_utility']
			+ $counts['standard'] * $rules['prices']['copy_standard']
			+ $counts['sales'] * $rules['prices']['copy_sales']
		: 0;

	$components = [
		'implementation' => $factors['base_implementation']
			+ $counts['utility'] * $factors['page_utility']
			+ $counts['standard'] * $factors['page_standard']
			+ $counts['sales'] * $factors['page_sales'],
		'texts' => ! empty( $options['texte'] )
			? $factors['copy_base']
				+ $counts['utility'] * $factors['copy_utility']
				+ $counts['standard'] * $factors['copy_standard']
				+ $counts['sales'] * $factors['copy_sales']
			: 0,
		'design' => 'neu' === $design ? $factors['design_first'] + ( $layouts - 1 ) * $factors['design_extra_layout'] : 0,
		'tracking' => $tracking ? $factors['tracking'] : 0,
		'relaunch' => 'relaunch' === $kind ? $factors['relaunch'] : 0,
		'crm' => ! empty( $options['crm'] ) ? $factors['crm'] : 0,
	];
	$days = (int) ceil( array_sum( $components ) );
	$design_price = $layouts ? $rules['prices']['design_first'] + ( $layouts - 1 ) * $rules['prices']['design_extra'] : 0;
	$price = $rules['prices']['base'] + $page_price + $text_price
		+ ( $tracking ? $rules['prices']['tracking'] : 0 )
		+ $design_price
		+ ( ! empty( $options['crm'] ) ? $rules['prices']['crm'] : 0 );

	return [
		'pages' => $pages,
		'page_types' => $counts,
		'kind' => 'relaunch' === $kind ? 'relaunch' : 'neubau',
		'tracking' => (bool) $tracking,
		'design' => $design,
		'design_layouts' => $layouts,
		'price' => $price,
		'page_price' => $page_price,
		'text_price' => $text_price,
		'design_price' => $design_price,
		'days' => $days,
		'components' => $components,
		'preparation_days' => $components['texts'] + $components['design'],
		'implementation_days' => $components['implementation'] + $components['tracking'] + $components['relaunch'] + $components['crm'],
		'duration_open' => ! empty( $options['dashboard'] ),
		'price_review' => 'vorhanden' === $design || ! empty( $options['crm'] ) || ! empty( $options['dashboard'] ),
		'version' => $rules['version'],
		// Compatibility for existing integrations; public copy uses working days.
		'weeks' => (int) ceil( $days / 5 ),
	];
}

// Landingpage: eine Seite, ein Angebot, ein Ziel. Festpreis inklusive Text,
// Anfrageformular und Herkunftsmessung. Der höhere Preis gegenüber einer
// Anfrage-Website erklärt sich durch den spezialisierten Angebotsumfang und die Messung.
define( 'HU_LANDINGPAGE_PRICE', 1990 );

// Übernahme-Check: bezahlte Diagnose, bevor eine fremde WordPress-Installation
// in Weiterentwicklung oder Relaunch übernommen wird. Ohne Check keine offene
// "nach Absprache"-Zusage für Fremdbestand (Entscheidung 02.09.2026).
// Wird bei Beauftragung mit dem Folgeprojekt verrechnet. Der Befund gehört dem
// Kunden, auch wenn er mit jemand anderem weiterarbeitet.
define( 'HU_FREELANCER_TAKEOVER_CHECK_PRICE', 390 );

// Weiterentwicklungs-Retainer der Freelancer-Route: gebuchtes Monatskontingent,
// monatlich kündbar, ausdrücklich ohne Rufbereitschaft und ohne
// Reaktionszeit-Zusage. Das Kontingent ist die Einheit – ein Stundensatz steht
// weder als Zahl noch als Herleitung auf der Seite (gleiche Regel wie White-Label).
define( 'HU_FREELANCER_RETAINER_TIERS', [
	[ 'hours' => 4, 'price' => 490 ],
	[ 'hours' => 8, 'price' => 950 ],
] );

/**
 * Display the canonical starting price for the WordPress freelancer route.
 *
 * @param bool $with_net Append the "netto" qualifier.
 * @return string
 */
function hu_freelancer_website_price( $with_net = false ) {
	$price = hu_format_eur( HU_FREELANCER_WEBSITE_MIN );

	return $with_net ? $price . ' netto' : $price;
}

/**
 * Display the compatibility price for a standard page beyond the first page.
 *
 * @param bool $with_net Append the "netto" qualifier.
 * @return string
 */
function hu_freelancer_website_extra_page_price( $with_net = false ) {
	$price = hu_format_eur( HU_FREELANCER_WEBSITE_EXTRA_PAGE );

	return $with_net ? $price . ' netto' : $price;
}

/**
 * Describe the request website scope as one phrase.
 *
 * Ein Satzbaustein, damit Seitenzahl und Zusatzpreis nirgends einzeln
 * abgeschrieben werden. "netto" steht einmal am Ende.
 *
 * @return string
 */
function hu_freelancer_website_scope_display() {
	return sprintf(
		'Festpreis für Grundsystem und erste Hauptseite; weitere Seiten ab %s netto nach Seitentyp',
		hu_format_eur( HU_WEBSITE_PAGE_UTILITY )
	);
}

/**
 * Spell out a small count for running text.
 *
 * Kleine Zahlen stehen im Fliesstext als Wort, wie die Referenzzahl der
 * Startseite. Ab sieben bleibt die Ziffer.
 *
 * @param int $count Count to spell out.
 * @return string
 */
function hu_pricing_count_word( $count ) {
	$words = [ 2 => 'zwei', 3 => 'drei', 4 => 'vier', 5 => 'fünf', 6 => 'sechs' ];

	return $words[ (int) $count ] ?? (string) $count;
}

/**
 * Display the canonical fixed price of a landing page.
 *
 * @param bool $with_net Append the "netto" qualifier.
 * @return string
 */
function hu_landingpage_price( $with_net = false ) {
	$price = hu_format_eur( HU_LANDINGPAGE_PRICE );

	return $with_net ? $price . ' netto' : $price;
}

/**
 * Display the canonical price of the Übernahme-Check.
 *
 * @param bool $with_net Append the "netto" qualifier.
 * @return string
 */
function hu_freelancer_takeover_check_price( $with_net = false ) {
	$price = hu_format_eur( HU_FREELANCER_TAKEOVER_CHECK_PRICE );

	return $with_net ? $price . ' netto' : $price;
}

/**
 * Display all development contingents of the freelancer route as one phrase.
 *
 * Ein Satzbaustein aus allen Stufen, damit keine Oberfläche eine Stufe
 * einzeln abschreibt. "netto" steht einmal am Ende und gilt für alle.
 *
 * @return string
 */
function hu_freelancer_retainer_display() {
	$tiers = array_map(
		static function ( $tier ) {
			return sprintf( '%d Stunden für %s', $tier['hours'], hu_format_eur( $tier['price'] ) );
		},
		HU_FREELANCER_RETAINER_TIERS
	);

	return implode( ' oder ', $tiers ) . ' netto';
}

// ── White-Label-Nebenpfad ────────────────────────────────────────
// Der Partner-Funnel hat eine eigene Einstiegsebene. Sie bleibt bewusst
//
// Die drei groesseren Erstprojekte liegen rund 30 % unter dem jeweiligen
// Endkundenpreis der Tracking-Leiter weiter oben. Das ist die Marge der
// Agentur und zugleich das, wofuer sie zahlt beziehungsweise nicht zahlt:
// kein Endkundenkontakt, keine Akquise, Wiederholungsgeschaeft.
//
// "ab" statt Spanne ist Absicht: die Untergrenze sortiert aus, ohne dass eine
// Obergrenze jemanden verschreckt, der seinen Scope noch nicht kennt. Ein
// Stundensatz gehoert weder als Zahl noch als Herleitung auf die Seite.
//
// Der Audit ist bewusst der guenstigste Punkt der Leiter. Er ist kein
// Ertragsprodukt, sondern der Tueroeffner, der das Setup verkauft.
define( 'HU_WHITELABEL_TEST_SPRINT_PRICE', 590 );
define( 'HU_WHITELABEL_TRACKING_AUDIT_MIN', 690 );

// 900 statt zuvor 1.290: HU_TRACKING_STANDARD_SETUP oben ist der oeffentlich
// auf /server-side-tracking-b2b/ genannte Endkundenpreis fuer dieselbe
// Leistung und steht ebenfalls bei 1.290. Bei identischem Betrag hat die
// Agentur keinen Margenspielraum — der Kaufgrund fehlt, und beide Zahlen sind
// auf indexierten Seiten nachlesbar. 900 haelt den rund 30-prozentigen
// Abstand, den jede andere Sprosse dieser Leiter einhaelt.
define( 'HU_WHITELABEL_SERVER_SIDE_MIN', 900 );

// 1.390 statt zuvor 1.900 (bis 2026-09-26): Seit es mit HU_LANDINGPAGE_PRICE
// einen oeffentlichen Endkundenpreis fuer die Landingpage gibt, gilt auch hier
// der rund 30-prozentige Abstand. 1.900 laege fast auf dem Endkundenpreis.
// Der alte Betrag steht als wl-landingpage-1900 in
// scripts/canon-forbidden-values.txt.
define( 'HU_WHITELABEL_LANDINGPAGE_MIN', 1390 );

// Der Retainer ist die versprochene Zielstufe des Partner-Funnels und trug
// keine Zahl — damit war er eine Absichtserklaerung. Eine Untergrenze macht
// ihn real, ohne dass eine Obergrenze jemanden verschreckt.
//
// und steht bei 1.500. Derselbe Betrag hiesse, die Agentur zahlt so viel wie
// ein Endkunde — das bricht die 30-%-Regel, die auf jeder anderen Sprosse
// dieser Leiter gilt.
//
// 780 seit 2026-09-25, davor 1.000 (Feinschliff /whitelabel-retainer/). Der
// alte Betrag steht als wl-retainer-1000 in scripts/canon-forbidden-values.txt.
//
// Das Stundenkontingent gehoert zur Zahl: "ab 780 € / Monat" allein sagt
// nicht, wofuer. Ein Stundensatz steht dabei weder als Zahl noch als
// Herleitung auf der Seite — das Kontingent ist die Einheit, nicht die Stunde.
define( 'HU_WHITELABEL_RETAINER_MIN', 780 );
define( 'HU_WHITELABEL_RETAINER_HOURS', 12 );

/**
 * Return the canonical White-Label pricing model.
 *
 * @return array<string, array<string, int|string>>
 */
function hu_whitelabel_pricing_canon() {
	return [
		'test_sprint'      => [
			'value'   => HU_WHITELABEL_TEST_SPRINT_PRICE,
			'display' => sprintf( '%d € netto', HU_WHITELABEL_TEST_SPRINT_PRICE ),
			// Der einzige feste Betrag der Seite. Auf der Angebotskarte traegt er
			// das Wort "Festpreis", weil dort zuvor "Festpreis nach
			// Umfangsklaerung" danebenstand und ihn weich aussehen liess.
			'display_fixed' => sprintf( 'Festpreis %d € netto', HU_WHITELABEL_TEST_SPRINT_PRICE ),
		],
		'tracking_audit'   => [
			'value'   => HU_WHITELABEL_TRACKING_AUDIT_MIN,
			'display' => sprintf( 'ab %d € netto', HU_WHITELABEL_TRACKING_AUDIT_MIN ),
		],
		'server_side'      => [
			'value'   => HU_WHITELABEL_SERVER_SIDE_MIN,
			'display' => 'ab ' . hu_format_eur( HU_WHITELABEL_SERVER_SIDE_MIN ) . ' netto',
		],
		'landingpage'      => [
			'value'   => HU_WHITELABEL_LANDINGPAGE_MIN,
			'display' => 'ab ' . hu_format_eur( HU_WHITELABEL_LANDINGPAGE_MIN ) . ' netto',
		],
		'retainer'         => [
			'value'   => HU_WHITELABEL_RETAINER_MIN,
			'display' => 'ab ' . hu_format_eur( HU_WHITELABEL_RETAINER_MIN ) . ' / Monat netto',
			// Kontingent-Fassungen. Die Angebotskarte traegt die kompakte Zeile,
			// die FAQ den Satzbaustein — beide lesen dieselbe Zahl, damit Karte
			// und Antwort nicht auseinanderlaufen koennen.
			'display_hours' => sprintf(
				'%1$d Stunden / Monat · %2$s netto',
				HU_WHITELABEL_RETAINER_HOURS,
				hu_format_eur( HU_WHITELABEL_RETAINER_MIN )
			),
			// Ohne Praeposition fuer den Eckdaten-Block, mit fuer den Fliesstext
			// der FAQ. Zwei Fassungen aus einer Zahl statt zweier Literale.
			'display_hours_plain' => sprintf(
				'%1$d Stunden im Monat für %2$s netto',
				HU_WHITELABEL_RETAINER_HOURS,
				hu_format_eur( HU_WHITELABEL_RETAINER_MIN )
			),
			'display_hours_sentence' => sprintf(
				'bei %1$d Stunden im Monat für %2$s netto',
				HU_WHITELABEL_RETAINER_HOURS,
				hu_format_eur( HU_WHITELABEL_RETAINER_MIN )
			),
		],
	];
}

/**
 * Return one field from the canonical White-Label pricing model.
 *
 * @param string $key      Price key.
 * @param string $field    Field key.
 * @param string $fallback Fallback value.
 * @return string
 */
function hu_whitelabel_price( $key, $field = 'display', $fallback = '' ) {
	$prices = hu_whitelabel_pricing_canon();

	if ( ! isset( $prices[ $key ] ) || ! is_array( $prices[ $key ] ) ) {
		return $fallback;
	}

	if ( ! array_key_exists( $field, $prices[ $key ] ) ) {
		return $fallback;
	}

	return (string) $prices[ $key ][ $field ];
}

/**
 * Compare published prices only where the delivered scope is identical.
 *
 * Landingpage is deliberately excluded: the direct offer includes concept
 * and copy; the agency supplies both. Ratios and labels share one source
 * with the hero strip, so a price change cannot leave a stale discount.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_whitelabel_margin_rows() {
	$retail = (int) hu_tracking_price( 'standard', 'setup', 'value' );
	$agency = (int) hu_whitelabel_price( 'server_side', 'value' );
	if ( $retail <= 0 || $agency <= 0 ) {
		return [];
	}
	$step       = 500;
	$scale      = (int) ( ceil( max( $retail, $agency ) / $step ) * $step );
	$difference = $retail - $agency;
	$discount   = (int) round( $difference / $retail * 100 );
	$ticks      = [];
	for ( $value = 0; $value <= $scale; $value += $step ) {
		$ticks[] = [
			'value'    => $value,
			'display'  => $value === $scale ? hu_format_eur( $value ) : number_format( $value, 0, ',', '.' ),
			'position' => number_format( $value / $scale * 100, 6, '.', '' ) . '%',
		];
	}
	return [
		[
			'key'              => 'server_side',
			'name'             => 'Server-Side-Setup',
			'retail_value'     => $retail,
			'agency_value'     => $agency,
			'retail_display'   => hu_tracking_price( 'standard', 'setup', 'display' ),
			'agency_display'   => hu_format_eur( $agency ),
			'difference'       => $difference,
			'difference_display' => hu_format_eur( $difference ),
			'discount_percent' => $discount,
			'discount_display' => ( $discount >= 0 ? '−' : '+' ) . abs( $discount ) . ' %',
			'scale_max'        => $scale,
			'scale_ticks'      => $ticks,
			'retail_ratio'     => number_format( $retail / $scale, 6, '.', '' ),
			'agency_ratio'     => number_format( $agency / $scale, 6, '.', '' ),
		],
	];
}
