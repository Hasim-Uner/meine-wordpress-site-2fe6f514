import copy
import importlib.util
import json
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path

ROOT = Path(__file__).resolve().parents[4]


def module_at(name, path):
    spec = importlib.util.spec_from_file_location(name, path)
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    return module


benchmark = module_at('benchmark', Path(__file__).resolve().parents[1] / 'scripts/benchmark.py')
validator = module_at('validator', ROOT / 'scripts/validate-skills.py')


class BenchmarkTests(unittest.TestCase):
    def setUp(self):
        # Synthetic observations test the scorer, never serve as agent evidence.
        self.cases = benchmark.load_cases()
        self.run = {'host': 'synthetic', 'model': 'test', 'revision': 'fixture',
                    'observations': [dict(id=c['id'], primary_skill=c['primary_skill'],
                                          context=c['context'], evidence='synthetic unit fixture')
                                     for c in self.cases]}

    def test_complete_synthetic_run_has_no_invented_usage(self):
        result = benchmark.score(self.cases, self.run)
        self.assertEqual(result['routing_passes'], len(self.cases))
        self.assertEqual(result['measured'], {})

    def test_wrong_primary_and_context_are_failures(self):
        self.run['observations'][0]['primary_skill'] = 'wordpress-growth-architecture'
        self.run['observations'][1]['context'] = 'docs/CONTEXT.md'
        self.assertEqual(benchmark.score(self.cases, self.run)['routing_failures'], ['spacing', 'motion'])

    def test_missing_duplicate_and_unknown_cases_cannot_inflate_score(self):
        for mode in ('missing', 'duplicate', 'unknown'):
            with self.subTest(mode=mode):
                run = copy.deepcopy(self.run)
                if mode == 'missing':
                    run['observations'].pop()
                elif mode == 'duplicate':
                    run['observations'].append(run['observations'][0])
                else:
                    run['observations'][0]['id'] = 'made-up'
                with self.assertRaises(ValueError):
                    benchmark.score(self.cases, run)

    def test_negative_case_rejects_unneeded_repo_workflow(self):
        self.run['observations'][-1]['primary_skill'] = 'performance-marketing'
        self.assertIn('no-repo', benchmark.score(self.cases, self.run)['routing_failures'])

    def test_partial_usage_is_reported_with_coverage(self):
        self.run['observations'][0].update(input_tokens=80, cached_input_tokens=50, output_tokens=10)
        result = benchmark.score(self.cases, self.run)
        self.assertEqual(result['measured']['input_tokens'], {'cases': 1, 'total': 80})
        self.assertNotIn('elapsed_seconds', result['measured'])

    def test_invalid_metrics_rejected(self):
        for value in (-1, True, '100', float('nan'), float('inf'), 1.5):
            with self.subTest(value=value):
                self.run['observations'][0]['input_tokens'] = value
                with self.assertRaises(ValueError):
                    benchmark.score(self.cases, self.run)

    def test_cached_tokens_require_compatible_input_count(self):
        row = self.run['observations'][0]
        row['cached_input_tokens'] = 10
        with self.assertRaises(ValueError):
            benchmark.score(self.cases, self.run)
        row['input_tokens'] = 9
        with self.assertRaises(ValueError):
            benchmark.score(self.cases, self.run)

    def test_missing_provenance_rejected(self):
        self.run['observations'][0]['evidence'] = ''
        with self.assertRaises(ValueError):
            benchmark.score(self.cases, self.run)

    def test_invalid_run_shape_rejected(self):
        for run in (None, [], 'not a run'):
            with self.subTest(run=run), self.assertRaises(ValueError):
                benchmark.score(self.cases, run)

    def test_prompt_export_contains_no_answers(self):
        result = subprocess.run([sys.executable, str(benchmark.__file__), '--prompts'],
                                capture_output=True, text=True, check=True)
        prompts = json.loads(result.stdout)
        self.assertEqual(len(prompts), len(self.cases))
        self.assertTrue(all(set(p) == {'id', 'prompt'} for p in prompts))

    def test_cli_wrong_routing_returns_failure(self):
        self.run['observations'][0]['primary_skill'] = None
        with tempfile.TemporaryDirectory() as directory:
            path = Path(directory) / 'run.json'
            path.write_text(json.dumps(self.run))
            result = subprocess.run([sys.executable, str(benchmark.__file__), '--score', str(path)],
                                    capture_output=True, text=True)
        self.assertEqual(result.returncode, 1)
        self.assertIn('spacing', json.loads(result.stdout)['routing_failures'])


class ContextTests(unittest.TestCase):
    def test_budget_and_name_mismatch_are_detected(self):
        with tempfile.TemporaryDirectory() as directory:
            path = Path(directory) / 'SKILL.md'
            path.write_text('---\nname: wrong\ndescription: Small.\n---\n' + 'instruction\n' * 80)
            errors, _, _ = validator.inspect_skill(path)
        self.assertTrue(any('name' in e for e in errors))
        self.assertTrue(any('exceeds' in e for e in errors))

    def test_links_resolve_relative_to_reference_and_stay_in_repo(self):
        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            refs = root / 'references'
            refs.mkdir()
            (refs / 'valid.md').write_text('Content')
            path = refs / 'guide.md'
            path.write_text('[valid](valid.md#section) [missing](missing.md) [escape](../../outside.md)')
            errors = validator.broken_links(path, root)
        self.assertEqual(len(errors), 2)
        self.assertTrue(any('broken reference' in e for e in errors))
        self.assertTrue(any('outside repository' in e for e in errors))


if __name__ == '__main__':
    unittest.main()
