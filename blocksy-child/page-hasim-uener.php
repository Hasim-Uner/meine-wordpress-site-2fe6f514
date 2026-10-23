<?php
/**
 * Personal page at /hasim-uener/.
 *
 * Die Seite beantwortet zuerst die Vertrauensfrage: Wer ist das? Danach
 * folgen Perspektive (Website als Gespräch mit Rückkanal), Arbeitsweise und
 * Zusammenarbeit. 01 Person, 02 Perspektive, 03 Arbeitsweise,
 * 04 Zusammenarbeit (inkl. Haltung und eigenem KI-Einsatz).
 *
 * Die oeffentlichen Anker (#hintergrund, #geschichte, #werkzeuge,
 * #arbeitsweise, #schwaechstes-glied, #defekt-oder-stellschraube,
 * #zugaenge, #gestaltung, #ueberzeugen) bleiben stabil. Zahlen zum
 * Referenzfall stehen eingeordnet in der Fallstudie, nicht auf dieser Seite.
 * Die Antwortzeit kommt aus dem Kanon.
 *
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes         = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$request_url    = $routes['project_request'] ?? home_url( '/kontakt/?type=project&focus=implementation_scope' );
$whitelabel_url = $routes['whitelabel'] ?? home_url( '/whitelabel-retainer/' );
$references_url = $routes['references'] ?? home_url( '/referenzen/' );
$mail_address   = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$response       = hu_response_promise( 'phrase' );

$linkedin_url = 'https://www.linkedin.com/in/hasim-uener/';
$github_url   = 'https://github.com/Hasim-hannover';
$blog_url     = 'https://hasimuener.org/';
$repo_url     = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';

$portrait_url = get_stylesheet_directory_uri() . '/assets/img/hasim-portrait-office-440x550.webp';

$e3_canon    = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );

get_header();
?>

<div class="doku hu-about" id="about-content">
	<header class="blatt kopfteil about-head">
		<div class="about-hero">
			<div class="about-hero-copy">
				<p class="gegenstand">Über Haşim Üner · WordPress-Entwickler</p>
				<h1>Ihre Website führt Gespräche, bei denen Sie nicht dabei sind.</h1>
				<p class="aufriss">Ich bin Haşim Üner. Ich baue Websites, die diese Gespräche gut führen – und die Ihnen erzählen, wie sie ausgegangen sind.</p>

				<div class="about-hero-actions">
					<a class="tun about-start" href="#arbeitsweise" data-track-action="about_read_method" data-track-category="navigation" data-track-section="about_hero">Wie ich arbeite <span aria-hidden="true">↓</span></a>
					<span class="about-hero-meta">WordPress · Tracking · Conversion</span>
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
			<a href="#hintergrund"><span>01</span>Person</a>
			<a href="#geschichte"><span>02</span>Perspektive</a>
			<a href="#arbeitsweise"><span>03</span>Arbeitsweise</a>
			<a href="#zugaenge"><span>04</span>Zusammenarbeit</a>
		</nav>
	</header>

	<div class="about-story" data-about-story>
		<div class="about-story-rail" aria-hidden="true">
			<span class="about-story-track"></span>
			<span class="about-story-progress" data-about-progress></span>
		</div>

		<section id="hintergrund" class="about-step about-step-origin" aria-labelledby="hintergrund-h" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">01</span><span class="titel">Person</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll">
					<div class="about-origin">
						<div class="about-origin-copy about-copy">
							<p class="about-section-kicker">Vier Linien, eine Arbeitsweise</p>
							<h2 class="kopf" id="hintergrund-h">Woher dieser Blick kommt.</h2>
							<p>Ich bin in einem Unternehmerhaushalt aufgewachsen, mein Vater war Bauunternehmer.</p>
							<p><strong>Medienwissenschaft.</strong> Paderborn, Abschluss 2013. In meiner Bachelorarbeit über persuasive Werbung zeigten die Daten, dass die Online-Werbeformate jener Zeit bei Nutzern schlecht ankamen, vor allem weil sie störten. Seitdem weiß ich: Aufmerksamkeit erzwingen ist nicht dasselbe wie überzeugen.</p>
							<p><strong>B2B-Vertrieb.</strong> Abschlüsse sind selten am Preis gescheitert. Eher an Restlaufzeiten, am falschen Ansprechpartner oder an einem fehlenden Vergleichsmaßstab. Einiges davon ist heute Aufgabe der Website.</p>
							<p><strong>Eigener Onlineshop.</strong> Die ganze Strecke habe ich selbst verantwortet, vom Shop über die Kampagnen bis zur E-Mail. Dort habe ich gelernt, auf die Marge zu schauen, nicht nur auf die Conversion.</p>
							<p><strong>WordPress &amp; Tracking.</strong> Websites baue ich seit dem Studium, zuerst für Familie und Bekannte. Heute verbinde ich damit alles andere: Ich baue die Seite und messe, was aus jeder Anfrage wird.</p>
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

		<section id="geschichte" class="about-step about-step-story" aria-labelledby="geschichte-h" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">02</span><span class="titel">Perspektive</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll about-story-grid">
					<div class="about-copy">
						<h2 class="kopf" id="geschichte-h">Man kann nicht nicht kommunizieren.</h2>
						<p>Der Satz stammt vom Kommunikationswissenschaftler Paul Watzlawick. Auf einer Website gilt er für jedes Detail. Eine Seite, die lange lädt, sagt: Wir haben keine Eile. Ein Formular mit vierzehn Feldern sagt: Ihre Zeit ist uns egal. Ein Blog, dessen letzter Beitrag drei Jahre alt ist, sagt: Hier passiert nichts mehr. Geschrieben hat das niemand. Gelesen wird es trotzdem.</p>
						<p>Im B2B wird vorher gelesen, verglichen und weitergeleitet, oft abends, oft von mehreren Leuten. Von denen, die sich melden, erfahren Sie. Von allen anderen nie.</p>
						<p>Deshalb gehört für mich beides zusammen: was eine Website sagt und was sie erfährt. Woher eine Anfrage kam, wie gut sie war und was aus ihr wurde.</p>
						<p id="werkzeuge">Immer öfter beginnt das Gespräch woanders: Suchmaschinen und KI-Assistenten beantworten Fragen selbst und nennen dabei Anbieter. Dort kommt vor, wer klar spricht und Inhalte hat, auf die man sich berufen kann.</p>
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
					<div class="kapitel"><span class="nr">03</span><span class="titel">Arbeitsweise</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll">
					<div class="about-copy">
						<p class="about-section-kicker">Diagnose vor Dekoration</p>
						<h2 class="kopf" id="arbeitsweise-h">Bevor ich etwas baue, suche ich die Stellen, an denen es abbricht.</h2>
						<p id="schwaechstes-glied">Ich lese Ihre Website so, wie ein Kunde sie liest: auf dem Handy, abends, mit einer konkreten Frage im Kopf. Dann schaue ich in die Daten und frage Ihren Vertrieb, welche Anfragen etwas taugen. Findet man Sie nicht? Versteht man nicht, was Sie anbieten? Bleibt die Anfrage zwischen Formular und Vertrieb liegen? Was kaputt ist, wird sofort repariert. Alles andere ordnen wir danach, was am meisten bremst.</p>
						<p>Manchmal reicht eine Reparatur. Dann verkaufe ich Ihnen keine neue Website.</p>
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
						<div class="about-proof-copy">
							<p><span class="mono">Beleg</span> Bei einem mittelständischen PV-Installationsbetrieb habe ich Landingpages, Kampagnen, Tracking und CRM zu einem eigenen Anfrageweg verbunden, statt weiter nur Portal-Anfragen einzukaufen.</p>
							<p><a class="textlink" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="about_station_solar_case" data-track-category="trust" data-track-section="about_method">Wie das lief, mit allen Zahlen <span aria-hidden="true">→</span></a> <span aria-hidden="true">·</span> <a class="textlink" href="<?php echo esc_url( $references_url ); ?>" data-track-action="about_reference_open" data-track-category="trust" data-track-section="about_method">Weitere Arbeiten <span aria-hidden="true">→</span></a></p>
						</div>
					</aside>
				</div>
			</div>
		</section>

		<section id="zugaenge" class="about-step about-step-collaboration" aria-labelledby="zugaenge-h" data-about-step>
			<div class="blatt reihe">
				<div class="spalte-links">
					<div class="kapitel"><span class="nr">04</span><span class="titel">Zusammenarbeit</span><span class="strich" aria-hidden="true"></span></div>
				</div>
				<div class="voll about-story-grid">
					<div class="about-copy">
						<p class="about-section-kicker">Direkter Weg, saubere Übergabe</p>
						<h2 class="kopf" id="zugaenge-h">Bei mir gibt es keine stille Post.</h2>
						<p>Sie sprechen mit dem, der plant, baut und prüft. Keine Übergabe an ein Team, das den Anfang nicht kennt.</p>
						<p id="gestaltung"><strong id="ueberzeugen">Überzeugen: ja. Täuschen: nein.</strong> Keine erfundene Knappheit, keine versteckten Kosten, keine künstlichen Hürden. Sie sollen selbst beurteilen können, ob ein Angebot zu Ihnen passt.</p>
						<p>KI nutze ich, wo sie die Arbeit besser macht. Die Verantwortung für das Ergebnis bleibt bei mir.</p>
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
					<p class="about-exits">Sie sind Agentur? <a class="satzlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="link_about_whitelabel" data-track-category="lead_gen" data-track-section="about_cta">White-Label ansehen</a></p>
				</div>
			</div>

			<div class="about-personal">
				<p>Abseits der Kundenarbeit schreibe ich auf <a class="satzlink" href="<?php echo esc_url( $blog_url ); ?>" rel="me noopener noreferrer" target="_blank" data-track-action="link_about_blog" data-track-category="navigation" data-track-section="about_coda">hasimuener.org</a> über Medien und Öffentlichkeit.</p>
				<ul role="list">
					<li><a class="satzlink" href="<?php echo esc_url( $linkedin_url ); ?>" rel="me noopener noreferrer" target="_blank" data-track-action="link_about_linkedin" data-track-category="navigation" data-track-section="about_cta">LinkedIn ↗</a></li>
					<li><a class="satzlink" href="<?php echo esc_url( $github_url ); ?>" rel="me noopener noreferrer" target="_blank" data-track-action="link_about_github" data-track-category="navigation" data-track-section="about_cta">GitHub-Profil ↗</a></li>
					<li><a class="satzlink" href="<?php echo esc_url( 'mailto:' . $mail_address, [ 'mailto' ] ); ?>" data-track-action="link_about_mail" data-track-category="navigation" data-track-section="about_cta">E-Mail</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
