#!/usr/bin/env bash
set -euo pipefail

ROOT="${1:-blocksy-child}"

fail() {
  echo "seo decision layer smoke failed: $*" >&2
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

LOADER="$ROOT/inc/seo-cockpit/seo-cockpit.php"
DECISION="$ROOT/inc/seo-cockpit/seo-cockpit-decision-layer.php"
DASH="$ROOT/inc/seo-cockpit/seo-cockpit-dashboard-v3.php"
RESEARCH="$ROOT/inc/seo-cockpit/seo-cockpit-research-v3.php"
CSS="$ROOT/assets/css/seo-cockpit-decision.css"

for file in "$LOADER" "$DECISION" "$DASH" "$RESEARCH" "$CSS"; do
  require_file "$file"
done

# The decision layer must load after Content Intelligence so it can consume the
# existing V1.1 signal engine without replacing provider logic.
require_pattern "seo-cockpit-content-intelligence-state\.php" "$LOADER"
require_pattern "seo-cockpit-decision-layer\.php" "$LOADER"

# It is an orchestration/read layer, never another provider client.
forbid_pattern "nexus_dataforseo_request" "$DECISION"
forbid_pattern "wp_remote_(get|post|request)" "$DECISION"
forbid_pattern "api\.dataforseo\.com" "$DECISION"
forbid_pattern "chromeuxreport\.googleapis\.com" "$DECISION"

# Existing intelligence is normalized into one queue.
require_pattern "nexus_get_market_intelligence_opportunities" "$DECISION"
require_pattern "nexus_ci_v11_opportunities" "$DECISION"
require_pattern "nexus_get_seo_cockpit_decision_layer" "$DECISION"
require_pattern "Warum jetzt\?" "$DECISION"
require_pattern "Nächster Schritt" "$DECISION"

# The old Opportunities renderers must not print alongside the action-first UI.
require_pattern "remove_action.*nexus_ci_render_admin_page" "$DECISION"
require_pattern "remove_action.*nexus_ci_v11_render_admin_page" "$DECISION"
require_pattern "Content-Chancen" "$DECISION"

# Research stays available, but its navigation language makes the hierarchy clear.
require_pattern "Datenquellen" "$RESEARCH"
require_pattern "Beleg-Layer" "$RESEARCH"

# Dashboard only surfaces the strongest decisions; full detail stays in the
# dedicated workspace.
require_pattern "nexus_render_decision_layer_dashboard_panel" "$DASH"
require_pattern "Nächste Entscheidungen" "$DECISION"

echo "seo decision layer smoke ok"
