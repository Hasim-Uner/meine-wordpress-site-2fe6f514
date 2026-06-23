# Claude Code Instructions

You are working in Hasim Üner's WordPress performance-marketing repository.

Use this file as the project entry point for Claude Code / Opus-class agents. Do not treat it as a replacement for the repo skills. Route into the relevant skill first, then inspect files.

## First steps

1. Read `agents/skills/CONTEXT.md`.
2. Pick the matching skill before editing.
3. For broad website, SEO, offer, tracking, CRO, landing page, lead generation, or content automation work, start with:

```bash
agents/skills/wordpress-performance-marketing/scripts/render-checklist.sh full
```

4. For frontend HTML, CSS, JavaScript, forms, accessibility, or Core Web Vitals work, also load `modern-web-guidance` before implementation.
5. Keep changes small and reviewable.

## Project defaults

- This repo is for WordPress technical implementation, service pages, funnel logic, SEO/CRO architecture, schema helpers, internal linking, and agent-ready planning.
- Do not assume RankMath. Use the custom WordPress SEO Cockpit context where relevant.
- Do not assume cookie-based tracking, ad pixels, or consent banners by default.
- Google Analytics for WordPress may exist, but tracking changes must be explicit and separate from planning notes.
- Current offer priorities: SEO audit, tracking audit, autopost/content automation, landing pages, lead generation, WordPress technical implementation.
- Prefer repo-verifiable findings over generic marketing advice.

## Opus operating style

Use Opus for deeper reasoning, not bigger uncontrolled edits.

Before changing files, produce a compact plan with:

- relevant files inspected
- assumed business goal
- likely repo tasks
- manual WordPress/admin tasks
- risk level

When editing:

- Do one coherent change per branch.
- Do not touch live-critical theme or deployment files without explicit scope.
- Separate strategy documents from code changes.
- Preserve existing project language and positioning.
- Avoid returning to old Shopify positioning unless a file explicitly requires cleanup of old copy.

## Required output for audits

For repo audits, structure the answer as:

- Critical
- High leverage
- Polish
- Manual WordPress tasks
- Agent tasks / repo tasks

Always separate repo fixes from editor work, SEO Cockpit work, WordPress admin work, analytics work, and external-platform work.

## Safety and quality rules

- Never add secrets, API keys, analytics IDs, pixels, or third-party scripts unless explicitly requested.
- Never invent keyword volumes, Search Console data, or analytics numbers.
- Mark assumptions clearly.
- If data is missing, create a clean placeholder or task instead of pretending it exists.
- Prefer PRs over direct commits to `main`.
- Keep comments and docs concise.
