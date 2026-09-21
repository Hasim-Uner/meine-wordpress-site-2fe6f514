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
- blocksy-child/inc/system-diagnose-page.php
- blocksy-child/inc/helpers.php
- blocksy-child/inc/seo-meta.php
- blocksy-child/front-page.php

Look for
- 30 Sekunden carryover
- retired response-time literals (see scripts/canon-forbidden-values.txt) or manual feedback language in the active route
- CRM-intake framing on the public path
- Shopify mentions in audit-specific copy
- weak hero, trust, or result CTA transitions

Copy target
- Fast, credible, strategic diagnosis entry point for Solar-, Wärmepumpen- und Speicher-Anbieter
EOF
