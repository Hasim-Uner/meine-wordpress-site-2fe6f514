<?php
/**
 * Canonical commercial routing for public entry points and conversion paths.
 *
 * SEO query ownership and conversion routing are deliberately separate:
 * a page may keep its own indexable search intent while its CTA routes to the
 * commercially correct next step. This module is the source of truth for the
 * three active commercial paths: direct projects, agency White-Label and the
 * Solar/Wärmepumpe specialization.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a contact URL for a typed direct-project intake.
 *
 * The existing contact form keys stay stable because CRM and automation may
 * consume them. Positioning changes the public semantics, not the transport
 * contract.
 *
 * An empty focus leaves the topic open: the contact page then shows its
 * topic step and the agency/energy switch. A focus is for links whose page
 * already answered the question (an offer card, a tracking page).
 *
 * @param string $type  Existing contact request type.
 * @param string $focus Existing contact focus key, or '' for an open topic.
 * @return string
 */
function hu_get_contact_intake_url( $type = 'project', $focus = 'implementation_scope' ) {
	$contact_url = function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' );
	$args        = [ 'type' => sanitize_key( (string) $type ) ];
	$focus       = sanitize_key( (string) $focus );

	if ( '' !== $focus ) {
		$args['focus'] = $focus;
	}

	return add_query_arg( $args, $contact_url );
}

/**
 * Return the canonical commercial route map.
 *
 * Important: `agentur_local` is an SEO/local acquisition entry, not a global
 * business pillar. The `marketcheck` belongs only to the Energy
 * specialization and explicit Energy contexts.
 *
 * Tracking has two routes on purpose. `tracking_setup` (/ga4-tracking-setup/)
 * is the broad Tracking product — the same offer the homepage sells as
 * "Tracking bis ins CRM" — and therefore the target of every generic
 * "Tracking" link in header, footer and 404. `tracking_b2b`
 * (/server-side-tracking-b2b/) stays the specialist page and query owner for
 * Server-Side Tracking; it is linked with that exact name, never as plain
 * "Tracking". Both keys live here and nowhere else: `tracking_setup` used to
 * be added by a filter in inc/canon/messaging-canon.php, was taken for a
 * missing key, and the header silently switched to the specialist page.
 *
 * @return array<string, string>
 */
function hu_get_commercial_route_map() {
	static $routes = null;

	if ( is_array( $routes ) ) {
		return $routes;
	}

	$routes = [
		'home'            => home_url( '/' ),
		// The homepage now owns the direct Freelancer offer and queries.
		'freelancer'      => home_url( '/' ),
		// Generic CTA: open topic. Pre-setting "implementation_scope" skipped the
		// topic step, hid the agency switch and labelled every lead the same.
		'project_request' => hu_get_contact_intake_url( 'project', '' ),
		'contact'         => function_exists( 'nexus_get_contact_url' ) ? nexus_get_contact_url() : home_url( '/kontakt/' ),
		'whitelabel'      => function_exists( 'nexus_get_whitelabel_page_url' )
			? nexus_get_whitelabel_page_url()
			: home_url( '/whitelabel-retainer/' ),
		'energy'          => function_exists( 'nexus_get_energy_systems_url' )
			? nexus_get_energy_systems_url()
			: home_url( '/solar-waermepumpen-leadgenerierung/' ),
		'marketcheck'     => function_exists( 'hu_get_request_analysis_url' )
			? hu_get_request_analysis_url()
			: home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' ),
		// Versionierte Cluster-Route (inc/wgos/wgos-cluster-pages.php): derselbe
		// Resolver wie der Schluessel 'tracking' der URL-Karte in helpers.php.
		'tracking_setup'  => function_exists( 'nexus_get_wgos_cluster_route_url' )
			? nexus_get_wgos_cluster_route_url( 'ga4-tracking-setup', home_url( '/ga4-tracking-setup/' ) )
			: home_url( '/ga4-tracking-setup/' ),
		'tracking_b2b'    => function_exists( 'nexus_get_page_url' )
			? nexus_get_page_url( [ 'server-side-tracking-b2b' ], home_url( '/server-side-tracking-b2b/' ) )
			: home_url( '/server-side-tracking-b2b/' ),
		'agentur_local'   => function_exists( 'nexus_get_page_url' )
			? nexus_get_page_url( [ 'wordpress-agentur-hannover', 'wordpress-agentur' ], home_url( '/wordpress-agentur-hannover/' ) )
			: home_url( '/wordpress-agentur-hannover/' ),
		'results'         => function_exists( 'nexus_get_results_url' )
			? nexus_get_results_url()
			: home_url( '/case-study-solar-leadgenerierung/' ),
		'about'           => function_exists( 'nexus_get_page_url' )
			? nexus_get_page_url( [ 'hasim-uener', 'uber-mich' ], home_url( '/hasim-uener/' ) )
			: home_url( '/hasim-uener/' ),
	];

	return (array) apply_filters( 'hu_commercial_route_map', $routes );
}

/**
 * Resolve one canonical commercial route.
 *
 * @param string $key      Route key.
 * @param string $fallback Optional fallback URL.
 * @return string
 */
function hu_get_commercial_route( $key, $fallback = '' ) {
	$routes = hu_get_commercial_route_map();
	$key    = sanitize_key( (string) $key );

	if ( isset( $routes[ $key ] ) && '' !== (string) $routes[ $key ] ) {
		return (string) $routes[ $key ];
	}

	return $fallback ? $fallback : home_url( '/' );
}

/**
 * Return the navigation target of the "Ergebnisse" menu item.
 *
 * Seit der Stilllegung von /ergebnisse/ (2026-09-25) fuehrt der Menuepunkt
 * auf den Nachweis-Abschnitt der Startseite: Fall und Referenzen stehen dort
 * unter #arbeiten. Links im Text verweisen weiter ueber die Route 'results'
 * direkt auf die Fallstudie. Kopf, gespeichertes WordPress-Menue und dessen
 * Fallback lesen diese eine Funktion, damit kein Menue mehr auf den alten
 * Hub zeigt.
 *
 * @return string
 */
function hu_get_results_nav_url() {
	return home_url( '/#arbeiten' );
}

/**
 * Determine whether the current request is the broad Tracking product page.
 *
 * `/ga4-tracking-setup/` ist eine versionierte Cluster-Route und wird ohne
 * eigene WordPress-Seite virtuell ausgeliefert; dann greifen `is_page()` und
 * `is_page_template()` nicht, der Anfragepfad schon.
 *
 * @return bool
 */
function hu_is_tracking_setup_route() {
	return is_page( 'ga4-tracking-setup' )
		|| is_page_template( 'page-ga4.php' )
		|| ( function_exists( 'nexus_get_current_wgos_cluster_route_slug' ) && 'ga4-tracking-setup' === nexus_get_current_wgos_cluster_route_slug() );
}

/**
 * Determine whether the current request is one of the tracking routes.
 *
 * Zwei Seiten bedienen denselben Weg: `/ga4-tracking-setup/` ist das Ziel
 * des Tracking-Wegs (Route `tracking_setup`), `/server-side-tracking-b2b/`
 * bleibt die Fachseite und Query-Owner fuer Server-Side Tracking und ist von
 * dort verlinkt. Beide sind derselbe Kontext — die Navigation markiert den
 * Tracking-Punkt auf beiden, der Fuss bietet den Tracking-Weg auf beiden
 * nicht noch einmal an.
 *
 * Eine Funktion statt derselben Vier-Wege-Pruefung in Kopf, Fuss und
 * Contract: genau daran ist das Ziel schon einmal auseinandergelaufen.
 *
 * @return bool
 */
function hu_is_tracking_route_context() {
	return hu_is_tracking_setup_route()
		|| is_page( 'server-side-tracking-b2b' )
		|| is_page_template( 'page-server-side-tracking-b2b.php' );
}

/**
 * Resolve the aria-current value of one navigation item.
 *
 * `page` nur, wenn der Link genau die aufgerufene Seite ist. Liegt die Seite
 * nur im Bereich des Punkts (Server-Side-Fachseite unter "Tracking",
 * Fallstudie unter "Ergebnisse"), ist `true` richtig: Der Punkt ist aktiv,
 * zeigt aber nicht auf diese Seite. Leer heisst: kein Attribut.
 *
 * @param bool $is_target  Link target is the requested page.
 * @param bool $in_section Requested page belongs to the item's area.
 * @return string '', 'page' or 'true'.
 */
function hu_nav_current_state( $is_target, $in_section = false ) {
	if ( $is_target ) {
		return 'page';
	}

	return $in_section ? 'true' : '';
}

/**
 * Return the repo-owned header navigation contract.
 *
 * Kopfzeile und Klappblatt (template-parts/site-header.php), die 404-Seite,
 * das gespeicherte WordPress-Menue (inc/menu-setup.php) und das SEO Cockpit
 * lesen diese eine Struktur. Die Tracking-Action-Namen bleiben die
 * historischen Werte, damit die Zeitreihe vergleichbar bleibt.
 *
 * Reihenfolge seit 2026-09-25: erst was ich anbiete (Leistungen, Tracking),
 * dann die beiden Wege fuer bestimmte Absender (White-Label fuer Agenturen,
 * Solar & Waermepumpe fuer Energiebetriebe), dann Belege und Person. Der
 * Fuss (template-parts/site-footer.php) bietet seine vier Wege in derselben
 * Reihenfolge an. Tracking stand vorher zwischen White-Label und Solar und
 * trennte damit die beiden direkten Leistungen voneinander.
 *
 * `current` ist ein aria-current-Wert aus hu_nav_current_state(), kein Bool.
 *
 * @return array<string, mixed>
 */
function hu_get_site_header_navigation_contract() {
	$routes = hu_get_commercial_route_map();
	return [
		'toggle' => [
			'track'    => 'nav_menu_toggle',
			'category' => 'navigation',
			'section'  => 'header',
		],
		'routes' => [
			[
				'kind'     => 'route',
				'kicker'   => __( 'Direkte Projekte', 'blocksy-child' ),
				'label'    => __( 'Leistungen', 'blocksy-child' ),
				'desc'     => __( 'Neubau, Relaunch und Weiterentwicklung — mit Messung, die von Anfang an mitgebaut wird.', 'blocksy-child' ),
				'url'      => home_url( '/#angebote' ),
				// Ein Anker, keine Seite: Auf der Startseite setzt
				// startseite-strecke.js aria-current="location", solange
				// #angebote im Blick ist. Serverseitig bleibt der Punkt neutral.
				'current'  => '',
				'class'    => 'nav-freelancer-link',
				'track'    => 'nav_header_freelancer',
				'category' => 'navigation',
				'section'  => 'header',
			],
			[
				'kind'     => 'route',
				'kicker'   => __( 'Messung', 'blocksy-child' ),
				'label'    => __( 'Tracking', 'blocksy-child' ),
				'desc'     => __( 'Conversion Tracking mit GA4, Tag Manager, Consent Mode und Google Ads, abgenommen mit Testfällen. Server-Side und CRM, wenn sie gebraucht werden.', 'blocksy-child' ),
				'url'      => $routes['tracking_setup'],
				'current'  => hu_nav_current_state( hu_is_tracking_setup_route(), hu_is_tracking_route_context() ),
				'class'    => 'nav-tracking-link',
				'track'    => 'nav_header_tracking',
				'category' => 'navigation',
				'section'  => 'header',
			],
			// White-Label ist seit 2026-09-22 gleichrangiger Geschaeftspfad und
			// steht deshalb direkt hinter den direkten Leistungen.
			[
				'kind'     => 'route',
				'kicker'   => __( 'Für Agenturen', 'blocksy-child' ),
				'label'    => __( 'White-Label', 'blocksy-child' ),
				'desc'     => __( 'Technik im Hintergrund, unter Ihrem Namen. Erstprojekt mit festem Umfang, Retainer erst danach.', 'blocksy-child' ),
				'url'      => $routes['whitelabel'],
				'current'  => hu_nav_current_state( function_exists( 'nexus_is_agency_nav_context' ) && nexus_is_agency_nav_context() ),
				'class'    => 'nav-agency-link',
				'track'    => 'nav_header_whitelabel',
				'category' => 'navigation',
				'section'  => 'header',
			],
			[
				'kind'     => 'route',
				'kicker'   => __( 'Spezialisierung', 'blocksy-child' ),
				'label'    => __( 'Solar & Wärmepumpe', 'blocksy-child' ),
				'desc'     => __( 'Anfragesysteme für Betriebe, die keine gekauften Portalleads mehr wollen.', 'blocksy-child' ),
				'url'      => $routes['energy'],
				'current'  => hu_nav_current_state( function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context() ),
				'class'    => 'nav-solar-link',
				'track'    => 'nav_header_solar',
				'category' => 'navigation',
				'section'  => 'header',
			],
		],
		'groups' => [
			[
				'title' => __( 'Belege & Person', 'blocksy-child' ),
				'items' => [
					[
						'kind'     => 'group',
						'label'    => __( 'Ergebnisse', 'blocksy-child' ),
						'url'      => hu_get_results_nav_url(),
						// Das Ziel ist ein Abschnitt der Startseite; die
						// Fallstudie liegt nur im Bereich des Punkts.
						'current'  => hu_nav_current_state( false, function_exists( 'nexus_is_results_context' ) && nexus_is_results_context() ),
						'class'    => 'nav-results-link',
						'track'    => 'nav_header_results',
						'category' => 'navigation',
						'section'  => 'header',
					],
					[
						'kind'     => 'group',
						'label'    => __( 'Über Haşim', 'blocksy-child' ),
						'url'      => $routes['about'],
						'current'  => hu_nav_current_state( is_page( 'hasim-uener' ) || is_page( 'uber-mich' ) || is_page_template( 'page-hasim-uener.php' ) ),
						'class'    => 'nav-about-link',
						'track'    => 'nav_header_about',
						'category' => 'navigation',
						'section'  => 'header',
					],
				],
			],
		],
		'meta' => [
			'location' => __( 'Pattensen bei Hannover · DACH-weit', 'blocksy-child' ),
			'links'    => [
				[
					'label'    => hu_get_contact_email(),
					'url'      => hu_get_contact_mailto(),
					'track'    => 'nav_header_project',
					'category' => 'lead_gen',
					'section'  => 'header',
				],
				[
					'label'    => '+49 176 76596580',
					'url'      => 'tel:+4917676596580',
					'track'    => 'nav_header_project',
					'category' => 'lead_gen',
					'section'  => 'header',
				],
				[
					'label'    => 'hasimuener.org',
					'url'      => 'https://hasimuener.org/',
					'track'    => 'nav_header_about',
					'category' => 'navigation',
					'section'  => 'header',
				],
			],
		],
		'cta' => [
			'kind'        => 'cta',
			'label'       => __( 'Projekt anfragen', 'blocksy-child' ),
			'short_label' => __( 'Anfragen', 'blocksy-child' ),
			'url'         => $routes['project_request'],
			'current'     => '',
			'class'       => 'nav-cta-button nav-project-link',
			'track'       => 'nav_header_project',
			'category'    => 'lead_gen',
			'section'     => 'header',
		],
	];
}

/**
 * Return a flat compatibility view of the global navigation contract.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_get_primary_navigation_contract() {
	$contract = hu_get_site_header_navigation_contract();
	$items    = (array) ( $contract['routes'] ?? [] );

	foreach ( (array) ( $contract['groups'] ?? [] ) as $group ) {
		$items = array_merge( $items, (array) ( $group['items'] ?? [] ) );
	}

	if ( ! empty( $contract['cta'] ) && is_array( $contract['cta'] ) ) {
		$items[] = $contract['cta'];
	}

	return $items;
}

/**
 * Return the repo-owned footer navigation contract.
 *
 * Der Fuss hat zwei Lautstaerken. Laut sind die vier Wege (`picks`) in der
 * Reihenfolge des Kopfs: Website, Tracking, Agentur, Energie. Leise ist das
 * Verzeichnis (`directory`) in vier kleinen Gruppen. Es fuehrt, was der Kopf
 * nicht fuehrt: die Fachseiten, die eigene Suchanfragen besitzen
 * (docs/seo/query-ownership.csv), Belege, Wissen und Rechtliches.
 *
 * "Server-Side Tracking" steht hier mit genau diesem Namen, weil der
 * Kopfpunkt "Tracking" seit 2026-09-25 auf das breite Tracking-Angebot zeigt;
 * die Fachseite behaelt so ihren seitenweiten Link und ihren eigenen
 * Ankertext. Die bestehenden cta_footer_*-Werte bleiben unveraendert, neue
 * Links bekommen eigene Werte.
 *
 * template-parts/site-footer.php rendert diese Struktur, das SEO Cockpit
 * zaehlt sie als seitenweite Linkquelle.
 *
 * @return array{picks: array<int, array<string, string>>, directory: array<int, array<string, mixed>>}
 */
function hu_get_site_footer_navigation_contract() {
	$routes = hu_get_commercial_route_map();
	$urls   = function_exists( 'nexus_get_primary_public_url_map' ) ? nexus_get_primary_public_url_map() : [];

	return [
		'picks'     => [
			[
				'route'  => 'freelancer',
				'pre'    => 'Ich habe ',
				'strong' => 'eine Website',
				'post'   => ', die neu gebaut oder besser werden soll.',
				'url'    => $routes['freelancer'],
				'track'  => 'cta_footer_pick_project',
			],
			[
				'route'  => 'tracking',
				'pre'    => 'Ich brauche ',
				'strong' => 'belastbare Messung',
				'post'   => ' für Anfragen, Kampagnen und CRM.',
				'url'    => $routes['tracking_setup'],
				'track'  => 'cta_footer_pick_tracking',
			],
			[
				'route'  => 'whitelabel',
				'pre'    => 'Ich bin ',
				'strong' => 'Agentur',
				'post'   => ' und brauche Technik unter meinem Namen.',
				'url'    => $routes['whitelabel'],
				'track'  => 'cta_footer_pick_agency',
			],
			[
				'route'  => 'energy',
				'pre'    => 'Ich bin ',
				'strong' => 'Solar- oder Wärmepumpenbetrieb',
				'post'   => ' und will eigene Anfragen statt Portalleads.',
				'url'    => $routes['energy'],
				'track'  => 'cta_footer_pick_energy',
			],
		],
		'directory' => [
			[
				'key'   => 'leistungen',
				'title' => __( 'Leistungen', 'blocksy-child' ),
				'items' => [
					[
						'label'    => __( 'Server-Side Tracking', 'blocksy-child' ),
						'url'      => $routes['tracking_b2b'],
						'track'    => 'cta_footer_nav_server_side_tracking',
						'category' => 'navigation',
					],
					[
						'label'    => __( 'Performance Marketing', 'blocksy-child' ),
						'url'      => $urls['performance_marketing'] ?? home_url( '/performance-marketing/' ),
						'track'    => 'cta_footer_nav_performance_marketing',
						'category' => 'navigation',
					],
					[
						'label'    => __( 'WordPress Agentur Hannover', 'blocksy-child' ),
						'url'      => $routes['agentur_local'],
						'track'    => 'cta_footer_nav_agentur_local',
						'category' => 'navigation',
					],
				],
			],
			[
				'key'   => 'belege',
				'title' => __( 'Belege & Person', 'blocksy-child' ),
				'items' => [
					[
						'label'    => __( 'Solar-Fallstudie', 'blocksy-child' ),
						'url'      => $urls['e3'] ?? $routes['results'],
						'track'    => 'cta_footer_nav_case_study_proof',
						'category' => 'trust',
					],
					[
						'label'    => __( 'Über Haşim', 'blocksy-child' ),
						'url'      => $routes['about'],
						'track'    => 'cta_footer_nav_about',
						'category' => 'navigation',
					],
				],
			],
			[
				'key'   => 'wissen',
				'title' => __( 'Wissen', 'blocksy-child' ),
				'items' => [
					[
						'label'    => __( 'Blog', 'blocksy-child' ),
						'url'      => $urls['blog'] ?? home_url( '/blog/' ),
						'track'    => 'cta_footer_nav_insights',
						'category' => 'navigation',
					],
					[
						'label'    => __( 'Glossar', 'blocksy-child' ),
						'url'      => $urls['glossary'] ?? home_url( '/glossar/' ),
						'track'    => 'cta_footer_nav_glossary',
						'category' => 'navigation',
					],
				],
			],
			[
				'key'   => 'rechtliches',
				'title' => __( 'Rechtliches', 'blocksy-child' ),
				'items' => [
					[
						'label'    => __( 'Impressum', 'blocksy-child' ),
						'url'      => $urls['impressum'] ?? home_url( '/impressum/' ),
						'track'    => 'cta_footer_nav_imprint',
						'category' => 'navigation',
					],
					[
						'label'    => __( 'Datenschutz', 'blocksy-child' ),
						'url'      => $urls['datenschutz'] ?? home_url( '/datenschutz/' ),
						'track'    => 'cta_footer_nav_privacy',
						'category' => 'navigation',
					],
				],
			],
		],
	];
}

/**
 * Return every URL of the footer directory, in rendered order.
 *
 * @return array<int, string>
 */
function hu_get_site_footer_directory_urls() {
	$urls = [];

	foreach ( hu_get_site_footer_navigation_contract()['directory'] as $group ) {
		foreach ( (array) ( $group['items'] ?? [] ) as $item ) {
			$urls[] = (string) ( $item['url'] ?? '' );
		}
	}

	return array_values( array_filter( $urls ) );
}

/**
 * Wayfinding contract for important public routes.
 *
 * This deliberately does not change query ownership, canonicals, titles or
 * redirects. It only describes orientation inside and between existing owners.
 * Search intent -> owning page -> next best action remains the invariant.
 *
 * @return array<string, mixed>
 */
function hu_get_wayfinding_context() {
	if ( is_page( 'server-side-tracking-b2b' ) || is_page_template( 'page-server-side-tracking-b2b.php' ) ) {
		return [
			'key'        => 'tracking',
			'label'      => 'Server-Side Tracking',
			'breadcrumb' => true,
			'toc_mode'   => 'existing',
			'next'       => [], // Die Tracking-Seite endet bereits in ihrer eigenen Anfrage-Sektion.
		];
	}

	// White-Label nimmt seit dem Relaunch auf dem Strecke-System nicht mehr
	// teil: Die Landeseite der Akquise-Mails hat einen eigenen Kopf mit
	// Startseiten-Link und Ankern (markiert von whitelabel.js). Breadcrumb
	// und navigation-ecosystem.css/.js kosteten dort nur den ersten
	// Bildschirm. Das BreadcrumbList-Schema bleibt davon unberuehrt.
	if ( function_exists( 'nexus_is_agency_nav_context' ) && nexus_is_agency_nav_context() ) {
		return [];
	}

	if ( function_exists( 'nexus_is_energy_systems_context' ) && nexus_is_energy_systems_context() ) {
		return [
			'key'        => 'energy',
			'label'      => 'Solar & Wärmepumpe',
			'breadcrumb' => false, // Eigene Energy-Dokumentnavigation; keine zweite Hierarchie darüberlegen.
			'toc_mode'   => 'existing',
			'next'       => [],
		];
	}

	if ( is_page( 'ergebnisse' ) || is_page_template( 'page-ergebnisse.php' ) ) {
		return [
			'key'        => 'results',
			'label'      => 'Ergebnisse',
			'breadcrumb' => true,
			'toc_mode'   => 'generated',
			'toc'        => [
				[ 'id' => 'arbeiten', 'label' => 'WordPress-Arbeiten' ],
				[ 'id' => 'technik', 'label' => 'Umsetzung im Detail' ],
				[ 'id' => 'grossprojekt', 'label' => 'Solar-Fall' ],
				[ 'id' => 'whitelabel', 'label' => 'Übergabe' ],
				[ 'id' => 'weiter', 'label' => 'Nächster Schritt' ],
			],
			'next'       => [], // Drei-Wege-Close der Seite bleibt alleiniger Abschluss.
		];
	}

	if ( is_page( 'hasim-uener' ) || is_page( 'uber-mich' ) || is_page_template( 'page-hasim-uener.php' ) ) {
		return [
			'key'        => 'about',
			'label'      => 'Über Haşim',
			'breadcrumb' => true,
			'toc_mode'   => 'existing',
			'next'       => [], // Der bestehende About-Abschluss führt bereits zu Projekt und White-Label.
		];
	}

	return [];
}

/**
 * Whether the current request participates in the shared wayfinding layer.
 *
 * @return bool
 */
function hu_wayfinding_is_active() {
	return [] !== hu_get_wayfinding_context();
}

add_filter( 'body_class', function ( $classes ) {
	if ( hu_wayfinding_is_active() ) {
		$context   = hu_get_wayfinding_context();
		$classes[] = 'hu-wayfinding-active';
		$classes[] = 'hu-wayfinding-' . sanitize_html_class( (string) ( $context['key'] ?? 'page' ) );
	}
	return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() || ! hu_wayfinding_is_active() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$css = '/assets/css/navigation-ecosystem.css';
	$js  = '/assets/js/navigation-ecosystem.js';

	if ( is_file( $dir . $css ) ) {
		wp_enqueue_style( 'hu-navigation-ecosystem', $uri . $css, [], (string) filemtime( $dir . $css ) );
	}
	if ( is_file( $dir . $js ) ) {
		wp_enqueue_script( 'hu-navigation-ecosystem', $uri . $js, [], (string) filemtime( $dir . $js ), true );
	}
}, 40 );

/**
 * Render a visible breadcrumb only. Structured BreadcrumbList data remains in
 * the existing schema layer so this component cannot create duplicate JSON-LD.
 *
 * @return void
 */
function hu_render_wayfinding_breadcrumb() {
	$context = hu_get_wayfinding_context();
	if ( empty( $context['breadcrumb'] ) || empty( $context['label'] ) ) {
		return;
	}
	?>
	<nav class="hu-wayfinding-breadcrumb" aria-label="Breadcrumb" data-track-section="breadcrumb">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track-action="breadcrumb_home" data-track-category="navigation">Startseite</a></li>
			<li><span aria-current="page"><?php echo esc_html( (string) $context['label'] ); ?></span></li>
		</ol>
	</nav>
	<?php
}
add_action( 'wp_body_open', 'hu_render_wayfinding_breadcrumb', 30 );

/**
 * Render a semantic TOC only on routes that do not already own a local index.
 * Existing TOCs are enhanced in-place by navigation-ecosystem.js.
 *
 * @return void
 */
function hu_render_generated_page_toc() {
	$context = hu_get_wayfinding_context();
	$items   = isset( $context['toc'] ) && is_array( $context['toc'] ) ? $context['toc'] : [];
	if ( 'generated' !== ( $context['toc_mode'] ?? '' ) || [] === $items ) {
		return;
	}
	?>
	<nav class="hu-page-toc" aria-label="Auf dieser Seite" data-hu-rail="true" data-track-section="page_toc">
		<details open>
			<summary>Auf dieser Seite</summary>
			<ul role="list">
				<?php foreach ( $items as $item ) : ?>
					<li><a href="#<?php echo esc_attr( (string) $item['id'] ); ?>" data-track-action="toc_<?php echo esc_attr( sanitize_key( (string) $item['id'] ) ); ?>" data-track-category="navigation"><?php echo esc_html( (string) $item['label'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</details>
	</nav>
	<?php
}
add_action( 'wp_body_open', 'hu_render_generated_page_toc', 31 );

/**
 * Render one recommended next action plus one safe alternative.
 * Pages with their own intentional closing architecture opt out in the contract.
 *
 * @return void
 */
function hu_render_wayfinding_next_step() {
	$context = hu_get_wayfinding_context();
	$next    = isset( $context['next'] ) && is_array( $context['next'] ) ? $context['next'] : [];
	if ( empty( $next['primary']['url'] ) || empty( $next['primary']['label'] ) ) {
		return;
	}
	?>
	<nav class="hu-next-step" aria-label="Nächster sinnvoller Schritt" data-track-section="next_step">
		<p class="hu-next-step__eyebrow">Nächster sinnvoller Schritt</p>
		<p class="hu-next-step__title"><?php echo esc_html( (string) ( $next['title'] ?? 'Weiter zum nächsten Schritt.' ) ); ?></p>
		<div class="hu-next-step__links">
			<a class="hu-next-step__primary" href="<?php echo esc_url( (string) $next['primary']['url'] ); ?>" data-track-action="wayfinding_next_primary" data-track-category="lead_gen"><?php echo esc_html( (string) $next['primary']['label'] ); ?> <span aria-hidden="true">→</span></a>
			<?php if ( ! empty( $next['secondary']['url'] ) && ! empty( $next['secondary']['label'] ) ) : ?>
				<a class="hu-next-step__secondary" href="<?php echo esc_url( (string) $next['secondary']['url'] ); ?>" data-track-action="wayfinding_next_secondary" data-track-category="navigation"><?php echo esc_html( (string) $next['secondary']['label'] ); ?></a>
			<?php endif; ?>
		</div>
	</nav>
	<?php
}
add_action( 'get_footer', 'hu_render_wayfinding_next_step', 5 );