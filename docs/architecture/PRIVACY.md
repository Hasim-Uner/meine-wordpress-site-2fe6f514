# Privacy

Stand: 2026-09-23. Technische Sicht auf die Datenverarbeitung im Theme. Die
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
- Anfrage-Herkunft wird ohne Einwilligung erst beim Absenden aus der
  Formularseite gelesen (siehe Herkunft).
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

## Formulare und Speicherung

Alle Formulare senden per `fetch()` an eigene REST-Endpunkte unter `nexus/v1`
und speichern im WordPress-Backend. Kein Browser-Submit an Drittanbieter.

| Formular | Endpunkt | Gespeichert | Einwilligung |
| --- | --- | --- | --- |
| `/kontakt/`, Server-Side-Formular | `contact-request` | `nexus_contact` mit Name, E-Mail, Anfrageangaben, Herkunft; Aktivität `inbound_inquiry` mit Anfragetext | Pflicht-Checkbox `consent`, serverseitig geprüft |
| `/whitelabel-retainer/` | `whitelabel-request` | `nexus_contact` (Quelle `whitelabel_request`) mit E-Mail, Aufgabe, Zeitrahmen, Zugängen, Herkunft; Aktivität mit Aufgabentext | Hinweis unter dem Formular (vorvertragliche Anfrage) |
| Marktcheck | `audit-request` | `nexus_review_request` mit Kontaktdaten, Antworten und Herkunft; Spiegelung in `nexus_contact` | Pflicht-Checkbox `consent_privacy` (`accepted`) |
| Blog-Abo | Blog-Notify-Route | Pending-Eintrag bis zur Bestätigung, danach `nexus_contact` | Double-Opt-in |
| Anfragesystem-Analyse | `analysis-submit` | standardmäßig abgeschaltet (`HU_FEATURE_READINESS_SUBMIT`) | – |

Die Ersteinschätzung (`/kontakt/?focus=ersteinschaetzung`, Versuch laut
`docs/experimente/ersteinschaetzung.md`) ist dasselbe Formular am selben
Endpunkt mit derselben Pflicht-Checkbox. Sie fragt die Website-URL als
Pflichtfeld und das Ziel als optionalen Satz im Feld `message`. Neue
Datenkategorien, Cookies oder Skripte kommen nicht hinzu; die
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
- Scheitert die interne Benachrichtigung, schreibt das Theme ins PHP-Log nur
  Quelle und Kontakt-ID, keine Adressen oder Inhalte
  (`nexus_record_lead_notification_failure()`). Der Antwortfrist-Wächter
  protokolliert nur Anzahlen.
- `/wp-json/nexus/v1/mail-diagnostics-public` gibt nur redigierte Werte aus,
  keinen Fehlertext des Providers.

## Missbrauchsschutz

Rate-Limits zählen pro IP und Stunde in Transients mit gehashtem Schlüssel
(`md5( IP . Stunde )`), Laufzeit eine Stunde. Die IP liefert
`nexus_get_review_request_ip()`.

## Offen

- Rechtlich bestätigen lassen (Anwalt oder Datenschutzbeauftragter): Koko
  im Cookieless-Modus ohne Einwilligung und das Lesen von Referrer und
  utm-Parametern beim Absenden. Beides speichert nichts im Browser; ob schon
  das Auslösen per Skript unter § 25 TDDDG fällt, ist nicht abschließend
  geklärt.
- Nicht aus dem Repo prüfbar: Auftragsverarbeitungsverträge mit Hoster und
  Brevo, Log-Aufbewahrung beim Hoster.
- n8n ist nicht angebunden. Wird es später aktiviert, braucht es vorher
  Payload-Contract, Retention-Regel und Auftragsverarbeitung.
