#!/usr/bin/env bash
# Shared input for copy/canon guards. Local checks include untracked files.
set -euo pipefail
base="$1"
head="$2"
shift 2
refs=("$base")
[[ -z "$head" ]] || refs+=("$head")
git diff --unified=0 --no-color --no-ext-diff --no-textconv "${refs[@]}" -- "$@"
if [[ -z "$head" ]]; then
  while IFS= read -r -d '' path; do
    # Link targets are paths, not public copy; never walk linked directories.
    [[ ! -L "$path" ]] || continue
    [[ -f "$path" ]] || { printf 'Cannot inspect untracked file: %s\n' "$path" >&2; exit 1; }
    # --no-index returns 1 when a new file has content; all other errors fail.
    git diff --no-index --unified=0 --no-color --no-ext-diff --no-textconv -- /dev/null "$path" || [[ "$?" -eq 1 ]]
  done < <(git ls-files --others --exclude-standard -z -- "$@")
fi
