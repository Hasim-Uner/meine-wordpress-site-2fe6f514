# Attribution

Keep the lead system 100% cookie-banner-free by default.

- Do not add a global cookie banner to make form tracking work.
- Do not add marketing pixels, GA/GTM events, Meta CAPI browser calls, or third-party trackers to conversion forms.
- Use Koko Analytics standards only for analytics context.
- Parse UTM parameters, referrer, and click IDs in the background with vanilla JavaScript.
- Append attribution fields invisibly to the CRM payload; do not make them user-facing form fields.
- Exception by design: the optional, self-reported question how someone found the site (`referral_source`, options in `nexus_get_inquiry_referral_options()`) is a visible field. It complements technical attribution for word of mouth, LinkedIn or AI assistants and must stay optional.
- Keep attribution storage session-scoped or payload-scoped. Do not introduce persistent cookies for attribution.

The visible consent checkbox in a form is processing consent for the submitted contact data. It is not a tracking-banner substitute.
