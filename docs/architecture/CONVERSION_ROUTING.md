# Conversion Routing Architecture

Status: active decision, updated 2026-09-13

This document separates **SEO ownership** from **conversion routing**. It exists to prevent a recurring failure mode: sending every page to the same funnel or moving a ranking query owner merely because another page has the preferred CTA.

## Core rule

**Search intent -> owning page -> next best action.**

Not:

**Search intent -> whichever landing page is currently the main sales focus.**

A page can remain the canonical SEO destination for its query while its CTA routes to a different next step.

## Commercial routes

| Route | Primary audience / intent | Page role | Primary CTA | Secondary CTA / bridge |
| --- | --- | --- | --- | --- |
| `/` | Brand and direct WordPress/Freelancer intent | Homepage and direct WordPress money page | `Projekt anfragen` with offer-specific focus | Proof / White-Label / Solar / tracking specialist |
| `/wordpress-freelancer-hannover/` | Retired direct-client route | 301 to `/`; excluded from sitemap | Homepage takes over content and query ownership | Legacy content anchors remain on `/` |
| `/whitelabel-retainer/` | Agencies seeking delivery capacity | Agency money page | White-Label request form (`?case=aufgabe` / `?case=angebotsphase`) or scoped first project | 30-minute call / proof |
| `/solar-waermepumpen-leadgenerierung/` | Solar, heat-pump and storage businesses | Energy vertical money page | Marktcheck | Solar proof / case study |
| `/server-side-tracking-b2b/` | Server-Side Tracking commercial intent | Specialist tracking money page | Tracking project request / scope clarification | White-Label bridge for agencies |
| `/ga4-tracking-setup/` | GA4/GTM setup or troubleshooting | Specialist tracking money page | `Tracking-Projekt anfragen` → `/kontakt/?type=project&focus=tracking` | Tracking specialist / project evidence |
| `/wordpress-agentur-hannover/` | Local `wordpress agentur hannover` search intent | Local SEO acquisition page | Project request | Explicit bridge to Freelancer page |
| `/ergebnisse/` | Proof / evaluation, all three routes | Proof hub / trust layer | Three-way close: Marktcheck, `Projekt anfragen`, White-Label `Aufgabe beschreiben` | Case study, public references, tracking money page |
| `/case-study-solar-leadgenerierung/` | Solar proof | Evidence page | Solar Marktcheck | Energy money page |

## GA4: direkte Tracking-Anfrage

GA4 verwendet für Hero, Proof und Abschluss dieselbe direkte Projektanfrage
mit dem Fokus `tracking`. Der zentrale Kontaktablauf übernimmt die Vorauswahl.
Die bestehenden Actions `cta_cluster_audit`, `cta_cluster_proof_audit` und
`cta_cluster_footer_audit` bleiben erhalten; auf dieser Route bezeichnen sie
ab dem Umbau vom 2026-09-14 Projektanfragen, keine Marktcheck-Starts.
Der Hinweis auf den Marktcheck im gemeinsamen Kennzahlenband wird nur für
GA4 durch die kanonische Antwortzusage zur Tracking-Anfrage ersetzt.

## Homepage: direkter Freelancer-Einstieg

Seit der Betreiberentscheidung vom 2026-09-13 übernimmt `/` die Inhalte und
Suchintention der früheren Freelancer-Route. Die Seite führt vom
WordPress-Freelancer-Hero zu konkreten Leistungen, Preisrahmen, öffentlichen
Arbeiten, Zusammenarbeit und Projektanfrage. White-Label und Solar/Wärmepumpe
bleiben als gezielte Brücken erreichbar. Der Solar-Fall ist abgegrenzter Proof.

Die Hero- und Abschluss-CTAs führen zur kanonischen Projektanfrage
`/kontakt/?type=project` **ohne** vorbelegtes Thema (seit 2026-09-22): Die
Kontaktseite zeigt dann die Themenwahl und die Weiche zu White-Label und
Marktcheck. Nur die drei Angebotskarten verwenden
`hu_get_contact_intake_url('project', focus)` mit `relaunch`, `conversion` oder
`tracking`; dort ist die Themenfrage schon beantwortet, und der Kontaktablauf
überspringt sie. Die Homepage benötigt kein eigenes Formular-JavaScript.
`#anfrage` und `#kontakt` bleiben als Anker des Abschlussblocks erhalten.

Routing, Inhalte, Weiterleitung, Analytics-Zuordnung und Nachkontrolle:
`docs/decisions/homepage-freelancer-konsolidierung.md`.

## Über Haşim: persönliche Methodik und Belege

`/hasim-uener/` erklärt Person und Arbeitsweise; Leistungen und Preisrahmen
bleiben auf der Homepage. Die Seite führt durch persönlichen Hintergrund,
Engpass-Priorisierung, Defekt/Gestaltung/Hypothese, unterschiedliche
Besucherbedürfnisse, Gestaltung und Übergabe. Ein kanonischer Solar-Fall und
eine öffentliche WordPress-Arbeit belegen den Ansatz mit getrenntem Kontext.

- Hero: `about_read_method` springt zu `#arbeitsweise` (Navigation).
- Besucherbeispiele: direkte Anbieterwahl → `/#angebote`
  (`link_about_freelancer`), Lösungsprüfung → `/ergebnisse/`
  (`about_view_results`), Orientierung → `/blog/` (`about_read_expertise`).
- Abschluss: `cta_about_project` → kanonische Projektanfrage;
  `link_about_whitelabel` → eigene White-Label-Seite.
- `about_station_solar_case` bleibt als Aktion erhalten; der Beleg steht jetzt
  im Abschnitt `about_method`. Neue Beleg-Links: `about_reference_open` und
  `about_code_history`. Bestehende Profil-/Mail-Actions bleiben erhalten.
- Native `<details>` erläutern Besucherbedürfnisse ohne zusätzliche
  JavaScript-Laufzeit. Profil, Kontaktformular, Consent und CRM bleiben in ihren
  bisherigen Zuständigkeiten. Kein Solar-Marktcheck als allgemeiner Seiten-CTA.

## Ergebnisse-Hub: Arbeitsbelege für die drei Geschäftspfade

`/ergebnisse/` folgt seit dem Umbau vom 2026-09-14 der Reihenfolge
WordPress-Arbeiten → technische Projektgeschichte → Solar-Fall →
Übergabemuster → passender nächster Schritt. Die bestehende generierte TOC
kommt aus `inc/commercial-routing.php`; ihre Einträge folgen der DOM-Reihenfolge.
Es gibt keine zweite seitenlokale TOC und keine eigene JavaScript-Laufzeit.

- Öffentliche Projekte und deren Beitrag stammen unverändert aus
  `inc/canon/reference-canon.php`. Ergänzte Prüfhinweise erklären, worauf
  Besucher achten können; sie behaupten keine zusätzlichen Messergebnisse.
- Die technische Projektgeschichte dokumentiert die Homepage-/Freelancer-
  Zusammenführung am eigenen Projekt. Die Quellen verlinken die umgesetzte
  Revision `569055ddd28214a9ff6e511369619f5a604eff07`. Technische Regeln sind
  damit nachprüfbar; Rankings, Live-Tracking und Conversions werden nicht
  daraus abgeleitet.
- Solar-Kennzahlen stammen aus `inc/canon/e3-proof-canon.php`. Die frühere
  angenommene Abschlussquote erscheint nicht mehr als gemessener Vorher-Wert.
  CPL, Zeitraum, Anfrage- und Abschlussquote haben direkt sichtbaren Kontext.
  Die bestehende Indexierungssperre der separaten Fallstudie bleibt erhalten.
- Das Übergabemuster verwendet das eigene Projekt und ist ausdrücklich keine
  veröffentlichte Agenturreferenz. Ein kundenspezifisches Abnahmeprotokoll oder
  Tracking-Messprotokoll wird damit nicht vorgetäuscht.
- Hero-Links führen zu Belegen. Der Abschluss hat drei getrennte Aktionen:
  direkte Projektanfrage, White-Label-Aufgabe und Energy-Marktcheck. Die
  Formulare und ihre Verarbeitung bleiben bei den bestehenden Routen.
- Blocksy besitzt den Hauptbereich `#main`; das Template ergänzt nur
  `#results-content`. Die bestehenden Abschnittsanker bleiben erhalten.

Tracking-Actions bleiben bei gleicher Handlung erhalten. Neu sind
`results_hero_to_wordpress`, `results_proof_migration_scope`,
`results_proof_migration_checks` und `results_proof_request_routing`.
`results_proof_tracking_page` führt weiter zum Tracking-Angebot, steht jetzt
aber im Abschnitt `whitelabel`; es ist kein Beleg für aktives Live-Tracking.
`cta_results_next_unsure` entfällt mit dem zusätzlichen allgemeinen
Anfrage-Link. Die drei segmentierten Abschluss-Actions sowie Case-,
Referenz-, PageSpeed-, GitHub- und Angebotslinks bleiben bestehen.
Keine externe Event-Konfiguration und kein Analytics-Runtime wurde verändert.

## Cluster rules

### 1. Energy cluster -> Marktcheck

Use the Marktcheck as primary next action when the page is clearly about:

- Solar / Photovoltaik / PV leads
- Wärmepumpen leads
- Speicher lead generation
- lead portals / lead buying / portal dependency
- provider comparisons such as Aroundhome, Checkfox, Wattfox, DAA
- Solar lead costs, CPO/CPL and own lead generation versus portals
- Solar-specific funnel architecture and qualification

Examples:

- `/solar-leads-kaufen-alternative/`
- `/solar-leads-kosten-studie/`
- `/cost-per-lead-photovoltaik/`
- `/eigene-leadgenerierung-vs-portale/`
- `/waermepumpen-leads/`
- `/lead-funnel-solar/`
- `/qualifizierte-pv-anfragen/`
- `/kunden-gewinnen-solarteure/`
- `/b2b-solar-leads/`
- `/aroundhome-solar-einordnung/`
- `/checkfox-solar-waermepumpe-einordnung/`
- `/wattfox-solar-leads-einordnung/`
- `/daa-photovoltaik-leads-einordnung/`

### 2. Direct WordPress / tracking / CRO -> project request

Use the generic project route when the visitor is evaluating an implementation problem rather than the energy vertical.

Typical topics:

- WordPress development
- Landingpages / funnel pages
- Server-Side Tracking
- GA4 / GTM / attribution
- Conversion optimization
- technical SEO
- WordPress performance / Core Web Vitals

Do **not** route these pages to the Solar Marktcheck merely because `hu_get_request_analysis_url()` exists as a shared helper.

Cross-route pages that serve all three paths use the generic project request as
well, even when they are not themselves an implementation page:

- `/hasim-uener/` — the final project request uses the direct-project path; the agency bridge leads to White-Label
- `/glossar/` — definitional layer below every cluster
- the technical-SEO cornerstone template (`page-seo-cornerstone.php`)

Use `hu_get_commercial_route( 'project_request' )` for those, with the label
`Projekt anfragen`.

Three Marktcheck links on non-energy pages are deliberate segmentation, not
misrouting, and stay: the energy branch in `page-wordpress-agentur.php`, the
energy branch in the `page-server-side-tracking-b2b.php` hero, and the energy
card in the `page-ergebnisse.php` close. All three name the energy vertical
explicitly before they hand off.

### 3. Agency intent -> White-Label

Use White-Label as primary route when intent explicitly involves:

- agency delivery capacity
- implementation under the agency brand
- NDA / invisible delivery
- partner / subcontractor / external implementation
- repeatable client project support

A specialist page may show a **secondary** White-Label bridge when the topic is relevant to agencies, but it should not lose its own SEO intent.

### 4. Local Agentur search intent stays separate

`/wordpress-agentur-hannover/` owns `wordpress agentur hannover` and related local variants. This SEO route must not be redirected to the Freelancer page.

The visible route switch to `/wordpress-freelancer-hannover/` is an intent correction for visitors who actually want direct collaboration.

## SEO ownership is independent

`docs/seo/query-ownership.csv` remains the authoritative query registry.

Changing a CTA does **not** automatically change:

- canonical URL
- page title / H1 owner
- internal SEO anchor ownership
- sitemap inclusion
- redirects

Changing query ownership requires separate evidence and a separate decision.

## Primary CTA destinations

### Generic project request

Canonical helper: `hu_get_navigation_project_request_url()`

Expected destination:

`/kontakt/?type=project`

Use outside the dedicated Solar and White-Label funnels. Without a `focus`
parameter the contact page shows its topic step and always shows the switch to
White-Label and Marktcheck. Add a `focus` only where the linking page already
answered the topic question (offer cards, tracking pages); a pre-set focus
skips the topic step. Until 2026-09-22 the generic CTA carried
`focus=implementation_scope`, which labelled every generic lead as
"Umsetzung / Optimierung".

Die frühere lokale Formularausnahme der Freelancer-Seite entfällt mit ihrer
Konsolidierung auf `/`. Homepage, Header und Footer verwenden den gemeinsamen
Kontaktweg; Angebotslinks tragen die passende Vorauswahl. Alte Formularanker
zeigen auf den Anfrageabschluss der Homepage.

### Solar Marktcheck

Canonical helper: `hu_get_request_analysis_url()`

Expected destination:

`/solar-waermepumpen-leadgenerierung/#marktcheck`

Use only for Energy-cluster purchase / diagnosis intent.

### White-Label

Canonical page helper where available: `nexus_get_whitelabel_page_url()`

Expected route:

`/whitelabel-retainer/`

The page owns its own local request form (`nexus/v1/whitelabel-request`) and its own success event `whitelabel_request_submit`.

The primary action is “Aufgabe beschreiben” and leads to `#aufgabe`. The paid
WordPress test sprint is the smallest scoped entry; a retainer follows a successful
first project. Presales uses the same form with `?case=angebotsphase` and a visible
context label. The calendar remains a secondary option. Both required fields
(task and email), optional timeframe/access, REST payload and success event are
preserved. Without the form script, an explicit email fallback remains available.

### Footer: Selbstauskunft statt Sammel-CTA

The global footer does not carry one CTA for everybody any more. By default it asks the
visitor to say who they are, and each of the three sentences routes into the
matching cluster above:

| Sentence | Destination | Event |
|---|---|---|
| Ich bin **Agentur** … | `/whitelabel-retainer/` | `cta_footer_pick_agency` |
| Ich bin **Solar- oder Wärmepumpenbetrieb** … | `/solar-waermepumpen-leadgenerierung/` | `cta_footer_pick_energy` |
| Ich habe **eine Seite** … | `/` | `cta_footer_pick_project` |

All three carry `data-track-category="lead_gen"` and
`data-track-section="footer"`. The direct line under them adds three more, all
`lead_gen`:

| Direct path | Destination | Event |
|---|---|---|
| E-Mail | `mailto:` the canonical address | `cta_footer_mail` |
| Telefon | `tel:` the canonical number | `cta_footer_tel` |
| Kontaktformular | `/kontakt/` | `cta_footer_form` |

On the front page the three self-selection sentences are omitted: direct
projects are already the primary offer and specialist bridges are visible.
Mail and telephone remain; `Kontaktformular` leads to the shared contact page.
The directory and sender lines remain. Existing tracking action names on
rendered links are unchanged; `cta_footer_pick_*` are absent on the homepage.

Below that the footer has one directory line and one sender line, no columns:

| Directory link | Destination | Event | Category |
|---|---|---|---|
| Über Haşim | `/hasim-uener/` | `cta_footer_nav_about` | `navigation` |
| Fallstudie Solar | `/case-study-solar-leadgenerierung/` | `cta_footer_nav_case_study_proof` | `trust` |
| Blog | `/blog/` | `cta_footer_nav_insights` | `navigation` |
| Glossar | `/glossar/` | `cta_footer_nav_glossary` | `navigation` |
| Impressum | `/impressum/` | `cta_footer_nav_imprint` | `navigation` |
| Datenschutz | `/datenschutz/` | `cta_footer_nav_privacy` | `navigation` |

Retired with the CTA band and the merged minimal footers:
`cta_footer_primary`, `cta_footer_primary_mobile`, `cta_footer_route_*`,
`cta_footer_min_route_*`, `cta_energy_footer_analysis`,
`cta_audit_footer_analysis`, `cta_footer_nav_project` and
`cta_footer_social_github`.

Retired with the two-volume footer, when the service and proof columns went:
`cta_footer_nav_freelancer`, `cta_footer_nav_tracking`, `cta_footer_nav_energy`,
`cta_footer_nav_agentur`, `cta_footer_nav_results`, `cta_footer_nav_whitelabel`
and `cta_footer_nav_contact`. Their targets were all already linked from the
header menu in the same document, or are reachable from the direct line above.
`cta_footer_direct_mail` and `cta_footer_direct_phone` were replaced by
`cta_footer_mail` and `cta_footer_tel`; that is a deliberate rename, so the
direct-line series restarts here. The three `cta_footer_pick_*` values and the
four surviving `cta_footer_nav_*` values are unchanged, so route reporting and
directory reporting stay comparable across the rebuild.

Note for the Energy cluster: the footer no longer repeats the Marktcheck CTA.
The Marktcheck stays the cluster's primary action on
`/solar-waermepumpen-leadgenerierung/#marktcheck` and in
`template-parts/footer-cta.php`; the footer's job is the self-selection above.

## Migration checklist

When auditing an existing page:

1. Identify its query owner from `docs/seo/query-ownership.csv`.
2. Classify audience: Energy, direct project, agency, proof/information, mixed.
3. Keep the owning URL unless there is separate SEO evidence for a migration.
4. Replace only the CTA destination / copy that conflicts with this routing contract.
5. Preserve tracking attributes and give new actions explicit event names.
6. Check footer, sticky CTA, reusable partials and helper-generated links, not only the hero button.
7. Run repo drift checks before merge.

## Known migration hotspots

These areas must be audited because the old architecture used the Marktcheck broadly:

- `hu_get_request_analysis_url()` call sites outside Energy pages
- `nexus_get_primary_public_url_map()` keys whose names imply generic actions but currently resolve to the Marktcheck
- `template-parts/footer-cta.php`
- `template-parts/seo-subpage-sticky-cta.php`
- `single.php` and category/archive CTAs
- specialist service pages such as Server-Side Tracking
- `llms.txt` and dynamic `inc/llms-txt.php`
- Organization / WebSite / Service schema descriptions and offer catalogs

## Guardrail

If intent is unclear, do not guess based on the current business priority. Keep the page's SEO owner stable and prefer a neutral `Projekt anfragen` route until the page is explicitly classified.
