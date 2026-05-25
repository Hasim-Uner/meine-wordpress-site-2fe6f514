#!/usr/bin/env sh
set -eu

ROOT="${1:-.}"

printf '%s\n' '== Blog surfaces =='
rg -n "blog|category|single|related|author|toc|article" "$ROOT/blocksy-child" \
  -g '*.php' -g '*.css' -g '*.js' \
  -g '!node_modules' -g '!.build' || true

printf '\n%s\n' '== Stale public-facing positioning candidates =='
rg -n "Growth Audit|Growth Partner|Growth Architect|WordPress Growth Operating System|WGOS|Shopify|Full-Stack Digital Marketer|Growth-System|Growth-Infrastruktur" \
  "$ROOT/blocksy-child" "$ROOT/content" \
  -g '*.php' -g '*.md' -g '*.html' -g '*.js' -g '*.css' \
  -g '!node_modules' -g '!.build' || true

printf '\n%s\n' '== Blog content files =='
rg --files "$ROOT/blocksy-child/assets/content/blog" "$ROOT/content/blog-drafts" \
  -g '*.md' -g '*.html' \
  -g '!node_modules' -g '!.build' || true
