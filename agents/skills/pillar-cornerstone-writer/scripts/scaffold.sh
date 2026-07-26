#!/usr/bin/env bash

set -euo pipefail

topic="${1:-Pillar Topic}"
slug_input="${2:-$topic}"

# Umlaute zuerst ausschreiben. Ohne diesen Schritt macht die [^a-z0-9]-Regel aus
# "Wärmepumpen" den Slug "w-rmepumpen" — bei diesem Themenfeld kein Randfall.
slug="$(
  printf '%s' "$slug_input" \
    | sed 's/Ä/Ae/g; s/Ö/Oe/g; s/Ü/Ue/g; s/ä/ae/g; s/ö/oe/g; s/ü/ue/g; s/ß/ss/g' \
    | tr '[:upper:]' '[:lower:]' \
    | sed 's/[^a-z0-9]/-/g' \
    | sed 's/-\{2,\}/-/g; s/^-//; s/-$//'
)"

cat <<EOF
# --- Vorbedingung -----------------------------------------------------------
# Ownership geprueft?  bash agents/skills/seo-agent/scripts/intent-gate.sh check "<query>" /${slug}/
# AEO-Regeln gelesen?  agents/skills/pillar-cornerstone-writer/references/aeo-answer-patterns.md
# ----------------------------------------------------------------------------

SEO title:
Meta description:
URL slug: /${slug}/
Ziel-Query (Owner laut query-ownership.csv):

# ${topic}

<!-- Einstieg: Kernaussage in 2-3 Saetzen. Kein Aufwaermen. -->

## Why This Matters

## The Strategic Mechanism

<!-- AEO-Block. Frage-H2s: Fragezeichen als LETZTES Zeichen, erster Satz
     beantwortet vollstaendig, 40-120 Woerter bis zur naechsten Ueberschrift,
     keine Tabellen darin. Mindestens 2, hoechstens 4. Fragen aus echten
     Suchanfragen ableiten, nicht erfinden. -->

## <Frage 1 aus echter Suchanfrage>?

<!-- Direktantwort im ersten Satz. Dann Begruendung. Entitaeten ausschreiben. -->

## <Frage 2 aus echter Suchanfrage>?

<!-- Direktantwort im ersten Satz. Dann Begruendung. -->

## Common Failure Patterns

## A Practical Framework

<!-- Tabellen/Vergleiche gehoeren hierher, NICHT unter eine Frage-Ueberschrift. -->

## Visual Concept

## Next Step

## Kernaussage

<!-- Fazit im letzten Zehntel, in eigenen Saetzen. Haeufig der Block, der als
     Ganzes zitiert wird. Keine Floskel-Zusammenfassung. -->

## Suggested Internal Links

- Anchor text ->
- Anchor text ->
- Anchor text ->

## Optional Image Brief

- Alt text:
- Title:
- Caption:

# --- Nach dem Veroeffentlichen ----------------------------------------------
# 1. SCF-Feld "FAQ-Schema erzwingen" aktivieren
# 2. Beitrag einmal speichern (sonst bleibt der Schema-Cache leer)
# 3. acceptedAnswer.text pruefen: lesbarer Text oder Tabellen-Klumpen?
# 4. Ziel-Query in docs/seo/query-ownership.csv eintragen
# ----------------------------------------------------------------------------
EOF
