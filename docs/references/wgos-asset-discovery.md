# WGOS Asset Discovery

- Stand: 2026-03-12
- Basis: Repo-Code, vorhandene Audit-Dokumente im Repo, lokale PHP-Pruefungen
- Einschraenkung: In dieser Umgebung gibt es kein `wp`/`wp-cli` und keinen direkten Live- oder DB-Zugriff. Diese Discovery bildet deshalb den versionierten Theme-Stand ab und markiert Live-Risiken separat.

## 1. Gefundene ACF-Felder

Quelle: `blocksy-child/inc/acf.php`

### SEO-Feldgruppe `group_nexus_seo`

| Name | Typ | Key | Gilt fuer |
| --- | --- | --- | --- |
| `seo_title` | Text | `field_seo_title` | `page`, `post`, `wgos_asset` |
| `seo_description` | Textarea | `field_seo_description` | `page`, `post`, `wgos_asset` |
| `og_image` | Image | `field_og_image` | `page`, `post`, `wgos_asset` |
| `seo_noindex` | True/False | `field_seo_noindex` | `page`, `post`, `wgos_asset` |

### WGOS-Asset-Feldgruppe `group_nexus_wgos_asset`

| Name | Typ | Key |
| --- | --- | --- |
| `asset_module` | Select | `field_wgos_asset_module` |
| `asset_phase` | Select | `field_wgos_asset_phase` |
| `asset_credits` | Number | `field_wgos_asset_credits` |
| `asset_keyword` | Text | `field_wgos_asset_keyword` |
| `asset_schema_type` | Select | `field_wgos_asset_schema_type` |
| `asset_goal` | Text | `field_wgos_asset_goal` |
| `asset_result` | Text | `field_wgos_asset_result` |
| `asset_prerequisite` | Text | `field_wgos_asset_prerequisite` |
| `asset_short` | Textarea | `field_wgos_asset_short` |
| `asset_intro` | Textarea | `field_wgos_asset_intro` |
| `asset_deliverable` | Text | `field_wgos_asset_deliverable` |
| `asset_bullets` | Textarea | `field_wgos_asset_bullets` |
| `asset_related_slugs` | Textarea | `field_wgos_asset_related_slugs` |

### Weitere registrierte ACF-Gruppen

- `group_nexus_kpi`
- `group_nexus_comparison`
- `group_nexus_related`

Diese Felder sind fuer allgemeine Seiten-/Content-Bausteine relevant, nicht fuer die WGOS-Asset-Synchronisation.

## 2. SEO-Meta-Mechanismus

Primarlogik: `blocksy-child/inc/seo-meta.php`

Wesentliche Mechanik:

- `hu_get_stored_seo_value()` liest zuerst ACF (`seo_title`, `seo_description`), danach gespeicherte Rank-Math-Meta.
- `hu_get_stored_seo_value()` liest SEO-Werte aus ACF-Feldern und Legacy-Post-Meta.
- `hu_document_title_overrides()` und `hu_get_seo_meta()` liefern pluginlose Fallbacks fuer Title, Description, Canonical, Robots und OG-Daten.
- WGOS-Assets werden ueber `nexus_sync_wgos_asset_posts()` aus der Registry synchronisiert; dabei schreibt das Theme `seo_title` und `seo_description` direkt in die Post-Meta.
- Legacy-Clusterseiten erhalten zusaetzlich versionierte SEO-Fallbacks aus `blocksy-child/inc/wgos-cluster-pages.php`, falls im Editor keine sauberen Metas gepflegt sind.

Fazit:

- Es gibt keine separate SEO-Plugin-Pflicht fuer `wgos_asset`.
- WGOS-Assets und zentrale Legacy-Seiten koennen SEO-Meta komplett aus dem Theme beziehen.

## 3. Template-Struktur

### `wgos_asset` Single

Dateien:

- `blocksy-child/single-wgos_asset.php`
- `blocksy-child/inc/wgos-asset-registry.php`

Aktueller Aufbau:

1. Hero mit Breadcrumbs, H1, Subtitle, CTA zu Landkarte und Growth Audit
2. Fallback auf versionierten Registry-Content, wenn `post_content` leer oder durch den Sync ersetzt wurde
3. Content-HTML aus `nexus_get_wgos_asset_content_html()`

Der Registry-Renderer bildet aktuell die geforderte 8-Abschnitte-Struktur ab:

1. Kurzprofil
2. Warum dieses Asset existiert
3. Was in diesem Asset passiert
4. Dieses Asset im WGOS-System
5. Wann dieses Asset priorisiert wird
6. CTA-Block
7. Verwandte WGOS-Bausteine

Hero, Breadcrumbs und obere CTA-Ebene werden im Single-Template selbst gerendert.

### WGOS Asset Hub / Systemlandkarte

Datei: `blocksy-child/page-wgos-assets.php`

- Die Seite ist dynamisch.
- Die Daten kommen aus `nexus_get_wgos_asset_explorer_payload()`.
- Explorer, Summary und Links bauen auf derselben Registry wie die Asset-Posts auf.

### Legacy-Clusterseiten

Neue Versionierungslogik:

- `blocksy-child/inc/wgos-cluster-pages.php`
- `blocksy-child/page-wgos-pillar.php`

Ergebnis:

- `/wordpress-seo-hannover/`
- `/core-web-vitals/`
- `/conversion-rate-optimization/`
- `/ga4-tracking-setup/`
- `/performance-marketing/`

werden jetzt ueber versionierte Cluster-/Pillar-Definitionen aus dem Theme gerendert oder dorthin route-forced, auch wenn die Seite im Admin auf `page-template-default` stehen sollte.

## 4. Asset-Inventur

Quelle: `blocksy-child/inc/wgos-asset-registry-data.php`

- Gesamt: `35` Asset-Definitionen
- `10` Zielstatus `publish`
- `25` Zielstatus `draft`

Die Aufgabenbeschreibung nennt zwar `32` Assets, die ausgeschriebene Matrix ergibt aber rechnerisch `35`. Der Repo-Stand folgt der ausgeschriebenen Matrix.

### Publish-Zielzustand

- `cwv-speed-audit`
- `cwv-optimierung`
- `server-tuning`
- `security-hardening`
- `tracking-audit`
- `ga4-event-blueprint`
- `consent-mode-v2`
- `server-side-tracking`
- `technical-seo-audit`
- `pillar-page`

### Draft-Zielzustand

Alle weiteren Registry-Eintraege bleiben `draft`, bis sie im Sync als Entwurf angelegt und redaktionell freigegeben werden.

### Sync-Mechanismus

Datei: `blocksy-child/inc/wgos-asset-registry.php`

- `nexus_maybe_sync_wgos_asset_posts()` laeuft auf `init`
- Posts werden bei neuer Registry-Version erstellt oder aktualisiert
- relevante Meta-Felder werden automatisch gesetzt
- Legacy-Asset-Slugs koennen per 301 auf den kanonischen WGOS-Asset-Slug umgeleitet werden

## 5. Navigation-Inventur

### Nav A: Custom Site Header

Dateien:

- `blocksy-child/inc/header.php`
- `blocksy-child/template-parts/site-header.php`
- `blocksy-child/inc/menu-setup.php`

Verwendung:

- Standard fuer Nicht-Blog-Seiten
- nutzt WordPress-Menues `primary` / `primary-slim`, normalisiert aber Ergebnisse- und Audit-Links beim Rendern

Stand:

- Ergebnisse laufen jetzt kanonisch ueber `nexus_get_results_url()`
- alte Menueintraege auf `/case-studies/` oder `/case-studies-e-commerce/` werden beim Rendern auf das kanonische Ziel gezogen

### Nav B: Blog Header

Datei: `blocksy-child/template-parts/blog-header.php`

Verwendung:

- Blog-Home
- Archive
- Single Posts

Stand:

- nutzt ebenfalls `nexus_get_results_url()`
- bleibt eigenstaendig, ist aber jetzt auf denselben Ergebnisse-Kanon ausgerichtet

### Nav C: Homepage-Shortcodes

Datei: `blocksy-child/inc/shortcodes.php`

Verwendung:

- interne One-Page-Scroll-Navigation auf der Startseite
- Hero-CTAs fuer Audit und Ergebnisse

Stand:

- Ergebnisse-CTA laeuft ueber `hu_home_urls()['cases']` und damit ebenfalls auf den kanonischen Ergebnisse-Hub

### Nav D: Legacy-/Editor-Navigation ausserhalb des Repo-Kerns

Bekannte Risiken aus `docs/audits/site-audit-hasimuener-2026-03-09.md`:

- einzelne Default-/Editor-Seiten liefen mit zusaetzlichem Blog-Header
- WordPress-Menues oder Editor-Links konnten bisher alte Ziele wie `/case-studies/` oder `/wordpress-tech-audit/` behalten

Repo-Status:

- wichtige Legacy-Routen werden jetzt per Theme weitergeleitet
- ohne WordPress-Admin-Zugriff bleibt eine Restpruefung fuer manuelle Menues und Editor-Inhalte offen

## 6. Legacy-Seiten-Inventur

### Versioniert bzw. route-forced

| URL | Repo-Status | WGOS-Ueberlappung |
| --- | --- | --- |
| `/wordpress-seo-hannover/` | versionierte Clusterseite | Sichtbarkeit |
| `/core-web-vitals/` | versionierte Clusterseite | Technisches Fundament |
| `/conversion-rate-optimization/` | versionierte Clusterseite | Conversion |
| `/ga4-tracking-setup/` | versionierte Clusterseite | Messbarkeit |
| `/performance-marketing/` | versionierte Clusterseite | Paid-Kontext ueber WGOS |
| `/wordpress-agentur-hannover/` | bestehendes Repo-Template | lokaler WGOS-Einstieg |

### Bekannte Editor-/Live-Risiken aus vorhandenem Audit

| URL | Letzter bekannter Hinweis im Repo | Risiko |
| --- | --- | --- |
| `/wordpress-seo-hannover/` | `page-template-default` statt Repo-Template | durch Route-Forcing jetzt theme-seitig abgesichert |
| `/core-web-vitals/` | `page-template-default` statt Repo-Template | durch Route-Forcing jetzt theme-seitig abgesichert |
| `/case-studies-e-commerce/` | Editor-/Default-Seite | jetzt Legacy-Alias fuer `/ergebnisse/` |
| `/kontaktiere-mich/` | Editor-/Default-Seite | kein Umbau in dieser Runde |
| `/kostenlose-tools/` | Tools-Hub vorhanden | bewusst nicht redirectet |

## 7. Redirect-Kandidaten und aktueller Repo-Stand

### Jetzt im Theme als 301 hinterlegt

| Legacy-URL | Ziel |
| --- | --- |
| `/case-studies/` | `/ergebnisse/` |
| `/case-studies-e-commerce/` | `/ergebnisse/` |
| `/audit/` | `/growth-audit/` |
| `/customer-journey-audit/` | `/growth-audit/` |
| `/360-audit/` | `/growth-audit/` |
| `/wordpress-tech-audit/` | `/growth-audit/` |
| `/alle-loesungen-im-detail/` | `/alle-loesungen/` |
| `/meta-ads/` | `/wordpress-growth-operating-system/` |
| `/wordpress-agentur/` | `/wordpress-agentur-hannover/` |
| `/roi-rechner/` | `/kostenlose-tools/` |

### Bewusst nicht redirectet

| URL | Grund |
| --- | --- |
| `/wordpress-wartung-hannover/` | konsolidierter Legacy-Slug; 301 auf `/wordpress-agentur-hannover/#wordpress-wartung` |
| `/ga4-tracking-setup/` | aktive versionierte Clusterseite, kein Legacy-Redirect mehr |
| `/performance-marketing/` | aktive versionierte Clusterseite, kein Legacy-Redirect mehr |
| `/kostenlose-tools/` | Im Repo existiert weiter ein Tools-Hub, daher kein Zwangsredirect gesetzt |

## 8. Technische Konsequenzen

1. Die WGOS-Assets selbst sind repo-seitig versioniert und synchronisierbar.
2. Wichtige Legacy-Clusterseiten sind nicht mehr auf Editor-Content oder manuelle Template-Zuordnung angewiesen.
3. Ergebnisse laufen kanonisch auf `/ergebnisse/`; alte Proof-Slugs sind nur noch Legacy-Aliase.
4. Single Posts koennen passende WGOS-Assets jetzt theme-seitig als Bridge anzeigen.
5. Offene Live-Risiken bleiben dort, wo WordPress-Admin-Inhalte ausserhalb der route-forced Seiten weiter manuell gepflegt werden.
