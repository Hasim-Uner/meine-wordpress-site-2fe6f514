#!/usr/bin/env bash

set -uo pipefail

TESTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd -P)"
REPO_ROOT="$(cd "${TESTS_DIR}/../../../.." && pwd -P)"
FIXTURES_DIR="${TESTS_DIR}/fixtures/motion-guard"
CSS_DIR="${FIXTURES_DIR}/css"
EMPTY_BASELINE="${FIXTURES_DIR}/baselines/empty.tsv"
TEST_TMP="$(mktemp -d /tmp/b2b-motion-guard.XXXXXX)"

passes=0
failures=0

cleanup() {
  case "${TEST_TMP}" in
    /tmp/b2b-motion-guard.*)
      if [[ -d "${TEST_TMP}" && ! -L "${TEST_TMP}" ]]; then
        rm -rf -- "${TEST_TMP}"
      fi
      ;;
  esac
}
trap cleanup EXIT

pass() {
  echo "PASS: $1"
  passes=$((passes + 1))
}

fail() {
  echo "FAIL: $1"
  failures=$((failures + 1))
}

check_exit() {
  local label="$1"
  local actual="$2"
  local expected="$3"

  if [[ "${actual}" -eq "${expected}" ]]; then
    pass "${label} exit ${expected}"
  else
    fail "${label} expected exit ${expected}, got ${actual}"
  fi
}

check() {
  local label="$1"
  local output="$2"
  local expected="$3"

  if [[ "${output}" == *"${expected}"* ]]; then
    pass "${label} contains ${expected}"
  else
    fail "${label} missing ${expected}"
  fi
}

check_absent() {
  local label="$1"
  local output="$2"
  local forbidden="$3"

  if [[ "${output}" == *"${forbidden}"* ]]; then
    fail "${label} unexpectedly contains ${forbidden}"
  else
    pass "${label} omits ${forbidden}"
  fi
}

run_case() {
  local label="$1"
  local fixture="$2"
  local baseline="$3"
  local expected_exit="$4"
  local expected_text="$5"
  local case_dir="${TEST_TMP}/${label}"
  local output
  local status

  mkdir -p "${case_dir}"
  cp "${FIXTURES_DIR}/js/${fixture}" "${case_dir}/input.js"

  output="$(
    cd "${REPO_ROOT}" || exit 2
    MOTION_JS_BASELINE="${baseline}" \
      bash scripts/lint-css-motion.sh "${CSS_DIR}" "${case_dir}" 2>&1
  )"
  status=$?

  check_exit "${label}" "${status}" "${expected_exit}"
  check "${label}" "${output}" "${expected_text}"
}

run_case "conditional-smooth" "conditional-smooth.js" "${EMPTY_BASELINE}" 0 "OK: JavaScript motion avoids unreviewed hard rules"
run_case "literal-smooth" "literal-smooth.js" "${EMPTY_BASELINE}" 1 "literal-smooth-scroll"
run_case "literal-multiline" "literal-multiline.js" "${EMPTY_BASELINE}" 1 "literal-smooth-scroll"
run_case "cssom-smooth" "cssom-smooth.js" "${EMPTY_BASELINE}" 1 "literal-smooth-scroll"
run_case "set-property-smooth" "set-property-smooth.js" "${EMPTY_BASELINE}" 1 "literal-smooth-scroll"
run_case "transition-all" "transition-all.js" "${EMPTY_BASELINE}" 1 "js-transition-all"
run_case "bare-ease-in" "ease-in.js" "${EMPTY_BASELINE}" 1 "js-bare-ease-in"
run_case "ease-in-out" "ease-in-out.js" "${EMPTY_BASELINE}" 0 "OK: JavaScript motion avoids unreviewed hard rules"
run_case "suppressed" "suppressed.js" "${EMPTY_BASELINE}" 0 "OK: JavaScript motion avoids unreviewed hard rules"
run_case "non-motion" "non-motion.js" "${EMPTY_BASELINE}" 0 "OK: JavaScript motion avoids unreviewed hard rules"
run_case "exact-baseline" "literal-smooth.js" "${FIXTURES_DIR}/baselines/exact-literal.tsv" 0 "OK: JavaScript motion baseline is current"
run_case "new-fingerprint" "cssom-smooth.js" "${FIXTURES_DIR}/baselines/exact-literal.tsv" 1 "JavaScript motion baseline is current"
run_case "baseline-cannot-grow" "literal-smooth-twice.js" "${FIXTURES_DIR}/baselines/exact-literal.tsv" 1 $'target.scrollIntoView({ behavior: \'smooth\', block: \'start\' });\t2'
run_case "stale-baseline" "conditional-smooth.js" "${FIXTURES_DIR}/baselines/stale.tsv" 1 "literal-smooth-scroll"

waapi_dir="${TEST_TMP}/waapi"
mkdir -p "${waapi_dir}"
cp "${FIXTURES_DIR}/js/waapi.js" "${waapi_dir}/input.js"
waapi_output="$(
  cd "${REPO_ROOT}" || exit 2
  MOTION_JS_BASELINE="${EMPTY_BASELINE}" \
    bash scripts/lint-css-motion.sh "${CSS_DIR}" "${waapi_dir}" 2>&1
)"
waapi_status=$?
check_exit "waapi-advisory" "${waapi_status}" 0
check "waapi-advisory" "${waapi_output}" "Web Animations API calls:                     1"
check_absent "waapi-advisory" "${waapi_output}" "FAIL: JavaScript motion avoids unreviewed hard rules"

echo
echo "Motion guard tests: ${passes} passed, ${failures} failed."

if [[ "${failures}" -gt 0 ]]; then
  exit 1
fi
