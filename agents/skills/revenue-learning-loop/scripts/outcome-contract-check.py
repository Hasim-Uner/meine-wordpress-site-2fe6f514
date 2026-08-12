#!/usr/bin/env python3
"""Check whether CRM qualification can reach route-level revenue decisions."""

from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
from pathlib import Path


CRM_PATH = Path("blocksy-child/inc/review-crm.php")
LEADS_PATH = Path("blocksy-child/inc/seo-cockpit/seo-cockpit-leads.php")
COMMAND_PATH = Path("blocksy-child/inc/seo-cockpit/seo-cockpit-command-center.php")
QUALIFICATION_META = "_nexus_review_qualification_status"


def repo_root(explicit: str | None = None) -> Path:
    if explicit:
        root = Path(explicit).expanduser().resolve()
    else:
        result = subprocess.run(
            ["git", "rev-parse", "--show-toplevel"],
            check=False,
            capture_output=True,
            text=True,
        )
        if result.returncode != 0:
            raise ValueError("not inside a git repository; pass --root")
        root = Path(result.stdout.strip()).resolve()
    if not root.is_dir():
        raise ValueError(f"repo root does not exist: {root}")
    return root


def read_required(root: Path, relative: Path) -> str:
    path = (root / relative).resolve()
    try:
        path.relative_to(root)
    except ValueError as error:
        raise ValueError(f"path escapes repo root: {relative}") from error
    if not path.is_file():
        raise ValueError(f"required file missing: {relative}")
    try:
        return path.read_text(encoding="utf-8")
    except OSError as error:
        raise ValueError(f"cannot read {relative}: {error}") from error


def has_qualification_bucket(text: str) -> bool:
    return bool(re.search(r"['\"]qualified['\"]\s*=>\s*0", text))


def has_qualification_increment(text: str) -> bool:
    patterns = (
        r"\[['\"]qualified['\"]\]\s*\+=\s*1",
        r"\[['\"]qualified['\"]\]\+\+",
        r"\+\+\s*\$[^;\n]*\[['\"]qualified['\"]\]",
    )
    return any(re.search(pattern, text) for pattern in patterns)


def accepted_qualification_values(text: str) -> list[str]:
    values: set[str] = set()
    for match in re.finditer(
        r"in_array\s*\(\s*\$qualification\s*,\s*\[([^\]]*)\]",
        text,
        re.DOTALL,
    ):
        values.update(re.findall(r"['\"]([a-z][a-z0-9_]*)['\"]", match.group(1)))
    for match in re.finditer(r"['\"]([a-z][a-z0-9_]*)['\"]\s*===\s*\$qualification", text):
        values.add(match.group(1))
    return sorted(values)


def crm_returned_statuses(text: str) -> list[str]:
    start = text.find("function nexus_compute_lead_qualification")
    if start >= 0:
        end = text.find("\n/**", start)
        text = text[start:] if end < 0 else text[start:end]
    return sorted(
        set(
            re.findall(
                r"['\"]status['\"]\s*=>\s*['\"]([a-z][a-z0-9_]*)['\"]",
                text,
            )
        )
    )


def build_report(root: Path) -> dict:
    root = root.resolve()
    crm = read_required(root, CRM_PATH)
    leads = read_required(root, LEADS_PATH)
    command = read_required(root, COMMAND_PATH)

    crm_statuses = crm_returned_statuses(crm)
    command_values = accepted_qualification_values(command)
    checks = [
        {
            "id": "crm_persists_qualification",
            "ok": QUALIFICATION_META in crm and "update_post_meta" in crm,
            "detail": f"CRM writes {QUALIFICATION_META}",
        },
        {
            "id": "crm_enum_is_explicit",
            "ok": {"qualified", "nurture"}.issubset(set(crm_statuses)),
            "detail": f"CRM returned statuses: {', '.join(crm_statuses) or 'none detected'}",
        },
        {
            "id": "cockpit_reads_qualification",
            "ok": QUALIFICATION_META in leads,
            "detail": f"Lead aggregation reads {QUALIFICATION_META}",
        },
        {
            "id": "cockpit_has_qualified_bucket",
            "ok": has_qualification_bucket(leads),
            "detail": "Lead aggregation exposes a qualified count",
        },
        {
            "id": "cockpit_increments_qualified",
            "ok": has_qualification_increment(leads),
            "detail": "Lead aggregation increments the qualified count",
        },
        {
            "id": "command_center_enum_matches_crm",
            "ok": "qualified" in command_values and not ({"green", "yellow"} & set(command_values)),
            "detail": (
                "Command Center qualification values: "
                f"{', '.join(command_values) or 'none detected'}; expected qualified/nurture contract"
            ),
        },
    ]

    failures = [check for check in checks if not check["ok"]]
    return {
        "ready": not failures,
        "checks": checks,
        "failure_count": len(failures),
        "decision_metric_gate": (
            "qualified_rate may be sourced from the repo-owned outcome layer"
            if not failures
            else "qualified_rate is not decision-ready from the repo-owned outcome layer"
        ),
        "manual_gate": (
            "Even when code checks pass, verify CRM status freshness, attribution, external event "
            "mapping, and whether progressed/won semantics are qualification-scoped."
        ),
    }


def render_text(report: dict) -> None:
    print("# Revenue Outcome Contract")
    print()
    for check in report["checks"]:
        label = "PASS" if check["ok"] else "FAIL"
        print(f"[{label}] {check['id']}: {check['detail']}")
    print()
    print(f"Decision metric gate: {'READY' if report['ready'] else 'BLOCKED'}")
    print(report["decision_metric_gate"] + ".")
    print()
    print(f"> {report['manual_gate']}")


def parse_args(argv: list[str] | None = None) -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--root", help="Repository root; defaults to git root")
    parser.add_argument("--format", choices=("text", "json"), default="text")
    return parser.parse_args(argv)


def main(argv: list[str] | None = None) -> int:
    args = parse_args(argv)
    try:
        report = build_report(repo_root(args.root))
    except (OSError, ValueError) as error:
        print(f"outcome contract error: {error}", file=sys.stderr)
        return 2

    if args.format == "json":
        print(json.dumps(report, ensure_ascii=False, indent=2))
    else:
        render_text(report)
    return 0 if report["ready"] else 1


if __name__ == "__main__":
    raise SystemExit(main())
