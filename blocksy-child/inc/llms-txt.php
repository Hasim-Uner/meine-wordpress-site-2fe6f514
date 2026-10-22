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

	$freelancer_url = $urls['freelancer'] ?? home_url( '/' );
	$whitelabel_url = $urls['whitelabel'] ?? (
		function_exists( 'nexus_get_whitelabel_page_url' )
			? nexus_get_whitelabel_page_url()
			: home_url( '/whitelabel-retainer/' )
	);
	$project_url = function_exists( 'hu_get_navigation_project_request_url' )
		? hu_get_navigation_project_request_url()
		// Fallback muss dieselbe Adresse tragen wie der eigene Fallback von
		// hu_get_navigation_project_request_url() (Typ ohne vorbelegtes Thema);
		// sonst veroeffentlicht der Route-Index einen anderen Intake-Kontext.
		: add_query_arg(
			[
				'type' => 'project',
			],
			$urls['contact'] ?? home_url( '/kontakt/' )
		);

	return [
		[
			'heading' => 'Primäre Einstiege',
			'links'   => [
				[
					'label'       => 'WordPress Freelancer Hannover',
					'url'         => $freelancer_url,
					'description' => 'Startseite und direkter Einstieg für WordPress-Websites, Relaunches, technisches SEO und Tracking bis ins CRM. Leistungen mit Preisen, ein dokumentierter B2B-Fall, öffentliche Referenzen und Projektanfrage.',
				],
				[
					'label'       => 'Für Agenturen: White-Label',
					'url'         => $whitelabel_url,
					'description' => 'Umsetzungskapazität für Agenturen: WordPress-Umsetzung, technisches SEO und die vollständige Messkette aus einer Hand.',
				],
				[
					'label'       => 'Anfragestrecke für Photovoltaik, Wärmepumpe und Speicher',
					'url'         => $urls['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' ),
					'description' => sprintf(
						'Money Page des Energie-Clusters. Definiert das eigene Anfragesystem, vergleicht Cost per Order mit Lead-Einkauf und führt zu zwei klaren Produkten: %1$s Marktcheck und konfigurierbares Anfragesystem ab %2$s netto; eine Produktstrecke ist enthalten, weitere Produktstrecken kosten jeweils %3$s zusätzlich. Das Sofortkontakt-Setup läuft über eine separate Kontaktstrecke. Enthält den dokumentierten Fall mit 150 € auf 22 € pro qualifizierter Anfrage.',
						hu_marketcheck_is_free() ? 'kostenloser' : 'bezahlter (' . hu_marketcheck_price( true ) . ')',
						hu_format_eur( HU_FOUNDATION_PRICE_STANDARD ),
						hu_format_eur( HU_FOUNDATION_EXTRA_PRODUCT_PRICE )
					),
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
                    'label'       => 'Technisches SEO für WordPress',
                    'url'         => hu_get_commercial_route( 'technical_seo' ),
                    'description' => 'Technischer SEO-Befund und separat vereinbarte Korrekturen für bestehende WordPress-Websites: Indexierung, Vorlagen und Ladezeit. Prüfumfang, Preis und Abnahme vor dem Start; keine Rankinggarantie.',
                ],
				[
					'label'       => 'WordPress-Übernahme und Weiterentwicklung',
					'url'         => hu_get_commercial_route( 'takeover' ),
					'description' => 'Bestehende WordPress-Websites prüfen und gezielt weiterentwickeln: bezahlter Übernahme-Check mit schriftlichem Befund und Festpreis für den nächsten Schritt. Bei Beauftragung des Folgeprojekts verrechnet. Danach Einzelprojekt oder optionales Monatskontingent, ohne Bereitschaftsdienst.',
				],
				[
					'label'       => 'Landingpage erstellen lassen',
					'url'         => $urls['landingpage'] ?? home_url( '/landingpage-erstellen-lassen/' ),
					'description' => 'Festpreis-Angebot für Direktkunden: eine WordPress-Seite für ein Angebot mit Konzept, Text, Anfrageformular und Abnahme. Einstiegsseite und verfügbare Kampagnenparameter werden mitgegeben; die vereinbarte Seiten-Conversion wird in vorhandenen Konten eingerichtet. Ein vollständiges Tracking-Setup ist ein separates Produkt.',
				],
				[
					'label'       => 'WordPress-Website erstellen lassen',
					'url'         => $urls['website'] ?? home_url( '/wordpress-website-erstellen-lassen/' ),
					'description' => 'Die Anfrage-Website für Unternehmen und Selbstständige: Custom Code ohne Pagebuilder, technisches SEO, geprüfte Anfrage-Strecke und Basis-Daten-Cockpit mit Search Console und gespeicherten Formularanfragen als Grundsystem. Der Konfigurator unterscheidet kurze Seiten, Standardseiten und Leistungs-/Verkaufsseiten; Texterstellung ist optional je Seitentyp. Neubau oder Relaunch, optionale Screendesign-, Conversion-Tracking- und Standard-CRM-Erweiterungen; individuelles Daten-Dashboard mit zusätzlichen Quellen nach Angebot. Einmalpreis und Produktionszeit im Konfigurator, Übergabe auf Ihren Namen.',
				],
				[
					'label'       => 'Conversion-Optimierung für B2B',
					'url'         => $urls['conversion'] ?? home_url( '/conversion-optimierung/' ),
					'description' => 'Conversion-Optimierung für B2B-Websites mit wenig Traffic: schriftlicher Befund der Strecke vom Besuch bis zur Rückmeldung im Vertrieb, danach Umsetzung zu festen Preisen. Einstieg ist die Anfragesystem-Analyse, ohne A/B-Tests.',
				],
				[
					'label'       => 'Conversion Tracking einrichten lassen',
					'url'         => $urls['tracking'] ?? home_url( '/ga4-tracking-setup/' ),
					'description' => 'Das Tracking-Angebot: GA4, Google Tag Manager, Consent Mode und Google Ads als abgegrenztes Setup mit Messplan und Abnahmeprotokoll. Server-Side, Meta CAPI und CRM kommen nur nach technischem Bedarf dazu.',
				],
				[
					'label'       => 'Server-Side Tracking einrichten lassen',
					'url'         => $urls['solar_tracking'] ?? home_url( '/server-side-tracking-b2b/' ),
					'description' => 'Fachseite für eine Server-Messstrecke mit Browser-Messbasis, Server-GTM, eigener Subdomain, Enhanced Conversions und Paralleltest. Meta CAPI und CRM-Rücksignale werden separat vereinbart; Hostingkosten und optionale Betreuung stehen vorab fest. Direkter Tracking-Projektpfad.',
				],
				[
					'label'       => 'Performance Marketing für B2B',
					'url'         => $urls['performance_marketing'] ?? home_url( '/performance-marketing/' ),
					'description' => 'Bezahlte Nachfrage über Google Ads und Meta in fester Reihenfolge: erst Conversion-Messung, dann Zielseite, dann Budget. Performance-Agenturen werden zu White-Label weitergeführt.',
				],
				[
					'label'       => 'WordPress Agentur Hannover',
					'url'         => $urls['agentur'] ?? home_url( '/wordpress-agentur-hannover/' ),
					'description' => 'Lokale Entscheidungsseite für den Agentur-Intent: klassische Agentur oder direkte Verantwortung; führt passend zu Website, Tracking und Conversion weiter.',
				],
				[
					'label'       => 'White-Label für Agenturen',
					'url'         => $whitelabel_url,
					'description' => 'Agentur-/Partner-Intent: Aufgabe beschreiben, Erstprojekt mit fixem Scope, Retainer erst danach.',
				],
			],
		],
		[
			'heading' => 'Proof und zitierfähige Quellen',
			// Projektportraets und anonymisierte Zahlenfallstudie bleiben getrennt.
			'links'   => [
				[
					'label'       => 'WordPress-Referenzen und Projekte',
					'url'         => hu_get_commercial_route( 'references' ),
					'description' => 'Kompakte Projektfälle mit Ausgangslage, eigenem Beitrag und öffentlich prüfbarer Website. Die namentlichen Projektporträts enthalten keine vertraulichen Kennzahlen.',
				],
				[
					'label'       => 'Solar Case Study',
					'url'         => $urls['e3'] ?? home_url( '/case-study-solar-leadgenerierung/' ),
					'description' => 'Proof-Case für Nachfrageaufbau, Vorqualifizierung, Tracking und Conversion.',
				],
				[
					'label'       => 'Solar-Leads-Kosten: CPL und Kosten pro Auftrag',
					'url'         => $urls['solar_leads_cost_study'] ?? home_url( '/solar-leads-kosten-studie/' ),
					'description' => 'Kostenleitfaden für Photovoltaik- und Wärmepumpen-Anfragen: CPL, Vertriebszeit, Abschlussquote und Berechnung mit eigenen Zahlen. Keine pauschalen Anbieterpreise oder Amortisationszusage.',
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
					'description' => 'Portal-Mechanik, Vertragsprüfung und Kosten-Logik für Solar- und Wärmepumpen-Betriebe, mit CPO-Rechner auf Basis eigener Betriebswerte.',
				],
				[
					'label'       => 'Wärmepumpen Leads kaufen – Alternative',
					'url'         => home_url( '/waermepumpen-leads/' ),
					'description' => 'Intercept-Page für Wärmepumpen-Lead-Kauf und eigenes Anfragesystem als Alternative.',
				],
				[
					'label'       => 'B2B Solar Leads für Gewerbe',
					'url'         => home_url( '/b2b-solar-leads/' ),
					'description' => 'Anfragesysteme für gewerbliche Photovoltaik-Projekte mit Buying-Center-Funnel und langen Sales-Zyklen.',
				],
			],
		],
		[
			'heading' => 'Wissen',
			'links'   => [
				[
					'label'       => 'Blog',
					'url'         => $urls['blog'] ?? home_url( '/blog/' ),
					'description' => 'Artikel zu WordPress, technischem SEO, Tracking, Conversion und Leadökonomie.',
				],
				[
					'label'       => 'Glossar',
					'url'         => $urls['glossary'] ?? home_url( '/glossar/' ),
					'description' => '60 kurze Erklärungen mit Beispielen: jeweils zwölf zu Anfragen, Ladezeit & Technik, Tracking, SEO und Conversion.',
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
		'> WordPress-Entwickler für Websites, Landingpages, Tracking und Conversion — aus Pattensen bei Hannover (Region Hannover, Niedersachsen) für Kunden vor Ort und im DACH-Raum. Direkte Projekte laufen über den WordPress-Freelancer-/Projektpfad, Agenturen über White-Label. Solar, Wärmepumpe und Speicher bleiben eine spezialisierte Vertikale mit eigenem Marktcheck.',
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
