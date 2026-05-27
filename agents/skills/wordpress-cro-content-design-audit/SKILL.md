---
name: wordpress-cro-content-design-audit
description: Run deep conversion-copy, CRO, CTA hierarchy, proof, offer-clarity, and page-architecture audits for hasimuener.de. Use when the user asks for page critiques, conversion recommendations, copy rewrites, homepage or service-page analysis, proof strategy, section ordering, decision-maker copy improvements, or repo-grounded CRO diagnosis in the Blocksy child theme.
---

# WordPress CRO Content Design Audit

Use this skill when the task is not only visual polish. The job is to decide whether a page makes the right buyer understand the offer, trust the mechanism, feel the cost of inaction, and take the next diagnostic step.

## Load First

1. `AGENTS.md`
2. `agents/skills/CONTEXT.md`
3. `docs/standards/BRAND_AND_COPY.md`
4. `llms.txt`
5. Then only the task-relevant template, partial, CSS, JS, helper, or editor-owned copy references.

## First Command

```bash
rg -n "WordPress Agentur|Anfrage-System|Marktcheck|Projekt prüfen|Auswertung anfordern|E3|CPL|qualifiziert|Portal|Tracking|Conversion|Kontakt" \
  blocksy-child/front-page.php \
  blocksy-child/page-*.php \
  blocksy-child/template-parts/ \
  blocksy-child/inc/ \
  llms.txt \
  docs/standards/BRAND_AND_COPY.md
```

## Primary Scope

- `blocksy-child/front-page.php`
- `blocksy-child/page-*.php`
- `blocksy-child/template-parts/*.php`
- `blocksy-child/inc/shortcodes.php`
- `blocksy-child/inc/canon/*.php`
- `blocksy-child/assets/css/*.css`
- `blocksy-child/assets/js/*.js`
- `llms.txt`
- `docs/standards/BRAND_AND_COPY.md`

Always classify the page first:

- `Editor-driven wrapper`
- `Hardcoded template`
- `Shared partial`
- `Canonical/helper-driven copy`

That classification determines whether the recommendation belongs in Git, WordPress editor work, or both.

## Deep Audit Lenses

Review in this order. Do not skip to design polish before the copy diagnosis is finished.

1. `First-screen clarity`
   - Who is this for?
   - What painful business problem changes?
   - What is the mechanism?
   - What is the next step?
   - What is the strongest proof visible before scroll?
2. `Buyer language`
   - Replace internal/system metaphors when they hide concrete pain.
   - Prefer Geschäftsführer-/Vertriebsleiter language over marketing-department language.
   - Translate abstractions into loss, risk, control, time, cost, lead quality, and sales capacity.
3. `Offer architecture`
   - Reinforce `Marktcheck -> Anfrage-System-Analyse -> Umsetzung / Retainer`.
   - Do not fragment the page into equal service tiles, feature catalogs, or generic agency positioning.
4. `CTA hierarchy`
   - One primary action per decision zone.
   - Secondary links must support trust or segmentation, not compete with the lead action.
   - CTA labels must say what the visitor receives, not just what they do.
5. `Proof density`
   - Claims need numbers, mechanism, constraints, named cases, before/after contrast, or precise limitations.
   - One strong proof surface beats scattered weak trust fragments.
6. `Objection handling`
   - Answer: cost, timeline, risk, handoff, data ownership, no lock-in, existing website, qualification, and what happens after submission.
7. `Friction and scan path`
   - A busy buyer must understand the page in 20 seconds.
   - Reduce duplicate framing, ornamental metaphors, repeated claims, and unclear gateways.
8. `Measurement readiness`
   - Check whether key CTAs, qualifiers, accordions, proof links, sticky CTAs, and form handoffs have stable tracking hooks.

## Hard Rules

- Use `docs/standards/BRAND_AND_COPY.md` as the positioning source of truth.
- Do not revive `Growth Audit`, `WGOS`, Shopify, generic `WordPress-Agentur` positioning, or service-catalog framing when it weakens the diagnosis-first funnel.
- WordPress may appear as technology or local SEO capture, but not as the core role if the page is meant to sell the active offer.
- Keep the primary path diagnosis-first.
- Prefer concrete buyer pain over clever category language.
- Preserve proven numbers only when the referenced case or canon supports them.
- Separate findings into `Repo`, `Manual WP`, and `Operational` follow-up.

## Default Fix Order

1. Hero promise, subline, primary CTA, and first proof surface
2. Section order and the first three scroll stops
3. CTA hierarchy and form handoff
4. Objection handling and FAQ language
5. Proof compression and case framing
6. Internal route consistency and stale-term cleanup
7. Visual polish and microcopy

## Expected Output

Return a brutal, prioritized CRO/copy report:

- `Critical` — conversion blockers that weaken buyer understanding or trust
- `High leverage` — changes likely to move qualified inquiries
- `Copy rewrites` — concrete replacement copy for hero, CTAs, proof, qualifiers, FAQs, or final CTA
- `Repo changes` — exact files/sections to edit
- `Manual WordPress tasks` — editor-owned copy or CMS work
- `Measurement tasks` — tracking events, segments, or funnel checks
- `Do not touch` — elements that already support positioning and should stay

For each finding include:

- affected file or route
- current issue
- why it hurts conversion
- suggested replacement or action
- priority: `P0`, `P1`, or `P2`

When editing, name the exact files changed and call out any editor-owned sections that still require manual follow-up.
