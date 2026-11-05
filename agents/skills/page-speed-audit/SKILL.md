---
name: page-speed-audit
description: Audit and fix Core Web Vitals and Lighthouse performance issues. Use when optimizing page speed, fixing LCP/CLS/INP, removing unused CSS/JS, or preparing for a performance push.
---

# Page Speed Audit

Use this skill to find and fix performance bottlenecks in the Blocksy child theme.

## Run First

```bash
agents/skills/page-speed-audit/scripts/audit.sh [url]
```

Default URL: `https://hasimuener.de/`. The script runs a PageSpeed Insights API check and prints a structured report.

## Audit Scope

1. **LCP** — Largest Contentful Paint (target < 2.5s)
2. **CLS** — Cumulative Layout Shift (target < 0.1)
3. **INP** — Interaction to Next Paint (target < 200ms)
4. **Unused CSS/JS** — bytes wasted by unused code
5. **Image optimization** — format, sizing, lazy-load
6. **Font loading** — self-hosted, preload, display swap

## Fix Patterns

- Unused CSS: isolate and remove from `style.css` or split into conditional loads via `enqueue.php`
- LCP images: add `fetchpriority="high"`, remove `loading="lazy"` on above-fold
- Fonts: already self-hosted in `blocksy-child/fonts/` — verify `font-display: swap` in `fonts.css`
- JS: defer or remove non-critical scripts in `enqueue.php`

## Deliver

- Prioritized fix list with file paths and line numbers
- Before/after metrics as measured values with a named run (tool, device/profile); without a measurement, label them explicitly as unverified estimates
- Files touched and rationale for each change
