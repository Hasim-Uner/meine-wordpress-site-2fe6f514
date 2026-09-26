# hasimuener.de — WordPress Engineering

Produktions-Codebase von [hasimuener.de](https://hasimuener.de) für **WordPress, Tracking & Conversion im B2B-Kontext**.

Das Repository zeigt nicht nur Theme-Code, sondern die technische Verbindung aus Website-Architektur, SEO, Tracking, Funnel-Logik und Automatisierung. Es dient zugleich als nachvollziehbarer Engineering-Nachweis für direkte Projekte und White-Label-Zusammenarbeit mit Agenturen.

**Live:** [hasimuener.de](https://hasimuener.de)  
**White-Label:** [Zusammenarbeit für Agenturen](https://hasimuener.de/whitelabel-retainer/)  
**Profil:** [Haşim Üner](https://hasimuener.de/hasim-uener/)

---

## Technischer Umfang

| Bereich | Inhalt |
|---|---|
| WordPress | eigenes Child-Theme, PHP-Templates, Hooks und REST-Endpunkte |
| Frontend | Vanilla JavaScript, komponentennahe Styles, bedarfsgesteuertes Asset-Loading |
| SEO | strukturierte Daten, technische SEO-Logik, interne Architektur |
| Tracking | GA4, GTM, Server-Side-Tracking, Consent Mode, Event-Konventionen |
| Automation | versionierte Workflows und Integrationslogik |
| Qualität | PHPStan, dokumentierte Architektur- und Entscheidungsregeln |
| Deployment | Git-basierter Workflow und automatisierbare Deployments |

## Engineering-Prinzipien

- Git statt Änderungen als Blackbox im Backend
- möglichst wenig globale Abhängigkeiten
- Conditional Loading nach Seitentyp und Template
- konsequentes WordPress-Escaping
- strukturierte Tracking-Events statt verstreuter Einzelimplementierungen
- technische Entscheidungen werden dokumentiert und versioniert
- KI-Agenten arbeiten mit repository-internem Kontext statt ohne Projektwissen

## Tracking- und Conversion-Schicht

CTAs und relevante Interaktionen werden systematisch für Analytics und Attribution vorbereitet. Die Tracking-Architektur ist so aufgebaut, dass Client-Side- und Server-Side-Signale sowie CRM-/Automationsprozesse erweitert werden können, ohne die Website-Templates jedes Mal neu zu erfinden.

## Für Agenturen

Das Repository ist bewusst öffentlich einsehbar, damit technische Arbeitsweise, Commit-Historie und Architektur überprüfbar sind.

Typische Einsatzbereiche in White-Label-Projekten:

- WordPress-Entwicklung und technische Umsetzung
- Landingpages und Conversion-Optimierung
- technische SEO- und Performance-Arbeit
- Tracking-Architektur
- API-/CRM-Anbindungen
- Wartung und Weiterentwicklung bestehender Codebases

## Mitarbeit und Wiederverwendung

Das Repository ist zur technischen Bewertung öffentlich. Für aktive Mitarbeit oder projektbezogene Zusammenarbeit bitte über [hasimuener.de](https://hasimuener.de) Kontakt aufnehmen.

## Lizenz

Siehe [LICENSE](./LICENSE). Source-Available, alle Rechte vorbehalten; der öffentlich sichtbare Code ist nicht automatisch zur freien Wiederverwendung lizenziert.
