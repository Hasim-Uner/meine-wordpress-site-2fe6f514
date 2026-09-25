# Delivery and Validation

Write down the intended flow before changing code:

1. User action.
2. Native HTML element or theme-rendered form.
3. Vanilla JS validation/enrichment.
4. `fetch()` target.
5. WordPress REST validation.
6. CRM write or explicit no-write decision.
7. Mail/booking/external handoff.
8. Failure and retry behavior.

Reject any path that bypasses WordPress REST for lead data.

### 3. Implement In This Order

1. Backend contract and validation.
2. CRM persistence and consent meta.
3. Frontend payload generation.
4. Fetch, error, duplicate-submit, and stale-cache handling.
5. Route helpers and CTA targets.
6. External handoff after successful CRM write.
7. Documentation/spec updates when contracts change.

Do not begin with visual polish. If the user also requests UI design, finish the architecture constraints first and delegate UI craft to `b2b-design-system` only when that implementation is in scope. Pure visual tasks start at `frontend-system`.

## Pre-Flight Before Final


Before presenting changes as final, run every available lint or smoke check that matches the touched surface. Do not skip checks silently. At minimum, execute or explicitly rule out:

- `git status --short`
- `npm run test:intake` for REST/CRM changes; `npm run test:forms` for form behavior.
- `bash scripts/smoke-lead-path-contract.sh` and `bash scripts/smoke-funnel-routing-contract.sh` for routing changes.
- `php -l blocksy-child/path/to/changed.php` for changed PHP files.
- `npm run lint:php` for broad PHP changes.
- `bash scripts/check-german-copy.sh` when customer-facing German copy changed.
- `bash scripts/lint-canon-drift.sh` when canon, CTA, offer, or proof data changed.
- `bash scripts/lint-e3-canon.sh` when E3 proof data changed.
- `bash scripts/verify-deploy-config.sh` when deploy configuration or deploy safety is in scope and required env values are available.

If a check cannot run because an environment variable, dependency, or live service is unavailable, state that explicitly and explain the residual risk. A final answer that omits the check status is incomplete.

## Final Answer Contract

Close substantial work with:

- Files changed.
- Contract files read.
- Endpoint and payload status: unchanged, extended compatibly, or intentionally migrated.
- Manual WordPress/admin tasks, if any.
- External-system tasks, if any.
- Checks run and any checks skipped.

Never claim a lead-flow change is done if the payload contract, cache behavior, consent boundary, or CRM write path was not verified.
