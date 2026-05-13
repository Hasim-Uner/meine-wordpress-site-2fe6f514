# Anfrage-System-Analyse Report v1

Status: defined-local-result-with-contact-submit
Owner: Repo / Funnel
Input-Spec: `docs/specs/anfrage-system-analyse-form-v1.md`
Runtime: `blocksy-child/readiness/src/App.tsx`

## Zweck

Dieser Report definiert die fachliche Bedeutung des lokalen Ergebnisses am Ende der Anfrage-System-Analyse. Er ist die Brücke zwischen dem aktuellen Browser-Ergebnis und einer späteren serverseitigen Befundlogik.

Der Report entscheidet nicht über einen Verkauf. Er ordnet ein, ob ein eigener Anfrage-System-Aufbau für den Betrieb aktuell sinnvoll, bedingt sinnvoll oder nicht empfehlenswert ist.

## Architektur-Grenzen

- WordPress bleibt das Hauptsystem für Route, SEO, Consent-Grenzen und spätere REST-Schicht.
- React berechnet in v1 nur ein lokales Ergebnis im Browser.
- Der Default-Fragepfad sendet keine Daten an n8n, CRM, E-Mail-Systeme oder externe Webhooks.
- Der separate Kontakt-Schritt sendet nach Einwilligung Name, Firma, E-Mail, Ergebnis und Antworten an WordPress REST, speichert in `nexus_contact` und triggert die zentrale Transaktionsmail-Schicht.
- Der Report enthält keine Namen, E-Mail-Adressen, Telefonnummern, Volladressen oder personenbezogenen Endkundendaten.
- E3-Proof-Zahlen dürfen in Report-Kontexten nur über `blocksy-child/inc/canon/e3-proof-canon.php` verwendet werden.

## Ergebnis-Status

| Status | Technischer Wert | Bedeutung | Nächster Schritt |
|---|---|---|---|
| Grün | `fit_green` | Umsetzung kann fachlich geprüft werden. | Founding-Partner-Fit einordnen; erst danach über Umsetzung sprechen. |
| Gelb | `fit_yellow` | Fit ist möglich, aber ein oder mehrere Risiken müssen vorher geklärt werden. | Gelbe und rote Signale bearbeiten, bevor Budget oder Technik gebaut wird. |
| Rot | `fit_red` | Keine Umsetzungsempfehlung im aktuellen Zustand. | Kein Umsetzungs-Pitch; zuerst Branche, Budget, Prozess oder Messbarkeit korrigieren. |

## Score-Schwellen

| Bedingung | Ergebnis |
|---|---|
| Score ab 74 und kein Hard Stop | Grün |
| Score 48 bis 73 oder genau ein Hard Stop | Gelb |
| Score unter 48 oder mindestens zwei Hard Stops | Rot |

Der Score ist ein Orientierungswert von `0` bis `100`. Er ist kein ROI-, Leadkosten- oder Umsatzversprechen.

## Hard Stops

Hard Stops sind Gründe, die eine Umsetzungsempfehlung blockieren oder stark einschränken.

| Bereich | Hard Stop |
|---|---|
| ICP | Branche außerhalb Solar, SHK, Wärmepumpe oder energie-nahem Speicher-Kontext |
| Betriebsgröße | sehr kleiner Betrieb mit `1-5` Mitarbeitenden |
| Region | außerhalb DACH |
| Wirtschaftlichkeit | durchschnittlicher Auftragswert unter `5.000 EUR` |
| Budget | Werbebudget unter `5.000 EUR / Monat` |
| Anfrageprozess | Reaktionszeit `später` oder `weiß ich nicht` |
| Verantwortung | keine klare Verantwortlichkeit oder `weiß ich nicht` |

Ein einzelner Hard Stop führt mindestens zu Gelb. Zwei oder mehr Hard Stops führen zu Rot.

## Modul-Signale

### ICP-Fit

Grün, wenn Branche und Betriebsgröße zum Founding-Partner-Rahmen passen.

Gelb, wenn der Betrieb energie-nah ist, aber Angebot oder Betriebsgröße noch eingeordnet werden müssen.

Rot, wenn die Branche außerhalb des aktuellen Fokus liegt oder der Betrieb sehr wahrscheinlich zu früh für ein eigenes Anfrage-System ist.

### Wirtschaftlichkeit

Grün, wenn Auftragswert und Monatsbudget einen bezahlbaren Anfragepfad plausibel tragen können.

Gelb, wenn Wirtschaftlichkeit möglich ist, aber Priorisierung, Marge oder Budgetdisziplin kritisch werden.

Rot, wenn Auftragswert oder Werbebudget unter den Mindestschwellen liegen.

### Anfragepfad

Grün, wenn eine Website-URL vorliegt und der Anfragepfad grundsätzlich prüfbar ist.

Gelb, wenn keine URL vorliegt oder die CMS-Lage unklar ist. Die Analyse bleibt dann bewusst gröber.

Rot ist in v1 für dieses Modul nicht vorgesehen, weil eine fehlende URL kein alleiniger Ausschlussgrund ist.

### Messbarkeit

Grün, wenn Pixel, GTM, Consent Mode und Meta CAPI laut Selbstauskunft bekannt oder vorhanden sind.

Gelb, wenn ein bis zwei Antworten `Nein` oder `Weiß ich nicht` sind.

Rot, wenn drei oder mehr Antworten `Nein` oder `Weiß ich nicht` sind.

Unklare Tracking-Antworten blockieren das Formular nicht, werden aber im Befund rot oder gelb bewertet.

### Anfrageprozess

Grün, wenn Reaktionszeit, Verantwortlichkeit und CRM-Selbstauskunft anschlussfähig wirken.

Gelb, wenn CRM fehlt, Verantwortlichkeit geteilt ist oder die Reaktionszeit erst am nächsten Werktag liegt.

Rot, wenn Reaktionszeit oder Verantwortlichkeit unklar beziehungsweise zu langsam sind.

### Marktbild

Grün, wenn Zielregion, Hauptkanal und Wettbewerbseindruck eine erste Korridor-Einordnung erlauben.

Gelb, wenn Zielregion oder Kanalbild unklar sind oder hoher Wettbewerb mit knappem Budget zusammentrifft.

Rot ist in v1 für dieses Modul nicht vorgesehen. Marktbild allein erzeugt keine Nicht-Empfehlung.

## Begründungslogik

Der Report zeigt maximal sechs Begründungen.

Reihenfolge:

1. Hard Stops zuerst.
2. Danach positive oder einschränkende Kontextgründe.
3. Keine erfundenen Garantien, keine exakten Leadkosten, keine Umsatzprognosen.
4. Fehlende Website-URL immer als eingeschränkte Befundtiefe markieren.
5. `Weiß ich nicht` bei Tracking oder CRM nie blockieren, aber sichtbar als Risiko benennen.

## Empfohlene Output-Struktur

Diese Struktur ist die Zielstruktur für eine spätere REST- und n8n-Anbindung. Die aktuelle React-App nutzt sie noch nicht als serialisierten Payload.

```json
{
  "report_version": "request_analysis_report.v1",
  "overall": {
    "fit_signal": "fit_green",
    "score": 82,
    "label": "Grüner Fit: Umsetzung prüfen",
    "recommendation": "Founding-Partner-Fit einordnen; erst danach über Umsetzung sprechen."
  },
  "modules": [
    {
      "key": "icp_fit",
      "signal": "green",
      "summary": "Branche und Betriebsgröße passen zum Founding-Partner-Rahmen."
    }
  ],
  "reasons": [
    "Branche passt zum Anfrage-System-Fokus."
  ],
  "constraints": [
    "Leadkosten bleiben ein Korridor, keine Garantie."
  ],
  "privacy": {
    "default_path_contains_personal_data": false,
    "submitted_to_n8n": false,
    "crm_write_after_consent": true,
    "transactional_email_after_consent": true
  }
}
```

## Textregeln

- Rot muss eine klare Nicht-Empfehlung enthalten.
- Gelb darf nicht wie ein versteckter Sales-CTA klingen.
- Grün ist eine Prüfempfehlung, kein automatischer Umsetzungsabschluss.
- Leadkosten nur als Korridor formulieren.
- Keine Formulierung darf den Growth Audit wieder als Hauptfunnel setzen.
- Keine Formulierung darf den retired EnergieFahrplan-Showroom als SaaS oder Lead-Pflichtpfad rahmen.

## Nächste Implementierungsschritte

1. Serverseitige Scoring-Funktion mit denselben Schwellen und Hard Stops anlegen.
2. React-App gegen gemeinsame Report-Semantik angleichen, damit Client und Server nicht driften.
3. Erst danach Consent-UI, REST-Submit, Contract-Version und n8n-Branch bauen.
