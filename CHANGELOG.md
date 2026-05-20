# CHANGELOG

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
