# hasimuener.de — Source

Production-Codebase von [hasimuener.de](https://hasimuener.de). WordPress-Child-Theme,
Funnel- und Tracking-Logik, Schema-Implementierung, REST-Endpunkte und Automation-Verträge.

**Warum öffentlich?**
Transparenz statt Blackbox. Wer mit mir arbeitet — oder über das White-Label-Modell
mit mir kollaboriert — kann jeden Commit, jedes Template, jede Tracking-Regel verifizieren.
Code-Qualität ist nicht ein Versprechen, sondern überprüfbar.

➡️ **Für Agenturen:** [White-Label-Partner-Modell](https://hasimuener.de/whitelabel-retainer/)
➡️ **Live-Site:** [hasimuener.de](https://hasimuener.de)
➡️ **Über mich:** [hasimuener.de/uber-mich/](https://hasimuener.de/uber-mich/)

---

## Was hier drin steckt

| Bereich | Pfad | Inhalt |
|---|---|---|
| WordPress-Child-Theme | `blocksy-child/` | PHP-Templates, CSS, JS, REST-Endpunkte, Schema-Logik |
| Architektur-Doku | `docs/architecture/` | Live-Status, System-Map, Entscheidungs-Logs |
| Agenten-Workflows | `agents/skills/` | Wiederholbare Automatisierungs-Skills (Claude Code) |
| n8n-Workflows | `automations/n8n/` | Versionierte Flow-Exports + Doku + Flow-Maps |
| Inhalt | `content/blog-drafts/` | Vorbereitete Blog-Drafts (nicht live-autoritativ) |

## Stack

- **Frontend:** WordPress 6.x · Blocksy Parent Theme · eigenes Child-Theme · Vanilla JS
- **Server:** PHP 8.x · MySQL · NGINX
- **Tracking:** GA4 · GTM (Client + Server-Side via Stape/GCP) · Consent Mode V2 · Meta CAPI
- **CRM:** Bitrix24 · HubSpot · Pipedrive (via n8n / Make)
- **Deploy:** GitHub Actions · SSH-Rsync (`.github/workflows/deploy.yml`)
- **Doku:** Markdown · ADR-style Entscheidungs-Logs · llms.txt

## Engineering-Standards

- Bedarfsgesteuertes Asset-Loading pro Template (siehe `blocksy-child/inc/enqueue.php`)
- Zentrale Schema-Logik (`blocksy-child/inc/seo-meta.php`, `blocksy-child/inc/org-schema.php`)
- WordPress-Escaping-Discipline (`esc_html`, `esc_attr`, `esc_url`)
- Kein Page-Builder, keine bloat-anfälligen Plugin-Stacks
- Jeder Tracking-CTA hat `data-track-action` / `data-track-category` / `data-track-section`
- Conditional Loading nach Template/Seitentyp statt globalem CSS-/JS-Dump

## Funnel-Architektur (Public)

```
Solar-/SHK-Audience              Agentur-Audience
        │                                │
        ▼                                ▼
/solar-waermepumpen-leadgenerierung/   /whitelabel-retainer/
        │                                │
        ▼                                ▼
   Marktcheck (60 Sek)              White-Label-Gespräch
        │                                │
        ▼                                ▼
   System-Analyse                   Testprojekt (1–2 Wo)
        │                                │
        ▼                                ▼
   Umsetzung                        Monats-Retainer
```

## Für Mitarbeit / Code-Review

Auf Sicht und für Bewertung öffentlich. Für aktive Mitarbeit / Forks bitte vorher per
[Kontakt](https://hasimuener.de/kontakt/) abstimmen.

Internes Setup, Conventions und Agent-Regeln stehen in [`AGENTS.md`](./AGENTS.md) und
in den lokalen `CONTEXT.md`-Dateien je Verzeichnis.

## Lizenz

Siehe [`LICENSE`](./LICENSE) — Source-Available, alle Rechte vorbehalten. Code ist
einsehbar, aber nicht zur freien Wiederverwendung lizenziert.
