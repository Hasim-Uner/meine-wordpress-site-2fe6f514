#!/usr/bin/env bash

# Stop-Hook fuer Claude Code.
#
# Repo-Policy (CLAUDE.md): Aenderungen gehen direkt auf `main`, ein PR ist die
# Ausnahme. Dieser Hook meldet am Ende einer Antwort, wenn Arbeit noch nicht
# dort gelandet ist — als Hinweis, nicht als Blocker. Er schreibt nichts und
# pusht nichts.
#
# Ausgabe: JSON mit systemMessage (sichtbar fuer den Nutzer) und
# additionalContext (geht zurueck ins Modell).

set -uo pipefail

root="${CLAUDE_PROJECT_DIR:-}"
if [ -z "$root" ]; then
  root="$(git rev-parse --show-toplevel 2>/dev/null || true)"
fi
[ -n "$root" ] || exit 0
cd "$root" 2>/dev/null || exit 0

git rev-parse --git-dir >/dev/null 2>&1 || exit 0

branch="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || true)"
[ -n "$branch" ] || exit 0

count_against_main() {
  git rev-list --count origin/main..HEAD 2>/dev/null || echo 0
}

notes=()

if [ "$branch" = "main" ]; then
  ahead="$(count_against_main)"
  if [ "${ahead:-0}" -gt 0 ] 2>/dev/null; then
    notes+=("$ahead Commit(s) auf main sind noch nicht gepusht. Push auf main loest den Deploy aus.")
  fi
else
  unmerged="$(count_against_main)"
  if [ "${unmerged:-0}" -gt 0 ] 2>/dev/null; then
    notes+=("Branch '$branch' traegt $unmerged Commit(s), die nicht auf main sind. Repo-Policy ist direkt auf main (CLAUDE.md); ein PR ist die Ausnahme fuer Aenderungen, deren Ergebnis man auf der Live-Seite nicht sehen kann.")
  fi
fi

if ! git diff --quiet 2>/dev/null || ! git diff --cached --quiet 2>/dev/null; then
  notes+=("Es liegen uncommittete Aenderungen im Arbeitsverzeichnis.")
fi

[ "${#notes[@]}" -gt 0 ] || exit 0

msg="$(printf '%s ' "${notes[@]}")"
msg="${msg% }"

jq -n --arg m "$msg" '{
  systemMessage: $m,
  hookSpecificOutput: { hookEventName: "Stop", additionalContext: $m }
}'
