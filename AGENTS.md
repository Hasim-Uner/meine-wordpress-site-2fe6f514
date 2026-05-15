# AGENTS.md

Global contract for agents in this repo. Keep context small: read this file, then exactly one matching local `CONTEXT.md`, then only task-relevant files.

## Load Order

1. `AGENTS.md`
2. One local context:
   - Theme-wide work: `blocksy-child/CONTEXT.md`
   - PHP modules/registries: `blocksy-child/inc/CONTEXT.md`
   - Shared sections/CTA surfaces: `blocksy-child/template-parts/CONTEXT.md`
   - n8n workflows/contracts: `automations/n8n/CONTEXT.md`
   - Durable docs: `docs/CONTEXT.md`
   - Draft content: `content/CONTEXT.md`
   - Skill work: `agents/skills/CONTEXT.md`
3. Only the files needed for the task.

## Token Rules

- Start with `rg --files`; avoid broad `find .` unless excluding generated/ignored paths.
- Do not load `node_modules`, `.build`, lockfiles, historical audits, references, plans, or workflow JSON unless the task explicitly needs them.
- Prefer `rg -n "pattern" path/` over opening whole directories.
- Do not create root plans, fix logs, or temporary markdown. Use `.ai/memory/` for ephemeral notes.

## Stack

- Deployable WordPress child theme: `blocksy-child/`
- PHP templates/modules, CSS, vanilla JS, self-hosted fonts
- ACF-backed metadata and WordPress REST integrations
- GitHub Actions SSH-Rsync deploy via `.github/workflows/deploy.yml`
- n8n artifacts under `automations/n8n/` only when a workflow is actually versioned with docs and flow map
- No formal test suite is checked in

## Product Defaults

- Primary cold Solar/SHK path: `/solar-waermepumpen-leadgenerierung/#marktcheck`
- Diagnosis/analysis canon: `blocksy-child/inc/canon/diagnose-canon.php`
- Secondary paths: `/ergebnisse/`, `/wordpress-agentur-hannover/`, `/blog/`
- Analyse before implementation pitch. Clarity before feature count.
- Do not reintroduce broad agency wording when it weakens the diagnosis-first funnel.

## Funnel Ladder

1. Marktcheck: qualifier for fit, not a generic sale.
2. Anfrage-System-Analyse: evidence-based fit and market check for suitable Founding-Partner.
3. Anfrage-System-Umsetzung: build only after green/yellow fit.
4. Optional performance and premium layers.

Use `Founding Cohort 2026`, `Founding-Partner`, and `Founding-Konditionen` for the 2026 offer frame. Customer-facing forbidden terms live in `blocksy-child/inc/canon/messaging-canon.php`.

## Required Patterns

Internal URLs:

```php
$analysis_url = home_url('/anfrage-system-analyse/');
echo esc_url($analysis_url);
```

Escaping:

```php
echo esc_html($label);
echo esc_attr($id);
echo esc_url($url);
```

n8n triplet:

```text
automations/n8n/workflows/<name>.json
automations/n8n/docs/<name>.md
automations/n8n/flow-maps/<name>.md
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
- Do not treat n8n JSON as self-explanatory; pair it with docs and a flow map.
- Do not version editor-owned copy as if it were the live source of truth.
- Do not write repetitive playbooks in `docs/`; create/update `agents/skills/<skill>/`.

## Update Triggers

- Runtime behavior, route status, or deploy scope changes: update `docs/architecture/LIVE_STATUS.md`.
- Cross-system contracts or dependencies change: update `docs/architecture/SYSTEM_MAP.md`.
- New repetitive workflow: add/update `agents/skills/<skill>/SKILL.md` plus scripts.
