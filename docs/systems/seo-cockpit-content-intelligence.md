# SEO Cockpit – Content Intelligence V1

Stand: 2026-09-10.

## Zweck

Content Intelligence verbindet den bestehenden Research-Layer mit der bereits angebundenen Google Search Console. Das System soll nicht automatisch bloggen, sondern belastbare Marktsignale erkennen und daraus nur dann eine Content-Maßnahme ableiten, wenn zusätzlich Search-Console-Nachfrage vorhanden ist.

Pipeline:

`Research-Provider -> persistierte Beobachtung -> deterministisches Marktsignal -> GSC-Matching -> Content-Opportunity`

## Datenquellen

V1 nutzt ausschließlich bereits vorhandene Provider:

- Fraunhofer ISE Energy-Charts
- Destatis GENESIS
- Eurostat

CrUX bleibt bewusst im technischen Research-/Performance-Layer und erzeugt in V1 keine Solar-/SHK-Content-Opportunity.

## Persistenz

Neue Tabelle: `{prefix}nexus_research_snapshots`.

Gespeichert werden nur normalisierte numerische Beobachtungen mit:

- Provider
- Metric-Key
- Wert und Einheit
- Quellperiode
- Quell-Update
- Vergleichsmetadaten wie YoY-Wachstum oder Prozentpunkt-Differenz
- Capture-Zeitpunkt
- SHA-256-Fingerprint zur Deduplizierung identischer Beobachtungen

Retention: 730 Tage.

Die Persistenz läuft nach dem vorhandenen Hook `nexus_seo_cockpit_research_background_refresh` mit Priorität 20. Dadurch werden keine externen Provider während des Admin-Renderings abgefragt.

## Signal Engine

V1 ist regelbasiert, nicht KI-basiert.

Aktive Regeln:

- Energy-Charts: installierte PV-Leistung, Signal ab 3 % Veränderung
- Energy-Charts: Solaranteil der letzten 30 Tage, Signal ab 3 Prozentpunkten Veränderung
- Eurostat: Anteil erneuerbarer Energien Deutschland, Signal ab 0,5 Prozentpunkten
- Eurostat: Anteil erneuerbaren Stroms Deutschland, Signal ab 0,5 Prozentpunkten
- Destatis: neues Berichtsjahr für den Wohngebäudebestand, sobald zwei unterschiedliche Berichtsperioden historisiert sind

Day-Ahead-Preise werden bereits historisiert, lösen in V1 aber bewusst keine automatische Content-Empfehlung aus.

## Search-Console-Matching

Die Opportunities-Seite verwendet ausschließlich den vorhandenen gecachten 28-Tage-GSC-Snapshot. Sie löst keine Live-Abfrage der Search Console aus.

Für jedes Marktsignal werden passende Query-/Page-Zeilen anhand eines konservativen Branchen-Keywordsets gruppiert. Nicht-Ziel-Queries aus der bestehenden SEO-Cockpit-Logik bleiben ausgeschlossen.

Ermittelt werden:

- relevante Impressionen
- passende URLs
- stärkste bestehende URL
- impressionsgewichtete Durchschnittsposition
- Top-Queries
- Anteil der stärksten URL an der passenden Nachfrage

## Maßnahmen

V1 erzeugt vier konservative Zustände:

- `Beobachten`: reales Marktsignal, aber zu wenig GSC-Nachfrage
- `Seite aktualisieren`: eine vorhandene URL ist bereits das klare Ziel
- `Seite erweitern`: vorhandene URL bündelt Nachfrage, hat aber Ranking-Potenzial
- `Eigene Seite prüfen`: Nachfrage verteilt sich auf mehrere URLs; vor einer neuen URL muss Kannibalisierung ausgeschlossen werden

Es gibt absichtlich kein automatisches `CREATE` ohne Prüfung.

## Prioritätsscore

Maximal 100 Punkte:

- Marktstärke: 35
- Aktualität: 15
- Business-Relevanz: 25
- Search-Console-Chance: 20
- Datenqualität: 5

Der Score priorisiert Arbeit. Er ist keine Aussage über Marktqualität, Leadqualität oder einen garantierten SEO-Effekt.

## Admin UI

Neues Untermenü:

`SEO Cockpit -> Opportunities`

Die Seite zeigt:

- Anzahl persistierter Beobachtungen
- aktive Marktsignale
- Opportunities mit Score >= 70
- Opportunities mit belastbarer GSC-Nachfrage
- pro Signal: Primärdatenwert, Veränderung, Empfehlung, Ziel-URL und passende Queries

Der Button `Research aktualisieren` reiht den vorhandenen Background-Refresh ein. Nach dem Provider-Refresh wird automatisch ein neuer Snapshot aufgenommen.

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

Nach realer Beobachtung der V1-Ausgaben kann V2 ergänzen:

1. Signal-Status wie erledigt/ignoriert
2. Content-Freshness-Mapping auf konkrete bestehende Artikel
3. Briefing-Generator aus Primärdaten + GSC + internen Links
4. optional LLM für Briefings/Entwürfe hinter manueller Freigabe
5. Benachrichtigung nur bei neuem Signal oberhalb eines definierten Scores

Code: `blocksy-child/inc/seo-cockpit/seo-cockpit-content-intelligence.php`.
