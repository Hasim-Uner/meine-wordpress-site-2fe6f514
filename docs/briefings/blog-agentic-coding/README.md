# Briefing: Fachartikel „Agentic Coding“

Fachartikel `/agentic-coding/` auf den Artikel-Bausteinen, entstanden am
18.10.2026 aus einem Entwurf von Haşim, nach Wettbewerbsprüfung erweitert.

| Datei | Stand im Repo |
|---|---|
| `titelbild.html`, `render-titelbild.cjs` | Quelle des Titelbilds. `node docs/briefings/blog-agentic-coding/render-titelbild.cjs` schreibt `blocksy-child/assets/img/blog/agentic-coding-hero.png` (2400 × 1260, Farben aus `.tafel`, Schriften aus `blocksy-child/fonts/`) |
| Artikel | `blocksy-child/assets/content/blog/agentic-coding.html`, Seed in `blocksy-child/inc/article-agentic-coding.php` |
| Abb. 1 | `blocksy-child/template-parts/editorial/abb-agent-schleife.php` |

## Wettbewerb (SERP-Prüfung 18.10.2026, ohne bezahltes Tool)

Auf den vorderen Plätzen stehen Anbieter-Glossare (IBM, Google Cloud,
TrueFoundry), ein deutscher Berater-Artikel (nevercodealone.de) und englische
Entwickler-Guides (Scrimba, Sourcegraph). Alle erklären das Werkzeug aus
Entwicklersicht. Keiner zeigt die Sicht des Auftraggebers, keiner ordnet die
METR-Studien korrekt ein (das Update vom Februar 2026 wird oft als
„Umkehrung“ zitiert), keiner hat eigene Praxis. Darauf baut der Artikel:
Abgrenzungstabelle, Bauformen statt Produktliste, Studienlage mit
Einschränkungen, WordPress-Beispiel mit prüfbarem Auftrag, Prüfliste,
Sicherheitsregeln, 14 Frage-Antwort-Blöcke für Suche und KI-Antworten.

## Offen

- Der Absatz „Aus meiner Praxis“ beschreibt einen eigenen Fehler auf dieser
  Website. Vor dem Merge freigeben oder streichen.
- Werkzeugtabelle („Stand Oktober 2026“) halbjährlich prüfen.

## Screenshots

`screenshots/`: lokal gerendert mit dem Reader-Template, den echten Bausteinen und Stylesheets (ohne WordPress), 1440 und 390 px. Enthält auch `/website-relaunch/` mit dem neuen Inhaltsverzeichnis.
