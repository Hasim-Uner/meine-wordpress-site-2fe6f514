/**
 * Legal page modal. Keep legal links usable without JavaScript, then enhance
 * ordinary clicks with one dialog and navigation inside its scroll panel.
 *
 * @package Blocksy_Child
 */
(function () {
	'use strict';

	var overlay;
	var panel;
	var closeBtn;
	var returnFocus;
	var previousOverflow;
	var background = [];
	var cache = {};
	var isOpen = false;
	var requestId = 0;
	var stylesReady;
	var scriptReady;

	function assetUrl(file) {
		var script = document.querySelector('script[src*="/assets/js/legal-modal.js"]');
		return script ? script.src.replace('/assets/js/legal-modal.js', '/assets/' + file) : '';
	}

	function ensureLegalStyles() {
		if (stylesReady) return stylesReady;
		stylesReady = new Promise(function (resolve) {
			if (document.querySelector('link[href*="/assets/css/legal-pages.css"]')) {
				resolve();
				return;
			}
			var href = assetUrl('css/legal-pages.css');
			if (!href) { resolve(); return; }
			var link = document.createElement('link');
			link.id = 'nexus-legal-pages-css-lazy';
			link.rel = 'stylesheet';
			link.href = href;
			link.onload = link.onerror = resolve;
			document.head.appendChild(link);
		});
		return stylesReady;
	}

	function ensureLegalScript() {
		if (window.NexusLegalPageInit) return Promise.resolve();
		if (scriptReady) return scriptReady;
		scriptReady = new Promise(function (resolve) {
			var src = assetUrl('js/legal-pages.js');
			if (!src) { resolve(); return; }
			var script = document.createElement('script');
			script.src = src;
			script.onload = resolve;
			script.onerror = function () { scriptReady = null; script.remove(); resolve(); };
			document.head.appendChild(script);
		});
		return scriptReady;
	}

	function getLegalUrl(href) {
		try {
			var url = new URL(href, location.href);
			if (url.origin === location.origin && /^\/(impressum|datenschutz)\/?$/.test(url.pathname)) return url;
		} catch (error) { /* Leave invalid links to the browser. */ }
		return null;
	}

	function ensureModal() {
		if (overlay) return;
		overlay = document.createElement('div');
		overlay.className = 'legal-modal';
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-modal', 'true');
		overlay.setAttribute('aria-hidden', 'true');
		overlay.setAttribute('aria-label', 'Rechtliche Informationen');

		var backdrop = document.createElement('div');
		backdrop.className = 'legal-modal__backdrop';
		backdrop.addEventListener('click', close);
		panel = document.createElement('div');
		panel.className = 'legal-modal__panel';

		closeBtn = document.createElement('button');
		closeBtn.className = 'legal-modal__close';
		closeBtn.type = 'button';
		closeBtn.setAttribute('aria-label', 'Schließen');
		closeBtn.innerHTML = '<svg aria-hidden="true" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
		closeBtn.addEventListener('click', close);
		panel.appendChild(closeBtn);
		overlay.appendChild(backdrop);
		overlay.appendChild(panel);
		document.body.appendChild(overlay);
	}

	function fetchContent(url) {
		var key = url.pathname + url.search;
		if (cache[key]) return cache[key];
		cache[key] = fetch(key, { credentials: 'same-origin' })
			.then(function (res) {
				if (!res.ok) throw new Error(res.status);
				return res.text();
			})
			.then(function (html) {
				var doc = new DOMParser().parseFromString(html, 'text/html');
				var shell = doc.querySelector('.legal-page, .privacy-shell, .imprint-shell') || doc.querySelector('main');
				if (!shell) throw new Error('Content not found');
				return shell.outerHTML;
			})
			.catch(function (error) { delete cache[key]; throw error; });
		return cache[key];
	}

	function clearContent() {
		if (window.NexusLegalPageDestroy) window.NexusLegalPageDestroy(panel);
		Array.prototype.slice.call(panel.children).forEach(function (node) {
			if (node !== closeBtn) node.remove();
		});
	}

	function scrollToContent(body, url, fromModal) {
		var target;
		if (url.hash) {
			try {
				var id = decodeURIComponent(url.hash.slice(1));
				var elements = body.querySelectorAll('[id]');
				for (var i = 0; i < elements.length; i++) {
					if (elements[i].id === id) { target = elements[i]; break; }
				}
			} catch (error) { /* Malformed fragments fall back to the document. */ }
		}
		if (!target && fromModal) target = body.querySelector('.privacy-section, .imprint-section');
		if (!target) return;
		if (window.NexusLegalPageScroll) {
			window.NexusLegalPageScroll(target);
		} else {
			// Even a failed enhancement script must leave document switching usable.
			panel.scrollTop = panel.scrollTop + target.getBoundingClientRect().top - panel.getBoundingClientRect().top - 64;
		}
	}

	function focusWhenVisible(currentRequest) {
		requestAnimationFrame(function () {
			if (!isOpen || currentRequest !== requestId || panel.contains(document.activeElement)) return;
			if (window.getComputedStyle(closeBtn).visibility === 'visible') closeBtn.focus({ preventScroll: true });
			else focusWhenVisible(currentRequest);
		});
	}

	function open(url, trigger) {
		ensureModal();
		var fromModal = isOpen && overlay.contains(trigger);
		var currentRequest = ++requestId;
		if (!isOpen) {
			returnFocus = trigger;
			previousOverflow = document.documentElement.style.overflow;
			background = Array.prototype.map.call(document.body.children, function (node) {
				var state = { node: node, inert: node.inert };
				if (node !== overlay) node.inert = true;
				return state;
			});
		}
		isOpen = true;
		clearContent();
		panel.scrollTop = 0;
		panel.setAttribute('aria-busy', 'true');
		var loader = document.createElement('div');
		loader.className = 'legal-modal__loader';
		loader.setAttribute('role', 'status');
		loader.textContent = 'Wird geladen\u2026';
		panel.appendChild(loader);
		overlay.setAttribute('aria-hidden', 'false');
		overlay.classList.add('legal-modal--open');
		document.documentElement.style.overflow = 'hidden';
		closeBtn.focus({ preventScroll: true });
		focusWhenVisible(currentRequest);
		document.addEventListener('keydown', onKeyDown);
		document.addEventListener('focusin', containFocus);

		Promise.all([fetchContent(url), ensureLegalStyles(), ensureLegalScript()])
			.then(function (results) {
				if (!isOpen || currentRequest !== requestId) return;
				clearContent();
				var body = document.createElement('div');
				body.className = 'legal-modal__body';
				body.innerHTML = results[0];
				panel.appendChild(body);
				var heading = body.querySelector('h1');
				overlay.setAttribute('aria-label', heading ? heading.textContent : 'Rechtliche Informationen');
				panel.removeAttribute('aria-busy');
				if (window.NexusLegalPageInit) window.NexusLegalPageInit(body);
				var fontsReady = document.fonts ? document.fonts.ready : Promise.resolve();
				fontsReady.then(function () {
					requestAnimationFrame(function () {
						if (isOpen && currentRequest === requestId) scrollToContent(body, url, fromModal);
					});
				});
			})
			.catch(function () {
				if (!isOpen || currentRequest !== requestId) return;
				close();
				location.href = url.href;
			});
	}

	function close() {
		if (!isOpen) return;
		isOpen = false;
		requestId++;
		overlay.classList.remove('legal-modal--open');
		document.documentElement.style.overflow = previousOverflow;
		document.removeEventListener('keydown', onKeyDown);
		document.removeEventListener('focusin', containFocus);
		background.forEach(function (state) { state.node.inert = state.inert; });
		background = [];
		clearContent();
		if (returnFocus && returnFocus.isConnected) returnFocus.focus({ preventScroll: true });
		overlay.setAttribute('aria-hidden', 'true');
	}

	function containFocus(event) {
		if (isOpen && !overlay.contains(event.target)) closeBtn.focus({ preventScroll: true });
	}

	function onKeyDown(event) {
		if (event.key === 'Escape') { event.preventDefault(); close(); return; }
		if (event.key !== 'Tab') return;
		var focusable = Array.prototype.filter.call(
			panel.querySelectorAll('a[href], button, input, select, textarea, [tabindex]'),
			function (node) { return !node.disabled && node.tabIndex >= 0 && node.getClientRects().length; }
		);
		var index = focusable.indexOf(document.activeElement);
		if (event.shiftKey && index <= 0) {
			event.preventDefault();
			focusable[focusable.length - 1].focus({ preventScroll: true });
		} else if (!event.shiftKey && (index === -1 || index === focusable.length - 1)) {
			event.preventDefault();
			focusable[0].focus({ preventScroll: true });
		}
	}

	// One delegated handler covers existing and dynamically inserted links.
	document.addEventListener('click', function (event) {
		if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
		var link = event.target.closest('a[href]');
		if (!link || link.hasAttribute('download') || (link.target && link.target !== '_self')) return;
		var url = getLegalUrl(link.href);
		if (!url) return;
		event.preventDefault();
		open(url, link);
	});
})();
