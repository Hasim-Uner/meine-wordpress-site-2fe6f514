# White-Label: Anfragen in der Angebotsphase

Finale Fassung im Repo vom 2026-10-02. Messfenster: sechs Wochen ab dem
Produktiv-Deploy; dessen Datum und Uhrzeit vor der Auswertung eintragen.

## Hypothese

Das echte Abnahmeprotokoll im Hero und die maßstäbliche Margen-Tafel erhöhen
den Anteil der Anfragen aus der Angebotsphase. Belege und Ablauf stehen vor
Preisen; der Hero hat einen Hauptweg zur konkreten Aufgabe.

## Messung

- `cta_whitelabel_offers_assessment` und `cta_whitelabel_way_offer` zusammen
  gegen `cta_whitelabel_hero_task_brief` auswerten; die Klicks sind Wegwahl,
  keine eingegangenen Anfragen.
- Für tatsächliche Anfragen `whitelabel_request_submit` und das Feld `case`
  im Posteingang/CRM nutzen: `angebotsphase`, `aufgabe`, `vormerken`.
- Anteil `angebotsphase` an den erfolgreich eingegangenen Anfragen vor und
  nach dem Deploy vergleichen. Absolute Fallzahlen, Sessions der Route und
  Änderungen der Akquise-Mails daneben nennen; bei wenigen Fällen keine
  gesicherte Wirkung behaupten.
- `cta_whitelabel_hero_offer` endet mit dem Umbau. Der neue Assessment-Hook
  markiert den Weg nach Belegen und Preisen und setzt seine alte Zeitreihe
  nicht fort.

## Abnahme vor dem Messfenster

Live als nicht eingeloggter Besucher prüfen: echte Protokollbefunde, LCP,
kein horizontaler Überlauf und Testanfragen für alle drei Fälle mit korrektem
`case` im tatsächlichen Posteingang/CRM. Lokale Browser-Tests fangen den
REST-Versand ab; die PHP-Endpoint-Tests prüfen die Übergabe an CRM und Mail
gegen Test-Doubles. Sie ersetzen diese Live-Abnahme nicht.

## Änderung nach dem Messfenster

2026-11-14: Abschnitt 02 bekommt das Schichtmodell und einen neuen Einstieg,
Abschnitt 05 den Margen-Rechner. Hero, Reihenfolge der Stationen, CTA-Hooks
und Formularvertrag sind unverändert. Fällt der Deploy dieser Änderung noch in
das Sechs-Wochen-Fenster, endet die Auswertung am Vortag des Deploys.

Mit der Überarbeitung vom 2026-11-24 heißt der Hauptweg in Kopf, Hero und
Sticky-Leiste „Projekt einschätzen lassen“. Er setzt weiterhin `case=aufgabe`.
Auch Briefings von Agenturen in der Angebotsphase können deshalb unter
`aufgabe` eingehen. Für die Auswertung den Briefinginhalt hinzuziehen und
qualifizierte Anfragen sowie daraus entstandene Angebote nach Zugangsquelle
ausweisen, soweit diese bekannt ist. Die Textänderung ab ihrem Produktiv-Deploy
als eigenen Zeitraum führen.
