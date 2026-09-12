# Start-Prompt für Claude Code

> **Archivstand.** Originaltext vom 12.09.2026. Schritt 1 ist mit diesem Commit
> erledigt, Schritt 2 beschreibt einen Arbeitsstand, der inzwischen abgeschlossen ist.
> Schritt 3 ist offen. Die mit **Nachtrag** markierten Kästen sagen, was jeweils gilt.

## Schritt 1 — Dateien ins Repo legen

```
docs/briefings/solar-money-page/
  ├── auftrag.md                    ← codex-auftrag-solar-money-page.md
  ├── referenz.html                 ← anfragestrecke-produktion.html
  ├── copy-durchgang.md             ← copy-durchgang-2-conversion.md
  └── seo-briefing.md               ← briefing-solar-money-page.md
```

Die vier Dateien einmal committen. Danach kann jede Claude-Code-Sitzung sie lesen, ohne dass du sie
erneut einfügst.

> **Nachtrag — die Ablage sieht anders aus als geplant.**
> `briefing-solar-money-page.md` existiert nicht und wurde nie geliefert. `seo-briefing.md`
> ist deshalb neu aus dem umgesetzten Stand geschrieben und dokumentiert, was tatsächlich
> ausgeliefert wird. Dieses Dokument hier lag als vierte Datei vor und heißt jetzt
> `start-prompt.md`, weil es ein Einrichtungsprotokoll ist und kein SEO-Briefing.
> Tatsächlicher Ordnerinhalt:
>
> ```
> docs/briefings/solar-money-page/
>   ├── README.md                     ← Einstieg, was wovon die Quelle ist
>   ├── auftrag.md                    ← Umbauauftrag, mit Nachträgen
>   ├── referenz.html                 ← Vorlage, Fassung nach Copy-Durchgang 2
>   ├── copy-durchgang.md             ← Begründung der Copy-Entscheidungen
>   ├── seo-briefing.md               ← ausgelieferte SEO-Signale, neu geschrieben
>   └── start-prompt.md               ← dieses Dokument
> ```

## Schritt 2 — Diesen Prompt in Claude Code einfügen

```
Lies docs/briefings/solar-money-page/auftrag.md vollständig, bevor du irgendetwas änderst.
Die Datei referenz.html im selben Ordner ist die maßgebliche Vorlage für Copy, Reihenfolge,
Zahlen und Verhalten der Seite /solar-waermepumpen-leadgenerierung/. Sie ist eigenständiges
HTML, kein WordPress — übersetze sie in unsere Templates, lade sie nicht hoch.

Arbeite nur Abschnitt 7 des Auftrags ab, Punkte 1 bis 3: JSON-LD, Heading-IDs, Breadcrumb,
og:image, Alt-Text, llms.txt, Antwortzeit sitewide vereinheitlichen, Title und Description.
Das sind die Schritte, die ohne meine offene Designentscheidung möglich sind.

Fass den Rest der Seite nicht an. Die JSON-LD-Blöcke stehen fertig in seo-briefing.md,
Abschnitt 7.3 — übernimm sie von dort, schreib sie nicht neu.

Zeig mir vor jedem Commit einen Diff. Arbeite auf einem Branch, kein Push auf main.
```

Wenn Punkt 1 bis 3 sitzen und du über das Design entschieden hast, gibst du denselben Prompt noch
einmal mit „Punkte 4 bis 10".

> **Nachtrag — Punkt 1 bis 10 sind fertig.** Der Prompt oben ist damit historisch. Wer
> die Seite heute anfasst, liest `README.md` und `seo-briefing.md` statt diesen Prompt
> noch einmal abzuschicken.

## Schritt 3 — Dauerhafte Regeln in die CLAUDE.md

> **Nachtrag — dieser Schritt ist offen.** Die Regeln unten stehen bisher nur hier, nicht
> in der `CLAUDE.md`. Zwei Gründe: der Auftrag lautete, die Briefings abzulegen, nicht die
> Projektregeln umzuschreiben — und ein Teil der Regeln überschneidet sich bereits mit
> `AGENTS.md` und dem Canon, sodass ein blindes Einfügen zwei Wahrheiten erzeugt statt
> einer. **Vor der Übernahme zu klären:** die Kennzahl „ROAS 34×" in der Liste unten steht
> in keiner Canon-Datei und ist damit nach der ersten Regel derselben Liste nicht belegt.
> Entweder im Canon ablegen oder aus der Regel streichen.

Diese Zeilen gehören in die `CLAUDE.md` im Repo-Wurzelverzeichnis, damit sie in jeder Sitzung
gelten und nicht jedes Mal neu gesagt werden müssen:

```markdown
## Inhaltsregeln für die Website

- Keine erfundene Zahl und kein erfundenes Kundenzitat. Jede Zahl auf einer Seite muss in
  docs/briefings/ belegt sein.
- Anonymisierte Fälle werden nie begründet. „Ein mittelständischer PV-Installationsbetrieb in
  DACH" genügt. Interne Gründe für die Anonymisierung erscheinen nirgends auf der Website.
- Kennzahlen: Kosten pro qualifizierter Anfrage 150 € → 22 € (über 85 % Reduktion), 1.750+
  Anfragen in 6 Monaten, Abschlussquote 15 %, ROAS 34×. Fremde Marktzahlen immer mit Quelle.
- Antwortzeit sitewide: „spätestens 2 Werktage". Keine abweichende Angabe in Footer oder
  auf /kontakt/.
- E-Mail sitewide: kontakt@hasimuener.de
- Preise netto B2B. Aufbau 14.900 € + ~50 €/Monat Hosting, Sofortkontakt-Setup 790 €,
  Anfragesystem-Analyse 690 €.

## Technikregeln

- Kein Cookie-Banner, keine einwilligungspflichtige Messung ohne Rücksprache.
- Auf der Server-Side-Tracking-Seite keine absoluten Versprechen: keine „100 % Daten",
  keine DSGVO-Garantie, kein „Tracking ohne Consent", keine Rechtsberatung.
- Animationen: nur transform und opacity. Niemals padding, width, margin oder top in
  Transitions. Jede Animation respektiert prefers-reduced-motion.
- Fokus: outline nur mit gleichwertigem Ersatz entfernen. Jedes fokussierbare Element
  behält einen sichtbaren Ring.
- Tabellen bekommen einen eigenen overflow-x-Kontext. Die Seite darf bei 360 px nie
  seitwärts scrollen.
```

## Zu den Skills

Skills liegen in Claude Code als Ordner mit einer `SKILL.md`:

- **projektweit:** `.claude/skills/<name>/SKILL.md` im Repo — gilt für alle, die im Repo arbeiten
- **nur für dich:** `~/.claude/skills/<name>/SKILL.md` — gilt auf deinem Rechner in allen Projekten

Wenn du die Emil-Kowalski-Skills als Repo oder Zip hast, legst du die Ordner an einer der beiden
Stellen ab und startest Claude Code neu. `/skills` zeigt danach, ob sie erkannt wurden.

Deine bestehenden Skills — `web-handwerk`, `ki-check`, `akquise-mail` — funktionieren in Claude Code
genauso, wenn sie unter einem dieser Pfade liegen.
