#!/usr/bin/env bash

set -euo pipefail

mode="${1:-all}"

print_reindex() {
  cat <<'EOF'
[REINDEX]
- /solar-waermepumpen-leadgenerierung/
- /case-study-solar-leadgenerierung/
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
Quelle: nexus_get_legacy_offer_redirect_map() in blocksy-child/inc/helpers.php
- /growth-audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /customer-journey-audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /360-audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /wordpress-tech-audit/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /system-diagnose/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /anfrage/ -> /solar-waermepumpen-leadgenerierung/#marktcheck
- /wordpress-agentur/ -> /wordpress-agentur-hannover/
- /alle-loesungen-im-detail/ -> /alle-loesungen/
- /e3-new-energy/ -> /case-study-solar-leadgenerierung/
- /case-e3/ -> /case-study-solar-leadgenerierung/
- /case-studies/e3-new-energy/ -> /case-study-solar-leadgenerierung/
- /photovoltaik-leads-kaufen-alternative/ -> /solar-waermepumpen-leadgenerierung/
- /category/owned-leads/ -> /eigene-leadgenerierung-vs-portale/
- /sitemap_index.xml -> /wp-sitemap.xml
EOF
}

print_gone() {
  cat <<'EOF'
[410 GONE — absichtlich, kein zu behebender Fehler]
Quelle: nexus_get_retired_gone_paths() in blocksy-child/inc/helpers.php
Diese Pfade sind bewusst aus dem oeffentlichen Crawl-Surface genommen. Sie
sammeln keine Crawl-Nachfrage mehr und spalten kein Canonical-Signal. Ein
301-Redirect auf einen dieser Pfade waere eine Rueckabwicklung der Entscheidung,
kein Bugfix — siehe docs/architecture/LIVE_STATUS.md.
- /wordpress-seo-hannover/
- /core-web-vitals/
- /conversion-rate-optimization/
- /wordpress-wartung-hannover/
- /roi-rechner/
- /seo/
- /kostenlose-tools/
- /kostenlose-tools/website-performance-analyse/
- /tools/
- /tools/website-performance-analyse/
- /website-performance-analyse/
- /audit-linkedin/
- /shopify-wartungsvertrag/
EOF
}

print_noindex() {
  cat <<'EOF'
[NOINDEX / GESCHUETZT — weder Redirect noch 410]
Quelle: hu_get_noindex_follow_slugs() / hu_get_noindex_nofollow_slugs() in
blocksy-child/inc/seo-meta.php
Diese Pfade existieren weiter, sind aber kein oeffentliches Primaerziel. Nicht
als kaputte Route melden und nicht reaktivieren.
- /case-studies/, /case-studies-e-commerce/  (noindex, follow)
- /meta-ads/                                  (noindex, follow)
- /alle-loesungen/, /loesungen/               (noindex, follow)
- /case-study-solar-leadgenerierung/          (noindex bis Anonymisierungs-Hold faellt)
- /wordpress-growth-operating-system/, /wgos-systemlandkarte/, /wgos-assets/*
                                              (noindex bzw. access-protected,
                                               solange intern als Dashboard-/
                                               Asset-Hub genutzt)
EOF
}

print_mapping() {
  cat <<'EOF'
[PRIMARY URL MAP]
- cold Solar/SHK demand -> /solar-waermepumpen-leadgenerierung/#marktcheck
- proof -> /case-study-solar-leadgenerierung/
- wordpress agentur hannover -> /wordpress-agentur-hannover/
- ga4 tracking setup / server-side tracking / consent mode -> /ga4-tracking-setup/
- pv termine b2b / pv leads gewerbe / b2b photovoltaik -> /b2b-solar-leads/
- leadgenerierung photovoltaik / photovoltaik leads -> /solar-waermepumpen-leadgenerierung/
- checkfox* -> /checkfox-solar-waermepumpe-einordnung/
- aroundhome* -> /aroundhome-solar-einordnung/

Kein Primaerziel mehr (die alten Slugs liefern 410, siehe oben):
wordpress seo hannover, wordpress wartung hannover, conversion rate optimization.
"core web vitals optimierung" zeigt auf /wgos-assets/cwv-optimierung/ — das ist
ein interner, noindex/access-protected Asset-Pfad, kein oeffentliches Ziel.

Verbindliche Quelle je Query: docs/seo/query-ownership.csv
(Pruefen: bash agents/skills/seo-agent/scripts/intent-gate.sh audit)
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
    print_gone
    print_noindex
    print_mapping
    print_live_qa
    ;;
  reindex)
    print_reindex
    ;;
  redirects)
    print_redirects
    ;;
  gone)
    print_gone
    ;;
  noindex)
    print_noindex
    ;;
  mapping)
    print_mapping
    ;;
  live-qa)
    print_live_qa
    ;;
  *)
    echo "Usage: $0 {all|reindex|redirects|gone|noindex|mapping|live-qa}" >&2
    exit 1
    ;;
esac
