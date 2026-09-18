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

Der Marktcheck qualifiziert Umsetzungs-Fit. Er fragt Angebot, wirtschaftlichen Fit (`business_fit`), Anfragevolumen, CPL, Engpass und Kontakt ab. Der Fit basiert auf Projektwert, eigenem Vertrieb bzw. geschäftsführergeführtem Vertrieb und Zielgebiet, nicht auf Mitarbeiterzahl. Er ist kein generischer Verkaufssprung.

## REST Contract

Der aktive Submit-Endpunkt ist versioniert und gibt maschinenlesbare Contract-Signale aus:

- Request-Feld: optional `contract_version`; wenn gesetzt, muss es dem aktiven Server-Contract entsprechen.
- Response-Felder bei Erfolg: `ok`, `requestId`, `message`, `status`, `auditType`, `qualification`, `contractVersion`, `traceId`.
- Response-Felder bei Fehlern: `ok=false`, `message`, `error`, `error_code`, `error_details.field`, `contractVersion`, `traceId`, `retryable`.
- Response-Header: `X-Nexus-Contract-Version` und `X-Nexus-Trace-Id`.
- Fehlerstatus: `400` für Validierung/Contract-Drift, `429` für Rate-Limit, `500` für Storage-Fehler.

## Legacy-Code

Die frühere Growth-Audit-UI ist entfernt. Erhalten bleiben nur die 301-/410-Kompatibilitätsverträge sowie separat zu prüfende 360°-Deep-Dive-/Result-Artefakte (`audit-live.js`, `audit-results.css`).


## n8n

Aktuell gibt es keinen produktiven n8n-Pfad für den Marktcheck. Workflow-Exports unter `automations/n8n/` gelten als historische oder vorbereitende Artefakte. Neue n8n-Arbeit braucht immer das Triplet aus Workflow-JSON, Doku und Flow-Map.

## Risiken

- Legacy-Audit-Code und aktiver Marktcheck duerfen nicht vermischt werden.
- Alte Doku oder Editor-Snippets können noch eine abgelöste Antwortzeit (siehe `scripts/canon-forbidden-values.txt`), `Growth Audit` oder n8n als aktiven Default suggerieren.
- Public CTA-Logik muss beim Marktcheck bleiben, solange keine neue Funnel-Entscheidung dokumentiert ist.
