#!/usr/bin/env bash
#
# Live-Smoke: nach dem Deploy die wichtigsten Seiten abrufen und Vitalzeichen
# pruefen. Kein Test des Aussehens, kein Test des Formulars — nur die Frage,
# ob der Patient atmet.
#
# Warum das noetig ist: deploy.yml schiebt Dateien auf den Server und hoert auf.
# Zerlegt ein Deploy eine Seite, setzt ein noindex oder leert den Title, meldet
# das niemand. Bei kleinen Besucherzahlen faellt es auch an den Zahlen nicht
# auf — ein toter Monat sieht aus wie ein ruhiger.
#
# Geprueft wird je Route:
#   - HTTP 200
#   - <title> vorhanden und nicht leer
#   - genau eine <h1>
#   - KEIN noindex im robots-Meta
#   - <link rel="canonical"> vorhanden und absolut
#
# Nutzung:
#   smoke-live.sh                             gegen https://hasimuener.de
#   smoke-live.sh https://staging.example.de  gegen eine andere Basis
#   ROUTES="/ /kontakt/" smoke-live.sh        eigene Routenliste
#
# Exit 0 = alle Routen gesund, 1 = mindestens ein Befund, 2 = Aufrufproblem.

set -uo pipefail

BASE="${1:-https://hasimuener.de}"
BASE="${BASE%/}"

# Die Money-Pages und der Anfragepfad. Bewusst kurz: ein Smoke prueft die
# Seiten, deren Ausfall wehtut, nicht den ganzen Bestand.
DEFAULT_ROUTES="/ /solar-waermepumpen-leadgenerierung/ /b2b-solar-leads/ /wordpress-agentur-hannover/ /server-side-tracking-b2b/ /checkfox-solar-waermepumpe-einordnung/ /kontakt/"
read -r -a ROUTE_LIST <<< "${ROUTES:-$DEFAULT_ROUTES}"

command -v curl >/dev/null 2>&1 || { echo "FEHLER: curl fehlt." >&2; exit 2; }

FAILED=0
CHECKED=0

printf '\n########## Live-Smoke: %s ##########\n\n' "$BASE"

for route in "${ROUTE_LIST[@]}"; do
  url="${BASE}${route}"
  problems=()

  # -L folgt Redirects: eine umgeleitete Route ist in Ordnung, solange am Ende
  # eine gesunde Seite steht. --max-time verhindert, dass ein haengender Server
  # den ganzen Lauf blockiert.
  body="$(curl -sSL --max-time 25 -w '\n__STATUS__%{http_code}' "$url" 2>/dev/null)"
  rc=$?
  status="$(printf '%s' "$body" | tail -1 | sed 's/^__STATUS__//')"
  html="$(printf '%s' "$body" | sed '$d')"

  if [[ $rc -ne 0 ]]; then
    problems+=("nicht erreichbar (curl-Fehler $rc)")
    status="—"
  else
    [[ "$status" == "200" ]] || problems+=("HTTP $status statt 200")
  fi

  if [[ $rc -eq 0 && "$status" == "200" ]]; then
    title="$(printf '%s' "$html" | tr '\n' ' ' | grep -oiE '<title[^>]*>[^<]*</title>' | head -1 \
             | sed -E 's/<[^>]*>//g' | sed 's/^ *//;s/ *$//')"
    [[ -n "$title" ]] || problems+=("kein oder leerer <title>")

    h1_count="$(printf '%s' "$html" | grep -oiE '<h1[ >]' | wc -l | tr -d ' ')"
    [[ "$h1_count" == "1" ]] || problems+=("$h1_count H1-Elemente statt genau einer")

    # Der teuerste Einzelfehler: ein verirrtes noindex nimmt die Seite aus dem
    # Index, und zurueck dauert Wochen.
    if printf '%s' "$html" | tr '\n' ' ' | grep -qiE '<meta[^>]+name=["'"'"']robots["'"'"'][^>]*content=["'"'"'][^"'"'"']*noindex'; then
      problems+=("NOINDEX gesetzt")
    fi

    printf '%s' "$html" | grep -qiE '<link[^>]+rel=["'"'"']canonical["'"'"'][^>]*href=["'"'"']https?://' \
      || problems+=("kein absolutes canonical")
  fi

  CHECKED=$((CHECKED + 1))
  if [[ ${#problems[@]} -eq 0 ]]; then
    printf '  ok    %-46s %s\n' "$route" "${title:0:40}"
  else
    FAILED=$((FAILED + 1))
    printf '  FEHL  %-46s HTTP %s\n' "$route" "$status"
    for p in "${problems[@]}"; do printf '          - %s\n' "$p"; done
  fi
done

printf '\n%s Routen geprueft, %s mit Befund.\n' "$CHECKED" "$FAILED"

if [[ $FAILED -gt 0 ]]; then
  cat <<'EOF'

Der Smoke prueft nur Vitalzeichen — Aussehen, Layout und Formular sind nicht
dabei. Ein Befund heisst: nachsehen, bevor die naechste Aenderung draufgeht.
Zurueckgerollt wird von Hand (git revert), das kann dieser Lauf nicht.
EOF
  exit 1
fi

echo "Alle geprueften Routen antworten gesund."
