# AGENTS.md

Global contract for agents in this repo. Keep context small: read this file, then exactly one matching local `CONTEXT.md`, then only task-relevant files.

## Load Order

1. `AGENTS.md`
2. One local context:
   - Theme-wide work: `blocksy-child/CONTEXT.md`
   - PHP modules/registries: `blocksy-child/inc/CONTEXT.md`
   - Shared sections/CTA surfaces: `blocksy-child/template-parts/CONTEXT.md`
   - Durable docs: `docs/CONTEXT.md`
   - Draft content: `content/CONTEXT.md`
   - Skill work: `agents/skills/CONTEXT.md`
3. Only the files needed for the task.

## Skill Routing

- `agents/skills/` is the canonical skill source for every agent host.
- Codex discovers the skills through `.agents/skills/`; Claude Code discovers
  the same skills through `.claude/skills/`. Both directories contain symlinks
  only; never edit a skill through an exposure path.
- Select and load the matching `agents/skills/<skill>/SKILL.md` before
  implementation. Use `agents/skills/CONTEXT.md` as the one local context when
  the task is skill work or routing is unclear.
- For broad website, SEO, offer, tracking, CRO, landing-page, lead-generation,
  or content-automation work, start with
  `agents/skills/wordpress-performance-marketing/scripts/render-checklist.sh full`.
- For frontend HTML, CSS, JavaScript, forms, accessibility, or Core Web Vitals,
  also load `modern-web-guidance` before implementation.
- Keep changes small, reviewable, and limited to one coherent intent per commit
  or PR.

## Token Rules

- Start with `rg --files`; avoid broad `find .` unless excluding generated/ignored paths.
- Do not load `node_modules`, `.build`, lockfiles, historical audits, references, plans, or workflow JSON unless the task explicitly needs them.
- Prefer `rg -n "pattern" path/` over opening whole directories.
- For pure layout/template work, keep searches scoped to templates/assets and avoid heavy backend modules such as `blocksy-child/inc/seo-cockpit/`, `blocksy-child/inc/wgos/`, and `blocksy-child/inc/glossary/` unless they are in scope.
- Do not create root plans, fix logs, or temporary markdown. Use `.ai/memory/` for ephemeral notes.

### Never read whole

These files cost more context than a whole task should. Locate the relevant lines with
`rg -n`, then read only that range with an offset. This applies even when the file is in scope.

| Datei | ~Tokens |
|---|---|
| `blocksy-child/inc/wgos/wgos-asset-registry-data.php` | 34k — reine Datenliste, keine Logik |
| `blocksy-child/inc/review-crm.php` | 34k |
| `blocksy-child/assets/css/agentur.css` | 25k |
| `blocksy-child/inc/seo-cockpit/seo-cockpit-ui.php` | 23k |
| `blocksy-child/page-solar-waermepumpen-leadgenerierung.php` | 19k |
| `blocksy-child/page-wordpress-agentur.php` | 19k |

## Stack

- Deployable WordPress child theme: `blocksy-child/`
- PHP templates/modules, CSS, vanilla JS, self-hosted fonts
- ACF-backed metadata and WordPress REST integrations
- GitHub Actions SSH-Rsync deploy via `.github/workflows/deploy.yml`
- n8n artifacts are inactive; ignore `automations/n8n/` unless the user explicitly asks for n8n work
- No formal test suite is checked in

## Product Defaults

- Canonical public routes, entry points, and business positioning live in `llms.txt`; use it as the route index before adding or changing public URLs.
- Diagnosis/analysis canon: `blocksy-child/inc/canon/diagnose-canon.php`
- Analyse before implementation pitch. Clarity before feature count.
- Do not reintroduce broad agency wording when it weakens the diagnosis-first funnel.
- Do not assume RankMath; use the custom WordPress SEO Cockpit where relevant.
- Do not reintroduce Shopify as a current service focus unless the task is
  explicitly about legacy cleanup.

## Evidence and Measurement Defaults

- Prefer repo-verifiable findings over generic marketing advice.
- Never invent keyword volumes, rankings, Search Console clicks, analytics
  numbers, GA4 events, or conversion data.
- Mark assumptions clearly. Report missing evidence; add a placeholder only
  when it belongs to the requested deliverable.
- Never commit secrets or API keys. Do not add analytics IDs, cookies, pixels,
  consent code, or third-party scripts unless explicitly requested.
- Tracking plans may use neutral event names and `data-track-*` attributes, but
  runtime tracking changes must be explicit and kept separate from planning.

## Workstream and Audit Protocol

- Classify work as strategy, docs/agent instructions, theme/template, backend,
  WordPress admin/editor, analytics, or external platform. Keep code changes and
  manual tasks separate when a request crosses workstreams.
- Do not touch live-critical theme, deployment, hosting, or generated assets
  during planning-only work or without explicit scope.
- Preserve the existing project language, positioning, and architecture.
- For repo audits, report findings under `Critical`, `High leverage`, `Polish`,
  `Manual WordPress tasks`, and `Agent tasks / repo tasks` as applicable.
- In audits, distinguish repo fixes from editor, SEO Cockpit, WordPress admin,
  analytics, and external-platform work.

## Git, PR, and Deploy

- Publishing requires explicit scope. When publishing is requested, `main` is
  the default target for small, reversible changes unless the task requests a
  branch or the risk rules below require a PR.
- Prefer a PR for non-visual contracts or data-writing changes such as tracking
  and GA4 event names, REST contracts, schema, registry or seeder versions, and
  for large, hard-to-reverse `blocksy-child/` changes.
- A `main` push that matches the CI path filters can start the production deploy
  after successful checks; a failed CI run must not deploy the live site. The
  deploy builds and rsyncs `blocksy-child/`.
- Workflow files, build scripts, and package configuration outside
  `blocksy-child/` can still affect deployment output. Instruction and research
  paths such as `agents/`, `docs/`, and `seo-research/` are not copied into the
  deployed child theme.
- After a merged PR, start new work from updated `main`; do not keep using the
  squash-merged branch.

## Sonderrouten & Schatten-Templates

- `blocksy-child/page-wordpress-agentur-hannover.php` is a native slug safety wrapper only.
- Maintain the Hannover landing page layout and copy in `blocksy-child/page-wordpress-agentur.php`.
- Do not duplicate Hannover page edits into the wrapper template.

## Funnel Ladder

1. Marktcheck: qualifier for fit, not a generic sale.
2. Anfragesystem-Analyse: evidence-based fit and market check for suitable businesses.
3. Anfragesystem-Umsetzung: build only after green/yellow fit.
4. Optional performance and premium layers.

Use `Umsetzungspartner` for a business that reaches the build stage. Do not reintroduce the retired `Founding Cohort 2026` frame, seat counters, or application deadlines — see `docs/decisions/0011-founding-cohort-2026-entfernt.md`. Customer-facing forbidden terms live in `blocksy-child/inc/canon/messaging-canon.php`.

For any task that touches offer logic, marketcheck framing, proof architecture, qualification, CTA economics, or the WGOS public/delivery boundary, load `agents/skills/offer-funnel-intelligence/SKILL.md` before changing copy or templates.

## Required Patterns

Internal URLs:

```php
$analysis_url = function_exists('hu_get_request_analysis_url') ? hu_get_request_analysis_url() : home_url('/');
echo esc_url($analysis_url);
```

Escaping:

```php
echo esc_html($label);
echo esc_attr($id);
echo esc_url($url);
```

Tracking hooks:

```html
data-track-action=""
data-track-category=""
data-track-section=""
```

## Do Not

- Do not change `.github/workflows/deploy.yml` unless deploy behavior is the task.
- Do not move or rename `blocksy-child/`.
- Do not duplicate SEO/meta/schema logic across templates, modules, and editor content.
- Do not load or change n8n artifacts unless n8n is explicitly in scope.
- Do not version editor-owned copy as if it were the live source of truth.
- Do not write repetitive playbooks in `docs/`; create/update `agents/skills/<skill>/`.

## Update Triggers

- Runtime behavior, route status, or deploy scope changes: update `docs/architecture/LIVE_STATUS.md`.
- Cross-system contracts or dependencies change: update `docs/architecture/SYSTEM_MAP.md`.
- New repetitive workflow: add/update `agents/skills/<skill>/SKILL.md` plus scripts.
- Skill added or removed: keep matching symlinks in both `.agents/skills/` and
  `.claude/skills/` synchronized with `agents/skills/`.
