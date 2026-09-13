---
name: frontend-system
description: "Primary frontend router for this WordPress child theme: HTML, CSS, vanilla JS, accessibility, browser APIs, visual polish, motion, and Core Web Vitals-sensitive UI work."
---

# Frontend System

Trigger: any frontend implementation or review in `blocksy-child/`.

Read `docs/architecture/CSS_ARCHITECTURE.md` before changing shared CSS architecture.

Delegate only as needed:
- Standards/browser behavior → `agents/skills/modern-web-guidance/SKILL.md`
- Design system/layout/components → `agents/skills/b2b-design-system/SKILL.md`
- Craft/motion detail → vendored `emil-design-eng`, `animate`, `review-animations`, or `improve-animations`

Hard rules:
- WordPress/PHP-rendered HTML, vanilla JS, CSS, native browser APIs.
- New and migrated surfaces use `assets/css/system.css` as the canonical design core; route CSS contains only justified deltas.
- `design-system.css` and `--nx-*` are legacy compatibility. Do not create a new `--nx-*` consumer or expand an existing one unless the task is explicitly a migration prerequisite.
- Prefer the Gutachten tokens (`--papier`, `--tinte`, `--stempel`, `--s0`…`--s6`, motion tokens) when their semantics fit.
- Progressive enhancement, feature detection, accessible semantics, `prefers-reduced-motion`.
- No React/Vue/Angular/component libraries unless explicitly requested.
- Open only task-matching guidance/references.

Validation:
```bash
python3 scripts/audit-css-architecture.py
python3 scripts/audit-legacy-nx-css.py
bash scripts/lint-css-motion.sh
bash scripts/lint-css-spacing.sh
npm run lint:php
```

Deliverable: scoped frontend change plus the relevant deterministic checks.
