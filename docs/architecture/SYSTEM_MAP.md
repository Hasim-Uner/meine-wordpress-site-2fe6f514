# System Map

Stand: 2026-05-08. Diese Karte basiert auf dem Repo-Inhalt plus punktueller Live-Verifikation der Anfrage-System-Analyse-Route.

## Hauptsysteme

| System | Zweck | Repo-Orte | Externe Abhaengigkeiten | Status |
| --- | --- | --- | --- | --- |
| Website | deploybarer WordPress-Theme-Code | `blocksy-child/`, `.github/workflows/ci.yml`, `.github/workflows/deploy.yml`, `docs/architecture/DEPLOYMENT.md` | WordPress, Blocksy Parent Theme, ACF | live |
| Crawl- und KI-Signale | textbasierte Discovery- und Crawl-Signale für Search- und KI-Crawler | `blocksy-child/inc/robots-txt.php`, `blocksy-child/inc/llms-txt.php`, `llms.txt` | Search-/KI-Crawler, native WordPress-Sitemap | repo-seitig live |
| Growth-Audit-Legacypfad | sekundäre Instant-Diagnose und Rückführung in qualifizierte Anfragepfade, kein Hauptfunnel | `blocksy-child/page-audit.php`, `blocksy-child/inc/cja-shortcode.php`, `blocksy-child/assets/css/cja-audit.css`, `blocksy-child/assets/js/cja-audit.js`, `blocksy-child/page-solar-waermepumpen-leadgenerierung.php`, `blocksy-child/assets/js/review-funnel.js`, `blocksy-child/assets/js/cal-embed.js`, `blocksy-child/inc/review-crm.php`, `docs/systems/audit-funnel.md` | n8n Webhook, WordPress, Cal.com, optional WordPress CRM auf Legacy-Pfaden | live als Sekundär-/Legacy-Route |
| Nexus CRM & Blog Notify | gemeinsames CRM für Analyse-Leads, Audit-, Folgeanalyse-, Umsetzungs- und Bestandskunden-Anfragen plus DOI- und Artikel-Mail-Logik | `blocksy-child/inc/crm.php`, `blocksy-child/inc/analysis-intake.php`, `blocksy-child/inc/blog-notify.php`, `blocksy-child/template-parts/blog-notify.php`, `blocksy-child/page-blog-notify.php`, `docs/systems/blog-notify.md` | WordPress CPT/Meta, WordPress REST, wp_mail, Brevo | repo-seitig live; Analyse-REST und Brevo-Konfiguration am 2026-05-07 verifiziert |
| SEO Cockpit | Search-Console-basiertes SEO-Dashboard mit optionalem Koko- und Audit-Lead-Layer | `blocksy-child/inc/seo-cockpit.php`, `blocksy-child/assets/css/seo-cockpit-admin.css`, `docs/systems/seo-cockpit.md` | Google Search Console API, optional Koko Analytics, Nexus CRM / Audit-CRM | repo-seitig vorbereitet, OAuth und Live-Daten offen |
| Tracking | Tracking-ready Markup, CTA-Events, SEO-/Schema-Layer | `blocksy-child/inc/helpers.php`, `blocksy-child/inc/seo-meta.php`, `blocksy-child/inc/org-schema.php`, Templates mit `data-track-*` | GTM, sGTM, GA4, Consent Mode v2, Meta CAPI | teils im Repo, teils extern |
| CTA- und Leadflow | CTA-Hierarchie vom ersten Besuch bis zur Diagnose, Folgeeinordnung und Qualifizierung | `blocksy-child/inc/shortcodes.php`, `blocksy-child/template-parts/footer-cta.php`, `blocksy-child/template-parts/trust-section.php`, Service-Templates | WordPress-Editor, Audit-Funnel, Cal.com, CRM | live |
| Public Proof Layer | zentraler oeffentlicher Proof- und Vokabular-Layer fuer kaufnahe Seiten | `blocksy-child/inc/helpers.php`, `blocksy-child/inc/shortcodes.php`, `blocksy-child/front-page.php`, `blocksy-child/page-wordpress-agentur.php`, `blocksy-child/page-wgos.php`, `blocksy-child/page-kontakt.php`, `blocksy-child/inc/contact-page.php` | WordPress-Editor, oeffentliche Cases und Profile | live |
| Content- und SEO-System | Blog, Pillar-Hubs, Cornerstone-Content, interne Verlinkung | `blocksy-child/category.php`, `blocksy-child/single.php`, `blocksy-child/page-seo-cornerstone.php`, `content/blog-drafts/` | WordPress-Editor | live plus Ausbau |
| Client Portal | Kunden-Cockpit mit Login, Upload und Roadmap-Slots | `blocksy-child/template-portal.php`, `blocksy-child/inc/client-portal.php`, `blocksy-child/inc/snippets.php` | WordPress-User-System, Media Library | live, aber aktuell mit Mock-Daten |
| n8n-Automationen | Workflow-Logik für aktiven Legacy-Audit und spätere Analyse-/Routing-/Nurture-Schritte hinter Contract, Consent und Feature-Flag | `automations/n8n/`, `blocksy-child/assets/js/cja-audit.js`, `blocksy-child/assets/js/audit-live.js` | n8n auf `n8n.hasimuener.de`, CRM, Mail, evtl. Sheets | Legacy-Audit aktiv; Anfrage-System-Analyse noch nicht angebunden |
| Anfrage-System-Analyse | evidenzbasierter Founding-Partner-Fitcheck mit Marktbild, lokaler Auswertung und Consent-basiertem Kontakt-Submit | `blocksy-child/readiness/`, `blocksy-child/page-readiness-diagnose.php`, `blocksy-child/inc/anfrage-system-analyse-page.php`, `blocksy-child/inc/analysis-intake.php`, `automations/n8n/data-models/readiness-diagnosis-payload.v1.contract.json`, `docs/architecture/PRIVACY.md` | WordPress, React-Mikro-App im Theme, Nexus CRM, Brevo; n8n nicht angebunden | repo-seitig als 8-Schritt-Form aktiv, `/readiness-diagnose/` und `/anfrage/` als Legacy-Redirects, WordPress-REST-Submit aktiv |
| Agenten- und Prompt-System | Kontext, Guardrails, Skills und minimale Legacy-Briefings | `AGENTS.md`, `agents/skills/`, `prompts/README.md` | keine direkte Laufzeitabhaengigkeit | in Aufbau |

## Website

Die Website ist aktuell der stabilste Teil des Repos. `blocksy-child/` ist der deploybare Kern; CI und Production-Deploy sind jetzt getrennt, und der Live-Deploy erfolgt erst nach erfolgreichem CI-Lauf fuer einen Push auf `main`.

Wichtige Merkmale:

- `functions.php` laedt die Module aus `inc/` zentral.
- `inc/enqueue.php` ist der Asset-Hub fuer CSS und JS pro Seitentyp.
- `inc/robots-txt.php` und `inc/llms-txt.php` liefern textbasierte Crawl- und Zitat-Signale für Search- und KI-Crawler direkt aus dem Theme.
- Ein Teil der Seiten ist editor-getrieben und nutzt `the_content()`.
- Ein anderer Teil ist hart codiert und traegt Business-Logik direkt im Template.
- Die kanonische Kontaktseite `/kontakt/` rendert im Frontend jetzt immer das versionierte Theme-Template statt editorgetriebener Altinhalte.
- `page-wgos.php` ist als template-getriebene Sales-Page fuer `WGOS = WordPress Growth Operating System` versioniert; Struktur, erklaerter Framework-Kontext und CTA-Hierarchie liegen im Repo, nicht im Editor.
- Die Homepage-Shortcodes liefern jetzt einen versionierten Public-Proof-Layer aus konservativen Leistungsmetriken, GitHub-Transparenz und audit-first Folgelogik statt Pilotangebot.

Kritische Dateien:

- `blocksy-child/functions.php`
- `blocksy-child/inc/robots-txt.php`
- `blocksy-child/inc/llms-txt.php`
- `blocksy-child/inc/enqueue.php`
- `blocksy-child/inc/seo-meta.php`
- `blocksy-child/inc/org-schema.php`
- `blocksy-child/page-wgos.php`

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

Im Repo liegen erste n8n-Artefakte, aber n8n ist nicht der Backend-Pfad der Anfrage-System-Analyse. Die neue Analyse nutzt lokal berechnetes Scoring und erst im separaten Kontakt-Schritt WordPress REST, Nexus CRM und Brevo. Die Rolle von n8n ist aktuell getrennt:

- aktiver Instant-Results-Layer für den Growth-Audit-Legacypfad über `https://n8n.hasimuener.de/webhook/audit` plus `https://n8n.hasimuener.de/webhook/audit-status`, mit `https://n8n.hasimuener.de/webhook/cja-analyze` als Legacy-Fallback
- künftiges Lead-Routing und Nurture erst nach Contract-, Consent- und Feature-Flag-Freigabe
- Reporting- oder CRM-Bridges, die in Texten und Angebotslogik bereits angedeutet werden

Aktuell dokumentierter Workflow:

- `automations/n8n/workflows/audit-funnel__customer-journey-audit__refactor.json`
- Doku: `automations/n8n/docs/audit-funnel__customer-journey-audit__refactor.md`
- Flow-Map: `automations/n8n/flow-maps/audit-funnel__customer-journey-audit__refactor.md`

Anfrage-System-Analyse:

- Route: `/anfrage-system-analyse/`
- Legacy: `/readiness-diagnose/` leitet per 301 weiter
- Contract: `automations/n8n/data-models/readiness-diagnosis-payload.v1.contract.json` bleibt bis zur nächsten Contract-Version intern stabil
- Status: lokale 8-Schritt-React-App plus Kontakt-Submit nach Einwilligung
- WordPress REST: `/wp-json/nexus/v1/analysis-submit`
- CRM: `nexus_contact`, Segment `analysis_lead`
- Mail: interne Admin-Benachrichtigung und Lead-Bestätigung über zentrale Brevo-/`wp_mail`-Schicht
- n8n-Route: nicht angebunden; erst nach neuer Contract-/Consent-/Feature-Flag-Freigabe
- Default-Fragepfad: kein Klarname, keine Telefonnummer, keine E-Mail, keine personenbezogenen Endkundendaten
- Retention: für n8n nicht anwendbar, weil nichts an n8n gesendet wird

Bekannte technische Touchpoints:

- `blocksy-child/inc/cja-shortcode.php`
- `blocksy-child/assets/js/cja-audit.js`
- `blocksy-child/assets/js/audit-live.js`
- aktive Webhooks `audit` und `audit-status`
- Legacy-Fallback `cja-analyze`

Fachliche Regel:

- n8n-JSONs gelten nie als selbsterklaerend.
- Jeder Workflow braucht zusaetzlich Doku, Flow-Map, Status und Risiko-Abschnitt.
- Der aktuelle Audit-Workflow ist fachlich wertvoll, aber architektonisch refactor-beduerftig.

## Growth-Audit-Legacypfad

Der Growth Audit ist nicht mehr der Primär-CTA des Systems und darf nicht als Hauptfunnel zurückkehren. Er bleibt als Legacy-/Sekundärpfad bestehen, während die Anfrage-System-Analyse der primäre Einstieg ist.

Aktuelle Logik:

1. Kalter B2B-Traffic führt primär zur `/anfrage-system-analyse/`.
2. Die aktive Audit-Landingpage nimmt nur die URL auf, startet den n8n-Job erst nach explizitem Klick über `audit` und pollt den Status anschließend über `audit-status`.
3. Das Frontend rendert das Ergebnis direkt auf der Seite als Modul-Dashboard; der Client akzeptiert sowohl den neuen V3-Payload als auch den bisherigen Direkt-Payload und nutzt `cja-analyze` nur noch als Fallback.
4. Die Branchen-Landingpage für Solar-/Wärmepumpen-Anbieter nutzt weiter einen separaten Multi-Step-Intake mit WordPress-CRM-Stack und serverseitigem Fallback.
5. Direkte Eskalation nach dem Ergebnis läuft über `/kontakt/` oder je nach Kontext über `Cal.com`.
6. Direkte Gesprächs-CTAs bleiben als normale Links erhalten, werden im Frontend aber per `blocksy-child/assets/js/cal-embed.js` event-typ-spezifisch zu einem Modal-Embed im Seitenkontext erweitert.
7. Der frühere 48h-Intake für die Hauptroute bleibt im Repo als Legacy-Layer, ist aber nicht mehr der Default-Flow.

## Nexus CRM und Blog Notify

Das Repo enthaelt jetzt zusaetzlich ein gemeinsames CRM-Modell fuer:

- Audit-Anfragen
- Folgeanalyse-, Umsetzungs- und Weiterentwicklungs-Anfragen
- Blog-Abos

Architektur:

- `nexus_review_request` bleibt der spezialisierte Datensatz fuer Audit-Intake
- `nexus_contact` ist der gemeinsame Kontakt-Datensatz fuer kontaktnahe Folgeanliegen und Blog-Abos
- das Admin-Menue heisst jetzt `Nexus CRM`
- neue Audit-Requests speichern jetzt auch Formular-Landingpage, ersten internen Einstieg, vorherige interne Seite und Referrer fuer spaetere SEO-/Lead-Auswertung
- Blog-Abos arbeiten mit eigenem DOI- und Abmelde-Flow ueber `/neue-artikel-per-email/`
- Artikel-Benachrichtigungen werden in V1 manuell pro Beitrag angestossen und dann in kleinen Batches versendet

## Tracking

Das Repo ist tracking-ready, aber nicht tracking-vollstaendig.

Im Repo vorhanden:

- `data-track-*` Attribute auf CTAs und Content-Bausteinen
- noindex- und SEO-Meta-Logik
- Schema-Ausgabe fuer Organisation, Services und Profile
- neue Homepage-Actions fuer Trust und Einstiegsangebote: `cta_github_repo`, `cta_proof_linkedin`

Ausserhalb des Repos:

- GTM / sGTM Container
- Consent Management
- GA4 Property und Event-Routing
- Meta CAPI
- CRM-Felder und Attribution

Systemische Grenze:

- Repo = Markup, Hooks, Dokumentation.
- Externe Plattformen = operative Konfiguration.

## SEO Cockpit

Neu im Repo:

- eigener Admin-Bereich `SEO Cockpit`
- kompaktes Snapshot-Widget im WordPress-Dashboard
- Search-Console-Anbindung per OAuth-Flow direkt im Theme
- gecachte Kernmetriken fuer Klicks, Impressionen, CTR, Position, Top Pages und Top Queries
- automatischer Snapshot-Refresh per WP-Cron
- optionale Koko-Erkennung als lokaler Traffic-Layer fuer spaetere Zusammenfuehrung
- Audit-Lead-Layer aus `nexus_review_request` mit Status, Source-Mix und intern attribuierten Seiten fuer neue Leads

Systemische Rolle:

- Search Console liefert die externe SEO-Sicht
- Koko liefert optional die lokale Seiten- und Traffic-Sicht
- das Audit-CRM liefert zusaetzlich Lead-Signale und neue interne Attributionsdaten
- WordPress bleibt der Ort, an dem diese Perspektiven in einem operativen Cockpit zusammenlaufen

## CTA- und Leadflow

Die CTA-Hierarchie ist klar und sollte nicht verwischt werden.

- Primärer CTA für kalten B2B-Traffic: `Anfrage-System-Analyse`
- Retired Warm-intent Intake: `/anfrage/` leitet auf `/anfrage-system-analyse/`
- Proof-Pfade: `/e3-new-energy/`, `/ergebnisse/`
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

- `category.php`: Pillar-Hub mit Featured Entry Point
- `single.php`: Artikel mit TOC, Related Content und Footer-CTA
- `page-seo-cornerstone.php`: Cornerstone-Template mit starkem Entscheider-Fokus
- `content/blog-drafts/`: Rohfassungen ausserhalb von WordPress

Risiko:

- Content liegt teils im Repo, teils im WordPress-Editor.
- Ohne saubere Statuspflege kann die Source of Truth unscharf werden.

## Systemabhaengigkeiten

- Website -> CTA-Layer -> Anfrage-System-Analyse -> Fitentscheidung -> Umsetzung / CRM / Sales
- Website -> Tracking-Layer -> GTM / GA4 / Consent -> Reporting / Optimierung
- Blog / SEO -> interne Verlinkung -> Service-Seiten / Audit -> Leadflow
- WordPress-Editor -> Theme-Struktur -> Live-Seiten
- GitHub Actions CI -> Build-Paket aus `blocksy-child/` -> GitHub Actions Deploy -> Live-Theme

## Kritische Abhaengigkeiten

- WordPress Block-Editor fuer editorgetriebene Seiten ausserhalb des Audit-Shells
- ACF fuer SEO- und Content-Fallbacks
- Theme-eigener SEO-Layer (seo-meta.php) für Title, Description, OG, Canonical und Robots
- Native WordPress-Sitemap (/wp-sitemap.xml)
- Theme-eigene Crawl-Signale für `/robots.txt` und `/llms.txt`
- Fluent Forms fuer die vertiefte Folgeanalyse
- n8n auf `n8n.hasimuener.de` fuer den aktiven Instant-Results-Audit inklusive Start-, Status- und Legacy-Fallback-Webhook
- Cal.com fuer direkte Gespraechsbuchung
- SSH-Deploy auf Basis des gebauten `blocksy-child/`-Pakets

## Groesste Risiken

- `page-wgos.php` ist fachlich wichtig und inzwischen deutlich verschlankt, bleibt aber technisch template-driven statt editor- oder ACF-getrieben.
- Kaufnahe Inhalte liegen weiter teils im Repo und teils im WordPress-Editor; Titel, Excerpts, Karten und manuell kuratierte Related-Module koennen die neue Proof- und Tonalitaetslogik unterlaufen, wenn sie nicht separat gepflegt werden.
- Der aktive CJA-Flow haengt an einem impliziten n8n-Payload-Contract; ohne versionierten Workflow-Export und Response-Schema bleibt die Schnittstelle dokumentatorisch fragil.
- Tracking-, Consent- und CRM-Logik sind operativ relevant, aber noch nicht als Repo-System dokumentiert.
- Manuelle WordPress-Admin-Schritte existieren noch als Betriebswissen und muessen weiter systematisiert werden.
