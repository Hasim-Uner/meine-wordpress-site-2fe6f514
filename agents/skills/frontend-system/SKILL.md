---
name: frontend-system
description: "Primary frontend router for this WordPress child theme: HTML, CSS, vanilla JS, accessibility, browser APIs, visual polish, motion, and Core Web Vitals-sensitive UI work."
---

# Frontend System

Trigger: any frontend implementation or review in `blocksy-child/`.

Delegate only as needed:
- Standards/browser behavior → `agents/skills/modern-web-guidance/SKILL.md`
- Design system/layout/components → `agents/skills/b2b-design-system/SKILL.md`
- Craft/motion detail → vendored `emil-design-eng`, `animate`, `review-animations`, or `improve-animations`

Hard rules:
- WordPress/PHP-rendered HTML, vanilla JS, CSS, native browser APIs.
- Use `design-system.css` tokens; do not introduce a parallel token system.
- Progressive enhancement, feature detection, accessible semantics, `prefers-reduced-motion`.
- No React/Vue/Angular/component libraries unless explicitly requested.
- Open only task-matching guidance/references.

Validation:
```bash
bash scripts/lint-css-motion.sh
npm run lint:php
```

Deliverable: scoped frontend change plus the relevant deterministic checks.
