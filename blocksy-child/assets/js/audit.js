/**
 * System-Diagnose page logic.
 *
 * Handles attribution capture, one-step form submission and FAQ schema.
 */
(function () {
  'use strict';

  var config = window.NexusAuditConfig || {};
  var form = null;
  var successBox = null;

  function initAuditPage() {
    captureAdsParams();
    injectFaqSchema();
    initForm();
  }

  function getSessionValue(key) {
    try {
      return sessionStorage.getItem(key) || '';
    } catch (error) {
      return '';
    }
  }

  function setSessionValue(key, value) {
    try {
      sessionStorage.setItem(key, value);
    } catch (error) {
      /* no-op */
    }
  }

  function pushEvent(eventName, extra) {
    if (typeof window.dataLayer === 'undefined' || typeof window.dataLayer.push !== 'function') {
      return;
    }

    var data = { event: eventName };
    if (extra && typeof extra === 'object') {
      Object.keys(extra).forEach(function (key) {
        data[key] = extra[key];
      });
    }

    window.dataLayer.push(data);
  }

  function captureAdsParams() {
    var urlParams = new URLSearchParams(window.location.search);
    var utmSource = urlParams.get('utm_source');
    var utmKeyword = urlParams.get('keyword');

    if (utmSource) {
      setSessionValue('ads_source', utmSource);
    }

    if (utmKeyword) {
      setSessionValue('ads_keyword', utmKeyword);
    }

    var adsSourceField = document.getElementById('ga-ads-source');
    var adsKeywordField = document.getElementById('ga-ads-keyword');

    if (adsSourceField) {
      adsSourceField.value = getSessionValue('ads_source');
    }

    if (adsKeywordField) {
      adsKeywordField.value = getSessionValue('ads_keyword');
    }
  }

  function injectFaqSchema() {
    var faqSection = document.querySelector('.audit-faq-section');
    if (!faqSection) return;

    var details = faqSection.querySelectorAll('details');
    if (!details.length) return;

    var faqEntries = [];
    details.forEach(function (detail) {
      var question = detail.querySelector('summary');
      var answer = detail.querySelector('.faq-ans');

      if (!question || !answer) return;

      faqEntries.push({
        '@type': 'Question',
        name: question.textContent.trim(),
        acceptedAnswer: {
          '@type': 'Answer',
          text: answer.textContent.trim()
        }
      });
    });

    if (!faqEntries.length) return;

    var schema = {
      '@context': 'https://schema.org',
      '@type': 'FAQPage',
      mainEntity: faqEntries
    };

    var script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = JSON.stringify(schema);
    document.head.appendChild(script);
  }

  function initForm() {
    form = document.getElementById('ga-request-form');
    successBox = document.getElementById('ga-request-success');

    if (!form || !config.restEndpoint) {
      return;
    }

    form.addEventListener('submit', handleSubmit);

    ['page_url', 'name', 'email'].forEach(function (fieldName) {
      var field = form.querySelector('[name="' + fieldName + '"]');
      if (!field) return;

      field.addEventListener('blur', function () {
        if (fieldName === 'page_url') {
          normalizeUrlField(field);
        }

        if (!field.value.trim()) return;
        showFieldError(fieldName, validateField(fieldName));
      });

      field.addEventListener('input', function () {
        if (field.classList.contains('is-invalid')) {
          showFieldError(fieldName, validateField(fieldName));
        }
      });
    });

    var checkbox = form.querySelector('[name="consent_privacy"]');
    if (checkbox) {
      checkbox.addEventListener('change', function () {
        if (checkbox.classList.contains('is-invalid')) {
          showFieldError('consent_privacy', validateField('consent_privacy'));
        }
      });
    }
  }

  function normalizeUrlField(field) {
    if (!field) return '';

    var value = field.value.trim();
    if (value && !/^https?:\/\//i.test(value)) {
      value = 'https://' + value;
      field.value = value;
    }

    return value;
  }

  function validateField(name) {
    if (!form) return '';

    if (name === 'page_url') {
      var urlField = form.querySelector('[name="page_url"]');
      var urlValue = normalizeUrlField(urlField);

      if (!urlValue) return 'Bitte die URL der Seite angeben.';
      if (!/^https?:\/\/.+/i.test(urlValue)) return 'Bitte eine gültige URL angeben, z. B. https://example.de.';

      return '';
    }

    if (name === 'name') {
      var nameField = form.querySelector('[name="name"]');
      return nameField && nameField.value.trim() ? '' : 'Bitte Ihren Namen angeben.';
    }

    if (name === 'email') {
      var emailField = form.querySelector('[name="email"]');
      var emailValue = emailField ? emailField.value.trim() : '';

      if (!emailValue) return 'Bitte eine gültige geschäftliche E-Mail-Adresse angeben.';
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) return 'Bitte eine gültige geschäftliche E-Mail-Adresse angeben.';

      return '';
    }

    if (name === 'consent_privacy') {
      var consentField = form.querySelector('[name="consent_privacy"]');
      return consentField && consentField.checked ? '' : 'Bitte bestätigen Sie den Datenschutzhinweis, damit ich Ihre Anfrage bearbeiten darf.';
    }

    return '';
  }

  function showFieldError(name, message) {
    var field = form.querySelector('[name="' + name + '"]');
    if (!field) return;

    var wrapper = field.closest('.ga-form__field');
    var errorEl = wrapper ? wrapper.querySelector('.ga-form__error') : null;

    if (message) {
      field.classList.add('is-invalid');
      if (wrapper) wrapper.classList.add('is-invalid');
      if (errorEl) errorEl.textContent = message;
    } else {
      field.classList.remove('is-invalid');
      if (wrapper) wrapper.classList.remove('is-invalid');
      if (errorEl) errorEl.textContent = '';
    }
  }

  function clearErrors() {
    Array.prototype.forEach.call(form.querySelectorAll('.is-invalid'), function (node) {
      node.classList.remove('is-invalid');
    });

    Array.prototype.forEach.call(form.querySelectorAll('.ga-form__error'), function (node) {
      node.textContent = '';
    });

    clearFeedback();
  }

  function validateForm() {
    var fields = ['page_url', 'name', 'email', 'consent_privacy'];
    var firstInvalid = null;

    for (var i = 0; i < fields.length; i += 1) {
      var fieldName = fields[i];
      var error = validateField(fieldName);
      showFieldError(fieldName, error);

      if (error && !firstInvalid) {
        firstInvalid = form.querySelector('[name="' + fieldName + '"]');
      }
    }

    if (firstInvalid) {
      firstInvalid.focus();
      return false;
    }

    return true;
  }

  function showFeedback(message) {
    var feedback = document.getElementById('ga-form-feedback');
    if (!feedback) return;

    feedback.textContent = message;
    feedback.className = 'ga-form__feedback is-visible is-error';
  }

  function clearFeedback() {
    var feedback = document.getElementById('ga-form-feedback');
    if (!feedback) return;

    feedback.textContent = '';
    feedback.className = 'ga-form__feedback';
  }

  function serializeForm() {
    var formData = new FormData(form);
    var payload = {};
    var attribution = window.NexusCore && typeof window.NexusCore.getLeadAttributionPayload === 'function'
      ? window.NexusCore.getLeadAttributionPayload()
      : {};

    formData.forEach(function (value, key) {
      payload[key] = typeof value === 'string' ? value.trim() : value;
    });

    Object.keys(attribution).forEach(function (key) {
      if (!attribution[key]) return;
      payload[key] = attribution[key];
    });

    return payload;
  }

  function setSubmitting(submitting) {
    var submitButton = form.querySelector('[type="submit"]');
    if (!submitButton) return;

    submitButton.disabled = submitting;
    submitButton.classList.toggle('is-loading', submitting);
  }

  function handleSubmit(event) {
    event.preventDefault();

    clearErrors();

    if (!validateForm()) {
      pushEvent('growth_audit_form_validation_error');
      return;
    }

    setSubmitting(true);

    var payload = serializeForm();
    pushEvent('growth_audit_form_submit_started');

    fetch(config.restEndpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    })
      .then(function (response) {
        return response.text().then(function (text) {
          var data = {};

          try {
            data = text ? JSON.parse(text) : {};
          } catch (error) {
            throw new Error(config.errorMessage || 'Die Anfrage konnte gerade nicht gesendet werden. Bitte versuchen Sie es erneut.');
          }

          if (!response.ok || !data.ok) {
            throw new Error(data.error || config.errorMessage || 'Die Anfrage konnte gerade nicht gesendet werden. Bitte versuchen Sie es erneut.');
          }

          return data;
        });
      })
      .then(function (data) {
        var successMessage = document.getElementById('ga-success-message');

        form.hidden = true;

        if (successMessage) {
          successMessage.textContent = (data && data.message) || config.successMessage || 'Ich prüfe die Seite und melde mich innerhalb von 2 Werktagen per E-Mail.';
        }

        if (successBox) {
          successBox.hidden = false;
          successBox.setAttribute('tabindex', '-1');
          successBox.focus({ preventScroll: true });
        }

        pushEvent('growth_audit_form_submit_success');
      })
      .catch(function (error) {
        var message = error && error.message ? error.message : (config.errorMessage || 'Die Anfrage konnte gerade nicht gesendet werden. Bitte versuchen Sie es erneut.');
        showFeedback(message);
        pushEvent('growth_audit_form_submit_failed', { growth_audit_error: message });
      })
      .finally(function () {
        setSubmitting(false);
      });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAuditPage);
  } else {
    initAuditPage();
  }
})();
