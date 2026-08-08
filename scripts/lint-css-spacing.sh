#!/usr/bin/env bash

# Spacing guard for the child theme stylesheets.
#
# Same shape as lint-css-motion.sh: hard rules fail the build, judgement calls
# land in a drift report that stays visible without blocking a release, and the
# existing state is frozen in a baseline that may only shrink.
#
# Suppress a deliberate exception with "lint-css-spacing: allow" on the
# offending line or the line above it.

set -euo pipefail

CSS_DIR="${1:-blocksy-child/assets/css}"

# Das Grundraster. layout-audit.mjs prueft gegen denselben Wert.
GRID=4

if [[ ! -d "${CSS_DIR}" ]]; then
  echo "Spacing guard failed: no CSS directory at ${CSS_DIR}" >&2
  exit 1
fi

# Textkaesten, die heute oben auf 0 kollabieren. Neue Dateien duerfen nicht
# dazukommen — diese Liste schrumpft, sie waechst nicht. Wie die
# Reduced-Motion-Baseline in lint-css-motion.sh.
BASELINE_COLLAPSED_PADDING=(
  "checkfox-decision.css"
  "ga4.css"
  "seo-cockpit-admin.css"
  "single-editorial.css"
  "single.css"
)

# Dateien mit eigener Spacing-Skala statt var(--nx-space-*). Historisch
# gewachsen; neue Stylesheets sollen die gemeinsamen Tokens benutzen.
# design-system.css steht hier, weil es die Skala DEFINIERT — das ist die
# Quelle, nicht die Abweichung.
BASELINE_LOCAL_SCALE=(
  "agentur.css"
  "design-system.css"
)

failures=0

report_rule() {
  local label="$1"
  local guidance="$2"
  local matches="$3"

  if [[ -n "${matches}" ]]; then
    echo "FAIL: ${label}"
    echo "      ${guidance}"
    printf '%s\n' "${matches}" | sed 's/^/      /'
    echo
    failures=$((failures + 1))
  else
    echo "OK: ${label}"
  fi
}

in_baseline() {
  local name="$1"
  shift
  local known
  for known in "$@"; do
    [[ "${name}" == "${known}" ]] && return 0
  done
  return 1
}

# Senkrecht kollabierte Innenabstaende an Textkaesten.
#
# Der Shorthand muss wirklich ausgewertet werden, ein Regex reicht nicht:
# "padding: 0 24px" ist zweiwertig und damit oben wie unten 0 — symmetrisch und
# kein Befund. Erst "padding: 0 18px 18px" setzt oben 0 gegen unten 18px.
#   1 Wert  -> alle Seiten gleich
#   2 Werte -> oben/unten gleich
#   3 Werte -> oben | seitlich | unten
#   4 Werte -> oben | rechts | unten | links
scan_collapsed_padding() {
  awk '
    function strip(s) { sub(/^[ \t]+/, "", s); sub(/[ \t]+$/, "", s); return s }

    # Leerzeichen INNERHALB von Klammern entfernen, damit var(--a, b) und
    # clamp(1rem, 2vw, 3rem) je ein Token bleiben. Ein Ersetzen per match() in
    # einer Schleife terminiert hier nicht: enthaelt das Token kein
    # Leerzeichen, bleibt der String gleich und match() findet dieselbe Stelle
    # wieder. Deshalb ein einfacher Durchlauf mit Klammertiefe.
    function flatten(s,   i, c, depth, out) {
      depth = 0; out = ""
      for (i = 1; i <= length(s); i++) {
        c = substr(s, i, 1)
        if (c == "(") depth++
        else if (c == ")") depth--
        if (depth > 0 && (c == " " || c == "\t")) continue
        out = out c
      }
      return out
    }

    FNR == 1 { sel = ""; prev = ""; pad_top_zero = 0; pad_top_line = 0 }

    # Selektorzeile merken und Rule-Zustand zuruecksetzen.
    /\{[ \t]*$/ {
      sel = $0
      sub(/[ \t]*\{[ \t]*$/, "", sel)
      sel = strip(sel)
      pad_top_zero = 0
      pad_top_line = 0
    }

    {
      line = $0
      allow = (line ~ /lint-css-spacing: allow/ || prev ~ /lint-css-spacing: allow/)

      # Nur Kaesten, deren Name sie als Texthuelle ausweist.
      is_text_box = (sel ~ /-(body|inner|content|answer|panel)([ ,:.]|$)/)

      if (is_text_box && !allow && line ~ /^[ \t]*padding[ \t]*:/) {
        decl = line
        sub(/^[ \t]*padding[ \t]*:[ \t]*/, "", decl)
        sub(/[ \t]*;.*$/, "", decl)
        gsub(/!important/, "", decl)
        n = split(strip(flatten(decl)), v, /[ \t]+/)
        top = v[1]
        bottom = (n == 3 || n == 4) ? v[3] : top
        if (n >= 3 && top ~ /^0[a-z%]*$/ && bottom !~ /^0[a-z%]*$/) {
          printf "%s:%d:%s { %s }\n", FILENAME, FNR, sel, strip(line)
        }
      }

      # Langform: padding-top: 0 zusammen mit einem padding-bottom != 0.
      if (is_text_box && !allow && line ~ /^[ \t]*padding-top[ \t]*:[ \t]*0[a-z%]*[ \t]*;/) {
        pad_top_zero = 1
        pad_top_line = FNR
        pad_top_sel = sel
        pad_top_text = strip(line)
      }
      if (is_text_box && pad_top_zero && line ~ /^[ \t]*padding-bottom[ \t]*:/) {
        val = line
        sub(/^[ \t]*padding-bottom[ \t]*:[ \t]*/, "", val)
        sub(/[ \t]*;.*$/, "", val)
        if (strip(val) !~ /^0[a-z%]*$/) {
          printf "%s:%d:%s { %s }\n", FILENAME, pad_top_line, pad_top_sel, pad_top_text
          pad_top_zero = 0
        }
      }

      prev = line
    }
  ' "${CSS_DIR}"/*.css
}

# Definiert die Datei eine eigene Spacing-Skala, statt die gemeinsamen Tokens
# zu konsumieren?
defines_local_scale() {
  grep -qE '^[ \t]*--[a-z0-9-]*(space|spacing|gap|section-pad|pad)-[a-z0-9]+[ \t]*:' "$1"
}

echo
echo "=== Spacing guard: hard rules ==="

# 1. Kollabierte Innenabstaende in Dateien ausserhalb der Baseline.
new_collapsed=""
while IFS= read -r hit; do
  [[ -z "${hit}" ]] && continue
  file="${hit%%:*}"
  name="$(basename "${file}")"
  if ! in_baseline "${name}" "${BASELINE_COLLAPSED_PADDING[@]}"; then
    new_collapsed+="${hit}"$'\n'
  fi
done <<< "$(scan_collapsed_padding)"

report_rule \
  "No newly collapsed padding on text boxes" \
  "padding-top: 0 next to a non-zero bottom makes the first line sit flush against the edge above. Give it the same breathing room as the other sides, or annotate with 'lint-css-spacing: allow'." \
  "${new_collapsed%$'\n'}"

# 2. Neue lokale Spacing-Skalen.
new_scales=""
for file in "${CSS_DIR}"/*.css; do
  name="$(basename "${file}")"
  in_baseline "${name}" "${BASELINE_LOCAL_SCALE[@]}" && continue
  if defines_local_scale "${file}"; then
    new_scales+="${file}"$'\n'
  fi
done

report_rule \
  "No new local spacing scales" \
  "Consume var(--nx-space-*) from design-system.css instead of inventing a per-page scale. Alias the shared tokens if a local name helps." \
  "${new_scales%$'\n'}"

# 3. Baseline darf nur schrumpfen.
stale=""
for known in "${BASELINE_COLLAPSED_PADDING[@]}"; do
  path="${CSS_DIR}/${known}"
  if [[ ! -f "${path}" ]]; then
    stale+="${known} (file is gone)"$'\n'
  elif ! scan_collapsed_padding | grep -q "^${path}:"; then
    stale+="${known} (now compliant)"$'\n'
  fi
done
for known in "${BASELINE_LOCAL_SCALE[@]}"; do
  path="${CSS_DIR}/${known}"
  if [[ ! -f "${path}" ]]; then
    stale+="${known} (file is gone)"$'\n'
  elif ! defines_local_scale "${path}"; then
    stale+="${known} (now compliant)"$'\n'
  fi
done

report_rule \
  "Spacing baselines are current" \
  "Drop these entries from the BASELINE_* arrays in this script." \
  "${stale%$'\n'}"

echo
echo "=== Spacing guard: drift report (never fails) ==="

collapsed_total="$(scan_collapsed_padding | grep -c . || true)"
local_scale_total=0
no_token_files=""
for file in "${CSS_DIR}"/*.css; do
  defines_local_scale "${file}" && local_scale_total=$((local_scale_total + 1))
  if ! grep -q 'var(--nx-space-\|var(--space-' "${file}"; then
    no_token_files+="$(basename "${file}") "
  fi
done
total_files="$(find "${CSS_DIR}" -maxdepth 1 -name '*.css' | wc -l | tr -d ' ')"
no_token_count="$(printf '%s' "${no_token_files}" | wc -w | tr -d ' ')"

echo "Text boxes with collapsed top padding:        ${collapsed_total}"
echo "Files defining their own spacing scale:       ${local_scale_total}"
echo "Files using no shared spacing token at all:   ${no_token_count} of ${total_files}"
echo
echo "The last number is the real debt: the shared scale in design-system.css"
echo "only governs a page once that page actually consumes it."
echo
echo "These shrink by editing CSS, not by editing this script."
echo

if [[ "${failures}" -gt 0 ]]; then
  echo "Spacing guard failed with ${failures} rule violation(s)." >&2
  exit 1
fi

echo "Spacing guard passed."
