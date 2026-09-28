# SEO Cockpit · Market Intelligence

Stand: 2026-09-28.

## Ziel

Market Intelligence ergänzt das SEO Cockpit um die Außenperspektive auf den Suchmarkt. Google Search Console bleibt die Quelle für reale Impressionen, Klicks und Positionen der eigenen Property. Nexus CRM bleibt die Quelle für Anfragen und Pipeline-Signale. DataForSEO liefert ergänzend Suchvolumen, Wettbewerber, Keyword Difficulty und punktuelle Live-SERPs.

Die Datenquelle entscheidet nicht über Prioritäten. Die Priorisierung bleibt repo-owned und verbindet externe Marktdaten mit First-Party-Signalen.

## Architektur

Code:

- `blocksy-child/inc/seo-cockpit/seo-cockpit-dataforseo-client.php`
  - Credentials
  - HTTP-Client
  - Fehlerbehandlung
  - Kostenledger und Auto-Budget
- `blocksy-child/inc/seo-cockpit/seo-cockpit-market-intelligence.php`
  - DataForSEO-Labs-Provider
  - Snapshot und Verlauf
  - wöchentlicher Refresh
  - manuelle Live-SERP-/Maps-Watchlist
  - deterministic Opportunity Engine
- `blocksy-child/inc/seo-cockpit/seo-cockpit-market-ui.php`
  - Untermenü `Markt & Wettbewerb`
  - Provider-Konfiguration
  - Keyword Radar
  - Competitive Landscape
  - Live Watch
  - kompakter Dashboard-Pulse
- `blocksy-child/assets/css/seo-cockpit-market.css`
  - additive Admin-Gestaltung
- `scripts/smoke-dataforseo-market-contract.sh`
  - statischer Architektur- und Endpoint-Contract

Die Module werden nur hinter dem bestehenden Admin-/Background-Early-Return des SEO-Cockpits geladen. Normale Frontend-Aufrufe lösen keine DataForSEO-Requests aus.

## Credentials

Produktion bevorzugt Runtime-Konstanten:

```php
define( 'NEXUS_DATAFORSEO_LOGIN', '…' );
define( 'NEXUS_DATAFORSEO_PASSWORD', '…' );
```

Alternativ können Login und API-Passwort in der Admin-Seite gespeichert werden. Ein leer gesendetes Passwort ersetzt einen bestehenden Wert nicht. Die UI gibt ein gespeichertes Passwort nie wieder aus.

Credentials gehören nicht ins Repository.

## Automatischer Markt-Snapshot

Der wöchentliche Hintergrundjob nutzt ausschließlich gebündelte DataForSEO-Labs-Abfragen:

1. `dataforseo_labs/google/ranked_keywords/live`
2. `dataforseo_labs/google/competitors_domain/live`
3. `dataforseo_labs/google/keyword_overview/live`

Er speichert keine vollständigen API-Rohpayloads als dauerhafte Historie. Der aktuelle Snapshot enthält normalisierte Felder für Cockpit und Opportunity Engine. Die Verlaufshistorie hält maximal 26 kompakte Zustände mit Keyword-Anzahl, ETV, Bewegungen und Wettbewerberanzahl.

Der Snapshot wird unter `nexus_market_intelligence_snapshot_v1` gespeichert. Änderungen invalidieren die zentrale Cockpit-Cache-Version, damit der nächste SEO-Snapshot die aktuellen Marktdaten einliest.

## Live Watch

Keyword-genaue Live-Abfragen sind bewusst nicht Teil des Cron-Jobs:

- Organic: `serp/google/organic/live/advanced`
- Maps: `serp/google/maps/live/advanced`

Sie laufen ausschließlich nach explizitem Admin-Klick.

Grenzen pro manueller Prüfung:

- Organic: maximal 8 Watchlist-Keywords
- Maps: maximal 5 Watchlist-Keywords

Die Maps-Zuordnung versucht das eigene Ergebnis über Domain oder den konfigurierten Business-Namen zu erkennen. Ein fehlender Treffer ist ein neutraler Zustand, kein Ranking `0`.

## Kostenkontrolle

Die DataForSEO-Antwort meldet Kosten pro Request. Das Cockpit führt daraus einen Monatsledger:

- Calls gesamt
- automatische Calls
- gemeldete Kosten gesamt
- gemeldete automatische Kosten
- letzter Endpoint
- letzter HTTP-/API-Status
- letzter Fehler

`monthly_auto_budget_usd` begrenzt weitere automatische Calls, sobald die bereits gemeldeten automatischen Monatskosten den Grenzwert erreicht haben. Weil die endgültigen Kosten eines Requests erst mit der Antwort bekannt sind, ist das kein Prepaid-Hard-Cap für den gerade laufenden Request.

Manuelle Live-Checks bleiben möglich und sind absichtlich nicht vom Auto-Budget blockiert, weil sie eine explizite Nutzeraktion sind. Ihre gemeldeten Kosten fließen dennoch in die Gesamtanzeige ein.

## Datenmodell

### Ranked Keywords

Normalisiert werden unter anderem:

- Keyword
- DataForSEO-Rang
- Suchvolumen
- CPC
- Wettbewerb
- Keyword Difficulty
- Search Intent
- Ziel-URL
- ETV
- Bewegungsstatus
- durchschnittliche Referring-Domain-Signale, sofern geliefert

### Wettbewerber

Normalisiert werden:

- Domain
- SERP-Überschneidungen
- durchschnittliche Position
- Shared ETV
- Shared Keyword Count
- Top-10-Überschneidungen
- organische Domain-Metriken

Die Liste ist datengetrieben. Sie ist keine manuell gepflegte Wettbewerberliste.

### Keyword Overview

Die Watchlist entsteht aus:

1. explizit gepflegten Keywords,
2. starken GSC-Queries,
3. vorhandenen Ranked Keywords.

Die Anzahl wird vor dem API-Call gedeckelt.

## Opportunity Engine

Der aktuelle Score ist deterministisch und transparent. Er kombiniert:

- externes Suchvolumen,
- Ranking-Lücke,
- eigene GSC-Impressionen,
- Search Intent,
- vorhandene Audit-Lead-/Won-Signale der rankenden URL,
- Keyword Difficulty als begrenzten Gegenfaktor.

Beispielhafte Aktionsklassen:

- `Top-10-Push`
- `Seite ausbauen`
- `Position verteidigen`
- `Beobachten`

Das ist kein DataForSEO-Score und keine Erfolgsprognose. Das Cockpit berechnet die Priorität aus den verbundenen Daten selbst.

## Trennung der Datenwelten

- **Search Console:** tatsächliche Google-Performance der eigenen Property
- **Koko:** lokale Onsite-Nutzung
- **Nexus CRM / Audit-CRM:** Anfrage- und Outcome-Signale
- **DataForSEO:** externer Markt, Wettbewerb, Volumen und Live-SERP
- **Research Intelligence:** Primärdaten wie CrUX, Destatis, Eurostat und Energy-Charts

Market Intelligence wird deshalb nicht in Research Intelligence eingebaut. Beide Systeme beantworten unterschiedliche Fragen.

## Fehler- und Fallback-Verhalten

- Jeder Provider-Call kann `WP_Error` liefern.
- Ein Teilausfall verwirft den letzten brauchbaren Snapshot des betroffenen Bereichs nicht.
- API-Status und Fehler werden in der Runtime sichtbar gehalten.
- Es gibt keine Nullwerte, die als erfundene Marktdaten ausgegeben werden.
- Ein Live-Check aktualisiert nur den Live-Bereich des bestehenden Snapshots.
- Ohne Credentials bleibt die Oberfläche als klarer Setup-State nutzbar.

## Bewusste Grenzen von V1

Noch nicht enthalten:

- DataForSEO Backlinks API als eigener Backlink-/Link-Gap-Layer
- DataForSEO OnPage als zweiter technischer Crawler
- exakte historische SERP-Tageskurven für alle Keywords
- automatische Keyword-für-Keyword-Live-Abfragen
- automatische Content-Erstellung oder Veröffentlichung
- Umsatzprognosen aus Suchvolumen

Backlinks sind ein sinnvoller V2-Kandidat, wenn die vorhandenen Markt-/GSC-/CRM-Signale zeigen, dass Link-Gaps tatsächlich die nächste Entscheidungsvariable sind.
