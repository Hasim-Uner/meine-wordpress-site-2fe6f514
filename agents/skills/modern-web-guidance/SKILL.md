---
name: modern-web-guidance
description: Use GoogleChrome/modern-web-guidance before frontend work in this WordPress child theme: HTML templates, CSS, vanilla JavaScript, forms, accessibility, browser APIs, Core Web Vitals, and progressive enhancement.
---

# Modern Web Guidance

Trigger: before changing frontend behavior in `blocksy-child/`, especially PHP templates that emit HTML, `assets/css/`, `assets/js/`, forms, dialogs, navigation, animation, image priority, and Core Web Vitals work.

First command:

```sh
npx -y modern-web-guidance@latest search "<specific frontend task>" --skill-version 2026_05_16-c5e7870
```

Retrieve only the most relevant guide IDs:

```sh
npx -y modern-web-guidance@latest retrieve "<id>"
```

Hard rules:

- Keep implementation aligned with this repo stack: WordPress child theme, PHP-rendered HTML, vanilla JS, CSS, native browser APIs, self-hosted assets.
- Do not add React, Vue, Angular, client-side routing, build-time frameworks, component libraries, polyfill packages, or analytics libraries unless the user explicitly asks and CI/build impact is accepted.
- Prefer Baseline widely available browser features. For newer APIs, use feature detection and graceful fallback instead of dependency overhead.
- Keep searches action-oriented and narrow. Do not open `agents/skills/modern-web-guidance/guides/` wholesale; load one retrieved or locally relevant guide at a time.
- Keep CI compatible with `.github/workflows/ci.yml`: Node 20, `npm ci`, PHP syntax check, deploy-config validation, and `npm run build:theme`.

Deliverable: cite the guide ID(s) used, explain any browser-support fallback, and keep changes scoped to task-relevant theme files.

Local guides are pruned to the relevant frontend categories: `accessibility`, `css`, `css-layout`, `forms`, `html`, `performance`, `privacy`, `security`, and `user-experience`.
