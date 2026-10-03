# Ehemalige Asset-Struktur entfernen und Fachbegriffe übernehmen

Entscheidung vom 2026-10-03. Repository-Änderung; Livegang und Datenbanklauf
werden erst nach Merge/Deploy überprüft.

## Umfang

Die ehemalige WGOS-Struktur ist vollständig eingestellt: CPT, Registry und
Wiederanlage, Hub, Dashboard, Detailtemplates, Asset-Explorer, CSS/JS,
Shortcode, ACF-Feldgruppe, Rolle und Capability. Historische allgemeine Audits
sind keine aktiven Implementierungsquellen. Alte Bezeichnungen bleiben nur
in gezielter Migration, Redirect-Zuordnung, Sperrregeln und Prüfungen erhalten.

Die aktiven GA4- und Performance-Marketing-Seiten sind als eigenständige
Service-Registry herausgelöst. Aktuelle Solar-Preise und TCO-Helfer bleiben
bestehen; ungenutzte Founding-, Retainer-, Premium- und Wertanker-Konstanten
wurden entfernt.

## Glossar

Acht neue eigenständige Definitionen: Positionierung, Seitenrollen,
Wettbewerbsanalyse, Keyword-Strategie, Pillar Page, Content Hub, Social Proof,
Lead-Magnet. Jeweils mit verständlicher Definition, Praxisbeispiel, Einordnung,
typischen Fehlern, verwandten Begriffen und passendem Anschluss zur Umsetzung.
Sie bleiben zunächst noindex/follow; bestehende Indexierungsregeln bleiben
ansonsten erhalten. 68 Definitionen, mindestens zwölf pro Bereich; keine
Duplikate für UTM-Parameter, Consent Mode oder strukturierte Daten.

## Datenbankmigration

`inc/legacy-system-retirement.php` läuft einmal in `init` nach dem
Glossar-Sync (Priorität 40). Vorbedingungen: aktuelle Sync-Version und bestandene
Glossar-Routing-Assertion. Gelöscht werden alle Status des alten CPT einschließlich
Papierkorb, dessen Metadaten/Revisionsbestand über die WordPress-API, alte
Hub-Seiten und ausschließlich zugehörige importierte ACF/SCF-Definitionen.
Gemeinsame Feldgruppen behalten ihre übrigen Standortregeln und Felder.
Nachkommen gelöschter ACF-Gruppen werden gezielt entfernt, auch verschachtelte
Felder; andere verwaiste Felder bleiben unangetastet. Fehlgeschlagene Löschungen
werden wiederholt; Eltern-IDs bleiben bis zum erfolgreichen Abschluss gespeichert.

Editor-Inhalte behalten ihren übrigen Inhalt, lokale alte Links erhalten
passende Ziele, nicht mehr verfügbare Links werden entlinkt und der entfernte
Shortcode wird gelöscht. Benutzerkonten bleiben bestehen. Andere Rollen bleiben
erhalten; reine ehemalige Dashboard-Kunden erhalten die Leserolle `subscriber`.
Die ehemalige Dashboard-Capability wird bei Rollen und einzelnen Benutzern entfernt.
Alte Sync-Optionen und Sperren werden gelöscht, Rewrite-Regeln einmal erneuert,
LiteSpeed-Seiten-Cache über seinen Purge-Hook geleert.

Die Entfernung ist dauerhaft. Ein Theme-Rollback stellt gelöschte Datenbankinhalte
nicht wieder her; dafür wäre ein Datenbank-Backup erforderlich. Dies entspricht
dem Auftrag, den vollständigen alten Bestand zu entfernen.

Beobachtbarkeit: `hu_legacy_system_retirement_version` steht bei Erfolg auf
`2026-10-03-1`. `hu_legacy_system_retirement_errors` enthält bei Fehlschlägen die
betroffenen IDs; der Abschlussmarker wird dann nicht gesetzt. Alte URLs sind
unabhängig von einer verbleibenden DB-Seite geschlossen: passende direkte 301,
sonst 410. Glossar-Redirects greifen nur mit tatsächlich veröffentlichter Detailseite.

## Abgleich aller 38 registrierten Assets

Lieferpakete sind keine Wörterbuchbegriffe. Nur fachlich passende Konzepte
bekommen eine Glossar-Definition; passende bestehende Dienstleistungen bleiben
Leistungsseiten. Hub-Routen und unbekannte Asset-Slugs einschließlich des bereits
stillgelegten Workflow-Angebots liefern 410. Kein pauschaler Startseiten-Redirect.

| Früherer Inhalt | Alte URL | Behandlung |
|---|---|---|
| Marktcheck | `/wgos-assets/growth-audit/` | 410 – Lieferpaket ohne direkten Nachfolger |
| Positionierungs-Check | `/wgos-assets/positionierungs-check/` | 301 → `/glossar/positionierung/` |
| Seitenrollen-Mapping | `/wgos-assets/seitenrollen-mapping/` | 301 → `/glossar/seitenrollen/` |
| Wettbewerbs-Analyse (Digital) | `/wgos-assets/wettbewerbs-analyse/` | 301 → `/glossar/wettbewerbsanalyse/` |
| Roadmap & Priorisierung | `/wgos-assets/roadmap-priorisierung/` | 410 – Lieferpaket ohne direkten Nachfolger |
| CWV Speed Audit | `/wgos-assets/cwv-speed-audit/` | 301 → `/wordpress-agentur-hannover/#zusammenarbeit` |
| CWV Optimierung | `/wgos-assets/cwv-optimierung/` | 301 → `/wordpress-agentur-hannover/#zusammenarbeit` |
| Server-Tuning | `/wgos-assets/server-tuning/` | 410 – Lieferpaket ohne direkten Nachfolger |
| Security Hardening | `/wgos-assets/security-hardening/` | 410 – Lieferpaket ohne direkten Nachfolger |
| Plugin Audit & Bereinigung | `/wgos-assets/plugin-audit/` | 410 – Lieferpaket ohne direkten Nachfolger |
| WordPress Update-Management | `/wgos-assets/update-management/` | 410 – Lieferpaket ohne direkten Nachfolger |
| Tracking Audit | `/wgos-assets/tracking-audit/` | 301 → `/ga4-tracking-setup/` |
| GA4 Event Blueprint | `/wgos-assets/ga4-event-blueprint/` | 301 → `/ga4-tracking-setup/` |
| Consent Mode v2 | `/wgos-assets/consent-mode-v2/` | 301 → `/glossar/consent-mode/` |
| Server-Side Tracking (sGTM & Matomo) | `/wgos-assets/server-side-tracking/` | 301 → `/server-side-tracking-b2b/` |
| KPI-Dashboard Setup | `/wgos-assets/kpi-dashboard/` | 410 – Lieferpaket ohne direkten Nachfolger |
| UTM-Framework & Attribution | `/wgos-assets/utm-framework/` | 301 → `/glossar/utm-parameter/` |
| Technical SEO Audit | `/wgos-assets/technical-seo-audit/` | 301 → `/wordpress-agentur-hannover/#zusammenarbeit` |
| Keyword-Strategie & Content-Map | `/wgos-assets/keyword-strategie/` | 301 → `/glossar/keyword-strategie/` |
| Pillar Page | `/wgos-assets/pillar-page/` | 301 → `/glossar/pillar-page/` |
| Content Hub Aufbau | `/wgos-assets/content-hub/` | 301 → `/glossar/content-hub/` |
| On-Page SEO Optimierung | `/wgos-assets/on-page-seo/` | 410 – Lieferpaket ohne direkten Nachfolger |
| Interne Verlinkung & Seitenarchitektur | `/wgos-assets/interne-verlinkung/` | 301 → `/glossar/interne-verlinkung/` |
| Schema Markup & Strukturierte Daten | `/wgos-assets/schema-markup/` | 301 → `/glossar/strukturierte-daten/` |
| Local SEO Setup | `/wgos-assets/local-seo/` | 301 → `/wordpress-agentur-hannover/` |
| Landing Page (Neu) | `/wgos-assets/landing-page-neu/` | 301 → `/landingpage-erstellen-lassen/` |
| Landing Page Optimierung | `/wgos-assets/landing-page-optimierung/` | 301 → `/conversion-optimierung/` |
| CTA & Formular-Optimierung | `/wgos-assets/cta-formular-optimierung/` | 301 → `/conversion-optimierung/` |
| Angebotsseiten-Architektur | `/wgos-assets/angebotsseiten-architektur/` | 301 → `/wordpress-website-erstellen-lassen/` |
| Social Proof & Trust-Elemente | `/wgos-assets/social-proof/` | 301 → `/glossar/social-proof/` |
| Lead-Magnet Konzeption | `/wgos-assets/lead-magnet/` | 301 → `/glossar/lead-magnet/` |
| Monthly Performance Review | `/wgos-assets/monthly-review/` | 410 – Lieferpaket ohne direkten Nachfolger |
| Quarterly Roadmap Update | `/wgos-assets/quarterly-roadmap/` | 410 – Lieferpaket ohne direkten Nachfolger |
| Reporting Dashboard | `/wgos-assets/reporting-dashboard/` | 410 – Lieferpaket ohne direkten Nachfolger |
| Conversion-Hypothesen & Testing | `/wgos-assets/conversion-testing/` | 301 → `/glossar/ab-test/` |
| KI-Assistent / Chatbot (DSGVO-konform) | `/wgos-assets/ki-assistent-chatbot/` | 410 – Lieferpaket ohne direkten Nachfolger |
| KI-gestützte Lead-Qualifizierung | `/wgos-assets/ki-lead-qualifizierung/` | 301 → `/glossar/lead-qualifizierung/` |
| RAG-Wissenssuche | `/wgos-assets/rag-wissenssuche/` | 410 – Lieferpaket ohne direkten Nachfolger |

Legacy-Alias `/wgos-assets/server-side-tracking-sgtm-matomo/` führt direkt zur
Server-Side-Seite. `/wgos-assets/wordpress-update-management/` liefert 410.
Auch alle sechs alten Hub-/Dashboard-Pfade liefern 410.

## Prüfung nach Deploy

- Erste ungecachte Anfrage auslösen; aktueller Glossar-Sync und Routing-Assertion
  müssen erfolgreich sein, erst dann darf der Abschlussmarker gesetzt werden.
- Alter CPT-Bestand, alte Hub-Seiten und zugehörige ACF/SCF-Definitionen müssen
  verschwunden sein; allgemeine SEO-/Portal-Feldgruppen und das aktive Glossar bleiben.
- `/wgos/`, `/wgos-assets/` und `/wgos-assets/monthly-review/`: 410.
- `/wgos-assets/positionierungs-check/`: 301 zu `/glossar/positionierung/`,
  dort 200 mit Definition und noindex/follow.
- `/wgos-assets/utm-framework/`: direkte 301 zum bestehenden UTM-Begriff.
- `/ga4-tracking-setup/` und `/performance-marketing/`: 200, bisherige Vorlagen,
  Meta, sichtbare FAQ und FAQ-Schema, bestehender Anfrageweg.
- Glossar-Routing-Assertion und `npm run test:retirement` bestehen.

Der PR behauptet keine bereits erfolgte Live-Datenbankmigration.
