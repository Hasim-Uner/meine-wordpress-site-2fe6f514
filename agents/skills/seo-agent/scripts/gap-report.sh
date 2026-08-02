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
#   gap-report.sh                      neueste Periode, 28-Tage-Snapshot (Default)
#   gap-report.sh 2026-07              bestimmte Periode
#   gap-report.sh --7d                 7-Tage-Snapshot statt 28 Tage
#   gap-report.sh --28d                28-Tage-Snapshot (explizit)
#   gap-report.sh --range=28           gleichwertig zu --28d
#   gap-report.sh 2026-07 --md         Markdown statt Konsolenausgabe
#
# Der Rang-Status kommt immer aus genau EINEM Snapshot: dem neuesten Export des
# gewaehlten Zeitraums, bestimmt ueber range_days + current_end (nicht ueber den
# Dateinamen). Historische Exporte werden nie mit dem aktuellen Stand vermischt,
# und eine alte Bestposition gewinnt nie gegen den Ist-Wert.
#
# Erwartete Dateien in seo-research/<periode>/data/:
#   keywords-master.csv                keyword,volume,kd,cpc,intent,cluster,quelle
#   gsc/*.csv                          SEO-Cockpit-Export (Semikolon)
#   comp-<domain>.json                 optional, Wettbewerber-Rankings
#
# Fuer Tests ueberschreibbar: RESEARCH_DIR, OWNERSHIP, EXCLUSIONS.

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd)"
RESEARCH_DIR="${RESEARCH_DIR:-$REPO_ROOT/seo-research}"

PERIOD=""
FORMAT="text"
RANGE=""
for arg in "$@"; do
  case "$arg" in
    --md|--markdown) FORMAT="md" ;;
    --7d|--7) RANGE="7" ;;
    --28d|--28) RANGE="28" ;;
    --range=*) RANGE="${arg#--range=}" ;;
    --help|-h) sed -n '2,30p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'; exit 0 ;;
    -*) echo "FEHLER: Unbekannte Option '$arg'. --help zeigt die Nutzung." >&2; exit 2 ;;
    *) PERIOD="$arg" ;;
  esac
done

if [[ -n "$RANGE" && ! "$RANGE" =~ ^[0-9]+$ ]]; then
  echo "FEHLER: --range erwartet eine Zahl (z. B. --range=28)." >&2
  exit 2
fi

if [[ -z "$PERIOD" ]]; then
  PERIOD="$(ls -1 "$RESEARCH_DIR" 2>/dev/null | grep -E '^[0-9]{4}-[0-9]{2}$' | sort | tail -1 || true)"
fi

DATA_DIR="$RESEARCH_DIR/$PERIOD/data"
if [[ -z "$PERIOD" || ! -d "$DATA_DIR" ]]; then
  echo "FEHLER: Keine Datenperiode gefunden. Erwartet: $RESEARCH_DIR/<JJJJ-MM>/data/" >&2
  exit 2
fi

DATA_DIR="$DATA_DIR" PERIOD="$PERIOD" FORMAT="$FORMAT" RANGE="$RANGE" \
OWNERSHIP="${OWNERSHIP:-$REPO_ROOT/docs/seo/query-ownership.csv}" \
EXCLUSIONS="${EXCLUSIONS:-$REPO_ROOT/docs/seo/keyword-exclusions.csv}" \
python3 <<'PY'
import csv, glob, json, os, re, sys
from collections import defaultdict

data_dir = os.environ["DATA_DIR"]
period = os.environ["PERIOD"]
fmt = os.environ["FORMAT"]
requested_range = (os.environ.get("RANGE") or "").strip()

# Bewusst ohne Schwellenwert: sobald eine Nicht-Owner-URL fuer eine Owner-Query
# ueberhaupt Impressionen bekommt, ist das ein Owner-Konflikt. Jede Grenze waere
# geraten und wuerde genau die Faelle verstecken, wegen derer dieser Report
# existiert.

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
# Pro Zeitraum nur den neuesten Snapshot auswerten. Sonst vermischen sich 7d
# und 28d sowie historische Exporte, und eine alte Bestposition gewinnt gegen
# den aktuellen Stand.
latest_gsc_end = {}
latest_gsc_start = {}
latest_gsc_rows = defaultdict(list)
latest_gsc_files = defaultdict(list)
for path in sorted(glob.glob(os.path.join(data_dir, "gsc", "*.csv"))):
    for r in read_semicolon_csv(path):
        range_days = (r.get("range_days") or "").strip()
        current_end = (r.get("current_end") or "").strip()
        if not range_days or not current_end:
            continue
        latest_end = latest_gsc_end.get(range_days, "")
        if current_end > latest_end:
            latest_gsc_end[range_days] = current_end
            latest_gsc_start[range_days] = (r.get("current_start") or "").strip()
            latest_gsc_rows[range_days] = [r]
            latest_gsc_files[range_days] = [path]
        elif current_end == latest_end:
            latest_gsc_rows[range_days].append(r)
            if path not in latest_gsc_files[range_days]:
                latest_gsc_files[range_days].append(path)

if requested_range:
    if requested_range not in latest_gsc_rows:
        available = ", ".join(sorted(latest_gsc_rows, key=lambda v: int(v) if v.isdigit() else 0)) or "keine"
        print(
            "FEHLER: Kein %s-Tage-Snapshot in %s. Vorhanden: %s."
            % (requested_range, os.path.relpath(os.path.join(data_dir, "gsc")), available),
            file=sys.stderr,
        )
        sys.exit(2)
    status_range_days = requested_range
else:
    # Default ist bewusst 28 Tage: der 7-Tage-Snapshot schwankt zu stark, um
    # daraus Eigentums- oder Prioritaetsentscheidungen abzuleiten.
    status_range_days = "28" if "28" in latest_gsc_rows else ""
    if not status_range_days and latest_gsc_rows:
        status_range_days = max(latest_gsc_rows, key=lambda value: int(value) if value.isdigit() else 0)

status_sources = [os.path.relpath(p) for p in latest_gsc_files.get(status_range_days, [])]

# Alle rankenden URLs je Query behalten. Blind auf die beste Position zu
# aggregieren wuerde Kannibalisierung im selben Snapshot unsichtbar machen.
gsc_pages = defaultdict(list)
for r in latest_gsc_rows.get(status_range_days, []):
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
    try:
        previous_pos = float((r.get("previous_position") or "0").replace(",", "."))
    except ValueError:
        previous_pos = 0
    url = (r.get("page") or "").replace("https://hasimuener.de", "") or "/"
    gsc_pages[q].append({
        "url": url,
        "pos": pos,
        "impr": impr,
        "previous_pos": previous_pos if previous_pos > 0 else None,
    })

# Schreibvarianten derselben Query ("pv leads" / "pv-leads") falten auf denselben
# Schluessel. Steht dahinter dieselbe URL, ist das keine Kannibalisierung, sondern
# eine Variante: Impressionen addieren, Position aus der staerksten Variante
# uebernehmen. Nichts wird gemittelt oder geschaetzt.
for q, entries in gsc_pages.items():
    merged = {}
    for e in entries:
        key = e["url"].rstrip("/")
        seen = merged.get(key)
        if seen is None:
            merged[key] = dict(e)
            continue
        if e["impr"] > seen["impr"]:
            seen["pos"] = e["pos"]
            seen["previous_pos"] = e["previous_pos"]
        seen["impr"] += e["impr"]
    gsc_pages[q] = sorted(merged.values(), key=lambda e: (e["pos"], -e["impr"]))

gsc_best = {q: entries[0] for q, entries in gsc_pages.items()}

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


def fmt_position(value):
    return ("%.1f" % value).replace(".", ",")


def fmt_ranking(pos, previous_pos):
    current = "Pos. %s" % fmt_position(pos)
    if previous_pos is None:
        return current
    return "%s; vorher Pos. %s" % (current, fmt_position(previous_pos))


def fmt_entry(entry, with_url=True):
    ranking = "%s, %d Impr." % (fmt_ranking(entry["pos"], entry["previous_pos"]), entry["impr"])
    return "%s (%s)" % (entry["url"], ranking) if with_url else ranking


def fmt_rivals(entries):
    return ", ".join(fmt_entry(e) for e in entries)


# Jede Query mit mehr als einer rankenden URL im gewaehlten Snapshot, unabhaengig
# davon ob sie im Keyword-Universum steht. Vollstaendig, ohne Schwelle: der
# Abschnitt ist die Kannibalisierungs-Karte des Snapshots.
multi_url_queries = sorted(
    ((q, entries) for q, entries in gsc_pages.items() if len(entries) > 1),
    key=lambda item: -sum(e["impr"] for e in item[1]),
)

for kw in keywords:
    q = fold(kw["keyword"])
    if q in excluded:
        status, detail = "AUSGESCHLOSSEN", excluded[q]["grund"]
    elif q in owned:
        status, detail = "BESTEHT", owned[q]
        if q in gsc_pages:
            owner_path = owned[q]
            entries = gsc_pages[q]
            owner_entry = next(
                (e for e in entries if e["url"].rstrip("/") == owner_path.rstrip("/")), None)
            rivals = [e for e in entries if e is not owner_entry]

            if owner_entry is None:
                status = "OWNER-KONFLIKT"
                detail = "Registry: %s — rankt aber %s" % (owner_path, fmt_rivals(entries))
            else:
                detail = "%s (rankt %s)" % (owner_path, fmt_entry(owner_entry, with_url=False))
                if rivals:
                    status = "OWNER-KONFLIKT"
                    detail += " — dieselbe Query rankt zusaetzlich mit %s" % fmt_rivals(rivals)
    elif q in gsc_pages:
        entries = gsc_pages[q]
        status = "REGISTRY-LUECKE"
        detail = "rankt bereits %s, fehlt in query-ownership.csv" % fmt_rivals(entries)
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
    if status_range_days:
        out.append("GSC-Status: %s Tage, Snapshot %s bis `%s`.\n" % (
            status_range_days, latest_gsc_start.get(status_range_days, "?"),
            latest_gsc_end[status_range_days]))
        out.append("Quelle: %s\n" % ", ".join("`%s`" % s for s in status_sources))
    out.append("| Status | Anzahl |\n| --- | ---: |")
    for s in ORDER:
        if counts[s]:
            out.append("| %s | %d |" % (s, counts[s]))
    out.append("")
else:
    out.append("=== Gap-Report %s ===\n" % period)
    out.append("Datenbasis: %s" % os.path.relpath(data_dir))
    if status_range_days:
        out.append("GSC-Status: %s Tage, Snapshot %s bis %s" % (
            status_range_days, latest_gsc_start.get(status_range_days, "?"),
            latest_gsc_end[status_range_days]))
        out.append("Quelle: %s" % ", ".join(status_sources))
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

if multi_url_queries:
    if fmt == "md":
        out.append("## MEHRFACH-URLS (%d)\n" % len(multi_url_queries))
        out.append("Queries, fuer die im gewaehlten Snapshot mehr als eine eigene URL rankt.")
        out.append("Vollstaendig und ohne Schwelle. Registry-Owner ist mit `=` markiert,")
        out.append("nicht registrierte URLs mit `+`.\n")
        out.append("| Query | Impr. gesamt | Rankende URLs |")
        out.append("| --- | ---: | --- |")
        for q, entries in multi_url_queries:
            owner_path = owned.get(q)
            urls = " · ".join(
                "%s%s" % (
                    "=" if owner_path and e["url"].rstrip("/") == owner_path.rstrip("/") else "+",
                    fmt_entry(e),
                )
                for e in entries
            )
            out.append("| %s | %d | %s |" % (q, sum(e["impr"] for e in entries), urls))
        out.append("")
    else:
        out.append("--- MEHRFACH-URLS (%d) ---" % len(multi_url_queries))
        out.append("  Mehr als eine eigene URL im Snapshot. '=' Registry-Owner, '+' nicht registriert.")
        for q, entries in multi_url_queries:
            owner_path = owned.get(q)
            out.append("  %-42s %d Impr. gesamt" % (q[:42], sum(e["impr"] for e in entries)))
            for e in entries:
                marker = "=" if owner_path and e["url"].rstrip("/") == owner_path.rstrip("/") else "+"
                out.append("    %s %s" % (marker, fmt_entry(e)))
        out.append("")

if fmt != "md":
    out.append("Naechster Schritt: LUECKE-Zeilen gegen die Positionierung pruefen.")
    out.append("Nicht gewollt -> docs/seo/keyword-exclusions.csv (mit Begruendung).")
    out.append("Gewollt -> intent-gate.sh check vor dem Bauen.")

print("\n".join(out))
PY
