#!/usr/bin/env python3
"""Remove the transitional Gutachten token mirror from deployment CSS.

Authoring source still contains the mirror so the Solar route can be migrated
incrementally and reviewed visually. The deployment package already loads
system.css globally, so repeating the same custom properties in
anfragestrecke.css is unnecessary. audit-css-architecture.py must run first and
prove that both value sets are identical.
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

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

TOKEN_ALT = "|".join(re.escape(token) for token in CANONICAL_TOKENS)
DECLARATION_LINE_RE = re.compile(
    rf"(?m)^[ \t]*(?:{TOKEN_ALT})[ \t]*:[^;]+;[ \t]*(?:\n|$)"
)
LEFTOVER_RE = re.compile(rf"(?m)^[ \t]*(?:{TOKEN_ALT})[ \t]*:")


def main() -> int:
    if len(sys.argv) != 2:
        print("Usage: collapse-gutachten-token-mirror.py <anfragestrecke.css>", file=sys.stderr)
        return 2

    path = Path(sys.argv[1])
    if not path.is_file():
        print(f"CSS file not found: {path}", file=sys.stderr)
        return 1

    original = path.read_text(encoding="utf-8")
    collapsed, removed = DECLARATION_LINE_RE.subn("", original)

    if removed == 0:
        print(f"No Gutachten token mirror found in {path}", file=sys.stderr)
        return 1

    leftovers = sorted(set(LEFTOVER_RE.findall(collapsed)))
    if leftovers:
        print(f"Canonical Gutachten token declarations remain in {path}", file=sys.stderr)
        return 1

    path.write_text(collapsed, encoding="utf-8")
    print(f"Collapsed {removed} mirrored Gutachten token declarations in {path}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
