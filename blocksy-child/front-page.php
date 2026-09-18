<?php
/**
 * Homepage: canonical WordPress Freelancer money page.
 *
 * Replaces /wordpress-freelancer-hannover/ with one query owner at /.
 * Specialist routes retain their own offers. Contact stays in the shared intake.
 * Prices, response promises and references are read from their existing canons.
 *
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes         = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$contact_url    = $routes['project_request'] ?? home_url( '/kontakt/?type=project&focus=implementation_scope' );
$whitelabel_url = $routes['whitelabel'] ?? home_url( '/whitelabel-retainer/' );
$energy_url     = $routes['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );
$tracking_url   = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
$about_url      = $routes['about'] ?? home_url( '/hasim-uener/' );
$results_url    = $routes['results'] ?? home_url( '/ergebnisse/' );
$contact_email  = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$portrait_url   = get_stylesheet_directory_uri() . '/assets/img/hasim-freelancer-relaxed-640x800.webp';
$website_price  = function_exists( 'hu_freelancer_website_price' ) ? hu_freelancer_website_price( true ) : '3.400 € netto';
$tracking_price = function_exists( 'hu_tracking_price' ) ? hu_tracking_price( 'standard', 'setup', 'display', '1.290 €' ) : '1.290 €';
$response       = hu_response_promise( 'phrase' );
$response_short = hu_response_promise( 'compact' );
$references     = function_exists( 'hu_public_reference_projects' ) ? hu_public_reference_projects() : [];
$github_url     = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/' ) );
$e3_canon       = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url    = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_case_label_accusative = $e3_canon['case_label_accusative'] ?? 'mittelständischen PV-Installationsbetrieb';
$e3_metric      = static function ( $key, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field, $fallback ) : $fallback;
};
$project_link = static function ( $focus ) use ( $contact_url ) {
	return function_exists( 'hu_get_contact_intake_url' )
		? hu_get_contact_intake_url( 'project', $focus )
		: add_query_arg( [ 'type' => 'project', 'focus' => $focus ], $contact_url );
};
$offers = [
	[
		'id' => 'angebot-website', 'nr' => '01', 'title' => 'WordPress-Entwicklung',
		'problem' => 'Neu bauen, relaunchen oder gezielt weiterentwickeln.',
		'text' => 'Ich entwickle und überarbeite WordPress-Websites so, dass Struktur, Technik und Nutzerführung zusammenpassen. Bestehende Inhalte, URLs und funktionierende Systeme bleiben dort erhalten, wo das sinnvoll ist.',
		'scope' => 'Relaunch · Bestandsentwicklung · technisches SEO · Performance · Staging und Übergabe',
		'price' => 'Ab ' . $website_price, 'focus' => 'relaunch', 'cta' => 'WordPress-Projekt anfragen',
	],
	[
		'id' => 'angebot-funnel', 'nr' => '02', 'title' => 'Anfragestrecken & Landingpages',
		'problem' => 'Traffic ist nur dann wertvoll, wenn der nächste Schritt funktioniert.',
		'text' => 'Ich baue Landingpages, Formulare und Qualifizierungswege so, dass Angebot, Anfrage und Übergabe logisch zusammenpassen. Der Umfang reicht vom einzelnen Conversion-Weg bis zur kompletten Anfragestrecke.',
		'scope' => 'Landingpage · Formular · Qualifizierung · Danke-Seite · Lead-Übergabe',
		'price' => 'Projektpreis nach Umfang', 'focus' => 'conversion', 'cta' => 'Anfragestrecke besprechen',
	],
	[
		'id' => 'angebot-tracking', 'nr' => '03', 'title' => 'Tracking & CRM',
		'problem' => 'Eine Anfrage ist erst dann messbar, wenn die Strecke bis ins System reicht.',
		'text' => 'Ich prüfe und entwickle die Messkette von GA4 und GTM bis zu Consent, Server-Side Tracking und CRM-Übergabe. Ergänzt wird nur, was für Ihr Setup und Ihre Daten tatsächlich gebraucht wird.',
		'scope' => 'GA4 · GTM · Consent · Server-Side Tracking · Attribution · CRM-Anbindung',
		'price' => 'Standard-Setup ab ' . $tracking_price . ' netto', 'focus' => 'tracking', 'cta' => 'Tracking-Projekt anfragen',
	],
];
$faqs = [
	[ 'q' => 'Übernehmen Sie eine bestehende WordPress-Website?', 'a' => 'Ja. Vor einer Zusage prüfe ich Theme, Plugins, Zugänge und die konkrete Aufgabe. Daraus wird eine gezielte Weiterentwicklung, eine technische Bereinigung oder ein Relaunch. Ein Neubau ist keine Voraussetzung.' ],
	[ 'q' => 'Arbeiten Sie mit unserem bestehenden Theme oder Page Builder?', 'a' => 'Wenn der vorhandene Aufbau technisch tragfähig ist, muss er nicht ersetzt werden. Erst wenn Performance, Wartbarkeit oder eine Integration klare Grenzen setzen, besprechen wir einen tieferen Eingriff.' ],
	[ 'q' => 'Was brauchen Sie von unserem Team?', 'a' => 'Eine Person für Entscheidungen und Freigaben, die nötigen Zugänge sowie vorhandene Inhalte und Gestaltungsvorgaben. Fehlende Texte, Branding, Übersetzungen oder zusätzliche Schnittstellen werden vor dem Start sauber abgegrenzt.' ],
	[ 'q' => 'Wie lange dauert ein Projekt?', 'a' => 'Das hängt von Umfang, Ausgangslage und Ihren Vorarbeiten ab. Nach der ersten Einordnung erhalten Sie einen realistischen Zeitrahmen. Inhaltslieferung, Freigaben und technische Abhängigkeiten werden dabei ausdrücklich berücksichtigt.' ],
	[ 'q' => 'Gehören Website, Konten und Code anschließend uns?', 'a' => 'Ja. Code, Repository, Hosting und eingesetzte Konten liegen in Ihrer Hand. Zur Übergabe gehören die vereinbarte Dokumentation und Zugänge. Eine weitere Betreuung ist möglich, aber keine Voraussetzung.' ],
];

/**
 * Hero flow diagram. Static markup, so it renders without JavaScript and cannot
 * shift the hero after paint. Motion stays in CSS behind prefers-reduced-motion.
 */
$flow_icon = static function ( $kind ) {
	$icons = [
		'website' => '<rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M3 8h18"></path><circle cx="6.5" cy="6" r=".7" fill="currentColor" stroke="none"></circle><circle cx="9" cy="6" r=".7" fill="currentColor" stroke="none"></circle>',
		'request' => '<path d="M7 3h7l4 4v14H7z"></path><path d="M14 3v5h5M10 12h5M10 16h5"></path>',
		'crm'     => '<ellipse cx="12" cy="5.5" rx="7" ry="2.5"></ellipse><path d="M5 5.5v6c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5v-6M5 11.5v6c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5v-6"></path>',
		'filter'  => '<path d="M4 5h16l-6.5 7.2V19l-3 1v-7.8z"></path>',
		'search'  => '<circle cx="10.5" cy="10.5" r="5.5"></circle><path d="m15 15 4.5 4.5"></path>',
		'people'  => '<circle cx="9" cy="8" r="3"></circle><circle cx="16.5" cy="9" r="2.3"></circle><path d="M3.5 19c.6-3.4 2.5-5.1 5.5-5.1s5 1.7 5.5 5.1M14.2 14.5c2.8-.3 4.8 1.1 5.5 4.5"></path>',
	];
	if ( empty( $icons[ $kind ] ) ) {
		return '';
	}
	return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $icons[ $kind ] . '</svg>';
};
$flow_stages = [
	[ 'kind' => 'website', 'label' => 'Website', 'mod' => 'website' ],
	[ 'kind' => 'request', 'label' => 'Anfrage', 'mod' => 'request' ],
	[ 'kind' => 'crm', 'label' => 'CRM', 'mod' => 'crm' ],
];
$flow_label = 'Ads, SEO und Empfehlungen führen über die Website zur Anfrage. Die Anfrage wird nach Region, Leistung und Qualität segmentiert und anschließend ins CRM übergeben.';

if ( function_exists( 'hu_enqueue_css' ) ) {
	hu_enqueue_css( 'hu-navigation-ecosystem', 'navigation-ecosystem.css', [ 'nexus-system-css' ] );
	hu_enqueue_css( 'nexus-startseite-css', 'startseite.css', [ 'nexus-system-css' ] );
	hu_enqueue_css( 'nexus-startseite-rest-css', 'startseite-rest-v1.css', [ 'nexus-startseite-css' ] );
}
if ( function_exists( 'hu_enqueue_js' ) ) {
	hu_enqueue_js( 'hu-navigation-ecosystem', 'navigation-ecosystem.js', [] );
}
get_header();
?>

<div class="doku startseite" id="top" data-track-section="homepage">
	<div class="blatt kopfteil">
		<div class="home-hero home-hero-v9">
			<div class="home-hero-copy">
				<p class="gegenstand">WordPress · Tracking · CRM · Pattensen bei Hannover</p>
				<h1><span class="home-title-primary">WordPress Freelancer Hannover.</span><span class="home-title-secondary">Von der Website<br>bis zur Anfrage<span class="home-title-stop">.</span></span></h1>
				<p class="aufriss">Ich entwickle WordPress-Websites, die Angebote verständlich machen und Anfragen sauber bis ins CRM führen. Direkt mit mir – ohne Übergabe an ein fremdes Entwicklerteam.</p>
				<div class="ausgang">
					<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_head_contact" data-track-category="lead_gen" data-track-section="hero">Projekt anfragen <span aria-hidden="true">→</span></a>
					<a class="tun still" href="#angebote" data-track-action="home_hero_to_offers" data-track-category="navigation" data-track-section="hero">Leistungen und Preise</a>
				</div>
				<div class="home-trust-row">
					<span><?php echo esc_html( $response_short ); ?></span>
					<span>Klare Projektpreise</span>
					<span>Direkt mit dem Entwickler</span>
				</div>
				<figure class="home-portrait">
					<img src="<?php echo esc_url( $portrait_url ); ?>" width="480" height="600" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" decoding="async">
					<figcaption><strong>Direkt mit Haşim Üner.</strong><span>Konzeption · Entwicklung · Übergabe</span><a class="satzlink" href="<?php echo esc_url( $about_url ); ?>" data-track-action="home_about" data-track-category="trust" data-track-section="hero">Mehr über Haşim</a></figcaption>
				</figure>
			</div>
			<figure class="home-flow-v9" role="img" aria-label="<?php echo esc_attr( $flow_label ); ?>">
				<div class="home-flow-v9__grid" aria-hidden="true"></div>
				<div class="home-flow-v9__track" aria-hidden="true">
					<span class="home-flow-v9__rail"></span>
					<span class="home-flow-v9__signal"></span>
					<div class="home-flow-v9__sources">
						<span class="home-flow-v9__source home-flow-v9__source--ads">Ads</span>
						<span class="home-flow-v9__source"><?php echo $flow_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG. ?><span>SEO</span></span>
						<span class="home-flow-v9__source"><?php echo $flow_icon( 'people' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG. ?><span>Empfehlung</span></span>
					</div>
					<?php foreach ( $flow_stages as $stage ) : ?>
						<div class="home-flow-v9__stage home-flow-v9__stage--<?php echo esc_attr( $stage['mod'] ); ?>">
							<span class="home-flow-v9__disc"><?php echo $flow_icon( $stage['kind'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG. ?></span>
							<strong><?php echo esc_html( $stage['label'] ); ?></strong>
						</div>
					<?php endforeach; ?>
					<div class="home-flow-v9__filter">
						<span class="home-flow-v9__filter-icon"><?php echo $flow_icon( 'filter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG. ?></span>
						<span class="home-flow-v9__filter-copy"><strong>Segmentierung</strong><span class="home-flow-v9__tags"><span>Region</span><span>Leistung</span><span>Qualität</span></span></span>
					</div>
					<span class="home-flow-v9__arrow home-flow-v9__arrow--a">→</span>
					<span class="home-flow-v9__arrow home-flow-v9__arrow--b">→</span>
				</div>
			</figure>
		</div>
		<div class="meta" id="einordnung">
			<dl>
				<div><dt>Website / Relaunch</dt><dd>Ab <?php echo esc_html( $website_price ); ?></dd></div>
				<div><dt>Zusammenarbeit</dt><dd>Direkt mit mir, ohne Übergabe an ein fremdes Entwicklerteam</dd></div>
				<div><dt>Vor dem Livegang</dt><dd>Prüfbarer Stand auf einer Testumgebung</dd></div>
				<div><dt>Nach der Übergabe</dt><dd>Dokumentation und vereinbarte Zugänge bei Ihnen</dd></div>
			</dl>
		</div>
		<div class="home-specialists" id="wege">
			<p><span class="mono">Für Agenturen</span><a class="satzlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="home_door_whitelabel" data-track-category="navigation" data-track-section="tueren">WordPress-Umsetzung unter Ihrem Namen →</a></p>
			<p><span class="mono">Spezialisierung</span><a class="satzlink" href="<?php echo esc_url( $energy_url ); ?>" data-track-action="home_door_energy" data-track-category="navigation" data-track-section="tueren">Anfragesysteme für Solar und Wärmepumpe →</a></p>
		</div>
	</div>

	<nav class="home-index hu-page-toc home-toc" aria-label="Auf dieser Seite" data-hu-rail="true" data-track-section="page_toc">
		<span class="home-toc__mark" aria-hidden="true"><span class="home-toc__mark-short">TOC</span><span class="home-toc__mark-full">Auf dieser Seite</span></span>
		<div class="home-toc__entries">
			<a href="#angebote" data-track-action="toc_angebote" data-track-category="navigation"><span class="home-toc__nr">01</span><span class="home-toc__txt">Leistungen</span></a>
			<a href="#nachweis" data-track-action="toc_nachweis" data-track-category="navigation"><span class="home-toc__nr">02</span><span class="home-toc__txt">Arbeiten</span></a>
			<a href="#arbeitsweise" data-track-action="toc_arbeitsweise" data-track-category="navigation"><span class="home-toc__nr">03</span><span class="home-toc__txt">Zusammenarbeit</span></a>
			<a href="#fragen" data-track-action="toc_fragen" data-track-category="navigation"><span class="home-toc__nr">04</span><span class="home-toc__txt">Fragen</span></a>
			<a href="#anfrage" data-track-action="toc_anfrage" data-track-category="lead_gen"><span class="home-toc__nr">05</span><span class="home-toc__txt">Anfrage</span></a>
		</div>
	</nav>

	<section id="angebote" aria-labelledby="angebote-h" data-track-section="angebote">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">01</span><span class="titel">Leistungen</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="angebote-h">Drei Kernbereiche. Ein Verantwortungsweg.</h2>
				<p class="vorspann">Website, Anfrage und Messung werden nicht als getrennte Gewerke behandelt. Sie buchen nur den Teil, den Ihr Projekt tatsächlich braucht.</p>
				<div class="home-offers home-offers-v2">
					<?php foreach ( $offers as $offer ) : ?>
						<article class="home-offer" id="<?php echo esc_attr( $offer['id'] ); ?>">
							<div><span class="mono"><?php echo esc_html( $offer['nr'] ); ?></span><h3><?php echo esc_html( $offer['title'] ); ?></h3><p class="home-price"><?php echo esc_html( $offer['price'] ); ?></p></div>
							<div><p class="home-problem"><?php echo esc_html( $offer['problem'] ); ?></p><p><?php echo esc_html( $offer['text'] ); ?></p><p class="home-scope"><?php echo esc_html( $offer['scope'] ); ?></p>
								<a class="textlink" href="<?php echo esc_url( $project_link( $offer['focus'] ) ); ?>" data-track-action="<?php echo esc_attr( 'home_offer_' . $offer['focus'] ); ?>" data-track-category="lead_gen" data-track-section="angebote"><?php echo esc_html( $offer['cta'] ); ?> →</a>
								<?php if ( 'tracking' === $offer['focus'] ) : ?><a class="satzlink home-detail-link" href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="home_proof_tracking_page" data-track-category="proof" data-track-section="angebote">Tracking-Leistung im Detail</a><?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
				<p class="home-note">Preis und Umfang werden vor Projektstart konkret festgelegt. Migrationen, Inhalte, zusätzliche Integrationen, Hosting, Lizenzen und Werbebudget werden nur dann Teil des Projekts, wenn sie ausdrücklich vereinbart sind.</p>
			</div>
		</div>
	</section>

	<section id="nachweis" aria-labelledby="nachweis-h" data-track-section="beweis">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">02</span><span class="titel">Arbeiten</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="nachweis-h">Ausgewählte Arbeiten. Von WordPress bis zum Vertriebsanschluss.</h2>
				<p class="vorspann">Ausgewählte Arbeiten zeigen die Umsetzung. Der B2B-Fall zeigt zusätzlich, wie Website, Anfrage, Messung und CRM zu einer durchgehenden Strecke werden.</p>

				<article class="home-featured-case home-featured-case-v2" id="systemprojekt">
					<div class="home-featured-case__intro">
						<div>
							<p class="mono">Ausgewähltes Großprojekt · B2B · Solar</p>
							<h3>Vom ersten Klick bis zum Vertriebsanschluss.</h3>
						</div>
						<p>Für einen <?php echo esc_html( $e3_case_label_accusative ); ?> entstand eine durchgehende Strecke aus Website, Qualifizierung, Messung, Server-Side Tracking, CRM und Vertriebsübergabe.</p>
					</div>

					<div class="home-system-board" aria-label="Systemarchitektur der umgesetzten B2B-Strecke">
						<div class="home-system-core">
							<div class="home-system-band">
								<div class="home-system-band__label"><span class="mono">Ebene 01</span><strong>Kundenerlebnis</strong></div>
								<div class="home-system-flow">
									<div class="home-system-node"><span class="mono">01</span><strong>Website</strong><small>Einstieg und Vertrauen</small></div>
									<div class="home-system-node"><span class="mono">02</span><strong>Landingpages</strong><small>gezielte Angebotswege</small></div>
									<div class="home-system-node"><span class="mono">03</span><strong>Qualifizierung</strong><small>Formulare und Vorfilter</small></div>
									<div class="home-system-node"><span class="mono">04</span><strong>Anfrage</strong><small>klarer nächster Schritt</small></div>
								</div>
							</div>

							<div class="home-system-band">
								<div class="home-system-band__label"><span class="mono">Ebene 02</span><strong>Messung</strong></div>
								<div class="home-system-measure">
									<span>GA4</span><span>GTM</span><span>Server-Side Tracking</span><span>Attribution</span>
								</div>
							</div>

							<div class="home-system-band">
								<div class="home-system-band__label"><span class="mono">Ebene 03</span><strong>Vertriebsinfrastruktur</strong></div>
								<div class="home-system-flow home-system-flow--sales">
									<div class="home-system-node"><span class="mono">05</span><strong>CRM</strong><small>Lead-Erfassung</small></div>
									<div class="home-system-node"><span class="mono">06</span><strong>Lead-Routing</strong><small>Zuordnung und Übergabe</small></div>
									<div class="home-system-node"><span class="mono">07</span><strong>Vertrieb</strong><small>Anschluss an den Prozess</small></div>
								</div>
							</div>
						</div>

						<aside class="home-system-results" aria-label="Ergebnisse des dokumentierten Falls">
							<div class="home-system-result">
								<span class="mono">Ergebnis 01</span>
								<strong><?php echo esc_html( $e3_metric( 'cpl_reduction', 'display', 'über 85 %' ) ); ?></strong>
								<span>weniger Kosten pro Anfrage</span>
								<small><?php echo esc_html( $e3_metric( 'cpl_before', 'display', '150 €' ) ); ?> → <?php echo esc_html( $e3_metric( 'cpl_after', 'display', '22 €' ) ); ?></small>
							</div>
							<div class="home-system-result">
								<span class="mono">Ergebnis 02</span>
								<strong><?php echo esc_html( $e3_metric( 'lead_count', 'display', '1.750+' ) ); ?></strong>
								<span>qualifizierte Anfragen</span>
								<small>in <?php echo esc_html( $e3_metric( 'timeframe', 'display_dative', '6 Monaten' ) ); ?></small>
							</div>
						</aside>
					</div>

					<div class="home-featured-case__footer">
						<span class="mono">System statt Einzelleistung · Entwicklung, Messung und Übergabe</span>
						<a class="textlink" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="home_work_system_case" data-track-category="proof" data-track-section="beweis">Projektfall und Ergebnisse ansehen →</a>
					</div>
				</article>

				<h3 class="home-reference-heading" id="projekte">Weitere öffentlich einsehbare Arbeiten</h3>
				<p class="home-reference-intro">Hier ist die Website selbst der Beleg. Die Beschreibung nennt meinen Beitrag; der Link zeigt den aktuellen öffentlichen Stand.</p>
				<div class="home-references">
					<?php foreach ( $references as $reference ) : ?>
						<article><p class="mono"><?php echo esc_html( $reference['tag'] ); ?></p><h4><a class="satzlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="home_reference_open" data-track-category="proof" data-track-section="beweis"><?php echo esc_html( $reference['name'] ); ?> ↗</a></h4><p><?php echo esc_html( $reference['text'] ); ?></p></article>
					<?php endforeach; ?>
				</div>
				<div class="home-technical" id="pruefstand">
					<h3>Auch diese Website gehört zum Nachweis.</h3>
					<p>Änderungen und automatische Prüfungen sind öffentlich einsehbar. Eine aktuelle Labormessung können Sie selbst starten; das Ergebnis hängt unter anderem vom Testzeitpunkt ab.</p>
					<ul><li><a class="satzlink" href="<?php echo esc_url( $github_url . '/commits/main/' ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_github_history" data-track-category="proof" data-track-section="beweis">Code und Änderungshistorie ↗</a></li><li><a class="satzlink" href="<?php echo esc_url( $github_url . '/actions' ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_github_ci" data-track-category="proof" data-track-section="beweis">Automatische Prüfungen ↗</a></li><li><a class="satzlink" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_pagespeed" data-track-category="proof" data-track-section="beweis">PageSpeed aktuell messen ↗</a></li></ul>
				</div>
				<div class="ausgang"><a class="textlink" href="<?php echo esc_url( $results_url ); ?>" data-track-action="home_more_results" data-track-category="proof" data-track-section="beweis">Weitere Ergebnisse und Einordnung →</a></div>
			</div>
		</div>
	</section>

	<section id="arbeitsweise" aria-labelledby="arbeitsweise-h" data-track-section="arbeitsweise">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">03</span><span class="titel">Zusammenarbeit</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll" id="strecke">
				<h2 class="kopf" id="arbeitsweise-h">Klarer Scope. Sichtbarer Stand. Saubere Übergabe.</h2>
				<p class="vorspann" id="position">Ich übernehme die technische Umsetzung selbst. Sie wissen vor dem Start, was gebaut wird, sehen den Stand vor der Veröffentlichung und erhalten am Ende einen nachvollziehbaren Übergabepunkt.</p>
				<ol class="home-process home-process-v2" id="ablauf">
					<li><span class="mono">01 · Klären</span><h3>Was wird gebaut – und was nicht?</h3><p>Wir prüfen Ausgangslage, Ziel und Abhängigkeiten. Daraus entstehen Scope, Preis und Zeitrahmen. Zusätzliche Wünsche werden nicht stillschweigend Teil des Projekts.</p></li>
					<li><span class="mono">02 · Bauen</span><h3>Am echten Stand arbeiten.</h3><p>Sie sehen die Umsetzung auf einer Testumgebung. Änderungen bleiben nachvollziehbar und technische Rückfragen landen direkt bei mir.</p></li>
					<li><span class="mono">03 · Prüfen & übergeben</span><h3>Die wichtigen Wege gemeinsam abnehmen.</h3><p>Vor dem Livegang prüfen wir die vereinbarten Funktionen. Danach erhalten Sie Dokumentation, Zugänge und den Stand, mit dem Ihr Team weiterarbeiten kann.</p></li>
				</ol>
				<div class="home-fit home-fit-v2" id="eignung"><h3 id="vergleich">Passt, wenn Verantwortung klar sein soll.</h3><div><p>Sie brauchen einen direkten technischen Ansprechpartner und intern jemanden für Inhalte und Freigaben. Tracking oder CRM werden nur dann mit eingeplant, wenn sie Teil der Aufgabe sind.</p><div class="home-fit-facts"><span>Direkter Ansprechpartner</span><span>Testumgebung vor Livegang</span><span>Dokumentierte Übergabe</span></div></div></div>
			</div>
		</div>
	</section>

	<section id="fragen" aria-labelledby="fragen-h" data-track-section="fragen">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">04</span><span class="titel">Fragen</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll"><h2 class="kopf" id="fragen-h">Was vor dem Start geklärt sein sollte.</h2><div class="fragen">
				<?php foreach ( $faqs as $faq ) : ?>
					<details name="home-faq"><summary><?php echo esc_html( $faq['q'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $faq['a'] ); ?></p></div></div></details>
				<?php endforeach; ?>
			</div></div>
		</div>
		<?php
		// inc/org-schema.php unterdrueckt den Editor-FAQ-Cache auf der Startseite,
		// weil die Fragen hier template-owned sind. Der Knoten wird deshalb hier
		// gebaut — aus demselben Array wie die sichtbaren Antworten, damit Text
		// und Schema nicht auseinanderlaufen koennen.
		$faq_schema = [
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'@id'        => home_url( '/' ) . '#faq',
			'url'        => home_url( '/' ),
			'inLanguage' => 'de',
			'publisher'  => [ '@id' => home_url( '/#organization' ) ],
			'mainEntity' => [],
		];
		foreach ( $faqs as $faq ) {
			$faq_schema['mainEntity'][] = [
				'@type'          => 'Question',
				'name'           => $faq['q'],
				'acceptedAnswer' => [
					'@type' => 'Answer',
					'text'  => $faq['a'],
				],
			];
		}
		?>
		<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	</section>

	<div class="abschluss" id="anfrage" data-track-section="abschluss" tabindex="-1">
		<div class="blatt" id="kontakt"><div class="tafel home-close home-close-v2">
			<div><p class="mono">Der nächste Schritt</p><h2>Was soll als Nächstes besser funktionieren?</h2><p class="aufriss">Schicken Sie mir Ausgangslage, Engpass und Ziel. Ich prüfe die Aufgabe selbst und melde mich mit einer ersten fachlichen Einordnung – ohne Vertriebsübergabe und ohne fertiges Briefing.</p><div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_close_contact" data-track-category="lead_gen" data-track-section="abschluss">Projekt beschreiben <span aria-hidden="true">→</span></a><a class="tun still" href="<?php echo esc_url( 'mailto:' . $contact_email, [ 'mailto' ] ); ?>" data-track-action="home_close_mail" data-track-category="lead_gen" data-track-section="abschluss">Per E-Mail anfragen</a></div></div>
			<div class="home-close-note"><strong><?php echo esc_html( $response ); ?></strong><p>Direkt bei mir. Kein gebuchter Termin und kein fertiges Briefing nötig.</p></div>
		</div></div>
	</div>
</div>
<?php get_footer(); ?>