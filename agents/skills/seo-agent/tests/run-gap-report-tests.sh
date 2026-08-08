#!/usr/bin/env bash
#
# Regressionstest fuer gap-report.sh.
#
# Deckt genau die Fehler ab, die den Report schon einmal falsch gemacht haben:
# eine historische Bestposition als aktuellen Rang zu melden, 7d und 28d zu
# vermischen und mehrere URLs derselben Query still auf die beste Position zu
# reduzieren.
#
# Die Fixture unter tests/fixtures/ ist erfunden und traegt bewusst
# "testfrage *"-Queries. Nur der letzte Test laeuft gegen die echten
# Repo-Exporte und haelt den Akzeptanzwert fest.
#
#   bash agents/skills/seo-agent/tests/run-gap-report-tests.sh

set -uo pipefail

TESTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$TESTS_DIR/../../../.." && pwd)"
GAP_REPORT="$REPO_ROOT/agents/skills/seo-agent/scripts/gap-report.sh"
FIXTURES="$TESTS_DIR/fixtures"

PASS=0
FAIL=0

run_fixture() {
  RESEARCH_DIR="$FIXTURES" \
  OWNERSHIP="$FIXTURES/query-ownership.csv" \
  EXCLUSIONS="$FIXTURES/keyword-exclusions.csv" \
  bash "$GAP_REPORT" "$@" 2>&1
}

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

check_absent() {
  local label="$1" haystack="$2" needle="$3"
  if printf '%s' "$haystack" | grep -qF -- "$needle"; then
    printf '  FAIL  %s\n         unerwartet gefunden: %s\n' "$label" "$needle"
    FAIL=$((FAIL + 1))
  else
    printf '  ok    %s\n' "$label"
    PASS=$((PASS + 1))
  fi
}

echo "=== gap-report.sh Regressionstests ==="
echo

echo "[1] Default nimmt den neuesten 28-Tage-Snapshot"
OUT="$(run_fixture 2026-01 --md)"
check        "Zeitraum ist 28 Tage"                "$OUT" "GSC-Status: 28 Tage"
check        "Snapshot endet 2026-01-31"           "$OUT" "bis \`2026-01-31\`"
check        "Quelldatei wird ausgewiesen"         "$OUT" "gsc-export-28d-2026-01-31.csv"
check        "aktueller Rang 26,6"                 "$OUT" "rankt Pos. 26,6"
check_absent "alte Bestposition nicht als Ist-Wert" "$OUT" "rankt Pos. 11,0"
check        "11,0 bleibt als Vorperiode sichtbar"  "$OUT" "vorher Pos. 11,0"
check_absent "alter Export nicht als Quelle"       "$OUT" "gsc-export-28d-2025-12-31.csv"
echo

echo "[2] --7d liest den 7-Tage-Snapshot"
OUT="$(run_fixture 2026-01 --7d --md)"
check        "Zeitraum ist 7 Tage"      "$OUT" "GSC-Status: 7 Tage"
check        "Quelldatei ist der 7d-Export" "$OUT" "gsc-export-7d-2026-01-31.csv"
check        "aktueller Rang 35,3"      "$OUT" "Pos. 35,3"
check_absent "kein 28d-Wert vermischt"  "$OUT" "Pos. 26,6"
echo

echo "[3] --28d ist gleichwertig zum Default"
OUT_DEFAULT="$(run_fixture 2026-01 --md)"
OUT_EXPLICIT="$(run_fixture 2026-01 --28d --md)"
if [[ "$OUT_DEFAULT" == "$OUT_EXPLICIT" ]]; then
  printf '  ok    identische Ausgabe\n'
  PASS=$((PASS + 1))
else
  printf '  FAIL  identische Ausgabe\n'
  FAIL=$((FAIL + 1))
fi
echo

echo "[4] Mehrere URLs je Query werden nicht still zusammengefasst"
OUT="$(run_fixture 2026-01 --md)"
check "Zweitseite meldet Owner-Konflikt"         "$OUT" "dieselbe Query rankt zusaetzlich mit /gamma-seite/"
check "Zweitseite mit Position und Impressionen" "$OUT" "/gamma-seite/ (Pos. 57,6, 28 Impr.)"
check "auch eine Impression zaehlt als Konflikt" "$OUT" "dieselbe Query rankt zusaetzlich mit /beta/ (Pos. 62,0, 1 Impr.)"
check "Registry-Luecke wird gemeldet"            "$OUT" "/epsilon/ (Pos. 8,0; vorher Pos. 10,0, 9 Impr.), fehlt in query-ownership.csv"
check_absent "previous-only URL ist kein aktueller Owner" "$OUT" "/old-beta/"
echo

echo "[4b] Mehrfach-URL-Abschnitt zeigt alle URLs vollstaendig"
OUT="$(run_fixture 2026-01 --md)"
check        "Abschnitt existiert"           "$OUT" "## MEHRFACH-URLS (2)"
check        "Owner ist markiert"            "$OUT" "=/beta/ (Pos. 29,6; vorher Pos. 9,5, 43 Impr.)"
check        "Fremd-URL ist markiert"        "$OUT" "+/gamma-seite/ (Pos. 57,6, 28 Impr.)"
check        "Impressionssumme stimmt"       "$OUT" "| testfrage beta | 71 |"
check_absent "Einzel-URL-Query fehlt zurecht" "$OUT" "| testfrage alpha | 190 |"
echo

echo "[5] Schreibvariante auf derselben URL ist kein Konflikt"
OUT="$(run_fixture 2026-01 --md)"
check        "Impressionen der Variante addiert" "$OUT" "/delta/ (rankt Pos. 12,0; vorher Pos. 14,0, 12 Impr.)"
check_absent "keine doppelte /delta/-Zeile"      "$OUT" "zusaetzlich mit /delta/"
echo

echo "[6] Fehlender Zeitraum bricht sichtbar ab"
OUT="$(run_fixture 2026-01 --range=90 --md)"
STATUS=$?
check "Fehlermeldung nennt den Zeitraum" "$OUT" "Kein 90-Tage-Snapshot"
if [[ $STATUS -ne 0 ]]; then
  printf '  ok    Exit-Code ungleich 0\n'
  PASS=$((PASS + 1))
else
  printf '  FAIL  Exit-Code ungleich 0\n'
  FAIL=$((FAIL + 1))
fi
OUT="$(run_fixture 2026-01 --unbekannt 2>&1)"
check "unbekannte Option wird abgelehnt" "$OUT" "Unbekannte Option"
echo

echo "[7] Akzeptanzwert auf den echten Repo-Exporten"
OUT="$(bash "$GAP_REPORT" --28d --md 2>&1 | grep -i '^| wordpress agentur hannover ')"
check        "28d meldet Pos. 26,6" "$OUT" "Pos. 26,6"
check_absent "28d meldet nicht Pos. 11" "$OUT" "Pos. 11,0"
OUT="$(bash "$GAP_REPORT" --7d --md 2>&1 | grep -i '^| wordpress agentur hannover ')"
check "7d meldet Pos. 35,3" "$OUT" "Pos. 35,3"
echo

echo "=== $PASS bestanden, $FAIL fehlgeschlagen ==="
[[ $FAIL -eq 0 ]]
