# Blocksy Child – Growth Architect Edition

**Child Theme für WordPress** · Haşim Üner – WordPress Growth Architect

---

## Architektur: Hybrid-Setup

| Layer | Ort | Verantwortlich | Beispiele |
|-------|-----|----------------|-----------|
| **Structure Layer** | Git Repository | Developer / Agent | Templates, CSS, JS, PHP-Module |
| **Content Layer** | WordPress Block-Editor | Developer, Texter, Kunde | Texte, Headlines, Bilder, KPIs |
| **External Systems** | Separate Plattformen | Manuelle Config | GTM, GA4, Meta CAPI, n8n, CRM |

**Kernregel:** Repo = WIE es aussieht. Editor = WAS drin steht.

## Runtime-Doku

- `docs/architecture/`: Seitentypen, Funnel-Rollen und Template-Entscheidungslogik im Theme.
- `docs/ux/`: Formular- und Interaktionsregeln fuer runtime-nahe Conversion-Pfade.
- `docs/implementation/`: technische Leitplanken und Umsetzungsnotizen fuer Theme-spezifische Arbeiten.

---

## Verzeichnisstruktur

```
blocksy-child/
├── functions.php              ← Schlank: nur require-Aufrufe nach inc/
├── style.css                  ← Child Theme Header + Mega-Menü + Custom Styles
├── front-page.php             ← Startseite (Content via Editor)
├── home.php                   ← Blog-Index
├── single.php                 ← Einzelbeitrag (Hero, Breadcrumb, Related, CTA)
├── archive.php                ← Archiv-Seiten
├── category.php               ← Pillar Hub (Kategorie als Content-Cluster)
├── 404.php                    ← Fehlerseite mit Suche + Top-Links
├── page-*.php                 ← Service- und Proof-Landingpages (Audit, SEO, CWV, CRO, GA4, Case Studies, …)
├── template-about.php         ← Über-Mich-Seite
├── template-portal.php        ← Client Portal (noindex)
├── assets/
│   ├── css/                   ← Modulare Stylesheets pro Seitentyp
│   ├── js/                    ← Frontend-Scripts (nexus-core, Seiten-spezifisch)
│   └── fonts/                 ← Self-hosted Satoshi Variable Font
├── template-parts/            ← Wiederverwendbare Sektionen
│   ├── kpi-block.php          ← KPI-Metrik als visueller Anker
│   ├── breadcrumb.php         ← Breadcrumb + BreadcrumbList Schema
│   ├── comparison-table.php   ← Vorher/Nachher Grid
│   ├── service-system-map.php ← Wiederverwendbare 4-Ebenen-Systemsektion für Service-Landings
│   ├── trust-section.php      ← Trust-Badges in CTA-Nähe
│   ├── related-content.php    ← Verwandte Inhalte (Flywheel)
│   └── footer-cta.php         ← Conversion-optimierter Bottom-CTA
├── inc/
│   ├── helpers.php            ← Utility-Funktionen (Reading Time, SCF/ACF Fallback, CTA)
│   ├── enqueue.php            ← CSS/JS Asset-Management (filemtime Cache-Busting)
│   ├── seo-meta.php           ← OG Tags, Canonical, Indexierungssteuerung
│   ├── org-schema.php         ← JSON-LD Structured Data (LocalBusiness, Service, FAQ)
│   ├── shortcodes.php         ← Homepage-Shortcodes
│   ├── client-portal.php      ← Client Portal Dashboard
│   ├── admin-manager.php      ← Backend-Felder für Portal
│   └── snippets.php           ← Nav Button, Security, Login-Redirect
└── fonts/                     ← Satoshi Variable Font Files
```

---

## WGOS – WordPress Growth Operating System

Jede Änderung ordnet sich in das 3-Phasen-Framework ein:

### Phase 1: Speed & Conversion
Core Web Vitals, UX-Reibung eliminieren, technische Hygiene.
→ **Ziel:** LCP < 0.8s, CLS < 0.1, INP < 200ms

### Phase 2: Privacy-First Measurement
Server-Side GTM, Consent Mode v2, GA4 Event-Blueprint, Meta CAPI.
→ **Ziel:** 100% Data Ownership, belastbare KPIs
→ **Hinweis:** Tracking-Implementierung ist NICHT im Repo. Code ist tracking-ready (`data-track-*` Attribute).

### Phase 3: Anfrage-Flywheel
Pillar Pages, Content-Cluster, interne Verlinkung, Proof-Assets, Nurture-Flows.
→ **Ziel:** Pipeline, die mit der Zeit günstiger wird.

---

## Commit-Messages

```
[WGOS-Phase] Bereich: Ergebnis-orientierte Beschreibung

[Speed]      template-parts/hero: Critical CSS inline, LCP-Reduktion
[Flywheel]   single.php: Related Content + Footer-CTA Template Parts
[Measurement] inc/enqueue: data-track Attribute für Footer-CTAs
[SEO]        inc/seo-meta: Zentralisierte OG/Canonical/Indexierung
[CRO]        template-parts/comparison-table: Responsive Vorher/Nachher
```

---

## Technische Standards

- **PHP:** WordPress Coding Standards (WPCS), Escaping (`esc_html()`, `esc_attr()`, `esc_url()`), Translation-ready (`__()`, `_e()`)
- **Assets:** Alles über `wp_enqueue_scripts`, Cache-Busting via `filemtime()`, JS im Footer
- **Fonts:** `font-display: swap`, self-hosted Satoshi, max. 2 Familien
- **Bilder:** WebP/AVIF, `loading="lazy"`, explizite `width` + `height`
- **Tracking-Ready:** `data-track-action`, `data-track-category`, `data-track-section`, `data-track-form`

---

## SEO-Architektur

- **Meta Tags:** Zentral in `inc/seo-meta.php` (Fallback-Kette: SCF/ACF-Felder → Post-Daten → Auto-Generate)
- **Schema:** JSON-LD in `inc/org-schema.php` (LocalBusiness, Service, FAQPage, Article)
- **Breadcrumbs:** `template-parts/breadcrumb.php` mit BreadcrumbList Schema
- **Indexierung:** noindex für Utility-Seiten (Portal, Danke, Login) via `inc/seo-meta.php`
- **Canonical:** Self-referencing, zentral gesteuert

---

## Nicht im Repo (Agent kennt Schnittstellen)

- **Tracking:** GTM Server-Side, GA4, Meta CAPI, Consent Mode v2
- **Automation:** n8n Workflows
- **CRM:** Bitrix24 / HubSpot
- **Ads:** Google Ads, Meta Ads Kampagnen
