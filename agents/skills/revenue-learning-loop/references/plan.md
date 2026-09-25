# Plan One Change

1. State route, funnel role, observed bottleneck and decision the evidence may support.
2. Verify [measurement readiness](instrument.md). Fix obvious P0 breakage directly
   within authorization; otherwise choose one falsifiable hypothesis and one layer:
   offer, structure, copy, design, SEO or technical handoff.
3. Before implementation, freeze release date, baseline/review windows, minimum
   volume, primary outcome, smallest meaningful change, guardrails, excluded traffic
   and confounders using the One-Change Release Record section of
   [measurement-contract](measurement-contract.md).
4. Keep the record in `.ai/memory/revenue-learning-<slug>-<date>.md`.
5. Hand implementation to its primary owner, then record commit/deploy. Review at
   the declared 14/28-day or volume gate; do not stop early on a favorable result.

The learning skill owns evidence and decisions. Implementation follows the owning
primary skill's contracts; it does not authorize an unrelated change or deployment.
