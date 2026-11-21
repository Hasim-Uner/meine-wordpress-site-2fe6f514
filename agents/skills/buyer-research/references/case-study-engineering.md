# Case-Study Engineering

Aus Rohdaten (CRM-Export, Tracking-Export, Kanonwerte, Rechnung) wird ein
Fallbeleg, in dem jede Zahl verteidigbar ist. Dieses Dokument ergänzt den
Evidence Standard: dort geht es um Kundensprache, hier um Kennzahlen. Beide
Belegarten bleiben getrennt. Sprachbelege sagen, wie Käufer reden; Zahlenbelege
sagen, was passiert ist. Ein Zitat beweist keine Kennzahl, eine Kennzahl ersetzt
kein Zitat.

Grundsätze: Ein Claim steht nur mit Beleg. Zu jeder Zahl gehören Nenner,
Zeitfenster und Definition. Ein Fall ist ein Fall, keine Statistik.

## Ledger-Schema

Arbeitskopie als UTF-8-Semikolon-CSV unter `.ai/memory/case-<slug>-YYYY-MM-DD.csv`
(gitignored). Exakter Header:

```csv
claim_id;metric;value;unit;numerator;denominator;denominator_label;window_start;window_end;baseline_value;baseline_window_start;baseline_window_end;source_kind;source_locator;definition;attribution_note;public
```

| Feld | Bedeutung |
| --- | --- |
| `claim_id` | stabile eindeutige ID, z. B. `CL-001` |
| `metric` | Kennzahlname; bei `canon` möglichst der Canon-Schlüssel (siehe `hu_e3_metric()` in `blocksy-child/inc/canon/e3-proof-canon.php`, z. B. `cpl_after`, `lead_count`) |
| `value`, `unit` | Zahl wie belegt und Einheit (`%`, `EUR`, `count`, `days` …) |
| `numerator`, `denominator`, `denominator_label` | Rechenbasis; bei `%` Pflicht. Das Label benennt den Nenner in Worten (z. B. „vorqualifizierte CRM-Leads“) |
| `window_start`, `window_end` | Messfenster, ISO `YYYY-MM-DD` |
| `baseline_*` | optionaler Vorher-Wert mit eigenem Fenster; alle drei Felder oder keines |
| `source_kind` | `canon`, `crm_export`, `tracking_export`, `invoice`, `client_statement` |
| `source_locator` | `canon`: Schlüssel- oder Getter-Name, der im Kanon vorkommt. Sonst anonymisierte Export-/Beleg-ID, nie E-Mail oder Telefonnummer |
| `definition` | was genau gezählt wurde und was nicht |
| `attribution_note` | was die Zahl nicht beweist (Anteil von Vertrieb, Saisonalität, Mehrfachmaßnahmen …) |
| `public` | `true` nur für Zahlen, die öffentlich erscheinen sollen |

## Regeln und Begründung

Das Skript `scripts/case-proof-check.py` erzwingt sie. ERROR bricht ab (Exit 1), WARN nicht.

| Regel | Stufe | Grund |
| --- | --- | --- |
| `source_kind=canon` braucht einen Schlüssel, der literal im Kanon-Verzeichnis steht | ERROR | Der Kanon ist die einzige Quelle öffentlicher Zahlen (BRAND_AND_COPY, „Fakten stehen nie als Text“); ein erfundener Schlüssel wäre ein Beleg ohne Quelle |
| Nicht-Canon-Locator ist anonym: kein `@`, keine Telefonnummer | ERROR | Rohdaten und Personenbezug bleiben aus dem Ledger |
| Einheit `%`: Zähler, Nenner und Nenner-Label Pflicht; `value` weicht höchstens 0,5 Punkte von Zähler/Nenner × 100 ab; Wert 0 bis 100 | ERROR | Eine Quote ohne benannten Nenner lässt sich nicht verteidigen. Der Referenzfall zeigt es: Lead-Quote und Abschlussquote haben verschiedene Nenner (Besucher, vorqualifizierte CRM-Leads), siehe `hu_e3_summary( 'definitions' )` |
| Datumsfelder ISO; Fensterbeginn nicht nach Fensterende | ERROR | Ohne Zeitraum kein Vergleich |
| Baseline-Fenster liegt vollständig vor dem Ergebnisfenster | ERROR | Sonst vermischt der Vergleich Vorher und Nachher |
| Baseline- und Ergebnisfenster unterscheiden sich um mehr als 10 % in der Länge | WARN | Ungleich lange Fenster sind vergleichbar nur mit Kennzeichnung. Nie über unterschiedlich definierte Fenster mitteln (BRAND_AND_COPY, „Referenzfall“: Projektmonate schließen die Vorbereitung ein, Kampagnenmonate nicht) |
| Nenner kleiner 30 | WARN | Kleine Basis: als Einzelfall formulieren, keine Quote verallgemeinern; Pilot-n und Zeitraum immer nennen |
| `public=true` braucht `definition`, `attribution_note` und `source_kind=canon` | ERROR | Nicht-Canon-Zahl öffentlich: erst in den Kanon, dann öffentlich |
| Metrik `cpo` oder `cost_per_order`, deren Definition CPL und Quote zusammen nennt | ERROR | BRAND_AND_COPY verbietet eine Gesamt-CPO aus CPL × CRM-Quote. Die Regel ist bewusst schmal: sie prüft nur Metrikname und Definitionstext, keine Rechnung |
| doppelte `claim_id` | ERROR | Eindeutige Zuordnung im Brief |

Zusätzlich gilt aus den Hausregeln, auch ohne Skriptprüfung: Der Rechner-
Default und Marktannahmen sind keine Messung. Eine Annahme (etwa die
Vorher-Quote gekaufter Portal-Leads) wird als Annahme gekennzeichnet. Der
erreichte CPL ist weder Durchschnitt aller Monate noch alleinige Wirkung einer
einzelnen Maßnahme.

## Sprachbeleg und Zahlenbeleg trennen

Aus „Gather Proof Before Copy“: Sprachbelege (Evidence Standard, `direct_voc`)
und Claim-Belege (dieses Ledger) werden getrennt erhoben und getrennt geführt.
Das Skript erzeugt deshalb nie ein Zitat; der Brief verweist auf den „Promotion
gate“ des Evidence Standards.

## Case-Brief

`case-proof-check.py LEDGER --brief` gibt bei fehlerfreiem Ledger ein Skelett aus:

1. **Ausgangslage** — 2–3 Sätze, aus Evidenz geschrieben (Problem, Segment, Zeitraum). Platzhalter.
2. **Mechanismus** — was konkret getan wurde, nur belegte Schritte. Platzhalter.
3. **Ergebnis** — höchstens 3 öffentlich zulässige Claims mit Wert, Einheit, Zeitraum, Nenner-Label und Definition. Nachgelagerte Ergebnisse (Abschluss, Umsatz, Auftrag) kommen vor vorgelagerten (Anfragen, CPL). Die Reihenfolge folgt einer Namensheuristik über den Metriknamen und bewertet nichts.
4. **Grenzen** — alle `attribution_note`-Einträge und alle WARN-Zeilen.
5. **Zitat** — Platzhalter; nur aus freigegebenen `direct_voc`-Einträgen (Evidence Standard, „Promotion gate“), sonst weglassen.

Das Skript übernimmt Werte unverändert aus dem Ledger und rechnet nichts hinzu.
Fehlt ein öffentlich zulässiger Claim, nennt der Brief keine Kennzahl.

## Ablauf

1. Roh-Export (CRM, Tracking, Rechnung) lokal sichten; nichts davon ins Repo.
2. Pro Aussage eine Ledger-Zeile unter `.ai/memory/case-<slug>-YYYY-MM-DD.csv`, mit anonymisierter Quellen-ID.
3. Prüfen:

   ```bash
   python3 agents/skills/buyer-research/scripts/case-proof-check.py \
     .ai/memory/case-<slug>-YYYY-MM-DD.csv
   ```

4. Eine Zahl soll öffentlich werden, ist aber nicht im Kanon: eigener, ausdrücklich beauftragter Task für das Kanon-Update (`blocksy-child/inc/canon/`, danach `docs/architecture/LIVE_STATUS.md` und ggf. Sperrliste prüfen). Dieses Skill ändert den Kanon nicht.
5. Nach dem Kanon-Update den Ledger-Eintrag auf `source_kind=canon`, Schlüssel und `public=true` umstellen und erneut prüfen.
6. `--brief` ausgeben, Platzhalter aus Evidenz füllen.
7. Der Brief geht als Handoff an den Primärskill `conversion-copy`; der schreibt den Text.

## Harte Grenzen

- Rohe CRM-Exporte, Namen, Firmen, E-Mails und Telefonnummern gehören nie in Git. Nur anonymisierte IDs im Ledger.
- Ein Fall ist ein Fall, keine Statistik. Keine Verallgemeinerung auf „typisch“ oder „durchschnittlich“ ohne Datenbasis dafür.
- Keine Zahl ohne Beleg, Nenner und Zeitfenster; keine erfundene Zahl, kein Runden zugunsten der Aussage.
- Der Skriptlauf belegt Konsistenz des Ledgers, nicht die Richtigkeit der Quelle. Die Quelle prüft ein Mensch.
