# Homepage Conversion Copy Audit — 2026-05-28

Scope: `blocksy-child/front-page.php`

Goal: sharpen the homepage for qualified Solar-/Wärmepumpen-/Speicher inquiries by reducing abstract system language, increasing buyer-pain clarity, and making the Marketcheck the obvious next diagnostic step.

## Executive Verdict

The homepage already has a strong strategic frame and strong proof. The main conversion problem is not weak substance. The problem is that the first screen and several routing surfaces still speak too much in internal architecture language before the visitor fully understands the business pain and the concrete result.

Current copy often says:

- Infrastruktur
- autarke Nachfrage-Kraftwerke
- System-Intake
- Daten-Integrität
- Prozess-Kaskade
- Asset-Ownership

This is ownable, but it requires interpretation. Cold traffic needs the pain first:

- Portal-Leads are expensive and shared.
- The website does not produce predictable qualified inquiries.
- Tracking does not show which channel becomes revenue.
- Sales wastes time on poor-fit contacts.
- The company rents demand instead of owning its own acquisition path.

## P0 — Critical Conversion Blockers

### P0.1 Hero promise is memorable but not instantly concrete

Affected file: `blocksy-child/front-page.php`

Current hero:

```html
Ich baue autarke
Nachfrage-Kraftwerke.
Für Ihren eigenen Vertrieb.
```

Issue: strong metaphor, but the concrete buyer problem is hidden one layer down. A Geschäftsführer or Vertriebsleiter must translate the metaphor before understanding the value.

Why it hurts conversion: the first screen should make the visitor feel, “this is exactly my problem”, not “interesting positioning”.

Recommended replacement:

```html
Weniger Portal-Leads.
Mehr eigene Anfragen.
Für Ihren Vertrieb.
```

Alternative, stronger but longer:

```html
Senken Sie Ihre Leadkosten.
Bauen Sie eigene Anfragen auf.
Für Solar, Wärmepumpe und Speicher.
```

Recommended subline:

```html
Für Solar-, Wärmepumpen- und Speicher-Anbieter im DACH-Raum, die weniger abhängig von Lead-Portalen werden wollen. Ich prüfe, wo Ihre Website, Vorqualifizierung, Tracking und Werbekanäle aktuell Anfragen verlieren — und welche Hebel zuerst greifen.
```

Priority: P0

### P0.2 Hero bullets contain internal language and outdated expectation risk

Current bullets:

```html
Architektonischer System-Intake statt Software-Score
Persönliche Diagnose der Region innerhalb von 48 Stunden
Fokus: Solar, Wärmepumpe, Speicher
```

Issue: “architektonischer System-Intake” is internal language. “48 Stunden” may also conflict with other marketcheck rules if the active flow no longer promises old manual-review timing.

Recommended bullets:

```html
Portal-Abhängigkeit, Leadkosten und Anfragequalität prüfen
Website, Tracking und Vorqualifizierung als ein System bewerten
Fokus: Solar, Wärmepumpe, Speicher und hohe Projektwerte
```

Priority: P0

### P0.3 Primary gateway CTA says “Infrastruktur prüfen” instead of buyer outcome

Current Marketcheck gateway:

```php
'title' => 'Der 60-Sekunden-Marktcheck',
'desc'  => 'Identifiziert die unsichtbaren Anfragebremsen auf der aktuellen B2B-Website. Persönliche Rückmeldung statt automatisiertem Tool-Score.',
'label' => 'Infrastruktur prüfen (60-Sek-Marktcheck)',
```

Issue: “Infrastruktur” is not the buyer’s felt problem. The phrase is technically correct but not emotionally sharp.

Recommended:

```php
'title' => 'Der 60-Sekunden-Marktcheck',
'desc'  => 'Prüft, wo Portal-Abhängigkeit, Website, Tracking oder Vorqualifizierung aktuell qualifizierte Anfragen kosten.',
'label' => 'Leadkosten & Anfrage-System prüfen',
```

Priority: P0

## P1 — High-Leverage Improvements

### P1.1 Gateway order is right, but labels create CTA drift

Current gateways:

- Marktcheck
- WordPress Agentur Hannover
- E3 Methodik

This is structurally fine. But the “WordPress Agentur Hannover” gateway should be framed clearly as secondary technical route, not equal strategic path.

Recommended Agentur gateway copy:

```php
'title' => 'Technische Umsetzung mit WordPress',
'desc'  => 'Für Unternehmen, bei denen WordPress, SEO, Tracking und Conversion-Führung technisch sauber zusammengeführt werden müssen.',
'label' => 'Technischen Umsetzungsweg ansehen',
'persona' => 'Für bestehende Websites mit Systembedarf',
```

Reason: the brand standard says WordPress should not become the core role. It can stay as local SEO capture and technology surface, but not as the primary offer.

Priority: P1

### P1.2 Loss-grid headline is strong, but first section should name the money leak earlier

Current:

```html
Drei Lecks, die jedes Wachstums-Budget aufzehren.
```

Recommended:

```html
Drei Stellen, an denen Solar- und SHK-Betriebe qualifizierte Anfragen verlieren.
```

Recommended intro:

```html
Mehr Reichweite hilft wenig, wenn Tracking blind ist, Portal-Leads geteilt werden und kaufnahe Besucher keinen klaren nächsten Schritt finden.
```

Reason: less generic “Wachstums-Budget”, more category-specific buyer pain.

Priority: P1

### P1.3 Proof section should state the before/after mechanism in one sentence

Current proof section is good but slightly understated:

```html
Vom Lead-Einkauf zur eigenen Pipeline.
```

Recommended:

```html
E3 New Energy: von eingekauften Leads zu eigenen qualifizierten Anfragen.
```

Recommended proof subline:

```html
In 6 Monaten sanken die Kosten pro Anfrage von 150 € auf 22 € — nicht durch einen schöneren Relaunch, sondern durch eigene Website-Strecke, Vorqualifizierung und belastbares Tracking.
```

Priority: P1

### P1.4 Category break is good but arrives too late

The “Warum dies kein Webdesign-Projekt ist” section is strategically valuable. It should remain. But its core message should be foreshadowed earlier in the hero or first loss grid.

Recommended microcopy near first CTA:

```html
Keine neue Website auf Verdacht. Erst Diagnose, dann Entscheidung.
```

Priority: P1

### P1.5 About section repeats the metaphor instead of strengthening trust

Current heading:

```html
Ich entwickle autarke Nachfrage-Kraftwerke. Digital.
```

Recommended:

```html
Ich baue Anfrage-Systeme, die Vertrieb und Daten zusammenbringen.
```

Recommended lead:

```html
Für Betriebe, die nicht dauerhaft Leads mieten wollen, sondern wissen müssen, welcher Kanal echte Projekte bringt — und wem die Anfrage am Ende gehört.
```

Priority: P1

### P1.6 FAQ should reduce risk, not just explain process

Current first FAQ answer promises a manually checked result within 48 hours. If this is no longer the operational promise, remove it immediately.

Recommended answer:

```html
Der Marktcheck ordnet Ihre Domain, Ihr Zielgebiet und Ihre aktuelle Anfrage-Strecke ein. Sie sehen, ob der Engpass eher bei Nachfrage, Website, Tracking, Vorqualifizierung oder Vertriebspfad liegt — und welcher nächste Schritt sinnvoll ist.
```

Priority: P1 if old 48h promise is no longer true. P0 if it is operationally false.

## Recommended First Implementation Patch

Do not rewrite the whole homepage at once. First patch only the highest-signal surfaces:

1. Hero H1
2. Hero subline
3. Hero bullets
4. Marketcheck gateway desc + CTA label
5. Loss-grid headline + intro
6. Proof headline + subline
7. About heading + lead
8. Final routing headline + intro

## Proposed Final Routing Copy

Current:

```html
Wählen Sie die Route, die zu Ihrem Reifegrad passt.
Kein Pitch. Drei klare Einstiege — jede führt zu einem konkreten, prüfbaren Schritt.
```

Recommended:

```html
Finden Sie heraus, wo Ihr Anfrage-System zuerst Geld verliert.
Starten Sie mit dem Marktcheck, prüfen Sie den E3-Case oder gehen Sie direkt in die technische Umsetzung — je nachdem, wo Sie gerade stehen.
```

Priority: P1

## Measurement Tasks

Track these as distinct events if not already fully visible in analytics:

- hero marketcheck CTA click
- gateway marketcheck click
- gateway proof click
- loss-grid marketcheck CTA click
- proof case click
- about marketcheck click
- final marketcheck click
- final contact click
- FAQ first open / FAQ cost open

Segment at least by:

- homepage → marketcheck
- homepage → E3 case
- homepage → WordPress Agentur Hannover
- homepage → contact

## Do Not Touch

Keep these elements:

- E3 numbers as the main proof asset
- diagnosis-first funnel
- “Portal-Abhängigkeit” as core enemy
- Founding-Partner scarcity if operationally true
- direct personal positioning instead of agency/team language
- the visual system if performance is fine; the copy problem is bigger than the design problem

## Implementation Note

Best next commit: small copy-only patch in `blocksy-child/front-page.php`. Avoid structural changes until the first-screen copy and CTA hierarchy are sharper.
