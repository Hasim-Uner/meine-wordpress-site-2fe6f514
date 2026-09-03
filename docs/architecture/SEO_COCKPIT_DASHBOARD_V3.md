# SEO Cockpit Dashboard V3

## Ziel

Die bestehende SEO-Cockpit-Logik bleibt erhalten, die Top-Level-Übersicht wird aber von einer tabellen- und listenlastigen Arbeitsoberfläche zu einem visuellen Command Center umgebaut.

## Neue Oberfläche

- dunkler Cockpit-Hero mit Verbindungsstatus, Zeitraum, Sync und CSV-Export
- KPI-Karten für Klicks, Impressionen, CTR und Position mit echten Perioden-Deltas und Sparklines
- `Action Hub` mit drei Spuren: `Sofort`, `Diese Woche`, `Beobachten`
- bestehende Revenue-/SEO-Prioritäten werden als Action Cards statt als breite To-do-Tabelle dargestellt
- vorhandene Statuswerte `Neu`, `Heute`, `In Arbeit`, `Erledigt`, `Ignorieren` bleiben schreibbar
- Quick Wins werden als Chancen mit Potenzialbalken dargestellt
- Query-Mover zeigen Gewinner und Verlierer
- priorisierte Problemseiten erscheinen als URL-Radar-Karten
- Search Console, Sitemap, Koko und Audit-CRM werden im System-Health-Bereich zusammengeführt
- Top Pages und Top Queries bleiben als sekundäre Rohdaten-Tabellen in einem Disclosure verfügbar

## Dateien

- `blocksy-child/inc/seo-cockpit/seo-cockpit-dashboard-v3.php`
  - neuer Top-Level-Renderer
  - nutzt bestehende Snapshot-, Insight-, Revenue- und Status-Helfer
  - delegiert URL-Drilldowns weiterhin an den bestehenden Renderer
- `blocksy-child/assets/css/seo-cockpit-dashboard-v3.css`
  - visuelles V3-System auf Basis des Markenakzents `#b46a3c`
  - enthält zusätzlich eine sanfte Restylingschicht für bestehende Detail-, Settings- und Search-Console-Panels
- `blocksy-child/inc/seo-cockpit/seo-cockpit.php`
  - lädt das V3-Modul nach `seo-cockpit-ui.php`

## Bewusst unverändert

- Google OAuth und Token-Handling
- Search-Console-Sitemap-Submit inklusive HTTP-411-Kompatibilitätsfix
- Snapshot-Aufbau und Cache-Vertrag
- WP-Cron und Deployment-Sync
- URL Inspection
- URL-Drilldown-Datenvertrag
- CSV-Export-Vertrag
- Koko- und Lead-Datenquellen

## Rollback

Das V3-Modul ist ein eigener UI-Layer. Für einen Rollback kann seine Loader-Zeile entfernt werden; der bisherige `nexus_render_seo_cockpit_dashboard()` bleibt vollständig im Repository erhalten.
