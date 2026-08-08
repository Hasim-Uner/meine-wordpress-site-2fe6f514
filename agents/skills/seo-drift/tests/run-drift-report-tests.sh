#!/usr/bin/env bash
#
# Regressionstest fuer drift-report.sh.
#
# Haelt genau die Entscheidungen fest, die den Report wertvoll machen und die
# beim Umbau leicht kippen:
#   - die Trennung ABSTURZ / POSITION / SICHTBARKEIT nach Beweislage
#   - dass Cluster nur aus belegten Verlusten entstehen
#   - dass eine Aenderung NACH dem Messfenster nicht als Ursache gilt
#   - dass Verbesserungen nicht als Verlust gezaehlt werden
#
# Die Fixture unter tests/fixtures/ ist erfunden und traegt bewusst
# "testfrage *"-Queries. Nur der letzte Test laeuft gegen die echten
# Repo-Exporte und haelt den Akzeptanzwert fest.
#
#   bash agents/skills/seo-drift/tests/run-drift-report-tests.sh

set -uo pipefail

TESTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$TESTS_DIR/../../../.." && pwd)"
DRIFT="$REPO_ROOT/agents/skills/seo-drift/scripts/drift-report.sh"
FIXTURES="$TESTS_DIR/fixtures"

PASS=0
FAIL=0

run_fixture() {
  RESEARCH_DIR="$FIXTURES" bash "$DRIFT" "$@" 2>&1
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

check_exit() {
  local label="$1" expected="$2"
  shift 2
  RESEARCH_DIR="$FIXTURES" bash "$DRIFT" "$@" >/dev/null 2>&1
  local rc=$?
  if [[ "$rc" -eq "$expected" ]]; then
    printf '  ok    %s\n' "$label"
    PASS=$((PASS + 1))
  else
    printf '  FAIL  %s\n         erwartet Exit %s, war %s\n' "$label" "$expected" "$rc"
    FAIL=$((FAIL + 1))
  fi
}

echo "=== drift-report.sh Regressionstests ==="
echo

echo "[1] Snapshot-Wahl und Fenster"
OUT="$(run_fixture 2026-01 --md)"
check        "28 Tage als Default"                  "$OUT" "Fenster: 28 Tage"
check        "Fenstergrenzen aus current_start/end" "$OUT" "2026-01-04 bis 2026-01-31"
OUT7="$(run_fixture 2026-01 --7d --md)"
check        "--7d waehlt den 7-Tage-Snapshot"      "$OUT7" "Fenster: 7 Tage"
check_absent "7d mischt den 28d-Snapshot nicht ein" "$OUT7" "testfrage gamma"

echo
echo "[2] Klassifikation nach Beweislage"
check        "Position und Impressionen fallen -> ABSTURZ"      "$OUT" "ABSTURZ 2"
check        "vollstaendig fehlende Query -> VERSCHWUNDEN"       "$OUT" "VERSCHWUNDEN 1"
check        "Position faellt, Impressionen nicht -> POSITION"  "$OUT" "POSITION 2"
check        "Impressionen brechen ein, Position flach -> SICHTBARKEIT" "$OUT" "SICHTBARKEIT 1"
check        "Verbesserung wird nicht als Verlust gezaehlt"     "$OUT" "Verbesserungen: 1"
check_absent "verbesserte Query steht in keiner Verlustliste"   "$OUT" "testfrage epsilon"
check        "verschwundene Query wird benannt"                  "$OUT" "testfrage verschwunden"
check_absent "Page-Total wird nicht als Query ausgewertet"       "$OUT" "page total sentinel"

echo
echo "[3] Cluster nur aus belegten Verlusten"
check        "zwei ABSTURZ auf einer URL bilden ein Cluster" "$OUT" "/testseite-eins/"
check        "genau ein Cluster"                             "$OUT" "SEITEN MIT MEHREREN VERLUSTEN (1)"
# testseite-zwei hat zwei Verlustzeilen, beide nur POSITION. Ohne
# Impressionsdeckung ist das kein Seiten-Ereignis, sondern Rauschen.
# Geprueft wird in der Textausgabe: dort erscheint eine URL ausschliesslich als
# Cluster-Kopf. In Markdown traegt auch die Einzelverlust-Tabelle eine
# URL-Spalte, ein Treffer dort waere also kein Beweis.
OUT_TXT="$(run_fixture 2026-01)"
check_absent "zwei POSITION-Zeilen bilden kein Cluster"      "$OUT_TXT" "/testseite-zwei/"
check        "POSITION-Zeilen stehen unter Einzelverlusten"  "$OUT" "testfrage gamma"

echo
echo "[4] Repo-Korrelation"
check        "Fixture-URL ohne Template wird benannt" "$OUT" "kein Template im Repo"

echo
echo "[5] Exit-Codes"
check_exit   "ABSTURZ vorhanden -> Exit 1"        1 2026-01
check_exit   "fehlender Zeitraum -> Exit 2"       2 2026-01 --range=99
check_exit   "unbekannte Option -> Exit 2"        2 --quatsch
check_exit   "unbekannte Periode -> Exit 2"       2 1999-01
check_exit   "--help -> Exit 0"                   0 --help

echo
echo "[6] Akzeptanzwert auf den echten Repo-Exporten"
REAL="$(bash "$DRIFT" 2026-07 --md 2>&1)"
check        "Hannover-Cluster wird gefunden"      "$REAL" "/wordpress-agentur-hannover/"
check        "gemeinsamer Positionsverlust sichtbar" "$REAL" "wordpress hannover"
# Der Template-Commit liegt nach dem Messfenster. Ihn als Ursachenkandidaten zu
# fuehren waere schlicht falsch — genau das war der erste Fehler des Reports.
check        "Aenderung nach dem Fenster ist ausgeschlossen" "$REAL" "NACH dem Fenster geaendert"

echo
if [[ "$FAIL" -eq 0 ]]; then
  echo "=== $PASS bestanden, 0 fehlgeschlagen ==="
else
  echo "=== $PASS bestanden, $FAIL fehlgeschlagen ==="
fi
exit $(( FAIL > 0 ? 1 : 0 ))
