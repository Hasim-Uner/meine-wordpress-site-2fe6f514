---
name: wordpress-performance-marketing
description: Audit or improve the Hasim Üner WordPress repo as a revenue system across technical SEO, offer architecture, money pages, CRO, content strategy, SEO Cockpit, and privacy-first measurement readiness. Use when the task mentions SEO audit, tracking audit, autopost, landing page, lead generation, offers, money pages, funnels, or repo-level performance marketing.
---

# WordPress Performance Marketing

Use this skill for repo work where SEO, UX, content, offer logic, lead generation, and measurement have to behave like one system.

This is the broad full-audit and planning skill for the Hasim Üner website repo. It orchestrates the sweep and delegates depth to the specialist skills. It is not a generic plugin checklist, and it does not re-encode rules the specialist skills already own.

## When NOT to Use This Skill

For deep, single-domain work, route to the specialist instead — do not re-derive their rules here:

- SEO Cockpit modules, diagnostics, Koko, link-graph internals → `seo-cockpit-hardening`
- Canonicals, redirects, reindex, Search Console follow-up → `seo-live-qa`
- Internal links, orphans, anchor logic → `internal-linking-audit`
- Offer logic, funnel, Marktcheck, proof, qualification → `offer-funnel-intelligence`
- Page critique, CTA/proof hierarchy, conversion-copy rewrites → `wordpress-cro-content-design-audit`
- Cornerstone/pillar articles, blog index, category, single-post UX → `pillar-cornerstone-writer`, `blog-seo-ux-optimizer`
- New paid/campaign landing pages → `landing-page-builder`
- Lead routing, REST endpoints, CRM, form architecture → `wordpress-growth-architecture`
- Core Web Vitals, LCP/CLS/INP → `page-speed-audit`

Use this skill when the task spans several of these at once or needs a repo-wide plan. For pure SEO triage, start from `seo-agent` and let it route.

## Run First

```bash
agents/skills/wordpress-performance-marketing/scripts/render-checklist.sh full
```

Supported modes: `full`, `seo`, `cro`, `content`, `tracking`, `offers`. The script emits the per-domain quick checklists — use it for the sweep instead of inlining rules here. For depth in any one domain, open the specialist skill listed above.

## Project Defaults

- For positioning, tone, and copy direction, reference `docs/standards/BRAND_AND_COPY.md` — do not inline brand/copy rules here.
- Treat the repo as the source for theme logic, templates, service pages, funnels, schema helpers, seeded content, and internal-link architecture.
- Do not treat RankMath as active. It survives only as legacy `rank_math_*` post-meta fallback in `blocksy-child/inc/seo-meta.php`; new content uses ACF plus the custom WordPress SEO Cockpit.
- Do not assume cookie-based tracking as default. The current direction is privacy-first and may start without advertising or heavy tracking.
- Do not add consent banners, ad pixels, or GA4/GTM runtime changes unless the task explicitly asks for them.
- Google Analytics for WordPress may exist, but measurement notes must stay separate from code changes unless instructed otherwise.
- Offer priorities: SEO audit (incl. technical/WordPress), tracking audit, autopost, landing pages, lead generation, WordPress development, and website relaunch/optimization.
- Prefer repo-verifiable findings over generic marketing advice.

## Core Agent Mission

When asked to plan or improve the repo, produce concrete output in this order:

1. Inspect repo structure and identify relevant theme/plugin/content files.
2. Map current pages, offers, CTAs, internal links, and reusable sections.
3. Identify missing or weak money pages.
4. Separate repo-changeable tasks from WordPress-admin/manual tasks.
5. Hand depth in any single domain to its specialist skill; keep this skill at the orchestration layer.
6. Suggest minimal high-leverage implementation steps.
7. Avoid touching live-critical files unless the task is explicit and scoped.

## Delivery Format

Use this structure:

- `Critical`
- `High leverage`
- `Polish`
- `Manual WordPress tasks`
- `Agent tasks / repo tasks`

Always separate repo fixes from editor work, SEO Cockpit work, WordPress admin work, analytics work, and external-platform work.
