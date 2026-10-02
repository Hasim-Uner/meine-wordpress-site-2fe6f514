<?php
/**
 * Template Name: WordPress-Website erstellen lassen
 * Description: Die Anfrage-Website: Festpreis, Umfangsrechner und Bauzeit.
 * Copy/reihenfolge: freigegebener Prototyp anfrage-website.html (02.10.2026).
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
    hu_enqueue_js( 'hu-website-product-events', 'website-product-events.js', [] );
    hu_enqueue_js( 'hu-anfrage-website', 'anfrage-website.js', [ 'hu-website-product-events' ] );
}, 90 );
$quote = hu_website_quote( 3 );
$contact_url = add_query_arg( [ 'seiten' => 3, 'art' => 'neubau' ], hu_get_contact_intake_url( 'project', 'website' ) );
$psi_url = 'https://pagespeed.web.dev/analysis?url=' . rawurlencode( home_url( '/wordpress-website-erstellen-lassen/' ) );
$eur = static function ( $value ) { return str_replace( ' €', "\u{00A0}€", hu_format_eur( $value ) ); };
get_header();
?>
<div id="website-content" class="doku anfrage-website" data-track-page="website_offer" data-website-product data-base="<?php echo esc_attr( (string) HU_FREELANCER_WEBSITE_MIN ); ?>" data-page-price="<?php echo esc_attr( (string) HU_FREELANCER_WEBSITE_EXTRA_PAGE ); ?>" data-tracking-price="<?php echo esc_attr( (string) hu_tracking_price( 'measurement', 'setup', 'value' ) ); ?>" data-max-pages="<?php echo esc_attr( (string) HU_WEBSITE_CALCULATOR_MAX ); ?>">
  <nav class="wrap krumen" aria-label="Brotkrumen"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Startseite</a> / WordPress-Website erstellen lassen</nav>

  <section class="hero" id="hero">
    <div class="wrap">
      <div>
        <div class="produkt"><span class="marke-produkt">Die Anfrage-Website</span><span class="mono">WordPress · Neubau oder Relaunch</span></div>
        <h1>WordPress-Website erstellen lassen. Gebaut für Anfragen, zum Festpreis.</h1>
        <p class="lead"><strong>Sie bekommen eine Website, die zu einer Anfrage führt.</strong> Das Angebot steht im ersten Bildschirm, die Formular-Mails kommen an, und Domain, Hosting und Code laufen von Anfang an auf Ihren Namen.</p>
        <div class="aktion">
          <a class="btn" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="hero" data-track-action="cta_website_offer_hero_project" data-track-category="lead_gen" data-track-section="website_offer_hero">Website-Projekt anfragen <span aria-hidden="true">→</span></a>
          <a class="leise" href="#unterschied" data-track-action="website_offer_to_unterschied" data-track-category="navigation">Was anders ist</a>
        </div>
        <p class="mikro">Antwort <?php echo esc_html( hu_response_promise( 'window' ) ); ?>, mit Rückfragen oder einem Festpreis-Angebot.</p>
      </div>
      <aside class="formel" aria-label="Preis">
        <p class="mono">Festpreis ab</p>
        <p class="betrag"><?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_MIN ) ); ?><small>netto</small></p>
        <p class="regel">Website mit einer Seite. Jede weitere Seite <?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_EXTRA_PAGE ) ); ?>. Bauzeit ab zwei Wochen. <span id="hero-bauzeit">Ihr Umfang: 3 Wochen.</span></p>
        <ul class="beispiele">
          <li><span>3 Seiten</span><em>3 Wochen</em><b><?php echo esc_html( $eur( $quote['price'] ) ); ?></b></li>
          <li><span>5 Seiten</span><em>3 Wochen</em><b><?php echo esc_html( $eur( hu_website_quote( 5 )['price'] ) ); ?></b></li>
        </ul>
        <p class="mikro">Immer dabei: technisches SEO, Texte einpflegen, Impressum und Datenschutz, Übergabe auf Ihren Namen.</p>
        <a class="zum leise" href="#angebot" data-track-action="website_offer_to_angebot" data-track-category="navigation">Endpreis selbst ausrechnen ↓</a>
      </aside>
    </div>
  </section>

  <section class="dunkel tafel" id="unterschied" aria-labelledby="h-unterschied">
    <div class="wrap raster">
      <p class="nr"><b>01</b><span>Unterschied</span></p>
      <div class="haupt breit">
        <h2 id="h-unterschied">Dieselbe Website, zweimal gebaut. Sieben Stellen entscheiden, ob Anfragen kommen.</h2>
        <p class="lead">Hier sehen Sie, wie kleine Websites oft verkauft werden. Schalten Sie um und vergleichen Sie Stelle für Stelle.</p>
        <div class="schalter" data-website-controls hidden role="group" aria-label="Ansicht wählen">
          <button type="button" id="m-klassisch" aria-pressed="true" data-modus="klassisch">Oft verkauft</button>
          <button type="button" id="m-anfragen" aria-pressed="false" data-modus="anfragen">Gebaut für Anfragen</button>
        </div>
        <div class="durch" id="durch" data-modus="klassisch">
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
            <li data-pin="3" tabindex="0"><span class="n">3</span><div><h3>Kontaktformular</h3><p class="k">Die Mail landet im Spam, und niemand merkt es.</p><p class="a">Versand über SPF und DKIM abgesichert, wenige Pflichtfelder, Danke-Seite mit nächstem Schritt.</p></div></li>
            <li data-pin="4" tabindex="0"><span class="n">4</span><div><h3>Google</h3><p class="k">Beim Relaunch laufen alte Links und Suchergebnisse ins Leere.</p><p class="a">Jede alte URL bekommt eine Weiterleitung, jede Seite Title, Canonical und Schema.</p></div></li>
            <li data-pin="5" tabindex="0"><span class="n">5</span><div><h3>Domain und Konten</h3><p class="k">Laufen über das Konto der Agentur. Wechseln wird schwer.</p><p class="a">Laufen von Anfang an auf Ihren Namen. Sie können jederzeit wechseln.</p></div></li>
            <li data-pin="6" tabindex="0"><span class="n">6</span><div><h3>Nach dem Start</h3><p class="k">Wartungsvertrag als Pflicht, dazu Jahreslizenzen für Theme und Plugins.</p><p class="a">Kein Vertrag, keine Lizenzkosten. Weiterentwicklung nur, wenn Sie wollen.</p></div></li>
            <li data-pin="7" tabindex="0"><span class="n">7</span><div><h3>Cookie-Banner</h3><p class="k">Schriften und Karten von fremden Servern, deshalb ein Banner vor dem ersten Klick.</p><p class="a">Schriften lokal, keine Dienste, die eine Einwilligung brauchen. Ohne Tracking entfällt der Banner.</p></div></li>
          </ol>
        </div>
        <p class="fair"><strong>Was die übliche Website gut kann:</strong> Sie ist schnell fertig und sieht ordentlich aus. Als Visitenkarte reicht das. Soll sie Anfragen bringen, entscheiden die sieben Stellen.</p>
      </div>
    </div>
  </section>

  <section class="abschnitt" id="angebot" aria-labelledby="h-angebot">
    <div class="wrap raster">
      <p class="nr"><b>02</b><span>Angebot</span></p>
      <div class="haupt breit">
        <h2 id="h-angebot">Was Sie bekommen und was es kostet. Rechnen Sie selbst.</h2>
        <p class="lead">Das ist der Lieferumfang der Anfrage-Website, Position für Position. Stellen Sie Größe und Projektart ein, die Summe ist der Festpreis für diesen Umfang.</p>

        <div class="groessen" data-website-controls hidden role="group" aria-label="Größe wählen">
          <button type="button" data-seiten="1" aria-pressed="false">Eine Seite <b><?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_MIN ) ); ?></b></button>
          <button type="button" data-seiten="3" aria-pressed="true">Drei Seiten <b><?php echo esc_html( $eur( $quote['price'] ) ); ?></b></button>
          <button type="button" data-seiten="5" aria-pressed="false">Fünf Seiten <b><?php echo esc_html( $eur( hu_website_quote( 5 )['price'] ) ); ?></b></button>
        </div>
        <div class="regler" data-website-controls hidden>
          <div class="art" role="group" aria-label="Projektart">
            <button type="button" data-art="neubau" aria-pressed="true">Neubau</button>
            <button type="button" data-art="relaunch" aria-pressed="false">Relaunch</button>
          </div>
        </div>

        <noscript><p class="mikro">Beispiel: drei Seiten, Neubau, ohne Tracking. Endpreis = Grundpreis + weitere Seiten × Seitenpreis + optionales Tracking. Schreiben Sie Ihren gewünschten Umfang in die Anfrage.</p></noscript>
        <div class="dokument">
          <div class="dok-kopf"><strong>Angebot vorab</strong><span class="mono">Anfrage-Website · Festpreis · netto</span></div>
          <ol class="posten">
            <li class="pos"><span class="p-nr">01</span><h3>Website mit erster Seite<small>Struktur, Umsetzung, technisches SEO, Ihre Inhalte eingepflegt</small></h3><span class="menge">1</span><span class="betrag-zeile"><?php echo esc_html( $eur( HU_FREELANCER_WEBSITE_MIN ) ); ?></span></li>
            <li class="pos"><span class="p-nr">02</span><h3>Weitere Seiten<small>gleicher Aufbau, gleiche Sorgfalt</small></h3>
              <span class="menge"><span class="stepper" data-website-controls hidden><button type="button" id="minus" aria-label="Eine Seite weniger">−</button><output id="weitere" aria-live="polite">2</output><button type="button" id="plus" aria-label="Eine Seite mehr">+</button></span></span>
              <span class="betrag-zeile" id="betrag-weitere"><?php echo esc_html( $eur( 2 * HU_FREELANCER_WEBSITE_EXTRA_PAGE ) ); ?></span></li>
            <li class="pos"><span class="p-nr">03</span><h3>Conversion-Tracking, optional<small>GA4, Tag Manager, Consent Mode und Google Ads, mit Abnahmeprotokoll. Mit Tracking braucht die Website eine Einwilligung der Besucher.</small></h3>
              <span class="menge"><label class="wahl" data-website-controls hidden for="tracking"><input type="checkbox" id="tracking"> dazu</label></span>
              <span class="betrag-zeile inkl" id="betrag-tracking"><?php echo esc_html( $eur( (int) hu_tracking_price( 'measurement', 'setup', 'value' ) ) ); ?></span></li>
          </ol>
          <div class="inklusive">
            <div class="inklusive-kopf"><h3>In jedem Projekt enthalten</h3><span class="mono"><span id="anzahl">34</span> Punkte ohne Aufpreis</span></div>
            <div class="gruppen">
              <div class="gruppe"><h4>Inhalte</h4><ul>
                <li>Gliederung jeder Seite: Angebot, Preis, Beleg</li>
                <li>Ihre Texte und Bilder eingepflegt</li>
                <li>Feinschliff der Texte</li>
                <li>Bilder zugeschnitten und als WebP verkleinert</li>
              </ul></div>
              <div class="gruppe"><h4>Technisches SEO und On-Page</h4><ul>
                <li>Pro Seite ein Suchbegriff, auf den Title, Überschrift und Gliederung ausgerichtet sind</li>
                <li>Title und Meta-Beschreibung je Seite</li>
                <li>Saubere URLs und eine logische Überschriften-Struktur</li>
                <li>Interne Links zwischen den Seiten</li>
                <li>Alternativtexte für alle Bilder</li>
                <li>Canonical, XML-Sitemap und robots.txt</li>
                <li>Strukturierte Daten: Unternehmen, Brotkrumen, FAQ</li>
                <li>Core Web Vitals: feste Bildmaße, kein Springen beim Laden</li>
                <li>Google Search Console eingerichtet, Sie als Inhaber</li>
                <li>Vorschau für WhatsApp, LinkedIn und Co.</li>
              </ul></div>
              <div class="gruppe"><h4>Technik</h4><ul>
                <li>WordPress auf Ihrem Hosting, auch dem bestehenden</li>
                <li>Keine Lizenzkosten für Themes oder Plugins</li>
                <li>Domain verbunden, HTTPS</li>
                <li>Für Handy und Desktop gebaut</li>
                <li>Grundlagen der Barrierefreiheit: Kontraste, Tastatur, Alternativtexte</li>
                <li>Favicon</li>
                <li>Backup vor dem Livegang</li>
              </ul></div>
              <div class="gruppe"><h4>Anfrage</h4><ul>
                <li>Kontaktformular, Versand über SPF und DKIM abgesichert</li>
                <li>Spam-Schutz ohne Captcha</li>
                <li>Danke-Seite mit nächstem Schritt</li>
                <li>404-Seite, die zurück zum Angebot führt</li>
              </ul></div>
              <div class="gruppe"><h4>Recht</h4><ul>
                <li>Impressum und Datenschutz angelegt, Ihre Rechtstexte eingebunden</li>
                <li>Cookiefrei gebaut, Schriften lokal</li>
              </ul></div>
              <div class="gruppe"><h4>Abnahme und Übergabe</h4><ul>
                <li>Testumgebung, live erst nach Ihrer Freigabe</li>
                <li>Zwei Korrekturrunden</li>
                <li>Abnahmeprotokoll: Testanfrage, Ladezeit, Weiterleitungen geprüft und dokumentiert</li>
                <li id="pos-weiterleitung" class="aus">Weiterleitungsplan für jede alte URL</li>
                <li>Domain, Hosting, Konten und Code auf Ihren Namen</li>
                <li>Dokumentation und Einweisung in den Editor</li>
                <li>30 Tage nach dem Livegang: Fehler behebe ich kostenlos</li>
              </ul></div>
            </div>
          </div>
          <div class="summe">
            <div>
              <p class="mono">Festpreis für diesen Umfang</p>
              <p class="gesamt" aria-live="polite"><span id="gesamt"><?php echo esc_html( $eur( $quote['price'] ) ); ?></span><small>netto</small></p>
              <p class="rechnung" id="rechnung"><?php echo esc_html( hu_freelancer_website_price() . ' + 2 × ' . hu_freelancer_website_extra_page_price() ); ?></p>
              <p class="bauzeit">Bauzeit <b id="bauzeit">3 Wochen</b> ab vollständigen Inhalten · <a href="#zeit" data-track-action="website_offer_to_zeit" data-track-category="navigation">wie gerechnet</a></p>
            </div>
            <a class="btn" id="cta-angebot" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="angebot" data-track-action="cta_website_offer_scope_project" data-track-category="lead_gen" data-track-section="website_offer_angebot">Mit diesem Umfang anfragen <span aria-hidden="true">→</span></a>
          </div>
        </div>
        <div class="klartext">
          <div><h3>Was als Seite zählt</h3><p>Jede Unterseite mit eigenem Inhalt, etwa Startseite, Leistung oder Über uns. Impressum, Datenschutz, Danke- und 404-Seite zählen nicht mit.</p></div>
          <div><h3>Was nicht enthalten ist</h3><p>Texte schreiben, Keyword-Recherche, Fotos und Logo, Rechtsberatung, Shop, Schnittstellen, mehrere Sprachen. Mehr als zehn Seiten kalkuliere ich einzeln.</p></div>
          <div><h3>Was danach laufend kostet</h3><p>Domain und Hosting zahlen Sie direkt beim Anbieter, auf Ihren Namen. Keine Lizenzen, kein Wartungsvertrag. Updates übernehmen Sie selbst oder über ein Kontingent.</p></div>
        </div>

        <div class="nach-dok">
          <p class="stempel"><span class="mono">Grundsatz</span>Nach der Übergabe können Sie mich ersetzen. Das ist Absicht.</p>
          <div class="einwand">
            <h3>Und wenn Sie als Einzelperson ausfallen?</h3>
            <p>Dann sage ich es Ihnen am selben Tag. Weil alles auf Ihren Namen läuft, kann jemand anderes ohne Umweg übernehmen.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="dunkel tafel" id="beleg" aria-labelledby="h-beleg">
    <div class="wrap raster">
      <p class="nr"><b>03</b><span>Beleg</span></p>
      <div class="haupt">
        <h2 id="h-beleg">Prüfen Sie die Arbeit, bevor Sie anfragen.</h2>
        <p class="lead">Eine Website, die ich gebaut habe, ist die, auf der Sie gerade sind. Dazu zwei Projekte und ein Fall mit Zahlen.</p>
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
      <aside class="rand"><span class="mono">Übertragbar?</span>Die Zahl aus dem Fall hängt an Markt und Angebot. Übertragbar ist die Bauweise.</aside>
    </div>
  </section>

  <section class="abschnitt" id="zeit" aria-labelledby="h-zeit">
    <div class="wrap raster">
      <p class="nr"><b>04</b><span>Zeit</span></p>
      <div class="haupt breit">
        <h2 id="h-zeit">Wie lange es dauert, und wovon das abhängt.</h2>
        <p class="lead">Die Bauzeit läuft ab dem Tag, an dem Texte, Bilder und Rechtstexte vollständig bei mir sind. Bis dahin bestimmen Sie das Tempo.</p>
        <div class="zeit">
          <div class="zeit-kopf"><span class="mono" id="zeit-umfang">Ihr Umfang: 3 Seiten · Neubau</span><strong id="zeit-gesamt">3 Wochen Bauzeit</strong></div>
          <div class="zeit-reihe">
            <div class="vorlauf"><span class="mono">Vorlauf · bei Ihnen</span><p>Angebot freigeben, Gliederung erhalten, Inhalte liefern.</p></div>
            <div>
              <p class="uhr">Ab hier läuft die Bauzeit</p>
              <div class="bahn" id="bahn">
                <div class="phase" style="--w:2"><span class="mono">Woche 1–2</span><b>Umsetzung auf der Testumgebung</b></div>
                <div class="phase ende" style="--w:1"><span class="mono">Woche 3</span><b>Korrekturen, Abnahme, Livegang</b></div>
              </div>
            </div>
          </div>
        </div>
        <ol class="bedingungen">
          <li><h3>Inhalte vollständig</h3><p>Die Bauzeit startet, wenn alles da ist. Kommen die Inhalte in Teilen, zählt der Tag des letzten Teils.</p></li>
          <li><h3>Rückmeldung in fünf Werktagen</h3><p>Je Korrekturrunde. Brauchen Sie länger, verschiebt sich der Livegang um dieselbe Zeit.</p></li>
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
        <p>Schreiben Sie kurz, welche Seiten Sie brauchen und bis wann die Website stehen soll. Sie bekommen Rückfragen oder ein Festpreis-Angebot.</p>
        <div class="aktion" style="margin-top:var(--s3)">
          <a class="btn" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="abschluss" data-track-action="cta_website_offer_close_project" data-track-category="lead_gen" data-track-section="website_offer_abschluss">Website-Projekt anfragen <span aria-hidden="true">→</span></a>
        </div>
        <p class="mikro">Persönlich, <?php echo esc_html( hu_response_promise( 'window' ) ); ?>.</p>
        <p class="direkt mikro">Lieber direkt: <span><?php echo esc_html( hu_get_contact_email() ); ?></span> · <span><?php echo esc_html( hu_get_contact_phone() ); ?></span></p>
      </div>
    </div>
  </section>


<div class="leiste-unten tafel" hidden inert id="leiste" aria-hidden="true">
  <p><span class="kurz-weg">Ihr Umfang: </span><b id="leiste-text"><?php echo esc_html( '3 Seiten · ' . hu_format_eur( $quote['price'] ) . ' netto · 3 Wochen' ); ?></b></p>
  <a class="btn" id="cta-leiste" tabindex="-1" href="<?php echo esc_url( $contact_url ); ?>" data-website-cta="leiste" data-track-action="cta_website_offer_sticky_project" data-track-category="lead_gen" data-track-section="website_offer_leiste">Anfragen <span aria-hidden="true">→</span></a>
</div>


</div>
<?php get_footer(); ?>
