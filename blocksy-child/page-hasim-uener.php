<?php
/**
 * Personenseite auf /hasim-uener/.
 *
 * Loest die beiden alten Ueber-Mich-Templates ab (template-about.php,
 * template-about-editorial.php). Sechs Bloecke, keine Story-Strecke:
 * Hero, vier Stationen, Haltung, Werdegang, CTA, Schlusszeile.
 *
 * Route statt Template-Auswahl: die Seite haengt am Slug, damit die
 * Zuordnung nicht mehr im WP-Admin gewaehlt werden muss. Der Seeder
 * nexus_maybe_ensure_about_page() in inc/helpers.php benennt die alte
 * Seite um und setzt dieses Template.
 *
 * Hero und Stationen-Band haengen direkt an .hu-about statt am Inhalts-
 * container: beide brauchen die volle Breite fuer Anschnitt und Farbbruch.
 *
 * Der Werdegang steht zwischen Haltung und CTA, weil die Haltung eine
 * Behauptung ist und der Werdegang ihre Belege liefert. Die fruehere
 * Kompetenzleiste (WordPress, Tracking, Ads, CRO, Automatisierung) ist
 * ersatzlos entfallen: fuenf Etiketten sagen weniger als die Absaetze,
 * die jetzt an ihrer Stelle stehen.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Die Über-Seite gehört zu allen drei Wegen, nicht zum Energy-Cluster. Der
// primäre CTA ist deshalb die generische Projektanfrage; den Marktcheck
// erreicht Energy-Traffic über Header und Footer.
$request_url    = function_exists( 'hu_get_commercial_route' )
	? hu_get_commercial_route( 'project_request', home_url( '/kontakt/' ) )
	: home_url( '/kontakt/' );
$whitelabel_url = function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' );
$e3_case_url    = function_exists( 'hu_e3_canon' )
	? (string) ( hu_e3_canon()['url'] ?? home_url( '/case-study-solar-leadgenerierung/' ) )
	: home_url( '/case-study-solar-leadgenerierung/' );
$linkedin_url   = 'https://www.linkedin.com/in/hasim-uener/';
// Wie in site-header, /whitelabel-retainer/ und Person.sameAs literal gehalten;
// fuer die Zweitdomain gibt es im Theme bisher keinen Helper.
$blog_url       = 'https://hasimuener.org/';
$mail_address   = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'hallo@hasimuener.de';

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
// Das vorangestellte Minus (U+2212, nicht der Bindestrich) gibt der Zahl die
// Richtung, die sie als Display-Zeile allein nicht hat. Es untertreibt
// gegenueber dem kanonischen "ueber 85 %" und behauptet damit nichts Neues.
$e3_cpl_reduction_percent = defined( 'HU_E3_CPL_REDUCTION_PERCENT' ) ? (int) HU_E3_CPL_REDUCTION_PERCENT : 85;

// Vier gleichwertige Stationen, kein Zeitstrahl. Reihenfolge ist die
// Lesereihenfolge und zugleich die Reveal-Reihenfolge. Die Kennzahl steht als
// eigene Display-Zeile ueber Titel und Satz; geschuetzte Leerzeichen halten
// Wert und Einheit zusammen, damit die Einheit nicht allein umbricht.
//
// Nicht jede Station traegt eine Zahl: Station 3 belegt unternehmerisches
// Risiko, keine zweite Kostensenkung. Die Display-Zeile nimmt dort zwei Woerter
// auf. 'wrap' => true erlaubt genau dieser Zeile einen Umbruch, weil das
// pauschale white-space: nowrap den laengeren Text sonst aus der Spalte
// schiebt (Korridor 761-901 px). Schriftgroesse bleibt unangetastet.
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
		// U+2060 bindet die Jahresspanne, damit der Halbgeviertstrich sie in
		// schmalen Spalten nicht zu "2019-" / "2023" trennt.
		'figure' => 'Eigenes Geld',
		'title'  => "Eigener Onlineshop\u{00A0}· 2019\u{2060}–\u{2060}2023",
		'text'   => 'Vier Jahre Anzeigen auf eigene Rechnung.',
		'wrap'   => true,
	],
	[
		'figure' => sprintf( "\u{2212}%d\u{00A0}%%", $e3_cpl_reduction_percent ),
		'title'  => 'Anfrage-Systeme, heute',
		'text'   => 'Weniger Kosten pro Anfrage, bei einem mittelständischen PV-Installationsbetrieb.',
		'url'    => $e3_case_url,
		'label'  => 'Dokumentierten Solar-Case ansehen',
	],
];

// Werdegang: fuenf Kapitel, jedes mit einer tragenden Ueberschrift. Die vier
// Stationen im dunklen Band sind der Index, diese Absaetze sind der Beleg —
// deshalb wiederholen sich Vertrieb, Shop und Studium hier bewusst, aber mit
// dem Detail, das eine Kennzahlzeile nicht tragen kann.
//
// Bewusst statisch und ohne data-hu-reveal: die Seite hat mit Portrait und
// Systemlinie bereits ihre eine erlaubte Bewegungsgruppe.
$about_vita = [
	[
		'title'      => 'Woher ich komme',
		'paragraphs' => [
			'Mein Vater ist Bauunternehmer, Putz und Hochbau. Ich bin mit einem Betrieb am Küchentisch aufgewachsen: Angebote, Termine, Kunden, die anrufen, wenn etwas klemmt. Deshalb ist meine erste Frage an eine Website, was sie dem Betrieb bringt.',
		],
	],
	[
		'title'      => 'Acht Jahre B2B-Vertrieb',
		'paragraphs' => [
			'Firmenkundengeschäft, überwiegend Telekommunikation: Verträge für Unternehmen und die Mobilfunkverträge ihrer Mitarbeiter.',
			'In meinen Verkäufen war der Preis selten der Grund, warum ein Abschluss nicht zustande kam. Häufiger war es der falsche Ansprechpartner, ein Angebot mit zu vielen Optionen oder ein Unternehmen, das mit dem Zustand ganz gut leben konnte. Drei Gründe, die eine Website heute klären kann, bevor jemand zum Telefon greift.',
		],
	],
	[
		'title'      => 'Vier Jahre eigener Onlineshop',
		'paragraphs' => [
			'2019 bis 2023, nachhaltige Produkte: umweltfreundlich, langlebig, erklärungsbedürftig. Anzeigen, Produktseiten, Warenkorb, Checkout, E-Mail-Strecken — alles in meiner Hand, alles auf eigene Rechnung.',
			'2023 gab es meinen Lieferanten in China nicht mehr. Ich habe den Shop geschlossen, statt monatelang Ersatz zu suchen. Eine schnelle Entscheidung, aus dem Bauch — aber eine klare.',
			'Eine bessere Conversion war für mich wertlos, wenn hinterher weniger übrig blieb. Rabatte, Gratisversand, kürzere Formulare: Jede dieser Stellschrauben hebt eine Zahl und senkt eine andere. Wer sie nicht zusammen betrachtet, optimiert sich ärmer. Dieselbe Rechnung führe ich heute für Anfragen: Zwei Anfragen, mit denen Ihr Vertrieb arbeiten kann, sind mehr wert als zehn, die er abtelefonieren muss.',
		],
	],
	[
		'title'      => 'Medienwissenschaft',
		'paragraphs' => [
			'Medientheorie, Mediensoziologie, Medienpsychologie, Medientechnik, webbasierte Informationssysteme. Meine Bachelorarbeit habe ich über persuasive Online-Werbung geschrieben. Mein Befund damals: Die Formate überzeugten kaum. Interaktiv waren sie, vor allem aber störend — Pop-ups, Unterbrechungen.',
			'Überzeugen ist deshalb nichts Anrüchiges. Es wird es erst, wenn eins von zwei Dingen fehlt: dass das Versprechen hält, oder dass der Weg dorthin ehrlich war. Kein erfundener Countdown, keine Knappheit, die keine ist, keine Kosten, die erst im letzten Schritt auftauchen. Bei einem Projekt fange ich deshalb bei der Frage an, was Sie tatsächlich liefern können. Alles andere verschiebt das Problem nur ins Erstgespräch.',
		],
	],
	[
		'title'      => 'Arbeiten mit KI',
		'paragraphs' => [
			'Ich arbeite täglich mit KI-Werkzeugen: beim Recherchieren, beim Bauen, beim Gegenlesen. Das Potenzial ist da — mehr Varianten prüfen, schneller zu einem belastbaren Stand kommen. Es zählt nur, wenn die Qualität dabei oben bleibt.',
			'Was die Werkzeuge nicht liefern, ist der Kontext Ihres Betriebs, ein Gefühl dafür, was ein Kunde meint, wenn er etwas anderes sagt, und die Entscheidung, in welcher Reihenfolge gebaut wird. Das ist der Teil der Arbeit, der geblieben ist — und der schwierigere.',
		],
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
						<p class="hu-about__station-figure<?php echo empty( $station['wrap'] ) ? '' : ' hu-about__station-figure--words'; ?>"><?php echo esc_html( $station['figure'] ); ?></p>
						<h2 class="hu-about__station-title"><?php echo esc_html( $station['title'] ); ?></h2>
						<p class="hu-about__station-text"><?php echo esc_html( $station['text'] ); ?></p>
						<?php if ( ! empty( $station['url'] ) ) : ?>
							<a class="hu-about__station-link" href="<?php echo esc_url( $station['url'] ); ?>" data-track-action="about_station_solar_case" data-track-category="trust" data-track-section="stations"><?php echo esc_html( $station['label'] ); ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<div class="hu-about__inner">

			<!-- 3 — HALTUNG -->
			<section class="hu-about__stance">
				<p class="hu-about__stance-text">
					Die meisten Websites, die ich übernehme, sind technisch nicht kaputt. Sie sind nur nie darauf ausgelegt worden, dass am Ende jemand anruft: Das Formular ist versteckt, niemand misst, welche Anfrage woher kommt, und der Vertrieb erfährt es zuletzt. Ich fange deshalb nicht bei der Gestaltung an, sondern bei der Frage, was am Ende passieren soll — und ändere den Ansatz, wenn die Zahlen etwas anderes sagen.
				</p>
			</section>

			<!-- 4 — WERDEGANG: die Belege hinter der Haltung -->
			<section class="hu-about__vita" aria-label="Werdegang">
				<?php foreach ( $about_vita as $chapter ) : ?>
					<article class="hu-about__vita-item">
						<h2 class="hu-about__vita-title"><?php echo esc_html( $chapter['title'] ); ?></h2>
						<div class="hu-about__vita-body">
							<?php foreach ( $chapter['paragraphs'] as $paragraph ) : ?>
								<p><?php echo esc_html( $paragraph ); ?></p>
							<?php endforeach; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</section>

			<!-- 5 — CTA: zwei klar getrennte Wege, danach die leisen Kontaktwege -->
			<section class="hu-about__cta" aria-labelledby="hu-about-cta-title">
				<header class="hu-about__cta-head">
					<p class="hu-about__cta-kicker">Zusammenarbeit</p>
					<h2 class="hu-about__cta-title" id="hu-about-cta-title">Direkt für Betriebe. Im Hintergrund für Agenturen.</h2>
				</header>

				<div class="hu-about__path-grid">
					<article class="hu-about__path hu-about__path--direct">
						<p class="hu-about__path-label">Für Betriebe</p>
						<h3 class="hu-about__path-title">Direktes Projekt</h3>
						<p class="hu-about__path-text">Sie sprechen mit dem, der es baut. Keine Zwischenebene, kein Account Manager. Wenn ich ausfalle, sage ich Ihnen das am selben Tag und wir verschieben — dafür wissen Sie immer, woran Sie sind.</p>
						<a
							class="hu-about__path-link hu-about__path-link--primary"
							href="<?php echo esc_url( $request_url ); ?>"
							data-track-action="cta_about_project"
							data-track-category="lead_gen"
							data-track-section="about_cta"
						>Projekt anfragen <span aria-hidden="true">→</span></a>
					</article>

					<article class="hu-about__path hu-about__path--whitelabel">
						<p class="hu-about__path-label">Für Agenturen</p>
						<h3 class="hu-about__path-title">White-Label-Umsetzung</h3>
						<?php /* Dritter Satz weicht bewusst vom Entwurf ab: /whitelabel-retainer/ verkauft kein Stundenkontingent, sondern ein Erstprojekt mit fixem Scope, aus dem erst danach ein Monats-Retainer entstehen kann. Die Karte darf der Zielseite nicht widersprechen. */ ?>
						<p class="hu-about__path-text">Sie führen das Kundenprojekt, ich übernehme die technische Umsetzung im Hintergrund. Nach außen tritt Ihre Agentur auf. Scope und Preis stehen vor dem Start fest; laufende Kapazität vereinbaren wir erst nach dem Erstprojekt.</p>
						<a
							class="hu-about__path-link hu-about__path-link--secondary"
							href="<?php echo esc_url( $whitelabel_url ); ?>"
							data-track-action="link_about_whitelabel"
							data-track-category="lead_gen"
							data-track-section="about_cta"
						>White-Label ansehen <span aria-hidden="true">→</span></a>
					</article>
				</div>

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

			<!-- 6 — SCHLUSSZEILE: der einzige Verweis nach draussen, bewusst leise -->
			<p class="hu-about__coda">
				Abseits der Arbeit schreibe ich auf <a
					href="<?php echo esc_url( $blog_url ); ?>"
					rel="me noopener noreferrer"
					target="_blank"
					data-track-action="link_about_blog"
					data-track-category="navigation"
					data-track-section="about_coda"
				>hasimuener.org</a> über Medien und Öffentlichkeit. Andere Baustelle, gleiche Neugier.
			</p>

		</div>
	</div>
</main>

<?php
get_footer();
