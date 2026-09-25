# Brand & Copy Standards

Single source of truth for positioning, tone, and copy direction.
Skills reference this file instead of duplicating brand rules.

## Fakten stehen nie als Text

**Kontaktdaten, Antwortzeit, Preise und Kennzahlen werden nirgends
ausgeschrieben. Sie werden aus dem Kanon gelesen.**

Der Kanon liegt in `blocksy-child/inc/canon/`: `messaging-canon.php` (Kontakt,
Antwortzeit, Wertanker), `pricing-canon.php` (Preise), `e3-proof-canon.php`
(Case-Kennzahlen), `diagnose-canon.php` (Diagnose-Stufen), `market-canon.php`
(fremde Marktzahlen mit Quelle), `reference-canon.php` (Referenzen).

Wo der Wert herkommt, hängt davon ab, wo er hin soll:

| Oberfläche | Zugriff |
|---|---|
| PHP-Template, Partial, `inc/*.php` | Getter aufrufen: `hu_get_contact_email()`, `hu_response_promise()`, `hu_e3_metric()`, `hu_foundation_price_display()` … |
| Gutenberg, ACF, Widget, Menü | Shortcode: `[nx_email]`, `[nx_antwortzeit]`, `[hu_price]`, `[hu_message]`, `[hu_markt]` (fremde Marktzahl, Quelle sichtbar daneben) |
| JavaScript | über `wp_localize_script()` aus PHP durchreichen, nie im Skript setzen |
| JSON-LD / Schema | denselben Getter wie die sichtbare Copy |
| E-Mail-Templates | denselben Getter wie die sichtbare Copy |

Auch der Fallback zählt. `function_exists( 'x' ) ? x() : 'der Wert als Text'`
ist eine zweite Fassung mit Verfallsdatum — der Kanon wird von `functions.php`
unbedingt geladen, der Getter darf direkt aufgerufen werden.

Warum: jede ausgeschriebene Zahl ist eine Kopie, die beim nächsten Wechsel
stehen bleibt. Genau so standen zeitweise drei Kontaktadressen und drei
Antwortzeiten gleichzeitig auf der Website — die schwächste davon im Formular,
also genau dort, wo abgeschickt wird.

Erzwungen wird die Regel von `scripts/canon-guard.sh` gegen die Sperrliste in
`scripts/canon-forbidden-values.txt`. Der Guard läuft in der CI und im
Theme-Build, also vor jedem Deploy, und kennt als Ausnahme nur den Kanon selbst.
Ändert sich ein Wert, wird er im Kanon geändert und die abgelöste Fassung in die
Sperrliste aufgenommen — nicht andersherum.

## Identity

- Entity / Marke: **Haşim Üner**, hasimuener.de
- Fachliche Klammer: **WordPress · Tracking · Conversion**
- Öffentliche Rolle: WordPress Freelancer für Unternehmen; für Agenturen zusätzlich White-Label-Partner
- Kernkompetenzen: WordPress-Entwicklung, technisches SEO, Tracking/Attribution, Server-Side Tracking, Landingpages/Funnel, Conversion-Optimierung
- Performance-Marketing ist eine vorhandene Kompetenz und kann als Leistung sichtbar sein, ist aber **nicht** der globale Rollen-Claim
- Solar, Wärmepumpe und Speicher bleiben eine **spezialisierte Vertikale mit eigenem Funnel und starkem Proof**, nicht mehr die einzige globale Positionierung
- Nicht: reine Webdesign-Agentur, reine Performance-Marketing-Agentur, reine Beratung, allgemeiner Full-Service-Bauchladen

## Positioning

**Haşim Üner verbindet WordPress-Entwicklung, Tracking und Conversion so, dass Websites, Landingpages und Anfragesysteme technisch zusammenpassen und messbar werden.**

Die Website hat drei Geschäftspfade. Die Startseite gehört den direkten WordPress-Projekten. White-Label (Platz 2 in der Navigation) und Solar/Wärmepumpe sind auf der Startseite seit 2026-09-24 leise Nebenausgänge: eine kleine Zeile unter den Leistungen, keine Weiche im Hero. Ihre eigenen Seiten und Anfragewege bleiben unverändert:

1. **Direkte Unternehmen / WordPress-Projekte** → `/` bzw. generische Projektanfrage
2. **Agenturen** → `/whitelabel-retainer/` bzw. Aufgabe beschreiben / Erstprojekt
3. **Solar / Wärmepumpe / Speicher** → `/solar-waermepumpen-leadgenerierung/` bzw. Marktcheck

Die Startseite ist zugleich Marke und direkte Freelancer-Money-Page. Sie übernimmt die frühere Freelancer-Seite vollständig; deren URL leitet dauerhaft auf `/` weiter. White-Label und Solar/Wärmepumpe behalten ihre eigenen Anfragewege. Entscheidung: `docs/decisions/homepage-freelancer-konsolidierung.md`.

## Commercial Architecture

### Globaler Einstieg

- `/` = WordPress-Freelancer-Positionierung + direkte Leistungen, Proof und Projektanfrage
- Globale sichtbare Kompetenz: WordPress, technisches SEO, Tracking, Conversion
- Globaler generischer CTA außerhalb der Spezialfunnel: **Projekt anfragen**

### Direkte Zusammenarbeit

- `/` = Money Page für `wordpress freelancer hannover`, `wordpress freelancer` und unterstützend `wordpress experte hannover`
- Zielgruppe: direkte Auftraggeber, die mit der ausführenden Person arbeiten wollen
- Differenzierung: Entwicklung + Tracking/Attribution + Server-Side Tracking + Funnel/CRO + technisches SEO + Performance/Accessibility
- Primärer nächster Schritt: Projektanfrage / Scope klären

### White-Label für Agenturen

- `/whitelabel-retainer/` = eigener Agentur-Einstieg
- Zielgruppe: Performance-, Web-, SEO- und Full-Service-Agenturen mit Umsetzungsbedarf
- Lieferfelder: WordPress, Tracking, CRO, technische SEO, Landingpages/Funnel
- Primärer nächster Schritt: Aufgabe beschreiben (Formular der Route) oder Erstprojekt mit klarem Scope; der 30-Minuten-Termin ist ein leiser Zweitweg (Hero und neben dem Formular)
- Unterschied im ersten Bildschirm: WordPress-Seite und Messung aus einer Person, deshalb keine Schnittstelle zwischen zwei Dienstleistern; ob der Kunde der Agentur mich sieht, entscheidet die Agentur (H1: „Ob euer Kunde mich sieht, entscheidet ihr.“)
- Keine erfundenen Agentur-Referenzen; keine Akquise im Kundenstamm der Partner
- Auf der Seite nie: Firmenname oder Kennzahlen des PV-Falls, Zitate, Logos, Projektzahlen, Verfügbarkeitsangaben, Stundensatz (Sperrliste `wl-*`)
- Margenblock nur für Leistungen mit veröffentlichtem Endkundenpreis für dieselbe Leistung im Kanon (heute das Server-Side-Setup)

### Solar / Wärmepumpe / Speicher

- `/solar-waermepumpen-leadgenerierung/` = spezialisierte Branchen-Money-Page
- Der Marktcheck bleibt **ausschließlich der primäre Einstieg für den Energie-Cluster**
- Solar-/SHK-Unterseiten, Portalvergleiche und Lead-Kauf-Intent dürfen weiterhin auf den Marktcheck führen
- E3 bleibt der wichtigste fachliche Proof für Nachfrageaufbau, Tracking, Vorqualifizierung und Conversion

## CTA- und Routing-Regeln

**Suchintention, Seitenrolle und Conversion-Ziel sind getrennte Entscheidungen.** Eine Seite darf ihre Query besitzen und trotzdem auf einen anderen nächsten Schritt führen.

- Energie-/Portal-/PV-/Wärmepumpen-Intent → Marktcheck
- Agentur-/White-Label-/Partner-Intent → White-Label-Formular „Aufgabe beschreiben“
- Direkter WordPress-, Tracking-, CRO-, technischer SEO- oder Landingpage-Intent → Projektanfrage; eine Fachseite bleibt dabei selbst Query-Owner
- Startseite → direkte Projektanfrage; sichtbare Brücken für Agenturen und Solar/Wärmepumpe
- Eine rankende Fachseite **nicht** auf eine andere Money Page umleiten, nur weil deren CTA besser passt
- `/server-side-tracking-b2b/` bleibt z. B. Query-Owner für Server-Side-Tracking-Intent; der CTA muss deshalb nicht zum Solar-Marktcheck führen
- `/wordpress-agentur-hannover/` bleibt Query-Owner für den lokalen Agentur-Intent, ist aber **keine globale Rollenbeschreibung** und braucht keinen Header-Slot

Details und konkrete Zuordnung: `docs/architecture/CONVERSION_ROUTING.md`.

## Zusagen mit Zeitangabe

Eine Antwortzeit, sitewide: **innerhalb von 24 Stunden werktags**. Sie steht im
Canon und gehört nie als Literal in Template, FAQ, Meta-Description, JSON-LD,
Formular oder E-Mail.

| Zusage | Quelle | Gilt für |
|---|---|---|
| Antwort auf eine Anfrage | `hu_response_promise()` in `inc/canon/messaging-canon.php` | Jede Anfragestrecke: Startseite, White-Label, Kontakt, Fachseiten |
| Marktcheck-Befund | `hu_marketcheck_reply_label()` in `inc/canon/diagnose-canon.php` | Nur Marktcheck und Analyse-Intake im Energy-Funnel |
| Editor-Inhalt (Gutenberg, ACF, Widgets) | Shortcode `[nx_antwortzeit]` | Alles, was nicht im Repo liegt |

Der Marktcheck-Befund ist ein eigener **Vorgang** — ein händisch geschriebener
Befund, keine Antwort auf eine E-Mail. Er hat deshalb einen eigenen Getter, seit
2026-09-18 aber keinen eigenen Wert mehr: `hu_marketcheck_reply_label()` liest
`hu_response_promise_short()`. Wo beides nebeneinander steht, muss die
Marktcheck-Zusage das Wort „Befund" tragen, sonst liest sie sich als zweite
Antwortzeit.

Warum gezählte **Arbeitsstunden** und nicht Kalenderstunden: der Einwand gegen
eine Stundenangabe war immer, dass sie über ein Wochenende nicht einlösbar ist.
Das trägt der Zusatz „werktags". `nexus_compute_intake_response_deadline()` in
`inc/review-crm.php` rechnet nach derselben Regel und liest dieselbe Konstante
(`HU_RESPONSE_HOURS`), damit der berechnete Termin in der Bestätigungsmail und
die sichtbare Zusage nicht auseinanderlaufen können.

Die abgelösten Fassungen stehen in `scripts/canon-forbidden-values.txt` und
werden von `scripts/canon-guard.sh` repo-weit geblockt — in CI und im
Theme-Build, also vor jedem Deploy. Sie hier noch einmal aufzuzählen wäre eine
zweite Liste.

Die Support-Frist der Tracking Care (`HU_TRACKING_RESPONSE_BUSINESS_DAYS`) ist
eine dritte, vertragliche Größe im laufenden Mandat. Sie gehört nicht in
Intake-Bestätigungen.

## Sichtbare Kontaktwege

Eine Adresse, eine Nummer, beide aus dem Canon. Zwei aktive Adressen sitewide
sind eine Frage zu viel für den Empfänger.

| Weg | Wert | Quelle |
|---|---|---|
| E-Mail | `kontakt@hasimuener.de` | `hu_get_contact_email()` in `inc/canon/messaging-canon.php` |
| E-Mail-Link | `mailto:` auf dieselbe Adresse | `hu_get_contact_mailto()` ebenda |
| E-Mail im Editor-Inhalt | — | Shortcode `[nx_email]` |
| Telefon | `0176 76596580` / `tel:+4917676596580` | `hu_get_contact_phone()` ebenda |

Die früheren Alias-Adressen nehmen weiter Post an, werden aber in keiner
sichtbaren Copy, keinem Schema und keinem Formularhinweis mehr ausgegeben.
Welche das sind, steht in `scripts/canon-forbidden-values.txt` und wird von
`scripts/canon-guard.sh` erzwungen — das ist die einzige Liste davon.

**Ohne Ausnahme, seit 2026-09-18:** Impressum und Datenschutzerklärung lesen
denselben Canon. Der rechtlich benannte Kontaktweg ist derselbe wie der
beworbene; ihn getrennt zu pflegen hat nur die Chance erhöht, dass eine der
beiden Seiten stehen bleibt. Der Absender der Transaktionsmails kommt weiterhin
aus der Laufzeitkonfiguration (`inc/mail.php`), nicht aus dem Canon — wo Copy
den Absender benennt, muss beides zusammenpassen.

## Tone

- Klar, direkt, technisch verständlich, entscheidungssicher
- Keine aufgeblasene Agentur-Sprache
- Konkrete Wirkung vor Feature-Listen
- Technik und Marketing zusammen erklären, nicht als künstlich getrennte Disziplinen
- Proof vor Behauptung
- Kein Rollen-Claim, der mehr verspricht als Website und Leistungen tatsächlich abbilden

## Preferred Terms

Global bevorzugt:

`WordPress`, `Tracking`, `Conversion`, `Conversion-Optimierung`, `technisches SEO`,
`Server-Side Tracking`, `Landingpage`, `Funnel`, `Projekt`, `direkte Zusammenarbeit`,
`White-Label`, `messbare Anfragen`, `Tracking & Attribution`, `Performance`, `Core Web Vitals`

Im Energie-Cluster zusätzlich:

`Anfragesystem`, `eigene Anfragen`, `Portal-Abhängigkeit`, `Leadkosten`,
`Kosten pro Anfrage`, `qualifizierte Anfragen`, `Abschlussquote`,
`Vorqualifizierung`, `Marktcheck`, `Solar`, `Photovoltaik`, `PV`,
`Wärmepumpe`, `Speicher`, `Energie-Anbieter`, `Handwerk`

`Founding Cohort 2026`, `Founding-Partner` und `Founding-Konditionen` sind
zurückgezogen und dürfen nicht mehr in Kundencopy auftauchen — ebenso wenig
Platzzähler oder Bewerbungsfristen. Wer den Betrieb hinter einer Umsetzung
benennen muss, schreibt `Umsetzungspartner` und erklärt ihn bei der ersten
sichtbaren Nutzung: kein Mitgründer, kein Anteilseigner, keine
gesellschaftsrechtliche Partnerschaft.

## Solar / Photovoltaik / PV

Die drei Begriffe sind keine Synonyme und haben feste Rollen innerhalb des Energie-Clusters.

| Begriff | Rolle | Wo |
| --- | --- | --- |
| `Solar` | Zielgruppen-/Cluster-Rahmen | Solar-Navigation, Branchen-Landingpage, Footer, Clustertexte |
| `Photovoltaik` | Produkt- und Suchbegriff | H1, Fließtext, Meta-Titles, Anchor-Texte |
| `PV` | Branchenkürzel | Komposita wie `PV-Anfragen`, `PV-Projekte`, `PV-Termine`, `Gewerbe-PV` |

Regeln:

- Nicht `Solar` schreiben, wenn konkret die PV-Anlage gemeint ist; Solarthermie ist ein anderes Gewerk und kein Angebotsschwerpunkt.
- `Wärmepumpe` bleibt gleichrangig neben `Photovoltaik`.
- `/waermepumpen-leads/` besitzt weiterhin die Query `wärmepumpen leads` und wird nicht durch PV-lastige Copy verwässert.
- Bestehende Query-Ownership aus `docs/seo/query-ownership.csv` bleibt bestehen, solange neue GSC-Daten keine andere Entscheidung belegen.

## Anti-Patterns (Hard Bans)

- `Architekt für eigene Anfragesysteme` als globaler Rollen- oder Entity-Claim
- Solar/Wärmepumpe als angeblich einzige Zielgruppe auf globalen Seiten
- Marktcheck als globaler CTA auf fachfremden WordPress-/Tracking-/CRO-Seiten
- `Growth Audit` als user-facing Label
- `WGOS` und `WordPress Growth Operating System` als öffentliches Angebot
- `KI-Integration` als eigenständiges Angebot
- `Growth Architect` und generische Growth-Blasen-Begriffe
- `Performance-Marketing-Agentur` als Unternehmensidentität
- `Shopify` als Live-Positionierung
- `kostenlos` als alleiniges Wertversprechen
- erfundene Rankings, Referenzen, Umsatzversprechen oder Rich-Result-Versprechen
- SEO-Query-Owner löschen oder umleiten, nur um Conversion-Pfade zu vereinfachen
- gleicher CTA auf jeder Seite unabhängig von Suchintent und Zielgruppe
- Service-Kataloge, die Fähigkeiten nur stapeln, ohne den konkreten Projektkontext zu erklären

## Route: WordPress Agentur Hannover

`/wordpress-agentur-hannover/` bleibt eine bewusste lokale SEO-Route für den **Agentur-Intent** `wordpress agentur hannover`.

- Sie ist **kein globaler Rollen-Claim** und kein primärer Navigationspunkt.
- Der getrennte Freelancer-Intent gehört auf `/`.
- Der Begriff `WordPress Agentur Hannover` darf im SEO-Title/H1 dieser Route stehen, weil er die Suchintention besitzt.
- Die Seite darf direkte Zusammenarbeit als Alternative sichtbar zur Freelancer-Route weiterführen.
- Benachbarte Kategoriebegriffe nicht wahllos ergänzen. Der Versuch mit `Webdesign-Agentur`, `Internetagentur` und `Webagentur` verschlechterte 2026 die Money-Query ohne belegten Zusatznutzen.
- Regressionen auf dieser Route prüft `agents/skills/seo-drift/scripts/drift-report.sh`.

## Route: WordPress Freelancer Hannover

`/` ist der zentrale direkte WordPress-Einstieg.

- Primärer Query-Owner: `wordpress freelancer hannover`
- Sekundär: `wordpress freelancer`; unterstützend `wordpress experte hannover`
- Targetet nicht `wordpress agentur hannover`
- Targetet keine White-Label-Queries
- SEO-Title seit 2026-09-24: `WordPress Freelancer für Unternehmen | Haşim Üner, Hannover`. Die Rolle steht vorn, der Ort hinten; Hannover und Pattensen stehen zusätzlich in der Metazeile und im Schema
- Die H1 benennt das Ergebnis oder das Problem des Lesers (mehr Anfragen, deren Herkunft sichtbar ist), nicht die Leistung, und trägt keinen Ortsnamen
- `WordPress Freelancer` darf auf dieser Route in SEO-Title, Metazeile und Rollenbeschreibung stehen
- GitHub, versionierter Code, Staging, Review und kontrollierte Deployments dürfen als Workflow-/Qualitätsbeleg sichtbar erklärt werden
- Lighthouse-Werte nur als Labtest bezeichnen; kein Ersatz für CrUX-/Felddaten
- Öffentliche Referenzen müssen direkt prüfbar sein

## Route: Server-Side Tracking

`/server-side-tracking-b2b/` bleibt die fachliche Money Page für nicht ortsqualifizierte Tracking-Queries.

- Query-Ownership bleibt bei der Fachseite
- Primärer CTA: direkte Projektanfrage / Tracking-Scope klären
- Sekundärer Agentur-Hinweis ist erlaubt, wenn der Besucher erkennbar White-Label-Kapazität sucht
- Kein automatisches Routing in den Solar-Marktcheck

## Route: White-Label

`/whitelabel-retainer/` ist ein eigenständiger Agentur-Einstieg und Teil der **globalen kommerziellen Architektur**.

- sichtbar in der Hauptnavigation als `Für Agenturen`
- Wegweiser auf der Startseite unter den Leistungen (seit 2026-09-25):
  Label `Für Agenturen und Webdesigner`, Link `Technik und Tracking für Ihre
  Kunden →`
- Rolle dort: White-Label-Partner / Umsetzung im Hintergrund
- WordPress, SEO, Tracking und CRO sind Lieferfelder
- Erstprojekt mit Scope und Preis vor Start; danach optional Retainer
- Schreibweise sichtbar immer `White-Label`

## Brand Colors (Project Override)

- Primary brand accent: `#b46a3c` (copper)
- HSL reference: `23 50% 47%`
- Red accent in design system maps to this copper tone for this project

## Hybrid Model

- Structure, templates, helpers, CSS, JS, schema live in the repo
- Homepage and service-page copy can live in the WordPress editor
- Always separate changes into: `Copy`, `Structure`, `Template`, `Refactor`, `Manual WP`
