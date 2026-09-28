#!/usr/bin/env bash
set -euo pipefail

ROOT="${1:-blocksy-child}"

fail() {
  echo "dataforseo market smoke failed: $*" >&2
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
CLIENT="$ROOT/inc/seo-cockpit/seo-cockpit-dataforseo-client.php"
MARKET="$ROOT/inc/seo-cockpit/seo-cockpit-market-intelligence.php"
UI="$ROOT/inc/seo-cockpit/seo-cockpit-market-ui.php"
SYNC="$ROOT/inc/seo-cockpit/seo-cockpit-sync.php"
DASH="$ROOT/inc/seo-cockpit/seo-cockpit-dashboard-v3.php"
CSS="$ROOT/assets/css/seo-cockpit-market.css"

for file in "$LOADER" "$CLIENT" "$MARKET" "$UI" "$SYNC" "$DASH" "$CSS"; do
  require_file "$file"
done

# Provider stays behind the SEO-Cockpit admin/background bootstrap and uses
# runtime credentials instead of hardcoded secrets.
require_pattern "seo-cockpit-dataforseo-client\.php" "$LOADER"
require_pattern "seo-cockpit-market-intelligence\.php" "$LOADER"
require_pattern "seo-cockpit-market-ui\.php" "$LOADER"
require_pattern "NEXUS_DATAFORSEO_LOGIN" "$CLIENT"
require_pattern "NEXUS_DATAFORSEO_PASSWORD" "$CLIENT"
require_pattern "Authorization.*Basic" "$CLIENT"
forbid_pattern "api\.dataforseo\.com[^']*(login|password)=" "$CLIENT"

# V1 uses batched Labs endpoints for the scheduled market layer. Live SERP
# endpoints exist only in the explicit manual watchlist function.
require_pattern "dataforseo_labs/google/ranked_keywords/live" "$MARKET"
require_pattern "dataforseo_labs/google/competitors_domain/live" "$MARKET"
require_pattern "dataforseo_labs/google/keyword_overview/live" "$MARKET"
require_pattern "serp/google/organic/live/advanced" "$MARKET"
require_pattern "serp/google/maps/live/advanced" "$MARKET"
require_pattern "function nexus_refresh_market_intelligence\(" "$MARKET"
require_pattern "function nexus_refresh_market_intelligence_live\(" "$MARKET"
require_pattern "nexus_refresh_market_intelligence\( true \)" "$MARKET"

# Cost control and cache discipline are part of the provider contract.
require_pattern "monthly_auto_budget_usd" "$CLIENT"
require_pattern "auto_cost_month_usd" "$CLIENT"
require_pattern "nexus_dataforseo_auto_budget_available" "$CLIENT"
require_pattern "nexus_market_intelligence_snapshot_v1" "$MARKET"
require_pattern "nexus_dataforseo_market_weekly_refresh" "$MARKET"

# Market data joins the existing snapshot and command center, not a parallel
# frontend analytics stack.
require_pattern "['\"]market['\"][[:space:]]*=>" "$SYNC"
require_pattern "nexus_get_market_intelligence_opportunities" "$SYNC"
require_pattern "nexus_get_market_intelligence_segment" "$MARKET"
require_pattern "Geschäftschance" "$MARKET"
require_pattern "segment_label" "$UI"
require_pattern "nexus_render_market_intelligence_dashboard_panel" "$DASH"
require_pattern "Markt & Wettbewerb" "$UI"

echo "dataforseo market smoke ok"
