<?php
/**
 * Funnel doors: one decision for header and footer.
 *
 * Der Kopf zeigt dem Leser genau eine Tuer, der Fuss fuehrt alle Tueren als
 * Register. Beide lesen dieselbe Entscheidung, damit sie nie auseinander-
 * laufen: hu_funnel_doors() kennt die sechs Tueren, hu_funnel_context()
 * entscheidet pro Anfrage Modus, Tuer und Weg.
 *
 * Betraege kommen ausschliesslich aus dem Kanon (inc/canon/pricing-canon.php).
 * Ein "ab"-Betrag steht nur an einer Tuer mit einem Produkt und ist die echte
 * Untergrenze dessen, was dahinter liegt. Die Tuer "Projekt anfragen" buendelt
 * Website, Landingpage, Relaunch und Optimierung und traegt deshalb keinen
 * Betrag. Tracking beginnt bei der Messung, nicht beim Basis-Paket.
 *
 * Ziele stehen in der Routen-Karte (inc/commercial-routing.php). Dieses Modul
 * haengt nur Anker an. Die Tracking-Actions des Kopfs sind eigene Werte
 * (`nav_header_door_*`); `nav_header_project` bleibt unveraendert.
 *
 * Doku: docs/architecture/CONVERSION_ROUTING.md (Header, Footer).
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Append an anchor to the Solar/Waermepumpe route.
 *
 * @param string $fragment Anchor without the hash.
 * @return string
 */
function hu_funnel_energy_anchor( $fragment ) {
	$routes = hu_get_commercial_route_map();
	$base   = (string) preg_replace( '/#.*$/', '', (string) ( $routes['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' ) ) );

	return trailingslashit( $base ) . '#' . sanitize_key( (string) $fragment );
}

/**
 * Resolve the anchor of a Solar door on the Solar page.
 *
 * Aus HU_FEATURE_SOLAR_DOORS: an heisst der Anker der Tuer selbst, aus
 * (Vorgabe) der Einstieg der Angebotsleiter, den es heute schon gibt.
 *
 * @param string $own_anchor Anchor of the door itself (analyse, sofortkontakt).
 * @return string
 */
function hu_funnel_solar_door_anchor( $own_anchor ) {
	$enabled = defined( 'HU_FEATURE_SOLAR_DOORS' ) && (bool) constant( 'HU_FEATURE_SOLAR_DOORS' );

	return $enabled ? sanitize_key( (string) $own_anchor ) : 'einstieg';
}

/**
 * Return the six funnel doors, keyed by door key.
 *
 * Schluessel: projekt, tracking, aufgabe (White-Label), marktcheck, analyse,
 * sofort (Sofortkontakt). `data-door` und `cta_footer_door_<schluessel>` tragen
 * diese Schluessel; `track` ist die Action im Kopf.
 *
 * Solange HU_FEATURE_SOLAR_DOORS aus ist, zeigen `analyse` und `sofort` auf
 * den Einstieg der Angebotsleiter (#einstieg). Die Anker #analyse und
 * #sofortkontakt entstehen erst mit der Solar-Strecke.
 *
 * `tier`: `scope` (Umfang offen, kein Betrag), `free` (kostenlos) oder `paid`.
 *
 * @return array<string, array<string, string>>
 */
function hu_funnel_doors() {
	$routes       = hu_get_commercial_route_map();
	$analysis_url = hu_funnel_energy_anchor( hu_funnel_solar_door_anchor( 'analyse' ) );
	$instant_url  = hu_funnel_energy_anchor( hu_funnel_solar_door_anchor( 'sofortkontakt' ) );

	return [
		'projekt'    => [
			'key'          => 'projekt',
			'label'        => __( 'Projekt anfragen', 'blocksy-child' ),
			'short'        => __( 'Projekt', 'blocksy-child' ),
			'footer_label' => __( 'Projekt anfragen', 'blocksy-child' ),
			'amount'       => '',
			'tier'         => 'scope',
			'url'          => (string) $routes['project_request'],
			'track'        => 'nav_header_project',
		],
		'tracking'   => [
			'key'          => 'tracking',
			'label'        => __( 'Tracking anfragen', 'blocksy-child' ),
			'short'        => __( 'Tracking', 'blocksy-child' ),
			'footer_label' => __( 'Tracking-Projekt anfragen', 'blocksy-child' ),
			'amount'       => 'ab ' . hu_tracking_price( 'measurement', 'setup' ),
			'tier'         => 'paid',
			'url'          => hu_get_contact_intake_url( 'project', 'tracking' ),
			'track'        => 'nav_header_door_tracking',
		],
		'aufgabe'    => [
			'key'          => 'aufgabe',
			'label'        => __( 'Test-Sprint anfragen', 'blocksy-child' ),
			'short'        => __( 'Test-Sprint', 'blocksy-child' ),
			'footer_label' => __( 'Test-Sprint anfragen', 'blocksy-child' ),
			'amount'       => hu_format_eur( HU_WHITELABEL_TEST_SPRINT_PRICE ),
			'tier'         => 'paid',
			'url'          => trailingslashit( (string) preg_replace( '/#.*$/', '', (string) $routes['whitelabel'] ) ) . '#aufgabe',
			'track'        => 'nav_header_door_whitelabel',
		],
		'marktcheck' => [
			'key'          => 'marktcheck',
			'label'        => HU_REQUEST_ANALYSIS_LABEL,
			'short'        => HU_REQUEST_ANALYSIS_LABEL,
			'footer_label' => sprintf(
				/* translators: %s: Marktcheck label. */
				__( '%s, regional', 'blocksy-child' ),
				HU_REQUEST_ANALYSIS_LABEL
			),
			'amount'       => hu_format_eur( 0 ),
			'tier'         => 'free',
			'url'          => (string) $routes['marketcheck'],
			'track'        => 'nav_header_door_marktcheck',
		],
		'analyse'    => [
			'key'          => 'analyse',
			'label'        => __( 'Analyse anfragen', 'blocksy-child' ),
			'short'        => __( 'Analyse', 'blocksy-child' ),
			'footer_label' => __( 'Anfragesystem-Analyse', 'blocksy-child' ),
			'amount'       => hu_analysis_price(),
			'tier'         => 'paid',
			'url'          => $analysis_url,
			'track'        => 'nav_header_door_analyse',
		],
		'sofort'     => [
			'key'          => 'sofort',
			'label'        => __( 'Sofortkontakt', 'blocksy-child' ),
			'short'        => __( 'Sofortkontakt', 'blocksy-child' ),
			'footer_label' => __( 'Sofortkontakt-Setup', 'blocksy-child' ),
			'amount'       => hu_entry_setup_price(),
			'tier'         => 'paid',
			'url'          => $instant_url,
			'track'        => 'nav_header_door_sofortkontakt',
		],
	];
}

/**
 * Return the slugs of the portal assessments (Checkfox, Aroundhome, Wattfox, DAA).
 *
 * Wer eine dieser Einordnungen liest, kauft heute Portal-Anfragen: der Kopf
 * zeigt dort den Sofortkontakt statt des Marktchecks.
 *
 * @return array<int, string>
 */
function hu_funnel_portal_post_slugs() {
	return [
		'checkfox-solar-waermepumpe-einordnung',
		'aroundhome-solar-einordnung',
		'wattfox-solar-leads-einordnung',
		'daa-photovoltaik-leads-einordnung',
	];
}

/**
 * Resolve the dossier of an article: slug, label and archive URL.
 *
 * Eine Funktion fuer Lesemodus der Leiste und Tuerentscheidung. Reihenfolge:
 * feste Zuordnung je Beitrag, dann die erste passende Kategorie in der
 * Prioritaet unten, sonst "leadgenerierung" als Beschriftung des Pfads.
 *
 * `matched` sagt, ob die Zuordnung belegt ist (Zuordnung je Beitrag oder
 * Kategorie). Der Rueckfall auf "leadgenerierung" beschriftet nur den Pfad;
 * eine Tuer entscheidet er nicht: ein Beitrag ohne Dossier-Kategorie bekommt
 * keine Energie-Tuer.
 *
 * @param string $post_slug Post slug.
 * @return array{slug: string, label: string, url: string, matched: bool}
 */
function hu_funnel_reader_dossier( $post_slug = '' ) {
	$post_slug    = sanitize_title( (string) $post_slug );
	$primary_urls = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];
	$blog_url     = $primary_urls['blog'] ?? home_url( '/blog/' );

	$priority = [
		'wordpress-performance',
		'tracking',
		'cro',
		'leadgenerierung',
	];
	$labels   = [
		'leadgenerierung'       => 'Eigene Anfragen & Leadökonomie',
		'wordpress-performance' => 'WordPress & Performance',
		'tracking'              => 'Tracking & Messbarkeit',
		'cro'                   => 'Conversion & Anfragearchitektur',
	];
	$overrides = [
		'aroundhome-solar-einordnung'           => 'leadgenerierung',
		'checkfox-solar-waermepumpe-einordnung' => 'leadgenerierung',
		'wattfox-solar-leads-einordnung'        => 'leadgenerierung',
		'wordpress-ttfb-google-ads-ladezeit'    => 'wordpress-performance',
		'server-side-tracking-gtm'              => 'tracking',
		'b2b-landingpage-optimieren'            => 'cro',
		'wordpress-seo-keine-anfragen'          => 'cro',
	];

	if ( function_exists( 'hu_get_positioned_blog_dossier_taxonomy' ) ) {
		foreach ( (array) hu_get_positioned_blog_dossier_taxonomy() as $key => $data ) {
			if ( ! empty( $data['name'] ) ) {
				$labels[ (string) $key ] = (string) $data['name'];
			}
		}
	}

	$categories = function_exists( 'get_the_category' ) ? get_the_category() : [];
	$cat_slugs  = ! empty( $categories ) && ! is_wp_error( $categories )
		? array_map( 'strval', wp_list_pluck( $categories, 'slug' ) )
		: [];
	$slug       = $overrides[ $post_slug ] ?? '';

	if ( '' === $slug ) {
		foreach ( $priority as $candidate ) {
			if ( in_array( $candidate, $cat_slugs, true ) ) {
				$slug = $candidate;
				break;
			}
		}
	}

	$matched = '' !== $slug;

	if ( ! $matched ) {
		$slug = 'leadgenerierung';
	}

	return [
		'slug'    => $slug,
		'label'   => $labels[ $slug ] ?? 'Werkstatt',
		'url'     => function_exists( 'nexus_get_category_url' ) ? (string) nexus_get_category_url( $slug, $blog_url ) : (string) $blog_url,
		'matched' => $matched,
	];
}

/**
 * Templates, deren Seite im Fuss kein Tuerregister ("Welcher Weg passt?") traegt.
 *
 * Eine Money Page mit genau einem Angebot und einem eigenen Abschluss braucht
 * keine zweite Auswahl unter ihrem Abschluss. Wer eine weitere Seite aus dem
 * Register nehmen will, traegt hier ihre Template-Datei ein; der Fuss selbst
 * bleibt unveraendert. /kontakt/ steht nicht in dieser Liste, hu_footer_shows_register()
 * behandelt sie eigens.
 *
 * @return string[]
 */
function hu_footer_register_suppressed_templates() {
	return [
		'page-conversion-optimierung.php',
	];
}

/**
 * Whether the footer renders the door register on the current request.
 *
 * Das Register steht auf jeder Seite ausser der Kontaktseite (sie ist das Ziel
 * jeder Tuer, eine erneute Auswahl wuerde aus dem Abschluss einen Ausgang
 * zurueck in die Orientierung machen) und den Seiten aus
 * hu_footer_register_suppressed_templates().
 *
 * @return bool
 */
function hu_footer_shows_register() {
	if ( function_exists( 'nexus_is_contact_page' ) && nexus_is_contact_page() ) {
		return false;
	}

	foreach ( hu_footer_register_suppressed_templates() as $template ) {
		if ( is_page_template( $template ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Decide mode, door and way for the current request.
 *
 * `mode`  voll (Hauptmenue plus Tuer), leser (Artikelpfad plus Tuer) oder
 *         fokus (nur die Leiter der Seite, ohne Hauptmenue).
 * `door`  Tuerschluessel aus hu_funnel_doors() oder null, wenn die Seite
 *         selbst das Ziel ist (Kontakt) oder keine Tuer zeigt (fokus).
 * `route` Weg fuer die markierte Zeile im Fuss: freelancer, tracking,
 *         whitelabel, energy oder ''.
 *
 * Reihenfolge der Pruefung ist die Tuerenmatrix in
 * docs/architecture/CONVERSION_ROUTING.md. White-Label steht vor der
 * Fallstudie, weil nexus_is_results_context() die Agenturseite einschliesst.
 *
 * @return array{mode: string, door: string|null, route: string}
 */
function hu_funnel_context() {
	if ( function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context() ) {
		return [ 'mode' => 'fokus', 'door' => null, 'route' => 'energy' ];
	}

	if ( function_exists( 'nexus_is_contact_page' ) && nexus_is_contact_page() ) {
		return [ 'mode' => 'voll', 'door' => null, 'route' => '' ];
	}

	if ( is_singular( 'post' ) ) {
		$slug    = (string) get_post_field( 'post_name', get_queried_object_id() );
		$reader  = hu_funnel_reader_dossier( $slug );
		$dossier = $reader['matched'] ? $reader['slug'] : '';

		if ( in_array( $slug, hu_funnel_portal_post_slugs(), true ) ) {
			return [ 'mode' => 'leser', 'door' => 'sofort', 'route' => 'energy' ];
		}

		if ( 'leadgenerierung' === $dossier ) {
			return [ 'mode' => 'leser', 'door' => 'marktcheck', 'route' => 'energy' ];
		}

		if ( 'tracking' === $dossier ) {
			return [ 'mode' => 'leser', 'door' => 'tracking', 'route' => 'tracking' ];
		}

		return [ 'mode' => 'leser', 'door' => 'projekt', 'route' => '' ];
	}

	if ( hu_is_tracking_route_context() ) {
		return [ 'mode' => 'voll', 'door' => 'tracking', 'route' => 'tracking' ];
	}

	if ( function_exists( 'nexus_is_agency_nav_context' ) && nexus_is_agency_nav_context() ) {
		return [ 'mode' => 'voll', 'door' => 'aufgabe', 'route' => 'whitelabel' ];
	}

	if ( is_page( [ 'case-study-solar-leadgenerierung', 'e3-new-energy' ] ) || is_page_template( 'page-case-study-solar.php' ) ) {
		return [ 'mode' => 'voll', 'door' => 'marktcheck', 'route' => 'energy' ];
	}

	if ( is_front_page() ) {
		return [ 'mode' => 'voll', 'door' => 'projekt', 'route' => 'freelancer' ];
	}

	return [ 'mode' => 'voll', 'door' => 'projekt', 'route' => '' ];
}
