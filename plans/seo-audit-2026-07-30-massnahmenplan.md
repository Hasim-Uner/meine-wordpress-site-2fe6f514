# SEO-Plan 2026-07-30 — Prüfung der Codex-Analyse + Umsetzungsplan

## Context

Hasim hat eine SEO-Analyse von Codex (inkl. Prompt) vorgelegt und will wissen:
stimmt sie, geht es besser, und wie sieht der Plan aus. Umsetzung soll Codex
machen (günstiger). Datenbasis: SEO-Cockpit-Exporte 7d und 28d vom 30.07.2026.

Ich habe beide Exporte selbst durchgerechnet und beide von Codex behaupteten
Repo-Bugs im Code verifiziert. Ergebnis unten.

---

## 1. Verdict zur Codex-Analyse

**Codex hat in allen prüfbaren Punkten recht.** Beide Tooling-Befunde sind echt,
die Zahlen stimmen exakt, die Grundstrategie ("keine neue Seitenwelle, erst
Ownership sortieren") ist richtig. Das ist eine gute Analyse.

### Verifiziert und bestätigt

| Behauptung | Status | Beleg |
|---|---|---|
| 28d: 1.420 Impr., 3 Klicks, 0,21 % CTR | ✅ exakt | Nachgerechnet über beide CSV |
| 7d: 462 Impr., 0 Klicks | ✅ exakt | Vorperiode 215 Impr. |
| `gap-report.sh` nimmt beste historische Position | ✅ **echter Bug** | `gap-report.sh:99–114`: `glob("gsc/*.csv")` + `if pos < prev[1]` — ohne Filter auf `range_days`/`current_end` |
| Hannover meldet ~Pos. 11 statt 26,6 | ✅ exakt | Repo-Exporte: 7d-Datei Pos. 11,46 / 28d-Datei 14,03 → `min` = 11,46. Aktuell real: 26,64 |
| Cockpit exportiert Frontpage-Alttitel | ✅ **echter Bug** | `hu_get_homepage_title()` = „Anfragesysteme für Solar & Wärmepumpe" (`seo-meta.php:30`), Export zeigt „WordPress Growth Architect \| Hasim Üner Hannover" |
| „wärmepumpen leads" auf 4 URLs | ✅ | 79 Impr. / 4 URLs, Gewinner `/waermepumpen-leads/` Pos. 29,6 |
| `/b2b-solar-leads/` rankt für „leadgenerierung photovoltaik" | ✅ | Pos. 46,2 / 34 Impr., Registry-Owner wäre `/solar-waermepumpen-leadgenerierung/` |

**Mechanik des Frontpage-Bugs** (Codex hat ihn richtig vermutet, hier die genaue
Stelle): Das Frontend erzwingt den Titel über `is_front_page()`
(`seo-meta.php:962`, `:1223`). Das Cockpit löst dagegen über `post_id` auf —
`nexus_get_seo_cockpit_post_seo_context()` → `seo-cockpit-insights.php:438` liest
`hu_get_stored_seo_value($post_id, 'seo_title', …)`. Die `is_front_page()`-
Verzweigung wird nie erreicht, die Startseite steht auch nicht in
`hu_get_forced_singular_seo_map()`. Also gewinnt das alte gespeicherte Meta.
Gleiches Problem betrifft den Blog-Index (`hu_get_blog_archive_title()`).

### Wo ich Codex widerspreche

**a) Checkfox: „nicht blind auf CTR optimieren" ist die halbe Antwort.**
Codex' Begründung stimmt (breite Verbraucher-Suchen), die Schlussfolgerung ist zu
passiv. Die Zahlen: der Checkfox/Aroundhome-Cluster ist **das komplette
Seite-1-Inventar der Domain** — 363 Impressionen auf Ø Pos. 8, davon 1 Klick.
`checkfox seriös` allein: 174 Impr., Pos. 8,39, **0 Klicks**.

Der wahrscheinlichste Grund steht im Title selbst: *„Checkfox Erfahrungen: Ist das
Portal seriös **für Betriebe**?"* — wer als Hausbesitzer „ist checkfox seriös"
sucht, liest „für Betriebe" und klickt weiter. Der Title schließt die Mehrheit der
Suchintention aktiv aus. Das ist keine unvermeidbare Zielgruppen-Mismatch, das ist
ein selbstgebauter Filter. Klicks kosten organisch nichts und stützen den ganzen
Cluster; die Betriebs-Abzweigung gehört in den Text, nicht in den Title.

**b) `/wordpress-agentur-hannover/`: Codex diagnostiziert nicht, warum.**
Codex sagt „Ranking deutlich gefallen, nur investieren wenn es Anfragen bringt."
Das ist keine Diagnose. Der Befund: **alle 11 Queries** dieser URL sind gleichmäßig
13–28 Positionen gefallen, Impressionen fast halbiert (343 vs. vorher deutlich
mehr; „wordpress hannover" 51 statt 130, Pos. 37,8 statt 19,3).

Eine gleichmäßige Demotion über *alle* Queries einer URL ist kein Zufall. Ich habe
die naheliegendste Ursache geprüft und **ausgeschlossen**: interne Verlinkung ist
intakt und stark (Footer, `related-content.php`, `category.php`, `single.php`,
`front-page.php`, `404.php`). Bleibt als Erklärung die Solar/SHK-Neuausrichtung —
die thematische Relevanz der Domain für „WordPress Agentur Hannover" ist verwässert
— plus 632 Wörter, die dünnste Money-Page im Export. Das ist ein **bewusst bezahlter
Preis der Positionierung**, kein Bug. So sollte es auch dokumentiert werden.

### Was Codex komplett übersehen hat

**c) `wattfox` — hier liegt der einzige verlorene Klick der Domain.**
Registry: `/wattfox-solar-leads-einordnung/` besitzt die Query. Real rankt
`/solar-leads-kaufen-alternative/` auf Pos. 30,67. Vorperiode: **Pos. 9 mit 1 Klick**.
Die Seite `/wattfox-solar-leads-einordnung/` existiert im Repo überhaupt nicht
(kein Template, kein Content). Codex erwähnt wattfox mit keinem Wort.

**d) Registry-Fäule: 7 Einträge ohne jedes Signal, 2 davon Phantom-Seiten.**
`docs/seo/query-ownership.csv` weist Queries an URLs zu, die es nicht gibt:
- `/daa-photovoltaik-leads-einordnung/` — kein Template, kein Content
- `/wattfox-solar-leads-einordnung/` — kein Template, kein Content

Ohne Signal im 28d-Export außerdem: `aroundhome`, `cost per lead photovoltaik`,
`kunden gewinnen solarteure`, `lead funnel solar`, `portal leads vergleich`,
`qualifizierte pv anfragen`, `server side tracking`, `solar leads kosten`,
`leadgenerierung waermepumpe`. **Das Intent-Gate validiert damit gegen
Phantom-Owner** — es gibt grünes Licht oder blockiert auf Basis von Zeilen, hinter
denen nichts steht. Das entwertet den Gate-Mechanismus, den Codex' Prompt gleichzeitig
als Qualitätssicherung voraussetzt.

**e) `/b2b-solar-leads/` ist die eigentliche Ursache fast aller Konflikte.**
Codex behandelt jede URL einzeln. Tatsächlich taucht `/b2b-solar-leads/` in **8 von
14** Kannibalisierungs-Clustern auf, fast immer als *Nicht*-Owner, der den
vorgesehenen Owner überholt: `photovoltaik leads`, `leadgenerierung photovoltaik`,
`photovoltaik leads kaufen`, `pv leads kaufen`, `solar leads kaufen`, `solar leads`,
`leads solar`, `leads kaufen photovoltaik`. Die Seite ist zur Auffangseite für das
gesamte Solar-Lead-Vokabular geworden — weil die vorgesehenen Owner schwach oder
unsichtbar sind. Das ist ein struktureller Befund, keine Sammlung von Einzelfällen.

**f) `/solar-waermepumpen-leadgenerierung/` produziert null.**
Die Seite besitzt laut Registry 3 Head-Terms und hat **in beiden Zeiträumen für
keine einzige Query auch nur eine Impression**. Sie hat aber Template
(`page-solar-waermepumpen-leadgenerierung.php`), Forced-SEO-Meta
(`seo-meta.php:242`) und starke interne Verlinkung. Codex schreibt „ist indexiert" —
das ist aus den Exporten **nicht belegbar** (keine Zeile = keine `noindex`/
`in_sitemap`-Info). Muss live geprüft werden, bevor irgendetwas darauf aufbaut.

**g) Das Cockpit zählt Bindestrich-Varianten als getrennte Queries.**
`photovoltaik leads` / `photovoltaik-leads`, `pv leads` / `pv-leads`,
`server side tracking dsgvo` / `server-side tracking dsgvo`. Kosmetisch, aber es
bläht die Kannibalisierungs-Liste auf und verfälscht Impressions-Summen pro Intent.

### Zum Codex-Prompt

Der Prompt ist handwerklich gut (Datentrennung 7d/28d, Ownership-Gate, klare
Nicht-Ziele). Ein struktureller Fehler: er lässt Codex **noch einmal auditieren**
(„Noch keine Änderungen implementieren. Zuerst einen Maßnahmenplan.") — genau das
ist dieses Dokument. Für die Umsetzung braucht Codex einen Prompt mit Branch,
Akzeptanzkriterien und Tests, nicht mit Analyseauftrag. Außerdem fehlen: Branch-
Vorgabe, CI-Gates (`check-german-copy.sh`, `validate-architecture.sh`), und der
Live-Verifikationsschritt setzt Netzzugang voraus, den Codex evtl. nicht hat.

---

## 2. Plan

Annahmen (unbeantwortet geblieben, so umgesetzt):
- Checkfox: **Klicks holen + B2B-Pfad im Text** (Begründung oben unter a).
- Hannover: **halten, nicht ausbauen**, Verlust als Positionierungspreis dokumentieren.
- Arbeitsteilung: **Codex macht Tooling + Registry, Copy bleibt bei mir.**

### P0 — Messgrundlage reparieren (Codex)

Solange `gap-report.sh` Bestpositionen aus vermischten Snapshots meldet, ist jede
Folgeentscheidung auf Sand gebaut. Das kommt zuerst.

1. **`agents/skills/seo-agent/scripts/gap-report.sh:97–114`**
   Statt „bestes `pos` über alle Dateien": nur den neuesten Snapshot je
   `range_days` verwenden. Also je Zeile `range_days` + `current_end` lesen,
   pro `range_days` das höchste `current_end` bestimmen, nur diese Zeilen in
   `gsc_best` aufnehmen. Default für die Statusberechnung: der 28d-Snapshot.
   Trend (`previous_position` → `position`) separat ausweisen, nicht mit der
   aktuellen Position vermischen.
   *Akzeptanz:* `gap-report.sh 2026-07` meldet für `wordpress agentur hannover`
   Pos. 14 (28d-Snapshot vom 03.07.), **nicht** 11.

2. **Neue Exporte einchecken**
   Die beiden 30.07.-Exporte nach `seo-research/2026-07/data/gsc/` als
   `gsc-export-28d-2026-07-30.csv` / `gsc-export-7d-2026-07-30.csv`.
   Erst *nach* Fix 1 — sonst verschlimmert sich die Vermischung.
   *Akzeptanz:* danach meldet der Report Hannover Pos. 26,6.

3. **Cockpit-Frontpage-Meta** — `seo-cockpit-insights.php` (~Zeile 438, in
   `nexus_get_seo_cockpit_post_seo_context()`): vor dem `hu_get_stored_seo_value()`-
   Pfad auf `(int) get_option('page_on_front')` bzw. `page_for_posts` prüfen und
   dann `hu_get_homepage_title()` / `hu_get_homepage_description()` bzw.
   `hu_get_blog_archive_title()` zurückgeben, mit `title_source = 'forced'`.
   Kein Duplizieren der Strings — die Helper aufrufen.
   *Akzeptanz:* Cockpit-Export zeigt für `/` „Anfragesysteme für Solar &
   Wärmepumpe | Haşim Üner"; Frontend-Ausgabe bleibt unverändert.

### P1 — Registry entrümpeln (Codex)

4. **`docs/seo/query-ownership.csv`** — die zwei Phantom-Owner auflösen:
   - `daa leads` → Zeile entfernen **oder** Owner auf eine existierende URL
     umhängen. Keine Seite bauen.
   - `wattfox` → Owner auf `/solar-leads-kaufen-alternative/` korrigieren
     (das ist die URL, die tatsächlich rankt und dort schon einen Klick hatte).
   - Owner-Konflikte als Ist-Stand nachziehen: `leadgenerierung photovoltaik`,
     `photovoltaik leads` — Entscheidung siehe P2, bis dahin unverändert lassen
     und im `source`-Feld den Konflikt vermerken.
   - Fehlende Zeilen ergänzen für Queries mit Signal, aber ohne Owner:
     `solar leads`, `pv leads`, `leads solar`, `leads kaufen photovoltaik`,
     `leadgenerator solar`, `server side tracking dsgvo` → alle auf die URL, die
     laut 28d-Export bereits gewinnt.
   *Akzeptanz:* `intent-gate.sh audit` läuft ohne Verweis auf nicht existierende
   Templates; jede Zeile hat eine belegbare `source`.

### P2 — Inhaltliche Entscheidungen (bleibt bei mir)

Diese drei brauchen Urteilsvermögen über Positionierung, nicht Ausführung:

5. **Live-Check `/solar-waermepumpen-leadgenerierung/`** — Indexierbarkeit,
   Canonical, Robots, H1. Eine Money-Page mit 3 Head-Terms und null Impressionen
   über 28 Tage ist ein Alarmsignal. **Blockiert 6.**
6. **Ownership-Entscheidung Solar-Lead-Vokabular** — entweder
   `/solar-waermepumpen-leadgenerierung/` übernimmt die Head-Terms wirklich
   (dann muss `/b2b-solar-leads/` inhaltlich auf Gewerbe/Termine eingegrenzt
   werden), oder die Registry wird an die Realität angepasst und
   `/b2b-solar-leads/` wird offizieller Owner. Nicht beides.
7. **Checkfox/Aroundhome-Titles** — „für Betriebe" aus dem Title-Anfang raus,
   Betriebs-Abzweigung in den Text. Live-sichtbar → direkt auf `main`.

### Nicht tun

- Keine neuen Seiten. Kein `/daa-…/`, kein `/wattfox-…/`, keine lokalen
  WordPress-Ableger.
- `/solar-leads-kaufen-lohnt-sich/` nicht ausbauen (überschneidet mit Alternative,
  Kostenstudie, TCO) — aber auch nicht löschen.
- `/server-side-tracking-b2b/` nicht auf Seite 1 optimieren wollen: 793 Wörter
  gegen Agentur-Wettbewerb auf Pos. 52 ist kein Title-Problem, das wäre ein
  eigenes Content-Projekt.
- `server side tracking regensburg` (26 Impr.) ist Geo-Rauschen, keine Chance.

---

## 3. Prompt für Codex

```text
Repository: Hasim-Uner/meine-wordpress-site-2fe6f514
Branch: claude/seo-tooling-fix-2026-07-30 (von main neu erstellen)
Abschluss: Pull Request. Begruendung: die Aenderungen sind auf der Live-Seite
nicht sichtbar (Tooling, Registry, Admin-Export) — laut CLAUDE.md ist der Diff
dort das einzige Review.

Lies zuerst AGENTS.md und agents/skills/CONTEXT.md.
Kein Audit, keine Analyse, keine Strategieempfehlung — die Entscheidungen sind
getroffen. Nur umsetzen, was hier steht.

AUFGABE 1 — gap-report.sh: neuester Snapshot statt bester Position
Datei: agents/skills/seo-agent/scripts/gap-report.sh, Block "--- 3. GSC" (Z. 97-114)
Ist: glob ueber alle gsc/*.csv, behaelt je Query die niedrigste je gesehene
     Position ("if prev is None or pos < prev[1]"). Vermischt 7d/28d und alte
     mit neuen Snapshots.
Soll: je range_days nur den Snapshot mit dem hoechsten current_end auswerten.
     Statusberechnung gegen den 28d-Snapshot. Trend (previous_position ->
     position) separat ausgeben, nicht in die aktuelle Position mischen.
     Die Spalten range_days und current_end stehen in jeder Export-Zeile.
Test: bash agents/skills/seo-agent/scripts/gap-report.sh 2026-07
     muss fuer "wordpress agentur hannover" Pos. 14 zeigen (28d-Snapshot
     2026-07-03), nicht 11 (das ist der 7d-Wert und der aktuelle Bug).

AUFGABE 2 — neue Exporte einchecken (erst nach Aufgabe 1)
Die beiden Exporte vom 30.07.2026 nach seo-research/2026-07/data/gsc/ als
gsc-export-28d-2026-07-30.csv und gsc-export-7d-2026-07-30.csv.
Test: gap-report.sh meldet danach Hannover Pos. 26,6 (aktueller 28d-Snapshot).

AUFGABE 3 — Cockpit exportiert Frontpage-Alttitel
Datei: blocksy-child/inc/seo-cockpit/seo-cockpit-insights.php,
       Funktion nexus_get_seo_cockpit_post_seo_context(), ~Z. 438
Ist: liest hu_get_stored_seo_value($post_id, 'seo_title', 'rank_math_title').
     Fuer die Startseite ist das der Alttitel "WordPress Growth Architect |
     Hasim Üner Hannover". Das Frontend erzwingt dagegen ueber is_front_page()
     (seo-meta.php:962 und :1223) den Titel aus hu_get_homepage_title().
     Das Cockpit loest per post_id auf und erreicht diese Verzweigung nie;
     die Startseite steht auch nicht in hu_get_forced_singular_seo_map().
Soll: vor dem stored-Pfad pruefen, ob $post_id === (int) get_option('page_on_front')
     -> hu_get_homepage_title() / hu_get_homepage_description(),
     bzw. === (int) get_option('page_for_posts') -> hu_get_blog_archive_title().
     title_source/description_source auf 'forced' setzen.
     Die Helper aufrufen, die Strings NICHT duplizieren.
Test: Cockpit-Export zeigt fuer / den Titel "Anfragesysteme für Solar &
     Wärmepumpe | Haşim Üner". Die Frontend-Ausgabe darf sich nicht aendern.

AUFGABE 4 — docs/seo/query-ownership.csv bereinigen
4a Phantom-Owner (Template und Content existieren nicht im Repo — pruefe das
   selbst nach, bevor du loeschst):
   - Zeile "daa leads;/daa-photovoltaik-leads-einordnung/" entfernen.
   - Zeile "wattfox": owner_path auf /solar-leads-kaufen-alternative/ aendern.
     Beleg: 28d-Export 2026-07-30, wattfox rankt dort Pos. 30,67; Vorperiode
     Pos. 9 mit 1 Klick. Die Registry-URL rankt nirgends.
4b Fehlende Zeilen ergaenzen — Owner ist jeweils die URL, die im 28d-Export
   vom 30.07. die beste Position hat. Nicht raten, aus der Datei ablesen:
   solar leads | pv leads | leads solar | leads kaufen photovoltaik |
   leadgenerator solar | server side tracking dsgvo
4c Bekannte Owner-Konflikte NICHT aufloesen, nur dokumentieren: bei
   "leadgenerierung photovoltaik" und "photovoltaik leads" im source-Feld
   ergaenzen, dass laut 28d-Export 2026-07-30 /b2b-solar-leads/ rankt
   (Pos. 46,2 bzw. 50,0) und nicht der eingetragene Owner. Die Entscheidung
   dazu faellt getrennt.
Format: Semikolon-getrennt, jede Zeile mit belegbarer source. Keine erfundenen
Volumina, keine erfundenen Positionen.
Test: bash agents/skills/seo-agent/scripts/intent-gate.sh audit laeuft sauber
und verweist auf keine nicht existierende URL mehr.

NICHT TUN
- Keine neuen Seiten, Templates oder Beitraege anlegen.
- Keine Titles, Descriptions oder Seiteninhalte aendern (macht ein anderer Lauf).
- Keine Positionierung anfassen, kein Zurueck auf alte Shopify-/Growth-Architect-Copy.
- Keine geschaetzten Keyword-Volumina oder Positionen eintragen.

VOR DEM PUSH
bash scripts/validate-architecture.sh
bash scripts/check-german-copy.sh <base> <head>
find blocksy-child -name '*.php' -print0 | xargs -0 -n1 php -l
composer analyse:php
```

---

## 4. Verification

Nach Codex' PR selbst prüfen:

```bash
# P0.1 — Snapshot-Fix greift
bash agents/skills/seo-agent/scripts/gap-report.sh 2026-07 --md | grep -i hannover
#   vor Aufgabe 2: Pos. 14 (nicht 11)
#   nach Aufgabe 2: Pos. 26,6

# P1 — Registry konsistent
bash agents/skills/seo-agent/scripts/intent-gate.sh audit
grep -c "daa-photovoltaik-leads-einordnung" docs/seo/query-ownership.csv   # 0
grep "wattfox" docs/seo/query-ownership.csv                                # /solar-leads-kaufen-alternative/

# P0.3 — Frontpage-Meta: Diff darf nur den Cockpit-Pfad treffen
git diff main -- blocksy-child/inc/seo-cockpit/seo-cockpit-insights.php
git diff main -- blocksy-child/inc/seo-meta.php    # muss leer sein

# CI
bash scripts/validate-architecture.sh && composer analyse:php
```

Live (durch mich, nicht Codex — braucht Netzzugang):
`/solar-waermepumpen-leadgenerierung/` auf Indexierbarkeit, Canonical, Robots, H1.
Erst danach die Ownership-Entscheidung aus P2.6.

---

## 5. Codex oder ich?

Direkt gefragt, direkt geantwortet — an dieser Aufgabe gut ablesbar:

**Codex war hier stark.** Beide Bugs gefunden, Zahlen exakt, die strategische
Grundhaltung („erst Ownership sortieren, keine neuen Seiten") richtig. Das ist
keine schlechte Analyse, die ich rette.

**Der Unterschied lag in der Tiefe, nicht in der Korrektheit.** Was ich zusätzlich
gefunden habe, kam aus systematischem Durchrechnen statt URL-für-URL-Lesen: dass
`/b2b-solar-leads/` in 8 von 14 Clustern die gemeinsame Ursache ist; dass die
Registry auf zwei nicht existierende Seiten zeigt; dass `wattfox` der einzige
verlorene Klick ist; dass bei Hannover die naheliegende Ursache (interne Links)
ausscheidet und es ein Positionierungspreis ist.

**Praktische Aufteilung:**
- **Codex**: spezifizierte Umsetzung mit klaren Akzeptanzkriterien — P0/P1 oben.
  Genau dafür ist der Prompt geschrieben. Günstiger, und das Ergebnis ist am Test
  überprüfbar.
- **Ich**: Diagnose, Priorisierung, und alles wo eine Positionierungsentscheidung
  drinsteckt (P2). Da ist „richtig ausgeführt" nicht dasselbe wie „richtig
  entschieden".

Die Arbeitsteilung, die du dir überlegt hast, ist also die richtige.
