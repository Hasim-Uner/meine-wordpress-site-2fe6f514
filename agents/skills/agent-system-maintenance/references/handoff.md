# Cross-Agent Handoff

For unfinished work, write `.ai/memory/handoff-<task>.md` with:

- objective and authorized scope;
- branch/commit and current uncommitted files;
- decisions and links to canonical sources, without copying their rules;
- checks actually run, outcome and remaining blockers;
- next concrete action and timestamp.

The receiving agent checks `git status` and HEAD before trusting the record.
Resolve stale observations against current files. Preserve user-owned changes.
Keep credentials, personal lead data and full tool logs out of the handoff.
This ignored local file helps agents sharing this workspace; other clones need
an explicit transfer. Durable contract changes belong in their canonical source.
