#!/usr/bin/env bash
#
# Live-Smoke: nach dem Deploy die wichtigsten Seiten abrufen und Vitalzeichen
# pruefen. Kein Test des Aussehens, kein Test des Formulars — nur die Frage,
# ob der Patient atmet.
#
# Geprueft wird je Route:
#   - HTTP 200
#   - <title> vorhanden und nicht leer
#   - genau eine <h1>
#   - KEIN noindex im robots-Meta
#   - <link rel="canonical"> vorhanden und absolut
#
# Die Routen laufen parallel. So blockiert ein langsamer oder vom GitHub-Runner
# schlecht erreichbarer Host nicht Route fuer Route den gesamten Deploy.
# Ein kurzer Retry faengt transiente Netzwerkfehler ab, ohne den Job minutenlang
# festzuhalten.
#
# Nutzung:
#   smoke-live.sh                             gegen https://hasimuener.de
#   smoke-live.sh https://staging.example.de  gegen eine andere Basis
#   ROUTES="/ /kontakt/" smoke-live.sh        eigene Routenliste
#
# Optionale Tuning-Variablen:
#   SMOKE_CONNECT_TIMEOUT=4  TCP/TLS-Verbindungsaufbau in Sekunden
#   SMOKE_MAX_TIME=8         maximale Dauer pro Curl-Versuch
#   SMOKE_RETRIES=1          zusaetzliche Versuche bei transienten Fehlern
#   SMOKE_RETRY_MAX_TIME=18  Obergrenze inkl. Retry pro Route
#
# Exit 0 = alle Routen gesund, 1 = mindestens ein Befund, 2 = Aufrufproblem.

set -uo pipefail

BASE="${1:-https://hasimuener.de}"
BASE="${BASE%/}"
CONNECT_TIMEOUT="${SMOKE_CONNECT_TIMEOUT:-4}"
MAX_TIME="${SMOKE_MAX_TIME:-8}"
RETRIES="${SMOKE_RETRIES:-1}"
RETRY_MAX_TIME="${SMOKE_RETRY_MAX_TIME:-18}"

# Die Money-Pages und der Anfragepfad. Bewusst kurz: ein Smoke prueft die
# Seiten, deren Ausfall wehtut, nicht den ganzen Bestand.
DEFAULT_ROUTES="/ /solar-waermepumpen-leadgenerierung/ /b2b-solar-leads/ /wordpress-agentur-hannover/ /server-side-tracking-b2b/ /checkfox-solar-waermepumpe-einordnung/ /aroundhome-solar-einordnung/ /kontakt/"
read -r -a ROUTE_LIST <<< "${ROUTES:-$DEFAULT_ROUTES}"

command -v curl >/dev/null 2>&1 || { echo "FEHLER: curl fehlt." >&2; exit 2; }

TMP_DIR="$(mktemp -d)" || exit 2
trap 'rm -rf "$TMP_DIR"' EXIT

check_route() {
  local route="$1"
  local index="$2"
  local url="${BASE}${route}"
  local body rc status html title h1_count
  local -a problems=()
  local result_file="$TMP_DIR/${index}.result"
  local fail_file="$TMP_DIR/${index}.fail"
  local transport_file="$TMP_DIR/${index}.transport"

  body="$(curl -sSL \
    --connect-timeout "$CONNECT_TIMEOUT" \
    --max-time "$MAX_TIME" \
    --retry "$RETRIES" \
    --retry-delay 1 \
    --retry-max-time "$RETRY_MAX_TIME" \
    --retry-all-errors \
    -w '\n__STATUS__%{http_code}' \
    "$url" 2>/dev/null)"
  rc=$?
  status="$(printf '%s' "$body" | tail -1 | sed 's/^__STATUS__//')"
  html="$(printf '%s' "$body" | sed '$d')"
  title=""

  if [[ $rc -ne 0 ]]; then
    problems+=("nicht erreichbar (curl-Fehler $rc)")
    status="—"
    : > "$transport_file"
  else
    [[ "$status" == "200" ]] || problems+=("HTTP $status statt 200")
  fi

  if [[ $rc -eq 0 && "$status" == "200" ]]; then
    # Herestring statt Pipe: ein frueh beendetes grep/head darf unter pipefail
    # keinen falschen Fehler durch SIGPIPE erzeugen.
    title="$(grep -oiE '<title[^>]*>[^<]*</title>' <<< "${html//$'\n'/ }" | head -1 \
             | sed -E 's/<[^>]*>//g' | sed 's/^ *//;s/ *$//')"
    [[ -n "$title" ]] || problems+=("kein oder leerer <title>")

    h1_count="$(grep -oiE '<h1[ >]' <<< "$html" | wc -l | tr -d ' ')"
    [[ "$h1_count" == "1" ]] || problems+=("$h1_count H1-Elemente statt genau einer")

    if grep -qiE '<meta[^>]+name=["'"'"']robots["'"'"'][^>]*content=["'"'"'][^"'"'"']*noindex' <<< "${html//$'\n'/ }"; then
      problems+=("NOINDEX gesetzt")
    fi

    if ! grep -qiE '<link[^>]+rel=["'"'"']canonical["'"'"'][^>]*href=["'"'"']https?://' <<< "$html"; then
      problems+=("kein absolutes canonical")
    fi
  fi

  if [[ ${#problems[@]} -eq 0 ]]; then
    printf '  ok    %-46s %s\n' "$route" "${title:0:40}" > "$result_file"
    return 0
  fi

  : > "$fail_file"
  {
    printf '  FEHL  %-46s HTTP %s\n' "$route" "$status"
    for problem in "${problems[@]}"; do
      printf '          - %s\n' "$problem"
    done
  } > "$result_file"
}

printf '\n########## Live-Smoke: %s ##########\n' "$BASE"
printf 'Parallel · connect=%ss · attempt=%ss · retry=%s · cap=%ss\n\n' \
  "$CONNECT_TIMEOUT" "$MAX_TIME" "$RETRIES" "$RETRY_MAX_TIME"

# Acht typische GETs sind fuer WordPress/Hosting unkritisch, reduzieren aber den
# Worst Case bei Runner-/WAF-Problemen von mehreren Minuten auf rund 20 Sekunden.
for index in "${!ROUTE_LIST[@]}"; do
  check_route "${ROUTE_LIST[$index]}" "$index" &
done
wait

FAILED=0
TRANSPORT_FAILED=0
CHECKED=${#ROUTE_LIST[@]}
for index in "${!ROUTE_LIST[@]}"; do
  cat "$TMP_DIR/${index}.result"
  [[ -f "$TMP_DIR/${index}.fail" ]] && FAILED=$((FAILED + 1))
  [[ -f "$TMP_DIR/${index}.transport" ]] && TRANSPORT_FAILED=$((TRANSPORT_FAILED + 1))
done

printf '\n%s Routen geprueft, %s mit Befund.\n' "$CHECKED" "$FAILED"

if [[ $FAILED -gt 0 ]]; then
  if [[ $TRANSPORT_FAILED -eq $CHECKED ]]; then
    cat <<'EOF'

Alle Routen sind bereits auf Transportebene fehlgeschlagen. Das spricht eher
fuer Runner-, Netzwerk-, Firewall- oder WAF-Erreichbarkeit als fuer acht
unabhaengige WordPress-Fehler. Der Deploy bleibt trotzdem rot, damit ein echter
Totalausfall nicht still durchrutscht.
EOF
    exit 2
  fi

  cat <<'EOF'

Der Smoke prueft nur Vitalzeichen — Aussehen, Layout und Formular sind nicht
dabei. Ein Befund heisst: nachsehen, bevor die naechste Aenderung draufgeht.
Zurueckgerollt wird von Hand (git revert), das kann dieser Lauf nicht.
EOF
  exit 1
fi

echo "Alle geprueften Routen antworten gesund."
