<?php
/**
 * Template Name: WordPress-Übernahme und Weiterentwicklung
 * Description: Bestehende WordPress-Websites prüfen und anschließend gezielt weiterentwickeln.
 *
 * Hardcoded offer page. Prices and reference scope use the existing canon;
 * metadata and Service schema live in their central modules. The request is
 * non-binding and uses the existing contact endpoint with the takeover focus.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes      = hu_get_commercial_route_map();
$request_url = hu_get_contact_intake_url( 'project', 'takeover' );
$price       = hu_freelancer_takeover_check_price();
$retainer    = hu_freelancer_retainer_display();
$project     = array_column( hu_reference_case_projects(), null, 'id' )['e3-new-energy'];
$proof_url   = $routes['references'] . '#' . $project['id'];
$img_url     = get_stylesheet_directory_uri() . '/assets/img/' . $project['screenshot'];

get_header();
?>
<div class="doku uebernahme" id="takeover-content" data-track-page="wordpress_takeover">
	<header class="blatt kopfteil uebernahme-einstieg">
		<div>
			<p class="gegenstand">WordPress-Übernahme & Weiterentwicklung</p>
			<h1>Ihre WordPress-Website weiterentwickeln.</h1>
			<p class="aufriss">Die bisherige Betreuung ist weg, Änderungen bleiben liegen oder der technische Stand ist unklar? Ich prüfe Ihre bestehende Website und kläre, was sich weiterverwenden lässt und was zuerst verbessert werden sollte.</p>
			<a class="textlink" href="#beleg" data-track-action="takeover_hero_proof" data-track-category="trust" data-track-section="takeover_intro">Weiterentwicklung am Beispiel E3 New Energy <span aria-hidden="true">↓</span></a>
		</div>
		<aside class="tafel uebernahme-angebot" aria-labelledby="takeover-offer-title">
			<p class="gegenstand" id="takeover-offer-title">Der Einstieg: Übernahme-Check</p>
			<p class="uebernahme-preis"><strong class="zahl"><?php echo esc_html( $price ); ?></strong><span>netto · einmalig</span></p>
			<p>Schriftlicher Befund mit Prioritäten und Festpreis für den nächsten Schritt.</p>
			<p>Bei Beauftragung des Folgeprojekts wird der Check verrechnet.</p>
			<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="takeover_request_hero" data-track-category="lead_gen" data-track-section="takeover_intro">Übernahme-Check anfragen <span aria-hidden="true">→</span></a>
			<p class="uebernahme-hinweis">Die Anfrage ist unverbindlich. Der kostenpflichtige Check beginnt erst nach Beauftragung.</p>
		</aside>
	</header>

	<section id="beleg" aria-labelledby="takeover-proof-title">
		<div class="blatt uebernahme-beleg">
			<div>
				<p class="gegenstand">Arbeitsbeleg · <?php echo esc_html( $project['name'] ); ?></p>
				<h2 class="kopf leise" id="takeover-proof-title">Auf einer bestehenden Website weiterbauen.</h2>
				<p><?php echo esc_html( $project['text'] ); ?></p>
				<p class="uebernahme-hinweis"><?php echo esc_html( $project['limit'] ); ?> Der Projektumfang ist kein Inklusivumfang des Übernahme-Checks.</p>
				<a class="textlink" href="<?php echo esc_url( $proof_url ); ?>" data-track-action="takeover_proof_reference" data-track-category="trust" data-track-section="takeover_proof">Projekt und eigenen Beitrag ansehen <span aria-hidden="true">→</span></a>
			</div>
			<figure>
				<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $project['alt'] ); ?>" width="960" height="600" loading="lazy" decoding="async">
				<figcaption>Ansicht der öffentlichen Website; der Projektanteil steht in der Referenz.</figcaption>
			</figure>
		</div>
	</section>

	<section id="uebernahme-check" aria-labelledby="takeover-check-title">
		<div class="blatt uebernahme-abschnitt">
			<div class="uebernahme-titel">
				<p class="gegenstand">Prüfen, bevor wir ändern</p>
				<h2 class="kopf" id="takeover-check-title">Was der Check klärt.</h2>
			</div>
			<dl class="uebernahme-liste">
				<div><dt>Theme und Plugins</dt><dd>Wie die Website aufgebaut ist, welche Erweiterungen sie nutzt und welche Abhängigkeiten bei Änderungen zu beachten sind.</dd></div>
				<div><dt>Updates, Backups und Zugänge</dt><dd>Welcher Stand vorliegt, welche Sicherungen vorhanden sind und ob die nötigen Konten zugänglich sind.</dd></div>
				<div><dt>Ladezeit und nächste Schritte</dt><dd>Wo technische Auffälligkeiten liegen und welche Aufgaben zuerst angegangen werden sollten.</dd></div>
			</dl>
			<div class="uebernahme-befund">
				<h3>Ihr Ergebnis: ein schriftlicher Befund.</h3>
				<p>Mit Prioritäten und Festpreis für den nächsten Schritt. Der Befund gehört Ihnen, auch wenn Sie mit jemand anderem weiterarbeiten.</p>
				<p class="uebernahme-hinweis">Der Check umfasst Prüfung und Befund. Reparaturen, Migrationen und neue Seiten werden separat vereinbart. Umfang und Termin stehen vor der Beauftragung fest.</p>
			</div>
		</div>
	</section>

	<section id="weiterentwicklung" aria-labelledby="takeover-development-title">
		<div class="blatt uebernahme-abschnitt">
			<div class="uebernahme-titel">
				<p class="gegenstand">Nach dem Befund</p>
				<h2 class="kopf" id="takeover-development-title">Weiterarbeiten mit klarem Umfang.</h2>
			</div>
			<dl class="uebernahme-liste">
				<div><dt>Ein abgegrenztes Projekt</dt><dd>Zum Beispiel eine Angebotsseite ergänzen, ein Formular verbessern, Ladezeitprobleme beheben oder Tracking anbinden. Aufgaben, Preis und Abnahme vereinbaren wir vor dem Start.</dd></div>
				<div><dt>Ein planbares Monatskontingent</dt><dd><p><?php echo esc_html( $retainer ); ?> im Monat, monatlich kündbar. Wir priorisieren gemeinsam die nächsten Aufgaben.</p><p class="uebernahme-hinweis">Ohne Rufbereitschaft und ohne garantierte Reaktionszeiten. Ein laufender Vertrag ist keine Voraussetzung für den Check oder ein Einzelprojekt.</p></dd></div>
				<div><dt>Ein Relaunch, wenn er sinnvoll ist</dt><dd><p>Falls sich der Bestand nicht sinnvoll weiterführen lässt, klären wir einen Neuaufbau als eigenes Projekt.</p><a class="textlink" href="<?php echo esc_url( $routes['website'] ); ?>" data-track-action="takeover_to_website" data-track-category="navigation" data-track-section="takeover_development">Website- und Relaunch-Angebot ansehen <span aria-hidden="true">→</span></a></dd></div>
			</dl>
		</div>
	</section>

	<section id="voraussetzungen" aria-labelledby="takeover-questions-title">
		<div class="blatt uebernahme-abschnitt">
			<div class="uebernahme-titel">
				<p class="gegenstand">Vor dem Start</p>
				<h2 class="kopf leise" id="takeover-questions-title">Was Sie mitbringen.</h2>
				<p>Für die Anfrage reichen die Website-Adresse und eine kurze Beschreibung des Anlasses. Passwörter gehören nicht ins Formular. Nötige WordPress- und Hosting-Zugänge stimmen wir vor dem Check gesondert ab.</p>
			</div>
			<div class="fragen" data-faq-group>
				<details name="takeover-faq"><summary>Übernehmen Sie auch Elementor und fremde Themes?</summary><div class="huelle"><div><p class="antwort">Auch bestehende Pagebuilder- und Theme-Installationen kommen infrage. Im Check prüfe ich den konkreten Aufbau und die Abhängigkeiten. Ob und in welchem Umfang ich die Website weiterbetreuen kann, klären wir anhand des Befunds.</p></div></div></details>
				<details name="takeover-faq"><summary>Muss ich Hosting oder Design wechseln?</summary><div class="huelle"><div><p class="antwort">Ein Wechsel ist keine Voraussetzung. Zuerst prüfen wir den Bestand. Falls ein Wechsel oder ein Neuaufbau nötig ist, erhalten Sie dafür einen eigenen Umfang und Preis. Hosting, externe Dienste und Lizenzen werden separat benannt.</p></div></div></details>
				<details name="takeover-faq"><summary>Was gilt bei einem akuten Ausfall?</summary><div class="huelle"><div><p class="antwort">Das Angebot ist für geplante Übernahme und Weiterentwicklung gedacht. Es umfasst keinen Notdienst und keine garantierte Wiederherstellungszeit. Klären Sie akute Ausfälle zuerst mit Ihrem Hosting-Anbieter oder Ihrem bestehenden Support. Eine Reaktionszeit bei Ausfall gehört zur <a class="satzlink" href="<?php echo esc_url( hu_get_commercial_route( 'maintenance' ) ); ?>" data-track-action="takeover_faq_to_maintenance" data-track-category="internal_link" data-track-section="takeover_questions">WordPress-Wartung</a>, die nach dem Check gebucht werden kann.</p></div></div></details>
			</div>
		</div>
	</section>

	<section id="anfrage" class="tafel" aria-labelledby="takeover-request-title">
		<div class="blatt uebernahme-abschluss">
			<div>
				<p class="gegenstand">Nächster Schritt</p>
				<h2 class="kopf" id="takeover-request-title">Welche Website soll wieder vorankommen?</h2>
				<p>Schicken Sie mir die Adresse und beschreiben Sie kurz, was gerade liegen bleibt. Ich antworte <?php echo esc_html( hu_response_promise( 'window' ) ); ?> auf Ihre Anfrage. Umfang und Termin des Checks klären wir danach.</p>
			</div>
			<div class="uebernahme-aktionen">
				<p><strong>Übernahme-Check: <?php echo esc_html( $price ); ?> netto.</strong> Bei Beauftragung des Folgeprojekts verrechnet.</p>
				<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="takeover_request_close" data-track-category="lead_gen" data-track-section="takeover_close">Übernahme-Check anfragen <span aria-hidden="true">→</span></a>
				<p class="uebernahme-hinweis">Unverbindliche Anfrage, keine kostenpflichtige Buchung.</p>
				<a class="textlink" href="<?php echo esc_url( $routes['whitelabel'] ); ?>" data-track-action="takeover_agency_collaboration" data-track-category="navigation" data-track-section="takeover_close">Sie suchen Unterstützung für Ihre Agentur? <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</section>
</div>
<?php get_footer(); ?>
