#!/usr/bin/env bash
# Offer/funnel evidence scan.
#
#   scan-offer-funnel.sh                  summary (hits per file) plus traffic light
#   scan-offer-funnel.sh --lines PATH...  matching lines for those paths only
#
# The default stays a summary on purpose: line-level output over the whole theme
# exceeds every host's tool-output window, and the verdict was lost with it.
set -euo pipefail

ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "$ROOT"

SOURCE_PATTERN="Marktcheck|Anfrage-System|Anfrage-System-Analyse|Founding|Portal-Abhängigkeit|Leadkosten|Kosten pro Anfrage|qualifizierte Anfragen|Abschlussquote|Vorqualifizierung|E3|Retainer|WGOS|WordPress Growth Operating System|Growth Audit|Shopify|Antwortzeit|kostenlos|Projekt prüfen|Auswertung anfordern|Kontakt"
CTA_PATTERN="data-track-action=|hu-btn|nx-btn|cta_|Marktcheck|Kontakt|prüfen|anfordern|ansehen|starten"
RISK_PATTERN="WGOS|WordPress Growth Operating System|Growth Audit|Shopify|KI-Integration|Webdesign|WordPress-Agentur|WordPress Agentur|B2B-Generalist|Performance-Marketing-Agentur"
LINE_WIDTH=240
SUMMARY_LIMIT=15

if [[ "${1:-}" == "--lines" ]]; then
  shift
  if [[ "$#" -eq 0 ]]; then
    echo 'usage: scan-offer-funnel.sh --lines PATH...' >&2
    exit 64
  fi
  printf '\n== Offer/Funnel source map (lines) ==\n'
  rg -n --max-columns "$LINE_WIDTH" --max-columns-preview "$SOURCE_PATTERN" "$@" || true
  printf '\n== CTA inventory (lines) ==\n'
  rg -n --max-columns "$LINE_WIDTH" --max-columns-preview "$CTA_PATTERN" "$@" || true
  exit 0
fi
if [[ "$#" -gt 0 ]]; then
  echo 'usage: scan-offer-funnel.sh [--lines PATH...]' >&2
  exit 64
fi

# Hits per file, strongest first; the tail is counted instead of printed.
summarize() {
  local pattern="$1"
  shift
  { rg -c --no-messages "$pattern" "$@" || true; } \
    | sort -t: -k2,2nr -k1,1 \
    | awk -F: -v limit="$SUMMARY_LIMIT" '
        NR <= limit { printf "  %5d  %s\n", $2, $1 }
        END { if (NR > limit) printf "  ... %d more files\n", NR - limit
              if (NR == 0) print "  (none)" }'
}

# Energy acquisition routes come from llms.txt: the page that owns the
# Marktcheck anchor plus the energy cluster section. Never keep a copy here.
energy_routes() {
  [[ -f llms.txt ]] || return 0
  {
    rg -o --no-messages '\]\((/[^)#?]*)#marktcheck\)' -r '$1' llms.txt || true
    awk '/^## /{ on = (index($0, "## Solar- und Wärmepumpen-Cluster") == 1) } on' llms.txt \
      | { rg -o '\]\((/[^)#?]+)' -r '$1' || true; }
  } | awk '!seen[$0]++'
}

EXISTING_COLD_ROUTES=()
UNRESOLVED_ROUTES=()
[[ -f blocksy-child/front-page.php ]] && EXISTING_COLD_ROUTES+=(blocksy-child/front-page.php)
while IFS= read -r route; do
  slug="${route#/}"
  slug="${slug%/}"
  [[ -n "$slug" ]] || continue
  if [[ -f "blocksy-child/page-${slug}.php" ]]; then
    EXISTING_COLD_ROUTES+=("blocksy-child/page-${slug}.php")
  else
    UNRESOLVED_ROUTES+=("$route")
  fi
done < <(energy_routes)

printf '\n== Offer/Funnel source map (hits per file) ==\n'
summarize "$SOURCE_PATTERN" \
  AGENTS.md \
  llms.txt \
  docs/standards/BRAND_AND_COPY.md \
  blocksy-child/front-page.php \
  blocksy-child/page-*.php \
  blocksy-child/template-parts \
  blocksy-child/inc

printf '\n== CTA inventory (hits per file) ==\n'
summarize "$CTA_PATTERN" \
  blocksy-child/front-page.php \
  blocksy-child/page-*.php \
  blocksy-child/template-parts \
  blocksy-child/inc

printf '\nLine detail for one route or partial: scan-offer-funnel.sh --lines <path>...\n'

printf '\n== Public-positioning risk terms ==\n'
rg -n --max-columns "$LINE_WIDTH" --max-columns-preview \
  "$RISK_PATTERN" \
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

# 0. The energy list must not vanish silently when llms.txt is restructured.
if (( ${#EXISTING_COLD_ROUTES[@]} + ${#UNRESOLVED_ROUTES[@]} <= 1 )); then
  amber+=("No energy routes read from llms.txt — check the Marktcheck anchor and the cluster heading before trusting this verdict")
fi

# 1. WGOS in cold-route hero copy
wgos_cold=0
if [[ "${#EXISTING_COLD_ROUTES[@]}" -gt 0 ]]; then
  wgos_cold=$(count_hits "WGOS|WordPress Growth Operating System" "${EXISTING_COLD_ROUTES[@]}")
fi
if (( wgos_cold > 0 )); then
  red+=("WGOS on $wgos_cold cold acquisition route(s) — remove the retired framework entirely")
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

# 5. "Free" framing as headline value.
# Der frühere Fristen-Detektor ist entfallen: retired response-time literals
# werden seit 2026-09 repo-weit von scripts/canon-guard.sh geblockt, ein
# zweiter Suchbegriff dafür wäre nur eine weitere Kopie der Sperrliste.
free_hook=0
if [[ "${#EXISTING_COLD_ROUTES[@]}" -gt 0 ]]; then
  free_hook=$(count_hits "kostenlos" "${EXISTING_COLD_ROUTES[@]}")
fi
if (( free_hook > 0 )); then
  amber+=("'kostenlos' appears on $free_hook cold route(s) — verify it is decision value, not the main hook")
else
  green+=("No 'free' framing as headline value on cold routes")
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
if [[ "${#UNRESOLVED_ROUTES[@]}" -gt 0 ]]; then
  printf 'Not inspected (no page-<slug>.php; editor-owned or carried by another template):\n'
  for route in "${UNRESOLVED_ROUTES[@]}"; do
    printf '  - %s\n' "$route"
  done
fi

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
