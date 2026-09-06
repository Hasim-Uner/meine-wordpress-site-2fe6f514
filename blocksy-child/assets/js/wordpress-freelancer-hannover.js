(() => {
	'use strict';

	const root = document.querySelector('.hu-fr');
	if (!root) return;

	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const sections = Array.from(root.querySelectorAll('[data-fr-section]'));
	const hero = root.querySelector('#start');
	const toc = root.querySelector('.hu-fr-toc');
	const tocLinks = Array.from(root.querySelectorAll('[data-fr-toc-link]'));
	const tocHeadTitle = root.querySelector('.hu-fr-toc__head b');
	const tocHeadMeta = root.querySelector('.hu-fr-toc__head span');
	const tocFill = root.querySelector('[data-fr-toc-fill]');
	const pageProgress = root.querySelector('[data-fr-progress]');
	const route = root.querySelector('[data-fr-route]');
	const routeItems = route ? Array.from(route.querySelectorAll('li')) : [];

	/*
	 * Diagonal Unicode arrows fall back to color emoji on some macOS/Firefox
	 * stacks. Normalize route-local decorative arrows to a text arrow.
	 */
	root.querySelectorAll('a, button').forEach((control) => {
		const walker = document.createTreeWalker(control, NodeFilter.SHOW_TEXT);
		let node = walker.nextNode();
		while (node) {
			if (node.nodeValue && /[\u2197\u2198]/.test(node.nodeValue)) {
				node.nodeValue = node.nodeValue.replace(/[\u2197\u2198]/g, '\u2192\uFE0E');
			}
			node = walker.nextNode();
		}
	});

	/* Stable measurement hooks without adding another tracking runtime. */
	tocLinks.forEach((link, index) => {
		const id = (link.getAttribute('href') || '').replace('#', '') || String(index + 1);
		if (!link.dataset.trackAction) link.dataset.trackAction = `freelancer_toc_${id}`;
		if (!link.dataset.trackCategory) link.dataset.trackCategory = 'navigation';
		if (!link.dataset.trackSection) link.dataset.trackSection = 'context_dock';
	});

	root.querySelectorAll('.hu-fr-situations a').forEach((link, index) => {
		if (!link.dataset.trackAction) link.dataset.trackAction = `freelancer_scope_select_${index + 1}`;
		if (!link.dataset.trackCategory) link.dataset.trackCategory = 'navigation';
		if (!link.dataset.trackSection) link.dataset.trackSection = 'einordnung';
	});

	root.querySelectorAll('.hu-fr-proof-grid a, .hu-fr-projects > a').forEach((link, index) => {
		if (!link.dataset.trackAction) link.dataset.trackAction = `freelancer_proof_link_${index + 1}`;
		if (!link.dataset.trackCategory) link.dataset.trackCategory = 'trust';
		if (!link.dataset.trackSection) link.dataset.trackSection = 'nachweis';
	});

	const setCurrent = (id) => {
		let currentSection = null;
		let currentIndex = -1;
		let currentLabel = '';

		sections.forEach((section, index) => {
			const isCurrent = section.id === id;
			section.classList.toggle('is-current', isCurrent);
			if (isCurrent) {
				currentSection = section;
				currentIndex = index;
			}
		});

		tocLinks.forEach((link) => {
			const isCurrent = link.getAttribute('href') === `#${id}`;
			if (isCurrent) {
				link.setAttribute('aria-current', 'true');
				const label = link.querySelector('.hu-fr-toc__label');
				currentLabel = label ? label.textContent.trim() : '';
			} else {
				link.removeAttribute('aria-current');
			}
		});

		if (toc && currentSection) {
			toc.classList.toggle('is-on-dark', currentSection.classList.contains('hu-fr-section--dark'));
		}

		if (tocHeadTitle && currentIndex >= 0) {
			tocHeadTitle.textContent = `${String(currentIndex + 1).padStart(2, '0')} · ${currentLabel || id}`;
		}
		if (tocHeadMeta) tocHeadMeta.textContent = 'Inhalt';
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

	const clamp01 = (value) => Math.min(1, Math.max(0, value));
	let ticking = false;

	const updateRouteProgress = () => {
		if (!route) return;

		if (reduceMotion.matches || window.innerWidth <= 760) {
			route.style.setProperty('--fr-route-progress', '1');
			routeItems.forEach((item) => item.classList.add('is-passed'));
			route.classList.add('is-complete');
			return;
		}

		const rect = route.getBoundingClientRect();
		const startLine = window.innerHeight * 0.78;
		const endLine = window.innerHeight * 0.24;
		const travel = Math.max(1, rect.height + startLine - endLine);
		const value = clamp01((startLine - rect.top) / travel);

		route.style.setProperty('--fr-route-progress', value.toFixed(4));

		const lastIndex = Math.max(1, routeItems.length - 1);
		routeItems.forEach((item, index) => {
			const threshold = Math.max(0, (index / lastIndex) - 0.035);
			item.classList.toggle('is-passed', value >= threshold);
		});
		route.classList.toggle('is-complete', value >= 0.92);
	};

	const updateFrame = () => {
		const doc = document.scrollingElement || document.documentElement;
		const headerOffset = 96;
		const heroBoundary = hero ? hero.offsetTop + hero.offsetHeight : 0;
		const contentStart = Math.max(0, heroBoundary - headerOffset);
		const pastHero = !hero || doc.scrollTop >= contentStart;

		root.classList.toggle('is-past-hero', pastHero);
		if (toc) toc.classList.toggle('is-available', pastHero);

		const progressEnd = Math.max(contentStart + 1, doc.scrollHeight - doc.clientHeight);
		const progressValue = clamp01((doc.scrollTop - contentStart) / (progressEnd - contentStart));
		if (toc) toc.style.setProperty('--fr-toc-progress', progressValue.toFixed(4));
		if (tocFill) tocFill.style.transform = `scaleY(${progressValue})`;
		if (pageProgress) pageProgress.style.transform = `scaleX(${progressValue})`;

		updateRouteProgress();
	};

	const requestFrame = () => {
		if (ticking) return;
		ticking = true;
		requestAnimationFrame(() => {
			ticking = false;
			updateFrame();
		});
	};

	window.addEventListener('scroll', requestFrame, { passive: true });
	window.addEventListener('resize', requestFrame, { passive: true });

	if (typeof reduceMotion.addEventListener === 'function') {
		reduceMotion.addEventListener('change', requestFrame);
	} else if (typeof reduceMotion.addListener === 'function') {
		reduceMotion.addListener(requestFrame);
	}

	requestAnimationFrame(() => {
		if (hero && !reduceMotion.matches) {
			hero.classList.add('is-motion-live');
		}
		updateFrame();
	});

	root.querySelectorAll('.hu-fr-toc-m a').forEach((link, index) => {
		if (!link.dataset.trackAction) link.dataset.trackAction = `freelancer_mobile_toc_${index + 1}`;
		if (!link.dataset.trackCategory) link.dataset.trackCategory = 'navigation';
		if (!link.dataset.trackSection) link.dataset.trackSection = 'mobile_toc';
		link.addEventListener('click', () => {
			const details = link.closest('details');
			if (details) details.open = false;
		});
	});

	const accordion = root.querySelector('[data-fr-accordion]');
	if (accordion) {
		const items = Array.from(accordion.querySelectorAll('details'));
		items.forEach((item, index) => {
			const summary = item.querySelector('summary');
			if (summary) {
				if (!summary.dataset.trackAction) summary.dataset.trackAction = `freelancer_faq_${index + 1}`;
				if (!summary.dataset.trackCategory) summary.dataset.trackCategory = 'engagement';
				if (!summary.dataset.trackSection) summary.dataset.trackSection = 'fragen';
			}
			item.addEventListener('toggle', () => {
				if (!item.open) return;
				items.forEach((other) => {
					if (other !== item && other.open) other.open = false;
				});
			});
		});
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
