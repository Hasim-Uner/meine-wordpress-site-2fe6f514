# Docs Context

Scope: durable documentation under `docs/`.

## Durable Docs

- `docs/architecture/`: live status and system map
- `docs/systems/`: operating docs for real systems
- `docs/decisions/`: durable decisions
- `docs/seo/` and `docs/ux/`: stable guidance

## Non-Durable Docs

- `docs/audits/`: historical audits, not source of truth
- `docs/references/`: supporting artifacts and snapshots
- Repetitive agent workflows belong in `agents/skills/`, not in a docs playbook

## Rules

- Do not create new fix logs, session plans, or temporary workaround notes here.
- If information is short-lived and session-specific, store it under `.ai/memory/`.
- If the content is procedural and reusable, promote it to a skill instead of another playbook.
- Keep `LIVE_STATUS.md` about current behavior and `SYSTEM_MAP.md` about system boundaries.
