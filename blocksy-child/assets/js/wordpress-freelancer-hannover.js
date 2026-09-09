(() => {
	'use strict';

	const root = document.querySelector('.hu-fr');
	if (!root) return;

	/* Preserve existing measurement hooks without loading a tracking runtime. */
	const addHooks = (element, action, category, section) => {
		if (!element.dataset.trackAction) element.dataset.trackAction = action;
		if (!element.dataset.trackCategory) element.dataset.trackCategory = category;
		if (!element.dataset.trackSection) element.dataset.trackSection = section;
	};

	root.querySelectorAll('.hu-fr-nav a[href^="#"]').forEach((link) => {
		const id = link.getAttribute('href').slice(1);
		if (id === 'anfrage') {
			addHooks(link, 'cta_freelancer_toc_project', 'lead_gen', 'toc');
		} else {
			addHooks(link, `freelancer_toc_${id === 'arbeitsweise' ? 'ablauf' : id}`, 'navigation', 'context_dock');
		}
	});

	/* Keep earlier proof identities even though case and working evidence moved. */
	root.querySelectorAll('.hu-fr-evidence__links a').forEach((link, index) => {
		const action = ['freelancer_proof_link_1', 'freelancer_proof_checks', 'freelancer_proof_performance'][index];
		if (action) addHooks(link, action, 'trust', 'nachweis');
	});
	root.querySelectorAll('#nachweis a').forEach((link) => {
		addHooks(link, 'freelancer_proof_link_2', 'trust', 'nachweis');
	});
	root.querySelectorAll('.hu-fr-projects__grid h4 a').forEach((link, index) => {
		addHooks(link, `freelancer_proof_link_${index + 3}`, 'trust', 'nachweis');
	});

	root.querySelectorAll('.hu-fr-faq summary').forEach((summary, index) => {
		addHooks(summary, `freelancer_faq_${index + 1}`, 'engagement', 'fragen');
	});

	const form = root.querySelector('[data-fr-form]');
	if (!form || typeof window.fetch !== 'function' || typeof window.FormData !== 'function') return;

	const briefStep = form.querySelector('[data-fr-form-step="brief"]');
	const contactStep = form.querySelector('[data-fr-form-step="contact"]');
	const nextButton = form.querySelector('[data-fr-form-next]');
	const backButton = form.querySelector('[data-fr-form-back]');
	const submitButton = form.querySelector('[data-fr-form-submit]');
	const status = form.querySelector('[data-fr-form-status]');
	const focusSelect = form.querySelector('[name="focus"]');
	if (!briefStep || !contactStep || !nextButton || !backButton || !submitButton || !status || !focusSelect) return;

	let isSubmitting = false;
	let isSubmitted = false;
	const failureMessage = 'Die Anfrage wurde nicht bestätigt. Ihre Angaben bleiben erhalten. Bitte versuchen Sie es später erneut oder schreiben Sie mir eine E-Mail.';
	const fieldSelector = 'input:not([type="hidden"]), select, textarea';

	status.setAttribute('tabindex', '-1');
	status.setAttribute('aria-atomic', 'true');

	const setStatus = (message = '', type = '') => {
		status.textContent = message;
		status.classList.toggle('is-error', type === 'error');
		status.classList.toggle('is-success', type === 'success');
	};

	const showStep = (step, focusField = null) => {
		briefStep.hidden = step !== briefStep;
		contactStep.hidden = step !== contactStep;
		if (focusField) focusField.focus();
	};

	const markValidity = (field) => {
		field.setCustomValidity('');
		if (field.required && field.type !== 'checkbox' && !field.value.trim()) {
			field.setCustomValidity('Bitte dieses Feld ausfüllen.');
		} else if (field.name === 'message' && field.value.trim().length < field.minLength) {
			field.setCustomValidity(`Bitte beschreiben Sie Ihr Projekt mit mindestens ${field.minLength} Zeichen.`);
		}
		const valid = field.checkValidity();
		if (valid) field.removeAttribute('aria-invalid');
		else field.setAttribute('aria-invalid', 'true');
		return valid;
	};

	const validateStep = (step) => {
		const fields = Array.from(step.querySelectorAll(fieldSelector)).filter((field) => !field.disabled);
		let firstInvalid = null;
		fields.forEach((field) => {
			if (!markValidity(field) && !firstInvalid) firstInvalid = field;
		});
		if (!firstInvalid) return true;
		showStep(step, firstInvalid);
		setStatus(firstInvalid.validationMessage, 'error');
		firstInvalid.reportValidity();
		return false;
	};

	const updateValidity = (event) => {
		const field = event.target;
		if (field.matches(fieldSelector) && field.hasAttribute('aria-invalid')) markValidity(field);
	};
	form.addEventListener('input', updateValidity);
	form.addEventListener('change', updateValidity);
	form.addEventListener('focusout', (event) => {
		const field = event.target;
		if (field.matches(fieldSelector) && field.required) markValidity(field);
	});

	const nextStep = () => {
		setStatus();
		if (validateStep(briefStep)) showStep(contactStep, contactStep.querySelector('[name="email"]'));
	};
	nextButton.addEventListener('click', nextStep);
	backButton.addEventListener('click', () => {
		if (isSubmitting) return;
		setStatus();
		showStep(briefStep, briefStep.querySelector(fieldSelector));
	});

	root.querySelectorAll('[data-fr-project-focus]').forEach((link) => {
		link.addEventListener('click', (event) => {
			if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || isSubmitting || isSubmitted) return;
			const value = link.dataset.frProjectFocus;
			if (!Array.from(focusSelect.options).some((option) => option.value === value)) return;
			focusSelect.value = value;
			markValidity(focusSelect);
			setStatus();
			showStep(briefStep);
			/* Keep the link's native anchor navigation and the visitor's typed brief. */
		});
	});

	form.addEventListener('submit', async (event) => {
		event.preventDefault();
		if (isSubmitting || isSubmitted) return;
		if (!briefStep.hidden) {
			nextStep();
			return;
		}
		setStatus();
		if (!validateStep(briefStep) || !validateStep(contactStep)) return;

		/* Serialize before disabling controls. Field names match the existing REST contract. */
		const data = Object.fromEntries(new FormData(form).entries());
		data.consent = Boolean(form.querySelector('[name="consent"]:checked'));
		isSubmitting = true;
		submitButton.disabled = true;
		backButton.disabled = true;
		form.setAttribute('aria-busy', 'true');
		setStatus('Ihre Anfrage wird gesendet …');

		const controller = typeof window.AbortController === 'function' ? new AbortController() : null;
		const timeout = controller ? window.setTimeout(() => controller.abort(), 20000) : null;
		try {
			const response = await fetch(form.action, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
				credentials: 'same-origin',
				body: JSON.stringify(data),
				...(controller ? { signal: controller.signal } : {}),
			});
			const payload = await response.json().catch(() => null);
			if (!response.ok || !payload || payload.ok !== true) {
				setStatus(payload && typeof payload.error === 'string' ? payload.error : failureMessage, 'error');
				return;
			}
			isSubmitted = true;
			briefStep.hidden = true;
			contactStep.hidden = true;
			form.reset();
			setStatus(typeof payload.message === 'string' && payload.message ? payload.message : 'Danke. Ihre Anfrage ist eingegangen.', 'success');
		} catch {
			setStatus(failureMessage, 'error');
		} finally {
			if (timeout !== null) window.clearTimeout(timeout);
			isSubmitting = false;
			submitButton.disabled = false;
			backButton.disabled = false;
			form.removeAttribute('aria-busy');
			status.focus();
		}
	});

	/* Server markup stays hidden until submission and validation handlers are ready. */
	showStep(briefStep);
	form.hidden = false;
})();
