# Preisarchitektur: Anfragestrecke, SEO-Befund, ein Hauptziel auf der Startseite

Freigabe durch Haşim am 22.10.2026 („setz das so um, wie du denkst“) nach
Preis- und Funnel-Prüfung der Live-Seite.

## Befund

- Der dokumentierte Fall (Landingpages, Vorqualifizierung, Tracking, CRM-Übergabe)
  hatte auf der Startseite kein kaufbares Produkt. Größtes Direktprodukt war die
  Landingpage; das Komplettsystem gab es nur in der Solar-Vertikale (9.999 €).
- Technisches SEO war das einzige Produkt ohne Preis, obwohl die Startseite
  verspricht, dass alle Preise dort stehen.
- Die Startseite hatte zwei Hauptziele (Kopf: Ersteinschätzung, Hero: Projekt).
- Drei Organisations- und Essayseiten waren die einzigen anklickbaren Arbeitsproben.

## Entscheidung

| Produkt | Preis netto | Herleitung |
| --- | ---: | --- |
| Anfragestrecke | 3.490 € | Landingpage inkl. Text + Conversion-Tracking + mehrstufiges Formular, Benachrichtigung, Bestätigung mit Terminlink |
| Anfragestrecke mit CRM | 5.490 € | zusätzlich CRM Standard, Server-Side statt Browser-Messung, Rückgabe qualifizierter Anfragen an Google Ads |
| Technischer SEO-Befund | 690 € | fester Einstieg wie Conversion-Analyse; bei Beauftragung der Korrekturen angerechnet |

Kanon: `HU_ANFRAGESTRECKE_PRICE`, `HU_ANFRAGESTRECKE_CRM_PRICE`,
`HU_SEO_FINDING_PRICE` in `blocksy-child/inc/canon/pricing-canon.php`.
Der Entwurf „Anfragestrecken ab 2.700 €“ (25.09.2026) ist abgelöst und gesperrt.

Solar-Vertikale: Anfragesystem von 9.999 € auf 9.900 € netto (Freigabe
22.10.2026, Kanon `HU_FOUNDATION_PRICE_STANDARD`). Die 9-9-9-Endung passte
nicht zum Ton der übrigen Preise. Weitere Produktstrecke unverändert.

Marktcheck kostenlos statt 99 € netto (Freigabe 22.10.2026): Die Hürde zum
ersten Kontakt soll so klein wie möglich sein, und neben der kostenlosen
Ersteinschätzung war ein bezahlter Marktcheck nicht mehr schlüssig. Auf den
Seiten steht „kostenlos“; Kopf und Leiter zeigen aus Platzgründen „0 €“, wie
die Ersteinschätzung. Die Anrechnungs-Sätze entfallen.

Unverändert und bewusst nicht angefasst:

- Anfrage-Website (Konfigurator, 1.900 € für Grundsystem und erste Hauptseite,
  Seitentypen, Texterstellung optional). Ein zwischenzeitlicher Vorschlag
  „2.490 € für drei Seiten“ beruhte auf einer veralteten, gecachten Live-Seite.
  Die Startseitenkarte sagt jetzt, dass Texte geliefert oder dazugebucht werden,
  damit der Vergleich zur Landingpage (Text inklusive) lesbar bleibt.
- White-Label-Leiter, Tracking-Leiter, Übernahme-Check, Kontingente.

Startseite:

- Neue Karte 03 „Anfragestrecke“ (`#angebot-anfragestrecke`), Fall verweist darauf.
- Kopf-Tür auf der Startseite: Projektanfrage, auch bei aktivem Ersteinschätzungs-Versuch.
- Ersteinschätzung als URL-Feld unter den Preisen und im Abschluss; /kontakt/
  übernimmt `url=` (https:// wird ergänzt, fremde Schemata verworfen).
- Arbeitsprobe: die eigene Solar-Money-Page als Anfragestrecke, ausdrücklich als
  eigenes Angebot bezeichnet. Die Organisationsseiten bleiben auf `/referenzen/`
  und der White-Label-Seite.

## Auswirkung auf den Versuch Ersteinschätzung

Die Kopf-Tür war Teil des Versuchs (`docs/experimente/ersteinschaetzung.md`).
Daten vor und nach dem 22.10.2026 werden getrennt betrachtet.
