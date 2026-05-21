#!/usr/bin/env bash

set -euo pipefail

THEME="blocksy-child"
FAIL=0

# --- Helpers ---
header() { printf '\n=== %s ===\n' "$1"; }
pass()   { printf '  ✓ %s\n' "$1"; }
fail()   { printf '  ✗ %s\n' "$1"; FAIL=1; }

# --- 1. PHP Lint on changed files ---
header "PHP Lint"

changed_php="$(git diff --name-only HEAD -- '*.php' 2>/dev/null || true)"
staged_php="$(git diff --cached --name-only -- '*.php' 2>/dev/null || true)"
untracked_php="$(git ls-files --others --exclude-standard -- '*.php' 2>/dev/null || true)"
all_php="$(printf '%s\n%s\n%s' "$changed_php" "$staged_php" "$untracked_php" | sort -u | grep -v '^$' || true)"
theme_php="$(printf '%s\n' "$all_php" | grep "^$THEME/.*\\.php$" || true)"

if [[ -z "$all_php" ]]; then
  pass "No changed PHP files"
else
  lint_ok=true
  while IFS= read -r f; do
    [[ -f "$f" ]] || continue
    if ! php -l "$f" 2>&1 | grep -q "No syntax errors"; then
      fail "$f"
      php -l "$f" 2>&1 | grep -v "^$"
      lint_ok=false
    fi
  done <<< "$all_php"
  $lint_ok && pass "All PHP files pass lint"
fi

# --- 2. Hardcoded domain check ---
header "Hardcoded URLs"

hardcoded=""
if [[ -n "$theme_php" ]]; then
  while IFS= read -r f; do
    [[ -f "$f" ]] || continue
    found="$(grep -n 'https\?://hasimuener\.de' "$f" 2>/dev/null || true)"
    [[ -n "$found" ]] && hardcoded+="${found/#/$f:}"$'\n'
  done <<< "$theme_php"
fi
if [[ -n "${hardcoded%$'\n'}" ]]; then
  fail "Hardcoded domain found (use home_url() instead):"
  echo "$hardcoded"
else
  pass "No hardcoded domain URLs in changed PHP files"
fi

# --- 3. Raw echo check ---
header "Escaping Hygiene"

raw_echo=""
if [[ -n "$theme_php" ]]; then
  while IFS= read -r f; do
    [[ -f "$f" ]] || continue
    found="$(grep -n 'echo \$' "$f" 2>/dev/null | grep -v 'esc_' | grep -v '// raw-ok' || true)"
    [[ -n "$found" ]] && raw_echo+="${found/#/$f:}"$'\n'
  done <<< "$theme_php"
fi
if [[ -n "${raw_echo%$'\n'}" ]]; then
  fail "Unescaped echo found (use esc_html/esc_attr/esc_url):"
  echo "$raw_echo" | head -20
else
  pass "No unescaped echo statements in changed PHP files"
fi

# --- 4. Asset existence ---
header "Asset References"

if [[ -f "$THEME/inc/enqueue.php" ]]; then
  # Extract filenames from helper calls without GNU grep-only flags.
  css_files="$(sed -nE "s/.*hu_enqueue_css\\([^,]+,[[:space:]]*'([^']+)'.*/\\1/p" "$THEME/inc/enqueue.php" || true)"
  js_files="$(sed -nE "s/.*hu_enqueue_js\\([^,]+,[[:space:]]*'([^']+)'.*/\\1/p" "$THEME/inc/enqueue.php" || true)"
  direct_css="$(sed -nE "s/.*\\\$css_uri[[:space:]]*\\.[[:space:]]*'([^']+)'.*/\\1/p" "$THEME/inc/enqueue.php" || true)"
  direct_js="$(sed -nE "s/.*\\\$js_uri[[:space:]]*\\.[[:space:]]*'([^']+)'.*/\\1/p" "$THEME/inc/enqueue.php" || true)"

  all_assets="$(printf '%s\n%s\n%s\n%s' "$css_files" "$js_files" "$direct_css" "$direct_js" | sort -u | grep -v '^$' || true)"
  asset_ok=true
  while IFS= read -r asset; do
    [[ -z "$asset" ]] && continue
    # Determine directory based on extension
    if [[ "$asset" == *.css ]]; then
      full="$THEME/assets/css/$asset"
    elif [[ "$asset" == *.js ]]; then
      full="$THEME/assets/js/$asset"
    else
      continue
    fi
    if [[ ! -f "$full" ]]; then
      fail "Missing asset: $full"
      asset_ok=false
    fi
  done <<< "$all_assets"
  $asset_ok && pass "All enqueued assets exist"
else
  pass "No enqueue.php found (skipped)"
fi

# --- 5. Unclosed PHP tags ---
header "Template Integrity"

unclosed_ok=true
for tpl in "$THEME"/template-parts/*.php "$THEME"/front-page.php "$THEME"/home.php "$THEME"/single.php "$THEME"/archive.php; do
  [[ -f "$tpl" ]] || continue
  open="$(grep -c '<?php' "$tpl" 2>/dev/null || echo 0)"
  close="$(grep -c '?>' "$tpl" 2>/dev/null || echo 0)"
  # Files that end in PHP mode legitimately have open > close by 1
  if (( open - close > 1 )); then
    fail "Possible unclosed PHP tag in $tpl (open=$open close=$close)"
    unclosed_ok=false
  fi
done
$unclosed_ok && pass "Template PHP tags balanced"

# --- Verdict ---
header "VERDICT"
if (( FAIL )); then
  echo "  ⛔ FIX BEFORE PUSH"
  exit 1
else
  echo "  ✅ SAFE TO PUSH"
  exit 0
fi
