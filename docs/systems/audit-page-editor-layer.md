# Audit Page Layer

Stand: 2026-05-15.

Der Dateiname ist historisch. Die öffentlichen Audit-/Tool-Routen sind retired und leiten per 301 auf den Marktcheck der Solar-Landingpage.

## Aktueller Status

- Öffentlicher Einstieg: `/solar-waermepumpen-leadgenerierung/#marktcheck`
- Legacy-Routen: `/growth-audit/`, `/audit/`, `/customer-journey-audit/`, `/360-audit/`, `/system-diagnose/`
- Redirect-Logik: `blocksy-child/inc/audit-page.php`, `blocksy-child/inc/system-diagnose-page.php`, `blocksy-child/inc/seo-meta.php`
- Aktiver Marktcheck: `blocksy-child/page-solar-waermepumpen-leadgenerierung.php` + `blocksy-child/assets/js/solar-leadgenerierung-solara.js`

## Legacy Layer

Der frühere Audit-Code bleibt repo-seitig als Referenz erhalten, ist aber kein öffentlicher Default-Flow:

- `blocksy-child/page-audit.php`
- `blocksy-child/inc/audit-page.php`
- `blocksy-child/inc/cja-shortcode.php`
- `blocksy-child/assets/js/cja-audit.js`
- `blocksy-child/assets/js/audit-live.js`
- `blocksy-child/template-parts/audit-page-shell.php`

## Editor-Snippet

Nur die letzte historische Editor-Variante bleibt als Snapshot erhalten:

- `docs/references/audit-page-editor-snippet-v3.html`

V1/V2 wurden geloescht, weil sie nicht aktiv, nicht referenziert und durch V3 ersetzt waren.

## Regel

Neue Marktcheck- oder Analyse-Logik gehört in versionierten Theme-Code. Editor-HTML darf nicht wieder funktionaler Source of Truth für Lead-Flows werden.
