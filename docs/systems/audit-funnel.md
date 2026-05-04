# Audit Funnel

Stand: 2026-05-04.

Diese Doku beschreibt den technisch aktiven Legacy-/Sekundärpfad rund um den `Growth Audit`.

Wichtig:

- Der aktive Funnel für `/growth-audit/` ist jetzt ein Instant-Results-Flow.
- Die Route rendert den Shortcode `cja_audit` und sendet erst nach explizitem Nutzerklick eine URL an n8n.
- Der frühere 48h-Intake und `audit-live.js` liegen weiter im Repo, sind aber nicht mehr der aktive Default der Hauptroute.
- Der Growth Audit ist kein Hauptfunnel mehr. Der primäre Einstieg für kalten B2B-Traffic ist `/anfrage-system-analyse/`.

## Zweck des Pfads

Der `Growth Audit` ist ein technischer Legacy-/Sekundärpfad für schnelle URL-orientierte Orientierung. Er darf die Anfrage-System-Analyse nicht als Hauptfunnel ersetzen.

Sein Job:

- Reibung fuer Erstkontakt senken
- die größten Bremsen sofort sichtbar machen
- Botschaft, Proof, CTA und Anfrageführung priorisieren
- einen klaren nächsten Schritt in die Anfrage-System-Analyse oder einen warmen Anfragepfad vorbereiten

Der Pfad verkauft nicht sofort. Er schafft Klarheit und führt zurück in die neue Funnel-Ladder.

## Aktiver Flow

1. Besucher landet auf `/growth-audit/`.
2. `blocksy-child/page-audit.php` rendert die Route ueber `blocksy-child/inc/audit-page.php`, das aktiv den Shortcode `cja_audit` ausgibt.
3. Das Frontend zeigt ein einzelnes URL-Feld, einen kurzen Loading-State und danach ein Ergebnis-Dashboard.
4. `blocksy-child/assets/js/cja-audit.js` bereinigt die URL, validiert sie clientseitig und startet bevorzugt `POST https://n8n.hasimuener.de/webhook/audit` mit JSON-Body `{ "url": "<bereinigte-url>" }`.
5. Der Start-Webhook antwortet sofort mit `jobId + processing`; das Frontend pollt danach `GET https://n8n.hasimuener.de/webhook/audit-status?jobId=...`, bis das Ergebnis fertig ist.
6. Falls der Async-Start-Endpoint nicht verfügbar ist, fällt das Frontend kontrolliert auf den Legacy-Webhook `https://n8n.hasimuener.de/webhook/cja-analyze` zurück.
7. Das Frontend akzeptiert sowohl den aktuellen V3-Result-Contract aus dem Polling als auch den bisherigen Direkt-Payload mit `overall_score`, Modulen, Revenue-Summary und `quickWins`.
8. Die Analyse rendert direkt auf der Seite und zeigt als nächsten Schritt einen qualifizierten Anfragepfad.
9. Nachgelagerte persönliche Qualifizierung läuft nur noch über Kontakt-/Call-Pfade, nicht mehr über einen Pflicht-CRM-Intake auf der Hauptroute.

## Nutzerseitige Inputs

Pflichtfeld:

- `url`

## Nutzerseitige Outputs

- Ergebnis-Dashboard direkt auf der Seite
- priorisierte Quick Wins
- CTA nach `/kontakt/`

## Systemseitige Outputs

- n8n-Response fuer das Dashboard
- kein Pflicht-CRM-Eintrag auf der Hauptroute

## Repo-Touchpoints

- `blocksy-child/page-audit.php`
- `blocksy-child/inc/audit-page.php`
- `blocksy-child/inc/cja-shortcode.php`
- `blocksy-child/assets/js/cja-audit.js`
- `blocksy-child/assets/css/cja-audit.css`
- `blocksy-child/inc/enqueue.php`
- `blocksy-child/inc/seo-meta.php`
- `blocksy-child/inc/org-schema.php`

## Externe Abhaengigkeiten

- n8n Webhooks `https://n8n.hasimuener.de/webhook/audit`, `https://n8n.hasimuener.de/webhook/audit-status` und als Fallback `https://n8n.hasimuener.de/webhook/cja-analyze`
- WordPress fuer Route, Shortcode und CTA-Ziel
- Cal.com nur fuer nachgelagerte Gespraechswege

## Nicht aktiver Repo-Layer

Im Repo liegt weiterhin ein aelterer oder alternativer Audit-Layer:

- `blocksy-child/assets/js/audit-live.js`
- `blocksy-child/assets/css/audit-results.css`
- `blocksy-child/template-parts/audit-page-shell.php`
- `automations/n8n/workflows/audit-funnel__customer-journey-audit__refactor.json`

Dieser Layer ist fachlich weiter relevant, aber derzeit nicht die aktive Landingpage-Logik der Hauptroute.

## Risiken

- Der aktive Frontend-Flow ist jetzt robuster, aber der produktive `n8n.hasimuener.de`-Stand fuer den Async-Runner ist repo-seitig noch nicht als vollstaendig versionierter Export plus Flow-Map beschrieben.
- Der Legacy-48h-Intake und der aktive Instant-Results-Layer leben parallel im Repo und muessen bewusst getrennt bleiben.
- CRM- und Follow-up-Logik fuer den alten Intake bleibt im Theme vorhanden, obwohl sie auf der Hauptroute nicht mehr aktiv ist.

## Naechste sinnvolle Schritte

1. Den aktiven n8n-Workflow `cja-analyze` als Export, Doku und Flow-Map versionieren.
2. Das Response-Schema der Analyse explizit dokumentieren, damit Frontend und n8n nicht still auseinanderlaufen.
3. Entscheiden, ob der Legacy-48h-Intake fuer andere Einstiege erhalten bleibt oder repo-seitig bereinigt werden soll.
