#!/usr/bin/env bash

set -euo pipefail

JS="blocksy-child/assets/js/cpo-calculator.js"
PHP="blocksy-child/inc/cpo-calculator.php"
FAIL=0

check() {
  local pattern="$1"
  local file="$2"
  local message="$3"
  if grep -Fq -- "$pattern" "$file"; then
    printf '  ✓ %s\n' "$message"
  else
    printf '  ✗ %s\n' "$message"
    FAIL=1
  fi
}

printf '\n=== CPO V2 cohort guard ===\n'

check 'function calculateCohort(input)' "$JS" 'shared cohort engine exists'
check 'mediaCosts + setupCosts + supportSoftware + salesCosts' "$JS" 'full-cost numerator includes media, setup, software/support and sales time'
check 'wonOrders > 0 ? totalCosts / wonOrders : null' "$JS" 'CPO denominator is actually won orders and zero wins return no numeric result'
check 'noch nicht bestimmbar' "$JS" 'zero-order state is explicit'
check 'vorläufig' "$JS" 'immature cohort is explicitly flagged'
check '[data-strecke-rechner]' "$JS" 'money-page calculator is upgraded by the shared engine'
check "hu_enqueue_js( 'nexus-cpo-calculator-js', 'cpo-calculator.js', [ 'nexus-anfragestrecke-js' ] );" "$PHP" 'shared engine loads after legacy money-page calculator'
check "is_page( 'solar-waermepumpen-leadgenerierung' )" "$PHP" 'money-page route loads CPO V2'

if (( FAIL )); then
  echo 'CPO V2 guard failed.'
  exit 1
fi

echo 'CPO V2 guard passed.'
