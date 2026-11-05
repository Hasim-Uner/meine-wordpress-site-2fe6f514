# Funnel Report

## Diagnosis contract

Every finding has four parts. The installed `flow` skill uses the same contract.

1. **Finding** — what is wrong and where (route/file/section).
2. **Evidence** — quote (≤15 words), measured value or `file:line`. Without it, label the finding `Assumption`.
3. **Limit** — confidence `high`/`medium`/`low` and what was not checked or measured.
4. **Decision** — `Fix` (concrete change), `Experiment` ("If X, then Y, measured by Z") or `Stop` (the bottleneck sits upstream in offer, audience or inflow; propose no page fixes below it).

The verdict is the bottleneck: the weakest dimension or a RED finding. The total never averages it away.
Scores are orientation, not measurements; never translate them into a promised conversion lift.

## Report order

For a full funnel audit, start with the `Verdict`, then show the ten-dimension score table, then the relevant sections below. Keep focused requests proportional to their scope:

- `Verdict` — the bottleneck, what is currently strongest and what is costing money
- `P0 Revenue Blockers` — issues that directly weaken qualified inquiries or sales fit
- `P1 High-Leverage Moves` — likely improvements to lead quality, desire, or trust
- `P2 Polish / Later` — useful but not urgent
- `Offer Ladder Fixes` — current steps from the canonical offer/routing contracts
- `Copy Rewrites` — concrete replacement text for hero, CTA, proof, FAQ, qualifier, final CTA
- `Proof Moves` — how to use E3 and other proof more effectively
- `Qualification Moves` — fields/questions/filters that improve sales quality
- `Tracking Questions` — only metrics needed for decisions
- `Do Not Touch` — working assets that should stay

For every recommendation include:

- affected route/file/section
- current issue with evidence, or `Assumption`
- confidence and what was not checked
- business consequence
- decision (`Fix`, `Experiment` or `Stop`) and the proposed change
- priority: `P0`, `P1`, `P2`
- whether it is `Repo`, `Manual WP`, `Sales`, or `Tracking`
