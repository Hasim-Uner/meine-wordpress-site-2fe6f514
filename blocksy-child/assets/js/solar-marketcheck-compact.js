/* Solar Marktcheck — compact visible two-step intake. */
(function () {
  'use strict';

  var CFG = window.NexusMarktcheckConfig || {};
  var mount = document.querySelector('.solara-landing #sol-quiz-mount');
  if (!mount) return;

  var answers = {};
  var fitFields = {
    solution_focus: [
      ['photovoltaik', 'Photovoltaik'],
      ['waermepumpen', 'Wärmepumpen'],
      ['speicher', 'Speicher'],
      ['mehrere_loesungen', 'Mehrere Lösungen'],
      ['sonstiges', 'Sonstiges']
    ],
    business_fit: [
      ['b2c_high_value_own_sales', 'B2C ab ca. 15.000 € · eigener Vertrieb'],
      ['b2b_high_value_own_sales', 'B2B ab ca. 50.000 € · eigener Vertrieb'],
      ['founder_led_regional', 'Klein, aber vertriebsstark'],
      ['resale_or_short_term', 'Vermittlung / kurzfristiger Lead-Bedarf']
    ],
    sales_team_size: [
      ['none', 'Geschäftsführung verkauft selbst'],
      ['one', '1 Person im Vertrieb'],
      ['two_to_five', '2–5 Personen im Vertrieb'],
      ['more_than_five', 'Mehr als 5 Personen'],
      ['no_owner', 'Keine feste Vertriebsverantwortung']
    ],
    project_timing: [
      ['sofort', 'Sofort'],
      ['naechste_4_wochen', 'In den nächsten 4 Wochen'],
      ['eins_bis_drei_monate', 'In 1–3 Monaten'],
      ['erstmal_orientierung', 'Erstmal Orientierung']
    ]
  };

  function esc(value) {
    return String(value || '').replace(/[&<>"']/g, function (char) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char];
    });
  }

  function track(action, extra) {
    try {
      if (window.dataLayer && typeof window.dataLayer.push === 'function') {
        window.dataLayer.push(Object.assign({event: action, event_category: 'lead_gen'}, extra || {}));
      }
    } catch (e) {}
  }

  function selectHtml(name, label, helper, options) {
    var html = '<div class="mc2-field" data-field="' + name + '">';
    html += '<label for="mc2-' + name + '">' + label + '</label>';
    html += '<p class="mc2-help">' + helper + '</p>';
    html += '<select id="mc2-' + name + '" name="' + name + '"><option value="">Bitte wählen</option>';
    options.forEach(function (option) {
      html += '<option value="' + esc(option[0]) + '">' + esc(option[1]) + '</option>';
    });
    html += '</select><p class="mc2-error" hidden></p></div>';
    return html;
  }

  function contactInput(name, label, type, placeholder, full, helper) {
    var autocomplete = {
      company: 'organization',
      name: 'name',
      email: 'email',
      postal_code: 'postal-code'
    }[name] || '';
    var inputmode = {
      email: 'email',
      postal_code: 'numeric'
    }[name] || '';
    var attrs = '';

    if (autocomplete) attrs += ' autocomplete="' + autocomplete + '"';
    if (inputmode) attrs += ' inputmode="' + inputmode + '"';
    if ('postal_code' === name) attrs += ' pattern="[0-9]{4,5}" maxlength="5"';

    return '<div class="mc2-field' + (full ? ' mc2-field--full' : '') + '" data-field="' + name + '">' +
      '<label for="mc2-' + name + '">' + label + '</label>' +
      (helper ? '<p class="mc2-help">' + helper + '</p>' : '') +
      '<input id="mc2-' + name + '" name="' + name + '" type="' + type + '" value="' + esc(answers[name] || '') + '" placeholder="' + placeholder + '"' + attrs + '>' +
      '<p class="mc2-error" hidden></p></div>';
  }

  function showFieldError(name, message) {
    var field = mount.querySelector('[data-field="' + name + '"]');
    if (!field) return;
    var control = field.querySelector('input,select');
    var error = field.querySelector('.mc2-error');
    field.classList.toggle('is-error', !!message);
    if (control) control.setAttribute('aria-invalid', message ? 'true' : 'false');
    if (error) {
      error.textContent = message || '';
      error.hidden = !message;
    }
  }

  function animate() {
    var shell = mount.querySelector('.mc2-shell');
    if (!shell || typeof shell.animate !== 'function') return;
    var reduced = false;
    try { reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (e) {}
    if (reduced) return;
    shell.animate([{opacity:.5,transform:'translateY(6px)'},{opacity:1,transform:'translateY(0)'}], {duration:200,easing:'cubic-bezier(.23,1,.32,1)'});
  }

  function renderFit() {
    mount.classList.remove('is-contact-step');
    mount.innerHTML = '<div class="mc2-shell">' +
      '<div class="mc2-topline"><span>Schritt 1 von 2</span><span>Kurz einordnen</span></div>' +
      '<h3 class="mc2-title">Vier Angaben. Dann weiß ich, ob ein eigener Anfrageweg grundsätzlich passt.</h3>' +
      '<p class="mc2-intro">Keine lange Diagnose. Nur die vier Signale, die für die erste Einordnung wirklich zählen.</p>' +
      '<form class="mc2-form" novalidate><div class="mc2-grid">' +
      selectHtml('solution_focus','Was verkaufen Sie?','Angebotslinie',fitFields.solution_focus) +
      selectHtml('business_fit','Welche Projekte?','Projektwert / Vertriebsmodell',fitFields.business_fit) +
      selectHtml('sales_team_size','Wer verkauft?','Vertriebsverantwortung',fitFields.sales_team_size) +
      selectHtml('project_timing','Wann soll sich etwas ändern?','Priorität',fitFields.project_timing) +
      '</div><div class="mc2-actions"><button class="mc2-primary" type="submit">Weiter zu Kontaktdaten <span aria-hidden="true">→</span></button></div>' +
      '<p class="mc2-footnote">Dauert meist unter einer Minute · keine Zahlungsdaten</p></form></div>';

    Object.keys(fitFields).forEach(function (name) {
      var control = mount.querySelector('[name="' + name + '"]');
      if (answers[name]) control.value = answers[name];
      control.addEventListener('change', function () {
        answers[name] = control.value;
        showFieldError(name, '');
        track('system_intake_pick', {step:1, field:name, value:control.value});
      });
    });

    mount.querySelector('form').addEventListener('submit', function (event) {
      event.preventDefault();
      var invalid = [];
      Object.keys(fitFields).forEach(function (name) {
        if (!answers[name]) invalid.push(name);
        showFieldError(name, answers[name] ? '' : 'Bitte auswählen.');
      });
      if (invalid.length) {
        track('system_intake_validation_error', {step:1, error_count:invalid.length});
        mount.querySelector('[name="' + invalid[0] + '"]').focus();
        return;
      }
      track('system_intake_step_next', {step:2});
      renderContact();
      animate();
    });

    track('system_intake_step_view', {step:1, variant:'compact_2_step'});
  }

  function renderContact() {
    mount.classList.add('is-contact-step');
    mount.innerHTML = '<div class="mc2-shell">' +
      '<div class="mc2-topline"><span>Schritt 2 von 2</span><span>Kontaktdaten</span></div>' +
      '<h3 class="mc2-title">Wohin darf der Befund?</h3>' +
      '<p class="mc2-intro">Ich brauche die Angaben für die regionale Prüfung und Ihre Rückmeldung. Kein Pflichttermin, kein Newsletter.</p>' +
      '<form class="mc2-form" novalidate><div class="mc2-grid">' +
      contactInput('company','Unternehmen','text','Mustermann Solar GmbH',true,'Für die Einordnung des Betriebs') +
      contactInput('name','Ansprechpartner','text','Max Mustermann',false,'Wer den Befund erhält') +
      '<div class="mc2-field" data-field="position"><label for="mc2-position">Position</label><p class="mc2-help">Rolle im Unternehmen</p><select id="mc2-position" name="position"><option value="">Bitte wählen</option><option>Geschäftsführung / Inhaber</option><option>Vertriebsleitung</option><option>Marketing / Web-Verantwortung</option><option>Andere</option></select><p class="mc2-error" hidden></p></div>' +
      contactInput('email','Geschäftliche E-Mail','email','max@solar-betrieb.de',false,'Firmen-Domain, keine Freemail-Adresse') +
      contactInput('postal_code','Firmen-PLZ','text','30159',false,'Für die regionale Wettbewerbsprüfung') +
      '</div><div class="mc2-consent" data-field="consent_privacy"><label><input type="checkbox" name="consent_privacy"> <span>Ich akzeptiere die <a href="' + esc(CFG.privacyUrl || '/datenschutz/') + '" target="_blank" rel="noopener">Datenschutzhinweise</a> und möchte zu meiner Anfrage kontaktiert werden.</span></label><p class="mc2-error" hidden></p></div>' +
      '<div class="mc2-submit-error" hidden></div>' +
      '<div class="mc2-actions"><button class="mc2-back" type="button">← Zurück</button><button class="mc2-primary" type="submit">Marktcheck anfordern <span aria-hidden="true">→</span></button></div>' +
      '<p class="mc2-footnote">Kein Pflichttermin · kein Newsletter · DSGVO</p></form></div>';

    if (answers.position) mount.querySelector('[name="position"]').value = answers.position;
    ['company','name','position','email','postal_code'].forEach(function (name) {
      var control = mount.querySelector('[name="' + name + '"]');
      control.addEventListener('input', function () { answers[name] = control.value; showFieldError(name,''); });
      control.addEventListener('change', function () { answers[name] = control.value; showFieldError(name,''); });
    });
    mount.querySelector('[name="consent_privacy"]').addEventListener('change', function (event) {
      answers.consent_privacy = event.target.checked ? 'accepted' : '';
      showFieldError('consent_privacy','');
    });
    mount.querySelector('.mc2-back').addEventListener('click', function () {
      track('system_intake_step_back', {step:1});
      renderFit();
      animate();
    });
    mount.querySelector('form').addEventListener('submit', submit);
    track('system_intake_step_view', {step:2, variant:'compact_2_step'});
  }

  function isFreemail(value) {
    var at = String(value || '').lastIndexOf('@');
    if (at < 0) return false;
    var domain = String(value || '').slice(at + 1).trim().toLowerCase();
    return /^(?:gmail|gmx|web|t-online|outlook|hotmail|yahoo|icloud|aol|live|mail|googlemail)\.(?:com|de|net|at|ch)$/i.test(domain);
  }

  function validateContact() {
    var errors = {};
    if (!answers.company || answers.company.trim().length < 2) errors.company = 'Bitte Unternehmen angeben.';
    if (!answers.name || answers.name.trim().length < 2) errors.name = 'Bitte Ansprechpartner angeben.';
    if (!answers.position) errors.position = 'Bitte Position auswählen.';
    if (!answers.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(answers.email)) {
      errors.email = 'Bitte gültige E-Mail angeben.';
    } else if (isFreemail(answers.email)) {
      errors.email = 'Bitte Ihre geschäftliche E-Mail-Adresse mit Firmen-Domain verwenden.';
    }
    if (!answers.postal_code || !/^[0-9]{4,5}$/.test(answers.postal_code.trim())) errors.postal_code = 'Bitte Firmen-PLZ angeben.';
    if (!answers.consent_privacy) errors.consent_privacy = 'Bitte Datenschutzhinweis bestätigen.';
    return errors;
  }

  function attribution() {
    if (window.NexusCore && typeof window.NexusCore.getLeadAttributionPayload === 'function') return window.NexusCore.getLeadAttributionPayload();
    return {landing_page_url:window.location.href,entry_page_url:window.location.href,previous_internal_url:'',referrer_url:document.referrer || '',ads_source:'',ads_keyword:''};
  }

  function submit(event) {
    event.preventDefault();
    var form = event.currentTarget;
    ['company','name','position','email','postal_code'].forEach(function (name) { answers[name] = form.elements[name].value; });
    answers.consent_privacy = form.elements.consent_privacy.checked ? 'accepted' : '';
    var errors = validateContact();
    ['company','name','position','email','postal_code','consent_privacy'].forEach(function (name) { showFieldError(name, errors[name] || ''); });
    if (Object.keys(errors).length) {
      track('system_intake_validation_error', {step:2, error_count:Object.keys(errors).length});
      form.elements[Object.keys(errors)[0]].focus();
      return;
    }

    var button = form.querySelector('.mc2-primary[type="submit"]');
    var errorBox = form.querySelector('.mc2-submit-error');
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.textContent = 'Wird gesendet …';
    errorBox.hidden = true;

    var payload = Object.assign({
      contract_version: CFG.contractVersion || '',
      intake_variant: 'energy_systems',
      audit_type: 'b2b_system_intake',
      solution_focus: answers.solution_focus,
      business_fit: answers.business_fit,
      sales_team_size: answers.sales_team_size,
      project_timing: answers.project_timing,
      name: answers.name,
      company: answers.company,
      position: answers.position,
      email: answers.email,
      phone: '',
      postal_code: answers.postal_code.trim(),
      page_url: CFG.pageUrl || window.location.href,
      consent_privacy: 'accepted',
      company_website: ''
    }, attribution());

    track('system_intake_submit_attempt', {step:2});

    fetch(CFG.restEndpoint || '/wp-json/nexus/v1/audit-request', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify(payload)
    }).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (json) { return {ok:response.ok,json:json}; });
    }).then(function (result) {
      if (!result.ok || !result.json || !result.json.ok) {
        var error = new Error((result.json && result.json.message) || 'Senden fehlgeschlagen.');
        error.field = result.json && result.json.error_details ? result.json.error_details.field || '' : '';
        throw error;
      }
      track('system_intake_submit_success', {
        funnel_stage:'lead_captured',
        qualification_status:result.json.qualification && result.json.qualification.status ? result.json.qualification.status : ''
      });
      renderSuccess(result.json.qualification || {});
      animate();
    }).catch(function (error) {
      button.disabled = false;
      button.removeAttribute('aria-busy');
      button.innerHTML = 'Marktcheck anfordern <span aria-hidden="true">→</span>';

      if (error.field && mount.querySelector('[data-field="' + error.field + '"]')) {
        showFieldError(error.field, error.message || 'Bitte prüfen.');
        var fieldControl = mount.querySelector('[data-field="' + error.field + '"] input, [data-field="' + error.field + '"] select');
        if (fieldControl) fieldControl.focus();
      } else {
        errorBox.textContent = error.message || 'Bitte erneut versuchen.';
        errorBox.hidden = false;
      }
      track('system_intake_submit_error', {funnel_stage:'submit_error', error_field:error.field || ''});
    });
  }

  function renderSuccess(qualification) {
    var headline = qualification.headline || 'Danke. Ihr Marktcheck ist eingegangen.';
    var message = qualification.message || 'Ich prüfe Region und Fit persönlich und sende Ihnen spätestens innerhalb von 2 Werktagen eine klare Rückmeldung.';
    var deadline = qualification.response_deadline_human || '';
    var ticket = qualification.ticket_id || '';
    var status = qualification.status || '';
    var caseLabel = 'qualified' === status ? 'Dokumentierten Fall ansehen' : 'Methode im Praxisfall ansehen';
    var html = '<div class="mc2-shell mc2-success">' +
      '<div class="mc2-success-mark">✓</div>' +
      '<p class="mc2-topline"><span>Marktcheck eingegangen</span><span>Persönliche Prüfung</span></p>' +
      '<h3 class="mc2-title">' + esc(headline) + '</h3>' +
      '<p class="mc2-intro">' + esc(message) + '</p>';

    if (deadline) {
      html += '<p class="mc2-deadline"><strong>Rückmeldung spätestens:</strong> ' + esc(deadline) + '</p>';
    }
    if (ticket) {
      html += '<p class="mc2-ticket">Referenz · ' + esc(ticket) + '</p>';
    }

    html += '<div class="mc2-success-actions">' +
      '<a class="mc2-primary mc2-linkbutton" href="' + esc(CFG.caseUrl || '/case-study-solar-leadgenerierung/') + '">' + esc(caseLabel) + ' <span aria-hidden="true">→</span></a>' +
      '<a class="mc2-secondary" href="/">Zur Startseite</a>' +
      '</div><p class="mc2-footnote">Bestätigung an ' + esc(answers.email) + '</p></div>';

    mount.innerHTML = html;
  }

  renderFit();
  track('system_intake_compact_view', {step:1,variant:'compact_2_step'});
})();
