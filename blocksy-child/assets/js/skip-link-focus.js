/* Global skip-link focus enhancement. */
(function () {
	'use strict';

	function getTarget(link) {
		var href = link.getAttribute('href') || '';
		if (href.charAt(0) !== '#' || href.length < 2) return null;

		try {
			return document.getElementById(decodeURIComponent(href.slice(1)));
		} catch (error) {
			return null;
		}
	}

	function getStickyOffset() {
		var offset = 16;
		var selectors = ['#wpadminbar', '.leiste', '.wl-site-header', '[data-site-header]', '.ct-header'];
		var seen = [];

		selectors.forEach(function (selector) {
			var node = document.querySelector(selector);
			if (!node || seen.indexOf(node) !== -1) return;
			seen.push(node);

			var style = window.getComputedStyle(node);
			if (style.position !== 'sticky' && style.position !== 'fixed') return;
			if (node.getBoundingClientRect().bottom <= 0) return;

			offset += node.getBoundingClientRect().height;
		});

		return offset;
	}

	function focusMainTarget(target) {
		var addedTabindex = !target.hasAttribute('tabindex');
		if (addedTabindex) target.setAttribute('tabindex', '-1');

		try {
			target.focus({ preventScroll: true });
		} catch (error) {
			target.focus();
		}

		target.scrollIntoView({ block: 'start' });
		window.scrollBy(0, -getStickyOffset());

		if (addedTabindex) {
			target.addEventListener('blur', function cleanupTemporaryTabindex() {
				target.removeAttribute('tabindex');
			}, { once: true });
		}
	}

	document.addEventListener('click', function (event) {
		var link = event.target.closest('a[data-skip-link], a.skip-to-content, a.wl-skip-link');
		if (!link) return;

		var target = getTarget(link);
		if (!target) return;

		// Capture-phase interception prevents nexus-core's generic smooth-scroll
		// handler from consuming the skip link without moving keyboard focus.
		event.preventDefault();
		event.stopPropagation();
		focusMainTarget(target);
	}, true);
})();
