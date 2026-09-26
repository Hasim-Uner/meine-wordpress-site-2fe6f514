---
name: seo-agent
description: "SEO-Werkzeuge fuer Keyword-Luecken (gap-report) und das Ownership-Gate vor neuen Seiten (intent-gate). Kein Router; die Auswahl trifft seo-intelligence."
---

# SEO Agent

Werkzeug-Skill fuer zwei Fragen: Was fehlt uns (Gap-Report)? Darf diese neue
Seite entstehen (Ownership-Gate)? Das Routing zu anderen SEO-Skills liegt bei
`seo-intelligence`; dieser Skill leitet nicht weiter.

## Run First

Sobald die Aufgabe eine **neue Seite, einen neuen Beitrag oder ein neues Cluster**
erzeugen wuerde, zuerst das Ownership-Gate:

```bash
bash agents/skills/seo-agent/scripts/intent-gate.sh check "<ziel-query>" "<geplanter-slug>"
bash agents/skills/seo-agent/scripts/intent-gate.sh audit   # Registry-Konsistenz
```

Fuer die Frage „woran arbeiten wir als naechstes?" der Gap-Report:

```bash
bash agents/skills/seo-agent/scripts/gap-report.sh          # neueste Periode, 28d
bash agents/skills/seo-agent/scripts/gap-report.sh --7d      # 7-Tage-Snapshot
bash agents/skills/seo-agent/scripts/gap-report.sh 2026-07 --md > /tmp/gap.md
```

Der Rang-Status kommt immer aus **einem** Snapshot: dem neuesten Export des
gewaehlten Zeitraums, bestimmt ueber `range_days` + `current_end`, nicht ueber
den Dateinamen. Default sind 28 Tage; `--7d` / `--28d` / `--range=N` waehlen
explizit. Fehlt der Zeitraum, bricht das Skript ab, statt still auf einen
anderen auszuweichen. Der Report weist Zeitraum **und** Quelldatei aus.

Die Spalten `previous_*` und `delta_*` bleiben hier bewusst liegen: der
Gap-Report soll den Ist-Stand zeigen, nicht die Bewegung. Den Periodenvergleich
macht `seo-drift` — dort gehoert er hin, nicht in eine zweite Auswertung hier.

Der Abschnitt `MEHRFACH-URLS` listet vollstaendig jede Query, fuer die im
Snapshot mehr als eine eigene URL rankt (`=` Registry-Owner, `+` nicht
registriert). Bewusst ohne Schwellenwert: sobald eine Nicht-Owner-URL fuer eine
Owner-Query Impressionen bekommt, ist das ein `OWNER-KONFLIKT`.

Regressionstest nach jeder Aenderung am Report:

```bash
bash agents/skills/seo-agent/tests/run-gap-report-tests.sh
```

Er rechnet nur mit vorhandenen Exporten unter `seo-research/<periode>/data/` —
keine API, keine geschaetzten Werte. Status je Keyword: `LUECKE`, `TEIL-LUECKE`
(nahe an bestehendem Owner → dort einarbeiten), `REGISTRY-LUECKE` (rankt schon,
fehlt aber in der Registry), `OWNER-KONFLIKT` (Registry und Realitaet
widersprechen sich), `BESTEHT`, `AUSGESCHLOSSEN`.

### Daten fuer eine neue Periode

Ohne API ist das Sammeln Handarbeit, die Auswertung nicht. Neuen Ordner
`seo-research/<JJJJ-MM>/data/` anlegen und ablegen:

| Datei | Quelle |
|---|---|
| `keywords-master.csv` | Keyword-Recherche, Spalten `keyword,volume,kd,cpc,intent,cluster,quelle` |
| `gsc/<export>.csv` | SEO-Cockpit-Export (Semikolon-getrennt) |
| `comp-<domain>.json` | optional, Wettbewerber-Rankings mit `domain` + `keywords[]` |

Fehlt eine Datei, meldet das Skript das — es rechnet nicht mit Platzhaltern.

## Decision Rules

- **Ownership vor Produktion.** Keine neue Seite/kein neuer Beitrag ohne freie
   Ziel-Query. Exit 1 aus `intent-gate.sh check` ist ein Stopp, kein Hinweis:
   bestehenden Owner ausbauen, Intent belegbar abgrenzen, oder als Support-Seite
   ohne Ranking-Ziel bauen. Neue Owner danach in `docs/seo/query-ownership.csv`
   eintragen — mit Belegstelle, nie mit geschaetzten Volumina.
- Markennahe Copy-Regeln stehen in `docs/standards/BRAND_AND_COPY.md`; nicht hier kodieren.
- Faellt die Aufgabe nicht unter Gap-Report oder Ownership-Gate, zurueck an
  `seo-intelligence`.

## Deliver

- Zeitraum und Quelldatei des Snapshots, Status je Keyword, naechster Schritt.
- Repo-Aenderungen getrennt von Editor-, Admin- und Search-Console-Aufgaben.
