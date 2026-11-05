---
name: internal-linking-audit
description: Audit internal link structure across all theme templates. Use to find orphan pages, missing cross-links, and link-equity distribution issues.
---

# Internal Linking Audit

Use this skill to find and fix internal linking gaps across the site.

## Run First

```bash
agents/skills/internal-linking-audit/scripts/link-map.sh
```

The script scans all PHP templates for internal links and prints a link matrix.

## What It Finds

1. **Orphan pages** — templates with no incoming links from other templates
2. **Hub gaps** — hubs read from `llms.txt` (`## Primäre Einstiege`) that few templates link to; pass paths as arguments to override
3. **Broken internal links** — `home_url()` calls pointing to slugs without a matching template
4. **Over-linked pages** — pages with too many outbound links diluting equity
5. **Anchor text quality** — generic "hier klicken" vs descriptive anchors

## Link Priority

Do not hard-code routes or tiers here. Read each route's role and intent from `docs/architecture/CONVERSION_ROUTING.md` and `llms.txt` (retired routes such as `/ergebnisse/` are not link targets). Keep money pages separated by intent: direct WordPress at `/`, agency at `/whitelabel-retainer/`, energy at `/solar-waermepumpen-leadgenerierung/`. No global Solar priority.

Minimum inbound links are a rule of thumb and starting value, not an SEO law: money pages 5+, hubs 3+, everything else 1+. `link-map.sh` applies the same heuristic (<3 ✗, <5 △).

## Deliver

- Link matrix (page → outbound links)
- Orphan page list
- Specific link suggestions with anchor text and placement
- Files and line numbers to edit
