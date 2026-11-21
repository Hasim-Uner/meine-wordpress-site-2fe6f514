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
| `value`, `unit` | Zahl wie belegt und Einheit (`%`, `EUR`, `count`, `days` …). `%`, `Prozent` und `percent` gelten gleich als Prozent |
| `numerator`, `denominator`, `denominator_label` | Rechenbasis; bei `%` Pflicht. Das Label benennt den Nenner in Worten (z. B. „vorqualifizierte CRM-Leads“) |
| `window_start`, `window_end` | Messfenster, ISO `YYYY-MM-DD` |
| `baseline_*` | optionaler Vorher-Wert mit eigenem Fenster; alle drei Felder oder keines |
| `source_kind` | `canon`, `crm_export`, `tracking_export`, `invoice`, `client_statement` |
| `source_locator` | `canon`: Schlüssel eines Blocks `'schluessel' => [ ... ]` im Kanon-Verzeichnis (ein bloßer Getter-Name genügt nicht). Sonst anonymisierte Export-/Beleg-ID, nie E-Mail oder Telefonnummer |
| `definition` | was genau gezählt wurde und was nicht. Öffentlicher Vorher/Nachher-Claim: zusätzlich `baseline:<kanon-schluessel>` |
| `attribution_note` | was die Zahl nicht beweist (Anteil von Vertrieb, Saisonalität, Mehrfachmaßnahmen …) |
| `public` | `true` nur für Zahlen, die öffentlich erscheinen sollen |

## Regeln und Begründung

Das Skript `scripts/case-proof-check.py` erzwingt sie. ERROR bricht ab (Exit 1), WARN nicht.

| Regel | Stufe | Grund |
| --- | --- | --- |
| `source_kind=canon`: der Schlüssel muss als Block `'schluessel' => [ ... ]` im Kanon-Verzeichnis lesbar sein; fehlt er oder lässt sich der Block nicht lesen, ist das ein Fehler (kein stilles Durchwinken) | ERROR | Der Kanon ist die einzige Quelle öffentlicher Zahlen (BRAND_AND_COPY, „Fakten stehen nie als Text“); ein erfundener oder nicht lesbarer Schlüssel wäre ein Beleg ohne Quelle |
| `source_kind=canon`: `value` stimmt mit der Kanonzahl überein (`counter_target`, sonst die erste Zahl in `display`), im Rahmen der Rundungsregel unten | ERROR „Kanonwert weicht ab“ | Nur das Vorkommen des Schlüssels zu prüfen ließ jeden Wert unter dem Schlüssel durch |
| `source_kind=canon`: `denominator_label` (bei `%`, sonst falls gesetzt) ist gleich dem `label` des Kanon-Eintrags, ohne Beachtung von Groß-/Kleinschreibung und Leerraum | ERROR „Bezugsgruppe weicht vom Kanon ab“ | Dieselbe Zahl gilt nur für ihre Bezugsgruppe. Das Ledger hat kein eigenes Label-Feld; geprüft wird daher das Nenner-Label |
| Kanon-Eintrag mit `display_hedged` (Annahme, keine Messung) | ERROR bei `public=true`, WARN sonst | Marktannahmen dürfen nicht als Ergebnis erscheinen; Wert- und Gruppenabgleich entfallen für sie. Nur `display_hedged` markiert eine Annahme; `value_high` allein tut es nicht |
| Kanon-Eintrag mit Spanne (`display` der Form „a–b“ mit Gedankenstrich oder Bindestrich, oder `value` plus `value_high`): `value` muss die ganze Spanne als „a-b“ nennen (Gedankenstrich oder Bindestrich), beide Endpunkte stimmen mit dem Kanon überein. Spannen überspringen die Zähler/Nenner-Rechnung | ERROR „Spanne als Einzelwert“ bei einer einzelnen Zahl, ERROR „Kanonwert weicht ab“ bei falschem Endpunkt | Eine gemessene Spanne ist keine Annahme, darf aber nicht auf einen Endpunkt gekürzt werden |
| Kanon-Eintrag mit Einheit (aus `display` abgeleitet: Prozentzeichen = Prozent, Euro-Zeichen oder EUR = Euro, sonst keine): die Ledger-`unit` muss übereinstimmen (`%`, `Prozent`, `percent` gelten gleich; Euro-Zeichen, `EUR`, `Euro` gelten gleich) | ERROR „Einheit weicht vom Kanon ab“ | Sonst ließe sich die Quoten-Pflicht (Zähler, Nenner, Label) durch eine fremde Einheit umgehen. Ein Prozent-Kanon-Claim braucht damit immer Zähler, Nenner und Nenner-Label |
| Nicht-Canon-Locator ist anonym: kein `@`, keine Telefonnummer | ERROR | Rohdaten und Personenbezug bleiben aus dem Ledger |
| Einheit Prozent: Zähler, Nenner und Nenner-Label Pflicht; Wert 0 bis 100; `value` weicht höchstens um eine halbe Einheit seiner letzten angegebenen Dezimalstelle von Zähler/Nenner × 100 ab | ERROR | Eine Quote ohne benannten Nenner lässt sich nicht verteidigen. Der Referenzfall zeigt es: Lead-Quote und Abschlussquote haben verschiedene Nenner (Besucher, vorqualifizierte CRM-Leads), siehe `hu_e3_summary( 'definitions' )` |
| Andere Einheit als Prozent, aber Zähler und Nenner gesetzt | ERROR „Einheit fuer Quote unklar“ | Sonst ließe sich die Rechenprobe durch eine andere Schreibweise der Einheit umgehen |
| Datumsfelder ISO; Fensterbeginn nicht nach Fensterende | ERROR | Ohne Zeitraum kein Vergleich |
| Baseline-Fenster liegt vollständig vor dem Ergebnisfenster | ERROR | Sonst vermischt der Vergleich Vorher und Nachher |
| Baseline- und Ergebnisfenster unterscheiden sich um mehr als 10 % in der Länge | WARN | Ungleich lange Fenster sind vergleichbar nur mit Kennzeichnung. Nie über unterschiedlich definierte Fenster mitteln (BRAND_AND_COPY, „Referenzfall“: Projektmonate schließen die Vorbereitung ein, Kampagnenmonate nicht) |
| Nenner kleiner 30 | WARN intern, ERROR bei `public=true` („kleine Basis nicht oeffentlich verallgemeinern“) | Kleine Basis: intern als Einzelfall formulieren; öffentlich nicht als Quote auftreten lassen. Auch ab 30 bleibt ein Betrieb ein Fall: das ist eine Formulierungsregel für den Text und vom Skript nicht prüfbar |
| `public=true` braucht `definition`, `attribution_note` und `source_kind=canon` | ERROR | Nicht-Canon-Zahl öffentlich: erst in den Kanon, dann öffentlich |
| `public=true` mit gesetztem `baseline_value`: `source_kind=canon` und in `definition` ein `baseline:<kanon-schluessel>`; dieser Schlüssel besteht denselben Wert- und Annahme-Abgleich (gegen `baseline_value`, ohne Gruppenvergleich) | ERROR | Ein öffentliches Vorher/Nachher braucht den Vorher-Wert im Kanon. Die Regel ist bewusst schmal: das Schema hat keine Spalten für Vorher-Zähler und -Nenner, daher prüft sie nur gegen den Kanon |
| Metrik `cpo` oder `cost_per_order`, deren Definition CPL und Quote zusammen nennt | ERROR | BRAND_AND_COPY verbietet eine Gesamt-CPO aus CPL × CRM-Quote. Die Regel ist bewusst schmal: sie prüft nur Metrikname und Definitionstext, keine Rechnung |
| doppelte `claim_id` | ERROR | Eindeutige Zuordnung im Brief |

### Rundungstoleranz

Die Toleranz folgt der angegebenen Genauigkeit: eine halbe Einheit der letzten
Dezimalstelle von `value`. Ein Wert ohne Nachkommastelle darf um höchstens 0,5
Punkte abweichen, ein Wert mit einer Nachkommastelle um höchstens 0,05. Wer
genauer schreibt, wird genauer geprüft. Das gilt für die Rechenprobe und für
den Kanonvergleich. Eine genauere Angabe als der Kanon hergibt ist deshalb ein
Fehler („Kanonwert weicht ab“, wenn der Wert auch abweicht). Hat das Ledger mehr
Nachkommastellen als die `display` des Kanons, ist das zusätzlich ein Fehler
„Scheingenauigkeit gegenueber Kanon“, auch wenn der Zahlenwert gleich bleibt
(Beispiel: zwei Nullen hinter einer ganzen Kanonzahl). Die Rundung folgt dem
Kanon, nicht der Aussage.

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

## Grenzen der Prüfung

Das Skript prüft Konsistenz mit dem Kanon, nicht Wahrheit und nicht Texttreue.
Offen und bewusst nicht gelöst:

- **Baseline-Bezugsgruppe:** Das Schema hat keine Spalte für die Bezugsgruppe der Baseline (Label, Zähler, Nenner). Der Vorher-Wert wird nur gegen den Kanon geprüft, nicht gegen eine eigene Gruppe. Ob Vorher und Nachher dieselbe Gruppe meinen, entscheidet ein Mensch; eine neue Spalte ist eine Schema-Entscheidung und hier nicht erfunden.
- **„Ein Betrieb bleibt ein Fall“:** Das ist eine Formulierungsregel für den Text (Copy-Ebene). Das Skript sieht nur Zahlen und kann Verallgemeinerungen im Text nicht erkennen.
- Quellenrichtigkeit und Attribution (`attribution_note`) prüft ein Mensch.

## Harte Grenzen

- Rohe CRM-Exporte, Namen, Firmen, E-Mails und Telefonnummern gehören nie in Git. Nur anonymisierte IDs im Ledger.
- Ein Fall ist ein Fall, keine Statistik. Keine Verallgemeinerung auf „typisch“ oder „durchschnittlich“ ohne Datenbasis dafür.
- Keine Zahl ohne Beleg, Nenner und Zeitfenster; keine erfundene Zahl, kein Runden zugunsten der Aussage.
- Der Skriptlauf belegt Konsistenz des Ledgers, nicht die Richtigkeit der Quelle. Die Quelle prüft ein Mensch.
