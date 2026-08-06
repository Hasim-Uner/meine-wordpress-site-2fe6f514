---
name: seo-conversion-copywriting
description: Write and rewrite sales-oriented German website copy in this repo — Money-Pages, Landingpages, Leistungsseiten, Hero-Headlines, Sublines, CTA-Labels, Einwand- und FAQ-Absätze, Proof-Texte sowie Meta-Title und -Description dieser Seiten. Use whenever visible German copy on a converting page is written, rewritten, shortened, or tuned for a search query, including terse requests like "Text für Seite X", "Hero neu schreiben", "Copy schärfen" or "Landingpage texten". Not for prioritized CRO page critiques (wordpress-cro-content-design-audit), blog or cornerstone articles (pillar-cornerstone-writer), or landing page scaffolding (landing-page-builder).
---

# SEO & Conversion Copywriting

Diese Skill schreibt den Text. Die Seitenkritik mit Priorisierung macht
`wordpress-cro-content-design-audit`, Artikel macht `pillar-cornerstone-writer`.

## Vor dem Schreiben

1. `docs/standards/BRAND_AND_COPY.md` — Positionierung, Ton, Hard Bans
2. `blocksy-child/inc/canon/messaging-canon.php` — kundenseitig gesperrte Begriffe
3. `[BELEGDATEI EINTRAGEN]` — die einzige Quelle für Zahlen
4. Die Zielseite selbst, dazu `llms.txt` für Route und CTA-Ziel

## A — Harte Regeln

Werden nie abgewogen. Ein einziger widerlegbarer Satz entwertet die ganze Seite,
und bei Rechts- oder Ergebnisaussagen kostet er mehr als eine Conversion.

- Keine erfundenen Zahlen, Referenzen oder Kundenergebnisse.
- Keine Garantien, keine Aussagen zu Rechtskonformität, keine absoluten
  Versprechen. Technische Tatsache statt Rechtsurteil: „läuft ohne Cookies" ja,
  „DSGVO-konform" nein.
- Belegbare Zahlen stehen ausschließlich in `[BELEGDATEI EINTRAGEN]`. Was dort
  nicht steht, wird nicht geschrieben, sondern als `[BELEG FEHLT: Aussage]`
  markiert und offen zurückgemeldet.
- Keine Kundennamen. Neu geschriebene Copy beschreibt anonymisiert: Gewerk,
  Größenordnung, Ausgangslage. (Bestehende freigegebene Cases zu entfernen ist
  eine eigene Entscheidung, keine Nebenwirkung dieser Regel.)
- Fachliche Einschränkungen und Nachteile offen benennen. Eine genannte Grenze
  qualifiziert vor; eine verschwiegene kostet den Termin später.

## B — Handwerk

- Aufbau: **Problem → Ursache → Lösung → Beleg → Einwand → CTA.** Die Ursache
  ist der Schritt, den Agenturprosa überspringt — ohne sie klingt jede Lösung
  austauschbar.
- Erst für Menschen, dann für Suchmaschinen. Suchintention und primäres Keyword
  bestimmen Thema, H1 und Einstieg. Wiederholt wird ein Keyword nur, wenn der
  Satz es ohnehin braucht.
- Nutzen konkret mit wirtschaftlicher Folge: was sich ändert, woran der Betrieb
  es merkt, was es kostet oder spart.
- Tragende Überschriften. Wer nur die H2 liest, kennt die Argumentation. Keine
  Etiketten wie „Unsere Leistungen".
- Eine Handlungsaufforderung pro Entscheidungszone. Das Label sagt, was der
  Besucher bekommt, nicht was er tut.
- Natürliche deutsche Sätze, keine KI-, Marketing- oder Agenturfloskeln
  (ganzheitlich, maßgeschneidert, passgenau, innovativ, nahtlos, aus einer Hand,
  Ihr Partner für). Ein Satz, der auf jeder Wettbewerberseite stehen könnte, ist
  noch nicht fertig.

## C — Prüfung vor Abschluss

```bash
agents/skills/seo-conversion-copywriting/scripts/copy-check.sh
```

Der Report zeigt Zahlen, Versprechen, Floskeln und offene Belegmarker in der
geänderten Copy. Er ersetzt kein Urteil — diese vier Fragen bleiben:

1. Steht eine unbelegte Zahl im Text?
2. Steht ein Versprechen darin, das nicht gehalten werden kann?
3. Wurde eine Einschränkung weggelassen?
4. Liest sich ein Absatz wie Agenturprosa?

Frage 3 findet kein Muster. Dafür die Lösung gegen die Realität lesen: wofür
passt sie nicht, was setzt sie voraus, wer ist nicht die Zielgruppe.

ASCII-Umlaute und Canon-Drift prüft die CI bereits
(`scripts/check-german-copy.sh`, `scripts/lint-canon-drift.sh`).

## Deliver

- Fertige Copy je Abschnitt, in der Reihenfolge der Seite
- Liste der offenen `[BELEG FEHLT: …]`-Marker: was geliefert werden muss, damit
  die Aussage stehen bleiben darf
- Trennung `Repo` (Template/Partial) und `Manual WP` (Editor-Copy)
- Meta-Title und -Description, wenn die Seite eine eigene Route hat
