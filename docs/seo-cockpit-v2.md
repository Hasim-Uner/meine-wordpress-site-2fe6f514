# SEO Cockpit V2

## Architektur

Das Cockpit ist aus der bisherigen Monolith-Datei in klar getrennte Layer aufgeteilt:

- `blocksy-child/inc/seo-cockpit.php`
  Bootstrap-Loader fuer das Cockpit.
- `blocksy-child/inc/seo-cockpit-core.php`
  Capabilities, Settings, Menues, Konfiguration, URL- und Cache-Helfer.
- `blocksy-child/inc/seo-cockpit-api.php`
  OAuth, Tokens, Google-Requests, Search-Console-Reports, Sitemaps und URL-Inspection.
- `blocksy-child/inc/seo-cockpit-koko.php`
  Koko-Status, REST-Zugriff, Onsite-Metriken und Koko-Kontext fuer Snapshot und Drilldown.
- `blocksy-child/inc/seo-cockpit-links.php`
  Interner Linkgraph auf Basis veroeffentlichter Inhalte.
- `blocksy-child/inc/seo-cockpit-leads.php`
  Audit-Lead-Layer aus dem internen CRM inkl. Seitenattribution fuer neue Leads.
- `blocksy-child/inc/seo-cockpit-sync.php`
  Snapshot-Aufbau, Historical Layer, Cache-Versionierung, Sync, Cron und Locking.
- `blocksy-child/inc/seo-cockpit-insights.php`
  Insight-Regeln, WordPress-Kontext, URL-Zuordnung und Drilldown-Datenmodell.
- `blocksy-child/inc/seo-cockpit-diagnostics.php`
  Runtime-Diagnostik fuer OAuth, Cron, Koko, Linkgraph und Drilldown.
- `blocksy-child/inc/seo-cockpit-command-center.php`
  Revenue Command Center V1: Score Helper, Next-Best-Action Resolver, Today Revenue Queue und minimaler Status-Layer.
- `blocksy-child/inc/seo-cockpit-ui.php`
  Admin-Rendering fuer Uebersicht, Drilldown, Widget und Einstellungen.

Zusaetzlich wurde in `blocksy-child/inc/seo-meta.php` ein wiederverwendbarer Helper fuer den effektiven SEO-Kontext pro Post ergaenzt:

- `hu_get_singular_post_seo_context()`

Dadurch greift das Cockpit bei Title/Description/Canonical/noindex nicht auf eine parallele Logik zurueck, sondern auf denselben Entscheidungsrahmen wie das Frontend.

## Datenfluss

### 1. Verbindung / OAuth

- Einstellungen werden in `nexus_seo_cockpit_settings` gespeichert.
- OAuth-Token werden in `nexus_seo_cockpit_tokens` gehalten.
- Die Verbindung wird ueber einen Google Web-OAuth-Flow aufgebaut.

### 2. Snapshot

Der Uebersichts-Snapshot kombiniert drei Ebenen:

- Search Console:
  - Overview-Metriken
  - Tagesverlauf
  - Top Pages
  - Top Queries
  - Top Devices
  - Query/Page-Kombinationen
  - Sitemaps
- Koko Analytics:
  - Besucher und Pageviews je Zeitraum
  - Tagesverlauf
  - Top-Seiten fuer den Zeitraum
- Audit-CRM:
  - Audit-Leads je Zeitraum
  - Statusverteilung und Source-Mix
  - intern attribuierte Seiten auf Basis von Einstieg, letzter interner Seite und Formular-Landing
- WordPress:
  - Post ID
  - Post Type
  - Status
  - Template / Seitentyp
  - Word Count
  - SEO-Kontext
  - interne Linksignale
  - Edit- und Frontend-Links
- Entscheidungslogik:
  - Quick Wins
  - CTR-Opportunities
  - Decay
  - Low Signal
  - Possible Cannibalization
  - Snippet Weakness
  - Money-Page-Unterperformance
  - Orphan Value Pages
  - Weak Funnel Bridges
  - Indexing Mismatches
- Priorisierungslayer:
  - Page Role Mapping
  - Business-Wert
  - Funnel-Naehe
  - Demand ueber Impressionen
  - Lead-Signal ueber intern attribuierte Audit-Leads
  - Confidence ueber Kontext und Koko-Signale
- Revenue Command Center:
  - Lead-Follow-up aus `nexus_review_request`
  - Page Revenue Queue aus SEO-Insights plus CRM-Attribution
  - Conversion Leaks für funnelnahe Seiten mit Sichtbarkeit, aber ohne Lead-Signal
  - Manual Checks bei fehlender Search Console, fehlenden Lead-Daten oder unmapped Leads
  - Statusspeicherung über `nexus_revenue_command_center_statuses`

### 3. Drilldown

Die URL-Detailansicht baut auf einem eigenen Detailmodell auf:

- aggregierte Kernmetriken fuer die URL
- Trend nach Datum
- Top Queries der URL
- Geraeteverteilung
- WordPress-Kontext
- Koko-Kontext der URL, soweit eindeutig zuordenbar
- Lead-Kontext der URL mit aktuellem, vorherigem und Lifetime-Fenster
- gefilterte Insights fuer diese URL
- vorbereitete, manuelle URL-Inspection
- Drilldown-Diagnostik

## Cache- und Sync-Logik

### Snapshot- und Detail-Caches

Die Cockpit-Caches verwenden eine zentrale Cache-Version:

- `nexus_seo_cockpit_cache_version`

Statt einzelne Transients schwerfaellig aufzuraeumen, invalidiert das Cockpit beim Refresh die Version und erzeugt neue Keys. Das betrifft:

- Snapshots
- Sites
- Sitemaps
- Detailansichten
- URL-Inspection-Caches
- Koko-Caches
- Linkgraph-Caches

### Refresh-Fenster

`refresh_window` steuert jetzt zwei Dinge konsistent:

- Snapshot-Cache-TTL
- Cron-Intervall

Wird das Fenster geaendert, wird das Event neu geplant.

### Runtime

Die Runtime-Option `nexus_seo_cockpit_runtime` fuehrt unter anderem:

- `last_sync_at`
- `next_sync_at`
- `cache_expires_at`
- `last_sync_status`
- `last_sync_source`
- `last_error_code`
- `last_error_message`

### Locking

Parallele Syncs werden ueber einen Transient-Lock verhindert:

- `nexus_seo_cockpit_sync_lock`

Dadurch kollidieren manuelle und Cron-Syncs nicht mehr so leicht.

## Interne Linkzaehlung

V2.1 misst interne Links jetzt in zwei Ebenen:

- `context`
  Redaktionelle Links aus veroeffentlichten oeffentlichen Inhalten auf Basis von `post_content`
- `sitewide`
  Theme-injizierte globale Links aus Header, Blog-Header und Footer-Shells

Dadurch zeigt der Drilldown jetzt getrennt:

- Kontextlinks eingehend
- verlinkende Inhaltsdokumente
- Sitewide-Links eingehend
- verlinkende Sitewide-Quellen
- globale Shell-Ziele fuer die aktuelle URL

Query-Parameter werden weiterhin entfernt, damit Tracking-Dubletten keine eigenen Knoten bilden.

Bewusst noch nicht enthalten:

- Widgets
- block-/shortcode-gerenderte Links ausserhalb von `post_content`
- exakte Footer/Header-Ausgaben aus fremden Plugins oder Buildern

## Row-Limits und Paging

Search Console arbeitet jetzt mit gekapselten Bucket-Limits statt mit verstreuten Einzelwerten:

- `top_pages`
- `top_queries`
- `top_devices`
- `page_rows`
- `query_page_rows`
- `detail_queries`
- `detail_devices`

Fuer groessere Buckets nutzt das Cockpit jetzt `startRow` plus begrenztes Paging.
Dadurch bleibt das System fuer groessere Sites robuster, ohne die API unkontrolliert mit Requests zu fluten.

## Runtime-Diagnostik

Das Cockpit fuehrt jetzt kompakte Laufzeitchecks fuer:

- OAuth / Token-Nutzbarkeit
- Cron / naechster Sync / Lock-Zustand
- Koko-Verfuegbarkeit
- Linkgraph-Aufbau
- Drilldown-Kontext

## Insight-Regeln

Die Insights sind bewusst heuristisch und regelbasiert, nicht "smart" behauptet:

- `QUICK_WIN`
  Position zwischen ca. 8 und 20 bei soliden Impressionen.
- `CTR_OPPORTUNITY`
  Hohe Impressionen, aber schwache CTR trotz brauchbarer Position.
- `DECAY`
  Signifikanter Rueckgang gegenueber dem Vergleichsfenster.
- `LOW_SIGNAL`
  Sichtbarkeit vorhanden, aber noch zu weit von echten Rankings entfernt.
- `POSSIBLE_CANNIBALIZATION`
  Mehrere URLs sammeln fuer dieselbe Query nennenswerte Impressionen.
- `SNIPPET_WEAKNESS`
  Impressionen vorhanden, aber Title/Description fehlen oder wirken formal schwach.
- `MONEY_PAGE_UNDERPERFORMING`
  Kaufnahe Seite sammelt Nachfrage, bleibt aber bei Position oder CTR unter Zielwert.
- `ORPHAN_VALUE_PAGE`
  Kaufnahe Seite hat zu wenig kontextuelle interne Signale.
- `WEAK_FUNNEL_BRIDGE`
  Content-/Hub-Seite sammelt Nachfrage, fuehrt aber kaum in Richtung Audit, Service oder Proof weiter.
- `INDEXING_MISMATCH`
  Kaufnahe Seite hat noindex-, Canonical- oder Sitemap-Reibung.

Seit 2026-03-20 werden die Insights nicht mehr nur nach Severity sortiert.
Zusaetzlich berechnet das Cockpit jetzt einen `Priority Score`, der mehrere Ebenen kombiniert:

- Severity
- Demand / Impressionen
- Business-Wert der URL
- Funnel-Naehe der URL
- Actionability des Insight-Typs
- Lead-Signal aus dem Audit-CRM
- Confidence aus WordPress-Kontext und optionalem Koko-Match

Jede Insight enthaelt:

- `type`
- `severity`
- `label`
- `reason`
- `url`
- `query`
- `metrics`
- `recommended_action`
- `page_role`
- `page_role_label`
- `priority_score`
- `priority_bucket`
- `priority_label`
- `lead_requests_current`
- `lead_requests_lifetime`
- `lead_won_lifetime`

## Offene Punkte

- URL-Inspection ist bewusst manuell im Drilldown, nicht massenhaft automatisiert.
- Sitemap-Mitgliedschaft pro URL ist derzeit ein WordPress-internes Signal, keine Search-Console-URL-Membership.
- Koko basiert auf defensivem REST-Mapping und nicht auf einer harten Plugin-internen API-Vertragsgarantie.
- Die interne Linkzaehlung trennt jetzt Kontext- und Sitewide-Signale, bleibt aber bei Widgets und dynamisch von Plugins injizierten Navigationspfaden noch konservativ.
- Die neue Lead-Attribution deckt den Audit-Intake bereits ab, aber noch keine generische serverseitige CTA-Klickhistorie ausserhalb dieses Funnels.

## Grenzen / Risiken

- Search Console bleibt zeitverzoegert; das Cockpit kann keine Echtzeit garantieren.
- Query/Page-Kombinationsdaten sind auf sinnvolle Row-Limits begrenzt und bilden nicht die komplette Long Tail ab.
- URL-Zuordnung basiert fuer Search-Console-URLs auf `url_to_postid()` plus Sonderfaellen fuer Startseite und Blog.
- Inspection und Search-Console-APIs bleiben quota- und permission-abhaengig.
- Koko-REST-Antworten werden bewusst flexibel geparst; falls das Plugin seine Antwortstruktur stark aendert, kann nur ein Teil des Kontextlayers verfuegbar sein.
- Audit-Leads vor dem 2026-03-20-Attributionsausbau koennen im Cockpit nur aggregiert, aber nicht immer intern einer Seite zugeordnet werden.
