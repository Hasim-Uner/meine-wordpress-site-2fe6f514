#!/usr/bin/env bash
#
# Regressionstests fuer ingest.py, intent_match.py und gap-report.sh --intent.
# Offline: keine API, kein Crawl.
#
#   bash agents/skills/seo-agent/tests/run-intent-tests.sh

set -euo pipefail
TESTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PYTHONDONTWRITEBYTECODE=1 python3 -m unittest discover -s "$TESTS_DIR" -p 'test_intent_match.py' -v
