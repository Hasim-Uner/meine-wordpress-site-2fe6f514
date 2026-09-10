<?php
/**
 * Front Page Template — zentrale Positionierung und drei Angebotswege.
 *
 * Die Startseite verkauft nicht jede Leistung vollständig. Sie erklärt die
 * gemeinsame Systemlogik, baut Vertrauen auf und verteilt auf drei getrennte
 * Commercial Routes: Energy, direkte WordPress-Projekte und White Label.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes         = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$energy_url     = $routes['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );
$freelancer_url = $routes['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );
$whitelabel_url = $routes['whitelabel'] ?? ( function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' ) );
$about_url      = $routes['about'] ?? home_url( '/hasim-uener/' );
$tracking_url   = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
$contact_url    = function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: home_url( '/kontakt/' );
$results_url    = home_url( '/ergebnisse/' );
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/' ) );

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url   = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_case_label = $e3_canon['case_label'] ?? '';
$e3_metric     = static function ( $key, $field = 'display' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field ) : '';
};

$home_doors = [
	[
		'badge'  => '01',
		'kind'   => 'energy',
		'kicker' => 'Solar · Wärmepumpe · Speicher',
		'title'  => 'Eigene Anfragen aufbauen.',
		'desc'   => 'Für Betriebe, die unabhängiger von Portalen werden und ein eigenes Anfragesystem aufbauen möchten.',
		'url'    => $energy_url,
		'label'  => 'Anfragesysteme ansehen',
		'action' => 'home_door_energy',
	],
	[
		'badge'  => '02',
		'kind'   => 'wordpress',
		'kicker' => 'Direkt für Unternehmen',
		'title'  => 'WordPress bauen oder verbessern.',
		'desc'   => 'Für Relaunch, Weiterentwicklung und technische Umsetzung mit klarer Struktur, sauberer Übergabe und passendem Tracking.',
		'url'    => $freelancer_url,
		'label'  => 'WordPress-Projekte ansehen',
		'action' => 'home_door_freelancer',
	],
	[
		'badge'  => '03',
		'kind'   => 'agency',
		'kicker' => 'Für Agenturen',
		'title'  => 'Kundenprojekte unter Ihrem Namen umsetzen.',
		'desc'   => 'White-Label-Umsetzung bei WordPress, Tracking und technischer Delivery — diskret, strukturiert und mit klarer Übergabe.',
		'url'    => $whitelabel_url,
		'label'  => 'White-Label ansehen',
		'action' => 'home_door_whitelabel',
	],
];

/*
 * Die drei Labtest-Kacheln kommen aus dem Canon. Sie standen bis 2026-09 als
 * Literale hier und ein zweites Mal im Ergebnisse-Hub — zwei Staende derselben
 * Messung sind auf einer Seite, die Attribution verkauft, teuer.
 *
 * Die kombinierte Kachel "SEO & Best Practices" gilt nur, solange beide
 * Canon-Werte gleich sind; laufen sie auseinander, gehoert sie getrennt.
 */
$home_proof_tiles = [
	[ 'value' => $e3_metric( 'site_lighthouse_accessibility' ), 'max' => $e3_metric( 'site_lighthouse_accessibility', 'max' ), 'label' => 'Barrierefreiheit', 'note' => '' ],
	[ 'value' => $e3_metric( 'site_lighthouse_seo' ), 'max' => $e3_metric( 'site_lighthouse_seo', 'max' ), 'label' => 'SEO & Best Practices', 'note' => '' ],
	[ 'value' => $e3_metric( 'site_lighthouse_performance' ), 'max' => '', 'label' => 'PageSpeed mobil', 'note' => 'Lighthouse, mobil' ],
	[
		'value'  => 'Aktiv',
		'max'    => '',
		'label'  => 'Server-Side Tracking',
		'note'   => 'GA4 + sGTM',
		'url'    => $tracking_url,
		'action' => 'home_proof_tracking_page',
	],
];

$e3_cpl_reduction    = $e3_metric( 'cpl_reduction', 'value' );
$e3_cpl_before       = $e3_metric( 'cpl_before' );
$e3_cpl_after        = $e3_metric( 'cpl_after' );
$e3_lead_count       = $e3_metric( 'lead_count' );
$e3_lead_conversion  = $e3_metric( 'lead_conversion' );
$e3_sales_conversion = $e3_metric( 'sales_conversion' );
$e3_timeframe        = $e3_metric( 'timeframe', 'display_dative' );

$e3_case_metrics = [
	[
		'value' => $e3_lead_count,
		'label' => 'qualifizierte Anfragen',
		'note'  => 'in ' . $e3_timeframe,
	],
	[
		'value' => $e3_lead_conversion,
		'label' => 'Lead-Conversion-Rate',
		'note'  => 'auf der Anfragestrecke',
	],
	[
		'value' => $e3_sales_conversion,
		'label' => 'Abschlussquote',
		'note'  => 'inkl. Beitrag des Vertriebs',
	],
];

$home_art_css_path = get_stylesheet_directory() . '/assets/css/homepage-art.css';
wp_enqueue_style(
	'nexus-home-art-css',
	get_stylesheet_directory_uri() . '/assets/css/homepage-art.css',
	[ 'nexus-home-redesign-css' ],
	function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $home_art_css_path ) : ( file_exists( $home_art_css_path ) ? (string) filemtime( $home_art_css_path ) : null )
);

$home_art_v3_css_path = get_stylesheet_directory() . '/assets/css/homepage-art-v3.css';
wp_enqueue_style(
	'nexus-home-art-v3-css',
	get_stylesheet_directory_uri() . '/assets/css/homepage-art-v3.css',
	[ 'nexus-home-art-css' ],
	function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $home_art_v3_css_path ) : ( file_exists( $home_art_v3_css_path ) ? (string) filemtime( $home_art_v3_css_path ) : null )
);

if ( function_exists( 'hu_enqueue_css' ) ) {
	hu_enqueue_css( 'nexus-home-flow-css', 'homepage-flow.css', [ 'nexus-home-art-v3-css' ] );
}

get_header();
?>

<div class="hu-hp hu-hp--art hu-hp--art-v3" id="top" data-track-section="homepage">
	<section class="hu-hero hu-hero--hub" id="hero" data-track-section="hero" aria-labelledby="hu-home-title">
		<div class="hu-container hu-hero__container">
			<div class="hu-hero__lede">
				<p class="hu-home-identity"><span aria-hidden="true"></span>Haşim Üner · WordPress-Entwickler</p>
				<h1 class="hu-display hu-hero__title" id="hu-home-title">
					Websites, die Anfragen produzieren.
					<span class="hu-hero__title-2">Und Technik, die Wirkung messbar macht.</span>
				</h1>
				<p class="hu-hero__sub">Ich entwickle WordPress-Websites und verbinde sie mit Tracking und klaren Anfragewegen. Technisches SEO, Kampagnen und Conversion denke ich bei der Umsetzung mit — vom ersten Besuch bis zur Übergabe an Ihr Team.</p>
				<p class="hu-home-location">Pattensen bei Hannover · Für Unternehmen und Agenturen im DACH-Raum</p>
				<a class="hu-hero__cue" href="#wege" data-track-action="home_hero_to_routes" data-track-category="navigation" data-track-section="hero">
					<span>Drei Wege — passenden Einstieg finden</span>
					<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
			</div>

			<div class="hu-flow" role="img" aria-label="Anfragefluss in vier Schritten: Traffic aus Google, Meta Ads, LinkedIn, E-Mail und organischer Suche läuft auf Website und Landingpage, wird dort qualifiziert und als passende Anfrage an CRM und Vertrieb übergeben. Tracking und Messung laufen über die gesamte Strecke.">

				<!-- Breite Ansicht -->
				<svg class="hu-flow__d" viewBox="0 0 1160 706" aria-hidden="true" focusable="false">
					<defs>
						<linearGradient id="hu-flow-karte-d" x1="0" y1="0" x2="0.38" y2="1">
							<stop class="stop-a" offset="0"/>
							<stop class="stop-b" offset="0.78"/>
						</linearGradient>
					</defs>

					<!-- Messring über die gesamte Strecke -->
					<path class="ring-basis" d="M580 84 A572 288 0 1 1 579.5 84 Z"/>
					<path class="ring-lauf ring-d" style="--len:2776px" d="M580 84 A572 288 0 1 1 579.5 84 Z"/>
					<circle class="ring-punkt" cx="813" cy="109" r="9"/>
					<circle class="kupfer" cx="813" cy="109" r="3.5"/>
					<text class="aussen mono" x="839" y="104">TRACKING &amp; MESSUNG</text>
					<text class="aussen-sub" x="839" y="128">über die gesamte Strecke</text>

					<!-- Wege -->
					<path class="weg" d="M244 333 C 270 333 276 372 298 372"/>
					<path class="weg" d="M244 372 H298"/>
					<path class="weg" d="M244 411 C 270 411 276 372 298 372"/>
					<path class="weg" d="M244 450 C 270 450 276 372 298 372"/>
					<path class="weg" d="M244 489 C 270 489 276 372 298 372"/>
					<path class="weg" d="M580 372 C 604 372 612 348 634 344"/>
					<path class="weg" d="M860 444 C 884 443 898 438 914 432"/>

					<!-- 01 Traffic -->
					<rect class="karte" x="34" y="222" width="210" height="300" rx="10" fill="url(#hu-flow-karte-d)"/>
					<path class="glanz" d="M45 222.5 H233"/>
					<text class="nr mono" x="58" y="268">01</text>
					<text class="titel mono" x="58" y="296">TRAFFIC</text>
					<rect class="innen" x="56" y="320" width="26" height="26" rx="7"/>
					<text class="icon-t" x="69" y="338" text-anchor="middle">G</text>
					<text class="quelle" x="94" y="340">Google</text>
					<rect class="innen" x="56" y="359" width="26" height="26" rx="7"/>
					<text class="icon-t" x="69" y="377" text-anchor="middle">∞</text>
					<text class="quelle" x="94" y="379">Meta Ads</text>
					<rect class="innen" x="56" y="398" width="26" height="26" rx="7"/>
					<text class="icon-t" x="69" y="416" text-anchor="middle">in</text>
					<text class="quelle" x="94" y="418">LinkedIn</text>
					<rect class="innen" x="56" y="437" width="26" height="26" rx="7"/>
					<text class="icon-t" x="69" y="455" text-anchor="middle">@</text>
					<text class="quelle" x="94" y="457">E-Mail</text>
					<rect class="innen" x="56" y="476" width="26" height="26" rx="7"/>
					<circle class="icon-linie" cx="67.5" cy="487" r="4.4"/>
					<path class="icon-linie" d="M70.6 490.1 L74 493.5"/>
					<text class="quelle" x="94" y="496">Organisch</text>

					<!-- 02 Website & Landingpage -->
					<rect class="karte" x="298" y="160" width="282" height="424" rx="12" fill="url(#hu-flow-karte-d)"/>
					<path class="glanz" d="M311 160.5 H567"/>
					<text class="nr mono" x="322" y="210">02</text>
					<text class="titel mono" x="322" y="238">WEBSITE &amp;</text>
					<text class="titel mono" x="322" y="260">LANDINGPAGE</text>
					<rect class="innen" x="322" y="282" width="234" height="278" rx="8"/>
					<circle class="linie" cx="339" cy="300" r="3.5"/>
					<circle class="linie" cx="351" cy="300" r="3.5"/>
					<circle class="linie" cx="363" cy="300" r="3.5"/>
					<rect class="linie" x="322" y="313" width="234" height="1"/>
					<circle class="kontur" cx="439" cy="356" r="30"/>
					<text class="marke" x="439" y="366" text-anchor="middle">W</text>
					<rect class="linie" x="348" y="406" width="182" height="9" rx="4.5"/>
					<rect class="linie" x="348" y="425" width="182" height="9" rx="4.5"/>
					<rect class="linie" x="348" y="444" width="118" height="9" rx="4.5"/>
					<rect class="puls puls-ring" x="458" y="498" width="82" height="28" rx="5" style="--v:0s"/>
					<rect class="kupfer" x="458" y="498" width="82" height="28" rx="5"/>

					<!-- 03 Qualifikation -->
					<rect class="karte" x="634" y="222" width="226" height="300" rx="10" fill="url(#hu-flow-karte-d)"/>
					<path class="glanz" d="M645 222.5 H849"/>
					<text class="nr mono" x="658" y="268">03</text>
					<text class="titel mono" x="658" y="296">QUALIFIKATION</text>
					<path class="form" d="M664 330 H830 L811 356 H683 Z"/>
					<path class="form" d="M685 362 H809 L790 388 H704 Z"/>
					<path class="kupfer" d="M706 394 H788 L764 420 H730 Z"/>
					<rect class="kupfer" x="739" y="420" width="16" height="12"/>
					<text class="notiz" x="658" y="474">passende Anfragen</text>
					<text class="notiz" x="658" y="496">statt Streuverlust</text>

					<!-- 04 CRM & Vertrieb -->
					<rect class="karte" x="914" y="222" width="212" height="300" rx="10" fill="url(#hu-flow-karte-d)"/>
					<path class="glanz" d="M925 222.5 H1115"/>
					<text class="nr mono" x="938" y="268">04</text>
					<text class="titel mono" x="938" y="296">CRM &amp;</text>
					<text class="titel mono" x="938" y="318">VERTRIEB</text>
					<circle class="empfang" cx="1088" cy="272" r="21"/>
					<circle class="kontur" cx="1088" cy="266" r="6.5"/>
					<path class="kontur" d="M1077 285 a11 11 0 0 1 22 0"/>
					<rect class="linie" x="938" y="448" width="164" height="1.5"/>
					<rect class="form" x="938" y="412" width="32" height="36"/>
					<rect class="form" x="982" y="392" width="32" height="56"/>
					<rect class="form" x="1026" y="370" width="32" height="78"/>
					<rect class="kupfer" x="1070" y="348" width="32" height="100"/>
					<rect class="ankunft" x="1070" y="348" width="32" height="100" style="--v:0s"/>
					<text class="notiz" x="938" y="478">qualifiziert</text>
					<text class="notiz" x="938" y="500">übergeben</text>

					<!-- Ergebnis -->
					<text class="ergebnis mono" x="1094" y="692" text-anchor="end">MEHR QUALIFIZIERTE ANFRAGEN</text>
					<rect class="kupfer" x="1100" y="686" width="26" height="2"/>

					<!-- Signale: Zulauf aus den Quellen -->
					<circle class="sig" r="5" style="offset-path:path('M244 333 C 270 333 276 372 298 372');animation-name:hu-flow-zulauf;--v:0s"/>
					<circle class="sig" r="5" style="offset-path:path('M244 372 H298');animation-name:hu-flow-zulauf;--v:-.14s"/>
					<circle class="sig" r="5" style="offset-path:path('M244 411 C 270 411 276 372 298 372');animation-name:hu-flow-zulauf;--v:-.28s"/>
					<circle class="sig" r="5" style="offset-path:path('M244 450 C 270 450 276 372 298 372');animation-name:hu-flow-zulauf;--v:-.42s"/>
					<circle class="sig" r="5" style="offset-path:path('M244 489 C 270 489 276 372 298 372');animation-name:hu-flow-zulauf;--v:-.56s"/>
					<circle class="sig" r="5" style="offset-path:path('M244 333 C 270 333 276 372 298 372');animation-name:hu-flow-zulauf;--v:-5.6s"/>
					<circle class="sig" r="5" style="offset-path:path('M244 372 H298');animation-name:hu-flow-zulauf;--v:-5.74s"/>
					<circle class="sig" r="5" style="offset-path:path('M244 411 C 270 411 276 372 298 372');animation-name:hu-flow-zulauf;--v:-5.88s"/>
					<circle class="sig" r="5" style="offset-path:path('M244 450 C 270 450 276 372 298 372');animation-name:hu-flow-zulauf;--v:-6.02s"/>
					<circle class="sig" r="5" style="offset-path:path('M244 489 C 270 489 276 372 298 372');animation-name:hu-flow-zulauf;--v:-6.16s"/>

					<!-- Signale: Website zur Qualifikation -->
					<circle class="sig sig-hell" r="5.5" style="offset-path:path('M580 372 C 604 372 612 348 634 344 L 706 338');animation-name:hu-flow-uebergabe;--v:0s"/>
					<circle class="sig sig-hell" r="5.5" style="offset-path:path('M580 372 C 604 372 612 348 634 344 L 706 338');animation-name:hu-flow-uebergabe;--v:-.18s"/>
					<circle class="sig sig-hell" r="5.5" style="offset-path:path('M580 372 C 604 372 612 348 634 344 L 706 338');animation-name:hu-flow-uebergabe;--v:-.36s"/>
					<circle class="sig sig-hell" r="5.5" style="offset-path:path('M580 372 C 604 372 612 348 634 344 L 706 338');animation-name:hu-flow-uebergabe;--v:-.54s"/>

					<!-- Signale: Streuverlust wird aussortiert -->
					<circle class="sig sig-aus" r="5" style="offset-path:path('M700 338 C 684 364 672 392 662 424');animation-name:hu-flow-ausschuss;--v:-.1s"/>
					<circle class="sig sig-aus" r="5" style="offset-path:path('M716 338 C 742 358 780 382 826 406');animation-name:hu-flow-ausschuss;--v:-.3s"/>
					<circle class="sig sig-aus" r="5" style="offset-path:path('M708 340 C 694 372 686 402 676 448');animation-name:hu-flow-ausschuss;--v:-.5s"/>

					<!-- Signale: qualifiziert weiter in den Vertrieb -->
					<circle class="sig sig-kern" r="6.5" style="offset-path:path('M712 338 C 730 366 748 392 750 432 C 760 442 800 446 860 444 C 884 443 898 438 914 432');animation-name:hu-flow-durchlauf;--v:0s"/>
					<circle class="sig sig-kern" r="6.5" style="offset-path:path('M712 338 C 730 366 748 392 750 432 C 760 442 800 446 860 444 C 884 443 898 438 914 432');animation-name:hu-flow-durchlauf;--v:-.4s"/>
				</svg>

				<!-- Kompakte Ansicht -->
				<svg class="hu-flow__m" viewBox="0 0 420 786" aria-hidden="true" focusable="false">
					<defs>
						<linearGradient id="hu-flow-karte-m" x1="0" y1="0" x2="0.38" y2="1">
							<stop class="stop-a" offset="0"/>
							<stop class="stop-b" offset="0.78"/>
						</linearGradient>
					</defs>

					<path class="ring-basis" d="M68 40 H352 A56 56 0 0 1 408 96 V694 A56 56 0 0 1 352 750 H68 A56 56 0 0 1 12 694 V96 A56 56 0 0 1 68 40 Z"/>
					<path class="ring-lauf ring-m" style="--len:2116px" d="M68 40 H352 A56 56 0 0 1 408 96 V694 A56 56 0 0 1 352 750 H68 A56 56 0 0 1 12 694 V96 A56 56 0 0 1 68 40 Z"/>

					<text class="aussen mono" x="210" y="22" text-anchor="middle" style="font-size:14px">TRACKING &amp; MESSUNG</text>
					<text class="ergebnis mono" x="210" y="776" text-anchor="middle" style="font-size:14px">MEHR QUALIFIZIERTE ANFRAGEN</text>

					<path class="weg" d="M210 184 V218"/>
					<path class="weg" d="M210 378 V412"/>
					<path class="weg" d="M210 566 V600"/>

					<!-- 01 Traffic -->
					<rect class="karte" x="46" y="68" width="328" height="116" rx="10" fill="url(#hu-flow-karte-m)"/>
					<path class="glanz" d="M57 68.5 H363"/>
					<text class="nr mono" x="70" y="96" style="font-size:15px">01</text>
					<text class="titel mono" x="70" y="116" style="font-size:15px;letter-spacing:.08em">TRAFFIC</text>
					<rect class="innen" x="80" y="130" width="44" height="32" rx="6"/>
					<text class="icon-t" x="102" y="151" text-anchor="middle">G</text>
					<rect class="innen" x="134" y="130" width="44" height="32" rx="6"/>
					<text class="icon-t" x="156" y="151" text-anchor="middle">∞</text>
					<rect class="innen" x="188" y="130" width="44" height="32" rx="6"/>
					<text class="icon-t" x="210" y="151" text-anchor="middle">in</text>
					<rect class="innen" x="242" y="130" width="44" height="32" rx="6"/>
					<text class="icon-t" x="264" y="151" text-anchor="middle">@</text>
					<rect class="innen" x="296" y="130" width="44" height="32" rx="6"/>
					<circle class="icon-linie" cx="316" cy="144" r="5"/>
					<path class="icon-linie" d="M319.5 147.5 L323 151"/>

					<!-- 02 Website & Landingpage -->
					<rect class="karte" x="46" y="218" width="328" height="160" rx="10" fill="url(#hu-flow-karte-m)"/>
					<path class="glanz" d="M57 218.5 H363"/>
					<text class="nr mono" x="70" y="246" style="font-size:15px">02</text>
					<text class="titel mono" x="70" y="266" style="font-size:15px;letter-spacing:.08em">WEBSITE &amp; LANDINGPAGE</text>
					<rect class="innen" x="70" y="282" width="280" height="76" rx="6"/>
					<circle class="kontur" cx="106" cy="320" r="17"/>
					<text class="marke" x="106" y="327" text-anchor="middle" style="font-size:17px">W</text>
					<rect class="linie" x="136" y="308" width="108" height="7" rx="3.5"/>
					<rect class="linie" x="136" y="322" width="132" height="7" rx="3.5"/>
					<rect class="puls puls-ring" x="276" y="328" width="58" height="18" rx="4" style="--v:0s"/>
					<rect class="kupfer" x="276" y="328" width="58" height="18" rx="4"/>

					<!-- 03 Qualifikation -->
					<rect class="karte" x="46" y="412" width="328" height="154" rx="10" fill="url(#hu-flow-karte-m)"/>
					<path class="glanz" d="M57 412.5 H363"/>
					<text class="nr mono" x="70" y="440" style="font-size:15px">03</text>
					<text class="titel mono" x="70" y="460" style="font-size:15px;letter-spacing:.08em">QUALIFIKATION</text>
					<text class="notiz" x="70" y="494" style="font-size:14px">passende Anfragen</text>
					<text class="notiz" x="70" y="514" style="font-size:14px">statt Streuverlust</text>
					<path class="form" d="M234 476 H346 L333 496 H247 Z"/>
					<path class="form" d="M248 500 H332 L319 520 H261 Z"/>
					<path class="kupfer" d="M262 524 H318 L303 544 H277 Z"/>
					<rect class="kupfer" x="283" y="544" width="14" height="10"/>

					<!-- 04 CRM & Vertrieb -->
					<rect class="karte" x="46" y="600" width="328" height="122" rx="10" fill="url(#hu-flow-karte-m)"/>
					<path class="glanz" d="M57 600.5 H363"/>
					<text class="nr mono" x="70" y="628" style="font-size:15px">04</text>
					<text class="titel mono" x="70" y="648" style="font-size:15px;letter-spacing:.08em">CRM &amp; VERTRIEB</text>
					<text class="notiz" x="70" y="674" style="font-size:14px">qualifiziert übergeben</text>
					<rect class="linie" x="232" y="700" width="118" height="1.5"/>
					<rect class="form" x="232" y="674" width="22" height="26"/>
					<rect class="form" x="264" y="660" width="22" height="40"/>
					<rect class="form" x="296" y="646" width="22" height="54"/>
					<rect class="kupfer" x="328" y="630" width="22" height="70"/>
					<rect class="ankunft" x="328" y="630" width="22" height="70" style="--v:0s"/>

					<!-- Signale -->
					<circle class="sig" r="5" style="offset-path:path('M210 184 V218');animation-name:hu-flow-zulauf;--v:0s"/>
					<circle class="sig" r="5" style="offset-path:path('M210 184 V218');animation-name:hu-flow-zulauf;--v:-5.6s"/>
					<circle class="sig sig-hell" r="5.5" style="offset-path:path('M210 378 V412');animation-name:hu-flow-uebergabe;--v:0s"/>
					<circle class="sig sig-aus" r="5" style="offset-path:path('M282 478 C 272 502 258 522 236 548');animation-name:hu-flow-ausschuss;--v:-.2s"/>
					<circle class="sig sig-kern" r="6" style="offset-path:path('M290 478 C 290 530 290 548 290 556 C 290 578 240 574 210 600');animation-name:hu-flow-durchlauf;--v:0s"/>
				</svg>
			</div>
		</div>
	</section>

	<section class="hu-section hu-section--routes" id="wege" data-track-section="tueren" aria-labelledby="hu-doors-h">
		<div class="hu-container">
			<div class="hu-section-heading hu-reveal">
				<div>
					<span class="hu-eyebrow">Drei Wege</span>
					<h2 id="hu-doors-h">Welcher Weg passt zu Ihrem Vorhaben?</h2>
				</div>
				<p>Die Startseite erklärt den Zusammenhang. Die Details, Preise und nächsten Schritte finden Sie im passenden Angebotsweg.</p>
			</div>

			<div class="hu-gateways hu-reveal">
				<?php foreach ( $home_doors as $door ) : ?>
					<a class="hu-gateway hu-gateway--<?php echo esc_attr( $door['kind'] ); ?>" href="<?php echo esc_url( $door['url'] ); ?>"
					   data-track-action="<?php echo esc_attr( $door['action'] ); ?>" data-track-category="navigation" data-track-section="tueren">
						<div class="hu-gateway__top">
							<span class="hu-gateway__icon" aria-hidden="true">
								<?php if ( 'energy' === $door['kind'] ) : ?>
									<svg viewBox="0 0 32 32"><circle cx="16" cy="16" r="5"/><path d="M16 2v5M16 25v5M2 16h5M25 16h5M6.1 6.1l3.5 3.5M22.4 22.4l3.5 3.5M25.9 6.1l-3.5 3.5M9.6 22.4l-3.5 3.5"/></svg>
								<?php elseif ( 'wordpress' === $door['kind'] ) : ?>
									<svg viewBox="0 0 32 32"><circle cx="16" cy="16" r="13"/><path d="M8 10h4l5 13 3-8M13 10h3M21 9c2 2 2 5 0 9"/></svg>
								<?php else : ?>
									<svg viewBox="0 0 32 32"><circle cx="12" cy="10" r="4"/><circle cx="22" cy="12" r="3"/><path d="M4 27v-5c0-4 3-7 8-7s8 3 8 7v5M20 18c5 0 8 2 8 6v3"/></svg>
								<?php endif; ?>
							</span>
							<span class="hu-gateway__badge"><?php echo esc_html( $door['badge'] ); ?></span>
						</div>
						<span class="hu-gateway__kicker"><?php echo esc_html( $door['kicker'] ); ?></span>
						<h3><?php echo esc_html( $door['title'] ); ?></h3>
						<p><?php echo esc_html( $door['desc'] ); ?></p>
						<span class="hu-gateway__cta"><?php echo esc_html( $door['label'] ); ?><span aria-hidden="true">→</span></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-section hu-section--ink" id="lebender-beweis" data-track-section="beweis" aria-labelledby="hu-proof-h">
		<div class="hu-container">
			<div class="hu-section-heading hu-section-heading--light hu-reveal">
				<div><span class="hu-eyebrow">Technischer Beweis</span><h2 id="hu-proof-h">Die Technik können Sie an dieser Seite selbst prüfen.</h2></div>
				<p>Performance, Zugänglichkeit, SEO und Messung sind keine Folien im Angebot. Sie laufen hier im Produktivbetrieb.</p>
			</div>
			<div class="hu-liveproof hu-reveal">
				<?php foreach ( $home_proof_tiles as $tile ) : ?>
					<?php $is_link = ! empty( $tile['url'] ); ?>
					<<?php echo $is_link ? 'a' : 'div'; ?> class="hu-liveproof__tile<?php echo $is_link ? ' hu-liveproof__tile--link' : ''; ?>"<?php if ( $is_link ) : ?> href="<?php echo esc_url( $tile['url'] ); ?>" data-track-action="<?php echo esc_attr( $tile['action'] ); ?>" data-track-category="proof" data-track-section="beweis"<?php endif; ?>>
						<strong><?php echo esc_html( $tile['value'] ); ?><?php if ( '' !== $tile['max'] ) : ?><small><?php echo esc_html( $tile['max'] ); ?></small><?php endif; ?></strong>
						<span><?php echo esc_html( $tile['label'] ); ?></span>
						<?php if ( '' !== $tile['note'] ) : ?><em><?php echo esc_html( $tile['note'] ); ?></em><?php endif; ?>
					</<?php echo $is_link ? 'a' : 'div'; ?>>
				<?php endforeach; ?>
			</div>
			<a href="<?php echo esc_url( $psi_url ); ?>" class="hu-inline-link hu-reveal" target="_blank" rel="noopener" data-track-action="home_proof_pagespeed" data-track-category="proof" data-track-section="beweis">Diese Seite bei PageSpeed Insights prüfen <span aria-hidden="true">↗</span></a>
		</div>
	</section>

	<section class="hu-section hu-section--case hu-case-v3" id="beleg" data-track-section="beleg" aria-labelledby="hu-case-h">
		<div class="hu-container">
			<div class="hu-case-v3__head hu-reveal">
				<div>
					<span class="hu-eyebrow">Ausgewähltes Großprojekt · Gesamtfunnel</span>
					<h2 id="hu-case-h">Vom ersten Besuch bis zur Vertriebsübergabe.</h2>
				</div>
				<div class="hu-case-v3__intro">
					<p>Ein Beispiel aus meiner Projektarbeit: Für einen <?php echo esc_html( $e3_case_label ); ?> wurden Website, Landingpages, Kampagnen, Tracking, Vorqualifizierung und Vertriebsübergabe als Gesamtfunnel aufgebaut und laufend optimiert.</p>
					<p class="hu-case-v3__stack">Website <span>·</span> Landingpages <span>·</span> Kampagnen <span>·</span> Tracking <span>·</span> Vorqualifizierung <span>·</span> Vertrieb</p>
				</div>
			</div>

			<div class="hu-case-v3__proof hu-reveal">
				<div class="hu-case-v3__primary">
					<span class="hu-case-v3__index">01 / Effizienz</span>
					<strong>−<?php echo esc_html( $e3_cpl_reduction ); ?> %</strong>
					<span>Kosten pro Anfrage</span>
					<small><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></small>
				</div>
				<dl class="hu-case-v3__metrics">
					<?php foreach ( $e3_case_metrics as $index => $metric ) : ?>
						<div>
							<span class="hu-case-v3__index">0<?php echo esc_html( (string) ( $index + 2 ) ); ?> / Ergebnis</span>
							<dt><?php echo esc_html( $metric['value'] ); ?></dt>
							<dd><?php echo esc_html( $metric['label'] ); ?><small><?php echo esc_html( $metric['note'] ); ?></small></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>

			<div class="hu-case-v3__foot hu-reveal">
				<p>Ausgewählter Referenzfall. Die Werte zeigen das Gesamtsystem und isolieren keinen WordPress-Effekt. Sie sind keine Prognose für andere Projekte.</p>
				<div class="hu-case-v3__links">
					<a class="hu-inline-link" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="home_case_study" data-track-category="proof" data-track-section="beleg">Großprojekt im Detail <span aria-hidden="true">→</span></a>
					<a class="hu-inline-link hu-inline-link--quiet" href="<?php echo esc_url( $results_url ); ?>" data-track-action="home_more_results" data-track-category="proof" data-track-section="beleg">Weitere Arbeiten ansehen <span aria-hidden="true">→</span></a>
				</div>
			</div>
		</div>
	</section>

	<section class="hu-section hu-section--fit" id="abgrenzung" data-track-section="abgrenzung" aria-labelledby="hu-fit-h">
		<div class="hu-container hu-fit hu-fit-v3">
			<div class="hu-fit-v3__lead hu-reveal">
				<span class="hu-eyebrow">Passt nicht für jedes Projekt</span>
				<h2 id="hu-fit-h">Relevant wird mein Setup, wenn die Übergänge zählen.</h2>
				<a class="hu-inline-link" href="<?php echo esc_url( $about_url ); ?>" data-track-action="home_about" data-track-category="trust" data-track-section="abgrenzung">Mehr über Haşim Üner <span aria-hidden="true">→</span></a>
			</div>
			<div class="hu-fit-v3__matrix hu-reveal">
				<div class="hu-fit-v3__row hu-fit-v3__row--muted">
					<span>Unnötig, wenn</span>
					<p>es nur um ein visuelles Redesign oder eine einfache Visitenkarte geht.</p>
				</div>
				<div class="hu-fit-v3__row hu-fit-v3__row--active">
					<span>Relevant, wenn</span>
					<p>Website, Messung und Anfrageübergabe als zusammenhängende Strecke funktionieren sollen.</p>
				</div>
				<ul class="hu-fit-v3__facts" aria-label="Zusammenarbeit">
					<li><strong>Direkter Kontakt</strong><span>Sie arbeiten mit mir, nicht über Projektmanager.</span></li>
					<li><strong>Scope &amp; Preis vor Start</strong><span>Umfang, Abhängigkeiten und Kosten werden vorab geklärt.</span></li>
					<li><strong>Code &amp; Konten bleiben bei Ihnen</strong><span>Code, Konten und Zugänge bleiben in Ihrer Hand.</span></li>
				</ul>
			</div>
		</div>
	</section>

	<section class="hu-section hu-section--close" id="cta" data-track-section="abschluss" aria-labelledby="hu-close-h">
		<div class="hu-container">
			<div class="hu-close hu-reveal">
				<span class="hu-eyebrow">Nächster Schritt</span>
				<h2 id="hu-close-h">Sie wissen noch nicht, welcher Weg passt?</h2>
				<p>Schreiben Sie mir kurz, was Sie vorhaben oder was gerade nicht funktioniert. Ich ordne ein, welcher nächste Schritt sinnvoll ist.</p>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="hu-btn hu-btn-primary" data-track-action="home_close_contact" data-track-category="lead_gen" data-track-section="abschluss">Projekt kurz beschreiben <span aria-hidden="true">→</span></a>
				<small><?php echo esc_html( hu_response_promise( 'sentence' ) ); ?></small>
			</div>
		</div>
	</section>
</div>

<?php get_footer(); ?>
