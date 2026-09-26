/**
 * White-Label Retainer — page-whitelabel-retainer.php
 * Sticky-CTA, native FAQ, Abnahmeprotokoll, Anker im Kopf und das
 * Agentur-Formular. Wird nur auf Whitelabel-Routen geladen (inc/enqueue.php,
 * Block P2). Linie und Stationspunkte fuellt startseite-strecke.js.
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

	// ─── Abnahmeprotokoll: Punkte haken sich an ihrer Station ab ───
	// Ohne dieses Skript stehen alle Punkte abgehakt (Muster). Mit ihm
	// starten sie offen; ein Punkt hakt sich ab, sobald die Leselinie bei
	// 60 % der Fensterhoehe seine Station in Abschnitt 05 erreicht, und bleibt
	// abgehakt. Dieselbe Leselinie wie die Linie in startseite-strecke.js.
	// Kein Netzwerk, kein Speicher.
	var pageRoot  = document.querySelector('.wl-page[data-st]');
	var protokoll = document.querySelector('[data-wl-protokoll]');
	var stationen = document.querySelectorAll('[data-wl-haken]');

	if (pageRoot && protokoll && stationen.length && 'IntersectionObserver' in window) {
		var punkte = {};
		protokoll.querySelectorAll('[data-wl-punkt]').forEach(function (punkt) {
			punkte[punkt.getAttribute('data-wl-punkt')] = punkt;
		});

		var abhaken = function (station) {
			(station.getAttribute('data-wl-haken') || '').split(' ').forEach(function (key) {
				if (punkte[key]) {
					punkte[key].classList.add('wl-erreicht');
				}
			});
		};

		var leselinie = new IntersectionObserver(function (eintraege) {
			eintraege.forEach(function (eintrag) {
				// Auch Stationen oberhalb des Fensters zaehlen: wer ueber
				// #aufgabe einsteigt, hat den Ablauf schon hinter sich.
				if (eintrag.isIntersecting || eintrag.boundingClientRect.bottom < 0) {
					abhaken(eintrag.target);
					leselinie.unobserve(eintrag.target);
				}
			});
		}, { rootMargin: '0px 0px -40% 0px' });

		pageRoot.setAttribute('data-wl-bereit', '');
		stationen.forEach(function (station) { leselinie.observe(station); });
	}

	// ─── Anker im Kopf: aktiv, solange ihr Abschnitt im Blick ist ───
	// Ohne JavaScript bleiben die Anker neutral.
	var anker = document.querySelectorAll('[data-wl-anker] a[href^="#"]');

	if (anker.length && 'IntersectionObserver' in window) {
		var ankerZiele = [];
		anker.forEach(function (link) {
			var ziel = document.getElementById(link.getAttribute('href').slice(1));
			if (ziel) {
				ankerZiele.push({ link: link, ziel: ziel });
			}
		});

		var markiere = function (aktiv) {
			ankerZiele.forEach(function (eintrag) {
				if (eintrag.ziel === aktiv) {
					eintrag.link.setAttribute('aria-current', 'location');
				} else {
					eintrag.link.removeAttribute('aria-current');
				}
			});
		};

		var imBlick = [];
		var band = new IntersectionObserver(function (eintraege) {
			eintraege.forEach(function (eintrag) {
				var i = imBlick.indexOf(eintrag.target);
				if (eintrag.isIntersecting && i === -1) imBlick.push(eintrag.target);
				if (!eintrag.isIntersecting && i !== -1) imBlick.splice(i, 1);
			});
			markiere(imBlick.length ? imBlick[imBlick.length - 1] : null);
		}, { rootMargin: '-45% 0px -45% 0px' });

		ankerZiele.forEach(function (eintrag) { band.observe(eintrag.ziel); });
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
		var caseLabel    = document.querySelector('[data-wl-case-label]');
		var taskLabel    = form.querySelector('[data-wl-task-label]');
		var taskField    = form.querySelector('#wl-task');
		var taskHint     = form.querySelector('#wl-task-hint');
		var isSubmitting = false;

		// Texte je Weg (aufgabe, angebotsphase, vormerken) kommen aus dem
		// Template, damit die Copy an einer Stelle steht.
		var caseTexts = {};
		try {
			caseTexts = JSON.parse(form.getAttribute('data-wl-case-texts') || '{}') || {};
		} catch (error) {
			caseTexts = {};
		}
		var taskError = (caseTexts.aufgabe && caseTexts.aufgabe.error) || 'Bitte die Aufgabe kurz beschreiben. Vier Zeilen genügen.';

		var setCase = function (value) {
			var texts = caseTexts[value];
			if (!caseField || !texts) {
				return;
			}
			caseField.value = value;
			if (caseLabel && texts.label) caseLabel.textContent = texts.label;
			if (taskLabel && texts.field) taskLabel.textContent = texts.field;
			if (taskField && texts.placeholder) taskField.setAttribute('placeholder', texts.placeholder);
			if (taskHint && texts.hint) taskHint.textContent = texts.hint;
			if (texts.error) taskError = texts.error;
			if (texts.submit) {
				submitLabel = texts.submit;
				if (submitButton && !isSubmitting) submitButton.textContent = texts.submit;
			}
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
				if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
					return;
				}

				if (!samePage(link)) {
					return;
				}

				event.preventDefault();
				if (isSubmitting) return;
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
			if (isSubmitting) {
				return;
			}

			var task  = form.querySelector('#wl-task');
			var email = form.querySelector('#wl-email');
			var errors = [];
			var invalidTask = !task || task.value.trim().length < 12;
			var invalidEmail = !email || !email.value.trim() || email.validity.typeMismatch;
			if (task) task.setAttribute('aria-invalid', invalidTask ? 'true' : 'false');
			if (email) email.setAttribute('aria-invalid', invalidEmail ? 'true' : 'false');

			if (invalidTask) {
				errors.push(taskError);
			}

			if (invalidEmail) {
				errors.push('Bitte eine gültige E-Mail-Adresse angeben.');
			}

			showErrors(errors);

			if (errors.length) {
				setFeedback('', null);
				if (errorSummary) {
					errorSummary.focus();
				}
				return;
			}

			var payload = {
				task: task.value.trim(),
				email: email.value.trim(),
				timeframe: (form.querySelector('#wl-timeframe') || { value: '' }).value.trim(),
				access: (form.querySelector('input[name="access"]:checked') || { value: '' }).value,
				referral_source: (form.querySelector('#wl-referral') || { value: '' }).value,
				company_website: (form.querySelector('#wl-company-website') || { value: '' }).value,
				'case': caseField ? caseField.value : 'aufgabe'
			};

			// Herkunft ueber NexusCore: ohne Cookies, ohne Einwilligung auch ohne Browser-Speicher.
			var core = window.NexusCore;
			var attribution = core && typeof core.getLeadAttributionPayload === 'function' ? core.getLeadAttributionPayload() : {};
			var campaign = core && typeof core.getCampaignContext === 'function' ? core.getCampaignContext() : {};

			['landing_page_url', 'entry_page_url', 'previous_internal_url', 'referrer_url', 'ads_source', 'ads_keyword'].forEach(function (key) {
				if (attribution[key]) {
					payload[key] = attribution[key];
				}
			});

			if (campaign.entry_referrer_url) {
				payload.referrer_url = campaign.entry_referrer_url;
			}

			['utm_medium', 'utm_campaign'].forEach(function (key) {
				if (campaign[key]) {
					payload[key] = campaign[key];
				}
			});

			isSubmitting = true;
			var unlockForm = window.NexusCore.lockForm(form);
			form.setAttribute('aria-busy', 'true');
			if (submitButton) {
				submitButton.disabled = true;
				submitButton.textContent = 'Wird gesendet …';
			}
			setFeedback('', null);

			window.NexusCore.submitJson(form.getAttribute('action'), {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload)
			})
				.then(function (result) {
					if (!result.ok || result.data.ok !== true) {
						var message = result.data && result.data.error
							? result.data.error
							: 'Das hat gerade nicht geklappt. Bitte noch einmal versuchen.';
						showErrors([message]);
						setFeedback(message, 'error');
						var field = { missing_task: task, invalid_email: email, invalid_access: form.querySelector('input[name="access"]') }[result.data.error_code];
						if (field) field.setAttribute('aria-invalid', 'true');
						if (errorSummary) errorSummary.focus();
						return;
					}

					showErrors([]);
					setFeedback(result.data.message || 'Danke. Die Aufgabe ist da.', 'success');
					form.reset();
					setCase(payload['case']);
					if (feedback) feedback.focus();

					track('whitelabel_request_submit', {
						event_category: 'lead_gen',
						event_section: 'naechster_schritt',
						event_label: payload['case']
					});
				})
				.catch(function (error) {
					var message = error.message;
					showErrors([message]);
					setFeedback(message, 'error');
					if (errorSummary) errorSummary.focus();
				})
				.finally(function () {
					unlockForm();
					isSubmitting = false;
					form.removeAttribute('aria-busy');
					if (submitButton) {
						submitButton.disabled = false;
						submitButton.textContent = submitLabel;
					}
				});
		});
		form.addEventListener('input', function (event) {
			if (event.target.hasAttribute('aria-invalid')) {
				event.target.removeAttribute('aria-invalid');
			}
		});
		// Erst nach Registrierung des Submit-Handlers aktivieren. Ohne JS bleibt
		// der sichtbare E-Mail-Kontakt verfügbar statt einer JSON-Ergebnisseite.
		if (submitButton) submitButton.disabled = false;
	}
})();
