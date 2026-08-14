<?php
/**
 * Canonical WGOS Foundation and add-on pricing.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HU_FOUNDATION_PRICE_STANDARD', 14900 );
define( 'HU_FOUNDATION_PRICE_FOUNDING', 9900 );
define( 'HU_FOUNDATION_HOSTING_MONTHLY', 50 );
define( 'HU_FOUNDATION_DURATION_WEEKS_MIN', 8 );
define( 'HU_FOUNDATION_DURATION_WEEKS_MAX', 10 );
define( 'HU_PERFORMANCE_RETAINER_PRICE', 1500 );
define( 'HU_PERFORMANCE_FOUNDING_RETAINER', 1000 );
define( 'HU_PERFORMANCE_FOUNDING_MTHS', 6 );
define( 'HU_PERFORMANCE_MIN_DURATION_MTHS', 6 );
define( 'HU_PREMIUM_LAYER_SETUP', 1500 );
define( 'HU_PREMIUM_LAYER_RETAINER', 700 );
define( 'HU_FOUNDING_DISCOUNT_PERCENT', 33 );
define( 'HU_VALUE_ANCHOR_MARKET_MIN', 34000 );
define( 'HU_VALUE_ANCHOR_MARKET_MAX', 77000 );

/**
 * Return the canonical pricing model.
 *
 * @return array<string, int|string>
 */
function hu_pricing_canon() {
	return [
		'foundation_price_standard'       => HU_FOUNDATION_PRICE_STANDARD,
		'foundation_price_founding'       => HU_FOUNDATION_PRICE_FOUNDING,
		'foundation_hosting_monthly'      => HU_FOUNDATION_HOSTING_MONTHLY,
		'foundation_duration_weeks_min'   => HU_FOUNDATION_DURATION_WEEKS_MIN,
		'foundation_duration_weeks_max'   => HU_FOUNDATION_DURATION_WEEKS_MAX,
		'performance_retainer_price'      => HU_PERFORMANCE_RETAINER_PRICE,
		'performance_founding_retainer'   => HU_PERFORMANCE_FOUNDING_RETAINER,
		'performance_founding_months'     => HU_PERFORMANCE_FOUNDING_MTHS,
		'performance_min_duration_months' => HU_PERFORMANCE_MIN_DURATION_MTHS,
		'premium_layer_setup'             => HU_PREMIUM_LAYER_SETUP,
		'premium_layer_retainer'          => HU_PREMIUM_LAYER_RETAINER,
		'founding_discount_percent'       => HU_FOUNDING_DISCOUNT_PERCENT,
		'value_anchor_market_min'         => HU_VALUE_ANCHOR_MARKET_MIN,
		'value_anchor_market_max'         => HU_VALUE_ANCHOR_MARKET_MAX,
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

/**
 * Display value of the Sofortkontakt-Setup entry price.
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

// ── Server-Side-Tracking: Einrichtung + laufende Kontrolle ───
// Eigener Preispfad fuer /server-side-tracking-b2b/. Die Werte standen zuvor
// als Literale in Template, FAQ und Meta-Description und konnten dort driften.
//
// Die Setup-Betraege sind die Endkunden-Obergrenze fuer dieselbe Leistung. Der
// White-Label-Pfad weiter unten liegt bewusst darunter; werden diese Werte
// gesenkt, muss der White-Label-Block mitgeprueft werden, sonst zahlen
// Agenturen mehr als Endkunden — auf zwei indexierten Seiten nachlesbar.
// Die Monatsbeitraege der Tracking Care bleiben davon unberuehrt.
define( 'HU_TRACKING_STANDARD_SETUP', 1290 );
define( 'HU_TRACKING_STANDARD_CARE_MONTHLY', 99 );
define( 'HU_TRACKING_STANDARD_INCLUDED_MINUTES', 30 );
define( 'HU_TRACKING_PRO_SETUP', 1900 );
define( 'HU_TRACKING_PRO_CARE_MONTHLY', 149 );
define( 'HU_TRACKING_PRO_INCLUDED_MINUTES', 60 );
define( 'HU_TRACKING_CUSTOM_SETUP_MIN', 3500 );
define( 'HU_TRACKING_CUSTOM_CARE_MONTHLY_MIN', 199 );
define( 'HU_TRACKING_RESPONSE_BUSINESS_DAYS', 2 );
define( 'HU_TRACKING_DURATION_WEEKS_MIN', 2 );
define( 'HU_TRACKING_DURATION_WEEKS_MAX', 3 );

/**
 * Return the canonical Server-Side-Tracking price and delivery model.
 *
 * @return array<string, mixed>
 */
function hu_tracking_pricing_canon() {
	$monthly_terms = 'Nettopreise, monatlich kündbar, Hosting separat';

	return [
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
			'response_business_days' => HU_TRACKING_RESPONSE_BUSINESS_DAYS,
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
			'response_business_days' => HU_TRACKING_RESPONSE_BUSINESS_DAYS,
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
 * Display the canonical delivery window for a standard tracking setup.
 *
 * @return string
 */
function hu_tracking_delivery_weeks_display() {
	return sprintf(
		'%d bis %d Wochen',
		HU_TRACKING_DURATION_WEEKS_MIN,
		HU_TRACKING_DURATION_WEEKS_MAX
	);
}

// ── White-Label-Nebenpfad ────────────────────────────────────────
// Der Partner-Funnel hat eine eigene Einstiegsebene. Sie bleibt bewusst
// getrennt vom WGOS-Foundation- und Add-on-Modell oben.
define( 'HU_WHITELABEL_TEST_SPRINT_PRICE', 590 );

/**
 * Return the canonical White-Label pricing model.
 *
 * @return array<string, array<string, int|string>>
 */
function hu_whitelabel_pricing_canon() {
	return [
		'test_sprint' => [
			'value'   => HU_WHITELABEL_TEST_SPRINT_PRICE,
			'display' => sprintf( '%d € netto', HU_WHITELABEL_TEST_SPRINT_PRICE ),
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
