# Conversion Routing Architecture

Status: active decision, updated 2026-09-26

This document separates **SEO ownership** from **conversion routing**. It exists to prevent a recurring failure mode: sending every page to the same funnel or moving a ranking query owner merely because another page has the preferred CTA.

## Core rule

**Search intent -> owning page -> next best action.**

Not:

**Search intent -> whichever landing page is currently the main sales focus.**

A page can remain the canonical SEO destination for its query while its CTA routes to a different next step.

## Commercial routes

| Route | Primary audience / intent | Page role | Primary CTA | Secondary CTA / bridge |
| --- | --- | --- | --- | --- |
| `/` | Brand and direct WordPress/Freelancer intent | Homepage and direct WordPress money page | `Projekt anfragen` in the hero; offer-specific project requests in price rows. With the Ersteinschätzung experiment on, header, case, the assessment card after offers and page close retain `/kontakt/?focus=ersteinschaetzung` | Scope/prices, proof / White-Label / Solar / tracking specialist |
| `/wordpress-freelancer-hannover/` | Retired direct-client route | 301 to `/`; excluded from sitemap | Homepage takes over content and query ownership | Legacy content anchors remain on `/` |
| `/whitelabel-retainer/` | Agencies seeking delivery capacity | Agency money page | White-Label request form (`?case=aufgabe` / `?case=angebotsphase` / `?case=vormerken`) or scoped first project | 30-minute call / proof |
| `/solar-waermepumpen-leadgenerierung/` | Solar, heat-pump and storage businesses | Energy vertical money page | Paid Marktcheck (99 €) for cold intent; configurator for the enquiry system from 9.999 € | Sofortkontakt routes to the separate contact path; Solar proof / case study |
| `/server-side-tracking-b2b/` | Server-Side Tracking commercial intent | Specialist tracking money page (route `tracking_b2b`); linked as „Server-Side Tracking“, never as plain „Tracking“ | Tracking project request / scope clarification | White-Label bridge for agencies |
| `/ga4-tracking-setup/` | Tracking purchase intent: GA4/GTM setup, consent, ads conversions | Tracking offer page; target of the header item „Tracking“ and the footer way (route `tracking_setup`) | `Tracking-Projekt anfragen` → `/kontakt/?type=project&focus=tracking` | Tracking specialist / project evidence |
| `/performance-marketing/` | B2B companies running Google Ads or Meta | Paid-demand money page (measurement → landing page → budget) | `Ausgangslage prüfen lassen` → `/kontakt/?type=project` | Tracking setup, landing page offer (`/landingpage-erstellen-lassen/`, `perf_to_landingpage_offer`; the former `perf_to_landingpages` → `/#angebot-funnel` is retired), case study; performance agencies → White-Label task (`?type=whitelabel&case=aufgabe`) |
| `/landingpage-erstellen-lassen/` | Direct clients who need one page for one offer (`landingpage erstellen lassen`) | Fixed-price product page (route `landingpage`, since 2026-09-26) | `Landingpage anfragen` → `/kontakt/?type=project&focus=conversion` (`cta_lp_offer_hero_project`, `cta_lp_offer_scope_project`, `cta_lp_offer_close_project`) | Tracking setup and website offer as add-ons, case study; agencies → White-Label (`lp_offer_hero_whitelabel`) |
| `/conversion-optimierung/` | B2B websites with visitors but too few matching inquiries (`conversion optimierung b2b`) | Written finding of the inquiry path (Anfragesystem-Analyse), then fixed-price implementation (route `conversion`, since 2026-09-30) | `Analyse anfragen` → `/kontakt/?type=analysis&focus=conversion` (`cro_offer_cta_hero_analysis`, `cro_offer_cta_analysis_scope`, `cro_offer_cta_close_analysis`) | Landingpage and tracking setup as price rows (`cro_offer_to_landingpage`, `cro_offer_to_tracking`), case study (`cro_offer_proof_case`); no footer door register on this page; no Marktcheck CTA. New direct-contact links in the close: `cro_offer_close_mail`, `cro_offer_close_tel`. Since 2026-10-01 the former `cro_offer_hero_whitelabel`, `cro_offer_to_website` and `cro_offer_reference_open` are retired (hero meta boxes, website row and reference list are gone) |
| `/wordpress-website-erstellen-lassen/` | Direktkunden, WordPress-Website erstellen lassen | Die Anfrage-Website: Produkt, Umfangsrechner, Festpreis und Bauzeit | `/kontakt/?type=project&focus=website&seiten=N&art=neubau\|relaunch` plus `tracking=1` bei Auswahl; Positionen hero/angebot/beleg/leiste/abschluss | Vergleich, Belege und Bauzeit; Kontextlink von der lokalen Agenturseite |
| `/wordpress-agentur-hannover/` | Local `wordpress agentur hannover` search intent | Local SEO acquisition + decision hub: Agenturmodell vs. direkte Verantwortung | Project request without preset focus (`hu_get_navigation_project_request_url()`, since 2026-09-26) | Context owners: Website, Conversion Tracking, Conversion-Optimierung; proof: public references `/#referenzen` and case study |
| `/ergebnisse/` | Retired proof hub (since 2026-09-25) | 301 to `/case-study-solar-leadgenerierung/`; excluded from sitemap and `llms.txt` | none | Menu item „Ergebnisse“ → `/#arbeiten`; route `results` → case study |
| `/case-study-solar-leadgenerierung/` | Solar proof; since 2026-09-25 also the proof target of homepage, menu item „Ergebnisse“, tracking and agency pages | Evidence page | Solar Marktcheck | Non-energy readers: quiet project request below the Marktcheck (`cta_case_study_to_project`, since 2026-09-26) |

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

GA4 verwendet für Hero, Angebot und Abschluss dieselbe direkte Projektanfrage
mit dem Fokus `tracking` (Action `cta_tracking_project`, je Abschnitt über
`data-track-section` unterscheidbar). Der zentrale Kontaktablauf übernimmt die
Vorauswahl. Die früheren Actions `cta_cluster_audit`,
`cta_cluster_proof_audit` und `cta_cluster_footer_audit` stehen nicht mehr im
Template.

## Tracking-Leiter: ein Produkt, vier Stufen

Seit 2026-09-26 (`docs/decisions/tracking-preisleiter.md`): Name, Umfang,
Preis und Lieferzeit jeder Stufe stehen einmal in
`hu_tracking_product_ladder()` (`inc/canon/pricing-canon.php`). Die Seiten
zeigen Ausschnitte, keine eigenen Fassungen:

| Oberfläche | Zeigt | Ziel des CTA |
|---|---|---|
| Startseite, Angebot 02 und Station 04 | Stufe 1 als Angebot, Stufen 2 bis 4 als Satz | `/kontakt/?type=project&focus=tracking` (`home_offer_tracking`) |
| `/ga4-tracking-setup/` | alle vier Stufen (Karten `#stufe-1` bis `#stufe-4`) | `/kontakt/?type=project&focus=tracking` (`cta_tracking_project`) |
| `/server-side-tracking-b2b/` | Stufen 2 bis 4, Stufe 1 als Verweis (`cta_package_to_measurement` → `/ga4-tracking-setup/#stufe-1`) | eigenes Formular `#anfrage` (`cta_package_standard`, `cta_package_pro`, `cta_package_individual`) |
| `/performance-marketing/` | Einstiegspreis der Messung | `/kontakt/?type=project` |
| `/whitelabel-retainer/`, Margenblock | Endkundenpreis von Stufe 2 neben dem Agenturpreis | Formular `#aufgabe` |

Tracking Care hängt nur an den Stufen 2 bis 4. Die Guard-Regel
`preis-tracking-leiter` und `npm run test:pricing` halten die Leiter
zusammen.

## Homepage: direkter Freelancer-Einstieg

Seit der Betreiberentscheidung vom 2026-09-13 übernimmt `/` die Inhalte und
Suchintention der früheren Freelancer-Route. Die finale Template-Fassung vom
2026-10-02 ordnet die vorhandene Strecke neu: Klick → Strecke → Fall →
Prüfstand mit Referenzen → Preise → Übergabe → Fragen → Anfrage. Der Fall steht
vor den Preisen; Hero samt Kopf und Messtafel sind die dunklen Messinstrumente.
White-Label und Solar/Wärmepumpe stehen unter „Wann ein anderer Weg besser
passt“ in der Übergabe (`home_door_whitelabel`, `home_door_energy`).

Hero und Abschluss nutzen die Projektanfrage `/kontakt/?type=project` ohne
Themenvorbelegung, wenn die Ersteinschätzung aus ist. Die fünf Leistungen
nutzen ihre passenden Fokusziele: Website `website`, Landingpage `conversion`,
Analyse `type=analysis&focus=conversion`, Tracking `tracking`, Übernahme
`implementation_scope`. Produktseiten vertiefen den Umfang. Die Website-Zeile
verlinkt seit 2026-10-01 `/wordpress-website-erstellen-lassen/`
(`home_offer_website_detail`). Die Homepage hat kein eigenes Formular.
`#angebot-funnel` bleibt an der Stationsliste; `#arbeiten`, `#systemprojekt`,
`#referenzen`, alle `#angebot-*`, `#anfrage` und `#kontakt` bleiben erhalten.

| Zeile | Anfrage-Hook (`lead_gen`) | Ziel | Sub-CTA „Was drinsteckt“ (`navigation`) |
| --- | --- | --- | --- |
| 01 Website und Relaunch | `home_offer_relaunch` | `/kontakt/?type=project&focus=website` | `home_offer_website_detail` → `/wordpress-website-erstellen-lassen/` |
| 02 Landingpage | `home_offer_landingpage` | `/kontakt/?type=project&focus=conversion` | `home_offer_landingpage_detail` → `/landingpage-erstellen-lassen/` |
| 03 Conversion-Analyse | `home_offer_analysis` | `/kontakt/?type=analysis&focus=conversion` | `home_offer_analysis_detail` → `/conversion-optimierung/` |
| 04 Tracking | `home_offer_tracking` | `/kontakt/?type=project&focus=tracking` | `home_offer_tracking_detail` → `/ga4-tracking-setup/` |
| 05 Übernahme-Check | `home_offer_takeover` | `/kontakt/?type=project&focus=implementation_scope` | keiner (keine Produktseite) |

Der Hook `home_offer_conversion` aus der Konsolidierungsentscheidung vom
2026-09-13 bleibt stillgelegt, damit ein Eventname nicht zwei Angebote trägt;
die Analyse läuft unter `home_offer_analysis`.

**Versuch Ersteinschätzung (`HU_EXPERIMENT_ERSTEINSCHAETZUNG`):** Solange er an
ist, führen Kopf, Fall, Preisabschluss und Seitenabschluss über
`hu_first_assessment_url()` zur kostenlosen Ersteinschätzung. Seit Freigabe
vom 04.10.2026 ist im Hero die Projektanfrage (`home_head_contact`) die
Hauptaktion, auch bei aktivem Versuch. Die Kopf-Tür `ersteinschaetzung` trägt
`nav_header_ersteinschaetzung`; der Abschluss `home_close_ersteinschaetzung`.
Neu sind `home_case_ersteinschaetzung` und `home_offers_ersteinschaetzung`.
Im Hero steht daneben „Leistungsumfang & Preise“ (`home_hero_prices`).
Der Kanonpreis im Kicker ist Klartext. `home_hero_price_line` und
`home_head_ersteinschaetzung` sind stillgelegt. Unter den Angeboten erklärt
eine bedingte Ersteinschätzungs-Tafel Eingabe, drei Befunde und die Antwortzeit.
`home_hero_case_proof` steht leise unter der Hauptaktion bei jeder Breite.
Die frühere Hero-Seitennavigation mit `home_nav_prices`, `home_nav_case` und
`home_nav_pruefstand` entfällt. Fall und Referenzen bleiben in der Belege-Leiste.
Die sechs Hero-Stationslinks tragen `home_hero_station` mit dem Slug als
`data-track-label`, führen auf `#station-{slug}` und öffnen dort das Akkordeon.
Die Reihenfolge, Türen und Anker der globalen Navigation bleiben unverändert;
nur auf der Startseite trägt der globale Kopf die dunklen Tafel-Tokens.
Schalter aus: Kopf, Hero und Fall führen zur Projektanfrage, die zusätzliche
Ersteinschätzungs-Zeile bei den Preisen und ihre Abschlusskarte entfallen.
Auf der Kontaktseite und bei den Mails gelten die bestehenden Kanon-Verträge.
Die Umbauentscheidung ersetzt die bisherige Einfrierung der Homepage;
vorherige und spätere Versuchsdaten werden getrennt betrachtet. Die neue
Hypothese wird sechs Wochen ab Livegang geprüft, siehe
`docs/experimente/ersteinschaetzung.md`.

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

On the four provider decision articles for Aroundhome, Checkfox, Wattfox and DAA, the primary context CTA goes to `/solar-waermepumpen-leadgenerierung/#sofortkontakt` for businesses already buying leads. The secondary CTA remains the Marktcheck. Other Energy-cluster routes retain the Marktcheck as their primary destination.
On the Solar money page itself, the primary hero CTA stays on `#marktcheck`; the secondary hero CTA addresses existing lead buyers and goes to `#sofortkontakt`. Editorial links from the ten related guides open the page without a form anchor; explicit „Marktcheck starten“ CTAs keep `#marktcheck`.

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

The final White-Label route keeps one primary hero action: `Aufgabe beschreiben`
(`cta_whitelabel_hero_task_brief`). Prices are an anchor link. The offer-stage
assessment follows proof and prices (`cta_whitelabel_offers_assessment`) and
remains one of the three enquiry ways (`cta_whitelabel_way_offer`). All three
ways retain the same REST form and their `case` values. Review after six weeks
from deployment: `docs/experimente/whitelabel-angebotsphase.md`.

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

### Header (seit 2026-10-01: Türen)

Quelle der Punkte: `hu_get_site_header_navigation_contract()` in
`blocksy-child/inc/commercial-routing.php`. Quelle der Tür und des Modus:
`hu_funnel_doors()` und `hu_funnel_context()` in
`blocksy-child/inc/funnel-doors.php`; Kopf und Fuß lesen dieselbe
Entscheidung. Kopfzeile und Klappblatt rendern dieselbe Liste in derselben
Reihenfolge (das Blatt zusätzlich „Über Haşim“); das gespeicherte
WordPress-Menü, die 404-Seite und das SEO Cockpit lesen denselben Contract.
Geprüft von `scripts/tests/navigation-contract.php` (CI).

| Punkt | Ziel | Event | Zeile |
|---|---|---|---|
| Projekte | `/#angebote` | `nav_header_freelancer` | ja |
| Tracking | `/ga4-tracking-setup/` (Route `tracking_setup`) | `nav_header_tracking` | ja |
| White-Label | `/whitelabel-retainer/` | `nav_header_whitelabel` | ja |
| Solar & Wärmepumpe | `/solar-waermepumpen-leadgenerierung/` | `nav_header_solar` | ja |
| Ergebnisse (hinter einer Haarlinie) | `/#arbeiten` | `nav_header_results` | ja |
| Über Haşim | `/hasim-uener/` | `nav_header_about` | nur Blatt |

Seit 2026-10-03 trägt die Wortmarke (Ziel `/`) `nav_header_home`.
Die Zeitreihe von `nav_header_about` ist bis zu dieser Umstellung mit
Logo-Klicks vermischt; „Über Haşim“ behält `nav_header_about`.

`meta.location` bleibt im Kopf-Contract für das Klappblatt. Die dort nie
gerenderten E-Mail-, Telefon- und hasimuener.org-Links (`meta.links`) sind
entfernt; das SEO Cockpit liest nur die tatsächlich definierten Kopfpunkte.

Die Tür ersetzt den kontextblinden CTA. Der Contract behält `cta` als
Standardtür „Projekt anfragen“ für Menü, 404 und SEO Cockpit.

Seit 2026-10-03 blendet nur die Zeilentür `ersteinschaetzung` ihren Betrag
unter 414 px aus, damit Wortmarke, Tür und Menü ohne Überlauf passen.
Im Klappblatt bleibt der Betrag sichtbar. Für alle übrigen Türen bleibt
die Grenze unter 371 px unverändert.

Türmatrix (Modus `voll` zeigt das Hauptmenü, `leser` den Artikelpfad statt des
Menüs, `fokus` die Leiter der Solar-Seite):

| Route | Modus | Tür | Betrag (Kanon) | Ziel | `data-track-action` |
|---|---|---|---|---|---|
| Über, sonstige Seiten; Startseite bei ausgeschaltetem Versuch | voll | Projekt anfragen | kein Betrag | `/kontakt/?type=project` | `nav_header_project` |
| Startseite bei eingeschaltetem Versuch | voll | Ersteinschätzung (`hu_first_assessment_text( 'label' )`) | kostenlos (`hu_format_eur( 0 )`) | `/kontakt/?focus=ersteinschaetzung` | `nav_header_ersteinschaetzung` |
| `/ga4-tracking-setup/`, `/server-side-tracking-b2b/` | voll | Tracking anfragen | „ab“ Messung-Setup | `/kontakt/?type=project&focus=tracking` | `nav_header_door_tracking` |
| White-Label | voll | Test-Sprint anfragen | Test-Sprint | `/whitelabel-retainer/#aufgabe` | `nav_header_door_whitelabel` |
| Portal-Einordnungen (Checkfox, Aroundhome, Wattfox, DAA) | leser | Sofortkontakt | Sofortkontakt-Setup | `/solar-waermepumpen-leadgenerierung/#sofortkontakt` | `nav_header_door_sofortkontakt` |
| Übrige Beiträge, Dossier „Leadgenerierung“ | leser | Marktcheck | kostenlos | `…/#marktcheck` | `nav_header_door_marktcheck` |
| Übrige Beiträge, Dossier „Tracking“ | leser | Tracking anfragen | „ab“ Messung-Setup | wie oben | `nav_header_door_tracking` |
| Übrige Beiträge, sonst | leser | Projekt anfragen | kein Betrag | wie oben | `nav_header_project` |
| Fallstudie | voll | Marktcheck | kostenlos | `…/#marktcheck` | `nav_header_door_marktcheck` |
| Solar-Cluster (Slugs aus `hu_get_solar_cluster_link_map()`) | voll | Marktcheck | kostenlos | `…/#marktcheck` | `nav_header_door_marktcheck` |
| Solar-Seite | fokus | Leiter der Seite: Marktcheck · Analyse · Sofortkontakt | kostenlos · Analyse · Sofortkontakt-Setup (je aus dem Kanon) | Anker der Seite (`#marktcheck`; `#analyse` und `#sofortkontakt`, bis `HU_FEATURE_SOLAR_DOORS` an ist `#einstieg`) | `nav_header_door_marktcheck`, `nav_header_door_analyse`, `nav_header_door_sofortkontakt` |
| Kontakt | voll | keine Tür | | | |

Die Zeile „White-Label“ gilt für `site-header.php` und für den eigenen Kopf der
Hauptseite `/whitelabel-retainer/` (`template-parts/whitelabel-header.php`,
Landeseite der Akquise-Mails mit fünf Ankern der Seite). Beide rendern die
Tür über `hu_funnel_door_link()`. Der Kopf der Hauptseite behält die Action
`cta_whitelabel_header_task_brief` (statt `nav_header_door_whitelabel`), damit
die Zeitreihe der Landeseite nicht abreißt; `data-door="aufgabe"` trägt er
trotzdem, die Messung liest ihn ohne Sonderfall. Auch dort steht die
Bezeichnung „Test-Sprint anfragen“ mit Betrag statt „Aufgabe beschreiben“. Ein Beitrag
ohne Dossier-Kategorie bekommt „Projekt anfragen“: der Rückfall auf das Dossier
„Leadgenerierung“ beschriftet nur den Artikelpfad.

Jede Tür trägt zusätzlich `data-door="<schlüssel>"` (`projekt`, `tracking`,
`aufgabe`, `marktcheck`, `analyse`, `sofort`), `data-track-category="lead_gen"`
und `data-track-section="header"`. Die Beträge kommen ausschließlich aus
`inc/canon/pricing-canon.php` (`hu_tracking_price( 'measurement', 'setup' )`,
`HU_WHITELABEL_TEST_SPRINT_PRICE`, `hu_analysis_price()`,
`hu_entry_setup_price()`); die Navigationsprüfung verbietet Preisliterale in
`inc/funnel-doors.php` und im Template. Ein „ab“-Betrag ist die echte
Untergrenze dessen, was hinter der Tür liegt: Tracking beginnt bei der
Messung (Stufe 1 der Tracking-Leiste), nicht beim Basis-Paket. Die Tür „Projekt anfragen“ bündelt
Website, Landingpage, Relaunch und Optimierung und trägt keinen Betrag.
Der Marktcheck erscheint als Tür nur im Energie-Kontext (Fallstudie, Dossier
„Leadgenerierung“, Portal-Einordnungen).

Der Modus `fokus` (Solar-Seite) zeigt Wortmarke links und rechts die Leiter als
drei Textlinks mit Betrag, Mono, Betrag in `--tinte`. Bezeichnung und Betrag
sind getrennte Felder mit Abstand `--s1`; die Abstände zwischen den Einstiegen
passen sich zwischen `--s1` und `--s2` an. Die globale Accessibility-Schicht
hält die Klickflächen mindestens 44 px hoch. Kein Hauptmenü, nicht
sticky, höchstens 56 px, Haarlinie unten, unter 561 px nur „Marktcheck · 0 €“ als
gefüllter Button (der Sofortkontakt steht dann nur im Angebotsblock). Die
sticky Kapitel-Leiste der Seite bleibt im Seiteninhalt.

Solange `HU_FEATURE_SOLAR_DOORS` aus ist, zeigen die Türen Analyse und
Sofortkontakt (Kopf, Leiter und Fuß)
auf `/solar-waermepumpen-leadgenerierung/#einstieg`; `HU_FEATURE_SOLAR_DOORS`
(`inc/feature-flags.php`, Vorgabe `false`) schaltet sie auf `#analyse` und
`#sofortkontakt`. Haşim schaltet ihn per `wp-config.php` um, sobald diese
Anker live sind.

Der Lesemodus übernimmt den Artikelpfad des früheren Lesekopfs:
„Wissen“ → Blog (`article_reader_back_blog`), Dossier →
Dossier-Archiv (`article_reader_open_dossier`). Die Metazeile
(Autor, Aktualisiert, Minuten) des früheren Lesekopfs entfällt im Kopf.

Reihenfolge: erst was angeboten wird (Projekte, Tracking), dann die Wege für
bestimmte Absender (Agenturen, Energiebetriebe), dann Belege. Der
Punkt „Tracking“ führt auf das Tracking-Angebot, dieselbe Leiter, deren erste
Stufe die Startseite als „Conversion-Tracking“ mit Preis verkauft. Die Server-Side-Seite
bleibt Query-Owner für Server-Side-Tracking-Suchen und wird mit genau diesem
Namen verlinkt (Fuß, GA4-Seite, White-Label-Margenblock), nie als bloßes
„Tracking“. `aria-current="page"` steht nur auf dem Link, der die aufgerufene
Seite ist; liegt die Seite nur im Bereich eines Punkts (Server-Side-Seite unter
Tracking, Fallstudie unter Ergebnisse), steht `aria-current="true"`.

Unter 1081 px bleibt die Tür in der Kopfzeile sichtbar, unter 561 px als
Kurztext, unter 371 px ohne Betrag (Ersteinschätzung bereits unter 414 px).
Nur im Klappblatt steht sie unter 340 px
und auf Seiten mit eigener Sticky-CTA-Leiste (unter 761 px). Ohne JavaScript
ist das Klappblatt offen. Der Kopf von `/whitelabel-retainer/` zeigt die Tür
zwischen 768 und 1080 px neben der Wortmarke und über 1080 px neben den Ankern;
unter 768 px trägt die Sticky-Leiste am unteren Rand den Weg zum Formular. Die
Seite rendert einen eigenen, reduzierten Fuß ohne Türregister (Landeseite).

### Footer: Türregister (seit 2026-10-01)

Quelle: `hu_get_site_footer_navigation_contract()` in
`blocksy-child/inc/commercial-routing.php` (Wege, Selbstauskunftssätze,
Türschlüssel) und `hu_funnel_doors()` in `blocksy-child/inc/funnel-doors.php`
(Bezeichnung, Betrag, Ziel), gerendert von `template-parts/site-footer.php`.
Der Fuß trägt keinen Sammel-CTA. Er fragt, wer der Besucher ist, in derselben
Reihenfolge wie der Kopf, und führt jeden Weg auf seine Türen statt auf eine
Landingpage. Kopfzeile des Registers: links „Welcher Weg passt?“, rechts
„Preise netto · “ plus `hu_response_promise()`.

| Satz | Türen (Schlüssel) | Event je Tür |
|---|---|---|
| Ich habe **eine Website** … | Projekt anfragen (`projekt`, „nach Umfang“) | `cta_footer_door_projekt` |
| Ich brauche **belastbare Messung** … | Tracking-Projekt anfragen (`tracking`, „ab“ Server-Side-Setup) | `cta_footer_door_tracking` |
| Ich bin **Agentur** … | Test-Sprint anfragen (`aufgabe`) | `cta_footer_door_aufgabe` |
| Ich bin **Solar- oder Wärmepumpenbetrieb** und kaufe heute Portal-Anfragen. | Marktcheck, regional (`marktcheck`, kostenlos) · Anfragesystem-Analyse (`analyse`) · Sofortkontakt-Setup (`sofort`) | `cta_footer_door_marktcheck`, `cta_footer_door_analyse`, `cta_footer_door_sofort` |

Jede Tür ist eine Zeile mit Bezeichnung, Betrag (Mono, tabellarische Ziffern)
und Pfeil; die Beträge kommen aus dem Kanon (`hu_funnel_doors()`), kein
Preisliteral steht im Template. Die Tür ohne Betrag zeigt „nach Umfang“.
Der Tracking-Footer liest dafür `footer_amount` aus
`hu_tracking_price( 'standard', 'setup' )`; Kopf und andere
Tracking-Türen behalten den clientseitigen Messung-Einstieg. Die
Navigationsprüfung rendert den Fuß in allen Kontexten und prüft diesen
eigenen Preisanker; die Literal-Sperre allein erkennt keinen falschen
Paketschlüssel.
Alle tragen `data-door="<schlüssel>"`, `data-track-category="lead_gen"` und
`data-track-section="footer"`. Die früheren `cta_footer_pick_project|tracking|
agency|energy` entfallen; ältere Zeitreihen enden am 2026-10-01.

Die eigene Route wird nicht mehr ausgeblendet, sondern markiert (3-px-Kante in
`--stempel`, Etikett „Ihr Weg“); `hu_funnel_context()` liefert sie (beide
Tracking-Seiten zählen als Tracking; Fallstudie, Portal-Einordnungen und das
Dossier „Leadgenerierung“ als Energie; Dossier „Tracking“ als Tracking). Auf
der Seite, die den Anker besitzt, zeigt die Tür auf den Anker der Seite
(Energie-Seite: `#marktcheck`, `#einstieg`). Das Register erscheint auf der Energie-Seite. Auf der Startseite bleibt es
wegen der eigenen Nebenwege und des Abschlusses weg; auf `/kontakt/` ebenso, die
Seite ist das Ziel jeder Tür. `/whitelabel-retainer/` rendert einen eigenen
Fuß und zeigt das Register nicht.

Der Schalter sitzt zentral: `hu_footer_shows_register()` in
`blocksy-child/inc/funnel-doors.php` liefert `false` für die Startseite, die Kontaktseite und für
jede Seite, deren Template in `hu_footer_register_suppressed_templates()`
steht (derzeit `page-conversion-optimierung.php`: eine Money Page mit genau einem
Angebot und eigenem Abschluss braucht darunter keine zweite Auswahl). Der Fuß
(`template-parts/site-footer.php`) liest nur die Funktion und bleibt sonst
unverändert, Direktzeile, Verzeichnis und Absender erscheinen weiter. Eine weitere
Money Page ohne Register trägt man mit ihrer Template-Datei in diese Liste ein;
`scripts/tests/navigation-contract.php` prüft die Kontexte `home` und `conversion`.

Die Energie-Zeile führt den Marktcheck damit auf jeder Seite (außer Kontakt
und White-Label) als eine von drei Türen der Energie-Betriebe. Das ist die
Vorgabe des Auftrags; `AGENTS.md` nennt sitewide Marktcheck-Routing als nicht
wieder einzuführen. Offen, ob die Energie-Zeile davon ausgenommen sein soll
(Entscheidung bei Haşim). Der Kopf zeigt den Marktcheck weiter nur im
Energie-Kontext.

Darunter die Direktzeile, ebenfalls `lead_gen`:

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
| Leistungen | Landingpage erstellen lassen | `/landingpage-erstellen-lassen/` | `cta_footer_nav_landingpage` | `navigation` |
| Leistungen | Conversion-Optimierung | `/conversion-optimierung/` | `cta_footer_nav_conversion` | `navigation` |
| Leistungen | WordPress-Website erstellen lassen | `/wordpress-website-erstellen-lassen/` | `cta_footer_nav_website_offer` | `navigation` |
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

Neu seit 2026-09-26: `cta_footer_nav_landingpage` für das Festpreis-Angebot
`/landingpage-erstellen-lassen/`, als letzter Eintrag der Gruppe Leistungen.

Neu seit 2026-09-30: `cta_footer_nav_conversion` für `/conversion-optimierung/`,
als letzter Eintrag der Gruppe Leistungen. Die Kopfnavigation führt die Seite
nicht (ihr Punkt „Leistungen“ ist ein Anker auf der Startseite); die
Startseite führt die Seite seit 2026-10-01 in der Preisliste
(`home_offer_analysis_detail`).

Neu seit 2026-10-01: `cta_footer_nav_website_offer` für das Festpreis-Angebot
`/wordpress-website-erstellen-lassen/`, als letzter Eintrag der Gruppe Leistungen.
Auf `/landingpage-erstellen-lassen/` führt die Zusatzzeile „Website“ jetzt dorthin
(`lp_offer_to_website_offer`; der frühere `lp_offer_to_website` → `/#angebot-website`
ist stillgelegt). Die Startseite verlinkt die Seite seit 2026-10-01 aus der
Preisliste (`home_offer_website_detail`).

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

## Die Anfrage-Website (freigegeben 02.10.2026)

Die Route verkauft ein konfigurierbares Website-Grundprodukt. Der Hero führt
mit `website_offer_hero_configure` in die Umfangswahl; er löst selbst keine
Projektanfrage aus. Reale Projektbelege stehen vor dem Rechner. Direkte
Anfrageziele liegen nach dem Proof, in der Rechner-Zusammenfassung, im Abschluss
und in der kontextuellen Sticky-Leiste:
`cta_website_offer_proof_project`,
`cta_website_offer_scope_project`,
`cta_website_offer_close_project` und
`cta_website_offer_sticky_project`.

Die drei `website_offer_scenario_1|3|5`-Hooks sind Abkürzungen für
Seitentyp-Mischungen, keine Pakete. Seit 06.10.2026 setzen sie
`kurz`, `standard` und `leistung` passend zur Gesamtseitenzahl.
Texterstellung, Design und Projektmodule bleiben bei einem Preset-Wechsel
erhalten. Tastaturfokus bleibt auf der aktivierten Auswahl; es gibt keinen
automatischen Scroll-/Fokuswechsel. Die Auswahl sendet keine Anfrage.

Der Browser übergibt `seiten`, `kurz`, `standard`, `leistung`, `art`,
`texte`, optional `design`/`design_layouts`, `tracking`, `crm` und
`dashboard`. Der Server prüft, dass Seitentypen und Gesamtseitenzahl
zusammenpassen, und berechnet Preis sowie Produktionszeit ausschließlich aus
dem Kanon neu. Alte Links ohne Seitentyp-Zähler bleiben kompatibel und werden
als Standardseiten interpretiert. Preis- oder Zeitwerte aus dem Browser werden
niemals als Angebotswerte übernommen. Der normalisierte Scope wird im
Kontaktformular, in beiden Mails und im CRM weitergeführt.

Seit der CRO-Kürzung vom 06.10.2026 gibt es keinen eigenen frühen
Qualitätsblock, keinen PageSpeed-Prüflink und keinen interaktiven
Sieben-Punkte-Vergleich mehr. Die Kapitelzeile enthält nur Preis & Umfang,
Projekte und Fragen. E3 ist der einzige ausführliche Projektbeleg und führt
weiter in den Rechner. Lieferumfang und Erweiterungsdetails bleiben direkt beim
Konfigurator; danach folgen drei statische Qualitätsprinzipien, der kompakte
Ablauf und sechs FAQ. Direkte Wege im Abschluss:
`website_offer_close_mail` und `website_offer_close_tel`.
`focus=website` ist das gültige Projekt-Thema; alte
`focus=relaunch`-Links bleiben kompatibel.

Matomo: Kategorie `anfrage_website`, Aktionen `cta_click`,
`rechner_change` und `form_submit`; `toggle_durchleuchtung` ist mit dem
entfernten Vergleich retired. `form_submit` erst nach bestätigter erfolgreicher
Antwort.

## Entfernung der ehemaligen Asset-Struktur (2026-10-03)

Die ehemalige Asset-Registry, ihre Landingpages und ihr Dashboard sind entfernt.
Glossar-Einträge schließen weiterhin mit der Projektanfrage ab; sie sind keine
Energie-Akquise. Fachbegriffe erhalten passende Erklärseiten, operative Themen
führen zu bestehenden Leistungsseiten. Pakete ohne inhaltlichen Nachfolger und
Hub-Routen liefern 410; es gibt keine pauschale Umleitung auf Startseite oder
Marktcheck. GA4- und Performance-Marketing-Routen bleiben eigenständig unter
`inc/service-cluster-pages.php`. Vollständige Zuordnung:
`docs/decisions/asset-system-retirement.md`.

## Solar-Produktanfrage (08.10.2026)

Der Marktcheck bleibt der primäre Energie-Einstieg. Der Konfigurator der
Solar-Money-Page bietet zusätzlich `/kontakt/?type=project&focus=energy`
mit `products=photovoltaik,waermepumpe,speicher` (eine beliebige nicht leere
Teilmenge). Der Server validiert die Kennungen, entfernt Duplikate und
berechnet den Preis aus dem Pricing-Kanon; Browserpreise sind keine Quelle.
Die Auswahl bleibt in Formular, beiden Mails und CRM erhalten.
Sofortkontakt führt mit `focus=sofortkontakt` auf eine thematisch vorbereitete
Kontaktanfrage. Beide Wege sind Anfragen, keine kostenpflichtige Beauftragung.
