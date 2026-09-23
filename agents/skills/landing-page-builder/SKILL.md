---
name: landing-page-builder
description: Scaffold conversion-optimized landing pages for Google Ads campaigns. Use when building new ad landing pages, campaign-specific pages, or optimizing existing pages for paid traffic.
---

# Landing Page Builder

Use this skill to create or optimize landing pages for paid traffic (Google Ads).

## Run First

```bash
agents/skills/landing-page-builder/scripts/scaffold-lp.sh "Page Title" "optional-slug"
```

The script prints the required file skeleton and section structure.

## Architecture Rules

- Landing pages live as `blocksy-child/page-<slug>.php`
- Reuse existing template-parts from `blocksy-child/template-parts/`
- Follow the b2b-design-system patterns (if active)
- Primary CTA follows the intent, per `docs/architecture/CONVERSION_ROUTING.md`: WordPress, tracking, CRO or technical SEO → `hu_get_commercial_route( 'project_request' )`; agencies → `hu_get_commercial_route( 'whitelabel' )`; Solar, Wärmepumpe or Speicher → `hu_get_commercial_route( 'marketcheck' )`. Never hardcode the URL and never use the Marktcheck as a generic CTA.
- Add `data-track-action`, `data-track-category`, `data-track-section` attributes to every CTA
- No cookies, no browser storage, no client-side tracking scripts — `NexusCore` reads UTM params and referrer from the form page at submit time; session attribution stays off without consent (`docs/architecture/PRIVACY.md`)

## Section Order

1. **Hero** — Headline matching the ad copy, single clear CTA
2. **Problem** — Pain point the visitor searched for
3. **Mechanism** — How the solution works (3 steps max)
4. **Proof** — Results, logos, or testimonials
5. **CTA** — Repeat the primary CTA with the concrete next step (what happens after the request, response time from `hu_response_promise()`); no deadlines, seat counters or artificial scarcity

## Deliver

1. PHP template file with all sections
2. Any new CSS added to existing stylesheet (no new CSS files)
3. `data-track-*` attributes on all interactive elements
4. Meta title + description suggestion
5. Recommended ad headline ↔ landing page headline alignment check
