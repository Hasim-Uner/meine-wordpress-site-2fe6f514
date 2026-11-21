#!/usr/bin/env bash
# Offline-Tests fuer scripts/channel-check.py (Exit-Codes, Regel-IDs, --strip).
set -uo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd -P)"
CHECK="$HERE/../scripts/channel-check.py"
FIX="$HERE/fixtures"
TMP="$(mktemp -d "${TMPDIR:-/tmp}/channel-check.XXXXXX")"
trap 'rm -rf "$TMP"' EXIT
export PYTHONDONTWRITEBYTECODE=1

failures=0
fail() { printf 'FAIL: %s\n' "$1" >&2; failures=$((failures + 1)); }

run() { OUT="$(python3 -I "$CHECK" "$@" 2>&1)"; CODE=$?; }
expect_code() { # name want args...
  local name="$1" want="$2"; shift 2
  run "$@"
  [[ "$CODE" -eq "$want" ]] || fail "$name: Exit $CODE statt $want
$OUT"
}
expect_rule() { # name rule
  grep -Eq "^L[-0-9]+ +$2 " <<<"$OUT" || fail "$1: Regel $2 nicht gemeldet
$OUT"
}
expect_no_rule() { # name rule
  ! grep -Eq "^L[-0-9]+ +$2 " <<<"$OUT" || fail "$1: Regel $2 unerwartet gemeldet
$OUT"
}

# Brief: gut -> 0
expect_code "brief gut" 0 brief "$FIX/brief-good.md"

# Brief: schlecht -> 1, jede Regel einzeln
expect_code "brief schlecht" 1 brief "$FIX/brief-bad.md"
for rule in anrede cta-anzahl zahl-ohne-quelle ps-fehlt agentur-floskel einstieg-ohne-fakt einstieg-ich-wir widerspruch-quelle; do
  expect_rule "brief schlecht" "$rule"
done

# Brief: weitere Regeln aus Einzelentwuerfen
printf '%s\n' 'Ihr Formular hat ein Problem. [Q: Beispiel]' '' 'P.S. Nur noch wenige Plätze frei, Frist bis zum 3. Mai.' \
  'Widerspruch jederzeit, Datenschutz im Briefkopf. [CTA] Founding Growth Audit kostenlos.' >"$TMP/urgency.md"
expect_code "brief dringlichkeit" 1 brief "$TMP/urgency.md"
for rule in dringlichkeit brand-bann kostenlos zahl-ohne-quelle; do expect_rule "brief dringlichkeit" "$rule"; done
expect_no_rule "brief dringlichkeit" anrede

python3 - "$TMP/long.md" <<'PY'
import sys
words = ' '.join(['Wort'] * 360)
open(sys.argv[1], 'w').write(f'Ihr Formular verliert Anfragen. [Q: Beispiel]\n\n{words}.\n')
PY
expect_code "brief lang" 1 brief "$TMP/long.md"
expect_rule "brief lang" laenge

# Quellenmarker hinter dem Satzende zaehlt, Zahl in Rechtszitat und Kommentar nicht
printf '%s\n' '<!-- 99 Notiz -->' 'Ihr Formular braucht 3 Klicks. [Q: Beispiel]' 'Siehe Art. 14 DSGVO und § 7 Abs. 2 Nr. 1 UWG.' >"$TMP/marker.md"
run brief "$TMP/marker.md"
expect_no_rule "marker" zahl-ohne-quelle

# Unbekannter canon-Schluessel
printf '%s\n' 'Ihr Wert liegt bei 5 Tagen. [Q: canon:gibt_es_nicht_xyz]' >"$TMP/canon.md"
run brief "$TMP/canon.md"
expect_rule "canon" quelle-unbekannt

# LinkedIn: gut -> 0, schlecht -> 1 mit allen Regeln
expect_code "linkedin gut" 0 linkedin "$FIX/linkedin-good.md"
expect_code "linkedin schlecht" 1 linkedin "$FIX/linkedin-bad.md"
for rule in hook-laenge engagement-koeder hashtags url grenze-fehlt; do expect_rule "linkedin schlecht" "$rule"; done

printf '%s\n' 'Eine These. [CTA]' 'Gilt nicht, wenn alles stimmt.' >"$TMP/li-cta.md"
expect_code "linkedin cta" 1 linkedin "$TMP/li-cta.md"
expect_rule "linkedin cta" cta-verboten

# --strip entfernt Marker und Kommentare, laesst Text stehen
run --strip "$FIX/brief-good.md"
[[ "$CODE" -eq 0 ]] || fail "strip: Exit $CODE"
grep -q '\[Q:' <<<"$OUT" && fail "strip: [Q: noch vorhanden"
grep -q '\[CTA\]' <<<"$OUT" && fail "strip: [CTA] noch vorhanden"
grep -q '<!--' <<<"$OUT" && fail "strip: Kommentar noch vorhanden"
grep -q 'antworten Sie mir kurz an die E-Mail-Adresse im Briefkopf\.$' <<<"$OUT" || fail "strip: Satz fehlt oder Rest-Leerzeichen"
run brief --strip "$FIX/linkedin-good.md"
[[ "$CODE" -eq 0 ]] || fail "strip mit Unterbefehl: Exit $CODE"

# Aufruffehler -> 2
expect_code "unbekannter Unterbefehl" 2 mail "$FIX/brief-good.md"
expect_code "ohne Argumente" 2
expect_code "Datei fehlt" 2 brief "$TMP/gibt-es-nicht.md"
expect_code "Unterbefehl ohne Datei" 2 linkedin

printf '%s Fehler\n' "$failures"
[[ "$failures" -eq 0 ]]
