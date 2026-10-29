---
name: wordpress-cro-content-design-audit
description: Run deep conversion-copy, CRO, CTA hierarchy, proof, offer-clarity, and page-architecture audits for hasimuener.de. Use when the user asks for page critiques, conversion recommendations, copy rewrites, homepage or service-page analysis, proof strategy, section ordering, decision-maker copy improvements, or repo-grounded CRO diagnosis in the Blocksy child theme.
---

# WordPress CRO Content Design Audit

Use this skill when the task is not only visual polish. The job is to decide whether a page makes the right buyer understand the offer, trust the mechanism, feel the cost of inaction, and take the next diagnostic step.

## Load First

1. `AGENTS.md`
2. Reuse the local context selected under `AGENTS.md`; do not load another.
3. `docs/standards/BRAND_AND_COPY.md`
4. `llms.txt`
5. Then only the task-relevant template, partial, CSS, JS, helper, or editor-owned copy references.

## First Command

```bash
rg -n "Projekt anfragen|White-Label|Marktcheck|Anfrage|Projekt prüfen|E3|CPL|qualifiziert|Tracking|Conversion|Kontakt" \
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
   - Message match: for each main inflow (ad, acquisition mail, post, query) does the H1 continue that exact promise in its words, not a generic version? A break here caps everything downstream.
2. `Buyer language`
   - Replace internal/system metaphors when they hide concrete pain.
   - Prefer Geschäftsführer-/Vertriebsleiter language over marketing-department language.
   - Translate abstractions into loss, risk, control, time, cost, lead quality, and sales capacity.
3. `Offer architecture`
   - Follow the route's role, audience and primary CTA from `docs/architecture/CONVERSION_ROUTING.md`; the Marktcheck ladder belongs to energy routes only.
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
   - Attribution survives to the lead: the conversion fires once per real submission, and the click ID (`gclid`, `gbraid`/`wbraid`, `fbclid`, `msclkid`) reaches the CRM payload even when the visitor changes pages before submitting. Check it: land with `?gclid=TEST123`, click to the form route, submit, check the lead. Click IDs belong to the CRM attribution contract, never to analytics parameters (`agents/skills/revenue-learning-loop/references/measurement-contract.md`).

## Evidence Limits

- State the data first: inflow with a sample ad/mail, sessions and inquiries for the last 14–30 days, device split. Name what was missing.
- Below roughly 1,000 sessions or 30 inquiries in the window, rank by first-principles friction and say the data cannot separate a real problem from noise.
- Never promise a percentage lift for a single change; give direction and rank.
- If the problem sits upstream (audience, offer, inflow), say so and stop — a page rebuild does not repair a broken offer.

## Hard Rules

- Public role, positioning and hard bans come only from `docs/standards/BRAND_AND_COPY.md`; route role and CTA only from `docs/architecture/CONVERSION_ROUTING.md`. Do not restate them here.
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
- `Could not check` — missing inputs and what they limit

For each finding include:

- affected file or route
- current issue
- why it hurts conversion
- suggested replacement or action
- priority: `P0`, `P1`, or `P2`

When editing, name the exact files changed and call out any editor-owned sections that still require manual follow-up.
