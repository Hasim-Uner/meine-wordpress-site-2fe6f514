# Measurement Contract

Use this reference only while planning or reviewing a revenue learning cycle.

## Contents

- Source Ownership
- Event Contract
- One-Change Release Record
- Aggregate Review CSV
- Evidence Gates
- Decision Semantics

## Source Ownership

| Layer | Evidence | Owner |
| --- | --- | --- |
| Repo | `data-track-*`, local emitters, route/template, commit | Repo |
| WordPress | SEO Cockpit, Koko context, audit CRM, statuses, attribution | Manual WP |
| Search | GSC clicks, impressions, CTR, position, query/page pairs | External |
| Event routing | GTM/GA4/sGTM mapping and consent behavior | External |
| Sales | qualified, progressed, won, rejection reason | Sales |

The presence of a repo hook proves only that markup exists. It does not prove
collection, consent, transport, mapping, deduplication, or reporting.

The presence of `_nexus_review_qualification_status` in the CRM likewise does
not prove that the Cockpit exposes a route/window `qualified` count. Run
`scripts/outcome-contract-check.py` before selecting `qualified_rate` as the
primary outcome. Also verify manually that progressed/won definitions are
qualification-scoped rather than independent status totals.

## Event Contract

For every decision-critical event, record:

- stable `snake_case` event name;
- route and funnel stage;
- trigger and deduplication rule;
- allowed parameters and their types;
- Repo emitter or external GTM selector;
- consent behavior;
- downstream report and owner;
- validation date and evidence.

Do not send names, email addresses, phone numbers, company names, free text,
form payloads, full URLs containing query parameters, `gclid`, or `fbclid` as
analytics parameters. CRM attribution may store required operational fields
under its own consent and retention contract; that does not make them analytics
dimensions.

## One-Change Release Record

Write the ephemeral record to
`.ai/memory/revenue-learning-<slug>-<date>.md`:

```markdown
# <route> — <release date>

- Decision: <what this cycle may authorize>
- Bottleneck: <observed evidence, not a theory>
- Hypothesis: If <one change>, then <primary outcome>, because <mechanism>.
- Changed layer: <offer|structure|copy|design|seo|handoff>
- Primary outcome: <qualified_rate|progressed_rate|win_rate>
- Minimum meaningful primary change: <percentage points, chosen before review>
- Guardrails: <qualification_rate, progression_rate, ...>
- Baseline window: <start> to <end>
- Review window: <start> to <end>
- Minimum volume: <sessions/requests/qualified>
- Exclusions: <internal, bot, campaign, route, geography>
- Known confounders: <campaign, seasonality, sales capacity, tracking>
- Commit/deploy: <sha and annotation>
```

If two changed layers are needed, split them into two cycles or state that the
release is not identifiable and can only be monitored as a bundle.

## Aggregate Review CSV

Use only aggregate counts; never export individual CRM rows:

```csv
period,start_date,end_date,sessions,requests,qualified,progressed,won
before,2026-07-01,2026-07-14,200,14,8,4,2
after,2026-07-16,2026-07-29,220,18,11,6,3
```

Definitions must stay stable across both periods:

- `sessions`: route sessions after declared exclusions;
- `requests`: distinct submitted inquiries attributed to the route;
- `qualified`: requests accepted as a real ICP/fit opportunity;
- `progressed`: qualified opportunities that reached the next sales step;
- `won`: progressed opportunities recorded as won.

The evidence gate derives:

- `request_rate = requests / sessions`;
- `qualified_rate = qualified / sessions`;
- `progressed_rate = progressed / sessions`;
- `win_rate = won / sessions`;
- `qualification_rate = qualified / requests`;
- `progression_rate = progressed / qualified`;
- `close_rate = won / progressed`.

## Evidence Gates

Return `INSUFFICIENT` when any required condition fails:

1. external mapping or CRM status definitions are unverified;
2. either source is stale or a window contains an outage;
3. baseline and review windows differ in length without normalization and a
   stated reason;
4. the predeclared volume threshold is missed;
5. the smallest decision-relevant primary change was not declared before review;
6. selected primary or guardrail denominators are zero;
7. traffic mix, campaigns, offer, sales capacity, or tracking changed enough
   to dominate the tested change;
8. more than one funnel layer changed.

The script checks structural gates, not source freshness or confounders. Those
remain explicit human decisions.

## Decision Semantics

- `KEEP CANDIDATE`: data gate ready, primary lift reaches the predeclared
  meaningful-change threshold, and no guardrail exceeds its tolerance.
- `REVERT CANDIDATE`: data gate ready and the primary loss reaches that
  threshold or a declared guardrail regressed beyond tolerance.
- `INCONCLUSIVE`: data gate ready but the measured change stays inside the
  predeclared threshold.
- `INSUFFICIENT`: evidence gate not met; do not dress missing data as a loss.

All four are directional operational labels. A pre/post comparison alone never
proves that the release caused the result.
