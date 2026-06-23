#!/usr/bin/env bash

set -euo pipefail

mode="${1:-full}"

print_seo() {
  cat <<'EOF'
[SEO]
- Confirm one H1 and coherent heading order.
- Check title, description, canonical, robots, indexability, and schema ownership.
- RankMath is legacy-only (rank_math_* fallback); new SEO data flows through ACF + the custom WP SEO Cockpit.
- Map money pages, supporting pages, internal links, and primary search intent.
- Verify canonical internal links point to primary URLs only.
- Review Core Web Vitals risks: hero media, blocking CSS/JS, layout shifts.
EOF
}

print_cro() {
  cat <<'EOF'
[CRO]
- Confirm one primary CTA per viewport cluster.
- Verify audit-first funnel alignment and risk-reversal copy.
- Remove CTA drift, proof fragmentation, and unnecessary choice points.
- Check mobile scannability and section order.
- Make outcomes concrete before adding visual polish.
EOF
}

print_content() {
  cat <<'EOF'
[CONTENT]
- Match the page to one intent and one offer stage.
- Replace vague agency wording with concrete business outcomes.
- Link to one service page and related proof or cluster assets.
- Keep terminology stable across hero, proof, and CTA blocks.
- For autopost planning, connect every topic to an offer, objection, proof point, or money page.
EOF
}

print_tracking() {
  cat <<'EOF'
[TRACKING]
- Do not assume cookie-based tracking, ad pixels, or active campaigns.
- Preserve or add neutral data-track attributes on core CTA surfaces only when useful.
- Confirm repo-owned event hooks versus external Google Analytics for WP / GA4 / GTM setup.
- Recommended planning events: CTA click, form submit, audit request, email click, phone click, lead source.
- Keep measurement notes separate from runtime code changes unless explicitly requested.
EOF
}

print_offers() {
  cat <<'EOF'
[OFFERS]
- Prioritize SEO audit, tracking audit, autopost, landing pages, lead generation, and WordPress technical implementation.
- Check whether each offer has: problem, outcome, proof, process, CTA, FAQ, and internal-link target.
- Separate money pages from supporting education posts.
- Flag missing offer pages, weak CTAs, unclear package boundaries, and duplicated positioning.
EOF
}

case "$mode" in
  full)
    print_seo
    print_cro
    print_content
    print_tracking
    print_offers
    ;;
  seo)
    print_seo
    ;;
  cro)
    print_cro
    ;;
  content)
    print_content
    ;;
  tracking)
    print_tracking
    ;;
  offers)
    print_offers
    ;;
  *)
    echo "Usage: $0 {full|seo|cro|content|tracking|offers}" >&2
    exit 1
    ;;
esac
