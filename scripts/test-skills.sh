#!/usr/bin/env bash
# Run every specialist suite; print successful suites once, failed logs in full.
set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd -P)"
cd "$ROOT"
export PYTHONDONTWRITEBYTECODE=1
log_file="$(mktemp "${TMPDIR:-/tmp}/skill-tests.XXXXXX")"
trap 'rm -f "$log_file"' EXIT
shopt -s nullglob
suites=(agents/skills/*/tests/*.sh)
if [[ ${#suites[@]} -eq 0 ]]; then
  echo 'FAIL: no skill test suites discovered' >&2
  exit 1
fi
failures=0
for suite in "${suites[@]}"; do
  if bash "$suite" >"$log_file" 2>&1; then
    printf 'PASS %s\n' "$suite"
  else
    printf 'FAIL %s\n' "$suite" >&2
    cat "$log_file" >&2
    failures=$((failures + 1))
  fi
done
printf '%s suites, %s failures\n' "${#suites[@]}" "$failures"
[[ "$failures" -eq 0 ]]
