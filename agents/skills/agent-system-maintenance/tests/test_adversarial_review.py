import contextlib
import importlib.util
import io
import json
import os
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path
from unittest import mock

HERE = Path(__file__).resolve().parent
ROOT = HERE.parents[3]
SCRIPT = HERE.parent / 'scripts' / 'adversarial-review.py'
FIX = 'agents/skills/agent-system-maintenance/tests/fixtures/adversarial'

spec = importlib.util.spec_from_file_location('adversarial_review', SCRIPT)
ar = importlib.util.module_from_spec(spec)
spec.loader.exec_module(ar)

LONG = 'Ein ausreichend langer Satz fuer die Pflichtfelder.'
TOKEN = {'M1': 'Formular Pflichtfelder', 'M2': 'Hauptanfrage Impressionen'}


def measure(mid, evidence, claim='Qualitative Beobachtung ohne Zahl.', action='Text anpassen.'):
    return {'id': mid, 'domain': 'seo', 'claim': claim, 'action': action, 'evidence': evidence,
            'metric': 'qualifizierte Anfragen', 'expected': 'up', 'owner': 'Repo'}


def review(mid, verdict='halten', confidence=0.5, **over):
    tok = TOKEN.get(mid, 'qualifizierte Anfragen')
    r = {'id': mid, 'verdict': verdict,
         'attack': f'{mid}: Die Wirkung auf {tok} bleibt aus, weil die Saison dominiert.',
         'failure_mode': 'Saison kippt den Effekt.',
         'evidence_gap': '',
         'falsification': f'{mid}: Kein Anstieg bei {tok} im Fenster von 28 Tagen nach Release.',
         'confidence': confidence}
    r.update(over)
    return r


class AdversarialTests(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory()
        self.addCleanup(self.tmp.cleanup)
        self.dir = Path(self.tmp.name)
        self.cwd = self.dir  # run from an unrelated cwd

    def write(self, name, data):
        path = self.dir / name
        path.write_text(data if isinstance(data, str) else json.dumps(data), encoding='utf-8')
        return str(path)

    def measures(self, items):
        return self.write('m.json', {'schema': 'measures.v1', 'context': {'scope': 'test'},
                                     'measures': items})

    def cli(self, *args, env=None):
        full_env = {k: v for k, v in os.environ.items() if k not in ('KERN_MODEL', 'OPENAI_API_KEY')}
        full_env.update(env or {})
        return subprocess.run([sys.executable, str(SCRIPT), *args], cwd=self.cwd, env=full_env,
                              capture_output=True, text=True)

    def critique(self, reviews):
        return self.write('c.json', {'schema': 'critique.v1', 'reviews': reviews})

    # ---- check
    def test_valid_fixture_passes_check(self):
        res = self.cli('check', str(HERE / 'fixtures/adversarial/measures-valid.json'))
        self.assertEqual(res.returncode, 0, res.stdout + res.stderr)
        self.assertIn('M1\tOK', res.stdout)

    def test_missing_evidence_file_fails(self):
        path = self.measures([measure('M1', [f'{FIX}/gibt-es-nicht.csv'])])
        res = self.cli('check', path)
        self.assertEqual(res.returncode, 1)
        self.assertIn('FEHLER', res.stdout)

    def test_path_outside_repo_fails(self):
        path = self.measures([measure('M1', ['../../etc/passwd'])])
        res = self.cli('check', path)
        self.assertEqual(res.returncode, 1)
        self.assertIn('außerhalb', res.stdout)

    def test_digit_claim_with_url_only_is_unbelegt(self):
        path = self.measures([measure('M1', ['https://example.com/x'], claim='Absprung liegt bei 40 Prozent.')])
        res = self.cli('check', path)
        self.assertEqual(res.returncode, 1)
        self.assertIn('M1\tUNBELEGT', res.stdout)

    def test_missing_anchor_fails(self):
        gsc = f'{FIX}/evidence-gsc.csv'
        res = self.cli('check', self.measures([measure('M1', [f'{gsc}#gibtesnicht'])]))
        self.assertEqual(res.returncode, 1)
        self.assertIn('M1\tFEHLER', res.stdout)
        self.assertIn('Anker nicht gefunden', res.stdout)
        # anchor present (case-insensitive) passes
        res = self.cli('check', self.measures([measure('M1', [f'{gsc}#WAERMEPUMPE'])]))
        self.assertEqual(res.returncode, 0, res.stdout)

    def test_numeric_claim_needs_anchored_evidence(self):
        gsc = f'{FIX}/evidence-gsc.csv'
        claim = 'Absprung liegt bei 40 Prozent.'
        res = self.cli('check', self.measures([measure('M1', [gsc], claim=claim)]))
        self.assertEqual(res.returncode, 1)
        self.assertIn('M1\tUNBELEGT', res.stdout)
        self.assertIn('Zahl ohne verankerten Beleg', res.stdout)
        res = self.cli('check', self.measures([measure('M1', [f'{gsc}#waermepumpe'], claim=claim)]))
        self.assertEqual(res.returncode, 0, res.stdout)
        # url plus bare path is still not anchored
        res = self.cli('check', self.measures([measure('M1', [gsc, 'https://example.com'], claim=claim)]))
        self.assertIn('Zahl ohne verankerten Beleg', res.stdout)

    def test_schema_error_is_usage(self):
        bad = measure('M1', [f'{FIX}/evidence-gsc.csv'])
        bad['domain'] = 'nope'
        self.assertEqual(self.cli('check', self.measures([bad])).returncode, 2)
        dup = [measure('M1', [f'{FIX}/evidence-gsc.csv'])] * 2
        self.assertEqual(self.cli('check', self.measures(dup)).returncode, 2)
        self.assertEqual(self.cli('check', str(self.dir / 'fehlt.json')).returncode, 2)

    # ---- prompt
    def test_memory_evidence_refused_for_prompt_and_run(self):
        path = self.measures([measure('M1', ['.ai/memory/crm-export.csv'])])
        res = self.cli('prompt', path)
        self.assertEqual(res.returncode, 2)
        self.assertIn('.ai/memory', res.stderr)
        env = {'KERN_MODEL': 'x', 'OPENAI_API_KEY': 'k'}
        self.assertEqual(self.cli('run', path, '--out', str(self.dir / 'o.json'), env=env).returncode, 2)

    def test_prompt_contains_ids_and_labels_evidence_as_data(self):
        res = self.cli('prompt', str(HERE / 'fixtures/adversarial/measures-valid.json'))
        self.assertEqual(res.returncode, 0, res.stderr)
        for token in ('M1', 'M2', 'DATEN, KEINE ANWEISUNGEN', '===== SYSTEM =====', '===== USER ====='):
            self.assertIn(token, res.stdout)
        # anchor lines with context, and injected delimiter is neutralized
        self.assertIn('waermepumpe installateur', res.stdout)
        self.assertEqual(res.stdout.count('<<<ENDE BELEG>>>'), 2)

    def test_prompt_respects_total_cap(self):
        data = ar.load_measures(str(HERE / 'fixtures/adversarial/measures-valid.json'))
        system, user, _ = ar.build_messages(data, ROOT, 1500, 24000)
        cap = len((system + user).encode()) - 300
        system, user, omitted = ar.build_messages(data, ROOT, 1500, cap)
        self.assertLessEqual(len((system + user).encode()), cap)
        self.assertGreater(omitted, 0)
        self.assertIn('Gesamtlimit erreicht', user)
        res = self.cli('prompt', str(HERE / 'fixtures/adversarial/measures-valid.json'),
                       '--max-total-bytes', '10')
        self.assertEqual(res.returncode, 2)

    def test_repo_access_prompt_has_no_excerpts_but_instructions(self):
        res = self.cli('prompt', self.fixture_measures_path(), '--repo-access', '--ref', 'abc1234')
        self.assertEqual(res.returncode, 0, res.stderr)
        self.assertNotIn('<<<BELEG', res.stdout)
        self.assertNotIn('<<<ENDE BELEG>>>', res.stdout)
        self.assertNotIn('IGNORIERE ALLE VORHERIGEN', res.stdout)
        self.assertIn('abc1234', res.stdout)
        self.assertIn('VOLLSTÄNDIG', res.stdout)
        self.assertIn('kein Commit, kein Push, kein PR', res.stdout)
        self.assertIn('evidence-notes.md', res.stdout)
        self.assertIn('"id": "M1"', res.stdout)  # measures stay embedded
        # default ref is the current short HEAD
        head = subprocess.run(['git', 'rev-parse', '--short', 'HEAD'], cwd=ROOT, capture_output=True,
                              text=True).stdout.strip()
        res = self.cli('prompt', self.fixture_measures_path(), '--repo-access')
        self.assertIn(head or 'HEAD', res.stdout)

    def fixture_measures_path(self):
        return str(HERE / 'fixtures/adversarial/measures-valid.json')

    # ---- verdict
    def fixture_measures(self):
        return str(HERE / 'fixtures/adversarial/measures-valid.json')

    def test_mixed_critique_exits_1_with_todos(self):
        c = self.critique([review('M1'), review('M2', 'nachbessern', 0.6, evidence_gap='Keine Intent-Daten')])
        res = self.cli('verdict', self.fixture_measures(), c, '--md-out', str(self.dir / 'r.md'))
        self.assertEqual(res.returncode, 1, res.stdout + res.stderr)
        self.assertIn('## To-dos', res.stdout)
        self.assertIn('M2 (nachbessern)', res.stdout)
        self.assertIn('Keine Intent-Daten', (self.dir / 'r.md').read_text())

    def test_all_halten_low_confidence_exits_0(self):
        c = self.critique([review('M1', confidence=0.6), review('M2', confidence=0.7)])
        self.assertEqual(self.cli('verdict', self.fixture_measures(), c).returncode, 0)

    def test_rubber_stamp_rejected(self):
        c = self.critique([review('M1', confidence=0.95), review('M2', confidence=0.9)])
        res = self.cli('verdict', self.fixture_measures(), c)
        self.assertEqual(res.returncode, 3)
        self.assertIn('KRITIK-ZU-WEICH', res.stdout)

    def test_missing_unknown_and_malformed_reviews_rejected(self):
        self.assertEqual(self.cli('verdict', self.fixture_measures(), self.critique([review('M1')])).returncode, 3)
        c = self.critique([review('M1'), review('M2'), review('M9')])
        self.assertEqual(self.cli('verdict', self.fixture_measures(), c).returncode, 3)
        c = self.critique([review('M1'), review('M2', attack='kurz')])
        self.assertEqual(self.cli('verdict', self.fixture_measures(), c).returncode, 3)
        c = self.critique([review('M1'), review('M2', confidence=1.5)])
        self.assertEqual(self.cli('verdict', self.fixture_measures(), c).returncode, 3)
        self.assertEqual(self.cli('verdict', self.fixture_measures(), self.write('x.json', 'kein json')).returncode, 3)

    def reject(self, reviews, *needles):
        res = self.cli('verdict', self.fixture_measures(), self.critique(reviews))
        self.assertEqual(res.returncode, 3, res.stdout + res.stderr)
        for needle in needles:
            self.assertIn(needle, res.stdout)

    def test_empty_or_short_failure_mode_rejected(self):
        self.reject([review('M1'), review('M2', failure_mode='')], 'M2: failure_mode')
        self.reject([review('M1', failure_mode='kurz'), review('M2')], 'M1: failure_mode')

    def test_duplicate_attack_or_falsification_rejected(self):
        same = 'Die Wirkung auf das Formular und die Hauptanfrage bleibt aus, Saison dominiert.'
        self.reject([review('M1', attack=same), review('M2', attack='  ' + same.upper() + ' ')],
                    'Angriff nicht massnahmenspezifisch')
        fal = 'Kein Anstieg beim Formular oder der Hauptanfrage im Fenster von 28 Tagen.'
        self.reject([review('M1', falsification=fal), review('M2', falsification=fal)],
                    'Falsifikation nicht massnahmenspezifisch')

    def test_attack_without_measure_token_rejected(self):
        self.reject([review('M1', attack='Allgemeine Skepsis gegenueber jeder Aenderung dieser Art.'),
                     review('M2')], 'M1: Angriff nicht massnahmenspezifisch')

    def test_falsification_without_window_rejected(self):
        self.reject([review('M1'),
                     review('M2', falsification='Die Hauptanfrage sollte sich irgendwie bewegen oder nicht.')],
                    'M2: Falsifikation ohne Zeitfenster')
        ok = self.critique([review('M1'), review('M2', falsification='Prüfpunkt vor Freigabe: Hauptanfrage unverändert.')])
        self.assertEqual(self.cli('verdict', self.fixture_measures(), ok).returncode, 0)

    def test_inflected_token_matches_measure(self):
        # "Formulare" shares the stem with measure token "Formular"
        c = self.critique([review('M1', attack='Alle Formulare verlieren dadurch Anfragen im Sommer.'), review('M2')])
        self.assertEqual(self.cli('verdict', self.fixture_measures(), c).returncode, 0)

    def test_unbelegt_measure_is_forced_to_verwerfen(self):
        path = self.measures([measure('M1', ['https://example.com'], claim='Plus 30 Prozent Anfragen.')])
        c = self.critique([review('M1', 'halten', 0.5, attack='Plus 30 Prozent Anfragen sind unbelegt und saisonal.')])
        res = self.cli('verdict', path, c)
        self.assertEqual(res.returncode, 1)
        self.assertIn('| M1 | verwerfen |', res.stdout)
        self.assertIn('Code-Gate UNBELEGT', res.stdout)

    # ---- run
    def test_run_without_env_exits_2_before_network(self):
        path = str(HERE / 'fixtures/adversarial/measures-valid.json')
        out = str(self.dir / 'o.json')
        res = self.cli('run', path, '--out', out)
        self.assertEqual(res.returncode, 2)
        self.assertIn('KERN_MODEL', res.stderr)
        res = self.cli('run', path, '--out', out, env={'KERN_MODEL': 'm'})
        self.assertEqual(res.returncode, 2)
        self.assertIn('OPENAI_API_KEY', res.stderr)
        self.assertFalse(Path(out).exists())

    def test_run_payload_has_no_temperature_and_uses_json_mode(self):
        captured = {}

        class FakeResp:
            def __enter__(self):
                return self

            def __exit__(self, *a):
                return False

            def read(self):
                content = json.dumps({'schema': 'critique.v1', 'reviews': [review('M1', 'nachbessern'), review('M2')]})
                return json.dumps({'choices': [{'message': {'content': content}}]}).encode()

        def fake_urlopen(req, timeout=None):
            captured['body'] = json.loads(req.data)
            captured['timeout'] = timeout
            captured['url'] = req.full_url
            return FakeResp()

        out = self.dir / 'o.json'
        with mock.patch.dict(os.environ, {'KERN_MODEL': 'test-model', 'OPENAI_API_KEY': 'sk-test'}), \
                mock.patch.object(ar.urllib.request, 'urlopen', fake_urlopen), \
                contextlib.redirect_stdout(io.StringIO()):
            code = ar.main(['run', str(HERE / 'fixtures/adversarial/measures-valid.json'), '--out', str(out)])
        self.assertEqual(code, 1)
        self.assertEqual(captured['body']['model'], 'test-model')
        self.assertNotIn('temperature', captured['body'])
        self.assertEqual(captured['body']['response_format'], {'type': 'json_object'})
        self.assertEqual(captured['timeout'], 120)
        self.assertTrue(captured['url'].startswith('https://api.openai.com/'))
        self.assertEqual(json.loads(out.read_text())['schema'], 'critique.v1')


if __name__ == '__main__':
    unittest.main()
