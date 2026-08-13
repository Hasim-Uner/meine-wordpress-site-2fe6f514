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

require_skill_discovery_links() {
  local discovery_dir="$1"
  local host_label="$2"
  local skill_dir
  local skill_name
  local link_path
  local expected_target
  local actual_target
  local nullglob_state
  local dotglob_state
  local canonical_skills=()
  local discovery_entries=()

  if [[ ! -d "$discovery_dir" ]]; then
    fail "Missing $host_label skill discovery directory: $discovery_dir"
    return
  fi

  nullglob_state="$(shopt -p nullglob || true)"
  dotglob_state="$(shopt -p dotglob || true)"
  shopt -s nullglob dotglob
  canonical_skills=(agents/skills/*/)
  discovery_entries=("$discovery_dir"/*)
  eval "$nullglob_state"
  eval "$dotglob_state"

  if [[ "${#discovery_entries[@]}" -ne "${#canonical_skills[@]}" ]]; then
    fail "$host_label exposes ${#discovery_entries[@]} skills; expected ${#canonical_skills[@]}"
  else
    pass "$host_label exposes all ${#canonical_skills[@]} canonical skills"
  fi

  for skill_dir in "${canonical_skills[@]}"; do
    skill_dir="${skill_dir%/}"
    skill_name="${skill_dir##*/}"
    link_path="$discovery_dir/$skill_name"
    expected_target="../../agents/skills/$skill_name"

    if [[ ! -L "$link_path" ]]; then
      fail "$host_label skill exposure is not a symlink: $link_path"
      continue
    fi

    actual_target="$(readlink "$link_path")"
    if [[ "$actual_target" != "$expected_target" ]]; then
      fail "$host_label skill exposure has wrong target: $link_path -> $actual_target"
      continue
    fi

    if [[ ! -f "$link_path/SKILL.md" ]]; then
      fail "$host_label skill exposure does not resolve to SKILL.md: $link_path"
      continue
    fi

    pass "$host_label skill exposure resolves: $skill_name"
  done

  for link_path in "${discovery_entries[@]}"; do
    skill_name="${link_path##*/}"
    if [[ ! -d "agents/skills/$skill_name" ]]; then
      fail "$host_label exposes unknown skill: $skill_name"
    fi
  done
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

require_skill_discovery_links ".agents/skills" "Codex"
require_skill_discovery_links ".claude/skills" "Claude Code"

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
require_text ".github/workflows/ci.yml" "^[[:space:]]+- '\\.agents/skills/\\*\\*'" "CI watches Codex skill discovery changes"
require_text ".github/workflows/ci.yml" "^[[:space:]]+- '\\.claude/skills/\\*\\*'" "CI watches Claude Code skill discovery changes"
require_text ".github/workflows/ci.yml" "^[[:space:]]+- 'scripts/\\*\\*'" "CI watches script changes"
require_text ".github/workflows/ci.yml" "^[[:space:]]+- '\\.github/workflows/\\*\\*'" "CI watches workflow changes"
require_text ".github/workflows/ci.yml" "^[[:space:]]+- 'blocksy-child/\\*\\*'" "CI watches theme changes"
require_text ".github/workflows/ci.yml" "^[[:space:]]+- 'AGENTS.md'" "CI watches AGENTS.md changes"
require_text ".github/workflows/ci.yml" "^[[:space:]]+- '\\.claudeignore'" "CI watches .claudeignore changes"
require_text ".github/workflows/ci.yml" 'fetch-depth:[[:space:]]*0' "CI fetches full history for diff guards"
require_text ".github/workflows/ci.yml" 'id:[[:space:]]*refs' "CI computes diff refs"
require_text ".github/workflows/ci.yml" 'bash scripts/validate-architecture\.sh' "CI runs architecture validation"
require_text ".github/workflows/ci.yml" 'bash scripts/lint-canon-drift\.sh.*steps\.refs\.outputs\.base.*steps\.refs\.outputs\.head' "CI runs canon drift guard with diff refs"
require_text ".github/workflows/ci.yml" 'bash scripts/lint-e3-canon\.sh' "CI runs E3 canon guard"
require_text ".github/workflows/ci.yml" 'bash scripts/lint-css-motion\.sh' "CI runs CSS and JavaScript motion guard"
require_text ".github/workflows/ci.yml" 'bash scripts/check-german-copy\.sh.*steps\.refs\.outputs\.base.*steps\.refs\.outputs\.head' "CI runs German copy guard with diff refs"

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
