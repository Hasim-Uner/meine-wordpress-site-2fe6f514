# Motion & Interaction Design Reference

Animation and micro-interaction patterns for B2B WordPress sites.
All patterns are CSS-first, WordPress-compatible, and performance-budgeted.

---

## Motion Budget

**Total page load animation time: < 2 seconds.**

| Animation Type        | Duration    | When                    | Priority |
|-----------------------|-------------|-------------------------|----------|
| Hero entrance         | 600–800ms   | Page load               | Critical |
| Scroll reveals        | 400–600ms   | On scroll into view     | High     |
| Number counters       | 1200–1800ms | On scroll into view     | Medium   |
| Hover states          | 120–200ms   | On hover                | High     |
| Focus states          | 120ms       | On focus                | Critical |
| Accordion open/close  | 300ms       | On click                | Medium   |
| Page transitions      | 200–350ms   | On navigation           | Low      |
| CTA attention pulse   | 2000ms      | After 5s idle, once     | Low      |

---

## 1. Page Load: Hero Entrance

The hero entrance is the first impression. It must feel intentional and confident.

### Staggered Reveal Pattern

```css
/* Hero container */
.hero {
  overflow: hidden; /* Prevent layout shift during animation */
}

/* Each element fades in + slides up with increasing delay */
.hero__overline {
  opacity: 0;
  transform: translateY(16px);
  animation: heroReveal 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
}

.hero__title {
  opacity: 0;
  transform: translateY(20px);
  animation: heroReveal 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards;
}

.hero__subtitle {
  opacity: 0;
  transform: translateY(16px);
  animation: heroReveal 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.35s forwards;
}

.hero__cta-group {
  opacity: 0;
  transform: translateY(16px);
  animation: heroReveal 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.5s forwards;
}

.hero__proof {
  opacity: 0;
  transform: translateY(12px);
  animation: heroReveal 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.65s forwards;
}

@keyframes heroReveal {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Reduced motion: instant display */
@media (prefers-reduced-motion: reduce) {
  .hero__overline,
  .hero__title,
  .hero__subtitle,
  .hero__cta-group,
  .hero__proof {
    opacity: 1;
    transform: none;
    animation: none;
  }
}
```

### Hero Background Glow

A subtle radial gradient that adds depth without distraction.

```css
.hero::before {
  content: '';
  position: absolute;
  top: -20%;
  left: 50%;
  transform: translateX(-50%);
  width: 800px;
  height: 600px;
  background: radial-gradient(
    ellipse,
    hsla(var(--accent-hsl) / 0.08) 0%,
    transparent 70%
  );
  pointer-events: none;
  z-index: 0;
}
```

---

## 2. Scroll Reveal System

### Base CSS Classes

```css
/* ─── Fade Up (Default) ─── */
.reveal {
  opacity: 0;
  transform: translateY(24px);
  transition:
    opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1),
    transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.reveal.is-visible {
  opacity: 1;
  transform: translateY(0);
}

/* ─── Fade In (No movement) ─── */
.reveal--fade {
  opacity: 0;
  transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  transform: none;
}

.reveal--fade.is-visible {
  opacity: 1;
}

/* ─── Scale Up ─── */
.reveal--scale {
  opacity: 0;
  transform: scale(0.95);
  transition:
    opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1),
    transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.reveal--scale.is-visible {
  opacity: 1;
  transform: scale(1);
}

/* ─── Stagger Children ─── */
.reveal-stagger > * {
  opacity: 0;
  transform: translateY(20px);
  transition:
    opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1),
    transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.reveal-stagger.is-visible > *:nth-child(1) { transition-delay: 0ms; opacity: 1; transform: translateY(0); }
.reveal-stagger.is-visible > *:nth-child(2) { transition-delay: 80ms; opacity: 1; transform: translateY(0); }
.reveal-stagger.is-visible > *:nth-child(3) { transition-delay: 160ms; opacity: 1; transform: translateY(0); }
.reveal-stagger.is-visible > *:nth-child(4) { transition-delay: 240ms; opacity: 1; transform: translateY(0); }
.reveal-stagger.is-visible > *:nth-child(5) { transition-delay: 320ms; opacity: 1; transform: translateY(0); }
.reveal-stagger.is-visible > *:nth-child(6) { transition-delay: 400ms; opacity: 1; transform: translateY(0); }

/* ─── Reduced Motion ─── */
@media (prefers-reduced-motion: reduce) {
  .reveal,
  .reveal--fade,
  .reveal--scale,
  .reveal-stagger > * {
    opacity: 1;
    transform: none;
    transition: none;
  }
}
```

### JavaScript (Intersection Observer)

```js
/**
 * Scroll Reveal Observer
 * Lightweight (~500 bytes), no dependencies.
 * Add class="reveal" to any element to animate on scroll.
 */
(function() {
  'use strict';

  // Bail if reduced motion preferred
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.querySelectorAll('.reveal, .reveal-stagger').forEach(function(el) {
      el.classList.add('is-visible');
    });
    return;
  }

  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -60px 0px'
  });

  // Observe all reveal elements
  document.querySelectorAll('.reveal, .reveal-stagger').forEach(function(el) {
    observer.observe(el);
  });
})();
```

**WordPress Integration**: Enqueue this as a deferred script in your child theme's `functions.php`:

```php
function theme_enqueue_reveal_script() {
    wp_enqueue_script(
        'scroll-reveal',
        get_stylesheet_directory_uri() . '/js/scroll-reveal.js',
        array(),
        '1.0',
        array('strategy' => 'defer', 'in_footer' => true)
    );
}
add_action('wp_enqueue_scripts', 'theme_enqueue_reveal_script');
```

---

## 3. Number Counter Animation

For stat bars and proof sections. Numbers count up from 0.

```js
/**
 * Counter Animation
 * Triggers on scroll into view. Supports integers, decimals, and prefixes/suffixes.
 * Usage: <span class="counter" data-target="83" data-prefix="-" data-suffix="%">0</span>
 */
(function() {
  'use strict';

  function animateCounter(el) {
    var target = parseFloat(el.dataset.target);
    var prefix = el.dataset.prefix || '';
    var suffix = el.dataset.suffix || '';
    var decimals = (el.dataset.target.includes('.')) ? el.dataset.target.split('.')[1].length : 0;
    var duration = 1500; // ms
    var startTime = null;

    function easeOutQuart(t) { return 1 - Math.pow(1 - t, 4); }

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var current = target * easeOutQuart(progress);
      el.textContent = prefix + current.toFixed(decimals) + suffix;

      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = prefix + target.toFixed(decimals) + suffix;
      }
    }

    requestAnimationFrame(step);
  }

  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('.counter').forEach(function(el) {
    observer.observe(el);
  });
})();
```

---

## 4. Hover & Interactive States

### Card Hover

```css
/* Dark mode: border brightens + background shifts */
.card {
  transition:
    border-color var(--duration-fast) var(--ease-default),
    background-color var(--duration-fast) var(--ease-default),
    transform var(--duration-normal) var(--ease-default),
    box-shadow var(--duration-normal) var(--ease-default);
}

.card:hover {
  border-color: var(--border-default);
  background: var(--bg-elevated);
}

/* Light mode: shadow elevation + subtle lift */
[data-theme="light"] .card:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}
```

### Button Hover

```css
.btn-primary {
  transition:
    background-color var(--duration-fast) var(--ease-default),
    transform var(--duration-fast) var(--ease-default),
    box-shadow var(--duration-fast) var(--ease-default);
}

.btn-primary:hover {
  background: var(--accent-hover);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px hsla(var(--accent-hsl) / 0.25);
}

.btn-primary:active {
  transform: translateY(0);
  box-shadow: none;
}
```

### Link Hover (Underline Animation)

```css
.link {
  position: relative;
  color: var(--accent);
  text-decoration: none;
}

.link::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 1px;
  background: currentColor;
  transition: width var(--duration-normal) var(--ease-default);
}

.link:hover::after {
  width: 100%;
}
```

### Navigation Link Active Indicator

```css
.nav-link {
  position: relative;
  color: var(--text-secondary);
  transition: color var(--duration-fast) var(--ease-default);
}

.nav-link:hover,
.nav-link.is-active {
  color: var(--text-primary);
}

.nav-link.is-active::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  width: 16px;
  height: 2px;
  background: var(--accent);
  border-radius: var(--radius-full);
}
```

---

## 5. Focus States (Accessibility)

```css
/* Visible focus ring for keyboard navigation */
:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 2px;
  border-radius: var(--radius-sm);
}

/* Remove outline for mouse clicks */
:focus:not(:focus-visible) {
  outline: none;
}

/* Custom focus ring for inputs */
input:focus-visible,
textarea:focus-visible,
select:focus-visible {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px hsla(var(--accent-hsl) / 0.2);
  outline: none;
}
```

---

## 6. Accordion / FAQ Animation

```css
.faq-item {
  border-bottom: 1px solid var(--border-subtle);
}

.faq-question {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding: var(--space-5) 0;
  font-size: var(--text-base);
  font-weight: var(--weight-medium);
  color: var(--text-primary);
  background: none;
  border: none;
  cursor: pointer;
  text-align: left;
  transition: color var(--duration-fast) var(--ease-default);
}

.faq-question:hover {
  color: var(--accent);
}

.faq-icon {
  flex-shrink: 0;
  width: 20px;
  height: 20px;
  color: var(--text-tertiary);
  transition: transform var(--duration-normal) var(--ease-default);
}

.faq-item.is-open .faq-icon {
  transform: rotate(45deg);
}

.faq-answer {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows var(--duration-normal) var(--ease-default);
}

.faq-item.is-open .faq-answer {
  grid-template-rows: 1fr;
}

.faq-answer__inner {
  overflow: hidden;
}

.faq-answer__content {
  padding-bottom: var(--space-6);
  color: var(--text-secondary);
  font-size: var(--text-base);
  line-height: var(--leading-normal);
  max-width: var(--measure-base);
}
```

```js
/* FAQ Accordion — lightweight, accessible */
document.querySelectorAll('.faq-question').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var item = this.closest('.faq-item');
    var isOpen = item.classList.contains('is-open');

    // Close all others (single-open mode)
    document.querySelectorAll('.faq-item.is-open').forEach(function(openItem) {
      openItem.classList.remove('is-open');
      openItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
    });

    // Toggle current
    if (!isOpen) {
      item.classList.add('is-open');
      this.setAttribute('aria-expanded', 'true');
    }
  });
});
```

---

## 7. CTA Attention Pulse

A subtle one-time pulse to draw attention to the primary CTA after the user has been idle.

```css
@keyframes ctaPulse {
  0%, 100% { box-shadow: 0 0 0 0 hsla(var(--accent-hsl) / 0.4); }
  50%      { box-shadow: 0 0 0 8px hsla(var(--accent-hsl) / 0); }
}

.btn-primary--pulse {
  animation: ctaPulse 2s ease-in-out 1;
}
```

```js
/* Trigger pulse once after 5s of no scroll/click */
var pulseTimer;
function resetPulseTimer() {
  clearTimeout(pulseTimer);
  pulseTimer = setTimeout(function() {
    var cta = document.querySelector('.hero .btn-primary');
    if (cta && !cta.classList.contains('btn-primary--pulse')) {
      cta.classList.add('btn-primary--pulse');
    }
  }, 5000);
}

['scroll', 'click', 'mousemove'].forEach(function(event) {
  document.addEventListener(event, resetPulseTimer, { passive: true });
});
resetPulseTimer();
```

---

## 8. Dark/Light Mode Transition

Smooth color transition when toggling themes.

```css
/* Apply to root for smooth theme switching */
html.theme-transitioning,
html.theme-transitioning *,
html.theme-transitioning *::before,
html.theme-transitioning *::after {
  transition:
    background-color 0.3s ease,
    color 0.3s ease,
    border-color 0.3s ease,
    box-shadow 0.3s ease !important;
}
```

```js
function toggleTheme() {
  var html = document.documentElement;
  html.classList.add('theme-transitioning');

  var current = html.getAttribute('data-theme');
  var next = current === 'dark' ? 'light' : 'dark';

  html.setAttribute('data-theme', next);
  localStorage.setItem('theme', next);

  // Remove transition class after animation completes
  setTimeout(function() {
    html.classList.remove('theme-transitioning');
  }, 350);
}
```

---

## Performance Rules

1. **No animation libraries** (GSAP, Anime.js, Framer Motion). Pure CSS + vanilla JS only.
2. **Total JS for all animations: < 3KB** (minified, gzipped).
3. **Use `transform` and `opacity` only** for animations — they don't trigger layout/paint.
4. **Never animate**: `width`, `height`, `top`, `left`, `margin`, `padding`, `font-size`.
5. **`will-change`**: Only add to elements that are actively animating. Remove after.
6. **Intersection Observer > scroll listeners**: Never use `scroll` event for reveal animations.
7. **`prefers-reduced-motion`**: Always respected. No exceptions.
8. **No animation on mobile that isn't on desktop**: Consistency across devices.
9. **Never `transition: all`**: It animates every property that ever changes,
   including layout and paint work nobody asked for. Name the properties.
10. **Never bare `ease-in`**: It accelerates from a standstill, so entering
    elements look sluggish. Use `var(--ease-default)`; `ease-out` for exits.

---

## Enforcement

Rules 4, 7, 9, and 10 are checked by `scripts/lint-css-motion.sh`, which runs in
CI and can be run by hand:

```bash
bash scripts/lint-css-motion.sh
```

It fails the build on `transition: all`, on bare `ease-in`, and on a stylesheet
that animates without a `prefers-reduced-motion` block. Files that predate the
guard sit in `BASELINE_REDUCED_MOTION` inside the script — that list may shrink,
never grow.

Slow transitions, hardcoded timings, and layout-property transitions are
reported without failing, because a 900ms scroll reveal is a choice and a 200ms
hover is not. Mark a deliberate exception with `lint-css-motion: allow` on the
line or the line above it.
