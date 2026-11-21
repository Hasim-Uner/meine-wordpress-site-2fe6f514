#!/usr/bin/env python3
"""Compress commits plus aggregate outcome/tracking CSVs into a short retainer update.

Reports only what the inputs contain. It never estimates, fills gaps or claims
causality. Exit 0 = digest printed, 2 = invalid input.
"""

from __future__ import annotations

import argparse
import csv
import re
import subprocess
import sys
from datetime import date, timedelta
from pathlib import Path


CSV_HEADER = ["metric", "window_start", "window_end", "value", "denominator", "source"]

# metric -> (sort tier, label, is_rate). Rates need a denominator; ctr = value (clicks) / denominator (impressions).
METRICS = {
    "won": (1, "lagging", False),
    "progressed": (2, "lagging", False),
    "qualified": (3, "lagging", False),
    "inquiries": (4, "lagging", False),
    "sessions": (5, "leading", False),
    "clicks": (6, "leading", False),
    "impressions": (7, "leading", False),
    "form_starts": (8, "leading", False),
    "ctr": (9, "leading", True),
}

# First entry wins ties. Order is also the output order; "Sonstiges" is last.
GROUPS = [
    ("blocksy-child/", "Website (wirksam erst nach Deploy)"),
    ("content/", "Inhalte (Entwurf)"),
    ("docs/", "Dokumentation"),
    ("seo-research/", "SEO-Daten"),
    ("agents/", "Werkzeuge und QA"),
    ("scripts/", "Werkzeuge und QA"),
    (".github/", "Werkzeuge und QA"),
]
OTHER = "Sonstiges"
GROUP_ORDER = list(dict.fromkeys([label for _, label in GROUPS] + [OTHER]))

MAX_SUBJECTS = 3
MAX_DECISIONS = 3
SUBJECT_WIDTH = 60

PREFIX = re.compile(
    r"^(feat|fix|chore|docs|refactor|style|test|tests|perf|build|ci|revert)(\([^)]*\))?!?:\s*",
    re.IGNORECASE,
)
RISK = re.compile(r"\b(revert|hotfix|rollback|roll back)\b", re.IGNORECASE)
EMAIL = re.compile(r"[^\s@]+@[^\s@]+")
# Dates (ISO or German) are matched first so they are never taken for phone numbers.
PHONE_OR_DATE = re.compile(
    r"(?P<date>\d{4}-\d{2}-\d{2}|\d{1,2}\.\d{1,2}\.(?:\d{4}|\d{2})(?!\d))"
    r"|(?P<phone>\+\d[\d\s().\/-]{6,}|\d[\d\s().\/-]{6,}\d)"
)
# Extending the allowlist is a code change on purpose: every new system is a privacy decision.
SOURCE_SYSTEMS = ("crm", "ga4", "gsc", "ads", "tracking", "cockpit", "calcom", "sheet")
SOURCE_DETAIL = re.compile(r"[a-z0-9][a-z0-9_.-]*")
LONG_DIGITS = re.compile(r"\d{6,}")
REDACTED = "[entfernt]"
MATURITY_DAYS = 30
MATURING = ("won", "progressed")
PIPELINE = ("qualified", "progressed", "won")


class InputError(ValueError):
    pass


def parse_date(value: str, label: str) -> date:
    try:
        return date.fromisoformat(value)
    except ValueError as error:
        raise InputError(f"{label} must use YYYY-MM-DD: {value}") from error


def _phone_match(match: re.Match) -> bool:
    return bool(match.group("phone")) and sum(ch.isdigit() for ch in match.group("phone")) >= 7


def has_phone(text: str) -> bool:
    return any(_phone_match(match) for match in PHONE_OR_DATE.finditer(text))


def looks_personal(text: str) -> bool:
    return bool(EMAIL.search(text)) or has_phone(text)


def redact(text: str) -> str:
    text = EMAIL.sub(REDACTED, text)
    return PHONE_OR_DATE.sub(lambda match: REDACTED if _phone_match(match) else match.group(0), text)


def valid_source(value: str) -> bool:
    system, colon, detail = value.partition(":")
    if system not in SOURCE_SYSTEMS or LONG_DIGITS.search(value):
        return False
    return not colon or bool(SOURCE_DETAIL.fullmatch(detail))


def read_measurements(path: Path) -> list[dict]:
    try:
        handle = path.open(newline="", encoding="utf-8-sig")
    except OSError as error:
        raise InputError(f"{path}: cannot read: {error}") from error
    with handle:
        reader = csv.reader(handle, delimiter=";")
        header = next(reader, None)
        if header is None:
            raise InputError(f"{path}: empty file")
        if [cell.strip() for cell in header] != CSV_HEADER:
            raise InputError(f"{path}: header must be exactly {';'.join(CSV_HEADER)}")
        rows = []
        for number, cells in enumerate(reader, start=2):
            if not any(cell.strip() for cell in cells):
                continue
            where = f"{path}: row {number}"
            if len(cells) != len(CSV_HEADER):
                raise InputError(f"{where}: expected {len(CSV_HEADER)} columns, got {len(cells)}")
            row = dict(zip(CSV_HEADER, (cell.strip() for cell in cells)))
            if row["source"] and not valid_source(row["source"]):
                raise InputError(
                    f"{where}: source must be <system> or <system>:<detail> with system in "
                    f"{', '.join(SOURCE_SYSTEMS)}, detail matching [a-z0-9][a-z0-9_.-]* "
                    f"and no run of 6 or more digits (e.g. crm:bitrix24, ga4)"
                )
            for field in ("metric", "value", "denominator"):
                if looks_personal(row[field]):
                    raise InputError(f"{where}: {field} looks like an email or phone number")
            rows.append(validate_row(row, where))
    return rows


def parse_count(text: str, where: str, label: str, minimum: int) -> int:
    if not re.fullmatch(r"\d+", text):
        raise InputError(f"{where}: {label} must be a whole number")
    number = int(text)
    if number < minimum:
        raise InputError(f"{where}: {label} must be >= {minimum}")
    return number


def validate_row(row: dict, where: str) -> dict:
    metric = row["metric"].lower()
    if metric not in METRICS:
        raise InputError(f"{where}: unknown metric '{row['metric']}' (known: {', '.join(METRICS)})")
    start = parse_date(row["window_start"], f"{where}: window_start")
    end = parse_date(row["window_end"], f"{where}: window_end")
    if end < start:
        raise InputError(f"{where}: window_end precedes window_start")
    value = parse_count(row["value"], where, "value", 0)
    denominator = None
    if row["denominator"]:
        denominator = parse_count(row["denominator"], where, "denominator", 1)
    if METRICS[metric][2] and denominator is None:
        raise InputError(f"{where}: rate metric '{metric}' needs a denominator")
    if METRICS[metric][2] and value > denominator:
        raise InputError(f"{where}: value exceeds denominator for rate metric '{metric}'")
    if not row["source"]:
        raise InputError(f"{where}: source is required")
    return {
        "metric": metric,
        "start": start,
        "end": end,
        "value": value,
        "denominator": denominator,
        "source": row["source"],
    }


def read_lines(path: Path, label: str) -> list[str]:
    try:
        text = path.read_text(encoding="utf-8")
    except OSError as error:
        raise InputError(f"{path}: cannot read: {error}") from error
    lines = [line.strip() for line in text.splitlines() if line.strip()]
    for number, line in enumerate(lines, start=1):
        if looks_personal(line):
            raise InputError(f"{path}: {label} entry {number} looks like an email address or phone number")
    return lines


def git(repo: str, *args: str) -> str:
    result = subprocess.run(["git", "-C", repo, *args], capture_output=True, text=True)
    if result.returncode != 0:
        message = (result.stderr.strip().splitlines() or ["git failed"])[0]
        raise InputError(f"git {args[0]}: {message}")
    return result.stdout


def read_commits(repo: str, since: date | None, until: date | None, revision_range: str | None) -> list[dict]:
    command = [
        "log", "--no-merges", "--date=short", "--pretty=format:%H%x1f%ad%x1f%s", "--name-only",
    ]
    if revision_range:
        command.append(revision_range)
    else:
        command += [f"--since={since.isoformat()} 00:00:00", f"--until={until.isoformat()} 23:59:59"]
    commits: list[dict] = []
    for line in git(repo, *command, "--").splitlines():
        if "\x1f" in line:
            sha, day, subject = line.split("\x1f", 2)
            commits.append({"sha": sha, "date": day, "subject": subject, "files": []})
        elif line.strip() and commits:
            commits[-1]["files"].append(line.strip())
    return commits


def group_of(files: list[str]) -> str:
    counts: dict[str, int] = {}
    for name in files:
        for prefix, label in GROUPS:
            if name.startswith(prefix):
                counts[label] = counts.get(label, 0) + 1
                break
        else:
            counts[OTHER] = counts.get(OTHER, 0) + 1
    if not counts:
        return OTHER
    return max(counts, key=lambda label: (counts[label], -GROUP_ORDER.index(label)))


def shorten(text: str) -> str:
    return text if len(text) <= SUBJECT_WIDTH else text[: SUBJECT_WIDTH - 1].rstrip() + "…"


def clean_subject(subject: str) -> str:
    return shorten(PREFIX.sub("", subject.strip()))


def with_more(items: list[str], limit: int) -> str:
    shown = "; ".join(items[:limit])
    extra = len(items) - limit
    return f"{shown}; +{extra} weitere" if extra > 0 else shown


def window_text(row: dict) -> str:
    text = f"{row['start']}..{row['end']}: {row['value']}"
    if row["denominator"] is not None:
        text += f" von {row['denominator']}"
        if METRICS[row["metric"]][2]:
            text += f" ({100 * row['value'] / row['denominator']:.1f}%)"
    return text


def delta_text(metric: str, rows: list[dict]) -> str:
    if len(rows) == 1:
        return ""
    if len(rows) > 2:
        return "kein Vergleich (mehr als zwei Fenster)"
    first, second = sorted(rows, key=lambda row: row["start"])
    if first["source"] != second["source"]:
        return "kein Vergleich (Quelle gewechselt)"
    if first["end"] >= second["start"]:
        return "kein Vergleich (Fenster überlappen)"
    if (first["end"] - first["start"]) != (second["end"] - second["start"]):
        return "kein Vergleich (Fenster ungleich)"
    if METRICS[metric][2]:
        change = 100 * second["value"] / second["denominator"] - 100 * first["value"] / first["denominator"]
        return f"Delta {change:+.1f} pp"
    return f"Delta {second['value'] - first['value']:+d}"


def result_lines(rows: list[dict], until: date | None = None) -> list[str]:
    by_metric: dict[str, list[dict]] = {}
    for row in rows:
        by_metric.setdefault(row["metric"], []).append(row)
    lines = []
    for metric in sorted(by_metric, key=lambda name: METRICS[name][0]):
        group = sorted(by_metric[metric], key=lambda row: row["start"])
        text = f"{metric} ({METRICS[metric][1]}) " + " -> ".join(window_text(row) for row in group)
        delta = delta_text(metric, group)
        if delta:
            text += f"; {delta}"
        if until and metric in MATURING and any(row["end"] >= until - timedelta(days=MATURITY_DAYS) for row in group):
            text += f"; {metric} noch nicht ausgereift"
        lines.append(text)
    if all(METRICS[name][1] == "leading" for name in by_metric):
        lines.append("Nur Frühindikatoren. Kein Pipeline-Beleg.")
    elif not any(name in by_metric for name in PIPELINE):
        lines.append("Kein Pipeline-Beleg (qualified/progressed/won fehlen).")
    sources = sorted({row["source"] for row in rows})
    lines.append("Quellen: " + "; ".join(sources))
    if until:
        lines.append(f"Stand: {until.isoformat()} (Referenzdatum für Reife)")
    return lines


def add_duplicates_check(rows: list[dict]) -> None:
    seen = set()
    for row in rows:
        key = (row["metric"], row["start"], row["end"])
        if key in seen:
            raise InputError(f"duplicate window for metric '{row['metric']}': {row['start']}..{row['end']}")
        seen.add(key)


def delivered_lines(commits: list[dict], redacted: int = 0) -> list[str]:
    if not commits:
        return ["Keine Commits im Fenster."]
    grouped: dict[str, list[str]] = {}
    for commit in commits:
        grouped.setdefault(group_of(commit["files"]), []).append(clean_subject(commit["subject"]))
    lines = []
    for label in GROUP_ORDER:
        subjects = grouped.get(label)
        if subjects:
            noun = "Commit" if len(subjects) == 1 else "Commits"
            lines.append(f"{label}: {len(subjects)} {noun}: {with_more(subjects, MAX_SUBJECTS)}")
    if redacted:
        lines.append(f"Betreffe bereinigt: {redacted}")
    return lines


def risk_lines(commits: list[dict]) -> list[str]:
    risky = [commit["subject"].strip() for commit in commits if RISK.search(commit["subject"])]
    if not risky:
        return ["Keine Reverts im Fenster."]
    shortened = [shorten(subject) for subject in risky]
    return [f"{len(risky)} Revert/Hotfix/Rollback: {with_more(shortened, MAX_SUBJECTS)}"]


def render(header: str, blocks: list[tuple[str, list[str], bool]], markdown: bool) -> str:
    out = [f"# {header}" if markdown else header]
    for title, lines, inline in blocks:
        name = f"**{title}:**" if markdown else f"{title}:"
        if inline:
            out.append(f"{name} {lines[0]}")
            continue
        out.append(name)
        out.extend(f"- {line}" for line in lines)
    return "\n".join(out)


def range_end_date(repo: str, revision_range: str) -> date:
    """Committer date of the range's end commit; an empty end means HEAD."""
    end = re.split(r"\.{2,3}", revision_range, maxsplit=1)[1] or "HEAD"
    return parse_date(git(repo, "log", "-1", "--format=%cs", end, "--").strip(), "range end commit date")


def build(args: argparse.Namespace) -> str:
    repo = args.repo or git(".", "rev-parse", "--show-toplevel").strip()
    since = until = None
    if args.range:
        if args.since or args.until:
            raise InputError("use either --since/--until or --range")
        if args.range.startswith("-") or ".." not in args.range:
            raise InputError("--range must look like A..B")
        window = args.range
        until = range_end_date(repo, args.range)
    else:
        if not (args.since and args.until):
            raise InputError("give --since and --until, or --range")
        since = parse_date(args.since, "--since")
        until = parse_date(args.until, "--until")
        if until < since:
            raise InputError("--until precedes --since")
        window = f"{since} bis {until}"

    rows: list[dict] = []
    for option in (args.outcomes, args.tracking):
        if option:
            rows += read_measurements(Path(option))
    add_duplicates_check(rows)
    decisions = read_lines(Path(args.decisions), "decision") if args.decisions else []
    next_step = (args.next or "").strip()
    if looks_personal(next_step):
        raise InputError("--next looks like it contains an email address or phone number")

    commits = read_commits(repo, since, until, args.range)
    redacted = 0
    for commit in commits:
        clean = redact(commit["subject"])
        if clean != commit["subject"]:
            commit["subject"] = clean
            redacted += 1
    head = git(repo, "rev-parse", "--short", "HEAD").strip()

    blocks: list[tuple[str, list[str], bool]] = []
    if rows:
        blocks.append(("Ergebnis", result_lines(rows, until), False))
    else:
        blocks.append(("Ergebnis", ["in diesem Fenster nicht gemessen."], True))
    blocks.append(("Geliefert", delivered_lines(commits, redacted), False))
    blocks.append(("Risiken", risk_lines(commits), False))
    if decisions:
        shown = decisions[:MAX_DECISIONS]
        if len(decisions) > MAX_DECISIONS:
            shown.append(f"+{len(decisions) - MAX_DECISIONS} weitere")
        blocks.append(("Entscheidung nötig", shown, False))
    if next_step:
        blocks.append(("Nächster Schritt", [next_step], True))
    return render(f"Retainer-Update {window} · Stand {head}", blocks, args.md)


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--since", help="YYYY-MM-DD, inclusive commit date")
    parser.add_argument("--until", help="YYYY-MM-DD, inclusive commit date")
    parser.add_argument("--range", help="git revision range A..B instead of dates")
    parser.add_argument("--repo", help="repository path (default: git toplevel of cwd)")
    parser.add_argument("--outcomes", help="semicolon CSV: " + ";".join(CSV_HEADER))
    parser.add_argument("--tracking", help="semicolon CSV with the same header")
    parser.add_argument("--decisions", help="text file, one decision request per line")
    parser.add_argument("--next", help="one next step")
    parser.add_argument("--md", action="store_true", help="markdown output")
    args = parser.parse_args(argv)
    try:
        print(build(args))
    except InputError as error:
        print(f"error: {error}", file=sys.stderr)
        return 2
    return 0


if __name__ == "__main__":
    sys.exit(main())
