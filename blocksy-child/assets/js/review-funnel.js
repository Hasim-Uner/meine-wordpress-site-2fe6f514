/**
 * Growth-Audit-Funnel
 *
 * Multi-step intake form that stores requests directly in WordPress.
 */
(function () {
  'use strict';

  var config = window.NexusReviewConfig || {};
  var auditLabel = config.auditLabel || 'Marktcheck';
  var submitLabel = config.submitLabel || (auditLabel + ' anfragen');
  var AUTO_ADVANCE_DELAY = 500;
  var FEEDBACK_ID = 'review-form-feedback';
  var state = {
    stepIndex: 0,
    steps: [],
    submitting: false,
    submitted: false,
    lastTrackedStepIndex: null,
    furthestStepIndex: 0,
    autoAdvanceTimer: null
  };

  function prefersReducedMotionEnabled() {
    return Boolean(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  }

  function getScrollBehavior() {
    return prefersReducedMotionEnabled() ? 'auto' : 'smooth';
  }

  function init() {
    var form = document.getElementById('review-request-form');
    if (!form) return;

    state.steps = Array.prototype.slice.call(form.querySelectorAll('.review-step'));
    if (!state.steps.length) return;

    state.furthestStepIndex = 0;
    form.classList.add('review-funnel--js');

    var startedField = form.querySelector('[name="started_at"]');
    if (startedField) {
      startedField.value = String(Date.now());
    }

    var urlParams = new URLSearchParams(window.location.search);
    var utmSource = urlParams.get('utm_source');
    var utmKeyword = urlParams.get('keyword');

    if (utmSource) {
      sessionStorage.setItem('ads_source', utmSource);
    }
    if (utmKeyword) {
      sessionStorage.setItem('ads_keyword', utmKeyword);
    }

    var adsSourceField = form.querySelector('#ads_source');
    var adsKeywordField = form.querySelector('#ads_keyword');

    if (adsSourceField) {
      adsSourceField.value = sessionStorage.getItem('ads_source') || '';
    }
    if (adsKeywordField) {
      adsKeywordField.value = sessionStorage.getItem('ads_keyword') || '';
    }

    Array.prototype.forEach.call(form.querySelectorAll('input[type="url"]'), function (input) {
      input.addEventListener('blur', function () {
        normalizeUrlField(this);
      });
    });

    form.addEventListener('click', handleClick);
    form.addEventListener('submit', handleSubmit);
    form.addEventListener('input', handleFieldChange);
    form.addEventListener('change', handleFieldChange);

    document.addEventListener('visibilitychange', handleAbandon);
    window.addEventListener('pagehide', handleAbandon);

    updateStepUi();
    syncSummary(form);
  }

  function handleAbandon() {
    if (state.submitted || state.submitting) return;
    if (state.furthestStepIndex === 0) {
      var form = getForm();
      var urlField = form && form.querySelector('input[type="url"]');
      if (!urlField || !urlField.value.trim()) return;
    }

    pushDataLayerEvent({
      event: 'review_funnel_abandoned',
      review_step_index: state.stepIndex + 1,
      review_step_name: getStepLabel(state.stepIndex),
      review_furthest_step: state.furthestStepIndex + 1,
      review_furthest_step_name: getStepLabel(state.furthestStepIndex)
    });
  }

  function getForm() {
    return document.getElementById('review-request-form');
  }

  function getStepLabel(index) {
    var form = getForm();
    if (!form) return String(index + 1);

    var labels = form.querySelectorAll('.review-progress-step-label');
    var label = labels[index];

    return label ? label.textContent.trim() : String(index + 1);
  }

  function getDomainFromUrl(url) {
    if (!url) return '';

    try {
      return new URL(url).hostname || '';
    } catch (error) {
      return '';
    }
  }

  function normalizeUrlField(field) {
    if (!field || field.type !== 'url') {
      return;
    }

    var value = field.value.trim();
    if (value && !/^https?:\/\//i.test(value)) {
      field.value = 'https://' + value;
    }
  }

  function pushDataLayerEvent(payload) {
    if (typeof window === 'undefined' || !window.dataLayer || typeof window.dataLayer.push !== 'function') {
      return;
    }

    window.dataLayer.push(payload);
  }

  function trackStepView() {
    if (state.lastTrackedStepIndex === state.stepIndex) {
      return;
    }

    state.lastTrackedStepIndex = state.stepIndex;

    pushDataLayerEvent({
      event: 'review_step_view',
      review_step_index: state.stepIndex + 1,
      review_step_name: getStepLabel(state.stepIndex)
    });
  }

  function trackStepNavigation(direction, fromIndex, toIndex) {
    pushDataLayerEvent({
      event: 'review_step_navigation',
      review_step_direction: direction,
      review_step_from: fromIndex + 1,
      review_step_to: toIndex + 1,
      review_step_name: getStepLabel(toIndex)
    });
  }

  function trackValidationError(message, fieldName) {
    pushDataLayerEvent({
      event: 'review_validation_error',
      review_step_index: state.stepIndex + 1,
      review_step_name: getStepLabel(state.stepIndex),
      review_field: fieldName || '',
      review_message: message || ''
    });
  }

  function handleClick(event) {
    var nextButton = event.target.closest('[data-review-next]');
    var prevButton = event.target.closest('[data-review-prev]');
    var stepButton = event.target.closest('[data-review-step-target]');

    if (nextButton) {
      event.preventDefault();
      goToNextStep();
      return;
    }

    if (prevButton) {
      event.preventDefault();
      goToPrevStep();
      return;
    }

    if (stepButton) {
      event.preventDefault();

      if (stepButton.disabled) {
        return;
      }

      var stepTarget = parseInt(stepButton.getAttribute('data-review-step-target') || '', 10);
      if (isNaN(stepTarget) || stepTarget === state.stepIndex) {
        return;
      }

      if (stepTarget < state.stepIndex) {
        goToStep(stepTarget, 'jump_back');
      }
    }
  }

  function handleFieldChange(event) {
    var form = getForm();
    if (!form) return;

    syncSummary(form);
    clearFeedback();
    clearFieldInvalidState(event.target);

    if (event.target.type === 'radio') {
      scheduleAutoAdvance(event.target);
    }
  }

  function scheduleAutoAdvance(radio) {
    cancelAutoAdvance();

    var currentStep = state.steps[state.stepIndex];
    var shouldAutoAdvance = !prefersReducedMotionEnabled();
    if (!currentStep || state.stepIndex >= state.steps.length - 1) return;

    if (shouldAutoAdvance && typeof radio.matches === 'function') {
      try {
        shouldAutoAdvance = !radio.matches(':focus-visible');
      } catch (error) {
        shouldAutoAdvance = true;
      }
    }

    if (!shouldAutoAdvance) {
      return;
    }

    var option = radio.closest('.review-option');
    if (option) {
      option.classList.add('is-just-selected');
    }

    state.autoAdvanceTimer = setTimeout(function () {
      state.autoAdvanceTimer = null;
      if (option) {
        option.classList.remove('is-just-selected');
      }
      goToNextStep();
    }, AUTO_ADVANCE_DELAY);
  }

  function goToNextStep() {
    if (!validateCurrentStep()) {
      return;
    }

    if (state.stepIndex < state.steps.length - 1) {
      var previousIndex = state.stepIndex;

      state.stepIndex += 1;
      state.furthestStepIndex = Math.max(state.furthestStepIndex, state.stepIndex);

      updateStepUi({ focus: true });
      trackStepNavigation('next', previousIndex, state.stepIndex);
    }
  }

  function goToPrevStep() {
    clearFeedback();
    cancelAutoAdvance();

    if (state.stepIndex > 0) {
      var previousIndex = state.stepIndex;

      state.stepIndex -= 1;

      updateStepUi({ focus: true });
      trackStepNavigation('prev', previousIndex, state.stepIndex);
    }
  }

  function cancelAutoAdvance() {
    if (state.autoAdvanceTimer) {
      clearTimeout(state.autoAdvanceTimer);
      state.autoAdvanceTimer = null;
    }
  }

  function goToStep(index, direction) {
    if (index < 0 || index >= state.steps.length || index === state.stepIndex) {
      return;
    }

    clearFeedback();
    cancelAutoAdvance();

    var previousIndex = state.stepIndex;
    state.stepIndex = index;

    updateStepUi({ focus: true });
    trackStepNavigation(direction || 'jump', previousIndex, state.stepIndex);
  }

  function handleSubmit(event) {
    event.preventDefault();

    if (state.submitting) {
      return;
    }

    if (state.stepIndex !== state.steps.length - 1) {
      goToNextStep();
      return;
    }

    if (!validateCurrentStep()) {
      return;
    }

    submitRequest(event.target);
  }

  function submitRequest(form) {
    var submitButton = form.querySelector('[data-review-submit]');
    var payload = serializeForm(form);

    if (!payload.page_url) {
      var urlError = 'Bitte zuerst eine gültige URL angeben.';

      showFeedback(urlError, 'error');
      trackValidationError(urlError, 'page_url');
      state.stepIndex = 0;
      updateStepUi({ focus: true });
      return;
    }

    state.submitting = true;

    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = 'Anfrage wird gesendet ...';
    }

    clearFeedback();

    pushDataLayerEvent({
      event: 'review_request_submit_started',
      review_step_index: state.stepIndex + 1,
      review_step_name: getStepLabel(state.stepIndex),
      review_domain: getDomainFromUrl(payload.page_url)
    });

    fetch(config.restEndpoint || '/wp-json/nexus/v1/audit-request', {
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
            throw new Error('Die Anfrage konnte nicht verarbeitet werden. Bitte später erneut versuchen.');
          }

          if (!response.ok || !data.ok) {
            throw new Error(data.error || 'Die Anfrage konnte nicht gespeichert werden.');
          }

          return data;
        });
      })
      .then(function (data) {
        renderSuccess(payload, data);
      })
      .catch(function (error) {
        var message = error.message || 'Die Anfrage konnte nicht gesendet werden.';

        showFeedback(message, 'error');
        pushDataLayerEvent({
          event: 'review_request_submit_failed',
          review_step_index: state.stepIndex + 1,
          review_step_name: getStepLabel(state.stepIndex),
          review_domain: getDomainFromUrl(payload.page_url),
          review_message: message
        });
      })
      .finally(function () {
        state.submitting = false;

        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = submitLabel;
        }
      });
  }

  function validateCurrentStep() {
    var currentStep = state.steps[state.stepIndex];
    if (!currentStep) {
      return true;
    }

    clearFeedback();
    clearStepInvalidState(currentStep);

    var requiredRadioNames = [];
    Array.prototype.forEach.call(currentStep.querySelectorAll('input[type="radio"][required]'), function (radio) {
      if (!radio.name || requiredRadioNames.indexOf(radio.name) !== -1) {
        return;
      }

      requiredRadioNames.push(radio.name);
    });

    for (var radioIndex = 0; radioIndex < requiredRadioNames.length; radioIndex += 1) {
      var radioName = requiredRadioNames[radioIndex];
      var checkedRadio = currentStep.querySelector('input[name="' + radioName + '"]:checked');

      if (!checkedRadio) {
        var radioMessage = currentStep.getAttribute('data-review-radio-message') || 'Bitte eine Option auswählen.';
        var firstRadio = currentStep.querySelector('input[name="' + radioName + '"]');

        showFeedback(radioMessage, 'error');
        trackValidationError(radioMessage, radioName);
        markRadioGroupInvalid(firstRadio);

        if (firstRadio) {
          firstRadio.focus();
        }

        return false;
      }
    }

    var fields = currentStep.querySelectorAll('input, textarea, select');
    for (var i = 0; i < fields.length; i += 1) {
      var field = fields[i];

      if (field.type === 'radio' || field.type === 'hidden') {
        continue;
      }

      if (field.type === 'url') {
        normalizeUrlField(field);
      }

      if (!field.checkValidity()) {
        var validationMessage = field.getAttribute('data-review-required-message') || field.validationMessage || 'Bitte das Feld korrekt ausfüllen.';

        showFeedback(validationMessage, 'error');
        trackValidationError(validationMessage, field.name || field.id || 'unknown');
        markFieldInvalid(field);
        field.focus();

        return false;
      }
    }

    return true;
  }

  function updateStepUi(options) {
    options = options || {};

    var form = getForm();
    if (!form) return;

    var prevButton = form.querySelector('[data-review-prev]');
    var nextButton = form.querySelector('[data-review-next]');
    var submitButton = form.querySelector('[data-review-submit]');
    var progressFill = document.getElementById('review-progress-fill');
    var progressCurrent = document.getElementById('review-progress-current');
    var progressSteps = form.querySelectorAll('.review-progress-steps li');
    var prefersReducedMotion = prefersReducedMotionEnabled();

    state.steps.forEach(function (step, index) {
      var isTarget = index === state.stepIndex;

      if (isTarget) {
        step.classList.remove('is-leaving');
        step.classList.add('is-active');
      } else if (step.classList.contains('is-active') && !prefersReducedMotion) {
        step.classList.remove('is-active');
        step.classList.add('is-leaving');
        step.addEventListener('animationend', function handler() {
          step.removeEventListener('animationend', handler);
          step.classList.remove('is-leaving');
        }, { once: true });
      } else {
        step.classList.remove('is-active', 'is-leaving');
      }
    });

    Array.prototype.forEach.call(progressSteps, function (step, index) {
      var button = step.querySelector('button');
      var isCurrent = index === state.stepIndex;
      var isReached = index <= state.furthestStepIndex;
      var isComplete = index < state.stepIndex;

      step.classList.toggle('is-current', isCurrent);
      step.classList.toggle('is-reached', isReached);
      step.classList.toggle('is-complete', isComplete);

      if (button) {
        button.disabled = index > state.furthestStepIndex || isCurrent;

        if (isCurrent) {
          button.setAttribute('aria-current', 'step');
        } else {
          button.removeAttribute('aria-current');
        }
      }
    });

    if (progressFill) {
      progressFill.style.width = (((state.stepIndex + 1) / state.steps.length) * 100) + '%';
    }

    if (progressCurrent) {
      progressCurrent.textContent = 'Schritt ' + (state.stepIndex + 1) + ' von ' + state.steps.length + ': ' + getStepLabel(state.stepIndex);
    }

    if (prevButton) {
      prevButton.hidden = state.stepIndex === 0;
    }

    if (nextButton) {
      nextButton.hidden = state.stepIndex === state.steps.length - 1;
      nextButton.textContent = getNextButtonLabel();
    }

    if (submitButton) {
      submitButton.hidden = state.stepIndex !== state.steps.length - 1;
      submitButton.textContent = submitLabel;
    }

    if (options.focus) {
      focusCurrentStep();
    }

    trackStepView();
  }

  function getNextButtonLabel() {
    var labelsByStep = {
      0: 'Weiter zu Engpass',
      1: 'Weiter zu Ziel',
      2: 'Weiter zu Kontakt'
    };

    var nextIndex = state.stepIndex + 1;

    if (nextIndex >= state.steps.length) {
      return 'Weiter';
    }

    return labelsByStep[state.stepIndex] || ('Weiter zu ' + getStepLabel(nextIndex));
  }

  function focusCurrentStep() {
    var currentStep = state.steps[state.stepIndex];
    if (!currentStep) return;

    currentStep.setAttribute('tabindex', '-1');

    var scrollTarget = document.querySelector('.review-progress') || currentStep;
    if (typeof scrollTarget.scrollIntoView === 'function') {
      scrollTarget.scrollIntoView({ behavior: getScrollBehavior(), block: 'start' });
    }

    var firstInput = currentStep.querySelector('input:not([type="hidden"]):not([type="radio"]), textarea, select');
    if (firstInput) {
      firstInput.focus({ preventScroll: true });
    } else {
      currentStep.focus({ preventScroll: true });
    }
  }

  function renderSuccess(payload, data) {
    var form = getForm();
    var success = document.getElementById('review-request-success');
    var successMessage = document.getElementById('review-success-message');
    var successUrl = document.getElementById('review-success-url');
    var reviewBox = form ? form.closest('.review-box') : null;
    var focusTarget = success || form;

    if (successUrl) {
      successUrl.textContent = payload.page_url ? 'Seite im Audit: ' + payload.page_url : 'Seite im Audit';
    }

    if (successMessage && data && data.message) {
      successMessage.textContent = data.message;
    }

    if (form) {
      form.hidden = true;
    }

    if (success) {
      success.hidden = false;
      success.setAttribute('tabindex', '-1');
    }

    if (reviewBox) {
      reviewBox.classList.add('is-success');
    }

    state.submitted = true;

    pushDataLayerEvent({
      event: 'review_request_submit_success',
      review_request_id: data.requestId || null,
      review_domain: getDomainFromUrl(payload.page_url)
    });
    pushDataLayerEvent({
      event: 'review_request_submitted',
      review_request_id: data.requestId || null
    });

    if (focusTarget && typeof focusTarget.scrollIntoView === 'function') {
      focusTarget.scrollIntoView({ behavior: getScrollBehavior(), block: 'start' });
    }

    if (success && typeof success.focus === 'function') {
      success.focus({ preventScroll: true });
    }
  }

  function serializeForm(form) {
    var formData = new FormData(form);
    var payload = {};
    var attribution = window.NexusCore && typeof window.NexusCore.getLeadAttributionPayload === 'function'
      ? window.NexusCore.getLeadAttributionPayload()
      : {};

    formData.forEach(function (value, key) {
      payload[key] = typeof value === 'string' ? value.trim() : value;
    });

    Object.keys(attribution).forEach(function (key) {
      if (!attribution[key]) {
        return;
      }

      payload[key] = attribution[key];
    });

    return payload;
  }

  function showFeedback(message, type) {
    var feedback = document.getElementById('review-form-feedback');
    if (!feedback) return;

    feedback.textContent = message;
    feedback.className = 'review-form-feedback is-visible';
    feedback.classList.add(type === 'error' ? 'is-error' : 'is-success');
    feedback.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
    feedback.setAttribute('aria-atomic', 'true');
    feedback.setAttribute('role', type === 'error' ? 'alert' : 'status');
  }

  function clearFeedback() {
    var feedback = document.getElementById('review-form-feedback');
    if (!feedback) return;

    feedback.textContent = '';
    feedback.className = 'review-form-feedback';
    feedback.setAttribute('aria-live', 'polite');
    feedback.setAttribute('aria-atomic', 'true');
    feedback.removeAttribute('role');
  }

  function attachFeedbackDescription(field) {
    if (!field) return;

    var describedBy = (field.getAttribute('aria-describedby') || '')
      .split(/\s+/)
      .filter(Boolean);

    if (describedBy.indexOf(FEEDBACK_ID) === -1) {
      describedBy.push(FEEDBACK_ID);
      field.setAttribute('aria-describedby', describedBy.join(' '));
    }
  }

  function detachFeedbackDescription(field) {
    if (!field) return;

    var describedBy = (field.getAttribute('aria-describedby') || '')
      .split(/\s+/)
      .filter(Boolean)
      .filter(function (value) {
        return value !== FEEDBACK_ID;
      });

    if (describedBy.length) {
      field.setAttribute('aria-describedby', describedBy.join(' '));
      return;
    }

    field.removeAttribute('aria-describedby');
  }

  function markFieldInvalid(field) {
    if (!field) return;

    field.setAttribute('aria-invalid', 'true');
    attachFeedbackDescription(field);
    field.classList.add('is-invalid');

    var parentField = field.closest('.review-field');
    if (parentField) {
      parentField.classList.add('is-invalid');
    }

    if (field.type === 'checkbox') {
      var consentCard = field.closest('.review-consent-card');
      if (consentCard) {
        consentCard.classList.add('is-invalid');
      }
    }
  }

  function clearFieldInvalidState(field) {
    if (!field) return;

    field.removeAttribute('aria-invalid');
    detachFeedbackDescription(field);
    field.classList.remove('is-invalid');

    var parentField = field.closest('.review-field');
    if (parentField) {
      parentField.classList.remove('is-invalid');
    }

    if (field.type === 'checkbox') {
      var consentCard = field.closest('.review-consent-card');
      if (consentCard) {
        consentCard.classList.remove('is-invalid');
      }
    }

    if (field.type === 'radio' && field.name) {
      var form = getForm();
      if (!form) return;

      Array.prototype.forEach.call(form.querySelectorAll('input[name="' + field.name + '"]'), function (radio) {
        var option = radio.closest('.review-option');
        if (option) {
          option.classList.remove('is-invalid');
        }
      });

      var block = field.closest('.review-choice-block');
      if (block) {
        block.classList.remove('is-invalid');
        block.removeAttribute('aria-invalid');
      }
    }
  }

  function clearStepInvalidState(step) {
    if (!step) return;

    Array.prototype.forEach.call(step.querySelectorAll('.review-field.is-invalid, .review-choice-block.is-invalid, .review-option.is-invalid, .review-consent-card.is-invalid'), function (node) {
      node.classList.remove('is-invalid');
    });

    Array.prototype.forEach.call(step.querySelectorAll('[aria-invalid="true"], .is-invalid'), function (field) {
      if (field.hasAttribute && field.hasAttribute('aria-invalid')) {
        field.removeAttribute('aria-invalid');
      }

      if (field.classList) {
        field.classList.remove('is-invalid');
      }

      detachFeedbackDescription(field);
    });
  }

  function markRadioGroupInvalid(radio) {
    if (!radio) return;

    var form = getForm();
    var option = radio.closest('.review-option');
    var block = radio.closest('.review-choice-block');

    if (form && radio.name) {
      Array.prototype.forEach.call(form.querySelectorAll('input[name="' + radio.name + '"]'), function (groupRadio) {
        groupRadio.setAttribute('aria-invalid', 'true');
        attachFeedbackDescription(groupRadio);
      });
    }

    if (option) {
      option.classList.add('is-invalid');
    }

    if (block) {
      block.classList.add('is-invalid');
      block.setAttribute('aria-invalid', 'true');
    }
  }

  function syncSummary(form) {
    var summaryFields = form.querySelectorAll('[data-review-summary]');

    Array.prototype.forEach.call(summaryFields, function (node) {
      var key = node.getAttribute('data-review-summary');
      if (!key) return;

      var value = readSummaryValue(form, key);
      node.textContent = formatSummaryValue(key, value);
    });
  }

  function readSummaryValue(form, key) {
    var selected = form.querySelector('input[name="' + key + '"]:checked');
    if (selected) {
      var option = selected.closest('.review-option');
      var label = option ? option.querySelector('[data-review-label]') : null;

      return label ? label.textContent.trim() : selected.value;
    }

    var field = form.querySelector('[name="' + key + '"]');
    return field && typeof field.value === 'string' ? field.value.trim() : '';
  }

  function formatSummaryValue(key, value) {
    if (!value) {
      if (key === 'current_challenge') {
        return 'Nicht ergänzt';
      }

      return 'Noch offen';
    }

    if (key === 'page_url') {
      return getDomainFromUrl(value) || value;
    }

    if (key === 'current_challenge') {
      return truncateValue(value, 96);
    }

    return truncateValue(value, 78);
  }

  function truncateValue(value, maxLength) {
    if (!value || value.length <= maxLength) {
      return value;
    }

    return value.slice(0, maxLength - 1).trim() + '…';
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { init(); });
  } else {
    init();
  }
})();
