import importlib.util
import tempfile
import unittest
from pathlib import Path

ROOT = Path(__file__).resolve().parents[4]
spec = importlib.util.spec_from_file_location('validator', ROOT / 'scripts/validate-skills.py')
validator = importlib.util.module_from_spec(spec)
spec.loader.exec_module(validator)

CONTEXT = """## Internal delegation map

| Primary | Internal specialists |
| --- | --- |
| `owner` | `alpha`, `beta` |

## Next
"""


def skill(name, body):
    return f'---\nname: {name}\ndescription: fixture\n---\n\n# {name}\n\n{body}\n'


class SpecialistHandoffTests(unittest.TestCase):
    def errors_for(self, alpha_body):
        with tempfile.TemporaryDirectory() as tmp:
            root = Path(tmp)
            skills = root / 'agents/skills'
            for name, body in {'owner': 'Primary.', 'alpha': alpha_body, 'beta': 'Second specialist.'}.items():
                (skills / name).mkdir(parents=True)
                (skills / name / 'SKILL.md').write_text(skill(name, body))
            (skills / 'beta/references').mkdir()
            (skills / 'beta/references/criteria.md').write_text('criteria\n')
            (skills / 'CONTEXT.md').write_text(CONTEXT)
            errors, _ = validator.specialist_errors(['owner'], root=root)
            return errors

    def test_clean_specialist_has_no_errors(self):
        self.assertEqual(self.errors_for('Works alone.'), [])

    def test_handoff_to_other_specialist_fails(self):
        for phrase in ('1. Copy -> `beta`', '2. Copy → `beta` (details)', 'This goes to `beta`.',
                       'Then hand off to\n`beta`.'):
            with self.subTest(phrase=phrase):
                errors = self.errors_for(phrase)
                self.assertEqual(len(errors), 1)
                self.assertIn('alpha: specialists must not hand off to specialist beta;', errors[0])

    def test_path_reference_is_allowed(self):
        # Built at runtime: a literal repo path to the fixture skill would trip the
        # missing-skill-path scan over this test file.
        skills_prefix = 'agents/' + 'skills/'
        body = ('1. Criteria → `beta/references/criteria.md`\n'
                f'2. Criteria → `{skills_prefix}beta/SKILL.md`, section X')
        self.assertEqual(self.errors_for(body), [])

    def test_handoff_to_primary_or_self_or_unknown_is_allowed(self):
        body = 'Back -> `owner`. Loop -> `alpha`. Props -> `transform`.'
        self.assertEqual(self.errors_for(body), [])


class RepositoryTests(unittest.TestCase):
    def test_current_tree_has_no_specialist_handoffs(self):
        primary = [s for s in (ROOT / 'agents/skills/PRIMARY_SKILLS.txt').read_text().splitlines()
                   if s and not s.startswith('#')]
        errors, _ = validator.specialist_errors(primary)
        self.assertEqual([e for e in errors if 'must not hand off' in e], [])


if __name__ == '__main__':
    unittest.main()
