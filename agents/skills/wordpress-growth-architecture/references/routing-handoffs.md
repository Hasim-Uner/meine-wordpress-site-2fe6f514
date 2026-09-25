# Routing and External Handoffs

For CTA destinations read `docs/architecture/CONVERSION_ROUTING.md`. For route/runtime state use `docs/architecture/LIVE_STATUS.md`; for external dependencies use `docs/architecture/SYSTEM_MAP.md`. Do not copy their contracts into skills.

n8n is not the submit target of any form.

Only connect n8n when all of these exist:

- A versioned payload contract.
- A workflow export in `automations/n8n/workflows/`.
- Companion docs in `automations/n8n/docs/`.
- A flow map in `automations/n8n/flow-maps/`.
- A clear retention and failure-mode policy.

Until then, WordPress REST and the internal CRM are the source of truth.
