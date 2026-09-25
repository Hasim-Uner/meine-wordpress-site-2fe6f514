# Conversion Routing Architecture

Status: active decision, updated 2026-09-25

This document separates **SEO ownership** from **conversion routing**. It exists to prevent a recurring failure mode: sending every page to the same funnel or moving a ranking query owner merely because another page has the preferred CTA.

## Core rule

**Search intent -> owning page -> next best action.**

Not:

**Search intent -> whichever landing page is currently the main sales focus.**

A page can remain the canonical SEO destination for its query while its CTA routes to a different next step.

## Commercial routes

| Route | Primary audience / intent | Page role | Primary CTA | Secondary CTA / bridge |
| --- | --- | --- | --- | --- |
| `/` | Brand and direct WordPress/Freelancer intent | Homepage and direct WordPress money page | `Projekt anfragen` with offer-specific focus; while the Ersteinschätzung experiment is switched on, hero and close lead with it (`/kontakt/?focus=ersteinschaetzung`) and keep the project request beside it | Proof / White-Label / Solar / tracking specialist |
| `/wordpress-freelancer-hannover/` | Retired direct-client route | 301 to `/`; excluded from sitemap | Homepage takes over content and query ownership | Legacy content anchors remain on `/` |
| `/whitelabel-retainer/` | Agencies seeking delivery capacity | Agency money page | White-Label request form (`?case=aufgabe` / `?case=angebotsphase` / `?case=vormerken`) or scoped first project | 30-minute call / proof |
| `/solar-waermepumpen-leadgenerierung/` | Solar, heat-pump and storage businesses | Energy vertical money page | Marktcheck | Solar proof / case study |
| `/server-side-tracking-b2b/` | Server-Side Tracking commercial intent | Specialist tracking money page (route `tracking_b2b`); linked as „Server-Side Tracking“, never as plain „Tracking“ | Tracking project request / scope clarification | White-Label bridge for agencies |
| `/ga4-tracking-setup/` | Tracking purchase intent: GA4/GTM setup, consent, ads conversions | Tracking offer page; target of the header item „Tracking“ and the footer way (route `tracking_setup`) | `Tracking-Projekt anfragen` → `/kontakt/?type=project&focus=tracking` | Tracking specialist / project evidence |
| `/performance-marketing/` | B2B companies running Google Ads or Meta | Paid-demand money page (measurement → landing page → budget) | `Ausgangslage prüfen lassen` → `/kontakt/?type=project` | Tracking setup, landing pages (`/#angebot-funnel`), case study; performance agencies → White-Label task (`?type=whitelabel&case=aufgabe`) |
| `/wordpress-agentur-hannover/` | Local `wordpress agentur hannover` search intent | Local SEO acquisition page | Project request | Explicit bridge to Freelancer page |
| `/ergebnisse/` | Retired proof hub (since 2026-09-25) | 301 to `/case-study-solar-leadgenerierung/`; excluded from sitemap and `llms.txt` | none | Menu item „Ergebnisse“ → `/#arbeiten`; route `results` → case study |
| `/case-study-solar-leadgenerierung/` | Solar proof | Evidence page | Solar Marktcheck | Energy money page |

## Glossar: erst erklären, dann Projektbezug

`/glossar/` führt über alphabetische Begriffe auf vorhandene Detail- oder
Themenseiten. Die allgemeine Projektanfrage steht am Abschluss
(`cta_glossary_hub_project`, Kategorie `lead_gen`). Auf den Detailseiten
bleibt `cta_glossary_term_project` mit Kategorie `project` am Abschluss;
`cta_glossary_hero_project` entfällt zusammen mit dem früheren Hero-CTA.
Beide verbleibenden Links nutzen die kanonische Route `project_request`.
Verwandte Begriffe führen auf aktuell verfügbare Ziele; fehlende Detailseiten
werden ausgelassen. Allgemeine Performance-Begriffe verweisen auf passende
WordPress-Leistungen. Solar-Themeneinträge behalten ihre fachlichen Ziele.

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
Suchintention der früheren Freelancer-Route. Seit 2026-09-24 ist die Seite als
Strecke gebaut: Hero mit Messprotokoll, Prüfstand, die sechs Stationen einer
Anfrage, drei Leistungen mit Preisen, Arbeiten, Übergabe, Fragen und der
Anfrageblock als Ende der Messlinie. White-Label und Solar/Wärmepumpe sind
leise Nebenausgänge unter den Leistungen (Hooks `home_door_whitelabel`,
`home_door_energy`), keine Weiche im Hero. Der Ausgang für Agenturen heißt seit
2026-09-25 „Für Agenturen und Webdesigner“ mit dem Link „Technik und Tracking
für Ihre Kunden“. Der Solar-Fall ist abgegrenzter Proof. Die Belegzeilen im
Hero führen auf Fall, Referenzen und seit 2026-09-25 auf die Preise
(`home_proof_strip_case`, `home_proof_strip_references`,
`home_proof_strip_price` → `#angebote`, Kategorie `proof`).

Die Hero- und Abschluss-CTAs führen zur kanonischen Projektanfrage
`/kontakt/?type=project` **ohne** vorbelegtes Thema (seit 2026-09-22): Die
Kontaktseite zeigt dann die Themenwahl und die Weiche zu White-Label und
Marktcheck. Nur die drei Leistungen verwenden
`hu_get_contact_intake_url('project', focus)` mit `relaunch`, `tracking` oder
`implementation_scope` (Übernahme-Check); dort ist die Themenfrage schon
beantwortet, und der Kontaktablauf überspringt sie. Die Homepage hat kein
eigenes Formular; die Messlinie endet am Anfrageblock, dessen Buttons auf
`/kontakt/` führen. `#anfrage` und `#kontakt` bleiben als Anker des
Abschlussblocks erhalten. `#angebot-funnel` (früher das Angebot
„Anfragestrecken“, sitewide von CRO-Links verlinkt) sitzt seit 2026-09-24 auf
der Stationsliste in Abschnitt 03.

**Versuch Ersteinschätzung (8 Wochen ab Deploy, Schalter
`HU_EXPERIMENT_ERSTEINSCHAETZUNG` im Kanon `inc/canon/messaging-canon.php`):**
Solange der Schalter an ist, ist in Hero und Abschluss die Ersteinschätzung
der primäre Button (`hu_first_assessment_url()` →
`/kontakt/?focus=ersteinschaetzung`, Hooks `home_head_ersteinschaetzung` und
`home_close_ersteinschaetzung`). Die Projektanfrage bleibt mit Ziel und Hooks
unverändert als sekundärer Button daneben. Auf `/kontakt/` wählt der
Parameter das Anliegen vor, die Website-URL ist dort Pflicht. Mails dazu
tragen das Betreff-Präfix aus dem Kanon, das ist die einzige Zählstelle.
Schalter aus: Startseite und `/kontakt/` rendern wie vorher. Laufzeit,
Messung und Abbruchregel: `docs/experimente/ersteinschaetzung.md`.

Routing, Inhalte, Weiterleitung, Analytics-Zuordnung und Nachkontrolle:
`docs/decisions/homepage-freelancer-konsolidierung.md`.

## Blog-Cornerstone `/website-relaunch/`

Der Beitrag besitzt die informationelle Query „website relaunch“
(`docs/seo/query-ownership.csv`) und führt Leser mit Relaunch-Vorhaben direkt
in die Projektanfrage, nicht auf `/wordpress-agentur-hannover/`:

- **Kontextbrücke** im Reader (`template-parts/single-reader.php`, Override
  wie beim Auslagerungs-Leitfaden): primär „Relaunch-Projekt anfragen“ →
  `hu_get_contact_intake_url('project', 'relaunch')`.
- **Abschluss-Tafel** (`[hu_abschluss]`, `inc/editorial-bausteine.php`):
  primär die Ersteinschätzung (`hu_first_assessment_url()`, Hook
  `blog_relaunch_close_ersteinschaetzung`), sekundär „Relaunch-Projekt
  anfragen“ (`/kontakt/?type=project&focus=relaunch`, Hook
  `blog_relaunch_close_project`), beide `data-track-category="lead_gen"`,
  `data-track-section="abschluss"`. Schalter der Ersteinschätzung aus: nur die
  Projektanfrage als primärer Button.

Kein Marktcheck: Der Beitrag steht im Dossier `cro`, nicht im Energie-Pfad.

## Über Haşim: persönliche Methodik und Belege

`/hasim-uener/` erklärt Person und Arbeitsweise; Leistungen und Preisrahmen
bleiben auf der Homepage. Die Seite führt durch persönlichen Hintergrund,
Engpass-Priorisierung, Defekt/Gestaltung/Hypothese, unterschiedliche
Besucherbedürfnisse, Gestaltung und Übergabe. Ein kanonischer Solar-Fall und
eine öffentliche WordPress-Arbeit belegen den Ansatz mit getrenntem Kontext.

- Hero: `about_read_method` springt zu `#arbeitsweise` (Navigation).
- Besucherbeispiele: direkte Anbieterwahl → `/#angebote`
  (`link_about_freelancer`), Lösungsprüfung → `/case-study-solar-leadgenerierung/`
  (`about_view_results`, bis 2026-09-25 `/ergebnisse/`), Orientierung → `/blog/` (`about_read_expertise`).
- Abschluss: `cta_about_project` → kanonische Projektanfrage;
  `link_about_whitelabel` → eigene White-Label-Seite.
- `about_station_solar_case` bleibt als Aktion erhalten; der Beleg steht jetzt
  im Abschnitt `about_method`. Neue Beleg-Links: `about_reference_open` und
  `about_code_history`. Bestehende Profil-/Mail-Actions bleiben erhalten.
- Native `<details>` erläutern Besucherbedürfnisse ohne zusätzliche
  JavaScript-Laufzeit. Profil, Kontaktformular, Consent und CRM bleiben in ihren
  bisherigen Zuständigkeiten. Kein Solar-Marktcheck als allgemeiner Seiten-CTA.

## Ergebnisse-Hub: Arbeitsbelege für die drei Geschäftspfade

Stillgelegt seit 2026-09-25: `/ergebnisse/` leitet per 301 auf die Fallstudie,
der Menüpunkt „Ergebnisse“ führt auf `/#arbeiten`. Der folgende Stand
beschreibt `page-ergebnisse.php`, das im Repo bleibt, aber nicht mehr
ausgeliefert wird.

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
context label; since 2026-09-26 it is the hero's quiet second path
(`cta_whitelabel_hero_offer`). Agencies without a current project use
`?case=vormerken` (`cta_whitelabel_way_later`, beside the form only); the form
swaps label, field text, hint, validation message and button per case. The
calendar is a secondary option beside the form only (`cta_whitelabel_form_call`).
Both required fields (task and email), optional timeframe/access, REST payload and
success event are preserved. Without the form script, an explicit email fallback
remains available.

Since 2026-09-25 the page runs on the homepage's Strecke system and has no
breadcrumb or wayfinding layer. Hooks kept from the previous version:
`nav_whitelabel_home|services|pricing|proof|faq`, `cta_whitelabel_header_task_brief`,
`cta_whitelabel_hero_task_brief`, `cta_whitelabel_entry_task_brief`,
`cta_whitelabel_proof_test_sprint`, `whitelabel_proof_repo`, `faq_whitelabel_open`,
`cta_whitelabel_way_offer`, `cta_whitelabel_form_call`,
`cta_sticky_whitelabel_task_brief`, `nav_whitelabel_footer_*`. New:
`nav_whitelabel_process`, `whitelabel_proof_ci`,
`whitelabel_proof_pagespeed`, `whitelabel_margin_reference`,
`whitelabel_reference_open`, `whitelabel_about`. Since 2026-09-26
`nav_whitelabel_proof` labels „Belege“ (`#proof`: test bench and references in
one section), `nav_whitelabel_process` labels „Ablauf“; `cta_whitelabel_hero_call`
was replaced by `cta_whitelabel_hero_offer`, and `cta_whitelabel_way_later` is new.

### Header (seit 2026-09-25)

Quelle: `hu_get_site_header_navigation_contract()` in
`blocksy-child/inc/commercial-routing.php`. Kopfzeile und Klappblatt rendern
dieselbe Liste in derselben Reihenfolge; das gespeicherte WordPress-Menü, die
404-Seite und das SEO Cockpit lesen denselben Contract. Geprüft von
`scripts/tests/navigation-contract.php` (CI).

| Punkt | Ziel | Event |
|---|---|---|
| Leistungen | `/#angebote` | `nav_header_freelancer` |
| Tracking | `/ga4-tracking-setup/` (Route `tracking_setup`) | `nav_header_tracking` |
| White-Label | `/whitelabel-retainer/` | `nav_header_whitelabel` |
| Solar & Wärmepumpe | `/solar-waermepumpen-leadgenerierung/` | `nav_header_solar` |
| Ergebnisse | `/#arbeiten` | `nav_header_results` |
| Über Haşim | `/hasim-uener/` | `nav_header_about` |
| CTA „Projekt anfragen“ | `/kontakt/?type=project` | `nav_header_project` |

Reihenfolge: erst was angeboten wird (Leistungen, Tracking), dann die Wege für
bestimmte Absender (Agenturen, Energiebetriebe), dann Belege und Person. Der
Punkt „Tracking“ führt auf das Tracking-Angebot, dieselbe Leistung, die die
Startseite als „Tracking bis ins CRM“ mit Preis verkauft. Die Server-Side-Seite
bleibt Query-Owner für Server-Side-Tracking-Suchen und wird mit genau diesem
Namen verlinkt (Fuß, GA4-Seite, White-Label-Margenblock), nie als bloßes
„Tracking“. `aria-current="page"` steht nur auf dem Link, der die aufgerufene
Seite ist; liegt die Seite nur im Bereich eines Punkts (Server-Side-Seite unter
Tracking, Fallstudie unter Ergebnisse), steht `aria-current="true"`.

Unter 1081 px bleibt der CTA in der Kopfzeile sichtbar, unter 480 px als
„Anfragen“. Nur im Klappblatt steht er unter 340 px, auf der Startseite (der
Hero führt dort schon mit zwei Buttons auf die Anfrage) und auf Seiten mit
eigener Sticky-CTA-Leiste (unter 761 px). Ohne JavaScript ist das Klappblatt
offen.

### Footer: Selbstauskunft statt Sammel-CTA

Quelle: `hu_get_site_footer_navigation_contract()` in
`blocksy-child/inc/commercial-routing.php`, gerendert von
`template-parts/site-footer.php`. Der Fuß trägt keinen Sammel-CTA. Er fragt,
wer der Besucher ist, in derselben Reihenfolge wie der Kopf:

| Satz | Ziel | Event |
|---|---|---|
| Ich habe **eine Website** … | `/` | `cta_footer_pick_project` |
| Ich brauche **belastbare Messung** … | `/ga4-tracking-setup/` | `cta_footer_pick_tracking` |
| Ich bin **Agentur** … | `/whitelabel-retainer/` | `cta_footer_pick_agency` |
| Ich bin **Solar- oder Wärmepumpenbetrieb** … | `/solar-waermepumpen-leadgenerierung/` | `cta_footer_pick_energy` |

Alle tragen `data-track-category="lead_gen"` und `data-track-section="footer"`.
Der eigene Weg entfällt auf seiner Route (beide Tracking-Seiten zählen als
Tracking). Auf `/`, `/kontakt/` und der Energie-Money-Page entfällt die Wahl
ganz. Darunter die Direktzeile, ebenfalls `lead_gen`:

| Direct path | Destination | Event |
|---|---|---|
| E-Mail | `mailto:` the canonical address | `cta_footer_mail` |
| Telefon | `tel:` the canonical number | `cta_footer_tel` |
| Kontaktformular | `/kontakt/` | `cta_footer_form` |

Darunter das Verzeichnis in vier leisen Gruppen. Es führt, was der Kopf nicht
führt: Fachseiten mit eigener Query-Ownership, Belege, Wissen, Rechtliches.
Ein Eintrag, der die aufgerufene Seite ist, trägt `aria-current="page"`.

| Gruppe | Link | Destination | Event | Category |
|---|---|---|---|---|
| Leistungen | Server-Side Tracking | `/server-side-tracking-b2b/` | `cta_footer_nav_server_side_tracking` | `navigation` |
| Leistungen | Performance Marketing | `/performance-marketing/` | `cta_footer_nav_performance_marketing` | `navigation` |
| Leistungen | WordPress Agentur Hannover | `/wordpress-agentur-hannover/` | `cta_footer_nav_agentur_local` | `navigation` |
| Belege & Person | Solar-Fallstudie | `/case-study-solar-leadgenerierung/` | `cta_footer_nav_case_study_proof` | `trust` |
| Belege & Person | Über Haşim | `/hasim-uener/` | `cta_footer_nav_about` | `navigation` |
| Wissen | Blog | `/blog/` | `cta_footer_nav_insights` | `navigation` |
| Wissen | Glossar | `/glossar/` | `cta_footer_nav_glossary` | `navigation` |
| Rechtliches | Impressum | `/impressum/` | `cta_footer_nav_imprint` | `navigation` |
| Rechtliches | Datenschutz | `/datenschutz/` | `cta_footer_nav_privacy` | `navigation` |

Neu seit 2026-09-25: `cta_footer_nav_server_side_tracking` und
`cta_footer_nav_performance_marketing`. Die Server-Side-Seite verlor mit dem
Kopfpunkt ihren seitenweiten Link und bekommt ihn hier mit ihrem eigenen
Ankertext zurück; `/performance-marketing/` (Query-Owner seit 2026-09-22) hatte
bis dahin gar keinen. Der frühere Wert `cta_footer_nav_tracking` bleibt
stillgelegt, damit seine Zeitreihe nicht mit einem anderen Ziel weiterläuft.

Retired with the CTA band and the merged minimal footers:
`cta_footer_primary`, `cta_footer_primary_mobile`, `cta_footer_route_*`,
`cta_footer_min_route_*`, `cta_energy_footer_analysis`,
`cta_audit_footer_analysis`, `cta_footer_nav_project` and
`cta_footer_social_github`.

Retired with the two-volume footer, when the service and proof columns went:
`cta_footer_nav_freelancer`, `cta_footer_nav_tracking`, `cta_footer_nav_energy`,
`cta_footer_nav_agentur`, `cta_footer_nav_results`, `cta_footer_nav_whitelabel`
and `cta_footer_nav_contact`. `cta_footer_direct_mail` and
`cta_footer_direct_phone` were replaced by `cta_footer_mail` and
`cta_footer_tel`.

### 404

`404.php` bietet dieselben Wege wie der Kopf: Startseite, die vier Routen aus
dem Header-Contract und den Blog (`404_nav_home`, `404_nav_freelancer`,
`404_nav_tracking`, `404_nav_whitelabel`, `404_nav_solar`, `404_nav_blog`).
Entfallen sind `404_nav_audit` (Marktcheck als seitenweiter Einstieg) und
`404_nav_seo` (Anker, den die Zielseite nicht mehr hat).

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
