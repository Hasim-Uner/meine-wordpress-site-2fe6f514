# Retainer Digest

Use for asynchronous retainer or White-Label updates: commit log, CRM outcome
aggregates and tracking aggregates become one short status. The digest reports;
it decides nothing. Keep/revert decisions stay in [review](review.md), and the
SKILL.md Invariants apply unchanged (outcome ranking, no causality, privacy).

```bash
python3 agents/skills/revenue-learning-loop/scripts/retainer-digest.py \
  --since 2026-10-01 --until 2026-10-31 \
  --outcomes .ai/memory/outcomes.csv --tracking .ai/memory/tracking.csv \
  --decisions .ai/memory/decisions.txt --next "Eine Aktion" --md
```

`--range A..B` replaces `--since/--until`; `--repo PATH` defaults to the git
toplevel. Both CSVs are optional.

## Five blocks

1. **Ergebnis**: each metric with window and denominator, sorted won,
   progressed, qualified, inquiries, then sessions, clicks, impressions,
   form_starts, ctr. Without any CSV: "in diesem Fenster nicht gemessen." Gaps
   are never filled or estimated.
2. **Geliefert**: non-merge commits by touched path prefix (majority of files),
   per group the count and up to three subjects. Website changes count as
   effective only after deploy.
3. **Risiken**: reverts, hotfixes, rollbacks; otherwise "Keine Reverts im Fenster."
4. **Entscheidung nötig**: one line per request from `--decisions`; omitted if none.
5. **Nächster Schritt**: `--next`; omitted if none.

## Leading versus lagging

- Lagging (`won`, `progressed`, `qualified`, `inquiries`) take weeks to months;
  read them over a month or longer.
- Leading (`sessions`, `clicks`, `impressions`, `form_starts`, `ctr`) move
  weekly. A leading metric counts only while it tracks pipeline outcomes
  (qualified or progressed) in earlier windows of this account; absent that
  evidence it is a lead indicator, not a result.
- Only leading metrics present: the digest prints "Nur Frühindikatoren. Kein
  Pipeline-Beleg." Do not rephrase it as success.
- Executive view: result first, no chart or table, one next step.

## Input contract

Semicolon CSV, header exactly `metric;window_start;window_end;value;denominator;source`.
`value` and `denominator` are whole counts. `ctr` is clicks as `value` and
impressions as `denominator`; it needs the denominator. Other metrics may carry
one. Unknown metric names stop the run (exit 2).

## Delta rule

A delta appears only when a metric has exactly two non-overlapping windows of
equal length in days. Otherwise values are shown with "kein Vergleich" and the
reason. A delta is a difference of reported values, not an effect of the release.

## Privacy rule

Aggregates only. Exit 2 on extra or missing columns, on values that look like an
email address or phone number, and on a `source` containing `@`. Errors name file
and row, never the offending value. Decision lines and `--next` must not contain
email addresses.

## Wording

State what was delivered and measured. Do not write "dadurch", "führte zu" or
other causal links between a commit and an outcome; name confounders as in the
Invariants when the reader asks why.
