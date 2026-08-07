---
name: route-conversion-review
description: Vollprüfung einer einzelnen Route auf hasimuener.de in fester Reihenfolge — Angebot, CRO, Copy, SEO, interne Links, Speed. Use für Aufträge, die eine konkrete URL oder Seite komplett bewerten sollen, etwa "Prüfe /whitelabel-retainer/ komplett", "Ist die Money Page rund?", "Seite X durchleuchten". Nicht für repo-weite Sweeps über mehrere Seiten (wordpress-performance-marketing) und nicht für Einzeldomänen-Tiefe (dann direkt den Fachskill).
---

# Route Conversion Review

Eine Route, alle Ebenen, immer dieselbe Reihenfolge. Der Skill bewertet nicht
selbst — er sammelt Evidenz und ruft die Fachskills in der Reihenfolge auf, in
der ihre Befunde aufeinander aufbauen.

## Abgrenzung

- `wordpress-performance-marketing` ist der **horizontale** Sweep: das ganze
  Repo über Modi (`full`, `seo`, `cro`, …).
- Dieser Skill ist **vertikal**: eine URL, dafür alle Ebenen.
- Geht es nur um eine Domäne, ist der Fachskill direkt schneller.

## Load First

1. `AGENTS.md`
2. `agents/skills/CONTEXT.md`
3. `docs/standards/BRAND_AND_COPY.md`
4. `llms.txt` für Route, Rolle und CTA-Ziel

## First Command

```bash
bash agents/skills/route-conversion-review/scripts/review-route.sh /whitelabel-retainer/
```

Das Skript löst die Route auf, listet Überschriften, CTAs, Proof-Referenzen,
harte Zahlen und eingehende Links und schließt mit einer Ampel.

Zur Ampel: `ROT` ist ein P0-Kandidat, kein Urteil. Zwei Fälle entwerten sie,
beide vor dem Report prüfen — Copy kann im WordPress-Editor liegen statt im
Template, und ein gesperrter Begriff in einer Abgrenzung („Architektur statt
Webdesign") ist Positionierung, kein Verstoß. Den zweiten Fall trennt das
Skript bereits ab.

## Reihenfolge der Linsen

Nicht umsortieren. Jede Stufe setzt die vorherige voraus.

1. `Angebot / Funnel` → `offer-funnel-intelligence`
2. `CRO / CTA- und Proof-Hierarchie` → `wordpress-cro-content-design-audit`
3. `Copy` → `seo-conversion-copywriting`
4. `SEO` → `seo-agent` (routet weiter)
5. `Interne Links` → `internal-linking-audit`
6. `Speed` → `page-speed-audit`

Angebot steht vor Copy, weil sich eine unklare Angebotslogik nicht durch Text
reparieren lässt. Wer bei Stufe 1 einen P0 findet, schreibt keine Copy-Vorschläge
für Stufe 3, sondern meldet den Blocker.

Stufe 6 nur, wenn die Route Medien oder eigene Assets lädt.

## Hard Rules

- Keine Regel neu kodieren, die ein Fachskill besitzt. Dieser Skill hält die
  Reihenfolge, nicht die Kriterien.
- Nichts ändern ohne ausdrücklichen Auftrag. Erst der Befund, dann die Frage.
- Zahlen ausschließlich aus `blocksy-child/inc/canon/`. Findet das Skript eine
  Zahl ohne Canon-Referenz, ist das ein Befund, keine Kleinigkeit.
- Auf `/whitelabel-*` gelten die dokumentierten Ausnahmen aus
  `BRAND_AND_COPY.md`. Kernpositionierung dort nicht einfordern.

## Deliver

Repo-Standard, je Punkt mit Route, Ist-Problem, Geschäftsfolge, Vorschlag:

- `Critical`
- `High leverage`
- `Polish`
- `Manual WordPress tasks`
- `Agent tasks / repo tasks`

Jeder Punkt trägt `Repo` oder `Manual WP`. Am Ende eine Zeile, welche Linse
nichts gefunden hat — das ist die Information, die beim nächsten Lauf Zeit spart.
