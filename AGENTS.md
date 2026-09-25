# AGENTS.md

Global contract for agents in this repository. Keep context small: load the global contract, one local context, one primary skill, then only task-relevant files.

## Load order

1. `AGENTS.md`
2. Exactly one local context:
   - Theme/runtime: `blocksy-child/CONTEXT.md`
   - PHP modules/registries: `blocksy-child/inc/CONTEXT.md`
   - Shared sections: `blocksy-child/template-parts/CONTEXT.md`
   - Durable docs: `docs/CONTEXT.md`
   - Draft content: `content/CONTEXT.md`
   - Agent/skill architecture: `agents/skills/CONTEXT.md`
3. Exactly one primary skill from `agents/skills/PRIMARY_SKILLS.txt`.
4. Only files required by that task. A primary skill may delegate to internal specialist skills.

Reuse already-loaded contracts. A skill's load list does not add a second local
context. For a distinct implementation phase, hand off to its primary owner;
do not preload several primary skills for the initial task.

## Canonical sources

- Public routes / route summaries: `llms.txt`
- Positioning / brand / public copy: `docs/standards/BRAND_AND_COPY.md`
- CTA routing: `docs/architecture/CONVERSION_ROUTING.md`
- SEO ownership: `docs/seo/query-ownership.csv`
- Live route/runtime status: `docs/architecture/LIVE_STATUS.md`
- Cross-system dependencies: `docs/architecture/SYSTEM_MAP.md`

Load a canonical source only when the task touches its contract. Never duplicate these rules in agent instructions.

## Skill routing

`agents/skills/` is the canonical store. Codex and Claude Code only discover the reduced public surface exposed through `.agents/skills/` and `.claude/skills/`.

Primary routes:
- `agent-system-maintenance` — agent instructions, skill routing, context efficiency, skill tests.
- `frontend-system` — HTML, CSS, JS, accessibility, browser APIs, UI craft, motion.
- `seo-intelligence` — SEO triage, live QA, drift, internal links.
- `seo-cockpit-dev` — SEO Cockpit implementation and diagnostics.
- `editorial-seo` — blog/archive/article UX and cornerstone content.
- `conversion-copy` — buyer research, teardown, sales copy, iterative copy improvement.
- `offer-funnel-intelligence` — offer, proof, qualification, Marktcheck, funnel economics.
- `conversion-architecture` — page CRO, route review, campaign landing pages.
- `wordpress-growth-architecture` — forms, lead routing, REST, CRM.
- `performance-marketing` — cross-site SEO/CRO/tracking audit and Core Web Vitals.
- `deploy-qa` — release smoke, registries, navigation migration and release gates.
- `revenue-learning-loop` — post-release evidence, KPI learning, keep/revert decisions.

Internal specialist skills are implementation details. Do not route directly to them unless a primary skill explicitly delegates there.

## Context discipline

- Start with `rg --files`; avoid broad `find .`.
- Search with `rg -n` before opening large files.
- Do not scan `node_modules`, `vendor`, `.build`, plans, historical audits, large references, binary assets, or inactive n8n exports unless explicitly needed.
- Do not read large files wholesale. Read the smallest relevant ranges.
- For layout/template work, stay in templates/assets unless backend behavior is in scope.
- Do not create root-level scratch plans or fix logs. Use `.ai/memory/` for ephemeral notes.
- Prefer repo-verifiable evidence over generic recommendations. Never invent analytics, rankings, keyword volumes, conversions, or runtime state.

## WordPress/runtime rules

- Deployable runtime is `blocksy-child/`.
- `functions.php` stays a thin bootstrap; modules belong in `inc/`.
- Keep canonical/meta/schema/robots logic centralized.
- Use `home_url()` for internal URLs and escape output.
- Preserve `data-track-*` hooks on conversion surfaces.
- Do not add React/Vue/Angular, client-side routing, heavy libraries, analytics code, IDs, pixels, cookies, or third-party scripts unless explicitly requested.
- Prefer vanilla JS, native browser APIs, progressive enhancement, feature detection, and `prefers-reduced-motion`.
- Do not move or rename `blocksy-child/`.
- Ignore `automations/n8n/` unless n8n is explicitly in scope.

## Product boundaries

- Public role: WordPress Freelancer; connected expertise: technical SEO, tracking, conversion.
- Energy intent routes through the Solar/Wärmepumpe cluster and Marktcheck.
- Generic WordPress/tracking/CRO/technical-SEO intent routes to project enquiry.
- Agency intent routes to White-Label.
- SEO query ownership and CTA routing are separate contracts.
- Do not reintroduce retired positioning, seat counters, deadlines, Shopify focus, or site-wide Marktcheck routing.

For exact wording and route behavior, read the canonical sources instead of restating them here.

## Validation

Use deterministic checks before LLM re-review.

After a coherent code change, run the narrowest relevant lint/test first. Before push or PR, run at minimum:

```bash
npm run lint:architecture
npm run lint:php
```

Also run task-specific CI scripts named by the routed skill. Fix from exit codes and error output; do not substitute prose review for a failing validator.

## Git / deploy

- Keep one coherent intent per commit/PR.
- Prefer a PR for architecture, tracking contracts, REST contracts, schema, registries, deployment configuration, or large theme changes.
- `main` may deploy production after CI passes.
- Never change `.github/workflows/deploy.yml` unless deploy behavior is the task.
- After a merged PR, continue from updated `main`.

## Update triggers

Update the canonical source when its contract changes:
- runtime/route status → `docs/architecture/LIVE_STATUS.md`
- cross-system dependencies → `docs/architecture/SYSTEM_MAP.md`
- CTA routing → `docs/architecture/CONVERSION_ROUTING.md`
- positioning/public copy → `docs/standards/BRAND_AND_COPY.md`
- SEO ownership → `docs/seo/query-ownership.csv`
- repeatable agent workflow → primary skill or delegated specialist skill
