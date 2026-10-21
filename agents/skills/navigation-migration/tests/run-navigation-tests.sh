#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd -P)"
cd "$ROOT"

# The checklist must render the contract, not a hand-kept list.
output="$(bash agents/skills/navigation-migration/scripts/print-checklist.sh)"
grep -q '^- Referenzen -> /referenzen/ \[nav_header_results\]$' <<<"$output"
grep -q '^- Leistungen: Conversion-Tracking /ga4-tracking-setup/.*Server-Side Tracking /server-side-tracking-b2b/' <<<"$output"
grep -q '^- Spezialisierung & Wissen: Solar & Wärmepumpe /solar-waermepumpen-leadgenerierung/' <<<"$output"
if grep -Eq 'ergebnisse/' <<<"$output"; then
  echo 'checklist lists a retired target' >&2
  exit 1
fi
# This generic-context checklist keeps the specialised Marktcheck off global navigation.
if grep -q '#marktcheck' <<<"$output"; then
  echo 'checklist lists a specialised Marktcheck target in global navigation' >&2
  exit 1
fi

php scripts/tests/navigation-contract.php >/dev/null
