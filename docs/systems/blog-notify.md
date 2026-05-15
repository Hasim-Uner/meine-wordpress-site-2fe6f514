# Blog Notify

Stand: 2026-05-15.

Diese Doku beschreibt das repo-seitige System fuer `Neue Artikel per E-Mail`.

Wichtig:

- Es ist bewusst kein klassischer Marketing-Newsletter.
- WordPress ist Source of Truth fuer Kontakte, Consent, Segmente und Status.
- Brevo bleibt reine Zustellungs- und Transportebene ueber den zentralen Mail-Layer.

## Zweck

Das System sammelt nur E-Mail-Adressen von Personen, die bei neuen Blogartikeln kurz informiert werden moechten.

Ziel:

- niedrige Reibung
- sauberes Double-Opt-in
- sofort wirksame Abmeldung
- klare CRM-Zuordnung im WordPress-Backend
- spaetere Erweiterbarkeit fuer manuelle oder automatische Artikel-Benachrichtigungen

## Aktiver Repo-Flow

1. Besucher sieht auf Blog-Home, Kategorie-/Archivseiten oder unter einzelnen Artikeln das Formular `Neue Artikel per E-Mail`.
2. `blocksy-child/assets/js/blog-notify.js` sendet `POST /wp-json/nexus/v1/blog-subscribe`.
3. `blocksy-child/inc/blog-notify.php` prueft Honeypot, Rate Limit, Nonce und E-Mail-Adresse.
4. WordPress speichert zuerst nur eine interne DOI-Intent-Notiz im versteckten CPT `nexus_blog_notify`.
5. Eine DOI-Mail wird ueber `wp_mail` und den zentralen Brevo-Mail-Layer versendet.
6. Erst beim Klick auf den Bestaetigungslink wird ein Kontakt im CPT `nexus_contact` angelegt oder ein bestehender CRM-Kontakt fuer `blog_notify` aktualisiert.
7. Der Klick auf den Bestaetigungslink aktiviert das Abo ueber die Route `/neue-artikel-per-email/?action=confirm&token=...`.
8. Der Klick auf den Abmeldelink entzieht das Abo sofort, egal ob die Anmeldung noch pending oder bereits bestaetigt war.

## Datenmodell

Pending vor DOI:

- verstecktes CPT `nexus_blog_notify`
- `_nexus_blog_notify_email`
- `_nexus_blog_notify_status` mit `pending|confirmed|unsubscribed`
- `_nexus_blog_notify_confirm_token`
- `_nexus_blog_notify_unsubscribe_token`
- `_nexus_blog_notify_requested_at`
- `_nexus_blog_notify_confirmed_at`
- `_nexus_blog_notify_unsubscribed_at`
- `_nexus_blog_notify_context_post_id`
- `_nexus_blog_notify_contact_id`

Primaerer CRM-Datensatz nach DOI:

- CPT `nexus_contact`

Wichtige Meta-Felder fuer Blog-Abos:

- `_nexus_contact_email`
- `_nexus_contact_source=blog_subscriber` beim Erstkontakt ueber dieses System
- `_nexus_contact_latest_source`
- `_nexus_contact_segments[]` mit `blog_notify`
- `_nexus_contact_segment_blog_notify=1`
- `_nexus_contact_blog_status` mit `active|unsubscribed`
- `_nexus_contact_consent_blog_email` mit `confirmed|revoked`
- `_nexus_contact_blog_confirm_requested_at`
- `_nexus_contact_double_opt_in_confirmed_at`
- `_nexus_contact_unsubscribe_token`
- `_nexus_contact_unsubscribed_at`
- `_nexus_contact_created_at`
- `_nexus_contact_updated_at`

## Admin und CRM

Das bisherige Audit-CRM ist repo-seitig zu `Nexus CRM` erweitert.

Darin sichtbar:

- Audit-Anfragen als `nexus_review_request`
- CRM-Kontakte als `nexus_contact`
- gefilterte Einstiegspunkte fuer `Blog-Abos` und `Projektanfragen`
- `DOI ausstehend` zaehlt nur noch interne Pending-Intents und keine unbestaetigten CRM-Leads

## Versandlogik fuer Artikel

Version 1 nutzt keinen vollautomatischen Publish-Blast.

Stattdessen:

- jeder Beitrag hat im Editor eine Meta-Box `Neue Artikel per E-Mail`
- der Versand wird manuell pro Artikel gestartet
- aktive Empfaenger werden als feste Snapshot-Liste gespeichert
- die Verarbeitung laeuft in kleinen Batches ueber einen einfachen Queue-/Cron-Mechanismus
- pro Artikel wird ein erneuter Versand in V1 bewusst blockiert

Warum diese Entscheidung:

- geringeres Risiko versehentlicher Sends
- saubere Kontrolle im Content-Workflow
- leichter spaeter zu automatisieren

## Repo-Touchpoints

- `blocksy-child/inc/crm.php`
- `blocksy-child/inc/blog-notify.php`
- `blocksy-child/template-parts/blog-notify.php`
- `blocksy-child/page-blog-notify.php`
- `blocksy-child/assets/js/blog-notify.js`
- `blocksy-child/assets/css/blog-notify.css`
- `blocksy-child/single.php`
- `blocksy-child/home.php`
- `blocksy-child/category.php`
- `blocksy-child/archive.php`
- `blocksy-child/inc/enqueue.php`

## Risiken

- der initiale Subscribe-Request wurde live am 2026-05-15 verifiziert; DOI-Bestaetigung und Artikelversand muessen weiter separat im Postfach bzw. Admin-Workflow geprueft werden
- der Queue-Ansatz ist bewusst leichtgewichtig und fuer spaeteren Ausbau gedacht, nicht fuer sehr grosse Listen
- bei identischen E-Mail-Adressen wird ein Kontakt CRM-seitig aktualisiert statt vervielfacht

## Naechste sinnvolle Schritte

1. einen End-to-End-Test fuer DOI, Bestaetigung und Abmeldung live durchfuehren
2. einen Testversand aus der Artikel-Meta-Box mit einer kleinen Empfaengerzahl pruefen
3. fuer Phase 2 entscheiden, ob Artikel-Benachrichtigungen automatisch beim Publish oder weiter manuell ausgeloest werden sollen
