"""Regression coverage for skipped-check and skipped-deploy decisions."""
import contextlib
import importlib.util
import io
import os
from pathlib import Path
import shutil
import subprocess
import sys
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[2]
spec = importlib.util.spec_from_file_location('repo_check', ROOT / 'scripts/check.py')
check = importlib.util.module_from_spec(spec)
spec.loader.exec_module(check)


class SelectionTests(unittest.TestCase):
    def test_documentation_does_not_install_or_deploy_runtime(self):
        plan = check.classify(['docs/architecture/LIVE_STATUS.md', 'content/article.md'])
        self.assertEqual(plan['profile'], 'docs')
        self.assertFalse(plan['runtime'])
        self.assertFalse(plan['deploy'])
        names = {name for name, _ in check.checks(plan, 'HEAD', '')}
        self.assertTrue({'architecture', 'php-syntax', 'canon', 'e3-canon', 'german-copy'} <= names)
        self.assertTrue({'theme-build', 'forms', 'php-analysis', 'skill-suites'}.isdisjoint(names))

    def test_agent_changes_run_regressions_without_browser_or_deploy(self):
        for path in ['AGENTS.md', 'CLAUDE.md', '.claude/settings.json',
                     'agents/skills/agent-system-maintenance/SKILL.md',
                     '.agents/skills/frontend-system', '.claude/skills/frontend-system']:
            with self.subTest(path=path):
                plan = check.classify(['docs/note.md', path])
                self.assertEqual(plan['profile'], 'agents')
                self.assertTrue(plan['skills'])
                self.assertFalse(plan['runtime'])
                self.assertFalse(plan['deploy'])

    def test_runtime_tooling_and_unknown_paths_cannot_skip_checks(self):
        for path in ['blocksy-child/inc/helpers.php', 'blocksy-child/assets/css/system.css',
                     'scripts/validate-skills.py', 'package.json', 'package-lock.json',
                     'composer.lock', '.github/workflows/ci.yml', 'llms.txt',
                     'new-build-config.toml', 'docs/new-generator.php', 'agents/new-hook.py']:
            with self.subTest(path=path):
                plan = check.classify([path, 'README.md'])
                self.assertTrue(plan['runtime'])
                self.assertTrue(plan['deploy'])
                self.assertTrue(plan['skills'])

    def test_full_suite_keeps_existing_release_gates(self):
        names = {name for name, _ in check.checks(check.classify(['unknown']), 'HEAD', '')}
        self.assertTrue({'lead-path', 'funnel-routing', 'crawler-signals', 'navigation',
                         'motion', 'spacing', 'forms', 'intake', 'permalinks',
                         'provisioning', 'pricing', 'navigation-ui', 'php-analysis',
                         'deploy-config', 'theme-build', 'theme-header'} <= names)

    def test_missing_history_is_conservative_and_manual_full_does_not_force_deploy(self):
        fallback = check.classify([], fallback='missing comparison base')
        self.assertTrue(fallback['runtime'])
        self.assertTrue(fallback['deploy'])
        manual = check.classify(['README.md'], force_full=True)
        self.assertTrue(manual['runtime'])
        self.assertFalse(manual['deploy'])

    def test_runner_keeps_failures_visible_and_propagates_failure(self):
        output = io.StringIO()
        with contextlib.redirect_stdout(output):
            result = check.run_checks([
                ('success', [sys.executable, '-c', 'print("successful-detail")']),
                ('failure', [sys.executable, '-c', 'print("failure-detail"); raise SystemExit(9)']),
            ])
        self.assertEqual(result, 1)
        self.assertIn('failure-detail', output.getvalue())
        self.assertNotIn('successful-detail', output.getvalue())
        self.assertIn('2 checks, 1 failures', output.getvalue())

    def test_missing_executable_cannot_pass(self):
        with contextlib.redirect_stdout(io.StringIO()):
            self.assertEqual(check.run_checks([('missing', ['/nonexistent/repo-check-tool'])]), 1)


class GitSelectionTests(unittest.TestCase):
    def setUp(self):
        self.directory = tempfile.TemporaryDirectory(prefix='check-selection-')
        self.root = Path(self.directory.name)
        self.addCleanup(self.directory.cleanup)
        self.git('init', '-q')
        self.git('config', 'user.email', 'checks@example.invalid')
        self.git('config', 'user.name', 'Check regression')
        self.write('docs/note.md', 'original\n')
        self.write('blocksy-child/example.php', '<?php\n')
        self.git('add', '.')
        self.git('commit', '-qm', 'initial fixture')
        self.base = self.git('rev-parse', 'HEAD').strip()
        self.git('update-ref', 'refs/remotes/origin/main', self.base)

    def git(self, *args):
        return check.git(*args, root=self.root)

    def write(self, path, content):
        file = self.root / path
        file.parent.mkdir(parents=True, exist_ok=True)
        file.write_text(content)

    def selected(self, **kwargs):
        return check.changes(root=self.root, **kwargs)

    def test_local_selection_includes_committed_staged_unstaged_and_untracked(self):
        self.write('docs/committed.md', 'committed\n')
        self.git('add', '.')
        self.git('commit', '-qm', 'second fixture')
        self.write('docs/staged.md', 'staged\n')
        self.git('add', 'docs/staged.md')
        self.write('docs/note.md', 'unstaged\n')
        self.write('new config.json', '{}\n')
        paths, _, head, reason = self.selected()
        self.assertEqual(set(paths), {'docs/committed.md', 'docs/staged.md', 'docs/note.md', 'new config.json'})
        self.assertEqual(head, '')
        self.assertEqual(reason, '')
        self.assertTrue(check.classify(paths)['runtime'])

    def test_rename_out_of_runtime_keeps_runtime_checks(self):
        self.git('mv', 'blocksy-child/example.php', 'docs/example.md')
        paths, *_ = self.selected()
        self.assertIn('blocksy-child/example.php', paths)
        self.assertIn('docs/example.md', paths)
        self.assertTrue(check.classify(paths)['deploy'])

    def test_deletion_of_runtime_file_still_requires_deploy(self):
        (self.root / 'blocksy-child/example.php').unlink()
        paths, *_ = self.selected()
        self.assertTrue(check.classify(paths)['deploy'])

    def test_zero_or_missing_base_never_silently_selects_docs(self):
        for base in ['0' * 40, 'nonexistent-ref']:
            paths, _, _, reason = self.selected(base=base, head='HEAD')
            self.assertTrue(reason)
            self.assertTrue(check.classify(paths, fallback=reason)['runtime'])

    def test_explicit_committed_mode_rejects_dirty_or_wrong_checkout(self):
        self.write('docs/note.md', 'modified\n')
        with self.assertRaises(ValueError):
            self.selected(base=self.base, head='HEAD')
        self.git('add', '.')
        self.git('commit', '-qm', 'next fixture')
        with self.assertRaises(ValueError):
            self.selected(base=self.base, head=self.base)

    def test_committed_docs_diff_is_lightweight(self):
        self.write('docs/note.md', 'committed\n')
        self.git('add', '.')
        self.git('commit', '-qm', 'docs fixture')
        paths, _, _, reason = self.selected(base=self.base, head='HEAD')
        self.assertEqual(paths, ['docs/note.md'])
        self.assertFalse(check.classify(paths, fallback=reason)['deploy'])

    def successor(self, path):
        self.write(path, 'new revision\n')
        self.git('add', '.')
        self.git('commit', '-qm', 'successor fixture')
        self.git('update-ref', 'refs/remotes/origin/main', self.git('rev-parse', 'HEAD'))
        self.git('checkout', '-q', self.base)

    def test_documentation_successor_cannot_suppress_pending_runtime_release(self):
        self.successor('docs/note.md')
        self.assertTrue(check.deployment_current(root=self.root))

    def test_older_runtime_cannot_overwrite_newer_main(self):
        self.successor('blocksy-child/example.php')
        self.assertFalse(check.deployment_current(root=self.root))

    def test_removed_main_history_cannot_deploy_automatically(self):
        self.write('docs/note.md', 'branch only\n')
        self.git('add', '.')
        self.git('commit', '-qm', 'branch-only fixture')
        self.assertFalse(check.deployment_current(root=self.root))

    def test_current_main_can_deploy(self):
        self.assertTrue(check.deployment_current(root=self.root))

    def guard(self, script, base='HEAD'):
        for name in ['check-diff.sh', script]:
            dest = self.root / 'scripts' / name
            dest.parent.mkdir(exist_ok=True)
            shutil.copy2(ROOT / 'scripts' / name, dest)
        return subprocess.run(['bash', 'scripts/' + script, base], cwd=self.root,
                              stdout=subprocess.PIPE, stderr=subprocess.STDOUT, text=True)

    def test_copy_guard_sees_untracked_docs_with_spaces(self):
        self.write('docs/new page.md', 'Wir pruefen die Seite.\n')
        result = self.guard('check-german-copy.sh')
        self.assertNotEqual(result.returncode, 0)
        self.assertIn('German copy guard failed', result.stdout)

    def test_canon_guard_sees_untracked_content_but_respects_research_exclusion(self):
        self.write('seo-research/export.csv', f'price,{590} EUR\n')
        self.assertEqual(self.guard('lint-canon-drift.sh').returncode, 0)
        self.write('content/new.md', f'Preis: {590} EUR\n')
        result = self.guard('lint-canon-drift.sh')
        self.assertNotEqual(result.returncode, 0)
        self.assertIn('Canon drift guard failed', result.stdout)

    def test_invalid_diff_ref_fails_instead_of_passing_empty_diff(self):
        for script in ['check-german-copy.sh', 'lint-canon-drift.sh']:
            self.assertNotEqual(self.guard(script, base='missing-ref').returncode, 0)

    def test_untracked_directory_symlink_is_not_followed_by_copy_guards(self):
        self.write('.gitignore', 'cache/\n')
        self.write('cache/ignore.md', f'{590} EUR\n')
        (self.root / 'dependencies').symlink_to(self.root / 'cache', target_is_directory=True)
        result = self.guard('lint-canon-drift.sh')
        self.assertEqual(result.returncode, 0, result.stdout)
        self.assertNotIn('error:', result.stdout)


if __name__ == '__main__':
    unittest.main()
