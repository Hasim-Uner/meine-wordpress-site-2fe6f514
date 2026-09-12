# SEO-Briefing /solar-waermepumpen-leadgenerierung/

Stand 12.09.2026 · beschreibt den umgesetzten und live ausgelieferten Zustand

> **Diese Datei ersetzt eine, die es nie gab.** `auftrag.md`, Abschnitt 5, verweist auf
> ein älteres Dokument `briefing-solar-money-page.md`, dessen Abschnitt 7.3 die
> JSON-LD-Blöcke „fertig ausformuliert" enthalten sollte. Dieses Dokument wurde nie
> geliefert und liegt in keiner Fassung im Repo. Die Blöcke sind deshalb aus dem Canon
> geschrieben worden statt von dort übernommen.
>
> Was hier steht, ist kein Entwurf. Es ist die Beschreibung dessen, was ausgeliefert
> wird, mit Dateiangabe und Zeilennummer. Wer die Seite ändert, prüft gegen diese Datei
> und schreibt sie nach.

---

## 1 Warum das hier kein Copy-Paste-Block ist

Die naheliegende Form für dieses Dokument wäre gewesen, die fünf JSON-LD-Blöcke
ausformuliert hineinzuschreiben, so wie der Auftrag es vom verschollenen Vorgänger
erwartet hat. Genau das wäre falsch.

Kein einziger Wert in diesen Blöcken ist im Template ausgeschrieben. Preise, Kennzahlen,
Zeiträume und Fallbeschreibung kommen zur Laufzeit aus `blocksy-child/inc/canon/`. Ein
ausformulierter Block in einer Markdown-Datei wäre eine zweite Wahrheit, die beim ersten
Preiswechsel still auseinanderläuft — und ein Guard im Repo (`lint-canon-drift.sh`) ist
genau dafür da, solche Zweitwahrheiten zu finden.

Dieses Dokument beschreibt deshalb **Struktur und Herkunft** jedes Blocks. Der Wortlaut
steht im Template, die Zahlen stehen im Canon.

---

## 2 Title und Description

`blocksy-child/inc/seo-meta.php:275`

```
Photovoltaik-Anfragen selbst generieren statt Leads kaufen

Eigenes Anfragesystem für Photovoltaik und Wärmepumpe: Anfragen auf Ihrer Domain,
vorqualifiziert, serverseitig gemessen. Aufbau ab {foundation}, Einstieg ab {entry}.
```

Die beiden Preise sind Platzhalter. Sie kommen über `hu_seo_price_display()` aus
`pricing-canon.php` (`foundation_price_standard`, `entry_setup_price`) und sind im
Quelltext nicht ausgeschrieben. Wer den Preis ändert, ändert den Canon — die Description
zieht nach.

---

## 3 Die fünf JSON-LD-Blöcke

Alle fünf werden im Template gesammelt und am Seitenende einzeln ausgegeben:
`page-solar-waermepumpen-leadgenerierung.php:1446`.

| # | Typ | `@id` | Was den Inhalt speist |
|---|---|---|---|
| 1 | `Service` | `<permalink>#service` | `e3-proof-canon.php` für Fall, Zeitraum, CPL vorher/nachher, Reduktion |
| 2 | `HowTo` | `<permalink>#howto` | das `$stations`-Array — dieselbe Quelle wie die sichtbaren fünf Stationen |
| 3 | `DefinedTerm` „Eigenes Anfragesystem" | `<permalink>#begriff-anfragesystem` | die erste FAQ-Antwort, wortgleich |
| 4 | `DefinedTerm` „Cost per Order" | `<permalink>#begriff-cost-per-order` | die dritte FAQ-Antwort, wortgleich |
| 5 | `FAQPage` | `<permalink>#faq` | `$faq_items`, dieselbe Quelle wie die sichtbaren Fragen |

**Block 2 und 5 lesen dieselben Arrays wie die sichtbare Ausgabe.** Das ist kein Zufall,
sondern der Punkt: eine Abweichung zwischen Schema und Seite ist dadurch nicht möglich,
ohne beides gleichzeitig zu ändern.

### Was bewusst nicht ausgegeben wird

**Kein `WebPage`- und kein `BreadcrumbList`-Block.** Der Auftrag nennt beide, sie fehlen
hier trotzdem — begründet im Template selbst, Zeile 542.

`inc/org-schema.php` gibt beide bereits global aus, unter exakt denselben `@id`s
(`<permalink>#webpage`, `<permalink>#breadcrumb`). Ein zweiter Knoten unter derselben
`@id` ist kein zusätzliches Signal, sondern ein widersprüchlicher Graph: zwei Beschreibungen
desselben Dings, und kein Verfahren, das entscheidet, welche gilt.

Der Begriffsknoten hängt sich deshalb per `subjectOf` an die globale WebPage, statt sie zu
ersetzen. Der **sichtbare** Breadcrumb oben im Dokument bleibt davon unberührt — er gehört
zur Seite, nicht zum Schema.

Wer den Rich-Results-Test für BreadcrumbList fahren will, prüft `inc/org-schema.php`, nicht
diese Seite.

---

## 4 Die Wortgleichheits-Regel und wie sie erzwungen wird

Der `text` im FAQPage-Schema muss wortgleich dem sichtbaren Antworttext entsprechen.
Abweichung ist ein Rich-Result-Verstoß.

Diese Regel wird hier nicht durch Disziplin eingehalten, sondern durch die Datenstruktur.
Jede Antwort liegt als zwei Felder vor, `lead` und `rest`:

- **sichtbar** (Zeile 1352): `lead` in einem `<span class="erst">`, direkt gefolgt von
  `rest` — die optische Hervorhebung des ersten Satzes ändert den Text nicht
- **im Schema** (Zeile 374): `trim( $item['lead'] . ' ' . $item['rest'] )`

Beides liest dasselbe Array. Eine Abweichung entstünde nur, wenn jemand einen zweiten
Textkörper einführt. **Genau das ist die Regel:** keine zweite Textquelle für eine Antwort.

Dasselbe gilt für die beiden `DefinedTerm`-Beschreibungen — sie greifen über dieselbe
Funktion auf `$faq_items[0]` und `$faq_items[2]` zu und können nicht driften.

---

## 5 Anker und Heading-IDs

Drei Anker sind **Bestandsschutz**. Auf sie zeigen interne Links von anderen Seiten; sie
dürfen nicht umbenannt werden:

```
#marktcheck    #ergebnisse    #einstieg
```

Zusätzlich vergeben, frei änderbar solange keine internen Links darauf zeigen:

```
#strecke   #anteil   #rechnung   #passung   #fragen   #verweise
```

Jede H2 und H3 trägt eine eigene `id` (`#strecke-titel`, `#rechnung-titel` und so weiter).
Die Kapitelnavigation und die Randmarken werden aus demselben `$chapters`-Array erzeugt wie
die Abschnitte — eine neue Sektion taucht dort automatisch auf.

`#marktcheck` ist zusätzlich als eigener Einstieg in `llms.txt` geführt.

---

## 6 og:image

`blocksy-child/assets/img/anfragestrecke-og.png`, 1200 × 630, eingehängt über
`hu_get_route_social_image()` in `inc/seo-meta.php:1601` — eine Routen-Tabelle, die derzeit
genau diesen einen Eintrag hat.

Ohne diesen Eintrag fällt die Seite auf das globale Profilbild zurück, ein Hochformat-Porträt,
das in der 1,91:1-Vorschau beschnitten wird. Das war der Grund für den Auftragspunkt.

Das Bild wird nicht von Hand gepflegt, sondern gebaut:
`scripts/build-anfragestrecke-og-image.py` zieht dieselben `.woff2`-Dateien und dieselben
Canon-Werte wie die Seite. Nach einer Preis- oder Kennzahlenänderung neu erzeugen.

**Der Auftragspunkt „Alt-Text am Porträt" ist gegenstandslos geworden.** Auf der Seite gibt
es kein Porträt mehr — der alte Hero, in dem es stand, ist im Umbau entfallen.

---

## 7 llms.txt

Die Seite ist in `llms.txt` im Root zweifach geführt: einmal als Money Page des
Energie-Clusters, einmal als Marktcheck-Einstieg unter `#marktcheck`.

**Wichtig für jede künftige Änderung:** `llms.txt` wird generiert, nicht getippt. Der Guard
`lint-entity-crawler-signals.php` vergleicht die Datei im Root gegen den gerenderten
Routenindex und schlägt bei Abweichung fehl. Nach jeder Änderung an Titel, Beschreibung oder
Routenbestand neu erzeugen:

```
--write-llms
```

Diese Prüfung ist in der lokalen Guard-Runde leicht zu übersehen, weil sie PHP ist und nicht
zu den `lint-*.sh`-Skripten gehört. Sie hat den Umbau einmal rot gemacht.

---

## 8 Was vor einem Merge zu prüfen ist

- [ ] Kein zweiter Knoten unter einer `@id`, die `inc/org-schema.php` bereits belegt
- [ ] FAQ-Antworten haben genau eine Textquelle je Frage (`lead` + `rest`)
- [ ] `#marktcheck`, `#ergebnisse`, `#einstieg` unverändert
- [ ] Keine Zahl im Schema ausgeschrieben, die im Canon steht
- [ ] `llms.txt` nach Änderungen an Titel oder Beschreibung neu erzeugt
- [ ] og:image nach Preisänderung neu gebaut
- [ ] Rich-Results-Test grün für Service, HowTo, FAQPage (BreadcrumbList global prüfen)
