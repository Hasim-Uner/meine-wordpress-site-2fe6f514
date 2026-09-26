# Tracking-Preisleiter: vier Stufen, eine Definition

Entscheidung: 2026-09-26. Der Betreiber hat die Server-Side-Stufe bei 1.290 € festgelegt und die übrige Preisfindung ausdrücklich delegiert. Umsetzung im selben PR.

## Anlass

Derselbe Kanonpreis `HU_TRACKING_STANDARD_SETUP` (1.290 €) stand für drei verschiedene Produkte:

| Oberfläche | Was 1.290 € dort kaufte |
|---|---|
| `/server-side-tracking-b2b/` („Basis“), White-Label-Margenblock | Server-GTM, eigene Tracking-Subdomain, GA4, Google Ads |
| `/ga4-tracking-setup/` („Basis“), Startseite Station 04 | clientseitig: GA4, Tag Manager, Consent Mode, Google Ads; Server-Side erst ab 1.900 € |
| Startseite, Angebot 02 („Tracking bis ins CRM“) | dasselbe clientseitige Setup unter einem Namen, dessen CRM-Teil auf beiden Detailseiten ab 3.500 € kostet |

Wer zwei Tabs vergleicht, fand für dieselbe Leistung zwei Preise. Die Startseite verspricht „Was es kostet, steht hier.“ Das Versprechen hielt nicht.

Der Kanon selbst war eindeutig: Der Kommentar zu `HU_TRACKING_STANDARD_SETUP` nennt ihn den Endkundenpreis des Server-Side-Setups, und der White-Label-Preis dafür (900 €) ist daraus mit rund 30 % Abstand abgeleitet. Falsch waren die beiden Oberflächen, die sich den Betrag für die clientseitige Stufe geliehen haben, weil diese keinen eigenen Preis hatte.

## Entscheidung

| Stufe | Name | Umfang | Preis netto | Lieferzeit |
|---|---|---|---|---|
| 1 | Conversion-Tracking | GA4, Tag Manager, Consent Mode, Google Ads, bis zu drei Haupt-Conversions, Abnahmeprotokoll | **890 €** (neu) | 1 bis 2 Wochen |
| 2 | Server-Side Tracking | Stufe 1 plus Server-GTM auf eigener Subdomain, Enhanced Conversions, Paralleltest | 1.290 € (unverändert) | 2 bis 3 Wochen |
| 3 | Server-Side mit Meta | Stufe 2 plus Meta Conversion API mit Deduplizierung, bis zu acht Events | 1.900 € (unverändert) | 2 bis 3 Wochen |
| 4 | Tracking bis ins CRM | CRM-Status und Offline-Conversions als Rücksignal | ab 3.500 € (unverändert) | nach Aufnahme |

Name, Umfang, Preis und Lieferzeit stehen einmal in `hu_tracking_product_ladder()` (`blocksy-child/inc/canon/pricing-canon.php`). Jede Seite zeigt einen Ausschnitt daraus:

- Startseite: Stufe 1 als Angebot, Stufen 2 bis 4 als Satz darunter.
- `/ga4-tracking-setup/`: alle vier Stufen.
- `/server-side-tracking-b2b/`: Stufen 2 bis 4, Stufe 1 als Verweis.
- `/performance-marketing/`: der Einstiegspreis.

Tracking Care bleibt an die Server-Side-Stufen gebunden. Stufe 1 hat keinen eigenen Server, also keine Infrastruktur, die laufend zu prüfen wäre.

## Herleitung der 890 €

Veröffentlichte Festpreise im DACH-Markt, abgerufen am 2026-09-26:

| Anbieter | Paket | Preis | Umfang |
|---|---|---|---|
| [EOM](https://eom.de/agency/ga4-einrichten) (Agentur) | GA4 Basis | 990 € netto | GTM, Consent, 1 Conversion, kein Google Ads |
| [Galineo](https://www.galineo.com/services/tracking-setup) (Agentur) | GA4 + Ads Komplett | 990 € | GA4, Events, Google-Ads-Verknüpfung, Conversion-Import |
| [Tobias Batke](https://tobiasbatke.com/preise/) (Freelancer, Server-Side) | Analytics / 1 Plattform / 2 Plattformen / Komplex | 1.500 € / 1.900 € / 2.300 € / ab 3.500 € | GA4 serverseitig; plus Ads oder Meta; beide dedupliziert; CRM |
| [traffic3](https://traffic3.net/wissen/webanalyse/serverside-tracking/kosten) (Agentur, Ratgeber) | Implementierung Server-Side | 500 bis 1.000 € je Datenempfänger | Richtwert, kein Paket |

Daraus:

1. Die Server-Side-Stufen liegen heute 15 bis 30 % unter dem einzigen veröffentlichten Spezialisten-Festpreis (1.290 € gegen 1.500 € bzw. 1.900 € für eine Plattform; 1.900 € gegen 2.300 € für Google und Meta). Stufe 4 liegt gleichauf.
2. Der Marktanker für ein clientseitiges Setup liegt bei 990 €, bei EOM mit nur einer Conversion und ohne Google Ads. Stufe 1 enthält drei Conversions, Google Ads, Consent Mode und ein Abnahmeprotokoll.
3. 890 € liegen rund 10 % unter dem Anker bei größerem Umfang. Das ist ein Preisvorteil mit Begründung, kein Discount-Signal. Ein Abschlag im Umfang der Server-Side-Stufen (rund 20 %, also etwa 790 €) hätte die Stufe billig wirken lassen.
4. Der Abstand von 400 € zu Stufe 2 entspricht dem Mehraufwand für Server-Container, DNS, Enhanced Conversions und Paralleltest. Er ist klein genug, dass Server-Side bei echtem Bedarf keine Budgetfrage wird, und groß genug, dass Stufe 1 kein Lockangebot für Stufe 2 ist.
5. 890 folgt der Preisgrammatik des Kanons (390, 590, 690, 1.290).

Nicht belegt und deshalb nicht verwendet: Stundensätze aus Ratgeberseiten, deren Quelle nicht abrufbar war, und Plattformpreise (Fiverr, Upwork), die einen anderen Markt abbilden.

## Folgen für White-Label

Keine. Der Margenblock vergleicht weiter das Server-Side-Setup (Endkunden 1.290 €, Agenturen ab 900 €). Für Stufe 1 gibt es kein White-Label-Gegenstück, also auch keinen Margenvergleich.

## Absicherung

- `scripts/canon-forbidden-values.txt`, Regel `preis-tracking-leiter`: ein Betrag der Leiter als Literal neben Tracking-Begriffen bricht den Build.
- Die Preise stehen nur als Konstanten im Kanon. Templates, FAQ, Meta-Descriptions und Schema lesen `hu_tracking_price()`, `hu_tracking_product_ladder()` oder `hu_tracking_ladder_display()`.

## Wieder prüfen, wenn

- fünf Projekte der Stufe 1 verkauft sind: Wie viele davon brauchten mehr als drei Conversions? Liegt der Anteil über der Hälfte, ist der Umfang falsch geschnitten, nicht der Preis.
- in einem Quartal mehr als die Hälfte der Tracking-Anfragen direkt Stufe 2 oder höher wird: Dann trägt Stufe 1 nicht als Einstieg, und die Startseite sollte mit Server-Side führen.
- ein Wettbewerber aus der Tabelle seine Preise sichtbar ändert.
