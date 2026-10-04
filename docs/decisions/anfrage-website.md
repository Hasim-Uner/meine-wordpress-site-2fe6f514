# Die Anfrage-Website

Freigegeben durch Haşim am 02.10.2026, Versionen 3–6 des Auftrags.
Diese Entscheidung ersetzt den Website-Teil von `preise-website-landingpage.md`.

Produkt statt offener Dienstleistung: erste Seite 1.490 € netto, jede weitere
290 €, Tracking optional 890 €. Bis zehn Seiten selbst rechnen; darüber Angebot.
Ergänzung 04.10.2026: Jede gewählte Seite enthält Texte auf Wunsch, auf Basis
der Kundenangaben. Technisches SEO, Formular, Bestätigungsmail, Danke-Seite sowie
Impressum-/Datenschutz-Seiten mit gelieferten Rechtstexten sind Standard.
Screendesign und CRM sind wählbar nach separatem Angebot; kein Preis erfunden.
Die Landingpage behält ihren eigenen spezialisierten Angebotsumfang und Preis.
Bauzeiten und Lieferzusagen sind ausdrücklich freigegeben, siehe Markenkanon.
Die Produktoberfläche vom 04.10.2026 ersetzt die Angebotsdarstellung des Prototyps:
Hero → Konfigurator mit Zusammenfassung und aufklappbarem Lieferumfang → Vergleich
→ Beleg → Zeit → FAQ → Anfrage. Custom Code, Gutenberg/ACF, GitHub und KI-Workflow
mit Prüfung sind sichtbare Qualitätsmerkmale. Der Kopf bleibt global. Auswertung nach acht Wochen oder 300 Aufrufen.

## Ergänzung: adaptive Zeitkalkulation, 04.10.2026

Die Wochenstaffel wird durch Arbeitsphasen und Werktage ersetzt. Auslöser:
fertige Texte/Designs sollen bei reiner Implementierung keine Erstellungszeit
auslösen; Tracking ist ein Projekt-Setup und kein Aufwand pro Seite.

Die benannten Faktoren in `hu_website_calculator_rules()` sind die gemeinsame
Konfiguration für PHP und Browser. Erste Seite drei Werktage inklusive QA,
Zusatzseite ein Tag, Standard-Tracking einmal ein Tag. Neues Screendesign:
erstes Layout zwei, weitere unterschiedliche Layouts je ein Tag. Texterstellung:
eins plus ein halber je Zusatzseite, zusammen aufgerundet; im Seitenpreis enthalten.
Relaunch: zwei Tage für Bestandsaufnahme und Weiterleitungen.

Die Zahlen sind logisch getrennte Planungsannahmen, keine gemessenen Projektzeiten.
Werktage beschreiben geplante Durchlaufzeit, nicht abrechenbare Personentage.
Freigabewartezeiten und Kapazitäts-/Starttermine sind separat. Ziel der angezeigten
Zeit ist ein geprüfter Abnahmestand; der Veröffentlichungstermin steht im Angebot.
Bei CRM gibt es keine erfundene Tageszahl. Die bekannte Summe ist als Mindestwert
ohne CRM-Aufwand markiert. Vorhandene Designs werden vor Preis-/Zeitzusage geprüft.

Die Layoutzahl ist bei neuen Designs zunächst gleich der Seitenzahl (konservative
Planung); der Kunde kann wiederverwendete Seitentypen zusammenfassen. Beim
Verkleinern des Umfangs wird sie auf die Seitenzahl begrenzt. Weder Layoutzahl
noch Designwahl ändern einen nicht freigegebenen Designpreis in einen Festpreis.
Preisformel unverändert. Alte `screendesign=1`-Anfragen bleiben kompatibel und
werden als neue Gestaltung behandelt. Dauer, Faktoren-Version und Optionen
werden serverseitig berechnet, im CRM gespeichert und in beiden Mails dargestellt.
