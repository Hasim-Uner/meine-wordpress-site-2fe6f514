# SEO-Recherche-Sprint — Summary (2026-07-03)

## Verbrauchte API-Units

**~17.740 von 35.000 budgetierten Units** (berechnetes Ledger, Details in `../data/units-log.md`).

Wichtig: Der Semrush MCP stellt keinen Balance-Endpoint bereit. Das Ledger rechnet dokumentierten
Preis/Zeile × tatsächlich gelieferte Zeilen. Fehlversuche (NOTHING FOUND) wurden mit 0 angesetzt.
Alle Tabellen sind unter `../data/` persistiert (Roh-CSV unter `../data/raw/`), bevor die Trial ausläuft.

| Phase | Inhalt | Units |
| --- | --- | ---: |
| 1 | Baseline hasimuener.de (4 Keywords) | 40 |
| 2 | SERPs + Seed-Volumen-Batch | 690 |
| 3 | 6 Competitor-Domains (490 Zeilen) | 4.900 |
| 4 | Keyword-Universum (Related/Fullsearch/KD) | 12.110 |
| **Σ** | | **17.740** |

## Top-5-Empfehlungen

1. **`/solar-leads-kaufen-alternative/` schärfen (P1, Aufwand S–M):** Alle Kauf-Keywords des Money-Clusters (photovoltaik/pv/solar leads kaufen, ~600 kumuliertes Volumen) haben **KD 0–9 bei CPC 10–16 €**. Die Seite rankt bereits Pos. 48 für „pv leads kaufen". Head-Term „photovoltaik leads" (210) in Title/H1 aufnehmen, interne Links aus Homepage-/Money-Hub prüfen. Das ist der schnellste messbare Hebel.
2. **Wärmepumpen-Leads-Seite bauen (P1, einzige echte Money-Lücke):** „wärmepumpen leads" (90, **KD 0**, CPC 11,83 €) + „wärmepumpen leads kaufen" (20, KD 0). Die Positionierung verspricht Solar UND Wärmepumpe — die Keyword-Abdeckung liefert bisher nur Solar. Wettbewerb (smart11, leadfluss) ist schwach.
3. **Portal-Posts auf echte Suchbegriffe drehen (P1):** Die existierenden Markteinordnungen zielen mit „Markteinordnung" an dem vorbei, was gesucht wird: „aroundhome erfahrungen" (480), „aroundhome erfahrung/seriös/kosten" (750), „wattfox erfahrungen" (110) + „wattfox leads kaufen/erfahrungen"-Longtails (KD 0). In der Wattfox-SERP rankt kein einziger Dienstleister — offene Flanke für die Anbieter-Perspektive.
4. **Checkfox als fünftes Portal aufnehmen (P1):** Neu identifizierter Portal-Gegner mit dem größten Erfahrungs-Suchvolumen des Datensatzes: „checkfox erfahrungen" + „checkfox seriös" je 590 (KD 16–20), „checkfox solar" 170, „checkfox wärmepumpe" 50. Im Repo bisher komplett unbehandelt.
5. **`/wordpress-agentur-hannover/` von Pos. 11 in die Top 10 schieben (P2, Quick Win):** 480 Volumen, KD 10, aktuell die einzige Traffic-Quelle der Domain. Interne Verlinkung + Title-CTR-Test genügen vermutlich für den Sprung.

## Auffälligkeiten

- **Die Nische ist volumenarm, aber extrem kaufstark:** 14 von 23 Seeds haben kein messbares Volumen (gesamtes Wärmepumpen-Wording im Singular, White-Label, Local, die meisten Problem-Aware-Begriffe). Die messbaren Money-Keywords kompensieren das mit CPCs bis 16 € und KD nahe 0 — ein ungewöhnlich günstiges Verhältnis.
- **Die Money-SERPs gehören Lead-Verkäufern:** Enpal verkauft überschüssige Leads per Shop (Pos. 1–2), dazu powerleads, pvleads, leadsnavigator & Co. Wer den Kauf-Intent bedient und dann umpositioniert („kaufen vs. eigenes System"), ist dort fast allein mit dieser Botschaft — nur anfragenfluss.de fährt dieselbe Strategie.
- **anfragenfluss.de ist die Content-Blaupause und der direkteste Wettbewerber:** rankt mit Make-or-Buy-, Anbieter-Vergleichs- und „Exklusiv vs. Portal-Leads"-Artikeln auf Pos. 6–9 der Money-SERPs — exakt die Positionierung von hasimuener.de, nur früher dran. Das bestehende 8-Sub-Page-Cluster im Repo ist strukturell bereits die Antwort; es fehlt Schärfung + Autorität, nicht Architektur.
- **leadfluss.de gewinnt seinen Traffic fast nur über ein LinkedIn-Formatierungs-Tool** (~80 % der Top-Keywords irrelevant für die Nische) — deren Solar-Rankings kommen aus wenigen Blog-Artikeln. Kein Grund zur Ehrfurcht.
- **Intent-Falle im Portal-Cluster:** „erfahrungen/seriös"-Suchen mischen Verbraucher und Anbieter. Die B2B-Sub-Varianten („aroundhome kosten für handwerker", „wattfox leads kaufen") haben KD 0 und sind die eigentlichen Ziel-Keywords; die großen Begriffe liefern Reichweite und Themenautorität obendrauf.
- **GSC-Daten fehlen als zweite Wahrheitsquelle:** Im Repo liegen keine Search-Console-Exports (nur der SEO-Cockpit-Code). Falls Exports existieren, bitte nachliefern — der Abgleich Semrush ↔ GSC steht noch aus.
- **Semrush-Datenlücken:** phrase_organic (SERP) liefert für die meisten Long-Tail-Seeds NOTHING FOUND, obwohl Volumendaten existieren. Positionen unterhalb der Messschwelle bleiben unsichtbar — reale Nachfrage ist vermutlich höher als gemessen (siehe „solar marketing agentur": Volumen 0, aber CPC 6,07 €).

## Dateiübersicht

```
seo-research/2026-07/
├── data/
│   ├── units-log.md                  # Unit-Ledger (berechnet)
│   ├── hasimuener-organic.json       # Phase 1: eigene Rankings
│   ├── seeds-volumes.json            # Phase 2: Volumen aller 23 Seeds
│   ├── serp-money-seeds.json         # Phase 2: SERPs Money-Keywords
│   ├── serp-wattfox-erfahrungen.json # Phase 2: SERP Wattfox
│   ├── competitors.md                # Phase 2: Wettbewerber + SERP-Gegner
│   ├── comp-<domain>.json            # Phase 3: 6 × Competitor-Keywords (strukturiert)
│   ├── keywords-master.csv           # Phase 4: 186 Keywords, dedupliziert, mit KD/Intent/Cluster
│   └── raw/                          # Roh-CSVs aller Abfragen (Trial-sicher)
└── reports/
    ├── gap-analyse.md                # Phase 5: Cluster ↔ Seiten-Mapping
    ├── keyword-map.md                # Phase 6.1: Keywords je Cluster
    ├── content-plan.md               # Phase 6.2: priorisierter Plan P1–P3
    └── summary.md                    # Phase 6.3: dieses Dokument
```
