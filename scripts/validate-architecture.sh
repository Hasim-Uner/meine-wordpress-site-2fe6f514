#!/usr/bin/env bash

set -euo pipefail

failures=0

fail() {
  echo "FAIL: $*" >&2
  failures=$((failures + 1))
}

pass() {
  echo "OK: $*"
}

require_file() {
  local path="$1"

  if [[ -f "$path" ]]; then
    pass "Found $path"
  else
    fail "Missing required file: $path"
  fi
}

require_no_path() {
  local path="$1"

  if [[ -e "$path" ]]; then
    fail "Forbidden path exists: $path"
  else
    pass "Forbidden path absent: $path"
  fi
}

require_text() {
  local path="$1"
  local pattern="$2"
  local label="$3"

  if grep -Eq "$pattern" "$path"; then
    pass "$label"
  else
    fail "$label"
  fi
}

echo
echo "=== Required Root Contracts ==="
require_file "AGENTS.md"
require_file "agents/skills/CONTEXT.md"
require_file ".github/workflows/ci.yml"

echo
echo "=== Skill Directory Contract ==="
require_no_path ".agents"
require_no_path "skills-lock.json"

for skill_dir in agents/skills/*/; do
  skill_dir="${skill_dir%/}"
  skill_name="${skill_dir##*/}"

  if [[ ! -f "$skill_dir/SKILL.md" ]]; then
    fail "Skill is missing SKILL.md: $skill_dir"
    continue
  fi

  pass "Skill has SKILL.md: $skill_name"

  if ! grep -q "| \`$skill_name\` |" agents/skills/CONTEXT.md; then
    fail "Skill is not registered in agents/skills/CONTEXT.md: $skill_name"
  fi
done

while IFS= read -r registered_skill; do
  [[ -z "$registered_skill" ]] && continue

  if [[ -d "agents/skills/$registered_skill" ]]; then
    pass "Registered skill exists: $registered_skill"
  else
    fail "Registered skill directory missing: $registered_skill"
  fi
done < <(sed -n 's/^| `\([^`]*\)` |.*/\1/p' agents/skills/CONTEXT.md)

echo
echo "=== Modern Web Guidance Contract ==="
require_file "agents/skills/modern-web-guidance/SKILL.md"
require_file "agents/skills/modern-web-guidance/guides/css/css.md"
require_file "agents/skills/modern-web-guidance/guides/performance/performance.md"
require_file "agents/skills/modern-web-guidance/guides/forms/forms.md"
require_file "agents/skills/modern-web-guidance/guides/accessibility/accessibility.md"
require_no_path "agents/skills/modern-web-guidance/guides/built-in-ai"
require_no_path "agents/skills/modern-web-guidance/guides/passkeys"
require_no_path "agents/skills/modern-web-guidance/guides/webmcp"
require_text ".claudeignore" '^agents/skills/modern-web-guidance/guides/\*\*$' "Modern web guides are excluded from default Claude context"
require_text "agents/skills/CONTEXT.md" 'Core rule: When a task touches `blocksy-child/assets/css/`, `blocksy-child/assets/js/`' "Modern web guidance core rule is registered"

echo
echo "=== CI Coverage Contract ==="
require_text ".github/workflows/ci.yml" "^[[:space:]]+- 'agents/skills/\\*\\*'" "CI watches agents/skills changes"
require_text ".github/workflows/ci.yml" "^[[:space:]]+- 'AGENTS.md'" "CI watches AGENTS.md changes"
require_text ".github/workflows/ci.yml" "^[[:space:]]+- '\\.claudeignore'" "CI watches .claudeignore changes"
require_text ".github/workflows/ci.yml" 'bash scripts/validate-architecture\.sh' "CI runs architecture validation"
require_text ".github/workflows/ci.yml" 'bash scripts/lint-canon-drift\.sh' "CI runs canon drift guard"
require_text ".github/workflows/ci.yml" 'bash scripts/lint-e3-canon\.sh' "CI runs E3 canon guard"
require_text ".github/workflows/ci.yml" 'bash scripts/check-german-copy\.sh' "CI runs German copy guard"

echo
echo "=== n8n Scope Contract ==="
if grep -Eq "automations/n8n" .github/workflows/ci.yml; then
  fail "CI must not be coupled to inactive automations/n8n artifacts"
else
  pass "CI remains decoupled from inactive automations/n8n artifacts"
fi

echo
echo "=== Verdict ==="
if [[ "$failures" -gt 0 ]]; then
  echo "Architecture validation failed with $failures issue(s)." >&2
  exit 1
fi

echo "Architecture validation passed."
