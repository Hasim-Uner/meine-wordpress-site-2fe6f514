<?php
/**
 * Personal methodology page at /hasim-uener/.
 *
 * Die Seite erzaehlt eine Sache: Eine Website fuehrt Gespraeche, wenn der
 * Betreiber nicht dabei ist. Gestaltung, Tracking und Organisation bilden
 * deshalb eine gemeinsame Strecke. 01–03 erzaehlen die Kommunikationslogik,
 * 04 die Diagnosemethode, 05–08 Wandel, Haltung, Zusammenarbeit und Herkunft.
 *
 * Die oeffentlichen Anker (#arbeitsweise, #schwaechstes-glied,
 * #defekt-oder-stellschraube, #werkzeuge, #gestaltung, #ueberzeugen,
 * #zugaenge, #hintergrund) bleiben stabil. Kennzahlen und Antwortzeit kommen
 * aus dem Kanon, nicht aus dem Template.
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
$results_url    = $routes['results'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$mail_address   = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$response       = hu_response_promise( 'phrase' );

$linkedin_url = 'https://www.linkedin.com/in/hasim-uener/';
$github_url   = 'https://github.com/Hasim-hannover';
$blog_url     = 'https://hasimuener.org/';
$repo_url     = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';

$portrait_url = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait-office-440x550.webp';

$e3_canon    = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_metric   = static function ( $key, $field = 'display' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field ) : '';
};

get_header();
?>

<div class="doku hu-about" id="about-content">
	<header class="blatt kopfteil about-head">
		<div class="about-hero">
			<div class="about-hero-copy">
				<p class="gegenstand">Über Haşim Üner · WordPress-Entwickler</p>
				<h1>Ihre Website führt Gespräche, bei denen Sie nicht dabei sind.</h1>
				<p class="aufriss">Ich baue Websites, die diese Gespräche gut führen. Und die Ihnen erzählen, wie sie ausgegangen sind.</p>

				<div class="about-hero-actions">
					<a class="tun about-start" href="#arbeitsweise" data-track-action="about_read_method" data-track-category="navigation" data-track-section="about_hero">Wie ich arbeite <span aria-hidden="true">↓</span></a>
					<span class="about-hero-meta">WordPress · Tracking · Conversion</span>
				</div>

				<div class="about-loop" aria-hidden="true">
					<div class="about-loop-node">
						<span class="about-loop-kicker">01</span>
						<strong>Besucher</strong>
						<small>kommt mit einer Frage</small>
					</div>
					<div class="about-loop-connector"><span class="about-loop-pulse"></span></div>
					<div class="about-loop-node is-core">
						<span class="about-loop-kicker">02</span>
						<strong>Website</strong>
						<small>führt das Gespräch</small>
					</div>
					<div class="about-loop-connector"><span class="about-loop-pulse"></span></div>
					<div class="about-loop-node">
						<span class="about-loop-kicker">03</span>
						<strong>Anfrage</strong>
						<small>wird zum Signal</small>
					</div>
					<div class="about-loop-return">
						<span class="about-loop-return-line"></span>
						<span>Rückkanal · Quelle → Qualität → Ergebnis</span>
					</div>
				</div>
			</div>

			<figure class="about-portrait">
				<div class="about-portrait-frame">
					<img src="<?php echo esc_url( $portrait_url ); ?>" width="440" height="550" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" fetchpriority="high" decoding="async">
					<div class="about-portrait-signal" aria-hidden="true">
						<span class="about-portrait-dot"></span>
						<span>Website als System</span>
					</div>
					<div class="about-portrait-grid" aria-hidden="true"></div>
				</div>
				<figcaption>
					<strong>Haşim Üner</strong>
					<span>Pattensen bei Hannover · Zusammenarbeit im DACH-Raum</span>
				</figcaption>
			</figure>
		</div>

		<nav class="about-index" aria-label="Auf dieser Seite">
			<a href="#geschichte"><span>01–03</span>Geschichte</a>
			<a href="#arbeitsweise"><span>04</span>Methode</a>
			<a href="#werkzeuge"><span>05</span>Wandel</a>
			<a href="#gestaltung"><span>06</span>Haltung</a>
			<a href="#zugaenge"><span>07</span>Zusammenarbeit</a>
			<a href="#hintergrund"><span>08</span>Kurz zu mir</a>
		</nav>
	</header>

	<div class="about-story" data-about-story>
		<div class="about-story-rail" aria-hidden="true">
			<span class="about-story-track"></span>
			<span class="about-story-progress" data-about-progress></span>
		</div>

		<section id="geschichte" class="about-step about-step-story" aria-labelledby="geschichte-h" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">01</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll about-story-grid">
					<div class="about-copy">
						<h2 class="kopf" id="geschichte-h">Man kann nicht nicht kommunizieren.</h2>
						<p>Der Satz stammt vom Kommunikationswissenschaftler Paul Watzlawick. Auf einer Website gilt er für jedes Detail. Eine Seite, die lange lädt, sagt: Wir haben keine Eile. Ein Formular mit vierzehn Feldern sagt: Ihre Zeit ist uns egal. Ein Blog, dessen letzter Beitrag drei Jahre alt ist, sagt: Hier passiert nichts mehr. Geschrieben hat das niemand. Gelesen wird es trotzdem.</p>
					</div>

					<div class="about-visual about-visual-language" aria-hidden="true">
						<p class="about-visual-label">Was die Oberfläche mitsagt</p>
						<div class="about-language-row">
							<span>Ladezeit</span>
							<i></i>
							<strong>„Wir haben keine Eile.“</strong>
						</div>
						<div class="about-language-row">
							<span>14 Felder</span>
							<i></i>
							<strong>„Ihre Zeit ist uns egal.“</strong>
						</div>
						<div class="about-language-row">
							<span>Alter Blog</span>
							<i></i>
							<strong>„Hier passiert nichts.“</strong>
						</div>
						<div class="about-visual-foot">Auch Schweigen sendet ein Signal.</div>
					</div>
				</div>
			</div>
		</section>

		<section id="erstes-gespraech" class="about-step about-step-story" aria-labelledby="erstes-gespraech-h" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">02</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll about-story-grid">
					<div class="about-copy">
						<h2 class="kopf" id="erstes-gespraech-h">Das erste Gespräch führt Ihre Website allein.</h2>
						<p>Im B2B ruft selten jemand einfach an. Vorher wird gelesen, verglichen und weitergeleitet, oft abends und oft von mehreren Leuten. In dieser Zeit entscheidet sich, ob Sie überhaupt auf die Liste kommen. Von denen, die sich melden, erfahren Sie. Von allen anderen hören Sie nie.</p>
					</div>

					<div class="about-visual about-visual-shortlist" aria-hidden="true">
						<p class="about-visual-label">Die unsichtbare Vorauswahl</p>
						<div class="about-shortlist-flow">
							<span>Lesen</span>
							<b>→</b>
							<span>Vergleichen</span>
							<b>→</b>
							<span>Weiterleiten</span>
						</div>
						<div class="about-shortlist-result">
							<span class="about-shortlist-dot"></span>
							<strong>Shortlist</strong>
							<small>bevor jemand mit Ihnen spricht</small>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section id="zuhoeren" class="about-step about-step-story" aria-labelledby="zuhoeren-h" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">03</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll about-story-grid">
					<div class="about-copy">
						<h2 class="kopf" id="zuhoeren-h">Zuhören kann sie auch. Wenn man sie lässt.</h2>
						<p>Jede Anfrage ist eine Nachricht vom Markt: wer sucht, mit welchem Problem und über welchen Weg. Auf den meisten Websites landet sie als lose E-Mail im Postfach. Woher sie kam, wie gut sie war und was aus ihr wurde, weiß ein halbes Jahr später niemand mehr.</p>
						<p>Deshalb gehört für mich beides zusammen: was eine Website sagt und was sie erfährt. Das eine ist Kommunikation, das andere Organisation. Die meisten Websites können nur das Erste.</p>
					</div>

					<div class="about-visual about-visual-return" aria-hidden="true">
						<p class="about-visual-label">Wenn die Strecke zurückspricht</p>
						<div class="about-return-flow">
							<span>Quelle</span>
							<i></i>
							<span>Anfrage</span>
							<i></i>
							<span>CRM</span>
							<i></i>
							<span>Ergebnis</span>
						</div>
						<div class="about-return-loop">
							<span></span>
							<strong>Rückkanal</strong>
							<small>Was funktioniert – und warum?</small>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section id="arbeitsweise" class="about-step about-step-method" aria-labelledby="arbeitsweise-h" data-track-section="about_method" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">04</span><span class="titel">Methode</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll">
					<div class="about-copy">
						<p class="about-section-kicker">Diagnose vor Dekoration</p>
						<h2 class="kopf" id="arbeitsweise-h">Ich fange mit Zuhören an.</h2>
						<p id="schwaechstes-glied">Bevor ich etwas baue, lese ich Ihre Website so, wie ein Kunde sie liest: auf dem Handy, abends, mit einer konkreten Frage im Kopf. Ich suche die Stelle, an der das Gespräch abbricht. Findet man Sie nicht? Versteht man nicht, was Sie anbieten? Oder bleibt die Anfrage irgendwo zwischen Formular und Vertrieb liegen? Dort fange ich an. Solange diese Stelle offen ist, bringt jede andere Verbesserung wenig.</p>
						<p>Danach klären wir, womit wir es zu tun haben.</p>
					</div>

					<div class="about-decisions" id="defekt-oder-stellschraube">
						<article class="about-decision">
							<div class="about-decision-head">
								<span class="about-decision-index">01</span>
								<span class="about-decision-state">feststellen</span>
							</div>
							<div>
								<h3>Ein Fehler.</h3>
								<p>Das Formular verschickt nichts. Eine wichtige Seite ist für Google gesperrt. Eine Anfrage wird nicht gezählt. Das wird repariert und geprüft. Darüber muss niemand diskutieren.</p>
							</div>
							<div class="about-decision-symbol" aria-hidden="true"><span></span><span></span></div>
						</article>

						<article class="about-decision">
							<div class="about-decision-head">
								<span class="about-decision-index">02</span>
								<span class="about-decision-state">abwägen</span>
							</div>
							<div>
								<h3>Eine Entscheidung.</h3>
								<p>Für einen Anfrageweg oder eine Seitenstruktur gibt es mehrere gute Lösungen. Ich wäge ab, was Ihre Besucher brauchen und was Ihr Team pflegen kann. Dann sage ich Ihnen, wofür ich mich entscheiden würde und warum.</p>
							</div>
							<div class="about-decision-symbol is-choice" aria-hidden="true"><span></span><span></span></div>
						</article>

						<article class="about-decision">
							<div class="about-decision-head">
								<span class="about-decision-index">03</span>
								<span class="about-decision-state">messen</span>
							</div>
							<div>
								<h3>Eine Annahme.</h3>
								<p>Ob eine andere Überschrift mehr Anfragen bringt, weiß vorher niemand. Wir legen fest, woran wir es messen, und schauen nach. Bei wenig Besuchern zählen Gespräche und die Qualität der Anfragen mehr als jeder A/B-Test.</p>
							</div>
							<div class="about-decision-symbol is-hypothesis" aria-hidden="true"><span></span><span></span><span></span></div>
						</article>
					</div>

					<p class="about-thesis">Viele Projekte verlieren Zeit, weil man diese drei verwechselt.</p>

					<aside class="about-proof" aria-label="Beleg">
						<div class="about-proof-number">
							<span class="mono">Beleg</span>
							<strong><?php echo esc_html( $e3_metric( 'cpl_before' ) ); ?> <i aria-hidden="true">→</i> <?php echo esc_html( $e3_metric( 'cpl_after' ) ); ?></strong>
						</div>
						<div class="about-proof-copy">
							<p>Kosten pro qualifizierter Anfrage bei einem mittelständischen PV-Installationsbetrieb – in <?php echo esc_html( $e3_metric( 'timeframe', 'display_dative' ) ); ?>.</p>
							<a class="textlink" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="about_station_solar_case" data-track-category="trust" data-track-section="about_method">Wie das im Einzelnen lief <span aria-hidden="true">→</span></a>
						</div>
					</aside>
				</div>
			</div>
		</section>

		<section id="werkzeuge" class="about-step about-step-change" aria-labelledby="werkzeuge-h" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">05</span><span class="titel">Wandel</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll about-story-grid">
					<div class="about-copy">
						<p class="about-section-kicker">Neue Oberfläche, gleiche Grundregel</p>
						<h2 class="kopf" id="werkzeuge-h">Das Gespräch beginnt immer öfter woanders.</h2>
						<p>Suchmaschinen und KI-Assistenten beantworten Fragen inzwischen selbst und nennen dabei Anbieter. Oder eben nicht. Wer dort vorkommen will, braucht eine klare Aussage, eine saubere Struktur und Inhalte, auf die man sich berufen kann.</p>
						<p>Die Werkzeuge ändern sich schnell. Die Grundregel bleibt: Wer klar spricht, wird verstanden, von Menschen und von Maschinen. KI nutze ich selbst, wo sie die Arbeit besser macht. Die Verantwortung für das Ergebnis bleibt bei mir.</p>
					</div>

					<div class="about-visual about-visual-discovery" aria-hidden="true">
						<p class="about-visual-label">Das Gespräch verteilt sich</p>
						<div class="about-discovery-sources">
							<span>Google</span>
							<span>KI-Assistent</span>
							<span>Empfehlung</span>
						</div>
						<div class="about-discovery-lines"><i></i><i></i><i></i></div>
						<div class="about-discovery-core">
							<span class="about-discovery-pulse"></span>
							<strong>Klare Aussage</strong>
							<small>strukturierte, belegbare Information</small>
						</div>
						<div class="about-discovery-output">→ Mensch und Maschine verstehen dasselbe.</div>
					</div>
				</div>
			</div>
		</section>

		<section id="gestaltung" class="about-step about-step-principle" aria-labelledby="ueberzeugen-h" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">06</span><span class="titel">Haltung</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll">
					<div class="about-principle-panel" id="ueberzeugen">
						<div class="about-principle-mark" aria-hidden="true">
							<span>Überzeugen</span>
							<b>≠</b>
							<span>Täuschen</span>
						</div>
						<div class="about-copy">
							<h2 class="kopf" id="ueberzeugen-h">Überzeugen: ja. Täuschen: nein.</h2>
							<p>Keine erfundene Knappheit, keine versteckten Kosten, keine künstlichen Hürden. Menschen sollen selbst beurteilen können, ob ein Angebot zu ihnen passt. Ein gutes Gespräch braucht keine Tricks.</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section id="zugaenge" class="about-step about-step-collaboration" aria-labelledby="zugaenge-h" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">07</span><span class="titel">Zusammenarbeit</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll about-story-grid">
					<div class="about-copy">
						<p class="about-section-kicker">Direkter Weg, saubere Übergabe</p>
						<h2 class="kopf" id="zugaenge-h">Bei mir gibt es keine stille Post.</h2>
						<p>Sie sprechen mit dem, der plant, baut und prüft. Keine Übergabe an ein Team, das den Anfang nicht kennt.</p>
						<p>Und falls Sie sich fragen, was passiert, wenn ich ausfalle: Domain, Konten und Daten liegen von Anfang an bei Ihnen. Code und Entscheidungen sind so dokumentiert, dass ein anderer Entwickler weiterarbeiten kann. Wie das aussieht, sehen Sie an dieser Website: Ihr Code und jede Änderung sind öffentlich. <a class="satzlink" href="<?php echo esc_url( $repo_url . '/commits/main/' ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="about_code_history" data-track-category="trust" data-track-section="about_handover">Änderungen ansehen ↗</a></p>
					</div>

					<div class="about-visual about-ownership" aria-hidden="true">
						<p class="about-visual-label">Ownership bleibt bei Ihnen</p>
						<div class="about-ownership-row">
							<span>Domain</span><strong>Ihr Konto</strong><i></i>
						</div>
						<div class="about-ownership-row">
							<span>Daten</span><strong>Ihr Zugriff</strong><i></i>
						</div>
						<div class="about-ownership-row">
							<span>Code</span><strong>Ihr Repository</strong><i></i>
						</div>
						<div class="about-ownership-footer">
							<span>Ich plane</span><b>→</b><span>ich baue</span><b>→</b><span>ich prüfe</span>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section id="hintergrund" class="about-step about-step-origin" aria-label="Kurz zu mir" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">08</span><span class="titel">Kurz zu mir</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll">
					<div class="about-origin">
						<div class="about-origin-copy about-copy">
							<p class="about-section-kicker">Vier Linien, eine Arbeitsweise</p>
							<h2 class="kopf">Warum ich Websites als System sehe.</h2>
							<p>Medienwissenschaften in Paderborn, Abschluss 2013. Meine Bachelorarbeit handelte von persuasiver Werbung, und das Ergebnis war: Sie nervt vor allem. Später kamen B2B-Vertrieb und ein eigener Onlineshop, bei dem ich die ganze Strecke selbst verantwortet habe. Ich komme aus einem Unternehmerhaushalt und arbeite heute als WordPress-Entwickler in Pattensen bei Hannover.</p>
						</div>

						<div class="about-origin-map" aria-hidden="true">
							<div class="about-origin-line">
								<span class="about-origin-year">01</span>
								<strong>Medienwissenschaft</strong>
								<small>Wie Kommunikation wirkt.</small>
							</div>
							<div class="about-origin-line">
								<span class="about-origin-year">02</span>
								<strong>B2B-Vertrieb</strong>
								<small>Wie Entscheidungen entstehen.</small>
							</div>
							<div class="about-origin-line">
								<span class="about-origin-year">03</span>
								<strong>E-Commerce</strong>
								<small>Wie eine Strecke wirtschaftlich wird.</small>
							</div>
							<div class="about-origin-line">
								<span class="about-origin-year">04</span>
								<strong>WordPress &amp; Tracking</strong>
								<small>Wie man alles technisch verbindet.</small>
							</div>
							<div class="about-origin-now">
								<span></span>
								<strong>Website als System</strong>
								<small>Kommunikation · Technik · Messbarkeit</small>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>

	<div class="abschluss about-finale" id="zusammenarbeit" data-track-section="about_cta">
		<div class="blatt">
			<div class="tafel about-close">
				<div>
					<p class="mono">Zusammenarbeit</p>
					<h2 id="hu-about-cta-title">Welches Gespräch führt Ihre Website gerade?</h2>
					<p class="aufriss">Beschreiben Sie kurz Ihre Ausgangslage und was besser werden soll. Ich sage Ihnen, wo ich ansetzen würde und was wir für ein Angebot noch klären müssen.</p>
					<div class="ausgang"><a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="cta_about_project" data-track-category="lead_gen" data-track-section="about_cta">Projekt beschreiben <span aria-hidden="true">→</span></a></div>
					<p class="about-reply"><?php echo esc_html( $response ); ?>, von mir persönlich.</p>
					<p class="about-exits"><a class="satzlink" href="<?php echo esc_url( $freelancer_url . '#angebote' ); ?>" data-track-action="link_about_freelancer" data-track-category="internal_link" data-track-section="about_cta">Leistungen und Preise ansehen</a> <span aria-hidden="true">·</span> <a class="satzlink" href="<?php echo esc_url( $results_url ); ?>" data-track-action="about_view_results" data-track-category="trust" data-track-section="about_cta">Arbeiten und Ergebnisse</a></p>
				</div>

				<aside class="about-agency">
					<p class="mono">Für Agenturen</p>
					<h3>Ihre Kundenführung. Meine Umsetzung.</h3>
					<p>WordPress, Tracking und technische Weiterentwicklung unter Ihrem Namen. Umfang und Preis vereinbaren wir vor dem Erstprojekt.</p>
					<a class="textlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="link_about_whitelabel" data-track-category="lead_gen" data-track-section="about_cta">White-Label ansehen →</a>
				</aside>
			</div>

			<div class="about-personal">
				<p>Abseits der Kundenarbeit schreibe ich auf <a class="satzlink" href="<?php echo esc_url( $blog_url ); ?>" rel="me noopener noreferrer" target="_blank" data-track-action="link_about_blog" data-track-category="navigation" data-track-section="about_coda">hasimuener.org</a> über Medien und Öffentlichkeit.</p>
				<ul role="list">
					<li><a class="satzlink" href="<?php echo esc_url( $linkedin_url ); ?>" rel="me noopener noreferrer" target="_blank" data-track-action="link_about_linkedin" data-track-category="navigation" data-track-section="about_cta">LinkedIn ↗</a></li>
					<li><a class="satzlink" href="<?php echo esc_url( $github_url ); ?>" rel="me noopener noreferrer" target="_blank" data-track-action="link_about_github" data-track-category="navigation" data-track-section="about_cta">GitHub ↗</a></li>
					<li><a class="satzlink" href="<?php echo esc_url( 'mailto:' . $mail_address, [ 'mailto' ] ); ?>" data-track-action="link_about_mail" data-track-category="navigation" data-track-section="about_cta">E-Mail</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
