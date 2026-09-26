---
name: seo-live-qa
description: Review live SEO follow-up work after template or routing changes. Use when primary URLs, redirects, canonicals, schema ownership, or Search Console recrawl tasks are part of the task.
---

# SEO Live QA

Use this skill when repo changes need live SEO verification or Search Console follow-up.

## Run First

```bash
agents/skills/seo-live-qa/scripts/print-scope.sh all
```

Supported modes: `all`, `reindex`, `redirects`, `gone`, `noindex`, `mapping`, `live-qa`. Every list is read from its source file.

## Rules

- Keep one primary URL per intent.
- Do not split identical search intent across multiple service pages.
- Separate repo-owned fixes from WordPress-admin cleanup and Search Console tasks.

## Deliver

- URLs to reindex or inspect
- Redirects and canonical paths to verify
- Any editor-owned or admin-owned cleanup still required
