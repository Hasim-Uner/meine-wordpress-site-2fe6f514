#!/usr/bin/env bash

set -euo pipefail

base_ref="${1:-HEAD~1}"
head_ref="${2:-HEAD}"

files=(
  "blocksy-child/inc/glossary/glossary-registry-data.php"
  "blocksy-child/inc/wgos/wgos-asset-registry-data.php"
)

if [[ "$head_ref" == "WORKTREE" ]]; then
  changed="$(git diff --name-only "$base_ref" -- "${files[@]}" || true)"
else
  changed="$(git diff --name-only "$base_ref" "$head_ref" -- "${files[@]}" || true)"
fi

if [[ -z "$changed" ]]; then
	echo "No glossary or WGOS registry changes detected between $base_ref and $head_ref."
  exit 0
fi

echo "Registry QA required for:"
printf ' - %s\n' $changed
echo
echo "Mandatory smoke URLs:"
cat <<'EOF'
- /glossar/utm-parameter/
- /glossar/owned-leads/
- /solar-leads-kaufen-alternative/
- /wordpress-agentur-hannover/
- /wgos-assets/
- /wgos-assets/cwv-optimierung/
- /ga4-tracking-setup/
EOF
