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
		'title'       => 'WordPress Agentur Hannover: direkte Umsetzung | Haşim Üner',
		'description' => 'WordPress Agentur Hannover gesucht? Direkte B2B-Umsetzung für WordPress, technisches SEO, Tracking und Conversion – ohne unnötige Übergaben.',
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
		[ 'id' => 'entscheidung', 'label' => 'Prinzip' ],
		[ 'id' => 'belege', 'label' => 'Beleg' ],
		[ 'id' => 'technik', 'label' => 'Vergleich' ],
		[ 'id' => 'zusammenarbeit', 'label' => 'Leistung' ],
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

// Ohne Vorauswahl: Wer „WordPress Agentur“ sucht, will meist eine neue
// Website oder einen Relaunch. Die Kontaktseite fragt das Vorhaben selbst ab,
// statt jede Anfrage als „Bestehende Website weiterentwickeln“ abzulegen.
$contact_url    = hu_get_navigation_project_request_url();
$website_url    = hu_get_commercial_route( 'website', home_url( '/wordpress-website-erstellen-lassen/' ) );
$tracking_url   = hu_get_commercial_route( 'tracking_setup', home_url( '/ga4-tracking-setup/' ) );
$conversion_url = hu_get_commercial_route( 'conversion', home_url( '/conversion-optimierung/' ) );
$references_url = home_url( '/#referenzen' );
$case_url       = nexus_get_results_url();
$response       = hu_response_promise( 'compact' );
$faqs         = function_exists( 'nexus_get_agentur_faq_items' ) ? nexus_get_agentur_faq_items() : [];

get_header();
?>

<div class="site-main doku agentur-decision" data-track-page="wordpress_agentur_hannover_decision">
	<header class="kopfteil" data-track-section="agentur_hero">
		<div class="blatt">
			<p class="gegenstand">WordPress Agentur Hannover · direkte Expertenverantwortung</p>
			<h1>WordPress Agentur Hannover – oder brauchen Sie jemanden, der es selbst umsetzt?</h1>
			<p class="aufriss">
				<span class="erst">Sie suchen eine WordPress Agentur in Hannover. Vielleicht brauchen Sie aber kein größeres Team, sondern einen Verantwortlichen, der Strategie und Umsetzung zusammenhält.</span>
				Ich verbinde WordPress, technisches SEO, Tracking und Conversion in einer Verantwortung. Sie sprechen mit der Person, die entscheidet, baut, prüft und die Wirkung danach wieder einordnet – ohne Übergabe zwischen Account-Management und Umsetzung.
			</p>

			<div class="ausgang">
				<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_hero_project" data-track-category="lead_gen">
					Projekt einordnen <span class="pf" aria-hidden="true">→</span>
				</a>
				<a class="tun still" href="<?php echo esc_url( $website_url ); ?>" data-track-action="cta_agentur_hero_website" data-track-category="navigation">Website-Angebot ansehen</a>
			</div>

			<a class="agentur-proofline" href="<?php echo esc_url( $case_url ); ?>" data-track-action="agentur_hero_case" data-track-category="proof">
				<strong>−80 %</strong>
				<span>Kosten pro qualifizierter Anfrage · dokumentierter B2B-Fall, bewusst gerundet</span>
			</a>

			<p class="mono"><?php echo esc_html( $response ); ?> · Sitz in Pattensen bei Hannover · Umsetzung DACH-weit</p>

			<div class="meta" aria-label="Arbeitsmodell im Überblick">
				<dl>
					<div><dt>Verantwortung</dt><dd>Strategie + Umsetzung</dd></div>
					<div><dt>System</dt><dd>WordPress · SEO · Tracking · CRO</dd></div>
					<div><dt>Arbeitsweg</dt><dd>direkt mit dem Entwickler</dd></div>
					<div><dt>Region</dt><dd>Hannover · remote im DACH-Raum</dd></div>
				</dl>
			</div>
		</div>
	</header>

	<section id="entscheidung" data-track-section="agentur_decision">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Prinzip</span><span class="strich"></span></div>
			</div>

			<div class="haupt">
				<p class="mono stempelfarbe">Der Unterschied ist strukturell</p>
				<h2 class="kopf">Das Problem ist selten die Teamgröße. Es sind die Übergaben.</h2>
				<p class="vorspann">Eine Agentur kann mehr Rollen parallel bereitstellen. Das ist wertvoll, wenn ein Projekt genau diese Kapazität braucht. Bei einer B2B-Website hängen WordPress, technische SEO, Messung und Conversion aber eng zusammen. Jede zusätzliche Übergabe ist dann eine Stelle, an der Kontext verloren gehen kann.</p>

				<div class="modellvergleich" aria-label="Vergleich der Verantwortungswege">
					<article class="modellpfad modellpfad--agentur">
						<p class="mono stempelfarbe">Klassische Struktur</p>
						<h3>Mehr Kapazität, mehr Schnittstellen.</h3>
						<div class="modellpfad__linie" aria-label="Typischer Agenturweg">
							<span class="modellpfad__schritt">Unternehmen</span>
							<span class="modellpfad__schritt">Projektleitung</span>
							<span class="modellpfad__schritt">Fachteams</span>
							<span class="modellpfad__schritt">Übergabe</span>
						</div>
						<p>Sinnvoll bei großen Rollouts, festen Vertretungsmodellen, SLA-Anforderungen oder mehreren Disziplinen, die wirklich gleichzeitig arbeiten müssen.</p>
					</article>

					<article class="modellpfad modellpfad--direkt">
						<p class="mono stempelfarbe">Direkte Verantwortung</p>
						<h3>Weniger Übergaben, ein gemeinsamer Kontext.</h3>
						<div class="modellpfad__linie" aria-label="Direkter Verantwortungsweg">
							<span class="modellpfad__schritt">Unternehmen</span>
							<span class="modellpfad__schritt">Haşim</span>
							<span class="modellpfad__schritt">Website + Daten</span>
							<span class="modellpfad__schritt">Weiterentwicklung</span>
						</div>
						<p>Strategische Entscheidung, technische Umsetzung und spätere Auswertung bleiben bei derselben Person. Das verkürzt nicht nur Kommunikation, sondern hält Ursache und Wirkung näher zusammen.</p>
					</article>
				</div>
			</div>

			<aside class="marg">
				<p class="note"><span class="label">Kein Anti-Agentur-Pitch</span>Wenn Ihr Projekt 24/7-Bereitschaft, Vertretung oder mehrere Teams parallel braucht, ist eine größere Agentur strukturell die bessere Wahl.</p>
			</aside>
		</div>
	</section>

	<section id="belege" data-track-section="agentur_proof">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Beleg</span><span class="strich"></span></div>
			</div>

			<div class="voll">
				<div class="proof-messung">
					<div class="proof-messung__zahl" aria-label="80 Prozent weniger Kosten pro qualifizierter Anfrage">
						<span>−80</span><small>%</small>
					</div>
					<div class="proof-messung__text">
						<p class="mono">Kosten pro qualifizierter Anfrage</p>
						<h2>Beleg vor Behauptung.</h2>
						<p>Im dokumentierten B2B-Fall wurde nicht ein einzelner WordPress-Baustein optimiert, sondern die gesamte Strecke: Zielseiten, Vorqualifizierung, Tracking und Übergabe an den Vertrieb. Die Prozentzahl ist hier bewusst konservativ gerundet und keine Erfolgszusage für andere Projekte.</p>
					</div>
				</div>

				<div class="beleg-links">
					<a class="beleg-link" href="<?php echo esc_url( $references_url ); ?>" data-track-action="agentur_proof_references" data-track-category="proof">
						<span>WordPress · öffentlich prüfbar</span>
						<strong>Websites ansehen, an denen ich gebaut habe</strong>
						<em>Reale URLs statt Logo-Wand.</em>
					</a>
					<a class="beleg-link" href="<?php echo esc_url( $case_url ); ?>" data-track-action="agentur_proof_case" data-track-category="proof">
						<span>Dokumentierter B2B-Fall</span>
						<strong>Methodik und Zahlen im Zusammenhang lesen</strong>
						<em>Keine isolierte WordPress-Erfolgsbehauptung.</em>
					</a>
				</div>
			</div>
		</div>
	</section>

	<section id="technik" data-track-section="agentur_comparison">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Vergleich</span><span class="strich"></span></div>
			</div>

			<div class="voll">
				<div class="tafel">
					<p class="mono stempelfarbe">Entscheidungsmatrix</p>
					<h2 class="kopf">Wann ist eine WordPress-Agentur besser – und wann direkte Umsetzung?</h2>
					<p class="vorspann">Die richtige Wahl hängt nicht am Etikett. Sie hängt daran, welche Organisationsform das Risiko Ihres Projekts tatsächlich reduziert.</p>

					<div class="vergleich" role="table" aria-label="Vergleich klassische Agentur und direkte Umsetzung">
						<div class="vergleich-zeile vergleich-kopf" role="row">
							<div role="columnheader">Anforderung</div>
							<strong role="columnheader">Klassische Agentur</strong>
							<strong role="columnheader">Direkt mit Haşim Üner</strong>
						</div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Viele Rollen gleichzeitig</div><div role="cell">stärker bei großen parallelen Teams</div><div role="cell">bewusst begrenzter Scope</div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Vertretung / SLA</div><div role="cell">geeigneter für feste Vertretung und 24/7-Anforderungen</div><div role="cell">keine 24/7-Bereitschaft, keine austauschbare Vertretung</div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Entscheidungsweg</div><div role="cell">Projektleitung und Fachteam können getrennt sein</div><div role="cell"><strong>Entscheidung und Umsetzung in einem Gespräch</strong></div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">WordPress + SEO + Tracking + CRO</div><div role="cell">Qualität hängt von Teamstruktur und Handoffs ab</div><div role="cell"><strong>ein gemeinsamer technischer Kontext</strong></div></div>
						<div class="vergleich-zeile" role="row"><div role="rowheader">Typischer Fit</div><div role="cell">großer Rollout, mehrere Gewerke, Beschaffungsprozess</div><div role="cell"><strong>B2B-Website, Relaunch, Landingpage, technische Weiterentwicklung</strong></div></div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="zusammenarbeit" data-track-section="agentur_collaboration">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Leistung</span><span class="strich"></span></div>
			</div>

			<div class="haupt">
				<p class="mono stempelfarbe">Warum direkte Zusammenarbeit?</p>
				<h2 class="kopf">Eine Person verantwortet den Zusammenhang.</h2>
				<p class="vorspann">Der Mehrwert entsteht nicht dadurch, dass eine Person „alles kann“. Er entsteht dort, wo Entscheidungen aus mehreren Disziplinen dieselbe Website betreffen und deshalb gemeinsam getroffen werden sollten.</p>

				<div class="verantwortung-grid">
					<div><span>01</span><h3>WordPress</h3><p>Architektur, Entwicklung, Relaunch, Performance und saubere Übergabe.</p></div>
					<div><span>02</span><h3>Technisches SEO</h3><p>Suchintention, Seitenrollen, interne Verlinkung, Titles, Canonicals, Schema und Weiterleitungen.</p></div>
					<div><span>03</span><h3>Tracking</h3><p>Messplan, GA4, Tag Manager und die Herkunft einer Anfrage – nur soweit das Projekt es braucht.</p></div>
					<div><span>04</span><h3>Conversion</h3><p>Angebotslogik, Anfragepfad, Formulare und Übergabe an Vertrieb oder CRM.</p></div>
				</div>

				<div class="route-intro">
					<p class="mono stempelfarbe">Der passende nächste Owner</p>
					<h3>Die Agentur-Seite entscheidet. Die Produktseiten konkretisieren.</h3>
				</div>

				<nav class="route-grid" aria-label="Passende Leistungen">
					<a href="<?php echo esc_url( $website_url ); ?>" data-track-action="agentur_to_website_offer" data-track-category="internal_link">
						<span>Website</span>
						<strong>WordPress-Website erstellen lassen</strong>
						<small>Neubau oder Relaunch, Umfang und Preis vor dem Start klären.</small>
					</a>
					<a href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="agentur_to_tracking_offer" data-track-category="internal_link">
						<span>Messung</span>
						<strong>Conversion Tracking einrichten lassen</strong>
						<small>Wenn Anfragen kommen, aber Herkunft und Qualität nicht sauber messbar sind.</small>
					</a>
					<a href="<?php echo esc_url( $conversion_url ); ?>" data-track-action="agentur_to_conversion_offer" data-track-category="internal_link">
						<span>Wirkung</span>
						<strong>Conversion-Optimierung für B2B</strong>
						<small>Wenn bereits Besucher da sind, aber zu wenige passende Anfragen entstehen.</small>
					</a>
				</nav>

				<div class="ausgang">
					<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_collaboration_project" data-track-category="lead_gen">Projekt einordnen <span class="pf" aria-hidden="true">→</span></a>
				</div>
			</div>

			<aside class="marg">
				<p class="note"><span class="label">Nicht passend</span>Große E-Commerce-Plattformen, reine Design-Relaunches ohne technischen Zweck, permanente 24/7-Bereitschaft oder Projekte, die gleichzeitig mehrere volle Spezialistenteams benötigen.</p>
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
				<h2 class="kopf">WordPress in Hannover: persönlich erreichbar, digital ohne Reibungsverlust.</h2>
				<p class="vorspann">Mein Sitz ist in Pattensen bei Hannover. Persönliche Reviews und Workshops sind in der Region nach Vereinbarung möglich; Entwicklung, QA, Tracking und laufende Abstimmung funktionieren ebenso remote im gesamten DACH-Raum.</p>

				<dl class="lokal-zeile">
					<div><dt>Sitz</dt><dd>Pattensen bei Hannover</dd></div>
					<div><dt>Persönlich</dt><dd>Region Hannover und Niedersachsen nach Vereinbarung</dd></div>
					<div><dt>Umsetzung</dt><dd>remote in Deutschland, Österreich und der Schweiz</dd></div>
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
							<h2>Nicht die größte Struktur gewinnt. Die passende.</h2>
							<p class="aufriss">Beschreiben Sie kurz Website, Ziel und aktuellen Engpass. Ich ordne ein, ob direkte Zusammenarbeit sinnvoll ist – und sage ebenso klar, wenn Ihr Projekt mit einem größeren Team besser aufgehoben ist.</p>
							<div class="ausgang">
								<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="cta_agentur_final_project" data-track-category="lead_gen">Projekt einordnen <span class="pf" aria-hidden="true">→</span></a>
							</div>
						</div>
						<aside class="marg">
							<p class="note"><span class="label">Ausgang</span>Direkter Scope, Verweis auf die passende Produktseite oder ein klares Nein. Kein Pflicht-Relaunch und kein Standard-Pitch.</p>
						</aside>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
