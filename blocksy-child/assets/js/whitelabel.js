/**
 * White-Label Retainer — page-whitelabel-retainer.php
 * Sticky-CTA, native FAQ, lokaler Live-Prüfstand, Anker im Kopf und das
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
	var cta    = document.getElementById('aufgabe');

	if (sticky && hero) {
		var updateSticky = function () {
			var heroBottom = hero.getBoundingClientRect().bottom;
			var rect = cta ? cta.getBoundingClientRect() : null;
			var formVisible = rect && rect.top < window.innerHeight && rect.bottom > 0;
			var shouldShow = window.innerWidth < 768 && heroBottom < 0 && !formVisible;
			sticky.classList.toggle('is-visible', shouldShow);
			sticky.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
			sticky.querySelector('a').tabIndex = shouldShow ? 0 : -1;
			document.body.classList.toggle('has-sticky-wl-cta', shouldShow);
		};
		var stickyPending = false;
		var scheduleSticky = function () {
			if (stickyPending) return; stickyPending = true;
			requestAnimationFrame(function () { stickyPending = false; updateSticky(); });
		};
		window.addEventListener('scroll', scheduleSticky, { passive: true });
		window.addEventListener('resize', scheduleSticky);
		if (window.ResizeObserver && cta) new ResizeObserver(scheduleSticky).observe(cta);
		updateSticky();
	}

	// strecke-js-privat:start — local measurement only; the form is outside.
	(function initLocalProtocol() {
		var root = document.querySelector('.wl-page');
		var hero = root && root.querySelector('[data-wl-pruefstand]');
		var panel = hero && hero.querySelector('[data-wl-protokoll]');
		if (!panel) return;
		var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
		var status = panel.querySelector('[data-wl-status]');
		var progress = panel.querySelector('[data-wl-fortschritt]');
		var rerun = panel.querySelector('[data-wl-nochmal]');
		var stamp = hero.querySelector('[data-wl-stempel]');
		var title = hero.querySelector('[data-wl-titel]');
		var lastWord = hero.querySelector('[data-wl-wort-abnahme]');
		var scan = hero.querySelector('[data-wl-scan]');
		var rows = Array.from(panel.querySelectorAll('[data-wl-pruefung]'));
		var marks = [], timers = [], running = false, complete = false, passed = 0;
		var started = 0, scanAnimation = null, lcp = null;
		var format = function (value, digits) { return value.toFixed(digits).replace('.', ','); };
		var seconds = function (value) { return format(value / 1000, 2) + ' s'; };
		var clock = function () { return performance.now(); };
		function schedule(fn, ms) { var id = window.setTimeout(fn, ms); timers.push(id); return id; }
		function wait(ms) { return reduced.matches ? Promise.resolve() : new Promise(function (resolve) { schedule(resolve, ms); }); }
		function announce() { status.textContent = passed + ' von ' + rows.length + ' bestanden · ' + format((clock() - started) / 1000, 1) + ' s'; }

		/* LCP entries are real browser measurements. If unavailable, name the
		   document timing explicitly; never substitute the duration of this run. */
		function loadMeasurement() {
			if (lcp !== null) return { ms: lcp, label: 'Ladezeit (LCP)', short: 'LCP' };
			var entry = performance.getEntriesByType('navigation')[0];
			return { ms: entry && (entry.domContentLoadedEventEnd || entry.loadEventEnd) || null, label: 'Ladezeit (Dokument)', short: 'Dokument geladen' };
		}
		function measureLoad() {
			var value = loadMeasurement();
			var label = panel.querySelector('[data-wl-lcp-label]');
			if (label) label.textContent = value.label;
			root.querySelectorAll('[data-wl-lcp-kopie]').forEach(function (node) { node.textContent = value.ms === null ? 'nicht prüfbar' : seconds(value.ms); });
			root.querySelectorAll('[data-wl-lcp-proof-label]').forEach(function (node) { node.textContent = value.short; });
			root.querySelectorAll('[data-wl-lcp-proof]').forEach(function (node) { node.hidden = false; });
			var explanation = panel.querySelector('[data-wl-pruefung="lcp"] .wl-pl-satz');
			if (explanation) explanation.textContent = (value.short === 'LCP' ? 'Bis das größte Element steht.' : 'Bis das Dokument geladen ist; dieser Browser meldet noch keinen LCP.') + ' Grenze: ' + seconds(2500) + '.';
			return { text: value.ms === null ? 'nicht prüfbar' : seconds(value.ms), ok: value.ms !== null && value.ms <= 2500 };
		}

		var checks = {
			ueberschriften: function () {
				var h1 = document.querySelectorAll('h1').length;
				var h2 = Array.from(document.querySelectorAll('h2'));
				var anchored = h2.filter(function (node) { return node.id && document.querySelectorAll('[id="' + CSS.escape(node.id) + '"]').length === 1; }).length;
				return { text: h1 + ' · ' + anchored + '/' + h2.length, ok: h1 === 1 && h2.length > 0 && anchored === h2.length };
			},
			bilder: function () {
				var images = Array.from(root.querySelectorAll('img'));
				var valid = images.filter(function (image) { return image.hasAttribute('alt') && Number(image.getAttribute('width')) > 0 && Number(image.getAttribute('height')) > 0; }).length;
				return { text: valid + '/' + images.length, ok: images.length > 0 && valid === images.length };
			},
			formular: function () {
				var fields = Array.from(root.querySelectorAll('form [required]'));
				var labelled = fields.filter(function (field) { return field.labels && Array.from(field.labels).some(function (label) { return label.textContent.trim(); }); }).length;
				return { text: labelled + '/' + fields.length, ok: fields.length > 0 && labelled === fields.length };
			},
			'neuer-tab': function () {
				var links = Array.from(document.querySelectorAll('a[target="_blank"]'));
				var announced = links.filter(function (link) {
					var name = (link.getAttribute('aria-label') || '') + ' ' + link.textContent;
					return /(?:neue[mnrs]?\s+Tab|neue[mnrs]?\s+Fenster)/i.test(name) && /(?:^|\s)noopener(?:\s|$)/.test(link.rel);
				}).length;
				return { text: announced + '/' + links.length, ok: announced === links.length };
			},
			lcp: measureLoad,
			cookies: function () {
				var count = document.cookie.split(';').filter(function (cookie) { return cookie.trim(); }).length;
				return { text: String(count), ok: count === 0 };
			},
			schema: function () {
				var types = 0, valid = true;
				document.querySelectorAll('script[type="application/ld+json"]').forEach(function (script) {
					try {
						var data = JSON.parse(script.textContent);
						var nodes = Array.isArray(data) ? data : (data && data['@graph'] || [data]);
						if (!Array.isArray(nodes) || !nodes.length) valid = false;
						nodes.forEach(function (node) { if (!node || typeof node !== 'object' || !node['@type']) valid = false; else types++; });
					} catch (error) { valid = false; }
				});
				return { text: types + ' Typen', ok: valid && types > 0 };
			}
		};
		function showResult(row, result) {
			var output = row.querySelector('.wl-pl-wert');
			output.textContent = result.text;
			output.dataset.ergebnis = result.ok ? 'ok' : 'befund';
		}
		function safeCheck(key) { try { return checks[key](); } catch (error) { return { text: 'nicht prüfbar', ok: false }; } }

		/* Canvas resolves computed CSS colours (including color-mix and alpha)
		   to sRGB; foreground/background are composited before WCAG luminance. */
		var context = document.createElement('canvas').getContext('2d', { willReadFrequently: true });
		function rgba(value) {
			if (!context) throw new Error('Colour measurement unavailable');
			context.clearRect(0, 0, 1, 1); context.fillStyle = value; context.fillRect(0, 0, 1, 1);
			var c = context.getImageData(0, 0, 1, 1).data;
			return [c[0] / 255, c[1] / 255, c[2] / 255, c[3] / 255];
		}
		function composite(front, back) { var a = front[3] + back[3] * (1 - front[3]); return front.slice(0, 3).map(function (v, i) { return a ? (v * front[3] + back[i] * back[3] * (1 - front[3])) / a : 0; }).concat(a); }
		function luminance(c) { return c.slice(0, 3).map(function (v) { return v <= .04045 ? v / 12.92 : Math.pow((v + .055) / 1.055, 2.4); }).reduce(function (sum, v, i) { return sum + v * [.2126, .7152, .0722][i]; }, 0); }
		function contrast(element) {
			var layers = [], node = element;
			while (node) { layers.unshift(rgba(getComputedStyle(node).backgroundColor)); node = node.parentElement; }
			var background = layers.reduce(function (back, front) { return composite(front, back); }, [1, 1, 1, 1]);
			var foreground = composite(rgba(getComputedStyle(element).color), background);
			var a = luminance(foreground), b = luminance(background);
			return (Math.max(a, b) + .05) / (Math.min(a, b) + .05);
		}
		function measurement(element) {
			var style = getComputedStyle(element), size = parseFloat(style.fontSize);
			switch (element.dataset.wlMess) {
				case 'h1': return ['Überschrift', Math.round(size) + ' px'];
				case 'satz': return ['Fließtext', Math.round(size) + ' px · Zeile ' + format(parseFloat(style.lineHeight) / size, 2)];
				case 'cta': return ['Kontrast ' + format(contrast(element), 1) + ' : 1', 'Fläche ' + Math.round(element.getBoundingClientRect().height) + ' px hoch'];
				case 'bild': return ['Bild ' + element.getAttribute('width') + ' × ' + element.getAttribute('height'), element.hasAttribute('alt') ? (element.alt ? 'Alt: ' + element.alt : 'Alt leer · dekorativ') : 'Alt fehlt'];
			}
		}
		function bounds(element) {
			if (element.dataset.wlMess === 'h1') {
				var words = Array.from(element.querySelectorAll('.wl-wort')).map(function (word) { return word.getBoundingClientRect(); });
				var lines = Array.from(element.querySelectorAll('.wl-zeile')).map(function (line) { return line.getBoundingClientRect(); });
				return { left: Math.min.apply(null, words.map(function (r) { return r.left; })), right: Math.max.apply(null, words.map(function (r) { return r.right; })), top: lines[0].top, bottom: lines[lines.length - 1].bottom };
			}
			return element.getBoundingClientRect();
		}
		function buildMarks() {
			marks.forEach(function (mark) { mark.label.remove(); mark.frame.remove(); }); marks = [];
			if (window.innerWidth < 768) return;
			var h = hero.getBoundingClientRect(), content = hero.querySelector('[data-wl-messfeld]').getBoundingClientRect();
			var point = hero.querySelector('.st-rail__punkt').getBoundingClientRect();
			var left = point.right - h.left + 16, width = content.left - h.left - left - 16;
			hero.querySelectorAll('[data-wl-mess]').forEach(function (element) {
				var result;
				try { result = measurement(element); } catch (error) { result = ['Messung', 'nicht prüfbar']; }
				var rect = bounds(element), label = document.createElement('div'), frame = document.createElement('div');
				label.className = 'wl-messmarke'; label.setAttribute('aria-hidden', 'true');
				var name = document.createElement('b'), value = document.createElement('span'); name.textContent = result[0]; value.textContent = result[1]; label.append(name, value);
				Object.assign(label.style, { left: left + 'px', top: (rect.top - h.top) + 'px', width: Math.max(0, width) + 'px' });
				frame.className = 'wl-messrahmen'; frame.setAttribute('aria-hidden', 'true');
				Object.assign(frame.style, { left: (rect.left - h.left - 8) + 'px', top: (rect.top - h.top - 8) + 'px', width: (rect.right - rect.left + 16) + 'px', height: (rect.bottom - rect.top + 16) + 'px' });
				hero.append(label, frame); marks.push({ label: label, frame: frame, y: rect.top - h.top });
			});
		}
		function placeStamp() {
			if (!stamp || !lastWord) return;
			var t = title.getBoundingClientRect(), w = lastWord.getBoundingClientRect();
			// Use layout dimensions, unaffected by the rotated stamp's animation.
			var sw = stamp.offsetWidth, sh = stamp.offsetHeight;
			var angle = 7 * Math.PI / 180;
			var rotatedHeight = sw * Math.sin(angle) + sh * Math.cos(angle);
			stamp.style.setProperty('--wl-stempel-scale', Math.min(1, w.height * .9 / rotatedHeight));
			stamp.style.left = Math.max(0, Math.min(w.right - t.left - sw * .45, t.width - sw - 8)) + 'px';
			stamp.style.top = (w.top - t.top + (w.height - sh) / 2) + 'px';
		}
		function stampResult() {
			var all = passed === rows.length;
			if (stamp) {
				stamp.querySelector('[data-wl-stempel-wert]').textContent = passed + '/' + rows.length;
				var date = new Date();
				stamp.querySelector('[data-wl-stempel-datum]').textContent = date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' · ' + date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
				placeStamp(); stamp.classList.toggle('is-finding', !all); stamp.classList.add('is-set');
			}
			hero.classList.toggle('is-approved', all);
		}
		async function run() {
			if (running) return; running = true; complete = false; passed = 0; started = clock();
			timers.forEach(window.clearTimeout); timers = []; if (scanAnimation) scanAnimation.cancel();
			hero.classList.remove('is-approved', 'is-stamped'); stamp.classList.remove('is-set', 'is-finding');
			panel.classList.remove('is-complete'); rerun.disabled = true;
			// Keep the control's footprint through reruns to avoid a layout shift.
			rows.forEach(function (row) { var output = row.querySelector('.wl-pl-wert'); output.textContent = '–'; delete output.dataset.ergebnis; });
			progress.style.setProperty('--wl-progress', 0); announce();
			hero.classList.add('is-started'); buildMarks();
			var end = Math.max(title.getBoundingClientRect().bottom - hero.getBoundingClientRect().top, ...marks.map(function (mark) { return mark.y + 60; }));
			if (scan && scan.animate && !reduced.matches) scanAnimation = scan.animate([{ transform: 'translateY(0)', opacity: 0 }, { opacity: 1, offset: .08 }, { opacity: 1, offset: .9 }, { transform: 'translateY(' + end + 'px)', opacity: 0 }], { duration: 1300, delay: 320, easing: 'linear', fill: 'both' });
			marks.forEach(function (mark) {
				var reveal = function () { mark.label.classList.add('is-visible'); mark.frame.classList.add('is-visible'); if (reduced.matches) mark.frame.classList.add('is-dimmed'); else schedule(function () { mark.frame.classList.add('is-dimmed'); }, 400); };
				if (reduced.matches) reveal(); else schedule(reveal, 320 + 1300 * Math.min(1, mark.y / Math.max(1, end)));
			});
			await wait(1035);
			for (var i = 0; i < rows.length; i++) {
				await wait(150); var result = safeCheck(rows[i].dataset.wlPruefung); showResult(rows[i], result); if (result.ok) passed++;
				progress.style.setProperty('--wl-progress', (i + 1) / rows.length); announce();
			}
			panel.classList.add('is-complete'); stampResult(); if (!reduced.matches) hero.classList.add('is-stamped');
			rerun.hidden = false; rerun.disabled = false; complete = true; running = false;
		}
		try {
			if (window.PerformanceObserver && PerformanceObserver.supportedEntryTypes.includes('largest-contentful-paint')) {
				new PerformanceObserver(function (list) {
					var entries = list.getEntries(), latest = entries[entries.length - 1];
					if (!latest) return; lcp = latest.renderTime || latest.startTime;
					if (complete) {
						var row = panel.querySelector('[data-wl-pruefung="lcp"]'); showResult(row, measureLoad());
						passed = rows.filter(function (r) { return r.querySelector('[data-ergebnis="ok"]'); }).length;
						announce(); stampResult();
					}
				}).observe({ type: 'largest-contentful-paint', buffered: true });
			}
		} catch (error) { /* Document timing remains the explicit fallback. */ }
		rerun.addEventListener('click', function () {
			if (running) return;
			panel.classList.remove('is-complete'); rerun.disabled = true;
			hero.classList.remove('is-started');
			window.requestAnimationFrame(function () { window.requestAnimationFrame(run); });
		});
		var reflowTimer;
		function reflow() {
			window.clearTimeout(reflowTimer);
			reflowTimer = window.setTimeout(function () {
				if (running) { reflow(); return; }
				buildMarks(); marks.forEach(function (mark) { mark.label.classList.add('is-visible'); mark.frame.classList.add('is-visible', 'is-dimmed'); }); placeStamp();
			}, 120);
		}
		window.addEventListener('resize', reflow, { passive: true });
		if (window.ResizeObserver) new ResizeObserver(reflow).observe(hero.querySelector('.wl-hero__links'));
		if (reduced.addEventListener) reduced.addEventListener('change', function () { if (reduced.matches && scanAnimation) scanAnimation.cancel(); reflow(); });
		function start() {
			var fonts = document.fonts ? document.fonts.ready : Promise.resolve();
			fonts.then(function () {
				// Enhance only after dependencies are ready; failure leaves words readable.
				hero.setAttribute('data-wl-enhanced', '');
				schedule(run, reduced.matches ? 200 : 120);
			});
		}
		if (document.readyState === 'complete') start(); else window.addEventListener('load', start, { once: true });
	})();
	// strecke-js-privat:end

	// ─── Margen-Tafel: once on first view, same transform as homepage ───
	document.querySelectorAll('[data-wl-marge]').forEach(function (table) {
		var reveal = function () { table.setAttribute('data-st-gesehen', ''); };
		if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { reveal(); return; }
		var observer = new IntersectionObserver(function (entries) {
			if (entries.some(function (entry) { return entry.isIntersecting; })) { reveal(); observer.disconnect(); }
		}, { threshold: .2 });
		observer.observe(table);
	});

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

		// Kampagnenparameter der URL in die versteckten Felder, wie auf /kontakt/:
		// utm_source landet im Feld ads_source. Ein Browser-Speicher entsteht nicht.
		var urlParams = new URLSearchParams(window.location.search);
		var paramMap = { utm_source: 'ads_source', ads_source: 'ads_source', utm_campaign: 'utm_campaign' };

		Object.keys(paramMap).forEach(function (urlKey) {
			var value = urlParams.get(urlKey);
			var field = value ? form.querySelector('input[name="' + paramMap[urlKey] + '"]') : null;

			if (field) {
				field.value = value;
			}
		});

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
				ads_source: (form.querySelector('#wl-ads-source') || { value: '' }).value,
				utm_campaign: (form.querySelector('#wl-utm-campaign') || { value: '' }).value,
				'case': caseField ? caseField.value : 'aufgabe'
			};

			// Herkunft ueber NexusCore: ohne Cookies, ohne Einwilligung auch ohne Browser-Speicher.
			var core = window.NexusCore;
			var attribution = core && typeof core.getLeadAttributionPayload === 'function' ? core.getLeadAttributionPayload() : {};
			var campaign = core && typeof core.getCampaignContext === 'function' ? core.getCampaignContext() : {};

			['landing_page_url', 'entry_page_url', 'previous_internal_url', 'referrer_url', 'ads_keyword'].forEach(function (key) {
				if (attribution[key]) {
					payload[key] = attribution[key];
				}
			});

			// Parameter dieser Seite stehen schon im Payload und haben Vorrang.
			if (!payload.ads_source && attribution.ads_source) {
				payload.ads_source = attribution.ads_source;
			}

			if (campaign.entry_referrer_url) {
				payload.referrer_url = campaign.entry_referrer_url;
			}

			['utm_medium', 'utm_campaign'].forEach(function (key) {
				if (campaign[key] && !payload[key]) {
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
