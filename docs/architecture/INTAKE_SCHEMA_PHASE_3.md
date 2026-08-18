# Intake & Schema – Phase 3

Stand: 18.08.2026

## Ziel

Die Repositionierung **WordPress · Tracking · Conversion** soll nicht nur in Copy und Navigation sichtbar sein, sondern auch im direkten Anfragepfad und im kanonischen Structured-Data-Graphen.

## Kontakt / Intake

Der generische `/kontakt/`-Einstieg ist ein direkter Projekt- und Zusammenarbeitsweg.

Standardmäßig sichtbar:

- Projektanfrage
- Umsetzung / Optimierung
- Weiterentwicklung
- allgemeine Anfrage

Der **Marktcheck** bleibt ein spezialisierter Einstieg für Solar- und Wärmepumpen-Anbieter und wird im Kontaktformular nur bei einem expliziten `type=audit` eingeblendet.

Die bestehenden technischen Transport-Keys (`project`, `implementation`, `ongoing`, `audit` usw.) bleiben stabil, damit REST, CRM, Mail- und Automationslogik nicht durch reine Positionierungsänderungen brechen.

## Commercial Routing

`inc/commercial-routing.php` ist die kanonische Quelle für die aktiven Geschäftswege.

Die Runtime lädt den Router aktuell über den Positioning-Layer, bevor öffentliche Header-, Menü- und Schema-Hooks ausgeführt werden. Damit ist der Route-Contract aktiv, ohne den großen Modul-Bootstrap in derselben Änderung umzubauen.

Die Route `agentur_local` bleibt ein SEO-/Local-Einstieg und ist kein globaler Geschäftspfeiler. `tracking_b2b` bleibt eigener Query-Owner. `marketcheck` gehört ausschließlich zum Energy-Pfad.

## Schema

`inc/org-schema.php` bleibt die ausgereifte Basis für route-spezifische Structured Data wie Article, FAQ, Breadcrumbs und Spezial-Services. `inc/schema-positioning.php` ist der kanonische Commercial-Identity-Layer darüber und normalisiert ausschließlich die globalen Person-, Organization- und WebSite-Knoten sowie den direkten Freelancer-Service.

Diese Trennung ist in Phase 3 bewusst: Der große bestehende Schema-Generator wird nicht für eine reine Positionierungsänderung breit umgeschrieben. Damit bleiben die vielen spezialisierten Schema-Pfade stabil, während die globale Identität trotzdem eindeutig **WordPress · Tracking · Conversion** abbildet.

Der globale OfferCatalog bildet ab:

- WordPress-Entwicklung
- Server-Side Tracking & Attribution
- Conversion-Optimierung
- White-Label für Agenturen
- Anfragesysteme für Solar & Wärmepumpe
- Marktcheck für Solar & Wärmepumpe

Die Seite `/wordpress-freelancer-hannover/` erhält einen eigenen Service-Knoten, dessen URL ebenfalls aus dem Commercial-Route-Contract kommt.

## Nicht Teil dieser Phase

- Blog-Templates und Blog-CTA-Routing
- Redirects oder Deindexierung bestehender Money Pages
- Änderung der bestehenden SEO-Query-Ownership
- Abschaffung des Energy-Marktchecks
- riskanter Vollrefactor des gewachsenen `org-schema.php`

## Prüfregel

Vor Merge müssen German Copy Guard, Linkprüfung, Architekturprüfung, PHP-Syntax, PHPStan und Theme-Build grün sein.