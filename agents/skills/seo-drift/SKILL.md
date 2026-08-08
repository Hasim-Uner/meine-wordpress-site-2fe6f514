---
name: seo-drift
description: SEO-Regressionen zwischen zwei Perioden finden und gegen Repo-Änderungen halten — welche Queries und Seiten haben Position oder Impressionen verloren, und hat sich gleichzeitig etwas am Template geändert. Use für "warum verliert Seite X Sichtbarkeit", "hat der letzte Deploy SEO gekostet", "was ist seit dem letzten Export schlechter geworden", oder als Kontrolle nach größeren Template-, Copy- und CRO-Änderungen. Nicht für die Frage, woran als Nächstes gearbeitet wird (seo-agent, gap-report.sh).
---

# SEO Drift

Findet, was schlechter geworden ist. `gap-report.sh` beantwortet „woran arbeiten
wir als Nächstes?" aus **einem** Snapshot — dieser Skill beantwortet „was haben
wir verloren?" aus dem Periodenvergleich, den der Export bereits mitbringt.

## Abgrenzung

- `seo-agent` / `gap-report.sh`: Lücken und Chancen, ein Snapshot, nach vorn.
- Dieser Skill: Verluste, Periodenvergleich, nach hinten.
- `seo-live-qa`: ob eine URL technisch korrekt ausgeliefert wird. Ein Drift-Fund
  kann dort landen, ist aber nicht dasselbe.

## First Command

```bash
bash agents/skills/seo-drift/scripts/drift-report.sh
```

Nutzt den neuesten 28-Tage-Snapshot. `--7d` für das kurze Fenster, `--md` für
Markdown, `--all` auch für unbelegte Positionsverluste, `--help` für den Rest.

**Exit 1, sobald ein `VERSCHWUNDEN` oder `ABSTURZ` gefunden wird.** Damit ist
der Report als Gate nach einem Deploy nutzbar. `POSITION` allein färbt nichts
rot.

## Die vier Befundarten

Der Unterschied ist der eigentliche Wert des Reports — die Rohdaten geben ihn
nicht her.

| Art | Bedeutung | Reaktion |
| --- | --- | --- |
| `VERSCHWUNDEN` | Query×URL existierte vorher, aktuell aber nicht mehr | Zuerst prüfen. Das ist ein vollständiger Sichtbarkeitsverlust. |
| `ABSTURZ` | Position **und** Impressionen fallen | Zuerst ansehen. Beide Signale zeigen in dieselbe Richtung. |
| `SICHTBARKEIT` | Impressionen brechen ein, Position bleibt | Kein Ranking-Verlust. Eher SERP-Feature, Query-Mix oder Saison. |
| `POSITION` | Position fällt, Impressionen nicht | Unbelegt. Prüfen, nicht handeln — eine GSC-Position ist ein Periodendurchschnitt. |

## Was der Report entscheidet und was nicht

- **Seiten-Cluster vor Einzel-Query.** Eine gefallene Query ist Rauschen.
  Mehrere Queries derselben URL, die gemeinsam fallen, sind ein Seiten-Ereignis.
  Cluster entstehen nur aus `VERSCHWUNDEN`, `ABSTURZ` und `SICHTBARKEIT` — wo
  die Impressionen nicht mitgehen, ist der Verlust nicht belegt.
- **Zeitliche Nähe ist keine Ursache.** Der Report stellt fest, wann das
  Template zuletzt geändert wurde, und ordnet die Änderung dem Messfenster zu:
  davor, darin, oder danach. Eine Änderung **nach** dem Fenster ist als Ursache
  ausgeschlossen — ihre Wirkung zeigt erst der nächste Export.
- **Wettbewerb, Google-Updates und Saison kann das Skript nicht trennen.** Es
  liefert Kandidaten, keine Diagnose.

## Hard Rules

- Keine API, keine geschätzten Werte. Nur was in `seo-research/<periode>/data/`
  liegt. Fehlt ein Zeitraum, bricht das Skript ab, statt auszuweichen.
- Snapshot-Wahl über `range_days` + `current_end`, nie über den Dateinamen —
  dieselbe Regel wie in `gap-report.sh`.
- Die Zuordnung Route → Template gehört `review-route.sh` samt Alias-Tabelle für
  Schatten-Wrapper. Hier wird sie gelesen, nicht kopiert.
- Ein Cluster ist noch keine Ursache. Vor dem Fix die Route mit
  `route-conversion-review` prüfen.

## Neue Periode

Der Report braucht nichts, was `gap-report.sh` nicht auch braucht: einen
SEO-Cockpit-Export unter `seo-research/<JJJJ-MM>/data/gsc/`. Die Spalten
`previous_*`, `delta_*`, `row_scope` und `period_presence` bringt der Export
bereits mit. Alte Exporte bleiben lesbar, können vollständig verschwundene
Query×URL-Paare aber noch nicht enthalten.

## Deliver

- Seiten-Cluster zuerst, mit verlorenen Impressionen und mittlerem Positionsverlust
- Je Cluster die Repo-Korrelation mit Lage zum Messfenster
- Einzelverluste nach Art getrennt
- Trennung `Repo` und `Manual WP`
- Ausdrücklich: welche Verluste **nicht** erklärt sind. Das ist der ehrliche
  Teil des Reports.
