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
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$request_url  = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
// Dasselbe Portrait wie auf der Startseite, nur zusaetzlich in Darstellungs-
// groesse: 384 px deckt beide Spaltenbreiten bis DPR 3 ab, die 760er Variante
// bleibt fuer alles darueber. Ohne das laedt Mobil 52 KB fuer 120 CSS-Pixel.
$portrait_url     = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait-384.webp';
$portrait_srcset  = sprintf(
	'%1$s/assets/img/hasim-portrait-384.webp 384w, %1$s/assets/img/hasim-portrait-760.webp 760w',
	get_stylesheet_directory_uri()
);
$linkedin_url = 'https://www.linkedin.com/in/hasim-%C3%BCner/';
$mail_address = 'hasim@hasimuener.de';

// Der Prozentwert kommt aus dem E3-Canon, damit eine Korrektur dort auch hier
// ankommt. Bewusst ohne das "ueber" der Canon-Display-Form: auf dieser Seite
// steht die nackte Zahl, ohne Betrag, Zeitraum, Region oder Firmennamen.
$e3_cpl_reduction_percent = defined( 'HU_E3_CPL_REDUCTION_PERCENT' ) ? (int) HU_E3_CPL_REDUCTION_PERCENT : 85;

// Vier gleichwertige Stationen, kein Zeitstrahl. Reihenfolge ist die
// Lesereihenfolge und zugleich die Reveal-Reihenfolge.
$about_stations = [
	[
		'kicker' => '8 Jahre',
		'title'  => 'B2B-Vertrieb',
		'text'   => 'Ich weiß, wie eine Anfrage klingt, aus der ein Auftrag wird.',
	],
	[
		'kicker' => 'Studium',
		'title'  => 'Medienwissenschaft',
		'text'   => 'Seitdem baue ich Websites. Schwerpunkt webbasierte Systeme.',
	],
	[
		'kicker' => 'Eigener Onlineshop',
		'title'  => 'D2C, eigenes Geld',
		// Geschuetzte Leerzeichen: sonst rutscht das Euro-Zeichen allein in die
		// naechste Zeile, sobald die Karte schmal wird.
		'text'   => "Kosten pro Bestellung von 70\u{00A0}€ auf 26\u{00A0}€ gesenkt.",
	],
	[
		'kicker' => 'Heute',
		'title'  => 'Anfrage-Systeme',
		'text'   => sprintf(
			"Kosten pro Anfrage um %d\u{00A0}%% gesenkt, bei einem mittelständischen PV-Installationsbetrieb.",
			$e3_cpl_reduction_percent
		),
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
		<div class="hu-about__inner">

			<!-- 1 — HERO -->
			<header class="hu-about__hero">
				<img
					class="hu-about__portrait"
					src="<?php echo esc_url( $portrait_url ); ?>"
					srcset="<?php echo esc_attr( $portrait_srcset ); ?>"
					sizes="(max-width: 620px) 120px, 168px"
					alt="Haşim Üner"
					width="384"
					height="384"
					fetchpriority="high"
					decoding="async"
				>
				<div class="hu-about__hero-body">
					<h1 class="hu-about__h1">Haşim Üner</h1>
					<p class="hu-about__lead">Ich baue Websites, die Anfragen produzieren — und die Technik dahinter. Pattensen bei Hannover.</p>
				</div>
			</header>

			<!-- 2 — VIER STATIONEN -->
			<section class="hu-about__stations" aria-label="Stationen">
				<ul class="hu-about__station-grid" role="list">
					<?php foreach ( $about_stations as $index => $station ) : ?>
						<li
							class="hu-about__station"
							data-hu-reveal
							style="--hu-about-delay: <?php echo (int) ( $index * 90 ); ?>ms"
						>
							<span class="hu-about__station-kicker"><?php echo esc_html( $station['kicker'] ); ?></span>
							<h2 class="hu-about__station-title"><?php echo esc_html( $station['title'] ); ?></h2>
							<p class="hu-about__station-text"><?php echo esc_html( $station['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>

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

			<!-- 5 — CTA -->
			<section class="hu-about__cta">
				<a
					class="hu-about__cta-btn"
					href="<?php echo esc_url( $request_url ); ?>"
					data-track-action="cta_about_marktcheck"
					data-track-category="lead_gen"
					data-track-section="about_cta"
				>Marktcheck anfragen</a>
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
