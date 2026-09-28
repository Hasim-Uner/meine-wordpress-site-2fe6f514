#!/usr/bin/env bash
set -euo pipefail

ROOT="${1:-blocksy-child}"

fail() {
  echo "content decision smoke failed: $*" >&2
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

LOADER="$ROOT/inc/seo-cockpit/seo-cockpit.php"
DECISIONS="$ROOT/inc/seo-cockpit/seo-cockpit-content-decisions.php"
V11="$ROOT/inc/seo-cockpit/seo-cockpit-content-intelligence-v11.php"
RESEARCH_ASYNC="$ROOT/inc/seo-cockpit/seo-cockpit-research-async.php"
RESEARCH_V3="$ROOT/inc/seo-cockpit/seo-cockpit-research-v3.php"
MARKET_UI="$ROOT/inc/seo-cockpit/seo-cockpit-market-ui.php"
CSS="$ROOT/assets/css/seo-cockpit-content-decisions.css"

for file in "$LOADER" "$DECISIONS" "$V11" "$RESEARCH_ASYNC" "$RESEARCH_V3" "$MARKET_UI" "$CSS"; do
  require_file "$file"
done

# The existing V1.1 signal engine remains the evidence layer.
require_pattern "function nexus_ci_v11_opportunities" "$V11"
require_pattern "seo-cockpit-content-decisions\.php" "$LOADER"

# The decision layer consumes cached/local signals only.
require_pattern "function nexus_ci_content_decisions" "$DECISIONS"
require_pattern "nexus_ci_v11_opportunities" "$DECISIONS"
require_pattern "nexus_get_market_intelligence_snapshot" "$DECISIONS"
require_pattern "nexus_get_seo_cockpit_lead_snapshot_data" "$DECISIONS"
require_pattern "nexus_get_seo_cockpit_crm_acquisition_snapshot_data" "$DECISIONS"
require_pattern "nexus_get_seo_cockpit_page_role_scores" "$DECISIONS"

# Action-first UX contract.
require_pattern "Content-Chancen" "$DECISIONS"
require_pattern "Jetzt tun" "$DECISIONS"
require_pattern "Prüfen & planen" "$DECISIONS"
require_pattern "Warum jetzt" "$DECISIONS"
require_pattern "Nächster Schritt" "$DECISIONS"
require_pattern "Datenbasis anzeigen" "$DECISIONS"

# Research stays available but moves behind operational layers.
require_pattern "'Datenbasis'" "$RESEARCH_ASYNC"
require_pattern "nexus_register_seo_cockpit_research_page_async', 49" "$RESEARCH_ASYNC"
require_pattern "Datenbasis · Research Intelligence" "$RESEARCH_V3"
require_pattern "nexus_register_market_intelligence_admin_page', 44" "$MARKET_UI"

echo "content decision smoke ok"
