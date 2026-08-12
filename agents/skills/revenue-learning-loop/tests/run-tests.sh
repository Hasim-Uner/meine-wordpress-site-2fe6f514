#!/usr/bin/env bash

set -euo pipefail

ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "${ROOT}"

python3 -m unittest discover \
  -s agents/skills/revenue-learning-loop/tests \
  -p 'test_*.py' \
  -v

python3 agents/skills/revenue-learning-loop/scripts/evidence-gate.py \
  agents/skills/revenue-learning-loop/tests/fixtures/ready.csv \
  --release-date 2026-07-15 \
  --min-sessions 100 \
  --min-requests 10 \
  --min-qualified 5 \
  --primary qualified_rate \
  --min-primary-change-pp 0.5 \
  >/dev/null
