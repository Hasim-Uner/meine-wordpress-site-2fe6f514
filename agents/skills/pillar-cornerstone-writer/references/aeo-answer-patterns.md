# AEO-Schreibmuster — abgeleitet aus dem eigenen FAQ-Schema-Code

Diese Regeln sind **keine allgemeine AEO-Theorie**, sondern aus dem Verhalten von
`hu_extract_faq_schema_entities_from_content()` (`blocksy-child/inc/org-schema.php:313-382`)
abgeleitet. Wer sie ignoriert, erzeugt entweder gar kein FAQPage-Schema oder ein
unbrauchbares.

## Wie der Mechanismus tatsächlich arbeitet

1. **Opt-in.** Nur wenn das ACF-Feld `enable_faq_schema` gesetzt ist, werden
   Überschriften ausgewertet (`acf.php:77`). Ohne Flag passiert nichts.
2. **Frage = Überschrift H2–H4, deren Text auf `?` endet.** Die Prüfung ist
   `preg_match( '/\?\s*$/u', $question )` — das Fragezeichen muss das **letzte
   Zeichen** sein.
3. **Antwort = der komplette Inhalt bis zur nächsten H2–H4.** Nicht der erste
   Absatz. Alles.
4. **Markup wird entfernt.** `wp_strip_all_tags()` macht aus Tabellen, Listen und
   Links reinen Fließtext ohne Trennzeichen.
5. **Dedupe über den kleingeschriebenen Fragetext.** Zwei gleich formulierte Fragen
   im selben Beitrag → die zweite verschwindet lautlos.
6. **Cache beim Speichern.** Änderungen am Text greifen erst nach erneutem Speichern
   des Beitrags (`hu_update_post_faq_schema_cache`).

## Daraus folgende Schreibregeln

### R1 — Fragezeichen ans Ende, sonst nichts

```text
✓ Was kostet ein Photovoltaik-Lead?
✗ Was kostet ein Photovoltaik-Lead? Ein Überblick     (endet nicht auf ?)
✗ Kosten von Photovoltaik-Leads                        (keine Frage)
```

Ein Zusatz nach dem Fragezeichen macht die Überschrift für das Schema unsichtbar.
Der Untertitel gehoert in den ersten Absatz, nicht in die Zeile.

### R2 — Direktantwort im ersten Satz nach der Überschrift

Der erste Satz beantwortet die Frage vollständig. Begründung, Kontext und
Einschränkungen kommen danach. Grund: Wer die Antwort extrahiert — Google-Snippet
oder LLM — nimmt typischerweise den Anfang.

```text
## Was kostet ein Photovoltaik-Lead?

Portal-Leads liegen im DACH-Raum bei 60 bis 120 Euro pro Anfrage, exklusive
Leads deutlich darüber. Der Preis allein sagt allerdings wenig — entscheidend
ist der Cost-per-Order, weil ein geteilter Lead mehrfach verkauft wird.
```

Nicht so: „Diese Frage lässt sich nicht pauschal beantworten, denn …" — das ist
als extrahierte Antwort wertlos.

### R3 — Frage-Block kurz halten (Ziel: 40–120 Wörter)

Weil die Antwort bis zur **nächsten Überschrift** reicht, wird ein 600-Wörter-Block
komplett zum `acceptedAnswer.text`. Das erzeugt aufgeblähtes Schema.

Wer nach einer Frage-Überschrift lange ausholen will, setzt eine normale
(nicht-fragende) H3 als Schnitt — sie beendet den Antwort-Block, ohne selbst eine
Frage-Entität zu erzeugen.

### R4 — Keine Tabellen und keine mehrstufigen Listen im Frage-Block

`wp_strip_all_tags()` macht aus

```text
| Portal | CPL | | Aroundhome | 80 € |
```

den Text `Portal CPL Aroundhome 80 €` — unlesbar. Tabellen und Vergleichslisten
gehören unter eine **normale** Überschrift. Kurze einstufige Aufzählungen sind ok,
weil sie als Satzfolge noch funktionieren.

### R5 — Jeder Antwort-Block steht für sich

Kein „wie oben beschrieben", kein „das gilt auch hier". Ein extrahierter Block
erscheint ohne seinen Kontext. Entität ausschreiben statt Pronomen: nicht „das
Portal", sondern „Aroundhome".

### R6 — Fragen nicht doppelt formulieren

Zwei Überschriften mit demselben Wortlaut → nur die erste überlebt. Bei nah
verwandten Fragen unterschiedlich formulieren oder zusammenlegen.

### R7 — Zahlen nur mit Beleg

Gilt unverändert aus `CLAUDE.md`: keine erfundenen Volumina, keine geschätzten
Analytics-Werte. Eine zitierfähige Antwort mit falscher Zahl ist schlimmer als
gar keine — sie wird zitiert.

### R8 — Fazit im letzten Zehntel

Der Beitrag endet mit einer kurzen Zusammenfassung der Kernaussage in eigenen
Sätzen (kein „Fazit:"-Floskelabsatz). Sie ist oft der Block, der als Ganzes
zitiert wird.

## Was hier bewusst NICHT steht

- **Keine „Entity Density"-Zielwerte.** Kursierende Angaben wie „~20 %" sind
  Scheinpräzision ohne validierte Grundlage. Stattdessen: Entitäten ausschreiben
  (R5) — das ist der überprüfbare Teil derselben Idee.
- **Kein „Citation Readiness Score".** Ohne Messmöglichkeit (es gibt kein
  Search-Console-Äquivalent für LLM-Antworten) wäre jede Punktzahl erfunden.

## Nach dem Veröffentlichen

1. ACF-Feld **FAQ-Schema erzwingen** aktivieren.
2. Beitrag einmal speichern — sonst bleibt der Schema-Cache leer.
3. Schema mit dem Rich-Results-Test oder View-Source prüfen: Enthält
   `acceptedAnswer.text` den erwarteten, lesbaren Text — oder einen
   Tabellen-Klumpen? Letzteres heisst: R4 verletzt.
