# Privacy

Stand: 2026-05-07

## Anfrage-System-Analyse

Die Anfrage-System-Analyse ist ein eigener Verarbeitungsvorgang. Sie dient der evidenzbasierten Prüfung, ob ein Solar-, Wärmepumpen- oder SHK-Betrieb als Founding-Partner für ein eigenes Anfrage-System geeignet ist.

## Verarbeitete Daten

Der Default-Fragepfad verarbeitet im Browser nur grobe Betriebs- und Selbstauskunftsdaten:

- Branche
- Mitarbeiter-Range
- Land und PLZ-Region
- Website-URL, falls vorhanden
- Angebotsfokus
- Werbebudget-Range
- Selbstauskunft zu Tracking, CRM, Consent Mode, serverseitigem Tracking und Meta CAPI
- Selbstauskunft zu Lead-Volumen, Lead-Qualität und Engpass
- UTM-Parameter, Referrer und Click-IDs, falls vorhanden

## Nicht verarbeitete Daten im Default-Fragepfad

- kein Klarname
- keine Telefonnummer
- keine E-Mail-Adresse
- keine personenbezogenen Endkundendaten
- keine Admin-Zugänge zu GA4, GTM, Ads, CRM oder Pixeln
- keine Cookies beim normalen Seitenaufruf

## Consent-Logik

Der Kontakt-Submit braucht eine sichtbare Zustimmung direkt im Analyse-Formular. Es gibt keinen globalen Banner als Ersatz für diese Zustimmung.

Der aktuelle WordPress-REST-Submit speichert nach Einwilligung:

- Name
- Firma
- E-Mail-Adresse
- lokales Analyse-Ergebnis
- Antworten aus dem Fragepfad
- optionale Website-URL
- UTM-Parameter und Click-IDs, falls vorhanden

Marketing und Analytics bleiben im Default-Fragepfad ausgenommen, solange keine eigene Zustimmung vorliegt.

## Transaktionsmail

Nach erfolgreichem Kontakt-Submit versendet die zentrale Mail-Schicht:

- eine interne Admin-Benachrichtigung
- eine Lead-Bestätigung an die angegebene E-Mail-Adresse

Telefonnummern bleiben ausgeschlossen. Klarnamen sind nur im Kontakt-Schritt nach Einwilligung erlaubt.

## n8n-Retention

n8n ist für die Anfrage-System-Analyse aktuell nicht angebunden. Falls später ein n8n-Branch aktiviert wird, dürfen Analyse-Intakes dort maximal 30 Tage gespeichert werden. Danach werden sie gelöscht oder so anonymisiert, dass kein Rückschluss auf den konkreten Betrieb möglich ist.

## Auftragsverarbeitung

WordPress ist für diesen Prozess Website, REST-Empfänger und CRM-Speicher (`nexus_contact`). Brevo ist für Transaktionsmails angebunden. n8n ist kein aktiver Empfänger dieses Payloads.

Kein neuer Drittland-Default wird durch die Anfrage-System-Analyse eingeführt.
