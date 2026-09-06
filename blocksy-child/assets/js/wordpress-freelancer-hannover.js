(() => {
	'use strict';

	const root = document.querySelector('.hu-fr');
	if (!root) return;

	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const sections = Array.from(root.querySelectorAll('[data-fr-section]'));
	const tocLinks = Array.from(root.querySelectorAll('[data-fr-toc-link]'));
	const tocFill = root.querySelector('[data-fr-toc-fill]');
	const pageProgress = root.querySelector('[data-fr-progress]');

	const setCurrent = (id) => {
		tocLinks.forEach((link) => {
			if (link.getAttribute('href') === `#${id}`) {
				link.setAttribute('aria-current', 'true');
			} else {
				link.removeAttribute('aria-current');
			}
		});
	};

	if (sections.length) {
		setCurrent(sections[0].id);
	}

	if ('IntersectionObserver' in window && sections.length) {
		const visible = new Map();
		const sectionObserver = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => visible.set(entry.target.id, entry.isIntersecting));
				const current = sections.find((section) => visible.get(section.id));
				if (current) setCurrent(current.id);
			},
			{ rootMargin: '-18% 0px -66% 0px', threshold: 0 }
		);
		sections.forEach((section) => sectionObserver.observe(section));
	}

	let ticking = false;
	const updateProgress = () => {
		const doc = document.scrollingElement || document.documentElement;
		const max = Math.max(1, doc.scrollHeight - doc.clientHeight);
		const value = Math.min(1, Math.max(0, doc.scrollTop / max));
		if (tocFill) tocFill.style.transform = `scaleY(${value})`;
		if (pageProgress) pageProgress.style.transform = `scaleX(${value})`;
	};

	const requestProgress = () => {
		if (ticking) return;
		ticking = true;
		requestAnimationFrame(() => {
			ticking = false;
			updateProgress();
		});
	};

	window.addEventListener('scroll', requestProgress, { passive: true });
	window.addEventListener('resize', requestProgress, { passive: true });
	updateProgress();

	root.querySelectorAll('.hu-fr-toc-m a').forEach((link) => {
		link.addEventListener('click', () => {
			const details = link.closest('details');
			if (details) details.open = false;
		});
	});

	const accordion = root.querySelector('[data-fr-accordion]');
	if (accordion) {
		const items = Array.from(accordion.querySelectorAll('details'));
		items.forEach((item) => {
			item.addEventListener('toggle', () => {
				if (!item.open) return;
				items.forEach((other) => {
					if (other !== item && other.open) other.open = false;
				});
			});
		});
	}

	const route = root.querySelector('[data-fr-route]');
	if (route) {
		if (reduceMotion.matches || !('IntersectionObserver' in window)) {
			route.classList.add('is-active');
		} else {
			const routeObserver = new IntersectionObserver(
				(entries, observer) => {
					entries.forEach((entry) => {
						if (!entry.isIntersecting) return;
						route.classList.add('is-active');
						observer.unobserve(entry.target);
					});
				},
				{ threshold: 0.28 }
			);
			routeObserver.observe(route);
		}
	}

	const form = root.querySelector('[data-fr-form]');
	if (!form) return;

	const briefStep = form.querySelector('[data-fr-form-step="brief"]');
	const contactStep = form.querySelector('[data-fr-form-step="contact"]');
	const nextButton = form.querySelector('[data-fr-form-next]');
	const backButton = form.querySelector('[data-fr-form-back]');
	const submitButton = form.querySelector('[data-fr-form-submit]');
	const status = form.querySelector('[data-fr-form-status]');

	const setStatus = (message = '', type = '') => {
		if (!status) return;
		status.textContent = message;
		status.classList.toggle('is-error', type === 'error');
		status.classList.toggle('is-success', type === 'success');
	};

	const markValidity = (field) => {
		const valid = field.checkValidity();
		if (valid) field.removeAttribute('aria-invalid');
		else field.setAttribute('aria-invalid', 'true');
		return valid;
	};

	const validateStep = (step) => {
		const fields = Array.from(step.querySelectorAll('input:not([type="hidden"]), select, textarea'))
			.filter((field) => !field.disabled);
		let firstInvalid = null;
		fields.forEach((field) => {
			if (!markValidity(field) && !firstInvalid) firstInvalid = field;
		});
		if (firstInvalid) {
			firstInvalid.focus();
			return false;
		}
		return true;
	};

	form.addEventListener('input', (event) => {
		const field = event.target;
		if (field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement) {
			if (field.hasAttribute('aria-invalid')) markValidity(field);
		}
	});

	if (nextButton && briefStep && contactStep) {
		nextButton.addEventListener('click', () => {
			setStatus();
			if (!validateStep(briefStep)) {
				setStatus('Bitte die drei Angaben kurz vervollständigen.', 'error');
				return;
			}
			briefStep.hidden = true;
			contactStep.hidden = false;
			const email = contactStep.querySelector('input[type="email"]');
			if (email) email.focus({ preventScroll: true });
			contactStep.scrollIntoView({ behavior: reduceMotion.matches ? 'auto' : 'smooth', block: 'center' });
		});
	}

	if (backButton && briefStep && contactStep) {
		backButton.addEventListener('click', () => {
			setStatus();
			contactStep.hidden = true;
			briefStep.hidden = false;
			const first = briefStep.querySelector('input, select, textarea');
			if (first) first.focus({ preventScroll: true });
		});
	}

	form.addEventListener('submit', async (event) => {
		event.preventDefault();
		setStatus();

		if (briefStep && !validateStep(briefStep)) {
			if (contactStep) contactStep.hidden = true;
			briefStep.hidden = false;
			setStatus('Bitte die Projektangaben prüfen.', 'error');
			return;
		}
		if (contactStep && !validateStep(contactStep)) {
			setStatus('Bitte E-Mail und Datenschutz-Zustimmung prüfen.', 'error');
			return;
		}

		if (submitButton) {
			submitButton.disabled = true;
			submitButton.setAttribute('aria-busy', 'true');
		}

		const data = Object.fromEntries(new FormData(form).entries());
		data.consent = Boolean(form.querySelector('[name="consent"]:checked'));

		try {
			const response = await fetch(form.action, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
				credentials: 'same-origin',
				body: JSON.stringify(data),
			});
			const payload = await response.json().catch(() => ({}));
			if (!response.ok || !payload.ok) {
				throw new Error(payload.error || 'Die Anfrage konnte gerade nicht gesendet werden.');
			}
			if (briefStep) briefStep.hidden = true;
			if (contactStep) contactStep.hidden = true;
			setStatus(payload.message || 'Danke. Die Anfrage ist eingegangen.', 'success');
			form.reset();
		} catch (error) {
			setStatus(error instanceof Error ? error.message : 'Die Anfrage konnte gerade nicht gesendet werden.', 'error');
		} finally {
			if (submitButton) {
				submitButton.disabled = false;
				submitButton.removeAttribute('aria-busy');
			}
		}
	});
})();
