# Live Status

Aktuelles Verhalten der Website, nach Bereichen. Stand: Repository `main`
einschließlich der Änderungen vom 2026-09-22 (gilt nach Merge und Deploy).

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
  `/whitelabel-retainer/` (seit 2026-09-22 gleichrangig), Energie-Intent →
  `/solar-waermepumpen-leadgenerierung/#marktcheck`. Der Marktcheck ist kein
  globaler CTA.
- Kopf: `.leiste` aus `system.css` plus `leiste.js`. Reihenfolge Leistungen
  (`/#angebote`), White-Label, Tracking (`/server-side-tracking-b2b/`),
  Solar & Wärmepumpe, Ergebnisse, Über Haşim, CTA „Projekt anfragen“. Quelle ist
  `hu_get_primary_navigation_contract()` in `inc/commercial-routing.php`; das
  gespeicherte WordPress-Menü wird daraus normalisiert (`inc/menu-setup.php`,
  `inc/header.php`). `/whitelabel-retainer/` hat eine eigene Seitennavigation.
- Fuß: `template-parts/site-footer.php`. Auf `/`, `/kontakt/` und der
  Energie-Money-Page entfällt die Wegewahl am Abschluss.

## Routen

- **`/`** (`front-page.php`): Money Page für direkte WordPress-Projekte.
  Title „WordPress Freelancer Hannover | Haşim Üner“, Query-Owner für
  `wordpress freelancer` und `wordpress freelancer hannover`. Unter dem Hero
  eine Weiche (`#wege`) zu White-Label und zum Anfragesystem; drei Leistungen
  mit den Ankern `#angebot-website`, `#angebot-funnel`, `#angebot-tracking`;
  Nachweis, Arbeitsweise, Fragen (template-eigenes FAQPage-Schema), Anfrage.
  `/wordpress-freelancer-hannover/` leitet per 301 hierher. Solange der
  Versuch Ersteinschätzung läuft (Schalter `HU_EXPERIMENT_ERSTEINSCHAETZUNG`,
  `docs/experimente/ersteinschaetzung.md`), führen Hero und Abschluss primär
  auf `/kontakt/?focus=ersteinschaetzung`; die Projektanfrage steht sekundär
  daneben. Seit 2026-09-23 hat der Hero genau diese zwei Buttons; der Sprung
  zu den Leistungen hängt am Preisanker der Trust-Zeile (`home_hero_to_offers`).
  Die Hero-Grafik ist eine Stromlinien-Tafel: Besuche strömen von links ein,
  ein Teil endet im Stempel „Neu im CRM“ mit Beispielquelle, der Rest zieht
  vorbei. Die Linien sind statisches SVG (`assets/img/home-feld.svg`, erzeugt
  von `scripts/build-home-feld-svg.py`); `home-feld.js` legt bewegte
  Funken darüber, mit Schalter zum Anhalten. Ohne JavaScript und bei
  reduzierter Bewegung steht die Tafel ruhig und vollständig. Direkt darunter steht
  eine Belegzeile (`#einordnung`) mit Fall, öffentlichen Arbeiten und offenem
  Code, jeweils als Sprung zum Nachweis in Abschnitt 02.
- **`/kontakt/`** (`page-kontakt.php`): Anfrage-Intake, siehe „Anfragewege“.
- **`/whitelabel-retainer/`** (`page-whitelabel-retainer.php`): Agentur-Einstieg
  mit eigenem Kopf, Fuß und Skip-Link. Primärziel ist das Aufgabenformular; der
  Belegblock nennt, was Agenturen vorab prüfen können: öffentliche Projekte, den
  offenen Code dieser Website und den bezahlten Test-Sprint. Preise aus
  `hu_whitelabel_pricing_canon()`; Service- und FAQPage-Schema.
- **`/performance-marketing/`** (`page-performance.php`, Gutachten-Layout):
  Performance Marketing für B2B in der Reihenfolge Messung → Zielseite →
  Budget, mit eigenem Weg für Performance-Agenturen zu White-Label. Titel,
  Beschreibung und FAQ kommen aus `nexus_get_wgos_cluster_page_data()`,
  Service-Schema aus `inc/org-schema.php`. Query-Owner für
  `performance marketing b2b`.
- **`/ga4-tracking-setup/`** (`page-ga4.php`): Conversion-Tracking-Setup mit
  denselben Stufennamen wie die Server-Side-Seite; CTAs auf
  `/kontakt/?type=project&focus=tracking`. FAQ aus demselben Register.
- **`/server-side-tracking-b2b/`** (`page-server-side-tracking-b2b.php`):
  Tracking-Money-Page mit eigenem Formular (`contact-request`, `type=project`,
  `focus=tracking`). Beide Tracking-Seiten gelten als ein Kontext
  (`hu_is_tracking_route_context()`).
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
- **Nachweise:** `/ergebnisse/` (`page-ergebnisse.php`) trennt öffentlich
  prüfbare WordPress-Arbeiten, technische Belege und den Solar-Fall.
  `/case-study-solar-leadgenerierung/` ist die Fallstudie; sie bleibt
  `noindex, follow`, bis die Freigabe vorliegt. Kennzahlen nur aus
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
  eigene Entscheidungs-Cockpits statt des Editor-Inhalts.
- **Weitere öffentliche Seiten:** `/glossar/`, `/impressum/`, `/datenschutz/`
  (Kontaktdaten aus dem Messaging-Canon).
- **Intern:** `page-wgos.php` ist ein geschütztes Kunden-Dashboard
  (`noindex, nofollow`); `wgos_asset`-Seiten unter `/wgos-assets/<slug>/` sind
  `noindex, follow`; das Kundenportal (`template-portal.php`) zeigt nur
  hinterlegte Daten; `/startseite-wow/` ist eine `noindex`-Testroute.

## Anfragewege und CRM

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
- Auswertung: Koko Analytics (Plugin, admin-owned) und das SEO-Cockpit im
  Admin (Search Console per OAuth, Linkgraph, Lead-Attribution aus dem CRM).

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
  `/solar-leads-kaufen-alternative/`, `/case-studies/` und
  `/case-studies-e-commerce/` → `/ergebnisse/`, `/wordpress-agentur/` →
  `/wordpress-agentur-hannover/`, `/category/owned-leads/` →
  `/eigene-leadgenerierung-vs-portale/`.
- `410 Gone` (`nexus_get_retired_gone_paths()`): `/wordpress-seo-hannover/`,
  `/core-web-vitals/`, `/conversion-rate-optimization/`,
  `/wordpress-wartung-hannover/`, `/seo/`, `/kostenlose-tools/`, `/tools/`,
  `/website-performance-analyse/`, `/roi-rechner/`, `/audit-linkedin/`,
  `/shopify-wartungsvertrag/`.
- Alte WGOS-Pfade bleiben `noindex` bzw. geschützt, solange sie intern als
  Dashboard- oder Asset-Pfade existieren.

## Offen und manuell

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
