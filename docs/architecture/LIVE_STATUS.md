# Live Status

Stand: 2026-05-13.

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
- Die Product-Default-Logik führt kalten B2B-Traffic zur `/system-diagnose/`; die versionierte Homepage ist repo-seitig als SHK-Hauptlandingpage auf System-Diagnose ausgerichtet und führt E3-Proof-Zahlen über den zentralen Canon. Der Homepage-Hero verzichtet jetzt auf ein künstlich wirkendes Fotomotiv und zeigt stattdessen ein codegebautes Anfrage-System-Bild: Marktchaos, Analyse, eigenes System und qualifizierte Anfrage in einem ersten Viewport. Header, globaler Footer und wiederverwendbarer Footer-CTA priorisieren jetzt ebenfalls die Analyse. Der wichtigste Proof-Link zeigt direkt auf `/e3-new-energy/` statt auf den breiten Ergebnisse-Hub. Technische Nebenpfade wie Core Web Vitals und kostenlose Tools sind aus der Footer-Navigation entfernt. `/wordpress-agentur-hannover/` bleibt als sekundärer Footer-Link für lokale Money-Page-/SEO-Signale erhalten; Whitelabel bleibt aus der globalen Navigation heraus.
- Die lokale Money Page `/wordpress-agentur-hannover/` folgt repo-seitig jetzt einem gestrafften CRO-Flow: Hero mit konservativem Proof, drei Problemkarten, gekürzte Vergleichstabelle, konkrete Leistungsbereiche statt 3-Step-Prozess, Fit-Check ohne Negativ-Absatz, E3-Proof, kurze Standort-Sektion für Hannover/DACH, reduzierte lokale FAQ, stärkere interne Anschlüsse zu SEO/GA4/CRO und bestehender finaler Audit-CTA.
- Die versionierte Über-mich-Seite `/uber-mich/` ist repo-seitig auf `Anfrage-Systeme für Solar- und Wärmepumpen-Anbieter` ausgerichtet; Editor- und Template-Copy müssen bei der E3-Canon-Bereinigung gegen `150 € -> 22 €`, `über 85 %`, `1.750+`, `12 %` und `9 Monate` geprüft werden.
- Service-Seiten, Blog, Kategorie-Hubs, Footer-CTA und Trust-Bausteine sind im Repo. Der frühere Tools-Hub bleibt als Legacy-Code erhalten, wird öffentlich aber nicht mehr gerendert.
- Die `/system-diagnose/` ist repo-seitig das Makro-Ziel für kalten B2B-Traffic; der `Growth Audit`, die kostenlosen Tools und `/anfrage/` leiten als retired Pfade per 301 auf die Analyse.
- Repo-seitig gibt es jetzt einen E3-Proof-Canon in `blocksy-child/inc/canon/e3-proof-canon.php`: `150 €` vorher, `22 €` nachher, `über 85 %` Delta, `1.750+` qualifizierte Anfragen, `12 %` Abschlussquote und `9 Monate` Zeitraum.
- Der versionierte Ergebnisse-Hub bleibt als vorhandener Proof-Hub erreichbar, ist aber nicht mehr die primaere Proof-Fläche in Header oder Footer. Der kaufnahe Proof wird ueber `/e3-new-energy/` direkt verlinkt und nutzt die kanonischen E3-Zahlen aus `blocksy-child/inc/canon/e3-proof-canon.php`.
- Die frühere `Growth Audit`-Route `/growth-audit/` ist öffentlich deaktiviert und leitet per 301 auf `/system-diagnose/`. Dasselbe gilt für die alten Audit-Aliasse und die kostenlosen Tool-Pfade.
- Der globale Theme-Header und der Blog-Header sind repo-seitig als Auto-Hide-Navigation ausgerichtet: beim Einstieg kurz sichtbar, danach innerhalb von ca. 2 Sekunden ausgeblendet, mit Reveal bei Aufwärts-Scroll, Fokus, offenem Mobilmenü oder Mausnähe am oberen Viewport-Rand. Routen, die den Header gar nicht rendern, bleiben davon unberührt.
- Die noindex-Kampagnenseite `/audit-linkedin/` bleibt als eigene LinkedIn-Landingpage im Theme hinterlegt und ist repo-seitig jetzt zusaetzlich als native WordPress-Seite mit Template-Zuweisung und festem Featured Image abgesichert.
- Repo-seitig liegt zusätzlich eine B2B-Branchen-Landingpage für Solar-, Wärmepumpen- und Speicher-Anbieter kanonisch unter `/solar-waermepumpen-leadgenerierung/`. Sie bleibt ein SEO- und Kontext-Zweig, führt ihre großen CTA-Flächen aber zur `/system-diagnose/` statt in einen eigenen Audit-, E-Mail- oder CRM-Intake.
- `page-wgos.php` ist repo-seitig jetzt als 8-Sektionen-WGOS-Page versioniert: Hero mit Audit- und Gespraechs-CTA, WGOS-Kurzform, inline Systemdiagramm, gestraffte Kernbereiche, frueher platzierte Pakete/Credits, kondensierter Proof, reduzierte FAQ und finaler CTA; die Sticky-Navigation fuehrt nur noch ueber System, Kernbereiche, Pakete, Wirkung und FAQ.
- Die wichtigsten Legacy-Clusterseiten für SEO, Core Web Vitals, CRO, GA4 und Performance Marketing sind jetzt als versionierte WGOS-Clusterseiten im Theme hinterlegt; die priorisierten Money-Cluster für `GA4 Tracking Setup` und `Conversion Rate Optimierung für WordPress` führen repo-seitig jetzt keyword-nähere H1-, Lead-, Meta- und FAQ-Signale.
- `inc/enqueue.php` nutzt für CSS- und JS-Assets jetzt in Live-Umgebungen eine statische Theme-Version und fällt nur in `local`/`development` auf `filemtime()` zurück; der alte Instant-Results-Stack bleibt als Legacy-Code vorhanden, wird auf `/growth-audit/` aber durch den 301-Redirect nicht mehr öffentlich geladen.
- Die WGOS-Clusterseiten zeigen im Proof-Block jetzt bewusst belastbare System-Beweise (`100 % B2B-Fokus`, `48h` Diagnosezeit, `3 Proof-Routen`) statt uebergreifend wiederholter Leistungszahlen, die als generischer Platzhalter gelesen werden koennten.
- Die kanonischen Cluster-Routen und die Kontaktseite `/kontakt/` werden repo-seitig jetzt zusaetzlich als veroeffentlichte WordPress-Seiten mit zugewiesenem Template abgesichert, damit oeffentliche URL, native Sitemap, Canonical-Logik und Admin-Aufloesung konsistent bleiben.
- Repo-seitige Primary-Links auf diese Clusterseiten zeigen jetzt direkt auf ihre kanonischen Routen und fallen nicht mehr auf kuerzere Legacy-Slugs wie `/seo/` zurueck.
- Ergebnisse laufen repo-seitig weiterhin kanonisch über `/ergebnisse/`; alte Proof-Slugs bleiben Legacy-Redirects. Für die Hauptnavigation ist `/e3-new-energy/` der direkte Proof-Link.
- Der frühere `Growth Audit`-Shortcode-Flow bleibt repo-seitig als Legacy-Code vorhanden, ist aber nicht mehr der öffentliche Einstieg. Neue kalte Besucher laufen über `/system-diagnose/`.
- Direkte Gespraechs-CTAs fuer Audit-, Kontakt- und WGOS-Kontexte nutzen theme-seitig zentral `https://cal.com/hasim-uener/30min?overlayCalendar=true`; die Whitelabel-Seite nutzt fuer Partner- und Agentur-Fit jetzt zusaetzlich `https://cal.com/hasim-uener/whitelabel-fit-gesprach?overlayCalendar=true`.
- Diese Gespraechs-CTAs oeffnen bei verfuegbarem JavaScript ein eingebettetes Cal.com-Popup im Seitenkontext; der direkte Kalender-Link bleibt pro Event-Typ als robuster Fallback erhalten.
- Die Kontaktseite führt öffentlich jetzt nur noch über drei kaufnahe Anfragearten: `System-Diagnose`, `Umsetzung` und `Weiterentwicklung`; Bestandskunden und allgemeine Rückfragen werden nicht mehr als Hauptauswahl gerendert. Die Frontend-Ausgabe von `/kontakt/` ist repo-seitig am nativen Theme-Template verankert und als kompakter Superflow reduziert. Neben-Routing, Social-Links und Erklär-Rail sind entfernt; Auswahlfragen springen automatisch weiter, `?type=implementation` startet direkt im passenden Thema und der bestehende REST-Submit-Vertrag bleibt unverändert.
- Der frühere warme Anfrage-Intake `/anfrage/` rendert kein eigenes Formular mehr. Repo-seitig ist `page-anfrage.php` nur noch ein Redirect-Template auf `/system-diagnose/`; die alte `energy-intake.js`-Strecke und der dedizierte `/anfrage/`-CSS-Block sind aus dem Deploy-Code entfernt.
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
- Der frühere EnergieFahrplan-Showroom ist repo-seitig aus dem Funnel entfernt. Beweisführung läuft über E3, Methodik und die direkte Achse Landingpage -> Branchen-Seite -> System-Diagnose.

## In Arbeit

- `/system-diagnose/` ist repo-seitig als noindex React-Funnel-Form öffentlich aktiv (`HU_FEATURE_REQUEST_ANALYSIS_ROUTE=true`). `/readiness-diagnose/` wird als Legacy-Route per 301 weitergeleitet. Die App baut nach `blocksy-child/readiness/dist/` und führt lokal durch acht Diagnosekapitel mit Pflichtfeldprüfung, Fit-Scoring und Ergebnis grün/gelb/rot. Die UX läuft als kompakter Auto-Advance-Ein-Frage-Flow: Optionsantworten öffnen nach kurzer Auswahlbestätigung automatisch die nächste Frage, Buttons bleiben nur für Texteingaben und das lokale Ergebnis sichtbar. Der Kontakt-Schritt ist über `/wp-json/nexus/v1/analysis-submit` aktiv (`HU_FEATURE_READINESS_SUBMIT=true`): Nach Einwilligung werden Name, Firma, E-Mail, Ergebnis und Antworten in `nexus_contact` mit Segment `analysis_lead` gespeichert; die zentrale Brevo-/`wp_mail`-Schicht versendet Admin-Benachrichtigung und Lead-Bestätigung. n8n ist für diese Route weiterhin nicht angebunden. Live-Verifikation am 2026-05-07: Route `HTTP 200`, REST-`POST` validiert, öffentlicher Mail-Diagnose-Endpunkt meldet Brevo API aktiv.
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
- Der frühere 48h-Intake und der Instant-Results-Flow für `/growth-audit/` liegen repo-seitig weiter als Legacy-Code vor, sind aber nicht mehr der aktive Renderpfad dieser Route.
- CRM-, Mail- und Follow-up-Logik fuer den Audit sind jetzt im Theme sichtbar; `wp_mail`-basierte Transaktionsmails koennen repo-seitig zentral ueber die Brevo API geroutet werden, sobald die Live-Credentials ausserhalb des Repos gesetzt sind.
- Neue Audit-Anfragen speichern repo-seitig jetzt zusaetzlich Formular-Landingpage, ersten internen Einstieg, vorherige interne Seite und Referrer fuer kuenftige Lead-Attribution im SEO-Cockpit.
- Die Energie-/Branchen-Landingpage bleibt unter `/solar-waermepumpen-leadgenerierung/` als SEO-Zweig und Kontextseite bestehen; der alte Pfad `/website-fuer-solar-und-waermepumpen-anbieter/` wird weiter per Redirect auf den kanonischen Slug gefuehrt. Saemtliche grossen CTA-Flaechen der Seite fuehren jetzt zur `/system-diagnose/`; kein eigener E-Mail-, n8n- oder CRM-Submit ist dort Default.
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

- Legacy-Slugs `/growth-audit/`, `/audit/`, `/customer-journey-audit/`, `/360-audit/` und `/wordpress-tech-audit/` werden auf `/system-diagnose/` umgeleitet.
- Legacy-Slugs `/case-studies/` und `/case-studies-e-commerce/` werden auf `/ergebnisse/` umgeleitet.
- Legacy-Slugs `/meta-ads/`, `/seo/`, `/wordpress-agentur/`, `/roi-rechner/`, `/kostenlose-tools/`, `/tools/` und `/website-performance-analyse/` werden auf ihre kanonischen Zielseiten umgeleitet.
- Der Legacy-Pfad `/alle-loesungen-im-detail/` wird auf die aktuelle Loesungs-Uebersicht umgeleitet.
- Die Clusterseiten `/ga4-tracking-setup/`, `/performance-marketing/` und `/wordpress-wartung-hannover/` bleiben aktive versionierte Routen und sind keine Legacy-Redirect-Ziele mehr.
- Im WordPress-Admin erscheint fuer Admins ein Legacy-Cleanup-Hinweis; der Ein-Klick-Flow setzt gefundene Altseiten auf `draft` und entfernt passende Menue-Eintraege.
- Der `Growth Audit` und die kostenlosen Tools sind nicht mehr öffentliche Funnel-Stufen. Der kaufnahe Einstieg für kalten B2B-Traffic läuft repo-seitig über die `/system-diagnose/`; vertiefte Folgeanalysen oder technische Integrationen ergeben sich erst danach.
- Oeffentliche Kontakt- und CTA-Texte mit `Pilotprojekt`, `Proof-of-Value`, `3.000+ Leads`, `34x ROAS` ohne Kontext oder `Retainer` als kaufnaher Standardbegriff sind nicht mehr Zielbild.
- Ein WordPress-Editor-Shell als Source of Truth fuer den Audit ist nicht mehr Zielbild.
- Lose Root-Ablage fuer Playbooks, Referenz-Snippets und Content-Drafts ist nicht mehr Zielstruktur.
