#!/usr/bin/env bash
# Exercise the real cockpit evaluation and render layers with CRM/GSC fixtures.
set -euo pipefail
repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd)"
php "$repo_dir/scripts/tests/seo-cockpit-action-hub.php"
