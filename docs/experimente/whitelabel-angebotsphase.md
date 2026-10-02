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
