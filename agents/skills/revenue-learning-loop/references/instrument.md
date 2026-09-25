# Measurement Readiness

Run both repo checks:

```bash
python3 agents/skills/revenue-learning-loop/scripts/tracking-inventory.py
python3 agents/skills/revenue-learning-loop/scripts/outcome-contract-check.py
```

The inventory proves repo hooks/emitters exist. The outcome check verifies the
CRM qualification enum reaches route-level Cockpit aggregation. Neither proves
external mappings, collection, consent or fresh production data.

Inspect relevant sections of `docs/systems/seo-cockpit.md` and Source Ownership /
Event Contract in [measurement-contract](measurement-contract.md). Verify external
mapping and fresh CRM statuses/attribution for the decision-critical signal.
A blocked outcome contract or missing mapping means `INSUFFICIENT`, not zero
conversions. Use existing Cockpit/Revenue Command Center signals; distinguish
GSC demand, Koko traffic, CTA events, requests, qualified, progressed and won.
