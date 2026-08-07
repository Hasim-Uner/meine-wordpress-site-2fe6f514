---
name: conversion-copy-loop
description: Copy einer Route iterativ verbessern, bis messbare Gates grün sind — Kritik, Fix, Nachmessung in Runden statt in einem Durchgang. Use für Aufträge, die einen Zielzustand statt einer Einschätzung verlangen, etwa "verbessere /x/ bis es sitzt", "überarbeite die Money Page, bis die Gates grün sind", "iteriere über den Hero". Nicht für eine einmalige Bewertung ohne Änderung (route-conversion-review), nicht für einen einzelnen Textauftrag (seo-conversion-copywriting).
---

# Conversion Copy Loop

Misst, ändert, misst nach. Solange, bis eine überprüfbare Bedingung erfüllt ist
oder ein benannter Grund den Loop stoppt.

## Abgrenzung

- `route-conversion-review` **misst einmal** und liefert einen Report.
- Dieser Skill **misst, ändert, misst nach** — und benutzt den ersten als
  Instrument. Er ersetzt ihn nicht.
- Ein einzelner Textauftrag ohne Zielzustand geht direkt an
  `seo-conversion-copywriting`. Ein Loop für einen Hero ist Overhead.

## First Command

```bash
bash agents/skills/conversion-copy-loop/scripts/gate-check.sh /waermepumpen-leads/
```

Exit 0 heißt: kein ROT. Exit 1 heißt: weitere Runde. Das ist das Loop-Signal —
nicht die eigene Einschätzung, ob der Text jetzt gut ist.

## Ablauf

```
Runde 0   gate-check.sh                        Ausgangsstand, wird protokolliert
          offer-funnel-intelligence            Angebotslogik tragfähig?
Runde n   wordpress-cro-content-design-audit   Kritik: P0 / P1 / P2
          seo-conversion-copywriting           nur die P0 beheben
          gate-check.sh                        nachmessen
          Stop-Bedingung?  ja -> Report   nein -> Runde n+1
```

Runde 0 ist nicht optional. Ohne Ausgangsstand ist am Ende nicht belegbar, dass
sich etwas verbessert hat — und ein Rückschritt fällt nicht auf.

Findet `offer-funnel-intelligence` einen P0 in der Angebotslogik, endet der Loop
dort. Eine unklare Angebotslogik lässt sich nicht durch Text reparieren; wer es
versucht, poliert die falsche Ebene.

## Stop-Bedingung

Fertig ist erreicht, wenn **alle drei** gelten:

1. `gate-check.sh` endet mit 0 — kein ROT
2. kein offener P0 aus `wordpress-cro-content-design-audit`
3. jeder `copy-check.sh`-Treffer ist entschieden: behoben **oder** mit einem
   Satz begründet stehen gelassen

**P1 und P2 werden nicht geloopt.** Sie sind „wahrscheinliche Verbesserungen",
und davon findet ein Kritiker immer noch eine. Wer auf P1 loopt, läuft
garantiert in die Rundengrenze und hat die Grenze zum Stop-Kriterium gemacht
statt die Qualität. P1/P2 einmal am Ende berichten, nicht abarbeiten.

## Abbruch ohne Erfolg

Drei Fälle beenden den Loop, ohne dass das Ziel erreicht ist. Alle drei werden
als Befund gemeldet, nie als Erfolg verkauft:

- **Rundengrenze.** Nach 3 Runden ist Schluss. Wer sie erreicht, hat ein Problem,
  das der Loop nicht lösen kann — das ist die Information, nicht das Versagen.
- **Stillstand.** Ändert eine Runde keinen einzigen Gate-Zustand, wird
  abgebrochen. Weiterlaufen wäre Beschäftigung.
- **Rückschritt.** Kippt ein Gate, das in einer früheren Runde OK war, sofort
  stoppen und die Regression benennen. Ohne diese Regel pendelt der Loop
  zwischen zwei Fehlern und verbraucht alle Runden.

## Rundenprotokoll

`.ai/memory/loop-<slug>.md`, ephemer und gitignored. Je Runde vier Zeilen:
Gate-Stand vorher, geänderte Dateien, Gate-Stand nachher, offene P1/P2.

Das Protokoll ist der Grund, warum der Loop überprüfbar ist. Ohne es bleibt
„ich habe dreimal iteriert" eine Behauptung.

## Hard Rules

- Keine Bewertungsregel neu kodieren. Die Ampel gehört `review-route.sh`, die
  Copy-Muster `copy-check.sh`, die Begriffe dem Canon.
- `copy-check.sh` ist **advisory**. Treffer werden entschieden, nie automatisch
  entfernt — eine wegoptimierte Floskel kann die tragende Aussage gewesen sein.
- ROT prüfen, bevor es behoben wird: Liegt die Copy im WordPress-Editor statt im
  Template, ist es eine Manual-WP-Aufgabe und kein Repo-Fix. Der Loop kann sie
  dann nicht schließen und meldet sie offen.
- Zahlen ausschließlich aus `blocksy-child/inc/canon/`. Eine Runde, die eine
  Zahl erfindet, um ein Gate zu schließen, hat das Gate gebrochen, nicht erfüllt.
- Auf `/whitelabel-*` gelten die Ausnahmen aus `BRAND_AND_COPY.md`.

## Deliver

- Rundentabelle: je Runde Gate-Stand vorher → nachher, was geändert wurde
- Endstand mit Begründung: Ziel erreicht, oder welcher der drei Abbrüche griff
- Offene P1/P2 als Liste, unbearbeitet
- Trennung `Repo` und `Manual WP`
- Jeder stehen gelassene Advisory-Treffer mit seinem Satz Begründung
