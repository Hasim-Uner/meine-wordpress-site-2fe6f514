#!/usr/bin/env python3
"""Inventory and guard legacy --nx-* CSS consumers.

`design-system.css` is a compatibility provider, not the design target for new
surfaces. This script inventories every stylesheet that still consumes an
`--nx-*` custom property and distinguishes two cases:

- provider-coupled tokens: actually declared by `design-system.css`;
- unresolved legacy names: NX-looking variables not declared by that provider,
  which therefore resolve through another layer or their `var()` fallback.

`scripts/baselines/legacy-nx-consumers.txt` is a shrink-only contract:

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
NX_DECL_RE = re.compile(r"(?m)^\s*(--nx-[a-zA-Z0-9_-]+)\s*:")


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
    if not PROVIDER.is_file():
        print(f"Missing legacy provider: {rel(PROVIDER)}", file=sys.stderr)
        return 1

    provider_tokens = set(NX_DECL_RE.findall(PROVIDER.read_text(encoding="utf-8")))
    consumers: dict[str, Counter[str]] = {}

    for path in css_files():
        if path == PROVIDER:
            continue
        text = path.read_text(encoding="utf-8")
        tokens = NX_USE_RE.findall(text)
        if tokens:
            consumers[rel(path)] = Counter(tokens)

    coupled_files: list[str] = []
    fallback_only_files: list[str] = []
    unresolved_tokens: set[str] = set()

    print(f"Legacy --nx-* consumer files: {len(consumers)}")
    print(f"NX tokens declared by design-system.css: {len(provider_tokens)}")

    for path in sorted(consumers):
        token_counts = consumers[path]
        used_tokens = set(token_counts)
        coupled = sorted(used_tokens & provider_tokens)
        unresolved = sorted(used_tokens - provider_tokens)
        uses = sum(token_counts.values())

        if coupled:
            coupled_files.append(path)
        else:
            fallback_only_files.append(path)
        unresolved_tokens.update(unresolved)

        coupled_label = ", ".join(coupled) if coupled else "none"
        unresolved_label = ", ".join(unresolved) if unresolved else "none"
        print(
            f"- {path}: {uses} uses / {len(used_tokens)} tokens"
            f" | provider={coupled_label} | unresolved={unresolved_label}"
        )

    print("\nProvider coupling summary:")
    print(f"- files actually coupled to design-system.css tokens: {len(coupled_files)}")
    print(f"- files with NX names but no provider-declared token: {len(fallback_only_files)}")
    if fallback_only_files:
        print("- fallback-only files:")
        for path in fallback_only_files:
            print(f"  - {path}")
    if unresolved_tokens:
        print("- NX names not declared by design-system.css: " + ", ".join(sorted(unresolved_tokens)))

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
