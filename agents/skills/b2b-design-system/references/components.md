# Component Library Reference

CRO-optimized component patterns for B2B WordPress sites.
Each component includes: structure, conversion purpose, design specs, and implementation notes.

---

## Table of Contents

1. [Hero Patterns](#hero-patterns)
2. [Navigation](#navigation)
3. [Proof Bar (Logos & Stats)](#proof-bar)
4. [Feature/Benefit Sections](#feature-sections)
5. [Process/How-It-Works](#process-section)
6. [Case Study Teaser](#case-study-teaser)
7. [Testimonials](#testimonials)
8. [Pricing/Offer Architecture](#pricing)
9. [FAQ Accordion](#faq)
10. [CTA Sections](#cta-sections)
11. [Forms](#forms)
12. [Cards](#cards)
13. [Buttons](#buttons)
14. [Badges & Pills](#badges)
15. [Footer](#footer)

---

## 1. Hero Patterns {#hero-patterns}

### Conversion Job
Capture attention in 5 seconds. Communicate: who you help, what you do, why they should care. Initiate primary action.

### Pattern A: "Statement Hero" (Recommended for B2B services)

```
┌──────────────────────────────────────────┐
│  [Overline: Category / positioning]       │
│                                           │
│  H1: Bold value proposition               │
│  (max 12 words, max 2 lines)              │
│                                           │
│  Subheadline: Qualifying statement        │
│  (max 3 lines, lighter weight)            │
│                                           │
│  [Primary CTA]   [Ghost CTA]             │
│                                           │
│  [Micro-proof: "83% CPL reduction" etc.]  │
│                                           │
│  ─── ambient gradient glow behind ───     │
└──────────────────────────────────────────┘
```

**Design Specs**:
- H1: `--text-4xl` desktop, `--text-3xl` mobile. `--weight-bold`. `--leading-tight`.
- Subheadline: `--text-md` or `--text-lg`. `--text-secondary`. `max-width: 60ch`.
- Overline: `--text-sm`, uppercase, `--tracking-wider`, `--accent` color.
- CTA group: `--space-4` gap between buttons. Center or left-align (never right).
- Micro-proof: Small stat badges below CTA. `--text-sm`, `--text-tertiary`.
- Vertical rhythm: `--space-6` between overline→H1, `--space-4` H1→subheadline, `--space-8` subheadline→CTA.
- Section padding: `--section-y-lg` (min 128px top, 96px bottom).
- Background: `--gradient-hero` radial glow from top-center. Subtle, not distracting.

**CRO Rules**:
- H1 must pass the "5-second test": cover the page, show only H1 — can you tell what the company does?
- One primary CTA. One secondary (ghost/link). Never three.
- No hero images unless they directly demonstrate the product/result.
- Stats in hero = instant credibility. Use 2–4 key numbers.

### Pattern B: "Split Hero" (When visual proof is available)

```
┌────────────────────┬─────────────────────┐
│  Overline           │                     │
│  H1                 │  [Screenshot /       │
│  Subheadline        │   Dashboard /        │
│                     │   Result visual]     │
│  [CTA] [Ghost]      │                     │
│  Micro-proof        │                     │
└────────────────────┴─────────────────────┘
```

- 55/45 or 60/40 text-to-visual split.
- Visual must be a real screenshot, data visualization, or result — never a stock photo.
- On mobile: stack vertically, text first.

### Pattern C: "Metric Hero" (When numbers are the main proof)

```
┌──────────────────────────────────────────┐
│  Overline                                 │
│  H1                                       │
│  Subheadline                              │
│                                           │
│  ┌─────┐  ┌─────┐  ┌─────┐  ┌─────┐     │
│  │-83% │  │34x  │  │1750+│  │98   │     │
│  │ CPL │  │ROAS │  │Leads│  │Perf │     │
│  └─────┘  └─────┘  └─────┘  └─────┘     │
│                                           │
│  [Primary CTA]                            │
└──────────────────────────────────────────┘
```

- Stats: `--text-3xl` number, `--weight-bold`, `--accent` color.
- Label: `--text-sm`, `--text-secondary`.
- Animate numbers on load (count up from 0).

---

## 2. Navigation {#navigation}

### Conversion Job
Provide wayfinding without friction. Guide toward CTA. Don't compete with page content.

### Design Specs

```
┌──[Logo]──────[Nav Links]──────[CTA Button]──┐
```

- Height: 64px desktop, 56px mobile.
- Background: `--bg-base` with `backdrop-filter: blur(12px)` and slight transparency.
- Position: `sticky`, top 0, `--z-sticky`.
- Logo: Left-aligned. Max height 32px. SVG preferred.
- Nav links: `--text-sm`, `--weight-medium`, `--text-secondary`. Hover: `--text-primary`.
- CTA button: Compact variant of primary button. Always visible. Stands out from nav links.
- Mobile: Hamburger menu. CTA button stays visible next to hamburger.
- Border-bottom: `1px solid var(--border-subtle)`.
- On scroll: Add subtle shadow or increase border opacity.

**CRO Rules**:
- Max 5 nav links (excluding CTA).
- CTA text = action verb ("Audit starten", not "Kontakt").
- No mega-menus for service businesses. Keep it flat.
- Active page indicator: accent-colored underline or dot, not background change.

---

## 3. Proof Bar {#proof-bar}

### Conversion Job
Remove the "who is this person?" doubt. Social proof within first scroll.

### Pattern A: Logo Bar

```
┌──────────────────────────────────────────┐
│  [Overline: "Vertrauen von" / "Bekannt   │
│   aus" / "Ergebnisse für"]                │
│                                           │
│  [Logo] [Logo] [Logo] [Logo] [Logo]      │
└──────────────────────────────────────────┘
```

- Logos: Grayscale (opacity 0.5), hover: full color (opacity 1).
- In dark mode: logos should be white/light. Use CSS `filter: brightness(0) invert(1)` if needed.
- Max height: 28–36px per logo. Uniform visual weight.
- Gap: `--space-10` to `--space-12` between logos.
- Overline: `--text-xs` or `--text-sm`, `--text-tertiary`, centered.
- Section padding: `--section-y-sm` (compact, not a full section).

### Pattern B: Stat Bar

```
┌──────────────────────────────────────────┐
│  ┌──────────┐  ┌──────────┐  ┌──────────┐│
│  │   98      │  │  -83%    │  │  <0.8s   ││
│  │ Mobile    │  │  CPL-    │  │  LCP auf ││
│  │ Perf.     │  │ Reduktion│  │ Kernseiten│
│  └──────────┘  └──────────┘  └──────────┘│
└──────────────────────────────────────────┘
```

- Number: `--text-2xl` or `--text-3xl`, `--weight-bold`, `--text-primary`.
- Label: `--text-sm`, `--text-secondary`.
- Container: Subtle border or bg differentiation. Not heavy cards.
- 3–4 stats max. Render the truthful final value server-side. Count up only
  when the Motion Brief justifies it; reduced-motion and no-support paths keep
  the final value static.

---

## 4. Feature/Benefit Sections {#feature-sections}

### Conversion Job
Explain what they get. Translate features into outcomes. Build understanding of the system.

### Pattern A: "Alternating Blocks" (Best for 2–4 features)

```
┌──────────────────────────────────────────┐
│  [Text Block]            [Visual]         │
│  Overline                                 │
│  H2: Feature headline                     │
│  Body: 2-3 sentences                      │
│  • Bullet 1                               │
│  • Bullet 2                               │
│  • Bullet 3                               │
└──────────────────────────────────────────┘
┌──────────────────────────────────────────┐
│  [Visual]            [Text Block]         │
│                      Overline             │
│                      H2: Feature headline │
│                      ...                  │
└──────────────────────────────────────────┘
```

- Alternate image/text left-right.
- Visual: Screenshot, diagram, or icon illustration. Never decorative.
- Bullets: Use check marks (✓) or custom icons, not default bullets.
- Max 3 bullets per feature. Each bullet = one benefit, max 10 words.

### Pattern B: "Feature Grid" (Best for 4–6 features)

```
┌──────────┐  ┌──────────┐  ┌──────────┐
│  [Icon]   │  │  [Icon]   │  │  [Icon]   │
│  Title    │  │  Title    │  │  Title    │
│  2-line   │  │  2-line   │  │  2-line   │
│  desc.    │  │  desc.    │  │  desc.    │
└──────────┘  └──────────┘  └──────────┘
```

- Grid: 3 columns desktop, 2 tablet, 1 mobile.
- Icon: 32–40px, `--accent` color or `--text-secondary`. Consistent style (all outlined or all filled).
- Title: `--text-lg`, `--weight-semibold`.
- Description: `--text-sm` or `--text-base`, `--text-secondary`. Max 2 lines.
- Card: Subtle border. No heavy shadow. Consistent padding `--card-padding`.

### Pattern C: "Comparison" (Best for "old way vs. new way")

```
┌─────────────────┐  ┌─────────────────┐
│  ❌ Modell A:     │  │  ✓ Modell B:     │
│  The Old Way     │  │  The New Way     │
│                  │  │                  │
│  • Pain point 1  │  │  • Benefit 1     │
│  • Pain point 2  │  │  • Benefit 2     │
│  • Pain point 3  │  │  • Benefit 3     │
└─────────────────┘  └─────────────────┘
```

- Left card: Muted, `--bg-inset`, `--text-secondary`, crossed-out or red indicators.
- Right card: Elevated, `--border-accent`, `--accent` checkmarks, `--bg-surface`.
- The contrast in visual treatment IS the argument.

---

## 5. Process / How-It-Works {#process-section}

### Conversion Job
Reduce uncertainty. Show the path from "interested" to "result". Make the next step feel safe.

### Pattern: "Numbered Steps"

```
┌──────────────────────────────────────────┐
│  [Section Overline]                       │
│  H2: Three steps to...                    │
│                                           │
│  ①──────────── ②──────────── ③            │
│  Growth Audit   Blueprint    Umsetzung    │
│  Description    Description  Description  │
└──────────────────────────────────────────┘
```

- Step numbers: `--text-3xl`, `--accent`, `--weight-bold`. Or circled numbers.
- Connector line: 1px `--border-subtle` between steps. Horizontal on desktop, vertical on mobile.
- 3 steps ideal. 4 max. Never 5+.
- Each step: Title (`--text-lg`, `--weight-semibold`) + 1–2 sentence description.
- Active/current step can have accent border or glow.

---

## 6. Case Study Teaser {#case-study-teaser}

### Conversion Job
Prove results with specifics. Named clients + real numbers = highest trust signal.

### Pattern

```
┌──────────────────────────────────────────┐
│  [Tag: "B2B Leadgen · WordPress · 12M"]   │
│                                           │
│  H3: Client Name / Project Name           │
│  Brief challenge description (1-2 lines)  │
│                                           │
│  ┌────┐  ┌────┐  ┌────┐  ┌────┐          │
│  │1750│  │-83%│  │12% │  │34x │          │
│  │Lead│  │CPL │  │Sale│  │ROAS│          │
│  └────┘  └────┘  └────┘  └────┘          │
│                                           │
│  [Read Case Study →]                      │
└──────────────────────────────────────────┘
```

- Container: `--bg-surface`, `--border-subtle`, generous padding `--space-10`.
- Stats: Grid row, `--text-2xl` numbers, `--accent` or `--text-primary`.
- Link: Text link with arrow, not a button. `--accent` color.
- Tag/category: `--text-xs`, pill badge, `--bg-elevated`.

---

## 7. Testimonials {#testimonials}

### Conversion Job
Third-party validation. Real words from real people remove skepticism.

### Pattern (Single Testimonial Block — NOT a carousel)

```
┌──────────────────────────────────────────┐
│  "Quote text, max 3 lines. Direct,        │
│   specific, result-oriented."             │
│                                           │
│  [Avatar]  Name · Title · Company         │
└──────────────────────────────────────────┘
```

- Quote: `--text-lg` or `--text-xl`, `--font-body`, italic or regular. `--text-primary`.
- NO quotation mark decorations (❝❞). The layout makes it clear it's a quote.
- Avatar: 40–48px, rounded-full. Real photo, not placeholder.
- Attribution: `--text-sm`, `--text-secondary`.
- Container: Subtle `--bg-surface` or left border `--border-accent`.
- **No carousels**. Show 1–3 testimonials statically. Carousels reduce engagement.

---

## 8. Pricing / Offer Architecture {#pricing}

### Conversion Job
Make the decision simple. Show clear tiers. Highlight the recommended option.

### Pattern: "Tiered Cards"

```
┌──────────┐  ┌══════════════┐  ┌──────────┐
│  Tier 1   │  ║  Tier 2       ║  │  Tier 3   │
│  Name     │  ║  Name         ║  │  Name     │
│  Price    │  ║  Price        ║  │  Price    │
│           │  ║  ← empfohlen  ║  │           │
│  • feat 1 │  ║  • feat 1     ║  │  • feat 1 │
│  • feat 2 │  ║  • feat 2     ║  │  • feat 2 │
│           │  ║  • feat 3     ║  │  • feat 3 │
│  [CTA]    │  ║  [CTA ████]   ║  │  [CTA]    │
└──────────┘  └══════════════┘  └──────────┘
```

- Recommended tier: `--border-accent` (2px), slight scale (1.02), "Empfohlen" badge.
- Other tiers: `--border-subtle`.
- CTA in recommended tier: Filled/primary. Others: Ghost/outline.
- Feature list: Checkmarks for included, dashes for excluded. Keep aligned across tiers.
- Prices: `--text-2xl`, `--weight-bold`. Period: `--text-sm`, `--text-secondary`.

**CRO Rules for pricing**:
- Always highlight one tier (anchoring effect).
- Middle tier converts best (center-stage effect).
- "Starting at" prices reduce commitment anxiety.
- For service businesses without fixed prices: use "Ab" pricing or describe the deliverable instead.

---

## 9. FAQ Accordion {#faq}

### Conversion Job
Handle objections. Answer the questions that block the conversion decision.

### Design Specs

- Container: Full-width within content area. No card wrapper needed.
- Question: `--text-base` or `--text-lg`, `--weight-medium`, `--text-primary`.
- Answer: `--text-base`, `--text-secondary`, `max-width: var(--measure-base)`.
- Divider: `1px solid var(--border-subtle)` between items.
- Toggle icon: Minimal `+`/`−` or chevron. `--text-tertiary`. Rotates on open.
- Animation: Prefer native `<details>` plus an icon rotation. If answer motion
  materially improves continuity, follow the disclosure contract in
  `motion.md` and keep the unenhanced answer accessible.
- Open state: Question color → `--text-primary` or `--accent`.

**CRO Rules**:
- First 3 FAQs should address buying objections (price, time, fit).
- Last FAQ: "Wie starten wir?" → links to CTA.
- 5–8 FAQs max. More = information overload.

---

## 10. CTA Sections {#cta-sections}

### Conversion Job
Final conversion push. Urgency + clarity + low friction.

### Pattern: "Full-Width CTA Block"

```
┌──────────────────────────────────────────┐
│          [gradient/glow background]        │
│                                           │
│  H2: Action-oriented headline             │
│  Subline: What happens next (1 line)      │
│                                           │
│  [████ Primary CTA ████]                  │
│                                           │
│  [Micro-text: "Kein Verkaufsgespräch"]    │
└──────────────────────────────────────────┘
```

- Background: `--bg-surface` with `--gradient-glow` or subtle accent gradient.
- Generous padding: `--section-y-lg`.
- H2: `--text-2xl`, centered.
- Subline: `--text-md`, `--text-secondary`, centered.
- CTA button: Large variant (see Buttons below). Centered. Alone — no competing elements.
- Micro-text below CTA: `--text-xs`, `--text-tertiary`. Risk reversal ("Keine Kosten", "10 Minuten").
- Optional: Subtle border or background differentiation from surrounding sections.

---

## 11. Forms {#forms}

### Conversion Job
Capture the lead with minimum friction. Every field is a friction point.

### Design Specs

- Max 3 fields for initial contact: Name, Email, Company (or URL).
- Input height: 48px (touch-friendly).
- Input border: `--border-default`. Focus: `--border-accent` + `--focus-ring`.
- Input background: `--bg-inset` (slightly darker/lighter than surface).
- Label: Above input, `--text-sm`, `--weight-medium`. Never floating labels.
- Placeholder: `--text-tertiary`. Never use as label substitute.
- Error state: `--error` border + message below input. `--text-sm`.
- Submit button: Full-width on mobile. Primary button style.
- Padding inside input: `--input-padding-x` and `--input-padding-y`.

**CRO Rules**:
- Every field removed = ~5% conversion lift (industry average).
- Phone number field: Only if sales team needs it. Otherwise, skip.
- Multi-step forms: Only for complex qualification. Step indicator required.
- Success state: Clear confirmation message + next step expectation ("Sie hören innerhalb von 24h von mir").

---

## 12. Cards {#cards}

### Base Card

```css
.card {
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  padding: var(--card-padding);
  transition:
    background-color var(--duration-fast) var(--ease-default),
    border-color var(--duration-fast) var(--ease-default),
    box-shadow var(--duration-normal) var(--ease-default),
    transform var(--duration-normal) var(--ease-default);
}

/* Only interactive cards promise a hover/focus response. */
.card--interactive:focus-within {
  border-color: var(--border-default);
  background: var(--bg-elevated);
}

/* Light mode: elevation via shadow */
[data-theme="light"] .card {
  border-color: transparent;
  box-shadow: var(--shadow-sm);
}
@media (hover: hover) and (pointer: fine) {
  .card--interactive:hover {
    border-color: var(--border-default);
    background: var(--bg-elevated);
  }

  [data-theme="light"] .card--interactive:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }
}
```

---

## 13. Buttons {#buttons}

### Primary Button

```css
.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  padding: var(--button-padding-y) var(--button-padding-x);
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: var(--weight-semibold);
  line-height: 1;
  color: var(--text-inverse);
  background: var(--accent);
  border: none;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: var(--transition-colors), var(--transition-transform);
  white-space: nowrap;
}

@media (hover: hover) and (pointer: fine) {
  .btn-primary:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
  }
}

.btn-primary:active {
  transform: translateY(0);
}

/* Large variant for CTA sections */
.btn-primary--lg {
  padding: var(--space-4) var(--space-8);
  font-size: var(--text-base);
}
```

### Ghost Button (Secondary)

```css
.btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--button-padding-y) var(--button-padding-x);
  font-size: var(--text-sm);
  font-weight: var(--weight-medium);
  color: var(--text-secondary);
  background: transparent;
  border: 1px solid var(--border-default);
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: var(--transition-colors);
}

@media (hover: hover) and (pointer: fine) {
  .btn-ghost:hover {
    color: var(--text-primary);
    border-color: var(--border-emphasis);
    background: var(--hover-overlay);
  }
}
```

### Text Link Button

```css
.btn-link {
  display: inline-flex;
  align-items: center;
  gap: var(--space-1);
  font-size: var(--text-sm);
  font-weight: var(--weight-medium);
  color: var(--accent);
  background: none;
  border: none;
  cursor: pointer;
  transition: var(--transition-colors);
}

.btn-link .arrow {
  transition: transform var(--duration-fast) var(--ease-default);
}

@media (hover: hover) and (pointer: fine) {
  .btn-link:hover {
    color: var(--accent-hover);
  }

  .btn-link:hover .arrow {
    transform: translateX(4px);
  }
}
```

---

## 14. Badges & Pills {#badges}

```css
.badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-1);
  padding: var(--space-1) var(--space-3);
  font-size: var(--text-xs);
  font-weight: var(--weight-medium);
  letter-spacing: var(--tracking-wide);
  border-radius: var(--radius-full);
  white-space: nowrap;
}

.badge--accent {
  color: var(--accent);
  background: var(--accent-subtle);
}

.badge--neutral {
  color: var(--text-secondary);
  background: var(--bg-elevated);
}

.badge--outline {
  color: var(--text-secondary);
  background: transparent;
  border: 1px solid var(--border-default);
}
```

---

## 15. Footer {#footer}

### Design Specs

- Background: `--bg-base` or `--bg-inset` (slightly darker than page).
- Top border: `1px solid var(--border-subtle)`.
- Padding: `--section-y` top, `--space-8` bottom.
- Layout: 4-column grid (Logo+Description | Links | Links | CTA/Contact). 
- On mobile: Stack, logo first, CTA prominent.
- Link style: `--text-sm`, `--text-secondary`. Hover: `--text-primary`.
- Bottom bar: Copyright, legal links. `--text-xs`, `--text-tertiary`.
- Footer CTA: Optional micro-CTA (newsletter signup or "Audit starten" button).

---

## Implementation Notes for WordPress / Blocksy

### Using These Components in Blocksy

1. **Custom sections**: Use Blocksy's content blocks or Gutenberg group blocks.
2. **Custom CSS**: Add component styles via Customizer → Additional CSS or child theme.
3. **Custom classes**: Add via block editor's "Advanced" panel → Additional CSS Class.
4. **Dynamic content**: Use Blocksy hooks for injecting proof bars, CTAs at specific positions.

### Gutenberg Block Mapping

| Component          | Gutenberg Block             | Custom Class        |
|--------------------|-----------------------------|---------------------|
| Hero               | Cover or Group              | `.hero-statement`   |
| Proof Bar          | Group → Columns             | `.proof-bar`        |
| Feature Grid       | Columns (3-col)             | `.feature-grid`     |
| Process Steps      | Group → Columns             | `.process-steps`    |
| Case Study         | Group                       | `.case-study-card`  |
| Testimonial        | Group → Quote               | `.testimonial`      |
| FAQ                | Custom HTML or Plugin        | `.faq-accordion`    |
| CTA Section        | Group (full-width)           | `.cta-section`      |
| Buttons            | Buttons block               | `.btn-primary`      |

### Performance Considerations

- All component styles should be in ONE consolidated CSS file (< 50KB).
- No component should require its own JS file. Use the shared Intersection Observer.
- Images in components: Always use `loading="lazy"` except hero image (use `fetchpriority="high"`).
- Icons: Inline SVG, not icon fonts. SVG sprites for repeated icons.
