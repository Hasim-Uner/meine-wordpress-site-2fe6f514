#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd -P)"
cd "$ROOT"
python3 -m unittest discover -s agents/skills/agent-system-maintenance/tests -p 'test_*.py'
