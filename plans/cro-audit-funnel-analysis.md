# CRO & Architektur-Analyse: Audit Funnel → Solar/Wärmepumpen Seite

**Datum:** 2026-04-23
**Ziel:** Reibungspunkte im Funnel von `/growth-audit/` zur Solar/Wärmepumpen-Leadgenerierungsseite identifizieren.

---

## 1. Funnel-Architektur (Ist-Stand)

```
User Journey:
┌─────────────────────────────────────────────────────────────────┐
│  /growth-audit/                                                  │
│  - CJA Shortcode: URL-Eingabe, 60-Sekunden-Diagnose              │
│  - Async n8n Webhook → Ergebnis-Dashboard                        │
│  - CTA: "Anfrage-System-Analyse starten" → /anfrage-system-      │
│    analyse/                                                      │
└──────────────────────────────┬──────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│  /solar-waermepumpen-leadgenerierung/                            │
│  - Landing Page mit Pain Cards, Journey Cards, Proof             │
│  - Kontextseite ohne eigenen Formular-Submit                     │
│  - 6 Schritte: Leistung → Zielmarkt → Engpass → Status → Timing  │
│    → Kontaktweg → Kontaktdaten                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. Identifizierte Reibungspunkte

### 2.1 KRITISCH: Messaging-Bruch zwischen Audit und Solar-Seite

**Problem:**
- Audit-Seite (`cja-audit.js` Zeile 73-75) spricht von **"60-Sekunden-Diagnose für WordPress-B2B-Websites"**
- Audit-Ergebnis-CTA (`cja-shortcode.php` Zeile 135-137) sagt: **"Der nächste Schritt ist das qualifizierte Formular für Solar- und Wärmepumpen-Anbieter"**
- Solar-Seite (`page-solar-waermepumpen-leadgenerierung.php` Zeile 223) targetet **"Für Solar- und Wärmepumpen-Betriebe mit 10–25 Mitarbeitern"**

**Reibung:**
Ein Besucher, der den Audit für seine **allgemeine B2B-WordPress-Website** startet, landet auf einer Seite, die explizit nur Solar/Wärmepumpen anspricht. Das ist ein **harter Zielgruppen-Bruch**.

**Betroffene Nutzer:**
- Alle Nicht-Solar/Wärmepumpen-Betriebe, die den Audit testen
- WordPress-Agenturen, Handwerker, Dienstleister aller Branchen

**CRO-Auswirkung:**
Hohe Absprungrate nach Audit-Ergebnis, da der CTA nicht zur ursprünglichen Erwartung passt.

---

### 2.2 KRITISCH: CTA-Text im Audit-Ergebnis ist irreführend

**Problem in [`cja-shortcode.php:135-137`](blocksy-child/inc/cja-shortcode.php:135):**

```php
<p>Der Audit war die Einordnung. Der nächste Schritt ist das qualifizierte Formular 
für Solar- und Wärmepumpen-Anbieter, damit aus Diagnose ein konkretes Projekt wird.</p>
<a href="<?php echo esc_url( $request_url ); ?>" class="cja-cta-button">Anfrage stellen</a>
```

**Reibung:**
- Der Text erwähnt explizit "Solar- und Wärmepumpen-Anbieter" — aber der Audit selbst ist allgemein für "WordPress-B2B-Websites" positioniert
- Der Button-Text "Anfrage stellen" ist zu generisch und erklärt nicht, WAS man anfragt

**Empfehlung:**
- CTA-Text sollte den **Wert** des nächsten Schritts erklären, nicht die Zielgruppe
- Button-Text: "Kostenlose Einordnung anfordern" oder "Persönliche Analyse anfragen"

---

### 2.3 MITTEL: Inkonsistente CTA-Pfade im Audit

**Problem in [`cja-shortcode.php:137-138`](blocksy-child/inc/cja-shortcode.php:137):**

```php
<a href="<?php echo esc_url( $request_url ); ?>" class="cja-cta-button">Anfrage stellen</a>
<a href="#form" class="cja-cta-link">Audit erneut starten</a>
```

**Reibung:**
- Der sekundäre CTA "Audit erneut starten" konkurriert mit dem primären "Anfrage stellen"
- `#form` ist ein interner Anker, der auf derselben Seite bleibt — das verwirrt, weil der User gerade Ergebnisse gesehen hat

**Empfehlung:**
- Sekundären CTA entfernen oder durch sozialen Proof ersetzen (z.B. "1.750+ Anfragen generiert")

---

### 2.4 MITTEL: Solar-Seite hat ZWEI konkurrierende CTAs

**Problem in [`page-solar-waermepumpen-leadgenerierung.php:227-230`](blocksy-child/page-solar-waermepumpen-leadgenerierung.php:227):**

```php
<a href="<?php echo esc_url( $request_url ); ?>" class="nx-btn nx-btn--primary">Anfrage stellen</a>
<a href="<?php echo esc_url( $audit_url ); ?>" class="energy-text-link">Audit starten</a>
```

**Reibung:**
- Die Solar-Seite verlinkt ZURÜCK zum Audit — das erzeugt eine **Endlosschleife**
- User, die vom Audit kommen, sehen "Audit starten" als sekundären CTA — das ist redundant
- Der Funnel sollte **unidirektional** sein: Audit → Solar-Seite → Formular

**Betroffene Stellen:**
- Hero (Zeile 229)
- Proof-Sektion (Zeile 246)
- Footer-CTA (Zeile 589)

**Empfehlung:**
- Auf der Solar-Seite den "Audit starten"-CTA entfernen oder durch etwas anderes ersetzen (z.B. Case Study Link)

---

### 2.5 MITTEL: Tracking-Inkonsistenzen

**Audit-Seite (`cja-shortcode.php`):**
```html
data-track-action="cja_start_analysis"
data-track-category="lead_gen"
data-track-section="growth_audit_input"
```

**Solar-Seite (`page-solar-waermepumpen-leadgenerierung.php`):**
```html
data-track-action="cta_energy_hero_request"
data-track-category="lead_gen"
```

**Problem:**
- `data-track-section` wird im Audit verwendet, aber auf der Solar-Seite teilweise vergessen
- Kein konsistentes Schema für die **Funnel-Stage** (input → results → request)

**Empfehlung:**
- Tracking-Schema vereinheitlichen:
  - `data-track-funnel-stage="audit_input|audit_results|energy_hero|energy_form"`
  - `data-track-action="cta_click"`

---

### 2.6 GERING: Formular-Fortschrittsanzeige ist irreführend

**Problem in [`page-solar-waermepumpen-leadgenerierung.php:350`](blocksy-child/page-solar-waermepumpen-leadgenerierung.php:350):**

```php
<strong id="energy-progress-current">Schritt 1 von 6 — Gleich wird klar, wo der größte Hebel liegt.</strong>
```

**Reibung:**
- 6 Schritte + Kontakt-Schritt = 7 Schritte insgesamt
- Der Text "Gleich wird klar..." ist zu vage — der User weiß nicht, was ihn erwartet

**Empfehlung:**
- Konkreter: "Schritt 1 von 7 — Ihre Leistung"
- Microcopy pro Schritt anpassen

---

### 2.7 GERING: Footer-Audit-CTA wird auf Solar-Seite ausgeblendet

**Problem in [`helpers.php:1008`](blocksy-child/inc/helpers.php:1008):**

```php
function nexus_should_hide_footer_primary_cta() {
    // ...
    'page-solar-waermepumpen-leadgenerierung.php',
```

**Auswirkung:**
- Das ist **korrekt** so — die Solar-Seite hat eigene CTA-Blöcke
- Aber: Der Audit-Seite fehlt diese Logik, dort könnte der Footer-CTA doppelt erscheinen

---

## 3. Zusammenfassung der Probleme nach Priorität

| Priorität | Problem | CRO-Auswirkung |
|-----------|---------|----------------|
| **KRITISCH** | Messaging-Bruch: Allgemeiner Audit → Solar-spezifische Seite | Hohe Absprungrate bei Nicht-Zielgruppe |
| **KRITISCH** | CTA-Text im Audit erwähnt Solar/Wärmepumpen explizit | Verwirrung, Vertrauensverlust |
| **MITTEL** | Solar-Seite verlinkt zurück zum Audit (Endlosschleife) | Funnel-Leak, zirkuläre Navigation |
| **MITTEL** | Zwei konkurrierende CTAs auf Solar-Seite | Entscheidungsparalyse |
| **MITTEL** | Tracking-Inkonsistenzen | Schlechte Messbarkeit |
| **GERING** | Formular-Fortschritt irreführend | Leichte Verunsicherung |

---

## 4. Empfohlene Maßnahmen

### 4.1 Sofort (KRITISCH)

**A) Audit-CTA-Pfad aufteilen nach Zielgruppe**

Option 1: **Zwei separate Audit-Enden**
- Der Audit erkennt anhand der URL oder einer zusätzlichen Frage, ob es sich um Solar/Wärmepumpen handelt
- Entsprechend unterschiedliche CTAs:
  - Solar/Wärmepumpen → `/anfrage-system-analyse/`
  - Andere B2B → `/kontakt/` oder generisches Formular

Option 2: **CTA-Text generalisieren**
- Statt "Solar- und Wärmepumpen-Anbieter" → "Ihr persönliches Anfrage-System"
- Der Zielgruppen-Fit wird erst auf der Folgeseite geklärt

**B) Solar-Seite: "Audit starten"-CTAs entfernen**
- Hero, Proof, Footer-CTA: Nur noch EIN primärer CTA ("Anfrage stellen")
- Sekundär: Case Study oder Trust-Element

### 4.2 Kurzfristig (MITTEL)

**C) Tracking-Schema vereinheitlichen**
- `data-track-funnel-stage` einführen
- Alle CTAs konsistent tracken

**D) Formular-Microcopy verbessern**
- Konkrete Schritt-für-Schritt-Beschreibung
- Fortschrittsanzeige korrigieren (7 Schritte)

### 4.3 Mittelfristig

**E) Audit-Ergebnisse personalisieren**
- Wenn Solar/Wärmepumpen-URL erkannt wird → spezifische Quick Wins
- Sonst → allgemeine B2B-WordPress-Empfehlungen

---

## 5. Offene Fragen

1. **Soll der Audit NUR für Solar/Wärmepumpen sein?**
   - Wenn ja: Audit-Seite entsprechend umpositionieren
   - Wenn nein: CTA-Pfad muss allgemeiner bleiben

2. **Gibt es einen separaten CTA-Pfad für andere Branchen?**
   - Aktuell fehlt dieser komplett

3. **Ist die Solar-Seite auch über andere Wege erreichbar (SEO, Ads)?**
   - Wenn ja: Der "Audit starten"-CTA dort kann sinnvoll sein
   - Wenn nur vom Audit: Entfernen

---

*Ende der Analyse*
