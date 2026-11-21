#!/usr/bin/env python3
"""CLI regression tests for buyer-research case-proof-check.py (offline, fake canon)."""

from __future__ import annotations

import csv
import re
import subprocess
import tempfile
import unittest
from pathlib import Path


ROOT = Path(__file__).resolve().parents[4]
SCRIPT = ROOT / "agents/skills/buyer-research/scripts/case-proof-check.py"
FIXTURES = ROOT / "agents/skills/buyer-research/tests/fixtures/case-proof"
FAKE_CANON = FIXTURES / "canon"
VALID_LEDGER = FIXTURES / "valid-ledger.csv"
HEADER = (
    "claim_id;metric;value;unit;numerator;denominator;denominator_label;"
    "window_start;window_end;baseline_value;baseline_window_start;baseline_window_end;"
    "source_kind;source_locator;definition;attribution_note;public"
).split(";")


def percent_row(**overrides: str) -> dict[str, str]:
    row = {
        "claim_id": "T-001",
        "metric": "fake_close_rate",
        "value": "15",
        "unit": "%",
        "numerator": "30",
        "denominator": "200",
        "denominator_label": "vorqualifizierte CRM-Leads",
        "window_start": "2026-01-01",
        "window_end": "2026-06-30",
        "source_kind": "canon",
        "source_locator": "fake_close_rate",
        "definition": "[SYNTHETISCH] Abschluesse unter vorqualifizierten Leads",
        "attribution_note": "Vertrieb hatte wesentlichen Anteil",
        "public": "true",
    }
    row.update(overrides)
    return row


def public_canon_row(index: int, metric: str, value: str, locator: str) -> dict[str, str]:
    return percent_row(
        claim_id=f"T-{index:03d}",
        metric=metric,
        value=value,
        unit="EUR",
        numerator="",
        denominator="",
        denominator_label="",
        source_locator=locator,
    )


class CaseProofCheckTests(unittest.TestCase):
    def run_cli(self, *args: str) -> subprocess.CompletedProcess[str]:
        return subprocess.run(
            ["python3", str(SCRIPT), *args, "--canon-dir", str(FAKE_CANON)],
            cwd=ROOT,
            text=True,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            check=False,
        )

    def write_ledger(
        self,
        directory: Path,
        rows: list[dict[str, str]],
        header: list[str] | None = None,
    ) -> Path:
        path = directory / "case.csv"
        fieldnames = header or HEADER
        with path.open("w", encoding="utf-8", newline="") as handle:
            writer = csv.DictWriter(handle, fieldnames=fieldnames, delimiter=";")
            writer.writeheader()
            for row in rows:
                writer.writerow({key: row.get(key, "") for key in fieldnames})
        return path

    def check(self, *rows: dict[str, str], extra: tuple[str, ...] = ()) -> subprocess.CompletedProcess[str]:
        with tempfile.TemporaryDirectory() as tmp:
            return self.run_cli(str(self.write_ledger(Path(tmp), list(rows))), *extra)

    def test_valid_fixture_ledger_passes(self) -> None:
        result = self.run_cli(str(VALID_LEDGER))
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        self.assertIn("error=0", result.stdout)
        self.assertNotIn("ERROR ", result.stdout)

    def test_percent_math_mismatch_fails(self) -> None:
        result = self.check(percent_row(value="25"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Rechnung stimmt nicht", result.stdout)

    def test_percent_within_tolerance_passes(self) -> None:
        result = self.check(percent_row(value="15.4"))
        self.assertEqual(result.returncode, 0, result.stdout)

    def test_missing_denominator_fails(self) -> None:
        result = self.check(percent_row(denominator="", denominator_label=""))
        self.assertEqual(result.returncode, 1)
        self.assertIn("denominator", result.stdout)

    def test_unknown_canon_key_fails(self) -> None:
        result = self.check(percent_row(source_locator="fake_unknown_key"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Kanon-Schluessel nicht gefunden", result.stdout)

    def test_public_non_canon_fails(self) -> None:
        result = self.check(percent_row(source_kind="crm_export", source_locator="CRM-EXPORT-A"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("erst in den Kanon, dann oeffentlich", result.stdout)

    def test_non_canon_internal_claim_passes(self) -> None:
        result = self.check(
            percent_row(source_kind="crm_export", source_locator="CRM-EXPORT-A", public="false")
        )
        self.assertEqual(result.returncode, 0, result.stdout)

    def test_pii_locator_fails(self) -> None:
        for locator in ("person@example.invalid", "0151 2345 6789"):
            with self.subTest(locator=locator):
                result = self.check(
                    percent_row(source_kind="crm_export", source_locator=locator, public="false")
                )
                self.assertEqual(result.returncode, 1)
                self.assertIn("E-Mail oder Telefonnummer", result.stdout)

    def test_baseline_after_window_fails(self) -> None:
        result = self.check(
            percent_row(
                baseline_value="5",
                baseline_window_start="2026-07-01",
                baseline_window_end="2026-12-31",
            )
        )
        self.assertEqual(result.returncode, 1)
        self.assertIn("vor dem Ergebnisfenster", result.stdout)

    def test_unequal_window_length_warns_only(self) -> None:
        result = self.check(
            percent_row(
                baseline_value="5",
                baseline_window_start="2025-11-01",
                baseline_window_end="2025-12-31",
            )
        )
        self.assertEqual(result.returncode, 0, result.stdout)
        self.assertIn("Fenster ungleich lang, Vergleich kennzeichnen", result.stdout)

    def test_small_base_warns_only(self) -> None:
        result = self.check(percent_row(value="20", numerator="4", denominator="20"))
        self.assertEqual(result.returncode, 0, result.stdout)
        self.assertIn("WARN T-001", result.stdout)
        self.assertIn("kleine Basis", result.stdout)

    def test_duplicate_claim_id_fails(self) -> None:
        result = self.check(percent_row(), percent_row())
        self.assertEqual(result.returncode, 1)
        self.assertIn("doppelte claim_id", result.stdout)

    def test_cpo_from_cpl_times_rate_fails(self) -> None:
        result = self.check(
            percent_row(
                metric="cpo_total",
                value="146",
                unit="EUR",
                numerator="",
                denominator="",
                denominator_label="",
                source_kind="crm_export",
                source_locator="CRM-EXPORT-A",
                definition="CPL geteilt durch CRM-Quote",
                public="false",
            )
        )
        self.assertEqual(result.returncode, 1)
        self.assertIn("Gesamt-CPO", result.stdout)

    def test_bad_header_is_usage_error(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            path = self.write_ledger(Path(tmp), [percent_row()], header=HEADER[:-1])
            result = self.run_cli(str(path))
        self.assertEqual(result.returncode, 2)
        self.assertIn("Header", result.stderr)

    def test_missing_file_is_usage_error(self) -> None:
        result = self.run_cli(str(FIXTURES / "does-not-exist.csv"))
        self.assertEqual(result.returncode, 2)

    def test_brief_prints_max_three_results_and_no_invented_numbers(self) -> None:
        rows = [
            public_canon_row(1, "fake_close_rate", "15", "fake_close_rate"),
            public_canon_row(2, "fake_cpl_after", "22", "fake_cpl_after"),
            public_canon_row(3, "fake_lead_count", "200", "fake_lead_count"),
            public_canon_row(4, "fake_won_deals", "15", "fake_close_rate"),
            public_canon_row(5, "fake_lead_count_b", "200", "fake_lead_count"),
        ]
        result = self.check(*rows, extra=("--brief",))
        self.assertEqual(result.returncode, 0, result.stdout)
        self.assertIn("Ausgangslage", result.stdout)
        self.assertIn("Mechanismus", result.stdout)
        self.assertIn("Grenzen", result.stdout)
        self.assertIn("Promotion gate", result.stdout)
        section = result.stdout.split("\nErgebnis\n", 1)[1].split("\nGrenzen\n", 1)[0]
        result_lines = [line for line in section.splitlines() if line.startswith("- ")]
        self.assertEqual(len(result_lines), 3)
        self.assertIn("fake_won_deals", section, "nachgelagertes Ergebnis wird bevorzugt")
        allowed = {"15", "22", "200", "2026", "01", "06", "30"}  # nur Ledger-Werte und -Daten
        for token in re.findall(r"\d+", "\n".join(result_lines)):
            self.assertIn(token, allowed, f"Zahl {token} steht nicht im Ledger")

    def test_brief_not_printed_on_errors(self) -> None:
        result = self.check(percent_row(value="99"), extra=("--brief",))
        self.assertEqual(result.returncode, 1)
        self.assertNotIn("CASE-BRIEF", result.stdout)


if __name__ == "__main__":
    unittest.main()
