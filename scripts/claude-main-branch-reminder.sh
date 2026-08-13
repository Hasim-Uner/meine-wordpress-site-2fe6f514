#!/usr/bin/env bash

# Stop-Hook fuer Claude Code.
#
# Die gemeinsame Git-/Deploy-Policy steht in AGENTS.md. Dieser Hook meldet am
# Ende einer Antwort unveroeffentlichte oder noch nicht integrierte Arbeit — als
# Hinweis, nicht als Freigabe oder Blocker. Er schreibt und pusht nichts.
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
    notes+=("$ahead Commit(s) auf main sind noch nicht gepusht. Ein Push auf main kann bei CI-erfassten Pfaden nach erfolgreichen Checks den Deploy ausloesen.")
  fi
else
  unmerged="$(count_against_main)"
  if [ "${unmerged:-0}" -gt 0 ] 2>/dev/null; then
    notes+=("Branch '$branch' traegt $unmerged Commit(s), die nicht auf main sind. Vor dem Veroeffentlichen gilt die gemeinsame Git-/PR-Policy aus AGENTS.md.")
  fi
fi

status_porcelain="$(git status --porcelain --untracked-files=normal 2>/dev/null || true)"
if [ -n "$status_porcelain" ]; then
  notes+=("Es liegen uncommittete Aenderungen im Arbeitsverzeichnis.")
fi

[ "${#notes[@]}" -gt 0 ] || exit 0

msg="$(printf '%s ' "${notes[@]}")"
msg="${msg% }"

json_escape() {
  local value="$1"
  value="${value//\\/\\\\}"
  value="${value//\"/\\\"}"
  value="${value//$'\n'/\\n}"
  value="${value//$'\r'/\\r}"
  value="${value//$'\t'/\\t}"
  printf '%s' "$value"
}

escaped_msg="$(json_escape "$msg")"
printf '{"systemMessage":"%s","hookSpecificOutput":{"hookEventName":"Stop","additionalContext":"%s"}}\n' \
  "$escaped_msg" "$escaped_msg"
