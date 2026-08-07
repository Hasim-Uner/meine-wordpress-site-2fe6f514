#!/usr/bin/env bash

set -euo pipefail

BASE_REF="${1:-HEAD}"
HEAD_REF="${2:-}"

PATHS=(
  "."
  ":(exclude)blocksy-child/inc/canon/**"
  ":(exclude)blocksy-child/energie-fahrplan/dist/**"
  ":(exclude)blocksy-child/energie-fahrplan/package-lock.json"
  ":(exclude)scripts/lint-canon-drift.sh"
  # Liest die Begriffe zur Laufzeit aus dem Canon und muss die mehrdeutigen
  # davon benennen duerfen, um sie von echten Verstoessen zu trennen —
  # gleiche Begruendung wie beim Selbstausschluss dieses Guards.
  ":(exclude)agents/skills/route-conversion-review/scripts/review-route.sh"
  ":(exclude)docs/audits/**"
  ":(exclude)docs/decisions/**"
  ":(exclude)docs/references/**"
)

if [[ -n "${HEAD_REF}" ]]; then
  DIFF_OUTPUT="$(git diff --unified=0 --no-color "${BASE_REF}" "${HEAD_REF}" -- "${PATHS[@]}" || true)"
else
  DIFF_OUTPUT="$(git diff --unified=0 --no-color "${BASE_REF}" -- "${PATHS[@]}" || true)"
fi

if [[ -z "${DIFF_OUTPUT}" ]]; then
  exit 0
fi

VALUE_PATTERN='(^|[^[:alnum:]_/-])(750[[:space:]]*(EUR|€)|1\.500[[:space:]]*(EUR|€|€/Mt|/Mt|€/Monat|/Monat)|1500[[:space:]]*(EUR|€|€/Mt|/Mt|€/Monat|/Monat)|9\.900[[:space:]]*(EUR|€)|9900[[:space:]]*(EUR|€)|14\.900[[:space:]]*(EUR|€)|14900[[:space:]]*(EUR|€)|6900[[:space:]]*(EUR|€))([^[:alnum:]_/-]|$)'
TERM_PATTERN='(^|[^[:alnum:]_/-])(Pilotprojekt|Pilot|Beta|Test|eigentlich kostet das viel mehr|ich bin neu|starte gerade|Berufsanfänger|Modul)([^[:alnum:]_/-]|$)'

MATCHES="$(
  printf '%s\n' "${DIFF_OUTPUT}" \
    | grep -E '^\+' \
    | grep -vE '^\+\+\+' \
    | grep -vE '^\+\s*(//|\*|/\*|#|<!--)' \
    | grep -En "${VALUE_PATTERN}|${TERM_PATTERN}" || true
)"

if [[ -n "${MATCHES}" ]]; then
  echo "Canon drift guard failed."
  echo "Move pricing, diagnosis, and forbidden customer-facing wording to blocksy-child/inc/canon/."
  echo
  echo "${MATCHES}"
  exit 1
fi
