#!/usr/bin/env python3
"""Intent-Abgleich: Crawl-Ist-Daten gegen SERP-Intents, rein rechnerisch.

Wird von `gap-report.sh --intent` aufgerufen, laeuft aber auch allein:

  intent_match.py --period 2026-09 [--md]

Liest nur normalisierte Records aus `ingest.py`:
  seo-research/<periode>/data/serp/*.json   serp.v1  (OpenSEO)
  seo-research/<periode>/data/crawl/*.json  crawl.v1 (eigene Seiten)
und die Registry `docs/seo/query-ownership.csv`.

Rechenregeln (alle Schwellen stehen unten als Konstanten):

1. Seitentyp je SERP-Treffer aus references/serp-type-rules.csv, erste Regel
   gewinnt, sonst Restklasse `anbieter`. Die eigene Domain zaehlt nicht mit.
2. Typ-Anteil = Summe seen_in des Typs / Summe seen_in aller Treffer mit
   Position <= 10. OpenSEO liest jede SERP mehrfach; seen_in gewichtet stabile
   Treffer staerker als einmal gesehene.
3. Lage: SPLIT wenn die zwei groessten Typen je >= 0,4 (auch bei 60:40,
   damit ein Typ mit 40 % nicht als Verlierer gilt); sonst DOMINANT ab 0,6;
   sonst GEMISCHT. Liegt mehr als die Haelfte des Gewichts in der Restklasse,
   ist die Verteilung RESTKLASSE-HOCH und das Urteil nur eingeschraenkt lesbar.
4. Unser Typ aus der Registry-Spalte `intent` (informational -> ratgeber,
   sonst anbieter), gegengeprueft mit JSON-LD aus dem Crawl.
5. Urteil: PASST (dominanter Typ = unserer) | SPLIT (unserer unter den zwei
   groessten) | GEMISCHT (keine Dominanz, unser Anteil >= 0,2) | MISMATCH.
6. On-Page: jedes Query-Token muss in Title oder H1 vorkommen (Praefix-Treffer
   ab 4 Zeichen, z. B. lead/leads). Fehlende Tokens werden genannt.
7. Abgrenzung: Ueberlappung = |A ∩ B| / min(|A|, |B|) der Top-10-Domains,
   nur wenn beide Mengen mindestens 5 Fremddomains haben; sonst traegt eine
   einzelne Domain 100 %. >= 0,7 heisst gleiche Intention, <= 0,1 heisst
   getrennt (Schwellen nach SERP-Overlap-Clustering: 7 von 10 gleich, 0-1 von
   10 getrennt). Nur Paare mit verschiedenen Ownern werden auf Abgrenzung
   geprueft; `distinct_from` innerhalb desselben Owners trennt Schreibvarianten
   und erwartet Ueberlappung.

Hart sind nur technische Befunde, die eine Seite aus dem Index nehmen:
NICHT-ERREICHBAR, NOINDEX, CANONICAL-FREMD. Intent-Urteile (MISMATCH,
ABGRENZUNG-WIDERLEGT, OWNER-SPLIT-FRAGLICH) sind Pruefbefunde: Die Schwellen
sind nicht gegen manuell gepruefte Suchintentionen kalibriert und loesen eine
Sichtung aus, kein Gate. Exit 0 ohne harten Befund, 1 mit, 2 bei fehlenden
Daten oder Bedienfehlern.
"""

from __future__ import annotations

import argparse
import csv
import json
import os
import re
import sys
from datetime import date
from pathlib import Path
from urllib.parse import urlsplit

HERE = Path(__file__).resolve().parent
REPO_ROOT = HERE.parents[3]
RULES = HERE.parent / "references" / "serp-type-rules.csv"

TOP_N = 10
DOMINANT = 0.6
SPLIT = 0.4
MIN_OWN_SHARE = 0.2
SAME_INTENT = 0.7
SEPARATE_INTENT = 0.1
MIN_OVERLAP_DOMAINS = 5
MAX_FALLBACK_SHARE = 0.5
MAX_AGE_GAP_DAYS = 30
HARD = {"NICHT-ERREICHBAR", "NOINDEX", "CANONICAL-FREMD"}
REVIEW = {"MISMATCH", "ABGRENZUNG-WIDERLEGT", "OWNER-SPLIT-FRAGLICH", "BUENDELUNG-FRAGLICH",
          "FREMDE-URL-RANKT", "TYP-WIDERSPRUCH"}

ARTICLE_TYPES = {"Article", "BlogPosting", "NewsArticle", "TechArticle"}
SERVICE_TYPES = {"Service", "ProfessionalService", "Product", "Offer", "LocalBusiness"}
FOLD = {"ä": "ae", "ö": "oe", "ü": "ue", "ß": "ss"}
STOP = {"und", "oder", "fuer", "der", "die", "das", "im", "in", "mit", "ohne", "vs", "von", "zu"}


def fold(text: str) -> str:
    out = "".join(FOLD.get(ch, ch) for ch in (text or "").lower())
    return re.sub(r"[^a-z0-9]+", " ", out).strip()


def toks(text: str) -> list[str]:
    return [t for t in fold(text).split() if t and t not in STOP and len(t) > 1]


def covered(token: str, field_tokens: set[str]) -> bool:
    if token in field_tokens:
        return True
    return any(min(len(token), len(f)) >= 4 and (f.startswith(token) or token.startswith(f))
               for f in field_tokens)


def norm_path(path: str) -> str:
    path = urlsplit(path).path if "://" in path else path
    return "/" + path.strip("/") + ("/" if path.strip("/") else "")


def read_rules(path: Path) -> list[tuple[str, str, re.Pattern]]:
    lines = [ln for ln in path.read_text(encoding="utf-8").splitlines() if ln.strip() and not ln.startswith("#")]
    rules = []
    for row in csv.DictReader(lines, delimiter=";"):
        rules.append((row["type"], row["field"], re.compile(row["pattern"], re.I)))
    return rules


def classify(result: dict, rules) -> tuple[str, bool]:
    fields = {"domain": result["domain"], "title": result["title"],
              "text": f"{result['title']} {result['description']}"}
    for kind, field, pattern in rules:
        if pattern.search(fields.get(field, "")):
            return kind, True
    return "anbieter", False


def load_json_dir(path: Path, schema: str) -> list[dict]:
    records = []
    for file in sorted(path.glob("*.json")):
        try:
            blob = json.loads(file.read_text(encoding="utf-8"))
        except ValueError:
            print(f"WARNUNG: {file} ist kein JSON, uebersprungen.", file=sys.stderr)
            continue
        if blob.get("schema") == schema:
            blob["_file"] = file
            records.append(blob)
    return records


def read_registry(path: Path) -> list[dict]:
    lines = [ln for ln in path.read_text(encoding="utf-8").splitlines() if ln.strip() and not ln.startswith("#")]
    return [r for r in csv.DictReader(lines, delimiter=";") if r.get("query") and r.get("status", "owned") == "owned"]


def pct(value: float) -> str:
    return f"{round(value * 100)} %"


def analyse_serp(record: dict, rules, own_domain: str) -> dict:
    weights: dict[str, float] = {}
    fallback = 0.0
    own_position, own_title = None, ""
    domains = set()
    for result in record["results"]:
        if result["domain"] == own_domain or result["domain"].endswith("." + own_domain):
            if own_position is None or result["position"] < own_position:
                own_position, own_title = result["position"], result.get("title") or ""
            continue
        if result["position"] > TOP_N:
            continue
        kind, by_rule = classify(result, rules)
        weight = max(1, int(result.get("seen_in") or 1))
        weights[kind] = weights.get(kind, 0) + weight
        if not by_rule:
            fallback += weight
        domains.add(result["domain"])
    total = sum(weights.values())
    shares = {k: v / total for k, v in weights.items()} if total else {}
    ranked = sorted(shares.items(), key=lambda kv: (-kv[1], kv[0]))
    if not ranked:
        shape, leaders = "LEER", []
    elif len(ranked) > 1 and ranked[1][1] >= SPLIT:
        shape, leaders = "SPLIT", [ranked[0][0], ranked[1][0]]
    elif ranked[0][1] >= DOMINANT:
        shape, leaders = "DOMINANT", [ranked[0][0]]
    else:
        shape, leaders = "GEMISCHT", []
    titled = sum(1 for r in record["results"] if r.get("title"))
    return {"shares": shares, "ranked": ranked, "shape": shape, "leaders": leaders,
            "own_position": own_position, "own_title": own_title, "domains": domains,
            "fallback_share": fallback / total if total else 0.0,
            "untitled": titled == 0}


def verdict(our_type: str, serp: dict) -> str:
    share = serp["shares"].get(our_type, 0.0)
    if serp["shape"] == "DOMINANT":
        return "PASST" if serp["leaders"][0] == our_type else "MISMATCH"
    if serp["shape"] == "SPLIT":
        return "SPLIT" if our_type in serp["leaders"] else "MISMATCH"
    if serp["shape"] == "GEMISCHT":
        return "GEMISCHT" if share >= MIN_OWN_SHARE else "MISMATCH"
    return "KEINE-SERP"


def crawl_findings(query: str, owner: str, our_type: str, crawl: dict | None) -> list[str]:
    if crawl is None:
        return ["KEIN-CRAWL"]
    found = []
    if crawl.get("status_code") != 200:
        found.append(f"NICHT-ERREICHBAR (Status {crawl.get('status_code')})")
    if "noindex" in (crawl.get("robots") or "").lower():
        found.append("NOINDEX")
    canonical = crawl.get("canonical") or ""
    if canonical and norm_path(canonical) != norm_path(owner):
        found.append(f"CANONICAL-FREMD ({norm_path(canonical)})")
    fields = set(toks(crawl.get("title", ""))) | {
        t for h in crawl.get("headings", []) if h.get("level") == 1 for t in toks(h.get("text", ""))}
    missing = [t for t in toks(query) if not covered(t, fields)]
    if missing:
        found.append("ONPAGE-LUECKE (fehlt in Title/H1: %s)" % ", ".join(missing))
    types = set(crawl.get("json_ld_types") or [])
    schema_type = "ratgeber" if types & ARTICLE_TYPES else "anbieter" if types & SERVICE_TYPES else None
    if schema_type and schema_type != our_type:
        found.append(f"TYP-WIDERSPRUCH (Registry {our_type}, JSON-LD {schema_type})")
    return found


def days_between(a: str, b: str) -> int | None:
    try:
        return abs((date.fromisoformat(a) - date.fromisoformat(b)).days)
    except (TypeError, ValueError):
        return None


def overlap(a: set[str], b: set[str]) -> float | None:
    if min(len(a), len(b)) < MIN_OVERLAP_DOMAINS:
        return None
    return len(a & b) / min(len(a), len(b))


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("--period")
    parser.add_argument("--research-dir", default=os.environ.get("RESEARCH_DIR", str(REPO_ROOT / "seo-research")))
    parser.add_argument("--ownership", default=os.environ.get("OWNERSHIP", str(REPO_ROOT / "docs/seo/query-ownership.csv")))
    parser.add_argument("--own-domain", default=os.environ.get("OWN_DOMAIN", "hasimuener.de"))
    parser.add_argument("--rules", default=str(RULES))
    parser.add_argument("--md", action="store_true")
    args = parser.parse_args()

    research = Path(args.research_dir)
    period = args.period or max((p.name for p in research.iterdir() if re.fullmatch(r"\d{4}-\d{2}", p.name)), default="")
    data = research / period / "data"
    serp_dir, crawl_dir = data / "serp", data / "crawl"
    if not period or not serp_dir.is_dir():
        print(f"FEHLER: {serp_dir} fehlt. Erst SERP holen (OpenSEO get_serp_results) und "
              f"mit ingest.py serp --period {period or '<JJJJ-MM>'} ablegen.", file=sys.stderr)
        return 2

    rules = read_rules(Path(args.rules))
    serps = {fold(r["keyword"]): r for r in load_json_dir(serp_dir, "serp.v1")}
    crawls = {norm_path(r.get("final_url") or r["url"]): r
              for r in load_json_dir(crawl_dir, "crawl.v1")} if crawl_dir.is_dir() else {}
    registry = read_registry(Path(args.ownership))
    owned = {fold(r["query"]): r for r in registry}
    if not serps:
        print(f"FEHLER: keine serp.v1-Records in {serp_dir}.", file=sys.stderr)
        return 2

    analysed = {q: analyse_serp(rec, rules, args.own_domain) for q, rec in serps.items()}
    rows, hard, review, thin_pairs = [], 0, 0, 0
    for q, reg in owned.items():
        if q not in serps:
            continue
        owner = norm_path(reg["owner_path"])
        our_type = "ratgeber" if reg.get("intent") == "informational" else "anbieter"
        serp = analysed[q]
        result = verdict(our_type, serp)
        crawl = crawls.get(owner)
        findings = crawl_findings(reg["query"], owner, our_type, crawl)
        if crawl is not None:
            gap = days_between(serps[q].get("fetched_at"), crawl.get("fetched_at"))
            if gap is not None and gap > MAX_AGE_GAP_DAYS:
                findings.append(f"ZEITVERSATZ ({gap} Tage zwischen SERP und Crawl)")
        # Welche eigene URL rankt? Der SERP-Title wird exakt (gefaltet) gegen die
        # gecrawlten Titles gehalten. Kein Treffer heisst: unbekannt, nicht geraten.
        ranking = [path for path, rec in crawls.items()
                   if serp["own_title"] and fold(rec.get("title", "")) == fold(serp["own_title"])]
        if len(ranking) == 1 and ranking[0] != owner:
            findings.append(f"FREMDE-URL-RANKT ({ranking[0]} auf Pos. {serp['own_position']} statt Owner {owner})")
        if serp["fallback_share"] > MAX_FALLBACK_SHARE:
            findings.append(f"RESTKLASSE-HOCH ({pct(serp['fallback_share'])} ohne Regeltreffer: "
                            "Urteil nur eingeschraenkt lesbar, Regeldatei ergaenzen)")
        if serp["untitled"]:
            findings.append("NUR-DOMAINS (Alt-Export ohne Titel: Typen nur per Domain-Regel)")
        hard += sum(f.split(" ")[0] in HARD for f in findings)
        review += (result in REVIEW) + sum(f.split(" ")[0] in REVIEW for f in findings)
        rows.append((reg, owner, our_type, serp, result, findings))

    pair_findings = []
    keys = sorted(q for q in owned if q in analysed)
    for i, a in enumerate(keys):
        for b in keys[i + 1:]:
            ratio = overlap(analysed[a]["domains"], analysed[b]["domains"])
            if ratio is None:
                thin_pairs += 1
                continue
            ra, rb = owned[a], owned[b]
            distinct = {fold(x) for x in (ra.get("distinct_from") or "").split("|")} | \
                       {fold(x) for x in (rb.get("distinct_from") or "").split("|")}
            same_owner = norm_path(ra["owner_path"]) == norm_path(rb["owner_path"])
            label = None
            if ratio >= SAME_INTENT and not same_owner and (b in distinct or a in distinct):
                label = "ABGRENZUNG-WIDERLEGT"
            elif ratio >= SAME_INTENT and not same_owner:
                label = "OWNER-SPLIT-FRAGLICH"
            elif ratio <= SEPARATE_INTENT and same_owner:
                label = "BUENDELUNG-FRAGLICH"
            if label:
                review += label in REVIEW
                pair_findings.append((label, ra, rb, ratio))

    unowned = [(q, serps[q], analysed[q]) for q in sorted(serps) if q not in owned]
    missing_serp = sorted(r["query"] for q, r in owned.items() if q not in serps)

    def dist(serp: dict) -> str:
        return ", ".join(f"{k} {pct(v)}" for k, v in serp["ranked"][:3]) or "-"

    out = []
    dates = sorted({r.get("fetched_at", "?") for r in serps.values()})
    head = (f"SERP {len(serps)} Keywords ({dates[0]} bis {dates[-1]}), Crawl {len(crawls)} Seiten, "
            f"Registry {len(owned)} Queries, davon {len(rows)} mit SERP. Regeln: {Path(args.rules).name}")
    if args.md:
        out += [f"## INTENT-ABGLEICH {period}\n", head + "\n",
                "| Query | Owner | Typ | SERP-Lage | Verteilung | Urteil | Eigene Pos. | Befunde |",
                "| --- | --- | --- | --- | --- | --- | ---: | --- |"]
        for reg, owner, our_type, serp, result, findings in rows:
            out.append("| %s | %s | %s | %s | %s | %s | %s | %s |" % (
                reg["query"], owner, our_type, serp["shape"], dist(serp), result,
                serp["own_position"] or "–", "; ".join(findings) or "–"))
        out.append("")
    else:
        out += [f"=== Intent-Abgleich {period} ===", head, ""]
        for reg, owner, our_type, serp, result, findings in rows:
            pos = serp["own_position"] or "-"
            out.append(f"  {result:<9} {reg['query'][:38]:<38} {owner}  (Typ {our_type}, eigene Pos. {pos})")
            out.append(f"            SERP {serp['shape']}: {dist(serp)}; Restklasse {pct(serp['fallback_share'])}")
            for finding in findings:
                out.append(f"            - {finding}")
        out.append("")

    if pair_findings:
        out.append("--- ABGRENZUNG (Top-10-Domain-Ueberlappung) ---" if not args.md else "### Abgrenzung\n")
        for label, ra, rb, ratio in pair_findings:
            out.append(f"  {label}: '{ra['query']}' ({norm_path(ra['owner_path'])}) <-> "
                       f"'{rb['query']}' ({norm_path(rb['owner_path'])}) Ueberlappung {pct(ratio)}")
        out.append("")
    if unowned:
        out.append("--- OHNE OWNER: welcher Seitentyp gewinnt? ---" if not args.md else "### Ohne Owner\n")
        for q, rec, serp in unowned:
            lead = serp["leaders"][0] if serp["shape"] == "DOMINANT" else "/".join(serp["leaders"]) or "kein Typ"
            out.append(f"  {rec['keyword'][:40]:<40} {serp['shape']} {lead}: {dist(serp)}")
        out.append("  Vor dem Bauen: intent-gate.sh check. Der Seitentyp oben ist Pflicht, kein Vorschlag.")
        out.append("")
    if missing_serp:
        out.append(f"Ohne SERP-Daten: {len(missing_serp)} Registry-Queries (nicht bewertet, nicht geraten).")
    if thin_pairs:
        out.append(f"Abgrenzung nicht pruefbar: {thin_pairs} Paare mit weniger als "
                   f"{MIN_OVERLAP_DOMAINS} Fremddomains in einer SERP.")
    out.append(f"Harte Befunde: {hard}. Pruefbefunde: {review} (Sichtung, kein Gate).")
    print("\n".join(out))
    return 1 if hard else 0


if __name__ == "__main__":
    sys.exit(main())
