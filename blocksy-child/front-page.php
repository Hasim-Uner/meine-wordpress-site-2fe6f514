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

$home_proof_tiles = [
	[ 'value' => '100', 'max' => '/100', 'label' => 'Barrierefreiheit', 'note' => '' ],
	[ 'value' => '100', 'max' => '/100', 'label' => 'SEO & Best Practices', 'note' => '' ],
	[ 'value' => '99', 'max' => '', 'label' => 'PageSpeed mobil', 'note' => 'Lighthouse, mobil' ],
	[
		'value'  => 'Aktiv',
		'max'    => '',
		'label'  => 'Server-Side Tracking',
		'note'   => 'GA4 + sGTM',
		'url'    => $tracking_url,
		'action' => 'home_proof_tracking_page',
	],
];

$e3_case_metrics = [
	[
		'value' => trim( $e3_metric( 'cpl_before' ) . ' → ' . $e3_metric( 'cpl_after' ) ),
		'label' => 'Kosten pro Anfrage',
	],
	[
		'value' => $e3_metric( 'lead_count' ),
		'label' => 'qualifizierte Anfragen',
	],
	[
		'value' => $e3_metric( 'sales_conversion' ),
		'label' => 'Abschlussquote',
	],
];

get_header();
?>

<div class="hu-hp hu-hp--art" id="top" data-track-section="homepage">
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

			<div class="hu-system-art" aria-hidden="true">
				<div class="hu-system-art__grid"></div>
				<div class="hu-system-measure">
					<span class="hu-system-measure__dot"></span>
					<div><strong>Tracking &amp; Messung</strong><small>über die gesamte Strecke</small></div>
				</div>
				<svg class="hu-system-orbit" viewBox="0 0 820 470" preserveAspectRatio="none">
					<path d="M76 336 C76 120 170 42 394 42 C620 42 748 112 758 276 C764 374 682 418 458 422 C232 426 96 408 76 336Z"/>
					<circle cx="158" cy="92" r="5"/><circle cx="395" cy="42" r="5"/><circle cx="655" cy="93" r="5"/><circle cx="758" cy="276" r="5"/><circle cx="555" cy="418" r="5"/><circle cx="240" cy="414" r="5"/>
					<path class="hu-system-orbit__tap" d="M158 92V178 M395 42V140 M655 93V177 M555 418V333"/>
				</svg>

				<ol class="hu-system-flow">
					<li class="hu-system-node hu-system-node--traffic">
						<span class="hu-system-node__num">01</span>
						<strong class="hu-system-node__title">Traffic</strong>
						<ul class="hu-system-sources">
							<li><span>G</span>Google</li>
							<li><span>∞</span>Meta Ads</li>
							<li><span>in</span>LinkedIn</li>
							<li><span>@</span>E-Mail</li>
							<li><span>⌕</span>Organisch</li>
						</ul>
					</li>

					<li class="hu-system-node hu-system-node--website">
						<span class="hu-system-node__num">02</span>
						<strong class="hu-system-node__title">Website &amp;<br>Landingpage</strong>
						<div class="hu-system-browser">
							<div class="hu-system-browser__bar"><span></span><span></span><span></span></div>
							<div class="hu-system-browser__wp">W</div>
							<div class="hu-system-browser__hero"></div>
							<div class="hu-system-browser__lines"><i></i><i></i><i></i></div>
							<b></b>
						</div>
					</li>

					<li class="hu-system-node hu-system-node--qualify">
						<span class="hu-system-node__num">03</span>
						<strong class="hu-system-node__title">Qualifikation</strong>
						<div class="hu-system-funnel"><i></i><i></i><i></i><i></i></div>
						<small>passende Anfragen statt Streuverlust</small>
					</li>

					<li class="hu-system-node hu-system-node--crm">
						<span class="hu-system-node__num">04</span>
						<strong class="hu-system-node__title">CRM &amp; Vertrieb</strong>
						<div class="hu-system-bars"><i></i><i></i><i></i><i></i></div>
						<div class="hu-system-person"><span></span></div>
						<small>qualifiziert übergeben</small>
					</li>
				</ol>
				<div class="hu-system-result">Mehr qualifizierte Anfragen</div>
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

	<section class="hu-section hu-section--case" id="beleg" data-track-section="beleg" aria-labelledby="hu-case-h">
		<div class="hu-container">
			<div class="hu-section-heading hu-reveal">
				<div><span class="hu-eyebrow">Dokumentierter Projektfall</span><h2 id="hu-case-h">Was passiert, wenn Website, Messung und Vertrieb als Strecke arbeiten.</h2></div>
				<p>Landingpages, Kampagnen, Tracking, Vorqualifizierung und Vertrieb wurden im Referenzfall <?php echo esc_html( $e3_case_label ); ?> miteinander verbunden.</p>
			</div>
			<dl class="hu-case-metrics hu-reveal">
				<?php foreach ( $e3_case_metrics as $metric ) : ?>
					<div><dt><?php echo esc_html( $metric['value'] ); ?></dt><dd><?php echo esc_html( $metric['label'] ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
			<div class="hu-case-foot hu-reveal"><small>Ergebnis des gesamten Systems, keine Prognose für andere Projekte.</small><a class="hu-inline-link" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="home_case_study" data-track-category="proof" data-track-section="beleg">Case Study ansehen <span aria-hidden="true">→</span></a></div>
		</div>
	</section>

	<section class="hu-section hu-section--fit" id="abgrenzung" data-track-section="abgrenzung" aria-labelledby="hu-fit-h">
		<div class="hu-container hu-fit">
			<div class="hu-reveal"><span class="hu-eyebrow">Passt nicht für jedes Projekt</span><h2 id="hu-fit-h">Relevant wird mein Setup, wenn die Übergänge zählen.</h2></div>
			<div class="hu-fit__copy hu-reveal">
				<p>Wenn es nur um ein visuelles Redesign oder eine einfache Visitenkarte geht, ist mein Setup unnötig. Relevant bin ich, wenn Website, Messung und Anfrageübergabe zusammen funktionieren sollen.</p>
				<p>Sie arbeiten direkt mit mir. Scope und Preis stehen vor dem Start fest; Code, Konten und Zugänge bleiben in Ihrer Hand.</p>
				<a class="hu-inline-link" href="<?php echo esc_url( $about_url ); ?>" data-track-action="home_about" data-track-category="trust" data-track-section="abgrenzung">Mehr über Haşim Üner <span aria-hidden="true">→</span></a>
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
