# Routing and External Handoffs

For CTA destinations read `docs/architecture/CONVERSION_ROUTING.md`. For route/runtime state use `docs/architecture/LIVE_STATUS.md`; for external dependencies use `docs/architecture/SYSTEM_MAP.md`. Do not copy their contracts into skills.

WordPress REST and the internal CRM are the source of truth for form submissions. External handoffs follow a successful CRM write and the applicable consent boundary.
