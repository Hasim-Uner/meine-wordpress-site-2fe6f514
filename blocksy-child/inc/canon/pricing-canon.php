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
