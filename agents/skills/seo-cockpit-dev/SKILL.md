---
name: seo-cockpit-dev
description: "Primary router for development, debugging, and hardening of the custom WordPress SEO Cockpit."
---

# SEO Cockpit Dev

Trigger: SEO Cockpit modules, GSC/Koko data ingestion, diagnostics, queues,
scoring, render helpers, internal link graph, paging, or cockpit UI/backend behavior.

Run first, then read only what the task needs from `docs/systems/seo-cockpit.md`
and `docs/seo-cockpit-v2.md`:

```bash
bash agents/skills/seo-cockpit-dev/scripts/print-focus.sh
```

Hard rules:
- Harden the existing cockpit; do not widen it into a rewrite.
- Do not assume RankMath owns runtime SEO behavior.
- Keep external data facts separate from UI/diagnostic logic.
- Preserve existing OAuth, snapshot, cron, detail, REST and admin contracts
  unless the task explicitly changes them.
- Do not bloat `seo-cockpit-ui.php` without extracting helpers; reuse existing
  cockpit layers before adding modules.
- Use defensive defaults, `WP_Error`, `is_array()` and `isset()` checks.

Focus order: render helpers and readability → internal link graph → Koko context
fusion → paging and caps → runtime diagnostics.

Validation: after changing the CSV export contract or cockpit display helpers run

```bash
bash agents/skills/seo-cockpit-dev/tests/run-export-tests.sh
bash agents/skills/seo-cockpit-dev/tests/run-display-tests.sh
```

Deliver: scoped cockpit fix/plan with reproducible validation, remaining live
credential or plugin dependencies, and docs that must change with the contract.
