# Briefing: Anfrage-Monitor, Stufe 1 (WordPress-Teil)

Ablage im Repo: `docs/briefings/anfrage-monitor/stufe-1-wordpress.md`
Primary skill: `wordpress-growth-architecture` · Local context: `blocksy-child/inc/CONTEXT.md`

## Ziel

hasimuener.de prüft sich jeden Tag selbst: Eine echte Testanfrage läuft durch das Kontaktformular, und eine öffentliche Ampel unter `/anfrage-monitor/` zeigt, ob Anfragen ankommen. Die Seite ist der Beweis für das neue Wartungsangebot (`/wordpress-wartung/`, eigener PR später). Dieser PR baut nur den WordPress-Teil. Den täglichen Browser-Test und die Postfach-Prüfung übernimmt n8n auf Hetzner (Stufe 1b, eigenes Briefing).

## Befund im Repo (Ausgangslage)

- Kontaktformular: REST `POST /nexus/v1/contact-request` → `nexus_handle_contact_request_submission()` in `inc/contact-page.php`. Reihenfolge: Honeypot/Spam-Guard → Rate-Limit → Validierung → CRM-Sync → Admin-Mail → `hu_record_inquiry_event()` → Bestätigungsmail.
- Es läuft **kein GA4, kein GTM, kein Matomo**. Messung = serverseitiges Protokoll `anfrage_gesendet` in `inc/inquiry-events.php` (eigene Tabelle, ohne personenbezogene Daten).
- Antwortzeit-Kanon: `HU_RESPONSE_HOURS = 24` in `inc/canon/messaging-canon.php`.
- `inc/crm-sales/watchdog.php` erinnert an unbearbeitete Chancen. Testanfragen dürfen dort nie auftauchen.
- `inc/client-portal.php` (Shortcode `[hu_performance_cockpit]`) existiert. Wird in Stufe 2 genutzt, in diesem PR nicht anfassen.

## Bausteine

### 1. Geheimnis
`HU_MONITOR_TOKEN` kommt aus `wp-config.php` (Raidboxes), nie ins Repo. Ist die Konstante nicht gesetzt oder kürzer als 32 Zeichen, ist das gesamte Modul inaktiv: Endpunkte antworten 404, die Seite zeigt den Zustand „Monitor nicht eingerichtet“.

### 2. Testmarker im Kontakt-Handler
Neues optionales Payload-Feld `monitor_token` plus `monitor_run_id` (`[a-z0-9-]{8,40}`). Gültig nur per `hash_equals()` gegen `HU_MONITOR_TOKEN`. Bei gültigem Marker:

- Spam-Guard und Rate-Limit werden übersprungen. Der Honeypot bleibt aktiv (der Test sendet ihn leer).
- CRM-Sync und Admin-Mail laufen **unverändert**, denn genau sie sind der Prüfgegenstand.
- Der CRM-Kontakt bekommt Status `archived` und die Meta `_nexus_monitor_test = run_id`. Watchdog, CRM-Listen und Sales-Ansichten schließen Kontakte mit dieser Meta aus.
- Der Betreff der Admin-Mail beginnt mit `[MONITOR <run_id>] `, damit n8n sie im Postfach findet.
- `hu_record_inquiry_event()` schreibt mit `form = 'monitor_test'`. Diese Zeilen zählen in keiner Summe, keiner Admin-Übersicht und nicht in der Stille-Berechnung.
- Die Bestätigungsmail geht wie bei echten Anfragen an die Absenderadresse (`monitor@hasimuener.de`).
- Die Antwort enthält zusätzlich `monitor: { runId, contactId, notified, eventRecorded, confirmationSent }`.

Ein ungültiger Token wird wie ein fehlender behandelt (keine Fehlermeldung, die ihn bestätigt).

### 3. Ergebnis-Endpunkt
`POST /nexus/v1/monitor/result`, Header `Authorization: Bearer <HU_MONITOR_TOKEN>`. Body von n8n:

```json
{ "runId": "2026-10-25-0614", "startedAt": "2026-10-25T06:14:00Z",
  "checks": { "pageLoad": true, "formSubmit": true, "uiSuccess": true,
              "mailAdmin": true, "mailConfirmation": true },
  "failedAt": null, "durationsMs": { "mailAdmin": 41000 } }
```

Gespeichert werden die letzten 90 Läufe nach dem Muster von `inquiry-events.php` (eigene Tabelle oder Option mit `autoload = false`, Wahl begründen). Serverseitig ergänzt: Für die `runId` prüft WordPress selbst, ob CRM-Kontakt und Ereigniszeile existieren (`crmStored`, `eventRecorded`).

### 4. Status-Endpunkt
`GET /nexus/v1/monitor/status`, öffentlich, ohne personenbezogene Daten, Antwort 5 Minuten zwischengespeichert. Drei Felder plus Verlauf:

| Feld | Grün | Gelb | Rot |
|---|---|---|---|
| `anfragen` | letzter Lauf jünger als 26 Std., alle Checks wahr | letzter Lauf älter als 26 Std. (der Monitor selbst ist ausgefallen) | ein Check falsch; `failedAt` nennt die Stelle |
| `aktuell` | keine offenen Kern-/Plugin-/Theme-Updates | Updates offen, jünger als 7 Tage | Updates länger als 7 Tage offen |
| `stille` | Tage seit letzter echter Anfrage unter der Schwelle | – | Schwelle überschritten |

Schwelle für `stille` = max(14 Tage, 1,5 × 90. Perzentil der Abstände zwischen echten Anfragen der letzten 180 Tage). Bei weniger als 6 echten Anfragen im Zeitraum: Feld `stille` = `"zu wenig Daten"`, neutral grau statt grün. Nicht schönrechnen.

`verlauf`: die letzten 30 Läufe als `{ datum, ok, failedAt }`.

### 5. Seite `/anfrage-monitor/`
Repo-gesteuert, virtuelle Seite nach dem Muster der Kontaktseite (`nexus_preempt_contact_404`, virtuelles Template). Rendert serverseitig aus derselben Funktion wie der Status-Endpunkt, also kein JavaScript nötig. Inhalt:

- Kicker „Anfrage-Monitor · hasimuener.de“, H1 „Kommen Anfragen über diese Website an?“
- Drei Ampelfelder mit Zeitstempel des letzten Laufs in Klartext („heute, 06:14 Uhr“)
- Verlauf der letzten 30 Tage als Punktreihe (grün/rot, `aria-label` je Punkt)
- Ein Absatz, was geprüft wird, und ein Link auf `/wordpress-wartung/` (Link erst aktiv, wenn die Seite live ist)
- `noindex, follow`; nicht in die Hauptnavigation; Footer-Link optional

Gestaltung über das `frontend-system`-Skill nach Freigabe des Wartungsseiten-Entwurfs (gleiche Sprache: Monospace für Messwerte, Akzentfarbe nur für die Hauptaktion).

## Nicht in diesem PR
- n8n-Workflow, Browser-Test, Postfach-Prüfung (Stufe 1b)
- Wartungsseite `/wordpress-wartung/`
- Kundenportal und Plugin für Kundenseiten (Stufe 2)

## Abnahme
1. Ohne `HU_MONITOR_TOKEN`: beide Endpunkte 404, Seite zeigt „Monitor nicht eingerichtet“, Kontaktformular unverändert.
2. Mit Token: Ein `curl` mit gültigem Marker erzeugt Admin-Mail mit `[MONITOR …]`-Betreff, archivierten CRM-Kontakt mit Meta, Ereigniszeile `monitor_test`. Watchdog, CRM-Listen und `hu_inquiry_events_summary()` zeigen nichts davon.
3. Ungültiger Token: verhält sich exakt wie eine normale Anfrage inklusive Spam-Guard und Rate-Limit.
4. `POST /monitor/result` ohne oder mit falschem Bearer: 401. Mit gültigem: 201, Lauf im Status sichtbar.
5. Status-Endpunkt enthält keine E-Mail, keinen Namen, keine IP.
6. `phpstan` und bestehende Tests grün; `docs/architecture/SYSTEM_MAP.md` und `LIVE_STATUS.md` um den Monitor ergänzt.
