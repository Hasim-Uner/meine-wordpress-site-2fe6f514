#!/usr/bin/env bash
# SEO Agent — print available sub-skills and their entry scripts
set -euo pipefail
SKILLS_DIR="$(cd "$(dirname "$0")/../.." && pwd)"

echo "=== SEO Agent: Available Routes ==="
echo ""

for skill in seo-live-qa seo-cockpit-hardening internal-linking-audit wordpress-performance-marketing pillar-cornerstone-writer page-speed-audit; do
  dir="$SKILLS_DIR/$skill"
  if [ -d "$dir" ]; then
    desc=$(grep '^description:' "$dir/SKILL.md" | head -1 | sed 's/^description: //')
    script=$(find "$dir/scripts" -name '*.sh' 2>/dev/null | head -1 || echo "none")
    printf "  %-35s %s\n" "$skill" "$desc"
    if [ "$script" != "none" ]; then
      # bash, nicht sh: die Skripte nutzen `set -o pipefail`, das unter dash fehlschlaegt.
      printf "    -> run: bash %s\n" "$script"
    fi
    echo ""
  fi
done

echo "=== Tip: Match task keywords to a skill, then load only that SKILL.md ==="
