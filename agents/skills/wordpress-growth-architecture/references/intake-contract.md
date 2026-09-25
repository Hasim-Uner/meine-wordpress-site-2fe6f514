# Intake and CRM Contract

Do not introduce third-party form or lead-capture plugins: no Contact Form 7, Gravity Forms, WPForms, HubSpot forms, Typeform embeds, Elementor forms, SaaS form posts, or plugin shortcodes for conversion flows.

All conversion elements must be native theme code:

- HTML rendered by PHP templates, partials, shortcodes, or theme components.
- CSS from the child theme.
- Vanilla JavaScript only for client behavior.
- Data submission via `fetch()` to first-party WordPress REST endpoints owned by this repo.

The browser must not post lead data directly to n8n, Brevo, a SaaS CRM, a form plugin endpoint, or an external webhook. External booking URLs such as Cal.com may be post-submit handoffs only after the WordPress CRM write has succeeded.

## CRM Contract

Before touching frontend forms, form JavaScript, REST intake, or CRM-bound payloads, inspect the relevant handlers and called validation/storage helpers in these files (use targeted searches and ranges):

- `blocksy-child/inc/crm.php` (contact upsert, source and segment labels, attribution sanitizing and meta keys)
- the endpoint file of the form you touch: `blocksy-child/inc/contact-page.php` (`contact-request`), `blocksy-child/inc/whitelabel-request.php` (`whitelabel-request`) or `blocksy-child/inc/review-crm.php` (`audit-request`)
- `docs/architecture/PRIVACY.md` (what is stored where, and which consent applies)

The JSON/FormData payload accepted by the WordPress backend is a hard contract. Do not rename fields, flatten nested structures, change consent flags, remove attribution keys, or alter source/status/segment values unless the backend, spec, and migration path are updated in the same change.

Current hard anchors:

- REST routes under `/wp-json/nexus/v1/`, public, each with honeypot and IP rate limit: `contact-request`, `whitelabel-request`, `audit-request` (contract `2026-05-26.audit-request.v1`). `analysis-submit` is off by default behind `HU_FEATURE_READINESS_SUBMIT`; its spec is `docs/specs/anfrage-system-analyse-form-v1.md`.
- CRM post types: `nexus_contact`, `nexus_review_request`, `nexus_opportunity`, `nexus_crm_activity`.
- CRM sources and segments: `project_request`, `general_inquiry`, `whitelabel_request`, `request_analysis` / `analysis_lead` (Marktcheck), `blog_subscriber`.
- Intake order for contact and White-Label: CRM write, internal mail, confirmation. A failed internal mail is recorded with `nexus_record_lead_notification_failure()`; the request only fails when CRM and mail both fail.
- Attribution meta keys come from `nexus_get_inquiry_attribution_meta()` in `inc/crm.php`.
- Contact data is written only after explicit in-form processing consent (`consent` on `/kontakt/`, `consent_privacy` in the Marktcheck). The White-Label form is a pre-contractual request with a visible privacy note.

If a task requires changing the contract, update the server validation first, then the frontend payload, then the spec. Preserve backward compatibility unless the user explicitly approves a breaking migration.

## Cache-Resilient Forms

Assume extreme server caching, full-page caching, CDN caching, stale HTML, and cached anonymous pages.

Forms and API calls must keep working under those conditions:

- Do not rely on per-request nonces printed into cached HTML.
- If a nonce is required, load it dynamically with JavaScript from a non-cached first-party endpoint immediately before submit, or refresh it on failure.
- Prefer safe stateless public endpoints when appropriate: strict server validation, honeypot, rate limiting, sanitized payloads, feature flags, and no privileged action.
- Keep cached page config limited to stable values such as route URLs, feature flags, and public labels.
- Treat nonce expiry, stale localized data, duplicate submits, and offline/network failures as expected states.

Public REST routes with `permission_callback => '__return_true'` are allowed only when the endpoint is explicitly designed as stateless public intake and includes spam/rate/validation safeguards.

## Privacy and Security

- Collect no default-path PII before explicit contact-submit consent.
- Sanitize and validate every backend field.
- Escape all rendered values with the appropriate WordPress escaping function.
- Keep honeypots invisible and non-destructive.
- Rate-limit public intake endpoints.
- Return generic user-safe errors; keep sensitive diagnostics server-side.
- Do not expose Brevo, n8n, CRM, mail, or API credentials to the browser.
