---
name: registry-release-qa
description: Run deploy and smoke-test QA whenever glossary or service registry data changes. Use when registry files, related routes, or sync behavior are edited.
---

# Registry Release QA

Use this skill when changes touch registry-backed routing or sync behavior.

## Run First

```bash
agents/skills/registry-release-qa/scripts/check_registry_changes.sh HEAD~1 HEAD
```

The script tells you whether the registry QA path applies and prints the mandatory smoke URLs.

## Manual QA Flow

1. Deploy the changed theme.
2. Purge page cache, CDN, and object cache if present.
3. Trigger one frontend request so the sync runs.
4. Validate observability markers and the admin routing assertion endpoint.
5. Smoke-test every printed URL and record `PASS` or `FAIL`.

## PASS Criteria

- Registry observability values are present and plausible
- Routing assertion returns `pass: true`
- No link drift or incorrect fallback routing appears on required URLs
