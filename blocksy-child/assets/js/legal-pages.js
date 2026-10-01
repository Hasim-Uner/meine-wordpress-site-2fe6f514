/**
 * Legal documents: section navigation, focus, active section and reading
 * progress use the document's actual scroll container, including the modal.
 *
 * @package Blocksy_Child
 */
(function () {
	'use strict';

	var instances = new WeakMap();

	function cleanHeading(text) {
		return String(text || '').replace(/^\s*\d+[.)]?\s*/, '').trim();
	}

	function findTarget(page, hash) {
		try {
			var id = decodeURIComponent(hash.slice(1));
			var elements = page.querySelectorAll('[id]');
			for (var i = 0; i < elements.length; i++) {
				if (elements[i].id === id) return elements[i];
			}
		} catch (error) { /* Keep malformed fragment links native. */ }
		return null;
	}

	function scrollToTarget(target) {
		var panel = target.closest('.legal-modal__panel');
		var rect = target.getBoundingClientRect();
		var margin = parseFloat(window.getComputedStyle(target).scrollMarginTop) || 0;
		var top = panel
			? panel.scrollTop + rect.top - panel.getBoundingClientRect().top - panel.clientTop - margin
			: window.scrollY + rect.top - margin;
		var focusTarget = target.querySelector('h2') || target;
		if (!focusTarget.hasAttribute('tabindex')) focusTarget.setAttribute('tabindex', '-1');
		focusTarget.focus({ preventScroll: true });
		(panel || window).scrollTo({
			top: Math.max(0, top),
			behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'
		});
	}

	function initPage(page) {
		if (instances.has(page)) return;
		var sections = Array.prototype.slice.call(page.querySelectorAll('.privacy-section, .imprint-section'));
		var aside = page.querySelector('.privacy-card, .imprint-card');
		if (!sections.length || !aside) return;
		page.setAttribute('data-legal-initialized', '1');

		var nav = document.createElement('nav');
		nav.className = 'legal-index';
		nav.setAttribute('aria-label', 'Inhalt dieser Seite');
		sections.forEach(function (section, index) {
			var nr = String(index + 1).padStart(2, '0');
			var heading = section.querySelector('h2');
			if (!heading) return;
			if (!section.id) section.id = heading.id ? heading.id + '-section' : 'legal-section-' + nr;
			section.setAttribute('data-legal-nr', nr);
			var link = document.createElement('a');
			link.className = 'legal-index__link';
			link.href = '#' + section.id;
			link.innerHTML = '<span class="legal-index__nr" aria-hidden="true">' + nr + '</span><span class="legal-index__label"></span>';
			link.querySelector('.legal-index__label').textContent = cleanHeading(heading.textContent);
			nav.appendChild(link);
		});
		var existingMeta = aside.querySelector('.privacy-meta, .imprint-quickfacts');
		aside.insertBefore(nav, existingMeta);
		var links = Array.prototype.slice.call(nav.querySelectorAll('.legal-index__link'));
		var panel = page.closest('.legal-modal__panel');
		var scrollRoot = panel || window;
		var frame = 0;
		var active;

		function update() {
			frame = 0;
			var rootTop = panel ? panel.getBoundingClientRect().top + panel.clientTop : 0;
			var height = panel ? panel.clientHeight : window.innerHeight;
			var rect = page.getBoundingClientRect();
			var max = Math.max(1, page.offsetHeight - height);
			var progress = Math.min(1, Math.max(0, (rootTop - rect.top) / max));
			page.style.setProperty('--legal-progress', progress.toFixed(4));
			var current = sections[0];
			sections.forEach(function (section) {
				if (section.getBoundingClientRect().top <= rootTop + height * 0.28) current = section;
			});
			if (active === current) return;
			active = current;
			sections.forEach(function (section) { section.classList.toggle('is-active', section === current); });
			links.forEach(function (link) {
				if (link.getAttribute('href') === '#' + current.id) link.setAttribute('aria-current', 'location');
				else link.removeAttribute('aria-current');
			});
		}

		function requestUpdate() {
			if (!frame) frame = window.requestAnimationFrame(update);
		}

		function onClick(event) {
			if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
			var link = event.target.closest('a[href]');
			if (!link || link.hasAttribute('download') || (link.target && link.target !== '_self')) return;
			var href = link.getAttribute('href');
			var hash = href.charAt(0) === '#' ? href : '';
			if (!hash) {
				var url = new URL(link.href, location.href);
				if (url.origin === location.origin && url.pathname === location.pathname && url.search === location.search) hash = url.hash;
			}
			var target = hash ? findTarget(page, hash) : null;
			if (!target) return;
			event.preventDefault();
			// Modal fragments belong to the loaded document, never the background URL.
			if (!panel && window.history.replaceState) window.history.replaceState(null, '', hash);
			scrollToTarget(target);
		}

		page.addEventListener('click', onClick);
		scrollRoot.addEventListener('scroll', requestUpdate, { passive: true });
		window.addEventListener('resize', requestUpdate, { passive: true });
		page.setAttribute('data-legal-ready', '');
		update();
		instances.set(page, function () {
			page.removeEventListener('click', onClick);
			scrollRoot.removeEventListener('scroll', requestUpdate);
			window.removeEventListener('resize', requestUpdate);
			window.cancelAnimationFrame(frame);
			nav.remove();
			page.removeAttribute('data-legal-initialized');
			page.removeAttribute('data-legal-ready');
			instances.delete(page);
		});
	}

	function forPages(root, callback) {
		var context = root && root.querySelectorAll ? root : document;
		if (context.matches && context.matches('.legal-page')) callback(context);
		Array.prototype.forEach.call(context.querySelectorAll('.legal-page'), callback);
	}

	window.NexusLegalPageInit = function (root) { forPages(root, initPage); };
	window.NexusLegalPageScroll = scrollToTarget;
	window.NexusLegalPageDestroy = function (root) {
		forPages(root, function (page) { var cleanup = instances.get(page); if (cleanup) cleanup(); });
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { window.NexusLegalPageInit(document); });
	} else {
		window.NexusLegalPageInit(document);
	}
})();
