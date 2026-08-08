#!/usr/bin/env bash
#
# Vollpruefung einer einzelnen Route: Template aufloesen, Inventar sammeln,
# Hard Bans greppen, Ampel-Verdikt. Kodiert keine Bewertungsregeln — die
# gehoeren den Fachskills, siehe SKILL.md.
#
# Usage: review-route.sh /whitelabel-retainer/

set -euo pipefail

ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "$ROOT"

RAW_ROUTE="${1:-}"

if [[ -z "${RAW_ROUTE}" ]]; then
  cat >&2 <<'EOF'
Fehlt: Route.

  review-route.sh /whitelabel-retainer/
  review-route.sh /solar-waermepumpen-leadgenerierung/
  review-route.sh /

Bekannte Routen stehen in llms.txt.
EOF
  exit 2
fi

# /Foo/Bar/ -> foo-bar; Query und Fragment fallen weg (der Marktcheck-Anker
# gehoert zur Money Page, nicht zu einer eigenen Route).
SLUG="$(printf '%s' "${RAW_ROUTE}" | sed -e 's/[#?].*$//' -e 's#^/##' -e 's#/$##' | tr '[:upper:]' '[:lower:]')"

# Die oeffentliche Route bleibt, was der Nutzer gefragt hat — llms.txt und die
# Suche nach eingehenden Links muessen sie kennen. Nur das Template wird auf
# den echten Traeger umgebogen (AGENTS.md, Sonderrouten).
if [[ -z "${SLUG}" ]]; then
  ROUTE="/"
else
  ROUTE="/${SLUG}/"
fi

CARRIER_NOTE=""
CARRIER_SLUG="${SLUG}"
case "${SLUG}" in
  wordpress-agentur-hannover)
    CARRIER_NOTE="Schatten-Wrapper. Layout und Copy liegen in page-wordpress-agentur.php — dort bearbeiten, nie im Wrapper."
    CARRIER_SLUG="wordpress-agentur"
    ;;
  case-study-solar-leadgenerierung)
    CARRIER_NOTE="Alias-Wrapper page-case-e3.php bindet page-e3-new-energy.php ein. Geprueft wird der effektive Template-Traeger."
    CARRIER_SLUG="e3-new-energy"
    ;;
  ergebnisse)
    CARRIER_NOTE="Legacy-Dateiname aus der E-Commerce-Zeit, traegt heute den Ergebnisse-Hub."
    CARRIER_SLUG="case-studies-e-commerce"
    ;;
esac

if [[ -z "${CARRIER_SLUG}" ]]; then
  TEMPLATE="blocksy-child/front-page.php"
else
  TEMPLATE="blocksy-child/page-${CARRIER_SLUG}.php"
fi

if [[ ! -f "${TEMPLATE}" ]]; then
  printf 'Kein Template fuer %s gefunden (erwartet: %s).\n\n' "${RAW_ROUTE}" "${TEMPLATE}" >&2
  printf 'Moegliche Ursachen: Route liegt im WordPress-Editor statt im Repo,\n' >&2
  printf 'Slug weicht vom Dateinamen ab, oder die Route existiert nicht mehr.\n\n' >&2
  # Slug und Dateiname laufen im Repo mehrfach auseinander. Der Template-Name-
  # Header ist die verlaesslichere Spur als der Dateiname.
  STEM="$(printf '%s' "${SLUG}" | cut -d- -f1)"
  printf 'Templates mit passendem "Template Name"-Header:\n' >&2
  FOUND_ANY=0
  for f in blocksy-child/page-*.php; do
    TN="$({ rg -m1 -o 'Template Name: (.*)' -r '$1' "$f" 2>/dev/null || true; })"
    if [[ -n "${TN}" ]] && printf '%s' "${TN}" | rg -qi -- "${STEM}" 2>/dev/null; then
      printf '  %-45s %s\n' "${f}" "${TN}" >&2
      FOUND_ANY=1
    fi
  done
  [[ "${FOUND_ANY}" -eq 0 ]] && printf '  (keine)\n' >&2

  printf '\nPasst eines davon, gehoert der Slug in die Alias-Tabelle oben im Skript.\n' >&2
  exit 1
fi

# White-Label ist der dokumentierte Nebenpfad: dort sind WordPress, SEO,
# Tracking und CRO als Lieferfelder erlaubt (BRAND_AND_COPY.md).
IS_WHITELABEL=0
[[ "${SLUG}" == whitelabel-* ]] && IS_WHITELABEL=1

printf '\n########## Route-Review: %s ##########\n' "${ROUTE}"
printf 'Template: %s\n' "${TEMPLATE}"
[[ -n "${CARRIER_NOTE}" ]] && printf 'Hinweis:  %s\n' "${CARRIER_NOTE}"
[[ "${IS_WHITELABEL}" -eq 1 ]] && printf 'Regelsatz: White-Label-Nebenpfad — Lieferfeld-Ausnahmen aktiv.\n'

printf '\n== Routen-Eintrag in llms.txt ==\n'
rg -n "\(${ROUTE}\)" llms.txt 2>/dev/null \
  || printf 'Nicht in llms.txt. Beabsichtigt (Nebenpfad) oder vergessen?\n'

printf '\n== Ueberschriften-Folge ==\n'
rg -n '<h[1-6][^>]*>' "${TEMPLATE}" 2>/dev/null \
  | sed -E 's/<[^>]*>//g; s/[[:space:]]+/ /g' \
  | head -40 \
  || printf 'Keine statischen Ueberschriften — Copy vermutlich im Editor.\n'

printf '\n== CTAs (data-track-action) ==\n'
rg -n 'data-track-action="[^"]*"' -o "${TEMPLATE}" 2>/dev/null | head -30 \
  || printf 'Keine getrackten CTAs. Auf einer konvertierenden Route ist das ein Befund.\n'

printf '\n== CTA-Ziele ==\n'
rg -n 'hu_get_[a-z_]*_url|home_url\([^)]*\)|href="[^"]*"' -o "${TEMPLATE}" 2>/dev/null \
  | sort -u -t: -k2 | head -25 || true

printf '\n== Proof-Referenzen (E3-Canon) ==\n'
if rg -n 'hu_e3_|HU_E3_' "${TEMPLATE}" >/dev/null 2>&1; then
  rg -n 'hu_e3_[a-z_]*|HU_E3_[A-Z_]*' -o "${TEMPLATE}" | head -20
else
  printf 'Keine. Zahlen auf dieser Route stammen dann nicht aus dem Canon — pruefen.\n'
fi

printf '\n== Harte Zahlen im Template ==\n'
rg -n '[0-9]+([.,][0-9]+)?[[:space:]]*(%|€|EUR|Prozent|-fach)' "${TEMPLATE}" 2>/dev/null \
  | head -20 || printf 'Keine.\n'
printf -- '-> Jede Zahl muss in e3-proof-canon.php oder pricing-canon.php stehen.\n'

# Die Begriffslisten gehoeren dem Canon und der Brand-Doku. Hier werden sie
# gelesen, nie kopiert — eine Kopie driftet, und scripts/lint-canon-drift.sh
# schlaegt zu Recht an.
read_canon_terms() {
  { sed -n "/'forbidden_terms'/,/]/p" blocksy-child/inc/canon/messaging-canon.php 2>/dev/null || true; } \
    | { rg -o "^[[:space:]]*'([^']+)'," -r '$1' 2>/dev/null || true; }
}

read_brand_bans() {
  { sed -n '/^## Anti-Patterns/,/^## [^A]/p' docs/standards/BRAND_AND_COPY.md 2>/dev/null || true; } \
    | { rg -o '`([^`]+)`' -r '$1' 2>/dev/null || true; }
}

# Zu unspezifisch fuer einen Grep: diese Woerter haben legitime Verwendungen
# ("Test-Sprint", "Leistungen" als White-Label-Navigation). Sie kommen in die
# Pruef-Spalte statt auf Rot.
AMBIGUOUS='^(Pilot|Beta|Test|Modul|Leistungen|Webdesign|WordPress Specialist|WordPress-Agentur|kostenlos)$'

# Erlaubt in dokumentierter Rolle und zu haeufig, um sie jeden Lauf einzeln zu
# pruefen: nur zaehlen. Wer sie inhaltlich pruefen will, nimmt den Fachskill.
FREQUENT='^(WordPress|B2B)$'

join_alt() { paste -sd'|' - 2>/dev/null || true; }

ALL_TERMS="$( { read_canon_terms; read_brand_bans; } | sort -u)"
BAN_PATTERN="$(printf '%s\n' "${ALL_TERMS}" | rg -v "${AMBIGUOUS}" | rg -v "${FREQUENT}" | join_alt)"
SOFT_PATTERN="$(printf '%s\n' "${ALL_TERMS}" | rg "${AMBIGUOUS}" | join_alt)"
FREQ_PATTERN="$(printf '%s\n' "${ALL_TERMS}" | rg "${FREQUENT}" | join_alt)"

if [[ -z "${BAN_PATTERN}" ]]; then
  printf '\nWarnung: keine Ban-Begriffe aus Canon/Brand-Doku gelesen.\n' >&2
  BAN_PATTERN='__kein_treffer__'
fi

# Ein gesperrter Begriff in einer Abgrenzung ist Positionierung, kein Verstoss:
# "Architektur statt Webdesign" behauptet das Gegenteil von dem, was der Ban
# verhindern soll. Solche Zeilen nicht rot faerben, sondern getrennt ausgeben.
NEG_PATTERN='\b([Kk]eine?[nmrs]?|[Nn]icht|statt|anstatt|weder|niemals)\b'

BAN_HITS="$(rg -n "${BAN_PATTERN}" "${TEMPLATE}" 2>/dev/null || true)"
BAN_HARD="$(printf '%s' "${BAN_HITS}" | rg -v "${NEG_PATTERN}" 2>/dev/null || true)"
BAN_CONTRAST="$(printf '%s' "${BAN_HITS}" | rg "${NEG_PATTERN}" 2>/dev/null || true)"

printf '\n== Hard Bans (BRAND_AND_COPY.md) ==\n'
if [[ -n "${BAN_HARD}" ]]; then
  printf '%s\n' "${BAN_HARD}" | head -20
  printf -- '-> Interne IDs/URLs duerfen die Begriffe tragen. Sichtbare Copy nicht.\n'
else
  printf 'Keine Treffer.\n'
fi

if [[ -n "${BAN_CONTRAST}" ]]; then
  printf '\n== Gesperrte Begriffe in Abgrenzung (kein Verstoss) ==\n'
  printf '%s\n' "${BAN_CONTRAST}" | head -12
  printf -- '-> Begriff wird verneint oder abgegrenzt. Nur pruefen, ob die\n'
  printf -- '   Abgrenzung noch stimmt — nicht automatisch entfernen.\n'
fi

if [[ -n "${SOFT_PATTERN}" ]]; then
  # Auch hier zaehlt die Abgrenzung als unverdaechtig: "kein Webdesign-Projekt"
  # ist der Gegenbeweis, nicht der Verstoss.
  SOFT_HITS="$( { rg -n "${SOFT_PATTERN}" "${TEMPLATE}" 2>/dev/null || true; } | { rg -v "${NEG_PATTERN}" 2>/dev/null || true; } )"
  if [[ -n "${SOFT_HITS}" ]]; then
    printf '\n== Mehrdeutige Begriffe (manuell entscheiden) ==\n'
    printf '%s\n' "${SOFT_HITS}" | head -15
    printf -- '-> Kein automatischer Verstoss. Die Frage ist jeweils die Rolle:\n'
    printf -- '   Zielgruppe oder Lieferfeld nennen ist erlaubt, sich selbst so\n'
    printf -- '   zu positionieren nicht. Auf /whitelabel-* gelten die Ausnahmen\n'
    printf -- '   aus BRAND_AND_COPY.md.\n'
  fi
fi

if [[ -n "${FREQ_PATTERN}" ]]; then
  FREQ_N="$( { rg -o "${FREQ_PATTERN}" "${TEMPLATE}" 2>/dev/null || true; } | rg -c '' 2>/dev/null || echo 0)"
  if [[ "${FREQ_N}" -gt 0 ]]; then
    printf '\n== Haeufige Begriffe in erlaubter Rolle ==\n'
    printf '%s: %s Vorkommen. Erlaubt als Technologie bzw. Beschreibung,\n' "${FREQ_PATTERN//|/, }" "${FREQ_N}"
    printf 'nicht als Positionierung oder Rollen-Claim. Nicht Zeile fuer Zeile\n'
    printf 'pruefen — wenn der Verdacht besteht, uebernimmt offer-funnel-intelligence.\n'
  fi
fi

printf '\n== Interne Links auf diese Route ==\n'
INBOUND="$(rg -l -- "${ROUTE}" blocksy-child --glob '*.php' 2>/dev/null | rg -v "$(basename "${TEMPLATE}")" || true)"
if [[ -n "${INBOUND}" ]]; then
  printf '%s\n' "${INBOUND}"
else
  printf 'Keine. Orphan-Verdacht — gegen internal-linking-audit pruefen.\n'
fi

# ---- Ampel ----------------------------------------------------------------

verdict() {
  local zone="$1" state="$2" note="$3"
  printf '  [%-4s] %-22s %s\n' "${state}" "${zone}" "${note}"
}

# rg beendet sich mit 1, wenn es nichts findet. Unter pipefail wuerde das die
# Zuweisung und damit das Skript abbrechen — also den Fehler hier abfangen.
count_hits() {
  { rg -o "$1" "${TEMPLATE}" 2>/dev/null || true; } | rg -c '' 2>/dev/null || echo 0
}

CTA_COUNT="$(count_hits 'data-track-action="[^"]*"')"
H1_COUNT="$(count_hits '<h1')"
PROOF_COUNT="$(count_hits 'hu_e3_[a-z_]*|HU_E3_[A-Z_]*')"

BAN_COUNT=0
[[ -n "${BAN_HARD}" ]] && BAN_COUNT="$(printf '%s\n' "${BAN_HARD}" | rg -c '' || echo 0)"

printf '\n== Ampel ==\n'

if [[ "${H1_COUNT}" -eq 1 ]]; then
  verdict "H1" "OK" "genau eine H1"
elif [[ "${H1_COUNT}" -eq 0 ]]; then
  verdict "H1" "GELB" "keine statische H1 — liegt sie im Editor?"
else
  verdict "H1" "ROT" "${H1_COUNT} H1-Elemente"
fi

if [[ "${CTA_COUNT}" -eq 0 ]]; then
  verdict "CTA-Tracking" "ROT" "kein data-track-action im Template"
else
  verdict "CTA-Tracking" "OK" "${CTA_COUNT} getrackte CTAs"
fi

if [[ "${BAN_COUNT}" -gt 0 ]]; then
  verdict "Hard Bans" "ROT" "${BAN_COUNT} Zeilen mit gesperrten Begriffen"
else
  verdict "Hard Bans" "OK" "sauber"
fi

if [[ "${PROOF_COUNT}" -eq 0 ]]; then
  verdict "Proof-Canon" "GELB" "keine Canon-Referenz — Zahlen pruefen"
else
  verdict "Proof-Canon" "OK" "${PROOF_COUNT} Canon-Referenzen"
fi

if [[ -z "${INBOUND}" ]]; then
  verdict "Interne Links" "ROT" "keine eingehenden Links"
else
  verdict "Interne Links" "OK" "$(printf '%s\n' "${INBOUND}" | wc -l | tr -d ' ') Quellen"
fi

cat <<'EOF'

ROT ist ein P0-Kandidat, nicht automatisch ein Fehler — die Route kann
Copy im WordPress-Editor halten. Vor dem Urteil dort nachsehen.

Weiter mit der Linsen-Reihenfolge aus SKILL.md:
Angebot -> CRO -> Copy -> SEO -> Interne Links -> Speed
EOF
