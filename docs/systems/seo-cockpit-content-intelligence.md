# SEO Cockpit – Content Intelligence V1.1

Stand: 2026-09-10.

## Zweck

Content Intelligence verbindet den bestehenden Research-Layer mit der Google Search Console. Das System bloggt nicht automatisch. Es erkennt belastbare Marktsignale und prüft anschließend, ob dafür tatsächlich direkte Suchnachfrage und eine passende bestehende Seite vorhanden sind.

Pipeline:

`Research-Provider -> persistierte Beobachtung -> Marktsignal -> Intent-Korridor -> GSC-Matching -> Seiten-Fit -> Content-Maßnahme`

## Datenquellen

V1.1 nutzt ausschließlich bereits vorhandene Provider:

- Fraunhofer ISE Energy-Charts
- Destatis GENESIS
- Eurostat

CrUX bleibt bewusst im technischen Research-/Performance-Layer und erzeugt keine Solar-/SHK-Content-Opportunity.

## Persistenz

Die begrenzte Historie liegt in der nicht automatisch geladenen WordPress-Option `nexus_content_intelligence_history_v1`.

Pro Metrik werden höchstens 36 unterschiedliche Zustände gespeichert. Identische Zustände werden per SHA-256-Fingerprint nicht erneut aufgenommen.

Gespeichert werden:

- Provider und Metrik
- Wert und Einheit
- Quellperiode
- Vergleichsmetadaten wie YoY-Wachstum oder Prozentpunkt-Differenz
- Capture-Zeitpunkt

Die Persistenz läuft nach dem vorhandenen Hook `nexus_seo_cockpit_research_background_refresh` mit Priorität 20. Externe Provider werden nie beim Rendern der Opportunities-Seite abgefragt.

## Signal Engine

Die Signal Engine bleibt regelbasiert.

Aktive Regeln:

- Energy-Charts: installierte PV-Leistung, Signal ab 3 % Veränderung
- Energy-Charts: Solaranteil der letzten 30 Tage, Signal ab 3 Prozentpunkten Veränderung
- Eurostat: Anteil erneuerbarer Energien Deutschland, Signal ab 0,5 Prozentpunkten
- Eurostat: Anteil erneuerbaren Stroms Deutschland, Signal ab 0,5 Prozentpunkten
- Destatis: neues Berichtsjahr für den Wohngebäudebestand, sobald zwei unterschiedliche Berichtsperioden historisiert sind

Day-Ahead-Preise werden historisiert, lösen aber noch keine automatische Content-Empfehlung aus.

## V1.1: Intent-Korridore

V1 hatte Solar-/PV-Begriffe zu breit gematcht. Dadurch konnten kommerzielle Queries wie `photovoltaik leads`, `pv termine b2b` oder Anbieter-Suchen einem Markt-/Daten-Signal zugeordnet werden.

V1.1 verlangt deshalb pro Signal zwei Komponenten gleichzeitig:

1. einen fachlichen Gegenstand, zum Beispiel `PV`, `Photovoltaik`, `Solar` oder `Erneuerbare`,
2. einen passenden Markt-/Daten-Kontext, zum Beispiel `Ausbau`, `Zubau`, `Leistung`, `Anteil`, `Strommix`, `Deutschland`, `Eurostat` oder `Statistik`.

Kommerzielle Lead-Intent-Begriffe wie `Lead`, `Termine`, `Anfragen`, `kaufen`, `Anbieter`, `B2B`, `Checkfox`, `Kosten` oder `Preis` werden für diese Marktsignale ausgeschlossen.

Ein Query muss einen Fit von mindestens 60/100 erreichen, bevor seine GSC-Daten in die Opportunity einfließen.

## Seiten-Fit

Eine rankende URL wird nicht mehr automatisch als Content-Ziel übernommen.

Der Seiten-Fit bewertet URL, WordPress-Titel, SEO-Titel und Description gegen denselben Themenkorridor. Kommerzielle Lead-Ausrichtung reduziert den Fit deutlich.

Das verhindert zum Beispiel, dass die zentrale B2B-Solar-Leadseite automatisch als Ziel für einen Eurostat-Strommix-Artikel vorgeschlagen wird, nur weil sie für allgemeine PV-Queries Impressionen erhält.

## Drei getrennte Scores

V1.1 zeigt keine künstliche Gesamtpriorität mehr, sondern drei getrennte Dimensionen:

### Marktsignal 0–100

Bewertet Stärke der Veränderung, Aktualität, Business-Relevanz und Datenqualität.

### SEO-Chance 0–100

Bewertet ausschließlich direkt passende GSC-Impressionen, Rankingposition und Zahl der passenden Queries.

### Bestehender Content-Fit 0–100

Bewertet, ob die aktuell rankende Seite fachlich wirklich zum Marktsignal passt.

Diese Trennung ist absichtlich: Ein wichtiges Marktsignal kann eine geringe SEO-Chance haben; eine gute SEO-Chance kann gleichzeitig ohne passende bestehende Zielseite auftreten.

## Maßnahmen

V1.1 erzeugt konservative Zustände:

- `Marktbeobachtung`: Signal relevant, aber keine ausreichende direkte Suchnachfrage
- `Passende Seite aktualisieren`: direkte Nachfrage plus guter bestehender Seiten-Fit
- `Passende Seite erweitern`: gute Nachfrage, guter Seiten-Fit und Ranking-Potenzial
- `Neue Analyse prüfen`: direkte Nachfrage vorhanden, aber keine ausreichend passende bestehende Seite

`Neue Analyse prüfen` ist noch keine automatische CREATE-Anweisung. Vor einer neuen URL muss Kannibalisierung geprüft werden.

## Admin UI

Unter `SEO Cockpit -> Opportunities` werden angezeigt:

- Snapshots
- aktive Marktsignale
- SEO-Chancen >= 60
- konkrete Maßnahmen
- pro Signal die drei getrennten Scores
- Intent-Korridor
- nur direkt passende GSC-Queries
- aktuell rankende URL mit Kennzeichnung, ob sie wirklich als Ziel bestätigt ist

## Refresh

Der explizite Button `Research aktualisieren` nutzt den manuellen, authentifizierten Refresh-Pfad und ist nicht von einem sofort laufenden WP-Cron abhängig. Die normale Research-Seite bleibt weiterhin asynchron.

## Sicherheits- und Qualitätsgrenzen

- Admin-/Background-only
- keine Frontend-Requests
- keine neuen externen APIs
- keine LLM-Abhängigkeit
- keine erfundenen Prognosen
- keine automatische Artikelgenerierung
- keine automatische Veröffentlichung
- keine Content-Empfehlung ohne GSC-Kontext
- vorhandene Research-Caches und Provider-Fehlerlogik bleiben maßgeblich

## Nächste Stufe

Nach Prüfung der realen V1.1-Ausgaben kann V1.2/V2 ergänzen:

1. Signal-Status wie erledigt/ignoriert
2. Content-Freshness-Mapping auf konkrete Textstellen bestehender Artikel
3. Briefing-Generator aus Primärdaten + GSC + internen Links
4. optional LLM für Briefings/Entwürfe hinter manueller Freigabe
5. Benachrichtigung nur bei neuem Signal oberhalb definierter Schwellen

Code:

- `blocksy-child/inc/seo-cockpit/seo-cockpit-content-intelligence.php`
- `blocksy-child/inc/seo-cockpit/seo-cockpit-content-intelligence-refresh.php`
- `blocksy-child/inc/seo-cockpit/seo-cockpit-content-intelligence-v11.php`
