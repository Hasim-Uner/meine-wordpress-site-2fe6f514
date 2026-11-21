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
- `inquiries` is not pipeline evidence. Without at least one of `qualified`,
  `progressed`, `won` the digest prints "Kein Pipeline-Beleg
  (qualified/progressed/won fehlen)."
- Maturity: a `won` or `progressed` window whose `window_end` lies within 30
  days before the reference date (or later) gets "<metric> noch nicht
  ausgereift". The value is still shown; late conversions can still change it.
  Reference date: `--until`, or with `--range A..B` the committer date of B
  (`git log -1 --format=%cs B`, an open end means HEAD). The digest prints it as
  "Stand: YYYY-MM-DD" in Ergebnis whenever measurements are given.
- Executive view: result first, no chart or table, one next step.

## Input contract

Semicolon CSV, header exactly `metric;window_start;window_end;value;denominator;source`.
`value` and `denominator` are whole counts. `ctr` is clicks as `value` and
impressions as `denominator`; it needs the denominator. Other metrics may carry
one. Unknown metric names stop the run (exit 2).

`source` is `<system>` or `<system>:<detail>`, not free text. `system` must be
one of the allowlist `crm`, `ga4`, `gsc`, `ads`, `tracking`, `cockpit`,
`calcom`, `sheet` (constant `SOURCE_SYSTEMS` in the script); `detail` matches
`[a-z0-9][a-z0-9_.-]*`; the whole value must not contain a run of 6 or more
digits. Examples: `crm:bitrix24`, `ga4`, `gsc`. Anything else stops the run
(exit 2, naming file and row). Extending the allowlist is a code change on
purpose, not a CSV edit.
Limit: the format check cannot recognise a person's name inside `detail`
(`crm:anna` passes). Use system or export identifiers only; a name in `detail`
is an operator error the script does not catch.

## Delta rule

A delta appears only when a metric has exactly two non-overlapping windows of
equal length in days and the same `source` in both. Otherwise values are shown
with "kein Vergleich" and the reason (for example "Quelle gewechselt"). A delta
is a difference of reported values, not an effect of the release.

A delta assumes stable CRM definitions. A changed qualification definition or
sales process is not detectable from the CSV: bump the `source` detail (for
example `crm:bitrix24-v2`) whenever either changes, otherwise deltas across the
change are misleading.

## Privacy rule

Enforced, and nothing beyond it:

- CSV: exit 2 on extra or missing columns, on `metric`, `value` or
  `denominator` that look like an email address or phone number, and on a
  `source` outside the allowlist format (see Input contract).
- `--decisions` lines and `--next`: exit 2 on email addresses and phone numbers
  (German formats such as `+49 ...` or `0xxx ...`, at least 7 digits, separators
  allowed). ISO and German dates are not treated as phone numbers.
- Commit subjects: email addresses and phone numbers are replaced by
  "[entfernt]". Only when this happened, the Geliefert block adds "Betreffe
  bereinigt: N".
- Errors name file and row, never the offending value.

Not covered: personal names in commit subjects, decision lines or `--next` are
not detected. The digest is no anonymization; check the text before sending.

## Success of the format

Fewer follow-up questions per update (Rückfragen je Update) alone does not show
better reports; fewer questions can also mean less reading. If used as success
metric, pair it with a second signal, for example decisions taken within the
update window (answered "Entscheidung nötig" lines). Neither is produced by the
digest.

## Wording

State what was delivered and measured. Do not write "dadurch", "führte zu" or
other causal links between a commit and an outcome; name confounders as in the
Invariants when the reader asks why.
