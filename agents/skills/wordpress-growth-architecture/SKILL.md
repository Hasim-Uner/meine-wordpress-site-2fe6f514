---
name: wordpress-growth-architecture
description: "Implement or repair WordPress forms, REST intake, CRM contracts, lead routing and attribution. Pure visual work belongs to frontend-system."
---

# WordPress Growth Architecture

Own the path from a native theme form through first-party REST to CRM and handoff.
Use the local context already selected under `AGENTS.md`; do not load another.

## Load by surface

- Forms, submit JS, REST or CRM payloads: [intake-contract](references/intake-contract.md).
  Trace the endpoint, called CRM helpers and privacy contract before editing.
- Attribution fields or storage: also read [attribution](references/attribution.md).
- CTA destinations or external integration: [routing-handoffs](references/routing-handoffs.md).
- Repo/editor ownership uncertainty: [hybrid-change-map](references/hybrid-change-map.md).
- Implementation and validation: [delivery-validation](references/delivery-validation.md).

Start with `git status --short` and targeted searches for the affected route/handler.
Read only references matching the change; do not load CRM code for pure layout work.

## Invariants

- Native theme forms, first-party REST and CRM own lead capture. No third-party
  form plugins or direct browser submissions to external services.
- Preserve accepted fields, consent, source/status/segment values and attribution.
  Update server validation, frontend and spec together; breaking migration needs
  explicit authorization. External booking follows a successful CRM write.
- Keep cached anonymous forms resilient: stable page config, no stale HTML nonces,
  safe retries, duplicate-submit and network-failure handling.
- Public intake requires validation, sanitization, rate limits and spam protection;
  privileged actions require authorization. Escape output and keep diagnostics private.
- Preserve the existing consent/privacy boundaries. No new pixels, persistent
  attribution cookies or external configuration without an explicit request.
- Read canonical routing/dependency docs when their contracts are touched.
  Keep inactive n8n outside the task unless explicitly requested.

Delegate UI craft to `b2b-design-system` only for an included implementation subtask.
Pure visual requests start at `frontend-system`; copy requests at `conversion-copy`.

Deliver: verified data path, payload compatibility, checks and any separate
WordPress/editor or external follow-ups. Never claim success without verifying
CRM persistence, cache behavior and the consent boundary.
