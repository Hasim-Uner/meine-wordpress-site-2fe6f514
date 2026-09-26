# Claude Code Instructions

@AGENTS.md

Follow the shared contract's load order and select the matching canonical skill from
`agents/skills/` before implementation. Claude Code exposes those skills through
`.claude/skills/`; do not edit the symlinks or their targets through that path.

## Working style

Reason first; change only what the request and the selected skill place in scope.
Before changing files, give a plan proportional to the task: files inspected,
intended change and validation; add manual WordPress/admin follow-ups and risk
when they apply. Then follow the `AGENTS.md` sections Context discipline,
WordPress/runtime rules, Product boundaries, Validation and Git / deploy.

## Claude Code hook

`scripts/claude-main-branch-reminder.sh` reports unpublished commits and work
left on a branch. Treat it as a reminder of the shared Git/deploy policy in
`AGENTS.md`, not as authorization to commit, push, merge, or deploy.
