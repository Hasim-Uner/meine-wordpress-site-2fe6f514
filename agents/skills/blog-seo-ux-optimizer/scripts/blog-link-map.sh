#!/usr/bin/env sh
set -eu

ROOT="${1:-.}"

printf '%s\n' '== Blog/category template internal URLs =='
rg -n "home_url\\(|href=[\"']/" "$ROOT/blocksy-child/single.php" "$ROOT/blocksy-child/category.php" "$ROOT/blocksy-child/template-parts" "$ROOT/blocksy-child/inc/blog"*.php \
  -g '*.php' \
  -g '!node_modules' -g '!.build' || true

printf '\n%s\n' '== Versioned blog Markdown links =='
rg -n "\\]\\(/|href=[\"']/" "$ROOT/blocksy-child/assets/content/blog" "$ROOT/content/blog-drafts" \
  -g '*.md' -g '*.html' \
  -g '!node_modules' -g '!.build' || true

printf '\n%s\n' '== Canonical route index excerpts =='
rg -n "/blog/|/wordpress-agentur-hannover/|/solar-waermepumpen-leadgenerierung/|/case-study-solar-leadgenerierung/|/stack-|/server-side-tracking-b2b/" "$ROOT/llms.txt" || true
