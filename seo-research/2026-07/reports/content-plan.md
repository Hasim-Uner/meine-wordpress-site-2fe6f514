# Content-Plan hasimuener.de (2026-07)

Priorisierung: **Kaufintention > Volumen × realistisch erreichbare Position > Aufwand.**
Datenbasis: `../data/keywords-master.csv`, Gap-Analyse in `gap-analyse.md`.

Wichtige Rahmenbedingung: Die Domain rankt aktuell für nur 4 Keywords. Die Seitenarchitektur ist
bereits vollständig — der Plan besteht überwiegend aus **Schärfung + interner Verlinkung**, nicht aus Neubau.
Neue URLs nur dort, wo die Entscheidungsregel aus `docs/seo/money-keyword-implementation.md` erfüllt ist.

## P1 — sofort (höchste Kaufintention, KD ≤ 22, Seiten größtenteils vorhanden)

| # | Cluster | Haupt-Keyword | Vol. | KD | Intent | Format | Seite | Aufwand |
| --- | --- | --- | ---: | ---: | --- | --- | --- | --- |
| 1 | Money | photovoltaik leads kaufen (+ pv/solar leads kaufen, photovoltaik leads, pv leads) | ~600 kumuliert | 0–9 | transaktional | Intercept-Landingpage schärfen | **BESTEHT:** `/solar-leads-kaufen-alternative/` | S–M |
| 2 | Money | **wärmepumpen leads** (+ kaufen) | 110 | **0** | transaktional | Money-/Intercept-Page analog Solar | **NEU:** z. B. `/waermepumpen-leads/` | M |
| 3 | Portal-Alt. | **aroundhome erfahrungen** (+ erfahrung, seriös, kosten für handwerker, erfahrungen handwerker) | ~1.150 | 0–22 | kommerziell | Bestehenden Blogpost auf Suchbegriff ausrichten | **BESTEHT:** `/aroundhome-solar-einordnung/` | S |
| 4 | Portal-Alt. | **wattfox erfahrungen** (+ kosten, seriös, leads, leads kaufen, leads erfahrungen) | ~300 (+Brand 880) | 0–19 | kommerziell | Bestehenden Blogpost auf Suchbegriff ausrichten | **BESTEHT:** `/wattfox-solar-leads-einordnung/` | S |
| 5 | Portal-Alt. | **checkfox erfahrungen / seriös / solar / wärmepumpe** | ~1.400 | 16–22 | kommerziell | Neue Markteinordnung (5. Portal-Post) | **NEU:** Blogpost analog Wattfox/Aroundhome | M |

Begründung P1:
- #1 rankt bereits (Pos. 48 „pv leads kaufen") und alle Ziel-Keywords haben KD 0–9 → schnellster Hebel. Head-Term „photovoltaik leads" (210) explizit in Title/H1/Intro aufnehmen; KEINE separate /photovoltaik-leads/-URL (Kannibalisierung).
- #2 ist die einzige echte Money-Lücke und deckt die zweite Hälfte der Kernpositionierung („…und Wärmepumpen-Anbieter") ab. smart11/leadfluss besetzen das Keyword bereits — beide schwach (Blog bzw. Service-Page ohne Fokus).
- #3–5: „Erfahrungen/seriös/kosten"-Suchen sind der Moment, in dem Betriebe am Portal zweifeln — exakt der Messaging-Einstieg. SERPs bestehen aus Bewertungsportalen, kein Dienstleister positioniert sich dort. Wording juristisch konservativ halten (Vorgabe aus LIVE_STATUS: sachliche Markteinordnung, keine Schmähkritik). Verbraucher-Anteil im Traffic bewusst in Kauf nehmen; B2B-Varianten („handwerker", „leads") als eigene H2/FAQ-Blöcke führen.

## P2 — nächste Welle (Cluster-Ausbau, Quick Wins)

| # | Cluster | Haupt-Keyword | Vol. | KD | Intent | Format | Seite | Aufwand |
| --- | --- | --- | ---: | ---: | --- | --- | --- | --- |
| 6 | Local | wordpress agentur hannover | 480 | 10 | transaktional | Pos. 11 → Top 10: interne Links stärken, Title-CTR testen, lokale Signale | **BESTEHT:** `/wordpress-agentur-hannover/` | S |
| 7 | Portal-Alt. | daa leads (+ daa leads erfahrungen) | 90 | 11 | kommerziell (B2B) | Blogpost auf „DAA Leads Erfahrungen"-Intent schärfen | **BESTEHT:** `/daa-photovoltaik-leads-einordnung/` | S |
| 8 | Portal-Alt./Money | solar leads anbieter vergleich (0 Vol., aber Ranking-Asset für Money-Terms — anfragenfluss-Blaupause) | – | – | kommerziell | Vergleichs-Hub, der die 4–5 Portal-Posts bündelt und auf Money-Pages verlinkt | **NEU:** Vergleichsseite oder Ausbau `/eigene-leadgenerierung-vs-portale/` | M |
| 9 | Problem-Aware | photovoltaik vertrieb / wärmepumpen vertrieb | 230 | 8–19 | informational | Ratgeber „PV-/WP-Vertrieb: planbare Anfragen statt Kaltakquise" → Marktcheck | **NEU:** Blogpost | M |
| 10 | Problem-Aware | photovoltaik marketing | 70 | **0** | informational | Ratgeber oder H2-Sektion | **BESTEHT (erweitern):** `/kunden-gewinnen-solarteure/` | S |

## P3 — opportunistisch / beobachten

| # | Cluster | Haupt-Keyword | Vol. | KD | Intent | Format | Seite | Aufwand |
| --- | --- | --- | ---: | ---: | --- | --- | --- | --- |
| 11 | Money | hochpreis leadquelle | 260 | 30 | transaktional (Vertriebs-Szene) | Blogpost, nur wenn Tonalität zur Marke passt | NEU (optional) | M |
| 12 | Portal-Alt. | selfmade energy erfahrungen | 20 | 0 | kommerziell | Abschnitt im Vergleichs-Hub (#8), kein eigener Post | BESTEHT (nach #8) | XS |
| 13 | Problem-Aware | kunden gewinnen handwerk / als dienstleister kunden gewinnen | 140 | 5–9 | informational | FAQ-/H2-Semantik auf `/kunden-gewinnen-solarteure/` | BESTEHT | XS |
| 14 | White-Label | leadgenerierung agentur / b2b leadgenerierung agentur | ~2.500 | 12–18 | transaktional | NUR falls Zielgruppe B öffentlich beworben werden soll — strategische Entscheidung, nicht SEO-getrieben | `/whitelabel-retainer/` ist bewusst noindex | L |

## Bewusst NICHT bespielen

- **photovoltaik angebotsvergleich** (320, KD 30): Verbraucher-Intent — falsche Zielgruppe.
- **kunden gewinnen / neue kunden gewinnen** (1.480 kumuliert): zu breit, zieht branchenfremden Traffic.
- **leads kaufen** (390) als eigenes Ziel: intent-gemischt über alle Branchen; wird über den Blog-Pillar mitbedient.
- **solar marketing hannover, werbung solarfirma, mehr anfragen photovoltaik, wärmepumpe aufträge bekommen, white label leadgenerierung**: kein messbares Volumen — als Copy-Semantik mitführen, keine eigenen Seiten.

## Trennung Repo vs. WordPress-Admin

- **Repo-Aufgaben:** #1 (Template `page-solar-leads-kaufen-alternative.php` + `seo-meta.php`-Title), #2 (neues Template im `.hu-intercept`-Cluster + Registrierung in `hu_get_solar_seo_subpage_paths()` / Sitemap / llms.txt), #6 (Template + interne Links), #8/#9 (je nach Format Template oder Seed-Content), Title-Anpassungen in `blog-provider-posts.php` (#3, #4, #7 — Achtung: bereits veröffentlichte Posts werden vom Provisioning nicht überschrieben).
- **WordPress-Admin-Aufgaben:** Bestehende Provider-Posts (#3, #4, #7) müssen im Editor aktualisiert werden, da `blog-provider-posts.php` publizierte Beiträge absichtlich nicht überschreibt; neuer Checkfox-Post (#5) kann über das bestehende Provisioning-Muster als Repo-Seed angelegt werden.
- **SEO-Cockpit/GSC:** Nach Umsetzung Recrawl anstoßen, Kannibalisierung „photovoltaik leads" (Intercept vs. B2B-Solar-Leads vs. Blog-Pillar) beobachten.
