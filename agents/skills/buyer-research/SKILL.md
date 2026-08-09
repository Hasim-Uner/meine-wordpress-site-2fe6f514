---
name: buyer-research
description: Research and synthesize evidence-backed buyer language, jobs-to-be-done, trigger events, pains, objections, alternatives, decision criteria, and awareness for Solar-, Photovoltaik-, Wärmepumpen-, Energie- and SHK decision-makers. Use when customer evidence is thin; when analyzing sales calls, interviews, emails, CRM or Marktcheck free text, lost leads, surveys, public buyer discussions, reviews, competitor language, or research notes; when preparing a funnel, offer, landing page, proof plan, or conversion copy; when filling VOICE_OF_CUSTOMER.md; or when the user asks what buyers think, say, fear, compare, or need. Separates verbatim direct Voice of Customer, paraphrased direct notes, proxy evidence, market language, and inference so weak evidence never becomes invented customer truth.
---

# Buyer Research

Research buyer reality before changing offers or copy. Produce an evidence
ledger and synthesis; do not write the landing page in this skill.

## First Command

```bash
python3 agents/skills/buyer-research/scripts/evidence-check.py --inventory
```

The inventory distinguishes an empty validated VoC canon from missing raw data.
If WordPress CRM data is in scope, run `scripts/harvest-voice-of-customer.sh` on
the server with WP-CLI; it writes sensitive raw material only to `.ai/memory/`.

## Load First

1. `docs/standards/VOICE_OF_CUSTOMER.md`
2. `docs/standards/BRAND_AND_COPY.md`
3. User-provided transcripts, exports, messages, or research sources
4. `references/evidence-standard.md` before classifying or promoting evidence

## Workflow

1. **Frame the decision** — name the segment, buyer role, funnel decision, and
   what evidence would change that decision.
2. **Collect direct evidence first** — include prospects, lost/no-decision
   leads, Marktcheck answers, sales calls, emails, and target-buyer interviews;
   paid customers are not the only valid direct source.
3. **Use proxy evidence only for gaps** — public statements by identifiable
   target buyers may form hypotheses. Competitor pages and industry press are
   market language, never buyer voice.
4. **Record every item** in a semicolon CSV ledger under
   `.ai/memory/buyer-evidence-YYYY-MM-DD.csv` using the reference schema.
5. **Validate the ledger** before synthesis:

   ```bash
   python3 agents/skills/buyer-research/scripts/evidence-check.py \
     .ai/memory/buyer-evidence-YYYY-MM-DD.csv
   ```

6. **Synthesize by segment** — cluster problem, trigger, desired outcome,
   alternative, objection, anxiety, decision criterion, and vocabulary. Show
   source count, evidence mix, intensity, contradictions, bias, and confidence.
7. **Promote cautiously** — only manually reviewed, anonymized, verbatim
   `direct_voc` entries may enter `VOICE_OF_CUSTOMER.md`. A paraphrased direct
   interaction is `direct_note`, remains a hypothesis, and is never rebuilt as
   a quote. Update the canon only when the task explicitly authorizes it.
8. **Hand off** — offer/funnel implications go to `offer-funnel-intelligence`;
   page copy goes to `seo-conversion-copywriting`; competitor page structure
   goes to `copy-anatomy`; query demand goes to `seo-agent`.

## Hard Rules

- Never invent, smooth, merge, or back-translate a quote.
- Never call proxy, competitor, industry, search, or agent-generated language
  Voice of Customer.
- A proxy finding never removes a `[KUNDENSPRACHE FEHLT: ...]` marker.
- Keep names, companies, emails, phone numbers, and raw CRM exports out of Git.
- Do not combine Solar/PV, Wärmepumpe, Energie, and SHK segments when their
  evidence differs.
- Count one person as one independent source, even across several channels or
  evidence rows.
- Treat frequency labels as research confidence, not statistical prevalence.
- A raw quote may contain a banned term; that does not make the term publishable.
- Do not fabricate numbers, results, guarantees, personas, or awareness stages.
- Do not recommend A/B tests before obvious clarity and qualification debt is
  resolved or before the required traffic and conversion volume is known.

## Deliver

- `Evidence ledger` — validated source-level register, kept gitignored
- `Theme map` — frequency × intensity × evidence quality, split by segment
- `Buyer language` — direct quotes separate from direct notes, proxy phrases,
  and market terms
- `Decision map` — triggers, JTBD, alternatives, objections, anxieties, criteria
- `Confidence and gaps` — what is known, hypothesized, contradicted, or missing
- `Handoff brief` — implications for offer, funnel, landing page, proof, and copy
- `Promotion list` — direct quotes eligible for manual VoC review; never auto-copy

Run regression tests after changing the schema or validator:

```bash
bash agents/skills/buyer-research/tests/run-evidence-validator-tests.sh
```
