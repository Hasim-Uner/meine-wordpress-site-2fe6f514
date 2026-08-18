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

`inc/commercial-routing.php` ist die kanonische Quelle für die aktiven Geschäftswege und muss in `functions.php` explizit geladen werden.

Die Route `agentur_local` bleibt ein SEO-/Local-Einstieg und ist kein globaler Geschäftspfeiler. `tracking_b2b` bleibt eigener Query-Owner. `marketcheck` gehört ausschließlich zum Energy-Pfad.

## Schema

Die kanonischen Person-, Organization- und WebSite-Werte werden direkt in `inc/org-schema.php` gepflegt. Die temporäre Output-Normalisierung aus `inc/schema-positioning.php` wird nach der Migration entfernt.

Der globale OfferCatalog bildet ab:

- WordPress-Entwicklung
- Server-Side Tracking & Attribution
- Conversion-Optimierung
- White-Label für Agenturen
- Anfragesysteme für Solar & Wärmepumpe
- Marktcheck für Solar & Wärmepumpe

Die Seite `/wordpress-freelancer-hannover/` erhält ihren Service-Knoten direkt aus der zentralen Service-Registry in `org-schema.php`.

## Nicht Teil dieser Phase

- Blog-Templates und Blog-CTA-Routing
- Redirects oder Deindexierung bestehender Money Pages
- Änderung der bestehenden SEO-Query-Ownership
- Abschaffung des Energy-Marktchecks

## Prüfregel

Vor Merge müssen German Copy Guard, Linkprüfung, Architekturprüfung, PHP-Syntax, PHPStan und Theme-Build grün sein.