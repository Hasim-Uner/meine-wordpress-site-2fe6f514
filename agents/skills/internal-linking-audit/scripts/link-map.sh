#!/usr/bin/env bash

set -euo pipefail

THEME="blocksy-child"

header() { printf '\n=== %s ===\n' "$1"; }

count_lines() {
  sed '/^[[:space:]]*$/d' | wc -l | tr -d '[:space:]'
}

# --- 1. Collect all page templates ---
header "Page Templates"

templates="$(
  rg --files "$THEME" \
    | awk -F/ 'NF == 2 && ($2 == "front-page.php" || $2 == "home.php" || $2 ~ /^page-.*\.php$/ || $2 ~ /^template-.*\.php$/) { print }' \
    | sort
)"

printf '%s\n' "$templates" | while IFS= read -r t; do
  [[ -n "$t" ]] || continue
  slug="$(basename "$t" .php | sed 's/^page-/\//' | sed 's/^front-page/\//' | sed 's/^template-/\//' | sed 's/$/\//')"
  printf '  %s -> %s\n' "$t" "$slug"
done

# --- 2. Extract internal links from templates ---
header "Link Matrix (template -> outbound links)"

template_parts=""
if [[ -d "$THEME/template-parts" ]]; then
  template_parts="$(rg --files "$THEME/template-parts" -g '*.php' | sort || true)"
fi

scan_files="$(printf '%s\n%s\n' "$templates" "$template_parts" | sed '/^[[:space:]]*$/d')"

printf '%s\n' "$scan_files" | while IFS= read -r tpl; do
  [[ -f "$tpl" ]] || continue
  name="$(basename "$tpl")"

  links="$(
    rg -o --no-filename "home_url\(\s*'[^']+'" "$tpl" 2>/dev/null \
      | sed -E "s/.*home_url\(\s*'([^']+)'.*/\1/" \
      | sort -u || true
  )"

  rel_links="$(
    rg -o --no-filename 'href="/[^"]*"' "$tpl" 2>/dev/null \
      | sed -E 's/^href="([^"]*)"/\1/' \
      | sort -u || true
  )"

  combined="$(printf '%s\n%s\n' "$links" "$rel_links" | sort -u | sed '/^[[:space:]]*$/d')"

  if [[ -n "$combined" ]]; then
    echo
    echo "  $name:"
    printf '%s\n' "$combined" | while IFS= read -r link; do
      printf '    -> %s\n' "$link"
    done
  fi
done

# --- 3. Find hub pages and their inbound count ---
header "Hub Page Inbound Links"

hubs=(
  "/solar-waermepumpen-leadgenerierung/"
  "/e3-new-energy/"
  "/ergebnisse/"
  "/blog/"
  "/wordpress-agentur-hannover/"
  "/uber-mich/"
)

for hub in "${hubs[@]}"; do
  matches="$(rg -l -F "$hub" "$THEME" -g '*.php' 2>/dev/null || true)"
  count="$(printf '%s\n' "$matches" | count_lines)"
  if (( count < 3 )); then
    icon="✗"
  elif (( count < 5 )); then
    icon="△"
  else
    icon="✓"
  fi
  printf '  %s %s — %d templates link here\n' "$icon" "$hub" "$count"
done

# --- 4. Find orphan templates ---
header "Potential Orphan Pages"

printf '%s\n' "$templates" | while IFS= read -r tpl; do
  [[ -n "$tpl" ]] || continue
  slug="$(basename "$tpl" .php | sed 's/^page-//')"
  [[ "$slug" == "front-page" ]] && continue
  [[ "$slug" == "datenschutz" || "$slug" == "impressum" ]] && continue

  matches="$(rg -l -F "/$slug/" "$THEME" -g '*.php' 2>/dev/null || true)"
  inbound="$(
    printf '%s\n' "$matches" \
      | rg -v -F "$(basename "$tpl")" 2>/dev/null \
      | count_lines || true
  )"
  inbound="${inbound:-0}"

  if (( inbound == 0 )); then
    printf '  ⚠ /%s/ — no inbound links from templates\n' "$slug"
  fi
done

header "Done"
echo "  Review the matrix above and add missing cross-links."
