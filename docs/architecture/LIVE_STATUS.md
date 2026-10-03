# Live Status

Aktuelles Verhalten der Website, nach Bereichen. Stand: Repository `main`
einschließlich der Änderungen vom 2026-09-26 (gilt nach Merge und Deploy).

Diese Datei beschreibt den Ist-Zustand, keinen Verlauf. Die frühere,
chronologische Fassung mit Begründungen und Prüfprotokollen bis 2026-09-22
liegt in der Git-History: `git show ad69c5c:docs/architecture/LIVE_STATUS.md`.
Neue Einträge ersetzen den betroffenen Absatz, statt oben einen datierten
Abschnitt anzuhängen; der Verlauf gehört in Commit-Nachrichten.

## Grundlage und Grenzen

- Basis: Repo-Inhalt, Deploy-Workflows und die Template- und Funnel-Logik im
  Theme `blocksy-child/`.
- Nicht aus dem Repo verifizierbar: WordPress-Admin (Menüs, Editor-Inhalte,
  Plugins), Brevo-, Cal.com- und Koko-Setups, Server-Cron und die tatsächliche
  Mailzustellung.
- Deploy: `.github/workflows/ci.yml` prüft; `.github/workflows/deploy.yml`
  deployt bei Runtime-/Tooling-Änderungen nach grünem CI-Lauf auf `main` oder
  manuell. Reine Dokumentations- und Skill-Änderungen lösen keinen automatischen
  Deploy aus; die gemeinsame Prüfauswahl liegt in `scripts/check.py`. Die statische `llms.txt`
  wird zusätzlich ins Webroot kopiert.
- Live liegt ein Cache vor PHP (nginx, Varnish). Formulare arbeiten deshalb
  ohne Nonce im HTML; öffentliche REST-Endpunkte sind zustandslos abgesichert.

## Positionierung und Navigation

- Öffentliche Rolle: WordPress Freelancer aus der Region Hannover; verbundene
  Kompetenz technisches SEO, Tracking und Conversion. Maßgeblich sind
  `docs/standards/BRAND_AND_COPY.md` und `docs/architecture/CONVERSION_ROUTING.md`.
- Drei Wege: direkte Projekte → `/kontakt/?type=project`, Agenturen →
  `/whitelabel-retainer/` (Platz 3 in der Navigation, direkt hinter den
  Leistungen; auf der Startseite seit
  2026-09-24 ein leiser Nebenausgang unter den Leistungen), Energie-Intent →
  `/solar-waermepumpen-leadgenerierung/#marktcheck`. Der Marktcheck ist kein
  globaler CTA.
- Kopf: `.leiste` aus `system.css` plus `leiste.js`, gerendert von
  `template-parts/site-header.php`. Seit 2026-10-01 entscheidet
  `hu_funnel_context()` (`inc/funnel-doors.php`) Modus und Tür; Kopf und Fuß
  lesen dieselbe Entscheidung. Modus `voll`: Wortmarke, Projekte (`/#angebote`),
  Tracking (`/ga4-tracking-setup/`, Route `tracking_setup`), White-Label,
  Solar & Wärmepumpe, Haarlinie, Ergebnisse (`/#arbeiten`, Ziel aus
  `hu_get_results_nav_url()`), die Tür, „Menü“; „Über Haşim“ steht nur im
  Klappblatt und im Fuß. Modus `leser` (Einzelbeiträge): Wortmarke,
  Artikelpfad „Wissen / Dossier“, die Tür, kein Hauptmenü; der frühere
  Lesekopf (`article-reader-header.php`, `blog-header.css`) ist entfallen, die
  Klasse `nexus-article-reader-header` bleibt als Haken für die Artikel-
  Stylesheets. Modus `fokus` (Solar-Seite, `inc/header.php` rendert die Leiste
  dort): Wortmarke und die Leiter Marktcheck · Analyse · Sofortkontakt mit
  Betrag auf die Anker der Seite, nicht sticky, höchstens 56 px, unter 561 px
  nur Sofortkontakt. Die Tür ist kontextabhängig (Matrix in
  `docs/architecture/CONVERSION_ROUTING.md`): Projekt anfragen (ohne Betrag),
  Tracking anfragen („ab“ Messung-Setup), Test-Sprint anfragen, Marktcheck
  (kostenlos) oder Sofortkontakt; Beträge kommen aus dem Kanon. Quelle der
  Punkte ist `hu_get_site_header_navigation_contract()` in
  `inc/commercial-routing.php`; Klappblatt, 404-Seite, gespeichertes
  WordPress-Menü (`inc/menu-setup.php`, nur Backend-Zustand, nirgends
  gerendert) und SEO Cockpit lesen denselben Contract.
  `aria-current="page"` nur auf dem Link, der die Seite ist;
  `aria-current="true"` für den Bereich (Server-Side-Seite unter Tracking,
  Fallstudie unter Ergebnisse). Unter 1081 px bleibt die Tür in der Zeile
  neben „Menü“ (unter 561 px als Kurztext, unter 371 px ohne Betrag, unter
  340 px nur im Blatt); auf Seiten mit Sticky-CTA-Leiste übernimmt unter
  761 px die Leiste. Das Klappblatt scrollt selbst, wenn es höher als der
  Viewport ist (Handy quer). Auf `/kontakt/` entfällt die Tür.
  `HU_FEATURE_SOLAR_DOORS` (Vorgabe `false`, `inc/feature-flags.php`) hält die
  Türen Analyse und Sofortkontakt bis zur Solar-Strecke auf `#einstieg`.
  `/whitelabel-retainer/` hat eine eigene Seitennavigation aus derselben
  `.leiste` (`template-parts/whitelabel-header.php`), ohne Klappblatt, mit der
  Tür „Test-Sprint anfragen“ aus `hu_funnel_doors()` (Action
  `cta_whitelabel_header_task_brief` bleibt, `data-door="aufgabe"`).
  Auf der Startseite (über `startseite-strecke.css/.js`) ist „Projekte“ nur
  aktiv, solange `#angebote` im Blick ist (`aria-current="location"`), ohne
  JavaScript neutral; die Wortmarke trägt `aria-current="page"`. Der
  Menüknopf hat kein eigenes `aria-label`, der sichtbare Text
  „Menü“/„Schließen“ ist sein Name.
- Fuß: `template-parts/site-footer.php` aus
  `hu_get_site_footer_navigation_contract()` und `hu_funnel_doors()`. Seit
  2026-10-01 ein Türregister: vier Wege in Kopf-Reihenfolge (Website,
  Tracking, Agentur, Energie), je Tür eine Zeile mit Bezeichnung, Betrag
  (aus dem Kanon) und Pfeil, Tracking `cta_footer_door_<schlüssel>`. Die
  Tracking-Tür im Fuß liest den Server-Side-Setup-Anker über `footer_amount`
  (`standard/setup`); andere Tracking-Türen behalten den Messung-Einstieg.
  eigene Route ist markiert („Ihr Weg“), nicht ausgeblendet; das Register
  erscheint auf der Energie-Seite; Startseite, `/kontakt/` und die
  Seiten aus `hu_footer_register_suppressed_templates()` (`inc/funnel-doors.php`,
  derzeit `/conversion-optimierung/`) zeigen es nicht. Darunter Direktzeile und Verzeichnis in vier Gruppen: Leistungen
  (Server-Side Tracking, Performance Marketing, WordPress Agentur Hannover,
  Landingpage erstellen lassen, Conversion-Optimierung, WordPress-Website
  erstellen lassen), Belege & Person
  (Solar-Fallstudie, Über Haşim), Wissen (Blog, Glossar), Rechtliches
  (Impressum, Datenschutz). Der frühere `<style>`-Block im Template
  (Kontaktseite) steht jetzt in `contact.css`.
- 404: dieselben Wege wie der Kopf plus Startseite und Blog; kein Marktcheck.
- Prüfung: `scripts/tests/navigation-contract.php` (Contract, Ziele,
  aria-current, bekannte Routen) und `scripts/tests/navigation.spec.cjs`
  (Desktop, Mobil, Tastatur, ohne JavaScript) laufen in CI.

## SEO Cockpit

- Die operative Content-Arbeitsfläche heißt `Content-Chancen`. Sie setzt auf
  Content Intelligence V1.1 auf, verändert die Research-/GSC-Matchinglogik aber
  nicht. Pro Signal werden jetzt `Jetzt tun`, `Prüfen & planen` oder
  `Beobachten`, `Warum jetzt`, Zielseite und ein deterministischer nächster
  Schritt ausgegeben.
- Die Decision Layer liest Research/GSC sowie lokale WordPress-, DataForSEO-
  Snapshot- und CRM-Signale. Sie führt jetzt sowohl primärdatengetriebene
  Content-Signale als auch direkte, business-segmentierte Market-Intelligence-
  Keyword-Chancen in derselben Queue. Beim bloßen Rendern von `Content-Chancen`
  wird kein neuer Research- oder DataForSEO-Netzwerkrequest ausgelöst.
- Dashboard V3 zeigt vor den analytischen Detailsektionen maximal drei
  handlungsfähige Entscheidungen aus `Jetzt tun` und `Prüfen & planen`;
  Beobachtungssignale bleiben in der vollständigen Content-Chancen-Ansicht.
- Audit-Titel mit Präfix `TEST` (case-insensitive) bleiben gespeichert und
  sind aus Lead-Zählern, Attribution und Action-Hub-/Revenue-Priorisierung
  ausgeschlossen. `/whitelabel-retainer/` hat die Rolle `Service`; der
  Maßnahmenhinweis nennt das Agentur-Angebot und die Aufgabenanfrage.
  Performance Pulse nutzt den letzten tatsächlich gelieferten Datentag
  statt der Nullwerte eines ergänzten fehlenden Tages.
- `Research` heißt im Menü `Datenbasis` und steht hinter den operativen
  Bereichen. Dort bleiben CrUX, Energy-Charts, Destatis und Eurostat als
  Primär-/Felddaten vollständig zugänglich.
- `Markt & Wettbewerb` trennt jetzt automatische organische Wettbewerber von
  einer strategischen WordPress-/B2B-Vergleichsgruppe. Die strategischen Domains
  können nur per explizitem Admin-Klick mit DataForSEO Domain Rank Overview
  geprüft werden (maximal acht Domains pro Klick); der Wochen-Cron startet diese
  Einzelabfragen nicht.
- Der separate `Market CSV`-Download exportiert den bestehenden Market-Snapshot,
  Keyword-/Wettbewerberdaten, Opportunities und vorhandene Live-SERPs. Der
  Download selbst ist read-only und löst keinen DataForSEO-Request aus.
- Operative Reihenfolge im unteren Cockpit-Menü: Content-Chancen, Markt &
  Wettbewerb, Site Audit, Datenbasis.

## Routen

- **`/`** (`front-page.php`): Money Page für direkte WordPress-Projekte.
  Finale Fassung im Repo am 2026-10-02 umgesetzt; der Livegang ist noch offen.
  Title „WordPress Freelancer: Website, Tracking, Anfragen · Haşim Üner“,
  Query-Owner für `wordpress freelancer` und `wordpress freelancer hannover`.
  Die bestehende Strecke (`assets/css/startseite-strecke.css/.js`) hat acht
  Abschnitte: Klick, Strecke, Fall, Prüfstand, Preise, Übergabe, Fragen, Anfrage.
  Hero und Startseiten-Kopf sind seit dem Umbau zur Messfläche dunkel; die
  gemeinsamen Flächen-, Raster- und Maskenregeln mit White-Label liegen einmal
  in `startseite-strecke.css`. Vier H1-Zeilen zeigen dieselbe Aussage; die Quelle
  dieses Besuchs hängt als dekoratives Etikett am letzten Satz. Die Herkunft
  bleibt in Station 01 für Hilfstechnik lesbar. Der Kicker enthält den Kanonpreis
  (`home_hero_price_line`), darunter stehen Ersteinschätzung und „Alle Preise“.
  Das Protokoll ist die Bahn aus demselben Stationsarray wie Abschnitt 02:
  Herkunft, LCP und Klickstatus („noch offen“ / „geöffnet · Ort“), danach die
  gedämpften Stationen Messung, CRM und Anfrage. Das Signal hält bei Formular;
  Stationslinks (`home_hero_station`, Label = Slug) öffnen und fokussieren das
  passende native Akkordeon. Person und Belege folgen der Bahn im Hero.
  Die frühere Hero-Seitennavigation entfällt. Alles ab Abschnitt 02 ist erhalten.
  Sechs Stationen sind native exklusive Akkordeons, die erste ist offen.
  Der dokumentierte Fall (`#arbeiten`, `#systemprojekt`) steht vor den Preisen:
  heller Hintergrund, Drei-Phasen-Verlauf aus dem E3-Kanon und eine dunkle
  Messtafel mit maßstäblichen CPL-Balken. Diese wachsen einmal beim Sichtkontakt
  ausschließlich per Transform. Der Prüfstand enthält Code, CI, PageSpeed mit
  dem lokal gemessenen LCP und die Referenzen (`#referenzen`). Unter fünf
  Angeboten (`#angebote`, alle Schema-Anker erhalten) stehen Weiterentwicklung,
  Hosting-Hinweis und die bedingte Ersteinschätzungs-Zeile. Übergabe enthält
  Eigentum, Ausfall-Einwand, Projektablauf und die Nebenwege zu White-Label und
  Solar/Wärmepumpe. Fünf sichtbare FAQ teilen ihr Array mit dem FAQPage-Schema.
  Der Abschluss (`#anfrage`, `#kontakt`) bleibt hell und enthält zwei Einstiege,
  die nächsten Schritte und das Portrait. Dunkel sind Hero-Messfläche und Messtafel.
  Die Messlinie liegt in der linken Rinne und folgt
  einer Lesekante bei 45 % der Fensterhöhe; am Seitenende ist alles erreicht.
  Ohne JavaScript bleiben Stationen und FAQ bedienbar, das Protokoll bleibt leer.
  Bei reduzierter Bewegung stehen Etikett und Signal nach 200 ms im Endzustand;
  die Leselinie folgt ohne Übergang dem Scrollstand. Ohne JS fehlen Etikett und
  Signal, die H1 bleibt gefüllt, Messwerte stehen auf „…“ mit Ausfallhinweis.
  Die Hero-Choreografie startet nach `document.fonts.ready`, animiert nur Transform
  und Opacity und vermisst Etikett und Bahn bei Größenwechseln neu.
  Hypothese: mehr Hero-Ersteinschätzungen und Stationsöffnungen. Sechs Wochen
  nach Livegang `home_head_ersteinschaetzung` (Section = hero) und
  `home_hero_station` gegen den vorherigen Zeitraum prüfen; Stationsöffnungen
  sind ein neuer Hook und haben vor diesem Release keine eigene Baseline.
  Das Protokoll zeigt lokale Herkunft (UTM oder Verweis-Domain), Ladezeit,
  erreichte Abschnitte, Scrolltiefe und den Ort einer Anfrage-Aktion, auch im
  globalen Kopf. Es sendet und speichert nichts (`strecke-js-privat`).
  Der Ersteinschätzungs-Schalter gilt für Kopf, Hero, Fall, Preise und Abschluss;
  ohne Versuch führen die Einstiege zur Projektanfrage; die zusätzliche
  Ersteinschätzungs-Zeile am Preisabschluss entfällt, der Kickerpreis bleibt.
  Das globale Fußregister entfällt nur auf der Homepage; Verzeichnis,
  Kontaktzeile und Absender bleiben. `/wordpress-freelancer-hannover/` leitet
  unverändert per 301 hierher. Beobachtung nach sechs Wochen ab Livegang:
  Fall-CTA gegen Hero-CTA und Qualität der Einsendungen, siehe Experiment-Doku.
- **`/kontakt/`** (`page-kontakt.php`): Anfrage-Intake, siehe „Anfragewege“.
  Bis 820 px folgt das Formular direkt auf Titel und Einleitung; Ablauf,
  andere Einstiege und E-Mail stehen darunter. Direkt unter der H1 steht auf
  jeder Variante „Was passiert danach?“ (Antwortzeit aus dem Kanon). In der
  Aktionsleiste ist „Lieber direkt Termin buchen →“ ein Sekundär-Button über
  der Antwortzeit-Zeile. `/kontakt/?focus=ersteinschaetzung` ist ein
  Kurzformular mit drei Feldern (URL, Ziel, E-Mail) und Datenschutz-Checkbox,
  Button „Drei Befunde anfordern“, ohne Name; `request_type=ersteinschaetzung`
  bleibt das Unterscheidungsmerkmal im Backend.
- **`/whitelabel-retainer/`** (`page-whitelabel-retainer.php`): Agentur-Einstieg
  und Landeseite der Akquise-Mails. Finale Fassung im Repo vom 2026-10-02,
  Live-Abnahme nach dem Deploy. Basis bleibt `startseite-strecke.css/.js`,
  `whitelabel.css/.js` ergänzt nur die lokalen Komponenten. Acht Stationen:
  Auftrag (`#hero`), Felder (`#lieferfelder`), Belege (`#proof`, `#pruefstand`,
  `#referenzen`), Ablauf (`#zusammenarbeit`), Preise (`#einstieg`), Absicherung
  (`#absicherung`, `#eignung`), Fragen (`#faq`), Anfrage (`#naechster-schritt`,
  Formular `#aufgabe`). Seitennavigation: Leistungen/Belege/Ablauf/Preise/Fragen;
  Aufbau und Tür des Kopfes bleiben, Kopf und Hero bilden eine dunkle Fläche.
  H1 „Gebaut. Gemessen. Abgenommen.“; ein Hauptbutton von Anfang an bedienbar,
  Preise als Link. Der Hero prüft lokal sieben echte Befunde (Überschriften,
  Bilder, Pflichtfelder, neue Tabs, LCP mit ausdrücklich benanntem Dokument-
  Fallback, lesbare Cookies, JSON-LD). Messmarken in der Randspalte ab 768 px,
  Protokoll daneben ab 1280 px. Stempel und Füllung zeigen die echte Punktzahl;
  ein Cookie bei eingeloggten Admins bleibt ein Befund. Ohne JS stehen alle
  Wörter, es läuft keine Prüfung. Reduzierte Bewegung zeigt Ergebnisse ohne
  Staffelung. Keine Kopplung mehr zwischen Protokoll und Ablaufstationen.
  Die vier Felder sind native exklusive Akkordeons. Belege sind hell; dunkel
  sind nur Hero-Prüfstand und Margen-Tafel (`#marge`). Vier Preiszeilen mit
  eigenen Anfrage-CTAs; Monatskontingent danach. `hu_whitelabel_margin_rows()`
  berechnet Vergleich, Differenz, Abschlag und Skala ausschließlich für das
  Server-Side-Setup mit gleichem Umfang. Die Landingpage bleibt wegen des
  abweichenden Endkundenumfangs außerhalb des Vergleichs. Fehler/Änderung
  steht im Ablauf, Ausstieg in der Absicherung; die fünf übrigen FAQ-Antworten
  einschließlich `kapazitaet` bleiben unverändert.
  Formular-Markup und Formular-JS bleiben unverändert: REST
  `nexus/v1/whitelabel-request`, Honeypot, Fehlerliste, Zugänge, Mail-Fallback,
  `?case=aufgabe`, `angebotsphase`, `vormerken`. Mobiler Sticky-CTA nach dem
  Hero, verborgen während das Formular im Bild ist. Preise und Antwortzeit
  aus dem Kanon, Service/BusinessAudience/OfferCatalog und FAQPage zentral in
  `inc/org-schema.php`; Offer-IDs auf `#test-sprint`, `#angebot-tracking-audit`,
  `#angebot-server-side`, `#angebot-landingpage`, mit Anker-Lint. Die Regel
  `strecke-js-privat` prüft auch das abgegrenzte White-Label-Messmodul;
  Cookie-Lesen ist eine Prüfung, Versand oder Speicherung dort verboten.
  Eigenes og:image `assets/img/whitelabel-retainer-og.jpg` bleibt.
  Hypothese und Auswertung: `docs/experimente/whitelabel-angebotsphase.md`.
- **`/performance-marketing/`** (`page-performance.php`, Gutachten-Layout):
  Performance Marketing für B2B in der Reihenfolge Messung → Zielseite →
  Budget, mit eigenem Weg für Performance-Agenturen zu White-Label. Titel,
  Beschreibung und FAQ kommen aus `nexus_get_wgos_cluster_page_data()`,
  Service-Schema aus `inc/org-schema.php`. Query-Owner für
  `performance marketing b2b`.
- **`/landingpage-erstellen-lassen/`** (`page-landingpage-erstellen-lassen.php`,
  Gutachten-Layout wie `/performance-marketing/`, seit 2026-09-26): Festpreis-
  Angebot Landingpage für Direktkunden. Preis aus `hu_landingpage_price()`,
  Umfang (Auftakt, Konzept und Text, Umsetzung, Anfrageformular, Herkunft jeder
  Anfrage, SEO-Grundlagen, Abnahme mit zwei Korrekturschleifen) und die Grenze
  „Nicht dazu“ im Template; Zusätze aus Tracking-Leiter und Freelancer-Preisliste.
  FAQ und FAQPage-Schema aus `nexus_get_landingpage_faq_items()`, Service mit
  Offer in `inc/org-schema.php`, Title/Description in `inc/seo-meta.php`.
  CTAs auf `/kontakt/?type=project&focus=conversion`. Route `landingpage`,
  Seitenanlage über `nexus_get_provisioned_pages()`. Query-Owner für
  `landingpage erstellen lassen`. Eingehende Links: Fuß (Gruppe Leistungen),
  `/performance-marketing/` (Vorgehen) und `/glossar/landingpage/`
  (Hauptziel des Begriffs). Die Startseite verlinkt die Seite seit 2026-10-01
  aus der Preisliste (`home_offer_landingpage_detail`).
- **`/conversion-optimierung/`** (`page-conversion-optimierung.php`,
  Gutachten-Layout wie `/landingpage-erstellen-lassen/`, seit 2026-09-30):
  Conversion-Optimierung für B2B-Websites mit wenig Traffic, Einstieg ist die
  Anfragesystem-Analyse (`hu_analysis_price()`, Dauer aus
  `hu_diagnose_canon()['primary_days']`, dasselbe Produkt wie auf der
  Solar-Seite), danach Umsetzung zu Festpreisen aus dem Kanon. Seit
  2026-10-01 verdichtet (rund 740 Wörter): Kopf, sechs Abschnitte (Anlass,
  Strecke, Analyse, Danach, Beleg, Fragen) und Abschluss. Die Kernaussage „Drei
  von sechs Stationen liegen hinter dem Formular“ ist eine HTML/CSS-Grafik
  (geordnete Liste, Formularkante, mobil einspaltig); „Warum kein A/B-Test“ ist
  Marginalie in Abschnitt 3, die Vorqualifizierung nur Station 05 und eine
  Preiszeile. Beleg ist allein der PV-Fall (kein Firmenname, keine Region), die
  Referenzliste entfällt. Layout aus `system.css` plus Delta
  `assets/css/conversion-optimierung.css`. FAQ (drei Fragen) und FAQPage-Schema
  aus `nexus_get_conversion_faq_items()`, Service mit Offer (Betrag, netto in der
  Beschreibung) in `inc/org-schema.php`, Title/Description in `inc/seo-meta.php`.
  CTAs auf `/kontakt/?type=analysis&focus=conversion`, alle Tracking-Werte mit
  Präfix `cro_offer_`, kein Marktcheck-CTA. Der Fuß zeigt auf dieser Seite kein
  Türregister (`hu_footer_register_suppressed_templates()`). Route `conversion`,
  Seitenanlage über `nexus_get_provisioned_pages()`. Query-Owner für
  `conversion optimierung b2b`, `conversion optimierung`,
  `conversion rate optimierung` und `wordpress conversion optimieren`.
  Eingehende Links: Fuß (Gruppe Leistungen, `cta_footer_nav_conversion`),
  `/landingpage-erstellen-lassen/` (Anlass 04), `/ga4-tracking-setup/`
  (Abschnitt Probleme), Artikel `/website-relaunch/` und die Glossarbegriffe
  `conversion`, `conversion-rate`, `formularabbruch`, `lead-qualifizierung`
  (Hauptziel „Passende Leistung“). Die Startseite verlinkt die Seite seit
  2026-10-01 aus der Preisliste (`home_offer_analysis_detail`). `assets/css/cro.css` (Scope
  `.cro-page`) lädt nur noch auf der stillgelegten Route
  `/conversion-rate-optimization/` und bleibt unberührt.
  `/conversion-rate-optimization/` liefert weiter 410. Die Backlink-Prüfung
  für 301 statt 410 konnte nicht abgeschlossen werden (DataForSEO ohne
  Guthaben, Bing-Linkdaten nicht angebunden, im Repo nur Domain-Summary:
  31 Backlinks, 25 verweisende Domains); Entscheidung offen.
- **`/wordpress-website-erstellen-lassen/`** (`page-wordpress-website-erstellen-lassen.php`,
  Die Anfrage-Website, Fassung 02.10.2026): Produktseite nach dem freigegebenen
  Prototyp. Hero → Durchleuchtung → Angebot mit Rechner → Beleg → Zeit → Fragen
  → Anfrage. Zwei dunkle Messflächen; globaler Kopf und Fuß bleiben erhalten.
  `system.css` plus gekapseltes `anfrage-website.css`, Vanilla-JS; kein Legacy-Provider.
  Preis aus `hu_freelancer_website_price()` inklusive erster Seite, jede weitere
  aus `hu_freelancer_website_extra_page_price()`, optional Tracking aus der Leiter. Rechner 1–10 Seiten. Bauzeit 2/3/4 Wochen (bis 2/5/10
  Seiten), Relaunch +1 Woche, ab vollständigen Inhalten. Acht FAQ aus einem Getter,
  identisch im sichtbaren Text und FAQPage; Service/Offer mit zwei Preispositionen,
  Breadcrumb und Meta bleiben zentral. Alle fünf Produkt-CTAs übergeben
  `/kontakt/?type=project&focus=website&seiten=N&art=neubau|relaunch&tracking=1`;
  tracking wird bei Abwahl weggelassen. Kontakt zeigt den Umfang, validiert ihn
  serverseitig und speichert ihn in Mail, Bestätigung und CRM. Rechnerwerte bleiben
  ohne Cookies oder Speicherung; Matomo-Events nur Umfang/Position, keine Formulardaten.
  Matomo-Transport und dessen Konfiguration werden vom vorhandenen Website-Setup
  erwartet; keine neue Tracker-ID oder externe Laufzeit wird injiziert.
  Ohne JS sind beide Vergleichsansichten und beide Textzeilen sichtbar, der
  statische Beispielumfang wird korrekt übergeben. Sticky-CTA erst nach dem Hero,
  am Abschluss ausgeblendet und aus der Tastaturfolge entfernt. Reduzierte Bewegung:
  keine Überblendung, kein Ladebalken. Belegbild: geliefert `hasimuener-org.webp`.
  Zufluss: Kontextlink „WordPress-Website zum Festpreis“ auf der lokalen Agenturseite,
  Startseite, Landingpage und Fuß. Query-Owner unverändert. Auswertung nach acht
  Wochen oder 300 Aufrufen, siehe `docs/experimente/anfrage-website.md`.
- **`/ga4-tracking-setup/`** (`page-ga4.php`, virtuelle Cluster-Route):
  Das Tracking-Angebot. Zeigt alle vier Stufen der Tracking-Leiter aus
  `hu_tracking_product_ladder()` als Karten `#stufe-1` bis `#stufe-4` (Raster
  2 × 2) mit Umfang, Festpreis und Lieferzeit; Meta-Description, FAQ
  (`hu_tracking_setup_faq_items()`) und Service-Offer lesen dieselbe Leiter,
  das Offer meldet Stufe 1. CTAs auf `/kontakt/?type=project&focus=tracking`.
  Query-Owner für `conversion tracking einrichten lassen`. Ziel des Kopfpunkts „Tracking“, des
  Tracking-Wegs im Fuß und der Station „Messung“ der Startseite (Route
  `tracking_setup`).
- **`/server-side-tracking-b2b/`** (`page-server-side-tracking-b2b.php`):
  Fachseite und Query-Owner für Server-Side Tracking mit eigenem Formular
  (`contact-request`, `type=project`, `focus=tracking`). Pakete sind die
  Stufen 2 bis 4 der Tracking-Leiter, Care-Stufen heißen nach der Stufe, die
  sie betreuen; über den Paketen verweist ein Satz auf Stufe 1
  (`cta_package_to_measurement`). Seitenweit verlinkt
  im Fuß als „Server-Side Tracking“ (Route `tracking_b2b`). Beide
  Tracking-Seiten gelten als ein Kontext (`hu_is_tracking_route_context()`).
  Seit 2026-10-01 steht die Route auf `system.css`: ein Stylesheet
  (`server-side-tracking.css`) statt fünf Schichten, kein
  `design-system.css`, keine `--sst-*`/`--vp-*`-Tokens, kein Orange außer
  `--stempel`; die Sticky-CTA-Leiste ist eine `.tafel`.
- **`/wordpress-agentur-hannover/`** (`page-wordpress-agentur.php`):
  Entscheidungsseite „Agentur oder direkte Umsetzung“ mit den Ankern
  `#entscheidung`, `#technik`, `#zusammenarbeit`, `#belege`, `#hannover`, `#faq`,
  `#anfrage`. CTAs seit 2026-09-26 auf die Projektanfrage ohne Vorauswahl;
  Belege: öffentliche Referenzen (`/#referenzen`) und die Fallstudie. Hält die lokalen Agentur-, SEO- und Wartungs-Queries; die
  URL-Karte führt „Technisches SEO“, „Wartung“ und „Methode“ auf
  `#zusammenarbeit`.
- **`/solar-waermepumpen-leadgenerierung/`**
  (`page-solar-waermepumpen-leadgenerierung.php`, `anfragestrecke.css` unter
  `.strecke-doc`): Energie-Money-Page mit Marktcheck am Mount `#sol-quiz-mount`,
  Rechner (`anfragestrecke.js`) und Einstieg `#einstieg`. Der Einstieg nennt
  Zielgruppe und persönliche Umsetzung; ein gemeinsames Belegfeld bündelt
  die Referenzzahlen. Die statische Modellgrafik und die zweite Vergleichstabelle
  entfallen zugunsten des Rechners und der Projektphasen. CTA- und Abschnitts-IDs
  bleiben erhalten. Der Marktcheck erklärt Prüfung, E-Mail-Befund und freie
  Entscheidung über die weitere Zusammenarbeit. Geladen wird
  `solar-leadgenerierung-solara.js`; es lädt `solar-marketcheck-compact.js`
  nach, das die sichtbare Strecke rendert (vier Fit-Fragen plus Kontaktdaten in
  `HU_MARKETCHECK_VISIBLE_STEPS` Schritten) und an `audit-request` sendet. Ohne
  JavaScript verweist der Mount auf das Formular unter `/kontakt/`.
  Die Angebotsleiter ist nach Marktcheck, Analyse, Sofortkontakt-Setup und
  Aufbau in aufsteigender Preisfolge sortiert. Der Marktcheck akzeptiert
  Freemail-Adressen und markiert sie im CRM als solche.
  Die Analyse und das Sofortkontakt-Setup haben nun eigene kurze Formulare bei
  `#analyse` und `#sofortkontakt`; beide senden über `audit-request` mit
  getrennten `intake_variant`-Werten. Die vier Portal-Einordnungen Aroundhome,
  Checkfox, Wattfox und DAA führen im ersten Kontext-CTA zum Sofortkontakt,
  danach zum Marktcheck. Abschnitt 01 zeigt als interaktives Strecken-Modul
  zwei Anfragewege mit Rechnerwerten und schematischen Zwischenstufen;
  ohne JavaScript steht der Endzustand bereit. Danach folgen Rechnung, Fall,
  Einstieg, der gemeinsame Abschnitt „Was es braucht“, Marktcheck, Fragen und
  Verweise. `#anteil` und `#passung` führen beide zu „Was es braucht“.
  Der Rechner zählt Änderungen in 300 ms und vergleicht die Auftragskosten
  in Balken mit gemeinsamem Maßstab. Der Messschrieb zeigt ausschließlich
  dokumentierte CPL-Bandbreiten nach Projektphase, ohne Monatsmesskurve;
  Hover und Tastaturfokus auf den Fallphasen heben die passenden Stufen hervor.
  Den Kopf trägt die Leiste im Modus `fokus` (Wortmarke und Leiter
  Marktcheck · Analyse · Sofortkontakt auf die Anker der Seite); die Seite
  selbst hat keine Kopfzeile mehr. Der
  Marktcheck hat eine harte Papier-/Tafel-Kante ohne Verlauf. `solar-events.js`
  zählt CTA-Klicks und Formularereignisse der drei Türen über anonyme UTC-
  Tageszähler (`nexus/v1/solar-events`); Details in `PRIVACY.md`. Die Türen
  der Leiste liest er über `data-door` (`sofort` zählt als `sofortkontakt`)
  unter den Ereignissen `nav_header_door_*`.
- **Energie-Cluster** (`.hu-intercept`, Pfade in
  `hu_get_solar_seo_subpage_paths()`): `/solar-leads-kaufen-alternative/`,
  `/waermepumpen-leads/`, `/b2b-solar-leads/`,
  `/eigene-leadgenerierung-vs-portale/`, `/lead-funnel-solar/`,
  `/kunden-gewinnen-solarteure/`, `/cost-per-lead-photovoltaik/`,
  `/qualifizierte-pv-anfragen/`, `/solar-leads-kosten-studie/`. Primärziel ist
  der Marktcheck; Byline, Breadcrumb- und Service-Schema über Helper in
  `inc/seo-meta.php`.
- **Nachweise:** `/case-study-solar-leadgenerierung/` ist die Fallstudie; sie
  bleibt `noindex, follow`, bis die Freigabe vorliegt. Unter dem Marktcheck
  steht seit 2026-09-26 ein zweiter, leiser Weg in die Projektanfrage für
  Leser ohne Energiebetrieb (`cta_case_study_to_project`). Seit 2026-10-01
  steht die Seite auf `system.css` und `e3-case-v2.css` (vier `.tafel`-Flächen,
  kein `design-system.css`, kein `energy-systems.css`). Eigenes og:image
  `assets/img/fallstudie-og.jpg` (1200 × 630, JPG, nur Kanonwerte), das auch
  das Beitragsbild im Schema ersetzt. Der Hub `/ergebnisse/` ist seit 2026-09-25
  stillgelegt: 301 auf die Fallstudie (`nexus_redirect_legacy_results_path()`),
  nicht in Sitemap, `llms.txt` und Fuß; die Route `results` und
  `nexus_get_results_url()` zeigen direkt auf die Fallstudie. Template
  `page-ergebnisse.php` bleibt im Repo, ist aber nicht mehr erreichbar. Kennzahlen nur aus
  `inc/canon/e3-proof-canon.php`, Referenzen aus
  `inc/canon/reference-canon.php`.
- **`/hasim-uener/`** (`page-hasim-uener.php`): Personenseite mit
  Person-/ProfilePage-Bezug; `/uber-mich/` leitet per 301 hierher.
- **Blog:** `/blog/` (`home.php`) und vier Dossiers unter `/category/`:
  `leadgenerierung`, `wordpress-performance`, `tracking`, `cro`. Alte
  Kategorien leiten per 301 auf das passende Dossier. Beiträge rendern über
  `template-parts/single-reader.php`; die Kontextbrücke richtet sich nach dem
  Dossier, ohne passende Kategorie führt sie zu den WordPress-Leistungen. Nur
  `leadgenerierung` führt in den Marktcheck. Aroundhome und Checkfox tragen
  eigene Entscheidungs-Cockpits statt des Editor-Inhalts. Der Fuß jedes
  Beitrags (Weiterarbeiten, Leserfeedback, Benachrichtigung, Teilen, Autor,
  Weiterlesen, Projekt-Abschluss) steht in einer Spalte auf `system.css`;
  Gestaltung im Nachsatz von `assets/css/article-reader-body.css`. Das
  Einblenden beim Scrollen versteckt nur unter `.hu-js`; ohne JavaScript
  bleiben alle Blöcke sichtbar, nur das Leserfeedback entfällt.
- **Artikel-Bausteine** (`inc/editorial-bausteine.php`): Shortcodes
  `[hu_kurz]`, `[hu_notiz]`, `[hu_abb]`, `[hu_fall]`, `[hu_pruefliste]`,
  `[hu_abschluss]`, `[hu_quellen]` und Klassen für reines HTML. CSS und JS
  laden nur auf Beiträgen, die einen Baustein enthalten; dort bekommt der
  Reader ab 1280 px eine Marginalspalte. Notiz, Abbildung, Fall,
  Abschluss-Tafel und Quellen tragen `data-hu-schema-skip` und landen nie im
  FAQ-Schema. Referenz:
  `agents/skills/pillar-cornerstone-writer/references/bausteine.md`.
- **`/website-relaunch/`** (Dossier `cro`): „Website-Relaunch: Wann die neue
  Website wirklich besser ist“. Kernaussage seit Fassung 2: Besser ist die
  neue Website, wenn an jedem Übergang mehr der richtigen Besucher
  weiterkommen, festgelegt als heutiger Wert und Ziel vor dem Relaunch; was
  heute trägt, darf dabei nicht verloren gehen. Erster Beitrag auf den
  Bausteinen, aus `assets/content/blog/website-relaunch.html` angelegt und
  mit Seed-Version `2026-09-24-website-relaunch-v2` einmal überschrieben
  (`inc/article-website-relaunch.php`), danach im Editor gepflegt. Zwei
  Abbildungen (Kette, schematisches Vorher/Nachher), FAQ-Schema mit sechs
  Fragen, Titelbild und og:image `website-relaunch-hero-v2.png` in voller
  Größe über das Feld „Open Graph Bild“. Kontextbrücke und Abschluss führen in
  die Projektanfrage mit Fokus `relaunch` bzw. in die Ersteinschätzung
  (`docs/architecture/CONVERSION_ROUTING.md`).
- **Glossar:** `/glossar/` und `/glossar/<begriff>/` verwenden `system.css`
  mit `glossary.css` als Layout-Ergänzung. Die Übersicht zeigt das zentrale
  Register alphabetisch, mit Definitionen sowie einer optionalen Suche und
  Themenfiltern (`glossary.js`); ohne JavaScript bleiben alle Links sichtbar.
  Einträge bestehen aus Kurzdefinition, Beispiel, Einordnung und typischen
  Fehlern. Registrierte Detailseiten rendern ihre Inhalte und verwandten Links
  beim Aufruf, damit frühere Sync-Fallbacks nicht im HTML stehen bleiben.
  Der Sync aktualisiert weiterhin Posts, Metadaten und seine Prüfmarker;
  Das Register enthält 60 Definitionen, jeweils zwölf in Anfragen, Ladezeit &
  Technik, Tracking, SEO und Conversion. 59 Detailseiten erlauben Indexierung;
  CTA-Hierarchie bleibt `noindex, follow`. Fünf Alias-Ziele bleiben erhalten:
  vier sind sichtbare Themenverweise, der lokale Agentur-Alias ist ausgeblendet.
  Damit enthält die Übersicht 64 Einträge. Definitionsfragen und kommerzielle
  Suchintentionen sind in `docs/seo/query-ownership.csv` getrennt;
  die Entscheidung erläutert `docs/seo/glossar-indexierung.md`.
  Neue Begriffe werden in `inc/glossary/glossary-registry-data.php` gepflegt;
  `search_terms` erweitert nur die Glossarsuche, `keywords_match` steuert
  weiterhin die bestehenden Blog-Verlinkungen. Abschlussziel: Projektanfrage.
- **Weitere öffentliche Seiten:** `/impressum/`, `/datenschutz/`
  (Kontaktdaten aus dem Messaging-Canon).
- **Intern:** `page-wgos.php` ist ein geschütztes Kunden-Dashboard
  (`noindex, nofollow`); `wgos_asset`-Seiten unter `/wgos-assets/<slug>/` sind
  `noindex, follow`; das Kundenportal (`template-portal.php`) zeigt nur
  hinterlegte Daten; `/startseite-wow/` ist eine `noindex`-Testroute.

- **Seitenanlage:** Theme-eigene Seiten stehen in `nexus_get_provisioned_pages()`
  (`inc/helpers.php`); Kontakt, Glossar, WGOS-Hub und Cluster-Seiten haben eigene
  Funktionen in ihrer Datei. Seit 2026-09-26 laufen alle nur noch einmal je
  Deploy (`.nexus-deploy-sha`, Option `nexus_route_pages_stamp`) statt bei jedem
  ungecachten Aufruf. Eine im Editor gelöschte Seite kommt mit dem nächsten Deploy
  zurück, solange sie in der Liste steht. Prüfung: `npm run test:provisioning`.

## Anfragewege und CRM

- Repo-Korrektur vom 2026-09-25 (Live-Nachweis erst nach Deployment): Kontakt,
  Ersteinschätzung, White-Label, Marktcheck und Blog-Abo bestätigen im Browser
  Erfolg nur bei HTTP 2xx und einem JSON-Objekt mit `ok === true`. Ungültige
  Antworten erhalten die Eingaben. `NexusCore.submitJson()` begrenzt einen
  Versuch einschließlich Antwortkörper auf 30 Sekunden; ein Timeout bedeutet
  einen unbestätigten Eingang, keine nachgewiesene Ablehnung. Späte Antworten
  ändern die Oberfläche nicht mehr. Während des Versuchs sind Eingaben und
  Formularnavigation gesperrt; anschließend wieder bedienbar. Keine automatische
  Wiederholung und keine Zusage serverseitiger Einmalverarbeitung.
  Browser- und isolierte Endpoint-Prüfungen laufen in CI; Umfang und Grenzen:
  `scripts/tests/README.md`. Payload, Consent, Quellen, Segmente, Honeypot,
  Rate-Limits und der Versuch Ersteinschätzung bleiben unverändert.
- Öffentliche REST-Endpunkte unter `nexus/v1`, jeweils mit Honeypot und
  IP-Rate-Limit. Die IP liefert `nexus_get_review_request_ip()`: `REMOTE_ADDR`,
  hinter einem internen Proxy die rechteste öffentliche Adresse aus
  `X-Forwarded-For`.
  - `contact-request` (`inc/contact-page.php`): `/kontakt/` und das
    Server-Side-Formular. Bei `type=project` erscheint zuerst die Themenwahl
    (Vorhaben), der Agentur-Hinweis ist immer sichtbar, Budget ist optional.
    Anfragetyp `ersteinschaetzung` (Versuch): Website-URL Pflicht, Nachricht
    optional, Betreff-Präfix aus dem Kanon; der Endpoint nimmt ihn auch bei
    ausgeschaltetem Schalter an.
  - `whitelabel-request` (`inc/whitelabel-request.php`): legt einen
    CRM-Kontakt mit Quelle und Segment `whitelabel_request` und eine
    Sales-Chance an.
  - `audit-request` (`inc/review-crm.php`): Marktcheck, CPT
    `nexus_review_request`, Contract `2026-05-26.audit-request.v1`; die
    Qualifizierung (`qualified` oder `nurture`) setzt die Stufe der Sales-Chance.
  - Blog-Abo (`inc/blog-notify.php`) mit Double-Opt-in.
  - `analysis-submit` ist standardmäßig aus (`HU_FEATURE_READINESS_SUBMIT`),
    kein Formular sendet dorthin.
- Reihenfolge bei Kontakt und White-Label: CRM → interne Mail → Bestätigung.
  Ein Fehler 500 kommt nur, wenn CRM und interne Mail beide scheitern.
  Scheitert nur die Mail, erscheint ein Hinweis im Dashboard und in den
  CRM-Ansichten (Option `nexus_lead_notification_failures`, 7 Tage) und eine
  Aktivität im Verlauf.
- CRM: `nexus_contact` (Upsert pro E-Mail), `nexus_opportunity` mit den Stufen
  aus `nexus_get_crm_sales_stages()`, `nexus_crm_activity` als Verlauf. Jede
  Kontakt- und White-Label-Anfrage schreibt eine Aktivität `inbound_inquiry`
  mit Anfragetext und Herkunft, auch bei wiederholten Anfragen. Admin: Nexus
  CRM mit Vertrieb, Kommunikation, Projektanfragen, White-Label-Anfragen und
  Blog-Abos.
- Herkunft: cookiefrei aus der Browser-Session (`NexusCore`): Landing-, Einstiegs-,
  vorherige und Referrer-URL, Kampagnenquelle, Suchbegriff, `utm_medium`,
  `utm_campaign` und die optionale Frage, wie jemand aufmerksam wurde. Die
  Meta-Keys liefert `nexus_get_inquiry_attribution_meta()` in `inc/crm.php`.
- Antwortfrist-Wächter (`inc/crm-sales/watchdog.php`): stündlich am Event
  `nexus_crm_sales_followup_cron`. Sechs Werktagsstunden vor Ablauf der
  zugesagten Antwortzeit geht eine interne Sammelmail raus, wenn es seit Anlage
  weder eine ausgehende Mail über das CRM noch einen Stufenwechsel gab. Pro
  Sales-Chance einmal, nur für Chancen der letzten 14 Tage.
- Mail: `wp_mail` läuft über die Brevo-API (`pre_wp_mail`). Diagnose über
  `/wp-json/nexus/v1/mail-diagnostics` (Admin) und
  `/wp-json/nexus/v1/mail-diagnostics-public` (redigiert, ohne Fehlertext).
- Termine: Cal.com-Links (30 Minuten, White-Label-Fit-Gespräch) als Popup mit
  direktem Link als Rückfall.

## Messung

- Das Theme bindet weder GTM noch GA4 ein. `data-track-*`-Hooks bleiben an
  allen Conversion-Flächen; Skripte schreiben nur dann in `dataLayer`, wenn
  eines existiert. Das Absenden einer Anfrage zählt der Server (`anfrage_gesendet`
  in `inc/inquiry-events.php`, Admin-Seite „Anfrage-Eingänge“, letzte 90 Tage
  je Formular und `utm_source`); `contact.js` und die Produktseite der
  Anfrage-Website senden dafür keine Browser-Ereignisse mehr.
- Auswertung: Koko Analytics (Plugin, admin-owned, Tracking-Methode
  `fingerprint`, cookielos) und das SEO-Cockpit im Admin (Search Console per
  OAuth, Linkgraph, Lead-Attribution aus dem CRM). Das Cockpit besitzt zusätzlich
  einen optionalen DataForSEO-Market-Intelligence-Layer für externe Keyword-,
  Wettbewerber- und manuelle Live-SERP-Daten. Automatisch laufen nur gebündelte
  Labs-Abfragen im Wochenrhythmus; Organic-/Maps-Live-Checks erfordern einen
  expliziten Admin-Klick und erzeugen keinen Frontend-Footprint.
- Öffentliche Besuche setzen keine Cookies und schreiben nichts in
  `localStorage` oder `sessionStorage`; kein Cookie-Banner. Formulare lesen die
  Herkunft erst beim Absenden aus der Formularseite (Adresse, utm-Parameter,
  Referrer). Die Sitzungs-Herkunft über mehrere Seiten ist vorbereitet, aber
  nur mit Einwilligung aktiv (`window.huConsent`). Sticky-CTA und
  „Gefällt mir“ speichern nichts, das WordPress-Emoji-Skript ist im Frontend
  aus. Details: `docs/architecture/PRIVACY.md`.

## Kanon und Guards

- Fakten stehen nur in `blocksy-child/inc/canon/`: Antwortzeit und Kontakt
  (`messaging-canon.php`, `hu_response_promise()`, `HU_RESPONSE_HOURS`), Preise
  (`pricing-canon.php`), Fallzahlen (`e3-proof-canon.php`), Marktcheck
  (`diagnose-canon.php`), Marktzahlen und Referenzen. Getter werden ohne
  Literal-Fallback aufgerufen; diese Datei nennt bewusst keine Beträge.
- Die Antwortfrist in Mails (`nexus_compute_intake_response_deadline()`) nutzt
  dieselbe Konstante und überspringt Wochenenden.
- Guards: `scripts/canon-guard.sh` (repo-weit, Sperrliste in
  `scripts/canon-forbidden-values.txt`), `lint-canon-drift.sh`,
  `lint-e3-canon.sh`, `check-german-copy.sh`, `validate-architecture.sh`,
  beide Funnel-Smoke-Contracts, `smoke-dataforseo-market-contract.sh`, CSS-Audits,
  `lint-entity-crawler-signals.php` und PHPStan mit Baseline. Der Theme-Build führt den Kanon-Guard erneut aus.
- Vorher-Abschlussquote des Falls (Marktannahme, nicht gemessen): Oberflächen
  lesen nur `display_hedged` und nennen sie Annahme; ein Vorher-Nachher-Feld
  gibt es seit 2026-09-26 nicht mehr. Guard-Regel `e3-vorher-quote`.
- Tracking-Leiter: Name, Umfang, Preis und Lieferzeit der vier Stufen nur in
  `hu_tracking_product_ladder()`; `npm run test:pricing`
  (`scripts/tests/tracking-ladder.php`, CI) prüft Reihenfolge, steigende
  Preise, Server-Side-Preis als Stufe 2 und den Abstand zum White-Label-Preis.

## SEO und Crawler

- Das Theme ist die einzige Quelle für Title, Meta, Canonical, Robots und
  Schema; es gibt kein SEO-Plugin. Resolver in `inc/seo-meta.php`, Schema in
  `inc/org-schema.php`.
- `/robots.txt` und `/llms.txt` sind Theme-Routen. Die `llms.txt` im
  Repo-Root ist ein Abzug der Route (`lint-entity-crawler-signals.php
  --write-llms`), kein zweiter Pflegeort.
- Sitemap: `/wp-sitemap.xml`; ausgeschlossene und `noindex, follow`-Slugs
  zentral in `inc/seo-meta.php` und `inc/helpers.php`.
- Query-Ownership: `docs/seo/query-ownership.csv`, geprüft mit
  `agents/skills/seo-agent/scripts/intent-gate.sh`.
- og:image: Routen mit Theme-Kachel (`hu_get_route_social_image()`) gehen vor
  ACF-Feld und Beitragsbild. Standard für jede Seite ohne eigenes Bild ist seit
  2026-09-25 `assets/img/standard-og.jpg` (1200 × 630, JPG), nicht mehr das
  Porträt im Hochformat; kommt das Porträt noch als Seitenbild an, ersetzt die
  Kachel es ebenfalls. Alle Theme-Kacheln außer der Solar-Kachel erzeugt
  `scripts/build-og-images.py`.

## Barrierefreiheit

- Genau ein `main`-Landmark: Blocksy öffnet `<main id="main">`, Vorlagen
  rendern darin nur `<div>`-Wrapper. Einige Energie-Vorlagen beginnen bei
  `#primary`; der Skip-Link folgt dem Ziel (`hu_get_skip_link_target_id()`).
- Ein Skip-Link pro Seite („Zum Hauptinhalt springen“ aus
  `inc/accessibility-navigation.php`); Blocksys englischer Link wird entfernt.

## Weiterleitungen und entfernte Routen

- 301 auf `/solar-waermepumpen-leadgenerierung/#marktcheck`: `/system-diagnose/`,
  `/anfrage-system-analyse/`, `/readiness-diagnose/`, `/anfrage/`,
  `/growth-audit/`, `/audit/`, `/customer-journey-audit/`, `/360-audit/`,
  `/wordpress-tech-audit/`.
- Weitere 301: `/stack-solar/` → Energie-Money-Page, `/stack-agentur/` →
  `/whitelabel-retainer/`, `/meta-ads/` → Beitrag `meta-ads-fuer-b2b`,
  `/solar-leads-kaufen-lohnt-sich/` und `/photovoltaik-leads-tco-rechnung/` →
  `/solar-leads-kaufen-alternative/`, `/ergebnisse/`, `/case-studies/` und
  `/case-studies-e-commerce/` → `/case-study-solar-leadgenerierung/` (ein
  Schritt, Query-String bleibt), `/wordpress-agentur/` →
  `/wordpress-agentur-hannover/`, `/category/owned-leads/` →
  `/eigene-leadgenerierung-vs-portale/`.
- 301 von `/<beitrag>/<autoren-slug>/` auf den Beitrag
  (`inc/post-permalink-author-redirect.php`, geprüft mit `npm run test:permalinks`).
  Greift nur, solange die Permalink-Struktur kein `%author%` enthält.
- `410 Gone` (`nexus_get_retired_gone_paths()`): `/wordpress-seo-hannover/`,
  `/core-web-vitals/`, `/conversion-rate-optimization/`,
  `/wordpress-wartung-hannover/`, `/seo/`, `/kostenlose-tools/`, `/tools/`,
  `/website-performance-analyse/`, `/roi-rechner/`, `/audit-linkedin/`,
  `/shopify-wartungsvertrag/`.
- Alte WGOS-Pfade bleiben `noindex` bzw. geschützt, solange sie intern als
  Dashboard- oder Asset-Pfade existieren.

## Offen und manuell

- Permalinks stehen seit 2026-09-26 wieder auf „Beitragsname“ (vorher etwa
  01.–25.09. mit `%author%`). Live am 2026-09-26 geprüft: alle 16 Beiträge der
  Sitemap antworten mit 200 und eigenem Canonical, `/<beitrag>/hasim/` leitet
  per 301 zurück, Sitemap neu eingereicht. Google führte am selben Tag für
  `/checkfox-solar-waermepumpe-einordnung/` noch die `/hasim/`-Variante als
  indexiert (letzter Crawl vor der Korrektur). Offen: In der Search Console
  für `/checkfox-solar-waermepumpe-einordnung/`, `/aroundhome-solar-einordnung/`,
  `/wattfox-solar-leads-einordnung/` und `/daa-photovoltaik-leads-einordnung/`
  „Indexierung beantragen“; der nächste GSC-Export zeigt, ob die Impressionen
  zurückkommen.
- Nach dem Merge je eine Testanfrage über `/kontakt/`, `/whitelabel-retainer/`
  und den Marktcheck: CRM-Eintrag, Sales-Chance, interne Mail und Bestätigung
  prüfen.
- Versuch Ersteinschätzung: Zählung 2026-09-25 bis 2026-11-20, Entscheidung
  nach der Tabelle in `docs/experimente/ersteinschaetzung.md`. Die finale
  Fassung bildet eine eigene Beobachtungsphase; Einsendungen vor und nach dem
  Deploy getrennt auswerten. Nach dem Deploy eine Einsendung über
  `/kontakt/?focus=ersteinschaetzung` schicken (Betreff-Präfix bei
  kontakt@hasimuener.de prüfen) und die Wochentabelle führen.
- WP-Cron: Wegen des Seiten-Caches ist ein echter Server-Cron für `wp-cron.php`
  nötig, sonst laufen Follow-ups und Antwortfrist-Wächter nur bei
  Admin-Besuchen zuverlässig. Im Repo nicht prüfbar.
- WordPress-Admin: die Seiten zu `/stack-solar/` und `/stack-agentur/` löschen,
  falls noch vorhanden (sonst Sitemap-Einträge trotz 301).
- WordPress-Admin: die Seite `/ergebnisse/` auf Entwurf setzen, nicht löschen.
  Die 301 greift auch ohne diesen Schritt.
- Seitencache: Der Server-Cache (`x-cacheable: YES`) wird beim Deploy nicht
  geleert und lieferte `/whitelabel-retainer/` am 2026-09-25 rund 18 Stunden
  nach dem Deploy noch im alten Stand aus. Nach jedem Deploy mit sichtbarer
  Änderung invalidieren. Am 2026-10-01 nach PR 3/4 verifiziert: erneutes
  Speichern der Solar-Seite (15048) mit unverändertem Titel invalidiert ihren
  Varnish-Eintrag; danach normale URL mit `age: 0` / `MISS` und neuen Modulen.
  Ein zusätzlicher Query-Parameter umgeht den Cache, leert die normale URL
  aber nicht.
- Mediathek: `Featured_CaseStudy_E3_1200x627.webp` (Anhang 14765) ist noch
  Beitragsbild der Fallstudie (Seite 12092) und öffentlich abrufbar; der
  Seitentitel im Editor lautet „Case Studies- e3-new-energy“. Das Theme
  überschreibt den WebPage- und BreadcrumbList-Schema-Namen. Der Editor-Titel
  bleibt in der REST-API sichtbar. Über Löschen bzw. Umbenennen entscheidet Hasim.
- Partnerlinks aus `inc/affiliate-links.php` tragen `rel="sponsored"`, aber
  keinen sichtbaren Werbehinweis; zu prüfen.
- Browser- und Lighthouse-Abnahmen der jüngsten Seitenumbauten stehen aus,
  soweit sie in den Commits nicht als geprüft vermerkt sind.

## Nicht mehr Zielbild

- Growth Audit, System-Diagnose und kostenlose Tools als öffentliche
  Funnel-Stufen; der Marktcheck als sitewide CTA.
- Öffentliche Copy mit abgelösten Angebotsbegriffen (Sperrlisten in
  `docs/standards/BRAND_AND_COPY.md` und `scripts/lint-canon-drift.sh`) oder
  unbelegten Leistungszahlen; `Retainer` als kaufnaher Standardbegriff.
- Ein WordPress-Editor-Shell als Quelle für Funnel-Logik; lose Root-Ablagen für
  Playbooks und Entwürfe.
