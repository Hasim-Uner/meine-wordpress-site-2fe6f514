# Privacy

Stand: 2026-10-29. Technische Sicht auf die Datenverarbeitung im Theme. Die
rechtliche Fassung für Besucher steht in `blocksy-child/page-datenschutz.php`;
Änderungen an Formularen, Speicherung oder Drittanbietern müssen dort
nachgezogen werden.

## Grundsatz

- Öffentliche Besuche setzen keine Cookies und schreiben nichts in
  `localStorage` oder `sessionStorage`; deshalb kein Cookie-Banner. Das Theme
  bindet kein GTM, GA4, Ads-Conversion-Tracking oder Pixel ein. Live geprüft
  am 2026-09-23 (Header, Cookies, Web-Speicher, Fremd-Hosts); nach dem Merge
  dieses Stands erneut prüfen.
- Speicher im Browser braucht nach § 25 TDDDG eine Einwilligung, sofern er
  nicht unbedingt erforderlich ist, egal ob Cookie oder Web-Speicher. Neue
  Funktionen mit Browser-Speicher deshalb nur mit Consent-Weg planen.
- Formular-Herkunft wird ohne Einwilligung erst beim Absenden aus der
  Formularseite gelesen (siehe Herkunft). Davon getrennt zählt der Server
  Kampagnenaufrufe mit `utm_source` in der URL als `besuch` (siehe unten).
- Koko Analytics läuft als Plugin (admin-owned), live mit Tracking-Methode
  `fingerprint` (`"method":"fingerprint","use_cookie":false`, v2.5.3). Das
  Inline-Skript liest User-Agent (Bot-Filter), externen Referrer und
  utm-Parameter und sendet per `sendBeacon` an die eigene Domain. Laut
  Hersteller bildet der Server einen Tages-Hash aus IP, User-Agent und
  rotierendem Geheimwert und speichert IP und User-Agent nicht. Die
  Datenschutzerklärung beschreibt genau diesen Modus; wer im Admin auf
  `cookie` umstellt, muss sie ändern und braucht eine Einwilligung.
- Das WordPress-Emoji-Skript ist im Frontend abgeschaltet
  (`hu_disable_frontend_emoji_detection()` in `inc/enqueue.php`): Es schrieb
  `wpEmojiSettingsSupports` in den `sessionStorage` und kann Bilder von
  `s.w.org` nachladen. Nach WordPress-Updates prüfen, dass `_wpemojiSettings`
  nicht wieder im HTML steht.

## Anonyme Solar-Tageszähler

`assets/js/solar-events.js` zählt nur auf der Solar-Money-Page CTA-Klicks und
Formularereignisse für `marktcheck`, `analyse` und `sofortkontakt`. Öffnen zählt
bei erster Aktivierung eines Tür-Links, erster Formularinteraktion oder einem
direkten Tür-Anker; Schritt 2 zählt einmal je Seitenladung, nur beim Marktcheck.
„Abgesendet“ zählt nach erfolgreicher REST-Annahme. Allgemeine Inhaltsklicks
haben eine leere Tür. Übertragen werden exakt `event`, `door`, `page`, `day`
(UTC), ohne Query, Fragment, Referrer, Formularwerte, Cookies oder Kennung.
`fetch` verwendet `credentials: omit` und `referrerPolicy: no-referrer`.

`inc/solar-events.php` akzeptiert ausschließlich feste Ereignisnamen und den
kanonischen Seitenpfad. Es speichert atomare Zähler pro Tag/Ereignis/Tür/Seite
in `<prefix>nexus_solar_events`, ohne Einzelereignisse oder Besucherverknüpfung.
Ein gemeinsames Minutenlimit in kurzlebigen Transients enthält nur eine Anzahl,
keine IP oder Besucherkennung. Die bestehende API-Fehlertelemetrie nimmt diesen
Endpunkt vollständig aus, damit auch abgewiesene Ereignisse keine IP-Hashes
oder Trace-IDs bekommen. Lesen per GET auf `nexus/v1/solar-events` erfordert
`manage_options` und liefert die letzten 31 Tage. Die bestehenden Formular-
Payloads, Einwilligungen, CRM-Einträge und Koko-Statistik bleiben getrennt.

Die Besucherinformation steht in `page-datenschutz.php`. Vor dem Merge des
zugehörigen PRs ist Hasims Bestätigung zur cookiefreien Linie erforderlich.

## Anfrage-Monitor

`inc/anfrage-monitor.php` lässt täglich eine Testanfrage mit eigener Absenderadresse
durch `/kontakt/` laufen. Sie trägt den Marker `monitor_token`/`monitor_run_id`,
landet als archivierter `nexus_contact` ohne Verkaufschance und schreibt im
Protokoll das Ereignis `monitor_test`. Gespeichert werden je Lauf nur Lauf-ID,
Zeitpunkt, Prüfergebnisse und Dauern (Optionen `hu_monitor_runs`, 90 Läufe, und
`hu_monitor_intake`, 20 Läufe). `GET /nexus/v1/monitor/status` und
`/anfrage-monitor/` zeigen Ampeln, Verlauf und die Tage seit der letzten echten
Anfrage, keine Personendaten.

Den Lauf führt ein geplanter GitHub-Actions-Workflow aus
(`scripts/monitor/anfrage_monitor.py`). Der Runner liest im Postfach nur die
Betreffzeilen von Mails, deren Betreff `MONITOR <Lauf-ID>` enthält, und das
ausschließlich lesend (IMAP `EXAMINE`, `BODY.PEEK`): Er markiert nichts als
gelesen, verschiebt und löscht nichts. Mail-Inhalte und Zugangsdaten gelangen
nicht ins Log. Das IMAP-Passwort und das Monitor-Geheimnis liegen als
GitHub-Secrets; der Job läuft nur auf `main`.

## Anfrage- und Besuchsprotokoll

`inc/inquiry-events.php` schreibt je erfolgreich verarbeiteter Anfrage eine
Zeile in `<prefix>hu_inquiry_events`: `event` (`anfrage_gesendet`), `form`
(`kontakt`, `ersteinschaetzung`, `whitelabel`, `marktcheck`, `analyse`,
`sofortkontakt`, `sst`), `seite` (Pfad der absendenden Seite, ohne Query und
Fragment), `utm_source`, `utm_campaign` und `created_at` (UTC). Der Aufruf
steht in den REST-Handlern von `contact-request`, `whitelabel-request` und
`audit-request`, nach CRM-Schreibvorgang und Benachrichtigung; Honeypot-Treffer,
Validierungsfehler, Rate-Limit und ein Fehlschlag von CRM und Mail erzeugen
keine Zeile. Der Browser sendet dafür nichts.

Nicht gespeichert werden IP, User-Agent, E-Mail, Name, Nachricht, Query-String,
Referrer-Host oder eine Besucher-/Sitzungskennung. `utm_source` und
`utm_campaign` werden auf `a-z 0-9 . _ -` und 64 beziehungsweise 96 Zeichen
reduziert. Es entsteht weder Cookie noch Browser-Speicher; deshalb kein Banner.
Ein Schreibfehler bricht nie eine Anfrage ab. Die Zeilen sind einzelne
Ereignisse ohne Personenbezug; eine automatische Löschung gibt es nicht. Die
Admin-Seite „Anfrage-Eingänge“ (unter dem CRM-Menü, `manage_options`) zeigt die
letzten 90 Tage als Anzahl je Formular und `utm_source`.

### Kampagnenaufrufe `besuch`

Die gleiche Datei schreibt über `template_redirect` zusätzlich `event=besuch`
mit leerem `form`, aktuellem Seitenpfad ohne Query/Fragment, `utm_source`,
`utm_campaign` und UTC-Zeitpunkt in dieselbe Tabelle. Die bestehenden
Token-Grenzen gelten unverändert. Nur Frontend-GETs mit nicht leerem,
bereinigtem `utm_source` und explizitem `Accept: text/html` zählen. Admin,
REST, Feed, Preview und Nutzer mit `manage_options` sind ausgeschlossen.
Der User-Agent wird nur flüchtig gegen
`bot|crawl|spider|preview|fetch|scan|monitor|curl|python|headless` geprüft,
ergänzt um `googleimageproxy|safelinks`, nicht gespeichert. `HEAD` und
Anfragen ohne HTML-Accept zählen nicht.
Die Filter reduzieren Mail-Link-Scanner; Scanner mit normalen Browser-Headern
lassen sich damit nicht sicher erkennen.

Es gibt keinen zusätzlichen Browser-Request, Cookie, Browser-Speicher,
IP-Hash, Referrer oder Besucher-/Sitzungskennung. Es zählen Aufrufe, keine
Personen oder eindeutigen Klicks; Neuladen zählt erneut. Der Anfrage-Monitor
wird für `besuch` nicht aufgerufen. Aufbewahrung wie bei den Anfragezeilen:
keine automatische Löschung, Auswertung der letzten 90 Tage.

Die Admin-Seite zeigt oberhalb der unveränderten Formularübersicht eine
Gruppierung nach `utm_source × utm_campaign`: Besuche, Anfragen und
`Anfragen/Besuche` als Prozentwert. Bei null Besuchen steht „–“. Es werden
nur `besuch` und `anfrage_gesendet` einbezogen, keine Monitor-Testanfragen.
Das Verhältnis verknüpft keine Besucher mit Anfragen und kann über 100 %
liegen; historische Anfragen haben keine rückwirkend gezählten Besuche.
Die Besucherinformation steht in `page-datenschutz.php`.

**Betriebsvoraussetzung:** Der vorgeschaltete Host-/Varnish-Cache muss URLs
mit `utm_source` bis PHP durchlassen. Das Theme setzt für gezählte Aufrufe
`DONOTCACHEPAGE` und `nocache_headers()` auch für gefilterte Scanner-GETs,
kann aber einen Cache-Treffer vor
WordPress nicht verhindern. Live am 29.10.2026 vor diesem Change geprüft:
Die Beispiel-URL `/whitelabel-retainer/?utm_source=akquise&utm_campaign=kaltakquise`
lieferte `x-varnish-cache: HIT`, `age: 37` und
`x-cacheable: YES:QUERY-STRING:UTM-PARAMETERS`. Vor produktiver Auswertung
im Hosting alle Requests mit `utm_source` vom Seitencache ausnehmen und
bestehende Einträge leeren; danach zwei normale Browseraufrufe und die
Datenbank prüfen. Ohne diese Ausnahme wird die Besuchszahl unvollständig.

## Terminbuchung über Cal.com

`assets/js/cal-embed.js` bindet ausschließlich die Klick-Aktivierung der
Buchungslinks. Erst ein abgeschlossener, nicht modifizierter Klick lädt das
externe Embed-Skript und öffnet den Kalender; Hover, Fokus, `pointerdown` und
eine abgebrochene Zeigeraktion lösen kein Vorladen aus. Tastatur-Aktivierung
funktioniert über denselben Klick-Handler. Ohne JavaScript, bei modifizierten
Klicks und bei einem Ladefehler bleibt der normale Buchungslink erhalten.

Beim Öffnen gibt das Embed nur feste Herkunftskürzel (`metadata[quelle]`) und
den aktuellen Seitenpfad ohne Query/Fragment (`metadata[seite]`, maximal
200 Zeichen) mit. Der geladene externe Dienst erhält Verbindungsdaten.
Cal.com verarbeitet die vom Gast eingegebenen Buchungsdaten in den USA;
bei Wahl des Rückrufs gehört die Rückrufnummer dazu. Google Kalender wird
für Verfügbarkeit und Kalendereinträge genutzt, Google Meet nur bei Wahl
dieser Gesprächsart. Der öffentliche Abschnitt `#terminbuchung` beschreibt
diese Datenkategorien und verlinkt beide Anbieterinformationen. Die Aussagen
zum cookiefreien normalen Seitenaufruf gelten vor dem Öffnen des Kalenders.

`inc/calcom-webhook.php` prüft Buchungs-/Verschiebungs-/Absageereignisse mit
HMAC-SHA256. Ohne konfiguriertes Geheimnis wird die Route nicht registriert.
Er speichert Kontakt- und Terminangaben, Herkunft, optionale Notiz und
Absagegrund im CRM und verschickt die interne Benachrichtigung über Brevo;
die Rückrufnummer wird im aktuellen Normalisierer nicht ins CRM übernommen.
Eine aktive Webhook-Konfiguration ist außerhalb des Repos zu prüfen.

Der Regressionstest `scripts/tests/cal-embed.spec.cjs` prüft die Netzgrenze
vor einer Aktivierung, Öffnung per Maus und Tastatur sowie den Link-Fallback
bei nicht verfügbarem externem Skript. Alle Anbieterantworten sind gemockt.

## Formulare und Speicherung

Alle Formulare senden per `fetch()` an eigene REST-Endpunkte unter `nexus/v1`
und speichern im WordPress-Backend. Kein Browser-Submit an Drittanbieter.

| Formular | Endpunkt | Gespeichert | Einwilligung |
| --- | --- | --- | --- |
| `/kontakt/`, Server-Side-Formular | `contact-request` | `nexus_contact` mit Name, E-Mail, Anfrageangaben, Herkunft; Aktivität `inbound_inquiry` mit Anfragetext | Pflicht-Checkbox `consent`, serverseitig geprüft |
| `/whitelabel-retainer/` | `whitelabel-request` | `nexus_contact` (Quelle `whitelabel_request`) mit E-Mail, Aufgabe, Zeitrahmen, Zugängen, Herkunft; Aktivität mit Aufgabentext | Hinweis unter dem Formular (vorvertragliche Anfrage) |
| Marktcheck | `audit-request` | `nexus_review_request` mit Kontaktdaten, Antworten und Herkunft; Spiegelung in `nexus_contact` | Pflicht-Checkbox `consent_privacy` (`accepted`) |
| Solar-Analyse und Sofortkontakt | `audit-request` | `nexus_review_request` mit Kontaktdaten, Anfragequellen und auftragsbezogenen Antworten; Spiegelung in `nexus_contact` | Pflicht-Checkbox `consent_privacy` (`accepted`) |
| Blog-Abo | Blog-Notify-Route | Pending-Eintrag bis zur Bestätigung, danach `nexus_contact` | Double-Opt-in |
| Anfragesystem-Analyse | `analysis-submit` | standardmäßig abgeschaltet (`HU_FEATURE_READINESS_SUBMIT`) | – |

Die Ersteinschätzung (`/kontakt/?focus=ersteinschaetzung`, Versuch laut
`docs/experimente/ersteinschaetzung.md`) ist dasselbe Formular am selben
Endpunkt mit derselben Pflicht-Checkbox. Sie ist ein Kurzformular mit drei
Feldern: Website-URL (Pflicht), Ziel als optionaler Satz im Feld `message` und
E-Mail. Einen Namen fragt sie nicht; der Server nimmt dann die Domain der URL
als Anzeigenamen (Betreff, CRM-Titel) und überschreibt keinen bekannten
Kontaktnamen. Neue Datenkategorien, Cookies oder Skripte kommen nicht hinzu; die
Datenschutzerklärung deckt sie mit den Anfragen aus den Formularen ab.

Vertriebsrelevante Kontakte bekommen eine Sales-Chance (`nexus_opportunity`)
und einen Aktivitätsverlauf (`nexus_crm_activity`). Telefonnummern sind nur
dort Feld, wo das Formular sie ausdrücklich abfragt.

## Herkunft

`NexusCore` (`assets/js/nexus-core.js`) liest ohne Einwilligung erst beim
Absenden, was die Formularseite mitbringt: ihre Adresse (`landing_page_url`),
utm-Parameter und Klick-IDs aus ihrer URL (`utm_source`, `utm_term`,
`utm_medium`, `utm_campaign`, `gclid`/`fbclid` als Quelle) und
`document.referrer` (nur Origin und Pfad). Eine interne Vorseite landet in
`previous_internal_url`; ohne interne Vorseite gilt die Formularseite als
Einstieg (`entry_page_url`), sonst bleibt der Einstieg leer. Das SEO-Cockpit
fällt dann auf Vorseite und Formularseite zurück.

Die Sitzungs-Herkunft über mehrere Seiten (erster Einstieg, Kampagne vom
ersten Aufruf) steht im `sessionStorage` und ist nur aktiv, wenn
`window.huConsent = { attribution: true }` gesetzt ist. Ein Consent-Tool setzt
das Flag und ruft danach `NexusCore.initLeadAttributionSession()` auf. Heute
gibt es kein Consent-Tool; der Weg ist vorbereitet, nicht aktiv.

`nexus_sanitize_inquiry_attribution()` in `inc/crm.php` begrenzt und bereinigt
die Werte. Dazu kommt die freiwillige Angabe, wie jemand aufmerksam wurde
(`referral_source`).

## Beitrags-Feedback

`inc/post-rating.php`: „Gefällt mir“ und Ja/Nein zählen in Post-Meta. Ein
freiwilliger Text wird mit Datum und `ip_hash` (SHA-256 aus IP und
`wp_salt( 'auth' )`, 16 Zeichen) gespeichert, höchstens 25 je Beitrag.
Rate-Limit: sechs Abgaben je zehn Minuten pro IP-Hash und Beitrag
(Transient). Im Browser wird nichts gespeichert; nach einem Neuladen ist der
Knopf wieder aktiv, das Rate-Limit begrenzt Mehrfachklicks. Die Sticky-CTA
der Unterseiten merkt ihr Wegklicken nur für den Seitenaufruf.

## Mail und Protokolle

- Transaktionsmails laufen über `wp_mail` und die Brevo-API
  (Auftragsverarbeitung). Pro Anfrage: interne Benachrichtigung und Bestätigung
  an die angegebene Adresse.
- Scheitert die interne Benachrichtigung oder bei `contact-request` die
  Bestätigung, schreibt das Theme ins PHP-Log nur Fehlertyp, Quelle und
  Kontakt-ID, keine Adressen oder Inhalte
  (`nexus_record_lead_notification_failure()`). Die private CRM-Notiz
  unterscheidet interne Benachrichtigung und Bestätigung. Der Antwortfrist-Wächter
  protokolliert nur Anzahlen.
- `/wp-json/nexus/v1/mail-diagnostics-public` gibt nur redigierte Werte aus,
  keinen Fehlertext des Providers.

## Missbrauchsschutz

Rate-Limits zählen pro IP und Stunde in Transients mit gehashtem Schlüssel
(`md5( IP . Stunde )`), Laufzeit eine Stunde. Die IP liefert
`nexus_get_review_request_ip()`.

Vor jedem CRM-Schreibvorgang prüft `inc/intake-spam-guard.php` jede Absendung:
Zeitsperre (`form_elapsed_ms` aus `nexus-core.js`, Millisekunden seit
Seitenaufruf, unter 3 Sekunden = verworfen) und Inhaltsfilter auf den
Freitextfeldern (kyrillische/ostasiatische Schrift, mehr als ein Link,
Link-Markup, Spam-Begriffe). Verworfen wird still mit Erfolgsantwort. Gezählt
wird in der Option `nexus_intake_spam_stats` nur Tag, Formular und Grund,
30 Tage lang – keine IP, keine Personendaten, kein Text. Übersicht im CRM-Menü
unter „Formular-Spam“. Fehlendes Zeitfeld blockt seit 24.10.2026 standardmäßig
(`NEXUS_SPAM_REQUIRE_TIMING` = `true`), weil Bots direkt an die REST-Endpunkte
posten. Ein Link im Feld „Zeitraum“ gilt immer als Spam.

## Offen

- Rechtlich bestätigen lassen (Anwalt oder Datenschutzbeauftragter): Koko
  im Cookieless-Modus ohne Einwilligung und das Lesen von Referrer und
  utm-Parametern beim Absenden. Beides speichert nichts im Browser; ob schon
  das Auslösen per Skript unter § 25 TDDDG fällt, ist nicht abschließend
  geklärt.
- Nicht aus dem Repo prüfbar: Auftragsverarbeitungsverträge mit Hoster und
  Brevo, Log-Aufbewahrung beim Hoster sowie die für das konkrete Cal.com-
  und Google-Konto geltenden Verträge und Transfergrundlagen. Die Freigabe
  von Dokumenten im Cal.com Trust Center beziehungsweise die Annahme einer
  NDA ist kein Nachweis eines abgeschlossenen AVV. Konkrete DPF-/SCC-
  Zusicherungen werden erst nach Prüfung der geltenden Unterlagen ergänzt.
