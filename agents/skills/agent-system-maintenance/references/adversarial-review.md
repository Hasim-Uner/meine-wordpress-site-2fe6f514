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
  müssen existieren und im Repo bleiben; `#anker` wählt beim Prompt die Zeilen mit
  dem Begriff (±2 Zeilen), sonst gilt der Dateianfang.
- Enthält `claim` oder `action` eine Ziffer, braucht die Maßnahme mindestens eine
  vorhandene lokale Belegdatei. Eine URL allein reicht nicht (Status `UNBELEGT`).

## Schema `critique.v1`

```json
{"schema":"critique.v1","reviews":[{"id":"M1","verdict":"halten|nachbessern|verwerfen",
 "attack":"stärkstes Gegenargument","failure_mode":"wie es real scheitert",
 "evidence_gap":"fehlender Beleg oder leerer String",
 "falsification":"beobachtbares Ergebnis plus Zeitfenster, das widerlegt","confidence":0.0}]}
```

Jede Maßnahme-ID genau einmal, keine unbekannten IDs. `attack` und `falsification`
sind für jedes Review Pflicht (mindestens 20 Zeichen), auch bei `halten`.
`confidence` liegt in [0,1].

## Subcommands

| Befehl | Zweck |
| --- | --- |
| `check MEASURES` | Pre-Gate ohne Netz. Je Maßnahme `OK`, `UNBELEGT` oder `FEHLER` mit Grund. |
| `prompt MEASURES` | Vollständigen Kritiker-Prompt (System und User) zum manuellen Einfügen in ChatGPT ausgeben. Optionen `--max-excerpt-bytes` (1500), `--max-total-bytes` (24000), `--allow-memory`. |
| `run MEASURES --out CRITIQUE.json [--md-out REPORT.md]` | Ein API-Aufruf an OpenAI, danach `verdict`. Env `KERN_MODEL` (Pflicht, kein Default) und `OPENAI_API_KEY`. |
| `verdict MEASURES CRITIQUE [--md-out FILE]` | Post-Gate. Markdown-Report mit Tabelle und To-dos. |

Belegauszüge stehen im Prompt in gekennzeichneten Blöcken und gelten als Daten,
nie als Anweisung. Das Gesamtlimit wird deterministisch erzwungen; Auszüge, die
nicht mehr passen, entfallen mit sichtbarem Marker.

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

Code schlägt Modell: Besteht eine Maßnahme den Pre-Gate nicht (`UNBELEGT` oder
`FEHLER`), meldet `verdict` sie als `verwerfen`, egal was der Kritiker sagt.

## Ablauf

1. Der Generator schreibt `.ai/memory/measures-<slug>.json`.
2. `check` ausführen und rote Maßnahmen beheben, bevor etwas den Rechner verlässt.
3. Automatisch: `run` mit `KERN_MODEL` und `OPENAI_API_KEY`.
   Manuell: `prompt` ausgeben, in ChatGPT einfügen, die JSON-Antwort als Datei
   speichern, `verdict` ausführen. Markdown-Codezäune um die Antwort werden toleriert.
4. Jeden Befund `nachbessern` oder `verwerfen` im Ergebnis beheben oder mit einem
   Satz widerlegen. Der Satz steht im Deliverable.

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
