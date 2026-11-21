from __future__ import annotations

import os
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path


SKILL = Path(__file__).resolve().parents[1]
SCRIPT = SKILL / "scripts" / "retainer-digest.py"
FIXTURES = Path(__file__).resolve().parent / "fixtures"
HEADER = "metric;window_start;window_end;value;denominator;source\n"
ENV = {**os.environ, "TZ": "UTC", "GIT_CONFIG_GLOBAL": os.devnull, "GIT_CONFIG_SYSTEM": os.devnull}


class RetainerDigestTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls) -> None:
        cls.tempdir = tempfile.TemporaryDirectory()
        cls.root = Path(cls.tempdir.name)
        cls.repo = cls.root / "repo"
        cls.repo.mkdir()
        cls.git("init", "-q")
        cls.git("config", "user.name", "Digest Fixture")
        cls.git("config", "user.email", "test@example.invalid")
        cls.commit("2026-10-02", "feat(hero): Hero kuerzen", ["blocksy-child/style.css", "blocksy-child/front.php"])
        cls.commit("2026-10-05", "fix: Tippfehler", ["docs/a.md"])
        cls.commit("2026-10-10", "Entwurf Ratgeber", ["content/draft.md"])
        cls.commit("2026-10-12", "chore: Skript", ["scripts/x.sh", "agents/y.md"])
        cls.commit("2026-10-15", "Revert \"Hero kuerzen\"", ["blocksy-child/style.css"])
        cls.commit("2026-10-20", "Sonstiges anpassen", ["README.md"])
        cls.commit("2026-10-21", "feat: gemischt", ["docs/b.md", "docs/c.md", "content/e.md"])
        cls.commit("2026-10-22", "feat: vier", ["blocksy-child/1.php"])
        cls.commit("2026-10-23", "fix: fuenf", ["blocksy-child/2.php"])

    @classmethod
    def tearDownClass(cls) -> None:
        cls.tempdir.cleanup()

    @classmethod
    def git(cls, *args: str, env: dict | None = None) -> None:
        subprocess.run(["git", "-C", str(cls.repo), *args], check=True, capture_output=True, env={**ENV, **(env or {})})

    @classmethod
    def commit(cls, day: str, subject: str, files: list[str]) -> None:
        for name in files:
            path = cls.repo / name
            path.parent.mkdir(parents=True, exist_ok=True)
            path.write_text(f"{day} {subject}\n", encoding="utf-8")
        stamp = f"{day}T12:00:00+0000"
        cls.git("add", "-A")
        cls.git("commit", "-q", "-m", subject, env={"GIT_AUTHOR_DATE": stamp, "GIT_COMMITTER_DATE": stamp})

    def run_digest(self, *args: str, since: str = "2026-10-01", until: str = "2026-10-31"):
        window = ["--since", since, "--until", until] if since else []
        return subprocess.run(
            [sys.executable, str(SCRIPT), "--repo", str(self.repo), *window, *args],
            capture_output=True, text=True, env=ENV,
        )

    def csv(self, name: str, rows: str, header: str = HEADER) -> str:
        path = self.root / name
        path.write_text(header + rows, encoding="utf-8")
        return str(path)

    def test_groups_and_strips_prefixes(self) -> None:
        result = self.run_digest()
        self.assertEqual(result.returncode, 0, result.stderr)
        out = result.stdout
        self.assertIn("Website (wirksam erst nach Deploy): 4 Commits", out)
        self.assertIn("Hero kuerzen", out)
        self.assertNotIn("feat(hero):", out)
        self.assertIn("Dokumentation: 2 Commits", out)  # b.md/c.md majority beats content/e.md
        self.assertIn("Inhalte (Entwurf): 1 Commit:", out)
        self.assertIn("Werkzeuge und QA: 1 Commit:", out)
        self.assertIn("Sonstiges: 1 Commit:", out)
        self.assertIn("+1 weitere", out)
        self.assertLessEqual(len(out.splitlines()), 25)

    def test_revert_risk_and_default_ergebnis(self) -> None:
        out = self.run_digest().stdout
        self.assertIn("1 Revert/Hotfix/Rollback", out)
        self.assertNotIn("Keine Reverts im Fenster.", out)
        self.assertIn("Ergebnis: in diesem Fenster nicht gemessen.", out)
        self.assertNotIn("Entscheidung nötig", out)
        self.assertNotIn("Nächster Schritt", out)

    def test_empty_window(self) -> None:
        out = self.run_digest(since="2025-01-01", until="2025-01-31").stdout
        self.assertIn("Keine Commits im Fenster.", out)
        self.assertIn("Keine Reverts im Fenster.", out)

    def test_window_is_inclusive_and_range_works(self) -> None:
        out = self.run_digest(since="2026-10-02", until="2026-10-02").stdout
        self.assertIn("Website (wirksam erst nach Deploy): 1 Commit:", out)
        result = self.run_digest("--range", "HEAD~2..HEAD", since="")
        self.assertEqual(result.returncode, 0, result.stderr)
        self.assertIn("2 Commits", result.stdout)

    def test_decisions_next_and_markdown(self) -> None:
        decisions = self.root / "decisions.txt"
        decisions.write_text("Budget freigeben?\n\nTermin bestaetigen\n", encoding="utf-8")
        out = self.run_digest("--decisions", str(decisions), "--next", "Deploy pruefen", "--md").stdout
        self.assertIn("**Entscheidung nötig:**", out)
        self.assertIn("- Budget freigeben?", out)
        self.assertIn("**Nächster Schritt:** Deploy pruefen", out)
        self.assertTrue(out.startswith("# Retainer-Update"))

    def test_tier_order_and_delta_with_equal_windows(self) -> None:
        tracking = self.csv("t.csv", "sessions;2026-10-01;2026-10-30;900;;tracking\n")
        result = self.run_digest("--outcomes", str(FIXTURES / "outcomes-two-windows.csv"), "--tracking", tracking)
        self.assertEqual(result.returncode, 0, result.stderr)
        out = result.stdout
        self.assertLess(out.index("won (lagging)"), out.index("qualified (lagging)"))
        self.assertLess(out.index("qualified (lagging)"), out.index("sessions (leading)"))
        self.assertIn("Delta +2", out)
        self.assertNotIn("Nur Frühindikatoren", out)
        self.assertNotIn("nicht gemessen", out)

    def test_unequal_windows_show_no_delta(self) -> None:
        outcomes = self.csv("u.csv", "qualified;2026-09-01;2026-09-30;4;;crm\nqualified;2026-10-01;2026-10-15;6;;crm\n")
        out = self.run_digest("--outcomes", outcomes).stdout
        self.assertIn("kein Vergleich (Fenster ungleich)", out)
        self.assertNotIn("Delta", out)

    def test_overlapping_windows_show_no_delta(self) -> None:
        outcomes = self.csv("o.csv", "qualified;2026-09-01;2026-09-30;4;;crm\nqualified;2026-09-15;2026-10-14;6;;crm\n")
        out = self.run_digest("--outcomes", outcomes).stdout
        self.assertIn("kein Vergleich", out)
        self.assertNotIn("Delta", out)

    def test_only_leading_metrics_warn_and_rate_delta(self) -> None:
        out = self.run_digest("--tracking", str(FIXTURES / "tracking-leading.csv")).stdout
        self.assertIn("Nur Frühindikatoren. Kein Pipeline-Beleg.", out)
        self.assertIn("ctr (leading) 2026-10-01..2026-10-30: 45 von 1000 (4.5%)", out)

    def assert_rejected(self, *args: str, expect: str) -> None:
        result = self.run_digest(*args)
        self.assertEqual(result.returncode, 2, result.stdout + result.stderr)
        self.assertIn(expect, result.stderr)

    def test_unknown_metric_exits_2(self) -> None:
        path = self.csv("m.csv", "revenue;2026-10-01;2026-10-30;5;;crm\n")
        self.assert_rejected("--outcomes", path, expect="unknown metric")
        self.assertIn("row 2", self.run_digest("--outcomes", path).stderr)

    def test_email_in_value_exits_2_without_echo(self) -> None:
        path = self.csv("e.csv", "won;2026-10-01;2026-10-30;max@example.com;;crm\n")
        self.assert_rejected("--outcomes", path, expect="row 2")
        self.assertNotIn("max@example.com", self.run_digest("--outcomes", path).stderr)

    def test_at_sign_in_source_and_phone_exit_2(self) -> None:
        self.assert_rejected("--outcomes", self.csv("s.csv", "won;2026-10-01;2026-10-30;1;;crm@x\n"), expect="source")
        self.assert_rejected("--outcomes", self.csv("p.csv", "won;2026-10-01;2026-10-30;+49 170 1234567;;crm\n"), expect="phone")

    def test_system_identifier_sources_are_allowed(self) -> None:
        for source in ("crm:bitrix24", "ga4", "gsc", "crm-export-2026-10-31", "crm:bitrix24-v2"):
            path = self.csv("d.csv", f"won;2026-10-01;2026-10-30;1;;{source}\n")
            self.assertEqual(self.run_digest("--outcomes", path).returncode, 0, source)

    def test_person_name_or_free_text_in_source_exits_2(self) -> None:
        for source in ("Max Mustermann", "crm export 2026-10-31", "CRM", "-crm"):
            path = self.csv("n.csv", f"won;2026-10-01;2026-10-30;1;;{source}\n")
            result = self.run_digest("--outcomes", path)
            self.assertEqual(result.returncode, 2, source)
            self.assertIn("source", result.stderr)
            self.assertIn("row 2", result.stderr)
            self.assertNotIn(source, result.stderr.replace("^[a-z0-9]", ""))
            self.assertNotIn("Max Mustermann", result.stdout)

    def test_phone_in_decisions_and_next_exits_2(self) -> None:
        for phone in ("0170 1234567", "+49 170 1234567", "030/12345678", "(0711) 123-4567"):
            decisions = self.root / "dp.txt"
            decisions.write_text(f"Rueckruf unter {phone}\n", encoding="utf-8")
            self.assert_rejected("--decisions", str(decisions), expect="phone")
            result = self.run_digest("--next", f"Anrufen {phone}")
            self.assertEqual(result.returncode, 2, phone)
            self.assertIn("phone", result.stderr)
            self.assertNotIn(phone, result.stderr)

    def test_dates_and_small_numbers_in_decisions_and_next_pass(self) -> None:
        decisions = self.root / "dd.txt"
        decisions.write_text("Freigabe bis 31.10.2026 oder 2026-10-31, Budget 12.500 EUR\n", encoding="utf-8")
        result = self.run_digest("--decisions", str(decisions), "--next", "Review am 15.11.2026")
        self.assertEqual(result.returncode, 0, result.stderr)

    def test_phone_and_email_in_commit_subject_are_redacted_and_counted(self) -> None:
        repo = self.root / "repo-redact"
        repo.mkdir()
        run = lambda *a, env=None: subprocess.run(["git", "-C", str(repo), *a], check=True, capture_output=True, env={**ENV, **(env or {})})
        run("init", "-q")
        run("config", "user.name", "Digest Fixture")
        run("config", "user.email", "test@example.invalid")
        for number, subject in enumerate(
            ["fix: Rueckruf 0170 1234567 fuer Kunde", "docs: Mail an max@example.com senden", "docs: Notiz vom 04.11.2026 ohne Daten"]
        ):
            (repo / f"docs{number}.md").write_text(subject, encoding="utf-8")
            stamp = f"2026-11-0{number + 2}T12:00:00+0000"
            run("add", "-A")
            run("commit", "-q", "-m", subject, env={"GIT_AUTHOR_DATE": stamp, "GIT_COMMITTER_DATE": stamp})
        out = subprocess.run(
            [sys.executable, str(SCRIPT), "--repo", str(repo), "--since", "2026-11-01", "--until", "2026-11-30"],
            capture_output=True, text=True, env=ENV,
        ).stdout
        self.assertNotIn("1234567", out)
        self.assertNotIn("max@example.com", out)
        self.assertIn("Rueckruf [entfernt] fuer Kunde", out)
        self.assertIn("Betreffe bereinigt: 2", out)
        self.assertIn("04.11.2026", out)

    def test_no_redaction_line_without_redaction(self) -> None:
        self.assertNotIn("Betreffe bereinigt", self.run_digest().stdout)

    def test_delta_suppressed_when_source_changes(self) -> None:
        outcomes = self.csv(
            "sc.csv", "qualified;2026-09-01;2026-09-30;4;;crm:bitrix24\nqualified;2026-10-01;2026-10-30;6;;crm:bitrix24-v2\n"
        )
        out = self.run_digest("--outcomes", outcomes).stdout
        self.assertIn("kein Vergleich (Quelle gewechselt)", out)
        self.assertNotIn("Delta", out)

    def test_inquiries_alone_do_not_count_as_pipeline_evidence(self) -> None:
        outcomes = self.csv("i.csv", "inquiries;2026-10-01;2026-10-30;12;;crm\n")
        out = self.run_digest("--outcomes", outcomes).stdout
        self.assertIn("Kein Pipeline-Beleg (qualified/progressed/won fehlen).", out)
        with_pipeline = self.csv("ip.csv", "inquiries;2026-10-01;2026-10-30;12;;crm\nqualified;2026-10-01;2026-10-30;3;;crm\n")
        self.assertNotIn("Kein Pipeline-Beleg", self.run_digest("--outcomes", with_pipeline).stdout)

    def test_recent_lagging_windows_are_marked_immature(self) -> None:
        outcomes = self.csv(
            "mat.csv",
            "won;2026-10-01;2026-10-30;1;;crm\nprogressed;2026-08-01;2026-08-31;3;;crm\nqualified;2026-10-01;2026-10-30;5;;crm\n",
        )
        out = self.run_digest("--outcomes", outcomes).stdout
        self.assertIn("won noch nicht ausgereift", out)
        self.assertNotIn("progressed noch nicht ausgereift", out)
        self.assertNotIn("qualified noch nicht ausgereift", out)
        self.assertIn("won (lagging) 2026-10-01..2026-10-30: 1", out)

    def test_extra_column_exits_2(self) -> None:
        path = self.csv("x.csv", "won;2026-10-01;2026-10-30;1;;crm;Anna\n", HEADER.strip() + ";name\n")
        self.assert_rejected("--outcomes", path, expect="header")

    def test_rate_without_denominator_exits_2(self) -> None:
        path = self.csv("r.csv", "ctr;2026-10-01;2026-10-30;45;;gsc\n")
        self.assert_rejected("--tracking", path, expect="needs a denominator")

    def test_bad_arguments_exit_2(self) -> None:
        result = subprocess.run(
            [sys.executable, str(SCRIPT), "--repo", str(self.repo)], capture_output=True, text=True, env=ENV
        )
        self.assertEqual(result.returncode, 2)


if __name__ == "__main__":
    unittest.main()
