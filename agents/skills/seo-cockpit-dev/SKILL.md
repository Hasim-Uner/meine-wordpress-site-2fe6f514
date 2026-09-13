---
name: seo-cockpit-dev
description: "Primary router for development, debugging, and hardening of the custom WordPress SEO Cockpit."
---

# SEO Cockpit Dev

Trigger: SEO Cockpit modules, GSC/Koko data ingestion, diagnostics, queues, scoring, or cockpit UI/backend behavior.

Delegate implementation detail to:
`agents/skills/seo-cockpit-hardening/SKILL.md`

Hard rules:
- Do not assume RankMath owns runtime SEO behavior.
- Keep external data facts separate from UI/diagnostic logic.
- Preserve existing REST/admin contracts unless the task explicitly changes them.
- Use existing test/diagnostic scripts before LLM-only debugging.

Deliverable: scoped cockpit fix/plan with reproducible validation.
