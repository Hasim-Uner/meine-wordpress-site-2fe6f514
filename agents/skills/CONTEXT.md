# Skills Context

Scope: `agents/skills/`.

## Contract

- One skill per directory.
- Required: `SKILL.md`.
- Optional: `scripts/`, `references/`, `agents/`.
- Keep `SKILL.md` short: trigger, first command, hard rules, deliverable.
- Move repeated checklists, scaffolds, and scans into scripts.
- Do not duplicate brand/copy rules; reference `docs/standards/BRAND_AND_COPY.md`.

## Routing

| Skill-Ordner | Präziser Zweck | Trigger-Keywords |
| --- | --- | --- |
| `seo-agent` | SEO-Triage und gemischte SEO-Arbeiten | seo, meta, schema, sitemap, robots, llms |
| `seo-live-qa` | Live-SEO, Canonicals, Redirects, Reindex | canonical, redirect, noindex, indexierung, search console |
| `seo-cockpit-hardening` | SEO-Cockpit-Module und Diagnostik | cockpit, insights, queue, gsc, koko |
| `internal-linking-audit` | Interne Links, Orphans, Ankerlogik | internal links, orphan, anchors, linkgraph |
| `pillar-cornerstone-writer` | Cornerstone-/Pillar-Artikelpakete | cornerstone, pillar, article package, publish pack |
| `wordpress-cro-content-design-audit` | B2B-CRO, Page-Kritik, CTA-/Proof-Hierarchie | cro, cta, proof, page critique, conversion |
| `b2b-design-system` | Visual System, UI-Komponenten, Premium-Polish | design, ui, component, polish, visual |
| `growth-audit-optimizer` | Legacy-Optimierung fuer `/growth-audit/` | growth audit, legacy audit, instant results |
| `landing-page-builder` | Neue Paid-/Kampagnen-Landingpages | landing page, paid, campaign, ads |
| `page-speed-audit` | Core Web Vitals und Page Speed | pagespeed, cwv, lcp, inp, cls |
| `wordpress-growth-architecture` | Lead-Routing, REST, CRM, Formulararchitektur | routing, rest, crm, form, lead |
| `pre-deploy-smoke` | Pre-Push-Smoke vor Deploy/Push | push, deploy, smoke, release |
| `registry-release-qa` | Glossar-/WGOS-Registry Release-QA | registry, glossary, wgos assets, release qa |
| `navigation-migration` | Header-, Menü- und Admin-Follow-up | navigation, menu, header, admin notice |
| `homepage-proof-monitoring` | Homepage-Proof-Monitoring | homepage proof, proof metrics, monitoring |
| `wordpress-performance-marketing` | Vollaudit aus SEO, CRO und Tracking | full audit, performance marketing, tracking |

Use scripts from the routed skill before opening long references.
