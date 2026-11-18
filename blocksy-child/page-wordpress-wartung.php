<?php
/**
 * Template Name: WordPress-Wartung mit Anfrage-Test
 * Description: Monatliche WordPress-Wartung mit echter Testanfrage, Zustell- und Messprüfung.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes      = hu_get_commercial_route_map();
$request_url = hu_get_contact_intake_url( 'project', 'wartung' );
$monitor_url = home_url( '/anfrage-monitor/' );
$price       = hu_maintenance_price();
$shop_price  = hu_maintenance_price( true );
$check_price = hu_freelancer_takeover_check_price();
$outage      = hu_maintenance_outage_promise();

$scenarios = [
	'fail' => [
		'summary' => 'Website online. Anfrage nicht zugestellt.',
		'detail'  => 'Das Formular zeigt „Danke“, doch im Postfach kommt nichts an. Der Anfrage-Test macht den Fehler sichtbar.',
		'stages'  => [
			[ 'Formular', 'Gesendet', 'pass' ],
			[ 'Postfach', 'Nicht zugestellt', 'fail' ],
			[ 'Messung', 'Prüfung offen', 'pending' ],
		],
	],
	'pass' => [
		'summary' => 'Website online. Anfrage zugestellt.',
		'detail'  => 'Die Testanfrage kommt im Postfach an. Das Conversion-Ereignis wurde separat bestätigt.',
		'stages'  => [
			[ 'Formular', 'Gesendet', 'pass' ],
			[ 'Postfach', 'Zugestellt', 'pass' ],
			[ 'Messung', 'Bestätigt', 'pass' ],
		],
	],
];

get_header();
?>
<div id="primary" class="doku wartung" data-track-page="wordpress_maintenance">
	<header class="kopfteil" id="hero" data-track-section="maintenance_hero">
		<div class="blatt wartung-einstieg">
			<div class="wartung-einstieg__text">
				<h1>WordPress-Wartung <span>Kommen Ihre Anfragen an?</span></h1>
				<p class="aufriss">Ich halte Ihre Website aktuell und schicke jeden Monat eine echte Testanfrage. So wissen Sie, ob Ihr Formular zustellt und Ihr Tracking die Anfrage erfasst.</p>
				<p class="wartung-einstieg__preis"><strong><?php echo esc_html( $price ); ?> netto / Monat</strong><span>Zum Monatsende kündbar</span></p>
				<div class="ausgang">
					<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="maintenance_request_hero" data-track-category="lead_gen" data-track-section="maintenance_hero">Wartung anfragen <span aria-hidden="true">→</span></a>
					<a class="textlink" href="#preis" data-track-action="maintenance_hero_price" data-track-category="navigation" data-track-section="maintenance_hero">Preise und Leistungen <span aria-hidden="true">↓</span></a>
				</div>
			</div>

			<div class="wartung-vorschau">
				<div class="tafel tafel--graphit wartung-demo">
					<div class="wartung-demo__kopf">
						<span class="mono">Interaktives Beispiel</span>
						<span class="wartung-demo__online">Website erreichbar</span>
					</div>
					<fieldset class="wartung-demo__wahl">
						<legend>Anfrage-Test</legend>
						<input class="nur-vorlesen" type="radio" name="wartung-demo" id="wartung-demo-fail" checked>
						<input class="nur-vorlesen" type="radio" name="wartung-demo" id="wartung-demo-pass">
						<div class="wartung-demo__optionen">
							<label for="wartung-demo-fail">Störung</label>
							<label for="wartung-demo-pass">Zustellung klappt</label>
						</div>
						<?php foreach ( $scenarios as $scenario => $example ) : ?>
							<div class="wartung-demo__szenario" data-scenario="<?php echo esc_attr( $scenario ); ?>">
								<ol class="wartung-demo__weg" role="list">
									<?php foreach ( $example['stages'] as [ $label, $status, $state ] ) : ?>
										<li data-state="<?php echo esc_attr( $state ); ?>">
											<span class="wartung-demo__knoten" aria-hidden="true"><?php echo esc_html( [ 'pass' => '✓', 'fail' => '×', 'pending' => '–' ][ $state ] ); ?></span>
											<strong><?php echo esc_html( $label ); ?></strong>
											<span class="wartung-demo__status"><?php echo esc_html( $status ); ?></span>
										</li>
									<?php endforeach; ?>
								</ol>
								<div class="wartung-demo__befund">
									<strong><?php echo esc_html( $example['summary'] ); ?></strong>
									<p><?php echo esc_html( $example['detail'] ); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</fieldset>
				</div>
				<div class="wartung-monitor" data-monitor-url="<?php echo esc_url( rest_url( 'nexus/v1/monitor/status' ) ); ?>">
					<div>
						<strong>Anfrage-Monitor für hasimuener.de</strong>
						<p class="wartung-monitor__status" data-monitor-status>Automatische Prüfung jeden Montag</p>
					</div>
					<a class="satzlink" href="<?php echo esc_url( $monitor_url ); ?>" data-track-action="maintenance_monitor_proof" data-track-category="trust" data-track-section="maintenance_hero">Live-Verlauf ansehen <span aria-hidden="true">↗</span></a>
				</div>
			</div>
		</div>
	</header>

	<section id="preis" class="wartung-pakete" data-track-section="maintenance_price" aria-labelledby="wartung-preis-titel">
		<div class="blatt">
			<h2 class="kopf" id="wartung-preis-titel">Wartungsvertrag für Website oder Shop</h2>
			<p class="vorspann">Monatlicher Festpreis. Den Anfrage-Test und den privaten Monatsbericht bekommen Sie in beiden Varianten.</p>
			<div class="wartung-pakete__raster">
				<article class="tafel wartung-paket">
					<h3>WordPress-Website</h3>
					<p class="wartung-paket__preis"><strong><?php echo esc_html( $price ); ?></strong><span>netto / Monat</span></p>
					<p>Für Unternehmenswebsites, über die Kunden Kontakt aufnehmen oder ein Angebot anfragen.</p>
				</article>
				<article class="tafel wartung-paket">
					<h3>WooCommerce-Shop</h3>
					<p class="wartung-paket__preis"><strong><?php echo esc_html( $shop_price ); ?></strong><span>netto / Monat</span></p>
					<p>Für bestehende Shops. Im Monatstest prüfe ich zusätzlich Warenkorb und Kasse.</p>
				</article>
			</div>
			<div class="wartung-pakete__details">
				<div>
					<h3>In beiden Varianten enthalten</h3>
					<ul class="wartung-inhalt">
						<li>Monatliche Testanfrage über das Formular, mit Zustellprüfung und Kontrolle der eingerichteten Conversion-Messung.</li>
						<li>Wöchentliche WordPress-, Theme- und Plugin-Updates. Sicherheitsupdates werktags innerhalb von <?php echo esc_html( (string) HU_MAINTENANCE_SECURITY_UPDATE_HOURS ); ?> Stunden.</li>
						<li>Tägliche externe Backups mit <?php echo esc_html( (string) HU_MAINTENANCE_BACKUP_RETENTION_DAYS ); ?> Tagen Aufbewahrung sowie Überwachung von Erreichbarkeit und SSL-Zertifikat.</li>
						<li>Privater Monatsbericht per E-Mail und Reaktion bei Website- oder Formularausfall <?php echo esc_html( $outage ); ?>.</li>
					</ul>
				</div>
				<div class="wartung-uebernahme">
					<h3>Übernahme-Check vor dem Start</h3>
					<p><strong><?php echo esc_html( $check_price ); ?> netto, einmalig.</strong> Bei Buchung der Wartung rechne ich den Betrag vollständig an. Den schriftlichen Befund erhalten Sie in jedem Fall.</p>
					<div class="ausgang">
						<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="maintenance_request_price" data-track-category="lead_gen" data-track-section="maintenance_price">Wartung anfragen <span aria-hidden="true">→</span></a>
						<a class="textlink" href="<?php echo esc_url( $routes['takeover'] ); ?>" data-track-action="maintenance_to_takeover" data-track-category="navigation" data-track-section="maintenance_price">Was der Check klärt</a>
					</div>
					<p class="wartung-klein">Die Anfrage ist unverbindlich. Der Vertrag ist zum Monatsende kündbar.</p>
				</div>
			</div>
		</div>
	</section>

	<section id="blindes-feld" data-track-section="maintenance_gap" aria-labelledby="wartung-ablauf-titel">
		<div class="blatt">
			<h2 class="kopf" id="wartung-ablauf-titel">So prüfe ich Ihren Anfrageweg</h2>
			<p class="vorspann">Eine erreichbare Website kann trotzdem Anfragen verlieren. Der Monatstest macht sichtbar, an welcher Stelle dieser Weg funktioniert oder abbricht.</p>
			<ol class="wartung-schritte" id="ablauf" data-track-section="maintenance_routine">
				<li><span class="mono stempelfarbe" aria-hidden="true">01</span><h3>Formular absenden</h3><p>Ich schicke eine gekennzeichnete Testanfrage über das Kontaktformular Ihrer Website.</p></li>
				<li><span class="mono stempelfarbe" aria-hidden="true">02</span><h3>Zustellung bestätigen</h3><p>Ich prüfe, ob die Anfrage im vorgesehenen Postfach oder CRM ankommt.</p></li>
				<li><span class="mono stempelfarbe" aria-hidden="true">03</span><h3>Messung prüfen</h3><p>Bei eingerichtetem Tracking prüfe ich das Conversion-Ereignis in Analytics oder Google Ads und berücksichtige die Einwilligung.</p></li>
			</ol>
		</div>
	</section>

	<section id="bericht" data-track-section="maintenance_report" aria-labelledby="wartung-bericht-titel">
		<div class="blatt wartung-bericht">
			<div>
				<h2 class="kopf" id="wartung-bericht-titel">Ihr Monatsbericht auf einer Seite</h2>
				<p class="vorspann">Sie sehen auf einen Blick, ob die Testanfrage ankam, was gewartet wurde und welcher Punkt als Nächstes ansteht. Der Bericht kommt privat per E-Mail.</p>
			</div>
			<div class="tafel wartung-bericht__beispiel" role="group" aria-label="Beispiel eines privaten Monatsberichts">
				<p class="mono">Beispiel · Monatsbericht</p>
				<p class="wartung-bericht__ergebnis"><strong>Testanfrage zugestellt</strong><span>Postfach und Conversion-Ereignis bestätigt.</span></p>
				<dl class="wartung-bericht__daten">
					<div><dt>Updates</dt><dd>WordPress und 4 Plugins</dd></div>
					<div><dt>Backups</dt><dd>Täglich, extern</dd></div>
					<div><dt>Ausfälle</dt><dd>Keine im Berichtszeitraum</dd></div>
					<div><dt>Empfehlung</dt><dd>Ein veraltetes Plugin ersetzen</dd></div>
				</dl>
			</div>
		</div>
	</section>

	<section id="zusage" data-track-section="maintenance_promise" aria-labelledby="wartung-zusage-titel">
		<div class="blatt wartung-zusage">
			<div>
				<h2 class="kopf" id="wartung-zusage-titel">Meine Zusage für den Monatstest</h2>
				<p class="vorspann"><strong><?php echo esc_html( hu_maintenance_guarantee() ); ?></strong></p>
				<p><?php echo esc_html( hu_maintenance_guarantee( 'condition' ) ); ?></p>
			</div>
			<dl class="wartung-bericht__daten">
				<div><dt>Website- oder Formularausfall</dt><dd><?php echo esc_html( ucfirst( $outage ) ); ?></dd></div>
				<div><dt>Sonstige Fehler</dt><dd>Am nächsten Werktag</dd></div>
				<div><dt>Wochenenden und Feiertage</dt><dd>Kein Notdienst</dd></div>
				<div><dt>Angekündigte Abwesenheit</dt><dd>Die Reaktionszusage ruht. Sie erfahren es vorher.</dd></div>
			</dl>
		</div>
	</section>

	<section id="fragen" data-track-section="maintenance_faq" aria-labelledby="wartung-fragen-titel">
		<div class="blatt wartung-fragen">
			<div>
				<h2 class="kopf leise" id="wartung-fragen-titel">Häufige Fragen zur WordPress-Wartung</h2>
				<p class="vorspann"><a class="satzlink" href="<?php echo esc_url( home_url( '/wordpress-wartung-kosten/' ) ); ?>" data-track-action="maintenance_to_cost_guide" data-track-category="internal_link" data-track-section="maintenance_faq">Ratgeber zu Wartungskosten und Eigenaufwand <span aria-hidden="true">→</span></a></p>
			</div>
			<div class="fragen" data-faq-group>
				<?php foreach ( hu_maintenance_faq_items() as $item ) : ?>
					<details name="wartung-faq">
						<summary><?php echo esc_html( $item['question'] ); ?></summary>
						<div class="huelle"><div><p class="antwort"><?php echo esc_html( $item['answer'] ); ?></p></div></div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="abschluss" id="anfrage" data-track-section="maintenance_close" aria-labelledby="wartung-abschluss-titel">
		<div class="blatt">
			<div class="tafel wartung-abschluss">
				<div>
					<h2 id="wartung-abschluss-titel">Welche Website soll ich warten?</h2>
					<p>Schicken Sie mir die Adresse Ihrer Website. Ich melde mich <?php echo esc_html( hu_response_promise( 'window' ) ); ?>. Wenn es passt, besprechen wir den Übernahme-Check und den Start.</p>
				</div>
				<div class="wartung-abschluss__aktion">
					<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="maintenance_request_close" data-track-category="lead_gen" data-track-section="maintenance_close">Wartung anfragen <span aria-hidden="true">→</span></a>
					<p class="wartung-klein">Unverbindlich und ohne Zugangsdaten.</p>
				</div>
			</div>
		</div>
	</section>
</div>
<?php get_footer(); ?>
