# SEO Cockpit Research: Destatis GENESIS

Stand: 2026-09-04.

## Zweck

Der Destatis-Provider ergänzt `SEO Cockpit -> Research` um amtliche Gebäudebestandsdaten. Er soll Marktanalysen für Solar- und Wärmepumpen-Themen mit belastbaren Strukturwerten versorgen, ohne aus Gebäudestruktur automatisch Eigentümerstatus, Kaufabsicht oder ein künstliches Marktpotenzial abzuleiten.

## Quelle und API

- Quelle: Statistisches Bundesamt (Destatis), GENESIS-Online
- REST-Basis: `https://genesis.destatis.de/genesisWS/rest/2020/`
- Datenservice: `POST /data/table`
- Content-Type: `application/x-www-form-urlencoded; charset=UTF-8`
- Authentifizierung: kostenloser GENESIS API-Token im HTTP-Header `username`; `password` bleibt leer
- GET-basierte Datenabrufe sind nicht Bestandteil dieses Providers
- kein Frontend-Request; Abruf nur im Admin-/Background-Kontext

## Credential-Vertrag

Bevorzugt:

```php
define( 'NEXUS_DESTATIS_API_TOKEN', '...' );
```

Alternativ kann der Token im Research-Admin gespeichert werden. Er landet dann in `nexus_seo_cockpit_research_settings` unter `destatis_api_token`.

Regeln:

- kein Token im Repo
- Runtime-Konstante gewinnt vor WordPress-Option
- ein leeres Formularfeld überschreibt einen bestehenden Token nicht
- `Token entfernen` löscht nur den WordPress-Option-Wert
- der kostenlose GENESIS-Account bleibt eine Live-Abhängigkeit außerhalb des Repos

## Allowlistete Tabellen

V1 nutzt nur:

- `31231-0005`: Wohngebäude, Wohnungen, Wohnfläche – Deutschland, Stichtag, Anzahl der Wohnungen
- `31231-0014`: dieselbe Struktur nach Bundesländern

Der Provider lädt nur ein rollendes kleines Zeitfenster aus aktuellem Jahr und zwei Vorjahren. Die Antworten werden 24 Stunden gecacht.

## Angezeigte Kennzahlen

Aus der neuesten sicher erkannten Zeile werden abgeleitet:

- Wohngebäude Deutschland insgesamt
- Anteil und Anzahl der Wohngebäude mit 1 oder 2 Wohnungen
- Wohngebäude Niedersachsen insgesamt
- Anteil und Anzahl der Wohngebäude mit 1 oder 2 Wohnungen in Niedersachsen

Diese Kennzahlen sind Strukturwerte. Sie dürfen nicht als Zahl der Eigentümer, Zahl potenzieller Wärmepumpenkunden oder Abschlusswahrscheinlichkeit bezeichnet werden.

## Parsing und Fehlerverhalten

`data/table` liefert den Tabelleninhalt in `Object.Content`. Der Provider:

1. zerlegt den Inhalt als semikolongetrennte Tabellenzeilen,
2. erkennt die neueste Stichtagszeile,
3. nutzt die bekannte 15-Werte-Struktur der Tabellen `31231-0005` und `31231-0014`,
4. berechnet nur die oben dokumentierten Kennzahlen.

Wenn das Tabellenformat nicht sicher erkannt wird, werden keine Nullwerte erfunden. Stattdessen erscheint im Admin ein Provider-Fehler. HTTP-Erfolg allein gilt ebenfalls nicht als fachlicher Erfolg; eine leere oder logisch fehlerhafte GENESIS-Antwort wird als Fehler behandelt.

## Cache und Refresh

- Cache: 24 Stunden pro Tabelle
- manueller Refresh: `SEO Cockpit -> Research -> Destatis neu laden`
- Admin-Action: `nexus_seo_cockpit_destatis_refresh`
- Credential-Action: `nexus_seo_cockpit_destatis_save`

## Code-Orte

- `blocksy-child/inc/seo-cockpit/seo-cockpit-research-destatis.php`
- `blocksy-child/inc/seo-cockpit/seo-cockpit-research-v3.php`
- Loader: `blocksy-child/inc/seo-cockpit/seo-cockpit.php`

## Noch nicht enthalten

- Kreisdaten
- Baugenehmigungen
- Unternehmensstatistik
- automatische Verknüpfung mit Search-Console-Opportunities
- Marktpotenzial- oder Lead-Score
- automatische Blogartikel

Ein späterer Ausbau soll nur erfolgen, wenn eine konkrete Content- oder Marktfrage eine weitere Tabelle rechtfertigt.
