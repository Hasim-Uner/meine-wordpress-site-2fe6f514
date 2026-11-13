---
name: editorial-seo
description: "Primary router for blog, category/archive, article UX, cornerstone content, and editorial internal-link architecture."
---

# Editorial SEO

Trigger: blog index, categories, single posts, author/related content, pillar/cornerstone packages, or editorial SEO UX.

Delegate:
- Existing blog/archive/article optimization → `blog-seo-ux-optimizer`
- New pillar/cornerstone publishing package → `pillar-cornerstone-writer`
- Link-graph-only diagnosis → `internal-linking-audit`
- Standards for an included template/CSS edit → `modern-web-guidance`

Hard rules:
- Preserve query ownership.
- Do not turn editorial pages into generic sales pages.
- Keep archive/category/single responsibilities distinct.
- Reuse shared editorial components before adding route-specific duplication.

Deliverable: route-specific editorial improvement with SEO and UX rationale.
