<?php
/**
 * Template Name: WordPress-Website erstellen lassen
 * Description: Die Anfrage-Website: Festpreis, Umfangsrechner und Bauzeit.
 * Produktkonfigurator: Grundprodukt plus freigegebene Erweiterungen (04.10.2026).
 * Preise, FAQ und SEO bleiben in ihren zentralen Modulen.
 * @package Blocksy_Child
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
add_filter( 'body_class', static function ( $classes ) {
    $classes[] = 'hu-website-offer-page';
    return $classes;
} );
add_action( 'wp_enqueue_scripts', static function () {
    hu_enqueue_css( 'hu-anfrage-website', 'anfrage-website.css', [ 'nexus-system-css' ] );
    hu_enqueue_js( 'hu-anfrage-website', 'anfrage-website.js', [] );
}, 90 );
$quote = hu_website_quote( 3, 'neubau', false, [ 'texte' => 1 ] );
$rules = hu_website_calculator_rules();
$days = static function ( $value ) { return number_format( (float) $value, floor( (float) $value ) === (float) $value ? 0 : 1, ',', '.' ) . ( 1.0 === (float) $value ? ' Werktag' : ' Werktage' ); };
$contact_url = add_query_arg( [ 'seiten' => 3, 'art' => 'neubau', 'texte' => 1 ], hu_get_contact_intake_url( 'project', 'website' ) );
$psi_url = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/wordpress-website-erstellen-lassen/' ) );
$eur = static function ( $value ) { return str_replace( ' €', "\u{00A0}€", hu_format_eur( $value ) ); };
$scenarios = [
    [ 'pages' => 1, 'title' => 'Ihr Angebot auf einen Blick', 'example' => 'Zum Beispiel: Friseursalon', 'purpose' => 'Leistungen, Preise, Team, Öffnungszeiten und Kontakt auf einer übersichtlichen Seite.', 'structure' => [ 'Startseite mit allen Abschnitten' ] ],
    [ 'pages' => 3, 'title' => 'Mehr Raum für Ihre Leistung', 'example' => 'Zum Beispiel: Unternehmensberatung', 'purpose' => 'Angebot und Arbeitsweise erklären. Expertise zeigen, bevor jemand eine Anfrage stellt.', 'structure' => [ 'Startseite mit Kontakt', 'Leistungen', 'Über uns' ] ],
    [ 'pages' => 5, 'title' => 'Jede Leistung mit eigener Seite', 'example' => 'Zum Beispiel: Sanitär- und Heizungsbetrieb', 'purpose' => 'Unterschiedliche Leistungen getrennt erklären und auf passende Suchanliegen ausrichten.', 'structure' => [ 'Startseite', 'Heizung', 'Sanitär', 'Wartung', 'Kontakt' ] ],
];
get_header();
?>
<div id="website-content" class="doku anfrage-website" data-track-page="website_offer" data-website-product data-website-rules="<?php echo esc_attr( wp_json_encode( $rules ) ); ?>">
  <nav class="wrap krumen" aria-label="Brotkrumen"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Startseite</a> / WordPress-Website erstellen lassen</nav>

  <section class="hero" id="hero">
    <div class="wrap">
      <div>
        <div class="produkt"><span class="marke-produkt">Die Anfrage-Website</span><span class="mono">WordPress · Neubau oder Relaunch</span></div>
        <h1>WordPress-Website erstellen lassen.<br><span class="hero-akzent">Ihr Angebot zeigen. Anfragen einfach machen.</span></h1>
        <p class="lead">Für Unternehmen und Selbstständige: eine WordPress-Website mit Custom Code zum Einmalpreis. Texte auf Wunsch, technisches SEO und die Anfragestrecke sind inklusive. Sie wählen Seiten und Extras – Preis und Produktionszeit sehen Sie sofort.</p>
        <div class="aktion">
          <a class="btn" href="#angebot" data-track-action="website_offer_hero_configure" data-track-category="navigation" data-track-section="website_offer_hero">Website zusammenstellen <span aria-hidden="true">→</span></a>
          <a class="leise" href="#beleg" data-track-action="website_offer_to_beleg" data-track-category="navigation">Arbeit ansehen</a>
        </div>
        <p class="mikro">Antwort <?php echo esc_html( hu_response_promise( 'window' ) ); ?>, mit Rückfragen oder einem Festpreis-Angebot.</p>
      </div>
      <aside class="formel tafel" aria-label="Grundprodukt und Einstiegspreis">
        <div class="aw-produkt-kopf"><span class="mono">Die Anfrage-Website</span><span class="aw-status">Erweiterbar</span></div>
        <p class="mono">Festpreis ab</p>
        <p class="betrag"><?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_MIN ) ); ?><small>netto</small></p>
        <p class="regel">Website mit einer Seite. Jede weitere Seite <?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_EXTRA_PAGE ) ); ?>. Mit fertigen Inhalten ab <?php echo esc_html( $days( hu_website_quote( 1 )['components']['implementation'] ) ); ?> Umsetzung geplant. <span id="hero-bauzeit">Ihre Auswahl: <?php echo esc_html( $days( $quote['days'] ) ); ?> geplant.</span></p>
        <ul class="beispiele">
<?php foreach ( $scenarios as $scenario ) : $example_quote = hu_website_quote( $scenario['pages'], 'neubau', false, [ 'texte' => 1 ] ); ?>
          <li><span><?php echo esc_html( $scenario['pages'] . ( 1 === $scenario['pages'] ? ' Seite' : ' Seiten' ) ); ?></span><em><?php echo esc_html( $days( $example_quote['days'] ) ); ?></em><b><?php echo esc_html( $eur( $example_quote['price'] ) ); ?></b></li>
<?php endforeach; ?>
        </ul>
        <p class="aw-hinweis">Beispiele mit Texterstellung, ohne Erweiterungen. Kundenfreigaben und Starttermin separat.</p>
        <ul class="aw-basis-kurz"><li>Texte auf Wunsch · technisches SEO</li><li>Formular · Bestätigungsmail · Danke-Seite</li><li>Impressum · Datenschutz · Übergabe</li></ul>
        <a class="zum leise" href="#angebot" data-track-action="website_offer_to_angebot" data-track-category="navigation">Seiten &amp; Extras auswählen ↓</a>
      </aside>
    </div>
  </section>

  <div class="wrap aw-qualitaet" aria-label="So wird Ihre Website gebaut">
    <div><span class="mono">01 · Entwicklung</span><strong>Custom Code. Kein Pagebuilder.</strong><p>Schlank entwickelt. Keine Pflichtlizenzen für einen Baukasten.</p></div>
    <div><span class="mono">02 · Inhalte</span><strong>Ihre Inhalte. Ihr Zugriff.</strong><p>Texte und Bilder selbst ändern – mit Gutenberg und ACF.</p></div>
    <div><span class="mono">03 · Qualität</span><strong>Vor dem Livegang geprüft.</strong><p>KI-Workflow und GitHub. Formular, Darstellung und Ladezeit werden geprüft.</p></div>
  </div>

  <section class="abschnitt aw-szenarien" id="beispiele" aria-labelledby="h-beispiele">
    <div class="wrap">
      <div class="aw-szenarien-kopf"><div><p class="mono">Der passende Einstieg</p><h2 id="h-beispiele">Wie viel Website brauchen Sie?</h2></div><p>Drei Beispiele zur Orientierung.<br>Sie können jeden Umfang anpassen.</p></div>
      <div class="aw-szenarien-raster">
<?php foreach ( $scenarios as $scenario ) : $example_quote = hu_website_quote( $scenario['pages'], 'neubau', false, [ 'texte' => 1 ] ); ?>
        <article class="aw-szenario" aria-labelledby="h-szenario-<?php echo esc_attr( (string) $scenario['pages'] ); ?>">
          <p class="aw-szenario-anzahl"><b><?php echo esc_html( (string) $scenario['pages'] ); ?></b><span><?php echo 1 === $scenario['pages'] ? 'Seite' : 'Seiten'; ?></span></p>
          <h3 id="h-szenario-<?php echo esc_attr( (string) $scenario['pages'] ); ?>"><?php echo esc_html( $scenario['title'] ); ?></h3>
          <p class="aw-szenario-beispiel"><?php echo esc_html( $scenario['example'] ); ?></p>
          <p class="aw-szenario-zweck"><?php echo esc_html( $scenario['purpose'] ); ?></p>
          <ol class="aw-seitenplan" aria-label="Beispielhafte Seitenstruktur"><?php foreach ( $scenario['structure'] as $page_name ) : ?><li><?php echo esc_html( $page_name ); ?></li><?php endforeach; ?></ol>
          <div class="aw-szenario-fuss"><p><strong><?php echo esc_html( $eur( $example_quote['price'] ) ); ?></strong><span>netto · ohne Extras</span></p><button type="button" data-website-controls hidden data-website-scenario="<?php echo esc_attr( (string) $scenario['pages'] ); ?>" aria-controls="angebot" aria-pressed="<?php echo 3 === $scenario['pages'] ? 'true' : 'false'; ?>" data-track-action="website_offer_scenario_<?php echo esc_attr( (string) $scenario['pages'] ); ?>" data-track-category="navigation" data-track-section="website_offer_examples"><?php echo esc_html( $scenario['pages'] . ( 1 === $scenario['pages'] ? ' Seite wählen' : ' Seiten wählen' ) ); ?> <span aria-hidden="true">→</span></button></div>
        </article>
<?php endforeach; ?>
      </div>
      <p class="aw-szenarien-hinweis">Texte auf Wunsch und Basisgestaltung sind enthalten. Impressum, Datenschutz, Danke- und 404-Seite kommen ohne Aufpreis dazu. Online-Terminbuchung und Shop werden separat kalkuliert. Bei der Auswahl bleiben Ihre Extras erhalten.</p>
    </div>
  </section>

  <section class="abschnitt aw-konfigurator" id="angebot" aria-labelledby="h-angebot">
    <div class="wrap">
      <div class="aw-config-heading"><div><p class="mono">01 · Ihr Produkt, Ihre Auswahl</p><h2 id="h-angebot" tabindex="-1">Ihre Website. Klar kalkuliert.</h2></div><p>Seiten wählen. Extras ergänzen.<br><strong>Preis und Zeit rechnen direkt mit.</strong></p></div>
      <noscript><p class="mikro">Beispiel: drei Seiten, Neubau, Texte inklusive, ohne Extras. Nennen Sie Ihren gewünschten Umfang in der Anfrage.</p></noscript>
      <div class="aw-config-grid">
        <div class="aw-config-controls">
          <div class="aw-config-column">
            <fieldset class="aw-feld aw-umfang">
              <legend><span class="aw-schritt">01</span> Seiten &amp; Projekt</legend>
              <div class="groessen" data-website-controls hidden role="group" aria-label="Seitenanzahl wählen">
                <button type="button" data-seiten="1" aria-pressed="false"><span>1 Seite</span><b><?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_MIN ) ); ?></b></button>
                <button type="button" data-seiten="3" aria-pressed="true"><span>3 Seiten</span><b><?php echo esc_html( $eur( $quote['price'] ) ); ?></b></button>
                <button type="button" data-seiten="5" aria-pressed="false"><span>5 Seiten</span><b><?php echo esc_html( $eur( hu_website_quote( 5 )['price'] ) ); ?></b></button>
              </div>
              <div class="aw-seitenzeile"><p>Individuell wählen<small>Weitere Seite +<?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_EXTRA_PAGE ) ); ?></small></p><div class="stepper" data-website-controls hidden><button type="button" id="minus" aria-label="Eine Seite weniger">−</button><output id="seiten" aria-live="polite" aria-label="Gewählte Seitenanzahl">3</output><button type="button" id="plus" aria-label="Eine Seite mehr">+</button></div></div>
              <p class="aw-hinweis">Impressum, Datenschutz, Danke- und 404-Seite zählen nicht mit.</p>
              <div class="art" data-website-controls hidden role="group" aria-label="Projektart"><button type="button" data-art="neubau" aria-pressed="true">Neubau</button><button type="button" data-art="relaunch" aria-pressed="false">Relaunch <small>+<?php echo esc_html( $days( $rules['days']['relaunch'] ) ); ?></small></button></div>
            </fieldset>
            <fieldset class="aw-feld aw-inhalte">
              <legend><span class="aw-schritt">02</span> Texte</legend>
              <label class="aw-option" data-website-controls hidden><input type="checkbox" id="texte" checked><span class="aw-option-inhalt"><strong>Für mich erstellen</strong><small>Texte für jede gewählte Seite, auf Basis Ihrer Angaben.</small></span><span class="aw-option-preis">Inklusive</span></label>
              <p class="aw-hinweis" id="text-hinweis">Texte bereits fertig? Abwählen. Der Seitenpreis bleibt gleich.</p>
            </fieldset>
            <div class="aw-standard-kurz"><span class="mono">Immer inklusive</span><ul><li>Technisches SEO &amp; mobile Darstellung</li><li>Formular, Bestätigungsmail &amp; Danke-Seite</li><li>Impressum- &amp; Datenschutz-Seite</li></ul><a href="#lieferumfang" data-track-action="website_offer_included_details" data-track-category="navigation">Gesamten Lieferumfang ansehen ↓</a></div>
          </div>
          <div class="aw-config-column">
            <fieldset class="aw-feld aw-design">
              <legend><span class="aw-schritt">03</span> Design</legend>
              <div class="aw-extras" data-website-controls hidden role="radiogroup" aria-label="Design-Umfang">
                <label class="aw-option"><input type="radio" name="website-design" id="design-basis" value="basis" checked><span class="aw-option-inhalt"><strong>Basisgestaltung</strong><small>Bewährte Layouts, Ihre Farben und Typografie.</small></span><span class="aw-option-preis">Inklusive</span></label>
                <label class="aw-option"><input type="radio" name="website-design" id="design-vorhanden" value="vorhanden"><span class="aw-option-inhalt"><strong>Design ist vorhanden</strong><small>Fertige Vorlage umsetzen. Umfang vorab prüfen.</small></span><span class="aw-option-preis">+0 €*</span></label>
                <label class="aw-option"><input type="radio" name="website-design" id="screendesign" value="neu"><span class="aw-option-inhalt"><strong>Individuelles Screendesign</strong><small>Figma-Entwurf für Desktop &amp; Mobil.</small></span><span class="aw-option-preis">+<?php echo esc_html( $eur( HU_WEBSITE_DESIGN_FIRST ) ); ?><small>erstes Layout</small></span></label>
              </div>
              <div class="aw-design-details" id="design-details" hidden>
                <label for="design-layouts">Unterschiedliche Seitenlayouts</label>
                <select id="design-layouts" aria-describedby="design-layout-hinweis"><?php for ( $n = 1; $n <= 3; $n++ ) : ?><option value="<?php echo esc_attr( (string) $n ); ?>" <?php selected( $n, 3 ); ?>><?php echo esc_html( $n . ( 1 === $n ? ' Layout' : ' Layouts' ) ); ?></option><?php endfor; ?></select>
                <p class="aw-hinweis" id="design-layout-hinweis">Weitere Layouts +<?php echo esc_html( $eur( HU_WEBSITE_DESIGN_EXTRA ) ); ?>. Wiederverwendete Layouts zählen einmal.</p>
              </div>
              <p class="aw-hinweis" id="design-hinweis">Basisdesign inklusive.</p>
            </fieldset>
            <fieldset class="aw-feld aw-erweiterungen">
              <legend><span class="aw-schritt">04</span> Erweiterungen</legend>
              <div class="aw-extras" data-website-controls hidden>
                <label class="aw-option"><input type="checkbox" id="tracking"><span class="aw-option-inhalt"><strong>Conversion-Tracking</strong><small>Anfragen in GA4 &amp; Google Ads messen. +<?php echo esc_html( $days( $rules['days']['tracking'] ) ); ?></small></span><span class="aw-option-preis">+<?php echo esc_html( $eur( $rules['prices']['tracking'] ) ); ?></span></label>
                <label class="aw-option"><input type="checkbox" id="crm"><span class="aw-option-inhalt"><strong>CRM-Anbindung</strong><small>HubSpot oder Bitrix24: ein Formular. +<?php echo esc_html( $days( $rules['days']['crm'] ) ); ?></small></span><span class="aw-option-preis">+<?php echo esc_html( $eur( HU_WEBSITE_CRM_STANDARD ) ); ?></span></label>
                <label class="aw-option"><input type="checkbox" id="dashboard"><span class="aw-option-inhalt"><strong>Daten-Dashboard</strong><small>Ihre Daten an einem Ort. Umfang nach Abstimmung.</small></span><span class="aw-option-preis">Nach Angebot</span></label>
              </div>
            </fieldset>
          </div>
        </div>
        <aside class="aw-zusammenfassung tafel" aria-labelledby="h-auswahl">
          <div class="aw-summary-kopf"><span class="mono">Ihre Konfiguration</span><span class="aw-status">Einmalpreis</span></div>
          <h3 id="h-auswahl">Die Anfrage-Website</h3>
          <p class="aw-summary-sub" id="auswahl-art">3 Seiten · Neubau</p>
          <p class="aw-summary-sub" id="auswahl-design">Basisdesign inklusive</p>
          <dl class="aw-preispositionen">
            <div><dt>Website mit erster Seite</dt><dd><?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_MIN ) ); ?></dd></div>
            <div><dt><span id="weitere">2</span> weitere Seiten</dt><dd id="betrag-weitere"><?php echo esc_html( $eur( 2 * HU_FREELANCER_WEBSITE_EXTRA_PAGE ) ); ?></dd></div>
            <div><dt id="auswahl-texte">Texte erstellen lassen</dt><dd>Inklusive</dd></div>
            <div id="summary-tracking" hidden><dt>Conversion-Tracking</dt><dd><?php echo esc_html( $eur( $rules['prices']['tracking'] ) ); ?></dd></div>
            <div id="summary-screendesign" hidden><dt>Screendesign</dt><dd id="betrag-design"></dd></div>
            <div id="summary-crm" hidden><dt>CRM-Anbindung</dt><dd><?php echo esc_html( $eur( HU_WEBSITE_CRM_STANDARD ) ); ?></dd></div>
            <div id="summary-dashboard" hidden><dt>Daten-Dashboard</dt><dd>Nach Angebot</dd></div>
          </dl>
          <div class="summe">
            <p class="mono" id="preis-label">Ihr Einmalpreis</p>
            <p class="gesamt"><span id="gesamt"><?php echo esc_html( $eur( $quote['price'] ) ); ?></span><small>netto · zzgl. USt.</small></p>
            <p class="rechnung" id="rechnung"><?php echo esc_html( hu_freelancer_website_price() . ' + 2 × ' . hu_freelancer_website_extra_page_price() ); ?></p>
            <p id="angebot-hinweis" class="aw-angebot-hinweis" hidden></p>
            <div class="aw-zeitkalkulation"><span id="dauer-label">Produktionszeit</span><b id="bauzeit"><?php echo esc_html( $days( $quote['days'] ) ); ?></b><a href="#zeit" data-track-action="website_offer_to_zeit" data-track-category="navigation">Zeitbeiträge ansehen ↓</a></div>
            <a class="btn" id="cta-angebot" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="angebot" data-track-action="cta_website_offer_scope_project" data-track-category="lead_gen" data-track-section="website_offer_angebot">Auswahl anfragen <span aria-hidden="true">→</span></a>
            <p class="aw-hinweis">Unverbindlich. Festpreis &amp; Starttermin im Angebot.</p>
          </div>
          <p class="aw-eigentum"><strong>Kein Website-Abo. Keine Pflichtwartung.</strong></p>
          <p class="aw-live-status" id="konfiguration-status" role="status" aria-live="polite" aria-atomic="true"></p>
        </aside>
      </div>
      <p class="aw-config-fuss">Alle Preise netto. Domain, Hosting und externe Dienstkosten separat. *Vorhandene Designs und CRM werden vor Auftragserteilung geprüft. <a href="#erweiterungen">Umfang der Erweiterungen ↓</a></p>
      <div class="inklusive" id="lieferumfang">
        <div class="inklusive-kopf"><div><p class="mono">Die Standardausstattung</p><h3>Das steckt schon drin.</h3></div><span class="aw-status">Ohne Aufpreis</span></div>
        <p class="mikro">Die wichtigsten Leistungen sind immer enthalten. Die Details öffnen Sie bei Bedarf.</p>
        <div class="gruppen"><details class="gruppe"><summary><span>Inhalte</span><span class="aw-inkl-label">Inklusive</span></summary><ul>
                <li>Gliederung jeder Seite: Angebot, Preis, Beleg</li>
                <li>Texte für jede gewählte Seite auf Wunsch erstellt, auf Basis Ihrer Angaben</li>
                <li>Ihre vorhandenen Texte und Bilder eingepflegt</li>
                <li>Feinschliff der Texte</li>
                <li>Bilder zugeschnitten und als <?php echo nexus_glossary_link( 'webp', 'WebP' ); ?> verkleinert</li>
              </ul></details>
<details class="gruppe"><summary><span>Technisches SEO und On-Page</span><span class="aw-inkl-label">Inklusive</span></summary><ul>
                <li>Pro Seite ein Suchbegriff, auf den Title, Überschrift und Gliederung ausgerichtet sind</li>
                <li>Title und Meta-Beschreibung je Seite</li>
                <li>Saubere URLs und eine logische Überschriften-Struktur</li>
                <li>Interne Links zwischen den Seiten</li>
                <li>Alternativtexte für alle Bilder</li>
                <li><?php echo nexus_glossary_link( 'canonical-url', 'Canonical' ); ?>, <?php echo nexus_glossary_link( 'xml-sitemap', 'XML-Sitemap' ); ?> und robots.txt</li>
                <li><?php echo nexus_glossary_link( 'strukturierte-daten', 'Strukturierte Daten' ); ?>: Unternehmen, Brotkrumen, FAQ</li>
                <li>Core Web Vitals: feste Bildmaße, kein Springen beim Laden</li>
                <li>Google Search Console eingerichtet, Sie als Inhaber</li>
                <li>Vorschau für WhatsApp, LinkedIn und Co.</li>
              </ul></details>
<details class="gruppe"><summary><span>Technik</span><span class="aw-inkl-label">Inklusive</span></summary><ul>
                <li>WordPress mit Custom Code, ohne Pagebuilder</li>
                <li>Inhalte im Gutenberg-Editor und mit ACF-Feldern pflegen</li>
                <li>WordPress auf Ihrem Hosting, auch dem bestehenden</li>
                <li>Keine Pflichtlizenzen für Themes oder Plugins im Grundprodukt</li>
                <li>Domain verbunden, HTTPS</li>
                <li>Für Handy und Desktop gebaut</li>
                <li>Grundlagen der Barrierefreiheit: Kontraste, Tastatur, Alternativtexte</li>
                <li>Favicon</li>
                <li>Backup vor dem Livegang</li>
              </ul></details>
<details class="gruppe"><summary><span>Anfrage</span><span class="aw-inkl-label">Inklusive</span></summary><ul>
                <li>Kontaktformular, Versand über SPF und DKIM abgesichert</li>
                <li>E-Mail-Bestätigung an die anfragende Person</li>
                <li>Spam-Schutz ohne Captcha</li>
                <li>Danke-Seite mit nächstem Schritt</li>
                <li>Formularanfragen in WordPress gespeichert; kein Analyse-Dashboard im Grundprodukt</li>
                <li>404-Seite, die zurück zum Angebot führt</li>
              </ul></details>
<details class="gruppe"><summary><span>Recht</span><span class="aw-inkl-label">Inklusive</span></summary><ul>
                <li>Impressum und Datenschutz angelegt, Ihre Rechtstexte eingebunden</li>
                <li>Ohne Tracking cookiefrei gebaut, Schriften lokal</li>
              </ul></details>
<details class="gruppe"><summary><span>Abnahme und Übergabe</span><span class="aw-inkl-label">Inklusive</span></summary><ul>
                <li>GitHub-Versionierung und KI-gestützter Workflow mit Qualitätsprüfung</li>
                <li>Testumgebung, live erst nach Ihrer Freigabe</li>
                <li>Zwei Korrekturrunden</li>
                <li>Abnahmeprotokoll: Testanfrage, Ladezeit, Weiterleitungen geprüft und dokumentiert</li>
                <li id="pos-weiterleitung" class="aus">Weiterleitungsplan für jede alte URL</li>
                <li>Domain, Hosting, Konten und Code auf Ihren Namen</li>
                <li>Dokumentation und Einweisung in den Editor</li>
                <li>30 Tage nach dem Livegang: Fehler behebe ich kostenlos</li>
              </ul></details></div>
      </div>
      <div class="klartext">
        <div><h3>Was als Seite zählt</h3><p>Eine Inhaltsseite mit eigener URL, etwa Startseite, Leistung oder Über uns. Mehrere Abschnitte auf derselben Seite zählen zusammen als eine Seite. Impressum, Datenschutz, Danke- und 404-Seite sind inklusive und zählen nicht mit.</p></div>
        <div><h3>Was separat kalkuliert wird</h3><p>Daten-Dashboard, Keyword-Recherche, Fotos und Logo, Shop, weitere Schnittstellen und mehrere Sprachen. Screendesign, Conversion-Tracking und Standard-CRM sind oben zum festen Aufpreis wählbar. Mehr als zehn Seiten erhalten ein eigenes Angebot.</p></div>
        <div><h3>Was laufend kostet</h3><p>Domain und Hosting zahlen Sie direkt beim Anbieter. Bei externen CRM- oder Zusatzdiensten können laufende Kosten entstehen; sie stehen vorab im Angebot. Weiterentwicklung buchen Sie nach Bedarf.</p></div>
      </div>
    </div>
  </section>

  <section class="abschnitt aw-erweiterungsumfang" id="erweiterungen" aria-labelledby="h-erweiterungen">
    <div class="wrap"><p class="mono">Die Erweiterungen im Detail</p><h2 id="h-erweiterungen">Mehr Möglichkeiten. Klarer Umfang.</h2>
      <details class="aw-erweiterungsdetail"><summary><span>Individuelles Screendesign</span><span><?php echo esc_html( $eur( HU_WEBSITE_DESIGN_FIRST ) ); ?> + <?php echo esc_html( $eur( HU_WEBSITE_DESIGN_EXTRA ) ); ?> je weiterem Layout</span></summary><div><p>Nach einem kurzen Briefing entwickle ich eine Gestaltungsrichtung in Figma. Jedes gebuchte Layout enthält Desktop und Mobil. Zwei gebündelte Korrekturrunden sind enthalten; nach Ihrer Freigabe setze ich das Design um.</p><p>Ein Layout ist ein unterschiedlicher Seitenaufbau. Startseite und drei gleich aufgebaute Leistungsseiten sind vier Seiten, aber zwei Layouts. Ein neues Logo, weitere Gestaltungsrichtungen und komplexe Interaktionen brauchen ein separates Angebot.</p></div></details>
      <details class="aw-erweiterungsdetail"><summary><span>Conversion-Tracking</span><span><?php echo esc_html( $eur( $rules['prices']['tracking'] ) ); ?></span></summary><div><p>GA4, Google Tag Manager, Consent Mode und eine Google-Ads-Conversion für erfolgreich abgesendete Standard-Anfragen. Einrichtung, Tests und Dokumentation sind enthalten. Ein funktionierendes Formular mit Bestätigungsmail und Danke-Seite gehört bereits zur Website; Marketing-Messung ist diese Erweiterung.</p><p>Server-Side Tracking, Meta CAPI, Offline-Conversions und weitere Ziele kalkuliere ich separat. Ein gegebenenfalls benötigter Consent-Dienst und seine laufenden Kosten werden vor Beauftragung benannt.</p></div></details>
      <details class="aw-erweiterungsdetail"><summary><span>CRM-Anbindung Standard</span><span><?php echo esc_html( $eur( HU_WEBSITE_CRM_STANDARD ) ); ?></span></summary><div><p>Ein Website-Formular an Ihr bestehendes HubSpot oder Bitrix24 anbinden: bis zu zehn Felder und ein Kontakt- oder Lead-Objekt. Feldzuordnung, Umgang mit Dubletten, Fehlerbehandlung, Testanfragen und Dokumentation sind enthalten.</p><p>Voraussetzung sind nutzbare API-Zugänge, passende Berechtigungen und ein bereits eingerichtetes Zielsystem. Ich prüfe die Anbindung vor Beauftragung. Salesforce, andere Systeme, mehrere Formulare oder Objekte, Datenmigration und Vertriebsautomationen erhalten ein eigenes Angebot. CRM-Lizenzen sind separat.</p></div></details>
      <details class="aw-erweiterungsdetail"><summary><span>Daten-Dashboard</span><span>Nach Angebot</span></summary><div><p>Ein optionales Dashboard in WordPress bündelt die vereinbarten Website-, Klick-, Formular- und Marketingdaten. Welche Kennzahlen sinnvoll sind, welche Daten bereits erfasst werden und welche Schnittstellen fehlen, klären wir vor der Kalkulation.</p><p>Messung und Anbindungen gehören zum vereinbarten Umfang. Vollständiger Festpreis und zusätzliche Zeit stehen vor Beauftragung im Angebot. Das Dashboard gehört nicht zum oben berechneten Standardpreis.</p></div></details>
    </div>
  </section>

  <section class="dunkel tafel" id="beleg" aria-labelledby="h-beleg">
    <div class="wrap raster">
      <p class="nr"><b>02</b><span>Beleg</span></p>
      <div class="haupt">
        <h2 id="h-beleg">Prüfen Sie die Arbeit, bevor Sie anfragen.</h2>
        <p class="lead">Sehen Sie sich Gestaltung und Umsetzung an. Die redaktionellen Projekte zeigen die Arbeit am Inhalt; der PV-Fall zeigt eine größere Anfragestrecke mit eigener Messung.</p>
        <div class="belege">
          <figure>
            <div class="bild"><img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/hasimuener-org-anfrage-website.webp' ); ?>" width="960" height="600" decoding="async" loading="lazy" alt="Startseite von hasimuener.org: großer Serifen-Schriftzug „Macht. Medien. Perspektive.“ über einer Navigation und einem Titelbild"></div>
            <figcaption><a href="https://hasimuener.org/" target="_blank" rel="noopener">hasimuener.org ↗</a> · eigenes redaktionelles Projekt. Typografie, Raster und Lesefluss tragen die Gestaltung.</figcaption>
          </figure>
          <ul class="messliste">
            <li><span class="mono">Diese Seite</span><p>Ohne Cookie-Banner, gebaut wie die Websites, die ich übergebe. <a href="<?php echo esc_url( $psi_url ); ?>" target="_blank" rel="noopener">Ladezeit selbst messen ↗</a></p></li>
            <li><span class="mono">Fall · PV-Installationsbetrieb</span><span class="wert"><?php echo esc_html( hu_e3_metric( 'cpl_before' ) . ' → ' . hu_e3_metric( 'cpl_after' ) ); ?></span><p>Kosten pro qualifizierter Anfrage in sechs Monaten. Gebaut: Website, Landingpages, Formular und Übergabe an den Vertrieb. <a href="<?php echo esc_url( home_url( '/case-study-solar-leadgenerierung/' ) ); ?>">Herleitung lesen</a></p></li>
            <li><span class="mono">Redaktioneller Bestand</span><p><a href="https://civaka-azad.org/" target="_blank" rel="noopener">civaka-azad.org ↗</a> · viele Inhalte, Navigation und Archive so gebaut, dass Themen auffindbar bleiben.</p></li>
          </ul>
        </div>
        <div class="folgerung">
          <a class="btn btn--glut" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="beleg" data-track-action="cta_website_offer_proof_project" data-track-category="lead_gen" data-track-section="website_offer_beleg">Website-Projekt anfragen <span aria-hidden="true">→</span></a>
          <p>Antwort <?php echo esc_html( hu_response_promise( 'window' ) ); ?>.</p>
        </div>
      </div>
      <aside class="rand"><span class="mono">Was der Fall belegt</span>Das Ergebnis gehört zur gesamten Maßnahme, mit Website, Kampagnen und Optimierung. Es ist keine Ergebniszusage für das Grundprodukt.</aside>
    </div>
  </section>

  <section class="dunkel tafel" id="unterschied" aria-labelledby="h-unterschied">
    <div class="wrap raster">
      <p class="nr"><b>03</b><span>Unterschied</span></p>
      <div class="haupt breit">
        <h2 id="h-unterschied">Sieben Punkte, an denen Sie die Qualität prüfen.</h2>
        <p class="lead">Prüfen Sie diese sieben Punkte bei jedem Angebot. Die Darstellung zeigt zwei beispielhafte Ausführungen, keine pauschale Bewertung anderer Anbieter.</p>
        <div class="schalter" data-website-controls hidden role="group" aria-label="Ansicht wählen">
          <button type="button" id="m-klassisch" aria-pressed="false" data-modus="klassisch">Mögliche Schwachstellen</button>
          <button type="button" id="m-anfragen" aria-pressed="true" data-modus="anfragen">Gebaut für Anfragen</button>
        </div>
        <div class="durch" id="durch" data-modus="anfragen">
          <div>
            <div class="geraet" aria-hidden="true">
              <div class="aw-browser-bar"><span class="url">ihre-firma.de</span><span class="laden"><i id="ladebalken"></i></span></div>
              <div class="ansichten">
                <div class="ansicht v-klassisch">
                  <div class="wf-nav"><span class="wf-logo"></span><span class="wf-link"></span><span class="wf-link"></span><span class="wf-link"></span><span class="wf-link"></span><span class="wf-link"></span><span class="wf-link"></span></div>
                  <div class="wf-bild"></div>
                  <div class="wf-z dick" style="width:62%"></div>
                  <div class="wf-z" style="width:88%"></div><div class="wf-z" style="width:74%"></div>
                  <div class="wf-karten"><span></span><span></span><span></span></div>
                  <div class="wf-z" style="width:40%"></div>
                  <div class="wf-form" style="width:56%"><div class="wf-feld"></div><div class="wf-feld"></div><div class="wf-feld"></div><div class="wf-feld"></div><div class="wf-feld" style="height:20px"></div></div>
                  <div class="wf-fuss"></div>
                  <span class="pin" data-pin="1" style="top:22%;left:50%">1</span>
                  <span class="pin" data-pin="3" style="top:79%;left:30%">3</span>
                  <span class="pin" data-pin="4" style="top:55%;left:72%">4</span>
                  <span class="pin" data-pin="6" style="top:95%;left:84%">6</span>
                  <span class="pin" data-pin="7" style="top:95%;left:16%">7</span>
                </div>
                <div class="ansicht v-anfragen">
                  <div class="wf-nav"><span class="wf-logo"></span><span class="wf-link"></span><span class="wf-link"></span><span class="wf-knopf"></span></div>
                  <div class="wf-z dick" style="width:78%;margin-top:10px"></div>
                  <div class="wf-z dick" style="width:52%"></div>
                  <div class="wf-z" style="width:70%"></div>
                  <div class="wf-reihe"><span class="wf-knopf" style="width:96px;height:22px"></span><span class="wf-preis"></span></div>
                  <div class="wf-belege"><span></span><span></span><span></span></div>
                  <div class="wf-form"><div class="wf-feld"></div><div class="wf-feld"></div><div class="wf-feld" style="height:20px"></div><span class="wf-knopf" style="width:90px"></span></div>
                  <div class="wf-fuss"></div>
                  <span class="pin" data-pin="1" style="top:17%;left:88%">1</span>
                  <span class="pin" data-pin="3" style="top:76%;left:92%">3</span>
                  <span class="pin" data-pin="4" style="top:52%;left:8%">4</span>
                  <span class="pin" data-pin="6" style="top:95%;left:84%">6</span>
                  <span class="pin" data-pin="7" style="top:95%;left:16%">7</span>
                </div>
              </div>
              <span class="pin" data-pin="2" style="top:20px;left:72%;background:var(--pin2,var(--stempel));color:var(--papier)">2</span>
              <span class="pin" data-pin="5" style="top:20px;left:24px;background:var(--pin2,var(--stempel));color:var(--papier)">5</span>
            </div>
            <p class="schema-hinweis">Schematisch · Ladebalken nicht maßstäblich</p>
          </div>
          <ol class="stellen">
            <li data-pin="1" tabindex="0"><span class="n">1</span><div><h3>Erster Bildschirm</h3><p class="k">Großes Bild, Slider, „Willkommen“. Das Angebot steht weiter unten.</p><p class="a">Was Sie anbieten, für wen und was es kostet, sofort sichtbar.</p></div></li>
            <li data-pin="2" tabindex="0"><span class="n">2</span><div><h3>Ladezeit auf dem Handy</h3><p class="k">Page-Builder mit vielen Plugins, die Seite baut sich spürbar auf.</p><p class="a">Schlank gebaut. Die Ladezeit messen Sie vor der Abnahme selbst.</p></div></li>
            <li data-pin="3" tabindex="0"><span class="n">3</span><div><h3>Kontaktformular</h3><p class="k">Die Mail landet im Spam, und niemand merkt es.</p><p class="a">Anfrage gespeichert, Versand getestet, wenige Pflichtfelder und eine Danke-Seite mit nächstem Schritt.</p></div></li>
            <li data-pin="4" tabindex="0"><span class="n">4</span><div><h3>Google</h3><p class="k">Beim Relaunch laufen alte Links und Suchergebnisse ins Leere.</p><p class="a">Jede alte URL bekommt eine Weiterleitung, jede Seite Title, Canonical und Schema.</p></div></li>
            <li data-pin="5" tabindex="0"><span class="n">5</span><div><h3>Domain und Konten</h3><p class="k">Laufen über das Konto der Agentur. Wechseln wird schwer.</p><p class="a">Laufen von Anfang an auf Ihren Namen. Sie können jederzeit wechseln.</p></div></li>
            <li data-pin="6" tabindex="0"><span class="n">6</span><div><h3>Nach dem Start</h3><p class="k">Wartungsvertrag als Pflicht, dazu Jahreslizenzen für Theme und Plugins.</p><p class="a">Einmalpreis, keine Pflichtwartung und keine Pflichtlizenzen im Grundprodukt. Weiterentwicklung nach Bedarf.</p></div></li>
            <li data-pin="7" tabindex="0"><span class="n">7</span><div><h3>Cookie-Banner</h3><p class="k">Schriften und Karten von fremden Servern, deshalb ein Banner vor dem ersten Klick.</p><p class="a">Schriften lokal, keine Dienste, die eine Einwilligung brauchen. Ohne Tracking entfällt der Banner.</p></div></li>
          </ol>
        </div>
        <p class="fair"><strong>Qualität lässt sich prüfen:</strong> Testen Sie Formular und Ladezeit, lassen Sie sich die Übergabe zeigen und prüfen Sie laufende Kosten vor dem Auftrag.</p>
      </div>
    </div>
  </section>

  <section class="abschnitt" id="zeit" aria-labelledby="h-zeit">
    <div class="wrap raster">
      <p class="nr"><b>04</b><span>Zeit</span></p>
      <div class="haupt breit">
        <h2 id="h-zeit">Wie lange es dauert, und wovon das abhängt.</h2>
        <p class="lead">Fertige Texte und Designs sparen ihre Erstellungsphase. Neue Texte, individuelle Layouts, Tracking und CRM bekommen eigene Zeitbeiträge. Bis <?php echo esc_html( $rules['implementation_tiers'][0]['max_pages'] ); ?> Standardseiten sind <?php echo esc_html( $days( $rules['implementation_tiers'][0]['days'] ) ); ?> Umsetzung und Prüfung vorgesehen, bis <?php echo esc_html( $rules['implementation_tiers'][1]['max_pages'] ); ?> Seiten <?php echo esc_html( $days( $rules['implementation_tiers'][1]['days'] ) ); ?>. Wir rechnen in Werktagen von Montag bis Freitag, ohne Feiertage; Ihre Freigabezeiten kommen separat dazu. Der verbindliche Start- und Veröffentlichungstermin steht im Angebot.</p>
        <div class="zeit">
          <div class="aw-zeit-details"><p id="zeit-aufteilung">Vorbereitung <?php echo esc_html( $days( $quote['preparation_days'] ) ); ?> · Umsetzung <?php echo esc_html( $days( $quote['implementation_days'] ) ); ?></p><dl class="aw-zeitpositionen">
            <div><dt>Umsetzung &amp; Prüfung</dt><dd id="tage-implementation"><?php echo esc_html( $days( $quote['components']['implementation'] ) ); ?></dd></div>
            <div id="zeit-texte"><dt>Texte erstellen</dt><dd id="tage-texts"><?php echo esc_html( $days( $quote['components']['texts'] ) ); ?></dd></div>
            <div id="zeit-design" hidden><dt>Screendesign</dt><dd id="tage-design"></dd></div>
            <div id="zeit-tracking" hidden><dt>Tracking einrichten</dt><dd id="tage-tracking"></dd></div>
            <div id="zeit-relaunch" hidden><dt>Bestand &amp; Weiterleitungen</dt><dd id="tage-relaunch"></dd></div>
            <div id="zeit-crm" hidden><dt>Standard-CRM</dt><dd id="tage-crm"></dd></div>
            <div id="zeit-dashboard" hidden><dt>Daten-Dashboard</dt><dd>Zusätzlich nach Angebot</dd></div>
          </dl><p class="aw-hinweis" id="zeit-hinweis">Planung bis zum geprüften Abnahmestand. Ihre Freigabezeiten und der Starttermin kommen separat dazu. Halbe Tage werden erst in der Gesamtsumme aufgerundet.</p></div>
          <div class="zeit-kopf"><span class="mono" id="zeit-umfang">Ihr Umfang: 3 Seiten · Neubau</span><strong id="zeit-gesamt"><?php echo esc_html( $days( $quote['days'] ) ); ?> geplant</strong></div>
          <div class="zeit-reihe">
            <div class="vorlauf"><span class="mono">Vor dem Start</span><p>Umfang vereinbaren, Briefing, Bilder, Rechtstexte und Zugänge liefern. Vorhandene Texte und Designs freigeben.</p></div>
            <div>
              <p class="uhr">Ab vereinbartem Start · bis zum Abnahmestand</p>
              <div class="bahn" id="bahn">
                <div class="phase"><span class="mono"><?php echo esc_html( $days( $quote['components']['texts'] ) ); ?></span><b>Texte erstellen</b></div>
                <div class="phase"><span class="mono"><?php echo esc_html( $days( $quote['components']['implementation'] - $rules['days']['qa'] ) ); ?></span><b>Umsetzung auf der Testumgebung</b></div>
                <div class="phase ende"><span class="mono"><?php echo esc_html( $days( $rules['days']['qa'] ) ); ?></span><b>Qualitätsprüfung und Abnahmestand</b></div>
              </div>
            </div>
          </div>
        </div>
        <ol class="bedingungen">
          <li><h3>Klare Ausgangslage</h3><p>Fertige Vorlagen müssen vollständig und freigegeben sein. Soll ich Texte oder Design erstellen, brauchen wir zuerst Ihr Briefing; diese Arbeit wird separat in der Zeit gezeigt.</p></li>
          <li><h3>Freigaben separat</h3><p>Für Ihre Rückmeldung sind je Korrekturrunde bis zu fünf Werktage vorgesehen. Diese Wartezeit und der Livegang gehören nicht zur angezeigten Produktionszeit.</p></li>
          <li><h3>Eine Person entscheidet</h3><p>Eine Ansprechperson, die freigibt. Abstimmungsschleifen im Team kosten die meiste Zeit.</p></li>
          <li><h3>Starttermin im Angebot</h3><p>Wann ich beginnen kann, steht mit Umfang und Preis im schriftlichen Angebot.</p></li>
        </ol>
      </div>
    </div>
  </section>

  <section class="abschnitt" id="fragen" aria-labelledby="h-fragen">
    <div class="wrap raster">
      <p class="nr"><b>05</b><span>Fragen</span></p>
      <div class="haupt">
        <h2 id="h-fragen">Was sonst noch gefragt wird.</h2>
        <div class="fragen">
<?php foreach ( nexus_get_website_faq_items() as $faq ) : ?>
<details><summary><?php echo esc_html( $faq['question'] ); ?></summary><p><?php echo esc_html( $faq['answer'] ); ?></p></details>
<?php endforeach; ?>
</div>
      </div>
    </div>
  </section>

  <section class="abschluss" id="anfrage" aria-labelledby="h-anfrage">
    <div class="wrap">
      <h2 id="h-anfrage">Neubau oder Relaunch: Was soll die Website können?</h2>
      <div>
        <p>Ihre Auswahl wird in die Anfrage übernommen. Ergänzen Sie, was Ihr Unternehmen anbietet und wann die Website stehen soll. Ich prüfe den Umfang und antworte mit Rückfragen oder einem Angebot. Beauftragt wird erst nach Ihrer Zusage.</p>
        <div class="aktion" style="margin-top:var(--s3)">
          <a class="btn" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="abschluss" data-track-action="cta_website_offer_close_project" data-track-category="lead_gen" data-track-section="website_offer_abschluss">Website-Projekt anfragen <span aria-hidden="true">→</span></a>
        </div>
        <p class="mikro">Persönlich, <?php echo esc_html( hu_response_promise( 'window' ) ); ?>.</p>
        <p class="direkt mikro">Lieber direkt: <a href="<?php echo esc_url( hu_get_contact_mailto() ); ?>" data-track-action="website_offer_close_mail" data-track-category="lead_gen"><?php echo esc_html( hu_get_contact_email() ); ?></a> · <a href="<?php echo esc_url( hu_get_contact_phone( 'link' ) ); ?>" data-track-action="website_offer_close_tel" data-track-category="lead_gen"><?php echo esc_html( hu_get_contact_phone() ); ?></a></p>
      </div>
    </div>
  </section>


<div class="leiste-unten tafel" hidden inert id="leiste" aria-hidden="true">
  <a class="aw-auswahl-edit" href="#angebot" data-track-action="website_offer_edit_scope" data-track-category="navigation">Auswahl ändern ↑</a><p><b id="leiste-text"><?php echo esc_html( '3 Seiten · Neubau · Texte inklusive' ); ?></b><span id="leiste-preis"><?php echo esc_html( hu_format_eur( $quote['price'] ) . ' netto' ); ?></span></p>
  <a class="btn" id="cta-leiste" tabindex="-1" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="leiste" data-track-action="cta_website_offer_sticky_project" data-track-category="lead_gen" data-track-section="website_offer_leiste">Anfragen <span aria-hidden="true">→</span></a>
</div>


</div>
<?php get_footer(); ?>
