# SEO Cockpit

Stand: 2026-09-04.

Diese Doku beschreibt den repo-seitigen Startpunkt fuer ein internes SEO-Dashboard im WordPress-Admin.

## Ziel

Das Cockpit soll keine neue externe Plattform einfuehren.

Stattdessen:

- WordPress bleibt die operative Schicht
- Google Search Console liefert die externe SEO-Sicht
- IndexNow meldet neue, geaenderte und geloeschte URLs an teilnehmende Suchmaschinen
- Koko Analytics liefert optional die lokale Traffic-Sicht
- das interne Audit-CRM liefert jetzt zusaetzlich Lead- und Attributionssignale
- das Repo enthaelt die Logik, das Caching und die Admin-Oberflaeche

## Aktueller Scope

Repo-seitig vorhanden:

- Top-Level-Admin-Menue `SEO Cockpit`
- visuelles Dashboard V3 als Command Center
- kompaktes Snapshot-Widget im Standard-WordPress-Dashboard
- priorisierte Queue im Admin, die SEO-Signale jetzt gegen Business-Wert, Funnel-Naehe und Confidence gewichtet
- Revenue Command Center V1 mit Today Revenue Queue fuer Anfrage-/Umsatzwirkung statt Traffic-Priorisierung
- Lead-Layer aus `nexus_review_request` mit Audit-Leads, Status, Source-Mix und intern attribuierten Seiten
- minimaler Status-Layer fuer Revenue-Queue-Eintraege ueber WordPress-Option, ohne neue DB-Tabelle
- Settings-Seite fuer
  - Search-Console-Property
  - Google Client ID
  - Google Client Secret
  - Cache-Fenster
- OAuth-Flow gegen Google mit Redirect auf `admin-post.php`
- Token-Speicherung und Token-Refresh
- gecachte Dashboard-Abfragen fuer
  - Klicks
  - Impressionen
  - CTR
  - durchschnittliche Position
  - Top Pages
  - Top Queries
  - Device-Split
- heuristische Insight-Typen fuer
  - Quick Wins
  - CTR-Chancen
  - Decay
  - Snippet-Schwaechen
  - Kannibalisierung
  - Money-Page-Unterperformance
  - Orphan-/Bridge-/Indexierungs-Signale
- browserseitige Lead-Attribution fuer neue Audit-Leads ueber
  - Formular-Landingpage
  - ersten internen Einstieg der Session
  - vorherige interne Seite
  - Referrer-URL
- automatischer Snapshot-Refresh per WP-Cron (`twicedaily`)
- optionale Erkennung des Plugins `koko-analytics/koko-analytics.php`
- IndexNow-Control-Layer mit
  - runtime-generiertem Ownership-Key in WordPress-Optionen
  - Root-Keydatei `/{key}.txt` fuer die Host-Verifikation
  - manuellem `URL jetzt melden`-Button
  - direktem IndexNow-Button auf URL-Drilldowns
  - automatischen Meldungen fuer Publish, Update und Delete oeffentlicher Inhalte
  - asynchroner Ausfuehrung automatischer Meldungen per Single WP-Cron Event
  - lokalem Verlauf der letzten 50 Meldungen
  - eigenem Untermenue `SEO Cockpit -> IndexNow`

## Revenue Command Center V1

Das Command Center ist Admin-only und sitzt innerhalb des SEO Cockpits. Es nutzt keine neuen externen Abhaengigkeiten und erzeugt keine Fake-Metriken. Wenn Search Console fehlt, werden nur CRM- und Manual-Checks angezeigt.

Sektionen:

- `Heute zuerst`: Top 5 aktive Aktionen mit hoechstem Revenue-Potenzial
- `Lead-Follow-up`: neue, offene, progressed, won und nicht attribuierte Marktcheck-Anfragen
- `Page Revenue Queue`: URLs mit SEO-Signal, Funnel-Wert oder Lead-Attribution
- `Conversion Leaks`: funnelnahe Seiten mit Sichtbarkeit, aber ohne Lead-Signal oder mit schwacher CTA-/Proof-Bruecke
- `Manual Checks`: Search Console, Tracking, WordPress Admin und Attribution nur bei fehlender oder schwacher Datenbasis

Die Queue zeigt pro Eintrag Prioritaet, Typ, URL/Lead, Funnel-Rolle, Problem, Warum-jetzt, konkrete naechste Aktion, erwarteten Hebel, Aufwand, Risiko, Repo-/Manual-Fixbarkeit, Status, Confidence und Datenbasis.

Revenue Score:

- Lead-Signal und Funnel-Naehe werden staerker gewichtet als Impressionen.
- Business-Wert unterscheidet Marktcheck, Money Pages, Proof, Cluster, Blog/Hub, Legacy und Utility.
- Search Demand ist gedeckelt, damit Traffic allein nicht nach oben rutscht.
- Conversion Gap wird gesetzt, wenn funnelnahe Seiten Sichtbarkeit, aber keine Lead-Signale haben.
- Assist-Wert nutzt Entry-/Previous-/Landing-Attribution aus dem Audit-CRM.
- Proof-Naehe staerkt E3-/Ergebnis-nahe URLs.
- Decay, Confidence und Risk Penalty verhindern blinde Repo-Aenderungen.

Status-Layer:

- Statuswerte: Neu, Heute, In Arbeit, Erledigt, Ignorieren.
- Speicherung: `nexus_revenue_command_center_statuses`.
- Capability: `manage_seo_cockpit` oder `manage_options`.
- Nonce: `nexus_revenue_command_center_status`.

## IndexNow-Control-Layer

IndexNow ist bewusst getrennt von Google Search Console und von Bing Places for Business.
Es braucht keinen Bing-OAuth-Flow. Der Host-Besitz wird ueber eine Runtime-Keydatei
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
- eine erfolgreiche Meldung ist keine Garantie fuer Crawling oder Indexierung

Automatik:

- Standard: aktiv
- oeffentliche Posts/Pages werden bei Publish und Update vorgemerkt
- geloeschte, zuvor veroeffentlichte URLs werden ebenfalls vorgemerkt
- Speichern im Editor blockiert nicht auf die externe API
- der Request laeuft ca. 20 Sekunden spaeter als Single WP-Cron Event
- ein 5-Minuten-Debounce verhindert doppelte Meldungen derselben URL/Quelle

Frontend-Footprint:

Das Cockpit bleibt grundsaetzlich Admin-/Background-only. Nur das IndexNow-Modul
wird vor dem Frontend-Early-Return geladen, damit die Root-Keydatei ausgeliefert
werden kann. Es rendert keine sonstige Frontend-UI und fuehrt keine API-Requests
bei normalen Seitenaufrufen aus.

## Nicht im Repo verifiziert

- echte Google-OAuth-Credentials
- echte Search-Console-Property-Verbindung
- echtes Refresh-Token
- installierte Koko-Analytics-Instanz
- Live-Erreichbarkeit der generierten IndexNow-Keydatei nach dem ersten Deploy
- erste echte IndexNow-Antwort auf der Produktionsdomain

## Architektur

Code-Orte:

- `blocksy-child/inc/seo-cockpit/seo-cockpit.php`
- `blocksy-child/assets/css/seo-cockpit-admin.css`
- `blocksy-child/inc/seo-cockpit/seo-cockpit-dashboard-v3.php`
- `blocksy-child/inc/seo-cockpit/seo-cockpit-indexnow.php`

Wichtige technische Entscheidungen:

- kein schweres SEO-Dashboard-Plugin
- keine externe Datenbank oder SaaS fuer die Dashboard-Logik
- API-Zugriff direkt per `wp_remote_*`
- leichter Cache ueber WordPress-Transients
- letzter Sync-Status in WordPress-Optionen fuer Admin-Sichtbarkeit
- Koko nur optional als zweiter Traffic-Layer
- Audit-CRM als dritter Datenlayer fuer Lead-Kontext und Priorisierung
- Revenue Command Center als vierter operativer Layer fuer Today Queue, Lead-Follow-up, Page Queue, Conversion Leaks und Manual Checks
- IndexNow als eigener Indexing-Control-Layer ohne Google- oder Bing-Account-Credentials

## CSV-Exportvertrag

Der Admin-Export enthaelt zwei explizit getrennte Zeilentypen:

- `query_page`: Query×URL-Metriken fuer Ownership, Gap- und Drift-Auswertungen
- `page_total`: vollstaendige URL-Summen, auch wenn Search Console einzelne Queries anonymisiert

`period_presence` unterscheidet `both`, `current_only` und `previous_only`.
Der Export bildet dafuer die Union beider Perioden. Vollstaendig verschwundene
Query×URL-Paare bleiben dadurch als `previous_only` mit leeren aktuellen
Positions-/CTR-Feldern sichtbar; fehlende Positionen werden nie als `0`
ausgegeben. `previous_start` und `previous_end` machen das Vergleichsfenster im
CSV selbst nachvollziehbar.

Konsumenten muessen `page_total` aus Query-Ownership-Auswertungen ausschliessen.
Der Gap-Report ignoriert ausserdem `previous_only`, waehrend der Drift-Report
diese Zeilen als `VERSCHWUNDEN` priorisiert.

## Naechster Ausbau

- IndexNow nach dem ersten Live-Deploy mit echter Keydatei und einer Test-URL verifizieren
- CTA-Klickpfade jenseits des Audit-Intakes serverseitig oder ueber einen belastbaren Event-Layer versionieren
- staerkerer Koko-Layer jenseits der aktuellen Top-Page-Zuordnung
- Notiz-/Owner-Layer fuer operative Revenue-Arbeit direkt im Admin, falls der einfache Status nicht mehr reicht
