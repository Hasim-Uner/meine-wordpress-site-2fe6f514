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
- Organic Live besitzt einen eigenen Standort; Standard ist `Hanover,Lower Saxony,Germany`
- Organic Live besitzt eine konfigurierbare SERP-Tiefe von 10 bis 200; Standard ist 50

DataForSEO liefert bei Organic Live ohne gesetzte Tiefe standardmäßig nur zehn Ergebnisse. Deshalb bedeutet ein fehlender eigener Treffer **nicht**, dass eine URL nicht indexiert ist oder überhaupt nicht rankt. Die Oberfläche speichert Standort und tatsächlich abgefragte Tiefe je Live-Check und zeigt beispielsweise `nicht in Top 50` statt des irreführenden `nicht gefunden`.

Eine größere Organic-Tiefe ist eine bewusste manuelle Kostenentscheidung: DataForSEO berechnet Organic Live je SERP-Block von bis zu zehn Ergebnissen. Der Live-Check bleibt deshalb außerhalb des regulären Wochen-Crons.

### Background-Verarbeitung

Der Admin-Klick führt die Watchlist nicht mehr synchron aus. Das ist wichtig, weil acht Live-Requests mit größerer SERP-Tiefe einen Proxy-/PHP-Request unnötig lange offen halten können.

Ablauf:

1. Der Admin-Klick legt einen Job mit Watchlist, Standort und Tiefe an und kehrt sofort zum Cockpit zurück.
2. Ein privater, token-geschützter Loopback-Worker verarbeitet **genau ein Keyword pro Request**.
3. Nach jedem Keyword wird der Fortschritt gespeichert und der nächste Worker non-blocking angestoßen.
4. Ein WP-Cron-Event dient nur als Fallback, falls der Host Loopback-Requests blockiert.
5. Erst nach Abschluss wird der Live-Bereich des Market-Snapshots atomar ersetzt.

Die UI zeigt `läuft im Hintergrund · x/y`. Dadurch hängt der Browser nicht mehr an der kompletten DataForSEO-Watchlist und ein Varnish-Timeout kann den Admin-Klick nicht mehr abbrechen.

Die Maps-Zuordnung versucht das eigene Ergebnis über Domain oder den konfigurierten Business-Namen zu erkennen. Ein fehlender Maps-Treffer ist ebenfalls ein neutraler Zustand, kein Ranking `0`.

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

Die automatische Liste bleibt vollständig datengetrieben. Sie beantwortet: **Mit welchen Domains überschneidet sich die Website aktuell in Googles organischem Suchraum?** Deshalb können bei einer historisch starken Energie-/Solar-Sichtbarkeit weiterhin Branchenportale und Energieanbieter auftauchen.

Daneben gibt es eine **strategische Vergleichsgruppe**. Sie beantwortet die andere Frage: **Mit welchen Anbietern soll die aktuelle WordPress-/B2B-Positionierung verglichen werden?** Diese Domains werden als editierbare Einstellung gepflegt und lösen allein keine zusätzlichen DataForSEO-Calls aus. Wenn eine strategische Domain zugleich im automatischen Competitor-Snapshot vorkommt, werden die vorhandenen Überschneidungsmetriken direkt an der strategischen Karte angezeigt.

Initiale Vergleichsgruppe:

- `oliverfleck.de`
- `onma.de`
- `goldenberg-agentur.de`
- `wedeon.de`
- `kontor4.de`
- `perimetrik.de`

### Strategische Domains manuell prüfen

Die strategische Liste selbst ist kostenlos und rein lokal. Für belastbare Domain-Metriken gibt es zusätzlich den expliziten Admin-Button `Strategische Domains prüfen`.

Dieser Pfad:

- nutzt `dataforseo_labs/google/domain_rank_overview/live`
- prüft maximal acht Domains pro Klick
- startet genau deshalb **nicht** automatisch im Wochen-Cron
- zählt als manueller DataForSEO-Request und damit in die Gesamt-Kostenanzeige, nicht in das automatische Monatsbudget
- speichert pro Domain unter anderem rankende Keywords, ETV, Top-10-Verteilung, geschätzten Paid-Traffic-Wert und Ranking-Bewegungen
- bewahrt den letzten brauchbaren Stand im Market-Snapshot

Der Button ist bewusst getrennt vom normalen `Marktdaten aktualisieren`: Die strategische Vergleichsgruppe soll nicht bei jedem Labs-Refresh zusätzliche Domain-Requests erzeugen.

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

Zusätzlich trennt die Oberfläche die Chancen in drei einfache Entscheidungssegmente:

- `Geschäftschance`: kaufnahe Queries oder Rankings auf Service-/Kontakt-/Money-Pages
- `Content & Nachfrage`: informative Nachfrage auf Blog-, Hub- oder Proof-Seiten
- `Marke & Proof`: navigationaler Fremdmarken-/Brand-Traffic, der sichtbar bleibt, aber die direkte Geschäftsqueue nicht dominiert

Der Opportunity-Score berücksichtigt deshalb neben Suchvolumen, Ranking und GSC jetzt auch den vorhandenen Page-Role-Business-Wert. Navigationaler Brand-Traffic erhält einen begrenzten Abschlag. Ein Checkfox-Keyword verschwindet damit nicht aus dem Cockpit; es wird nur nicht mehr automatisch vor einem kaufnahen WordPress-Keyword priorisiert.

Das ist kein DataForSEO-Score und keine Erfolgsprognose. Das Cockpit berechnet die Priorität aus den verbundenen Daten selbst.

## Market CSV

`Markt & Wettbewerb` besitzt einen eigenen `Market CSV`-Export. Er ist bewusst vom allgemeinen GSC-Export getrennt.

Der Export liest ausschließlich den bereits gespeicherten Market-Intelligence-Snapshot sowie lokal vorhandene GSC-/CRM-Signale. Ein Download startet **keinen neuen DataForSEO- oder Research-Request**.

Zeilentypen:

- `ranked_keyword` — aktuelle DataForSEO-Rankings der eigenen Domain
- `keyword_overview` — Suchvolumen, CPC, Keyword Difficulty, Intent und SERP-Merkmale
- `competitor_auto` — automatisch erkannte organische Wettbewerber
- `competitor_strategic` — kuratierte strategische Vergleichsgruppe inklusive vorhandener Overlap-Metriken
- `opportunity` — repo-eigener Join aus DataForSEO, GSC, WordPress und CRM
- `live_organic` / `live_maps` — zuletzt manuell gespeicherte Live-Ergebnisse, sofern vorhanden

Format:

- UTF-8 mit BOM
- Semikolon als Trennzeichen
- deutsche Dezimaldarstellung
- direkt in Excel öffnungsfähig
- keine PDF-Ausgabe, weil Rohdaten für Analyse, Agenten und weitere Verarbeitung erhalten bleiben sollen

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
