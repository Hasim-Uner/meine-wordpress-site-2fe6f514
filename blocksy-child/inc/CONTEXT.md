# Module Context

Scope: `blocksy-child/inc/*.php`

## Module Buckets

- Bootstrap and helpers: `helpers.php`, `enqueue.php`, `menu-setup.php`
- SEO and schema: `seo-meta.php`, `org-schema.php`, `llms-txt.php`
- Funnel and CRM: `review-crm.php`, `crm.php`, `blog-notify.php`, `mail.php`
- Routing and doors: `commercial-routing.php` (routes, navigation contracts), `funnel-doors.php` (six doors, mode and way per request for header and footer)
- Registries and sync: `glossary/`, `service-cluster-pages.php`, `legacy-system-retirement.php`
- Admin products: `seo-cockpit*.php`, `client-portal.php`, `admin-manager.php`

## Rules

- Keep modules single-purpose and wire them through `functions.php`.
- Do not duplicate meta or schema generation in templates.
- When a registry file changes, run the `registry-release-qa` skill.
- Keep webhook URLs, API keys, and external credentials out of the repo.
- If you alter a contract consumed by JS or WordPress admin, update the matching docs.

## Guardrails

- Prefer additive helpers over hidden side effects.
- Do not bury business logic in anonymous arrays without naming the intent.
- Use stable slugs and central URL helpers instead of hardcoded internal paths.
