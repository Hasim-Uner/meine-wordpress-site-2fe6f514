#!/bin/sh

cat <<'EOF'
Growth Audit Optimizer
======================

Primary objective
- Improve the active Solar/SHK marketcheck so more qualified visitors start, trust the result, and take the next step.
- Treat /growth-audit/ as a retired legacy route unless the task explicitly asks for legacy cleanup.

Active assumptions
- /solar-waermepumpen-leadgenerierung/#marktcheck is the live truth.
- /growth-audit/ redirects to the marketcheck.
- Public timing copy should use 60 Sekunden.
- Shopify is not active positioning and should only be treated as stale copy debt.

Check first
- blocksy-child/page-solar-waermepumpen-leadgenerierung.php
- blocksy-child/page-audit.php
- blocksy-child/inc/audit-page.php
- blocksy-child/inc/cja-shortcode.php
- blocksy-child/assets/js/cja-audit.js
- blocksy-child/assets/css/cja-audit.css
- blocksy-child/inc/helpers.php
- blocksy-child/inc/seo-meta.php
- blocksy-child/front-page.php
- blocksy-child/template-parts/site-header.php

Look for
- 30 Sekunden carryover
- 48h or manual feedback language in the active route
- CRM-intake framing on the public path
- Shopify mentions in audit-specific copy
- weak hero, trust, or result CTA transitions

Copy target
- Fast, credible, strategic diagnosis entry point for Solar-, Wärmepumpen- und Speicher-Anbieter
EOF
