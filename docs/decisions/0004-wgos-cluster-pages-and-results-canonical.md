# 0004 WGOS Cluster Pages And Results Canonical

- Datum: 2026-03-12
- Status: teilweise superseded am 2026-05-20 durch Marktcheck-/Service-Konsolidierung

## Entscheidung

Wichtige Legacy-Service-Seiten werden nicht mehr dem WordPress-Editor oder manueller Template-Zuordnung ueberlassen.

Stattdessen gilt jetzt:

- zentrale Cluster-/Pillar-Definitionen liegen versioniert in `blocksy-child/inc/wgos/wgos-cluster-pages.php`
- die Seiten `/ga4-tracking-setup/` und `/performance-marketing/` werden theme-seitig weiter auf versionierte Templates geroutet
- die alten Service-Slugs `/wordpress-seo-hannover/`, `/core-web-vitals/`, `/conversion-rate-optimization/` und `/wordpress-wartung-hannover/` sind nachgelagert konsolidiert und bleiben nur als 301-/Asset-/Anker-Pfade abgesichert
- der Ergebnisse-Hub ist repo-seitig kanonisch `/ergebnisse/`
- alte Proof- und Audit-Slugs bleiben nur noch als 301-Aliase bestehen
- Single Posts koennen passende WGOS-Assets automatisch als Anschlussblock ausgeben

## Begruendung

- Vorhandene Audits im Repo zeigen klare Drift zwischen Editor-Seiten, Default-Templates und der eigentlichen WGOS-Architektur.
- Besonders kritisch waren alte Ergebnisse-Links, 404-Pfade wie `/wordpress-tech-audit/` und uneinheitliche CTA-Ziele.
- Ohne `wp-cli` und direkten Live-Admin-Zugriff muss die belastbare Logik aus dem Theme kommen, nicht aus impliziten Admin-Konfigurationen.

## Konsequenzen

- Aktive Clusterseiten bleiben versioniert review- und deploybar; konsolidierte Legacy-Service-Slugs duerfen nicht wieder als primaere CTAs oder eigene Money Pages aufgebaut werden.
- Navigation und Redirects laufen auf weniger historische Alias-Pfade.
- Der Proof-Layer zeigt repo-seitig auf `/ergebnisse/`, nicht mehr auf alte Case-Slugs.
- Weitere editorgetriebene Seiten bleiben moegliche Drift-Zonen und muessen separat geprueft oder spaeter ebenfalls versioniert werden.
