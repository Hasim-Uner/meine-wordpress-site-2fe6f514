# Gap-Analyse: Keyword-Cluster ↔ Repo-Seiten (Stand 2026-07-03)

Quellen: Semrush-Live-Daten (`data/`), `blocksy-child/inc/seo-meta.php` (Forced-SEO-Map),
`blocksy-child/inc/blog-provider-posts.php`, `docs/architecture/LIVE_STATUS.md`.

## Indexierbares Seiten-Inventar (relevant für die Recherche-Cluster)

| URL | Title (aus seo-meta.php) | Erkennbares Ziel-Keyword |
| --- | --- | --- |
| `/` | Homepage (Solar-/WP-Positionierung, Marktcheck) | Anfrage-Systeme (Brand-Positionierung, kein Volumen-Keyword) |
| `/solar-waermepumpen-leadgenerierung/` | Leadgenerierung Photovoltaik & Wärmepumpe ohne Portale | leadgenerierung photovoltaik (70, KD 0) |
| `/solar-leads-kaufen-alternative/` | Solar Leads kaufen? Alternative ohne Portal-Abhängigkeit | solar/pv/photovoltaik leads kaufen (Summe ~240, KD 0–1) — **rankt bereits Pos. 48 für „pv leads kaufen"** |
| `/b2b-solar-leads/` | Photovoltaik B2B Leads: Gewerbe-PV statt Masse | b2b leads photovoltaik (0 Vol.) |
| `/eigene-leadgenerierung-vs-portale/` | Portal-Leads vs. eigenes System: TCO-Vergleich Solar/SHK | eigene leadgenerierung vs portale (kein messbares Vol., strategisch) |
| `/lead-funnel-solar/` | Lead-Funnel Solar & Wärmepumpe | lead funnel solar (kein messbares Vol.) |
| `/kunden-gewinnen-solarteure/` | Kunden gewinnen für Solarteure – ohne Portal-Leads | photovoltaik kunden gewinnen (0 Vol., aber CPC-Signal) |
| `/cost-per-lead-photovoltaik/` | Cost per Lead Photovoltaik | photovoltaik leads kosten (0–20 Vol.) |
| `/qualifizierte-pv-anfragen/` | Qualifizierte PV-Anfragen: 4 Merkmale | qualifizierte pv anfragen (kein messbares Vol.) |
| `/solar-leads-kosten-studie/` | Solar-Leads Kosten 2026: CPL, CPO & Portal-Vergleich | photovoltaik leads kaufen kosten (20) |
| `/server-side-tracking-b2b/` | Server-Side Tracking Agentur: DSGVO, GA4 & CAPI | server side tracking (nicht Teil dieses Sprints) |
| `/wordpress-agentur-hannover/` | WordPress Agentur Hannover: SEO, Tracking & CRO | wordpress agentur hannover (480, KD 10) — **rankt Pos. 11** |
| `/e3-new-energy/` | E3-Methodik-Case | e3 new energy (390, KD 26) — rankt Pos. 24 |
| Blog `/solar-leads-kaufen-lohnt-sich/` | Pillar: Solar-Leads kaufen lohnt sich? | solar leads kaufen (Ratgeber-Intent) |
| Blog `/aroundhome-solar-einordnung/` | Aroundhome für Solarteure: Markteinordnung | aroundhome erfahrungen handwerker (50) |
| Blog `/wattfox-solar-leads-einordnung/` | Wattfox Solar Leads: Markteinordnung & Alternative | wattfox erfahrungen / wattfox leads (110 + Longtails) |
| Blog `/daa-photovoltaik-leads-einordnung/` | DAA Photovoltaik Leads: Markteinordnung | daa leads (90, KD 11) |
| Blog `/leadfluss-pv-leads-einordnung/` | Leadfluss PV-Leads: Markteinordnung | leadfluss (170, navigational — Wettbewerber-Brand) |
| `/whitelabel-retainer/` | (noindex, follow; sitemap-excluded — bewusste Entscheidung) | white label leadgenerierung (0 Vol.) |
| `/ga4-tracking-setup/`, `/performance-marketing/`, `/ergebnisse/`, `/uber-mich/`, `/kontakt/`, Glossar, Kategorien | Service-/Support-Layer | nicht Teil dieses Sprints |

## Mapping: Cluster → Seite oder LÜCKE

### (a) Money/Transaktional

| Keyword(-Gruppe) | Vol. | KD | Status |
| --- | --- | --- | --- |
| photovoltaik leads kaufen + pv leads kaufen + solar leads kaufen + leads photovoltaik-Varianten | ~240 | 0–1 | **BESTEHT:** `/solar-leads-kaufen-alternative/` + Blog-Pillar. Rankt aber nur Pos. 48 für ein Keyword → On-Page-/Interne-Link-Optimierung statt Neubau. |
| photovoltaik leads (Head-Term) | 210 | 9 | **TEIL-LÜCKE:** Kein Template führt den Head-Term in Title/H1. anfragenfluss.de hat dafür eine dedizierte /photovoltaik-leads-Seite (Pos. 9). Empfehlung: Head-Term in `/solar-leads-kaufen-alternative/` (Title/H1/Intro) einarbeiten, KEINE neue URL (Kannibalisierungsgefahr). |
| **wärmepumpen leads (+ kaufen)** | 90+20 | **0** | **LÜCKE:** Keine Wärmepumpen-Lead-Seite im Repo, obwohl „Wärmepumpe" Teil der Kernpositionierung ist. smart11.de und leadfluss.de besetzen das Keyword bereits. CPC 11,83 € belegt Kaufintention. |
| leads kaufen (generisch) | 390 | 6 | TEILWEISE: Blog-Pillar bedient den Ratgeber-Intent. Generischer Term ist intent-gemischt (Immobilien/Versicherung dominieren die Related-Daten) → kein eigenes Ziel. |
| hochpreis leadquelle | 260 | 30 | **LÜCKE (optional):** Vertriebler-/Coaching-Szene-Begriff; anfragenfluss + smart11 ranken. Passt nur bedingt zur seriösen Positionierung → P3. |
| leadgenerierung photovoltaik / photovoltaik leads generieren | 70+20 | 0 | **BESTEHT:** `/solar-waermepumpen-leadgenerierung/` (Title exakt darauf geschärft). |
| photovoltaik leads kaufen kosten / leads kosten | 20 | 0 | **BESTEHT:** `/solar-leads-kosten-studie/` + `/cost-per-lead-photovoltaik/`. |

### (b) Portal-Alternativen

| Keyword(-Gruppe) | Vol. | KD | Status |
| --- | --- | --- | --- |
| aroundhome erfahrungen/-erfahrung/seriös/kosten (+ handwerker-Varianten) | ~1.350 gesamt | 0–22 | **TEIL-LÜCKE:** Blogpost existiert, aber SEO-Title „Markteinordnung" zielt an den Suchbegriffen vorbei. „aroundhome erfahrungen" (480) ist das größte kaufnahe Portal-Keyword im ganzen Datensatz. Achtung: Suchintention gemischt (Verbraucher + Anbieter); B2B-Winkel „aroundhome kosten für handwerker" (70, KD 0) + „aroundhome erfahrungen handwerker" (50, KD 0) sind exakt unsere Zielgruppe. |
| wattfox erfahrungen/kosten/seriös/leads/leads kaufen/leads erfahrungen | ~1.300 gesamt (inkl. Brand 880) | 0–39 | **TEIL-LÜCKE:** Blogpost existiert. SERP „wattfox erfahrungen" enthält KEINEN einzigen Dienstleister — nur Bewertungsportale + Wattfox selbst. Title-/H1-Schärfung auf „Erfahrungen/Kosten aus Anbietersicht" nötig. |
| **checkfox (erfahrungen/seriös/solar/wärmepumpe)** | ~1.400 relevant | 16–22 | **LÜCKE:** Drittes großes Portal, im Repo komplett unbehandelt. „checkfox erfahrungen" + „checkfox seriös" je 590. Achtung: Checkfox ist breiter (Versicherungen), Solar-/WP-Anteil über „checkfox solar" (170) + „checkfox wärmepumpe" (50) belegt. |
| daa leads (+ erfahrungen) | 90 | 11 | **BESTEHT (schärfbar):** Blogpost existiert; leadfluss.de rankt Pos. 11 mit generischem Ratgeber → Top-10 realistisch. |
| selfmade energy (erfahrungen) | 480 Brand / 20 | 0–37 | TEIL-LÜCKE: kein eigener Post; Volumen im B2B-Winkel minimal → Abschnitt im Vergleichs-Hub reicht (P3). |
| solar leads anbieter vergleich / photovoltaik portal erfahrungen | 0 | – | Kein messbares Volumen, ABER anfragenfluss.de fährt exakt dieses Format erfolgreich (`beste-pv-lead-anbieter-vergleich`, `photovoltaik-leads-kaufen-anbieter-vergleich` ranken für Money-Terms). Vergleichs-Hub wirkt als Ranking-Asset für die Money-Keywords, nicht als eigenes Volumen-Ziel. |
| photovoltaik angebotsvergleich | 320 | 30 | KEINE SEITE BAUEN: Verbraucher-Intent (Hausbesitzer suchen Angebote) — nicht Zielgruppe A oder B. |

### (c) Problem-Aware

| Keyword(-Gruppe) | Vol. | KD | Status |
| --- | --- | --- | --- |
| photovoltaik kunden gewinnen | 0 | – | **BESTEHT:** `/kunden-gewinnen-solarteure/`. Kein messbares Volumen — Seite wirkt als Funnel-/GEO-Asset. |
| kunden gewinnen (generisch, 1.000) + handwerk (70, KD 5) | 1.070 | 5–18 | Zu breit für Money Page; „kunden gewinnen handwerk" als H2/FAQ-Semantik auf `/kunden-gewinnen-solarteure/` einarbeiten. |
| photovoltaik vertrieb / wärmepumpen vertrieb | 140+90 | 8–19 | **LÜCKE:** Vertriebs-Winkel unbesetzt (nur leadfluss streift ihn). Ratgeber „PV-/Wärmepumpen-Vertrieb: Anfragen statt Kaltakquise" möglich (P2). |
| photovoltaik marketing | 70 | 0 | **LÜCKE (klein):** KD 0. Als eigener Ratgeber oder Sektion auf `/kunden-gewinnen-solarteure/`. |
| solar marketing agentur / werbung solarfirma / mehr anfragen photovoltaik / wärmepumpe aufträge bekommen | 0 | – | Kein messbares Volumen → notiert, keine Aktion. „solar marketing agentur" hat CPC 6,07 € (Ads-Nachfrage unter Messschwelle) — als Semantik auf bestehenden Seiten mitführen. |

### (d) White-Label

| Keyword(-Gruppe) | Vol. | KD | Status |
| --- | --- | --- | --- |
| white label leadgenerierung / performance marketing white label | 0 | – | Kein messbares Volumen. `/whitelabel-retainer/` ist bewusst noindex → kein Handlungsbedarf. |
| leadgenerierung agentur (880, KD 12) / b2b leadgenerierung agentur (480, KD 17) / leadgenerierung b2b (480) | ~2.500 | 12–18 | BEWUSSTE NICHT-LÜCKE: Größtes Volumen im Datensatz, aber generischer Agentur-Intent widerspricht der Nischen-Positionierung (Solar/WP). Nur relevant, falls Zielgruppe B (Agenturen) öffentlich beworben werden soll — dann eigene Entscheidung nötig, nicht Teil dieses Plans. |

### (e) Local

| Keyword(-Gruppe) | Vol. | KD | Status |
| --- | --- | --- | --- |
| wordpress agentur hannover / wordpress hannover | 480+320 | 7–10 | **BESTEHT, Quick Win:** Pos. 11 — knapp außerhalb Top 10, einzige nennenswerte Traffic-Quelle der Domain. |
| solar marketing hannover | 0 | – | Wie vermutet zu dünn → notiert, keine Aktion. |

## Kern-Erkenntnis

Das Repo hat die richtige Seitenarchitektur bereits gebaut (8-Sub-Page-Cluster + 4 Portal-Posts + Intercept-Page = exakt die anfragenfluss-Blaupause). Die Lücken sind:
1. **Wärmepumpen-Leads-Seite fehlt** (einzige echte Money-Lücke, KD 0),
2. **Checkfox fehlt** als drittes Portal,
3. **bestehende Portal-Posts zielen mit „Markteinordnung" an den echten Suchbegriffen („erfahrungen", „kosten", „seriös") vorbei**,
4. Domain-Autorität/Rankings hinken der Architektur hinterher — Priorität liegt auf Schärfung + internen Links, nicht auf Masse.
