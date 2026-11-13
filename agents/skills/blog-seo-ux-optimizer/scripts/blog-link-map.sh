#!/usr/bin/env sh
set -eu

ROOT="${1:-.}"

printf '%s\n' '== Blog/category template internal URLs =='
rg -n "home_url\\(|href=[\"']/" "$ROOT/blocksy-child/single.php" "$ROOT/blocksy-child/category.php" "$ROOT/blocksy-child/template-parts" "$ROOT/blocksy-child/inc/blog"*.php \
  -g '*.php' \
  -g '!node_modules' -g '!.build' || true

# Only the link itself (anchor text and target): the surrounding paragraph is
# prose, and a width cap would cut links that sit late in a long line.
printf '\n%s\n' '== Versioned blog Markdown links (anchor and target only) =='
rg -n -o "\\[[^\\]]*\\]\\(/[^)]*\\)|href=[\"']/[^\"']*[\"'][^>]*>[^<]{0,80}" "$ROOT/blocksy-child/assets/content/blog" "$ROOT/content/blog-drafts" \
  -g '*.md' -g '*.html' \
  -g '!node_modules' -g '!.build' || true

# The route opens each llms.txt entry, so the description may be cut.
printf '\n%s\n' '== Canonical route index excerpts =='
rg -n --max-columns 200 --max-columns-preview "/blog/|/wordpress-agentur-hannover/|/solar-waermepumpen-leadgenerierung/|/case-study-solar-leadgenerierung/|/stack-|/server-side-tracking-b2b/" "$ROOT/llms.txt" || true
