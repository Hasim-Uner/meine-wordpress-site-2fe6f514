# SEO Cockpit Research – Eurostat

Stand: 2026-09-04.

## Zweck

Eurostat ist der vierte Primärdaten-Provider im Admin-Bereich `SEO Cockpit -> Research`. Er ergänzt CrUX, Energy-Charts und Destatis um kostenlose EU-Vergleichsdaten.

Die Integration ist Admin-/Background-only. Es gibt keine Frontend-Requests, keine Cookies und keine öffentliche Proxy-Route.

## Quelle und Zugang

Provider: Eurostat, Statistics API.

API-Basis:

`https://ec.europa.eu/eurostat/api/dissemination/statistics/1.0/data`

Der Zugang ist öffentlich und kostenlos. Es ist kein API-Key und kein OAuth-Client erforderlich. Eurostat liefert die Statistics-API als REST-Service in JSON-stat 2.0.

## V1-Datensatz

Eurostat-Datensatz:

`nrg_ind_ren`

V1 fragt bewusst nur vier kleine Signale ab:

1. Deutschland – Anteil erneuerbarer Energien am Bruttoendenergieverbrauch (`geo=DE`, `nrg_bal=REN`)
2. EU27 – Anteil erneuerbarer Energien am Bruttoendenergieverbrauch (`geo=EU27_2020`, `nrg_bal=REN`)
3. Deutschland – Anteil erneuerbaren Stroms am Bruttostromverbrauch (`geo=DE`, `nrg_bal=REN_ELC`)
4. EU27 – Anteil erneuerbaren Stroms am Bruttostromverbrauch (`geo=EU27_2020`, `nrg_bal=REN_ELC`)

Gemeinsame Filter:

- `freq=A`
- `unit=PC`
- `lastTimePeriod=2`
- `format=JSON`

Aus den letzten zwei verfügbaren Perioden wird zusätzlich die Veränderung in Prozentpunkten berechnet.

## JSON-stat-Sicherheit

Die Abfragen filtern jede Nicht-Zeit-Dimension auf genau eine Position. Der Parser akzeptiert die Zeitreihe nur, wenn die Response diese Singleton-Struktur bestätigt. Dadurch wird verhindert, dass mehrere Länder, Einheiten oder Indikatoren versehentlich als eine lineare Zeitreihe interpretiert werden.

Fehlende oder nichtnumerische Werte werden nicht als Null interpretiert.

## Caching

Jedes der vier Eurostat-Signale wird 12 Stunden gecacht.

Der Button `Eurostat neu laden` löscht ausschließlich die vier Eurostat-Transients und lädt die Research-Seite neu.

## UI

Der Eurostat-Layer zeigt vier Karten:

- Deutschland · Erneuerbare gesamt
- EU27 · Erneuerbare gesamt
- Deutschland · Erneuerbarer Strom
- EU27 · Erneuerbarer Strom

Die Werte sind Markt- und Recherchekontext. Sie werden nicht als SEO-Score, Lead-Score oder direkte Nachfrage interpretiert.

## Code-Orte

- Provider und Panel: `blocksy-child/inc/seo-cockpit/seo-cockpit-research-eurostat.php`
- Research-Renderer: `blocksy-child/inc/seo-cockpit/seo-cockpit-research-v3.php`
- Loader: `blocksy-child/inc/seo-cockpit/seo-cockpit.php`

## Noch nicht enthalten

- keine automatische Content-Erstellung
- keine automatische Ableitung von Nachfrage aus Eurostat
- keine Gebäudedaten-Dopplung zu Destatis
- keine frei konfigurierbaren Eurostat-Datensätze im Admin
- keine Länder-Ranglisten oder Vollimporte

Der nächste fachliche Schritt ist nicht mehr ein weiterer Datenprovider, sondern die Verbindung von GSC-/WordPress-Signalen mit den Research-Dossiers im Opportunity Engine.
