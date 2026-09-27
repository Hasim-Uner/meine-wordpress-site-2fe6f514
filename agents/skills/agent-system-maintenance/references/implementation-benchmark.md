# Five implementation tasks

This complements the 21 routing cases. The five repair tasks exercise existing
motion, form, permalink, navigation and pricing contracts. Their defects are
deliberately seeded in disposable copies; they are not findings in production.

```bash
python3 agents/skills/agent-system-maintenance/scripts/task-benchmark.py check
python3 agents/skills/agent-system-maintenance/scripts/task-benchmark.py list
python3 agents/skills/agent-system-maintenance/scripts/task-benchmark.py prepare author-redirect /tmp/agent-eval/codex/author-redirect --revision HEAD
```

Preparation reads a committed revision, makes an independent Git repository,
seeds one defect and removes the implementation answer files. It never modifies
the source checkout or creates a remote. Installed Node/Composer dependencies are
copied when available; there is no network installation. The input commit, task
digest and prompt live beside the disposable workspace.

Use the same explicit revision for both hosts and a fresh copy/session for each
case. Never let an evaluated agent read grading files or the original checkout.
Run the check once before the model: the seeded fixture must fail for the intended
reason, not because of missing tools. Then run the corresponding correct source
as a separate control: it must pass. An unverified fixture is not an evaluation.

Start the host with the project environment, e.g. through
`python3 scripts/toolchain.py exec`, and use its documented workspace restrictions.
Give it only `prompt.txt` and the prepared workspace. Do not disable sandboxing or
approval controls for a benchmark. Keep external integrations inactive. Record
CLI version, actual model/version, platform, settings and measured wall time
alongside the trace. Do not select new model defaults silently.

Retain the native JSONL trace outside the evaluated workspace. After the agent:

```bash
python3 agents/skills/agent-system-maintenance/scripts/task-benchmark.py verify /tmp/agent-eval/codex/author-redirect --host codex --model ACTUAL_MODEL --trace /tmp/agent-eval/codex/author-redirect/trace.jsonl
```

`verify` checks changed paths against the saved input commit, refuses changes to
tests or other files, runs the fixed task check and requires its success marker.
It writes the log and result beside the workspace. Without host/model/trace the
result is explicitly a fixture check, never a model observation. Review each
candidate diff as well: a passing contract test is not a complete quality review.

Tokens come only from terminal host events. Codex cached input is a subset of
reported input; Claude's separately reported cache-read/cache-creation counts
are included only when all components are present. Missing metrics remain absent.
`validation_seconds` measures the grader, not model time. Rework counts, actual
file-read counts, CLI/settings provenance and agent elapsed time still require
observed traces or operator measurements; this helper does not invent them.

Compare only completed observations for the same five cases, revision and
settings. Show completion and metric coverage beside totals; never rank a partial
run as cheaper. Repeat runs before making a causal savings claim. Keep raw traces
and per-run reports outside Git or under ignored `.ai/memory/`; inspect them for
secrets before sharing. CI runs synthetic scorer regressions only and makes no
model calls.

Host references: [OpenAI non-interactive mode](https://learn.chatgpt.com/docs/non-interactive-mode)
and [Claude programmatic mode](https://code.claude.com/docs/en/headless).
