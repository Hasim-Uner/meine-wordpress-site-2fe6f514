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

Die v2-API liefert ein einheitliches, selbstbeschreibendes Response-Envelope mit Series-IDs, Zeitstempeln, Einheiten, Datenverfügbarkeit und Lizenzfeld. Die Integration liest keine Legacy-v1-Strukturen.

## Angezeigte Signale

Der Layer zeigt bewusst nur fachlich nachvollziehbare Signale:

1. tatsächlich installierte PV-Leistung in Deutschland aus der aktuellen Solar-Ist-Serie
2. Veränderung dieser Ist-Leistung gegenüber dem vorherigen verfügbaren Ist-Jahreswert, absolut und prozentual
3. ein explizit als Planung/Ziel ausgewiesenes Solar-Ausbauziel getrennt vom Ist-Bestand
4. durchschnittlicher Solaranteil an der Last über die letzten bis zu 30 verfügbaren Tageswerte plus Vergleich mit dem vorherigen Fenster, sofern vorhanden
5. aktueller Day-Ahead-Preis für die Gebotszone DE-LU

Die UI bewertet den Strompreis nicht als positiv oder negativ. Marktpreise sind Kontext, kein SEO- oder Business-Score.

## Ist-Bestand und Ausbauziel

Die Antwort von `installed_power` kann mehrere Solar-Reihen gleichzeitig enthalten, insbesondere Solar DC, Solar AC und zukünftige Plan-/Zielreihen. Diese dürfen fachlich nicht addiert werden.

Für die aktuelle installierte PV-Leistung gilt deshalb:

- bevorzugt wird eine explizite Solar-DC-Serie, weil sie die installierte Modulleistung beschreibt
- eine nicht geplante Solar-/AC-Serie dient nur als Fallback, falls keine eindeutige DC-Serie vorhanden ist
- Serien, deren ID oder Beschreibung auf `planned`, `plan`, `target`, `EEG` oder `Ziel` hinweist, werden niemals als aktueller Bestand interpretiert
- Werte mit einem Berichtsjahr nach dem aktuellen Kalenderjahr werden nicht als aktueller Ist-Wert verwendet
- AC, DC und geplante Reihen werden nicht miteinander summiert

Ein explizit erkannter Plan-/Zielwert wird separat als Ausbauziel angezeigt. Damit kann beispielsweise ein Ziel für 2030 sichtbar sein, ohne dass es als bereits installierte Leistung oder als reales Jahreswachstum erscheint.

## Solaranteil

Für `/solar_share_daily_avg` versucht der Adapter die Serie in dieser Reihenfolge eindeutig aufzulösen:

1. bekannte IDs wie `solar_share_of_load`, `solar_share`, `solar_share_daily_avg` oder `share`
2. Serienbeschreibung mit `solar` und `share`
3. bei genau einer gelieferten Serie diese einzige Serie
4. eine explizite Prozent-Serie
5. bei genau einem numerischen Wert je Datenzeile dessen Serien-ID

Erst nach erfolgreicher Serienauflösung werden die letzten bis zu 30 numerischen Tageswerte gemittelt. Sind genügend ältere Werte vorhanden, wird das vorherige 30-Tage-Fenster als Vergleich genutzt. Eine uneindeutige Antwort wird als Provider-Fehler sichtbar gemacht und nicht still als `0` interpretiert.

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
- uneindeutige Ist- oder Solaranteil-Serien werden als Fehler gemeldet statt geraten
- wenn alle drei Provider-Abfragen scheitern, wird der Provider als nicht verfügbar markiert
- fehlende Daten erscheinen als `—`, nicht als `0`
- Provider-Fehler sind im Admin aufklappbar

## Lizenz und Quellenangabe

Energy-Charts liefert die Lizenz im v2-Response mit. Die Research-Oberfläche zeigt die zurückgelieferte Lizenzinformation zusammen mit der Quellenangabe `Fraunhofer ISE · Energy-Charts.info`.

Vor einer Veröffentlichung abgeleiteter Zahlen in Blogartikeln oder Grafiken muss die Lizenz des konkret verwendeten Datensatzes beibehalten und die erforderliche Attribution in der Veröffentlichung angegeben werden. Preis-Daten dürfen nicht pauschal als frei lizenziert angenommen werden; maßgeblich ist das Lizenzfeld der konkreten Response.

## Code-Orte

- Provider: `blocksy-child/inc/seo-cockpit/seo-cockpit-research-energy-charts.php`
- Energy-Charts-Renderer: `blocksy-child/inc/seo-cockpit/seo-cockpit-research-v2.php`
- aktiver Drei-Provider-Renderer: `blocksy-child/inc/seo-cockpit/seo-cockpit-research-v3.php`
- Basis-CrUX-/Research-Modul: `blocksy-child/inc/seo-cockpit/seo-cockpit-research.php`
- Styles: `blocksy-child/assets/css/seo-cockpit-research.css`
- Loader: `blocksy-child/inc/seo-cockpit/seo-cockpit.php`

## Noch nicht enthalten

- keine automatische Blog-Veröffentlichung aus Marktdaten
- keine Content-Ideen aus Daten ohne GSC-Kontext
- kein MaStR oder Eurostat in diesem Provider
- keine Speicherung einer eigenen langfristigen Markt-Zeitreihe außerhalb der Provider-Caches
- keine erfundenen Marktprognosen oder Scores

Der nächste fachliche Schritt ist, Energy-Charts-Signale mit vorhandenen GSC-Seiten-/Query-Signalen zu verbinden. Erst dann kann das Cockpit belastbar sagen, dass ein Markttrend und eine konkrete vorhandene URL gemeinsam eine Content-Opportunity bilden.
