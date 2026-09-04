# SEO Cockpit Research – Energy-Charts

Stand: 2026-09-04.

## Zweck

Energy-Charts ist der zweite Primärdaten-Provider im Admin-Bereich `SEO Cockpit -> Research`. Er ergänzt die websitebezogenen CrUX-Felddaten um öffentliche Markt- und Energiesystemdaten für Solar-Dossiers, Marktanalysen und spätere Content-Opportunity-Logik.

Die Integration ist Admin-/Background-only. Es gibt keine Frontend-Requests, keine Cookies und keine öffentliche API-Proxy-Route.

## Quelle

Provider: Fraunhofer ISE Energy-Charts.

API-Basis: `https://api.energy-charts.info/v2`

Verwendete v2-Endpunkte:

- `/v2/installed_power?country=de&time_step=yearly&installation_decommission=false`
- `/v2/solar_share_daily_avg?country=de&year=-1`
- `/v2/price_current?bzn=DE-LU`

Die v2-API liefert ein einheitliches, selbstbeschreibendes Response-Envelope mit stabilen Series-IDs, Zeitstempeln, Einheit, Datenverfügbarkeit und Lizenzfeld. Die Integration liest keine Legacy-v1-Strukturen.

## Angezeigte Signale

V2 zeigt bewusst nur vier kleine, fachlich nachvollziehbare Signale:

1. installierte PV-Leistung in Deutschland aus dem neuesten verfügbaren Jahreswert
2. Veränderung der installierten PV-Leistung gegenüber dem vorherigen Jahreswert, absolut und prozentual
3. durchschnittlicher Solaranteil an der Last über die letzten bis zu 30 verfügbaren Tageswerte plus Vergleich mit den vorherigen 30 Werten
4. aktueller Day-Ahead-Preis für die Gebotszone DE-LU

Die UI bewertet den Strompreis nicht als positiv oder negativ. Marktpreise sind Kontext, kein SEO- oder Business-Score.

## Serienauflösung

Die API beschreibt Serien über stabile snake_case-IDs. Für installierte PV-Leistung gilt:

- falls eine aggregierte Serie `solar` vorhanden ist, wird nur diese verwendet
- andernfalls werden verfügbare `solar_*`-Serien summiert

Damit werden Aggregate und AC/DC-Teilserien nicht gleichzeitig doppelt gezählt.

Für den Solaranteil wird zuerst nach `solar_share_of_load`, dann `solar_share`, dann `solar` gesucht; andernfalls wird die erste Serie mit `solar` in der ID verwendet.

Fehlende oder nichtnumerische Werte werden übersprungen und nie als Null interpretiert.

## Caching und Rate-Nutzung

Der öffentliche API-Zugang wird defensiv genutzt:

- installierte Leistung: 12 Stunden Cache
- täglicher Solaranteil: 6 Stunden Cache
- aktueller Day-Ahead-Preis: 30 Minuten Cache
- manueller Refresh im Research-Admin löscht nur die drei Energy-Charts-Transients

Die Abfragen sind fest allowlisted; der Admin kann keine freien Endpunkte oder Query-Parameter über die Oberfläche an die externe API schicken.

## Fehlerverhalten

- HTTP- und Netzwerkfehler werden als `WP_Error` behandelt
- Teilfehler lassen die anderen Provider-Signale sichtbar
- wenn alle drei Abfragen scheitern, wird der Provider als nicht verfügbar markiert
- fehlende Daten erscheinen als `—`, nicht als `0`
- Provider-Fehler sind im Admin aufklappbar

## Lizenz und Quellenangabe

Energy-Charts liefert die Lizenz im v2-Response mit. Die Research-Oberfläche zeigt die zurückgelieferte Lizenzinformation zusammen mit der Quellenangabe `Fraunhofer ISE · Energy-Charts.info`.

Vor einer Veröffentlichung abgeleiteter Zahlen in Blogartikeln oder Grafiken muss die Lizenz des konkret verwendeten Datensatzes beibehalten und die erforderliche Attribution in der Veröffentlichung angegeben werden. Preis-Daten dürfen nicht pauschal als frei lizenziert angenommen werden; maßgeblich ist das Lizenzfeld der konkreten Response.

## Code-Orte

- Provider: `blocksy-child/inc/seo-cockpit/seo-cockpit-research-energy-charts.php`
- Multi-Provider-Renderer: `blocksy-child/inc/seo-cockpit/seo-cockpit-research-v2.php`
- Basis-CrUX-/Research-Modul: `blocksy-child/inc/seo-cockpit/seo-cockpit-research.php`
- Styles: `blocksy-child/assets/css/seo-cockpit-research.css`
- Loader: `blocksy-child/inc/seo-cockpit/seo-cockpit.php`

## Noch nicht enthalten

- keine automatische Blog-Veröffentlichung aus Marktdaten
- keine Content-Ideen aus Daten ohne GSC-Kontext
- kein MaStR, Destatis oder Eurostat in diesem Provider
- keine Speicherung einer eigenen langfristigen Markt-Zeitreihe außerhalb der Provider-Caches
- keine erfundenen Marktprognosen oder Scores

Der nächste fachliche Schritt ist, Energy-Charts-Signale mit vorhandenen GSC-Seiten-/Query-Signalen zu verbinden. Erst dann kann das Cockpit belastbar sagen, dass ein Markttrend und eine konkrete vorhandene URL gemeinsam eine Content-Opportunity bilden.
