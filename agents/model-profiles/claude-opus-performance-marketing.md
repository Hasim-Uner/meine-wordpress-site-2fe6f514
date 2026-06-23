# Claude Opus Performance Marketing Profile

Use this profile when running Claude / Opus-class agents against this repository.

The goal is not to make the agent more verbose. The goal is to make it harder for the agent to damage positioning, tracking assumptions, SEO architecture, or live WordPress behavior.

## Best use cases

Use Opus for:

- repo-wide SEO and offer audits
- money-page architecture
- internal-link strategy
- CRO and conversion-copy critique
- landing-page planning
- tracking-audit planning without adding scripts
- SEO Cockpit hardening ideas
- agent task decomposition
- PR review before merge

Avoid Opus as a blind bulk editor. It should reason first, then change only what is scoped.

## Repo-specific assumptions

- The site is positioned around technical SEO, SEO audits, tracking audits, WordPress implementation, landing pages, lead generation, and content/autopost systems.
- Shopify is not the current positioning unless the task is explicitly about old-copy cleanup or legacy content.
- RankMath is not the default SEO system.
- SEO Cockpit is the custom WordPress SEO workflow/plugin context.
- No cookie-based tracking, ad pixels, or consent banners should be added by default.
- Measurement can be planned via neutral event names and `data-track` attributes, but runtime tracking code needs explicit instruction.

## Decision protocol

Before implementation, classify the task:

1. Strategy only
2. Docs / agent instructions
3. Theme/template implementation
4. Plugin/backend implementation
5. WordPress admin/editor task
6. External platform task

If the task crosses categories, split the answer and avoid mixing code changes with manual tasks.

## Audit protocol

For audits, inspect before advising:

- routing table: `agents/skills/CONTEXT.md`
- relevant skill: usually `agents/skills/wordpress-performance-marketing/SKILL.md`
- theme files only if the task touches frontend behavior
- plugin files only if the task touches SEO Cockpit, forms, REST, tracking, or lead routing
- content/docs files only if the task touches positioning or page structure

Then output:

```text
Critical
High leverage
Polish
Manual WordPress tasks
Agent tasks / repo tasks
```

## PR protocol

For PRs:

- one intent per PR
- small file set
- no unrelated formatting
- no hidden live behavior changes
- explain business impact, not just technical diff
- mention what was deliberately not changed

## Red lines

Do not:

- add pixels, cookies, consent code, analytics IDs, or third-party scripts without explicit request
- invent keyword volumes, GSC clicks, GA4 events, rankings, or conversion data
- reintroduce Shopify as a current service focus
- replace existing skills with generic AI-agent advice
- touch deployment, hosting, or live-critical files during planning-only tasks
- modify generated assets unless the task explicitly requires it

## Good default output

A good Opus answer should say:

- what the repo currently supports
- what is missing for SEO/offer/lead-generation value
- what can be changed in code
- what must be done in WordPress admin or SEO Cockpit
- what should become separate issues or PRs

Keep the answer sharp. No motivational filler.
