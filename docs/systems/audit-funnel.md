# Audit Funnel

Stand: 2026-05-26.

## Status

Der frühere `Growth Audit` ist kein öffentlicher Hauptfunnel mehr. Geschützte Audit-Legacy-Einstiege leiten per 301 auf den Marktcheck; alte Tool-/ROI-/Service-Pfade liefern `410 Gone`:

- Ziel: `/solar-waermepumpen-leadgenerierung/#marktcheck`
- 301-Legacy: `/growth-audit/`, `/audit/`, `/customer-journey-audit/`, `/360-audit/`, `/system-diagnose/`, `/readiness-diagnose/`, `/anfrage/`
- 410-Legacy: `/kostenlose-tools/`, `/tools/`, `/website-performance-analyse/`, `/roi-rechner/`, `/audit-linkedin/`, alte Service-Slugs

## Aktiver Marktcheck

- Render: `blocksy-child/page-solar-waermepumpen-leadgenerierung.php`
- Frontend: `blocksy-child/assets/js/solar-leadgenerierung-solara.js`
- Submit: `POST /wp-json/nexus/v1/audit-request`
- Contract: `2026-05-26.audit-request.v1`, serverseitig in `NEXUS_REVIEW_REQUEST_CONTRACT_VERSION`
- Persistenz: `nexus_review_request` im Audit-CRM
- Mail: zentrale Mail-/Brevo-Schicht
- n8n: nicht angebunden

Der Marktcheck qualifiziert Founding-Partner-Fit. Er fragt Angebot, wirtschaftlichen Fit (`business_fit`), Anfragevolumen, CPL, Engpass und Kontakt ab. Der Fit basiert auf Projektwert, eigenem Vertrieb bzw. geschäftsführergeführtem Vertrieb und Zielgebiet, nicht auf Mitarbeiterzahl. Er ist kein generischer Verkaufssprung.

## REST Contract

Der aktive Submit-Endpunkt ist versioniert und gibt maschinenlesbare Contract-Signale aus:

- Request-Feld: optional `contract_version`; wenn gesetzt, muss es dem aktiven Server-Contract entsprechen.
- Response-Felder bei Erfolg: `ok`, `requestId`, `message`, `status`, `auditType`, `qualification`, `contractVersion`, `traceId`.
- Response-Felder bei Fehlern: `ok=false`, `message`, `error`, `error_code`, `error_details.field`, `contractVersion`, `traceId`, `retryable`.
- Response-Header: `X-Nexus-Contract-Version` und `X-Nexus-Trace-Id`.
- Fehlerstatus: `400` für Validierung/Contract-Drift, `429` für Rate-Limit, `500` für Storage-Fehler.

## Legacy-Code

Bleibt im Repo, ist aber nicht der öffentliche Default:

- `blocksy-child/page-audit.php`
- `blocksy-child/inc/audit-page.php`
- `blocksy-child/inc/cja-shortcode.php`
- `blocksy-child/template-parts/audit-page-shell.php`
- `blocksy-child/assets/js/cja-audit.js`
- `blocksy-child/assets/js/audit-live.js`
- `blocksy-child/assets/css/cja-audit.css`
- `blocksy-child/assets/css/audit-results.css`

## n8n

Aktuell gibt es keinen produktiven n8n-Pfad für den Marktcheck. Workflow-Exports unter `automations/n8n/` gelten als historische oder vorbereitende Artefakte. Neue n8n-Arbeit braucht immer das Triplet aus Workflow-JSON, Doku und Flow-Map.

## Risiken

- Legacy-Audit-Code und aktiver Marktcheck duerfen nicht vermischt werden.
- Alte Doku oder Editor-Snippets können noch `48h`, `Growth Audit` oder n8n als aktiven Default suggerieren.
- Public CTA-Logik muss beim Marktcheck bleiben, solange keine neue Funnel-Entscheidung dokumentiert ist.
