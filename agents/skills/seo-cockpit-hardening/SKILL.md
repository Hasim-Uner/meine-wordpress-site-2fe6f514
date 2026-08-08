---
name: seo-cockpit-hardening
description: Harden the existing WordPress SEO Cockpit without widening it into a rewrite. Use when work touches render helpers, internal links, Koko integration, paging, or runtime diagnostics.
---

# SEO Cockpit Hardening

Use this skill for focused work on the existing SEO Cockpit modules.

## Run First

```bash
agents/skills/seo-cockpit-hardening/scripts/print-focus.sh
```

Then read only the docs you need:

- `blocksy-child/inc/CONTEXT.md`
- `docs/systems/seo-cockpit.md`
- `docs/seo-cockpit-v2.md`

## Hard Constraints

- Preserve existing OAuth, snapshot, cron, and detail behavior.
- Do not bloat `seo-cockpit-ui.php` without extracting helpers.
- Use defensive defaults, `WP_Error`, `is_array()`, and `isset()` checks.
- Reuse existing SEO Cockpit layers before adding new modules.

## Regression Test

After changing the CSV export contract, run:

```bash
bash agents/skills/seo-cockpit-hardening/tests/run-export-tests.sh
```

## Focus Order

1. Render helpers and readability
2. Internal link graph
3. Koko context fusion
4. Paging and caps
5. Runtime diagnostics

## Deliver

- Repo changes
- Remaining live credential or plugin dependencies
- Any docs that must be updated with contract or runtime changes
