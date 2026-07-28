# 0009 Fix-Preis-Foundation-Pricing

- Datum: 2026-05-02
- Status: proposed

## Entscheidung

WGOS Foundation wird der verpflichtende Umsetzungseinstieg. Performance und Premium-Layer sind optionale Add-ons.

Die Foundation kostet in der Founding Cohort 2026 9.900 EUR. Der Standardpreis ab 2027 beträgt 14.900 EUR. Die geplante Dauer liegt bei 8 bis 10 Wochen.

> Nachtrag 2026-07-28: Der Kohorten-Rahmen ist mit [0011](0011-founding-cohort-2026-entfernt.md) zurückgezogen. Die beiden Preisstufen stehen unverändert im Canon, sind aber nicht mehr an eine Kohorte gebunden — welcher Preis öffentlich gilt, ist offen.

## Begründung

Setup-plus-Retainer-Bundles vermischen Implementierung, Betrieb und Wachstum. Ein fixer Foundation-Preis macht den ersten Kauf klarer, reduziert Scope-Diskussionen und trennt Systemaufbau von laufender Optimierung.

## Konsequenzen

- Foundation garantiert ein funktionsfähiges Anfrage-System, kein Anfrage-Volumen.
- Volumenzusagen dürfen nicht als Landingpage-Headline genutzt werden.
- Performance ist optional ab Monat 3.
- Premium-Layer ist nur zusätzlich zu Performance möglich.
- Preise, Laufzeiten und Wertanker leben in `blocksy-child/inc/canon/pricing-canon.php`.
