# Brand & Copy Standards

Single source of truth for positioning, tone, and copy direction.
Skills reference this file instead of duplicating brand rules.

## Identity

- Role: **Architekt für eigene Anfrage-Systeme**
- Entity: Haşim Üner, hasimuener.de
- Not: WordPress-Agentur, B2B-Generalist, Performance-Marketing-Agentur, Webdesign-Dienstleister

## Positioning

**Ich baue Solar- und Wärmepumpen-Anbietern im DACH-Raum eigene Anfrage-Systeme, die Portal-Abhängigkeit ablösen und Leadkosten messbar senken.**

- Zielgruppe: Solar-, Wärmepumpen-, Speicher- und Energie-Anbieter mit eigenem Vertrieb, hohen Projektwerten und klarem Zielgebiet
- Wettbewerb: Lead-Portale (Aroundhome, Check24, DAA) — nicht Webdesign-Agenturen
- Website, Tracking, Vorqualifizierung und Werbekanal-Steuerung sind ein verbundenes System
- Eigene Nachfrage-Infrastruktur vor Portal-Zukauf
- Diagnose vor Pitch
- Klarheit vor Feature-Count

## Offer Ladder

```
Marktcheck -> Anfrage-System-Analyse -> Umsetzung / Retainer
```

- Primary CTA: Marktcheck (`/solar-waermepumpen-leadgenerierung/#marktcheck`)
- Retired Pfade: `/growth-audit/`, `/anfrage-system-analyse/` und alte Audit-Aliasse leiten auf den Marktcheck; alte Tool-/ROI-/Service-Slugs liefern 410 statt als Redirect-Netz betrieben zu werden. WGOS-Altpfade bleiben noindex/access-protected, solange sie intern noch gebraucht werden
- Der Marktcheck ist diagnostischer Einstieg, kein gimmicky Gratis-Tool

## Tone

- Klar, direkt, handwerklich, entscheidungssicher
- Sprache für Geschäftsführer und Vertriebsleiter, nicht für Marketing-Abteilungen
- Technische Klarheit vor kosmetischer Politur

## Preferred Terms

`Anfrage-System`, `eigene Anfragen`, `Portal-Abhängigkeit`, `Leadkosten`,
`Kosten pro Anfrage`, `qualifizierte Anfragen`, `Abschlussquote`,
`Vorqualifizierung`, `Tracking`, `Nachfrage-Infrastruktur`,
`System-Diagnose`, `Potenzial-Check`, `priorisierte Hebel`,
`Solar`, `Photovoltaik`, `PV`, `Wärmepumpe`, `Speicher`, `Energie-Anbieter`, `Handwerk`

`Founding Cohort 2026`, `Founding-Partner` und `Founding-Konditionen` sind
zurückgezogen und dürfen nicht mehr in Kundencopy auftauchen — ebenso wenig
Platzzähler oder Bewerbungsfristen. Wer den Betrieb hinter einer Umsetzung
benennen muss, schreibt `Umsetzungspartner` und erklärt ihn bei der ersten
sichtbaren Nutzung: kein Mitgründer, kein Anteilseigner, keine
gesellschaftsrechtliche Partnerschaft.

## Solar / Photovoltaik / PV

Die drei Begriffe sind keine Synonyme und haben feste Rollen. Sie stehen
absichtlich nebeneinander — wer sie gegeneinander austauscht, verliert
entweder den Suchbegriff oder die Präzision.

| Begriff | Rolle | Wo |
| --- | --- | --- |
| `Solar` | Zielgruppen- und Marken-Rahmen: **wen** wir ansprechen | Slug, Header-Nav, Footer, Positionierung, Site-Title |
| `Photovoltaik` | Produkt- und Suchbegriff: **worum** es geht | H1, Fließtext, Meta-Titles, Anchor-Texte |
| `PV` | Branchenkürzel der Betriebe, Insider-Ton | Nur in Komposita: `PV-Anfragen`, `PV-Projekte`, `PV-Termine`, `Gewerbe-PV` |

Regeln:

- **Nicht `Solar` schreiben, wenn konkret die PV-Anlage gemeint ist.** `Solar`
  ist der Oberbegriff und führt Solarthermie mit — ein anderes Gewerk, das nicht
  Zielgruppe ist. (`Solarthermie` kommt im Theme bewusst 0× vor.)
- `Wärmepumpe` bleibt **gleichrangig** neben `Photovoltaik`, nicht nachgeordnet.
  `/waermepumpen-leads/` besitzt die Query `wärmepumpen leads` und wird nicht
  durch PV-lastige Copy auf anderen Seiten verwässert.
- Zielgruppen-Rahmen über Produktaussage ist die gewollte Schichtung, z. B. auf
  der Money Page: Tag „Für Solar- & Wärmepumpen-Betriebe" über der H1
  „… Photovoltaik-Anfragen …".

Belege (nicht raten, nachschlagen):

- `seo-research/2026-07/reports/gsc-verlauf-2026-07-20.md`,
  Kannibalisierungs-Karte: „**Money-Page besitzt die Query**
  `leadgenerierung photovoltaik`" → deshalb trägt die Money-Page-H1
  `Photovoltaik` und nicht nur `Solar`.
- `seo-research/2026-07/data/keywords-master.csv`, Cluster `a-money`:
  Photovoltaik-Terme tragen das größte Volumen, `PV` folgt, `Solar` liegt
  darunter. Zahlen dort nachsehen statt hier duplizieren.

## Anti-Patterns (Hard Bans)

- `Growth Audit` als user-facing Label (interne URL/IDs dürfen bleiben)
- `WGOS` und `WordPress Growth Operating System` — Legacy-Framework, wird nicht mehr aktiv verkauft, zugehörige Seite ist noindex
- `KI-Integration` als Angebot — Legacy-Thema, zugehörige Seite ist noindex
- `WordPress` als Angebot oder Rolle (nur als Technologie im Nebensatz erlaubt)
- `B2B` als Positionierung (zu generisch — wir sprechen Solar/Wärmepumpe)
- `WordPress Specialist`, `WordPress-Agentur`, `Webdesign`, `Leistungen`
- `Growth Architect`, generische Growth-/Marketing-Blasen-Begriffe
- `Shopify` als Live-Positionierung
- `kostenlos` als alleiniges Wertversprechen
- `Founding Cohort 2026`, `Founding-Partner`, `Founding-Konditionen` — Detail
  oben unter Preferred Terms
- `Solarthermie` — anderes Gewerk, nicht Zielgruppe; Detail unten unter Solar / Photovoltaik / PV
- Tool-artige Rahmung der Diagnose
- Überhöhte Umsatzversprechen
- Gleichgewichtiger Leistungskatalog statt Diagnose-first-Funnel
- Copy, die Taktik vor Diagnose verkauft

## Nebenpfad: White-Label für Agenturen

`/whitelabel-retainer/` ist ein eigener Agentur-Einstieg außerhalb der
Kern-Positionierung: indexierbar, in der Sitemap und über den Navigationspunkt
„Für Agenturen“ erreichbar, aber weiterhin nicht in `llms.txt`. Für diese
eine Route gelten kontrollierte Ausnahmen:

- Zielgruppe: Agenturen (Performance, Webdesign, Full-Service) — Anrede „ihr/euch“
- Rolle dort: White-Label-Partner / „die System-Ebene hinter euren Kundenprojekten“
- WordPress, SEO, Tracking, CRO dürfen als Lieferfelder benannt werden,
  WordPress bleibt Technologie-Nennung, nie Rollen-Claim
- Kein gleichgewichtiger Leistungskatalog: Einstieg immer über Erstprojekt
  mit fixem Scope und Preis vorab, danach Retainer; Fit-Check vor Call
- Proof: ausschließlich anonymisierte Arbeitsprobe (E3-Canon) mit dem
  expliziten Hinweis, dass es kein White-Label-Mandat war; keine erfundenen
  Agentur-Referenzen, keine Akquise im Kundenstamm der Partner
- Schreibweise sichtbar immer „White-Label“ (Slug/interne Keys bleiben)

Diese Ausnahmen gelten nur auf Whitelabel-Routen und rechtfertigen keine
Aufweichung der Solar-/SHK-Kernpositionierung an anderer Stelle.

## Nebenpfad: Lokale WordPress-Route Hannover

`/wordpress-agentur-hannover/` trägt die H1 „WordPress Agentur Hannover" und
steht damit sichtbar gegen die Identity-Zeile „Not: WordPress-Agentur". Das ist
eine bewusste Ausnahme, keine Drift:

- Die Route existiert für eine lokale Suchanfrage, die genau diesen Begriff
  benutzt. Ohne ihn rankt sie nicht, und sie ist die einzige Seite mit
  lokalem Einstieg.
- Der Begriff wird immer qualifiziert („für messbare B2B-Anfragen"), nie allein
  als Rollen-Claim gesetzt. Im Fließtext bleibt die Rolle `Spezialist für
  WordPress als Anfrage-System`, ausdrücklich abgegrenzt gegen die lokale
  Allround-Agentur.
- **Benachbarte Kategoriebegriffe bleiben draußen.** Am 2026-07-09 wurden
  `Webdesign-Agentur`, `Internetagentur` und `Webagentur` über zwei FAQ-Items
  ergänzt, um lokale Zweitbegriffe mitzunehmen. Ergebnis: die Money-Query fiel
  von Position 11 auf 35, die Zielbegriffe bekamen keine einzige Impression.
  Zurückgenommen am 2026-08-08.
- Die Lehre daraus gilt über diese Route hinaus: Der Hard-Ban-Check lässt
  gesperrte Begriffe in der Abgrenzung durch, weil „Architektur statt
  Webdesign" positionierungsseitig ein Gegen-Claim ist. Eine Suchmaschine liest
  denselben Satz ohne Verneinung. Grün im Guard heißt hier nicht unschädlich.

Regressionen auf dieser Route findet
`agents/skills/seo-drift/scripts/drift-report.sh`.

## Brand Colors (Project Override)

- Primary brand accent: `#b46a3c` (copper)
- HSL reference: `23 50% 47%`
- Red accent in design system maps to this copper tone for this project

## Hybrid Model

- Structure, templates, helpers, CSS, JS, schema live in the repo
- Homepage and service-page copy can live in the WordPress editor
- Always separate changes into: `Copy`, `Structure`, `Template`, `Refactor`, `Manual WP`
