---
name: channel-copy
description: Print-Brief an eine namentlich genannte Geschäftsführung und LinkedIn-Inbound-Beiträge aus Audits oder eigenen Konzepten. Mechanik, Rechtsrahmen, Linter. Nicht für Kaltakquise per Mail, Telefon oder DM.
---

# Channel Copy

Trigger: Entwurf für einen gedruckten Brief an eine Geschäftsführung oder für einen LinkedIn-Beitrag, der einen Audit-Befund oder ein eigenes Konzept als These erzählt. Beide Kanäle liegen außerhalb der Website; Seitencopy geht zurück an den Primärskill `conversion-copy`.

Vorher laden:
- `docs/standards/BRAND_AND_COPY.md`: Fakten stehen nie als Text, Tone, Anti-Patterns
- Kanal: `references/direct-mail.md` oder `references/linkedin-inbound.md`, nur einer

Erster Befehl, sobald ein Entwurf in `.ai/memory/` liegt:

```bash
python3 agents/skills/channel-copy/scripts/channel-check.py brief <entwurf.md>
python3 agents/skills/channel-copy/scripts/channel-check.py linkedin <entwurf.md>
```

Der Linter findet Muster (Quelle genannt, Verbote, Länge), nicht Wahrheit. Mit `--strip` gibt er den Text ohne Marker aus.

Prüfraster für jeden Satz (aus dem Copywriting-Teil von coreyhaines31/marketingskills übernommen):
1. Vorstellbar: Sieht der Leser das Gemeinte vor sich?
2. Beweisbar: Gibt es Quelle oder Beobachtung dafür?
3. Swap-Test: Könnte ein Wettbewerber den Satz unverändert veröffentlichen? Dann streichen.

Bewusstseinsstufe des Lesers (problem-unaware bis product-aware) bestimmt den nächsten Schritt, nie den Kauf. Die Positionierungskette (Alternativen, Unterscheidungsmerkmal, Wert, Zielsegment) wählt den einen Mechanismus.

Hard rules:
- Zahlen und Kontaktwege aus dem Kanon (`blocksy-child/inc/canon/`), Befunde mit `[Q: …]`-Marker; ohne Beleg wird gestrichen.
- Keine Kaltakquise per E-Mail, Telefon oder LinkedIn-DM; Brief-Nachfassen nur per Post oder nach Antwort.
- Widerspruchsliste führen und einhalten; Rechtsrahmen steht in `references/direct-mail.md`, keine Rechtsberatung.
- Keine Kundennamen ohne schriftliche Freigabe, keine erfundenen Begriffsdefinitionen des Inhabers.
- Der Inhaber gibt vor Druck oder Posting frei.

Deliverable: Entwurf, saubere Linter-Ausgabe, bereinigte Endfassung (`--strip`), offene Rechts- und Freigabepunkte.
