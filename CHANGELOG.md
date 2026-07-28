# CHANGELOG

## 2026-07

### `Founding Cohort 2026` vollständig zurückgezogen

- **Auslöser:** Auf der Solar-Money-Page stand der Trust-Chip „Founding Cohort 2026 · 3/3 Plätze". Ein Platzzähler, der dauerhaft auf 3/3 steht, belegt keine Knappheit — er entwertet sie. Statt den Chip einzeln zu entfernen, ist der ganze Angebotsrahmen raus (ADR `docs/decisions/0011-founding-cohort-2026-entfernt.md`, löst `0007` ab).
- **Gelöscht:** `inc/canon/founding-canon.php`, `inc/components/founding-cohort-block.php`, `assets/css/founding-cohort.css` (nur `.hu-founding*`-Regeln, keine Fremdnutzung), der Shortcode `[hu_founding]` und der bedingte CSS-Enqueue für Startseite/About.
- **Solar-LP:** Trust-Chip weg; die Abschluss-Sektion ist jetzt reine Final-CTA — `#founding` → `#final-cta`, `data-track-section="founding_cohort"` → `final_cta` (der CTA-Link darin meldete schon immer `final_cta`). Platzzähler und Bewerbungsfrist ersatzlos gestrichen, die drei Fit-Bedingungen bleiben. CSS-Klasse `.sol-founding-facts` → `.sol-cta-facts`.
- **About:** Kohorten-Card behält Titel, Text und CTA — Eyebrow „FOUNDING COHORT 2026" → „ARBEITSWEISE", Status-Zeile „3 von 3 Plätzen offen" samt zugehöriger CSS-Regeln raus, der Satz über die Kohorten-Öffnung gestrichen. Der zusätzlich gerenderte `about`-Block darunter entfällt komplett.
- **Startseite:** Bullet „Maximal 3 Founding-Partner pro Jahr" → „Bewusst wenige Projekte parallel · 1:1 mit dem Betrieb"; FAQ „Bin ich gebunden?" schließt jetzt mit begrenzter Kapazität statt mit Kohorten-Plätzen. **Agentur-Seite:** Hero-Chip → „Solar · Wärmepumpe · SHK". **Quiz-JS:** grünes Signal spricht vom Umsetzungsfall.
- **Sprachregelung:** `Founding-Partner` → `Umsetzungspartner`, gepflegt in `messaging-canon.php` (`preferred_terms`, `term_definitions`) und als Verbot in `AGENTS.md` + `docs/standards/BRAND_AND_COPY.md` verankert. Specs, Privacy-Doc, Audit-Funnel-Doc, Blog-Entwurf und zwei Skills nachgezogen.
- **Bewusst unverändert:** die Preis-Konstanten in `pricing-canon.php`. Sie tragen die Namen `*_FOUNDING`, sind aber reine Preisdaten und können über `[hu_price]` in WordPress-Inhalten referenziert sein, die das Repo nicht sieht. Welcher Preis ohne Kohorte gilt, ist eine offene Angebotsentscheidung — kein Rename ohne sie.

### `/b2b-solar-leads/`: Feindbild korrigiert — gekaufte PV-Termine statt Lead-Portale

- **Datenbefund** (`seo-research/2026-07/data/gsc/gsc-export-28d-2026-07-03.csv`, 28 Tage): Die Seite sammelt **207 Impressionen über 20 Queries, 0 Klicks**. `pv termine b2b` trägt davon **86 Impressionen (42 %) auf Position 9,65** — die einzige Seite-1-Position der gesamten Site. Zum Vergleich: Money Page 11 Impressionen, `/solar-leads-kaufen-alternative/` 34.
- **Die geplante Suchintention existiert nicht.** `b2b leads photovoltaik` hat in `keywords-master.csv` Volumen 0 und ist der einzige Gewerbe-Term im Money-Cluster; „Termin" kommt im gesamten Research-Set kein einziges Mal vor. Semrush sieht den Longtail nicht, Google schon.
- **Das Portal-Feindbild trug nicht.** Unter allen 20 Queries kommt Aroundhome, DAA, Check24, Wattfox oder „leads kaufen" **kein einziges Mal** vor — der Kauf-Intent sitzt auf `/solar-leads-kaufen-alternative/`. Die Seite argumentierte seitenlang gegen einen Wettbewerber, den ihre Zielgruppe nicht hat; die eigene FAQ gab das bereits zu („Wie passt das mit DAA, Aroundhome oder Check24 zusammen? — Gar nicht."). Der reale Wettbewerber im Gewerbe sind **Terminierungs-Dienstleister und Callcenter**.
- **Umgebaut:** H1, Lead, Title und die Sektion „Warum …" argumentieren jetzt gegen eingekaufte Gewerbe-Termine. Fakten-Karte 4 stellt auf das Abrechnungsmodell der Terminierung um (Aussage über das Preismodell, keine erfundene Statistik — gleiche Vorsicht wie in #150). Sektions-Anker `#warum-b2c-funnel` → `#warum-gekaufte-termine` (keine interne oder externe Referenz darauf, geprüft).
- **Title auf die tragende Query gezogen** (`inc/seo-meta.php`, Forced-Map): „PV-Termine B2B & Photovoltaik Leads: Gewerbe statt Masse". H1 synchron gehalten — sonst laufen Snippet und Einstieg wieder auseinander, genau der Fehler aus #150. Die H1 behält „Photovoltaik", weil `leadgenerierung photovoltaik` und `photovoltaik leads gewerbe` ebenfalls auf die Seite zeigen.
- **Drei FAQ-Einträge zur Termin-Frage ergänzt** (wertloser vs. qualifizierter Termin, einkaufen vs. selbst aufbauen) — beantwortbar aus eigenen Kriterien, ohne erfundene Quoten oder Preise. `$faq` speist sichtbaren Text und `FAQPage`-Schema; das Schema bekommt via `wp_strip_all_tags()` Klartext, weil eine Antwort jetzt einen Link trägt.
- **Intent-Trennung in zwei Richtungen:** Die Portal-FAQ bleibt und verlinkt Portal-Suchende auf `/solar-leads-kaufen-alternative/` (Ziel über `hu_get_solar_cluster_link_map()`). Der Eyebrow grenzt sichtbar gegen Modul-Großhandel ab — `b2b-handel pv`, `b2b solar panels` u. a. brachten 12 Impressionen ohne jede Chance.
- `Service`-Schema nachgezogen (`name`, `alternateName`, `serviceType`, `description`). Slug, Canon-Werte, Matrix und Proof-Band aus #150 unverändert, kein CSS.

### `/b2b-solar-leads/`: Einstieg auf „pv termine b2b" ausgerichtet, unbelegte Zahlen entschärft

- **Ausgangslage:** Die Seite rankt laut `seo-research/2026-07/reports/gsc-verlauf-2026-07-20.md` für `pv termine b2b` auf Position 9,7 — 45 Impressionen, **0 Klicks**, im Report als „dichtester Klick-Kandidat" markiert. Ursache im Repo auffindbar: Der Title verspricht seit #147 „PV-Termine", aber H1 und Lead erwähnten sie nicht, und der Abschnitt „PV-Termine im B2B" stand an dritter Stelle hinter zwei Kartenrastern.
- **H1 zieht jetzt zum Title** („B2B Solar Leads & PV-Termine für gewerbliche Projekte ab 50.000 €"); „B2B Solar Leads" bleibt vorn, weil das das rankende Asset ist. Der Title selbst (Forced-Map) bleibt unverändert. Lead greift Termine auf. Der Termin-Abschnitt rückt direkt hinter den Einstieg, Anker `#pv-termine` bleibt erhalten.
- **Vier unbelegte Kennzahlen entschärft** (`page-b2b-solar-leads.php`, `$b2b_facts`). Herkunft war nicht mehr rekonstruierbar. Statt pauschal zu löschen, nach Risiko behandelt: Der `50.000 €`-Wert ist jetzt als **eigenes Fit-Kriterium** ausgewiesen (deckungsgleich mit der Money Page, „B2B ab ca. 50 k € pro Projekt"), `100–180 Tage` → `3–6 Monate` (falsche Präzision raus), die Entscheiderzahl als „in der Regel" markiert. **Entfernt: „< 5 % qualifizierte Gewerbe-Anfragen in B2C-Lead-Portalen"** — die einzige bezifferte Negativ-Aussage über fremde Anbieter, unbelegt und wettbewerbsrechtlich angreifbar. Ersetzt durch eine nachprüfbare Beobachtung über die Bauweise der Portal-Formulare („0 Felder" fragen Anschlussleistung, Dachstatik oder EEG-Status ab). Der Block ist zusätzlich sichtbar als Orientierungsgröße gekennzeichnet, nicht als Marktstudie.
- **Termin-Vergleich als Matrix** statt zwei gestapelter Panels: `.hu-intercept__matrix` war bereits im geteilten Stylesheet vorhanden, wurde aber nur von `/eigene-leadgenerierung-vs-portale/` genutzt. Zeilenweiser Vergleich (Qualifizierung · Bezahlt wird · Kriterien setzt · Gemessen wird) — neuer Look ohne neues Layout-CSS, und der Baustein, der diese Seite von ihren fünf Geschwistern unterscheidet.
- **Bug beim Wiederverwenden der Matrix gefunden und behoben:** Unter 760px blendet die Komponente die Kopfzeile aus und injiziert die Spaltenbedeutung per `::before` — mit **fest verdrahtetem** „Portal mieten: " / „Eigenes System: ". Auf der Termin-Matrix wären das die falschen Labels gewesen. Die Strings kommen jetzt aus `--matrix-rent-label` / `--matrix-own-label` mit den bisherigen Werten als Default; die TCO-Seite bleibt unverändert.
- **Proof früh sichtbar:** Die Case-Study-Kennzahlen standen nur in einer Button-Beschriftung und einer FAQ-Antwort. Jetzt als kompaktes Kennzahlen-Band direkt unter den Hero-CTAs, gespeist aus `hu_e3_canon()`. Fallstudie bleibt anonym.
- **Interne Links:** Der tote Verweis „siehe Branchen-Money-Page" (ohne Link) zeigt endlich auf die Money Page — mit beschreibendem Anker statt Struktur-Vokabular. Dazu drei kontextuelle Links im Fließtext, Ziele aus `hu_get_solar_cluster_link_map()` statt hart verdrahtet, damit sie mit den automatischen Cluster-Links konsistent bleiben.
- CSS ausschließlich auf `[data-track-page="b2b-solar-leads"]` gescoped — die Datei ist von sechs Intercept-Seiten geteilt. Title/Description, Slug und Canon-Werte unverändert.

### Solar-Money-Page: Copy geschärft, heller Einstieg, Portal-Kosten-Rechner

- **H1 trug kein Money-Keyword.** `docs/seo/money-keyword-implementation.md` (Phase 1) verlangt H1, Hero und die ersten 150 Wörter auf genau einen Cluster; der SEO-Title (`inc/seo-meta.php`) führte bereits „Leadgenerierung Photovoltaik & Wärmepumpe ohne Portale", die H1 („Hören Sie auf, Anfragen zu mieten.") zog nicht mit. Jetzt „Hören Sie auf, Photovoltaik-Anfragen zu mieten." — Hook bleibt, Cluster sitzt drin. Der harte `<br />` ist raus, `.hu-display` bricht per `text-wrap: balance` über alle Breiten sauberer.
- **Brand-Verstoß:** Trust-Item „B2B · DACH · eigener Vertrieb" — `docs/standards/BRAND_AND_COPY.md` listet `B2B` als Positionierung unter den Hard Bans. Ersetzt durch „Solar · Wärmepumpe · Speicher · DACH". „Fit-Entscheid" (erfundenes Kompositum) → „Fit-Befund in 48 h".
- **Gebrochene Deklination an drei Stellen:** `HU_E3_CASE_LABEL` („mittelständischer PV-Installationsbetrieb") wurde als Kompositum verwendet („im …-Case", „den …-Case"). Aufgelöst zu „die Fallstudie" — grammatisch korrekt, weiterhin anonym; der Canon-Wert selbst bleibt unangetastet.
- **Person-Signal ergänzt:** Die Seite verkauft ein 12.000–18.000-€-Vorhaben, nannte aber nirgends einen Menschen. Eine ruhige Zeile in der Marktcheck-Karte, bewusst **außerhalb** des Quiz-Mountpoints, damit sie über alle drei Schritte stehen bleibt.
- **Heller Einstieg (Hero, Anfrage-Kette, Trust-Strip)** statt durchgehend Ink: Zielgruppe sind Solar-/SHK-Entscheider, die weiße Fachbetriebs-Websites gewohnt sind. Umgesetzt als reiner Token-Swap in `assets/css/solar-leadgenerierung-solara.css` (Abschnitt 14) — das Stylesheet ist fast vollständig `var()`-getrieben, die Komponenten bleiben unverändert. Palette identisch zur hellen Variante auf `/waermepumpen-leads/`. Die Proof-Bar darunter bleibt dunkel und wirkt als Kontrast-Anker vor der Sticky-Nav.
- **Kupfer in zwei Rollen getrennt:** `--accent` (Fläche) und neu `--accent-ink` (Text). `#E08A3C` als Text auf Papier erreicht nur 2,42:1 und wäre durch WCAG AA gefallen; `--accent-ink` (`#8f4f24`) liegt bei 5,74:1. Alle Text-Rollen im hellen Bereich sind explizit umgestellt, alle Paare rechnerisch ≥ 4,9:1 geprüft.
- **CPL-Chart:** Farben lagen als SVG-Präsentationsattribute im Template (`fill="#5C5A52"` etc.) und hätten im hellen Hero nicht mitgezogen. Ersetzt durch Klassen (`.sol-cpl-grid`, `.sol-cpl-tick`, `.sol-bar`, `.sol-bar-label`), eingefärbt aus Tokens.
- **Neu: Portal-Kosten-Rechner** in Sektion 04 (`.sol-calc`). Zwei Slider (Anfragen/Monat, Preis pro Portal-Anfrage) gegen Setup-Spanne und Hosting aus derselben Quelle wie die Modellkarten. Bewusst **kein** eigener Zeitraum-Zustand — der Zeitraum kommt vom bestehenden 12/24/36-Picker, damit Karten, Fazit und Rechner nie auseinanderlaufen. SSR rendert die Startwerte fertig aus, ohne JS steht eine korrekte statische Rechnung. Bei kleinem Volumen sagt der Rechner ausdrücklich, dass sich der Aufbau im gewählten Zeitraum **nicht** trägt — Abrat statt Ersparnis-Versprechen.
- **Footer-CTA saß linksbündig statt zentriert** (`ft--energy-minimal`): `.ft__cta` setzt global `align-self: flex-start` (unter 900px `stretch`) und schlug damit das `align-items: center` von `.ft__energy-shell`. Fix per höherer Spezifität in `style.css`.
- Canon-Werte, REST-Contract, Tracking-Attribute und SEO-Meta unverändert.

### Semrush Site-Audit: Meta-Descriptions & interne Link-Hygiene

- **Fehlende/doppelte Meta-Descriptions behoben** (`inc/seo-meta.php`, Forced-SEO-Map): `/stack-solar/` hatte keine Meta-Description; `/owned-leads-statt-ad-miete/` und `/meta-ads-fuer-b2b/` teilten sich eine identische. Jeder Slug bekommt jetzt eine eigene, eindeutige Description (nur `description` gesetzt, Titel bleiben unberührt).
- **`rel="nofollow"` von internen Impressum-/Datenschutz-Links entfernt** (`template-parts/site-footer.php`, `template-parts/footer-cta.php`): nofollow auf internen Links widerspricht Googles Empfehlung und war Auslöser für ~168 Semrush-Warnungen. Die Seiten bleiben über `noindex`/robots aus dem Index — der nofollow war redundant. Sponsored-/Affiliate-Links (HostPress) bleiben korrekt `sponsored nofollow`.
- **Nicht im Repo behebbar** (als manuelle WordPress-Admin-Aufgabe dokumentiert): 161 „Broken internal links" mit Zielen `?page_id=13035` / `?page_id=14283` (beide 404) stammen aus Menü/Widgets in der WordPress-DB, nicht aus dem Theme. Ebenso: fehlendes HSTS auf `www.hasimuener.de` (Server/Hosting).

### WordPress-Agentur-Seite: benachbarte lokale Suchintents via FAQ

- Datengrundlage (Semrush DE): `/wordpress-agentur-hannover/` rankt #11 für „wordpress agentur hannover" (480/Monat); die Seite ist On-Page/intern (24 Link-Quellen, `ProfessionalService`-Schema) bereits ausgereizt. Benachbarte lokale Begriffe mit realem Volumen bleiben ungenutzt: „webdesign hannover" (1.600), „internetagentur hannover" (590), „webagentur hannover" (480).
- Zwei FAQ-Items in `nexus_get_agentur_faq_items()` (`inc/helpers.php`) ergänzt, die diese Zweitbegriffe **als Abgrenzung** aufgreifen (Webdesign-/Internet-/Webagentur vs. Anfrage-System) — Positionierung wird gestärkt, nicht verwässert. Items fließen automatisch in Seite und `FAQPage`-JSON-LD.
- `inc/org-schema.php`: FAQ-Schema-Cap (`array_slice(..., 0, 8)`) entfernt, damit das Schema den vollen sichtbaren FAQ-Satz spiegelt (Schema == sichtbarer Inhalt).

### Kontaktformular: stille `invalid_focus_type`-400er behoben (verlorene Leads)

- **Ursache:** `/kontakt/` rendert das `focus`-Dropdown aus dem vollen `nexus_get_contact_focus_options()`-Satz, bietet standardmäßig aber nur die Anfragetypen `audit`/`implementation`/`ongoing` an. Vier Themen (`followup_scope`, `existing_client`, `question`, `cooperation`) passen zu keinem davon und wurden vom REST-Contract (`nexus_validate_contact_request_payload`) zwangsläufig mit `invalid_focus_type` (400) abgelehnt. Einziger Schutz war der JS-Optionsfilter — bei Cache-/JS-Aussetzern oder Flow-Races rutschte die Kombi durch: stiller 400, **keine Bestätigungs-/Benachrichtigungsmail, verlorener Lead** (u. a. Ads-Traffic mit `gclid`).
- `page-kontakt.php`: `focus`-Optionen serverseitig auf Themen gefiltert, die zu mindestens einem angebotenen Anfragetyp passen (`$public_focus_options`). Deep-Links wie `?type=project&focus=followup_scope` bleiben funktionsfähig, weil dort `analysis`/`project` angeboten werden.
- `assets/js/contact.js`: zusätzlicher Combo-Guard in `validateForm()` — eine inkompatible Thema/Typ-Kombination wird jetzt clientseitig mit klarer Meldung blockiert statt als stiller 400 gesendet.
- REST-Contract und CRM-Payload unverändert.

## 2026-06

### Über-mich (Editorial) geschärft — person-first, ohne Money-Page-Redundanz

- `/uber-mich/` (Editorial-Template): Hero führt jetzt mit Outcome-H1 („Ich beende Portal-Abhängigkeit …") statt der Küchentisch-Story; darunter nur **ein ruhiger Textlink** zum Marktcheck (kein Hero-Button).
- Biografischer Pfad als Kompetenz-Beleg geschärft; **Fit-/Standards-Sektion** („Mit wem ich arbeite") und interner **Experten-Link-Cluster** (6 Sub-Pages, 3 System-Ebenen) für E-E-A-T und `#person`-Signal.
- **Veraltete E3-Zahlen entfernt** (vorher hardcodet „−83 %" / „neun Monaten"); E3 erscheint nur noch als **ein** Satz im Bio-Text mit korrektem Canon (über 85 % / 6 Monate aus `hu_e3_canon()`) plus Link zum E3-Case — kein dupliziertes Zahlen-Band.
- Bewusst **schlank** gehalten: Proof-Zahlen-Band, Founding-Cohort-/Scarcity-Band und mobiler Sticky-CTA gehören auf die Money-Pages, nicht auf die Personen-Seite — daher hier nicht gerendert. **Ein** primärer Marktcheck-CTA am Seitenende; Styles im Cream/Kupfer-System (`about-editorial.css`), `prefers-reduced-motion` respektiert.

### Solar-Proof früher, Cluster-Hublink & Koko-Cockpit-Beobachtbarkeit

- `/solar-waermepumpen-leadgenerierung/`: kompakter **Proof-Bar** direkt unter Hero/Trust-Strip (E3-Kennzahlen 150 €→22 €, 1.750+, 12 %, über 85 % als Teaser → `#ergebnisse`), damit der Umsatzbeleg nicht erst bei ~73 % Scrolltiefe sichtbar wird; Kontextzeile macht die CPL-Zahl belegbar statt kontextlos. Funnel und Marktcheck-Formular unverändert.
- Sekundärer, risikoärmerer CTA an der Fit-Sektion („Erst den E3-Case ansehen" → `#ergebnisse`) neben dem primären Marktcheck-CTA — für Besucher, die noch nicht intake-bereit sind.
- Kannibalisierung „leadgenerierung photovoltaik": Cluster-Subpages setzen jetzt einen term-nahen **Einbahn-Hublink** zurück auf die Money Page (`hu_render_solar_cluster_links()`), um das Signal auf dem Hub zu bündeln (Hub → Cluster bestand bereits über die Vertiefung-Sektion).
- SEO-Cockpit / Koko: **Live-Selbsttest** in der Diagnostik (HTTP-Status + Roh-Body + normalisiertes Ergebnis für die aktuelle Range), Koko-Fehler werden **nicht mehr als 0/0 gecacht** (kein eingefrorener Cron-Fehler überdeckt echte Daten), und „Cache bis n/a" zeigt bei Sync-Fehler einen interpretierbaren Status. Macht den 0/0-Bug diagnostizierbar — die sichtbare „0 zurück"-Note belegt ein erfolgreiches 200→0/0 (Range/Param/Shape), keinen Permission-Fehler.

### Solar-Landingpage v2: Ink/Creme-Redesign + Marktcheck-CRO

- `/solar-waermepumpen-leadgenerierung/` komplett auf das geteilte `.hu-hp`-Brand-System umgestellt (Ink `#0B0F12` im Wechsel mit Creme-Sektionen, Kupfer `#E08A3C`) — Basis ist der Claude-Design-Handoff „Solar-Leadgenerierung v2"; `homepage-redesign.css` wird wiederverwendet, das Page-CSS ist nur noch ein schlankes Delta (~3.100 → ~950 Zeilen).
- Neuer Hero: Headline „Hören Sie auf, Anfragen zu mieten." mit Kupfer-Akzent, rotierende Kupfer-Sonne (32 Strahlen, SVG, `prefers-reduced-motion`-sicher), animiertes CPL-Balken-Chart (150 € → 22 € aus `hu_e3_canon`), Count-up-Stats; Marktcheck-Formular bleibt above the fold in der rechten Spalte (dunkle Card).
- Marktcheck-CRO: Frage-Copy verkürzt („Wer verkauft bei Ihnen?" / „Was kostet Sie Akquise heute?" / „Wohin darf der Befund?"), Telefon-Feld entfernt, **Firmen-PLZ-Feld neu** (`postal_code`, fünfstellig validiert — speist die Regions-Verfügbarkeitsprüfung, Backend-Feld existierte bereits); REST-Kontrakt, Attribution, GA4-Events und Success-Logik unverändert (Smoke-Test grün).
- Sektionen auf Kit-Komponenten umgezogen: Kostenkarten, Compare („Portal-Miete vs. eigener Anfrageweg"), Phasen mit Asset-Panel, CAPEX/OPEX als Modell-Karten (12/24/36-Toggle erhalten), E3-Proof als Vorher/Nachher + Stats, Fit-Grid, Risiko-Umkehr „Diagnose vor Pitch.", Vertiefung, FAQ (alle 11 Items + Schema erhalten); Founding-Cohort- und Final-CTA-Sektion zu einem `hu-final-cta`-Block gemerged.
- System-Diagramm-Sektion (`#system-bild`) aufgelöst — Inhalt kondensiert ins Phasen-Asset-Panel; Canon-Fix: kundenseitige „Module" durchgängig zu „Bausteinen".

### CRO-Microcopy & CTA-Konsistenz

- Trust-Microcopy „kostenlos & unverbindlich" für den Marktcheck am Homepage-Hero und in der Solar-Quiz-Card (SSR-Bullet + JS-Fineprint) — Kostenunsicherheit war der größte Reibungspunkt für kalten Traffic; die bezahlte Diagnose bleibt davon klar getrennt.
- Server-Side-Tracking in Phase 02 der Solar-Methoden-Karte mit Klartext-Nutzen erklärt (eigener Server statt nur Browser, belastbare Zahlen trotz Ad-Blockern).
- Freemail-Validierungstexte im Solar-Quiz begründen jetzt die Firmen-Domain-Anforderung und bieten einen direkten E-Mail-Ausweg statt harter Ablehnung.
- CTA-Tap-Targets vereinheitlicht: Header-Audit-Link auf 44px, Sticky-CTA-Primary auf 48px.
- Kontaktformular: Error-Summary mit kräftigerem Rahmen und Titel, Submit-Button mit Lade-Spinner inklusive `prefers-reduced-motion`-Fallback.

## 2026-05

### Public Release: White-Label-Partner-Modell

- Neue Premium-Landingpage `/whitelabel-retainer/` für Agentur-Partner: Dark-Mode-Hero mit KPI-Dashboard, Problem/Lösung/Stack-Sektionen, Live-Beleg, technischer Code-Beleg, Testprojekt-Scope.
- Eigenes Stylesheet `assets/css/whitelabel.css` (loadet nur auf der Seite), animierte KPI-Counter mit `prefers-reduced-motion`-Respekt, Sticky Mobile CTA.
- Kanonische E3-KPIs aus `hu_e3_canon()` durchgängig referenziert (150 €→22 €, 1.750+, +85 %, 12 %).

### Header- und Footer-Aufräumung

- Globaler Header-Eyebrow „Anfrage-Systeme für Solar & Wärmepumpe." entfernt — passte nicht zu allen Audiences.
- Footer per-template: Tagline + Copyright auf der Whitelabel-Seite agentur-bezogen, sonst Solar wie gehabt.
- Solar-Marktcheck-CTA wird auf Whitelabel-Seite ausgeblendet (kein doppelter CTA-Konflikt).
- Energy-Footer-Variante (`/solar-waermepumpen-leadgenerierung/`) bekommt eigene CSS-Regeln — vorher unstyled / linksbündiger Block.
- Doppelten „Datenschutz ansehen"-Link und Einzel-Bullet-Liste aus dem Footer entfernt.
- Globaler Footer-Link „Für Agenturen: White-Label" unter Kontakt — sitewide internes SEO-Signal.

### GitHub-Repo öffnen für Agentur-Publikum

- `README.md` neu für Agentur-First-Impression: Stack, Engineering-Standards, Funnel-Architektur, Live-Site-Verweise.
- `SECURITY.md` aus GitHub-Default-Template in echte Policy gewandelt.
- `LICENSE` neu — Source-Available, All Rights Reserved.
- GitHub-Icon im Footer (nur auf `/whitelabel-retainer/`) als Trust-Signal verlinkt.
- Alle `Hasim-hannover`-Verweise auf den tatsächlichen `Hasim-Uner`-Account korrigiert (Footer, Person-/Organization-Schema, Homepage-GitHub-Proof).

## 2026-03-14

- Homepage von editorgetriebenem Content auf ein versioniertes `front-page.php` Template umgestellt.
- Homepage-Hero verdichtet: kürzere Subline, 3 dominante KPI-Proofs, 1 primärer Audit-CTA, sekundäre Links nur noch subtil.
- Homepage-Flow neu geordnet: Hero → Track Record → Problem-Frame → WGOS System → Case Teaser → Audit CTA → FAQ → Knowledge Base.
- Homepage-Knowledge-Base visuell beruhigt und bestehendes Blog-Grid-JS auf den tatsächlichen Knowledge-Base-Container begrenzt.
- `/wordpress-agentur-hannover/` Hero als zweispaltigen Einstieg mit Proof-Card, größerem Audit-CTA und kompakter Local-Trust-Message refaktoriert.
- Problemsektion der Agentur-Seite in 3 scanbare Pain Cards plus Lösungskarte modularisiert.
- Case-Study-Bereich der Agentur-Seite in Ausgangslage, Maßnahme, Ergebnis und CTA strukturiert.
- CTA-Hierarchie auf beiden Seiten reduziert und konsistenter auf Audit-first ausgerichtet.
- Neue Styles auf bestehende Design-Tokens aufgebaut und für Dark- und Light-Mode über bestehende Variablen gehalten.
