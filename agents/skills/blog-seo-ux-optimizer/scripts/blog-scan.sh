#!/usr/bin/env sh
# Blog surface scan.
#
#   blog-scan.sh [ROOT]            summary: hits per file, stale positioning, content files
#   blog-scan.sh --lines PATH...   matching lines for those paths only
#
# The default stays a summary on purpose: the surface pattern matches thousands
# of theme lines, which exceeds every host's tool-output window.
set -eu

SURFACE_PATTERN='blog|category|single|related|author|toc|article'
LINE_WIDTH=240
SUMMARY_LIMIT=25

if [ "${1:-}" = "--lines" ]; then
  shift
  if [ "$#" -eq 0 ]; then
    echo 'usage: blog-scan.sh --lines PATH...' >&2
    exit 64
  fi
  rg -n --max-columns "$LINE_WIDTH" --max-columns-preview "$SURFACE_PATTERN" "$@" || true
  exit 0
fi

ROOT="${1:-.}"

printf '%s\n' '== Blog surfaces (hits per file, strongest first) =='
{ rg -c "$SURFACE_PATTERN" "$ROOT/blocksy-child" \
    -g '*.php' -g '*.css' -g '*.js' \
    -g '!node_modules' -g '!.build' || true; } \
  | sort -t: -k2,2nr -k1,1 \
  | awk -F: -v limit="$SUMMARY_LIMIT" '
      NR <= limit { printf "  %5d  %s\n", $2, $1 }
      END { if (NR > limit) printf "  ... %d more files\n", NR - limit
            if (NR == 0) print "  (none)" }'
printf '%s\n' 'Line detail for one surface: blog-scan.sh --lines <path>...'

printf '\n%s\n' '== Stale public-facing positioning candidates =='
rg -n --max-columns "$LINE_WIDTH" --max-columns-preview \
  "Growth Audit|Growth Partner|Growth Architect|WordPress Growth Operating System|WGOS|Shopify|Full-Stack Digital Marketer|Growth-System|Growth-Infrastruktur" \
  "$ROOT/blocksy-child" "$ROOT/content" \
  -g '*.php' -g '*.md' -g '*.html' -g '*.js' -g '*.css' \
  -g '!node_modules' -g '!.build' || true

printf '\n%s\n' '== Blog content files =='
rg --files "$ROOT/blocksy-child/assets/content/blog" "$ROOT/content/blog-drafts" \
  -g '*.md' -g '*.html' \
  -g '!node_modules' -g '!.build' || true
