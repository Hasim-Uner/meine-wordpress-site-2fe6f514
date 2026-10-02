<?php
/**
 * Homepage: canonical WordPress Freelancer money page.
 *
 * Die Seite ist die Strecke, die sie verkauft: Eine Messlinie laeuft in der
 * Randspalte vom Hero bis zum Anfrageblock und fuellt sich beim Lesen. Oben
 * protokolliert die Hero-Strecke diesen Besuch mit Werten, die der Browser selbst
 * misst; startseite-strecke.js sendet und speichert davon nichts (erzwungen
 * ueber scripts/canon-forbidden-values.txt, Regel strecke-js-privat).
 *
 * Ohne JavaScript steht alles: Linie statisch, Stationen nativ bedienbar, das
 * Protokoll sagt, dass es leer bleibt. Balken wachsen beim Sichtkontakt;
 * bei reduzierter Bewegung folgt nur die Linie dem Scrollstand.
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
$website_url    = $routes['website'];
$landing_url    = $routes['landingpage'];
$conversion_url = $routes['conversion'];
$website_url    = $routes['website'];
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
$landing_price  = $fest( hu_landingpage_price() );
$analysis_price = $fest( hu_analysis_price() );
$analysis_days  = (int) hu_diagnose_canon()['primary_days'];
$retainer       = $fest( hu_freelancer_retainer_display() );
$references     = hu_public_reference_projects();
$reference_n    = count( $references );
$count_words    = [ 1 => 'Eine', 2 => 'Zwei', 3 => 'Drei', 4 => 'Vier', 5 => 'Fünf', 6 => 'Sechs' ];
$reference_word = $count_words[ $reference_n ] ?? (string) $reference_n;

$e3          = hu_e3_canon();
$e3_case_url = $e3['url'];
$cpl_before = (float) hu_e3_metric( 'cpl_before', 'value' );
$cpl_after = (float) hu_e3_metric( 'cpl_after', 'value' );
$cpl_ratio = $cpl_before > 0 ? min( 1, $cpl_after / $cpl_before ) : 0;
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
 * An: Ersteinschaetzung fuehrt in Kopf, Hero, Fall, Preisen und Abschluss.
 * Aus: Die Projektanfrage ersetzt sie; die Preiszeile entfaellt.
 */
$first_assessment_on = hu_first_assessment_enabled();
$project_cta_class   = $first_assessment_on ? 'st-link st-link--stark' : 'tun';
$assessment_url      = $first_assessment_on ? hu_first_assessment_url() : $contact_url;
$assessment_label    = $first_assessment_on ? hu_first_assessment_text( 'cta' ) : 'Projekt anfragen';

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
 * Fuenf Leistungen mit Kanonpreis. Die ids sind die Anker der Angebote im
 * Service-Schema (inc/schema-positioning.php); scripts/lint-entity-crawler-signals.php
 * prueft, dass jeder Schema-Anker hier als 'id' => '…' steht.
 *
 * 'detail' ist die Produktseite zur Vertiefung (Sub-CTA neben der Anfrage). Sie
 * traegt den Umfang; die Zeile bleibt kurz. Nur wo es keine Produktseite gibt
 * (Uebernahme-Check), nennt der Text den Umfang selbst.
 */
$offers = [
	[
		'id' => 'angebot-website',
		'nr'      => '01',
		'tags'    => 'Website · Relaunch · Landingpages · technisches SEO',
		'title'   => 'WordPress-Website und Relaunch',
		'price'   => sprintf( 'ab %s', $website_price ),
		'situation' => 'Sie brauchen eine neue Website oder einen Relaunch.',
		'price_note' => 'netto · Festpreis für den vereinbarten Umfang',
		'text'    => 'Neubau oder Relaunch auf WordPress, gebaut auf einer Testumgebung und erst nach Ihrer Abnahme live. Beim Relaunch bekommt jede alte URL eine Weiterleitung, damit bestehende Links und Suchergebnisse weiter ankommen.',
		'more'    => $fest( hu_freelancer_website_scope_display() ) . '.',
		'request' => $project_link( 'website' ),
		'hook'    => 'home_offer_relaunch',
		'cta'     => 'Website-Projekt anfragen',
		'detail'  => [ 'url' => $website_url, 'hook' => 'home_offer_website_detail' ],
	],
	[
		'id' => 'angebot-landingpage',
		'nr'      => '02',
		'tags'    => 'Landingpage · Anfrageformular · Abnahme',
		'title'   => 'Landingpage für ein Angebot',
		'price'   => $landing_price,
		'situation' => 'Sie haben ein Angebot, aber keine Seite, die dafür anfragt.',
		'price_note' => 'netto · Festpreis',
		'text'    => 'Eine Seite für ein Angebot, mit Konzept, Text, Anfrageformular und Herkunft jeder Anfrage. Sie geben zweimal frei: den Text und die fertige Seite.',
		'more'    => 'Tracking und Website sind eigene Zusätze mit eigenem Preis.',
		'request' => $project_link( 'conversion' ),
		'hook'    => 'home_offer_landingpage',
		'cta'     => 'Landingpage anfragen',
		'detail'  => [ 'url' => $landing_url, 'hook' => 'home_offer_landingpage_detail' ],
	],
	[
		'id' => 'angebot-conversion',
		'nr'      => '03',
		'tags'    => 'Conversion-Optimierung · Anfragesystem-Analyse · B2B',
		'title'   => 'Conversion-Analyse für B2B-Websites',
		'price'   => $analysis_price,
		'situation' => 'Besucher kommen, aber zu wenige passende Anfragen.',
		'price_note' => sprintf( 'netto · Analyse · %d Werktage', $analysis_days ),
		'text'    => 'Besucher kommen, aber zu wenige passende Anfragen? Die Analyse liefert einen schriftlichen Befund der Strecke vom Besuch bis zur Rückmeldung im Vertrieb. Umgesetzt wird danach nur, was im Befund steht, zu festen Preisen.',
		'more'    => sprintf( 'Die %s der Analyse werden bei einer Umsetzung angerechnet.', $analysis_price ),
		'request' => hu_get_contact_intake_url( 'analysis', 'conversion' ),
		'hook'    => 'home_offer_analysis',
		'cta'     => 'Analyse anfragen',
		'detail'  => [ 'url' => $conversion_url, 'hook' => 'home_offer_analysis_detail' ],
	],
	[
		'id' => 'angebot-tracking',
		'nr'      => '04',
		'tags'    => 'GA4 · Google Tag Manager · Consent Mode · Server-Side · CRM',
		'title'   => $tracking_offer['name'],
		'price'   => $tracking_price,
		'situation' => 'Anfragen kommen an, aber niemand weiß, woher.',
		'price_note' => 'netto · Festpreis · ' . $tracking_offer['weeks'],
		'text'    => 'Messplan, GA4, Google Tag Manager, Consent Mode und Google Ads. Jede Conversion mit Testfall und Abnahmeprotokoll. Gemessen wird im Browser, ohne eigenen Server.',
		'more'    => sprintf( 'Die Stufen darüber: %s.', $fest( hu_tracking_ladder_display( 2 ) ) ),
		'request' => $project_link( 'tracking' ),
		'hook'    => 'home_offer_tracking',
		'cta'     => 'Tracking-Projekt anfragen',
		'detail'  => [ 'url' => $tracking_url, 'hook' => 'home_offer_tracking_detail' ],
	],
	[
		'id' => 'angebot-weiterentwicklung',
		'nr'      => '05',
		'tags'    => 'Übernahme · Pflege · Weiterentwicklung',
		'title'   => 'Bestehende Website übernehmen',
		'price'   => $takeover_price,
		'situation' => 'Ihre Website läuft, aber niemand kümmert sich mehr darum.',
		'price_note' => 'netto · Übernahme-Check',
		'text'    => 'Ich prüfe Theme, Plugins, Updates, Backups, Zugänge und Ladezeit. Sie bekommen einen schriftlichen Befund mit Festpreis für den nächsten Schritt. Der Befund gehört Ihnen, auch wenn Sie mit jemand anderem weiterarbeiten.',
		'more'    => 'Beauftragen Sie mich danach, wird der Check verrechnet.',
		'request' => $project_link( 'implementation_scope' ),
		'hook'    => 'home_offer_takeover',
		'cta'     => 'Übernahme-Check anfragen',
		'detail'  => null,
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

$project_steps = [
	[ 'Ziel und Umfang klären', 'Ausgangslage, Engpass und Ziel werden konkret. Sie bekommen ein Angebot mit Umfang, Festpreis und Zeitrahmen.' ],
	[ 'Aufbauen und abnehmen', 'Gebaut wird auf einer Testumgebung. Vor dem Livegang prüfen Sie die vereinbarten Funktionen und schicken eine Testanfrage ab.' ],
	[ 'Übergeben und erklären', 'Sie erhalten Code, Zugänge und Dokumentation. Danach entscheiden Sie, ob und mit wem es weitergeht.' ],
];
$request_steps = [
	[ 'Heute', 'Sie schicken das kurze Formular ab. Mehr als die Angaben auf der Karte oben braucht es nicht.' ],
	[ 'Antwort ' . $response_short, 'Ich lese Ihre Angaben selbst und antworte schriftlich mit einer Einordnung.' ],
	[ 'Danach', 'Sie entscheiden, ob ein Projekt daraus wird. Ohne Verpflichtung.' ],
];

$faqs = [
	[
		'q' => 'Muss unsere Website komplett neu gebaut werden?',
		'a' => 'Das entscheidet der Befund, nicht meine Vorliebe. Trägt der vorhandene Aufbau, baue ich darauf weiter. Ein Theme oder Page Builder wird nur ersetzt, wenn er Ladezeit, Pflege oder eine Anbindung nachweisbar blockiert, und die Begründung steht dann im Befund.',
	],
	[
		'q' => 'Brauchen wir Website, Tracking und CRM zusammen?',
		'a' => 'Nein. Der Umfang richtet sich nach Ihrem Ziel. Eine Landingpage oder ein Conversion-Tracking kann der erste Schritt sein. Server-Side Tracking und die Anbindung ans CRM sind eigene Ausbaustufen mit eigenem Preis.',
	],
	[
		'q' => 'Wie lange dauert ein Projekt, und was brauchen Sie von uns?',
		'a' => 'Den Zeitrahmen nenne ich mit dem Angebot. Darin steht auch, wann ich Texte, Bilder, Zugänge und Freigaben von Ihnen brauche. Auf Ihrer Seite braucht es eine Person, die entscheidet und freigibt.',
	],
	[
		'q' => 'Arbeiten Sie nur mit Unternehmen aus Hannover?',
		'a' => 'Nein. Ich sitze in Pattensen bei Hannover und arbeite remote mit Unternehmen in Deutschland, Österreich und der Schweiz.',
	],
	[
		'q' => 'Was misst das Protokoll oben auf dieser Seite?',
		'a' => 'Ihr Browser liest, über welchen Weg Sie gekommen sind, misst die Ladezeit dieses Aufrufs, welche Abschnitte Sie erreicht haben, wie weit Sie gescrollt haben und ob Sie auf eine Anfrage geklickt haben. Die Werte bleiben in Ihrem Browser und verschwinden mit dem Tab. Gesendet oder gespeichert wird davon nichts. Die Website selbst zählt nur den Seitenaufruf, ohne Cookie.',
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

<?php // data-st-final grenzt die Homepage-Deltas von der gemeinsamen White-Label-Basis ab. ?>
<div class="doku st" id="top" data-track-section="homepage" data-st data-st-final>

	<section class="st-abschnitt st-hero st-hero--messflaeche st-messflaeche tafel" id="klick" aria-labelledby="st-h1" data-st-abschnitt="01" data-track-section="hero" data-st-hero>
		<?php echo $marke( '01', 'Klick' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<div class="st-hero__kopfzeile">
				<p class="st-klein-label st-hero__kicker">WordPress Freelancer für Unternehmen · Pattensen bei Hannover · remote in DACH</p>
				<a class="st-klein-label st-hero__preis" href="#angebote" data-track-action="home_hero_price_line" data-track-category="navigation" data-track-section="hero">Websites ab <?php echo esc_html( $website_price ); ?> netto · alle Preise ↓</a>
			</div>
			<div class="st-hero__titel" data-st-titel>
				<h1 class="st-hero__h1" id="st-h1"><span class="st-messzeile"><span class="st-messwort">Mehr Anfragen</span></span> <span class="st-messzeile"><span class="st-messwort">über Ihre Website.</span></span> <span class="st-messzeile st-leise"><span class="st-messwort">Und Sie sehen,</span></span> <span class="st-messzeile st-leise"><span class="st-messwort st-wort--quelle" data-st-wort-quelle><span class="st-quelle__kontur">woher jede kommt.</span><span class="st-quelle__fuell" aria-hidden="true">woher jede kommt.</span></span></span></h1>
				<div class="st-etikett" data-st-etikett aria-hidden="true" hidden>
					<span class="st-etikett__kopf">Ihr Besuch kam über</span>
					<span class="st-etikett__wert" data-st-etikett-wert>…</span>
					<span class="st-etikett__fuss" data-st-etikett-fuss>zugeordnet · ohne Cookie</span>
				</div>
			</div>
			<div class="st-hero__unten">
				<p class="st-hero__satz">Ich baue Ihre WordPress-Website, den Weg zum Formular und die Messung dazu. Sie arbeiten dabei direkt mit mir, vom ersten Entwurf bis zur Übergabe.</p>
				<div>
					<div class="st-hero__ctas">
						<?php if ( $first_assessment_on ) : ?>
							<a class="tun" href="<?php echo esc_url( hu_first_assessment_url() ); ?>" data-track-action="home_head_ersteinschaetzung" data-track-category="lead_gen" data-track-section="hero"><?php echo esc_html( hu_first_assessment_text( 'cta' ) ); ?> <span class="pf" aria-hidden="true">→</span></a>
						<?php else : ?>
							<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_head_contact" data-track-category="lead_gen" data-track-section="hero">Projekt anfragen <span class="pf" aria-hidden="true">→</span></a>
						<?php endif; ?>
						<a class="st-link" href="#angebote" data-track-action="home_hero_prices" data-track-category="navigation" data-track-section="hero">Alle Preise <span class="pf" aria-hidden="true">↓</span></a>
					</div>
					<?php if ( $first_assessment_on ) : ?>
						<p class="st-hero__notiz"><?php echo esc_html( hu_first_assessment_text( 'cta_note' ) ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="st-spur" id="protokoll" role="group" aria-labelledby="st-protokoll-titel" data-st-protokoll>
				<p class="st-protokoll__kopf"><span id="st-protokoll-titel">Protokoll · Ihr Besuch</span><span class="st-protokoll__status" data-st-status hidden>läuft</span></p>
				<div class="st-spur__bahn" data-st-bahn>
					<div class="st-spur__linie" aria-hidden="true" data-st-spur-linie><i data-st-spur-fuell></i></div>
					<span class="st-spur__signal" aria-hidden="true" data-st-signal hidden><span class="st-spur__hier">Sie sind hier</span></span>
					<ol class="st-spur__stationen">
						<?php foreach ( $stations as $i => $station ) : ?>
							<li class="st-spur__station<?php echo $i > 2 ? ' st-spur__station--spaeter' : ''; // raw-ok -- static class. ?>" data-st-spur-station="<?php echo esc_attr( (string) $i ); ?>">
								<a href="#station-<?php echo esc_attr( $station['slug'] ); ?>" data-st-station-link data-track-action="home_hero_station" data-track-label="<?php echo esc_attr( $station['slug'] ); ?>" data-track-category="navigation" data-track-section="hero"><span class="st-spur__punkt" aria-hidden="true"></span><span class="st-spur__nr"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><span class="st-spur__name"><?php echo esc_html( $station['name'] ); ?></span></a>
								<?php if ( 0 === $i ) : ?>
									<span class="nur-vorlesen">Herkunft: </span><span class="st-spur__wert" data-st-wert="herkunft">…</span>
								<?php elseif ( 1 === $i ) : ?>
									<span class="st-spur__wert st-messwert" data-st-wert="lcp">…</span><span class="st-spur__einheit" data-st-lcp-label>Ladezeit (LCP)</span>
								<?php elseif ( 2 === $i ) : ?>
									<span class="nur-vorlesen">Klick auf Anfrage: </span><span class="st-spur__wert" data-st-wert="klick">…</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
					<p class="st-spur__klammer">Ab hier baue ich für Ihre Website: Messung bis zur Quelle, Übergabe ins CRM, Antwort mit Termin.</p>
				</div>
				<dl class="st-spur__messung"><div><dt>Abschnitte erreicht</dt><dd data-st-wert="abschnitte">…</dd></div><div><dt>Scrolltiefe</dt><dd data-st-wert="tiefe">…</dd></div></dl>
				<p class="st-protokoll__ohne-js">Ohne JavaScript misst diese Seite nichts. Das Protokoll bleibt leer.</p>
				<p class="st-spur__fuss">Diese Werte entstehen in Ihrem Browser und werden nirgendwohin gesendet. <a href="<?php echo esc_url( $privacy_url ); ?>" data-track-action="home_protocol_privacy" data-track-category="trust" data-track-section="hero">Datenschutz&nbsp;<span aria-hidden="true">→</span></a></p>
				<p class="nur-vorlesen" aria-live="polite" data-st-ansage></p>
			</div>

			<div class="st-hero__meta">
				<img class="st-hero__portrait" src="<?php echo esc_url( $img_uri . 'hasim-freelancer-portrait-112.webp' ); ?>" width="56" height="56" alt="" decoding="async" fetchpriority="low">
				<p class="st-hero__person"><strong>Haşim Üner</strong> · Entwicklung, Tracking und Conversion<br><a href="<?php echo esc_url( $about_url ); ?>" data-track-action="home_about" data-track-category="trust" data-track-section="hero">Wer hier für Sie arbeitet</a></p>
			</div>

			<ul class="st-belege" aria-label="Belege">
				<li><a href="#angebote" data-track-action="home_proof_strip_price" data-track-category="proof" data-track-section="hero"><span class="st-belege__zahl"><?php echo $zahl( "ab\u{00A0}" . $website_price ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></span> <span class="st-belege__text">netto für eine WordPress-Website, alle Preise stehen auf dieser Seite</span></a></li>
				<li><a href="#arbeiten" data-track-action="home_proof_strip_case" data-track-category="proof" data-track-section="hero"><span class="st-belege__zahl st-belege__zahl--messwert"><?php echo $zahl( $cpl_drop ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></span> <span class="st-belege__text">Kosten pro qualifizierter Anfrage in einem dokumentierten B2B-Fall</span></a></li>
				<?php if ( $reference_n ) : ?>
					<li><a href="#referenzen" data-track-action="home_proof_strip_references" data-track-category="proof" data-track-section="hero"><span class="st-belege__zahl"><?php echo esc_html( (string) $reference_n ); ?></span> <span class="st-belege__text"><?php echo esc_html( 1 === $reference_n ? 'öffentliche Website, die Sie selbst öffnen können' : 'öffentliche Websites, die Sie selbst öffnen können' ); ?></span></a></li>
				<?php endif; ?>
			</ul>
		</div>
	</section>


	<section class="st-abschnitt st-strecke" id="strecke" aria-labelledby="strecke-h" data-st-abschnitt="02" data-track-section="strecke">
		<?php echo $marke( '02', 'Strecke' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="strecke-h">Eine Anfrage passiert sechs Stationen. An jeder kann sie verloren gehen.</h2>
			<p class="st-vorspann">Anfragen, die nie ankommen, stehen in keinem Bericht. Darum sieht eine Website oft gesund aus, während sie Interessenten verliert. Zu jeder Station steht hier, was dort bricht und was ich dagegen baue.</p>
			<?php // #angebot-funnel: frueherer Anker des Angebots "Anfragestrecken", von CRO-Links im ganzen Theme verlinkt. ?>
			<ol class="st-stationen" id="angebot-funnel" data-st-stationen>
				<?php foreach ( $stations as $i => $station ) : ?>
					<li><details class="st-station" name="station"<?php echo 0 === $i ? ' open' : ''; ?>>
						<summary id="station-<?php echo esc_attr( $station['slug'] ); ?>"><span class="st-station__nr"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><span class="st-station__name"><?php echo esc_html( $station['name'] ); ?></span><span class="st-station__teaser"><?php echo esc_html( $station['teaser'] ); ?></span></summary>
						<div class="st-station__detail" id="station-<?php echo esc_attr( $station['slug'] ); ?>-detail">
							<div><p class="st-klein-label">Was bricht</p><p><?php echo esc_html( $station['bricht'] ); ?></p></div>
							<div><p class="st-klein-label">Was ich baue</p><p><?php echo esc_html( $station['baue'] ); ?></p></div>
							<?php if ( ! empty( $station['beleg']['lcp'] ) ) : ?>
								<p class="st-station__beleg" data-st-nur-js hidden>Diese Seite stand in Ihrem Browser nach <span class="st-messwert" data-st-lcp-kopie>…</span>.</p>
							<?php elseif ( ! empty( $station['beleg'] ) ) : ?>
								<p class="st-station__beleg">
									<?php if ( ! empty( $station['beleg']['text'] ) ) : ?><?php echo esc_html( $station['beleg']['text'] ); ?><?php endif; ?>
									<a class="st-link" href="<?php echo esc_url( $station['beleg']['url'] ); ?>" data-track-action="<?php echo esc_attr( $station['beleg']['hook'] ); ?>" data-track-category="<?php echo esc_attr( 'home_proof_tracking_page' === $station['beleg']['hook'] ? 'proof' : 'navigation' ); ?>" data-track-section="strecke"><?php echo esc_html( $station['beleg']['label'] ); ?>&nbsp;<span aria-hidden="true">→</span></a>
								</p>
							<?php endif; ?>
						</div>
					</details></li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="st-abschnitt st-arbeiten st-wash" id="arbeiten" aria-labelledby="arbeiten-h" data-st-abschnitt="03" data-track-section="beweis">
		<?php echo $marke( '03', 'Fall' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt" id="fall">
			<h2 class="st-h2" id="arbeiten-h">Von <?php echo esc_html( $fest( hu_e3_metric( 'cpl_before' ) ) ); ?> auf <?php echo esc_html( $fest( hu_e3_metric( 'cpl_after' ) ) ); ?> pro Anfrage.</h2>
			<p class="st-vorspann">Was die ganze Strecke in einem Projekt verändert hat, dokumentiert für einen <?php echo esc_html( $e3['case_label_accusative'] ); ?>.</p>
			<article class="st-fall" id="systemprojekt" aria-label="Dokumentierter B2B-Fall">
				<div class="st-fall__text">
					<p class="st-klein-label">Dokumentierter Fall · B2B · Photovoltaik</p>
					<p>Der Betrieb hat seine Anfragen über Portale eingekauft, zu <?php echo esc_html( $fest( hu_e3_metric( 'cpl_before' ) ) ); ?> pro Anfrage. Gebaut habe ich die ganze Strecke: Website und Landingpages, Kampagnen, ein Formular mit Vorqualifizierung, Server-Side Tracking und die Übergabe jeder Anfrage an den Vertrieb.</p>
					<ol class="st-verlauf" aria-label="Verlauf des Projekts">
						<li><span class="st-klein-label"><?php echo esc_html( $e3['homepage_build_label'] ); ?></span><span>Aufbau von Website, Formular, Tracking und Übergabe an den Vertrieb</span></li>
						<li><span class="st-klein-label"><?php echo esc_html( $e3['cpl_reached_label'] ); ?></span><span>Rund <?php echo esc_html( $fest( hu_e3_metric( 'cpl_after' ) ) ); ?> pro eigener Anfrage erreicht</span></li>
						<li><span class="st-klein-label">nach <?php echo esc_html( hu_e3_metric( 'timeframe', 'display_dative' ) ); ?></span><span><?php echo esc_html( hu_e3_metric( 'lead_count' ) ); ?> Anfragen insgesamt</span></li>
					</ol>
					<a class="st-link st-link--stark" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="home_work_system_case" data-track-category="proof" data-track-section="beweis">Den Fall mit allen Zahlen lesen <span aria-hidden="true">→</span></a>
				</div>
				<figure class="st-messtafel tafel" aria-labelledby="messtafel-h" data-st-messtafel>
					<div class="st-messtafel__kopf"><p class="st-klein-label" id="messtafel-h">Messwerte · Kosten pro Anfrage</p><span class="st-klein-label">maßstäblich</span></div>
					<div class="st-vergleich"><div class="st-vergleich__kopf"><span>Gekaufte Portal-Anfrage vorher</span><strong><?php echo $zahl( hu_e3_metric( 'cpl_before' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></strong></div><div class="st-balken" aria-hidden="true"><i style="--st-anteil: 1"></i></div></div>
					<div class="st-vergleich st-vergleich--nach"><div class="st-vergleich__kopf"><span>Eigene Anfrage <?php echo esc_html( $e3['cpl_reached_label'] ); ?></span><strong><?php echo $zahl( hu_e3_metric( 'cpl_after' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></strong></div><div class="st-balken" aria-hidden="true"><i style="--st-anteil: <?php echo esc_attr( (string) $cpl_ratio ); ?>"></i></div></div>
					<div class="st-skala" aria-hidden="true"><?php foreach ( range( 0, 3 ) as $tick ) : ?><span style="--st-position: <?php echo esc_attr( (string) ( $tick / 3 * 100 ) ); ?>%"><?php echo esc_html( hu_format_eur( $cpl_before * $tick / 3 ) ); ?></span><?php endforeach; ?></div>
					<dl class="st-kennzahlen">
						<div><dt>Kosten pro qualifizierter Anfrage</dt><dd><span class="st-kennzahl"><?php echo $zahl( $cpl_drop ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></span></dd></div>
						<div><dt>Anfragen insgesamt in <?php echo esc_html( hu_e3_metric( 'timeframe', 'display_dative' ) ); ?></dt><dd><span class="st-kennzahl"><?php echo esc_html( hu_e3_metric( 'lead_count' ) ); ?></span></dd></div>
						<div><dt><?php echo esc_html( hu_e3_metric( 'sales_conversion', 'label' ) ); ?></dt><dd><span class="st-kennzahl"><?php echo $zahl( hu_e3_metric( 'sales_conversion' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?></span></dd></div>
					</dl>
					<figcaption>Vergleich zweier Anfragequellen, kein Durchschnitt über den ganzen Zeitraum. Das Ergebnis kommt aus mehreren Maßnahmen zusammen.</figcaption>
				</figure>
			</article>
			<div class="st-folge"><p>Welche Station bei Ihnen bricht, sehe ich mir gern an.</p><a class="tun" href="<?php echo esc_url( $assessment_url ); ?>" data-track-action="<?php echo esc_attr( $first_assessment_on ? 'home_case_ersteinschaetzung' : 'home_case_contact' ); ?>" data-track-category="lead_gen" data-track-section="beweis"><?php echo esc_html( $assessment_label ); ?> <span class="pf" aria-hidden="true">→</span></a></div>
		</div>
	</section>

	<section class="st-abschnitt st-pruefstand st-pruefstand--leise" id="pruefstand" aria-labelledby="pruefstand-h" data-st-abschnitt="04" data-track-section="pruefstand">
		<?php echo $marke( '04', 'Prüfstand' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<div class="st-pruefstand__kopf">
				<h2 class="st-h2" id="pruefstand-h">Prüfen Sie mich, <span class="st-leise">bevor Sie mir schreiben.</span></h2>
				<p class="st-vorspann">Diese Website ist mein Arbeitsbeleg: Der Code liegt offen, jede Änderung wird vor dem Livegang automatisch geprüft, und die Ladezeit messen Sie selbst.</p>
			</div>
			<?php // Icons: Strich in currentColor, ohne eigene Farbe; nur die Ladezeit traegt den Akzent, weil sie ein Messwert ist. ?>
			<ol class="st-pruefungen">
				<li>
					<svg class="st-pruefungen__icon" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="6" cy="5" r="2"/><circle cx="6" cy="19" r="2"/><circle cx="18" cy="7" r="2"/><path d="M6 7v10M18 9v1a4 4 0 0 1-4 4h-4a4 4 0 0 0-4 3"/></svg>
					<p class="st-pruefungen__label">Quellcode</p>
					<p class="st-pruefungen__text">Jede Datei und jede Änderung, mit Datum und Begründung.</p>
					<a class="st-link" href="<?php echo esc_url( $github_url . '/commits/main/' ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_github_history" data-track-category="proof" data-track-section="pruefstand">Code auf GitHub&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
				</li>
				<li>
					<svg class="st-pruefungen__icon" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M8 7l-5 5 5 5M16 7l5 5-5 5M13.5 4.5l-3 15"/></svg>
					<p class="st-pruefungen__label">Prüfungen</p>
					<p class="st-pruefungen__text">Vor dem Livegang laufen automatische Prüfungen. Schlägt eine fehl, geht nichts live.</p>
					<a class="st-link" href="<?php echo esc_url( $github_url . '/actions' ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_github_ci" data-track-category="proof" data-track-section="pruefstand">Prüfläufe auf GitHub&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
				</li>
				<li class="st-pruefungen__mess">
					<svg class="st-pruefungen__icon" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M3.5 17a8.5 8.5 0 1 1 17 0"/><path d="M12 17l4.5-5.5"/><path d="M6.5 13.5l1 .6M12 8.5v1.2M17.5 13.5l-1 .6"/></svg>
					<p class="st-pruefungen__label">Ladezeit</p>
					<p class="st-pruefungen__text">Eine Zahl von mir wäre nur eine Behauptung. PageSpeed Insights misst die Seite, jederzeit und ohne mich.</p>
					<p class="st-pruefungen__messung" data-st-nur-js hidden>Ihr Browser hat diesen Aufruf in <span class="st-messwert" data-st-lcp-kopie>…</span> dargestellt.</p>
					<a class="st-link" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_pagespeed" data-track-category="proof" data-track-section="pruefstand">PageSpeed jetzt messen&nbsp;<span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
					<p class="st-pruefungen__klein">Laborwerte schwanken mit Uhrzeit und Serverlast.</p>
				</li>
			</ol>
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

	<section class="st-abschnitt st-leistungen st-wash" id="angebote" aria-labelledby="angebote-h" data-st-abschnitt="05" data-track-section="angebote">
		<?php echo $marke( '05', 'Preise' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt">
			<h2 class="st-h2" id="angebote-h">Was es kostet, steht hier. <span class="st-leise">Der Einstieg richtet sich nach Ihrer Ausgangslage.</span></h2>
			<p class="st-vorspann">Alle Preise netto. Umfang und Endpreis stehen vor dem Start schriftlich fest. Was danach dazukommt, kommt nur mit Ihrer Zustimmung dazu.</p>
			<div class="st-angebote">
				<?php foreach ( $offers as $offer ) : ?>
					<article class="st-angebot" id="<?php echo esc_attr( $offer['id'] ); ?>" aria-labelledby="<?php echo esc_attr( $offer['id'] ); ?>-h">
						<span class="st-angebot__nr st-klein-label" aria-hidden="true"><?php echo esc_html( $offer['nr'] ); ?></span>
						<div class="st-angebot__kopf"><h3 class="st-angebot__titel" id="<?php echo esc_attr( $offer['id'] ); ?>-h"><?php echo esc_html( $offer['title'] ); ?></h3><p class="st-angebot__lage"><?php echo esc_html( $offer['situation'] ); ?></p></div>
						<div class="st-angebot__text"><p><?php echo esc_html( $offer['text'] ); ?></p><p class="st-angebot__mehr"><?php echo esc_html( $offer['more'] ); ?></p></div>
						<div class="st-angebot__preise">
							<p class="st-angebot__preis"><?php echo esc_html( $offer['price'] ); ?><small><?php echo esc_html( $offer['price_note'] ); ?></small></p>
							<p class="st-angebot__wege"><a class="st-link st-link--stark" href="<?php echo esc_url( $offer['request'] ); ?>" data-track-action="<?php echo esc_attr( $offer['hook'] ); ?>" data-track-category="lead_gen" data-track-section="angebote">Projekt anfragen <span aria-hidden="true">→</span><span class="nur-vorlesen"> für <?php echo esc_html( $offer['title'] ); ?></span></a>
							<?php if ( ! empty( $offer['detail'] ) ) : ?><a class="st-link" href="<?php echo esc_url( $offer['detail']['url'] ); ?>" data-track-action="<?php echo esc_attr( $offer['detail']['hook'] ); ?>" data-track-category="navigation" data-track-section="angebote">Umfang im Detail <span aria-hidden="true">→</span><span class="nur-vorlesen"> zu <?php echo esc_html( $offer['title'] ); ?></span></a><?php endif; ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="st-weiter"><p><strong>Weiterentwicklung nach dem Projekt.</strong> <?php echo esc_html( $retainer ); ?> im Monat, monatlich kündbar. Eine Voraussetzung ist das nicht.</p><a class="st-link" href="#uebergabe" data-track-action="home_offer_retainer_more" data-track-category="navigation" data-track-section="angebote">Was Sie ohnehin behalten <span aria-hidden="true">↓</span></a></div>
			<p class="st-preisnotiz">Hosting, Lizenzen und externe Dienste stehen getrennt im Angebot und laufen auf Ihren Namen.</p>
			<?php if ( $first_assessment_on ) : ?><div class="st-folge"><p>Unsicher, wo Sie anfangen? Die Ersteinschätzung sagt es Ihnen, kostenlos.</p><a class="tun" href="<?php echo esc_url( $assessment_url ); ?>" data-track-action="home_offers_ersteinschaetzung" data-track-category="lead_gen" data-track-section="angebote"><?php echo esc_html( $assessment_label ); ?> <span class="pf" aria-hidden="true">→</span></a></div><?php endif; ?>
		</div>
	</section>

	<section class="st-abschnitt st-uebergabe" id="uebergabe" aria-labelledby="uebergabe-h" data-st-abschnitt="06" data-track-section="uebergabe">
		<?php echo $marke( '06', 'Übergabe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt"><div class="st-zweispaltig">
			<div>
				<h2 class="st-h2" id="uebergabe-h">Nach der Übergabe können Sie mich ersetzen. <span class="st-leise">Das ist Absicht.</span></h2>
				<p class="st-vorspann">Hosting, Tag Manager und Tracking-Server richte ich ein, aber sie laufen von Anfang an auf Ihren Namen. Eine Website, deren Zugänge und Eigenheiten nur eine Person kennt, kostet bei jedem Wechsel noch einmal. Deshalb liegt nach dem Projekt diese Liste bei Ihnen.</p>
				<p class="st-einwand"><strong>Und wenn Sie als Freelancer ausfallen?</strong> Dann sage ich es Ihnen am selben Tag. Zugänge und Code liegen bei Ihnen, und die Dokumentation ist für einen anderen Entwickler geschrieben. Ihre Website hängt nicht an mir.</p>
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
			<div class="st-ablauf"><h3 class="st-h3">So läuft ein Projekt.</h3><ol><?php foreach ( $project_steps as $i => $step ) : ?><li><p class="st-klein-label">Schritt <?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p><h4><?php echo esc_html( $step[0] ); ?></h4><p><?php echo esc_html( $step[1] ); ?></p></li><?php endforeach; ?></ol></div>
			<div class="st-eignung" id="eignung"><div><h3 class="st-h3">Wann ein anderer Weg besser passt.</h3><p>Lieber sage ich das hier als nach dem ersten Gespräch.</p></div><ul>
				<li><strong>Ein Wartungsvertrag mit Rufbereitschaft und garantierter Reaktionszeit.</strong> Weiterentwicklung gibt es im Monatskontingent, ohne Bereitschaftsdienst.</li>
				<li><strong>Sie sind eine Agentur und geben die Arbeit unter Ihrem Namen weiter.</strong> Dafür gibt es einen eigenen Weg mit Agenturkonditionen.<br><a class="st-link" href="<?php echo esc_url( $whitelabel_url ); ?>" data-track-action="home_door_whitelabel" data-track-category="navigation" data-track-section="tueren">White-Label-Zusammenarbeit <span aria-hidden="true">→</span></a></li>
				<li><strong>Sie betreiben einen Solar- oder Wärmepumpenbetrieb.</strong> Für diese Branche gibt es einen eigenen Anfrageweg mit Marktcheck.<br><a class="st-link" href="<?php echo esc_url( $energy_url ); ?>" data-track-action="home_door_energy" data-track-category="navigation" data-track-section="tueren">Solar &amp; Wärmepumpe <span aria-hidden="true">→</span></a></li>
			</ul></div>
		</div>
	</section>

	<section class="st-abschnitt st-fragen st-wash" id="fragen" aria-labelledby="fragen-h" data-st-abschnitt="07" data-track-section="fragen">
		<?php echo $marke( '07', 'Fragen' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper. ?>
		<div class="st-inhalt st-zweispaltig">
			<h2 class="st-h2" id="fragen-h">Was sonst noch vor einer Anfrage gefragt wird.</h2>
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
			<?php
			/*
			 * Heller Abschluss: Ueberschrift, Einstiege, naechste Schritte,
			 * Portrait. Mobil bleiben die Buttons vor dem Bild; am Desktop
			 * stellt das Raster die Person neben den Einstieg.
			 */
			?>
			<div class="st-schluss">
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
				<ol class="st-danach" aria-label="Was nach dem Absenden passiert"><?php foreach ( $request_steps as $i => $step ) : ?><li><span class="st-klein-label"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?> · <?php echo esc_html( $step[0] ); ?></span><?php echo esc_html( $step[1] ); ?></li><?php endforeach; ?></ol>
				<?php // Kontaktzeile (E-Mail, Telefon) steht direkt darunter im Fuss; hier nicht ein zweites Mal. ?>
				<figure class="st-portrait">
					<img src="<?php echo esc_url( $img_uri . 'hasim-freelancer-portrait-480x600.webp' ); ?>" width="480" height="600" alt="Haşim Üner, WordPress-Entwickler aus Pattensen bei Hannover" loading="lazy" decoding="async">
					<figcaption><strong>Haşim Üner</strong><br>WordPress · Tracking · Conversion<br>Pattensen bei Hannover</figcaption>
				</figure>
			</div>
		</div>
	</section>
</div>
<?php get_footer(); ?>
