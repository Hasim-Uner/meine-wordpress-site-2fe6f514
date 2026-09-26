#!/usr/bin/env bash

set -euo pipefail

files=(
  "blocksy-child/inc/seo-cockpit/seo-cockpit.php"
  "blocksy-child/inc/seo-cockpit/seo-cockpit-core.php"
  "blocksy-child/inc/seo-cockpit/seo-cockpit-api.php"
  "blocksy-child/inc/seo-cockpit/seo-cockpit-koko.php"
  "blocksy-child/inc/seo-cockpit/seo-cockpit-links.php"
  "blocksy-child/inc/seo-cockpit/seo-cockpit-sync.php"
  "blocksy-child/inc/seo-cockpit/seo-cockpit-insights.php"
  "blocksy-child/inc/seo-cockpit/seo-cockpit-diagnostics.php"
  "blocksy-child/inc/seo-cockpit/seo-cockpit-ui.php"
  "docs/seo-cockpit-v2.md"
  "docs/systems/seo-cockpit.md"
)

echo "[SCOPE FILES]"
printf '%s\n' "${files[@]}"
echo
echo "[LINE COUNTS]"
wc -l "${files[@]}"
echo
echo "[FOCUS]"
echo "1. Extract repeated UI render blocks instead of widening seo-cockpit-ui.php."
echo "2. Keep internal link counting normalized to the same URL model as the cockpit."
echo "3. Treat Koko as traffic context, not a replacement for Search Console."
echo "4. Add paging and caps deliberately, not by inflating row limits."
echo "5. Add small runtime diagnostics for OAuth, cron, and drilldown stability."
