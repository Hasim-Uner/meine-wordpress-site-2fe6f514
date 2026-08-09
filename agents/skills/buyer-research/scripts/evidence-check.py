#!/usr/bin/env python3
"""Inventory and validate buyer-research evidence without external packages."""

from __future__ import annotations

import argparse
import csv
import re
import subprocess
import sys
from collections import Counter
from datetime import date
from pathlib import Path


FIELDS = [
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

CLASS_POLICY = {
    "direct_voc": {
        "allowed_use": "voc",
        "source_kinds": {
            "crm_form",
            "marktcheck",
            "sales_call",
            "interview",
            "email",
            "survey",
        },
    },
    "direct_note": {
        "allowed_use": "hypothesis",
        "source_kinds": {
            "crm_form",
            "marktcheck",
            "sales_call",
            "interview",
            "email",
            "survey",
        },
    },
    "proxy": {
        "allowed_use": "hypothesis",
        "source_kinds": {"forum", "community", "review", "public_comment"},
    },
    "market_language": {
        "allowed_use": "structure_only",
        "source_kinds": {
            "competitor_page",
            "portal_page",
            "industry_press",
            "search_data",
        },
    },
    "inference": {
        "allowed_use": "blocked",
        "source_kinds": {"agent_inference"},
    },
}

TARGET_FIT = {"confirmed", "probable", "unknown", "not_target"}
BOOLEANS = {"true", "false"}
TOPICS = {
    "problem",
    "trigger",
    "outcome",
    "alternative",
    "objection",
    "anxiety",
    "criterion",
    "vocabulary",
    "proof",
    "awareness",
}
EMAIL_RE = re.compile(r"[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}", re.I)


def repo_root() -> Path:
    try:
        output = subprocess.check_output(
            ["git", "rev-parse", "--show-toplevel"],
            cwd=Path.cwd(),
            stderr=subprocess.DEVNULL,
            text=True,
        ).strip()
        if output:
            return Path(output)
    except (OSError, subprocess.CalledProcessError):
        pass
    return Path(__file__).resolve().parents[4]


def count_blockquotes_outside_fences(path: Path) -> int:
    in_fence = False
    count = 0
    for raw_line in path.read_text(encoding="utf-8").splitlines():
        line = raw_line.strip()
        if line.startswith("```"):
            in_fence = not in_fence
            continue
        if not in_fence and raw_line.startswith("> "):
            count += 1
    return count


def inventory(root: Path) -> int:
    voc_path = root / "docs/standards/VOICE_OF_CUSTOMER.md"
    harvest_path = root / "scripts/harvest-voice-of-customer.sh"
    raw_dir = root / ".ai/memory"
    raw_files = sorted(raw_dir.glob("voc-rohmaterial-*.md")) if raw_dir.is_dir() else []

    if not voc_path.is_file():
        print(f"FEHLER: VoC-Canon fehlt: {voc_path}", file=sys.stderr)
        return 2

    voc_text = voc_path.read_text(encoding="utf-8")
    status_match = re.search(r"\*\*Status:\s*([^*]+)\*\*", voc_text)
    status = status_match.group(1).strip() if status_match else "nicht angegeben"
    accepted_quotes = count_blockquotes_outside_fences(voc_path)

    print("Buyer-Research-Inventar")
    print(f"VoC-Status: {status}")
    print(f"VoC-Zitatblöcke außerhalb von Codebeispielen: {accepted_quotes}")
    print(f"Lokale rohe Harvest-Dateien (.ai/memory): {len(raw_files)}")
    print(f"Server-Harvester vorhanden: {'ja' if harvest_path.is_file() else 'nein'}")
    if harvest_path.is_file():
        print("Server-Befehl: bash scripts/harvest-voice-of-customer.sh 100")
    if accepted_quotes == 0:
        print("Nächster Schritt: direkte Interessenten-/CRM-Quellen prüfen; erst dann Proxy-Research.")
    return 0


def parse_iso(value: str, field: str, row_id: str, errors: list[str]) -> date | None:
    try:
        return date.fromisoformat(value)
    except ValueError:
        errors.append(f"{row_id}: {field} muss YYYY-MM-DD sein (ist: {value!r}).")
        return None


def validate_row(
    row: dict[str, str],
    line_number: int,
    errors: list[str],
    warnings: list[str],
) -> None:
    row_id = row.get("id", "").strip() or f"Zeile {line_number}"
    if row.get(None) is not None:
        errors.append(
            f"{row_id}: unerwartete zusätzliche CSV-Spalten; "
            "Semikolons im Text müssen in Anführungszeichen stehen."
        )
    values = {key: (row.get(key) or "").strip() for key in FIELDS}

    for field in FIELDS:
        if not values[field]:
            errors.append(f"{row_id}: Pflichtfeld {field} fehlt.")

    evidence_class = values["evidence_class"]
    policy = CLASS_POLICY.get(evidence_class)
    if not policy:
        errors.append(
            f"{row_id}: unbekannte evidence_class {evidence_class!r}; "
            f"erlaubt: {', '.join(CLASS_POLICY)}."
        )
        return

    source_kind = values["source_kind"]
    if source_kind not in policy["source_kinds"]:
        errors.append(
            f"{row_id}: source_kind {source_kind!r} passt nicht zu {evidence_class}."
        )

    expected_use = policy["allowed_use"]
    if values["allowed_use"] != expected_use:
        if evidence_class == "proxy" and values["allowed_use"] == "voc":
            errors.append(f"{row_id}: Proxy darf nicht Voice of Customer werden.")
        else:
            errors.append(
                f"{row_id}: {evidence_class} verlangt allowed_use={expected_use}."
            )

    if values["target_fit"] not in TARGET_FIT:
        errors.append(f"{row_id}: ungültiger target_fit {values['target_fit']!r}.")
    if values["verbatim"] not in BOOLEANS:
        errors.append(f"{row_id}: verbatim muss true oder false sein.")
    if values["anonymized"] not in BOOLEANS:
        errors.append(f"{row_id}: anonymized muss true oder false sein.")
    if values["topic"] not in TOPICS:
        errors.append(f"{row_id}: ungültiges topic {values['topic']!r}.")

    source_date = parse_iso(values["source_date"], "source_date", row_id, errors)
    captured_at = parse_iso(values["captured_at"], "captured_at", row_id, errors)
    if source_date and captured_at and source_date > captured_at:
        errors.append(f"{row_id}: source_date liegt nach captured_at.")

    if evidence_class == "direct_voc":
        if values["target_fit"] != "confirmed":
            errors.append(f"{row_id}: direct_voc verlangt target_fit=confirmed.")
        if values["verbatim"] != "true":
            errors.append(f"{row_id}: Paraphrase darf nicht Voice of Customer werden.")
        if values["anonymized"] != "true":
            errors.append(f"{row_id}: direct_voc muss anonymized=true sein.")
        if values["role"].lower() in {"unknown", "unbekannt"}:
            errors.append(f"{row_id}: direct_voc braucht eine bestätigte Käuferrolle.")

    if evidence_class == "direct_note":
        if values["target_fit"] != "confirmed":
            errors.append(f"{row_id}: direct_note verlangt target_fit=confirmed.")
        if values["verbatim"] != "false":
            errors.append(f"{row_id}: direct_note muss als nicht-wörtlich markiert sein.")
        if values["anonymized"] != "true":
            errors.append(f"{row_id}: direct_note muss anonymized=true sein.")
        if values["role"].lower() in {"unknown", "unbekannt"}:
            errors.append(f"{row_id}: direct_note braucht eine bestätigte Käuferrolle.")

    if evidence_class == "proxy":
        if values["target_fit"] not in {"confirmed", "probable"}:
            errors.append(f"{row_id}: Proxy braucht bestätigten oder wahrscheinlichen Zielgruppenfit.")
        if values["anonymized"] != "true":
            errors.append(f"{row_id}: öffentliche Proxy-Aussagen müssen anonymisiert sein.")
        if not values["source_locator"].startswith(("https://", "http://")):
            errors.append(f"{row_id}: Proxy braucht eine vollständige Quellen-URL.")

    if evidence_class == "market_language" and source_kind != "search_data":
        if not values["source_locator"].startswith(("https://", "http://")):
            errors.append(f"{row_id}: öffentliche Marktsprache braucht eine Quellen-URL.")

    if evidence_class == "inference" and values["verbatim"] != "false":
        errors.append(f"{row_id}: Inferenz darf nicht als wörtliches Zitat markiert sein.")

    pii_surface = " ".join(
        [
            values["source_key"],
            values["source_locator"],
            values["context"],
            values["evidence_text"],
        ]
    )
    if evidence_class in {"direct_voc", "direct_note", "proxy"} and EMAIL_RE.search(pii_surface):
        errors.append(f"{row_id}: mögliche E-Mail/PII im anonymisierten Ledger gefunden.")

    if values["verbatim"] == "false" and values["evidence_text"].startswith(('"', "„")):
        warnings.append(
            f"{row_id}: nicht-wörtliche Evidenz beginnt wie ein Zitat; Kennzeichnung prüfen."
        )


def validate_ledger(path: Path, root: Path) -> int:
    if not path.is_file():
        print(f"FEHLER: Datei nicht gefunden: {path}", file=sys.stderr)
        return 2

    try:
        with path.open("r", encoding="utf-8-sig", newline="") as handle:
            reader = csv.DictReader(handle, delimiter=";")
            if reader.fieldnames != FIELDS:
                print("FEHLER: CSV-Header entspricht nicht dem Evidence Standard.", file=sys.stderr)
                print("Erwartet: " + ";".join(FIELDS), file=sys.stderr)
                return 1
            rows = list(reader)
    except (OSError, UnicodeError, csv.Error) as exc:
        print(f"FEHLER: CSV konnte nicht gelesen werden: {exc}", file=sys.stderr)
        return 2

    if not rows:
        print("FEHLER: Evidence Ledger enthält keine Einträge.", file=sys.stderr)
        return 1

    errors: list[str] = []
    warnings: list[str] = []
    seen_ids: set[str] = set()
    seen_evidence: set[tuple[str, str]] = set()
    counts: Counter[str] = Counter()
    source_keys: dict[str, set[str]] = {name: set() for name in CLASS_POLICY}
    buyer_source_keys: set[str] = set()

    for line_number, row in enumerate(rows, start=2):
        row_id = (row.get("id") or "").strip()
        if row_id in seen_ids:
            errors.append(f"{row_id or f'Zeile {line_number}'}: doppelte ID.")
        elif row_id:
            seen_ids.add(row_id)

        signature = (
            (row.get("source_key") or "").strip(),
            (row.get("evidence_text") or "").strip(),
        )
        if signature in seen_evidence and all(signature):
            warnings.append(f"{row_id or f'Zeile {line_number}'}: mögliche doppelte Evidenz.")
        elif all(signature):
            seen_evidence.add(signature)

        validate_row(row, line_number, errors, warnings)
        evidence_class = (row.get("evidence_class") or "").strip()
        counts[evidence_class] += 1
        source_key = (row.get("source_key") or "").strip()
        if evidence_class in source_keys and source_key:
            source_keys[evidence_class].add(source_key)
        if evidence_class in {"direct_voc", "direct_note", "proxy"} and source_key:
            buyer_source_keys.add(source_key)

    try:
        relative = path.resolve().relative_to(root.resolve())
        if relative.parts[:2] != (".ai", "memory"):
            warnings.append("Ledger liegt nicht unter .ai/memory/; Datenschutz und Git-Status prüfen.")
    except ValueError:
        warnings.append("Ledger liegt außerhalb des Repos; Datenschutz und Ablage prüfen.")

    for message in errors:
        print(f"FEHLER: {message}", file=sys.stderr)
    for message in warnings:
        print(f"WARNUNG: {message}")

    summary = ", ".join(
        f"{name}={counts.get(name, 0)} (sources={len(source_keys[name])})"
        for name in CLASS_POLICY
    )
    eligible = counts.get("direct_voc", 0) if not errors else 0
    eligible_sources = len(source_keys["direct_voc"]) if not errors else 0
    print(
        f"SUMMARY: {summary}; buyer_sources_total={len(buyer_source_keys)}; "
        f"voc_eligible={eligible}; voc_sources_eligible={eligible_sources}; "
        f"errors={len(errors)}; warnings={len(warnings)}"
    )
    return 1 if errors else 0


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(
        description="Buyer-Research-Inventar anzeigen oder Evidence Ledger validieren."
    )
    parser.add_argument("ledger", nargs="?", type=Path, help="Semikolon-CSV Evidence Ledger")
    parser.add_argument("--inventory", action="store_true", help="lokalen VoC-/Rohdatenstand anzeigen")
    return parser


def main() -> int:
    parser = build_parser()
    args = parser.parse_args()
    if args.inventory and args.ledger:
        parser.error("--inventory kann nicht zusammen mit einer Ledger-Datei verwendet werden")
    if not args.inventory and not args.ledger:
        parser.error("Ledger-Datei angeben oder --inventory verwenden")
    root = repo_root()
    return inventory(root) if args.inventory else validate_ledger(args.ledger, root)


if __name__ == "__main__":
    raise SystemExit(main())
