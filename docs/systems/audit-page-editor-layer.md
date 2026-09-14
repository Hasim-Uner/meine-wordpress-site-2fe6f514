# Audit Page Layer

Stand: 2026-09-15.

Die frühere Growth-Audit-UI ist aus dem Runtime-Code entfernt. Öffentliche Audit-/Tool-Routen sind retired und werden nur noch als URL-Kompatibilität behandelt.

## Aktueller Status

- Öffentlicher Einstieg: `/solar-waermepumpen-leadgenerierung/#marktcheck`
- Legacy-Routen: `/growth-audit/`, `/audit/`, `/customer-journey-audit/`, `/360-audit/`, `/system-diagnose/`
- 301-Redirects: `blocksy-child/inc/system-diagnose-page.php`
- Kompatibilitäts-URL: `nexus_get_audit_url()` in `blocksy-child/inc/helpers.php`
- Aktiver Marktcheck: `blocksy-child/page-solar-waermepumpen-leadgenerierung.php` + `blocksy-child/assets/js/solar-leadgenerierung-solara.js`

## Entfernte Legacy-UI

`page-audit.php`, `inc/audit-page.php`, `inc/cja-shortcode.php`, `template-parts/audit-page-shell.php`, `assets/js/cja-audit.js` und `assets/css/cja-audit.css` sind entfernt. Der frühere Audit-Sonderheader samt `site-header.css`/`site-header.js` sowie das verwaiste `audit.css` sind ebenfalls entfernt.

`audit-live.js` und `audit-results.css` gehören nicht zu diesem UI-Layer; sie bleiben bis zur separaten Prüfung des 360°-Deep-Dive-/Result-Vertrags unangetastet.

## Historische Referenz

`docs/references/audit-page-editor-snippet-v3.html` ist nur Snapshot, kein ausführbarer Source of Truth.

## Regel

Neue Marktcheck- oder Analyse-Logik gehört in versionierten Theme-Code. Editor-HTML oder retired Growth-Audit-Komponenten dürfen nicht wieder funktionaler Source of Truth für Lead-Flows werden.
