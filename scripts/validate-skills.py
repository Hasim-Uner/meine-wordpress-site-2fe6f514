#!/usr/bin/env python3
"""Check public skill budgets and links; never claim to measure model quality."""
import re
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SKILLS = ROOT / 'agents/skills'
# Review these ceilings deliberately when a workflow needs a larger entrypoint.
MAX_LINES, MAX_BYTES, MAX_DESCRIPTION = 60, 4500, 240


def inspect_skill(path):
    text = path.read_text()
    errors = []
    header = re.match(r'\A---\n(.*?)\n---\n', text, re.S)
    if not header:
        return ['missing YAML frontmatter'], 0, 0
    fields = dict(re.findall(r'^(name|description):\s*([^\n]+)$', header[1], re.M))
    name = fields.get('name', '').strip('"\'')
    description = fields.get('description', '').strip('"\'')
    if name != path.parent.name:
        errors.append('name must match directory')
    # Public entrypoints use a single-line scalar for predictable discovery cost.
    if not description or description in ('>', '|') or len(description) > MAX_DESCRIPTION:
        errors.append(f'description must be a nonempty single line <= {MAX_DESCRIPTION} characters')
    size, lines = len(text.encode()), len(text.splitlines())
    if lines > MAX_LINES or size > MAX_BYTES:
        errors.append(f'entrypoint exceeds {MAX_LINES} lines / {MAX_BYTES} bytes; split conditional guidance')
    return errors, lines, size


def broken_links(path, root=ROOT):
    errors = []
    for link in re.findall(r'\[[^\]\n]+\]\(([^)]+)\)', path.read_text()):
        target = link.split('#', 1)[0]
        if not target or re.match(r'[a-z]+://', target):
            continue
        resolved = (path.parent / target).resolve()
        try:
            resolved.relative_to(root.resolve())
        except ValueError:
            errors.append(f'link outside repository: {target}')
            continue
        if not resolved.exists():
            errors.append(f'broken reference: {target}')
    return errors


def main():
    primary = [s for s in (SKILLS / 'PRIMARY_SKILLS.txt').read_text().splitlines()
               if s and not s.startswith('#')]
    errors, total = [], 0
    for name in primary:
        path = SKILLS / name / 'SKILL.md'
        if not path.is_file():
            errors.append(f'{name}: missing SKILL.md')
            continue
        issues, lines, size = inspect_skill(path)
        total += size
        print(f'{name}: {lines} lines, {size} bytes')
        errors.extend(f'{name}: {e}' for e in issues)
        references = path.parent / 'references'
        for document in [path, *sorted(references.glob('*.md'))]:
            errors.extend(f'{document.relative_to(ROOT)}: {e}' for e in broken_links(document))
    workflow = (ROOT / '.github/workflows/ci.yml').read_text()
    required = ['CLAUDE.md', '.claude/settings.json', 'agents/model-profiles/**']
    for event, following in [('push', 'pull_request'), ('pull_request', 'workflow_dispatch')]:
        block = workflow.split(f'  {event}:', 1)[-1].split(f'  {following}:', 1)[0]
        for pattern in required:
            if f"- '{pattern}'" not in block:
                errors.append(f'CI {event}: missing path {pattern}')
    benchmark = SKILLS / 'agent-system-maintenance/scripts/benchmark.py'
    result = subprocess.run([sys.executable, str(benchmark), '--check'], cwd=ROOT)
    if result.returncode:
        errors.append('benchmark contract validation failed')
    for error in errors:
        print(f'FAIL {error}', file=sys.stderr)
    print(f'{len(primary)} public entrypoints, {total} bytes, {len(errors)} failures. '
          'Bytes are a size proxy, not measured tokens; agent behavior is not evaluated.')
    return bool(errors)


if __name__ == '__main__':
    sys.exit(main())
