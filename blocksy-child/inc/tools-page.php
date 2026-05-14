<?php
/**
 * Tools hub rendering helpers.
 *
 * Keeps the tools hub coupled to versioned theme code instead of editor HTML.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Redirect retired tools hub pages to the current System-Diagnose.
 *
 * @return void
 */
function nexus_redirect_deprecated_tools_page() {
	if ( is_admin() || wp_doing_ajax() || is_feed() || ! function_exists( 'nexus_is_tools_page' ) || ! nexus_is_tools_page() ) {
		return;
	}

	$target_url  = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/system-diagnose/' );
	$target_path = trailingslashit( '/' . ltrim( (string) wp_parse_url( $target_url, PHP_URL_PATH ), '/' ) );

	if ( function_exists( 'nexus_get_current_request_path' ) && nexus_get_current_request_path() === $target_path ) {
		return;
	}

	nocache_headers();
	wp_safe_redirect( $target_url, 301 );
	exit;
}
add_action( 'template_redirect', 'nexus_redirect_deprecated_tools_page', 2 );

/**
 * Return the curated tools hub cards.
 *
 * @return array<int, array<string, string>>
 */
function nexus_get_tools_hub_items() {
	$primary_urls   = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
	$audit_url      = function_exists( 'nexus_get_audit_url' ) ? nexus_get_audit_url() : home_url( '/system-diagnose/' );
	$wgos_url       = $primary_urls['wgos'] ?? home_url( '/wordpress-agentur-hannover/#wgos' );
	$asset_hub_url  = function_exists( 'nexus_get_wgos_asset_hub_url' ) ? nexus_get_wgos_asset_hub_url() : home_url( '/wordpress-agentur-hannover/#asset-uebersicht' );
	$core_web_url   = $primary_urls['cwv'] ?? home_url( '/wgos-assets/cwv-optimierung/' );

	return [
		[
			'eyebrow'      => 'Diagnose',
			'title'        => 'System-Diagnose',
			'description'  => 'Wenn zuerst klar werden soll, wo Positionierung, Proof, CTA oder Tracking auf einer konkreten Seite Nachfrage verlieren.',
			'use_case'     => 'Eine Seite wirtschaftlich priorisieren statt mehrere Symptome parallel zu behandeln.',
			'outcome'      => 'Schriftliche Rückmeldung in 48 Stunden mit stärkster Bremse, Priorität und nächstem sinnvollen Schritt.',
			'url'          => $audit_url,
			'cta_label'    => 'Audit starten',
			'schema_type'  => 'WebPage',
		],
		[
			'eyebrow'      => 'System',
			'title'        => 'WordPress Growth Operating System',
			'description'  => 'Wenn Sie nicht zuerst einen Einzelhebel, sondern das Betriebsmodell hinter Sichtbarkeit, Tracking und Conversion verstehen wollen.',
			'use_case'     => 'Die Gesamtlogik hinter Nachfrage, Datensignalen und Weiterentwicklung einordnen.',
			'outcome'      => 'Ein klarer Überblick über Systemlogik, Proof, Module und den Audit-Einstieg.',
			'url'          => $wgos_url,
			'cta_label'    => 'WGOS verstehen',
			'schema_type'  => 'WebPage',
		],
		[
			'eyebrow'      => 'Landkarte',
			'title'        => 'WGOS Systemlandkarte',
			'description'  => 'Wenn Sie Module, Assets und Reihenfolge des Systems schnell scannen und in eine feste Struktur einordnen wollen.',
			'use_case'     => 'Phasen, Module und konkrete Asset-Bausteine lesbar überblicken.',
			'outcome'      => 'Schneller Einstieg in die Arbeitslogik statt unverbundene Leistungslisten.',
			'url'          => $asset_hub_url,
			'cta_label'    => 'Systemlandkarte öffnen',
			'schema_type'  => 'WebPage',
		],
		[
			'eyebrow'      => 'Performance',
			'title'        => 'Core Web Vitals',
			'description'  => 'Wenn Ladezeit, mobile Reibung und technische Stabilität zuerst als echter Nachfragefaktor eingeordnet werden sollen.',
			'use_case'     => 'Performance-Probleme nicht nur messen, sondern gegen Conversion und Sichtbarkeit priorisieren.',
			'outcome'      => 'Ein sauberer Einstieg in technische Bremsen statt isolierter Score-Jagd.',
			'url'          => $core_web_url,
			'cta_label'    => 'Performance einordnen',
			'schema_type'  => 'WebPage',
		],
	];
}

/**
 * Render the versioned tools hub shell from the theme.
 *
 * @return string
 */
function nexus_get_tools_hub_shell_markup() {
	ob_start();
	get_template_part( 'template-parts/tools-page-shell' );

	return (string) ob_get_clean();
}

/**
 * Replace tools page content with the versioned shell as a fallback.
 *
 * @param string $content Rendered page content.
 * @return string
 */
function nexus_replace_tools_page_content_with_shell( $content ) {
	if ( is_admin() || ! function_exists( 'nexus_is_tools_page' ) || ! nexus_is_tools_page() || ! is_singular( 'page' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	return nexus_get_tools_hub_shell_markup();
}

add_filter( 'the_content', 'nexus_replace_tools_page_content_with_shell', 20 );
