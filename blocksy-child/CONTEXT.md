# Theme Context

Scope: all deployable WordPress runtime code in `blocksy-child/`.

## Load Next

- Editing `inc/*.php`: read `blocksy-child/inc/CONTEXT.md`
- Editing `template-parts/*.php`: read `blocksy-child/template-parts/CONTEXT.md`

## Runtime Model

- `functions.php` is a thin bootstrap and lists every `inc/` module. Module groups with their own folder (`crm-sales/`, `seo-cockpit/`) have one bootstrap in that list; no other module loads modules as a side effect.
- `inc/` holds PHP modules and registries.
- `template-parts/` holds shared sections used across multiple routes.
- `assets/css/` and `assets/js/` are page- or system-specific assets.
- Deploy scope is the whole `blocksy-child/` tree.

## Hybrid Source Of Truth

Classify the surface before editing:

- `Editor-driven wrapper`: repo controls structure, editor controls most copy
- `Hardcoded template`: repo owns structure and copy
- `Shared partial`: repo owns reusable section logic

If the page is editor-driven, do not fake a complete content migration in code.

## Critical Files

- `blocksy-child/functions.php` (the only module loader; every `inc/` module is listed there)
- `blocksy-child/inc/commercial-routing.php` (canonical routes and navigation contract)
- `blocksy-child/inc/canon/` (facts: response time, prices, case figures, Marktcheck)
- `blocksy-child/inc/enqueue.php`
- `blocksy-child/inc/seo-meta.php`
- `blocksy-child/inc/org-schema.php`
- `blocksy-child/inc/crm.php`, `blocksy-child/inc/crm-sales/` (CRM, pipeline, response watchdog)
- `blocksy-child/inc/contact-page.php`, `blocksy-child/inc/whitelabel-request.php`, `blocksy-child/inc/review-crm.php` (public intake endpoints)

## Working Rules

- Use `home_url()` for internal links and escape every output.
- Preserve or add `data-track-*` attributes on CTA surfaces.
- Keep schema, canonical, and robots logic centralized.
- If user-facing CTA hierarchy, route status, or offer framing changes, update `docs/architecture/LIVE_STATUS.md`.
