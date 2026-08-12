---
name: homepage-proof-monitoring
description: Reconstruct or finish the fixed 2026-03-11 homepage public-proof monitoring cohort, including its historical proof hooks and pilot-intent review. Use only for that specific launch record; use revenue-learning-loop for current or route-independent post-release measurement.
---

# Homepage Proof Monitoring

This is a historical adapter for the fixed 2026-03-11 homepage proof release.
For every current release, use `revenue-learning-loop` instead.

## Run First

```bash
agents/skills/homepage-proof-monitoring/scripts/render-review.sh
```

The script prints tracked CTA hooks found in the theme plus the fixed review checklist.

## What To Check

- Compare 14 days before launch with 14 and 28 days after launch.
- Confirm repo-owned `data-track-action` hooks are still present.
- Confirm GTM maps those hooks into usable events.
- Review pilot-intent contacts separately from generic contact volume.

## Deliver

- Launch annotation task
- GTM or GA4 follow-up if hooks are not wired through
- Weekly review summary with pilot lead quality notes
