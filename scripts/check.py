#!/usr/bin/env python3
"""Select and run the same repository checks locally and in CI (stdlib only)."""
import argparse
import json
import os
from pathlib import Path
import subprocess
import sys
import tempfile
import time

ROOT = Path(__file__).resolve().parents[1]
DOC_ROOTS = ('docs/', 'content/', 'seo-research/')
DOC_SUFFIXES = {'.md', '.txt', '.csv', '.json', '.svg', '.png', '.jpg', '.webp', '.pdf'}
AGENT_ROOTS = ('agents/skills/', '.agents/skills/', '.claude/skills/')
AGENT_FILES = {'AGENTS.md', 'CLAUDE.md', '.claudeignore', '.claude/settings.json'}


def git(*args, root=ROOT):
    result = subprocess.run(['git', *args], cwd=root, stdout=subprocess.PIPE,
                            stderr=subprocess.PIPE)
    if result.returncode:
        raise ValueError(result.stderr.decode(errors='replace').strip())
    return result.stdout.decode(errors='surrogateescape').strip('\n')


def resolve_commit(ref, root):
    return git('rev-parse', '--verify', '--end-of-options', ref + '^{commit}', root=root)


def changes(base=None, head=None, root=ROOT):
    """Use both sides of renames; local mode includes index, worktree and new files."""
    reason = ''
    resolved_head = resolve_commit(head or 'HEAD', root)
    if head and resolved_head != resolve_commit('HEAD', root):
        raise ValueError('--head must be the checked-out commit; checkout that revision first')
    try:
        if base:
            resolved_base = resolve_commit(base, root)
        else:
            resolved_base = git('merge-base', 'origin/main', resolved_head, root=root)
    except ValueError:
        # An unusable before SHA, missing origin/main or shallow history cannot
        # justify skipping checks. Diff from the root and run the full suite.
        resolved_base = git('rev-list', '--max-parents=0', resolved_head, root=root).splitlines()[0]
        reason = 'comparison base unavailable; full validation required'
    refs = [resolved_base] + ([resolved_head] if head else [])
    paths = set(filter(None, git('diff', '--name-only', '--no-renames', '-z',
                                *refs, '--', root=root).split('\0')))
    if not head:
        paths.update(filter(None, git('ls-files', '--others', '--exclude-standard',
                                      '-z', root=root).split('\0')))
    elif git('status', '--porcelain', '--untracked-files=no', root=root):
        raise ValueError('committed comparison requires a clean tracked worktree; omit --head locally')
    return sorted(paths), resolved_base, resolved_head if head else '', reason


def classify(paths, force_full=False, fallback=''):
    kinds = set()
    for path in paths:
        if path in AGENT_FILES or path.startswith(AGENT_ROOTS):
            kinds.add('agents')
        elif ((path.startswith(DOC_ROOTS) and Path(path).suffix.lower() in DOC_SUFFIXES)
              or path in {'README.md', 'CHANGELOG.md', 'LICENSE', 'LICENSE.md'}):
            kinds.add('docs')
        else:
            # Runtime, tooling, workflow and all unknown paths are deliberately
            # conservative. Never silently exempt a newly introduced dependency.
            kinds.add('runtime')
    runtime = bool(force_full or fallback or 'runtime' in kinds)
    return {
        'profile': 'full' if runtime else ('agents' if 'agents' in kinds else 'docs'),
        'runtime': runtime,
        'skills': runtime or 'agents' in kinds,
        'deploy': bool(fallback or 'runtime' in kinds),
        'reason': fallback or ('full validation requested' if force_full else 'changed paths'),
    }


def deployment_current(root=ROOT):
    """Inside the production lock: allow docs successors, never older runtime."""
    head = resolve_commit('HEAD', root)
    latest = resolve_commit('origin/main', root)
    ancestor = subprocess.run(['git', 'merge-base', '--is-ancestor', head, latest], cwd=root)
    if ancestor.returncode == 1:
        return False
    if ancestor.returncode:
        raise ValueError('cannot verify deployment ancestry')
    paths = list(filter(None, git('diff', '--name-only', '--no-renames', '-z',
                                  head, latest, '--', root=root).split('\0')))
    return not classify(paths)['deploy']


def checks(plan, base, head):
    diff_refs = [base, head]
    result = [
        ('architecture', ['bash', 'scripts/validate-architecture.sh']),
        ('php-syntax', ['bash', '-c', "find blocksy-child -name '*.php' -print0 | xargs -0 -n1 php -l"]),
        ('skill-contracts', ['python3', 'scripts/validate-skills.py']),
        ('check-selection', ['python3', '-m', 'unittest', 'discover', '-s', 'scripts/tests', '-p', 'test_check.py']),
        ('canon', ['bash', 'scripts/canon-guard.sh']),
        ('e3-canon', ['bash', 'scripts/lint-e3-canon.sh']),
        ('canon-drift', ['bash', 'scripts/lint-canon-drift.sh', *diff_refs]),
        ('german-copy', ['bash', 'scripts/check-german-copy.sh', *diff_refs]),
    ]
    if plan['skills']:
        result.append(('skill-suites', ['bash', 'scripts/test-skills.sh']))
    if plan['runtime']:
        result.extend([
            ('theme-assets', ['python3', 'scripts/audit-theme-assets.py']),
            ('lead-path', ['bash', 'scripts/smoke-lead-path-contract.sh']),
            ('funnel-routing', ['bash', 'scripts/smoke-funnel-routing-contract.sh']),
            ('crawler-signals', ['php', 'scripts/lint-entity-crawler-signals.php']),
            ('navigation', ['php', 'scripts/tests/navigation-contract.php']),
            ('motion', ['bash', 'scripts/lint-css-motion.sh']),
            ('spacing', ['bash', 'scripts/lint-css-spacing.sh']),
            ('forms', ['npm', 'run', 'test:forms']),
            ('intake', ['npm', 'run', 'test:intake']),
            ('permalinks', ['npm', 'run', 'test:permalinks']),
            ('provisioning', ['npm', 'run', 'test:provisioning']),
            ('pricing', ['npm', 'run', 'test:pricing']),
            ('navigation-ui', ['npm', 'run', 'test:navigation-ui']),
            ('php-analysis', ['php', os.environ.get('PHPSTAN_PHAR', 'vendor/phpstan/phpstan/phpstan.phar'),
                              'analyse', '--no-progress', '--memory-limit=4G']),
            ('deploy-config', ['bash', 'scripts/verify-deploy-config.sh', '22', 'www/wp-content/themes/blocksy-child/']),
            ('theme-build', ['npm', 'run', 'build:theme', '--', '.build/blocksy-child']),
            ('theme-header', ['grep', '-q', '^Theme Name: Blocksy Child$', '.build/blocksy-child/style.css']),
        ])
    return result


def run_checks(selected, root=ROOT):
    """Keep success output small, retain complete logs, and propagate any failure."""
    log_dir = Path(tempfile.mkdtemp(prefix='repo-check-'))
    print(f'Logs: {log_dir}', flush=True)
    failures = 0
    for name, command in selected:
        started = time.monotonic()
        print(f'RUN  {name}', flush=True)
        log_path = log_dir / (name + '.log')
        try:
            with log_path.open('w') as log:
                result = subprocess.run(command, cwd=root, stdout=log, stderr=subprocess.STDOUT,
                                        env={**os.environ, 'PYTHONDONTWRITEBYTECODE': '1'})
            status = result.returncode
        except OSError as exc:
            log_path.write_text(str(exc) + '\n')
            status = 127
        label = 'FAIL' if status else 'PASS'
        print(f'{label} {name} ({time.monotonic() - started:.1f}s)', flush=True)
        if status:
            failures += 1
            if os.environ.get('GITHUB_ACTIONS') == 'true':
                print(f'::group::{name} failure', flush=True)
            print(log_path.read_text(errors='replace'), flush=True)
            if os.environ.get('GITHUB_ACTIONS') == 'true':
                print('::endgroup::', flush=True)
    print(f'{len(selected)} checks, {failures} failures. Logs: {log_dir}', flush=True)
    return 1 if failures else 0


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--base', default=os.environ.get('CHECK_BASE') or None)
    parser.add_argument('--head', default=os.environ.get('CHECK_HEAD') or None)
    parser.add_argument('--full', action='store_true', default=os.environ.get('CHECK_FULL') == 'true')
    parser.add_argument('--plan', action='store_true', help='print the plan without running checks')
    parser.add_argument('--deployment-current', action='store_true',
                        help='check a queued automatic release against freshly fetched origin/main')
    parser.add_argument('--github-output', type=Path, help='append selection flags for CI dependency setup')
    args = parser.parse_args()
    try:
        if args.deployment_current:
            current = deployment_current()
            if args.github_output:
                with args.github_output.open('a') as output:
                    output.write(f'current={str(current).lower()}\n')
            print('Automatic release is current.' if current else 'Skipping superseded automatic release.')
            return 0
        paths, base, head, fallback = changes(args.base, args.head)
        plan = classify(paths, args.full, fallback)
        selected = checks(plan, base, head)
        if args.github_output:
            with args.github_output.open('a') as output:
                for key in ('runtime', 'skills', 'deploy'):
                    output.write(f'{key}={str(plan[key]).lower()}\n')
        if args.plan:
            print(json.dumps({**plan, 'base': base, 'head': head or 'worktree',
                              'paths': paths, 'checks': [name for name, _ in selected]}, indent=2))
            return 0
        print(f"Profile: {plan['profile']} ({len(paths)} changed paths; {plan['reason']})", flush=True)
        return run_checks(selected)
    except (ValueError, OSError) as exc:
        print(f'FAIL: {exc}', file=sys.stderr)
        return 1


if __name__ == '__main__':
    sys.exit(main())
