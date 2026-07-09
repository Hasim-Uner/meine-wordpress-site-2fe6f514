# CHANGELOG

## 2026-07

### Semrush Site-Audit: Meta-Descriptions & interne Link-Hygiene

- **Fehlende/doppelte Meta-Descriptions behoben** (`inc/seo-meta.php`, Forced-SEO-Map): `/stack-solar/` hatte keine Meta-Description; `/owned-leads-statt-ad-miete/` und `/meta-ads-fuer-b2b/` teilten sich eine identische. Jeder Slug bekommt jetzt eine eigene, eindeutige Description (nur `description` gesetzt, Titel bleiben unberührt).
- **`rel="nofollow"` von internen Impressum-/Datenschutz-Links entfernt** (`template-parts/site-footer.php`, `template-parts/footer-cta.php`): nofollow auf internen Links widerspricht Googles Empfehlung und war Auslöser für ~168 Semrush-Warnungen. Die Seiten bleiben über `noindex`/robots aus dem Index — der nofollow war redundant. Sponsored-/Affiliate-Links (HostPress) bleiben korrekt `sponsored nofollow`.
- **Nicht im Repo behebbar** (als manuelle WordPress-Admin-Aufgabe dokumentiert): 161 „Broken internal links" mit Zielen `?page_id=13035` / `?page_id=14283` (beide 404) stammen aus Menü/Widgets in der WordPress-DB, nicht aus dem Theme. Ebenso: fehlendes HSTS auf `www.hasimuener.de` (Server/Hosting).

### WordPress-Agentur-Seite: benachbarte lokale Suchintents via FAQ

- Datengrundlage (Semrush DE): `/wordpress-agentur-hannover/` rankt #11 für „wordpress agentur hannover" (480/Monat); die Seite ist On-Page/intern (24 Link-Quellen, `ProfessionalService`-Schema) bereits ausgereizt. Benachbarte lokale Begriffe mit realem Volumen bleiben ungenutzt: „webdesign hannover" (1.600), „internetagentur hannover" (590), „webagentur hannover" (480).
- Zwei FAQ-Items in `nexus_get_agentur_faq_items()` (`inc/helpers.php`) ergänzt, die diese Zweitbegriffe **als Abgrenzung** aufgreifen (Webdesign-/Internet-/Webagentur vs. Anfrage-System) — Positionierung wird gestärkt, nicht verwässert. Items fließen automatisch in Seite und `FAQPage`-JSON-LD.
- `inc/org-schema.php`: FAQ-Schema-Cap (`array_slice(..., 0, 8)`) entfernt, damit das Schema den vollen sichtbaren FAQ-Satz spiegelt (Schema == sichtbarer Inhalt).

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
