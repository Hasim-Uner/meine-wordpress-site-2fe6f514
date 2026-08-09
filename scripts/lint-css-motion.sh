#!/usr/bin/env bash

# Motion guard for child-theme CSS and JavaScript.
#
# Hard rules cover what is unambiguously wrong and fail the build. Judgement
# calls — a 900ms scroll reveal is a choice, not a bug — land in the drift
# report, which stays visible without blocking a release.
#
# Suppress a deliberate CSS exception with "lint-css-motion: allow -- <reason>"
# and a JavaScript exception with "lint-js-motion: allow -- <reason>" on the
# offending line or the line above it. Existing debt belongs in the baselines.

set -euo pipefail

CSS_DIR="${1:-blocksy-child/assets/css}"
JS_DIR="${2:-blocksy-child/assets/js}"
JS_MOTION_BASELINE="${MOTION_JS_BASELINE:-scripts/baselines/js-motion.tsv}"
JS_MOTION_SCANNER="agents/skills/b2b-design-system/scripts/scan-js-motion.py"

# A transition above this reads as lag on a discrete state change. Reveal
# choreography legitimately runs longer, so this only reports.
SLOW_TRANSITION_MS=600

if [[ ! -d "${CSS_DIR}" ]]; then
  echo "Motion guard failed: no CSS directory at ${CSS_DIR}" >&2
  exit 1
fi

if [[ ! -d "${JS_DIR}" ]]; then
  echo "Motion guard failed: no JavaScript directory at ${JS_DIR}" >&2
  exit 1
fi

if [[ ! -f "${JS_MOTION_BASELINE}" ]]; then
  echo "Motion guard failed: no JavaScript baseline at ${JS_MOTION_BASELINE}" >&2
  exit 1
fi

if [[ ! -f "${JS_MOTION_SCANNER}" ]]; then
  echo "Motion guard failed: no JavaScript scanner at ${JS_MOTION_SCANNER}" >&2
  exit 1
fi

if ! command -v python3 >/dev/null 2>&1; then
  echo "Motion guard failed: python3 is required for the JavaScript scan" >&2
  exit 1
fi

shopt -s nullglob
JS_FILES=("${JS_DIR}"/*.js "${JS_DIR}"/*.jsx)
shopt -u nullglob

if [[ "${#JS_FILES[@]}" -eq 0 ]]; then
  echo "Motion guard failed: no .js or .jsx files at ${JS_DIR}" >&2
  exit 1
fi

css_dir_resolved="$(cd "${CSS_DIR}" && pwd -P)"
project_css_dir_resolved="$(cd blocksy-child/assets/css && pwd -P)"
use_css_baseline=0
if [[ "${css_dir_resolved}" == "${project_css_dir_resolved}" ]]; then
  use_css_baseline=1
fi

# Files that animate without a prefers-reduced-motion block. New files must not
# join this list — shrink it instead. Same idea as phpstan-baseline.neon.
BASELINE_REDUCED_MOTION=(
  "audit-results.css"
  "audit.css"
  "blog-header.css"
  "blog-notify.css"
  "case-study.css"
  "cluster-pillar.css"
  "deep-dive.css"
  "footer-cta.css"
  "glossary.css"
  "seo-cockpit-admin.css"
  "single.css"
  "site-header.css"
  "waermepumpen-leads.css"
  "wgos.css"
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

# Emits "file:line:text" for lines matching $1, skipping allow-listed lines.
scan() {
  local pattern="$1"

  awk -v pattern="${pattern}" '
    FNR == 1 { prev = "" }
    {
      line = $0
      if (line ~ pattern && line !~ /lint-css-motion: allow/ && prev !~ /lint-css-motion: allow/) {
        text = line
        sub(/^[ \t]+/, "", text)
        printf "%s:%d:%s\n", FILENAME, FNR, text
      }
      prev = line
    }
  ' "${CSS_DIR}"/*.css
}

# Emits "file:line:ms:text" for transitions whose longest time value exceeds
# $1 milliseconds. Regex cannot do this: "0.2s" contains the substring "2s",
# so the numbers have to be parsed and compared. The value pattern also has to
# accept ".15s", which carries no leading zero — hence the [0-9.] class rather
# than an optional dot, which mawk skips over.
#
# transition-delay is deliberately excluded: a long delay is how staged reveal
# choreography is written, not a slow animation.
scan_slow_transitions() {
  local limit_ms="$1"

  awk -v limit="${limit_ms}" '
    FNR == 1 { prev = "" }
    {
      line = $0
      if (line ~ /transition(-duration)?[ \t]*:/ && line !~ /lint-css-motion: allow/ && prev !~ /lint-css-motion: allow/) {
        rest = line
        worst = 0
        while (match(rest, /[0-9.]+(ms|s)/)) {
          token = substr(rest, RSTART, RLENGTH)
          rest = substr(rest, RSTART + RLENGTH)
          value = token + 0
          if (token !~ /ms$/) {
            value = value * 1000
          }
          if (value > worst) {
            worst = value
          }
        }
        if (worst > limit) {
          text = line
          sub(/^[ \t]+/, "", text)
          printf "%s:%d:%dms:%s\n", FILENAME, FNR, worst, text
        }
      }
      prev = line
    }
  ' "${CSS_DIR}"/*.css
}

echo
echo "=== Motion guard: hard rules ==="

# "all" animates every property that ever changes, including layout and paint
# work nobody asked for. Name the properties instead.
report_rule \
  "No 'transition: all'" \
  "Enumerate the properties that actually change." \
  "$(scan "transition[^:]*:[[:space:]]*all[[:space:]]")"

# ease-in accelerates from a standstill, so entering elements look sluggish.
# ease-out and ease-in-out are unaffected.
report_rule \
  "No bare 'ease-in' easing" \
  "Use var(--ease-default) for entrances, ease-out for exits." \
  "$(scan "ease-in([^-]|$)")"

missing_reduced_motion=""
for file in "${CSS_DIR}"/*.css; do
  name="$(basename "${file}")"

  if ! grep -qE "transition:|@keyframes|animation:" "${file}"; then
    continue
  fi

  if grep -q "prefers-reduced-motion" "${file}"; then
    continue
  fi

  baselined=0
  if [[ "${use_css_baseline}" -eq 1 ]]; then
    for known in "${BASELINE_REDUCED_MOTION[@]}"; do
      if [[ "${name}" == "${known}" ]]; then
        baselined=1
        break
      fi
    done
  fi

  if [[ "${baselined}" -eq 0 ]]; then
    missing_reduced_motion+="${file}"$'\n'
  fi
done

report_rule \
  "Animated files honour prefers-reduced-motion" \
  "Add a @media (prefers-reduced-motion: reduce) block, or extend the baseline with a reason." \
  "${missing_reduced_motion%$'\n'}"

# A baselined file that grew a reduced-motion block should leave the list, so
# the baseline can only shrink.
stale_baseline=""
if [[ "${use_css_baseline}" -eq 1 ]]; then
  for known in "${BASELINE_REDUCED_MOTION[@]}"; do
    path="${CSS_DIR}/${known}"

    if [[ ! -f "${path}" ]]; then
      stale_baseline+="${known} (file is gone)"$'\n'
    elif grep -q "prefers-reduced-motion" "${path}"; then
      stale_baseline+="${known} (now compliant)"$'\n'
    fi
  done
fi

report_rule \
  "Reduced-motion baseline is current" \
  "Drop these entries from BASELINE_REDUCED_MOTION in this script." \
  "${stale_baseline%$'\n'}"

if ! js_findings="$(python3 -B "${JS_MOTION_SCANNER}" "${JS_DIR}")"; then
  echo "Motion guard failed: JavaScript scanner could not complete" >&2
  exit 1
fi

baseline_findings="$(awk -F '\t' 'NF && $1 !~ /^#/ { print }' "${JS_MOTION_BASELINE}" | LC_ALL=C sort)"
sorted_js_findings="$(printf '%s\n' "${js_findings}" | sed '/^$/d' | LC_ALL=C sort)"

new_js_findings="$(comm -23 \
  <(printf '%s\n' "${sorted_js_findings}") \
  <(printf '%s\n' "${baseline_findings}"))"

stale_js_baseline="$(comm -13 \
  <(printf '%s\n' "${sorted_js_findings}") \
  <(printf '%s\n' "${baseline_findings}"))"

report_rule \
  "JavaScript motion avoids unreviewed hard rules" \
  "Resolve literal smooth scrolling through reduced-motion state; do not author transition: all or bare ease-in in JavaScript." \
  "${new_js_findings}"

report_rule \
  "JavaScript motion baseline is current" \
  "Remove or update only the exact stale fingerprint in ${JS_MOTION_BASELINE}; never broaden the baseline." \
  "${stale_js_baseline}"

echo
echo "=== Motion guard: drift report (never fails) ==="

slow="$(scan_slow_transitions "${SLOW_TRANSITION_MS}")"
slow_count="$(printf '%s' "${slow}" | grep -c . || true)"

hardcoded_count="$(scan "transition:[^;]*[0-9](\\.[0-9]+)?(m?s)" | grep -v "var(--" | grep -c . || true)"
layout_count="$(scan "transition:[^;]*(width|height|top|left|right|bottom|margin|padding)[^;]*[0-9]" | grep -c . || true)"
will_change_count="$(scan "will-change[[:space:]]*:" | grep -c . || true)"
literal_smooth_count="$(printf '%s\n' "${sorted_js_findings}" | awk -F '\t' '$1 == "literal-smooth-scroll" { count += $4 } END { print count + 0 }')"
waapi_count="$(awk '{ count += gsub(/\.animate[[:space:]]*\(/, "&") } END { print count + 0 }' "${JS_FILES[@]}")"
cssom_motion_count="$(awk '{ count += gsub(/\.style\.(transition|animation)[A-Za-z]*/, "&") } END { print count + 0 }' "${JS_FILES[@]}")"

echo "Transitions over ${SLOW_TRANSITION_MS}ms:                    ${slow_count}"
if [[ -n "${slow}" ]]; then
  printf '%s\n' "${slow}" | sed 's/^/  /'
fi
echo "Transitions with hardcoded timing, no token:  ${hardcoded_count}"
echo "Transitions on layout properties:             ${layout_count}"
echo "CSS will-change declarations:                 ${will_change_count}"
echo "Literal smooth-scroll calls (baselined):      ${literal_smooth_count}"
echo "Web Animations API calls:                     ${waapi_count}"
echo "JavaScript CSSOM motion declarations:         ${cssom_motion_count}"
echo
echo "These shrink by editing source files, not by weakening this script."
echo

if [[ "${failures}" -gt 0 ]]; then
  echo "Motion guard failed with ${failures} rule violation(s)." >&2
  exit 1
fi

echo "Motion guard passed."
