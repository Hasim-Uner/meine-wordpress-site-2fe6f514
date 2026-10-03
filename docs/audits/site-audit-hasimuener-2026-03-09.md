# Site Audit Report: hasimuener.de
Datum: 2026-03-09
Scope: Vollaudit
Seiten geprueft: 26
Quelle: Beides

## Executive Summary

- Gesamt-Score: 61/100
- Issues: 5 CRITICAL | 8 HIGH | 8 MEDIUM | 4 LOW
- Top 3 Quick Wins:
  1. Alle verbleibenden Live-/Editor-Links von `/case-studies/` auf `/case-studies-e-commerce/` und von `/kontakt/` auf `/kontaktiere-mich/` umstellen -> Proof-Layer und Kontaktpfad werden wieder kanonisch.
  2. Alle CTAs auf `/wordpress-tech-audit/` durch den aktiven Growth-Audit-Pfad ersetzen -> 404-Bruch auf SEO-/Content-Seiten verschwindet sofort.
  3. Im aktiven Audit-Funnel DSGVO-Consent und noindex/Meta-Fix fuer `/danke-anfage-audit/` nachziehen -> Primaer-Funnel wird rechtlich und SEO-seitig sauber.

## Findings nach Severity

### 🔴 CRITICAL
| Issue | Seite(n) | Problem | Fix | Impact |
|---|---|---|---|---|
| Case-Studies-Ziel ist systemweit falsch | `/`, `/growth-audit/`, `/wgos/`, `/uber-mich/`, `/wordpress-agentur-hannover/`, Blog-Header, Footer, WGOS-Proof | 143 interne Links zeigen auf `/case-studies/`; live landet das auf `/e3-new-energy/`, waehrend `/case-studies-e-commerce/` als Overview 0 eingehende Links hat. | Theme-Fix umgesetzt; zusaetzlich verbleibende WordPress-Menues/Editor-Inhalte auf `/case-studies-e-commerce/` umstellen. | Proof-Layer ist fehlgeleitet, die Uebersichtsseite ist praktisch verwaist. |
| SEO-/Content-CTAs zeigen auf 404 | `/wordpress-seo-hannover/`, `/roi-rechner/`, `/owned-leads-statt-ad-miete/` | 7 interne Links zeigen auf `/wordpress-tech-audit/`; live liefert die URL 404. | Im WordPress-Editor alle CTAs auf `/growth-audit/` oder ein live existierendes Angebot umstellen. | Organischer High-Intent-Traffic endet direkt im Leerlauf. |
| Tier-1-Service-Seiten brechen die CTA-Hierarchie | `/conversion-rate-optimization/`, `/ga4-tracking-setup/`, `/core-web-vitals/` | Primaere CTAs fuehren in Cal.com oder auf Seitenanker, nicht in den Growth Audit. | Editor-/Template-Content auf Audit-first umstellen; Cal.com nur sekundaer lassen. | Der verbindliche Primaer-Funnel wird auf mehreren Money Pages ausgehebelt. |
| Audit-Formular ohne DSGVO-Consent | `/growth-audit/` | Das aktive Multi-Step-Formular hat kein Checkbox-/Consent-Feld und keinen expliziten Datenschutzhinweis im Formular. | Mit Freigabe in `template-parts/audit-page-shell.php` plus `review-funnel.js` und `review-crm.php` sauber ergaenzen. | Rechtliches Risiko direkt im Primaer-Conversion-Pfad. |
| Erwartete Hub-Struktur unvollstaendig | Erwartet: Strategie, CRO, Performance Hubs | Live auffindbar sind nur `/category/seo/`, `/category/tracking/`, `/category/owned-leads/`; explizite CRO- und Performance-Hubs fehlen, eine klare Strategie-URL fehlt ebenfalls. | Fehlende Hubs anlegen oder bestehende Struktur eindeutig mappen und intern verlinken. | Pillar-/Cluster-Architektur ist fuer SEO und Funnel-Einstiege nicht vollstaendig. |

### 🟠 HIGH
| Issue | Seite(n) | Problem | Fix | Impact |
|---|---|---|---|---|
| WGOS Meta ist unvollstaendig | `/wordpress-growth-operating-system/` | Title faellt auf den Slug zuruueck, Meta Description fehlt komplett. | Rank-Math-/Editor-Meta nachpflegen; keine Aenderung in `seo-meta.php` ohne Freigabe. | Zentrales Retainer-Angebot verschenkt CTR und klare SERP-Positionierung. |
| Doppelte H1 auf mehreren wichtigen Seiten | `/wordpress-seo-hannover/`, `/core-web-vitals/`, `/case-studies-e-commerce/`, `/kontaktiere-mich/`, `/danke-anfage-audit/` | Live werden jeweils zwei H1 ausgegeben, meist Parent-Template plus Editor-Hero. | Im WordPress-Editor bzw. Template-Zuordnung bereinigen; repo-seitig nur dokumentiert. | Keyword-Signal und Hierarchie auf mehreren Einstiegsseiten sind verwaessert. |
| SEO-Service ohne Service-Schema | `/wordpress-seo-hannover/` | Live erscheinen `BreadcrumbList`, `LocalBusiness`, `FAQPage`, aber kein `Service`. | Mit Freigabe in `org-schema.php` den Slug `wordpress-seo-hannover` ergaenzen oder Rank Math angleichen. | Schwacher strukturierter Angebotskontext fuer eine Tier-1-Service-Seite. |
| Thank-you-Seite indexierbar und Meta kaputt | `/danke-anfage-audit/` | Die Seite ist indexiert, liegt in der Sitemap und gibt als Description CSS-Text aus. | Slug-/noindex-Logik in `seo-meta.php` nachziehen und Editor-Meta reparieren. | Duenne Utility-Seite kann indexiert werden und wirkt im Snippet defekt. |
| Kontaktseite ohne Formular | `/kontaktiere-mich/` | Kein Formular, keine Mail-CTA, nur indirekte Call-/Footer-Pfade; die kanonische URL hat 0 interne Links. | Kontakt-Content im Editor erweitern und interne Verlinkung auf die kanonische URL umstellen. | Direkter Kontaktweg unterperformt trotz eigener Seite. |
| Case-Studies-Overview zieht in Cal.com statt in den Audit | `/case-studies-e-commerce/` | Primaere CTAs sind Strategiegespraech/Audit buchen via Cal.com; Audit erscheint nur spaet/seitenweit im Footer. | CTA-Hierarchie im Editor auf Audit-first umstellen. | Proof-Seite beschleunigt in die Eskalation statt in die Qualifizierung. |
| Repo und Live laufen bei Schluesselseiten auseinander | `/wordpress-seo-hannover/`, `/core-web-vitals/`, `/case-studies-e-commerce/`, `/kontaktiere-mich/` | Live laufen diese Seiten als `page-template-default`; die vorhandenen Repo-Templates greifen nicht oder nur teilweise. | Template-Zuordnung im WP-Admin pruefen oder Live-Content in die aktiven Templates migrieren. | Repo-Fixes koennen an wichtigen Seiten wirkungslos bleiben. |
| Cornerstone-Schema doppelt/ueberlappt | `/technisches-seo-performance-fundament/` | Live erscheinen u.a. `BlogPosting`, `Article` und zwei `BreadcrumbList`-Bloecke. | Rank-Math-/Theme-Schema gegeneinander abgleichen; keine Aenderung ohne Freigabe. | Validator-/Rich-Result-Risiko auf dem wichtigsten Pillar-Beitrag. |

### 🟡 MEDIUM
| Issue | Seite(n) | Problem | Fix | Impact |
|---|---|---|---|---|
| Meta-Laengen haeufig ueber Ziel | `/ga4-tracking-setup/`, `/uber-mich/`, `/case-studies-e-commerce/`, `/category/tracking/`, `/category/owned-leads/`, mehrere Posts | Mehrere Titles/Descriptions werden in den SERPs abgeschnitten. | Editor-/Rank-Math-Metas kuerzen. | CTR- und Snippet-Qualitaet leiden. |
| Duplicate Description in Blog | `/meta-ads-fuer-b2b/`, `/owned-leads-statt-ad-miete/` | Beide Posts nutzen dieselbe Meta Description. | Eine Description unique formulieren. | Duplicate-Snippets mindern Relevanz und CTR. |
| Schwache Meta auf Proof-/Service-Seiten | `/e3-new-energy/`, `/conversion-rate-optimization/` | E3 liefert nur `case-studies`, CRO nur eine halbe Teaser-Zeile als Description. | Editor-/SEO-Meta sauber nachziehen. | Proof- und Service-Seiten wirken im Snippet unpraezise. |
| Default-Seiten tragen zusaetzlichen Blog-Header | `/wordpress-seo-hannover/`, `/core-web-vitals/`, `/case-studies-e-commerce/`, `/kontaktiere-mich/`, `/danke-anfage-audit/` | Neben dem globalen Header wird live ein `nexus-blog-header` ausgegeben. | Template-Zuordnung bzw. ungeplante Header-Einbindung im Live-Setup pruefen. | Navigation und Seitenkontext wirken inkonsistent. |
| Design-System driftet zwischen Template- und Editor-Seiten | SEO/CWV/CRO/GA4/Case Overview | Unterschiedliche Button-Systeme (`btn`, `cro-btn`, `ga4-btn`, Theme-Buttons) und Hero-Aufbauten. | Editor-/Template-Angleichung an das Nexus-Design-System. | Professioneller Gesamteindruck leidet. |
| Tracking-Attribute nicht seitenweit konsequent | Footer, Blog-/Editor-CTAs, Utility-Seiten | Viele primaere Template-CTAs haben `data-track-action`, Footer-/Editor-Links oft nicht. | Tracking-Markup fuer wichtige CTAs vereinheitlichen. | CTA-Auswertung bleibt lueckenhaft. |
| Rechtliche Seiten ohne Canonical | `/impressum/`, `/datenschutz/` | Beide Seiten sind `noindex`, geben live aber keinen Canonical aus. | Nur bei Bedarf nachziehen; geringe Prioritaet wegen `noindex`. | SEO-Hygiene-Luecke, aber kein akuter Traffic-Verlust. |
| Kontakt-Link laeuft ueber Redirect | sitewide Footer-/Utility-Pfade | 34 interne Links zeigen auf `/kontakt/`, live wird auf `/kontaktiere-mich/` umgeleitet. | Theme-Fix umgesetzt; restliche Menue-/Editor-Links noch angleichen. | Unnoetiger Redirect auf einem Utility-Pfad. |

### 🟢 LOW
| Issue | Seite(n) | Problem | Fix | Impact |
|---|---|---|---|---|
| WGOS-Modullinks waren generisch | `/wordpress-growth-operating-system/` | Fuenf Modul-Links hiessen nur `Mehr erfahren`. | Im Theme auf beschreibende Anchors umgestellt. | Bessere Link-Qualitaet und Klarheit. |
| WGOS-Speed-Link war tot | `/wordpress-growth-operating-system/` | Link zeigte auf `/core-web-vitals-optimierung/` (404). | Im Theme auf die kanonische Core-Web-Vitals-URL umgestellt. | 404-Risiko im Offer-Template entfernt. |
| Homepage-E3-Link nutzte alten Fallback | `/` | Der E3-Link fiel auf alte Case-Study-Pfade zurueck. | Im Theme auf `/e3-new-energy/` korrigiert. | Proof-Linking wird sauberer. |
| Statischer WGOS-Proof-Block nutzte alten Case-Studies-Pfad | `/wordpress-growth-operating-system/` | Das HTML-Asset verlinkte direkt auf `/case-studies/`. | Im Asset auf `/case-studies-e-commerce/` geaendert. | Konsistenz ueber Template und Asset verbessert. |

## Seiten-Scorecards

### Startseite – /
Tier: 1 | Score: 78/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 22/25 | Audit CTA sichtbar und klar; sekundaerer Proof-Link zeigte auf falsche Case-Studies-URL. |
| Trust & Proof | 13/15 | Proof-Zahlen und Cases vorhanden. |
| Content & Struktur | 15/20 | Gute Positionierung, aber nicht alle Tier-1-Angebote direkt verlinkt. |
| Meta & Schema | 12/15 | Meta solide; Schema-Mix nicht auffaellig kritisch. |
| Interne Links | 7/10 | Viele interne Links, aber Case-Studies-Ziel war kanonisch falsch. |
| Design-Konsistenz | 6/10 | Starker Nexus-Look; Footer/Header waren vor Fix nicht ganz kanonisch. |
| Mobile & Speed | 3/5 | Grundsaetzlich sauber, aber keine Live-CWV-Pruefung per Lighthouse. |

Befunde: Starke Homepage mit sauberem Audit-first Above-Fold; groesster Verlust lag in der falschen Verlinkung des Proof-Layers.

### Growth Audit – /growth-audit/
Tier: 1 | Score: 83/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 22/25 | Klarer Growth-Audit-Fokus, gutes Message Match. |
| Trust & Proof | 12/15 | Prozess und Cases vorhanden. |
| Content & Struktur | 15/20 | Logischer Funnel-Aufbau mit Erwartungen und Next Steps. |
| Meta & Schema | 12/15 | Title/Description/Canonical stark. |
| Interne Links | 8/10 | Gute Weiterleitungen zu Cases, WGOS und About. |
| Design-Konsistenz | 9/10 | Sehr konsistent im aktiven Audit-Shell. |
| Mobile & Speed | 5/5 | Formular und Layout klar strukturiert. |

Befunde: Beste Conversion-Seite im Scope; der groesste offene Blocker ist der fehlende DSGVO-Consent im aktiven Formular.

### WGOS – /wordpress-growth-operating-system/
Tier: 1 | Score: 58/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 19/25 | Audit bleibt primaer, CTA-Wiederholung gut. |
| Trust & Proof | 11/15 | Starke Proof-Elemente und Zahlen. |
| Content & Struktur | 12/20 | Inhaltlich stark, aber einige Modulbeschriftungen und Linkziele waren unscharf. |
| Meta & Schema | 4/15 | Title faellt auf Slug zurueck, Description fehlt. |
| Interne Links | 5/10 | Case-Studies-Link und Core-Web-Vitals-Link waren fehlerhaft; teils generische Anchors. |
| Design-Konsistenz | 5/10 | Visuell eigenstaendig, aber teils zu stark hardcodiert. |
| Mobile & Speed | 2/5 | Kein harter Fehler, aber Template bleibt schwergewichtig. |

Befunde: Starkes Offer-Template mit guter Audit-Hierarchie, aber schwache Meta-Hygiene und mehrere Link-/Anchor-Probleme.

### SEO Service – /wordpress-seo-hannover/
Tier: 1 | Score: 38/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 6/25 | Haupt-CTAs zeigen auf `/wordpress-tech-audit/` (404) oder Cal.com statt Growth Audit. |
| Trust & Proof | 10/15 | Gute Resultate/FAQ sichtbar. |
| Content & Struktur | 12/20 | Inhaltlich stark, aber zwei H1 und Live-Template-Mismatch. |
| Meta & Schema | 7/15 | Meta okay, aber kein `Service`-Schema fuer den Live-Slug. |
| Interne Links | 2/10 | Mehrere interne Links gehen auf 404. |
| Design-Konsistenz | 1/10 | Live laeuft auf Default-Template und traegt zusaetzlichen Blog-Header. |
| Mobile & Speed | 0/5 | Keine gesicherte technische Verifikation. |

Befunde: In der SERP solide, im Funnel aktuell die problematischste Tier-1-Service-Seite.

### CRO Service – /conversion-rate-optimization/
Tier: 1 | Score: 42/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 7/25 | Hero-CTAs fuehren in Cal.com oder zu `mailto`, nicht in den Audit. |
| Trust & Proof | 8/15 | Grundlegende Argumentation vorhanden. |
| Content & Struktur | 10/20 | H1 sauber, aber Funnel-Pfad schwach. |
| Meta & Schema | 8/15 | Description zu generisch. |
| Interne Links | 4/10 | Audit nur im Footer sichtbar. |
| Design-Konsistenz | 3/10 | Eigenes Button-System driftet vom Nexus-Standard. |
| Mobile & Speed | 2/5 | Keine harten Live-Messwerte. |

Befunde: Fachlich passend, aber die Seite umgeht die verbindliche Audit-Qualifizierung fast komplett.

### GA4 Service – /ga4-tracking-setup/
Tier: 1 | Score: 40/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 6/25 | Primaere CTAs fuehren direkt in Cal.com statt in den Growth Audit. |
| Trust & Proof | 8/15 | Solide Positionierung. |
| Content & Struktur | 10/20 | Klarer Nutzen, aber schwacher Funnel-Anschluss. |
| Meta & Schema | 7/15 | Title und Description sind zu lang. |
| Interne Links | 4/10 | Audit nur als Footer-/Utility-Pfad. |
| Design-Konsistenz | 3/10 | Eigenes GA4-Button-System statt seitenweiter CTA-Hierarchie. |
| Mobile & Speed | 2/5 | Keine vertiefte Live-Messung. |

Befunde: Tracking-Thema ist klar, die Conversion-Fuehrung ist es nicht.

### Core Web Vitals Service – /core-web-vitals/
Tier: 1 | Score: 44/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 7/25 | Primarer Above-Fold-CTA fuehrt in Cal.com oder nur tiefer auf dieselbe Seite. |
| Trust & Proof | 9/15 | Gute Nutzenargumente und Performance-Proof. |
| Content & Struktur | 11/20 | Zwei H1; Live laeuft auf Default-Template statt dem Repo-Template. |
| Meta & Schema | 8/15 | Description zu lang. |
| Interne Links | 4/10 | Audit erscheint spaet; Service-Link im WGOS war vorher 404. |
| Design-Konsistenz | 3/10 | Blog-Header plus Default-Template-Kontext verwischt den Service-Charakter. |
| Mobile & Speed | 2/5 | Kein Lighthouse-Live-Lauf im Audit. |

Befunde: Gute Argumentation, aber Above-Fold-Conversion und Template-Zuordnung passen nicht zur CTA-Hierarchie.

### WordPress Agentur – /wordpress-agentur-hannover/
Tier: 1 | Score: 79/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 23/25 | Audit klar primaer, Cases sauber sekundaer. |
| Trust & Proof | 13/15 | Gute Proof-Bar und FAQ. |
| Content & Struktur | 16/20 | Saubere Problem-zu-System-Fuehrung. |
| Meta & Schema | 12/15 | Service-Schema und Meta stimmig. |
| Interne Links | 7/10 | Linkt gut in System, Cases und Services; Case-URL war nicht kanonisch. |
| Design-Konsistenz | 6/10 | Nahe am Design-System, aber Abweichungen zum Footer/Header-Layer. |
| Mobile & Speed | 2/5 | Keine gesonderte Live-Messung. |

Befunde: Eine der staerksten Money Pages; Hauptproblem war die veraltete Case-Studies-Verlinkung.

### Ueber mich – /uber-mich/
Tier: 2 | Score: 76/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 21/25 | Audit vorne gut sichtbar. |
| Trust & Proof | 12/15 | Haltung, Prozess und Cases gut verbunden. |
| Content & Struktur | 16/20 | Starke Positionierung und guter Story-Fluss. |
| Meta & Schema | 9/15 | Description zu lang. |
| Interne Links | 8/10 | Gute interne Fuehrung, Case-Ziel war nicht kanonisch. |
| Design-Konsistenz | 7/10 | Eigenstaendiges, aber klares Seitenbild. |
| Mobile & Speed | 3/5 | Keine harte Live-Messung. |

Befunde: Gute Vertrauensseite; kleinere SEO-Hygiene und alte Case-Studies-Links bremsen den Gesamteindruck.

### Case Studies Overview – /case-studies-e-commerce/
Tier: 2 | Score: 35/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 6/25 | Primaere CTAs fuehren in Cal.com statt in den Audit. |
| Trust & Proof | 10/15 | Inhaltlich als Proof-Layer plausibel. |
| Content & Struktur | 8/20 | Zwei H1; Overview ist intern praktisch nicht auffindbar. |
| Meta & Schema | 6/15 | Description zu lang. |
| Interne Links | 1/10 | 0 eingehende Links; sitewide wird auf `/case-studies/` statt auf diese Seite verlinkt. |
| Design-Konsistenz | 2/10 | Default-Template plus Blog-Header-Kontext. |
| Mobile & Speed | 2/5 | Keine harte Live-Messung. |

Befunde: Der Proof-Hub existiert live, ist aber aus der internen Architektur fast komplett abgekoppelt.

### Case Study E3 – /e3-new-energy/
Tier: 2 | Score: 68/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 18/25 | Audit-CTA klar sichtbar. |
| Trust & Proof | 15/15 | Sehr starke Zahlen und Kontext. |
| Content & Struktur | 15/20 | Gute Fallstudienlogik und FAQ. |
| Meta & Schema | 6/15 | Meta Description ist praktisch leer (`case-studies`). |
| Interne Links | 6/10 | Gute Deep Links; Overview-Link zeigte auf falsche Case-URL. |
| Design-Konsistenz | 6/10 | Starkes Layout, aber nicht komplett aligned mit globalen Navigationsmustern. |
| Mobile & Speed | 2/5 | Keine gesonderte Verifikation. |

Befunde: Starke Proof-Seite mit klarer Wirkung, die vor allem durch schwache Meta-Hygiene verliert.

### Cornerstone – /technisches-seo-performance-fundament/
Tier: 2 | Score: 81/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 18/25 | Audit/SEO-CTAs sauber eingebettet. |
| Trust & Proof | 11/15 | Autor, Praxisfall und Checklisten gut sichtbar. |
| Content & Struktur | 19/20 | Sehr starkes Longform-Stueck mit klarer Entscheider-Perspektive. |
| Meta & Schema | 11/15 | Gute Meta, aber Schema dupliziert `BreadcrumbList` und `Article`-Typen. |
| Interne Links | 9/10 | Sehr gute interne Verlinkung in Services und Audit. |
| Design-Konsistenz | 8/10 | Nah am Single-/Nexus-System. |
| Mobile & Speed | 5/5 | Keine offensichtlichen Strukturprobleme. |

Befunde: Eines der staerksten Assets im Content-System; Schema-Bereinigung waere der naechste technische Hebel.

### Kategorie Hub SEO – /category/seo/
Tier: 2 | Score: 72/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 15/25 | Sauberer Hub-CTA, Audit als Sekundaerpfad sichtbar. |
| Trust & Proof | 10/15 | Featured Entry und Hub-Logik vorhanden. |
| Content & Struktur | 16/20 | Saubere Pillar-/Cluster-Struktur. |
| Meta & Schema | 9/15 | Meta brauchbar, aber ausbaufahig. |
| Interne Links | 8/10 | Gute Ruecklinks zu Pillar und Services. |
| Design-Konsistenz | 9/10 | Hub-Layout stimmig. |
| Mobile & Speed | 5/5 | Keine groben Auffaelligkeiten. |

Befunde: Solider SEO-Hub; die uebergeordnete Hub-Landschaft ist aber unvollstaendig.

### Kategorie Hub Tracking – /category/tracking/
Tier: 2 | Score: 70/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 15/25 | Guter Hub-CTA, Audit bleibt sichtbar. |
| Trust & Proof | 9/15 | Featured-Post/Hub-Utility okay. |
| Content & Struktur | 15/20 | Klarer Tracking-Fokus. |
| Meta & Schema | 8/15 | Description deutlich zu lang. |
| Interne Links | 8/10 | Gute Service- und Audit-Anbindung. |
| Design-Konsistenz | 10/10 | Konsistentes Hub-Pattern. |
| Mobile & Speed | 5/5 | Keine strukturellen Brueche. |

Befunde: Sauberer Hub; Meta-Kuerzung und vollstaendige Hub-Struktur fehlen noch.

### Kategorie Hub Owned Leads – /category/owned-leads/
Tier: 2 | Score: 69/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 16/25 | Audit sichtbar, Hub-Entry stimmig. |
| Trust & Proof | 8/15 | Solider Thought-Leadership-Charakter. |
| Content & Struktur | 15/20 | Funktioniert als Strategie-nahe Kategorie, aber nicht als expliziter Strategie-Hub. |
| Meta & Schema | 7/15 | Description zu lang. |
| Interne Links | 8/10 | Gute Rueckwege in Audit und Posts. |
| Design-Konsistenz | 10/10 | Konsistentes Hub-Pattern. |
| Mobile & Speed | 5/5 | Unauffaellig. |

Befunde: Inhaltlich brauchbarer Strategie-Ersatz, aber die erwartete Strategie-Hub-URL bleibt unklar.

### Blog Top-Artikel – /b2b-landingpage-optimieren/
Tier: 2 | Score: 74/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 16/25 | Audit CTA spaeter stark eingebunden. |
| Trust & Proof | 10/15 | Gute Framework-/Praxisorientierung. |
| Content & Struktur | 18/20 | Starker, kaufnaher Content. |
| Meta & Schema | 8/15 | Description zu lang. |
| Interne Links | 8/10 | Gute Anbindung an Audit und verwandte Inhalte. |
| Design-Konsistenz | 9/10 | Single-/Related-Pattern konsistent. |
| Mobile & Speed | 5/5 | Keine groben Probleme sichtbar. |

Befunde: Sehr brauchbarer Funnel-Einstieg; Meta-Kuerzung ist der naechste Hygiene-Schritt.

### Blog Top-Artikel – /owned-leads-statt-ad-miete/
Tier: 2 | Score: 65/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 14/25 | Audit ist vorhanden, aber nicht dominant. |
| Trust & Proof | 9/15 | Gute Entscheider-Story. |
| Content & Struktur | 17/20 | Starkes Thema fuer ToFu/MoFu. |
| Meta & Schema | 6/15 | Duplicate Description; im Crawl tauchen Links zum 404-Ziel `/wordpress-tech-audit/` auf. |
| Interne Links | 8/10 | Gute Content-Verzahnung. |
| Design-Konsistenz | 7/10 | Solide, aber weniger stark als Cornerstone. |
| Mobile & Speed | 4/5 | Keine tiefe Messung. |

Befunde: Starker Inhalt, aber schwache Meta-Hygiene und problematische CTA-/Linkziele im Live-Content.

### Blog Top-Artikel – /meta-ads-fuer-b2b/
Tier: 2 | Score: 62/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 13/25 | CTA-Pfad ist weicher und weniger auf Audit ausgerichtet. |
| Trust & Proof | 8/15 | Gute Problemstellung, aber weniger Proof. |
| Content & Struktur | 16/20 | Solider ToFu/MoFu-Artikel. |
| Meta & Schema | 6/15 | Duplicate Description mit `/owned-leads-statt-ad-miete/`. |
| Interne Links | 7/10 | Vernetzung okay. |
| Design-Konsistenz | 7/10 | Unauffaellig. |
| Mobile & Speed | 5/5 | Keine groben Auffaelligkeiten. |

Befunde: Solider Content-Entry, aber Meta-Uniqueness fehlt.

### Blog Top-Artikel – /core-web-vitals-wachstum-seo-und-roas/
Tier: 2 | Score: 64/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 14/25 | Content-first, Audit erst spaeter. |
| Trust & Proof | 8/15 | Solide Argumentation. |
| Content & Struktur | 17/20 | Thematisch stark. |
| Meta & Schema | 6/15 | Title und Description zu lang. |
| Interne Links | 8/10 | Gute Rueckwege. |
| Design-Konsistenz | 6/10 | Standard-Single okay. |
| Mobile & Speed | 5/5 | Keine offensichtlichen Fehler. |

Befunde: Inhaltlich passend, technisch vor allem ein Meta-Hygiene-Thema.

### Blog Top-Artikel – /server-side-tracking-gtm/
Tier: 2 | Score: 75/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 16/25 | Solider Audit-Pfad ueber Content und Footer. |
| Trust & Proof | 10/15 | Sehr fachlich und glaubwuerdig. |
| Content & Struktur | 18/20 | Tief und sauber aufgebaut. |
| Meta & Schema | 8/15 | Keine schwere Meta-Luecke. |
| Interne Links | 9/10 | Gute Tracking-/Service-/Audit-Verzahnung. |
| Design-Konsistenz | 9/10 | Saubere Single-Umsetzung. |
| Mobile & Speed | 5/5 | Keine sichtbaren Strukturprobleme. |

Befunde: Einer der staerkeren Tracking-Einstiege; eher Fine-Tuning als strukturelles Problem.

### Kontakt – /kontaktiere-mich/
Tier: 2 | Score: 34/100

| Kategorie | Score | Issues |
|---|---|---|
| CTA & Above-Fold | 6/25 | Keine klare Primaerhandlung ausser indirektem Call-Pfad. |
| Trust & Proof | 6/15 | Kaum eigenstaendige Vertrauenselemente. |
| Content & Struktur | 8/20 | Zwei H1, wenig Nutzwert, kein Formular. |
| Meta & Schema | 4/15 | Title/Description generisch. |
| Interne Links | 1/10 | Die kanonische URL hat 0 eingehende Links; sitewide verlinkt der Footer auf `/kontakt/`. |
| Design-Konsistenz | 6/10 | Visuell okay, aber wieder mit Blog-Header-Kontext. |
| Mobile & Speed | 3/5 | Kein tiefer technischer Fehler sichtbar. |

Befunde: Besteht als Seite, erfuellt aber die Funktion eines direkten Kontaktwegs nicht ueberzeugend.

## Cross-Page Consistency Report

### Navigation
| Check | Ergebnis | Befund |
|---|---|---|
| Hauptnavigation identisch | Teilweise | Globaler `nx-site-header` ist konsistent, aber mehrere Default-Seiten rendern zusaetzlich einen `nexus-blog-header`. |
| Logo verlinkt korrekt zur Startseite | Ja | Kein Bruch gefunden. |
| Mobile Menue entspricht Desktop | Weitgehend | Gleiches Link-Set, aber alte Case-Studies-URL war in beiden Ebenen vorhanden. |
| Aktive States korrekt | Teilweise | Blog-/Case-State lief auf `/case-studies/`, nicht auf die echte Overview. |

### Footer
| Check | Ergebnis | Befund |
|---|---|---|
| Einheitlicher Footer | Ja | Footer-Struktur ist sitewide stabil. |
| Impressum/Datenschutz erreichbar | Ja | Beide Links funktionieren. |
| Footer-CTA konsistent | Ja | `Growth Audit starten` ist durchgaengig der Footer-CTA. |
| Kontakt-Link kanonisch | Nein -> Theme-Fix umgesetzt | Footer verlinkte `/kontakt/` statt `/kontaktiere-mich/`. |

### CTA-Matrix
| Seite | Primaer CTA | Sekundaer CTA | CTA-URL | CTA-Text | Konsistent? |
|---|---|---|---|---|---|
| Startseite | Growth Audit | Case Studies / About | `/growth-audit/` | `Audit starten` | Ja |
| Growth Audit | Audit-Formular | Case Studies / WGOS | `#form` | `Growth Audit starten` | Ja |
| WGOS | Growth Audit | Case Studies | `/growth-audit/` | `Mit dem Audit starten` | Ja |
| WordPress Agentur | Growth Audit | Case Studies / WGOS | `/growth-audit/` | `Audit starten` | Ja |
| SEO Service | 404-Ziel / Call | Related Content | `/wordpress-tech-audit/` | `Kostenloses SEO-Audit starten` | Nein |
| CRO Service | Cal.com / Mail | Footer Audit | `https://cal.com/...` | `Kostenlose Potenzialanalyse anfordern` | Nein |
| GA4 Service | Cal.com | Footer Audit | `https://cal.com/...` | `Kostenlosen Tracking-Audit anfordern` | Nein |
| Core Web Vitals | Cal.com / Seitenanker | Footer Audit | `https://cal.com/...` | `Kostenlose Performance-Analyse` | Nein |
| About | Growth Audit | Case Studies | `/growth-audit/` | `Growth Audit starten` | Ja |
| Case Studies Overview | Cal.com | Footer Audit | `https://cal.com/...` | `Strategie-Gespraech buchen` | Nein |
| Cornerstone | Service + Audit | Audit | `/wordpress-seo-hannover/` + `/growth-audit/` | `Technisches SEO Audit anfragen` | Teilweise |
| Kategorie-Hubs | Fach-CTA + Audit | Audit | Service-URL + `/growth-audit/` | varies | Teilweise |
| Kontakt | Indirekter Call | Footer Audit | `https://cal.com/...` | `Strategiegespraech` | Nein |

### Design
| Check | Ergebnis | Befund |
|---|---|---|
| Button-Styles konsistent | Nein | Template-Seiten nutzen Nexus/NX-Buttons; Editor-/Service-Seiten nutzen `btn`, `cro-btn`, `ga4-btn` und teils Theme-Buttons parallel. |
| Farben/Typo konsistent | Teilweise | Harte Template-Seiten sind konsistent; Default-Template-Seiten weichen durch Zusatzheader und Editor-Styles ab. |
| Maximal zwei Button-Typen | Nein | Live sind mehr als zwei CTA-Stile aktiv. |

### Terminologie
| Check | Ergebnis | Befund |
|---|---|---|
| `Growth Audit` einheitlich | Teilweise | Weitgehend sauber, aber SEO-/GA4-/CRO-Seiten nutzen eigene Audit-/Analyse-Begriffe oder direkte Call-CTAs. |
| `WGOS` / `WordPress Growth Operating System` | Ja | Auf den geprueften Templates konsistent. |
| `Owned-First` / Kernsprache | Teilweise | `Owned Leads` ist konsistent, aber es gibt keinen expliziten Strategie-Hub-Namen. |

### Links
| Check | Ergebnis | Befund |
|---|---|---|
| Broken internal links | Nein / Ja | Theme-seitig wurde der WGOS-404-Link gefixt; live bestehen weiter 7 Links auf `/wordpress-tech-audit/` (404). |
| Redirecting internal links | Ja | 143 Links auf `/case-studies/`, 34 Links auf `/kontakt/`. |
| Deprecated URLs | Ja | `/audit/`, `/customer-journey-audit/`, `/360-audit/` leiten korrekt auf `/growth-audit/` um. |
| Orphans | Ja | `/case-studies-e-commerce/`, `/kontaktiere-mich/`, `/alle-loesungen/`, `/kostenlose-tools/`, `/danke-anfage-audit/` hatten im Crawl 0 eingehende interne Links. |

### Tracking
| Check | Ergebnis | Befund |
|---|---|---|
| Primaere CTAs mit Tracking-Attributen | Teilweise | Template-CTAs oft sauber, Editor-CTAs haeufig ohne `data-track-*`. |
| Footer/Utility-Links trackbar | Teilweise | Footer-CTA ja, Footer-Navigation nein. |
| 404-/Recovery-Links trackbar | Ja | 404-Template setzt `data-track-action`. |

## Fix-Priorisierung

### 🚀 Quick Wins (High Impact, Low Effort)
- Bereits im Theme korrigiert: kanonische Case-Studies-URL in Header, Footer, About, Homepage, WGOS, Audit-Shell, Agentur, Case Study und statischem WGOS-Proof-Asset.
- Bereits im Theme korrigiert: Footer-Kontakt auf `/kontaktiere-mich/` statt Redirect-URL `/kontakt/`.
- Bereits im Theme korrigiert: WGOS-Link von `/core-web-vitals-optimierung/` auf `/core-web-vitals/` sowie generische `Mehr erfahren`-Anchors auf beschreibende Linktexte.

### 📈 Strategisch (High Impact, High Effort)
- Live-SEO- und Core-Web-Vitals-Seite auf die vorgesehenen Repo-Templates umziehen oder den Default-Template-/Editor-Stack als Source of Truth dokumentieren.
- CTA-Hierarchie auf SEO, CRO, GA4, CWV und Case-Overview wieder auf Audit-first ausrichten.
- Hub-Landschaft fuer Strategie, CRO und Performance komplettieren und klar intern verlinken.

### 🔧 Maintenance (Low Impact, Low Effort)
- Ueberlange/duplicate Meta Descriptions kuerzen und vereinheitlichen.
- E3-, CRO- und Blog-Metas nachschaerfen.
- Schema-Doppelungen auf dem Cornerstone und fehlendes Service-Schema fuer SEO bereinigen.

### ⏭️ Skip (Low Impact, High Effort)
- `page-wgos.php` grundlegend refactoren; laut `docs/architecture/LIVE_STATUS.md` ist das weiterhin "in Arbeit".
- Externe GTM/GA4/Consent-/SMTP-Konfigurationen aendern; ausserhalb des versionierten Repo-Layers.
