/**
 * Design essay V3
 *
 * Motion is used as hierarchy: initial hero choreography, small pointer depth
 * on fine pointers, and viewport reveals for diagrams/major chapter markers.
 * No storage, analytics, network requests or layout-dependent timers.
 *
 * @package Blocksy_Child
 */
(function () {
	'use strict';

	var hero = document.querySelector('.hu-design-hero');
	var essay = document.querySelector('.hu-design-essay');

	if (!hero || !essay) {
		return;
	}

	var reduceMotion = window.matchMedia &&
		window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (!reduceMotion) {
		hero.classList.add('has-design-motion');
		essay.classList.add('has-design-motion');
	}

	window.requestAnimationFrame(function () {
		hero.classList.add('is-ready');
	});

	if (!reduceMotion && window.matchMedia &&
		window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
		var visual = hero.querySelector('[data-design-hero-visual]');

		if (visual) {
			var raf = 0;

			visual.addEventListener('pointermove', function (event) {
				var rect = visual.getBoundingClientRect();
				var x = ((event.clientX - rect.left) / rect.width - 0.5) * 12;
				var y = ((event.clientY - rect.top) / rect.height - 0.5) * 12;

				if (raf) {
					window.cancelAnimationFrame(raf);
				}

				raf = window.requestAnimationFrame(function () {
					visual.style.setProperty('--hero-x', x.toFixed(2) + 'px');
					visual.style.setProperty('--hero-y', y.toFixed(2) + 'px');
				});
			}, { passive: true });

			visual.addEventListener('pointerleave', function () {
				visual.style.setProperty('--hero-x', '0px');
				visual.style.setProperty('--hero-y', '0px');
			}, { passive: true });
		}
	}

	var revealTargets = Array.prototype.slice.call(
		essay.querySelectorAll(
			'.hu-dv, :scope > h2, :scope > .hu-design-essay__questions, :scope > .hu-design-essay__ethics, :scope > .hu-design-essay__future'
		)
	);

	if (reduceMotion || !('IntersectionObserver' in window)) {
		revealTargets.forEach(function (target) {
			target.classList.add('is-visible');
		});
		return;
	}

	var observer = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (!entry.isIntersecting) {
				return;
			}

			entry.target.classList.add('is-visible');
			observer.unobserve(entry.target);
		});
	}, {
		root: null,
		rootMargin: '0px 0px -12% 0px',
		threshold: 0.14
	});

	revealTargets.forEach(function (target) {
		observer.observe(target);
	});
})();
