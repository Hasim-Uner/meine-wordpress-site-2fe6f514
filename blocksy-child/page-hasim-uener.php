<?php
/**
 * Personenseite auf /hasim-uener/.
 *
 * Loest die beiden alten Ueber-Mich-Templates ab (template-about.php,
 * template-about-editorial.php). Fuenf Bloecke, keine Story-Strecke:
 * Hero, vier Stationen, Kompetenzraster, Haltung, CTA.
 *
 * Route statt Template-Auswahl: die Seite haengt am Slug, damit die
 * Zuordnung nicht mehr im WP-Admin gewaehlt werden muss. Der Seeder
 * nexus_maybe_ensure_about_page() in inc/helpers.php benennt die alte
 * Seite um und setzt dieses Template.
 *
 * Bloecke 1, 2 und 4 haengen direkt an .hu-about statt am Inhalts-
 * container: Hero und Stationen-Band brauchen die volle Breite fuer
 * Anschnitt und Farbbruch.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$request_url    = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$whitelabel_url = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );
$linkedin_url   = 'https://www.linkedin.com/in/hasim-%C3%BCner/';
$mail_address   = 'hasim@hasimuener.de';

// Portrait im 3:4-Ausschnitt. Der neue Hero setzt das Bild als eigene,
// responsiv zugeschnittene Buehne ein statt es am Viewport anzuschneiden.
// Zwei Groessen: 400 deckt Mobil und 1x-Desktop, 800 die Retina-Faelle.
$portrait_url    = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait-400x533.webp';
$portrait_srcset = sprintf(
	'%1$s/assets/img/hasim-portrait-400x533.webp 400w, %1$s/assets/img/hasim-portrait-800x1067.webp 800w',
	get_stylesheet_directory_uri()
);

// Der Prozentwert kommt aus dem E3-Canon, damit eine Korrektur dort auch hier
// ankommt. Bewusst ohne das "ueber" der Canon-Display-Form: auf dieser Seite
// steht die nackte Zahl, ohne Betrag, Zeitraum, Region oder Firmennamen.
$e3_cpl_reduction_percent = defined( 'HU_E3_CPL_REDUCTION_PERCENT' ) ? (int) HU_E3_CPL_REDUCTION_PERCENT : 85;

// Vier gleichwertige Stationen, kein Zeitstrahl. Reihenfolge ist die
// Lesereihenfolge und zugleich die Reveal-Reihenfolge. Die Kennzahl steht als
// eigene Display-Zeile ueber Titel und Satz; geschuetzte Leerzeichen halten
// Wert und Einheit zusammen, damit die Einheit nicht allein umbricht.
$about_stations = [
	[
		'figure' => '8 Jahre',
		'title'  => 'B2B-Vertrieb',
		'text'   => 'Ich weiß, wie eine Anfrage klingt, aus der ein Auftrag wird.',
	],
	[
		'figure' => 'Studium',
		'title'  => 'Medienwissenschaft',
		'text'   => 'Seitdem baue ich Websites. Schwerpunkt webbasierte Systeme.',
	],
	[
		'figure' => "70\u{00A0}€ → 26\u{00A0}€",
		'title'  => 'Eigener Onlineshop',
		'text'   => 'Kosten pro Bestellung. D2C, auf eigenes Geld.',
	],
	[
		'figure' => sprintf( "%d\u{00A0}%%", $e3_cpl_reduction_percent ),
		'title'  => 'Anfrage-Systeme, heute',
		'text'   => 'Weniger Kosten pro Anfrage, bei einem mittelständischen PV-Installationsbetrieb.',
	],
];

// Kompetenzraster: ein Wort je Kachel, Icon rein dekorativ (aria-hidden).
// Die Pfade sind bewusst kurz gehalten — 24er-Viewbox, currentColor, kein Fill.
$about_skills = [
	[
		'label' => 'WordPress',
		'path'  => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/><path d="M7 6.5h.01"/>',
	],
	[
		'label' => 'Tracking',
		'path'  => '<path d="M4 19V5"/><path d="M4 19h16"/><path d="m7 15 4-5 3 3 5-6"/>',
	],
	[
		'label' => 'Ads',
		'path'  => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="1"/>',
	],
	[
		'label' => 'CRO',
		'path'  => '<path d="M4 5h16l-6 7v6l-4 2v-8Z"/>',
	],
	[
		'label' => 'Automatisierung',
		'path'  => '<path d="M20 12a8 8 0 1 1-2.3-5.6"/><path d="M20 4v4h-4"/>',
	],
];

get_header();
?>

<main id="main" class="site-main">
	<div class="hu-about" data-track-section="about_page">

		<!-- 1 — HERO -->
		<header class="hu-about__hero">
			<div class="hu-about__hero-inner">
				<div class="hu-about__hero-copy">
					<h1 class="hu-about__h1">Haşim Üner</h1>
					<p class="hu-about__lead">
						<span class="hu-about__lead-main">Ich baue Websites, die Anfragen produzieren — und die Technik dahinter.</span>
						<span class="hu-about__lead-place">Pattensen bei Hannover.</span>
					</p>
				</div>
				<div class="hu-about__hero-visual">
					<img
						class="hu-about__portrait"
						src="<?php echo esc_url( $portrait_url ); ?>"
						srcset="<?php echo esc_attr( $portrait_srcset ); ?>"
						sizes="(max-width: 760px) calc(100vw - 48px), (max-width: 1100px) 42vw, 480px"
						alt="Porträt von Haşim Üner"
						width="400"
						height="533"
						fetchpriority="high"
						decoding="async"
					>
					<span class="hu-about__portrait-axis" aria-hidden="true"></span>
				</div>
			</div>
			<span class="hu-about__hero-signal" aria-hidden="true"></span>
		</header>

		<!-- 2 — VIER STATIONEN -->
		<section class="hu-about__band" aria-label="Stationen">
			<ul class="hu-about__station-grid" role="list">
				<?php foreach ( $about_stations as $station ) : ?>
					<li
						class="hu-about__station"
						data-hu-reveal
					>
						<p class="hu-about__station-figure"><?php echo esc_html( $station['figure'] ); ?></p>
						<h2 class="hu-about__station-title"><?php echo esc_html( $station['title'] ); ?></h2>
						<p class="hu-about__station-text"><?php echo esc_html( $station['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<div class="hu-about__inner">

			<!-- 3 — KOMPETENZRASTER -->
			<section class="hu-about__skills" aria-label="Kompetenzen">
				<ul class="hu-about__skill-grid" role="list">
					<?php foreach ( $about_skills as $skill ) : ?>
						<li class="hu-about__skill">
							<svg
								class="hu-about__skill-icon"
								viewBox="0 0 24 24"
								width="24"
								height="24"
								fill="none"
								stroke="currentColor"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
								aria-hidden="true"
								focusable="false"
							><?php echo $skill['path']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- statische SVG-Pfade aus dem Array oben. ?></svg>
							<span class="hu-about__skill-label"><?php echo esc_html( $skill['label'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>

			<!-- 4 — HALTUNG -->
			<section class="hu-about__stance">
				<p class="hu-about__stance-text">
					Die meisten Websites, die ich übernehme, sind technisch nicht kaputt. Sie sind nur nie darauf ausgelegt worden, dass am Ende jemand anruft: Das Formular ist versteckt, niemand misst, welche Anfrage woher kommt, und der Vertrieb erfährt es zuletzt. Ich fange deshalb nicht bei der Gestaltung an, sondern bei der Frage, was am Ende passieren soll — und ändere den Ansatz, wenn die Zahlen etwas anderes sagen.
				</p>
			</section>

			<!-- 5 — CTA: zwei Ausgaenge, danach die leisen Kontaktwege -->
			<section class="hu-about__cta">
				<p class="hu-about__cta-note">Sie betreiben selbst einen Solar- oder Wärmepumpenbetrieb</p>
				<a
					class="hu-about__cta-btn"
					href="<?php echo esc_url( $request_url ); ?>"
					data-track-action="cta_about_marktcheck"
					data-track-category="lead_gen"
					data-track-section="about_cta"
				>Marktcheck anfragen</a>
				<p class="hu-about__cta-secondary">
					<a
						href="<?php echo esc_url( $whitelabel_url ); ?>"
						data-track-action="link_about_whitelabel"
						data-track-category="lead_gen"
						data-track-section="about_cta"
					>Sie sind Agentur und suchen Umsetzung <span class="hu-about__cta-target"><span aria-hidden="true">→</span> White-Label</span></a>
				</p>
				<p class="hu-about__cta-links">
					<a
						href="<?php echo esc_url( $linkedin_url ); ?>"
						rel="me noopener noreferrer"
						target="_blank"
						data-track-action="link_about_linkedin"
						data-track-category="navigation"
						data-track-section="about_cta"
					>LinkedIn</a>
					<a
						href="mailto:<?php echo esc_attr( $mail_address ); ?>"
						data-track-action="link_about_mail"
						data-track-category="navigation"
						data-track-section="about_cta"
					>E-Mail</a>
				</p>
			</section>

		</div>
	</div>
</main>

<?php
get_footer();
