from __future__ import annotations

import importlib.util
import tempfile
import unittest
from datetime import date
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[1] / "scripts" / "evidence-gate.py"
SPEC = importlib.util.spec_from_file_location("evidence_gate", SCRIPT)
assert SPEC and SPEC.loader
MODULE = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(MODULE)


HEADER = "period,start_date,end_date,sessions,requests,qualified,progressed,won\n"


class EvidenceGateTests(unittest.TestCase):
    def setUp(self) -> None:
        self.tempdir = tempfile.TemporaryDirectory()
        self.addCleanup(self.tempdir.cleanup)
        self.root = Path(self.tempdir.name)

    def csv(self, rows: str) -> Path:
        path = self.root / "window.csv"
        path.write_text(HEADER + rows, encoding="utf-8")
        return path

    def evaluate(self, path: Path, **overrides):
        values = {
            "periods": MODULE.load_periods(path),
            "release_date": date(2026, 7, 15),
            "min_sessions": 100,
            "min_requests": 10,
            "min_qualified": 5,
            "primary": "qualified_rate",
            "min_primary_change_pp": 0.5,
            "guardrails": ["qualification_rate", "progression_rate"],
            "guardrail_tolerance_pp": 0.0,
        }
        values.update(overrides)
        return MODULE.evaluate(**values)

    def test_ready_improvement_is_keep_candidate(self) -> None:
        path = self.csv(
            "before,2026-07-01,2026-07-14,200,14,8,4,2\n"
            "after,2026-07-16,2026-07-29,220,18,11,6,3\n"
        )

        result = self.evaluate(path)

        self.assertEqual(result["data_gate"], "ready")
        self.assertEqual(result["structural_decision"], "keep_candidate")
        self.assertGreater(result["metrics"]["qualified_rate"]["delta_pp"], 0)
        self.assertIn("does not prove", result["causal_limit"])

    def test_quality_regression_overrides_primary_lift(self) -> None:
        path = self.csv(
            "before,2026-07-01,2026-07-14,200,14,8,4,2\n"
            "after,2026-07-16,2026-07-29,200,20,9,2,1\n"
        )

        result = self.evaluate(path)

        self.assertEqual(result["data_gate"], "ready")
        self.assertEqual(result["structural_decision"], "revert_candidate")
        self.assertEqual(
            result["guardrail_regressions"],
            ["qualification_rate", "progression_rate"],
        )

    def test_missed_predeclared_volume_is_insufficient(self) -> None:
        path = self.csv(
            "before,2026-07-01,2026-07-14,90,9,6,3,1\n"
            "after,2026-07-16,2026-07-29,120,12,7,4,2\n"
        )

        result = self.evaluate(path)

        self.assertEqual(result["data_gate"], "insufficient")
        self.assertEqual(result["structural_decision"], "insufficient")
        self.assertTrue(any("before sessions" in reason for reason in result["reasons"]))
        self.assertTrue(any("before requests" in reason for reason in result["reasons"]))

    def test_unequal_windows_are_insufficient(self) -> None:
        path = self.csv(
            "before,2026-07-01,2026-07-14,200,14,8,4,2\n"
            "after,2026-07-16,2026-07-22,120,12,7,4,2\n"
        )

        result = self.evaluate(path)

        self.assertEqual(result["structural_decision"], "insufficient")
        self.assertIn("window lengths differ (14 vs 7 days)", result["reasons"])

    def test_rejects_impossible_funnel_counts(self) -> None:
        path = self.csv(
            "before,2026-07-01,2026-07-14,200,10,11,4,2\n"
            "after,2026-07-16,2026-07-29,220,18,11,6,3\n"
        )

        with self.assertRaisesRegex(MODULE.ContractError, "qualified exceeds requests"):
            MODULE.load_periods(path)

    def test_change_below_predeclared_threshold_is_inconclusive(self) -> None:
        path = self.csv(
            "before,2026-07-01,2026-07-14,1000,100,50,25,10\n"
            "after,2026-07-16,2026-07-29,1000,104,54,27,11\n"
        )

        result = self.evaluate(path, min_primary_change_pp=0.5)

        self.assertEqual(result["data_gate"], "ready")
        self.assertAlmostEqual(result["metrics"]["qualified_rate"]["delta_pp"], 0.4)
        self.assertEqual(result["structural_decision"], "inconclusive")


if __name__ == "__main__":
    unittest.main()
