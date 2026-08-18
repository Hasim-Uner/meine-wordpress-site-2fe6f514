# Commercial Routing Phase 2

## Scope

Diese Phase lässt den Blog bewusst außen vor und stabilisiert die aktive kommerzielle Architektur außerhalb redaktioneller Templates.

## Source of truth

`blocksy-child/inc/commercial-routing.php` ist die kanonische Quelle für öffentliche Geschäftswege und die globale Navigation.

### Kommerzielle Routen

- `freelancer`: direkte WordPress-Zusammenarbeit
- `project_request`: neutrale Projektanfrage für direkte Unternehmen
- `whitelabel`: Agentur-/White-Label-Einstieg
- `energy`: Solar-/Wärmepumpen-Spezialisierung
- `marketcheck`: diagnostischer Einstieg nur für den Energy-Kontext
- `tracking_b2b`: eigener Tracking-Query-Owner
- `agentur_local`: lokale SEO-Einstiegsseite, kein globaler Navigationspfeiler
- `results`: Proof-Hub
- `about`: Personen-/Vertrauensseite
- `contact`: neutraler Kontakt

## Navigation contract

Die globale Reihenfolge ist:

1. Solar & Wärmepumpen
2. WordPress Freelancer
3. Für Agenturen
4. Ergebnisse
5. Über Haşim
6. Projekt anfragen

Der direkt gerenderte Header und ein später neu aufgebautes WordPress-Menü beziehen sich auf denselben Contract. Ein Theme-Wechsel oder `?nexus_rebuild_menu=1` darf deshalb nicht mehr die alte Kombination `WordPress Agentur` + `Marktcheck` als globale Hauptnavigation wiederherstellen.

## Conversion contract

SEO-Query-Ownership und Conversion-Ziel bleiben getrennt. Eine indexierbare Fachseite muss nicht auf ihre SEO-Hub-Seite umgeleitet werden. Der CTA folgt dem Geschäftsweg:

- allgemeine WordPress-/Tracking-/Conversion-Arbeit -> Projektanfrage
- Agentur-Intent -> White-Label
- Solar-/Wärmepumpen-Intent -> Marktcheck bzw. Energy-Hub
- lokale Agentur-Suche -> Agentur-Seite bleibt indexierbar, verweist aber sichtbar auf direkte Freelancer-Zusammenarbeit

Die bestehenden Kontakt-Keys (`type`, `focus`) bleiben stabil, damit CRM- und Automationsverträge nicht unnötig verändert werden. Die globale Projektanfrage nutzt `type=project&focus=implementation_scope`.

## Bewusst außerhalb dieses Schritts

- `single.php` und `category.php`
- Blog-Kategorien und redaktionelle CTA-Bridges
- vollständiger Refactor von `org-schema.php`
