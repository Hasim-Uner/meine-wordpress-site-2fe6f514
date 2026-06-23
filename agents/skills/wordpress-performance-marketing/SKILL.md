---
name: wordpress-performance-marketing
description: Audit or improve the Hasim Üner WordPress repo as a revenue system across technical SEO, offer architecture, money pages, CRO, content strategy, SEO Cockpit, and privacy-first measurement readiness. Use when the task mentions SEO audit, tracking audit, autopost, landing page, lead generation, offers, money pages, funnels, or repo-level performance marketing.
---

# WordPress Performance Marketing

Use this skill for repo work where SEO, UX, content, offer logic, lead generation, and measurement have to behave like one system.

This is the main planning skill for the Hasim Üner website repo. It is not a generic plugin checklist.

## Run First

```bash
agents/skills/wordpress-performance-marketing/scripts/render-checklist.sh full
```

Supported modes: `full`, `seo`, `cro`, `content`, `tracking`, `offers`.

## Project Defaults

- Treat the repo as the source for theme logic, templates, service pages, funnels, schema helpers, seeded content, and internal-link architecture.
- Do not assume RankMath. The repo uses a custom WordPress SEO Cockpit plugin/workflow where applicable.
- Do not assume cookie-based tracking as default. The current direction is privacy-first and may start without advertising or heavy tracking.
- Do not add consent banners, ad pixels, or GA4/GTM runtime changes unless the task explicitly asks for them.
- Google Analytics for WordPress may exist, but measurement notes must stay separate from code changes unless instructed otherwise.
- Offer priorities include SEO audit, tracking audit, autopost, landing pages, lead generation, WordPress/technical implementation, and related performance-marketing services.
- Prefer repo-verifiable findings over generic marketing advice.

## Core Agent Mission

When asked to plan or improve the repo, produce concrete output in this order:

1. Inspect repo structure and identify relevant theme/plugin/content files.
2. Map current pages, offers, CTAs, internal links, and reusable sections.
3. Identify missing or weak money pages.
4. Separate repo-changeable tasks from WordPress-admin/manual tasks.
5. Suggest minimal high-leverage implementation steps.
6. Avoid touching live-critical files unless the task is explicit and scoped.

## SEO / Money Page Rules

For every money page or service page, check:

- One clear search intent.
- One primary offer.
- Title, meta description, H1, canonical ownership, indexability, schema ownership.
- Strong above-the-fold promise.
- Proof, process, FAQ, objections, CTA, and internal links.
- Local or topical relevance where useful.
- Links to related service pages and supporting content.

Potential money-page clusters:

- SEO Audit
- Technical SEO Audit
- WordPress SEO Audit
- Tracking Audit
- Server-side / privacy-first tracking readiness
- Landingpage Erstellung
- Leadgenerierung System
- Autopost / content automation
- WordPress Entwicklung
- Website Relaunch / Website Optimierung

## CRO Rules

- Keep one dominant CTA per section cluster.
- Avoid too many choices above the fold.
- Make the offer concrete before adding visual polish.
- Prefer audit-first or diagnostic CTAs when trust is not established yet.
- Copy must say what business outcome improves, not just which tool is used.

## Tracking Rules

Current default: no ad-tracking assumption.

Allowed by default:

- Keep or add neutral `data-track` attributes for future measurement.
- Document recommended events separately.
- Suggest form-submit, CTA-click, phone/email-click, audit-request, and lead-magnet events as planning notes.

Not allowed unless explicitly requested:

- Adding pixels.
- Adding cookie-setting scripts.
- Adding consent-manager logic.
- Assuming active ads.
- Treating GA4/GTM as already final.

## Content / Autopost Rules

For content automation or autopost planning:

- Start from offers and money pages, not random topics.
- Build clusters around demand, objections, proof, comparisons, and technical education.
- Every post should point to one next action or one supporting page.
- Avoid generic AI slop. Use concrete examples, local context, technical clarity, and business impact.

## Delivery Format

Use this structure:

- `Critical`
- `High leverage`
- `Polish`
- `Manual WordPress tasks`
- `Agent tasks / repo tasks`

Always separate repo fixes from editor work, SEO Cockpit work, WordPress admin work, analytics work, and external-platform work.