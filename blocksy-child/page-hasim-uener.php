<?php
/**
 * Personal methodology page at /hasim-uener/.
 *
 * The homepage owns the Freelancer offer. This page explains the person,
 * decision process and evidence behind it, using the shared document system.
 * Existing profile identities, request routes and public anchors remain intact.
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
$e3_metric    = static function ( $key, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field, $fallback ) : $fallback;
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
				<p class="gegenstand">Über Haşim Üner · WordPress Freelancer</p>
				<h1>Eine Website kommuniziert mit ihrer ganzen Konstruktion.</h1>
				<p class="aufriss">Was Menschen finden, verstehen und als Nächstes tun, entsteht aus vielen Entscheidungen. Ich verbinde WordPress-Entwicklung, SEO, Gestaltung und Tracking, damit diese Entscheidungen zusammenpassen.</p>
				<a class="textlink about-start" href="#arbeitsweise" data-track-action="about_read_method" data-track-category="navigation" data-track-section="about_hero">So arbeite ich <span aria-hidden="true">↓</span></a>
			</div>
			<figure class="about-portrait">
				<img src="<?php echo esc_url( $portrait_url ); ?>" srcset="<?php echo esc_attr( $portrait_srcset ); ?>" sizes="(max-width: 640px) 38vw, (max-width: 960px) 30vw, 320px" width="400" height="533" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" fetchpriority="high" decoding="async">
				<figcaption><strong>Haşim Üner</strong><span>Pattensen bei Hannover.<br>Direkte Zusammenarbeit im DACH-Raum.</span></figcaption>
			</figure>
		</div>
		<nav class="about-index" aria-label="Auf dieser Seite">
			<a href="#hintergrund">01 Hintergrund</a>
			<a href="#arbeitsweise">02 Entscheidungen</a>
			<a href="#besucherwege">03 Orientierung</a>
			<a href="#gestaltung">04 Gestaltung</a>
			<a href="#zugaenge">05 Weiterentwicklung</a>
		</nav>
	</header>

	<section id="hintergrund" aria-labelledby="hintergrund-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">01</span><span class="titel">Hintergrund</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll about-copy">
				<h2 class="kopf" id="hintergrund-h">Mich interessiert Kommunikation schon länger als Websites.</h2>
				<p>Ich habe Medienwissenschaften studiert. Medientheorie, Medienpsychologie und Werbung zeigten mir unterschiedliche Perspektiven auf dieselbe Frage: Wie entsteht Wirkung? Wer spricht, in welchem Kontext und über welches Medium, verändert, was beim Gegenüber ankommt.</p>
				<p>Heute begegnet mir diese Frage bei Websites ganz konkret. Die Navigation zeigt, was wichtig ist. Ein Formular bestimmt, welche Angaben wir erwarten und wie viel Aufwand eine Anfrage bedeutet. Geschwindigkeit und Gestaltung beeinflussen, wie selbstverständlich sich der Weg anfühlt.</p>
				<p>Mein Vater war Bauunternehmer. Vielleicht kommt daher mein handwerklicher Blick: Was wir bauen, muss stehen, funktionieren und seiner Aufgabe gerecht werden. Bei einer Website besteht diese Konstruktion aus Code, Struktur, Sprache und Gestaltung.</p>
				<p>Die wirtschaftliche Perspektive kenne ich aus dem B2B-Vertrieb und aus Werbung für eigene Projekte. Als jeder Klick mein eigenes Geld kostete, wurde die Frage drängender: Welche Besucher werden zu passenden Anfragen – und können wir nachvollziehen, warum?</p>
				<p class="about-thesis">Der Klick ist der Anfang einer Strecke. Ob sie funktioniert, entscheidet sich danach.</p>
			</div>
		</div>
	</section>

	<section id="arbeitsweise" aria-labelledby="arbeitsweise-h" data-track-section="about_method">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">02</span><span class="titel">Entscheidungen</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<div class="about-copy">
					<h2 class="kopf" id="arbeitsweise-h">Wo verliert Ihre Website gerade Wirkung?</h2>
					<p id="schwaechstes-glied">Vielleicht fehlt Sichtbarkeit. Vielleicht kommen Besucher, verstehen aber das Angebot nicht. Oder es entstehen Anfragen, deren Herkunft und Qualität niemand einordnen kann. Ich prüfe zuerst, welcher Engpass die nächste Änderung rechtfertigt.</p>
					<p>Das gibt Ihrem Projekt eine begründete Priorität. Wie wir dann vorgehen, hängt davon ab, welche Art von Entscheidung vor uns liegt.</p>
				</div>
				<div class="about-decisions" id="defekt-oder-stellschraube">
					<article class="about-decision">
						<div><p class="mono">01 · Reparieren</p><h3>Defekt.</h3></div>
						<div><p>Ein Formular überträgt keine Daten. Eine Seite, die gefunden werden soll, ist versehentlich von der Indexierung ausgeschlossen. Ein vereinbartes Event wird trotz erfüllter Auslösebedingungen nicht erfasst.</p><p class="about-verification"><strong>Die Aufgabe:</strong> Fehler eingrenzen, beheben und die Funktion erneut prüfen.</p></div>
					</article>
					<article class="about-decision">
						<div><p class="mono">02 · Entwickeln</p><h3>Gestaltung.</h3></div>
						<div><p>Für eine Seitenstruktur, einen Anfrageprozess oder eine technische Integration gibt es mehrere vernünftige Lösungen. Ich wäge sie anhand von Nutzerbedarf, Wartbarkeit und Ihrem verfügbaren Rahmen ab.</p><p class="about-verification"><strong>Die Aufgabe:</strong> Eine Lösung bewusst wählen, umsetzen und ihre Anforderungen prüfen.</p></div>
					</article>
					<article class="about-decision">
						<div><p class="mono">03 · Überprüfen</p><h3>Hypothese.</h3></div>
						<div><p>Ob eine andere Headline stärker überzeugt oder ein zusätzliches Formularfeld sinnvoll qualifiziert, wissen wir vorher nicht sicher. Daten, Forschung und Erfahrung begründen eine Annahme. Wir legen fest, woran wir ihre Wirkung beurteilen.</p><p class="about-verification"><strong>Die Aufgabe:</strong> Die Annahme überprüfen. Ein A/B-Test braucht dafür ausreichend geeignete Daten; bei wenig Traffic helfen zunächst Nutzergespräche, Beobachtungen und die Qualität eingehender Anfragen.</p></div>
					</article>
				</div>
				<aside class="tafel about-evidence" aria-labelledby="beleg-h">
					<div><p class="mono">Dokumentierter Projektfall · Solar</p><h3 id="beleg-h">Von der Anfrage bis zur Übergabe mitgedacht.</h3><p>Bei einem mittelständischen PV-Installationsbetrieb wurden Angaben vor dem Erstkontakt strukturiert erfasst und im CRM priorisiert. Landingpages, Vorqualifizierung, Tracking und Vertriebsübergabe wurden gemeinsam weiterentwickelt.</p></div>
					<div class="about-evidence-result"><p class="about-number"><?php echo esc_html( $e3_metric( 'cpl_before', 'display', '150 €' ) ); ?> → <?php echo esc_html( $e3_metric( 'cpl_after', 'display', '22 €' ) ); ?></p><p>Kosten pro qualifizierter Anfrage im dokumentierten Zeitraum von <?php echo esc_html( $e3_metric( 'timeframe', 'display_dative', '6 Monaten' ) ); ?>.</p></div>
					<div class="about-evidence-source"><p>Das Ergebnis beschreibt das Gesamtsystem einschließlich Marketing und Vertrieb. Daraus lässt sich keine isolierte Wirkung eines einzelnen Bausteins oder eine Prognose für andere Projekte ableiten.</p><a class="textlink" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="about_station_solar_case" data-track-category="trust" data-track-section="about_method">Ausgangslage und Arbeitsschritte ansehen →</a></div>
				</aside>
			</div>
		</div>
	</section>

	<section id="besucherwege" aria-labelledby="besucherwege-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">03</span><span class="titel">Orientierung</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<div class="about-copy"><h2 class="kopf" id="besucherwege-h">Menschen kommen mit unterschiedlichen Fragen.</h2><p>Wer einen Anbieter sucht, braucht andere Informationen als jemand, der gerade erst sein Problem versteht. Eine B2B-Website sollte beide weiterbringen. Ihr nächster sinnvoller Schritt kann ein Gespräch sein, aber ebenso eine Erklärung oder ein Vergleich.</p></div>
				<div class="fragen about-paths">
					<details open><summary>„Ich brauche einen Anbieter.“</summary><div class="huelle"><div><div class="antwort"><p>Leistungen, Referenzen und ein verständlicher Ablauf helfen, die Zusammenarbeit einzuschätzen. Der Weg zur konkreten Anfrage sollte direkt erreichbar sein.</p><a class="satzlink" href="<?php echo esc_url( $freelancer_url . '#angebote' ); ?>" data-track-action="link_about_freelancer" data-track-category="internal_link" data-track-section="about_paths">Meine Leistungen und Preisrahmen</a></div></div></div></details>
					<details><summary>„Ich brauche eine Lösung.“</summary><div class="huelle"><div><div class="antwort"><p>Das Problem ist klar, der Weg noch offen. Ein nachvollziehbarer Projektfall zeigt, welche Entscheidungen möglich sind und unter welchen Bedingungen ein Ansatz funktioniert.</p><a class="satzlink" href="<?php echo esc_url( $results_url ); ?>" data-track-action="about_view_results" data-track-category="trust" data-track-section="about_paths">Arbeiten und Ergebnisse einordnen</a></div></div></div></details>
					<details><summary>„Ich möchte das Problem erst verstehen.“</summary><div class="huelle"><div><div class="antwort"><p>Eine konkrete Erklärung, ein Vergleich oder ein Werkzeug kann jetzt hilfreicher sein als eine Projektanfrage. Deshalb verbinde ich Fachinhalte mit passenden Vertiefungen.</p><a class="satzlink" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" data-track-action="about_read_expertise" data-track-category="navigation" data-track-section="about_paths">Fachartikel lesen</a></div></div></div></details>
				</div>
				<p class="about-afterword">So wird eine Website langfristig zur Plattform: Angebote, Fachwissen, Fälle und Werkzeuge gehören zusammen. Menschen können finden, verstehen, vertiefen, wiederkommen und entscheiden. Vertrauen darf dabei über mehrere Besuche entstehen.</p>
			</div>
		</div>
	</section>

	<section id="gestaltung" aria-labelledby="gestaltung-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">04</span><span class="titel">Gestaltung</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<div class="about-copy">
					<h2 class="kopf" id="gestaltung-h">Gute Gestaltung macht Komplexität selbstverständlich.</h2>
					<p>Auch technische Entwicklung ist für mich kreative Arbeit: Welche Informationen gehören zusammen? Wie lösen wir einen Prozess? Wie funktioniert dieselbe Aufgabe auf einem kleinen Bildschirm oder mit assistiven Technologien?</p>
					<p>Typografie, Sprache, Bewegung und Interaktion sollen diese Entscheidungen spürbar machen. Eine B2B-Website darf Freude machen, weil sie verständlich ist, sich gut bedienen lässt und auch im Detail durchdacht wirkt.</p>
					<p class="about-thesis">Sie sollte Menschen Gründe geben, freiwillig weiterzugehen.</p>
				</div>
				<?php if ( $public_reference ) : ?>
					<aside class="about-reference" aria-labelledby="referenz-h"><p class="mono">Öffentliche Arbeit · <?php echo esc_html( $public_reference['discipline'] ); ?></p><h3 id="referenz-h"><?php echo esc_html( $public_reference['name'] ); ?></h3><p><?php echo esc_html( $public_reference['text'] ); ?></p><a class="textlink" href="<?php echo esc_url( $public_reference['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="about_reference_open" data-track-category="trust" data-track-section="about_design">Website ansehen ↗</a></aside>
				<?php endif; ?>
				<div class="about-copy about-principle" id="ueberzeugen"><h3>Überzeugen: ja. Täuschen: nein.</h3><p>Menschen sollen beurteilen können, ob ein Angebot zu ihnen passt. Dafür brauchen sie verständliche Informationen, belegbare Aussagen und transparente Bedingungen. Diese Haltung beeinflusst auch die Gestaltung: keine erfundene Knappheit, keine versteckten Kosten, keine künstlichen Hürden.</p></div>
			</div>
		</div>
	</section>

	<section id="zugaenge" aria-labelledby="zugaenge-h">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">05</span><span class="titel">Weiterentwicklung</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll about-copy">
				<h2 class="kopf" id="zugaenge-h">Nach dem Launch muss die Arbeit weitergehen können.</h2>
				<p>Angebote verändern sich, Inhalte wachsen und Daten werfen neue Fragen auf. Deshalb gehören nachvollziehbarer Code, klare Strukturen und Dokumentation zur Umsetzung. Domains, Konten und Daten bleiben beim Kunden. Zugänge und technische Entscheidungen werden so übergeben, dass auch ein anderer Entwickler weiterarbeiten kann.</p>
				<p>Was das praktisch bedeutet, lässt sich an dieser Website sehen: Ihre Codebasis und Änderungshistorie sind öffentlich. <a class="satzlink" href="<?php echo esc_url( $repo_url . '/commits/main/' ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="about_code_history" data-track-category="trust" data-track-section="about_handover">Änderungen ansehen ↗</a></p>
				<p id="werkzeuge">Neue Werkzeuge, auch KI, können die Umsetzung unterstützen. Die Verantwortung für Auswahl, Prüfung und Übergabe bleibt bei mir.</p>
				<p class="about-thesis">Die Technik darf im Hintergrund bleiben. Für Besucher muss klar sein, was sie finden und wie es weitergeht. Für Ihr Unternehmen muss nachvollziehbar werden, ob die Website ihre Aufgabe erfüllt.</p>
			</div>
		</div>
	</section>

	<div class="abschluss" id="zusammenarbeit" data-track-section="about_cta">
		<div class="blatt">
			<div class="tafel about-close">
				<div><p class="mono">Zusammenarbeit</p><h2 id="hu-about-cta-title">Woran soll Ihre Website als Nächstes wachsen?</h2><p class="aufriss">Beschreiben Sie kurz die Ausgangslage und was sich verbessern soll. Ich ordne ein, wo ich ansetzen würde und was wir für ein konkretes Angebot noch klären müssen.</p><div class="ausgang"><a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_about_project" data-track-category="lead_gen" data-track-section="about_cta">Projekt beschreiben <span aria-hidden="true">→</span></a></div><p class="about-reply"><?php echo esc_html( $response ); ?></p></div>
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
