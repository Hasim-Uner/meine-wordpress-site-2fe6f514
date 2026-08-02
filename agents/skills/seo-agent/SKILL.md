---
name: seo-agent
description: >
  SEO dispatcher that routes to the right sub-skill based on the task.
  Use this skill for any SEO-related work instead of picking a sub-skill manually.
context: light
preload: none
---

# SEO Agent

Dispatcher for all SEO work. Analyzes the task, selects the right sub-skill, and executes.

## When to Use

Any time the task involves SEO: technical audits, internal linking, meta/schema, rankings, Search Console, content strategy, or cockpit work.

## Run First

```bash
bash agents/skills/seo-agent/scripts/route.sh
```

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

## Routing Table

| Signal in Task | Route to Skill | Context to Load |
|---|---|---|
| Was fehlt uns? Themenauswahl, Keyword-Luecken, Wettbewerbsvergleich | hier bleiben | `gap-report.sh` + `docs/seo/keyword-exclusions.csv` |
| Live SEO check, reindex, redirects, canonicals, Search Console | `seo-live-qa` | `seo-live-qa/SKILL.md` |
| SEO Cockpit, render helpers, Koko, internal link graph module | `seo-cockpit-hardening` | `seo-cockpit-hardening/SKILL.md` + `docs/systems/seo-cockpit.md` |
| Orphan pages, link equity, cross-links, anchor text | `internal-linking-audit` | `internal-linking-audit/SKILL.md` |
| Full performance-marketing audit (SEO + CRO + tracking) | `wordpress-performance-marketing` | `wordpress-performance-marketing/SKILL.md` |
| Cornerstone content, pillar articles, SEO packaging | `pillar-cornerstone-writer` | `pillar-cornerstone-writer/SKILL.md` |
| Page speed, Core Web Vitals, LCP/CLS/INP | `page-speed-audit` | `page-speed-audit/SKILL.md` |

## Decision Rules

0. **Ownership vor Produktion.** Keine neue Seite/kein neuer Beitrag ohne freie
   Ziel-Query. Exit 1 aus `intent-gate.sh check` ist ein Stopp, kein Hinweis:
   bestehenden Owner ausbauen, Intent belegbar abgrenzen, oder als Support-Seite
   ohne Ranking-Ziel bauen. Neue Owner danach in `docs/seo/query-ownership.csv`
   eintragen — mit Belegstelle, nie mit geschaetzten Volumina.
1. Match the task against the **Signal** column above.
2. If multiple signals match, prefer the more specific skill over the broader one.
3. Load only the routed skill's `SKILL.md` — do not preload all SEO skills.
4. For brand/copy direction, reference `docs/standards/BRAND_AND_COPY.md` instead of inlining rules.
5. If no signal matches, default to `seo-live-qa`.

## Deliver

- Name the routed skill in your first response line
- Follow the routed skill's delivery format exactly
- Separate repo fixes from editor/admin/Search Console tasks
