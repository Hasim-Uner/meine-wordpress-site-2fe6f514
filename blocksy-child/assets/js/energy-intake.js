(function () {
  'use strict';

  var config = window.NexusEnergyFormConfig || {};
  var AUTO_ADVANCE_DELAY = 260;
  var state = {
    form: null,
    root: null,
    steps: [],
    stepButtons: [],
    stepId: '',
    furthestIndex: 0,
    submitting: false,
    submitted: false,
    autoAdvanceTimer: null
  };

  function init() {
    var form = document.querySelector('[data-energy-form]');
    if (!form || typeof window.fetch !== 'function') {
      return;
    }

    state.form = form;
    state.root = form.closest('.energy-form-shell') || form.parentElement || document;
    state.steps = Array.prototype.slice.call(form.querySelectorAll('[data-energy-step-id]'));
    state.stepButtons = Array.prototype.slice.call(form.querySelectorAll('[data-energy-step-target]'));

    if (!state.steps.length) {
      return;
    }

    var startedField = form.querySelector('[name="started_at"]');
    if (startedField && !startedField.value) {
      startedField.value = String(Date.now());
    }

    captureAdsParams(form);

    form.classList.add('review-funnel--js');
    form.addEventListener('click', handleClick);
    form.addEventListener('submit', handleSubmit);
    form.addEventListener('change', handleFieldChange);
    form.addEventListener('input', handleFieldChange);

    state.stepId = getInitialStepId();
    state.furthestIndex = Math.max(0, getCurrentIndex());

    updateStepUi({ announce: true });
    syncSummary();
  }

  function prefersReducedMotionEnabled() {
    return Boolean(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  }

  function getScrollBehavior() {
    return prefersReducedMotionEnabled() ? 'auto' : 'smooth';
  }

  function captureAdsParams(form) {
    var urlParams = new URLSearchParams(window.location.search);
    var source = urlParams.get('utm_source') || urlParams.get('ads_source') || '';
    var keyword = urlParams.get('keyword') || urlParams.get('ads_keyword') || '';
    var sourceField = form.querySelector('[name="ads_source"]');
    var keywordField = form.querySelector('[name="ads_keyword"]');

    if (sourceField) {
      sourceField.value = source;
    }

    if (keywordField) {
      keywordField.value = keyword;
    }
  }

  function getInitialStepId() {
    var activeStep = state.form.querySelector('.energy-step.is-active[data-energy-step-id]');
    if (activeStep && isStepRelevant(activeStep)) {
      return activeStep.getAttribute('data-energy-step-id') || '';
    }

    var activeSteps = getActiveSteps();
    return activeSteps.length ? activeSteps[0].id : '';
  }

  function getActiveSteps() {
    return state.steps
      .filter(function (step) {
        return isStepRelevant(step);
      })
      .map(function (step) {
        return {
          id: step.getAttribute('data-energy-step-id') || '',
          label: step.getAttribute('data-energy-step-label') || '',
          el: step
        };
      });
  }

  function isStepRelevant(step) {
    var field = step.getAttribute('data-energy-show-field');
    var values = step.getAttribute('data-energy-show-values');

    if (!field || !values) {
      return true;
    }

    return values.split(',').indexOf(getFieldValue(field)) !== -1;
  }

  function getCurrentIndex() {
    var activeSteps = getActiveSteps();
    var index = findActiveStepIndexById(activeSteps, state.stepId);

    if (index !== -1) {
      return index;
    }

    return 0;
  }

  function getCurrentStep() {
    var i;

    for (i = 0; i < state.steps.length; i += 1) {
      if ((state.steps[i].getAttribute('data-energy-step-id') || '') === state.stepId) {
        return state.steps[i];
      }
    }

    return null;
  }

  function getFieldValue(name) {
    if (!state.form || !name) {
      return '';
    }

    var checked = state.form.querySelector('input[name="' + name + '"]:checked');
    if (checked) {
      return (checked.value || '').trim();
    }

    var field = state.form.querySelector('[name="' + name + '"]');
    if (!field) {
      return '';
    }

    if (field.type === 'checkbox') {
      return field.checked ? (field.value || '').trim() : '';
    }

    return typeof field.value === 'string' ? field.value.trim() : '';
  }

  function handleClick(event) {
    var optionCard = event.target.closest('.review-option');
    var nextButton = event.target.closest('[data-energy-next-button]');
    var prevButton = event.target.closest('[data-energy-prev]');
    var stepButton = event.target.closest('[data-energy-step-target]');

    if (optionCard && !nextButton && !prevButton && !stepButton) {
      var optionInput = optionCard.querySelector('input[type="radio"]');

      if (optionInput && !optionInput.disabled && event.target !== optionInput) {
        event.preventDefault();

        if (!optionInput.checked) {
          optionInput.checked = true;
          optionInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (typeof optionInput.focus === 'function') {
          optionInput.focus({ preventScroll: true });
        }

        return;
      }
    }

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

      var targetId = stepButton.getAttribute('data-energy-step-target') || '';
      if (targetId) {
        goToStep(targetId);
      }
    }
  }

  function handleFieldChange(event) {
    var target = event.target;
    if (!target) {
      return;
    }

    if (target.type === 'url') {
      normalizeUrlField(target);
    }

    clearFeedback();
    clearFieldError(target);
    syncSummary();

    if (target.type === 'radio') {
      scheduleAutoAdvance(target);
      updateStepUi();
    }
  }

  function scheduleAutoAdvance(radio) {
    cancelAutoAdvance();

    var currentStep = getCurrentStep();
    if (!currentStep || currentStep.getAttribute('data-energy-kind') !== 'single_choice') {
      return;
    }

    if (prefersReducedMotionEnabled()) {
      return;
    }

    state.autoAdvanceTimer = window.setTimeout(function () {
      state.autoAdvanceTimer = null;
      goToNextStep();
    }, AUTO_ADVANCE_DELAY);
  }

  function cancelAutoAdvance() {
    if (state.autoAdvanceTimer) {
      window.clearTimeout(state.autoAdvanceTimer);
      state.autoAdvanceTimer = null;
    }
  }

  function goToNextStep() {
    var activeSteps = getActiveSteps();
    var currentIndex = getCurrentIndex();

    if (!validateCurrentStep()) {
      return;
    }

    if (currentIndex >= activeSteps.length - 1) {
      return;
    }

    state.stepId = activeSteps[currentIndex + 1].id;
    state.furthestIndex = Math.max(state.furthestIndex, currentIndex + 1);

    updateStepUi({ focus: true, announce: true });
  }

  function goToPrevStep() {
    var activeSteps = getActiveSteps();
    var currentIndex = getCurrentIndex();

    clearFeedback();
    cancelAutoAdvance();

    if (currentIndex <= 0) {
      return;
    }

    state.stepId = activeSteps[currentIndex - 1].id;
    updateStepUi({ focus: true, announce: true });
  }

  function goToStep(stepId) {
    var activeSteps = getActiveSteps();
    var targetIndex = findActiveStepIndexById(activeSteps, stepId);

    if (targetIndex === -1 || targetIndex > state.furthestIndex) {
      return;
    }

    state.stepId = stepId;
    updateStepUi({ focus: true, announce: true });
  }

  function updateStepUi(options) {
    options = options || {};

    var activeSteps = getActiveSteps();
    var currentIndex = findActiveStepIndexById(activeSteps, state.stepId);

    if (currentIndex === -1 && activeSteps.length) {
      state.stepId = activeSteps[0].id;
      currentIndex = 0;
    }

    state.furthestIndex = Math.min(Math.max(state.furthestIndex, currentIndex), Math.max(activeSteps.length - 1, 0));

    state.steps.forEach(function (step) {
      var stepId = step.getAttribute('data-energy-step-id') || '';
      var isRelevant = activeSteps.some(function (item) {
        return item.id === stepId;
      });
      var isActive = isRelevant && stepId === state.stepId;

      setDisplayed(step, isActive);
      step.setAttribute('aria-hidden', isActive ? 'false' : 'true');
      step.classList.toggle('is-active', isActive);
    });

    state.stepButtons.forEach(function (button) {
      var targetId = button.getAttribute('data-energy-step-target') || '';
      var stepItem = findActiveStepById(activeSteps, targetId);
      var itemIndex = findActiveStepIndexById(activeSteps, targetId);
      var parent = button.closest('li');
      var isCurrent = itemIndex === currentIndex;
      var isReached = itemIndex !== -1 && itemIndex <= state.furthestIndex;
      var isVisible = Boolean(stepItem);

      if (parent) {
        parent.hidden = !isVisible;
        parent.classList.toggle('is-current', isCurrent);
        parent.classList.toggle('is-reached', isReached);
        parent.classList.toggle('is-complete', itemIndex !== -1 && itemIndex < currentIndex);
      }

      button.disabled = !isVisible || itemIndex > state.furthestIndex || isCurrent;

      if (isCurrent) {
        button.setAttribute('aria-current', 'step');
      } else {
        button.removeAttribute('aria-current');
      }
    });

    updateProgress(activeSteps, currentIndex);
    updateActionButtons(activeSteps, currentIndex);
    syncSummary();
    syncContactPreference();

    if (options.focus) {
      focusCurrentStep();
    }

    if (options.announce) {
      announceCurrentStep(activeSteps, currentIndex);
    }
  }

  function updateProgress(activeSteps, currentIndex) {
    var fill = document.getElementById('energy-progress-fill');
    var current = document.getElementById('energy-progress-current');
    var track = document.getElementById('energy-progress-track');
    var stepItem = activeSteps[currentIndex];
    var label = stepItem ? stepItem.label : '';
    var stepId = stepItem ? stepItem.id : '';
    var total = activeSteps.length || 1;
    var width = ((currentIndex + 1) / total) * 100;
    var messages = config.progressMessages || {};
    var contextMessage = messages[stepId] || '';
    var currentText = contextMessage
      ? 'Schritt ' + (currentIndex + 1) + ' von ' + total + ' — ' + contextMessage
      : 'Schritt ' + (currentIndex + 1) + ' von ' + total + ': ' + label;

    if (fill) {
      fill.style.width = width + '%';
    }

    if (current) {
      current.textContent = currentText;
    }

    if (track) {
      track.setAttribute('aria-valuemax', String(total));
      track.setAttribute('aria-valuenow', String(currentIndex + 1));
      track.setAttribute('aria-valuetext', currentText);
    }
  }

  function updateActionButtons(activeSteps, currentIndex) {
    var prevButton = state.form.querySelector('[data-energy-prev]');
    var nextButton = state.form.querySelector('[data-energy-next-button]');
    var submitButton = state.form.querySelector('[data-energy-submit]');
    var isLast = currentIndex >= activeSteps.length - 1;
    var nextLabel = activeSteps[currentIndex + 1] ? activeSteps[currentIndex + 1].label : 'Weiter';
    var currentStep = activeSteps[currentIndex] ? activeSteps[currentIndex].el : null;
    var isAutoAdvanceChoice = currentStep
      && currentStep.getAttribute('data-energy-kind') === 'single_choice'
      && currentStep.getAttribute('data-energy-auto-advance') === 'true'
      && !prefersReducedMotionEnabled();

    if (prevButton) {
      setDisplayed(prevButton, currentIndex > 0, 'inline-flex');
    }

    if (nextButton) {
      setDisplayed(nextButton, !isLast && !isAutoAdvanceChoice, 'inline-flex');
      nextButton.textContent = activeSteps[currentIndex + 1] ? ('Weiter: ' + nextLabel) : 'Weiter';
    }

    if (submitButton) {
      setDisplayed(submitButton, isLast, 'inline-flex');
      submitButton.textContent = config.submitLabel || 'Standortbestimmung absenden';
    }
  }

  function setDisplayed(node, isVisible, displayValue) {
    if (!node) {
      return;
    }

    node.hidden = !isVisible;
    node.style.display = isVisible ? (displayValue || '') : 'none';

    if (isVisible) {
      node.removeAttribute('aria-hidden');
      return;
    }

    node.setAttribute('aria-hidden', 'true');
  }

  function announceCurrentStep(activeSteps, currentIndex) {
    var liveRegion = state.form.querySelector('[data-energy-step-live]');
    var current = activeSteps[currentIndex];

    if (!liveRegion || !current) {
      return;
    }

    liveRegion.textContent = 'Abschnitt ' + (currentIndex + 1) + ' von ' + activeSteps.length + ': ' + current.label + '.';
  }

  function focusCurrentStep() {
    var step = getCurrentStep();
    if (!step) {
      return;
    }

    var scrollTarget = state.form.querySelector('.review-progress') || step;
    if (typeof scrollTarget.scrollIntoView === 'function') {
      scrollTarget.scrollIntoView({ behavior: getScrollBehavior(), block: 'start' });
    }

    var target =
      step.querySelector('input[type="radio"]:checked') ||
      step.querySelector('input[type="radio"]') ||
      step.querySelector('input:not([type="hidden"]), textarea, select') ||
      step;

    if (target && typeof target.focus === 'function') {
      target.focus({ preventScroll: true });
    }
  }

  function validateCurrentStep() {
    var step = getCurrentStep();
    if (!step) {
      return true;
    }

    clearFeedback();
    clearStepErrors(step);

    if (step.getAttribute('data-energy-kind') === 'single_choice') {
      var fieldName = step.getAttribute('data-energy-field') || '';
      var checked = fieldName ? step.querySelector('input[name="' + fieldName + '"]:checked') : null;
      if (!checked) {
        var radioMessage = 'Bitte eine Option auswählen.';
        showFeedback(radioMessage, 'error');
        markRadioGroupInvalid(step, radioMessage);
        var firstRadio = step.querySelector('input[type="radio"]');
        if (firstRadio) {
          firstRadio.focus();
        }
        return false;
      }

      return true;
    }

    var fields = Array.prototype.slice.call(step.querySelectorAll('input, textarea, select'));
    for (var i = 0; i < fields.length; i += 1) {
      var field = fields[i];
      var fieldName = field.name || field.id || 'field';

      if (field.type === 'hidden' || field.disabled) {
        continue;
      }

      if (field.type === 'url') {
        normalizeUrlField(field);
      }

      if (field.type === 'checkbox' && !field.checked && field.required) {
        setFieldError(field, 'Bitte bestätigen Sie den Datenschutzhinweis.');
        return false;
      }

      if (!field.checkValidity()) {
        setFieldError(field, getFieldMessage(fieldName, field));
        return false;
      }
    }

    return true;
  }

  function getFieldMessage(name, field) {
    var messages = {
      name: 'Bitte Vor- und Nachname angeben.',
      company: 'Bitte Ihr Unternehmen angeben.',
      email: 'Bitte eine gültige geschäftliche E-Mail-Adresse angeben.',
      page_url: 'Bitte eine gültige URL angeben oder das Feld leer lassen.',
      postal_code: 'Bitte eine gültige fünfstellige deutsche Postleitzahl angeben.',
      consent_privacy: 'Bitte bestätigen Sie den Datenschutzhinweis.'
    };

    if (messages[name]) {
      return messages[name];
    }

    return field.validationMessage || 'Bitte das Feld korrekt ausfüllen.';
  }

  function setFieldError(field, message) {
    if (!field) {
      return;
    }

    showFeedback(message, 'error');
    field.setAttribute('aria-invalid', 'true');
    field.classList.add('is-invalid');

    var errorId = '';
    if (field.id) {
      errorId = field.id + '-error';
    }

    var errorNode =
      (errorId && document.getElementById(errorId)) ||
      state.form.querySelector('[data-energy-field-error="' + field.name + '"]');

    if (errorNode) {
      errorNode.textContent = message;
    }

    if (field.type === 'checkbox') {
      var consentCard = field.closest('.review-consent-card');
      if (consentCard) {
        consentCard.classList.add('is-invalid');
      }
    } else {
      var wrapper = field.closest('.review-field');
      if (wrapper) {
        wrapper.classList.add('is-invalid');
      }
    }

    if (typeof field.focus === 'function') {
      field.focus();
    }
  }

  function clearFieldError(field) {
    if (!field) {
      return;
    }

    field.removeAttribute('aria-invalid');
    field.classList.remove('is-invalid');

    if (field.id) {
      var fieldError = document.getElementById(field.id + '-error');
      if (fieldError) {
        fieldError.textContent = '';
      }
    }

    var byName = field.name ? state.form.querySelector('[data-energy-field-error="' + field.name + '"]') : null;
    if (byName) {
      byName.textContent = '';
    }

    var wrapper = field.closest('.review-field');
    if (wrapper) {
      wrapper.classList.remove('is-invalid');
    }

    var consentCard = field.closest('.review-consent-card');
    if (consentCard) {
      consentCard.classList.remove('is-invalid');
    }

    if (field.type === 'radio') {
      var choiceBlock = field.closest('.review-choice-block');
      if (choiceBlock) {
        choiceBlock.classList.remove('is-invalid');
      }

      var choiceError = state.root
        ? state.root.querySelector('[data-energy-choice-error="' + field.name + '"]')
        : null;
      if (choiceError) {
        choiceError.textContent = '';
      }

      Array.prototype.forEach.call(state.form.querySelectorAll('input[name="' + field.name + '"]'), function (radio) {
        radio.removeAttribute('aria-invalid');
        var option = radio.closest('.review-option');
        if (option) {
          option.classList.remove('is-invalid');
        }
      });
    }
  }

  function clearStepErrors(step) {
    Array.prototype.forEach.call(step.querySelectorAll('[aria-invalid="true"]'), function (node) {
      node.removeAttribute('aria-invalid');
    });

    Array.prototype.forEach.call(step.querySelectorAll('.is-invalid'), function (node) {
      node.classList.remove('is-invalid');
    });

    Array.prototype.forEach.call(step.querySelectorAll('.energy-field-error'), function (node) {
      node.textContent = '';
    });
  }

  function markRadioGroupInvalid(step, message) {
    var block = step.querySelector('.review-choice-block');
    var firstRadio = step.querySelector('input[type="radio"]');

    if (block) {
      block.classList.add('is-invalid');
    }

    if (firstRadio && state.root) {
      var choiceError = state.root.querySelector('[data-energy-choice-error="' + firstRadio.name + '"]');
      if (choiceError) {
        choiceError.textContent = message || 'Bitte eine Option auswählen.';
      }
    }

    if (!firstRadio) {
      return;
    }

    Array.prototype.forEach.call(step.querySelectorAll('input[name="' + firstRadio.name + '"]'), function (radio) {
      radio.setAttribute('aria-invalid', 'true');
      var option = radio.closest('.review-option');
      if (option) {
        option.classList.add('is-invalid');
      }
    });
  }

  function handleSubmit(event) {
    event.preventDefault();

    if (state.submitting) {
      return;
    }

    var activeSteps = getActiveSteps();
    var currentIndex = getCurrentIndex();
    if (currentIndex < activeSteps.length - 1) {
      goToNextStep();
      return;
    }

    if (!validateCurrentStep()) {
      return;
    }

    submitRequest();
  }

  function submitRequest() {
    var submitButton = state.form.querySelector('[data-energy-submit]');
    var payload = serializeForm();

    state.submitting = true;

    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = config.submittingLabel || 'Anfrage wird gesendet …';
    }

    clearFeedback();

    window.fetch(config.restEndpoint || '/wp-json/nexus/v1/audit-request', {
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
            throw new Error(config.errorMessage || 'Die Anfrage konnte gerade nicht verarbeitet werden.');
          }

          if (!response.ok || !data.ok) {
            throw new Error(data.error || config.errorMessage || 'Die Anfrage konnte gerade nicht gesendet werden.');
          }

          return data;
        });
      })
      .then(function (data) {
        renderSuccess(data);
      })
      .catch(function (error) {
        showFeedback(error.message || config.errorMessage || 'Die Anfrage konnte gerade nicht gesendet werden.', 'error');
      })
      .finally(function () {
        state.submitting = false;

        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = config.submitLabel || 'Standortbestimmung absenden';
        }
      });
  }

  function serializeForm() {
    var payload = {};
    var formData = new FormData(state.form);
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

  function renderSuccess(data) {
    var success = document.getElementById('energy-request-success');
    var successMessage = document.getElementById('energy-success-message');

    state.submitted = true;

    if (state.form) {
      state.form.hidden = true;
    }

    if (successMessage && data && data.message) {
      successMessage.textContent = data.message;
    }

    if (success) {
      success.hidden = false;
      success.setAttribute('tabindex', '-1');
      success.scrollIntoView({ behavior: getScrollBehavior(), block: 'start' });
      success.focus({ preventScroll: true });
    }
  }

  function showFeedback(message, type) {
    var feedback = document.getElementById('energy-form-feedback');
    if (!feedback) {
      return;
    }

    feedback.textContent = message;
    feedback.className = 'review-form-feedback is-visible';
    feedback.classList.add(type === 'error' ? 'is-error' : 'is-success');
    feedback.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
    feedback.setAttribute('aria-atomic', 'true');
    feedback.setAttribute('role', type === 'error' ? 'alert' : 'status');
  }

  function clearFeedback() {
    var feedback = document.getElementById('energy-form-feedback');
    if (!feedback) {
      return;
    }

    feedback.textContent = '';
    feedback.className = 'review-form-feedback';
    feedback.setAttribute('aria-live', 'polite');
    feedback.setAttribute('aria-atomic', 'true');
    feedback.removeAttribute('role');
  }

  function normalizeUrlField(field) {
    if (!field || field.type !== 'url') {
      return;
    }

    var value = (field.value || '').trim();
    if (value && !/^https?:\/\//i.test(value)) {
      field.value = 'https://' + value;
    }
  }

  function syncContactPreference() {
    var pref = getFieldValue('contact_preference');
    var phoneWrapper = state.form ? state.form.querySelector('[data-energy-phone-field]') : null;

    if (!phoneWrapper) {
      return;
    }

    var phoneInput = phoneWrapper.querySelector('input');
    var isPhone = pref === 'email_und_telefon';

    phoneWrapper.hidden = !isPhone;
    phoneWrapper.style.display = isPhone ? '' : 'none';

    if (!isPhone && phoneInput) {
      phoneInput.value = '';
    }
  }

  function syncSummary() {
    var summaryNodes = state.root
      ? state.root.querySelectorAll('[data-energy-summary]')
      : state.form.querySelectorAll('[data-energy-summary]');

    Array.prototype.forEach.call(summaryNodes, function (node) {
      var key = node.getAttribute('data-energy-summary') || '';
      var value = readSummaryValue(key);
      node.textContent = formatSummaryValue(key, value);
    });
  }

  function readSummaryValue(key) {
    var checked = state.form.querySelector('input[name="' + key + '"]:checked');
    if (checked) {
      var optionLabel = checked.getAttribute('data-energy-label');
      var option = checked.closest('.review-option');
      var label = option ? option.querySelector('[data-energy-option-label]') : null;

      if (optionLabel) {
        return optionLabel.trim();
      }

      return label ? label.textContent.trim() : checked.value;
    }

    var field = state.form.querySelector('[name="' + key + '"]');
    return field && typeof field.value === 'string' ? field.value.trim() : '';
  }

  function formatSummaryValue(key, value) {
    if (!value) {
      return key === 'measurement_state' ? 'Nur wenn relevant' : 'Noch offen';
    }

    if (key === 'page_url') {
      try {
        return new URL(value).hostname || value;
      } catch (error) {
        return value;
      }
    }

    if (key === 'current_challenge' && value.length > 72) {
      return value.slice(0, 71).trim() + '…';
    }

    return value;
  }

  function findActiveStepById(activeSteps, stepId) {
    var i;

    for (i = 0; i < activeSteps.length; i += 1) {
      if (activeSteps[i].id === stepId) {
        return activeSteps[i];
      }
    }

    return null;
  }

  function findActiveStepIndexById(activeSteps, stepId) {
    var i;

    for (i = 0; i < activeSteps.length; i += 1) {
      if (activeSteps[i].id === stepId) {
        return i;
      }
    }

    return -1;
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
