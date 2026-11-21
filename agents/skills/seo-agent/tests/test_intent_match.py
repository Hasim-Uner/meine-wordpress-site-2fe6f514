#!/usr/bin/env python3
"""Regressionstests fuer ingest.py, intent_match.py und gap-report.sh --intent.

Synthetische Daten tragen bewusst "testfrage"-Queries und example-Domains.
Einzige echte Quelle ist fixtures/openseo/: eine OpenSEO-Antwort vom
2026-11-21, deren Verteilung (Jobboerse 33 %, Anbieter 26 %, Portal 26 %)
als Akzeptanzwert von Hand nachgerechnet ist.
"""

from __future__ import annotations

import json
import os
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path

TESTS = Path(__file__).resolve().parent
SKILL = TESTS.parent
INGEST = SKILL / "scripts" / "ingest.py"
MATCH = SKILL / "scripts" / "intent_match.py"
GAP = SKILL / "scripts" / "gap-report.sh"
OPENSEO = TESTS / "fixtures" / "openseo" / "serp-wordpress-freelancer-hannover.json"

PAGE = """<!doctype html><html lang="de"><head>
<title>Testfrage Alpha Leistung | Beispiel</title>
<meta name="description" content="Beschreibung">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://example.org/alpha/">
<script type="application/ld+json">{"@context":"https://schema.org","@graph":[{"@type":"Service"},{"@type":"BreadcrumbList"}]}</script>
<script type="application/ld+json">{kaputt</script>
</head><body>
<header><a href="/">Start</a></header>
<nav><a href="/alpha/">Alpha</a><a href="/beta/">Zweite Seite</a></nav>
<main><h1>Testfrage Alpha fuer Betriebe</h1><h2>Ablauf</h2>
<p>Drei Saetze echter Inhalt hier. <a href="/beta/?x=1">Mehr zur zweiten Seite</a>
<a href="/gamma/"><img src="g.png" alt="Gamma Bild"></a>
<a href="https://extern.example.com/">Extern</a> <a href="mailto:a@b.c">Mail</a></p>
<script>var ignoriert = "nicht zaehlen";</script></main>
<footer><a href="/impressum/">Impressum</a> Fusszeile Text</footer>
</body></html>"""


def run(cmd, env=None):
    merged = {**os.environ, **(env or {})}
    return subprocess.run(cmd, capture_output=True, text=True, env=merged)


def serp(keyword, rows, date="2026-01-31"):
    return {"schema": "serp.v1", "keyword": keyword, "fetched_at": date, "source": "test",
            "results": [{"position": p, "seen_in": s, "domain": d, "url": "", "title": t, "description": ""}
                        for p, s, d, t in rows], "features": []}


def crawl(path, title, h1, robots="index, follow", canonical=None, ld=("Service",), date="2026-01-31"):
    url = "https://example.org" + path
    return {"schema": "crawl.v1", "url": url, "final_url": url, "path": path, "fetched_at": date,
            "status_code": 200, "canonical": canonical if canonical is not None else url, "robots": robots,
            "title": title, "headings": [{"level": 1, "text": h1}], "json_ld_types": list(ld)}


RATGEBER = [(i, 1, f"wissen{i}.example.com", f"Was ist Testfrage {i}? Ein Leitfaden") for i in range(1, 9)]
SHARED = [(i, 1, f"shared{i}.example.com", f"Shared {i}") for i in range(1, 9)]


class IntentMatchTests(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory()
        self.root = Path(self.tmp.name)
        self.data = self.root / "research" / "2026-01" / "data"
        (self.data / "serp").mkdir(parents=True)
        (self.data / "crawl").mkdir(parents=True)
        self.ownership = self.root / "query-ownership.csv"
        self.ownership.write_text(
            "# Test-Registry\nquery;owner_path;intent;status;source;distinct_from\n"
            "testfrage alpha;/alpha/;commercial;owned;test;\n"
            "testfrage beta;/beta/;commercial;owned;test;testfrage gamma\n"
            "testfrage gamma;/gamma/;commercial;owned;test;testfrage beta\n"
            "testfrage delta;/delta/;commercial;owned;test;\n"
            "wordpress freelancer hannover;/;local;owned;test;\n", encoding="utf-8")

    def tearDown(self):
        self.tmp.cleanup()

    def put(self, kind, name, record):
        (self.data / kind / f"{name}.json").write_text(json.dumps(record), encoding="utf-8")

    def match(self, *extra):
        return run([sys.executable, str(MATCH), "--period", "2026-01", "--research-dir",
                    str(self.root / "research"), "--ownership", str(self.ownership), "--own-domain",
                    "example.org", *extra])

    # --- ingest -----------------------------------------------------------

    def test_ingest_openseo_drops_redirects_and_keeps_features(self):
        out = run([sys.executable, str(INGEST), "serp", str(OPENSEO), "--out-dir", str(self.root / "s"),
                   "--date", "2026-11-21"])
        self.assertEqual(out.returncode, 0, out.stderr)
        record = json.loads((self.root / "s" / "wordpress-freelancer-hannover.json").read_text())
        self.assertEqual(record["schema"], "serp.v1")
        self.assertEqual(len(record["results"]), 24)
        self.assertTrue(all(r["url"] == "" for r in record["results"]), "goto-URLs muessen weg")
        self.assertEqual(record["features"], ["ai_overview", "local_pack"])
        self.assertEqual(record["results"][0]["position"], 1)
        self.assertEqual(record["results"][0]["seen_in"], 2)

    def test_ingest_legacy_export(self):
        legacy = self.root / "legacy.json"
        legacy.write_text(json.dumps({"report": "phrase_organic", "database": "de", "retrieved": "2026-07-03",
                                      "serps": {"testfrage pv": {"results": [
                                          {"pos": 1, "domain": "www.a.example", "url": "https://www.a.example/x"}]}}}))
        out = run([sys.executable, str(INGEST), "serp", str(legacy), "--out-dir", str(self.root / "s")])
        self.assertEqual(out.returncode, 0, out.stderr)
        record = json.loads((self.root / "s" / "testfrage-pv.json").read_text())
        self.assertEqual(record["fetched_at"], "2026-07-03")
        self.assertEqual(record["results"][0]["domain"], "a.example")

    def test_ingest_crawl_structures_dom(self):
        page = self.root / "alpha.html"
        page.write_text(PAGE, encoding="utf-8")
        out = run([sys.executable, str(INGEST), "crawl", str(page), "--url", "https://example.org/alpha/",
                   "--out-dir", str(self.root / "c")])
        self.assertEqual(out.returncode, 0, out.stderr)
        record = json.loads((self.root / "c" / "alpha.json").read_text())
        self.assertEqual(record["canonical"], "https://example.org/alpha/")
        self.assertEqual(record["robots"], "index, follow")
        self.assertEqual(record["headings"][0], {"level": 1, "text": "Testfrage Alpha fuer Betriebe"})
        self.assertEqual(record["json_ld_types"], ["BreadcrumbList", "Service"])
        self.assertEqual(record["json_ld_errors"], 1)
        zones = record["links"]["internal_by_zone"]
        self.assertEqual(zones, {"header": 1, "nav": 2, "main": 2, "footer": 1})
        anchors = {l["href"]: l["anchor"] for l in record["links"]["internal"]}
        self.assertEqual(anchors["/gamma/"], "Gamma Bild", "img alt als Anker")
        self.assertEqual(anchors["/beta/?x=1"], "Mehr zur zweiten Seite")
        self.assertEqual(record["links"]["external_count"], 1)
        self.assertNotIn("Fusszeile", json.dumps(record), "kein Volltext im Record")
        # Wortzahl nur aus main/body: ohne Head (Title), nav/header/footer und Script.
        self.assertEqual(record["word_count"], 16)
        self.assertTrue(record["content_hash"].startswith("sha256:"))

    def test_ingest_refuses_markdown_only_firecrawl(self):
        blob = self.root / "fc.json"
        blob.write_text(json.dumps({"data": {"markdown": "# Titel", "html": "<h1>Titel</h1>"}}))
        out = run([sys.executable, str(INGEST), "crawl", str(blob), "--url", "https://example.org/",
                   "--out-dir", str(self.root / "c")])
        self.assertEqual(out.returncode, 2)
        self.assertIn("rawHtml fehlt", out.stderr)

    def test_ingest_accepts_firecrawl_rawhtml(self):
        blob = self.root / "fc.json"
        blob.write_text(json.dumps({"data": {"rawHtml": PAGE, "metadata": {"statusCode": 301,
                                                                          "url": "https://example.org/alpha/"}}}))
        out = run([sys.executable, str(INGEST), "crawl", str(blob), "--url", "https://example.org/alt/",
                   "--out-dir", str(self.root / "c")])
        self.assertEqual(out.returncode, 0, out.stderr)
        record = json.loads((self.root / "c" / "alpha.json").read_text())
        self.assertEqual(record["status_code"], 301)

    # --- intent_match ------------------------------------------------------

    def test_real_openseo_distribution(self):
        run([sys.executable, str(INGEST), "serp", str(OPENSEO), "--out-dir", str(self.data / "serp"),
             "--date", "2026-01-31"])
        self.put("crawl", "start", crawl("/", "Wordpress Freelancer Hannover", "Mehr Anfragen"))
        out = self.match("--own-domain", "hasimuener.de")
        self.assertEqual(out.returncode, 0, out.stdout + out.stderr)
        self.assertIn("GEMISCHT  wordpress freelancer hannover", out.stdout)
        self.assertIn("jobboerse 33 %, anbieter 26 %, portal 26 %", out.stdout)
        self.assertIn("eigene Pos. 10", out.stdout)

    def test_mismatch_is_hard_and_onpage_gap_named(self):
        self.put("serp", "alpha", serp("testfrage alpha", RATGEBER))
        self.put("crawl", "alpha", crawl("/alpha/", "Leistung Alpha", "Alpha fuer Betriebe"))
        out = self.match()
        self.assertEqual(out.returncode, 1)
        self.assertIn("MISMATCH  testfrage alpha", out.stdout)
        self.assertIn("SERP DOMINANT: ratgeber 100 %", out.stdout)
        self.assertIn("ONPAGE-LUECKE (fehlt in Title/H1: testfrage)", out.stdout)

    def test_noindex_canonical_and_type_conflict(self):
        anbieter = [(i, 1, f"firma{i}.example.com", f"Firma {i}") for i in range(1, 9)]
        self.put("serp", "delta", serp("testfrage delta", anbieter))
        self.put("crawl", "delta", crawl("/delta/", "Testfrage Delta", "Testfrage Delta", robots="noindex",
                                         canonical="https://example.org/anders/", ld=("BlogPosting",)))
        out = self.match()
        self.assertEqual(out.returncode, 1)
        self.assertIn("PASST     testfrage delta", out.stdout)
        self.assertIn("NOINDEX", out.stdout)
        self.assertIn("CANONICAL-FREMD (/anders/)", out.stdout)
        self.assertIn("TYP-WIDERSPRUCH (Registry anbieter, JSON-LD ratgeber)", out.stdout)

    def test_distinct_from_refuted_by_serp_overlap(self):
        self.put("serp", "beta", serp("testfrage beta", SHARED))
        self.put("serp", "gamma", serp("testfrage gamma", SHARED[:7] + [(8, 1, "anders.example.com", "x")]))
        out = self.match()
        self.assertEqual(out.returncode, 1)
        self.assertIn("ABGRENZUNG-WIDERLEGT: 'testfrage beta' (/beta/) <-> 'testfrage gamma' (/gamma/) "
                      "Ueberlappung 88 %", out.stdout)

    def test_foreign_own_url_detected_by_title(self):
        rows = [(i, 1, f"firma{i}.example.com", f"Firma {i}") for i in range(1, 9)]
        rows.append((4, 2, "example.org", "Testfrage Alpha Leistung | Beispiel"))
        self.put("serp", "alpha", serp("testfrage alpha", rows))
        self.put("crawl", "alpha", crawl("/alpha/", "Testfrage Alpha", "Testfrage Alpha"))
        self.put("crawl", "beta", crawl("/beta/", "Testfrage Alpha Leistung | Beispiel", "Zweite Seite"))
        out = self.match()
        self.assertIn("FREMDE-URL-RANKT (/beta/ auf Pos. 4 statt Owner /alpha/)", out.stdout)

    def test_unowned_keyword_gets_required_page_type(self):
        self.put("serp", "neu", serp("testfrage neu", RATGEBER))
        out = self.match()
        self.assertEqual(out.returncode, 0)
        self.assertIn("OHNE OWNER", out.stdout)
        self.assertIn("DOMINANT ratgeber", out.stdout)

    def test_missing_serp_dir_is_usage_error(self):
        (self.data / "serp").rmdir()
        out = self.match()
        self.assertEqual(out.returncode, 2)
        self.assertIn("ingest.py serp", out.stderr)

    def test_gap_report_hook_propagates_exit_code(self):
        fixtures = TESTS / "fixtures"
        research = self.root / "research"
        target = research / "2026-01" / "data"
        for item in (fixtures / "2026-01" / "data").iterdir():
            dest = target / item.name
            if item.is_dir():
                dest.mkdir(exist_ok=True)
                for f in item.iterdir():
                    (dest / f.name).write_bytes(f.read_bytes())
            else:
                dest.write_bytes(item.read_bytes())
        self.put("serp", "alpha", serp("testfrage alpha", RATGEBER))
        env = {"RESEARCH_DIR": str(research), "OWNERSHIP": str(self.ownership),
               "EXCLUSIONS": str(fixtures / "keyword-exclusions.csv")}
        plain = run(["bash", str(GAP), "2026-01", "--md"], env)
        hooked = run(["bash", str(GAP), "2026-01", "--md", "--intent"], env)
        self.assertEqual(plain.returncode, 0, plain.stderr)
        self.assertNotIn("INTENT-ABGLEICH", plain.stdout)
        self.assertTrue(hooked.stdout.startswith(plain.stdout.rstrip("\n")), "Report ohne Hook bleibt unveraendert")
        self.assertIn("## INTENT-ABGLEICH 2026-01", hooked.stdout)
        self.assertEqual(hooked.returncode, 1)


if __name__ == "__main__":
    unittest.main()
