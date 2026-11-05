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

## Claude Code hooks

Registered in `.claude/settings.json`; tests live in
`agents/skills/agent-system-maintenance/tests/test_claude_hooks.py`.

- `scripts/claude-guardrails.py` (PreToolUse) asks before edits to
  `.github/workflows/deploy.yml` or edits that remove `data-track-*` hooks, and
  runs `lint:architecture` plus `lint:php` before `git push`, denying it on failure.
- `scripts/claude-rule-router.py` (UserPromptSubmit) flags durable rules in a
  prompt; after the task, ask where to store them per `AGENTS.md` Update triggers.
- `scripts/claude-main-branch-reminder.sh` (Stop) reports unpublished commits and
  work left on a branch.

Hooks are reminders of the `AGENTS.md` policy, not authorization to commit,
push, merge, or deploy, and not a security boundary.

## Arbeitsweise & Token-Budget

- Kleine, klare Änderungen (1–3 Dateien, Ursache bekannt): selbst erledigen, keine Subagenten.
- Suche über viele Dateien, Logs oder Testausgaben: an `Explore` delegieren, nur das Ergebnis kommt zurück.
- Umsetzung mit klarem Plan über mehrere Dateien: an `implementer`.
- Eskalation: `implementer` scheitert 2× an derselben Stelle → `implementer-deep`, beide Fehlversuche in den Auftrag.
- Scheitert auch der: stoppen, Befund in 5 Zeilen an mich. Architekturfragen löst eine Opus-Session, kein weiterer Versuch.
- `reviewer` nur bei Auth, Zahlung, Tracking-Datenfluss, DB-Migration, mehr als 10 geänderten Dateien oder auf Ansage.
- Max-Effort nie automatisch.
- Subagenten-Aufträge vollständig schreiben (Ziel, betroffene Dateien, Akzeptanzkriterium), damit sie nicht neu suchen müssen.
- Berichte an mich: Ergebnis, geänderte Dateien, offene Punkte. Keine Logs.
