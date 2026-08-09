---
name: b2b-design-system
description: >
  Premium UX/UI and motion-direction system for B2B WordPress websites. Use for
  pages, heroes, sections, components, design critique, visual hierarchy,
  interaction states, premium polish, or requests for more Dynamik, motion,
  animation, microinteractions, scroll effects, hover behavior, entrance
  choreography, bolder art direction, or a less static interface.
---

# B2B Premium Design & Motion Direction System

Every design decision serves two masters: aesthetic excellence and measurable conversion.

## Shared Standards

For positioning, tone, copy direction, and brand colors: read `docs/standards/BRAND_AND_COPY.md`.

Project brand accent override: `#b46a3c` (copper, HSL `23 50% 47%`).

## Before You Code: Mandatory Pre-Flight

1. Read this SKILL.md completely.
2. Read the relevant reference file:
   - Components → `references/components.md`
   - Layout, spacing, color, typography → `references/design-tokens.md`
   - Motion, Dynamik, animation, interaction states → `references/motion.md`
3. Identify the visitor decision, interaction goal, and primary content hierarchy.
   Load `wordpress-cro-content-design-audit` when motion may affect conversion.
   Load `offer-funnel-intelligence` before inventing CTA-attention mechanics.
4. For motion work, write the route's Motion Brief before choosing effects.
5. Check the mode — Dark Mode is default.

## Design Philosophy: "Engineered Elegance"

Precision-meets-warmth. Not cold minimalism. Not loud maximalism.

Benchmarks: Linear (clean density), Vercel (typographic confidence), Stripe (layered depth), Raycast (refined dark UI).

**Core Principle**: Design should feel like it was built by engineers who care deeply about craft.

## Anti-Patterns (Hard Bans)

- Purple-gradient-on-white (the #1 AI slop pattern)
- Bright/saturated colors besides the accent
- Cold blue-gray backgrounds (hue 220+) — always warm neutrals (hue 25-35)
- Pure white (#fff) or pure black (#000) — always warm-tinted
- Inter/Roboto/Arial as primary typeface
- Card grids with identical border-radius and shadows
- Blue CTA buttons — CTAs use the accent color, always
- Gradient text on light backgrounds
- More than 2 font families
- Mixing rounded and sharp corners on the same page
- Red/accent used for decoration or large areas (< 5% surface only)

## Design Tokens (Summary)

Full specs in `references/design-tokens.md`. Key rules:

- **Typography**: Modular scale (ratio 1.25). Max 2 font families. Never use Inter/Roboto/Open Sans/Poppins.
- **Spacing**: 4px base grid, 8px the preferred step. Section padding: 80-128px vertical. More space = more premium.
- **Color**: Monochrom-warm + one chromatic accent. Three neutral axes (black, silver, brown) + accent.
- **Border-radius**: One personality per project (sharp/soft/round). No mixing.
- **Shadows**: Dark mode uses border+bg elevation. Light mode uses warm-tinted layered shadows.

## Spacing-Kontrakt

Das Raster ist **4px**, bevorzugter Schritt **8px**. Die `--space-*`-Skala in
`blocksy-child/assets/css/design-system.css` ist 4px-basiert, `layout-audit.mjs`
prüft gegen denselben Wert.

**Token-Hierarchie — von oben nach unten benutzen:**

1. `--nx-space-*` — die semantischen Namen. Diese gehören in Seiten-CSS.
2. `--space-*` — die rohe Skala. Nur in `design-system.css` selbst.
3. Seitenlokale Namen — **nur als Alias** auf die obigen, nie mit neuen Zahlen.

Das ist heute die größte Lücke im System: 45 von 51 Stylesheets benutzen keinen
einzigen gemeinsamen Spacing-Token. Genau deshalb laufen die Abstände von Seite
zu Seite auseinander. Neue lokale Skalen lässt der Guard nicht mehr durch.

**Innenabstände sind symmetrisch**, außer mit begründeter Ausnahme. `padding: 0 2rem 2rem`
an einem Textkasten heißt: die erste Zeile startet bündig an der Oberkante. Wenn
darüber ein Rahmen, ein Focus-Ring oder ein Hintergrundwechsel liegt, klebt der
Text daran. Achtung bei `width: 100%`-Buttons in `overflow: hidden`-Containern:
ein positiver `outline-offset` wird seitlich weggeschnitten und die Unterkante
des Rings bleibt als Strich quer durch die Karte stehen.

**Lange deutsche Komposita brauchen eine Breitenprüfung.** Steht ein Wert oder
eine Überschrift in einer festen Rasterzelle, muss die Zelle das **längste Wort
bei maximaler Schriftgröße** tragen. Das ist keine Theorie — genau daran sind
„entscheidungsfähig" und „PV-Installationsbetrieb" zerbrochen. Rechnen statt
schätzen: Zellbreite minus Innenabstand ergibt die Textbox, dagegen die
gerenderte Wortbreite messen. Reicht es nicht, sinkt der `font-size`-Cap oder
die Zelle wird breiter — die Copy bleibt.

Blocksy setzt im Parent `body { overflow-wrap: break-word }`. Ein zu langes Wort
läuft deshalb nicht sichtbar über, sondern bricht am letzten passenden Zeichen.
Der Fehler sieht dann aus wie ein Umbruchproblem, ist aber ein Breitenproblem.
`hyphens: auto` ist das Netz für Textzoom, kein Ersatz für die Rechnung.

Guard vor dem Push, CI führt ihn auch aus:

```bash
bash scripts/lint-css-spacing.sh
```

## CRO Architecture

Conversion hierarchy for every B2B page:

1. **HERO** — Capture attention, state value (5-8 sec window)
2. **PROOF** — Remove doubt (logos, stats, case study teaser)
3. **MECHANISM** — Show how it works (3-step process)
4. **OFFER** — Define what they get
5. **OBJECTION** — Handle resistance (FAQ)
6. **FINAL CTA** — Last chance with clarity

### CRO Rules (Non-Negotiable)

- One primary CTA per page. Repeat it, don't compete with it.
- CTA must be highest-contrast element. Min 7:1 against background.
- Above-fold: headline, sub-headline, primary CTA, one trust signal.
- Form fields: max 3 for first contact.
- Negative space around CTA: min 48px padding.
- Mobile: sticky CTA after scroll past hero.
- Page interactive in <2.5s.

## Motion (Summary)

Full specs in `references/motion.md`. Core rules:

- Decide the route's motion intensity (`0`–`3`) and one motion thesis before code.
- Use motion for feedback, continuity, hierarchy, or one earned focal moment.
  Generic fade-and-rise on every section is not a direction.
- Prefer the shared `NexusCore` and canonical `.nx-*` states. Add a route-local
  system only when the route has a genuinely unique narrative interaction.
- Keep content visible by default. Add an element-local pending state only after
  capability and preference checks; no-JS and unsupported APIs stay visible.
- Respect `prefers-reduced-motion`, including smooth scrolling, counters, loops,
  and long-lived interactions. Reduced motion must show the final state.
- Require progressive enhancement and no new dependency when the current stack
  can express the effect. Let `modern-web-guidance` select the exact browser API
  and fallback.
- Gate hover-only movement with `(hover: hover) and (pointer: fine)` and specify
  keyboard, touch, loading, success, error, expanded, and disabled states.
- Never `transition: all` — name the properties that actually change.
- Never bare `ease-in` — entrances use `var(--nx-ease)`, exits are faster.

**Role boundary:** `wordpress-cro-content-design-audit` decides whether motion
helps the decision; `offer-funnel-intelligence` owns CTA economics. This skill
defines what should move and how it should feel. `modern-web-guidance` selects
the browser API and fallback. `page-speed-audit` verifies the rendered CWV and
payload result. `landing-page-builder` applies the decision to a campaign route.

Run the guard before pushing frontend motion; CI runs it too. After changing the
guard or its scanner, run the regression suite as well:

```bash
bash scripts/lint-css-motion.sh
bash agents/skills/b2b-design-system/tests/run-motion-guard-tests.sh
```

## WordPress / Blocksy Notes

- Base theme: Blocksy. Custom CSS via Customizer or child theme.
- Follow the repository enqueue contract. Prefer deferred route scripts; do not
  add route-local inline behavior or a second generic motion runtime.
- No Elementor/Divi. Gutenberg blocks + custom CSS.

### Performance Budgets

| Metric | Target | Hard Limit |
|--------|--------|------------|
| LCP | < 1.5s | < 2.5s |
| INP | < 100ms | < 200ms |
| CLS | < 0.05 | < 0.1 |
| Total Weight | < 800KB | < 1.5MB |
| JS Bundle | < 100KB | < 200KB |
| Web Fonts | 2 weights | 3 weights |

## Messen statt beurteilen

Dieser Skill definiert das **Soll**. Ob eine gerenderte Seite es einhält, ist
eine Messung, kein Urteil — „sieht gut aus" hat heute schon einen 1px-Versatz
und einen Umbruch im Währungszeichen durchgelassen.

```bash
PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 npm i --no-save playwright   # einmalig

node agents/skills/b2b-design-system/scripts/layout-audit.mjs <url> \
  --expect=.wp-agentur-page-wrapper --expect-token=--ag-bg \
  --expand --shot=/tmp/audit.png
```

Misst horizontalen Überlauf, abgeschnittenen Inhalt, Tap-Targets unter 44×44,
typografische Waisen, am Rand klebenden Text, Fast-Ausrichtungen (1–7 px neben
einer Flucht) und Abstände neben dem 4px-Raster. Zeichnet die Fundstellen in den
Screenshot. Ohne Baseline: jede Prüfung ist eine absolute Zusicherung, es gibt
nichts zu pflegen und nichts abzunicken.

Überlauf, abgeschnittener Inhalt, Waisen und klebender Text sind **Befunde**.
Fast-Ausrichtung und Rhythmus sind Hinweise — ein bewusst gesetzter Versatz ist
erlaubt.

**`--expand` mitgeben, sonst misst das Audit nur den Startzustand.** Eingeklappte
FAQ-Antworten sind `opacity: 0` und damit für die Messung unsichtbar. Der
gemeldete Abstandsfehler auf der Agentur-Seite lag genau dort und blieb deshalb
liegen. Die Option öffnet vorher jedes `<details>` und alles mit
`aria-expanded="false"`.

**`--expect` und `--expect-token` sind nicht optional gemeint.** Die Tokens
dieser Seiten hängen an einem Wrapper (`.wp-agentur-page-wrapper`), nicht an
`:root`. Wer eine Testseite ohne ihn rendert, misst ein Layout ohne Abstände und
bekommt Befunde, die es nicht gibt. Das Audit bricht in dem Fall ab, statt zu
messen — Zahlen aus einer falsch aufgebauten Seite sind schlechter als gar keine.

Läuft bewusst nicht in der CI: `ci.yml` und `deploy.yml` rufen beide `npm ci`,
und playwright zöge dort jedes Mal einen Browser nach.

## Quality Checklist

- [ ] Typography: Max 2 fonts, modular ratio, line-height 1.4-1.6
- [ ] Color: One accent, consistent HSL, WCAG AA contrast
- [ ] Spacing: 4px grid (8px preferred), generous section padding (min 80px)
- [ ] Spacing: `var(--nx-space-*)` consumed, no new local scale
- [ ] Spacing: symmetric padding on text boxes, no collapsed top edge
- [ ] Longest German compound fits its grid cell at max font size
- [ ] Border-radius: Consistent, one personality
- [ ] CTA: Highest contrast, 48px+ padding, visible above fold
- [ ] Mobile: Responsive, CTA accessible without scrolling
- [ ] Performance: CSS-first motion, no unapproved runtime dependency, web fonts <= 2 weights
- [ ] Motion Brief records purpose, intensity, focal moment, variants, and acceptance criteria
- [ ] Motion: reduced-motion, keyboard, touch, mobile, and no-JS final states verified
- [ ] Motion: shared `.nx-*` pattern reused or route-local exception justified
- [ ] Motion guard green: `bash scripts/lint-css-motion.sh`
- [ ] Spacing guard green: `bash scripts/lint-css-spacing.sh`
- [ ] Layout audit green on hard findings, run with `--expand`
- [ ] No anti-patterns from Hard Bans list
- [ ] Dark/Light: Both modes tested
