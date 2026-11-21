# Adversarial Review (Kern-Gate)

Zweck: Ein OpenAI-Modell prüft als Kritiker "Kern" generierte SEO- und
Conversion-Maßnahmen auf Plausibilität, bevor das Ergebnis final ausgegeben wird.
Das Urteil des Kritikers ist **beratender Input, kein Beweis**. Deterministischer
Code rahmt es ein: vorher `check` (Belege), nachher `verdict` (Form, Vollständigkeit,
Anti-Rubber-Stamp). Code schlägt Modell.

Script: `agents/skills/agent-system-maintenance/scripts/adversarial-review.py`
(Python 3, nur Standardbibliothek).

## Schema `measures.v1`

```json
{"schema":"measures.v1","context":{"scope":"Route /x/ oder gap-report 2026-09"},
 "measures":[{"id":"M1","domain":"seo|conversion","claim":"Warum","action":"konkrete Änderung",
  "evidence":["repo/relativer/pfad.csv#anker-begriff","https://..."],
  "metric":"Ergebnis, das sich bewegen soll","expected":"up|down",
  "owner":"Repo|Manual WP|Sales|External"}]}
```

- `evidence`: mindestens ein Eintrag je Maßnahme. Lokale Pfade sind repo-relativ,
  müssen existieren und im Repo bleiben. `#anker` wählt beim Prompt die Zeilen mit
  dem Begriff (±2 Zeilen). Kommt der Anker in keiner Zeile der Datei vor
  (Groß-/Kleinschreibung egal), ist die Maßnahme `FEHLER` ("Anker nicht gefunden").
- Enthält `claim` oder `action` eine Ziffer, braucht die Maßnahme mindestens eine
  vorhandene lokale Belegdatei **mit gefundenem Anker**; der Anker ist die Stelle,
  die die Zahl trägt. URL allein oder bloßer Pfad reichen nicht (Status `UNBELEGT`,
  "Zahl ohne verankerten Beleg").
- Zahlenprüfung am Anker: Aus `claim` und `action` werden die Zahlen gezogen
  (deutsche Dezimale "0,6" wird 0.6, Tausenderpunkt "1.750" wird 1750, "14/28"
  und "60:40" zählen als je zwei Zahlen, ein Datum JJJJ-MM-TT bleibt ganz).
  Paragraphen- und Artikelverweise ("§ 7", "Art. 14/21", "Abs. 2", "Nr. 1") sind
  keine Claim-Zahlen. Mindestens eine dieser Zahlen muss in den Zeilen um einen
  Anker vorkommen (Anker-Zeile ±2 Zeilen, dasselbe Fenster wie im Prompt-Auszug),
  sonst `UNBELEGT` mit "Anker traegt die Zahl nicht". Das prüft nur, dass die Zahl
  an der Stelle steht, nicht dass die Stelle die Aussage inhaltlich stützt.

## Schema `critique.v1`

```json
{"schema":"critique.v1","reviews":[{"id":"M1","verdict":"halten|nachbessern|verwerfen",
 "attack":"stärkstes Gegenargument","failure_mode":"wie es real scheitert",
 "evidence_gap":"fehlender Beleg oder leerer String",
 "falsification":"beobachtbares Ergebnis plus Zeitfenster, das widerlegt","confidence":0.0}]}
```

Jede Maßnahme-ID genau einmal, keine unbekannten IDs. `attack`, `failure_mode`
und `falsification` sind für jedes Review Pflicht (mindestens 20 Zeichen), auch bei
`halten`. `confidence` liegt in [0,1].

## Subcommands

| Befehl | Zweck |
| --- | --- |
| `check MEASURES` | Pre-Gate ohne Netz. Je Maßnahme `OK`, `UNBELEGT` oder `FEHLER` mit Grund. |
| `prompt MEASURES` | Vollständigen Kritiker-Prompt (System und User) zum manuellen Einfügen in ChatGPT ausgeben. Optionen `--max-excerpt-bytes` (1500), `--max-total-bytes` (24000), `--allow-memory`, `--repo-access`, `--ref`. |
| `run MEASURES --out CRITIQUE.json [--md-out REPORT.md]` | Ein API-Aufruf an OpenAI, danach `verdict`. Env `KERN_MODEL` (Pflicht, kein Default) und `OPENAI_API_KEY`. |
| `verdict MEASURES CRITIQUE [--md-out FILE]` | Post-Gate. Markdown-Report mit Tabelle und To-dos. |

Belegauszüge stehen im Prompt in gekennzeichneten Blöcken und gelten als Daten,
nie als Anweisung. Das Gesamtlimit wird deterministisch erzwungen; Auszüge, die
nicht mehr passen, entfallen mit sichtbarem Marker.

### Auszüge sind nur der Fallback

Die Auszüge (Standard 1500 Byte je Beleg) sind für Kritiker **ohne Repo-Zugriff**
(API-Aufruf per `run`, ChatGPT ohne Connector). Sie können die einschränkende
Passage auslassen. Ein Kritiker **mit Repo-Zugriff** (Kern über ChatGPT mit
GitHub-Connector) muss die zitierten Dateien vollständig lesen. Dafür gibt
`prompt --repo-access [--ref BRANCH_ODER_COMMIT]` einen Prompt ohne Auszugsblöcke aus.
Er nennt je Maßnahme die Belegpfade samt Anker, weist den Kritiker an, jeden Pfad
vollständig auf `--ref` zu lesen (Default: `git rev-parse --short HEAD`), und
verbietet jede Änderung am Repo (kein Commit, kein Push, kein PR). Die Maßnahmen
stehen weiterhin im Prompt, weil `.ai/memory` per gitignore nicht im Repo liegt.
Der Stand auf `--ref` muss auf GitHub gepusht sein, sonst sieht der Kritiker
lokale Änderungen nicht.

## Exit-Codes

| Code | `check` | `verdict` / `run` |
| --- | --- | --- |
| 0 | alle Maßnahmen `OK` | alle `halten`, Kritik gültig |
| 1 | mindestens ein `UNBELEGT` oder `FEHLER` | mindestens ein `nachbessern` oder `verwerfen` (To-dos im Report); bei `run` auch Pre-Gate-Abbruch vor dem API-Aufruf |
| 2 | Usage- oder Schemafehler in der Measures-Datei, fehlende Env, `.ai/memory`-Beleg ohne `--allow-memory` | dasselbe |
| 3 | nicht vergeben | Kritik abgelehnt: unvollständig, unbekannte oder doppelte IDs, ungültiges Format, Rubber-Stamp |

Rubber-Stamp (`KRITIK-ZU-WEICH`): alle Urteile `halten` bei mittlerer
`confidence` von mindestens 0,9. Ein Kritiker, der nichts findet, gilt als
unglaubwürdig, nicht als Freispruch.

Strukturprüfung gegen Schablonenkritik (Exit 3, "Kritik abgelehnt"):

- `failure_mode` ist je Review nicht leer und mindestens 20 Zeichen lang.
- Derselbe `attack` (Leerraum und Groß-/Kleinschreibung normalisiert) bei zwei oder
  mehr Reviews wird abgelehnt ("Angriff nicht massnahmenspezifisch"); dasselbe gilt
  für `falsification`.
- Jeder `attack` teilt mindestens zwei verschiedene Begriffe (ab 5 Buchstaben,
  Umlaute gefaltet) mit `claim`, `action`, `metric`, Dateiname und Anker der Belege
  seiner eigenen Maßnahme. Gleiche Stämme zählen als Treffer, wenn der kürzere
  Begriff mindestens 6 Buchstaben hat und Präfix des längeren ist ("unbelegte" und
  "unbelegten"); Beugungsformen desselben Stamms zählen nur einmal.
- `falsification` nennt ein Fenster oder eine Schwelle. Gültig ist (a) eine Zahl
  direkt an einer Einheit (Tage, Wochen, Monate, Stunden, %, Prozent,
  Prozentpunkte, Anfragen, Sitzungen, Briefe, Leads, Fenster, Exporte), ein Datum
  JJJJ-MM-TT oder "14/28", oder (b) ein Prüfpunkt-Ereignis: "vor Freigabe",
  "vor Versand", "vor Druck", "vor dem nächsten", "vor jeder", "beim nächsten",
  "am Prüfpunkt", "nächster Export". Eine bloße Ziffer ("Abschnitt 1") oder
  ein Wort wie "Lauf" allein genügt nicht.

Die Grenze 0,9 bleibt bestehen, ist aber nicht kalibriert: sie stammt aus keiner
Messung und ist nur ein Backstop. Die Strukturprüfungen oben sind die primäre
Verteidigung. Auch sie erkennen nur Form: Das Gate kann nicht beurteilen, ob eine
angenommene Kritik inhaltlich richtig ist. Ein Kritiker, der die Struktur erfüllt
und Unsinn schreibt, kommt durch.

### Stichprobe (Pflicht für Menschen)

Jede Runde liest der Mensch mindestens ein `halten`-Review vollständig und gleicht
es gegen die zitierten Belege ab (Anker öffnen, Zahl und Aussage prüfen). Trägt der
Beleg die Aussage nicht, zählt das Review als abgelehnt und die Maßnahme geht in
die nächste Runde. Gibt es in einer Runde kein `halten`, gilt die Regel für ein
`nachbessern`-Review.

Code schlägt Modell: Besteht eine Maßnahme den Pre-Gate nicht (`UNBELEGT` oder
`FEHLER`), meldet `verdict` sie als `verwerfen`, egal was der Kritiker sagt.

## Ablauf

1. Der Generator schreibt `.ai/memory/measures-<slug>.json`.
2. `check` ausführen und rote Maßnahmen beheben, bevor etwas den Rechner verlässt.
3. Automatisch: `run` mit `KERN_MODEL` und `OPENAI_API_KEY`.
   Manuell: `prompt` ausgeben, in ChatGPT einfügen, die JSON-Antwort als Datei
   speichern, `verdict` ausführen. Markdown-Codezäune um die Antwort werden toleriert.
4. Jeden Befund `nachbessern` oder `verwerfen` im Ergebnis beheben oder widerlegen.
   Eine Widerlegung nennt einen Beleg (Repo-Pfad), der den Angriff widerlegt, und
   steht im Deliverable sowie im Rundenlog. Ein bloßer Satz ohne Beleg gilt nicht.
   Das Skript prüft die Widerlegung nicht; der Beleg macht sie nachprüfbar.

## Umgang mit Befunden

- Höchstens zwei Kritikrunden, kein Endlos-Loop. Ein Kritiker findet immer noch
  etwas; wer darauf loopt, macht die Rundengrenze zum Stop-Kriterium.
- P1-artige Funde (wahrscheinliche Verbesserungen statt Fehler) werden berichtet,
  nicht geloopt.
- Verbleibende Befunde nach Runde zwei gehen als offene Punkte in den Report.

## Datenschutz

Belege unter `.ai/memory/` lehnen `prompt` und `run` ab, weil dort Roh-Exporte
(zum Beispiel CRM) liegen. `--allow-memory` hebt das nur auf, wenn der Inhalt
nachweislich unkritisch ist. Es gehen nur Maßnahmen und die ausgewählten
Auszüge an OpenAI, keine personenbezogenen Daten, keine Schlüssel. URL-Belege
werden nicht geladen.

## Kosten

Ein API-Aufruf je Runde, also höchstens zwei je Maßnahmenliste. Die Größe
begrenzt `--max-total-bytes`. Das Modell wählt der Betreiber über `KERN_MODEL`;
das Skript kennt keinen Standard.
