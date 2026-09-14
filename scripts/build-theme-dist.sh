#!/usr/bin/env bash

set -euo pipefail

root_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
source_dir="$root_dir/blocksy-child"
output_dir="${1:-$root_dir/.build/blocksy-child}"

case "$output_dir" in
  /*) ;;
  *) output_dir="$root_dir/$output_dir" ;;
esac

if [ ! -d "$source_dir" ]; then
  echo "Source theme directory not found: $source_dir" >&2
  exit 1
fi

# Fail before packaging if the canonical Gutachten token owner and its one
# transitional Solar mirror have drifted apart, or if another stylesheet has
# started redefining the same core token family.
python3 "$root_dir/scripts/audit-css-architecture.py"

if [ "$output_dir" = "$root_dir" ] || [ "$output_dir" = "$source_dir" ]; then
  echo "Refusing to build into an unsafe output directory: $output_dir" >&2
  exit 1
fi

lightningcss_bin="$root_dir/node_modules/.bin/lightningcss"
terser_bin="$root_dir/node_modules/.bin/terser"

if [ ! -x "$lightningcss_bin" ] || [ ! -x "$terser_bin" ]; then
  echo "Build tooling is missing. Run npm ci first." >&2
  exit 1
fi

mkdir -p "$output_dir"
rsync -a --delete \
  --exclude '.DS_Store' \
  --exclude 'node_modules' \
  --exclude '.env' \
  --exclude '.env.*' \
  "$source_dir/" "$output_dir/"

style_file="$output_dir/style.css"
sst_css_dir="$output_dir/assets/css"
sst_entry="$sst_css_dir/server-side-tracking.css"
anfragestrecke_css="$sst_css_dir/anfragestrecke.css"
single_css="$sst_css_dir/single.css"
sst_layers=(
  "server-side-tracking-base.css"
  "server-side-tracking-cro.css"
  "server-side-tracking-funnel.css"
  "server-side-tracking-protocol.css"
  "server-side-tracking-contrast.css"
)
react_funnels=(
  "energie-fahrplan"
  "readiness"
)

minify_css_file() {
  local file="$1"

  "$lightningcss_bin" --minify "$file" -o "$file"
}

minify_js_file() {
  local file="$1"

  "$terser_bin" "$file" --compress --mangle -o "$file"
}

build_sst_css_bundle() {
  if [ ! -f "$sst_entry" ]; then
    return
  fi

  local bundle_input
  bundle_input="$(mktemp)"

  for layer in "${sst_layers[@]}"; do
    if [ ! -f "$sst_css_dir/$layer" ]; then
      echo "Missing Server-Side-Tracking CSS layer: $layer" >&2
      rm -f "$bundle_input"
      exit 1
    fi
    cat "$sst_css_dir/$layer" >> "$bundle_input"
    printf '\n' >> "$bundle_input"
  done

  # The source entry documents composition with @import. In the deployment
  # package the imports are replaced by the concatenated layers above, while
  # entry-local rules remain last so their cascade order is unchanged.
  sed '/^[[:space:]]*@import[[:space:]]/d' "$sst_entry" >> "$bundle_input"
  "$lightningcss_bin" --minify "$bundle_input" -o "$sst_entry"
  rm -f "$bundle_input"

  # Source layers are authoring units only. Production needs one request.
  for layer in "${sst_layers[@]}"; do
    rm -f "$sst_css_dir/$layer"
  done
}

build_react_funnel() {
  local funnel_name="$1"
  local funnel_dir="$output_dir/$funnel_name"

  if [ ! -f "$funnel_dir/package.json" ]; then
    return
  fi

  if [ ! -f "$funnel_dir/package-lock.json" ]; then
    echo "Missing package-lock.json for React funnel: $funnel_name" >&2
    exit 1
  fi

  echo "Building React funnel: $funnel_name"
  npm --prefix "$funnel_dir" ci --include=dev --ignore-scripts --no-audit --no-fund
  npm --prefix "$funnel_dir" run build
  rm -rf "$funnel_dir/node_modules"
}

for funnel in "${react_funnels[@]}"; do
  build_react_funnel "$funnel"
done

build_sst_css_bundle

# system.css is globally loaded before the Solar route CSS. Source keeps the
# mirror temporarily for a reviewable migration path, but production must not
# ship two owners for the same Gutachten custom-property family.
if [ -f "$anfragestrecke_css" ]; then
  python3 "$root_dir/scripts/collapse-gutachten-token-mirror.py" "$anfragestrecke_css"
fi

# The source still carries one historical single-post base block in style.css.
# Dedicated single.css is loaded later on every single-post/SEO-cornerstone
# surface and owns those selectors. Keep the source migration reviewable, but
# do not ship both generations in the deployment package.
if [ -f "$style_file" ] && [ -f "$single_css" ]; then
  python3 "$root_dir/scripts/prune-style-single-legacy.py" "$style_file" "$single_css"
fi

if [ -f "$style_file" ]; then
  style_header="$(awk 'NR == 1 && /^\/\*/ { in_header = 1 } in_header { print; if ($0 ~ /\*\//) exit }' "$style_file")"
  style_header_end_line="$(awk 'NR == 1 && /^\/\*/ { in_header = 1 } in_header && /\*\// { print NR; exit }' "$style_file")"

  if [ -z "$style_header" ] || [ -z "$style_header_end_line" ]; then
    echo "Expected a WordPress theme header at the top of $style_file" >&2
    exit 1
  fi

  # Production asset URLs must change on every deployed commit. The runtime
  # intentionally uses the theme Version header instead of filemtime(), so the
  # build stamps the immutable source version with the current Git revision.
  # This prevents page/RUCSS/browser caches from pinning an older CSS payload
  # after a deploy while keeping the source theme header human-readable.
  source_theme_version="$(printf '%s\n' "$style_header" | sed -n 's/^Version:[[:space:]]*//p' | head -n 1)"
  build_ref="$(git -C "$root_dir" rev-parse --short=12 HEAD 2>/dev/null || true)"

  if [ -z "$source_theme_version" ]; then
    source_theme_version="1.0.0"
  fi
  if [ -z "$build_ref" ]; then
    build_ref="$(date -u +%Y%m%d%H%M%S)"
  fi

  deploy_asset_version="${source_theme_version}-${build_ref}"
  style_header="$(printf '%s\n' "$style_header" | sed "s/^Version:.*/Version: ${deploy_asset_version}/")"

  style_body_input="$(mktemp)"
  style_body_output="$(mktemp)"
  tail -n +"$((style_header_end_line + 1))" "$style_file" > "$style_body_input"
  "$lightningcss_bin" --minify "$style_body_input" -o "$style_body_output"

  {
    printf '%s\n\n' "$style_header"
    cat "$style_body_output"
  } > "$style_file"

  rm -f "$style_body_input" "$style_body_output"
fi

find "$output_dir" -type f -name '*.css' ! -name '*.min.css' ! -path "$style_file" ! -path "$sst_entry" -print0 | while IFS= read -r -d '' file; do
  minify_css_file "$file"
done

find "$output_dir" -type f -name '*.js' ! -name '*.min.js' -print0 | while IFS= read -r -d '' file; do
  minify_js_file "$file"
done

grep -q '^Theme Name: Blocksy Child$' "$style_file"
grep -q '^Version: .\+-[0-9a-f]\{7,12\}$' "$style_file"

# Deployment contract: SST ships as one CSS file without runtime @imports.
if [ -f "$sst_entry" ]; then
  if grep -q '@import' "$sst_entry"; then
    echo "Server-Side-Tracking deployment CSS still contains @import." >&2
    exit 1
  fi
  for layer in "${sst_layers[@]}"; do
    if [ -e "$sst_css_dir/$layer" ]; then
      echo "Server-Side-Tracking source layer leaked into deployment package: $layer" >&2
      exit 1
    fi
  done
fi

echo "Built deployment package at $output_dir"
