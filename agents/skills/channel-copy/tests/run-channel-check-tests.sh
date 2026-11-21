#!/usr/bin/env bash
# Offline-Tests fuer scripts/channel-check.py (Exit-Codes, Regel-IDs, --strip).
set -uo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd -P)"
CHECK="$HERE/../scripts/channel-check.py"
FIX="$HERE/fixtures"
TMP="$(mktemp -d "${TMPDIR:-/tmp}/channel-check.XXXXXX")"
trap 'rm -rf "$TMP"' EXIT
export PYTHONDONTWRITEBYTECODE=1
unset OWN_HOST

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

# Brief: neue Regeln (Antwortwege, namentliche Anrede, Front Matter, Freigabe)
mkbrief() { # datei [sed-Ausdruck]: gutes Brief-Fixture mit einer Aenderung
  if [[ $# -ge 2 ]]; then sed -E "$2" "$FIX/brief-good.md" >"$1"; else cp "$FIX/brief-good.md" "$1"; fi
}
mkbrief "$TMP/two-channels.md" 's/^P\.S\. /P.S. Oder rufen Sie mich an: 0511 1234567. /'
expect_code "brief zwei Antwortwege" 1 brief "$TMP/two-channels.md"
expect_rule "brief zwei Antwortwege" mehrere-antwortwege
mkbrief "$TMP/mail-and-url.md" 's/^P\.S\. /P.S. Oder besuchen Sie https:\/\/example.de\/kontakt\/ . /'
run brief "$TMP/mail-and-url.md"
expect_rule "brief Antwort-Formulierung und URL" mehrere-antwortwege
mkbrief "$TMP/phone-only.md" 's/^P\.S\. /P.S. Telefon +49 511 123 4567. /'
run brief "$TMP/phone-only.md"
expect_rule "brief Telefon zaehlt als Antwortweg" mehrere-antwortwege
mkbrief "$TMP/phone-date.md" 's/^P\.S\. /P.S. Stand 03.05.2026. /'
run brief "$TMP/phone-date.md"
expect_no_rule "brief Datum ist keine Telefonnummer" mehrere-antwortwege
mkbrief "$TMP/same-channel-twice.md" 's/^P\.S\. /P.S. Antworten Sie an antwort@example.de. /; s/an die E-Mail-Adresse im Briefkopf/an antwort@example.de/'
run brief "$TMP/same-channel-twice.md"
expect_no_rule "brief derselbe Antwortweg wiederholt" mehrere-antwortwege

mkbrief "$TMP/salutation.md" 's/^Sehr geehrter Herr Beispiel,/Sehr geehrter Herr,/'
expect_code "brief Anrede ohne Namen" 1 brief "$TMP/salutation.md"
expect_rule "brief Anrede ohne Namen" anrede-nicht-namentlich
run brief "$FIX/brief-bad.md"
expect_rule "brief schlecht" anrede-nicht-namentlich
for rule in fm-adressquelle fm-widerspruch fm-antwortweg fm-freigabe; do expect_rule "brief schlecht ohne Front Matter" "$rule"; done

sed '/^---$/,/^---$/d' "$FIX/brief-good.md" >"$TMP/no-fm.md"
expect_code "brief ohne Front Matter" 1 brief "$TMP/no-fm.md"
for rule in fm-adressquelle fm-widerspruch fm-antwortweg fm-freigabe; do expect_rule "brief ohne Front Matter" "$rule"; done

mkbrief "$TMP/fm-empty.md" 's/^adressquelle: .*/adressquelle:/'
run brief "$TMP/fm-empty.md"; expect_rule "fm adressquelle leer" fm-adressquelle
mkbrief "$TMP/fm-objection.md" 's/^widerspruchsliste_geprueft: .*/widerspruchsliste_geprueft: ja/'
run brief "$TMP/fm-objection.md"; expect_rule "fm Widerspruchsliste ohne Datum" fm-widerspruch
mkbrief "$TMP/fm-objection2.md" 's/^widerspruchsliste_geprueft: .*/widerspruchsliste_geprueft: nein 2026-11-20/'
run brief "$TMP/fm-objection2.md"; expect_rule "fm Widerspruchsliste nein" fm-widerspruch
mkbrief "$TMP/fm-objection3.md" 's/^widerspruchsliste_geprueft: .*/widerspruchsliste_geprueft: ja 2026-13-45/'
run brief "$TMP/fm-objection3.md"; expect_rule "fm Widerspruchsliste Datum ungueltig" fm-widerspruch
mkbrief "$TMP/fm-route.md" 's/^antwortweg: .*/antwortweg: telefon/'
run brief "$TMP/fm-route.md"; expect_rule "fm unbekannte Route" fm-antwortweg
mkbrief "$TMP/fm-approval.md" 's/^freigabe: .*/freigabe: HM/'
run brief "$TMP/fm-approval.md"; expect_rule "fm Freigabe ohne Datum" fm-freigabe

mkbrief "$TMP/fm-offen.md" 's/^freigabe: .*/freigabe: offen/'
expect_code "brief Freigabe offen" 1 brief "$TMP/fm-offen.md"
expect_rule "brief Freigabe offen" freigabe-offen

# Route und URL muessen zusammenpassen; passende URL bleibt ein einziger Antwortweg
swap_url() { # datei route url: Antwortweg-Satz durch eine URL ersetzen
  sed -E "s/^antwortweg: .*/antwortweg: $2/; s|, antworten Sie mir kurz an die E-Mail-Adresse im Briefkopf\.| unter $3 nachlesen.|" \
    "$FIX/brief-good.md" >"$1"
}
swap_url "$TMP/route-ok.md" white-label https://hasimuener.de/whitelabel-retainer/
expect_code "brief Route passt zur URL" 0 brief "$TMP/route-ok.md"
swap_url "$TMP/route-bad.md" marktcheck https://hasimuener.de/whitelabel-retainer/
expect_code "brief Route passt nicht zur URL" 1 brief "$TMP/route-bad.md"
expect_rule "brief Route passt nicht zur URL" antwortweg-url

# Marktcheck-Route aus llms.txt hat ein Fragment (#marktcheck); die URL muss es tragen
swap_url "$TMP/mc-ok.md" marktcheck https://hasimuener.de/solar-waermepumpen-leadgenerierung/#marktcheck
expect_code "brief Marktcheck mit Fragment" 0 brief "$TMP/mc-ok.md"
swap_url "$TMP/mc-nofrag.md" marktcheck https://hasimuener.de/solar-waermepumpen-leadgenerierung/
expect_code "brief Marktcheck ohne Fragment" 1 brief "$TMP/mc-nofrag.md"
expect_rule "brief Marktcheck ohne Fragment" antwortweg-url
swap_url "$TMP/mc-wrongfrag.md" marktcheck https://hasimuener.de/solar-waermepumpen-leadgenerierung/#anfrage
expect_code "brief Marktcheck falsches Fragment" 1 brief "$TMP/mc-wrongfrag.md"
expect_rule "brief Marktcheck falsches Fragment" antwortweg-url

# Eigener Host: Standard hasimuener.de (mit/ohne www), Override per OWN_HOST, fremde Hosts sind Befunde
swap_url "$TMP/host-www.md" projektanfrage https://www.hasimuener.de/kontakt/?type=project
expect_code "brief www-Host" 0 brief "$TMP/host-www.md"
swap_url "$TMP/host-foreign.md" projektanfrage https://example.de/kontakt/?type=project
expect_code "brief fremder Host" 1 brief "$TMP/host-foreign.md"
expect_rule "brief fremder Host" fremder-host
expect_no_rule "brief fremder Host" antwortweg-url
swap_url "$TMP/host-suffix.md" projektanfrage https://hasimuener.de.example.de/kontakt/?type=project
run brief "$TMP/host-suffix.md"; expect_rule "brief Host mit Eigen-Suffix" fremder-host
swap_url "$TMP/host-userinfo.md" projektanfrage https://hasimuener.de@example.de/kontakt/?type=project
run brief "$TMP/host-userinfo.md"; expect_rule "brief Host hinter Userinfo" fremder-host
OWN_HOST=example.de run brief "$TMP/host-foreign.md"
[[ "$CODE" -eq 0 ]] || fail "brief OWN_HOST-Override: Exit $CODE statt 0
$OUT"
OWN_HOST=example.de run brief "$TMP/host-www.md"
expect_rule "brief OWN_HOST-Override sperrt Standardhost" fremder-host

# Kein Antwortweg im Text: genau einer ist Pflicht
mkbrief "$TMP/no-channel.md" 's/^Wenn Sie wissen wollen.*/Bei Interesse. [CTA]/'
expect_code "brief ohne Antwortweg" 1 brief "$TMP/no-channel.md"
expect_rule "brief ohne Antwortweg" kein-antwortweg
expect_no_rule "brief ohne Antwortweg" mehrere-antwortwege
run brief "$FIX/brief-good.md"
expect_no_rule "brief gut" kein-antwortweg

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

# LinkedIn: Beobachtung braucht Quelle, Grenze braucht Substanz
sed -E 's/ *\[Q:[^]]*\]//' "$FIX/linkedin-good.md" >"$TMP/li-noq.md"
expect_code "linkedin ohne [Q:]" 1 linkedin "$TMP/li-noq.md"
expect_rule "linkedin ohne [Q:]" beobachtung-ohne-quelle
sed -E 's/^Gilt nicht, wenn .*/Es gibt eine Ausnahme./' "$FIX/linkedin-good.md" >"$TMP/li-thin.md"
expect_code "linkedin duenne Grenze" 1 linkedin "$TMP/li-thin.md"
expect_rule "linkedin duenne Grenze" grenze-zu-duenn
expect_no_rule "linkedin duenne Grenze" grenze-fehlt
run linkedin "$FIX/linkedin-bad.md"
expect_rule "linkedin schlecht" beobachtung-ohne-quelle

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
grep -q '^---' <<<"$OUT" && fail "strip: Front Matter noch vorhanden"
grep -q 'adressquelle' <<<"$OUT" && fail "strip: Front-Matter-Feld noch vorhanden"
grep -q '^Sehr geehrter Herr Beispiel,' <<<"$OUT" || fail "strip: Anrede fehlt"
run brief --strip "$FIX/linkedin-good.md"
[[ "$CODE" -eq 0 ]] || fail "strip mit Unterbefehl: Exit $CODE"

# Aufruffehler -> 2
expect_code "unbekannter Unterbefehl" 2 mail "$FIX/brief-good.md"
expect_code "ohne Argumente" 2
expect_code "Datei fehlt" 2 brief "$TMP/gibt-es-nicht.md"
expect_code "Unterbefehl ohne Datei" 2 linkedin

printf '%s Fehler\n' "$failures"
[[ "$failures" -eq 0 ]]
