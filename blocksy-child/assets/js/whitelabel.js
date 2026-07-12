/**
 * White-Label Retainer — page-whitelabel-retainer.php
 * Sticky-CTA, Arbeitsmodus-Toggle und Kennzahlen-Counter.
 * Wird nur auf Whitelabel-Routen geladen (inc/enqueue.php, Block P2).
 */
(function () {
	'use strict';

	// ─── Sticky Mobile CTA visibility ───
	var sticky = document.getElementById('wl-sticky-cta');
	var hero   = document.getElementById('hero');
	var cta    = document.getElementById('cta');

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

	// ─── Animated Counters ───
	// Eigene Implementierung statt NexusCore.initCounters: braucht deutsche
	// Tausenderpunkte (1.750) und die data-counter-*-API mit HTML-Suffixen.
	var counters = document.querySelectorAll('.wl-counter');
	if (!counters.length) {
		return;
	}
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function format(n, target) {
		var s = String(Math.round(n));
		if (target >= 1000) {
			s = s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
		}
		return s;
	}

	function run(el) {
		var target = parseInt(el.getAttribute('data-counter-target') || '0', 10);
		var suffix = el.getAttribute('data-counter-suffix') || '';
		var prefix = el.getAttribute('data-counter-prefix') || '';
		if (reduceMotion || !('requestAnimationFrame' in window)) {
			el.innerHTML = prefix + format(target, target) + suffix;
			return;
		}
		var duration = 1400;
		var start = null;
		function tick(t) {
			if (start === null) {
				start = t;
			}
			var p = Math.min(1, (t - start) / duration);
			var eased = 1 - Math.pow(1 - p, 3);
			el.innerHTML = prefix + format(target * eased, target) + suffix;
			if (p < 1) {
				requestAnimationFrame(tick);
			} else {
				el.innerHTML = prefix + format(target, target) + suffix;
			}
		}
		requestAnimationFrame(tick);
	}

	if (!('IntersectionObserver' in window)) {
		counters.forEach(run);
		return;
	}
	var io = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (entry.isIntersecting) {
				run(entry.target);
				io.unobserve(entry.target);
			}
		});
	}, { threshold: 0.2 });
	counters.forEach(function (el) { io.observe(el); });
})();
