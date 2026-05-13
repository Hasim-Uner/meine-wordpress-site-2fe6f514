---
name: wordpress-growth-architecture
description: Enforce the technical WordPress growth-system architecture for hasimuener.de. Use when work touches lead routing, conversion forms, WordPress REST endpoints, CRM payloads, Anfrage-System-Analyse/System-Diagnose, attribution payloads, caching resilience, route contracts, n8n handoff boundaries, or repo-vs-editor ownership in the Blocksy child theme. Do not use for visual polish, color systems, card styling, typography, or premium-design direction; route those tasks to b2b-design-system.
---

# WordPress Growth Architecture

Use this skill as a hard architecture gate for the WordPress growth system. It governs infrastructure, lead routing, REST contracts, CRM persistence, attribution payloads, cache behavior, and release validation.

This is not a copywriting or cosmetic design skill. Move colors, premium look, typography, card density, animation, spacing polish, and UI taste decisions to `$b2b-design-system`.

## Core Boundary

Treat the site as business infrastructure:

- WordPress owns routing, templates, SEO/meta/schema, REST endpoints, CRM storage, mail handoff, and canonical contract files.
- The repo owns runtime code in `blocksy-child/`, reusable helpers, registries, scripts, and durable docs.
- The WordPress editor owns much of the live page copy and media.
- n8n, Brevo, Cal.com, Koko Analytics, GTM, GA4, Consent, and external ad systems are real dependencies, not implied code.

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

Before touching frontend forms, form JavaScript, analysis-submit behavior, or CRM-bound payloads, read these files in full:

- `blocksy-child/inc/crm.php`
- `blocksy-child/inc/analysis-intake.php`
- `docs/specs/anfrage-system-analyse-form-v1.md`

The JSON/FormData payload accepted by the WordPress backend is a hard contract. Do not rename fields, flatten nested structures, change consent flags, remove attribution keys, or alter source/status/segment values unless the backend, spec, and migration path are updated in the same change.

Current hard anchors:

- REST route: `/wp-json/nexus/v1/analysis-submit`
- Feature flag: `HU_FEATURE_READINESS_SUBMIT`
- CRM post type: `nexus_contact`
- CRM source: `request_analysis`
- CRM segment: `analysis_lead`
- Contact submit writes only after explicit in-form processing consent.

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
- Keep attribution storage session-scoped or payload-scoped. Do not introduce persistent cookies for attribution.

The visible consent checkbox in a form is processing consent for the submitted contact data. It is not a tracking-banner substitute.

### 5. Lead Routing Is Not Optional

Preserve the diagnosis-first funnel:

- Cold B2B CTA: `/anfrage-system-analyse/` or the current canon returned by `hu_get_request_analysis_url()`.
- Warm-intent fallback: `/anfrage/` only when the existing routing contract requires it.
- Demo paths are showroom assets, not direct generic sales funnels.
- Retired growth-audit or generic agency paths must not become primary CTAs again.
- Use `blocksy-child/inc/canon/diagnose-canon.php` as the source for current analysis labels, route, scope, and offer frame.

Do not route analysis or demo interactions directly into a generic sales pitch. The flow must qualify or disqualify Founding-Partner fit before implementation is sold.

### 6. No Silent n8n Coupling

n8n is not the browser submit target for the analysis flow.

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
