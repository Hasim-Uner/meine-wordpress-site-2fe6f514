<?php
/**
 * Template Name: WordPress Freelancer Hannover
 * Template Post Type: page
 *
 * Schlanke lokale Money-Page fuer direkte WordPress-Projekte.
 * Route: /wordpress-freelancer-hannover/
 *
 * Die Seite ist bewusst KEINE zweite Agentur- oder White-Label-Seite:
 * - Agentur-Intent bleibt auf /wordpress-agentur-hannover/
 * - White-Label-Intent bleibt auf /whitelabel-retainer/
 * - Diese Route besitzt den Freelancer-Intent und verkauft direkte Zusammenarbeit.
 *
 * SEO title/meta werden ueber den bestehenden zentralen Resolver erweitert,
 * nicht als eigener Meta-Stack ausgegeben.
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

// Das Markup dieser Seite ist vollstaendig repo-eigen. Block-Styles sind hier
// Render-Blocking-Overhead ohne Gegenwert und koennen vor wp_head entfernt werden.
wp_dequeue_style( 'wp-block-library' );
wp_dequeue_style( 'wp-block-library-theme' );

add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'hu-wordpress-freelancer-page';
		return $classes;
	}
);

// Nutzt den bestehenden SEO-Resolver (inc/seo-meta.php) statt einen zweiten
// Meta-Stack im Template aufzubauen. Der Title bleibt absichtlich eng auf dem
// Freelancer-Intent; lokale WordPress-SEO-Queries gehoeren zur Agentur-Route.
add_filter(
	'hu_forced_singular_seo_map',
	static function ( $map ) {
		$map['wordpress-freelancer-hannover'] = [
			'title'       => 'WordPress Freelancer Hannover | Haşim Üner',
			'description' => 'WordPress Freelancer in Hannover für individuelle Entwicklung, Tracking und Funnels. Direkte Zusammenarbeit, versionierter Code und klarer Scope.',
		];
		return $map;
	}
);

$contact_url = add_query_arg(
	[
		'type'  => 'project',
		'focus' => 'followup_scope',
	],
	home_url( '/kontakt/' )
);
$agentur_url    = home_url( '/wordpress-agentur-hannover/' );
$whitelabel_url = home_url( '/whitelabel-retainer/' );
$tracking_url   = home_url( '/server-side-tracking-b2b/' );
$about_url      = home_url( '/hasim-uener/' );
$e3_case_url    = home_url( '/case-study-solar-leadgenerierung/' );
$github_url     = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';

$portrait_url = get_stylesheet_directory_uri() . '/assets/img/hasim-freelancer-relaxed-640x800.webp';

$website_start_price = function_exists( 'hu_freelancer_website_price' ) ? hu_freelancer_website_price( true ) : 'Preis nach Scope';

$e3_cpl_before                   = hu_e3_metric( 'cpl_before' );
$e3_cpl_after                    = hu_e3_metric( 'cpl_after' );
$e3_leads                        = hu_e3_metric( 'lead_count' );
$lighthouse_mobile_performance   = hu_e3_metric( 'freelancer_lighthouse_mobile_performance' );
$lighthouse_accessibility        = hu_e3_metric( 'freelancer_lighthouse_accessibility' );

$references = [
	[
		'name' => 'civaka-azad.org',
		'url'  => 'https://civaka-azad.org/',
		'tag'  => 'Informationsarchitektur',
		'text' => 'Ein über Jahre gewachsener redaktioneller Bestand: Navigation, Archive und interne Verweise so strukturiert, dass ältere Beiträge auffindbar bleiben.',
	],
	[
		'name' => 'e3-newenergy.de',
		'url'  => 'https://e3-newenergy.de/',
		'tag'  => 'WordPress · Funnel · Tracking',
		'text' => 'Solar und Wärmepumpe mit erklärungsbedürftigem Angebot: Beratung, Angebotsanfrage und Kontakt in einer klaren Strecke statt über die Website verteilt.',
	],
	[
		'name' => 'hasimuener.org',
		'url'  => 'https://hasimuener.org/',
		'tag'  => 'Editorial Design',
		'text' => 'Typografie, Raster und Lesefluss als eigentliche Aufgabe — ein Layout, das auch ohne große Bildwelt trägt.',
	],
	[
		'name' => 'kurdischer-rat.org',
		'url'  => 'https://kurdischer-rat.org/',
		'tag'  => 'Aktuelles Projekt',
		'text' => 'Organisationswebsite mit klarer Informationshierarchie und einem technischen Workflow, der Änderungen versioniert und kontrolliert ausrollt.',
	],
];

$faqs = [
	[
		'q' => 'Was kostet ein WordPress Freelancer?',
		'a' => sprintf( 'Eine individuell entwickelte WordPress-Website startet bei mir aktuell ab %s. Der Einstieg ist für kompakte Unternehmenswebsites mit klar abgegrenztem Scope gedacht. Größere Integrationen, Tracking-Setups oder Funnel-Projekte kalkuliere ich separat.', $website_start_price ),
	],
	[
		'q' => 'Arbeiten Sie nur in Hannover?',
		'a' => 'Nein. Ich sitze in der Region Hannover und arbeite deutschlandweit remote. Für Projekte in Hannover und Umgebung sind persönliche Termine möglich, wenn sie wirklich helfen.',
	],
	[
		'q' => 'Arbeiten Sie mit Elementor?',
		'a' => 'Ja, wenn es für das Projekt sinnvoll ist. Standardmäßig bevorzuge ich einen schlanken Aufbau mit möglichst wenig Page-Builder-Abhängigkeit. Wenn ein Team Inhalte später sehr frei selbst umbauen muss, kann Elementor die pragmatischere Lösung sein.',
	],
	[
		'q' => 'Können Sie ein vorhandenes Screendesign oder Figma-Design umsetzen?',
		'a' => 'Ja. Ein vorhandenes Figma- oder Screendesign kann direkt in die technische Umsetzung gehen. Ich überführe es responsive in WordPress, versioniere die Änderungen und prüfe sie vor dem Deployment.',
	],
	[
		'q' => 'Was ist der Unterschied zwischen Freelancer und Agentur?',
		'a' => 'Sie arbeiten direkt mit der Person, die strukturiert, entwickelt und technisch entscheidet. Das reduziert Übergaben und macht den Scope klarer. Wenn ein größeres Team-Setup sinnvoller ist, sage ich das vor Projektstart offen.',
	],
	[
		'q' => 'Berücksichtigen Sie Barrierefreiheit und Core Web Vitals?',
		'a' => 'Ja. Semantisches HTML, Kontraste, Tastaturbedienung, Formulare, Bildgrößen und Ladepfade gehören für mich zur technischen Qualität. Lighthouse ist dabei ein Labortest und nicht dasselbe wie echte Felddaten — ich trenne beides bewusst.',
	],
];

get_header();
?>

<main id="main" class="site-main">
	<div class="hu-fr" data-track-page="wordpress_freelancer_hannover">

		<section class="hu-fr__hero" aria-labelledby="hu-fr-title">
			<div class="hu-fr__shell">
				<div class="hu-fr__hero-grid">
					<div class="hu-fr__hero-copy">
						<h1 id="hu-fr-title" class="hu-fr__seo-title">WordPress Freelancer Hannover</h1>
						<p class="hu-fr__hero-title">Technik, Tracking und Funnel aus einer Hand.</p>
						<p class="hu-fr__lede">Ich entwickle WordPress-Websites und Landingpages so, dass Code, Messung und Conversion-Logik zusammenpassen. Direkt mit mir — vom Scope bis zum Deployment.</p>

						<ul class="hu-fr__hero-capabilities" role="list" aria-label="Schwerpunkte">
							<li>WordPress</li>
							<li>Server-Side Tracking</li>
							<li>Funnel &amp; CRO</li>
						</ul>

						<div class="hu-fr__hero-actions">
							<a class="hu-fr__button hu-fr__button--primary" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_freelancer_hero_project" data-track-category="lead_gen" data-track-section="hero">Projekt prüfen lassen <span aria-hidden="true"><?php echo hu_arrow_up_right_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span></a>
							<a class="hu-fr__button hu-fr__button--secondary" href="#projekte" data-track-action="cta_freelancer_hero_references" data-track-category="navigation" data-track-section="hero">Referenzen ansehen</a>
						</div>
					</div>

					<div class="hu-fr__hero-visual">
						<div class="hu-fr__portrait-frame">
							<img class="hu-fr__portrait" src="<?php echo esc_url( $portrait_url ); ?>" sizes="(max-width: 760px) calc(100vw - 32px), 44vw" width="640" height="800" alt="Haşim Üner, WordPress Freelancer aus der Region Hannover" fetchpriority="high" decoding="async">
						</div>
					</div>
				</div>

				<ul class="hu-fr__proofline" role="list" aria-label="Kurzbelege">
					<li><strong><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></strong><span>Cost per Lead vorher → nachher</span></li>
					<li><strong><?php echo esc_html( $lighthouse_mobile_performance ); ?></strong><span>Mobile Performance*</span></li>
					<li><strong><?php echo esc_html( $lighthouse_accessibility ); ?></strong><span>Barrierefreiheit*</span></li>
				</ul>
				<p class="hu-fr__lab-note">* Lighthouse Mobile-Labtest dieser Seite, 17.08.2026, 00:22 MESZ. Werte können variieren; keine CrUX-Felddaten.</p>
			</div>
		</section>

		<section class="hu-fr__system" aria-labelledby="hu-fr-system-title">
			<div class="hu-fr__shell">
				<div class="hu-fr__section-intro">
					<div>
						<p class="hu-fr__kicker">Der Unterschied</p>
						<h2 id="hu-fr-system-title" class="hu-fr__h2">Eine Website ist nicht das Ende der Strecke.</h2>
					</div>
					<p>Viele WordPress-Projekte enden beim Launch. Entscheidend ist, ob Struktur, Messung und Nutzerweg danach zusammenpassen.</p>
				</div>

				<div class="hu-fr-system" aria-label="System aus Code, Tracking, Funnel und Anfrage">
					<svg class="hu-fr-system__svg" viewBox="0 0 1000 300" role="img" aria-labelledby="hu-fr-system-graphic-title hu-fr-system-graphic-desc">
						<title id="hu-fr-system-graphic-title">Vom WordPress-Code zur qualifizierten Anfrage</title>
						<desc id="hu-fr-system-graphic-desc">Vier verbundene Ebenen: Custom WordPress, Tracking, Funnel und Anfrage.</desc>
						<defs>
							<linearGradient id="huFrCopper" x1="0" x2="1">
								<stop offset="0" stop-color="#b46a3c" stop-opacity="0.18"/>
								<stop offset="0.5" stop-color="#b46a3c" stop-opacity="1"/>
								<stop offset="1" stop-color="#b46a3c" stop-opacity="0.18"/>
							</linearGradient>
						</defs>
						<path class="hu-fr-system__rail" d="M120 150 C250 150 250 80 380 80 S510 220 640 220 S770 150 900 150"/>
						<path class="hu-fr-system__signal" d="M120 150 C250 150 250 80 380 80 S510 220 640 220 S770 150 900 150"/>
						<g class="hu-fr-system__node" transform="translate(120 150)"><circle r="44"/><text y="-4">01</text><text y="17">Code</text></g>
						<g class="hu-fr-system__node" transform="translate(380 80)"><circle r="44"/><text y="-4">02</text><text y="17">Tracking</text></g>
						<g class="hu-fr-system__node" transform="translate(640 220)"><circle r="44"/><text y="-4">03</text><text y="17">Funnel</text></g>
						<g class="hu-fr-system__node hu-fr-system__node--result" transform="translate(900 150)"><circle r="52"/><text y="-4">04</text><text y="17">Anfrage</text></g>
					</svg>

					<div class="hu-fr-system__labels">
						<p><strong>Custom WordPress</strong><span>schlank, wartbar, ohne unnötigen Builder-Ballast</span></p>
						<p><strong>Server-Side Tracking</strong><span>GA4, GTM, CAPI und Consent sauber zusammengedacht</span></p>
						<p><strong>Funnel &amp; CRO</strong><span>klare Wege statt nur hübscher Seiten</span></p>
						<p><strong>Messbare Anfrage</strong><span>der eigentliche Zweck der Strecke</span></p>
					</div>
				</div>
			</div>
		</section>

		<section class="hu-fr__skills" aria-labelledby="hu-fr-skills-title">
			<div class="hu-fr__shell hu-fr__split-head">
				<div>
					<p class="hu-fr__kicker">Kompetenz</p>
					<h2 id="hu-fr-skills-title" class="hu-fr__h2">Vier Ebenen, die zusammenpassen müssen.</h2>
				</div>
				<p class="hu-fr__section-aside">Sie müssen Entwicklung, Tracking und Funnel nicht auf mehrere Ansprechpartner verteilen. Die technischen Entscheidungen bleiben in einem Scope.</p>
			</div>

			<div class="hu-fr__shell hu-fr__skill-list">
				<article class="hu-fr__skill-row">
					<span class="hu-fr__skill-index">01</span>
					<h3>WordPress-Entwicklung</h3>
					<p>Individuelle Templates, bestehende Websites, technische Fehler, WooCommerce und Funktionen, die nicht vom nächsten Plugin abhängen sollen.</p>
				</article>
				<article class="hu-fr__skill-row">
					<span class="hu-fr__skill-index">02</span>
					<h3>Tracking &amp; Attribution</h3>
					<p>Server-Side GTM, GA4, Meta CAPI, Consent Mode und saubere Übergaben. Tracking ist kein nachträglicher Pixel, sondern Teil der Architektur.</p>
				</article>
				<article class="hu-fr__skill-row">
					<span class="hu-fr__skill-index">03</span>
					<h3>Landingpages &amp; Funnel</h3>
					<p>Angebot, Reihenfolge, Formulare, Vorqualifizierung und Conversion. Der Nutzer soll nicht durch eine Website laufen, sondern einen nachvollziehbaren Weg haben.</p>
				</article>
				<article class="hu-fr__skill-row">
					<span class="hu-fr__skill-index">04</span>
					<h3>Technisches SEO, Performance &amp; Accessibility</h3>
					<p>Crawlability, interne Struktur, Core Web Vitals, semantisches HTML und Barrierefreiheit werden beim Bauen berücksichtigt — nicht erst nach dem Launch angeklebt.</p>
				</article>
			</div>
		</section>

		<section class="hu-fr__workflow" id="arbeitsweise" aria-labelledby="hu-fr-workflow-title">
			<div class="hu-fr__shell hu-fr__workflow-copy-shell">
				<div class="hu-fr__workflow-copy">
					<p class="hu-fr__kicker">Workflow</p>
					<h2 id="hu-fr-workflow-title" class="hu-fr__h2">Sauber entwickeln statt live herumprobieren.</h2>
					<p>Größere Änderungen entstehen versioniert, werden geprüft und kontrolliert ausgerollt. Das macht Änderungen nachvollziehbar und reduziert das Risiko, dass ein schneller Fix die nächste Baustelle erzeugt.</p>
					<ul class="hu-fr__checks" role="list">
						<li>GitHub statt Datei-Chaos</li>
						<li>Staging und nachvollziehbare Änderungen</li>
						<li>Performance- und Accessibility-Checks</li>
						<li>Review vor dem Deployment</li>
					</ul>
					<div class="hu-fr__builder-note">
						<strong>Elementor? Kann ich.</strong>
						<p>Page Builder setze ich ein, wenn sie für die spätere Redaktion einen echten Vorteil bringen. Standard ist die schlankste Lösung, die Redaktion und Wartung wirklich brauchen.</p>
					</div>
				</div>
			</div>
		</section>

		<section class="hu-fr__repo-proof" aria-labelledby="hu-fr-repo-proof-title">
			<div class="hu-fr__shell">
				<p class="hu-fr__kicker">Repo-Beweis</p>
				<h2 id="hu-fr-repo-proof-title" class="hu-fr__h2">Qualität im Repository prüfen.</h2>

				<div class="hu-fr__repo-proof-grid">
					<figure class="hu-fr-git">
						<div class="hu-fr-git__topbar"><span></span><span></span><span></span><code>project/main</code></div>
						<ol class="hu-fr-git__rows">
							<li><span class="hu-fr-git__node"></span><code>brief/</code><strong>Ziel &amp; Scope</strong></li>
							<li><span class="hu-fr-git__node"></span><code>feature/</code><strong>Design → Code</strong></li>
							<li><span class="hu-fr-git__node"></span><code>staging/</code><strong>Responsive &amp; QA</strong></li>
							<li><span class="hu-fr-git__node"></span><code>review/</code><strong>Performance &amp; Accessibility</strong></li>
							<li><span class="hu-fr-git__node hu-fr-git__node--last"></span><code>main/</code><strong>kontrolliertes Deployment</strong></li>
						</ol>
						<figcaption><a class="hu-fr-git__repo" href="<?php echo esc_url( $github_url ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="link_freelancer_github" data-track-category="trust" data-track-section="repo_proof">Öffentliches GitHub-Repo ansehen <span aria-hidden="true"><?php echo hu_arrow_up_right_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span></a></figcaption>
					</figure>

					<ul class="hu-fr__repo-artifacts" role="list">
						<li>
							<h3>Statische Analyse</h3>
							<code>phpstan.neon</code>
							<p><!-- TODO: Copy --></p>
						</li>
						<li>
							<h3>Copy-Guard</h3>
							<code>.github/workflows/copy-style.yml</code>
							<p><!-- TODO: Copy --></p>
						</li>
						<li>
							<h3>Lead-Path-Smoke</h3>
							<code>scripts/smoke-lead-path-contract.sh</code>
							<p><!-- TODO: Copy --></p>
						</li>
						<li>
							<h3>Versionierte Deploys</h3>
							<code>.github/workflows/deploy.yml</code>
							<p><!-- TODO: Copy --></p>
						</li>
					</ul>
				</div>
			</div>
		</section>

		<section class="hu-fr__proof" id="projekte" aria-labelledby="hu-fr-proof-title">
			<div class="hu-fr__shell">
				<div class="hu-fr__proof-head">
					<div>
						<p class="hu-fr__kicker">Proof</p>
						<h2 id="hu-fr-proof-title" class="hu-fr__h2">Direkt prüfbar. Nicht nur behauptet.</h2>
					</div>
					<a class="hu-fr__text-link" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="link_freelancer_e3_case" data-track-category="trust" data-track-section="proof">Dokumentierten Case ansehen</a>
				</div>

				<div class="hu-fr__case-strip">
					<div><strong><?php echo esc_html( $e3_cpl_before ); ?> → <?php echo esc_html( $e3_cpl_after ); ?></strong><span>Cost per Lead im dokumentierten Solar-Case</span></div>
					<div><strong><?php echo esc_html( $e3_leads ); ?></strong><span>qualifizierte Anfragen im aufgebauten System</span></div>
					<div><strong>Technik + Marketing</strong><span>Landingpages, Tracking und Optimierung als eine Strecke</span></div>
				</div>
				<p class="hu-fr__case-note">Die Kennzahlen stammen aus dem dokumentierten Solar-Case und beschreiben das Gesamtsystem aus Landingpages, Tracking und Optimierung. Sie sind kein isolierter WordPress-Effekt und keine Ergebniszusage.</p>

				<div class="hu-fr__references">
					<?php foreach ( $references as $index => $reference ) : ?>
						<article class="hu-fr__reference">
							<span class="hu-fr__reference-index"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
							<div class="hu-fr__reference-main">
								<p class="hu-fr__reference-tag"><?php echo esc_html( $reference['tag'] ); ?></p>
								<h3><a href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="link_freelancer_reference" data-track-category="trust" data-track-section="references"><?php echo esc_html( $reference['name'] ); ?> <span aria-hidden="true"><?php echo hu_arrow_up_right_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span></a></h3>
							</div>
							<p><?php echo esc_html( $reference['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>

				<p class="hu-fr__whitelabel-proof">Weitere Umsetzungen laufen als White-Label-Projekte. Die Namen veröffentliche ich bewusst nicht. <a href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="link_freelancer_whitelabel" data-track-category="navigation" data-track-section="proof">White-Label für Agenturen</a></p>
			</div>
		</section>

		<section class="hu-fr__pricing" aria-labelledby="hu-fr-pricing-title">
			<div class="hu-fr__shell hu-fr__pricing-grid">
				<div>
					<p class="hu-fr__kicker hu-fr__kicker--light">Preisrahmen</p>
					<h2 id="hu-fr-pricing-title" class="hu-fr__h2 hu-fr__h2--light">Vor dem Projekt soll klar sein, worüber wir sprechen.</h2>
					<p class="hu-fr__pricing-copy">Für kompakte Unternehmensseiten gibt es einen klaren Einstieg. Größere Setups werden erst nach einem abgegrenzten Scope kalkuliert.</p>
				</div>
				<div class="hu-fr__price-panel">
					<div class="hu-fr__price-row hu-fr__price-row--primary"><span>Individuelle WordPress-Website</span><strong>ab <?php echo esc_html( $website_start_price ); ?></strong></div>
					<div class="hu-fr__price-row"><span>Technische Aufgaben / bestehende Website</span><strong>nach Scope</strong></div>
					<div class="hu-fr__price-row"><span>Tracking / Funnel / komplexere Integrationen</span><strong>nach Scope</strong></div>
					<p>Der Einstieg gilt für eine kompakte Unternehmenswebsite mit klar abgegrenztem Umfang. Custom Code ist kein Aufpreis-Label. Elementor ist möglich, wenn redaktionelle Flexibilität den zusätzlichen Builder rechtfertigt.</p>
					<a class="hu-fr__button hu-fr__button--primary" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_freelancer_pricing_project" data-track-category="lead_gen" data-track-section="pricing">Projekt prüfen lassen <span aria-hidden="true"><?php echo hu_arrow_up_right_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span></a>
				</div>
			</div>
		</section>

		<section class="hu-fr__faq" aria-labelledby="hu-fr-faq-title">
			<div class="hu-fr__shell hu-fr__faq-grid">
				<div class="hu-fr__faq-intro">
					<p class="hu-fr__kicker">FAQ</p>
					<h2 id="hu-fr-faq-title" class="hu-fr__h2">Die Fragen vor dem ersten Projekt.</h2>
					<p>Wenn danach noch etwas offen ist, reicht eine kurze Nachricht. Kein Pflicht-Call.</p>
				</div>
				<div class="hu-fr__faq-list" data-fr-accordion>
					<?php foreach ( $faqs as $faq ) : ?>
						<details class="hu-fr__faq-item">
							<summary><?php echo esc_html( $faq['q'] ); ?><span aria-hidden="true">+</span></summary>
							<div><p><?php echo esc_html( $faq['a'] ); ?></p></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hu-fr__final" aria-labelledby="hu-fr-final-title">
			<div class="hu-fr__shell hu-fr__final-grid">
				<div>
					<p class="hu-fr__kicker">Direkte Zusammenarbeit</p>
					<h2 id="hu-fr-final-title" class="hu-fr__h2">Wenn Website, Tracking und Funnel nicht getrennt gedacht werden sollen.</h2>
				</div>
				<div class="hu-fr__final-action">
					<p>Beschreiben Sie kurz Ausgangslage und Ziel. Sie bekommen eine klare Rückmeldung, ob das Projekt passt und welcher Scope sinnvoll wäre.</p>
					<a class="hu-fr__button hu-fr__button--primary" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_freelancer_final_project" data-track-category="lead_gen" data-track-section="final">Projekt prüfen lassen <span aria-hidden="true"><?php echo hu_arrow_up_right_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static inline SVG ?></span></a>
					<p class="hu-fr__route-links">Oder: <a href="<?php echo esc_url( $agentur_url ); ?>">WordPress Agentur Hannover</a> · <a href="<?php echo esc_url( $tracking_url ); ?>">Server-Side Tracking</a> · <a href="<?php echo esc_url( $about_url ); ?>">Über Haşim</a></p>
				</div>
			</div>
		</section>

	</div>
</main>

<?php get_footer(); ?>
