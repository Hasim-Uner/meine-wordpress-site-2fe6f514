<?php
/**
 * Template Name: WordPress-Wartung mit Anfrage-Test
 * Description: Monatliche WordPress-Wartung mit echter Testanfrage, Zustell- und Messprüfung.
 *
 * Money Page der nationalen Wartungsroute. Preise und Zusagen kommen aus
 * canon/pricing-canon.php, die Antwortzeit aus canon/messaging-canon.php, die
 * Fragen aus inc/wordpress-wartung.php (dieselbe Quelle wie das FAQPage-Schema).
 * Metadaten und Service-Schema stehen in ihren zentralen Modulen. Alle CTAs
 * fuehren auf das bestehende Kontaktformular mit focus=wartung.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes       = hu_get_commercial_route_map();
$request_url  = hu_get_contact_intake_url( 'project', 'wartung' );
$monitor_url  = home_url( '/anfrage-monitor/' );
$price        = hu_maintenance_price();
$shop_price   = hu_maintenance_price( true );
$check_price  = hu_freelancer_takeover_check_price();
$retainer     = hu_freelancer_retainer_display();
$outage       = hu_maintenance_outage_promise();
$faq          = hu_maintenance_faq_items();

// Live-Beleg: der Anfrage-Monitor dieser Website. Ohne eingerichteten
// Monitor bleibt nur der Verweis, keine Behauptung ueber einen Zustand.
$monitor        = function_exists( 'hu_monitor_cached_status' ) ? hu_monitor_cached_status() : [];
$monitor_line   = '';
$monitor_state  = 'none';
if ( ! empty( $monitor['enabled'] ) && ! empty( $monitor['anfragen'] ) && 'none' !== ( $monitor['anfragen']['state'] ?? 'none' ) ) {
	$monitor_state = (string) $monitor['anfragen']['state'];
	$monitor_when  = function_exists( 'hu_monitor_human_time' ) ? hu_monitor_human_time( $monitor['anfragen']['at'] ?? null ) : '';
	$monitor_line  = (string) $monitor['anfragen']['label'] . ( '' !== $monitor_when ? ' · letzter Lauf ' . $monitor_when : '' );
}

$test_rows = [
	[ 'ok', 'WordPress-Kern aktuell', 'aktuell' ],
	[ 'ok', 'Plugins aktualisiert', '23 von 23' ],
	[ 'ok', 'Backup vor dem Update', 'extern' ],
	[ 'ok', 'Erreichbar, SSL gültig', '200 OK' ],
	'teiler',
	[ 'ok', 'Testanfrage über das Formular', 'gesendet' ],
	[ 'fail', 'Im Postfach angekommen', 'Mailversand abgelaufen' ],
	[ 'fail', 'Conversion gezählt', 'vom Banner blockiert' ],
];

$breaks = [
	[ 'Ein Plugin-Update ändert den Mailversand.', 'Das Formular zeigt „Danke“, die Mail kommt nie an.' ],
	[ 'Das Passwort des Mailversands läuft ab.', 'Ab diesem Tag landet jede Anfrage im Nichts, ohne Fehlermeldung.' ],
	[ 'Der Cookie-Banner bekommt ein Update.', 'Er blockiert die Messung. Google Ads sieht keine Anfragen mehr und wirbt danach schlechter.' ],
	[ 'Ein Cache zeigt ein altes Formular.', 'Es sieht richtig aus, schickt aber an eine alte Adresse.' ],
];

$steps = [
	[ 'Sichern', 'Backup vor jedem Eingriff, extern gespeichert.' ],
	[ 'Aktualisieren', 'WordPress, Theme und Plugins.' ],
	[ 'Testanfrage', 'Eine echte Anfrage über Ihr Formular.', true ],
	[ 'Ankunft prüfen', 'Kommt sie im Postfach oder CRM an?', true ],
	[ 'Messung prüfen', 'Wird die Anfrage in Analytics oder Google Ads gezählt?', true ],
	[ 'Bericht', 'Erste Zeile: Anfragen kommen an, ja oder nein.' ],
];

$included = [
	[ 'Anfrage-Test', 'Monatlich eine echte Testanfrage mit Prüfung der Zustellung' ],
	[ 'Messung', 'Prüfung, ob die Anfrage in Analytics oder Google Ads zählt' ],
	[ 'Sicherheit', sprintf( 'Sicherheitsupdates werktags innerhalb von %d Stunden, alle übrigen Updates wöchentlich', HU_MAINTENANCE_SECURITY_UPDATE_HOURS ) ],
	[ 'Backups', sprintf( 'Täglich, extern gespeichert, %d Tage aufbewahrt', HU_MAINTENANCE_BACKUP_RETENTION_DAYS ) ],
	[ 'Überwachung', 'Erreichbarkeit und SSL-Zertifikat' ],
	[ 'Bericht', 'Einmal im Monat per Mail, nur für Sie, eine Seite' ],
];

get_header();
?>
<div id="primary" class="doku wartung" data-track-page="wordpress_maintenance">
	<header class="kopfteil" id="hero" data-track-section="maintenance_hero">
		<div class="blatt wartung-einstieg">
			<div class="wartung-einstieg__text">
				<p class="gegenstand">WordPress-Wartung · mit Anfrage-Test</p>
				<h1>WordPress-Wartung, die prüft, ob Ihre Anfragen ankommen.</h1>
				<p class="aufriss">Updates und Backups gehören zu jeder Wartung. Ich prüfe zusätzlich jeden Monat mit einer echten Testanfrage: Kommt sie bei Ihnen an? Wird sie gezählt?</p>
				<div class="ausgang">
					<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="maintenance_request_hero" data-track-category="lead_gen" data-track-section="maintenance_hero">Wartung anfragen <span aria-hidden="true">→</span></a>
					<a class="textlink" href="#bericht" data-track-action="maintenance_hero_report" data-track-category="navigation" data-track-section="maintenance_hero">So sieht der Monatsbericht aus</a>
				</div>
				<div class="meta">
					<dl>
						<div><dt>Preis</dt><dd><span class="zahl"><?php echo esc_html( $price ); ?></span> netto im Monat</dd></div>
						<div><dt>Laufzeit</dt><dd>zum Monatsende kündbar</dd></div>
						<div><dt>Bei Ausfall</dt><dd>Reaktion in <?php echo esc_html( hu_maintenance_outage_promise( 'short' ) ); ?>, <?php echo esc_html( hu_maintenance_outage_promise( 'window' ) ); ?></dd></div>
					</dl>
				</div>
			</div>

			<figure class="tafel tafel--graphit wartung-test" aria-labelledby="wartung-test-titel">
				<figcaption class="wartung-test__kopf">
					<span class="mono" id="wartung-test-titel">Monatstest · ihre-firma.de</span>
					<span class="mono wartung-test__marke">Beispiel</span>
				</figcaption>
				<ul class="wartung-test__zeilen">
					<?php $hu_row = 0; ?>
					<?php foreach ( $test_rows as $row ) : ?>
						<?php if ( 'teiler' === $row ) : ?>
							<li class="wartung-test__teiler mono">Was übliche Wartung nicht prüft</li>
						<?php else : ?>
						<li class="wartung-test__zeile" data-state="<?php echo esc_attr( $row[0] ); ?>" style="--i: <?php echo (int) $hu_row++; ?>">
							<span class="wartung-test__zeichen" aria-hidden="true"><?php echo 'ok' === $row[0] ? '✓' : '✕'; ?></span>
							<span><?php echo esc_html( $row[1] ); ?><span class="nur-vorlesen">: <?php echo 'ok' === $row[0] ? 'in Ordnung' : 'Störung'; ?></span></span>
							<span class="zahl wartung-test__wert"><?php echo esc_html( $row[2] ); ?></span>
						</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<p class="wartung-test__ergebnis"><strong>Die Website läuft. Anfragen kommen nicht an.</strong> <span>Der Monatstest findet das. Ich behebe es, bevor ein Kunde nachfragt.</span></p>
			</figure>
		</div>
	</header>

	<section id="blindes-feld" data-track-section="maintenance_gap" aria-labelledby="wartung-gap-titel">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">01</span><span class="titel">Das blinde Feld</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="wartung-gap-titel">Grüne Häkchen sagen nichts darüber, ob Anfragen ankommen.</h2>
				<p class="vorspann">Übliche Wartung prüft nur, ob die Website erreichbar ist. Ob Anfragen ankommen, prüft sie nicht. Das Formular zeigt trotzdem „Danke“. So fällt der Fehler oft wochenlang niemandem auf.</p>
			</div>
			<div class="voll">
				<ol class="wartung-brueche">
					<?php foreach ( $breaks as $index => $break ) : ?>
						<li><span class="zahl stempelfarbe" aria-hidden="true"><?php echo esc_html( chr( 65 + $index ) ); ?></span><div><h3><?php echo esc_html( $break[0] ); ?></h3><p><?php echo esc_html( $break[1] ); ?></p></div></li>
					<?php endforeach; ?>
				</ol>
				<div class="tabelle" tabindex="0" role="region" aria-labelledby="wartung-vergleich-titel">
					<table>
						<caption id="wartung-vergleich-titel" class="wartung-caption">Was geprüft wird</caption>
						<thead><tr><th scope="col">Prüfpunkt</th><th scope="col">Übliche Wartung</th><th scope="col">Diese Wartung</th></tr></thead>
						<tbody>
							<tr><th scope="row">Updates, Backups, Erreichbarkeit</th><td>ja</td><td>ja</td></tr>
							<tr><th scope="row">Formular lässt sich absenden</th><td>teilweise</td><td>ja</td></tr>
							<tr><th scope="row">Anfrage kommt im Postfach oder CRM an</th><td>nein</td><td><strong>ja, mit echter Testanfrage</strong></td></tr>
							<tr><th scope="row">Anfrage wird gemessen, Einwilligung greift</th><td>nein</td><td><strong>ja</strong></td></tr>
							<tr><th scope="row">Erste Zeile im Bericht</th><td>Liste der Plugins</td><td><strong>Anfragen kommen an: ja oder nein</strong></td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>

	<section id="ablauf" data-track-section="maintenance_routine" aria-labelledby="wartung-ablauf-titel">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">02</span><span class="titel">Jeden Monat</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="wartung-ablauf-titel">Sechs Schritte, in fester Reihenfolge.</h2>
				<p class="vorspann">Erst sichern, dann aktualisieren, dann prüfen. Die Schritte 3 bis 5 macht übliche Wartung nicht.</p>
			</div>
			<div class="voll">
				<ol class="wartung-schritte">
					<?php foreach ( $steps as $index => $step ) : ?>
						<li<?php echo ! empty( $step[2] ) ? ' class="ist-test"' : ''; ?>><span class="zahl" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span><h3><?php echo esc_html( $step[0] ); ?></h3><p><?php echo esc_html( $step[1] ); ?></p></li>
					<?php endforeach; ?>
				</ol>
			</div>
		</div>
	</section>

	<section id="bericht" data-track-section="maintenance_report" aria-labelledby="wartung-bericht-titel">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">03</span><span class="titel">Der Bericht</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="wartung-bericht-titel">Ihr Bericht beginnt mit einer Frage: Kommen Anfragen an?</h2>
				<p class="vorspann">Den Bericht bekommen nur Sie, einmal im Monat per Mail. Darunter steht, was ich geändert habe und was ich empfehle. Eine Seite, ohne Fachsprache.</p>
				<div class="wartung-bericht" role="group" aria-label="Beispiel eines Monatsberichts">
					<p class="wartung-bericht__kopf"><span class="mono">Bericht · Oktober · ihre-firma.de</span><span class="mono">Privat · Beispiel</span></p>
					<p class="mono">Anfragen kommen an</p>
					<p class="wartung-bericht__antwort">Ja</p>
					<p class="wartung-bericht__beleg">Testanfrage am 3. Oktober gesendet, nach 40 Sekunden im Postfach, als Conversion gezählt.</p>
					<div class="protokoll">
						<div class="z"><span>Updates</span><b>Kern und 4 Plugins</b></div>
						<div class="z"><span>Backups</span><b>täglich, extern</b></div>
						<div class="z"><span>Ausfälle</span><b>keine</b></div>
						<div class="z"><span>Empfehlung</span><b>1 Plugin ersetzen</b></div>
					</div>
				</div>
			</div>
			<aside class="marg">
				<p class="note"><span class="label">Mein eigener Beweis</span>Auf hasimuener.de läuft dieselbe Prüfung jeden Morgen. Das Ergebnis zeige ich öffentlich, damit Sie es prüfen können. Für Ihre Website gibt es keine öffentliche Seite. Ihr Ergebnis steht nur in Ihrem Bericht.
					<?php if ( '' !== $monitor_line ) : ?>
						<b class="wartung-live" data-state="<?php echo esc_attr( $monitor_state ); ?>"><?php echo esc_html( $monitor_line ); ?></b>
					<?php endif; ?>
				</p>
				<p class="note"><a class="satzlink" href="<?php echo esc_url( $monitor_url ); ?>" data-track-action="maintenance_monitor_proof" data-track-category="trust" data-track-section="maintenance_report">Meinen Anfrage-Monitor ansehen →</a></p>
			</aside>
		</div>
	</section>

	<section id="preis" data-track-section="maintenance_price" aria-labelledby="wartung-preis-titel">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">04</span><span class="titel">Preis</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="wartung-preis-titel">Ein Paket. Ein Preis. Zum Monatsende kündbar.</h2>
				<p class="vorspann">Keine Stufen, kein Premium-Paket. Der Anfrage-Test ist immer dabei.</p>
			</div>
			<div class="voll wartung-preis">
				<div class="tafel wartung-preis__paket">
					<p class="mono stempelfarbe">WordPress-Wartung mit Anfrage-Test</p>
					<p class="wartung-preis__betrag"><span class="zahl"><?php echo esc_html( $price ); ?></span><small>netto im Monat · WooCommerce-Shops <?php echo esc_html( $shop_price ); ?></small></p>
					<div class="protokoll posten">
						<?php foreach ( $included as $item ) : ?>
							<div class="z"><span><?php echo esc_html( $item[0] ); ?></span><b><?php echo esc_html( $item[1] ); ?></b></div>
						<?php endforeach; ?>
					</div>
					<div class="ausgang">
						<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="maintenance_request_price" data-track-category="lead_gen" data-track-section="maintenance_price">Wartung anfragen <span aria-hidden="true">→</span></a>
					</div>
					<p class="wartung-hinweis">Keine Mindestlaufzeit. Bei Shops prüft der Monatstest zusätzlich Warenkorb und Kasse.</p>
				</div>
				<div class="wartung-preis__rand">
					<div>
						<p class="mono">Vor dem Start</p>
						<h3>Übernahme-Check · <span class="zahl"><?php echo esc_html( $check_price ); ?></span></h3>
						<p>Bevor ich übernehme, prüfe ich Zugänge, Backups, Plugins, Formular und Messung. Buchen Sie danach die Wartung, wird der Preis voll angerechnet. Den Befund behalten Sie immer.</p>
						<a class="textlink" href="<?php echo esc_url( $routes['takeover'] ); ?>" data-track-action="maintenance_to_takeover" data-track-category="navigation" data-track-section="maintenance_price">Was der Check klärt</a>
					</div>
					<div>
						<p class="mono">Getrennt von der Wartung</p>
						<h3>Betreuung: Änderungen und Ausbau</h3>
						<p>Neue Seiten, Texte und Funktionen laufen über ein Stundenkontingent: <?php echo esc_html( $retainer ); ?> im Monat. So bleibt der Wartungspreis fest.</p>
					</div>
					<details class="wartung-ohne">
						<summary>Was nicht enthalten ist</summary>
						<ul>
							<li>Hosting und Plugin-Lizenzen, sie laufen über Ihr Konto</li>
							<li>Inhaltliche Änderungen, sie laufen über das Kontingent</li>
							<li>Bereinigung nach einem Angriff, nach Aufwand</li>
							<li>Notdienst nachts, am Wochenende und an Feiertagen</li>
						</ul>
					</details>
				</div>
			</div>
		</div>
	</section>

	<section id="zusage" data-track-section="maintenance_promise" aria-labelledby="wartung-zusage-titel">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">05</span><span class="titel">Zusage</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf" id="wartung-zusage-titel"><?php echo esc_html( hu_maintenance_guarantee() ); ?></h2>
				<p class="vorspann"><?php echo esc_html( hu_maintenance_guarantee( 'condition' ) ); ?></p>
				<div class="tabelle" tabindex="0" role="region" aria-labelledby="wartung-reaktion-titel">
					<table class="wartung-reaktion">
						<caption id="wartung-reaktion-titel" class="wartung-caption">Reaktionszeiten</caption>
						<thead><tr><th scope="col">Fall</th><th scope="col">Reaktion</th></tr></thead>
						<tbody>
							<tr><th scope="row">Website nicht erreichbar oder Formular stellt nicht zu</th><td><strong><?php echo esc_html( $outage ); ?></strong></td></tr>
							<tr><th scope="row">Alle anderen Fehler</th><td>am nächsten Werktag</td></tr>
							<tr><th scope="row">Wochenende und Feiertage</th><td>keine Zusage</td></tr>
							<tr><th scope="row">Angekündigte Abwesenheit</th><td>Zusage ruht, Sie erfahren es vorher</td></tr>
						</tbody>
					</table>
				</div>
			</div>
			<aside class="marg">
				<p class="note"><span class="label">Einwand</span><b>„Ein Einzelner ist ein Ausfallrisiko.“</b> Stimmt. Deshalb bleiben alle Zugänge bei Ihnen, die Backups liegen extern, und jede Änderung steht im Bericht. Hören wir auf, kann jeder Nachfolger sofort weitermachen.</p>
			</aside>
		</div>
	</section>

	<section id="fragen" data-track-section="maintenance_faq" aria-labelledby="wartung-fragen-titel">
		<div class="blatt reihe">
			<div class="spalte-links"><div class="kapitel" aria-hidden="true"><span class="nr">06</span><span class="titel">Fragen</span><span class="strich"></span></div></div>
			<div class="haupt">
				<h2 class="kopf leise" id="wartung-fragen-titel">WordPress-Wartung: Kosten, Umfang, Grenzen.</h2>
				<div class="fragen">
					<?php foreach ( $faq as $item ) : ?>
						<details><summary><?php echo esc_html( $item['question'] ); ?></summary><div class="huelle"><div><p class="antwort"><?php echo esc_html( $item['answer'] ); ?></p></div></div></details>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<section class="abschluss" id="anfrage" data-track-section="maintenance_close" aria-labelledby="wartung-abschluss-titel">
		<div class="blatt">
			<div class="tafel wartung-abschluss">
				<div>
					<p class="mono stempelfarbe">Nächster Schritt</p>
					<h2 id="wartung-abschluss-titel">Drei Schritte bis zum ersten Monatstest.</h2>
					<ol class="wartung-start">
						<li><span class="zahl">01</span><span><b>Anfrage senden.</b> Website-Adresse und eine Zeile zur bisherigen Betreuung genügen.</span></li>
						<li><span class="zahl">02</span><span><b>Übernahme-Check.</b> Sie bekommen den Befund, auch wenn Sie danach nicht buchen.</span></li>
						<li><span class="zahl">03</span><span><b>Wartung läuft.</b> Die erste Prüfung läuft im Folgemonat, der Bericht kommt per Mail an Sie.</span></li>
					</ol>
				</div>
				<div class="wartung-abschluss__aktion">
					<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="maintenance_request_close" data-track-category="lead_gen" data-track-section="maintenance_close">Wartung anfragen <span aria-hidden="true">→</span></a>
					<p class="wartung-hinweis"><?php echo esc_html( hu_response_promise( 'sentence' ) ); ?> Die Anfrage ist unverbindlich.</p>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
get_footer();
