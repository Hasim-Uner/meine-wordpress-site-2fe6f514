# Skills Context

Scope: `agents/skills/`.

## Architecture

`agents/skills/` contains two layers:

1. **Primary skills** — the only skills exposed to Codex/Claude discovery. They are listed in `PRIMARY_SKILLS.txt`.
2. **Internal specialist skills** — focused workflows, scripts, vendored references, and legacy helpers loaded only when a primary skill delegates to them.

The primary layer reduces router ambiguity without deleting working specialist logic.

Hard rules:
- Every skill directory has `SKILL.md`.
- `PRIMARY_SKILLS.txt` is the discovery allowlist and single source for public skill exposure.
- `.agents/skills/` and `.claude/skills/` contain relative symlinks only for primary skills.
- Never edit skill content through discovery symlinks.
- Keep `SKILL.md` short: trigger, delegation, first command/check, hard rules, deliverable.
- Put repeatable mechanics in scripts instead of prose. Scripts read runtime facts (routes, redirects, slugs) from their source; never copy them.
- A documented first command prints a summary that fits a tool-output window; line-level detail is opt-in per path (`--lines`). `test_first_command_budget.py` enforces the size.
- Cite functions or sections, never `file:line`; line numbers go stale with the next edit.
- Specialists do not route: no routing tables, no dispatcher chains. Out-of-scope work goes back to the owning primary. `npm run lint:skills` enforces ownership, this rule and that a primary names only specialists from its row below.
- Do not duplicate brand/copy/CTA/SEO ownership rules; reference canonical docs.
- Large references/guides are opt-in, never default context.

## Primary routing

Primary routes: see `AGENTS.md` Skill routing. This file only holds the delegation below.

## Internal delegation map

| Primary | Internal specialists |
| --- | --- |
| `agent-system-maintenance` | no mandatory delegate |
| `frontend-system` | `b2b-design-system`, `modern-web-guidance`, `emil-design-eng`, `animate`, `review-animations`, `improve-animations` |
| `seo-intelligence` | `seo-agent`, `seo-live-qa`, `seo-drift`, `internal-linking-audit` |
| `seo-cockpit-dev` | no mandatory delegate |
| `editorial-seo` | `blog-seo-ux-optimizer`, `pillar-cornerstone-writer`; `internal-linking-audit`, `modern-web-guidance` when the subtask needs them |
| `conversion-copy` | `buyer-research`, `seo-conversion-copywriting`, `conversion-copy-loop`, `copy-anatomy` |
| `offer-funnel-intelligence` | `buyer-research`, `modern-web-guidance` when the subtask needs them |
| `conversion-architecture` | `wordpress-cro-content-design-audit`, `route-conversion-review`, `landing-page-builder`, `homepage-proof-monitoring` |
| `wordpress-growth-architecture` | `b2b-design-system` for included UI craft |
| `performance-marketing` | `page-speed-audit` |
| `deploy-qa` | `pre-deploy-smoke`, `registry-release-qa`, `navigation-migration` |
| `revenue-learning-loop` | no mandatory delegate |

## Specialist-only notes

- `emil-design-eng`, `animate`, `review-animations`, `improve-animations` are vendored from `emilkowalski/skills` (MIT). Keep them unchanged and refresh from upstream when needed.
- `modern-web-guidance` remains a versioned specialist reference. Retrieve only task-matching guidance; never load its guide tree wholesale.
- `homepage-proof-monitoring` is a pre/post-release proof helper; final KPI decisions belong to `revenue-learning-loop`.

## Routing rule

Choose one primary skill by user intent. The primary skill may load one or more listed specialist skills only when the subtask requires them. Do not make the user-facing router choose among specialists.

Reuse the selected local context and already-loaded contracts. A separate
implementation phase may hand off to another primary owner without loading all
owners upfront. See `agent-system-maintenance/references/benchmark.md` only when
evaluating routing or comparing agent runs.

## Validation rule

Run the narrowest delegated validator first. Before push/PR run:

```bash
npm run lint:architecture
npm run lint:php
```

Use script output as the source of truth for deterministic failures.
