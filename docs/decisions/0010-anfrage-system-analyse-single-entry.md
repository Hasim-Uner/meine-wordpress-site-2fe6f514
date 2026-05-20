# 0010 Anfrage-System-Analyse ersetzt zweistufige Diagnose

Status: superseded
Datum: 2026-05-04

Superseded-Hinweis: Der aktuelle öffentliche Einstieg ist der Marktcheck auf `/solar-waermepumpen-leadgenerierung/#marktcheck`; `/anfrage-system-analyse/` bleibt nur als Legacy-Redirect erhalten.

## Entscheidung

Der öffentliche Funnel nutzt vorerst nur eine Analyse-Stufe: `Anfrage-System-Analyse`.

Die bisher geplante öffentliche Zweistufigkeit aus `Readiness-Diagnose` und `Tiefendiagnose` wird abgelöst. `/readiness-diagnose/` bleibt als Legacy-Route erhalten und leitet auf den aktuellen Marktcheck weiter.

## Begründung

Der erste gute Kunde soll nicht über eine generische kostenlose Analyse gewonnen werden, sondern über einen selektiven Founding-Partner-Prozess. Die Analyse muss Entscheidungssicherheit liefern: Marktbild, Anfragepfad, Leadkosten-Korridor, Tracking-/CRM-Realität und klare Empfehlung für oder gegen eine Umsetzung. Eine zusätzliche öffentliche Tiefendiagnose erhöht die Komplexität, bevor Angebot und erster Case belastbar sind.

## Konsequenzen

- Primärer CTA für kalten Solar-/SHK-Traffic ist inzwischen `/solar-waermepumpen-leadgenerierung/#marktcheck`.
- Die Analyse wird nicht als kostenloser Massen-Leadmagnet positioniert.
- Der bestehende `readiness-diagnosis-payload.v1` bleibt bis zur nächsten Contract-Version intern stabil.
- Texte, Demo-CTA und Founding-Cohort-Block sprechen öffentlich von `Anfrage-System-Analyse`.
- Die Tiefendiagnose bleibt intern als Option möglich, aber nicht als öffentlicher Funnel-Schritt.
