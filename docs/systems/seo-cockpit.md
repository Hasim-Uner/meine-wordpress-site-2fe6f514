# SEO Cockpit

Stand: 2026-03-20.

Diese Doku beschreibt den repo-seitigen Startpunkt fuer ein internes SEO-Dashboard im WordPress-Admin.

## Ziel

Das Cockpit soll keine neue externe Plattform einfuehren.

Stattdessen:

- WordPress bleibt die operative Schicht
- Google Search Console liefert die externe SEO-Sicht
- Koko Analytics liefert optional die lokale Traffic-Sicht
- das interne Audit-CRM liefert jetzt zusaetzlich Lead- und Attributionssignale
- das Repo enthaelt die Logik, das Caching und die Admin-Oberflaeche

## Aktueller Scope

Repo-seitig vorhanden:

- Top-Level-Admin-Menue `SEO Cockpit`
- kompaktes Snapshot-Widget im Standard-WordPress-Dashboard
- priorisierte Queue im Admin, die SEO-Signale jetzt gegen Business-Wert, Funnel-Naehe und Confidence gewichtet
- Revenue Command Center V1 mit Today Revenue Queue für Anfrage-/Umsatzwirkung statt Traffic-Priorisierung
- Lead-Layer aus `nexus_review_request` mit Audit-Leads, Status, Source-Mix und intern attribuierten Seiten
- minimaler Status-Layer für Revenue-Queue-Einträge über WordPress-Option, ohne neue DB-Tabelle
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
  - Snippet-Schwächen
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

## Nicht im Repo verifiziert

- echte Google-OAuth-Credentials
- echte Search-Console-Property-Verbindung
- echtes Refresh-Token
- installierte Koko-Analytics-Instanz

## Architektur

Code-Orte:

- `blocksy-child/inc/seo-cockpit.php`
- `blocksy-child/assets/css/seo-cockpit-admin.css`

Wichtige technische Entscheidungen:

- kein schweres SEO-Dashboard-Plugin
- keine externe Datenbank oder SaaS fuer die Dashboard-Logik
- API-Zugriff direkt per `wp_remote_*`
- leichter Cache ueber WordPress-Transients
- letzter Sync-Status in WordPress-Optionen fuer Admin-Sichtbarkeit
- Koko nur optional als zweiter Traffic-Layer
- Audit-CRM als dritter Datenlayer fuer Lead-Kontext und Priorisierung
- Revenue Command Center als vierter operativer Layer für Today Queue, Lead-Follow-up, Page Queue, Conversion Leaks und Manual Checks

## Naechster Ausbau

- CTA-Klickpfade jenseits des Audit-Intakes serverseitig oder ueber einen belastbaren Event-Layer versionieren
- staerkerer Koko-Layer jenseits der aktuellen Top-Page-Zuordnung
- Notiz-/Owner-Layer für operative Revenue-Arbeit direkt im Admin, falls der einfache Status nicht mehr reicht
- URL-Inspection oder Indexing-nahe Erweiterungen nur, wenn der operative Nutzen klar ist
