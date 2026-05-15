#!/usr/bin/env bash

set -euo pipefail

base_ref="${1:-HEAD~1}"
head_ref="${2:-HEAD}"

files=(
  "blocksy-child/inc/glossary/glossary-registry-data.php"
  "blocksy-child/inc/wgos/wgos-asset-registry-data.php"
)

changed="$(git diff --name-only "$base_ref" "$head_ref" -- "${files[@]}" || true)"

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
- /wordpress-growth-operating-system/
- /wgos-systemlandkarte/
- /core-web-vitals/
- /ga4-tracking-setup/
EOF
