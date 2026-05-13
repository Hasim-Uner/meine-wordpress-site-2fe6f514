# Brand & Copy Standards

Single source of truth for positioning, tone, and copy direction.
Skills reference this file instead of duplicating brand rules.

## Identity

- Role: **Architekt für eigene Anfrage-Systeme**
- Entity: Haşim Üner, hasimuener.de
- Not: WordPress-Agentur, B2B-Generalist, Performance-Marketing-Agentur, Webdesign-Dienstleister

## Positioning

**Ich baue Solar- und Wärmepumpen-Anbietern im DACH-Raum eigene Anfrage-Systeme, die Portal-Abhängigkeit ablösen und Leadkosten messbar senken.**

- Zielgruppe: Solar-, Wärmepumpen-, Speicher- und Energie-Anbieter mit eigenem Vertrieb (typ. 10–25 Mitarbeiter)
- Wettbewerb: Lead-Portale (Aroundhome, Check24, DAA) — nicht Webdesign-Agenturen
- Website, Tracking, Vorqualifizierung und Werbekanal-Steuerung sind ein verbundenes System
- Eigene Nachfrage-Infrastruktur vor Portal-Zukauf
- Diagnose vor Pitch
- Klarheit vor Feature-Count

## Offer Ladder

```
System-Diagnose -> Blueprint -> Umsetzung / Retainer
```

- Primary CTA: Anfrage-System-Analyse (`/anfrage-system-analyse/`)
- Retired Pfade: `/growth-audit/`, `/kostenlose-tools/`, `/tools/`, `/roi-rechner/` und alte Audit-Aliasse leiten auf `/anfrage-system-analyse/`
- Die System-Diagnose ist diagnostischer Einstieg, kein gimmicky Gratis-Tool

## Tone

- Klar, direkt, handwerklich, entscheidungssicher
- Sprache für Geschäftsführer und Vertriebsleiter, nicht für Marketing-Abteilungen
- Technische Klarheit vor kosmetischer Politur

## Preferred Terms

`Anfrage-System`, `eigene Anfragen`, `Portal-Abhängigkeit`, `Leadkosten`,
`Kosten pro Anfrage`, `qualifizierte Anfragen`, `Abschlussquote`,
`Vorqualifizierung`, `Tracking`, `Nachfrage-Infrastruktur`,
`System-Diagnose`, `Potenzial-Check`, `priorisierte Hebel`,
`Solar`, `Wärmepumpe`, `Speicher`, `Energie-Anbieter`, `Handwerk`

## Anti-Patterns (Hard Bans)

- `Growth Audit` als user-facing Label (interne URL/IDs dürfen bleiben)
- `WGOS` und `WordPress Growth Operating System` — Legacy-Framework, wird nicht mehr aktiv verkauft, zugehörige Seite ist noindex
- `KI-Integration` als Angebot — Legacy-Thema, zugehörige Seite ist noindex
- `WordPress` als Angebot oder Rolle (nur als Technologie im Nebensatz erlaubt)
- `B2B` als Positionierung (zu generisch — wir sprechen Solar/Wärmepumpe)
- `WordPress Specialist`, `WordPress-Agentur`, `Webdesign`, `Leistungen`
- `Growth Architect`, generische Growth-/Marketing-Blasen-Begriffe
- Shopify als Live-Positionierung
- `kostenlos` als alleiniges Wertversprechen
- Tool-artige Rahmung der Diagnose
- Überhöhte Umsatzversprechen
- Gleichgewichtiger Leistungskatalog statt Diagnose-first-Funnel
- Copy, die Taktik vor Diagnose verkauft

## Brand Colors (Project Override)

- Primary brand accent: `#b46a3c` (copper)
- HSL reference: `23 50% 47%`
- Red accent in design system maps to this copper tone for this project

## Hybrid Model

- Structure, templates, helpers, CSS, JS, schema live in the repo
- Homepage and service-page copy can live in the WordPress editor
- Always separate changes into: `Copy`, `Structure`, `Template`, `Refactor`, `Manual WP`
