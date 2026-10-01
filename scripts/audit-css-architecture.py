#!/usr/bin/env python3
"""Guard the CSS token ownership contract.

The current site intentionally has more than one visual system while legacy
routes are being migrated. This guard does not pretend they are one system.
It protects the new Gutachten core instead:

- system.css owns the canonical Gutachten tokens (colour, type, spacing, radius,
  type grades, door, motion).
- known scoped legacy collisions are frozen to exact selectors and values.
- no other stylesheet may redefine those canonical token names.

There is no value-identical mirror any more: anfragestrecke.css consumes
system.css directly (removed 2026-10-01). Scoped legacy collisions should
disappear by renaming/migrating their local tokens, never by broadening this
allowlist.
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CSS_DIR = ROOT / "blocksy-child" / "assets" / "css"
CANONICAL_OWNER = CSS_DIR / "system.css"

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

    "--r0",
    "--r1",
    "--grad-h1",
    "--grad-h2",
    "--grad-h2-leise",
    "--grad-text",
    "--grad-klein",
    "--tuer-h",
    "--tuer-schrift",
)

# Scoped legacy collisions with core tokens used to be frozen here (the
# .solar-page --serif/--mono of energy-systems.css). That file is gone; the
# mechanism stays so a future exception must be listed selector- and
# value-exact instead of being silently tolerated.
SCOPED_LEGACY_COLLISIONS: dict[Path, dict[str, object]] = {}

# A declaration starts a line or follows `{` or `;`, so `.x { --papier: #fff; }`
# on one line is caught as well as the multi-line form.
DECLARATION_RE = re.compile(
    r"(?m)(?:^|[{;])\s*(--[a-zA-Z0-9_-]+)\s*:\s*([^;}]+)[;}]"
)
ROOT_BLOCK_RE = re.compile(r"(?s):root(?:\s*,[^\{]+)?\s*\{(.*?)\}")


RETIRED_TOKEN_PREFIXES = ("--sst-", "--vp-")


def normalize_value(value: str) -> str:
    return re.sub(r"\s+", " ", value.strip())


def declarations_from_text(text: str) -> dict[str, set[str]]:
    found: dict[str, set[str]] = {}
    for token, value in DECLARATION_RE.findall(text):
        found.setdefault(token, set()).add(normalize_value(value))
    return found


def declarations(path: Path) -> dict[str, set[str]]:
    return declarations_from_text(path.read_text(encoding="utf-8"))


def relative(path: Path) -> str:
    return path.relative_to(ROOT).as_posix()


def selector_block(text: str, selector: str) -> str | None:
    pattern = re.compile(rf"(?s){re.escape(selector)}\s*\{{(.*?)\}}")
    match = pattern.search(text)
    return match.group(1) if match else None


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
    file_declarations: dict[Path, dict[str, set[str]]] = {}

    for path in css_files:
        defined_all = declarations(path)
        file_declarations[path] = defined_all
        if path == CANONICAL_OWNER:
            continue
        defined = canonical_set.intersection(defined_all)
        if defined:
            redefiners[path] = defined

    for path, tokens in redefiners.items():
        legacy = SCOPED_LEGACY_COLLISIONS.get(path)
        if legacy is None:
            errors.append(
                f"{relative(path)} redefines Gutachten core tokens owned by system.css: "
                + ", ".join(sorted(tokens))
            )
            continue

        approved_tokens = set(legacy["tokens"])
        unexpected_tokens = tokens - approved_tokens
        if unexpected_tokens:
            errors.append(
                f"{relative(path)} adds unapproved Gutachten core token collisions: "
                + ", ".join(sorted(unexpected_tokens))
            )

        text = path.read_text(encoding="utf-8")
        selector = str(legacy["selector"])
        block = selector_block(text, selector)
        if block is None:
            errors.append(
                f"{relative(path)} legacy collision selector disappeared: {selector}. "
                "Migrate/remove the exception instead of relocating it."
            )
            continue

        block_declarations = declarations_from_text(block)
        all_declarations = file_declarations[path]

        for token in tokens.intersection(approved_tokens):
            expected = {
                normalize_value(value)
                for value in legacy["tokens"][token]
            }
            actual_file = all_declarations.get(token, set())
            actual_scope = block_declarations.get(token, set())
            if actual_file != expected or actual_scope != expected:
                errors.append(
                    f"Scoped legacy collision drift for {token} in {relative(path)}: "
                    f"expected {selector}={sorted(expected)!r}, "
                    f"file={sorted(actual_file)!r}, scope={sorted(actual_scope)!r}"
                )

    # Abgeloeste Token-Familien: --sst-* und --vp-* waren die Seitentokens der
    # Server-Side-Tracking-Schichten und sind in system.css aufgegangen.
    for path, defined_all in file_declarations.items():
        retired = sorted(t for t in defined_all if t.startswith(RETIRED_TOKEN_PREFIXES))
        if retired:
            errors.append(
                f"{relative(path)} declares retired page tokens ({', '.join(retired)}); "
                "use the system.css tokens instead"
            )

    sst_css = CSS_DIR / "server-side-tracking.css"
    if sst_css.is_file() and re.search(r"(?m)^\s*@import\b", sst_css.read_text(encoding="utf-8")):
        errors.append("server-side-tracking.css must be one file without @import")

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
    if SCOPED_LEGACY_COLLISIONS:
        print(
            "Frozen scoped legacy collisions: "
            + ", ".join(
                f"{relative(path)} {spec['selector']} ({', '.join(sorted(spec['tokens']))})"
                for path, spec in sorted(SCOPED_LEGACY_COLLISIONS.items())
            )
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
