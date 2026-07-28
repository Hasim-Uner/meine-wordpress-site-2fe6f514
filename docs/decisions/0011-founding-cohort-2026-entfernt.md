# 0011 Founding Cohort 2026 entfernt

- Datum: 2026-07-28
- Status: angenommen
- Löst ab: [0007](0007-founding-cohort-2026.md)

## Entscheidung

Der Angebotsrahmen `Founding Cohort 2026` wird vollständig zurückgezogen. Aus
Website, Theme-Code und Projektsprache verschwinden:

- die Programm-Marke `Founding Cohort 2026`
- die Begriffe `Founding-Partner` und `Founding-Konditionen`
- der Platzzähler (`3/3 Plätze`, `3 von 3 Plätzen offen`)
- die Bewerbungsfrist 2026-09-30

Wo ein Betrieb benannt werden muss, der die Umsetzungsstufe erreicht, heißt er
`Umsetzungspartner`. Die Selektion selbst bleibt: der Marktcheck entscheidet
weiterhin über Fit, und eine Absage bleibt ein zulässiges Ergebnis.

## Begründung

Der Rahmen band drei Zusagen aneinander, die nicht mehr zusammen passten: eine
öffentliche Platzzahl, eine feste Frist und einen daran gekoppelten Preis. Ein
Zähler, der dauerhaft auf `3/3` steht, belegt keine Knappheit, sondern entwertet
sie. Ohne den Zähler trägt die Seite die Auswahl-Logik ohnehin selbst: der
Marktcheck qualifiziert, der Befund entscheidet.

## Konsequenzen

- `blocksy-child/inc/canon/founding-canon.php`, der Block
  `blocksy-child/inc/components/founding-cohort-block.php` und
  `blocksy-child/assets/css/founding-cohort.css` sind gelöscht; der Shortcode
  `[hu_founding]` existiert nicht mehr.
- Die Solar-Landingpage schließt mit einer reinen Final-CTA-Sektion
  (`#final-cta`, `data-track-section="final_cta"`); die Sektions-ID `#founding`
  und der Tracking-Wert `founding_cohort` entfallen.
- `messaging-canon.php` führt `Umsetzungspartner` als bevorzugten Begriff. Die
  Liste der verbotenen Begriffe bleibt unverändert.
- Offen und bewusst nicht mitentschieden: die Preisfrage. Die Konstanten in
  `pricing-canon.php` (Foundation 9.900 vs. 14.900, Performance-Rate, Rabattsatz)
  bleiben unverändert stehen, weil sie über `[hu_price]` in WordPress-Inhalten
  referenziert sein können. Welcher Preis ohne Kohorten-Rahmen gilt, ist eine
  separate Entscheidung.
