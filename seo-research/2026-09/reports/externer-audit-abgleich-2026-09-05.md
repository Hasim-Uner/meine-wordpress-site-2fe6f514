# Abgleich: externer SEO-Audit vs. Exportdaten (2026-09-05)

Prüfung eines extern erstellten Audits (Gemini, Stand September 2026) gegen die
SEO-Cockpit-Exporte vom 2026-09-01 und gegen den Repo-Stand.

**Datenbasis:** `../data/gsc/gsc-export-{7,28,90}d-2026-09-01.csv`
**Fenster:** 7d `2026-08-26…09-01` · 28d `2026-08-05…09-01` · 90d `2026-06-04…09-01`
**Werkzeuge:** `agents/skills/seo-drift/scripts/drift-report.sh`

## Kernaussage

Die Bestandsaufnahme des externen Audits stimmt. Die drei abgeleiteten
Sofort-Maßnahmen sind **nicht umsetzbar**: zwei sind bereits ausgeliefert, eine
ist sachlich falsch. Die tatsächlich größte belegte Bewegung im Export — ein
Like-for-Like-Rankingverlust auf zwei am 2026-08-19/21 umgebauten Templates —
kommt im externen Audit nicht vor.

Die Priorität des externen Audits ist gegenüber der Datenlage **invertiert**:
Es benennt die beiden stabilen Seiten als größte Hebel und die beiden
abstürzenden Seiten als Nebenschauplatz.

## Was stimmt

| Aussage | Status |
|---|---|
| 8 Seiten tragen das Suchvolumen | **Belegt** — 85,6 % der Impressionen (90d), 63 Seiten gesamt |
| `checkfox seriös`: 785 Impr., Pos. 8,58, 0 Klicks (90d) | **Belegt** |
| `/server-side-tracking-b2b/`: 764 Impr., 0 Klicks | **Belegt** |
| `woocommerce agentur hannover`: 197 Impr., Pos. 21,3 | **Belegt** |
| `/b2b-solar-leads/` rankt breit und tief | **Belegt** — 54 Query-Zeilen (nicht 55), fast alle Pos. 40+ |
| Mehrfach-URLs im Solar-Cluster | **Belegt**, aber ohne Klickverlust — siehe unten |

## Was nicht stimmt

### 1. „Meta-Title härten" — bereits am 2026-08-19 geschehen

Die Titel von `/checkfox-…/` und `/aroundhome-…/` wurden in `cd1764e`
(2026-08-19) in `blocksy-child/inc/seo-meta.php` genau auf diese Diagnose hin
neu gesetzt; die Begründung steht als Kommentar im Forced-Map-Eintrag.

Der Audit liest die **90-Tage-CTR** und schließt auf ein Titelproblem. Das
90d-Fenster beginnt am 2026-06-04 und wird zu rund 85 % vom **alten** Titel
dominiert. Nur das 7d-Fenster zeigt ausschließlich den neuen Titel.

Der neue Titel wirkt bereits auf die Position:

| Seite | Pos. 90d | Pos. 28d | Pos. 7d |
|---|---:|---:|---:|
| `/checkfox-solar-waermepumpe-einordnung/` | 8,4 | 8,4 | **6,5** |
| `/aroundhome-solar-einordnung/` | 9,2 | 8,2 | **4,8** |

Der Drift-Report stuft beide Seiten als stabil ein (Ø +0,7 bzw. −0,8 Position).
**Ein zweiter Titelwechsel 17 Tage nach dem ersten macht den ersten
unmessbar.** Der vorgeschlagene Ersatztitel behält zudem die Frageform bei, die
der Audit selbst als Ursache benennt.

> Nächster Schritt: nichts ändern, Export 2026-10-01 abwarten. Erst dann ist
> die Titeländerung von 2026-08-19 bewertbar.

### 2. „Solar-Kannibalisierung bereinigen, 301 auf `/b2b-solar-leads/`"

Drei Einwände:

1. **Bereits geschehen.** `16fafa6` (2026-09-04, #257) hat
   `/solar-leads-kaufen-lohnt-sich/` per 301 auf
   `/solar-leads-kaufen-alternative/` konsolidiert — drei Tage nach dem
   Exportende, deshalb noch in den Daten sichtbar.
2. **Richtung invertiert.** `docs/seo/query-ownership.csv` weist
   `solar leads kaufen`, `photovoltaik leads kaufen`, `pv leads kaufen` und
   `leadgenerator solar` `/solar-leads-kaufen-alternative/` zu.
   `/b2b-solar-leads/` besitzt nur Gewerbe-PV-Intent. Der Export bestätigt die
   Registry: Die Owner-URL rankt bei **jeder** geteilten Query besser.

   | Query | Owner `/solar-leads-kaufen-alternative/` | Eindringling `/b2b-solar-leads/` |
   |---|---:|---:|
   | `solar leads kaufen` | **40,6** | 72,1 |
   | `leads kaufen photovoltaik` | **48,2** | 78,0 |
   | `pv leads kaufen` | **48,7** | 70,5 |
   | `photovoltaik leads kaufen` | **46,0** | 61,2 |

   Der Vorschlag würde den besser rankenden Owner auf den schlechter
   rankenden Eindringling umleiten.
3. **Regelverstoß.** `AGENTS.md`: *„Do not redirect or de-index a query-owning
   page just to simplify CTA routing."*

Zur Sache selbst: Alle geteilten Queries liegen bei **0 Klicks auf beiden
URLs**. Ein zweites, schwächeres Ergebnis auf Position 70+ verdrängt den Owner
nicht — das ist Rauschen, kein Klickverlust. `/b2b-solar-leads/` grenzt sich in
der eigenen FAQ bereits explizit vom Kauf-Fall ab und verlinkt auf den Owner
(`page-b2b-solar-leads.php:201`).

### 3. „Interne Links fehlen fast komplett, isolierte Inseln" — falsch

Beide Portal-Artikel verlinken bereits mit hartem Ankertext auf die
Angebotsseiten:

- `template-parts/checkfox-decision-cockpit.php` → `/solar-leads-kaufen-alternative/`,
  `/waermepumpen-leads/`, `/eigene-leadgenerierung-vs-portale/`, Case Study,
  Marktcheck (2×)
- `template-parts/aroundhome-decision-cockpit.php` → `/solar-leads-kaufen-alternative/`,
  `/cost-per-lead-photovoltaik/`, `/eigene-leadgenerierung-vs-portale/`,
  `/case-study-solar-leadgenerierung/`, Marktcheck

### 4. Vermischte Zeitfenster bei `/wordpress-agentur-hannover/`

Der Audit nennt „Position 22,7" (90d) und „−101 Impressionen im 28-Tage-Trend"
in einem Satz. Der 28d-Export zeigt Pos. **33,7** (Vorperiode 33,6) und
**−39** Impressionen. Position stabil, Impressionen rückläufig — eine andere
Diagnose als die genannte.

### 5. „Homepage rankt für lokalen Nonsense"

`architekten pattensen` hat **1 Impression** in 90 Tagen. Die Homepage hat
insgesamt 183 Impressionen. Einzelimpressionen tragen keine Aussage.

## Der eigentliche Befund: Like-for-Like-Regression auf zwei Templates

`drift-report.sh` meldet für das 28d-Fenster 54 × `VERSCHWUNDEN` und
29 × `ABSTURZ`. Die beiden schwersten Fälle sind zugleich die beiden Templates,
die im Fenster umgebaut wurden.

Der Positionsschnitt einer Seite kann allein dadurch fallen, dass neue
Long-Tail-Queries dazukommen. Deshalb hier nur Queries, die in **beiden**
Perioden Impressionen hatten (impressionsgewichtete Position):

| Seite | Queries in beiden Perioden | Position vorher → jetzt | Impressionen |
|---|---:|---|---|
| `/server-side-tracking-b2b/` | 7 | 56,1 → **83,6** (+27,6) | 189 → 117 |
| `/b2b-solar-leads/` | 35 | 37,4 → **47,5** (+10,1) | 435 → 426 |

Das ist kein Mittelwert-Artefakt, sondern ein echter Verlust auf gehaltenen
Queries. Schwerster Einzelfall: `server side tracking agentur`
**52,6 → 86,8** (+34,2), 112 → 83 Impressionen — die Hauptquery der Seite.

**Zeitliche Korrelation** (Korrelation, nicht Ursache — so auch der Report):

- `blocksy-child/page-server-side-tracking-b2b.php` — **neu angelegt** am
  2026-08-19 (`cd1764e`, 1.059 Zeilen). Die Route existierte vorher (216
  Impressionen in der Vorperiode), wurde aber nicht aus dem Repo bedient.
- `blocksy-child/page-b2b-solar-leads.php` — geändert am 2026-08-21 (`1a639c6`).

### Nebenbefund: Ownership ohne On-Page-Deckung

`docs/seo/query-ownership.csv` weist `/server-side-tracking-b2b/` die Queries
`server side tracking agentur`, `serverside tracking agentur` und
`agentur server side tracking` zu. Das Template enthält das Wort **„Agentur"
kein einziges Mal**.

Das ist **kein Auftrag, es einzubauen.** `docs/standards/BRAND_AND_COPY.md:183`
hält fest, dass der Versuch mit benachbarten Kategoriebegriffen 2026 die
Money-Query verschlechtert hat, und Zeile 167 verbietet
`Performance-Marketing-Agentur` als Unternehmensidentität. Die öffentliche
Rolle ist Freelancer, nicht Agentur.

Damit steht ein Ownership-Eintrag ohne mögliche On-Page-Deckung in der
Registry. Zu klären ist die Zuweisung, nicht die Copy.

## Priorität

### Critical

1. **`/server-side-tracking-b2b/`: Rankingverlust nach dem Template-Neubau
   prüfen.** +27,6 Positionen Like-for-Like, Hauptquery von 52,6 auf 86,8. Die
   Route hat keine Repo-Vorgeschichte zum Diffen — der Vorzustand lag außerhalb
   des Repos. Nächster Schritt ist deshalb `seo-live-qa` gegen die Live-URL
   (Auslieferung, Canonical, Indexierung, Content-Umfang gegen vorher), nicht
   ein zweiter Rewrite.

### High leverage

2. **`/b2b-solar-leads/`: +10,1 Positionen Like-for-Like über 35 Queries**,
   30 Queries mit Verlusten. Gegen `1a639c6` prüfen.
3. **Ownership-Konflikt `… tracking agentur`** entscheiden: Query abgeben,
   ausschließen (`docs/seo/keyword-exclusions.csv`) oder Zuweisung begründet
   halten. Keine Copy-Änderung.

### Polish

4. `checkfox` / `aroundhome`: **liegen lassen** bis Export 2026-10-01.
5. Mehrfach-URLs im Solar-Cluster beobachten. Aktuell 0 Klicks auf beiden
   Seiten jeder geteilten Query — keine Maßnahme belegt.

### Agent tasks / repo tasks

- `seo-research/2026-09/data/keywords-master.csv` fehlt; ohne sie läuft
  `gap-report.sh` für diese Periode nicht. Braucht echte Volumendaten aus einer
  Keyword-Recherche — nicht schätzen.
- `drift-report.sh` meldet für `/checkfox-…/` und `/aroundhome-…/`
  „kein Template im Repo — Copy liegt im Editor". Titel und Description beider
  Routen stammen aber aus dem Forced-Map in `inc/seo-meta.php`. Die
  Template-Auflösung des Reports kennt diesen Pfad nicht.

## Manual WordPress tasks

Keine. Alle offenen Punkte sind Repo- oder Analyse-Aufgaben.
