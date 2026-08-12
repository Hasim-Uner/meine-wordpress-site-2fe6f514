from __future__ import annotations

import importlib.util
import tempfile
import unittest
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[1] / "scripts" / "outcome-contract-check.py"
SPEC = importlib.util.spec_from_file_location("outcome_contract_check", SCRIPT)
assert SPEC and SPEC.loader
MODULE = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(MODULE)


class OutcomeContractCheckTests(unittest.TestCase):
    def setUp(self) -> None:
        self.tempdir = tempfile.TemporaryDirectory()
        self.addCleanup(self.tempdir.cleanup)
        self.root = Path(self.tempdir.name).resolve()

    def write_contract(self, crm: str, leads: str, command: str) -> None:
        sources = {
            MODULE.CRM_PATH: crm,
            MODULE.LEADS_PATH: leads,
            MODULE.COMMAND_PATH: command,
        }
        for relative, content in sources.items():
            path = self.root / relative
            path.parent.mkdir(parents=True, exist_ok=True)
            path.write_text(content, encoding="utf-8")

    def test_ready_contract(self) -> None:
        self.write_contract(
            """
            return [ 'status' => 'qualified' ];
            return [ 'status' => 'nurture' ];
            update_post_meta( $id, '_nexus_review_qualification_status', $status );
            """,
            """
            $metrics = [ 'qualified' => 0 ];
            $qualification = get_post_meta( $id, '_nexus_review_qualification_status', true );
            $metrics['qualified'] += 1;
            """,
            """
            if ( in_array( $qualification, [ 'qualified' ], true ) ) { $score += 8; }
            """,
        )

        report = MODULE.build_report(self.root)

        self.assertTrue(report["ready"])
        self.assertEqual(report["failure_count"], 0)

    def test_detects_missing_aggregation_and_stale_enum(self) -> None:
        self.write_contract(
            """
            return [ 'status' => 'qualified' ];
            return [ 'status' => 'nurture' ];
            update_post_meta( $id, '_nexus_review_qualification_status', $status );
            """,
            "$metrics = [ 'requests' => 0, 'progressed' => 0, 'won' => 0 ];",
            "if ( in_array( $qualification, [ 'green', 'yellow' ], true ) ) { $score += 8; }",
        )

        report = MODULE.build_report(self.root)
        failed_ids = [check["id"] for check in report["checks"] if not check["ok"]]

        self.assertFalse(report["ready"])
        self.assertEqual(
            failed_ids,
            [
                "cockpit_reads_qualification",
                "cockpit_has_qualified_bucket",
                "cockpit_increments_qualified",
                "command_center_enum_matches_crm",
            ],
        )
        self.assertIn("not decision-ready", report["decision_metric_gate"])


if __name__ == "__main__":
    unittest.main()
