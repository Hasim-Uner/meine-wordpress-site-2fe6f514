---
name: b2b-design-system
description: >
  Premium UX/UI and motion-direction guidance for B2B WordPress surfaces.
  Use for layout, hierarchy, components, visual polish, interaction states,
  motion, and design critique.
---

# B2B Design System

Every design decision must improve comprehension, trust, or conversion without increasing architectural drift.

## Canon First

Repository architecture overrides older examples in this skill's references.

Read `docs/architecture/CSS_ARCHITECTURE.md` before shared CSS work.

- `blocksy-child/assets/css/system.css` is the canonical design core for new and migrated surfaces.
- Route CSS is a delta: route-specific layout, components, responsive rules, and justified overrides only.
- `design-system.css` and `--nx-*` are legacy compatibility. Do not add a new NX consumer or grow an existing NX footprint.
- Existing `--nx-*` code may be edited to migrate or reduce debt. Run `python3 scripts/audit-legacy-nx-css.py` after doing so.
- The Gutachten spacing scale is `--s0` through `--s6`; do not invent another page-local spacing scale when those roles fit.
- Use semantic Gutachten tokens such as `--papier`, `--tinte`, `--grau`, `--stempel`, `--haar`, and the shared motion tokens instead of copying literal values.

Load references only when needed:
- Components → `references/components.md`
- Layout/typography principles → `references/design-tokens.md`
- Motion → `references/motion.md`

Treat any legacy `--nx-*` examples inside those references as historical syntax, not permission to create new consumers.

## Direction

Aim for engineered clarity: strong typography, deliberate negative space, warm neutrals, one earned accent, restrained depth, and obvious hierarchy. Avoid decorative complexity that makes the interface harder to scan.

Hard bans:
- generic AI-gradient aesthetics;
- bright competing accent colors;
- gratuitous card grids;
- mixed corner personalities without semantic reason;
- more than two display/body font families;
- motion that hides content until JavaScript runs;
- `transition: all` or unbounded decorative loops.

## Conversion Hierarchy

For commercial B2B surfaces, preserve this decision order when applicable:

1. Hero — value and audience fit.
2. Proof — evidence and credibility.
3. Mechanism — how the result is produced.
4. Offer — what is actually delivered.
5. Objections — remove material resistance.
6. Final CTA — one clear next action.

One primary CTA can repeat; competing primary actions should not.

## Layout and Typography

- Use a 4px base rhythm; prefer 8px steps where the canonical tokens allow it.
- Keep text measure deliberate and test the longest German compound at the largest responsive type size.
- Text containers need balanced breathing room unless asymmetry is purposeful and documented.
- Use responsive `clamp()` where it improves continuity, not to hide breakpoint problems.
- Prefer intrinsic layout, grid/flex, logical properties, and content-driven sizing.
- Do not solve overflow by clipping meaningful content.

## Motion

Choose an intensity from 0–3 and one purpose before adding effects.

Motion may communicate feedback, continuity, hierarchy, or one focal moment. It must not become the visual concept by itself.

Rules:
- content visible by default;
- progressive enhancement only;
- respect `prefers-reduced-motion` and show the final state there;
- hover-only movement requires `(hover: hover) and (pointer: fine)`;
- name transitioned properties explicitly;
- prefer transform/opacity over layout animation;
- reuse existing route/shared runtime before adding another JS motion layer.

## WordPress Contract

- PHP-rendered semantic HTML, CSS, vanilla JS, native browser APIs.
- Follow `inc/enqueue.php`; do not inject a second stylesheet at runtime.
- No Elementor/Divi or frontend framework unless explicitly requested.
- Preserve tracking attributes and accessible focus/keyboard states.

## Verification

Run deterministic guards before visual judgement:

```bash
python3 scripts/audit-css-architecture.py
python3 scripts/audit-legacy-nx-css.py
bash scripts/lint-css-spacing.sh
bash scripts/lint-css-motion.sh
```

For layout-sensitive work, use the existing `layout-audit.mjs` against the real route with the relevant `--expect` selector and `--expect-token`. Expand disclosures when the component must be measured open.

A change is complete only when architecture does not regress, reduced motion works, keyboard/touch states remain usable, and the page still communicates its primary decision without relying on animation.
