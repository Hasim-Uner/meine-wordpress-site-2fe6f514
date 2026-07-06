# GSC ↔ Semrush Abgleich (SEO-Cockpit-Exports vom 2026-07-03)

Datenbasis: `../data/gsc/gsc-export-7d-2026-07-03.csv` und `gsc-export-28d-2026-07-03.csv`
(zweite Wahrheitsquelle, die im Sprint noch offen war). Abgleich gegen `../data/keywords-master.csv`.

## Gesamtbild (28 Tage)

**2 Klicks auf der ganzen Domain** — „pattensen" → Homepage (Pos. 1) und **„wattfox" → /solar-leads-kaufen-alternative/ (Pos. 9)**.
Das Problem ist nicht CTR, sondern Position: Fast alles rankt auf Seite 2–7. Impressionen nach Seite:

| Seite | Klicks | Impressionen 28d | Befund |
| --- | ---: | ---: | --- |
| /wordpress-agentur-hannover/ | 0 | 715 | Reichweiten-Asset Nr. 1; „wordpress agentur hannover" 342 Impr., Pos. 30,5 → 14,0 (28d) → **11,5 (7d)** — starker Aufwärtstrend |
| /server-side-tracking-b2b/ | 0 | 288 | Nachfrage existiert, aber Pos. 60–90 — nicht konkurrenzfähig |
| /b2b-solar-leads/ | 0 | 207 | Versteckte Perle: **„pv termine b2b" 86 Impr., Pos. 9,65** (Seite 1!) |
| /solar-leads-kaufen-alternative/ | **1** | 34 | Money-Cluster bewegt sich: „solar leads kaufen" Pos. 41 → 33; Portal-Winkel liefert den einzigen Sach-Klick (wattfox, Pos. 9) |
| /solar-waermepumpen-leadgenerierung/ | 0 | 11 | **Missstand: Die zentrale Money-Page ist quasi unsichtbar** — „leadgenerierung photovoltaik" nur Pos. 50 |
| Rest (12 Seiten) | 0 | ≤ 8 | Cluster zu jung für Signal — kein Löschkandidat, aber auch kein Invest |

## Muster / Missstände

1. **Homepage sendet laut Export noch die ALTE Positionierung:** `seo_title` = „WordPress Growth Architect | Hasim Üner Hannover", Description „Ihr strategischer Partner für digitales Wachstum…". Laut LIVE_STATUS sollten Title/Description längst auf Anfrage-Systeme/Marktcheck umgestellt sein. Entweder liest das Cockpit ein veraltetes Editor-Feld, oder live wird wirklich noch der alte Title ausgespielt. **→ Manuell prüfen: View-Source auf hasimuener.de + ggf. Editor-Feld im Admin leeren.**
2. **Kannibalisierung „leadgenerierung photovoltaik":** /b2b-solar-leads/ (14 Impr., Pos. 67) und die Money-Page (9 Impr., Pos. 50) konkurrieren. Die Money-Page muss den Begriff besitzen — Ankertexte, die „Leadgenerierung Photovoltaik" enthalten, konsequent auf die Money-Page zeigen lassen.
3. **„wärmepumpen leads" bekam schon 13–15 Impressionen** — Google rankte mangels Zielseite die Solar-Intercept-Page (Pos. 42). Die gestern live gestellte /waermepumpen-leads/ ist exakt die fehlende Antwort; nach Indexierung sollte sie diese Impressionen übernehmen.
4. **Portal-Fokus validiert:** Der einzige inhaltliche Klick kam über „wattfox" (Pos. 9). Dazu Impressionen für „aroundhome" (Pos. 37) und — neu — **„einfachsolar portal"** (5 Impr. über drei Seiten): EinfachSolar ist ein weiterer Portal-Name mit Suchnachfrage, den Semrush nicht zeigte. → Kandidat für die Portal-Post-Reihe.
5. **Non-Target-Rauschen (bewusst NICHT bespielen):** „woocommerce agentur hannover" (95 Impr., bereits als non-target markiert), „elevar server side tracking" (Shopify-Tool-Brand), „server side tracking regensburg" (Geo-Fremdsignal), „wordpress wartung/wartungsvertrag/schulung hannover" (Leistungen retired). Keine Optimierung, keine neuen Seiten dafür.
6. **Semrush-Blindspots bestätigt:** GSC zeigt kaufnahe Queries unter der Semrush-Messschwelle: „pv termine b2b" (86!), „photovoltaik b2b lösung" (61), „energie leads b2b", „pv leads gewerbe", „photovoltaik leads gewerbe", „leadgenerierung solar". Die reale Nachfrage im B2B-/Gewerbe-Winkel ist größer als die Semrush-Zahlen suggerieren.

## Weglassen / Löschen?

**Keine Seite löschen.** Der Sub-Page-Cluster ist wenige Wochen alt, stützt die Money-Pages über interne Links und kostet nichts. Aber klare Nicht-Invest-Liste:
- /server-side-tracking-b2b/: Nachfrage da, aber generischer Wettbewerb (Pos. 60–90) — kein weiterer Ausbau, bis der Solar-Kern steht. Später prüfen: Konsolidierung mit dem alten Blogpost /server-side-tracking-gtm/ (beide ranken schwach fürs selbe Cluster).
- /ga4-tracking-setup/, /design-ist-mehr-als-aesthetik/: Altlasten mit ~0 Signal — liegen lassen.
- Alle Non-Target-Queries aus Punkt 5: ignorieren.

## Fokus-Ableitung (Reihenfolge)

1. **Portal-Posts (P1 #3–5) starten** — durch GSC bestätigt (wattfox-Klick, aroundhome-/einfachsolar-Impressionen). EinfachSolar als fünften Kandidaten aufnehmen.
2. **Zwei Mini-Fixes nebenbei:** (a) Homepage-Title/Description-Missstand prüfen/beheben, (b) „PV-Termine kaufen"-Semantik als FAQ/Abschnitt auf /b2b-solar-leads/ (Pos. 9,65 bei 86 Impr. — dichtester Klick-Kandidat der ganzen Domain).
3. **Nichts Neues für Tracking/WooCommerce/Wartung bauen.**
4. Wirkung von Paket 1+2 abwarten (Deploy war gestern) — nächster Export in 2–4 Wochen zeigt, ob /waermepumpen-leads/ die Impressionen übernimmt und das Kaufen-Cluster steigt.
