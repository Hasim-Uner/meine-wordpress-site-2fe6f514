# Privacy

Stand: 2026-09-22. Technische Sicht auf die Datenverarbeitung im Theme. Die
rechtliche Fassung für Besucher steht in `blocksy-child/page-datenschutz.php`;
Änderungen an Formularen, Speicherung oder Drittanbietern müssen dort
nachgezogen werden.

## Grundsatz

- Keine Cookies bei öffentlichen Besuchen, kein Cookie-Banner. Das Theme bindet
  kein GTM, GA4, Ads-Conversion-Tracking oder Pixel ein.
- Anfrage-Herkunft wird nur im `sessionStorage` des Tabs gehalten und erst mit
  einer abgesendeten Anfrage gespeichert.
- Koko Analytics läuft als Plugin (admin-owned). Konfiguration und
  Speicherumfang sind aus dem Repo nicht prüfbar.

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

Vertriebsrelevante Kontakte bekommen eine Sales-Chance (`nexus_opportunity`)
und einen Aktivitätsverlauf (`nexus_crm_activity`). Telefonnummern sind nur
dort Feld, wo das Formular sie ausdrücklich abfragt.

## Herkunft

`NexusCore` (`assets/js/nexus-core.js`) hält pro Browser-Tab im
`sessionStorage`: erste und letzte interne URL, Kampagnenquelle und
Suchbegriff (`utm_source`, `utm_term`), `utm_medium`, `utm_campaign` und die
verweisende Seite des Sitzungsstarts (nur Origin und Pfad). Beim Absenden
landen diese Werte im Payload; `nexus_sanitize_inquiry_attribution()` in
`inc/crm.php` begrenzt und bereinigt sie. Dazu kommt die freiwillige Angabe,
wie jemand aufmerksam wurde (`referral_source`).

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

- Die Sticky-CTA der Solar-Unterseiten merkt ihr Wegklicken 24 Stunden im
  `localStorage`. Die Datenschutzerklärung schließt persistente
  Browser-Speicherung für Komfortzwecke aus; eines von beiden anpassen.
- Die Datenschutzerklärung nennt keine Analyse-Skripte; ob und wie Koko
  Analytics dort genannt werden muss, ist rechtlich zu klären.
- n8n ist nicht angebunden. Wird es später aktiviert, braucht es vorher
  Payload-Contract, Retention-Regel und Auftragsverarbeitung.
