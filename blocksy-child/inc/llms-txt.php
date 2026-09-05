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
 * Query arguments are preserved because the generic project route uses them to
 * distinguish a scoped project request from an unspecific contact visit.
 *
 * @param string $url Absolute public URL.
 * @return string
 */
function nexus_get_llms_txt_markdown_path( $url ) {
	$url      = (string) $url;
	$path     = wp_parse_url( $url, PHP_URL_PATH );
	$query    = wp_parse_url( $url, PHP_URL_QUERY );
	$fragment = wp_parse_url( $url, PHP_URL_FRAGMENT );
	$path     = is_string( $path ) && '' !== $path ? $path : '/';

	$markdown_path = '/' === $path ? '/' : trailingslashit( '/' . ltrim( $path, '/' ) );

	if ( is_string( $query ) && '' !== $query ) {
		$markdown_path .= '?' . $query;
	}

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

	$freelancer_url = $urls['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );
	$whitelabel_url = $urls['whitelabel'] ?? (
		function_exists( 'nexus_get_whitelabel_page_url' )
			? nexus_get_whitelabel_page_url()
			: home_url( '/whitelabel-retainer/' )
	);
	$project_url = function_exists( 'hu_get_navigation_project_request_url' )
		? hu_get_navigation_project_request_url()
		// Fallback muss denselben Focus tragen wie der eigene Fallback von
		// hu_get_navigation_project_request_url(); sonst veroeffentlicht der
		// Route-Index fuer AI-Agents einen anderen Intake-Kontext als die Site.
		: add_query_arg(
			[
				'type'  => 'project',
				'focus' => 'implementation_scope',
			],
			$urls['contact'] ?? home_url( '/kontakt/' )
		);

	return [
		[
			'heading' => 'Primäre Einstiege',
			'links'   => [
				[
					'label'       => 'Startseite',
					'url'         => $urls['home'] ?? home_url( '/' ),
					'description' => 'Fachliche Klammer und Verteiler auf direkte Projekte, White-Label und Solar/Wärmepumpe.',
				],
				[
					'label'       => 'WordPress Freelancer Hannover',
					'url'         => $freelancer_url,
					'description' => 'Direkter Einstieg für WordPress-Projekte mit Entwicklung, Tracking, Funnel/CRO und technischer SEO.',
				],
				[
					'label'       => 'Für Agenturen: White-Label',
					'url'         => $whitelabel_url,
					'description' => 'Umsetzungskapazität für Agenturen: WordPress, Tracking, CRO und technische SEO im Hintergrund.',
				],
				[
					'label'       => 'Solar- und Wärmepumpen-Leadgenerierung',
					'url'         => $urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' ),
					'description' => 'Spezialisierte Branchen-Landingpage für eigene Anfragesysteme statt Portal-Abhängigkeit.',
				],
				[
					'label'       => 'Projekt anfragen',
					'url'         => $project_url,
					'description' => 'Generischer Anfragepfad für direkte WordPress-, Tracking-, CRO- und technische SEO-Projekte.',
				],
				[
					'label'       => 'Marktcheck',
					'url'         => $urls['audit'] ?? home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ),
					'description' => 'Diagnostischer Einstieg ausschließlich für Solar-, Wärmepumpen- und Speicher-Intent.',
				],
			],
		],
		[
			'heading' => 'Fachseiten und kommerzielle Suchintents',
			'links'   => [
				[
					'label'       => 'Server-Side Tracking einrichten lassen',
					'url'         => $urls['solar_tracking'] ?? home_url( '/server-side-tracking-b2b/' ),
					'description' => 'Money Page für Server-Side Tracking, Server-GTM, GA4, Google Ads und Meta CAPI; direkter Projektpfad statt Solar-Marktcheck.',
				],
				[
					'label'       => 'WordPress Agentur Hannover',
					'url'         => $urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' ),
					'description' => 'Lokale SEO-Seite für den Agentur-Intent; direkte Zusammenarbeit wird zur Freelancer-Route weitergeführt.',
				],
				[
					'label'       => 'WordPress Freelancer Hannover',
					'url'         => $freelancer_url,
					'description' => 'Lokaler Freelancer-Intent für direkte Zusammenarbeit.',
				],
				[
					'label'       => 'White-Label für Agenturen',
					'url'         => $whitelabel_url,
					'description' => 'Agentur-/Partner-Intent mit Fit-Check und scoped Erstprojekt.',
				],
			],
		],
		[
			'heading' => 'Proof und zitierfähige Quellen',
			'links'   => [
				[
					'label'       => 'Ergebnisse',
					'url'         => $urls['results'] ?? home_url( '/ergebnisse/' ),
					'description' => 'Kuratierter Proof-Hub mit Cases, Kennzahlen und Einordnung.',
				],
				[
					'label'       => 'Solar Case Study',
					'url'         => $urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' ),
					'description' => 'Proof-Case für Nachfrageaufbau, Vorqualifizierung, Tracking und Conversion.',
				],
				[
					'label'       => 'Was kosten Solar-Leads? (Marktstudie)',
					'url'         => $urls['solar_leads_cost_study'] ?? home_url( '/solar-leads-kosten-studie/' ),
					'description' => 'Marktstudie zu Lead-Kosten im DACH-Raum: Preisspannen je Modell, Cost-per-Order, Methodik und Case-Study-Benchmark.',
				],
				[
					'label'       => 'Haşim Üner',
					'url'         => $urls['about'] ?? home_url( '/hasim-uener/' ),
					'description' => 'Personenprofil des Betreibers: Arbeitsweise und fachliche Kompetenz.',
				],
			],
		],
		[
			'heading' => 'Solar- und Wärmepumpen-Cluster',
			'links'   => [
				[
					'label'       => 'Photovoltaik & Solar Leads kaufen – Alternative',
					'url'         => $urls['solar_leads_alternative'] ?? home_url( '/solar-leads-kaufen-alternative/' ),
					'description' => 'Intercept-Page für Kauf-Suchintent: Lead-Anbieter einordnen und eigene Anfragesysteme als Alternative bewerten.',
				],
				// Aufnahmekriterium fuer die Provider-Markteinordnungen: nur die
				// Beitraege mit eigenem Entscheidungs- und Rechenlayer stehen hier.
				// Aroundhome und Checkfox tragen den CPO-Rechner auf Basis eigener
				// Betriebswerte und gelten auch in scripts/smoke-live.sh als
				// Money-Routen. Die uebrigen drei (Wattfox, DAA, Leadfluss) sind
				// Markteinordnungen ohne eigene Rechengrundlage und bleiben ueber
				// /blog/ und die Sitemap auffindbar — llms.txt ist ein Routen-Index,
				// kein Abzug des gesamten Bestands.
				[
					'label'       => 'Aroundhome Kosten für Handwerker',
					'url'         => home_url( '/aroundhome-solar-einordnung/' ),
					'description' => 'Kosten-, Vertrags- und Portal-Fit-Entscheid mit CPO-Rechner auf Basis eigener Betriebswerte.',
				],
				[
					'label'       => 'Checkfox für Solar- und Wärmepumpen-Betriebe',
					'url'         => home_url( '/checkfox-solar-waermepumpe-einordnung/' ),
					'description' => 'Portal-Mechanik, Seriositätsfrage und Kosten-Logik für Solar- und Wärmepumpen-Betriebe, mit CPO-Rechner auf Basis eigener Betriebswerte.',
				],
				[
					'label'       => 'Wärmepumpen Leads kaufen – Alternative',
					'url'         => home_url( '/waermepumpen-leads/' ),
					'description' => 'Intercept-Page für Wärmepumpen-Lead-Kauf und eigenes Anfragesystem als Alternative.',
				],
				[
					'label'       => 'Eigene Leadgenerierung vs. Portale',
					'url'         => $urls['solar_leads_tco'] ?? home_url( '/eigene-leadgenerierung-vs-portale/' ),
					'description' => 'Vergleich von Portal-Leads und eigenem Anfragesystem: Mieten vs. Besitzen, TCO und Datenbesitz.',
				],
				[
					'label'       => 'Cost per Lead Photovoltaik',
					'url'         => $urls['solar_cpl'] ?? home_url( '/cost-per-lead-photovoltaik/' ),
					'description' => 'CPL-Analyse mit Szenarienvergleich und versteckten Kostentreibern.',
				],
				[
					'label'       => 'Qualifizierte PV-Anfragen',
					'url'         => home_url( '/qualifizierte-pv-anfragen/' ),
					'description' => 'Merkmale hochwertiger Solar-Anfragen, Warnsignale und Messmethoden.',
				],
				[
					'label'       => 'Lead-Funnel Solar',
					'url'         => $urls['solar_funnel'] ?? home_url( '/lead-funnel-solar/' ),
					'description' => 'Funnel-Architektur von Erstkontakt bis Sales-Anschluss für Photovoltaik- und Wärmepumpen-Anbieter.',
				],
				[
					'label'       => 'B2B Solar Leads für Gewerbe',
					'url'         => home_url( '/b2b-solar-leads/' ),
					'description' => 'Anfragesysteme für gewerbliche Photovoltaik-Projekte mit Buying-Center-Funnel und langen Sales-Zyklen.',
				],
				[
					'label'       => 'Kunden gewinnen für Solarteure',
					'url'         => home_url( '/kunden-gewinnen-solarteure/' ),
					'description' => 'Pillar-Page mit systematischen Hebeln für Solar-Betriebe im DACH-Mittelstand.',
				],
			],
		],
		[
			'heading' => 'Wissen',
			'links'   => [
				[
					'label'       => 'Blog',
					'url'         => $urls['blog'] ?? home_url( '/blog/' ),
					'description' => 'Artikel zu SEO, Tracking, WordPress-Performance, Conversion und Anfragesystemen.',
				],
				[
					'label'       => 'Glossar',
					'url'         => $urls['glossary'] ?? home_url( '/glossar/' ),
					'description' => 'Begriffe und Definitionen für SEO, Tracking, CRO und Demand-Architektur.',
				],
				[
					'label'       => 'Stack Solar',
					'url'         => home_url( '/stack-solar/' ),
					'description' => 'Technischer Unterbau für Solar- und Wärmepumpen-Anbieter: Frontend, Hosting, Server-Side Tracking, CRM-Übergabe und Vorqualifizierung.',
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
	$urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];

	// Der Standort steht im Intro, weil ein Agent den Kopf dieser Datei liest,
	// bevor er eine einzelne Route aufloest: ohne Ortsangabe dort ist die
	// Entitaet fuer eine lokale Frage ("WordPress- und Tracking-Experte in
	// Hannover") nicht als lokal verfuegbar erkennbar. Die Routing-Aussage
	// (Freelancer / White-Label / Marktcheck) bleibt daneben stehen — sie
	// beantwortet die haeufigere Frage und darf nicht dafuer weichen.
	//
	// Bewusst nur Ort und Einzugsgebiet, keine Strasse und keine Telefonnummer:
	// die vollstaendige NAP-Angabe hat mit Impressum und Organization-Schema
	// bereits zwei gepflegte Orte. Ein dritter driftet, und eine abweichende
	// NAP-Schreibweise beschaedigt genau das lokale Vertrauenssignal, das
	// dieser Absatz aufbauen soll. Der Link zeigt deshalb auf die Quelle.
	$imprint_path = nexus_get_llms_txt_markdown_path( $urls['impressum'] ?? home_url( '/impressum/' ) );

	$lines = [
		'# Haşim Üner',
		'',
		'> WordPress, Tracking und Conversion als zusammenhängendes System — aus Pattensen bei Hannover (Region Hannover, Niedersachsen) für Kunden vor Ort und im DACH-Raum. Direkte Projekte laufen über den WordPress-Freelancer-/Projektpfad, Agenturen über White-Label. Solar, Wärmepumpe und Speicher bleiben eine spezialisierte Vertikale mit eigenem Marktcheck.',
		'',
		sprintf(
			'Standort: Pattensen bei Hannover, Niedersachsen (DE). Persönliche Termine, Workshops und Reviews sind in der Region Hannover, Hildesheim, Braunschweig, Wolfsburg und Celle möglich; die laufende Umsetzung erfolgt remote im DACH-Raum. Vollständige Anschrift und Kontaktdaten: [Impressum](%s).',
			$imprint_path
		),
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
