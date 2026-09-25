# Agent Skills

This directory is the canonical store for repo-specific agent skills.

The router sees only the reduced public surface listed in `PRIMARY_SKILLS.txt`. Focused implementation workflows remain here as internal specialists and are loaded only through a primary skill.

## Discovery

- Canonical content: `agents/skills/`
- Codex discovery: `.agents/skills/`
- Claude Code discovery: `.claude/skills/`

The two discovery directories contain symlinks only for primary skills. Do not edit through them.

## How to route

1. Read `AGENTS.md`.
2. Read one local `CONTEXT.md`.
3. Pick one primary skill from `PRIMARY_SKILLS.txt`.
4. Let that skill delegate to internal specialists when needed.
5. Open only task-relevant files/references.
6. Prefer deterministic scripts over repeated LLM review.

`agents/skills/CONTEXT.md` contains the complete primary-to-specialist map.

## Why specialists remain

Specialist skills keep tested scripts, narrow workflows, vendored upstream material, and route-specific knowledge without forcing every agent host to discover all of them. This preserves capability while reducing routing ambiguity and default context.

## Agent-system checks

- `npm run lint:skills`: public entrypoint budgets, reference links and benchmark fixture validity.
- `npm run test:skills`: all specialist regression suites, with full logs only on failure.
- `npm run benchmark:agents -- --prompts`: blind routing prompts for independent host runs.

For measured comparisons use [the benchmark protocol](agent-system-maintenance/references/benchmark.md).
Static checks do not prove agent behavior or token savings. Host ignore-file behavior
must be verified in the host; the architecture guard only checks recorded patterns.
