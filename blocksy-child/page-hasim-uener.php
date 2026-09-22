<?php
/**
 * Personal methodology page at /hasim-uener/.
 *
 * Die Seite erzaehlt eine Sache: Die Website fuehrt Gespraeche, bei denen der
 * Betreiber nicht dabei ist — und sie koennte zuhoeren, wenn man sie laesst.
 * 01–03 erzaehlen das, 04 ist die Methode dazu, 05–08 Wandel, Haltung,
 * Zusammenarbeit und Kurzbiografie. Copy: docs/briefings/ueber-mich/copy.md.
 *
 * Die oeffentlichen Anker (#arbeitsweise, #schwaechstes-glied,
 * #defekt-oder-stellschraube, #werkzeuge, #gestaltung, #ueberzeugen,
 * #zugaenge, #hintergrund) bleiben vergeben wie bisher, damit bestehende
 * Sprungziele eine Textaenderung ueberleben. Kennzahlen und Antwortzeit
 * kommen aus dem Kanon, nicht aus dem Template.
 *
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes         = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$request_url    = $routes['project_request'] ?? home_url( '/kontakt/?type=project&focus=implementation_scope' );
$freelancer_url = $routes['freelancer'] ?? home_url( '/' );
$whitelabel_url = $routes['whitelabel'] ?? home_url( '/whitelabel-retainer/' );
$results_url    = $routes['results'] ?? home_url( '/ergebnisse/' );
$mail_address   = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$response       = hu_response_promise( 'phrase' );
// Visible rel=me links corroborate the existing canonical Person.sameAs graph.
$linkedin_url   = 'https://www.linkedin.com/in/hasim-uener/';
$github_url     = 'https://github.com/Hasim-hannover';
$blog_url       = 'https://hasimuener.org/';
$repo_url       = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$portrait_url   = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait-400x533.webp';
$portrait_srcset = sprintf(
	'%1$s/assets/img/hasim-portrait-400x533.webp 400w, %1$s/assets/img/hasim-portrait-800x1067.webp 800w',
	get_stylesheet_directory_uri()
);
$e3_canon     = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url  = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_metric    = static function ( $key, $field = 'display' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field ) : '';
};
$public_reference = null;
if ( function_exists( 'hu_public_reference_projects' ) ) {
	foreach ( hu_public_reference_projects() as $reference ) {
		if ( 'civaka-azad.org' === $reference['name'] ) {
			$public_reference = $reference;
			break;
		}
	}
}
get_header();
?>

<div class="doku hu-about" id="about-content">
	<header class="blatt kopfteil">
		<div class="about-hero">
			<div class="about-hero-copy">
				<p class="gegenstand">Über Haşim Üner · WordPress-Freelancer</p>
				<h1>Ihre Website führt Gespräche, bei denen Sie nicht dabei sind.</h1>
				<p class="aufriss">Ich baue Websites, die diese Gespräche gut führen. Und die Ihnen erzählen, wie sie ausgegangen sind.</p>
				<a class="textlink about-start" href="#arbeitsweise" data-track-action="about_read_method" data-track-category="navigation" data-track-section="about_hero">Wie ich arbeite <span aria-hidden="true">↓</span></a>
			</div>
			<figure class="about-portrait">
				<img src="<?php echo esc_url( $portrait_url ); ?>" srcset="<?php echo esc_attr( $portrait_srcset ); ?>" sizes="(max-width: 640px) 38vw, (max-width: 960px) 30vw, 320px" width="400" height="533" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" fetchpriority="high" decoding="async">
				<figcaption><strong>Haşim Üner</strong><span>Pattensen bei Hannover.<br>Zusammenarbeit im DACH-Raum.</span></figcaption>
			</figure>
		</div>
		<nav class="about-index" aria-label="Auf dieser Seite">
			<a href="#geschichte">Geschichte</a>
			<a href="#arbeitsweise">Methode</a>
			<a href="#werkzeuge">Wandel</a>
			<a href="#gestaltung">Haltung</a>
			<a href="#zugaenge">Zusammenarbeit</a>
			<a href="#hintergrund">Kurz zu mir</a>
		</nav>
	</header>

	<section id="geschichte" aria-labelledby="geschichte-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">01</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll about-copy">
				<h2 class="kopf" id="geschichte-h">Man kann nicht nicht kommunizieren.</h2>
				<p>Der Satz stammt vom Kommunikationswissenschaftler Paul Watzlawick. Auf einer Website gilt er für jedes Detail. Eine Seite, die lange lädt, sagt: Wir haben keine Eile. Ein Formular mit vierzehn Feldern sagt: Ihre Zeit ist uns egal. Ein Blog, dessen letzter Beitrag drei Jahre alt ist, sagt: Hier passiert nichts mehr. Geschrieben hat das niemand. Gelesen wird es trotzdem.</p>
			</div>
		</div>
	</section>

	<section id="erstes-gespraech" aria-labelledby="erstes-gespraech-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">02</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll about-copy">
				<h2 class="kopf" id="erstes-gespraech-h">Das erste Gespräch führt Ihre Website allein.</h2>
				<p>Im B2B ruft selten jemand einfach an. Vorher wird gelesen, verglichen und weitergeleitet, oft abends und oft von mehreren Leuten. In dieser Zeit entscheidet sich, ob Sie überhaupt auf die Liste kommen. Von denen, die sich melden, erfahren Sie. Von allen anderen hören Sie nie.</p>
			</div>
		</div>
	</section>

	<section id="zuhoeren" aria-labelledby="zuhoeren-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">03</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll about-copy">
				<h2 class="kopf" id="zuhoeren-h">Zuhören kann sie auch. Wenn man sie lässt.</h2>
				<p>Jede Anfrage ist eine Nachricht vom Markt: wer sucht, mit welchem Problem und über welchen Weg. Auf den meisten Websites landet sie als lose E-Mail im Postfach. Woher sie kam, wie gut sie war und was aus ihr wurde, weiß ein halbes Jahr später niemand mehr.</p>
				<p>Deshalb gehört für mich beides zusammen: was eine Website sagt und was sie erfährt. Das eine ist Kommunikation, das andere Organisation. Die meisten Websites können nur das Erste.</p>
			</div>
		</div>
	</section>

	<section id="arbeitsweise" aria-labelledby="arbeitsweise-h" data-track-section="about_method">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">04</span><span class="titel">Methode</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<div class="about-copy">
					<h2 class="kopf" id="arbeitsweise-h">Ich fange mit Zuhören an.</h2>
					<p id="schwaechstes-glied">Bevor ich etwas baue, lese ich Ihre Website so, wie ein Kunde sie liest: auf dem Handy, abends, mit einer konkreten Frage im Kopf. Ich suche die Stelle, an der das Gespräch abbricht. Findet man Sie nicht? Versteht man nicht, was Sie anbieten? Oder bleibt die Anfrage irgendwo zwischen Formular und Vertrieb liegen? Dort fange ich an. Solange diese Stelle offen ist, bringt jede andere Verbesserung wenig.</p>
					<p>Danach klären wir, womit wir es zu tun haben.</p>
				</div>
				<div class="about-decisions" id="defekt-oder-stellschraube">
					<article class="about-decision">
						<div><h3>Ein Fehler.</h3></div>
						<div><p>Das Formular verschickt nichts. Eine wichtige Seite ist für Google gesperrt. Eine Anfrage wird nicht gezählt. Das wird repariert und geprüft. Darüber muss niemand diskutieren.</p></div>
					</article>
					<article class="about-decision">
						<div><h3>Eine Entscheidung.</h3></div>
						<div><p>Für einen Anfrageweg oder eine Seitenstruktur gibt es mehrere gute Lösungen. Ich wäge ab, was Ihre Besucher brauchen und was Ihr Team pflegen kann. Dann sage ich Ihnen, wofür ich mich entscheiden würde und warum.</p></div>
					</article>
					<article class="about-decision">
						<div><h3>Eine Annahme.</h3></div>
						<div><p>Ob eine andere Überschrift mehr Anfragen bringt, weiß vorher niemand. Wir legen fest, woran wir es messen, und schauen nach. Bei wenig Besuchern zählen Gespräche und die Qualität der Anfragen mehr als jeder A/B-Test.</p></div>
					</article>
				</div>
				<p class="about-thesis">Viele Projekte verlieren Zeit, weil man diese drei verwechselt.</p>
				<aside class="about-beleg" aria-label="Beleg">
					<p class="mono">Beleg</p>
					<p>So haben wir bei einem mittelständischen PV-Installationsbetrieb gearbeitet. Die Kosten pro qualifizierter Anfrage sanken in <?php echo esc_html( $e3_metric( 'timeframe', 'display_dative' ) ); ?> von <?php echo esc_html( $e3_metric( 'cpl_before' ) ); ?> auf <?php echo esc_html( $e3_metric( 'cpl_after' ) ); ?>.</p>
					<p class="about-beleg-link"><a class="textlink" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="about_station_solar_case" data-track-category="trust" data-track-section="about_method">Wie das im Einzelnen lief <span aria-hidden="true">→</span></a> <em>(Fallstudie)</em></p>
				</aside>
			</div>
		</div>
	</section>

	<section id="werkzeuge" aria-labelledby="werkzeuge-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">05</span><span class="titel">Wandel</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll about-copy">
				<h2 class="kopf" id="werkzeuge-h">Das Gespräch beginnt immer öfter woanders.</h2>
				<p>Suchmaschinen und KI-Assistenten beantworten Fragen inzwischen selbst und nennen dabei Anbieter. Oder eben nicht. Wer dort vorkommen will, braucht eine klare Aussage, eine saubere Struktur und Inhalte, auf die man sich berufen kann.</p>
				<p>Die Werkzeuge ändern sich schnell. Die Grundregel bleibt: Wer klar spricht, wird verstanden, von Menschen und von Maschinen. KI nutze ich selbst, wo sie die Arbeit besser macht. Die Verantwortung für das Ergebnis bleibt bei mir.</p>
			</div>
		</div>
	</section>

	<section id="gestaltung" aria-labelledby="ueberzeugen-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">06</span><span class="titel">Haltung</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<div class="about-copy about-principle" id="ueberzeugen">
					<h3 id="ueberzeugen-h">Überzeugen: ja. Täuschen: nein.</h3>
					<p>Keine erfundene Knappheit, keine versteckten Kosten, keine künstlichen Hürden. Menschen sollen selbst beurteilen können, ob ein Angebot zu ihnen passt. Ein gutes Gespräch braucht keine Tricks.</p>
				</div>
				<?php if ( $public_reference ) : ?>
					<aside class="about-reference" aria-labelledby="referenz-h"><p class="mono">Öffentliche Arbeit · <?php echo esc_html( $public_reference['discipline'] ); ?></p><h3 id="referenz-h"><?php echo esc_html( $public_reference['name'] ); ?></h3><p><?php echo esc_html( $public_reference['text'] ); ?></p><a class="textlink" href="<?php echo esc_url( $public_reference['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="about_reference_open" data-track-category="trust" data-track-section="about_design">Website ansehen ↗</a></aside>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section id="zugaenge" aria-labelledby="zugaenge-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">07</span><span class="titel">Zusammenarbeit</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll about-copy">
				<h2 class="kopf" id="zugaenge-h">Bei mir gibt es keine stille Post.</h2>
				<p>Sie sprechen mit dem, der plant, baut und prüft. Keine Übergabe an ein Team, das den Anfang nicht kennt.</p>
				<p>Und falls Sie sich fragen, was passiert, wenn ich ausfalle: Domain, Konten und Daten liegen von Anfang an bei Ihnen. Code und Entscheidungen sind so dokumentiert, dass ein anderer Entwickler weiterarbeiten kann. Wie das aussieht, sehen Sie an dieser Website: Ihr Code und jede Änderung sind öffentlich. <a class="satzlink" href="<?php echo esc_url( $repo_url . '/commits/main/' ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="about_code_history" data-track-category="trust" data-track-section="about_handover">Änderungen ansehen ↗</a></p>
			</div>
		</div>
	</section>

	<section id="hintergrund" aria-label="Kurz zu mir">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">08</span><span class="titel">Kurz zu mir</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll about-copy">
				<p>Medienwissenschaften in Paderborn, Abschluss 2013. Meine Bachelorarbeit handelte von persuasiver Werbung, und das Ergebnis war: Sie nervt vor allem. Später kamen B2B-Vertrieb und ein eigener Onlineshop, bei dem ich die ganze Strecke selbst verantwortet habe. Ich komme aus einem Unternehmerhaushalt und arbeite heute als WordPress-Freelancer in Pattensen bei Hannover.</p>
			</div>
		</div>
	</section>

	<div class="abschluss" id="zusammenarbeit" data-track-section="about_cta">
		<div class="blatt">
			<div class="tafel about-close">
				<div><p class="mono">Zusammenarbeit</p><h2 id="hu-about-cta-title">Welches Gespräch führt Ihre Website gerade?</h2><p class="aufriss">Beschreiben Sie kurz Ihre Ausgangslage und was besser werden soll. Ich sage Ihnen, wo ich ansetzen würde und was wir für ein Angebot noch klären müssen.</p><div class="ausgang"><a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_about_project" data-track-category="lead_gen" data-track-section="about_cta">Projekt beschreiben <span aria-hidden="true">→</span></a></div><p class="about-reply"><?php echo esc_html( $response ); ?>, von mir persönlich.</p><p class="about-exits"><a class="satzlink" href="<?php echo esc_url( $freelancer_url . '#angebote' ); ?>" data-track-action="link_about_freelancer" data-track-category="internal_link" data-track-section="about_cta">Leistungen und Preise ansehen</a> <span aria-hidden="true">·</span> <a class="satzlink" href="<?php echo esc_url( $results_url ); ?>" data-track-action="about_view_results" data-track-category="trust" data-track-section="about_cta">Arbeiten und Ergebnisse</a></p></div>
				<aside class="about-agency"><p class="mono">Für Agenturen</p><h3>Ihre Kundenführung. Meine Umsetzung.</h3><p>WordPress, Tracking und technische Weiterentwicklung unter Ihrem Namen. Umfang und Preis vereinbaren wir vor dem Erstprojekt.</p><a class="textlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="link_about_whitelabel" data-track-category="lead_gen" data-track-section="about_cta">White-Label ansehen →</a></aside>
			</div>
			<div class="about-personal">
				<p>Abseits der Kundenarbeit schreibe ich auf <a class="satzlink" href="<?php echo esc_url( $blog_url ); ?>" rel="me noopener noreferrer" target="_blank" data-track-action="link_about_blog" data-track-category="navigation" data-track-section="about_coda">hasimuener.org</a> über Medien und Öffentlichkeit.</p>
				<ul role="list"><li><a class="satzlink" href="<?php echo esc_url( $linkedin_url ); ?>" rel="me noopener noreferrer" target="_blank" data-track-action="link_about_linkedin" data-track-category="navigation" data-track-section="about_cta">LinkedIn ↗</a></li><li><a class="satzlink" href="<?php echo esc_url( $github_url ); ?>" rel="me noopener noreferrer" target="_blank" data-track-action="link_about_github" data-track-category="navigation" data-track-section="about_cta">GitHub ↗</a></li><li><a class="satzlink" href="<?php echo esc_url( 'mailto:' . $mail_address, [ 'mailto' ] ); ?>" data-track-action="link_about_mail" data-track-category="navigation" data-track-section="about_cta">E-Mail</a></li></ul>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>
