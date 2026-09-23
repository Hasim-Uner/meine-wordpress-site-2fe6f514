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
$response_short = hu_response_promise( 'compact' );
$references     = function_exists( 'hu_public_reference_projects' ) ? hu_public_reference_projects() : [];
$github_url     = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/' ) );
$e3_canon       = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url    = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_case_label_accusative = $e3_canon['case_label_accusative'] ?? 'mittelständischen PV-Installationsbetrieb';
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
		'id' => 'angebot-website', 'nr' => '01', 'title' => 'WordPress-Entwicklung',
		'problem' => 'Neu bauen, relaunchen oder gezielt weiterentwickeln.',
		'text' => 'Ich entwickle und überarbeite WordPress-Websites so, dass Struktur, Technik und Nutzerführung zusammenpassen. Bestehende Inhalte, URLs und funktionierende Systeme bleiben dort erhalten, wo das sinnvoll ist.',
		'scope' => 'Relaunch · Bestandsentwicklung · technisches SEO · Performance · Staging und Übergabe',
		'price' => 'Ab ' . $website_price, 'focus' => 'relaunch', 'cta' => 'WordPress-Projekt anfragen',
	],
	[
		'id' => 'angebot-funnel', 'nr' => '02', 'title' => 'Anfragestrecken & Landingpages',
		'problem' => 'Traffic ist nur dann wertvoll, wenn der nächste Schritt funktioniert.',
		'text' => 'Ich baue Landingpages, Formulare und Qualifizierungswege so, dass Angebot, Anfrage und Übergabe logisch zusammenpassen. Der Umfang reicht vom einzelnen Conversion-Weg bis zur kompletten Anfragestrecke.',
		'scope' => 'Landingpage · Formular · Qualifizierung · Danke-Seite · Lead-Übergabe',
		'price' => 'Projektpreis nach Umfang', 'focus' => 'conversion', 'cta' => 'Anfragestrecke besprechen',
	],
	[
		'id' => 'angebot-tracking', 'nr' => '03', 'title' => 'Tracking & CRM',
		'problem' => 'Eine Anfrage ist erst dann messbar, wenn die Strecke bis ins System reicht.',
		'text' => 'Ich prüfe und entwickle die Messkette von GA4 und GTM bis zu Consent, Server-Side Tracking und CRM-Übergabe. Ergänzt wird nur, was für Ihr Setup und Ihre Daten tatsächlich gebraucht wird.',
		'scope' => 'GA4 · GTM · Consent · Server-Side Tracking · Attribution · CRM-Anbindung',
		'price' => 'Standard-Setup ab ' . $tracking_price . ' netto', 'focus' => 'tracking', 'cta' => 'Tracking-Projekt anfragen',
	],
];
$faqs = [
	[ 'q' => 'Übernehmen Sie eine bestehende WordPress-Website?', 'a' => 'Ja. Vor einer Zusage prüfe ich Theme, Plugins, Zugänge und die konkrete Aufgabe. Daraus wird eine gezielte Weiterentwicklung, eine technische Bereinigung oder ein Relaunch. Ein Neubau ist keine Voraussetzung.' ],
	[ 'q' => 'Arbeiten Sie mit unserem bestehenden Theme oder Page Builder?', 'a' => 'Wenn der vorhandene Aufbau technisch tragfähig ist, muss er nicht ersetzt werden. Erst wenn Performance, Wartbarkeit oder eine Integration klare Grenzen setzen, besprechen wir einen tieferen Eingriff.' ],
	[ 'q' => 'Was brauchen Sie von unserem Team?', 'a' => 'Eine Person für Entscheidungen und Freigaben, die nötigen Zugänge sowie vorhandene Inhalte und Gestaltungsvorgaben. Fehlende Texte, Branding, Übersetzungen oder zusätzliche Schnittstellen werden vor dem Start sauber abgegrenzt.' ],
	[ 'q' => 'Wie lange dauert ein Projekt?', 'a' => 'Das hängt von Umfang, Ausgangslage und Ihren Vorarbeiten ab. Nach der ersten Einordnung erhalten Sie einen realistischen Zeitrahmen. Inhaltslieferung, Freigaben und technische Abhängigkeiten werden dabei ausdrücklich berücksichtigt.' ],
	[ 'q' => 'Gehören Website, Konten und Code anschließend uns?', 'a' => 'Ja. Code, Repository, Hosting und eingesetzte Konten liegen in Ihrer Hand. Zur Übergabe gehören die vereinbarte Dokumentation und Zugänge. Eine weitere Betreuung ist möglich, aber keine Voraussetzung.' ],
];

/**
 * Übergabe als Dokument: dasselbe Muster wie "Was im Befund steht" auf der
 * Energie-Seite. Ein Beispiel, keine Referenz; jeder Punkt steht bereits als
 * Zusage im Ablauf oder in den Fragen. Tracking und CRM nur, wenn beauftragt.
 */
$handover = [
	[ 'Testumgebung', 'Der geprüfte Stand vor dem Livegang, gemeinsam abgenommen.', false ],
	[ 'Code und Repository', 'In Ihrem Account, mit nachvollziehbarer Änderungshistorie.', false ],
	[ 'Zugänge und Konten', 'Hosting, Domain und eingesetzte Dienste laufen auf Ihren Namen.', false ],
	[ 'Dokumentation', 'Aufbau, Pflege und offene Punkte, damit Ihr Team weiterarbeiten kann.', false ],
	[ 'Tracking-Plan', 'Events, Auslöser und Consent-Verhalten, in GA4 geprüft.', true ],
	[ 'Anfrage im CRM', 'Eine Testanfrage mit ihrer Quelle bis ins CRM verfolgt.', true ],
];

/**
 * Hero-Tafel: Stromlinien aus scripts/build-home-feld-svg.py, als statisches
 * SVG vollständig im Markup. home-feld.js legt nur bewegte Funken darüber und
 * wechselt die Beispielquelle; ohne Skript bleibt die erste Quelle stehen.
 */
$flow_label   = 'Beispiel als Stromlinien: Besuche kommen von links über die Website. Ein Teil wird zur Anfrage und landet mit seiner Quelle im CRM, der Rest zieht vorbei.';
$flow_sources = [ 'Empfehlung', 'Google-Suche', 'LinkedIn', 'Google Ads' ];
$feld_file    = get_stylesheet_directory() . '/assets/img/home-feld.svg';
$feld_svg     = is_readable( $feld_file ) ? (string) file_get_contents( $feld_file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme asset.
$feld_svg     = false !== strpos( $feld_svg, '<svg' ) ? substr( $feld_svg, strpos( $feld_svg, '<svg' ) ) : '';

/**
 * Versuch "Kostenlose Ersteinschätzung" (Schalter und Texte im Kanon).
 * Eingeschaltet wird die Ersteinschätzung der primäre Button in Hero und
 * Abschluss, der Projekt-Button bleibt als sekundärer daneben. Ausgeschaltet
 * liefert der Helper nichts und der Projekt-Button behält seine Klasse, damit
 * die Seite byte-gleich zum Stand vor dem Versuch rendert.
 */
$first_assessment_on  = function_exists( 'hu_first_assessment_enabled' ) && hu_first_assessment_enabled();
$project_cta_class    = $first_assessment_on ? 'tun still' : 'tun';
$first_assessment_cta = static function ( $section ) use ( $first_assessment_on ) {
	if ( ! $first_assessment_on ) {
		return '';
	}
	$track_action = 'hero' === $section ? 'home_head_ersteinschaetzung' : 'home_close_ersteinschaetzung';

	return sprintf(
		'<div class="home-cta-first"><a class="tun" href="%1$s" data-track-action="%2$s" data-track-category="lead_gen" data-track-section="%3$s">%4$s <span aria-hidden="true">→</span></a><p class="home-cta-note">%5$s</p></div>',
		esc_url( hu_first_assessment_url() ),
		esc_attr( $track_action ),
		esc_attr( $section ),
		esc_html( hu_first_assessment_text( 'cta' ) ),
		esc_html( hu_first_assessment_text( 'cta_note' ) )
	);
};

if ( function_exists( 'hu_enqueue_css' ) ) {
	hu_enqueue_css( 'hu-navigation-ecosystem', 'navigation-ecosystem.css', [ 'nexus-system-css' ] );
	hu_enqueue_css( 'nexus-startseite-css', 'startseite.css', [ 'nexus-system-css' ] );
	hu_enqueue_css( 'nexus-startseite-rest-css', 'startseite-rest-v1.css', [ 'nexus-startseite-css' ] );
}
if ( function_exists( 'hu_enqueue_js' ) ) {
	hu_enqueue_js( 'hu-navigation-ecosystem', 'navigation-ecosystem.js', [] );
	hu_enqueue_js( 'hu-home-feld', 'home-feld.js', [] );
	hu_enqueue_js( 'hu-home-uebergabe', 'home-uebergabe.js', [] );
}
get_header();
?>

<div class="doku startseite" id="top" data-track-section="homepage">
	<div class="blatt kopfteil">
		<div class="home-hero home-hero-v9">
			<div class="home-hero-copy">
				<p class="gegenstand">WordPress&nbsp;· Tracking&nbsp;· CRM&nbsp;· Pattensen&nbsp;bei&nbsp;Hannover</p>
				<h1><span class="home-title-primary">WordPress Freelancer Hannover.</span><span class="home-title-secondary">Von der Website<br>bis zur Anfrage<span class="home-title-stop">.</span></span></h1>
				<p class="aufriss">Ich entwickle WordPress-Websites mit technischem SEO und sauberer Messung – damit Angebote verständlich werden und Anfragen bis ins CRM ankommen. Direkt mit mir, ohne Übergabe an ein fremdes Entwicklerteam.</p>
				<div class="ausgang">
					<?php echo $first_assessment_cta( 'hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?><a class="<?php echo esc_attr( $project_cta_class ); ?>" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_head_contact" data-track-category="lead_gen" data-track-section="hero">Projekt anfragen <span aria-hidden="true">→</span></a>
				</div>
				<?php // Zwei Buttons, keine dritte Wahl: Der Sprung zu den Leistungen trägt jetzt den Preisanker und behält seinen Hook. ?>
				<ul class="home-trust-row">
					<li><?php echo esc_html( $response_short ); ?></li>
					<li><a class="satzlink" href="#angebote" data-track-action="home_hero_to_offers" data-track-category="navigation" data-track-section="hero">Website-Projekte ab <?php echo esc_html( $website_price ); ?></a></li>
				</ul>
				<?php // Weiche im ersten Blick: Agenturen und Energy-Betriebe haben eigene Wege. Hooks unverändert. ?>
				<div class="home-doors" id="wege" data-track-section="tueren">
					<p><span class="mono">Für Agenturen</span><a class="satzlink" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="home_door_whitelabel" data-track-category="navigation" data-track-section="tueren">White-Label: Umsetzung unter Ihrem Namen →</a></p>
					<p><span class="mono">Solar &amp; Wärmepumpe</span><a class="satzlink" href="<?php echo esc_url( $energy_url ); ?>" data-track-action="home_door_energy" data-track-category="navigation" data-track-section="tueren">Anfragesystem und Marktcheck →</a></p>
				</div>
				<figure class="home-portrait">
					<img src="<?php echo esc_url( $portrait_url ); ?>" width="480" height="600" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" decoding="async">
					<figcaption><strong>Direkt mit Haşim Üner.</strong><span>Konzeption · Entwicklung · Übergabe</span><a class="satzlink" href="<?php echo esc_url( $about_url ); ?>" data-track-action="home_about" data-track-category="trust" data-track-section="hero">Mehr über Haşim</a></figcaption>
				</figure>
			</div>
			<figure class="home-feld" data-home-feld data-home-feld-quellen="<?php echo esc_attr( implode( '|', $flow_sources ) ); ?>" aria-labelledby="home-feld-caption">
				<div class="home-feld__tafel" role="img" aria-label="<?php echo esc_attr( $flow_label ); ?>">
					<?php echo $feld_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the theme, generated by scripts/build-home-feld-svg.py. ?>
					<canvas class="home-feld__funken" aria-hidden="true"></canvas>
					<p class="home-feld__marke" aria-hidden="true"><span class="home-feld__marke-titel">Neu im CRM</span><span class="home-feld__marke-quelle"><span class="home-feld__marke-praefix">Quelle: </span><b data-home-feld-quelle><?php echo esc_html( $flow_sources[0] ); ?></b></span></p>
				</div>
				<ol class="home-feld__achse" aria-hidden="true"><li>Website</li><li>Anfrage</li></ol>
				<figcaption class="home-feld__caption">
					<span id="home-feld-caption">Beispiel: Jede Anfrage kommt mit ihrer Quelle im CRM an.</span>
					<button class="home-feld__schalter" type="button" aria-label="Bewegung anhalten" data-home-feld-schalter hidden><svg class="home-feld__symbol-halt" viewBox="0 0 12 12" aria-hidden="true" focusable="false"><path d="M3 2h2v8H3zM7 2h2v8H7z"></path></svg><svg class="home-feld__symbol-weiter" viewBox="0 0 12 12" aria-hidden="true" focusable="false"><path d="M3 1.5 10.5 6 3 10.5z"></path></svg><span data-home-feld-schalter-text>Anhalten</span></button>
				</figcaption>
			</figure>
		</div>
		<?php
		// Belege direkt unter dem Hero statt einer zweiten Fassung der Hero-Zusagen.
		// Jede Zelle springt zu ihrem Nachweis in Abschnitt 02; Zahlen aus dem Kanon.
		$reference_count = count( $references );
		?>
		<div class="meta home-proof" id="einordnung" data-track-section="belege">
			<dl>
				<div class="home-proof__case"><dt>Dokumentierter B2B-Fall</dt><dd><a href="#systemprojekt" data-track-action="home_proof_strip_case" data-track-category="proof" data-track-section="belege"><strong><?php echo esc_html( $e3_metric( 'cpl_reduction' ) ); ?></strong> weniger Kosten pro Anfrage, <strong><?php echo esc_html( $e3_metric( 'lead_count' ) ); ?></strong> qualifizierte Anfragen in <?php echo esc_html( $e3_metric( 'timeframe', 'display_dative' ) ); ?></a></dd></div>
				<?php if ( $reference_count ) : ?>
					<div><dt>Öffentliche Arbeiten</dt><dd><a href="#projekte" data-track-action="home_proof_strip_references" data-track-category="proof" data-track-section="belege"><strong><?php echo esc_html( $reference_count . ( 1 === $reference_count ? ' Website' : ' Websites' ) ); ?></strong> zum Nachprüfen</a></dd></div>
				<?php endif; ?>
				<div><dt>Diese Website</dt><dd><a href="#pruefstand" data-track-action="home_proof_strip_code" data-track-category="proof" data-track-section="belege"><strong>Offener Code</strong> mit automatischen Prüfungen</a></dd></div>
			</dl>
		</div>
	</div>

	<nav class="home-index hu-page-toc home-toc" aria-label="Auf dieser Seite" data-hu-rail="true" data-track-section="page_toc">
		<span class="home-toc__mark" aria-hidden="true"><span class="home-toc__mark-short">TOC</span><span class="home-toc__mark-full">Auf dieser Seite</span></span>
		<div class="home-toc__entries">
			<a href="#angebote" data-track-action="toc_angebote" data-track-category="navigation"><span class="home-toc__nr">01</span><span class="home-toc__txt">Leistungen</span></a>
			<a href="#nachweis" data-track-action="toc_nachweis" data-track-category="navigation"><span class="home-toc__nr">02</span><span class="home-toc__txt">Arbeiten</span></a>
			<a href="#arbeitsweise" data-track-action="toc_arbeitsweise" data-track-category="navigation"><span class="home-toc__nr">03</span><span class="home-toc__txt">Zusammenarbeit</span></a>
			<a href="#fragen" data-track-action="toc_fragen" data-track-category="navigation"><span class="home-toc__nr">04</span><span class="home-toc__txt">Fragen</span></a>
			<a href="#anfrage" data-track-action="toc_anfrage" data-track-category="lead_gen"><span class="home-toc__nr">05</span><span class="home-toc__txt">Anfrage</span></a>
		</div>
	</nav>

	<section id="angebote" aria-labelledby="angebote-h" data-track-section="angebote">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">01</span><span class="titel">Leistungen</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="angebote-h">Drei Kernbereiche. Ein Verantwortungsweg.</h2>
				<p class="vorspann">Website, Anfrage und Messung werden nicht als getrennte Gewerke behandelt. Sie buchen nur den Teil, den Ihr Projekt tatsächlich braucht.</p>
				<div class="home-offers home-offers-v2">
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
				<p class="home-note">Preis und Umfang werden vor Projektstart konkret festgelegt. Migrationen, Inhalte, zusätzliche Integrationen, Hosting, Lizenzen und Werbebudget werden nur dann Teil des Projekts, wenn sie ausdrücklich vereinbart sind.</p>
			</div>
		</div>
	</section>

	<section id="nachweis" aria-labelledby="nachweis-h" data-track-section="beweis">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">02</span><span class="titel">Arbeiten</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll">
				<h2 class="kopf" id="nachweis-h">Ausgewählte Arbeiten.</h2>
				<p class="vorspann">WordPress-Projekte und ein dokumentierter B2B-Fall über die komplette Strecke bis ins CRM.</p>

				<article class="home-featured-case home-featured-case-v2" id="systemprojekt">
					<div class="home-featured-case__intro">
						<div>
							<p class="mono">Ausgewähltes Großprojekt · B2B · Solar</p>
							<h3>Vom ersten Klick bis zum Vertriebsanschluss.</h3>
						</div>
						<p>Für einen <?php echo esc_html( $e3_case_label_accusative ); ?> entstand eine durchgehende Strecke aus Website, Qualifizierung, Messung, Server-Side Tracking, CRM und Vertriebsübergabe.</p>
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
						<article><p class="mono"><?php echo esc_html( $reference['tag'] ); ?></p><h4><a class="satzlink" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="home_reference_open" data-track-category="proof" data-track-section="beweis"><?php echo esc_html( $reference['name'] ); ?> ↗</a></h4><p><?php echo esc_html( $reference['text'] ); ?></p></article>
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
				<h2 class="kopf" id="arbeitsweise-h">Klarer Scope. Sichtbarer Stand. Saubere Übergabe.</h2>
				<p class="vorspann" id="position">Ich übernehme die technische Umsetzung selbst. Sie wissen vor dem Start, was gebaut wird, sehen den Stand vor der Veröffentlichung und erhalten am Ende einen nachvollziehbaren Übergabepunkt.</p>
				<ol class="home-process home-process-v2" id="ablauf">
					<li><span class="mono">01 · Klären</span><h3>Was wird gebaut – und was nicht?</h3><p>Wir prüfen Ausgangslage, Ziel und Abhängigkeiten. Daraus entstehen Scope, Preis und Zeitrahmen. Zusätzliche Wünsche werden nicht stillschweigend Teil des Projekts.</p></li>
					<li><span class="mono">02 · Bauen</span><h3>Am echten Stand arbeiten.</h3><p>Sie sehen die Umsetzung auf einer Testumgebung. Änderungen bleiben nachvollziehbar und technische Rückfragen landen direkt bei mir.</p></li>
					<li><span class="mono">03 · Prüfen & übergeben</span><h3>Die wichtigen Wege gemeinsam abnehmen.</h3><p>Vor dem Livegang prüfen wir die vereinbarten Funktionen. Danach erhalten Sie Dokumentation, Zugänge und den Stand, mit dem Ihr Team weiterarbeiten kann.</p></li>
				</ol>
				<figure class="home-uebergabe" data-home-uebergabe aria-labelledby="home-uebergabe-titel">
					<div class="home-uebergabe__kopf">
						<p class="mono">Übergabe · Beispiel</p>
						<h3 id="home-uebergabe-titel">Was bei Ihnen liegt, wenn das Projekt abgeschlossen ist.</h3>
					</div>
					<ol class="home-uebergabe__liste">
						<?php foreach ( $handover as $item ) : ?>
							<li><span class="home-uebergabe__haken" aria-hidden="true"></span><strong><?php echo esc_html( $item[0] ); ?><?php if ( $item[2] ) : ?> <em>falls beauftragt</em><?php endif; ?></strong><span><?php echo esc_html( $item[1] ); ?></span></li>
						<?php endforeach; ?>
					</ol>
					<p class="home-uebergabe__stempel" aria-hidden="true"><span>Übergeben</span><small>nach Abnahme</small></p>
					<figcaption>Beispiel. Was genau dazugehört, legen wir vor dem Start im Scope fest.</figcaption>
				</figure>
				<div class="home-fit home-fit-v2" id="eignung"><h3 id="vergleich">Passt, wenn Verantwortung klar sein soll.</h3><div><p>Sie brauchen einen direkten technischen Ansprechpartner und intern jemanden für Inhalte und Freigaben. Tracking oder CRM werden nur dann mit eingeplant, wenn sie Teil der Aufgabe sind.</p></div></div>
			</div>
		</div>
	</section>

	<section id="fragen" aria-labelledby="fragen-h" data-track-section="fragen">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel"><span class="nr">04</span><span class="titel">Fragen</span><span class="strich" aria-hidden="true"></span></div></div>
			<div class="voll"><h2 class="kopf" id="fragen-h">Was vor dem Start geklärt sein sollte.</h2><div class="fragen">
				<?php foreach ( $faqs as $faq ) : ?>
					<details name="home-faq"><summary><?php echo esc_html( $faq['q'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $faq['a'] ); ?></p></div></div></details>
				<?php endforeach; ?>
			</div></div>
		</div>
		<?php
		// inc/org-schema.php unterdrueckt den Editor-FAQ-Cache auf der Startseite,
		// weil die Fragen hier template-owned sind. Der Knoten wird deshalb hier
		// gebaut — aus demselben Array wie die sichtbaren Antworten, damit Text
		// und Schema nicht auseinanderlaufen koennen.
		$faq_schema = [
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'@id'        => home_url( '/' ) . '#faq',
			'url'        => home_url( '/' ),
			'inLanguage' => 'de',
			'publisher'  => [ '@id' => home_url( '/#organization' ) ],
			'mainEntity' => [],
		];
		foreach ( $faqs as $faq ) {
			$faq_schema['mainEntity'][] = [
				'@type'          => 'Question',
				'name'           => $faq['q'],
				'acceptedAnswer' => [
					'@type' => 'Answer',
					'text'  => $faq['a'],
				],
			];
		}
		?>
		<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	</section>

	<?php
	// Abschluss: beide Wege als Karten mit demselben Aufbau wie die Einstiege der
	// anderen Geschäftswege (Test-Sprint, Marktcheck). Ziele und Hooks unverändert;
	// ohne Versuch bleibt nur die Projektkarte. Texte der Ersteinschätzung aus dem Kanon.
	$portrait_close_url = get_stylesheet_directory_uri() . '/assets/img/hasim-freelancer-portrait-480x600.webp';
	?>
	<div class="abschluss" id="anfrage" data-track-section="abschluss" tabindex="-1">
		<div class="blatt" id="kontakt"><div class="tafel home-close home-close-v2">
			<p class="mono">Der nächste Schritt</p>
			<h2>Was soll als Nächstes besser funktionieren?</h2>
			<p class="aufriss"><?php echo $first_assessment_on ? 'Zwei Wege, beide landen direkt bei mir – ohne Vertriebsübergabe und ohne fertiges Briefing.' : 'Schicken Sie mir Ausgangslage, Engpass und Ziel. Ich prüfe die Aufgabe selbst und melde mich mit einer ersten fachlichen Einordnung – ohne Vertriebsübergabe und ohne fertiges Briefing.'; ?></p>
			<figure class="home-close__portrait">
				<img src="<?php echo esc_url( $portrait_close_url ); ?>" width="480" height="600" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" loading="lazy" decoding="async">
				<figcaption><strong>Sie schreiben direkt mir.</strong><span>Haşim Üner · Pattensen bei Hannover</span></figcaption>
			</figure>
			<div class="home-einstiege">
				<?php if ( $first_assessment_on ) : ?>
					<article class="home-einstieg home-einstieg--erst">
						<p class="mono"><?php echo esc_html( hu_first_assessment_text( 'label' ) ); ?></p>
						<h3><?php echo esc_html( hu_first_assessment_text( 'card_title' ) ); ?></h3>
						<dl>
							<div><dt>Sie schicken</dt><dd><?php echo esc_html( hu_first_assessment_text( 'card_send' ) ); ?></dd></div>
							<div><dt>Sie bekommen</dt><dd><?php echo esc_html( hu_first_assessment_text( 'card_get' ) ); ?></dd></div>
							<div><dt>Antwort</dt><dd><?php echo esc_html( $response_short ); ?></dd></div>
						</dl>
						<a class="tun" href="<?php echo esc_url( hu_first_assessment_url() ); ?>" data-track-action="home_close_ersteinschaetzung" data-track-category="lead_gen" data-track-section="abschluss"><?php echo esc_html( hu_first_assessment_text( 'cta' ) ); ?> <span aria-hidden="true">→</span></a>
					</article>
				<?php endif; ?>
				<article class="home-einstieg">
					<p class="mono">Projektanfrage</p>
					<h3>Scope, Preis und Zeitrahmen.</h3>
					<dl>
						<div><dt>Sie schicken</dt><dd>Ausgangslage, Engpass und Ziel</dd></div>
						<div><dt>Sie bekommen</dt><dd>Eine fachliche Einordnung, danach Scope, Preis und Zeitrahmen</dd></div>
						<div><dt>Antwort</dt><dd><?php echo esc_html( $response_short ); ?></dd></div>
					</dl>
					<a class="<?php echo esc_attr( $project_cta_class ); ?>" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_close_contact" data-track-category="lead_gen" data-track-section="abschluss">Projekt beschreiben <span aria-hidden="true">→</span></a>
				</article>
			</div>
			<p class="home-close__mail">Lieber per E-Mail? <a class="satzlink" href="<?php echo esc_url( 'mailto:' . $contact_email, [ 'mailto' ] ); ?>" data-track-action="home_close_mail" data-track-category="lead_gen" data-track-section="abschluss"><?php echo esc_html( $contact_email ); ?></a></p>
		</div></div>
	</div>
</div>
<?php get_footer(); ?>