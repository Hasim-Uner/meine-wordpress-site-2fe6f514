<?php
/**
 * Front Page Template — Dokumentkopf, Pruefstand und drei Wege.
 *
 * Die Startseite verkauft keine Leistung vollstaendig. Sie sagt, was hier
 * gemacht wird, laesst sich an der eigenen Technik pruefen und sortiert auf
 * drei getrennte Commercial Routes: direkte Projekte, White Label, Energie.
 *
 * ── Was sich am 2026-09-12 geaendert hat ──────────────────────────────
 *
 * Von elf Abschnitten auf sieben. Ersatzlos entfallen sind die
 * Funnel-Grafik (vier Begriffe, die in "Die Strecke" ohnehin stehen), das
 * Auffangbecken "Sie wissen noch nicht, welcher Weg passt?" und die
 * Schlussliste "Was trifft zu?" im Fuss.
 *
 * Der Grund fuer die letzten beiden ist derselbe: Die drei Wege standen
 * dreimal auf der Seite — Hauptmenue, Kartenabschnitt, Schlussliste — und
 * in drei verschiedenen Reihenfolgen. Das ist keine Fuehrung, sondern
 * Wiederholung, und die wechselnde Reihenfolge nimmt jeder Anordnung ihre
 * Aussage. Jetzt stehen sie im Seiteninhalt genau einmal, als Abschnitt 01,
 * mit Preisrahmen in der Zeile. Im Hauptmenue bleiben sie natuerlich. Die
 * Unterdrueckung im Fuss sitzt in template-parts/site-footer.php.
 *
 * Der Pruefstand ist von Platz vier auf Platz zwei gerueckt: wer hier
 * ankommt, hat noch keinen Grund zu glauben, was weiter unten steht.
 *
 * ── Zahlen ────────────────────────────────────────────────────────────
 *
 * Jede Zahl auf dieser Seite kommt aus inc/canon/. Die Preisrahmen der
 * drei Wege lesen den Pricing-Canon, die Fallzahlen den E3-Canon, die
 * Antwortzeit den Messaging-Canon.
 *
 * Die frueher hier gezeigte Lead-Conversion-Rate (12 %) steht nicht mehr
 * auf der Seite. Sie ist kein Fehler — der Canon fuehrt sie als eigene
 * Kennzahl neben der Abschlussquote (15 %) —, aber zwei Prozentzahlen aus
 * demselben Fall nebeneinander erklaeren sich auf einer Verteilerseite
 * nicht in der Zeit, die sie bekommt. Der ausfuehrliche Fall zeigt beide.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes         = function_exists( 'hu_get_commercial_route_map' ) ? hu_get_commercial_route_map() : [];
$energy_url     = $routes['energy'] ?? home_url( '/solar-waermepumpen-leadgenerierung/' );
$freelancer_url = $routes['freelancer'] ?? home_url( '/wordpress-freelancer-hannover/' );
$whitelabel_url = $routes['whitelabel'] ?? ( function_exists( 'nexus_get_whitelabel_page_url' ) ? nexus_get_whitelabel_page_url() : home_url( '/whitelabel-retainer/' ) );
$tracking_url   = $routes['tracking_b2b'] ?? home_url( '/server-side-tracking-b2b/' );
$contact_url    = function_exists( 'hu_get_navigation_project_request_url' )
	? hu_get_navigation_project_request_url()
	: home_url( '/kontakt/' );
$psi_url        = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/' ) );

$contact_email = function_exists( 'hu_get_contact_email' ) ? hu_get_contact_email() : 'kontakt@hasimuener.de';
$phone_display = function_exists( 'hu_get_contact_phone' ) ? hu_get_contact_phone( 'display' ) : '';

$response_value   = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'value' ) : 'spätestens 2 Werktage';
$response_compact = function_exists( 'hu_response_promise' ) ? hu_response_promise( 'compact' ) : 'Antwort spätestens 2 Werktage';

$e3_canon      = function_exists( 'hu_e3_canon' ) ? hu_e3_canon() : [];
$e3_case_url   = $e3_canon['url'] ?? home_url( '/case-study-solar-leadgenerierung/' );
$e3_case_label = $e3_canon['case_label'] ?? 'mittelständischer PV-Installationsbetrieb';
$e3_metric     = static function ( $key, $field = 'display', $fallback = '' ) {
	return function_exists( 'hu_e3_metric' ) ? hu_e3_metric( $key, $field, $fallback ) : $fallback;
};

/*
 * Preisrahmen der drei Wege. Alle drei Betraege stehen im Pricing-Canon;
 * die Seite rechnet nichts und rundet nichts.
 *
 * Der direkte Weg nennt HU_FREELANCER_WEBSITE_MIN — denselben Anker, den
 * /wordpress-freelancer-hannover/ als Einstieg zeigt. Der Agenturweg nennt
 * den Test-Sprint als unterste Sprosse seiner Leiter; ein Stundensatz steht
 * hier bewusst nicht, weil er auf der Zielseite ausdruecklich nicht steht
 * (siehe den Kommentar an HU_WHITELABEL_TEST_SPRINT_PRICE). Der Energieweg
 * nennt Einstieg und Aufbau, weil beides dort zwei getrennte Stufen sind.
 *
 * Kein Betrag steht hier als Literal, auch nicht als Fallback hinter einem
 * function_exists(): der Canon wird in functions.php unbedingt geladen, und
 * ein Fallback-Literal waere genau die zweite Fassung, die der
 * Canon-Drift-Guard verhindern soll.
 */
$preis_direkt     = hu_freelancer_website_price();
$preis_agentur    = hu_format_eur( HU_WHITELABEL_TEST_SPRINT_PRICE );
$retainer_agentur = hu_format_eur( HU_WHITELABEL_RETAINER_MIN );
$preis_energie    = hu_entry_setup_price();
$aufbau_energie   = hu_foundation_price_display();

$wege = [
	[
		'nr'      => '01',
		'titel'   => 'Direkt für Unternehmen',
		'fuerwen' => 'Sie haben eine Website · oder brauchen eine',
		'text'    => 'Relaunch, Weiterentwicklung oder technische Reparatur an einer bestehenden Seite. Mit Tracking, das von Anfang an mitgebaut wird, statt hinterher aufgesetzt zu werden. Danach optional ein Weiterentwicklungs-Retainer mit festem Stundenkontingent.',
		'preis'   => 'ab ' . $preis_direkt,
		'notiz'   => 'netto · Umfang vor Start geklärt',
		'url'     => $freelancer_url,
		'action'  => 'home_door_freelancer',
	],
	[
		'nr'      => '02',
		'titel'   => 'Für Agenturen, unter Ihrem Namen',
		'fuerwen' => 'Sie haben den Kunden · ich die Umsetzung',
		'text'    => sprintf(
			'WordPress, Tracking und technische Delivery für Ihre Kundenprojekte. Ich tauche gegenüber Ihrem Kunden nicht auf, arbeite in Ihren Zugängen und liefere so, dass Ihr Team es übernehmen kann. Laufende Kapazität später als Kontingent ab %s im Monat.',
			$retainer_agentur
		),
		'preis'   => 'ab ' . $preis_agentur,
		'notiz'   => 'netto · Erstprojekt zum Festpreis',
		'url'     => $whitelabel_url,
		'action'  => 'home_door_whitelabel',
	],
	[
		'nr'      => '03',
		'titel'   => 'Solar, Wärmepumpe, Speicher',
		'fuerwen' => 'Sie kaufen Anfragen · und wollen eigene',
		'text'    => 'Die vollständige Anfragestrecke für Installationsbetriebe: eigene Seite, Vorqualifizierung vor dem ersten Anruf, serverseitige Messung, Übergabe an den Vertrieb ohne Verzögerung. Der einzige Weg mit einem dokumentierten Fall über sechs Monate.',
		'preis'   => 'ab ' . $preis_energie,
		'notiz'   => 'Einstieg · Aufbau ' . $aufbau_energie,
		'url'     => $energy_url,
		'action'  => 'home_door_energy',
	],
];

/*
 * Die Messwerte dieser Seite. Sie leben im E3-Canon, damit Startseite und
 * Ergebnisse-Hub nicht zwei Staende derselben Messung zeigen. Lighthouse
 * ist ein Labormesswert und ersetzt keine Felddaten — der Nachweissatz
 * unter der Zeile fuehrt das mit.
 */
$messwerte = [
	[
		'wert'  => $e3_metric( 'site_lighthouse_accessibility', 'display', '100' ),
		'text'  => 'Barrierefreiheit',
	],
	[
		'wert'  => $e3_metric( 'site_lighthouse_seo', 'display', '100' ),
		'text'  => 'SEO und Best Practices',
	],
	[
		'wert'  => $e3_metric( 'site_lighthouse_performance', 'display', '99' ),
		'text'  => 'PageSpeed mobil',
	],
	[
		'wert'   => 'aktiv',
		'text'   => 'Server-Side-Tracking, GA4 und sGTM',
		'url'    => $tracking_url,
		'action' => 'home_proof_tracking_page',
	],
];

$strecke = [
	[
		'schritt' => '01 Sichtbarkeit',
		'titel'   => 'Der Weg auf die Seite',
		'text'    => 'Anzeige, Suche oder Empfehlung führt auf eine Seite, die technisch sauber lädt und für die Frage gebaut ist, mit der jemand kommt.',
	],
	[
		'schritt' => '02 Anfrage',
		'titel'   => 'Vom Besuch zur Anfrage',
		'text'    => 'Ein Formular, das vorqualifiziert statt nur Felder abzufragen. Wer nicht passt, bricht ab, bevor jemand Zeit investiert hat.',
	],
	[
		'schritt' => '03 Messung',
		'titel'   => 'Woher die Anfrage kam',
		'text'    => 'Serverseitiges Tracking auf eigenem Server. Jede Anfrage bleibt bis zur auslösenden Quelle zurückverfolgbar, auch ohne Cookie-Zustimmung.',
	],
	[
		'schritt' => '04 Übergabe',
		'titel'   => 'Von der Anfrage zum Vertrieb',
		'text'    => 'Anschluss an CRM oder Postfach, Benachrichtigung ohne Verzögerung, und am Ende eine dokumentierte Übergabe an Ihr Team.',
	],
];

$arbeitsweise = [
	[
		'titel' => 'Kein Projektmanager',
		'text'  => 'Sie sprechen mit der Person, die auch baut. Was besprochen wird, muss nicht erst weitergegeben werden.',
	],
	[
		'titel' => 'Scope und Preis vor Start',
		'text'  => 'Was gebaut wird und was es kostet, steht schriftlich fest, bevor die erste Zeile entsteht. Änderungen werden benannt, nicht nachberechnet.',
	],
	[
		'titel' => 'Code und Konten bleiben bei Ihnen',
		'text'  => 'Domain, Hosting, Werbe- und Tracking-Konten laufen auf Ihren Namen. Ein Wechsel kostet Sie keine Neuentwicklung.',
	],
];

$eignung_ja = [
	[ 'Die Übergänge zählen', 'Seite, Messung und Anfrageübergabe sollen als eine Strecke funktionieren, nicht als drei Gewerke.' ],
	[ 'Anfragen sind das Ziel', 'Die Seite soll etwas auslösen, nicht nur besser aussehen.' ],
	[ 'Sie wollen die Zugänge behalten', 'Code, Konten und Daten sollen Ihnen gehören, auch nach der Zusammenarbeit.' ],
	[ 'Eine Person entscheidet', 'Jemand mit Entscheidungsbefugnis ist erreichbar. Kein Gremium.' ],
];

$eignung_nein = [
	[ 'Rein visuelles Redesign', 'Wenn es nur schöner werden soll, ist eine Designagentur die bessere Adresse.' ],
	[ 'Einfache Visitenkarten-Seite', 'Fünf Seiten ohne Anfrageziel bekommen Sie anderswo günstiger.' ],
	[ 'Rund-um-die-Uhr-Bereitschaft', 'Ich sage keine Reaktionszeit im Störfall zu. Wer das braucht, braucht ein Team.' ],
	[ 'Der Preis entscheidet allein', 'Ich bin nicht der günstigste Anbieter und will es nicht sein.' ],
];

$protokoll = [
	[ 'Antwort', $response_value ],
	[ 'Sitz', 'Pattensen · Hannover' ],
	[ 'Arbeitsweise', 'remote in DACH, 1:1' ],
	[ 'Messung', 'ohne Cookie-Banner' ],
];

if ( '' !== $phone_display ) {
	$protokoll[] = [ 'Telefon', $phone_display ];
}

if ( function_exists( 'hu_enqueue_css' ) ) {
	hu_enqueue_css( 'nexus-startseite-css', 'startseite.css', [ 'nexus-system-css' ] );
}

if ( function_exists( 'hu_enqueue_js' ) ) {
	hu_enqueue_js( 'nexus-startseite-js', 'startseite.js', [] );
}

get_header();
?>

<div class="doku startseite" id="top" data-track-section="homepage">

	<!-- ══ Dokumentkopf ══════════════════════════════════════════ -->
	<div class="blatt kopfteil">
		<div class="reihe">
			<div class="breit">
				<p class="gegenstand">WordPress · Tracking · Conversion · Pattensen bei Hannover, remote in DACH</p>

				<h1>Die Strecke vom Klick bis zur Anfrage — gebaut und gemessen.</h1>

				<p class="aufriss">
					<span class="erst">Ich baue WordPress-Seiten, auf denen Anfragen entstehen, und die Messung, die zeigt, woher sie kamen.</span>
					Formular, Tracking und der Anschluss an Ihren Vertrieb gehören dabei zusammen, nicht in drei getrennte Rechnungen. Code, Konten und Daten bleiben bei Ihnen. Ich arbeite allein und 1:1, ohne Projektmanager dazwischen.
				</p>

				<div class="meta">
					<dl>
						<div>
							<dt>Für wen</dt>
							<dd>Unternehmen, Agenturen und Solarbetriebe im DACH-Raum</dd>
						</div>
						<div>
							<dt>Einstieg</dt>
							<dd>Projekt kurz beschreiben · <?php echo esc_html( $response_compact ); ?></dd>
						</div>
						<div>
							<dt>Rahmen</dt>
							<dd>Projekte ab <span class="zahl"><?php echo esc_html( $preis_direkt ); ?></span>&nbsp;netto · Solar-Aufbau <span class="zahl"><?php echo esc_html( $aufbau_energie ); ?></span></dd>
						</div>
						<div>
							<dt>Arbeitsweise</dt>
							<dd>Scope und Preis vor Start · Übergabe dokumentiert</dd>
						</div>
					</dl>
				</div>

				<div class="ausgang">
					<a class="tun" href="#wege" data-track-action="home_hero_to_routes" data-track-category="navigation" data-track-section="hero">
						Passenden Weg finden <span class="pf" aria-hidden="true">&rarr;</span>
					</a>
					<a class="tun still" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_head_contact" data-track-category="lead_gen" data-track-section="hero">
						Projekt beschreiben
					</a>
				</div>
			</div>
		</div>

		<!-- ══ Pruefstand ════════════════════════════════════════ -->
		<div class="reihe pruefstand" id="pruefstand" data-track-section="beweis">
			<div class="ganz tafel">
				<div class="kopfzeile">
					<h2 id="pruefstand-h">Ob ich das kann, prüfen Sie an dieser Seite. Jetzt, in dreißig Sekunden.</h2>
					<span class="mono">Lighthouse · mobil · öffentlich nachmessbar</span>
				</div>

				<div class="messwerte">
					<?php foreach ( $messwerte as $messwert ) : ?>
						<div>
							<?php if ( ! empty( $messwert['url'] ) ) : ?>
								<a href="<?php echo esc_url( (string) $messwert['url'] ); ?>" data-track-action="<?php echo esc_attr( (string) $messwert['action'] ); ?>" data-track-category="proof" data-track-section="beweis">
									<span class="w"><?php echo esc_html( (string) $messwert['wert'] ); ?></span>
									<span class="b"><?php echo esc_html( (string) $messwert['text'] ); ?></span>
								</a>
							<?php else : ?>
								<span class="w"><?php echo esc_html( (string) $messwert['wert'] ); ?></span>
								<span class="b"><?php echo esc_html( (string) $messwert['text'] ); ?></span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="nachweis">
					<p>Diese Werte gelten für die Seite, auf der Sie gerade sind — als Labormessung, nicht als Felddaten. Keine Referenzliste, keine Logowand: Sie tippen die Adresse in PageSpeed Insights und sehen selbst, was herauskommt. Ein Anbieter, dessen eigene Seite langsam ist, baut Ihnen keine schnelle.</p>
					<a class="textlink" href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener" data-track-action="home_proof_pagespeed" data-track-category="proof" data-track-section="beweis">Diese Seite jetzt messen ↗</a>
				</div>
			</div>
		</div>
	</div>

	<!-- ══ 01 Drei Wege ══════════════════════════════════════════ -->
	<section id="wege" data-track-section="tueren" aria-labelledby="wege-h">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel">
					<span class="nr">01</span>
					<span class="titel">Drei Wege</span>
					<span class="strich" aria-hidden="true"></span>
				</div>
			</div>

			<div class="voll">
				<h2 class="kopf" id="wege-h">Drei Wege hinein. Einer davon ist Ihrer.</h2>

				<p class="vorspann">Die Arbeit dahinter ist in allen drei Fällen dieselbe. Was sich unterscheidet, ist der Markt, der Name auf der Rechnung und wer danach weiterarbeitet. Der Preisrahmen steht dabei, damit Sie nicht drei Seiten weit lesen müssen, um zu merken, dass es nicht passt.</p>

				<div class="wege">
					<?php foreach ( $wege as $weg ) : ?>
						<a class="weg-z" href="<?php echo esc_url( (string) $weg['url'] ); ?>" data-track-action="<?php echo esc_attr( (string) $weg['action'] ); ?>" data-track-category="navigation" data-track-section="tueren">
							<span class="i" aria-hidden="true"><?php echo esc_html( (string) $weg['nr'] ); ?></span>

							<div>
								<h3><?php echo esc_html( (string) $weg['titel'] ); ?></h3>
								<span class="fuerwen"><?php echo esc_html( (string) $weg['fuerwen'] ); ?></span>
							</div>

							<p><?php echo esc_html( (string) $weg['text'] ); ?></p>

							<div class="rahmen">
								<span class="p"><?php echo esc_html( (string) $weg['preis'] ); ?></span>
								<span class="n"><?php echo esc_html( (string) $weg['notiz'] ); ?></span>
								<span class="pf" aria-hidden="true">&rarr;</span>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- ══ 02 Die Strecke ════════════════════════════════════════ -->
	<section id="strecke" data-track-section="strecke" aria-labelledby="strecke-h">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel">
					<span class="nr">02</span>
					<span class="titel">Die Strecke</span>
					<span class="strich" aria-hidden="true"></span>
				</div>
			</div>

			<div class="voll">
				<h2 class="kopf" id="strecke-h">Was in jedem der drei Wege gleich ist.</h2>

				<p class="vorspann">Vier Übergänge, an denen Projekte scheitern, wenn niemand sie zusammen denkt. Genau dort liegt die Arbeit, unabhängig davon, wessen Name auf der Rechnung steht.</p>

				<div class="gemein">
					<?php foreach ( $strecke as $station ) : ?>
						<div>
							<span class="s"><?php echo esc_html( (string) $station['schritt'] ); ?></span>
							<h3><?php echo esc_html( (string) $station['titel'] ); ?></h3>
							<p><?php echo esc_html( (string) $station['text'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="arbeit">
					<?php foreach ( $arbeitsweise as $punkt ) : ?>
						<div>
							<h3><?php echo esc_html( (string) $punkt['titel'] ); ?></h3>
							<p><?php echo esc_html( (string) $punkt['text'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- ══ 03 Der Fall ═══════════════════════════════════════════ -->
	<section id="fall" data-track-section="beleg" aria-labelledby="fall-h">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel">
					<span class="nr">03</span>
					<span class="titel">Der Fall</span>
					<span class="strich" aria-hidden="true"></span>
				</div>
			</div>

			<div class="voll">
				<h2 class="kopf" id="fall-h">Ein Fall, sechs Monate, beziffert.</h2>

				<p class="vorspann">Ein <?php echo esc_html( $e3_case_label ); ?> in DACH. Die Zahlen zeigen das Gesamtsystem aus Seite, Messung und Vertriebsanschluss. Sie isolieren keinen einzelnen Baustein und sind keine Prognose für ein anderes Projekt.</p>

				<div class="fall tafel">
					<div class="fallzeile">
						<div>
							<span class="w"><?php echo esc_html( $e3_metric( 'cpl_before', 'display', '150 €' ) ); ?> → <?php echo esc_html( $e3_metric( 'cpl_after', 'display', '22 €' ) ); ?></span>
							<span class="b">Kosten pro qualifizierter Anfrage, <?php echo esc_html( $e3_metric( 'cpl_reduction', 'display', 'über 85 %' ) ); ?> weniger</span>
						</div>
						<div>
							<span class="w"><?php echo esc_html( $e3_metric( 'lead_count', 'display', '1.750+' ) ); ?></span>
							<span class="b">qualifizierte Anfragen in <?php echo esc_html( $e3_metric( 'timeframe', 'display_dative', '6 Monaten' ) ); ?></span>
						</div>
						<div>
							<span class="w"><?php echo esc_html( $e3_metric( 'sales_conversion', 'display', '15 %' ) ); ?></span>
							<span class="b">Abschlussquote von der Anfrage zum Vertrag, inklusive Beitrag des Vertriebs</span>
						</div>
					</div>

					<p class="methodik">Drei Monate Vorbereitung, ab Monat drei stabil. In den ersten Wochen passiert wenig sichtbar, weil ohne Datengrundlage nichts zu optimieren ist. Wer damit rechnet, hält es aus.</p>
				</div>

				<div class="ausgang">
					<a class="textlink" href="<?php echo esc_url( $e3_case_url ); ?>" data-track-action="home_case_study" data-track-category="proof" data-track-section="beleg">Den Fall im Detail lesen &rarr;</a>
				</div>
			</div>
		</div>
	</section>

	<!-- ══ 04 Eignung ════════════════════════════════════════════ -->
	<section id="eignung" data-track-section="abgrenzung" aria-labelledby="eignung-h">
		<div class="blatt reihe">
			<div class="spalte-links">
				<div class="kapitel">
					<span class="nr">04</span>
					<span class="titel">Eignung</span>
					<span class="strich" aria-hidden="true"></span>
				</div>
			</div>

			<div class="voll">
				<h2 class="kopf leise" id="eignung-h">Wann ich der Richtige bin. Und wann nicht.</h2>

				<p class="vorspann">Damit Sie das nicht erst im dritten Gespräch merken.</p>

				<div class="eignung">
					<div class="ja">
						<span class="mono">Passt</span>
						<ul>
							<?php foreach ( $eignung_ja as $punkt ) : ?>
								<li>
									<b><?php echo esc_html( $punkt[0] ); ?></b>
									<span><?php echo esc_html( $punkt[1] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div>
						<span class="mono">Passt nicht</span>
						<ul>
							<?php foreach ( $eignung_nein as $punkt ) : ?>
								<li>
									<b><?php echo esc_html( $punkt[0] ); ?></b>
									<span><?php echo esc_html( $punkt[1] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ══ Kontakt ═══════════════════════════════════════════════ -->
	<div class="abschluss" id="kontakt" data-track-section="abschluss">
		<div class="blatt">
			<div class="tafel reihe">
				<div class="breit">
					<h2 id="kontakt-h">Beschreiben Sie Ihr Vorhaben in drei Sätzen.</h2>

					<p class="aufriss">Sie schreiben, was ansteht. Ich antworte mit einer Einschätzung, ob und wie ich das umsetzen würde, und was es ungefähr kostet. Kein Pflichtgespräch, keine Präsentation. Falls es nicht passt, erfahren Sie das <?php echo esc_html( $response_value ); ?> und nicht nach dem dritten Termin.</p>

					<div class="ausgang">
						<a class="tun" href="<?php echo esc_url( $contact_url ); ?>" data-track-action="home_close_contact" data-track-category="lead_gen" data-track-section="abschluss">
							Projekt beschreiben <span class="pf" aria-hidden="true">&rarr;</span>
						</a>
						<a class="tun still" href="<?php echo esc_url( 'mailto:' . $contact_email, [ 'mailto' ] ); ?>" data-track-action="home_close_mail" data-track-category="lead_gen" data-track-section="abschluss">
							<?php echo esc_html( $contact_email ); ?>
						</a>
					</div>
				</div>

				<div class="marg">
					<div class="protokoll">
						<?php foreach ( $protokoll as $zeile ) : ?>
							<div class="z">
								<span><?php echo esc_html( $zeile[0] ); ?></span>
								<b><?php echo esc_html( $zeile[1] ); ?></b>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
