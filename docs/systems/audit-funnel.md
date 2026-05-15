# Audit Funnel

Stand: 2026-05-15.

## Status

Der frühere `Growth Audit` ist kein öffentlicher Hauptfunnel mehr. Audit- und Tool-Legacy-Routen leiten per 301 auf den Marktcheck:

- Ziel: `/solar-waermepumpen-leadgenerierung/#marktcheck`
- Legacy: `/growth-audit/`, `/audit/`, `/customer-journey-audit/`, `/360-audit/`, `/system-diagnose/`, `/kostenlose-tools/`, `/tools/`

## Aktiver Marktcheck

- Render: `blocksy-child/page-solar-waermepumpen-leadgenerierung.php`
- Frontend: `blocksy-child/assets/js/solar-leadgenerierung-solara.js`
- Submit: `POST /wp-json/nexus/v1/audit-request`
- Persistenz: `nexus_review_request` im Audit-CRM
- Mail: zentrale Mail-/Brevo-Schicht
- n8n: nicht angebunden

Der Marktcheck qualifiziert Founding-Partner-Fit. Er ist kein generischer Verkaufssprung.

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
