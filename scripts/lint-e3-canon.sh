#!/usr/bin/env bash

set -euo pipefail

TARGETS=(
  "blocksy-child/front-page.php"
  "blocksy-child/page-anfrage.php"
  "blocksy-child/page-case-e3.php"
  "blocksy-child/page-case-studies-e-commerce.php"
  "blocksy-child/page-solar-waermepumpen-leadgenerierung.php"
  "blocksy-child/page-wordpress-agentur.php"
  "blocksy-child/page-wgos.php"
  "blocksy-child/inc/helpers.php"
  "blocksy-child/inc/seo-meta.php"
  "blocksy-child/inc/shortcodes.php"
  "blocksy-child/page-whitelabel-retainer.php"
  "blocksy-child/assets/html/wgos-social-proof.html"
  "blocksy-child/assets/js/homepage-mindmap-teaser.js"
  "blocksy-child/assets/js/homepage-mindmap-teaser.jsx"
  "blocksy-child/wgos-video.html"
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
