"""Synthetic scorer fixtures; these are never evidence of actual agent performance."""
import importlib.util
import json
from pathlib import Path
import unittest

spec = importlib.util.spec_from_file_location('tasks', Path(__file__).resolve().parents[1] / 'scripts/task-benchmark.py')
tasks = importlib.util.module_from_spec(spec)
spec.loader.exec_module(tasks)


def stream(*events):
    return '\n'.join(json.dumps(event) for event in events)


class TaskBenchmarkTests(unittest.TestCase):
    def test_five_distinct_bounded_tasks(self):
        rows = tasks.cases()
        self.assertEqual(len(rows), 5)
        self.assertEqual(len({row['path'] for row in rows}), 5)
        for row in rows:
            self.assertEqual((tasks.ROOT / row['path']).read_text().count(row['before']), 1)

    def test_codex_observed_usage_preserves_cache_subset(self):
        result = tasks.trace_usage('codex', stream(
            {'type': 'thread.started', 'thread_id': 'synthetic'},
            {'type': 'turn.completed', 'usage': {'input_tokens': 80, 'cached_input_tokens': 50, 'output_tokens': 10}}))
        self.assertTrue(result['terminal_success'])
        self.assertEqual(result['measured'], {'input_tokens': 80, 'cached_input_tokens': 50, 'output_tokens': 10})

    def test_claude_input_total_includes_observed_cache_components(self):
        result = tasks.trace_usage('claude', stream({'type': 'result', 'subtype': 'success', 'is_error': False,
            'usage': {'input_tokens': 20, 'cache_read_input_tokens': 50, 'cache_creation_input_tokens': 10, 'output_tokens': 8}}))
        self.assertEqual(result['measured']['input_tokens'], 80)
        self.assertEqual(result['measured']['cached_input_tokens'], 50)
        self.assertEqual(result['measured']['output_tokens'], 8)

    def test_missing_usage_does_not_become_zero(self):
        result = tasks.trace_usage('codex', stream({'type': 'thread.started'}, {'type': 'turn.completed'}))
        self.assertEqual(result['measured'], {})
        partial = tasks.trace_usage('claude', stream({'type': 'result', 'subtype': 'success', 'usage': {'input_tokens': 20}}))
        self.assertNotIn('input_tokens', partial['measured'])
        self.assertEqual(partial['measured']['uncached_input_tokens'], 20)

    def test_incomplete_and_failed_sessions_cannot_pass(self):
        for host, events in [('codex', [{'type': 'thread.started'}]),
                             ('codex', [{'type': 'thread.started'}, {'type': 'turn.failed'}]),
                             ('claude', []), ('claude', [{'type': 'result', 'subtype': 'error_max_turns', 'is_error': True}])]:
            with self.subTest(host=host, events=events):
                self.assertFalse(tasks.trace_usage(host, stream(*events))['terminal_success'])

    def test_mixed_sessions_and_invalid_metrics_are_rejected(self):
        for events in [
            [{'type': 'thread.started'}, {'type': 'thread.started'}],
            [{'type': 'thread.started'}, {'type': 'turn.completed', 'usage': {'input_tokens': True}}],
            [{'type': 'thread.started'}, {'type': 'turn.completed', 'usage': {'input_tokens': 10, 'cached_input_tokens': 11}}],
        ]:
            with self.assertRaises(ValueError):
                tasks.trace_usage('codex', stream(*events))
        with self.assertRaises(ValueError):
            tasks.trace_usage('claude', stream({'type': 'result'}, {'type': 'result'}))

    def test_preparation_refuses_main_checkout(self):
        with self.assertRaisesRegex(ValueError, 'outside'):
            tasks.prepare(tasks.cases()[0], tasks.ROOT / 'nested-fixture', 'HEAD')


if __name__ == '__main__':
    unittest.main()
