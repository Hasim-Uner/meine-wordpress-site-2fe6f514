# Skills Context

Scope: `agents/skills/`.

## Contract

- One skill per directory.
- Required: `SKILL.md`.
- Optional: `scripts/`, `references/`, `guides/`, `agents/`.
- Keep `SKILL.md` short: trigger, first command, hard rules, deliverable.
- Move repeated checklists, scaffolds, and scans into scripts.
- Keep large `guides/` trees out of default context; retrieve or open only task-matching guides.
- Do not duplicate brand/copy rules; reference `docs/standards/BRAND_AND_COPY.md`.

## Routing

| Skill-Ordner | Präziser Zweck | Trigger-Keywords |
| --- | --- | --- |
| `seo-agent` | SEO-Triage und gemischte SEO-Arbeiten | seo, meta, schema, sitemap, robots, llms |
| `seo-live-qa` | Live-SEO, Canonicals, Redirects, Reindex | canonical, redirect, noindex, indexierung, search console |
| `seo-cockpit-hardening` | SEO-Cockpit-Module und Diagnostik | cockpit, insights, queue, gsc, koko |
| `internal-linking-audit` | Interne Links, Orphans, Ankerlogik | internal links, orphan, anchors, linkgraph |
| `pillar-cornerstone-writer` | Cornerstone-/Pillar-Artikelpakete | cornerstone, pillar, article package, publish pack |
| `offer-funnel-intelligence` | Angebotslogik, Funnel, Marktcheck, Proof, Qualifizierung, WGOS-Grenze | offer, funnel, angebot, marktcheck, proof, qualifizierung, sales, wgos |
| `wordpress-cro-content-design-audit` | B2B-CRO, Page-Kritik, CTA-/Proof-Hierarchie | cro, cta, proof, page critique, conversion |
| `seo-conversion-copywriting` | Verkaufsorientierte deutsche Copy schreiben: Money-Pages, Landingpages, Leistungsseiten | copywriting, copy, text, hero, headline, cta-label, meta description |
| `b2b-design-system` | Visual System, UI-Komponenten, Premium-Polish | design, ui, component, polish, visual |
| `modern-web-guidance` | Moderne Web-Standards fuer WordPress-Frontend, CSS, Vanilla JS, Forms, A11y, CWV | frontend, html, css, javascript, vanilla js, forms, a11y, cwv, browser api |
| `growth-audit-optimizer` | Legacy-Optimierung fuer `/growth-audit/` | growth audit, legacy audit, instant results |
| `landing-page-builder` | Neue Paid-/Kampagnen-Landingpages | landing page, paid, campaign, ads |
| `page-speed-audit` | Core Web Vitals und Page Speed | pagespeed, cwv, lcp, inp, cls |
| `wordpress-growth-architecture` | Lead-Routing, REST, CRM, Formulararchitektur | routing, rest, crm, form, lead |
| `pre-deploy-smoke` | Pre-Push-Smoke vor Deploy/Push | push, deploy, smoke, release |
| `registry-release-qa` | Glossar-/WGOS-Registry Release-QA | registry, glossary, wgos assets, release qa |
| `navigation-migration` | Header-, Menü- und Admin-Follow-up | navigation, menu, header, admin notice |
| `homepage-proof-monitoring` | Homepage-Proof-Monitoring | homepage proof, proof metrics, monitoring |
| `wordpress-performance-marketing` | Repo-weiter Sweep aus SEO, CRO und Tracking | full audit, performance marketing, tracking |
| `route-conversion-review` | Eine einzelne Route komplett, in fester Linsen-Reihenfolge | route prüfen, seite komplett, vollcheck, durchleuchten |
| `copy-anatomy` | Fremde Copy in Struktur zerlegen, Muster extrahieren | teardown, copy analyse, wettbewerberseite, vorbild |
| `blog-seo-ux-optimizer` | Blog-Index, Kategorien, Single-Posts, interne Links, alte Positionierung | blog, category, article, author bio, related content, stale copy |

Horizontal vs. vertikal: `wordpress-performance-marketing` prüft viele Seiten
auf eine Domäne, `route-conversion-review` eine Seite auf alle Domänen. Wer
beide Beschreibungen aufweicht, macht die Auswahl zum Ratespiel.

Core rule: When a task touches `blocksy-child/assets/css/`, `blocksy-child/assets/js/`, or PHP templates that emit frontend HTML, load `modern-web-guidance` before implementation and retrieve only task-matching guides.

Use scripts from the routed skill before opening long references.
