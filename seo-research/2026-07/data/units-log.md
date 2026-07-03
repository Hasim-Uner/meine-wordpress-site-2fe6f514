# Semrush API Units Log — Session 2026-07-03

**Wichtig:** Der Semrush MCP stellt KEINEN Balance-Endpoint (`countapiunits`) bereit.
Deshalb wird hier ein berechnetes Ledger geführt: dokumentierter Preis pro Zeile × tatsächlich zurückgegebene Zeilen.
Session-Budget: 35.000 Units (Hard Stop), 15.000 Reserve.

## Preistabelle (aus MCP-Discovery)

| Report | Preis |
| --- | --- |
| domain_organic | 10 Units/Zeile |
| phrase_organic | 10 Units/Zeile |
| phrase_these | 10 Units/Zeile |
| phrase_fullsearch | 20 Units/Zeile |
| phrase_related | 40 Units/Zeile |

## Ledger

| # | Phase | Call | Zeilen | Units | Kumuliert |
| --- | --- | --- | --- | --- | --- |
| 1 | 1 | domain_organic hasimuener.de (de, limit 200) | 4 | 40 | 40 |
| 2 | 2 | phrase_organic "photovoltaik leads kaufen" (limit 20) | 20 | 200 | 240 |
| 3 | 2 | phrase_organic 4 Money-Seeds → NOTHING FOUND | 0 | 0 | 240 |
| 4 | 2 | phrase_organic wattfox alternative / pv kunden gewinnen / solar marketing agentur → NOTHING FOUND | 0 | 0 | 240 |
| 5 | 2 | phrase_these Batch 23 Seeds | 9 | 90 | 330 |
| 6 | 2 | phrase_organic "pv leads" (limit 20) | 20 | 200 | 530 |
| 7 | 2 | phrase_organic "wattfox erfahrungen" (limit 20) | 20 | 200 | 730 |
| 8 | 2 | phrase_organic aroundhome erf. handwerker / leadgen. photovoltaik → NOTHING FOUND | 0 | 0 | 730 |
| 9 | 3 | domain_organic anfragenfluss.de | 49 | 490 | 1.220 |
| 10 | 3 | domain_organic leadfluss.de | 200 | 2.000 | 3.220 |
| 11 | 3 | domain_organic hntmedia.de | 30 | 300 | 3.520 |
| 12 | 3 | domain_organic andreas-may.com | 34 | 340 | 3.860 |
| 13 | 3 | domain_organic coform.de | 109 | 1.090 | 4.950 |
| 14 | 3 | domain_organic smart11.de | 68 | 680 | 5.630 |
| 15 | 4 | phrase_related "pv leads" (limit 50) | 7 | 280 | 5.910 |
| 16 | 4 | phrase_related "photovoltaik leads kaufen" (limit 50) | 9 | 360 | 6.270 |
| 17 | 4 | phrase_related "leads kaufen" (limit 50) | 42 | 1.680 | 7.950 |
| 18 | 4 | phrase_related "wattfox erfahrungen" (limit 30) | 12 | 480 | 8.430 |
| 19 | 4 | phrase_related leadgen. photovoltaik / pv kunden gewinnen → NOTHING FOUND | 0 | 0 | 8.430 |
| 20 | 4 | phrase_related "wärmepumpe vertrieb" (limit 30) | 9 | 360 | 8.790 |
| 21 | 4 | phrase_fullsearch "solar leads" (limit 50) | 51 | 1.020 | 9.810 |
| 22 | 4 | phrase_fullsearch "photovoltaik leads" (limit 50) | 20 | 400 | 10.210 |
| 23 | 4 | phrase_fullsearch "wärmepumpe leads" (limit 30) | 4 | 80 | 10.290 |
| 24 | 4 | phrase_fullsearch "wattfox" (limit 30) | 30 | 600 | 10.890 |
| 25 | 4 | phrase_fullsearch "aroundhome" (limit 30) | 30 | 600 | 11.490 |
| 26 | 4 | phrase_fullsearch "selfmade energy" (limit 20) | 15 | 300 | 11.790 |
| 27 | 4 | phrase_fullsearch "daa leads" (limit 20) | 5 | 100 | 11.890 |
| 28 | 4 | phrase_fullsearch "leadgenerierung" (limit 50) | 50 | 1.000 | 12.890 |
| 29 | 4 | phrase_fullsearch "kunden gewinnen" (limit 30) | 30 | 600 | 13.490 |
| 30 | 4 | phrase_fullsearch "solar marketing" (limit 20) | 20 | 400 | 13.890 |
| 31 | 4 | phrase_fullsearch "photovoltaik marketing" (limit 20) | 20 | 400 | 14.290 |
| 32 | 4 | phrase_fullsearch "checkfox" (limit 20) | 20 | 400 | 14.690 |
| 33 | 4 | phrase_kdi Batch 61 Keywords | 61 | 3.050 | 17.740 |

## Status (final)

- Verbraucht (berechnet): **17.740 / 35.000** — Budget zu ~51 % genutzt, 15.000er-Reserve unangetastet.
- Annahme: Fehlversuche (NOTHING FOUND) kosten 0 Units, da 0 Zeilen geliefert.
- Nicht abgefragt (bewusst): historische Reports (5x Kosten), Traffic/Trends (separates Abo), Backlinks, Keyword-Ideen für Seeds ohne Volumen.
