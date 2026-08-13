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

forbid_pattern() {
  local pattern="$1"
  local file="$2"
  if grep -Eq "$pattern" "$file"; then
    fail "forbidden pattern in $file: $pattern"
  fi
}

CORE_JS="$ROOT/assets/js/nexus-core.js"
SOLAR_JS="$ROOT/assets/js/solar-leadgenerierung-solara.js"
CRM_PHP="$ROOT/inc/review-crm.php"
COCKPIT_LEADS="$ROOT/inc/seo-cockpit/seo-cockpit-leads.php"
COCKPIT_COMMAND="$ROOT/inc/seo-cockpit/seo-cockpit-command-center.php"
DIAGNOSE_CANON="$ROOT/inc/canon/diagnose-canon.php"
PRICING_CANON="$ROOT/inc/canon/pricing-canon.php"

require_file "$CORE_JS"
require_file "$SOLAR_JS"
require_file "$CRM_PHP"
require_file "$COCKPIT_LEADS"
require_file "$COCKPIT_COMMAND"
require_file "$DIAGNOSE_CANON"
require_file "$PRICING_CANON"

# Frontend attribution helper must still expose the fields consumed by the CRM.
require_pattern "getLeadAttributionPayload" "$CORE_JS"
for field in landing_page_url entry_page_url previous_internal_url referrer_url ads_source ads_keyword; do
  require_pattern "$field" "$CORE_JS"
  require_pattern "$field" "$SOLAR_JS"
  require_pattern "$field" "$CRM_PHP"
done

# Solar marketcheck must submit the energy intake to the shared REST contract.
require_pattern "/wp-json/nexus/v1/audit-request" "$SOLAR_JS"
require_pattern "intake_variant:[[:space:]]*'energy_systems'" "$SOLAR_JS"
require_pattern "audit_type:[[:space:]]*'b2b_system_intake'" "$SOLAR_JS"
require_pattern "Object\\.keys\\(attribution\\)" "$SOLAR_JS"

# The active intake must submit actual answers. Team size and portal pressure
# used to fabricate lead volume, CPL and a bottleneck in the browser, which made
# CRM qualification look more precise than the visitor's answers allowed.
for field in solution_focus business_fit sales_team_size project_timing; do
  require_pattern "$field" "$SOLAR_JS"
  require_pattern "$field" "$CRM_PHP"
done
forbid_pattern "mapSalesTeamToLeadVolume" "$SOLAR_JS"
forbid_pattern "mapMarginLossToCplRange" "$SOLAR_JS"
forbid_pattern "mapMarginLossToBottleneck" "$SOLAR_JS"
require_pattern "no_owner" "$SOLAR_JS"
require_pattern "no_owner" "$CRM_PHP"

# Other routes promise the length of this intake before the visitor reaches it.
# That promise reads from the diagnosis canon, so the canon has to keep matching
# the form: the intake grew from three steps to five while the homepage, the
# case study and the test route still advertised a sixty-second, three-step
# marketcheck.
js_steps="$(grep -cE "^[[:space:]]*key: '" "$SOLAR_JS")"
canon_steps="$(grep -oE "define\( 'HU_MARKETCHECK_STEPS', [0-9]+" "$DIAGNOSE_CANON" | grep -oE '[0-9]+$')"
[[ -n "$canon_steps" ]] || fail "missing HU_MARKETCHECK_STEPS in $DIAGNOSE_CANON"
[[ "$js_steps" == "$canon_steps" ]] || fail "intake has $js_steps steps, HU_MARKETCHECK_STEPS says $canon_steps"

# The build price may not live as literal copy in templates. It drifted once
# already: the money page moved to the canon, three other routes kept quoting
# the retired 12.000-18.000 range.
require_pattern "HU_FOUNDATION_HOSTING_MONTHLY" "$PRICING_CANON"
require_pattern "function hu_foundation_total_display" "$PRICING_CANON"

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

# Repo-owned outcome reporting must use the same qualified|nurture contract as
# the CRM. Otherwise a successful form redesign cannot be evaluated by lead
# quality in the command center.
require_pattern "['\"]qualified['\"][[:space:]]*=>[[:space:]]*0" "$COCKPIT_LEADS"
require_pattern "\[['\"]qualified['\"]\][[:space:]]*(\+\+|\+=)" "$COCKPIT_LEADS"
require_pattern "\[[[:space:]]*['\"]qualified['\"][[:space:]]*\]" "$COCKPIT_COMMAND"
forbid_pattern "\[[[:space:]]*['\"]green['\"][[:space:]]*,[[:space:]]*['\"]yellow['\"][[:space:]]*\]" "$COCKPIT_COMMAND"

# ---------------------------------------------------------------------------
# Kontaktformular: Thema/Typ-Kombination
#
# Am 2026-07-09 bot das Themen-Select alle $focus_options an, waehrend nur eine
# Teilmenge der Anfragetypen angeboten wurde. Jede unpassende Kombination
# quittierte das Backend mit einem stillen invalid_focus_type-400 — der
# Besucher sah nichts, die Anfrage war weg (494788d, "verlorene Leads").
#
# Der Fehler war eine statische Vertragsverletzung zwischen Formular und
# Validator, also hier pruefbar: kein Browser, keine Live-Seite, kein
# Test-Lead. Faellt eine der vier Zusicherungen, ist der Weg wieder offen.
# ---------------------------------------------------------------------------

CONTACT_PAGE="$ROOT/page-kontakt.php"
CONTACT_JS="$ROOT/assets/js/contact.js"
CONTACT_INC="$ROOT/inc/contact-page.php"

require_file "$CONTACT_PAGE"
require_file "$CONTACT_JS"
require_file "$CONTACT_INC"

# 1. Der Server lehnt unpassende Kombinationen weiterhin ab. Ohne diese Pruefung
#    waere der Client-Guard die einzige Verteidigung.
require_pattern "invalid_focus_type" "$CONTACT_INC"

# 2. Das Select bietet ausschliesslich die gefilterten Themen an. Die Rohliste
#    zu iterieren war genau der Fehler.
require_pattern 'public_focus_options[[:space:]]*=[[:space:]]*array_filter' "$CONTACT_PAGE"
require_pattern 'foreach \([[:space:]]*\$public_focus_options' "$CONTACT_PAGE"
forbid_pattern 'foreach \([[:space:]]*\$focus_options' "$CONTACT_PAGE"

# 3. Jede Option traegt ihre erlaubten Typen, damit der JS-Guard sie lesen kann.
require_pattern 'data-types=' "$CONTACT_PAGE"

# 4. Der Client-Guard prueft die Kombination vor dem Absenden — die zweite
#    Verteidigungslinie, wenn der Options-Filter durch Cache oder Race umgangen
#    wird.
require_pattern "getAttribute\\('data-types'\\)" "$CONTACT_JS"

echo "lead-path smoke ok"
