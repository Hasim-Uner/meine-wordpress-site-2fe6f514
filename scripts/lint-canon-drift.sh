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
  # Archivierte Auftrags- und Vorlagentexte. Sie zitieren Preise im Wortlaut,
  # in dem sie beauftragt wurden, und sind genau deshalb belegkraeftig — sie
  # nachtraeglich auf den Canon umzuschreiben wuerde ihren Zweck zerstoeren.
  # Nichts davon wird ausgeliefert. Gleiche Begruendung wie bei audits,
  # decisions und references darueber.
  ":(exclude)docs/briefings/**"
  # Rohexporte aus Search Console und dem SEO-Cockpit. Sie zitieren Title und
  # Meta-Description so, wie Google sie gesehen hat — Fremddaten, nicht
  # editierbar. Gleiche Ausnahme wie `ignore seo-research/**` in
  # scripts/canon-forbidden-values.txt fuer den repo-weiten Guard.
  ":(exclude)seo-research/**"
)

if [[ -n "${HEAD_REF}" ]]; then
  DIFF_OUTPUT="$(git diff --unified=0 --no-color "${BASE_REF}" "${HEAD_REF}" -- "${PATHS[@]}" || true)"
else
  DIFF_OUTPUT="$(git diff --unified=0 --no-color "${BASE_REF}" -- "${PATHS[@]}" || true)"
fi

if [[ -z "${DIFF_OUTPUT}" ]]; then
  exit 0
fi

VALUE_PATTERN='(^|[^[:alnum:]_/-])(590[[:space:]]*(EUR|€)|750[[:space:]]*(EUR|€)|1\.500[[:space:]]*(EUR|€|€/Mt|/Mt|€/Monat|/Monat)|1500[[:space:]]*(EUR|€|€/Mt|/Mt|€/Monat|/Monat)|9\.900[[:space:]]*(EUR|€)|9900[[:space:]]*(EUR|€)|14\.900[[:space:]]*(EUR|€)|14900[[:space:]]*(EUR|€)|6900[[:space:]]*(EUR|€))([^[:alnum:]_/-]|$)'
TERM_PATTERN='(^|[^[:alnum:]_/-])(Pilotprojekt|Pilot|Beta|Test|eigentlich kostet das viel mehr|ich bin neu|starte gerade|Berufsanfänger|Modul)([^[:alnum:]_/-]|$)'

# Fristen-Literale. BRAND_AND_COPY.md ("Zusagen mit Zeitangabe") verbietet sie
# in Template, FAQ, Meta-Description und E-Mail: die Antwortzusage und der
# Marktcheck-Befund stehen im Canon. Bis 2026-09 fehlten sie hier, und
# assets/js/startseite.js konnte die Antwortzusage fest verdrahten und
# damit die Canon-Zusage aus dem sichtbaren Hero verdraengen — gruen an der CI
# vorbei. Der Guard prueft den Wortlaut, nicht nur den Preis.
PROMISE_PATTERN='(^|[^[:alnum:]_/-])([0-9]+[[:space:]]*(Werktage|Werktagen)|48[[:space:]]*(h|Stunden))([^[:alnum:]_/-]|$)'
# Zeilen, die den Canon selbst aufrufen, duerfen ihren Fallback im Wortlaut
# tragen — das ist das etablierte Muster im Repo, kein Drift.
PROMISE_ALLOW='hu_response_promise|hu_marketcheck_reply_label|HU_RESPONSE_HOURS|HU_RESPONSE_PROMISE|HU_TRACKING_RESPONSE_BUSINESS_DAYS'

ADDED_LINES="$(
  printf '%s\n' "${DIFF_OUTPUT}" \
    | grep -E '^\+' \
    | grep -vE '^\+\+\+' \
    | grep -vE '^\+\s*(//|\*|/\*|#|<!--)' || true
)"

MATCHES="$(
  printf '%s\n' "${ADDED_LINES}" \
    | grep -En "${VALUE_PATTERN}|${TERM_PATTERN}" || true
)"

PROMISE_MATCHES="$(
  printf '%s\n' "${ADDED_LINES}" \
    | grep -En "${PROMISE_PATTERN}" \
    | grep -vE "${PROMISE_ALLOW}" || true
)"

if [[ -n "${MATCHES}" ]]; then
  echo "Canon drift guard failed."
  echo "Move pricing, diagnosis, and forbidden customer-facing wording to blocksy-child/inc/canon/."
  echo
  echo "${MATCHES}"
  exit 1
fi

if [[ -n "${PROMISE_MATCHES}" ]]; then
  echo "Canon drift guard failed."
  echo "Time promises belong to the canon, never to a template, FAQ, meta description or email."
  echo "Use hu_response_promise() for the reply promise and hu_marketcheck_reply_label() for the marketcheck finding."
  echo
  echo "${PROMISE_MATCHES}"
  exit 1
fi
