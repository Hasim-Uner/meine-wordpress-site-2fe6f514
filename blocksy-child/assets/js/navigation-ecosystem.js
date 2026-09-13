/* Navigation Ecosystem — progressive wayfinding enhancement. */
(function () {
	'use strict';

	if (typeof document === 'undefined') return;

	var SELECTORS = [
		'.hu-page-toc',
		'.about-index',
		'.wl-site-header__nav',
		'.hu-sst__toc-band',
		'#nav'
	];

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

	function initScrollSpy(nav) {
		var links = samePageLinks(nav);
		if (!links.length) return;
		markScrollTargets(links);

		var targets = links.map(function (link) {
			return document.getElementById(link.getAttribute('href').slice(1));
		}).filter(Boolean);

		if (!targets.length) return;

		if (!('IntersectionObserver' in window)) {
			setCurrent(links, targets[0].id);
			return;
		}

		var visible = new Map();
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) visible.set(entry.target.id, entry.boundingClientRect.top);
				else visible.delete(entry.target.id);
			});

			var active = null;
			var closest = Infinity;
			visible.forEach(function (top, id) {
				var distance = Math.abs(top);
				if (distance < closest) {
					closest = distance;
					active = id;
				}
			});

			if (active) setCurrent(links, active);
		}, {
			rootMargin: '-18% 0px -62% 0px',
			threshold: [0, 0.01, 0.5]
		});

		targets.forEach(function (target) { observer.observe(target); });
	}

	function normalizeEnergyNav() {
		if (!document.body.classList.contains('hu-wayfinding-energy')) return;
		var nav = document.getElementById('nav');
		if (!nav) return;

		var stages = [
			['strecke', 'System', 'toc_energy_system'],
			['rechnung', 'Wirtschaftlichkeit', 'toc_energy_economics'],
			['fall', 'Case', 'toc_energy_case'],
			['leiter', 'Einstieg', 'toc_energy_entry'],
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

	function normalizeGeneratedToc(nav) {
		var details = nav.querySelector('details');
		if (!details) return;
		var desktop = window.matchMedia('(min-width: 1380px) and (min-height: 720px)');

		function sync() {
			if (desktop.matches) details.setAttribute('open', '');
			else details.removeAttribute('open');
		}
		sync();
		if (typeof desktop.addEventListener === 'function') desktop.addEventListener('change', sync);

		nav.addEventListener('click', function (event) {
			if (!desktop.matches && event.target.closest('a[href^="#"]')) {
				details.removeAttribute('open');
			}
		});
	}

	function focusTargetFromKeyboard(event) {
		if (event.detail !== 0) return;
		var link = event.target.closest('a[href^="#"]');
		if (!link) return;
		var target = document.getElementById(link.getAttribute('href').slice(1));
		if (!target) return;
		window.setTimeout(function () {
			var heading = target.matches('h1,h2,h3,h4,h5,h6') ? target : target.querySelector('h1,h2,h3,h4,h5,h6');
			if (!heading) return;
			if (!heading.hasAttribute('tabindex')) heading.setAttribute('tabindex', '-1');
			heading.focus({ preventScroll: true });
		}, 0);
	}

	function init() {
		document.body.classList.add('hu-wayfinding-active');
		normalizeEnergyNav();
		updateStickyOffset();
		window.addEventListener('resize', updateStickyOffset, { passive: true });

		SELECTORS.forEach(function (selector) {
			document.querySelectorAll(selector).forEach(function (nav) {
				if (nav.classList.contains('hu-page-toc')) normalizeGeneratedToc(nav);
				initScrollSpy(nav);
			});
		});

		document.addEventListener('click', focusTargetFromKeyboard);
	}

	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
	else init();
})();
