---
name: revenue-learning-loop
description: Operate a privacy-first post-release learning loop for hasimuener.de from measurement readiness through one-change test design to a Keep candidate, Revert candidate, or Insufficient evidence decision. Use after funnel, offer, copy, design, SEO, CTA, form, or tracking-hook releases; for requests such as "Hat der Release die Umsatzwirkung verbessert?", "Was testen wir als Nächstes?", KPI-, Messungs- oder Experiment-Auswertung; when auditing an event/KPI blueprint; or when comparing route-level GSC, Koko, SEO Cockpit, CRM, GTM, or GA4 evidence against qualified, progressed, and won inquiries. Not for building the SEO Cockpit or implementing lead and analytics infrastructure.
---

# Revenue Learning Loop

Turn releases into business learning. Measure one material change against buyer
progress, not clicks alone, and stop when the evidence cannot support a decision.

## Load First

1. `AGENTS.md`
2. `agents/skills/CONTEXT.md`
3. `llms.txt` for the route's role and next step
4. `docs/systems/seo-cockpit.md` for available GSC, Koko, CRM, and Revenue
   Command Center signals
5. `references/measurement-contract.md` when planning or reviewing a release

## First Command

```bash
python3 agents/skills/revenue-learning-loop/scripts/tracking-inventory.py
python3 agents/skills/revenue-learning-loop/scripts/outcome-contract-check.py
```

The first command inventories repo-owned hooks and emitters. The second verifies
that the CRM qualification enum reaches route-level Cockpit aggregation. Neither
proves that GTM, GA4, Koko, consent, or external event mappings are live.

## Modes

- `Instrument`: audit hooks, event names, KPI coverage, privacy, and the
  Repo/Manual WP/External boundary.
- `Plan`: select one bottleneck, one material change, one primary outcome, and
  guardrails before implementation.
- `Review`: compare the predeclared windows and decide `KEEP CANDIDATE`,
  `REVERT CANDIDATE`, or `INSUFFICIENT`.

## Workflow

1. **Name the decision.** State the route, funnel role, observed bottleneck,
   and what action the evidence may authorize.
2. **Gate the instrument.** Run both first commands. Confirm the relevant hook
   is mapped externally and the CRM statuses and attribution fields are fresh.
   A blocked outcome contract or missing external mapping means `INSUFFICIENT`,
   not zero conversions.
3. **Read the current system.** Use the SEO Cockpit and Revenue Command Center
   before inventing a new dashboard. Separate GSC demand, Koko traffic, CTA
   events, requests, qualified, progressed, and won signals.
4. **Choose one change.** Fix obvious P0 breakage directly. Otherwise write one
   falsifiable hypothesis and change only one funnel layer: offer, structure,
   copy, design, SEO, or technical handoff.
5. **Freeze the contract.** Before implementation, record release date,
   baseline and review windows, minimum volume, primary outcome, its smallest
   decision-relevant change, guardrails, excluded traffic, and known confounders in
   `.ai/memory/revenue-learning-<slug>-<date>.md`.
6. **Delegate the implementation.** Route the change to the owning specialist.
   This skill owns evidence and the decision loop, not their implementation
   rules.
7. **Annotate and wait.** Record the commit/deploy and review at the declared
   14/28-day or volume gate. Do not peek-and-stop when a favorable result first
   appears.
8. **Run the evidence gate.** Prepare the small aggregate CSV described in the
   reference, then run:

   ```bash
   python3 agents/skills/revenue-learning-loop/scripts/evidence-gate.py \
     .ai/memory/revenue-window.csv \
     --release-date 2026-08-12 \
     --min-sessions 100 \
     --min-requests 10 \
     --primary qualified_rate \
     --min-primary-change-pp 0.5
   ```

9. **Decide honestly.** `KEEP CANDIDATE` and `REVERT CANDIDATE` are directional
   recommendations, never causal proof. `INSUFFICIENT` means preserve the
   current state and collect the missing evidence unless a safety regression
   requires an explicit fix.

## Specialist Boundaries

- Build or repair SEO Cockpit code → `seo-cockpit-hardening`
- Lead, REST, CRM, consent, attribution, or event plumbing →
  `wordpress-growth-architecture`
- Repo-wide structural audit → `wordpress-performance-marketing`
- Offer diagnosis → `offer-funnel-intelligence`
- One-route critique → `route-conversion-review`
- Pre-release copy gates → `conversion-copy-loop`
- Visual implementation → `b2b-design-system` + `modern-web-guidance`
- Search demand/regression → `seo-agent` / `seo-drift`

## Hard Rules

- Qualified, progressed, and won outcomes outrank clicks, CTR, and raw request
  volume. A click lift cannot win while lead quality falls.
- Never infer causality from a simple pre/post comparison. Name seasonality,
  traffic-mix, sales-process, campaign, and tracking changes as confounders.
- Do not call a low-volume rollout an A/B test. Use a directional observation
  and return `INSUFFICIENT` when the predeclared data gate is not met.
- Never change two funnel layers in one learning cycle.
- Never choose the metric, window, guardrail, or threshold after seeing the
  result.
- Never put names, email addresses, phone numbers, free text, click IDs, or raw
  CRM records into analytics events, committed files, or reports.
- Do not add pixels, analytics libraries, consent banners, GTM/GA4 changes, or
  external configuration unless explicitly requested.
- Never auto-revert. A revert candidate is a recommendation requiring an
  authorized implementation step.

## Deliver

- `Decision` — Keep candidate, Revert candidate, or Insufficient
- `Evidence` — window, volume, source freshness, primary outcome, guardrails
- `Confounders` — what prevents a causal claim
- `Next action` — exactly one owner-routed change or one missing-data task
- `Ownership` — Repo, Manual WP, Sales, or External
