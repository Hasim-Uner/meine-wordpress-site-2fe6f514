# Buyer Research Evidence Standard

Use this standard whenever evidence is collected, classified, synthesized, or
considered for `docs/standards/VOICE_OF_CUSTOMER.md`.

## Evidence classes

| Class | Meaning | Allowed use | VoC eligible |
| --- | --- | --- | --- |
| `direct_voc` | Exact statement made directly to this business by a confirmed target buyer: prospect, lost lead, customer, Marktcheck respondent, or interviewee | `voc` | Yes, after manual review |
| `direct_note` | Paraphrased or incomplete note from a direct interaction with a confirmed target buyer | `hypothesis` | No |
| `proxy` | Public statement or review by a confirmed/probable target buyer outside a direct interaction | `hypothesis` | No |
| `market_language` | Competitor/portal copy, industry press, or search-demand data | `structure_only` | No |
| `inference` | Agent interpretation, synthetic persona, or unsupported hypothesis | `blocked` | No |

`direct_voc` describes the relationship to the evidence, not whether money was
paid. A verbatim sentence from a qualified lost lead can be direct VoC; a public
review from somebody else's customer remains proxy evidence.

If a direct conversation was recorded only from memory or as a paraphrase,
classify it as `direct_note`. It can guide the next interview but cannot be
reconstructed, polished, or promoted into a quote later.

## Source-kind matrix

- `direct_voc`: `crm_form`, `marktcheck`, `sales_call`, `interview`, `email`,
  `survey`
- `direct_note`: `crm_form`, `marktcheck`, `sales_call`, `interview`, `email`,
  `survey`
- `proxy`: `forum`, `community`, `review`, `public_comment`
- `market_language`: `competitor_page`, `portal_page`, `industry_press`,
  `search_data`
- `inference`: `agent_inference`

Competitor testimonials embedded in a competitor page are not automatically
proxy evidence: authorship, target fit, and source integrity are not established.
Classify the page as `market_language` unless the original buyer statement can
be independently resolved.

## Ledger schema

Store working ledgers under `.ai/memory/` as UTF-8 semicolon CSV. Use this exact
header:

```csv
id;source_key;evidence_class;source_kind;source_locator;source_date;captured_at;segment;role;target_fit;verbatim;anonymized;allowed_use;topic;context;evidence_text
```

Required values:

- `id`: stable unique ID such as `BR-001`
- `source_key`: stable anonymized speaker/author/document key such as `P-004`;
  all statements by the same person use the same key across channels
- `source_locator`: anonymized internal ID for direct evidence; full URL for
  public proxy/market sources
- `source_date`, `captured_at`: ISO date `YYYY-MM-DD`
- `segment`: do not collapse materially different trades or offers
- `role`: buyer/decision role, not a cute invented persona
- `target_fit`: `confirmed`, `probable`, `unknown`, or `not_target`
- `verbatim`, `anonymized`: `true` or `false`
- `topic`: one of `problem`, `trigger`, `outcome`, `alternative`, `objection`,
  `anxiety`, `criterion`, `vocabulary`, `proof`, or `awareness`
- `context`: what prompted the statement or observation
- `evidence_text`: exact quote when `verbatim=true`; otherwise a clearly marked
  observation, never written in quotation marks as if spoken

Example rows are intentionally synthetic and must never be promoted:

```csv
BR-TEST-001;P-TEST-001;direct_voc;marktcheck;CRM-TEST-17;2026-08-01;2026-08-10;Photovoltaik;Geschäftsführung;confirmed;true;true;voc;objection;Testfixture ohne reale Person;"[SYNTHETISCHES TESTZITAT]"
BR-TEST-002;PUBLIC-TEST-002;proxy;forum;https://example.invalid/thread/17;2026-08-02;2026-08-10;SHK;Betriebsleitung;probable;true;true;hypothesis;trigger;Öffentliche Testfixture;"[SYNTHETISCHES PROXY-ZITAT]"
```

## Confidence labels

These labels guide decisions; they do not estimate population prevalence.

- `High`: same theme in at least three independent direct sources within one
  segment, ideally unprompted or supported by behavior
- `Medium`: two independent direct sources, or one direct source corroborated
  by several independent proxy sources
- `Low`: one direct source, proxy-only pattern, unclear target fit, or prompted
  statement; unverified `direct_note` observations also remain low
- `Hypothesis`: market language, search data, inference, or conflicting evidence
  without direct buyer confirmation

Never upgrade confidence by counting copies, syndications, or repeated posts
from the same person as independent sources. One person remains one independent
source even when they appear in several channels or contribute several rows.

## Synthesis table

For each theme report:

| Theme | Segment | Independent sources | Evidence mix | Intensity | Contradictions/bias | Confidence | Implication | Next evidence needed |
| --- | --- | ---: | --- | --- | --- | --- | --- | --- |

Keep exact `direct_voc` quotes, `direct_note` observations, proxy phrases,
market terms, and inference in separate output blocks. Only the first block can
feed the VoC canon.

## Promotion gate

An entry can be proposed for `VOICE_OF_CUSTOMER.md` only when all are true:

1. `evidence_class=direct_voc`
2. `allowed_use=voc`
3. `target_fit=confirmed`
4. `verbatim=true`
5. `anonymized=true`
6. source and date are retained in the required VoC attribution format
7. a human manually verifies that wording and context were not altered

Promotion does not make every phrase publishable. Brand bans, proof canons,
legal constraints, and contextual accuracy still apply downstream.
