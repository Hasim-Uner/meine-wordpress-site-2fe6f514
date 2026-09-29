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
EXPORT="$ROOT/inc/seo-cockpit/seo-cockpit-market-export.php"
UI="$ROOT/inc/seo-cockpit/seo-cockpit-market-ui.php"
SYNC="$ROOT/inc/seo-cockpit/seo-cockpit-sync.php"
DASH="$ROOT/inc/seo-cockpit/seo-cockpit-dashboard-v3.php"
CSS="$ROOT/assets/css/seo-cockpit-market.css"

for file in "$LOADER" "$CLIENT" "$MARKET" "$EXPORT" "$UI" "$SYNC" "$DASH" "$CSS"; do
  require_file "$file"
done

# Provider stays behind the SEO-Cockpit admin/background bootstrap and uses
# runtime credentials instead of hardcoded secrets.
require_pattern "seo-cockpit-dataforseo-client\.php" "$LOADER"
require_pattern "seo-cockpit-market-intelligence\.php" "$LOADER"
require_pattern "seo-cockpit-market-export\.php" "$LOADER"
require_pattern "seo-cockpit-market-ui\.php" "$LOADER"
require_pattern "NEXUS_DATAFORSEO_LOGIN" "$CLIENT"
require_pattern "NEXUS_DATAFORSEO_PASSWORD" "$CLIENT"
require_pattern "Authorization.*Basic" "$CLIENT"
forbid_pattern "api\.dataforseo\.com[^']*(login|password)=" "$CLIENT"

# The scheduled market layer uses batched Labs endpoints plus one bounded
# domain-level backlink summary. Live SERP endpoints remain explicit/manual.
require_pattern "dataforseo_labs/google/ranked_keywords/live" "$MARKET"
require_pattern "dataforseo_labs/google/competitors_domain/live" "$MARKET"
require_pattern "dataforseo_labs/google/keyword_overview/live" "$MARKET"
require_pattern "backlinks/summary/live" "$MARKET"
require_pattern "function nexus_market_intelligence_fetch_authority" "$MARKET"
require_pattern "Autorität & Linkprofil" "$UI"
require_pattern "serp/google/organic/live/advanced" "$MARKET"
require_pattern "serp/google/maps/live/advanced" "$MARKET"
require_pattern "organic_live_location_name" "$CLIENT"
require_pattern "organic_live_depth" "$CLIENT"
require_pattern "\['depth'\].*organic_live_depth|task\['depth'\]" "$MARKET"
require_pattern "nicht in Top" "$UI"
require_pattern "function nexus_refresh_market_intelligence\(" "$MARKET"
require_pattern "function nexus_refresh_market_intelligence_live\(" "$MARKET"
require_pattern "function nexus_queue_market_intelligence_live_refresh\(" "$MARKET"
require_pattern "function nexus_run_market_intelligence_live_background_step\(" "$MARKET"
require_pattern "admin_post_nopriv_nexus_market_intelligence_live_worker" "$MARKET"
require_pattern "nexus_queue_market_intelligence_live_refresh" "$UI"
require_pattern "läuft im Hintergrund" "$UI"
require_pattern "organic_live_rows.*organic_job" "$UI"
require_pattern "Neue Top-" "$UI"
require_pattern "nexus_refresh_market_intelligence\( true \)" "$MARKET"
forbid_pattern "nexus_refresh_market_intelligence_strategic_overview\(.*true" "$MARKET"

# Cost control and cache discipline are part of the provider contract.
require_pattern "monthly_auto_budget_usd" "$CLIENT"
require_pattern "auto_cost_month_usd" "$CLIENT"
require_pattern "nexus_dataforseo_auto_budget_available" "$CLIENT"
require_pattern "nexus_market_intelligence_snapshot_v1" "$MARKET"
require_pattern "nexus_dataforseo_market_weekly_refresh" "$MARKET"

# Strategic competitors are an explicit market-definition layer, separate from
# the automatic organic-overlap competitors.
require_pattern "strategic_competitors" "$CLIENT"
require_pattern "function nexus_market_intelligence_strategic_domains" "$MARKET"
require_pattern "function nexus_market_intelligence_strategic_competitors" "$MARKET"
require_pattern "function nexus_refresh_market_intelligence_strategic_overview" "$MARKET"
require_pattern "dataforseo_labs/google/domain_rank_overview/live" "$MARKET"
require_pattern "admin_post_nexus_market_intelligence_strategic_refresh" "$UI"
require_pattern "strategic_overview.*current" "$MARKET"
require_pattern "Strategische Domains prüfen" "$UI"
require_pattern "Strategische Vergleichsgruppe" "$UI"

# Export is snapshot-only: it may read DataForSEO-derived data but must never
# make a provider request itself.
require_pattern "function nexus_build_market_intelligence_export_rows" "$EXPORT"
require_pattern "admin_post_nexus_market_intelligence_export" "$EXPORT"
require_pattern "Market CSV" "$UI"
require_pattern "competitor_strategic" "$EXPORT"
require_pattern "authority_summary" "$EXPORT"
require_pattern "opportunity" "$EXPORT"
forbid_pattern "nexus_dataforseo_request" "$EXPORT"
forbid_pattern "wp_remote_(get|post|request)" "$EXPORT"

# Market data joins the existing snapshot and command center, not a parallel
# frontend analytics stack.
require_pattern "['\"]market['\"][[:space:]]*=>" "$SYNC"
require_pattern "nexus_get_market_intelligence_opportunities" "$SYNC"
require_pattern "nexus_get_market_intelligence_segment" "$MARKET"
require_pattern "function nexus_market_intelligence_opportunity_candidates" "$MARKET"
require_pattern "\$overview[[:space:]]*=.*keyword_overview" "$MARKET"
require_pattern "is_ranking_gap" "$MARKET"
require_pattern "Ranking & Owner prüfen" "$MARKET"
require_pattern "Content-Gap prüfen" "$MARKET"
require_pattern "Ranking-Gap" "$UI"
require_pattern "ranking_gap" "$EXPORT"
require_pattern "market_source" "$EXPORT"
require_pattern "Geschäftschance" "$MARKET"
require_pattern "segment_label" "$UI"
require_pattern "nexus_render_market_intelligence_dashboard_panel" "$DASH"
require_pattern "Markt & Wettbewerb" "$UI"

echo "dataforseo market smoke ok"
