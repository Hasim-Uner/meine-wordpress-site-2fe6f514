---
name: navigation-migration
description: Change or review header, mobile menu, footer or 404 navigation, then run the post-deploy WordPress checks. Use when menu items, labels, order, targets or CTA placement change.
---

# Navigation Migration

## Run First

```bash
agents/skills/navigation-migration/scripts/print-checklist.sh
php scripts/tests/navigation-contract.php
```

The checklist renders the live contract; it never lists a hand-kept menu.

## Source of Truth

- Header, mobile sheet, 404, stored WP menu, SEO Cockpit:
  `hu_get_site_header_navigation_contract()`.
- Footer ways and directory: `hu_get_site_footer_navigation_contract()`.
- Route targets: `hu_get_commercial_route_map()`. All in
  `blocksy-child/inc/commercial-routing.php`.
- Current targets and events: `docs/architecture/CONVERSION_ROUTING.md`
  (sections Header, Footer, 404). Do not restate them here.

## Hard Rules

- Change a target in the route map, never in a template or via a filter in
  another module.
- A generic label ("Tracking") points to the broad offer; a specialist page is
  linked with its own name. Check `docs/seo/query-ownership.csv` first.
- Header stays flat. Footer order follows the header order.
- `aria-current="page"` only for the page itself, `"true"` for its area.
- Keep existing `data-track-action` values; new links get new names.
- Update `scripts/tests/navigation-contract.php` expectations with the change,
  then run `npm run test:navigation-ui`.

## Deliver

- What changed in the contract and why
- Gate results (contract test, UI test, lint:architecture, lint:php)
- Manual WordPress admin and uncached post-deploy checks from the checklist
