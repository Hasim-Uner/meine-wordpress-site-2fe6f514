---
name: seo-intelligence
description: "Primary SEO router for technical SEO triage, live indexing/canonical QA, ranking drift, query ownership, and internal linking."
---

# SEO Intelligence

Trigger: SEO diagnosis, indexing, canonical/redirect/noindex questions, ranking changes, or internal-link architecture.

Delegate by subtask (specialists do not route further):
- Keyword gaps / next topics, or a new page/post/cluster (ownership gate) → `seo-agent`
- Live canonical/indexing/redirect QA → `seo-live-qa` (default when nothing else fits)
- Period-over-period regression → `seo-drift`
- Internal link graph → `internal-linking-audit`
- Cockpit code → hand off to `seo-cockpit-dev`; page speed → `performance-marketing`

Hard rules:
- `docs/seo/query-ownership.csv` is canonical when ownership changes.
- Never invent GSC, rankings, volumes, clicks, or indexing state.
- Separate query ownership from CTA routing.
- Use the narrow specialist script before broad manual inspection.

Deliverable: evidence-backed finding, exact affected routes, and only the necessary repo/admin actions.
