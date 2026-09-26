#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd -P)"
cd "$ROOT"

# The checklist must render the contract, not a hand-kept list.
output="$(bash agents/skills/navigation-migration/scripts/print-checklist.sh)"
grep -q '^- Tracking -> /ga4-tracking-setup/ \[nav_header_tracking\]$' <<<"$output"
grep -q '^- Leistungen: Server-Side Tracking /server-side-tracking-b2b/' <<<"$output"
if grep -Eq 'ergebnisse/|#marktcheck' <<<"$output"; then
  echo 'checklist lists a retired or Marktcheck target' >&2
  exit 1
fi

php scripts/tests/navigation-contract.php >/dev/null
