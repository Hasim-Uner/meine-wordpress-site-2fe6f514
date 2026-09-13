---
name: deploy-qa
description: "Primary release router for pre-deploy smoke checks, registry release QA, navigation migration, and architecture gates."
---

# Deploy QA

Trigger: before push/deploy/release, registry changes, navigation/header migration, or release-risk verification.

Delegate:
- General pre-deploy checks → `pre-deploy-smoke`
- WGOS/glossary/registry release → `registry-release-qa`
- Header/menu/navigation migration → `navigation-migration`

Required baseline:
```bash
npm run lint:architecture
npm run lint:php
```

Hard rules:
- A failing deterministic gate blocks release.
- Do not change deploy workflow unless deploy behavior is explicitly in scope.
- Report manual WordPress/admin follow-ups separately from repo checks.

Deliverable: pass/fail release evidence and exact blockers.
