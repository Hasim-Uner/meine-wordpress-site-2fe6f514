#!/usr/bin/env python3
"""Shrink-only guard for literal design values in stylesheets.

system.css is the one source for colour, radius and typeface. Every other
stylesheet is counted here, per file and per category:

- color:  hex literals and rgb()/hsl()-style colour functions without var()
          outside `:root` and `.tafel` (the two places allowed to hold values);
- radius: border-radius declarations whose value is not var(--r0), var(--r1),
          0 or 50% (keywords inherit/initial/unset included);
- font:   font-family declarations (and font shorthands) that name a literal
          typeface instead of a var(--serif|--serif-display|--mono) token.

`scripts/baselines/css-values.tsv` is a shrink-only contract, the same
mechanic as `legacy-nx-consumers.txt`:

- a count above the baseline fails the build;
- a count below the baseline fails the build until the baseline is lowered
  (`--write-baseline`), so a cleaned file cannot regress unnoticed;
- a file missing from the baseline counts as 0 in every category.

Counting is per literal, not per distinct value, and is deterministic: no
heuristics beyond the declaration grammar below.
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CSS_DIR = ROOT / "blocksy-child" / "assets" / "css"
STYLE_CSS = ROOT / "blocksy-child" / "style.css"
BASELINE = ROOT / "scripts" / "baselines" / "css-values.tsv"
CATEGORIES = ("color", "radius", "font")

# Selectors whose declarations may hold literal values (the token owners).
VALUE_OWNER_SELECTORS = {":root", ".tafel"}

ALLOWED_RADIUS = {
    "0",
    "50%",
    "var(--r0)",
    "var(--r1)",
    "inherit",
    "initial",
    "unset",
    "revert",
}
RADIUS_PROPERTY_RE = re.compile(r"^border(?:-(?:top|bottom|start|end)-(?:left|right|start|end))?-radius$")
HEX_RE = re.compile(r"#(?:[0-9a-fA-F]{8}|[0-9a-fA-F]{6}|[0-9a-fA-F]{4}|[0-9a-fA-F]{3})(?![0-9a-zA-Z_-])")
COLOR_FUNCTION_RE = re.compile(r"\b(?:rgba?|hsla?|hwb|lab|lch|oklab|oklch)\(", re.IGNORECASE)
FONT_FAMILY_KEYWORDS = {"inherit", "initial", "unset", "revert", "revert-layer"}
GENERIC_FAMILY_RE = re.compile(
    r"(?<![\w-])(?:serif|sans-serif|monospace|system-ui|ui-monospace|ui-sans-serif|ui-serif|cursive)(?![\w-])",
    re.IGNORECASE,
)
STRING_RE = re.compile(r'"(?:\\.|[^"\\])*"|\'(?:\\.|[^\'\\])*\'')
URL_RE = re.compile(r"url\(\s*(?:\"(?:\\.|[^\"\\])*\"|'(?:\\.|[^'\\])*'|[^)]*)\s*\)", re.IGNORECASE)


def rel(path: Path) -> str:
    return path.relative_to(ROOT).as_posix()


def css_files() -> list[Path]:
    files = sorted(CSS_DIR.rglob("*.css"))
    if STYLE_CSS.is_file():
        files.append(STYLE_CSS)
    return files


def iter_declarations(text: str):
    """Yield (selector_stack, property, value) for every declaration.

    A small scanner instead of a regex: it respects comments, strings, and
    parentheses (`url(data:...;base64,...)` contains semicolons), and it tracks
    the stack of preludes so a declaration knows whether it sits in `:root`,
    `.tafel` or an `@font-face`.
    """
    stack: list[str] = []
    buffer: list[str] = []
    paren = 0
    i = 0
    length = len(text)

    def flush_declaration():
        raw = "".join(buffer).strip()
        buffer.clear()
        if not raw or ":" not in raw or not stack:
            return None
        prop, _, value = raw.partition(":")
        return list(stack), prop.strip().lower(), re.sub(r"\s+", " ", value).strip()

    while i < length:
        char = text[i]

        if char == "/" and text.startswith("/*", i):
            end = text.find("*/", i + 2)
            i = length if end == -1 else end + 2
            continue

        if char in "\"'":
            end = i + 1
            while end < length and text[end] != char:
                end += 2 if text[end] == "\\" else 1
            buffer.append(text[i : end + 1])
            i = end + 1
            continue

        if char == "(":
            paren += 1
        elif char == ")":
            paren = max(0, paren - 1)

        if paren == 0:
            if char == "{":
                stack.append(re.sub(r"\s+", " ", "".join(buffer)).strip())
                buffer.clear()
                i += 1
                continue
            if char == "}":
                declaration = flush_declaration()
                if declaration:
                    yield declaration
                if stack:
                    stack.pop()
                i += 1
                continue
            if char == ";":
                declaration = flush_declaration()
                if declaration:
                    yield declaration
                buffer.clear()
                i += 1
                continue

        buffer.append(char)
        i += 1


def is_value_owner(prelude: str) -> bool:
    selectors = {part.strip() for part in prelude.split(",")}
    return bool(selectors & VALUE_OWNER_SELECTORS)


def strip_important(value: str) -> str:
    return re.sub(r"\s*!\s*important\s*$", "", value, flags=re.IGNORECASE).strip()


def count_colors(value: str) -> int:
    value = STRING_RE.sub("", URL_RE.sub("", value))
    count = len(HEX_RE.findall(value))

    for match in COLOR_FUNCTION_RE.finditer(value):
        depth = 1
        end = match.end()
        while end < len(value) and depth:
            depth += {"(": 1, ")": -1}.get(value[end], 0)
            end += 1
        if "var(" not in value[match.end() : end]:
            count += 1

    return count


def font_is_literal(prop: str, value: str) -> bool:
    value = strip_important(value)
    lowered = value.lower()

    if prop == "font-family":
        return not (lowered in FONT_FAMILY_KEYWORDS or lowered.startswith("var("))

    if prop == "font":
        if "var(--" in lowered:
            return False
        return bool(STRING_RE.search(value) or GENERIC_FAMILY_RE.search(value))

    return False


def count_file(path: Path) -> dict[str, int]:
    counts = dict.fromkeys(CATEGORIES, 0)
    text = path.read_text(encoding="utf-8")

    for stack, prop, value in iter_declarations(text):
        if any(prelude.lower().startswith("@font-face") for prelude in stack):
            continue
        if is_value_owner(stack[-1]):
            continue

        value = strip_important(value)

        counts["color"] += count_colors(value)

        if RADIUS_PROPERTY_RE.match(prop) and value.lower() not in ALLOWED_RADIUS:
            counts["radius"] += 1

        if font_is_literal(prop, value):
            counts["font"] += 1

    return counts


def read_baseline() -> dict[str, dict[str, int]] | None:
    if not BASELINE.is_file():
        return None

    entries: dict[str, dict[str, int]] = {}
    for number, raw in enumerate(BASELINE.read_text(encoding="utf-8").splitlines(), start=1):
        line = raw.strip()
        if not line or line.startswith("#"):
            continue
        columns = raw.split("\t")
        if len(columns) != 1 + len(CATEGORIES):
            raise SystemExit(f"{rel(BASELINE)}:{number}: expected path + {len(CATEGORIES)} counts")
        entries[columns[0]] = {
            category: int(value) for category, value in zip(CATEGORIES, columns[1:])
        }
    return entries


def write_baseline(current: dict[str, dict[str, int]]) -> None:
    lines = [
        "# Literal design values per stylesheet. Shrink only (scripts/audit-css-values.py).",
        "# Counted outside :root and .tafel. Files not listed count as 0 in every category.",
        "# path\t" + "\t".join(CATEGORIES),
    ]
    for path in sorted(current):
        counts = current[path]
        if any(counts.values()):
            lines.append(path + "\t" + "\t".join(str(counts[category]) for category in CATEGORIES))
    BASELINE.write_text("\n".join(lines) + "\n", encoding="utf-8")


def main(argv: list[str]) -> int:
    write = "--write-baseline" in argv
    allow_increase = "--allow-increase" in argv

    current = {rel(path): count_file(path) for path in css_files()}
    totals = {category: sum(counts[category] for counts in current.values()) for category in CATEGORIES}
    dirty = sum(1 for counts in current.values() if any(counts.values()))

    print(f"CSS files checked: {len(current)} ({dirty} with literal values)")
    print("Literal values outside :root/.tafel: " + ", ".join(f"{c}={totals[c]}" for c in CATEGORIES))

    baseline = read_baseline()

    if write:
        if baseline is not None and not allow_increase:
            raised = [
                path
                for path, counts in current.items()
                if any(counts[c] > baseline.get(path, {}).get(c, 0) for c in CATEGORIES)
            ]
            if raised:
                print(
                    "Refusing to raise the baseline (use --allow-increase for a deliberate exception):\n  - "
                    + "\n  - ".join(sorted(raised)),
                    file=sys.stderr,
                )
                return 1
        write_baseline(current)
        print(f"Wrote {rel(BASELINE)}")
        return 0

    if baseline is None:
        print(
            f"\nNo baseline yet. Run with --write-baseline to create {rel(BASELINE)}.",
            file=sys.stderr,
        )
        return 1

    increases: list[str] = []
    decreases: list[str] = []
    stale = sorted(set(baseline) - set(current))

    for path in sorted(current):
        base = baseline.get(path, dict.fromkeys(CATEGORIES, 0))
        for category in CATEGORIES:
            now, then = current[path][category], base[category]
            if now > then:
                increases.append(f"{path}: {category} {then} -> {now}")
            elif now < then:
                decreases.append(f"{path}: {category} {then} -> {now}")

    failures: list[str] = []
    if increases:
        failures.append(
            "Literal design values grew. Use the tokens from system.css "
            "(colour tokens, --r0/--r1, --serif/--serif-display/--mono) instead:\n  - "
            + "\n  - ".join(increases)
        )
    if decreases:
        failures.append(
            "Counts dropped; lower the baseline with "
            "`python3 scripts/audit-css-values.py --write-baseline`:\n  - "
            + "\n  - ".join(decreases)
        )
    if stale:
        failures.append(
            "Baseline lists files that no longer exist; lower it with --write-baseline:\n  - "
            + "\n  - ".join(stale)
        )

    if failures:
        print("\nCSS value guard failed:", file=sys.stderr)
        for failure in failures:
            print(f"- {failure}", file=sys.stderr)
        return 1

    print("\nCSS value guard passed: no literal value above baseline, baseline is current.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main(sys.argv[1:]))
