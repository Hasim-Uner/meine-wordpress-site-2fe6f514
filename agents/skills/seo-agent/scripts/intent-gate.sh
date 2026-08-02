#!/usr/bin/env bash
#
# Pre-Publish-Gate: Besitzt diese Suchintention bereits eine URL?
#
# Setzt die Regeln aus docs/seo/seo-content-system.md §8 ("keine zwei Seiten mit
# gleichem Keyword-Ziel") und docs/standards/BRAND_AND_COPY.md ("Money-Page
# besitzt die Query") als ausfuehrbaren Check um, statt sie nur zu dokumentieren.
#
# Nutzung:
#   intent-gate.sh check "waermepumpen leads" [/ziel-slug/]   Einzelpruefung
#   intent-gate.sh audit                                       Registry-Konsistenz
#
# Exit-Codes: 0 = frei/ok, 1 = Konflikt (Query gehoert einer anderen URL), 2 = Fehler.

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd)"
REGISTRY="$REPO_ROOT/docs/seo/query-ownership.csv"
SEO_META="$REPO_ROOT/blocksy-child/inc/seo-meta.php"
PROVIDER_POSTS="$REPO_ROOT/blocksy-child/inc/blog-provider-posts.php"

if [[ ! -f "$REGISTRY" ]]; then
  echo "FEHLER: Registry fehlt: $REGISTRY" >&2
  exit 2
fi

MODE="${1:-audit}"

case "$MODE" in
  check)
    if [[ $# -lt 2 ]]; then
      echo 'Nutzung: intent-gate.sh check "<query>" [<ziel-slug>]' >&2
      exit 2
    fi
    QUERY="$2"
    TARGET="${3:-}"
    ;;
  audit) QUERY=""; TARGET="" ;;
  *)
    echo "Unbekannter Modus: $MODE (erlaubt: check, audit)" >&2
    exit 2
    ;;
esac

REGISTRY="$REGISTRY" SEO_META="$SEO_META" PROVIDER_POSTS="$PROVIDER_POSTS" MODE="$MODE" QUERY="$QUERY" TARGET="$TARGET" \
python3 <<'PY'
import csv, os, re, sys

registry_path = os.environ["REGISTRY"]
seo_meta_path = os.environ["SEO_META"]
provider_posts_path = os.environ["PROVIDER_POSTS"]
mode = os.environ["MODE"]
query_in = os.environ["QUERY"]
target_in = os.environ["TARGET"]

FOLD = {"ä": "ae", "ö": "oe", "ü": "ue", "ß": "ss", "Ä": "ae", "Ö": "oe", "Ü": "ue"}
STOP = {
    "und", "oder", "fuer", "für", "der", "die", "das", "den", "dem", "ein", "eine",
    "im", "in", "am", "an", "auf", "mit", "ohne", "vs", "statt", "von", "zu", "bei",
    "was", "wie", "wer", "ist", "sind", "the", "for", "and",
}


def fold(text):
    out = "".join(FOLD.get(ch, ch) for ch in text.lower())
    return re.sub(r"[^a-z0-9]+", " ", out).strip()


def tokens(text):
    # Kurze Tokens wie "pv" sind bedeutungstragend und duerfen nicht wegfallen.
    return {t for t in fold(text).split() if t and t not in STOP and len(t) > 1}


def norm_path(value):
    value = (value or "").strip()
    if not value:
        return ""
    if not value.startswith("/"):
        value = "/" + value
    if not value.endswith("/"):
        value += "/"
    return value


rows = []
with open(registry_path, encoding="utf-8-sig") as fh:
    lines = [ln for ln in fh if not ln.lstrip().startswith("#") and ln.strip()]
for row in csv.DictReader(lines, delimiter=";"):
    if row.get("query"):
        rows.append(row)

if not rows:
    print("FEHLER: Registry enthaelt keine Zeilen.", file=sys.stderr)
    sys.exit(2)


def similarity(a, b):
    """Jaccard: bestraft unterscheidende Modifier ("kaufen" vs. "kosten") korrekt.

    Containment allein wuerde "pv leads kaufen" und "waermepumpen leads kaufen"
    als identisch melden, obwohl die Entitaet den Unterschied macht.
    """
    if not a or not b:
        return 0.0
    return len(a & b) / len(a | b)


def distinct_pairs(rows):
    """Bewusst getrennte Query-Paare aus der Spalte distinct_from."""
    pairs = set()
    for r in rows:
        for other in (r.get("distinct_from") or "").split("|"):
            other = fold(other)
            if other:
                pairs.add(frozenset((fold(r["query"]), other)))
    return pairs


if mode == "check":
    q_norm = fold(query_in)
    q_tok = tokens(query_in)
    target = norm_path(target_in)

    exact = [r for r in rows if fold(r["query"]) == q_norm]
    if exact:
        owner = norm_path(exact[0]["owner_path"])
        if target and target == owner:
            print("OK — %s besitzt \"%s\" bereits. Ausbau auf dieser URL ist korrekt." % (owner, query_in))
            sys.exit(0)
        print("BLOCKIERT — die Query \"%s\" gehoert bereits %s" % (query_in, owner))
        print("  Intent: %s | Status: %s" % (exact[0]["intent"], exact[0]["status"]))
        print("  Beleg:  %s" % exact[0]["source"])
        print()
        print("  Regel: docs/seo/seo-content-system.md §8 — keine zwei Seiten mit gleichem Keyword-Ziel.")
        print("  Optionen: (a) bestehende Seite ausbauen, (b) Intent echt abgrenzen und")
        print("            Registry-Zeile mit Beleg ergaenzen, (c) als Support-Seite ohne")
        print("            Ranking-Ziel bauen und intern auf den Owner verlinken.")
        sys.exit(1)

    near = []
    for r in rows:
        score = similarity(q_tok, tokens(r["query"]))
        if score >= 0.6:
            near.append((score, r))
    near.sort(key=lambda x: -x[0])

    if near:
        print("WARNUNG — kein exakter Eintrag, aber nahe Intentionen vorhanden:")
        for score, r in near[:5]:
            print("  %3d%% Ueberlappung: \"%s\" -> %s" % (round(score * 100), r["query"], norm_path(r["owner_path"])))
        print()
        print("  Entscheiden und begruenden, bevor eine neue Seite entsteht.")
        print("  Wenn die Intention wirklich neu ist: Zeile in docs/seo/query-ownership.csv")
        print("  mit Beleg eintragen — sonst hat die naechste Seite denselben Konflikt.")
        sys.exit(1)

    print("FREI — \"%s\" hat noch keinen Owner." % query_in)
    if target:
        print("  Nach dem Anlegen eintragen:")
        print("  %s;%s;<intent>;owned;<beleg>" % (fold(query_in), target))
    else:
        print("  Nach dem Anlegen in docs/seo/query-ownership.csv eintragen.")
    sys.exit(0)

# --- audit ---------------------------------------------------------------
print("=== Query-Ownership-Audit ===\n")

owners = {}
for r in rows:
    owners.setdefault(norm_path(r["owner_path"]), []).append(r["query"])

print("Registry: %d Queries auf %d URLs\n" % (len(rows), len(owners)))

problems = 0

# 1. Doppelte Queries
seen = {}
for r in rows:
    key = fold(r["query"])
    seen.setdefault(key, []).append(norm_path(r["owner_path"]))
dupes = {q: p for q, p in seen.items() if len(set(p)) > 1}
if dupes:
    print("KONFLIKT — dieselbe Query mit mehreren Ownern:")
    for q, paths in dupes.items():
        print("  \"%s\" -> %s" % (q, ", ".join(sorted(set(paths)))))
        problems += 1
    print()

# 2. Near-Duplicate-Intentionen ueber URL-Grenzen hinweg
declared_distinct = distinct_pairs(rows)
flagged = []
for i, a in enumerate(rows):
    for b in rows[i + 1:]:
        pa, pb = norm_path(a["owner_path"]), norm_path(b["owner_path"])
        if pa == pb:
            continue
        if frozenset((fold(a["query"]), fold(b["query"]))) in declared_distinct:
            continue
        score = similarity(tokens(a["query"]), tokens(b["query"]))
        if score >= 0.6:
            flagged.append((score, a["query"], pa, b["query"], pb))
if flagged:
    print("PRUEFEN — nahe Intentionen auf verschiedenen URLs (Kannibalisierungsrisiko):")
    for score, qa, pa, qb, pb in sorted(flagged, key=lambda x: -x[0]):
        print("  %3d%%  \"%s\" (%s)  <->  \"%s\" (%s)" % (round(score * 100), qa, pa, qb, pb))
        problems += 1
    print("  Bewusste Trennung? Dann in distinct_from der jeweiligen Zeile eintragen.")
    print()

# 3. Owner ohne auffindbaren Rendering-Pfad im Theme
theme_dir = os.path.dirname(seo_meta_path)
theme_root = os.path.dirname(theme_dir)
try:
    theme_files = set(os.listdir(theme_root))
except OSError:
    theme_files = set()
meta_src = ""
if os.path.exists(seo_meta_path):
    with open(seo_meta_path, encoding="utf-8") as fh:
        meta_src = fh.read()
provider_posts_src = ""
if os.path.exists(provider_posts_path):
    with open(provider_posts_path, encoding="utf-8") as fh:
        provider_posts_src = fh.read()

missing = []
for path in sorted(owners):
    slug = path.strip("/")
    has_template = ("page-%s.php" % slug) in theme_files
    has_meta = ("'%s'" % slug) in meta_src
    has_provider_seed = bool(re.search(r"['\"]slug['\"]\s*=>\s*['\"]%s['\"]" % re.escape(slug), provider_posts_src))
    if not has_template and not has_meta and not has_provider_seed:
        missing.append(path)
if missing:
    print("HINWEIS — Owner ohne Template und ohne Forced-Meta-Eintrag:")
    for path in missing:
        print("  %s  (Blogpost/CPT oder WP-Admin-Seite? Dann ok — sonst tote Zeile)" % path)
    print()

print("=== Ergebnis ===")
if problems:
    print("  %d Punkt(e) pruefen." % problems)
    sys.exit(1)
print("  Keine Ownership-Konflikte in der Registry.")
sys.exit(0)
PY
