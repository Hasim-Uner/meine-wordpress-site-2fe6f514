# 0011 Founding Cohort 2026 entfernt

- Datum: 2026-07-28
- Status: angenommen
- Loest ab: [0007](0007-founding-cohort-2026.md)

## Entscheidung

Der Angebotsrahmen `Founding Cohort 2026` wird vollstaendig zurueckgezogen. Aus
Website, Theme-Code und Projektsprache verschwinden:

- die Programm-Marke `Founding Cohort 2026`
- die Begriffe `Founding-Partner` und `Founding-Konditionen`
- der Platzzaehler (`3/3 Plaetze`, `3 von 3 Plaetzen offen`)
- die Bewerbungsfrist 2026-09-30

Wo ein Betrieb benannt werden muss, der die Umsetzungsstufe erreicht, heisst er
`Umsetzungspartner`. Die Selektion selbst bleibt: der Marktcheck entscheidet
weiterhin ueber Fit, und eine Absage bleibt ein zulaessiges Ergebnis.

## Begruendung

Der Rahmen band drei Zusagen aneinander, die nicht mehr zusammen passten: eine
oeffentliche Platzzahl, eine feste Frist und einen daran gekoppelten Preis. Ein
Zaehler, der dauerhaft auf `3/3` steht, belegt keine Knappheit, sondern
entwertet sie. Ohne den Zaehler traegt die Seite die Auswahl-Logik ohnehin
selbst: der Marktcheck qualifiziert, der Befund entscheidet.

## Konsequenzen

- `blocksy-child/inc/canon/founding-canon.php`, der Block
  `blocksy-child/inc/components/founding-cohort-block.php` und
  `blocksy-child/assets/css/founding-cohort.css` sind geloescht; der Shortcode
  `[hu_founding]` existiert nicht mehr.
- Die Solar-Landingpage schliesst mit einer reinen Final-CTA-Sektion
  (`#final-cta`, `data-track-section="final_cta"`); die Sektions-ID `#founding`
  und der Tracking-Wert `founding_cohort` entfallen.
- `messaging-canon.php` fuehrt `Umsetzungspartner` als bevorzugten Begriff. Die
  Liste der verbotenen Begriffe bleibt unveraendert.
- Offen und bewusst nicht mitentschieden: die Preisfrage. Die Konstanten in
  `pricing-canon.php` (Foundation 9.900 vs. 14.900, Performance-Rate, Rabattsatz)
  bleiben unveraendert stehen, weil sie ueber `[hu_price]` in WordPress-Inhalten
  referenziert sein koennen. Welcher Preis ohne Kohorten-Rahmen gilt, ist eine
  separate Entscheidung.
