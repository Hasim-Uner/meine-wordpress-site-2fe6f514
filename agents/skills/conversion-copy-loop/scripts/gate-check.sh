#!/usr/bin/env bash
#
# Abbruchbedingung fuer den Copy-Loop. Fuehrt review-route.sh aus, liest dessen
# Ampel und uebersetzt sie in ein Exit-Signal: ROT -> 1, sonst 0.
#
# Kodiert keine eigene Bewertungsregel. Die Ampel gehoert review-route.sh, die
# Copy-Muster gehoeren copy-check.sh, die Begriffslisten dem Canon. Hier wird
# nur zusammengefuehrt und entschieden, ob eine weitere Runde noetig ist.
#
# Deterministisch: gleicher Commit + gleicher Arbeitsbaum -> gleiche Ausgabe.
# Nur deshalb ist der Vergleich zweier Runden ueberhaupt aussagekraeftig.
#
# Usage: gate-check.sh /waermepumpen-leads/

set -euo pipefail

ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "$ROOT"

REVIEW_SCRIPT="agents/skills/route-conversion-review/scripts/review-route.sh"
COPY_SCRIPT="agents/skills/seo-conversion-copywriting/scripts/copy-check.sh"

RAW_ROUTE="${1:-}"

if [[ -z "${RAW_ROUTE}" ]]; then
  cat >&2 <<'EOF'
Fehlt: Route.

  gate-check.sh /whitelabel-retainer/
  gate-check.sh /wgos/

Bekannte Routen stehen in llms.txt.
EOF
  exit 2
fi

for s in "${REVIEW_SCRIPT}" "${COPY_SCRIPT}"; do
  [[ -f "$s" ]] || { printf 'Fehlt: %s\n' "$s" >&2; exit 2; }
done

# ---- Ampel einsammeln ------------------------------------------------------

# review-route.sh endet mit 1, wenn kein Template existiert. Diesen Fall nicht
# verschlucken: seine Diagnose ist besser als jede, die hier entstehen koennte.
set +e
REVIEW_OUT="$(bash "${REVIEW_SCRIPT}" "${RAW_ROUTE}" 2>&1)"
REVIEW_RC=$?
set -e

if [[ "${REVIEW_RC}" -ne 0 ]]; then
  printf '%s\n' "${REVIEW_OUT}" >&2
  exit "${REVIEW_RC}"
fi

# Die Template-Aufloesung inklusive Alias-Tabelle steht in review-route.sh. Sie
# hier zu wiederholen hiesse, sie doppelt zu pflegen — also aus dem Report lesen.
TEMPLATE="$({ printf '%s\n' "${REVIEW_OUT}" | rg -m1 -o '^Template: (.*)$' -r '$1' 2>/dev/null || true; })"
ROUTE_SHOWN="$({ printf '%s\n' "${REVIEW_OUT}" | rg -m1 -o 'Route-Review: (\S+)' -r '$1' 2>/dev/null || true; })"

# Verdikt-Zeilen tragen als einzige das Muster "  [STATE] ".
AMPEL="$({ printf '%s\n' "${REVIEW_OUT}" | rg '^[[:space:]]*\[(OK|GELB|ROT)' 2>/dev/null || true; })"

if [[ -z "${AMPEL}" ]]; then
  printf 'Keine Ampel in der Ausgabe von %s gefunden.\n' "${REVIEW_SCRIPT}" >&2
  printf 'Hat sich dort das Ausgabeformat geaendert? Dann gehoert der Parser hier nach.\n' >&2
  exit 2
fi

# rg endet ohne Treffer mit 1; unter pipefail reisst das die Zuweisung mit.
count_state() {
  { printf '%s\n' "${AMPEL}" | rg -c "^[[:space:]]*\[$1" 2>/dev/null || true; } | rg '^[0-9]+$' || echo 0
}

pick_state() {
  { printf '%s\n' "${AMPEL}" | rg "^[[:space:]]*\[$1" 2>/dev/null || true; }
}

N_ROT="$(count_state 'ROT')"
N_GELB="$(count_state 'GELB')"
N_OK="$(count_state 'OK')"

# ---- Advisory --------------------------------------------------------------

COPY_SECTIONS=""
if [[ -n "${TEMPLATE}" && -f "${TEMPLATE}" ]]; then
  COPY_OUT="$(bash "${COPY_SCRIPT}" "${TEMPLATE}" 2>&1 || true)"
  COPY_SECTIONS="$({ printf '%s\n' "${COPY_OUT}" | rg -o '^== (.+) ==$' -r '$1' 2>/dev/null || true; } | paste -sd, - 2>/dev/null | sed 's/,/, /g' || true)"
fi

# ---- Report ----------------------------------------------------------------

printf '\n########## Gate-Check: %s ##########\n' "${ROUTE_SHOWN:-${RAW_ROUTE}}"
[[ -n "${TEMPLATE}" ]] && printf 'Template: %s\n' "${TEMPLATE}"

printf '\n== Harte Gates (Abbruchbedingung) ==\n'
# Dringendes zuerst — die Reihenfolge ist die Arbeitsreihenfolge.
pick_state 'ROT'
pick_state 'GELB'
pick_state 'OK'
printf '\n  %s ROT, %s GELB, %s OK\n' "${N_ROT}" "${N_GELB}" "${N_OK}"

printf '\n== Advisory (Urteil noetig, kein Gate) ==\n'
if [[ -n "${COPY_SECTIONS}" ]]; then
  printf '  %s\n' "${COPY_SECTIONS}"
  printf -- '  -> Jeder Treffer wird entschieden: behoben oder begruendet stehen\n'
  printf -- '     gelassen. Nie automatisch entfernen — copy-check.sh ist advisory.\n'
  printf -- '  -> Details: %s %s\n' "${COPY_SCRIPT}" "${TEMPLATE}"
else
  printf '  Keine Treffer im Template.\n'
fi

printf '\n== Verdikt ==\n'
if [[ "${N_ROT}" -gt 0 ]]; then
  ROT_ZONES="$({ pick_state 'ROT' | rg -o '^[[:space:]]*\[ROT[[:space:]]*\][[:space:]]*(\S+([[:space:]]\S+)*?)[[:space:]]{2,}' -r '$1' 2>/dev/null || true; } | paste -sd, - 2>/dev/null | sed 's/,/, /g' || true)"
  printf '  ROT — weitere Runde noetig.\n'
  [[ -n "${ROT_ZONES}" ]] && printf '  P0 zuerst: %s\n' "${ROT_ZONES}"
  printf '\n  Vor dem Fix pruefen: liegt die Copy im WordPress-Editor statt im\n'
  printf '  Template? Dann ist ROT eine Manual-WP-Aufgabe, kein Repo-Fix.\n'
  exit 1
fi

printf '  Kein ROT. Harte Gates erfuellt.\n'
printf '\n  Der Loop stoppt trotzdem erst, wenn zusaetzlich gilt:\n'
printf '  - kein offener P0 aus wordpress-cro-content-design-audit\n'
printf '  - jeder Advisory-Treffer oben ist entschieden\n'
[[ "${N_GELB}" -gt 0 ]] && printf '  - die %s GELB-Zeilen sind geprueft (GELB blockiert nicht, klaert aber)\n' "${N_GELB}"
exit 0
