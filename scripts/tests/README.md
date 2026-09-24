# Formular- und Endpoint-Prüfung

```sh
npm ci
npx playwright install --with-deps chromium
npm run test:forms
npm run test:intake
```

Auf macOS nutzt die Browser-Konfiguration vorhandenes Google Chrome, sonst das
von Playwright installierte Chromium. CI führt beide Prüfungen aus. Artefakte
liegen unter `.build/form-test-results/`. Benötigt werden Node 20–22 und PHP
mit mbstring. Es werden keine produktiven Endpunkte aufgerufen und keine
echten E-Mails gesendet.

## Ausgangsbefund und Annahmevertrag

Ausgangspunkt: `52154bb2c0a7695ef59ea716046d82df6f09ba06`, am 2026-09-25 auch
Remote-Stand. Vor der Korrektur scheiterte die Browser-Prüfung
`contact › rejects empty object with HTTP 200`: Der echte Controller zeigte
„Danke. Ihre Anfrage ist eingegangen.“ und setzte das Formular zurück. Dasselbe
passierte bei der Ersteinschätzung und beim Blog-Abo. White-Label und Marktcheck
lehnten `{}` bereits ab, behandelten aber `{"ok":"false"}` als Erfolg; auch
das wurde vor der Korrektur im Browser reproduziert. Kein Beleg für tatsächlich
verlorene Anfragen.

Kontakt und White-Label: Browser validiert und ergänzt Herkunft → eigener REST-
Endpunkt → Honeypot, Rate-Limit und Feldvalidierung → CRM-Upsert und Aktivität →
interne Benachrichtigung → Bestätigung. Das Backend liefert `ok: true`, wenn CRM
oder interne Mail-Annahme gelingt. `contactId: 0` bleibt für den Mail-Rückfall
gültig. Scheitern beide, folgt HTTP 500 mit `ok: false`. CRM-Erfolg bei gescheiterter
interner Mail bleibt erfolgreich und nutzt die vorhandene Fehlermeldung im
Admin. Die Bestätigungsmail entscheidet nicht über die Annahme.

Marktcheck bestätigt nach Speicherung des Review-Datensatzes; Mail-Annahme
entscheidet dort nicht über Erfolg. Blog-Abo bestätigt einen gespeicherten
Pending-Eintrag mit angenommener DOI-Mail oder ein bereits aktives Abo. Ein
Honeypot-Treffer bestätigt absichtlich ohne Speicherung. Ein erfolgreiches
[`wp_mail()`](https://developer.wordpress.org/reference/functions/wp_mail/)
belegt die Annahme durch den Versandweg, keine Zustellung beim Empfänger.

## Verhalten und Abdeckung

| Antwort / Situation | Rückmeldung | Eingaben | Senden |
| --- | --- | --- | --- |
| HTTP 200/201, Objekt mit `ok: true`, auch ohne positive Kontakt-ID | Erfolg | Reset; Marktcheck zeigt Ergebnis | wieder aktiv bzw. Formular ersetzt |
| Leerer Körper, kaputtes JSON, HTML, `null`, Array, Skalar, `{}`, fremdes JSON, falscher `ok`-Typ, HTTP 204 | Eingang nicht bestätigt, möglicherweise angekommen | erhalten | wieder aktiv |
| Explizites `ok: false`, HTTP 400/429/500 | Endpoint-Fehlermeldung | erhalten | wieder aktiv |
| Feldvalidierung | Fehlermeldung und `aria-invalid`; Kontakt zeigt betroffenen Schritt | erhalten | wieder aktiv |
| Netzwerkfehler / keine Antwort / hängender Antwortkörper | Eingang unbestätigt; Hinweis auf mögliche Annahme | erhalten | spätestens nach 30 Sekunden wieder aktiv |
| Zwei Submit-Ereignisse während eines Versuchs | ein ausgehender POST | währenddessen gesperrt | währenddessen gesperrt |
| Antwort nach Timeout, auch während eines neuen Versuchs | keine veraltete Rückmeldung | unverändert | neuer Versuch bleibt maßgeblich |

Browserprüfungen laden die unveränderten produktiven JavaScript-Dateien und für
Kontakt/Ersteinschätzung das echte PHP-Template. White-Label und Blog nutzen
kleine native DOM-Fixtures; der Marktcheck rendert selbst. Antwortsteuerung und
Browser-Uhr ermöglichen deterministische Fehler- und Timeoutfälle. Zusätzlich
wird natives `fetch()` mit abgefangener HTTP-Antwort geprüft. Keine Kopie der
Erfolgsbedingung im Prüfcode.

Die PHP-Prüfung führt echte Handler, Validatoren, CRM-Helfer und Mail-Erzeugung
aus. WordPress-Funktionen für Speicherung, Optionen, Nonces und `wp_mail` sind
isolierte Doubles; Mail-Aufrufe erlauben ausschließlich `example.test`.
Speicher-/Benachrichtigungs-/Bestätigungsfehler werden getrennt geprüft, ebenso
Spam-Schutz, Consent, Attribution und Erstkontaktvarianten.

Grenzen: keine isolierte vollständige WordPress-Installation, keine echte
Datenbank, keine Produktions-Caches und kein Brevo-Aufruf. Die Prüfungen belegen
weder produktive CRM-Speicherung noch Mailzustellung. Ein manueller Neuversuch
nach unklarer Übermittlung kann doppelte Aktivitäten oder Mails auslösen:
E-Mail-Upsert ist keine Anfrage-Deduplizierung. Deshalb keine automatischen
POST-Wiederholungen. Routen, Angebot, Ersteinschätzungsversuch und Datenschutz-
Vertrag bleiben unverändert; keine manuellen Admin-Aufgaben oder Migrationen.
