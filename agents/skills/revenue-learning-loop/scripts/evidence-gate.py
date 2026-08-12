#!/usr/bin/env python3
"""Validate aggregate pre/post revenue evidence and emit a directional label."""

from __future__ import annotations

import argparse
import csv
import json
import sys
from datetime import date
from pathlib import Path
from typing import NamedTuple


COUNT_FIELDS = ("sessions", "requests", "qualified", "progressed", "won")
REQUIRED_FIELDS = ("period", "start_date", "end_date", *COUNT_FIELDS)


class Metric(NamedTuple):
    numerator: str
    denominator: str


METRICS = {
    "request_rate": Metric("requests", "sessions"),
    "qualified_rate": Metric("qualified", "sessions"),
    "progressed_rate": Metric("progressed", "sessions"),
    "win_rate": Metric("won", "sessions"),
    "qualification_rate": Metric("qualified", "requests"),
    "progression_rate": Metric("progressed", "qualified"),
    "close_rate": Metric("won", "progressed"),
}
DEFAULT_GUARDRAILS = ("qualification_rate", "progression_rate")


class ContractError(ValueError):
    pass


def parse_date(value: str, label: str) -> date:
    try:
        return date.fromisoformat(value)
    except ValueError as error:
        raise ContractError(f"{label} must use YYYY-MM-DD: {value}") from error


def parse_count(value: str, label: str) -> int:
    try:
        number = int(value)
    except (TypeError, ValueError) as error:
        raise ContractError(f"{label} must be a non-negative integer: {value}") from error
    if number < 0:
        raise ContractError(f"{label} must be non-negative: {value}")
    return number


def load_periods(path: Path) -> dict[str, dict]:
    try:
        handle = path.open(newline="", encoding="utf-8-sig")
    except OSError as error:
        raise ContractError(f"cannot read CSV: {path}: {error}") from error

    with handle:
        reader = csv.DictReader(handle)
        missing = [field for field in REQUIRED_FIELDS if field not in (reader.fieldnames or [])]
        if missing:
            raise ContractError(f"missing CSV columns: {', '.join(missing)}")
        rows = list(reader)

    periods: dict[str, dict] = {}
    for index, row in enumerate(rows, start=2):
        period = (row.get("period") or "").strip().lower()
        if period not in {"before", "after"}:
            raise ContractError(f"row {index}: period must be before or after")
        if period in periods:
            raise ContractError(f"duplicate period row: {period}")
        start = parse_date((row.get("start_date") or "").strip(), f"row {index} start_date")
        end = parse_date((row.get("end_date") or "").strip(), f"row {index} end_date")
        if end < start:
            raise ContractError(f"row {index}: end_date precedes start_date")
        parsed = {field: parse_count(row.get(field, ""), f"row {index} {field}") for field in COUNT_FIELDS}
        if parsed["qualified"] > parsed["requests"]:
            raise ContractError(f"row {index}: qualified exceeds requests")
        if parsed["progressed"] > parsed["qualified"]:
            raise ContractError(f"row {index}: progressed exceeds qualified")
        if parsed["won"] > parsed["progressed"]:
            raise ContractError(f"row {index}: won exceeds progressed")
        periods[period] = {"start": start, "end": end, **parsed}

    if set(periods) != {"before", "after"}:
        raise ContractError("CSV must contain exactly one before and one after row")
    return periods


def metric_rate(period: dict, metric_name: str) -> float | None:
    metric = METRICS[metric_name]
    denominator = period[metric.denominator]
    if denominator == 0:
        return None
    return period[metric.numerator] / denominator


def direction(delta_pp: float, epsilon: float = 1e-9) -> str:
    if delta_pp > epsilon:
        return "up"
    if delta_pp < -epsilon:
        return "down"
    return "flat"


def evaluate(
    periods: dict[str, dict],
    release_date: date,
    min_sessions: int,
    min_requests: int,
    min_qualified: int,
    primary: str,
    min_primary_change_pp: float,
    guardrails: list[str],
    guardrail_tolerance_pp: float,
) -> dict:
    before = periods["before"]
    after = periods["after"]
    reasons: list[str] = []
    warnings: list[str] = []

    if before["end"] >= release_date:
        reasons.append("baseline window reaches or crosses the release date")
    if after["start"] < release_date:
        reasons.append("review window starts before the release date")
    elif after["start"] == release_date:
        warnings.append(
            "review window includes the release date; confirm full-day exposure or a declared normalization"
        )
    if before["end"] >= after["start"]:
        reasons.append("baseline and review windows overlap")

    before_days = (before["end"] - before["start"]).days + 1
    after_days = (after["end"] - after["start"]).days + 1
    if before_days != after_days:
        reasons.append(f"window lengths differ ({before_days} vs {after_days} days)")

    thresholds = {
        "sessions": min_sessions,
        "requests": min_requests,
        "qualified": min_qualified,
    }
    for period_name, period in periods.items():
        for field, threshold in thresholds.items():
            if period[field] < threshold:
                reasons.append(
                    f"{period_name} {field} below predeclared minimum "
                    f"({period[field]} < {threshold})"
                )
        if period["requests"] > period["sessions"]:
            warnings.append(
                f"{period_name} requests exceed sessions; verify distinct-request and session definitions"
            )

    selected_metrics = list(dict.fromkeys([primary, *guardrails]))
    metrics: dict[str, dict] = {}
    for metric_name in selected_metrics:
        before_rate = metric_rate(before, metric_name)
        after_rate = metric_rate(after, metric_name)
        if before_rate is None or after_rate is None:
            reasons.append(f"{metric_name} has a zero denominator")
            metrics[metric_name] = {
                "before": before_rate,
                "after": after_rate,
                "delta_pp": None,
                "direction": "unavailable",
            }
            continue
        delta_pp = (after_rate - before_rate) * 100
        metrics[metric_name] = {
            "before": before_rate,
            "after": after_rate,
            "delta_pp": delta_pp,
            "direction": direction(delta_pp),
        }

    gate = "ready" if not reasons else "insufficient"
    primary_result = metrics[primary]
    guardrail_regressions = [
        metric_name
        for metric_name in guardrails
        if metrics[metric_name]["delta_pp"] is not None
        and metrics[metric_name]["delta_pp"] < -guardrail_tolerance_pp
    ]

    if gate == "insufficient":
        decision = "insufficient"
    elif guardrail_regressions:
        decision = "revert_candidate"
    elif primary_result["delta_pp"] >= min_primary_change_pp:
        decision = "keep_candidate"
    elif primary_result["delta_pp"] <= -min_primary_change_pp:
        decision = "revert_candidate"
    else:
        decision = "inconclusive"

    return {
        "data_gate": gate,
        "structural_decision": decision,
        "release_date": release_date.isoformat(),
        "window_days": {"before": before_days, "after": after_days},
        "thresholds": thresholds,
        "primary": primary,
        "min_primary_change_pp": min_primary_change_pp,
        "guardrails": guardrails,
        "guardrail_tolerance_pp": guardrail_tolerance_pp,
        "guardrail_regressions": guardrail_regressions,
        "metrics": metrics,
        "reasons": reasons,
        "warnings": warnings,
        "causal_limit": (
            "Structural aggregate-data result only. Source freshness, mapping, stable definitions, "
            "and confounders still require signoff; pre/post evidence does not prove causality."
        ),
    }


def percent(value: float | None) -> str:
    return "n/a" if value is None else f"{value * 100:.2f}%"


def render_text(result: dict) -> None:
    labels = {
        "keep_candidate": "KEEP CANDIDATE",
        "revert_candidate": "REVERT CANDIDATE",
        "insufficient": "INSUFFICIENT",
        "inconclusive": "INCONCLUSIVE",
    }
    print(f"Structural decision: {labels[result['structural_decision']]}")
    print(f"Structural data gate: {result['data_gate'].upper()}")
    print(f"Primary outcome: {result['primary']}")
    print(f"Minimum meaningful primary change: {result['min_primary_change_pp']:.2f} pp")
    print()
    print("Metric                  Before      After       Delta")
    print("----------------------  ----------  ----------  ----------")
    for metric_name, values in result["metrics"].items():
        delta = values["delta_pp"]
        delta_label = "n/a" if delta is None else f"{delta:+.2f} pp"
        print(
            f"{metric_name:<22}  {percent(values['before']):>10}  "
            f"{percent(values['after']):>10}  {delta_label:>10}"
        )
    if result["reasons"]:
        print()
        print("Insufficient because:")
        for reason in result["reasons"]:
            print(f"- {reason}")
    if result["warnings"]:
        print()
        print("Warnings:")
        for warning in result["warnings"]:
            print(f"- {warning}")
    print()
    print(result["causal_limit"])


def non_negative_int(value: str) -> int:
    parsed = int(value)
    if parsed < 0:
        raise argparse.ArgumentTypeError("must be non-negative")
    return parsed


def non_negative_float(value: str) -> float:
    parsed = float(value)
    if parsed < 0:
        raise argparse.ArgumentTypeError("must be non-negative")
    return parsed


def positive_float(value: str) -> float:
    parsed = float(value)
    if parsed <= 0:
        raise argparse.ArgumentTypeError("must be greater than zero")
    return parsed


def parse_args(argv: list[str] | None = None) -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("csv_path", type=Path)
    parser.add_argument("--release-date", required=True, help="YYYY-MM-DD")
    parser.add_argument("--min-sessions", required=True, type=non_negative_int)
    parser.add_argument("--min-requests", default=0, type=non_negative_int)
    parser.add_argument("--min-qualified", default=0, type=non_negative_int)
    parser.add_argument("--primary", choices=tuple(METRICS), default="qualified_rate")
    parser.add_argument(
        "--min-primary-change-pp",
        required=True,
        type=positive_float,
        help="Predeclared smallest decision-relevant primary change in percentage points",
    )
    parser.add_argument(
        "--guardrail",
        action="append",
        choices=tuple(METRICS),
        help="Repeatable; defaults to qualification_rate and progression_rate",
    )
    parser.add_argument(
        "--guardrail-tolerance-pp",
        default=0.0,
        type=non_negative_float,
        help="Predeclared allowed guardrail decline in percentage points",
    )
    parser.add_argument("--format", choices=("text", "json"), default="text")
    return parser.parse_args(argv)


def main(argv: list[str] | None = None) -> int:
    args = parse_args(argv)
    try:
        release = parse_date(args.release_date, "release date")
        periods = load_periods(args.csv_path)
        result = evaluate(
            periods=periods,
            release_date=release,
            min_sessions=args.min_sessions,
            min_requests=args.min_requests,
            min_qualified=args.min_qualified,
            primary=args.primary,
            min_primary_change_pp=args.min_primary_change_pp,
            guardrails=args.guardrail or list(DEFAULT_GUARDRAILS),
            guardrail_tolerance_pp=args.guardrail_tolerance_pp,
        )
    except (ContractError, OSError) as error:
        print(f"evidence gate error: {error}", file=sys.stderr)
        return 2

    if args.format == "json":
        print(json.dumps(result, ensure_ascii=False, indent=2))
    else:
        render_text(result)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
