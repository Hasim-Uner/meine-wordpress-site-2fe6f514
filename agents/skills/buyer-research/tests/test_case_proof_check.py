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


def internal_row(**overrides: str) -> dict[str, str]:
    """Interner Claim aus einem Export: kein Kanon, nicht oeffentlich."""
    return percent_row(
        source_kind="crm_export", source_locator="CRM-EXPORT-A", public="false", **overrides
    )


def public_canon_row(index: int, metric: str, value: str, locator: str, unit: str = "EUR") -> dict[str, str]:
    """Oeffentlicher Canon-Claim ohne Rechenbasis (nur fuer Nicht-Prozent-Kanon)."""
    return percent_row(
        claim_id=f"T-{index:03d}",
        metric=metric,
        value=value,
        unit=unit,
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
        # 30/199 = 15,075: ganze Zahl, Abweichung unter einer halben Einheit.
        result = self.check(percent_row(numerator="30", denominator="199"))
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
                source_kind="crm_export",
                source_locator="CRM-EXPORT-A",
                public="false",
                baseline_value="5",
                baseline_window_start="2025-11-01",
                baseline_window_end="2025-12-31",
            )
        )
        self.assertEqual(result.returncode, 0, result.stdout)
        self.assertIn("Fenster ungleich lang, Vergleich kennzeichnen", result.stdout)

    def test_small_base_warns_only(self) -> None:
        result = self.check(internal_row(value="20", numerator="4", denominator="20"))
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

    # --- Kanon-Abgleich: Wert, Bezugsgruppe, Annahme, Parsebarkeit ---

    def test_canon_value_mismatch_fails(self) -> None:
        # 1/2 = 50 % stimmt rechnerisch, weicht aber vom Kanonwert ab.
        result = self.check(percent_row(value="50", numerator="1", denominator="2"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Kanonwert weicht ab", result.stdout)

    def test_canon_group_label_mismatch_fails(self) -> None:
        result = self.check(percent_row(denominator_label="alle Anfragen"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Bezugsgruppe weicht vom Kanon ab", result.stdout)

    def test_canon_group_label_is_case_and_whitespace_insensitive(self) -> None:
        result = self.check(percent_row(denominator_label="  Vorqualifizierte   CRM-leads "))
        self.assertEqual(result.returncode, 0, result.stdout)

    def test_public_claim_on_hedged_assumption_fails(self) -> None:
        result = self.check(
            percent_row(
                metric="fake_close_rate_before",
                value="3",
                numerator="3",
                denominator="100",
                denominator_label="Abschlussquote vorher (gekaufte Portal-Leads)",
                source_locator="fake_close_rate_before",
            )
        )
        self.assertEqual(result.returncode, 1)
        self.assertIn("Annahme, keine Messung", result.stdout)

    def test_internal_claim_on_hedged_assumption_warns_only(self) -> None:
        result = self.check(
            percent_row(
                metric="fake_close_rate_before",
                value="3",
                numerator="3",
                denominator="100",
                denominator_label="Abschlussquote vorher (gekaufte Portal-Leads)",
                source_locator="fake_close_rate_before",
                public="false",
            )
        )
        self.assertEqual(result.returncode, 0, result.stdout)
        self.assertIn("Annahme, keine Messung", result.stdout)

    def test_unparseable_canon_key_fails(self) -> None:
        # Schluessel steht nur als Getter-Argument im Kanon, nicht als Block.
        result = self.check(percent_row(source_locator="fake_getter_only"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("nicht parsebar", result.stdout)

    def test_canon_block_without_readable_number_fails(self) -> None:
        result = self.check(percent_row(source_locator="fake_no_number"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("nicht lesbar", result.stdout)

    def test_canon_number_falls_back_to_display_with_decimal_comma(self) -> None:
        result = self.check(
            percent_row(
                value="15.4", numerator="308", denominator="2000", source_locator="fake_decimal_rate"
            )
        )
        self.assertEqual(result.returncode, 0, result.stdout)

    def test_non_percent_canon_value_mismatch_fails(self) -> None:
        result = self.check(public_canon_row(1, "fake_cpl_after", "25", "fake_cpl_after"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Kanonwert weicht ab", result.stdout)

    # --- Rundung nach angegebener Genauigkeit ---

    def test_precision_tolerance_blocks_coarse_deviation_on_decimal_value(self) -> None:
        # 15.4 mit 30/200 = 15,0: frueher durch die globale Toleranz gerutscht.
        result = self.check(internal_row(value="15.4"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Rechnung stimmt nicht", result.stdout)

    def test_precision_tolerance_accepts_matching_decimal_value(self) -> None:
        result = self.check(internal_row(value="15.4", numerator="308", denominator="2000"))
        self.assertEqual(result.returncode, 0, result.stdout)

    def test_precision_tolerance_for_whole_number_is_half_unit(self) -> None:
        # 31/200 = 15,5: genau eine halbe Einheit entfernt, noch erlaubt.
        ok = self.check(internal_row(numerator="31", denominator="200"))
        self.assertEqual(ok.returncode, 0, ok.stdout)
        # 32/200 = 16,0: eine ganze Einheit entfernt, Fehler.
        bad = self.check(internal_row(numerator="32", denominator="200"))
        self.assertEqual(bad.returncode, 1)
        self.assertIn("Rechnung stimmt nicht", bad.stdout)

    def test_precision_tolerance_applies_to_canon_value(self) -> None:
        # Genauere Angabe als der Kanon hergibt: 15.4 gegen den Kanonwert mit ganzer Zahl.
        result = self.check(percent_row(value="15.4", numerator="308", denominator="2000"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Kanonwert weicht ab", result.stdout)

    # --- Kleine Basis ---

    def test_small_base_public_is_error(self) -> None:
        result = self.check(percent_row(value="15", numerator="3", denominator="20"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("kleine Basis nicht oeffentlich verallgemeinern", result.stdout)

    def test_small_base_at_threshold_is_not_small(self) -> None:
        result = self.check(internal_row(value="20", numerator="6", denominator="30"))
        self.assertEqual(result.returncode, 0, result.stdout)
        self.assertNotIn("kleine Basis", result.stdout)

    # --- Einheit ---

    def test_percent_unit_spellings_are_normalized_and_checked(self) -> None:
        for unit in ("Prozent", "percent", "PERCENT"):
            with self.subTest(unit=unit):
                ok = self.check(percent_row(unit=unit))
                self.assertEqual(ok.returncode, 0, ok.stdout)
                bad = self.check(percent_row(unit=unit, value="25"))
                self.assertEqual(bad.returncode, 1)
                self.assertIn("Rechnung stimmt nicht", bad.stdout)

    def test_unknown_unit_with_numerator_and_denominator_fails(self) -> None:
        result = self.check(internal_row(unit="pct", value="25"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Einheit fuer Quote unklar", result.stdout)

    # --- Oeffentlicher Vorher/Nachher-Claim ---

    def test_public_baseline_without_baseline_key_fails(self) -> None:
        row = public_canon_row(1, "fake_cpl_after", "22", "fake_cpl_after")
        row.update(
            baseline_value="150",
            baseline_window_start="2025-07-01",
            baseline_window_end="2025-12-31",
        )
        result = self.check(row)
        self.assertEqual(result.returncode, 1)
        self.assertIn("baseline:<kanon-schluessel>", result.stdout)

    def public_before_after(self, **overrides: str) -> subprocess.CompletedProcess[str]:
        row = public_canon_row(1, "fake_cpl_after", "22", "fake_cpl_after")
        row.update(
            baseline_value="150",
            baseline_window_start="2025-07-01",
            baseline_window_end="2025-12-31",
            definition="[SYNTHETISCH] Kosten pro Anfrage, baseline:fake_cpl_before",
        )
        row.update(overrides)
        return self.check(row)

    def test_public_baseline_with_canon_baseline_key_passes(self) -> None:
        result = self.public_before_after()
        self.assertEqual(result.returncode, 0, result.stdout)

    def test_public_baseline_value_must_match_canon_baseline(self) -> None:
        result = self.public_before_after(baseline_value="120")
        self.assertEqual(result.returncode, 1)
        self.assertIn("Baseline: Kanonwert weicht ab", result.stdout)

    def test_public_baseline_unknown_key_fails(self) -> None:
        result = self.public_before_after(definition="[SYNTHETISCH] baseline:fake_missing_key")
        self.assertEqual(result.returncode, 1)
        self.assertIn("Baseline: Kanon-Schluessel nicht gefunden", result.stdout)

    def test_public_baseline_on_hedged_assumption_fails(self) -> None:
        result = self.public_before_after(
            baseline_value="3", definition="[SYNTHETISCH] baseline:fake_close_rate_before"
        )
        self.assertEqual(result.returncode, 1)
        self.assertIn("Baseline: Annahme, keine Messung", result.stdout)

    def test_internal_baseline_needs_no_canon_baseline(self) -> None:
        result = self.check(
            internal_row(
                baseline_value="5",
                baseline_window_start="2025-07-01",
                baseline_window_end="2025-12-31",
            )
        )
        self.assertEqual(result.returncode, 0, result.stdout)

    # --- Einheit gegen Kanon (Bypass: Einheit weglassen/umbenennen) ---

    def test_percent_canon_with_euro_unit_and_no_base_fails(self) -> None:
        result = self.check(public_canon_row(1, "fake_close_rate", "15", "fake_close_rate", unit="EUR"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Einheit weicht vom Kanon ab", result.stdout)

    def test_percent_canon_with_unrelated_unit_fails(self) -> None:
        result = self.check(public_canon_row(1, "fake_close_rate", "15", "fake_close_rate", unit="count"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Einheit weicht vom Kanon ab", result.stdout)

    def test_percent_canon_always_requires_base(self) -> None:
        result = self.check(percent_row(numerator="", denominator="", denominator_label=""))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Prozentwert braucht numerator, denominator, denominator_label", result.stdout)

    def test_euro_canon_with_percent_unit_fails(self) -> None:
        result = self.check(percent_row(metric="fake_cpl_after", value="22", source_locator="fake_cpl_after"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Einheit weicht vom Kanon ab", result.stdout)

    def test_euro_unit_spellings_match_euro_canon(self) -> None:
        for unit in ("EUR", "eur", "Euro", "\u20ac"):
            with self.subTest(unit=unit):
                result = self.check(public_canon_row(1, "fake_cpl_after", "22", "fake_cpl_after", unit=unit))
                self.assertEqual(result.returncode, 0, result.stdout)

    def test_unitless_canon_accepts_any_unit(self) -> None:
        result = self.check(public_canon_row(1, "fake_lead_count", "200", "fake_lead_count", unit="count"))
        self.assertEqual(result.returncode, 0, result.stdout)

    # --- Spannen: nur display_hedged ist eine Annahme ---

    def test_measured_range_is_not_an_assumption(self) -> None:
        result = self.check(public_canon_row(1, "fake_cpl_tested", "70-80", "fake_range_measured"))
        self.assertEqual(result.returncode, 0, result.stdout)
        self.assertNotIn("Annahme", result.stdout)

    def test_range_accepts_en_dash_and_spaces(self) -> None:
        for value in ("70\u201380", "70 - 80", "70 \u2013 80"):
            with self.subTest(value=value):
                result = self.check(public_canon_row(1, "fake_cpl_tested", value, "fake_range_measured"))
                self.assertEqual(result.returncode, 0, result.stdout)

    def test_range_as_single_number_fails(self) -> None:
        for value in ("70", "80", "75"):
            with self.subTest(value=value):
                result = self.check(public_canon_row(1, "fake_cpl_tested", value, "fake_range_measured"))
                self.assertEqual(result.returncode, 1)
                self.assertIn("Spanne als Einzelwert", result.stdout)

    def test_range_must_match_both_endpoints(self) -> None:
        for value in ("70-90", "60-80", "75-80"):
            with self.subTest(value=value):
                result = self.check(public_canon_row(1, "fake_cpl_tested", value, "fake_range_measured"))
                self.assertEqual(result.returncode, 1)
                self.assertIn("Kanonwert weicht ab", result.stdout)

    def test_display_only_range_requires_full_range(self) -> None:
        ok = self.check(public_canon_row(1, "fake_cpl_late", "15-30", "fake_range_display_only"))
        self.assertEqual(ok.returncode, 0, ok.stdout)
        bad = self.check(public_canon_row(1, "fake_cpl_late", "15", "fake_range_display_only"))
        self.assertEqual(bad.returncode, 1)
        self.assertIn("Spanne als Einzelwert", bad.stdout)

    def test_value_high_without_range_display_is_still_a_range(self) -> None:
        ok = self.check(public_canon_row(1, "fake_cpl_values", "40-50", "fake_range_values_only"))
        self.assertEqual(ok.returncode, 0, ok.stdout)
        bad = self.check(public_canon_row(1, "fake_cpl_values", "40", "fake_range_values_only"))
        self.assertEqual(bad.returncode, 1)
        self.assertIn("Spanne als Einzelwert", bad.stdout)

    def test_range_with_wrong_unit_fails(self) -> None:
        result = self.check(public_canon_row(1, "fake_cpl_tested", "70-80", "fake_range_measured", unit="%"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Einheit weicht vom Kanon ab", result.stdout)

    def test_range_value_on_single_number_canon_fails(self) -> None:
        result = self.check(public_canon_row(1, "fake_cpl_after", "20-25", "fake_cpl_after"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("value ist keine Zahl", result.stdout)

    def test_range_endpoints_checked_for_false_precision(self) -> None:
        result = self.check(public_canon_row(1, "fake_cpl_tested", "70,0-80", "fake_range_measured"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Scheingenauigkeit gegenueber Kanon", result.stdout)

    def test_internal_claim_on_hedged_range_still_assumption_warning(self) -> None:
        # value_high + display_hedged bleibt Annahme: Gruppen-/Spannenabgleich entfaellt.
        result = self.check(
            percent_row(
                metric="fake_close_rate_before", value="3", numerator="3", denominator="100",
                source_locator="fake_close_rate_before", public="false",
            )
        )
        self.assertEqual(result.returncode, 0, result.stdout)

    # --- Scheingenauigkeit gegenueber dem Kanon ---

    def test_false_precision_against_canon_fails(self) -> None:
        for value in ("15,00", "15.0", "15,0"):
            with self.subTest(value=value):
                result = self.check(percent_row(value=value, numerator="30", denominator="200"))
                self.assertEqual(result.returncode, 1)
                self.assertIn("Scheingenauigkeit gegenueber Kanon", result.stdout)

    def test_canon_decimals_allow_same_precision(self) -> None:
        ok = self.check(
            percent_row(value="15,4", numerator="308", denominator="2000", source_locator="fake_decimal_rate")
        )
        self.assertEqual(ok.returncode, 0, ok.stdout)
        # Mehr Stellen als der Kanon (15,4 -> 15,40) ist Scheingenauigkeit.
        bad = self.check(
            percent_row(value="15,40", numerator="308", denominator="2000", source_locator="fake_decimal_rate")
        )
        self.assertEqual(bad.returncode, 1)
        self.assertIn("Scheingenauigkeit gegenueber Kanon", bad.stdout)

    def test_false_precision_on_euro_canon_fails(self) -> None:
        result = self.check(public_canon_row(1, "fake_cpl_after", "22.00", "fake_cpl_after"))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Scheingenauigkeit gegenueber Kanon", result.stdout)

    def test_brief_prints_max_three_results_and_no_invented_numbers(self) -> None:
        rows = [
            percent_row(claim_id="T-001", metric="fake_close_rate"),
            public_canon_row(2, "fake_cpl_after", "22", "fake_cpl_after"),
            public_canon_row(3, "fake_lead_count", "200", "fake_lead_count", unit="count"),
            percent_row(claim_id="T-004", metric="fake_won_deals"),
            public_canon_row(5, "fake_lead_count_b", "200", "fake_lead_count", unit="count"),
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
