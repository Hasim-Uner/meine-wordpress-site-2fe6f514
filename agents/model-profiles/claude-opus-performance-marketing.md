# Claude Opus Performance Marketing Profile

Use this profile with Claude / Opus-class agents after loading the shared
`AGENTS.md` contract and the matching task skill. The skill defines what to do;
this profile only tunes how Opus approaches the work.

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

Avoid using Opus as a blind bulk editor. Reason first, then change only what the
request and selected skill place in scope.

## Decision protocol

Before implementation, classify the task using the workstreams in `AGENTS.md`.
If it crosses workstreams, keep repo changes and manual actions distinct. Use
the compact pre-edit plan required by `CLAUDE.md`.

Then follow the shared evidence, audit, safety, Git, PR, and deploy rules in
`AGENTS.md`. Explain business impact, mention deliberate non-changes when they
matter, and keep the answer sharp without motivational filler.
