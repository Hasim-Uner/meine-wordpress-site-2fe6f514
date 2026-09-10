<?php
/**
 * Homepage: Person, drei Angebotswege, Projektbeleg und Zusammenarbeit.
 *
 * Die Auswahl führt zu den spezialisierten Angebotsseiten. Der Marktcheck
 * bleibt im Energie-Funnel; die Startseite trägt kein eigenes Formular.
 * SEO und Schema werden weiterhin zentral aus inc/ ausgegeben.
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
$contact_url    = function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: home_url( '/kontakt/' );
$tracking_url   = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/' ) );
$portrait_url   = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait-400x533.webp';
$portrait_small = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait-192.webp';

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url   = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_case_label = $e3_canon['case_label'] ?? '';
$e3_metric     = static function ( $key, $field = 'display' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field ) : '';
};
$e3_case_metrics = [
	[
		'value' => trim( $e3_metric( 'cpl_before' ) . ' → ' . $e3_metric( 'cpl_after' ) ),
		'label' => 'Kosten pro Anfrage: eingekaufte Kontakte vorher, eigene Anfragen nach der Aufbauphase.',
	],
	[
		'value' => $e3_metric( 'lead_count' ),
		'label' => 'qualifizierte Anfragen in ' . $e3_metric( 'timeframe', 'display_dative' ),
	],
];

// Alternative Einstiege, keine Prozessschritte. Zahlenbelege stehen im Case.
$home_doors = [
	[
		'kicker' => 'Solar · Wärmepumpe · Speicher',
		'title'  => 'Eigene Anfragen aufbauen.',
		'desc'   => 'Angebot, Kampagnen und Vorqualifizierung für eigene Anfragen. Auf der Branchenseite erfahren Sie, wie der Aufbau funktioniert und was der Marktcheck für Ihren Betrieb klärt.',
		'url'    => $energy_url,
		'label'  => 'Anfragesysteme ansehen',
		// Neuer Hook: Der frühere home_door_marktcheck sprang zum Formular.
		'action' => 'home_door_energy',
	],
	[
		'kicker' => 'Direkt für Unternehmen',
		'title'  => 'WordPress bauen oder verbessern.',
		'desc'   => 'Neue Website, Relaunch oder gezielte Weiterentwicklung: Ich übernehme die technische Umsetzung und kläre, welches Tracking und welche Anfragewege Ihr Projekt braucht. Sie arbeiten direkt mit mir.',
		'url'    => $freelancer_url,
		'label'  => 'WordPress-Projekte ansehen',
		'action' => 'home_door_freelancer',
	],
	[
		'kicker' => 'Für Agenturen',
		'title'  => 'Kundenprojekte unter Ihrem Namen umsetzen.',
		'desc'   => 'Sie führen das Kundenprojekt. Ich ergänze Ihre Agentur bei WordPress und Tracking. Wir starten mit einer klar abgegrenzten Aufgabe; eine laufende Zusammenarbeit ist anschließend möglich.',
		'url'    => $whitelabel_url,
		'label'  => 'White-Label-Zusammenarbeit ansehen',
		'action' => 'home_door_whitelabel',
	],
];

get_header();
?>

<div class="hu-hp hu-hp--hub hu-hp--orientation" id="top" data-track-section="homepage">
	<section class="hu-hero hu-hero--hub" id="hero" data-track-section="hero" aria-labelledby="hu-home-title">
		<div class="hu-hero__grid-bg" aria-hidden="true"></div>
		<div class="hu-container hu-hero__container">
			<div class="hu-hero__lede">
				<p class="hu-home-identity"><strong>Haşim Üner</strong><span>WordPress-Entwickler</span></p>
				<h1 class="hu-display hu-hero__title" id="hu-home-title">Websites, die Anfragen produzieren.</h1>
				<p class="hu-hero__sub">Ich entwickle WordPress-Websites und verbinde sie mit Tracking und klaren Anfragewegen. Technisches SEO, Kampagnen und Conversion denke ich bei der Umsetzung mit – vom ersten Besuch bis zur Übergabe an Ihr Team.</p>
				<p class="hu-home-location">Pattensen bei Hannover · Für Unternehmen und Agenturen im DACH-Raum</p>
				<a class="hu-hero__cue" href="#wege"
				   data-track-action="home_hero_to_routes" data-track-category="navigation" data-track-section="hero">
					<span>Passenden Einstieg finden</span>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
				</a>
			</div>
			<figure class="hu-home-portrait">
				<img src="<?php echo esc_url( $portrait_url ); ?>"
				     srcset="<?php echo esc_url( $portrait_small ); ?> 192w, <?php echo esc_url( $portrait_url ); ?> 400w"
				     sizes="(max-width: 760px) 72px, 240px" width="400" height="533"
				     alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" decoding="async" fetchpriority="high">
			</figure>
		</div>
	</section>

	<section class="hu-section hu-section--first" id="wege" data-track-section="wege" aria-labelledby="hu-doors-h">
		<div class="hu-container">
			<div class="hu-section-heading hu-section-heading--solo">
				<div class="hu-section-heading__primary">
					<h2 id="hu-doors-h">Welcher Weg passt zu Ihrem Vorhaben?</h2>
				</div>
			</div>
			<div class="hu-gateways hu-gateways--doors" data-track-section="tueren">
				<?php foreach ( $home_doors as $index => $door ) : ?>
					<a class="hu-gateway" href="<?php echo esc_url( $door['url'] ); ?>"
					   aria-labelledby="<?php echo esc_attr( 'hu-door-' . $index . '-audience hu-door-' . $index . '-title hu-door-' . $index . '-cta' ); ?>"
					   data-track-action="<?php echo esc_attr( $door['action'] ); ?>" data-track-category="navigation" data-track-section="tueren">
						<div class="hu-gateway__head">
							<span class="hu-gateway__kicker hu-mono" id="<?php echo esc_attr( 'hu-door-' . $index . '-audience' ); ?>"><?php echo esc_html( $door['kicker'] ); ?></span>
						</div>
						<h3 class="hu-gateway__title" id="<?php echo esc_attr( 'hu-door-' . $index . '-title' ); ?>"><?php echo esc_html( $door['title'] ); ?></h3>
						<p class="hu-gateway__desc"><?php echo esc_html( $door['desc'] ); ?></p>
						<div class="hu-gateway__foot">
							<span class="hu-gateway__cta" id="<?php echo esc_attr( 'hu-door-' . $index . '-cta' ); ?>">
								<?php echo esc_html( $door['label'] ); ?>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
							</span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="hu-section" id="beleg" data-track-section="beleg" aria-labelledby="hu-case-h">
		<div class="hu-container">
			<div class="hu-section-heading">
				<div class="hu-section-heading__primary">
					<span class="hu-eyebrow">Dokumentierter Solar-Fall</span>
					<h2 id="hu-case-h">Was die Verbindung in einem Projekt bewirkt hat.</h2>
				</div>
				<p class="hu-section-heading__intro">Dokumentierter Referenzfall: <?php echo esc_html( $e3_case_label ); ?>. Landingpages, Kampagnen, Tracking und Vorqualifizierung wurden zu einer eigenen Anfragestrecke verbunden.</p>
			</div>
			<dl class="hu-home-case-metrics">
				<?php foreach ( $e3_case_metrics as $metric ) : ?>
					<div class="hu-home-case-metric">
						<dt><?php echo esc_html( $metric['label'] ); ?></dt>
						<dd><?php echo esc_html( $metric['value'] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
			<p class="hu-home-case-note">Ergebnis des gesamten Projekts einschließlich Kampagnen und laufender Optimierung. Der Fall beschreibt unterschiedliche Wege der Anfragegewinnung; die Zahlen lassen sich nicht pauschal auf andere Projekte übertragen.</p>
			<a href="<?php echo esc_url( $e3_case_url ); ?>" class="hu-btn hu-btn-link"
			   data-track-action="home_case_study" data-track-category="proof" data-track-section="beleg">Umsetzung und Ergebnisse im Solar-Fall ansehen <span aria-hidden="true">→</span></a>
			<div class="hu-home-proof-links" id="lebender-beweis">
				<a href="<?php echo esc_url( $freelancer_url . '#projekte' ); ?>" class="hu-btn hu-btn-link"
				   data-track-action="home_proof_wordpress" data-track-category="proof" data-track-section="beweis">WordPress-Umsetzungen ansehen <span aria-hidden="true">→</span></a>
				<a href="<?php echo esc_url( $tracking_url ); ?>" class="hu-btn hu-btn-link"
				   data-track-action="home_proof_tracking_page" data-track-category="proof" data-track-section="beweis">Tracking und Messung im Detail <span aria-hidden="true">→</span></a>
				<a href="<?php echo esc_url( $psi_url ); ?>" class="hu-btn hu-btn-link" target="_blank" rel="noopener"
				   data-track-action="home_proof_pagespeed" data-track-category="proof" data-track-section="beweis">Technik dieser Website prüfen <span aria-hidden="true">↗</span><span class="screen-reader-text"> (öffnet in neuem Tab)</span></a>
			</div>
		</div>
	</section>

	<section class="hu-section" id="abgrenzung" data-track-section="abgrenzung" aria-labelledby="hu-fit-h">
		<div class="hu-container hu-home-work">
			<div class="hu-section-heading__primary">
				<span class="hu-eyebrow">Persönliche Zusammenarbeit</span>
				<h2 id="hu-fit-h">Ich entwickle mit Blick auf das, was nach dem Klick passiert.</h2>
			</div>
			<div class="hu-selfqual">
				<p>Meine Arbeit verbindet WordPress-Entwicklung mit Erfahrung aus B2B-Vertrieb und eigener Kampagnenarbeit. Deshalb gehören für mich die Website, die Messung und die Übergabe einer Anfrage zusammen.</p>
				<p>Sie sprechen direkt mit mir. Vor dem Start klären wir Aufgabe, Umfang und Zuständigkeiten. Ihre Konten und Zugänge bleiben in Ihrer Hand; Änderungen und Übergabe dokumentiere ich. Der Einstieg kann auch eine einzelne WordPress- oder Tracking-Aufgabe sein.</p>
				<a href="<?php echo esc_url( $about_url ); ?>" class="hu-btn hu-btn-link"
				   data-track-action="home_about" data-track-category="trust" data-track-section="abgrenzung">Mehr über Haşim Üner <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</section>

	<section class="hu-section hu-section--close" id="cta" data-track-section="abschluss" aria-labelledby="hu-close-h">
		<div class="hu-container">
			<div class="hu-close">
				<h2 id="hu-close-h">Sie wissen noch nicht, welcher Weg passt?</h2>
				<p class="hu-close__sub">Schreiben Sie mir kurz, was Sie vorhaben oder was gerade nicht funktioniert. Ich ordne ein, welcher nächste Schritt sinnvoll ist.</p>
				<div class="hu-close__actions">
					<a href="<?php echo esc_url( $contact_url ); ?>" class="hu-btn hu-btn-primary"
					   data-track-action="home_close_contact" data-track-category="lead_gen" data-track-section="abschluss">Projekt kurz beschreiben <span aria-hidden="true">→</span></a>
				</div>
				<p class="hu-home-response"><?php echo esc_html( hu_response_promise( 'sentence' ) ); ?></p>
			</div>
		</div>
	</section>
</div>

<?php get_footer(); ?>
