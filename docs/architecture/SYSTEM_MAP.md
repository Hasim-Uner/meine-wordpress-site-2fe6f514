# System Map

Stand: 2026-09-22. Diese Karte beschreibt Systemgrenzen und Abhängigkeiten nach dem Repo-Inhalt; das Routen- und Laufzeitverhalten steht in `LIVE_STATUS.md`.

## Hauptsysteme

| System | Zweck | Repo-Orte | Externe Abhaengigkeiten | Status |
| --- | --- | --- | --- | --- |
| Website | deploybarer WordPress-Theme-Code | `blocksy-child/`, `.github/workflows/ci.yml`, `.github/workflows/deploy.yml`, `docs/architecture/DEPLOYMENT.md` | WordPress, Blocksy Parent Theme, SCF | live |
| Crawl- und KI-Signale | textbasierte Discovery- und Crawl-Signale für Search- und KI-Crawler; `llms.txt` ist der kompakte Routen- und Positionierungsindex fuer Agenten | `blocksy-child/inc/robots-txt.php`, `blocksy-child/inc/llms-txt.php`, `llms.txt` | Search-/KI-Crawler, native WordPress-Sitemap | repo-seitig live |
| Growth-Audit-Legacypfad | keine eigene UI mehr; geschützte Audit-Einstiege führen per 301 zum Marktcheck, alte Tools liefern 410 | `blocksy-child/inc/system-diagnose-page.php`, `blocksy-child/inc/helpers.php`, `docs/systems/audit-funnel.md` | WordPress | öffentlich retired; nur Redirect-/Kompatibilitätsvertrag |
| Nexus CRM & Blog Notify | gemeinsames CRM für Projekt-, White-Label- und Marktcheck-Anfragen mit Sales-Pipeline, Aktivitätsverlauf, Herkunft und Antwortfrist-Wächter, plus DOI- und Artikel-Mail-Logik | `blocksy-child/inc/crm.php`, `blocksy-child/inc/crm-sales/`, `blocksy-child/inc/contact-page.php`, `blocksy-child/inc/whitelabel-request.php`, `blocksy-child/inc/review-crm.php`, `blocksy-child/inc/blog-notify.php`, `docs/systems/nexus-crm-sales.md`, `docs/systems/blog-notify.md` | WordPress CPT/Meta, WordPress REST, wp_mail, Brevo, WP-Cron | repo-seitig live; `analysis-submit` (`inc/analysis-intake.php`) standardmäßig abgeschaltet |
| SEO Cockpit | Search-Console-basiertes SEO-Dashboard mit optionalem Koko- und Audit-Lead-Layer | `blocksy-child/inc/seo-cockpit/`, `blocksy-child/assets/css/seo-cockpit-admin.css`, `docs/systems/seo-cockpit.md` | Google Search Console API, optional Koko Analytics, Nexus CRM / Audit-CRM | repo-seitig implementiert; OAuth/API-Livezustand admin-owned, Koko optional; Legacy-/Nicht-Zielintent-Filter aktiv |
| Messung | `data-track-*`-Hooks, cookiefreie Anfrage-Herkunft im CRM, SEO-/Schema-Layer | `blocksy-child/assets/js/nexus-core.js`, `blocksy-child/inc/crm.php`, `blocksy-child/inc/seo-meta.php`, `blocksy-child/inc/org-schema.php`, Templates mit `data-track-*` | Koko Analytics (Plugin), Google Search Console | live; kein GTM, GA4 oder Pixel auf dieser Website |
| CTA- und Leadflow | CTA-Hierarchie vom ersten Besuch bis zur Diagnose, Folgeeinordnung und Qualifizierung | `blocksy-child/inc/shortcodes.php`, `blocksy-child/template-parts/footer-cta.php`, `blocksy-child/template-parts/trust-section.php`, Service-Templates | WordPress-Editor, Audit-Funnel, Cal.com, CRM | live |
| Public Proof Layer | zentraler oeffentlicher Proof- und Vokabular-Layer fuer kaufnahe Seiten | `blocksy-child/inc/helpers.php`, `blocksy-child/inc/shortcodes.php`, `blocksy-child/front-page.php`, `blocksy-child/page-wordpress-agentur.php`, `blocksy-child/page-kontakt.php`, `blocksy-child/inc/contact-page.php` | WordPress-Editor, oeffentliche Cases und Profile | live |
| WGOS Client Dashboard | internes Delivery-/Client-Dashboard mit minimaler Rolle und Capability | `blocksy-child/page-wgos.php`, `blocksy-child/inc/wgos/wgos-access.php` | WordPress-User-System | intern; noindex/nofollow; Zugriff nur fuer `manage_options` oder `view_wgos_dashboard` |
| Content- und SEO-System | Blog, Kategorie-Archive, Cornerstone-Content, Glossar-Registry und interne Verlinkung | `blocksy-child/home.php`, `blocksy-child/category.php`, `blocksy-child/single.php`, `blocksy-child/template-parts/post-title-visual.php`, `blocksy-child/page-seo-cornerstone.php`, `blocksy-child/inc/glossary/`, `blocksy-child/inc/blog-provider-posts.php`, `content/blog-drafts/` | WordPress-Editor, einmalige Theme-Seeds | live plus Ausbau |
| Client Portal | Kunden-Cockpit mit Login, Upload und optionalen Nutzer-Metadaten für Ressourcen, KPI und Roadmap | `blocksy-child/template-portal.php`, `blocksy-child/inc/client-portal.php`, `blocksy-child/inc/snippets.php` | WordPress-User-System, User Meta, Media Library | live; keine Mock-Daten mehr, Empty-State ohne gepflegtes `nexus_client_portal`; Pflege im Benutzerprofil |
| n8n-Automationen | inaktive optionale Workflow-Artefakte für spätere Analyse-/Routing-/Nurture-Schritte | `automations/n8n/` | n8n Cloud, CRM, Mail, evtl. Sheets | nicht in Operation; ignorieren, sofern n8n nicht explizit beauftragt ist |
| Marktcheck / System-Diagnose-Legacy | aktiver B2B-System-Intake auf der Solar-Landingpage; frühere System-Diagnose-Route bleibt als Redirect erhalten | `blocksy-child/page-solar-waermepumpen-leadgenerierung.php`, `blocksy-child/assets/js/solar-leadgenerierung-solara.js`, `blocksy-child/assets/js/solar-marketcheck-compact.js`, `blocksy-child/inc/system-diagnose-page.php`, `blocksy-child/inc/review-crm.php` | WordPress, Audit-CRM, Brevo/wp_mail, Cal.com; n8n nicht angebunden | System-Intake aktiv über `/wp-json/nexus/v1/audit-request` mit Contract `2026-05-26.audit-request.v1`, Trace-ID und strukturierten Fehlern; `/system-diagnose/`, `/readiness-diagnose/` und `/anfrage/` als Legacy-Redirects |
| Agenten- und Skill-System | Kontext, Guardrails und wiederholbare Skills; konkrete public Routes werden an `llms.txt` delegiert | `AGENTS.md`, `agents/skills/`, `llms.txt` | keine direkte Laufzeitabhaengigkeit | aktiv verdichtet |

## Website

Die Website ist aktuell der stabilste Teil des Repos. `blocksy-child/` ist der deploybare Kern; CI und Production-Deploy sind jetzt getrennt, und der Live-Deploy erfolgt erst nach erfolgreichem CI-Lauf fuer einen Push auf `main`.

Wichtige Merkmale:

- `functions.php` laedt die Module aus `inc/` zentral.
- `inc/enqueue.php` ist der Asset-Hub fuer CSS und JS pro Seitentyp.
- `inc/robots-txt.php` und `inc/llms-txt.php` liefern textbasierte Crawl- und Zitat-Signale für Search- und KI-Crawler direkt aus dem Theme.
- Ein Teil der Seiten ist editor-getrieben und nutzt `the_content()`.
- Ein anderer Teil ist hart codiert und traegt Business-Logik direkt im Template.
- Die kanonische Kontaktseite `/kontakt/` rendert im Frontend jetzt immer das versionierte Theme-Template statt editorgetriebener Altinhalte.
- Die frühere WGOS-Erklärung ist öffentlich entfallen; die Agentur-Seite ist eine Entscheidungsseite, und ihre frühere Methodenbeschreibung steht verdichtet in `/wordpress-agentur-hannover/#zusammenarbeit`. `page-wgos.php` ist kein Sales-Template mehr, sondern internes Client-Dashboard mit Login- und Capability-Schutz; alte WGOS-Erklärpfade sind keine erzwungenen 301-Ziele mehr und bleiben bei vorhandenen DB-Seiten noindex sowie sitemap-excluded.
- Die Homepage-Shortcodes liefern jetzt einen versionierten Public-Proof-Layer aus konservativen Leistungsmetriken, GitHub-Transparenz und audit-first Folgelogik statt Pilotangebot.

Kritische Dateien:

- `blocksy-child/functions.php`
- `blocksy-child/inc/robots-txt.php`
- `blocksy-child/inc/llms-txt.php`
- `blocksy-child/inc/enqueue.php`
- `blocksy-child/inc/seo-meta.php`
- `blocksy-child/inc/org-schema.php`
- `blocksy-child/page-wgos.php`

### Homepage-Verträge (2026-09-13)

- `/` ist Marke und Freelancer-Money-Page. `hu_get_commercial_route_map()`
  liefert für `home` und `freelancer` dieselbe URL. Spezialfunnel bleiben getrennt.
- `inc/helpers.php` leitet den früheren Freelancer-Pfad und alte Seiten-ID-/
  Template-Einstiege mit 301 nach `/` um; Routingparameter werden entfernt,
  Kampagnenparameter erhalten. `inc/seo-meta.php` schließt den alten Slug aus
  der Sitemap aus. Bestehende WordPress-Daten werden nicht gelöscht.
- Homepage-WebPage und Freelancer-Service verwenden `/`, `/#webpage` und
  `/#service`. Titel und Beschreibung kommen aus den zentralen Homepage-Helpers.
- Inhalte sind template-owned. Editor-FAQ-Caches bleiben ausgeschlossen.
- Vier Angebotslinks nutzen den vorhandenen Kontaktablauf mit passendem `focus`.
  Details: `CONVERSION_ROUTING.md` und `../decisions/homepage-freelancer-konsolidierung.md`.

## Crawl- und KI-Signale

Die Website stellt repo-seitig drei komplementäre Discovery-Flächen bereit:

- `/robots.txt` für generelle Crawl-Regeln inklusive expliziter KI-User-Agents
- `/llms.txt` für kompakte Entity-, Angebots- und URL-Signale in Markdown-Form
- `/wp-sitemap.xml` als native XML-Sitemap für kanonische URL-Discovery

Systemische Rolle:

- Search- und KI-Crawler bekommen eine saubere text/plain-Crawl-Oberfläche ohne Editor-Abhängigkeit.
- `llms.txt` verweist bewusst auf Money-Pages, Proof-Routen und Kontaktpfade statt auf eine lose URL-Liste.
- Die native Sitemap bleibt die kanonische URL-Quelle; `robots.txt` und `llms.txt` sind zusaetzliche Signale, kein Ersatz.

## n8n-Automationen

n8n ist aktuell nicht der Backend-Pfad des Marktchecks. Der aktive Marktcheck nutzt WordPress REST, Audit-CRM und Brevo/wp_mail. n8n-Artefakte bleiben nur als vorbereitende oder historische Arbeitsflaeche im Repo.

Regel:

- keine neue n8n-Datei ohne konkreten Workflow-Bedarf
- jedes aktive Workflow-Artefakt braucht Triplet aus JSON, Doku und Flow-Map
- Workflow-JSONs sind keine Standard-Agentenlekture

Marktcheck / System-Diagnose-Legacy:

- Aktive Route: `/solar-waermepumpen-leadgenerierung/#marktcheck`
- Legacy: `/system-diagnose/`, `/readiness-diagnose/` und `/anfrage/` leiten per 301 weiter
- Contract: aktiver REST-Contract `2026-05-26.audit-request.v1`; historischer n8n-Contract `automations/n8n/data-models/readiness-diagnosis-payload.v1.contract.json` bleibt nur für Legacy-Kontext intern stabil
- Status: B2B-System-Intake im Marktcheck-Abschnitt der Solar-Landingpage, sichtbar in zwei Schritten (`HU_MARKETCHECK_VISIBLE_STEPS`), intern fünf Datengruppen (`HU_MARKETCHECK_STEPS`); vier tatsächliche Fit-Signale (`solution_focus`, `business_fit`, `sales_team_size`, `project_timing`) führen zu den geschäftlichen Kontaktdaten. CPL, Anfragevolumen oder Engpass werden nicht aus anderen Antworten abgeleitet; die Firmen-PLZ bleibt Pflicht für die regionale Einordnung. Die frühere Audit-/Instant-Results-UI ist aus der Runtime entfernt
- WordPress REST: `/wp-json/nexus/v1/audit-request`; Antworten tragen `contractVersion`, `traceId` sowie `X-Nexus-Contract-Version`/`X-Nexus-Trace-Id`
- CRM: `nexus_review_request`, Audit-Typ `B2B-System-Intake`; Legacy-Energy-Intakes bleiben als `Marktcheck` rückwärtskompatibel
- Mail: interne Admin-Benachrichtigung und Lead-Bestätigung über zentrale Brevo-/`wp_mail`-Schicht
- n8n-Route: nicht angebunden; erst nach neuer Contract-/Consent-/Feature-Flag-Freigabe
- Default-Fragepfad: Leistungsfokus, wirtschaftlicher Projekt-Fit, Vertriebsverantwortung, Umsetzungshorizont, Firma, Name, Position, geschäftliche E-Mail und Firmen-PLZ; keine personenbezogenen Endkundendaten
- Retention: für n8n nicht anwendbar, weil nichts an n8n gesendet wird

Historische technische Touchpoints:

- `blocksy-child/assets/js/audit-live.js`
- `blocksy-child/assets/css/audit-results.css`
- `blocksy-child/page-360-deep-dive.php`
- historische Webhook-Namen `audit`, `audit-status`, `cja-analyze`

Fachliche Regel:

- n8n-JSONs gelten nie als selbsterklaerend.
- Jeder aktive Workflow braucht Doku, Flow-Map, Status und Risiko-Abschnitt.

## Growth-Audit-Legacypfad

Der Growth Audit und die frühere System-Diagnose-Seite sind nicht mehr der Primär-CTA des Systems und dürfen nicht als Hauptfunnel zurückkehren. Geschützte Legacy-Einstiege mit wahrscheinlicher externer Nutzung leiten auf den Marktcheck; interne Tool-, ROI- und Service-Altlasten liefern 410 statt als Redirect-Netz weiterzulaufen. WGOS-Altpfade bleiben separat noindex/access-protected, solange sie intern noch Dashboard-/Asset-Hub-Funktionen tragen.

Aktuelle Logik:

1. Kalter Solar-/SHK-Traffic führt primär zu `/solar-waermepumpen-leadgenerierung/#marktcheck`.
2. `/system-diagnose/`, `/readiness-diagnose/`, `/anfrage/`, `/growth-audit/`, alte Audit-Aliasse und `/wordpress-tech-audit/` leiten per 301 auf den Marktcheck; `/kostenlose-tools/`, `/tools/`, `/website-performance-analyse/`, `/roi-rechner/` und alte Service-Slugs liefern 410.
3. Die frühere Audit-/Instant-Results-UI ist aus der Runtime entfernt; verbleibende Deep-Dive-/Result-Artefakte werden separat als historische 3B-Reste bewertet.
4. Die Branchen-Landingpage für Solar-/Wärmepumpen-Anbieter führt ihre großen CTA-Flächen zum eigenen `#marktcheck` im Hero.

## Nexus CRM und Blog Notify

Das Repo enthält ein gemeinsames CRM-Modell für:

- Marktcheck-Anfragen (`audit-request`, Datensatz `nexus_review_request`)
- Projekt- und Kontaktanfragen (`contact-request`)
- White-Label-Anfragen (`whitelabel-request`, Quelle und Segment `whitelabel_request`)
- Blog-Abos

Vertriebsrelevante Kontakte bekommen eine Sales-Chance (`nexus_opportunity`) mit
Aktivitätsverlauf (`nexus_crm_activity`); Details in `docs/systems/nexus-crm-sales.md`.

Architektur:

- `nexus_review_request` bleibt der spezialisierte Datensatz fuer Audit-Intake
- `nexus_contact` ist der gemeinsame Kontakt-Datensatz fuer kontaktnahe Folgeanliegen und Blog-Abos
- `contact.js` erlaubt gescopten Landingpages opt-in-spezifische Formularcopy und DOM-nahe Fehlerreihenfolge ueber `data-contact-submit-label`, `data-contact-message-placeholder` und `data-contact-dom-error-order`; Payload-Felder und REST-Endpunkt bleiben unveraendert
- das Admin-Menue heisst jetzt `Nexus CRM`
- Audit-, Kontakt- und White-Label-Anfragen speichern Formular-Landingpage, ersten internen Einstieg, vorherige interne Seite, Referrer und Kampagnenangaben; Kontakt und White-Label zusätzlich die optionale Angabe, wie jemand aufmerksam wurde
- Blog-Abos arbeiten mit eigenem DOI- und Abmelde-Flow ueber `/neue-artikel-per-email/`
- Artikel-Benachrichtigungen werden in V1 manuell pro Beitrag angestossen und dann in kleinen Batches versendet

## Messung

Diese Website misst bewusst schlank und ohne Cookie-Banner.

Im Repo:

- `data-track-*`-Attribute auf CTAs und Content-Bausteinen; Skripte schreiben nur dann in `dataLayer`, wenn eines existiert
- cookiefreie Anfrage-Herkunft aus der Browser-Session, gespeichert am CRM-Kontakt (`nexus_get_inquiry_attribution_meta()` in `inc/crm.php`)
- noindex- und SEO-Meta-Logik; Schema-Ausgabe für Organisation, Services und Profile; editorgetriebenes FAQPage-Schema wird per `save_post` gecacht und im Frontend nur gelesen

Außerhalb des Repos:

- Koko Analytics als Plugin (admin-owned) für Seitenaufrufe
- Google Search Console, angebunden über das SEO-Cockpit

GTM, sGTM, GA4, Consent Mode und Meta CAPI sind Leistungen für Kunden, keine Abhängigkeiten dieser Website.

## SEO Cockpit

Neu im Repo:

- eigener Admin-Bereich `SEO Cockpit`
- kompaktes Snapshot-Widget im WordPress-Dashboard
- Search-Console-Anbindung per OAuth-Flow direkt im Theme
- gecachte Kernmetriken fuer Klicks, Impressionen, CTR, Position, Top Pages und Top Queries
- automatischer Snapshot-Refresh per WP-Cron
- optionale Koko-Erkennung als lokaler Traffic-Layer fuer spaetere Zusammenfuehrung
- Audit-Lead-Layer aus `nexus_review_request` mit dem gemeinsamen Statusvertrag `qualified|nurture`, qualifizierter Anzahl, Source-Mix und intern attribuierten Seiten für neue Leads

Systemische Rolle:

- Search Console liefert die externe SEO-Sicht
- Koko liefert optional die lokale Seiten- und Traffic-Sicht
- das Audit-CRM liefert zusaetzlich Lead-Signale, qualifizierte Anfragen und interne Attributionsdaten; der sessionbasierte Frontend-Helper uebernimmt `utm_source`/`utm_term` als Quelle bzw. Suchbegriff, erkennt Google-/Meta-Klickparameter nur als Quellensignal und speichert keine rohe Klick-ID
- WordPress bleibt der Ort, an dem diese Perspektiven in einem operativen Cockpit zusammenlaufen
- Der CSV-Vertrag exportiert Query×URL- und Page-Total-Zeilen getrennt, vereinigt aktuelle und vorherige Perioden und kennzeichnet vollständig verschwundene Rankings explizit für Gap-/Drift-Auswertungen

## CTA- und Leadflow

Die CTA-Hierarchie ist klar und sollte nicht verwischt werden.

- Primärer CTA für kalten Solar-/SHK-Traffic: `Marktcheck`
- Retired Warm-intent Intake: `/anfrage/` leitet auf `/solar-waermepumpen-leadgenerierung/#marktcheck`
- Proof-Pfade: `/case-study-solar-leadgenerierung/`, `/ergebnisse/`
- Kein separater öffentlicher Tiefendiagnose-Schritt im Hauptfunnel
- Umsetzungsnahe Kontaktwege: `Umsetzung / Optimierung`, `Laufende Weiterentwicklung`
- Partner-/Agentur-Einstieg auf der Whitelabel-Seite: `Whitelabel-Fit-Gespraech`
- Kein oeffentlicher 360-/Blueprint-CTA mehr im Erstkontakt
- Eskalations-CTA: `Cal.com`-Strategiecall
- Default-URL fuer direkte Gespraechsbuchung in Audit-, Kontakt- und WGOS-Kontexten: `https://cal.com/hasim-uener/30min?overlayCalendar=true`
- Partner-/Agentur-URL fuer direkte Gespraechsbuchung auf der Whitelabel-Seite: `https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true`
- Direkte Gespraechsbuchung wird repo-seitig als progressive Enhancement umgesetzt: Modal bei aktivem JS, normaler Link als Fallback, auch bei mehreren Cal.com-Event-Typen.
- Utility-CTA: Kunden-Portal fuer Bestandskunden

Wichtige Repopunkte:

- `blocksy-child/template-parts/footer-cta.php`
- `blocksy-child/template-parts/trust-section.php`
- `blocksy-child/inc/shortcodes.php`
- `blocksy-child/page-kontakt.php`
- `blocksy-child/inc/contact-page.php`
- Service- und Hub-Templates mit Audit-CTA

## Content- und SEO-System

Das Content-System ist auf Pillar- und Cluster-Logik aufgebaut.

Bausteine:

- `home.php`: reduziertes Editorial-Blogarchiv mit echten Kategorie-Links, bildloser Artikelliste, CollectionPage-/ItemList-Schema und Marktcheck-Führung
- `category.php`: bildloses Archiv der vier Dossiers (`leadgenerierung`, `wordpress-performance`, `tracking`, `cro`) mit Kategorienavigation, zentralen SEO-Fallbacks, thematischen Vertiefungslinks und CollectionPage-/ItemList-Schema; Marktcheck-Führung nur im Dossier `leadgenerierung`, sonst Projektanfrage
- `single.php` → `template-parts/single-reader.php`: Artikel mit reduziertem Editorial-Hero, dossierabhängiger Kontextbrücke, TOC, strukturierter Next-Step-Zone, thematischem Related Content und Footer-CTA; die Provider-Entscheide für Checkfox und Aroundhome ersetzen den Editor-Inhalt slug-spezifisch durch repo-eigene Templates und Assets
- `template-parts/post-title-visual.php`: generierte Titelgrafik als Fallback für Beiträge ohne Featured Image
- `page-seo-cornerstone.php`: Cornerstone-Template mit starkem Entscheider-Fokus
- `inc/glossary/`: Registry, Alias-Logik und Blog-Autolinking für Fachbegriffe, 90-Tage-Fokus-Keywords und Money-Page-Brücken
- `content/blog-drafts/`: Rohfassungen ausserhalb von WordPress; Lead-Anbieter-Markteinordnungen werden zusätzlich einmalig über `blocksy-child/inc/blog-provider-posts.php` in WordPress veröffentlicht.

Risiko:

- Content liegt teils im Repo, teils im WordPress-Editor.
- Ohne saubere Statuspflege kann die Source of Truth unscharf werden.

## Systemabhaengigkeiten

- Website -> CTA-Layer -> Marktcheck -> Fitentscheidung -> Umsetzung / CRM / Sales
- Website -> Anfrage-Formulare -> WordPress REST -> CRM mit Herkunft -> Sales-Pipeline / Antwortfrist-Wächter
- Website -> `data-track-*` / Koko Analytics / Search Console -> SEO-Cockpit
- Blog / SEO -> interne Verlinkung -> Service-Seiten / Audit -> Leadflow
- WordPress-Editor -> Theme-Struktur -> Live-Seiten
- GitHub Actions CI -> Build-Paket aus `blocksy-child/` -> GitHub Actions Deploy -> Live-Theme

## Kritische Abhaengigkeiten

- WordPress Block-Editor fuer editorgetriebene Seiten ausserhalb des Audit-Shells
- SCF (Secure Custom Fields) fuer SEO- und Content-Fallbacks. ACF ist
  deinstalliert; SCF ist dessen WordPress.org-Fork, daher bleiben die `acf_*`-API
  (`acf/init`, `acf_add_local_field_group()`) und der Dateiname `inc/acf.php`
  unveraendert gueltig.
- Theme-eigener SEO-Layer (seo-meta.php) für Title, Description, OG, Canonical und Robots
- Native WordPress-Sitemap (/wp-sitemap.xml)
- Theme-eigene Crawl-Signale für `/robots.txt` und `/llms.txt`
- Brevo-API für Transaktionsmails (über `wp_mail`, Zugangsdaten außerhalb des Repos)
- WP-Cron für Follow-ups, Antwortfrist-Wächter und SEO-Cockpit-Snapshots; wegen des Seiten-Caches sollte ein Server-Cron `wp-cron.php` aufrufen
- Cal.com fuer direkte Gespraechsbuchung
- SSH-Deploy auf Basis des gebauten `blocksy-child/`-Pakets

## Groesste Risiken

- `page-wgos.php` ist fachlich wichtig und inzwischen deutlich verschlankt, bleibt aber technisch template-driven statt editor- oder SCF-getrieben.
- Kaufnahe Inhalte liegen weiter teils im Repo und teils im WordPress-Editor; Titel, Excerpts, Karten und manuell kuratierte Related-Module koennen die neue Proof- und Tonalitaetslogik unterlaufen, wenn sie nicht separat gepflegt werden.
- WP-Cron hängt ohne Server-Cron an nicht gecachten Aufrufen; Follow-ups und Antwortfrist-Erinnerungen können sich dann verzögern.
- Manuelle WordPress-Admin-Schritte existieren noch als Betriebswissen und muessen weiter systematisiert werden.
