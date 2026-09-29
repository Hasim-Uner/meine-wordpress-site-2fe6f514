# SEO-Cockpit Gap Analysis — Claude Audit vs. aktueller Stand

**Stand:** 2026-09-29  
**Vergleichsbasis:** `seo-audit-2026-09-26.md` vs. aktueller Repo-Stand `40f2f6f` plus die offenen Verbesserungs-PRs #488 und #489.

## Zweck

Der Audit vom 26.09. bleibt eine historische Messung. Er darf nicht ungeprüft als aktueller Zustand weiterverwendet werden. Diese Gap Analysis trennt deshalb vier Ebenen:

1. **historischer Befund** — was am 26.09. tatsächlich gemessen wurde,
2. **aktuelle Systemfähigkeit** — was das SEO Cockpit heute selbst messen kann,
3. **Interpretation** — welche Aussage weiterhin plausibel ist,
4. **Systemmaßnahme** — was im Cockpit fehlt, damit die Aussage künftig automatisch überprüfbar wird.

Das Ziel ist kein maximal großes Dashboard, sondern ein Cockpit, das zwischen Daten, Evidenz, Diagnose und Entscheidung unterscheidet.

## Executive Summary

Der Audit vom 26.09. war für die damalige Situation fachlich nützlich, aber mehrere seiner technischen Lücken sind inzwischen überholt. Das heutige Cockpit besitzt bereits deutlich mehr eigene Messschichten: Search Console, URL Inspection, Site Audit, Sitemap-/robots-Prüfung, interne Linkarchitektur, CrUX-Diagnostik, DataForSEO Market Intelligence sowie Lead-/CRM-Signale.

Die größte verbleibende Architekturfrage ist deshalb nicht mehr „Welche Daten fehlen?“, sondern:

> **Welche Evidenz beantwortet welche Entscheidung — und wie verhindern wir, dass fehlende Daten als Gesundheit interpretiert werden?**

Daraus folgen zwei unmittelbar umgesetzte Korrekturen:

- **PR #488 — Audit Truth:** technischer SEO-Score, explizite Score-Abdeckung und CrUX als separate Felddaten-Evidenz.
- **PR #489 — Authority Pulse:** Domain-Level-Backlink-Evidenz aus DataForSEO, weil der Audit externe Autorität als möglichen Hauptengpass identifiziert hatte.

## Befundmatrix

| Thema | Claude-Audit 26.09. | Aktuelles Cockpit / Repo | Status / Konsequenz |
|---|---|---|---|
| GSC-Suchrealität | 90 Tage GSC, Seiten × Query, 15 URL Inspections | GSC-Snapshots, Zeitvergleich, Seiten-/Query-Ebene, URL-Inspection-Queue | **operationalisiert** |
| Positionierung | Google verbindet Domain stark mit Solar-/Portal-Themen | Market Intelligence + GSC können Nachfrage und aktuelle organische Nachbarschaft wiederholt prüfen | **strategische Hypothese weiter messen, nicht einfrieren** |
| WordPress-Sichtbarkeit | neue WordPress-Positionierung hatte kaum Sichtbarkeit | Ranked Keywords, Keyword Overview, Live SERPs, strategische Wettbewerber, Opportunity Engine | **operationalisiert** |
| Core Web Vitals | im damaligen gsc-wizard nicht verfügbar | eigene CrUX-Origin-Diagnostik inkl. Historie vorhanden | **technisch vorhanden; mit PR #488 direkt im Site Audit sichtbar** |
| Audit-Score | damaliger Bericht bewertet Technik überwiegend positiv | aktueller Site Audit normalisiert über gemessene Kategorien; CWV war aus dem Score ausgeschlossen | **Semantik korrigiert in PR #488: Technischer SEO-Score + Score-Abdeckung** |
| robots / Sitemap | live geprüft | eigener robots-/Sitemap-Check inkl. rekursiver Sitemap-Auswertung | **operationalisiert** |
| Canonical / Indexierung | Kernseiten geprüft | technischer Crawl + zentrale Indexierungsregel + Google URL Inspection + Canonical-Diagnostik | **deutlich stärker als 26.09.** |
| Interne Architektur | Solar-Seiten erhielten historisch viel interne Stärke | Crawl-Tiefe, eingehende Links, interne relative Authority/PageRank-artiges Signal, Rollen | **Messung vorhanden; tatsächliche Verteilung regelmäßig neu bewerten** |
| Broken Internal Links | punktuelle technische Prüfung | Audit erkennt kaputte bekannte Ziele und Links auf bekannte Redirects | **Teilabdeckung; unbekannte interne Linkziele müssen als Coverage-Lücke sichtbar/prüfbar werden** |
| Schema | punktuelle On-Page-Hinweise | JSON-LD-Syntax plus semantische Schema-Hinweise | **besser; formale Seitentyp-Verträge bleiben P2** |
| DataForSEO Keywords | 50 Keywords + 5 Live-SERPs | wöchentliche Market Intelligence + manuelle Live Watch + strategische Wettbewerber | **operationalisiert** |
| Backlink-Autorität | 25 Referring Domains, geringe Autorität als Engpasshypothese | auf `main` bisher kein eigener Backlink-Summary-Layer | **Lücke wird mit PR #489 geschlossen** |
| Conversions pro Landingpage | im Audit nicht verfügbar | Audit-CRM + Nexus CRM liefern Lead-/Kontakt-/Kampagnenkontext; Opportunity Layer verbindet Search- und Business-Signale | **Business-Evidenz heute deutlich stärker; kein GA4-Zwang für diese Fragestellung** |
| Permalink-Drift | `%author%`-Phase als konkrete Ursache des Einbruchs identifiziert | Permalink-/Routing-Verträge und CI-Smokes vorhanden | **historischer Incident, nicht als aktuelles Dauerproblem behandeln** |
| Authority-/Content-Entscheidung | manuelle Schlussfolgerung aus Audit | Opportunity Engine verbindet Markt, GSC, Intent und Lead-Signale | **Entscheidungslogik vorhanden; Authority-Evidenz fehlte bislang** |

## Zielarchitektur: fünf Wahrheiten statt ein Mega-Score

### 1. Crawl Reality

Frage: **Ist die Website technisch konsistent erreichbar und steuerbar?**

Quellen:

- interner Audit-Crawler
- HTTP-Status / Redirects
- Canonicals
- Robots
- Sitemap
- Schema
- interne Linkarchitektur

Output:

- Technischer SEO-Score
- Score-Abdeckung
- technische Findings
- Crawl-/Linkgraph-Evidenz

### 2. Search Reality

Frage: **Wie behandelt Google die Website tatsächlich?**

Quellen:

- Google Search Console
- URL Inspection

Output:

- Klicks, Impressionen, CTR, Position
- Gewinner / Verlierer
- Google Canonical vs. User Canonical
- Coverage-/Fetch-Probleme
- Query-/Page-Zuordnung

### 3. Experience Reality

Frage: **Wie erlebt reale Chrome-Nutzung die Site?**

Quelle:

- Chrome UX Report

Output:

- LCP
- INP
- CLS
- diagnostisch TTFB
- Messstatus / Sample vorhanden oder nicht vorhanden

Wichtig: Fehlende CrUX-Stichprobe ist **weder grün noch rot**.

### 4. Market Reality

Frage: **Wo existieren Nachfrage, Konkurrenz und externe Autorität?**

Quellen:

- DataForSEO Labs
- Live SERPs / Maps
- DataForSEO Backlinks

Output:

- Suchvolumen / Intent / Difficulty
- eigene Rankings
- aktuelle organische Wettbewerber
- strategische Vergleichsgruppe
- Authority Pulse
- später: detaillierter Link Gap

### 5. Business Reality

Frage: **Welche Sichtbarkeit erzeugt reale Geschäftssignale?**

Quellen:

- Audit-CRM
- Nexus CRM
- Kampagnen-/UTM-Zuordnung

Output:

- Leads
- CRM-Kontakte
- gewonnene Kontakte / Abschlüsse, soweit vorhanden
- Landingpage-/Kampagnenbezug

## Entscheidungslogik

Das Cockpit sollte keine Datensilos nebeneinanderstellen, sondern Findings in einer gemeinsamen Entscheidungsstruktur normalisieren.

Empfohlenes Finding-Schema:

```text
id
source
scope
observed_at
freshness
finding
severity
confidence
seo_impact
business_impact
effort
evidence
recommended_action
status
```

Dabei bleiben Messwerte und Bewertungen getrennt. Beispiel:

- **Messwert:** 25 Referring Domains.
- **Kontext:** strategische Vergleichsdomains besitzen deutlich mehr belastbare Referring Domains.
- **Diagnose:** externe Autorität ist eine plausible Limitation.
- **Aktion:** hochwertige relevante Erwähnungen/Links priorisieren.
- **Nicht zulässig:** „Authority Score 37/100“ ohne transparentes, validiertes Modell.

## Command-Center-Fragen

Das Dashboard sollte in dieser Reihenfolge Antworten liefern:

1. **Was ist kaputt oder widersprüchlich?**
2. **Was hat sich seit dem letzten belastbaren Snapshot verändert?**
3. **Wo existiert reale Suchnachfrage, die zum Geschäft passt?**
4. **Welche URL besitzt oder verliert diese Nachfrage?**
5. **Ist der Engpass Technik, Content/Intent, interne Architektur oder externe Autorität?**
6. **Welche Maßnahme hat bei vertretbarem Aufwand den höchsten erwartbaren Business-Nutzen?**

Damit wird das Cockpit von einer Sammlung von SEO-Kennzahlen zu einem Diagnose- und Priorisierungssystem.

## Prioritäten nach diesem Durchgang

### P1 — Audit Truth

PR #488:

- `health_score` kompatibel erhalten
- fachlich als `technical_seo_score` ausweisen
- `score_coverage` speichern und anzeigen
- CrUX im Site Audit als separate Felddaten-Evidenz zeigen
- keine künstliche Vermischung von URL-Crawl und Origin-CWV

### P1 — Authority Pulse

PR #489:

- DataForSEO `backlinks/summary/live`
- Domain Rank, Backlinks, Broken Backlinks
- Referring Domains / Main Domains
- Nofollow-Zähler
- IP-/Subnet-Diversität
- Provider-Spam-Metriken
- Verlauf im Market-History-Snapshot
- CSV-Export
- kein erfundener Cockpit-Authority-Score

### P2 — Audit Coverage / unbekannte Linkziele

Der aktuelle Broken-Link-Check kennt den Status eines Linkziels nur sicher, wenn das Ziel bereits im Audit-Inventar liegt. Diese Grenze muss als Messabdeckung sichtbar werden und anschließend über eine begrenzte, asynchrone Linkziel-Prüfung geschlossen werden.

Anforderungen an eine spätere Umsetzung:

- unbekannte interne Ziele deduplizieren
- Quellen je Ziel speichern
- kleine Hintergrundbatches
- keine langen Netzwerk-Loops im Option-Save-Filter
- Redirect-Kette und Loop erkennen
- 4xx/5xx und Fetch-Fehler unterscheiden
- Coverage im UI ausweisen

### P2 — formale Schema-Verträge

JSON-Syntax und heuristische Typ-Hinweise reichen nicht für die wichtigsten Money-/Entity-Seiten. Für definierte Seitentypen sollten erwartete `@type`, `@id`-Beziehungen und Pflichtfelder als Repo-Vertrag prüfbar werden.

### P2 — Performance-/Accessibility-Regressions

CrUX ist Feldbeobachtung und eignet sich nicht als deterministischer Deploy-Blocker. Separat sinnvoll:

- Accessibility-Smokes für Kernflows
- Asset-Budgets für CSS/JS
- planmäßige Lab-Checks auf Kernrouten
- CrUX-Trend als Feldrealität

## Was aus dem Audit vom 26.09. nicht blind weitergetragen werden darf

- die damalige CrUX-Lücke — die Integration existiert inzwischen;
- damalige Keywordpositionen als aktueller Zustand;
- damalige Referring-Domain-Zahl als aktueller Zustand;
- die Permalink-Krise als noch aktives Problem;
- die Aussage „keine Conversiondaten“, weil heute eigene CRM-/Lead-Layer existieren;
- Empfehlungen für neue Seiten allein aus Suchvolumen.

Die historischen Zahlen bleiben wichtig, aber nur als Baseline.

## Definition of Done für ein belastbares SEO Cockpit

Das Cockpit ist nicht „perfekt“, wenn es möglichst viele APIs integriert. Es ist belastbar, wenn:

- jede Kennzahl ihre Quelle, Zeit und Reichweite erkennen lässt;
- fehlende Daten explizit als fehlend erscheinen;
- ein Score nur das behauptet, was er tatsächlich misst;
- technische, Such-, Experience-, Markt- und Business-Evidenz getrennt bleiben;
- Diagnosen aus mehreren Evidenzquellen ableitbar sind;
- alte Snapshots nicht als Gegenwart missverstanden werden;
- Providerfehler und Teilabdeckung sichtbar bleiben;
- automatische Calls budgetiert und begrenzt sind;
- jede priorisierte Maßnahme auf nachvollziehbare Evidenz zurückgeführt werden kann.

Das ist der Architekturmaßstab für die nächsten Cockpit-Durchgänge.
