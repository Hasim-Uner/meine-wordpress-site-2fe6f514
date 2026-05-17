<?php
/**
 * Dynamic llms.txt route for AI agents and citation-oriented crawlers.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the canonical request path for llms.txt.
 *
 * @return string
 */
function nexus_get_llms_txt_request_path() {
	return trailingslashit( '/llms.txt' );
}

/**
 * Check whether the current request targets llms.txt.
 *
 * @return bool
 */
function nexus_is_llms_txt_request() {
	return nexus_get_current_request_path() === nexus_get_llms_txt_request_path();
}

/**
 * Normalize a public URL into the markdown path used inside llms.txt.
 *
 * @param string $url Absolute public URL.
 * @return string
 */
function nexus_get_llms_txt_markdown_path( $url ) {
	$path = wp_parse_url( (string) $url, PHP_URL_PATH );
	$path = is_string( $path ) && '' !== $path ? $path : '/';

	return '/' === $path ? '/' : trailingslashit( '/' . ltrim( $path, '/' ) );
}

/**
 * Build the structured sections for llms.txt from the primary public URL map.
 *
 * @return array<int, array<string, mixed>>
 */
function nexus_get_llms_txt_sections() {
	$urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];

	return [
		[
			'heading' => 'Primäre Einstiege',
			'links'   => [
				[
					'label'       => 'Startseite',
					'url'         => $urls['home'] ?? home_url( '/' ),
					'description' => 'Überblick über Positionierung, Proof und primäre Einstiege.',
				],
				[
					'label'       => 'Marktcheck',
					'url'         => $urls['audit'] ?? home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ),
					'description' => '60-Sekunden-Einstieg für Solar-, Wärmepumpen- und Speicher-Anbieter: Website, Tracking und Anfrageprozess einordnen.',
				],
				[
					'label'       => 'WordPress Agentur Hannover',
					'url'         => $urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' ),
					'description' => 'Lokale B2B-Service-Seite für WordPress, Leadgenerierung und strukturierte Weiterentwicklung.',
				],
				[
					'label'       => 'WordPress Growth Operating System',
					'url'         => $urls['wgos'] ?? home_url( '/wordpress-agentur-hannover/#wgos' ),
					'description' => 'Angebots- und Systemseite für Audit, Blueprint und Umsetzung.',
				],
			],
		],
		[
			'heading' => 'Service-Cluster',
			'links'   => [
				[
					'label'       => 'WordPress SEO Hannover',
					'url'         => $urls['seo'] ?? home_url( '/wordpress-agentur-hannover/#technisches-seo' ),
					'description' => 'Technical SEO Audit, Informationsarchitektur und Suchnachfrage für B2B-Seiten.',
				],
				[
					'label'       => 'GA4 Tracking Setup',
					'url'         => $urls['tracking'] ?? home_url( '/ga4-tracking-setup/' ),
					'description' => 'GA4, GTM und serverseitiges Tracking für saubere Messbarkeit.',
				],
				[
					'label'       => 'Conversion Rate Optimierung',
					'url'         => $urls['cro'] ?? home_url( '/wordpress-agentur-hannover/#wgos' ),
					'description' => 'CRO für WordPress mit Fokus auf Leads, Reibung und Formularpfade.',
				],
				[
					'label'       => 'Core Web Vitals',
					'url'         => $urls['cwv'] ?? home_url( '/wgos-assets/cwv-optimierung/' ),
					'description' => 'Performance-Analyse für langsame WordPress-Seiten und Web-Vitals-Probleme.',
				],
			],
		],
		[
			'heading' => 'Proof und Relevanz',
			'links'   => [
				[
					'label'       => 'Ergebnisse',
					'url'         => $urls['results'] ?? home_url( '/ergebnisse/' ),
					'description' => 'Kuratierter Proof-Hub mit Cases, Kennzahlen und Einordnung.',
				],
				[
					'label'       => 'E3 New Energy',
					'url'         => $urls['e3'] ?? home_url( '/e3-new-energy/' ),
					'description' => 'B2B-Case für Nachfrageaufbau, Tracking und Conversion-Verbesserung.',
				],
				[
					'label'       => 'Leadgenerierung für Energie-Systeme',
					'url'         => $urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' ),
					'description' => 'Branchen-Landingpage für Solar-, Wärmepumpen- und Speicher-Anbieter.',
				],
			],
		],
		[
			'heading' => 'Wissen und Kontakt',
			'links'   => [
				[
					'label'       => 'Blog',
					'url'         => $urls['blog'] ?? home_url( '/blog/' ),
					'description' => 'Artikel zu SEO, Tracking, WordPress-Performance und B2B-Wachstum.',
				],
				[
					'label'       => 'Glossar',
					'url'         => $urls['glossary'] ?? home_url( '/glossar/' ),
					'description' => 'Begriffe und Definitionen für SEO, Tracking, CRO und Demand-Architektur.',
				],
				[
					'label'       => 'Über mich',
					'url'         => $urls['about'] ?? home_url( '/uber-mich/' ),
					'description' => 'Personenprofil von Haşim Üner als Architekt für Anfrage-Systeme und Autor der Website.',
				],
				[
					'label'       => 'Kontakt',
					'url'         => $urls['contact'] ?? home_url( '/kontakt/' ),
					'description' => 'Direkter Kontakt für Audit, Folgeanalyse oder Umsetzung.',
				],
			],
		],
		[
			'heading' => 'Solar- und Wärmepumpen-Leadgenerierung: Themen-Cluster',
			'links'   => [
				[
					'label'       => 'Solar Leads kaufen – Alternative',
					'url'         => home_url( '/solar-leads-kaufen-alternative/' ),
					'description' => 'Intercept-Page für den Kauf-Suchintent: Markteinordnung der Lead-Anbieter und Argumentation für eigene Anfrage-Systeme.',
				],
				[
					'label'       => 'Eigene Leadgenerierung vs. Portale',
					'url'         => home_url( '/eigene-leadgenerierung-vs-portale/' ),
					'description' => 'Vergleichsmatrix Mieten vs. Besitzen mit TCO-Überschlag über 24/36 Monate und 8 Kriterien.',
				],
				[
					'label'       => 'Cost per Lead Photovoltaik',
					'url'         => home_url( '/cost-per-lead-photovoltaik/' ),
					'description' => 'CPL-Analyse mit drei Szenarien-Vergleich (Portal-Standard, Portal-Exklusiv, Eigenes System) und versteckten Kostentreibern.',
				],
				[
					'label'       => 'Qualifizierte PV-Anfragen',
					'url'         => home_url( '/qualifizierte-pv-anfragen/' ),
					'description' => 'Vier-Merkmale-Modell für hochwertige Solar-Anfragen mit Warnsignalen und Messmethoden.',
				],
				[
					'label'       => 'Lead-Funnel Solar (Pillar)',
					'url'         => home_url( '/lead-funnel-solar/' ),
					'description' => 'Fünf-Stufen-Funnel-Architektur von TOFU bis Sales-Anschluss für Photovoltaik- und Wärmepumpen-Anbieter.',
				],
				[
					'label'       => 'Server-Side Tracking für B2B',
					'url'         => home_url( '/server-side-tracking-b2b/' ),
					'description' => 'GA4, Meta CAPI und Consent Mode v2 auf eigenem Server in Frankfurt – DSGVO-konforme Tracking-Architektur.',
				],
				[
					'label'       => 'B2B Solar Leads für Gewerbe',
					'url'         => home_url( '/b2b-solar-leads/' ),
					'description' => 'Anfrage-Systeme für gewerbliche Photovoltaik-Projekte mit Buying-Center-Funnel und langen Sales-Zyklen.',
				],
				[
					'label'       => 'Kunden gewinnen für Solarteure',
					'url'         => home_url( '/kunden-gewinnen-solarteure/' ),
					'description' => 'Pillar-Page mit Mythen-Aufklärung und fünf systematischen Hebeln für Solar-Betriebe im DACH-Mittelstand.',
				],
			],
		],
	];
}

/**
 * Build the markdown response for llms.txt from the primary public URL map.
 *
 * @return string
 */
function nexus_get_llms_txt_content() {
	$lines = [
		'# Haşim Üner',
		'',
		'> Architekt für eigene Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter im DACH-Raum. Ablösung von Portal-Abhängigkeit durch Website, Tracking, Vorqualifizierung und Werbekanal-Steuerung als ein verbundenes System. Primärer Einstieg ist der Marktcheck auf der Solar- und Wärmepumpen-Seite.',
	];

	foreach ( nexus_get_llms_txt_sections() as $section ) {
		$lines[] = '';
		$lines[] = '## ' . (string) $section['heading'];

		foreach ( (array) $section['links'] as $link ) {
			$lines[] = sprintf(
				'- [%1$s](%2$s) - %3$s',
				(string) ( $link['label'] ?? '' ),
				nexus_get_llms_txt_markdown_path( (string) ( $link['url'] ?? home_url( '/' ) ) ),
				(string) ( $link['description'] ?? '' )
			);
		}
	}

	return implode( "\n", $lines ) . "\n";
}

/**
 * Prevent canonical redirects from interfering with llms.txt.
 *
 * @param string|false $redirect_url Redirect target.
 * @return string|false
 */
function nexus_disable_canonical_redirect_for_llms_txt( $redirect_url ) {
	if ( nexus_is_llms_txt_request() ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'nexus_disable_canonical_redirect_for_llms_txt' );

/**
 * Render the llms.txt payload directly from WordPress.
 *
 * @return void
 */
function nexus_render_llms_txt() {
	if ( is_admin() || wp_doing_ajax() || ! nexus_is_llms_txt_request() ) {
		return;
	}

	nocache_headers();
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
	echo nexus_get_llms_txt_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'template_redirect', 'nexus_render_llms_txt', 0 );
