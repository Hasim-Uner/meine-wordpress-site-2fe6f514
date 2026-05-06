# Live Status

Stand: 2026-05-05.

Basis dieses Status:

- Repo-Inhalt
- bestehende Deploy-Workflows
- vorhandene Template- und Funnel-Logik

Nicht verifiziert:

- exakte Live-Konfiguration in WordPress-Admin
- exakte n8n-, GTM-, GA4-, Consent- und CRM-Setups

## Live

- `blocksy-child/` ist der deploybare Website-Code.
- Das Theme laeuft jetzt ueber einen getrennten CI/CD-Pfad: `.github/workflows/ci.yml` prueft PHP-Syntax, Workflow-YAML und das deploybare Theme-Paket; `.github/workflows/deploy.yml` deployed erst nach erfolgreichem CI-Run fuer einen Push auf `main` oder optional manuell per Dry-Run bzw. `workflow_dispatch`.
- Zentrale Theme-Module fuer Assets, SEO-Meta, Schema, Shortcodes, Portal und Snippets sind versioniert.
- Die Product-Default-Logik führt kalten B2B-Traffic zur `/anfrage-system-analyse/`; die versionierte Homepage ist repo-seitig als SHK-Hauptlandingpage auf Anfrage-System-Analyse ausgerichtet, nutzt die EnergieFahrplan-Demo nur als sekundären Showroom-Pfad und führt E3-Proof-Zahlen über den zentralen Canon. Header, globaler Footer und wiederverwendbarer Footer-CTA priorisieren jetzt ebenfalls die Analyse; der wichtigste Proof-Link zeigt direkt auf `/e3-new-energy/` statt auf den breiten Ergebnisse-Hub. Technische Nebenpfade wie Core Web Vitals und kostenlose Tools sind aus der Footer-Navigation entfernt. `/wordpress-agentur-hannover/` bleibt als sekundärer Footer-Link für lokale Money-Page-/SEO-Signale erhalten; Whitelabel bleibt aus der globalen Navigation heraus.
- Die lokale Money Page `/wordpress-agentur-hannover/` folgt repo-seitig jetzt einem gestrafften CRO-Flow: Hero mit konservativem Proof, drei Problemkarten, gekürzte Vergleichstabelle, konkrete Leistungsbereiche statt 3-Step-Prozess, Fit-Check ohne Negativ-Absatz, E3-Proof, kurze Standort-Sektion für Hannover/DACH, reduzierte lokale FAQ, stärkere interne Anschlüsse zu SEO/GA4/CRO und bestehender finaler Audit-CTA.
- Die versionierte Über-mich-Seite `/uber-mich/` ist repo-seitig auf `Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter` ausgerichtet; Editor- und Template-Copy müssen bei der E3-Canon-Bereinigung gegen `150 € -> 22 €`, `-85,3 %`, `1.750+`, `12 %` und `9 Monate` geprüft werden.
- Service-Seiten, Tools-Hub, Blog, Kategorie-Hubs, Footer-CTA und Trust-Bausteine sind im Repo.
- Die `/anfrage-system-analyse/` ist repo-seitig das Makro-Ziel für kalten B2B-Traffic; der `Growth Audit` bleibt Legacy-/Sekundärpfad.
- Repo-seitig gibt es jetzt einen E3-Proof-Canon in `blocksy-child/inc/canon/e3-proof-canon.php`: `150 €` vorher, `22 €` nachher, `-85,3 %` Delta, `1.750+` qualifizierte Anfragen, `12 %` Abschlussquote und `9 Monate` Zeitraum.
- Der versionierte Ergebnisse-Hub bleibt als vorhandener Proof-Hub erreichbar, ist aber nicht mehr die primaere Proof-Fläche in Header oder Footer. Der kaufnahe Proof wird ueber `/e3-new-energy/` direkt verlinkt und nutzt die kanonischen E3-Zahlen aus `blocksy-child/inc/canon/e3-proof-canon.php`.
- Die aktive `Growth Audit`-Route `/growth-audit/` rendert repo-seitig jetzt einen shortcode-getriebenen Instant-Results-Flow: URL-Eingabe, asynchron gestartete 60-Sekunden-Diagnose mit Status-Polling, Hero-Trust fuer WordPress/B2B, Ergebnisvorschau vor dem ersten Klick, Ergebnis-Dashboard fuer Performance, Tracking, SEO, Content und je nach Payload ein als Orientierungswert gerahmtes Potenzial-Modul; der Ergebnis-CTA fuehrt direkt in den qualifizierten Solar-/Waermepumpen-Intake statt in Kontakt- oder Kalenderpfade.
- Der aktive Instant-Results-Flow auf `/growth-audit/` blendet Loading und Ergebnisse repo-seitig initial aus, scrollt bei Analyse-Start und Ergebniswechsel wieder an den App-Anfang, haelt im Results-State mehr Luft unter dem fixen Header, zeigt priorisierte Hebel mit Kontextkopf, rundet Potenzial-Metriken sichtbar auf ganze Werte, fuehrt die oeffentliche Timing-Copy jetzt konsistent mit `60 Sekunden`, rahmt den naechsten Schritt weicher als Einordnung statt Hart-Sales und startet n8n bevorzugt ueber `audit + audit-status`, damit erfolgreiche Jobs nicht mehr clientseitig nach 60 Sekunden abgebrochen werden.
- Der fixe Header auf `/growth-audit/` fuehrt im aktiven Instant-Results-Flow repo-seitig jetzt direkt den Hard-CTA `Anfrage stellen`; der redundante Sprunglink `Zur Analyse` ist entfernt und der Hero startet mit mehr Abstand unter dem Header.
- Der globale Theme-Header und der Blog-Header sind repo-seitig als Auto-Hide-Navigation ausgerichtet: beim Einstieg kurz sichtbar, danach innerhalb von ca. 2 Sekunden ausgeblendet, mit Reveal bei Aufwärts-Scroll, Fokus, offenem Mobilmenü oder Mausnähe am oberen Viewport-Rand. Routen, die den Header gar nicht rendern, bleiben davon unberührt.
- Die noindex-Kampagnenseite `/audit-linkedin/` bleibt als eigene LinkedIn-Landingpage im Theme hinterlegt und ist repo-seitig jetzt zusaetzlich als native WordPress-Seite mit Template-Zuweisung und festem Featured Image abgesichert.
- Repo-seitig liegt zusätzlich eine B2B-Branchen-Landingpage für Solar-, Wärmepumpen- und Speicher-Anbieter kanonisch unter `/solar-waermepumpen-leadgenerierung/`. Sie bleibt ein SEO- und Kontext-Zweig, führt ihre großen CTA-Flächen aber zur `/anfrage-system-analyse/` statt in einen eigenen Audit-, E-Mail- oder CRM-Intake.
- `page-wgos.php` ist repo-seitig jetzt als 8-Sektionen-WGOS-Page versioniert: Hero mit Audit- und Gespraechs-CTA, WGOS-Kurzform, inline Systemdiagramm, gestraffte Kernbereiche, frueher platzierte Pakete/Credits, kondensierter Proof, reduzierte FAQ und finaler CTA; die Sticky-Navigation fuehrt nur noch ueber System, Kernbereiche, Pakete, Wirkung und FAQ.
- Die wichtigsten Legacy-Clusterseiten für SEO, Core Web Vitals, CRO, GA4 und Performance Marketing sind jetzt als versionierte WGOS-Clusterseiten im Theme hinterlegt; die priorisierten Money-Cluster für `GA4 Tracking Setup` und `Conversion Rate Optimierung für WordPress` führen repo-seitig jetzt keyword-nähere H1-, Lead-, Meta- und FAQ-Signale.
- `inc/enqueue.php` nutzt für CSS- und JS-Assets jetzt in Live-Umgebungen eine statische Theme-Version und fällt nur in `local`/`development` auf `filemtime()` zurück; `/growth-audit/` lädt den neuen Instant-Results-Stack über den Shortcode `cja_audit` mit `cja-audit.css` und `cja-audit.js`, während die Branchen-Landingpage nur noch als SEO-/Kontextseite zur Anfrage-System-Analyse weiterleitet.
- Die WGOS-Clusterseiten zeigen im Proof-Block jetzt bewusst belastbare System-Beweise (`100 % B2B-Fokus`, `48h` Diagnosezeit, `3 Proof-Routen`) statt uebergreifend wiederholter Leistungszahlen, die als generischer Platzhalter gelesen werden koennten.
- Die kanonischen Cluster-Routen und die Kontaktseite `/kontakt/` werden repo-seitig jetzt zusaetzlich als veroeffentlichte WordPress-Seiten mit zugewiesenem Template abgesichert, damit oeffentliche URL, native Sitemap, Canonical-Logik und Admin-Aufloesung konsistent bleiben.
- Repo-seitige Primary-Links auf diese Clusterseiten zeigen jetzt direkt auf ihre kanonischen Routen und fallen nicht mehr auf kuerzere Legacy-Slugs wie `/seo/` zurueck.
- Ergebnisse laufen repo-seitig weiterhin kanonisch über `/ergebnisse/`; alte Proof-Slugs bleiben Legacy-Redirects. Für die Hauptnavigation ist `/e3-new-energy/` der direkte Proof-Link.
- Der aktive `Growth Audit`-Pfad nutzt jetzt einen privacy-first Shortcode-Flow ohne E-Mail-Pflicht: erst nach Klick auf `Jetzt analysieren` sendet das Frontend die bereinigte URL bevorzugt an `https://n8n.hasimuener.de/webhook/audit`, pollt danach `https://n8n.hasimuener.de/webhook/audit-status` und faellt nur bei Bedarf auf `https://n8n.hasimuener.de/webhook/cja-analyze` zurueck; Ergebnis, Header und Footer routen im Audit-Kontext auf den qualifizierten Anfragepfad zurueck.
- Direkte Gespraechs-CTAs fuer Audit-, Kontakt- und WGOS-Kontexte nutzen theme-seitig zentral `https://cal.com/hasim-uener/30min?overlayCalendar=true`; die Whitelabel-Seite nutzt fuer Partner- und Agentur-Fit jetzt zusaetzlich `https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true`.
- Diese Gespraechs-CTAs oeffnen bei verfuegbarem JavaScript ein eingebettetes Cal.com-Popup im Seitenkontext; der direkte Kalender-Link bleibt pro Event-Typ als robuster Fallback erhalten.
- Die Kontaktseite führt öffentlich jetzt über sechs saubere Anfragearten: `Erstdiagnose / Growth Audit`, `Fokussierte Folgeanalyse`, `Umsetzung / Optimierung`, `Laufende Weiterentwicklung`, `Allgemeine Anfrage` und `Bestehender Kunde`; die Frontend-Ausgabe von `/kontakt/` ist repo-seitig am nativen Theme-Template verankert und als kompakter Superflow reduziert. Neben-Routing, Social-Links und Erklär-Rail sind entfernt; Auswahlfragen springen automatisch weiter, `?type=implementation` startet direkt im passenden Thema und der bestehende REST-Submit-Vertrag bleibt unverändert.
- Die warme Anfrage-Seite `/anfrage/` ist repo-seitig ebenfalls auf Superflow ausgerichtet: kompakter Hero, kein vorgelagerter Founding-Cohort-/Proof-Block, keine Übersichts-Sidebar, Auto-Advance für Auswahlfragen und Submit erst im Kontakt-Schritt.
- Das Client Portal existiert technisch inklusive Login- und Upload-Logik.
- Repo-seitig existiert jetzt ein gemeinsames `Nexus CRM` fuer Audit-Anfragen, Projektanfragen und Blog-Abos; die Blog-Benachrichtigungen inkl. DOI, Abmeldung und manuell anstossbarem Artikelversand liegen im Theme.
- Repo-seitig existiert jetzt ein `SEO Cockpit` im WordPress-Admin: Search-Console-OAuth, Cache-Layer, Kernmetriken, Dashboard-Widget, Cron-Snapshot, optionaler Koko-Kontext und ein neuer Audit-Lead-Layer aus dem internen CRM sind im Theme vorbereitet.
- Das `SEO Cockpit` priorisiert repo-seitig jetzt nicht mehr nur nach Severity, sondern ueber eine business-aware Queue aus Page Role, Funnel-Naehe, Impressionen, Lead-Signal, Confidence und neuen Insight-Typen wie `Money Page Underperforming`, `Weak Funnel Bridge`, `Orphan Value Page` und `Indexing Mismatch`.
- Die Crawl-Signale für Search- und KI-Crawler werden repo-seitig jetzt über eigene Theme-Routen für `/robots.txt` und `/llms.txt` ausgeliefert: `robots.txt` antwortet als `text/plain` mit expliziten Regeln für OAI-SearchBot, GPTBot, ChatGPT-User, ClaudeBot und PerplexityBot; `llms.txt` bündelt Audit-, Service-, Proof- und Kontaktpfade in einem erweiterten Markdown-Signal.
- Nicht-kritische Theme-Skripte werden zentral ueber `inc/enqueue.php` mit `defer` ausgeliefert; `nexus-core-js` und `nexus-site-header-js` bleiben fuer unmittelbare Boot- und Header-Interaktionen ausgenommen.
- Zusaetzliche INP-Haertung: `nexus-core.js` verschiebt nicht-kritische Observer/TOC/Progress-Initialisierung auf `requestAnimationFrame` bzw. Idle-Zeit, der Header verarbeitet Scroll- und Pointer-Bewegungen nur noch RAF-gebuendelt und das Cal.com-Enhancement bindet Preload-Listener direkt an passende Buchungslinks statt global am gesamten Dokument.
- Zusaetzliche Homepage-Performance: die Font-Faces liegen jetzt direkt in `style.css` statt in einer separaten `fonts.css`-Anfrage, `related-content.css` und `footer-cta.css` laden nur noch auf Blog-Singles und die Homepage zieht die Core-Block-Library nicht mehr in den kritischen CSS-Pfad.
- Zusaetzliche Homepage-Font-Optimierung: auf der Startseite wird fuer Figtree jetzt gezielt `figtree-600.woff2` als kritischer Schnitt vorgeladen; gleichzeitig sind Floating-Nav, Hero-Badge, Hero-CTA, Hero-Textlink und der fruehe Proof-Strip typografisch auf leichtere Figtree-Gewichte reduziert, damit `figtree-700.woff2` nicht mehr fuer den ersten Viewport notwendig wird.
- Zusaetzliche Homepage-JS-Optimierung: die Startseite zwingt jetzt auch Parent-Theme- und Core-Handles (`ct-scripts`, `nexus-core-js`, `nexus-site-header-js`) in den `defer`-Pfad; `nexus-core.js` initialisiert Smart-Nav-Scroll-Spy spaeter und bevorzugt `IntersectionObserver`.
- Das globale Theme ist repo-seitig jetzt fest auf das dunkle Farbschema gesetzt; der fruehere Desktop-Hell/Dunkel-Toggle rendert nicht mehr und der Frontend-Boot setzt `data-theme` bzw. `data-nx-theme` konsistent auf `dark`.
- Repo-seitige Branding-Ausgabe fuer Logo- und Favicon-Signale priorisiert jetzt das im WordPress-Admin gesetzte `custom_logo` bzw. `site_icon`; die Theme-SVGs unter `assets/brand/` bleiben nur noch Fallback, damit Frontend-Head-Tags und Organization-Schema dieselbe Quelle nutzen.
- `/energie-fahrplan-demo/` ist repo-seitig als eingebettete Showroom-Demo vorbereitet und wird über `blocksy-child/inc/energy-demo-page.php` als virtuelle Route stabil ausgeliefert. Der globale Theme-Header wird auf dieser Route nicht gerendert, damit der Hero ungestört startet. Besucher füllen einen beispielhaften Energie-Funnel aus, erhalten eine lokale Ergebnisbühne mit PDF-Ausgabe und sehen eine Betreiber-Prozesskarte für Lead-Karte, CRM, n8n und serverseitiges Tracking. Hero- und Abschluss-CTA führen in die `/anfrage-system-analyse/`. Die Demo sendet keine E-Mail, erzeugt keinen CRM-Datensatz und startet keinen n8n-Workflow.

## In Arbeit

- `/anfrage-system-analyse/` ist repo-seitig als noindex React-Funnel-Form öffentlich aktiv (`HU_FEATURE_REQUEST_ANALYSIS_ROUTE=true`). `/readiness-diagnose/` wird als Legacy-Route per 301 weitergeleitet. Die App baut nach `blocksy-child/readiness/dist/` und führt lokal durch acht Diagnosekapitel mit Pflichtfeldprüfung, Fit-Scoring und Ergebnis grün/gelb/rot. Die UX läuft jetzt als kompakter Auto-Advance-Ein-Frage-Flow: Optionsantworten öffnen nach kurzer Auswahlbestätigung automatisch die nächste Frage, Buttons bleiben nur für Texteingaben und das lokale Ergebnis sichtbar. Der bestehende Submit-Contract `readiness-diagnosis-payload.v1` bleibt bis zur nächsten Contract-Version intern stabil; `HU_FEATURE_READINESS_SUBMIT=false` bleibt der Default. Es gibt keinen n8n-Submit, keinen CRM-Write und keine E-Mail-Abfrage im Default-Pfad.
- Das Repo wird gerade von einem Theme-Repo zu einem Website Operating System erweitert.
- `page-wgos.php` bleibt template-driven; eine spaetere Auslagerung des Content-Layers in Editor oder ACF ist weiter offen.
- WGOS Asset-Detailseiten laufen jetzt ueber den hierarchischen CPT `wgos_asset` mit ACF-Meta und Single-Template; die redaktionelle Befuellung der Spokes passiert weiter im WordPress-Admin.
- WGOS-Assets sind jetzt zusaetzlich als versionierte Registry im Theme definiert: 39 Assets stehen repo-seitig auf `publish`, inklusive 4 KI-/Automatisierungs-Assets im Kernbereich `Weiterentwicklung`.
- Die neue WGOS-Asset-Struktur rendert versionierte SEO-Meta, Related-Links und `Service`-Schema aus derselben Registry.
- Die WGOS Systemlandkarte wird jetzt bei Bedarf automatisch als Seite angelegt und ist repo-seitig auf einen kompakten 3-Block-Flow reduziert: Hero, Explorer/Library und kurzer Audit-CTA.
- Die eigentliche Post-Erstellung und Aktualisierung fuer WGOS-Assets haengt aktuell noch am neuen Theme-Sync und damit am naechsten Deployment auf die Live-Umgebung.
- Teile der Homepage- und Navigationslogik haengen noch an manuellen WordPress-Admin-Schritten oder editorgetriebenen Default-Seiten ausserhalb der route-forced Clusterpages.
- Editorgetriebene Seitentitel, Excerpts, Karten und `the_content()`-Bereiche koennen weiter alte Proof-, Tonalitaets- oder Du/Sie-Brueche enthalten und muessen im WordPress-Admin separat verifiziert werden.
- Blog-Artikel koennen jetzt theme-seitig passende WGOS-Assets als Anschlussblock ausgeben; weitere Post-Mappings bleiben ausbaubar.
- Der fruehere 48h-Intake fuer `/growth-audit/` liegt repo-seitig weiter als Legacy-Shell und CRM-Stack vor, ist aber nicht mehr der aktive Renderpfad dieser Route.
- CRM-, Mail- und Follow-up-Logik fuer den Audit sind jetzt im Theme sichtbar; `wp_mail`-basierte Transaktionsmails koennen repo-seitig zentral ueber die Brevo API geroutet werden, sobald die Live-Credentials ausserhalb des Repos gesetzt sind.
- Neue Audit-Anfragen speichern repo-seitig jetzt zusaetzlich Formular-Landingpage, ersten internen Einstieg, vorherige interne Seite und Referrer fuer kuenftige Lead-Attribution im SEO-Cockpit.
- Die Energie-/Branchen-Landingpage bleibt unter `/solar-waermepumpen-leadgenerierung/` als SEO-Zweig und Kontextseite bestehen; der alte Pfad `/website-fuer-solar-und-waermepumpen-anbieter/` wird weiter per Redirect auf den kanonischen Slug gefuehrt. Saemtliche grossen CTA-Flaechen der Seite fuehren jetzt zur `/anfrage-system-analyse/`; kein eigener E-Mail-, n8n- oder CRM-Submit ist dort Default.
- Das neue Blog-Notify-System ist repo-seitig implementiert, aber End-to-End auf der Live-Instanz noch nicht verifiziert.
- Das neue SEO-Cockpit ist repo-seitig implementiert, aber ohne echte Google-OAuth-Credentials und ohne installiertes Koko-Plugin noch nicht end-to-end verifiziert.
- Das SEO-Cockpit zählt interne Kontextlinks jetzt nicht mehr nur aus `post_content`, sondern zusätzlich aus den relevanten Laufzeit-Templates für Cluster-Seiten, Blog-Bridges, die Agentur-Seite und den Ergebnisse-Hub; zugleich sind forcierte SEO-Snippets für `/ergebnisse/` und ein keyword-first Title für `/wordpress-agentur-hannover/` im Theme hinterlegt.
- Der Solar-Hauptfunnel fuehrt repo-seitig nicht mehr ueber `/kontakt/`, `/wordpress-agentur-hannover/` oder `/whitelabel-retainer/`; diese Nebenpfade bleiben in isolierten Kontexten bzw. im Footer erreichbar, nicht in der primaeren Header- oder CTA-Logik.
- Kannibalisierungs-Hinweise im SEO-Cockpit zeigen jetzt direkt die staerksten konkurrierenden URLs mit Impressionen und Position an, statt nur auf den normalen URL-Drilldown der Primaer-URL zu verweisen.
- Das Client Portal arbeitet aktuell mit Mock-Daten und ist noch kein voll dokumentiertes Produktivsystem.
- KI-Erweiterung: 4 WGOS-Assets (KI-Assistent/Chatbot 30 Cr, KI-Lead-Qualifizierung 20 Cr, RAG-Wissenssuche 25 Cr, LLM-Workflow-Automatisierung 20 Cr) stehen repo-seitig jetzt auf `publish` und sind dem Weiterentwicklungs-Layer zugeordnet. Dachseite `/ki-integration-wordpress/` als versioniertes Page-Template (`page-ki-integration.php`) mit Service- und FAQPage-Schema bleibt im Repo; die Live-Synchronisation der Asset-Posts ueber den Theme-Sync ist noch nicht verifiziert.

## Geplant

- weitere n8n-Workflow-Exporte unter `automations/n8n/workflows/`
- weitere menschlich lesbare Workflow-Doku und Flow-Maps unter `automations/n8n/docs/` und `automations/n8n/flow-maps/`
- sauberer Prompt- und Agenten-Layer fuer wiederverwendbare Arbeitskontexte
- weitere Systemdoku fuer Tracking, CRM-Routing, Offer-Logik und CTA-Inventar
- Ausbau des SEO Cockpits um echte Koko-Daten, Page-Level-Korrelationen und spaetere URL-Inspection-Sichten
- weiterer Ausbau der n8n-Analyse inklusive dokumentierter Payload-Contracts, Monitoring und Workflow-Exporten
- Ausbau des Decision Logs fuer strukturelle Repo- und Systementscheidungen

## Deprecated

- Legacy-Slugs `/audit/`, `/customer-journey-audit/`, `/360-audit/` und `/wordpress-tech-audit/` werden auf `/growth-audit/` umgeleitet.
- Legacy-Slugs `/case-studies/` und `/case-studies-e-commerce/` werden auf `/ergebnisse/` umgeleitet.
- Legacy-Slugs `/meta-ads/`, `/seo/`, `/wordpress-agentur/` und `/roi-rechner/` werden auf ihre kanonischen Zielseiten umgeleitet.
- Der Legacy-Pfad `/alle-loesungen-im-detail/` wird auf die aktuelle Loesungs-Uebersicht umgeleitet.
- Die Clusterseiten `/ga4-tracking-setup/`, `/performance-marketing/` und `/wordpress-wartung-hannover/` bleiben aktive versionierte Routen und sind keine Legacy-Redirect-Ziele mehr.
- Im WordPress-Admin erscheint fuer Admins ein Legacy-Cleanup-Hinweis; der Ein-Klick-Flow setzt gefundene Altseiten auf `draft` und entfernt passende Menue-Eintraege.
- Der `Growth Audit` bleibt Legacy-/Sekundärpfad und ist nicht mehr das Makro-Ziel des Hauptfunnels. Der kaufnahe Einstieg für kalten B2B-Traffic läuft repo-seitig über die `/anfrage-system-analyse/`; vertiefte Folgeanalysen oder technische Integrationen ergeben sich erst danach.
- Oeffentliche Kontakt- und CTA-Texte mit `Pilotprojekt`, `Proof-of-Value`, `3.000+ Leads`, `34x ROAS` ohne Kontext oder `Retainer` als kaufnaher Standardbegriff sind nicht mehr Zielbild.
- Ein WordPress-Editor-Shell als Source of Truth fuer den Audit ist nicht mehr Zielbild.
- Lose Root-Ablage fuer Playbooks, Referenz-Snippets und Content-Drafts ist nicht mehr Zielstruktur.
