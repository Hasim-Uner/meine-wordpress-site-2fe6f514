---
name: offer-funnel-intelligence
description: "Diagnose offer clarity, proof, qualification and sales handoff when the question is whether a funnel attracts qualified, closeable inquiries."
---

# Offer Funnel Intelligence

Assess buyer fit, offer value and sales quality using the route's actual role.
Use the local context already selected under `AGENTS.md`; do not load another.

## Evidence and diagnosis

- Read `docs/standards/BRAND_AND_COPY.md` and the relevant route in `llms.txt`.
- For buyer claims, read `docs/standards/VOICE_OF_CUSTOMER.md`; delegate to
  `buyer-research` when direct evidence is thin. Label proxy evidence and inference.
- For funnel/CTA changes read `docs/architecture/CONVERSION_ROUTING.md`.
- Full funnel audit: run `bash agents/skills/offer-funnel-intelligence/scripts/scan-offer-funnel.sh`,
  then read [diagnosis](references/diagnosis.md) and [scoring-rubric](references/scoring-rubric.md).
  Treat RED findings as P0 blockers. Show all ten scores; below 60/100 recommends
  pausing paid traffic until P0s are fixed. This does not authorize external changes.
- Focused offer questions: inspect only the relevant diagnosis dimensions and files.
- Use [report](references/report.md) for the full report and implementation handoff.

## Invariants

- Lead quality and qualified demand outrank clicks. Resolve obvious clarity and
  qualification debt before suggesting an A/B test.
- Never present proxy research or competitor language as direct buyer evidence.
- On energy acquisition routes, lead with buyer outcomes; keep WGOS as delivery
  architecture. Apply the canonical brand and offer boundaries to other routes.
- Use E3 as mechanism proof with constraints, never as a universal guarantee.
- Do not restore retired offers, timing or positioning; use current canonical sources.
- An audit request yields recommendations. Implement only within the user's
  authorized scope; existing authorization remains valid.

For included frontend implementation delegate to `modern-web-guidance` as needed.
Pure visual tasks start at `frontend-system`; pure copy tasks at `conversion-copy`.
Deliver: evidence-ranked P0/P1/P2 actions with route/file, consequence, proposed
change and owner (Repo, Manual WP, Sales or Tracking).
