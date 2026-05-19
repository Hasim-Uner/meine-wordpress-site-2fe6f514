# Solar-/Wärmepumpen-Landingpage — Copy-Rewrite

**Datum:** 2026-04-02
**Branch:** claude/solar-lp-rewrite-bN5Ww
**Seite:** /solar-waermepumpen-leadgenerierung/

## Zusammenfassung

Copy-Rewrite der Solar-/Wärmepumpen-Landingpage von Berater-Sprache auf Handwerker-Sprache. Zielgruppe: Geschäftsführer und Vertriebsleiter von Installationsbetrieben mit eigenem Vertrieb, hohen Projektwerten und klarem Zielgebiet.

## Geänderte Dateien

| Datei | Änderung |
|-------|----------|
| `blocksy-child/page-solar-waermepumpen-leadgenerierung.php` | Kompletter Copy-Rewrite aller Sections |
| `blocksy-child/inc/seo-meta.php` | Meta Title + Description aktualisiert |

## Änderungen im Detail

### Hero
- **Alt:** "Website für Solar- und Wärmepumpen-Anbieter, die qualifizierte Anfragen erzeugt."
- **Neu:** "Schluss mit teuren Portal-Leads, die nicht ans Telefon gehen."
- Proof-Zeile mit E3-Zahlen direkt unter der Headline ergänzt
- CTAs von 3 auf 2 reduziert (Primary: "Kostenloses Erstgespräch — 15 Min.", Secondary: "E3-Ergebnis ansehen →")
- "Oder direkt Ihr Setup einordnen" Link entfernt

### Problem-Section
- **Alt:** "Was im Energie-Vertrieb digital oft bremst." (2 generische Punkte)
- **Neu:** "Kommt Ihnen das bekannt vor?" (3 konkrete Szenarien mit €-Beträgen)
- Dritter Punkt ergänzt: Markt-Normalisierung seit 2024

### Branchenverständnis
- **Alt:** Marketing-Lehrbuch-Sprache ("Noch Orientierung", "Konkretes Interesse", "Anfragebereit")
- **Neu:** Kundenperspektive ("Lohnt sich das überhaupt?", "Wer macht das bei uns in der Region?", "Ich will ein Angebot — aber einfach.")

### E3 Case Study
- Kontextabsatz ergänzt: Wer E3 ist, Ausgangslage, Ergebnis

### FAQ
- **Alt:** 3 generische Fragen
- **Neu:** 5 branchenspezifische Fragen (Kosten, kleine Betriebe, Google Ads, Relaunch, bestehende Agentur)
- FAQPage Schema aktualisiert sich automatisch aus dem $faq_items Array

### Neuer Block: 15-Min-Erstgespräch
- Eigenständige Section vor dem Formular
- Adressiert "noch nicht bereit"-Typ

### Formular
- Fortschrittstext geändert: "In 2 Minuten: Wo liegt bei Ihnen der größte Hebel?"

### Final CTA
- Headline und Button-Text auf Handwerker-Sprache umgestellt

### KPI-Labels
- "Leads im System" → "qualifizierte Anfragen"
- "Cost per Lead" → "Kosten pro Anfrage"
- "Sales-Conversion" → "Abschlussquote"

### SEO
- **Meta Title:** "Leadgenerierung für Solar & Wärmepumpen | Weniger Kosten, bessere Anfragen"
- **Meta Description:** "Schluss mit teuren Portal-Leads. Eigenes Anfrage-System für Solarteure und Wärmepumpen-Installateure. Referenz: –83 % Kosten pro Anfrage. Kostenloses Erstgespräch."

## Copy-Regeln angewendet

- Keine Wörter aus Verbotsliste ("ganzheitlich", "Reibung", "Friktion", "Nutzerführung", "Pain Point", "Touchpoint")
- "Anfragen" statt "Leads", "Abschlussquote" statt "Conversion Rate", "Kosten pro Anfrage" statt "CPL"
- Kurze Sätze, max 20 Wörter
- Zahlen und Szenarien vor abstrakten Konzepten

## Annahmen

1. E3-Zahlen: 1.750+ Anfragen, –83 % CPL, 12 % Sales-Conversion, 9 Monate (aktualisierte Zahlen lt. Briefing)
2. Formular bleibt unverändert (nur Einleitungstext angepasst)
3. Keine CSS-/JS-Änderungen nötig — neue Section nutzt bestehende Klassen
4. FAQPage Schema generiert sich dynamisch aus $faq_items Array

## Nicht geändert

- Formular-Logik und -Felder (review-crm.php)
- CSS (energy-systems.css)
- JavaScript (energy-intake.js)
- Dateien außerhalb des Scopes dieser Landingpage
