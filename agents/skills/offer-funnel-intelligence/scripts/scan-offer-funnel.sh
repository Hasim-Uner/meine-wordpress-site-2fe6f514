#!/usr/bin/env bash
set -euo pipefail

ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "$ROOT"

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

printf '\n== Suggested next read order ==\n'
printf '1. docs/standards/BRAND_AND_COPY.md\n'
printf '2. llms.txt\n'
printf '3. blocksy-child/front-page.php or task-specific page template\n'
printf '4. relevant canonical helper under blocksy-child/inc/canon/\n'
printf '5. only then CSS/JS if the issue is interaction or visual hierarchy\n'
