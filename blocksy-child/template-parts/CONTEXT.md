# Shared Sections Context

Scope: `blocksy-child/template-parts/*.php`

## Purpose

These files are reusable UI sections shared by multiple routes. They carry CTA hierarchy, proof surfaces, and repeated layout logic.

## Rules

- Keep one clear primary action per section.
- Primary lead paths stay aligned with the current marketcheck/contact architecture unless the task explicitly changes funnel strategy.
- Pass values into partials instead of hardcoding route-specific assumptions.
- Keep tracking attributes intact on buttons and links.
- Use partials for repeatable structure, not for hiding undocumented business logic.

## Watchlist

- `footer-cta.php`
- `trust-section.php`
- `site-header.php`

If one of these changes behaviorally, verify the same surface on every page that includes it.
