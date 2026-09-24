# Auftrag, Durchlauf 2: Blogartikel „Website-Relaunch“ in neuer Fassung

Stand 2026-09-24 · Auftraggeber Haşim · erstellt von Argos

## Worum es geht

Der Beitrag `/website-relaunch/` (PR #449) bekommt eine neue Kernaussage. Fassung 1 fragte, was ein Relaunch verlieren darf. Fassung 2 fragt, **wann die neue Website wirklich besser ist**. Der Verlust-Teil bleibt als eigener Abschnitt „Was dabei nicht verloren gehen darf“ erhalten.

Die Bausteine aus Durchlauf 1 bleiben. Neu sind eine zweite Abbildung und ein frei setzbarer Nachsatz im Fall-Baustein. Alles andere ist Inhalt und Metadaten.

Primärer Skill: `editorial-seo`, delegiert an `pillar-cornerstone-writer`.

## Dateien in diesem Ordner

| Datei | Rolle |
|---|---|
| `artikel.html` | **Freigegebene Copy, Fassung 2**, ersetzt `assets/content/blog/website-relaunch.html` vollständig. Nicht umformulieren. |
| `referenz-vorschau.html` | Gestaltungsreferenz. Neu darin ist Abb. 2 in Abschnitt 03. |
| `abb2-hell.svg`, `abb2.py` | Abb. 2 als fertiges SVG und das Skript, das es erzeugt, mit den Werten und der Rechnung. |
| `website-relaunch-hero-v2.png` | Neues Titelbild 2400 × 1260 px, ersetzt das alte als Beitragsbild und og:image. |

## Änderungen

### 1. Inhalt und Seed

- `assets/content/blog/website-relaunch.html` wird durch `artikel.html` ersetzt.
- In `inc/article-website-relaunch.php` die Seed-Version auf `2026-09-24-website-relaunch-v2` setzen, sodass der bestehende, vom Seed verwaltete Beitrag einmal überschrieben wird. Andere Beiträge bleiben unberührt.
- Metadaten:
  - Titel: `Website-Relaunch: Wann die neue Website wirklich besser ist`
  - SEO-Title: `Website-Relaunch: Wann die neue Website wirklich besser ist`
  - Meta-Description: `Besser heißt nicht moderner. Wie Sie vor dem Relaunch festlegen, was an jeder Stelle besser werden soll, und was dabei nicht verloren gehen darf.`
  - Excerpt (Vorspann): `Eine neue Website ist besser, wenn an jeder Stelle mehr der richtigen Besucher einen Schritt weiterkommen und Sie das sehen können. Dass sie moderner aussieht, ist dafür ein Mittel, kein Beweis. Dieser Artikel zeigt, was eine B2B-Website eigentlich leistet, wie Sie „besser“ vor dem Relaunch messbar machen und was auf dem Weg dorthin nicht verloren gehen darf.`
  - Slug `website-relaunch`, Dossier `cro`, Tags und FAQ-Flag bleiben.
- Titelbild: `assets/img/blog/website-relaunch-hero-v2.png`. Der neue Dateiname ist Absicht, damit kein gecachtes Bild stehen bleibt. Alt-Text: `Titelbild: Wann die neue Website wirklich besser ist. Darunter zwei Bänder von der Suche bis zum Auftrag: An vier Übergängen kommt nachher jeweils ein Fünftel mehr weiter, am Ende rund doppelt so viele.` Beitragsbild und `og_image` zeigen auf das neue Bild, das alte Bild kann aus dem Repo.

### 2. Neue Abbildung `relaunch-besser`

`[hu_abb id="relaunch-besser"]` lädt `template-parts/editorial/abb-relaunch-besser.php`. Vorlage ist Abb. 2 in `referenz-vorschau.html`.

- Kopf: „Abb. 2“ und der Titel „Vorher und nachher: wie viele an jedem Übergang weiterkommen“, dazu die Legende vorher (Fläche `--zone2`, gestrichelter Rand `--grau`) und nachher (Stempel-Fläche mit geringer Deckkraft, Rand `--stempel`).
- Darunter das SVG aus `abb2-hell.svg`. Die Farbwerte darin bitte auf `currentColor` bzw. CSS-Variablen umstellen, damit keine Farbe außerhalb von `system.css` steht.
- Unter 640 px scrollt die Grafik in einem eigenen Container. Die Seite selbst scrollt nicht horizontal.
- Bildunterschrift wörtlich: `Schematisch, keine Messwerte. An vier Übergängen kommt jeweils ein Fünftel mehr weiter. Keiner der Schritte ist groß, am Ende der Kette sind es trotzdem rund doppelt so viele.`
- `title` und `desc` im SVG bleiben, sie sind die Textfassung für Screenreader.
- Die Abbildung trägt `data-hu-schema-skip` wie Abb. 1.
- Die Werte sind eine Rechnung, keine Messung: Vier Übergänge werden jeweils mit 1,2 multipliziert, das Ergebnis ist 1,2⁴ ≈ 2,07. Sie stehen in `abb2.py` und gehören **nicht** in einen Kanon. Die Grafik zeigt keine Zahlen außer „+ 20 %“ und „rund 2 ×“.

### 3. Fall-Baustein mit eigenem Nachsatz

`[hu_fall id="pv-anfragesystem"]…[/hu_fall]`: Ist der Shortcode umschließend und hat Inhalt, ersetzt dieser Inhalt den Standard-Nachsatz im Partial. Der Link „Zur Fallstudie“ bleibt dahinter stehen. Ohne Inhalt gilt weiter der bisherige Nachsatz, so bleiben andere Verwendungen unverändert. Titel, Text und Zahlenreihe des Falls ändern sich nicht.

### 4. Kleinigkeiten

- Die Abschluss-Tafel bekommt ebenfalls `data-hu-schema-skip`, falls nicht schon gesetzt.
- `docs/architecture/LIVE_STATUS.md`: Titel und Kernaussage des Beitrags nachziehen.
- `references/bausteine.md`: Ergänzen, dass `[hu_fall]` einen eigenen Nachsatz annimmt und dass es `abb-relaunch-besser` gibt.

## Nicht tun

- Die Copy umschreiben, kürzen oder „verbessern“.
- Zahlen als Literal in Inhalt oder Partials schreiben. Ausnahme ist die schematische Abb. 2, siehe oben.
- Abb. 1, die Prüfliste, die Tabellenklassen oder andere Beiträge verändern.

## Abnahme

1. `npm run lint:architecture`, `npm run lint:php` und `scripts/canon-guard.sh` sind grün.
2. Der Beitrag zeigt Titel und Excerpt der Fassung 2, das neue Titelbild und das neue og:image (2400 × 1260).
3. Das FAQPage-Schema enthält **sechs** Fragen: „Was ist eine B2B-Website?“ und die fünf im Fragenblock, darunter neu „Woran erkennt man, dass ein Website-Relaunch erfolgreich war?“. Keine `acceptedAnswer` enthält Text aus Notiz, Abbildung, Fall, Abschluss oder Quellen.
4. Darstellung bei 360, 768, 1280 und 1440 px ohne horizontales Scrollen der Seite. Abb. 2 ist bei 1280 px vollständig sichtbar, ohne abgeschnittene Beschriftung rechts.
5. Der Fall in Abschnitt 04 zeigt den neuen Nachsatz. Eine Verwendung von `[hu_fall]` ohne Inhalt zeigt den alten.
6. Screenshots bei 1440 und 390 px liegen im PR.
