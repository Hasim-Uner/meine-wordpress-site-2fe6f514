# 0005 Diagnose wird zweistufig

Historische Entscheidung: Das ehemalige Framework samt Liefermodell wurde am
2026-10-03 eingestellt. Aktuell gilt `asset-system-retirement.md`; aktive
Angebotsdaten kommen aus den zentralen Kanons.

- Datum: 2026-05-02
- Status: superseded by `0010-anfrage-system-analyse-single-entry.md`

## Entscheidung

Der Einstieg in das WGOS-Angebot wird zweistufig.

1. Readiness-Diagnose: 14 Tage, 750 EUR, vollständig anrechenbar.
2. Tiefendiagnose: 30 Tage, 1.500 EUR, vollständig anrechenbar.

Die Readiness-Diagnose ist formulargetrieben und nutzt Selbstauskunft plus öffentliche Prüfsignale. Die Tiefendiagnose setzt die Readiness-Diagnose voraus oder schließt sie ein.

## Begründung

Ein einzelner 30-Tage-Einstieg ist für kalte Interessenten zu groß und für frühe Qualifizierung zu langsam. Die kleinere Readiness-Diagnose senkt die Einstiegshürde, ohne den Anspruch an Diagnosequalität oder Zahlungsbereitschaft aufzugeben.

## Konsequenzen

- `/readiness-diagnose/` wird der Primary-CTA für kalten B2B-Traffic.
- `/anfrage/` bleibt der Hauptintake für warme Anfragen.
- Beide Diagnose-Stufen führen nicht direkt in einen Foundation-Verkauf, sondern liefern eine Empfehlung oder Nicht-Empfehlung.
- Formularfragen zu Tracking, CRM und CAPI erlauben `Ja`, `Nein` und `Weiß ich nicht`.
- `Weiß ich nicht` ist fachlich gültig und wird im Befund als rotes Signal bewertet.
