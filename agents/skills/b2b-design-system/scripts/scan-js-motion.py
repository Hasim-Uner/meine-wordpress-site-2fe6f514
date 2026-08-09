#!/usr/bin/env python3
"""Emit normalized JavaScript motion-policy findings as TSV.

This is a scanner, not the user-facing guard. scripts/lint-css-motion.sh owns
baseline comparison, diagnostics, and exit codes.
"""

from __future__ import annotations

import argparse
import re
import sys
from collections import Counter
from dataclasses import dataclass
from pathlib import Path
from typing import Callable, Iterable


QUOTED = r"(?P<value>'(?:\\.|[^'\\])*'|\"(?:\\.|[^\"\\])*\"|`(?:\\.|[^`\\])*`)"
ALLOW_MARKER = "lint-js-motion: allow"


@dataclass(frozen=True)
class RulePattern:
    rule_id: str
    regex: re.Pattern[str]
    accepts: Callable[[re.Match[str]], bool] = lambda _match: True


def unquote(value: str) -> str:
    return value[1:-1] if len(value) >= 2 else value


def contains_transition_all(match: re.Match[str]) -> bool:
    return re.search(r"(?:^|[;{\s])transition\s*:\s*all\b|\ball\b", unquote(match.group("value")), re.I) is not None


def contains_bare_ease_in(match: re.Match[str]) -> bool:
    return re.search(r"\bease-in(?!-out)\b", unquote(match.group("value")), re.I) is not None


RULE_PATTERNS = (
    RulePattern(
        "literal-smooth-scroll",
        re.compile(r"\bbehavior\s*:\s*(?P<value>'smooth'|\"smooth\"|`smooth`)", re.I | re.S),
    ),
    RulePattern(
        "literal-smooth-scroll",
        re.compile(r"\b(?:style\s*\.\s*)?scrollBehavior\s*=\s*(?P<value>'smooth'|\"smooth\"|`smooth`)", re.I | re.S),
    ),
    RulePattern(
        "literal-smooth-scroll",
        re.compile(
            r"\bsetProperty\s*\(\s*(['\"])scroll-behavior\1\s*,\s*(?P<value>'smooth'|\"smooth\"|`smooth`)",
            re.I | re.S,
        ),
    ),
    RulePattern(
        "js-transition-all",
        re.compile(r"\b(?:style\s*\.\s*)?transition\s*=\s*" + QUOTED, re.I | re.S),
        contains_transition_all,
    ),
    RulePattern(
        "js-transition-all",
        re.compile(r"\btransition\s*:\s*" + QUOTED, re.I | re.S),
        contains_transition_all,
    ),
    RulePattern(
        "js-transition-all",
        re.compile(r"\bsetProperty\s*\(\s*(['\"])transition\1\s*,\s*" + QUOTED, re.I | re.S),
        contains_transition_all,
    ),
    RulePattern(
        "js-transition-all",
        re.compile(r"\b(?:style\s*\.\s*)?cssText\s*(?:\+=|=)\s*" + QUOTED, re.I | re.S),
        contains_transition_all,
    ),
    RulePattern(
        "js-bare-ease-in",
        re.compile(r"\beasing\s*:\s*" + QUOTED, re.I | re.S),
        contains_bare_ease_in,
    ),
    RulePattern(
        "js-bare-ease-in",
        re.compile(r"\b(?:style\s*\.\s*)?(?:transition|animation)\s*=\s*" + QUOTED, re.I | re.S),
        contains_bare_ease_in,
    ),
    RulePattern(
        "js-bare-ease-in",
        re.compile(r"\b(?:transition|animation)\s*:\s*" + QUOTED, re.I | re.S),
        contains_bare_ease_in,
    ),
)


def mask_comments(source: str) -> str:
    """Blank comments while preserving strings, offsets, and newlines."""

    chars = list(source)
    index = 0
    state = "code"
    quote = ""

    while index < len(chars):
        char = chars[index]
        next_char = chars[index + 1] if index + 1 < len(chars) else ""

        if state == "line-comment":
            if char == "\n":
                state = "code"
            else:
                chars[index] = " "
            index += 1
            continue

        if state == "block-comment":
            if char == "*" and next_char == "/":
                chars[index] = " "
                chars[index + 1] = " "
                index += 2
                state = "code"
                continue
            if char != "\n":
                chars[index] = " "
            index += 1
            continue

        if state == "string":
            if char == "\\":
                index += 2
                continue
            if char == quote:
                state = "code"
                quote = ""
            index += 1
            continue

        if char == "/" and next_char == "/":
            chars[index] = " "
            chars[index + 1] = " "
            index += 2
            state = "line-comment"
            continue

        if char == "/" and next_char == "*":
            chars[index] = " "
            chars[index + 1] = " "
            index += 2
            state = "block-comment"
            continue

        if char in ("'", '"', "`"):
            state = "string"
            quote = char

        index += 1

    return "".join(chars)


def is_allowed(source_lines: list[str], start_line: int, end_line: int) -> bool:
    first = max(0, start_line - 1)
    last = min(len(source_lines) - 1, end_line)
    return any(ALLOW_MARKER in source_lines[line] for line in range(first, last + 1))


def normalize(snippet: str) -> str:
    return re.sub(r"\s+", " ", snippet).strip()


def scan_file(path: Path) -> Counter[tuple[str, str]]:
    source = path.read_text(encoding="utf-8")
    masked = mask_comments(source)
    lines = source.splitlines()
    findings: Counter[tuple[str, str]] = Counter()
    seen: set[tuple[str, int, int]] = set()

    for rule in RULE_PATTERNS:
        for match in rule.regex.finditer(masked):
            if not rule.accepts(match):
                continue

            span_key = (rule.rule_id, match.start(), match.end())
            if span_key in seen:
                continue
            seen.add(span_key)

            start_line = source.count("\n", 0, match.start())
            end_line = source.count("\n", 0, match.end())
            if is_allowed(lines, start_line, end_line):
                continue

            snippet_first = start_line if start_line == end_line else max(0, start_line - 1)
            snippet = normalize("\n".join(lines[snippet_first : end_line + 1]))
            findings[(rule.rule_id, snippet)] += 1

    return findings


def source_files(js_dir: Path) -> Iterable[Path]:
    for path in sorted(js_dir.rglob("*")):
        if path.is_file() and path.suffix.lower() in {".js", ".jsx"}:
            yield path


def main() -> int:
    parser = argparse.ArgumentParser(description="Scan JavaScript-authored motion policy violations.")
    parser.add_argument("js_dir", type=Path)
    args = parser.parse_args()

    js_dir = args.js_dir
    if not js_dir.is_dir():
        parser.error(f"not a JavaScript directory: {js_dir}")

    try:
        for path in source_files(js_dir):
            relative = path.relative_to(js_dir).as_posix()
            for (rule_id, snippet), count in sorted(scan_file(path).items()):
                print(f"{rule_id}\t{relative}\t{snippet}\t{count}")
    except (OSError, UnicodeError) as exc:
        print(f"JavaScript motion scan failed: {exc}", file=sys.stderr)
        return 2

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
