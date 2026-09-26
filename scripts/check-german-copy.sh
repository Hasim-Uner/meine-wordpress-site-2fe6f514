#!/usr/bin/env bash

set -euo pipefail

BASE_REF="${1:-HEAD}"
HEAD_REF="${2:-}"

TARGETS=(
  "blocksy-child/page-*.php"
  "blocksy-child/template-*.php"
  "blocksy-child/template-parts/*.php"
  ":(glob)content/**/*.md"
  ":(glob)docs/**/*.md"
)

PATTERN='(^|[^[:alnum:]_/-])(oeffentlich|Oeffentlich|oeffentlichen|Oeffentlichen|oeffentliche|Oeffentliche|oeffentlicher|Oeffentlicher|oeffentliches|Oeffentliches|Datenschutzerklaerung|Datenschutzerklaerungen|Kurzueberblick|zugaenglich|Zugaenglich|unberuehrt|Unberuehrt|geschaeftlich|Geschaeftlich|geschaeftliche|Geschaeftliche|pruefenden|Pruefenden|pruefen|Pruefen|prueft|Prueft|geschuetzt|Geschuetzt|uebermittelt|Uebermittelt|uebertragung|Uebertragung|ueberblick|Ueberblick|fuer|Fuer|koennen|Koennen|zustaendig|Zustaendig|aufsichtsbehoerde|Aufsichtsbehoerde|datenuebertragbarkeit|Datenuebertragbarkeit)([^[:alnum:]_/-]|$)'

DIFF_OUTPUT="$(bash scripts/check-diff.sh "${BASE_REF}" "${HEAD_REF}" "${TARGETS[@]}")"

if [[ -z "${DIFF_OUTPUT}" ]]; then
  exit 0
fi

MATCHES="$(
  printf '%s\n' "${DIFF_OUTPUT}" \
    | grep -E '^\+' \
    | grep -vE '^\+\+\+' \
    | grep -vE '^\+\s*(//|\*|/\*|#|<!--)' \
    | grep -En "${PATTERN}" || true
)"

if [[ -n "${MATCHES}" ]]; then
  echo "German copy guard failed."
  echo "Use real UTF-8 umlauts in visible German text."
  echo "ASCII transliterations are only acceptable for slugs, file names, URLs, and code identifiers."
  echo
  echo "${MATCHES}"
  exit 1
fi
