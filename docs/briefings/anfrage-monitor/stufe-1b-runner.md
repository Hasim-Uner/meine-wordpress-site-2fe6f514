# Briefing: Anfrage-Monitor, Stufe 1b (Runner)

Ablage im Repo: `docs/briefings/anfrage-monitor/stufe-1b-runner.md`
Voraussetzung: Stufe 1 (`stufe-1-wordpress.md`) ist live und `HU_MONITOR_TOKEN` steht in `wp-config.php`.

## Was läuft

Ein geplanter GitHub-Actions-Workflow (`.github/workflows/anfrage-monitor.yml`, täglich 04:17 UTC, also 06:17 Berlin im Sommer und 05:17 im Winter) startet `scripts/monitor/anfrage_monitor.py`:

1. Headless-Chromium öffnet `/kontakt/` und füllt das Formular wie eine Person aus. Name „Anfrage-Monitor“, Absender `kontakt@hasimuener.de`, Nachricht mit dem Hinweis auf eine automatische Testanfrage, Einwilligung gesetzt.
2. Beim Absenden ergänzt eine Browser-Route `monitor_token` und `monitor_run_id` im Request. Das Geheimnis steht nie im DOM der Seite.
3. Der Runner sucht per IMAP im Postfach nach beiden Mails: `[MONITOR <lauf>] …` (interne Mail) und `[MONITOR <lauf> · Bestätigung] …` (Bestätigung an den Absender). Beide landen im selben Postfach. Er fragt alle 20 Sekunden ab, höchstens 10 Minuten.
4. Das Ergebnis geht an `POST /wp-json/nexus/v1/monitor/result` (Bearer). WordPress ergänzt `crmStored` und `eventRecorded` selbst und antwortet mit `run.ok` und `run.failedAt`.

Exit-Code 0, wenn WordPress `run.ok = true` meldet. Sonst Exit-Code 1, der Workflow gilt als fehlgeschlagen und GitHub schickt die übliche Mail an den Repo-Eigentümer. Fehlende Einstellungen ergeben Exit-Code 2.

## Einrichtung (einmalig)

1. **Secrets anlegen.** GitHub → Repo → Settings → Secrets and variables → Actions → Repository secrets:
   - `HU_MONITOR_TOKEN`: derselbe Wert wie die Konstante in `wp-config.php` (mindestens 32 Zeichen, z. B. `openssl rand -hex 32`).
   - `MONITOR_IMAP_USER`: vollständige Adresse des Postfachs, in das beide Mails fallen (`kontakt@hasimuener.de`).
   - `MONITOR_IMAP_PASSWORD`: Passwort dieses Postfachs, siehe Sicherheitshinweis unten.
2. **Optional: Repository-Variable** `MONITOR_SITE_URL` (Variables, nicht Secrets). Ohne sie gilt `https://hasimuener.de`.
3. **Optional: Filterregel bei Infomaniak.** Betreff enthält `MONITOR` → in den Ordner `Monitor` verschieben (Ordner vorher anlegen). Der Runner durchsucht `INBOX` und `Monitor`; ein fehlender Ordner wird übersprungen. Mit der Regel bleibt der Posteingang frei von Monitor-Mails, ohne sie ist alles auch im Posteingang zu finden.
4. **Auf `main` bringen.** Zeitpläne laufen nur auf dem Standard-Branch, der Job selbst ist zusätzlich auf `refs/heads/main` begrenzt.
5. **Einmal von Hand starten.** Actions → „Anfrage-Monitor“ → „Run workflow“ → Branch `main`. Der Lauf dauert etwa drei bis zehn Minuten (Mailzustellung). Danach zeigt `/anfrage-monitor/` den ersten Lauf, statt „Noch kein Testlauf“.

Lokal ohne Netz prüfen: `python3 scripts/monitor/anfrage_monitor.py --self-test`. Mit echten Zugangsdaten, aber ohne Meldung an die Website: `MONITOR_DRY_RUN=1` setzen, der Lauf gibt das Ergebnis aus.

## Einstellungen des Runners

| Variable | Standard |
|---|---|
| `MONITOR_SITE_URL` | `https://hasimuener.de` |
| `HU_MONITOR_TOKEN` | Pflicht |
| `MONITOR_SENDER_EMAIL` | `kontakt@hasimuener.de` |
| `MONITOR_IMAP_HOST` / `MONITOR_IMAP_PORT` | `mail.infomaniak.com` / `993` |
| `MONITOR_IMAP_USER`, `MONITOR_IMAP_PASSWORD` | Pflicht |
| `MONITOR_IMAP_FOLDERS` | `INBOX,Monitor` |
| `MONITOR_MAIL_TIMEOUT_S` | `600` |
| `MONITOR_DRY_RUN` | `1` meldet nichts an die Website |

## Was eine Fehlermail bedeutet

GitHub meldet „Run failed: Anfrage-Monitor“. Im Log stehen nur Prüfnamen, Wahrheitswerte und Dauern. `run.failedAt` nennt die erste fehlgeschlagene Stelle:

| `failedAt` | Bedeutung, wahrscheinliche Ursache |
|---|---|
| `pageLoad` | `/kontakt/` antwortet nicht mit 2xx: Seite, Hosting oder Cache. |
| `formSubmit` | Der Server hat die markierte Anfrage nicht mit 201 angenommen. Häufig: Token in `wp-config.php` und Secret weichen ab, das Modul ist aus, oder das Formular hat sich geändert. |
| `uiSuccess` | Die Anfrage kam an, das Formular zeigt aber keine Erfolgsmeldung. Fehler in `contact.js`. |
| `mailAdmin` | Die interne Mail ist nach zehn Minuten nicht im Postfach: Mailversand, Spam- oder Filterregel, falscher Ordner. |
| `mailConfirmation` | Die Bestätigung an den Absender fehlt, die interne Mail ist da. |
| `crmStored` / `eventRecorded` | WordPress hat Kontakt oder Protokollzeile nicht geschrieben: CRM oder Anfrage-Protokoll prüfen. |

Läuft der Monitor selbst nicht mehr, springt die Ampel nach 26 Stunden auf Gelb („Letzter Testlauf ist älter als ein Tag“). Dann Actions öffnen: Bei öffentlichen Repositories deaktiviert GitHub Zeitpläne nach 60 Tagen ohne Aktivität, der Workflow lässt sich dort wieder einschalten.

Meldet das Log „Ergebnis abgelehnt: HTTP 401“, stimmt das Secret `HU_MONITOR_TOKEN` nicht mit der Konstante überein. Bei „IMAP-Abfrage fehlgeschlagen“ sind Benutzer, Passwort oder Host falsch.

## Sicherheitshinweis

Das Secret `MONITOR_IMAP_PASSWORD` ist das Passwort des Hauptpostfachs `kontakt@hasimuener.de`. Wer den Workflow auf `main` ändern kann, kann es auslesen.

- Wenn Infomaniak ein Anwendungs- oder gerätespezifisches Passwort anbietet, dieses verwenden und nicht das Hauptpasswort. Es lässt sich einzeln widerrufen.
- Nur `main` darf die Secrets nutzen (`if: github.ref == 'refs/heads/main'`). Pull Requests aus Forks bekommen keine Secrets. Änderungen am Workflow oder am Skript vor dem Merge lesen, `main` schützen (Branch-Regel mit Pull Request).
- Der Runner liest nur Betreffzeilen von Mails mit `MONITOR <Lauf-ID>` im Betreff, ausschließlich lesend (`EXAMINE`, `BODY.PEEK`): nichts wird als gelesen markiert, verschoben oder gelöscht. Mail-Inhalte und Zugangsdaten stehen nie im Log.
- Nach Verdacht auf ein Leck beide Werte erneuern: Postfach-Passwort bei Infomaniak, `HU_MONITOR_TOKEN` in `wp-config.php` und in den Secrets.

Datenschutz: `docs/architecture/PRIVACY.md`, Abschnitt „Anfrage-Monitor“. Abhängigkeiten: `docs/architecture/SYSTEM_MAP.md`.
