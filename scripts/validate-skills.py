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


def delegation_map(root=ROOT):
    """Parse the primary -> specialist table in agents/skills/CONTEXT.md."""
    text = (root / 'agents/skills/CONTEXT.md').read_text()
    section = text.split('## Internal delegation map', 1)[-1].split('\n## ', 1)[0]
    mapping = {}
    for line in section.splitlines():
        cells = [c.strip() for c in line.strip().strip('|').split('|')]
        if len(cells) == 2 and re.fullmatch(r'`[a-z0-9-]+`', cells[0]):
            mapping[cells[0].strip('`')] = re.findall(r'`([a-z0-9-]+)`', cells[1])
    return mapping


ROUTING_TABLE = re.compile(r'(?im)^#{1,6}\s*routing table\b|^\|[^\n]*\broute to\b')
SKILL_PATH = re.compile(r'agents/skills/([a-z0-9-]+)/')
PATH_SOURCES = ['AGENTS.md', 'CLAUDE.md', 'agents/skills', 'scripts', '.github/workflows']


def specialist_errors(primary, root=ROOT):
    """Guard the internal layer: ownership, no nested routers, no dead skill paths."""
    skills_dir = root / 'agents/skills'
    names = {p.name for p in skills_dir.iterdir() if p.is_dir()}
    errors, mapping = [], delegation_map(root)
    if set(mapping) != set(primary):
        errors.append(f'CONTEXT.md delegation map must list exactly the primaries: '
                      f'{sorted(set(mapping) ^ set(primary))}')
    owned = set()
    for owner, delegates in mapping.items():
        for name in delegates:
            owned.add(name)
            if name not in names:
                errors.append(f'{owner}: delegate {name} does not exist')
            elif name in primary:
                errors.append(f'{owner}: delegate {name} is a primary; hand off instead')
    for name in sorted(names - set(primary)):
        path = skills_dir / name / 'SKILL.md'
        if not path.is_file():
            errors.append(f'{name}: missing SKILL.md')
            continue
        if name not in owned:
            errors.append(f'{name}: specialist has no owner in the CONTEXT.md delegation map')
        text = path.read_text()
        declared = re.search(r'^name:\s*["\']?([^"\'\n]+)', text, re.M)
        if not declared or declared[1].strip() != name:
            errors.append(f'{name}: frontmatter name must match directory')
        if ROUTING_TABLE.search(text):
            errors.append(f'{name}: specialists must not route; move routing to the primary')
        for document in [path, *sorted((path.parent / 'references').glob('*.md'))]:
            errors.extend(f'{document.relative_to(root)}: {e}' for e in broken_links(document, root))
    for source in PATH_SOURCES:
        base = root / source
        files = [base] if base.is_file() else [
            f for f in base.rglob('*') if f.is_file() and f.suffix in {'.md', '.sh', '.py', '.yml', '.json'}]
        for file in files:
            for name in set(SKILL_PATH.findall(file.read_text(errors='ignore'))) - names:
                errors.append(f'{file.relative_to(root)}: path to missing skill {name}')
    return errors, mapping


def main():
    primary = [s for s in (SKILLS / 'PRIMARY_SKILLS.txt').read_text().splitlines()
               if s and not s.startswith('#')]
    internal_errors, mapping = specialist_errors(primary)
    errors, total = [], 0
    for name in primary:
        path = SKILLS / name / 'SKILL.md'
        if not path.is_file():
            errors.append(f'{name}: missing SKILL.md')
            continue
        issues, lines, size = inspect_skill(path)
        total += size
        chain = size + sum((SKILLS / d / 'SKILL.md').stat().st_size
                           for d in mapping.get(name, []) if (SKILLS / d / 'SKILL.md').is_file())
        print(f'{name}: {lines} lines, {size} bytes; with all delegates {chain} bytes')
        errors.extend(f'{name}: {e}' for e in issues)
        references = path.parent / 'references'
        for document in [path, *sorted(references.glob('*.md'))]:
            errors.extend(f'{document.relative_to(ROOT)}: {e}' for e in broken_links(document))
    workflow = (ROOT / '.github/workflows/ci.yml').read_text()
    required = ['CLAUDE.md', '.claude/settings.json']
    for event, following in [('push', 'pull_request'), ('pull_request', 'workflow_dispatch')]:
        block = workflow.split(f'  {event}:', 1)[-1].split(f'  {following}:', 1)[0]
        for pattern in required:
            if f"- '{pattern}'" not in block:
                errors.append(f'CI {event}: missing path {pattern}')
    benchmark = SKILLS / 'agent-system-maintenance/scripts/benchmark.py'
    result = subprocess.run([sys.executable, str(benchmark), '--check'], cwd=ROOT)
    if result.returncode:
        errors.append('benchmark contract validation failed')
    errors.extend(internal_errors)
    for error in errors:
        print(f'FAIL {error}', file=sys.stderr)
    print(f'{len(primary)} public entrypoints, {total} bytes, {len(errors)} failures. '
          'Bytes are a size proxy, not measured tokens; agent behavior is not evaluated.')
    return bool(errors)


if __name__ == '__main__':
    sys.exit(main())
