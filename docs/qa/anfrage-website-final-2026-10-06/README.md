# Anfrage-Website: finale Gestaltung

Zielroute: `/wordpress-website-erstellen-lassen/`. Ausgangspunkt: `d5c13a7`
(PR #543). Auftrag vom 06.10.2026: vollständige Seite, eigener visueller Stil,
sichtbare Buttons, Suchintention und verständliche Qualitätsgründe im ersten
Besuch. Keine neue Preisregel, Messung oder Geschäftszusage.

## Entscheidung und Qualitätsbelege

| Ort | Beobachtung am 06.10.2026 | Evidenz | Änderung / nächste Prüfung |
| --- | --- | --- | --- |
| Einstieg | Angebot und Preis vorhanden, Qualitätsgründe überwiegend als Behauptungen | Aktuelles Template | Drei konkrete Gründe früh zeigen; direkte Prüflinks |
| Referenzen | Reale Ansichten und freigegebene Beiträge erst nach umfangreicher Konfiguration | Template, Referenzkanon | Belege vor Konfiguration; E3 zuerst, weitere Projekte kompakter |
| Vergleich | Schematische Ladeanimation wirkt wie Geschwindigkeitsvergleich | CSS/JS | Keine simulierte Ladezeit; Darstellung und Prüfung getrennt beschreiben |
| Buttons | Kleine Versalien und unterschiedlich gewichtete Folgeaktionen | CSS | Lesbare Beschriftungen, einheitliche Zustände, große Trefferflächen |
| Suchintention | Eigene URL, Title, Meta und FAQ-Kanon bereits vorhanden | SEO-Modul / Query-Owner | Eigentümer und zentrale SEO-Quellen erhalten; gerenderte Ausgabe prüfen |

| Qualitätsmerkmal | Kundennutzen | Beleg | Position |
| --- | --- | --- | --- |
| Verständliches Angebot und klare Buttons | Angebot und nächsten Schritt erkennen | Tatsächlicher Einstieg und Kapitelziele | Hero / Qualitätsabschnitt |
| Nachvollziehbarer Umfang und Preis | Kosten vor einer Anfrage prüfen | Bedienbarer Rechner; serverseitiger Preisabgleich | Hero / Konfigurator |
| Prüfbarer Anfrageweg und Übergabe | Anfrage und späteren Betrieb nachvollziehen | Vertraglicher Lieferumfang; isolierter Abschluss-Test | Qualitätsabschnitt / Lieferumfang |
| Gestaltung und konkrete Projektarbeit | Passung selbst beurteilen | Reale Screenshots, freigegebene Beschreibung des Beitrags | Frühe Referenzen |

Qualitätswirkung und höhere Anfragerate sind Hypothesen. Der 60-Sekunden-Check
ist eine Entwurfsprüfung, solange keine Personen aus der Zielgruppe beobachtet
wurden. Ein passender Besucher soll nach freier Betrachtung Angebot, Preis,
zwei Qualitätsgründe und nächsten Schritt ohne vorgegebenes Urteil benennen.

## Konfiguration

| Entscheidung | Optionen / Default | Preis und Zeit | Abhängigkeit / Hilfe |
| --- | --- | --- | --- |
| Seiten | 1–10 / 3 | Kanonischer Grundpreis plus Zusatzseiten | Vorlagen 1/3/5 ändern nur Seitenzahl |
| Art | Neubau / Relaunch; Neubau | Kanonische Relaunch-Zeit | Bestand und Weiterleitungen bei Relaunch |
| Texte | Erstellen / vorhanden; erstellen | Inklusive, eigene Vorbereitungszeit | Kundenangaben und Freigabe erforderlich |
| Gestaltung | Basis / vorhanden / neu; Basis | Bestehender Designkanon | Layoutzahl nur bei neuem Screendesign |
| Erweiterungen | Tracking / CRM / Dashboard; keine | Feste Extras aus Kanon; Dashboard separat | Explizite unbekannte Kosten / Zeit |

## Motion-Matrix

| Element | Auslöser | Zweck | Dauer / Distanz | Unterbrechung | Reduced Motion |
| --- | --- | --- | --- | --- | --- |
| Button / Auswahl | Hover, Fokus, Auswahl | Aktions- und Auswahlzustand | `--t-mikro`; Pfeil höchstens 3 px | CSS folgt aktueller Eingabe | Ohne Bewegung |
| Rechnerpreis | Preisänderung | Änderung bestätigen | `--t-norm`, 4 px / Opacity | Vorherige Animation abbrechen | Sofortige statische Ausgabe |
| Design / Extras | Bewusste Auswahl | Abhängige Inhalte erklären | `--t-norm`, 4 px / Opacity | Eingaben jederzeit möglich | Inhalte direkt sichtbar |
| Qualitätsansicht | Bewusster Wechsel | Vergleich der Struktur | Zustandswechsel, keine Ladezeit-Simulation | Sofort ersetzen | Gleicher Inhalt statisch |

Produktionsdateien werden auf einer separaten PR-Branch geändert. Die lokale
Vorschau rendert das tatsächliche Template mit WordPress-Testgrenzen. Keine
Live-Anfrage; keine Veröffentlichung durch die Vorschau.

## Validierung

| Prüfung | Ergebnis |
| --- | --- |
| Gesamtlauf `npm run check` | 29 Prüfgruppen bestanden; darin 155 Formular-/Produktfälle und 189 Navigations-/Seitenfälle (zwei vorhandene Fälle übersprungen) |
| Architektur, CSS-System, Tokens, Canon, Motion, Spacing | Bestanden; keine neue Bibliothek oder Tokenfamilie |
| PHP-Syntax / PHPStan | Bestanden mit der festgelegten PHP-Version |
| Produktbrowser in Chromium und Firefox | Je 24 Fälle bestanden: 40 Umfänge, 96 Faktorkombinationen, Extras, Preis, Zeit, Anfrage-URL, native Eingaben und isolierter Anfrageabschluss |
| Große Schrift, abschließende Wiederholungsprüfung | Je vier Breiten in beiden Browsern bestanden; kein horizontaler Überlauf; eine übergroße, ungenutzte Anfrageleiste wird ausgeblendet |
| Axe, WCAG-A/AA-Regeln bis 2.2 | 0 automatische Befunde bei 320, 390, 768 und 1440 Pixeln |
| Manuelle Sichtprüfung | Einstieg, Qualitätsgründe, Referenzen, Konfigurator, Vergleich, Ablauf, FAQs und Abschluss auf Desktop und Mobil geprüft |
| Standalone-Vorschau | Rechner, native FAQs und Anfrage-Vorschaudialog bedienbar; keine externen Ressourcen oder tatsächliche Anfrage; keine JavaScript-Fehler |
| Copy- und Diff-Prüfung | Keine Befunde; keine erfundenen Kennzahlen, Stimmen oder Rangversprechen |

Der Firefox-Durchgang zeigte bei 320 Pixeln und 200 Prozent Schriftgröße einen
Überlauf im Vergleichsraster. Eine explizite schrumpfbare Grid-Spalte behebt
ihn. Die mobile Anfrageleiste darf umbrechen. Bei großer Schrift blendet sie
sich aus, wenn sie mehr als ein Drittel des Bildschirms belegen würde; die
normale Auswahlübersicht und Abschlussanfrage bleiben erreichbar. Ein bereits
fokussiertes Bedienelement wird dabei nicht ausgeblendet.

Die gerenderte Route behält genau eine H1 mit der Suchintention, zentrale
Title-/Meta-Quellen, die vorhandene URL und den zentralen FAQ-Kanon. Canonical,
Schema und Indexierungsregeln bleiben in den vorhandenen SEO-Modulen. Es gibt
keine zweite konkurrierende Angebotsseite und keine neue SEO-Zusage.

Die Browserprüfungen rendern das echte PHP-Template und die echten CSS-/JS-
Dateien mit isolierten WordPress-Grenzen. Sie sind keine Feldmessung der neuen
Live-Fassung. Safari, reale Geräte und manuelle Screenreader-Bedienung wurden
hier nicht geprüft; automatische Axe-Regeln belegen keine vollständige
WCAG-Konformität.

## Verständlichkeit nach einer Minute

Nächste Prüfung mit fünf passenden Interessenten: 60 Sekunden freie Betrachtung,
danach die Seite schließen. Ohne vorherige Hinweise fragen: Was wird angeboten?
Welchen Preisrahmen haben Sie verstanden? Welche zwei Gründe sprechen für die
Qualität, und woran würden Sie sie prüfen? Was wäre Ihr nächster Schritt?
Was blieb unklar? Die Antworten wörtlich festhalten, ohne sie zu verbessern.
Ziel: mindestens vier von fünf Personen benennen Angebot, Preisgrundlage,
zwei konkrete Gründe und eine passende nächste Aktion. Bis dahin bleibt dieser
Teil eine begründete Gestaltungshypothese.
