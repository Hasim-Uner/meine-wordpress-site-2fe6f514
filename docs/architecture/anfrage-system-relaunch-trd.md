# Technical Requirements Document: Anfrage-System Relaunch

**Document Status:** Architecture / Implementation Plan  
**Datum:** 2026-05-04  
**Primary Sources:** `AGENTS.md`, `docs/architecture/LIVE_STATUS.md`, `docs/architecture/SYSTEM_MAP.md`, `docs/specs/anfrage-system-analyse-form-v1.md`, `docs/decisions/0010-anfrage-system-analyse-single-entry.md`, Repo-Scan am 2026-05-04  
**Target Positioning:** Anfrage-Systeme fuer Solar-, SHK- und Waermepumpenbetriebe mit Founding-Partner-Fitcheck  
**Non-Goals:** SaaS, Multi-Tenant-App, oeffentlicher Growth-Audit-Hauptfunnel, oeffentliche Tiefendiagnose, neuer n8n-Webhook ohne Freigabe, PII im Default-Pfad

> Core architecture decision: `hasimuener.de` wird ein proof-gefuehrter Anfrage-System-Funnel mit WordPress-Core und React-Funnel-Layer; die EnergieFahrplan-Demo verkauft das Erlebnis, die Anfrage-System-Analyse entscheidet den Fit.

---

## 1. Executive Architecture Decision

Die Website muss von einer breiten WordPress-/Audit-Kommunikation zu einem engen Anfrage-System-Funnel wechseln. Der oeffentliche Pfad lautet: Proof ansehen, EnergieFahrplan-Demo erleben, Anfrage-System-Analyse starten, danach nur bei Fit in die Umsetzung gehen.

| Area | Current State | Target State | Decision |
|---|---|---|---|
| Positioning | Pivot ist in `AGENTS.md` gesetzt, einzelne Live-Dokumente und Templates tragen noch Audit-/Readiness-/alte Proof-Sprache. | Ein Angebot: eigenes Anfrage-System fuer passende Solar-/SHK-/Waermepumpenbetriebe. | Canon in `blocksy-child/inc/canon/` bleibt fuehrend; alte Woerter werden per Lint und PRs reduziert. |
| Funnel | `/anfrage-system-analyse/` aktiv, aber React-App und Template heissen intern noch `readiness`. | Demo -> Analyse -> Umsetzung -> optional Performance/Premium. | Deutsche Customer-Copy; interne Altpfade nur solange sie technisch guenstig sind. |
| Trust | E3-Beweis ist sichtbar, aber Zahlen driften in PHP/Docs. | Ein E3-Canon mit `150 EUR -> 22 EUR`, `-85,3 %`, `1.750+`, `12 %`, `9 Monate`. | Proof-Drift ist P0, weil sie direkt Vertrauen und Sales-Sicherheit trifft. |
| SEO | Analyse und Demo sind aktuell `noindex, follow`; Proof-Routen bleiben indexierbar. | Index nur fuer belastbare Proof- und Money-Pages. | `/anfrage-system-analyse/` bleibt bis Form/Offer-final noindex. |
| Architecture | WP-Core mit React-Micro-Apps existiert bereits. | WordPress fuehrt Pages/SEO/Content; React fuehrt Funnel-Erlebnisse. | Mono-Repo bleibt; `blocksy-child/energie-fahrplan/` ist Schablone. |

## 2. Deep Context Ingestion: Strategy and Repo Findings

### 2.1 Strategy Findings

| Strategy Dimension | Finding | Technical Consequence |
|---|---|---|
| Positioning | Kein breiter Agentur-Pitch; enger Anfrage-System-Fokus. | Header, Hero, CTA und Footer duerfen nicht auf generische Kontaktlogik zurueckfallen. |
| Target customer | Solar, SHK, Waermepumpe, DACH, 10-25 MA, Werbebudget-Fit. | Analyseformular braucht harte Fit-Fragen statt weicher Kontaktfelder. |
| Exclusions | Analyse ist kein kostenloser Massen-Leadmagnet und kein Admin-Audit. | Kein Admin-Zugang, keine PII im Default, keine Downloadpflicht als Lead-Hook. |
| Offer architecture | Founding-Partner-Fit -> Umsetzung -> optional Performance/Premium. | Pricing- und Messaging-Canons muessen Templates treiben. |
| Proof | E3 ist Anker-Case, nicht einziger Case. | `/e3-new-energy/` bleibt Proof-Route; Zahlen muessen canonisiert werden. |
| Demo | EnergieFahrplan soll aus Kaeufersicht fuehlbar machen, was gebaut wird. | Demo braucht interaktive Eingabe, Ergebnisbuehne, Prozessvisualisierung und lokale PDF-Ausgabe. |

### 2.2 Repository Findings

| Component | Current Role | Relaunch Assessment |
|---|---|---|
| `front-page.php` | Homepage mit Pivot-Elementen, Proof und CTA. | Muss E3-Zahlen-Canon nutzen und Growth-/Audit-Sprache weiter reduzieren. |
| `page-energie-fahrplan-demo.php` | Template fuer eingebettete Demo. | Passt zur Zielarchitektur; Demo braucht noch Showroom-Haertung und Tracking-Spec. |
| `inc/energy-demo-page.php` | Virtuelle Demo-Route. | Behalten; noindex ist korrekt fuer Showroom ohne SEO-Ziel. |
| `page-readiness-diagnose.php` | Technisches Template fuer `/anfrage-system-analyse/`. | Funktional ok, semantisch driftend; spaeter umbenennen oder klar als Legacy-Alias dokumentieren. |
| `inc/anfrage-system-analyse-page.php` | Deutsche virtuelle Analyse-Route plus Legacy-Redirect. | Zielrichtig; bleibt Router-Schicht. |
| `blocksy-child/readiness/` | React-App-Shell fuer Analyse. | Funktional weiter nutzbar, aber Customer-Copy darf nur `Anfrage-System-Analyse` nutzen. |
| `inc/canon/*.php` | Pricing, Founding, Diagnose, Messaging. | Muss Single Source of Truth bleiben; keine neuen hardcodierten Preis-/Frame-Texte. |
| `automations/n8n/data-models/readiness-diagnosis-payload.v1.contract.json` | Alter Contract fuer geplanten Submit. | Intern stabil lassen; naechste Version sollte Analyse-Semantik ausdruecken. |
| `docs/architecture/LIVE_STATUS.md` | Live-Systemueberblick. | Enthalt noch alte Proof-/Growth-Audit-Aussagen; nach Code-Fix aktualisieren. |
| `docs/architecture/SYSTEM_MAP.md` | Systemgrenzen und Integrationen. | Enthalt neue Analyse, aber noch widerspruechliche CTA-Hierarchie an einzelnen Stellen. |

## 3. Current Codebase Gap Analysis

| Gap | Why It Matters | Required Action |
|---|---|---|
| E3-Zahlen-Drift in Templates, Meta, Shortcodes und Docs. | Falsche Proof-Zahlen schwächen Vertrauen und koennen in Calls sofort auffallen. | `blocksy-child/inc/canon/e3-proof-canon.php` plus Lint; alle E3-Ausgaben auf Getter umstellen. |
| `readiness`-Namen in Pfaden und Feature-Flag. | Interne Semantik kann Agenten und spaetere PRs wieder in die alte Diagnose-Logik ziehen. | Kurzfristig dokumentieren; mittelfristig `request-analysis`-Contract und Flag einfuehren. |
| Analyse-App ist jetzt ein lokales 8-Schritt-Formular mit separatem Kontakt-Submit. | Der wichtigste Einstieg liefert eine erste Fit-Einordnung und kann nach Einwilligung einen CRM-/Mail-Kontakt erzeugen, aber noch keinen serverseitigen Befund. | Serverseitiges Scoring und neuer n8n-Contract bleiben Folgearbeiten; WordPress-REST-Submit ist aktiv. |
| Kein serverseitiges Scoring fuer Analyse. | Client-Scoring allein ist manipulierbar und nicht als Befundbasis sauber. | `blocksy-child/inc/request-analysis-scoring.php` und REST-Route planen. |
| Kein finaler Submit-Contract fuer neue Analyse-Semantik. | n8n kann nicht sauber, versioniert und privacy-konform angebunden werden. | `automations/n8n/data-models/request-analysis-payload.v1.contract.json` oder v2 nach Migrationsentscheidung. |
| Kein `docs/architecture/TRACKING.md`. | Funnel-Events werden sonst adhoc in Templates verteilt. | Tracking-Spec fuer Demo und Analyse anlegen. |
| Growth-Audit-System ist technisch weiter aktiv. | Alte Routen koennen Hauptfunnel und CTA-Logik verwässern. | Nicht entfernen, aber aus Primary-CTA und Customer-Copy entfernen; Legacy-Status klar dokumentieren. |
| Editor-Content ist nicht versioniert. | WP-Editor kann alte Zahlen und alte CTAs behalten, obwohl Repo korrekt ist. | WP-Editor-Audit als manuelle QA-Checkliste in Deploy-Abnahme aufnehmen. |
| Kontaktseite ist spaeter anzupassen. | `/kontakt/` kann warme Leads aufnehmen, darf aber nicht alter Hauptfunnel werden. | Nach Analyse-MVP als separater Copy-/Routing-PR. |

## 4. Target Information Architecture and Sitemap

| Target URL | Template / Router | Intent | Indexing | Primary CTA |
|---|---|---|---|---|
| `/` | `front-page.php` | Positionierung, Proof, Founding-Partner-Fit | Index | `/anfrage-system-analyse/` |
| `/energie-fahrplan-demo/` | `page-energie-fahrplan-demo.php`, `inc/energy-demo-page.php` | Showroom: Kaeufer erlebt Anfrageprozess und Ergebnis | Noindex, follow | `/anfrage-system-analyse/` |
| `/anfrage-system-analyse/` | `page-readiness-diagnose.php`, `inc/anfrage-system-analyse-page.php` | Evidenzbasierter Fit- und Marktcheck | Noindex, follow bis Angebot final ist | Formularabschluss / spaeter Zustell-Consent |
| `/anfrage/` | `page-anfrage.php`, `inc/anfrage-system-analyse-page.php` | Retired Warm-intent intake | 301 auf `/anfrage-system-analyse/` | Anfrage-System-Analyse |
| `/e3-new-energy/` | `page-case-e3.php` oder WP-Seite mit Template | Flagship-Proof | Index | Demo ansehen / Analyse starten |
| `/ergebnisse/` | Results-Hub | Proof-Sammlung | Index | E3 ansehen / Analyse starten |
| `/wordpress-agentur-hannover/` | `page-wordpress-agentur.php` | Money-/Legacy-SEO-Page | Index | Anfrage-System-Analyse statt Audit |
| `/growth-audit/` | Audit-Route | Legacy-/Sekundärpfad | // ANNAHME, zu verifizieren: Indexing live prüfen | Rückführung auf `/anfrage-system-analyse/` |

## 5. Redirect, Noindex, and Deletion Plan

| Old URL / Module | Target / Decision | Status | Reason |
|---|---|---:|---|
| `/readiness-diagnose/` | `/anfrage-system-analyse/` | 301 | Deutsche Analyse ersetzt Readiness-Frame. |
| `/anfrage/` | `/anfrage-system-analyse/` | 301 | Die evidenzbasierte Analyse ersetzt den alten warmen Intake. |
| `/energie-fahrplan-demo/` | Behalten | Noindex | Showroom-Asset, kein SEO-Landingpage-Ziel. |
| `/anfrage-system-analyse/` | Behalten | Noindex vorerst | Angebot/Form noch nicht final genug fuer SEO-Traffic. |
| `/growth-audit/` | Depriorisieren, nicht als Haupt-CTA | Pending | Bestehender Flow bleibt technisch real, aber nicht mehr Ladder-Stufe 1. |
| Legacy Audit-Slugs | Bestehende Redirects beibehalten | 301 | Kein neuer Growth-Hauptpfad. |
| `blocksy-child/readiness/` | Vorerst behalten | Internal alias | Build-Churn vermeiden; spaeter Rename nur mit eigenem PR. |
| `HU_FEATURE_READINESS_SUBMIT` | Vorerst behalten | Internal flag | Submit bleibt aus; Rename erst mit Contract-/Submit-PR. |

## 6. Schema.org Architecture

| URL / Template | Schema Types | Data Source | Implementation |
|---|---|---|---|
| `/` | `Organization`, `Service`, optional `FAQPage` | `inc/org-schema.php`, `inc/canon/*.php` | Keine hardcodierten E3-/Pricing-Werte in Schema. |
| `/e3-new-energy/` | `CaseStudy`-nahe Struktur ueber `Article`/`CreativeWork`, `FAQPage` falls sichtbar | Neuer E3-Canon | Proof-Zahlen nur ueber Getter, keine Editor-Duplikate. |
| `/energie-fahrplan-demo/` | Kein starkes SEO-Schema erforderlich | Route-Meta | Noindex; Schema minimal halten. |
| `/anfrage-system-analyse/` | Kein SEO-Schema vor Index-Freigabe | Diagnose-Canon | Noindex; keine Leistungsversprechen als Schema. |
| `/wordpress-agentur-hannover/` | `Service`, `FAQPage` | Pricing-/Messaging-Canon | Alte Agentur-Position nicht ausbauen, sondern auf Anfrage-System rueckbinden. |

## 7. CRO and Funnel Engineering

| Funnel Step | Field / Section | Type | Hard Rule / Behavior |
|---|---|---|---|
| Demo | Eingaben zum Haus-/Energie-Szenario | Interaktive React-Eingabe | Nutzer muss Prozess selbst bedienen koennen; kein Video-only-Beweis. |
| Demo | Ergebnisbuehne | Visual + PDF lokal | PDF wird lokal erzeugt; kein automatischer Versand im Demo-Pfad. |
| Demo | Prozesskarte | Visualisierung | Zeigt, was nach einem echten Submit passieren kann: Validierung, Routing, CRM, Follow-up. |
| Analyse Schritt 1 | Branche / Angebotsart | Pflicht | Nicht-ICP erzeugt gelb/rot, nicht harte Formularsperre. |
| Analyse Schritt 2 | Region | Pflicht | Ausserhalb DACH kann Nicht-Empfehlung ausloesen. |
| Analyse Schritt 3 | Angebot / Auftragswert | Pflicht | Niedriger Auftragswert senkt Fit. |
| Analyse Schritt 4 | Werbebudget | Pflicht | Unter 5.000 EUR/Monat blockiert Founding-Cohort-Empfehlung. |
| Analyse Schritt 5 | Website | Pflicht mit Ausnahme | Fehlende URL erlaubt Analyse, aber Befundtiefe wird eingeschraenkt. |
| Analyse Schritt 6 | Tracking | Ja/Nein/Weiss ich nicht | `Weiss ich nicht` wird rot bewertet, aber akzeptiert. |
| Analyse Schritt 7 | Anfrageprozess | Ja/Nein/Weiss ich nicht | Fehlender Prozess erzeugt rotes Prozesssignal. |
| Analyse Schritt 8 | Marktbild | Einschätzung | Leadkosten nur als Korridor, nie als Garantie. |
| Abschluss | Empfehlung | `fit_green`, `fit_yellow`, `fit_red` | Rot muss explizit Nicht-Empfehlung kommunizieren. |

## 8. API, CRM, Automation, Tracking, and Privacy Requirements

| Layer | File / System | Requirement |
|---|---|---|
| Frontend | `blocksy-child/readiness/src/App.tsx` | 8-Schritt-Form, lokale Validierung, kein PII im Default-Pfad, data-track-Hooks. |
| REST | `blocksy-child/inc/analysis-intake.php` | Submit nur bei Feature-Flag; Server validiert Kontaktblock, Signal, Score und Antworten. |
| Scoring | `blocksy-child/inc/request-analysis-scoring.php` | Serverseitige Fit-Scores und Ampeln; keine reine Client-Entscheidung. |
| Contract | `automations/n8n/data-models/request-analysis-payload.v1.contract.json` | Neue Analyse-Semantik; bestehender `readiness-diagnosis-payload.v1` bleibt bis Migration stabil. |
| n8n | `https://n8n.hasimuener.de/webhook/audit-consultation` | Nur mit bestehendem Webhook und `intake_variant`-Routing; kein neuer Webhook ohne Freigabe. |
| CRM | WordPress `nexus_contact` | Kein CRM-Write im lokalen Fragepfad; Kontakt-Step schreibt nach Einwilligung Analyse-Leads. |
| Tracking | `docs/architecture/TRACKING.md` | Definierte Events fuer Demo, Analyse, Founding-Block, Value-Vergleich. |
| Privacy | `docs/architecture/PRIVACY.md` | Cookie-Default bleibt; Consent direkt am Zustell-/Submit-Schritt, nicht pauschal ueber Banner. |

## 9. Code-Level Repo Action Plan

### 9.1 New Files

| File | Purpose | Priority |
|---|---|---:|
| `docs/architecture/TRACKING.md` | Single Source fuer Funnel-Events und data-track-Konvention. | P0 |
| `blocksy-child/inc/canon/e3-proof-canon.php` | Single Source fuer E3-Zahlen. | P0 |
| `scripts/lint-e3-canon.sh` | CI-/Predeploy-Schutz gegen alte E3-Zahlen. | P0 |
| `automations/n8n/data-models/request-analysis-payload.v1.contract.json` | Neuer Contract fuer Anfrage-System-Analyse. | P1 |
| `blocksy-child/inc/request-analysis-scoring.php` | Serverseitige Fit-/Ampel-Logik. | P1 |
| `blocksy-child/inc/request-analysis-api.php` | REST-Submit-Schicht hinter Feature-Flag. | P1 |
| `docs/specs/anfrage-system-analyse-report-v1.md` | Struktur des schriftlichen Befunds. | definiert |

### 9.2 Files to Rewrite

| File | Required Change |
|---|---|
| `blocksy-child/readiness/src/App.tsx` | 8-Schritt-Analyse mit lokaler Validierung und lokalem Ergebnis ist umgesetzt; Consent-/Submit-Stufe bleibt deaktiviert. |
| `blocksy-child/readiness/src/index.css` | Form-, Ampel-, Ergebnis- und mobile States sauber abbilden. |
| `blocksy-child/page-readiness-diagnose.php` | Tracking-, Root-IDs und Copy auf Anfrage-System-Analyse stabilisieren; Rename spaeter separat entscheiden. |
| `blocksy-child/front-page.php` | E3-Canon, Analyse-CTA, Founding-Partner-Frame konsolidieren. |
| `blocksy-child/page-anfrage.php` | Erledigt: retired Redirect-Template auf `/anfrage-system-analyse/`. |
| `blocksy-child/page-case-e3.php` | E3-Zahlen und Zeitraum korrigieren; Methodik und Beweisdichte erhoehen. |
| `blocksy-child/page-solar-waermepumpen-leadgenerierung.php` | E3-Zahlen-Drift und alte CTA-Microcopy bereinigen. |
| `blocksy-child/inc/helpers.php` | Public-Proof-Daten aus E3-Canon beziehen. |
| `blocksy-child/inc/shortcodes.php` | Hardcodierte Proof-Ausgaben auf Getter umstellen. |
| `docs/architecture/LIVE_STATUS.md` | Growth-Audit-/Proof-Drift nach Code-Fix entfernen. |
| `docs/architecture/SYSTEM_MAP.md` | CTA-Hierarchie final auf Analyse statt Growth Audit stellen. |

### 9.3 Files / Modules to Delete or Deactivate

| File / Module | Recommendation | Reason |
|---|---|---|
| Public `Tiefendiagnose`-Copy | Deaktivieren | Kein oeffentlicher Funnel-Schritt. |
| Growth-Audit als Primary-CTA | Deaktivieren | Widerspricht `AGENTS.md` und Ladder. |
| Hardcoded `-83 %`, `120 -> 20`, `~25 EUR`, `12 Monate` E3 | Ersetzen | Proof-Drift gegen Inhaber-Canon. |
| Direkte n8n-Webhook-Calls aus neuer Analyse-App | Nicht einfuehren | Webhook darf nicht im Browser liegen; REST-Schicht vorgeschaltet. |

## 10. ACF, CPT, and Data Model Requirements

| Field / CPT | Type | Location | Purpose |
|---|---|---|---|
| `hu_request_analysis` | // ANNAHME, zu verifizieren: private CPT erst bei Speicherung noetig | `blocksy-child/inc/request-analysis-api.php` oder separates CPT-Modul | Nur wenn Analyse-Submits im WP gespeichert werden sollen. |
| `client.industry` | enum | Contract + React state | ICP-Fit. |
| `client.employee_range` | enum | Contract + React state | Founding-Partner-Fit. |
| `client.plz_region` | string, regional begrenzt | Contract + React state | Markt- und Regionalbewertung ohne Volladresse. |
| `inputs.website_url` | URL | Contract + React state | Public-Sniff-Grundlage, keine Admin-Zugaenge. |
| `inputs.ad_budget_range` | enum | Contract + React state | Budget-Fit; unter 5.000 EUR/Monat blockiert Cohort-Empfehlung. |
| `inputs.tracking.*` | enum `yes/no/unknown` | Contract + React state | `unknown` ist gueltig und wird rot bewertet. |
| `inputs.crm.*` | enum `yes/no/unknown` | Contract + React state | Prozessreife. |
| `delivery.email` | email, optional | Separater Zustell-Consent | Nur wenn Befundzustellung per E-Mail gewuenscht ist. |
| `consent.text_hash` | string | Submit-Payload | Nachweis der konkreten Consent-Version. |

## 11. Template and Asset Loading Plan

| URL / Template | CSS | JS | Data Passed from PHP |
|---|---|---|---|
| `/anfrage-system-analyse/` via `page-readiness-diagnose.php` | `blocksy-child/readiness/dist/assets/*.css` | `blocksy-child/readiness/dist/assets/*.js` | Route, feature flags, consent version, tracking defaults. |
| `/energie-fahrplan-demo/` via `page-energie-fahrplan-demo.php` | `blocksy-child/energie-fahrplan/dist/assets/*.css` | `blocksy-child/energie-fahrplan/dist/assets/*.js` | Demo mode, CTA URL, no-submit flag. |
| `/` via `front-page.php` | Theme CSS | Theme JS | Founding/pricing/proof canons. |
| `/e3-new-energy/` | Theme CSS | Theme JS | E3 proof canon and schema data. |
| `/anfrage/` | keine eigenen Assets | keine eigene JS-Strecke | Legacy-Redirect auf `/anfrage-system-analyse/`. |

Asset-Regel: React-Funnel-Apps bleiben unter `blocksy-child/<funnel-name>/`, Build-Output unter `dist/`. `scripts/build-theme-dist.sh` ist die Build-Grenze und darf multi-funnel-faehig bleiben, ohne `.github/workflows/deploy.yml` anzufassen.

## 12. Implementation Sequence

| Sprint | Scope | Result |
|---|---|---|
| Sprint 1 | E3-Proof-Canon, Drift-Lint, Live/System-Docs bereinigen. | Trust-Risiko entfernt; Sales-Gespraeche nutzen konsistente Zahlen. |
| Sprint 2 | Analyse-Form als echte 8-Schritt-React-App bauen. | Erledigt repo-seitig am 2026-05-04: Nutzer erlebt qualifizierenden Prozess mit lokalem Ergebnis. |
| Sprint 3 | Serverseitiges Scoring und Report-Spec bauen. | Report-Spec definiert; serverseitige Scoring-Funktion bleibt offen. |
| Sprint 4 | Consent-UI und WordPress-REST-Schicht hinter Feature-Flag. | Teilweise erledigt: Kontakt-Submit schreibt CRM und triggert Transaktionsmails; serverseitiges Scoring und n8n-Contract offen. |
| Sprint 5 | n8n-Branch ueber bestehenden `audit-consultation`-Webhook pruefen. | Manuelle/automatisierte Weiterverarbeitung ohne neuen oeffentlichen Webhook. |
| Sprint 6 | Demo-Showroom haerten: Prozessvisualisierung, lokale PDF, CTA zur Analyse. | Interessent versteht aus Kaeufersicht, was umgesetzt wird. |
| Sprint 7 | Kontaktseite und warme Anfragepfade nachziehen. | Keine Nebenroute zieht zurueck in alten generischen Pitch. |

## 13. Acceptance Criteria

| Criterion | Must Be True |
|---|---|
| Strategy alignment | Primary CTA fuer kalten B2B-Traffic zeigt auf `/anfrage-system-analyse/`; Demo bleibt Showroom, nicht Leadmagnet. |
| SEO safety | `/anfrage-system-analyse/` und `/energie-fahrplan-demo/` sind `noindex, follow`; `/readiness-diagnose/` und `/anfrage/` leiten 301 weiter. |
| Proof integrity | Keine customer-facing Fundstelle nutzt E3 `-83 %`, `120 -> 20`, `~25 EUR` oder `12 Monate` fuer den finalen E3-Case. |
| Funnel integrity | Analyse kann Fit grün/gelb/rot ausdruecken und rot als Nicht-Empfehlung ausgeben. |
| API security | Kein n8n-Webhook steht im Analyse-Frontend; Submit laeuft ueber WP REST und Feature-Flag. |
| Privacy | Default-Fragepfad enthaelt keinen Klarnamen, keine E-Mail, keine Telefonnummer und keine Endkundendaten; Kontakt-Submit verarbeitet Name, Firma und E-Mail nur nach sichtbarer Einwilligung. |
| Tracking | Demo- und Analyse-Events sind in `docs/architecture/TRACKING.md` definiert und im Markup ueber `data-track-*` abbildbar. |
| Maintainability | Pricing, Founding, Messaging und E3-Proof kommen aus Canon-Dateien, nicht aus verstreuten Template-Strings. |
| Deploy safety | `scripts/build-theme-dist.sh` baut alle aktiven Funnel-Apps; `.github/workflows/deploy.yml` bleibt unveraendert. |

## 14. Direct Final Assessment

Der groesste technische und vertriebliche Risikopunkt ist nicht die React-App, sondern Proof- und Funnel-Drift: falsche E3-Zahlen, alte Growth-Audit-CTA-Logik und interne `readiness`-Begriffe koennen den neuen Founding-Partner-Pfad verwässern.

Der hoechste Hebel ist deshalb diese Reihenfolge:

1. E3-Proof-Canon bereinigen.
2. Serverseitiges Scoring und Report-Spec definieren.
3. Consent-/Submit-Grenze hinter Feature-Flag bauen.
4. Erst danach n8n und CRM anbinden.

Die richtige technische Architektur ist bereits angelegt: WordPress fuehrt SEO, Seiten und Canons; React fuehrt die Funnel-Erlebnisse; n8n verarbeitet erst nach Contract, Consent und Feature-Flag.
