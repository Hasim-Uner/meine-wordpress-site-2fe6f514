# Manual WordPress Steps

Diese Schritte passieren im WordPress-Admin bzw. im Redirect-Tool, nicht im Theme-Code.

1. WordPress-Seite für `/energie-fahrplan-demo/` in den Papierkorb verschieben.
2. 301-Redirect anlegen: `/energie-fahrplan-demo/` -> `/solar-waermepumpen-leadgenerierung/#energie-anfrage`.
3. Hauptmenü, Footer-Menü und editorgetriebene interne Links auf alte Demo-Verweise prüfen.
4. Cache/CDN leeren und den Redirect im Browser testen.

## E3 Methodik-Case

1. Prüfen, ob eine veröffentlichte WordPress-Seite mit dem Slug `e3-new-energy` existiert.
2. Falls sie fehlt: neue Seite `E3 New Energy` mit Slug `e3-new-energy` anlegen.
3. Template-Zuweisung kann auf Standard bleiben, weil `page-e3-new-energy.php` als Slug-Template greift. Falls die Seite noch das ältere Template `Case Study – E3 New Energy` nutzt, ist das weiterhin kompatibel.
4. Cache/CDN leeren und `/e3-new-energy/` im Browser testen.
