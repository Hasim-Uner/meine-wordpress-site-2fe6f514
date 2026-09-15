<?php
/**
 * Homepage: canonical WordPress Freelancer money page.
 *
 * Replaces /wordpress-freelancer-hannover/ with one query owner at /.
 * Specialist routes retain their own offers. Contact stays in the shared intake.
 * Prices, response promises and references are read from their existing canons.
 *
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes         = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$contact_url    = $routes['project_request'] ?? home_url( '/kontakt/?type=project&focus=implementation_scope' );
$whitelabel_url = $routes['whitelabel'] ?? home_url( '/whitelabel-retainer/' );
$energy_url     = $routes['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );
$tracking_url   = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
$about_url      = $routes['about'] ?? home_url( '/hasim-uener/' );
$results_url    = $routes['results'] ?? home_url( '/ergebnisse/' );
$contact_email  = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$portrait_url   = get_stylesheet_directory_uri() . '/assets/img/hasim-freelancer-relaxed-640x800.webp';
$website_price  = function_exists( 'hu_freelancer_website_price' ) ? hu_freelancer_website_price( true ) : '3.400 € netto';
$tracking_price = function_exists( 'hu_tracking_price' ) ? hu_tracking_price( 'standard', 'setup', 'display', '1.290 €' ) : '1.290 €';
$response       = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'phrase' ) : 'Antwort spätestens in 2 Werktagen';
$references     = function_exists( 'hu_public_reference_projects' ) ? hu_public_reference_projects() : [];
$github_url     = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/' ) );
$e3_canon       = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url    = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_case_label  = $e3_canon['case_label'] ?? 'mittelständischer PV-Installationsbetrieb';
$e3_metric      = static function ( $key, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field, $fallback ) : $fallback;
};
$project_link = static function ( $focus ) use ( $contact_url ) {
	return function_exists( 'hu_get_contact_intake_url' )
		? hu_get_contact_intake_url( 'project', $focus )
		: add_query_arg( [ 'type' => 'project', 'focus' => $focus ], $contact_url );
};
$offers = [
	[
		'id' => 'angebot-website', 'nr' => '01', 'title' => 'Website neu oder Relaunch',
		'problem' => 'Ihr Angebot ist weiter als Ihre Website.',
		'text' => 'Ich entwickle eine WordPress-Website, die Ihr Angebot verständlich macht und zum nächsten Schritt führt. Bei einem Relaunch berücksichtige ich bestehende Inhalte, URLs und Weiterleitungen.',
		'scope' => 'Seitenstruktur · WordPress-Entwicklung · technisches SEO · Staging und Übergabe',
		'price' => 'Ab ' . $website_price, 'focus' => 'relaunch', 'cta' => 'Website-Projekt anfragen',
	],
	[
		'id' => 'angebot-funnel', 'nr' => '02', 'title' => 'Landingpage und Anfragestrecke',
		'problem' => 'Die Kampagne läuft. Die passenden Anfragen fehlen.',
		'text' => 'Eine Landingpage mit passendem Angebot, verständlichem Formular und klarer Bestätigung. Vorqualifizierung, Conversion-Messung und Lead-Übergabe werden im vereinbarten Umfang mitgebaut.',
		'scope' => 'Landingpage · Formular · Danke-Seite · Messung und Übergabe nach Bedarf',
		'price' => 'Projektpreis nach Umfang', 'focus' => 'conversion', 'cta' => 'Anfragestrecke besprechen',
	],
	[
		'id' => 'angebot-tracking', 'nr' => '03', 'title' => 'Tracking nachvollziehbar machen',
		'problem' => 'Werbeplattform, Analytics und CRM erzählen verschiedene Geschichten.',
		'text' => 'Ich prüfe die Messkette und setze GA4, GTM und Consent-Anbindung passend zu Ihrem Setup um. Server-Side Tracking und Rückmeldungen an Werbekanäle ergänzen wir dort, wo sie gebraucht werden.',
		'scope' => 'Messkonzept · Ereignisse prüfen · Consent berücksichtigen · Abnahme dokumentieren',
		'price' => 'Standard-Setup ab ' . $tracking_price . ' netto', 'focus' => 'tracking', 'cta' => 'Tracking-Projekt anfragen',
	],
	[
		'id' => 'angebot-weiterentwicklung', 'nr' => '04', 'title' => 'Bestehendes WordPress weiterentwickeln',
		'problem' => 'Es gibt konkrete Aufgaben, aber niemanden, der sie umsetzt.',
		'text' => 'Neue Bereiche, technische Fehler, Ladezeit oder ein Formular, das nicht sauber arbeitet: Wir priorisieren die nächste sinnvolle Änderung. Ein kompletter Neubau ist dafür keine Voraussetzung.',
		'scope' => 'Bestand prüfen · Aufgabe begrenzen · Änderung testen · kontrolliert veröffentlichen',
		'price' => 'Umfang und Budget vor Start', 'focus' => 'implementation_scope', 'cta' => 'Aufgabe beschreiben',
	],
];
$faqs = [
	[ 'q' => 'Übernehmen Sie eine bestehende WordPress-Website?', 'a' => 'Ja. Vor einer Zusage prüfe ich Theme, Plugins, Zugänge und die konkrete Aufgabe. Daraus wird eine begrenzte Weiterentwicklung, eine technische Bereinigung oder ein Relaunch. Ein Neubau ist keine Voraussetzung.' ],
	[ 'q' => 'Arbeiten Sie auch mit unserem Page Builder?', 'a' => 'Die Entscheidung hängt vom vorhandenen Aufbau und der Aufgabe ab. Ein funktionierender Editor muss nicht ersetzt werden. Wo Performance, Wartbarkeit oder eine Integration an Grenzen stoßen, klären wir den nötigen Eingriff vorab.' ],
	[ 'q' => 'Was brauchen Sie von unserem Team?', 'a' => 'Eine Person, die Entscheidungen trifft, die nötigen Zugänge sowie vorhandene Texte und Gestaltungsvorgaben. Fehlende Inhalte, Branding, Übersetzungen und zusätzliche Schnittstellen klären wir im Angebot. Sie sind nicht automatisch im Einstiegspreis enthalten.' ],
	[ 'q' => 'Wann kann das Projekt starten und wie lange dauert es?', 'a' => 'Das hängt von meiner freien Kapazität, dem Umfang und Ihren Vorarbeiten ab. Nach der ersten Einordnung erhalten Sie einen realistischen Zeitrahmen. Inhaltslieferung, Freigaben und technische Abhängigkeiten werden dabei berücksichtigt.' ],
	[ 'q' => 'Gehören Website, Konten und Code anschließend uns?', 'a' => 'Ja. Code, Repository, Hosting und eingesetzte Konten liegen in Ihrer Hand. Zur Übergabe gehören die vereinbarte Dokumentation und Zugänge. Eine weitere Betreuung ist möglich, aber keine Voraussetzung dafür, die Website weiterzuführen.' ],
	[ 'q' => 'Was passiert nach meiner Anfrage?', 'a' => 'Ich prüfe Ausgangslage und Ziel und melde mich innerhalb der genannten Antwortzeit mit einer ersten Einordnung. Passt die Aufgabe, klären wir Scope, Zugänge, Zeitrahmen und Angebot. Passt sie nicht, sage ich das ebenfalls.' ],
	[ 'q' => 'Müssen wir aus Hannover kommen?', 'a' => 'Nein. Ich arbeite aus Pattensen in der Region Hannover und betreue Projekte remote im gesamten DACH-Raum. Abstimmung und Abnahme funktionieren über einen gemeinsamen, dokumentierten Projektstand.' ],
];

if ( function_exists( 'hu_enqueue_css' ) ) {
	hu_enqueue_css( 'nexus-startseite-css', 'startseite.css', [ 'nexus-system-css' ] );
}
if ( function_exists( 'hu_enqueue_js' ) ) {
	hu_enqueue_js( 'nexus-startseite-js', 'startseite.js', [] );
}
get_header();
?>

<div class="doku startseite" id="top" data-track-section="homepage">
	<div class="blatt kopfteil">
		<div class="home-hero">
			<div>
				<p class="gegenstand">Haşim Üner · Pattensen bei Hannover · remote im DACH-Raum</p>
				<h1>WordPress Freelancer Hannover.<br><span>Von der Website bis zur Anfrage.</span></h1>
				<p class="aufriss">Ich entwickle WordPress-Websites, Relaunches und Landingpages für Unternehmen. Wenn Formulare, Tracking oder CRM zum Projekt gehören, baue ich die Übergänge mit. Sie arbeiten direkt mit dem Entwickler, der Ihr Projekt umsetzt.</p>
				<div class="ausgang">
					<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_head_contact" data-track-category="lead_gen" data-track-section="hero">Projekt anfragen <span aria-hidden="true">→</span></a>
					<a class="tun still" href="#angebote" data-track-action="home_hero_to_offers" data-track-category="navigation" data-track-section="hero">Leistungen und Preise</a>
				</div>
				<p class="home-reply"><?php echo esc_html( $response ); ?> · Umfang und Preis vor Projektstart</p>
			</div>
			<figure class="home-portrait">
				<img src="<?php echo esc_url( $portrait_url ); ?>" width="640" height="800" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" fetchpriority="high" decoding="async">
				<figcaption><strong>Ihr direkter Ansprechpartner.</strong><span>Von der ersten Abstimmung bis zur technischen Übergabe.</span><a class="satzlink" href="<?php echo esc_url( $about_url ); ?>" data-track-action="home_about" data-track-category="trust" data-track-section="hero">Mehr über Haşim</a></figcaption>
			</figure>
		</div>
		<div class="meta" id="einordnung">
			<dl>
				<div><dt>Website / Relaunch</dt><dd>Ab <?php echo esc_html( $website_price ); ?></dd></div>
				<div><dt>Zusammenarbeit</dt><dd>Direkt mit mir, ohne Übergabe an ein fremdes Entwicklerteam</dd></div>
				<div><dt>Vor dem Livegang</dt><dd>Prüfbarer Stand auf einer Testumgebung</dd></div>
				<div><dt>Nach der Übergabe</dt><dd>Dokumentation und vereinbarte Zugänge bei Ihnen</dd></div>
			</dl>
		</div>
		<div class="home-specialists" id="wege">
			<p><span class="mono">Für Agenturen</span><a class="satzlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="home_door_whitelabel" data-track-category="navigation" data-track-section="tueren">WordPress-Umsetzung unter Ihrem Namen →</a></p>
			<p><span class="mono">Spezialisierung</span><a class="satzlink" href="<?php echo esc_url( $energy_url ); ?>" data-track-action="home_door_energy" data-track-category="navigation" data-track-section="tueren">Anfragesysteme für Solar und Wärmepumpe →</a></p>
		</div>
		<nav class="home-index" aria-label="Auf dieser Seite">
			<a href="#angebote">01 Leistungen</a><a href="#nachweis">02 Arbeiten</a><a href="#arbeitsweise">03 Zusammenarbeit</a><a href="#fall">04 Ergebnis</a><a href="#fragen">05 Fragen</a>
		</nav>
	</div>

	<section id="angebote" aria-labelledby="angebote-h" data-track-section="angebote">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">01</span><span class="titel">Leistungen</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="angebote-h">Was soll Ihre Website als Nächstes leisten?</h2>
				<p class="vorspann">Ein neues Projekt oder eine konkrete Verbesserung: Sie müssen kein Gesamtpaket buchen, wenn eine begrenzte Aufgabe reicht.</p>
				<div class="home-offers">
					<?php foreach ( $offers as $offer ) : ?>
						<article class="home-offer" id="<?php echo esc_attr( $offer['id'] ); ?>">
							<div><span class="mono"><?php echo esc_html( $offer['nr'] ); ?></span><h3><?php echo esc_html( $offer['title'] ); ?></h3><p class="home-price"><?php echo esc_html( $offer['price'] ); ?></p></div>
							<div><p class="home-problem"><?php echo esc_html( $offer['problem'] ); ?></p><p><?php echo esc_html( $offer['text'] ); ?></p><p class="home-scope"><?php echo esc_html( $offer['scope'] ); ?></p>
								<a class="textlink" href="<?php echo esc_url( $project_link( $offer['focus'] ) ); ?>" data-track-action="<?php echo esc_attr( 'home_offer_' . $offer['focus'] ); ?>" data-track-category="lead_gen" data-track-section="angebote"><?php echo esc_html( $offer['cta'] ); ?> →</a>
								<?php if ( 'tracking' === $offer['focus'] ) : ?><a class="satzlink home-detail-link" href="<?php echo esc_url( $tracking_url ); ?>" data-track-action="home_proof_tracking_page" data-track-category="proof" data-track-section="angebote">Tracking-Leistung im Detail</a><?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
				<p class="home-note">Die Preisrahmen beziehen sich auf den vereinbarten Leistungsumfang. Umfangreiche Migrationen, Texte, zusätzliche Integrationen, Hosting, Lizenzen und Werbebudget klären wir separat. Sie erhalten vor dem Start ein konkretes Angebot.</p>
			</div>
		</div>
	</section>

	<section id="nachweis" aria-labelledby="nachweis-h" data-track-section="beweis">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">02</span><span class="titel">Arbeiten</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="nachweis-h">Ausgewählte Arbeiten. Von WordPress bis zum Vertriebsanschluss.</h2>
				<p class="vorspann">Öffentliche Projekte zeigen die WordPress-Arbeit. Der ausgewählte B2B-Fall zeigt zusätzlich, wie Website, Anfragestrecke, Tracking und CRM als ein zusammenhängendes System entwickelt wurden.</p>

				<article class="tafel home-featured-case" id="systemprojekt">
					<div class="home-featured-case__intro">
						<div>
							<p class="mono">Ausgewähltes Großprojekt · B2B · Solar</p>
							<h3>Vom ersten Klick bis zum Vertriebsanschluss.</h3>
						</div>
						<p>Für einen <?php echo esc_html( $e3_case_label ); ?> wurde nicht nur die Website weiterentwickelt, sondern die gesamte digitale Strecke: Website, Landingpages, Qualifizierung, Messung, Server-Side Tracking, CRM und die Übergabe an den Vertrieb.</p>
					</div>

					<div class="home-system-board" aria-label="Systemarchitektur der umgesetzten B2B-Strecke">
						<div class="home-system-core">
							<div class="home-system-band">
								<div class="home-system-band__label"><span class="mono">Ebene 01</span><strong>Kundenerlebnis</strong></div>
								<div class="home-system-flow">
									<div class="home-system-node"><span class="mono">01</span><strong>Website</strong><small>Einstieg und Vertrauen</small></div>
									<div class="home-system-node"><span class="mono">02</span><strong>Landingpages</strong><small>gezielte Angebotswege</small></div>
									<div class="home-system-node"><span class="mono">03</span><strong>Qualifizierung</strong><small>Formulare und Vorfilter</small></div>
									<div class="home-system-node"><span class="mono">04</span><strong>Anfrage</strong><small>klarer nächster Schritt</small></div>
								</div>
							</div>

							<div class="home-system-band">
								<div class="home-system-band__label"><span class="mono">Ebene 02</span><strong>Messung</strong></div>
								<div class="home-system-measure">
									<span>GA4</span><span>GTM</span><span>Server-Side Tracking</span><span>Attribution</span>
								</div>
							</div>

							<div class="home-system-band">
								<div class="home-system-band__label"><span class="mono">Ebene 03</span><strong>Vertriebsinfrastruktur</strong></div>
								<div class="home-system-flow home-system-flow--sales">
									<div class="home-system-node"><span class="mono">05</span><strong>CRM</strong><small>Lead-Erfassung</small></div>
									<div class="home-system-node"><span class="mono">06</span><strong>Lead-Routing</strong><small>Zuordnung und Übergabe</small></div>
									<div class="home-system-node"><span class="mono">07</span><strong>Vertrieb</strong><small>Anschluss an den Prozess</small></div>
								</div>
							</div>
						</div>

						<aside class="home-system-results" aria-label="Ergebnisse des dokumentierten Falls">
							<div class="home-system-result">
								<span class="mono">Ergebnis 01</span>
								<strong><?php echo esc_html( $e3_metric( 'cpl_reduction', 'display', 'über 85 %' ) ); ?></strong>
								<span>weniger Kosten pro Anfrage</span>
								<small><?php echo esc_html( $e3_metric( 'cpl_before', 'display', '150 €' ) ); ?> → <?php echo esc_html( $e3_metric( 'cpl_after', 'display', '22 €' ) ); ?></small>
							</div>
							<div class="home-system-result">
								<span class="mono">Ergebnis 02</span>
								<strong><?php echo esc_html( $e3_metric( 'lead_count', 'display', '1.750+' ) ); ?></strong>
								<span>qualifizierte Anfragen</span>
								<small>in <?php echo esc_html( $e3_metric( 'timeframe', 'display_dative', '6 Monaten' ) ); ?></small>
							</div>
						</aside>
					</div>

					<div class="home-featured-case__footer">
						<span class="mono">System statt Einzelleistung · Entwicklung, Messung und Übergabe</span>
						<a class="textlink" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="home_work_system_case" data-track-category="proof" data-track-section="beweis">Projektfall und Ergebnisse ansehen →</a>
					</div>
				</article>

				<h3 class="home-reference-heading" id="projekte">Weitere öffentlich einsehbare Arbeiten</h3>
				<p class="home-reference-intro">Hier ist die Website selbst der Beleg. Die Beschreibung nennt meinen Beitrag; der Link zeigt den aktuellen öffentlichen Stand.</p>
				<div class="home-references">
					<?php foreach ( $references as $reference ) : ?>
						<article><p class="mono"><?php echo esc_html( $reference['tag'] ); ?></p><h3><a class="satzlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="home_reference_open" data-track-category="proof" data-track-section="beweis"><?php echo esc_html( $reference['name'] ); ?> ↗</a></h3><p><?php echo esc_html( $reference['text'] ); ?></p></article>
					<?php endforeach; ?>
				</div>
				<div class="home-technical" id="pruefstand">
					<h3>Auch diese Website gehört zum Nachweis.</h3>
					<p>Änderungen und automatische Prüfungen sind öffentlich einsehbar. Eine aktuelle Labormessung können Sie selbst starten; das Ergebnis hängt unter anderem vom Testzeitpunkt ab.</p>
					<ul><li><a class="satzlink" href="<?php echo esc_url( $github_url . '/commits/main/' ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_github_history" data-track-category="proof" data-track-section="beweis">Code und Änderungshistorie ↗</a></li><li><a class="satzlink" href="<?php echo esc_url( $github_url . '/actions' ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_github_ci" data-track-category="proof" data-track-section="beweis">Automatische Prüfungen ↗</a></li><li><a class="satzlink" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_pagespeed" data-track-category="proof" data-track-section="beweis">PageSpeed aktuell messen ↗</a></li></ul>
				</div>
				<div class="ausgang"><a class="textlink" href="<?php echo esc_url( $results_url ); ?>" data-track-action="home_more_results" data-track-category="proof" data-track-section="beweis">Weitere Ergebnisse und Einordnung →</a></div>
			</div>
		</div>
	</section>

	<section id="arbeitsweise" aria-labelledby="arbeitsweise-h" data-track-section="arbeitsweise">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">03</span><span class="titel">Zusammenarbeit</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll" id="strecke">
				<h2 class="kopf" id="arbeitsweise-h">Ein Ansprechpartner. Ein prüfbarer Projektstand.</h2>
				<p class="vorspann" id="position">Als WordPress Freelancer übernehme ich die technische Umsetzung selbst. Sie wissen vor dem Start, was geliefert wird, und vor der Veröffentlichung, was sich geändert hat.</p>
				<ol class="home-process" id="ablauf">
					<li><span class="mono">01 · Einordnen</span><h3>Aufgabe und Grenzen klären</h3><p>Wir prüfen Bestand, Ziel und Abhängigkeiten. Daraus entstehen Leistungsumfang, Preis und Zeitrahmen. Zusätzliche Wünsche stimmen wir vor der Umsetzung ab.</p></li>
					<li><span class="mono">02 · Umsetzen</span><h3>Am echten Stand abstimmen</h3><p>Sie sehen die Arbeit auf einer Testumgebung. Änderungen bleiben nachvollziehbar, Rückfragen landen direkt bei mir.</p></li>
					<li><span class="mono">03 · Übergeben</span><h3>Die wichtigen Wege abnehmen</h3><p>Vor dem Livegang prüfen wir die vereinbarten Funktionen: beispielsweise mobile Nutzung, Formular, Bestätigung und Lead-Eingang. Sie erhalten die passende Dokumentation.</p></li>
				</ol>
				<div class="home-fit" id="eignung"><h3 id="vergleich">Wann diese Zusammenarbeit passt</h3><p>Sie brauchen jemanden, der eine konkrete WordPress-Aufgabe technisch verantwortet, und haben intern einen Ansprechpartner für Inhalte und Freigaben. Wenn zusätzlich Tracking oder eine CRM-Anbindung nötig ist, planen wir beides mit ein. Messung wird passend zu Consent und den verfügbaren Daten umgesetzt.</p></div>
			</div>
		</div>
	</section>

	<section id="fall" aria-labelledby="fall-h" data-track-section="beleg">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">04</span><span class="titel">Ergebnis</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="fall-h">Was das Gesamtsystem erreicht hat.</h2>
				<p class="vorspann">Der dokumentierte Fall aus meiner Solar-Spezialisierung zeigt die Wirkung der gesamten Strecke. Die Kennzahlen beschreiben Website, Nachfrageaufbau, Tracking, CRM-Prozess und Vertrieb im Zusammenspiel.</p>
				<div class="tafel home-case">
					<dl>
						<div>
							<dt>Weniger Kosten pro Anfrage</dt>
							<dd><strong><?php echo esc_html( $e3_metric( 'cpl_reduction', 'display', 'über 85 %' ) ); ?></strong><small><?php echo esc_html( $e3_metric( 'cpl_before', 'display', '150 €' ) ); ?> → <?php echo esc_html( $e3_metric( 'cpl_after', 'display', '22 €' ) ); ?></small></dd>
						</div>
						<div>
							<dt>Qualifizierte Anfragen</dt>
							<dd><strong><?php echo esc_html( $e3_metric( 'lead_count', 'display', '1.750+' ) ); ?></strong><small>in <?php echo esc_html( $e3_metric( 'timeframe', 'display_dative', '6 Monaten' ) ); ?></small></dd>
						</div>
					</dl>
					<p>Die Zahlen beschreiben dieses Gesamtsystem. Sie belegen keinen isolierten WordPress-Effekt und sind keine Prognose für Ihr Projekt.</p>
					<a class="textlink" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="home_case_study" data-track-category="proof" data-track-section="beleg">Ausgangslage und Methodik lesen →</a>
				</div>
			</div>
		</div>
	</section>

	<section id="fragen" aria-labelledby="fragen-h" data-track-section="fragen">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">05</span><span class="titel">Fragen</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll"><h2 class="kopf" id="fragen-h">Vor der Zusammenarbeit.</h2><div class="fragen">
				<?php foreach ( $faqs as $faq ) : ?>
					<details><summary><?php echo esc_html( $faq['q'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $faq['a'] ); ?></p></div></div></details>
				<?php endforeach; ?>
			</div></div>
		</div>
	</section>

	<div class="abschluss" id="anfrage" data-track-section="abschluss" tabindex="-1">
		<div class="blatt" id="kontakt"><div class="tafel home-close">
			<div><p class="mono">Der nächste Schritt</p><h2>Was möchten Sie an Ihrer Website verändern?</h2><p class="aufriss">Beschreiben Sie kurz Ihre Ausgangslage, den Engpass und das gewünschte Ergebnis. Ich melde mich mit einer ersten fachlichen Einordnung und den Fragen, die für ein konkretes Angebot noch offen sind.</p><div class="ausgang"><a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_close_contact" data-track-category="lead_gen" data-track-section="abschluss">Projekt beschreiben <span aria-hidden="true">→</span></a><a class="tun still" href="<?php echo esc_url( 'mailto:' . $contact_email, [ 'mailto' ] ); ?>" data-track-action="home_close_mail" data-track-category="lead_gen" data-track-section="abschluss">Per E-Mail anfragen</a></div></div>
			<div class="home-close-note"><strong><?php echo esc_html( $response ); ?></strong><p>Sie sprechen direkt mit mir. Für die erste Anfrage brauchen Sie kein fertiges Briefing und keinen gebuchten Termin.</p></div>
		</div></div>
	</div>
</div>
<?php get_footer(); ?>