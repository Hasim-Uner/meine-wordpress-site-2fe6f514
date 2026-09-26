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

load_primary_skills() {
  PRIMARY_SKILLS=()
  if [[ ! -f "agents/skills/PRIMARY_SKILLS.txt" ]]; then
    fail "Missing agents/skills/PRIMARY_SKILLS.txt"
    return
  fi

  while IFS= read -r skill_name; do
    [[ -z "$skill_name" || "$skill_name" =~ ^[[:space:]]*# ]] && continue
    PRIMARY_SKILLS+=("$skill_name")
  done < "agents/skills/PRIMARY_SKILLS.txt"

  if [[ "${#PRIMARY_SKILLS[@]}" -eq 0 ]]; then
    fail "PRIMARY_SKILLS.txt contains no skills"
    return
  fi

  local unique_count
  unique_count="$(printf '%s\n' "${PRIMARY_SKILLS[@]}" | sort -u | wc -l | tr -d ' ')"
  if [[ "$unique_count" -ne "${#PRIMARY_SKILLS[@]}" ]]; then
    fail "PRIMARY_SKILLS.txt contains duplicate entries"
  else
    pass "Primary skill allowlist has ${#PRIMARY_SKILLS[@]} unique skills"
  fi
}

require_primary_skills() {
  local skill_name
  for skill_name in "${PRIMARY_SKILLS[@]}"; do
    if [[ -f "agents/skills/$skill_name/SKILL.md" ]]; then
      pass "Primary skill exists: $skill_name"
    else
      fail "Primary skill missing SKILL.md: $skill_name"
    fi

    if grep -q "| \`$skill_name\` |" agents/skills/CONTEXT.md; then
      pass "Primary skill registered: $skill_name"
    else
      fail "Primary skill not registered in primary routing table: $skill_name"
    fi
  done
}

require_skill_discovery_links() {
  local discovery_dir="$1"
  local host_label="$2"
  local skill_name
  local link_path
  local expected_target
  local actual_target
  local nullglob_state
  local dotglob_state
  local discovery_entries=()

  if [[ ! -d "$discovery_dir" ]]; then
    fail "Missing $host_label skill discovery directory: $discovery_dir"
    return
  fi

  nullglob_state="$(shopt -p nullglob || true)"
  dotglob_state="$(shopt -p dotglob || true)"
  shopt -s nullglob dotglob
  discovery_entries=("$discovery_dir"/*)
  eval "$nullglob_state"
  eval "$dotglob_state"

  if [[ "${#discovery_entries[@]}" -ne "${#PRIMARY_SKILLS[@]}" ]]; then
    fail "$host_label exposes ${#discovery_entries[@]} skills; expected ${#PRIMARY_SKILLS[@]} primary skills"
  else
    pass "$host_label exposes only ${#PRIMARY_SKILLS[@]} primary skills"
  fi

  for skill_name in "${PRIMARY_SKILLS[@]}"; do
    link_path="$discovery_dir/$skill_name"
    expected_target="../../agents/skills/$skill_name"

    if [[ ! -L "$link_path" ]]; then
      fail "$host_label primary exposure is not a symlink: $link_path"
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

    pass "$host_label primary exposure resolves: $skill_name"
  done

  for link_path in "${discovery_entries[@]}"; do
    skill_name="${link_path##*/}"
    if ! printf '%s\n' "${PRIMARY_SKILLS[@]}" | grep -Fxq "$skill_name"; then
      fail "$host_label exposes non-primary skill: $skill_name"
    fi
  done
}

echo
echo "=== Required Root Contracts ==="
require_file "AGENTS.md"
require_file "agents/skills/CONTEXT.md"
require_file "agents/skills/PRIMARY_SKILLS.txt"
require_file ".github/workflows/ci.yml"
if grep -Eq '^[[:space:]]*(function[[:space:]]|add_action|add_filter|remove_action|remove_filter)' blocksy-child/functions.php; then
  fail "blocksy-child/functions.php defines functions or hooks; move them into blocksy-child/inc/"
else
  pass "blocksy-child/functions.php only bootstraps inc/ modules"
fi

require_file ".claudeignore"
require_file ".cursorignore"
require_file ".rooignore"

echo
echo "=== Canonical Skill Store ==="
require_no_path "skills-lock.json"
for skill_dir in agents/skills/*/; do
  skill_dir="${skill_dir%/}"
  skill_name="${skill_dir##*/}"
  if [[ -f "$skill_dir/SKILL.md" ]]; then
    pass "Skill has SKILL.md: $skill_name"
  else
    fail "Skill is missing SKILL.md: $skill_dir"
  fi
done

echo
echo "=== Primary Skill Surface ==="
load_primary_skills
require_primary_skills
require_skill_discovery_links ".agents/skills" "Codex"
require_skill_discovery_links ".claude/skills" "Claude Code"

echo
echo "=== Context Hygiene ==="
require_text ".claudeignore" '^agents/skills/modern-web-guidance/guides/\*\*$' "Guide exclusion pattern is recorded (host behavior not verified)"
require_text ".claudeignore" '^\*\*/\*\.webp$' "WebP exclusion pattern is recorded (host behavior not verified)"
require_text ".claudeignore" '^\*\*/\*\.avif$' "AVIF exclusion pattern is recorded (host behavior not verified)"
require_text ".cursorignore" '^\.agents/skills/\*\*$' "Cursor ignores Codex discovery mirrors"
require_text ".cursorignore" '^\.claude/skills/\*\*$' "Cursor ignores Claude discovery mirrors"
require_text ".rooignore" '^\.agents/skills/\*\*$' "Roo ignores Codex discovery mirrors"
require_text ".rooignore" '^\.claude/skills/\*\*$' "Roo ignores Claude discovery mirrors"

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

echo
echo "=== CI Coverage Contract ==="
require_text "CLAUDE.md" '^@AGENTS\.md$' "Claude imports the shared contract"
require_file "scripts/check.py"
require_file "scripts/check-diff.sh"
require_text ".github/workflows/ci.yml" 'fetch-depth:[[:space:]]*0' "CI fetches full history for diff guards"
require_text ".github/workflows/ci.yml" 'python3 scripts/check\.py --plan --github-output' "CI uses the shared check selector"
require_text ".github/workflows/ci.yml" 'run: python3 scripts/check\.py$' "CI runs shared repository checks"
require_text ".github/workflows/ci.yml" 'needs\.validate\.outputs\.deploy' "Automatic deploy respects the selected scope"
# The selector's behavioral suite covers docs, skills, runtime, unknown paths,
# renames and missing history; validate-skills checks the workflow event scope.

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
