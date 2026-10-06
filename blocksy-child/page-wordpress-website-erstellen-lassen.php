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
$quote = hu_website_quote( 3, 'neubau', false, [ 'utility_pages' => 0, 'standard_pages' => 1, 'sales_pages' => 1, 'texte' => 0 ] );
$text_quote = hu_website_quote( 3, 'neubau', false, [ 'utility_pages' => 0, 'standard_pages' => 1, 'sales_pages' => 1, 'texte' => 1 ] );
$rules = hu_website_calculator_rules();
$days = static function ( $value ) { return number_format( (float) $value, floor( (float) $value ) === (float) $value ? 0 : 1, ',', '.' ) . ( 1.0 === (float) $value ? ' Werktag' : ' Werktage' ); };
$contact_url = add_query_arg( [ 'seiten' => 3, 'art' => 'neubau', 'kurz' => 0, 'standard' => 1, 'leistung' => 1, 'texte' => 0 ], hu_get_contact_intake_url( 'project', 'website' ) );
$references = array_slice( hu_website_reference_projects(), 0, 1 );
$eur = static function ( $value ) { return str_replace( ' €', "\u{00A0}€", hu_format_eur( $value ) ); };
$scenarios = [
    [ 'pages' => 1, 'utility' => 0, 'standard' => 0, 'sales' => 0, 'title' => 'Kompakt', 'example' => 'Eine Hauptseite', 'purpose' => 'Angebot, Beleg und Kontakt auf einer Seite.' ],
    [ 'pages' => 3, 'utility' => 0, 'standard' => 1, 'sales' => 1, 'title' => 'Unternehmen', 'example' => 'Hauptseite + Leistung + Über uns', 'purpose' => 'Ein Angebot verkaufen und das Unternehmen separat erklären.' ],
    [ 'pages' => 5, 'utility' => 1, 'standard' => 1, 'sales' => 2, 'title' => 'Leistungswebsite', 'example' => 'Hauptseite + 2 Leistungen + Über uns + Kontakt', 'purpose' => 'Mehrere Leistungen mit eigener Such- und Verkaufslogik.' ],
];
get_header();
?>
<div id="website-content" class="doku anfrage-website" data-track-page="website_offer" data-website-product data-website-rules="<?php echo esc_attr( wp_json_encode( $rules ) ); ?>">
  <nav class="wrap krumen" aria-label="Brotkrumen"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Startseite</a> / WordPress-Website erstellen lassen</nav>

  <section class="hero" id="hero" aria-labelledby="h-hero">
    <div class="wrap">
      <div>
        <div class="produkt"><span class="marke-produkt">Die Anfrage-Website</span><span class="mono">WordPress · Neubau oder Relaunch</span></div>
        <h1 id="h-hero">WordPress-Website erstellen lassen.<br><span class="hero-akzent">Ihr Angebot klar.<br>Der nächste Schritt sichtbar.</span></h1>
        <p class="aw-einstieg"><?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_MIN ) ); ?> netto inkl. Grundsystem &amp; erster Hauptseite · weitere Seiten ab <?php echo esc_html( $eur( HU_WEBSITE_PAGE_UTILITY ) ); ?></p>
        <p class="lead">Für Unternehmen und Selbstständige, die ihre Leistungen erklären und passende Anfragen erhalten möchten. Ich entwickle Struktur, Gestaltung und Anfrageweg zusammen. Ihre fertigen Texte pflege ich ein; Texterstellung können Sie dazunehmen.</p>
        <ul class="aw-hero-checks" aria-label="Im Grundprodukt enthalten"><li>Responsive Gestaltung</li><li>Technisches SEO</li><li>Formular mit Bestätigung</li></ul>
        <div class="aktion">
          <a class="btn" href="#angebot" data-track-action="website_offer_hero_configure" data-track-category="navigation" data-track-section="website_offer_hero">Website zusammenstellen <span aria-hidden="true">→</span></a>
          <a class="btn btn--sekundaer" href="#beleg" data-track-action="website_offer_to_beleg" data-track-category="navigation">Projekte ansehen <span aria-hidden="true">↓</span></a>
        </div>
        <p class="mikro">Antwort <?php echo esc_html( hu_response_promise( 'window' ) ); ?>, mit Rückfragen oder einem Festpreis-Angebot.</p>
      </div>
      <aside class="formel tafel aw-produktkarte" aria-label="Grundprodukt und Einstiegspreis">
        <div class="aw-produkt-kopf"><span class="mono">Die Anfrage-Website</span><span class="aw-status"><i aria-hidden="true"></i>Erweiterbar</span></div>
        <div class="aw-preis-lockup">
          <p class="mono">Festpreis ab</p>
          <p class="betrag"><?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_MIN ) ); ?><small>netto</small></p>
          <p class="aw-produktkern">Grundsystem + erste Hauptseite</p>
        </div>
        <div class="aw-modulband" aria-label="Modular erweiterbar um Seiten, Texte, Design und Tracking"><span>Seiten</span><span>Texte</span><span>Design</span><span>Tracking</span></div>
        <p class="regel">Weitere Seiten nach Typ ab <?php echo esc_html( $eur( HU_WEBSITE_PAGE_UTILITY ) ); ?>. Mit fertigen Inhalten: <?php echo esc_html( $days( hu_website_quote( 1 )['components']['implementation'] ) ); ?> Umsetzung geplant. <span id="hero-bauzeit">Ihre Auswahl: <?php echo esc_html( $days( $quote['days'] ) ); ?> geplant.</span></p>
        <ul class="aw-basis-kurz"><li>Basisgestaltung inklusive, Texterstellung optional</li><li>Formular, Bestätigungsmail &amp; Danke-Seite</li><li>Domain und Code gehören Ihnen</li></ul>
        <a class="zum leise" href="#angebot" data-track-action="website_offer_to_angebot" data-track-category="navigation">Produkt konfigurieren <span aria-hidden="true">↓</span></a>
      </aside>
    </div>
  </section>

  <nav class="wrap aw-kapitel" aria-label="Auf dieser Seite">
    <span class="mono">Direkt zu</span>
    <a href="#angebot" data-track-action="website_offer_chapter_scope" data-track-category="navigation">Preis &amp; Umfang</a>
    <a href="#beleg" data-track-action="website_offer_chapter_proof" data-track-category="navigation">Projekte</a>
    <a href="#fragen" data-track-action="website_offer_chapter_faq" data-track-category="navigation">Fragen</a>
  </nav>

  <section class="abschnitt aw-referenzen" id="beleg" aria-labelledby="h-beleg">
    <div class="wrap">
      <div class="aw-referenzen-kopf">
        <p class="mono">01 · Ein reales Projekt</p>
        <h2 id="h-beleg">Nicht nur Website.<br>Der Anfrageweg dahinter.</h2>
        <p class="lead">E3 New Energy zeigt die Arbeit, die über Gestaltung hinausgeht: Landingpages, technisches SEO, Tracking und die Strecke bis ins CRM.</p>
      </div>
      <div class="aw-projekte aw-projekte--fokus">
<?php foreach ( $references as $index => $reference ) : $reference_id = 'aw-projekt-' . ( $index + 1 ); ?>
        <article class="aw-projekt" aria-labelledby="<?php echo esc_attr( $reference_id ); ?>">
          <figure class="aw-projekt-ansicht">
            <div class="aw-projekt-browser" aria-hidden="true"><span><?php echo esc_html( wp_parse_url( $reference['url'], PHP_URL_HOST ) ); ?></span><span>Projektansicht</span></div>
            <div class="bild"><img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/' . $reference['screenshot'] ); ?>" width="960" height="600" decoding="async" loading="lazy" alt="<?php echo esc_attr( $reference['alt'] ); ?>"></div>
            <figcaption><?php echo esc_html( $reference['role'] ); ?></figcaption>
          </figure>
          <div class="aw-projekt-copy">
            <p class="mono aw-projekt-fach"><?php echo esc_html( $reference['tag'] ); ?></p>
            <p class="aw-projekt-name"><?php echo esc_html( $reference['name'] ); ?></p>
            <h3 id="<?php echo esc_attr( $reference_id ); ?>"><?php echo esc_html( $reference['title'] ); ?></h3>
            <p class="aw-projekt-text"><?php echo esc_html( $reference['text'] ); ?></p>
<?php if ( ! empty( $reference['flow'] ) ) : ?>
            <ol class="aw-projekt-flow" aria-label="Aufgebaute Anfragestrecke">
<?php foreach ( $reference['flow'] as $step ) : ?>
              <li><?php echo esc_html( $step ); ?></li>
<?php endforeach; ?>
            </ol>
<?php endif; ?>
            <a class="aw-projekt-link" href="<?php echo esc_url( $reference['url'] ); ?>" target="_blank" rel="noopener" data-track-action="website_offer_reference_open" data-track-category="proof" data-track-section="website_offer_beleg">Projekt ansehen <span aria-hidden="true">↗</span><span class="nur-vorlesen">: <?php echo esc_html( $reference['name'] ); ?> (öffnet in neuem Tab)</span></a>
          </div>
        </article>
<?php endforeach; ?>
      </div>
      <div class="folgerung">
        <a class="btn btn--sekundaer" href="#angebot" data-track-action="website_offer_proof_to_scope" data-track-category="navigation">Eigene Website zusammenstellen <span aria-hidden="true">→</span></a>
      </div>
    </div>
  </section>

  <section class="abschnitt aw-konfigurator" id="angebot" aria-labelledby="h-angebot">
    <div class="wrap">
      <div class="aw-config-heading"><div><p class="mono">02 · Preis &amp; Umfang</p><h2 id="h-angebot" tabindex="-1">Wie viel Website brauchen Sie?</h2></div><p>Basisgestaltung ist enthalten.<br><strong>Seitentypen, Texte und Extras werden getrennt kalkuliert.</strong></p></div>
      <noscript><p class="mikro">Beispiel: drei Seiten, Neubau, fertige Texte, ohne Extras. Nennen Sie Seitentypen und gewünschten Umfang in der Anfrage.</p></noscript>
      <div class="aw-config-grid">
        <div class="aw-config-controls">
          <div class="aw-config-column">
            <fieldset class="aw-feld aw-umfang">
              <legend><span class="aw-schritt">01</span> Welche Seiten brauchen Sie?</legend>
              <p class="aw-feld-intro">Grundsystem und erste Hauptseite sind enthalten. Ergänzen Sie weitere Seiten nach Aufgabe – nicht nach einer pauschalen Seitenzahl.</p>
              <div id="beispiele" class="groessen" data-website-controls hidden role="group" aria-label="Website-Beispiele wählen">
<?php foreach ( $scenarios as $scenario ) :
    $example_quote = hu_website_quote( $scenario['pages'], 'neubau', false, [
        'utility_pages' => $scenario['utility'],
        'standard_pages' => $scenario['standard'],
        'sales_pages' => $scenario['sales'],
        'texte' => 0,
    ] );
?>
                <button type="button" class="aw-szenario" data-website-scenario="<?php echo esc_attr( (string) $scenario['pages'] ); ?>" data-utility="<?php echo esc_attr( (string) $scenario['utility'] ); ?>" data-standard="<?php echo esc_attr( (string) $scenario['standard'] ); ?>" data-sales="<?php echo esc_attr( (string) $scenario['sales'] ); ?>" aria-pressed="<?php echo 3 === $scenario['pages'] ? 'true' : 'false'; ?>" data-track-action="website_offer_scenario_<?php echo esc_attr( (string) $scenario['pages'] ); ?>" data-track-category="navigation"><span><?php echo esc_html( $scenario['title'] ); ?></span><small><?php echo esc_html( $scenario['example'] ); ?></small><b><?php echo esc_html( $eur( $example_quote['price'] ) ); ?></b></button>
<?php endforeach; ?>
              </div>
              <div class="aw-seitentypen" data-website-controls hidden>
                <div class="aw-seitenzeile" data-page-row="utility"><p>Kurze Seite<small>Kontakt, Standort, kurze Information · +<?php echo esc_html( $eur( HU_WEBSITE_PAGE_UTILITY ) ); ?></small></p><div class="stepper"><button type="button" id="utility-minus" data-page-type="utility" data-page-delta="-1" aria-label="Eine kurze Seite weniger">−</button><output id="utility-pages" aria-label="Kurze Seiten">0</output><button type="button" id="utility-plus" data-page-type="utility" data-page-delta="1" aria-label="Eine kurze Seite mehr">+</button></div></div>
                <div class="aw-seitenzeile" data-page-row="standard"><p>Standardseite<small>Über uns, Team, Unternehmen · +<?php echo esc_html( $eur( HU_WEBSITE_PAGE_STANDARD ) ); ?></small></p><div class="stepper"><button type="button" id="standard-minus" data-page-type="standard" data-page-delta="-1" aria-label="Eine Standardseite weniger">−</button><output id="standard-pages" aria-label="Standardseiten">1</output><button type="button" id="standard-plus" data-page-type="standard" data-page-delta="1" aria-label="Eine Standardseite mehr">+</button></div></div>
                <div class="aw-seitenzeile" data-page-row="sales"><p>Leistungs-/Verkaufsseite<small>Leistung, Angebot, Money Page · +<?php echo esc_html( $eur( HU_WEBSITE_PAGE_SALES ) ); ?></small></p><div class="stepper"><button type="button" id="sales-minus" data-page-type="sales" data-page-delta="-1" aria-label="Eine Leistungsseite weniger">−</button><output id="sales-pages" aria-label="Leistungsseiten">1</output><button type="button" id="sales-plus" data-page-type="sales" data-page-delta="1" aria-label="Eine Leistungsseite mehr">+</button></div></div>
              </div>
              <p class="aw-hinweis">Die erste Hauptseite ist immer enthalten. Maximal <?php echo esc_html( (string) HU_WEBSITE_CALCULATOR_MAX ); ?> Inhaltsseiten im Rechner; Impressum, Datenschutz, Danke- und 404-Seite zählen nicht mit.</p>
              <div class="art" data-website-controls hidden role="group" aria-label="Projektart"><button type="button" data-art="neubau" aria-pressed="true">Neubau</button><button type="button" data-art="relaunch" aria-pressed="false">Relaunch <small>+<?php echo esc_html( $days( $rules['days']['relaunch'] ) ); ?></small></button></div>
            </fieldset>
            <fieldset class="aw-feld aw-inhalte">
              <legend><span class="aw-schritt">02</span> Texte</legend>
              <label class="aw-option" data-website-controls hidden><input type="checkbox" id="texte"><span class="aw-option-inhalt"><strong>Texte erstellen lassen</strong><small>Auf Basis Ihrer Angaben. Der Aufwand richtet sich nach den gewählten Seitentypen.</small></span><span class="aw-option-preis" id="text-option-preis">+<?php echo esc_html( $eur( $text_quote['text_price'] ) ); ?></span></label>
              <p class="aw-hinweis" id="text-hinweis">Fertige, freigegebene Texte werden ohne Texterstellungs-Aufpreis eingepflegt.</p>
            </fieldset>
            <fieldset class="aw-feld aw-design">
              <legend><span class="aw-schritt">03</span> Wie soll sie aussehen?</legend>
              <div class="aw-extras" data-website-controls hidden role="radiogroup" aria-label="Design-Umfang">
                <label class="aw-option"><input type="radio" name="website-design" id="design-basis" value="basis" checked><span class="aw-option-inhalt"><strong>Basisgestaltung</strong><small>Bewährte Layouts, Ihre Farben und Typografie.</small></span><span class="aw-option-preis">Inklusive</span></label>
                <label class="aw-option"><input type="radio" name="website-design" id="design-vorhanden" value="vorhanden"><span class="aw-option-inhalt"><strong>Design ist vorhanden</strong><small>Fertige Vorlage umsetzen. Umfang vorab prüfen.</small></span><span class="aw-option-preis">+0 €*</span></label>
                <label class="aw-option"><input type="radio" name="website-design" id="screendesign" value="neu"><span class="aw-option-inhalt"><strong>Individuelles Screendesign</strong><small>Ein eigener Entwurf für Desktop und Mobil.</small></span><span class="aw-option-preis"><span id="design-option-preis">+<?php echo esc_html( $eur( HU_WEBSITE_DESIGN_FIRST ) ); ?></span><small id="design-option-layouts">1 Layout</small></span></label>
              </div>
              <div class="aw-design-details" id="design-details" hidden>
                <label for="design-layouts">Unterschiedliche Seitenlayouts</label>
                <select id="design-layouts" aria-describedby="design-layout-hinweis"><?php for ( $n = 1; $n <= 3; $n++ ) : ?><option value="<?php echo esc_attr( (string) $n ); ?>" <?php selected( $n, 1 ); ?>><?php echo esc_html( $n . ( 1 === $n ? ' Layout' : ' Layouts' ) ); ?></option><?php endfor; ?></select>
                <p class="aw-hinweis" id="design-layout-hinweis">Ein Layout ist eine eigene Seitengestaltung. Sie können es auf mehreren Seiten nutzen. Jedes weitere unterschiedliche Layout +<?php echo esc_html( $eur( HU_WEBSITE_DESIGN_EXTRA ) ); ?>.</p>
              </div>
              <p class="aw-hinweis" id="design-hinweis">Basisdesign inklusive.</p>
            </fieldset>
            <details class="aw-extras-panel" data-website-controls hidden>
              <summary><span><span class="aw-schritt">04</span> Optionale Erweiterungen<small>Tracking · CRM · Daten-Dashboard</small></span><span class="aw-extras-count" id="extras-count">Keine gewählt</span></summary>
              <fieldset class="aw-feld aw-erweiterungen">
              <legend class="aw-live-status">Optionale Erweiterungen</legend>
              <div class="aw-extras" data-website-controls hidden>
                <label class="aw-option"><input type="checkbox" id="tracking"><span class="aw-option-inhalt"><strong>Conversion-Tracking</strong><small>Anfragen in GA4 &amp; Google Ads messen. +<?php echo esc_html( $days( $rules['days']['tracking'] ) ); ?></small></span><span class="aw-option-preis">+<?php echo esc_html( $eur( $rules['prices']['tracking'] ) ); ?></span></label>
                <label class="aw-option"><input type="checkbox" id="crm"><span class="aw-option-inhalt"><strong>CRM-Anbindung</strong><small>HubSpot oder Bitrix24: ein Formular. +<?php echo esc_html( $days( $rules['days']['crm'] ) ); ?></small></span><span class="aw-option-preis">+<?php echo esc_html( $eur( HU_WEBSITE_CRM_STANDARD ) ); ?></span></label>
                <label class="aw-option"><input type="checkbox" id="dashboard"><span class="aw-option-inhalt"><strong>Daten-Dashboard</strong><small>Ihre Daten an einem Ort. Umfang nach Abstimmung.</small></span><span class="aw-option-preis">Nach Angebot</span></label>
              </div>
              </fieldset>
            </details>
            <div class="aw-standard-kurz"><span class="mono">Immer inklusive</span><ul><li>Technisches SEO &amp; mobile Darstellung</li><li>Formular, Bestätigungsmail &amp; Danke-Seite</li><li>Impressum- &amp; Datenschutz-Seite</li></ul><a href="#lieferumfang" data-track-action="website_offer_included_details" data-track-category="navigation">Gesamten Lieferumfang ansehen ↓</a></div>
          </div>
        </div>
        <aside class="aw-zusammenfassung" aria-labelledby="h-auswahl">
          <div class="aw-summary-kopf"><span class="mono">Ihre Konfiguration</span><span class="aw-status">Einmalpreis</span></div>
          <h3 id="h-auswahl">Ihre Auswahl</h3>
          <p class="aw-summary-sub" id="auswahl-art">3 Seiten · Neubau · 1 Standard · 1 Leistung</p>
          <p class="aw-summary-sub" id="auswahl-design">Basisdesign inklusive</p>
          <div class="summe">
            <p class="mono" id="preis-label">Ihr Einmalpreis</p>
            <p class="gesamt"><span id="gesamt"><?php echo esc_html( $eur( $quote['price'] ) ); ?></span><small>netto · zzgl. USt.</small></p>
            <p class="rechnung" id="rechnung"><?php echo esc_html( hu_freelancer_website_price() . ' + ' . hu_format_eur( HU_WEBSITE_PAGE_STANDARD ) . ' Standard + ' . hu_format_eur( HU_WEBSITE_PAGE_SALES ) . ' Leistung' ); ?></p>
            <p id="angebot-hinweis" class="aw-angebot-hinweis" hidden></p>
            <div class="aw-zeitkalkulation"><span id="dauer-label">Produktionszeit</span><b id="bauzeit"><?php echo esc_html( $days( $quote['days'] ) ); ?></b><a href="#zeit" data-track-action="website_offer_to_zeit" data-track-category="navigation">Zeitbeiträge ansehen ↓</a></div>
            <a class="btn" id="cta-angebot" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="angebot" data-track-action="cta_website_offer_scope_project" data-track-category="lead_gen" data-track-section="website_offer_angebot">Weiter zur Anfrage <span aria-hidden="true">→</span></a>
            <p class="aw-hinweis">Ihre Auswahl wird übernommen. Antwort <?php echo esc_html( hu_response_promise( 'window' ) ); ?>.</p>
          </div>
          <details class="aw-price-details"><summary>So setzt sich der Preis zusammen</summary>
          <dl class="aw-preispositionen">
            <div><dt>Grundsystem + erste Hauptseite</dt><dd><?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_MIN ) ); ?></dd></div>
            <div id="summary-utility" hidden><dt><span id="count-utility">0</span> kurze Seiten</dt><dd id="betrag-utility"><?php echo esc_html( $eur( 0 ) ); ?></dd></div>
            <div id="summary-standard"><dt><span id="count-standard">1</span> Standardseite</dt><dd id="betrag-standard"><?php echo esc_html( $eur( HU_WEBSITE_PAGE_STANDARD ) ); ?></dd></div>
            <div id="summary-sales"><dt><span id="count-sales">1</span> Leistungsseite</dt><dd id="betrag-sales"><?php echo esc_html( $eur( HU_WEBSITE_PAGE_SALES ) ); ?></dd></div>
            <div id="summary-copy"><dt id="auswahl-texte">Eigene Texte</dt><dd id="betrag-texte"><?php echo esc_html( $eur( 0 ) ); ?></dd></div>
            <div id="summary-tracking" hidden><dt>Conversion-Tracking</dt><dd><?php echo esc_html( $eur( $rules['prices']['tracking'] ) ); ?></dd></div>
            <div id="summary-screendesign" hidden><dt>Screendesign</dt><dd id="betrag-design"></dd></div>
            <div id="summary-crm" hidden><dt>CRM-Anbindung</dt><dd><?php echo esc_html( $eur( HU_WEBSITE_CRM_STANDARD ) ); ?></dd></div>
            <div id="summary-dashboard" hidden><dt>Daten-Dashboard</dt><dd>Nach Angebot</dd></div>
          </dl>
          </details>
          <p class="aw-eigentum"><strong>Unverbindlich anfragen.</strong>Festpreis und Starttermin vor Auftrag bestätigt. Freigabezeiten sowie Domain, Hosting und externe Dienste separat.</p>
          <p class="aw-live-status" id="konfiguration-status" role="status" aria-live="polite" aria-atomic="true"></p>
        </aside>
      </div>
      <p class="aw-config-fuss">Alle Preise netto. Domain, Hosting und externe Dienstkosten separat. *Vorhandene Designs und CRM werden vor Auftragserteilung geprüft. <a href="#erweiterungen">Umfang der Erweiterungen ↓</a></p>
      <details class="inklusive aw-lieferumfang-kompakt" id="lieferumfang">
        <summary><span><span class="mono">Standardausstattung</span><strong>Vollständigen Lieferumfang ansehen</strong></span><span class="aw-status">Inklusive</span></summary>
        <div class="aw-lieferumfang-inhalt">
          <p class="mikro">Technisches SEO, responsive Umsetzung, Anfrageweg, Rechtstext-Seiten, QA und Übergabe sind im Grundprodukt enthalten.</p>
          <div class="gruppen"><details class="gruppe"><summary><span>Inhalte</span><span class="aw-inkl-label">Inklusive</span></summary><ul>
                <li>Gliederung jeder Seite: Angebot, Preis, Beleg</li>
                <li>Texterstellung optional je Seitentyp kalkulierbar, auf Basis Ihrer Angaben</li>
                <li>Ihre vorhandenen Texte und Bilder eingepflegt</li>
                <li>Einpflege und technischer Feinschliff Ihrer freigegebenen Texte</li>
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
          <div class="klartext">
            <div><h3>Was als Seite zählt</h3><p>Eine Inhaltsseite mit eigener URL. Impressum, Datenschutz, Danke- und 404-Seite zählen nicht mit.</p></div>
            <div><h3>Separat kalkuliert</h3><p>Keyword-Recherche, Fotos und Logo, Shop, weitere Schnittstellen, mehrere Sprachen und mehr als zehn Inhaltsseiten.</p></div>
            <div><h3>Laufende Kosten</h3><p>Domain, Hosting und externe Dienste zahlen Sie direkt beim jeweiligen Anbieter.</p></div>
          </div>
        </div>
      </details>
      <div class="aw-erweiterungsumfang" id="erweiterungen">
        <details class="aw-erweiterungen-kompakt">
          <summary><span><span class="mono">Erweiterungen</span><strong>Was Design, Tracking, CRM und Dashboard genau enthalten</strong></span><span>Details ↓</span></summary>
          <div class="aw-erweiterungen-grid">
            <div><h3>Individuelles Screendesign</h3><p>Gestaltungsrichtung in Figma, Desktop und Mobil je gebuchtem Layout, zwei gebündelte Korrekturrunden.</p></div>
            <div><h3>Conversion-Tracking</h3><p>GA4, Google Tag Manager, Consent Mode und eine Google-Ads-Conversion für erfolgreich abgesendete Standard-Anfragen.</p></div>
            <div><h3>CRM-Anbindung Standard</h3><p>Ein Formular an ein bestehendes HubSpot oder Bitrix24, bis zu zehn Felder und ein Kontakt- oder Lead-Objekt.</p></div>
            <div><h3>Daten-Dashboard</h3><p>Umfang, Datenquellen, Preis und zusätzliche Produktionszeit werden vor Auftrag separat festgelegt.</p></div>
          </div>
        </details>
      </div>
    </div>
  </section>

  <section class="dunkel tafel" id="unterschied" aria-labelledby="h-unterschied">
    <div class="wrap raster">
      <p class="nr"><b>03</b><span>Unterschied</span></p>
      <div class="haupt breit">
        <h2 id="h-unterschied">Drei Dinge müssen funktionieren.</h2>
        <p class="lead">Nicht möglichst viele Effekte. Sondern Klarheit vor dem Klick, ein geprüfter Anfrageweg und eine Website, die Ihnen danach wirklich gehört.</p>
        <div class="aw-prinzipien" data-principles>
          <article style="--aw-i:0">
            <span class="mono">01 · Klarheit</span>
            <div class="aw-prinzip-signal" aria-hidden="true"><span>Angebot</span><i></i><span>Beweis</span><i></i><span>CTA</span></div>
            <h3>Das Angebot ist im ersten Bildschirm verständlich.</h3>
            <p>Leistung, passende Zielgruppe und nächster Schritt werden priorisiert. Navigation und Buttons konkurrieren nicht um Aufmerksamkeit.</p>
            <ul class="aw-prinzip-output"><li>klare Leistungslogik</li><li>eindeutiger nächster Schritt</li></ul>
          </article>
          <article style="--aw-i:1">
            <span class="mono">02 · Anfrageweg</span>
            <div class="aw-prinzip-signal aw-prinzip-signal--vier" aria-hidden="true"><span>Formular</span><i></i><span>Mail</span><i></i><span>Danke</span><i></i><span>CRM</span></div>
            <h3>Eine Anfrage wird nicht nur abgeschickt, sondern geprüft.</h3>
            <p>Formular, Speicherung, Mailversand, Bestätigung und Danke-Seite werden vor der Übergabe als zusammenhängende Strecke getestet.</p>
            <ul class="aw-prinzip-output"><li>getestete Zustände</li><li>nachvollziehbare Übergabe</li></ul>
          </article>
          <article style="--aw-i:2">
            <span class="mono">03 · Eigentum</span>
            <div class="aw-prinzip-signal aw-prinzip-signal--besitz" aria-hidden="true"><span>Domain</span><span>Code</span><span>Zugänge</span></div>
            <h3>Code, Zugänge und technische Grundlage bleiben in Ihrer Hand.</h3>
            <p>Technisches SEO, responsive Umsetzung und Übergabe gehören zum System. Keine Pflichtwartung und kein Pagebuilder-Lock-in im Grundprodukt.</p>
            <ul class="aw-prinzip-output"><li>kein technischer Lock-in</li><li>dokumentierte Übergabe</li></ul>
          </article>
        </div>
        <div class="aw-ergebnisband"><span class="mono">Sie bekommen</span><strong>Verständlichkeit vor dem Klick.</strong><strong>Einen geprüften Anfrageweg.</strong><strong>Eine Website, die Ihnen gehört.</strong></div>
        <div class="aw-pruefung"><p><strong>Vor der Abnahme:</strong> Handyansicht, Anfrageweg, technische Basis und Übergabe gemeinsam prüfen.</p><a class="btn btn--sekundaer" href="#lieferumfang" data-track-action="website_offer_quality_checklist" data-track-category="navigation">Lieferumfang ansehen <span aria-hidden="true">→</span></a></div>
      </div>
    </div>
  </section>

  <section class="abschnitt" id="zeit" aria-labelledby="h-zeit">
    <div class="wrap raster">
      <p class="nr"><b>04</b><span>Ablauf</span></p>
      <div class="haupt breit">
        <h2 id="h-zeit">Vom Briefing zum Livegang.</h2>
        <p class="lead">Der Rechner zeigt die geplante Produktionszeit. Starttermin und Ihre Freigabezeiten werden separat vereinbart.</p>
        <div class="zeit">
          <div class="zeit-kopf"><span class="mono" id="zeit-umfang">Ihr Umfang: 3 Seiten · Neubau</span><strong id="zeit-gesamt"><?php echo esc_html( $days( $quote['days'] ) ); ?> geplant</strong></div>
          <div class="bahn aw-bahn-kompakt" id="bahn">
            <div class="phase"><span class="mono">Vor dem Start</span><b>Briefing &amp; Material</b></div>
            <div class="phase"><span class="mono"><?php echo esc_html( $days( $quote['days'] ) ); ?></span><b>Umsetzung &amp; Prüfung</b></div>
            <div class="phase ende"><span class="mono">danach</span><b>Freigabe &amp; Livegang</b></div>
          </div>
          <details class="aw-plan-details aw-zeit-komponenten">
            <summary>Zeitbeiträge der Auswahl ansehen</summary>
            <div class="aw-zeit-details"><p id="zeit-aufteilung">Vorbereitung <?php echo esc_html( $days( $quote['preparation_days'] ) ); ?> · Umsetzung <?php echo esc_html( $days( $quote['implementation_days'] ) ); ?></p><dl class="aw-zeitpositionen">
              <div><dt>Umsetzung &amp; Prüfung</dt><dd id="tage-implementation"><?php echo esc_html( $days( $quote['components']['implementation'] ) ); ?></dd></div>
              <div id="zeit-texte" hidden><dt>Texte erstellen</dt><dd id="tage-texts"><?php echo esc_html( $days( $quote['components']['texts'] ) ); ?></dd></div>
              <div id="zeit-design" hidden><dt>Screendesign</dt><dd id="tage-design"></dd></div>
              <div id="zeit-tracking" hidden><dt>Tracking</dt><dd id="tage-tracking"></dd></div>
              <div id="zeit-relaunch" hidden><dt>Relaunch</dt><dd id="tage-relaunch"></dd></div>
              <div id="zeit-crm" hidden><dt>CRM</dt><dd id="tage-crm"></dd></div>
              <div id="zeit-dashboard" hidden><dt>Daten-Dashboard</dt><dd>Zusätzlich nach Angebot</dd></div>
            </dl><p class="aw-hinweis" id="zeit-hinweis">Planung bis zum geprüften Abnahmestand. Ihre Freigabezeiten und der Starttermin kommen separat dazu.</p></div>
          </details>
        </div>
      </div>
    </div>
  </section>

  <section class="abschnitt" id="fragen" aria-labelledby="h-fragen">
    <div class="wrap raster">
      <p class="nr"><b>05</b><span>Fragen</span></p>
      <div class="haupt">
        <h2 id="h-fragen">Antworten vor dem Auftrag.</h2>
        <p class="lead">Kosten, Inhalte, Gestaltung und späterer Betrieb. Die wichtigsten Grenzen und Leistungen stehen hier zusammen.</p>
        <div class="fragen" data-exclusive-details>
<?php foreach ( nexus_get_website_faq_items() as $faq ) : ?>
<details><summary><?php echo esc_html( $faq['question'] ); ?></summary><p><?php echo esc_html( $faq['answer'] ); ?></p></details>
<?php endforeach; ?>
</div>
      </div>
    </div>
  </section>

  <section class="abschluss" id="anfrage" aria-labelledby="h-anfrage">
    <div class="wrap">
      <div class="aw-abschluss-kopf"><p class="mono">Ihr nächster Schritt</p><h2 id="h-anfrage">Was soll Ihre neue Website leisten?</h2><p class="aw-close-scope" id="abschluss-umfang">3 Seiten · Neubau</p><p class="aw-close-price"><span id="abschluss-preis"><?php echo esc_html( $eur( $quote['price'] ) ); ?></span><small id="abschluss-preiszusatz">netto · zzgl. USt.</small></p></div>
      <div>
        <p>Ihre Auswahl wird in die Anfrage übernommen. Ergänzen Sie, was Ihr Unternehmen anbietet und wann die Website stehen soll. Ich prüfe den Umfang und antworte mit Rückfragen oder einem Angebot. Beauftragt wird erst nach Ihrer Zusage.</p>
        <div class="aktion">
          <a class="btn" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="abschluss" data-track-action="cta_website_offer_close_project" data-track-category="lead_gen" data-track-section="website_offer_abschluss">Website-Projekt anfragen <span aria-hidden="true">→</span></a>
        </div>
        <p class="mikro">Persönlich, <?php echo esc_html( hu_response_promise( 'window' ) ); ?>.</p>
        <p class="direkt mikro">Lieber direkt: <a href="<?php echo esc_url( hu_get_contact_mailto() ); ?>" data-track-action="website_offer_close_mail" data-track-category="lead_gen"><?php echo esc_html( hu_get_contact_email() ); ?></a> · <a href="<?php echo esc_url( hu_get_contact_phone( 'link' ) ); ?>" data-track-action="website_offer_close_tel" data-track-category="lead_gen"><?php echo esc_html( hu_get_contact_phone() ); ?></a></p>
      </div>
    </div>
  </section>


<div class="leiste-unten tafel" hidden inert id="leiste" aria-hidden="true">
  <a class="aw-auswahl-edit" href="#angebot" data-track-action="website_offer_edit_scope" data-track-category="navigation">Auswahl ändern ↑</a><p><b id="leiste-text"><?php echo esc_html( '3 Seiten · Neubau · 1 Standard · 1 Leistung' ); ?></b><span id="leiste-preis"><?php echo esc_html( hu_format_eur( $quote['price'] ) . ' netto' ); ?></span></p>
  <a class="btn" id="cta-leiste" tabindex="-1" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="leiste" data-track-action="cta_website_offer_sticky_project" data-track-category="lead_gen" data-track-section="website_offer_leiste">Anfragen <span aria-hidden="true">→</span></a>
</div>


</div>
<?php get_footer(); ?>
