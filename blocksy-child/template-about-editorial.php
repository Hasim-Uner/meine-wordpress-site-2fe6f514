<?php
/**
 * Template Name: Über Mich (Editorial)
 * Description: Biografisch-editoriale Alternative zur Standard-Über-Mich-Seite.
 *              Fokus: Unternehmer-DNA, Mediensystem-Analyse, Drop-Cap-Erzählung.
 *
 * Parallel zu template-about.php. Auswahl pro Page im WP-Admin.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$request_url  = function_exists( 'nexus_get_primary_request_url' ) ? nexus_get_primary_request_url() : home_url( '/solar-waermepumpen-leadgenerierung/#marktcheck' );
$request_cta  = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Kostenfreien Marktcheck starten';
$portrait_url = function_exists( 'hu_get_profile_image_url' ) ? hu_get_profile_image_url() : get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';
$portrait_path = get_stylesheet_directory() . '/assets/img/hasim-portrait.png';

// Biografischer Pfad — von Herkunft zur Methode.
$about_hero_path = [
	[
		't' => 'Die Unternehmer-DNA',
		's' => 'Aufgewachsen im Bauunternehmen des Vaters. Vertrieb, Margendruck und das wirtschaftliche Risiko von Kindesbeinen an verstanden.',
	],
	[
		't' => 'Die medienwissenschaftliche Struktur',
		's' => 'Systeme dekonstruieren. Analysieren, wie digitale Zeichen, Signale und Kanäle Märkte und menschliche Entscheidungen steuern.',
	],
	[
		't' => 'Die Fusion im Anfrage-System',
		's' => 'Vertriebs-Verstand trifft präzise Daten-Integrität. Für Betriebe, die ihre eigene Nachfrage besitzen wollen, statt Leads zu mieten.',
	],
];

// Die drei Grundüberzeugungen (Manifest).
$about_work_principles = [
	[
		'eyebrow' => 'Überzeugung I',
		'title'   => 'Code muss verkaufen, nicht informieren.',
		'body'    => 'Die meisten Websites sind digitale Hochglanz-Prospekte. Wer selbst aus dem Vertrieb kommt, weiß: Eine B2B-Website hat eine Aufgabe — Abschlüsse vorzubereiten. Ein eigenes Anfrage-System ist kein Informationsfriedhof, sondern ein scharf kalkulierter Filter, der unqualifizierte Anfragen abweist und kaufbereite Entscheider isoliert.',
		'detail'  => 'Wenn die technische Struktur die Sprache des Vertriebs nicht spricht, ist sie wertlos.',
	],
	[
		'eyebrow' => 'Überzeugung II',
		'title'   => 'Mieten ist teuer. Eigentum ist planbar.',
		'body'    => 'Im Bau- und Energiesektor gilt: Wer Maschinen, Hallen und Grundstücke nur mietet, macht sich erpressbar. Beim wichtigsten Gut — den Kundenanfragen — tun viele Betriebe genau das. Wer Leads bei Portalen kauft, füttert fremde Plattformen, teilt sich Kontakte mit Mitbewerbern und steht unter dauerhaftem Margendruck.',
		'detail'  => 'Eigentum schlägt Miete. Auch im Vertrieb.',
	],
	[
		'eyebrow' => 'Überzeugung III',
		'title'   => 'Daten-Integrität schlägt Bauchgefühl.',
		'body'    => 'Wenn Tracking nur Klicks statt unterschriebene Werkverträge misst, verbrennt Werbebudget auf den falschen Kanälen. Erst wenn serverseitiges Tracking und CRM nahtlos verschmelzen, sehen die Werbekanäle, wo der echte Profit liegt — und optimieren auf Umsatz statt auf bunte Grafiken.',
		'detail'  => 'Saubere Datenarchitektur ist der einzige Hebel für planbare Skalierung.',
	],
];

get_header();
?>

<main id="main" class="site-main">
	<div class="about-editorial" data-track-section="about_editorial_page">
		<div class="about-editorial__inner">

			<!-- HERO -->
			<header class="about-editorial__hero">
				<span class="about-editorial__kicker">Inquiry Systems Architect</span>
				<h1 class="about-editorial__h1">Vertriebs-Pragmatismus trifft Mediensystem-Analyse.</h1>
				<p class="about-editorial__lead">
					Ich entwerfe keine austauschbaren Webseiten. Ich konstruiere autarke Anfrage-Systeme für den inhabergeführten Solar- und SHK-Mittelstand.
				</p>
			</header>

			<!-- BIOGRAFISCHER PFAD + PORTRAIT-CARD -->
			<section class="about-editorial__split">
				<div class="about-editorial__path-col">
					<h2 class="about-editorial__section-kicker">Evolution der Methode</h2>
					<ol class="about-editorial__path" role="list">
						<?php foreach ( $about_hero_path as $index => $step ) : ?>
							<li class="about-editorial__path-item">
								<span class="about-editorial__path-num" aria-hidden="true">0<?php echo (int) ( $index + 1 ); ?></span>
								<div class="about-editorial__path-body">
									<h3 class="about-editorial__path-title"><?php echo esc_html( $step['t'] ); ?></h3>
									<p class="about-editorial__path-text"><?php echo esc_html( $step['s'] ); ?></p>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>

				<aside class="about-editorial__portrait-card">
					<?php if ( file_exists( $portrait_path ) ) : ?>
						<img
							src="<?php echo esc_url( $portrait_url ); ?>"
							alt="Porträt von Haşim Üner"
							class="about-editorial__portrait"
							loading="lazy"
							width="340"
							height="420"
						/>
					<?php endif; ?>
					<dl class="about-editorial__meta">
						<div class="about-editorial__meta-row">
							<dt>Status</dt>
							<dd>Aktiv · Pattensen</dd>
						</div>
						<div class="about-editorial__meta-row">
							<dt>Fokus</dt>
							<dd>Solar &amp; SHK-Infrastruktur</dd>
						</div>
						<div class="about-editorial__meta-row">
							<dt>Methode</dt>
							<dd>Sovereign Demand System</dd>
						</div>
					</dl>
				</aside>
			</section>

			<!-- MANIFEST -->
			<section class="about-editorial__manifest" aria-labelledby="about-editorial-manifest-title">
				<h2 id="about-editorial-manifest-title" class="about-editorial__section-kicker about-editorial__section-kicker--center">
					Das Unternehmer-Manifest
				</h2>
				<div class="about-editorial__manifest-grid">
					<?php foreach ( $about_work_principles as $principle ) : ?>
						<article class="about-editorial__principle">
							<span class="about-editorial__principle-eyebrow"><?php echo esc_html( $principle['eyebrow'] ); ?></span>
							<h3 class="about-editorial__principle-title"><?php echo esc_html( $principle['title'] ); ?></h3>
							<p class="about-editorial__principle-body"><?php echo esc_html( $principle['body'] ); ?></p>
							<p class="about-editorial__principle-detail"><?php echo esc_html( $principle['detail'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</section>

			<!-- BIOGRAFISCHE ERZÄHLUNG -->
			<section class="about-editorial__bio" id="about-editorial-hintergrund">
				<h2 class="about-editorial__section-kicker">Der Hintergrund</h2>
				<div class="about-editorial__bio-prose">
					<p class="about-editorial__dropcap">Mein Vater war Bauunternehmer. Ich bin mit dem Wissen aufgewachsen, was es bedeutet, Verantwortung für Projekte, Margen und ein fest angestelltes Team zu tragen. Vertrieb und Unternehmertum wurden mir nicht in Seminaren beigebracht — sie sind meine DNA.</p>
					<p>Mein Studium der Medienwissenschaft an der Universität Paderborn war kein Elfenbeinturm, sondern ein logischer Werkzeugkasten. Ich habe gelernt, komplexe Informationssysteme radikal zu dekonstruieren. Ich schaue nicht darauf, was auf einer Website schön aussieht. Ich analysiere, wie Daten fließen, wo Aufmerksamkeit im Funnel versickert und welche unsichtbaren Signale zwischen einer digitalen Oberfläche und einem B2B-Entscheider übertragen werden müssen, damit echtes Vertrauen entsteht.</p>
					<p>Die meisten WordPress-Websites scheitern, weil sie von Designern gebaut wurden, die nie ein echtes Verkaufsgespräch geführt haben. Sie informieren den Nutzer zu Tode, statt Entscheidungen zu provozieren. Ich fusioniere den Vertriebs-Pragmatismus des Bauwesens mit der analytischen Präzision der Medienwissenschaft. Seit dem Case bei E3 New Energy wende ich diese Methode exklusiv auf den Solar- und Wärmepumpen-Mittelstand an.</p>
				</div>
			</section>

			<!-- CTA -->
			<footer class="about-editorial__cta-card">
				<span class="about-editorial__cta-eyebrow">Exklusiver Marktcheck</span>
				<h2 class="about-editorial__cta-title">Bereit für ein autarkes System?</h2>
				<p class="about-editorial__cta-text">
					Wir analysieren, wie viel Werbebudget aktuell in Portal-Lücken versickert und wie ein eigenes Anfrage-Kraftwerk für Ihren Betrieb aussehen muss.
				</p>
				<a
					href="<?php echo esc_url( $request_url ); ?>"
					class="about-editorial__cta-btn"
					data-track-action="cta_about_editorial_marktcheck"
					data-track-category="lead_gen"
					data-track-section="about_editorial_cta"
				>
					<?php echo esc_html( $request_cta ); ?>
				</a>
				<p class="about-editorial__cta-meta">
					<span>Exklusive Erst-Analyse</span>
					<span>Prüfung auf Regions-Verfügbarkeit</span>
					<span>Händischer Befund in 48 h</span>
				</p>
			</footer>

		</div>
	</div>
</main>

<?php
get_footer();
