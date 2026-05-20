#!/usr/bin/env bash

set -euo pipefail

mode="${1:-all}"

print_reindex() {
  cat <<'EOF'
[REINDEX]
- /solar-waermepumpen-leadgenerierung/
- /e3-new-energy/
- /wordpress-agentur-hannover/
- /ergebnisse/
- /ga4-tracking-setup/
- /performance-marketing/
- /blog/
EOF
}

print_redirects() {
  cat <<'EOF'
[REDIRECTS]
- /growth-audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /customer-journey-audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /360-audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /wordpress-tech-audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /wordpress-seo-hannover/ -> /wordpress-agentur-hannover/#technisches-seo
- /wordpress-wartung-hannover/ -> /wordpress-agentur-hannover/#wordpress-wartung
- /conversion-rate-optimization/ -> /wordpress-agentur-hannover/#methode
- /core-web-vitals/ -> /wgos-assets/cwv-optimierung/ or /wordpress-agentur-hannover/#methode
- /case-studies/ -> /ergebnisse/
- /case-studies-e-commerce/ -> /ergebnisse/
- /meta-ads/ -> canonical target
- /wordpress-agentur/ -> canonical target
- /roi-rechner/ -> canonical target
- /shopify-wartungsvertrag/ -> 410
EOF
}

print_mapping() {
  cat <<'EOF'
[PRIMARY URL MAP]
- cold Solar/SHK demand -> /solar-waermepumpen-leadgenerierung/#marktcheck
- proof -> /e3-new-energy/
- wordpress agentur hannover -> /wordpress-agentur-hannover/
- wordpress seo hannover -> /wordpress-agentur-hannover/#technisches-seo
- wordpress wartung hannover -> /wordpress-agentur-hannover/#wordpress-wartung
- ga4 tracking setup / server-side tracking / consent mode -> /ga4-tracking-setup/
- core web vitals optimierung / pagespeed optimierung -> /wgos-assets/cwv-optimierung/
EOF
}

print_live_qa() {
  cat <<'EOF'
[LIVE QA]
- Check title, description, canonical, og:url on every primary URL.
- Check live DOM for duplicate H1s and duplicate schema output.
- Check homepage, footer, related content, and hubs for legacy internal links.
- Confirm the active sitemap source is consistent with the canonical map.
EOF
}

case "$mode" in
  all)
    print_reindex
    print_redirects
    print_mapping
    print_live_qa
    ;;
  reindex)
    print_reindex
    ;;
  redirects)
    print_redirects
    ;;
  mapping)
    print_mapping
    ;;
  live-qa)
    print_live_qa
    ;;
  *)
    echo "Usage: $0 {all|reindex|redirects|mapping|live-qa}" >&2
    exit 1
    ;;
esac
