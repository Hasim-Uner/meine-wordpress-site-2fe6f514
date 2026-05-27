# Agent Skills Index

This directory contains the repo-specific agent skills. Use `agents/skills/CONTEXT.md` as the routing table before opening individual skills.

## High-priority routing

- `offer-funnel-intelligence` — offer logic, funnel strategy, marketcheck, proof, qualification, sales-fit, WGOS boundary
- `wordpress-cro-content-design-audit` — page-level CRO, conversion copy, CTA/proof hierarchy, content design
- `wordpress-performance-marketing` — full audit across SEO, CRO, tracking, and performance marketing
- `seo-agent` — general SEO triage
- `seo-live-qa` — live SEO, canonical, redirect, noindex, indexing checks
- `page-speed-audit` — Core Web Vitals and speed diagnosis
- `modern-web-guidance` — frontend, HTML, CSS, JS, forms, accessibility, Core Web Vitals

## Rule of thumb

Use `offer-funnel-intelligence` first when the task asks whether the website or offer creates better qualified leads, whether the marketcheck is strong enough, whether proof is used well, or whether WGOS belongs in public copy or only in delivery.

Use `wordpress-cro-content-design-audit` when the task is narrower: improve one page, hero, CTA, proof block, FAQ, form flow, or section order.

Use `modern-web-guidance` before implementation when frontend templates, CSS, JS, forms, accessibility, or Core Web Vitals are touched.

## Canonical skill table

The canonical routing table lives in:

```text
agents/skills/CONTEXT.md
```

Do not duplicate long rules here. Keep this file as a discoverable index only.
