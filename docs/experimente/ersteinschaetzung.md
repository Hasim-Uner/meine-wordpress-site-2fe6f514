# Versuch: Kostenlose Ersteinschätzung

Direktinteressenten ohne fertiges Projekt bekommen auf der Startseite einen
niedrigschwelligen ersten Schritt: URL schicken, drei Befunde schriftlich
zurück, ohne Verpflichtung. Der Versuch läuft acht Wochen und lässt sich per
Schalter vollständig zurücknehmen.

## Laufzeit

| | Datum |
| --- | --- |
| Start | Deploy-Tag (Merge auf `main`, `main` deployt nach grüner CI): _eintragen_ |
| Ende | Start + 8 Wochen: _eintragen_ |

## Was sich ändert, solange der Schalter an ist

- **Startseite `/`** (auch `/wordpress-freelancer-hannover/`, das per 301 auf
  `/` zeigt): Hero und Abschluss bekommen „Kostenlose Ersteinschätzung“ als
  primären Button mit einer Zeile darunter. Der bisherige Projekt-Button
  bleibt mit gleichem Ziel und gleichen `data-track`-Hooks als sekundärer
  Button daneben. Im Abschluss stehen beide Wege seit 2026-09-23 als Karten
  („Sie schicken / Sie bekommen / Antwort“); die Texte der Ersteinschätzung
  (`card_title`, `card_send`, `card_get`) stehen im Kanon.
- **`/kontakt/?focus=ersteinschaetzung`**: Das Anliegen „Ersteinschätzung“ ist
  vorausgewählt, die Themenfrage entfällt. Schritt 1 fragt die Website-URL
  (Pflicht) und in einem Satz, was die Website erreichen soll (optional).
  Schritt 2 ist der bestehende Kontaktschritt. Über dem Formular stehen die
  Zusage und die Antwortzeit aus dem Kanon.
- **Mails**: Die interne Benachrichtigung und die Bestätigung tragen im
  Betreff das Präfix `[Ersteinschätzung]`.

Title, H1, Meta und JSON-LD bleiben auf allen Seiten unverändert, auch auf
`/kontakt/`. `/kontakt/` ohne Parameter und alle anderen Seiten verhalten sich
wie vorher.

## Wo was steht

| Was | Wo |
| --- | --- |
| Schalter `HU_EXPERIMENT_ERSTEINSCHAETZUNG` | `blocksy-child/inc/canon/messaging-canon.php` |
| Alle Texte, Kennung `ersteinschaetzung`, Betreff-Präfix | `hu_first_assessment_text()` im selben Abschnitt |
| URL `/kontakt/?focus=ersteinschaetzung` | `hu_first_assessment_url()` |
| Antwortzeit | `hu_response_promise()`, keine eigene Fassung |
| Buttons Startseite | `blocksy-child/front-page.php`, Stil in `assets/css/startseite.css` |
| Formular-Variante | `blocksy-child/page-kontakt.php`, `assets/js/contact.js` |
| Validierung, Betreff-Präfix | `blocksy-child/inc/contact-page.php` |

Das Formular ist das bestehende `/kontakt/`-Formular am Endpoint
`nexus/v1/contact-request`. Neu ist nur der Anfragetyp `ersteinschaetzung`.
Im CRM landet die Einsendung als `nexus_contact` mit Quelle `general_inquiry`
und `_nexus_contact_request_type = ersteinschaetzung`. Neue Felder, Quellen
oder Segmente gibt es nicht.

## Schalter

Standard ist an. Abschalten ohne Deploy über `wp-config.php`:

```php
define( 'HU_EXPERIMENT_ERSTEINSCHAETZUNG', false );
```

Dauerhaft ab: den Standardwert im Kanon auf `false` setzen und deployen.

Ist der Schalter aus, rendern Startseite und `/kontakt/` wieder wie vor dem
Versuch; `?focus=ersteinschaetzung` fällt auf die normale Projektanfrage
zurück. Einzige Ausnahme: Der Endpoint nimmt eine Ersteinschätzung weiter an
und versieht die Mails mit dem Präfix. Sonst ginge eine Einsendung aus einer
noch zwischengespeicherten Seite nach dem Abschalten verloren.

## Messung

Gezählt wird nur über den Betreff. Kein Cookie, kein Skript, kein
Consent-Banner, keine neue Analytics.

- **Einsendungen**: Mails im Postfach kontakt@hasimuener.de mit
  `[Ersteinschätzung]` am Anfang und „Neue Ersteinschätzung“ im Betreff. Die
  Bestätigung geht an die einsendende Person, nicht ins eigene Postfach.
  Antworten darauf beginnen mit „Re:“ und zählen nicht als Einsendung.
- **Bezahlte Gespräche**: trägt der Betreiber selbst nach.

Schlägt die interne Mail fehl, steht die Einsendung trotzdem im CRM
(Anfragetyp „Ersteinschätzung“); der Fehler wird protokolliert.

| Woche | Einsendungen | davon bezahlte Gespräche | Notiz |
| --- | --- | --- | --- |
| 1 | | | |
| 2 | | | |
| 3 | | | |
| 4 | | | |
| 5 | | | |
| 6 | | | |
| 7 | | | |
| 8 | | | |
| **Summe** | | | |

## Abbruchregel

Kommen überwiegend Anfragen, die ich nicht annehmen will, wird der Schalter
ausgeschaltet. Das geht jederzeit, auch vor Ablauf der acht Wochen.

## Nach dem Ende

- **Behalten**: Den Versuch in den regulären Routing-Vertrag übernehmen
  (`docs/architecture/CONVERSION_ROUTING.md`) und den Abschnitt „Versuch“ im
  Kanon umbenennen.
- **Zurücknehmen**: Schalter aus, danach den Code in einem eigenen PR
  entfernen. Einsendungen im CRM bleiben erhalten.

## Manuell nach dem Deploy

- Eine Testeinsendung über `/kontakt/?focus=ersteinschaetzung` schicken und
  prüfen, dass sie mit dem Präfix bei kontakt@hasimuener.de ankommt. Den
  Empfänger der internen Mail bestimmt die Laufzeit (`admin_email` bzw. der
  Filter `nexus_contact_notification_email`), nicht das Repo.
- Die drei Befunde pro passender Einsendung schreibt der Betreiber selbst.
  Über dem Formular steht die Antwortzeit direkt nach der Zusage. Besucher
  lesen sie deshalb als Frist für die Befunde oder die Absage.
