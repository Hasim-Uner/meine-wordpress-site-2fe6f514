---
name: blog-seo-ux-optimizer
description: Optimize hasimuener.de blog index, category archives, single posts, related content, author bio, internal links, and stale blog positioning. Use for Blog UX/UI, SEO, category hubs, article next steps, anchor text, copy debt, or legacy Growth/Shopify cleanup in blog surfaces.
---

# Blog SEO UX Optimizer

Use this skill for repo-owned blog work: `single.php`, `category.php`, blog header/related/author surfaces, category archive CSS, blog archive CSS, blog registry modules, and versioned Markdown under `blocksy-child/assets/content/blog/`.

## Run First

```bash
sh agents/skills/blog-seo-ux-optimizer/scripts/blog-scan.sh
sh agents/skills/blog-seo-ux-optimizer/scripts/blog-link-map.sh
```

## Required Context

- Read `docs/standards/BRAND_AND_COPY.md`; do not duplicate its copy rules.
- For frontend edits, use `modern-web-guidance` and cite the guide ID.
- For link equity, check `llms.txt` before adding or changing public URLs.

## Blog Strategy

- Blog is a supporting layer for the diagnosis-first Anfrage-System funnel, not a generic magazine.
- Category archives are topic hubs: each needs a clear intro, useful internal links, and a next-step bridge.
- Single posts should connect to exactly useful next steps: category hub, matching money page, proof/case, marketcheck or scoped contact.
- Internal anchors must describe the destination and intent; avoid generic "mehr erfahren" links.
- Old broad positioning must not survive in visible blog surfaces.

## Hard Rules

- Do not use user-facing `Growth Audit`, `Growth Partner`, `Growth Architect`, `WordPress Growth Operating System`, `WGOS`, or Shopify positioning. Keep internal IDs only when changing them would break legacy contracts.
- Do not add new CTAs before removing CTA drift.
- Do not move SEO/meta/schema logic out of centralized modules.
- Do not convert editor-owned content into templates unless the task explicitly asks.
- Keep UI changes scoped to blog surfaces; no global palette churn.

## Fix Order

1. Remove stale positioning and broken/retired CTA language.
2. Repair internal links and descriptive anchors.
3. Improve category hub structure and next-step routing.
4. Improve single-post UX: TOC, author bio, share, related content, mobile reading.
5. Only then polish visual density, spacing, and motion.

## Deliver

- `Repo changes`: exact files and behavior changed.
- `Manual WP`: editor/admin work that repo cannot safely own.
- `Validation`: scripts/checks run and live/deploy status if pushed.
