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
2. **Hub gaps** — high-value pages (Marktcheck, E3, ergebnisse, blog, agentur) not linked from enough places
3. **Broken internal links** — `home_url()` calls pointing to slugs without a matching template
4. **Over-linked pages** — pages with too many outbound links diluting equity
5. **Anchor text quality** — generic "hier klicken" vs descriptive anchors

## Link Priority Tiers

| Tier | Pages | Min inbound links |
|------|-------|--------------------|
| 1 | /solar-waermepumpen-leadgenerierung/ | 8+ |
| 2 | /case-study-solar-leadgenerierung/, /ergebnisse/, /blog/, /wordpress-agentur-hannover/ | 5+ |
| 3 | Service pages, cornerstone content | 3+ |
| 4 | Glossary terms, blog posts | 1+ |

## Deliver

- Link matrix (page → outbound links)
- Orphan page list
- Specific link suggestions with anchor text and placement
- Files and line numbers to edit
