<?php
/**
 * Homepage: canonical WordPress Freelancer money page.
 *
 * Die Seite ist die Strecke, die sie verkauft: Eine Messlinie laeuft in der
 * Randspalte vom Hero bis zum Anfrageblock und fuellt sich beim Lesen. Oben
 * protokolliert ein Panel diesen Besuch mit Werten, die der Browser selbst
 * misst; startseite-strecke.js sendet und speichert davon nichts (erzwungen
 * ueber scripts/canon-forbidden-values.txt, Regel strecke-js-privat).
 *
 * Ohne JavaScript steht alles: Linie statisch, alle Stationen offen, das
 * Protokoll sagt, dass es leer bleibt.
 *
 * Fakten (Preise, Antwortzeit, Kontakt, Fallzahlen, Referenzen) kommen aus
 * inc/canon/. Hier steht keine Zahl als Text.
 *
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes         = hu_get_commercial_route_map();
$contact_url    = $routes['project_request'];
$whitelabel_url = $routes['whitelabel'];
$energy_url     = $routes['energy'];
$tracking_url   = $routes['tracking_setup'];
$about_url      = $routes['about'];
$privacy_url    = home_url( '/datenschutz/#privacy-cookies' );
$github_url     = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514';
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/' ) );
$img_uri        = get_stylesheet_directory_uri() . '/assets/img/';

// Betrag und Einheit bleiben in einer Zeile: "490" am Zeilenende und "€" am
// naechsten Anfang liest sich als zwei Angaben.
$fest           = static function ( $value ) {
	return str_replace( [ ' €', ' %' ], [ "\u{00A0}€", "\u{00A0}%" ], (string) $value );
};
$response_short = hu_response_promise_short();
$website_price  = $fest( hu_freelancer_website_price() );
// Tracking-Angebot = Stufe 1 der Tracking-Leiter; die Stufen darueber stehen
// als ein Satz daneben (hu_tracking_ladder_display()), nie einzeln abgeschrieben.
$tracking_offer = hu_tracking_product_ladder()['measurement'];
$tracking_price = $fest( $tracking_offer['price'] );
$takeover_price = $fest( hu_freelancer_takeover_check_price() );
$retainer       = $fest( hu_freelancer_retainer_display() );
$references     = hu_public_reference_projects();
$reference_n    = count( $references );
$count_words    = [ 1 => 'Eine', 2 => 'Zwei', 3 => 'Drei', 4 => 'Vier', 5 => 'Fünf', 6 => 'Sechs' ];
$reference_word = $count_words[ $reference_n ] ?? (string) $reference_n;

$e3          = hu_e3_canon();
$e3_case_url = $e3['url'];
// Minuszeichen U+2212, nicht der Bindestrich: Die Zahl ist ein Messwert.
$cpl_drop    = $fest( sprintf( '−%s %%', hu_e3_metric( 'cpl_reduction', 'value' ) ) );

// Grosse Mono-Zahlen: Die Einheit steht kleiner und ohne Monospace-Luecke
// daneben. Gibt escaptes HTML zurueck.
$zahl = static function ( $value ) {
	if ( preg_match( '/^(.*?)[\s\x{00A0}]*(%|€)$/u', (string) $value, $teile ) ) {
		return esc_html( $teile[1] ) . '<span class="st-einheit">' . esc_html( $teile[2] ) . '</span>';
	}
	return esc_html( (string) $value );
};

// Mono-Listen mit Trennpunkt: Der Punkt haengt am vorigen Wort, damit keine
// Zeile mit "·" beginnt.
$liste = static function ( $value ) {
	return str_replace( ' · ', "\u{00A0}· ", (string) $value );
};

$project_link = static function ( $focus ) {
	return hu_get_contact_intake_url( 'project', $focus );
};

/*
 * Versuch "Kostenlose Ersteinschaetzung" (Schalter und Texte im Kanon).
 * An: Ersteinschaetzung ist der primaere Button, die Projektanfrage steht
 * sekundaer daneben. Aus: Die Projektanfrage ist der einzige, primaere Button.
 */
$first_assessment_on = hu_first_assessment_enabled();
$project_cta_class   = $first_assessment_on ? 'tun still' : 'tun';

/*
 * Sechs Stationen einer Anfrage. Dieselben Namen wie die Marken auf der
 * Linie. Jede Station: Bruchstelle in einem Satz, dann was dort bricht,
 * was ich baue und, wo es einen gibt, der Beleg auf dieser Seite.
 */
$stations = [
	[
		'slug'   => 'klick',
		'name'   => 'Klick',
		'teaser' => 'Die Seite wird gefunden, nur für die falsche Suche.',
		'bricht' => 'Die Website rankt für Begriffe, nach denen Ihre Kunden nicht suchen. Zwei Unterseiten konkurrieren um dieselbe Suchanfrage, und keine kommt nach vorn. Nach einem Relaunch ohne Weiterleitungen laufen alte Links und Suchergebnisse ins Leere.',
		'baue'   => 'Eine Seite pro Suchanfrage, saubere Titel und Canonicals, strukturierte Daten und einen Weiterleitungsplan für jede alte URL.',
		'beleg'  => [ 'text' => 'Titel, Canonical und Schema dieser Seite prüft die CI bei jeder Änderung.', 'url' => '#pruefstand', 'label' => 'Zum Prüfstand', 'hook' => 'home_station_pruefstand' ],
	],
	[
		'slug'   => 'seite',
		'name'   => 'Seite',
		'teaser' => 'Das Angebot steht zu weit unten.',
		'bricht' => 'Die Seite braucht auf dem Handy mehrere Sekunden. Das Angebot steht unter drei Absätzen Einleitung, ein Preis fehlt. Wer zwei Anbieter vergleicht, liest dort weiter, wo die Antwort schneller kommt.',
		'baue'   => 'WordPress-Seiten, die auf dem Handy schnell stehen. Angebot, Preis und Beleg kommen in dieser Reihenfolge. Die Inhalte pflegt Ihr Team selbst im Editor.',
		'beleg'  => [ 'lcp' => true ],
	],
	[
		'slug'   => 'formular',
		'name'   => 'Formular',
		'teaser' => 'Das Formular sendet, die Mail kommt nicht an.',
		'bricht' => 'Das Formular fragt mehr ab, als für eine erste Antwort nötig ist. Oder es sendet, aber die Mail landet im Spam, weil der Server nicht als Absender berechtigt ist.',
		'baue'   => 'Formulare mit so wenigen Pflichtfeldern wie möglich und Fehlermeldungen direkt am Feld. Der Versand ist über SPF und DKIM abgesichert, und die Danke-Seite sagt, was als Nächstes passiert.',
		'beleg'  => [],
	],
	[
		'slug'   => 'messung',
		'name'   => 'Messung',
		'teaser' => 'Die Zahl in GA4 passt nicht zum Postfach.',
		'bricht' => 'GA4 zählt jeden Aufruf der Danke-Seite als Conversion, auch das Neuladen. Ohne Einwilligung fehlen Daten, und niemand weiß, wie viele. Welche Kampagne eine Anfrage gebracht hat, lässt sich dann nicht mehr sagen.',
		'baue'   => sprintf( 'Einen Messplan mit GA4, Google Tag Manager und Consent Mode. Jede Conversion wird mit einem Testfall abgenommen und protokolliert. %s ab %s netto.', $tracking_offer['name'], $tracking_price ),
		'beleg'  => [ 'url' => $tracking_url, 'label' => 'Tracking-Setup im Detail', 'hook' => 'home_proof_tracking_page' ],
	],
	[
		'slug'   => 'crm',
		'name'   => 'CRM',
		'teaser' => 'Die Anfrage kommt an, ihre Quelle nicht.',
		'bricht' => 'Die Anfrage landet als E-Mail in einem Sammelpostfach. Die Quelle fehlt, der Vertrieb tippt sie ab, und ob jemand zurückgerufen hat, steht nirgends.',
		'baue'   => 'Die Übergabe jeder Anfrage mit Quelle, Kampagne und Formularinhalt in Ihr CRM. Vor dem Livegang verfolge ich eine Testanfrage bis dorthin.',
		'beleg'  => [ 'text' => 'Im dokumentierten B2B-Fall reicht die Strecke bis zur Übergabe an den Vertrieb.', 'url' => '#arbeiten', 'label' => 'Zum Fall', 'hook' => 'home_station_fall' ],
	],
	[
		'slug'   => 'anfrage',
		'name'   => 'Anfrage',
		'teaser' => 'Niemand weiß, wann eine Antwort kommt.',
		'bricht' => 'Nach dem Absenden kommt eine Standardmail ohne Termin. Wer nichts hört, schreibt den nächsten Anbieter an.',
		'baue'   => 'Eine Bestätigung, die sagt, wer die Anfrage liest und bis wann eine Antwort kommt.',
		'beleg'  => [ 'text' => sprintf( 'Meine eigene Zusage: Antwort %s.', $response_short ), 'url' => '#anfrage', 'label' => 'Zum Ende der Strecke', 'hook' => 'home_station_anfrage' ],
	],
];

/*
 * Drei Leistungen mit Kanonpreis. Die ids sind die Anker der Angebote im
 * Service-Schema (inc/schema-positioning.php); scripts/lint-entity-crawler-signals.php
 * prueft, dass jeder Schema-Anker hier als 'id' => '…' steht.
 */
$offers = [
	[
		'id' => 'angebot-website',
		'nr'    => '01',
		'tags'  => 'Website · Relaunch · Landingpages · technisches SEO',
		'title' => 'WordPress-Website und Relaunch',
		'price' => sprintf( 'ab %s netto', $website_price ),
		'text'  => 'Neubau oder Relaunch auf WordPress, gebaut auf einer Testumgebung und erst nach Ihrer Abnahme live. Beim Relaunch bekommt jede alte URL eine Weiterleitung, damit bestehende Links und Suchergebnisse weiter ankommen.',
		'more'  => $fest( hu_freelancer_website_scope_display() ) . '.',
		'scope' => 'Seitenstruktur · Entwicklung · Landingpages · Weiterleitungsplan · Title, Canonical, Schema · Dokumentation',
		'focus' => 'relaunch',
		'hook'  => 'home_offer_relaunch',
		'cta'   => 'Website-Projekt anfragen',
	],
	[
		'id' => 'angebot-tracking',
		'nr'    => '02',
		'tags'  => 'GA4 · Google Tag Manager · Consent Mode · Server-Side · CRM',
		'title' => $tracking_offer['name'],
		'price' => sprintf( 'Festpreis %s netto', $tracking_price ),
		'text'  => 'Ein Messplan, sauber eingerichtete Conversions und ein Abnahmeprotokoll mit Testfällen. Gemessen wird im Browser, ohne eigenen Server. Einen Server-Endpunkt oder die Übergabe ins CRM brauchen Sie nur, wenn das eigentliche Problem dort liegt.',
		'more'  => sprintf( 'Die Stufen darüber: %s.', $fest( hu_tracking_ladder_display( 2 ) ) ),
		'scope' => 'Messplan · GTM und GA4 · Consent Mode · Google Ads · Abnahmeprotokoll',
		'focus' => 'tracking',
		'hook'  => 'home_offer_tracking',
		'cta'   => 'Tracking-Projekt anfragen',
	],
	[
		'id' => 'angebot-weiterentwicklung',
		'nr'    => '03',
		'tags'  => 'Übernahme · Pflege · Weiterentwicklung',
		'title' => 'Bestehende Website übernehmen',
		'price' => sprintf( 'Übernahme-Check %s netto', $takeover_price ),
		'text'  => 'Ich prüfe Theme, Plugins, Updates, Backups, Zugänge und Ladezeit. Sie bekommen einen schriftlichen Befund mit Festpreis für den nächsten Schritt. Beauftragen Sie mich danach, wird der Check verrechnet. Der Befund gehört Ihnen, auch wenn Sie mit jemand anderem weiterarbeiten.',
		'more'  => sprintf( 'Danach Weiterentwicklung im Monatskontingent: %s, monatlich kündbar.', $retainer ),
		'scope' => '',
		'focus' => 'implementation_scope',
		'hook'  => 'home_offer_takeover',
		'cta'   => 'Übernahme-Check anfragen',
	],
];

$handover = [
	[ 'Testumgebung', 'Der abgenommene Stand vor dem Livegang. Jede spätere Änderung wird dort zuerst geprüft.', false ],
	[ 'Code und Repository', 'In Ihrem Account, mit jeder Änderung und ihrem Grund.', false ],
	[ 'Zugänge und Konten', 'Domain, Hosting und jeder eingesetzte Dienst laufen auf Ihren Namen.', false ],
	[ 'Dokumentation', 'Aufbau, Pflege und offene Punkte, geschrieben für den nächsten Entwickler.', false ],
	[ 'Tracking-Plan', 'Jedes Event mit Auslöser und Consent-Verhalten, in GA4 geprüft.', true ],
	[ 'Testanfrage im CRM', 'Eine Anfrage mit ihrer Quelle bis ins CRM verfolgt und protokolliert.', true ],
];

$faqs = [
	[
		'q' => 'Übernehmen Sie eine bestehende WordPress-Website?',
		'a' => sprintf( 'Ja, nach einem Übernahme-Check für %s netto. Ich prüfe Theme, Plugins, Updates, Backups, Zugänge und Ladezeit und schicke Ihnen den Befund schriftlich, mit einem Festpreis für den nächsten Schritt. Beauftragen Sie mich danach, verrechne ich den Check. Der Befund gehört Ihnen, auch wenn Sie mit jemand anderem weiterarbeiten.', $takeover_price ),
	],
	[
		'q' => 'Müssen wir unser Theme oder unseren Page Builder ersetzen?',
		'a' => 'Nur wenn er Ladezeit, Pflege oder eine Anbindung nachweisbar blockiert. Das steht dann mit Begründung im Befund. Trägt der vorhandene Aufbau, baue ich darauf weiter.',
	],
	[
		'q' => 'Wie lange dauert ein Projekt, und was brauchen Sie von uns?',
		'a' => 'Den Zeitrahmen nenne ich mit dem Angebot. Darin steht auch, wann ich Texte, Bilder und Freigaben von Ihnen brauche. Auf Ihrer Seite braucht es eine Person, die entscheidet und freigibt.',
	],
	[
		'q' => 'Gehören Website, Konten und Code danach uns?',
		'a' => sprintf( 'Ja, von Anfang an. Domain, Hosting, Konten und Repository laufen auf Ihren Namen. Wer danach weiterentwickeln lassen will, bucht ein Kontingent: %s, monatlich kündbar. Voraussetzung ist das nicht.', $retainer ),
	],
	[
		'q' => 'Was passiert, wenn Sie ausfallen?',
		'a' => 'Dann sage ich es Ihnen am selben Tag. Zugänge und Code liegen bei Ihnen, und die Dokumentation ist für einen anderen Entwickler geschrieben. Ihre Website hängt also nicht an mir.',
	],
	[
		'q' => 'Was misst das Protokoll oben auf dieser Seite?',
		'a' => 'Ihr Browser misst die Ladezeit dieses Aufrufs, welche Abschnitte Sie gesehen haben, wie weit Sie gescrollt haben und ob Sie auf einen Anfrage-Button geklickt haben. Die Werte bleiben in Ihrem Browser und verschwinden, wenn Sie den Tab schließen. Gesendet oder gespeichert wird davon nichts. Die Website selbst zählt nur den Seitenaufruf, ohne Cookie.',
	],
];

/*
 * Mono-Marke eines Abschnitts auf der Linie. Die Linie selbst ist
 * aria-hidden; die Marke steht als Text im Dokument und traegt Nummer und
 * Namen fuer alle.
 */
$marke = static function ( $nr, $name ) {
	return sprintf(
		'<div class="st-rail" aria-hidden="true"><span class="st-rail__fuellung" data-st-fuellung></span><span class="st-rail__punkt"></span></div><p class="st-marke"><span class="st-marke__nr">%1$s</span> <span class="st-marke__name">%2$s</span></p>',
		esc_html( $nr ),
		esc_html( $name )
	);
};

hu_enqueue_css( 'nexus-startseite-strecke-css', 'startseite-strecke.css', [ 'nexus-system-css' ] );
hu_enqueue_js( 'nexus-startseite-strecke-js', 'startseite-strecke.js', [] );
get_header();
?>

<div class="doku st" id="top" data-track-section="homepage" data-st>

	<section class="st-abschnitt st-hero" id="klick" aria-labelledby="st-h1" data-st-abschnitt="01">
		<?php echo $marke( '01', 'Klick' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt st-hero__raster">
			<?php // Jeder Satz der H1 umbricht fuer sich; der zweite traegt den Beleg und steht leiser. ?>
			<h1 class="st-hero__h1" id="st-h1"><span class="st-hero__h1-satz">Mehr Anfragen über Ihre Website.</span> <span class="st-hero__h1-satz st-hero__h1-satz--leise">Und Sie sehen, woher jede kommt.</span></h1>
			<div class="st-hero__text">
				<?php // Metazeile als Byline unter der H1: So passen H1, Satz, Buttons und Protokoll bei 1280 x 800 in den ersten Bildschirm. ?>
				<div class="st-hero__meta">
					<img class="st-hero__portrait" src="<?php echo esc_url( $img_uri . 'hasim-freelancer-portrait-112.webp' ); ?>" width="56" height="56" alt="" decoding="async" fetchpriority="low">
					<p class="st-hero__metazeile">WordPress Freelancer für Unternehmen <span aria-hidden="true">·</span> <a href="<?php echo esc_url( $about_url ); ?>" data-track-action="home_about" data-track-category="trust" data-track-section="hero">Haşim Üner</a> <span aria-hidden="true">·</span> Pattensen&nbsp;bei&nbsp;Hannover</p>
				</div>
				<p class="st-hero__satz">Ich baue WordPress-Websites, die gefunden werden, und verfolge jede Anfrage vom ersten Klick bis in Ihr CRM.</p>
				<div class="st-hero__ctas">
					<?php if ( $first_assessment_on ) : ?>
						<a class="tun" href="<?php echo esc_url( hu_first_assessment_url() ); ?>" data-track-action="home_head_ersteinschaetzung" data-track-category="lead_gen" data-track-section="hero"><?php echo esc_html( hu_first_assessment_text( 'cta' ) ); ?> <span class="pf" aria-hidden="true">→</span></a>
					<?php endif; ?>
					<a class="<?php echo esc_attr( $project_cta_class ); ?>" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_head_contact" data-track-category="lead_gen" data-track-section="hero">Projekt anfragen <span class="pf" aria-hidden="true">→</span></a>
				</div>
				<?php if ( $first_assessment_on ) : ?>
					<p class="st-hero__notiz"><?php echo esc_html( hu_first_assessment_text( 'cta_note' ) ); ?></p>
				<?php endif; ?>
			</div>

			<aside class="st-protokoll" id="protokoll" aria-labelledby="st-protokoll-titel" data-st-protokoll>
				<p class="st-protokoll__kopf"><span id="st-protokoll-titel">Protokoll · dieser Besuch</span><span class="st-protokoll__status" data-st-status hidden>läuft</span></p>
				<dl class="st-protokoll__werte">
					<div><dt data-st-lcp-label>Ladezeit (LCP)</dt><dd data-st-wert="lcp">…</dd></div>
					<div><dt>Abschnitte gesehen</dt><dd data-st-wert="abschnitte">…</dd></div>
					<div><dt>Scrolltiefe</dt><dd data-st-wert="tiefe">…</dd></div>
					<div><dt>Klick auf Anfrage</dt><dd data-st-wert="klick">…</dd></div>
				</dl>
				<ol class="st-protokoll__verlauf" aria-label="Verlauf" data-st-verlauf></ol>
				<p class="st-protokoll__ohne-js">Ohne JavaScript misst diese Seite nichts. Das Protokoll bleibt leer.</p>
				<p class="st-protokoll__fuss">So sieht saubere Messung aus. Diese Werte bleiben in Ihrem Browser. Die Website selbst zählt nur den Seitenaufruf, ohne Cookie. <a href="<?php echo esc_url( $privacy_url ); ?>" data-track-action="home_protocol_privacy" data-track-category="trust" data-track-section="hero">Datenschutz&nbsp;<span aria-hidden="true">→</span></a></p>
				<p class="nur-vorlesen" aria-live="polite" data-st-ansage></p>
			</aside>

			<ul class="st-belege" aria-label="Belege">
				<li><a href="#arbeiten" data-track-action="home_proof_strip_case" data-track-category="proof" data-track-section="hero"><span class="st-belege__zahl"><?php echo $zahl( $cpl_drop ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></span> <span class="st-belege__text">Kosten pro qualifizierter Anfrage in einem dokumentierten B2B-Fall</span></a></li>
				<?php if ( $reference_n ) : ?>
					<li><a href="#referenzen" data-track-action="home_proof_strip_references" data-track-category="proof" data-track-section="hero"><span class="st-belege__zahl"><?php echo esc_html( (string) $reference_n ); ?></span> <span class="st-belege__text"><?php echo esc_html( 1 === $reference_n ? 'öffentliche Website, die Sie selbst öffnen können' : 'öffentliche Websites, die Sie selbst öffnen können' ); ?></span></a></li>
				<?php endif; ?>
				<?php // Der erste Preis im ersten Bildschirm; Station 02 nennt den fehlenden Preis als Bruchstelle. ?>
				<li><a href="#angebote" data-track-action="home_proof_strip_price" data-track-category="proof" data-track-section="hero"><span class="st-belege__zahl"><?php echo $zahl( "ab\u{00A0}" . $website_price ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></span> <span class="st-belege__text">netto für eine WordPress-Website, alle Preise stehen auf dieser Seite</span></a></li>
			</ul>
		</div>
	</section>

	<section class="st-abschnitt st-pruefstand" id="pruefstand" aria-labelledby="pruefstand-h" data-st-abschnitt="02" data-track-section="pruefstand">
		<?php echo $marke( '02', 'Prüfstand' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<div class="tafel st-tafel">
				<div class="st-tafel__kopf">
					<h2 class="st-h2" id="pruefstand-h">Prüfen Sie mich, bevor Sie mir schreiben.</h2>
					<p class="st-vorspann">Diese Website ist mein offenster Arbeitsbeleg. Ihr Code liegt öffentlich, jede Änderung durchläuft vor dem Livegang automatische Prüfungen, und die Ladezeit messen Sie selbst.</p>
				</div>
				<ol class="st-pruefungen">
					<li>
						<p class="st-pruefungen__label">Quellcode</p>
						<p class="st-pruefungen__text">Jede Datei dieser Website und jede Änderung, mit Datum und Begründung.</p>
						<a class="st-link" href="<?php echo esc_url( $github_url . '/commits/main/' ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_github_history" data-track-category="proof" data-track-section="pruefstand">Code und Änderungen auf GitHub&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
					</li>
					<li>
						<p class="st-pruefungen__label">Prüfungen</p>
						<p class="st-pruefungen__text">Vor jedem Livegang prüft die CI unter anderem PHP-Syntax, statische Analyse, strukturierte Daten und eine Sperrliste veralteter Preise und Zusagen. Schlägt eine Prüfung fehl, geht nichts live.</p>
						<a class="st-link" href="<?php echo esc_url( $github_url . '/actions' ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_github_ci" data-track-category="proof" data-track-section="pruefstand">Prüfläufe auf GitHub&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
					</li>
					<li>
						<p class="st-pruefungen__label">Ladezeit</p>
						<p class="st-pruefungen__text">Eine Zahl von mir wäre nur eine Behauptung. PageSpeed Insights misst die Seite unter Laborbedingungen, jederzeit und ohne mich.</p>
						<p class="st-pruefungen__messung" data-st-nur-js hidden>Ihr Browser hat diesen Aufruf in <span class="st-messwert" data-st-lcp-kopie>…</span> dargestellt.</p>
						<a class="st-link" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_pagespeed" data-track-category="proof" data-track-section="pruefstand">PageSpeed jetzt messen&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
						<p class="st-pruefungen__klein">Laborwerte schwanken mit Uhrzeit und Serverlast.</p>
					</li>
				</ol>
			</div>
		</div>
	</section>

	<section class="st-abschnitt st-strecke" id="strecke" aria-labelledby="strecke-h" data-st-abschnitt="03" data-track-section="strecke">
		<?php echo $marke( '03', 'Strecke' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="strecke-h">Eine Anfrage passiert sechs Stationen. An jeder kann sie verloren gehen.</h2>
			<p class="st-vorspann">Anfragen, die nie ankommen, stehen in keinem Bericht. Zu jeder Station steht hier, was dort bricht und was ich dagegen baue.</p>
			<?php // #angebot-funnel: frueherer Anker des Angebots "Anfragestrecken", von CRO-Links im ganzen Theme verlinkt. ?>
			<ol class="st-stationen" id="angebot-funnel" data-st-stationen>
				<?php foreach ( $stations as $i => $station ) : ?>
					<?php $nr = sprintf( '%02d', $i + 1 ); ?>
					<li class="st-station" data-st-station>
						<h3 class="st-station__kopf" id="station-<?php echo esc_attr( $station['slug'] ); ?>" data-st-station-kopf data-st-ziel="station-<?php echo esc_attr( $station['slug'] ); ?>-detail"><span class="st-station__punkt" aria-hidden="true" data-st-punkt></span><span class="st-station__nr"><?php echo esc_html( $nr ); ?></span> <span class="st-station__name"><?php echo esc_html( $station['name'] ); ?></span> <span class="st-station__teaser"><?php echo esc_html( $station['teaser'] ); ?></span></h3>
						<div class="st-station__detail" id="station-<?php echo esc_attr( $station['slug'] ); ?>-detail" data-st-station-detail>
							<p class="st-station__label">Was bricht</p>
							<p><?php echo esc_html( $station['bricht'] ); ?></p>
							<p class="st-station__label">Was ich baue</p>
							<p><?php echo esc_html( $station['baue'] ); ?></p>
							<?php if ( ! empty( $station['beleg']['lcp'] ) ) : ?>
								<p class="st-station__beleg" data-st-nur-js hidden>Diese Seite stand in Ihrem Browser nach <span class="st-messwert" data-st-lcp-kopie>…</span>.</p>
							<?php elseif ( ! empty( $station['beleg'] ) ) : ?>
								<p class="st-station__beleg">
									<?php if ( ! empty( $station['beleg']['text'] ) ) : ?>
										<?php echo esc_html( $station['beleg']['text'] ); ?>
									<?php endif; ?>
									<a class="st-link" href="<?php echo esc_url( $station['beleg']['url'] ); ?>" data-track-action="<?php echo esc_attr( $station['beleg']['hook'] ); ?>" data-track-category="<?php echo esc_attr( 'home_proof_tracking_page' === $station['beleg']['hook'] ? 'proof' : 'navigation' ); ?>" data-track-section="strecke"><?php echo esc_html( $station['beleg']['label'] ); ?>&nbsp;<span aria-hidden="true">→</span></a>
								</p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="st-abschnitt st-leistungen" id="angebote" aria-labelledby="angebote-h" data-st-abschnitt="04" data-track-section="angebote">
		<?php echo $marke( '04', 'Leistungen' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="angebote-h">Was es kostet, steht hier.</h2>
			<p class="st-vorspann">Alle Preise netto. Umfang und Endpreis stehen vor dem Start schriftlich fest. Was danach dazukommt, kommt nur mit Ihrer Zustimmung dazu.</p>
			<div class="st-angebote">
				<?php foreach ( $offers as $offer ) : ?>
					<article class="st-angebot" id="<?php echo esc_attr( $offer['id'] ); ?>" aria-labelledby="<?php echo esc_attr( $offer['id'] ); ?>-h">
						<div class="st-angebot__kopf">
							<p class="st-angebot__tags"><span class="st-angebot__nr"><?php echo esc_html( $offer['nr'] ); ?></span> <?php echo esc_html( $liste( $offer['tags'] ) ); ?></p>
							<h3 class="st-angebot__titel" id="<?php echo esc_attr( $offer['id'] ); ?>-h"><?php echo esc_html( $offer['title'] ); ?></h3>
							<p class="st-angebot__preis"><?php echo esc_html( $offer['price'] ); ?></p>
						</div>
						<div class="st-angebot__text">
							<p><?php echo esc_html( $offer['text'] ); ?></p>
							<?php if ( '' !== $offer['more'] ) : ?>
								<p class="st-angebot__mehr"><?php echo esc_html( $offer['more'] ); ?></p>
							<?php endif; ?>
							<?php if ( '' !== $offer['scope'] ) : ?>
								<p class="st-angebot__umfang"><?php echo esc_html( $liste( $offer['scope'] ) ); ?></p>
							<?php endif; ?>
							<p class="st-angebot__wege">
								<a class="st-link st-link--stark" href="<?php echo esc_url( $project_link( $offer['focus'] ) ); ?>" data-track-action="<?php echo esc_attr( $offer['hook'] ); ?>" data-track-category="lead_gen" data-track-section="angebote"><?php echo esc_html( $offer['cta'] ); ?>&nbsp;<span aria-hidden="true">→</span></a>
							</p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
			<ul class="st-ausgaenge" aria-label="Andere Wege" data-track-section="tueren">
				<li><span class="st-ausgaenge__label">Für Agenturen und Webdesigner</span> <a class="st-link" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="home_door_whitelabel" data-track-category="navigation" data-track-section="tueren">Technik und Tracking für Ihre Kunden&nbsp;<span aria-hidden="true">→</span></a></li>
				<li><span class="st-ausgaenge__label">Solar &amp; Wärmepumpe</span> <a class="st-link" href="<?php echo esc_url( $energy_url ); ?>" data-track-action="home_door_energy" data-track-category="navigation" data-track-section="tueren">Eigener Anfrageweg mit Marktcheck&nbsp;<span aria-hidden="true">→</span></a></li>
			</ul>
		</div>
	</section>

	<section class="st-abschnitt st-arbeiten" id="arbeiten" aria-labelledby="arbeiten-h" data-st-abschnitt="05" data-track-section="beweis">
		<?php echo $marke( '05', 'Arbeiten' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="arbeiten-h">Was ich gebaut habe, können Sie nachlesen und öffnen.</h2>
			<article class="st-fall" id="systemprojekt" aria-labelledby="fall-h">
				<div class="st-fall__text">
					<p class="st-klein-label">Dokumentierter Fall · B2B · Photovoltaik</p>
					<h3 class="st-fall__h3" id="fall-h">Für einen <?php echo esc_html( $e3['case_label_accusative'] ); ?> sanken die Kosten pro qualifizierter Anfrage von <?php echo esc_html( $fest( hu_e3_metric( 'cpl_before' ) ) ); ?> auf <?php echo esc_html( $fest( hu_e3_metric( 'cpl_after' ) ) ); ?>.</h3>
					<p>Gebaut habe ich die ganze Strecke: Website und Landingpages, Vorqualifizierung im Formular, Server-Side Tracking und die Übergabe jeder Anfrage an den Vertrieb.</p>
					<a class="st-link st-link--stark" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="home_work_system_case" data-track-category="proof" data-track-section="beweis">Den Fall mit allen Zahlen lesen&nbsp;<span aria-hidden="true">→</span></a>
				</div>
				<dl class="st-kennzahlen">
					<div><dt>Kosten pro qualifizierter Anfrage</dt><dd><span class="st-kennzahl"><?php echo $zahl( $cpl_drop ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></span></dd></div>
					<div><dt>Qualifizierte Anfragen in <?php echo esc_html( hu_e3_metric( 'timeframe', 'display_dative' ) ); ?></dt><dd><span class="st-kennzahl"><?php echo esc_html( hu_e3_metric( 'lead_count' ) ); ?></span></dd></div>
					<div><dt><?php echo esc_html( hu_e3_metric( 'sales_conversion', 'label' ) ); ?></dt><dd><span class="st-kennzahl"><?php echo $zahl( hu_e3_metric( 'sales_conversion' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></span></dd></div>
				</dl>
			</article>

			<?php if ( $reference_n ) : ?>
				<div class="st-referenzen" id="referenzen">
					<h3 class="st-h3"><?php echo esc_html( $reference_word ); ?> <?php echo esc_html( 1 === $reference_n ? 'Website, die Sie jetzt öffnen können.' : 'Websites, die Sie jetzt öffnen können.' ); ?></h3>
					<p class="st-referenzen__intro">Die Beschreibung nennt meinen Teil. Der Link zeigt den heutigen Stand.</p>
					<ul class="st-referenzen__liste">
						<?php foreach ( $references as $reference ) : ?>
							<li>
								<p class="st-klein-label"><?php echo esc_html( $liste( $reference['tag'] ) ); ?></p>
								<p class="st-referenzen__name"><a class="st-link st-link--stark" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="home_reference_open" data-track-category="proof" data-track-section="beweis"><?php echo esc_html( $reference['name'] ); ?>&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a></p>
								<p><?php echo esc_html( $reference['text'] ); ?></p>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="st-abschnitt st-uebergabe" id="uebergabe" aria-labelledby="uebergabe-h" data-st-abschnitt="06" data-track-section="uebergabe">
		<?php echo $marke( '06', 'Übergabe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt st-zweispaltig">
			<div>
				<h2 class="st-h2" id="uebergabe-h">Ich bin teurer, weil Sie mich nach der Übergabe nicht mehr brauchen.</h2>
				<p class="st-vorspann">Eine Website, deren Zugänge und Eigenheiten nur eine Person kennt, kostet bei jedem Wechsel noch einmal. Deshalb liegt nach dem Projekt diese Liste bei Ihnen.</p>
			</div>
			<ol class="st-liste">
				<?php foreach ( $handover as $i => $item ) : ?>
					<li>
						<span class="st-liste__nr" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
						<p class="st-liste__begriff"><?php echo esc_html( $item[0] ); ?><?php if ( $item[2] ) : ?> <span class="st-liste__zusatz">falls beauftragt</span><?php endif; ?></p>
						<p class="st-liste__satz"><?php echo esc_html( $item[1] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="st-abschnitt st-fragen" id="fragen" aria-labelledby="fragen-h" data-st-abschnitt="07" data-track-section="fragen">
		<?php echo $marke( '07', 'Fragen' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt st-zweispaltig">
			<h2 class="st-h2" id="fragen-h">Hier steht, wem der Code gehört und was passiert, wenn ich ausfalle.</h2>
			<div class="fragen">
				<?php foreach ( $faqs as $faq ) : ?>
					<details name="home-faq"><summary><?php echo esc_html( $faq['q'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $faq['a'] ); ?></p></div></div></details>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		// inc/org-schema.php unterdrueckt den Editor-FAQ-Cache auf der Startseite,
		// weil die Fragen hier template-owned sind. Der Knoten wird deshalb hier
		// gebaut, aus demselben Array wie die sichtbaren Antworten.
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

	<section class="st-abschnitt st-anfrage" id="anfrage" aria-labelledby="anfrage-h" data-st-abschnitt="08" data-track-section="abschluss" tabindex="-1">
		<?php echo $marke( '08', 'Anfrage' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt" id="kontakt">
			<p class="st-ende" data-st-ende><span class="st-ende__text">Ende der Strecke</span><span class="st-ende__zeit" data-st-ende-zeit hidden></span></p>
			<h2 class="st-h2 st-anfrage__h2" id="anfrage-h">Ich lese Ihre Anfrage selbst und antworte <?php echo esc_html( $response_short ); ?>.</h2>
			<div class="st-einstiege">
				<?php if ( $first_assessment_on ) : ?>
					<article class="st-einstieg" aria-labelledby="einstieg-erst-h">
						<p class="st-klein-label"><?php echo esc_html( hu_first_assessment_text( 'label' ) ); ?> · kostenlos</p>
						<h3 class="st-h3" id="einstieg-erst-h"><?php echo esc_html( hu_first_assessment_text( 'card_title' ) ); ?></h3>
						<dl>
							<div><dt>Sie schicken</dt><dd><?php echo esc_html( hu_first_assessment_text( 'card_send' ) ); ?></dd></div>
							<div><dt>Sie bekommen</dt><dd><?php echo esc_html( hu_first_assessment_text( 'card_get' ) ); ?></dd></div>
						</dl>
						<a class="tun" href="<?php echo esc_url( hu_first_assessment_url() ); ?>" data-track-action="home_close_ersteinschaetzung" data-track-category="lead_gen" data-track-section="abschluss"><?php echo esc_html( hu_first_assessment_text( 'cta' ) ); ?> <span class="pf" aria-hidden="true">→</span></a>
					</article>
				<?php endif; ?>
				<article class="st-einstieg" aria-labelledby="einstieg-projekt-h">
					<p class="st-klein-label">Projektanfrage</p>
					<h3 class="st-h3" id="einstieg-projekt-h">Für Ihr Vorhaben bekommen Sie einen Festpreis.</h3>
					<dl>
						<div><dt>Sie schicken</dt><dd>Ausgangslage, Engpass und Ziel</dd></div>
						<div><dt>Sie bekommen</dt><dd>Eine fachliche Einordnung, danach Umfang, Festpreis und Zeitrahmen</dd></div>
					</dl>
					<a class="<?php echo esc_attr( $project_cta_class ); ?>" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_close_contact" data-track-category="lead_gen" data-track-section="abschluss">Projekt anfragen <span class="pf" aria-hidden="true">→</span></a>
				</article>
			</div>
			<?php // Kontaktzeile (E-Mail, Telefon) steht direkt darunter im Fuss; hier nicht ein zweites Mal. ?>
			<figure class="st-portrait">
				<img src="<?php echo esc_url( $img_uri . 'hasim-freelancer-portrait-480x600.webp' ); ?>" width="480" height="600" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" loading="lazy" decoding="async">
				<figcaption>Haşim Üner · Pattensen bei Hannover</figcaption>
			</figure>
		</div>
	</section>
</div>
<?php get_footer(); ?>
