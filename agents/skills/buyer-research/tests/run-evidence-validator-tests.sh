#!/usr/bin/env bash
set -euo pipefail

ROOT="$(git rev-parse --show-toplevel)"
python3 "${ROOT}/agents/skills/buyer-research/tests/test_evidence_check.py"
