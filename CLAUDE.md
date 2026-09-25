# Claude Code Instructions

@AGENTS.md

Follow the shared contract's load order and select the matching canonical skill from
`agents/skills/` before implementation. Claude Code exposes those skills through
`.claude/skills/`; do not edit the symlinks or their targets through that path.

For a broad strategy/audit task that benefits from the Opus behavior profile, read:

```text
agents/model-profiles/claude-opus-performance-marketing.md
```

## Opus operating style

Use Opus for deeper reasoning, not broader uncontrolled edits. Before changing
files, produce a compact plan containing:

- relevant files inspected
- assumed business goal
- likely repo tasks
- manual WordPress/admin tasks
- risk level

Keep the plan proportional to the task, then apply the shared scope, evidence,
audit, safety, and publishing rules from `AGENTS.md`.

## Claude Code hook

`scripts/claude-main-branch-reminder.sh` reports unpublished commits and work
left on a branch. Treat it as a reminder of the shared Git/deploy policy in
`AGENTS.md`, not as authorization to commit, push, merge, or deploy.
