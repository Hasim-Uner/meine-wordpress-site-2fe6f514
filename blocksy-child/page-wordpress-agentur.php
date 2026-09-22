<?php
/**
 * Template Name: WordPress Agentur Hannover
 * Description: Lokale Vergleichs- und Entscheidungsseite fuer Unternehmen, die zwischen Agentur und direkter WordPress-Umsetzung waehlen.
 *
 * Die Startseite besitzt den direkten WordPress-/Freelancer-Kaufintent.
 * Diese URL beantwortet bewusst die lokale Agentur-Suche und hilft bei der
 * Wahl des passenden Arbeitsmodells, ohne eine zweite allgemeine Money Page
 * aufzubauen.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'hu_forced_singular_seo_map', function ( $map ) {
	if ( ! is_array( $map ) ) {
		$map = [];
	}

	$map['wordpress-agentur-hannover'] = [
		'title'       => 'WordPress Agentur Hannover | Persönlicher Umsetzungspartner',
		'description' => 'Sie suchen eine WordPress Agentur in Hannover? Vergleichen Sie klassische Agentur und direkte Umsetzung für B2B-WordPress, SEO, Tracking und CRO. Sitz: Pattensen bei Hannover.',
	];

	return $map;
}, 90 );

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'hu-wayfinding-active';
	$classes[] = 'hu-wayfinding-results';
	$classes[] = 'hu-agentur-decision-page';
	return array_values( array_unique( $classes ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$navigation_css = '/assets/css/navigation-ecosystem.css';
	$navigation_js  = '/assets/js/navigation-ecosystem.js';
	$route_css      = '/assets/css/agentur-decision.css';

	if ( is_file( $dir . $navigation_css ) ) {
		wp_enqueue_style( 'hu-navigation-ecosystem', $uri . $navigation_css, [], (string) filemtime( $dir . $navigation_css ) );
	}
	if ( is_file( $dir . $route_css ) ) {
		wp_enqueue_style( 'hu-agentur-decision', $uri . $route_css, [ 'nexus-system-css', 'hu-navigation-ecosystem' ], (string) filemtime( $dir . $route_css ) );
	}
	if ( is_file( $dir . $navigation_js ) ) {
		wp_enqueue_script( 'hu-navigation-ecosystem', $uri . $navigation_js, [], (string) filemtime( $dir . $navigation_js ), true );
	}
}, 90 );

add_action( 'wp_body_open', function () {
	?>
	<nav class="hu-wayfinding-breadcrumb" aria-label="Breadcrumb" data-track-section="breadcrumb">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track-action="breadcrumb_home" data-track-category="navigation">Startseite</a></li>
			<li><span aria-current="page">WordPress Agentur Hannover</span></li>
		</ol>
	</nav>
	<?php
}, 30 );

add_action( 'wp_body_open', function () {
	$items = [
		[ 'id' => 'entscheidung', 'label' => 'Entscheidung' ],
		[ 'id' => 'technik', 'label' => 'Vergleich' ],
		[ 'id' => 'zusammenarbeit', 'label' => 'Zusammenarbeit' ],
		[ 'id' => 'belege', 'label' => 'Belege' ],
		[ 'id' => 'hannover', 'label' => 'Hannover' ],
		[ 'id' => 'faq', 'label' => 'FAQ' ],
	];
	?>
	<nav class="hu-page-toc hu-results-register" aria-label="Abschnitte dieses Dokuments" data-hu-rail="true" data-hu-results-register-ready="true" data-track-section="page_toc">
		<span class="hu-results-register__marke" aria-hidden="true"><span class="hu-results-register__marke-short">Reg.</span><span class="hu-results-register__marke-full">Register</span></span>
		<div class="hu-results-register__entries">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php $number = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ); ?>
				<a href="#<?php echo esc_attr( $item['id'] ); ?>" data-track-action="toc_agentur_<?php echo esc_attr( sanitize_key( $item['id'] ) ); ?>" data-track-category="navigation"><span class="hu-results-register__nr"><?php echo esc_html( $number ); ?></span><span class="hu-results-register__txt"><?php echo esc_html( $item['label'] ); ?></span></a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
}, 31 );

$contact_url = function_exists( 'hu_get_contact_intake_url' )
	? hu_get_contact_intake_url( 'project', 'implementation_scope' )
	: add_query_arg(
		[
			'type'  => 'project',
			'focus' => 'implementation_scope',
		],
		home_url( '/kontakt/' )
	);
$offers_url   = home_url( '/#angebote' );
$results_url  = function_exists( 'nexus_get_results_url' ) ? nexus_get_results_url() : home_url( '/ergebnisse/' );
$tracking_url = home_url( '/ga4-tracking-setup/' );
$case_url     = home_url( '/case-study-solar-leadgenerierung/' );
$response     = hu_response_promise( 'compact' );
$faqs         = function_exists( 'nexus_get_agentur_faq_items' ) ? nexus_get_agentur_faq_items() : [];

get_header();
?>

<div class="site-main doku agentur-decision" data-track-page="wordpress_agentur_hannover_decision">
	<header class="kopfteil" data-track-section="agentur_hero">
		<div class="blatt">
			<p class="gegenstand">WordPress Agentur Hannover · Arbeitsmodell vergleichen</p>
			<h1>WordPress Agentur Hannover – oder direkter Umsetzungspartner?</h1>
			<p class="aufriss">
				<span class="erst">Sie suchen eine Agentur. Die wichtigere Frage ist, welches Arbeitsmodell Ihr Projekt wirklich braucht.</span>
				Bei mir bekommen Sie bewusst kein großes Agenturteam, sondern direkte Zusammenarbeit mit der Person, die WordPress, technische SEO, Tracking und Conversion tatsächlich umsetzt. Wenn Ihr Projekt Vertretung, 24/7-Bereitschaft oder viele parallele Fachdisziplinen braucht, ist eine klassische Agentur die bessere Wahl.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_hero_project" data-track-category="lead_gen">
					Projekt einordnen <span class="pf" aria-hidden="true">→</span>
				</a>
				<a class="tun still" href="<?php echo esc_url( $offers_url ); ?>">WordPress-Leistungen &amp; Preise</a>
			</div>

			<p class="mono"><?php echo esc_html( $response ); ?> · Sitz in Pattensen bei Hannover · Umsetzung DACH-weit</p>

			<div class="meta" aria-label="Arbeitsmodell im Überblick">
				<dl>
					<div><dt>Verantwortung</dt><dd>direkt bei mir</dd></div>
					<div><dt>Schwerpunkt</dt><dd>WordPress · SEO · Tracking · CRO</dd></div>
					<div><dt>Zusammenarbeit</dt><dd>projektbezogen oder laufend</dd></div>
					<div><dt>Region</dt><dd>Hannover · remote im DACH-Raum</dd></div>
				</dl>
			</div>
		</div>
	</header>

	<section id="entscheidung" data-track-section="agentur_decision">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Entscheidung</span><span class="strich"></span></div>
			</div>

			<div class="haupt">
				<p class="mono stempelfarbe">Nicht „Agentur oder Freelancer?“ – sondern passendes Betriebsmodell</p>
				<h2 class="kopf">Zwei gute Modelle. Für unterschiedliche Situationen.</h2>
				<p class="vorspann">Eine Agentur ist nicht automatisch besser, ein Einzelumsetzer nicht automatisch günstiger oder schneller. Entscheidend sind Verantwortungsweg, Parallelität, Risikoabsicherung und die Frage, wie eng Strategie und technische Umsetzung zusammenliegen sollen.</p>

				<div class="entscheidung-grid">
					<article class="entscheidung-karte">
						<p class="mono stempelfarbe">Klassische Agentur</p>
						<h3>Stärker, wenn viele Rollen gleichzeitig gebraucht werden.</h3>
						<p>Geeignet für größere Rollouts, feste Vertretungsmodelle, parallele Spezialdisziplinen, SLA-Anforderungen oder Beschaffungsprozesse, die ausdrücklich ein Team verlangen.</p>
					</article>
					<article class="entscheidung-karte">
						<p class="mono stempelfarbe">Direkte Umsetzung</p>
						<h3>Stärker, wenn Verantwortung und Umsetzung nah beieinander bleiben sollen.</h3>
						<p>Geeignet für B2B-Websites, Relaunches, Landingpages und technische Weiterentwicklung, wenn Entscheidungen ohne Account-Management-Schleifen direkt mit dem Umsetzer getroffen werden sollen.</p>
					</article>
				</div>
			</div>

			<aside class="marg">
				<p class="note"><span class="label">Transparenz</span>Ich bin keine klassische Mehrpersonen-Agentur. Die URL beantwortet den Suchbegriff „WordPress Agentur Hannover“, aber die Zusammenarbeit selbst ist bewusst persönlich.</p>
			</aside>
		</div>
	</section>

	<section id="technik" data-track-section="agentur_comparison">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Vergleich</span><span class="strich"></span></div>
			</div>

			<div class="voll">
				<div class="tafel">
					<p class="mono stempelfarbe">Entscheidungsmatrix</p>
					<h2 class="kopf">Wann passt welches Modell?</h2>
					<p class="vorspann">Keine künstliche Gewinner-Spalte. Es gibt Anforderungen, bei denen Sie mit einem Team besser aufgehoben sind – und andere, bei denen ein direkter technischer Verantwortungsweg mehr Wert schafft.</p>

					<div class="vergleich" role="table" aria-label="Vergleich klassische Agentur und direkte Umsetzung">
						<div class="vergleich-zeile vergleich-kopf" role="row">
							<div role="columnheader">Kriterium</div>
							<strong role="columnheader">Klassische Agentur</strong>
							<strong role="columnheader">Direkt mit Haşim Üner</strong>
						</div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Ansprechpartner</div><div role="cell">häufig Projektleitung plus Fachteam</div><div role="cell">direkt die umsetzende Person</div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Parallele Kapazität</div><div role="cell">stärker bei mehreren gleichzeitig laufenden Disziplinen</div><div role="cell">bewusst begrenzter Scope und weniger Übergaben</div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">WordPress + Messung</div><div role="cell">abhängig von Teamstruktur und Übergaben</div><div role="cell">Entwicklung, Tracking und Conversion werden gemeinsam entschieden</div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Vertretung / SLA</div><div role="cell">geeigneter für feste Vertretung und 24/7-Anforderungen</div><div role="cell">keine 24/7-Bereitschaft und kein austauschbares Vertretungsteam</div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Kommunikationsweg</div><div role="cell">mehr Rollen können mehr Abstimmung bedeuten</div><div role="cell">kurzer Weg von Entscheidung zu Umsetzung</div></div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="zusammenarbeit" data-track-section="agentur_collaboration">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Zusammenarbeit</span><span class="strich"></span></div>
			</div>

			<div class="haupt">
				<p class="mono stempelfarbe">Wenn direkte Umsetzung passt</p>
				<h2 class="kopf">Ein Verantwortungsweg von WordPress bis zur Messung.</h2>
				<p class="vorspann">Die Startseite ist der Leistungsowner für direkte WordPress-Projekte. Hier reicht deshalb die Entscheidung: Passt das Arbeitsmodell, gelangen Sie von hier in den passenden Scope – ohne dass diese Seite dieselben Leistungen noch einmal komplett dupliziert.</p>

				<div class="protokoll">
					<div class="z"><span>01 · Build</span><b>WordPress-Websites, Relaunches und Landingpages mit sauberer technischer Übergabe</b></div>
					<div class="z"><span>02 · Sichtbarkeit</span><b>technische SEO, Seitenarchitektur, Performance und Barrierefreiheit</b></div>
					<div class="z"><span>03 · Messbarkeit</span><b>GA4, GTM, Consent und Conversion Tracking als eigener oder integrierter Scope</b></div>
					<div class="z"><span>04 · Weiterentwicklung</span><b>begrenzte laufende Betreuung für Systeme, die technisch geprüft oder von mir aufgebaut wurden</b></div>
				</div>

				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $offers_url ); ?>">Direkte WordPress-Leistungen ansehen</a>
					<a class="textlink" href="<?php echo esc_url( $tracking_url ); ?>">Conversion Tracking separat ansehen</a>
				</div>
			</div>

			<aside class="marg">
				<p class="note"><span class="label">Nicht passend</span>24/7-Support, große E-Commerce-Projekte, permanent parallele Kreativ-, Media- und Development-Teams oder reine Design-Relaunches ohne technischen bzw. messbaren Zweck.</p>
			</aside>
		</div>
	</section>

	<section id="belege" data-track-section="agentur_proof">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Belege</span><span class="strich"></span></div>
			</div>

			<div class="haupt">
				<p class="mono stempelfarbe">Proof ohne geliehene Agentur-Logos</p>
				<h2 class="kopf">Prüfbare Arbeit statt austauschbarer Leistungsbehauptungen.</h2>
				<p class="vorspann">Der Ergebnisse-Bereich trennt öffentlich prüfbare WordPress-Arbeiten, technische Nachweise und den dokumentierten Solar-Referenzfall. Der Solar-Case belegt das Zusammenspiel eines gesamten Anfragesystems – nicht die Wirkung einer isolierten WordPress- oder Tracking-Maßnahme.</p>

				<div class="beleg-links">
					<a class="beleg-link" href="<?php echo esc_url( $results_url ); ?>">
						<span>WordPress &amp; Technik</span>
						<strong>Ergebnisse und technische Belege öffnen</strong>
						Prüfbare Projekte, Rolle, technischer Scope und Nachweise.
					</a>
					<a class="beleg-link" href="<?php echo esc_url( $case_url ); ?>">
						<span>Referenzfall · System</span>
						<strong>Solar-Case mit Zahlen und Methodik öffnen</strong>
						Gesamtsystem aus Nachfrage, Website, Qualifizierung, Tracking und Vertrieb.
					</a>
				</div>
			</div>

			<aside class="marg">
				<p class="note"><span class="label">Beweisstandard</span>Was ein einzelnes Gewerk nicht isoliert belegt, wird auch nicht als isolierte Erfolgszusage verkauft.</p>
			</aside>
		</div>
	</section>

	<section id="hannover" data-track-section="agentur_local">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Hannover</span><span class="strich"></span></div>
			</div>

			<div class="haupt">
				<p class="mono stempelfarbe">Lokal erreichbar, nicht lokal begrenzt</p>
				<h2 class="kopf">Sitz in Pattensen bei Hannover. Umsetzung im DACH-Raum.</h2>
				<p class="vorspann">Für Unternehmen aus Hannover, Pattensen, Braunschweig, Wolfsburg, Hildesheim und Celle sind persönliche Reviews und Workshops nach Vereinbarung möglich. Entwicklung, QA, Tracking und laufende Abstimmung funktionieren ebenso remote – deshalb ist die Zusammenarbeit nicht auf die Region begrenzt.</p>

				<dl class="lokal-zeile">
					<div><dt>Sitz</dt><dd>Pattensen bei Hannover</dd></div>
					<div><dt>Persönlich</dt><dd>Region Hannover und Niedersachsen nach Vereinbarung</dd></div>
					<div><dt>Umsetzung</dt><dd>remote im gesamten DACH-Raum</dd></div>
				</dl>
			</div>
		</div>
	</section>

	<section id="faq" data-track-section="agentur_faq">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">06</span><span class="titel">FAQ</span><span class="strich"></span></div>
			</div>

			<div class="haupt">
				<p class="mono stempelfarbe">Vor der Entscheidung</p>
				<h2 class="kopf leise">Häufige Fragen zur WordPress-Zusammenarbeit in Hannover.</h2>

				<?php if ( ! empty( $faqs ) ) : ?>
					<div class="fragen">
						<?php foreach ( $faqs as $item ) : ?>
							<?php
							$question = isset( $item['question'] ) ? (string) $item['question'] : '';
							$answer   = isset( $item['answer'] ) ? (string) $item['answer'] : '';
							if ( '' === $question || '' === $answer ) {
								continue;
							}
							?>
							<details>
								<summary><?php echo esc_html( $question ); ?></summary>
								<div class="huelle"><div><p class="antwort"><?php echo esc_html( $answer ); ?></p></div></div>
							</details>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="abschluss" id="anfrage" data-track-section="agentur_final_cta">
		<div class="blatt reihe">
			<div class="ganz">
				<div class="tafel">
					<div class="reihe">
						<div class="haupt">
							<p class="mono stempelfarbe">Nächster Schritt</p>
							<h2>Sie brauchen keine Agentur-Schublade. Sie brauchen das passende Arbeitsmodell.</h2>
							<p class="aufriss">Beschreiben Sie kurz Website, Ziel und aktuellen Engpass. Ich sage Ihnen, ob eine direkte Zusammenarbeit sinnvoll ist – oder ob Ihr Projekt mit einem größeren Team besser aufgehoben ist.</p>
							<div class="ausgang">
								<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_final_project" data-track-category="lead_gen">Projekt einordnen <span class="pf" aria-hidden="true">→</span></a>
							</div>
						</div>
						<aside class="marg">
							<p class="note"><span class="label">Ausgang</span>Direkter Scope, Verweis auf eine passendere Leistung – oder ein klares Nein. Kein Pflicht-Relaunch und kein Standard-Pitch.</p>
						</aside>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php
get_footer();