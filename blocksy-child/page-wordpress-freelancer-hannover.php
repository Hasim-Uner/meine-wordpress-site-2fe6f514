<?php
/**
 * Template Name: WordPress Freelancer Hannover
 * Template Post Type: page
 *
 * Direkte Money-Page fuer WordPress-Projekte: Entwicklung, Tracking und
 * Conversion als zusammenhaengende Strecke.
 * Route: /wordpress-freelancer-hannover/
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$asset_path = get_stylesheet_directory() . '/assets/css/wordpress-freelancer-hannover.css';
$asset_url  = get_stylesheet_directory_uri() . '/assets/css/wordpress-freelancer-hannover.css';
$asset_ver  = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $asset_path ) : wp_get_theme()->get( 'Version' );

wp_enqueue_style(
	'hu-wordpress-freelancer-hannover',
	$asset_url,
	[ 'nexus-design-system' ],
	$asset_ver
);

$polish_path = get_stylesheet_directory() . '/assets/css/wordpress-freelancer-hannover-polish.css';
$polish_url  = get_stylesheet_directory_uri() . '/assets/css/wordpress-freelancer-hannover-polish.css';
if ( file_exists( $polish_path ) ) {
	$polish_ver = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $polish_path ) : wp_get_theme()->get( 'Version' );
	wp_enqueue_style(
		'hu-wordpress-freelancer-hannover-polish',
		$polish_url,
		[ 'hu-wordpress-freelancer-hannover' ],
		$polish_ver
	);
}

$script_path = get_stylesheet_directory() . '/assets/js/wordpress-freelancer-hannover.js';
$script_url  = get_stylesheet_directory_uri() . '/assets/js/wordpress-freelancer-hannover.js';
$script_ver  = function_exists( 'hu_get_asset_version' ) ? hu_get_asset_version( $script_path ) : wp_get_theme()->get( 'Version' );

wp_enqueue_script(
	'hu-wordpress-freelancer-hannover',
	$script_url,
	[],
	$script_ver,
	true
);

wp_dequeue_style( 'wp-block-library' );
wp_dequeue_style( 'wp-block-library-theme' );

add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'hu-wordpress-freelancer-page';
		return $classes;
	}
);

add_filter(
	'hu_forced_singular_seo_map',
	static function ( $map ) {
		$map['wordpress-freelancer-hannover'] = [
			'title'       => 'WordPress Freelancer Hannover | Haşim Üner',
			'description' => 'WordPress Freelancer in Hannover für individuelle Entwicklung, Tracking und Conversion. Direkte Zusammenarbeit, versionierter Code und klarer Scope.',
		];
		return $map;
	}
);

$project_url = function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: add_query_arg( [ 'type' => 'project', 'focus' => 'followup_scope' ], home_url( '/kontakt/' ) );

$agentur_url    = home_url( '/wordpress-agentur-hannover/' );
$whitelabel_url = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );
$tracking_url   = home_url( '/server-side-tracking-b2b/' );
$about_url      = home_url( '/hasim-uener/' );
$e3_case_url    = home_url( '/case-study-solar-leadgenerierung/' );
$privacy_url    = home_url( '/datenschutz/' );
$github_url     = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$rest_endpoint  = rest_url( 'nexus/v1/contact-request' );
$portrait_url   = get_stylesheet_directory_uri() . '/assets/img/hasim-freelancer-relaxed-640x800.webp';

$website_price     = function_exists( 'hu_freelancer_website_price' ) ? hu_freelancer_website_price() : '3.400 €';
$website_price_net = function_exists( 'hu_freelancer_website_price' ) ? hu_freelancer_website_price( true ) : '3.400 € netto';
$tracking_price    = function_exists( 'hu_tracking_price' ) ? hu_tracking_price( 'standard', 'setup', 'display', '1.290 €' ) : '1.290 €';
$response_promise  = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'phrase' ) : 'Antwort innerhalb von 24 Stunden werktags';

$e3_cpl_before                 = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_before' ) : '150 €';
$e3_cpl_after                  = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'cpl_after' ) : '22 €';
$e3_leads                      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'lead_count' ) : '1.750+';
$lighthouse_mobile_performance = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'freelancer_lighthouse_mobile_performance' ) : '99/100';
$lighthouse_accessibility      = function_exists( 'hu_e3_metric' ) ? hu_e3_metric( 'freelancer_lighthouse_accessibility' ) : '100/100';

$references = [
	[
		'name' => 'civaka-azad.org',
		'url'  => 'https://civaka-azad.org/',
		'tag'  => 'Informationsarchitektur',
		'text' => 'Gewachsener redaktioneller Bestand, klare Navigation und auffindbare Archive.',
	],
	[
		'name' => 'e3-newenergy.de',
		'url'  => 'https://e3-newenergy.de/',
		'tag'  => 'WordPress · Funnel · Tracking',
		'text' => 'Erklärungsbedürftiges Angebot mit Anfrageweg, Tracking und Conversion als System.',
	],
	[
		'name' => 'hasimuener.org',
		'url'  => 'https://hasimuener.org/',
		'tag'  => 'Editorial Design',
		'text' => 'Typografie, Raster und Lesefluss als tragende Gestaltung — ohne dekorative Überladung.',
	],
	[
		'name' => 'kurdischer-rat.org',
		'url'  => 'https://kurdischer-rat.org/',
		'tag'  => 'Organisation · Workflow',
		'text' => 'Informationshierarchie mit versioniertem, kontrolliertem Deployment-Prozess.',
	],
];

$faqs = [
	[
		'q' => 'Arbeiten Sie mit Elementor?',
		'a' => 'Ja, wenn es für Redaktion und Betrieb sinnvoll ist. Standardmäßig bevorzuge ich die schlankste Lösung, die das Projekt wirklich braucht — nicht den Builder mit den meisten Optionen.',
	],
	[
		'q' => 'Wem gehören Zugänge, Konten und Code?',
		'a' => 'Ihnen. Repository, Hosting, Analytics- und Werbekonten sollen nicht an meine Person gebunden sein. Das reduziert Lock-in und macht einen späteren Wechsel technisch möglich.',
	],
	[
		'q' => 'Können Sie ein vorhandenes Figma-Design umsetzen?',
		'a' => 'Ja. Ein vorhandenes Screendesign kann direkt in die responsive WordPress-Umsetzung gehen. Vor dem Build prüfe ich nur, ob Zustände, Formulare und mobile Varianten vollständig beschrieben sind.',
	],
	[
		'q' => 'Bieten Sie einen Wartungsvertrag mit Rufbereitschaft?',
		'a' => 'Nein. Laufende Weiterentwicklung ist möglich, aber ohne 24/7-Rufbereitschaft. Wenn Ihr Betrieb verbindliche Vertretung und garantierte Reaktionszeiten braucht, ist ein Agentur- oder Team-Setup die bessere Wahl.',
	],
	[
		'q' => 'Wie lange dauert ein Projekt?',
		'a' => 'Das hängt vom Scope ab. Vor Start steht ein abgegrenzter Umfang mit Meilensteinen. Kleine Korrekturen sind etwas anderes als ein Relaunch mit Tracking, Migration und mehreren Freigabeschleifen.',
	],
	[
		'q' => 'Arbeiten Sie nur in Hannover?',
		'a' => 'Nein. Ich sitze in der Region Hannover und arbeite im gesamten DACH-Raum remote. Persönliche Termine sind möglich, wenn sie für Workshop, Kick-off oder Review einen echten Vorteil bringen.',
	],
];

$toc_items = [
	'einordnung' => 'Einordnung',
	'strecke'    => 'Die Strecke',
	'angebote'   => 'Angebote',
	'vergleich'  => 'Vergleich',
	'ablauf'     => 'Ablauf',
	'position'   => 'Position',
	'nachweis'   => 'Nachweis',
	'fragen'     => 'Fragen',
	'anfrage'    => 'Anfrage',
];

get_header();
?>

<main id="main" class="site-main">
	<div class="hu-fr" data-track-page="wordpress_freelancer_hannover">
		<div class="hu-fr__page-progress" aria-hidden="true"><span data-fr-progress></span></div>

		<nav class="hu-fr-toc" aria-label="Inhaltsverzeichnis">
			<div class="hu-fr-toc__panel">
				<span class="hu-fr-toc__spine" aria-hidden="true">Inhalt · 09</span>
				<div class="hu-fr-toc__head"><b>Auf dieser Seite</b><span>9 Abschnitte</span></div>
				<div class="hu-fr-toc__scale">
					<span class="hu-fr-toc__fill" data-fr-toc-fill aria-hidden="true"></span>
					<ol class="hu-fr-toc__list">
						<?php foreach ( $toc_items as $toc_id => $toc_label ) : ?>
							<li><a href="#<?php echo esc_attr( $toc_id ); ?>" data-fr-toc-link><span class="hu-fr-toc__label"><?php echo esc_html( $toc_label ); ?></span><i class="hu-fr-toc__tick" aria-hidden="true"></i></a></li>
						<?php endforeach; ?>
					</ol>
				</div>
				<div class="hu-fr-toc__act">
					<a class="hu-fr-toc__cta" href="#anfrage" aria-label="Projekt anfragen" data-track-action="cta_freelancer_toc_project" data-track-category="lead_gen" data-track-section="toc">
						<span>Projekt anfragen</span><b aria-hidden="true">↗</b>
					</a>
					<p><?php echo esc_html( $response_promise ); ?></p>
				</div>
			</div>
		</nav>

		<section class="hu-fr-section hu-fr-section--dark hu-fr-hero" id="start" aria-labelledby="hu-fr-title">
			<div class="hu-fr__shell">
				<div class="hu-fr-hero__grid">
					<div class="hu-fr-hero__copy">
						<p class="hu-fr__eyebrow">WordPress · Tracking · Conversion</p>
						<h1 id="hu-fr-title">WordPress Freelancer Hannover, der die <em>Messung</em> mitbaut.</h1>
						<p class="hu-fr-hero__lede">Ich entwickle WordPress-Seiten, Landingpages und Anfragestrecken inklusive Tracking. So hängen Website, Werbekanal und CRM technisch zusammen — und Sie müssen nicht zwischen mehreren Dienstleistern vermitteln, wenn Anfragen oder Zahlen nicht stimmen.</p>
						<div class="hu-fr__actions">
							<a class="hu-fr__button hu-fr__button--primary" href="<?php echo esc_url( $project_url ); ?>" data-track-action="cta_freelancer_hero_project" data-track-category="lead_gen" data-track-section="hero">Projekt anfragen <span aria-hidden="true">↗</span></a>
							<a class="hu-fr__button hu-fr__button--ghost" href="#angebote" data-track-action="cta_freelancer_hero_pricing" data-track-category="navigation" data-track-section="hero">Was das kostet</a>
						</div>
						<ul class="hu-fr-hero__signals" role="list">
							<li>Code &amp; Konten gehören Ihnen</li>
							<li>Tracking von Anfang an</li>
							<li>Direkt mit dem Entwickler</li>
						</ul>
					</div>
					<div class="hu-fr-hero__visual">
						<figure class="hu-fr-portrait">
							<img src="<?php echo esc_url( $portrait_url ); ?>" width="640" height="800" sizes="(max-width: 900px) 88vw, 430px" alt="Haşim Üner, WordPress Freelancer aus der Region Hannover" fetchpriority="high" decoding="async">
							<figcaption><strong>Haşim Üner</strong><span>Entwicklung · Messung · Anfrageweg</span></figcaption>
						</figure>
						<blockquote>„Technik sollte Probleme lösen — nicht neue Messlücken schaffen.“</blockquote>
					</div>
				</div>
			</div>
			<div class="hu-fr__shell hu-fr-hero__proofbar" aria-label="Kurzbelege">
				<span><strong><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></strong> CPL im dokumentierten Solar-Case*</span>
				<span><strong><?php echo esc_html( $e3_leads ); ?></strong> qualifizierte Anfragen im selben System*</span>
				<span><strong><?php echo esc_html( $lighthouse_mobile_performance ); ?></strong> Mobile Performance dieser Seite**</span>
				<span><strong>≤ 24 h werktags</strong> Antwort auf Projektanfragen</span>
			</div>
		</section>

		<div class="hu-fr__mobile-toc-wrap">
			<details class="hu-fr-toc-m">
				<summary>Auf dieser Seite — 9 Abschnitte <span aria-hidden="true">⌄</span></summary>
				<ol>
					<?php foreach ( $toc_items as $toc_id => $toc_label ) : ?>
						<li><a href="#<?php echo esc_attr( $toc_id ); ?>"><?php echo esc_html( $toc_label ); ?></a></li>
					<?php endforeach; ?>
				</ol>
			</details>
		</div>

		<section class="hu-fr-section hu-fr-section--light" id="einordnung" data-fr-section aria-labelledby="hu-fr-einordnung-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>01</span><b>Einordnung</b></div>
				<div>
					<p class="hu-fr__eyebrow">Typische Ausgangslagen</p>
					<h2 id="hu-fr-einordnung-title">Wo steht Ihr Projekt gerade?</h2>
					<p class="hu-fr__intro">Vier typische Ausgangslagen. Je nachdem, wo es gerade hängt, sieht auch die sinnvolle Lösung anders aus.</p>
					<div class="hu-fr-situations">
						<a href="#angebot-website"><span>01</span><h3>Wir haben keine Website — oder eine, die niemand mehr anfassen will.</h3><p>Saubere technische Basis, klare Seitenstruktur und Messung von Anfang an.</p><b>ab <?php echo esc_html( $website_price ); ?> →</b></a>
						<a href="#angebot-funnel"><span>02</span><h3>Wir schalten Anzeigen auf eine Seite, die dafür nie gebaut wurde.</h3><p>Landingpage, Formular, Danke-Seite und Tracking als eine durchgehende Anfragestrecke.</p><b>auf Anfrage →</b></a>
						<a href="#angebot-tracking"><span>03</span><h3>Die Seite läuft. Aber niemand kann sagen, woher die Anfragen kommen.</h3><p>Belastbare Messung und Attribution, ohne die bestehende Website komplett neu aufzubauen.</p><b>ab <?php echo esc_html( $tracking_price ); ?> →</b></a>
						<a href="#angebot-weiterentwicklung"><span>04</span><h3>Es gibt eine Website, aber niemanden, der sie verlässlich weiterentwickelt.</h3><p>Planbare Weiterentwicklung mit klaren Prioritäten und einem direkten Ansprechpartner.</p><b>monatlich nach Scope →</b></a>
					</div>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--dark" id="strecke" data-fr-section aria-labelledby="hu-fr-strecke-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>02</span><b>Die Strecke</b></div>
				<div>
					<div class="hu-fr__split-head">
						<div><p class="hu-fr__eyebrow">Eine Anfrage legt fünf Stationen zurück</p><h2 id="hu-fr-strecke-title">Vom ersten Klick bis zur Anfrage.</h2></div>
						<p>Die Website ist nur eine Station. Verloren geht eine Anfrage meistens zwischen zwei Stationen — dort, wo Zuständigkeit und Information wechseln.</p>
					</div>
					<div class="hu-fr-route" data-fr-route>
						<div class="hu-fr-route__rail" aria-hidden="true"><span></span></div>
						<ol>
							<li><i>01</i><h3>Suche oder Anzeige</h3><p>Jemand sucht ein Problem, nicht Ihre Firma.</p><small>Üblich: SEO- oder Ads-Dienstleister</small></li>
							<li><i>02</i><h3>Seite</h3><p>Erster Satz, Ladezeit, Struktur und Relevanz.</p><small>Üblich: Webdesigner oder Agentur</small></li>
							<li><i>03</i><h3>Formular</h3><p>Welche Felder, welche Vorqualifizierung, welche Hürde.</p><small>Üblich: niemand ausdrücklich</small></li>
							<li><i>04</i><h3>Messung</h3><p>Was ausgelöst wird und welche Quelle erhalten bleibt.</p><small>Üblich: Analytics-Dienstleister</small></li>
							<li><i>05</i><h3>Postfach oder CRM</h3><p>Wer die Anfrage bekommt und wie schnell daraus ein Gespräch wird.</p><small>Üblich: der Vertrieb, ungefragt</small></li>
						</ol>
						<div class="hu-fr-route__note"><strong>Drei Dienstleister = mehrere Übergaben.</strong><p>Bei mir bleibt die technische Verantwortung für diese Strecke in einem Scope.</p></div>
					</div>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--light" id="angebote" data-fr-section aria-labelledby="hu-fr-angebote-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>03</span><b>Angebote</b></div>
				<div>
					<p class="hu-fr__eyebrow">Klare Leistungen. Transparente Einstiege.</p>
					<h2 id="hu-fr-angebote-title">Vier Angebote. Jedes mit einem Ergebnis, das man abnehmen kann.</h2>
					<div class="hu-fr-offers">
						<article id="angebot-website"><header><span>01 — Aufbau</span><strong>ab <?php echo esc_html( $website_price ); ?></strong></header><h3>Website neu oder Relaunch</h3><p>Für Unternehmen, die eine technische Basis brauchen, die die nächsten Jahre trägt.</p><ul><li>Seitenarchitektur und Inhaltsinventur</li><li>individuelle WordPress-Umsetzung</li><li>technisches SEO, Core Web Vitals, Accessibility-Basis</li><li>Messung von Anfang an mitgedacht</li><li>versioniertes Deployment und Abnahme auf Staging</li></ul><footer>Festpreis nach Scope. Zugänge, Repository und Konten gehören Ihnen.</footer></article>
						<article id="angebot-funnel"><header><span>02 — Anfragestrecke</span><strong>auf Anfrage</strong></header><h3>Landingpage, Formular und Messung als ein Stück</h3><p>Für eine Kampagne mit einem klaren Ziel — nicht für eine weitere lose Landingpage.</p><ul><li>Landingpage auf Angebot und Zielgruppe</li><li>Formular mit Vorqualifizierung</li><li>Danke-Seite und Conversion-Auslösung</li><li>Server-Side-Messung und Werbekanal-Rückkanal</li><li>Auswertung, aus der eine Budgetentscheidung folgen kann</li></ul><footer>Sinnvoll bei laufendem oder geplantem Anzeigenbudget.</footer></article>
						<article id="angebot-tracking"><header><span>03 — Messung</span><strong>ab <?php echo esc_html( $tracking_price ); ?></strong></header><h3>Tracking und Attribution nachrüsten</h3><p>Für Seiten, die laufen, aber keine belastbaren Zahlen liefern.</p><ul><li>GA4 und Google Tag Manager</li><li>Server-Side Tracking</li><li>Consent-Anbindung</li><li>Google Ads / Meta Rückkanal je Scope</li><li>prüfbare Event- und Datenlogik</li></ul><footer><a href="<?php echo esc_url( $tracking_url ); ?>">Tracking-Leistungsumfang ansehen ↗</a></footer></article>
						<article id="angebot-weiterentwicklung"><header><span>04 — Weiterentwicklung</span><strong>nach Scope</strong></header><h3>Feste Kapazität für laufende Weiterentwicklung</h3><p>Für Unternehmen, deren Seite weitergebaut werden soll — kontrolliert statt als loses Ticket-Pingpong.</p><ul><li>neue Bereiche und Landingpages</li><li>technische Optimierung</li><li>Tracking- und Conversion-Korrekturen</li><li>klare Prioritäten statt offener Wunschliste</li><li>direkter Ansprechpartner</li></ul><footer>Umfang und Takt werden vor Start festgelegt.</footer></article>
					</div>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--dark" id="vergleich" data-fr-section aria-labelledby="hu-fr-vergleich-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>04</span><b>Vergleich</b></div>
				<div>
					<div class="hu-fr__split-head"><div><p class="hu-fr__eyebrow">Direkt, Agentur oder Baukasten</p><h2 id="hu-fr-vergleich-title">Nicht jede Zeile spricht für mich. Die Unterschiede, die im Ergebnis zählen.</h2></div><p>Ein Freelancer ist nicht automatisch besser. Der Vorteil entsteht nur dort, wo direkte Verantwortung, technische Tiefe und geringer Übergabeaufwand wirklich zum Projekt passen.</p></div>
					<div class="hu-fr-table-wrap" role="region" aria-labelledby="hu-fr-vergleich-title" tabindex="0">
						<table class="hu-fr-table">
							<thead><tr><th>Kriterium</th><th>Direkt mit mir</th><th>Agentur</th><th>Baukasten</th></tr></thead>
							<tbody>
								<tr><th>Ansprechpartner</th><td>Die Person, die entwickelt und deployt.</td><td>Projektleitung und Team.</td><td>Sie selbst plus Anbieter-Support.</td></tr>
								<tr><th>Code-Eigentum</th><td>Ihr Repository, vollständiger Verlauf.</td><td>Abhängig vom Vertrag und Setup.</td><td>Kein eigener Zugriff auf den Plattform-Code.</td></tr>
								<tr><th>Messung</th><td>Tracking kann Teil derselben Architektur sein.</td><td>Möglich, oft eigenes Gewerk.</td><td>Standard-Integrationen.</td></tr>
								<tr><th>Abstimmung</th><td>Direkt, ohne Account-Handover.</td><td>Mehr Rollen, dafür mehr Kapazität.</td><td>Kein Projektteam.</td></tr>
								<tr><th>Weiterentwicklung</th><td>Planbar im vereinbarten Scope.</td><td>Retainer oder Wartungsmodell möglich.</td><td>Innerhalb der Plattformgrenzen.</td></tr>
								<tr><th>Wenn ich ausfalle</th><td>Das Projekt pausiert; Code und Zugänge bleiben bei Ihnen.</td><td>Vertretung im Team ist möglich.</td><td>Die Plattform läuft weiter, Umsetzung bleibt bei Ihnen.</td></tr>
								<tr><th>Kosten</th><td>Aufbau ab <?php echo esc_html( $website_price_net ); ?>.</td><td>Je nach Team- und Projektmodell.</td><td>Abo-Einstieg, eigene Zeit nicht eingerechnet.</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--light" id="ablauf" data-fr-section aria-labelledby="hu-fr-ablauf-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>05</span><b>Ablauf</b></div>
				<div>
					<p class="hu-fr__eyebrow">Drei Schritte bis zum ersten Deployment</p>
					<h2 id="hu-fr-ablauf-title">Scope vor Code. Abnahme vor Livegang.</h2>
					<ol class="hu-fr-process">
						<li><span>01</span><h3>Abgleich, 30 Minuten</h3><p>Ausgangslage, Ziel und vorhandene Daten. Am Ende steht ein sinnvoller nächster Schritt — oder ein begründetes Nein.</p></li>
						<li><span>02</span><h3>Angebot mit festem Umfang</h3><p>Was gebaut wird, was gemessen wird und was ausdrücklich nicht dazugehört. Nachträgliche Wünsche werden getrennt beauftragt.</p></li>
						<li><span>03</span><h3>Umsetzung, Abnahme, Übergabe</h3><p>Entwicklung in Branches, Review auf Staging, kontrolliertes Deployment. Zugänge und Repository liegen von Anfang an bei Ihnen.</p></li>
					</ol>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--dark" id="position" data-fr-section aria-labelledby="hu-fr-position-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>06</span><b>Position</b></div>
				<div class="hu-fr-position">
					<div><p class="hu-fr__eyebrow">Der Unterschied</p><h2 id="hu-fr-position-title">Ich bin teurer als ein Baukasten und meist günstiger als ein größeres Agentur-Setup.</h2><p>Der Unterschied soll nicht aus Design-Behauptungen kommen, sondern aus Verantwortung: Messung wird mitgebaut statt nachgeklebt, Änderungen werden versioniert und Entscheidungen bleiben später erklärbar.</p></div>
					<aside><span>Wofür ich nicht der Richtige bin</span><ul><li>Baukasten-Projekte ohne individuelle Technik</li><li>24/7-Rufbereitschaft oder garantierte Vertretung</li><li>reines Design ohne technische Umsetzung</li><li>Projekte, bei denen nur der niedrigste Preis entscheidet</li></ul></aside>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--light" id="nachweis" data-fr-section aria-labelledby="hu-fr-nachweis-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>07</span><b>Nachweis</b></div>
				<div>
					<p class="hu-fr__eyebrow">Keine Versprechen. Sondern prüfbare Arbeit.</p>
					<h2 id="hu-fr-nachweis-title">Technik, die man messen und nachsehen kann.</h2>
					<div class="hu-fr-proof-grid">
						<article><span>Arbeitsweise</span><h3>Kein Herumprobieren im Live-System</h3><code>brief → feature → staging → review → main</code><p>Größere Änderungen laufen versioniert und kontrolliert. Das Repository bleibt nachvollziehbar.</p><a href="<?php echo esc_url( $github_url ); ?>" target="_blank" rel="noopener noreferrer">Repository ansehen ↗</a></article>
						<article><span>Diese Seite</span><h3>Labwerte offen benannt</h3><dl><div><dt>Mobile Performance</dt><dd><?php echo esc_html( $lighthouse_mobile_performance ); ?></dd></div><div><dt>Barrierefreiheit</dt><dd><?php echo esc_html( $lighthouse_accessibility ); ?></dd></div></dl><p>Labtests sind keine CrUX-Felddaten. Deshalb werden sie hier auch nicht als solche verkauft.</p></article>
						<article><span>Fallbeispiel</span><h3>Dokumentierter Solar-Case</h3><dl><div><dt>Kosten pro qualifizierter Anfrage</dt><dd><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></dd></div><div><dt>Qualifizierte Anfragen</dt><dd><?php echo esc_html( $e3_leads ); ?></dd></div></dl><p>Das Ergebnis stammt aus dem Gesamtsystem aus Angebot, Landingpages, Tracking und Optimierung — nicht aus WordPress allein.</p><a href="<?php echo esc_url( $e3_case_url ); ?>">Case ansehen ↗</a></article>
					</div>
					<div class="hu-fr-projects">
						<?php foreach ( $references as $index => $reference ) : ?>
							<a href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener noreferrer"><span><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span><div><small><?php echo esc_html( $reference['tag'] ); ?></small><strong><?php echo esc_html( $reference['name'] ); ?> ↗</strong></div><p><?php echo esc_html( $reference['text'] ); ?></p></a>
						<?php endforeach; ?>
					</div>
					<p class="hu-fr__proof-note">* Fallzahlen und CPL stammen aus dem dokumentierten Solar-Case. ** Lighthouse-Labtest; Werte können je Messung variieren.</p>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--dark" id="fragen" data-fr-section aria-labelledby="hu-fr-fragen-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>08</span><b>Fragen</b></div>
				<div class="hu-fr-faq-grid">
					<div><p class="hu-fr__eyebrow">Häufige Fragen. Klare Antworten.</p><h2 id="hu-fr-fragen-title">Kurz und konkret.</h2></div>
					<div class="hu-fr-faq" data-fr-accordion>
						<?php foreach ( $faqs as $faq ) : ?>
							<details><summary><?php echo esc_html( $faq['q'] ); ?><span aria-hidden="true">+</span></summary><div><p><?php echo esc_html( $faq['a'] ); ?></p></div></details>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--light" id="anfrage" data-fr-section aria-labelledby="hu-fr-anfrage-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>09</span><b>Anfrage</b></div>
				<div class="hu-fr-inquiry">
					<div class="hu-fr-inquiry__intro"><p class="hu-fr__eyebrow">Der erste Scope</p><h2 id="hu-fr-anfrage-title">Drei Angaben reichen für den ersten fachlichen Abgleich.</h2><p>Die erste Stufe fragt nur nach Ansprechpartner, Ausgangslage und Ziel. Kontakt und Einwilligung kommen erst danach. So bleibt die sichtbare Hürde klein, ohne Daten stillschweigend zu sammeln.</p></div>
					<div class="hu-fr-inquiry__main">
						<form class="hu-fr-form" data-fr-form action="<?php echo esc_url( $rest_endpoint ); ?>" method="post" novalidate>
							<input type="hidden" name="request_type" value="project">
							<div class="hu-fr-form__honeypot" aria-hidden="true"><label>Website <input type="text" name="company_website" tabindex="-1" autocomplete="off"></label></div>

							<div class="hu-fr-form__step" data-fr-form-step="brief">
								<label><span>Name / Ansprechpartner</span><input type="text" name="name" autocomplete="name" required placeholder="Max Mustermann"></label>
								<label><span>Womit kommen Sie?</span><select name="focus" required><option value="">Bitte wählen</option><option value="relaunch">Website neu oder Relaunch</option><option value="implementation_scope">Konkrete WordPress-Umsetzung</option><option value="tracking">Tracking &amp; Analytics</option><option value="conversion">Conversion &amp; Anfrageweg</option><option value="website_strategy">Positionierung / Seitenbotschaft</option></select></label>
								<label><span>Was soll am Ende besser funktionieren?</span><textarea name="message" rows="4" minlength="24" required placeholder="Zwei bis drei Sätze reichen für den ersten Scope."></textarea></label>
								<button class="hu-fr__button hu-fr__button--primary" type="button" data-fr-form-next data-track-action="cta_freelancer_form_next" data-track-category="lead_gen" data-track-section="inquiry">Weiter zur E-Mail <span aria-hidden="true">→</span></button>
							</div>

							<div class="hu-fr-form__step" data-fr-form-step="contact" hidden>
								<div class="hu-fr-form__backline"><button type="button" data-fr-form-back>← Angaben ändern</button><span>Schritt 2 von 2</span></div>
								<label><span>E-Mail</span><input type="email" name="email" autocomplete="email" required placeholder="name@unternehmen.de"></label>
								<label class="hu-fr-form__consent"><input type="checkbox" name="consent" value="1" required><span>Ich stimme der Verarbeitung meiner Angaben zur Bearbeitung der Anfrage zu. <a href="<?php echo esc_url( $privacy_url ); ?>">Datenschutz</a>.</span></label>
								<button class="hu-fr__button hu-fr__button--primary" type="submit" data-fr-form-submit data-track-action="cta_freelancer_form_submit" data-track-category="lead_gen" data-track-section="inquiry">Projekt anfragen <span aria-hidden="true">↗</span></button>
							</div>
							<p class="hu-fr-form__status" data-fr-form-status role="status" aria-live="polite"></p>
						</form>
						<aside class="hu-fr-inquiry__measurement"><span>Was ein gutes Formular sichtbar macht</span><ol><li><b>01</b> Einstieg und Ausgangslage</li><li><b>02</b> Auswahl des Projektkontexts</li><li><b>03</b> tatsächliches Absenden</li><li><b>04</b> später: Auftrag oder kein Auftrag</li></ol><p>Gemessen werden soll nur, was eine Entscheidung verbessert. Keine zwanzig Events, weil das Dashboard dann voller aussieht.</p></aside>
					</div>
					<div class="hu-fr-inquiry__fallback"><span>Lieber ohne Formular?</span><a href="<?php echo esc_url( $project_url ); ?>">Projektanfrage auf der Kontaktseite öffnen ↗</a></div>
				</div>
			</div>
		</section>
	</div>
</main>

<?php get_footer(); ?>