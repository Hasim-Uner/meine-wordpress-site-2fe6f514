#!/usr/bin/env python3
"""Fail when a top-level theme CSS/JS asset has no literal source/build reference.

This is a conservative orphan guard, not a dead-code analyser: a referenced file can
still be obsolete. But an asset with no reference outside itself cannot be loaded by
the checked-in theme or deployment tooling and should be removed or deliberately
wired up.
"""

from __future__ import annotations

from pathlib import Path
import sys

ROOT = Path(__file__).resolve().parents[1]
ASSET_DIRS = (
    ROOT / "blocksy-child" / "assets" / "css",
    ROOT / "blocksy-child" / "assets" / "js",
)
REFERENCE_ROOTS = (
    ROOT / "blocksy-child",
    ROOT / "scripts",
)
REFERENCE_SUFFIXES = {
    ".php",
    ".css",
    ".js",
    ".mjs",
    ".json",
    ".html",
    ".sh",
    ".py",
}


def iter_reference_files() -> list[Path]:
    files: list[Path] = []
    for root in REFERENCE_ROOTS:
        if not root.exists():
            continue
        for path in root.rglob("*"):
            if path.is_file() and path.suffix.lower() in REFERENCE_SUFFIXES:
                files.append(path)
    return files


def read_text(path: Path) -> str:
    try:
        return path.read_text(encoding="utf-8")
    except (UnicodeDecodeError, OSError):
        return ""


def main() -> int:
    assets = sorted(
        path
        for asset_dir in ASSET_DIRS
        if asset_dir.exists()
        for path in asset_dir.iterdir()
        if path.is_file() and path.suffix.lower() in {".css", ".js"}
    )
    reference_files = iter_reference_files()
    contents = {path: read_text(path) for path in reference_files}

    orphans: list[Path] = []
    for asset in assets:
        filename = asset.name
        referenced = any(
            path != asset and filename in text
            for path, text in contents.items()
        )
        if not referenced:
            orphans.append(asset)

    print(f"Checked {len(assets)} top-level CSS/JS assets.")
    if not orphans:
        print("Asset reference audit passed: no unreferenced assets found.")
        return 0

    print("Unreferenced assets:", file=sys.stderr)
    for asset in orphans:
        print(f"- {asset.relative_to(ROOT)}", file=sys.stderr)
    print(
        "Remove obsolete files or add a real runtime/build reference; do not silence the guard with documentation-only mentions.",
        file=sys.stderr,
    )
    return 1


if __name__ == "__main__":
    raise SystemExit(main())
