#!/usr/bin/env python3
"""Prepare isolated repair tasks and grade changes against unchanged repo checks."""
import argparse
import hashlib
import json
import os
from pathlib import Path
import shutil
import shlex
import subprocess
import sys
import time

ROOT = Path(__file__).resolve().parents[4]
CASES = Path(__file__).resolve().parents[1] / 'evals/implementation.json'


def cases():
    rows = json.loads(CASES.read_text())
    if len(rows) != 5 or len({row['id'] for row in rows}) != 5:
        raise ValueError('Expected five distinct implementation cases')
    for row in rows:
        path = Path(row['path'])
        if path.is_absolute() or '..' in path.parts or not str(path).startswith('blocksy-child/'):
            raise ValueError('Case target must be inside the theme')
        if not row['before'] or row['before'] == row['seed'] or not row['check'] or not row['success_marker']:
            raise ValueError('Invalid seed/check: ' + row['id'])
    return rows


def git(root, *args):
    return subprocess.check_output(['git', *args], cwd=root, text=True, stderr=subprocess.PIPE).strip()


def prepare(case, directory, revision):
    directory = directory.resolve()
    if directory == ROOT or ROOT in directory.parents:
        raise ValueError('Use a new directory outside the working repository, e.g. /tmp/agent-eval/codex/css-motion')
    if directory.exists():
        raise ValueError('Destination already exists; each case needs a fresh directory')
    commit = git(ROOT, 'rev-parse', '--verify', '--end-of-options', revision + '^{commit}')
    directory.mkdir(parents=True)
    workspace = directory / 'workspace'
    workspace.mkdir()
    archive = subprocess.Popen(['git', 'archive', '--format=tar', commit], cwd=ROOT, stdout=subprocess.PIPE)
    unpack = subprocess.run(['tar', '-xf', '-', '-C', str(workspace)], stdin=archive.stdout)
    archive.stdout.close()
    if archive.wait() or unpack.returncode:
        raise ValueError('Snapshot extraction failed; do not use this fixture')
    target = workspace / case['path']
    original = target.read_text()
    if original.count(case['before']) != 1:
        raise ValueError('Seed no longer matches this revision: ' + case['id'])
    target.write_text(original.replace(case['before'], case['seed'], 1))
    # Keep the grader and answer mutations outside the evaluated checkout.
    for relative in ['evals/implementation.json', 'scripts/task-benchmark.py', 'tests/test_task_benchmark.py']:
        path = workspace / 'agents/skills/agent-system-maintenance' / relative
        if path.exists():
            path.unlink()
    env = dict(os.environ, GIT_CONFIG_GLOBAL=os.devnull, GIT_CONFIG_NOSYSTEM='1')
    for command in [
        ['git', 'init', '-q'], ['git', 'config', 'user.name', 'Evaluation fixture'],
        ['git', 'config', 'user.email', 'fixture@example.invalid'], ['git', 'add', '.'],
        ['git', '-c', 'core.hooksPath=/dev/null', '-c', 'commit.gpgsign=false', 'commit', '-qm', 'Isolated evaluation input'],
    ]:
        subprocess.run(command, cwd=workspace, env=env, check=True)
    # Real copies: agent writes cannot modify dependencies in the main checkout.
    for name in ('node_modules', 'vendor'):
        if (ROOT / name).is_dir():
            shutil.copytree(ROOT / name, workspace / name, symlinks=True)
    metadata = {'case': case['id'], 'revision': commit, 'seed_commit': git(workspace, 'rev-parse', 'HEAD'),
                'case_digest': hashlib.sha256(json.dumps(case, sort_keys=True).encode()).hexdigest()}
    (directory / 'task.json').write_text(json.dumps(metadata, indent=2) + '\n')
    prompt = case['prompt'] + '\n\n' + (
        'Arbeite nur in diesem isolierten Checkout. Befolge AGENTS.md. '
        'Ändere nur {}. Verändere keine Tests, Preise, Abhängigkeiten oder Git-Historie. '
        'Kein Commit, Push, Deploy oder Zugriff auf andere Checkouts und Bewertungsdateien. '
        'Prüfe mit: {}. Berichte die Änderung und das tatsächliche Prüfergebnis.\n'
    ).format(case['path'], shlex.join(case['check']))
    (directory / 'prompt.txt').write_text(prompt)
    return {'workspace': str(workspace), 'prompt': str(directory / 'prompt.txt'), **metadata}


def trace_usage(host, text):
    events = [json.loads(line) for line in text.splitlines() if line.strip()]
    if not all(isinstance(event, dict) for event in events):
        raise ValueError('Trace events must be objects')
    if any(event.get('usage') is not None and not isinstance(event['usage'], dict) for event in events):
        raise ValueError('Usage must be an object when present')
    if host == 'codex':
        if sum(event.get('type') == 'thread.started' for event in events) != 1:
            raise ValueError('Expected exactly one Codex session')
        terminals = [event for event in events if event.get('type') in ('turn.completed', 'turn.failed')]
        success = bool(terminals) and all(event['type'] == 'turn.completed' for event in terminals)
        mapping = {'input_tokens': 'input_tokens', 'cached_input_tokens': 'cached_input_tokens', 'output_tokens': 'output_tokens'}
    elif host == 'claude':
        terminals = [event for event in events if event.get('type') == 'result']
        if len(terminals) > 1:
            raise ValueError('Expected exactly one Claude result')
        success = bool(terminals) and terminals[0].get('subtype') == 'success' and not terminals[0].get('is_error')
        mapping = {'uncached_input_tokens': 'input_tokens', 'cached_input_tokens': 'cache_read_input_tokens',
                   'cache_creation_input_tokens': 'cache_creation_input_tokens', 'output_tokens': 'output_tokens'}
    else:
        raise ValueError('Host must be codex or claude')
    measured = {}
    for name, source in mapping.items():
        values = [(event.get('usage') or {}).get(source) for event in terminals]
        if not values or any(value is None for value in values):
            continue
        if any(type(value) is not int or value < 0 for value in values):
            raise ValueError('Invalid token measurement: ' + source)
        measured[name] = sum(values)
    if host == 'claude' and all(name in measured for name in ('uncached_input_tokens', 'cached_input_tokens', 'cache_creation_input_tokens')):
        measured['input_tokens'] = sum(measured[name] for name in ('uncached_input_tokens', 'cached_input_tokens', 'cache_creation_input_tokens'))
    if 'cached_input_tokens' in measured and 'input_tokens' in measured and measured['cached_input_tokens'] > measured['input_tokens']:
        raise ValueError('Cached tokens exceed input tokens')
    return {'terminal_success': success, 'measured': measured}


def verify(directory, host=None, model=None, trace=None, label='candidate'):
    if not label or any(character not in 'abcdefghijklmnopqrstuvwxyz0123456789-' for character in label):
        raise ValueError('Label must contain lowercase letters, digits or hyphens')
    metadata = json.loads((directory / 'task.json').read_text())
    case = next(row for row in cases() if row['id'] == metadata['case'])
    if metadata['case_digest'] != hashlib.sha256(json.dumps(case, sort_keys=True).encode()).hexdigest():
        raise ValueError('Case changed after preparation; prepare a new fixture')
    workspace = directory / 'workspace'
    changed = set(filter(None, git(workspace, 'diff', '--name-only', '--no-renames', metadata['seed_commit']).splitlines()))
    changed.update(filter(None, git(workspace, 'ls-files', '--others', '--exclude-standard').splitlines()))
    outside_scope = sorted(changed - {case['path']})
    sys.path.insert(0, str(ROOT / 'scripts'))
    from toolchain import environment
    started = time.monotonic()
    code = None
    log_path = directory / (label + '-validation.log')
    if not outside_scope:
        # Run checks from the prepared commit; modifying a test blocks execution.
        with log_path.open('w') as log:
            try:
                code = subprocess.run(case['check'], cwd=workspace, env=environment(ROOT),
                                      stdout=log, stderr=subprocess.STDOUT, timeout=240).returncode
            except subprocess.TimeoutExpired:
                code = 124
    result = {**metadata, 'kind': 'fixture-check', 'changed_files': sorted(changed),
              'out_of_scope': outside_scope, 'command': case['check'], 'exit_code': code,
              'validation_seconds': round(time.monotonic() - started, 3),
              'validation_log': str(log_path) if code is not None else None,
              'quality_pass': not outside_scope and code == 0 and case['success_marker'] in log_path.read_text(),
              'measured': {}}
    if host or model or trace:
        if not (host and model and trace):
            raise ValueError('An agent observation requires --host, --model and --trace together')
        raw = trace.read_text()
        usage = trace_usage(host, raw)
        result.update(kind='agent-observation', host=host, model=model, trace=str(trace.resolve()),
                      trace_sha256=hashlib.sha256(raw.encode()).hexdigest(), **usage)
        result['quality_pass'] = result['quality_pass'] and usage['terminal_success'] and bool(changed)
    (directory / (label + '-result.json')).write_text(json.dumps(result, ensure_ascii=False, indent=2) + '\n')
    return result


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    modes = parser.add_subparsers(dest='mode', required=True)
    modes.add_parser('list')
    modes.add_parser('check')
    setup = modes.add_parser('prepare')
    setup.add_argument('case')
    setup.add_argument('directory', type=Path)
    setup.add_argument('--revision', default='HEAD')
    grade = modes.add_parser('verify')
    grade.add_argument('directory', type=Path)
    grade.add_argument('--host', choices=('codex', 'claude'))
    grade.add_argument('--model')
    grade.add_argument('--trace', type=Path)
    grade.add_argument('--label', default='candidate')
    args = parser.parse_args()
    try:
        rows = cases()
        if args.mode == 'list':
            result = [{key: row[key] for key in ('id', 'title', 'prompt', 'path', 'check')} for row in rows]
        elif args.mode == 'check':
            for row in rows:
                if (ROOT / row['path']).read_text().count(row['before']) != 1:
                    raise ValueError('Seed anchor changed: ' + row['id'])
            result = {'cases': len(rows), 'scope': 'Fixture definitions only; no model evaluated.'}
        elif args.mode == 'prepare':
            result = prepare(next(row for row in rows if row['id'] == args.case), args.directory, args.revision)
        else:
            result = verify(args.directory.resolve(), args.host, args.model, args.trace, args.label)
        print(json.dumps(result, ensure_ascii=False, indent=2))
        return int(args.mode == 'verify' and not result['quality_pass'])
    except (ValueError, OSError, KeyError, StopIteration, subprocess.SubprocessError) as error:
        print('FAIL: {}'.format(error), file=sys.stderr)
        return 2


if __name__ == '__main__':
    sys.exit(main())
