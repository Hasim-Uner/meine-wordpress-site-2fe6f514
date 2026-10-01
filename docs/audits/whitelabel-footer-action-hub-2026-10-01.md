# Whitelabel-Title, Footer-Preis und Action Hub

Stand: 01.10.2026. Umsetzung in drei getrennten Commits auf
`codex/whitelabel-footer-action-hub`, ausgehend von `origin/main` (`6a95487c`).
Worktree: `/Users/hasimster1/Desktop/meine-wordpress-site-action-hub`.
Vorhandene Änderungen im ursprünglichen Arbeitsverzeichnis wurden nicht berührt.
Die Änderungen sind lokal validiert; Produktions-Deployment und
authentifizierte Admin-Abnahme sind noch offen.

## Änderungen und Dateien

| Commit | Dateien | Ergebnis und Begründung |
| --- | --- | --- |
| `25eacee2` — Whitelabel-Title | `blocksy-child/inc/seo-meta.php` | Einziger erzwungener Title-Eintrag für die Route: `White-Label für Agenturen: WordPress-Webdesign & Tracking`. WordPress-Dokumenttitel sowie OG und Twitter lesen denselben Eintrag; gespeicherte ACF-/Legacy-Meta werden überschrieben. Laut Auftrag: Position 2 für „seo white label“, aber keine Klicks wegen unpassender Intention; Ziel sind „white label agentur“ und „white label webdesign“. Diese Rankingangaben wurden nicht eigenständig aus GSC verifiziert. |
| `ddb26b8e` — Footer-Preis und CI | `blocksy-child/inc/funnel-doors.php`, `blocksy-child/template-parts/site-footer.php`, `scripts/canon-forbidden-values.txt`, `scripts/tests/navigation-contract.php`, `docs/architecture/CONVERSION_ROUTING.md`, `docs/architecture/LIVE_STATUS.md` | Eigener Footer-Betrag aus `standard/setup` statt `measurement/setup`. Server-Side-Anker im Footer: ab 1.290 €. Andere Oberflächen behalten den gültigen clientseitigen Einstieg. Literal-Sperre und Prüfung der gerenderten Ausgabe ergänzen sich. |
| Dritter Commit — Action Hub | `blocksy-child/inc/seo-cockpit/seo-cockpit-leads.php`, `seo-cockpit-command-center.php`, `seo-cockpit-insights.php`, `seo-cockpit-sync.php`, `seo-cockpit-ui.php` im selben Modulverzeichnis; `scripts/tests/seo-cockpit-action-hub.php`, `agents/skills/seo-cockpit-dev/tests/run-action-hub-tests.sh`, `docs/systems/seo-cockpit.md`, `docs/architecture/LIVE_STATUS.md`, dieser Bericht | Gemeinsamer TEST-Filter, Whitelabel als Service mit passender Maßnahmenempfehlung, neuer Snapshot-Key für korrigierte Auswertung, kleine Korrektur der Pulse-Kartenwerte. |

Die Whitelabel-Meta-Description, H1, Body-Copy und URL wurden nicht geändert.
Es gab keine Datenbankänderung und keine Löschung von Leads. Der benannte
Filter `nexus_is_seo_cockpit_lead_signal()` prüft den gespeicherten Titel,
unabhängig davon, welches Firmen-/Domain-Label die Queue später anzeigt.
Er filtert vor der Aggregation aller Zeitfenster und vor dem Queue-Limit.
Der gefilterte Snapshot versorgt Zähler, Attribution und Revenue-Score.

## Warum die Preis-CI den Fehler übersehen hat

`scripts/canon-guard.sh` durchsucht bereits versionierte und neue Dateien,
einschließlich Footer und Templates. Eine fehlende Pfadabdeckung war hier
nicht die Ursache: Im Footer stand kein Preis-Literal. `hu_funnel_doors()`
lieferte dynamisch die gültige, aber für diesen Footer falsche Stufe
`measurement`. Auch der bisherige Navigationstest erwartete diese Stufe
im Footer ausdrücklich.

Der Footer liest jetzt `footer_amount` aus `hu_tracking_price('standard',
'setup')`. Die bestehende Navigationsprüfung prüft die tatsächlich gerenderte
Footer-Ausgabe gegen diesen unabhängigen Produktanker in allen Kontexten.
Die neue Regel `preis-tracking-footer-alt` erfasst zusätzlich den alten
„ab“-Betrag, auch bei HTML-/geschützten Leerzeichen und `EUR`. Ihr
Footer-/Template-Kontext greift auch bei getrennten Zeilen für Label und
Betrag. Die erste, clientseitige Stufe bleibt gültig.

Negativprüfung mit kurzzeitiger Änderung des echten Footers und vollständiger
Wiederherstellung: `ab 890 €`, `ab 890&nbsp;€`, `ab 890 EUR`, geschützte
Leerzeichen und `ab&nbsp;890&nbsp;&euro;` erzeugten jeweils Exit 1 mit der
neuen Sperrregel. Eine temporäre Rückkehr zu `measurement/setup` erzeugte
Exit 1 in der Prüfung der gerenderten Footer-Beträge. Es blieb keine
Probeänderung zurück.

## Übrige 890-Treffer: unverändert

Die repo-weite Textsuche erfasste Preisliterale, HTML-Entitäten, Quellcode,
JSON/Schema und `llms.txt`. Abhängigkeiten, Build-Artefakte und binäre Bilder
sind keine Preisquellen. Die Preis-Suche erfasste auch inaktive n8n-Dateien
(ohne Fund); an den Automationen wurde nichts geändert. Historische Treffer
wurden zur Einordnung gesucht.

| Datei / Fundstellen | Einordnung |
| --- | --- |
| `blocksy-child/inc/canon/pricing-canon.php:211,214` | Kommentar und Konstante `HU_TRACKING_MEASUREMENT_SETUP = 890`: gültiges **clientseitiges Conversion-Tracking**, kein alter Server-Side-Preis. Daraus werden auch die korrekten Preise dieser Stufe auf Startseite und GA4-Seite sowie deren strukturierte Daten generiert. |
| `docs/decisions/tracking-preisleiter.md:23,37,52,54` | Preis und Herleitung derselben clientseitigen Stufe; archivierte Entscheidung. |
| `CHANGELOG.md:59` | Historischer Eintrag über die Einführung der vierstufigen Leiter; 890 bezeichnet Conversion-Tracking. |
| `scripts/canon-forbidden-values.txt:94,100` | Bestehendes Leiter-Muster und neue Sperrregel, keine ausgelieferte Preis-Copy. |
| `blocksy-child/inc/crm-sales/ui.php:133` | Potenzialwert-Platzhalter `8900`, kein Trackingpreis. |
| `seo-research/2026-07/data/units-log.md:44,47,48,50` | API-Verbrauchsstände mit Endziffern 890; keine Europreise. |
| GSC-CSV-Dateien unter `seo-research/2026-09/data/gsc/` | Die Ziffernfolge in Post-ID `14890` des Assets „Wettbewerbs-Analyse“; kein Preis. |
| Archivierte HTML-Bilddaten / SVG-Geometrie | Zufällige Ziffernfolgen in Base64 bzw. Vektordaten, keine Preisangaben. |

`llms.txt` enthält keinen 890-Preis. Ein alter Server-Side-Endkundenpreis
wird nach der Änderung weder als Literal noch dynamisch im Footer
verwendet. Zahlen in diesem Befundbericht dokumentieren die Suche und sind
keine Angebotsangaben.

## Weitere verdächtige Seitenrollen: nur Diagnose

Prüfung mit dem tatsächlichen Rollen-Resolver und der primären URL-Map des
Repos, für einen normalen veröffentlichten Seitenkontext. Authentifizierte
Live-WordPress-Meta konnten hier nicht abgefragt werden.

| Route | Derzeitige Rolle | Auffälligkeit |
| --- | --- | --- |
| `/server-side-tracking-b2b/` | Seite | Service-Angebot; die Service-Liste enthält nur den allgemeinen GA4-/Tracking-Pfad. |
| `/landingpage-erstellen-lassen/` | Seite | Angebotsroute ist in der primären URL-Map, fehlt aber in der Service-Liste. |
| `/conversion-optimierung/` | Seite | Angebotsroute fehlt in der Service-Liste. |
| `/wordpress-website-erstellen-lassen/` | Seite | Angebotsroute fehlt in der Service-Liste. |
| `/solar-waermepumpen-leadgenerierung/` | Audit | Der Audit-Einstieg zeigt auf `#marktcheck` dieser Seite. Der Rollenvergleich entfernt den Anker und klassifiziert dadurch die ganze Angebotsseite als Audit. |

Diese Rollen wurden nicht geändert. Whitelabel wurde von `results`/Proof
in die bestehende `service`-Liste verschoben. Zusätzlich war die konkrete
„Proof/E3-Nähe und Marktcheck-Brücke“-Empfehlung im Resolver für
`MONEY_PAGE_UNDERPERFORMING` unabhängig von der Rolle fest geschrieben.
Nur für Whitelabel nennt dieser Hinweis jetzt Agentur-Angebot,
Erstprojekt-CTA und Aufgabenanfrage.

## Performance Pulse: Diagnose und kleiner Fix

Der Datenweg ist `nexus_get_seo_cockpit_date_series()` →
`nexus_normalize_seo_cockpit_date_series()` → Snapshot `trend` →
`nexus_render_seo_cockpit_trend_card()`.

Die Normalisierung ergänzt jeden fehlenden Kalendertag mit Nullwerten und
`has_data=false`. Der Renderer las bislang für **alle vier Karten** nur
`end($values)`. Ein fehlender letzter Tag erzeugt dadurch Klicks 0,
Impressionen 0, CTR 0 und Position 0, obwohl vorherige Datentage Werte haben.
Die Zahlen waren also Tagesendwerte, keine Periodensummen.

Dieser Fehler ist mit echten Normalisierungs- und Renderfunktionen und
synthetischen GSC-Testdaten reproduziert. Das Reporting-Fenster berücksichtigt
bereits drei Tage Datenverzug; trotzdem kann der letzte angefragte Tag
fehlen. Ohne Zugriff auf den authentifizierten Live-Snapshot lässt sich
nicht abschließend belegen, dass genau dieser fehlende Tag die konkrete
Live-Nullanzeige verursacht hat. Es gibt im geprüften Datenweg keinen
Hinweis auf eine generelle falsche Feldzuordnung der vier GSC-Metriken.

Der kleine Fix liest den letzten tatsächlich gelieferten Datentag und
kennzeichnet dessen Datum am Wert. Ein gemeldeter Nullwert bleibt Null;
bei vollständig fehlenden Daten steht `—`. GSC-Abfragen, Fenster,
Normalisierung, Kurven und OAuth bleiben unverändert.

## Abnahme / Validierung

- Lokal gerenderter Whitelabel-Head: neuer `<title>`, `og:title` und
  `twitter:title`, identische bisherige Meta-Description. Der erzwungene
  Title überschreibt im Prüfaufbau auch einen alten gespeicherten Title.
- Gerenderter globaler Footer auf `/`, `/hasim-uener/` und `/impressum/`:
  jeweils `ab 1.290 €` für die Tracking-Tür. Die Navigationstests prüfen
  weitere Kontextseiten und erhalten die Conversion-Tracking-Angaben im Kopf.
- Preis-CI: positiver Lauf und die beschriebenen sechs Negativprüfungen
  (fünf Literalvarianten plus falscher dynamischer Paketschlüssel).
- Neue Action-Hub-Suite: 31 Prüfungen, darunter mehr TEST-Leads als das
  Queue-Limit, Erhalt realer Leads, aktuelle/vorherige/Lifetime-Zähler,
  unveränderte CRM-Datensätze, Revenue-Score, gerenderter Audit-Zähler,
  gerendertes URL-Radar mit Service, Pulse mit fehlendem Tag/echter Null/
  vollständig fehlenden Daten und Invalidierung alter Snapshot-Auswertung.
- Bestehende Cockpit-Suites: 8 Display- und 12 Export-Prüfungen bestanden.
- Erster gemeinsamer Prüflauf: 28 Checks bestanden, einschließlich
  Architektur, PHP-Lint, Browser-Tests, PHPStan und Theme-Build.
- Abschließender gemeinsamer Prüflauf nach den Cockpit-Änderungen:
  `npm run check` mit 28 Checks bestanden, einschließlich aller Skill-Suites,
  Browser-Tests, PHPStan und Theme-Build. Zusätzlich bestanden
  `npm run lint:architecture`, `npm run lint:php` und der Pre-Deploy-Smoke.

Produktionsabnahme bleibt nach Deployment offen: Live-Head, Footer auf drei
Seiten und authentifiziertes Dashboard samt tatsächlichem GSC-Tagesende.
Es wurden keine produktiven Leads angelegt, geändert oder gelöscht.
