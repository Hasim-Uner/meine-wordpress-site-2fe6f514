---
name: copy-anatomy
description: Fremde Copy in ihre Struktur zerlegen und das übertragbare Muster extrahieren — Abschnittsfolge, Beweisführung, Einwandlogik, CTA-Ökonomie. Use wenn eine Wettbewerber-, Portal- oder Vorbildseite analysiert werden soll ("Was macht die Seite gut?", "Zerleg mir die Landingpage von X", "Warum konvertiert das?"), oder wenn vor einem Copy-Auftrag ein Strukturvorbild fehlt. Nicht zum Schreiben von Copy (seo-conversion-copywriting) und nicht für eigene Routen (route-conversion-review).
---

# Copy Anatomy

Starke Seiten funktionieren wegen ihrer Struktur, nicht wegen ihrer Sätze. Der
Skill trennt beides: er nimmt das Gerüst mit und lässt den Text liegen.

## Abgrenzung

- Eigene Routen bewertet `route-conversion-review`.
- Copy schreibt `seo-conversion-copywriting` — dieser Skill liefert ihm nur
  das Strukturvorbild.
- Reine SEO-Wettbewerbsanalyse (Rankings, Keywords, Sichtbarkeit) gehört zu
  `seo-agent`.

## Load First

1. `docs/standards/BRAND_AND_COPY.md` — sonst lässt sich nicht beurteilen, was
   übertragbar ist und was an der Positionierung scheitert
2. `seo-research/2026-07/data/competitors.md` — Kandidaten und Einordnung

Quelle holen per WebFetch, oder der Nutzer fügt die Copy ein.

## Harte Regel

**Muster ja, Text nein.** Kein Satz und keine Halbzeile aus der Quelle geht in
einen Vorschlag. Der Output beschreibt, *welche Aufgabe* ein Abschnitt erfüllt,
nie *mit welchen Worten*. Wer eine Formulierung zitiert, tut das erkennbar als
Beleg der Analyse und markiert sie als Zitat.

Zwei Gründe, und der zweite wiegt schwerer: übernommene Copy ist fremde
Leistung, und sie trägt die Positionierung des Wettbewerbers mit. Eine
Portal-Landingpage verkauft Bequemlichkeit — genau das Gegenteil unserer
Positionierung „eigene Nachfrage-Infrastruktur statt Portal-Zukauf".

Wörtliche Kundenformulierungen aus solchen Quellen sind **keine** Voice of
Customer und gehören nicht in `docs/standards/VOICE_OF_CUSTOMER.md`. Das ist
Marktsprache, formuliert von einer fremden Marketingabteilung.

## Zerlegung

Je Abschnitt der Quelle:

1. `Position` — wievielter Abschnitt, über oder unter dem Fold
2. `Aufgabe` — was der Abschnitt im Kopf des Lesers erledigen soll
3. `Mittel` — Behauptung, Beweis, Vergleich, Zahl, Bild, Angst, Entlastung
4. `Übergang` — welche Frage er offenlässt, die der nächste Abschnitt aufgreift

Danach über die ganze Seite:

- `Beweisführung` — worauf stützt sich der Anspruch, und an welcher Stelle
  kommt der Beweis relativ zur Forderung?
- `Einwandlogik` — welche Einwände werden vorweggenommen, welche ignoriert,
  und was verrät das über die Zielgruppe?
- `CTA-Ökonomie` — was verlangt jeder CTA, was gibt er zurück, und wie oft
  wechselt die Seite das Angebot?
- `Auslassung` — was fehlt auffällig. Das ist oft der verwertbarste Befund.

## Deliver

- `Struktur` — die Abschnittsfolge als Tabelle: Position, Aufgabe, Mittel
- `Was trägt` — Muster, die unabhängig von Branche und Text funktionieren
- `Was nicht übertragbar ist` — mit Begründung aus `BRAND_AND_COPY.md`,
  namentlich Hard Bans und der Diagnose-vor-Pitch-Reihenfolge
- `Auslassungen` — was die Quelle nicht tut und warum das eine Lücke sein könnte
- `Übergabe` — welcher Abschnitt welcher Route als Strukturvorbild dient,
  als Auftrag für `seo-conversion-copywriting` formuliert

Kein Fließtextessay. Wer die Tabelle liest, kennt die Seite.
