# Briefings /solar-waermepumpen-leadgenerierung/

Die Unterlagen zum Umbau der Money Page, abgelegt am 12.09.2026, damit keine Sitzung sie
neu eingefügt bekommen muss.

## Was hier wovon die Quelle ist

| Datei | Rolle | Stand |
|---|---|---|
| `referenz.html` | **maßgeblich für Copy, Reihenfolge, Zahlen, Verhalten.** Eigenständiges HTML, kein WordPress. Wird nicht deployt. | 12.09.2026, Fassung nach Copy-Durchgang 2 |
| `auftrag.md` | Umbauauftrag: Zuordnung alt→neu, Rechnerspezifikation, Zahlenherkunft, Prüfliste | 11.09.2026, archiviert |
| `copy-durchgang.md` | Begründung der Copy-Entscheidungen — warum Sätze gestrichen, gedreht oder ersetzt wurden | 12.09.2026, archiviert |
| `seo-briefing.md` | **was tatsächlich an SEO-Signalen ausgeliefert wird**, mit Datei und Zeile | 12.09.2026, laufend zu pflegen |
| `start-prompt.md` | Einrichtungsprotokoll: wie die Dateien hierhergekommen sind, welche Regeln noch offen sind | 12.09.2026, archiviert |

Die vier archivierten Dateien sind **Originaltexte**. Sie wurden nicht rückwirkend
geglättet — ein Auftrag, den man nachträglich richtigschreibt, taugt nicht mehr als Beleg
dafür, was beauftragt war. Wo eine Aussage überholt oder widerlegt ist, steht ein
**Nachtrag**-Kasten daneben. Wer die Dateien liest, liest die Kästen mit.

`seo-briefing.md` ist die Ausnahme: neu geschrieben, kein Archiv, und die einzige Datei
hier, die nach einer Änderung an der Seite nachgezogen werden muss.

## Drei Dinge, die beim Lesen leicht schiefgehen

**Die Referenz enthält drei Stellen, die nicht übernommen werden dürfen.** Google-Fonts
per CDN, einen ungültigen Selektor `.stufe@media (…)`, der den ganzen Mobilblock
verschluckt, und ein `--matt`, das auf Weiß die WCAG-AA-Schwelle reißt. Alle drei stehen
im Kommentarkopf der Datei, mit Begründung.

**Die umgesetzte Seite weicht an drei Stellen bewusst von der Referenz ab.** Sie läuft in
der Hausschrift Satoshi/Figtree statt in Newsreader; der Marktcheck ist eine zweistufige
kompakte Strecke statt des Referenzformulars; die Kapitelnavigation als feste Randspalte
kam dazu. IBM Plex Mono für Zahlen und Marginalien ist geblieben.

**Keine Zahl in diesen Dateien ist die Quelle.** Preise, Kennzahlen und Zusagen stehen in
`blocksy-child/inc/canon/`. Die Briefings zitieren sie, sie definieren sie nicht. Bei
einem Widerspruch gewinnt der Canon.

## Stand der Umsetzung

`auftrag.md`, Abschnitt 7, Punkt 1 bis 10 sind umgesetzt und live, ausgeliefert über die
Pull Requests #331 bis #333.

Offen:

- **Punkt 11** — Kontextlinks aus den zehn Vertiefungsseiten auf die Money Page, im
  Fließtext, mit variierendem beschreibendem Ankertext. Liegt außerhalb dieser Seite.
- **Schritt 3 aus `start-prompt.md`** — die Inhalts- und Technikregeln stehen bisher nur
  dort, nicht in der `CLAUDE.md`. Vor einer Übernahme ist zu klären, was sich mit
  `AGENTS.md` und dem Canon überschneidet, und ob „ROAS 34×" in den Canon soll oder aus
  der Regel gestrichen wird — die Kennzahl ist derzeit nirgends belegt und steht deshalb
  auch nicht auf der Seite.
- **Zwei Copy-Lücken** aus `copy-durchgang.md`, Abschnitt 8: ein Satz über einen
  Fehlversuch im dokumentierten Fall, und echte Kundensprache statt Marktzahlen. Beide
  brauchen Material aus Gesprächen und Marktcheck-Freitext, nicht aus Redaktion.
