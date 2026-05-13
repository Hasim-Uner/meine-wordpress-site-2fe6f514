# Manual WordPress Steps

Diese Schritte passieren im WordPress-Admin bzw. im Redirect-Tool, nicht im Theme-Code.

1. WordPress-Seite für `/energie-fahrplan-demo/` in den Papierkorb verschieben.
2. 301-Redirect anlegen: `/energie-fahrplan-demo/` -> `/system-diagnose/`.
3. Hauptmenü, Footer-Menü und editorgetriebene interne Links auf alte Demo-Verweise prüfen.
4. Cache/CDN leeren und den Redirect im Browser testen.

## E3 Methodik-Case

1. Prüfen, ob eine veröffentlichte WordPress-Seite mit dem Slug `e3-new-energy` existiert.
2. Falls sie fehlt: neue Seite `E3 New Energy` mit Slug `e3-new-energy` anlegen.
3. Template-Zuweisung kann auf Standard bleiben, weil `page-e3-new-energy.php` als Slug-Template greift. Falls die Seite noch das ältere Template `Case Study – E3 New Energy` nutzt, ist das weiterhin kompatibel.
4. Cache/CDN leeren und `/e3-new-energy/` im Browser testen.

## System-Diagnose Route

1. WordPress-Seite `Anfrage-System-Analyse` öffnen.
2. URL-Slug ändern: `anfrage-system-analyse` -> `system-diagnose`.
3. Seitentitel ändern: `Anfrage-System-Analyse` -> `System-Diagnose`.
4. Meta-Description setzen: `System-Diagnose für Solar- und Wärmepumpen-Betriebe: Schriftlicher Befund in sieben Werktagen zu Anfrage-Quellen, Tracking, Funnel und Vertriebsanschluss. Drei priorisierte Hebel und Wirtschaftlichkeits-Einordnung.`
5. Redirect-Plugin `Redirection` öffnen und 301 einrichten: `/anfrage-system-analyse/` -> `/system-diagnose/`.
6. Klären, ob die Seite indexierbar werden soll. Falls ja: `noindex` für die System-Diagnose entfernen.
7. Cache/CDN leeren und `/system-diagnose/` sowie den alten Pfad im Browser testen.
