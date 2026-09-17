#!/usr/bin/env bash
set -euo pipefail

ROOT="${1:-blocksy-child}"

fail() {
  echo "funnel-routing smoke failed: $*" >&2
  exit 1
}

require_file() {
  [[ -f "$1" ]] || fail "missing file: $1"
}

require_pattern() {
  local pattern="$1"
  local file="$2"
  grep -Eq "$pattern" "$file" || fail "missing pattern in $file: $pattern"
}

forbid_pattern() {
  local pattern="$1"
  local file="$2"
  if grep -Eq "$pattern" "$file"; then
    fail "forbidden pattern in $file: $pattern"
  fi
}

RESULTS_PAGE="$ROOT/page-ergebnisse.php"
RESULTS_DATA="$ROOT/template-parts/results/data.php"
RESULTS_NEXT="$ROOT/template-parts/results/next.php"
RESULTS_ROW="$ROOT/template-parts/results/next-row.php"
RESULTS_JS="$ROOT/assets/js/results-routing.js"
RESULTS_CSS="$ROOT/assets/css/ergebnisse.css"
CONTACT_PAGE="$ROOT/page-kontakt.php"

for file in "$RESULTS_PAGE" "$RESULTS_DATA" "$RESULTS_NEXT" "$RESULTS_ROW" "$RESULTS_JS" "$RESULTS_CSS" "$CONTACT_PAGE"; do
  require_file "$file"
done

# 1) Direct-project entry is a scoped review, not an unspecific contact CTA.
require_pattern "Projekt kurz prüfen und sauber eingrenzen" "$RESULTS_DATA"
require_pattern "Projekt prüfen lassen" "$RESULTS_DATA"
require_pattern "hu_get_contact_intake_url\( 'project', 'tracking' \)" "$RESULTS_DATA"
require_pattern "Tracking-Projekt prüfen lassen" "$RESULTS_DATA"

# 2) The proof hub keeps all three commercial paths, but exactly one row is
# visually primary at a time and the immediate same-origin route may reorder it.
for kind in project agency energy; do
  require_pattern "'kind'[[:space:]]*=>[[:space:]]*'$kind'" "$RESULTS_DATA"
  require_pattern "data-funnel-kind" "$RESULTS_ROW"
done
require_pattern "erg-next-row--primary" "$RESULTS_ROW"
require_pattern "erg-next-row--primary" "$RESULTS_CSS"
require_pattern "server-side-tracking-b2b" "$RESULTS_JS"
require_pattern "whitelabel-retainer" "$RESULTS_JS"
require_pattern "solar-waermepumpen-leadgenerierung" "$RESULTS_JS"
require_pattern "data-funnel-context" "$RESULTS_NEXT"
require_pattern "results-routing.js" "$RESULTS_PAGE"

# 3) This routing is intentionally not analytics. It may read the immediate
# same-origin referrer or an explicit ?from= context, but it must not persist,
# identify, beacon or submit visitor behaviour.
for forbidden in "localStorage" "sessionStorage" "document.cookie" "dataLayer" "fetch\\(" "sendBeacon"; do
  forbid_pattern "$forbidden" "$RESULTS_JS"
done
require_pattern "document.referrer" "$RESULTS_JS"
require_pattern "referrer.origin[[:space:]]*!==[[:space:]]*window.location.origin" "$RESULTS_JS"
require_pattern "URLSearchParams" "$RESULTS_JS"

# 4) The measurable conversion boundary remains the submitted request itself.
# A pre-scoped direct CTA carries its business focus into the existing contact
# contract, where the already known topic is skipped instead of asked twice.
require_pattern "requested_focus" "$CONTACT_PAGE"
require_pattern "selected_focus" "$CONTACT_PAGE"
require_pattern 'visible_step_count[[:space:]]*=[[:space:]]*3[[:space:]]*-[[:space:]]*\([[:space:]]*\$is_scoped_focus' "$CONTACT_PAGE"
require_pattern "data-contact-step-skip" "$CONTACT_PAGE"

# Passive future instrumentation stays present without requiring an analytics
# runtime today.
require_pattern "data-track-action" "$RESULTS_ROW"
require_pattern 'data-track-category="lead_gen"' "$RESULTS_ROW"

echo "funnel-routing smoke ok"
