#!/usr/bin/env bash

set -euo pipefail

cat <<'EOF'
Main menu target:
- /solar-waermepumpen-leadgenerierung/#marktcheck [class=nav-cta-button]
- /e3-new-energy/
- /wordpress-agentur-hannover/
- /ergebnisse/
- /blog/
- /uber-mich/

Homepage editor target order:
1. Hero
2. Schmerz-Sektion
3. System-Flow
4. E3-Proof
5. Themen-Hub
6. Marktcheck-CTA
7. FAQ
8. Blog

Post-deploy QA:
- Check header menu on desktop and mobile.
- Remove duplicated marketcheck buttons or legacy booking links.
- Re-check homepage, Solar/SHK marketcheck, Agentur page, and E3 proof uncached.
- Verify footer and utility links still follow the CTA hierarchy.
EOF
