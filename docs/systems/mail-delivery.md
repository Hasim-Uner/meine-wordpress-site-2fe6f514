# Mail Delivery

Stand: 2026-03-13.

Diese Doku beschreibt den versionierten Mail-Layer fuer die Website.

## Ziel

- alle Formularbestaetigungen
- interne Benachrichtigungen aus Kontakt- und Audit-Flow
- sonstige Transaktionsmails aus dem Theme

sollen ueber Brevo zugestellt werden.

## Repo-Layer

- `blocksy-child/inc/mail.php`
- `blocksy-child/inc/review-crm.php`
- `blocksy-child/inc/contact-page.php`

Die eigentlichen Fachmails werden weiter ueber `wp_mail` ausgelost.

Der Unterschied:

- `blocksy-child/inc/mail.php` haengt sich global an `pre_wp_mail`
- sobald ein gueltiger Brevo-API-Key vorhanden ist, werden `wp_mail`-Aufrufe ueber die Brevo API an `https://api.brevo.com/v3/smtp/email` zugestellt
- wenn kein API-Key vorhanden ist, kann optional weiter der Brevo-SMTP-Fallback genutzt werden
- Absendername und Absenderadresse koennen zentral gesetzt werden

Dadurch muessen Kontaktseite, Audit-Intake und weitere Theme-Mails keinen eigenen SMTP-Code duplizieren.

## Aktivierung

Secrets liegen bewusst nicht im Repo.

Der Mailer liest zuerst Konstanten, dann Umgebungsvariablen.

Relevante Schluessel:

- `NEXUS_BREVO_API_KEY`
- `NEXUS_BREVO_API_ENABLED` optional
- `NEXUS_BREVO_API_ENDPOINT` optional, Standard `https://api.brevo.com/v3/smtp/email`
- `NEXUS_BREVO_API_TIMEOUT` optional, Standard `15`
- `NEXUS_BREVO_API_FALLBACK_TO_WP_MAIL` optional, Standard `false`
- `NEXUS_BREVO_FROM_EMAIL` optional, sonst `admin_email`
- `NEXUS_BREVO_FROM_NAME` optional, sonst `bloginfo('name')`
- `NEXUS_BREVO_SMTP_USERNAME` optionaler Fallback
- `NEXUS_BREVO_SMTP_PASSWORD` optionaler Fallback
- `NEXUS_BREVO_SMTP_HOST` optionaler Fallback, Standard `smtp-relay.brevo.com`
- `NEXUS_BREVO_SMTP_PORT` optionaler Fallback, Standard `587`
- `NEXUS_BREVO_SMTP_ENCRYPTION` optionaler Fallback, Standard `auto`
- `NEXUS_BREVO_SMTP_ENABLED` optionaler Fallback

Es werden ausserdem gaengige Alias-Namen wie `BREVO_API_KEY`, `SENDINBLUE_API_KEY`, `BREVO_SMTP_USER`, `BREVO_SMTP_KEY`, `SMTP_HOST` oder `SMTP_FROM_EMAIL` akzeptiert, falls die Live-Umgebung diese bereits nutzt.

## Verhalten

- wenn ein API-Key vorhanden ist, routed der Mailer `wp_mail` global ueber Brevo
- Reply-To, CC, BCC, HTML-Inhalte und Anhaenge werden fuer die API mitgenommen
- explizite `multipart/*`-Mails koennen auf den Fallback zurueckfallen, statt unsauber in die API zu laufen
- wenn kein API-Key vorhanden ist, kann SMTP als Fallback genutzt werden
- wenn weder API noch SMTP konfiguriert sind, bleibt WordPress beim Standard-Mailpfad

## Betriebsrisiken

- ohne gueltige Live-Credentials ist Brevo repo-seitig vorbereitet, aber nicht aktiv
- wenn die Absenderadresse in Brevo nicht als Sender/Domain freigegeben ist, koennen Zustellfehler entstehen
- wenn der Brevo-API-Key auf autorisierte IPs begrenzt ist, muss die Server-IP in Brevo freigegeben werden
- bei API-Fehlern endet der Versand standardmaessig mit Fehler statt still auf einen anderen Mailweg zu kippen
- wenn stattdessen der SMTP-Fallback genutzt wird und Port `587` blockiert, sollte `2525` oder `465` geprueft werden

## Nach Deployment pruefen

1. Kontaktformular absenden
2. Audit-Anfrage absenden
3. Eingangs- und Bestaetigungsmail pruefen
4. Reply-To und Absenderadresse pruefen
5. Brevo-Log und WordPress-Fehlerlog einmal gegenchecken

## Diagnose

Fuer Admins gibt es zusaetzlich den Endpoint:

- `/wp-json/nexus/v1/mail-diagnostics`

Er zeigt live:

- ob der Brevo-API-Key im Runtime-Kontext vorhanden ist
- welche Absenderadresse aktiv ist
- welcher Transport aktiv ist
- was der letzte Mailversuch als Status oder Fehler geliefert hat

Fuer schnelle Live-Pruefung ohne Admin-Nonce gibt es ausserdem:

- `/wp-json/nexus/v1/mail-diagnostics-public`

Dieser Endpoint liefert nur redigierte Werte, reicht aber fuer die Fehlersuche an API-Key, Provider und letztem Mailstatus (Status- und Fehlercode). Den Fehlertext des Providers zeigt seit 2026-09-22 nur noch der Admin-Endpoint, weil er Empfaengeradressen oder interne Details enthalten kann.
