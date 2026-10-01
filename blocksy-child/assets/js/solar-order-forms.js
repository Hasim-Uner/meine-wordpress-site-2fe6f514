(function () {
  'use strict';

  var config = window.NexusMarktcheckConfig || {};
  var forms = document.querySelectorAll('[data-order-form]');
  if (!forms.length || !window.NexusCore || typeof window.NexusCore.submitJson !== 'function') return;

  forms.forEach(function (form) {
    var variant = form.getAttribute('data-order-form');
    var status = form.querySelector('.auftragsformular-status');
    var button = form.querySelector('[type="submit"]');
    var destination = form.elements.namedItem('lead_destination');
    var crmField = form.querySelector('[data-crm-only]');
    var busy = false;

    function count(event) {
      window.dispatchEvent(new CustomEvent('nexus:solar-form', { detail: { event: event, door: variant } }));
    }

    function value(name) {
      var control = form.elements.namedItem(name);
      return control ? control.value.trim() : '';
    }

    function showStatus(message, success) {
      status.textContent = message;
      status.hidden = false;
      status.classList.toggle('ist-erfolg', !!success);
    }

    if (destination && crmField) {
      destination.addEventListener('change', function () {
        var active = destination.value === 'crm';
        crmField.hidden = !active;
        crmField.querySelector('input').required = active;
        if (!active) crmField.querySelector('input').value = '';
      });
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (busy) return;

      var sources = Array.prototype.map.call(form.querySelectorAll('[name="request_sources[]"]:checked'), function (input) {
        return input.value;
      });
      if (!sources.length) {
        count('form_validation_error');
        showStatus('Bitte mindestens eine Anfragequelle auswählen.', false);
        form.querySelector('[name="request_sources[]"]').focus();
        return;
      }
      if (!form.reportValidity()) return;

      var attribution = typeof window.NexusCore.getLeadAttributionPayload === 'function'
        ? window.NexusCore.getLeadAttributionPayload() : {};
      var payload = Object.assign({
        contract_version: config.contractVersion || '',
        intake_variant: variant,
        audit_type: variant === 'sofortkontakt' ? 'sofortkontakt_setup' : 'anfragesystem_analyse',
        name: variant === 'sofortkontakt' ? value('callback_name') : value('company'),
        company: value('company'),
        email: value('email'),
        phone: value('phone'),
        page_url: variant === 'analyse' ? value('page_url') : '',
        request_sources: sources,
        crm_name: value('crm_name'),
        lead_volume: value('lead_volume'),
        lead_destination: value('lead_destination'),
        callback_name: value('callback_name'),
        callback_mobile: value('callback_mobile'),
        desired_start: value('desired_start'),
        analysis_question: value('analysis_question'),
        consent_privacy: form.elements.namedItem('consent_privacy').checked ? 'accepted' : '',
        company_website: value('company_website')
      }, attribution);

      busy = true;
      var unlock = window.NexusCore.lockForm(form);
      button.setAttribute('aria-busy', 'true');
      showStatus('Anfrage wird gesendet …', false);

      window.NexusCore.submitJson(config.restEndpoint || '/wp-json/nexus/v1/audit-request', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
      }).then(function (result) {
        if (!result.ok || !result.data || result.data.ok !== true) {
          if (result.data && result.data.error_details && result.data.error_details.field) count('form_validation_error');
          throw new Error(result.data && result.data.message ? result.data.message : 'Senden fehlgeschlagen. Bitte erneut versuchen.');
        }
        showStatus(result.data.message || ('Angekommen. Ich melde mich ' + (config.replyPromise || 'bald') + '.'), true);
        form.classList.add('ist-gesendet');
        count('form_submitted');
        button.hidden = true;
      }).catch(function (error) {
        showStatus(error.message || 'Senden fehlgeschlagen. Bitte erneut versuchen.', false);
      }).finally(function () {
        unlock();
        button.removeAttribute('aria-busy');
        busy = false;
      });
    });
  });
}());
