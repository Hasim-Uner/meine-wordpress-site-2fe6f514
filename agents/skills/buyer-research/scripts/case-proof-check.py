#!/usr/bin/env python3
"""Validate a case-study claim ledger and optionally print a Case-Brief skeleton.

Python 3 stdlib only. Regeln und Begruendungen: references/case-study-engineering.md.
Exit codes: 0 keine Fehler, 1 Fehler im Ledger, 2 Aufruf-/Header-/Lesefehler.
"""

from __future__ import annotations

import argparse
import csv
import re
import subprocess
import sys
from datetime import date
from pathlib import Path


FIELDS = [
    "claim_id",
    "metric",
    "value",
    "unit",
    "numerator",
    "denominator",
    "denominator_label",
    "window_start",
    "window_end",
    "baseline_value",
    "baseline_window_start",
    "baseline_window_end",
    "source_kind",
    "source_locator",
    "definition",
    "attribution_note",
    "public",
]

SOURCE_KINDS = {"canon", "crm_export", "tracking_export", "invoice", "client_statement"}
PERCENT_TOLERANCE = 0.5
SMALL_BASE = 30
WINDOW_LENGTH_TOLERANCE = 0.10
MAX_RESULT_CLAIMS = 3
# Metrik-Namen, die einen nachgelagerten Erfolg (Abschluss, Umsatz) anzeigen.
# Reine Namensheuristik fuer die Reihenfolge im Brief; sie bewertet nichts.
LAGGING_HINTS = (
    "won", "abschluss", "close", "closing", "order", "auftrag",
    "revenue", "umsatz", "deal", "sales", "cpo",
)
EMAIL_CHARS = "@"
PHONE_SEPARATORS = re.compile(r"[\s+\-()/.]")


def repo_root() -> Path:
    try:
        output = subprocess.check_output(
            ["git", "rev-parse", "--show-toplevel"],
            cwd=Path.cwd(),
            stderr=subprocess.DEVNULL,
            text=True,
        ).strip()
        if output:
            return Path(output)
    except (OSError, subprocess.CalledProcessError):
        pass
    return Path(__file__).resolve().parents[4]


def parse_number(text: str) -> float | None:
    """Zahl aus dem Ledger lesen; deutsches Dezimalkomma ist erlaubt."""
    cleaned = text.strip().replace(" ", "")
    if re.fullmatch(r"-?\d+,\d+", cleaned):
        cleaned = cleaned.replace(",", ".")
    try:
        return float(cleaned)
    except ValueError:
        return None


def parse_iso(text: str) -> date | None:
    try:
        return date.fromisoformat(text.strip())
    except ValueError:
        return None


def load_canon_text(canon_dir: Path) -> str:
    chunks: list[str] = []
    for path in sorted(canon_dir.rglob("*")):
        if path.is_file():
            try:
                chunks.append(path.read_text(encoding="utf-8"))
            except (OSError, UnicodeError):
                continue
    return "\n".join(chunks)


def canon_has_key(canon_text: str, key: str) -> bool:
    return re.search(r"(?<![A-Za-z0-9_])" + re.escape(key) + r"(?![A-Za-z0-9_])", canon_text) is not None


def locator_looks_personal(locator: str) -> bool:
    if EMAIL_CHARS in locator:
        return True
    stripped = PHONE_SEPARATORS.sub("", locator)
    return stripped.isdigit() and len(stripped) >= 7


def check_claim(
    values: dict[str, str],
    canon_text: str,
    errors: list[str],
    warnings: list[str],
) -> None:
    for field in ("metric", "value", "unit", "source_kind", "source_locator", "public"):
        if not values[field]:
            errors.append(f"Pflichtfeld {field} fehlt")

    value = parse_number(values["value"]) if values["value"] else None
    if values["value"] and value is None:
        errors.append(f"value ist keine Zahl ({values['value']!r})")

    # Quelle
    kind = values["source_kind"]
    locator = values["source_locator"]
    if kind and kind not in SOURCE_KINDS:
        errors.append(f"unbekannter source_kind {kind!r}; erlaubt: {', '.join(sorted(SOURCE_KINDS))}")
    elif kind == "canon" and locator:
        if not canon_has_key(canon_text, locator):
            errors.append("Kanon-Schluessel nicht gefunden")
    elif kind and locator and locator_looks_personal(locator):
        errors.append("source_locator enthaelt E-Mail oder Telefonnummer; anonymisierte ID verwenden")

    public = values["public"].lower()
    if values["public"] and public not in {"true", "false"}:
        errors.append("public muss true oder false sein")

    # Prozentwerte: harte Rechenprobe
    if values["unit"] == "%":
        numerator = parse_number(values["numerator"]) if values["numerator"] else None
        denominator = parse_number(values["denominator"]) if values["denominator"] else None
        missing = [
            name
            for name in ("numerator", "denominator", "denominator_label")
            if not values[name]
        ]
        if missing:
            errors.append("Prozentwert braucht " + ", ".join(missing))
        elif numerator is None or denominator is None:
            errors.append("numerator/denominator sind keine Zahlen")
        elif denominator <= 0:
            errors.append("denominator muss groesser 0 sein")
        elif value is not None and abs(value - numerator / denominator * 100) > PERCENT_TOLERANCE:
            errors.append(
                "Rechnung stimmt nicht: "
                f"{numerator:g} / {denominator:g} = {numerator / denominator * 100:.1f} %, "
                f"Ledger sagt {value:g} %"
            )
        if value is not None and not 0 <= value <= 100:
            errors.append("Prozentwert ausserhalb 0-100")

    # Kleine Basis (gilt fuer jede Einheit, sobald ein Nenner genannt ist)
    if values["denominator"]:
        denominator = parse_number(values["denominator"])
        if denominator is None:
            if values["unit"] != "%":
                errors.append("denominator ist keine Zahl")
        elif 0 < denominator < SMALL_BASE:
            warnings.append("kleine Basis: als Einzelfall formulieren, keine Quote verallgemeinern")

    # Zeitfenster
    window_start = parse_iso(values["window_start"]) if values["window_start"] else None
    window_end = parse_iso(values["window_end"]) if values["window_end"] else None
    if not window_start or not window_end:
        errors.append("window_start/window_end muessen ISO-Daten (YYYY-MM-DD) sein")
    elif window_start > window_end:
        errors.append("window_start liegt nach window_end")

    baseline_fields = ("baseline_value", "baseline_window_start", "baseline_window_end")
    if any(values[name] for name in baseline_fields):
        missing = [name for name in baseline_fields if not values[name]]
        base_start = parse_iso(values["baseline_window_start"]) if values["baseline_window_start"] else None
        base_end = parse_iso(values["baseline_window_end"]) if values["baseline_window_end"] else None
        if missing:
            errors.append("Baseline unvollstaendig: " + ", ".join(missing) + " fehlt")
        elif parse_number(values["baseline_value"]) is None:
            errors.append("baseline_value ist keine Zahl")
        elif not base_start or not base_end:
            errors.append("Baseline-Fenster muss ISO-Daten (YYYY-MM-DD) enthalten")
        elif base_start > base_end:
            errors.append("baseline_window_start liegt nach baseline_window_end")
        elif window_start and window_end and window_start <= window_end:
            if base_end >= window_start:
                errors.append("Baseline-Fenster muss vollstaendig vor dem Ergebnisfenster liegen")
            else:
                base_days = (base_end - base_start).days + 1
                window_days = (window_end - window_start).days + 1
                if abs(base_days - window_days) / max(base_days, window_days) > WINDOW_LENGTH_TOLERANCE:
                    warnings.append("Fenster ungleich lang, Vergleich kennzeichnen")

    # Kennzahl-Ableitungen: Gesamt-CPO nie aus CPL x CRM-Quote
    metric = values["metric"].lower()
    definition = values["definition"].lower()
    if ("cpo" in metric or "cost_per_order" in metric) and "cpl" in definition and "quote" in definition:
        errors.append("Gesamt-CPO aus CPL x CRM-Quote ist unzulaessig (BRAND_AND_COPY, Referenzfall)")

    # Oeffentlich nur aus dem Kanon
    if public == "true":
        if not values["definition"]:
            errors.append("public braucht definition")
        if not values["attribution_note"]:
            errors.append("public braucht attribution_note")
        if kind and kind != "canon":
            errors.append("erst in den Kanon, dann oeffentlich")


def load_ledger(path: Path) -> tuple[list[dict[str, str]] | None, int]:
    if not path.is_file():
        print(f"FEHLER: Datei nicht gefunden: {path}", file=sys.stderr)
        return None, 2
    try:
        with path.open("r", encoding="utf-8-sig", newline="") as handle:
            reader = csv.DictReader(handle, delimiter=";")
            if reader.fieldnames != FIELDS:
                print("FEHLER: CSV-Header entspricht nicht dem Case-Proof-Schema.", file=sys.stderr)
                print("Erwartet: " + ";".join(FIELDS), file=sys.stderr)
                return None, 2
            return list(reader), 0
    except (OSError, UnicodeError, csv.Error) as exc:
        print(f"FEHLER: CSV konnte nicht gelesen werden: {exc}", file=sys.stderr)
        return None, 2


def is_lagging(metric: str) -> bool:
    lowered = metric.lower()
    return any(hint in lowered for hint in LAGGING_HINTS)


def print_brief(claims: list[dict[str, str]], notes: list[tuple[str, str]], warns: list[tuple[str, str]]) -> None:
    eligible = [c for c in claims if c["public"].lower() == "true"]
    # Nachgelagerte Ergebnisse zuerst; sonst Ledger-Reihenfolge (stabile Sortierung).
    eligible.sort(key=lambda c: 0 if is_lagging(c["metric"]) else 1)
    chosen = eligible[:MAX_RESULT_CLAIMS]

    print()
    print("CASE-BRIEF (Skelett, kein fertiger Text)")
    print()
    print("Ausgangslage")
    print("  [PLATZHALTER: 2-3 Saetze aus Evidenz schreiben (Ausgangsproblem, Segment, Zeitraum); nichts erfinden]")
    print()
    print("Mechanismus")
    print("  [PLATZHALTER: was wurde konkret getan; nur belegte Schritte, keine Wirkung ohne Beleg zuschreiben]")
    print()
    print("Ergebnis")
    if not chosen:
        print("  Keine oeffentlich zulaessigen Claims im Ledger: keine Kennzahl nennen.")
    for claim in chosen:
        line = (
            f"- {claim['metric']}: {claim['value']} {claim['unit']}"
            f" | Zeitraum {claim['window_start']} bis {claim['window_end']}"
        )
        if claim["denominator_label"]:
            basis = claim["denominator_label"]
            if claim["denominator"]:
                basis += f" (n={claim['denominator']})"
            line += f" | Bezug: {basis}"
        if claim["baseline_value"]:
            line += (
                f" | vorher: {claim['baseline_value']} {claim['unit']}"
                f" ({claim['baseline_window_start']} bis {claim['baseline_window_end']})"
            )
        line += f" | Definition: {claim['definition']}"
        print(line)
    if len(eligible) > len(chosen):
        print(f"  Hinweis: {len(eligible) - len(chosen)} weitere zulaessige Claims bewusst nicht aufgenommen (Maximum {MAX_RESULT_CLAIMS}).")
    print()
    print("Grenzen")
    if not notes and not warns:
        print("  [PLATZHALTER: keine Zuordnungshinweise im Ledger; Grenzen selbst benennen]")
    for claim_id, note in notes:
        print(f"- {claim_id}: {note}")
    for claim_id, message in warns:
        print(f"- WARN {claim_id}: {message}")
    print()
    print("Zitat")
    print("  [ZITAT FEHLT: Zitate nur aus freigegebenen direct_voc-Eintraegen (evidence-standard, Abschnitt Promotion gate); sonst weglassen]")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Case-Study-Claim-Ledger pruefen; jede Zahl braucht Beleg, Nenner und Zeitfenster."
    )
    parser.add_argument("ledger", type=Path, help="Semikolon-CSV Claim-Ledger")
    parser.add_argument("--brief", action="store_true", help="bei fehlerfreiem Ledger ein Case-Brief-Skelett ausgeben")
    parser.add_argument("--canon-dir", type=Path, default=None, help="Kanon-Verzeichnis (Default: blocksy-child/inc/canon im Repo)")
    args = parser.parse_args()

    canon_dir = args.canon_dir or (repo_root() / "blocksy-child/inc/canon")
    if not canon_dir.is_dir():
        print(f"FEHLER: Kanon-Verzeichnis nicht gefunden: {canon_dir}", file=sys.stderr)
        return 2

    rows, status = load_ledger(args.ledger)
    if rows is None:
        return status
    if not rows:
        print("FEHLER: Ledger enthaelt keine Claims.", file=sys.stderr)
        return 1

    canon_text = load_canon_text(canon_dir)
    seen: set[str] = set()
    counts = {"OK": 0, "WARN": 0, "ERROR": 0}
    claims: list[dict[str, str]] = []
    notes: list[tuple[str, str]] = []
    warn_lines: list[tuple[str, str]] = []

    for line_number, row in enumerate(rows, start=2):
        values = {name: (row.get(name) or "").strip() for name in FIELDS}
        claim_id = values["claim_id"] or f"Zeile {line_number}"
        errors: list[str] = []
        warnings: list[str] = []
        if not values["claim_id"]:
            errors.append("Pflichtfeld claim_id fehlt")
        elif claim_id in seen:
            errors.append("doppelte claim_id")
        seen.add(claim_id)
        if row.get(None) is not None:
            errors.append("zusaetzliche CSV-Spalten; Semikolons im Text muessen in Anfuehrungszeichen stehen")
        check_claim(values, canon_text, errors, warnings)

        status_label = "ERROR" if errors else "WARN" if warnings else "OK"
        counts[status_label] += 1
        reasons = errors + warnings
        print(f"{status_label} {claim_id}: " + (" | ".join(reasons) if reasons else "belegt"))

        values["claim_id"] = claim_id
        claims.append(values)
        if values["attribution_note"]:
            notes.append((claim_id, values["attribution_note"]))
        warn_lines.extend((claim_id, message) for message in warnings)

    public_eligible = sum(
        1 for claim in claims if claim["public"].lower() == "true"
    ) if counts["ERROR"] == 0 else 0
    print(
        f"SUMMARY: claims={len(claims)}; ok={counts['OK']}; warn={counts['WARN']}; "
        f"error={counts['ERROR']}; public_eligible={public_eligible}"
    )

    if counts["ERROR"]:
        return 1
    if args.brief:
        print_brief(claims, notes, warn_lines)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
