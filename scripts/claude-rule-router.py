#!/usr/bin/env python3
"""UserPromptSubmit-Hook fuer Claude Code: dauerhafte Regeln an den richtigen Ort.

Erkennt Formulierungen wie "ab jetzt", "merk dir" oder "nie wieder" und gibt
Claude den Hinweis, nach der Aufgabe zu fragen, ob und wo die Regel gespeichert
wird. Das Ziel bestimmt AGENTS.md (Update triggers); dieser Hook kopiert keine
Regeln. Keine KI, keine Schreibzugriffe. Ohne Treffer: keine Ausgabe, Exit 0.
"""
import json
import re
import sys

RULE_MARKERS = re.compile(
    r'\b(ab jetzt|ab sofort|ab heute|von nun an|von jetzt an|k[uü]nftig|zuk[uü]nftig|'
    r'in zukunft|f[uü]r die zukunft|merk(?:e)? dir|grunds[aä]tzlich|generell gilt|'
    r'nie wieder|nie mehr|niemals|auf keinen fall mehr|regel:|'
    r'from now on|going forward|remember that|always use|never use)\b',
    re.IGNORECASE)
SENTENCE_END = re.compile(r'[.!?\n]')
MAX_QUOTE = 160


def quote(prompt, match):
    """Der Satz, in dem der Treffer steht, gekuerzt."""
    before = [m.end() for m in SENTENCE_END.finditer(prompt, 0, match.start())]
    after = SENTENCE_END.search(prompt, match.end())
    text = ' '.join(prompt[before[-1] if before else 0:after.end() if after else None].split())
    return text if len(text) <= MAX_QUOTE else text[:MAX_QUOTE - 1] + '…'


def evaluate(prompt):
    prompt = prompt or ''
    if prompt.lstrip().startswith(('/', '<')):
        return None
    match = RULE_MARKERS.search(prompt)
    if not match:
        return None
    rule = quote(prompt, match)
    context = (
        f'Regel-Router: Die Nachricht enthaelt moeglicherweise eine dauerhafte Regel: "{rule}". '
        'Erledige zuerst die eigentliche Aufgabe. Ist es eine dauerhafte Regel, frage danach '
        'einmal mit AskUserQuestion, ob und wo sie gespeichert wird. Optionen: die passende '
        'kanonische Quelle laut AGENTS.md "Update triggers" oder der zustaendige Skill '
        '(1-2 konkrete Ziele nennen), dazu "nicht speichern". Vorher mit rg pruefen, ob die '
        'Regel dort schon steht. Erst nach Zustimmung schreiben, nichts duplizieren. '
        'Ist es keine dauerhafte Regel, diesen Hinweis ignorieren.'
    )
    return {
        'systemMessage': 'Regel-Router: moegliche dauerhafte Regel erkannt.',
        'hookSpecificOutput': {'hookEventName': 'UserPromptSubmit',
                               'additionalContext': context},
    }


def main():
    try:
        event = json.load(sys.stdin)
    except ValueError:
        return 0
    result = evaluate(event.get('prompt'))
    if result:
        print(json.dumps(result, ensure_ascii=False))
    return 0


if __name__ == '__main__':
    sys.exit(main())
