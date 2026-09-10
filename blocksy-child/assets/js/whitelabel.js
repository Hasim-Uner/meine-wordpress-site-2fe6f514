/**
 * White-Label Retainer — page-whitelabel-retainer.php
 * Sticky-CTA, Arbeitsmodus-Toggle und das vierfeldrige Agentur-Formular.
 * Wird nur auf Whitelabel-Routen geladen (inc/enqueue.php, Block P2).
 *
 * Das vorgeschaltete Quiz (3 Klickfragen → Termin) ist ersatzlos entfallen:
 * es qualifizierte den Anbieter, nicht den Käufer, und stand zwischen dem
 * Interessenten und dem, was der eigentlich wollte. Sein Ersatz ist ein
 * Formular mit vier Feldern und einem eigenen Erfolgs-Event.
 */
(function () {
	'use strict';

	// ─── Seitenlokales Tracking: ein dataLayer-Push, sonst nichts ───
	var track = function (action, extra) {
		if (!action) {
			return;
		}

		try {
			if (!window.dataLayer || typeof window.dataLayer.push !== 'function') {
				return;
			}

			var payload = Object.assign({ event: action }, extra || {});
			window.dataLayer.push(payload);
		} catch (error) {
			// Tracking darf Navigation und Formular nie blockieren.
		}
	};

	var handleTrackedClick = function (event) {
		var target = event.target;

		if (!target || typeof target.closest !== 'function') {
			return;
		}

		var node = target.closest('[data-track-action]');

		if (!node || !node.closest('.wl-page, .wl-site-header, .wl-page-footer')) {
			return;
		}

		var action = node.getAttribute('data-track-action');

		if (!action) {
			return;
		}

		// Capture läuft vor dem nativen <details>-Toggle. Ein bereits offenes
		// Element wird mit diesem Klick geschlossen und ist kein Open-Event.
		if (action === 'faq_whitelabel_open') {
			var details = node.closest('.wl-faq__item');

			if (details && details.open) {
				return;
			}
		}

		var sectionOwner = node.closest('[data-track-section]');
		var eventData = {
			event_category: node.getAttribute('data-track-category') || 'engagement',
			event_section: sectionOwner ? sectionOwner.getAttribute('data-track-section') : 'whitelabel'
		};
		var label = node.getAttribute('data-track-label');

		if (label) {
			eventData.event_label = label;
		}

		track(action, eventData);
	};

	if (document.querySelector('.wl-page')) {
		document.addEventListener('click', handleTrackedClick, true);
	}

	// ─── Sticky Mobile CTA visibility ───
	var sticky = document.getElementById('wl-sticky-cta');
	var hero   = document.getElementById('hero');
	var cta    = document.getElementById('naechster-schritt');

	if (sticky && hero) {
		var updateSticky = function () {
			var heroBottom = hero.getBoundingClientRect().bottom;
			var ctaTop     = cta ? cta.getBoundingClientRect().top : Infinity;
			var shouldShow = heroBottom < 0 && ctaTop > window.innerHeight - 80;
			sticky.classList.toggle('is-visible', shouldShow);
			sticky.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
			document.body.classList.toggle('has-sticky-wl-cta', shouldShow);
		};
		window.addEventListener('scroll', updateSticky, { passive: true });
		window.addEventListener('resize', updateSticky);
		updateSticky();
	}

	// ─── Arbeitsmodus-Toggle ───
	// JS setzt data-wl-mode als primären CSS-Schlüssel; die :has()-Regeln in
	// whitelabel.css decken nur den No-JS-Fall ab (Radio-Zustand direkt).
	var mode = document.getElementById('wl-mode');
	if (mode) {
		mode.addEventListener('change', function (e) {
			var radio = e.target;
			if (!radio || radio.name !== 'wl-mode' || !radio.checked) {
				return;
			}
			mode.setAttribute('data-wl-mode', radio.value);
		});
	}

	// ─── FAQ: nativen <details>-Zustand mit explizitem ARIA spiegeln ───
	document.querySelectorAll('.wl-faq__item').forEach(function (item) {
		var summary = item.querySelector('.wl-faq__summary');
		if (!summary) {
			return;
		}
		var syncExpanded = function () {
			summary.setAttribute('aria-expanded', item.open ? 'true' : 'false');
		};
		item.addEventListener('toggle', syncExpanded);
		syncExpanded();
	});

	// ─── Agentur-Formular ───
	// Vier Felder, ein eigener REST-Endpunkt, ein eigenes Erfolgs-Event.
	// `whitelabel_request_submit` feuert kein anderes Formular der Website —
	// erst dadurch wird die Conversion dieser Route isoliert messbar.
	var form = document.querySelector('[data-wl-request-form]');

	if (form) {
		var feedback     = form.querySelector('[data-wl-feedback]');
		var submitButton = form.querySelector('[data-wl-submit]');
		var caseField    = form.querySelector('[data-wl-case]');
		var errorSummary = document.querySelector('[data-wl-error-summary]');
		var errorList    = document.querySelector('[data-wl-error-list]');
		var submitLabel  = submitButton ? submitButton.textContent : 'Aufgabe senden';
		var validCases   = ['aufgabe', 'angebotsphase'];

		var setCase = function (value) {
			if (!caseField || validCases.indexOf(value) === -1) {
				return;
			}
			caseField.value = value;
		};

		// Serverseitig steht immer "aufgabe" im Feld, weil die Route gecacht
		// wird. Den tatsächlichen Weg setzt der Client aus ?case= nach.
		var readCaseFromUrl = function (search) {
			try {
				return new URLSearchParams(search || window.location.search).get('case') || '';
			} catch (error) {
				return '';
			}
		};

		setCase(readCaseFromUrl());

		var showErrors = function (messages) {
			if (!errorSummary || !errorList) {
				return;
			}

			errorList.innerHTML = '';

			if (!messages.length) {
				errorSummary.classList.add('is-hidden');
				return;
			}

			messages.forEach(function (message) {
				var item = document.createElement('li');
				item.textContent = message;
				errorList.appendChild(item);
			});

			errorSummary.classList.remove('is-hidden');
		};

		var setFeedback = function (message, state) {
			if (!feedback) {
				return;
			}
			feedback.textContent = message;
			feedback.classList.toggle('is-error', state === 'error');
			feedback.classList.toggle('is-success', state === 'success');
		};

		// Die drei Wege und die CTAs zeigen auf dieselbe Seite. Ohne diesen
		// Handler lädt der Klick die Route neu, nur um zwei Felder tiefer zu
		// landen. Ohne JS bleibt genau dieser Reload der funktionierende Pfad.
		var samePage = function (link) {
			return link.pathname === window.location.pathname
				&& link.host === window.location.host;
		};

		// Der Sprung zum Formular folgt derselben Reduced-Motion-Regel wie das
		// übrige Seiten-Motion: wer Bewegung abbestellt hat, springt hart.
		var getScrollBehavior = function () {
			try {
				return window.matchMedia('(prefers-reduced-motion: reduce)').matches
					? 'auto'
					: 'smooth';
			} catch (error) {
				return 'auto';
			}
		};

		document.querySelectorAll('[data-wl-form-link]').forEach(function (link) {
			link.addEventListener('click', function (event) {
				if (event.metaKey || event.ctrlKey || event.shiftKey || event.button !== 0) {
					return;
				}

				if (!samePage(link)) {
					return;
				}

				event.preventDefault();
				setCase(readCaseFromUrl(link.search));

				var target = document.getElementById('aufgabe');
				if (target) {
					target.scrollIntoView({ behavior: getScrollBehavior(), block: 'start' });
				}

				var firstField = form.querySelector('#wl-task');
				if (firstField) {
					try { firstField.focus({ preventScroll: true }); } catch (error) { /* Fokus optional */ }
				}
			});
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var task  = form.querySelector('#wl-task');
			var email = form.querySelector('#wl-email');
			var errors = [];

			if (!task || task.value.trim().length < 12) {
				errors.push('Bitte die Aufgabe kurz beschreiben — vier Zeilen genügen.');
			}

			if (!email || !email.value.trim() || email.validity.typeMismatch) {
				errors.push('Bitte eine gültige E-Mail-Adresse angeben.');
			}

			showErrors(errors);

			if (errors.length) {
				setFeedback('', null);
				var firstInvalid = errors.length && task && task.value.trim().length < 12 ? task : email;
				if (firstInvalid) {
					try { firstInvalid.focus(); } catch (error) { /* Fokus optional */ }
				}
				return;
			}

			var payload = {
				task: task.value.trim(),
				email: email.value.trim(),
				timeframe: (form.querySelector('#wl-timeframe') || { value: '' }).value.trim(),
				access: (form.querySelector('input[name="access"]:checked') || { value: '' }).value,
				company_website: (form.querySelector('#wl-company-website') || { value: '' }).value,
				'case': caseField ? caseField.value : 'aufgabe'
			};

			if (submitButton) {
				submitButton.disabled = true;
				submitButton.textContent = 'Wird gesendet …';
			}
			setFeedback('', null);

			fetch(form.getAttribute('action'), {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload)
			})
				.then(function (response) {
					return response.json().then(function (data) {
						return { ok: response.ok, data: data };
					});
				})
				.then(function (result) {
					if (!result.ok || !result.data || !result.data.ok) {
						var message = result.data && result.data.error
							? result.data.error
							: 'Das hat gerade nicht geklappt. Bitte noch einmal versuchen.';
						showErrors([message]);
						setFeedback(message, 'error');
						return;
					}

					showErrors([]);
					setFeedback(result.data.message || 'Danke. Die Aufgabe ist da.', 'success');
					form.reset();
					setCase(payload['case']);

					track('whitelabel_request_submit', {
						event_category: 'lead_gen',
						event_section: 'naechster_schritt',
						event_label: payload['case']
					});
				})
				.catch(function () {
					var message = 'Verbindung fehlgeschlagen. Bitte noch einmal versuchen.';
					showErrors([message]);
					setFeedback(message, 'error');
				})
				.finally(function () {
					if (submitButton) {
						submitButton.disabled = false;
						submitButton.textContent = submitLabel;
					}
				});
		});
	}
})();
