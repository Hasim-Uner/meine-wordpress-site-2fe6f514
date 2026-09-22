---
name: wordpress-growth-architecture
description: Enforce the technical WordPress growth-system architecture for hasimuener.de. Use when work touches lead routing, conversion forms, WordPress REST endpoints, CRM payloads, contact, White-Label and Marktcheck intake (plus the retired Anfragesystem-Analyse), attribution payloads, caching resilience, route contracts, n8n handoff boundaries, or repo-vs-editor ownership in the Blocksy child theme. Do not use for visual polish, color systems, card styling, typography, or premium-design direction; route those tasks to b2b-design-system.
---

# WordPress Growth Architecture

Use this skill as a hard architecture gate for the WordPress growth system. It governs infrastructure, lead routing, REST contracts, CRM persistence, attribution payloads, cache behavior, and release validation.

This is not a copywriting or cosmetic design skill. Move colors, premium look, typography, card density, animation, spacing polish, and UI taste decisions to `$b2b-design-system`.

## Core Boundary

Treat the site as business infrastructure:

- WordPress owns routing, templates, SEO/meta/schema, REST endpoints, CRM storage, mail handoff, and canonical contract files.
- The repo owns runtime code in `blocksy-child/`, reusable helpers, registries, scripts, and durable docs.
- The WordPress editor owns much of the live page copy and media.
- Brevo (mail), Cal.com (booking links) and Koko Analytics (plugin) are real external dependencies, not implied code. n8n is not connected. GTM, GA4, Consent Mode and ad platforms are services sold to clients, not dependencies of this site.

Never hide architecture changes inside visual refactors. Separate repo code, editor tasks, external-system tasks, and contract changes in the final answer.

## Non-Negotiable Rules

### 1. No Lead-Capture Plugins

Do not introduce third-party form or lead-capture plugins: no Contact Form 7, Gravity Forms, WPForms, HubSpot forms, Typeform embeds, Elementor forms, SaaS form posts, or plugin shortcodes for conversion flows.

All conversion elements must be native theme code:

- HTML rendered by PHP templates, partials, shortcodes, or theme components.
- CSS from the child theme.
- Vanilla JavaScript only for client behavior.
- Data submission via `fetch()` to first-party WordPress REST endpoints owned by this repo.

The browser must not post lead data directly to n8n, Brevo, a SaaS CRM, a form plugin endpoint, or an external webhook. External booking URLs such as Cal.com may be post-submit handoffs only after the WordPress CRM write has succeeded.

### 2. CRM Contract Is Sacred

Before touching frontend forms, form JavaScript, REST intake, or CRM-bound payloads, read these files in full:

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

### 3. Cache-Resilient Forms Only

Assume extreme server caching, full-page caching, CDN caching, stale HTML, and cached anonymous pages.

Forms and API calls must keep working under those conditions:

- Do not rely on per-request nonces printed into cached HTML.
- If a nonce is required, load it dynamically with JavaScript from a non-cached first-party endpoint immediately before submit, or refresh it on failure.
- Prefer safe stateless public endpoints when appropriate: strict server validation, honeypot, rate limiting, sanitized payloads, feature flags, and no privileged action.
- Keep cached page config limited to stable values such as route URLs, feature flags, and public labels.
- Treat nonce expiry, stale localized data, duplicate submits, and offline/network failures as expected states.

Public REST routes with `permission_callback => '__return_true'` are allowed only when the endpoint is explicitly designed as stateless public intake and includes spam/rate/validation safeguards.

### 4. Cookie-Banner-Free Attribution

Keep the lead system 100% cookie-banner-free by default.

- Do not add a global cookie banner to make form tracking work.
- Do not add marketing pixels, GA/GTM events, Meta CAPI browser calls, or third-party trackers to conversion forms.
- Use Koko Analytics standards only for analytics context.
- Parse UTM parameters, referrer, and click IDs in the background with vanilla JavaScript.
- Append attribution fields invisibly to the CRM payload; do not make them user-facing form fields.
- Exception by design: the optional, self-reported question how someone found the site (`referral_source`, options in `nexus_get_inquiry_referral_options()`) is a visible field. It complements technical attribution for word of mouth, LinkedIn or AI assistants and must stay optional.
- Keep attribution storage session-scoped or payload-scoped. Do not introduce persistent cookies for attribution.

The visible consent checkbox in a form is processing consent for the submitted contact data. It is not a tracking-banner substitute.

### 5. Lead Routing Is Not Optional

Routing follows `docs/architecture/CONVERSION_ROUTING.md`:

- Direct WordPress, tracking, CRO or technical-SEO intent → project request via `hu_get_commercial_route( 'project_request' )` (`/kontakt/?type=project`).
- Agency intent → `/whitelabel-retainer/`.
- Energy intent (Solar, Wärmepumpe, Speicher) → Marktcheck via `hu_get_request_analysis_url()`; labels, scope and offer frame come from `blocksy-child/inc/canon/diagnose-canon.php`. The Marktcheck is never a global CTA.
- Retired growth-audit, analysis and `/anfrage/` paths redirect to the Marktcheck and must not become primary CTAs again.
- Demo paths are showroom assets, not direct generic sales funnels.

In the energy path, the Marktcheck qualifies or disqualifies fit before implementation is sold.

### 6. No Silent n8n Coupling

n8n is not the submit target of any form.

Only connect n8n when all of these exist:

- A versioned payload contract.
- A workflow export in `automations/n8n/workflows/`.
- Companion docs in `automations/n8n/docs/`.
- A flow map in `automations/n8n/flow-maps/`.
- A clear retention and failure-mode policy.

Until then, WordPress REST and the internal CRM are the source of truth.

## Required Workflow

### 1. Load The Contract Surface

For every lead-routing or conversion-system task, inspect the relevant local context and then classify the touched surface:

- `REST/API`: route registration, validation, permissions, rate limits.
- `CRM`: `nexus_contact`, segments, consent meta, source labels, admin display.
- `Frontend submit`: HTML, hidden fields, JS payload, fetch behavior, error states.
- `Routing`: canonical CTA URLs, redirects, noindex/index rules, menu helpers.
- `Attribution`: UTM/referrer/click IDs, Koko context, payload-only analytics.
- `External handoff`: Brevo, Cal.com, n8n, GTM/GA4, Consent.
- `Editor-owned`: live copy/media that must be changed manually in WordPress.

If frontend forms or JS are involved, load the three CRM contract files listed above before editing.

### 2. Design The Data Path Before Editing

Write down the intended flow before changing code:

1. User action.
2. Native HTML element or theme-rendered form.
3. Vanilla JS validation/enrichment.
4. `fetch()` target.
5. WordPress REST validation.
6. CRM write or explicit no-write decision.
7. Mail/booking/external handoff.
8. Failure and retry behavior.

Reject any path that bypasses WordPress REST for lead data.

### 3. Implement In This Order

1. Backend contract and validation.
2. CRM persistence and consent meta.
3. Frontend payload generation.
4. Fetch, error, duplicate-submit, and stale-cache handling.
5. Route helpers and CTA targets.
6. External handoff after successful CRM write.
7. Documentation/spec updates when contracts change.

Do not begin with visual polish. If the user also requests UI design, finish the architecture constraints first and then use `$b2b-design-system`.

### 4. Preserve Privacy And Security

- Collect no default-path PII before explicit contact-submit consent.
- Sanitize and validate every backend field.
- Escape all rendered values with the appropriate WordPress escaping function.
- Keep honeypots invisible and non-destructive.
- Rate-limit public intake endpoints.
- Return generic user-safe errors; keep sensitive diagnostics server-side.
- Do not expose Brevo, n8n, CRM, mail, or API credentials to the browser.

## Pre-Flight Before Final

Before presenting changes as final, run every available lint or smoke check that matches the touched surface. Do not skip checks silently. At minimum, execute or explicitly rule out:

- `git status --short`
- `php -l blocksy-child/path/to/changed.php` for changed PHP files.
- `find blocksy-child -name '*.php' -print0 | xargs -0 -n1 php -l` for broad PHP changes.
- `sh scripts/check-german-copy.sh` when customer-facing German copy changed.
- `sh scripts/lint-canon-drift.sh` when canon, CTA, offer, or proof data changed.
- `sh scripts/lint-e3-canon.sh` when E3 proof data changed.
- `sh scripts/verify-deploy-config.sh` when deploy configuration or deploy safety is in scope and required env values are available.

If a check cannot run because an environment variable, dependency, or live service is unavailable, state that explicitly and explain the residual risk. A final answer that omits the check status is incomplete.

## Final Answer Contract

Close substantial work with:

- Files changed.
- Contract files read.
- Endpoint and payload status: unchanged, extended compatibly, or intentionally migrated.
- Manual WordPress/admin tasks, if any.
- External-system tasks, if any.
- Checks run and any checks skipped.

Never claim a lead-flow change is done if the payload contract, cache behavior, consent boundary, or CRM write path was not verified.
