#!/usr/bin/env bash
# Regression tests for explicit route-to-template aliases.

set -uo pipefail

TESTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$TESTS_DIR/../../../.." && pwd)"
REVIEW="$REPO_ROOT/agents/skills/route-conversion-review/scripts/review-route.sh"

PASS=0
FAIL=0

check() {
	local label="$1" haystack="$2" needle="$3"
	if printf '%s' "$haystack" | grep -qF -- "$needle"; then
		printf '  ok    %s\n' "$label"
		PASS=$((PASS + 1))
	else
		printf '  FAIL  %s\n         erwartet: %s\n' "$label" "$needle"
		FAIL=$((FAIL + 1))
	fi
}

echo "=== review-route.sh Regressionstests ==="

CASE_OUT="$(bash "$REVIEW" /case-study-solar-leadgenerierung/ 2>&1)"
CASE_STATUS=$?
if [[ "$CASE_STATUS" -ne 0 ]]; then
	printf 'Route review exited with status %s:\n%s\n' "$CASE_STATUS" "$CASE_OUT" >&2
	FAIL=$((FAIL + 1))
fi
check "Case Study uses effective carrier" "$CASE_OUT" "Template: blocksy-child/page-e3-new-energy.php"
check "Case Study H1 is inspected"         "$CASE_OUT" "[OK  ] H1"
check "Case Study CTAs are inspected"       "$CASE_OUT" "[OK  ] CTA-Tracking"
check "Case Study proof canon is inspected" "$CASE_OUT" "[OK  ] Proof-Canon"

HANNOVER_OUT="$(bash "$REVIEW" /wordpress-agentur-hannover/ 2>&1)"
HANNOVER_STATUS=$?
if [[ "$HANNOVER_STATUS" -ne 0 ]]; then
	printf 'Route review exited with status %s:\n%s\n' "$HANNOVER_STATUS" "$HANNOVER_OUT" >&2
	FAIL=$((FAIL + 1))
fi
check "Hannover wrapper remains mapped" "$HANNOVER_OUT" "Template: blocksy-child/page-wordpress-agentur.php"

EMPTY_PATH="$(mktemp -d)"
MISSING_OUT="$(PATH="$EMPTY_PATH" "$BASH" "$REVIEW" /case-study-solar-leadgenerierung/ 2>&1)"
MISSING_STATUS=$?
rmdir "$EMPTY_PATH"
check "Missing ripgrep is reported explicitly" "$MISSING_OUT" "Missing required command: rg"
if [[ "$MISSING_STATUS" -eq 2 ]]; then
	PASS=$((PASS + 1))
else
	printf '  FAIL  missing dependency must exit 2; got %s\n' "$MISSING_STATUS"
	FAIL=$((FAIL + 1))
fi

echo "=== $PASS bestanden, $FAIL fehlgeschlagen ==="
[[ "$FAIL" -eq 0 ]]
