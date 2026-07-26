#!/usr/bin/env bash
#
# Gap-Report: Keyword-Universum <-> eigene Seiten <-> Wettbewerber.
#
# Macht aus der Handarbeit von gap-analyse.md (2026-07-03) einen wiederholbaren
# Lauf. Das Skript rechnet ausschliesslich mit vorhandenen Exporten in
# seo-research/<periode>/data/ — es ruft keine API und erfindet keine Volumina.
# Fehlende Daten werden als fehlend ausgewiesen, nicht geschaetzt.
#
# Nutzung:
#   gap-report.sh                      neueste Periode unter seo-research/
#   gap-report.sh 2026-07              bestimmte Periode
#   gap-report.sh 2026-07 --md         Markdown statt Konsolenausgabe
#
# Erwartete Dateien in seo-research/<periode>/data/:
#   keywords-master.csv                keyword,volume,kd,cpc,intent,cluster,quelle
#   gsc/*.csv                          SEO-Cockpit-Export (Semikolon)
#   comp-<domain>.json                 optional, Wettbewerber-Rankings

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd)"
RESEARCH_DIR="$REPO_ROOT/seo-research"

PERIOD=""
FORMAT="text"
for arg in "$@"; do
  case "$arg" in
    --md|--markdown) FORMAT="md" ;;
    --help|-h) sed -n '2,20p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'; exit 0 ;;
    *) PERIOD="$arg" ;;
  esac
done

if [[ -z "$PERIOD" ]]; then
  PERIOD="$(ls -1 "$RESEARCH_DIR" 2>/dev/null | grep -E '^[0-9]{4}-[0-9]{2}$' | sort | tail -1 || true)"
fi

DATA_DIR="$RESEARCH_DIR/$PERIOD/data"
if [[ -z "$PERIOD" || ! -d "$DATA_DIR" ]]; then
  echo "FEHLER: Keine Datenperiode gefunden. Erwartet: seo-research/<JJJJ-MM>/data/" >&2
  exit 2
fi

DATA_DIR="$DATA_DIR" PERIOD="$PERIOD" FORMAT="$FORMAT" \
OWNERSHIP="$REPO_ROOT/docs/seo/query-ownership.csv" \
EXCLUSIONS="$REPO_ROOT/docs/seo/keyword-exclusions.csv" \
python3 <<'PY'
import csv, glob, json, os, re, sys
from collections import defaultdict

data_dir = os.environ["DATA_DIR"]
period = os.environ["PERIOD"]
fmt = os.environ["FORMAT"]

FOLD = {"ä": "ae", "ö": "oe", "ü": "ue", "ß": "ss"}
STOP = {"und", "oder", "fuer", "der", "die", "das", "im", "in", "mit", "ohne", "vs", "von", "zu"}


def fold(text):
    out = "".join(FOLD.get(ch, ch) for ch in (text or "").lower())
    return re.sub(r"[^a-z0-9]+", " ", out).strip()


def toks(text):
    return {t for t in fold(text).split() if t and t not in STOP and len(t) > 1}


def read_semicolon_csv(path):
    if not os.path.exists(path):
        return []
    with open(path, encoding="utf-8-sig") as fh:
        lines = [ln for ln in fh if not ln.lstrip().startswith("#") and ln.strip()]
    return list(csv.DictReader(lines, delimiter=";"))


# --- 1. Keyword-Universum ------------------------------------------------
master_path = os.path.join(data_dir, "keywords-master.csv")
if not os.path.exists(master_path):
    print("FEHLER: %s fehlt." % master_path, file=sys.stderr)
    sys.exit(2)

with open(master_path, encoding="utf-8-sig") as fh:
    keywords = [r for r in csv.DictReader(fh) if r.get("keyword")]

# --- 2. Ownership + Exclusions -------------------------------------------
owned = {}
for r in read_semicolon_csv(os.environ["OWNERSHIP"]):
    if r.get("query"):
        owned[fold(r["query"])] = r["owner_path"]

excluded = {}
for r in read_semicolon_csv(os.environ["EXCLUSIONS"]):
    if r.get("keyword"):
        excluded[fold(r["keyword"])] = r

# --- 3. GSC: was rankt tatsaechlich? -------------------------------------
gsc_best = {}
for path in sorted(glob.glob(os.path.join(data_dir, "gsc", "*.csv"))):
    for r in read_semicolon_csv(path):
        q = fold(r.get("query", ""))
        if not q:
            continue
        try:
            pos = float((r.get("position") or "0").replace(",", "."))
            impr = int(r.get("impressions") or 0)
        except ValueError:
            continue
        if pos <= 0:
            continue
        url = (r.get("page") or "").replace("https://hasimuener.de", "") or "/"
        prev = gsc_best.get(q)
        if prev is None or pos < prev[1]:
            gsc_best[q] = (url, pos, impr)

# --- 4. Wettbewerber ------------------------------------------------------
comp_best = defaultdict(list)
for path in sorted(glob.glob(os.path.join(data_dir, "comp-*.json"))):
    try:
        with open(path, encoding="utf-8") as fh:
            blob = json.load(fh)
    except (OSError, ValueError):
        continue
    domain = blob.get("domain", os.path.basename(path))
    for row in blob.get("keywords", []):
        q = fold(row.get("keyword", ""))
        if q and row.get("position"):
            comp_best[q].append((domain, int(row["position"])))

# --- 5. Status je Keyword -------------------------------------------------
owned_tok = [(toks(q), p) for q, p in owned.items()]
rows = []
for kw in keywords:
    q = fold(kw["keyword"])
    if q in excluded:
        status, detail = "AUSGESCHLOSSEN", excluded[q]["grund"]
    elif q in owned:
        status, detail = "BESTEHT", owned[q]
        if q in gsc_best:
            u, pos, impr = gsc_best[q]
            detail = "%s (rankt Pos. %.0f, %d Impr.)" % (owned[q], pos, impr)
            if u.rstrip("/") != owned[q].rstrip("/"):
                status = "OWNER-KONFLIKT"
                detail = "Registry: %s — rankt aber %s (Pos. %.0f)" % (owned[q], u, pos)
    elif q in gsc_best:
        u, pos, impr = gsc_best[q]
        status = "REGISTRY-LUECKE"
        detail = "rankt bereits %s (Pos. %.0f, %d Impr.), fehlt in query-ownership.csv" % (u, pos, impr)
    else:
        near = [(len(toks(kw["keyword"]) & t) / len(toks(kw["keyword"]) | t), p)
                for t, p in owned_tok if t and toks(kw["keyword"])]
        near = [x for x in near if x[0] >= 0.5]
        if near:
            near.sort(key=lambda x: (-x[0], x[1]))
            status = "TEIL-LUECKE"
            top = near[0][0]
            # Gleichstaende sichtbar machen, statt willkuerlich einen Owner zu waehlen.
            # Dedupe: mehrere Registry-Queries koennen auf dieselbe URL zeigen.
            tied = list(dict.fromkeys(p for s, p in near if s == top))[:2]
            detail = "nahe an %s — dort einarbeiten statt neu bauen" % " oder ".join(tied)
        else:
            status = "LUECKE"
            comps = sorted(comp_best.get(q, []), key=lambda x: x[1])[:2]
            detail = ("Wettbewerber: " + ", ".join("%s Pos. %d" % c for c in comps)) if comps else "kein Wettbewerber-Signal in den Exporten"
    rows.append((kw, status, detail))


def num(value):
    try:
        return int(float(value))
    except (TypeError, ValueError):
        return 0


ORDER = ["LUECKE", "TEIL-LUECKE", "REGISTRY-LUECKE", "OWNER-KONFLIKT", "BESTEHT", "AUSGESCHLOSSEN"]
counts = defaultdict(int)
for _, s, _ in rows:
    counts[s] += 1

out = []
if fmt == "md":
    out.append("# Gap-Report %s\n" % period)
    out.append("Erzeugt aus `%s`. Keine API-Aufrufe, keine geschaetzten Werte.\n" % os.path.relpath(data_dir))
    out.append("| Status | Anzahl |\n| --- | ---: |")
    for s in ORDER:
        if counts[s]:
            out.append("| %s | %d |" % (s, counts[s]))
    out.append("")
else:
    out.append("=== Gap-Report %s ===\n" % period)
    out.append("Datenbasis: %s" % os.path.relpath(data_dir))
    out.append("Keywords: %d | " % len(rows) + " | ".join("%s %d" % (s, counts[s]) for s in ORDER if counts[s]))
    out.append("")

for status in ORDER:
    group = [r for r in rows if r[1] == status]
    if not group:
        continue
    group.sort(key=lambda r: -num(r[0].get("volume")))
    if fmt == "md":
        out.append("## %s (%d)\n" % (status, len(group)))
        out.append("| Keyword | Vol. | KD | Cluster | Befund |")
        out.append("| --- | ---: | ---: | --- | --- |")
        for kw, _, detail in group:
            out.append("| %s | %s | %s | %s | %s |" % (
                kw["keyword"], kw.get("volume") or "–", kw.get("kd") or "–",
                kw.get("cluster") or "–", detail))
        out.append("")
    else:
        out.append("--- %s (%d) ---" % (status, len(group)))
        for kw, _, detail in group[:25]:
            out.append("  %-42s Vol %-6s KD %-4s  %s" % (
                kw["keyword"][:42], kw.get("volume") or "–", kw.get("kd") or "–", detail))
        if len(group) > 25:
            out.append("  … %d weitere (--md fuer die vollstaendige Liste)" % (len(group) - 25))
        out.append("")

if fmt != "md":
    out.append("Naechster Schritt: LUECKE-Zeilen gegen die Positionierung pruefen.")
    out.append("Nicht gewollt -> docs/seo/keyword-exclusions.csv (mit Begruendung).")
    out.append("Gewollt -> intent-gate.sh check vor dem Bauen.")

print("\n".join(out))
PY
