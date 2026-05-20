---
name: growth-audit-optimizer
description: Inspect retired /growth-audit legacy code only when needed; public CRO and next-step work belongs to the Solar/SHK marketcheck on /solar-waermepumpen-leadgenerierung/#marktcheck.
---

# Growth Audit Optimizer

Use this skill only when the task explicitly needs the retired `/growth-audit/` code path, redirect behavior, or legacy audit copy. For public CRO work, use the Solar/SHK marketcheck on `/solar-waermepumpen-leadgenerierung/#marktcheck`.

Run first:

```bash
sh agents/skills/growth-audit-optimizer/scripts/print-scope.sh
```

## Focus

- Treat `/growth-audit/` as a legacy route that redirects to the marketcheck.
- Use `60 Sekunden` as the timing baseline.
- Keep public positioning centered on `Solar`, `Wärmepumpe`, `Speicher`, `Founding-Partner`, `Marktcheck`, and `Anfrage-Systeme`.
- Remove stale Shopify references if they still appear in the audit context.
- Frame the page as a strategic diagnosis entry point, not as a gimmicky free tool.

## Default Workflow

1. Identify whether the task targets legacy `/growth-audit/` code or the active marketcheck path before changing copy.
2. Search audit-relevant strings across template, helper, JS, CSS, and SEO layers.
3. Remove stale `30 Sekunden`, `48h`, manual-feedback, CRM-intake, or Shopify carryover from public-facing flow copy.
4. Sharpen marketcheck hero, trust, expectation-setting, and result-to-next-step CTA logic.
5. Align active metadata and shared audit CTA copy when they still point at outdated `/growth-audit/` framing.

## Copy Direction

See `docs/standards/BRAND_AND_COPY.md` for full tone, preferred terms, and anti-patterns.
Additional audit-specific rule: avoid old 48h/manual-review language in the active marketcheck flow and do not revive `/growth-audit/` as a primary CTA.

## Deliver

- Repo-side implementation in the active marketcheck files or clearly scoped legacy files
- Short note with changed files, removed copy debt, hero/trust/CTA upgrades, and any remaining legacy risks
