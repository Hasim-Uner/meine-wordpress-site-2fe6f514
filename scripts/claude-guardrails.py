#!/usr/bin/env python3
"""PreToolUse-Hook fuer Claude Code: Leitplanken aus AGENTS.md (stdlib only).

1. Aenderungen an .github/workflows/deploy.yml brauchen eine Bestaetigung.
2. Weniger data-track-* Hooks nach einer Aenderung brauchen eine Bestaetigung.
3. Vor `git push` laufen lint:architecture und lint:php; ein Fehler sperrt den Push.

Liest das Hook-Event als JSON von stdin. Ohne Befund: keine Ausgabe, Exit 0.
Ein Hinweisgeber, keine Sicherheitsgrenze: Umwege ueber Bash erkennt er nur grob.
"""
import json
import os
from pathlib import Path
import re
import shutil
import subprocess
import sys

DEPLOY_WORKFLOW = '.github/workflows/deploy.yml'
TRACK_HOOK = re.compile(r'data-track-[a-z0-9_-]*', re.IGNORECASE)
GIT_PUSH = re.compile(r'\bgit\b(?:\s+-[cC]\s+\S+)*\s+push\b')
DEPLOY_WRITE = re.compile(
    r'(\bsed\s+(-\S*\s+)*-i|\bperl\s+-\S*i|\btee\b|\bmv\b|\bcp\b|\brm\b|>{1,2}\s*\S*deploy\.yml)')
LINTS = (
    ('lint:architecture', ['bash', 'scripts/validate-architecture.sh']),
    ('lint:php', ['bash', '-c', "find blocksy-child -name '*.php' -print0 | xargs -0 -n1 php -l"]),
)


def project_root():
    root = os.environ.get('CLAUDE_PROJECT_DIR')
    if root:
        return Path(root)
    return Path(__file__).resolve().parents[1]


def relative(path, root):
    try:
        return Path(path).resolve().relative_to(root.resolve()).as_posix()
    except (ValueError, OSError):
        return str(path)


def decision(kind, reason):
    return {'hookSpecificOutput': {'hookEventName': 'PreToolUse',
                                   'permissionDecision': kind,
                                   'permissionDecisionReason': reason}}


def track_count(text):
    return len(TRACK_HOOK.findall(text or ''))


def removed_track_hooks(tool, data, root):
    """Wie viele data-track-* Vorkommen die Aenderung entfernt (0 = keine)."""
    path = Path(data.get('file_path') or '')
    if not path.is_absolute():
        path = root / path
    if tool == 'Write':
        try:
            before = track_count(path.read_text(encoding='utf-8', errors='replace'))
        except OSError:
            return 0
        return max(0, before - track_count(data.get('content')))
    edits = data.get('edits') if tool == 'MultiEdit' else [data]
    removed = 0
    for edit in edits or []:
        removed += track_count(edit.get('old_string')) - track_count(edit.get('new_string'))
    return max(0, removed)


def check_file_edit(tool, data, root):
    target = relative(data.get('file_path') or '', root)
    if target == DEPLOY_WORKFLOW:
        return decision('ask', f'{DEPLOY_WORKFLOW} steuert den Produktions-Deploy. '
                               'Laut AGENTS.md nur aendern, wenn Deploy-Verhalten die Aufgabe ist.')
    removed = removed_track_hooks(tool, data, root)
    if removed:
        return decision('ask', f'Die Aenderung an {target} entfernt {removed} data-track-* Hook(s). '
                               'AGENTS.md: data-track-* auf Conversion-Flaechen erhalten. '
                               'Nur bestaetigen, wenn das gewollt ist.')
    return None


def run_lints(root):
    if not shutil.which('php'):
        return decision('ask', 'Vor dem Push muss lint:php laufen, aber php ist nicht installiert. '
                               'Push trotzdem freigeben?')
    for name, command in LINTS:
        try:
            done = subprocess.run(command, cwd=root, stdout=subprocess.PIPE,
                                  stderr=subprocess.STDOUT, timeout=100)
        except subprocess.TimeoutExpired:
            return decision('deny', f'{name} lief laenger als 100 s. Push gesperrt; Check manuell ausfuehren.')
        if done.returncode:
            tail = '\n'.join(done.stdout.decode(errors='replace').strip().splitlines()[-15:])
            return decision('deny', f'Push gesperrt: npm run {name} schlaegt fehl '
                                    f'(Exit {done.returncode}). Erst beheben.\n{tail}')
    return None


def check_bash(data, root):
    command = data.get('command') or ''
    if 'deploy.yml' in command and DEPLOY_WRITE.search(command):
        return decision('ask', f'Der Befehl scheint {DEPLOY_WORKFLOW} zu aendern. '
                               'Laut AGENTS.md nur, wenn Deploy-Verhalten die Aufgabe ist.')
    if GIT_PUSH.search(command):
        return run_lints(root)
    return None


def evaluate(event, root):
    tool = event.get('tool_name') or ''
    data = event.get('tool_input') or {}
    if tool in {'Edit', 'MultiEdit', 'Write'}:
        return check_file_edit(tool, data, root)
    if tool == 'Bash':
        return check_bash(data, root)
    return None


def main():
    try:
        event = json.load(sys.stdin)
    except ValueError:
        return 0
    result = evaluate(event, project_root())
    if result:
        print(json.dumps(result, ensure_ascii=False))
    return 0


if __name__ == '__main__':
    sys.exit(main())
