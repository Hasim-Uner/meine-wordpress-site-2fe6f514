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
	$url      = (string) $url;
	$path     = wp_parse_url( $url, PHP_URL_PATH );
	$fragment = wp_parse_url( $url, PHP_URL_FRAGMENT );
	$path     = is_string( $path ) && '' !== $path ? $path : '/';

	$markdown_path = '/' === $path ? '/' : trailingslashit( '/' . ltrim( $path, '/' ) );

	if ( is_string( $fragment ) && '' !== $fragment ) {
		$markdown_path .= '#' . ltrim( $fragment, '#' );
	}

	return $markdown_path;
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
					'label'       => 'Solar- und Wärmepumpen-Leadgenerierung',
					'url'         => $urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' ),
					'description' => 'Branchen-Landingpage für eigene Anfrage-Systeme gegen Portal-Abhängigkeit: Website, Vorqualifizierung, Tracking und steuerbare Werbekanäle.',
				],
				[
					'label'       => 'Marktcheck',
					'url'         => $urls['audit'] ?? home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ),
					'description' => 'Primärer Einstieg für kalten Solar-/SHK-Traffic: händische Einordnung von Region, Anfragebremsen, Datenlage und nächstem sinnvollen Schritt.',
				],
				[
					'label'       => 'Kontakt',
					'url'         => $urls['contact'] ?? home_url( '/kontakt/' ),
					'description' => 'Direkter Kontakt für Analyse, Umsetzung und Weiterentwicklung nach Marktcheck-Fit.',
				],
			],
		],
		[
			'heading' => 'Proof und zitierfähige Quellen',
			'links'   => [
				[
					'label'       => 'Solar Case Study',
					'url'         => $urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' ),
					'description' => 'Kaufnaher Proof-Case: eigenes Anfrage-System, Vorqualifizierung, Tracking und Conversion statt Portal-Lead-Abhängigkeit.',
				],
				[
					'label'       => 'Was kosten Solar-Leads? (Marktstudie)',
					'url'         => $urls['solar_leads_cost_study'] ?? home_url( '/solar-leads-kosten-studie/' ),
					'description' => 'Zitierfähige Marktstudie zu Lead-Kosten im DACH-Raum: Preisspannen je Modell, Cost-per-Order, Methodik und Case-Study-Benchmark.',
				],
				[
					'label'       => 'Ergebnisse',
					'url'         => $urls['results'] ?? home_url( '/ergebnisse/' ),
					'description' => 'Kuratierter Proof-Hub mit Cases, Kennzahlen und Einordnung.',
				],
				[
					'label'       => 'Über Haşim Üner',
					'url'         => $urls['about'] ?? home_url( '/uber-mich/' ),
					'description' => 'Personenprofil des Autors und Betreibers: Architekt für eigene Anfrage-Systeme.',
				],
			],
		],
		[
			'heading' => 'Solar- und Wärmepumpen-Leadgenerierung: Themen-Cluster',
			'links'   => [
				[
					'label'       => 'Photovoltaik & Solar Leads kaufen – Alternative',
					'url'         => $urls['solar_leads_alternative'] ?? home_url( '/solar-leads-kaufen-alternative/' ),
					'description' => 'Intercept-Page für Kauf-Suchintent: Lead-Anbieter einordnen und eigene Anfrage-Systeme als Alternative bewerten.',
				],
				[
					'label'       => 'Wärmepumpen Leads kaufen – Alternative',
					'url'         => home_url( '/waermepumpen-leads/' ),
					'description' => 'Intercept-Page für Wärmepumpen-Lead-Kauf: Marktmodelle, wärmepumpen-spezifische Vorqualifizierung und eigenes Anfrage-System als Alternative.',
				],
				[
					'label'       => 'Eigene Leadgenerierung vs. Portale',
					'url'         => $urls['solar_leads_tco'] ?? home_url( '/eigene-leadgenerierung-vs-portale/' ),
					'description' => 'Vergleich von Portal-Leads und eigenem Anfrage-System: Mieten vs. Besitzen, TCO und Datenbesitz.',
				],
				[
					'label'       => 'Cost per Lead Photovoltaik',
					'url'         => $urls['solar_cpl'] ?? home_url( '/cost-per-lead-photovoltaik/' ),
					'description' => 'CPL-Analyse mit Szenarienvergleich, versteckten Kostentreibern und Marktcheck-Anschluss.',
				],
				[
					'label'       => 'Qualifizierte PV-Anfragen',
					'url'         => home_url( '/qualifizierte-pv-anfragen/' ),
					'description' => 'Vier-Merkmale-Modell für hochwertige Solar-Anfragen mit Warnsignalen und Messmethoden.',
				],
				[
					'label'       => 'Lead-Funnel Solar',
					'url'         => $urls['solar_funnel'] ?? home_url( '/lead-funnel-solar/' ),
					'description' => 'Funnel-Architektur von Erstkontakt bis Sales-Anschluss für Photovoltaik- und Wärmepumpen-Anbieter.',
				],
				[
					'label'       => 'Server-Side Tracking für B2B',
					'url'         => $urls['solar_tracking'] ?? home_url( '/server-side-tracking-b2b/' ),
					'description' => 'GA4, Meta CAPI und Consent Mode v2 auf eigenem Server als Messfundament für Anfrage-Systeme.',
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
		[
			'heading' => 'Sekundäre Einstiege und Wissen',
			'links'   => [
				[
					'label'       => 'Blog',
					'url'         => $urls['blog'] ?? home_url( '/blog/' ),
					'description' => 'Artikel zu SEO, Tracking, WordPress-Performance und Anfrage-Systemen.',
				],
				[
					'label'       => 'Glossar',
					'url'         => $urls['glossary'] ?? home_url( '/glossar/' ),
					'description' => 'Begriffe und Definitionen für SEO, Tracking, CRO und Demand-Architektur.',
				],
				[
					'label'       => 'Stack Solar',
					'url'         => home_url( '/stack-solar/' ),
					'description' => 'Performance-Stack für Solar-/SHK-Anbieter: Frontend, Managed Hosting, Server-Side Tracking, CRM-Anbindung und Marktcheck-Vorqualifizierung.',
				],
				[
					'label'       => 'WordPress Agentur Hannover',
					'url'         => $urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' ),
					'description' => 'Sekundäre lokale B2B-Seite für WordPress-Systeme, technisches SEO, Tracking und CRO in Hannover.',
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
