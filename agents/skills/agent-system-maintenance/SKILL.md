---
name: agent-system-maintenance
description: "Maintain repo agent instructions, skill routing, context efficiency and skill test integration for Codex and Claude Code."
---

# Agent System Maintenance

Use for agent/skill architecture work. Website audits belong to
`performance-marketing`; release verification belongs to `deploy-qa`.

Start with `git status --short` and `npm run lint:skills`. Reuse the selected
`agents/skills/CONTEXT.md`; inspect only the changed entrypoints and their callers.

- Keep canonical skill content in `agents/skills/`. Register new primary skills
  in `PRIMARY_SKILLS.txt`, the routing tables and both relative discovery links.
- Keep entrypoints short; place conditional workflows in linked references.
  Preserve non-obvious safety/contract constraints when shortening instructions.
- Run `npm run check` after changes; use `-- --plan` to preview the selection.
  Agent changes include skill suites; unknown/tooling changes use all checks.
  For dependencies and CI behavior, see [checks](references/checks.md).
- For routing changes or cross-agent comparisons, use
  [benchmark](references/benchmark.md). A fixture/schema pass is not an agent eval.
- For switching agents on unfinished work, use [handoff](references/handoff.md).
- Report bytes/lines as context proxies only. Actual token/cost claims need
  measured host usage; include correctness and rework in comparisons.
- Change host/plugin settings only within the user's requested scope.

Deliver: concrete changes, validation evidence, measured context deltas and any
unrun behavioral evaluation. Keep temporary observations in `.ai/memory/`.
