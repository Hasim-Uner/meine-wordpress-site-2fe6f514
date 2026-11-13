"""Documented first commands must fit a host tool-output window.

Bytes are a size proxy, not measured tokens. Exit codes are ignored on purpose:
several first commands are gates that legitimately fail on the current tree.
"""
import re
import subprocess
import tempfile
import unittest
from pathlib import Path

ROOT = Path(__file__).resolve().parents[4]
# Claude Code spills or truncates tool output beyond 30,000 characters; a verdict
# printed after that point never reaches the agent. Stay inside with headroom.
MAX_OUTPUT_BYTES = 24000
FENCE = re.compile(r'^```(?:bash|sh)\n(.*?)^```', re.M | re.S)
COMMAND = re.compile(r'^(?:(?:bash|sh|python3)\s+)?agents/skills/[a-z0-9-]+/scripts/\S+')
# Inline spans count only with an interpreter; a bare path is a file reference.
INLINE = re.compile(r'`((?:bash|sh|python3)\s+agents/skills/[a-z0-9-]+/scripts/[^`\n]+)`')
# Placeholders, redirects, pipes and continuations are examples, not runnable as written.
NOT_RUNNABLE = re.compile(r'[<>\[|\\]|YYYY')


def documented_commands(root=ROOT):
    """Every script command a SKILL.md shows in a shell block or inline, as (skill, command)."""
    found = set()
    for entry in (root / 'agents/skills').glob('*/SKILL.md'):
        text = entry.read_text()
        lines = [line for block in FENCE.findall(text) for line in block.splitlines()]
        for line in lines + INLINE.findall(text):
            command = line.split(' #', 1)[0].strip()
            if COMMAND.match(command) and not NOT_RUNNABLE.search(command):
                found.add((entry.parent.name, command))
    return sorted(found)


class FirstCommandBudgetTests(unittest.TestCase):
    def test_discovery_finds_documented_commands(self):
        # Guards the parser: an empty list would make the budget test pass silently.
        self.assertGreaterEqual(len(documented_commands()), 10)

    def test_discovery_reads_blocks_and_inline_spans(self):
        with tempfile.TemporaryDirectory() as tmp:
            root = Path(tmp)
            # Built at runtime: a literal path to the fixture skill would trip the
            # missing-skill-path scan over this test file.
            prefix = 'agents/' + 'skills/demo/scripts/'
            fixture = root / 'agents' / 'skills' / 'demo'
            fixture.mkdir(parents=True)
            (fixture / 'SKILL.md').write_text(
                f'Run `bash {prefix}scan.sh` first; `{prefix}scan.sh` is only a path.\n'
                f'```bash\n{prefix}map.sh all   # comment\nbash {prefix}gate.sh /<route>/\n'
                f'bash {prefix}report.sh --md > out.md\n```\n')
            commands = [command for _, command in documented_commands(root)]
        self.assertEqual(commands, [f'{prefix}map.sh all', f'bash {prefix}scan.sh'])

    def test_output_fits_tool_window(self):
        for skill, command in documented_commands():
            with self.subTest(skill=skill, command=command):
                result = subprocess.run(command, shell=True, cwd=ROOT, capture_output=True, timeout=300)
                self.assertNotIn(result.returncode, (126, 127), 'documented command is not runnable')
                size = len(result.stdout) + len(result.stderr)
                self.assertLessEqual(
                    size, MAX_OUTPUT_BYTES,
                    f'{size} bytes: print a summary and move line detail behind --lines')


if __name__ == '__main__':
    unittest.main()
