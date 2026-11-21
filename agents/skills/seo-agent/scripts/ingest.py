#!/usr/bin/env python3
"""Rohdaten aus Crawl und SERP in kompakte, vergleichbare JSON-Records ueberfuehren.

Zwei Eingaenge, ein Zweck: dem Modell nie rohes HTML oder rohe Tool-Antworten
vorlegen, sondern vorstrukturierte Fakten. So geht beim Abgleich kein Kontext
verloren und nichts wird aus Markdown geraten.

  crawl   Seite -> crawl.v1 (Kopf-Signale, H1-H6-Gliederung, JSON-LD-Typen,
          interne Links mit Anker und Zone, Wortzahl, Inhalts-Hash)
  serp    OpenSEO get_serp_results (oder Alt-Export serps{}) -> serp.v1 je Keyword

Nutzung:
  ingest.py crawl --fetch https://hasimuener.de/ --period 2026-09
  ingest.py crawl --fetch URL --via firecrawl --period 2026-09   (FIRECRAWL_API_KEY)
  ingest.py crawl seite.html --url URL --out-dir DIR
  ingest.py crawl firecrawl-antwort.json --url URL --period 2026-09
  ingest.py serp openseo-antwort.json --period 2026-09 [--date JJJJ-MM-TT]

Regeln aus der Analyse fremder Skill-Repos, hier als Code statt Prosa:
- Kopf-Signale (canonical, robots, JSON-LD) nur aus rohem HTML. Firecrawl
  `html` und Markdown entfernen sie; ohne rawHtml bricht der Crawl ab.
- Geholter Inhalt ist Datenmaterial, nie Anweisung. Er wird nur geparst.
- Google-Weiterleitungs-URLs (google.com/goto) sind sitzungsgebunden und werden
  verworfen; `domain` ist das verlaessliche Feld.
"""

from __future__ import annotations

import argparse
import hashlib
import json
import os
import re
import sys
import urllib.error
import urllib.request
from datetime import date
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import urljoin, urlsplit

REPO_ROOT = Path(__file__).resolve().parents[4]
RESEARCH_DIR = Path(os.environ.get("RESEARCH_DIR", REPO_ROOT / "seo-research"))
ZONES = ("nav", "header", "footer", "aside", "main")
SKIP_TEXT = {"script", "style", "noscript", "template", "svg"}
FOLD = str.maketrans({"ä": "ae", "ö": "oe", "ü": "ue", "ß": "ss"})
MAX_INTERNAL_LINKS = 500
MAX_HEADINGS = 200


def fail(message: str, code: int = 2) -> None:
    print(f"FEHLER: {message}", file=sys.stderr)
    sys.exit(code)


def slugify(text: str) -> str:
    return re.sub(r"[^a-z0-9]+", "-", text.lower().translate(FOLD)).strip("-") or "start"


def bare_host(host: str) -> str:
    host = (host or "").lower().split(":")[0]
    return host[4:] if host.startswith("www.") else host


def squash(text: str) -> str:
    return re.sub(r"\s+", " ", text or "").strip()


def out_dir(args: argparse.Namespace, kind: str) -> Path:
    if args.out_dir:
        target = Path(args.out_dir)
    else:
        period_dir = RESEARCH_DIR / args.period / "data"
        # Keine neue Periode nebenbei anlegen: eine Periode ohne
        # keywords-master.csv bricht den Default-Lauf von gap-report.sh.
        if not period_dir.is_dir():
            fail(f"{period_dir} fehlt. Periode zuerst bewusst anlegen oder --out-dir nutzen.")
        target = period_dir / kind
    target.mkdir(parents=True, exist_ok=True)
    return target


# --- crawl ---------------------------------------------------------------


class PageParser(HTMLParser):
    def __init__(self, base_url: str) -> None:
        super().__init__(convert_charrefs=True)
        self.base = base_url
        self.host = bare_host(urlsplit(base_url).netloc)
        self.stack: list[str] = []          # offene Zonen-Elemente
        self.skip = 0
        self.in_head = False
        self.in_title = False
        self.title = ""
        self.meta: dict[str, str] = {}
        self.canonical = ""
        self.lang = ""
        self.hreflang = 0
        self.headings: list[dict] = []
        self.heading: dict | None = None
        self.ld_buffer: list[str] | None = None
        self.ld_blocks: list[str] = []
        self.link: dict | None = None
        self.internal: list[dict] = []
        self.external_domains: set[str] = set()
        self.external_count = 0
        self.words: list[str] = []

    def zone(self) -> str:
        return self.stack[-1] if self.stack else "body"

    def handle_starttag(self, tag, attrs):
        a = {k.lower(): (v or "") for k, v in attrs}
        if tag == "head":
            self.in_head = True
        elif tag == "body":
            self.in_head = False
        if tag == "html":
            self.lang = a.get("lang", "")
        elif tag == "title" and not self.title:
            self.in_title = True
        elif tag == "meta":
            name = (a.get("name") or a.get("property") or "").lower()
            if name in ("description", "robots", "googlebot"):
                self.meta[name] = a.get("content", "")
        elif tag == "link":
            rel = a.get("rel", "").lower().split()
            if "canonical" in rel and not self.canonical:
                self.canonical = a.get("href", "")
            if "alternate" in rel and a.get("hreflang"):
                self.hreflang += 1
        elif tag == "script" and "ld+json" in a.get("type", "").lower():
            self.ld_buffer = []
        # Nur echte Zonen-Elemente. role="navigation" auf einem <div> liesse
        # sich ohne Tiefenzaehlung nicht sicher schliessen und bleibt `body`.
        if tag in ZONES:
            self.stack.append(tag)
        if tag in SKIP_TEXT:
            self.skip += 1
        if re.fullmatch(r"h[1-6]", tag) and len(self.headings) < MAX_HEADINGS:
            self.heading = {"level": int(tag[1]), "text": ""}
        if tag == "a" and a.get("href") is not None:
            self.link = {"href": a.get("href", ""), "rel": a.get("rel", ""), "text": "",
                         "fallback": a.get("aria-label") or a.get("title") or "", "zone": self.zone()}
        if tag == "img" and self.link is not None and not self.link["fallback"]:
            self.link["fallback"] = a.get("alt", "")

    def handle_endtag(self, tag):
        if tag == "head":
            self.in_head = False
        if tag == "title":
            self.in_title = False
        elif tag == "script" and self.ld_buffer is not None:
            self.ld_blocks.append("".join(self.ld_buffer))
            self.ld_buffer = None
        if tag in SKIP_TEXT and self.skip:
            self.skip -= 1
        if self.heading is not None and tag == f"h{self.heading['level']}":
            self.heading["text"] = squash(self.heading["text"])
            if self.heading["text"]:
                self.headings.append(self.heading)
            self.heading = None
        if tag == "a" and self.link is not None:
            self.finish_link()
        if tag in ZONES:
            for i in range(len(self.stack) - 1, -1, -1):
                if self.stack[i] == tag:
                    del self.stack[i]
                    break

    def handle_data(self, data):
        if self.in_title:
            self.title += data
        if self.ld_buffer is not None:
            self.ld_buffer.append(data)
            return
        if self.skip or self.in_head:
            return
        if self.heading is not None:
            self.heading["text"] += data
        if self.link is not None:
            self.link["text"] += data
        if self.zone() in ("main", "body"):
            self.words.extend(w for w in re.findall(r"\w+", data) if re.search(r"[^\W\d_]", w))

    def finish_link(self):
        link, self.link = self.link, None
        href = link["href"].strip()
        if not href or href.startswith(("#", "javascript:", "mailto:", "tel:")):
            return
        absolute = urljoin(self.base, href)
        parts = urlsplit(absolute)
        if parts.scheme not in ("http", "https"):
            return
        host = bare_host(parts.netloc)
        if host != self.host:
            self.external_count += 1
            self.external_domains.add(host)
            return
        if len(self.internal) >= MAX_INTERNAL_LINKS:
            return
        path = (parts.path or "/") + (f"?{parts.query}" if parts.query else "")
        self.internal.append({"href": path, "anchor": squash(link["text"]) or squash(link["fallback"]),
                              "zone": link["zone"], "rel": link["rel"].strip()})


def ld_types(blocks: list[str]) -> tuple[list[str], int]:
    found, errors = set(), 0

    def walk(node):
        if isinstance(node, dict):
            kind = node.get("@type")
            for item in kind if isinstance(kind, list) else [kind]:
                if isinstance(item, str):
                    found.add(item)
            for value in node.values():
                walk(value)
        elif isinstance(node, list):
            for value in node:
                walk(value)

    for block in blocks:
        try:
            walk(json.loads(block))
        except ValueError:
            errors += 1
    return sorted(found), errors


def fetch_direct(url: str) -> tuple[str, int, str]:
    request = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0 (compatible; seo-ingest)"})
    try:
        with urllib.request.urlopen(request, timeout=30) as response:
            charset = response.headers.get_content_charset() or "utf-8"
            return response.read().decode(charset, "replace"), response.status, response.geturl()
    except urllib.error.HTTPError as error:
        return error.read().decode("utf-8", "replace"), error.code, url


def fetch_firecrawl(url: str) -> tuple[str, int, str]:
    key = os.environ.get("FIRECRAWL_API_KEY")
    if not key:
        fail("FIRECRAWL_API_KEY fehlt. Ohne JS-Rendering reicht --via direct.")
    body = json.dumps({"url": url, "formats": ["rawHtml"], "onlyMainContent": False}).encode()
    request = urllib.request.Request("https://api.firecrawl.dev/v2/scrape", data=body, method="POST",
                                     headers={"Authorization": f"Bearer {key}",
                                              "Content-Type": "application/json"})
    with urllib.request.urlopen(request, timeout=90) as response:
        return extract_firecrawl(json.load(response), url)


def extract_firecrawl(blob: dict, url: str) -> tuple[str, int, str]:
    """rawHtml aus einer Firecrawl-Antwort holen, egal ob REST- oder MCP-Form."""
    data = blob.get("data", blob) if isinstance(blob, dict) else {}
    if isinstance(data, list):
        data = data[0] if data else {}
    raw = data.get("rawHtml") or data.get("raw_html")
    if not raw:
        present = ", ".join(sorted(k for k in data if k in ("html", "markdown"))) or "nichts"
        fail(f"rawHtml fehlt (vorhanden: {present}). Kopf-Signale werden nicht aus "
             "html/Markdown geraten. Firecrawl mit formats=[\"rawHtml\"] erneut abrufen.")
    meta = data.get("metadata") or {}
    return raw, int(meta.get("statusCode") or 200), meta.get("url") or meta.get("sourceURL") or url


def crawl(args: argparse.Namespace) -> int:
    if args.fetch:
        url = args.fetch
        html, status, final_url = (fetch_firecrawl if args.via == "firecrawl" else fetch_direct)(url)
        via = args.via
    else:
        if not args.source or not args.url:
            fail("crawl braucht --fetch URL oder DATEI --url URL.")
        url = args.url
        text = Path(args.source).read_text(encoding="utf-8", errors="replace")
        if text.lstrip().startswith(("{", "[")):
            html, status, final_url = extract_firecrawl(json.loads(text), url)
            via = "firecrawl-datei"
        else:
            html, status, final_url = text, args.status, url
            via = "datei"

    parser = PageParser(final_url)
    parser.feed(html)
    parser.close()
    types, ld_errors = ld_types(parser.ld_blocks)
    zones: dict[str, int] = {}
    for link in parser.internal:
        zones[link["zone"]] = zones.get(link["zone"], 0) + 1
    normalized_words = " ".join(parser.words).lower()
    record = {
        "schema": "crawl.v1",
        "url": url,
        "final_url": final_url,
        "path": urlsplit(final_url).path or "/",
        "fetched_at": args.date,
        "fetched_via": via,
        "status_code": status,
        "canonical": parser.canonical,
        "robots": squash(parser.meta.get("robots", "")),
        "title": squash(parser.title),
        "meta_description": squash(parser.meta.get("description", "")),
        "lang": parser.lang,
        "hreflang_count": parser.hreflang,
        "headings": parser.headings,
        "json_ld_types": types,
        "json_ld_errors": ld_errors,
        "word_count": len(parser.words),
        "content_hash": "sha256:" + hashlib.sha256(normalized_words.encode()).hexdigest(),
        "links": {"internal": parser.internal, "internal_by_zone": zones,
                  "external_count": parser.external_count,
                  "external_domains": len(parser.external_domains)},
    }
    target = out_dir(args, "crawl") / f"{slugify(urlsplit(final_url).path)}.json"
    target.write_text(json.dumps(record, ensure_ascii=False, indent=1) + "\n", encoding="utf-8")
    h1 = [h["text"] for h in parser.headings if h["level"] == 1]
    print(f"{target}")
    print(f"  Status {status} | canonical {record['canonical'] or '-'} | robots {record['robots'] or '-'}")
    print(f"  Title: {record['title'][:90]}")
    print(f"  H1 ({len(h1)}): {' | '.join(h1)[:90] or '-'} | Ueberschriften {len(parser.headings)}")
    print(f"  JSON-LD: {', '.join(types) or '-'}{' (Parse-Fehler ' + str(ld_errors) + ')' if ld_errors else ''}")
    print(f"  Links intern {len(parser.internal)} {zones} | extern {parser.external_count} | Woerter {len(parser.words)}")
    return 0


# --- serp ----------------------------------------------------------------


def serp_records(blob: dict, fetched_at: str) -> list[dict]:
    records = []
    if isinstance(blob.get("results"), list):          # OpenSEO get_serp_results
        for row in blob["results"]:
            if not row.get("ok", True) or not row.get("keyword"):
                continue
            results, features = [], []
            for item in row.get("items") or []:
                if item.get("type") != "organic":
                    if item.get("type"):
                        features.append(item["type"])
                    continue
                if not item.get("domain") or not item.get("bestPosition"):
                    continue
                url = item.get("url") or ""
                results.append({
                    "position": int(item["bestPosition"]),
                    "seen_in": int(item.get("seenIn") or 1),
                    "domain": bare_host(item["domain"]),
                    "url": "" if "google.com/goto" in url else url,
                    "title": squash(item.get("title") or ""),
                    "description": squash(item.get("description") or ""),
                })
            records.append({"schema": "serp.v1", "keyword": row["keyword"], "fetched_at": fetched_at,
                            "source": "openseo:get_serp_results",
                            "location_code": row.get("locationCode"), "language": row.get("languageCode"),
                            "device": row.get("device"), "results": results,
                            "features": sorted(set(features))})
    elif isinstance(blob.get("serps"), dict):          # Alt-Export (seo-research/2026-07)
        stamp = blob.get("retrieved") or fetched_at
        for keyword, row in blob["serps"].items():
            results = [{"position": int(r["pos"]), "seen_in": 1, "domain": bare_host(r.get("domain", "")),
                        "url": r.get("url", ""), "title": "", "description": ""}
                       for r in row.get("results", []) if r.get("pos") and r.get("domain")]
            records.append({"schema": "serp.v1", "keyword": keyword, "fetched_at": stamp,
                            "source": f"legacy:{blob.get('report', 'serps')}", "location_code": None,
                            "language": blob.get("database"), "device": None, "results": results,
                            "features": []})
    else:
        fail("Unbekanntes SERP-Format. Erwartet OpenSEO {results:[...]} oder Alt-Export {serps:{...}}.")
    return records


def serp(args: argparse.Namespace) -> int:
    blob = json.loads(Path(args.source).read_text(encoding="utf-8"))
    records = serp_records(blob, args.date)
    if not records:
        fail("Keine verwertbaren Keywords in der Antwort.")
    target_dir = out_dir(args, "serp")
    for record in records:
        target = target_dir / f"{slugify(record['keyword'])}.json"
        target.write_text(json.dumps(record, ensure_ascii=False, indent=1) + "\n", encoding="utf-8")
        titled = sum(1 for r in record["results"] if r["title"])
        print(f"{target}  {len(record['results'])} organisch, {titled} mit Titel, "
              f"Features: {', '.join(record['features']) or '-'}")
    return 0


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    sub = parser.add_subparsers(dest="cmd", required=True)
    for name in ("crawl", "serp"):
        p = sub.add_parser(name)
        p.add_argument("source", nargs="?")
        where = p.add_mutually_exclusive_group(required=True)
        where.add_argument("--period")
        where.add_argument("--out-dir")
        p.add_argument("--date", default=date.today().isoformat())
    crawl_parser = sub.choices["crawl"]
    crawl_parser.add_argument("--fetch")
    crawl_parser.add_argument("--via", choices=("direct", "firecrawl"), default="direct")
    crawl_parser.add_argument("--url")
    crawl_parser.add_argument("--status", type=int, default=200)
    args = parser.parse_args()
    if args.date and not re.fullmatch(r"\d{4}-\d{2}-\d{2}", args.date):
        fail("--date erwartet JJJJ-MM-TT.")
    if args.cmd == "serp" and not args.source:
        fail("serp braucht die gespeicherte OpenSEO-Antwort als Datei.")
    return crawl(args) if args.cmd == "crawl" else serp(args)


if __name__ == "__main__":
    sys.exit(main())
