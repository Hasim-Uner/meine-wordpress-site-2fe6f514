/* solar-leadgenerierung-solara.js
   SOLARA Landing — B2B Marktcheck im Hero.
   3-stufige Progressive-Disclosure-Sequenz (Vertrieb → Akquisekosten → Befund-Daten).
   Submit → /wp-json/nexus/v1/audit-request (intake_variant=energy_systems).
   Success-Screen mit Cal.com-Direktbuchung + händischer SLA-Antwort.
   Vanilla JS. Keine Dependencies. Touch- & Keyboard-accessible. */
(function () {
  'use strict';

  var ROOT_SELECTOR = '.solara-landing';
  var CFG = (window.NexusMarktcheckConfig && typeof window.NexusMarktcheckConfig === 'object')
    ? window.NexusMarktcheckConfig : {};

  // ── Progressive Disclosure: 3-stufige B2B-Sequenz ─────────────
  // Step 1 — Klickbasierter Einstieg ohne Datenrisiko.
  // Step 2 — Problemverankerung über Akquisekosten (Sunk-Cost-Trigger).
  // Step 3 — Erst danach freigeschaltete Business-Datenfelder.
  var QUIZ_STEPS = [
    {
      key: 'sales_team_size',
      label: 'Vertrieb',
      title: 'Wer verkauft bei Ihnen?',
      hint: 'Das System richtet sich an Betriebe mit eigenem Vertrieb — die Antwort entscheidet über den Fit.',
      kind: 'pick',
      options: [
        { v: 'none',           t: 'Die Geschäftsführung verkauft selbst', s: 'Noch kein eigenes Vertriebsteam — Abschluss ist Chefsache.' },
        { v: 'one',            t: '1 Person im Vertrieb',                 s: 'Eine zentrale Anlaufstelle, klare Verantwortung.' },
        { v: 'two_to_five',    t: '2–5 Personen im Vertrieb',             s: 'Vertriebsteam vorhanden, Pipeline-Routing geregelt.' },
        { v: 'more_than_five', t: 'Mehr als 5 Personen im Vertrieb',      s: 'Strukturierter Vertrieb mit eigener Pipeline-Logik.' }
      ]
    },
    {
      key: 'portal_margin_loss',
      label: 'Akquisekosten',
      title: 'Was kostet Sie Akquise heute?',
      hint: 'Portal-Leads, Google Ads, Agentur — alles zusammengenommen, grob geschätzt. Es geht um den Druck auf Ihre Marge.',
      kind: 'pick',
      options: [
        { v: 'low',    t: 'Gering',    s: 'Portal-Leads und Werbekosten sind wirtschaftlich noch vertretbar.' },
        { v: 'medium', t: 'Deutlich',  s: 'CPL und Anfragequalität drücken die Marge in Grenzprojekten.' },
        { v: 'high',   t: 'Erheblich', s: 'Budget und Vertriebszeit verbrennen in Anfragen, die nicht kaufen.' }
      ]
    },
    {
      key: 'contact',
      label: 'Marktcheck',
      title: 'Wohin darf der Befund?',
      hint: 'Fünf Angaben — mehr braucht der Marktcheck nicht. Die Firmen-PLZ dient der Regions-Verfügbarkeitsprüfung.',
      kind: 'form'
    }
  ];

  // ── DOM-helpers ──────────────────────────────────────────────
  function ready(fn) {
    if (document.readyState !== 'loading') { fn(); return; }
    document.addEventListener('DOMContentLoaded', fn, { once: true });
  }
  function el(tag, attrs, children) {
    var node = document.createElement(tag);
    if (attrs) {
      Object.keys(attrs).forEach(function (k) {
        var v = attrs[k];
        if (v === null || v === undefined || v === false) return;
        if (k === 'className') node.className = v;
        else if (k === 'html') node.innerHTML = v;
        else if (k.indexOf('on') === 0 && typeof v === 'function') node.addEventListener(k.substring(2).toLowerCase(), v);
        else if (k === 'dataset') Object.keys(v).forEach(function (dk) { node.dataset[dk] = v[dk]; });
        else node.setAttribute(k, v === true ? '' : String(v));
      });
    }
    if (children) {
      (Array.isArray(children) ? children : [children]).forEach(function (c) {
        if (c === null || c === undefined || c === false) return;
        node.appendChild(typeof c === 'string' ? document.createTextNode(c) : c);
      });
    }
    return node;
  }
  var ARROW_SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
  var CHECK_SVG = '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="24" cy="24" r="21"/><path d="M14 24l8 8 14-16"/></svg>';

  function track(action, extra) {
    if (typeof window === 'undefined') return;
    try {
      if (window.dataLayer && typeof window.dataLayer.push === 'function') {
        window.dataLayer.push(Object.assign({ event: action, event_category: 'lead_gen' }, extra || {}));
      }
    } catch (e) { /* swallow */ }
  }

  function normalizeInternalAttributionUrl(url) {
    if (!url || !window.location || !window.location.origin) {
      return '';
    }

    try {
      var parsed = new URL(url, window.location.origin);
      if (parsed.origin !== window.location.origin) {
        return '';
      }

      var path = parsed.pathname || '/';
      if (path !== '/' && path.slice(-1) !== '/') {
        path += '/';
      }

      return parsed.origin + path;
    } catch (error) {
      return '';
    }
  }

  function getLeadAttributionPayload() {
    if (window.NexusCore && typeof window.NexusCore.getLeadAttributionPayload === 'function') {
      return window.NexusCore.getLeadAttributionPayload();
    }

    var landingUrl = normalizeInternalAttributionUrl(window.location.href);
    var referrerUrl = '';

    if (document.referrer) {
      try {
        referrerUrl = String(new URL(document.referrer, window.location.origin)).split('#')[0];
      } catch (error) {
        referrerUrl = '';
      }
    }

    return {
      landing_page_url: landingUrl,
      entry_page_url: landingUrl,
      previous_internal_url: '',
      referrer_url: referrerUrl
    };
  }

  // ── Quiz controller ──────────────────────────────────────────
  function QuizController(mount) {
    var state = {
      step: 0,
      answers: {},
      submitting: false,
      submitError: null,
      done: false,
      qualification: null
    };

    var totalSteps = QUIZ_STEPS.length;

    function setState(patch) {
      Object.keys(patch).forEach(function (k) { state[k] = patch[k]; });
      render();
    }

    function setAnswer(k, v) {
      var answers = Object.assign({}, state.answers);
      answers[k] = v;
      state.answers = answers;
    }

    function validateContact() {
      var a = state.answers;
      var errs = {};
      if (!a.company || a.company.trim().length < 2) errs.company = 'Bitte Ihr Unternehmen angeben.';
      if (!a.name || a.name.trim().length < 2) errs.name = 'Bitte Ihren Namen angeben.';
      if (!a.position || a.position.trim().length < 2) errs.position = 'Bitte Ihre Position im Unternehmen angeben.';
      if (!a.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(a.email)) errs.email = 'Bitte eine gültige geschäftliche E-Mail angeben.';
      // Konsumenten-Provider auf geschäftliche E-Mail-Felder ausschließen.
      if (a.email && /^[^\s@]+@(gmail|gmx|web|t-online|outlook|hotmail|yahoo|icloud|aol|live|mail|googlemail)\.(com|de|net|at|ch)$/i.test(a.email)) {
        errs.email = 'Bitte nutzen Sie Ihre geschäftliche E-Mail-Adresse (Firmen-Domain) — so kann ich Betrieb und Region eindeutig zuordnen. Keine eigene Domain? Schreiben Sie direkt an hasim@hasimuener.de.';
      }
      if (!a.postal_code || !/^[0-9]{5}$/.test(String(a.postal_code).trim())) errs.postal_code = 'Bitte eine fünfstellige Firmen-PLZ angeben.';
      if (!a.consent_privacy) errs.consent_privacy = 'Bitte den Datenschutzhinweis bestätigen.';
      return errs;
    }

    function submit() {
      if (state.submitting) return;
      var errs = validateContact();
      if (Object.keys(errs).length) {
        state.touched = Object.assign({}, state.touched || {}, { name: true, company: true, position: true, email: true, postal_code: true, consent_privacy: true });
        render();
        // Focus and scroll to the first invalid field so the user knows what to fix.
        var firstKey = Object.keys(errs)[0];
        setTimeout(function () {
          var input = mount.querySelector('[name="' + firstKey + '"]');
          if (!input) return;
          var wrap = input.closest('.sol-quiz-field, .sol-quiz-consent') || input;
          if (typeof wrap.scrollIntoView === 'function') {
            wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          try { input.focus({ preventScroll: true }); } catch (e) { input.focus(); }
        }, 0);
        return;
      }

      setState({ submitting: true, submitError: null });

      var endpoint = CFG.restEndpoint || '/wp-json/nexus/v1/audit-request';
      var attribution = getLeadAttributionPayload();
      var payload = {
        contract_version:   CFG.contractVersion || '',
        intake_variant:      'energy_systems',
        audit_type:          'b2b_system_intake',
        sales_team_size:     state.answers.sales_team_size || '',
        portal_margin_loss:  state.answers.portal_margin_loss || '',
        portal_streuverlust: state.answers.portal_margin_loss || '',
        lead_volume:         mapSalesTeamToLeadVolume(state.answers.sales_team_size),
        cpl_range:           mapMarginLossToCplRange(state.answers.portal_margin_loss),
        primary_bottleneck:  mapMarginLossToBottleneck(state.answers.portal_margin_loss),
        name:                state.answers.name || '',
        company:             state.answers.company || '',
        position:            state.answers.position || '',
        email:                state.answers.email || '',
        phone:                state.answers.phone || '',
        postal_code:          (state.answers.postal_code || '').trim(),
        page_url:             CFG.pageUrl || window.location.href,
        consent_privacy:      'accepted',
        company_website:      state.answers.company_website || ''
      };

      Object.keys(attribution).forEach(function (key) {
        if (!attribution[key]) return;
        payload[key] = attribution[key];
      });

      fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
      }).then(function (res) {
        return res.json().then(function (json) { return { ok: res.ok, json: json }; }).catch(function () { return { ok: res.ok, json: {} }; });
      }).then(function (r) {
        if (r.ok && r.json && r.json.ok) {
          var qualification = (r.json.qualification && typeof r.json.qualification === 'object') ? r.json.qualification : null;
          track('system_intake_submit_success', { funnel_stage: 'lead_captured' });
          if (qualification && qualification.status) {
            track('system_intake_qualification', {
              funnel_stage: 'lead_qualified',
              qualification_status: qualification.status,
              qualification_reason: qualification.reason || ''
            });
          }
          setState({ submitting: false, done: true, submitError: null, qualification: qualification });
        } else {
          var msg = (r.json && (r.json.message || r.json.error || r.json.code))
            ? (typeof r.json.message === 'string' ? r.json.message : 'Bitte Eingaben prüfen.')
            : 'Die Anfrage konnte gerade nicht gesendet werden. Bitte erneut versuchen.';
          track('system_intake_submit_error', { funnel_stage: 'submit_error' });
          setState({ submitting: false, submitError: msg });
        }
      }).catch(function () {
        track('system_intake_submit_error', { funnel_stage: 'submit_network' });
        setState({ submitting: false, submitError: 'Netzwerkfehler. Bitte erneut versuchen.' });
      });
    }

    function goNext() {
      if (state.step < totalSteps - 1) {
        setState({ step: state.step + 1 });
        track('system_intake_step_next', { step: state.step + 1 });
      }
    }

    function goBack() {
      if (state.step > 0) {
        setState({ step: Math.max(0, state.step - 1) });
        track('system_intake_step_back', { step: state.step });
      }
    }

    function pick(key, value) {
      setAnswer(key, value);
      track('system_intake_pick', { step: state.step + 1, field: key, value: value });
      // auto-advance — long enough for the user to register the chosen option
      setTimeout(function () {
        if (state.step < totalSteps - 1) {
          setState({ step: state.step + 1 });
        } else {
          render();
        }
      }, 600);
    }

    function reset() {
      state.step = 0;
      state.answers = {};
      state.submitting = false;
      state.submitError = null;
      state.done = false;
      state.qualification = null;
      state.touched = {};
      render();
      track('system_intake_reset');
    }

    function mapSalesTeamToLeadVolume(value) {
      if (value === 'more_than_five') return '51_bis_120';
      if (value === 'two_to_five') return '20_bis_50';
      return 'unter_20';
    }

    function mapMarginLossToCplRange(value) {
      if (value === 'high') return 'ueber_300';
      if (value === 'medium') return '151_bis_300';
      return '80_bis_150';
    }

    function mapMarginLossToBottleneck(value) {
      if (value === 'low') return 'tracking_klarheit';
      return 'lead_qualitaet';
    }

    // Pre-fill the Cal.com booking URL with the data the buyer just typed
    // into the Marktcheck, so the booking step is one click instead of a
    // second round of name+email entry.
    function buildCalcomUrl(baseUrl, answers) {
      var name = (answers.name || '').trim();
      var email = (answers.email || '').trim();
      var company = (answers.company || '').trim();
      var position = (answers.position || '').trim();
      var plz = (answers.postal_code || '').trim();
      var noteParts = ['Aus Marktcheck'];
      if (company) noteParts.push('Firma: ' + company);
      if (position) noteParts.push('Position: ' + position);
      if (plz) noteParts.push('PLZ: ' + plz);

      var params = [];
      if (name) params.push('name=' + encodeURIComponent(name));
      if (email) params.push('email=' + encodeURIComponent(email));
      params.push('notes=' + encodeURIComponent(noteParts.join(' · ')));

      return baseUrl + (baseUrl.indexOf('?') === -1 ? '?' : '&') + params.join('&');
    }

    // ── render helpers ────────────────────────────────────────
    function renderProgress() {
      var pct = Math.round((state.step / (totalSteps - 1)) * 100);
      var dotsTrack = el('div', { className: 'sol-quiz-dots', 'aria-hidden': 'true' });
      for (var i = 0; i < totalSteps; i++) {
        var cls = 'sol-quiz-dot';
        if (i === state.step) cls += ' is-active';
        else if (i < state.step) cls += ' is-done';
        var dotIdx = i; // capture
        var dot = el('button', {
          type: 'button',
          className: cls,
          // nicht fokussierbar: der Dots-Track ist aria-hidden (dekorativer
          // Fortschritt); Tastatur-Rücknavigation läuft über „← Zurück".
          // Verhindert aria-hidden-focus, sobald erledigte Schritte erscheinen.
          tabindex: '-1',
          'aria-label': 'Zu Schritt ' + (i + 1),
          onClick: (function (idx) {
            return function () {
              if (idx < state.step) {
                state.step = idx;
                render();
                track('system_intake_dot_jump', { to_step: idx + 1 });
              }
            };
          })(dotIdx)
        }, [
          i < state.step ? el('span', { className: 'sol-quiz-dot-check', html: '✓' }) : null
        ]);
        dotsTrack.appendChild(dot);
      }
      return el('div', { className: 'sol-quiz-progress' }, [
        el('div', { className: 'sol-quiz-progress-meta' }, [
          el('span', null, 'Schritt ' + String(state.step + 1).padStart(2, '0') + ' / ' + String(totalSteps).padStart(2, '0')),
          el('span', null, pct + ' %')
        ]),
        el('div', { className: 'sol-quiz-progress-track' }, [
          el('div', { className: 'sol-quiz-progress-fill', style: 'width:' + pct + '%' }, [
            el('span', { className: 'sol-quiz-progress-shimmer', 'aria-hidden': 'true' })
          ])
        ]),
        dotsTrack
      ]);
    }

    function renderPickStep(step) {
      var picks = el('div', { className: 'sol-quiz-options', role: 'radiogroup', 'aria-label': step.title });
      step.options.forEach(function (o, i) {
        var sel = state.answers[step.key] === o.v;
        var children = [];
        children.push(el('span', { className: 'sol-quiz-opt-bullet', 'aria-hidden': 'true' }, [
          el('span', { className: 'sol-quiz-opt-bullet-dot' })
        ]));
        if (o.i) {
          children.push(el('span', { className: 'sol-quiz-opt-icon', 'aria-hidden': 'true' }, o.i));
        }
        children.push(el('span', { className: 'sol-quiz-opt-body' }, [
          el('span', { className: 'sol-quiz-opt-t' }, o.t),
          o.s ? el('span', { className: 'sol-quiz-opt-s' }, o.s) : null
        ]));
        children.push(el('span', { className: 'sol-quiz-opt-idx' }, '0' + (i + 1)));
        children.push(el('span', { className: 'sol-quiz-opt-arrow', 'aria-hidden': 'true', html: ARROW_SVG }));
        var btn = el('button', {
          type: 'button',
          className: 'sol-quiz-opt' + (sel ? ' is-sel' : ''),
          role: 'radio',
          'aria-checked': sel ? 'true' : 'false',
          style: '--sol-opt-delay:' + (i * 60) + 'ms',
          dataset: { trackAction: 'submit_solar_intake', trackCategory: 'lead_gen', trackSection: 'hero_intake', trackIntent: 'intake_step_' + (state.step + 1) },
          onClick: function () { pick(step.key, o.v); }
        }, children);
        picks.appendChild(btn);
      });
      return picks;
    }

    function renderField(opts) {
      var a = state.answers;
      var t = state.touched || {};
      var v = a[opts.k] || '';
      var errMsg = (t[opts.k] && opts.validator) ? opts.validator(v) : null;
      var wrap = el('label', { className: 'sol-quiz-field' + (opts.full ? ' sol-quiz-field--full' : '') + (errMsg ? ' is-err' : '') }, [
        el('span', { className: 'sol-quiz-field-lbl' }, [
          opts.t,
          opts.req ? el('span', { className: 'sol-quiz-field-lbl-req' }, ' *') : null
        ])
      ]);

      var input;
      if (opts.kind === 'select') {
        input = el('select', {
          name: opts.k,
          autocomplete: opts.ac || 'off',
          'aria-invalid': errMsg ? 'true' : 'false',
          onChange: function (e) {
            setAnswer(opts.k, e.target.value);
            var touched = Object.assign({}, state.touched || {});
            touched[opts.k] = true;
            state.touched = touched;
            render();
          }
        });
        (opts.options || []).forEach(function (o) {
          var attrs = { value: o.v };
          if (o.v === v) attrs.selected = 'selected';
          if (o.disabled) attrs.disabled = 'disabled';
          input.appendChild(el('option', attrs, o.t));
        });
      } else {
        input = el('input', {
          type: opts.type || 'text',
          value: v,
          placeholder: opts.ph || '',
          autocomplete: opts.ac || 'off',
          inputmode: opts.im || null,
          maxlength: opts.maxlength || null,
          name: opts.k,
          onInput: function (e) { setAnswer(opts.k, e.target.value); },
          onBlur: function () {
            var touched = Object.assign({}, state.touched || {});
            touched[opts.k] = true;
            state.touched = touched;
            render();
          }
        });
      }

      wrap.appendChild(input);
      if (errMsg) wrap.appendChild(el('span', { className: 'sol-quiz-field-err' }, errMsg));
      return wrap;
    }

    function renderContactForm() {
      var form = el('form', {
        className: 'sol-quiz-form',
        novalidate: 'novalidate',
        dataset: { trackAction: 'submit_solar_intake', trackCategory: 'lead_gen', trackSection: 'hero_intake' },
        onSubmit: function (e) { e.preventDefault(); submit(); }
      });

      var rationale = el('div', { className: 'sol-quiz-rationale', role: 'note' }, [
        el('p', { className: 'sol-quiz-rationale-h' }, 'Daten-Integrität · Letzter Schritt des Marktchecks'),
        el('p', { className: 'sol-quiz-rationale-b' },
          'Sie haben Vertrieb und Akquisekosten skizziert — jetzt brauche ich die Firmen-Eckdaten, um Domain, Region und Fit persönlich-händisch zu prüfen und den Befund an den richtigen Entscheider zu senden.')
      ]);
      form.appendChild(rationale);

      form.appendChild(renderField({ k: 'company', t: 'Firma', type: 'text', ph: 'Mustermann Solar GmbH', req: true, ac: 'organization', full: true,
        validator: function (v) { return (!v || v.trim().length < 2) ? 'Bitte Firma angeben.' : null; }
      }));
      form.appendChild(renderField({ k: 'name', t: 'Ansprechpartner', type: 'text', ph: 'Max Mustermann', req: true, ac: 'name',
        validator: function (v) { return (!v || v.trim().length < 2) ? 'Bitte Namen angeben.' : null; }
      }));
      form.appendChild(renderField({ k: 'position', t: 'Position', kind: 'select', req: true, ac: 'organization-title',
        options: [
          { v: '',                              t: '— Bitte wählen —',              disabled: true },
          { v: 'Geschäftsführung / Inhaber',    t: 'Geschäftsführung / Inhaber' },
          { v: 'Vertriebsleitung',              t: 'Vertriebsleitung' },
          { v: 'Marketing / Web-Verantwortung', t: 'Marketing / Web-Verantwortung' },
          { v: 'Andere',                        t: 'Andere' }
        ],
        validator: function (v) { return !v ? 'Bitte Position auswählen.' : null; }
      }));
      form.appendChild(renderField({ k: 'email', t: 'Geschäftliche E-Mail', type: 'email', ph: 'max@solar-betrieb.de', req: true, ac: 'email',
        validator: function (v) {
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v || ''))) return 'Gültige E-Mail nötig.';
          if (/^[^\s@]+@(gmail|gmx|web|t-online|outlook|hotmail|yahoo|icloud|aol|live|mail|googlemail)\.(com|de|net|at|ch)$/i.test(String(v || ''))) return 'Bitte die E-Mail-Adresse Ihrer Firmen-Domain verwenden — sie ordnet Betrieb und Region eindeutig zu.';
          return null;
        }
      }));
      form.appendChild(renderField({ k: 'postal_code', t: 'Firmen-PLZ', type: 'text', ph: '30159', req: true, ac: 'postal-code', im: 'numeric',
        maxlength: '5',
        validator: function (v) {
          return /^[0-9]{5}$/.test(String(v || '').trim()) ? null : 'Bitte eine fünfstellige PLZ angeben — sie steuert die Regions-Verfügbarkeitsprüfung.';
        }
      }));

      // Honeypot
      var hp = el('input', {
        type: 'text', tabindex: '-1', autocomplete: 'off',
        className: 'sol-quiz-hp', 'aria-hidden': 'true',
        name: 'company_website',
        onInput: function (e) { setAnswer('company_website', e.target.value); }
      });
      form.appendChild(hp);

      // Consent
      var consentTouched = !!(state.touched || {}).consent_privacy;
      var consentInvalid = consentTouched && !state.answers.consent_privacy;
      var consentLbl = el('label', { className: 'sol-quiz-consent' + (consentInvalid ? ' is-err' : '') });
      var consentInput = el('input', {
        type: 'checkbox',
        name: 'consent_privacy',
        checked: state.answers.consent_privacy ? 'checked' : null,
        'aria-invalid': consentInvalid ? 'true' : 'false',
        onChange: function (e) {
          setAnswer('consent_privacy', e.target.checked ? 'accepted' : '');
          var touched = Object.assign({}, state.touched || {});
          touched.consent_privacy = true;
          state.touched = touched;
          render();
        }
      });
      consentLbl.appendChild(consentInput);
      var consentText = el('span', { html: 'Ich akzeptiere die <a href="' + (CFG.privacyUrl || '/datenschutz/') + '" target="_blank" rel="noopener">Datenschutzhinweise</a> und möchte zu meiner Anfrage kontaktiert werden.' });
      consentLbl.appendChild(consentText);
      form.appendChild(consentLbl);
      if (consentInvalid) {
        form.appendChild(el('span', { className: 'sol-quiz-field-err', role: 'alert' }, 'Bitte den Datenschutzhinweis bestätigen, um den Marktcheck zu starten.'));
      }

      // Error banner
      if (state.submitError) {
        form.appendChild(el('div', { className: 'sol-quiz-error', role: 'alert' }, state.submitError));
      }

      var errs = validateContact();
      var canSubmit = Object.keys(errs).length === 0 && !state.submitting;

      var submitBtn = el('button', {
        type: 'submit',
        className: 'sol-quiz-submit' + (canSubmit ? '' : ' is-dis') + (state.submitting ? ' is-loading' : ''),
        'aria-disabled': canSubmit ? 'false' : 'true',
        dataset: {
          trackAction: 'submit_solar_intake',
          trackCategory: 'lead_gen',
          trackSection: 'hero_intake',
          trackFunnelStage: 'lead_capture_submit'
        }
      }, [
        el('span', null, state.submitting ? 'Wird gesendet …' : 'Befund in 48 h anfordern'),
        el('span', { className: 'sol-quiz-submit-arrow', 'aria-hidden': 'true', html: ARROW_SVG })
      ]);
      form.appendChild(submitBtn);

      form.appendChild(el('p', { className: 'sol-quiz-fineprint' }, 'Keine Zahlungsdaten, kein Abo · Regions-Verfügbarkeitsprüfung · Manuelle Erst-Analyse · DSGVO'));

      return form;
    }

    function renderSuccess() {
      var first = (state.answers.name || '').trim().split(' ')[0];
      var fallbackHeadline = 'Danke' + (first ? ', ' + first : '') + '.';
      var qualification = state.qualification || { status: 'qualified', reason: 'sweet_spot' };
      var isNurture = qualification.status === 'nurture';
      var headline = qualification.headline || fallbackHeadline;
      var message = qualification.message || 'Ihr Marktcheck ist eingegangen. Ich prüfe Domain, Region und Fit persönlich-händisch und sende den Befund in der Regel innerhalb von 48 Stunden, spätestens 2 Werktage, an Ihre geschäftliche E-Mail.';
      var ticketId = qualification.ticket_id || '';
      var deadlineHuman = qualification.response_deadline_human || '';
      var proof = (!isNurture && qualification.proof && typeof qualification.proof === 'object') ? qualification.proof : null;
      var calcom = buildCalcomUrl(CFG.calcomUrl || 'https://cal.com/hasim-uener/30min', state.answers);
      var caseUrl = CFG.caseUrl || '/case-study-solar-leadgenerierung/';

      var ctaRow;
      if (isNurture) {
        ctaRow = el('div', { className: 'sol-quiz-success-row' }, [
          el('a', {
            href: caseUrl,
            className: 'is-primary',
            dataset: {
              trackAction: 'cta_solar_success_to_case_study',
              trackCategory: 'proof',
              trackSection: 'intake_success_nurture',
              trackFunnelStage: 'nurture_resource'
            }
          }, [
            el('span', null, 'Case Study lesen'),
            el('span', { 'aria-hidden': 'true', html: ARROW_SVG })
          ])
        ]);
      } else {
        ctaRow = el('div', { className: 'sol-quiz-success-row' }, [
          el('a', {
            href: calcom,
            className: 'is-primary',
            target: '_blank',
            rel: 'noopener',
            dataset: {
              trackAction: 'cta_solar_calcom_book',
              trackCategory: 'lead_gen',
              trackSection: 'intake_success',
              trackFunnelStage: 'calendar_open'
            }
          }, [
            el('span', null, '30-Min-Gespräch direkt buchen'),
            el('span', { 'aria-hidden': 'true', html: ARROW_SVG })
          ]),
          el('a', {
            href: caseUrl,
            className: 'is-ghost',
            dataset: {
              trackAction: 'cta_solar_success_to_case_study',
              trackCategory: 'proof',
              trackSection: 'intake_success'
            }
          }, [
            el('span', null, 'Case Study lesen'),
            el('span', { 'aria-hidden': 'true', html: ARROW_SVG })
          ])
        ]);
      }

      var children = [
        el('div', { className: 'sol-quiz-success-icon', 'aria-hidden': 'true', html: CHECK_SVG }),
        el('h3', { className: 'sol-quiz-success-h' }, headline)
      ];

      if (ticketId) {
        children.push(el('p', { className: 'sol-quiz-success-ticket sol-mono' }, [
          el('span', { className: 'sol-quiz-success-ticket-label' }, 'Vorgang '),
          el('span', { className: 'sol-quiz-success-ticket-id' }, ticketId)
        ]));
      }

      children.push(el('p', { className: 'sol-quiz-success-sub' }, message));

      if (deadlineHuman) {
        children.push(el('p', { className: 'sol-quiz-success-deadline' }, [
          el('span', { className: 'sol-quiz-success-deadline-label' }, 'Schriftliche Antwort bis '),
          el('strong', null, deadlineHuman)
        ]));
      }

      if (proof) {
        children.push(el('aside', { className: 'sol-quiz-success-proof' }, [
          el('span', { className: 'sol-quiz-success-proof-label sol-mono' }, proof.label || 'Proof'),
          el('p', { className: 'sol-quiz-success-proof-body' }, proof.body || '')
        ]));
      }

      children.push(ctaRow);
      children.push(el('p', { className: 'sol-quiz-success-fineprint' }, 'Bestätigung an ' + (state.answers.email || 'Ihre E-Mail')));
      children.push(el('button', {
        type: 'button',
        className: 'sol-quiz-back',
        style: 'margin-top:4px;text-decoration:underline;text-underline-offset:3px;',
        onClick: reset
      }, 'Marktcheck neu starten'));

      return el('div', { className: 'sol-quiz-success' + (isNurture ? ' is-nurture' : '') }, children);
    }

    function render() {
      // clear
      while (mount.firstChild) mount.removeChild(mount.firstChild);

      // header card
      var head = el('div', { className: 'sol-cta-head' }, [
        el('span', { className: 'sol-cta-tag sol-mono' }, [
          el('span', { className: 'sol-cta-tag-dot', 'aria-hidden': 'true' }),
          'Marktcheck · Fit geprüft · 48-h-Befund'
        ]),
        el('span', { className: 'sol-cta-head-right sol-mono' }, 'Fit-Check')
      ]);
      mount.appendChild(head);

      if (state.done) {
        mount.appendChild(renderSuccess());
        return;
      }

      var quiz = el('div', { className: 'sol-quiz' });
      quiz.appendChild(renderProgress());

      var current = QUIZ_STEPS[state.step];
      var body = el('div', { className: 'sol-quiz-body' }, [
        el('div', { className: 'sol-quiz-label' }, current.label),
        // h2: schließt direkt an die Hero-h1 an (h3 erzeugte einen Level-Sprung)
        el('h2', { className: 'sol-quiz-title' }, current.title),
        el('p', { className: 'sol-quiz-hint' }, current.hint)
      ]);

      if (current.kind === 'pick') {
        body.appendChild(renderPickStep(current));
      } else {
        body.appendChild(renderContactForm());
      }

      quiz.appendChild(body);

      // nav (only for pick steps — contact form has its own submit)
      var nav = el('div', { className: 'sol-quiz-nav' });
      var canBack = state.step > 0;
      var backBtn = el('button', {
        type: 'button',
        className: 'sol-quiz-back' + (canBack ? '' : ' is-dis'),
        'aria-disabled': canBack ? 'false' : 'true',
        dataset: { trackAction: 'submit_solar_intake', trackCategory: 'lead_gen', trackSection: 'hero_intake', trackIntent: 'intake_back' },
        onClick: goBack
      }, '← Zurück');
      nav.appendChild(backBtn);

      var dots = el('div', { className: 'sol-quiz-dots', 'aria-hidden': 'true' });
      QUIZ_STEPS.forEach(function (_, i) {
        var cls = 'sol-quiz-dot';
        if (i === state.step) cls += ' is-cur';
        else if (i < state.step) cls += ' is-done';
        dots.appendChild(el('span', { className: cls }));
      });
      nav.appendChild(dots);

      if (current.kind === 'pick') {
        var canNext = !!state.answers[current.key] && state.step < totalSteps - 1;
        var nextBtn = el('button', {
          type: 'button',
          className: 'sol-quiz-next' + (canNext ? '' : ' is-dis'),
          'aria-disabled': canNext ? 'false' : 'true',
          dataset: { trackAction: 'submit_solar_intake', trackCategory: 'lead_gen', trackSection: 'hero_intake', trackIntent: 'intake_next' },
          onClick: function () { if (canNext) goNext(); }
        }, 'Weiter →');
        nav.appendChild(nextBtn);
      } else {
        nav.appendChild(el('span', { style: 'width:60px;' }));
      }

      quiz.appendChild(nav);
      mount.appendChild(quiz);
    }

    render();
    return { reset: reset };
  }

  // ── Page-wide setup ──────────────────────────────────────────

  /* Count-up für die Hero-Stats. Liest den serverseitig gerenderten
     Zieltext (z. B. "1.750+", "22 €", "12 %") direkt aus dem DOM,
     zählt per rAF hoch und stellt am Ende exakt den Originaltext
     wieder her — Canon-Werte bleiben damit die Quelle der Wahrheit. */
  function setupCountUp() {
    var nodes = document.querySelectorAll(ROOT_SELECTOR + ' [data-sol-countup]');
    if (!nodes.length) return;
    var reduced = false;
    try { reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (e) {}
    if (reduced || typeof window.requestAnimationFrame !== 'function') return;

    function animate(node) {
      var original = node.textContent;
      var match = /^([^0-9]*)([0-9][0-9.,]*)(.*)$/.exec(original.trim());
      if (!match) return;
      var target = parseInt(match[2].replace(/[.,]/g, ''), 10);
      if (!isFinite(target) || target <= 0) return;
      var prefix = match[1];
      var suffix = match[3];
      var t0 = null;
      var duration = 1100;
      function tick(now) {
        if (t0 === null) t0 = now;
        var p = Math.min(1, (now - t0) / duration);
        var eased = 1 - Math.pow(1 - p, 3);
        if (p < 1) {
          node.textContent = prefix + Math.round(target * eased).toLocaleString('de-DE') + suffix;
          window.requestAnimationFrame(tick);
        } else {
          node.textContent = original;
        }
      }
      window.requestAnimationFrame(tick);
    }

    try {
      if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            io.unobserve(entry.target);
            animate(entry.target);
          });
        }, { threshold: 0.4 });
        nodes.forEach(function (node) { io.observe(node); });
      } else {
        nodes.forEach(animate);
      }
    } catch (e) { /* Stats bleiben statisch — kein Schaden. */ }
  }

  /* Scroll-Reveal + Anfrage-Ketten-Choreografie.
     Gate: html.sol-anim kommt erst nach Reduced-Motion- und
     IntersectionObserver-Check — ohne JS (oder bei Reduced Motion)
     bleibt jeder Ausgangszustand sichtbar, es gibt keine
     opacity:0-Zustände ohne Fallback. Bereits sichtbare oder
     überscrollte Elemente werden vor dem Gate als .is-in markiert,
     damit ein Reload mitten auf der Seite nicht blinkt. */
  function setupMotion() {
    var reduced = false;
    try { reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (e) {}
    if (reduced || !('IntersectionObserver' in window)) return;
    var root = document.querySelector(ROOT_SELECTOR);
    if (!root) return;

    var reveals = [].slice.call(root.querySelectorAll('[data-sol-reveal]'));
    var chain = root.querySelector('.sol-chain');
    var vh = window.innerHeight || document.documentElement.clientHeight || 0;
    var pending = [];

    reveals.forEach(function (el) {
      if (el.getBoundingClientRect().top < vh * 0.88) el.classList.add('is-in');
      else pending.push(el);
    });

    var chainPending = false;
    if (chain) {
      if (chain.getBoundingClientRect().top < vh * 0.88) startChain(chain, false);
      else chainPending = true;
    }

    document.documentElement.classList.add('sol-anim');
    if (!pending.length && !chainPending) return;

    try {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          io.unobserve(entry.target);
          if (entry.target === chain) startChain(chain, true);
          else entry.target.classList.add('is-in');
        });
      }, { rootMargin: '0px 0px -12% 0px' });
      pending.forEach(function (el) { io.observe(el); });
      if (chainPending) io.observe(chain);
    } catch (e) {
      document.documentElement.classList.remove('sol-anim');
    }
  }

  /* Startet den Ketten-Aufbau (CSS übernimmt die Stagger-Delays) und
     zählt den CPL-Wert am CRM-Ende von Canon-vorher auf Canon-nachher
     herunter. SSR-Text ist der Endzustand und wird am Schluss exakt
     wiederhergestellt. */
  function startChain(chain, viaScroll) {
    chain.classList.add('is-live');
    var from = parseInt(chain.getAttribute('data-count-from'), 10);
    var to = parseInt(chain.getAttribute('data-count-to'), 10);
    var nodes = chain.querySelectorAll('.sol-chain-count');
    if (!nodes.length || !isFinite(from) || !isFinite(to) || from <= to) return;
    if (typeof window.requestAnimationFrame !== 'function') return;

    var originals = [];
    var i;
    for (i = 0; i < nodes.length; i++) originals.push(nodes[i].textContent);
    for (i = 0; i < nodes.length; i++) nodes[i].textContent = from + ' €';

    var duration = 1200;
    var t0 = null;
    function tick(now) {
      if (t0 === null) t0 = now;
      var p = Math.min(1, (now - t0) / duration);
      var eased = 1 - Math.pow(1 - p, 3);
      var j;
      if (p < 1) {
        var val = Math.round(from + (to - from) * eased);
        for (j = 0; j < nodes.length; j++) nodes[j].textContent = val + ' €';
        window.requestAnimationFrame(tick);
      } else {
        for (j = 0; j < nodes.length; j++) nodes[j].textContent = originals[j];
      }
    }
    window.setTimeout(function () { window.requestAnimationFrame(tick); }, viaScroll ? 1500 : 300);
  }

  function setupFaq() {
    var items = document.querySelectorAll(ROOT_SELECTOR + ' .sol-faq-item');
    if (!items.length) return;
    items.forEach(function (item) {
      var btn = item.querySelector('.sol-faq-q');
      var ans = item.querySelector('.sol-faq-a');
      if (!btn || !ans) return;
      var id = 'sol-faq-a-' + Math.random().toString(36).slice(2, 8);
      ans.id = id;
      btn.setAttribute('aria-controls', id);
      btn.setAttribute('aria-expanded', item.classList.contains('is-open') ? 'true' : 'false');
      btn.addEventListener('click', function () {
        var isOpen = item.classList.contains('is-open');
        items.forEach(function (other) {
          other.classList.remove('is-open');
          var ob = other.querySelector('.sol-faq-q');
          if (ob) ob.setAttribute('aria-expanded', 'false');
        });
        if (!isOpen) {
          item.classList.add('is-open');
          btn.setAttribute('aria-expanded', 'true');
        }
      });
    });
  }

  /* Sticky Mobile-CTA · erscheint ab 20 % Scrolltiefe der Seite,
     verschwindet wieder im obersten Viewport-Bereich oder wenn der
     #marktcheck-Anker im Viewport ist (sonst doppeltes CTA). */
  function setupStickyCta() {
    var sticky = document.querySelector(ROOT_SELECTOR + ' .sol-sticky-cta');
    if (!sticky) return;
    var marktcheck = document.querySelector('#marktcheck');
    var fired = false;
    var THRESHOLD = 0.20;

    function update() {
      var docH = Math.max(
        document.body.scrollHeight,
        document.documentElement.scrollHeight,
        document.body.offsetHeight,
        document.documentElement.offsetHeight
      );
      var winH = window.innerHeight || document.documentElement.clientHeight || 0;
      var scrollable = Math.max(1, docH - winH);
      var depth = (window.scrollY || window.pageYOffset || 0) / scrollable;

      var intakeVisible = false;
      if (marktcheck) {
        var r = marktcheck.getBoundingClientRect();
        intakeVisible = r.top < winH * 0.6 && r.bottom > winH * 0.4;
      }

      if (depth >= THRESHOLD && !intakeVisible) {
        if (!sticky.classList.contains('is-visible')) {
          sticky.classList.add('is-visible');
          if (!fired) {
            fired = true;
            try { track('sticky_cta_visible', { depth: Math.round(depth * 100) }); } catch (e) {}
          }
        }
      } else {
        sticky.classList.remove('is-visible');
      }
    }

    update();
    window.addEventListener('scroll',  update, { passive: true });
    window.addEventListener('resize',  update, { passive: true });
  }

  function setupScrollAnchor() {
    document.querySelectorAll(ROOT_SELECTOR + ' a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var hash = a.getAttribute('href');
        if (!hash || hash.length < 2) return;
        var target = document.querySelector(hash);
        if (!target) return;
        e.preventDefault();
        var top = target.getBoundingClientRect().top + window.scrollY - 20;
        window.scrollTo({ top: top, behavior: 'smooth' });
      });
    });
  }

  function setupQuiz() {
    var mount = document.querySelector(ROOT_SELECTOR + ' [data-sol-quiz]');
    if (!mount) return;
    try {
      QuizController(mount);
      track('system_intake_view');
    } catch (e) {
      // Intake failed to render — leave the SSR fallback in place so user can still see card & link.
      // (Mount is unchanged because we never cleared it.)
      if (window && window.console) { window.console.warn('Marktcheck render failed:', e); }
    }
  }

  /* CAPEX vs OPEX · Timeframe-Picker.
     Tauscht alle [data-sol-capex-out="key"] gegen die passende Zahl
     für 12/24/36 Monate. Suffixe (" / Monat", " Stück") bleiben aus
     dem statischen Markup erhalten. */
  var CAPEX_DATA = {
    12: {
      portal_monthly: '~ 1.080 €',
      portal_leads:   '~ 160',
      portal_total:   '13.000 €',
      portal_total2:  '13.000 €',
      own_setup:      '12.000 – 18.000 €',
      own_monthly:    '~ 50 €',
      own_total:      '12.600 – 18.600 €',
      own_total2:     '12.600 – 18.600 €'
    },
    24: {
      portal_monthly: '~ 1.080 €',
      portal_leads:   '~ 320',
      portal_total:   '26.000 €',
      portal_total2:  '26.000 €',
      own_setup:      '12.000 – 18.000 €',
      own_monthly:    '~ 50 €',
      own_total:      '13.200 – 19.200 €',
      own_total2:     '13.200 – 19.200 €'
    },
    36: {
      portal_monthly: '~ 1.080 €',
      portal_leads:   '~ 480',
      portal_total:   '39.000 €',
      portal_total2:  '39.000 €',
      own_setup:      '12.000 – 18.000 €',
      own_monthly:    '~ 50 €',
      own_total:      '14.160 – 20.160 €',
      own_total2:     '14.160 – 20.160 €'
    }
  };
  var CAPEX_SUFFIX = {
    portal_monthly: ' / Monat',
    portal_leads:   ' Stück',
    own_monthly:    ' / Monat'
  };

  function setupCapexPicker() {
    var section = document.querySelector(ROOT_SELECTOR + ' [data-sol-capex-buttons]');
    if (!section) return;
    var btns = section.querySelectorAll('[data-sol-capex-tf]');
    if (!btns.length) return;

    function apply(tf) {
      var data = CAPEX_DATA[tf];
      if (!data) return;
      btns.forEach(function (b) {
        var on = String(b.getAttribute('data-sol-capex-tf')) === String(tf);
        b.classList.toggle('is-active', on);
        b.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      var outs = document.querySelectorAll(ROOT_SELECTOR + ' [data-sol-capex-out]');
      outs.forEach(function (el) {
        var key = el.getAttribute('data-sol-capex-out');
        if (key === 'tf' || key === 'tf2' || key === 'tf3' || key === 'tf4') {
          el.textContent = String(tf);
          return;
        }
        if (Object.prototype.hasOwnProperty.call(data, key)) {
          el.textContent = data[key] + (CAPEX_SUFFIX[key] || '');
        }
      });
    }

    btns.forEach(function (b) {
      b.addEventListener('click', function () {
        var tf = b.getAttribute('data-sol-capex-tf');
        apply(tf);
        try { track('capex_timeframe_change', { tf: tf }); } catch (e) {}
      });
    });
  }

  /* Setzt --sol-nav-top auf die Höhe eines sticky Blocksy-Headers,
     damit unsere In-Page-Section-Nav darunter andockt. Stiller no-op,
     wenn kein sticky Header existiert. */
  function setupSectionNavOffset() {
    var landing = document.querySelector(ROOT_SELECTOR);
    if (!landing) return;
    var header = document.querySelector('header.ct-header, [data-header*="sticky"], #header, .ct-sticky-container');
    function apply() {
      var h = 0;
      if (header) {
        var rect = header.getBoundingClientRect();
        var cs = window.getComputedStyle(header);
        var isSticky = (cs.position === 'sticky' || cs.position === 'fixed') && rect.top <= 1;
        if (isSticky) h = Math.round(rect.height);
      }
      landing.style.setProperty('--sol-nav-top', h + 'px');
    }
    apply();
    window.addEventListener('resize', apply, { passive: true });
    window.addEventListener('scroll', apply, { passive: true });
  }

  ready(function () {
    setupMotion();
    setupCountUp();
    setupFaq();
    setupStickyCta();
    setupScrollAnchor();
    setupQuiz();
    setupCapexPicker();
    setupSectionNavOffset();
  });
})();
