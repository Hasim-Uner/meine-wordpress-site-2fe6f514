---
name: performance-marketing
description: "Primary router for broad website audits across SEO, CRO, tracking, performance marketing, and Core Web Vitals."
---

# Performance Marketing

Trigger: repo-wide or multi-route audit spanning several acquisition/conversion disciplines, or page-speed/CWV diagnosis.

Delegate:
- Broad horizontal sweep → `wordpress-performance-marketing`
- Page speed / LCP / INP / CLS → `page-speed-audit`

Hard rules:
- Do not use this skill for a single narrow page-copy task.
- Separate repository fixes from WordPress admin, analytics, and external-platform actions.
- Never invent analytics/ads data.
- Prefer deterministic measurements and existing scripts over qualitative guesses.

Deliverable: evidence-ranked findings separated by workstream and implementation surface.
