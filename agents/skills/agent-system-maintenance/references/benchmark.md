# Cross-Agent Routing Benchmark

`npm run benchmark:agents -- --check` validates fixtures only. CI runs this and
the scorer's regression tests without model/API calls. It does not prove routing
accuracy, task quality or token savings.

## Run a comparison

1. Export blind cases with `npm run --silent benchmark:agents -- --prompts`.
   Keep `evals/routing.json` and its answer key away from the evaluated agent.
2. In a fresh session per case, give Codex or Claude the same repo revision,
   task prompt and this read-only instruction: "Select the primary skill and one
   local context, or null for both if this needs no repo workflow. Inspect only
   agent entrypoints as needed. Do not implement or read benchmark answers."
3. Record the observed selection and a local trace reference. Preserve host,
   model/version, repo revision, instruction/config differences and timestamp.
   Use the same settings for before/after runs; repeat ambiguous cases.
4. Review the rubric manually. For implementation quality, perform a separate
   authorized run in an isolated checkout and inspect the diff/tests; the routing
   score alone cannot certify implementation quality.
5. Save results in ignored `.ai/memory/agent-benchmark-<host>-<date>.json`, then run
   `npm run benchmark:agents -- --score .ai/memory/agent-benchmark-<host>-<date>.json`.

## Observed run format

```json
{
  "host": "codex",
  "model": "actual model/version",
  "revision": "actual git SHA plus dirty-state note",
  "observations": [
    {
      "id": "actual case id",
      "primary_skill": "observed primary name, or null",
      "context": "observed local CONTEXT.md path, or null",
      "evidence": ".ai/memory/trace-file.md"
    }
  ]
}
```

Include each case exactly once; an incomplete run fails instead of yielding an
inflated score. Optional per-case fields: `input_tokens`, `cached_input_tokens`
(a subset of input), `output_tokens`, `elapsed_seconds`, `rework_count`. Record
only actual host measurements; omit unavailable values. Never substitute bytes
or zeros for missing token usage. The report shows measurement coverage per field.

Review instruction load, irrelevant file reads, test choice and prohibited scope
changes against each rubric. Record these findings alongside the trace, not as an
unverified automatic quality score. Keep personal data and secrets out of records.

Use `npm run lint:skills` for entrypoint sizes and links. Its ceilings apply to
public entrypoints only, not vendored specialists or optional references. Review
budgets deliberately if preserving an essential contract needs more space.
