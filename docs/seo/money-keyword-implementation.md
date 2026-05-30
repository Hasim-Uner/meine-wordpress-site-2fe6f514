# Money-Keyword-Fit und Implementierungsstrategie

Stand: 2026-04-06. Status-Korrektur: 2026-05-30.

> **Hinweis — teilweise überholt.** Dieses Dokument entstand vor dem Schwenk auf die
> Positionierung „Leadgenerierung für Solar-, Wärmepumpen- und Speicher-Anbieter". Die
> Keyword-Logik (Money- vs. Mid-Funnel- vs. schwache Keywords, Entscheidungsregel für neue
> URLs) bleibt als Methodik gültig. Mehrere konkrete Pfade und der Primärpfad sind aber
> nicht mehr aktuell. Maßgeblich ist `docs/architecture/LIVE_STATUS.md`.
>
> Aktuelle Zuordnung statt der alten Service-Slugs:
>
> | Alt im Dokument | Aktueller Stand |
> | --- | --- |
> | Primärpfad `/growth-audit/` | Marktcheck auf `/solar-waermepumpen-leadgenerierung/#marktcheck` |
> | `/wordpress-seo-hannover/` | `410 Gone`; SEO-Signale laufen über `/wordpress-agentur-hannover/` |
> | `/conversion-rate-optimization/` | `410 Gone`; CRO-Semantik liegt in Money- und Sub-Pages |
> | `/core-web-vitals/` | `410 Gone`; Performance bleibt technisches Fundament ohne eigene Money Page |
> | `/server-side-tracking-gtm/` | aktiv als `/server-side-tracking-b2b/` |
> | `/ga4-tracking-setup/`, `/performance-marketing/` | weiterhin aktive Service-Cluster |
>
> Neue primäre Money Pages: `/solar-waermepumpen-leadgenerierung/` (Solar-/SHK-Branche) und
> `/wordpress-agentur-hannover/` (lokaler B2B-Anker), flankiert vom Solar-SEO-Sub-Page-Cluster.

## Zielbild

Die Website verkauft nicht "beliebige Agenturleistungen", sondern ein audit-first System für B2B:

- lokale Nachfrage über Hannover und Region Hannover
- technisches Fundament aus SEO, Tracking und Performance
- Conversion-Logik statt Einzelleistung ohne Kontext
- klarer Primärpfad über den Marktcheck auf `/solar-waermepumpen-leadgenerierung/#marktcheck` (früher `/growth-audit/`)

Deshalb müssen Money Keywords nicht nur Suchvolumen haben, sondern zum bestehenden Angebots- und CTA-System passen.

## Kurzfazit zur vorgeschlagenen Liste

Die Liste ist in Teilen brauchbar, aber sie mischt drei Dinge, die getrennt werden müssen:

1. echte Money Keywords für Service- und Local Pages
2. Mid-Funnel- und Problem-Keywords für Pillar- oder Blog-Content
3. Formulierungen mit schwachem Demand-Signal oder unklarer Suchintention

Die stärkste gemeinsame Linie ist:

- `WordPress Agentur Hannover`
- `GA4 Tracking Setup` / `Server Side Tracking`
- `Conversion Rate Optimierung WordPress`
- `Technical SEO Audit`
- `WordPress Performance Optimierung`

Schwächer sind Keywords, die zu klein, zu breit oder zu künstlich formuliert sind:

- `SEO Agentur Pattensen`
- `CRO Agentur Niedersachsen`
- `Growth Architect Hannover`
- `Tracking und Leadgenerierung Hannover`
- `Beste WordPress Agentur nahe Hannover`

## Bewertung nach Fit

### Core Keywords

| Keyword / Cluster | Fit | Rolle | Zielseite |
| --- | --- | --- | --- |
| `WordPress Agentur Hannover` | hoch | lokale Money Page | `/wordpress-agentur-hannover/` |
| `Technical SEO Audit` | hoch | service-naher SEO-Cluster | `/wordpress-seo-hannover/` |
| `WordPress Performance Optimierung` | mittel bis hoch | technischer Service-Cluster | `/core-web-vitals/` |
| `GA4 Tracking Setup` | hoch | service-naher Tracking-Cluster | `/ga4-tracking-setup/` |
| `Server Side Tracking Google Tag Manager` | hoch | spezialisierter Tracking-Spoke | `/ga4-tracking-setup/` plus `/server-side-tracking-gtm/` |
| `Conversion Rate Optimierung WordPress` | hoch | service-naher CRO-Cluster | `/conversion-rate-optimization/` |

### Secondary Keywords

Diese Keywords passen, aber nicht als primäre Money Pages.

| Keyword / Cluster | Rolle | Empfohlenes Format |
| --- | --- | --- |
| `Leadgenerierung für B2B Website` | Mid-Funnel / Pillar | Cornerstone oder Blog-Bridge |
| `Ich bekomme keine Leads über meine Website` | Problem-Keyword | Blog- oder Audit-Entry |
| `Meine WordPress Site ist langsam` | Problem-Keyword | Blog-Bridge auf `/core-web-vitals/` |
| `Google Analytics 4 Setup Agentur` | semantische Variante | Subheadline, FAQ, Meta und Copy auf `/ga4-tracking-setup/` |
| `Heatmap Analyse Agentur` | taktischer Subtopic | FAQ / Supporting Section, nicht eigene Page |
| `Website Umsatz steigern` | sehr breit | nur in Thought Leadership / Proof Content |
| `Wer macht SEO in Pattensen?` | GEO-Frage | FAQ oder regionale Brücken-Copy, keine eigene Money Page |
| `Tracking für B2B Unternehmen Hannover` | GEO-Longtail | FAQ, H2 oder supporting copy auf Tracking-Seiten |

### Avoid oder umformulieren

| Keyword | Problem | Empfehlung |
| --- | --- | --- |
| `SEO Agentur Pattensen` | zu kleiner lokaler Markt, schwaches Demand-Signal | Hannover als Primärort beibehalten, Pattensen nur in Copy und FAQ |
| `CRO Agentur Niedersachsen` | unscharfer Geo-Raum, niedrige Kaufklarheit | auf `Conversion Rate Optimierung WordPress` fokussieren |
| `Growth Architect Hannover` | eher Eigenbegriff als Suchbegriff | als Positionierungsbegriff behalten, nicht als SEO-Ziel |
| `Tracking und Leadgenerierung Hannover` | zwei Intentionen in einem Keyword | Tracking und Leadgenerierung sauber trennen |
| `WordPress Agentur für kleine Unternehmen` | zieht eher kleinteilige Anfragen an | B2B-Mittelstand klarer benennen |
| `Beste WordPress Agentur nahe Hannover` | clickbaitig und schwer seriös zu bedienen | nur als FAQ-Semantik, nicht als Hauptkeyword |

## Empfohlene Fokusgruppe für die nächsten 90 Tage

Wenn auf drei Cluster reduziert werden muss, dann:

1. `WordPress Agentur Hannover`
2. `GA4 Tracking Setup` plus `Server Side Tracking`
3. `Conversion Rate Optimierung WordPress`

`Technical SEO Audit` bleibt ein Hochfit-Cluster, weil er bereits als kanonische Route vorhanden ist und eng mit der bestehenden Positionierung verzahnt ist. Er sollte gepflegt werden, aber nicht auf Kosten der drei kaufnäheren Cluster die Priorität stehlen.

## Seiten-Mapping im bestehenden System

### 1. `/wordpress-agentur-hannover/`

Primäre Rolle:

- lokale Money Page für WordPress + B2B + Hannover

Zu verarbeitende Keyword-Signale:

- `WordPress Agentur Hannover`
- `B2B WordPress Agentur Region Hannover`
- semantisch: Angebotsseiten, Tracking, Conversion, WordPress für B2B

Nicht auf diese Seite ziehen:

- `SEO Agentur Pattensen`
- `CRO Agentur Niedersachsen`

### 2. `/wordpress-seo-hannover/`

Primäre Rolle:

- technischer SEO-Service-Einstieg

Zu verarbeitende Keyword-Signale:

- `Technical SEO Audit`
- `WordPress SEO Hannover`
- semantisch: Crawlability, interne Verlinkung, kaufnahe Seitentypen

### 3. `/ga4-tracking-setup/`

Primäre Rolle:

- Messbarkeit, Consent, Event-Logik, serverseitige Signale

Zu verarbeitende Keyword-Signale:

- `GA4 Tracking Setup`
- `Google Analytics 4 Setup Agentur`
- `Server Side Tracking Google Tag Manager`

Nur bedingt passend:

- `Google Ads Tracking einrichten`

Diese Formulierung sollte nur dann prominent gespielt werden, wenn Ads-Tracking wirklich als öffentliche Kernleistung verkauft wird. Sonst zieht sie Nutzer mit engerem Ads-Setup-Intent an als die Seite aktuell verspricht.

### 4. `/conversion-rate-optimization/`

Primäre Rolle:

- CRO- und Seitenführungs-Cluster

Zu verarbeitende Keyword-Signale:

- `Conversion Rate Optimierung WordPress`
- semantisch: CTA-Logik, Landingpage-Optimierung, Anfragepfad

Sekundäre Semantik:

- `Leadgenerierung für B2B Website`

Diese Semantik darf in Copy und FAQ vorkommen, sollte aber kein eigenständiges Hauptkeyword dieser URL werden.

### 5. `/core-web-vitals/`

Primäre Rolle:

- Performance als technisches Fundament

Zu verarbeitende Keyword-Signale:

- `WordPress Performance Optimierung`
- Problem-Sprache wie `Meine WordPress Site ist langsam`

## Bereits vorhandene Content-Assets, die in die Strategie passen

Diese Entwürfe können direkt als Cluster-Unterstützung genutzt werden:

- `content/blog-drafts/privacy-first-measurement-b2b.md`
- `content/blog-drafts/seo-sichtbarkeit-cornerstone.md`
- `content/blog-drafts/wordpress-seo-keine-anfragen.html`

Empfohlene Rolle:

- `privacy-first-measurement-b2b` stärkt den Tracking-Cluster
- `technisches-seo-performance-fundament` verbindet SEO, Tracking und Paid-Qualität
- `wordpress-seo-keine-anfragen` greift das Problem-Keyword "keine Leads" auf und führt in Audit, SEO und Agentur-Seite

## Implementierungsstrategie

### Phase 1: Money Pages schärfen

Ziel:

- keine neuen Seiten bauen, bevor die vorhandenen Cluster klar geschliffen sind

Maßnahmen:

- H1, Hero, erste 150 Wörter und FAQ jeder Money Page auf genau einen primären Cluster ausrichten
- lokale Semantik auf `/wordpress-agentur-hannover/` konsistent auf `Hannover` und `Region Hannover` halten
- Tracking-Seite stärker auf `GA4 Tracking Setup`, `Google Analytics 4 Setup` und `Server Side Tracking` schieben
- CRO-Seite klarer mit `WordPress` und `B2B` verbinden
- SEO-Seite auf `Technical SEO Audit` und `WordPress SEO Hannover` fokussiert halten

### Phase 2: Supporting Content veröffentlichen

Ziel:

- Problem- und Mid-Funnel-Traffic auf bestehende Money Pages zuführen

Maßnahmen:

- `privacy-first-measurement-b2b` veröffentlichen und intern auf `/ga4-tracking-setup/` verlinken
- `wordpress-seo-keine-anfragen` veröffentlichen und intern auf `/wordpress-seo-hannover/`, `/wordpress-agentur-hannover/` und `/growth-audit/` verlinken
- `technisches-seo-performance-fundament` weiter als Pillar zwischen SEO, Performance und Audit einsetzen

### Phase 3: GEO- und Proof-Layer ausbauen

Ziel:

- Generative Engines und lokale Suchsignale mit echten Belegen füttern

Maßnahmen:

- pro Money Page 3 bis 5 echte Frageformulierungen als FAQ einbauen
- messbare Proof-Sätze auf Service- und Proof-Seiten konsistent halten
- LocalBusiness-, FAQ- und Service-Schema nur dort ausgeben, wo die Seite wirklich Angebots-Intent hat
- Hannover und Region Hannover in Proof, Über-mich, Kontakt und Agentur-Seite konsistent verbinden

## GEO-Regeln

Für KI-Suche und generative Antworten gelten im bestehenden Setup diese Regeln:

- keine künstlichen Frage-Seiten für jedes Mikro-Ortsteil bauen
- lieber echte Fragen als FAQ auf starken Money Pages und Pillars integrieren
- konkrete Zahlen und Ergebnisse vor generischem Werbetext priorisieren
- Begriffe wie `Growth Architect` als Differenzierung nutzen, aber nicht als primäres Keyword forcieren

Geeignete Frageformate:

- Welche WordPress Agentur in Hannover hilft bei Leadgenerierung für B2B?
- Wie richtet man ein sauberes GA4 Tracking Setup für eine B2B-Website ein?
- Warum rankt eine WordPress-Seite, liefert aber keine Anfragen?
- Wann lohnt sich Server Side Tracking mit GTM wirklich?

## Repo

Empfohlene Repo-Arbeit:

- Meta-, Hero- und FAQ-Schärfung auf den bestehenden Clusterseiten
- interne Verlinkung von Pillar- und Problem-Content auf Money Pages
- bestehende Drafts in den Content-Plan für Tracking, SEO und CRO überführen

## Manual WordPress

Im WordPress-Admin prüfen:

- ob die kanonischen Clusterseiten wirklich die versionierten Templates rendern
- ob Editor-Content, Excerpts oder Rank-Math-Felder die repo-seitige Keyword-Führung verwässern
- ob FAQs, Featured Snippets und lokale Hinweise konsistent sind

## Operational

Außerhalb des Repos:

- lokale Referenzen und Backlinks aus Hannover / Region Hannover aufbauen
- Case Studies mit belastbaren Zahlen öffentlich nutzbar machen
- Search Console und SEO Cockpit monatlich für Query-Mapping und Kannibalisierung prüfen

## Entscheidungsregel für neue Keywords

Ein neues Keyword wird nur dann zu einer neuen URL, wenn alle drei Punkte erfüllt sind:

1. klare kommerzielle oder strategische Suchintention
2. kein Konflikt mit einer bestehenden Clusterseite
3. eigener Proof, eigener Use Case oder eigene Seitenrolle

Wenn nur ein Themenwinkel, eine Ortsvariante oder eine FAQ-Erweiterung vorliegt, wird keine neue URL gebaut. Dann wird die bestehende Clusterseite vertieft.
