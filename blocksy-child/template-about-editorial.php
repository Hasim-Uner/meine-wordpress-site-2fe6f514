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
$request_cta  = function_exists( 'nexus_get_primary_request_cta_label' ) ? nexus_get_primary_request_cta_label() : 'Marktcheck mit Fit-Entscheid starten';
$portrait_url = function_exists( 'hu_get_profile_image_url' ) ? hu_get_profile_image_url() : get_stylesheet_directory_uri() . '/assets/img/hasim-portrait.png';
$portrait_path = get_stylesheet_directory() . '/assets/img/hasim-portrait.png';

// Biografischer Pfad — von Herkunft zur Methode.
$about_hero_path = [
	[
		't' => 'Die Vertriebs-Schule',
		's' => 'Aufgewachsen im – bis heute aktiven – Bauunternehmen des Vaters. Danach acht Jahre B2B-Beratung am echten Entscheider: Bedarf erkennen, Einwände auflösen, verbindlich abschließen. Gelernt im Gespräch, nicht im Seminar.',
	],
	[
		't' => 'Der analytische Blick',
		's' => 'Medienwissenschaft als Werkzeugkasten, nicht als Elfenbeinturm. Ich sehe, wo Daten fließen, wo Aufmerksamkeit im Funnel versickert und welches Signal über Vertrauen oder Wegklicken entscheidet.',
	],
	[
		't' => 'Der eigene Beweis',
		's' => 'Eigenen Shop von Null aufgebaut und am eigenen Geld gespürt, was Plattform-Abhängigkeit kostet. Dieselbe Logik auf Energie skaliert — und bei E3 New Energy bewiesen.',
	],
];

// Die drei Grundüberzeugungen (Manifest).
$about_work_principles = [
	[
		'eyebrow' => 'Überzeugung I',
		'title'   => 'Code muss verkaufen, nicht informieren.',
		'body'    => 'Die meisten Websites sind digitale Hochglanz-Prospekte. Wer selbst aus dem Vertrieb kommt, weiß: Eine B2B-Website hat eine Aufgabe — Abschlüsse vorzubereiten. Ein eigenes Anfrage-System ist kein Informationsfriedhof, sondern ein scharf kalkulierter Filter, der unqualifizierte Anfragen abweist und kaufbereite Entscheider isoliert.',
		'detail'  => 'Wer acht Jahre im B2B-Vertrieb stand, weiß: Wenn die technische Struktur die Sprache des Vertriebs nicht spricht, ist sie wertlos.',
	],
	[
		'eyebrow' => 'Überzeugung II',
		'title'   => 'Mieten ist teuer. Eigentum ist planbar.',
		'body'    => 'Im Bau- und Energiesektor gilt: Wer Maschinen, Hallen und Grundstücke nur mietet, macht sich erpressbar. Beim wichtigsten Gut — den Kundenanfragen — tun viele Betriebe genau das. Wer Leads bei Portalen kauft, füttert fremde Plattformen, teilt sich Kontakte mit Mitbewerbern und steht unter dauerhaftem Margendruck.',
		'detail'  => 'Ich habe das nicht nur beobachtet — ich habe meinen eigenen Shop aufgebaut und am eigenen Umsatz gespürt, was es heißt, die eigene Nachfrage zu besitzen statt zu mieten. Eigentum schlägt Miete. Auch im Vertrieb.',
	],
	[
		'eyebrow' => 'Überzeugung III',
		'title'   => 'Daten-Integrität schlägt Bauchgefühl.',
		'body'    => 'Wenn Tracking nur Klicks statt unterschriebene Werkverträge misst, verbrennt Werbebudget auf den falschen Kanälen. Erst wenn serverseitiges Tracking und CRM nahtlos verschmelzen, sehen die Werbekanäle, wo der echte Profit liegt — und optimieren auf Umsatz statt auf Klicks, die niemand bezahlt hat.',
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
				<h1 class="about-editorial__h1">Ich habe am Küchentisch gelernt, was eine schlechte Anfrage kostet.</h1>
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
							<dd>Anfrage-Kraftwerk</dd>
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
					<p class="about-editorial__dropcap">Mein Vater ist Bauunternehmer. Ich bin mit dem Wissen aufgewachsen, was es heißt, Verantwortung für Projekte, Margen und ein fest angestelltes Team zu tragen — und trage es bis heute mit, wenn am Küchentisch über Auftragslage und Zahlungsmoral geredet wird.</p>
					<p>Vertrieb habe ich danach nicht in Seminaren gelernt, sondern in acht Jahren B2B-Beratung: Bedarf strukturiert aufnehmen, mit Entscheidern verbindlich kommunizieren, langfristige Beziehungen über zuverlässige Follow-ups halten.</p>
					<p>Dann habe ich selbst gegründet — einen eigenen Online-Shop, von der ersten Zeile Code bis zur letzten Conversion. Dort habe ich am eigenen Geld erlebt, was es bedeutet, seine Nachfrage selbst zu erzeugen, statt sie von Plattformen zu mieten. Genau diese Erfahrung ist der Kern dessen, was ich heute baue.</p>
					<p>Mein Studium der Medienwissenschaft an der Universität Paderborn war dabei der analytische Werkzeugkasten: Ich schaue nicht darauf, was auf einer Website schön aussieht, sondern wie Daten fließen, wo Aufmerksamkeit versickert und welche Signale zwischen Oberfläche und B2B-Entscheider übertragen werden müssen, damit Vertrauen entsteht.</p>
					<p>Die meisten WordPress-Websites scheitern, weil sie von Designern gebaut wurden, die nie ein echtes Verkaufsgespräch geführt haben. Sie informieren den Nutzer zu Tode, statt Entscheidungen zu provozieren. Ich verbinde acht Jahre Vertriebs-Pragmatismus mit analytischer Präzision — und seit dem Case bei E3 New Energy (−83 % Cost-per-Lead, über 1.750 qualifizierte Anfragen in neun Monaten) wende ich diese Methode exklusiv auf den Solar- und Wärmepumpen-Mittelstand an.</p>
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
					<span>Händischer Befund innerhalb von 2 Werktagen</span>
				</p>
			</footer>

		</div>
	</div>
</main>

<?php
get_footer();
