# Conversion Routing Architecture

Status: active decision, 2026-08-18

This document separates **SEO ownership** from **conversion routing**. It exists to prevent a recurring failure mode: sending every page to the same funnel or moving a ranking query owner merely because another page has the preferred CTA.

## Core rule

**Search intent -> owning page -> next best action.**

Not:

**Search intent -> whichever landing page is currently the main sales focus.**

A page can remain the canonical SEO destination for its query while its CTA routes to a different next step.

## Commercial routes

| Route | Primary audience / intent | Page role | Primary CTA | Secondary CTA / bridge |
| --- | --- | --- | --- | --- |
| `/` | Brand / mixed / undecided | Global hub and fachliche Klammer | Choose one of the three paths or `Projekt anfragen` | Solar / Freelancer / White-Label |
| `/wordpress-freelancer-hannover/` | Direct clients seeking one freelancer | Direct WordPress money page | `Projekt anfragen` / scope clarification | Proof / tracking specialist pages |
| `/whitelabel-retainer/` | Agencies seeking delivery capacity | Agency money page | White-Label Fit-Check / scoped first project | Proof / direct contact |
| `/solar-waermepumpen-leadgenerierung/` | Solar, heat-pump and storage businesses | Energy vertical money page | Marktcheck | Solar proof / case study |
| `/server-side-tracking-b2b/` | Server-Side Tracking commercial intent | Specialist tracking money page | Tracking project request / scope clarification | White-Label bridge for agencies |
| `/wordpress-agentur-hannover/` | Local `wordpress agentur hannover` search intent | Local SEO acquisition page | Project request | Explicit bridge to Freelancer page |
| `/ergebnisse/` | Proof / evaluation | Proof hub | Context-dependent project request | Relevant case / route |
| `/case-study-solar-leadgenerierung/` | Solar proof | Evidence page | Solar Marktcheck | Energy money page |

## Cluster rules

### 1. Energy cluster -> Marktcheck

Use the Marktcheck as primary next action when the page is clearly about:

- Solar / Photovoltaik / PV leads
- Wärmepumpen leads
- Speicher lead generation
- lead portals / lead buying / portal dependency
- provider comparisons such as Aroundhome, Checkfox, Wattfox, DAA
- Solar lead costs, CPO/CPL and own lead generation versus portals
- Solar-specific funnel architecture and qualification

Examples:

- `/solar-leads-kaufen-alternative/`
- `/solar-leads-kosten-studie/`
- `/cost-per-lead-photovoltaik/`
- `/eigene-leadgenerierung-vs-portale/`
- `/waermepumpen-leads/`
- `/lead-funnel-solar/`
- `/qualifizierte-pv-anfragen/`
- `/kunden-gewinnen-solarteure/`
- `/b2b-solar-leads/`
- `/aroundhome-solar-einordnung/`
- `/checkfox-solar-waermepumpe-einordnung/`
- `/wattfox-solar-leads-einordnung/`
- `/daa-photovoltaik-leads-einordnung/`

### 2. Direct WordPress / tracking / CRO -> project request

Use the generic project route when the visitor is evaluating an implementation problem rather than the energy vertical.

Typical topics:

- WordPress development
- Landingpages / funnel pages
- Server-Side Tracking
- GA4 / GTM / attribution
- Conversion optimization
- technical SEO
- WordPress performance / Core Web Vitals

Do **not** route these pages to the Solar Marktcheck merely because `hu_get_request_analysis_url()` exists as a shared helper.

Cross-route pages that serve all three paths use the generic project request as
well, even when they are not themselves an implementation page:

- `/hasim-uener/` — the "Für Betriebe" card is the direct-project path, not the energy path
- `/glossar/` — definitional layer below every cluster
- the technical-SEO cornerstone template (`page-seo-cornerstone.php`)
- the DOMDAR e-commerce case study — a cart-and-margin case with no energy context

Use `hu_get_commercial_route( 'project_request' )` for those, with the label
`Projekt anfragen`.

Two Marktcheck links on non-energy pages are deliberate segmentation, not
misrouting, and stay: the energy branch in `page-wordpress-agentur.php` and the
energy branch in the `page-server-side-tracking-b2b.php` hero. Both name the
energy vertical explicitly before they hand off.

### 3. Agency intent -> White-Label

Use White-Label as primary route when intent explicitly involves:

- agency delivery capacity
- implementation under the agency brand
- NDA / invisible delivery
- partner / subcontractor / external implementation
- repeatable client project support

A specialist page may show a **secondary** White-Label bridge when the topic is relevant to agencies, but it should not lose its own SEO intent.

### 4. Local Agentur search intent stays separate

`/wordpress-agentur-hannover/` owns `wordpress agentur hannover` and related local variants. This SEO route must not be redirected to the Freelancer page.

The visible route switch to `/wordpress-freelancer-hannover/` is an intent correction for visitors who actually want direct collaboration.

## SEO ownership is independent

`docs/seo/query-ownership.csv` remains the authoritative query registry.

Changing a CTA does **not** automatically change:

- canonical URL
- page title / H1 owner
- internal SEO anchor ownership
- sitemap inclusion
- redirects

Changing query ownership requires separate evidence and a separate decision.

## Primary CTA destinations

### Generic project request

Canonical helper: `hu_get_navigation_project_request_url()`

Expected destination:

`/kontakt/?type=project&focus=followup_scope`

Use outside the dedicated Solar and White-Label funnels.

### Solar Marktcheck

Canonical helper: `hu_get_request_analysis_url()`

Expected destination:

`/solar-waermepumpen-leadgenerierung/#marktcheck`

Use only for Energy-cluster purchase / diagnosis intent.

### White-Label

Canonical page helper where available: `nexus_get_whitelabel_page_url()`

Expected route:

`/whitelabel-retainer/`

The page owns its own local Fit-Check CTA.

### Footer: Selbstauskunft statt Sammel-CTA

The global footer does not carry one CTA for everybody any more. It asks the
visitor to say who they are, and each of the three sentences routes into the
matching cluster above:

| Sentence | Destination | Event |
|---|---|---|
| Ich bin **Agentur** … | `/whitelabel-retainer/` | `cta_footer_pick_agency` |
| Ich bin **Solar- oder Wärmepumpenbetrieb** … | `/solar-waermepumpen-leadgenerierung/` | `cta_footer_pick_energy` |
| Ich habe **eine Seite** … | `/wordpress-freelancer-hannover/` | `cta_footer_pick_project` |

All three carry `data-track-category="lead_gen"` and
`data-track-section="footer"`. The direct line adds `cta_footer_direct_mail`
and `cta_footer_direct_phone`.

Retired with the CTA band and the merged minimal footers:
`cta_footer_primary`, `cta_footer_primary_mobile`, `cta_footer_route_*`,
`cta_footer_min_route_*`, `cta_energy_footer_analysis`,
`cta_audit_footer_analysis`, `cta_footer_nav_project` and
`cta_footer_social_github`. The remaining `cta_footer_nav_*` values in the
directory are unchanged, so directory reporting stays comparable across the
rebuild.

Note for the Energy cluster: the footer no longer repeats the Marktcheck CTA.
The Marktcheck stays the cluster's primary action on
`/solar-waermepumpen-leadgenerierung/#marktcheck` and in
`template-parts/footer-cta.php`; the footer's job is the self-selection above.

## Migration checklist

When auditing an existing page:

1. Identify its query owner from `docs/seo/query-ownership.csv`.
2. Classify audience: Energy, direct project, agency, proof/information, mixed.
3. Keep the owning URL unless there is separate SEO evidence for a migration.
4. Replace only the CTA destination / copy that conflicts with this routing contract.
5. Preserve tracking attributes and give new actions explicit event names.
6. Check footer, sticky CTA, reusable partials and helper-generated links, not only the hero button.
7. Run repo drift checks before merge.

## Known migration hotspots

These areas must be audited because the old architecture used the Marktcheck broadly:

- `hu_get_request_analysis_url()` call sites outside Energy pages
- `nexus_get_primary_public_url_map()` keys whose names imply generic actions but currently resolve to the Marktcheck
- `template-parts/footer-cta.php`
- `template-parts/seo-subpage-sticky-cta.php`
- `single.php` and category/archive CTAs
- specialist service pages such as Server-Side Tracking
- `llms.txt` and dynamic `inc/llms-txt.php`
- Organization / WebSite / Service schema descriptions and offer catalogs

## Guardrail

If intent is unclear, do not guess based on the current business priority. Keep the page's SEO owner stable and prefer a neutral `Projekt anfragen` route until the page is explicitly classified.
