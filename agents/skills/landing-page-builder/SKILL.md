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
- Primary CTA always points to `/solar-waermepumpen-leadgenerierung/#marktcheck`
- Add `data-track-action`, `data-track-category`, `data-track-section` attributes to every CTA
- No cookies, no client-side tracking scripts — UTM params are handled via existing sessionStorage logic

## Section Order

1. **Hero** — Headline matching the ad copy, single clear CTA
2. **Problem** — Pain point the visitor searched for
3. **Mechanism** — How the solution works (3 steps max)
4. **Proof** — Results, logos, or testimonials
5. **CTA** — Repeat the primary CTA with urgency framing

## Deliver

1. PHP template file with all sections
2. Any new CSS added to existing stylesheet (no new CSS files)
3. `data-track-*` attributes on all interactive elements
4. Meta title + description suggestion
5. Recommended ad headline ↔ landing page headline alignment check
