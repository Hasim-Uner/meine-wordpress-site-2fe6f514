"""Toolchain failures must be actionable and must not start misleading test runs."""
import contextlib
import io
import json
import os
from pathlib import Path
import subprocess
import sys
import tempfile
import textwrap
import unittest
from unittest.mock import patch

ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(ROOT / 'scripts'))
import check
import toolchain


class ToolchainTests(unittest.TestCase):
    def test_wrong_php_stops_before_checks(self):
        with patch.object(check, 'changes', return_value=(['README.md'], 'HEAD', '', '')), \
                patch.object(check, 'run_checks') as run, \
                patch.object(toolchain, 'capture', return_value='7.3.29'), \
                patch.object(toolchain, 'environment', return_value=dict(os.environ)), \
                patch.object(sys, 'argv', ['check.py']), contextlib.redirect_stdout(io.StringIO()) as output:
            self.assertEqual(check.main(), 1)
        run.assert_not_called()
        self.assertIn('required ' + toolchain.CONFIG['php']['version'], output.getvalue())
        self.assertIn(toolchain.SETUP, output.getvalue())

    def test_plan_never_probes_or_installs_tools(self):
        with patch.object(check, 'changes', return_value=(['scripts/new.py'], 'HEAD', '', '')), \
                patch.object(toolchain, 'doctor', side_effect=AssertionError('doctor called')), \
                patch.object(toolchain, 'setup', side_effect=AssertionError('setup called')), \
                patch.object(sys, 'argv', ['check.py', '--plan']), contextlib.redirect_stdout(io.StringIO()) as output:
            self.assertEqual(check.main(), 0)
        self.assertEqual(json.loads(output.getvalue())['profile'], 'full')

    def test_basic_doctor_needs_no_node_composer_or_browser(self):
        with patch.object(toolchain, 'capture', return_value=toolchain.CONFIG['php']['version']) as capture, \
                contextlib.redirect_stdout(io.StringIO()):
            self.assertEqual(toolchain.doctor(full=False, env=dict(os.environ)), 0)
        self.assertEqual([c.args[0][0] for c in capture.call_args_list], ['php'])

    def test_missing_php_and_timeout_are_reported_without_traceback(self):
        for failure in [FileNotFoundError('php missing'), subprocess.TimeoutExpired('php', 30)]:
            with self.subTest(failure=type(failure).__name__), \
                    patch.object(toolchain, 'capture', side_effect=failure), \
                    contextlib.redirect_stdout(io.StringIO()) as output:
                self.assertEqual(toolchain.doctor(full=False, env=dict(os.environ)), 1)
                self.assertIn('FAIL PHP', output.getvalue())

    def test_full_doctor_catches_missing_packages_and_browser(self):
        def capture(command, *args):
            if command[:2] == ['php', '-r']:
                return toolchain.CONFIG['php']['version'] if command[-1] == 'echo PHP_VERSION;' else 'ready'
            if command[:2] == ['node', '-p']:
                return toolchain.CONFIG['node']['version']
            if command[0] == 'npm':
                return toolchain.CONFIG['node']['npm']
            if command[0] == 'composer':
                return 'Composer version ' + toolchain.CONFIG['composer']['version'] + ' 2026-08-27 13:34:23'
            if command[-1] == '--version':
                return 'PHPStan - PHP Static Analysis Tool 1.12.33'
            raise ValueError('missing fixture dependency')
        with patch.object(toolchain, 'capture', side_effect=capture), \
                contextlib.redirect_stdout(io.StringIO()) as output:
            self.assertEqual(toolchain.doctor(env=dict(os.environ)), 1)
        self.assertIn('FAIL Node dependencies', output.getvalue())
        self.assertIn('FAIL Browser', output.getvalue())

    def test_worktrees_resolve_shared_tool_directory(self):
        with patch.object(toolchain, 'capture', return_value='/tmp/parent/.git'):
            self.assertEqual(toolchain.tool_dir(Path('/tmp/worktree')), Path('/tmp/parent/.build/toolchain').resolve())
        with patch.object(toolchain, 'capture', return_value='.git'):
            self.assertEqual(toolchain.tool_dir(Path('/tmp/parent')), Path('/tmp/parent/.build/toolchain').resolve())

    def test_ci_never_shadows_action_installed_tools(self):
        with patch.dict(os.environ, {'GITHUB_ACTIONS': 'true', 'PATH': '/ci/bin'}, clear=True), \
                patch.object(toolchain, 'tool_dir', side_effect=AssertionError('local tools selected')):
            self.assertEqual(toolchain.environment()['PATH'], '/ci/bin')

    def test_checksum_mismatch_rejected_before_use(self):
        with tempfile.TemporaryDirectory() as directory:
            path = Path(directory) / 'archive.tar.xz'
            path.write_bytes(b'corrupt download')
            with patch.object(toolchain.urllib.request, 'urlopen', side_effect=AssertionError('network')), \
                    self.assertRaisesRegex(ValueError, 'Checksum mismatch'):
                toolchain.download('https://example.invalid/archive', path, '0' * 64)

    def test_versions_output_needs_no_installed_tools(self):
        with tempfile.TemporaryDirectory() as directory:
            output = Path(directory) / 'output'
            output.write_text('runtime=true\n')
            with patch.object(toolchain, 'capture', side_effect=AssertionError('tools invoked')), \
                    contextlib.redirect_stdout(io.StringIO()):
                toolchain.versions(output)
            self.assertIn('runtime=true\nphp=' + toolchain.CONFIG['php']['version'], output.read_text())

    def test_legacy_manual_rollback_keeps_selected_theme_revision(self):
        workflow = (ROOT / '.github/workflows/deploy.yml').read_text()
        step = workflow.split('      - name: Read pinned toolchain\n', 1)[1].split('\n      - name:', 1)[0]
        script = textwrap.dedent(step.split('        run: |\n', 1)[1])
        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            def git(*args):
                return subprocess.check_output(['git', *args], cwd=root, stderr=subprocess.DEVNULL, text=True).strip()
            git('init', '-q')
            git('config', 'user.email', 'toolchain@example.invalid')
            git('config', 'user.name', 'Toolchain regression')
            (root / 'theme.txt').write_text('old theme')
            git('add', 'theme.txt')
            git('commit', '-qm', 'old release')
            release = git('rev-parse', 'HEAD')
            (root / '.toolchain.json').write_text(json.dumps(toolchain.CONFIG))
            git('add', '.toolchain.json')
            git('commit', '-qm', 'workflow with toolchain')
            workflow_sha = git('rev-parse', 'HEAD')
            git('remote', 'add', 'origin', str(root))
            git('checkout', '-q', release)
            output = root / 'github-output'
            subprocess.run(['bash', '-e', '-o', 'pipefail', '-c', script], cwd=root, check=True,
                           stdout=subprocess.PIPE, stderr=subprocess.PIPE,
                           env=dict(os.environ, WORKFLOW_SHA=workflow_sha,
                                    RUNNER_TEMP=directory, GITHUB_OUTPUT=str(output)))
            self.assertEqual(git('rev-parse', 'HEAD'), release)
            self.assertFalse((root / '.toolchain.json').exists())
            self.assertIn('php=' + toolchain.CONFIG['php']['version'], output.read_text())


if __name__ == '__main__':
    unittest.main()
