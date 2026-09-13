---
name: conversion-architecture
description: "Primary router for page-level CRO, route-wide conversion review, campaign landing pages, and conversion-oriented page architecture."
---

# Conversion Architecture

Trigger: page structure, CTA hierarchy, proof placement, route conversion review, or a new campaign/paid landing page.

Delegate:
- Page critique/CRO/content design → `wordpress-cro-content-design-audit`
- Full vertical route review → `route-conversion-review`
- New paid/campaign page → `landing-page-builder`
- Legacy `/growth-audit/` work → `growth-audit-optimizer`
- Homepage proof instrumentation → `homepage-proof-monitoring`

Hard rules:
- Offer/funnel economics belong to `offer-funnel-intelligence`.
- Copy generation belongs to `conversion-copy`.
- Preserve query ownership and canonical CTA-routing contracts.
- Prefer shared components and measurable friction removal over decorative complexity.

Deliverable: prioritized page architecture/change set with measurable conversion intent.
