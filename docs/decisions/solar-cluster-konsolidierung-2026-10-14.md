# Solar-Cluster konsolidieren

Entscheidung: 14.10.2026. Auftrag: Solar als Nebenbereich konzentrieren,
vorhandene Sichtbarkeit schützen und die betroffenen Anfragewege vollständig
prüfen. Keine neuen Solar-Routen und keine neue Angebotsstufe.

## Datenbasis

SEO-Cockpit-Export `seo-cockpit-export-90d-2026-10-11.csv`, Zeitraum
14.07.–11.10.2026. Seitenwerte und Query-Seiten-Zeilen getrennt ausgewertet.
Der Zeitraum enthält alte und neue Positionierung, keine Conversion-Daten.

| Quellseite | Klicks | Impressionen | Ziel |
|---|---:|---:|---|
| Kunden gewinnen für Solarteure | 0 | 37 | Solar-Hauptseite |
| Lead-Funnel Solar | 0 | 29 | Hauptseite, Anfragearchitektur |
| Eigene Leadgenerierung vs. Portale | 0 | 8 | Kaufvergleich |
| Cost per Lead Photovoltaik | 1 | 14 | Kostenbeitrag |
| Qualifizierte PV-Anfragen | 0 | 30 | Kaufvergleich, Qualitätsprüfung |

Geringe Reichweite allein war kein Löschgrund. Ausschlaggebend war zusätzlich
die Wiederholung von Anfrageweg, Vorqualifizierung, Kosten und Fallbelegen.
Die Hauptseite behält eine kommerzielle Rolle auch ohne bisherigen Suchklick.

Checkfox (21 Klicks, 3.112 Impressionen) und Aroundhome (7/918) bleiben.
Gewerbe-PV (4/1.216) besitzt konkrete Termin-/Projektfragen; „pv termine b2b“
liegt im Export bei Ø-Position 4,83. Wärmepumpe (3/257), DAA (2/75) und
Wattfox (1/156) bleiben eigenständige Zugänge. Kein Ausbau um weitere Anbieter.

## Architektur und Umsetzung

- Drei zentrale Ziele: Angebot, Kaufentscheidung und Kostenrechnung.
- Vier aktive Fachrouten: Kaufvergleich, Kosten, Wärmepumpe und Gewerbe-PV.
- Die Fallstudie bleibt der Beleg mit seinen tatsächlichen Bezugsgrößen.
- Fünf Quellrouten erhalten 301 auf inhaltlich passende Zielabschnitte.
- Provisionierung, Template-/Asset-Listen, Sitemap-Ausschluss, Query-Owner,
  Blog-Ressourcen, Cockpit-Linkgraph und `llms.txt` werden gemeinsam angepasst.
- Bestehende Preise, Form-Endpunkte und Tracking-Hooks bleiben Kanon-Verträge.
- Keine Testanfragen in echte Postfächer als Teil dieser Änderung.

Das Angebot beginnt bei kanonischen 9.999 € netto für eine Produktstrecke,
der Marktcheck kostet kanonische 99 € netto. Die Kopien dieser Beträge gehören
nicht in Runtime-Copy. Werbung, Hosting und weitere vereinbarte Kosten bleiben
sichtbar zusätzlich. Alte Preis-/Ergebnisbehauptungen werden nicht importiert.

## Erwartete Wirkung und Grenzen

Hypothese: Weniger wiederholende Inhalte, verständlichere Rollen und direkte
Links erleichtern den passenden Besuchern die Entscheidung. Daraus folgt
keine belegte Conversion- oder Rankingsteigerung. Die Anfrage wird getrennt
von einer kostenpflichtigen Beauftragung erklärt.

Beobachten: Query-Verteilung und Klicks auf die retained URLs sowie tatsächlich
angenommene, passende Energie-Anfragen. Wegen des niedrigen Volumens zunächst
Richtung und Qualität beurteilen, keine kausale Vorher-/Nachher-Aussage.

Vor Release: Architektur-/PHP-Checks, Canon- und Routing-Invarianten,
Redirect- und Provisionierungsfälle, Editor-Link-/Proof-Reparatur sowie
Browserprüfung der Ziele. Nach Release: alte Pfade, passende Fragmente,
Canonicals, Sitemap und sichtbare Anfragewege prüfen.
