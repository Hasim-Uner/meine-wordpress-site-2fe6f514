#!/usr/bin/env python3
"""Validate routing cases, export blind prompts, or score observed host runs."""
import argparse
import json
import math
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[4]
CASES = Path(__file__).resolve().parents[1] / 'evals/routing.json'


def require(condition, message):
    if not condition:
        raise ValueError(message)


def load_cases():
    cases = json.loads(CASES.read_text())
    primary = set((ROOT / 'agents/skills/PRIMARY_SKILLS.txt').read_text().split())
    seen, covered = set(), set()
    for case in cases:
        require(set(case) == {'id', 'prompt', 'primary_skill', 'context', 'rubric'},
                'case has missing/unknown fields')
        require(case['id'] and case['id'] not in seen, 'missing/duplicate case id')
        seen.add(case['id'])
        require(isinstance(case['prompt'], str) and case['prompt'].strip(), 'empty prompt')
        skill = case['primary_skill']
        require(skill is None or skill in primary, f'unknown primary: {skill}')
        if skill:
            covered.add(skill)
            require(case['context'] in {
                'blocksy-child/CONTEXT.md', 'blocksy-child/inc/CONTEXT.md',
                'blocksy-child/template-parts/CONTEXT.md', 'docs/CONTEXT.md',
                'content/CONTEXT.md', 'agents/skills/CONTEXT.md'}, 'invalid local context')
            require((ROOT / case['context']).is_file(), 'missing context file')
        else:
            require(case['context'] is None, 'negative case must have no repo context')
        require(isinstance(case['rubric'], list) and case['rubric'] and
                all(isinstance(r, str) and r.strip() for r in case['rubric']), 'empty rubric')
    require(covered == primary, f'uncovered primary skills: {sorted(primary - covered)}')
    require(any(c['primary_skill'] is None for c in cases), 'missing negative case')
    return cases


def score(cases, run):
    require(isinstance(run, dict), 'run must be an object')
    for key in ('host', 'model', 'revision'):
        require(isinstance(run.get(key), str) and run[key].strip(), f'missing {key}')
    observations = run.get('observations')
    require(isinstance(observations, list), 'observations must be a list')
    expected = {c['id']: c for c in cases}
    seen, failures, metrics = set(), [], {}
    for row in observations:
        require(isinstance(row, dict), 'observation must be an object')
        identifier = row.get('id')
        require(identifier in expected and identifier not in seen, f'unknown/duplicate case: {identifier}')
        seen.add(identifier)
        require({'primary_skill', 'context', 'evidence'} <= row.keys(), f'{identifier}: missing observation fields')
        require(isinstance(row['evidence'], str) and row['evidence'].strip(), f'{identifier}: missing trace reference')
        target = expected[identifier]
        if (row['primary_skill'], row['context']) != (target['primary_skill'], target['context']):
            failures.append(identifier)
        for field in ('input_tokens', 'cached_input_tokens', 'output_tokens', 'elapsed_seconds', 'rework_count'):
            if field not in row or row[field] is None:
                continue
            value = row[field]
            require(type(value) in (int, float) and math.isfinite(value) and value >= 0,
                    f'{identifier}: invalid {field}')
            if field != 'elapsed_seconds':
                require(type(value) is int, f'{identifier}: {field} must be an integer')
            metrics.setdefault(field, []).append(value)
        if row.get('cached_input_tokens') is not None:
            require(row.get('input_tokens') is not None and row['cached_input_tokens'] <= row['input_tokens'],
                    f'{identifier}: cached tokens must be a subset of input tokens')
    require(seen == set(expected), f'incomplete run; missing: {sorted(set(expected) - seen)}')
    return {
        'host': run['host'], 'model': run['model'], 'revision': run['revision'],
        'cases': len(cases), 'routing_passes': len(cases) - len(failures),
        'routing_failures': failures,
        'measured': {key: {'cases': len(values), 'total': sum(values)} for key, values in metrics.items()},
        'scope': 'Observed routing only. Inspect each rubric and trace for task quality; no causal cost claim.',
    }


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    mode = parser.add_mutually_exclusive_group(required=True)
    mode.add_argument('--check', action='store_true')
    mode.add_argument('--prompts', action='store_true')
    mode.add_argument('--score', type=Path, metavar='RUN_JSON')
    args = parser.parse_args()
    try:
        cases = load_cases()
        if args.check:
            print(f'PASS {len(cases)} routing fixtures; no model has been evaluated.')
        elif args.prompts:
            print(json.dumps([{'id': c['id'], 'prompt': c['prompt']} for c in cases], ensure_ascii=False, indent=2))
        else:
            result = score(cases, json.loads(args.score.read_text()))
            print(json.dumps(result, ensure_ascii=False, indent=2))
            return bool(result['routing_failures'])
    except (ValueError, OSError, TypeError, KeyError) as error:
        print(f'FAIL {error}', file=sys.stderr)
        return 2
    return 0


if __name__ == '__main__':
    sys.exit(main())
