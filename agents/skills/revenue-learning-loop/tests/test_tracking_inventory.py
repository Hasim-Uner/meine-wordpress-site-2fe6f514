from __future__ import annotations

import importlib.util
import tempfile
import unittest
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[1] / "scripts" / "tracking-inventory.py"
SPEC = importlib.util.spec_from_file_location("tracking_inventory", SCRIPT)
assert SPEC and SPEC.loader
MODULE = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(MODULE)


class TrackingInventoryTests(unittest.TestCase):
    def setUp(self) -> None:
        self.tempdir = tempfile.TemporaryDirectory()
        self.addCleanup(self.tempdir.cleanup)
        self.root = Path(self.tempdir.name)
        (self.root / "blocksy-child").mkdir()

    def write(self, relative: str, content: str) -> None:
        path = self.root / relative
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_text(content, encoding="utf-8")

    def test_inventories_literal_dynamic_and_emitter_contracts(self) -> None:
        self.write(
            "blocksy-child/page-demo.php",
            """
            <a href="<?php echo esc_url( $url ); ?>"
               data-track-action="cta_demo"
               data-track-category="lead_gen"
               data-track-section="hero">Start</a>
            <button data-track-action="<?php echo esc_attr( $action ); ?>"
                    data-track-category="engagement"
                    data-track-section="tool">Run</button>
            """,
        )
        self.write(
            "blocksy-child/assets/js/demo.js",
            """
            document.addEventListener('click', function (event) {
              var node = event.target.closest('[data-track-action]');
              window.dataLayer.push({ event: 'cta_demo' });
            });
            """,
        )

        inventory = MODULE.build_inventory(self.root)

        self.assertEqual(inventory["action_occurrences"], 2)
        self.assertEqual(inventory["literal_actions"], 1)
        self.assertEqual(inventory["dynamic_action_occurrences"], 1)
        self.assertEqual(inventory["actions"], {"cta_demo": 1})
        self.assertEqual(inventory["findings"]["advisory"]["missing_same_tag_category"], [])
        self.assertEqual(inventory["findings"]["advisory"]["missing_same_tag_section"], [])
        self.assertEqual(len(inventory["emitters"]), 1)
        self.assertEqual(len(inventory["listeners"]), 1)
        self.assertEqual(inventory["literal_emitted_events"], {"cta_demo": 1})

    def test_flags_invalid_literal_action_but_not_sprintf_placeholder(self) -> None:
        self.write(
            "blocksy-child/page-demo.php",
            """
            <a data-track-action="404_home" data-track-category="navigation">Home</a>
            <?php $html = '<a data-track-action="nav_%s">Link</a>'; ?>
            """,
        )

        inventory = MODULE.build_inventory(self.root)
        invalid = inventory["findings"]["hard"]["invalid_action_names"]

        self.assertEqual([row["action"] for row in invalid], ["404_home"])
        self.assertEqual(inventory["dynamic_action_occurrences"], 1)
        self.assertEqual(MODULE.hard_finding_count(inventory), 1)

    def test_reports_category_collision_as_advisory(self) -> None:
        self.write(
            "blocksy-child/page-demo.php",
            """
            <a data-track-action="cta_demo" data-track-category="lead_gen">A</a>
            <a data-track-action="cta_demo" data-track-category="navigation">B</a>
            """,
        )

        inventory = MODULE.build_inventory(self.root)

        self.assertEqual(
            inventory["findings"]["advisory"]["category_collisions"],
            {"cta_demo": ["lead_gen", "navigation"]},
        )
        self.assertEqual(MODULE.hard_finding_count(inventory), 0)

    def test_rejects_path_outside_repo(self) -> None:
        with self.assertRaisesRegex(ValueError, "escapes repo root"):
            MODULE.collect_files(self.root, ["../outside"])


if __name__ == "__main__":
    unittest.main()
