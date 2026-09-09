<?php
/**
 * Template Name: WordPress Freelancer Hannover
 * Template Post Type: page
 *
 * Direct projects: WordPress, measurement and inquiry handoff.
 * Route: /wordpress-freelancer-hannover/
 *
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$asset_path  = get_stylesheet_directory() . '/assets/css/wordpress-freelancer-hannover.css';
$script_path = get_stylesheet_directory() . '/assets/js/wordpress-freelancer-hannover.js';
wp_enqueue_style(
	'hu-wordpress-freelancer-hannover',
	get_stylesheet_directory_uri() . '/assets/css/wordpress-freelancer-hannover.css',
	[ 'nexus-design-system' ],
	function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $asset_path ) : wp_get_theme()->get( 'Version' )
);
wp_enqueue_script(
	'hu-wordpress-freelancer-hannover',
	get_stylesheet_directory_uri() . '/assets/js/wordpress-freelancer-hannover.js',
	[],
	function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $script_path ) : wp_get_theme()->get( 'Version' ),
	true
);

// This template renders no editor blocks or booking links. Run after enqueues.
add_action( 'wp_enqueue_scripts', static function () {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_script( 'nexus-cal-embed-js' );
}, 100 );
add_filter( 'body_class', static function ( $classes ) {
	$classes[] = 'hu-wordpress-freelancer-page';
	return $classes;
} );
add_filter( 'hu_forced_singular_seo_map', static function ( $map ) {
	$map['wordpress-freelancer-hannover'] = [
		'title'       => 'WordPress Freelancer Hannover | Entwicklung & Tracking',
		'description' => 'WordPress Freelancer aus Hannover für Relaunch, Landingpages, Weiterentwicklung und Tracking. Direkter Ansprechpartner, klarer Scope und versionierter Code.',
	];
	return $map;
} );

$whitelabel_url   = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );
$tracking_url     = home_url( '/server-side-tracking-b2b/' );
$about_url        = home_url( '/hasim-uener/' );
$e3_case_url      = home_url( '/case-study-solar-leadgenerierung/' );
$privacy_url      = home_url( '/datenschutz/' );
$github_url       = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$pagespeed_url    = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/wordpress-freelancer-hannover/' ) );
$rest_endpoint    = rest_url( 'nexus/v1/contact-request' );
$portrait_url     = get_stylesheet_directory_uri() . '/assets/img/hasim-freelancer-relaxed-640x800.webp';
$contact_email    = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$contact_phone    = function_exists( 'hu_get_contact_phone' ) ? hu_get_contact_phone() : '0176 76596580';
$contact_tel      = function_exists( 'hu_get_contact_phone' ) ? hu_get_contact_phone( 'link' ) : 'tel:+4917676596580';
$website_price    = function_exists( 'hu_freelancer_website_price' ) ? hu_freelancer_website_price( true ) : '3.400 € netto';
$tracking_price   = function_exists( 'hu_tracking_price' ) ? hu_tracking_price( 'standard', 'setup', 'display', '1.290 €' ) : '1.290 €';
$response_promise = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'phrase' ) : 'Antwort innerhalb von 24 Stunden werktags';
$e3_cpl_before    = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before' ) : '150 €';
$e3_cpl_after     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after' ) : '22 €';
$e3_leads         = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count' ) : '1.750+';
$e3_timeframe     = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'timeframe', 'display_dative' ) : '6 Monaten';
$references = [
	[ 'name' => 'civaka-azad.org', 'url' => 'https://civaka-azad.org/', 'tag' => 'Informationsarchitektur', 'text' => 'Gewachsener redaktioneller Bestand, Navigation und auffindbare Archive.' ],
	[ 'name' => 'hasimuener.org', 'url' => 'https://hasimuener.org/', 'tag' => 'Eigenes Projekt · Editorial Design', 'text' => 'Typografie, Raster und Lesefluss für eine eigene redaktionelle Website.' ],
	[ 'name' => 'kurdischer-rat.org', 'url' => 'https://kurdischer-rat.org/', 'tag' => 'Organisation · Workflow', 'text' => 'Informationshierarchie und ein versionierter Prozess für die Veröffentlichung.' ],
];
$faqs = [
	[ 'q' => 'Übernehmen Sie eine bestehende WordPress-Website?', 'a' => 'Ja. Vor einer Zusage prüfe ich Theme, Plugins, Zugänge und die konkrete Aufgabe. Daraus wird eine begrenzte Weiterentwicklung, eine technische Bereinigung oder ein Relaunch. Ein Neubau ist keine Voraussetzung.' ],
	[ 'q' => 'Arbeiten Sie auch mit unserem Page Builder?', 'a' => 'Die Entscheidung hängt vom vorhandenen Aufbau und der Aufgabe ab. Ein funktionierender Editor muss nicht ersetzt werden. Wo Performance, Wartbarkeit oder eine Integration an Grenzen stoßen, klären wir den nötigen Eingriff vorab.' ],
	[ 'q' => 'Was brauchen Sie von unserem Team?', 'a' => 'Eine Person, die Entscheidungen trifft, die nötigen Zugänge sowie vorhandene Texte und Gestaltungsvorgaben. Fehlende Inhalte, Branding, Übersetzungen und zusätzliche Schnittstellen klären wir im Angebot. Sie sind nicht automatisch im Einstiegspreis enthalten.' ],
	[ 'q' => 'Wann kann das Projekt starten und wie lange dauert es?', 'a' => 'Das hängt von meiner freien Kapazität, dem Umfang und Ihren Vorarbeiten ab. Nach der ersten Einordnung erhalten Sie einen realistischen Zeitrahmen. Inhaltslieferung, Freigaben und technische Abhängigkeiten werden dabei berücksichtigt.' ],
	[ 'q' => 'Gehören Website, Konten und Code anschließend uns?', 'a' => 'Ja. Code, Repository, Hosting und eingesetzte Konten liegen in Ihrer Hand. Zur Übergabe gehören die vereinbarte Dokumentation und Zugänge. Eine weitere Betreuung ist möglich, aber keine Voraussetzung dafür, die Website weiterzuführen.' ],
	[ 'q' => 'Müssen wir aus Hannover kommen?', 'a' => 'Nein. Ich arbeite aus Pattensen in der Region Hannover und betreue Projekte remote im gesamten DACH-Raum. Abstimmung und Abnahme funktionieren über einen gemeinsamen, dokumentierten Projektstand.' ],
];
get_header();
?>

<div class="hu-fr" data-track-page="wordpress_freelancer_hannover">
	<section class="hu-fr-hero" aria-labelledby="hu-fr-title">
		<div class="hu-fr__shell hu-fr-hero__grid">
			<div class="hu-fr-hero__copy">
				<p class="hu-fr__eyebrow">Haşim Üner · WordPress-Entwickler · Region Hannover</p>
				<h1 id="hu-fr-title">WordPress Freelancer Hannover, der <em>Entwicklung und Messung verbindet.</em></h1>
				<p class="hu-fr__lead">Ich entwickle WordPress-Websites, Relaunches und Landingpages. Wo es zum Projekt gehört, verbinde ich Formular, Tracking und CRM direkt mit der Umsetzung – statt diese Übergänge später zwischen mehreren Dienstleistern zu reparieren.</p>
				<div class="hu-fr__actions">
					<a class="hu-fr__button hu-fr__button--primary" href="#anfrage" data-track-action="cta_freelancer_hero_project" data-track-category="lead_gen" data-track-section="hero">Projekt anfragen <span class="hu-fr__arrow" aria-hidden="true"></span></a>
					<a class="hu-fr__text-link" href="#nachweis">Projektbeispiel ansehen <span aria-hidden="true">↓</span></a>
				</div>
				<ul class="hu-fr-hero__assurances"><li>Direkter Kontakt zum Entwickler</li><li>Staging vor dem Livegang</li><li>Code &amp; Konten gehören Ihnen</li></ul>
			</div>
			<figure class="hu-fr-portrait">
				<img src="<?php echo esc_url( $portrait_url ); ?>" width="640" height="800" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" fetchpriority="high" decoding="async">
				<figcaption><strong>Haşim Üner</strong><span>Pattensen bei Hannover · remote im DACH-Raum</span><a href="<?php echo esc_url( $about_url ); ?>">Mehr über mich <span class="hu-fr__arrow" aria-hidden="true"></span></a></figcaption>
			</figure>
		</div>
	</section>
	<div class="hu-fr-body">
	<div class="hu-fr-nav-slot">
		<nav class="hu-fr-nav" aria-label="Auf dieser Seite">
			<button class="hu-fr-nav__toggle" type="button" aria-expanded="false" aria-controls="hu-fr-nav-links" hidden><span>Auf dieser Seite</span><span class="hu-fr-nav__current" aria-hidden="true">Übersicht</span><span class="hu-fr-nav__chevron" aria-hidden="true">+</span></button>
			<div class="hu-fr-nav__heading" aria-hidden="true"><span>Index</span><span class="hu-fr-nav__hint">Auf dieser Seite</span></div>
			<div class="hu-fr-nav__links" id="hu-fr-nav-links">
				<a href="#nachweis" aria-label="Nachweis"><span class="hu-fr-nav__number" aria-hidden="true">01</span><span class="hu-fr-nav__label">Nachweis</span></a>
				<a href="#angebote" aria-label="Leistungen &amp; Preise"><span class="hu-fr-nav__number" aria-hidden="true">02</span><span class="hu-fr-nav__label">Leistungen &amp; Preise</span></a>
				<a href="#strecke" aria-label="Anfrageweg"><span class="hu-fr-nav__number" aria-hidden="true">03</span><span class="hu-fr-nav__label">Anfrageweg</span></a>
				<a href="#arbeitsweise" aria-label="Zusammenarbeit"><span class="hu-fr-nav__number" aria-hidden="true">04</span><span class="hu-fr-nav__label">Zusammenarbeit</span></a>
				<a href="#fragen" aria-label="Fragen"><span class="hu-fr-nav__number" aria-hidden="true">05</span><span class="hu-fr-nav__label">Fragen</span></a>
				<a href="#anfrage" aria-label="Projekt anfragen"><span class="hu-fr-nav__number" aria-hidden="true">06</span><span class="hu-fr-nav__label">Projekt anfragen <span class="hu-fr__arrow" aria-hidden="true"></span></span></a>
			</div>
		</nav>
	</div>
	<div class="hu-fr-content">

	<section class="hu-fr-section hu-fr-proof" id="nachweis" aria-labelledby="hu-fr-proof-title">
		<div class="hu-fr__shell hu-fr__split">
			<div><p class="hu-fr__eyebrow">01 / Ausgewähltes Großprojekt</p><h2 id="hu-fr-proof-title">Vom Lead-Einkauf zum eigenen Anfragesystem.</h2><a class="hu-fr__text-link" href="<?php echo esc_url( $e3_case_url ); ?>">Umsetzung und Ergebnisse ansehen <span class="hu-fr__arrow" aria-hidden="true"></span></a></div>
			<div>
				<p>Eines meiner umfangreichsten Projekte: Für einen mittelständischen PV-Installationsbetrieb wurden Website, Landingpages, Tracking und laufende Optimierung zu einer durchgängigen Anfragestrecke verbunden. Weitere öffentlich prüfbare Arbeiten finden Sie weiter unten.</p>
				<dl class="hu-fr-metrics">
					<div><dt>Vorher: eingekaufte Leads</dt><dd><?php echo esc_html( $e3_cpl_before ); ?></dd><dd class="hu-fr-metrics__unit">pro Lead</dd></div>
					<div><dt>Nach der Aufbauphase</dt><dd><?php echo esc_html( $e3_cpl_after ); ?></dd><dd class="hu-fr-metrics__unit">pro eigener Anfrage</dd></div>
					<div><dt>Qualifizierte Anfragen</dt><dd><?php echo esc_html( $e3_leads ); ?></dd><dd class="hu-fr-metrics__unit">in <?php echo esc_html( $e3_timeframe ); ?></dd></div>
				</dl>
				<p class="hu-fr__note">Ergebnis des gesamten Systems einschließlich Kampagnen und Optimierung. Der Vergleich beschreibt unterschiedliche Wege der Leadgewinnung; er isoliert keinen WordPress-Effekt und ist keine Ergebnisgarantie für andere Projekte.</p>
			</div>
		</div>
	</section>

	<section class="hu-fr-section hu-fr-section--paper" id="angebote" aria-labelledby="hu-fr-offers-title">
		<div class="hu-fr__shell">
			<div class="hu-fr__section-head" id="einordnung"><p class="hu-fr__eyebrow">02 / Leistungen &amp; Preise</p><h2 id="hu-fr-offers-title">Was soll sich konkret verbessern?</h2><p>Neubau, Kampagnenstrecke, Tracking oder laufende Weiterentwicklung: Der Einstieg richtet sich nach der Aufgabe. Umfang, Abhängigkeiten und Abnahme stehen vor dem Start fest.</p></div>
			<div class="hu-fr-offers">
				<article class="hu-fr-offer" id="angebot-website">
					<div class="hu-fr-offer__heading"><span class="hu-fr__index" aria-hidden="true">01</span><h3>Website neu oder Relaunch</h3><p class="hu-fr-offer__price">ab <?php echo esc_html( $website_price ); ?></p></div>
					<div><p>Für Unternehmen, deren Website neu aufgebaut werden soll oder technisch und inhaltlich nicht mehr zum Angebot passt.</p><ul><li>WordPress-Aufbau mit pflegbaren Seiten und klarer Navigation</li><li>Performance, technisches SEO und Messkonzept von Beginn an</li><li>Vorschau zur Abnahme, kontrollierter Livegang und Übergabe</li></ul><p><strong>Ergebnis:</strong> eine pflegbare WordPress-Website mit klarer technischer Übergabe und vorbereiteter Messung.</p><p class="hu-fr__note">Einstieg für einen klar begrenzten Unternehmensauftritt. Seitenumfang, Inhalte, Migration und Integrationen bestimmen das konkrete Angebot.</p></div>
					<a class="hu-fr__text-link" href="#anfrage" data-fr-project-focus="relaunch" data-track-action="cta_freelancer_offer_website" data-track-category="lead_gen" data-track-section="offers">Website anfragen <span class="hu-fr__arrow" aria-hidden="true"></span></a>
				</article>
				<article class="hu-fr-offer" id="angebot-funnel">
					<div class="hu-fr-offer__heading"><span class="hu-fr__index" aria-hidden="true">02</span><h3>Anfragestrecke für eine Kampagne</h3><p class="hu-fr-offer__price">Nach vereinbartem Umfang</p></div>
					<div><p>Sie investieren in Ads oder SEO. Jetzt müssen Seite, Formular und Übergabe zu Ihrem Angebot und Vertrieb passen.</p><ul><li>Landingpage und Formular mit den nötigen Qualifizierungsfragen</li><li>Bestätigung und vereinbarte Übergabe an Postfach oder CRM</li><li>Messung der vereinbarten Anfrageziele und Prüfung der Datenwege</li></ul><p><strong>Ergebnis:</strong> ein durchgängiger Anfrageweg von der Landingpage bis zum vereinbarten Empfänger.</p></div>
					<a class="hu-fr__text-link" href="#anfrage" data-fr-project-focus="conversion" data-track-action="cta_freelancer_offer_funnel" data-track-category="lead_gen" data-track-section="offers">Anfrageweg besprechen <span class="hu-fr__arrow" aria-hidden="true"></span></a>
				</article>
				<article class="hu-fr-offer" id="angebot-tracking">
					<div class="hu-fr-offer__heading"><span class="hu-fr__index" aria-hidden="true">03</span><h3>Tracking &amp; Attribution nachrüsten</h3><p class="hu-fr-offer__price">ab <?php echo esc_html( $tracking_price ); ?> netto</p><p class="hu-fr__note">Einrichtung; laufende Betreuung separat.</p></div>
					<div><p>Ihre Website steht, aber Sie wissen nicht zuverlässig, welche Quellen und Kampagnen Anfragen bringen.</p><ul><li>GA4, Google Tag Manager und definierte Anfrageziele</li><li>Prüfung von Auslösung, Einwilligung und Quellenzuordnung</li><li>Server-Side Tracking und weitere Integrationen je nach Setup</li></ul><p><strong>Ergebnis:</strong> eine nachvollziehbare Messkette für die vereinbarten Anfrageziele.</p><a class="hu-fr__inline-link" href="<?php echo esc_url( $tracking_url ); ?>">Details zu Tracking und Paketen <span class="hu-fr__arrow" aria-hidden="true"></span></a></div>
					<a class="hu-fr__text-link" href="#anfrage" data-fr-project-focus="tracking" data-track-action="cta_freelancer_offer_tracking" data-track-category="lead_gen" data-track-section="offers">Tracking anfragen <span class="hu-fr__arrow" aria-hidden="true"></span></a>
				</article>
				<article class="hu-fr-offer hu-fr-offer--compact" id="angebot-weiterentwicklung">
					<div class="hu-fr-offer__heading"><span class="hu-fr__index" aria-hidden="true">04</span><h3>Planbare Weiterentwicklung</h3><p class="hu-fr-offer__price">Nach Kapazität und Aufgaben</p></div>
					<div><p>Für wiederkehrende Verbesserungen an einer bestehenden Website. Wir vereinbaren Prioritäten, verfügbare Kapazität und Abnahme. Neue Aufgaben bleiben im Änderungsverlauf nachvollziehbar.</p><p><strong>Ergebnis:</strong> priorisierte Weiterentwicklung mit dokumentierten Änderungen und klaren Abnahmen.</p></div>
					<a class="hu-fr__text-link" href="#anfrage" data-fr-project-focus="implementation_scope" data-track-action="cta_freelancer_offer_retainer" data-track-category="lead_gen" data-track-section="offers">Aufgaben besprechen <span class="hu-fr__arrow" aria-hidden="true"></span></a>
				</article>
			</div>
			<p class="hu-fr__note hu-fr-offers__note">Alle Preise verstehen sich netto. Hosting, Lizenzen und laufende Drittanbieter-Kosten werden separat ausgewiesen. Vor Beginn halten wir Leistungen, Ausschlüsse und Abnahmekriterien schriftlich fest.</p>
		</div>
	</section>

	<section class="hu-fr-section hu-fr-system" id="strecke" aria-labelledby="hu-fr-system-title">
		<div class="hu-fr__shell">
			<div class="hu-fr__section-head"><p class="hu-fr__eyebrow">03 / Vom ersten Klick zur Übergabe</p><h2 id="hu-fr-system-title">Die Website ist ein Teil der Strecke.</h2><p>Eine technisch saubere Website kann trotzdem Anfragen verlieren, wenn Formular, Messung oder Übergabe nicht mitgedacht sind. Deshalb plane ich die entscheidenden Übergänge mit, sofern sie zum Projekt gehören.</p></div>
			<ol class="hu-fr-route">
				<li><span class="hu-fr__index">01 / Einstieg</span><h3>Suche oder Anzeige</h3><p>Mit welchem Anliegen kommt der Besucher auf die Seite?</p></li>
				<li><span class="hu-fr__index">02 / Seite</span><h3>Angebot verstehen</h3><p>Inhalt, Tempo und nächster Schritt passen zur Erwartung.</p></li>
				<li><span class="hu-fr__index">03 / Formular</span><h3>Anfrage einordnen</h3><p>Die nötigen Angaben abfragen und die Absendung bestätigen.</p></li>
				<li><span class="hu-fr__index">04 / Übergabe</span><h3>Team kann reagieren</h3><p>Die vereinbarten Informationen kommen im Postfach oder CRM an.</p></li>
			</ol>
			<div class="hu-fr-measurement"><strong>Messung über die Strecke</strong><p>Quelle → erlaubte Datenerfassung → bestätigte Anfrage. Eine Rückmeldung aus dem Vertrieb kann ergänzen, welche Anfragen tatsächlich passen.</p></div>
			<p class="hu-fr__note">Beispiel für ein Abnahmekriterium: Eine Testanfrage kommt mit den vereinbarten Angaben beim richtigen Empfänger an; ihre Erfassung wird geprüft. Welche Schritte und Integrationen ich übernehme, steht im Projektumfang.</p>
		</div>
	</section>

	<section class="hu-fr-section" id="arbeitsweise" aria-labelledby="hu-fr-work-title">
		<div class="hu-fr__shell">
			<div class="hu-fr__split" id="ablauf">
				<div><p class="hu-fr__eyebrow">04 / Zusammenarbeit</p><h2 id="hu-fr-work-title">Sie sprechen mit dem, der es baut.</h2><p>Von der technischen Einordnung bis zur Abnahme arbeite ich direkt mit Ihnen. Entscheidungen und Änderungen bleiben dokumentiert.</p></div>
				<ol class="hu-fr-process">
					<li><h3>Aufgabe und Grenzen klären</h3><p>Was soll sich verbessern? Was ist vorhanden? Wir halten Umfang, Abhängigkeiten und Abnahmekriterien vor dem Start fest.</p></li>
					<li><h3>Versioniert entwickeln und prüfen</h3><p>Änderungen laufen über Git. Sie sehen den Stand auf einer Testumgebung und geben die vereinbarte Umsetzung vor dem Livegang frei.</p></li>
					<li><h3>Kontrolliert veröffentlichen und übergeben</h3><p>Code, Hosting, Konten und Repository gehören Ihnen. Dokumentation und vereinbarte Prüfungen machen eine spätere Weiterentwicklung möglich.</p></li>
				</ol>
			</div>
			<div class="hu-fr-evidence"><div><p class="hu-fr__eyebrow">Einsehbarer Arbeitsnachweis</p><h3>Diese Website hat einen öffentlichen Änderungsverlauf.</h3><p>Im Repository sehen Sie Codeänderungen und die automatisierten Prüfungen dieser Website, darunter PHP-Syntax sowie Regeln für CSS und deutsche Texte.</p></div><div class="hu-fr-evidence__links"><a class="hu-fr__text-link" href="<?php echo esc_url( $github_url . '/commits/main/' ); ?>" target="_blank" rel="noopener noreferrer">Änderungsverlauf auf GitHub <span class="hu-fr__arrow" aria-hidden="true"></span></a><a class="hu-fr__text-link" href="<?php echo esc_url( $github_url . '/blob/main/.github/workflows/ci.yml' ); ?>" target="_blank" rel="noopener noreferrer">Automatisierte Prüfungen ansehen <span class="hu-fr__arrow" aria-hidden="true"></span></a><a class="hu-fr__text-link" href="<?php echo esc_url( $pagespeed_url ); ?>" target="_blank" rel="noopener noreferrer">Diese Seite mit PageSpeed prüfen <span class="hu-fr__arrow" aria-hidden="true"></span></a></div></div>
			<details class="hu-fr-fit" id="vergleich"><summary>Freelancer, Agentur oder Baukasten: Wann passt welches Modell?</summary><div id="position"><dl><div><dt>Direkt mit mir</dt><dd>Sie brauchen technische Umsetzung mit klarer Verantwortung und können Prioritäten und Freigaben in Ihrem Team klären.</dd></div><div><dt>Agentur oder größeres Team</dt><dd>Sie brauchen garantierte Vertretung, 24/7-Bereitschaft oder mehrere Gewerke gleichzeitig mit hoher Kapazität.</dd></div><div><dt>Baukasten</dt><dd>Eine einfache Präsenzseite ohne besondere Integrationen und Messanforderungen reicht Ihnen.</dd></div></dl></div></details>
			<div class="hu-fr-projects" id="projekte"><h3>Weitere öffentlich prüfbare Arbeiten</h3><p>Der Case oben zeigt ein umfangreiches Projekt in der Tiefe. Diese Arbeiten zeigen weitere Schwerpunkte – direkt auf den jeweiligen Websites prüfbar.</p><div class="hu-fr-projects__grid">
				<?php foreach ( $references as $reference ) : ?>
					<article><p class="hu-fr__eyebrow"><?php echo esc_html( $reference['tag'] ); ?></p><h4><a href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $reference['name'] ); ?> <span class="hu-fr__arrow" aria-hidden="true"></span></a></h4><p><?php echo esc_html( $reference['text'] ); ?></p></article>
				<?php endforeach; ?>
			</div></div>
			<aside class="hu-fr-evidence" aria-labelledby="hu-fr-agency-title"><div><p class="hu-fr__eyebrow">Für Agenturen</p><h3 id="hu-fr-agency-title">Technische Delivery im Hintergrund.</h3><p>WordPress-, Tracking- und technische Umsetzung übernehme ich auch White-Label. Sie bleiben Ansprechpartner für Ihren Kunden; Scope, Übergabe und Zusammenarbeit werden vor dem Start geklärt.</p></div><div><a class="hu-fr__text-link" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="cta_freelancer_agency_bridge" data-track-category="segmentation" data-track-section="arbeitsweise">White-Label-Zusammenarbeit ansehen <span class="hu-fr__arrow" aria-hidden="true"></span></a></div></aside>
		</div>
	</section>

	<section class="hu-fr-section hu-fr-section--paper" id="fragen" aria-labelledby="hu-fr-faq-title">
		<div class="hu-fr__shell hu-fr__split"><div><p class="hu-fr__eyebrow">05 / Vor einer Beauftragung</p><h2 id="hu-fr-faq-title">Was Sie vorab wissen sollten.</h2></div><div class="hu-fr-faq">
			<?php foreach ( $faqs as $faq ) : ?>
				<details><summary><?php echo esc_html( $faq['q'] ); ?></summary><p><?php echo esc_html( $faq['a'] ); ?></p></details>
			<?php endforeach; ?>
		</div></div>
	</section>

	<section class="hu-fr-section hu-fr-inquiry" id="anfrage" tabindex="-1" aria-labelledby="hu-fr-inquiry-title">
		<div class="hu-fr__shell hu-fr__split">
			<div><p class="hu-fr__eyebrow">06 / Ihr Projekt</p><h2 id="hu-fr-inquiry-title">Was möchten Sie bauen oder verbessern?</h2><p>Beschreiben Sie Ausgangslage und Ziel in wenigen Sätzen. Ich ordne ein, ob ich die Aufgabe übernehmen kann und was wir als Nächstes klären sollten.</p><p class="hu-fr-inquiry__promise"><?php echo esc_html( $response_promise ); ?>.</p><div class="hu-fr-contact"><span>Direkt per E-Mail oder Telefon</span><a href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a><a href="<?php echo esc_url( $contact_tel ); ?>"><?php echo esc_html( $contact_phone ); ?></a></div></div>
			<div>
				<noscript><p class="hu-fr-form__fallback">Für das Formular wird JavaScript benötigt. Senden Sie mir Ihre Ausgangslage und Ihr Ziel einfach per E-Mail an <a href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>.</p></noscript>
				<form class="hu-fr-form" data-fr-form action="<?php echo esc_url( $rest_endpoint ); ?>" method="post" novalidate hidden>
					<input type="hidden" name="request_type" value="project">
					<div class="hu-fr-form__honeypot" aria-hidden="true"><label>Website <input type="text" name="company_website" tabindex="-1" autocomplete="off"></label></div>
					<div class="hu-fr-form__step" data-fr-form-step="brief">
						<p class="hu-fr-form__step-label">Schritt 1 von 2 · Ihr Vorhaben</p>
						<label for="hu-fr-name">Name / Ansprechpartner <span>(Pflichtfeld)</span></label><input id="hu-fr-name" type="text" name="name" autocomplete="name" required>
						<label for="hu-fr-focus">Worum geht es? <span>(Pflichtfeld)</span></label><select id="hu-fr-focus" name="focus" required><option value="">Bitte wählen</option><option value="relaunch">Website neu oder Relaunch</option><option value="implementation_scope">Weiterentwicklung / konkrete Umsetzung</option><option value="tracking">Tracking &amp; Analytics</option><option value="conversion">Conversion &amp; Anfrageweg</option><option value="website_strategy">Positionierung / Seitenbotschaft</option></select>
						<label for="hu-fr-message">Ausgangslage und Ziel <span>(Pflichtfeld)</span></label><p id="hu-fr-message-help" class="hu-fr-form__help">Was planen Sie, und was soll besser funktionieren? Zwei bis drei Sätze reichen, mindestens 24 Zeichen.</p><textarea id="hu-fr-message" name="message" rows="4" minlength="24" required aria-describedby="hu-fr-message-help"></textarea>
						<button class="hu-fr__button hu-fr__button--primary" type="button" data-fr-form-next data-track-action="cta_freelancer_form_next" data-track-category="lead_gen" data-track-section="inquiry">Weiter zur E-Mail <span aria-hidden="true">→</span></button>
					</div>
					<div class="hu-fr-form__step" data-fr-form-step="contact" hidden>
						<div class="hu-fr-form__backline"><button type="button" data-fr-form-back>← Angaben ändern</button><span>Schritt 2 von 2</span></div>
						<label for="hu-fr-email">E-Mail <span>(Pflichtfeld)</span></label><input id="hu-fr-email" type="email" name="email" autocomplete="email" required>
						<label class="hu-fr-form__consent"><input type="checkbox" name="consent" value="1" required><span>Ich stimme der Verarbeitung meiner Angaben zur Bearbeitung der Anfrage zu. <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>.</span></label>
						<button class="hu-fr__button hu-fr__button--primary" type="submit" data-fr-form-submit data-track-action="cta_freelancer_form_submit" data-track-category="lead_gen" data-track-section="inquiry">Projekt anfragen <span class="hu-fr__arrow" aria-hidden="true"></span></button>
					</div>
					<p class="hu-fr-form__status" id="hu-fr-form-status" data-fr-form-status tabindex="-1" role="status" aria-live="polite" aria-atomic="true"></p>
				</form>
			</div>
		</div>
	</section>
	</div>
	</div>
</div>

<?php get_footer(); ?>
