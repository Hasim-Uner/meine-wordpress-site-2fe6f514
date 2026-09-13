#!/usr/bin/env python3
"""Remove the superseded single-post base block from deployment style.css.

The source stylesheet still contains an early historical `NEXUS SINGLE PAGE
LAYOUT` block. Every single post and the SEO cornerstone route already load the
newer dedicated `assets/css/single.css`, which owns the same structural
selectors and is enqueued later in the cascade.

This deployment transform is intentionally narrow:
- it only removes the text between two stable section markers;
- it verifies that the dedicated single stylesheet still owns the critical
  selectors before removing anything;
- it leaves the immediately following archive block untouched because
  `archive.php` still uses that legacy shell.

The long-term source migration should delete the block from style.css itself.
Until then, production does not need to ship both generations.
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

START_MARKER = "/* --- NEXUS SINGLE PAGE LAYOUT --- */"
END_MARKER = "/* --- NEXUS ARCHIVE LAYOUT --- */"
REQUIRED_SINGLE_SELECTORS = (
    ".nexus-single-container",
    ".nexus-article-hero",
    ".nexus-meta-top",
    ".nexus-title",
    ".nexus-author-row",
    ".nexus-article-content",
    ".nexus-post-footer-cta",
    ".nexus-btn",
)
NX_USE_RE = re.compile(r"var\(\s*--nx-[a-zA-Z0-9_-]+")


def fail(message: str) -> int:
    print(f"style.css legacy prune failed: {message}", file=sys.stderr)
    return 1


def main() -> int:
    if len(sys.argv) != 3:
        print(
            "Usage: prune-style-single-legacy.py <style.css> <single.css>",
            file=sys.stderr,
        )
        return 2

    style_path = Path(sys.argv[1])
    single_path = Path(sys.argv[2])

    if not style_path.is_file():
        return fail(f"missing style file: {style_path}")
    if not single_path.is_file():
        return fail(f"missing dedicated single stylesheet: {single_path}")

    style = style_path.read_text(encoding="utf-8")
    single = single_path.read_text(encoding="utf-8")

    missing_selectors = [
        selector for selector in REQUIRED_SINGLE_SELECTORS if selector not in single
    ]
    if missing_selectors:
        return fail(
            "dedicated single.css no longer covers critical selectors: "
            + ", ".join(missing_selectors)
        )

    start = style.find(START_MARKER)
    end = style.find(END_MARKER)
    if start < 0 or end < 0:
        return fail("expected single/archive section markers are missing")
    if end <= start:
        return fail("archive marker occurs before single marker")
    if style.find(START_MARKER, start + len(START_MARKER)) >= 0:
        return fail("single marker occurs more than once")
    if style.find(END_MARKER, end + len(END_MARKER)) >= 0:
        return fail("archive marker occurs more than once")

    legacy_block = style[start:end]
    if ".nexus-archive-container" in legacy_block:
        return fail("single block unexpectedly contains archive selectors")

    missing_from_legacy = [
        selector for selector in REQUIRED_SINGLE_SELECTORS if selector not in legacy_block
    ]
    if missing_from_legacy:
        return fail(
            "legacy block shape changed; review before pruning. Missing: "
            + ", ".join(missing_from_legacy)
        )

    nx_uses = len(NX_USE_RE.findall(legacy_block))
    removed_lines = legacy_block.count("\n")
    removed_bytes = len(legacy_block.encode("utf-8"))

    replacement = (
        "/* Deployment: superseded single-post base removed. "
        "assets/css/single.css owns this surface. */\n\n"
    )
    style_path.write_text(style[:start] + replacement + style[end:], encoding="utf-8")

    print(
        "Pruned superseded style.css single block: "
        f"{removed_lines} lines, {removed_bytes} bytes, {nx_uses} legacy NX uses."
    )
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
