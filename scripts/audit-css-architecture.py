#!/usr/bin/env python3
"""Guard the CSS token ownership contract.

The current site intentionally has more than one visual system while legacy
routes are being migrated. This guard does not pretend they are one system.
It protects the new Gutachten core instead:

- system.css owns the canonical Gutachten tokens.
- anfragestrecke.css is the only temporary value-identical mirror.
- known scoped legacy collisions are frozen to exact selectors and values.
- no other stylesheet may redefine those canonical token names.

The mirror can be removed later without changing this contract: delete it from
TRANSITIONAL_MIRRORS once anfragestrecke.css consumes system.css directly.
Scoped legacy collisions should disappear by renaming/migrating their local
tokens, never by broadening this allowlist.
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

# These names predate system.css and are deliberately local to an older
# editorial Solar surface. Their semantics and values differ from the
# Gutachten core, so silently treating them as another owner would be wrong.
# Freeze the exact selector/value pair until that route is renamed/migrated.
SCOPED_LEGACY_COLLISIONS = {
    CSS_DIR / "energy-systems.css": {
        "selector": ".solar-page",
        "tokens": {
            "--serif": {
                '"Satoshi", "Figtree", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif'
            },
            "--mono": {
                'ui-monospace, "SF Mono", "Cascadia Mono", "Roboto Mono", "Menlo", "Consolas", monospace'
            },
        },
    }
}

DECLARATION_RE = re.compile(
    r"(?m)^\s*(--[a-zA-Z0-9_-]+)\s*:\s*([^;]+);"
)
ROOT_BLOCK_RE = re.compile(r"(?s):root(?:\s*,[^\{]+)?\s*\{(.*?)\}")


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
        if path in TRANSITIONAL_MIRRORS:
            continue

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

    for mirror_path in sorted(TRANSITIONAL_MIRRORS):
        if not mirror_path.is_file():
            errors.append(f"Transitional CSS mirror is missing: {relative(mirror_path)}")
            continue

        mirror = file_declarations.get(mirror_path, declarations(mirror_path))
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
