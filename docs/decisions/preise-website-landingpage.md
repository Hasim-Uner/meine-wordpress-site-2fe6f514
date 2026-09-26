# Preise: Website Kompakt und Landingpage

Entscheidung: 2026-09-26, vom Betreiber bestätigt. Werte im Kanon
`blocksy-child/inc/canon/pricing-canon.php`.

## Entscheidung

| Produkt | Preis | Umfang |
|---|---|---|
| Website Kompakt | 2.490 € netto, Festpreis | bis zu 3 Seiten mit Kontaktformular; Texte liefert der Kunde, Struktur und Feinschliff sind enthalten |
| Zusatzseite | 290 € netto je Seite | jede Seite über die drei hinaus |
| Landingpage | 1.990 € netto, Festpreis | eine Seite, ein Angebot, ein Ziel; Text, Anfrageformular und Herkunftsmessung enthalten |
| White-Label-Landingpage | ab 1.390 € netto | unverändert im Umfang, Preis von ab 1.900 € gesenkt |

Abgelöst: Website ab 3.400 € ohne Seitenangabe, White-Label-Landingpage ab
1.900 €. Beide alten Beträge stehen in `scripts/canon-forbidden-values.txt`
(`website-3400`, `wl-landingpage-1900`).

## Begründung

- **Website.** 3.400 € lag für zwei bis drei Seiten über der Marktmitte. Die
  Preise, die Anbieter auf Seite 1 bei Google selbst nennen
  (DataForSEO-Live-SERP, 2026-09-26, `wordpress website erstellen lassen kosten`
  und `wordpress freelancer website preis`): kleine Websites 1.500 bis
  2.500 € (ucentric-media), fünf Unterseiten 2.000 bis 4.000 € (graphek),
  Starter bis zehn Seiten ab 1.490 € (ci-commerce), Freelancer 2.000 bis
  10.000 € (Kopf & Stift). 2.490 € liegt in der Mitte und ist nicht der
  Billigpreis. Der Zusatzpreis pro Seite lässt das Paket mitwachsen, ohne
  jedes Projekt neu zu verhandeln.
- **Landingpage.** Marktpreise laut SERP zu `landingpage erstellen lassen`:
  899 € (kigoo, Vorlage in 24 Stunden), ab 1.500 € (ucentric-media), 1.500 bis
  8.000 € (cookie.design). Die Landingpage liegt unter Website Kompakt, weil
  Käufer eine Seite günstiger erwarten als mehrere. Die Differenz erklärt sich
  über den Inhalt: Text und Messung statt Seitenzahl.
- **White-Label.** Auf jeder Sprosse der Partnerleiter zahlen Agenturen rund
  30 % weniger als Endkunden (siehe Kommentar im Kanon). Mit einem öffentlichen
  Endkundenpreis von 1.990 € für die Landingpage hätte ab 1.900 € keinen
  Margenspielraum gelassen; 1.390 € hält den Abstand.

## Grenzen

- Keine direkten Kundenaussagen zu Landingpage-Käufern im Repo
  (`docs/standards/VOICE_OF_CUSTOMER.md`). Die Marktpreise sind
  Wettbewerberangaben, kein Käuferbeleg.
- Der Versuch Ersteinschätzung läuft bis 2026-11-20. Preiskorrekturen sind
  laut Versuchsregel erlaubt. Ein niedrigerer Website-Preis kann aber mehr
  direkte Projektanfragen bringen; die Änderung ist deshalb in
  `docs/experimente/ersteinschaetzung.md` datiert.

## Absicherung

`npm run test:pricing` prüft: Landingpage günstiger als Website Kompakt,
Zusatzseite günstiger als Landingpage, Agentur-Landingpage mindestens 25 %
unter dem Endkundenpreis.
