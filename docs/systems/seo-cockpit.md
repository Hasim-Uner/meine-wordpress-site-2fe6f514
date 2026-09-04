# SEO Cockpit

Stand: 2026-09-04.

Diese Doku beschreibt den repo-seitigen Startpunkt für ein internes SEO-Dashboard im WordPress-Admin.

## Ziel

Das Cockpit soll keine neue externe Plattform einführen.

Stattdessen:

- WordPress bleibt die operative Schicht
- Google Search Console liefert die externe SEO-Sicht
- Chrome UX Report liefert im Research-Layer reale Web-Performance-Felddaten
- IndexNow meldet neue, geänderte und gelöschte URLs an teilnehmende Suchmaschinen
- Koko Analytics liefert optional die lokale Traffic-Sicht
- das interne Audit-CRM liefert jetzt zusätzlich Lead- und Attributionssignale
- das Repo enthält die Logik, das Caching und die Admin-Oberfläche

## Aktueller Scope

Repo-seitig vorhanden:

- Top-Level-Admin-Menü `SEO Cockpit`
- visuelles Dashboard V3 als Command Center
- eigenes Untermenü `SEO Cockpit -> Research` für externe Primärdaten
- kompaktes Snapshot-Widget im Standard-WordPress-Dashboard
- priorisierte Queue im Admin, die SEO-Signale jetzt gegen Business-Wert, Funnel-Nähe und Confidence gewichtet
- Revenue Command Center V1 mit Today Revenue Queue für Anfrage-/Umsatzwirkung statt Traffic-Priorisierung
- Lead-Layer aus `nexus_review_request` mit Audit-Leads, Status, Source-Mix und intern attribuierten Seiten
- minimaler Status-Layer für Revenue-Queue-Einträge über WordPress-Option, ohne neue DB-Tabelle
- Settings-Seite für
  - Search-Console-Property
  - Google Client ID
  - Google Client Secret
  - Cache-Fenster
- OAuth-Flow gegen Google mit Redirect auf `admin-post.php`
- Token-Speicherung und Token-Refresh
- gecachte Dashboard-Abfragen für
  - Klicks
  - Impressionen
  - CTR
  - durchschnittliche Position
  - Top Pages
  - Top Queries
  - Device-Split
- Research-Intelligence-Layer V1 mit
  - Chrome UX Report API als erster Primärdaten-Provider
  - Origin-Level-Daten für Mobil und Desktop
  - LCP, INP, CLS und TTFB am p75
  - CrUX-History mit bis zu 40 Wochenpunkten beziehungsweise ungefähr sechs Monaten
  - eigenem Cache und manuellem Refresh
  - API-Key aus WordPress-Option oder bevorzugt `NEXUS_CRUX_API_KEY`
  - sichtbarer Provider-Roadmap für Destatis GENESIS, Eurostat und Energy-Charts ohne vorgetäuschte Anbindung
- heuristische Insight-Typen für
  - Quick Wins
  - CTR-Chancen
  - Decay
  - Snippet-Schwächen
  - Kannibalisierung
  - Money-Page-Unterperformance
  - Orphan-/Bridge-/Indexierungs-Signale
- Sitewide-Linkgraph aus dem repo-eigenen Standard-Header statt aus einem
  möglicherweise veralteten WordPress-Menü; die Detailansicht zeigt zusätzlich
  die statischen Shell-Tracking-Hooks einschließlich `nav_menu_toggle`
- browserseitige Lead-Attribution für neue Audit-Leads über
  - Formular-Landingpage
  - ersten internen Einstieg der Session
  - vorherige interne Seite
  - Referrer-URL
- automatischer Snapshot-Refresh per WP-Cron (`twicedaily`)
- optionale Erkennung des Plugins `koko-analytics/koko-analytics.php`
- IndexNow-Control-Layer mit
  - runtime-generiertem Ownership-Key in WordPress-Optionen
  - Root-Keydatei `/{key}.txt` für die Host-Verifikation
  - manuellem `URL jetzt melden`-Button
  - direktem IndexNow-Button auf URL-Drilldowns
  - automatischen Meldungen für Publish, Update und Delete öffentlicher Inhalte
  - asynchroner Ausführung automatischer Meldungen per Single WP-Cron Event
  - lokalem Verlauf der letzten 50 Meldungen
  - eigenem Untermenü `SEO Cockpit -> IndexNow`

## Research Intelligence V1

Research Intelligence ist bewusst ein eigenes Admin-Untermenü und kein weiterer Block im bereits dichten Command Center. V1 bindet nur CrUX an; die übrigen Datenquellen sind sichtbar als Roadmap markiert, werden aber nicht abgefragt und erzeugen keine Kennzahlen.

Code-Orte:

- `blocksy-child/inc/seo-cockpit/seo-cockpit-research.php`
- `blocksy-child/assets/css/seo-cockpit-research.css`
- Loader: `blocksy-child/inc/seo-cockpit/seo-cockpit.php`

CrUX-Vertrag:

- Endpoint aktuell: `https://chromeuxreport.googleapis.com/v1/records:queryRecord`
- Endpoint Verlauf: `https://chromeuxreport.googleapis.com/v1/records:queryHistoryRecord`
- Methode: `POST`
- Granularität: Origin, getrennt nach `PHONE` und `DESKTOP`
- Metriken: `largest_contentful_paint`, `interaction_to_next_paint`, `cumulative_layout_shift`, `experimental_time_to_first_byte`
- Verlauf: `collectionPeriodCount = 40`
- Current-Cache: 6 Stunden
- History-Cache: 12 Stunden
- kein Frontend-Request; Abruf nur im Admin/Background-Kontext

Credential-Vertrag:

- bevorzugt Runtime-Konstante `NEXUS_CRUX_API_KEY`
- alternativ WordPress-Option `nexus_seo_cockpit_research_settings`
- der Key wird nie ins Repo geschrieben
- ein leer gesendetes Key-Feld überschreibt einen bereits gespeicherten Key nicht
- `Key entfernen` löscht nur den WordPress-Option-Wert; eine Runtime-Konstante bleibt maßgeblich

UI-Vertrag:

- View-Capability reicht zum Lesen der Research-Seite
- Manage-Capability ist für Key-Speicherung und manuellen Refresh erforderlich
- API-Fehler und fehlende CrUX-Abdeckung werden sichtbar als fehlende Daten ausgegeben, nicht als Nullwerte
- TTFB wird als Diagnosemetrik gekennzeichnet; LCP, INP und CLS als Core Web Vitals
- der Trend vergleicht den ersten mit dem letzten numerischen p75-Wert der verfügbaren History-Serie; niedrigere Werte werden positiv dargestellt

## Revenue Command Center V1

Das Command Center ist Admin-only und sitzt innerhalb des SEO Cockpits. Es nutzt keine neuen externen Abhängigkeiten und erzeugt keine Fake-Metriken. Wenn Search Console fehlt, werden nur CRM- und Manual-Checks angezeigt.

Sektionen:

- `Heute zuerst`: Top 5 aktive Aktionen mit höchstem Revenue-Potenzial
- `Lead-Follow-up`: neue, offene, progressed, won und nicht attribuierte Marktcheck-Anfragen
- `Page Revenue Queue`: URLs mit SEO-Signal, Funnel-Wert oder Lead-Attribution
- `Conversion Leaks`: funnelnahe Seiten mit Sichtbarkeit, aber ohne Lead-Signal oder mit schwacher CTA-/Proof-Brücke
- `Manual Checks`: Search Console, Tracking, WordPress Admin und Attribution nur bei fehlender oder schwacher Datenbasis

Die Queue zeigt pro Eintrag Priorität, Typ, URL/Lead, Funnel-Rolle, Problem, Warum-jetzt, konkrete nächste Aktion, erwarteten Hebel, Aufwand, Risiko, Repo-/Manual-Fixbarkeit, Status, Confidence und Datenbasis.

Revenue Score:

- Lead-Signal und Funnel-Nähe werden stärker gewichtet als Impressionen.
- Business-Wert unterscheidet Marktcheck, Money Pages, Proof, Cluster, Blog/Hub, Legacy und Utility.
- Search Demand ist gedeckelt, damit Traffic allein nicht nach oben rutscht.
- Conversion Gap wird gesetzt, wenn funnelnahe Seiten Sichtbarkeit, aber keine Lead-Signale haben.
- Assist-Wert nutzt Entry-/Previous-/Landing-Attribution aus dem Audit-CRM.
- Proof-Nähe stärkt E3-/Ergebnis-nahe URLs.
- Decay, Confidence und Risk Penalty verhindern blinde Repo-Änderungen.

Status-Layer:

- Statuswerte: Neu, Heute, In Arbeit, Erledigt, Ignorieren.
- Speicherung: `nexus_revenue_command_center_statuses`.
- Capability: `manage_seo_cockpit` oder `manage_options`.
- Nonce: `nexus_revenue_command_center_status`.

## IndexNow-Control-Layer

IndexNow ist bewusst getrennt von Google Search Console und von Bing Places for Business.
Es braucht keinen Bing-OAuth-Flow. Der Host-Besitz wird über eine Runtime-Keydatei
auf der eigenen Domain nachgewiesen. Der Key wird nicht im Repo gespeichert.

Code-Orte:

- `blocksy-child/inc/seo-cockpit/seo-cockpit-indexnow.php`
- `blocksy-child/assets/css/seo-cockpit-indexnow.css`
- Loader: `blocksy-child/inc/seo-cockpit/seo-cockpit.php`

Runtime-Optionen:

- `nexus_seo_cockpit_indexnow_key`
- `nexus_seo_cockpit_indexnow_auto`
- `nexus_seo_cockpit_indexnow_history`
- `nexus_seo_cockpit_indexnow_last_result`
- `nexus_seo_cockpit_indexnow_rewrite_version`

Submission-Vertrag:

- Endpoint: `https://api.indexnow.org/indexnow`
- Methode: `POST`
- Payload: `host`, `key`, `keyLocation`, `urlList`
- nur URLs mit exakt demselben Host wie `home_url()` werden akzeptiert
- HTTP `200` und `202` gelten als erfolgreich angenommen
- `400`, `403`, `422`, `429` werden im lokalen Verlauf als Fehler gespeichert
- eine erfolgreiche Meldung ist keine Garantie für Crawling oder Indexierung

Automatik:

- Standard: aktiv
- öffentliche Posts/Pages werden bei Publish und Update vorgemerkt
- gelöschte, zuvor veröffentlichte URLs werden ebenfalls vorgemerkt
- Speichern im Editor blockiert nicht auf die externe API
- der Request läuft ca. 20 Sekunden später als Single WP-Cron Event
- ein 5-Minuten-Debounce verhindert doppelte Meldungen derselben URL/Quelle

Frontend-Footprint:

Das Cockpit bleibt grundsätzlich Admin-/Background-only. Nur das IndexNow-Modul
wird vor dem Frontend-Early-Return geladen, damit die Root-Keydatei ausgeliefert
werden kann. Es rendert keine sonstige Frontend-UI und führt keine API-Requests
bei normalen Seitenaufrufen aus.

## Nicht im Repo verifiziert

- echte Google-OAuth-Credentials
- echte Search-Console-Property-Verbindung
- echtes Refresh-Token
- installierte Koko-Analytics-Instanz
- produktiver `NEXUS_CRUX_API_KEY` beziehungsweise gespeicherter CrUX-Key
- echte CrUX-Antwort für `hasimuener.de` nach Deploy
- Live-Erreichbarkeit der generierten IndexNow-Keydatei nach dem ersten Deploy
- erste echte IndexNow-Antwort auf der Produktionsdomain

## Architektur

Code-Orte:

- `blocksy-child/inc/seo-cockpit/seo-cockpit.php`
- `blocksy-child/assets/css/seo-cockpit-admin.css`
- `blocksy-child/inc/seo-cockpit/seo-cockpit-dashboard-v3.php`
- `blocksy-child/inc/seo-cockpit/seo-cockpit-research.php`
- `blocksy-child/inc/seo-cockpit/seo-cockpit-indexnow.php`

Wichtige technische Entscheidungen:

- kein schweres SEO-Dashboard-Plugin
- keine externe Datenbank oder SaaS für die Dashboard-Logik
- API-Zugriff direkt per `wp_remote_*`
- leichter Cache über WordPress-Transients
- letzter Sync-Status in WordPress-Optionen für Admin-Sichtbarkeit
- Koko nur optional als zweiter Traffic-Layer
- Audit-CRM als dritter Datenlayer für Lead-Kontext und Priorisierung
- Revenue Command Center als vierter operativer Layer für Today Queue, Lead-Follow-up, Page Queue, Conversion Leaks und Manual Checks
- Research Intelligence als getrennte Primärdaten-Schicht; V1 nur CrUX, keine vorgetäuschten Provider-Daten
- IndexNow als eigener Indexing-Control-Layer ohne Google- oder Bing-Account-Credentials

## CSV-Exportvertrag

Der Admin-Export enthält zwei explizit getrennte Zeilentypen:

- `query_page`: Query×URL-Metriken für Ownership, Gap- und Drift-Auswertungen
- `page_total`: vollständige URL-Summen, auch wenn Search Console einzelne Queries anonymisiert

`period_presence` unterscheidet `both`, `current_only` und `previous_only`.
Der Export bildet dafür die Union beider Perioden. Vollständig verschwundene
Query×URL-Paare bleiben dadurch als `previous_only` mit leeren aktuellen
Positions-/CTR-Feldern sichtbar; fehlende Positionen werden nie als `0`
ausgegeben. `previous_start` und `previous_end` machen das Vergleichsfenster im
CSV selbst nachvollziehbar.

Konsumenten müssen `page_total` aus Query-Ownership-Auswertungen ausschließen.
Der Gap-Report ignoriert außerdem `previous_only`, während der Drift-Report
diese Zeilen als `VERSCHWUNDEN` priorisiert.

## Nächster Ausbau

- CrUX nach Deploy mit einem echten API-Key und der Produktions-Origin verifizieren
- als nächsten Research-Provider eine Quelle mit hoher Blog-Relevanz anbinden; bevorzugte Kandidaten: Destatis GENESIS oder Energy-Charts
- IndexNow nach dem ersten Live-Deploy mit echter Keydatei und einer Test-URL verifizieren
- CTA-Klickpfade jenseits des Audit-Intakes serverseitig oder über einen belastbaren Event-Layer versionieren
- stärkerer Koko-Layer jenseits der aktuellen Top-Page-Zuordnung
- Notiz-/Owner-Layer für operative Revenue-Arbeit direkt im Admin, falls der einfache Status nicht mehr reicht
