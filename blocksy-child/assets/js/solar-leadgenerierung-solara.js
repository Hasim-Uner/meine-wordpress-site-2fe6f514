/* solar-leadgenerierung-solara.js
   SOLARA Landing — B2B Marktcheck im Hero.
   5-stufige Progressive-Disclosure-Sequenz (vier Fit-Fragen → Befund-Daten).
   Submit → /wp-json/nexus/v1/audit-request (intake_variant=energy_systems).
   Success-Screen mit Cal.com-Direktbuchung + händischer SLA-Antwort.
   Vanilla JS. Keine Dependencies. Touch- & Keyboard-accessible. */
(function () {
  'use strict';

  var ROOT_SELECTOR = '.solara-landing';
  var CFG = (window.NexusMarktcheckConfig && typeof window.NexusMarktcheckConfig === 'object')
    ? window.NexusMarktcheckConfig : {};

  // ── Progressive Disclosure: vier Fit-Fragen + Kontakt ─────────
  // Die Werte entsprechen exakt den serverseitigen Enums. Es werden keine
  // CPL-, Leadvolumen- oder Engpasswerte aus anderen Antworten abgeleitet.
  var QUIZ_STEPS = [
    {
      key: 'solution_focus',
      label: 'Angebot',
      title: 'Welche Lösung verkaufen Sie?',
      hint: 'Damit der Marktcheck Angebotslogik, Zielgebiet und typische Kaufentscheidung passend einordnet.',
      kind: 'pick',
      options: [
        { v: 'photovoltaik',       t: 'Photovoltaik',      s: 'PV-Anlagen, Planung oder Vertrieb.' },
        { v: 'waermepumpen',       t: 'Wärmepumpen',       s: 'Wärmepumpen-Angebote mit regionalem Beratungsbedarf.' },
        { v: 'speicher',           t: 'Speicher',          s: 'Speicherlösungen mit erklärungsbedürftiger Kaufentscheidung.' },
        { v: 'mehrere_loesungen',  t: 'Mehrere Lösungen',  s: 'PV, Wärmepumpe, Speicher oder ergänzende Leistungen.' },
        { v: 'sonstiges',          t: 'Sonstiges',         s: 'Angrenzende Energielösungen oder ein hybrides Angebot.' }
      ]
    },
    {
      key: 'business_fit',
      label: 'Projektgeschäft',
      title: 'Welche Projekte bilden Ihr Kerngeschäft?',
      hint: 'Projektwert und Verkaufsmodell entscheiden, ob ein eigenes Anfragesystem wirtschaftlich passt.',
      kind: 'pick',
      options: [
        { v: 'b2c_high_value_own_sales', t: 'B2C ab ca. 15.000 € mit eigenem Vertrieb', s: 'Privatkundenprojekte mit eigenem Verkaufsprozess.' },
        { v: 'b2b_high_value_own_sales', t: 'B2B ab ca. 50.000 € mit eigenem Vertrieb', s: 'Gewerbeprojekte mit höherem Beratungs- und Abschlusswert.' },
        { v: 'founder_led_regional',     t: 'Klein, aber vertriebsstark',                s: 'Die Geschäftsführung verkauft selbst; Region und Marge stimmen.' },
        { v: 'resale_or_short_term',     t: 'Vermittlung oder kurzfristiger Lead-Bedarf', s: 'Sie suchen eher kurzfristige Kontakte als ein eigenes Anfrage-Asset.' }
      ]
    },
    {
      key: 'sales_team_size',
      label: 'Vertrieb',
      title: 'Wer übernimmt den Verkauf?',
      hint: 'Ein eigener Anfrageweg braucht eine klare Zuständigkeit für Qualifizierung, Rückruf und Abschluss.',
      kind: 'pick',
      options: [
        { v: 'none',           t: 'Die Geschäftsführung verkauft selbst', s: 'Der Inhaber oder die Geschäftsführung übernimmt den Abschluss.' },
        { v: 'one',            t: '1 Person im Vertrieb',                  s: 'Eine zentrale Anlaufstelle mit klarer Verantwortung.' },
        { v: 'two_to_five',    t: '2–5 Personen im Vertrieb',              s: 'Ein Team mit geregelter Übergabe und Nachverfolgung.' },
        { v: 'more_than_five', t: 'Mehr als 5 Personen im Vertrieb',       s: 'Ein strukturierter Vertrieb mit eigener Pipeline-Logik.' },
        { v: 'no_owner',       t: 'Aktuell übernimmt das niemand verbindlich', s: 'Anfragen haben noch keine feste Vertriebsverantwortung.' }
      ]
    },
    {
      key: 'project_timing',
      label: 'Zeitrahmen',
      title: 'Wann soll sich etwas verändern?',
      hint: 'Der Zeitrahmen hilft, zwischen akuter Priorität und erster Orientierung zu unterscheiden.',
      kind: 'pick',
      options: [
        { v: 'sofort',                  t: 'Sofort',                    s: 'Es besteht akuter Handlungsdruck oder laufender Vertriebsbedarf.' },
        { v: 'naechste_4_wochen',       t: 'In den nächsten 4 Wochen',  s: 'Das Thema ist konkret und soll jetzt sauber priorisiert werden.' },
        { v: 'eins_bis_drei_monate',    t: 'In 1–3 Monaten',            s: 'Die Entscheidung steht bevor, braucht aber noch Vorbereitung.' },
        { v: 'erstmal_orientierung',    t: 'Erstmal Orientierung',      s: 'Zuerst soll klar werden, wo heute Nachfrage verloren geht.' }
      ]
    },
    {
      key: 'contact',
      label: 'Marktcheck',
      title: 'Wohin darf der Befund?',
      hint: 'Fünf geschäftliche Angaben — die Firmen-PLZ dient ausschließlich der Regions-Verfügbarkeitsprüfung.',
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
    var adsSource = '';
    var adsKeyword = '';

    if (document.referrer) {
      try {
        var referrer = new URL(document.referrer, window.location.origin);
        referrerUrl = referrer.origin + (referrer.pathname || '/');
      } catch (error) {
        referrerUrl = '';
      }
    }

    try {
      var searchParams = new URLSearchParams(window.location.search || '');
      adsSource = (searchParams.get('utm_source') || '').trim().slice(0, 120);
      adsKeyword = (searchParams.get('utm_term') || searchParams.get('keyword') || '').trim().slice(0, 180);

      // Click IDs bleiben bewusst draußen. Sie liefern nur ein grobes
      // Quellensignal, niemals die rohe personenbeziehbare Kennung.
      if (!adsSource && searchParams.get('gclid')) adsSource = 'google-ads';
      if (!adsSource && searchParams.get('fbclid')) adsSource = 'meta-ads';

      if (typeof window.sessionStorage !== 'undefined') {
        if (adsSource) window.sessionStorage.setItem('nexus_ads_source', adsSource);
        else adsSource = (window.sessionStorage.getItem('nexus_ads_source') || '').slice(0, 120);

        if (adsKeyword) window.sessionStorage.setItem('nexus_ads_keyword', adsKeyword);
        else adsKeyword = (window.sessionStorage.getItem('nexus_ads_keyword') || '').slice(0, 180);
      }
    } catch (error) {
      adsSource = '';
      adsKeyword = '';
    }

    return {
      landing_page_url: landingUrl,
      entry_page_url: landingUrl,
      previous_internal_url: '',
      referrer_url: referrerUrl,
      ads_source: adsSource,
      ads_keyword: adsKeyword
    };
  }

  // ── Quiz controller ──────────────────────────────────────────
  function QuizController(mount) {
    var state = {
      step: 0,
      answers: {},
      touched: {},
      submitting: false,
      submitError: null,
      done: false,
      qualification: null
    };

    var totalSteps = QUIZ_STEPS.length;
    var view;
    var liveRegion;

    function setAnswer(k, v) {
      state.answers[k] = v;
    }

    function announce(message) {
      if (!liveRegion) return;
      liveRegion.textContent = '';
      window.requestAnimationFrame(function () {
        liveRegion.textContent = message;
      });
    }

    function focusCurrentHeading() {
      window.setTimeout(function () {
        var heading = mount.querySelector('#sol-quiz-title');
        if (!heading) return;
        try { heading.focus({ preventScroll: true }); } catch (e) { heading.focus(); }
      }, 0);
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
      if (!a.postal_code || !/^[0-9]{4,5}$/.test(String(a.postal_code).trim())) errs.postal_code = 'Bitte die Firmen-PLZ angeben — vierstellig (AT, CH) oder fünfstellig (DE).';
      if (!a.consent_privacy) errs.consent_privacy = 'Bitte den Datenschutzhinweis bestätigen.';
      return errs;
    }

    function setFieldError(name, message) {
      var input = mount.querySelector('[name="' + name + '"]');
      var error = mount.querySelector('#sol-quiz-' + name.replace(/_/g, '-') + '-error');
      if (!input || !error) return;

      var field = input.closest('.sol-quiz-field, .sol-quiz-consent-wrap');
      var invalid = !!message;
      input.setAttribute('aria-invalid', invalid ? 'true' : 'false');
      if (field) field.classList.toggle('is-err', invalid);
      error.textContent = message || '';
      error.hidden = !invalid;
    }

    function showContactErrors(errors) {
      [ 'company', 'name', 'position', 'email', 'postal_code', 'consent_privacy' ].forEach(function (name) {
        setFieldError(name, errors[name] || '');
      });
    }

    function showSubmitError(message) {
      var error = mount.querySelector('#sol-quiz-submit-error');
      if (!error) return;
      error.textContent = message || '';
      error.hidden = !message;
    }

    function setSubmitting(submitting) {
      state.submitting = submitting;
      var button = mount.querySelector('.sol-quiz-submit');
      if (!button) return;
      button.disabled = submitting;
      button.classList.toggle('is-loading', submitting);
      button.setAttribute('aria-busy', submitting ? 'true' : 'false');
      var label = button.querySelector('.sol-quiz-submit-label');
      if (label) label.textContent = submitting ? 'Wird gesendet …' : 'Fit-Befund anfordern';
    }

    function submit() {
      if (state.submitting) return;
      var errs = validateContact();
      if (Object.keys(errs).length) {
        state.touched = { name: true, company: true, position: true, email: true, postal_code: true, consent_privacy: true };
        showContactErrors(errs);
        showSubmitError('');
        announce('Bitte prüfen Sie die markierten Pflichtfelder.');
        var firstKey = Object.keys(errs)[0];
        setTimeout(function () {
          var input = mount.querySelector('[name="' + firstKey + '"]');
          if (!input) return;
          var wrap = input.closest('.sol-quiz-field, .sol-quiz-consent-wrap') || input;
          if (typeof wrap.scrollIntoView === 'function') {
            var reduced = false;
            try { reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (e) {}
            if (reduced) {
              wrap.scrollIntoView({ behavior: 'auto', block: 'center' });
            } else {
              wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          }
          try { input.focus({ preventScroll: true }); } catch (e) { input.focus(); }
        }, 0);
        return;
      }

      showContactErrors({});
      showSubmitError('');
      setSubmitting(true);
      announce('Der Marktcheck wird gesendet.');

      var endpoint = CFG.restEndpoint || '/wp-json/nexus/v1/audit-request';
      var attribution = getLeadAttributionPayload();
      var payload = {
        contract_version:   CFG.contractVersion || '',
        intake_variant:      'energy_systems',
        audit_type:          'b2b_system_intake',
        solution_focus:      state.answers.solution_focus || '',
        business_fit:        state.answers.business_fit || '',
        sales_team_size:     state.answers.sales_team_size || '',
        project_timing:      state.answers.project_timing || '',
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
        if (r.ok && r.json && r.json.ok && r.json.status === 'ignored') {
          setSubmitting(false);
          showSubmitError('Die Anfrage konnte nicht verifiziert werden. Bitte laden Sie die Seite neu und versuchen Sie es erneut.');
          announce('Die Anfrage konnte nicht verifiziert werden. Es wurde kein Marktcheck angelegt.');
          track('system_intake_submit_ignored', { funnel_stage: 'submit_ignored' });
          return;
        }

        if (r.ok && r.json && r.json.ok) {
          var qualification = (r.json.qualification && typeof r.json.qualification === 'object') ? r.json.qualification : null;
          track('system_intake_submit_success', { funnel_stage: 'lead_captured' });
          if (qualification && qualification.status) {
            track('system_intake_qualification', {
              funnel_stage: qualification.status === 'qualified' ? 'lead_qualified' : 'lead_nurture',
              qualification_status: qualification.status,
              qualification_reason: qualification.reason || ''
            });
          }
          state.submitting = false;
          state.done = true;
          state.submitError = null;
          state.qualification = qualification;
          renderSuccessView();
        } else {
          var msg = (r.json && (r.json.message || r.json.error || r.json.code))
            ? (typeof r.json.message === 'string' ? r.json.message : 'Bitte Eingaben prüfen.')
            : 'Die Anfrage konnte gerade nicht gesendet werden. Bitte erneut versuchen.';
          track('system_intake_submit_error', { funnel_stage: 'submit_error' });
          setSubmitting(false);
          showSubmitError(msg);
          announce(msg);

          var field = r.json && r.json.error_details && r.json.error_details.field;
          if (field && mount.querySelector('[name="' + field + '"]')) {
            setFieldError(field, msg);
            var invalidInput = mount.querySelector('[name="' + field + '"]');
            try { invalidInput.focus({ preventScroll: true }); } catch (e) { invalidInput.focus(); }
          }
        }
      }).catch(function () {
        track('system_intake_submit_error', { funnel_stage: 'submit_network' });
        setSubmitting(false);
        showSubmitError('Netzwerkfehler. Bitte erneut versuchen.');
        announce('Netzwerkfehler. Bitte versuchen Sie es erneut.');
      });
    }

    function goNext() {
      var current = QUIZ_STEPS[state.step];
      if (!current || current.kind !== 'pick' || !state.answers[current.key]) return;
      if (state.step >= totalSteps - 1) return;
      state.step += 1;
      renderStep(true);
      track('system_intake_step_next', { step: state.step + 1 });
    }

    function goBack() {
      if (state.step > 0) {
        state.step = Math.max(0, state.step - 1);
        renderStep(true);
        track('system_intake_step_back', { step: state.step });
      }
    }

    function pick(key, value) {
      setAnswer(key, value);
      track('system_intake_pick', { step: state.step + 1, field: key, value: value });

      mount.querySelectorAll('.sol-quiz-opt').forEach(function (option) {
        var input = option.querySelector('input[type="radio"]');
        option.classList.toggle('is-sel', !!input && input.checked);
      });

      var next = mount.querySelector('.sol-quiz-next');
      if (next) {
        next.disabled = false;
        next.classList.remove('is-dis');
        next.setAttribute('aria-disabled', 'false');
      }
    }

    function reset() {
      state.step = 0;
      state.answers = {};
      state.submitting = false;
      state.submitError = null;
      state.done = false;
      state.qualification = null;
      state.touched = {};
      renderStep(true);
      track('system_intake_reset');
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
      var pct = Math.round(((state.step + 1) / totalSteps) * 100);
      var dotsTrack = el('div', { className: 'sol-quiz-dots', 'aria-hidden': 'true' });
      for (var i = 0; i < totalSteps; i++) {
        var cls = 'sol-quiz-dot';
        if (i === state.step) cls += ' is-active';
        else if (i < state.step) cls += ' is-done';
        dotsTrack.appendChild(el('span', { className: cls }));
      }
      return el('div', { className: 'sol-quiz-progress' }, [
        el('div', { className: 'sol-quiz-progress-meta' }, [
          el('span', null, 'Schritt ' + String(state.step + 1).padStart(2, '0') + ' / ' + String(totalSteps).padStart(2, '0')),
          el('span', null, pct + ' %')
        ]),
        el('div', {
          className: 'sol-quiz-progress-track',
          role: 'progressbar',
          'aria-label': 'Fortschritt im Marktcheck',
          'aria-valuemin': '1',
          'aria-valuemax': String(totalSteps),
          'aria-valuenow': String(state.step + 1),
          'aria-valuetext': 'Schritt ' + (state.step + 1) + ' von ' + totalSteps
        }, [
          el('div', { className: 'sol-quiz-progress-fill', style: 'width:' + pct + '%' }, [
            el('span', { className: 'sol-quiz-progress-shimmer', 'aria-hidden': 'true' })
          ])
        ]),
        dotsTrack
      ]);
    }

    function renderPickStep(step) {
      var picks = el('fieldset', {
        className: 'sol-quiz-options',
        'aria-describedby': 'sol-quiz-hint'
      });
      picks.appendChild(el('legend', { className: 'sol-sr' }, step.title));

      step.options.forEach(function (o, i) {
        var sel = state.answers[step.key] === o.v;
        var id = 'sol-quiz-' + step.key.replace(/_/g, '-') + '-' + i;
        var input = el('input', {
          id: id,
          className: 'sol-quiz-opt-input',
          type: 'radio',
          name: step.key,
          value: o.v,
          required: true,
          checked: sel ? true : null,
          onChange: function (event) {
            if (event.target.checked) pick(step.key, o.v);
          }
        });

        var option = el('label', {
          className: 'sol-quiz-opt' + (sel ? ' is-sel' : ''),
          for: id,
          style: '--sol-opt-delay:' + (i * 60) + 'ms',
          dataset: { trackAction: 'marketcheck_answer_select', trackCategory: 'engagement', trackSection: 'hero_intake', trackIntent: 'intake_step_' + (state.step + 1) }
        }, [
          input,
          el('span', { className: 'sol-quiz-opt-body' }, [
          el('span', { className: 'sol-quiz-opt-t' }, o.t),
          o.s ? el('span', { className: 'sol-quiz-opt-s' }, o.s) : null
          ]),
          el('span', { className: 'sol-quiz-opt-idx', 'aria-hidden': 'true' }, String(i + 1).padStart(2, '0')),
          el('span', { className: 'sol-quiz-opt-arrow', 'aria-hidden': 'true', html: ARROW_SVG })
        ]);
        picks.appendChild(option);
      });
      return picks;
    }

    function renderField(opts) {
      var a = state.answers;
      var v = a[opts.k] || '';
      var id = 'sol-quiz-' + opts.k.replace(/_/g, '-');
      var errorId = id + '-error';
      var wrap = el('div', { className: 'sol-quiz-field' + (opts.full ? ' sol-quiz-field--full' : '') }, [
        el('label', { className: 'sol-quiz-field-lbl', for: id }, [
          opts.t,
          opts.req ? el('span', { className: 'sol-quiz-field-lbl-req', 'aria-hidden': 'true' }, ' *') : null
        ])
      ]);

      var input;
      if (opts.kind === 'select') {
        input = el('select', {
          id: id,
          name: opts.k,
          autocomplete: opts.ac || 'off',
          required: opts.req ? true : null,
          'aria-required': opts.req ? 'true' : null,
          'aria-invalid': 'false',
          'aria-describedby': errorId,
          'aria-errormessage': errorId,
          onChange: function (e) {
            setAnswer(opts.k, e.target.value);
            state.touched[opts.k] = true;
            setFieldError(opts.k, opts.validator ? opts.validator(e.target.value) : '');
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
          id: id,
          type: opts.type || 'text',
          value: v,
          placeholder: opts.ph || '',
          autocomplete: opts.ac || 'off',
          inputmode: opts.im || null,
          maxlength: opts.maxlength || null,
          name: opts.k,
          required: opts.req ? true : null,
          'aria-required': opts.req ? 'true' : null,
          'aria-invalid': 'false',
          'aria-describedby': errorId,
          'aria-errormessage': errorId,
          onInput: function (e) {
            setAnswer(opts.k, e.target.value);
            if (state.touched[opts.k]) setFieldError(opts.k, opts.validator ? opts.validator(e.target.value) : '');
          },
          onBlur: function (e) {
            state.touched[opts.k] = true;
            setFieldError(opts.k, opts.validator ? opts.validator(e.target.value) : '');
          }
        });
      }

      wrap.appendChild(input);
      wrap.appendChild(el('span', {
        id: errorId,
        className: 'sol-quiz-field-err',
        role: 'status',
        'aria-live': 'polite',
        hidden: true
      }));
      return wrap;
    }

    function renderContactForm() {
      var form = el('form', {
        className: 'sol-quiz-form',
        novalidate: 'novalidate',
        dataset: { trackAction: 'marketcheck_contact_form', trackCategory: 'engagement', trackSection: 'hero_intake' },
        onSubmit: function (e) { e.preventDefault(); submit(); }
      });

      var rationale = el('div', { className: 'sol-quiz-rationale', role: 'note' }, [
        el('p', { className: 'sol-quiz-rationale-h' }, 'Daten-Integrität · Letzter Schritt des Marktchecks'),
        el('p', { className: 'sol-quiz-rationale-b' },
          'Sie haben Angebot, Projektgeschäft, Vertrieb und Zeitrahmen eingeordnet. Jetzt brauche ich die Firmen-Eckdaten, um Region und Fit persönlich-händisch zu prüfen und den Befund an den richtigen Entscheider zu senden.')
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
          return /^[0-9]{4,5}$/.test(String(v || '').trim()) ? null : 'Bitte die Firmen-PLZ angeben — vierstellig (AT, CH) oder fünfstellig (DE). Sie steuert die Regions-Verfügbarkeitsprüfung.';
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
      var consentErrorId = 'sol-quiz-consent-privacy-error';
      var consentWrap = el('div', { className: 'sol-quiz-consent-wrap' });
      var consentLbl = el('label', { className: 'sol-quiz-consent', for: 'sol-quiz-consent-privacy' });
      var consentInput = el('input', {
        id: 'sol-quiz-consent-privacy',
        type: 'checkbox',
        name: 'consent_privacy',
        required: true,
        'aria-required': 'true',
        checked: state.answers.consent_privacy ? 'checked' : null,
        'aria-invalid': 'false',
        'aria-describedby': consentErrorId,
        'aria-errormessage': consentErrorId,
        onChange: function (e) {
          setAnswer('consent_privacy', e.target.checked ? 'accepted' : '');
          state.touched.consent_privacy = true;
          setFieldError('consent_privacy', e.target.checked ? '' : 'Bitte den Datenschutzhinweis bestätigen, um den Marktcheck zu starten.');
        }
      });
      consentLbl.appendChild(consentInput);
      var consentText = el('span', { html: 'Ich akzeptiere die <a href="' + (CFG.privacyUrl || '/datenschutz/') + '" target="_blank" rel="noopener">Datenschutzhinweise</a> und möchte zu meiner Anfrage kontaktiert werden.' });
      consentLbl.appendChild(consentText);
      consentWrap.appendChild(consentLbl);
      consentWrap.appendChild(el('span', {
        id: consentErrorId,
        className: 'sol-quiz-field-err',
        role: 'status',
        'aria-live': 'polite',
        hidden: true
      }));
      form.appendChild(consentWrap);

      // Error banner
      form.appendChild(el('div', {
        id: 'sol-quiz-submit-error',
        className: 'sol-quiz-error',
        role: 'alert',
        hidden: true
      }));

      var submitBtn = el('button', {
        type: 'submit',
        className: 'sol-quiz-submit',
        'aria-busy': 'false',
        dataset: {
          trackAction: 'submit_solar_intake',
          trackCategory: 'lead_gen',
          trackSection: 'hero_intake',
          trackFunnelStage: 'lead_capture_submit'
        }
      }, [
        el('span', { className: 'sol-quiz-submit-label' }, 'Fit-Befund anfordern'),
        el('span', { className: 'sol-quiz-submit-arrow', 'aria-hidden': 'true', html: ARROW_SVG })
      ]);
      form.appendChild(submitBtn);

      form.appendChild(el('p', { className: 'sol-quiz-fineprint' }, 'Keine Zahlungsdaten, kein Abo · Regions-Verfügbarkeitsprüfung · Manuelle Erst-Analyse · DSGVO'));

      return form;
    }

    function renderSuccess() {
      var first = (state.answers.name || '').trim().split(' ')[0];
      var fallbackHeadline = 'Danke' + (first ? ', ' + first : '') + '.';
      var qualification = state.qualification || { status: 'nurture', reason: 'unverified' };
      var isNurture = qualification.status === 'nurture';
      var headline = qualification.headline || fallbackHeadline;
      var message = qualification.message || 'Ihr Marktcheck ist eingegangen. Ich prüfe Domain, Region und Fit persönlich-händisch und sende den Befund spätestens 2 Werktage nach Eingang an Ihre geschäftliche E-Mail.';
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
        el('h2', { id: 'sol-quiz-title', className: 'sol-quiz-success-h', tabindex: '-1' }, headline)
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

    function renderShell() {
      while (mount.firstChild) mount.removeChild(mount.firstChild);
      mount.appendChild(el('div', { className: 'sol-cta-head' }, [
        el('span', { className: 'sol-cta-tag sol-mono' }, [
          el('span', { className: 'sol-cta-tag-dot', 'aria-hidden': 'true' }),
          'Marktcheck · Fit geprüft · spätestens 2 Werktage'
        ]),
        el('span', { className: 'sol-cta-head-right sol-mono' }, 'Fit-Check')
      ]));
      liveRegion = el('div', {
        className: 'sol-sr sol-quiz-live',
        role: 'status',
        'aria-live': 'polite',
        'aria-atomic': 'true'
      });
      mount.appendChild(liveRegion);
      view = el('div', { className: 'sol-quiz-view' });
      mount.appendChild(view);
    }

    function renderStep(shouldFocus) {
      while (view.firstChild) view.removeChild(view.firstChild);

      var quiz = el('div', { className: 'sol-quiz' });
      quiz.appendChild(renderProgress());

      var current = QUIZ_STEPS[state.step];
      var body = el('div', { className: 'sol-quiz-body' }, [
        el('div', { className: 'sol-quiz-label' }, current.label),
        el('h2', { id: 'sol-quiz-title', className: 'sol-quiz-title', tabindex: '-1' }, current.title),
        el('p', { id: 'sol-quiz-hint', className: 'sol-quiz-hint' }, current.hint)
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
        disabled: canBack ? null : true,
        'aria-disabled': canBack ? 'false' : 'true',
        dataset: { trackAction: 'marketcheck_step_back', trackCategory: 'engagement', trackSection: 'hero_intake', trackIntent: 'intake_back' },
        onClick: goBack
      }, '← Zurück');
      nav.appendChild(backBtn);

      if (current.kind === 'pick') {
        var canNext = !!state.answers[current.key] && state.step < totalSteps - 1;
        var nextBtn = el('button', {
          type: 'button',
          className: 'sol-quiz-next' + (canNext ? '' : ' is-dis'),
          disabled: canNext ? null : true,
          'aria-disabled': canNext ? 'false' : 'true',
          dataset: { trackAction: 'marketcheck_step_next', trackCategory: 'engagement', trackSection: 'hero_intake', trackIntent: 'intake_next' },
          onClick: goNext
        }, 'Weiter →');
        nav.appendChild(nextBtn);
      } else {
        nav.appendChild(el('span', { 'aria-hidden': 'true' }));
      }

      quiz.appendChild(nav);
      view.appendChild(quiz);

      if (shouldFocus) {
        announce('Schritt ' + (state.step + 1) + ' von ' + totalSteps + ': ' + current.title);
        focusCurrentHeading();
      }
    }

    function renderSuccessView() {
      while (view.firstChild) view.removeChild(view.firstChild);
      view.appendChild(renderSuccess());
      announce('Ihr Marktcheck wurde übermittelt. ' + (state.qualification && state.qualification.headline ? state.qualification.headline : 'Vielen Dank.'));
      focusCurrentHeading();
    }

    renderShell();
    renderStep(false);
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
      if (chain.getBoundingClientRect().top < vh * 0.88) startChain(chain);
      else chainPending = true;
    }

    document.documentElement.classList.add('sol-anim');
    if (!pending.length && !chainPending) return;

    try {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          io.unobserve(entry.target);
          if (entry.target === chain) startChain(chain);
          else entry.target.classList.add('is-in');
        });
      }, { rootMargin: '0px 0px -12% 0px' });
      pending.forEach(function (el) { io.observe(el); });
      if (chainPending) io.observe(chain);
    } catch (e) {
      document.documentElement.classList.remove('sol-anim');
    }
  }

  /* Startet den Ketten-Aufbau; CSS übernimmt die Stagger-Delays. */
  function startChain(chain) {
    chain.classList.add('is-live');
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

    function setVisible(visible) {
      sticky.classList.toggle('is-visible', visible);
      sticky.setAttribute('aria-hidden', visible ? 'false' : 'true');
      if (visible) sticky.removeAttribute('tabindex');
      else sticky.setAttribute('tabindex', '-1');
    }

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

      var shouldShow = window.innerWidth <= 820 && depth >= THRESHOLD && !intakeVisible;
      if (shouldShow) {
        if (!sticky.classList.contains('is-visible')) {
          setVisible(true);
          if (!fired) {
            fired = true;
            try { track('sticky_cta_visible', { depth: Math.round(depth * 100) }); } catch (e) {}
          }
        }
      } else {
        setVisible(false);
      }
    }

    setVisible(false);
    update();
    window.addEventListener('scroll',  update, { passive: true });
    window.addEventListener('resize',  update, { passive: true });
  }

  function setupScrollAnchor() {
    var reduced = false;
    try { reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (e) {}

    function focusTarget(target) {
      var focusNode = target.querySelector('#sol-quiz-title') || target;
      if (!focusNode.hasAttribute('tabindex')) focusNode.setAttribute('tabindex', '-1');
      try { focusNode.focus({ preventScroll: true }); } catch (e) { focusNode.focus(); }
    }

    function activate(hash, updateHistory) {
      if (!hash || hash.length < 2) return;
      var target = document.querySelector(hash);
      if (!target) return;

      if (updateHistory && window.history && typeof window.history.pushState === 'function') {
        if (window.location.hash !== hash) {
          window.history.pushState(null, '', window.location.pathname + window.location.search + hash);
        }
      }

      var scrollMargin = parseFloat(window.getComputedStyle(target).scrollMarginTop || '0') || 0;
      var top = target.getBoundingClientRect().top + window.scrollY - scrollMargin;
      if (reduced) {
        window.scrollTo({ top: top, behavior: 'auto' });
      } else {
        window.scrollTo({ top: top, behavior: 'smooth' });
      }
      focusTarget(target);
    }

    document.querySelectorAll(ROOT_SELECTOR + ' a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var hash = a.getAttribute('href');
        if (!hash || hash.length < 2) return;
        var target = document.querySelector(hash);
        if (!target) return;
        e.preventDefault();
        // Der globale Nexus-Ankerhandler darf denselben Klick nicht ein zweites
        // Mal verarbeiten; dieser Handler pflegt zusätzlich Hash und Fokus.
        e.stopPropagation();
        activate(hash, true);
      });
    });

    if (window.location.hash === '#marktcheck') {
      window.requestAnimationFrame(function () {
        var target = document.querySelector('#marktcheck');
        if (target) focusTarget(target);
      });
    }
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
      portal_total2:  '13.000 €'
    },
    24: {
      portal_monthly: '~ 1.080 €',
      portal_leads:   '~ 320',
      portal_total:   '26.000 €',
      portal_total2:  '26.000 €'
    },
    36: {
      portal_monthly: '~ 1.080 €',
      portal_leads:   '~ 480',
      portal_total:   '39.000 €',
      portal_total2:  '39.000 €'
    }
  };
  var CAPEX_SUFFIX = {
    portal_monthly: ' / Monat',
    portal_leads:   ' Stück',
    own_monthly:    ' / Monat'
  };

  /* Portal-Kosten-Rechner (Sektion 04).
     Zwei Slider × Canon-Zahlen aus den data-Attributen des Mounts.
     Bewusst ohne eigenen Zeitraum-Zustand: der Zeitraum kommt vom
     bestehenden Picker, damit Modellkarten, Fazit und Rechner nie
     auseinanderlaufen können. Ohne JS bleibt der SSR-Stand stehen. */
  var EUR = (function () {
    try {
      var nf = new Intl.NumberFormat('de-DE', { maximumFractionDigits: 0 });
      return function (v) { return nf.format(Math.round(v)) + ' €'; };
    } catch (e) {
      return function (v) { return String(Math.round(v)) + ' €'; };
    }
  }());

  function PortalCalc() {
    var mount = document.querySelector(ROOT_SELECTOR + ' [data-sol-calc]');
    if (!mount) return null;

    var setupLow  = parseInt(mount.getAttribute('data-setup-low'), 10)  || 14900;
    var setupHigh = parseInt(mount.getAttribute('data-setup-high'), 10) || setupLow;
    var hosting   = parseInt(mount.getAttribute('data-hosting'), 10)    || 50;

    var leadsInput = mount.querySelector('[data-sol-calc-input="leads"]');
    var priceInput = mount.querySelector('[data-sol-calc-input="price"]');
    if (!leadsInput || !priceInput) return null;

    function out(key) { return mount.querySelector('[data-sol-calc-out="' + key + '"]'); }

    function render(tf) {
      var months = parseInt(tf, 10) || 24;
      var leads  = parseInt(leadsInput.value, 10) || 0;
      var price  = parseInt(priceInput.value, 10) || 0;

      var portal   = leads * price * months;
      var ownLow   = setupLow + hosting * months;
      var ownHigh  = setupHigh + hosting * months;

      var leadsOut = out('leads');
      var priceOut = out('price');
      var portalOut = out('portal');
      var ownOut = out('own');

      if (leadsOut)  leadsOut.textContent  = String(leads);
      if (priceOut)  priceOut.textContent  = String(price) + ' €';
      if (portalOut) portalOut.textContent = EUR(portal);
      if (ownOut)    ownOut.textContent    = ownLow === ownHigh ? EUR(ownLow) : EUR(ownLow) + ' – ' + EUR(ownHigh);
    }

    return { mount: mount, render: render, leadsInput: leadsInput, priceInput: priceInput };
  }

  function setupCapexPicker() {
    var section = document.querySelector(ROOT_SELECTOR + ' [data-sol-capex-buttons]');
    if (!section) return;
    var btns = section.querySelectorAll('[data-sol-capex-tf]');
    if (!btns.length) return;

    var calc = PortalCalc();
    var activeTf = 24;
    btns.forEach(function (b) {
      if (b.classList.contains('is-active')) {
        activeTf = parseInt(b.getAttribute('data-sol-capex-tf'), 10) || activeTf;
      }
    });

    function apply(tf) {
      var data = CAPEX_DATA[tf];
      if (!data) return;
      activeTf = parseInt(tf, 10) || activeTf;

      if (calc) {
        var setupLow = parseInt(calc.mount.getAttribute('data-setup-low'), 10) || 14900;
        var setupHigh = parseInt(calc.mount.getAttribute('data-setup-high'), 10) || setupLow;
        var hosting = parseInt(calc.mount.getAttribute('data-hosting'), 10) || 50;
        var ownLow = setupLow + hosting * activeTf;
        var ownHigh = setupHigh + hosting * activeTf;
        data = Object.assign({}, data, {
          own_setup: setupLow === setupHigh ? EUR(setupLow) : EUR(setupLow) + ' – ' + EUR(setupHigh),
          own_monthly: '~ ' + EUR(hosting),
          own_total: ownLow === ownHigh ? EUR(ownLow) : EUR(ownLow) + ' – ' + EUR(ownHigh),
          own_total2: ownLow === ownHigh ? EUR(ownLow) : EUR(ownLow) + ' – ' + EUR(ownHigh)
        });
      }
      btns.forEach(function (b) {
        var on = String(b.getAttribute('data-sol-capex-tf')) === String(tf);
        b.classList.toggle('is-active', on);
        b.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      var outs = document.querySelectorAll(ROOT_SELECTOR + ' [data-sol-capex-out]');
      outs.forEach(function (el) {
        var key = el.getAttribute('data-sol-capex-out');
        if (key.indexOf('tf') === 0) {
          el.textContent = String(tf);
          return;
        }
        if (Object.prototype.hasOwnProperty.call(data, key)) {
          el.textContent = data[key] + (CAPEX_SUFFIX[key] || '');
        }
      });
      if (calc) calc.render(activeTf);
    }

    btns.forEach(function (b) {
      b.addEventListener('click', function () {
        var tf = b.getAttribute('data-sol-capex-tf');
        apply(tf);
        try { track('capex_timeframe_change', { tf: tf }); } catch (e) {}
      });
    });

    if (calc) {
      var trackOnce = false;
      [calc.leadsInput, calc.priceInput].forEach(function (input) {
        input.addEventListener('input', function () { calc.render(activeTf); });
        input.addEventListener('change', function () {
          if (trackOnce) return;
          trackOnce = true;
          try { track('capex_calc_used', { tf: activeTf }); } catch (e) {}
        });
      });
      calc.render(activeTf);
    }
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
    setupQuiz();
    setupScrollAnchor();
    setupCapexPicker();
    setupSectionNavOffset();
  });
})();
