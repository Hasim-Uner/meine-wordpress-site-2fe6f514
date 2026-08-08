#!/usr/bin/env bash
#
# Drift-Report: welche Queries und Seiten haben zwischen zwei Perioden verloren,
# und hat sich im Repo etwas geaendert, das dazu passt?
#
# Der SEO-Cockpit-Export traegt den Periodenvergleich bereits mit sich
# (previous_* und delta_*). gap-report.sh wertet bewusst genau EINEN Snapshot
# aus und laesst diese Spalten liegen — hier werden sie gelesen.
#
# Rechnet ausschliesslich mit vorhandenen Exporten in seo-research/<periode>/.
# Keine API, keine geschaetzten Werte, keine Ursachenbehauptung: das Skript
# stellt zeitliche Naehe zwischen Repo-Aenderung und Verlust fest, mehr nicht.
#
# Nutzung:
#   drift-report.sh                    neueste Periode, 28-Tage-Snapshot (Default)
#   drift-report.sh 2026-07            bestimmte Periode
#   drift-report.sh --7d               7-Tage-Snapshot statt 28 Tage
#   drift-report.sh --range=28         gleichwertig zu --28d
#   drift-report.sh --md               Markdown statt Konsolenausgabe
#   drift-report.sh --all              auch Verluste ohne Impressionsdeckung
#
# Exit 1, wenn mindestens ein ABSTURZ gefunden wurde (Position und Impressionen
# fallen gemeinsam). Damit ist der Report als Gate nach einem Deploy nutzbar.
#
# Fuer Tests ueberschreibbar: RESEARCH_DIR.

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd)"
RESEARCH_DIR="${RESEARCH_DIR:-$REPO_ROOT/seo-research}"

PERIOD=""
FORMAT="text"
RANGE=""
SHOW_ALL="0"
for arg in "$@"; do
  case "$arg" in
    --md|--markdown) FORMAT="md" ;;
    --7d|--7) RANGE="7" ;;
    --28d|--28) RANGE="28" ;;
    --range=*) RANGE="${arg#--range=}" ;;
    --all) SHOW_ALL="1" ;;
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
SHOW_ALL="$SHOW_ALL" REPO_ROOT="$REPO_ROOT" \
python3 <<'PY'
import csv, glob, os, subprocess, sys
from collections import defaultdict

data_dir = os.environ["DATA_DIR"]
period = os.environ["PERIOD"]
fmt = os.environ["FORMAT"]
repo_root = os.environ["REPO_ROOT"]
show_all = os.environ.get("SHOW_ALL") == "1"
requested_range = (os.environ.get("RANGE") or "").strip()


def read_semicolon_csv(path):
    if not os.path.exists(path):
        return []
    with open(path, encoding="utf-8-sig") as fh:
        lines = [ln for ln in fh if not ln.lstrip().startswith("#") and ln.strip()]
    return list(csv.DictReader(lines, delimiter=";"))


def fnum(value):
    try:
        return float((value or "").replace(",", "."))
    except (TypeError, ValueError):
        return None


def inum(value):
    try:
        return int(float((value or "0").replace(",", ".")))
    except (TypeError, ValueError):
        return 0


# --- Snapshot waehlen: gleiche Regel wie gap-report.sh ---------------------
# Zeitraum ueber range_days + current_end, nie ueber den Dateinamen.
latest_end, latest_start = {}, {}
latest_rows, latest_files = defaultdict(list), defaultdict(list)
for path in sorted(glob.glob(os.path.join(data_dir, "gsc", "*.csv"))):
    for r in read_semicolon_csv(path):
        range_days = (r.get("range_days") or "").strip()
        current_end = (r.get("current_end") or "").strip()
        if not range_days or not current_end:
            continue
        known = latest_end.get(range_days, "")
        if current_end > known:
            latest_end[range_days] = current_end
            latest_start[range_days] = (r.get("current_start") or "").strip()
            latest_rows[range_days] = [r]
            latest_files[range_days] = [path]
        elif current_end == known:
            latest_rows[range_days].append(r)
            if path not in latest_files[range_days]:
                latest_files[range_days].append(path)

if not latest_rows:
    print("FEHLER: Keine GSC-Exporte in %s." % os.path.relpath(os.path.join(data_dir, "gsc")),
          file=sys.stderr)
    sys.exit(2)

if requested_range:
    if requested_range not in latest_rows:
        available = ", ".join(sorted(latest_rows, key=lambda v: int(v) if v.isdigit() else 0))
        print("FEHLER: Kein %s-Tage-Snapshot vorhanden. Vorhanden: %s."
              % (requested_range, available or "keine"), file=sys.stderr)
        sys.exit(2)
    range_days = requested_range
else:
    # Wie gap-report.sh: 28 Tage als Default, der 7-Tage-Wert schwankt zu stark.
    range_days = "28" if "28" in latest_rows else max(
        latest_rows, key=lambda v: int(v) if v.isdigit() else 0)

rows = latest_rows[range_days]
sources = [os.path.relpath(p) for p in latest_files[range_days]]
window_start = latest_start.get(range_days, "")
window_end = latest_end[range_days]

# --- Verluste klassifizieren ----------------------------------------------
# Bewusst ohne Mindest-Impressionsschwelle: jede Grenze waere geraten. Statt zu
# filtern wird nach Beweislage getrennt und nach verlorenen Impressionen
# sortiert — das ist die wirtschaftliche Groesse, keine gewichtete Kennzahl.
#
# VERSCHWUNDEN  Query×URL existierte vorher, in der aktuellen Periode nicht.
# ABSTURZ       Position schlechter UND Impressionen gefallen. Beide Signale
#               zeigen in dieselbe Richtung.
# POSITION      Position schlechter, Impressionen aber nicht gefallen. Kann ein
#               veraenderter Query-Mix sein — pruefen, nicht sofort handeln.
# SICHTBARKEIT  Impressionen deutlich gefallen, Position nahezu unveraendert.
#               Eher SERP-Feature oder Saison als Ranking-Verlust.

losses = []
gains = 0
for r in rows:
    row_scope = (r.get("row_scope") or "query_page").strip()
    period_presence = (r.get("period_presence") or "").strip()
    if row_scope != "query_page":
        continue

    dpos = fnum(r.get("delta_position"))
    dimpr = fnum(r.get("delta_impressions"))
    pos, prev_pos = fnum(r.get("position")), fnum(r.get("previous_position"))
    impr, prev_impr = inum(r.get("impressions")), inum(r.get("previous_impressions"))
    query = (r.get("query") or "").strip()
    url = (r.get("page") or "").replace("https://hasimuener.de", "") or "/"
    if not query:
        continue

    if period_presence == "previous_only" and prev_impr > 0:
        kind = "VERSCHWUNDEN"
        losses.append({
            "kind": kind, "query": query, "url": url,
            "pos": None, "prev_pos": prev_pos, "dpos": 0.0,
            "impr": 0, "prev_impr": prev_impr,
            "lost_impr": prev_impr,
            "page_type": (r.get("page_type") or "").strip(),
            "word_count": (r.get("word_count") or "").strip(),
        })
        continue

    # delta_position: positiv = weiter hinten = schlechter.
    worse_pos = dpos is not None and dpos > 0
    lost_impr = dimpr is not None and dimpr < 0

    if dpos is not None and dpos < 0 and not lost_impr:
        gains += 1
        continue

    if worse_pos and lost_impr:
        kind = "ABSTURZ"
    elif worse_pos:
        kind = "POSITION"
    elif lost_impr and prev_impr and (prev_impr - impr) / prev_impr >= 0.5:
        kind = "SICHTBARKEIT"
    else:
        continue

    losses.append({
        "kind": kind, "query": query, "url": url,
        "pos": pos, "prev_pos": prev_pos, "dpos": dpos or 0.0,
        "impr": impr, "prev_impr": prev_impr,
        "lost_impr": max(prev_impr - impr, 0),
        "page_type": (r.get("page_type") or "").strip(),
        "word_count": (r.get("word_count") or "").strip(),
    })

if not show_all:
    losses = [l for l in losses if l["kind"] != "POSITION" or l["prev_impr"] > 0]

losses.sort(key=lambda l: (-l["lost_impr"], l["dpos"] and -l["dpos"] or 0))

# --- Seiten-Cluster: das eigentliche Signal --------------------------------
# Eine einzelne verlorene Query ist Rauschen. Mehrere Queries derselben URL, die
# gemeinsam fallen, sind ein Seiten-Ereignis — und genau das findet man in einer
# nach Queries sortierten Liste nicht.
#
# Ein Cluster entsteht nur aus belegten Verlusten: ABSTURZ und SICHTBARKEIT.
# POSITION allein bleibt draussen — eine GSC-Position ist der Durchschnitt ueber
# die Periode, kleine Ausschlaege sind dort Rauschen. Das ist keine geratene
# Schwelle, sondern die Trennung nach Beweislage: wo die Impressionen nicht
# mitgehen, ist der Verlust nicht belegt.
by_page = defaultdict(list)
for l in losses:
    if l["kind"] in ("VERSCHWUNDEN", "ABSTURZ", "SICHTBARKEIT"):
        by_page[l["url"]].append(l)
clusters = sorted(
    ((u, ls) for u, ls in by_page.items() if len(ls) > 1),
    key=lambda item: -sum(x["lost_impr"] for x in item[1]),
)
clustered_urls = {u for u, _ in clusters}

# --- Repo-Korrelation ------------------------------------------------------
# Die Zuordnung Route -> Template gehoert review-route.sh (inklusive
# Alias-Tabelle fuer Schatten-Wrapper). Hier wird sie gelesen, nicht kopiert.
review = os.path.join(repo_root, "agents/skills/route-conversion-review/scripts/review-route.sh")


def resolve_template(route):
    if not os.path.exists(review):
        return None
    try:
        out = subprocess.run(["bash", review, route], cwd=repo_root, timeout=60,
                             capture_output=True, text=True)
    except (OSError, subprocess.SubprocessError):
        return None
    for line in (out.stdout or "").splitlines():
        if line.startswith("Template: "):
            return line[len("Template: "):].strip()
    return None


def last_change(path):
    try:
        out = subprocess.run(["git", "log", "-1", "--format=%as|%h|%s", "--", path],
                             cwd=repo_root, timeout=30, capture_output=True, text=True)
    except (OSError, subprocess.SubprocessError):
        return None
    line = (out.stdout or "").strip()
    return line.split("|", 2) if line else None


correlations = []
for url, _ in clusters[:8]:
    tpl = resolve_template(url)
    if not tpl:
        correlations.append((url, None, None, "kein Template im Repo — Copy liegt im Editor"))
        continue
    change = last_change(tpl)
    if not change:
        correlations.append((url, tpl, None, "keine Git-Historie"))
        continue
    date, sha, subject = change
    # Drei Lagen, nicht zwei. Eine Aenderung NACH dem Messfenster kann den
    # Verlust darin nicht verursacht haben — sie als Kandidat zu fuehren waere
    # schlicht falsch. Ihre Wirkung zeigt erst der naechste Export.
    if not window_start:
        note = "Fenstergrenzen unbekannt"
    elif date > window_end:
        note = "NACH dem Fenster geaendert — als Ursache ausgeschlossen, Wirkung erst im naechsten Export"
    elif date >= window_start:
        note = "IM Fenster geaendert — zeitlich naechstliegender Kandidat"
    else:
        note = "vor dem Fenster geaendert"
    correlations.append((url, tpl, (date, sha, subject), note))

# --- Ausgabe ---------------------------------------------------------------
counts = defaultdict(int)
for l in losses:
    counts[l["kind"]] += 1

out = []
md = fmt == "md"


def pos_s(v):
    return "–" if v is None else ("%.1f" % v).replace(".", ",")


if md:
    out.append("# Drift-Report %s\n" % period)
    out.append("Erzeugt aus `%s`. Keine API-Aufrufe, keine geschaetzten Werte.\n"
               % os.path.relpath(data_dir))
    out.append("Fenster: %s Tage, %s bis %s." % (range_days, window_start or "?", window_end))
    out.append("Quelle: %s\n" % ", ".join("`%s`" % s for s in sources))
else:
    out.append("=== Drift-Report %s ===\n" % period)
    out.append("Datenbasis: %s" % os.path.relpath(data_dir))
    out.append("Fenster: %s Tage, %s bis %s" % (range_days, window_start or "?", window_end))
    out.append("Quelle: %s" % ", ".join(sources))

summary = " | ".join("%s %d" % (k, counts[k]) for k in ("VERSCHWUNDEN", "ABSTURZ", "POSITION", "SICHTBARKEIT") if counts[k])
out.append("Verluste: %s%s | Verbesserungen: %d" % (
    summary or "keine", "", gains))
out.append("")

if clusters:
    head = "SEITEN MIT MEHREREN VERLUSTEN (%d)" % len(clusters)
    out.append(("## %s\n" % head) if md else ("--- %s ---" % head))
    if md:
        out.append("Mehrere Queries derselben URL fallen gemeinsam. Das ist ein")
        out.append("Seiten-Ereignis, kein Rauschen.\n")
        out.append("| URL | Queries | Impr. verloren | Positionsverlust |")
        out.append("| --- | ---: | ---: | --- |")
    for url, ls in clusters:
        lost = sum(x["lost_impr"] for x in ls)
        # Mittlerer Positionsverlust des Clusters. Die frueher gezeigte beste
        # Position verglich potenziell zwei verschiedene Queries miteinander und
        # liess einen Absturz harmlos aussehen.
        moved = [x["dpos"] for x in ls if x["dpos"]]
        span = ("Ø %+.1f Pos." % (sum(moved) / len(moved))) if moved else "Position stabil"
        if md:
            out.append("| %s | %d | %d | %s |" % (url, len(ls), lost, span))
        else:
            out.append("  %-46s %2d Queries, %4d Impr. verloren, %s"
                       % (url[:46], len(ls), lost, span))
            for x in sorted(ls, key=lambda x: -x["lost_impr"])[:6]:
                out.append("      %-38s %s → %s (%+.1f), %d → %d Impr."
                           % (x["query"][:38], pos_s(x["prev_pos"]), pos_s(x["pos"]),
                              x["dpos"], x["prev_impr"], x["impr"]))
            if len(ls) > 6:
                out.append("      … %d weitere" % (len(ls) - 6))
    out.append("")

    # Die Summenzeile allein macht ein Cluster nicht handlungsfaehig — man muss
    # sehen, welche Queries es tragen. Im Textmodus stehen sie eingerueckt unter
    # der URL; in Markdown braucht es dafuer eine eigene Tabelle.
    if md:
        out.append("### Queries in den Clustern\n")
        out.append("| URL | Query | vorher | jetzt | Δ Pos. | Impr. |")
        out.append("| --- | --- | ---: | ---: | ---: | ---: |")
        for url, ls in clusters:
            for x in sorted(ls, key=lambda x: -x["lost_impr"]):
                out.append("| %s | %s | %s | %s | %+.1f | %d → %d |" % (
                    url, x["query"], pos_s(x["prev_pos"]), pos_s(x["pos"]),
                    x["dpos"], x["prev_impr"], x["impr"]))
        out.append("")


# Alles, was in keinem Cluster steckt — auch die POSITION-Faelle, die als
# unbelegt gar nicht erst gruppiert wurden.
single = [l for l in losses if l["url"] not in clustered_urls]
if single:
    head = "EINZELNE VERLUSTE (%d)" % len(single)
    out.append(("## %s\n" % head) if md else ("--- %s ---" % head))
    if md:
        out.append("| Art | Query | URL | vorher | jetzt | Impr. |")
        out.append("| --- | --- | --- | ---: | ---: | ---: |")
    limit = len(single) if md else 15
    for l in single[:limit]:
        if md:
            out.append("| %s | %s | %s | %s | %s | %d → %d |" % (
                l["kind"], l["query"], l["url"], pos_s(l["prev_pos"]), pos_s(l["pos"]),
                l["prev_impr"], l["impr"]))
        else:
            out.append("  [%-12s] %-34s %s → %s, %d → %d Impr."
                       % (l["kind"], l["query"][:34], pos_s(l["prev_pos"]),
                          pos_s(l["pos"]), l["prev_impr"], l["impr"]))
    if not md and len(single) > limit:
        out.append("  … %d weitere (--md fuer die vollstaendige Liste)" % (len(single) - limit))
    out.append("")

if correlations:
    head = "REPO-KORRELATION"
    out.append(("## %s\n" % head) if md else ("--- %s ---" % head))
    if md:
        out.append("| URL | Template | Letzte Aenderung | Lage |")
        out.append("| --- | --- | --- | --- |")
    for url, tpl, change, note in correlations:
        if md:
            ch = "%s (`%s`) %s" % (change[0], change[1], change[2][:48]) if change else "–"
            out.append("| %s | %s | %s | %s |" % (url, "`%s`" % tpl if tpl else "–", ch, note))
        else:
            out.append("  %s" % url)
            out.append("    Template: %s" % (tpl or "–"))
            if change:
                out.append("    Letzte Aenderung: %s (%s) %s" % (change[0], change[1], change[2][:56]))
            out.append("    %s" % note)
    out.append("")

tail = [
    "Zeitliche Naehe ist keine Ursache. Ein Verlust kann am Wettbewerb, an einem",
    "Google-Update oder an der Saison liegen — das Skript kann das nicht trennen.",
    "Es zeigt, wo sich gleichzeitig etwas im Repo bewegt hat.",
    "",
    "Naechster Schritt: VERSCHWUNDEN- und ABSTURZ-Zeilen zuerst. Bei einem Seiten-Cluster die Route",
    "mit route-conversion-review pruefen, bevor einzelne Queries bewertet werden.",
]
out.append(("\n".join("> " + t if t else ">" for t in tail)) if md else "\n".join(tail))

print("\n".join(out))

# Exit 1 bei vollstaendig verschwundenen Queries oder ABSTURZ. POSITION allein
# reicht nicht — ein veraenderter Query-Mix ist kein Grund, einen Deploy rot zu
# faerben.
sys.exit(1 if counts["VERSCHWUNDEN"] or counts["ABSTURZ"] else 0)
PY
