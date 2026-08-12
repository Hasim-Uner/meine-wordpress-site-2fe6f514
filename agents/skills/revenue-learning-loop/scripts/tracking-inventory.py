#!/usr/bin/env python3
"""Inventory repo-owned tracking hooks without claiming external collection."""

from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
from collections import Counter, defaultdict
from pathlib import Path
from typing import Iterable


SOURCE_SUFFIXES = {".php", ".js", ".html"}
EXCLUDED_DIRS = {".git", ".build", "dist", "node_modules", "vendor"}
TRACK_ATTR_RE = re.compile(
    r"\bdata-track-([a-z0-9_-]+)\s*=\s*([\"'])(.*?)\2",
    re.IGNORECASE | re.DOTALL,
)
TAG_START_RE = re.compile(r"<[A-Za-z][A-Za-z0-9:_-]*(?:\s|>)")
ACTION_NAME_RE = re.compile(r"^[a-z][a-z0-9_]*$")
EMITTER_RE = re.compile(r"(?:window\.)?dataLayer\s*\.\s*push\s*\(")
GTAG_RE = re.compile(r"(?:window\.)?gtag\s*\(")
LISTENER_RE = re.compile(
    r"(?:closest|querySelector(?:All)?)\s*\(\s*['\"]\[data-track-action\]",
)
LITERAL_EVENT_RE = re.compile(r"\bevent\s*:\s*['\"]([a-z][a-z0-9_]*)['\"]")
DYNAMIC_MARKERS = ("<?", "?>", "$", "{{", "}}", "${", "%s")


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


def is_source(path: Path) -> bool:
    return path.suffix.lower() in SOURCE_SUFFIXES and not any(
        part in EXCLUDED_DIRS for part in path.parts
    )


def within_root(path: Path, root: Path) -> bool:
    try:
        path.relative_to(root)
        return True
    except ValueError:
        return False


def collect_files(root: Path, requested: Iterable[str] | None = None) -> list[Path]:
    root = root.resolve()
    raw_paths = list(requested or [])
    if not raw_paths:
        raw_paths = ["blocksy-child"]

    files: set[Path] = set()
    for raw in raw_paths:
        candidate = (root / raw).resolve() if not Path(raw).is_absolute() else Path(raw).resolve()
        if not within_root(candidate, root):
            raise ValueError(f"path escapes repo root: {raw}")
        if not candidate.exists():
            raise ValueError(f"path does not exist: {raw}")
        if candidate.is_file():
            if is_source(candidate):
                files.add(candidate)
            continue
        for path in candidate.rglob("*"):
            if path.is_file() and is_source(path):
                files.add(path.resolve())
    return sorted(files)


def line_number(text: str, offset: int) -> int:
    return text.count("\n", 0, offset) + 1


def enclosing_tag(text: str, start: int, end: int) -> str:
    candidates = list(TAG_START_RE.finditer(text, 0, start + 1))
    if not candidates:
        return ""
    tag_start = candidates[-1].start()

    # PHP close tokens inside an opening tag contain `>`, but do not close the
    # HTML tag. Reject only a real HTML end between the candidate and action.
    for offset in range(tag_start, start):
        if text[offset] == ">" and (offset == 0 or text[offset - 1] != "?"):
            return ""

    tag_end = end
    while True:
        tag_end = text.find(">", tag_end)
        if tag_end < 0:
            return ""
        if tag_end == 0 or text[tag_end - 1] != "?":
            return text[tag_start : tag_end + 1]
        tag_end += 1


def attributes(fragment: str) -> dict[str, str]:
    return {match.group(1).lower(): match.group(3).strip() for match in TRACK_ATTR_RE.finditer(fragment)}


def is_dynamic(value: str) -> bool:
    return any(marker in value for marker in DYNAMIC_MARKERS)


def build_inventory(root: Path, requested: Iterable[str] | None = None) -> dict:
    root = root.resolve()
    files = collect_files(root, requested)
    action_rows: list[dict] = []
    all_attribute_count = 0
    emitters: list[dict] = []
    listeners: list[dict] = []
    emitted_event_names: Counter[str] = Counter()

    for path in files:
        try:
            text = path.read_text(encoding="utf-8")
        except UnicodeDecodeError:
            continue
        relative = path.relative_to(root).as_posix()
        matches = list(TRACK_ATTR_RE.finditer(text))
        all_attribute_count += len(matches)

        for match in matches:
            if match.group(1).lower() != "action":
                continue
            value = match.group(3).strip()
            tag_attrs = attributes(enclosing_tag(text, match.start(), match.end()))
            dynamic = is_dynamic(value)
            action_rows.append(
                {
                    "action": value,
                    "category": tag_attrs.get("category", ""),
                    "section": tag_attrs.get("section", ""),
                    "dynamic": dynamic,
                    "path": relative,
                    "line": line_number(text, match.start()),
                }
            )

        emitter_count = len(EMITTER_RE.findall(text))
        gtag_count = len(GTAG_RE.findall(text))
        if emitter_count or gtag_count:
            emitters.append(
                {
                    "path": relative,
                    "data_layer_pushes": emitter_count,
                    "gtag_calls": gtag_count,
                }
            )
            emitted_event_names.update(LITERAL_EVENT_RE.findall(text))

        listener_count = len(LISTENER_RE.findall(text))
        if listener_count:
            listeners.append({"path": relative, "selectors": listener_count})

    literal_rows = [row for row in action_rows if not row["dynamic"]]
    blank_rows = [row for row in literal_rows if not row["action"]]
    invalid_rows = [
        row
        for row in literal_rows
        if row["action"] and not ACTION_NAME_RE.fullmatch(row["action"])
    ]
    missing_categories = [row for row in action_rows if not row["category"]]
    missing_sections = [row for row in action_rows if not row["section"]]

    categories_by_action: defaultdict[str, set[str]] = defaultdict(set)
    for row in literal_rows:
        if row["action"] and row["category"] and not is_dynamic(row["category"]):
            categories_by_action[row["action"]].add(row["category"])
    collisions = {
        action: sorted(categories)
        for action, categories in categories_by_action.items()
        if len(categories) > 1
    }

    action_counts = Counter(row["action"] for row in literal_rows if row["action"])
    category_counts = Counter(
        row["category"]
        for row in action_rows
        if row["category"] and not is_dynamic(row["category"])
    )
    section_counts = Counter(
        row["section"]
        for row in action_rows
        if row["section"] and not is_dynamic(row["section"])
    )

    return {
        "scope": [str(item) for item in (requested or ["blocksy-child"])],
        "files_scanned": len(files),
        "tracking_attributes": all_attribute_count,
        "action_occurrences": len(action_rows),
        "literal_actions": len(action_counts),
        "dynamic_action_occurrences": sum(1 for row in action_rows if row["dynamic"]),
        "categories": dict(sorted(category_counts.items())),
        "sections": dict(sorted(section_counts.items())),
        "actions": dict(sorted(action_counts.items())),
        "emitters": emitters,
        "listeners": listeners,
        "literal_emitted_events": dict(sorted(emitted_event_names.items())),
        "findings": {
            "hard": {
                "blank_actions": blank_rows,
                "invalid_action_names": invalid_rows,
            },
            "advisory": {
                "missing_same_tag_category": missing_categories,
                "missing_same_tag_section": missing_sections,
                "category_collisions": collisions,
            },
        },
        "boundary": (
            "Repo hooks and emitters were inventoried; external GTM/GA4/Koko/consent "
            "mapping remains unverified."
        ),
    }


def hard_finding_count(inventory: dict) -> int:
    hard = inventory["findings"]["hard"]
    return sum(len(rows) for rows in hard.values())


def print_locations(rows: list[dict], limit: int = 12) -> None:
    for row in rows[:limit]:
        print(f"  - {row['path']}:{row['line']} — {row['action'] or '<blank>'}")
    if len(rows) > limit:
        print(f"  - … {len(rows) - limit} more")


def render_markdown(inventory: dict) -> None:
    hard = inventory["findings"]["hard"]
    advisory = inventory["findings"]["advisory"]
    push_count = sum(item["data_layer_pushes"] for item in inventory["emitters"])
    gtag_count = sum(item["gtag_calls"] for item in inventory["emitters"])

    print("# Tracking Contract Inventory")
    print()
    print(f"- Scope: `{', '.join(inventory['scope'])}`")
    print(f"- Source files scanned: {inventory['files_scanned']}")
    print(f"- `data-track-*` attributes: {inventory['tracking_attributes']}")
    print(f"- Action occurrences: {inventory['action_occurrences']}")
    print(f"- Unique literal actions: {inventory['literal_actions']}")
    print(f"- Dynamic action occurrences: {inventory['dynamic_action_occurrences']}")
    print(f"- `dataLayer.push` calls: {push_count} in {len(inventory['emitters'])} files")
    print(f"- `gtag` calls: {gtag_count}")
    print(f"- Generic `[data-track-action]` listeners: {len(inventory['listeners'])} files")
    print()
    print("## Hard Findings")
    print()
    if hard_finding_count(inventory) == 0:
        print("None.")
    else:
        for label, rows in hard.items():
            if not rows:
                continue
            print(f"### {label.replace('_', ' ').title()} ({len(rows)})")
            print()
            print_locations(rows)
            print()

    print("## Advisories")
    print()
    print(
        "Missing `data-track-section` on the same element can be valid when the "
        "section is inherited from an ancestor; verify it in the runtime mapper."
    )
    print()
    print(f"- Missing same-tag category: {len(advisory['missing_same_tag_category'])}")
    print(f"- Missing same-tag section: {len(advisory['missing_same_tag_section'])}")
    print(f"- Actions mapped to multiple literal categories: {len(advisory['category_collisions'])}")
    if advisory["category_collisions"]:
        for action, categories in list(advisory["category_collisions"].items())[:12]:
            print(f"  - `{action}` → {', '.join(f'`{item}`' for item in categories)}")
    print()
    print("## Event Emitters")
    print()
    if inventory["emitters"]:
        for item in inventory["emitters"]:
            print(
                f"- `{item['path']}` — dataLayer: {item['data_layer_pushes']}, "
                f"gtag: {item['gtag_calls']}"
            )
    else:
        print("No local emitters found; hooks may rely entirely on external GTM.")
    print()
    print(f"> {inventory['boundary']}")


def parse_args(argv: list[str] | None = None) -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--root", help="Repository root; defaults to git root")
    parser.add_argument(
        "--path",
        action="append",
        dest="paths",
        help="Repo-relative source file or directory; repeatable",
    )
    parser.add_argument("--format", choices=("markdown", "json"), default="markdown")
    parser.add_argument(
        "--strict",
        action="store_true",
        help="Exit 1 for blank or non-snake-case literal action names",
    )
    return parser.parse_args(argv)


def main(argv: list[str] | None = None) -> int:
    args = parse_args(argv)
    try:
        root = repo_root(args.root)
        inventory = build_inventory(root, args.paths)
    except (OSError, ValueError) as error:
        print(f"tracking inventory error: {error}", file=sys.stderr)
        return 2

    if args.format == "json":
        print(json.dumps(inventory, ensure_ascii=False, indent=2))
    else:
        render_markdown(inventory)

    if args.strict and hard_finding_count(inventory):
        return 1
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
