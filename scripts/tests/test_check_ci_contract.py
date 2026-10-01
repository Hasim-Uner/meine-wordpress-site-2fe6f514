"""Execute release guards against removed requirements, not just happy paths."""
import contextlib
import io
import os
from pathlib import Path
import re
import shutil
import subprocess
import sys
import tempfile
import textwrap
import unittest

ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(ROOT / 'scripts'))
import check


class ArchitectureGuardTests(unittest.TestCase):
    def setUp(self):
        temporary = tempfile.TemporaryDirectory(prefix='ci-contract-')
        self.addCleanup(temporary.cleanup)
        self.root = Path(temporary.name)
        # The guard needs the real route/skill contracts. Only workflow and
        # build files are copied and mutated; the original checkout is read-only.
        for source in ROOT.iterdir():
            if source.name in {'.git', '.github', 'scripts', 'node_modules', 'vendor', '.build'}:
                continue
            (self.root / source.name).symlink_to(source, target_is_directory=source.is_dir())
        shutil.copytree(ROOT / '.github', self.root / '.github', symlinks=True)
        scripts = self.root / 'scripts'
        scripts.mkdir()
        for source in (ROOT / 'scripts').iterdir():
            if source.name in {'validate-architecture.sh', 'build-theme-dist.sh'}:
                shutil.copy2(source, scripts / source.name)
            else:
                (scripts / source.name).symlink_to(source, target_is_directory=source.is_dir())

    def run_guard(self):
        return subprocess.run(['bash', 'scripts/validate-architecture.sh'], cwd=self.root,
                              text=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)

    def test_intact_guard_reports_every_release_requirement(self):
        result = self.run_guard()
        self.assertEqual(result.returncode, 0, result.stdout)
        for label in ['CI core runs shared repository checks',
                      'CI keeps isolated form browser coverage',
                      'CI keeps isolated navigation browser coverage',
                      'CI keeps PHPStan coverage', 'CI restores the PHPStan result cache',
                      'Automatic deploy requires merged-PR provenance',
                      'Theme build keeps legacy CSS coverage']:
            self.assertIn('OK: ' + label, result.stdout)
        self.assertEqual(result.stdout.count('=== Verdict ==='), 1)

    def test_removing_each_ci_requirement_fails(self):
        target = self.root / '.github/workflows/ci.yml'
        original = target.read_text()
        requirements = [
            'run: python3 scripts/check.py --skip-browser --skip-analysis --skip-doctor',
            'npm run test:forms', 'npm run test:navigation-ui',
            'php vendor/phpstan/phpstan/phpstan.phar analyse',
            'actions/cache/restore@v5', 'actions/cache/save@v5', 'phpstan-result-v1-',
        ]
        for requirement in requirements:
            with self.subTest(requirement=requirement):
                self.assertIn(requirement, original)
                target.write_text(original.replace(requirement, 'disabled-requirement'))
                result = self.run_guard()
                self.assertNotEqual(result.returncode, 0, result.stdout)
                self.assertIn('Architecture validation failed', result.stdout)
        target.write_text(original)

    def test_removing_each_deploy_requirement_fails(self):
        target = self.root / '.github/workflows/deploy.yml'
        original = target.read_text()
        for requirement in ['phpstan-cache:', 'Refresh PHPStan result cache on main',
                            'actions/cache/save@v5',
                            'python3 scripts/check.py --plan --github-output',
                            "needs.revision.outputs.deploy == 'true'",
                            'commits/${RELEASE_SHA}/pulls']:
            with self.subTest(requirement=requirement):
                self.assertIn(requirement, original)
                target.write_text(original.replace(requirement, 'disabled-requirement'))
                result = self.run_guard()
                self.assertNotEqual(result.returncode, 0, result.stdout)
        target.write_text(original)

    def test_consolidated_css_coverage_cannot_be_removed(self):
        target = self.root / 'scripts/build-theme-dist.sh'
        original = target.read_text()
        for requirement in ['scripts/audit-css-architecture.py',
                            'scripts/audit-css-values.py', 'scripts/audit-legacy-nx-css.py']:
            with self.subTest(requirement=requirement):
                self.assertIn(requirement, original)
                target.write_text(original.replace(requirement, 'disabled-requirement'))
                self.assertNotEqual(self.run_guard().returncode, 0)
        target.write_text(original)


class CIGateTests(unittest.TestCase):
    def test_gate_fails_on_failure_cancellation_or_skipped_partition(self):
        workflow = (ROOT / '.github/workflows/ci.yml').read_text()
        step = workflow.split('      - name: Require every CI partition\n', 1)[1]
        script = textwrap.dedent(step.split('        run: |\n', 1)[1])
        success = dict(CORE_RESULT='success', BROWSER_RESULT='success', ANALYSIS_RESULT='success')
        cases = [('all successful', success, 0)]
        for name in success:
            for status in ['failure', 'cancelled', 'skipped']:
                cases.append((name + ' ' + status, {**success, name: status}, 1))
        for name, results, expected in cases:
            with self.subTest(case=name):
                result = subprocess.run(['bash', '-c', script], env={**os.environ, **results},
                                        text=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
                self.assertEqual(result.returncode, expected, result.stdout)

    def test_workflow_job_names_are_unique_and_required_gate_is_stable(self):
        seen = {}
        for path in sorted((ROOT / '.github/workflows').glob('*.yml')):
            source = path.read_text().split('\njobs:\n', 1)[1]
            jobs = re.finditer(r'^  ([\w-]+):\n(.*?)(?=^  [\w-]+:\n|\Z)', source,
                               re.MULTILINE | re.DOTALL)
            for job in jobs:
                explicit = re.search(r'^    name: (.+)$', job.group(2), re.MULTILINE)
                name = explicit.group(1) if explicit else job.group(1)
                self.assertNotIn(name, seen, f'{name}: {path.name} collides with {seen.get(name)}')
                seen[name] = path.name
        self.assertEqual(seen['validate'], 'ci.yml')

    def test_failed_command_keeps_log_in_configured_artifact_directory(self):
        with tempfile.TemporaryDirectory(prefix='check-artifact-') as directory:
            root = Path(directory)
            with contextlib.redirect_stdout(io.StringIO()):
                result = check.run_checks([
                    ('broken', [sys.executable, '-c', 'print("failure evidence"); raise SystemExit(9)']),
                ], root=root, env={**os.environ, 'CHECK_LOG_DIR': '.build/check-logs'})
            self.assertEqual(result, 1)
            self.assertIn('failure evidence', (root / '.build/check-logs/broken.log').read_text())


if __name__ == '__main__':
    unittest.main()
