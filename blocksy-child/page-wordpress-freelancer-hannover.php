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
			'title'       => 'WordPress Freelancer Hannover | Tracking & Conversion',
			'description' => 'WordPress-Websites, Landingpages und Tracking aus einer Hand. Direkt mit dem Entwickler in der Region Hannover – klarer Scope und versionierter Code.',
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
		'q' => 'Können Sie eine bestehende WordPress-Seite übernehmen?',
		'a' => 'Ja, nach einem kurzen technischen Check. Ich prüfe Theme oder Builder, Plugins, Hosting und Tracking und sage Ihnen dann, ob ein sauberer Weiterbau sinnvoll ist oder ein Relaunch langfristig die bessere Lösung wäre.',
	],
	[
		'q' => 'Arbeiten Sie mit Elementor?',
		'a' => 'Ja, wenn es für Redaktion und Betrieb sinnvoll ist. Standardmäßig bevorzuge ich die schlankste Lösung, die das Projekt wirklich braucht — nicht den Builder mit den meisten Optionen.',
	],
	[
		'q' => 'Wem gehören Zugänge, Konten und Code?',
		'a' => 'Ihnen. Repository, Hosting, Analytics- und Werbekonten sollen nicht an meine Person gebunden sein. Das reduziert Lock-in und macht einen späteren Wechsel technisch möglich.',
	],
	[
		'q' => 'Was kostet ein Website-Neuaufbau oder Relaunch?',
		'a' => 'Der Einstieg liegt bei ' . $website_price_net . '. Der konkrete Festpreis hängt von Seitenumfang, Inhaltslage, Funktionen, Tracking und Migration ab und steht vor Projektstart im Scope.',
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
							<a class="hu-fr__button hu-fr__button--primary" href="#anfrage" data-track-action="cta_freelancer_hero_project" data-track-category="lead_gen" data-track-section="hero">Projekt anfragen <span aria-hidden="true">↗</span></a>
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
						<div><p class="hu-fr__eyebrow">Fünf Stationen. Vier Übergaben.</p><h2 id="hu-fr-strecke-title">Der kritische Teil liegt zwischen den Stationen.</h2></div>
						<p>Vom ersten Klick bis ins CRM werden Erwartung, Quelle und Zuständigkeit weitergereicht. Genau an diesen Übergaben entstehen Messlücken, falsche Zuordnungen und unnötige Reibung.</p>
					</div>
					<div class="hu-fr-route" data-fr-route>
						<div class="hu-fr-route__rail" aria-hidden="true"><span></span></div>
						<ol>
							<li><i>01</i><h3>Suche oder Anzeige</h3><p>Hier entsteht die Erwartung: Suchintention, Anzeige und Versprechen.</p><small>Verantwortung meist: SEO oder Ads</small></li>
							<li><i>02</i><h3>Seite</h3><p>Botschaft, Geschwindigkeit und Relevanz entscheiden, ob jemand bleibt.</p><small>Verantwortung meist: Web oder Content</small></li>
							<li><i>03</i><h3>Formular</h3><p>Fragen sollen qualifizieren, ohne gute Anfragen unnötig auszubremsen.</p><small>Verantwortung oft: nicht klar geregelt</small></li>
							<li><i>04</i><h3>Messung</h3><p>Quelle, Consent und Conversion müssen technisch sauber zusammenlaufen.</p><small>Verantwortung meist: Analytics</small></li>
							<li><i>05</i><h3>Postfach oder CRM</h3><p>Anfrage, Herkunft und Status müssen beim richtigen Team ankommen.</p><small>Verantwortung meist: Vertrieb oder CRM</small></li>
						</ol>
						<div class="hu-fr-route__note"><strong>Jede Übergabe ist eine mögliche Messlücke.</strong><p>Mein Scope endet deshalb nicht automatisch am WordPress-Template. Wenn ich die Anfragestrecke baue, gehören Formular, Messung und technische Übergabe zum selben System.</p></div>
					</div>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--light" id="angebote" data-fr-section aria-labelledby="hu-fr-angebote-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>03</span><b>Angebote</b></div>
				<div>
					<p class="hu-fr__eyebrow">Vier klare Einstiege</p>
					<h2 id="hu-fr-angebote-title">Kein Leistungskatalog. Vier klar abgegrenzte Ergebnisse.</h2>
					<div class="hu-fr-offers">
						<article id="angebot-website"><header><span>01 — Aufbau</span><strong>ab <?php echo esc_html( $website_price ); ?></strong></header><h3>Website neu oder Relaunch</h3><p>Für Unternehmen, die eine neue technische Basis brauchen — nicht nur ein neues Layout.</p><ul><li>Seiten- und Inhaltsarchitektur</li><li>individuelle WordPress-Umsetzung</li><li>responsive, performant und barrierearm</li><li>technisches SEO und Messkonzept</li><li>Staging, Abnahme und versioniertes Deployment</li></ul><footer>Ergebnis: eine übergebene, messbare Website in Ihren Konten und Ihrem Repository.</footer></article>
						<article id="angebot-funnel"><header><span>02 — Anfragestrecke</span><strong>auf Anfrage</strong></header><h3>Anfragestrecke für eine Kampagne</h3><p>Für Ads- oder SEO-Traffic, der nicht auf einer allgemeinen Unternehmensseite enden soll.</p><ul><li>Landingpage auf Angebot und Such- oder Anzeigenintention</li><li>Formular mit sinnvoller Vorqualifizierung</li><li>Danke-Seite und Übergabelogik</li><li>Conversion- und Server-Side-Messung</li><li>Rückkanal zum Werbekanal oder CRM je Scope</li></ul><footer>Ergebnis: ein durchgängiger Pfad vom Klick bis zur qualifizierten Anfrage.</footer></article>
						<article id="angebot-tracking"><header><span>03 — Messung</span><strong>ab <?php echo esc_html( $tracking_price ); ?></strong></header><h3>Tracking und Attribution nachrüsten</h3><p>Für Websites, auf denen Anfragen entstehen, aber Quelle, Consent und Conversion nicht verlässlich zusammenlaufen.</p><ul><li>Analyse der bestehenden Messkette</li><li>GA4 und Google Tag Manager</li><li>Server-Side Tracking je technischem Setup</li><li>Consent-Anbindung</li><li>Google Ads oder Meta Rückkanal je Scope</li></ul><footer>Ergebnis: belastbare Quellen- und Conversion-Daten. <a href="<?php echo esc_url( $tracking_url ); ?>">Tracking-Leistungsumfang ansehen ↗</a></footer></article>
						<article id="angebot-weiterentwicklung"><header><span>04 — Weiterentwicklung</span><strong>nach Scope</strong></header><h3>Planbare Kapazität für Weiterentwicklung</h3><p>Für Unternehmen, die kein neues Projekt brauchen, sondern jemanden, der die bestehende Website technisch weiterführt.</p><ul><li>priorisierte technische Weiterentwicklung</li><li>neue Bereiche, Landingpages und Funktionen</li><li>Performance und technisches SEO</li><li>Tracking- und Conversion-Korrekturen</li><li>feste Kapazität und direkter Ansprechpartner</li></ul><footer>Ergebnis: kontinuierliche Weiterentwicklung ohne jedes Mal neues Onboarding.</footer></article>
					</div>
					<div class="hu-fr__actions" aria-label="Nächster Schritt">
						<p class="hu-fr__intro">Passt einer dieser Einstiege zu Ihrem Projekt?</p>
						<a class="hu-fr__button hu-fr__button--primary" href="#anfrage" data-track-action="cta_freelancer_offers_project" data-track-category="lead_gen" data-track-section="offers">Projekt anfragen <span aria-hidden="true">↗</span></a>
					</div>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--dark" id="vergleich" data-fr-section aria-labelledby="hu-fr-vergleich-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>04</span><b>Vergleich</b></div>
				<div>
					<div class="hu-fr__split-head"><div><p class="hu-fr__eyebrow">Welches Setup passt zum Projekt?</p><h2 id="hu-fr-vergleich-title">Nicht jedes Projekt braucht einen Freelancer. Aber jedes Projekt braucht die richtige Struktur.</h2></div><p>Direkt mit mir ist vor allem dann sinnvoll, wenn der Scope überschaubar bleibt, technische Tiefe gefragt ist und möglichst wenig zwischen Projektleitung, Entwicklung und Tracking verloren gehen soll.</p></div>
					<div class="hu-fr-table-wrap" role="region" aria-labelledby="hu-fr-vergleich-title" tabindex="0">
						<table class="hu-fr-table">
							<thead><tr><th>Kriterium</th><th>Direkt mit mir</th><th>Agentur</th><th>Baukasten</th></tr></thead>
							<tbody>
								<tr><th>Ansprechpartner</th><td>Sie sprechen mit der Person, die Architektur, Umsetzung und Deployment verantwortet.</td><td>Projektleitung koordiniert mehrere Rollen.</td><td>Sie setzen selbst um; Support hilft bei Plattformfragen.</td></tr>
								<tr><th>Code &amp; Konten</th><td>Repository, Hosting und Messkonten liegen bei Ihnen.</td><td>Abhängig von Vertrag und Setup; Übergabe sollte geregelt sein.</td><td>Plattform-Code und Infrastruktur bleiben beim Anbieter.</td></tr>
								<tr><th>Messung</th><td>Tracking kann Teil derselben technischen Umsetzung sein.</td><td>Gut möglich, oft über ein eigenes Spezialisten-Team.</td><td>Standard-Integrationen mit begrenzter technischer Tiefe.</td></tr>
								<tr><th>Kapazität</th><td>Eine Person, deshalb bewusst begrenzte Parallelität.</td><td>Mehrere Rollen können parallel arbeiten und sich vertreten.</td><td>Die verfügbare Kapazität ist Ihre eigene Zeit.</td></tr>
								<tr><th>Weiterentwicklung</th><td>Planbar innerhalb einer vereinbarten Kapazität und Prioritätenliste.</td><td>Retainer, Wartung und größere Teams sind möglich.</td><td>Innerhalb der Funktionen und Grenzen der Plattform.</td></tr>
								<tr><th>Ausfallrisiko</th><td>Wenn ich ausfalle, pausiert das Projekt. Code, Zugänge und Verlauf bleiben bei Ihnen.</td><td>Vertretung im Team ist grundsätzlich möglich.</td><td>Die Plattform läuft weiter; Umsetzung und Entscheidungen bleiben bei Ihnen.</td></tr>
								<tr><th>Kosten</th><td>Aufbau ab <?php echo esc_html( $website_price_net ); ?>.</td><td>Je nach Teamgröße, Leistungsumfang und Projektmodell.</td><td>Niedriger Abo-Einstieg; eigene Zeit und Grenzen der Plattform kommen hinzu.</td></tr>
								<tr><th>Passt besonders, wenn …</th><td>der Umfang klar ist und direkte technische Verantwortung zählt.</td><td>viele Gewerke gleichzeitig laufen oder Vertretung wichtig ist.</td><td>die Seite einfach bleibt und Budget wichtiger als Individualität ist.</td></tr>
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
					<p class="hu-fr__eyebrow">Drei Entscheidungen vor dem Livegang</p>
					<h2 id="hu-fr-ablauf-title">Erst Scope. Dann Build. Dann kontrolliert live.</h2>
					<ol class="hu-fr-process">
						<li><span>01</span><h3>Passt das Projekt? 30 Minuten.</h3><p>Wir klären Ausgangslage, Ziel, vorhandenes Setup und was wirklich gebaut werden muss. Danach gibt es einen klaren nächsten Schritt — oder ein begründetes Nein.</p></li>
						<li><span>02</span><h3>Scope wird schriftlich</h3><p>Leistungen, Messung, Übergaben, Abnahmekriterien und Ausschlüsse stehen vor dem Start fest. Neue Wünsche werden sichtbar ergänzt statt still in den Scope gedrückt.</p></li>
						<li><span>03</span><h3>Build → Staging → Abnahme → Live</h3><p>Entwicklung läuft versioniert, die Abnahme auf Staging und das Deployment kontrolliert. Zugänge und Repository liegen von Anfang an bei Ihnen.</p></li>
					</ol>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--dark" id="position" data-fr-section aria-labelledby="hu-fr-position-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>06</span><b>Position</b></div>
				<div class="hu-fr-position">
					<div><p class="hu-fr__eyebrow">Wofür Sie bezahlen</p><h2 id="hu-fr-position-title">Ich bin teurer als ein Baukasten und meist günstiger als ein größeres Agentur-Setup.</h2><p>Sie bezahlen nicht dafür, dass ich möglichst viele Seiten baue. Sie bezahlen dafür, dass Entwicklung, Messung und Übergabe zusammen gedacht werden — und Sie später nachvollziehen können, was geändert wurde, wem die Konten gehören und woher eine Anfrage kommt.</p></div>
					<aside><span>Wann ein anderes Setup besser ist</span><ul><li>Sie brauchen 24/7-Support oder garantierte Vertretung: Dann ist ein Team sinnvoller.</li><li>Es geht nur um eine sehr einfache Präsenzseite: Dann kann ein Baukasten reichen.</li><li>Sie brauchen ausschließlich Branding oder UI ohne technische Umsetzung: Dann passt ein Designstudio besser.</li><li>Mehrere Gewerke müssen gleichzeitig mit hoher Kapazität laufen: Dann ist eine größere Agentur im Vorteil.</li></ul></aside>
				</div>
			</div>
		</section>

		<section class="hu-fr-section hu-fr-section--light" id="nachweis" data-fr-section aria-labelledby="hu-fr-nachweis-title">
			<div class="hu-fr__shell hu-fr__section-shell">
				<div class="hu-fr__section-mark"><span>07</span><b>Nachweis</b></div>
				<div>
					<p class="hu-fr__eyebrow">Belege statt Behauptungen</p>
					<h2 id="hu-fr-nachweis-title">Was Sie prüfen können, bevor Sie mich beauftragen.</h2>
					<div class="hu-fr-proof-grid">
						<article><span>Arbeitsweise</span><h3>Änderungen hinterlassen einen Verlauf</h3><code>brief → feature → staging → review → main</code><p>Größere Änderungen laufen versioniert und kontrolliert. Damit bleibt nachvollziehbar, was geändert wurde und wann es live ging.</p><a href="<?php echo esc_url( $github_url ); ?>" target="_blank" rel="noopener noreferrer">Repository ansehen ↗</a></article>
						<article><span>Diese Seite</span><h3>Performance offen als Labwert benannt</h3><dl><div><dt>Mobile Performance</dt><dd><?php echo esc_html( $lighthouse_mobile_performance ); ?></dd></div><div><dt>Barrierefreiheit</dt><dd><?php echo esc_html( $lighthouse_accessibility ); ?></dd></div></dl><p>Das sind Lighthouse-Labtests und keine CrUX-Felddaten. Deshalb werden sie hier auch nicht als reale Nutzerwerte verkauft.</p></article>
						<article><span>Fallbeispiel</span><h3>Case mit klarer Grenze</h3><dl><div><dt>Kosten pro qualifizierter Anfrage</dt><dd><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></dd></div><div><dt>Qualifizierte Anfragen</dt><dd><?php echo esc_html( $e3_leads ); ?></dd></div></dl><p>Das Ergebnis stammt aus dem Gesamtsystem aus Angebot, Landingpages, Tracking und Optimierung — nicht aus WordPress allein.</p><a href="<?php echo esc_url( $e3_case_url ); ?>">Case ansehen ↗</a></article>
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
					<div><p class="hu-fr__eyebrow">Fragen vor dem Start</p><h2 id="hu-fr-fragen-title">Was meistens vor einer Beauftragung geklärt wird.</h2></div>
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
					<div class="hu-fr-inquiry__intro"><p class="hu-fr__eyebrow">Projekt kurz einordnen</p><h2 id="hu-fr-anfrage-title">Beschreiben Sie kurz, was gerade nicht funktioniert — ich sage Ihnen, ob und wie ich einsteigen kann.</h2><p>Name, Ausgangslage und Ziel reichen zunächst. Die E-Mail kommt erst im zweiten Schritt. Auf Projektanfragen antworte ich in der Regel innerhalb von 24 Stunden werktags.</p></div>
					<div class="hu-fr-inquiry__main">
						<form class="hu-fr-form" data-fr-form action="<?php echo esc_url( $rest_endpoint ); ?>" method="post" novalidate>
							<input type="hidden" name="request_type" value="project">
							<div class="hu-fr-form__honeypot" aria-hidden="true"><label>Website <input type="text" name="company_website" tabindex="-1" autocomplete="off"></label></div>

							<div class="hu-fr-form__step" data-fr-form-step="brief">
								<label><span>Name / Ansprechpartner</span><input type="text" name="name" autocomplete="name" required placeholder="Max Mustermann"></label>
								<label><span>Was trifft am ehesten zu?</span><select name="focus" required><option value="">Bitte wählen</option><option value="relaunch">Website neu oder Relaunch</option><option value="implementation_scope">Konkrete WordPress-Umsetzung</option><option value="tracking">Tracking &amp; Analytics</option><option value="conversion">Conversion &amp; Anfrageweg</option><option value="website_strategy">Positionierung / Seitenbotschaft</option></select></label>
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
						<aside class="hu-fr-inquiry__measurement"><span>Was ich an einer Anfragestrecke messe</span><ol><li><b>01</b> Einstieg und Ausgangslage</li><li><b>02</b> gewählter Projektkontext</li><li><b>03</b> tatsächliche Absendung</li><li><b>04</b> später: qualifizierte Anfrage oder Auftrag</li></ol><p>Nicht jeder Klick braucht ein Event. Gemessen wird, was eine Entscheidung über Seite, Kampagne oder Vertrieb verbessert.</p></aside>
					</div>
					<div class="hu-fr-inquiry__fallback"><span>Lieber ohne Formular?</span><a href="<?php echo esc_url( $project_url ); ?>">Projektanfrage auf der Kontaktseite öffnen ↗</a></div>
				</div>
			</div>
		</section>
	</div>
</main>

<?php get_footer(); ?>