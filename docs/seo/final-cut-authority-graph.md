# Final-Cut SEO Authority Graph

Stand: 2026-10-06

## Zweck

Die Domain hat mehrfach ihre Positionierung verändert. Der 90-Tage-GSC-Export
bildet deshalb überwiegend historische Solar-/Leadgen-Sichtbarkeit ab und ist
kein Zielbild für die aktuelle kommerzielle Architektur.

Final-Cut:

- WordPress-Entwicklung / direkte Projekte
- WordPress-Website und Landingpage
- technisches SEO
- Conversion Tracking / Server-Side Tracking
- Conversion-Optimierung
- White-Label für Agenturen
- Solar/Wärmepumpe als spezialisierte Vertikale

Der bestehende Solar-Cluster bleibt intakt. Dieser Graph baut parallel die
interne Autorität des kommerziellen Kerns auf.

## Query-Owner

Die Query-Ownership bleibt in `docs/seo/query-ownership.csv` autoritativ.
Dieser Graph darf keine zweite URL für einen vorhandenen Intent erzeugen.

Primäre Knoten:

| Knoten | URL | Rolle |
| --- | --- | --- |
| Website | `/wordpress-website-erstellen-lassen/` | transaktionaler Website-Intent |
| Landingpage | `/landingpage-erstellen-lassen/` | transaktionaler Landingpage-Intent |
| Tracking | `/ga4-tracking-setup/` | breites Conversion-Tracking |
| Server-Side | `/server-side-tracking-b2b/` | technische Tracking-Vertiefung |
| Conversion | `/conversion-optimierung/` | B2B-CRO |
| White-Label | `/whitelabel-retainer/` | Agentur-/Partner-Intent |
| Agentur Hannover | `/wordpress-agentur-hannover/` | lokale Vergleichs-/Entscheidungsseite |
| Relaunch | `/website-relaunch/` | informationaler Relaunch-Owner |
| Auslagern | `/wordpress-projekte-auslagern/` | informationaler Agentur-Owner |

## Relevante Kanten

- `Agentur Hannover` → `Website` → transaktionaler Neubau-/Relaunch-Owner
- `Agentur Hannover` → `Tracking` → Messbarkeits-Owner
- `Agentur Hannover` → `Conversion` → Optimierungs-Owner

Die lokale Agentur-Seite ist damit Entscheidungs-Hub für direkte Unternehmen. White-Label bleibt ein eigener Agentur-/Partner-Intent und ist kein Kontextziel dieser Route.

## Tracking-Support

`/server-side-tracking-b2b/` bleibt alleiniger kommerzieller Owner für
`server side tracking`, Anbieter-/Agentur- und DSGVO-nahe Kaufintents.

`/server-side-tracking-gtm/` ist Supporting Content für die technische Frage:
Architektur aus Web-GTM + Server-GTM, Consent-Signale, Deduplizierung und
Paralleltest. Der Beitrag darf den Owner stärken, aber keine eigene Anbieter-
oder Preispositionierung aufbauen.

## Regeln

1. Pro Knoten höchstens drei Kontextziele.
2. Keine generischen `Mehr erfahren`-Anker.
3. Keine Solar-Links in den Core-Graph; die Energie-Vertikale besitzt ihren
   eigenen Cluster.
4. Homepage bleibt Hub und rendert keinen zusätzlichen Authority-Block.
5. Der globale Footer bleibt Navigation; dieser Graph ist fachlicher Kontext.
6. Neue Knoten nur nach Query-Ownership- und SERP-Intent-Prüfung.
7. GEO folgt denselben Knoten: Person, Organization, Service und sichtbare
   interne Architektur müssen dieselbe Positionierung ausdrücken.

## Messung

Nach einem stabilen Zeitraum auswerten:

- Impressionen/Klicks der Core-Query-Owner
- neue Queries pro Owner
- Cannibalization / Mehrfachranking
- interne Klicks `authority_link_*`
- generative Sichtbarkeit nach URL, soweit Search Console sie ausweist

Solar-Traffic wird separat beobachtet und nicht als Misserfolg der neuen
Positionierung interpretiert.
