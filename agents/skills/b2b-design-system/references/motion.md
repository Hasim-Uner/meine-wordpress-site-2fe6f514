# Motion Direction & Interaction Standard

Use this reference when a page should feel more dynamic, when adding or
reviewing animation, or when changing an interactive component. Motion must
improve orientation, feedback, hierarchy, or authored character without making
the visitor wait for the interface.

## Contents

1. [Ownership](#ownership)
2. [Motion Direction Pass](#motion-direction-pass)
3. [Decision gate and intensity](#decision-gate-and-intensity)
4. [Choreography and timing](#choreography-and-timing)
5. [Canonical runtime patterns](#canonical-runtime-patterns)
6. [Interaction state contract](#interaction-state-contract)
7. [Progressive enhancement and accessibility](#progressive-enhancement-and-accessibility)
8. [Performance rules](#performance-rules)
9. [Pattern decisions](#pattern-decisions)
10. [Verification](#verification)

## Ownership

- CRO decides **whether** motion helps the visitor understand or act.
- This skill defines **what moves**, the hierarchy, and the intended feel.
- `modern-web-guidance` chooses the browser API, feature detection, and fallback.
- `page-speed-audit` verifies rendered CWV and transfer/execution cost.
- `landing-page-builder` applies the brief to a campaign route.

Do not let a route-local effect become a second global design system. Do not let
a generic design trend override the route's conversion job.

## Motion Direction Pass

Write this brief before choosing CSS properties or JavaScript APIs:

```text
Route / surface:
Visitor decision and primary CTA:
Motion intensity (0–3):
Motion thesis (one sentence):
Focal moment (one or none):
Continuity to explain:
Feedback states to acknowledge:
Explicitly static areas:
Maximum simultaneous moving groups:
Runtime and payload delta:
CWV baseline and post-change evidence:
Mobile / coarse-pointer variant:
Reduced-motion final state:
No-JS / unsupported-API final state:
Acceptance checks:
```

The motion thesis must be specific to the page. “Fade sections in on scroll” is
an implementation habit, not a thesis. A useful thesis names the relationship
or feeling, for example: “The diagnostic path resolves from uncertainty into a
single next step; supporting sections remain quiet.”

If no meaningful focal moment exists, record `none`. Motion intensity is not a
quality score; level 1 can be the premium choice.

## Decision gate and intensity

For every proposed animation, answer:

1. What becomes clearer: action feedback, state, spatial relationship,
   hierarchy, or one brand-defining moment?
2. How often will the visitor encounter it?
3. What is lost if the animation is removed?
4. What do touch, keyboard, reduced-motion, no-JS, and unsupported browsers see?
5. Can an existing shared pattern express it?

If nothing meaningful is lost when it is removed, keep the surface static.
Frequent actions must be faster and quieter than rare, authored moments. Never
delay keyboard-initiated work for choreography.

| Level | Use | Default behavior |
| --- | --- | --- |
| `0` Static | Dense reading, utility, or constrained performance | No decorative movement; state changes remain immediate and legible |
| `1` Responsive | Default B2B UI | Hover/press/focus, disclosure, loading, success, and error feedback |
| `2` Guided | Marketing route with a clear visual thesis | Level 1 plus one focal sequence and a few hierarchy reveals |
| `3` Narrative | Rare hero story or scrollytelling with explicit approval | Purpose-built sequence, measured on target devices, complete static fallback |

Do not use level 3 merely because the user asks for “more Dynamik.” Translate
that request into a clearer focal moment, stronger state feedback, or better
continuity first.

## Choreography and timing

Use a three-tier hierarchy, but do not force all tiers onto every page:

1. **Focal:** at most one authored sequence per route.
2. **Supporting:** feedback and continuity that make the interface legible.
3. **Ambient:** optional, quiet, and only when it expresses the brand; never a
   permanent competitor to the CTA or reading flow.

Prefer one rehearsed focal sequence over repeated section reveals. Do not run
multiple large moving groups in the same viewport. Keep above-fold copy and the
primary CTA available without waiting for a stagger to complete.

Use the existing tokens from `design-system.css`:

| Token | Typical job |
| --- | --- |
| `--duration-fast` (`120ms`) | press, focus, immediate acknowledgement |
| `--duration-normal` (`200ms`) | hover and routine state changes |
| `--duration-slow` (`350ms`) | disclosure, layout continuity, overlays |
| `--duration-reveal` (`600ms`) | one deliberate focal entrance |
| `--ease-default` | confident entrance and responsive state movement |
| `--ease-linear` | progress tied directly to elapsed time or scroll position |

Exit faster than entrance. Cap a sibling stagger as one bounded group; after
roughly five items, reveal the remainder together. Never turn every section into
a staggered list. Use bounce or spring-like easing only when the established
visual personality earns it, not as a default “premium” effect.

## Canonical runtime patterns

The shared authorities are:

- CSS and tokens: `blocksy-child/assets/css/design-system.css`
- JavaScript utilities: `blocksy-child/assets/js/nexus-core.js`

For new general-purpose reveal and counter work, use:

| Job | Canonical contract |
| --- | --- |
| One-time reveal | `.nx-reveal` → `.nx-visible` |
| Numeric proof | `.nx-counter` plus its final value in the markup/data contract |
| Shared timing | `--duration-*`, `--ease-*`, or their `--nx-*` aliases |

`.reveal`, `.reveal-stagger`, `.is-visible`, `.is-revealed`, and `.in` exist in
legacy route systems. Do not create another dialect. When editing one route,
migrate only that route where safe; do not bulk-rewrite unrelated behavior.
Compatibility aliases may remain until their consumers are migrated.

The current shared reveal/counter implementation predates the pending-state
contract. Do not copy its default-hidden legacy CSS into new work. When a route
touches that behavior, harden the shared utility or the scoped consumer so a
missing Intersection Observer, reduced motion, or an exception leaves the final
server-rendered state visible.

Before adding route JavaScript:

1. Search `NexusCore` for the behavior.
2. Extend the shared utility if the behavior is reusable and its contract stays
   small.
3. Keep code route-local only for a genuinely unique story or data model.
4. Do not create a second generic reveal observer.

Do not hide meaningful content in the default CSS state. After capability and
reduced-motion checks pass, JavaScript may add an element-local
`.is-motion-pending` state to an eligible, initially offscreen target. Settling
adds `.nx-visible` and removes the pending state. Missing APIs, exceptions, and
reduced motion skip pending and keep the server-rendered final state visible.
A failed script must leave readable content.

## Interaction state contract

Specify every applicable state before styling the transition. Motion may
reinforce a state, but color, copy, iconography, and ARIA must carry the meaning.

| State | Required behavior |
| --- | --- |
| Rest | Stable hierarchy and sufficient contrast |
| Hover | Optional pointer affordance; never the only discoverability cue |
| Focus-visible | Immediate visible ring; never delayed or removed for aesthetics |
| Pressed / active | Fast acknowledgement without moving the target away |
| Selected | Persistent non-motion cue and correct semantic state |
| Expanded / collapsed | Correct `aria-expanded` or native `<details>` state |
| Loading | Preserve label/context, prevent duplicate action, announce when needed |
| Success | Confirm the completed action and next step |
| Error | Identify the problem near its source; no aggressive shake by default |
| Disabled | Visibly unavailable and semantically disabled; no hover promise |

Gate hover transforms:

```css
@media (hover: hover) and (pointer: fine) {
  .component:hover {
    transform: translateY(-2px);
  }
}
```

Touch must not need hover to expose content or controls. Keyboard and pointer
activation must reach the same end state, even if their motion differs.

## Progressive enhancement and accessibility

`prefers-reduced-motion` is an alternate design, not a blanket `animation: none`
afterthought:

- Show final values and content immediately.
- Replace smooth scrolling with instant/automatic scrolling.
- Remove parallax, large spatial movement, counters, and nonessential loops.
- Keep essential state feedback, but make it brief and non-spatial.
- For long-lived components, listen for preference changes rather than reading
  the media query only once.

Resolve scroll behavior before calling the API; never hard-code literal smooth
behavior:

```js
var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
```

Autoplaying or persistent motion must:

- pause when offscreen or when `document.hidden` is true;
- expose a pause control when it carries information or persists;
- never start in the reduced-motion variant;
- preserve the same content and decision path when stopped.

Use modern APIs only as progressive enhancement:

- `@starting-style` for an entrance whose steady state already works.
- View Transitions for continuity when normal navigation remains complete.
- Scroll/View Timelines inside full `@supports` blocks with a static fallback.
- Intersection Observer with an immediate-visible fallback.

Do not add a polyfill for a decorative effect. Do not add GSAP, React motion, or
another runtime unless a level-3 brief demonstrates that CSS, WAAPI, and the
current utilities cannot express the approved sequence.

## Performance rules

- Prefer transform and opacity, but choose a property by meaning rather than by
  slogan. Measure bounded clip-path, filter, shadow, and layout transitions on
  target devices when they are justified.
- Avoid animating `width`, `height`, inset, margin, padding, and font size. Use
  FLIP, transforms, or grid disclosure where practical. A justified exception
  requires measurement and a lint reason.
- Add `will-change` only immediately before known motion and remove it when the
  motion finishes.
- Use passive scroll listeners and batch visual writes through
  `requestAnimationFrame`. Prefer Intersection Observer when the question is
  simply whether an element entered the viewport.
- Make programmatic animation interruptible. Cancel obsolete animation and
  timer work during rapid state changes.
- Bound blur, shadow, canvas, masks, and shader-like work to an isolated region.
- Do not assume a transform is cheap; verify frame stability on representative
  mobile and desktop viewports.
- Keep the default dependency delta at zero. Record any route-specific CSS/JS
  byte increase and its execution cost in the Motion Brief.
- Do not trade motion for Core Web Vitals. Recheck the route against the skill's
  LCP, INP, and CLS targets after implementation; remove or simplify the effect
  when it pushes a metric across its budget.

There is no “cumulative page animation time” budget. A reveal triggered minutes
later cannot be added meaningfully to the hero duration. Budget by interaction:
duration, delay before usefulness, simultaneous moving groups, frequency, main
thread cost, and the time until the CTA/content is usable.

## Pattern decisions

### Hero

- Level 1: static composition plus immediate control feedback.
- Level 2: one page-specific sequence; keep headline and CTA readable throughout.
- Do not copy the same overline/title/subtitle/CTA fade stagger to every route.
- Background glow is a visual layer, not motion by itself.

### Scroll reveal

- Reveal only when it clarifies hierarchy or sequence.
- Do not hide above-fold decision content while waiting for observation.
- Run once by default. Replaying on every scroll direction usually adds noise.
- Unsupported API and reduced-motion paths mark every target final immediately.

### Counters

- Put the truthful final value in the markup or data contract.
- Reduced-motion and no-support paths display the final value without counting.
- Do not animate weak or synthetic proof to make it appear stronger.

### Disclosure / FAQ

- Prefer native `<details>` when its behavior fits.
- Keep the icon and `aria-expanded`/`open` state synchronized.
- Use a grid-row disclosure only when continuity materially improves; content
  must remain reachable without animation.

### CTA attention

- Do not add an automatic pulse by default. Contrast, placement, and copy own
  CTA prominence.
- A one-time attention cue requires a concrete behavioral reason and must stop
  after interaction, offscreen, hidden, and reduced-motion conditions.

### Loading, success, and error

- Feedback motion confirms cause and result; it must not postpone the result.
- Prefer progress or a state morph over indefinite decorative spinners.
- Never use shake as the default error treatment; identify the field and error.

## Verification

Run the repository guard:

```bash
bash scripts/lint-css-motion.sh
```

After changing the guard, scanner, or baseline, run its synthetic regression
suite:

```bash
bash agents/skills/b2b-design-system/tests/run-motion-guard-tests.sh
```

For a rendered route, also run the layout audit and then verify the motion states
in a real browser:

```bash
node agents/skills/b2b-design-system/scripts/layout-audit.mjs <url> \
  --expect=<page-wrapper> --expand --shot=/tmp/layout-audit.png
```

Acceptance requires:

- normal motion at desktop and mobile widths;
- reduced motion with final content visible and no smooth-scroll surprise;
- keyboard activation and focus-visible states;
- touch/coarse-pointer behavior without hover dependency;
- unsupported Intersection Observer / enhanced API fallback;
- page-hidden/offscreen behavior for persistent motion;
- final state after interruption or rapid repeated input;
- no layout shift, horizontal overflow, or blocked CTA/content;
- no regression against the route's recorded LCP, INP, CLS, and payload budget;
- no unexplained new route-local reveal dialect.

The guard fails objective new violations and reports legacy drift separately.
Use `lint-css-motion: allow -- <reason>` or `lint-js-motion: allow -- <reason>`
on the offending line or the line above only for a measured, deliberate
exception. Baselines may shrink, never grow.
