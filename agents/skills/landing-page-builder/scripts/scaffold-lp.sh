#!/usr/bin/env bash

set -euo pipefail

title="${1:-Landing Page Title}"
slug_input="${2:-$title}"

slug="$(
  printf '%s' "$slug_input" \
    | tr '[:upper:]' '[:lower:]' \
    | sed 's/ä/ae/g; s/ö/oe/g; s/ü/ue/g; s/ß/ss/g' \
    | sed 's/[^a-z0-9]/-/g' \
    | sed 's/-\{2,\}/-/g; s/^-//; s/-$//'
)"

file="blocksy-child/page-${slug}.php"

cat <<EOF
Landing Page Scaffold
=====================

File:  ${file}
Slug:  /${slug}/
Title: ${title}

Required WordPress setup:
  1. Create page "/${slug}/" in WordPress admin
  2. Assign template "page-${slug}.php"

--- Template Structure ---

<?php
/**
 * Template Name: ${title}
 */
get_header();
?>

<!-- Section: Hero -->
<section class="lp-hero" data-track-section="hero">
  <h1><!-- Ad-aligned headline --></h1>
  <p><!-- Supporting subline --></p>
  <a href="<?php echo esc_url(home_url('/solar-waermepumpen-leadgenerierung/#marktcheck')); ?>"
     class="btn btn-primary"
     data-track-action="cta_hero"
     data-track-category="landing_page">
    Marktcheck starten
  </a>
</section>

<!-- Section: Problem -->
<section class="lp-problem" data-track-section="problem">
</section>

<!-- Section: Mechanism -->
<section class="lp-mechanism" data-track-section="mechanism">
</section>

<!-- Section: Proof -->
<section class="lp-proof" data-track-section="proof">
</section>

<!-- Section: CTA -->
<section class="lp-cta" data-track-section="cta_bottom">
  <a href="<?php echo esc_url(home_url('/solar-waermepumpen-leadgenerierung/#marktcheck')); ?>"
     class="btn btn-primary"
     data-track-action="cta_bottom"
     data-track-category="landing_page">
    Marktcheck starten
  </a>
</section>

<?php get_footer(); ?>

--- SEO ---

Meta title:
Meta description:
Ad headline alignment:
EOF
