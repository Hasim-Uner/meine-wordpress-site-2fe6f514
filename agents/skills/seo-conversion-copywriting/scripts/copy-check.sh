#!/usr/bin/env bash
#
# Fundstellen-Report fuer verkaufsorientierte deutsche Copy.
# Meldet Zahlen ohne sichtbaren Beleg, Garantie- und Absolut-Versprechen,
# Floskeln sowie offene Belegmarker.
#
# Advisory, kein Gate: jeder Treffer wird geprueft, nicht automatisch entfernt.
# ASCII-Umlaute und Canon-Drift decken scripts/check-german-copy.sh und
# scripts/lint-canon-drift.sh ab, hier bewusst nicht dupliziert.
#
# Nutzung:
#   copy-check.sh                  geaenderte sichtbare Copy gegen HEAD
#   copy-check.sh BASE [HEAD]      Commit-Range
#   copy-check.sh pfad [pfad ...]  konkrete Dateien oder Ordner, auch untracked

set -euo pipefail

cd "$(git rev-parse --show-toplevel)"

TARGETS=(
  "blocksy-child/front-page.php"
  "blocksy-child/page-*.php"
  "blocksy-child/template-*.php"
  "blocksy-child/template-parts/*.php"
  "content/*.md"
  "content/**/*.md"
)

# Code-Zeilen tragen Zahlen, die keine Copy-Aussage sind.
NOISE='style=|padding|margin|width:|height:|font-size|line-height|rgba?\(|var\(--|@media|z-index|opacity|translate|viewBox|stroke|version|@since'

if [[ $# -gt 0 && -e "$1" ]]; then
  SCOPE="Dateien: $*"
  SOURCE="$(grep -rIn '' -- "$@" 2>/dev/null || true)"
else
  BASE_REF="${1:-HEAD}"
  HEAD_REF="${2:-}"
  SCOPE="Diff: ${BASE_REF}${HEAD_REF:+..${HEAD_REF}}"
  if [[ -n "${HEAD_REF}" ]]; then
    RAW="$(git diff --unified=0 --no-color "${BASE_REF}" "${HEAD_REF}" -- "${TARGETS[@]}" || true)"
  else
    RAW="$(git diff --unified=0 --no-color "${BASE_REF}" -- "${TARGETS[@]}" || true)"
  fi
  SOURCE="$(printf '%s\n' "${RAW}" | grep -E '^\+' | grep -vE '^\+\+\+' || true)"
fi

SOURCE="$(printf '%s\n' "${SOURCE}" | grep -vE "${NOISE}" || true)"

echo "Copy-Check — ${SCOPE}"

FOUND=0

report() {
  local label="$1" hint="$2" pattern="$3"
  local hits count

  hits="$(printf '%s\n' "${SOURCE}" | grep -E "${pattern}" || true)"
  [[ -z "${hits}" ]] && return 0

  count="$(printf '%s\n' "${hits}" | wc -l | tr -d ' ')"
  FOUND=1

  printf '\n== %s (%s) ==\n' "${label}" "${count}"
  printf '%s\n' "${hits}" | head -12
  [[ "${count}" -gt 12 ]] && printf '   ... %s weitere\n' "$((count - 12))"
  printf '   -> %s\n' "${hint}"
}

report "Zahlen" \
  "Steht der Wert in der Belegdatei? Sonst streichen oder als [BELEG FEHLT: ...] markieren." \
  '(^|[^[:alnum:]_-])[0-9]+([.,][0-9]+)?[[:space:]]*(%|€|EUR|Prozent|-fach|Tage|Wochen|Monate|Stunden|Anfragen|Leads|Projekte|Kunden)|(bis zu|mehr als|ueber|über|durchschnittlich|im Schnitt|Faktor)[[:space:]]+[0-9]'

report "Versprechen" \
  "Garantie, Rechtsurteil oder Absolutaussage. In eine pruefbare Tatsache umschreiben." \
  '(^|[^[:alnum:]_-])(garantier|Garantie|rechtssicher|Rechtssicherheit|abmahnsicher|rechtskonform|DSGVO-konform|datenschutzkonform|100[[:space:]]*%|risikofrei|ohne Risiko|in jedem Fall|jederzeit kuendbar|verspricht|versprechen wir|sicher(t|n)? Ihnen)'

report "Floskeln" \
  "Steht so auf jeder Wettbewerberseite. Konkrete Folge fuer den Betrieb einsetzen." \
  '(^|[^[:alnum:]_-])(ganzheitlich|maßgeschneidert|massgeschneidert|passgenau|innovativ|Innovation|Synergie|nahtlos|State of the Art|zukunftssicher|Rundum-sorglos|aus einer Hand|Ihr (starker )?Partner|in der heutigen Zeit|revolutionär|einzigartig|mit Leidenschaft|Full-Service|optimal(e|en)? Ergebnis)'

report "Offene Marker" \
  "Vor dem Livegang aufloesen: Beleg liefern oder Aussage streichen." \
  '\[BELEG FEHLT|\[KUNDENSPRACHE FEHLT|BELEGDATEI EINTRAGEN|TODO|TBD'

if [[ "${FOUND}" -eq 0 ]]; then
  echo "Keine Treffer."
fi

echo
echo "Die vier Pruefragen aus SKILL.md bleiben offen — weggelassene Einschraenkungen"
echo "und traege Absaetze findet kein Muster."
