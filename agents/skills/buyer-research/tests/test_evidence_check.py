#!/usr/bin/env python3
"""CLI regression tests for buyer-research evidence-check.py."""

from __future__ import annotations

import csv
import subprocess
import tempfile
import unittest
from pathlib import Path


ROOT = Path(__file__).resolve().parents[4]
SCRIPT = ROOT / "agents/skills/buyer-research/scripts/evidence-check.py"
HEADER = [
    "id",
    "source_key",
    "evidence_class",
    "source_kind",
    "source_locator",
    "source_date",
    "captured_at",
    "segment",
    "role",
    "target_fit",
    "verbatim",
    "anonymized",
    "allowed_use",
    "topic",
    "context",
    "evidence_text",
]


def direct_row() -> dict[str, str]:
    return {
        "id": "BR-001",
        "source_key": "P-TEST-001",
        "evidence_class": "direct_voc",
        "source_kind": "marktcheck",
        "source_locator": "CRM-TEST-17",
        "source_date": "2026-08-01",
        "captured_at": "2026-08-10",
        "segment": "Photovoltaik",
        "role": "Geschäftsführung",
        "target_fit": "confirmed",
        "verbatim": "true",
        "anonymized": "true",
        "allowed_use": "voc",
        "topic": "objection",
        "context": "Synthetische Testfixture ohne reale Person",
        "evidence_text": "[SYNTHETISCH] Wir brauchen bessere Anfragen; nicht nur mehr Leads.",
    }


def proxy_row() -> dict[str, str]:
    row = direct_row()
    row.update(
        {
            "id": "BR-002",
            "source_key": "PUBLIC-TEST-002",
            "evidence_class": "proxy",
            "source_kind": "community",
            "source_locator": "https://example.invalid/thread/17",
            "source_date": "2026-08-02",
            "segment": "SHK",
            "role": "Betriebsleitung",
            "target_fit": "probable",
            "allowed_use": "hypothesis",
            "topic": "trigger",
            "evidence_text": "[SYNTHETISCH] Seit dem Semikolon; im Satz prüfen wir Alternativen.",
        }
    )
    return row


def direct_note_row() -> dict[str, str]:
    row = direct_row()
    row.update(
        {
            "id": "BR-004",
            "source_key": "P-TEST-004",
            "evidence_class": "direct_note",
            "source_kind": "sales_call",
            "source_locator": "CALL-TEST-4",
            "verbatim": "false",
            "allowed_use": "hypothesis",
            "topic": "anxiety",
            "context": "Synthetische Gesprächsnotiz ohne reale Person",
            "evidence_text": "[SYNTHETISCH] Gesprächspartner äußerte Sorge vor Büroarbeit.",
        }
    )
    return row


def market_row() -> dict[str, str]:
    row = direct_row()
    row.update(
        {
            "id": "BR-003",
            "source_key": "DOMAIN-TEST-003",
            "evidence_class": "market_language",
            "source_kind": "competitor_page",
            "source_locator": "https://example.invalid/competitor",
            "source_date": "2026-08-03",
            "target_fit": "unknown",
            "verbatim": "false",
            "allowed_use": "structure_only",
            "topic": "vocabulary",
            "evidence_text": "[SYNTHETISCH] Wettbewerber verspricht planbare Anfragen.",
        }
    )
    return row


class EvidenceCheckTests(unittest.TestCase):
    def run_cli(self, *args: str) -> subprocess.CompletedProcess[str]:
        return subprocess.run(
            ["python3", str(SCRIPT), *args],
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
        path = directory / "evidence.csv"
        fieldnames = header or HEADER
        with path.open("w", encoding="utf-8", newline="") as handle:
            writer = csv.DictWriter(handle, fieldnames=fieldnames, delimiter=";")
            writer.writeheader()
            for row in rows:
                writer.writerow({key: row.get(key, "") for key in fieldnames})
        return path

    def test_valid_mixed_ledger_and_utf8(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            path = self.write_ledger(Path(tmp), [direct_row(), proxy_row()])
            result = self.run_cli(str(path))
        self.assertEqual(result.returncode, 0, result.stderr)
        self.assertIn("direct_voc=1", result.stdout)
        self.assertIn("proxy=1", result.stdout)
        self.assertIn("voc_eligible=1", result.stdout)

    def test_same_person_across_rows_counts_as_one_source(self) -> None:
        first = direct_row()
        second = direct_row()
        second["id"] = "BR-005"
        second["source_locator"] = "CALL-TEST-5"
        second["evidence_text"] = "[SYNTHETISCH] Ein zweiter Beleg derselben Person."
        with tempfile.TemporaryDirectory() as tmp:
            path = self.write_ledger(Path(tmp), [first, second])
            result = self.run_cli(str(path))
        self.assertEqual(result.returncode, 0, result.stderr)
        self.assertIn("direct_voc=2 (sources=1)", result.stdout)

    def test_same_person_across_evidence_classes_counts_once(self) -> None:
        direct = direct_row()
        note = direct_note_row()
        note["source_key"] = direct["source_key"]
        public = proxy_row()
        public["source_key"] = direct["source_key"]
        with tempfile.TemporaryDirectory() as tmp:
            path = self.write_ledger(Path(tmp), [direct, note, public])
            result = self.run_cli(str(path))
        self.assertEqual(result.returncode, 0, result.stderr)
        self.assertIn("buyer_sources_total=1", result.stdout)
        self.assertIn("voc_sources_eligible=1", result.stdout)

    def test_proxy_cannot_become_voc(self) -> None:
        row = proxy_row()
        row["allowed_use"] = "voc"
        with tempfile.TemporaryDirectory() as tmp:
            path = self.write_ledger(Path(tmp), [row])
            result = self.run_cli(str(path))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Proxy darf nicht Voice of Customer werden", result.stderr)

    def test_competitor_is_market_language_not_proxy(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            valid_path = self.write_ledger(Path(tmp), [market_row()])
            valid_result = self.run_cli(str(valid_path))
            self.assertEqual(valid_result.returncode, 0, valid_result.stderr)
            self.assertIn("market_language=1", valid_result.stdout)

            invalid = market_row()
            invalid["evidence_class"] = "proxy"
            invalid["allowed_use"] = "hypothesis"
            invalid_path = self.write_ledger(Path(tmp), [invalid])
            invalid_result = self.run_cli(str(invalid_path))
        self.assertEqual(invalid_result.returncode, 1)
        self.assertIn("source_kind 'competitor_page' passt nicht zu proxy", invalid_result.stderr)

    def test_direct_paraphrase_and_non_anonymized_fail(self) -> None:
        row = direct_row()
        row["verbatim"] = "false"
        row["anonymized"] = "false"
        with tempfile.TemporaryDirectory() as tmp:
            path = self.write_ledger(Path(tmp), [row])
            result = self.run_cli(str(path))
        self.assertEqual(result.returncode, 1)
        self.assertIn("Paraphrase darf nicht Voice of Customer werden", result.stderr)
        self.assertIn("direct_voc muss anonymized=true sein", result.stderr)

    def test_direct_note_is_hypothesis_not_voc(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            valid_path = self.write_ledger(Path(tmp), [direct_note_row()])
            valid_result = self.run_cli(str(valid_path))
            self.assertEqual(valid_result.returncode, 0, valid_result.stderr)
            self.assertIn("direct_note=1", valid_result.stdout)
            self.assertIn("voc_eligible=0", valid_result.stdout)

            invalid = direct_note_row()
            invalid["allowed_use"] = "voc"
            invalid["verbatim"] = "true"
            invalid_path = self.write_ledger(Path(tmp), [invalid])
            invalid_result = self.run_cli(str(invalid_path))
        self.assertEqual(invalid_result.returncode, 1)
        self.assertIn("direct_note verlangt allowed_use=hypothesis", invalid_result.stderr)
        self.assertIn("direct_note muss als nicht-wörtlich markiert sein", invalid_result.stderr)

    def test_inference_cannot_claim_voc_or_verbatim(self) -> None:
        row = direct_row()
        row.update(
            {
                "evidence_class": "inference",
                "source_kind": "agent_inference",
                "target_fit": "unknown",
                "allowed_use": "voc",
                "verbatim": "true",
            }
        )
        with tempfile.TemporaryDirectory() as tmp:
            path = self.write_ledger(Path(tmp), [row])
            result = self.run_cli(str(path))
        self.assertEqual(result.returncode, 1)
        self.assertIn("inference verlangt allowed_use=blocked", result.stderr)
        self.assertIn("Inferenz darf nicht als wörtliches Zitat", result.stderr)

    def test_bad_header_empty_file_duplicate_id_and_date_order_fail(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            tmp_path = Path(tmp)
            bad_header = self.write_ledger(tmp_path, [direct_row()], HEADER[:-1])
            result = self.run_cli(str(bad_header))
            self.assertEqual(result.returncode, 1)
            self.assertIn("CSV-Header entspricht nicht", result.stderr)

            empty_path = self.write_ledger(tmp_path, [])
            result = self.run_cli(str(empty_path))
            self.assertEqual(result.returncode, 1)
            self.assertIn("enthält keine Einträge", result.stderr)

            first = direct_row()
            second = proxy_row()
            second["id"] = first["id"]
            second["source_date"] = "2026-08-11"
            path = self.write_ledger(tmp_path, [first, second])
            result = self.run_cli(str(path))
            self.assertEqual(result.returncode, 1)
            self.assertIn("doppelte ID", result.stderr)
            self.assertIn("source_date liegt nach captured_at", result.stderr)

    def test_missing_file_is_usage_error(self) -> None:
        result = self.run_cli("/tmp/buyer-research-file-does-not-exist.csv")
        self.assertEqual(result.returncode, 2)
        self.assertIn("Datei nicht gefunden", result.stderr)

    def test_unquoted_semicolon_creates_extra_column_error(self) -> None:
        row = direct_row()
        with tempfile.TemporaryDirectory() as tmp:
            path = self.write_ledger(Path(tmp), [row])
            content = path.read_text(encoding="utf-8")
            path.write_text(
                content.replace(
                    "[SYNTHETISCH] Wir brauchen bessere Anfragen; nicht nur mehr Leads.",
                    "[SYNTHETISCH] Wir brauchen bessere Anfragen;nicht nur mehr Leads.",
                ).replace(
                    '"[SYNTHETISCH] Wir brauchen bessere Anfragen;nicht nur mehr Leads."',
                    "[SYNTHETISCH] Wir brauchen bessere Anfragen;nicht nur mehr Leads.",
                ),
                encoding="utf-8",
            )
            result = self.run_cli(str(path))
        self.assertEqual(result.returncode, 1)
        self.assertIn("unerwartete zusätzliche CSV-Spalten", result.stderr)

    def test_inventory_and_help(self) -> None:
        inventory = self.run_cli("--inventory")
        self.assertEqual(inventory.returncode, 0, inventory.stderr)
        self.assertIn("Buyer-Research-Inventar", inventory.stdout)
        help_result = self.run_cli("--help")
        self.assertEqual(help_result.returncode, 0)
        self.assertIn("Evidence Ledger", help_result.stdout)


if __name__ == "__main__":
    unittest.main(verbosity=2)
