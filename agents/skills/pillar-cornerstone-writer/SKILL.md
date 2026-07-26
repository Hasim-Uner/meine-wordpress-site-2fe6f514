---
name: pillar-cornerstone-writer
description: Write conversion-focused cornerstone articles for WordPress pillar categories. Use for long-form category articles, SEO packaging, slug proposals, internal-link plans, or decision-maker educational pages.
---

# Pillar Cornerstone Writer

Use this skill when the output is a durable pillar article, not a short blog post.

## Run First

Erst das Ownership-Gate, dann das Scaffold. Ohne freie Ziel-Query entsteht keine Seite:

```bash
bash agents/skills/seo-agent/scripts/intent-gate.sh check "<ziel-query>" "<geplanter-slug>"
agents/skills/pillar-cornerstone-writer/scripts/scaffold.sh "Topic" "optional-slug"
```

The script prints the required package skeleton with a normalized slug.

## Hard Rule — Ownership vor Produktion

- Exit 1 aus dem Gate heisst: **keine neue Seite.** Stattdessen den Owner ausbauen
  oder den Intent belegbar abgrenzen und die Registry-Zeile ergaenzen.
- Nach dem Anlegen die Query in `docs/seo/query-ownership.csv` eintragen — sonst
  faellt die naechste Seite in dieselbe Luecke.

## Writing Rules

- Write for founders, CEOs, CMOs, and sales-adjacent decision-makers.
- Keep the progression `Problem -> Mechanism -> Proof logic -> Action`.
- Use short paragraphs and consistent terminology.
- End with a soft strategic CTA, not a hard sell.
- Suggest internal links to one service page and supporting cluster articles.

## AEO — Antwortbare Abschnitte

Das Theme erzeugt FAQPage-Schema aus Überschriften, die auf `?` enden
(`inc/org-schema.php:369-380`, Opt-in per SCF-Feld `enable_faq_schema`). Wer dafür
schreibt, muss wissen, wie der Parser arbeitet — sonst entsteht leeres oder
aufgeblähtes Schema.

Vor dem Schreiben lesen: `references/aeo-answer-patterns.md`. Die vier Regeln, die
am häufigsten verletzt werden:

- **Fragezeichen ist das letzte Zeichen der Überschrift.** „… Lead? Ein Überblick"
  wird nicht erkannt.
- **Erster Satz beantwortet die Frage vollständig.** Kein „das kommt darauf an".
- **Frage-Block 40–120 Wörter.** Die Antwort reicht bis zur nächsten Überschrift —
  ein langer Block landet komplett im Schema.
- **Keine Tabellen im Frage-Block.** Markup wird entfernt, die Tabelle wird zu
  unlesbarem Fließtext.

Mindestens zwei bis vier Frage-Überschriften pro Cornerstone, abgeleitet aus echten
Suchanfragen — nicht erfunden.

## Deliver

1. SEO title
2. Meta description
3. URL slug
4. Full article in Markdown
5. Suggested internal links
6. Optional image brief
7. Zeile für `docs/seo/query-ownership.csv` (Query, Owner-URL, Intent, Beleg)
8. WP-Admin-Schritte: SCF-Feld `FAQ-Schema erzwingen` aktivieren, Beitrag einmal
   speichern, `acceptedAnswer.text` auf Lesbarkeit prüfen
