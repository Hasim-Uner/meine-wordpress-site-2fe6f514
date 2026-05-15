# Website Operating System

Versionierbare Source of Truth fuer hasimuener.de: WordPress-Child-Theme, Funnel-/CRM-Logik, Agentenregeln, Automationsvertraege und dauerhafte Systemdoku.

## Schnellstart

Menschen:

1. `README.md`
2. `docs/architecture/SYSTEM_MAP.md`
3. `docs/architecture/LIVE_STATUS.md`

Agenten:

1. `AGENTS.md`
2. genau ein passendes lokales `CONTEXT.md`
3. nur die Dateien, die fuer die konkrete Aenderung noetig sind

## Struktur

```text
.
├── AGENTS.md
├── README.md
├── blocksy-child/          # deploybarer WordPress-Code
├── docs/                   # dauerhafte Architektur, Entscheidungen, Systeme
├── agents/skills/          # wiederholbare Agent-Workflows
├── automations/n8n/        # Workflow-Exports + Doku + Flow-Maps, wenn aktiv
├── content/blog-drafts/    # vorbereitete, nicht live autoritative Inhalte
└── scripts/                # lokale Checks und Build-Helfer
```

## Ownership

- Repo: Templates, PHP-Module, CSS, JS, Schema, REST-Endpunkte, Helper, Registries, technische Contracts.
- WordPress-Editor: grosse Teile von Live-Copy, Medien und redaktionellen Inhalten.
- Externe Systeme: GTM, sGTM, GA4, Consent, Brevo, Cal.com, n8n Cloud und WordPress-Admin-Konfiguration.

## Aktuelle Leitplanken

- Primaerer kalter Solar-/SHK-Einstieg: `/solar-waermepumpen-leadgenerierung/#marktcheck`
- Analyse-/Anfrage-Canon: `blocksy-child/inc/canon/`
- Live-Status: `docs/architecture/LIVE_STATUS.md`
- Systemgrenzen: `docs/architecture/SYSTEM_MAP.md`
- Deploy-Code bleibt unter `blocksy-child/`; diesen Root nicht umbenennen oder verschieben.

## Doku-Regel

Kein neues Root-Markdown fuer Plaene, Session-Notizen oder Zwischenstaende. Kurzlebiges gehoert nach `.ai/memory/`; wiederholbare Ablaeufe werden Skills unter `agents/skills/`.

## CLI

```bash
git status --short
rg --files
rg -n "pattern" path/
php -l blocksy-child/path/to/file.php
find blocksy-child -name '*.php' -print0 | xargs -0 -n1 php -l
sh scripts/check-german-copy.sh
git diff --stat
```
