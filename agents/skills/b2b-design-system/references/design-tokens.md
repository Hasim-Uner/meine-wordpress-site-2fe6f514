# Design Tokens Reference

Complete token definitions for the B2B Premium Design System.
Import these as CSS custom properties in your project root.

Project override for this repo: use the logo copper `#b46a3c` (`hsl(23 50% 47%)`) as the accent instead of the generic reference red.

---

## Color Philosophy: Monochrom-Warm mit Rot-Akzent

The palette is built on three neutral axes plus one accent:
- **Black**: Deep, warm blacks with minimal brown undertone (not cold blue-black)
- **Silver**: Cool metallic grays for structure, borders, secondary text
- **Brown**: Warm mid-tones for surface elevation, hover warmth, subtle depth
- **Red**: The ONLY chromatic color. Used exclusively for CTAs, active states, and emphasis. Sparingly.

The result feels like: polished concrete, brushed steel, dark leather, a single red thread.

## Complete Dark Mode Token Set

```css
[data-theme="dark"],
:root:not([data-theme="light"]) {
  /* ─── Backgrounds (warm black base) ─── */
  --bg-base:       hsl(30 6% 5%);        /* Deep warm black — not blue, not pure */
  --bg-surface:    hsl(30 5% 8%);        /* Cards, panels — hint of warmth */
  --bg-elevated:   hsl(30 4% 11%);       /* Hover states, active panels */
  --bg-overlay:    hsl(30 4% 14%);       /* Modals, dropdowns, tooltips */
  --bg-inset:      hsl(30 6% 3%);        /* Inset areas, code blocks — near black */
  --bg-subtle:     hsl(30 5% 7%);        /* Alternating rows, subtle sections */

  /* ─── Text (silver spectrum) ─── */
  --text-primary:    hsl(30 4% 90%);      /* Warm white — headlines, body */
  --text-secondary:  hsl(30 3% 58%);      /* Silver — descriptions, metadata */
  --text-tertiary:   hsl(30 2% 38%);      /* Dark silver — placeholders, disabled */
  --text-inverse:    hsl(30 6% 5%);       /* Text on accent/light backgrounds */

  /* ─── Borders (silver/steel) ─── */
  --border-subtle:   hsl(30 4% 13%);      /* Faint — card borders, dividers */
  --border-default:  hsl(30 3% 18%);      /* Medium — input borders, separators */
  --border-emphasis: hsl(30 3% 25%);      /* Strong — focus rings, active */
  --border-accent:   var(--accent);        /* Red — accent borders */

  /* ─── Brown Layer (warmth & depth) ─── */
  --brown-subtle:    hsl(28 12% 12%);     /* Subtle warm surface tint */
  --brown-muted:     hsl(28 10% 18%);     /* Warm hover states */
  --brown-medium:    hsl(25 14% 25%);     /* Active states, warm emphasis */
  --brown-text:      hsl(28 12% 50%);     /* Brown as text accent (labels, tags) */

  /* ─── Interactive States ─── */
  --hover-overlay:   hsla(30 10% 100% / 0.04);  /* Warm hover on surfaces */
  --active-overlay:  hsla(30 10% 100% / 0.08);  /* Active/pressed state */
  --focus-ring:      hsla(var(--accent-hsl) / 0.5); /* Red focus ring */

  /* ─── Accent: Red (the only chromatic color) ─── */
  --accent-hsl:     4 75% 50%;
  --accent:         hsl(4 75% 50%);       /* Confident red — not fire-truck, not wine */
  --accent-hover:   hsl(4 75% 58%);       /* Lighter on hover */
  --accent-muted:   hsl(4 30% 18%);       /* Dark red tint for backgrounds */
  --accent-subtle:  hsla(4 75% 50% / 0.1); /* Faint red wash */

  /* ─── Silver Metallic (for special elements) ─── */
  --silver-low:     hsl(30 3% 35%);       /* Dark silver — icons, dividers */
  --silver-mid:     hsl(30 3% 55%);       /* Mid silver — secondary elements */
  --silver-high:    hsl(30 2% 75%);       /* Bright silver — highlights, badges */

  /* ─── Semantic Colors (muted to match palette) ─── */
  --success:       hsl(145 40% 45%);      /* Muted green — doesn't compete with red */
  --warning:       hsl(38 70% 50%);       /* Warm amber */
  --error:         var(--accent);          /* Red IS the error color */
  --info:          hsl(210 40% 55%);      /* Muted steel-blue */

  /* ─── Gradients ─── */
  --gradient-surface:  linear-gradient(180deg, var(--bg-surface) 0%, var(--bg-base) 100%);
  --gradient-glow:     radial-gradient(600px circle at var(--mouse-x, 50%) var(--mouse-y, 30%),
                         hsla(var(--accent-hsl) / 0.05) 0%, transparent 100%);
  --gradient-hero:     radial-gradient(ellipse 80% 60% at 50% -10%,
                         hsla(var(--accent-hsl) / 0.08) 0%, transparent 70%);
  --gradient-warm:     radial-gradient(ellipse 60% 50% at 50% 50%,
                         hsla(28 12% 12% / 0.5) 0%, transparent 70%);
  --gradient-text:     linear-gradient(135deg, var(--text-primary) 0%, var(--silver-mid) 100%);
}
```

## Complete Light Mode Token Set

```css
[data-theme="light"] {
  /* ─── Backgrounds (warm stone/cream — NOT cold white) ─── */
  --bg-base:       hsl(35 12% 96%);       /* Warm off-white — like aged paper */
  --bg-surface:    hsl(35 10% 93%);       /* Stone surface — cards, panels */
  --bg-elevated:   hsl(35 14% 98%);       /* Bright cream — elevated cards (with shadow) */
  --bg-overlay:    hsl(35 14% 99%);       /* Near-white — modals, dropdowns */
  --bg-inset:      hsl(35 8% 90%);        /* Warm gray — code blocks, inset areas */
  --bg-subtle:     hsl(35 8% 92%);        /* Subtle warm gray — alternating rows */

  /* ─── Text (warm blacks & browns) ─── */
  --text-primary:    hsl(30 10% 12%);      /* Warm near-black — never pure black */
  --text-secondary:  hsl(30 6% 38%);       /* Warm dark gray — descriptions */
  --text-tertiary:   hsl(30 4% 52%);       /* Medium gray — placeholders */
  --text-inverse:    hsl(35 12% 96%);      /* Light text on dark/accent backgrounds */

  /* ─── Borders (warm grays) ─── */
  --border-subtle:   hsl(30 6% 86%);       /* Light warm border */
  --border-default:  hsl(30 5% 80%);       /* Input borders */
  --border-emphasis: hsl(30 4% 68%);       /* Focus, active */

  /* ─── Brown Layer (light mode warmth) ─── */
  --brown-subtle:    hsl(28 10% 88%);      /* Warm surface tint */
  --brown-muted:     hsl(28 8% 82%);       /* Warm hover */
  --brown-medium:    hsl(25 12% 70%);      /* Active states */
  --brown-text:      hsl(25 18% 38%);      /* Brown text accent — tags, labels */

  /* ─── Interactive States ─── */
  --hover-overlay:   hsla(30 10% 10% / 0.03);
  --active-overlay:  hsla(30 10% 10% / 0.06);
  --focus-ring:      hsla(var(--accent-hsl) / 0.3);

  /* ─── Accent: Red (deeper/richer in light mode) ─── */
  --accent-hsl:     4 72% 42%;
  --accent:         hsl(4 72% 42%);        /* Darker red for light bg contrast */
  --accent-hover:   hsl(4 72% 36%);        /* Even darker on hover */
  --accent-muted:   hsl(4 25% 92%);        /* Very faint red wash */
  --accent-subtle:  hsla(4 72% 42% / 0.06); /* Barely-there red tint */

  /* ─── Silver Metallic (light mode variants) ─── */
  --silver-low:     hsl(30 4% 65%);
  --silver-mid:     hsl(30 3% 50%);
  --silver-high:    hsl(30 2% 35%);        /* Inverted: darker = more prominent in light */

  /* ─── Shadows (PRIMARY elevation in light mode — warm-tinted) ─── */
  --shadow-xs:  0 1px 2px hsla(30 10% 10% / 0.04);
  --shadow-sm:  0 1px 3px hsla(30 10% 10% / 0.05),
                0 1px 2px hsla(30 10% 10% / 0.04);
  --shadow-md:  0 4px 6px hsla(30 10% 10% / 0.05),
                0 2px 4px hsla(30 10% 10% / 0.04);
  --shadow-lg:  0 10px 25px hsla(30 10% 10% / 0.07),
                0 4px 10px hsla(30 10% 10% / 0.04);
  --shadow-xl:  0 20px 50px hsla(30 10% 10% / 0.09),
                0 8px 20px hsla(30 10% 10% / 0.04);

  /* ─── Gradients (warm, soft) ─── */
  --gradient-surface:  linear-gradient(180deg, var(--bg-elevated) 0%, var(--bg-surface) 100%);
  --gradient-hero:     radial-gradient(ellipse 80% 50% at 50% 0%,
                         hsla(var(--accent-hsl) / 0.04) 0%, transparent 70%);
  --gradient-warm:     radial-gradient(ellipse 60% 50% at 50% 50%,
                         hsla(28 12% 80% / 0.3) 0%, transparent 70%);
}
```

---

## Typography Token Set

```css
:root {
  /* ─── Font Families ─── */
  /* Set these per project. Examples: */
  --font-display: 'Bricolage Grotesque', sans-serif;  /* Headlines */
  --font-body:    'Figtree', sans-serif;               /* Body text */
  --font-mono:    'JetBrains Mono', monospace;         /* Code, data */

  /* ─── Font Sizes (Modular Scale 1.25) ─── */
  --text-xs:    0.75rem;    /* 12px */
  --text-sm:    0.875rem;   /* 14px */
  --text-base:  1rem;       /* 16px */
  --text-md:    1.125rem;   /* 18px */
  --text-lg:    1.25rem;    /* 20px */
  --text-xl:    1.5rem;     /* 24px */
  --text-2xl:   2rem;       /* 32px */
  --text-3xl:   2.5rem;     /* 40px */
  --text-4xl:   3.25rem;    /* 52px */
  --text-5xl:   4rem;       /* 64px */

  /* ─── Font Weights ─── */
  --weight-normal:   400;
  --weight-medium:   500;
  --weight-semibold: 600;
  --weight-bold:     700;

  /* ─── Line Heights ─── */
  --leading-tight:   1.15;   /* Headlines */
  --leading-snug:    1.3;    /* Subheadlines */
  --leading-normal:  1.55;   /* Body text */
  --leading-relaxed: 1.7;    /* Long-form reading */

  /* ─── Letter Spacing ─── */
  --tracking-tight:  -0.02em;  /* Large headlines */
  --tracking-normal: 0em;      /* Body */
  --tracking-wide:   0.04em;   /* Overlines, labels */
  --tracking-wider:  0.08em;   /* ALL-CAPS labels */

  /* ─── Paragraph Width ─── */
  --measure-narrow: 45ch;   /* Narrow columns */
  --measure-base:   60ch;   /* Default body */
  --measure-wide:   75ch;   /* Wide content */
}
```

### Heading Styles (Copy-Paste Ready)

```css
h1, .h1 {
  font-family: var(--font-display);
  font-size: var(--text-4xl);
  font-weight: var(--weight-bold);
  line-height: var(--leading-tight);
  letter-spacing: var(--tracking-tight);
  color: var(--text-primary);
}

h2, .h2 {
  font-family: var(--font-display);
  font-size: var(--text-2xl);
  font-weight: var(--weight-bold);
  line-height: var(--leading-tight);
  letter-spacing: var(--tracking-tight);
  color: var(--text-primary);
}

h3, .h3 {
  font-family: var(--font-display);
  font-size: var(--text-xl);
  font-weight: var(--weight-semibold);
  line-height: var(--leading-snug);
  color: var(--text-primary);
}

.overline {
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: var(--weight-semibold);
  letter-spacing: var(--tracking-wider);
  text-transform: uppercase;
  color: var(--accent);
}

.body-large {
  font-family: var(--font-body);
  font-size: var(--text-md);
  line-height: var(--leading-normal);
  color: var(--text-secondary);
  max-width: var(--measure-base);
}
```

---

## Spacing Token Set

```css
:root {
  /* ─── Base Grid: 4px ─── */
  --space-0:   0;
  --space-1:   0.25rem;   /*  4px */
  --space-2:   0.5rem;    /*  8px */
  --space-3:   0.75rem;   /* 12px */
  --space-4:   1rem;      /* 16px */
  --space-5:   1.25rem;   /* 20px */
  --space-6:   1.5rem;    /* 24px */
  --space-8:   2rem;      /* 32px */
  --space-10:  2.5rem;    /* 40px */
  --space-12:  3rem;      /* 48px */
  --space-16:  4rem;      /* 64px */
  --space-20:  5rem;      /* 80px */
  --space-24:  6rem;      /* 96px */
  --space-32:  8rem;      /* 128px */
  --space-40:  10rem;     /* 160px */

  /* ─── Semantic Spacing ─── */
  --section-y:     var(--space-24);     /* Vertical padding between sections */
  --section-y-sm:  var(--space-16);     /* Compact sections */
  --section-y-lg:  var(--space-32);     /* Hero, CTA — premium breathing room */
  --container-x:   var(--space-6);      /* Horizontal page padding (mobile) */
  --container-max:  1200px;             /* Max content width */
  --container-narrow: 800px;            /* Text-heavy content */

  /* ─── Component Spacing ─── */
  --card-padding:    var(--space-8);
  --card-gap:        var(--space-6);
  --input-padding-x: var(--space-4);
  --input-padding-y: var(--space-3);
  --button-padding-x: var(--space-6);
  --button-padding-y: var(--space-3);
  --cta-isolation:    var(--space-12);  /* Min space around primary CTA */
}

/* ─── Layout-Primitive, wie sie im Theme wirklich heissen ─── */
/* design-system.css, Abschnitt "LAYOUT PRIMITIVES".            */
.nx-container {
  max-width: var(--nx-max-width);
  margin: 0 auto;
  padding: 0 20px;
}

.nx-section {
  padding: var(--nx-space-section) 0;
}

.nx-section-header {
  text-align: center;
  margin-bottom: var(--nx-space-2xl);
}
```

**Vorsicht bei den Namen.** Es gibt kein `.section`, `.section--compact`,
`.section--hero` oder `.container` im Theme — wer danach greift, schreibt eine
Klasse ohne Wirkung. Die Primitive heissen `.nx-*`.

Dasselbe bei den Tokens: `--section-y*` ist definiert und loest auf, hat aber
**null Verwender**. Der Abschnittsrhythmus laeuft ueber `--nx-space-section`.
Fuer neues CSS gilt die Hierarchie aus dem Spacing-Kontrakt in `SKILL.md`:
`--nx-space-*` benutzen, `--space-*` nur in `design-system.css` selbst, lokale
Namen nur als Alias.

---

## Z-Index Scale

```css
:root {
  --z-base:     0;
  --z-dropdown: 100;
  --z-sticky:   200;
  --z-overlay:  300;
  --z-modal:    400;
  --z-toast:    500;
  --z-tooltip:  600;
}
```

---

## Transition Tokens

```css
:root {
  /* ─── Durations ─── */
  --duration-fast:   120ms;
  --duration-normal: 200ms;
  --duration-slow:   350ms;
  --duration-reveal: 600ms;

  /* ─── Easings ─── */
  --ease-default: cubic-bezier(0.16, 1, 0.3, 1);   /* Smooth deceleration */
  --ease-bounce:  cubic-bezier(0.34, 1.56, 0.64, 1); /* Slight overshoot */
  --ease-spring:  cubic-bezier(0.175, 0.885, 0.32, 1.1); /* Springy */
  --ease-linear:  linear;

  /* ─── Common Transitions ─── */
  --transition-colors: color var(--duration-fast) var(--ease-default),
                        background-color var(--duration-fast) var(--ease-default),
                        border-color var(--duration-fast) var(--ease-default);
  --transition-transform: transform var(--duration-normal) var(--ease-default);
  --transition-shadow:    box-shadow var(--duration-normal) var(--ease-default);
  --transition-all:       all var(--duration-normal) var(--ease-default);
}
```
