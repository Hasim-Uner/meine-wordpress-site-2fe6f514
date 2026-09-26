# Live Status

Aktuelles Verhalten der Website, nach Bereichen. Stand: Repository `main`
einschließlich der Änderungen vom 2026-09-25 (gilt nach Merge und Deploy).

Diese Datei beschreibt den Ist-Zustand, keinen Verlauf. Die frühere,
chronologische Fassung mit Begründungen und Prüfprotokollen bis 2026-09-22
liegt in der Git-History: `git show ad69c5c:docs/architecture/LIVE_STATUS.md`.
Neue Einträge ersetzen den betroffenen Absatz, statt oben einen datierten
Abschnitt anzuhängen; der Verlauf gehört in Commit-Nachrichten.

## Grundlage und Grenzen

- Basis: Repo-Inhalt, Deploy-Workflows und die Template- und Funnel-Logik im
  Theme `blocksy-child/`.
- Nicht aus dem Repo verifizierbar: WordPress-Admin (Menüs, Editor-Inhalte,
  Plugins), Brevo-, Cal.com- und Koko-Setups, Server-Cron und die tatsächliche
  Mailzustellung.
- Deploy: `.github/workflows/ci.yml` prüft; `.github/workflows/deploy.yml`
  deployt nach grünem CI-Lauf auf `main` oder manuell. Die statische `llms.txt`
  wird zusätzlich ins Webroot kopiert.
- Live liegt ein Cache vor PHP (nginx, Varnish). Formulare arbeiten deshalb
  ohne Nonce im HTML; öffentliche REST-Endpunkte sind zustandslos abgesichert.

## Positionierung und Navigation

- Öffentliche Rolle: WordPress Freelancer aus der Region Hannover; verbundene
  Kompetenz technisches SEO, Tracking und Conversion. Maßgeblich sind
  `docs/standards/BRAND_AND_COPY.md` und `docs/architecture/CONVERSION_ROUTING.md`.
- Drei Wege: direkte Projekte → `/kontakt/?type=project`, Agenturen →
  `/whitelabel-retainer/` (Platz 3 in der Navigation, direkt hinter den
  Leistungen; auf der Startseite seit
  2026-09-24 ein leiser Nebenausgang unter den Leistungen), Energie-Intent →
  `/solar-waermepumpen-leadgenerierung/#marktcheck`. Der Marktcheck ist kein
  globaler CTA.
- Kopf: `.leiste` aus `system.css` plus `leiste.js`. Reihenfolge seit
  2026-09-25 Leistungen (`/#angebote`), Tracking (`/ga4-tracking-setup/`,
  Route `tracking_setup`), White-Label, Solar & Wärmepumpe, Ergebnisse
  (`/#arbeiten`, Ziel aus `hu_get_results_nav_url()`), Über Haşim, CTA
  „Projekt anfragen“. Erst die Leistungen, dann die Wege für Agenturen und
  Energiebetriebe, dann Belege und Person. Quelle ist
  `hu_get_site_header_navigation_contract()` in `inc/commercial-routing.php`;
  Klappblatt, 404-Seite, gespeichertes WordPress-Menü (`inc/menu-setup.php`,
  nur Backend-Zustand, nirgends gerendert) und SEO Cockpit lesen denselben
  Contract. `aria-current="page"` nur auf dem Link, der die Seite ist;
  `aria-current="true"` für den Bereich (Server-Side-Seite unter Tracking,
  Fallstudie unter Ergebnisse). Unter 1081 px bleibt der CTA in der Zeile
  neben „Menü“ (unter 480 px als „Anfragen“, unter 340 px nur im Blatt); auf
  Seiten mit Sticky-CTA-Leiste übernimmt unter 761 px die Leiste, auf der
  Startseite bleibt er schmal im Blatt, weil der Hero die Anfrage trägt. Das
  Klappblatt scrollt selbst, wenn es höher als der Viewport ist (Handy quer).
  `/whitelabel-retainer/` hat eine eigene Seitennavigation aus derselben
  `.leiste`, ohne Klappblatt.
  Auf der Startseite (nur dort, über `startseite-strecke.css/.js`) ist der
  Header-Button ein Outline-Button, weil dort „Kostenlose Ersteinschätzung“
  der primäre Button ist; „Leistungen“ ist nur aktiv, solange `#angebote` im
  Blick ist (`aria-current="location"`), ohne JavaScript neutral; die
  Wortmarke trägt `aria-current="page"`. Der Menüknopf hat kein eigenes
  `aria-label`, der sichtbare Text „Menü“/„Schließen“ ist sein Name.
- Fuß: `template-parts/site-footer.php` aus
  `hu_get_site_footer_navigation_contract()`. Vier Wege in Kopf-Reihenfolge
  (Website, Tracking, Agentur, Energie; der eigene Weg entfällt), Direktzeile,
  Verzeichnis in vier Gruppen: Leistungen (Server-Side Tracking, Performance
  Marketing, WordPress Agentur Hannover), Belege & Person (Solar-Fallstudie,
  Über Haşim), Wissen (Blog, Glossar), Rechtliches (Impressum, Datenschutz).
  Auf `/`, `/kontakt/` und der Energie-Money-Page entfällt die Wegewahl am
  Abschluss.
- 404: dieselben Wege wie der Kopf plus Startseite und Blog; kein Marktcheck.
- Prüfung: `scripts/tests/navigation-contract.php` (Contract, Ziele,
  aria-current, bekannte Routen) und `scripts/tests/navigation.spec.cjs`
  (Desktop, Mobil, Tastatur, ohne JavaScript) laufen in CI.

## Routen

- **`/`** (`front-page.php`): Money Page für direkte WordPress-Projekte.
  Title „WordPress Freelancer für Unternehmen | Haşim Üner, Hannover“,
  Query-Owner für `wordpress freelancer` und `wordpress freelancer hannover`.
  Seit 2026-09-24 als Strecke gebaut (`assets/css/startseite-strecke.css`,
  `assets/js/startseite-strecke.js`): Eine Messlinie läuft in der Randspalte
  vom Hero (Marke „Klick“) bis zum Anfrageblock (Marke „Anfrage“) und füllt
  sich beim Lesen; ohne JavaScript und bei reduzierter Bewegung steht sie
  statisch. Acht Abschnitte: Hero (`#klick`) mit H1 ohne Ortsnamen, Byline
  mit Portrait, zwei Buttons, Messprotokoll und drei Belegzeilen (Fall,
  Referenzen, seit 2026-09-25 der Websitepreis aus
  `hu_freelancer_website_price()` mit Hook `home_proof_strip_price`; H1, Satz,
  Buttons und Protokoll passen bei 1280 × 800 in den ersten Bildschirm); Prüfstand (`#pruefstand`, die
  einzige dunkle Tafel, Links auf Code, CI und PageSpeed, keine Scores);
  sechs Stationen einer Anfrage (`#strecke`, Liste mit `#angebot-funnel`);
  drei Leistungen mit Kanonpreisen (`#angebote`, Anker `#angebot-website`,
  `#angebot-tracking`, `#angebot-weiterentwicklung`) und darunter die leisen
  Nebenausgänge zu White-Label und Solar/Wärmepumpe; Arbeiten (`#arbeiten`,
  `#referenzen`); Übergabe (`#uebergabe`); Fragen (`#fragen`,
  template-eigenes FAQPage-Schema); Anfrage (`#anfrage`, `#kontakt`).
  Das Messprotokoll zeigt Ladezeit (LCP bzw. Navigation Timing), gesehene
  Abschnitte, Scrolltiefe und den Klick auf einen Anfrage-Button. Es sendet
  und speichert nichts; die Sperrliste (`strecke-js-privat`) bricht den
  Build, sobald das Skript einen Netzwerk- oder Speicheraufruf enthält.
  `/wordpress-freelancer-hannover/` leitet per 301 hierher. Solange der
  Versuch Ersteinschätzung läuft (Schalter `HU_EXPERIMENT_ERSTEINSCHAETZUNG`,
  `docs/experimente/ersteinschaetzung.md`), führen Hero und Abschluss primär
  auf `/kontakt/?focus=ersteinschaetzung`; die Projektanfrage
  (`/kontakt/?type=project`) steht sekundär daneben.
- **`/kontakt/`** (`page-kontakt.php`): Anfrage-Intake, siehe „Anfragewege“.
  Bis 820 px folgt das Formular direkt auf Titel und Einleitung; Ablauf,
  andere Einstiege und E-Mail stehen darunter.
- **`/whitelabel-retainer/`** (`page-whitelabel-retainer.php`): Agentur-Einstieg
  und Landeseite der Akquise-Mails. Seit 2026-09-25 auf dem System der
  Startseite: lädt `startseite-strecke.css/.js` als Basis, `whitelabel.css`
  ist nur das Delta; `design-system.css`, Breadcrumb und
  `navigation-ecosystem.css/.js` laden hier nicht. Eigener Kopf aus der
  `.leiste` (Wortmarke, Anker Leistungen/Belege/Preise/Ablauf/Fragen,
  Button „Aufgabe beschreiben“; unter 768 px übernimmt der Button am unteren
  Rand), eigener Fuß, globale Sprungmarke auf `#main`. Sieben Abschnitte
  (Stand 2026-09-26): Hero (`#hero`) mit H1 „Ich baue die WordPress-Seite und
  die Messung dazu. Festpreis und Termin stehen, bevor ihr zusagt.“, Zweitweg
  „Vor der Zusage einschätzen lassen“ (`?case=angebotsphase`) und
  Abnahmeprotokoll als Muster; Leistungen (`#lieferfelder`, vier Aufgaben
  WordPress, Tracking, Anfragestrecke, Barrierefreiheit, je mit Anlass aus
  Sicht der Agentur und „Ihr nehmt ab“); Belege (`#proof`: Prüfstand
  `#pruefstand` als einzige dunkle Tafel, darunter die Referenzen aus
  `hu_public_reference_projects()`); Preise (`#einstieg`, Test-Sprint, drei
  Erstprojekte, Monatskontingent, Margenblock nur für das Server-Side-Setup
  mit dem Endkundenpreis aus `hu_tracking_price()`); Ablauf
  (`#zusammenarbeit`, fünf Stationen Aufgabe → Umfang → Umsetzung → Abnahme →
  Übergabe auf der Linie, Ausfall-Zusage); Fragen (`#faq`); Anfrage
  (`#naechster-schritt`, Formular `#aufgabe` als Ende der Linie; Wege
  Aufgabe, Angebotsphase, Vormerken und 30-Minuten-Termin). Die Punkte des
  Protokolls haken sich mit JavaScript ab, wenn die Leselinie ihre Station
  erreicht; ohne JavaScript stehen sie abgehakt. `?case=` kennt `aufgabe`,
  `angebotsphase` und `vormerken`; Formulartexte je Fall stehen im Template
  (`data-wl-case-texts`), Antwort und nächster Schritt der Bestätigung in
  `hu_whitelabel_request_cases()`. Preise aus
  `hu_whitelabel_pricing_canon()`; Service- und FAQPage-Schema (FAQ aus
  `nexus_get_whitelabel_faq_items()`, sieben Fragen). Sperrliste: `wl-*`-Regeln
  in `scripts/canon-forbidden-values.txt`. Eigenes og:image
  `assets/img/whitelabel-retainer-og.jpg` (1200 × 630, JPG) über
  `hu_get_route_social_image()`, erzeugt mit
  `scripts/build-og-images.py`.
- **`/performance-marketing/`** (`page-performance.php`, Gutachten-Layout):
  Performance Marketing für B2B in der Reihenfolge Messung → Zielseite →
  Budget, mit eigenem Weg für Performance-Agenturen zu White-Label. Titel,
  Beschreibung und FAQ kommen aus `nexus_get_wgos_cluster_page_data()`,
  Service-Schema aus `inc/org-schema.php`. Query-Owner für
  `performance marketing b2b`.
- **`/ga4-tracking-setup/`** (`page-ga4.php`, virtuelle Cluster-Route):
  Das Tracking-Angebot (Conversion-Tracking-Setup) mit denselben Stufennamen
  wie die Server-Side-Seite; CTAs auf `/kontakt/?type=project&focus=tracking`.
  FAQ aus demselben Register. Ziel des Kopfpunkts „Tracking“, des
  Tracking-Wegs im Fuß und der Station „Messung“ der Startseite (Route
  `tracking_setup`).
- **`/server-side-tracking-b2b/`** (`page-server-side-tracking-b2b.php`):
  Fachseite und Query-Owner für Server-Side Tracking mit eigenem Formular
  (`contact-request`, `type=project`, `focus=tracking`). Seitenweit verlinkt
  im Fuß als „Server-Side Tracking“ (Route `tracking_b2b`). Beide
  Tracking-Seiten gelten als ein Kontext (`hu_is_tracking_route_context()`).
- **`/wordpress-agentur-hannover/`** (`page-wordpress-agentur.php`):
  Entscheidungsseite „Agentur oder direkte Umsetzung“ mit den Ankern
  `#entscheidung`, `#technik`, `#zusammenarbeit`, `#belege`, `#hannover`, `#faq`,
  `#anfrage`. Hält die lokalen Agentur-, SEO- und Wartungs-Queries; die
  URL-Karte führt „Technisches SEO“, „Wartung“ und „Methode“ auf
  `#zusammenarbeit`.
- **`/solar-waermepumpen-leadgenerierung/`**
  (`page-solar-waermepumpen-leadgenerierung.php`, `anfragestrecke.css` unter
  `.strecke-doc`): Energie-Money-Page mit Marktcheck am Mount `#sol-quiz-mount`,
  Rechner (`anfragestrecke.js`) und Einstieg `#einstieg`. Geladen wird
  `solar-leadgenerierung-solara.js`; es lädt `solar-marketcheck-compact.js`
  nach, das die sichtbare Strecke rendert (vier Fit-Fragen plus Kontaktdaten in
  `HU_MARKETCHECK_VISIBLE_STEPS` Schritten) und an `audit-request` sendet. Ohne
  JavaScript verweist der Mount auf das Formular unter `/kontakt/`.
- **Energie-Cluster** (`.hu-intercept`, Pfade in
  `hu_get_solar_seo_subpage_paths()`): `/solar-leads-kaufen-alternative/`,
  `/waermepumpen-leads/`, `/b2b-solar-leads/`,
  `/eigene-leadgenerierung-vs-portale/`, `/lead-funnel-solar/`,
  `/kunden-gewinnen-solarteure/`, `/cost-per-lead-photovoltaik/`,
  `/qualifizierte-pv-anfragen/`, `/solar-leads-kosten-studie/`. Primärziel ist
  der Marktcheck; Byline, Breadcrumb- und Service-Schema über Helper in
  `inc/seo-meta.php`.
- **Nachweise:** `/case-study-solar-leadgenerierung/` ist die Fallstudie; sie
  bleibt `noindex, follow`, bis die Freigabe vorliegt. Eigenes og:image
  `assets/img/fallstudie-og.jpg` (1200 × 630, JPG, nur Kanonwerte), das auch
  das Beitragsbild im Schema ersetzt. Der Hub `/ergebnisse/` ist seit 2026-09-25
  stillgelegt: 301 auf die Fallstudie (`nexus_redirect_legacy_results_path()`),
  nicht in Sitemap, `llms.txt` und Fuß; die Route `results` und
  `nexus_get_results_url()` zeigen direkt auf die Fallstudie. Template
  `page-ergebnisse.php` bleibt im Repo, ist aber nicht mehr erreichbar. Kennzahlen nur aus
  `inc/canon/e3-proof-canon.php`, Referenzen aus
  `inc/canon/reference-canon.php`.
- **`/hasim-uener/`** (`page-hasim-uener.php`): Personenseite mit
  Person-/ProfilePage-Bezug; `/uber-mich/` leitet per 301 hierher.
- **Blog:** `/blog/` (`home.php`) und vier Dossiers unter `/category/`:
  `leadgenerierung`, `wordpress-performance`, `tracking`, `cro`. Alte
  Kategorien leiten per 301 auf das passende Dossier. Beiträge rendern über
  `template-parts/single-reader.php`; die Kontextbrücke richtet sich nach dem
  Dossier, ohne passende Kategorie führt sie zu den WordPress-Leistungen. Nur
  `leadgenerierung` führt in den Marktcheck. Aroundhome und Checkfox tragen
  eigene Entscheidungs-Cockpits statt des Editor-Inhalts. Der Fuß jedes
  Beitrags (Weiterarbeiten, Leserfeedback, Benachrichtigung, Teilen, Autor,
  Weiterlesen, Projekt-Abschluss) steht in einer Spalte auf `system.css`;
  Gestaltung im Nachsatz von `assets/css/article-reader-body.css`. Das
  Einblenden beim Scrollen versteckt nur unter `.hu-js`; ohne JavaScript
  bleiben alle Blöcke sichtbar, nur das Leserfeedback entfällt.
- **Artikel-Bausteine** (`inc/editorial-bausteine.php`): Shortcodes
  `[hu_kurz]`, `[hu_notiz]`, `[hu_abb]`, `[hu_fall]`, `[hu_pruefliste]`,
  `[hu_abschluss]`, `[hu_quellen]` und Klassen für reines HTML. CSS und JS
  laden nur auf Beiträgen, die einen Baustein enthalten; dort bekommt der
  Reader ab 1280 px eine Marginalspalte. Notiz, Abbildung, Fall,
  Abschluss-Tafel und Quellen tragen `data-hu-schema-skip` und landen nie im
  FAQ-Schema. Referenz:
  `agents/skills/pillar-cornerstone-writer/references/bausteine.md`.
- **`/website-relaunch/`** (Dossier `cro`): „Website-Relaunch: Wann die neue
  Website wirklich besser ist“. Kernaussage seit Fassung 2: Besser ist die
  neue Website, wenn an jedem Übergang mehr der richtigen Besucher
  weiterkommen, festgelegt als heutiger Wert und Ziel vor dem Relaunch; was
  heute trägt, darf dabei nicht verloren gehen. Erster Beitrag auf den
  Bausteinen, aus `assets/content/blog/website-relaunch.html` angelegt und
  mit Seed-Version `2026-09-24-website-relaunch-v2` einmal überschrieben
  (`inc/article-website-relaunch.php`), danach im Editor gepflegt. Zwei
  Abbildungen (Kette, schematisches Vorher/Nachher), FAQ-Schema mit sechs
  Fragen, Titelbild und og:image `website-relaunch-hero-v2.png` in voller
  Größe über das Feld „Open Graph Bild“. Kontextbrücke und Abschluss führen in
  die Projektanfrage mit Fokus `relaunch` bzw. in die Ersteinschätzung
  (`docs/architecture/CONVERSION_ROUTING.md`).
- **Glossar:** `/glossar/` und `/glossar/<begriff>/` verwenden `system.css`
  mit `glossary.css` als Layout-Ergänzung. Die Übersicht zeigt das zentrale
  Register alphabetisch, mit Definitionen sowie einer optionalen Suche und
  Themenfiltern (`glossary.js`); ohne JavaScript bleiben alle Links sichtbar.
  Einträge bestehen aus Kurzdefinition, Beispiel, Einordnung und typischen
  Fehlern. Registrierte Detailseiten rendern ihre Inhalte und verwandten Links
  beim Aufruf, damit frühere Sync-Fallbacks nicht im HTML stehen bleiben.
  Der Sync aktualisiert weiterhin Posts, Metadaten und seine Prüfmarker;
  Das Register enthält 60 Definitionen, jeweils zwölf in Anfragen, Ladezeit &
  Technik, Tracking, SEO und Conversion. 59 Detailseiten erlauben Indexierung;
  CTA-Hierarchie bleibt `noindex, follow`. Fünf Alias-Ziele bleiben erhalten:
  vier sind sichtbare Themenverweise, der lokale Agentur-Alias ist ausgeblendet.
  Damit enthält die Übersicht 64 Einträge. Definitionsfragen und kommerzielle
  Suchintentionen sind in `docs/seo/query-ownership.csv` getrennt;
  die Entscheidung erläutert `docs/seo/glossar-indexierung.md`.
  Neue Begriffe werden in `inc/glossary/glossary-registry-data.php` gepflegt;
  `search_terms` erweitert nur die Glossarsuche, `keywords_match` steuert
  weiterhin die bestehenden Blog-Verlinkungen. Abschlussziel: Projektanfrage.
- **Weitere öffentliche Seiten:** `/impressum/`, `/datenschutz/`
  (Kontaktdaten aus dem Messaging-Canon).
- **Intern:** `page-wgos.php` ist ein geschütztes Kunden-Dashboard
  (`noindex, nofollow`); `wgos_asset`-Seiten unter `/wgos-assets/<slug>/` sind
  `noindex, follow`; das Kundenportal (`template-portal.php`) zeigt nur
  hinterlegte Daten; `/startseite-wow/` ist eine `noindex`-Testroute.

- **Seitenanlage:** Theme-eigene Seiten stehen in `nexus_get_provisioned_pages()`
  (`inc/helpers.php`); Kontakt, Glossar, WGOS-Hub und Cluster-Seiten haben eigene
  Funktionen im jeweiligen Modul. Seit 2026-09-26 laufen alle nur noch einmal je
  Deploy (`.nexus-deploy-sha`, Option `nexus_route_pages_stamp`) statt bei jedem
  ungecachten Aufruf. Eine im Editor gelöschte Seite kommt mit dem nächsten Deploy
  zurück, solange sie in der Liste steht. Test: `npm run test:provisioning`.

## Anfragewege und CRM

- Repo-Korrektur vom 2026-09-25 (Live-Nachweis erst nach Deployment): Kontakt,
  Ersteinschätzung, White-Label, Marktcheck und Blog-Abo bestätigen im Browser
  Erfolg nur bei HTTP 2xx und einem JSON-Objekt mit `ok === true`. Ungültige
  Antworten erhalten die Eingaben. `NexusCore.submitJson()` begrenzt einen
  Versuch einschließlich Antwortkörper auf 30 Sekunden; ein Timeout bedeutet
  einen unbestätigten Eingang, keine nachgewiesene Ablehnung. Späte Antworten
  ändern die Oberfläche nicht mehr. Während des Versuchs sind Eingaben und
  Formularnavigation gesperrt; anschließend wieder bedienbar. Keine automatische
  Wiederholung und keine Zusage serverseitiger Einmalverarbeitung.
  Browser- und isolierte Endpoint-Prüfungen laufen in CI; Umfang und Grenzen:
  `scripts/tests/README.md`. Payload, Consent, Quellen, Segmente, Honeypot,
  Rate-Limits und der Versuch Ersteinschätzung bleiben unverändert.
- Öffentliche REST-Endpunkte unter `nexus/v1`, jeweils mit Honeypot und
  IP-Rate-Limit. Die IP liefert `nexus_get_review_request_ip()`: `REMOTE_ADDR`,
  hinter einem internen Proxy die rechteste öffentliche Adresse aus
  `X-Forwarded-For`.
  - `contact-request` (`inc/contact-page.php`): `/kontakt/` und das
    Server-Side-Formular. Bei `type=project` erscheint zuerst die Themenwahl
    (Vorhaben), der Agentur-Hinweis ist immer sichtbar, Budget ist optional.
    Anfragetyp `ersteinschaetzung` (Versuch): Website-URL Pflicht, Nachricht
    optional, Betreff-Präfix aus dem Kanon; der Endpoint nimmt ihn auch bei
    ausgeschaltetem Schalter an.
  - `whitelabel-request` (`inc/whitelabel-request.php`): legt einen
    CRM-Kontakt mit Quelle und Segment `whitelabel_request` und eine
    Sales-Chance an.
  - `audit-request` (`inc/review-crm.php`): Marktcheck, CPT
    `nexus_review_request`, Contract `2026-05-26.audit-request.v1`; die
    Qualifizierung (`qualified` oder `nurture`) setzt die Stufe der Sales-Chance.
  - Blog-Abo (`inc/blog-notify.php`) mit Double-Opt-in.
  - `analysis-submit` ist standardmäßig aus (`HU_FEATURE_READINESS_SUBMIT`),
    kein Formular sendet dorthin.
- Reihenfolge bei Kontakt und White-Label: CRM → interne Mail → Bestätigung.
  Ein Fehler 500 kommt nur, wenn CRM und interne Mail beide scheitern.
  Scheitert nur die Mail, erscheint ein Hinweis im Dashboard und in den
  CRM-Ansichten (Option `nexus_lead_notification_failures`, 7 Tage) und eine
  Aktivität im Verlauf.
- CRM: `nexus_contact` (Upsert pro E-Mail), `nexus_opportunity` mit den Stufen
  aus `nexus_get_crm_sales_stages()`, `nexus_crm_activity` als Verlauf. Jede
  Kontakt- und White-Label-Anfrage schreibt eine Aktivität `inbound_inquiry`
  mit Anfragetext und Herkunft, auch bei wiederholten Anfragen. Admin: Nexus
  CRM mit Vertrieb, Kommunikation, Projektanfragen, White-Label-Anfragen und
  Blog-Abos.
- Herkunft: cookiefrei aus der Browser-Session (`NexusCore`): Landing-, Einstiegs-,
  vorherige und Referrer-URL, Kampagnenquelle, Suchbegriff, `utm_medium`,
  `utm_campaign` und die optionale Frage, wie jemand aufmerksam wurde. Die
  Meta-Keys liefert `nexus_get_inquiry_attribution_meta()` in `inc/crm.php`.
- Antwortfrist-Wächter (`inc/crm-sales/watchdog.php`): stündlich am Event
  `nexus_crm_sales_followup_cron`. Sechs Werktagsstunden vor Ablauf der
  zugesagten Antwortzeit geht eine interne Sammelmail raus, wenn es seit Anlage
  weder eine ausgehende Mail über das CRM noch einen Stufenwechsel gab. Pro
  Sales-Chance einmal, nur für Chancen der letzten 14 Tage.
- Mail: `wp_mail` läuft über die Brevo-API (`pre_wp_mail`). Diagnose über
  `/wp-json/nexus/v1/mail-diagnostics` (Admin) und
  `/wp-json/nexus/v1/mail-diagnostics-public` (redigiert, ohne Fehlertext).
- Termine: Cal.com-Links (30 Minuten, White-Label-Fit-Gespräch) als Popup mit
  direktem Link als Rückfall.

## Messung

- Das Theme bindet weder GTM noch GA4 ein. `data-track-*`-Hooks bleiben an
  allen Conversion-Flächen; Skripte schreiben nur dann in `dataLayer`, wenn
  eines existiert.
- Auswertung: Koko Analytics (Plugin, admin-owned, Tracking-Methode
  `fingerprint`, cookielos) und das SEO-Cockpit im Admin (Search Console per
  OAuth, Linkgraph, Lead-Attribution aus dem CRM).
- Öffentliche Besuche setzen keine Cookies und schreiben nichts in
  `localStorage` oder `sessionStorage`; kein Cookie-Banner. Formulare lesen die
  Herkunft erst beim Absenden aus der Formularseite (Adresse, utm-Parameter,
  Referrer). Die Sitzungs-Herkunft über mehrere Seiten ist vorbereitet, aber
  nur mit Einwilligung aktiv (`window.huConsent`). Sticky-CTA und
  „Gefällt mir“ speichern nichts, das WordPress-Emoji-Skript ist im Frontend
  aus. Details: `docs/architecture/PRIVACY.md`.

## Kanon und Guards

- Fakten stehen nur in `blocksy-child/inc/canon/`: Antwortzeit und Kontakt
  (`messaging-canon.php`, `hu_response_promise()`, `HU_RESPONSE_HOURS`), Preise
  (`pricing-canon.php`), Fallzahlen (`e3-proof-canon.php`), Marktcheck
  (`diagnose-canon.php`), Marktzahlen und Referenzen. Getter werden ohne
  Literal-Fallback aufgerufen; diese Datei nennt bewusst keine Beträge.
- Die Antwortfrist in Mails (`nexus_compute_intake_response_deadline()`) nutzt
  dieselbe Konstante und überspringt Wochenenden.
- Guards: `scripts/canon-guard.sh` (repo-weit, Sperrliste in
  `scripts/canon-forbidden-values.txt`), `lint-canon-drift.sh`,
  `lint-e3-canon.sh`, `check-german-copy.sh`, `validate-architecture.sh`,
  beide Smoke-Contracts, CSS-Audits, `lint-entity-crawler-signals.php` und
  PHPStan mit Baseline. Der Theme-Build führt den Kanon-Guard erneut aus.

## SEO und Crawler

- Das Theme ist die einzige Quelle für Title, Meta, Canonical, Robots und
  Schema; es gibt kein SEO-Plugin. Resolver in `inc/seo-meta.php`, Schema in
  `inc/org-schema.php`.
- `/robots.txt` und `/llms.txt` sind Theme-Routen. Die `llms.txt` im
  Repo-Root ist ein Abzug der Route (`lint-entity-crawler-signals.php
  --write-llms`), kein zweiter Pflegeort.
- Sitemap: `/wp-sitemap.xml`; ausgeschlossene und `noindex, follow`-Slugs
  zentral in `inc/seo-meta.php` und `inc/helpers.php`.
- Query-Ownership: `docs/seo/query-ownership.csv`, geprüft mit
  `agents/skills/seo-agent/scripts/intent-gate.sh`.
- og:image: Routen mit Theme-Kachel (`hu_get_route_social_image()`) gehen vor
  ACF-Feld und Beitragsbild. Standard für jede Seite ohne eigenes Bild ist seit
  2026-09-25 `assets/img/standard-og.jpg` (1200 × 630, JPG), nicht mehr das
  Porträt im Hochformat; kommt das Porträt noch als Seitenbild an, ersetzt die
  Kachel es ebenfalls. Alle Theme-Kacheln außer der Solar-Kachel erzeugt
  `scripts/build-og-images.py`.

## Barrierefreiheit

- Genau ein `main`-Landmark: Blocksy öffnet `<main id="main">`, Vorlagen
  rendern darin nur `<div>`-Wrapper. Einige Energie-Vorlagen beginnen bei
  `#primary`; der Skip-Link folgt dem Ziel (`hu_get_skip_link_target_id()`).
- Ein Skip-Link pro Seite („Zum Hauptinhalt springen“ aus
  `inc/accessibility-navigation.php`); Blocksys englischer Link wird entfernt.

## Weiterleitungen und entfernte Routen

- 301 auf `/solar-waermepumpen-leadgenerierung/#marktcheck`: `/system-diagnose/`,
  `/anfrage-system-analyse/`, `/readiness-diagnose/`, `/anfrage/`,
  `/growth-audit/`, `/audit/`, `/customer-journey-audit/`, `/360-audit/`,
  `/wordpress-tech-audit/`.
- Weitere 301: `/stack-solar/` → Energie-Money-Page, `/stack-agentur/` →
  `/whitelabel-retainer/`, `/meta-ads/` → Beitrag `meta-ads-fuer-b2b`,
  `/solar-leads-kaufen-lohnt-sich/` und `/photovoltaik-leads-tco-rechnung/` →
  `/solar-leads-kaufen-alternative/`, `/ergebnisse/`, `/case-studies/` und
  `/case-studies-e-commerce/` → `/case-study-solar-leadgenerierung/` (ein
  Schritt, Query-String bleibt), `/wordpress-agentur/` →
  `/wordpress-agentur-hannover/`, `/category/owned-leads/` →
  `/eigene-leadgenerierung-vs-portale/`.
- 301 von `/<beitrag>/<autoren-slug>/` auf den Beitrag
  (`inc/post-permalink-author-redirect.php`, geprüft mit `npm run test:permalinks`).
  Greift nur, solange die Permalink-Struktur kein `%author%` enthält.
- `410 Gone` (`nexus_get_retired_gone_paths()`): `/wordpress-seo-hannover/`,
  `/core-web-vitals/`, `/conversion-rate-optimization/`,
  `/wordpress-wartung-hannover/`, `/seo/`, `/kostenlose-tools/`, `/tools/`,
  `/website-performance-analyse/`, `/roi-rechner/`, `/audit-linkedin/`,
  `/shopify-wartungsvertrag/`.
- Alte WGOS-Pfade bleiben `noindex` bzw. geschützt, solange sie intern als
  Dashboard- oder Asset-Pfade existieren.

## Offen und manuell

- Permalinks stehen seit 2026-09-26 wieder auf „Beitragsname“ (vorher etwa
  01.–25.09. mit `%author%`). Live am 2026-09-26 geprüft: alle 16 Beiträge der
  Sitemap antworten mit 200 und eigenem Canonical, `/<beitrag>/hasim/` leitet
  per 301 zurück, Sitemap neu eingereicht. Google führte am selben Tag für
  `/checkfox-solar-waermepumpe-einordnung/` noch die `/hasim/`-Variante als
  indexiert (letzter Crawl vor der Korrektur). Offen: In der Search Console
  für `/checkfox-solar-waermepumpe-einordnung/`, `/aroundhome-solar-einordnung/`,
  `/wattfox-solar-leads-einordnung/` und `/daa-photovoltaik-leads-einordnung/`
  „Indexierung beantragen“; der nächste GSC-Export zeigt, ob die Impressionen
  zurückkommen.
- Nach dem Merge je eine Testanfrage über `/kontakt/`, `/whitelabel-retainer/`
  und den Marktcheck: CRM-Eintrag, Sales-Chance, interne Mail und Bestätigung
  prüfen.
- Versuch Ersteinschätzung: nach dem Deploy eine Einsendung über
  `/kontakt/?focus=ersteinschaetzung` schicken (Betreff-Präfix bei
  kontakt@hasimuener.de prüfen) und Start- und Enddatum in
  `docs/experimente/ersteinschaetzung.md` eintragen.
- WP-Cron: Wegen des Seiten-Caches ist ein echter Server-Cron für `wp-cron.php`
  nötig, sonst laufen Follow-ups und Antwortfrist-Wächter nur bei
  Admin-Besuchen zuverlässig. Im Repo nicht prüfbar.
- WordPress-Admin: die Seiten zu `/stack-solar/` und `/stack-agentur/` löschen,
  falls noch vorhanden (sonst Sitemap-Einträge trotz 301).
- WordPress-Admin: die Seite `/ergebnisse/` auf Entwurf setzen, nicht löschen.
  Die 301 greift auch ohne diesen Schritt.
- Seitencache: Der Server-Cache (`x-cacheable: YES`) wird beim Deploy nicht
  geleert und lieferte `/whitelabel-retainer/` am 2026-09-25 rund 18 Stunden
  nach dem Deploy noch im alten Stand aus. Nach jedem Deploy mit sichtbarer
  Änderung im Hosting leeren.
- Mediathek: `Featured_CaseStudy_E3_1200x627.webp` (Anhang 14765) ist noch
  Beitragsbild der Fallstudie (Seite 12092) und öffentlich abrufbar; der
  Seitentitel im Editor lautet „Case Studies- e3-new-energy“ und steht im
  WebPage-Schema und in der REST-API. Über Löschen bzw. Umbenennen entscheidet
  Hasim.
- Der nicht gemessene Vorher-Wert der Abschlussquote steht noch auf
  `/case-study-solar-leadgenerierung/`, `/cost-per-lead-photovoltaik/`,
  `/solar-leads-kaufen-alternative/` und `/eigene-leadgenerierung-vs-portale/`
  (aus `hu_e3_metric()`); der Canon bietet dafür `display_hedged`.
- Partnerlinks aus `inc/affiliate-links.php` tragen `rel="sponsored"`, aber
  keinen sichtbaren Werbehinweis; zu prüfen.
- Browser- und Lighthouse-Abnahmen der jüngsten Seitenumbauten stehen aus,
  soweit sie in den Commits nicht als geprüft vermerkt sind.

## Nicht mehr Zielbild

- Growth Audit, System-Diagnose und kostenlose Tools als öffentliche
  Funnel-Stufen; der Marktcheck als sitewide CTA.
- Öffentliche Copy mit abgelösten Angebotsbegriffen (Sperrlisten in
  `docs/standards/BRAND_AND_COPY.md` und `scripts/lint-canon-drift.sh`) oder
  unbelegten Leistungszahlen; `Retainer` als kaufnaher Standardbegriff.
- Ein WordPress-Editor-Shell als Quelle für Funnel-Logik; lose Root-Ablagen für
  Playbooks und Entwürfe.
