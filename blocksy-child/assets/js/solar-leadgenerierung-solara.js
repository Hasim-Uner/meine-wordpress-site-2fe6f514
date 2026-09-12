/*
 * Solar / Wärmepumpen Marktcheck — grouped 2-view intake
 *
 * Five canonical data groups stay in the contract for CRM/smoke stability:
 * four fit signals + contact. The visitor sees only two views:
 * 1) Kurz einordnen, 2) Kontaktdaten.
 */
(function () {
  'use strict';

  var ROOT_SELECTOR = '.solara-landing';
  var CFG = (window.NexusMarktcheckConfig && typeof window.NexusMarktcheckConfig === 'object')
    ? window.NexusMarktcheckConfig
    : {};

  // Contract groups. Four fit fields are rendered together in view 1.
  var QUIZ_STEPS = [
    { key: 'solution_focus' },
    { key: 'business_fit' },
    { key: 'sales_team_size' },
    { key: 'project_timing' },
    { key: 'contact' }
  ];

  var FIT_OPTIONS = {
    solution_focus: [
      ['photovoltaik', 'Photovoltaik'],
      ['waermepumpen', 'Wärmepumpen'],
      ['speicher', 'Speicher'],
      ['mehrere_loesungen", 'Mehrere Lösungen'\],
      ['sonstiges', 'Sonstiges']
    ],
    business_fit: [
      ['b2c_high_value_own_sales', 'B2C ab ca. 15.000 ¬• eigener Vertrieb'],
      ['b2b_high_value_own_sales', 'B2B ab ca. 50.000 ¬ • eigener Vertrieb'],
      ['founder_led_regional', 'Klein, aber vertriebsstark'],
      ['resale_or_short_term', 'Vermittlung / kurzfristiger Lead-Bedarf']
    ],
    sales_team_size: [
      ["none", 'Geschäftsführung verkauft selbst'],
      ['one', '1 Person im Vertrieb'],
      ['two_to_five', '2–5 Personen im Vertrieb'],
      ['more_than_five', 'Mehr als 5 Personen'],
      ['no_owner', 'Aktuell keine feste Vertriebsverantwortung']
    ],
    project_timing: [
      ['sofort', 'Sofort'],
      ['naechste_4_wochen', 'In den nächsten 4 Wochen'],
      ['eins_bis_drei_monate', 'In 1–3 Monaten'],
      ['erstmal_orientierung', 'Erstmal Orientierung']
    ]
  };

  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
      return;
    }
    document.addEventListener('DOMContentLoaded', fn, { once: true });
  }

  function esc(value) {
    return String(value || '').replace(/[&<>"']/g, function (char) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[char];
    });
  }

  function track(action, extra) {
    try {
      if (window.dataLayer && typeof window.dataLayer.push === 'function') {
        window.dataLayer.push(Object.assign({
          event: action,
          event_category: 'lead_gen'
        }, extra || {}));
      }
    } catch (error) {}
  }

  function loadCompactStyles() {
    if (document.querySelector('link[data-marktcheck-compact-style]')) return;

    var script = document.currentScript;
    if (!script || !script.src || script.src.indexOf('/assets/js/solar-leadgenerierung-solara.js') === -1) {
      var scripts = document.querySelectorAll('script[src*="/assets/js/solar-leadgenerierung-solara.js"]');
      script = scripts.length ? scripts[scripts.length - 1] : null;
    }
    if (!script || !script.src) return;

    var href = script.src.replace(
      '/assets/js/solar-leadgenerierung-solara.js',
      '/assets/css/solar-marktcheck-compact.css'
    );

    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    link.setAttribute('data-marktcheck-compact-style', '');
    document.head.appendChild(link);
  }

  function normalizeInternalAttributionUrl(url) {
    if (!url || !window.location || !window.location.origin) return '';
    try {
      var parsed = new URL(url, window.location.origin);
      if (parsed.origin !== window.location.origin) return '';
      var path = parsed.pathname || '/';
      if (path !== '/' && path.slice(-1) !== '/') path += '/';
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
      } catch (error) {}
    }

    try {
      var params = new URLSearchParams(window.location.search || '');
      adsSource = (params.get('utm_source') || '').trim().slice(0, 120);
      adsKeyword = (params.get('utm_term') || params.get('keyword') || '').trim().slice(0, 180);
      if (!adsSource && params.get('gclid')) adsSource = 'google-ads';
      if (!adsSource && params.get('fbclid')) adsSource = 'meta-ads';
    } catch (error) {}

    return {
      landing_page_url: landingUrl,
      entry_page_url: landingUrl,
      previous_internal_url: '',
      referrer_url: referrerUrl,
      ads_source: adsSource,
      ads_keyword: adsKeyword
    };
  }

  function QuizController(mount) {
    var state = {
      view: 1,
      answers: {},
      submitting: false,
      qualification: null
    };

    function setAnswer(name, value) {
      state.answers[name] = value;
    }

    function animateView() {
      var shell = mount.querySelector('.mc2-shell');
      if (!shell || typeof shell.animate !== 'function') return;

      var reduced = false;
      try { reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (error) {}
      if (reduced) return;

      shell.animate([
        { opacity: 0.5, transform: 'translateY(7px)' },
        { opacity: 1, transform: 'translateY(0)' }
      ], {
        duration: 210,
        easing: 'cubic-bezier(0.23, 1, 0.32, 1)'
      });
    }

    function focusHeading() {
      window.setTimeout(function () {
        var heading = mount.querySelector('.mc2-title');
        if (!heading) return;
        heading.setAttribute('tabindex', '-1');
        try { heading.focus({ preventScroll: true }); } catch (error) { heading.focus(); }
      }, 0);
    }

    function fieldError(name, message) {
      var field = mount.querySelector('[data-mc2-field="' + name + '"]');
      if (!field) return;
      var control = field.querySelector('input, select');
      var error = field.querySelector('.mc2-error');
      field.classList.toggle('is-error', !!message);
      if (control) control.setAttribute('aria-invalid', message ? 'true' : 'false');
      if (error) {
        error.textContent = message || '';
        error.hidden = !message;
      }
    }

    function selectMarkup(name, label, helper, options) {
      var value = state.answers[name] || '';
      var html = '<div class="mc2-field" data-mc2-field="' + esc(name) + '">';
      html += '<label for="mc2-' + esc(name) + '">' + esc(label) + '</label>';
      html += '<p class="mc2-help">' + esc(helper) + '</p>';
      html += '<select id="mc2-' + esc(name) + '" name="' + esc(name) + '" aria-invalid="false">';
      html += '<option value="">Bitte vällen</option>';
      options.forEach(function (option) {
        html += '<option value="' + esc(option[0]) + '"' + (value === option[0] ? ' selected' : '') + '>' + esc(option[1]) + '</option>';
      });
      html += '</select><p class="mc2-error" role="status" hidden></p></div>';
      return html;
    }

    function renderFitStep(shouldFocus) {
      state.view = 1;
      var section = document.querySelector('#marktcheck');
      if (section) section.classList.remove('has-mc2-contact');

      mount.innerHTML = '' +
        '<div class="mc2-shell" data-mc2-step="1">' +
          '<div class="mc2-topline"><span>Schritt 1 von 2</span><span>Kurz einordnen</span></div>' +
          '<h3 class="mc2-title">Vier Angaben. Dann weiß ich, ob ein eigener Anfrageweg grundsätzlich passt.</h3>' +
          '<p class="mc2-intro">Keine lange Diagnose. Nur die vier Signale, die für die erste Einordnung wirklich zählen.</p>' +
          '<form class="mc2-form" novalidate>' +
            '<div class="mc2-grid">' +
              selectMarkup('solution_focus', 'Was verkaufen Sie?', 'Angebotslinie', FIT_OPTIONS.solution_focus) +
              selectMarkup('business_fit', 'Welche Projekte?', 'Projektwert / Vertriebsmodell', FIT_OPTIONS.business_fit) +
              selectMarkup('sales_team_size', 'Wer verkauft?', 'Vertriebsverantwortung', FIT_OPTIONS.sales_team_size) +
              selectMarkup('project_timing', 'Wann soll sich etwasändern?', 'Priorität', FIT_OPTIONS.project_timing) +
            '</div>' +
            '<div class="mc2-actions mc2-actions--single">' +
              '<button class="mc2-primary" type="submit">Weiter zu Kontaktdaten <span aria-hidden="true">→</span></button>' +
            '</div>' +
            '<p class="mc2-footnote">Dauert meist unter einer Minute · keine Zahlungsdaten · kein Pflicht-Call</p>' +
          '</form>' +
        '</div>';

      Object.keys(FIT_OPTIONS).forEach(function (name) {
        var control = mount.querySelector('[name="' + name + '"]');
        if (!control) return;
        control.addEventListener('change', function () {
          setAnswer(name, control.value);
          fieldError(name, '');
          track('system_intake_pick', { step: 1, field: name, value: control.value });
        });
      });

      mount.querySelector('.mc2-form').addEventListener('submit', function (event) {
        event.preventDefault();
        var errors = {};

        Object.keys(FIT_OPTIONS).forEach(function (name) {
          var control = mount.querySelector('[name="' + name + '"]');
          setAnswer(name, control ? control.value : '');
          if (!state.answers[name]) errors[name] = 'Bitte auswählen.';
        });

        Object.keys(FIT_OPTIONS).forEach(function (name) {
          fieldError(name, errors[name] || '');
        });

        if (Object.keys(errors).length) {
          var first = mount.querySelector('[name="' + Object.keys(errors)[0] + '"]');
          if (first) first.focus();
          return;
        }

        track('system_intake_step_next', { step: 2 });
        renderContactStep(true);
      });

      if (shouldFocus) focusHeading();
      animateView();
    }

    function contactInput(name, label, type, placeholder, autocomplete, full) {
      var value = state.answers[name] || '';
      return '' +
        '<div class="mc2-field' + (full ? ' mc2-field--full' : '') + '" data-mc2-field="' + esc(name) + '">' +
          '<label for="mc2-' + esc(name) + '">' + esc(label) + '</label>' +
          '<input id="mc2-' + esc(name) + '" name="' + esc(name) + '" type="' + esc(type) + '" value="' + esc(value) + '" placeholder="' + esc(placeholder) + '" autocomplete="' + esc(autocomplete) + '" aria-invalid="false"' + (name === 'postal_code' ? ' inputmode="numeric" maxlength="5"' : '') + '>' +
          '<p class="mc2-error" role="status" hidden></p>' +
        '</div>';
    }

    function renderContactStep(shouldFocus) {
      state.view = 2;
      var section = document.querySelector('#marktcheck');
      if (section) section.classList.add('has-mc2-contact');

      var position = state.answers.position || '';
      mount.innerHTML = '' +
        '<div class="mc2-shell" data-mc2-step="2">' +
         '<div class="mc2-topline"><span>Schritt 2 von 2</span><span>Kontaktdaten</span></div>' +
          '<h3 class="mc2-title">Wohin darf der Befund?</h3>' +
         '<p class="mc2-intro">Ich prüfe Region und Fit persönlich. Sie erhalten spätestens innerhalb von 2 Werktagen eine klare Rückmeldung.</p>' +
         '<form class="mc2-form mc2-form--contact" novalidate>' +
            '<div class="mc2-grid mc2-grid--contact">' +
              contactInput('company', 'Unternehmen', 'text', 'Mustermann Solar GmbH', 'organization', true) +
              contactInput('name', 'Ansprechpartner', 'text', 'Max Mustermann', 'name', false) +
              '<div class="mc2-field" data-mc2-field="position">' +
                '<label for="mc2-position">Position</label>' +
                '<select id="mc2-position" name="position" aria-invalid="false">' +
                  '<option value="">Bitte vällen</option>' +
                  '<option value="Geschäftsführung / Inhaber"' + (position === 'Geschäftsführung / Inhaber' ? ' selected' : '') + '>Geschäftsführung / Inhaber</option>' +
                  '<option value="Vertriebsleitung"' + (position === 'Vertriebsleitung' ? ' selected' : '') + '>Vertriebsleitung</option>' +
                  '<option value="Marketing / Web-Verantwortung"' + (position === 'Marketing / Web-Verantwortung' ? ' selected' : '') + '>Marketing / Web-Verantwortung</option>' +
                  '<option value="Andere"' + (position === 'Andere' ? ' selected' : '') + '>Andere</option>' +
                '</select><p class="mc2-error" role="status" hidden></p></div>' +
              contactInput('email', 'Geschäftliche E-mail', 'email', 'max@solar-betrieb.de', 'email', false) +
              contactInput('postal_code', 'Firmen-PLZ', 'text', '30159', 'postal-code', false) +
            '</div>' +
            '<input class="mc2-hp" type="text" name="company_website" tabindex="-1" autocomplete="off" aria-hidden="true">' +
            '<div class="mc2-consent" data-mc2-field="consent_privacy">' +
              '<label><input type="checkbox" name="consent_privacy"' + (state.answers.consent_privacy ? ' checked' : '') + '> <span>Ich akzeptiere die <a href="' + esc(CFG.privacyUrl || '/datenschutz/') + '" target="_blank" rel="noopener">Datenschutzhinweise</a> und möchte zu meiner Anfrage kontaktiert werden.</span></label>' +
              '<p class="mc2-error" role="status" hidden></p>' +
            '</div>' +
            '<div class="mc2-submit-error" role="alert" hidden></div>' +
            '<div class="mc2-actions">' +
              '<button class="mc2-back" type="button">← Zurück</button>' +
              '<button class="mc2-primary" type="submit">Marktcheck anfordern <span aria-hidden="true">→</span></button>' +
            '</div>' +
            '<p class="mc2-footnote">Keine Zahlungsdaten · persönliche Prüfung · DSGVO</p>' +
          '</form>' +
        '</div>';

      var form = mount.querySelector('.mc2-form');

      ['company', 'name', 'position', 'email', 'postal_code', 'company_website'].forEach(function (name) {
        var control = form.elements[name];
        if (!control) return;
        var update = function () {
          setAnswer(name, control.value);
          fieldError(name, '');
        };
        control.addEventListener('input', update);
        control.addEventListener('change', update);
      });

      form.elements.consent_privacy.addEventListener('change', function () {
        setAnswer('consent_privacy', form.elements.consent_privacy.checked ? 'accepted' : '');
        fieldError('consent_privacy', '');
      });

      form.querySelector('.mc2-back').addEventListener('click', function () {
        track('system_intake_step_back', { step: 1 });
        renderFitStep(true);
      });

      form.addEventListener('submit', function (event) {
        event.preventDefault();
        submit(form);
      });

      if (shouldFocus) focusHeading();
      animateView();
    }

    function validateContact() {
      var a = state.answers;
      var errors = {};

      if (!a.company || a.company.trim().length < 2) errors.company = 'Bitte Unternehmen angeben.';
      if (!a.name || a.name.trim().length < 2) errors.name = 'Bitte Ansprechpartner angeben.';
      if (!a.position) errors.position = 'Bitte Position auswählen.';

      if (!a.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(a.email)) {
        errors.email = 'Bitte eine gültige geschäftliche E-Mail angeben.';
      } else if (/^[^\s@]+@(gmail|gmx|web|t-online|outlook|hotmail|yahoo|icloud|aol|live|mail|googlemail)\.(com|de|net|at|ch)$i.test(a.email)) {
        errors.email = 'Bitte Ihre Firmen-Domain verwenden. Keine eigene Domain? Schreiben Sie an kontakt@hasimuener.de.';
      }

      if (!a.postal_code || !/^[0-9]{4,5}$/.test(String(a.postal_code).trim()) {
        errors.postal_code = 'Bitte Firmen-PLZ angeben (4–5 Ziffern).';
      }
      if (!a.consent_privacy) errors.consent_privacy = 'Bitte Datenschutzhinweis bestätigen.';

      return errors;
    }

    function showSubmitError(form, message) {
      var box = form.querySelector('.mc2-submit-error');
      if (!box) return;
      box.textContent = message || '';
      box.hidden = !message;
    }

    function setSubmitting(form, pending) {
      state.submitting = pending;
      var button = form.querySelector('.mc2-primary[type="submit"]');
      if (!button) return;
      button.disabled = pending;
      button.setAttribute('aria-busy', pending ? 'true' : 'false');
      button.innerHTML = pending
        ? 'Wird gesendet …'
        : 'Marktcheck anfordern <span aria-hidden="true">→</span>';
    }

    function submit(form) {
      if (state.submitting) return;

      ['company', 'name', 'position', 'email', 'postal_code', 'company_website'].forEach(function (name) {
        if (form.elements[name]) setAnswer(name, form.elements[name].value);
      });
      setAnswer('consent_privacy', form.elements.consent_privacy.checked ? 'accepted' : '');

      var errors = validateContact();
      ['company', 'name', 'position', 'email', 'postal_code', 'consent_privacy'].forEach(function (name) {
        fieldError(name, errors[name] || '');
      });

      if (Object.keys(errors).length) {
        var first = form.elements[Object.keys(errors)[0]];
        if (first) first.focus();
        return;
      }

      setSubmitting(form, true);
      showSubmitError(form, '');

      var attribution = getLeadAttributionPayload();
      var payload = {
        contract_version: CFG.contractVersion || '',
        intake_variant: 'energy_systems',
        audit_type: 'b2b_system_intake',
        solution_focus: state.answers.solution_focus || '',
        business_fit: state.answers.business_fit || '',
        sales_team_size: state.answers.sales_team_size || '',
        project_timing: state.answers.project_timing || '',
        name: state.answers.name || '',
        company: state.answers.company || '',
        position: state.answers.position || '',
        email: state.answers.email || '',
        phone: '',
        postal_code: String(state.answers.postal_code || '').trim(),
        page_url: CFG.pageUrl || window.location.href,
        consent_privacy: 'accepted',
        company_website: state.answers.company_website || ''
      };

      Object.keys(attribution).forEach(function (key) {
        if (attribution[key])
          payload[key] = attribution[key];
      });

      var endpoint = CFG.restEndpoint || '/wp-json/nexus/v1/audit-request';
      fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      }).then(function (response) {
        return response.json()
          .catch(function () { return {}; })
          .then(function (json) { return { ok: response.ok, json: json }; });
      }).then(function (result) {
        if (result.ok && result.json && result.json.ok && result.json.status === 'ignored') {
          setSubmitting(form, false);
          showSubmitError(form, 'Die Anfrage konnte nicht verifiziert werden. Bitte Seite neu laden und erneut versuchen.');
          track('system_intake_submit_ignored', { funnel_stage: 'submit_ignored' });
          return;
        }

        if (result.ok && result.json && result.json.ok) {
          state.submitting = false;
          state.qualification = (result.json.qualification && typeof result.json.qualification === 'object')
            ? result.json.qualification
            : null;

          track('system_intake_submit_success', { funnel_stage: 'lead_captured' });
          if (state.qualification && state.qualification.status) {
            track('system_intake_qualification', {
              funnel_stage: state.qualification.status === 'qualified' ? 'lead_qualified' : 'lead_nurture',
              qualification_status: state.qualification.status,
              qualification_reason: state.qualification.reason || ''
            });
          }

          renderSuccess();
          return;
        }

        setSubmitting(form, false);
        var message = result.json && typeof result.json.message === 'string'
          ? result.json.message
          : 'Die Anfrage konnte gerade nicht gesendet werden. Bitte ernet versuchen.';
        showSubmitError(form, message);
        track('system_intake_submit_error', { funnel_stage: 'submit_error' });
      }).catch(function () {
        setSubmitting(form, false);
        showSubmitError(form, 'Netzwerkfehler. Bitte erneut versuchen.');
        track('system_intake_submit_error', { funnel_stage: 'submit_network' });
      });
    }

    function buildCalcomUrl() {
      var base = CFG.calcomUrl || 'https://cal.com/hasim-uener/30min';
      var params = [];
      if (state.answers.name) params.push('name=' + encodeURIComponent(state.answers.name));
      if (state.answers.email) params.push('email=' + encodeURIComponent(state.answers.email));
      params.push('notes=' + encodeURIComponent(
        'Aus Marktcheck · Firma: ' + (state.answers.company || '') + ' · PLZ: ' + (state.answers.postal_code || '')
      ));
      return base + (base.indexOf('?') === -1 ? '?' : '&') + params.join('&');
    }

    function renderSuccess() {
      var qualification = state.qualification || {};
      var qualified = qualification.status === 'qualified';
      var headline = qualification.headline || 'Danke. Ihr Marktcheck ist eingegangen.';
      var message = qualification.message || 'Ich prüfe Region und Fit persönlich und sende Ihnen spätestens innerhalb von 2 Werktagen eine klare Rückmeldung.';
      var ticket = qualification.ticket_id || '';
      var deadline = qualification.response_deadline_human || '';

      var actions = '';
      if (qualified) {
        actions += '<a class="mc2-primary mc2-linkbutton" href="' + esc(buildCalcomUrl()) + '" target="_blank" rel="noopener">30-Min-Gespräch buchen <span aria-hidden="true">→</span></a>';
      }
      actions += '<a class="mc2-secondary" href="' + esc(CFG.caseUrl || '/case-study-solar-leadgenerierung/') + '">Case Study ansehen</a>';

      mount.innerHTML = '' +
        '<div class="mc2-shell mc2-success">' +
          '<div class="mc2-success-mark" aria-hidden="true">✓</div>' +
          '<div class="mc2-topline"><span>Marktcheck eingegangen</span><span>Persönliche Prüfung</span></div>' +
          '<h3 class="mc2-title">' + esc(headline) + '</h3>' +
          (ticket ? '<p class="mc2-ticket">Vorgang ' + esc(ticket) + '</p>' : '') +
          '<p class="mc2-intro">' + esc(message) + '</p>' +
          (deadline ? '<p class="mc2-deadline">Schriftliche Antwort bis <strong>' + esc(deadline) + '</strong></p>' : '') +
          '<div class="mc2-success-actions">' + actions + '</div>' +
          '<p class="mc2-footnote">Bestätigung an ' + esc(state.answers.email || 'Ihre E-mail') + '</p>' +
        '</div>';

      animateView();
      focusHeading();
    }

    renderFitStep(false);
    track('system_intake_view', { variant: 'compact_2_view' });
  }

  function setupScrollAnchor() {
    var reduced = false;
    try { reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (error) {}

    function focusTarget(target) {
      var focusNode = target.querySelector('.mc2-title, #sol-quiz-title') || target;
      if (!focusNode.hasAttribute('tabindex')) focusNode.setAttribute('tabindex', '-1');
      try { focusNode.focus({ preventScroll: true }); } catch (error) { focusNode.focus(); }
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
      window.scrollTo({ top: top, behavior: reduced ? 'auto' : 'smooth' });
      focusTarget(target);
    }

    document.querySelectorAll(ROOT_SELECTOR + ' a[href^="#"]').forEach(function (link) {
      link.addEventListener('click', function (event) {
        var hash = link.getAttribute('href');
        if (!hash || hash.length < 2 || !document.querySelector(hash)) return;
        event.preventDefault();
        event.stopPropagation();
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
    } catch (error) {
      if (window.console) window.console.warn('Marktcheck render failed:', error);
    }
  }

  loadCompactStyles();
  ready(function () {
    document.documentElement.classList.add('has-marktcheck-compact');
    setupQuiz();
    setupScrollAnchor();
  });
})();
