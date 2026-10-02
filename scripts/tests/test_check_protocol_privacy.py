"""The White-Label measurement boundary must not absorb the existing REST form."""
from pathlib import Path
import shutil
import subprocess
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[2]


class ProtocolPrivacy(unittest.TestCase):
    def guard(self, measurement, before='', after=''):
        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            subprocess.run(['git', 'init', '-q', str(root)], check=True, capture_output=True)
            scripts = root / 'scripts'
            scripts.mkdir()
            shutil.copyfile(ROOT / 'scripts/canon-guard.sh', scripts / 'canon-guard.sh')
            (scripts / 'canon-forbidden-values.txt').write_text(
                'rule\tstrecke-js-privat\tfetch\\(|localStorage|document\\.cookie\t'
                '^blocksy-child/assets/js/startseite-strecke\\.js:\tLocal protocol only\n')
            asset = root / 'blocksy-child/assets/js/whitelabel.js'
            asset.parent.mkdir(parents=True)
            asset.write_text(before + '\n// strecke-js-privat:start\n' + measurement
                             + '\n// strecke-js-privat:end\n' + after)
            return subprocess.run(['bash', str(scripts / 'canon-guard.sh')], cwd=root,
                                  capture_output=True, text=True)

    def test_cookie_read_and_existing_form_transport_are_allowed(self):
        self.assertEqual(0, self.guard('const count = document.cookie.length;',
                                     after='fetch(endpoint);').returncode)

    def test_measurement_transport_and_storage_are_rejected(self):
        for code in ['fetch(endpoint);', 'navigator.sendBeacon(endpoint);',
                     'new XMLHttpRequest();', 'localStorage.setItem("x", "y");',
                     'sessionStorage.getItem("x");', 'indexedDB.open("x");',
                     'document.cookie = "x=y";', 'window.dataLayer.push({value: 1});']:
            with self.subTest(code=code):
                result = self.guard(code)
                self.assertEqual(1, result.returncode, result.stdout + result.stderr)
                self.assertIn('strecke-js-privat', result.stdout)

    def test_duplicate_boundary_is_rejected(self):
        self.assertEqual(2, self.guard('// strecke-js-privat:start').returncode)


if __name__ == '__main__':
    unittest.main()
