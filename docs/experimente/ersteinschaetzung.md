# Versuch: Kostenlose Ersteinschätzung

Direktinteressenten ohne fertiges Projekt bekommen auf der Startseite einen
niedrigschwelligen ersten Schritt: URL schicken, drei Befunde schriftlich
zurück, ohne Verpflichtung. Der Versuch läuft acht Wochen und lässt sich per
Schalter vollständig zurücknehmen.

## Laufzeit

| | Datum |
| --- | --- |
| Schalter an | 2026-09-23 (#440) |
| Zählung ab | 2026-09-25: Relaunch der Startseite als Strecke (#453, #455). Was vorher einging, lief auf einer anderen Seite und zählt nicht mit. |
| Ende | Zählbeginn + 8 Wochen: 2026-11-20 |
| Startseite | Einfrierung durch den Auftrag zur finalen Fassung vom 2026-10-02 aufgehoben. Der Livegang dieser Fassung wird als neue Beobachtungsphase notiert; Daten davor und danach nicht zusammen auswerten. |

## Entscheidung am Ende

Festgelegt vor dem ersten Ergebnis, damit die Zahl die Entscheidung trifft und
nicht umgekehrt. „Passend“ heißt: ein Vorhaben, das ich als Projekt annehmen
würde (WordPress, Tracking oder Conversion für ein Unternehmen, kein
Privatprojekt, kein reiner Preisvergleich).

| Ergebnis nach 8 Wochen | Entscheidung |
| --- | --- |
| Mindestens ein bezahltes Gespräch oder Projekt aus einer Ersteinschätzung | Behalten |
| Kein bezahltes Gespräch, aber mindestens die Hälfte der Einsendungen passend | Vier Wochen verlängern, dann dieselbe Regel |
| Weniger als die Hälfte passend, oder keine Einsendung | Abschalten; die Projektanfrage wird wieder der einzige Button |

Die Abbruchregel unten gilt daneben weiter.

## Was sich ändert, solange der Schalter an ist

- **Startseite `/`**: In der finalen Fassung führen Kopf, Hero, Fall,
  Preisabschluss und Seitenabschluss zur Ersteinschätzung. Im Hero steht
  „Alle Preise“ daneben; Projektanfragen stehen in den Angeboten und als
  zweiter Einstieg im Abschluss. Kanon-Schalter und Texte bleiben zentral.
  Schalter aus: Projektanfrage in Kopf, Hero und Fall; die zusätzliche Zeile
  bei den Preisen und die Ersteinschätzungs-Karte im Abschluss entfallen.
- **`/kontakt/?focus=ersteinschaetzung`**: Kurzformular mit drei Feldern
  (seit 2026-10-03): Website-URL (Pflicht), „Was soll die Seite erreichen?“
  (ein Satz, optional) und E-Mail, dazu die Datenschutz-Checkbox. H1 „Welche
  Website soll ich mir ansehen?“, Button „Drei Befunde anfordern“, darunter die
  Antwortzeit aus dem Kanon. Kein Name, kein Themenschritt. Das Anliegen bleibt
  `request_type=ersteinschaetzung`. Der Server zählt jede Einsendung zusätzlich
  als `anfrage_gesendet` mit `form=ersteinschaetzung` (Admin-Seite
  „Anfrage-Eingänge“); die Betreff-Zählung bleibt daneben bestehen.
- **Mails**: Die interne Benachrichtigung und die Bestätigung tragen im
  Betreff das Präfix `[Ersteinschätzung]`.

Die finale Homepage-Fassung aktualisiert Title, Meta und das gemeinsame
FAQ-Array. `/kontakt/` behält ihre SEO- und Schema-Verträge. `/kontakt/` ohne Parameter und alle anderen Seiten verhalten sich
wie vorher.

### Zusätzlicher Einstieg: Beitrag `/website-relaunch/` (seit 2026-09-24)

Die Abschluss-Tafel des Beitrags (`[hu_abschluss variante="ersteinschaetzung"]`,
`inc/editorial-bausteine.php`) führt mit derselben URL, demselben Button-Text
und denselben Kanon-Texten in die Ersteinschätzung. Daneben steht
„Relaunch-Projekt anfragen“ (`/kontakt/?type=project&focus=relaunch`). Hooks:
`blog_relaunch_close_ersteinschaetzung` und `blog_relaunch_close_project`.
Ist der Schalter aus, zeigt die Tafel nur die Projektanfrage.

- **Zählung:** unverändert über das Betreff-Präfix. Einsendungen aus dem
  Beitrag laufen in dieselbe Summe wie die der Startseite.
- **Getrennt auswerten:** im CRM über die Herkunftsfelder der Einsendung.
  Ohne Einwilligung kennt der Kontaktablauf nur die interne Vorseite
  („Vorige Seite“, `_nexus_contact_previous_page_url` = `/website-relaunch/`),
  mit Einwilligung zusätzlich die Einstiegsseite („Einstiegsseite“,
  `_nexus_contact_entry_page_url`). Eine eigene Messung für den Beitrag gibt
  es nicht.
- Der Beitrag verlängert die Laufzeit nicht. Endet der Versuch, gilt für die
  Tafel dieselbe Entscheidung wie für die Startseite.

### Preiskorrektur am 2026-09-26

Der Website-Preis auf der Startseite sinkt. Statt eines Einstiegspreises ohne
Seitenangabe gilt Website Kompakt: Festpreis für bis zu drei Seiten, jede
weitere Seite zum Zusatzpreis (Werte im Kanon, Herleitung und alter Betrag in
`docs/decisions/preise-website-landingpage.md`). Das ist
eine Preiskorrektur für alle Wege und nach der Regel oben erlaubt. Ein
niedrigerer Preis kann die Zahl direkter Projektanfragen erhöhen. Bei der
Auswertung Anfragen vor und nach dem 2026-09-26 getrennt ansehen.

### Umbau der Startseite am 2026-10-01

Der Betreiber hat den Freeze für Reihenfolge und Leistungen aufgehoben. Geändert
ab Deploy: Der Prüfstand wandert von Position 02 hinter die Arbeiten und wird ein
schmaler heller Streifen statt einer dunklen Tafel. Die drei Leistungskarten
werden fünf Zeilen (neu: Landingpage, Conversion-Optimierung); Zeilen mit
Produktseite bekommen neben der Anfrage den Sub-CTA „Was drinsteckt“. Hero und
Abschluss, also die beiden Buttons des Versuchs, bleiben unverändert.

Die Änderung ging in zwei Deploys live. **Trennpunkt für die Auswertung ist der
erste Deploy: 2026-10-01, 23:16 Uhr MESZ (21:16 UTC), Pull Request 511.** Er
brachte die neue Reihenfolge (Prüfstand hinter den Arbeiten) und die fünf Zeilen.
Pull Request 513 folgte danach mit dem schmalen Prüfstand-Streifen, dem Link der
Website-Zeile auf die neue Produktseite und den eindeutigen Hook-Namen; er ändert
nichts mehr an der Reihenfolge. Einsendungen vor und nach dem ersten Deploy
getrennt ansehen. Die Entscheidungsregel gilt unverändert; die Zählung danach
läuft auf einer anderen Seitenstruktur und ist mit der davor nur eingeschränkt
vergleichbar. Ein längerer Weg bis zu den Preisen oder neue
Ausgänge zu Produktseiten können Anfragen vom Hero-Button wegziehen.

### Hero als Messfläche (Auftrag 2026-10-02)

Die nächste Änderung betrifft nur den Kopf und Abschnitt 01. Hero und Kopf
bilden ein dunkles Messinstrument. Ein Herkunftsetikett und der Besuch als
Signal führen auf einer Bahn von Klick über Seite bis Formular. Die sechs
Stationslinks öffnen die bestehenden Akkordeons in Abschnitt 02.

**Hypothese:** mehr Ersteinschätzungs-Klicks aus dem ersten Bildschirm und mehr
Stationsöffnungen. Sechs Wochen ab dem tatsächlichen Livegang auswerten:

- `home_head_ersteinschaetzung`, `data-track-section=hero`: Anteil der
  Hero-Klicks an Startseitenaufrufen, verglichen mit den sechs Wochen davor.
- `home_hero_station`, `data-track-label={slug}`: Anteil der Aufrufe mit
  mindestens einer Stationsöffnung und Verteilung auf die sechs Slugs.
  Dieser Hook ist neu; vor dem Release gibt es dafür keine vergleichbare
  Ereignisreihe. Erst ab Livegang eine Baseline bilden.
- Herkunftsmix und passende Einsendungen daneben betrachten. Mehr Klicks
  allein belegen keinen zusätzlichen Umsatz; Vorher/Nachher ist bei
  verändertem Traffic kein kausaler Nachweis.

Livegang und Ende der Beobachtung werden nach dem erfolgreichen Deploy
notiert. Die `data-track-*`-Hooks sind vorhanden; die lokale Messfläche
sendet keine Ereignisse. Vor der Auswertung prüfen, ob das bestehende
Tracking die beiden Hooks erfasst. Keine neue Analytics im Messmodul.

## Wo was steht

| Was | Wo |
| --- | --- |
| Schalter `HU_EXPERIMENT_ERSTEINSCHAETZUNG` | `blocksy-child/inc/canon/messaging-canon.php` |
| Alle Texte, Kennung `ersteinschaetzung`, Betreff-Präfix | `hu_first_assessment_text()` im selben Abschnitt |
| URL `/kontakt/?focus=ersteinschaetzung` | `hu_first_assessment_url()` |
| Antwortzeit | `hu_response_promise()`, keine eigene Fassung |
| Buttons Startseite | `blocksy-child/front-page.php`, Stil in `assets/css/system.css` (`.tun`), Anordnung in `assets/css/startseite-strecke.css` |
| Abschluss-Tafel im Beitrag | `[hu_abschluss]` in `blocksy-child/inc/editorial-bausteine.php`, Intro ohne Antwortzeit `hu_first_assessment_text( 'intro_short' )` |
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

Ist der Schalter aus, zeigt die finale Startseite Projektanfragen und
`/kontakt/` verhält sich wie vor dem Versuch; `?focus=ersteinschaetzung` fällt auf die normale Projektanfrage
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

## Beobachtung der finalen Fassung

Repo-Umsetzung: 2026-10-02. Livegang und Beginn der Beobachtung sind noch offen.
Hypothese: Fall vor Preis und die Herkunftszeile erhöhen den Anteil der
Ersteinschätzungen über den Fall-CTA. Nach sechs Wochen ab Livegang werden
`home_case_ersteinschaetzung` und `home_head_ersteinschaetzung` sowie die
Qualität der daraus entstandenen Einsendungen verglichen. Der Kopf trägt
separat `nav_header_ersteinschaetzung`; Preise und Abschluss tragen eigene
Hooks. Kein neuer Analytics-Code, Speicher oder Netzaufruf wird ergänzt.
Die Herkunft bleibt lokal und wird nicht als CRM- oder Kampagnendatum ausgegeben.

Die ursprüngliche Entscheidung über die Ersteinschätzung bleibt bestehen;
vorherige und spätere Phase werden separat berichtet. Keine Ergebnisse oder
Einsendungszahlen aus der Umsetzung ableiten.

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
