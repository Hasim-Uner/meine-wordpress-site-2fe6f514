# PAGE ARCHITECTURE – Growth Architect Website

Diese Datei definiert die strukturelle Logik aller Seitentypen im Theme.

Ziel:
- konsistente Premium UX
- klare Funnelführung
- saubere SEO-Architektur
- Vermeidung von Keyword-Kannibalisierung
- skalierbares Wachstumssystem

Die Website ist kein Blog.
Sie ist ein Conversion-System.

---

## 1. PAGE SYSTEM OVERVIEW

Die Seite besteht aus 6 funktionalen Seitentypen:

1. HERO / ENTRY (Startseite)
2. SERVICE LANDING
3. PILLAR PAGE
4. PROOF / CASE STUDY
5. GLOSSARY SYSTEM
6. UTILITY PAGES

Jede Seite erfüllt eine klare Aufgabe im Funnel.

---

## 2. STARTSEITE (ENTRY PAGE)

Ziel:
- sofortige Positionierung als Premium Experte
- Vertrauen aufbauen
- Funnel starten

Struktur:

1. Hero → klares Leistungsversprechen
2. Authority Signals → Logos / Resultate
3. System Erklärung (WGOS)
4. Service Einstieg
5. Proof Teaser
6. CTA → Audit

SEO Rolle:
Brand + Hauptkeyword Cluster

---

## 3. SERVICE LANDING PAGES

Beispiele:
- page-meta-ads.php
- page-performance.php

Entfernt am 2026-07-26: `page-seo.php`, `page-cro.php`, `page-cwv.php`. Ihre
Cluster-Daten waren bereits geloescht, die Ziel-URLs liefern 410
(`nexus_get_retired_gone_paths`).

Ziel:
- Suchintention bedienen
- Expertise beweisen
- Anfrage generieren

Struktur:

1. Problem Framing
2. Lösungsansatz
3. Methodik
4. Resultate
5. Prozess
6. CTA

SEO Rolle:
Commercial Intent Keywords

Regel:
KEIN Grundlagencontent → dafür Glossar oder Pillar nutzen.

---

## 4. PILLAR PAGES

Beispiele:
- page-seo-cornerstone.php
- page-wgos.php

Ziel:
- thematische Autorität aufbauen
- interne Verlinkung bündeln
- Rankingbasis schaffen

Struktur:

1. Themenüberblick
2. Systematische Erklärung
3. Cluster-Links
4. CTA Soft Integration

SEO Rolle:
Topical Authority

Regel:
Service-Pitch darf hier NICHT dominant sein.

---

## 5. PROOF / CASE STUDY PAGES

Beispiele:
- page-case-study-domdar.php

Ziel:
- Vertrauen massiv erhöhen
- konkrete Ergebnisse zeigen
- Einwandbehandlung

Struktur:

1. Ausgangssituation
2. Maßnahmen
3. Zahlen
4. Ergebnis
5. CTA

SEO Rolle:
Trust + Bottom Funnel

---

## 6. GLOSSARY SYSTEM

Beispiele:
- page-glossar.php
- single-glossary_term.php

Ziel:
- Informationsintention bedienen
- Keyword Breite abdecken
- interne Autorität stärken

Regeln:

- Definition kurz und präzise
- keine aggressive Sales Sprache
- Soft CTA möglich
- interne Links zu Pillar & Service

SEO Rolle:
Top Funnel Traffic

WICHTIG:
Glossar darf NICHT mit Service Pages konkurrieren.

---

## 7. UTILITY PAGES

Beispiele:
- Datenschutz
- Impressum
- Portal
- Kontakt

Ziel:
- Vertrauen / Compliance
- Conversion Unterstützung

SEO:
teilweise noindex

---

## 8. CTA ARCHITEKTUR

Global Regel:

Top Funnel → Education CTA
Mid Funnel → Strategy CTA
Bottom Funnel → Audit CTA

Nie mehrere harte CTAs pro Section.

---

## 9. VISUAL FLOW PRINZIP

Jede Seite folgt:

- Hook
- Klarheit
- Vertrauen
- Beweis
- Handlung

Kein Block darf ohne Ziel existieren.

---

## 10. TEMPLATE ENTSCHEIDUNG REGEL

Neue Seite erstellen?

Fragen:

1. Ist es Verkauf?
→ Service Template

2. Ist es Erklärung?
→ Pillar oder Glossar

3. Ist es Beweis?
→ Case Study

4. Ist es Navigation?
→ Hub

So bleibt UX konsistent.
