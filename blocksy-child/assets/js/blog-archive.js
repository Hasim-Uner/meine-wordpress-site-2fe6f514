(function () {
	'use strict';

	var BODY_OPEN_CLASS = 'blog-bell-modal-open';
	var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

	function initRevealObserver() {
		var nodes = document.querySelectorAll('.blog-bell [data-reveal]');
		if (!nodes.length) {
			return;
		}

		if (!('IntersectionObserver' in window)) {
			nodes.forEach(function (node) { node.classList.add('is-visible'); });
			return;
		}

		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry, index) {
				if (!entry.isIntersecting) { return; }
				var delay = Math.min(index * 60, 240);
				window.setTimeout(function () {
					entry.target.classList.add('is-visible');
				}, delay);
				observer.unobserve(entry.target);
			});
		}, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

		nodes.forEach(function (node) { observer.observe(node); });
	}

	function initBellModal() {
		var trigger = document.getElementById('blog-bell-trigger');
		var modal = document.getElementById('blog-bell-modal');
		if (!trigger || !modal) { return; }

		var panel = modal.querySelector('.blog-bell__modal-panel');
		var dismissers = modal.querySelectorAll('[data-blog-bell-dismiss]');
		var lastFocus = null;

		function open() {
			lastFocus = document.activeElement;
			modal.hidden = false;
			// next frame so the transition kicks in
			window.requestAnimationFrame(function () {
				modal.classList.add('is-open');
			});
			document.body.classList.add(BODY_OPEN_CLASS);
			trigger.setAttribute('aria-expanded', 'true');

			var firstField = modal.querySelector('input[type="email"]');
			if (firstField) {
				window.setTimeout(function () { firstField.focus(); }, 120);
			} else if (panel) {
				panel.focus();
			}
		}

		function close() {
			modal.classList.remove('is-open');
			document.body.classList.remove(BODY_OPEN_CLASS);
			trigger.setAttribute('aria-expanded', 'false');

			// hide after the transition has finished
			window.setTimeout(function () {
				if (!modal.classList.contains('is-open')) {
					modal.hidden = true;
				}
			}, 360);

			if (lastFocus && typeof lastFocus.focus === 'function') {
				lastFocus.focus();
			}
		}

		trigger.addEventListener('click', function (event) {
			event.preventDefault();
			open();
		});

		dismissers.forEach(function (node) {
			node.addEventListener('click', function (event) {
				event.preventDefault();
				close();
			});
		});

		document.addEventListener('keydown', function (event) {
			if (!modal.classList.contains('is-open')) { return; }

			if (event.key === 'Escape') {
				event.preventDefault();
				close();
				return;
			}

			if (event.key !== 'Tab') { return; }

			var focusables = Array.prototype.slice.call(modal.querySelectorAll(FOCUSABLE))
				.filter(function (node) { return node.offsetParent !== null; });
			if (!focusables.length) { return; }

			var first = focusables[0];
			var last = focusables[focusables.length - 1];

			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		});

		// Auto-close after a successful subscription (the shared blog-notify.js
		// writes the message into [data-blog-notify-feedback].is-success).
		var feedback = modal.querySelector('[data-blog-notify-feedback]');
		if (feedback && 'MutationObserver' in window) {
			var fbObserver = new MutationObserver(function () {
				if (feedback.classList.contains('is-success')) {
					window.setTimeout(close, 2600);
				}
			});
			fbObserver.observe(feedback, { attributes: true, attributeFilter: ['class'] });
		}
	}

	function init() {
		initRevealObserver();
		initBellModal();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
