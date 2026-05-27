---
name: offer-funnel-intelligence
description: World-class offer, funnel, positioning, proof, qualification, and sales-architecture diagnosis for hasimuener.de. Use when the user asks whether the website, marketcheck, offer ladder, Solar/SHK positioning, E3 proof, CTA logic, WGOS/module framing, or lead qualification is strong enough to create qualified inquiries and closeable opportunities.
---

# Offer Funnel Intelligence

Use this skill when the task is closer to money than polish: offer clarity, buyer psychology, funnel economics, sales fit, proof architecture, qualification, post-sale modular delivery, and whether the page creates qualified demand instead of just looking good.

This is not a generic CRO checklist. It is the strategic revenue layer above copy, SEO, tracking, and design.

## Load First

1. `AGENTS.md`
2. `agents/skills/CONTEXT.md`
3. `docs/standards/BRAND_AND_COPY.md`
4. `llms.txt`
5. Relevant route/template/helper files only
6. If touching frontend HTML/CSS/JS, also load `modern-web-guidance`

## First Command

```bash
bash agents/skills/offer-funnel-intelligence/scripts/scan-offer-funnel.sh
```

## Core Diagnosis

Score the funnel as a business system, not as a page:

1. `Buyer fit`
   - Is the page clearly for Solar, Wärmepumpe, Speicher, Energie-Anbieter, SHK, or high-project-value operators?
   - Does it exclude poor-fit visitors without sounding arrogant?
   - Does it speak to Geschäftsführer/Vertriebsleiter rather than marketing insiders?
2. `Pain economics`
   - Are Portal-Abhängigkeit, Leadkosten, Anfragequalität, Vertriebszeit, Datenbesitz, and lost control concrete?
   - Can the buyer feel the cost of doing nothing?
   - Are numbers used responsibly and tied to proof?
3. `Offer ladder`
   - Is the path clear: Marktcheck -> Anfrage-System-Analyse -> Umsetzung/WGOS-Module -> Retainer/Weiterentwicklung?
   - Does each step have a job, decision criterion, and next action?
   - Is the Marktcheck framed as diagnosis, not as a gimmicky free tool?
4. `Marketcheck desire`
   - Does the visitor understand what they get?
   - Does the Marketcheck feel valuable enough to submit real business context?
   - Does it reduce risk before asking for contact?
5. `Proof architecture`
   - Is E3 used as named proof with before/after, mechanism, constraints, and transfer logic?
   - Is proof placed before major asks?
   - Are claims limited enough to stay credible?
6. `Qualification design`
   - Does the funnel separate quantity, quality, CPL, tracking, and portal-dependence problems?
   - Does it identify whether a lead is worth a call?
   - Does it prevent weak leads from consuming time?
7. `CTA economics`
   - Does each CTA communicate the value received, not only the action taken?
   - Is the primary CTA obvious at every decision point?
   - Are secondary CTAs proof/support, not distractions?
8. `WGOS boundary`
   - WGOS/WordPress Growth Operating System is allowed as delivery architecture, offer module logic, internal/post-sale language, proposal structure, protected/noindex detail, or implementation system.
   - WGOS is not allowed as cold acquisition positioning on Solar/SHK landing pages, first-screen copy, primary CTA labels, or marketcheck entry copy.
   - Public cold traffic should not have to learn the internal framework before understanding the business outcome.
9. `Sales handoff`
   - Does the page prepare the buyer for the next conversation?
   - Are timeline, fit, price range, ownership, risk, and handoff objections answered?
   - Does the lead arrive warmer and more qualified?
10. `Operational truth`
   - No promise that sales/delivery cannot keep.
   - No outdated timing, free-audit, Shopify, generic agency, or old WGOS front-positioning leakage.

## Hard Rules

- Do not optimize for clicks if lead quality drops.
- Do not add clever terminology when buyer language is clearer.
- Do not make WordPress the core offer on Solar/SHK acquisition routes.
- Do not delete WGOS blindly. Reclassify it: public acquisition vs delivery architecture.
- Do not treat E3 as a universal guarantee. Use it as mechanism proof with constraints.
- Do not suggest A/B tests before identifying obvious clarity or qualification debt.
- Do not create broad service catalogs. Preserve diagnosis-first funnel logic.
- Do not change repo files unless explicitly asked. Prefer a P0/P1/P2 action plan first.

## Output Standard

Return a brutal board-level funnel report:

- `Verdict` — what is currently strongest and what is costing money
- `P0 Revenue Blockers` — issues that directly weaken qualified inquiries or sales fit
- `P1 High-Leverage Moves` — likely improvements to lead quality, desire, or trust
- `P2 Polish / Later` — useful but not urgent
- `Offer Ladder Fixes` — Marketcheck, Analysis, WGOS Modules, Retainer
- `Copy Rewrites` — concrete replacement text for hero, CTA, proof, FAQ, qualifier, final CTA
- `Proof Moves` — how to use E3 and other proof more effectively
- `Qualification Moves` — fields/questions/filters that improve sales quality
- `Tracking Questions` — only metrics needed for decisions
- `Do Not Touch` — working assets that should stay

For every recommendation include:

- affected route/file/section
- current issue
- business consequence
- proposed change
- priority: `P0`, `P1`, `P2`
- whether it is `Repo`, `Manual WP`, `Sales`, or `Tracking`

## Quality Bar

A good output should make the next implementation obvious.
A great output should make a weak lead less likely and a strong buyer more willing to submit.
A world-class output should sharpen the business model, not just the page.
