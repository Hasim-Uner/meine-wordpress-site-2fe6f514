<?php
/**
 * Template Name: Technisches SEO für WordPress
 * Description: Technischen Befund, Korrektur und Abnahme vor dem Auftrag abgrenzen.
 *
 * Hardcoded service page. Fixed price only for the written finding
 * (hu_seo_finding_price()); corrections are quoted after it. No ranking
 * promise. Metadata and
 * Service schema are owned by the central modules, not this template.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$routes      = hu_get_commercial_route_map();
$request_url = hu_get_contact_intake_url( 'project', 'seo' );
$article_url = home_url( '/technisches-seo-performance-fundament/' );
$code_url    = 'https://github.com/Hasim-Uner/meine-wordpress-site-2fe6f514/blob/dc4446fbdd1fb9cd91aad05383625ec51e39c57d/scripts/tests/navigation-contract.php';

get_header();
?>
<div class="doku seo-angebot" id="technical-seo-content" data-track-page="technical_seo">
	<header class="blatt kopfteil seo-angebot-raster">
		<div class="seo-angebot-text">
			<p class="gegenstand">Technischer SEO-Befund & Korrektur</p>
			<h1>Technisches SEO für Ihre WordPress-Website.</h1>
			<p class="aufriss">Wichtige Seiten fehlen in der Suche, Weiterleitungen führen falsch oder die Website lädt zu langsam? Ich prüfe mögliche technische Ursachen und setze die vereinbarten Korrekturen in WordPress um.</p>
			<a class="textlink" href="#arbeitsbeispiel" data-track-action="technical_seo_hero_proof" data-track-category="trust" data-track-section="technical_seo_intro">Ein prüfbares Beispiel aus dieser Website <span aria-hidden="true">↓</span></a>
		</div>
		<aside class="tafel seo-angebot-text" aria-labelledby="technical-seo-start-title">
			<p class="gegenstand" id="technical-seo-start-title">Vor dem Auftrag</p>
			<h2 class="kopf leise">Erst der Befund, dann der Festpreis.</h2>
			<p>Sie schicken die Website-Adresse und beschreiben das Problem. Wir grenzen die betroffenen Seiten, Prüffragen und nötigen Zugänge ein.</p>
			<p><strong>SEO-Befund für <?php echo esc_html( hu_seo_finding_price( true ) ); ?>.</strong> Schriftlich, mit betroffenen URLs, Nachweis und Priorität. Beauftragen Sie danach die Korrekturen, wird der Befund angerechnet. Die Korrekturen bekommen einen Festpreis nach Befund.</p>
			<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="technical_seo_request_hero" data-track-category="lead_gen" data-track-section="technical_seo_intro">SEO-Projekt anfragen <span aria-hidden="true">→</span></a>
			<p class="seo-angebot-hinweis">Mit dem Absenden fragen Sie eine Zusammenarbeit an. Eine Prüfung beginnt erst nach Beauftragung.</p>
		</aside>
	</header>

	<section id="pruefumfang" aria-labelledby="technical-seo-scope-title">
		<div class="blatt seo-angebot-abschnitt">
			<div class="seo-angebot-text">
				<p class="gegenstand">Prüfumfang nach Anlass</p>
				<h2 class="kopf" id="technical-seo-scope-title">Was Ihre Seiten technisch ausbremst.</h2>
				<p>Wir legen fest, welche URLs und Seitentypen geprüft werden. Ein einzelnes Problem braucht einen anderen Umfang als ein Relaunch oder ein großer redaktioneller Bestand.</p>
			</div>
			<dl class="seo-angebot-liste">
				<div><dt>Erreichbarkeit und Indexierung</dt><dd>Statuscodes, Weiterleitungen, robots.txt, Indexierungsregeln, Canonicals und Sitemap. Welche Seite soll erreichbar sein, und welches Ziel soll in der Suche erscheinen?</dd></div>
				<div><dt>Vorlagen und Seitenstruktur</dt><dd>Titel, Beschreibungen, Überschriften, interne Links und strukturierte Daten. Ich prüfe die vereinbarten Seiten und die WordPress-Vorlagen dahinter auf widersprüchliche oder fehlende Angaben.</dd></div>
				<div><dt>Ladezeit und mobile Darstellung</dt><dd>Ladeverhalten und technische Engpässe auf den vereinbarten Seiten. Labortests und verfügbare Nutzungsdaten werden getrennt ausgewiesen; fehlende Daten werden als solche benannt.</dd></div>
			</dl>
		</div>
	</section>

	<section id="abnahme" aria-labelledby="technical-seo-acceptance-title">
		<div class="blatt seo-angebot-abschnitt">
			<div class="seo-angebot-text">
				<p class="gegenstand">Lieferung und Abnahme</p>
				<h2 class="kopf" id="technical-seo-acceptance-title">Ein Befund, mit dem sich arbeiten lässt.</h2>
			</div>
			<ol class="seo-angebot-schritte">
				<li><h3>Befund mit Nachweis</h3><p>Betroffene URL oder Vorlage, beobachtetes Problem, Prüfmethode und Priorität. Dazu die empfohlene Korrektur und erkennbare Abhängigkeiten.</p></li>
				<li><h3>Vereinbarte Korrektur</h3><p>Wenn Sie die Umsetzung beauftragen, stehen Aufgaben, Preis, Zugänge und Abnahmekriterien vorher fest. Der Befund kann auch an Ihre bestehende Entwicklung gehen.</p></li>
				<li><h3>Prüfung nach der Änderung</h3><p>Für die vereinbarten Korrekturen dokumentiere ich den Zustand vor und nach der Änderung und die wiederholbare Prüfung. Offene Punkte und verbleibende Grenzen werden festgehalten.</p></li>
			</ol>
			<p class="seo-angebot-grenze"><strong>Abgenommen wird die vereinbarte technische Arbeit.</strong> Indexierung, Positionen und Anfragen hängen auch von anderen Faktoren ab und werden nicht garantiert. Gute Labortestwerte allein belegen keinen Rankinggewinn.</p>
		</div>
	</section>

	<section id="arbeitsbeispiel" aria-labelledby="technical-seo-proof-title">
		<div class="blatt seo-angebot-abschnitt">
			<div class="seo-angebot-text">
				<p class="gegenstand">Arbeitsbeispiel · eigene Website</p>
				<h2 class="kopf leise" id="technical-seo-proof-title">Eine Projektübersicht mit eindeutiger Seitenrolle.</h2>
				<p>Die Referenzseite dieser Website zeigt, wie sichtbarer Inhalt, Seitenadresse und strukturierte Daten zusammengeführt und geprüft werden.</p>
			</div>
			<dl class="seo-angebot-liste">
				<div><dt>Die Aufgabe</dt><dd>Eine eigenständige Projektübersicht anlegen und sie von der bestehenden Fallstudie trennen. Alte Ergebnisse-Links behalten ihr festgelegtes Ziel.</dd></div>
				<div><dt>Die Umsetzung</dt><dd>Die Referenzseite hat eine eigene URL, eigene Metadaten und einen CollectionPage-Knoten mit Verweisen auf die sichtbaren Projekte.</dd></div>
				<div><dt>Die Kontrolle</dt><dd>Automatische Prüfungen vergleichen die Projektanker im HTML mit den Verweisen der strukturierten Daten. Weitere Prüfungen kontrollieren die Navigationsziele und den Routenindex.</dd></div>
			</dl>
			<div class="seo-angebot-links">
				<a class="textlink" href="<?php echo esc_url( $routes['references'] ); ?>" data-track-action="technical_seo_proof_page" data-track-category="trust" data-track-section="technical_seo_proof">Referenzseite selbst ansehen <span aria-hidden="true">→</span></a>
				<a class="textlink" href="<?php echo esc_url( $code_url ); ?>" target="_blank" rel="noopener noreferrer" data-track-action="technical_seo_proof_code" data-track-category="trust" data-track-section="technical_seo_proof">Prüfung im öffentlichen Code <span aria-hidden="true">↗</span><span class="nur-vorlesen"> (öffnet in neuem Tab)</span></a>
			</div>
			<p class="seo-angebot-hinweis">Ein Beispiel aus meinem eigenen Projekt, keine Kundenreferenz und kein Nachweis für eine Rankingsteigerung. Es belegt die technische Umsetzung und ihre Prüfung.</p>
		</div>
	</section>

	<section id="abgrenzung" aria-labelledby="technical-seo-fit-title">
		<div class="blatt seo-angebot-abschnitt">
			<div class="seo-angebot-text">
				<p class="gegenstand">Passung und Grenzen</p>
				<h2 class="kopf leise" id="technical-seo-fit-title">Welche Aufgabe haben Sie?</h2>
				<p>Dieses Angebot betrifft die Technik einer bestehenden WordPress-Website. Texterstellung, laufende Content-Produktion, Linkaufbau, Anzeigen und ein vollständiges Tracking-Setup gehören nicht automatisch dazu. Externe Lizenzen oder Hostingkosten werden vorab separat benannt.</p>
			</div>
			<ul class="seo-angebot-wege">
				<li><strong>Eine neue Website oder ein kompletter Relaunch?</strong><p>Das Grundprodukt enthält bereits die technische SEO-Basis.</p><a class="textlink" href="<?php echo esc_url( $routes['website'] ); ?>" data-track-action="technical_seo_to_website" data-track-category="navigation" data-track-section="technical_seo_fit">Website-Angebot ansehen <span aria-hidden="true">→</span></a></li>
				<li><strong>Die bisherige Betreuung fehlt?</strong><p>Der Übernahme-Check klärt zunächst den allgemeinen technischen Bestand.</p><a class="textlink" href="<?php echo esc_url( $routes['takeover'] ); ?>" data-track-action="technical_seo_to_takeover" data-track-category="navigation" data-track-section="technical_seo_fit">Übernahme und Weiterentwicklung <span aria-hidden="true">→</span></a></li>
				<li><strong>Besucher sind da, aber Anfragen bleiben aus?</strong><p>Dann geht es um Angebot, Seitenführung und Anfrageweg.</p><a class="textlink" href="<?php echo esc_url( $routes['conversion'] ); ?>" data-track-action="technical_seo_to_conversion" data-track-category="navigation" data-track-section="technical_seo_fit">Conversion-Analyse ansehen <span aria-hidden="true">→</span></a></li>
			</ul>
			<a class="textlink" href="<?php echo esc_url( $article_url ); ?>" data-track-action="technical_seo_read_guide" data-track-category="navigation" data-track-section="technical_seo_fit">Technischen Hintergrund im Fachartikel lesen <span aria-hidden="true">→</span></a>
		</div>
	</section>

	<section id="anfrage" class="tafel" aria-labelledby="technical-seo-request-title">
		<div class="blatt seo-angebot-abschnitt">
			<div class="seo-angebot-text">
				<p class="gegenstand">Ausgangslage beschreiben</p>
				<h2 class="kopf" id="technical-seo-request-title">Welche Seiten machen Probleme?</h2>
				<p>Website-Adresse, beobachtetes Problem und bisherige Änderungen reichen für die Anfrage. Ich antworte <?php echo esc_html( hu_response_promise( 'window' ) ); ?>. Prüfumfang, Termin, Preis und nötige Zugänge stimmen wir vor dem Start ab.</p>
				<a class="tun" href="<?php echo esc_url( $request_url ); ?>" data-track-action="technical_seo_request_close" data-track-category="lead_gen" data-track-section="technical_seo_close">SEO-Projekt anfragen <span aria-hidden="true">→</span></a>
				<p class="seo-angebot-hinweis">Keine kostenpflichtige Buchung durch das Absenden. Bitte keine Passwörter oder Zugangsdaten ins Formular eintragen.</p>
				<a class="textlink" href="<?php echo esc_url( $routes['whitelabel'] ); ?>" data-track-action="technical_seo_agency_collaboration" data-track-category="navigation" data-track-section="technical_seo_close">Für Agenturen: Zusammenarbeit ansehen <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</section>
</div>
<?php get_footer(); ?>
