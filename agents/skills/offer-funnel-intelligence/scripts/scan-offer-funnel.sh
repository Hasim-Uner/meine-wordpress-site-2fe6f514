#!/usr/bin/env bash
set -euo pipefail

ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "$ROOT"

COLD_ROUTES=(
  blocksy-child/front-page.php
  blocksy-child/page-b2b-solar-leads.php
  blocksy-child/page-kunden-gewinnen-solarteure.php
  blocksy-child/page-lead-funnel-solar.php
  blocksy-child/page-cost-per-lead-photovoltaik.php
  blocksy-child/page-eigene-leadgenerierung-vs-portale.php
)

EXISTING_COLD_ROUTES=()
for f in "${COLD_ROUTES[@]}"; do
  if [[ -f "$f" ]]; then
    EXISTING_COLD_ROUTES+=("$f")
  fi
done

printf '\n== Offer/Funnel source map ==\n'
rg -n \
  "Marktcheck|Anfrage-System|Anfrage-System-Analyse|Founding|Portal-Abhängigkeit|Leadkosten|Kosten pro Anfrage|qualifizierte Anfragen|Abschlussquote|Vorqualifizierung|E3|Retainer|WGOS|WordPress Growth Operating System|Growth Audit|Shopify|48 Stunden|kostenlos|Projekt prüfen|Auswertung anfordern|Kontakt" \
  AGENTS.md \
  llms.txt \
  docs/standards/BRAND_AND_COPY.md \
  blocksy-child/front-page.php \
  blocksy-child/page-*.php \
  blocksy-child/template-parts \
  blocksy-child/inc \
  2>/dev/null || true

printf '\n== CTA inventory ==\n'
rg -n \
  "data-track-action=|hu-btn|nx-btn|cta_|Marktcheck|Kontakt|prüfen|anfordern|ansehen|starten" \
  blocksy-child/front-page.php \
  blocksy-child/page-*.php \
  blocksy-child/template-parts \
  blocksy-child/inc \
  2>/dev/null || true

printf '\n== Public-positioning risk terms ==\n'
rg -n \
  "WGOS|WordPress Growth Operating System|Growth Audit|Shopify|KI-Integration|Webdesign|WordPress-Agentur|WordPress Agentur|B2B-Generalist|Performance-Marketing-Agentur" \
  blocksy-child/front-page.php \
  blocksy-child/page-*.php \
  blocksy-child/template-parts \
  llms.txt \
  docs/standards/BRAND_AND_COPY.md \
  2>/dev/null || true

# ---------- Traffic light ----------

count_hits() {
  local pattern="$1"
  shift
  if [[ "$#" -eq 0 ]]; then
    echo 0
    return
  fi
  local out
  out="$(rg -l --no-messages "$pattern" "$@" 2>/dev/null || true)"
  if [[ -z "$out" ]]; then
    echo 0
  else
    printf '%s\n' "$out" | wc -l | tr -d ' '
  fi
}

red=()
amber=()
green=()

# 1. WGOS in cold-route hero copy
wgos_cold=0
if [[ "${#EXISTING_COLD_ROUTES[@]}" -gt 0 ]]; then
  wgos_cold=$(count_hits "WGOS|WordPress Growth Operating System" "${EXISTING_COLD_ROUTES[@]}")
fi
if (( wgos_cold > 0 )); then
  red+=("WGOS on $wgos_cold cold acquisition route(s) — move to delivery/proposal language only")
else
  green+=("WGOS boundary held on cold acquisition routes")
fi

# 2. Legacy positioning
legacy=0
if [[ "${#EXISTING_COLD_ROUTES[@]}" -gt 0 ]]; then
  legacy=$(count_hits "Shopify|Growth Audit|Performance-Marketing-Agentur|B2B-Generalist" "${EXISTING_COLD_ROUTES[@]}")
fi
if (( legacy > 0 )); then
  red+=("Legacy positioning ($legacy file[s]) — retire Shopify/Growth-Audit/Generalist framing")
else
  green+=("No legacy Shopify/Growth-Audit positioning on cold routes")
fi

# 3. Generic agency framing as primary
generic=0
if [[ "${#EXISTING_COLD_ROUTES[@]}" -gt 0 ]]; then
  generic=$(count_hits "Webdesign|WordPress-Agentur|WordPress Agentur" "${EXISTING_COLD_ROUTES[@]}")
fi
if (( generic > 0 )); then
  amber+=("Generic agency wording on $generic cold route(s) — verify it is not in hero/primary CTA")
else
  green+=("No generic agency wording on cold routes")
fi

# 4. Marktcheck presence
marktcheck=0
if [[ "${#EXISTING_COLD_ROUTES[@]}" -gt 0 ]]; then
  marktcheck=$(count_hits "Marktcheck" "${EXISTING_COLD_ROUTES[@]}")
fi
if (( marktcheck == 0 )); then
  red+=("Marktcheck not surfaced on any cold acquisition route — funnel entry is missing")
elif (( marktcheck < ${#EXISTING_COLD_ROUTES[@]} )); then
  amber+=("Marktcheck present on $marktcheck/${#EXISTING_COLD_ROUTES[@]} cold routes — verify ladder is consistent")
else
  green+=("Marktcheck surfaced on every cold acquisition route")
fi

# 5. Free / 48h framing as headline value
free48=0
if [[ "${#EXISTING_COLD_ROUTES[@]}" -gt 0 ]]; then
  free48=$(count_hits "48 Stunden|kostenlos" "${EXISTING_COLD_ROUTES[@]}")
fi
if (( free48 > 0 )); then
  amber+=("'kostenlos' or '48 Stunden' appears on $free48 cold route(s) — verify it is decision value, not the main hook")
else
  green+=("No 'free/48h' as headline value on cold routes")
fi

# 6. Tracking hooks on CTAs
tracking=0
if [[ "${#EXISTING_COLD_ROUTES[@]}" -gt 0 ]]; then
  tracking=$(count_hits "data-track-action=" "${EXISTING_COLD_ROUTES[@]}")
fi
if (( tracking < ${#EXISTING_COLD_ROUTES[@]} )); then
  amber+=("Tracking hooks present on only $tracking/${#EXISTING_COLD_ROUTES[@]} cold routes — qualification economics will be invisible")
else
  green+=("Tracking hooks present on all cold acquisition routes")
fi

# 7. E3 proof referenced on cold routes
e3=0
if [[ "${#EXISTING_COLD_ROUTES[@]}" -gt 0 ]]; then
  e3=$(count_hits "E3" "${EXISTING_COLD_ROUTES[@]}")
fi
if (( e3 == 0 )); then
  amber+=("E3 proof not referenced on cold routes — proof not placed before the ask")
else
  green+=("E3 proof referenced on $e3 cold route(s)")
fi

printf '\n== Traffic light verdict (cold acquisition routes) ==\n'
printf 'Cold routes inspected: %d\n' "${#EXISTING_COLD_ROUTES[@]}"
for f in "${EXISTING_COLD_ROUTES[@]}"; do
  printf '  - %s\n' "$f"
done

printf '\nRED (P0):\n'
if [[ "${#red[@]}" -eq 0 ]]; then
  printf '  (none)\n'
else
  for m in "${red[@]}"; do printf '  - %s\n' "$m"; done
fi

printf '\nAMBER (P1):\n'
if [[ "${#amber[@]}" -eq 0 ]]; then
  printf '  (none)\n'
else
  for m in "${amber[@]}"; do printf '  - %s\n' "$m"; done
fi

printf '\nGREEN:\n'
if [[ "${#green[@]}" -eq 0 ]]; then
  printf '  (none)\n'
else
  for m in "${green[@]}"; do printf '  - %s\n' "$m"; done
fi

printf '\n== Suggested next read order ==\n'
printf '1. docs/standards/BRAND_AND_COPY.md\n'
printf '2. llms.txt\n'
printf '3. blocksy-child/front-page.php or task-specific page template\n'
printf '4. relevant canonical helper under blocksy-child/inc/canon/\n'
printf '5. references/scoring-rubric.md (score before writing the report)\n'
printf '6. only then CSS/JS if the issue is interaction or visual hierarchy\n'

# Exit non-zero if any RED so CI/agents can treat it as a blocker
if [[ "${#red[@]}" -gt 0 ]]; then
  exit 2
fi
