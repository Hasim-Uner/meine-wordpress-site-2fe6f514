#!/usr/bin/env python3
"""Inventory and guard legacy --nx-* CSS consumers.

`design-system.css` is a compatibility provider, not the design target for new
surfaces. This script inventories every stylesheet that still consumes an
`--nx-*` custom property. Once `scripts/baselines/legacy-nx-consumers.txt`
exists, the inventory becomes a shrink-only contract:

- a new consumer fails the build;
- a stale baseline entry fails the build and must be removed;
- existing consumers remain visible as migration debt.

The provider itself is reported separately and is not part of the consumer
baseline.
"""

from __future__ import annotations

import re
import sys
from collections import Counter
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CSS_DIR = ROOT / "blocksy-child" / "assets" / "css"
STYLE_CSS = ROOT / "blocksy-child" / "style.css"
PROVIDER = CSS_DIR / "design-system.css"
BASELINE = ROOT / "scripts" / "baselines" / "legacy-nx-consumers.txt"
NX_USE_RE = re.compile(r"var\(\s*(--nx-[a-zA-Z0-9_-]+)")


def rel(path: Path) -> str:
    return path.relative_to(ROOT).as_posix()


def css_files() -> list[Path]:
    files = sorted(CSS_DIR.glob("*.css"))
    if STYLE_CSS.is_file():
        files.append(STYLE_CSS)
    return files


def read_baseline() -> set[str] | None:
    if not BASELINE.is_file():
        return None
    entries: set[str] = set()
    for raw in BASELINE.read_text(encoding="utf-8").splitlines():
        line = raw.strip()
        if not line or line.startswith("#"):
            continue
        entries.add(line)
    return entries


def main() -> int:
    consumers: dict[str, Counter[str]] = {}

    for path in css_files():
        if path == PROVIDER:
            continue
        text = path.read_text(encoding="utf-8")
        tokens = NX_USE_RE.findall(text)
        if tokens:
            consumers[rel(path)] = Counter(tokens)

    print(f"Legacy --nx-* consumer files: {len(consumers)}")
    for path in sorted(consumers):
        token_counts = consumers[path]
        uses = sum(token_counts.values())
        token_list = ", ".join(sorted(token_counts))
        print(f"- {path}: {uses} uses / {len(token_counts)} tokens :: {token_list}")

    baseline = read_baseline()
    if baseline is None:
        print(
            "\nNo legacy NX baseline yet. Inventory-only mode; create "
            f"{rel(BASELINE)} from the list above to freeze the current debt."
        )
        return 0

    current = set(consumers)
    new_consumers = sorted(current - baseline)
    stale = sorted(baseline - current)

    failures: list[str] = []
    if new_consumers:
        failures.append(
            "New --nx-* consumer files are forbidden; use system.css tokens or "
            "migrate an existing legacy surface instead:\n  - "
            + "\n  - ".join(new_consumers)
        )
    if stale:
        failures.append(
            "Legacy NX baseline can shrink; remove these stale entries:\n  - "
            + "\n  - ".join(stale)
        )

    if failures:
        print("\nLegacy NX guard failed:", file=sys.stderr)
        for failure in failures:
            print(f"- {failure}", file=sys.stderr)
        return 1

    print("\nLegacy NX guard passed: no new consumer files and baseline is current.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
