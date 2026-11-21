# LinkedIn Inbound: Beitrag aus Audit oder Konzept

Herkunft: Prüfraster, Bewusstseinsstufen und Positionierungskette stammen aus coreyhaines31/marketingskills. Ein LinkedIn-Hook-Rahmen steht dort nicht; Beitragsgerüst und Übersetzungsregeln unten sind eigene Herleitungen aus Grundprinzipien, kein Import. Kalte Social-Inhalte wurden verworfen.

## Zweck und Grenzen

Inbound-Positionierung: Wer den Beitrag liest, soll die Beobachtung bei sich wiederfinden. Der Beitrag verkauft nicht.

- Keine Direktnachrichten an Fremde mit Werbeinhalt (elektronische Werbung ohne Einwilligung, Risiko nach § 7 UWG). Keine Rechtsberatung, im Zweifel vor dem Posten prüfen lassen.
- Kein Engagement-Köder: kein „Kommentiere …“, kein „Like wenn …“, kein „Link in den Kommentaren“.
- Höchstens 3 Hashtags, keine URL im Beitragstext.
- Kein Verkaufs-CTA im Text; eine offene Frage am Ende oder nichts.

## Gerüst

1. Gegenthese: der verbreitete Glaube in einer Zeile, höchstens 120 Zeichen. Sie steht vor dem „mehr anzeigen“ und ist der Hook.
2. Beobachtung: eigene, anonymisierte Audit-Evidenz, mit `[Q: …]`-Marker.
3. Mechanismus: warum es so ist.
4. Grenze: wann die These nicht gilt. Erst die Grenze trennt These von Provokation.
5. Offene Frage oder nichts.

Höchstens 3000 Zeichen. Der Linter `channel-check.py linkedin` erwartet ein Signal der Grenze (Gilt nicht, Grenze, Ausnahme, funktioniert nicht, wenn).

## Tiefe Audits übersetzen

- Ein Befund pro Beitrag, ein Beobachtbares.
- Zahlen nur mit Quellenmarker; Zahlen aus dem Kanon mit `[Q: canon:<Schlüssel>]`.
- Kundennamen nur mit schriftlicher Freigabe, sonst anonymisieren (Gewerk, Größenordnung, Ausgangslage; vgl. `agents/skills/seo-conversion-copywriting/SKILL.md`, Abschnitt „A — Harte Regeln“).
- Befund und Deutung trennen: erst was zu sehen war, dann warum.

## Eigene Konzepte übersetzen

- Der Leser kennt den Begriff nicht. Erst in einem Satz über die beobachtbare Wirkung in einer Website, einem Funnel oder einem Tracking-Aufbau definieren, danach den Begriff benutzen.
- Lässt sich der Begriff an keiner Beobachtung festmachen, ist es noch kein Beitrag.
- Die Bedeutung eines selbst geprägten Begriffs definiert ausschließlich der Inhaber. Im Entwurf steht dafür der Platzhalter `[DEFINITION OWNER: <Begriff>]`; der Linter meldet ihn als offen, bis er ersetzt ist.

## Prüfung

- Prüfraster aus `SKILL.md` auf jeden Satz: vorstellbar, beweisbar, Swap-Test.
- Bewusstseinsstufe: Leser sind meist problem-unaware oder problem-aware. Nächster Schritt ist das Erkennen im eigenen Aufbau, nicht eine Anfrage.
- Positionierungskette bestimmt, welcher eine Unterschied im Beitrag steht; Begriffe und Ton nach BRAND Preferred Terms und Tone.
- Posting nur mit `--strip`-Fassung und Freigabe des Inhabers.
