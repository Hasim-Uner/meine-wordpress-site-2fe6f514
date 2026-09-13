#!/usr/bin/env python3
"""Guard the CSS token ownership contract.

The current site intentionally has more than one visual system while legacy
routes are being migrated. This guard does not pretend they are one system.
It protects the new Gutachten core instead:

- system.css owns the canonical Gutachten tokens.
- anfragestrecke.css is the only temporary mirror and must stay value-identical.
- no third stylesheet may redefine those canonical tokens.

The mirror can be removed later without changing this contract: delete it from
TRANSITIONAL_MIRRORS once anfragestrecke.css consumes system.css directly.
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CSS_DIR = ROOT / "blocksy-child" / "assets" / "css"
CANONICAL_OWNER = CSS_DIR / "system.css"
TRANSITIONAL_MIRRORS = {CSS_DIR / "anfragestrecke.css"}

CANONICAL_TOKENS = (
    "--papier",
    "--zone",
    "--zone2",
    "--tinte",
    "--grau",
    "--matt",
    "--stempel",
    "--haar",
    "--strich",
    "--serif",
    "--serif-display",
    "--mono",
    "--rand",
    "--marg",
    "--satz",
    "--s0",
    "--s1",
    "--s2",
    "--s3",
    "--s4",
    "--s5",
    "--s6",
    "--mono-s",
    "--mono-m",
    "--mono-l",
    "--ease-aus",
    "--ease-weich",
    "--t-mikro",
    "--t-norm",
    "--t-gross",
)

DECLARATION_RE = re.compile(
    r"(?m)^\s*(--[a-zA-Z0-9_-]+)\s*:\s*([^;]+);"
)
ROOT_BLOCK_RE = re.compile(r"(?s):root(?:\s*,[^\{]+)?\s*\{(.*?)\}")


def normalize_value(value: str) -> str:
    return re.sub(r"\s+", " ", value.strip())


def declarations(path: Path) -> dict[str, set[str]]:
    text = path.read_text(encoding="utf-8")
    found: dict[str, set[str]] = {}
    for token, value in DECLARATION_RE.findall(text):
        found.setdefault(token, set()).add(normalize_value(value))
    return found


def relative(path: Path) -> str:
    return path.relative_to(ROOT).as_posix()


def main() -> int:
    errors: list[str] = []

    if not CANONICAL_OWNER.is_file():
        print(f"Missing canonical CSS token owner: {relative(CANONICAL_OWNER)}", file=sys.stderr)
        return 1

    owner = declarations(CANONICAL_OWNER)
    missing = [token for token in CANONICAL_TOKENS if token not in owner]
    if missing:
        errors.append(
            "system.css is missing canonical tokens: " + ", ".join(missing)
        )

    css_files = sorted(CSS_DIR.rglob("*.css"))
    canonical_set = set(CANONICAL_TOKENS)
    redefiners: dict[Path, set[str]] = {}

    for path in css_files:
        if path == CANONICAL_OWNER:
            continue
        defined = canonical_set.intersection(declarations(path))
        if defined:
            redefiners[path] = defined

    unexpected = {
        path: tokens
        for path, tokens in redefiners.items()
        if path not in TRANSITIONAL_MIRRORS
    }
    for path, tokens in unexpected.items():
        errors.append(
            f"{relative(path)} redefines Gutachten core tokens owned by system.css: "
            + ", ".join(sorted(tokens))
        )

    for mirror_path in sorted(TRANSITIONAL_MIRRORS):
        if not mirror_path.is_file():
            errors.append(f"Transitional CSS mirror is missing: {relative(mirror_path)}")
            continue

        mirror = declarations(mirror_path)
        mirror_missing = [token for token in CANONICAL_TOKENS if token not in mirror]
        if mirror_missing:
            errors.append(
                f"{relative(mirror_path)} no longer mirrors all canonical tokens; "
                "finish the migration and remove it from TRANSITIONAL_MIRRORS. Missing: "
                + ", ".join(mirror_missing)
            )
            continue

        for token in CANONICAL_TOKENS:
            owner_values = owner.get(token, set())
            mirror_values = mirror.get(token, set())
            if owner_values != mirror_values:
                errors.append(
                    f"Token drift for {token}: system.css={sorted(owner_values)!r}, "
                    f"{mirror_path.name}={sorted(mirror_values)!r}"
                )

    # Report unscoped root token owners. This is diagnostic for the remaining
    # legacy systems; it intentionally does not fail while those routes exist.
    root_token_files: list[str] = []
    for path in css_files:
        text = path.read_text(encoding="utf-8")
        root_blocks = ROOT_BLOCK_RE.findall(text)
        if any(DECLARATION_RE.search(block) for block in root_blocks):
            root_token_files.append(relative(path))

    print(f"CSS files checked: {len(css_files)}")
    print(f"Canonical Gutachten token owner: {relative(CANONICAL_OWNER)}")
    if TRANSITIONAL_MIRRORS:
        print(
            "Transitional token mirrors: "
            + ", ".join(relative(path) for path in sorted(TRANSITIONAL_MIRRORS))
        )
    print("Stylesheets with :root token declarations: " + (", ".join(root_token_files) or "none"))

    if errors:
        print("\nCSS architecture violations:", file=sys.stderr)
        for error in errors:
            print(f"- {error}", file=sys.stderr)
        return 1

    print("CSS architecture guard passed.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
