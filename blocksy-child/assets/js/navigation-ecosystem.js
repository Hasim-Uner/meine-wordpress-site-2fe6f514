/* Navigation Ecosystem — progressive wayfinding enhancement. */
(function () {
	'use strict';

	if (typeof document === 'undefined') return;

	var SCROLLSPY_SELECTORS = [
		'.hu-page-toc',
		'.about-index',
		'.wl-site-header__nav',
		'.hu-sst__toc-band',
		'#nav'
	];
	var DISCLOSURE_SELECTORS = [
		'.about-index',
		'.hu-sst__toc-band',
		'#nav'
	];
	var MOBILE_TOC_QUERY = '(max-width: 900px), (max-height: 680px)';
	var DESKTOP_RAIL_QUERY = '(min-width: 1280px) and (min-height: 620px) and (hover: hover) and (pointer: fine)';
	var tocPanelCount = 0;

	function samePageLinks(root) {
		return Array.prototype.slice.call(root.querySelectorAll('a[href^="#"]')).filter(function (link) {
			var hash = link.getAttribute('href');
			return hash && hash.length > 1 && document.getElementById(hash.slice(1));
		});
	}

	function updateStickyOffset() {
		var candidates = [
			document.querySelector('.leiste'),
			document.querySelector('.wl-site-header'),
			document.querySelector('.nx-site-header'),
			document.querySelector('[data-site-header]'),
			document.getElementById('nav')
		].filter(Boolean);
		var offset = 0;

		candidates.forEach(function (node) {
			var style = window.getComputedStyle(node);
			if (style.position === 'sticky' || style.position === 'fixed') {
				offset += node.getBoundingClientRect().height;
			}
		});

		document.documentElement.style.setProperty('--hu-wayfinding-offset', Math.max(56, Math.round(offset)) + 'px');
	}

	function markScrollTargets(links) {
		links.forEach(function (link) {
			var target = document.getElementById(link.getAttribute('href').slice(1));
			if (target) target.setAttribute('data-hu-scroll-target', 'true');
		});
	}

	function setCurrent(links, activeId) {
		links.forEach(function (link) {
			var isCurrent = link.getAttribute('href') === '#' + activeId;
			if (isCurrent) {
				link.setAttribute('aria-current', 'location');
			} else {
				link.removeAttribute('aria-current');
			}
		});
	}

	function getWayfindingOffset() {
		var raw = window.getComputedStyle(document.documentElement).getPropertyValue('--hu-wayfinding-offset');
		var parsed = parseFloat(raw);
		return Number.isFinite(parsed) ? parsed : 76;
	}

	function getScrollSpyThreshold(nav) {
		var threshold = getWayfindingOffset() + 24;
		if (nav && nav.classList.contains('home-toc')) {
			threshold = Math.max(threshold, window.innerHeight * 0.34);
		}
		return threshold;
	}

	function initScrollSpy(nav) {
		var links = samePageLinks(nav);
		if (!links.length) return;
		markScrollTargets(links);

		var targets = links.map(function (link) {
			return document.getElementById(link.getAttribute('href').slice(1));
		}).filter(Boolean);
		var queued = false;
		var activeId = '';

		if (!targets.length) return;

		function evaluate() {
			queued = false;
			var threshold = getScrollSpyThreshold(nav);
			var nextId = targets[0].id;
			var atDocumentEnd = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4;

			if (atDocumentEnd) {
				nextId = targets[targets.length - 1].id;
			} else {
				targets.forEach(function (target) {
					if (target.getBoundingClientRect().top <= threshold) {
						nextId = target.id;
					}
				});
			}

			if (nextId !== activeId) {
				activeId = nextId;
				setCurrent(links, activeId);
			}
		}

		function queueEvaluate() {
			if (queued) return;
			queued = true;
			window.requestAnimationFrame(evaluate);
		}

		evaluate();
		window.addEventListener('scroll', queueEvaluate, { passive: true });
		window.addEventListener('resize', queueEvaluate, { passive: true });
		window.addEventListener('load', queueEvaluate, { once: true });
	}

	function normalizeEnergyNav() {
		if (!document.body.classList.contains('hu-wayfinding-energy')) return;
		var nav = document.getElementById('nav');
		if (!nav) return;

		var stages = [
			['strecke', 'System', 'toc_energy_system'],
			['rechnung', 'Wirtschaftlichkeit', 'toc_energy_economics'],
			['ergebnisse', 'Case', 'toc_energy_case'],
			['einstieg', 'Einstieg', 'toc_energy_entry'],
			['fragen', 'FAQ', 'toc_energy_faq']
		].filter(function (stage) {
			return !!document.getElementById(stage[0]);
		});

		if (stages.length < 3) return;
		nav.textContent = '';
		if (!nav.getAttribute('aria-label')) nav.setAttribute('aria-label', 'Auf dieser Seite');

		stages.forEach(function (stage) {
			var link = document.createElement('a');
			link.href = '#' + stage[0];
			link.textContent = stage[1];
			link.setAttribute('data-track-action', stage[2]);
			link.setAttribute('data-track-category', 'navigation');
			link.setAttribute('data-track-section', 'energy_toc');
			nav.appendChild(link);
		});
	}

	function normalizeResultsRegister(nav) {
		if (!document.body.classList.contains('hu-wayfinding-results')) return false;
		if (nav.hasAttribute('data-hu-results-register-ready')) return true;

		var links = samePageLinks(nav);
		if (!links.length) return false;

		var marker = document.createElement('span');
		marker.className = 'hu-results-register__marke';
		marker.setAttribute('aria-hidden', 'true');
		marker.innerHTML = '<span class="hu-results-register__marke-short">Reg.</span><span class="hu-results-register__marke-full">Register</span>';

		var entries = document.createElement('div');
		entries.className = 'hu-results-register__entries';

		links.forEach(function (link, index) {
			var label = link.textContent.trim();
			var number = index + 1 < 10 ? '0' + (index + 1) : String(index + 1);
			var numberNode = document.createElement('span');
			var textNode = document.createElement('span');

			numberNode.className = 'hu-results-register__nr';
			numberNode.textContent = number;
			textNode.className = 'hu-results-register__txt';
			textNode.textContent = label;

			link.textContent = '';
			link.appendChild(numberNode);
			link.appendChild(textNode);
			entries.appendChild(link);
		});

		nav.textContent = '';
		nav.classList.add('hu-results-register');
		nav.setAttribute('aria-label', 'Abschnitte dieses Dokuments');
		nav.setAttribute('data-hu-results-register-ready', 'true');
		nav.appendChild(marker);
		nav.appendChild(entries);
		return true;
	}

	function normalizeGeneratedToc(nav) {
		if (normalizeResultsRegister(nav)) {
			updateStickyOffset();
			return;
		}

		var details = nav.querySelector('details');
		var summary = details ? details.querySelector('summary') : null;
		if (!details || !summary) return;

		var rail = window.matchMedia('(min-width: 1380px) and (min-height: 720px)');

		function sync() {
			if (rail.matches) {
				details.setAttribute('open', '');
				summary.setAttribute('tabindex', '-1');
				summary.setAttribute('aria-hidden', 'true');
				nav.setAttribute('data-hu-toc-static', 'true');
			} else {
				details.removeAttribute('open');
				summary.removeAttribute('tabindex');
				summary.removeAttribute('aria-hidden');
				nav.removeAttribute('data-hu-toc-static');
			}
			updateStickyOffset();
		}

		sync();
		if (typeof rail.addEventListener === 'function') rail.addEventListener('change', sync);

		details.addEventListener('toggle', updateStickyOffset);
		nav.addEventListener('click', function (event) {
			if (!rail.matches && event.target.closest('a[href^="#"]')) {
				details.removeAttribute('open');
			}
		});
	}

	function initResultsTocRail(nav) {
		if (!document.body.classList.contains('hu-wayfinding-results')) return;
		if (!nav.classList.contains('hu-results-register')) return;

		var trigger = document.getElementById('technik');
		if (!trigger) return;

		var rail = window.matchMedia(DESKTOP_RAIL_QUERY);
		var queued = false;
		nav.setAttribute('data-hu-results-toc-ready', 'true');

		function evaluate() {
			queued = false;

			if (!rail.matches) {
				nav.removeAttribute('data-hu-results-toc-visible');
				return;
			}

			var threshold = getWayfindingOffset() + 24;
			var visible = trigger.getBoundingClientRect().top <= threshold;
			nav.setAttribute('data-hu-results-toc-visible', visible ? 'true' : 'false');
		}

		function queueEvaluate() {
			if (queued) return;
			queued = true;
			window.requestAnimationFrame(evaluate);
		}

		evaluate();
		window.addEventListener('scroll', queueEvaluate, { passive: true });
		window.addEventListener('resize', queueEvaluate, { passive: true });
		window.addEventListener('load', queueEvaluate, { once: true });
		if (typeof rail.addEventListener === 'function') rail.addEventListener('change', evaluate);
	}

	function initHomeTocRail(nav) {
		if (!nav || !nav.classList.contains('home-toc')) return;
		var trigger = document.getElementById('nachweis');
		if (!trigger) return;

		var queued = false;
		nav.setAttribute('data-hu-home-toc-ready', 'true');

		function setVisible(visible) {
			nav.setAttribute('data-hu-home-toc-visible', visible ? 'true' : 'false');
			nav.setAttribute('aria-hidden', visible ? 'false' : 'true');
			if (visible) {
				nav.hidden = false;
				nav.style.removeProperty('display');
			} else {
				nav.hidden = true;
				nav.style.setProperty('display', 'none', 'important');
			}
		}

		function evaluate() {
			queued = false;
			var threshold = getScrollSpyThreshold(nav);
			setVisible(trigger.getBoundingClientRect().top <= threshold);
		}

		function queueEvaluate() {
			if (queued) return;
			queued = true;
			window.requestAnimationFrame(evaluate);
		}

		evaluate();
		window.addEventListener('scroll', queueEvaluate, { passive: true });
		window.addEventListener('resize', queueEvaluate, { passive: true });
		window.addEventListener('load', queueEvaluate, { once: true });
	}

	function createPanelForDirectLinks(nav) {
		var panel = document.createElement('div');
		panel.className = 'hu-toc-panel hu-toc-panel--contents';

		while (nav.firstChild) {
			panel.appendChild(nav.firstChild);
		}
		nav.appendChild(panel);
		return panel;
	}

	function enhanceResponsiveToc(nav) {
		if (!nav || nav.classList.contains('hu-page-toc') || nav.classList.contains('wl-site-header__nav')) return;
		if (nav.hasAttribute('data-hu-toc-disclosure-ready')) return;

		var panel = nav.classList.contains('hu-sst__toc-band')
			? nav.querySelector('.hu-sst__toc')
			: createPanelForDirectLinks(nav);
		if (!panel) return;

		nav.setAttribute('data-hu-toc-disclosure-ready', 'true');
		panel.classList.add('hu-toc-panel');
		if (!panel.id) {
			tocPanelCount += 1;
			panel.id = 'hu-toc-panel-' + tocPanelCount;
		}

		var button = document.createElement('button');
		button.type = 'button';
		button.className = 'hu-toc-toggle';
		button.hidden = true;
		button.setAttribute('aria-controls', panel.id);
		button.setAttribute('aria-expanded', 'true');
		button.innerHTML = '<span>Auf dieser Seite</span><span class="hu-toc-toggle__icon" aria-hidden="true">+</span>';
		nav.insertBefore(button, panel);

		var mobile = window.matchMedia(MOBILE_TOC_QUERY);

		function setExpanded(expanded) {
			button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
			button.setAttribute('aria-label', expanded ? 'Inhaltsnavigation schließen' : 'Inhaltsnavigation öffnen');
			panel.hidden = !expanded;
			var icon = button.querySelector('.hu-toc-toggle__icon');
			if (icon) icon.textContent = expanded ? '−' : '+';
			updateStickyOffset();
		}

		function syncMode() {
			if (mobile.matches) {
				nav.classList.add('hu-toc-collapsible');
				button.hidden = false;
				setExpanded(false);
				return;
			}

			nav.classList.remove('hu-toc-collapsible');
			button.hidden = true;
			setExpanded(true);
		}

		button.addEventListener('click', function () {
			setExpanded(button.getAttribute('aria-expanded') !== 'true');
		});

		nav.addEventListener('click', function (event) {
			if (mobile.matches && event.target.closest('a[href^="#"]')) {
				setExpanded(false);
			}
		});

		syncMode();
		if (typeof mobile.addEventListener === 'function') mobile.addEventListener('change', syncMode);
	}

	function focusTargetFromKeyboard(event) {
		if (event.detail !== 0) return;
		var link = event.target.closest('a[href^="#"]');
		if (!link || !link.closest(SCROLLSPY_SELECTORS.join(','))) return;

		var target = document.getElementById(link.getAttribute('href').slice(1));
		if (!target) return;

		window.setTimeout(function () {
			var heading = target.matches('h1,h2,h3,h4,h5,h6') ? target : target.querySelector('h1,h2,h3,h4,h5,h6');
			if (!heading) return;

			var addedTabindex = !heading.hasAttribute('tabindex');
			if (addedTabindex) heading.setAttribute('tabindex', '-1');
			heading.focus({ preventScroll: true });

			if (addedTabindex) {
				heading.addEventListener('blur', function () {
					heading.removeAttribute('tabindex');
				}, { once: true });
			}
		}, 0);
	}

	function init() {
		document.body.classList.add('hu-wayfinding-active');
		normalizeEnergyNav();
		updateStickyOffset();
		window.addEventListener('resize', updateStickyOffset, { passive: true });

		SCROLLSPY_SELECTORS.forEach(function (selector) {
			document.querySelectorAll(selector).forEach(function (nav) {
				if (nav.classList.contains('hu-page-toc')) {
					normalizeGeneratedToc(nav);
					initResultsTocRail(nav);
					initHomeTocRail(nav);
				}
				initScrollSpy(nav);
			});
		});

		DISCLOSURE_SELECTORS.forEach(function (selector) {
			document.querySelectorAll(selector).forEach(enhanceResponsiveToc);
		});

		document.addEventListener('click', focusTargetFromKeyboard);
	}

	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
	else init();
})();