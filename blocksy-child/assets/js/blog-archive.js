(function () {
	'use strict';

	var BODY_OPEN_CLASS = 'blog-bell-modal-open';
	var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

	function initBellModal() {
		var triggers = document.querySelectorAll('#blog-bell-trigger, [data-blog-bell-open]');
		var modal = document.getElementById('blog-bell-modal');
		if (!triggers.length || !modal) { return; }

		var panel = modal.querySelector('.blog-bell__modal-panel');
		var dismissers = modal.querySelectorAll('[data-blog-bell-dismiss]');
		var lastFocus = null;

		function setExpanded(value) {
			triggers.forEach(function (trigger) {
				trigger.setAttribute('aria-expanded', value);
			});
		}

		function open() {
			lastFocus = document.activeElement;
			modal.hidden = false;
			modal.classList.add('is-open');
			document.body.classList.add(BODY_OPEN_CLASS);
			setExpanded('true');

			var firstField = modal.querySelector('input[type="email"]');
			if (firstField) {
				firstField.focus();
			} else if (panel) {
				panel.focus();
			}
		}

		function close() {
			modal.classList.remove('is-open');
			document.body.classList.remove(BODY_OPEN_CLASS);
			setExpanded('false');

			modal.hidden = true;

			if (lastFocus && typeof lastFocus.focus === 'function') {
				lastFocus.focus();
			}
		}

		triggers.forEach(function (trigger) {
			trigger.addEventListener('click', function (event) {
				event.preventDefault();
				open();
			});
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

		var feedback = modal.querySelector('[data-blog-notify-feedback]');
		if (feedback && 'MutationObserver' in window) {
			var feedbackObserver = new MutationObserver(function () {
				if (feedback.classList.contains('is-success')) {
					window.setTimeout(close, 2600);
				}
			});
			feedbackObserver.observe(feedback, { attributes: true, attributeFilter: ['class'] });
		}
	}

	function init() {
		initBellModal();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
