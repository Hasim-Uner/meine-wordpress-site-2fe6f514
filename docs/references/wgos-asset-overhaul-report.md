# WGOS Asset Overhaul Report

- Stand: 2026-03-12
- Basis: Repo-Implementierung auf `main` nach Merge des Feature-Branches
- Einschraenkung: Kein lokales `wp-cli`, kein direkter DB- oder Live-Admin-Zugriff in dieser Session

## 1. Zusammenfassung

Repo-seitig sind jetzt vier Ebenen sauberer verbunden:

1. WGOS-Assets laufen ueber eine versionierte Registry mit `35` Definitionen.
2. Die Registry bleibt mit SEO-Meta, Schema und Hub-/Explorer-Logik verbunden.
3. Wichtige Legacy-Service-Seiten werden theme-seitig als WGOS-Clusterseiten gerendert statt dem Editor oder der Template-Zuordnung ueberlassen.
4. Ergebnisse, Audit und weitere Legacy-Pfade haben harte kanonische Ziele statt historischer Alias-Links.

## 2. WGOS-Assets

### Source of Truth

Die WGOS-Asset-Registry bleibt die zentrale Quelle fuer:

- Struktur
- Content
- Status
- SEO-Meta
- Related Assets
- Schema-Typ
- Sync in den CPT `wgos_asset`

### Bestehende Assets

1. `CWV Speed Audit`
2. `CWV Optimierung`
3. `Server-Tuning`
4. `Security Hardening`
5. `Tracking Audit`
6. `GA4 Event Blueprint`
7. `Consent Mode v2`
8. `Server-Side Tracking (sGTM & Matomo)`
9. `Technical SEO Audit`
10. `Pillar Page`

### Weitere Registry-Assets

1. `Growth Audit`
2. `Positionierungs-Check`
3. `Seitenrollen-Mapping`
4. `Wettbewerbs-Analyse (Digital)`
5. `Roadmap & Priorisierung`
6. `Plugin Audit & Bereinigung`
7. `WordPress Update-Management`
8. `KPI-Dashboard Setup`
9. `UTM-Framework & Attribution`
10. `Keyword-Strategie & Content-Map`
11. `Content Hub Aufbau`
12. `On-Page SEO Optimierung`
13. `Interne Verlinkung & Seitenarchitektur`
14. `Schema Markup & Strukturierte Daten`
15. `Local SEO Setup`
16. `Landing Page (Neu)`
17. `Landing Page Optimierung`
18. `CTA & Formular-Optimierung`
19. `Angebotsseiten-Architektur`
20. `Social Proof & Trust-Elemente`
21. `Lead-Magnet Konzeption`
22. `Monthly Performance Review`
23. `Quarterly Roadmap Update`
24. `Reporting Dashboard`
25. `Conversion-Hypothesen & Testing`

### Aktueller Repo-Status

- Zielstatus laut aktuellem Code: `35 publish`, `0 draft`
- Die eigentliche Post-Erstellung oder -Aktualisierung passiert weiter ueber den Theme-Sync
- Die dedizierte Seite `wgos-systemlandkarte` wird jetzt bei Bedarf automatisch angelegt und mit dem Hub-Template verknuepft

### Wichtige Abweichung im Briefing

Die ausgeschriebene Matrix ergibt `35` Assets, nicht `32`. Der Repo-Stand folgt der Matrix, nicht der fehlerhaften Summenzeile.

## 3. ACF-Felder und SEO-Meta

### ACF-Felder

Gefundene und genutzte WGOS-relevante Felder:

- `seo_title`
- `seo_description`
- `og_image`
- `seo_noindex`
- `asset_module`
- `asset_phase`
- `asset_credits`
- `asset_keyword`
- `asset_schema_type`
- `asset_goal`
- `asset_result`
- `asset_prerequisite`
- `asset_short`
- `asset_intro`
- `asset_deliverable`
- `asset_bullets`
- `asset_related_slugs`

### SEO-Meta-Logik

- Primarlogik bleibt `blocksy-child/inc/seo-meta.php`
- WGOS-Assets beziehen ihre Meta-Werte aus der Registry-Synchronisation
- Cluster-/Legacy-Seiten bekommen jetzt theme-seitige SEO-Fallbacks, falls im Editor keine sauberen Meta-Werte stehen
- Rank-Math-Bridges bleiben erhalten, damit gespeicherte Werte nicht verloren gehen

## 4. Schema-Markup-Status

### WGOS-Assets

- Zielstatus fuer alle `35` WGOS-Assets: `Service`
- Ausgabe ueber `blocksy-child/inc/org-schema.php`

### Legacy-/Clusterseiten

Neu oder bestaetigt im Repo:

- `wordpress-seo-hannover` -> `Service`
- `core-web-vitals` -> `Service`
- `conversion-rate-optimization` -> `Service`
- `ga4-tracking-setup` -> `Service`
- `performance-marketing` -> `Service`
- `wordpress-agentur-hannover` -> bestehendes `Service`-Schema bleibt

## 5. Legacy-Seiten migriert oder abgesichert

### Versionierte Cluster-/Pillar-Seiten

Diese Seiten werden jetzt nicht mehr nur ueber Editor-Inhalt oder manuelle Template-Zuordnung bestimmt:

- `/wordpress-seo-hannover/`
- `/core-web-vitals/`
- `/conversion-rate-optimization/`
- `/ga4-tracking-setup/`
- `/performance-marketing/`

Technik:

- neue Cluster-Definitionen in `blocksy-child/inc/wgos-cluster-pages.php`
- shared renderer in `blocksy-child/page-wgos-pillar.php`
- Route-Forcing ueber `template_include`

### Bereits versionierte lokale Einstiegsseite

- `/wordpress-agentur-hannover/` bleibt die lokale Einstiegsseite und fuehrt weiter in WGOS, Audit, Cases und Systemlogik

### Admin-Helfer fuer Legacy-Bereinigung

- Im WordPress-Admin erscheint ein Legacy-Cleanup-Hinweis
- der Ein-Klick-Flow kann gefundene Altseiten auf `draft` setzen und passende Menue-Eintraege entfernen

## 6. Redirects und Navigation

### Behobene Navigations-/URL-Inkonsistenzen

- Ergebnisse sind repo-seitig jetzt kanonisch auf `/ergebnisse/`
- `/case-studies/` und `/case-studies-e-commerce/` sind nur noch 301-Aliase
- statische Proof-Assets und Referenz-Dokumente wurden auf `/ergebnisse/` umgestellt

### 301-Redirects im Theme

- `/case-studies/` -> `/ergebnisse/`
- `/case-studies-e-commerce/` -> `/ergebnisse/`
- `/audit/` -> `/growth-audit/`
- `/customer-journey-audit/` -> `/growth-audit/`
- `/360-audit/` -> `/growth-audit/`
- `/wordpress-tech-audit/` -> `/growth-audit/`
- `/alle-loesungen-im-detail/` -> `/alle-loesungen/`
- `/meta-ads/` -> `/wordpress-growth-operating-system/`
- `/wordpress-agentur/` -> `/wordpress-agentur-hannover/`
- `/roi-rechner/` -> `/kostenlose-tools/`

### Bewusst offen gelassen

- `/kostenlose-tools/` bleibt live, weil im Repo weiterhin ein Tools-Hub existiert
- `/wordpress-wartung-hannover/` ist nachgelagert konsolidiert und wird per 301 auf `/wordpress-agentur-hannover/#wordpress-wartung` geführt; `/ga4-tracking-setup/` und `/performance-marketing/` bleiben aktive versionierte Clusterseiten

## 7. Blog -> WGOS-Asset Verlinkung

Single Posts koennen jetzt automatisch einen WGOS-Anschlussblock ausgeben.

Aktuelle Mapping-Matrix im Repo:

| Beitrag | Verlinkte WGOS-Assets |
| --- | --- |
| `/owned-leads-statt-ad-miete/` | `growth-audit`, `angebotsseiten-architektur`, `landing-page-neu` |
| `/b2b-landingpage-optimieren/` | `landing-page-optimierung`, `cta-formular-optimierung`, `angebotsseiten-architektur` |
| `/meta-ads-fuer-b2b/` | `landing-page-neu`, `landing-page-optimierung`, `tracking-audit` |
| `/design-ist-mehr-als-aesthetik/` | `angebotsseiten-architektur`, `cta-formular-optimierung` |
| `/server-side-tracking-gtm/` | `server-side-tracking`, `tracking-audit`, `consent-mode-v2` |
| `/core-web-vitals-wachstum-seo-und-roas/` | `cwv-speed-audit`, `cwv-optimierung`, `server-tuning` |

Hinweis:

- Die Mapping-Logik ist theme-seitig versioniert.
- Fuer weitere Posts kann dieselbe Struktur in `nexus_get_wgos_blog_asset_bridge_data()` erweitert werden.

## 8. Dokumentation und Betriebsstatus

Aktualisiert in dieser Runde:

- `docs/references/wgos-asset-discovery.md`
- `docs/references/wgos-asset-overhaul-report.md`
- `agents/skills/navigation-migration/SKILL.md`
- `docs/architecture/LIVE_STATUS.md`
- `docs/decisions/0004-wgos-cluster-pages-and-results-canonical.md`

## 9. Offene Punkte

1. Deployment wurde in dieser Session erst durch den geplanten Push auf `main` ausgelost, nicht lokal simuliert.
2. Ohne `wp-cli` bleibt offen, welche Menues oder Editor-Inhalte live noch von der Repo-Logik abweichen.
3. Fuer `/kontaktiere-mich/` und weitere editorgetriebene Default-Seiten gab es in dieser Runde keinen Umbau.
4. Die WGOS-Hauptseite und die Systemlandkarte sind repo-seitig konsistent, muessen nach dem Deploy aber gegen den Live-Inhalt verifiziert werden.
5. Falls WordPress-Menues im Admin manuell gepflegt werden, sollten sie nach dem Deploy kurz gegen `/ergebnisse/` und `/growth-audit/` gegengeprueft werden.
