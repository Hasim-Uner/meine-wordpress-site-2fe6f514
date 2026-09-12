# Auftrag an Codex: Umbau /solar-waermepumpen-leadgenerierung/

Stand 11.09.2026 · Referenzdatei: `referenz.html` in diesem Ordner

> **Archivstand.** Dieser Auftrag ist der Originaltext vom 11.09.2026, unverändert
> übernommen. Er beschreibt einen Zustand vor der Umsetzung. Was seither entschieden,
> gebaut oder widerlegt wurde, steht in `README.md` und in den mit **Nachtrag**
> markierten Kästen. Der Fließtext wurde nicht rückwirkend geglättet — ein Auftrag,
> den man nachträglich richtigschreibt, taugt nicht mehr als Beleg dafür, was
> beauftragt war.

---

## 0 Vorab: zwei Entscheidungen von Haşim

Beide betreffen die Umsetzung direkt. Ohne sie kann Codex loslegen, aber nicht fertig werden.

> **Nachtrag 12.09.2026 — beide Entscheidungen sind gefallen.**
> **A:** neuer Standard, gestaffelt. Diese Seite zuerst, Header und Footer im selben
> Schritt. **B:** 690 € netto für die Anfragesystem-Analyse bestätigt; der Wert liegt
> als `HU_ANALYSIS_PRICE` in `blocksy-child/inc/canon/pricing-canon.php`.
> Zusätzlich entschieden: die beiden bestehenden Antwortzeit-Zusagen bleiben, nur der
> Marktcheck wird vereinheitlicht.

**A — Gilt das neue Design nur für diese Seite oder für die Domain?**
Die Referenzdatei ist eine vollständige Neugestaltung: weißer Grund, Serif (Newsreader) auch im
Fließtext, Gutachten-Raster mit Randspalte, keine Karten, Radius 3 px. Das kollidiert mit dem
bestehenden System der Domain (dunkle/creme Sektionen im Wechsel, geometrische Grotesk,
Kartenradius ~16 px, `site-header-premium.css`, `site-footer-modern.css`).

Zwei Wege:

- **Sonderfall.** Nur diese Seite bekommt das neue Kleid, Header und Footer bleiben wie sie sind.
  Schnell umzusetzen, aber die Seite wirkt wie von einer anderen Firma.
- **Neuer Standard.** Das Designsystem der Referenz wird die Basis, die übrigen Seiten ziehen
  nach. Deutlich mehr Arbeit, aber die einzige Variante, die auf Dauer stimmig ist.

*Empfehlung: neuer Standard, aber gestaffelt — diese Seite zuerst, Header/Footer im selben Schritt
angepasst, der Rest über die nächsten Wochen.* Solange das nicht entschieden ist, sollte Codex
ausschließlich Inhalt und Struktur umsetzen und das Kleid unangetastet lassen.

**B — Preis der Anfragesystem-Analyse.** Die Referenz nennt 690 € netto. Das ist ein Vorschlag,
keine Entscheidung. Ohne Bestätigung bleibt die Stelle offen.

---

## 1 Was die Referenzdatei ist — und was nicht

**Ist:** eine lauffähige, eigenständige HTML-Seite mit der vollständigen Copy, dem funktionierenden
Rechner, der Hauptgrafik als Inline-SVG, allen Zuständen und Animationen. Sie ist die
maßgebliche Quelle für Text, Reihenfolge, Zahlen und Verhalten.

**Ist nicht:** WordPress. Kein Theme, kein Template, keine Blocks, kein PHP. Sie kann nicht
hochgeladen werden, sie muss übersetzt werden.

**Die Arbeitsnotizen sind entfernt.** Eine frühere Fassung enthielt Blöcke „Redaktionsnotiz" und
„Offene Entscheidung" — die sind in dieser Datei raus. Falls irgendwo noch ein Satz auftaucht, der
über die Live-Seite spricht statt zum Kunden („heute stehen hier elf Punkte…"), ist das ein Rest und
gehört gelöscht, nicht übersetzt.

---

## 2 Zuordnung alt → neu

| Live heute | Neu | Anmerkung |
|---|---|---|
| Hero mit Marktcheck-Formular | Dokumentkopf mit Definition, Metadatenzeile, zwei Ausgängen | Formular wandert nach unten |
| 01 Status quo | **entfällt** | Inhalt steckt in „Fehlt diese Station" |
| 02 Mieten oder besitzen | **entfällt** | dito |
| — | **neu:** Marktzeile mit drei Fremdzahlen | Quellen siehe Abschnitt 4 |
| — | **neu:** Hauptgrafik „Zwei Wege, ein Interessent" | Inline-SVG, kein Bild |
| 03 Das System | **01 Die Strecke** — fünf Stationen | ersetzt Fundament-Liste und Output-Zeilen |
| — | **neu: 02 Ihr Anteil** — drei Voraussetzungen | |
| 04 Die Rechnung | **03 Die Rechnung** — Cost per Order | Rechner komplett neu, Spezifikation Abschnitt 3 |
| 06 Referenzfall | **04 Der Fall** — drei Phasen + Tabelle | von 86 auf ~330 Wörter |
| 03 + 04 + 05 verstreut | **05 Die Leiter** — vier Stufen | bündelt alle Angebote |
| — | **neu:** „Und wenn es nicht funktioniert?" | vier Ausstiegspunkte |
| 07 Wann es passt | **06 Passung** | von 11 auf 8 Punkte |
| 08 Risiko-Umkehr | **entfällt** | Inhalt in Leiter und Marktcheck-Karte |
| Hero-Formular | **07 Marktcheck** | Gate an neuer Position, Anker `#marktcheck` bleibt |
| 10 FAQ (12 Fragen) | **08 Fragen** (5 in der Referenz) | Muster in Abschnitt 5 |
| 09 Vertiefung | **09 Verweise** | nach Frage sortiert statt nach Kategorie |
| Abschluss + Routing | bleibt | |

**Anker, die erhalten bleiben müssen:** `#marktcheck`, `#ergebnisse`, `#einstieg`. Auf sie zeigen
interne Links von anderen Seiten. Neue Anker zusätzlich vergeben, alte nicht ersetzen.

---

## 3 Der Rechner — verbindliche Spezifikation

```
Weg A · Anfragen einkaufen
  Eingaben:  a1 Anfragen/Monat (Vorgabe 25)
             a2 Preis je Anfrage in € (Vorgabe 80)
             a3 Abschlussquote in % (Vorgabe 4)
  einkauf        = a1 * a2
  auftraege      = a1 * a3/100
  kostenJeAuftrag = einkauf / auftraege        → 2.000 € bei Vorgabe

Weg B · Eigene Strecke
  Eingaben:  b1 Werbebudget/Monat in € (Vorgabe 2000)
             b2 Kosten je Anfrage in € (Vorgabe 45)
             b3 Abschlussquote in % (Vorgabe 12)
  Konstanten: AUFBAU = 14900, MONATE = 24, HOSTING = 50
  kosten         = b1 + AUFBAU/MONATE + HOSTING
  anfragen       = b1 / b2
  auftraege      = anfragen * b3/100
  kostenJeAuftrag = kosten / auftraege         → 501 € bei Vorgabe
```

Beide Spalten sind absichtlich mit 2.000 € vorbelegt, damit der Vergleich am gleichen Budget hängt.
**Die Vorgabewerte nicht ändern** — sie sind mit der Hauptgrafik und mit einem belegten Marktfall
abgestimmt (siehe Abschnitt 4). Division durch null gibt „–", nicht NaN.

Formatierung über `Intl.NumberFormat('de-DE')`, Beträge ohne Nachkommastellen, Auftragszahlen mit
maximal zwei. Beide Ergebnisblöcke tragen `aria-live="polite"`.

---

## 4 Zahlen: was belegt ist und was nicht

**Eigene Zahlen** (aus dem dokumentierten Fall, anonymisiert): 150 € → 22 € je qualifizierter
Anfrage, über 85 % Reduktion, 1.750+ Anfragen in 6 Monaten, Abschlussquote 15 %, ROAS 34-fach,
drei Monate Vorbereitung.

> **Nachtrag 12.09.2026 — ROAS 34-fach ist nicht umgesetzt.**
> Alle anderen Zahlen dieser Aufzählung stehen in
> `blocksy-child/inc/canon/e3-proof-canon.php` und werden von dort gelesen. Die
> 34-fache ROAS steht in keiner Canon-Datei. Nach der Repo-Regel „jede Zahl auf einer
> Seite muss belegt sein" ist sie deshalb nirgends auf der Seite gelandet. Wer sie
> verwenden will, legt sie zuerst im Canon ab.

**Fremde Zahlen** in der Marktzeile — müssen als solche gekennzeichnet bleiben, der Hinweissatz
darunter ist Pflicht:

| Zahl | Quelle |
|---|---|
| CPL Photovoltaik 25–200 € (2026) | Anfragenfluss, CPL-Übersicht |
| ~30 € nicht exklusiv / ~150 € exklusiv vorqualifiziert | pv magazine |
| ein PV-Kontakt an 3–5 Betriebe | Anfragenfluss, A&M Beratung |
| 84.000 € → 950 Anfragen → 42 Aufträge (2.000 €/Auftrag, 4,4 %) | A&M Beratung |

**Nicht verwenden:** „12 Whitelabel-Projekte", „4 laufende Retainer", jede Projektanzahl. Die
Vorher-Abschlussquote des eigenen Falls ist eine Marktannahme, keine Messung — deshalb steht in der
Tabelle „einstellig" und nicht „1–5 %".

**Antwortzeit:** überall **„spätestens 2 Werktage"**. Der Footer sagt derzeit „24 Stunden
werktags", `/kontakt/` sagt „48 Stunden, spätestens 2 Werktage". Beides angleichen —
`inc/canon/messaging-canon.php` greift im Footer offenbar nicht durch, das ist ein eigener Bug.

> **Nachtrag 12.09.2026 — der beschriebene Bug existiert nicht.**
> Nachgeprüft in `blocksy-child/template-parts/site-footer.php:131`: der Footer liest
> `hu_response_promise()` aus `messaging-canon.php`. Der Canon greift also durch; er
> hält dort bewusst `HU_RESPONSE_HOURS = 24`. `/kontakt/` verwendet dieselbe
> 24-Stunden-Zusage. Die 48 Stunden sind kein abweichender Footer-Wert, sondern
> `HU_MARKETCHECK_REPLY_HOURS` aus `diagnose-canon.php` — das Label des Marktchecks,
> eine andere Zusage für einen anderen Vorgang. `messaging-canon.php:76` trennt beide
> im Kommentar ausdrücklich.
>
> Umgesetzt wurde deshalb nicht „alles auf 2 Werktage", sondern die Entscheidung aus
> Abschnitt 0: die zwei Zusagen bleiben getrennt, nur das Marktcheck-Label wurde
> vereinheitlicht. `hu_marketcheck_reply_label()` gibt jetzt ausschließlich
> „spätestens 2 Werktage" zurück, die Stundenangabe erscheint nicht mehr sichtbar.

---

## 5 SEO und GEO

Die vier JSON-LD-Blöcke (Service, HowTo, BreadcrumbList, WebPage mit DefinedTerm) und der
FAQPage-Block stehen fertig ausformuliert im älteren Dokument
`briefing-solar-money-page.md`, Abschnitt 7.3. Von dort übernehmen, nichts neu erfinden.

> **Nachtrag 12.09.2026 — diese Quelle gibt es nicht.**
> `briefing-solar-money-page.md` wurde nie geliefert und liegt in keiner Fassung im
> Repo. Die Blöcke wurden deshalb aus dem Canon geschrieben statt übernommen. Was
> tatsächlich ausgeliefert wird, steht in `seo-briefing.md` in diesem Ordner — die
> Datei ersetzt die hier genannte Quelle und ist ab jetzt die Adresse für diesen
> Verweis.
>
> Zwei Abweichungen vom Auftragstext, beide begründet in
> `page-solar-waermepumpen-leadgenerierung.php:542`: **BreadcrumbList und WebPage
> werden hier nicht ausgegeben.** `inc/org-schema.php` emittiert beide global unter
> exakt denselben `@id`s (`<permalink>#webpage`, `<permalink>#breadcrumb`). Ein
> zweiter Knoten unter derselben `@id` ist kein zusätzliches Signal, sondern ein
> widersprüchlicher Graph. Statt der WebPage hängen zwei eigenständige
> DefinedTerm-Knoten per `subjectOf` an der globalen Seite. Der sichtbare Breadcrumb
> im Dokument bleibt davon unberührt.

Dazu unverändert gültig:

- `id` an jede H2 und H3
- Vergleich als echte `<table>` mit `<th scope>` — in der Referenzdatei bereits so gebaut
- Breadcrumb sichtbar plus Schema
- eigenes `og:image` 1200×630 statt des Hochformat-Porträts
- Alt-Text am Porträt
- `llms.txt` im Root

**Regel für den FAQPage-Block:** Der `text` im Schema muss wortgleich dem sichtbaren Antworttext
entsprechen. Abweichung ist ein Rich-Result-Verstoß.

**Neuer Title und Description:**

```
Photovoltaik-Anfragen selbst generieren statt Leads kaufen

Eigenes Anfragesystem für Photovoltaik und Wärmepumpe: Anfragen auf Ihrer Domain,
vorqualifiziert, serverseitig gemessen. Aufbau ab 14.900 €, Einstieg ab 790 €.
```

---

## 6 Technische Details aus der Referenz, die übernommen gehören

- **Farbtokens** in `:root`, die Tafeln definieren ihre eigenen Tokens lokal um. Dadurch ist eine
  dunkle Fläche eine einzige CSS-Klasse (`.tafel`) und kein zweiter Komponentensatz.
- **Kein Dark Mode.** `color-scheme: light`, keine `prefers-color-scheme`-Umschaltung. Die
  vier dunklen Tafeln sind Absicht und bleiben in jeder Umgebung dunkel.
- **Abstände** ausschließlich aus der Skala `--s0` bis `--s6`.
- **Bewegung:** genau zwei. Die Hauptgrafik baut sich beim ersten Sichtkontakt einmal auf
  (Wischblende, nur `transform`), die CPL-Kurve zeichnet sich über `stroke-dashoffset`. Beide
  werden nur scharfgeschaltet, wenn das Element beim Laden unterhalb des Sichtfelds liegt — ohne
  JavaScript und bei `prefers-reduced-motion` steht alles sofort vollständig da. **Keine weiteren
  Entrance-Animationen ergänzen.**
- **Niemals Layout animieren.** Hover-Zustände laufen über `transform`, `color` und
  `background-color`. Kein `padding`, `width`, `margin` in Transitions.
- **Fokus:** jedes fokussierbare Element behält einen sichtbaren Ring. `outline: none` nur mit
  gleichwertigem Ersatz — in der Referenz gibt es keinen einzigen Fall davon.
- **Tabellen** bekommen einen eigenen `overflow-x`-Kontext, damit die Seite bei 360 px nie
  seitwärts läuft.

---

## 7 Reihenfolge

**Ohne Designentscheidung sofort machbar:**

1. JSON-LD (fünf Blöcke), Heading-IDs, Breadcrumb, `og:image`, Alt-Text, `llms.txt`
2. Antwortzeit sitewide vereinheitlichen, Footer-Bug in `messaging-canon.php`
3. Title und Description

**Nach Entscheidung A:**

4. Fünf Stationen ersetzen „03 Das System"; „01" und „02" entfernen
5. Marktzeile und Hauptgrafik einsetzen
6. Rechner nach Spezifikation neu
7. Fall auf drei Phasen ausbauen
8. Leiter (braucht Entscheidung B)
9. Ausstiegs-Block, Marktcheck an neue Position, Passung kürzen
10. FAQ umschreiben, Verweise neu sortieren

**Danach, außerhalb dieser Seite:**

11. Kontextlinks aus den zehn Vertiefungsseiten auf die Money-Page, im Fließtext, mit
    variierendem beschreibendem Ankertext

> **Nachtrag 12.09.2026 — Punkt 1 bis 10 sind umgesetzt und live.**
> Ausgeliefert über die Pull Requests #331 bis #333; der Deploy lief am 12.09. um
> 01:17 Uhr, Smoke-Test grün. **Punkt 11 ist offen** und liegt außerhalb dieser Seite.
>
> Drei Dinge kamen während der Umsetzung dazu und stehen so in keinem Punkt oben:
> die Kapitelnavigation als feste Randspalte, der Umbau des Marktchecks in eine
> zweistufige kompakte Strecke, und die Rücknahme der Schriftentscheidung — die Seite
> läuft in der Hausschrift Satoshi/Figtree, nicht in Newsreader. IBM Plex Mono bleibt
> für Zahlen und Marginalien.

---

## 8 Prüfliste vor dem Merge

- [ ] Keine Arbeitsnotizen, keine Sätze über die Redaktion auf der Seite
- [ ] `#marktcheck`, `#ergebnisse`, `#einstieg` funktionieren weiterhin
- [ ] Rechner liefert bei Vorgabewerten 2.000 € und 501 €
- [ ] Kein horizontaler Überlauf bei 360, 390, 768, 1024, 1440 px
- [ ] Tastaturdurchlauf ohne Falle, Fokus überall sichtbar
- [ ] Mit `prefers-reduced-motion` ist alles sofort und vollständig sichtbar
- [ ] Antwortzeit auf dieser Seite, im Footer und auf `/kontakt/` identisch
- [ ] Rich-Results-Test ohne Fehler für Service, FAQPage, HowTo, BreadcrumbList
- [ ] Jede Zahl auf der Seite steht so in Abschnitt 4 dieses Dokuments

> **Nachtrag 12.09.2026 — Stand der Prüfliste.**
> Die Kästchen bleiben leer, weil die Liste ein Dokument vom 11.09. ist und kein
> laufendes Protokoll. Geprüft wurde gegen eine PHP-Render-Vorrichtung plus
> Playwright, weil im Arbeitscontainer keine WordPress-Laufzeit existiert:
>
> - Arbeitsnotizen, Anker, Rechnerwerte (2.000 € / 501 €), Überlauf bei 360 bis
>   1440 px, Tastaturdurchlauf, `prefers-reduced-motion` — alle geprüft und in
>   Ordnung.
> - **Antwortzeit weicht bewusst ab.** Nicht identisch, sondern zwei getrennte
>   Zusagen — siehe den Nachtrag in Abschnitt 4.
> - **BreadcrumbList wird von dieser Seite nicht ausgegeben**, sondern global von
>   `inc/org-schema.php`. Der Rich-Results-Test prüft ihn dort. Siehe den Nachtrag in
>   Abschnitt 5.
> - Die 34-fache ROAS aus Abschnitt 4 steht nicht auf der Seite. Alle übrigen Zahlen
>   stammen aus dem Canon.
>
> Ein Punkt fehlt der Liste, und er hat live wehgetan: **das Marktcheck-Formular in
> seinem gemounteten Zustand.** Geprüft wurde nur die servergerenderte Fassung; das
> Formular baut sich per JavaScript auf und stand dadurch eine Zeit lang ungestylt
> auf der Seite. Wer diese Liste wiederverwendet, nimmt den Punkt mit auf.
