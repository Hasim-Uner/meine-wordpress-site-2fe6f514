# Review Evidence

Verify [measurement readiness](instrument.md), the frozen release record and the Aggregate Review CSV / Evidence Gates sections of [measurement-contract](measurement-contract.md). Use only aggregate data.

Prepare the aggregate CSV described in the reference. Substitute the frozen
release date and thresholds for these illustrative values:

   ```bash
   python3 agents/skills/revenue-learning-loop/scripts/evidence-gate.py \
     .ai/memory/revenue-window.csv \
     --release-date 2026-08-12 \
     --min-sessions 100 \
     --min-requests 10 \
     --primary qualified_rate \
     --min-primary-change-pp 0.5
   ```

**Decide honestly.** `KEEP CANDIDATE` and `REVERT CANDIDATE` are directional
   recommendations, never causal proof. `INSUFFICIENT` means preserve the
   current state and collect the missing evidence unless a safety regression
   requires an explicit fix.
