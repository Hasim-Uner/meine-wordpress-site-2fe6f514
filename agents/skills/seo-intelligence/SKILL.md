---
name: seo-intelligence
description: "Primary SEO router for technical SEO triage, live indexing/canonical QA, ranking drift, query ownership, and internal linking."
---

# SEO Intelligence

Trigger: SEO diagnosis, indexing, canonical/redirect/noindex questions, ranking changes, or internal-link architecture.

Delegate by subtask:
- General triage → `seo-agent`
- Live canonical/indexing QA → `seo-live-qa`
- Period-over-period regression → `seo-drift`
- Internal link graph → `internal-linking-audit`

Hard rules:
- `docs/seo/query-ownership.csv` is canonical when ownership changes.
- Never invent GSC, rankings, volumes, clicks, or indexing state.
- Separate query ownership from CTA routing.
- Use the narrow specialist script before broad manual inspection.

Deliverable: evidence-backed finding, exact affected routes, and only the necessary repo/admin actions.
