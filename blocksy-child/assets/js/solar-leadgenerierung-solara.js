/* solar-leadgenerierung-solara.js
   SOLARA Landing — interaktives 5-Step "Marktcheck"-Quiz im Hero.
   Submit → /wp-json/nexus/v1/audit-request (intake_variant=energy_systems).
   Success-Screen mit Cal.com-Direktbuchung + 24h-Antwort-Pfad.
   Vanilla JS. Keine Dependencies. Touch- & Keyboard-accessible. */
(function () {
  'use strict';

  var ROOT_SELECTOR = '.solara-landing';
  var CFG = (window.NexusMarktcheckConfig && typeof window.NexusMarktcheckConfig === 'object')
    ? window.NexusMarktcheckConfig : {};

  var QUIZ_STEPS = [
    {
      key: 'solution_focus',
      label: 'Angebot',
      title: 'Worauf liegt der Schwerpunkt Ihres Angebots?',
      hint: 'Wir kalibrieren die Analyse auf Ihre Vertriebslogik.',
      kind: 'pick',
      options: [
        { v: 'photovoltaik',      t: 'Photovoltaik',      s: 'Aufdach · Speicher · Direktinvest', i: '☀️' },
        { v: 'waermepumpen',      t: 'Wärmepumpen',       s: 'Sanierung & Neubau',                i: '🔥' },
        { v: 'speicher',          t: 'Speicher',          s: 'Stand-alone oder Nachrüstung',      i: '🔋' },
        { v: 'mehrere_loesungen', t: 'Mehrere Lösungen',  s: 'PV · WP · Speicher kombiniert',     i: '⚡' }
      ]
    },
    {
      key: 'lead_volume',
      label: 'Volumen',
      title: 'Wie viele Anfragen kommen aktuell pro Monat rein?',
      hint: 'Qualifiziert + unqualifiziert zusammen — Bauchgefühl reicht.',
      kind: 'pick',
      options: [
        { v: 'unter_20',    t: 'Unter 20',  s: 'Vertrieb hat Kapazität, aber Pipeline ist dünn', i: '📉' },
        { v: '20_bis_50',   t: '20 – 50',   s: 'Volumen vorhanden, Qualität unklar',             i: '📊' },
        { v: '51_bis_120',  t: '51 – 120',  s: 'Stabiler Korridor — Hebel bei Qualität',         i: '📈' },
        { v: 'ueber_120',   t: 'Über 120',  s: 'Skalierung greift nur bei sauberem Fundament',   i: '🚀' }
      ]
    },
    {
      key: 'primary_bottleneck',
      label: 'Engpass',
      title: 'Wo ist Ihr größter Engpass — ehrlich?',
      hint: 'Einer trifft fast immer am stärksten zu.',
      kind: 'pick',
      options: [
        { v: 'lead_menge',             t: 'Lead-Menge',             s: 'Zu wenige Anfragen für die Kapazität',                   i: '🪫' },
        { v: 'lead_qualitaet',         t: 'Lead-Qualität',          s: 'Anfragen kommen, aber zu viel Streuverlust',             i: '🎯' },
        { v: 'tracking_klarheit',      t: 'Tracking-Klarheit',      s: 'Welcher Kanal bringt Abschlüsse? Unklar.',               i: '🔍' },
        { v: 'eigentum_abhaengigkeit', t: 'Eigentum / Abhängigkeit', s: 'Sie mieten — Portal oder Agentur hält den Hebel',       i: '🔓' }
      ]
    },
    {
      key: 'cpl_range',
      label: 'CPL',
      title: 'Was kostet eine Anfrage heute im Schnitt?',
      hint: 'Voll belastet (Portal-Gebühren + Ads + Folgekosten).',
      kind: 'pick',
      options: [
        { v: 'unter_80',    t: 'Unter 80 €',  s: 'Organisch / Empfehlung-getrieben',          i: '💰' },
        { v: '80_bis_150',  t: '80 – 150 €',  s: 'Typischer Portal- / Performance-Korridor', i: '💸' },
        { v: '151_bis_300', t: '151 – 300 €', s: 'Spürbar hoher CPL — Streuverlust hoch',     i: '⚠️' },
        { v: 'ueber_300',   t: 'Über 300 €',  s: 'Akut unwirtschaftlich',                     i: '🔥' }
      ]
    },
    {
      key: 'contact',
      label: 'Kontakt',
      title: 'Wohin senden wir Ihre Ersteinschätzung?',
      hint: 'Persönliche Antwort innerhalb von 24 h — keine Newsletter, keine Pitch-Mails.',
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
        window.dataLayer.push(Object.assign({ event: action, event_category: 'lead_funnel' }, extra || {}));
      }
    } catch (e) { /* swallow */ }
  }

  // ── Quiz controller ──────────────────────────────────────────
  function QuizController(mount) {
    var state = {
      step: 0,
      answers: {},
      submitting: false,
      submitError: null,
      done: false
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
      if (!a.name || a.name.trim().length < 2) errs.name = 'Bitte Ihren Namen angeben.';
      if (!a.company || a.company.trim().length < 2) errs.company = 'Bitte Ihr Unternehmen angeben.';
      if (!a.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(a.email)) errs.email = 'Bitte eine gültige E-Mail angeben.';
      if (!a.postal_code || !/^[0-9]{5}$/.test(String(a.postal_code).trim())) errs.postal_code = '5-stellige PLZ nötig.';
      if (!a.consent_privacy) errs.consent_privacy = 'Bitte den Datenschutzhinweis bestätigen.';
      return errs;
    }

    function submit() {
      if (state.submitting) return;
      var errs = validateContact();
      if (Object.keys(errs).length) {
        state.touched = Object.assign({}, state.touched || {}, { name: true, company: true, email: true, postal_code: true, consent_privacy: true });
        render();
        return;
      }

      setState({ submitting: true, submitError: null });

      var endpoint = CFG.restEndpoint || '/wp-json/nexus/v1/audit-request';
      var payload = {
        intake_variant:      'energy_systems',
        audit_type:          'growth_audit',
        solution_focus:      state.answers.solution_focus || '',
        lead_volume:         state.answers.lead_volume || '',
        cpl_range:           state.answers.cpl_range || '',
        primary_bottleneck:  state.answers.primary_bottleneck || '',
        postal_code:         (state.answers.postal_code || '').toString().replace(/\D/g, ''),
        name:                state.answers.name || '',
        company:             state.answers.company || '',
        email:                state.answers.email || '',
        phone:                state.answers.phone || '',
        page_url:             CFG.pageUrl || window.location.href,
        consent_privacy:      'accepted',
        company_website:      state.answers.company_website || '',
        landing_page_url:     window.location.pathname || '',
        entry_page_url:       window.location.pathname || '',
        referrer_url:         document.referrer || ''
      };

      fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
      }).then(function (res) {
        return res.json().then(function (json) { return { ok: res.ok, json: json }; }).catch(function () { return { ok: res.ok, json: {} }; });
      }).then(function (r) {
        if (r.ok && r.json && r.json.ok) {
          track('marktcheck_submit_success', { funnel_stage: 'lead_captured' });
          setState({ submitting: false, done: true, submitError: null });
        } else {
          var msg = (r.json && (r.json.message || r.json.error || r.json.code))
            ? (typeof r.json.message === 'string' ? r.json.message : 'Bitte Eingaben prüfen.')
            : 'Die Anfrage konnte gerade nicht gesendet werden. Bitte erneut versuchen.';
          track('marktcheck_submit_error', { funnel_stage: 'submit_error' });
          setState({ submitting: false, submitError: msg });
        }
      }).catch(function () {
        track('marktcheck_submit_error', { funnel_stage: 'submit_network' });
        setState({ submitting: false, submitError: 'Netzwerkfehler. Bitte erneut versuchen.' });
      });
    }

    function goNext() {
      if (state.step < totalSteps - 1) {
        setState({ step: state.step + 1 });
        track('marktcheck_step_next', { step: state.step + 1 });
      }
    }

    function goBack() {
      if (state.step > 0) {
        setState({ step: Math.max(0, state.step - 1) });
        track('marktcheck_step_back', { step: state.step });
      }
    }

    function pick(key, value) {
      setAnswer(key, value);
      track('marktcheck_pick', { step: state.step + 1, field: key, value: value });
      // auto-advance
      setTimeout(function () {
        if (state.step < totalSteps - 1) {
          setState({ step: state.step + 1 });
        } else {
          render();
        }
      }, 240);
    }

    function reset() {
      state.step = 0;
      state.answers = {};
      state.submitting = false;
      state.submitError = null;
      state.done = false;
      state.touched = {};
      render();
      track('marktcheck_reset');
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
          tabindex: i < state.step ? '0' : '-1',
          'aria-label': 'Zu Schritt ' + (i + 1),
          onClick: (function (idx) {
            return function () {
              if (idx < state.step) {
                state.step = idx;
                render();
                track('marktcheck_dot_jump', { to_step: idx + 1 });
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
          dataset: { trackAction: 'marktcheck_pick', trackCategory: 'lead_funnel', trackSection: 'quiz_step_' + (state.step + 1) },
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
      var input = el('input', {
        type: opts.type || 'text',
        value: v,
        placeholder: opts.ph || '',
        autocomplete: opts.ac || 'off',
        inputmode: opts.im || null,
        name: opts.k,
        onInput: function (e) { setAnswer(opts.k, e.target.value); },
        onBlur: function () {
          var touched = Object.assign({}, state.touched || {});
          touched[opts.k] = true;
          state.touched = touched;
          render();
        }
      });
      wrap.appendChild(input);
      if (errMsg) wrap.appendChild(el('span', { className: 'sol-quiz-field-err' }, errMsg));
      return wrap;
    }

    function renderContactForm() {
      var form = el('form', {
        className: 'sol-quiz-form',
        novalidate: 'novalidate',
        onSubmit: function (e) { e.preventDefault(); submit(); }
      });

      form.appendChild(renderField({ k: 'company', t: 'Firma', type: 'text', ph: 'Mustermann Solar GmbH', req: true, ac: 'organization', full: true,
        validator: function (v) { return (!v || v.trim().length < 2) ? 'Bitte Firma angeben.' : null; }
      }));
      form.appendChild(renderField({ k: 'name', t: 'Name', type: 'text', ph: 'Max Mustermann', req: true, ac: 'name',
        validator: function (v) { return (!v || v.trim().length < 2) ? 'Bitte Namen angeben.' : null; }
      }));
      form.appendChild(renderField({ k: 'postal_code', t: 'PLZ', type: 'text', ph: '30853', req: true, ac: 'postal-code', im: 'numeric',
        validator: function (v) { return (!/^[0-9]{5}$/.test(String(v || '').trim())) ? '5-stellige PLZ.' : null; }
      }));
      form.appendChild(renderField({ k: 'email', t: 'E-Mail', type: 'email', ph: 'max@solar-betrieb.de', req: true, ac: 'email', full: true,
        validator: function (v) { return (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v || ''))) ? 'Gültige E-Mail nötig.' : null; }
      }));
      form.appendChild(renderField({ k: 'phone', t: 'Telefon (optional)', type: 'tel', ph: '+49 ___ ____', ac: 'tel', im: 'tel', full: true }));

      // Honeypot
      var hp = el('input', {
        type: 'text', tabindex: '-1', autocomplete: 'off',
        className: 'sol-quiz-hp', 'aria-hidden': 'true',
        name: 'company_website',
        onInput: function (e) { setAnswer('company_website', e.target.value); }
      });
      form.appendChild(hp);

      // Consent
      var consentLbl = el('label', { className: 'sol-quiz-consent' });
      var consentInput = el('input', {
        type: 'checkbox',
        checked: state.answers.consent_privacy ? 'checked' : null,
        onChange: function (e) { setAnswer('consent_privacy', e.target.checked ? 'accepted' : ''); render(); }
      });
      consentLbl.appendChild(consentInput);
      var consentText = el('span', { html: 'Ich akzeptiere die <a href="' + (CFG.privacyUrl || '/datenschutz/') + '" target="_blank" rel="noopener">Datenschutzhinweise</a> und möchte zu meiner Anfrage kontaktiert werden.' });
      consentLbl.appendChild(consentText);
      form.appendChild(consentLbl);

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
          trackAction: 'cta_solar_to_diagnostic_request',
          trackCategory: 'lead_funnel',
          trackSection: 'quiz_submit',
          trackFunnelStage: 'lead_capture_submit'
        }
      }, [
        el('span', null, state.submitting ? 'Wird gesendet …' : 'Marktcheck abschicken'),
        el('span', { className: 'sol-quiz-submit-arrow', 'aria-hidden': 'true', html: ARROW_SVG })
      ]);
      form.appendChild(submitBtn);

      form.appendChild(el('p', { className: 'sol-quiz-fineprint' }, 'Persönliche Antwort · 24 h · DSGVO'));

      return form;
    }

    function renderSuccess() {
      var first = (state.answers.name || '').trim().split(' ')[0];
      var calcom = CFG.calcomUrl || 'https://cal.com/hasim-uener/30min';
      var caseUrl = CFG.caseUrl || '/e3-new-energy/';
      return el('div', { className: 'sol-quiz-success' }, [
        el('div', { className: 'sol-quiz-success-icon', 'aria-hidden': 'true', html: CHECK_SVG }),
        el('h3', { className: 'sol-quiz-success-h' }, 'Danke' + (first ? ', ' + first : '') + '.'),
        el('p', { className: 'sol-quiz-success-sub' },
          'Ihre Antworten sind eingegangen. Sie erhalten innerhalb von 24 h eine persönliche Ersteinschätzung per E-Mail.'),
        el('div', { className: 'sol-quiz-success-row' }, [
          el('a', {
            href: calcom,
            className: 'is-primary',
            target: '_blank',
            rel: 'noopener',
            dataset: {
              trackAction: 'cta_solar_calcom_book',
              trackCategory: 'lead_funnel',
              trackSection: 'quiz_success',
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
              trackAction: 'cta_solar_success_to_e3_case',
              trackCategory: 'proof',
              trackSection: 'quiz_success'
            }
          }, [
            el('span', null, 'E3 Case lesen'),
            el('span', { 'aria-hidden': 'true', html: ARROW_SVG })
          ])
        ]),
        el('p', { className: 'sol-quiz-success-fineprint' }, 'Bestätigung an ' + (state.answers.email || 'Ihre E-Mail')),
        el('button', {
          type: 'button',
          className: 'sol-quiz-back',
          style: 'margin-top:4px;text-decoration:underline;text-underline-offset:3px;',
          onClick: reset
        }, 'Marktcheck neu starten')
      ]);
    }

    function render() {
      // clear
      while (mount.firstChild) mount.removeChild(mount.firstChild);

      // header card
      var head = el('div', { className: 'sol-cta-head' }, [
        el('span', { className: 'sol-cta-tag sol-mono' }, [
          el('span', { className: 'sol-cta-tag-dot', 'aria-hidden': 'true' }),
          'Marktcheck · 60 Sek · 5 Fragen'
        ]),
        el('span', { className: 'sol-cta-head-right sol-mono' }, 'Kostenfrei')
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
        el('h3', { className: 'sol-quiz-title' }, current.title),
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
  function mountSunRays() {
    var hosts = document.querySelectorAll(ROOT_SELECTOR + ' [data-sol-rays]');
    if (!hosts.length) return;
    hosts.forEach(function (host) {
      if (host.dataset.solRaysMounted === '1') return;
      var frag = document.createDocumentFragment();
      for (var i = 0; i < 24; i++) {
        var ray = document.createElement('span');
        ray.className = 'sol-hero-sun-ray';
        ray.style.transform = 'rotate(' + (i * 15) + 'deg)';
        ray.setAttribute('aria-hidden', 'true');
        frag.appendChild(ray);
      }
      host.appendChild(frag);
      host.dataset.solRaysMounted = '1';
    });
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

  function setupStickyCta() {
    var sticky = document.querySelector(ROOT_SELECTOR + ' .sol-sticky-cta');
    var hero   = document.querySelector(ROOT_SELECTOR + ' .sol-hero');
    if (!sticky || !hero) return;
    if (!('IntersectionObserver' in window)) {
      sticky.classList.add('is-visible');
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) sticky.classList.remove('is-visible');
        else if (entry.boundingClientRect.top < 0) sticky.classList.add('is-visible');
      });
    }, { threshold: 0 });
    io.observe(hero);
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
      track('marktcheck_view');
    } catch (e) {
      // Quiz failed to render — leave the SSR fallback in place so user can still see card & link.
      // (Mount is unchanged because we never cleared it.)
      if (window && window.console) { window.console.warn('Marktcheck quiz failed:', e); }
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
    mountSunRays();
    setupFaq();
    setupStickyCta();
    setupScrollAnchor();
    setupQuiz();
    setupCapexPicker();
    setupSectionNavOffset();
  });
})();
