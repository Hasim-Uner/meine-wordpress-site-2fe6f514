#!/usr/bin/env bash
# Print the live SEO QA scope. Lists are read from their sources on every run —
# llms.txt and the PHP functions named in each section — never copied here.

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../../../.." && pwd)"
INC="$ROOT/blocksy-child/inc"
mode="${1:-all}"

# extract <mode:map|paths|slugs> <file> <function>
extract() {
  python3 - "$@" <<'PY'
import re, sys
mode, path, name = sys.argv[1:4]
text = open(path, encoding='utf-8').read()
match = re.search(r'^function ' + re.escape(name) + r'\(.*?^}', text, re.M | re.S)
if not match:
    sys.exit(f'FEHLT: {name}() in {path}')
body = re.sub(r"'\s*\.\s*'", '', match[0])  # join split string literals
rel = path.split('/blocksy-child/', 1)[-1]
print(f'Quelle: {name}() in blocksy-child/{rel}')
if mode == 'map':
    rows = re.findall(r"^\s*'(/[^']*)'\s*=>\s*(.+?),?\s*$", body, re.M)
    print('\n'.join(f'- {src} -> {dst}' for src, dst in rows))
elif mode == 'paths':
    print('\n'.join(f'- {p}' for p in dict.fromkeys(re.findall(r"'(/[^']*/)'", body))))
else:
    print('\n'.join(f'- /{s}/' for s in re.findall(r"^\s*'([a-z0-9-]+)',", body, re.M)))
PY
}

print_reindex() {
  echo '[ROUTEN AUS llms.txt — Kandidaten fuer Reindex und Live-Pruefung]'
  grep -o '](/[^)#?]*' "$ROOT/llms.txt" | cut -c3- | awk '!seen[$0]++ { print "- " $0 }'
}

print_redirects() {
  echo '[REDIRECTS]'
  extract map "$INC/helpers.php" nexus_get_legacy_offer_redirect_map
  extract paths "$INC/helpers.php" nexus_redirect_legacy_results_path
  extract paths "$INC/helpers.php" nexus_redirect_legacy_energy_systems_path
  extract paths "$INC/system-diagnose-page.php" hu_get_request_analysis_legacy_paths
  echo 'Ziele der Variablen und Status: docs/architecture/LIVE_STATUS.md.'
}

print_gone() {
  echo '[410 GONE — absichtlich, kein zu behebender Fehler]'
  echo 'Ein 301 auf einen dieser Pfade waere eine Rueckabwicklung der Entscheidung, kein Bugfix.'
  extract paths "$INC/helpers.php" nexus_get_retired_gone_paths
}

print_noindex() {
  echo '[NOINDEX — nicht als kaputte Route melden und nicht reaktivieren]'
  extract slugs "$INC/seo-meta.php" hu_get_noindex_follow_slugs
  extract slugs "$INC/seo-meta.php" hu_get_noindex_nofollow_slugs
}

print_mapping() {
  cat <<'EOF'
[QUERY-OWNERSHIP]
Verbindliche Quelle je Query: docs/seo/query-ownership.csv
Pruefen: bash agents/skills/seo-agent/scripts/intent-gate.sh audit
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
  all) print_reindex; print_redirects; print_gone; print_noindex; print_mapping; print_live_qa ;;
  reindex) print_reindex ;;
  redirects) print_redirects ;;
  gone) print_gone ;;
  noindex) print_noindex ;;
  mapping) print_mapping ;;
  live-qa) print_live_qa ;;
  *)
    echo "Usage: $0 {all|reindex|redirects|gone|noindex|mapping|live-qa}" >&2
    exit 1
    ;;
esac
