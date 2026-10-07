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
- Öffentliche Rolle: **WordPress-Entwickler** für Unternehmen; für Agenturen zusätzlich White-Label-Partner. Die Startseite bleibt aus SEO-Gründen Query-Owner für `wordpress freelancer` / `wordpress freelancer hannover`, ohne „Freelancer“ zur globalen Entity-Rolle zu machen.
- Kernkompetenzen: WordPress-Entwicklung, technisches SEO, Tracking/Attribution, Server-Side Tracking, Landingpages/Funnel, Conversion-Optimierung
- Performance-Marketing ist eine vorhandene Kompetenz und kann als Leistung sichtbar sein, ist aber **nicht** der globale Rollen-Claim
- Solar, Wärmepumpe und Speicher bleiben eine **spezialisierte Vertikale mit eigenem Funnel und starkem Proof**, nicht mehr die einzige globale Positionierung
- Nicht: reine Webdesign-Agentur, reine Performance-Marketing-Agentur, reine Beratung, allgemeiner Full-Service-Bauchladen

## Positioning

**Haşim Üner verbindet WordPress-Entwicklung, Tracking und Conversion so, dass Websites, Landingpages und Anfragesysteme technisch zusammenpassen und messbar werden.**

Die Website hat drei Geschäftspfade. Die Startseite gehört den direkten WordPress-Projekten. White-Label (Platz 3 in der Navigation, direkt hinter Leistungen und Tracking) und Solar/Wärmepumpe sind auf der Startseite seit 2026-09-24 leise Nebenausgänge: eine kleine Zeile unter den Leistungen, keine Weiche im Hero. Ihre eigenen Seiten und Anfragewege bleiben unverändert:

1. **Direkte Unternehmen / WordPress-Projekte** → `/` bzw. generische Projektanfrage
2. **Agenturen** → `/whitelabel-retainer/` bzw. Aufgabe beschreiben / Erstprojekt
3. **Solar / Wärmepumpe / Speicher** → `/solar-waermepumpen-leadgenerierung/` bzw. Marktcheck

Die Startseite ist zugleich Marke und direkte Freelancer-Money-Page. Sie übernimmt die frühere Freelancer-Seite vollständig; deren URL leitet dauerhaft auf `/` weiter. White-Label und Solar/Wärmepumpe behalten ihre eigenen Anfragewege. Entscheidung: `docs/decisions/homepage-freelancer-konsolidierung.md`.

## Commercial Architecture

### Globaler Einstieg

- `/` = zentraler direkter WordPress-Einstieg + Leistungen, Proof und Projektanfrage; SEO-Owner für den Freelancer-Intent
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
- Lieferfelder: WordPress (Landingpages, Ladezeit, technische SEO), Tracking, Anfragestrecken bis ins CRM, Barrierefreiheit (WCAG 2.1 AA); jede Leistung beginnt mit dem Anlass aus Sicht der Agentur
- Primärer nächster Schritt: Aufgabe beschreiben (Formular der Route) oder Erstprojekt mit klarem Scope; Zweitweg im Hero ist die Einschätzung vor der Zusage (`?case=angebotsphase`); Vormerken (`?case=vormerken`) und der 30-Minuten-Termin stehen nur neben dem Formular
- Unterschied im ersten Bildschirm: WordPress-Seite und Messung aus einer Person, Festpreis und Termin stehen vor der Zusage der Agentur an ihren Kunden (H1: „Ich baue die WordPress-Seite und die Messung dazu. Festpreis und Termin stehen, bevor ihr zusagt.“). Unsichtbarkeit ist kein Versprechen der H1: Ob die Agentur mich im Hintergrund hält oder mit an den Tisch nimmt, entscheidet sie; das steht im Ablauf
- Keine erfundenen Agentur-Referenzen; keine Akquise im Kundenstamm der Partner
- Auf der Seite nie: Firmenname oder Kennzahlen des PV-Falls, Zitate, Logos, Projektzahlen, Verfügbarkeitsangaben, Stundensatz (Sperrliste `wl-*`)
- Margenblock nur für Leistungen mit veröffentlichtem Endkundenpreis für dieselbe Leistung im Kanon (heute das Server-Side-Setup)

### Solar / Wärmepumpe / Speicher

- `/solar-waermepumpen-leadgenerierung/` = spezialisierte Branchen-Money-Page
- Der Marktcheck bleibt **ausschließlich der primäre Einstieg für den Energie-Cluster**
- Solar-/SHK-Unterseiten, Portalvergleiche und Lead-Kauf-Intent dürfen weiterhin auf den Marktcheck führen
- E3 bleibt der wichtigste fachliche Proof für Nachfrageaufbau, Tracking, Vorqualifizierung und Conversion

### Namentliche E3-Referenz auf der Anfrage-Website

- Freigegeben am 05.10.2026: E3 New Energy als Unternehmensreferenz für die
  aufgebaute Funnel-Architektur vom Klick bis zur CRM-Übergabe nennen.
- In diesem Projektporträt keine Kennzahlen, Ergebniszusage oder Verlinkung
  zur separaten anonymisierten Fallstudie. Die Beschreibung nennt den eigenen
  Beitrag; der aktuelle Website-Screenshot zeigt den öffentlichen Stand.
- `hu_website_reference_projects()` in `reference-canon.php` besitzt die
  Auswahl und Projektcopy dieser Produktseite. Die anderen Referenzflächen
  behalten ihre bisherige Auswahl. Die größere E3-Strecke ist kein Beleg für
  den Inklusivumfang des Grundprodukts.

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

Die Support-Frist der Tracking Care ist keine eigene Größe mehr: Im laufenden
Care-Vertrag gilt dieselbe Antwortzeit wie für Anfragen, ausgegeben über
`hu_response_promise()`. Die frühere eigene Kanon-Konstante `HU_TRACKING_RESPONSE_BUSINESS_DAYS`
ist entfernt.

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
- `WGOS` und `WordPress Growth Operating System`: vollständig eingestellt, auch intern als Dashboard, Credits- oder Delivery-Modell. Aktive Energie-Preise bleiben eigenständige Angebotsdaten.
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
- Die Seite positioniert direkte Verantwortung als Alternative zur klassischen Agentur und führt anschließend zu den passenden Produkt-Ownern für Website, Tracking oder Conversion weiter.
- Benachbarte Kategoriebegriffe nicht wahllos ergänzen. Der Versuch mit `Webdesign-Agentur`, `Internetagentur` und `Webagentur` verschlechterte 2026 die Money-Query ohne belegten Zusatznutzen.
- Regressionen auf dieser Route prüft `agents/skills/seo-drift/scripts/drift-report.sh`.

## Route: WordPress Freelancer Hannover

`/` ist der zentrale direkte WordPress-Einstieg.

- Primärer Query-Owner: `wordpress freelancer hannover`
- Sekundär: `wordpress freelancer`; unterstützend `wordpress experte hannover`
- Targetet nicht `wordpress agentur hannover`
- Targetet keine White-Label-Queries
- SEO-Title der finalen Fassung (2026-10-02): `WordPress Freelancer: Website, Tracking, Anfragen · Haşim Üner`. Hannover und Pattensen stehen in Kicker, Person, FAQ und Schema, nicht in der H1
- Die H1 benennt das Ergebnis oder das Problem des Lesers (mehr Anfragen, deren Herkunft sichtbar ist), nicht die Leistung, und trägt keinen Ortsnamen
- `WordPress Freelancer` darf auf dieser Route in SEO-Title, Metazeile und Rollenbeschreibung stehen
- GitHub, versionierter Code, Staging, Review und kontrollierte Deployments dürfen als Workflow-/Qualitätsbeleg sichtbar erklärt werden
- Lighthouse-Werte nur als Labtest bezeichnen; kein Ersatz für CrUX-/Felddaten
- Öffentliche Referenzen müssen direkt prüfbar sein
- Einstiegsschärfung 04.10.2026: Hero-Hauptaktion „Projekt anfragen“, leiser
  Link „Leistungsumfang & Preise“, Kanonpreis als Klartext und Case-Link darunter.
  Bei aktivem Ersteinschätzungs-Versuch bleibt dieser Weg in Kopf, Fall und
  Abschluss und erhält nach den Angeboten eine deutliche Tafel mit Eingabe,
  Ergebnis/Passungsgrenze und Kanon-Antwortzeit. Keine zusätzliche
  Ersteinschätzungs-Weiche im Website-Produktkonfigurator. Tracking/CRM im
  Hero-Absatz ausdrücklich bedarfsabhängige, separat kalkulierte Bausteine.

## Route: Server-Side Tracking

`/server-side-tracking-b2b/` bleibt die fachliche Money Page für nicht ortsqualifizierte Tracking-Queries.

- Query-Ownership bleibt bei der Fachseite
- Ankertext `Server-Side Tracking`; das bloße `Tracking` in Kopf, Fuß und 404 führt auf das Tracking-Angebot `/ga4-tracking-setup/` (seit 2026-09-25)
- Primärer CTA: direkte Projektanfrage / Tracking-Scope klären
- Sekundärer Agentur-Hinweis ist erlaubt, wenn der Besucher erkennbar White-Label-Kapazität sucht
- Kein automatisches Routing in den Solar-Marktcheck

## Route: White-Label

`/whitelabel-retainer/` ist ein eigenständiger Agentur-Einstieg und Teil der **globalen kommerziellen Architektur**.

- sichtbar in der Hauptnavigation als `White-Label` (seit 2026-09-17; „Für Agenturen“ war die Zeile darüber im früheren Vollflächen-Menü)
- Wegweiser der finalen Startseite (2026-10-02) in der Übergabe unter
  `Wann ein anderer Weg besser passt.`: Link `White-Label-Zusammenarbeit →`
- Rolle dort: White-Label-Partner / Umsetzung im Hintergrund
- WordPress, SEO, Tracking, CRO und Barrierefreiheit sind Lieferfelder
- Erstprojekt mit Scope und Preis vor Start; danach optional Retainer
- Schreibweise sichtbar immer `White-Label`
- Finale Strecke: Auftrag → Felder → Belege → Ablauf → Preise → Absicherung → Fragen → Anfrage
- Dunkel sind nur zwei Messinstrumente: der live prüfende Hero und die Margen-Tafel
- Ein Hauptweg im Hero; die Angebotsphase bekommt ihren CTA nach den Preisen
- Margenvergleich nur bei gleichem Umfang; Landingpages werden derzeit nicht verglichen

## Brand Colors (Project Override)

- Primary brand accent: `#b46a3c` (copper)
- HSL reference: `23 50% 47%`
- Red accent in design system maps to this copper tone for this project

## Hybrid Model

- Structure, templates, helpers, CSS, JS, schema live in the repo
- Homepage and service-page copy can live in the WordPress editor
- Always separate changes into: `Copy`, `Structure`, `Template`, `Refactor`, `Manual WP`

## Referenzfall: Verlauf und Vergleichsbasis

Seit Betreiberklärung vom 27.09.2026 ist `hu_e3_canon()['timeline']` die
verbindliche Quelle für Phasen, Kurzfassung und Vergleichsbeschriftung.
Projektmonate schließen die Vorbereitung ein; Kampagnenmonate beginnen erst
danach. Portal-Einkauf ist eine andere Anfragequelle, kein Messpunkt der
eigenen Kampagne. Der erreichte CPL darf nicht als Durchschnitt aller Monate
oder als alleinige Wirkung des Server-Side-Trackings bezeichnet werden.
Details und Grenzen: `docs/decisions/solar-case-timeline-2026-09-27.md`.

### Bezugsgrößen der Solar-Referenz (28.09.2026)

1.750 ist die Gesamtleadzahl über sechs Monate und alle genannten Quellen,
nicht die Anzahl qualifizierter CRM-Leads. Rund 95 % wurden nach formularbasierter
Vorqualifizierung in Bitrix24 erfasst. 12 % bezeichnet Besucher zu Leads;
15 % bezeichnet Abschlüsse unter diesen vorqualifizierten CRM-Leads.
Die gemeinsame Erläuterung liegt in `hu_e3_summary('definitions')`.
Keine Gesamt-CPO-Rechnung aus dem erreichten Kampagnen-CPL und der CRM-Quote.

### Solar-Leistungsseite: Informationsfolge

Zielgruppe und persönliche Umsetzung zuerst; Kennzahlen in einem gemeinsamen
Belegfeld, Projektphasen im Fallabschnitt. Der interaktive Rechner enthält
Annahmen und darf nicht als Ergebnisprognose dargestellt werden. Stationen
erklären die Umsetzung und den Nutzen für den Vertrieb. Der kostenlose
Marktcheck bleibt der primäre Schritt: persönliche Prüfung, schriftlicher
E-Mail-Befund gemäß Antwortzeit-Kanon, danach freie Entscheidung. Kein
Pflichtgespräch und keine Buchung durch das Absenden.

## Die Anfrage-Website — Freigaben 02.10. und 04.10.2026

Produktname: **Die Anfrage-Website**. Erweiterbares Grundprodukt aus Custom Code
ohne Pagebuilder, mit Gutenberg/ACF für pflegbare Inhalte, GitHub-Versionierung
und KI-gestützter Umsetzung mit Qualitätsprüfung. Preise ausschließlich aus
`inc/canon/pricing-canon.php`: Grundpreis für Grundsystem und erste Hauptseite;
weitere Seiten nach Aufgabe als kurze Seite, Standardseite oder
Leistungs-/Verkaufsseite. Conversion-Tracking aus der Messung-Stufe ist optional
und wird projektweit berechnet. Seit der Preispräzisierung vom 06.10.2026 ist
diese Produktformel der öffentliche Preisanker. Ein Stundensatz wird auf der
Produktseite nicht daneben gestellt und der Konfigurator bleibt eine
unverbindliche Scope-Anfrage, kein Shop-Checkout. Fertige, freigegebene Texte
werden eingepflegt; Texterstellung ist ein optionaler, je Seitentyp
kalkulierter Baustein.
Kontaktformular, Bestätigungsmail und Danke-Seite sind Standard. Technisches SEO
und On-Page sowie das Erstellen der Impressum- und Datenschutz-Seiten mit
Einbindung gelieferter Rechtstexte sind inklusive. Keine Rechtsberatung oder
Erstellung der Rechtstexte als Zusage.

Finale Seitengestaltung vom 06.10.2026: Der Einstieg erklärt die Anfrage-Website
über ein verständliches Angebot, einen sichtbaren nächsten Schritt und den
kanonischen Einstiegspreis. Früh sichtbare Qualitätsgründe führen zu Aufbau,
Rechner, Lieferumfang und einer eigenständig ausführbaren Ladezeitprüfung.
Reale Projekte stehen vor dem Konfigurator, E3 zuerst; die Illustrationen im
Qualitätsabschnitt sind ausdrücklich keine Kundenprojekte oder Messergebnisse.
Keine simulierte Ladezeit, kein behaupteter Qualitätsvorsprung oder Rankingsieg.
Einheitliche lesbare Buttons und eine Kapitelzeile unterstützen die Entscheidung.
Der Abschluss zeigt dieselbe Auswahl und denselben bekannten Preis wie der
Rechner; unbekannte Dashboard-Kosten bleiben zusätzlich ausgewiesen.

Seit dem Produktdurchgang vom 04.10.2026 sind individuelles Screendesign,
Conversion-Tracking und Standard-CRM feste, vollständig eingerechnete Extras.
Screendesign: erstes unterschiedliches Layout und jedes weitere nach Kanon;
Desktop und Mobil bilden ein Layout, Wiederverwendung wird einmal berechnet.
Eine Gestaltungsrichtung in Figma und zwei gebündelte Korrekturrunden sind
enthalten. Neue Marke/Logo, weitere Richtungen oder komplexe Interaktionen separat.
Die responsive Basisgestaltung bleibt inklusive; fertige Designs sparen die
Erstellungsphase und benötigen eine Umfangsprüfung vor Beauftragung.

Standard-CRM: ein Formular an bestehendes HubSpot oder Bitrix24, höchstens zehn
Felder und ein Kontakt- oder Lead-Objekt. Feldzuordnung, Dublettenregel,
Fehlerbehandlung, Tests und Dokumentation. Preis und Planungszeit aus dem Kanon,
Voraussetzungen vor Auftrag prüfen. Andere CRMs, Salesforce, weitere Formulare,
Objekte, Migration und Automationen separat. CRM-Lizenzen sind nicht enthalten.
Conversion-Tracking: GA4/GTM/Consent Mode und eine Google-Ads-Conversion für die
Standard-Anfrage; Server-Side/Meta/Offline und weitere Ziele separat.
Ein gegebenenfalls benötigter Consent-Dienst wird vorab mit Kosten benannt.

Basis-Daten-Cockpit inklusive: Search Console, gespeicherte Formularanfragen und Top-Landingpages;
keine GA4-Pflicht im Grundprodukt. Ein individuelles Daten-Dashboard für weitere Quellen wie
GA4, Ads, Meta oder CRM bleibt optional nach Angebot. Bei Auswahl stehen Preis und Zeit ausdrücklich
**ohne individuellen Dashboard-Zusatz**, als bekannter Anteil. Die vollständige Kalkulation erfolgt
vor Beauftragung. Kein separates Analytics-
Plugin und keine neue Messung auf Hasims Website durch diesen Konfigurator.
Formularstrecke und gespeicherte Anfragen gehören zum Grundprodukt; ein
funktionierendes Formular ist noch kein Conversion-Tracking.

Rechner bis zehn Inhaltsseiten. Preise und Zeitfaktoren liegen in
`hu_website_calculator_rules()`. Grundsystem und erste Hauptseite bilden die
Basis; zusätzliche kurze Seiten, Standardseiten und Leistungs-/Verkaufsseiten
haben eigene Preis- und Zeitbeiträge. Tracking und Standard-CRM werden je
Projekt gerechnet, nicht pro URL; Relaunch besitzt einen eigenen Zeitbeitrag.
Texterstellung ist optional und folgt dem gewählten Seitentyp. Fertige,
freigegebene Texte werden ohne Copy-Aufpreis eingepflegt. Screendesign wird pro
unterschiedlichem Layout gerechnet; Wiederverwendung erzeugt keinen zweiten
Designpreis. Teil-Tage werden erst nach Addition aller Phasen auf volle
Gesamt-Werktage gerundet. Diese Werte sind Planungsannahmen, keine empirisch
gemessenen Leistungswerte oder Liefergarantie.
Projektzeit bis zum geprüften Abnahmestand; Vorbereitung und Umsetzung separat.
Kundenfreigaben, Verfügbarkeit, Starttermin und Livegang kommen separat dazu.
Start nach vereinbartem Umfang, Vertrag, Briefing, Bildern, Rechtstexten und
Zugängen; gelieferte Texte/Designs müssen vollständig und freigegeben sein.
Eine entscheidende Person, Rückmeldung in fünf Werktagen je Runde.

Einmalpreis, kein Website-Abo, keine Pflichtwartung und keine Pflichtlizenzen
im Grundprodukt. Keine pauschale Aussage, alle Projekte seien dauerhaft kostenlos.
Gutenberg/ACF machen Inhalte pflegbar; keine implizite ACF-Pro-Lizenz im Grundpreis.
Domain/Hosting direkt beim Anbieter, externe Dienste/Lizenzen vorab benennen.
Zwei Korrekturrunden, Dokumentation, Editor-Einweisung und 30 Tage kostenlose
Fehlerbehebung. Impressum, Datenschutz, Danke und 404 zählen nicht als Seite.
Rechtstexte werden geliefert; ihre Erstellung/Prüfung ist nicht zugesagt.
Keine unbelegte Behauptung über fehlende Leistungen bei anderen Anbietern und
kein unbelegter Rabatt gegenüber einem angeblichen Marktdurchschnitt.
Landingpage, Übernahme-Check und Monatskontingente bleiben unverändert.
Auswahl und serverseitige Kalkulation werden in Anfrage, Mails und CRM erhalten.

Flow-Durchgang 05.10.2026, Scope-Präzisierung 06.10.2026: Diese
Produktseite spricht Unternehmen und Selbstständige an, auch wenn ihre eigenen
Kunden Privatpersonen sind. Die Umfangswahl ist einmal im Konfigurator
integriert. Grundsystem und erste Hauptseite sind die Basis; zusätzliche Seiten
werden als kurze Seite, Standardseite oder Leistungs-/Verkaufsseite gewählt.
Die drei sichtbaren Vorlagen setzen nur passende Typmischungen und bleiben
jederzeit manuell veränderbar. Ein Inhaltsabschnitt ist keine zusätzliche
Seite. Shop und Buchung bleiben separat.

Die Oberfläche folgt Seitentypen → Texte → Gestaltung → optionale
Erweiterungen. Fertige Texte sind der Default; Texterstellung ist ein
preiswirksamer Zusatz je Seitentyp. Basisgestaltung bleibt inklusive.
Individuelles Screendesign startet mit einem wiederverwendbaren Layout; weitere
unterschiedliche Layouts werden ausdrücklich gewählt. Tracking und CRM sind
Projektmodule und vervielfachen sich nicht mit der Seitenzahl. Preise und Zeit
werden aus dem Kanon und serverseitig neu berechnet. Dashboard bleibt zusätzlich
nach Angebot. Kein verpflichtender Wizard.
Seit der CRO-Kürzung vom 06.10.2026 steht vor dem Konfigurator nur E3
New Energy als Hauptbeleg. Der Beitrag wird korrekt als Weiterentwicklung und
Anfrageweg beschrieben, nicht als eigener Website-Neubau; vertrauliche
Kennzahlen bleiben unsichtbar. hasimuener.org und Civaka Azad werden auf dieser
Produktseite nicht mehr als eigene Referenzkarten wiederholt. Nach dem Rechner
folgen nur drei Differenzierungsprinzipien (Klarheit, geprüfter Anfrageweg,
Eigentum), ein kompakter Drei-Stufen-Ablauf und sechs Kauf-Fragen.
