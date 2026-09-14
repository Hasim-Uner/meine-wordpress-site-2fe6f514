#!/bin/sh

cat <<'EOF'
Growth Audit Optimizer
======================

Primary objective
- Improve the active Solar/SHK marketcheck so more qualified visitors start, trust the result, and take the next step.
- Treat /growth-audit/ and the former Audit UI as retired legacy unless the task explicitly asks for redirect or archaeology work.

Active assumptions
- /solar-waermepumpen-leadgenerierung/#marktcheck is the live truth.
- Legacy Audit routes redirect through blocksy-child/inc/system-diagnose-page.php.
- Public timing copy should use the current marketcheck wording, not historical 48h/Instant-Audit language.
- Shopify is not active positioning and should only be treated as stale copy debt.

Check first
- blocksy-child/page-solar-waermepumpen-leadgenerierung.php
- blocksy-child/assets/js/solar-leadgenerierung-solara.js
- blocksy-child/inc/system-diagnose-page.php
- blocksy-child/inc/review-crm.php
- blocksy-child/inc/analysis-intake.php
- blocksy-child/inc/helpers.php
- blocksy-child/inc/seo-meta.php
- blocksy-child/front-page.php
- docs/systems/audit-funnel.md

Do not resurrect
- page-audit.php or an Audit-specific template fallback
- cja_audit shortcode/UI
- Audit-only site header assets
- editor HTML as functional source of truth

Look for
- historical 30 Sekunden or 48h carryover on the active route
- CRM-internal framing leaking into public copy
- Shopify mentions in audit-specific copy
- weak hero, trust, or result CTA transitions
- redirect drift away from the canonical marketcheck target

Copy target
- Fast, credible, strategic diagnosis entry point for Solar-, Wärmepumpen- und Speicher-Anbieter
EOF
