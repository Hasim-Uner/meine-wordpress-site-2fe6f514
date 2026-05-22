# Single Post — Editorial Template

Author: Haşim Üner · Stack: `single.php` + `assets/css/single-editorial.css` + `assets/js/single-editorial.js` + `inc/post-rating.php`

This template turns every blog post (`single.php`) into a world-class editorial page. Hero, sticky TOC, share rail, progress bar, rating widget, author bio, related and footer-CTA are rendered automatically. Inside `the_content()` you can drop the block classes below to upgrade individual passages.

---

## Shell (rendered automatically by `single.php`)

| Element | Where it appears |
|---|---|
| Reading progress bar | Fixed top, 3px copper gradient |
| Sticky share rail (LinkedIn / X / Mail / Copy) | Fixed left, only ≥ 1280px |
| Hero (Category · Date · Reading time · Title · Author) | `nexus-article-hero` |
| Article context bridge | `nexus-article-context` (category-aware) |
| TOC sidebar with scroll-spy | `sticky-toc` (left of content) |
| Inline mid-content CTA | Auto-injected by `blog-inline-cta.js` |
| Article-next CTAs | `nexus-article-next` |
| Rating widget | `nexus-rating` (Helpful / Not helpful + free text) |
| Blog notification subscribe | `nexus-blog-notify` |
| Author bio card | `nexus-author-bio` |
| Related content | `template-parts/related-content.php` |
| Footer CTA | `template-parts/footer-cta.php` |
| Back to top | `nexus-back-to-top` |

---

## Editor block classes

Drop into the Gutenberg **Custom HTML** block (or via `wp_kses` allowed markup).

### Lead paragraph + drop cap

```html
<p class="lead-para dropcap">Photovoltaik-Leads werden im Einkauf fast immer falsch bewertet.</p>
```

### Section divider with big chapter number

```html
<div class="section-divider">
  <div class="section-divider-num">01</div>
  <div class="section-divider-content">
    <h2 id="sektion-1">Section title</h2>
    <p class="section-divider-dek">One-line subhead that frames the section.</p>
  </div>
</div>
```

### Pull quote with attribution

```html
<blockquote class="pull-quote">
  Ein Portal-Lead für 60 € kann teuer sein. Ein eigener Lead für 180 € kann billig sein.
  <span class="pull-quote-attr">Kapitel 1 · Markt</span>
</blockquote>
```

### Formula / code-style block

Inline color helpers inside: `.accent` (copper), `.good`, `.bad`, `.warn`, `.comment`.

```html
<div class="formula-block"><span class="comment"># Cost per Order</span>
<span class="accent">CPO</span> = Leadkosten / Abschlussquote</div>
```

### Stat row (3-column)

```html
<div class="stat-row">
  <div class="stat-card">
    <div class="stat-value">€ 1.500</div>
    <div class="stat-label"><strong>Portal-Leads</strong><br>60 € CPL · 4 % Abschluss</div>
  </div>
  <!-- repeat -->
</div>
```

### Callout box with inline CTA

```html
<div class="callout">
  <div class="callout-label">Stopp · Bevor Sie weiterlesen</div>
  <div class="callout-title">Prüfen Sie, ob Ihr Zielgebiet ein eigenes Anfrage-System trägt.</div>
  <p class="callout-text">Der Marktcheck prüft Region, Projektvolumen und Vertriebsreife in 48 h.</p>
  <a href="/marktcheck/" class="callout-cta">Marktcheck starten →</a>
</div>
```

### Comparison table (3-column)

```html
<div class="comparison">
  <div class="comparison-row header">
    <div>Dimension</div><div>Portal-Miete</div><div>Eigene Strecke</div>
  </div>
  <div class="comparison-row">
    <div>Abschlussquote</div><div>~ 4-7 %</div><div>~ 12-18 %</div>
  </div>
  <!-- repeat -->
</div>
```

### Case study card

```html
<div class="casestudy">
  <div class="casestudy-header">
    <div>
      <div class="casestudy-label">Fallstudie</div>
      <div class="casestudy-name">E3 New Energy</div>
    </div>
    <div class="casestudy-meta">6 Monate<br>Vollkostenbasiert</div>
  </div>
  <div class="casestudy-stats">
    <div class="casestudy-stat">
      <div class="casestudy-stat-num">71 %</div>
      <div class="casestudy-stat-from">CPO-Reduktion</div>
      <div class="casestudy-stat-label">€ 5.000 → € 1.450</div>
    </div>
    <!-- 2 more -->
  </div>
  <blockquote class="casestudy-quote">Dieselben Verkäufer, bessere Anfragequelle.</blockquote>
</div>
```

### FAQ accordion

```html
<section class="nexus-faq">
  <ul class="faq-list">
    <li class="faq-item">
      <button class="faq-question" type="button">
        Sind gekaufte Photovoltaik-Leads grundsätzlich schlecht?
        <span class="faq-toggle"></span>
      </button>
      <div class="faq-answer"><div class="faq-answer-inner">
        Nein. Kurzfristig sinnvoll für neue Regionen oder Validierung. Gefährlich als Dauerstrategie.
      </div></div>
    </li>
    <!-- repeat -->
  </ul>
</section>
```

JS toggles `.is-open` on the `<li class="faq-item">` automatically — no inline event handlers required.

### Highlight marker

```html
<p>Der Einkauf sieht den 60-Euro-Lead. <span class="highlight">Die Geschäftsführung muss den 1.500-Euro-Auftrag sehen.</span></p>
```

### Cover flow schematic (Modell A vs. B)

Heavy block — drop above the first H2 when the article genuinely benefits.

```html
<div class="cover-schematic">
  <div class="cover-grid">
    <div class="cover-pane bad">
      <div class="cover-pane-label">Modell A · Portal-Miete</div>
      <div class="cover-pane-title">Geteilte Anfrage, geteilte Marge</div>
      <div class="cover-nodes">
        <div class="cover-node"><span class="dot"></span>Lead-Portal<span class="cover-node-meta">Aroundhome</span></div>
        <div class="cover-node"><span class="dot"></span>Anbieter A<span class="cover-node-meta">4-7 %</span></div>
      </div>
      <div class="cover-cpo">
        <div class="cover-cpo-label">Cost per Order</div>
        <div class="cover-cpo-value">€ 2.500</div>
      </div>
    </div>
    <div class="cover-divider">
      <div class="cover-divider-line"></div><div class="cover-divider-vs">VS</div><div class="cover-divider-line"></div>
    </div>
    <div class="cover-pane good">
      <div class="cover-pane-label">Modell B · Eigene Strecke</div>
      <div class="cover-pane-title">Exklusiv, eigener Besitz</div>
      <div class="cover-nodes">
        <div class="cover-node"><span class="dot"></span>Money Page<span class="cover-node-meta">First-Party</span></div>
        <div class="cover-node"><span class="dot"></span>Vertrieb<span class="cover-node-meta">~15 %</span></div>
      </div>
      <div class="cover-cpo">
        <div class="cover-cpo-label">Cost per Order</div>
        <div class="cover-cpo-value">€ 1.300</div>
      </div>
    </div>
  </div>
</div>
```

---

## Hero opt-in: decorative ghost stat

Add `data-hero-stat="71%"` and `data-hero-stat-label="Case Study"` to the `<header class="nexus-article-hero">` to render the giant transparent number top-right. Only renders ≥ 1100px.

---

## Rating widget

- Renders by default on every single post.
- POSTs to `wp-json/nexus/v1/post-rating` with WP REST nonce.
- Stores aggregated counters + last 25 free-text feedbacks as post meta.
- Admin: post-edit screen has a side meta-box "Artikel-Bewertungen"; the posts list has a sortable column.
- Downstream hook for CRM/Brevo/n8n:
  ```php
  add_action( 'nexus_post_rating_received', function ( $post_id, $rating, $feedback, $context ) {
      // forward to Brevo, n8n, etc.
  }, 10, 4 );
  ```

GA4 events pushed to `window.dataLayer`:

| Event | Payload |
|---|---|
| `post_rating` | `rating`, `post_id` |
| `post_rating_feedback` | `rating`, `post_id`, `feedback_length` |
| `post_share` | `method`, `post_id` |

---

## Performance notes

- CSS loaded only on `is_singular('post')` via `inc/enqueue.php`.
- JS deferred (via `hu_mark_script_for_defer`).
- `IntersectionObserver` used for reveals; no continuous scroll work beyond a single `requestAnimationFrame`-throttled progress bar updater.
- All shell elements respect `prefers-reduced-motion`.
