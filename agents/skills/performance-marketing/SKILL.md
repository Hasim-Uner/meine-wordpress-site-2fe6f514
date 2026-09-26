---
name: performance-marketing
description: "Primary router for broad website audits across SEO, CRO, tracking, performance marketing, and Core Web Vitals."
---

# Performance Marketing

Trigger: repo-wide or multi-route audit spanning several acquisition/conversion
disciplines, or page-speed/CWV diagnosis. Keep this skill at the orchestration
layer; a single-domain task belongs to its own primary.

Run first (modes: `full`, `seo`, `cro`, `content`, `tracking`, `offers`):

```bash
bash agents/skills/performance-marketing/scripts/render-checklist.sh full
```

Delegate / hand off:
- Page speed, LCP/INP/CLS → internal `page-speed-audit`
- Depth in one domain → its primary: `seo-intelligence`, `seo-cockpit-dev`,
  `offer-funnel-intelligence`, `conversion-architecture`, `editorial-seo`,
  `wordpress-growth-architecture`

Project defaults:
- Positioning, offers and copy direction come from `docs/standards/BRAND_AND_COPY.md`
  and `llms.txt`; do not restate or invent offers here.
- RankMath is inactive: only legacy `rank_math_*` post-meta fallback remains in
  `blocksy-child/inc/seo-meta.php`. New content uses SCF (the `acf_*` API and
  `inc/acf.php` keep their names) plus the custom SEO Cockpit.
- Measurement is privacy-first. No consent banners, ad pixels or GA4/GTM runtime
  changes unless explicitly requested; keep measurement notes apart from code.
- Never invent analytics/ads data. Prefer deterministic measurements and existing
  scripts over qualitative guesses; do not touch live-critical files without an
  explicit, scoped task.

Workflow: map the relevant routes, offers, CTAs and internal links; identify weak
or missing money pages; separate repo-changeable work from WordPress-admin work;
propose minimal high-leverage steps.

Deliver `Critical`, `High leverage`, `Polish`, `Manual WordPress tasks`,
`Agent tasks / repo tasks`. Keep repo fixes apart from editor, SEO Cockpit,
WordPress-admin, analytics and external-platform work.
