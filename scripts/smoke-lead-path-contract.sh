#!/usr/bin/env bash
set -euo pipefail

ROOT="${1:-blocksy-child}"

fail() {
  echo "lead-path smoke failed: $*" >&2
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

CORE_JS="$ROOT/assets/js/nexus-core.js"
SOLAR_JS="$ROOT/assets/js/solar-leadgenerierung-solara.js"
CRM_PHP="$ROOT/inc/review-crm.php"
COCKPIT_LEADS="$ROOT/inc/seo-cockpit/seo-cockpit-leads.php"

require_file "$CORE_JS"
require_file "$SOLAR_JS"
require_file "$CRM_PHP"
require_file "$COCKPIT_LEADS"

# Frontend attribution helper must still expose the fields consumed by the CRM.
require_pattern "getLeadAttributionPayload" "$CORE_JS"
for field in landing_page_url entry_page_url previous_internal_url referrer_url; do
  require_pattern "$field" "$CORE_JS"
  require_pattern "$field" "$SOLAR_JS"
  require_pattern "$field" "$CRM_PHP"
done

# Solar marketcheck must submit the energy intake to the shared REST contract.
require_pattern "/wp-json/nexus/v1/audit-request" "$SOLAR_JS"
require_pattern "intake_variant:[[:space:]]*'energy_systems'" "$SOLAR_JS"
require_pattern "audit_type:[[:space:]]*'b2b_system_intake'" "$SOLAR_JS"
require_pattern "Object\\.keys\\(attribution\\)" "$SOLAR_JS"

# REST route, schema, sanitization and CRM meta persistence must remain wired.
require_pattern "register_rest_route" "$CRM_PHP"
require_pattern "'nexus/v1'" "$CRM_PHP"
require_pattern "'/audit-request'" "$CRM_PHP"
require_pattern "nexus_validate_energy_review_request_payload" "$CRM_PHP"
require_pattern "nexus_sanitize_review_request_internal_url" "$CRM_PHP"
require_pattern "_nexus_review_landing_page_url" "$CRM_PHP"
require_pattern "_nexus_review_entry_page_url" "$CRM_PHP"
require_pattern "_nexus_review_previous_internal_url" "$CRM_PHP"
require_pattern "_nexus_review_referrer_url" "$CRM_PHP"
require_pattern "energy_systems_landing" "$CRM_PHP"

# SEO Cockpit must distinguish measured attribution from source-derived fallback.
require_pattern "nexus_get_seo_cockpit_review_request_attribution_target" "$COCKPIT_LEADS"
require_pattern "'inferred'[[:space:]]*=>[[:space:]]*true" "$COCKPIT_LEADS"
require_pattern "inferred_requests" "$COCKPIT_LEADS"

echo "lead-path smoke ok"
