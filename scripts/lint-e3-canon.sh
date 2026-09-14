#!/usr/bin/env bash

set -euo pipefail

TARGETS=(
  "blocksy-child/front-page.php"
  "blocksy-child/page-ergebnisse.php"
  "blocksy-child/page-solar-waermepumpen-leadgenerierung.php"
  "blocksy-child/page-wordpress-agentur.php"
  "blocksy-child/page-wgos.php"
  "blocksy-child/inc/helpers.php"
  "blocksy-child/inc/seo-meta.php"
  "blocksy-child/inc/shortcodes.php"
  "blocksy-child/page-whitelabel-retainer.php"
  "blocksy-child/assets/js/homepage-mindmap-teaser.js"
  "blocksy-child/assets/js/homepage-mindmap-teaser.jsx"
  "blocksy-child/template-parts/audit-page-shell.php"
  "docs/architecture/LIVE_STATUS.md"
  "docs/architecture/SYSTEM_MAP.md"
)

PATTERN='(-83|–83|−83|83[[:space:]]*%|120[[:space:]]*€[[:space:]]*(→|->|zu)[[:space:]]*20[[:space:]]*€|~25[[:space:]]*€|12[[:space:]]*Monate)'

MATCHES="$(grep -En -- "${PATTERN}" "${TARGETS[@]}" || true)"

if [[ -n "${MATCHES}" ]]; then
  echo "E3 canon guard failed."
  echo "Use blocksy-child/inc/canon/e3-proof-canon.php for E3 proof metrics."
  echo
  echo "${MATCHES}"
  exit 1
fi

E3_CANON="blocksy-child/inc/canon/e3-proof-canon.php"
OG_BUILDER="scripts/build-anfragestrecke-og-image.py"

# Der E3-Proof-Canon darf nur dokumentierte Fallwerte als numerische
# Proof-Metriken enthalten. Diese historischen Konstanten waren Marktannahmen
# bzw. ungenutzte Portalwerte und duerfen nicht zurueckkehren.
LEGACY_ASSUMPTIONS='HU_E3_SALES_CONVERSION_BEFORE_LOW|HU_E3_SALES_CONVERSION_BEFORE_HIGH|HU_E3_PORTAL_CONVERSION_AVG|HU_E3_PORTAL_COST_PER_DEAL'
if grep -En -- "${LEGACY_ASSUMPTIONS}" "${E3_CANON}" >/tmp/hu-e3-legacy-assumptions.txt 2>/dev/null; then
  echo "E3 proof-boundary guard failed."
  echo "Unmeasured market/portal assumptions must not live as numeric E3 constants."
  cat /tmp/hu-e3-legacy-assumptions.txt
  rm -f /tmp/hu-e3-legacy-assumptions.txt
  exit 1
fi
rm -f /tmp/hu-e3-legacy-assumptions.txt

# Der alte 1–5-%-Wert war keine dokumentierte Kohortenmessung des Falls. Der
# Compatibility-Key darf die fehlende Messung beschreiben, aber keinen alten
# Zahlenwert wieder einfuehren.
if grep -En -- "1[[:space:]]*[–-][[:space:]]*5[[:space:]]*%" "${E3_CANON}" >/tmp/hu-e3-old-close-rate.txt 2>/dev/null; then
  echo "E3 close-rate guard failed."
  echo "The unmeasured 1–5 % before-rate must not return to the E3 proof canon."
  cat /tmp/hu-e3-old-close-rate.txt
  rm -f /tmp/hu-e3-old-close-rate.txt
  exit 1
fi
rm -f /tmp/hu-e3-old-close-rate.txt

# Das OG-Bild darf die drei Proof-Werte nicht als zweite Zahlenquelle spiegeln.
# Es muss sie zur Build-Zeit aus den PHP-Konstanten lesen.
if grep -En -- 'CPL_VORHER[[:space:]]*=[[:space:]]*"150 €"|CPL_NACHHER[[:space:]]*=[[:space:]]*"22 €"|ZEITRAUM[[:space:]]*=[[:space:]]*"6 Monate"' "${OG_BUILDER}" >/tmp/hu-e3-og-literals.txt 2>/dev/null; then
  echo "E3 OG-image guard failed."
  echo "The OG builder must read proof values from the E3 canon instead of mirroring literals."
  cat /tmp/hu-e3-og-literals.txt
  rm -f /tmp/hu-e3-og-literals.txt
  exit 1
fi
rm -f /tmp/hu-e3-og-literals.txt

for constant in HU_E3_CPL_BEFORE HU_E3_CPL_AFTER HU_E3_TIMEFRAME_MONTHS; do
  if ! grep -Fq "canon_int('${constant}')" "${OG_BUILDER}"; then
    echo "E3 OG-image guard failed. Missing canon read for ${constant}."
    exit 1
  fi
done
