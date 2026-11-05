import importlib.util
import json
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path
from unittest import mock

ROOT = Path(__file__).resolve().parents[4]


def module_at(name, path):
    spec = importlib.util.spec_from_file_location(name, path)
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    return module


guardrails = module_at('claude_guardrails', ROOT / 'scripts/claude-guardrails.py')
router = module_at('claude_rule_router', ROOT / 'scripts/claude-rule-router.py')


def verdict(result):
    return result and result['hookSpecificOutput']['permissionDecision']


class GuardrailTests(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory()
        self.root = Path(self.tmp.name)

    def tearDown(self):
        self.tmp.cleanup()

    def edit(self, tool, data):
        return guardrails.evaluate({'tool_name': tool, 'tool_input': data}, self.root)

    def test_deploy_workflow_edit_asks(self):
        path = self.root / '.github/workflows/deploy.yml'
        self.assertEqual(verdict(self.edit('Edit', {'file_path': str(path), 'old_string': 'a',
                                                    'new_string': 'b'})), 'ask')
        self.assertIsNone(self.edit('Edit', {'file_path': str(self.root / '.github/workflows/ci.yml'),
                                             'old_string': 'a', 'new_string': 'b'}))

    def test_removed_track_hook_asks_and_kept_hook_passes(self):
        old = '<a data-track-action="cta" data-track-section="hero">'
        self.assertEqual(verdict(self.edit('Edit', {'file_path': 'x.php', 'old_string': old,
                                                    'new_string': '<a>'})), 'ask')
        self.assertIsNone(self.edit('Edit', {'file_path': 'x.php', 'old_string': old,
                                             'new_string': old.replace('hero', 'footer')}))
        multi = {'file_path': 'x.php', 'edits': [{'old_string': old, 'new_string': old},
                                                  {'old_string': 'data-track-x', 'new_string': ''}]}
        self.assertEqual(verdict(self.edit('MultiEdit', multi)), 'ask')

    def test_write_compares_with_file_on_disk(self):
        page = self.root / 'page.php'
        page.write_text('<a data-track-action="cta">', encoding='utf-8')
        self.assertEqual(verdict(self.edit('Write', {'file_path': str(page), 'content': '<a>'})), 'ask')
        self.assertIsNone(self.edit('Write', {'file_path': str(self.root / 'new.php'), 'content': '<a>'}))

    def test_bash_deploy_write_asks_and_read_passes(self):
        self.assertEqual(verdict(self.edit('Bash', {'command': "sed -i 's/a/b/' .github/workflows/deploy.yml"})),
                         'ask')
        self.assertIsNone(self.edit('Bash', {'command': 'cat .github/workflows/deploy.yml'}))

    def test_push_runs_lints_and_denies_on_failure(self):
        for command in ('git push -u origin feature', 'git -C repo push', 'cd x && git push'):
            with mock.patch.object(guardrails, 'run_lints', return_value='ran') as lints:
                self.assertEqual(self.edit('Bash', {'command': command}), 'ran')
                lints.assert_called_once()
        self.assertIsNone(self.edit('Bash', {'command': 'git status && git log'}))
        failed = subprocess.CompletedProcess([], 1, stdout=b'ERROR: boom')
        with mock.patch.object(guardrails.shutil, 'which', return_value='/usr/bin/php'), \
                mock.patch.object(guardrails.subprocess, 'run', return_value=failed):
            result = guardrails.run_lints(self.root)
        self.assertEqual(verdict(result), 'deny')
        self.assertIn('lint:architecture', result['hookSpecificOutput']['permissionDecisionReason'])
        with mock.patch.object(guardrails.shutil, 'which', return_value=None):
            self.assertEqual(verdict(guardrails.run_lints(self.root)), 'ask')

    def test_cli_is_silent_without_findings(self):
        done = subprocess.run([sys.executable, str(ROOT / 'scripts/claude-guardrails.py')],
                              input=json.dumps({'tool_name': 'Read', 'tool_input': {}}).encode(),
                              stdout=subprocess.PIPE, check=True)
        self.assertEqual(done.stdout, b'')


class RuleRouterTests(unittest.TestCase):
    def test_rule_phrases_trigger_with_sentence_quote(self):
        result = router.evaluate('Bau die Seite um. Ab jetzt keine Gedankenstriche in Headlines! Danke.')
        context = result['hookSpecificOutput']['additionalContext']
        self.assertIn('"Ab jetzt keine Gedankenstriche in Headlines!"', context)
        self.assertIn('AGENTS.md', context)
        for prompt in ('Merk dir: CTA immer auf Projektanfrage.', 'from now on use Sie',
                       'Künftig bitte nur kurze Commits.'):
            self.assertIsNotNone(router.evaluate(prompt), prompt)

    def test_ordinary_prompts_and_commands_stay_quiet(self):
        for prompt in ('Fix den Footer auf Mobile.', '/mods screen-share', '', None,
                       '<task-notification>ab jetzt</task-notification>'):
            self.assertIsNone(router.evaluate(prompt), prompt)

    def test_long_sentence_is_shortened(self):
        result = router.evaluate('Ab jetzt ' + 'sehr ' * 80 + 'lang.')
        context = result['hookSpecificOutput']['additionalContext']
        self.assertIn('…', context)


if __name__ == '__main__':
    unittest.main()
